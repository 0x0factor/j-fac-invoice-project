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
use App\Models\User;
use App\Models\Charge;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Services\ExcelService;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    const DISCOUNT_TYPE_PERCENT = 0;
    const DISCOUNT_TYPE_NONE = 2;

    protected $excelService;

    // public function __construct(ExcelService $excelService)
    public function __construct()
    {
        // $this->excelService = $excelService;
        $this->middleware('auth');
    }

    // 一覧用
    public function index(Request $request)
    {
        $this->data = [];

        $this->data['main_title'] = "納品書管理";
        $this->data['title_text'] = "帳票管理";
        $this->data['title'] = "抹茶請求書";
        $deliveries = Delivery::with('user')->get();
        // var_export($deliveries[0]->USER['NAME']);
        // var_export($deliveries[0]->UPDATEUSER['NAME']);
        // die;
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



        $action = config('constants.ActionCode');
        $name = Auth::user()->NAME;

        $query = Delivery::query();

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

        $condition = [];
        $paginator = Delivery::where($condition)
        ->orderBy('INSERT_DATE')
        ->paginate(20);
        $list = $paginator->items();

        $searchData = $request ? $request: "";
        $searchStatus = $request->STATUS;

        // $this->data['action'] = $action;
        // $this->data['name'] = $name;

        $this->data['controller_name'] = 'Delivery';
        $this->data['searchStatus'] = $searchStatus;
        $this->data['searchData'] = $searchData;
        $this->data['paginator'] = $paginator;
        $this->data['list'] = $list;
        $this->data['mailstatus'] = config('constants.MailStatusCode');
        $this->data['status'] = config('constants.IssuedStatCode');

        return view('delivery.index', $this->data);
    }

    // 登録用
    public function add(Request $request)
    {
        $this->data = [];
        $this->data['main_title'] = "納品書登録";
        $this->data['title_text'] = "帳票管理";
        $this->data['title'] = "抹茶請求書";

        if ($request->has('cancel_x')) {
            return redirect()->route('delivery.index');
        }

        $company_ID = 1;
        $error = config('constants.ItemErrorCode');
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

                return redirect()->route('delivery.check', ['id' => $result]);
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
                $serial = new Serial();
                $this->data['Delivery']['NO'] = $serial->get_number('Delivery');
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
                // Check if $default_dec is not null and has at least one element
                if (!empty($default_dec) && isset($default_dec[0]['Company']['DECIMAL_QUANTITY'])) {
                    $this->data['Delivery']['DECIMAL_QUANTITY'] = $default_dec[0]['Company']['DECIMAL_QUANTITY'];
                } else {
                    // Handle the case where DECIMAL_QUANTITY is not set
                    $this->data['Delivery']['DECIMAL_QUANTITY'] = 0; // or any default value
                }

                if (!empty($default_dec) && isset($default_dec[0]['Company']['DECIMAL_UNITPRICE'])) {
                    $this->data['Delivery']['DECIMAL_UNITPRICE'] = $default_dec[0]['Company']['DECIMAL_UNITPRICE'];
                } else {
                    // Handle the case where DECIMAL_UNITPRICE is not set
                    $this->data['Delivery']['DECIMAL_UNITPRICE'] = 0; // or any default value you prefer
                }
            } else {
                $this->data['Delivery']['DECIMAL_QUANTITY'] = 0;
                $this->data['Delivery']['DECIMAL_UNITPRICE'] = 0;
            }

            $this->data['Delivery']['DISCOUNT_TYPE'] = 2;
            $this->data['Delivery']['DATE'] = Carbon::now()->format('Y-m-d');
            $company = new Company;
            $this->data['Delivery']['CMP_SEAL_FLG'] = $company->getSealFlg();
            $this->data['Delivery']['CHR_SEAL_FLG'] = 0;

            $default_honor = $delivery->get_honor($company_ID);
            if ($default_honor) {
                if (!empty($default_honor) && isset($default_honor[0]['Company']['HONOR_CODE'])) {
                    $this->data['Delivery']['HONOR_CODE'] = $default_honor[0]['Company']['HONOR_CODE'];

                    if ($default_honor[0]['Company']['HONOR_CODE'] == 2) {
                        $this->data['Delivery']['HONOR_TITLE'] = $default_honor[0]['Company']['HONOR_TITLE'] ?? 'default_value'; // Set a default value if HONOR_TITLE is not set
                    }
                } else {
                    // Handle the case where default_honor is empty or HONOR_CODE is not set
                    $this->data['Delivery']['HONOR_CODE'] = 'default_value'; // or any default value you prefer
                    $this->data['Delivery']['HONOR_TITLE'] = 'default_value'; // or any default value you prefer
                }
            }

            // Set tax operation date
            $taxOperationDate = config('constants.TaxOperationDate');

            if (is_array($taxOperationDate) || is_object($taxOperationDate))
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
            ? ['CMP_ID' => $company_ID, 'USR_ID' => $this->getUserId()]
            : ['CMP_ID' => $company_ID];

        $items = Item::where('USR_ID', $this->getUserId())->get();
        $itemList = [];
        foreach ($items as $item) {
            $itemList[$item->ITM_ID] = [
                'ITEM' => $item->ITEM,
                'UNIT' => $item->UNIT,
                'UNIT_PRICE' => $item->UNIT_PRICE
            ];
        }

        $user = Auth::user();
        $action = config('constants.ActionCode');


        $name = $user['NAME'];

        // Set data for the view
        $this->data['controller_name'] = 'Delivery';
        $this->data['excises'] = config('constants.ExciseCode');
        $this->data['fractions'] = config('constants.FractionCode');
        $this->data['tax_fraction_timing'] = config('constants.TaxFractionTimingCode');
        $this->data['discount'] = config('constants.DiscountCode');
        $this->data['decimal'] = config('constants.DecimalCode');
        $this->data['status'] = config('constants.IssuedStatCode');
        $this->data['companys'] = $delivery->get_customer($company_ID, $cst_condition);
        $this->data['error'] = $error;
        $this->data['dataline'] = $count;
        $this->data['name'] = $name;
        $this->data['user'] = $user;
        $this->data['item'] = $items->pluck('ITEM', 'ITM_ID')->prepend('＋アイテム選択＋', 'default')->prepend('＋アイテム追加＋', 'item');
        $this->data['itemlist'] = !empty($itemList) ? json_encode($itemList) : false;
        $this->data['honor'] = config('constants.HonorCode');
        $this->data['hidden'] = $delivery->get_payment($company_ID);
        $this->data['collapse_other'] = $collaspe['other'];
        $this->data['collapse_management'] = $collaspe['management'];
        $this->data['lineAttribute'] = config('constants.LineAttribute');
        $this->data['taxClass'] = config('constants.TaxClass');
        $this->data['taxRates'] = config('constants.TaxRates');
        $this->data['taxOperationDate'] = config('constants.TaxOpera  tionDate');
        $this->data['seal_flg'] = config('constants.SealFlg');

        return view('delivery.add', $this->data);
    }

    public function check(Request $request, $id)
    {
        // Set the main title and title text
        $main_title = "納品書確認";
        $title_text = "帳票管理";
        $title = "抹茶請求書";

        // IDの取得
        if (!$delivery_ID) {
            // エラー処理
            Session::flash('error', '指定の納品書が存在しません');
            return redirect()->route('delivery.index');
        }

        // 初期データの取得
        $delivery = Delivery::edit_select($delivery_ID);

        // 顧客に紐付けられた自社担当者を取得
        $charge_name = Charge::get_charge($delivery->CHR_ID);
        $delivery->Charge->NAME = $charge_name;

        $customer_charge = CustomerCharge::where('CHRC_ID', $delivery->CHRC_ID)->first();
        if ($customer_charge) {
            $delivery->CustomerCharge = $customer_charge;
        }

        // データが取得できない場合
        if (!$delivery) {
            Session::flash('error', '指定の納品書が削除されたか、存在しない可能性があります');
            return redirect()->route('delivery.index');
        }

        if (!$this->getCheckAuthority($delivery->USR_ID)) {
            Session::flash('error', '帳票を閲覧する権限がありません');
            return redirect()->route('delivery.index');
        }

        // バージョン2.3.0追加 、割引の変換
        $delivery = $this->getCompatibleItems($delivery);
        $count = $delivery->count;

        $editauth = $this->Get_Edit_Authority($delivery->USR_ID);

        // Pass data to the view
        return view('delivery.check', [
            'main_title' => $main_title,
            'title_text' => $title_text,
            'title' => $title,
            'controller_name' => "Delivery",
            'decimals' => config('constants.DecimalCode'),
            'excises' => config('constants.ExciseCode'),
            'fractions' => config('constants.FractionCode'),
            'tax_fraction_timing' => config('constants.TaxFractionTimingCode'),
            'status' => config('constants.IssuedStatCode'),
            'editauth' => $editauth,
            'param' => $delivery,
            'honor' => config('constants.HonorCode'),
            'dataline' => $count,
            'seal_flg' => config('constants.SealFlg'),
        ]);
    }
    public function edit(Request $request, $id = null)
    {
        // Set the main title and title text
        $main_title = "納品書編集";
        $title_text = "帳票管理";
        $title = "抹茶請求書";
        $controller_name = "Delivery";

        // Handle cancel action
        if ($request->has('cancel_x')) {
            return redirect()->route('delivery.index');
        }

        // テスト用データ
        $company_ID = 1;

        $error = config('constants.ItemErrorCode');
        $count = 1;

        if (!$request->isMethod('post')) {
            // 折りたたみ設定
            $collapse = [
                'management' => 1,
                'other' => 1
            ];

            // IDの取得
            if ($delivery_ID) {
                $delivery = Delivery::edit_select($delivery_ID, $count);
            } else {
                // エラー処理
                Session::flash('error', '指定の納品書が存在しません');
                return redirect()->route('delivery.check');
            }

            // 初期データの取得
            $data = $this->getCompatibleItems($delivery);
            $count = $data['count'];

            // データが取得できない場合
            if (!$data) {
                Session::flash('error', '指定の納品書が削除されたか、存在しない可能性があります');
                return redirect()->route('delivery.index');
            }

            $customer_charge = CustomerCharge::where('CHRC_ID', $data['Delivery']['CHRC_ID'])->first();
            if ($customer_charge) {
                $data['Delivery']['CUSTOMER_CHARGE_NAME'] = $customer_charge->CHARGE_NAME;
                $data['Delivery']['CUSTOMER_CHARGE_UNIT'] = $customer_charge->UNIT;
            }
            $data['Delivery']['item'] = 'default';

            if (!$this->Get_Edit_Authority($data['Delivery']['USR_ID'])) {
                Session::flash('error', '帳票を編集する権限がありません');
                return redirect()->route('delivery.index');
            }

        } else {
            // トークンチェック
            $this->isCorrectToken($request->input('Security.token'));

            if ($request->has('del_x')) {
                Delivery::where('MDV_ID', $request->input('Delivery.MDV_ID'))->delete();
                Session::flash('success', '削除しました。');
                return redirect()->route('delivery.index');
            }

            $user = Auth::user();

            // バリデーション
            $error = $this->itemValidation($request->all(), 'Deliveryitem');

            // 割引のバリデーション
            $error['DISCOUNT'] = $this->validateDiscount($request->all());

            if ($request->input('Delivery.DISCOUNT_TYPE') != DISCOUNT_TYPE_PERCENT) {
                $discount = mb_strlen($request->input('Delivery.DISCOUNT'));
                if ($discount > 2) {
                    $error['DISCOUNT'] = 1;
                }
                if ($request->input('Delivery.DISCOUNT') == '100') {
                    $error['DISCOUNT'] = 0;
                }
                if (!preg_match("/^[0-9]+$/", $request->input('Delivery.DISCOUNT')) && $request->input('Delivery.DISCOUNT') != NULL) {
                    $error['DISCOUNT'] = 2;
                }
            }

            if ($request->input('Delivery.HONOR_CODE') != 2) {
                $request->merge(['Delivery.HONOR_TITLE' => ""]);
            }

            if ($MDV_ID = $this->setData($request->all(), 'update', $error)) {
                // アクションログ
                $this->reportAction($user->id, 9, $request->input('Delivery.MDV_ID'));
                // 成功
                Session::flash('success', '納品書を保存しました');
                return redirect()->route('delivery.check', ['delivery_ID' => $MDV_ID]);
            } else {
                // 失敗
                $count = count($request->all()) - 2 > 1 ? count($request->all()) - 2 : 1;

                // その他情報に何も入力されていなければ非表示
                if (empty($data['Delivery']['DELIVERY'])) {
                    $collapse['other'] = 1;
                } else {
                    $collapse['other'] = 0;
                }

                // 管理情報に何も入力されていなければ非表示
                if (empty($data['Delivery']['MEMO'])) {
                    $collapse['management'] = 1;
                } else {
                    $collapse['management'] = 0;
                }
            }
        }

        // 企業情報の取得
        if ($this->getUserAuthority() == 1) {
            $cst_condition = [
                'CMP_ID' => $company_ID,
                'USR_ID' => $this->getUserID()
            ];
            $items = Item::where('USR_ID', $this->getUserID())->get();
        } else {
            $cst_condition = [
                'CMP_ID' => $company_ID
            ];
            $items = Item::all();
        }

        $hidden = $this->getPayment($company_ID);
        if ($default_cmp = $this->getCompanyPayment($company_ID)) {
            $hidden['default']['EXCISE'] = $default_cmp->EXCISE;
            $hidden['default']['FRACTION'] = $default_cmp->FRACTION;
            $hidden['default']['TAX_FRACTION'] = $default_cmp->TAX_FRACTION;
            $hidden['default']['TAX_FRACTION_TIMING'] = $default_cmp->TAX_FRACTION_TIMING;
        } else {
            $hidden['default']['EXCISE'] = 1;
            $hidden['default']['FRACTION'] = 1;
            $hidden['default']['TAX_FRACTION'] = 1;
            $hidden['default']['TAX_FRACTION_TIMING'] = 0;
        }

        if (!$request->has(['Delivery.DECIMAL_QUANTITY', 'Delivery.DECIMAL_UNITPRICE'])) {
            if ($default_dec = $this->getDecimal($company_ID)) {
                $request->merge([
                    'Delivery.DECIMAL_QUANTITY' => $default_dec[0]['Company']['DECIMAL_QUANTITY'],
                    'Delivery.DECIMAL_UNITPRICE' => $default_dec[0]['Company']['DECIMAL_UNITPRICE']
                ]);
            } else {
                $request->merge([
                    'Delivery.DECIMAL_QUANTITY' => 0,
                    'Delivery.DECIMAL_UNITPRICE' => 0
                ]);
            }
        }

        $items_array = ['item' => '＋アイテム追加＋', 'default' => '＋アイテム選択＋'];
        $itemlist = [];

        if ($items) {
            foreach ($items as $item) {
                $items_array[$item->ITM_ID] = $item->ITEM;
                $itemlist[$item->ITM_ID] = [
                    'ITEM' => $item->ITEM,
                    'UNIT' => $item->UNIT,
                    'UNIT_PRICE' => $item->UNIT_PRICE
                ];
            }
        }

        if (isset($data['Customer']['NAME'])) {
            $data['Delivery']['CUSTOMER_NAME'] = $data['Customer']['NAME'];
            $data['Delivery']['CHARGE_NAME'] = Charge::get_charge($data['Delivery']['CHR_ID']);
        }

        // Pass data to the view
        return view('delivery.edit', [
            'main_title' => $main_title,
            'title_text' => $title_text,
            'title' => $title,
            'controller_name' => 'Delivery',
            'status' => config('constants.IssuedStatCode'),
            'excises' => config('constants.ExciseCode'),
            'fractions' => config('constants.FractionCode'),
            'tax_fraction_timing' => config('constants.TaxFractionTimingCode'),
            'discount' => config('constants.DiscountCode'),
            'decimal' => config('constants.DecimalCode'),
            'itemlist' => json_encode($itemlist),
            'error' => $error,
            'dataline' => $count,
            'item' => $items_array,
            'hidden' => $hidden,
            'honor' => config('constants.HonorCode'),
            'collapse_other' => $collapse['other'],
            'collapse_management' => $collapse['management'],
            'lineAttribute' => config('constants.LineAttribute'),
            'taxClass' => config('constants.TaxClass'),
            'taxRates' => config('constants.TaxRates'),
            'taxOperationDate' => config('constants.TaxOperationDate'),
            'seal_flg' => config('constants.SealFlg'),
        ]);
    }

    public function action(Request $request)
    {
        $customer_id = $request->input('Customer.id', null);
        $form_check = $request->has('Action.type');

        // Token validation (assuming this is handled in middleware or custom logic)
        // $this->validateToken($request->input('Security.token'));

        $user_id = Auth::id();

        if ($request->has('delete_x')) {
            $selectedDeliveries = $request->input('data.Delivery', []);
            if (empty($selectedDeliveries)) {
                return redirect()->route('delivery.index', ['customer' => $customer_id])
                    ->with('error', '納品書が選択されていません');
            }

            foreach ($selectedDeliveries as $key => $val) {
                if ($val == 1) {
                    $delivery = Delivery::where('MDV_ID', $key)->first();
                    if ($delivery && !$this->hasEditAuthority($delivery->USR_ID)) {
                        return redirect()->route('delivery.index', ['customer' => $customer_id])
                            ->with('error', '削除できない請求書が含まれていました');
                    }
                }
            }

            if ($this->deleteDeliveries($selectedDeliveries)) {
                // Log the action
                foreach ($selectedDeliveries as $key => $value) {
                    if ($value == 1) {
                        History::create([
                            'USR_ID' => $user_id,
                            'action_type' => 10, // Assuming 10 represents the delete action
                            'reference_id' => $key,
                        ]);
                    }
                }
                return redirect()->route('delivery.index', ['customer' => $customer_id])
                    ->with('success', '納品書を削除しました');
            }

            return redirect()->route('delivery.index', ['customer' => $customer_id]);
        }

        if ($request->has('reproduce_quote_x')) {
            $result = $this->reproduceCheck($request->input('data'));
            if ($result && $this->insertReproduceIntoQuote($result, $user_id)) {
                return redirect()->route('quotes.index', ['customer' => $customer_id])
                    ->with('success', '見積書に転記しました');
            }
            return redirect()->route('delivery.index', ['customer' => $customer_id]);
        }

        if ($request->has('reproduce_bill_x')) {
            $result = $this->reproduceCheck($request->input('data'));
            if ($result && $this->insertReproduceIntoBill($result, $user_id)) {
                return redirect()->route('bills.index', ['customer' => $customer_id])
                    ->with('success', '請求書に転記しました');
            }
            return redirect()->route('delivery.index', ['customer' => $customer_id]);
        }

        if ($request->has('reproduce_delivery_x')) {
            $result = $this->reproduceCheck($request->input('data'));
            if ($result && $inv_id = $this->insertReproduceIntoDelivery($result, $user_id)) {
                $redirectUrl = $form_check ? route('delivery.edit', ['id' => $inv_id]) : route('delivery.index', ['customer' => $customer_id]);
                return redirect($redirectUrl)
                    ->with('success', '納品書に転記しました');
            }
            return redirect()->route('delivery.index', ['customer' => $customer_id]);
        }

        if ($request->has('status_change_x')) {
            return $this->statusChange($request->input('data.Delivery'), route('delivery.index', ['customer' => $customer_id]));
        }
    }

    public function export(Request $request)
    {
        if ($request->has('download_x') && $request->has('data.Delivery')) {
            $selectedDeliveries = $request->input('data.Delivery');
            $error = "";

            $data = $this->exportDeliveries($selectedDeliveries, $error);

            if ($data) {
                $filename = '納品書.xlsx';
                $export = new DeliveriesExport($data);

                // Browser check for compatibility
                $browser = $request->server('HTTP_USER_AGENT');
                if (preg_match("/MSIE/", $browser) || preg_match('/Trident\/[0-9]\.[0-9]/', $browser)) {
                    // MSIE or Trident (Edge) specific handling if needed
                    return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX);
                } else {
                    return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX);
                }
            } else {
                return redirect()->route('delivery.export')->with('error', $error);
            }
        }

        $main_title = "納品書Excel出力";
        $title_text = "帳票管理";
        $title = "抹茶請求書";
        $controller_name = "Delivery";

        return view('delivery.export', compact('main_title', 'title_text', 'title', 'controller_name'));
    }

    public function pdf(Request $request, $id)
    {
        if (!$delivery_ID) {
            abort(404);
        }

        $items = 0;
        $discounts = 0;

        // Fetch delivery data
        $param = $this->getPreviewData($delivery_ID, $items, $discounts);

        if (!$param) {
            abort(404);
        }

        if (!$this->hasAuthority($param['Delivery']['USR_ID'])) {
            return redirect('/deliveries/')->with('error', 'You do not have permission to view this document.');
        }

        // Load company and customer charge information
        $param['CustomerCharge'] = CustomerCharge::find($param['Delivery']['CHRC_ID']);

        // Determine PDF orientation
        $direction = $param['Company']['DIRECTION'];
        $pages = $this->calculatePages($param, $direction);

        // Load the PDF view
        $pdf = Pdf::loadView('pdf.delivery', [
            'param' => $param,
            'direction' => $direction,
            'items' => $items,
            'pages' => $pages,
            'county' => config('constants.PrefectureCode'), // Update with your actual config
        ]);

        // Handle download or view
        $fileName = "納品書_{$param['Delivery']['SUBJECT']}.pdf";

        if ($request->has('download_with_coverpage')) {
            $pdf->loadView('pdf.coverpage', ['param' => $param, 'county' => config('constants.PrefectureCode')]);
            $fileName = "送付状_{$param['Delivery']['SUBJECT']}.pdf";
        }

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    private function getUserAuthority()
    {
        if (auth()->check()) {
            return auth()->user()->authority;
        }
        return null;
    }

    private function getUserId()
    {
        if (auth()->check()) {
            return auth()->user()->id;
        }
        return null;
    }
    private function getCheckAuthority($user_id)
    {
        // Implement the method to check user authority
        // For example:
        return Auth::user()->id == $user_id || Auth::user()->AUTHORITY == 1;
    }

    private function Get_Edit_Authority($user_id)
    {
        // Implement the method to get edit authority
        // For example:
        return Auth::user()->id == $user_id || Auth::user()->AUTHORITY == 1;
    }

    private function getCompatibleItems($delivery)
    {
        // Implement your discount conversion logic here
        // For now, we'll just return the delivery object as is
        return $delivery;
    }


}

