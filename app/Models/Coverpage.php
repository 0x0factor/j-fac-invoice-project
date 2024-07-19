<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coverpage extends Model
{
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
    protected $fillable = [
        'CUSTOMER_NAME', 'CUSTOMER_CHARGE_NAME', 'CUSTOMER_CHARGE_UNIT',
        'CHARGE_NAME', 'DATE', 'UNIT'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'DATE' => 'date',
    ];

    /**
     * Validation rules for the Coverpage model.
     *
     * These rules can be enforced using Laravel's validation system,
     * typically in a FormRequest class or within controller methods.
     *
     * @var array
     */
    public static $rules = [
        'CUSTOMER_NAME' => [
            'required',
            'string',
            'max:255',
            'regex:/[^\s]/', // Ensures there is at least one non-space character
        ],
        'CUSTOMER_CHARGE_NAME' => [
            'nullable',
            'string',
            'max:255',
            'regex:/[^\s]/', // Ensures there is at least one non-space character
        ],
        'CUSTOMER_CHARGE_UNIT' => [
            'nullable',
            'string',
            'max:255',
            'regex:/[^\s]/', // Ensures there is at least one non-space character
        ],
        'CHARGE_NAME' => [
            'nullable',
            'string',
            'max:255',
            'regex:/[^\s]/', // Ensures there is at least one non-space character
        ],
        'DATE' => [
            'required',
            'date',
        ],
        'UNIT' => [
            'nullable',
            'string',
            'max:250',
        ],
    ];
}
