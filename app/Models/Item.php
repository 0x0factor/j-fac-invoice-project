<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Item extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_ITEM';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ITM_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ITM_ID', 'ITEM', 'ITEM_KANA', 'ITEM_CODE', 'UNIT', 'UNIT_PRICE', 'TAX_CLASS', 'USR_ID', 'UPDATE_USR_ID', 'INSERT_DATE', 'LAST_UPDATE'
    ];

    /**
     * Virtual fields equivalent in Laravel.
     *
     * @var array
     */
    protected $appends = [
        'UNIT_PRICE'
    ];

    /**
     * Define relationships.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    /**
     * Define validation rules.
     *
     * @var array
     */
    public static $rules = [
        'ITEM' => ['required', 'string', 'max:40'],
        'ITEM_CODE' => ['string', 'max:8'],
        'ITEM_KANA' => ['string', 'max:50'],
        'UNIT' => ['string', 'max:4'],
        'UNIT_PRICE' => ['string', 'max:8', 'regex:/^\d+$/']
    ];

    /**
     * Set data into the model instance.
     *
     * @param array $_param
     * @return array|bool
     */

    public function set_data($_param)
    {
        // Check and set 'ITM_ID'
        if (isset($_param['ITM_ID'])) {
            $this->setAttribute('id', $_param['ITM_ID']);
        } else {
            $_param['INSERT_DATE'] = Carbon::now();
        }
        $_param['LAST_UPDATE'] = Carbon::now();

        // Fill the object with permitted parameters
        $this->fill($this->permit_params($_param));

        // Attempt to save and capture the result
        $saveResult = $this->save();

        // Check if save was successful
        if (!$saveResult) {
            return ['error' => $this->getErrors()];
        }

        // Now, check if $_param['ITM_ID'] is set, and if not, assign the ID
        if (!isset($_param['ITM_ID'])) {
            $_param['ITM_ID'] = $this->ITM_ID;
        }

        // Return $_param as it has the ITM_ID and other data
        return $_param;
    }

    /**
     * Retrieve item information by ID.
     *
     * @param int $_item_ID
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    public function edit_select($_item_ID)
    {
        return $this->where('ITM_ID', $_item_ID)->first();
    }

    /**
     * Get decimal unit price information.
     *
     * @param int $_company_id
     * @return mixed
     */
    public function get_decimal($_company_id)
    {
        return DB::table('Company')
            ->where('CMP_ID', $_company_id)
            ->value('DECIMAL_UNITPRICE');
    }

    /**
     * Delete items based on criteria.
     *
     * @param array $_param
     * @return bool
     */
    public function index_delete($_param)
    {
        $param = [];

        if (is_array($_param)) {
            foreach ($_param['Item'] as $key => $value) {
                if ($value == 1) {
                    $param[] = $key;
                }
            }
        }

        if (!empty($param)) {
            return $this->whereIn('ITM_ID', $param)->delete();
        } else {
            return false;
        }
    }

    /**
     * Get the virtual field UNIT_PRICE.
     *
     * @return string|null
     */
    public function getUNITPRICEAttribute()
    {
        return (string)$this->attributes['UNIT_PRICE'];
    }

    /**
     * Validate the model attributes.
     *
     * @param array $_param
     * @return array
     */
    public static function validate($_param)
    {
        return Validator::make($_param, static::$rules)->validate();
    }


    public function permit_params($params)
    {
        return array_filter($params, function ($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);
    }
}
