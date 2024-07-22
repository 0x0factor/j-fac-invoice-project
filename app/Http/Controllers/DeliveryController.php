<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Delivery;
use App\Models\Quote;
use App\Models\Bill;
use App\Models\Item;
use App\Models\Mail;
use App\Models\CustomerCharge;
use App\Models\Serial;
use App\Models\Charge;
use App\Models\Customer;
use App\Services\ExcelService;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    const DISCOUNT_TYPE_PERCENT = 0;
    const DISCOUNT_TYPE_NONE = 2;

    protected $excelService;

    public function __construct(ExcelService $excelService)
    {
        $this->excelService = $excelService;
        $this->middleware('auth');
    }

    // 一覧用
    public function index(Request $request)
    {
        $this->data = [];

        $this->data['main_title'] = "納品書管理";
        $this->data['title_text'] = "帳票管理";

        if ($request->has('customer')) {
            $customer = Customer::where('CST_ID', $request->query('customer'))->first();
            if ($customer) {
                $this->data[$this->name]['NAME'] = $customer->NAME;
                $this->data['customer_id'] = $request->query('customer');

                $insArray = [
                    'controller' => [
                        $this->name => [
                            'NAME' => $this->data[$this->name]['NAME']
                        ]
                    ]
                ];
                Session::put('session_params', $insArray);
            }
        }

        $this->data['mailstatus'] = config('app.MailStatusCode');
        $this->data['status'] = config('app.IssuedStatCode');

        return view('deliverie.index', $this->data);
    }

    // 登録用
    public function add(Request $request)
    {
        $this->data = [];
        $this->data['main_title'] = "納品書登録";
        $this->data['title_text'] = "帳票管理";

        if ($request->has('cancel_x')) {
            return redirect()->route('deliveries.index');
        }

        $company_ID = 1;
        $error = config('app.ItemErrorCode');
        $count = 1;

        if ($request->isMethod('post')) {
            // Validate token
            $this->validateToken($request->input('Security.token'));

            // Validate item data
            $error = $this->itemValidation($request->input('data'), 'Deliveryitem');

            // Validate discount
            $error['DISCOUNT'] = $this->validateDiscount($request->input('data'));

            if ($request->input('data.Delivery.DISCOUNT_TYPE') == self::DISCOUNT_TYPE_PERCENT) {
                $discount = mb_strlen($request->input('data.Delivery.DISCOUNT'));
                if ($discount > 2) {
                    $error['DISCOUNT'] = 1;
                }
                if ($request->input('data.Delivery.DISCOUNT') == '100') {
                    $error['DISCOUNT'] = 0;
                }
                if (!preg_match("/^[0-9]+$/", $request->input('data.Delivery.DISCOUNT')) && $request->input('data.Delivery.DISCOUNT') != null) {
                    $error['DISCOUNT'] = 2;
                }
            }

            if ($request->input('data.Delivery.HONOR_CODE') != 2) {
                $request->merge(['data.Delivery.HONOR_TITLE' => '']);
            }

            $deliveryData = $request->input('data.Delivery');
            $delivery = new Delivery();
            $result = $delivery->setData($deliveryData, 'new', $error);

            if ($result) {
                // Log action
                // Assuming you have a History service for logging actions
                app('App\Services\HistoryService')->h_reportaction($deliveryData['USR_ID'], 8, $result);

                // Success
                Session::flash('message', '納品書を保存しました');

                // Increment serial
                Serial::serial_increment('Delivery');

                return redirect()->route('deliveries.check', ['id' => $result]);
            } else {
                // Failure
                $count = count($request->input('data')) - 2 > 1 ? count($request->input('data')) - 2 : 1;

                // Set collapse settings
                $collaspe['other'] = empty($request->input('data.Delivery.DELIVERY')) ? 1 : 0;
                $collaspe['management'] = empty($request->input('data.Delivery.MEMO')) ? 1 : 0;
            }
        } else {
            // Default settings
            $collaspe['management'] = 1;
            $collaspe['other'] = 1;

            $delivery = new Delivery();
            if ($delivery->get_serial($company_ID) == 0) {
                $this->data['Delivery']['NO'] = Serial::get_number('Delivery');
            }

            $this->data['Delivery']['CST_ID'] = 'default';
            $this->data['Delivery']['item'] = 'default';
            $defult_cmp = $delivery->get_company_payment($company_ID);

            if ($defult_cmp) {
                $this->data['Delivery']['EXCISE'] = $defult_cmp['EXCISE'];
                $this->data['Delivery']['FRACTION'] = $defult_cmp['FRACTION'];
                $this->data['Delivery']['TAX_FRACTION'] = $defult_cmp['TAX_FRACTION'];
                $this->data['Delivery']['TAX_FRACTION_TIMING'] = $defult_cmp['TAX_FRACTION_TIMING'];
            } else {
                $this->data['Delivery']['EXCISE'] = 1;
                $this->data['Delivery']['FRACTION'] = 1;
                $this->data['Delivery']['TAX_FRACTION'] = 1;
                $this->data['Delivery']['TAX_FRACTION_TIMING'] = 0;
            }

            $default_dec = $delivery->get_decimal($company_ID);
            if ($default_dec) {
                $this->data['Delivery']['DECIMAL_QUANTITY'] = $default_dec[0]['Company']['DECIMAL_QUANTITY'];
                $this->data['Delivery']['DECIMAL_UNITPRICE'] = $default_dec[0]['Company']['DECIMAL_UNITPRICE'];
            } else {
                $this->data['Delivery']['DECIMAL_QUANTITY'] = 0;
                $this->data['Delivery']['DECIMAL_UNITPRICE'] = 0;
            }

            $this->data['Delivery']['DISCOUNT_TYPE'] = 2;
            $this->data['Delivery']['DATE'] = Carbon::now()->format('Y-m-d');
            $this->data['Delivery']['CMP_SEAL_FLG'] = app('App\Services\CompanyService')->getSealFlg();
            $this->data['Delivery']['CHR_SEAL_FLG'] = 0;

            $default_honor = $delivery->get_honor($company_ID);
            if ($default_honor) {
                $this->data['Delivery']['HONOR_CODE'] = $default_honor[0]['Company']['HONOR_CODE'];
                if ($default_honor[0]['Company']['HONOR_CODE'] == 2) {
                    $this->data['Delivery']['HONOR_TITLE'] = $default_honor[0]['Company']['HONOR_TITLE'];
                }
            }

            // Set tax operation date
            $taxOperationDate = config('app.TaxOperationDate');
            foreach ($taxOperationDate as $key => $value) {
                if ($this->data['Delivery']['DATE'] >= $value['start']) {
                    if ($this->data['Delivery']['DATE'] <= $value['end']) {
                        if ($key == 8) {
                            $tax_index = $key;
                            $defult_cmp['EXCISE'] = $tax_index . $defult_cmp['EXCISE'];
                        } elseif ($key == 5) {
                            $defult_cmp['EXCISE'];
                        }
                    } elseif ($key >= 10) {
                        $tax_index = $key;
                        $defult_cmp['EXCISE'] = $tax_index . $defult_cmp['EXCISE'];
                        break;
                    }
                }
            }
            $this->data['defaultExcise'] = $defult_cmp['EXCISE'];
        }

        // Get company information
        $company_ID = 1; // Assuming company_ID is 1, adjust as needed
        $cst_condition = $this->getUserAuthority() == 1
            ? ['Customer.CMP_ID' => $company_ID, 'Customer.USR_ID' => $this->getUserId()]
            : ['Customer.CMP_ID' => $company_ID];

        $items = Item::where('USR_ID', $this->getUserId())->get();
        $itemList = [];
        foreach ($items as $item) {
            $itemList[$item->ITM_ID] = [
                'ITEM' => $item->ITEM,
                'UNIT' => $item->UNIT,
                'UNIT_PRICE' => $item->UNIT_PRICE
            ];
        }

        // Set data for the view
        $this->data['excises'] = config('app.ExciseCode');
        $this->data['fractions'] = config('app.FractionCode');
        $this->data['tax_fraction_timing'] = config('app.TaxFractionTimingCode');
        $this->data['discount'] = config('app.DiscountCode');
        $this->data['decimal'] = config('app.DecimalCode');
        $this->data['status'] = config('app.IssuedStatCode');
        $this->data['companys'] = $delivery->get_customer($company_ID, $cst_condition);
        $this->data['error'] = $error;
        $this->data['dataline'] = $count;
        $this->data['item'] = $items->pluck('ITEM', 'ITM_ID')->prepend('＋アイテム選択＋', 'default')->prepend('＋アイテム追加＋', 'item');
        $this->data['itemlist'] = !empty($itemList) ? json_encode($itemList) : false;
        $this->data['honor'] = config('app.HonorCode');
        $this->data['hidden'] = $delivery->get_payment($company_ID);
        $this->data['collapse_other'] = $collaspe['other'];
        $this->data['collapse_management'] = $collaspe['management'];
        $this->data['lineAttribute'] = config('app.LineAttribute');
        $this->data['taxClass'] = config('app.TaxClass');
        $this->data['taxRates'] = config('app.TaxRates');
        $this->data['taxOperationDate'] = config('app.TaxOperationDate');
        $this->data['seal_flg'] = config('app.SealFlg');

        return view('deliverie.add', $this->data);
    }

    public function check(Request $request, $id)
    {
        $delivery = Delivery::find($id);

        if (!$delivery) {
            abort(404);
        }

        if (!$this->checkViewAuthority($delivery->USR_ID)) {
            return redirect('/deliveries/')->with('error', '帳票を閲覧する権限がありません');
        }

        // Load data necessary for the check view
        $items = $delivery->items; // Assuming there is a relationship defined
        $customerCharge = CustomerCharge::find($delivery->CHRC_ID);
        if ($customerCharge) {
            $delivery->CustomerCharge = $customerCharge;
        }

        $data = [
            'delivery' => $delivery,
            'items' => $items,
            'customerCharge' => $delivery->CustomerCharge,
        ];

        return view('deliverie.check', $data);
    }
    public function edit(Request $request, $id = null)
    {
        $data = [];
        $error = config('itemErrorCode');
        $count = 1;

        if (!$request->isMethod('post')) {
            // Fetch the delivery data if not a POST request
            $delivery = Delivery::find($id);
            if (!$delivery) {
                return redirect('/deliveries')->with('error', '指定の納品書が存在しません');
            }

            $data = $delivery->toArray();
            $count = $data['count'];
            $customerCharge = CustomerCharge::find($data['Delivery']['CHRC_ID']);
            if ($customerCharge) {
                $data['Delivery']['CUSTOMER_CHARGE_NAME'] = $customerCharge->CHARGE_NAME;
                $data['Delivery']['CUSTOMER_CHARGE_UNIT'] = $customerCharge->UNIT;
            }

            $data['Delivery']['item'] = 'default';

            if (!$this->checkEditAuthority($data['Delivery']['USR_ID'])) {
                return redirect('/deliveries/')->with('error', '帳票を編集する権限がありません');
            }
        } else {
            // Handle the POST request
            $request->validate([
                'Security.token' => 'required',
                'Delivery.MDV_ID' => 'required_if:del_x,1',
                // Add other validation rules as needed
            ]);

            if ($request->input('del_x')) {
                Delivery::destroy($request->input('Delivery.MDV_ID'));
                History::create([
                    'user_id' => Auth::id(),
                    'action_type' => 10,
                    'delivery_id' => $request->input('Delivery.MDV_ID'),
                ]);
                return redirect()->route('deliveries.index')->with('success', '削除しました。');
            }

            $user = Auth::user();
            $error = $this->itemValidation($request->input('data'), 'Deliveryitem');
            $error['DISCOUNT'] = $this->validateDiscount($request->input('data'));

            if ($request->input('Delivery.DISCOUNT_TYPE') != config('DISCOUNT_TYPE_PERCENT')) {
                $discount = mb_strlen($request->input('Delivery.DISCOUNT'));
                if ($discount > 2 || $request->input('Delivery.DISCOUNT') == '100' || !preg_match("/^[0-9]+$/", $request->input('Delivery.DISCOUNT'))) {
                    $error['DISCOUNT'] = 1;
                }
            }

            if ($request->input('Delivery.HONOR_CODE') != 2) {
                $request->merge(['Delivery.HONOR_TITLE' => '']);
            }

            $delivery = Delivery::updateOrCreate(
                ['MDV_ID' => $request->input('Delivery.MDV_ID')],
                $request->input('data')
            );

            if ($delivery) {
                History::create([
                    'user_id' => $user->id,
                    'action_type' => 9,
                    'delivery_id' => $delivery->MDV_ID,
                ]);
                return redirect()->route('deliveries.check', ['id' => $delivery->MDV_ID])->with('success', '納品書を保存しました');
            } else {
                return back()->withInput()->withErrors($error);
            }
        }

        $items = Item::where('USR_ID', Auth::id())->pluck('ITEM', 'ITM_ID')->toArray();
        $hidden = Delivery::getPayment(Auth::id());
        $hidden['default'] = Delivery::getDefaultPayment();

        $this->data['Delivery']['DECIMAL_QUANTITY'] = Delivery::getDecimalQuantity(Auth::id());
        $this->data['Delivery']['DECIMAL_UNITPRICE'] = Delivery::getDecimalUnitPrice(Auth::id());

        $this->data['itemlist'] = $items;
        $this->data['hidden'] = $hidden;
        $this->data['error'] = $error;
        $this->data['dataline'] = $count;

        return view('deliverie.edit', $this->data);
    }

    public function action(Request $request)
    {
        $customer_id = $request->input('Customer.id');

        if ($request->input('delete_x')) {
            if (!$request->input('data.Delivery')) {
                return redirect()->route('deliveries.index', ['customer' => $customer_id])->with('error', '納品書が選択されていません');
            }

            foreach ($request->input('data.Delivery') as $key => $val) {
                if ($val == 1) {
                    $delivery = Delivery::find($key);
                    if ($delivery && !$this->checkEditAuthority($delivery->USR_ID)) {
                        return redirect()->route('deliveries.index', ['customer' => $customer_id])->with('error', '削除できない請求書が含まれていました');
                    }
                }
            }

            Delivery::destroy(array_keys($request->input('data.Delivery')));

            foreach ($request->input('data.Delivery') as $key => $value) {
                if ($value == 1) {
                    History::create([
                        'user_id' => Auth::id(),
                        'action_type' => 10,
                        'delivery_id' => $key,
                    ]);
                }
            }

            return redirect()->route('deliveries.index', ['customer' => $customer_id])->with('success', '納品書を削除しました');
        }

        if ($request->input('reproduce_quote_x')) {
            $result = Delivery::reproduceCheck($request->input('data'));
            if ($result && Quote::insertReproduce($result, Auth::id())) {
                return redirect()->route('quote.index', ['customer' => $customer_id])->with('success', '見積書に転記しました');
            }
            return redirect()->route('deliveries.index', ['customer' => $customer_id]);
        }

        if ($request->input('reproduce_bill_x')) {
            $result = Delivery::reproduceCheck($request->input('data'));
            if ($result && Bill::insertReproduce($result, Auth::id())) {
                return redirect()->route('bills.index', ['customer' => $customer_id])->with('success', '請求書に転記しました');
            }
            return redirect()->route('deliveries.index', ['customer' => $customer_id]);
        }

        if ($request->input('reproduce_delivery_x')) {
            $result = Delivery::reproduceCheck($request->input('data'), Serial::getSerialConf(), 'Delivery');
            if ($result && $inv_id = Delivery::insertReproduce($result, Auth::id())) {
                if ($request->input('form_check')) {
                    return redirect()->route('deliveries.edit', ['id' => $inv_id])->with('success', '納品書に転記しました');
                }
                return redirect()->route('deliveries.index', ['customer' => $customer_id])->with('success', '納品書に転記しました');
            }
            return redirect()->route('deliveries.index', ['customer' => $customer_id]);
        }

        if ($request->input('status_change_x')) {
            return $this->statusChange($request->input('data.Delivery'), ['controller' => 'deliveries', 'action' => 'index', 'customer' => $customer_id]);
        }
    }

    public function export(Request $request)
    {
        if ($request->input('download_x')) {
            if ($request->input('data.Delivery')) {
                $error = "";
                $data = Delivery::export($request->input('data.Delivery'), $error, 'term', Auth::user()->auth_level, Auth::id());

                if ($data) {
                    $fileName = "納品書";
                    if (preg_match("/MSIE/", $request->header('User-Agent')) || preg_match('/Trident\/[0-9]\.[0-9]/', $request->header('User-Agent'))) {
                        return Excel::download(new DeliveryExport($data), $fileName . '.xls');
                    } else {
                        return Excel::download(new DeliveryExport($data), $fileName . '.xlsx');
                    }
                } else {
                    return redirect('/deliveries/export')->with('error', $error);
                }
            }
        }

        return view('deliverie.export', ['main_title' => '納品書Excel出力', 'title_text' => '帳票管理']);
    }

    public function pdf(Request $request, $id)
    {
        $delivery = Delivery::find($id);
        if (!$delivery) {
            abort(404);
        }

        $items = 0;
        $discounts = 0;
        $param = Delivery::previewData($id, $items, $discounts);

        if (!$this->checkViewAuthority($param['Delivery']['USR_ID'])) {
            return redirect('/deliveries/')->with('error', '帳票を閲覧する権限がありません');
        }

        $county = config('prefecture_code');
        $direction = $param['Company']['DIRECTION'];
        $pages = $this->calculatePages($param, $direction);

        $pdf = PDF::loadView('pdf.delivery', [
            'param' => $param,
            'county' => $county,
            'direction' => $direction,
            'pages' => $pages,
            'items' => $items
        ]);

        $fileName = "納品書_{$param['Delivery']['SUBJECT']}.pdf";

        if ($request->input('pass.1') === 'download_with_coverpage') {
            $pdf->setOption('coverpage', true);
            $fileName = "送付状_{$param['Delivery']['SUBJECT']}.pdf";
        }

        return $pdf->download($fileName);
    }

}
