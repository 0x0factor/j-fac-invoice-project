<?php

namespace App\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppModel extends Model
{
    /**
     * 住所検索で使用
     * 配列内検索
     * @param  $_param  検索対象の配列または多重配列
     * @param  $_option 検索したいキーを配列で渡す
     * @return array    結果データを配列で返す
     */
    public function SearchArray($_param, $_option)
    {
        // 初期化
        $data = [];

        if (is_array($_option)) {
            foreach ($_option as $value) {
                if (!is_array($value)) {
                    $data[$value] = null;
                }
            }
        }

        if (is_array($_param)) {
            $this->_SearchArray($_param, $_option, $data);
        }

        return $data;
    }

    protected function _SearchArray($_param, $_option, array &$_data)
    {
        foreach ($_param as $Pkey => $Pvalue) {
            foreach ($_option as $Ovalue) {
                if ($Pkey == $Ovalue) {
                    $_data[$Pkey] = $Pvalue;
                }
            }
            if (is_array($Pvalue)) {
                $this->_SearchArray($Pvalue, $_option, $_data);
            }
        }
    }

    /**
     * Custom paginate count method.
     *
     * @param array|null $conditions
     * @param int        $recursive
     * @param array      $extra
     * @return int
     */
    public function paginateCount($conditions = null, $recursive = 0, $extra = [])
    {
        $this->setRecursive($recursive);
        $count = $this->where($conditions)->count();

        if (isset($extra['group'])) {
            $count = $this->getConnection()->select("SELECT COUNT(*) as aggregate FROM {$this->getTable()}");
        }

        return $count;
    }

    /**
     * Change status method.
     *
     * @param array $_param
     * @param int   $value
     * @param array $user
     * @return bool
     */
    public function change_status($_param, $value, $user)
    {
        switch ($this->name) {
            case 'Quote':
                $model = new Quote();
                $primaryKey = 'MQT_ID';
                break;
            case 'Bill':
                $model = new Bill();
                $primaryKey = 'MBL_ID';
                break;
            case 'Delivery':
                $model = new Delivery();
                $primaryKey = 'MDV_ID';
                break;
            default:
                return false;
        }

        $change_ids = [];
        foreach ($_param as $key => $val) {
            if ($val == 1 && is_numeric($key)) {
                $change_ids[] = $key;
            }
        }

        DB::beginTransaction();

        try {
            $model->whereIn($primaryKey, $change_ids)->update([
                'STATUS' => $value,
                'UPDATE_USR_ID' => $user['User']['USR_ID']
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Permit only accessible parameters.
     *
     * @param array $param
     * @return array
     */
    public function permit_params($param)
    {
        $result = [];

        if (isset($param[$this->name])) {
            foreach ($param as $key => $val) {
                if (isset($this->accessible[$key])) {
                    foreach ($val as $skey => $sval) {
                        if (in_array($skey, $this->accessible[$key])) {
                            $result[$key][$skey] = $sval;
                        }
                    }
                }
            }
        } else {
            foreach ($param as $key => $val) {
                if (in_array($key, $this->accessible[$this->name])) {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }
}
