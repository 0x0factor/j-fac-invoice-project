<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bill;
use App\Models\Quote;
use App\Models\Delivery;
use App\Models\User;

class Home extends Model
{
    protected $table = false;

    public static function getRecentForms($modelName, $userId = null)
    {
        $result = [];
        $primaryId = null;

        switch ($modelName) {
            case 'T_BILL':
                $model = new Bill();
                $primaryId = 'MBL_ID';
                break;
            case 'T_QUOTE':
                $model = new Quote();
                $primaryId = 'MQT_ID';
                break;
            case 'T_DELIVERY':
                $model = new Delivery();
                $primaryId = 'MDV_ID';
                break;
        }

        $conditions = $userId ? ["{$modelName}.USR_ID" => $userId] : [];
        $result = $model->orderBy("{$modelName}.LAST_UPDATE", 'desc')
            ->orderBy("{$modelName}.$primaryId", 'desc')
            ->limit(3)
            // ->groupBy("{$modelName}.$primaryId")
            ->get()
            ->toArray();

        $users = User::select('USR_ID', 'NAME')->get()->keyBy('USR_ID')->toArray();

        foreach ($result as &$item) {
            if (isset($item["{$modelName}"]) && is_array($item["{$modelName}"])) {
                $usrId = $item["{$modelName}"]['USR_ID'] ?? null;
                $item["{$modelName}"]['USR_NAME'] = $usrId !== null ? ($users[$usrId] ?? null) : null;
            }
        }


        return $result;
    }
}
