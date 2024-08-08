<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\Charge;

class Customer extends Model
{
    protected $table = 'T_CUSTOMER';
    protected $primaryKey = 'CST_ID';
    public $timestamps = false;

    protected $fillable = [
        'CST_ID', 'NAME', 'NAME_KANA', 'POSTCODE1', 'POSTCODE2', 'CNT_ID',
        'ADDRESS', 'BUILDING', 'PHONE_NO1', 'PHONE_NO2', 'PHONE_NO3',
        'FAX_NO1', 'FAX_NO2', 'FAX_NO3', 'HONOR_CODE', 'HONOR_TITLE',
        'WEBSITE', 'CHR_NAME', 'CHR_ID', 'CUTOOFF_SELECT', 'CUTOOFF_DATE',
        'PAYMENT_MONTH', 'PAYMENT_SELECT', 'PAYMENT_DAY', 'EXCISE',
        'TAX_FRACTION', 'TAX_FRACTION_TIMING', 'FRACTION', 'NOTE',
        'USR_ID', 'UPDATE_USR_ID', 'SEARCH_ADDRESS', 'CMP_ID',
        'INSERT_DATE', 'LAST_UPDATE'
    ];

    protected $searchableColumns = [
        'NAME' => ['NAME', 'NAME_KANA'],
        'ADDRESS' => 'SEARCH_ADDRESS',
        'CST_ID' => 'CST_ID'
    ];

    protected $casts = [
        'INSERT_DATE' => 'datetime',
        'LAST_UPDATE' => 'datetime',
    ];


    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    // Validation rules
    public static $rules = [
        'NAME' => 'required|max:30|not_regex:/^\s*$/',
        'NAME_KANA' => 'max:100|regex:/^[ァ-ヴー\s]+$/|not_regex:/^\s*$/',
        'POSTCODE1' => 'required|digits:3',
        'POSTCODE2' => 'required|digits:4',
        'ADDRESS' => 'max:50|not_regex:/^\s*$/',
        'BUILDING' => 'max:50',
        'WEBSITE' => 'nullable|url|max:100',
        'NOTE' => 'max:1000',
        'CUTOOFF_DATE' => 'nullable|integer|between:1,31',
        'PAYMENT_DAY' => 'nullable|integer|between:1,31',
        'HONOR_TITLE' => 'max:4'
    ];

    // Custom Methods

    public function setData($params, $companyId, $state = '', $phoneError = false, $faxError = false)
    {
        $county = Config::get('prefecture_codes');
        $params['Customer']['SEARCH_ADDRESS'] = ($params['Customer']['CNT_ID'] != 0) ? $county[$params['Customer']['CNT_ID']] . $params['Customer']['ADDRESS'] . ($params['Customer']['BUILDING'] ?? "") : "";

        $params['Customer']['CMP_ID'] = $companyId;
        $params['Customer']['LAST_UPDATE'] = now();

        if ($state === 'new') {
            $params['Customer']['INSERT_DATE'] = now();
        }

        DB::beginTransaction();
        try {
            $customer = $this->fill($params['Customer']);
            $customer->save();

            if ($phoneError || $faxError) {
                DB::rollBack();
                $errors = [];
                if ($phoneError) $errors['PHONE'] = "正しい電話番号を入力してください";
                if ($faxError) $errors['FAX'] = "正しいfax番号を入力してください";
                return ['error' => $errors];
            } else {
                DB::commit();
                return $customer;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function indexDelete(array $params, &$errors)
    {
        $ids = [];
        $errors = [];

        foreach ($params['Customer'] as $key => $value) {
            if ($value == 1) {
                if ($this->hasRelatedRecords($key)) {
                    $errors[] = $key;
                } else {
                    $ids[] = $key;
                }
            }
        }

        if (!empty($errors)) {
            return false;
        }

        return $this->destroy($ids);
    }

    private function hasRelatedRecords($customerId)
    {
        $hasQuotes = Quote::where('CST_ID', $customerId)->exists();
        $hasBills = Bill::where('CST_ID', $customerId)->exists();
        $hasDeliveries = Delivery::where('CST_ID', $customerId)->exists();
        $hasCustomerCharges = CustomerCharge::where('CST_ID', $customerId)->exists();

        return $hasQuotes || $hasBills || $hasDeliveries || $hasCustomerCharges;
    }

    public function select_Charge($companyId, $condition = null)
    {
        $charges = Charge::where($condition)->get(['CHR_ID', 'CHARGE_NAME']);

        $result = [];
        foreach ($charges as $charge) {
            $result[$charge->CHR_ID] = $charge->CHARGE_NAME;
        }

        return $result;
    }

    public function getCharge($chrId)
    {
        $charge = Charge::find($chrId, ['CHR_ID', 'CHARGE_NAME']);
        return $charge ? $charge->CHARGE_NAME : '';
    }


    public function editSelect($customerId)
    {
        return $this->find($customerId);
    }

    public function selectCustomer($condition = null)
    {
        $customers = $this->where($condition)->get(['CST_ID', 'NAME']);

        $result = [0 => "顧客名を選択してください"];
        foreach ($customers as $customer) {
            $result[$customer->CST_ID] = $customer->NAME;
        }

        return $result;
    }

    public function getCustomer($customerId)
    {
        $customer = $this->find($customerId, ['CST_ID', 'NAME']);
        return $customer ? $customer->NAME : '';
    }
    public function checkPegging(array $params)
    {
        $ids = array_column($params, 'Customer.CST_ID');
        if (empty($ids)) {
            return false;
        }

        $pegged = [
            'quotes' => Quote::whereIn('CST_ID', $ids)->pluck('CST_ID')->toArray(),
            'bills' => Bill::whereIn('CST_ID', $ids)->pluck('CST_ID')->toArray(),
            'deliveries' => Delivery::whereIn('CST_ID', $ids)->pluck('CST_ID')->toArray(),
            'customerCharges' => CustomerCharge::whereIn('CST_ID', $ids)->pluck('CST_ID')->toArray(),
        ];

        $errors = array_unique(array_merge(...array_values($pegged)));

        return $errors;
    }

    public function getHonor($_company_id)
    {
        $form = new Form(); // Assuming Form model exists in App\Models namespace
        return $form->getHonor($_company_id);
    }

    public function getPayment($companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return false;
        }
        return [
            'Customer' => [
                'CUTOOFF_SELECT' => 0,
                'PAYMENT_SELECT' => 0,
                'EXCISE' => $company->EXCISE,
                'FRACTION' => $company->FRACTION,
                'TAX_FRACTION' => $company->TAX_FRACTION,
                'TAX_FRACTION_TIMING' => $company->TAX_FRACTION_TIMING
            ]
        ];
    }

    public function getInvoiceNum()
    {
        $customers = $this->all();
        $invoiceNum = [];
        foreach ($customers as $customer) {
            $id = $customer->CST_ID;
            $invoiceNum[$id] = [
                'Quote' => Quote::where('CST_ID', $id)->distinct('MQT_ID')->count(),
                'Bill' => Bill::where('CST_ID', $id)->distinct('MBL_ID')->count(),
                'Delivery' => Delivery::where('CST_ID', $id)->distinct('MDV_ID')->count()
            ];
        }
        return $invoiceNum;
    }
}
