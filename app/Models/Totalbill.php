<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Vendors\model\Form;

class Totalbill extends Model
{
    use HasFactory;

    protected $table = 'T_TOTAL_BILL';
    protected $primaryKey = 'TBL_ID';
    public $timestamps = false;

    protected $fillable = [
        'TBL_ID', 'NO', 'DATE', 'SUBJECT', 'CST_ID', 'CUSTOMER_CHARGE_NAME',
        'CHRC_ID', 'CUSTOMER_CHARGE_UNIT', 'CHR_ID', 'HONOR_CODE', 'HONOR_TITLE',
        'DUE_DATE', 'SUBTOTAL', 'SALE_TAX', 'THISM_BILL', 'EDIT_STAT', 'TOTAL',
        'LASTM_BILL', 'DEPOSIT', 'CARRY_BILL', 'SALE', 'USR_ID', 'UPDATE_USR_ID',
        'ISSUE_DATE', 'INSERT_DATE', 'LAST_UPDATE'
    ];

    protected $casts = [
        'THISM_BILL' => 'integer',
    ];

    // Validation rules
    public static $rules = [
        'SUBJECT' => ['required', 'string', 'not_only_spaces', 'max:40'],
        'FEE' => ['max:20'],
        'CST_ID' => ['required', 'numeric'],
        'NO' => ['max:20', 'not_allowed_characters'],
        'DATE' => ['date'],
        'DUE_DATE' => ['max:20'],
        'LASTM_BILL' => ['max:15', 'numeric', 'nullable'],
        'DEPOSIT' => ['max:15', 'numeric', 'nullable'],
        'CARRY_BILL' => ['max:15', 'numeric', 'nullable'],
        'SALE' => ['max:15', 'numeric', 'nullable'],
        'SALE_TAX' => ['max:15', 'numeric', 'nullable'],
        'THISM_BILL' => ['max:15', 'numeric', 'nullable'],
        'SUBTOTAL' => ['max:15', 'numeric', 'nullable'],
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CST_ID', 'CST_ID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID', 'USR_ID');
    }

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'UPDATE_USR_ID', 'USR_ID');
    }

    // Methods
    public function set_data($param, $state = null)
    {
        if ($state == 'new') {
            $param['insert_date'] = now();
        }
        $param['last_update'] = now();
        $param['issue_date'] = $param['date'];

        DB::beginTransaction();

        try {
            $this->fill($param)->save();

            if (!isset($param['tbl_id'])) {
                $param['tbl_id'] = $this->tbl_id;
            }

            Totalbillitem::where('tbl_id', $param['tbl_id'])->delete();

            $items = [];
            foreach ($param as $key => $val) {
                if (is_numeric($key)) {
                    $items[] = [
                        'tbl_id' => $param['tbl_id'],
                        'mbl_id' => $val['totalbillitem']['mbl_id'],
                        'insert_date' => ($state == 'new') ? now() : null,
                        'last_update' => now()
                    ];
                }
            }

            Totalbillitem::insert($items);

            DB::commit();
            return $param['tbl_id'];
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    public function index_delete($param)
    {
        $ids = [];

        foreach ($param['totalbill'] as $key => $value) {
            if ($value == 1) {
                $ids[] = $key;
            }
        }

        if (!empty($ids)) {
            return Totalbill::whereIn('tbl_id', $ids)->delete();
        } else {
            return false;
        }
    }

    public function edit_select($_tbl_id)
    {
        return Totalbill::select([
                'subject',
                'no',
                'due_date',
                'issue_date',
                'honor_code',
                'honor_title',
                'chrc_id',
                'lastm_bill',
                'deposit'
            ])
            ->where('tbl_id', $_tbl_id)
            ->first();
    }

    public function check_select($_tbl_id)
    {
        return Totalbill::where('tbl_id', $_tbl_id)->first();
    }

    public function get_bill($_tbl_id)
    {
        $items = Totalbillitem::where('tbl_id', $_tbl_id)->get();
        $result = [];

        foreach ($items as $item) {
            $bill = Bill::where('mbl_id', $item->mbl_id)->first();

            if ($bill) {
                $bill->CHK = 1;
                $result[] = $bill;
            }
        }

        return $result;
    }

    public function get_bill_id($_tbl_id)
    {
        return Totalbillitem::where('tbl_id', $_tbl_id)->get();
    }

    public function get_serial($_company_id)
    {
        // Assuming FormModel is a custom class handling form data, adjust as per your application's structure
        $form = new Form();
        return $form->Get_Serial($_company_id);
    }

    public function preview_data($_tbl_id)
    {
        $result = Totalbill::select('*')->where('tbl_id', $_tbl_id)->first();

        if ($result) {
            $company = Company::index_select(1);
            $result = array_merge($result->toArray(), $company->toArray());

            $charge = Charge::where('chr_id', $result['chrc_id'])->first();
            $result['charge']['seal'] = ($charge) ? $charge->get_image() : null;
        }

        return $result;
    }

    public function get_cstmer($_tbl_id)
    {
        return Totalbill::select('customers.cst_id', 'customers.name')
            ->join('customers', 'totalbills.cst_id', '=', 'customers.cst_id')
            ->where('tbl_id', $_tbl_id)
            ->first();
    }

    public function get_edit_stat($_tbl_id)
    {
        return Totalbill::where('tbl_id', $_tbl_id)->value('edit_stat');
    }

    public function get_user_id($_tbl_id)
    {
        return Totalbill::where('tbl_id', $_tbl_id)->value('usr_id');
    }

    public function search_bill($param, $user_id = null)
    {
        $query = Bill::groupBy('mbl_id');

        if (isset($param['totalbill']['from']) && $param['totalbill']['from']) {
            $query->where('issue_date', '>=', $param['totalbill']['from']);
        }

        if (isset($param['totalbill']['to']) && $param['totalbill']['to']) {
            $query->where('issue_date', '<=', $param['totalbill']['to']);
        }

        if (isset($param['totalbill']['cst_id']) && $param['totalbill']['cst_id'] != 0) {
            $query->where('cst_id', $param['totalbill']['cst_id']);
        }

        if ($user_id !== null) {
            $query->where('usr_id', $user_id);
        }

        return $query->where('status', 1)->get();
    }
}
