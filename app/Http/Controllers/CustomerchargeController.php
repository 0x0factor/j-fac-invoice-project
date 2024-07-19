<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerCharge;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CustomerChargesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $main_title = "取引先担当者一覧";
        $title_text = "顧客管理";

        if (request()->has('delete_x')) {
            // トークンチェック
            $this->isCorrectToken(request()->input('Security.token'));

            // 成功
            if (CustomerCharge::destroy(request()->input('data'))) {
                Session::flash('message', '取引先担当者を削除しました');
                return redirect()->route('customer_charges.index');
            } else {
                Session::flash('message', '取引先担当者を削除できませんでした');
                return redirect()->route('customer_charges.index');
            }
        }

        $customers = Customer::all();
        $status = Config::get('statusCode');
        $countys = Config::get('prefectureCode');

        return view('customer_charges.index', compact('main_title', 'title_text', 'customers', 'status', 'countys'));
    }

    public function check($chargeID)
    {
        $main_title = "取引先担当者確認";
        $title_text = "顧客管理";

        $chargeAry = CustomerCharge::find($chargeID);

        if (!$chargeAry || !$this->Get_Check_Authority($chargeAry->USR_ID)) {
            Session::flash('message', 'ページを開く権限がありません');
            return redirect()->route('customer_charges.index');
        }

        $customer = Customer::find($chargeAry->CST_ID);
        $editauth = $this->Get_Edit_Authority($chargeAry->USR_ID);

        $status = Config::get('statusCode');
        $countys = Config::get('prefectureCode');

        return view('customer_charges.check', compact('main_title', 'title_text', 'chargeAry', 'customer', 'editauth', 'status', 'countys'));
    }

    public function add(Request $request)
    {
        $main_title = "取引先担当者登録";
        $title_text = "顧客管理";

        if ($request->has('cancel_x')) {
            return redirect()->route('customer_charges.index');
        }

        $phone_error = 0;
        $fax_error = 0;

        if ($request->isMethod('post')) {
            $this->isCorrectToken($request->input('Security.token'));

            $phone_error = $this->phone_validation($request->input('CustomerCharge'));
            $fax_error = $this->fax_validation($request->input('CustomerCharge'));

            $result = $this->set_data($request->input('CustomerCharge'), 'new', $phone_error, $fax_error);

            if (!isset($result['error'])) {
                Session::flash('message', '取引先担当者を保存しました');
                return redirect()->route('customer_charges.check', ['chargeID' => $result['CustomerCharge']['CHRC_ID']]);
            }
        }

        $user_id = Auth::id();
        $countys = Config::get('prefectureCode');
        $status = Config::get('statusCode');

        return view('customer_charges.add', compact('main_title', 'title_text', 'user_id', 'phone_error', 'fax_error', 'countys', 'status'));
    }

    public function edit(Request $request, $chargeID)
    {
        $main_title = "取引先担当者編集";
        $title_text = "顧客管理";

        if ($request->has('cancel_x')) {
            return redirect()->route('customer_charges.index');
        }

        $phone_error = 0;
        $fax_error = 0;

        if (!$request->isMethod('post')) {
            $chargeAry = CustomerCharge::find($chargeID);

            if (!$chargeAry) {
                return redirect()->route('customer_charges.index');
            }

            $customer = Customer::find($chargeAry->CST_ID);
            $this->data = $chargeAry;

        } else {
            $this->isCorrectToken($request->input('Security.token'));

            $phone_error = $this->phone_validation($request->input('CustomerCharge'));
            $fax_error = $this->fax_validation($request->input('CustomerCharge'));

            $this->set_data($request->input('CustomerCharge'), 'update', $phone_error, $fax_error);
            Session::flash('message', '取引先担当者を保存しました');
            return redirect()->route('customer_charges.check', ['chargeID' => $chargeID]);
        }

        if (!$this->Get_Edit_Authority($this->data['CustomerCharge']['USR_ID'])) {
            Session::flash('message', 'ページを開く権限がありません');
            return redirect()->route('customer_charges.index');
        }

        $customer = Customer::find($this->data['CustomerCharge']['CST_ID']);
        $status = Config::get('statusCode');
        $countys = Config::get('prefectureCode');

        return view('customer_charges.edit', compact('main_title', 'title_text', 'customer', 'phone_error', 'fax_error', 'status', 'countys'));
    }
}