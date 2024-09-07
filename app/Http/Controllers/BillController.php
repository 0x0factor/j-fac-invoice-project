<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Quote;
use App\Models\Delivery;
use App\Models\Item;
use App\Models\Receipt;
use App\Models\CustomerCharge;
use App\Models\User;
use App\Models\Mail;
use App\Models\Serial;
use App\Models\Charge;
use App\Models\Customer;
use App\Models\Company;
use App\Models\History;
use App\Components\ExcelComponent;
use App\Services\ExcelService;
use App\Services\PDF\BillPDF;
use App\Services\PDF\BillPDFSide;
use App\Services\PDF\ReceiptPDF;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

use App\Main\AppController;


class BillController extends AppController
{
    const DISCOUNT_TYPE_PERCENT = 0;
    const DISCOUNT_TYPE_NONE = 2;


    protected $excel;

    public function __construct()
    {
        $this->middleware('auth');
        // $this->excel = $excel;
    }


    public function index(Request $request)
    {
        $bills = Bill::orderBy('STATUS', 'DESC')->paginate(20);
        $authority = [];
        $list = $bills->items();

        foreach ($bills as $bill) {
            $authority[$bill->user->USR_ID] = $this->Get_Edit_Authority($bill->user->USR_ID) ? true : false;
        }

        if ($request->has('customer')) {
            $customer = Customer::find($request->input('customer'));
            $customerName = $customer->NAME;
            $request->session()->put('session_params', [
                $request->route()->getName() => [
                    'NAME' => $customerName
                ]
            ]);
        }

        $name = Auth::user()->NAME;
        $query = Bill::query();

        if ($request->NO) {
            $query->where('MQT_ID', 'like', '%' . $request->NO . '%');
        }

        if ($request->SUBJECT) {
            $query->where('SUBJECT', 'like', '%' . $request->SUBJECT . '%');
        }

        if ($request->CHR_USR_NAME) {
            $query->where('CHR_ID', 'like', '%' . $request->CHR_USR_NAME . '%');
        }

        if ($request->USR_NAME) {
            $query->where('USR_ID', 'like', '%' . $request->USR_NAME . '%');
        }

        if ($request->UPD_USR_NAME) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->UPD_USR_NAME . '%');
        }

        if ($request->STATUS) {
            $query->whereIn('STATUS', $request->STATUS);
        }

        if ($request->ITEM_NAME) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->ITEM_NAME . '%');
        }
        if ($request->ITEM_CODE) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->ITEM_CODE . '%');
        }
        if ($request->TOTAL_FROM) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->TOTAL_FROM . '%');
        }
        if ($request->TOTAL_TO) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->TOTAL_TO . '%');
        }
        if ($request->ACTION_DATE_FROM) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->ACTION_DATE_FROM . '%');
        }
        if ($request->ACTION_DATE_TO) {
            $query->where('UPDATE_USR_ID', 'like', '%' . $request->ACTION_DATE_TO . '%');
        }
        if ($request->NOTE) {
            $query->where('NOTE', 'like', '%' . $request->NOTE . '%');
        }
        if ($request->MEMO) {
            $query->where('MEMO', 'like', '%' . $request->MEMO . '%');
        }

        $searchData = $request ? $request: "";
        $searchStatus = $request->STATUS;


        return view('bill.index', [
            'bills' => $bills,
            'authority' => $authority,
            'main_title' => '請求書管理',
            'title_text' => '帳票管理',
            'title' => "抹茶請求書",
            'controller_name' => "Bill",
            'list' => $list,
            'searchData' => $searchData,
            'searchStatus' => $searchStatus,
            'mailstatus' => config('constants.MailStatusCode'),
            'status' => config('constants.IssuedStatCode')
        ]);
    }

    public function add(Request $request)
    {
        $mainTitle = '請求書登録';
        $titleText = '帳票管理';
        $title = "抹茶請求書";
        $controller_name = "Bill";

        $companyID = 1;

        if ($request->has('cancel_x')) {
            return redirect('/bills');
        }

        $data = $request->input('data');

        if (isset($data['Bill']['DISCOUNT_TYPE']) && $data['Bill']['DISCOUNT_TYPE'] == self::DISCOUNT_TYPE_NONE) {
            $data['Bill']['DISCOUNT'] = '';
        }

        $error = config('constants.ItemErrorCode');
        $count = 1;

        if ($data && $this->isCorrectToken($data['Security']['token'])) {
            $error = $this->itemValidation($data, 'Billitem');
            $error['DISCOUNT'] = $this->validateDiscount($data['Bill']);

            if ($data['Bill']['DISCOUNT_TYPE'] == self::DISCOUNT_TYPE_PERCENT) {
                $discountLength = strlen($data['Bill']['DISCOUNT']);
                if ($discountLength > 2) {
                    $error['DISCOUNT'] = 1;
                }
                if ($data['Bill']['DISCOUNT'] == '100') {
                    $error['DISCOUNT'] = 0;
                }
                if (!preg_match("/^[0-9]+$/", $data['Bill']['DISCOUNT']) && $data['Bill']['DISCOUNT'] != null) {
                    $error['DISCOUNT'] = 2;
                }
            }

            if ($data['Bill']['HONOR_CODE'] != 2) {
                $data['Bill']['HONOR_TITLE'] = '';
            }

            if ($MBL_ID = $this->setData($data, 'new', $error)) {
                $this->historyReportAction($data['Bill']['USR_ID'], 5, $MBL_ID);
                Session::flash('message', '請求書を保存しました');
                Serial::increment('Bill');
                return redirect("/bills/check/{$MBL_ID}");
            } else {
                $count = max(1, count($data) - 2);
                $collaspe = [
                    'other' => empty($data['Bill']['FEE']) && empty($data['Bill']['DUE_DATE']) ? 1 : 0,
                    'management' => empty($data['Bill']['MEMO']) ? 1 : 0
                ];
            }
        } else {
            $collaspe = [
                'management' => 1,
                'other' => 1
            ];

            $serial = new Serial();
            $data['Bill']['NO'] = $serial->get_number('Bill');
            $data['Bill']['CST_ID'] = 'default';
            $data['Bill']['item'] = 'default';

            $bill = new Bill;
            $defaultCompany = $bill->getCompanyPayment($companyID);
            $data['Bill'] = array_merge($data['Bill'], [
                'EXCISE' => $defaultCompany['EXCISE'] ?? 1,
                'FRACTION' => $defaultCompany['FRACTION'] ?? 1,
                'TAX_FRACTION' => $defaultCompany['TAX_FRACTION'] ?? 1,
                'TAX_FRACTION_TIMING' => $defaultCompany['TAX_FRACTION_TIMING'] ?? 0
            ]);

            $defaultDecimal = $bill->getDecimal($companyID);
            $data['Bill'] = array_merge($data['Bill'], [
                'DECIMAL_QUANTITY' => $defaultDecimal['DECIMAL_QUANTITY'] ?? 0,
                'DECIMAL_UNITPRICE' => $defaultDecimal['DECIMAL_UNITPRICE'] ?? 0
            ]);

            $data['Bill']['DISCOUNT_TYPE'] = self::DISCOUNT_TYPE_NONE;
            $data['Bill']['DATE'] = now()->toDateString();
            $company = new Company;
            $data['Bill']['CMP_SEAL_FLG'] = $company->getSealFlg();
            $data['Bill']['CHR_SEAL_FLG'] = 0;

            $defaultHonor = $bill->getHonor($companyID);
            // dd(json_decode(json_encode($defaultHonor[0]), true)['HONOR_CODE']);
            if ($defaultHonor) {
                $data['Bill']['HONOR_CODE'] = json_decode(json_encode($defaultHonor[0]), true)['HONOR_CODE'];
                if (json_decode(json_encode($defaultHonor[0]), true)['HONOR_CODE'] == 2) {
                    $data['Bill']['HONOR_TITLE'] = json_decode(json_encode($defaultHonor[0]), true)['HONOR_TITLE'];
                }
            }

            $taxOperationDate = config('constants.TaxOperationDate');
            $taxOperationDate = $taxOperationDate ?? [];
            foreach ($taxOperationDate as $key => $value) {
                if ($data['Bill']['DATE'] >= $value['start'] && $data['Bill']['DATE'] <= $value['end']) {
                    if ($key == 8 || $key >= 10) {
                        $data['Bill']['EXCISE'] = "{$key}{$defaultCompany['EXCISE']}";
                        break;
                    }
                }
            }
        }

        $items = Item::where('USR_ID', $bill->getUser($companyID))->pluck('ITEM', 'ITM_ID')->toArray();
        $company = $bill->getCustomer($companyID);
        $hidden = $bill->getPayment($companyID);
        $defaultCompany = $bill->getCompanyPayment($companyID);
        $user = Auth::user();
        $action = config('constants.ActionCode');


        $name = $user['NAME'];

        $hidden['default'] = [
            'EXCISE' => $defaultCompany['EXCISE'] ?? 1,
            'FRACTION' => $defaultCompany['FRACTION'] ?? 1,
            'TAX_FRACTION' => $defaultCompany['TAX_FRACTION'] ?? 1,
            'TAX_FRACTION_TIMING' => $defaultCompany['TAX_FRACTION_TIMING'] ?? 0
        ];

        return view('bill.add', [
            'main_title' => $mainTitle,
            'title_text' => $titleText,
            'title' => $title,
            'controller_name' => $controller_name,
            'name' => $name,
            'action' => $action,
            'user' => $user,
            'excises' => config('constants.ExciseCode'),
            'fractions' => config('constants.FractionCode'),
            'tax_fraction_timing' => config('constants.TaxFractionTimingCode'),
            'discount' => config('constants.DiscountCode'),
            'status' => config('constants.IssuedStatCode'),
            'decimal' => config('constants.DecimalCode'),
            'itemlist' => json_encode($items),
            'companys' => $company,
            'error' => $error,
            'dataline' => $count,
            'item' => array_merge(['＋アイテム追加＋', '＋アイテム選択＋'], $items),
            'hidden' => $hidden,
            'honor' => config('constants.HonorCode'),
            'page_title' => 'Bill',
            'collapse_other' => $collaspe['other'],
            'collapse_management' => $collaspe['management'],
            'lineAttribute' => config('constants.LineAttribute'),
            'taxClass' => config('constants.TaxClass'),
            'taxRates' => config('constants.TaxRates'),
            'taxOperationDate' => config('constants.TaxOperationDate'),
            'seal_flg' => config('constants.SealFlg'),
        ]);
    }

    // 確認
    public function check($bill_ID = null)
    {
        if (is_null($bill_ID)) {
            Session::flash('error', '指定の請求書が存在しません');
            return redirect('/bills/index');
        }

        $param = Bill::edit_select($bill_ID, $count);
        if (!$param) {
            Session::flash('error', '指定の請求書が削除されたか、存在しない可能性があります');
            return redirect('/bills/index');
        }

        $param['Charge']['NAME'] = Charge::get_charge($param['Bill']['CHR_ID']);

        $customer_charge = CustomerCharge::select(['CHRC_ID' => $param['Bill']['CHRC_ID']])->first();
        if ($customer_charge) {
            $param['CustomerCharge'] = $customer_charge->CustomerCharge;
        }

        if (!$this->getCheckAuthority($param['Bill']['USR_ID'])) {
            Session::flash('error', '帳票を閲覧する権限がありません');
            return redirect('/bills/');
        }

        $param = $this->getCompatibleItems($param);
        $count = $param['count'];

        $editauth = $this->Get_Edit_Authority($param['Bill']['USR_ID']);

        // Set data for the view
        return view('bill.check', [
            'main_title' => '請求書確認',
            'title_text' => '帳票管理',
            'title' => "抹茶請求書",
            'status' => config('constants.IssuedStatCode'),
            'decimals' => config('constants.DecimalCode'),
            'excises' => config('constants.ExciseCode'),
            'fractions' => config('constants.FractionCode'),
            'tax_fraction_timing' => config('constants.TaxFractionTimingCode'),
            'honor' => config('constants.HonorCode'),
            'param' => $param,
            'dataline' => $count,
            'editauth' => $editauth,
            'controller_name' => "Bill",
            'seal_flg' => config('constants.SealFlg')
        ]);
    }

    public function edit(Request $request, $bill_ID = null)
    {
        $data = $request->all();
        $company_ID = 1; // Test data
        $count = 1;

        if ($request->has('cancel_x')) {
            return redirect('/bills');
        }

        $error = config('constants.ItemErrorCode');
        $user_ID = $this->Get_User_ID();
        $user_auth = $this->Get_User_AUTHORITY();

        if (empty($data)) {
            // Initial Data Fetch
            $bill = Bill::find($bill_ID);
            if (!$bill) {
                Session::flash('message', '指定の請求書が存在しません');
                return redirect('/bills/index');
            }

            $data = $bill->toArray();

            // Get Customer Charge Details
            $customer_charge = CustomerCharge::find($bill->CHRC_ID);
            if ($customer_charge) {
                $data['Bill']['CUSTOMER_CHARGE_NAME'] = $customer_charge->CHARGE_NAME;
                $data['Bill']['CUSTOMER_CHARGE_UNIT'] = $customer_charge->UNIT;
            }

            if (!$this->hasEditAuthority($bill->USR_ID)) {
                Session::flash('message', '帳票を編集する権限がありません');
                return redirect('/bills/');
            }
        } else {
            // Token Check
            if (!$this->isCorrectToken($data['Security']['token'])) {
                return redirect('/bills/edit/' . $bill_ID);
            }

            if ($request->has('del_x')) {
                Bill::destroy($data['Bill']['MBL_ID']);
                Session::flash('message', '削除しました。');
                return redirect('/bills/index');
            }

            // Validation
            $error = $this->itemValidation($data, 'Billitem');

            // Discount Validation
            $error['DISCOUNT'] = $this->validateDiscount($data);

            if ($data['Bill']['DISCOUNT_TYPE'] == 'DISCOUNT_TYPE_PERCENT') {
                $discount = strlen($data['Bill']['DISCOUNT']);

                if ($discount > 2) {
                    $error['DISCOUNT'] = 1;
                }
                if ($data['Bill']['DISCOUNT'] == '100') {
                    $error['DISCOUNT'] = 0;
                }
                if (!preg_match("/^[0-9]+$/", $data['Bill']['DISCOUNT']) && $data['Bill']['DISCOUNT'] != null) {
                    $error['DISCOUNT'] = 2;
                }
            }

            if ($data['Bill']['HONOR_CODE'] != 2) {
                $data['Bill']['HONOR_TITLE'] = "";
            }

            // Data Insert
            $bill = Bill::find($data['Bill']['MBL_ID']);
            if ($bill->update($data['Bill'])) {
                History::create(['USR_ID' => $user_ID, 'ACTION' => 6, 'MBL_ID' => $data['Bill']['MBL_ID']]);
                Session::flash('message', '請求書を保存しました');
                return redirect('/bills/check/' . $data['Bill']['MBL_ID']);
            } else {
                $count = max(count($data) - 2, 1);

                // Collapsing Panels
                $collaspe = [
                    'other' => empty($data['Bill']['FEE']) && empty($data['Bill']['DUE_DATE']),
                    'management' => empty($data['Bill']['MEMO']),
                ];
            }
        }

        // Fetch Items for the Company
        $itemQuery = Item::query();
        if ($user_auth != 1) {
            $itemQuery->where('CMP_ID', $company_ID);
        }
        $items = $itemQuery->get();

        // Hidden fields and default settings
        $hidden = $this->getPayment($company_ID);
        $defaultCmp = $this->getCompanyPayment($company_ID);
        $hidden['default'] = [
            'EXCISE' => $defaultCmp->EXCISE ?? 1,
            'FRACTION' => $defaultCmp->FRACTION ?? 1,
            'TAX_FRACTION' => $defaultCmp->TAX_FRACTION ?? 1,
            'TAX_FRACTION_TIMING' => $defaultCmp->TAX_FRACTION_TIMING ?? 0,
        ];

        $defaultDec = $this->getDecimal($company_ID);
        $data['Bill']['DECIMAL_QUANTITY'] = $defaultDec->DECIMAL_QUANTITY ?? 0;
        $data['Bill']['DECIMAL_UNITPRICE'] = $defaultDec->DECIMAL_UNITPRICE ?? 0;

        $itemList = [];
        foreach ($items as $item) {
            $itemList[$item->ITM_ID] = [
                'ITEM' => $item->ITEM,
                'UNIT' => $item->UNIT,
                'UNIT_PRICE' => $item->UNIT_PRICE,
            ];
        }

        if (isset($data['Customer']['NAME'])) {
            $data['Bill']['CUSTOMER_NAME'] = $data['Customer']['NAME'];
            $data['Bill']['CHARGE_NAME'] = $this->getChargeName($data['Bill']['CHR_ID']);
        }

        return view('bill.edit', [
            'excises' => config('constants.ExciseCode'),
            'fractions' => config('constants.FractionCode'),
            'tax_fraction_timing' => config('constants.TaxFractionTimingCode'),
            'discount' => config('constants.DiscountCode'),
            'decimal' => config('constants.DecimalCode'),
            'status' => config('constants.IssuedStatCode'),
            'itemlist' => $itemList,
            'error' => $error,
            'dataline' => $count,
            'item' => $items,
            'hidden' => $hidden,
            'honor' => config('constants.HonorCode'),
            'collapse_other' => $collaspe['other'],
            'collapse_management' => $collaspe['management'],
            'lineAttribute' => config('constants.LineAttribute'),
            'seal_flg' => config('constants.SealFlg'),
            'taxClass' => config('constants.TaxClass'),
            'taxRates' => config('constants.TaxRates'),
            'controller_name' => "Bill",
            'taxOperationDate' => config('constants.TaxOperationDate'),
        ]);
    }
    public function action(Request $request)
    {
        dd($request->all());
        $bill = new Bill();
        // 絞り込みした場合の顧客IDを取得
        $customer_id = $request->input('Customer.id');

        $form_check = false;
        if ($request->has('Action.type')) {
            $request->merge(['reproduce_' . $request->input('Action.type') . '_x' => 1]);
            $form_check = true; // 詳細から転記したかどうか
        }
        // トークンチェック
        // $this->isCorrectToken($request->input('data.Security.token'));

        $user_ID = $this->Get_User_ID(); // Assuming Get_User_ID() function exists
        $billIds = $request->input('selected', []);
        dd($request->all(), $billIds);

        if ($request->has('delete_x')) {
            if (empty($billIds)) {
                Session::flash('error', '請求書が選択されていません');
                return redirect()->route('bill.index', ['customer' => $customer_id]);
            }

            // 削除
            foreach ($billIds as $key => $val) {
                $bill = Bill::find($val);
                if ($bill && !$this->Get_Edit_Authority($bill->USR_ID)) {
                    Session::flash('message', '削除できない請求書が含まれていました');
                    return redirect()->route('bill.index', ['customer' => $customer_id]);
                }
                $history = new History();
                $history->h_reportaction($user_ID, 4, $val);
            }

            Bill::destroy($billIds);
            Session::flash('message', '見積書を削除しました');
            return redirect()->route('quote.index', ['customer' => $customer_id]);
        }

        // 見積書へ複製
        elseif ($request->has('reproduce_quote_x')) {
            if ($result = $bill->reproduce_check($billIds, false)) {
                // 成功
                if (Quote::insert_reproduce($result, $user_ID)) {
                    Session::flash('success', '見積書に転記しました');
                    return redirect()->route('quote.index', ['customer' => $customer_id]);
                } else {
                    return redirect()->route('bill.index', ['customer' => $customer_id]);
                }
            } else {
                // 失敗
                return redirect()->route('bill.index', ['customer' => $customer_id]);
            }
        }

        // 請求書へ複製
        elseif ($request->has('reproduce_bill_x')) {
            // dd($request->all(), $billIds);
            // $serial = new Serial();
            // $serial->getSerialConf(),
            if ($result = $bill->reproduce_check($request->all(), 'Bill')) {
                // 成功
                if ($inv_id = $bill->insert_reproduce($result, $user_ID)) {
                    Session::flash('success', '請求書に転記しました');
                    if ($form_check) {
                        return redirect("/bills/edit/$inv_id");
                    } else {
                        return redirect()->route('bill.index', ['customer' => $customer_id]);
                    }
                } else {
                    // 失敗
                    return redirect()->route('bill.index', ['customer' => $customer_id]);
                }
            } else {
                // 失敗
                return redirect()->route('bill.index', ['customer' => $customer_id]);
            }
        }

        // 納品書へ複製
        elseif ($request->has('reproduce_delivery_x')) {
            if ($result = $bill->reproduce_check($billIds, false)) {
                // 成功
                if (Delivery::insert_reproduce($result, $user_ID)) {
                    Session::flash('success', '納品書に転記しました');
                    return redirect()->route('deliveries.index', ['customer' => $customer_id]);
                } else {
                    // 失敗
                    return redirect()->route('bill.index', ['customer' => $customer_id]);
                }
            } else {
                // 失敗
                return redirect()->route('bill.index', ['customer' => $customer_id]);
            }
        }

        // 発行ステータス一括変更
        elseif ($request->has('status_change_x')) {
            return $this->status_change($billIds, ['controller' => 'bills', 'action' => 'index', 'customer' => $customer_id]);
        }
    }

    // excel形式の一覧を抽出します
    // public function export(Request $request, ExcelService $excelService)
    public function export(Request $request)
    {
        // Browser identification
        $browser = $request->server('HTTP_USER_AGENT');
        $data = [
            'title' => 'Sample PDF',
            'date' => date('m/d/Y'),
        ];

        // // Load the view and pass the data
        // $pdf = PDF::loadView('bill.pdf', $data);

        // // Define print format type (e.g., A4, A3, Letter)
        // $pdf->setPaper('A4', 'portrait'); // Options: 'A4', 'A3', 'Letter', etc.

        // return $pdf->download('bill.pdf');
        if ($request->filled('download_x')) {
            $billIds = $request->input('Bill', []);

            if (!empty($billIds)) {
                $error = "";
                $data = (new Bill())->export($billIds, $error, 'term', $this->Get_User_AUTHORITY(), $this->Get_User_ID());

                if ($data) {
                    $fileName = "請求書";
                    if (preg_match("/MSIE/", $browser) || preg_match('/Trident\/[0-9]\.[0-9]/', $browser)) {
                        $excelService->outputXls($fileName, $data);
                    } else {
                        $excelService->outputXls($fileName, $data);
                    }
                } else {
                    Session::flash('error', $error);
                    return redirect()->route('bill.export');
                }
            }
        }

        return view('bill.export')->with([
            'main_title' => '請求書Excel出力',
            'title_text' => '帳票管理',
            'title' => '抹茶請求書',
             'controller_name' => "Bill",
        ]);
    }

    public function pdf(Request $request)
    {
        // Disable layout
        $this->layout = false;

        // Get bill ID
        $billId = $request->route('id');

        if (!$billId) {
            abort(404);
        }

        $items = 0;
        $discounts = 0;

        // Fetch data
        $param = (new Bill())->preview_data($billId, $items, $discounts);

        if (!$this->Get_Check_Authority($param['Bill']['USR_ID'])) {
            Session::flash('error', '帳票を閲覧する権限がありません');
            return redirect()->route('bill.index');
        }

        $Color = config('constants.ColorCode'); // Assuming color code is stored in config/constants.php

        $customerCharge = (new CustomerCharge())->select(['CHRC_ID' => $param['Bill']['CHRC_ID']]);

        if ($customerCharge) {
            $param['CustomerCharge'] = $customerCharge[0]['CustomerCharge'];
        }

        // Set URLs
        if ($param['Company']['SEAL']) {
            $param['Company']['SEAL_IMAGE'] = $this->getTmpImagePath(null, true); // Implement getTmpImagePath
        }

        if ($param['Bill']['CHR_ID'] && $param['Charge']['SEAL']) {
            $param['Charge']['SEAL_IMAGE'] = $this->getTmpImagePath(); // Implement getTmpImagePath
        }

        // Add compatible items
        $param = $this->getCompatibleItems($param); // Implement getCompatibleItems

        $itemCount = $param['count'];
        $direction = $param['Company']['DIRECTION'];

        $pages = 1;
        $county = config('constants.PrefectureCode');
        $accounttype = config('constants.AccountTypeCode');

        // Calculate pages
        for ($i = 0, $itemCountPerPage = 0; $i < $itemCount; $i++) {
            $fbreak = isset($param[$i]['Billitem']['LINE_ATTRIBUTE']) && intval($param[$i]['Billitem']['LINE_ATTRIBUTE']) == 8;

            if ($direction == 0) {
                // Vertical
                if ($pages == 1) {
                    if ($fbreak) {
                        $pages++;
                        $itemCountPerPage = 0;
                    } elseif ($itemCountPerPage >= 20) {
                        $pages++;
                        $itemCountPerPage = -(10-1); // Start counting from previous page's 10 items
                    } else {
                        $itemCountPerPage++;
                    }
                } else {
                    if ($fbreak) {
                        $pages++;
                        $itemCountPerPage = 0;
                    } elseif ($itemCountPerPage >= 30) {
                        $pages++;
                        $itemCountPerPage = -(10-1); // Start counting from previous page's 10 items
                    } else {
                        $itemCountPerPage++;
                    }
                }
            } else {
                // Horizontal
                if ($pages == 1) {
                    if ($fbreak) {
                        $pages++;
                        $itemCountPerPage = 0;
                    } elseif ($itemCountPerPage >= 14) {
                        $pages++;
                        $itemCountPerPage = -(6-1); // Start counting from previous page's 6 items
                    } else {
                        $itemCountPerPage++;
                    }
                } else {
                    if ($fbreak) {
                        $pages++;
                        $itemCountPerPage = 0;
                    } elseif ($itemCountPerPage >= 24) {
                        $pages++;
                        $itemCountPerPage = -(6-1); // Start counting from previous page's 6 items
                    } else {
                        $itemCountPerPage++;
                    }
                }
            }
        }

        // Browser identification
        $browser = $request->server('HTTP_USER_AGENT');

        // Instantiate PDF
        if ($direction == 1) {
            $pdf = new BillPDFSide();
        } else {
            $pdf = new BillPDF();
        }

        $pdf->AddMBFont(MINCHO, 'SJIS');
        $pdf->Total_Page = $pages;
        $pdf->Direction = $direction;

        // Download with cover page
        if ($request->route('option') === 'download_with_coverpage') {
            $pdf->cover = 1;
            $pdf->AddPage();
            $pdf->coverpage($param, 'Bill'); // Implement coverpage method
            $pdf->Total_Page = $pages + 1;
        }

        // Create pages
        if ($direction == 1) {
            $pdf->AddPage('L');
        } else {
            $pdf->AddPage();
        }

        $pdf->cover = 0;
        // Add main content
        $pdf->main($param); // Implement main method

        // Download PDF
        if ($request->route('option') === 'download' || $request->route('option') === 'download_with_coverpage') {
            $fileName = "請求書_{$param['Bill']['SUBJECT']}.pdf";
            return $pdf->Output($fileName, 'D');
        } else {
            $fileName = "請求書_{$param['Bill']['SUBJECT']}.pdf";
            return $pdf->Output($fileName, 'I');
        }
    }

    public function receipt(Request $request)
    {
        $mainTitle = "領収書発行";
        $titleText = "帳票管理";
        $title = "抹茶請求書";


        $errors = 0;

        // Handle cancel button press
        if ($request->filled('cancel_x')) {
            return redirect()->route('bill.index');
        }

        // Handle form submission
        if ($request->filled('submit')) {
            // Validate form data
            $request->validate([
                // Validation rules here
            ]);

            // Disable layout
            $this->layout = false;

            // Get bill ID
            $billId = $request->route('id');

            // Fetch data
            $param = (new Bill())->preview_data($billId);

            // Add receipt number etc.
            $param = array_merge($param, $request->all());

            // Get color code
            $Color = config('constants.ColorCode'); // Assuming color code is stored in config/constants.php
            $county = config('constants.PrefectureCode');

            // Browser identification
            $browser = $request->server('HTTP_USER_AGENT');

            // Instantiate PDF
            $pdf = new ReceiptPDF();
            $pdf->AddMBFont(MINCHO, 'SJIS');
            $pdf->AddPage();
            $pdf->main($param); // Implement main method

            // Increment serial number
            (new Serial())->serial_increment('Receipt'); // Implement serial_increment method

            $fileName = "領収書_.pdf";
            return $pdf->Output($fileName, 'D');
        }

        // Initial data retrieval
        $billId = $request->route('id');

        if (!$billId) {
            return redirect()->route('bill.index');
        }

        $data = (new Bill())->preview_data($billId);
        $company = (new Bill())->get_customer($company_ID);

        if ($request->filled('submit')) {
            $company = $request->input('Bill.CST_ID');
        } else {
            $company = $data['Bill']['CST_ID'];
        }

        $billId = $data['Bill']['MBL_ID'];
        $data = array_merge((new Bill())->preview_data($billId), $data);

        if (isset($data['Bill']['STATUS']) && $data['Bill']['STATUS'] != 1) {
            Session::flash('error', '領収書を作成できません');
            return redirect()->route('bill.index');
        }

        if (!$this->Get_Edit_Authority($data['Bill']['USR_ID'])) {
            Session::flash('error', '帳票を閲覧する権限がありません');
            return redirect()->route('bill.index');
        }

        // Rounding
        if ($data['Customer']['FRACTION'] == 0) {
            $data['Customer']['TOTAL'] = ceil($data['Bill']['TOTAL']);
        }

        if ($data['Customer']['FRACTION'] == 1) {
            $data['Customer']['TOTAL'] = floor($data['Bill']['TOTAL']);
        }

        if ($data['Customer']['FRACTION'] == 2) {
            $data['Customer']['TOTAL'] = round($data['Bill']['TOTAL']);
        }

        return view('bill.receipt')->with([
            'main_title' => $mainTitle,
            'title_text' => $titleText,
            'controller_name' => "Bill",
            'error' => $errors,
            'companys' => $company
        ]);
    }



}
