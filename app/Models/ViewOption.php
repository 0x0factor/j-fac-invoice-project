<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ViewOption extends Model
{
    protected $table = 'T_OPTION';
    public $timestamps = false;

    public function update_data($param)
    {
        // Fetch all options
        $options = DB::table('T_OPTION')->select('OPTION_ID', 'OPTION_NAME')->get();

        foreach ($options as $option) {
            $ID = $option->OPTION_ID;
            $NAME = $option->OPTION_NAME;

            // Handle logo separately
            if ($NAME === 'logo') {
                if (isset($param['ViewOption'][$NAME]) && !$param['ViewOption'][$NAME]->getError()) {
                    $VALUE = $param['ViewOption'][$NAME]->getClientOriginalName();
                    DB::table('T_OPTION')
                        ->where('OPTION_ID', $ID)
                        ->update([
                            'OPTION_VALUE' => $VALUE,
                            'LAST_UPDATE' => DB::raw('NOW()')
                        ]);
                }
            } else {
                if (isset($param['ViewOption'][$NAME])) {
                    $VALUE = $param['ViewOption'][$NAME];
                    DB::table('T_OPTION')
                        ->where('OPTION_ID', $ID)
                        ->update([
                            'OPTION_VALUE' => $VALUE,
                            'LAST_UPDATE' => DB::raw('NOW()')
                        ]);
                }
            }
        }
    }

    public function get_option()
    {
        return DB::table('T_OPTION')
            ->select('OPTION_NAME', 'OPTION_NAME_JP', 'OPTION_VALUE')
            ->get()
            ->toArray();
    }
}
