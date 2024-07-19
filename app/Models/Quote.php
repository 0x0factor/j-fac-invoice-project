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

/**
 * Content: Quote registration and edit model class
 */
class Quote extends Model
{
    protected $table = 't_quote';
    protected $primaryKey = 'MQT_ID';
    public $timestamps = false;
    // Validation

    protected $fillable = [
        'MQT_ID',
        'NO',
        'DATE',
        'SUBJECT',
        'CUSTOMER_NAME',
        'CST_ID',
        'CUSTOMER_CHARGE_NAME',
        'CHRC_ID',
        'CHARGE_NAME',
        'CHR_ID',
        'HONOR_CODE',
        'HONOR_TITLE',
        'CMP_SEAL_FLG',
        'CHR_SEAL_FLG',
        'DISCOUNT',
        'DISCOUNT_TYPE',
        'DECIMAL_QUANTITY',
        'DECIMAL_UNITPRICE',
        'DISCOUNT',
        'EXCISE',
        'TAX_FRACTION',
        'TAX_FRACTION_TIMING',
        'FRACTION',
        'SUBTOTAL',
        'SALES_TAX',
        'TOTAL',
        'STATUS',
        'DEADLINE',
        'DEAL',
        'DELIVERY',
        'DUE_DATE',
        'NOTE',
        'MEMO',
        'USR_ID',
        'UPDATE_USR_ID',
        'ISSUE_DATE',
        'INSERT_DATE',
        'LAST_UPDATE',
        'FIVE_RATE_TAX',
        'FIVE_RATE_TOTAL',
        'EIGHT_RATE_TAX',
        'EIGHT_RATE_TOTAL',
        'REDUCED_RATE_TAX',
        'REDUCED_RATE_TOTAL',
        'TEN_RATE_TAX',
        'TEN_RATE_TOTAL',
    ];

    protected $rules = [
        'SUBJECT' => [
            'rule0' => [
                'rule' => 'regex:/^\S+$/',
                'message' => 'スペース以外も入力してください',
            ],
            'rule1' => [
                'rule' => 'required',
                'message' => '件名は必須項目です',
            ],
            'rule2' => [
                'rule' => 'max:40',
                'message' => '件名が長すぎます',
            ],
        ],
        'DELIVERY' => [
            'rule0' => [
                'rule' => 'max:20',
                'message' => '納品場所が長すぎます',
            ],
        ],
        'CST_ID' => [
            'rule0' => [
                'rule' => 'numeric',
                'message' => '企業名は必須項目です',
            ],
            'rule1' => [
                'rule' => 'required',
                'message' => '企業名は必須項目です',
            ],
        ],
        'CHRC_ID' => [
            'rule2' => [
                'rule' => 'numeric',
                'message' => '数字以外は入力できません',
            ],
        ],
        'NOTE' => [
            'rule0' => [
                'rule' => 'max:300',
                'message' => '備考が長すぎます',
            ],
            'rule1' => [
                'rule' => 'max_lines:6,55',
                'message' => '行数または文字数が多すぎます',
            ],
        ],
        'NO' => [
            'rule0' => [
                'rule' => 'max:20',
                'message' => '納品書番号が長すぎます',
            ],
            'rule1' => [
                'rule' => 'regex:/^[a-zA-Z0-9_]+$/',
                'message' => '使用できない文字が含まれています',
            ],
        ],
        'MEMO' => [
            'rule0' => [
                'rule' => 'max:50',
                'message' => 'メモが長すぎます',
            ],
        ],
        'DATE' => [
            'rule0' => [
                'rule' => 'date',
                'message' => '有効な日付ではありません',
            ],
        ],
        'DUE_DATE' => [
            'rule0' => [
                'rule' => 'max:20',
                'message' => '有効期限が長すぎます',
            ],
        ],
        'DISCOUNT' => [
            'rule0' => [
                'rule' => 'max:15',
                'message' => '割引が長すぎます',
            ],
            'rule1' => [
                'rule' => 'numeric',
                'message' => '割引は数字のみです',
            ],
        ],
        'TAX_FRACTION_TIMING' => [
            'rule0' => [
                'rule' => 'in:line_item',
                'message' => '選択できるのは明細単位のみです',
            ],
        ],
        'HONOR_TITLE' => [
            'rule0' => [
                'rule' => 'max:4',
                'message' => '敬称が長すぎます',
            ],
        ],
        'DEADLINE' => [
            'rule0' => [
                'rule' => 'max:20',
                'message' => '納入期限が長すぎます',
            ],
        ],
        'DEAL' => [
            'rule0' => [
                'rule' => 'max:20',
                'message' => '取引方法が長すぎます',
            ],
        ],
    ];
    var $belongsTo = [
        'Customer' => [
            'className' => 'Customer',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'CST_ID'
        ],
        'User' => [
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'USR_ID'
        ],
        'UpdateUser' => [
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'UPDATE_USR_ID'
        ],
        'Charge' => [
            'className' => 'Charge',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'CHR_ID'
        ],
    ];

    var $hasOne =[
            'Quoteitem' => [
            'className' => 'Quoteitem',
            'type' => '',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'MQT_ID',
        ]
            ];

    var $virtualFields = [
        'MQT_ID' => 'Quote.MQT_ID',
        'CAST_TOTAL' => 'CAST(Quote.TOTAL AS SIGNED)'
    ];

    protected $order = [
        'MQT_ID DESC'
    ];

    protected $groupBy =[
        'MQT_ID'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CST_ID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    public function quoteItem()
    {
        return $this->hasOne(QuoteItem::class, 'MQT_ID');
    }

    public function getCastTotalAttribute()
    {
        return (int)$this->TOTAL;
    }

    public function scopeSearchColumns($query, array $searchData)
    {
        $searchColumnAry = [
            'MQT_ID' => 'Quote.MQT_ID',
            'NO' => 'Quote.NO',
            'ACTION_DATE_FROM' => 'Quote.ACTION_DATE_FROM',
            'ACTION_DATE_TO' => 'Quote.ACTION_DATE_TO',
            'SUBJECT' => 'Quote.SUBJECT',
            'STATUS' => 'Quote.STATUS',
            'NAME' => 'Customer.NAME',
            'USR_NAME' => 'User.NAME',
            'UPD_USR_NAME' => 'UpdateUser.NAME',
            'CHR_USR_NAME' => 'Charge.CHARGE_NAME',
            'ITEM_NAME' => 'Quoteitem.ITEM',
            'ITEM_CODE' => 'Quoteitem.ITEM_CODE',
            'NOTE' => 'Quote.NOTE',
            'MEMO' => 'Quote.MEMO',
            'TOTAL_FROM' => 'TOTAL_FROM',
            'TOTAL_TO' => 'TOTAL_TO',
        ];

        foreach ($searchData as $key => $value) {
            if (array_key_exists($key, $searchColumnAry)) {
                $query->where($searchColumnAry[$key], $value);
            }
        }

        return $query->orderBy('MQT_ID', 'desc')
                     ->groupBy('MQT_ID');
    }

    public function index_delete($param)
    {
        $form = new Form();
        return $form->Delete_Replication_Data($param, 'Quote', 'MQT_ID');
    }

    public function reproduce_check($param, $auto_serial = true, $model_from = 'Quote')
    {
        $form = new Form();

        unset($param['Quote']['STATUS_CHANGE']);

        // Check if any of the copy items are non-numeric
        foreach ($param['Quote'] as $key => $val) {
            if (!preg_match("/^[0-9]+$/", $key)) {
                return false;
            }
        }

        // Organize the IDs of the items to be copied
        $form->Sort_Replication_ID($param, 'Quote');
        if (!$param) {
            return false;
        }

        // Fetch the data to be copied
        $quotes = $this->whereIn('MQT_ID', explode(',', implode(',', $param)))->get();
        if (!$quotes) {
            return false;
        }

        // Get the information of the items to be copied
        $form->Get_Replication_Item($quotes, 'Quote', $auto_serial, $model_from);
        if (!$quotes) {
            return false;
        }

        return $quotes;
    }

    public function insert_reproduce($param, $userId)
    {
        $form = new Form();

        $tableBefore = "Table";
        $tableAfter = "Quote";
        $itemBefore = "Item";
        $itemAfter = "Quoteitem";

        // Organize the data of the items to be copied
        $form->Sort_Replication_Data($param, $tableBefore, $tableAfter, $itemBefore, $itemAfter);
        if (!$param) {
            return false;
        }

        return $form->Copy_Replication_Data($param, 'Quote', 'MQT_ID', $itemAfter, $userId);
    }

    public function edit_select($quoteId, &$count = null)
    {
        $form = new Form();
        return $form->Edit_Select($quoteId, 'Quote', 'MQT_ID', $count);
    }

    public function get_customer($companyId, $condition)
    {
        $form = new Form();
        return $form->Get_Customer($companyId, $condition);
    }

    public function get_decimal($companyId)
    {
        $form = new Form();
        return $form->Get_Decimal($companyId);
    }
    public function getHonor($company_id)
    {
        $form = new Form();
        return $form->Get_Honor($company_id);
    }

    /**
     * Get the customer payment information
     *
     * @param array $company_id
     * @return array
     */
    public function getPayment($company_id)
    {
        $form = new Form();
        return $form->Get_Payment($company_id);
    }

    /**
     * Get the company payment information
     *
     * @param array $company_id
     * @return array
     */
    public function getCompanyPayment($company_id)
    {
        $form = new Form();
        return $form->Get_Company_Payment($company_id);
    }

    /**
     * Get the serial number setting information
     *
     * @param array $company_id
     * @return array
     */
    public function getSerial($company_id)
    {
        $form = new Form();
        return $form->Get_Serial($company_id);
    }

    /**
     * Get the estimated amount for the last 3 months
     *
     * @return array
     */
    public function getAmountMonth()
    {
        // 2 months ago
        $monthBeforeLast = date('Y-m-1', strtotime(date('Y-m-1') . ' -2 month'));
        // Last month
        $lastMonth = date('Y-m-1', strtotime(date('Y-m-1') . ' -1 month'));
        // This month
        $thisMonth = date('Y-m-1');
        // Next month
        $nextMonth = date('Y-m-1', strtotime(date('Y-m-1') . ' +1 month'));

        // Get information for the last 3 months
        $param = [];
        $param['monthBeforeLast'] = $this->where('ISSUE_DATE', '>=', $monthBeforeLast)
                                        ->where('ISSUE_DATE', '<', $lastMonth)
                                        ->select('MQT_ID', 'EXCISE', 'FRACTION')
                                        ->get();
        $param['lastMonth'] = $this->where('ISSUE_DATE', '>=', $lastMonth)
                                    ->where('ISSUE_DATE', '<', $thisMonth)
                                    ->select('MQT_ID', 'EXCISE', 'FRACTION')
                                    ->get();
        $param['thisMonth'] = $this->where('ISSUE_DATE', '>=', $thisMonth)
                                    ->where('ISSUE_DATE', '<', $nextMonth)
                                    ->select('MQT_ID', 'EXCISE', 'FRACTION')
                                    ->get();

        // Load the Quoteitem model
        $Quoteitem = new Quoteitem();

        // Organize the data
        $param1 = [];
        foreach ($param as $key => $value) {
            $param1[$key] = null;
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    $param1[$key][$value1->MQT_ID]['EXCISE'] = $value1->EXCISE;
                    $param1[$key][$value1->MQT_ID]['FRACTION'] = $value1->FRACTION;
                }
            }
        }

        // Get the amount and count
        $quote = [];
        foreach ($param1 as $key => $value) {
            $quote[$key]['money'] = 0;
            $quote[$key]['count'] = 0;
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    $quote[$key]['count']++;
                    $tmp = $Quoteitem->where('QUANTITY', '!=', null)
                                    ->where('UNIT_PRICE', '!=', null)
                                    ->where('MQT_ID', $key1)
                                    ->select('QUANTITY', 'UNIT_PRICE')
                                    ->get();
                    if ($tmp) {
                        $total = 0;
                        foreach ($tmp as $key2 => $value2) {
                            $total += $value2->QUANTITY * $value2->UNIT_PRICE;
                        }
                        if ($value1['EXCISE'] == 1) {
                            $total = $total * 1.05;
                        }
                        $quote[$key]['money'] += $total;
                    }
                }
            }
        }

        return $quote;
    }

    /**
     * Save the data
     *
     * @param array $param
     * @param string|null $state
     * @param string $error
     * @return bool
     */
    public function setData($param, $state = null, $error)
    {
        $this->fill($param);
        if (!$this->isValid()) {
            return false;
        }

        $form = new Form();
        return $form->Set_Replication_Data($param, 'Quote', $state, $error);
    }
    public function preview_data($quote_ID, &$items = null, &$discounts = null)
    {
        $form = new Form();
        return $form->Get_Preview_Data($quote_ID, 'Quote', $items, $discounts);
    }

    public function send_mail($param)
    {
        $form = new Form();
        return $form->Send_Mail($param, 'Quote');
    }

    public function get_for_mail_param($quote_id, &$customer_charge = null, &$charge = null)
    {
        $form = new Form();
        return $form->Get_Mail_Param($quote_id, 'Quote', $customer_charge, $charge);
    }

    // EXCEL形式での出力用
    public function export($param, &$error, $type = 'term', $user_auth = null, $user_id = null)
    {
        $form = new Form();
        return $form->Export_Excel('Quote', $param, $error, $type, $user_auth, $user_id);
    }

    public function get_user($id)
    {
        $form = new Form();
        return $form->Get_User_Data('Quote', $id);
    }

    // 割引(円)の項目をエラーにする
    public function validateDiscount($data)
    {
        // TODO 定数を持ってないので、既存のものを含めどこかで定義しなおす
        $DiscountCodeYen = 1;
        $DiscountCodeNone = 2;
        $DiscountCodeError = 3;
        $TaxClassFree = 3;
        $TaxClassInclusive = 1;
        $TaxClassExclusive = 2;
        $LineAttrNormal = 0;

        // 設定なしの場合はチェックを行わない
        if ($DiscountCodeNone == $data["Quote"]["DISCOUNT_TYPE"]) {
            return;
        }

        unset($data["Quote"]);
        unset($data["Security"]);

        $prevTaxRate = null;
        foreach ($data as $key => $item) {
            if ($item["Quoteitem"]["LINE_ATTRIBUTE"] != 0) continue;
            if ($item["Quoteitem"]["TAX_CLASS"] == $TaxClassFree) continue;
            $taxRate = intval($item["Quoteitem"]["TAX_CLASS"] / 10);
            if (is_null($prevTaxRate)) {
                $prevTaxRate = $taxRate;
            } elseif ($prevTaxRate != $taxRate) {
                $this->invalidate("DISCOUNT");
                return $DiscountCodeError;
            }
        }
    }
}
