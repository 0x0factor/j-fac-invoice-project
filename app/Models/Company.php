<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Vendors\model\OtherModel;

class Company extends Model
{
    protected $table = 'T_COMPANY';
    protected $primaryKey = 'CMP_ID';
    public $timestamps = false;

    // Accessible columns for mass assignment
    protected $fillable = [
        'CMP_ID',
        'NAME',
        'REPRESENTATIVE',
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
        'INVOICE_NUMBER',
        'HONOR_CODE',
        'HONOR_TITLE',
        'SEAL',
        'DEL_SEAL',
        'CMP_SEAL_FLG',
        'CUTOOFF_SELECT',
        'CUTOOFF_DATE',
        'PAYMENT_MONTH',
        'PAYMENT_SELECT',
        'PAYMENT_DAY',
        'DECIMAL_QUANTITY',
        'DECIMAL_UNITPRICE',
        'EXCISE',
        'TAX_FRACTION',
        'TAX_FRACTION_TIMING',
        'FRACTION',
        'ACCOUNT_HOLDER',
        'BANK_NAME',
        'BANK_BRANCH',
        'ACCOUNT_TYPE',
        'ACCOUNT_NUMBER',
        'COLOR',
        'DIRECTION',
        'SERIAL_NUMBER',
        'SEARCH_ADDRESS',
        'INSERT_DATE',
        'LAST_UPDATE',
    ];

    // Validation rules, can be used in Form Request or Validator
    public static function rules($id = null)
    {
        return [
            'NAME' => [
                'required',
                'max:30',
                'not_regex:/^\s*$/'
            ],
            'CNT_ID' => [
                'required',
                'integer',
                'between:1,47',
            ],
            'REPRESENTATIVE' => [
                'nullable',
                'max:30',
            ],
            'ADDRESS' => [
                'required',
                'max:50',
                'not_regex:/^\s*$/'
            ],
            'BUILDING' => [
                'nullable',
                'max:50',
            ],
            'POSTCODE1' => [
                'required',
                'digits:3',
                'regex:/^[0-9]+$/'
            ],
            'POSTCODE2' => [
                'required',
                'digits:4',
                'regex:/^[0-9]+$/'
            ],
            'CUTOOFF_DATE' => [
                'nullable',
                'integer',
                'between:1,31'
            ],
            'PAYMENT_DAY' => [
                'nullable',
                'integer',
                'between:1,31'
            ],
            'ACCOUNT_NUMBER' => [
                'nullable',
                'digits:7',
                'regex:/^[0-9]+$/'
            ],
            'ACCOUNT_HOLDER' => [
                'nullable',
                'max:100',
            ],
            'BANK_NAME' => [
                'nullable',
                'max:100',
            ],
            'BANK_BRANCH' => [
                'nullable',
                'max:100',
            ],
            'HONOR_TITLE' => [
                'nullable',
                'max:4',
            ],
            'INVOICE_NUMBER' => [
                'nullable',
                'regex:/^T[1-9]{1}[0-9]{12}$/i'
            ],
        ];
    }
public function indexSetData($params, $perror, $ferror, $serror)
    {
        if ($serror['ERROR']) {
            return false;
        }

        // Construct search address
        $county = Config::get('constants.PrefectureCode');
        $params['SEARCH_ADDRESS'] = $params['CNT_ID'] ? ($county[$params['CNT_ID']] ?? '') : '';
        $params['SEARCH_ADDRESS'] .= $params['ADDRESS'] . $params['BUILDING'];

        // Add timestamps
        if (!isset($params['CMP_ID'])) {
            $params['INSERT_DATE'] = Carbon::now();
        }
        $params['LAST_UPDATE'] = Carbon::now();

        // Handle image creation
        $imageerror = 0;
        $other = new OtherModel();
        $other->imageCreate($params, 'Company', $imageerror);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Save the company data
            if ($this->fill($params)->save()) {
                if ($perror != 1 && $ferror != 1 && empty($imageerror)) {
                    DB::commit();
                    return true;
                } else {
                    // Rollback on error
                    DB::rollback();
                    return empty($imageerror) ? false : $imageerror;
                }
            } else {
                DB::rollback();
                return empty($imageerror) ? false : $imageerror;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function indexSelect($companyID)
    {
        return $this->find($companyID);
    }

    public function getImage($companyID)
    {
        $result = $this->where('CMP_ID', $companyID)->value('SEAL');
        return $result ?? null;
    }

    public function sealDelete($companyID)
    {
        $company = $this->find($companyID);
        if ($company) {
            $company->SEAL = null;
            return $company->save();
        }
        return false;
    }

    public function getSealFlg($companyID = 1)
    {
        return $this->where('CMP_ID', $companyID)->value('CMP_SEAL_FLG');
    }

}
