<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->middleware('auth'); // Ensure authentication middleware is applied
    }

    public function index(Request $request)
    {
        $main_title = "取引先一覧";
        $title_text = "顧客管理";

        $company_ID = 1;

        if ($request->input('delete_x')) {
            // Handle delete action
            if (! hash_equals($request->input('_token'), $request->session()->token())) {
                abort(419); // CSRF token mismatch
            }

            $error = [];

            if ($this->customer->index_delete($request->all(), $error)) {
                session()->flash('success', '取引先を削除しました');
            } else {
                session()->flash('error', '取引先の削除に失敗しました');
            }

            return redirect()->route('customers.index');
        }

        $condition = [];
        $charge = $this->customer->select_charge($company_ID, $condition);

        return view('customer.index', compact('main_title', 'title_text', 'charge'));
    }

    public function select(Request $request)
    {
        $main_title = "顧客から絞り込み";
        $title_text = "帳票管理";
        $title = "抹茶請求書";

        $inv_num = $this->customer->getInvoiceNum();

        // Handle search and pagination logic

        $company_ID = 1;
        $condition = [];
        $charge = $this->customer->select_charge($company_ID, $condition);

        return view('customer.select', compact('main_title', 'title_text', 'title', 'inv_num', 'charge'));
    }

    public function check($customer_ID)
    {
        $main_title = "取引先確認";
        $title_text = "顧客管理";

        $company_ID = 1;

        $customer = $this->customer->find($customer_ID);

        if (!$customer) {
            return redirect()->route('customers.index')->with('error', '顧客が見つかりません');
        }

        // Handle edit and check authorities
        // Example: Check authority logic

        $editauth = $this->Get_Edit_Authority($customer->USR_ID);

        if (! $this->Get_Check_Authority($customer->USR_ID)) {
            return redirect()->route('customers.index')->with('error', 'ページを開く権限がありません');
        }

        $charge = $this->customer->get_charge($customer->CHR_ID);

        return view('customer.check', compact('main_title', 'title_text', 'editauth', 'customer_ID', 'customer', 'charge'));
    }

    public function add(Request $request)
    {
        $main_title = "取引先登録";
        $title_text = "顧客管理";

        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;

        if ($request->input('cancel_x')) {
            return redirect()->route('customers.index');
        }

        if ($request->input('submit_x')) {
            $request->validate([
                'Customer.NAME' => 'required|string|max:255',
                'Customer.PHONE' => 'required|string|max:20',
                'Customer.FAX' => 'nullable|string|max:20',
                'Customer.HONOR_CODE' => 'required|integer',
                'Customer.HONOR_TITLE' => 'nullable|string|max:50'
                // Add other validation rules as necessary
            ]);

            // Perform additional validation
            $phone_error = $this->phone_validation($request->input('Customer'));
            $fax_error = $this->fax_validation($request->input('Customer'));

            // Handle the honor code
            if ($request->input('Customer.HONOR_CODE') != 2) {
                $request->merge(['Customer.HONOR_TITLE' => '']);
            }

            // Data insertion logic
            $setdata = $this->customer->set_data($request->input(), $company_ID, 'new', $phone_error, $fax_error);

            if (!isset($setdata['error'])) {
                $customer_ID = $setdata['Customer']['CST_ID'];
                return redirect()->route('customers.check', ['customer_ID' => $customer_ID])->with('success', '取引先を保存しました');
            }
        }

        // Fetch additional data for the form
        // Example: Fetch data for drop-downs and checkboxes
        $payment = config('constants.PaymentMonth');
        $countys = config('constants.PrefectureCode');
        $excises = config('constants.ExciseCode');
        $fractions = config('constants.FractionCode');
        $tax_fraction_timing = config('constants.TaxFractionTimingCode');
        $honor = config('constants.HonorCode');

        return view('customer.add', compact('main_title', 'title_text', 'payment', 'countys', 'excises', 'fractions', 'tax_fraction_timing', 'honor', 'phone_error', 'fax_error'));
    }

    public function edit(Request $request, $customer_ID)
    {
        $main_title = "取引先編集";
        $title_text = "顧客管理";

        if ($request->input('cancel_x')) {
            return redirect()->route('customers.index');
        }

        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;

        $customer = $this->customer->find($customer_ID);

        if (!$customer) {
            return redirect()->route('customers.index')->with('error', '顧客が見つかりません');
        }

        if ($request->input('submit_x')) {
            $request->validate([
                'Customer.NAME' => 'required|string|max:255',
                'Customer.PHONE' => 'required|string|max:20',
                'Customer.FAX' => 'nullable|string|max:20',
                'Customer.HONOR_CODE' => 'required|integer',
                'Customer.HONOR_TITLE' => 'nullable|string|max:50'
                // Add other validation rules as necessary
            ]);

            // Perform additional validation
            $phone_error = $this->phone_validation($request->input('Customer'));
            $fax_error = $this->fax_validation($request->input('Customer'));

            // Handle the honor code
            if ($request->input('Customer.HONOR_CODE') != 2) {
                $request->merge(['Customer.HONOR_TITLE' => '']);
            }

            // Data update logic
            $setdata = $this->customer->set_data($request->input(), $company_ID, 'update', $phone_error, $fax_error);

            if (!isset($setdata['error'])) {
                return redirect()->route('customers.check', ['customer_ID' => $customer_ID])->with('success', '取引先を保存しました');
            }
        }

        if (!$this->Get_Edit_Authority($customer->USR_ID)) {
            return redirect()->route('customers.index')->with('error', 'ページを開く権限がありません');
        }

        // Fetch additional data for the form
        // Example: Fetch data for drop-downs and checkboxes
        $payment = config('constants.PaymentMonth');
        $countys = config('constants.PrefectureCode');
        $excises = config('constants.ExciseCode');
        $fractions = config('constants.FractionCode');
        $tax_fraction_timing = config('constants.TaxFractionTimingCode');
        $honor = config('constants.HonorCode');

        return view('customer.edit', compact('main_title', 'title_text', 'customer', 'payment', 'countys', 'excises', 'fractions', 'tax_fraction_timing', 'honor', 'phone_error', 'fax_error'));
    }

    // Custom methods for validation and authorities
    private function phone_validation($data)
    {
        // Implement phone validation logic here
        return 0; // Return error count or boolean indicating validation status
    }

    private function fax_validation($data)
    {
        // Implement fax validation logic here
        return 0; // Return error count or boolean indicating validation status
    }

    private function Get_Edit_Authority($user_id)
    {
        // Implement authority check logic here
        return true; // Return boolean indicating authority status
    }

    private function Get_Check_Authority($user_id)
    {
        // Implement authority check logic here
        return true; // Return boolean indicating authority status
    }
}
