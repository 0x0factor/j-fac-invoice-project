<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerCharge extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_CUSTOMER_CHARGE';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'CHRC_ID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CHRC_ID', 'STATUS', 'CUSTOMER_NAME', 'CST_ID', 'CHARGE_NAME',
        'CHARGE_NAME_KANA', 'UNIT', 'POST', 'MAIL', 'POSTCODE1', 'POSTCODE2',
        'CNT_ID', 'ADDRESS', 'BUILDING', 'PHONE_NO1', 'PHONE_NO2', 'PHONE_NO3',
        'FAX_NO1', 'FAX_NO2', 'FAX_NO3', 'USR_ID', 'UPDATE_USR_ID', 'SEARCH_ADDRESS',
        'INSERT_DATE', 'LAST_UPDATE'
    ];

    /**
     * Validation rules for the CustomerCharge model.
     *
     * These rules can be enforced using Laravel's validation system,
     * typically in a FormRequest class or within controller methods.
     *
     * @var array
     */
    public static $rules = [
        'UNIT' => ['nullable', 'string', 'max:30'],
        'POST' => ['nullable', 'string', 'max:30'],
        'CHARGE_NAME' => ['required', 'string', 'max:30'],
        'CHARGE_NAME_KANA' => ['nullable', 'string', 'max:60', 'regex:/^[ァ-ヶー\s]+$/u'],
        'MAIL' => ['nullable', 'email', 'max:256'],
        'POSTCODE1' => ['nullable', 'string', 'max:3', 'regex:/^[0-9]+$/'],
        'POSTCODE2' => ['nullable', 'string', 'max:4', 'regex:/^[0-9]+$/'],
        'ADDRESS' => ['nullable', 'string', 'max:50'],
        'BUILDING' => ['nullable', 'string', 'max:50'],
    ];

    /**
     * Set data and perform data writing process.
     *
     * @param array $_param
     * @param string $_state
     * @param int $_perror
     * @param int $_ferror
     * @param int|null $_cst_id
     * @return array|false
     */
    public function set_data($_param, $_state = '', $_perror, $_ferror, $_cst_id = null)
    {
        if (isset($_param['CHRC_ID'])) {
            $this->setAttribute('CHRC_ID', $_param['CHRC_ID']);
        }

        // Create searchable address
        $county = config('prefecture.code');
        $building = $_param['BUILDING'] ?? '';
        $_param['SEARCH_ADDRESS'] = ($county[$_param['CNT_ID']] ?? '') . ($_param['ADDRESS'] ?? '') . $building;

        if ($_cst_id !== null) {
            $_param['CST_ID'] = $_cst_id;
        }

        $_param['INSERT_DATE'] = ($_state === 'new') ? now() : $_param['INSERT_DATE'];
        $_param['LAST_UPDATE'] = now();

        DB::beginTransaction();

        try {
            $this->fill($_param)->save();

            if ($_perror != 1 && $_ferror != 1) {
                DB::commit();
                $_param['CHRC_ID'] = $this->getAttribute('CHRC_ID');
            } else {
                DB::rollback();
                if ($_perror == 1) {
                    $_param['error']['PHONE'] = '正しい電話番号を入力してください';
                }
                if ($_ferror == 1) {
                    $_param['error']['FAX'] = '正しいfax番号を入力してください';
                }
                return $_param;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $_param['error'] = $this->getErrors()->toArray();
            if ($_perror == 1) {
                $_param['error']['PHONE'] = '正しい電話番号を入力してください';
            }
            if ($_ferror == 1) {
                $_param['error']['FAX'] = '正しいfax番号を入力してください';
            }
            return $_param;
        }

        return $_param;
    }

    /**
     * Delete records based on parameters.
     *
     * @param array $_param
     * @return bool
     */
    public function index_delete($_param)
    {
        $param = [];
        foreach ($_param['CHRC_ID'] as $key => $value) {
            if ($value == 1) {
                $param[] = $key;
            }
        }

        if (!empty($param)) {
            return $this->whereIn('CHRC_ID', $param)->delete();
        }

        return false;
    }

    /**
     * Select charge information based on company ID.
     *
     * @param int $_company_ID
     * @return array
     */
    public function select_charge($_company_ID)
    {
        $result = self::whereHas('user', function ($query) use ($_company_ID) {
            $query->where('CMP_ID', $_company_ID);
        })->pluck('CHARGE_NAME', 'CHRC_ID')->toArray();

        return $result;
    }

    /**
     * Select customer charge information based on ID.
     *
     * @param int $_customer_ID
     * @return mixed
     */
    public function edit_select($_customer_ID)
    {
        return $this->where('CHRC_ID', $_customer_ID)->first();
    }

    /**
     * Select customer charge records.
     *
     * @param int|null $id
     * @return mixed
     */
    public function select($id = null)
    {
        $query = $this->newQuery();

        if ($id) {
            $query->where('CHRC_ID', $id);
        }

        return $query->get();
    }

    /**
     * Check association with documents.
     *
     * @param array $_param
     * @return array
     */
    public function check_pegging($_param)
    {
        $ids = collect($_param)->pluck('CHRC_ID')->toArray();

        $result = [];

        // Check association with Quote
        $quoteCount = Quote::whereIn('CHRC_ID', $ids)->count();
        foreach ($ids as $id) {
            $result[$id] = $quoteCount > 0 ? 1 : 0;
        }

        // Check association with Bill
        $billCount = Bill::whereIn('CHRC_ID', $ids)->count();
        foreach ($ids as $id) {
            $result[$id] = $billCount > 0 ? 1 : 0;
        }

        // Check association with Delivery
        $deliveryCount = Delivery::whereIn('CHRC_ID', $ids)->count();
        foreach ($ids as $id) {
            $result[$id] = $deliveryCount > 0 ? 1 : 0;
        }

        return $result;
    }

    /**
     * Define the relationship with User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    /**
     * Define the relationship with Customer model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CST_ID');
    }
}

