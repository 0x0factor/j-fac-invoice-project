<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class History extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_HISTORY';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'HST_ID';

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
        'USR_ID', 'ACTION_DATE', 'ACTION', 'DEL_FLAG', 'BROWSER', 'RPT_ID'
    ];

    /**
     * Define relationships.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'USR_ID');
    }

    /**
     * Login action logging.
     *
     * @param int $_user
     * @return bool
     */
    public function h_login($_user)
    {
        $history = new History();
        $history->USR_ID = $_user;
        $history->ACTION_DATE = Carbon::now();
        $history->ACTION = 0;
        $history->DEL_FLAG = 0;
        $history->BROWSER = $this->browser_hash();

        return $history->save();
    }

    /**
     * Logout action logging.
     *
     * @param int $_user
     * @param string|null $_browser
     * @return bool
     */
    public function h_logout($_user, $_browser = null)
    {
        $history = new History();
        $history->USR_ID = $_user;
        $history->ACTION_DATE = Carbon::now();
        $history->ACTION = 1;
        $history->DEL_FLAG = 0;
        $history->BROWSER = $_browser ? $_browser : $this->browser_hash();

        return $history->save();
    }

    /**
     * Report action logging.
     *
     * @param int $_user
     * @param int $a_num
     * @param int $r_num
     * @return bool
     */
    public function h_reportaction($_user, $a_num, $r_num)
    {
        $history = new History();
        $history->USR_ID = $_user;
        $history->ACTION = $a_num;
        $history->RPT_ID = $r_num;
        $history->ACTION_DATE = Carbon::now();
        // $history->DEL_FLAG = 0;

        return $history->save();
    }

    /**
     * Retrieve last log for a user.
     *
     * @param int $_user
     * @return mixed
     */
    public function h_getlastlog($_user)
    {
        return History::where('USR_ID', $_user)
            ->orderBy('HST_ID', 'desc')
            ->first(['ACTION', 'BROWSER']);
    }

    /**
     * Retrieve browser log for a user.
     *
     * @param int $_user
     * @return mixed
     */
    public function browser_log($_user)
    {
        $browser = getenv("HTTP_USER_AGENT");

        return History::where('USR_ID', $_user)
            ->where('BROWSER', $browser)
            ->orderBy('HST_ID', 'desc')
            ->first(['ACTION']);
    }

    /**
     * Generate hash for browser identification.
     *
     * @return string
     */
    public function browser_hash()
    {
        $browser = getenv("HTTP_USER_AGENT");
        return Hash::make($browser);
    }
}
