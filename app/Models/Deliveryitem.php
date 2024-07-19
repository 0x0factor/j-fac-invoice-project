<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deliveryitem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_DELIVERY_ITEM';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ITM_ID';

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
    protected $fillable = [];

    /**
     * Get the index for the model.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->getKey();
    }
}
