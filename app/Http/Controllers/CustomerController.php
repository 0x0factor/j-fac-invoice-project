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
        $title = "抹茶請求書";

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

            return redirect()->route('customer.index');
        }

        $condition = [];
        $paginator = Customer::where($condition)
        ->orderBy('INSERT_DATE')
        ->paginate(15);
        $list = $paginator->items();
        $charge = $this->customer->select_charge($company_ID, $condition);
        $countys = config('constants.PrefectureCode');

        return view('customer.index', compact('main_title', 'title_text', 'title', 'charge', 'paginator', 'list', 'countys'));
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
        $paginator = Customer::where($condition)
        ->orderBy('INSERT_DATE')
        ->paginate(15);
        $list = $paginator->items();
        $charge = $this->customer->select_charge($company_ID, $condition);
        $countys = config('constants.PrefectureCode');

        return view('customer.select', compact('main_title', 'title_text', 'title', 'inv_num', 'charge', 'paginator', 'list', 'countys'));
    }

    public function check($customer_ID)
    {
        $main_title = "取引先確認";
        $title_text = "顧客管理";
        $title = "抹茶請求書";

        $company_ID = 1;

        $customer = $this->customer->find($customer_ID);

        if (!$customer) {
            return redirect()->route('customer.index')->with('error', '顧客が見つかりません');
        }

        // Handle edit and check authorities
        // Example: Check authority logic

        $editauth = $this->Get_Edit_Authority($customer->USR_ID);

        if (! $this->Get_Check_Authority($customer->USR_ID)) {
            return redirect()->route('customer.index')->with('error', 'ページを開く権限がありません');
        }

        $charge = $this->customer->get_charge($customer->CHR_ID);
        $countys = config('constants.PrefectureCode');
        $excises = config('constants.ExciseCode');
        $fractions = config('constants.FractionCode');
        $tax_fraction_timing = config('constants.TaxFractionTimingCode');
        $honor = config('constants.HonorCode');

        return view('customer.check', compact('main_title', 'title_text', 'title', 'editauth', 'customer_ID', 'customer', 'charge', 'countys'));
    }

    public function add(Request $request)
    {
        $main_title = "取引先登録";
        $title_text = "顧客管理";
        $title = "抹茶請求書";

        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;

        if ($request->input('cancel_x')) {
            return redirect()->route('customer.index');
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
                session()->flash('success', '取引先を保存しました');
                return redirect()->route('customer.check', ['customer_ID' => $customer_ID])->with('success', '取引先を保存しました');
            }
        } else {
            $customer = Customer::getPayment($company_ID);
            if ($default_honor = Customer::getHonor($company_ID)) {
                $customer['Customer']['HONOR_CODE'] = $default_honor[0]['Company']['HONOR_CODE'];
                if ($default_honor[0]['Company']['HONOR_CODE'] == 2) {
                    $customer['Customer']['HONOR_TITLE'] = $default_honor[0]['Company']['HONOR_TITLE'];
                }
            }
        }

        // Prepare form fields
        $excises = [
            'type' => 'radio',
            'options' => config('constants.ExciseCode'),
            'style' => 'width:30px;'
        ];

        $fractions = [
            'type' => 'radio',
            'options' => config('constants.FractionCode'),
            'style' => 'width:30px;'
        ];

        $tax_fraction_timing = [
            'type' => 'radio',
            'options' => config('constants.TaxFractionTimingCode'),
            'style' => 'margin-right: 10px; margin-left: 8px;',
            'class' => 'txt_mid'
        ];

        $cutooffSelect = [
            'type' => 'radio',
            'options' => [
                0 => '末日',
                1 => '指定'
            ],
            'class' => 'cutooff_select',
            'style' => 'width:30px;'
        ];

        $paymentSelect = [
            'type' => 'radio',
            'options' => [
                0 => '末日',
                1 => '指定'
            ],
            'class' => 'payment_select',
            'style' => 'width:30px;'
        ];

        // Fetch additional data for the form
        // Example: Fetch data for drop-downs and checkboxes
        $payment = config('constants.PaymentMonth');
        $countys = config('constants.PrefectureCode');
        $honor = config('constants.HonorCode');

        return view('customer.add', compact('main_title', 'title_text', 'title', 'payment', 'countys', 'cutooffSelect', 'paymentSelect', 'excises', 'fractions', 'tax_fraction_timing', 'honor', 'phone_error', 'fax_error'));
    }

    public function edit(Request $request, $customer_ID)
    {
        $main_title = "取引先編集";
        $title_text = "顧客管理";
        $title = "抹茶請求書";

        if ($request->input('cancel_x')) {
            return redirect()->route('customer.index');
        }

        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;


          // Check if request has data
          if ($request->isMethod('post')) {
            // Phone number validation
            $phone_error = $this->phoneValidation($request->input('Customer'));

            // FAX number validation
            $fax_error = $this->faxValidation($request->input('Customer'));

            if ($request->input('Customer.HONOR_CODE') != 2) {
                $request->merge(['Customer.HONOR_TITLE' => '']);
            }

            // Update customer data
            $customer = Customer::find($request->input('Customer.CST_ID'));

            if ($customer) {
                $this->setData($request->input(), $company_ID, 'update', $phone_error, $fax_error);

                if (!$this->hasError()) {
                    Session::flash('success', '取引先を保存しました');
                    return Redirect::to("/customers/check/" . $request->input('Customer.CST_ID'));
                }
            } else {
                return Redirect::to("/customers");
            }
        } else {
            // Fetch customer information if not a POST request
            if ($customerId) {
                $customer = Customer::find($customerId);

                if (!$customer) {
                    return Redirect::to("/customers");
                }
            } else {
                return Redirect::to("/customers");
            }
        }

        // Check edit authority
        if (!$this->getEditAuthority($customer->USR_ID)) {
            Session::flash('error', 'ページを開く権限がありません');
            return Redirect::to("/customers/index/");
        }

        // Form options
        $excise = [
            'type' => 'radio',
            'options' => config('constants.ExciseCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        $fraction = [
            'type' => 'radio',
            'options' => config('constants.FractionCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        $tax_fraction_timing = [
            'type' => 'radio',
            'options' => config('constants.TaxFractionTimingCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 10px; margin-left: 8px;',
            'class' => 'txt_mid'
        ];

        $cutooff_select = [
            'type' => 'radio',
            'options' => [
                0 => '末日',
                1 => '指定'
            ],
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        $payment_select = [
            'type' => 'radio',
            'options' => [
                0 => '末日',
                1 => '指定'
            ],
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        $charge = $this->getCharge($customer->CHR_ID);
        $payment = config('constants.PaymentMonth');
        $countys = config('constants.PrefectureCode');
        $honor = config('constants.HonorCode');

        // Return view with data
        return view('customer.edit', compact('main_title', 'title_text', 'title', 'payment', 'countys', 'cutooff_select', 'payment_select', 'excise', 'fraction', 'tax_fraction_timing', 'honor', 'phone_error', 'fax_error'));

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
