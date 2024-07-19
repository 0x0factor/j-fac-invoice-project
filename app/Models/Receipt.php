<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = false;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'CST_ID' => '',
        'TOTAL' => 0,
        'DATE' => null,
        'RECEIPT_NUMBER' => '',
        'PROVISO' => '',
    ];

    /**
     * The model's validation rules.
     *
     * @var array
     */
    public static $rules = [
        'CST_ID' => [
            'spaceOnly' => 'required|regex:/\S/',
            'notEmpty' => 'required',
            'maxLengthW' => 'max:30',
        ],
        'TOTAL' => [
            'spaceOnly' => 'required|regex:/\S/',
            'notEmpty' => 'required',
            'maxLengthJP' => 'max:16',
        ],
        'DATE' => [
            'notEmpty' => 'required',
            'date' => 'date_format:Y-m-d', // Adjust date format as needed
        ],
        'RECEIPT_NUMBER' => [
            'notEmpty' => 'required',
            'maxLengthJP' => 'max:20',
            'manageNumber' => 'regex:/^[a-zA-Z0-9]*$/', // Define 'manageNumber' validation rule
        ],
        'PROVISO' => [
            'spaceOnly' => 'required|regex:/\S/',
            'notEmpty' => 'required',
            'maxLengthW' => 'max:20',
        ],
    ];

    /**
     * Get the validation rules that apply to the model instance.
     *
     * @return array
     */
    public function rules()
    {
        return self::$rules;
    }
}

