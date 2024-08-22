<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer; // Adjust based on your actual model namespace

class Charge extends Model
{
    protected $table = 'T_CHARGE'; // Table name

    protected $primaryKey = 'CHR_ID'; // Primary key

    protected $fillable = [
        'STATUS',
        'CHARGE_NAME',
        'CHARGE_NAME_KANA',
        'UNIT',
        'POST',
        'MAIL',
        'POSTCODE1',
        'POSTCODE2',
        'CNT_ID',
        'ADDRESS',
        'BUILDING',
        'PHONE_NO1',
        'PHONE_NO2',
        'PHONE_NO3',
        'FAX_NO1',
        'FAX_NO2',
        'FAX_NO3',
        'SEAL_METHOD',
        'SEAL_STR',
        'SEAL',
        'DEL_SEAL',
        'CHR_SEAL_FLG',
        'USR_ID',
        'UPDATE_USR_ID',
        'SEARCH_ADDRESS',
        'INSERT_DATE',
        'CMP_ID',
        'LAST_UPDATE',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    /**
     * Custom methods
     */

    /**
     * Retrieve list of charges for a company
     *
     * @param int $_company_ID
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function indexSelect($_company_ID)
    {
        return $this->where('CMP_ID', $_company_ID)
                    ->select('CHR_ID', 'CHARGE_NAME', 'UNIT', 'PHONE_NO1', 'PHONE_NO2', 'PHONE_NO3', 'STATUS')
                    ->get();
    }

    /**
     * Delete selected charges and disassociate from customers
     *
     * @param array $_param
     * @return bool
     */
    public function indexDelete($_param)
    {
        $param = array_keys(array_filter($_param['Charge'], function ($value) {
            return $value == 1;
        }));

        // Find customers associated with the charges
        $customers = Customer::whereIn('CHR_ID', $param)->get();

        \DB::beginTransaction();

        try {
            // Disassociate charges from customers
            foreach ($customers as $customer) {
                $customer->CHR_ID = 0;
                $customer->save();
            }

            // Delete charges
            $deleted = $this->whereIn('CHR_ID', $param)->delete();

            if ($deleted) {
                \DB::commit();
                return true;
            } else {
                \DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            \DB::rollback();
            return false;
        }
    }

    /**
     * Retrieve charge details by CHR_ID
     *
     * @param int $_chr_id
     * @return string|null
     */
    public function getCharge($_chr_id)
    {
        $charge = $this->where('CHR_ID', $_chr_id)->first();

        return $charge ? $charge->CHARGE_NAME : null;
    }

    /**
     * Save charge data
     *
     * @param array $_param
     * @param int $_company_ID
     * @param int $_perror
     * @param int $_ferror
     * @param int|null $_chr_id
     * @return bool|string
     */
    public function setData($_param, $_company_ID, $_perror, $_ferror, &$_chr_id = null)
    {
        // Generate searchable address
        $county = config('constants.PrefectureCode');

        $_param['SEARCH_ADDRESS'] = '';



        if ($_param['CNT_ID']) {
            $_param['SEARCH_ADDRESS'] .= $county[$_param['CNT_ID']];
        }

        $_param['SEARCH_ADDRESS'] .= $_param['ADDRESS'] . $_param['BUILDING'];

        // Handle image processing (not shown in the provided code)

        // Begin transaction
        \DB::beginTransaction();

        try {
            // Save charge data
            $charge = new Charge;
            // $charge->fill($_param);
            $charge->CHR_ID = $_company_ID;
            $charge->CMP_ID = $_company_ID;
            $charge->USR_ID = $_param['USR_ID'];
            $charge->UPDATE_USR_ID = $_param['UPDATE_USR_ID'];
            $charge->UNIT = $_param['UNIT'];
            $charge->POST = $_param['POST'];
            $charge->CHARGE_NAME = $_param['CHARGE_NAME'];
            $charge->CHARGE_NAME_KANA = $_param['CHARGE_NAME_KANA'];
            $charge->MAIL = $_param['MAIL'];
            $charge->POSTCODE1 = $_param['POSTCODE1'];
            $charge->POSTCODE2 = $_param['POSTCODE2'];
            $charge->CNT_ID = $_param['CNT_ID'];
            $charge->ADDRESS = $_param['ADDRESS'];
            $charge->SEARCH_ADDRESS = $_param['SEARCH_ADDRESS'];
            $charge->BUILDING = $_param['BUILDING'];
            $charge->PHONE_NO1 = $_param['PHONE_NO1'];
            $charge->PHONE_NO2 = $_param['PHONE_NO2'];
            $charge->PHONE_NO3 = $_param['PHONE_NO3'];
            $charge->FAX_NO1 = $_param['FAX_NO1'];
            $charge->FAX_NO2 = $_param['FAX_NO2'];
            $charge->FAX_NO3 = $_param['FAX_NO3'];
            $charge->STATUS = $_param['STATUS'];
            $charge->SEAL = "SEAL";
            $charge->CHR_SEAL_FLG = $_param['CHR_SEAL_FLG'];
            $charge->INSERT_DATE = date("Y-m-d H:i:s");
            $charge->LAST_UPDATE = date("Y-m-d H:i:s");
            $charge->ADD_DATE = date("Y-m-d H:i:s");
            $charge->save();
            // Commit transaction on success
            \DB::commit();
            $_chr_id = $charge->CHR_ID;
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on failure
            \DB::rollback();
            return false;
        }
    }

    /**
     * Retrieve charge details for editing
     *
     * @param int $_charge_ID
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function editSelect($_charge_ID)
    {
        return $this->where('CHR_ID', $_charge_ID)->first();
    }

    /**
     * Retrieve seal image by CHR_ID
     *
     * @param int $_charge_ID
     * @return string|null
     */
    public function getImage($_charge_ID)
    {
        $seal = $this->where('CHR_ID', $_charge_ID)->pluck('SEAL')->first();

        return $seal ?: null;
    }

    /**
     * Delete seal image by CHR_ID
     *
     * @param int $_charge_ID
     * @return bool
     */
    public function sealDelete($_charge_ID)
    {
        $charge = $this->find($_charge_ID);
        $charge->SEAL = null;

        return $charge->save();
    }

    /**
     * Retrieve CHR_SEAL_FLG by CHR_ID
     *
     * @param int $_charge_ID
     * @return mixed
     */
    public function getSealFlg($_charge_ID)
    {
        return $this->where('CHR_ID', $_charge_ID)->value('CHR_SEAL_FLG');
    }

    /**
     * Check association with invoices (Quote, Bill, Delivery)
     *
     * @param array $_param
     * @return array
     */
    public function checkPegging($_param)
    {
        $param = array_filter($_param, function ($value) {
            return $value['Charge']["CHR_ID"] ?? null;
        });

        if (empty($param)) {
            return false;
        }

        $chargeIds = array_column($param, 'CHR_ID');

        // Check association with Quote
        $quoteCount = Quote::whereIn('CHR_ID', $chargeIds)->count();

        // Check association with Bill
        $billCount = Bill::whereIn('CHR_ID', $chargeIds)->count();

        // Check association with Delivery
        $deliveryCount = Delivery::whereIn('CHR_ID', $chargeIds)->count();

        return [
            'Quote' => $quoteCount,
            'Bill' => $billCount,
            'Delivery' => $deliveryCount,
        ];
    }
}
