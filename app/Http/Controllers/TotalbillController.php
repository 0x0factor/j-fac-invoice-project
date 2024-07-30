<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\Totalbill;
use App\Models\Bill;
use App\Models\Mail;
use App\Models\CustomerCharge;
use App\Models\Customer;
use App\Models\Serial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Barryvdh\DomPDF\Facade\Pdf; // For PDF generation

class TotalbillController extends Controller
{
    protected $totalbill;
    protected $bill;
    protected $mail;
    protected $customerCharge;
    protected $customer;
    protected $serial;

    public function __construct(
        Totalbill $totalbill,
        Bill $bill,
        Mail $mail,
        CustomerCharge $customerCharge,
        Customer $customer,
        Serial $serial
    ) {
        $this->totalbill = $totalbill;
        $this->bill = $bill;
        $this->mail = $mail;
        $this->customerCharge = $customerCharge;
        $this->customer = $customer;
        $this->serial = $serial;

        $this->middleware('auth');
    }

    public function index()
    {
        $authority = [];
        $condition = [];
        $paginator = Totalbill::where($condition)
        ->orderBy('INSERT_DATE')
        ->paginate(20);
        $list = $paginator->items();

        return view('totalbill.index', [
            'authority' => $authority,
            'main_title' => '合計請求書管理',
            'title_text' => '帳票管理',
            'title' => '抹茶請求書',
            'edit_stat' => config('app.Edit_StatProtocolCode'),
            'mailstatus' => config('app.MailStatusCode'),
            'status' => config('app.IssuedStatCode'),
            'paginator' => $paginator,
            'list' => $list,
        ]);
    }

    public function add(Request $request)
    {
        $data = [];
        $total = 0;
        $tax = 0;
        $subtotal = 0;
        $billfrag = 0;
        $i = 0;
        $stat = 0;
        $user = Auth::user();
        $totalbill = new Totalbill;
        $serial = new Serial;


        if ($request->has('select')) {
            foreach ($request->input('Totalbill') as $key => $val) {
                if (preg_match("/^[0-9]*$/", $key) && $val == 1) {
                    $data[$i] = Bill::where('MBL_ID', $key)->first();
                    $i++;
                }
            }

            if (empty($data)) {
                $i = 0;
                foreach ($request->input('Totalbill') as $key => $val) {
                    if (preg_match("/^[0-9]*$/", $key)) {
                        $data[$i]['Totalbillitem']['MBL_ID'] = $key;
                    }
                    $i++;
                }
                $request->session()->flash('error', '請求書を選択してください');
            }
        }

        if ($request->has('cancel_x') || $request->input('form') == null || $request->has('search_x')) {
            $totalbill = new Totalbill();
            $user_ID = $totalbill->get_user_id($user);
            $user_auth = $this->getUserAuthority($user);

            if ($request->isMethod('post')) {
                if (!$request->has('cancel_x')) {
                    if ($user_auth != 1) {
                        $data = Totalbill::searchBill($request->all());
                    } else {
                        $data = Totalbill::searchBill($request->all(), $user_ID);
                    }
                } else {
                    $i = 0;
                    foreach ($request->input() as $key => $val) {
                        if (preg_match("/^[0-9]*$/", $key)) {
                            $bill_id[$i] = $val['Totalbillitem']['MBL_ID'];
                            $data[$i] = Bill::where('MBL_ID', $val['Totalbillitem']['MBL_ID'])->first();
                            if ($request->input('cancel_x') != 1) {
                                $data[$i]['Bill']['CHK'] = 1;
                            }
                            $i++;
                        }
                    }
                    $stat = $request->input('Totalbill.EDIT_STAT');
                }

                if (!empty($data)) {
                    $billfrag = 1;
                } else {
                    if (!empty($request->input('Totalbill.FROM')) || !empty($request->input('Totalbill.TO')) || !empty($request->input('Totalbill.CST_ID'))) {
                        $request->session()->flash('error', '請求書がありません');
                    } else {
                        $request->session()->flash('error', '条件を指定してください');
                    }
                }

                return view('totalbill.search', [
                    'billlist' => $data,
                    'cst_name' => $request->input('Totalbill.CUSTOMER_NAME'),
                    'cst_id' => $request->input('Totalbill.CST_ID'),
                    'edit_stat' => $this->getEditStatOptions($stat),
                    'billfrag' => $billfrag,
                    'main_title' => '合計請求書管理',
                    'title_text' => '帳票管理',
                    'title' => '帳票管理',
                ]);
            } else {
                $i = 0;

                if ($request->has('submit_x')) {
                    // Token check
                    $this->isCorrectToken($request->input('Security.token'));

                    if ($request->input('Totalbill.EDIT_STAT') == 1) {
                        $total = $request->input('Totalbill.SALE', '0');
                    } elseif ($request->input('Totalbill.EDIT_STAT') == 0) {
                        $total = $request->input('Totalbill.THISM_BILL', '0');
                    }

                    $tax = $request->input('Totalbill.SALE_TAX', '0');

                    if ($request->input('Totalbill.HONOR_CODE') != 2) {
                        $request->merge(['Totalbill.HONOR_TITLE' => '']);
                    }

                    if ($request->input('Totalbill.EDIT_STAT') == 1) {
                        $this->updateValidationRules();
                    }

                    $tbl_id = Totalbill::setData($request->all(), 'new');
                    if ($tbl_id) {
                        // Action log
                        History::reportAction($request->input('Totalbill.USR_ID'), 11, $tbl_id);
                        $request->session()->flash('success', '合計請求書を保存しました');
                        Serial::incrementSerial('TotalBill');
                        return redirect("/totalbills/check/{$tbl_id}");
                    } else {
                        foreach ($request->input() as $key => $val) {
                            if (preg_match("/^[0-9]*$/", $key)) {
                                $bill_id[$i] = $val['Totalbillitem']['MBL_ID'];
                                $data[$i] = Bill::where('MBL_ID', $val['Totalbillitem']['MBL_ID'])->first();
                                $i++;
                            }
                        }
                    }
                } else {
                    $i = 0;
                    foreach ($request->input('Totalbill', []) as $key => $val) {
                        if (preg_match("/^[0-9]*$/", $key) && $val == 1) {
                            $bill_id[$i] = $key;
                            $data[$i] = Bill::where('MBL_ID', $key)->first();

                            // Check if data is not null and use object properties
                            if ($data) {
                                $subtotal += $data->SUBTOTAL; // Correct way to access Eloquent attributes
                                $total += $data->TOTAL;
                                $tax += $data->SALES_TAX;
                            }

                            $i++;
                        }
                    }

                    if ($request->input('Totalbill.EDIT_STAT') == 0) {
                        $request->merge([
                            'Totalbill.THISM_BILL' => $total,
                            'Totalbill.SUBTOTAL' => $subtotal,
                            'Totalbill.SALE_TAX' => $tax,
                        ]);
                    } else {
                        $request->merge([
                            'Totalbill.LASTM_BILL' => 0,
                            'Totalbill.DEPOSIT' => 0,
                            'Totalbill.SALE' => $total,
                            'Totalbill.SALE_TAX' => $tax,
                        ]);
                    }
                    $request->merge(['Totalbill.DATE' => date('Y-m-d')]);

                    $company_ID = 1;
                    $bill = new Bill;
                    $default_honor = $bill->getHonor($company_ID);
                    if(is_array($default_honor) && !empty($default_honor) && isset($default_honor[0]['Company']['HONOR_CODE'])) {
                        $request->merge([
                            'Totalbill.HONOR_CODE' => $default_honor[0]['Company']['HONOR_CODE'],
                            'Totalbill.HONOR_TITLE' => $default_honor[0]['Company']['HONOR_TITLE'],
                        ]);
                    }

                    if ($totalbill->get_serial($company_ID) == 0) {
                        $request->merge(['Totalbill.NO' => $serial->get_number('TotalBill')]);
                    }
                }

                if (empty($data)) {
                    return redirect()->back();
                }

                return view('totalbill.add', [
                    'billlist' => $data,
                    'bill_id' => $bill_id,
                    'edit_stat' => $request->input('Totalbill.EDIT_STAT'),
                    'main_title' => '合計請求書管理',
                    'title_text' => '帳票管理',
                    'title' => '抹茶請求書',
                    'mailstatus' => config('app.MailStatusCode'),
                    'status' => config('app.IssuedStatCode'),
                    'honor' => config('app.HonorCode'),
                ]);
            }
        }
    }

    public function check($tbl_ID = null)
    {
        if (!$tbl_ID) {
            return redirect('/totalbills/index');
        }

        $param = Totalbill::checkSelect($tbl_ID);
        $param['Bill'] = Totalbill::getBill($tbl_ID);

        if (!$param) {
            Session::flash('error', '指定の合計請求書が削除されたか、存在しない可能性があります');
            return redirect('/totalbills/index');
        }

        if (!$this->getCheckAuthority($param['Totalbill']['USR_ID'])) {
            Session::flash('error', '帳票を閲覧する権限がありません');
            return redirect('/totalbills/');
        }

        $editauth = $this->getEditAuthority($param['Totalbill']['USR_ID']);

        if ($customer_charge = CustomerCharge::where('CHRC_ID', $param['Totalbill']['CHRC_ID'])->first()) {
            $param['CustomerCharge']['CHARGE_NAME'] = $customer_charge->CHARGE_NAME;
            $param['CustomerCharge']['UNIT'] = $customer_charge->UNIT;
        }

        return view('totalbill.check', [
            'editauth' => $editauth,
            'param' => $param,
            'honor' => config('app.HonorCode'),
            'main_title' => '合計請求書確認',
            'title_text' => '帳票管理',
            'title' => '抹茶請求書',
        ]);
    }

    /**
     * Show the form for editing the specified total bill.
     */
    public function edit(Request $request, $tbl_ID = null)
    {
        $edit = 0;
        $total = 0;
        $subtotal = 0;
        $tax = 0;
        $data = [];
        $billfrag = 0;

        if ($request->has('select')) {
            $selectedBills = $request->input('Totalbill');
            foreach ($selectedBills as $key => $val) {
                if (preg_match("/^[0-9]*$/", $key) && $val == 1) {
                    $data[] = Bill::where('MBL_ID', $key)->first();
                }
            }

            if (empty($data)) {
                $request->session()->flash('error', '請求書を選択してください');
                return redirect('/totalbills/index');
            }
        }

        if ($request->has('cancel_x') || $request->isMethod('get') || $request->has('search_x') || $request->has('search_y')) {
            $user_ID = $this->getUserID();
            $user_auth = $this->getUserAuthority();

            if ($request->isMethod('post') && !$request->has('cancel_x')) {
                $data = ($user_auth != 1)
                    ? Totalbill::searchBill($request->all())
                    : Totalbill::searchBill($request->all(), $user_ID);
            } else {
                $data = [];
                foreach ($request->input('Totalbill', []) as $key => $val) {
                    if (preg_match("/^[0-9]*$/", $key)) {
                        $bill_id[] = $val['Totalbillitem']['MBL_ID'];
                        $data[] = Bill::where('MBL_ID', $val['Totalbillitem']['MBL_ID'])->first();
                        if (!$request->has('cancel_x')) {
                            $data[count($data) - 1]['CHK'] = 1;
                        }
                    }
                }
            }

            if (empty($data)) {
                $message = (isset($request->input('Totalbill')['FROM']) || isset($request->input('Totalbill')['TO']) || isset($request->input('Totalbill')['CST_ID']))
                    ? '請求書がありません'
                    : '条件を指定してください';
                $request->session()->flash('error', $message);
                $billfrag = 0;
            } else {
                $billfrag = 1;
            }

            return view('totalbill.edit_search', [
                'authority' => $user_auth,
                'main_title' => '合計請求書編集',
                'title_text' => '帳票管理',
                'title' => '抹茶請求書',
                'billlist' => $data,
                'billfrag' => $billfrag,
                'cst_name' => $request->input('Totalbill.CUSTOMER_NAME'),
                'cst_id' => $request->input('Totalbill.CST_ID'),
                'tbl_id' => $request->input('Totalbill.TBL_ID'),
                'edit_stat' => config('app.Edit_StatProtocolCode'),
                'mailstatus' => config('app.MailStatusCode'),
                'status' => config('app.IssuedStatCode'),
            ]);
        }

        if ($tbl_ID) {
            $result = Totalbill::where('TBL_ID', $tbl_ID)->first();
            if (!$result) {
                $request->session()->flash('error', '指定の合計請求書が削除されたか、存在しない可能性があります');
                return redirect('/totalbills/index');
            }

            $data = Totalbill::getBill($tbl_ID);
            $cst = Totalbill::getCustomer($tbl_ID);
            $edit = Totalbill::getEditStatus($tbl_ID);
            $tbl_user_id = Totalbill::getUserID($tbl_ID);

            if ($request->input('Customer.NAME')) {
                $cst_name = $request->input('Customer.NAME');
                $cst_id = $request->input('Customer.CST_ID');
            } else {
                $cst_name = $cst['Customer']['NAME'];
                $cst_id = $cst['Customer']['CST_ID'];
            }

            if (!$this->getEditAuthority($tbl_user_id)) {
                $request->session()->flash('error', '帳票を編集する権限がありません');
                return redirect('/totalbills/');
            }

            return view('totalbill.edit', [
                'main_title' => '合計請求書編集',
                'title_text' => '帳票管理',
                'title' => '抹茶請求書',
                'billlist' => $data,
                'tbl_id' => $tbl_ID,
                'bill_id' => $bill_id ?? [],
                'edit_stat' => $request->input('Totalbill.EDIT_STAT'),
                'mailstatus' => config('app.MailStatusCode'),
                'status' => config('app.IssuedStatCode'),
                'honor' => config('app.HonorCode'),
                'cst_name' => $cst_name,
                'cst_id' => $cst_id,
            ]);
        }

        $edit_stat = [
            'type' => 'radio',
            'options' => config('app.Edit_StatProtocolCode'),
            'value' => $edit,
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        return view('totalbill.edit_search', [
            'edit_stat' => $edit_stat,
            'mailstatus' => config('app.MailStatusCode'),
            'status' => config('app.IssuedStatCode')
        ]);
    }

    /**
     * Handle the form actions like delete.
     */
    public function action(Request $request)
    {
        $this->validateToken($request->input('Security.token'));

        $user_ID = $this->getUserID();

        if ($request->has('delete_x')) {
            if (empty($request->input('Totalbill'))) {
                $request->session()->flash('error', '合計請求書が選択されていません');
                return redirect('/totalbills/index');
            }

            foreach ($request->input('Totalbill') as $key => $val) {
                if ($val == 1) {
                    $id = Totalbill::where('TBL_ID', $key)->pluck('USR_ID')->first();
                    if (!$this->getEditAuthority($id)) {
                        $request->session()->flash('error', '削除できない合計請求書が含まれていました');
                        return redirect('/totalbills/index');
                    }
                }
            }

            if (Totalbill::deleteBills($request->input('Totalbill'))) {
                foreach ($request->input('Totalbill') as $key => $value) {
                    if ($value == 1) {
                        History::reportAction($user_ID, 13, $key);
                    }
                }
                $request->session()->flash('success', '合計請求書を削除しました');
                return redirect('/totalbills/index');
            } else {
                return redirect('/totalbills/index');
            }
        }
    }

    /**
     * Generate a PDF for the specified total bill.
     */
    public function pdf(Request $request, $tbl_ID = null)
    {
        if (!$tbl_ID) {
            abort(404);
        }

        $param = Totalbill::previewData($tbl_ID);
        $bill = Totalbill::getBillID($tbl_ID);
        $count = count($bill);

        $Color = config('app.ColorCode');
        $items_a = [];
        $discounts_a = [];
        $item_count_a = [];
        $page_a = [];

        foreach ($bill as $key => $val) {
            $billparam[$key] = Totalbill::getBillDetail($val);
            $items_a[$key] = Totalbill::getBillItems($val);
            $discounts_a[$key] = Totalbill::getDiscounts($val);
            $item_count_a[$key] = Totalbill::getItemCounts($val);
            $page_a[$key] = Totalbill::getPages($val);
        }

        $pdf = Pdf::loadView('totalbills.pdf', [
            'Color' => $Color,
            'param' => $param,
            'count' => $count,
            'items_a' => $items_a,
            'discounts_a' => $discounts_a,
            'item_count_a' => $item_count_a,
            'page_a' => $page_a,
            'title' => '請求書'
        ]);

        return $pdf->download('invoice.pdf');
    }

    private function getUserAuthority()
    {
        if (auth()->check()) {
            return auth()->user()->authority;
        }
        return null;
    }

}
