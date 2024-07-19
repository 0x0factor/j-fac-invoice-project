<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;


class CustomersController extends Controller
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->middleware('auth'); // Example: Adding authentication middleware
    }

    public function index()
    {
        $main_title = "取引先一覧";
        $title_text = "顧客管理";

        $company_ID = 1;

        if ($request->input('delete_x')) {
            // Handle delete action
            // Use Laravel's CSRF protection for token validation
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

        return view('customers.index', compact('main_title', 'title_text', 'charge'));
    }

    public function select()
    {
        $main_title = "顧客から絞り込み";
        $title_text = "帳票管理";
        $inv_num = $this->customer->getInvoiceNum();

        // Pagination and search handling here

        $company_ID = 1;
        $condition = [];
        $charge = $this->customer->select_charge($company_ID, $condition);

        return view('customers.select', compact('main_title', 'title_text', 'inv_num', 'charge'));
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

        return view('customers.check', compact('main_title', 'title_text', 'editauth', 'customer_ID', 'charge'));
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
                // Validation rules for input fields
            ]);

            // Process form data and validation
            // Handle data insertion and redirection

            return redirect()->route('customers.check', ['customer_ID' => $customer_ID])->with('success', '取引先を保存しました');
        }

        // Fetch additional data for the form
        // Example: Fetch data for drop-downs and checkboxes

        return view('customers.add', compact('main_title', 'title_text'));
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
                // Validation rules for input fields
            ]);

            // Process form data and validation
            // Handle data update and redirection

            return redirect()->route('customers.check', ['customer_ID' => $customer_ID])->with('success', '取引先を保存しました');
        }

        // Fetch additional data for the form
        // Example: Fetch data for drop-downs and checkboxes

        return view('customers.edit', compact('main_title', 'title_text', 'customer'));
    }

    // Actions go here
}