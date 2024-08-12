<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coverpage;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\Charge;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use PDF;

class CoverpageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('cancel_x')) {
            return redirect('/homes');
        }
        $main_title = "送付状作成";
        $title_text = "帳票管理";
        $title = "抹茶請求書";

        $maxline = config('constants.CoverpageMaxFormLine');

        if ($maxline == null) {
            $maxline = 1;
        }
        $SendMethod = config('constants.SendMethod');

        $errors = null;
        $data = $request->all();
        $condition = [];



        if (isset($data['Coverpages'])) {
            // バリデーション
            $coverpage = new Coverpage();
            $coverpage->fill($data['Coverpages']);
            $errors = $coverpage->invalidFields();
            $document_error = $this->document_validation($data, 'Reports');

            // Get the number of non-empty lines
            $count = 0;
            for ($i = 0; $i < $data['Coverpages']['maxformline']; $i++) {
                if (!empty($data[$i]['Reports']['DOCUMENT_TITLE']) || !empty($data[$i]['Reports']['DOCUMENT_NUMBER'])) {
                    $count = $i + 1;
                }
            }

            if ($coverpage->validate() && $document_error['FLG'] == 0) {
                // Remove empty lines
                $data = $this->document_shift($data);
                $count = 0;
                for ($i = 0; $i < $data['Coverpages']['maxformline']; $i++) {
                    if (!empty($data[$i]['Reports']['DOCUMENT_TITLE'])) {
                        $data['Coverpages']['dataformline'] = ($i + 1);
                    }
                }

                $Color = config('constants.ColorCode');
                $dataline = $count;

                // Retrieve company information
                $company_ID = 1;
                $company = Company::index_select($company_ID);
                if (!$company) {
                    abort(404, 'Company not found');
                }

                // Handle invalid input
                if (!isset($data['Coverpages']['CST_ID']) || !isset($data['Coverpages']['SEND_METHOD'])) {
                    Session::flash('message', '値が不正に入力されました');
                    return redirect('/coverpages');
                }

                // Retrieve customer information
                $customer = Customer::edit_select($data['Coverpages']['CST_ID']);
                if (!$customer) {
                    Session::flash('message', '顧客IDが不正に入力されました');
                    return redirect('/coverpages');
                }

                $company['COLOR'] = $Color[$company['COLOR']]['code'];

                // Set company seal URL
                if ($company['SEAL']) {
                    $company['SEAL_IMAGE'] = $this->getTmpImagePath(null, true);
                }

                // Retrieve prefecture and account type information
                $county = config('constants.PrefectureCode');
                $accounttype = config('constants.AccountTypeCode');

                // Browser detection
                $browser = $request->server('HTTP_USER_AGENT');

                // Create PDF
                $pdf = PDF::loadView('pdf.coverpage', [
                    'param' => $data,
                    'county' => $county,
                    'company' => $company
                ]);

                $title = htmlspecialchars($data['Coverpages']['TITLE']);
                $filename = mb_convert_encoding("送付状_" . $title . ".pdf", "SJIS-win", "UTF-8");

                return $pdf->download($filename);
            } else {
                return view('coverpage.index', [
                    'errors' => $errors,
                    'dataline' => $count ?: 1,
                    'main_title' => $main_title,
                    'title_text' => $title_text,
                    'title' => $title,
                    'maxline' => $maxline,
                    'SendMethod' => $SendMethod,
                    'data' => $data,
                    'controller_name' => 'Coverpage',
                ]);
            }
        } else {
            $data['Coverpages']['DATE'] = date("Y-m-d");
            return view('coverpage.index', [
                'dataline' => 1,
                'main_title' => $main_title,
                'title_text' => $title_text,
                'title' => $title,
                'controller_name' => 'Coverpage',
                'maxline' => $maxline,
                'SendMethod' => $SendMethod,
                'data' => $data,
            ]);
        }
    }

    /**
     * 送付書類のバリデーション
     *
     * @param array $_param アイテムの入っている配列
     * @param string $_field 検索したいフィールド名を渡す
     * @return array $error 結果データを配列で返す
     */
    protected function document_validation(array $_param, string $_field)
    {
        $_error = [
            'DOCUMENT_TITLE' => [
                'NO' => [],
                'FLAG' => 0
            ],
            'DOCUMENT_NUMBER' => [
                'NO' => [],
                'FLAG' => 0
            ],
            'FLG' => 0
        ];

        // バリデーション
        $empty = 0;
        for ($i = 0; $i < count($_param) - 1; $i++) {
            $document_value = ceil(mb_strwidth($_param[$i][$_field]['DOCUMENT_TITLE']) / 2);
            if ($document_value > 15) {
                $_error['DOCUMENT_TITLE']['NO'][$i] = 'over';
            }

            if (empty($document_value) && empty($_param[$i][$_field]['DOCUMENT_NUMBER'])) {
                $empty++;
            } else if (empty($document_value) && !empty($_param[$i][$_field]['DOCUMENT_NUMBER'])) {
                $_error['DOCUMENT_TITLE']['NO'][$i] = 'empty';
            }

            if (preg_match("/^[0-9]*$/", $_param[$i][$_field]['DOCUMENT_NUMBER']) == 0 && $_param[$i][$_field]['DOCUMENT_NUMBER'] !== null) {
                $_error['DOCUMENT_NUMBER']['NO'][$i] = $i;
            } else if ($_param[$i][$_field]['DOCUMENT_NUMBER'] > 9999999) {
                $_error['DOCUMENT_NUMBER']['NO'][$i] = $i;
            } else if (!empty($_param[$i][$_field]['DOCUMENT_TITLE']) && empty($_param[$i][$_field]['DOCUMENT_NUMBER'])) {
                $_error['DOCUMENT_NUMBER']['NO'][$i] = $i;
            }
        }

        if ($empty == count($_param) - 1) {
            $_error['EMP_FLG'] = 1;
            $_error['FLG'] = 1;
        }

        foreach ($_error['DOCUMENT_TITLE']['NO'] as $key => $value) {
            if ($value === 'over') {
                $_error['DOCUMENT_TITLE']['OVER_FLAG'] = 1;
            }
            if ($value === 'empty') {
                $_error['DOCUMENT_TITLE']['EMP_FLAG'] = 1;
            }
            $_error['FLG'] = 1;
        }

        if (!empty($_error['DOCUMENT_NUMBER']['NO'])) {
            $_error['DOCUMENT_NUMBER']['FLAG'] = 1;
            $_error['FLG'] = 1;
        }

        return $_error;
    }

    // 送付書類上詰め
    protected function document_shift(array $_param)
    {
        $max = $_param['Coverpages']['maxformline']; // 最大行数
        $fill_line = 0; // 上から埋まっている行数

        for ($i = 0; $i < $max; $i++) {
            if (empty($_param[$i]['Reports']['DOCUMENT_TITLE'])) {
                $fill_line = $i;
                break;
            }
        }

        for ($i = 0; $i < $max; $i++) {
            for ($j = $fill_line; $j < $max; $j++) {
                if (!empty($_param[$j]['Reports']['DOCUMENT_TITLE'])) {
                    $_param[$fill_line]['Reports']['DOCUMENT_TITLE'] = $_param[$j]['Reports']['DOCUMENT_TITLE'];
                    $_param[$fill_line]['Reports']['DOCUMENT_NUMBER'] = $_param[$j]['Reports']['DOCUMENT_NUMBER'];
                    $_param[$j]['Reports']['DOCUMENT_TITLE'] = '';
                    $_param[$j]['Reports']['DOCUMENT_NUMBER'] = '';
                    $fill_line++;
                }
            }
        }
        return $_param;
    }

    public function store() {
        return "123";
    }
}
