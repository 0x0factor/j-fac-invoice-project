<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BaseModel extends Model
{
    /**
     * Search within an array
     *
     * @param  array  $_param  The array to search within
     * @param  array  $_option The keys to search for
     * @return array  The result data array
     */
    public function searchArray($_param, $_option)
    {
        // Initialize data
        $data = array_fill_keys($_option, null);

        if (is_array($_param)) {
            $this->_searchArray($_param, $_option, $data);
        }

        return $data;
    }

    private function _searchArray($_param, $_option, &$_data)
    {
        foreach ($_param as $Pkey => $Pvalue) {
            if (in_array($Pkey, $_option)) {
                $_data[$Pkey] = $Pvalue;
            }
            if (is_array($Pvalue)) {
                $this->_searchArray($Pvalue, $_option, $_data);
            }
        }
    }

    /**
     * Paginate count with additional options.
     *
     * @param  array|null  $conditions
     * @param  int  $recursive
     * @param  array  $extra
     * @return int
     */
    public function paginateCount($conditions = null, $extra = [])
    {
        $query = $this->where($conditions);

        if (isset($extra['group'])) {
            $query->groupBy($extra['group']);
        }

        return $query->count();
    }

    /**
     * Change status for a specific set of records.
     *
     * @param  array  $_param
     * @param  int  $value
     * @param  array  $user
     * @return bool
     */
    public function changeStatus($_param, $value, $user)
    {
        $primaryKey = $this->getKeyName();
        $changeIds = [];

        foreach ($_param as $key => $val) {
            if ($val == 1 && is_numeric($key)) {
                $changeIds[] = $key;
            }
        }

        try {
            DB::transaction(function () use ($changeIds, $value, $user, $primaryKey) {
                $this->whereIn($primaryKey, $changeIds)
                    ->update([
                        'status' => $value,
                        'update_usr_id' => $user['id']
                    ]);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Permit parameters based on accessible fields.
     *
     * @param  array  $param
     * @return array
     */
    public function permitParams($param)
    {
        $result = [];
        $accessible = property_exists($this, 'accessible') ? $this->accessible : [];

        if (isset($param[$this->getTable()])) {
            foreach ($param as $key => $val) {
                if (isset($accessible[$key])) {
                    foreach ($val as $skey => $sval) {
                        if (in_array($skey, $accessible[$key])) {
                            $result[$key][$skey] = $sval;
                        }
                    }
                }
            }
        } else {
            foreach ($param as $key => $val) {
                if (in_array($key, $accessible[$this->getTable()])) {
                    $result[$key] = $val;
                }
            }
        }

        return $result;
    }
}
