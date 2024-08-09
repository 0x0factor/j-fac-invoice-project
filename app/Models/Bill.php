<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Vendors\model\Form;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AddValidationRule;
use App\Traits\CustomValidation;
class Bill extends Model
{

    protected $table = 'T_BILL';
    protected $primaryKey = 'MBL_ID';

    protected $fillable = [
        'NO',
        'DATE',
        'EIGHT_RATE_TOTAL',
        'REDUCED_RATE_TAX',
        'REDUCED_RATE_TOTAL',
        'TEN_RATE_TAX',
        'TEN_RATE_TOTAL',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'CST_ID');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    public function updateUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UPDATE_USR_ID');
    }

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class, 'CHR_ID');
    }

    protected $rules = [
        'SUBJECT' => [
            'rule0' => ['rule' => 'spaceOnly', 'message' => 'スペース以外も入力してください'],
            'rule1' => ['rule' => 'required', 'message' => '件名は必須項目です'],
            'rule2' => ['rule' => 'max:40', 'message' => '件名が長すぎます'],
        ],
        'FEE' => [
            'rule0' => ['rule' => 'max:20', 'message' => '振込手数料が長すぎます'],
        ],
        'CST_ID' => [
            'rule1' => ['rule' => 'required', 'message' => '企業名は必須項目です'],
            'rule2' => ['rule' => 'numeric', 'message' => '企業名は必須項目です'],
        ],
        'CHRC_ID' => [
            'rule2' => ['rule' => 'numeric', 'message' => '数字以外は入力できません'],
        ],
        'NOTE' => [
            'rule0' => ['rule' => 'max:300', 'message' => '備考が長すぎます'],
            'rule1' => ['rule' => 'maxLines:6,55', 'message' => '行数または文字数が多すぎます'],
        ],
        'NO' => [
            'rule0' => ['rule' => 'max:20', 'message' => '納品書番号が長すぎます'],
            'rule1' => ['rule' => 'manageNumber', 'message' => '使用できない文字が含まれています'],
        ],
        'MEMO' => [
            'rule0' => ['rule' => 'max:50', 'message' => 'メモが長すぎます'],
        ],
        'DATE' => [
            'rule0' => ['rule' => 'date', 'message' => '有効な日付ではありません'],
        ],
        'DUE_DATE' => [
            'rule0' => ['rule' => 'max:20', 'message' => '支払い期日が長すぎます'],
        ],
        'DISCOUNT' => [
            'rule0' => ['rule' => 'max:15', 'message' => '割引が長すぎます'],
            'rule1' => ['rule' => 'numeric', 'message' => '割引は数字のみです'],
        ],
        'TAX_FRACTION_TIMING' => [
            'rule0' => ['rule' => 'taxFractionTiming', 'message' => '選択できるのは明細単位のみです'],
        ],
        'HONOR_TITLE' => [
            'rule0' => ['rule' => 'max:4', 'message' => '敬称が長すぎます'],
        ],
    ];

    public function rules(): array
    {
        return $this->rules;
    }
    /**
     * Index delete method
     *
     * @param array $_param
     * @return boolean
     */
    public function indexDelete($_param)
    {
        $form = new Form();
        return $form->Delete_Replication_Data($_param, 'T_BILL', 'MBL_ID');
    }

    /**
     * Get checked items
     *
     * @param array $_param
     * @param boolean $autoSerial
     * @param string $modelFrom
     * @return array|boolean
     */
    public function reproduceCheck($_param, $autoSerial = true, $modelFrom = 'T_BILL')
    {
        $form = new Form();
        unset($_param['T_BILL']['STATUS_CHANGE']);

        // Check if any copy item is not numeric
        foreach ($_param['T_BILL'] as $key => $val) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        // Organize copy item IDs
        $form->Sort_Replication_ID($_param, 'T_BILL');
        if (!$_param) {
            return false;
        }

        // Retrieve copy data
        $_param = $this->whereIn('MBL_ID', explode(',', implode(',', $_param)))
            ->get()
            ->toArray();
        if (!$_param) {
            return false;
        }

        // Get copy item information
        $form->getReplicationItem($_param, 'T_BILL', $autoSerial, $modelFrom);
        if (!$_param) {
            return false;
        }

        return $_param;
    }

    /**
     * Replicate report process
     *
     * @param array $_param
     * @param integer $_userId
     * @return boolean
     */
    public function insertReproduce($_param, $_userId)
    {
        $form = new Form();

        $tableBefore = "Table";
        $tableAfter = "T_BILL";

        $itemBefore = "Item";
        $itemAfter = "T_BILL_ITEM";

        // Organize copy item data
        $form->Sort_Replication_Data($_param, $tableBefore, $tableAfter, $itemBefore, $itemAfter);
        if (!$_param) {
            return false;
        }

        return $form->Copy_Replication_Data($_param, 'T_BILL', 'MBL_ID', $itemAfter, $_userId);
    }

    /**
     * Get decimal information
     *
     * @param integer $_companyId
     * @return array
     */
    public function getDecimal($_companyId)
    {
        $form = new Form();
        return $form->Get_Decimal($_companyId);
    }

    /**
     * Get customer honorific
     *
     * @param integer $_companyId
     * @return array
     */
    public function getHonor($_companyId)
    {
        $form = new Form();
        return $form->Get_Honor($_companyId);
    }

    /**
     * Get serial setting information
     *
     * @param integer $_companyId
     * @return array
     */
    public function getSerial($_companyId)
    {
        $form = new Form();
        return $form->Get_Serial($_companyId);
    }

    /**
     * Get invoice
     *
     * @param integer $_billId
     * @param integer|null $count
     * @return array
     */
    public function editSelect($_billId, &$count = null)
    {
        $form = new Form();
        return $form->Edit_Select($_billId, 'Bill', 'MBL_ID', $count);
    }

    /**
     * Get customer information
     *
     * @param integer $_companyId
     * @param array|null $_condition
     * @return array
     */
    public function getCustomer($_companyId, $_condition = null)
    {
        $form = new Form();
        return $form->Get_Customer($_companyId, $_condition);
    }
    public function getPayment($companyId)
    {
        $form = new Form();
        return $form->Get_Payment($companyId);
    }

    /**
     * Get the company payment information
     *
     * @param int $companyId
     * @return bool|array
     */
    public function getCompanyPayment($companyId)
    {
        $form = new Form();
        return $form->Get_Company_Payment($companyId);
    }

    /**
     * Save the data
     *
     * @param array $params
     * @param string|null $state
     * @param array $error
     * @return bool
     */
    public function setData($params, $state = null, $error)
    {
        $this->fill($params);
        if (!$this->validate()) {
            return false;
        }

        $form = new Form();
        return $form->Set_Replication_Data($params, 'Bill', $state, $error);
    }

    /**
     * Get the preview data
     *
     * @param int $billId
     * @param array|null $items
     * @param array|null $discounts
     * @return bool|array
     */
    public function previewData($billId, &$items = null, &$discounts = null)
    {
        $form = new Form();
        return $form->Get_Preview_Data($billId, 'T_BILL', $items, $discounts);
    }

    /**
     * Send the email
     *
     * @param array $params
     * @return bool
     */
    public function sendMail($params)
    {
        $form = new Form();
        return $form->Send_Mail($params, 'T_BILL');
    }

    /**
     * Get the mail parameters
     *
     * @param int $billId
     * @param float|null $customerCharge
     * @param float|null $charge
     * @return bool|array
     */
    public function getMailParams($billId, &$customerCharge = null, &$charge = null)
    {
        $form = new Form();
        return $form->Get_Mail_Param($billId, 'T_BILL', $customerCharge, $charge);
    }

    /**
     * Export to Excel
     *
     * @param array $params
     * @param array $error
     * @param string $type
     * @param mixed $userAuth
     * @param mixed $userId
     * @return bool
     */
    public function export($params, &$error, $type = 'term', $userAuth = null, $userId = null)
    {
        $form = new Form();
        return $form->Export_Excel('T_BILL', $params, $error, $type, $userAuth, $userId);
    }

    /**
     * Get the user data
     *
     * @param int $id
     * @return mixed
     */
    public function getUser($id)
    {
        $form = new Form();
        return $form->Get_User_Data('T_BILL', $id);
    }

    /**
     * Field definitions
     *
     * @var array
     */
    public $field = [
        1 => "日付",
        2 => "管理番号",
        3 => "取引先",
        4 => "件名",
        5 => "自社担当者",
        6 => "小計",
        7 => "消費税",
        8 => "合計",
        9 => "振込手数料",
        10 => "振込期限"
    ];

    /**
     * Validate the discount
     *
     * @param array $data
     * @return int|void
     */
    public function validateDiscount($data)
    {
        // Constants
        $discountCodeYen = 1;
        $discountCodeNone = 2;
        $discountCodeError = 3;
        $taxClassFree = 3;
        $taxClassInclusive = 1;
        $taxClassExclusive = 2;
        $lineAttrNormal = 0;

        // If no discount type, skip the check
        if ($discountCodeNone == $data['T_BILL']['DISCOUNT_TYPE']) {
            return;
        }

        unset($data['T_BILL']);
        unset($data['Security']);

        $prevTaxRate = null;
        foreach ($data as $key => $item) {
            if ($item['T_BILL_ITEM']['LINE_ATTRIBUTE'] != 0) {
                continue;
            }
            if ($item['T_BILL_ITEM']['TAX_CLASS'] == $taxClassFree) {
                continue;
            }

            $taxRate = intval($item['T_BILL_ITEM']['TAX_CLASS'] / 10);

            if (is_null($prevTaxRate)) {
                $prevTaxRate = $taxRate;
            } elseif ($prevTaxRate != $taxRate) {
                $this->addError('DISCOUNT', 'Discount is not valid');
                return $discountCodeError;
            }
        }
    }
}
