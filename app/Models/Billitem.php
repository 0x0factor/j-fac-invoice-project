<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billitem extends Model
{
    // Set the table associated with the model
    protected $table = 'T_BILL_ITEM';

    // Set the primary key
    protected $primaryKey = 'ITM_ID';

    // Optionally, you can disable timestamps if your table does not have created_at and updated_at columns
    public $timestamps = false;
}
