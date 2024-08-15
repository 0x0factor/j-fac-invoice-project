<?php

namespace App\Main;

use App\Models\History;
use App\Models\Company;
use App\Models\ViewOption;
use App\Models\Charge;
use App\Models\Bill;
use App\Models\Delivery;
use App\Models\Quote;
use App\Models\Totalbill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;



class AppController extends Controller
{
    public $autoLayout = false;
    public $uses = ['History', 'Company', 'ViewOption', 'Charge', 'Bill', 'Delivery', 'Quote', 'Totalbill'];
    public $helpers = ['Session', 'Html', 'Form', 'CustomAjax', 'CustomHtml'];
    public $components = ['Session', 'Common', 'Auth', 'Cookie'];

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Config::get('configure.onlyFullGroupByDisable')) {
                $this->disable_only_full_group_by();
            }

            // プロキシサーバー経由でアクセスした際のcacheを行わない処理を追加
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            header('Pragma: no-cache');

            $user = $this->Auth::user();
            view()->share('usernavi', Config::get('UserNavigate'));
            view()->share('user', $user); // Bladeテンプレートで$userを使えるようにする
            $value = Cookie::get('userid');
            $log = History::h_getlastlog($user['USR_ID']);
            $nbrowser = History::browser_hash();

            if ($log['ACTION'] == 0 && $nbrowser != $log['BROWSER']) {
                if (!$this->Common::matchCheck($this->action, ['pdf', 'contents']) // PDF出力画面はログアウトしないように
                    && !($this->Common::matchCheck($this->params['controller'], ['mails']) && $this->Common::matchCheck($this->action, ['login', 'customer', 'logout']))
                ) {
                    Session::flash('同じユーザーIDで他PCでログインされたかセッションが切れた為、ログアウトしました', '', ['auto_logout']);
                    return redirect()->to('/users/logout');
                }
            }

            // セッションに検索条件を入れる
            $session_params = Session::get('session_params');
            $read_session_params = Session::get('read_session_params');
            $dataArray = $request->query();
            unset($dataArray['url']);

            if (!empty($session_params[$this->controller])) {
                $url = '/' . $this->controller . '?';

                foreach ($session_params[$this->controller] as $key => $val) {
                    if ($val !== reset($session_params[$this->controller])) {
                        $url .= '&';
                    }
                    $url .= $key . '=' . $val;
                }
            } else if (SearchBoxSessionMode == SessionDeleteAlways && $this->action != 'edit' && $this->action != 'check' && $this->action != 'moveback') {
                Session::forget('session_params');
            }
            if (SearchBoxSessionMode == SessionDeleteAlways && $this->action != 'index') {
                Session::forget('read_session_params');
            }
            if (SearchBoxSessionMode == SessionDeleteAlways && empty($session_params[$this->controller])) {
                Session::forget('session_params');
            }

            if (!empty($dataArray) && $this->action == 'index') {
                $insArray = $session_params;
                $insArray[$this->controller] = $dataArray;
                Session::put('session_params', $insArray);
            }

            $this->Common->Authority_Check($user, $this);

            $this->Set_View_Option();

            // PDF出力時の一時画像のチェック
            if ($this->action == 'pdf' && $this->controller != 'Totalbill' ||
                $this->action == 'index' && $this->controller == 'Coverpage') {
                $this->createTmpImage();
            }

            if (file_exists(app_path('plugins/regularbill'))) {
                view()->share('rb_flag', true);
            }

            return $next($request);
        });
    }

    /**
     * ユーザー情報の取得
     */
    public function Get_User_Data()
    {
        return $this->Auth::user();
    }

    /**
     * 表示設定の取得
     */
    public function Set_View_Option()
    {
        $options = ViewOption::all(['OPTION_NAME', 'OPTION_NAME_JP', 'OPTION_VALUE']);

        foreach ($options as $option) {
            if ($option->OPTION_NAME == 'logo') {
                view()->share($option->OPTION_NAME, 'cms/' . $option->OPTION_VALUE);
            } else {
                view()->share($option->OPTION_NAME, $option->OPTION_VALUE);
            }
        }
    }

    /**
     * 閲覧の権限
     */
    public function Get_Check_Authority($_id)
    {
        $user = $this->Auth::user();
        if ($user['AUTHORITY'] == 1) {
            if ($_id != $user['USR_ID']) {
                return false;
            }
        }
        return true;
    }

    /**
     * 編集の権限
     */
    public function Get_Edit_Authority($_id)
    {
        $user = $this->Auth::user();
        if ($user['AUTHORITY'] == 2 || $user['AUTHORITY'] == 1) {
            if ($_id != $user['USR_ID']) {
                return false;
            }
        }
        return true;
    }

    public function Get_User_ID()
    {
        $user = Auth::user();

        if ($user) {
            return $user->USR_ID; // Assuming 'USR_ID' is the field in your user model
        }

        return null; // or handle the case where user is not authenticated
    }

    public function Get_User_AUTHORITY()
    {
        $user = Auth::user();

        if ($user) {
            return $user->AUTHORITY; // Assuming 'AUTHORITY' is the field in your user model
        }

        return null; // or handle the case where user is not authenticated
    }

    /**
     * アイテムのバリデーション
     * @param  Request  $request
     * @param  string  $_field
     * @return array
     *
     * 使用例
     * $error = $this->item_validation($request, 'Deliveryitem');
     */
    public function item_validation(Request $request, $_field)
    {
        
        $_param = $request->input();
        $_error = [
            'ITEM' => [
                'NO' => [],
                'FLAG' => 0
            ],
            'ITEM_NO' => [
                'NO' => [],
                'FLAG' => 0
            ],
            'QUANTITY' => [
                'NO' => [],
                'FLAG' => 0
            ],
            'UNIT' => [
                'NO' => [],
                'FLAG' => 0
            ],
            'UNIT_PRICE' => [
                'NO' => [],
                'FLAG' => 0
            ],
        ];

        // ここからバリデーション
        foreach ($_param as $item) {
            var_export($_param);
            dd($item);

            // 商品名
            $item_value = mb_strwidth($item[$_field]['ITEM']) / 2;
            if ($item_value > 40) {
                $_error['ITEM']['NO'][] = $item;
            }

            // アイテムNO
            $no_value = mb_strlen($item[$_field]['ITEM_NO']);
            if ($no_value > 2 || !preg_match("/^[0-9]+$/", $item[$_field]['ITEM_NO'])) {
                $_error['ITEM_NO']['NO'][] = $item;
            }

            // 数量
            $quantity_value = 0;
            $quantityf_value = 0;
            $j = strpos($item[$_field]['QUANTITY'], '.');
            if ($j) {
                $str = substr($item[$_field]['QUANTITY'], 0, $j);
                $astr = substr($item[$_field]['QUANTITY'], $j + 1);
                $quantityf_value = mb_strlen($str) + mb_strlen($astr);
            } else {
                $quantity_value = mb_strlen($item[$_field]['QUANTITY']);
            }
            if ($quantity_value > 6 || $quantityf_value > 6 || !preg_match("/^(\\|\$)?(0|-?[1-9]\d*|-?(0|[1-9]\d*)\.\d+)$/", $item[$_field]['QUANTITY'])) {
                $_error['QUANTITY']['NO'][] = $item;
            }

            // 単位
            $unit_value = mb_strwidth($item[$_field]['UNIT']) / 2;
            if ($unit_value > 4) {
                $_error['UNIT']['NO'][] = $item;
            }

            // 単価
            $unitprice_value = 0;
            $unitpricef_value = 0;
            $j = strpos($item[$_field]['UNIT_PRICE'], '.');
            if ($j) {
                $str = substr($item[$_field]['UNIT_PRICE'], 0, $j);
                $astr = substr($item[$_field]['UNIT_PRICE'], $j + 1);
                $unitpricef_value = mb_strlen($str) + mb_strlen($astr);
            } else {
                $unitprice_value = mb_strlen($item[$_field]['UNIT_PRICE']);
            }
            if ($unitprice_value > 8 || $unitpricef_value > 8 || !preg_match("/^(\\|\$)?(0|-?[1-9]\d*|-?(0|[1-9]\d*)\.\d+)$/", $item[$_field]['UNIT_PRICE'])) {
                $_error['UNIT_PRICE']['NO'][] = $item;
            }
        }

        foreach ($_param as $item) {
            if (in_array($item, $_error['ITEM']['NO'])) {
                $_error['ITEM']['FLAG'] = 1;
            }
            if (in_array($item, $_error['ITEM_NO']['NO'])) {
                $_error['ITEM_NO']['FLAG'] = 1;
            }
            if (in_array($item, $_error['QUANTITY']['NO'])) {
                $_error['QUANTITY']['FLAG'] = 1;
            }
            if (in_array($item, $_error['UNIT']['NO'])) {
                $_error['UNIT']['FLAG'] = 1;
            }
            if (in_array($item, $_error['UNIT_PRICE']['NO'])) {
                $_error['UNIT_PRICE']['FLAG'] = 1;
            }
        }

        return $_error;
    }
	// Phone number validation
    public function phone_validation($_param, $_type = null)
    {
        if ($_type == 'CustomerCharge') {
            if (!strlen($_param['PHONE_NO1']) && !strlen($_param['PHONE_NO2']) && !strlen($_param['PHONE_NO3'])) {
                return 0;
            } elseif (!strlen($_param['PHONE_NO1']) || !strlen($_param['PHONE_NO2']) || !strlen($_param['PHONE_NO3'])) {
                return 1;
            }
        } elseif ($_type == 'Company') {
            if (!strlen($_param['PHONE_NO1']) || !strlen($_param['PHONE_NO2']) || !strlen($_param['PHONE_NO3'])) {
                return 1;
            }
        } else {
            if (!strlen($_param['PHONE_NO1']) && !strlen($_param['PHONE_NO2']) && !strlen($_param['PHONE_NO3'])) {
                return 0;
            } elseif (!strlen($_param['PHONE_NO1']) || !strlen($_param['PHONE_NO2']) || !strlen($_param['PHONE_NO3'])) {
                return 1;
            }
        }

        $phone_error = 0;
        $phone_no = ($_param['PHONE_NO1'] . $_param['PHONE_NO2'] . $_param['PHONE_NO3']);
        $mphone_no = mb_strlen($phone_no);
        if ($mphone_no > 11 || $mphone_no < 10) {
            $phone_error = 1;
        }
        if (preg_match("/^[0-9]+$/", $phone_no) == 0) {
            $phone_error = 1;
        }
        return $phone_error;
    }

    // Fax number validation
    public function fax_validation($_param)
    {
        if ($_param['FAX_NO1'] || $_param['FAX_NO2'] || $_param['FAX_NO3']) {
            if (!strlen($_param['FAX_NO1']) && !strlen($_param['FAX_NO2']) && !strlen($_param['FAX_NO3'])) {
                return 1;
            }
        }

        $fax_error = 0;
        $fax_no = ($_param['FAX_NO1'] . $_param['FAX_NO2'] . $_param['FAX_NO3']);
        $mfax_no = mb_strlen($fax_no);
        if ($mfax_no > 11 || ($mfax_no < 10 && $mfax_no != 0)) {
            $fax_error = 1;
        }
        if ($mfax_no != 0 && preg_match("/^[0-9]+$/", $fax_no) == 0) {
            $fax_error = 1;
        }
        return $fax_error;
    }

    // Serial validation
    public function serial_validation($_param)
    {
        $serial_error = [
            'ERROR' => 0,
        ];

        for ($i = 0; $i < 5; $i++) {
            if ($_param[$i]['NUMBERING_FORMAT'] == 0 || $_param[$i]['NUMBERING_FORMAT'] == 1) {
                $serial_error[$i]['NUMBERING_FORMAT'] = 0;
            } else {
                $serial_error[$i]['NUMBERING_FORMAT'] = 1;
                $serial_error['ERROR'] = 1;
            }

            if (mb_strlen($_param[$i]['PREFIX']) > 12) {
                $serial_error[$i]['PREFIX'] = 1;
                $serial_error['ERROR'] = 1;
            } elseif (preg_match("/^[a-zA-Z0-9\/_\.-]*$/", $_param[$i]['PREFIX']) == 0) {
                $serial_error[$i]['PREFIX'] = 2;
                $serial_error['ERROR'] = 1;
            } else {
                $serial_error[$i]['PREFIX'] = 0;
            }

            if (mb_strlen($_param[$i]['NEXT']) > 8) {
                $serial_error[$i]['NEXT'] = 1;
                $serial_error['ERROR'] = 1;
            } elseif (!is_numeric($_param[$i]['NEXT'])) {
                $serial_error[$i]['NEXT'] = 2;
                $serial_error['ERROR'] = 1;
            } else {
                $serial_error[$i]['NEXT'] = 0;
            }
        }

        return $serial_error;
    }

    // Compatibility adjustment for items
    public function getCompatibleItems($_param)
    {
        $formType = 'nameitem'; // Assuming 'name' should be replaced with your actual name variable
        $compatibleItems = $_param;
        $count = 0;

        for ($i = 0; $i < count($_param); $i++) {
            if (!empty($_param[$i])) {
                $count = $i;
            } else {
                $count++;
                break;
            }
        }

        for ($i = 0, $j = 0; $i < $count; $i++, $j++) {
            $compatibleItems[$j] = $_param[$i];

            if ($_param[$i][$formType]['LINE_ATTRIBUTE'] == 0 && $_param[$i][$formType]['TAX_CLASS'] == 0) {
                $compatibleItems[$j][$formType]['ITEM_CODE'] = '';
                $compatibleItems[$j][$formType]['TAX_CLASS'] = $_param['name']['EXCISE'] + 1;
            }

            if (isset($_param[$i][$formType]['DISCOUNT_TYPE']) && $_param[$i][$formType]['DISCOUNT_TYPE'] == 0 && empty($_param[$i][$formType]['DISCOUNT'])) {
                $_param[$i][$formType]['DISCOUNT_TYPE'] = null;
                $_param[$i][$formType]['DISCOUNT'] = null;
            }

            if (isset($_param[$i][$formType]['DISCOUNT_TYPE']) && $_param[$i][$formType]['DISCOUNT_TYPE'] == 0) {
                $compatibleItems[$j + 1][$formType] = [];
                $compatibleItems[$j + 1][$formType]['ITEM'] = '　(割引)';
                $compatibleItems[$j + 1][$formType]['UNIT'] = '％';
                $compatibleItems[$j + 1][$formType]['QUANTITY'] = $_param[$i][$formType]['DISCOUNT'];
                $compatibleItems[$j + 1][$formType]['LINE_ATTRIBUTE'] = 4;
                $compatibleItems[$j + 1][$formType]['TAX_CLASS'] = 0;
                $compatibleItems[$j][$formType]['DISCOUNT'] = '';
                $compatibleItems[$j][$formType]['DISCOUNT_TYPE'] = '';
                $compatibleItems[$j][$formType]['TAX_CLASS'] = $_param['name']['EXCISE'] + 1;
                $compatibleItems[$j][$formType]['AMOUNT'] = $_param[$i][$formType]['UNIT_PRICE'] * $_param[$i][$formType]['QUANTITY'];

                $j++;
            } elseif (isset($_param[$i][$formType]['DISCOUNT_TYPE']) && $_param[$i][$formType]['DISCOUNT_TYPE'] == 1) {
                $compatibleItems[$j + 1][$formType] = [];
                $compatibleItems[$j + 1][$formType]['ITEM'] = '　(割引)';
                $compatibleItems[$j + 1][$formType]['AMOUNT'] = -$_param[$i][$formType]['DISCOUNT'];
                $compatibleItems[$j + 1][$formType]['LINE_ATTRIBUTE'] = 3;
                $compatibleItems[$j + 1][$formType]['TAX_CLASS'] = 0;
                $compatibleItems[$j][$formType]['DISCOUNT'] = '';
                $compatibleItems[$j][$formType]['DISCOUNT_TYPE'] = '';
                $compatibleItems[$j][$formType]['TAX_CLASS'] = $_param['name']['EXCISE'] + 1;
                $compatibleItems[$j][$formType]['AMOUNT'] = $_param[$i][$formType]['UNIT_PRICE'] * $_param[$i][$formType]['QUANTITY'];

                $j++;
            }

            $compatibleItems['count'] = $j + 1;
        }

        return $compatibleItems;
    }

    // CSRF token validation
    public function isCorrectToken($_token)
    {
        if ($_token === session()->getId()) {
            return true;
        } else {
            session()->flush();
            session()->flash('message', '正規の画面からご利用ください。');
            return redirect()->route('logout'); // Adjust route name as per your application
        }
    }

    // Create temporary image file
    public function createTmpImage($id = null)
    {
        if ($this->name == 'Coverpage') {
            $this->params['pass'][0] = 1;
        }

        if ($id == null && isset($this->params['pass'][0])) {
            $id = $this->params['pass'][0];
        } elseif (!isset($this->params['pass'][0])) {
            return false;
        }

        $_name = $this->name == 'Totalbill' ? 'Bill' : $this->name;
        $_path = sys_get_temp_dir() . '/img/';
        $_user = auth()->user(); // Assuming you have Laravel authentication set up properly

        $_primary_key = null; // Initialize the primary key variable

        switch ($this->name) {
            case 'Quote':
                $_primary_key = 'MQT_ID';
                break;
            case 'Bill':
                $_primary_key = 'MBL_ID';
                break;
            case 'Delivery':
                $_primary_key = 'MDV_ID';
                break;
            case 'Totalbill':
                $_primary_key = 'MBL_ID';
                break;
        }

        $_company = Company::first(['Company.LAST_UPDATE', 'Company.SEAL']); // Adjust model and fields as per your Company model structure

        $_last_update = str_replace(['-', ':', ' '], '', $_company->LAST_UPDATE);

        $tmp_company_name_user = $_user->LOGIN_ID . '_company';
        $tmp_company_name = $tmp_company_name_user . '_' . $_last_update . '_.png';

        $company_file = glob($_path . $tmp_company_name_user . "*");

        if (empty($company_file) && !empty($_company->SEAL)) {
            $image = fopen($_path . $tmp_company_name, 'w');
            fwrite($image, $_company->SEAL);
            fclose($image);
        } elseif (!empty($_company->SEAL)) {
            $exist_file = explode('_', $company_file[0]);
            $exist_file_last_update = $exist_file[count($exist_file) - 2];

            if ($exist_file_last_update != $_last_update) {
                $image = fopen($_path . $tmp_company_name, 'w');
                fwrite($image, $_company->SEAL);
                fclose($image);

                unlink($company_file[0]);
            }
        }

        if ($this->name != 'Coverpage') {
            $_chr_id = $_name::where($_primary_key, $id)->first(['CHR_ID']);

            $_params = Charge::where('Charge.CHR_ID', $_chr_id->CHR_ID)
                ->first(['Charge.LAST_UPDATE', 'Charge.SEAL']);

            $_last_update = str_replace(['-', ':', ' '], '', $_params->LAST_UPDATE);

            $tmp_charge_name_user = $_user->LOGIN_ID . '_charge' . $_chr_id->CHR_ID;
            $tmp_charge_name = $tmp_charge_name_user . '_' . $_last_update . '_.png';

            $filenames = glob($_path . $tmp_charge_name_user . "*");

            if (empty($filenames) && !empty($_params->SEAL)) {
                $image = fopen($_path . $tmp_charge_name, 'w');
                fwrite($image, $_params->SEAL);
                fclose($image);
            } elseif (!empty($_params->SEAL)) {
                $exist_file = explode('_', $filenames[0]);
                $exist_file_last_update = $exist_file[count($exist_file) - 2];

                if ($exist_file_last_update != $_last_update) {
                    $image = fopen($_path . $tmp_charge_name, 'w');
                    fwrite($image, $_params->SEAL);
                    fclose($image);

                    unlink($filenames[0]);
                }
            }
        }
    }

	//一時画像ファイルのパスを取得
	public function getTmpImagePath($id = null, $isCompany = false)
    {
        if ($this->name == 'Coverpage') {
            $this->params['pass'][0] = 1;
        }

        if ($id == null && isset($this->params['pass'][0])) {
            $id = $this->params['pass'][0];
        } elseif (!isset($this->params['pass'][0])) {
            return false;
        }

        $_name = $this->name == 'Totalbill' ? 'Bill' : $this->name;
        $_path = sys_get_temp_dir() . '/img/';
        $_user = auth()->user();

        switch ($_name) {
            case 'Quote':
                $_primary_key = 'MQT_ID';
                break;
            case 'Bill':
                $_primary_key = 'MBL_ID';
                break;
            case 'Delivery':
                $_primary_key = 'MDV_ID';
                break;
            case 'Totalbill':
                $_primary_key = 'MBL_ID';
                break;
        }

        if ($isCompany) {
            $filenames = glob($_path . $_user->LOGIN_ID . '_company*');
        } else {
            $model = null;
            switch ($_name) {
                case 'Quote':
                    $model = new Quote();
                    break;
                case 'Bill':
                    $model = new Bill();
                    break;
                case 'Delivery':
                    $model = new Delivery();
                    break;
            }

            $_chr_id = $model::where($_primary_key, $id)->first(['CHR_ID']);
            $tmp_charge_name_user = $_user->LOGIN_ID . '_charge' . $_chr_id->CHR_ID;
            $filenames = glob($_path . $tmp_charge_name_user . "*");
        }

        return $filenames[0] ?? null;
    }

    // Move back action
    public function moveback()
    {
        session()->put('read_session_params', true);
        return redirect()->route('index');
    }

    // Move to index action
    public function movetoindex()
    {
        if (config('searchBoxSessionMode') != 'SessionDeleteNever') {
            session()->forget('session_params');
        }
        return redirect()->route('index');
    }

    // Status change action
    public function status_change($data, $redirect_uri)
    {
        $controller_name = '';
        $primary_key = '';
        $model = null;

        switch ($this->params['controller']) {
            case 'quotes':
                $controller_name = '見積書';
                $primary_key = 'MQT_ID';
                $model = new Quote();
                break;
            case 'bills':
                $controller_name = '請求書';
                $primary_key = 'MBL_ID';
                $model = new Bill();
                break;
            case 'deliveries':
                $controller_name = '納品書';
                $primary_key = 'MDV_ID';
                $model = new Delivery();
                break;
        }

        $status_value = $data['STATUS_CHANGE'];
        $status_request = $data;

        if (empty($status_request)) {
            session()->flash('message', $controller_name . 'が選択されていません');
            return redirect($redirect_uri);
        }

        foreach ($status_request as $key => $val) {
            if ($val == 1) {
                $id = $model::where($primary_key, $key)->first(['USR_ID']);

                if (! $this->Get_Edit_Authority($id->USR_ID)) {
                    session()->flash('message', '変更できない' . $controller_name . 'が含まれていました');
                    return redirect($redirect_uri);
                }
            }
        }

        $user = auth()->user();

        if ($model->change_status($status_request, $status_value, $user)) {
            // Success
            session()->flash('message', $controller_name . 'のステータスを一括変更しました');
        } else {
            // Failure
            session()->flash('message', $controller_name . 'のステータスの一括変更に失敗しました');
        }

        return redirect($redirect_uri);
    }

    // Disable ONLY_FULL_GROUP_BY mode
    private function disable_only_full_group_by()
    {
        $result = DB::select("SELECT @@SESSION.sql_mode as result;");

        $setting = $result[0]->result;

        if (strpos($setting, 'ONLY_FULL_GROUP_BY') !== false) {
            $setting = str_replace('ONLY_FULL_GROUP_BY,', '', $setting);
            $query = "SET SESSION sql_mode = '$setting'";
            DB::statement($query);
        }
    }
}
