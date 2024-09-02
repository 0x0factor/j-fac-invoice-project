<?php
/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

namespace App\Vendors\model;

use App\Models\Company;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Customer;
use App\Models\Charge;
use App\Models\Serial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class Form
{
	//帳票の共通の処理はこちらで行う


	/*
	 *
	 */
	public function Sort_Replication_ID(&$_param, $modelName)
	{

		// $param = array();
        // dd($_param);

		// if(is_array($_param)){
		// 	//複製する項目をピックアップ
		// 	foreach($_param[$modelName] as $key => $value){
		// 		if($value == 1){
        //             $param[$key] = $value;
		// 		}

        //         $_param = $param;
		// 	}
        //     dd($_param);
		// }
        $param = $_param[$modelName];
	}

	/*
	 *
	 */
	public function Sort_Replication_Data(&$_param, $_Table_before, $_Table_after, $_Item_before, $_Item_after)
	{
		//情報整理
		if(is_array($_param))
		{
			foreach($_param as $key => $value)
			{
				if(is_array($value))
				{
					foreach($value as $key1 => $value1)
					{
						if($key1 === $_Table_before)
						{
							$_param[$key][$_Table_after] = $value1;
							unset($_param[$key][$_Table_before]);
						}
						if(is_array($value1))
						{
							foreach($value1 as $key2 => $value2)
							{
								if($key2 === $_Item_before)
								{
									$_param[$key][$key1][$_Item_after] = $value2;
									unset($_param[$key][$key1][$_Item_before]);
								}
							}
						}
					}
				}
			}
		}
		else
		{
			return false;
		}
	}

	/*
	 *
	 */
	public function Get_Replication_Item(&$_param, $modelName, $auto_serial = true, $model_from)
	{
		$Model = null;

        switch ($modelName) {
            case 'Quote':
                $Model = new QuoteItem();
                break;
            case 'Bill':
                $Model = new BillItem();
                break;
            case 'Delivery':
                $Model = new DeliveryItem();
                break;
        }

		if($auto_serial) {
			if(empty($model_from)) {
				$model_from = $modelName;
			}
			$Serial = new Serial;

		}

        if (!$Model) return $_param = null;


        foreach ($_param as $key => $value) {
            // Initialize status
            $_param[$key]['STATUS'] = 0;

            // Set timestamps
            $_param[$key]['INSERT_DATE'] = now();
            $_param[$key]['LAST_UPDATE'] = now();

            // Append subject
            $_param[$key]['SUBJECT'] = $value['SUBJECT'] . 'のコピー';


            // Set serial number
            if ($auto_serial) {
                $_param[$key]['NO'] = $Serial->get_number($model_from);
                $Serial->serial_increment($modelName);
            }

            $_param[$key]['Table'] = $_param[$key];

            if ($modelName === 'Quote') {
                $items = $Model->where('MQT_ID', $_param[$key]['MQT_ID'])->get()->toArray();
            } elseif ($modelName === 'Bill') {
                $items = $Model->where('MBL_ID', $_param[$key]['MBL_ID'])->get()->toArray();
            } elseif ($modelName === 'Delivery') {
                $items = $Model->where('MDV_ID', $_param[$key]['MDV_ID'])->get()->toArray();
            }

            if (is_array($items)) {
                foreach ($items as $key1 => $value) {
                    // $items[$key1]['Item'] = $items[$key1][$modelName . 'item'];
                    $items[$key1]['Item'] = $items[$key1]['ITEM'];
                    // unset($items[$key1][$modelName . 'item']);
                    unset($items[$key1]['ITEM']);
                }
            }


            if ($_param[$key] instanceof Quote) {
                $_param[$key] = $_param[$key]->toArray();
            }

            // Ensure $items is an array
            if (!is_array($items)) {
                $items = [];
            }

            // Merge arrays

            // dd($_param[$key], $items[$key]);
            $_param[$key] = array_merge($_param[$key], $items[$key]);

            // $_param[$key] = array_merge($_param[$key], $items);

            // Remove IDs
            if ($modelName === 'Quote') {
                foreach ($_param as $key => $item) {
                    if (isset($item['Table'])) {
                        unset($item['Table']['MQT_ID']);
                        $_param[$key] = $item; // re-assign modified item back to the collection
                    }
                }
                // unset($_param[$key]['Table']['MQT_ID']);
            }
            if ($modelName === 'Bill') {
                unset($_param[$key]['Table']['MBL_ID']);
            }
            if ($modelName === 'Delivery') {
                unset($_param[$key]['Table']['MDV_ID']);
            }
            // unset($_param[$key][$modelName]);
            // unset($_param[$key]['Customer']);

            // Unset the modelName key if it exists
            if (isset($item[$modelName])) {
                unset($item[$modelName]);
            }

            // Unset the Customer key if it exists
            if (isset($item['Customer'])) {
                unset($item['Customer']);
            }


        }
    }
	/*
	 *
	 */
	/*
     * Copy Replication Data
     */
    public function Copy_Replication_Data($_copy_param, $_model_Name, $_primary_key, $_Item_after, $_user_id = null)
    {
        // Load the respective models
        $Model = null;
        $ItemModel = null;

        if ($_model_Name === 'Quote') {
            $Model = new Quote();
            $ItemModel = new QuoteItem();
        } elseif ($_model_Name === 'Bill') {
            $Model = new Bill();
            $ItemModel = new BillItem();
        } elseif ($_model_Name === 'Delivery') {
            $Model = new Delivery();
            $ItemModel = new DeliveryItem();
        }

        $id = null;
        // Start transaction
        DB::beginTransaction();


        $modelInstance = $Model->create($_copy_param[0]);
        dd($_copy_param[0]['USR_ID'], $_copy_param[0]['UPDATE_USR_ID'], $modelInstance);

        try {

            foreach ($_copy_param as $key => $value) {
                $_copy_param[$key]['USR_ID'] = $_user_id;
                $_copy_param[$key]['UPDATE_USR_ID'] = $_user_id;
                $modelInstance = $Model->create($_copy_param[$key]);

                if ($modelInstance) {
                    // Get the inserted ID
                    $id = $modelInstance->id;

                    $items = [];
                    for ($i = 0; $i < count($_copy_param[$key]) - 3; $i++) {
                        unset($_copy_param[$key][$i][$_Item_after]['ITM_ID']);
                        if (!empty($_copy_param[$key][$i][$_Item_after])) {
                            $items[$i] = $_copy_param[$key][$i];
                            $items[$i][$_Item_after][$_primary_key] = $id;
                        }
                    }

                    if (!$ItemModel->insert($items)) {
                        // Rollback on error
                        DB::rollBack();
                        return false;
                    }
                } else {
                    // Rollback on error
                    DB::rollBack();
                    return false;
                }
            }


            // Commit transaction
            DB::commit();
            return $id;
        } catch (\Exception $e) {
            // Rollback on exception
            DB::rollBack();
            return false;
        }
    }

	/*
	 *
	 */
	public function Delete_Replication_Data($_del_param, $_model_Name, $_primary_key, $_user_id = null)
    {
        $Model = null;
        if ($_model_Name === 'Quote') {
            $Model = new Quote();
        } elseif ($_model_Name === 'Bill') {
            $Model = new Bill();
        } elseif ($_model_Name === 'Delivery') {
            $Model = new Delivery();
        }

        if (!$Model) return false;

        $param = [];
        $ids = [];

        $_primary_key = $_model_Name . '.' . $_primary_key;
        // Pick up items to delete
        if (is_array($_del_param)) {
            foreach ($_del_param[$_model_Name] as $key => $value) {
                if ($value == 1) {
                    $data = [$_primary_key => $key];
                    $param[][$_model_Name] = $data;
                    array_push($ids, $data[$_primary_key]);
                }
            }
        }

        if ($param) {
            // Delete process
            return $Model->whereIn($_primary_key, $ids)->delete();
        } else {
            return false;
        }
    }

    /*
     * Get Decimal
     */
    public function Get_Decimal($_company_id)
    {
        $result = Company::where('CMP_ID', $_company_id)->get(['DECIMAL_QUANTITY', 'DECIMAL_UNITPRICE']);

        if (!$result) return false;

        return $result;
    }

    /*
     * Get Honor
     */
    public function Get_Honor($_company_id)
    {
        $result = Company::where('CMP_ID', $_company_id)->get(['HONOR_CODE', 'HONOR_TITLE']);

        if (!$result) return false;

        return $result;
    }

    /*
     * Get Serial
     */
    public function Get_Serial($_company_id)
    {
        $result = Company::where('CMP_ID', $_company_id)->get(['SERIAL_NUMBER']);

        if (!$result) return false;

        return $result[0]->SERIAL_NUMBER;
    }

    /*
     * Edit Select
     */
    public function Edit_Select($_model_ID, $_model_Name, $_primary_key, &$count = null)
    {
        $Model = null;
        $ItemModel = null;

        if ($_model_Name === 'Quote') {
            $Model = new Quote();
            $ItemModel = new QuoteItem();
        } elseif ($_model_Name === 'Bill') {
            $Model = new Bill();
            $ItemModel = new BillItem();
        } elseif ($_model_Name === 'Delivery') {
            $Model = new Delivery();
            $ItemModel = new DeliveryItem();
        }

        if (!is_numeric($_model_ID)) {
            return false;
        }

        $result = $Model->where($_primary_key, $_model_ID)->first();
        if (!$result) {
            return false;
        }

        $result->DATE = $result->ISSUE_DATE;

        // Check for multiple tax types
        $every_tax_total_key = ['FIVE_RATE_TOTAL', 'EIGHT_RATE_TOTAL', 'REDUCED_RATE_TOTAL', 'TEN_RATE_TOTAL'];
        $tax_kind_count = 0;
        foreach ($every_tax_total_key as $key) {
            if ($result[$key]) {
                $tax_kind_count++;
            }
        }
        $result->tax_kind_count = $tax_kind_count;

        // Load item model
        $items = $ItemModel->where($_primary_key, $_model_ID)->orderBy('ITM_ID', 'ASC')->get()->toArray();
        $count = count($items);
        $result = array_merge($result->toArray(), $items);
        return $result;
    }

    /*
     * Get Customer
     */
    public function Get_Customer($_company_id, $_condition)
    {
        $results = Customer::where($_condition)->get(['CST_ID', 'NAME']);

        $customer['customer'] = '＋顧客追加＋';
        $customer['default'] = '＋顧客選択＋';

        if (!$results) return $customer;

        foreach ($results as $result) {
            $customer[$result->CST_ID] = $result->NAME;
        }
        return $customer;
    }
	/*
	 *
	 */
	public function Get_Payment($_company_id)
    {
        $result = Customer::where('CMP_ID', $_company_id)
            ->get(['CST_ID', 'EXCISE', 'FRACTION', 'TAX_FRACTION', 'TAX_FRACTION_TIMING'])
            ->toArray();

        if (empty($result)) return false;

        $param = [];
        foreach ($result as $value) {
            $param[$value['CST_ID']] = $value;
        }

        return $param;
    }

    public function Get_Company_Payment($_company_id)
    {
        $result = Company::where('CMP_ID', $_company_id)
            ->first(['EXCISE', 'FRACTION', 'TAX_FRACTION', 'TAX_FRACTION_TIMING']);

        if (!$result) return false;

        return $result->toArray();
    }

    public function Set_Replication_Data($_set_param, $_model_Name, $_state, $_error)
    {
        $Model = null;
        $ItemModel = null;
        $itemModel_Name = null;
        $_primary_key = null;

        if ($_model_Name === 'Quote') {
            $Model = new Quote();
            $ItemModel = new QuoteItem();
            $itemModel_Name = 'Quoteitem';
            $_primary_key = 'MQT_ID';
        } elseif ($_model_Name === 'Bill') {
            $Model = new Bill();
            $ItemModel = new BillItem();
            $itemModel_Name = 'Billitem';
            $_primary_key = 'MBL_ID';
        } elseif ($_model_Name === 'Delivery') {
            $Model = new Delivery();
            $ItemModel = new DeliveryItem();
            $itemModel_Name = 'Deliveryitem';
            $_primary_key = 'MDV_ID';
        }

        dd($_set_param);
        // $_set_param[$_model_Name]['ISSUE_DATE'] = $_set_param[$_model_Name]['DATE'];
        $_set_param['data'][$_model_Name]['DATE'] = $_set_param['data'][$_model_Name]['DATE'];

        if ($_state === "new") {
            $_set_param[$_model_Name]['INSERT_DATE'] = Carbon::now();
        }
        $_set_param[$_model_Name]['LAST_UPDATE'] = Carbon::now();

        // Begin transaction
        DB::beginTransaction();


        if ($Model->fill($_set_param[$_model_Name])->save()) {
            if ($_state === "new") {
                $_set_param[$_model_Name][$_primary_key] = $Model->id;
            }

            if (!$ItemModel::where($_primary_key, $_set_param[$_model_Name][$_primary_key])->delete()) {
                DB::rollBack();
                return false;
            }

            $item = [];
            for ($i = 0; isset($_set_param[$i]); $i++) {
                $item[$i] = $_set_param[$i];
                $item[$i][$itemModel_Name][$_primary_key] = $_set_param[$_model_Name][$_primary_key];
                $item[$i][$itemModel_Name]['INSERT_DATE'] = Carbon::now();
                $item[$i][$itemModel_Name]['LAST_UPDATE'] = Carbon::now();
            }

            if ($_error['ITEM']['FLAG'] == 0 &&
                $_error['ITEM_NO']['FLAG'] == 0 &&
                $_error['QUANTITY']['FLAG'] == 0 &&
                $_error['UNIT']['FLAG'] == 0 &&
                $_error['UNIT_PRICE']['FLAG'] == 0 &&
                $_error['DISCOUNT'] == 0) {
                if ($ItemModel::insert($item)) {
                    DB::commit();
                    return $_set_param[$_model_Name][$_primary_key];
                } else {
                    DB::rollBack();
                    return false;
                }
            } else {
                DB::rollBack();
                return false;
            }
        } else {
            DB::rollBack();
            return false;
        }
    }

    public function Get_Preview_Data($_model_id, $_model_Name, &$_items = null, &$_discounts = null)
    {
        $Model = null;
        $itemModel_Name = null;

        if ($_model_Name === 'Quote') {
            $Model = new Quote();
            $itemModel_Name = 'Quoteitem';
        } elseif ($_model_Name === 'Bill') {
            $Model = new Bill();
            $itemModel_Name = 'Billitem';
        } elseif ($_model_Name === 'Delivery') {
            $Model = new Delivery();
            $itemModel_Name = 'Deliveryitem';
        }

        $_company_ID = 1;

        $result = $Model->edit_select($_model_id);

        if (!$result) return false;

        $subtotal = 0;

        $count = 0;
        $_discounts = 0;

        foreach ($result as $key => $value) {
            if (isset($value[$itemModel_Name]) && is_array($value[$itemModel_Name])) {
                $subtotal += $result[$key][$itemModel_Name]['AMOUNT'];
                if ($value[$itemModel_Name]['DISCOUNT']) {
                    $subtotal -= $value[$itemModel_Name]['DISCOUNT_TYPE'] == 0 ? $value[$itemModel_Name]['QUANTITY'] * $value[$itemModel_Name]['UNIT_PRICE'] * $value[$itemModel_Name]['DISCOUNT'] * 0.01 : $value[$itemModel_Name]['DISCOUNT'];
                    $_discounts++;
                }
                $count++;
            }
        }

        $_items = $count;

        $Company = new Company();
        $result = array_merge($result, $Company->index_select($_company_ID));

        if (!empty($result[$_model_Name]['CHR_ID'])) {
            $Charge = new Charge();
            $charge = $Charge::where('CHR_ID', $result[$_model_Name]['CHR_ID'])->first();
            $result = array_merge($result, $charge->toArray());
        } elseif ($result['Customer']["CHR_ID"]) {
            $Charge = new Charge();
            $charge = $Charge::where('CHR_ID', $result['Customer']["CHR_ID"])->first();
            $result = array_merge($result, $charge->toArray());
        }

        return $result;
    }

    public function Export_Excel($_model_Name, $_excel_param, &$error, $_type, $_user_auth = null, $_user_id = null)
    {
        $Model = null;
        $_primary_key = null;

        if ($_model_Name === 'Quote') {
            $Model = new Quote();
            $_primary_key = 'MQT_ID';
        } elseif ($_model_Name === 'Bill') {
            $Model = new Bill();
            $_primary_key = 'MBL_ID';
        } elseif ($_model_Name === 'Delivery') {
            $Model = new Delivery();
            $_primary_key = 'MDV_ID';
        }

        $excel_param = [];

        if ($_type === 'term') {
            if (!$_excel_param) {
                return false;
            }

            $date1 = $_excel_param['DATE1']['year'] . "-" . $_excel_param['DATE1']['month'] . "-" . $_excel_param['DATE1']['day'];
            $date2 = $_excel_param['DATE2']['year'] . "-" . $_excel_param['DATE2']['month'] . "-" . $_excel_param['DATE2']['day'];

            if (Carbon::createFromFormat('Y-m-d', $date1) && Carbon::createFromFormat('Y-m-d', $date2)) {
                if ($_user_auth != 1) {
                    $data = $Model::whereBetween('ISSUE_DATE', [$date1, $date2])->groupBy($Model->groupBy)->get();
                } else {
                    $data = $Model::where('USR_ID', $_user_id)->whereBetween('ISSUE_DATE', [$date1, $date2])->groupBy($Model->groupBy)->get();
                }

                if ($data->isEmpty()) {
                    $error = "データがありません";
                    return false;
                }

                $Charge = new Charge();
                $count = 0;

                foreach ($data as $key1 => $value1) {
                    if ($count < 1000) {
                        if (is_array($value1->toArray())) {
                            foreach ($value1->toArray() as $key2 => $value2) {
                                if ($key2 === $_model_Name) {
                                    $excel_param[$key1][1] = date('Y年m月d日', strtotime($value2['ISSUE_DATE']));
                                    $excel_param[$key1][2] = $value2['NO'];
                                    $excel_param[$key1][4] = $value2['SUBJECT'];
                                    $excel_param[$key1][5] = $value2['CHR_ID'] == 0 ? null : $Charge->get_charge($value2['CHR_ID']);
                                    $excel_param[$key1][6] = $value2['SUBTOTAL'];
                                    $excel_param[$key1][7] = $value2['SALES_TAX'];
                                    $excel_param[$key1][8] = $value2['TOTAL'];
                                    if ($_model_Name === 'Quote') {
                                        $excel_param[$key1][9] = $value2['DELIVERY'];
                                        $excel_param[$key1][10] = $value2['DUE_DATE'];
                                    } elseif ($_model_Name === 'Bill') {
                                        $excel_param[$key1][9] = $value2['FEE'];
                                        $excel_param[$key1][10] = $value2['DUE_DATE'];
                                    } elseif ($_model_Name === 'Delivery') {
                                        $excel_param[$key1][9] = $value2['DELIVERY'];
                                    }
                                }
                                if ($key2 === "Customer") {
                                    $excel_param[$key1][3] = $value2['NAME'];
                                }
                                ksort($excel_param[$key1]);
                            }
                        }
                        $count++;
                    }
                }
            } else {
                $error = "日付が正しくありません";
                return false;
            }
        } else {
            $error = "データが空";
            return false;
        }

        return $excel_param;
    }

    public function Get_User_Data($_model_Name, $_id)
    {
        // Define an associative array mapping model names to class names and primary keys
        $models = [
            'Quote' => ['class' => Quote::class, 'primary_key' => 'MQT_ID'],
            'Bill' => ['class' => Bill::class, 'primary_key' => 'MBL_ID'],
            'Delivery' => ['class' => Delivery::class, 'primary_key' => 'MDV_ID']
        ];

        // Check if the model name exists in the array
        if (!array_key_exists($_model_Name, $models)) {
            // Handle the case where the model name is not recognized
            return false;
        }

        // Get the model class and primary key
        $modelClass = $models[$_model_Name]['class'];
        $primaryKey = $models[$_model_Name]['primary_key'];

        // Perform the query
        $result = $modelClass::where($primaryKey, $_id)->get(['USR_ID']);

        // Check if the result is empty and return false if it is
        if ($result->isEmpty()) {
            return false;
        }

        // Return the USER_ID from the result
        return $result[0]['USR_ID'];
    }
}
