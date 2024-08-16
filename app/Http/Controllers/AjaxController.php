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


class AjaxController extends AppController
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
        return view('ajax.candidacy', ['data' => $data]);
    }

    public function popup(Request $request)
    {
        $type = $request->input('params')['type'];
        $params = $request->input('params', []);
        $page = $params['page'] ?? 0;
        $number = 10;
        $nowpage = $page + 1;
        $paging = []; // Initialize paging variable

        switch ($type) {
            case 'customer':
                $countys = config('constants.PrefectureCode');
                return view('ajax.customer', compact('countys'));

            case 'add_customer_charge':
                $cst_id = $params['no'];
                $countys = config('constants.PrefectureCode');
                return view('ajax.add_customer_charge', compact('cst_id', 'countys'));

            case 'item':
                $company = Company::first();
                $TaxClass = $company->EXCISE + 1;
                return view('ajax.item', compact('TaxClass'));

            case 'select_item':
                $page = $request->input('params.page', 0);
                $number = 10;
                $keyword = $request->input('params.keyword');
                $sort = $request->input('params.sort', 'LAST_UPDATE');
                $desc = $request->input('params.desc', false);

                $itemCondition = [];
                if ($keyword) {
                    $qKana = mb_convert_kana($keyword, "C");
                    $itemCondition[] = ['ITEM', 'LIKE', "%$keyword%"];
                    $itemCondition[] = ['ITEM_KANA', 'LIKE', "%$qKana%"];
                    $itemCondition[] = ['ITEM_CODE', 'LIKE', "%$keyword%"];
                }

                $query = Item::where($itemCondition);
                if ($sort === 'UNIT_PRICE') {
                    $query->orderByRaw("CAST($sort AS UNSIGNED) " . ($desc ? 'DESC' : 'ASC'));
                } else {
                    $query->orderBy($sort, $desc ? 'DESC' : 'ASC');
                }

                $count = $query->count();
                $item = $query->skip($page * $number)->take($number)->get();

                $excises = config('constants.ExciseCode'); // Assuming you have this in config
                $taxOperationDate = config('constants.TaxOperationDate');
                $paginator = Item::where($itemCondition)
                    ->orderBy('INSERT_DATE')
                    ->paginate(20);

                return view('ajax.select_item', compact('item', 'excises', 'taxOperationDate', 'page', 'number', 'count', 'sort', 'desc', 'paginator'));

            case 'to':
                $page = $request->input('params.page', 0);
                $number = 10;
                $no = $request->input('params.no');
                $ctype = $request->input('params.ctype', 0);
                $userAuth = auth()->user()->authority; // Assuming you have this in the user model

                $chargeCondition = ($userAuth == 1) ? ['Charge.USR_ID' => auth()->id()] : [];
                $customerChargeCondition = ($userAuth == 1) ? ['CustomerCharge.CST_ID' => $no, 'CustomerCharge.USR_ID' => auth()->id()] : ['CustomerCharge.CST_ID' => $no];

                if ($ctype == 1) {
                    $count = Charge::where($chargeCondition)->count();
                    $charge = Charge::where($chargeCondition)
                        ->select('MAIL', 'CHARGE_NAME')
                        ->skip($page * $number)
                        ->take($number)
                        ->get();
                    $type = 1;
                } else {
                    $count = CustomerCharge::where($customerChargeCondition)->count();
                    $charge = CustomerCharge::where($customerChargeCondition)
                        ->select('MAIL', 'CHARGE_NAME')
                        ->skip($page * $number)
                        ->take($number)
                        ->get();
                    $type = 0;
                }

                $paginator = CustomerCharge::where($conditions)
                    ->orderBy('INSERT_DATE')
                    ->paginate(20);
                return view('ajax.to', compact('charge', 'no', 'page', 'number', 'count', 'type', 'paginator'));

            case 'from':
                $page = $request->input('params.page', 0);
                $number = 10;
                $userAuth = auth()->user()->authority;

                $chargeCondition = ($userAuth == 1) ? ['Charge.USR_ID' => auth()->id()] : [];
                $count = Charge::where($chargeCondition)->count();
                $charge = Charge::where($chargeCondition)
                    ->select('MAIL', 'CHARGE_NAME')
                    ->skip($page * $number)
                    ->take($number)
                    ->get();
                $paginator = Charge::where($conditions)
                    ->orderBy('INSERT_DATE')
                    ->paginate(20);

                return view('ajax.from', compact('charge', 'page', 'number', 'count', 'paginator'));
            case 'charge':
                $page = $request->input('params.page', 0);
                $number = 10;

                $conditions = auth()->user()->authority != 1
                    ? []
                    : ['Charge.USR_ID' => auth()->id()];

                if ($keyword = $request->input('params.keyword')) {
                    $qKana = mb_convert_kana($keyword, "C");
                    $conditions['or']['CHARGE_NAME'] = "%$keyword%";
                    $conditions['or']['CHARGE_NAME_KANA'] = "%$qKana%";
                    $request->session()->put('CHR_KEYWORD', $keyword);
                }

                $sort = $request->input('params.sort', 'LAST_UPDATE');
                $desc = $request->input('params.desc', false);
                $chrOrder = "LAST_UPDATE DESC";
                if ($sort) {
                    $chrOrder = "$sort " . ($desc ? "DESC" : "");
                    $request->session()->put('sort', $sort);
                    $request->session()->put('desc', $desc);
                }

                $count = Charge::where($conditions)->count();
                $charges = Charge::where($conditions)
                    ->orderByRaw($chrOrder)
                    ->limit($number)
                    ->offset($page * $number)
                    ->get();
                $paginator = Charge::where($conditions)
                    ->orderBy('INSERT_DATE')
                    ->paginate(20);

                return view('ajax.charge', [
                    'charge' => $charges,
                    'nowpage' => $page,
                    'paginator' => $paginator,
                    'paging' => $this->paginate($count, $page, $number),
                ]);
            case 'select_customer':
                $page = $request->input('params.page', 0);
                $number = 10;

                $conditions = auth()->user()->authority != 1
                    ? []
                    : ['Customer.USR_ID' => auth()->id()];

                if ($keyword = $request->input('params.keyword')) {
                    $qKana = mb_convert_kana($keyword, "C");
                    $conditions['or']['Customer.NAME'] = "%$keyword%";
                    $conditions['or']['Customer.NAME_KANA'] = "%$qKana%";
                    $request->session()->put('CST_KEYWORD', $keyword);
                }

                $sort = $request->input('params.sort', 'LAST_UPDATE');
                $desc = $request->input('params.desc', false);
                $cstOrder = "LAST_UPDATE DESC";
                if ($sort) {
                    $cstOrder = "$sort " . ($desc ? "DESC" : "");
                    if ($sort === 'CUSTOMER_NAME') {
                        $cstOrder = "Customer.NAME_KANA " . ($desc ? "DESC" : "");
                    }
                    $request->session()->put('sort', $sort);
                    $request->session()->put('desc', $desc);
                }

                $count = Customer::where($conditions)->count();
                $customers = Customer::where($conditions)
                    ->orderByRaw($cstOrder)
                    ->limit($number)
                    ->offset($page * $number)
                    ->get();

                foreach ($customers as $customer) {
                    $charge = Charge::where('CHR_ID', $customer->CHR_ID)->first();
                    if ($charge) {
                        $customer->Charge = $charge;
                    }
                }

                $paginator = Customer::where($conditions)
                            ->orderBy('INSERT_DATE')
                            ->paginate(20);

                return view('ajax.select_customer', [
                    'customer' => $customers,
                    'nowpage' => $page,
                    'paging' => $this->paginate($count, $page, $number),
                    'desc' => $desc,
                    'paginator' => $paginator,
                ]);
            case 'customer_charge':
                $page = $request->input('params.page', 0);
                $number = 10;

                $conditions = auth()->user()->authority != 1
                    ? []
                    : ['CustomerCharge.USR_ID' => auth()->id()];

                if ($cstId = $request->input('params.id')) {
                    if ($cstId !== 'default') {
                        $conditions['CustomerCharge.CST_ID'] = $cstId;
                    }
                }

                if ($keyword = $request->input('params.keyword')) {
                    $qKana = mb_convert_kana($keyword, "C");
                    $conditions['or']['CustomerCharge.CHARGE_NAME'] = "%$keyword%";
                    $conditions['or']['Customer.NAME'] = "%$keyword%";
                    $conditions['or']['CustomerCharge.CHARGE_NAME_KANA'] = "%$qKana%";
                    $conditions['or']['Customer.NAME_KANA'] = "%$qKana%";
                    $request->session()->put('CHRC_KEYWORD', $keyword);
                }

                $sort = $request->input('params.sort', 'LAST_UPDATE');
                $desc = $request->input('params.desc', false);
                $cstOrder = "LAST_UPDATE DESC";
                if ($sort) {
                    $cstOrder = "$sort " . ($desc ? "DESC" : "");
                    if ($sort === 'CUSTOMER_NAME') {
                        $cstOrder = "Customer.NAME_KANA " . ($desc ? "DESC" : "");
                    }
                    $request->session()->put('sort', $sort);
                    $request->session()->put('desc', $desc);
                }

                $count = CustomerCharge::where($conditions)->count();
                $customerCharges = CustomerCharge::where($conditions)
                    ->orderByRaw($cstOrder)
                    ->limit($number)
                    ->offset($page * $number)
                    ->get();

                $paginator = CustomerCharge::where($conditions)
                        ->orderBy('INSERT_DATE')
                        ->paginate(20);

                return view('ajax.customercharge ', [
                    'customer_charge' => $customerCharges,
                    'nowpage' => $page,
                    'paginator' => $paginator,
                    'paging' => $this->paginate($count, $page, $number),
                    'cst_id' => $cstId === 'default' ? 'undefined' : $cstId,
                ]);
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

    private function paginate($count, $page, $number)
    {
        $totalPages = ceil($count / $number);
        return [
            'total' => $count,
            'pages' => $totalPages,
            'current' => $page,
            'per_page' => $number,
        ];
    }

}
