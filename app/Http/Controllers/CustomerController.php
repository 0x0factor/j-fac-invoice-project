<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Main\AppController;
use Illuminate\Support\Facades\Config;

class CustomerController extends AppController
{
    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $companyId = 1;
        $controllerName = 'Customer';

        if ($request->input('delete_x')) {
            return $this->handleDelete($request);
        }

        $query = Customer::query();
        $this->applyFilters($query, $request);

        $paginator = $query->orderBy('INSERT_DATE')->paginate(20);
        $charge = $this->customer->select_charge($companyId, []);

        return view('customer.index', $this->getViewData($paginator, $charge, $request, $controllerName));
    }

    public function select(Request $request)
    {
        $companyId = 1;
        $controllerName = 'Customer';

        $invNum = $this->customer->getInvoiceNum();
        $paginator = Customer::where('NAME', 'like', '%' . $request->NAME . '%')
            ->orderBy('INSERT_DATE')
            ->paginate(20);

        $charge = $this->customer->select_charge($companyId, []);

        return view('customer.select', $this->getSelectViewData($paginator, $charge, $request, $controllerName, $invNum));
    }

    public function check($customerId)
    {
        $customer = $this->customer->find($customerId);
        
        if (!$customer) {
            return redirect()->route('customer.index')->with('error', '顧客が見つかりません');
        }
        
        if (!$this->Get_Check_Authority($customer->USR_ID)) {
            return redirect()->route('customer.index')->with('error', 'ページを開く権限がありません');
        }

        $editAuth = $this->Get_Edit_Authority($customer->USR_ID);
        $charge = Customer::getCharge($customer->CHR_ID);

        return view('customer.check', $this->getCheckViewData($customer, $editAuth, $charge, $customerId));
    }

    public function add(Request $request)
    {
        $companyId = 1;
        
        if ($request->has('cancel')) {
            return redirect()->route('customer.index');
        }
        if ($request->has('submit')) {   
            return $this->handleAddSubmit($request, $companyId);
        }
        
        $customer = $this->initializeCustomer($companyId);

        return view('customer.add', $this->getAddViewData($customer));
    }

    public function edit(Request $request, $customerId)
    {
        $companyId = 1;

        if ($request->input('cancel_x')) {
            return redirect()->route('customer.index');
        }

        $customer = Customer::find($customerId);

        if (!$customer) {
            return redirect()->route('customer.index');
        }

        if (!$this->Get_Edit_Authority($customer->USR_ID)) {
            Session::flash('error', 'ページを開く権限がありません');
            return redirect()->route('customer.index');
        }

        if ($request->isMethod('post')) {
            return $this->handleEditSubmit($request, $companyId);
        }

        return view('customer.edit', $this->getEditViewData($customer));
    }

    // Helper methods...

    public function store(Request $request)
    {
        $companyId = 1;

        $validatedData = $request->validate([
            'NAME' => 'required|max:60',
            'PHONE_NO1' => 'required|numeric|digits_between:2,5',
            'PHONE_NO2' => 'required|numeric|digits:4',
            'PHONE_NO3' => 'required|numeric|digits:4',
            // Add other validation rules as needed
        ]);

        $customer = new Customer();
        $customer->fill($validatedData);
        $customer->COMPANY_ID = $companyId;

        if ($customer->save()) {
            Session::flash('success', '取引先を保存しました');
            return redirect()->route('customer.check', ['customer_ID' => $customer->CST_ID]);
        } else {
            return back()->withInput()->withErrors(['error' => '取引先の保存に失敗しました']);
        }
    }

    private function handleDelete(Request $request)
    {
        if (!hash_equals($request->input('_token'), $request->session()->token())) {
            abort(419);
        }

        $error = [];

        if ($this->customer->index_delete($request->all(), $error)) {
            Session::flash('success', '取引先を削除しました');
        } else {
            Session::flash('error', '取引先の削除に失敗しました');
        }

        return redirect()->route('customer.index');
    }

    private function applyFilters($query, $request)
    {
        if ($request->NAME) {
            $query->where('NAME', 'like', '%' . $request->NAME . '%');
        }

        if ($request->ADDRESS) {
            $query->where('ADDRESS', 'like', '%' . $request->ADDRESS . '%');
        }
    }

    private function getViewData($paginator, $charge, $request, $controllerName)
    {
        return [
            'main_title' => '取引先一覧',
            'title_text' => '顧客管理',
            'title' => '抹茶請求書',
            'charge' => $charge,
            'paginator' => $paginator,
            'list' => $paginator->items(),
            'countys' => Config::get('constants.PrefectureCode'),
            'search_name' => $request,
            'controller_name' => $controllerName
        ];
    }

    private function getSelectViewData($paginator, $charge, $request, $controllerName, $invNum)
    {
        return [
            'main_title' => '顧客から絞り込み',
            'title_text' => '帳票管理',
            'title' => '抹茶請求書',
            'inv_num' => $invNum,
            'charge' => $charge,
            'paginator' => $paginator,
            'list' => $paginator->items(),
            'countys' => Config::get('constants.PrefectureCode'),
            'search_name' => $request->NAME,
            'controller_name' => $controllerName
        ];
    }

    private function getCheckViewData($customer, $editAuth, $charge, $customerId)
    {
        return [
            'main_title' => '取引先確認',
            'title_text' => '顧客管理',
            'title' => '抹茶請求書',
            'controller_name' => 'Customer',
            'editauth' => $editAuth,
            'customer_ID' => $customerId,
            'customer' => $customer,
            'charge' => $charge,
            'countys' => Config::get('constants.PrefectureCode'),
            'honor' => Config::get('constants.HonorCode'),
            'cutooff_select' => $this->getCutoffSelectOptions(),
            'payment_select' => $this->getPaymentSelectOptions(),
            'excises' => $this->getExciseOptions(),
            'fractions' => $this->getFractionOptions(),
            'tax_fraction_timing' => $this->getTaxFractionTimingOptions()
        ];
    }

    private function handleAddSubmit(Request $request, $companyId)
    {
        $request->validate([
            'NAME' => 'required|string|max:255',
            'NAME_KANA' => 'required|string|max:255',
            'HONOR_CODE' => 'required|integer',
            'HONOR_TITLE' => 'nullable|string|max:50',
            'POSTCODE1' => 'required|string|size:3',
            'POSTCODE2' => 'required|string|size:3',
            'CNT_ID' => 'required|integer',
            'ADDRESS' => 'required|string|max:255',
            'BUILDING' => 'nullable|string|max:255',
            'PHONE_NO1' => 'nullable|string|max:5',
            'PHONE_NO2' => 'nullable|string|max:4',
            'PHONE_NO3' => 'nullable|string|max:4',
            'FAX_NO1' => 'nullable|string|max:5',
            'FAX_NO2' => 'nullable|string|max:4', 
            'FAX_NO3' => 'nullable|string|max:4',
            'WEBSITE' => 'nullable|url|max:255',
            'CHR_NAME' => 'nullable|string|max:255',
            'CHR_ID' => 'nullable|integer',
            'CUTOOFF_SELECT' => 'required|integer',
            'CUTOOFF_DATE' => 'nullable|integer|min:1|max:31',
            'PAYMENT_MONTH' => 'required|integer',
            'PAYMENT_SELECT' => 'required|integer',
            'PAYMENT_DAY' => 'nullable|integer|min:1|max:31',
            'TAX_FRACTION' => 'required|integer',
            'TAX_FRACTION_TIMING' => 'required|integer',
            'FRACTION' => 'required|integer',
            'NOTE' => 'nullable|string|max:1000'
        ]);
        
        $customerData = $request->all();
        if (!is_array($customerData)) {
            $customerData = [];
        }
        
        $phoneError = $this->phone_validation($customerData);
        $faxError = $this->fax_validation($customerData);
        
        if (isset($customerData['HONOR_CODE']) && $customerData['HONOR_CODE'] != 2) {
            $customerData['HONOR_TITLE'] = '';
            $request->merge($customerData);
        }
        
        // Prepare data for Customer model
        $customerData = [
            'CST_ID' => null, // This will be auto-generated
            'CMP_ID' => $companyId,
            'USR_ID' => Auth::id(),
            'UPDATE_USR_ID' => Auth::id(),
            'NAME' => $request->NAME,
            'NAME_KANA' => $request->NAME_KANA,
            'POSTCODE1' => $request->POSTCODE1,
            'HONOR_CODE' => $request->HONOR_CODE,
            'HONOR_TITLE' => $request->HONOR_TITLE,
            'POSTCODE2' => $request->POSTCODE2,
            'CNT_ID' => $request->CNT_ID,
            'ADDRESS' => $request->ADDRESS,
            'SEARCH_ADDRESS' => $request->ADDRESS, // Assuming SEARCH_ADDRESS is same as ADDRESS
            'BUILDING' => $request->BUILDING,
            'PHONE_NO1' => $request->PHONE_NO1,
            'PHONE_NO2' => $request->PHONE_NO2,
            'PHONE_NO3' => $request->PHONE_NO3,
            'FAX_NO1' => $request->FAX_NO1,
            'FAX_NO2' => $request->FAX_NO2,
            'FAX_NO3' => $request->FAX_NO3,
            'WEBSITE' => $request->WEBSITE,
            'CUTOOFF_SELECT' => $request->CUTOOFF_SELECT,
            'CUTOOFF_DATE' => $request->CUTOOFF_DATE,
            'PAYMENT_MONTH' => $request->PAYMENT_MONTH,
            'PAYMENT_SELECT' => $request->PAYMENT_SELECT,
            'PAYMENT_DAY' => $request->PAYMENT_DAY,
            'EXCISE' => $request->TAX_FRACTION, // Assuming EXCISE is same as TAX_FRACTION
            'FRACTION' => $request->FRACTION,
            'TAX_FRACTION' => $request->TAX_FRACTION,
            'TAX_FRACTION_TIMING' => $request->TAX_FRACTION_TIMING,
            'CHR_ID' => $request->CHR_ID,
            'NOTE' => $request->NOTE,
            'INSERT_DATE' => now(),
            'LAST_UPDATE' => now(),
            'COMPANY_ID' => $companyId
        ];

        $customer = new Customer();
        $customer->fill($customerData);

        // Store phone and fax error in session instead of trying to save to non-existent columns
        session(['phone_error' => $phoneError, 'fax_error' => $faxError]);

        try {
            $customer->save();
            Session::flash('success', '取引先を保存しました');
            return redirect()->route('customer.check', ['customer_ID' => $customer->CST_ID]);
        } catch (\Exception $e) {
            var_dump($e->getMessage());die;
            \Log::error('Customer save error: ' . $e->getMessage());
            return back()->withInput()->withErrors('取引先の保存中にエラーが発生しました。');
        }

        return back()->withInput()->withErrors($setdata['error']);
    }

    private function initializeCustomer($companyId)
    {
        $customer = $this->customer->getPayment($companyId);
        $defaultHonor = $this->customer->getHonor($companyId);
        
        if ($defaultHonor) {
            $customer['HONOR_CODE'] = $defaultHonor[0]['HONOR_CODE'];
            if ($defaultHonor[0]['HONOR_CODE'] == 2) {
                $customer['HONOR_TITLE'] = $defaultHonor[0]['HONOR_TITLE'];
            }
        }

        return $customer;
    }

    private function getAddViewData($customer)
    {
        return [
            'main_title' => '取引先登録',
            'title_text' => '顧客管理',
            'title' => '抹茶請求書',
            'controller_name' => 'Customer',
            'payment' => Config::get('constants.PaymentMonth'),
            'countys' => Config::get('constants.PrefectureCode'),
            'cutooffSelect' => $this->getCutoffSelectOptions(),
            'paymentSelect' => $this->getPaymentSelectOptions(),
            'excises' => $this->getExciseOptions(),
            'fractions' => $this->getFractionOptions(),
            'tax_fraction_timing' => $this->getTaxFractionTimingOptions(),
            'honor' => Config::get('constants.HonorCode'),
            'phone_error' => 0,
            'fax_error' => 0,
            'perror' => 0,
            'ferror' => 0,
            'user' => Auth::user(),
            'customer' => $customer
        ];
    }

    private function handleEditSubmit(Request $request, $companyId)
    {
        $phoneError = $this->phone_Validation($request->input('Customer'));
        $faxError = $this->faxValidation($request->input('Customer'));

        if ($request->input('Customer.HONOR_CODE') != 2) {
            $request->merge(['Customer.HONOR_TITLE' => '']);
        }

        $customer = Customer::find($request->input('Customer.CST_ID'));

        if ($customer) {
            $this->setData($request->input(), $companyId, 'update', $phoneError, $faxError);

            if (!$this->hasError()) {
                Session::flash('success', '取引先を保存しました');
                return redirect()->route('customer.check', ['customer_ID' => $request->input('Customer.CST_ID')]);
            }
        }

        return back()->withInput()->withErrors($this->getErrors());
    }

    private function getEditViewData($customer)
    {
        return [
            'main_title' => '取引先編集',
            'title_text' => '顧客管理',
            'title' => '抹茶請求書',
            'controller_name' => 'Customer',
            'payment' => Config::get('constants.PaymentMonth'),
            'countys' => Config::get('constants.PrefectureCode'),
            'cutooff_select' => $this->getCutoffSelectOptions(),
            'payment_select' => $this->getPaymentSelectOptions(),
            'excise' => $this->getExciseOptions(),
            'fraction' => $this->getFractionOptions(),
            'tax_fraction_timing' => $this->getTaxFractionTimingOptions(),
            'honor' => Config::get('constants.HonorCode'),
            'phone_error' => 0,
            'fax_error' => 0,
            'charge' => Customer::getCharge($customer->CHR_ID),
            'customer' => $customer,
            'user' => Auth::user()
        ];
    }

    private function getCutoffSelectOptions()
    {
        return [
            'type' => 'radio',
            'options' => [0 => '末日', 1 => '指定'],
            'class' => 'cutooff_select',
            'style' => 'width:30px;'
        ];
    }

    private function getPaymentSelectOptions()
    {
        return [
            'type' => 'radio',
            'options' => [0 => '末日', 1 => '指定'],
            'class' => 'payment_select',
            'style' => 'width:30px;'
        ];
    }

    private function getExciseOptions()
    {
        return [
            'type' => 'radio',
            'options' => Config::get('constants.ExciseCode'),
            'style' => 'width:30px;'
        ];
    }

    private function getFractionOptions()
    {
        return [
            'type' => 'radio',
            'options' => Config::get('constants.FractionCode'),
            'style' => 'width:30px;'
        ];
    }

    private function getTaxFractionTimingOptions()
    {
        return [
            'type' => 'radio',
            'options' => Config::get('constants.TaxFractionTimingCode'),
            'style' => 'margin-right: 10px; margin-left: 8px;',
            'class' => 'txt_mid'
        ];
    }
}
