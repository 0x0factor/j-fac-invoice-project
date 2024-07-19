<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quoteitem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_QUOTE_ITEM';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ITM_ID';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'ITM_ID' => null,
    ];
}

