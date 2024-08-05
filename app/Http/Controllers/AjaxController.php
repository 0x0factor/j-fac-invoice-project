<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Post;
use App\Models\Item;
use App\Models\User;
use App\Models\Charge;
use App\Models\CustomerCharge;
use App\Models\Quote;
use App\Models\Bill;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;


class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['candidacy', 'search']);
    }

    // ユーザーIDが使用可能かどうかを検索
    public function searchId(Request $request)
    {
        $usercode = $request->input('usercode', 0);
        $logincode = $request->input('logincode');

        if ($logincode) {
            $data = User::searchUserID($logincode, $usercode);
        }

        if ($data == 1) {
            return response()->json(['message' => `<span class='allow'>そのIDは使用可能です。</span>`, 'status' => 'allow']);
        } elseif ($data == 0) {
            return response()->json(['message' => `<span class='must'>そのIDは既に使用されています。</span>`, 'status' => 'must']);
        } elseif ($data == 2) {
            return response()->json(['message' => `<span class='must'>文字数が足りません。</span>`, 'status' => 'must']);
        } elseif ($data == 3) {
            return response()->json(['message' => `<span class='must'>文字数が長すぎます。</span>`, 'status' => 'must']);
        } elseif ($data == 4) {
            return response()->json(['message' => `<span class='must'>使用できない文字が含まれています。</span>`, 'status' => 'must']);
        }
    }

    public function search(Request $request)
    {
        $postcode = $request->input('postcode');
        if ($postcode) {
            $data = Post::searchPostCord($postcode);
        }
        return response()->json($data['Post']);
    }

    public function candidacy(Request $request)
    {
        parse_str($request->input('params'), $param);
        $data = Post::candidacyPostCord($param);
        return view('candidacy', ['data' => $data]);
    }

    public function popup(Request $request)
    {
        $type = $request->input('type');
        $params = $request->input('params', []);
        $page = $params['page'] ?? 0;
        $number = 10;
        $nowpage = $page + 1;
        $paging = []; // Initialize paging variable

        switch ($type) {
            case 'customer':
                $countys = config('constants.PrefectureCode');
                return view('customer', compact('countys'));

            case 'add_customer_charge':
                $cst_id = $params['no'];
                $countys = config('constants.PrefectureCode');
                return view('add_customer_charge', compact('cst_id', 'countys'));

            case 'item':
                $company = Company::first();
                $TaxClass = $company->EXCISE + 1;
                return view('item', compact('TaxClass'));

            case 'select_item':
                $item_condition = $this->getItemCondition();
                $item_order = $this->getItemOrder();
                $count = Item::where($item_condition)->count();
                $items = Item::where($item_condition)
                    ->orderByRaw($item_order)
                    ->skip($page * $number)
                    ->take($number)
                    ->get();
                // Add your paging logic here
                $excises = config('constants.excise_code');
                $taxOperationDate = config('constants.tax_operation_date');
                return view('select_item', compact('items', 'excises', 'taxOperationDate', 'nowpage', 'paging'));

            case 'to':
                $chr_condition = $this->getToCondition();
                $count = $this->getCount($chr_condition);
                $charge = $this->getCharges($chr_condition, $page, $number);
                return view('to', compact('charge', 'nowpage', 'paging'));

            case 'from':
                $chr_condition = $this->getFromCondition();
                $count = Charge::where($chr_condition)->count();
                $charges = Charge::where($chr_condition)
                    ->skip($page * $number)
                    ->take($number)
                    ->get();
                return view('from', compact('charges', 'nowpage', 'paging'));

            case 'charge':
                $chr_condition = $this->getChargeCondition();
                $chr_order = $this->getChargeOrder();
                $count = Charge::where($chr_condition)->count();
                $charges = Charge::where($chr_condition)
                    ->orderByRaw($chr_order)
                    ->skip($page * $number)
                    ->take($number)
                    ->get();
                return view('charge', compact('charges', 'nowpage', 'paging'));

            case 'select_customer':
                $cst_condition = $this->getCustomerCondition();
                $cst_order = $this->getCustomerOrder();
                $count = Customer::where($cst_condition)->count();
                $customers = Customer::where($cst_condition)
                    ->orderByRaw($cst_order)
                    ->skip($page * $number)
                    ->take($number)
                    ->get();
                return view('select_customer', compact('customers', 'nowpage', 'paging'));

            case 'customer_charge':
                $cst_condition = $this->getCustomerChargeCondition();
                $cst_order = $this->getCustomerChargeOrder();
                $count = CustomerCharge::where($cst_condition)->count();
                $customerCharges = CustomerCharge::where($cst_condition)
                    ->orderByRaw($cst_order)
                    ->skip($page * $number)
                    ->take($number)
                    ->get();
                return view('customercharge', compact('customerCharges', 'nowpage', 'paging'));

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }

    public function popupInsert(Request $request)
    {
        $phoneError = 0;
        $userId = auth()->id();
        $params = $request->input('params');

        // Token check (Assuming a token validation method is available)
        if (isset($params['data']['type']) && $params['data']['type']) {
            $this->isCorrectToken($params['data']['Security']['token']);
        }

        switch ($params['data']['type']) {
            case 'customer':
                $data = $params['data'];
                $data['USR_ID'] = $userId;
                $data = array_merge_recursive($data, Customer::getPayment(1));

                // Phone validation
                $phoneError = $this->phoneValidation($data);
                $json = Customer::setData($data, 1, 'new', $phoneError, 0);
                return response()->json($json);

            case 'item':
                $data = $params['data'];
                $data['USR_ID'] = $userId;
                $json = Item::setData($data);
                return response()->json($json);

            case 'customer_charge':
                $data = $params['data'];
                $data['USR_ID'] = $userId;
                $cstId = $data['CST_ID'];

                // Phone validation
                $phoneError = $this->phoneValidation($data, 'CustomerCharge');
                $json = CustomerCharge::setData($data, 'new', $phoneError, 0, $cstId);
                $json['pe'] = $phoneError;
                return response()->json($json);

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
    }


    public function charge($id)
    {
        $charge = Charge::select('MAIL', 'UNIT', 'CHARGE_NAME')
                        ->where('CHR_ID', $id)
                        ->first();

        if (!$charge) {
            return response()->json(null);
        }

        return response()->json($charge);
    }

    public function customerCharge($id)
    {
        $cCharge = CustomerCharge::select('MAIL', 'UNIT', 'CHARGE_NAME')
                                ->where('CHRC_ID', $id)
                                ->first();

        if (!$cCharge) {
            return response()->json(null);
        }

        return response()->json($cCharge);
    }

    public function excel(Request $request)
    {
        $param = $request->input('params');

        $date1 = $param['year1'] . "-" . $param['month1'] . "-" . $param['day1'];
        $date2 = $param['year2'] . "-" . $param['month2'] . "-" . $param['day2'];

        $_modelName = "Quote";

        $userId = Auth::id();

        $count = 0;

        if (Carbon::createFromFormat('Y-m-d', $date1) && Carbon::createFromFormat('Y-m-d', $date2)) {
            $count = Quote::whereBetween('ISSUE_DATE', [$date1, $date2])->count();
        }

        return response()->json(['count' => $count . "件"]);
    }

}
