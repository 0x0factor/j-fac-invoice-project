<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Serial extends Model
{
    protected $table = 'T_SERIAL';
    protected $primaryKey = 'FORM_ID';
    public $timestamps = false;

    protected $fillable = [
        'NUMBERING_FORMAT',
        'PREFIX',
        'NEXT',
        'LAST_UPDATE'
    ];

    public static $rules = [
        'NUMBERING_FORMAT' => 'required',
        'PREFIX' => 'required|manage_number',
        'NEXT' => 'required|numeric',
    ];

    public function set_data($_param)
    {
        for ($i = 0; $i < 6; $i++) {
            if ($_param[$i]['CHANGED']) {
                if (empty($_param[$i]['NEXT'])) {
                    $_param[$i]['NEXT'] = 1;
                }
                $_param[$i]['LAST_UPDATE'] = now();
                DB::table($this->table)
                    ->where('FORM_ID', $i)
                    ->update([
                        'NUMBERING_FORMAT' => $_param[$i]['NUMBERING_FORMAT'],
                        'NEXT' => $_param[$i]['NEXT'],
                        'PREFIX' => $_param[$i]['PREFIX'],
                        'LAST_UPDATE' => now()
                    ]);
            }
        }
    }

    public function get_data()
    {
        $result = [];
        for ($i = 0; $i < 6; $i++) {
            $temp = DB::table($this->table)
                ->select('FORM_ID', 'NUMBERING_FORMAT', 'PREFIX', 'NEXT', 'LAST_UPDATE')
                ->where('FORM_ID', $i)
                ->first();
            $result[$i] = (array) $temp;
        }
        return $result;
    }

    public function get_number($_form)
    {
       // Check if data is empty or null
    if (empty($data) || !is_array($data)) {
        // Handle the case where data is empty or null
        return null; // Or throw an exception, depending on your logic
    }

    // Get the FormID configuration value
    $id = Config::get('FormID');

    // Check if $_form is a valid index in $id array
    if (!isset($id[$_form])) {
        // Handle the case where $_form is not a valid index
        return null; // Or throw an exception, depending on your logic
    }

    // Retrieve values from $data using $_form index
    $numbering_format = $data[$id[$_form]]['NUMBERING_FORMAT'] ?? null;
    $prefix = $data[$id[$_form]]['PREFIX'] ?? null;
    $next = $data[$id[$_form]]['NEXT'] ?? null;
    $last = $data[$id[$_form]]['LAST_UPDATE'] ?? null;

    $number = '';

        if ($numbering_format == 0) {
            if (isset($prefix)) {
                $number .= $prefix;
            }
            $number .= sprintf('%05d', $next);
        } else {
            $this->reset_next($id[$_form], $last);

            if (isset($prefix)) {
                $number .= $prefix;
            }
            $number .= date("ymd");
            $number .= sprintf('%02d', $next);
        }

        return $number;
    }

    public function serial_increment($_form)
    {
        $data = $this->get_data();
        $id = Config::get('FormID');
        $next = $data[$id[$_form]]['NEXT'];

        DB::table($this->table)
            ->where('FORM_ID', $id[$_form])
            ->update([
                'LAST_UPDATE' => now(),
                'NEXT' => ++$next
            ]);
    }

    public function reset_next($id, $last)
    {
        $data = $this->get_data();
        $next = $data[$id]['LAST_UPDATE'];

        if (strtotime(date("Y-m-d")) > strtotime($last)) {
            DB::table($this->table)
                ->where('FORM_ID', $id)
                ->update([
                    'LAST_UPDATE' => now(),
                    'NEXT' => 1
                ]);
        }
    }

    public function getSerialConf()
    {
        $company = DB::table('Company')
            ->select('SERIAL_NUMBER')
            ->where('CMP_ID', 1)
            ->first();
        return 1 - $company->SERIAL_NUMBER;
    }
}
?>
