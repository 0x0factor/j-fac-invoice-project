<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;
use App\Models\Bill;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Mail;
use App\Models\CustomerCharge;
use App\Models\Serial;
use App\Models\Charge;
use App\Models\Customer;
use App\Models\History;
use App\Models\Company;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

use App\Main\AppController;

class QuoteController extends AppController
{
    protected $quote;
    protected $customer;

    public function __construct(Quote $quote, Customer $customer)
    {
        $this->quote = $quote;
        $this->customer = $customer;
    }

    public function index(Request $request)
    {
        if ($request->has('customer')) {
            $customer = $this->customer->where('CST_ID', $request->input('customer'))->first();
            session()->put('session_params', [
                'Quote' => [
                    'NAME' => $customer->NAME
                ]
            ]);
            $customer_id = $request->input('customer');
        } else {
            $customer_id = null;
        }
        $quotes = Quote::paginate(10); // Adjust the number of items per page as needed
        // Assuming $status is retrieved from some config or database
        $status = []; // Placeholder for status options

        return view('quote.index', [
            'main_title' => '見積書管理',
            'title_text' => '帳票管理',
            'title' => '抹茶請求書',
            'mailstatus' => config('constants.MailStatusCode'),
            'status' => config('constants.IssuedStatCode'),
            'customer_id' => $customer_id,
            'quotes' => $quotes

        ]);
    }

    public function add(Request $request)
    {
        $main_title = "見積書登録";
        $title_text = "帳票管理";

        if ($request->has('cancel_x')) {
            return redirect('/quotes');
        }

        // Discount settings
        if ($request->input('Quote.DISCOUNT_TYPE') == config('constants.DISCOUNT_TYPE_NONE')) {
            $request->merge(['Quote.DISCOUNT' => '']);
        }

        $company_ID = 1;
        $error = Config::get('ItemErrorCode');
        $count = 1;

        if ($request->isMethod('post')) {
            // Token check (Laravel handles CSRF automatically)

            // Validation
            $error = $this->itemValidation($request->input(), 'Quoteitem');

            // Discount validation
            $error['DISCOUNT'] = $this->validateDiscount($request);

            if ($request->input('Quote.DISCOUNT_TYPE') == config('constants.DISCOUNT_TYPE_PERCENT')) {
                $discount = strlen($request->input('Quote.DISCOUNT'));
                if ($discount > 2) {
                    $error['DISCOUNT'] = 1;
                }
                if ($request->input('Quote.DISCOUNT') == '100') {
                    $error['DISCOUNT'] = 0;
                }
                if (!is_numeric($request->input('Quote.DISCOUNT')) && $request->input('Quote.DISCOUNT') != null) {
                    $error['DISCOUNT'] = 2;
                }
            }

            if ($request->input('Quote.HONOR_CODE') != 2) {
                $request->merge(['Quote.HONOR_TITLE' => '']);
            }

            // Insert data
            if ($MQT_ID = $this->setData($request->input(), 'new', $error)) {
                // Action log
                $this->reportAction($request->input('Quote.USR_ID'), 2, $MQT_ID);

                // Success
                Session::flash('message', '見積書を保存しました');

                // Increment serial
                Serial::serialIncrement('Quote');

                return redirect("/quotes/check/" . $MQT_ID);
            } else {
                // Failure
                $count = max(count($request->input()) - 2, 1);

                // Collapse settings
                $collapse['other'] = empty($request->input('Quote.DEADLINE')) &&
                                     empty($request->input('Quote.DEAL')) &&
                                     empty($request->input('Quote.DELIVERY')) &&
                                     empty($request->input('Quote.DUE_DATE')) ? 1 : 0;

                $collapse['management'] = empty($request->input('Quote.MEMO')) ? 1 : 0;
            }
        } else {
            // Collapse settings
            $collapse['management'] = 1;
            $collapse['other'] = 1;

            // Serial settings

            $quote = new Quote();

            if ($quote->getSerial($company_ID) == 0) {
                $serial = new Serial();
                $request->merge(['Quote.NO' => $serial->get_number('Quote')]);
            }


            $request->merge([
                'Quote.CST_ID' => 'default',
                'Quote.item' => 'default',
                'Quote.DISCOUNT_TYPE' => config('constants.DISCOUNT_TYPE_NONE'),
                'Quote.DATE' => date("Y-m-d"),
                'Quote.CMP_SEAL_FLG' => (new Company())->getSealFlg(),
                'Quote.CHR_SEAL_FLG' => 0
            ]);

            // Company payment settings
            $quote = new Quote();
            $default_cmp = $quote->getCompanyPayment($company_ID);

            if ($default_cmp) {
                $request->merge([
                    'Quote.EXCISE' => $default_cmp['EXCISE'],
                    'Quote.FRACTION' => $default_cmp['FRACTION'],
                    'Quote.TAX_FRACTION' => $default_cmp['TAX_FRACTION'],
                    'Quote.TAX_FRACTION_TIMING' => $default_cmp['TAX_FRACTION_TIMING']
                ]);
            } else {
                $request->merge([
                    'Quote.EXCISE' => 1,
                    'Quote.FRACTION' => 1,
                    'Quote.TAX_FRACTION' => 1,
                    'Quote.TAX_FRACTION_TIMING' => 0
                ]);
            }

            // Company decimal settings
            $quote = new Quote();
            $default_dec = $quote->Get_Decimal($company_ID);
            if ($default_dec && isset($default_dec[0]['Company']['DECIMAL_QUANTITY']) && isset($default_dec[0]['Company']['DECIMAL_UNITPRICE'])) {
                $request->merge([
                    'Quote.DECIMAL_QUANTITY' => $default_dec[0]['Company']['DECIMAL_QUANTITY'],
                    'Quote.DECIMAL_UNITPRICE' => $default_dec[0]['Company']['DECIMAL_UNITPRICE']
                ]);
            } else {
                $request->merge([
                    'Quote.DECIMAL_QUANTITY' => 0,
                    'Quote.DECIMAL_UNITPRICE' => 0
                ]);
            }

            // Honor settings

            $quote = new Quote(); // Instantiate the Quote model

            $default_honor = $quote->getHonor($company_ID); // Call the method on the instance

            if ($default_honor && isset($default_honor[0]['Company']['HONOR_CODE'])) {
                $request->merge([
                    'Quote.HONOR_CODE' => $default_honor[0]['Company']['HONOR_CODE']
                ]);

                if ($default_honor[0]['Company']['HONOR_CODE'] == 2 && isset($default_honor[0]['Company']['HONOR_TITLE'])) {
                    $request->merge([
                        'Quote.HONOR_TITLE' => $default_honor[0]['Company']['HONOR_TITLE']
                    ]);
                }
            } else {
                // Handle the case where $default_honor is null or does not contain expected data
                // For example, set default values or handle the absence of data
                $request->merge([
                    'Quote.HONOR_CODE' => null,
                    'Quote.HONOR_TITLE' => null  // Or any default value you want to set
                ]);
            }
            // Tax settings by date
            $taxOperationDate = Config::get('TaxOperationDate');
            if ($taxOperationDate && is_array($taxOperationDate)) {
                foreach ($taxOperationDate as $key => $value) {
                    if ($request->input('Quote.DATE') >= $value['start']) {
                        if ($request->input('Quote.DATE') <= $value['end']) {
                            if ($key == 8) {
                                $tax_index = $key;
                                $default_cmp['EXCISE'] = $tax_index . $default_cmp['EXCISE'];
                            } elseif ($key == 5) {
                                // handle this case as needed
                            }
                        } elseif ($key >= 10) {
                            $tax_index = $key;
                            $default_cmp['EXCISE'] = $tax_index . $default_cmp['EXCISE'];
                            break;
                        }
                    }
                }
            $this->set('defaultExcise', $default_cmp['EXCISE']);
            }
        }

        // Company information
        $cst_condition = $this->Get_User_AUTHORITY() == 1 ?
            ['.CMP_ID' => $company_ID, 'Customer.USR_ID' => $this->getUserID()] :
            ['.CMP_ID' => $company_ID];

        $item = $this->Get_User_AUTHORITY() == 1 ?
            Item::where('USR_ID', $this->getUserID())->get() :
            Item::all();

        $quote = new Quote();

        // Call the instance method get_customer
        $company = $quote->get_customer($company_ID, $cst_condition);
        $quote = new Quote();
        $hidden = $quote->getPayment($company_ID);
        if ($default_cmp) {
            $hidden['default'] = [
                'EXCISE' => $default_cmp['EXCISE'],
                'FRACTION' => $default_cmp['FRACTION'],
                'TAX_FRACTION' => $default_cmp['TAX_FRACTION'],
                'TAX_FRACTION_TIMING' => $default_cmp['TAX_FRACTION_TIMING']
            ];
        } else {
            $hidden['default'] = [
                'EXCISE' => 1,
                'FRACTION' => 1,
                'TAX_FRACTION' => 1,
                'TAX_FRACTION_TIMING' => 0
            ];
        }

        $items['item'] = '＋アイテム追加＋';
        $items['default'] = '＋アイテム選択＋';
        $itemlist = [];

        if ($item) {
            foreach ($item as $value) {
                $items[$value->ITM_ID] = $value->ITEM;
                $itemlist[$value->ITM_ID] = [
                    'ITEM' => $value->ITEM,
                    'ITEM_CODE' => $value->ITEM_CODE,
                    'UNIT' => $value->UNIT,
                    'UNIT_PRICE' => $value->UNIT_PRICE
                ];
            }
        }

        $honor = is_null(Config::get('HonorCode')) ? [] : Config::get('HonorCode') ;
        $seal_flg = is_null(Config::get('SealFlg')) ? [] : Config::get('SealFlg') ;
        $taxClass = is_null(Config::get('TaxClass')) ? [] : Config::get('TaxClass') ;
        return view('quote.add', [
            'main_title' => $main_title,
            'title_text' => $title_text,
            'excises' => Config::get('ExciseCode'),
            'fractions' => Config::get('FractionCode'),
            "discount" => Config::get('DiscountCode'),
            "status" => Config::get('IssuedStatCode'),
            "decimal" => Config::get('DecimalCode'),
            "itemlist" => $itemlist ? json_encode($itemlist) : false,
            "error" => $error,
            "dataline" => $count,
            "item" => $items,
            "companys" => $company,
            "honor" => $honor,
            "hidden" => $hidden,
            'collapse_other' => $collapse['other'],
            'collapse_management' => $collapse['management'],
            'lineAttribute' => Config::get('LineAttribute'),
            'taxClass' => $taxClass,
            'taxRates' => Config::get('TaxRates'),
            'taxOperationDate' => Config::get('TaxOperationDate'),
            'seal_flg' => $seal_flg,
        ]);
    }

    public function check($id)
    {
        $quote = Quote::find($id);

        if (!$quote) {
            session()->flash('error', '指定の見積書が存在しません');
            return redirect('/quotes/index');
        }

        // Example of passing data to view
        return view('quotes.check', [
            'main_title' => '見積書確認',
            'title_text' => '帳票管理',
            'decimals' => config('constants.DecimalCode'),
            'excises' => config('constants.ExciseCode'),
            'fractions' => config('constants.FractionCode'),
            'tax_fraction_timing' => config('constants.TaxFractionTimingCode'),
            'status' => config('constants.IssuedStatCode'),
            'editauth' => $this->getEditAuthority($quote->USR_ID),
            'param' => $quote,
            'dataline' => $count, // Ensure $count is defined
            'honor' => config('constants.HonorCode'),
            'seal_flg' => config('constants.SealFlg')
        ]);
    }


    public function edit(Request $request, $quoteId = null)
    {
        $data = [];
        $data['main_title'] = "見積書編集";
        $data['title_text'] = "帳票管理";

        if ($request->has('cancel_x')) {
            return redirect('/quotes');
        }

        $companyId = 1;
        $error = config('app.ItemErrorCode'); // Assuming you have ItemErrorCode config
        $count = 1;

        if (!$request->isMethod('post')) {
            $data['collapse'] = ['management' => 1, 'other' => 1];

            if ($quoteId) {
                $quote = Quote::find($quoteId);
                if (!$quote) {
                    Session::flash('message', '指定の見積書が存在しません');
                    return redirect('/quotes/check');
                }

                $data['quote'] = $quote;
                $data['quote'] = $this->getCompatibleItems($quote); // Assuming a method getCompatibleItems

                $customerCharge = CustomerCharge::find($quote->CHRC_ID);
                if ($customerCharge) {
                    $data['quote']['CUSTOMER_CHARGE_NAME'] = $customerCharge->CHARGE_NAME;
                    $data['quote']['CUSTOMER_CHARGE_UNIT'] = $customerCharge->UNIT;
                }

                if (!$this->hasEditAuthority($quote->USR_ID)) {
                    Session::flash('message', '帳票を編集する権限がありません');
                    return redirect('/quotes/');
                }
            } else {
                Session::flash('message', '指定の見積書が存在しません');
                return redirect('/quotes/check');
            }
        } else {
            $this->validateToken($request->input('Security.token'));

            if ($request->has('del_x')) {
                Quote::destroy($request->input('Quote.MQT_ID'));
                Session::flash('message', '削除しました。');
                return redirect('/quotes/index');
            }

            $user = auth()->user();
            $error = $this->validateItem($request->all(), 'Quoteitem');
            $error['DISCOUNT'] = $this->validateDiscount($request->all());

            if ($request->input('Quote.DISCOUNT_TYPE') == DISCOUNT_TYPE_PERCENT) {
                $discount = strlen($request->input('Quote.DISCOUNT'));
                if ($discount > 2) {
                    $error['DISCOUNT'] = 1;
                }
                if ($request->input('Quote.DISCOUNT') == '100') {
                    $error['DISCOUNT'] = 0;
                }
                if (!ctype_digit($request->input('Quote.DISCOUNT')) && $request->input('Quote.DISCOUNT') != null) {
                    $error['DISCOUNT'] = 2;
                }
            }

            if ($request->input('Quote.HONOR_CODE') != 2) {
                $request->merge(['Quote.HONOR_TITLE' => ""]);
            }

            if ($MQT_ID = $this->updateQuote($request->all(), $error)) {
                History::logAction($user->id, 3, $request->input('Quote.MQT_ID'));
                Session::flash('message', '見積書を保存しました');
                return redirect("/quotes/check/$MQT_ID");
            } else {
                $count = max(count($request->all()) - 2, 1);
                $data['collapse']['other'] = empty($data['quote']['DEADLINE']) && empty($data['quote']['DEAL']) && empty($data['quote']['DELIVERY']) && empty($data['quote']['DUE_DATE']) ? 1 : 0;
                $data['collapse']['management'] = empty($data['quote']['MEMO']) ? 1 : 0;
            }
        }

        $items = $this->getItems($companyId);
        $data['hidden'] = $this->getPaymentInfo($companyId);

        $this->setViewData($data, $items, $error, $count);

        return view('quotes.edit', $data);
    }

    public function action(Request $request)
    {
        $customerId = $request->input('Customer.id');

        $this->validateToken($request->input('Security.token'));
        $userId = auth()->id();

        if ($request->has('delete_x')) {
            $quoteIds = array_keys($request->input('Quote', []));
            if (empty($quoteIds)) {
                Session::flash('message', '見積書が選択されていません');
                return redirect()->route('quotes.index', ['customer' => $customerId]);
            }

            foreach ($quoteIds as $quoteId) {
                $quote = Quote::find($quoteId);
                if ($quote && !$this->hasEditAuthority($quote->USR_ID)) {
                    Session::flash('message', '削除できない見積書が含まれていました');
                    return redirect()->route('quotes.index', ['customer' => $customerId]);
                }
            }

            Quote::destroy($quoteIds);
            History::logActions($userId, 4, $quoteIds);
            Session::flash('message', '見積書を削除しました');
            return redirect()->route('quotes.index', ['customer' => $customerId]);
        }

        if ($request->has('reproduce_quote_x')) {
            $result = $this->checkReproduce($request->all(), 'Quote');
            if ($result && $quoteId = $this->insertReproduce($result, $userId, Quote::class)) {
                Session::flash('message', '見積書に転記しました');
                return redirect("/quotes/edit/$quoteId");
            }
        }

        if ($request->has('reproduce_bill_x')) {
            $result = $this->checkReproduce($request->all());
            if ($result && $this->insertReproduce($result, $userId, Bill::class)) {
                Session::flash('message', '請求書に転記しました');
                return redirect()->route('bills.index', ['customer' => $customerId]);
            }
        }

        if ($request->has('reproduce_delivery_x')) {
            $result = $this->checkReproduce($request->all());
            if ($result && $this->insertReproduce($result, $userId, Delivery::class)) {
                Session::flash('message', '納品書に転記しました');
                return redirect()->route('deliveries.index', ['customer' => $customerId]);
            }
        }

        if ($request->has('status_change_x')) {
            return $this->statusChange($request->input('Quote'), ['controller' => 'quotes', 'action' => 'index', 'customer' => $customerId]);
        }
    }
    public function export(Request $request)
    {
        $browser = $request->header('User-Agent');

        if ($request->has('download_x')) {
            if ($request->has('Quote')) {
                $error = "";
                $quoteData = $request->input('Quote');
                $data = Quote::export($quoteData, $error, 'term', auth()->user()->authority, auth()->id());

                if ($data) {
                    $str = mb_convert_encoding("見積書", "SJIS-win", "UTF-8");
                    if (preg_match("/MSIE/", $browser) || preg_match('/Trident\/[0-9]\.[0-9]/', $browser)) {
                        return $this->outputXls($data, $str);
                    } else {
                        return $this->outputXls($data, "見積書");
                    }
                } else {
                    return redirect()->route('quotes.export')->with('error', $error);
                }
            }
        }

        return view('quotes.export', [
            'main_title' => '見積書Excel出力',
            'title_text' => '帳票管理'
        ]);
    }

    public function pdf($quote_id, $option = null)
    {
        $browser = request()->header('User-Agent');

        if (!$quote_id) {
            abort(404);
        }

        $items = 0;
        $discounts = 0;

        $param = Quote::preview_data($quote_id, $items, $discounts);
        if (!$param) {
            abort(404);
        }

        if (!$this->checkAuthority($param['Quote']['USR_ID'])) {
            return redirect()->route('quotes.export')->with('error', '帳票を閲覧する権限がありません');
        }

        $customer_charge = CustomerCharge::where('CHRC_ID', $param['Quote']['CHRC_ID'])->first();
        if ($customer_charge) {
            $param['CustomerCharge'] = $customer_charge->toArray();
        }

        $color = config('colorcode');

        if ($param['Company']['SEAL']) {
            $param['Company']['SEAL_IMAGE'] = $this->getTmpImagePath(null, true);
        }

        if ($param['Quote']['CHR_ID'] && $param['Charge']['SEAL']) {
            $param['Charge']['SEAL_IMAGE'] = $this->getTmpImagePath();
        }

        $param = $this->getCompatibleItems($param);
        $item_count = $param['count'];
        $county = config('prefecturecode');

        $pages = $this->calculatePages($param, $item_count);

        $pdf = $this->initializePdf($param, $pages);

        if ($option === 'download_with_coverpage') {
            $pdf->cover = 1;
            $pdf->AddPage();
            $pdf->coverpage($param, $county, 'Quote');
            $pdf->Total_Page = $pages + 1;
        }

        $pdf->cover = 0;
        $pdf->main($param, $county, $param['Company']['DIRECTION'], $items, $pages);

        $filename = "見積書_{$param['Quote']['SUBJECT']}.pdf";
        $filenameSJIS = mb_convert_encoding($filename, "SJIS-win", "UTF-8");

        if ($option === 'download' || $option === 'download_with_coverpage') {
            return $pdf->Output($filenameSJIS, 'D');
        } else {
            return $pdf->Output($filenameSJIS, 'I');
        }
    }
}