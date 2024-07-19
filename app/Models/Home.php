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
            case 't_bill':
                $model = new Bill();
                $primaryId = 'MBL_ID';
                break;
            case 't_quote':
                $model = new Quote();
                $primaryId = 'MQT_ID';
                break;
            case 't_delivery':
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
            $item["{$modelName}"]['USR_NAME'] = $users[$item["{$modelName}"]['USR_ID']] ?? null;
        }

        return $result;
    }
}
