<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mail;
use App\Models\Quote;
use App\Models\Bill;
use App\Models\Delivery;
use Auth;
use Session;
use Config;
use Carbon\Carbon;

class MailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['login', 'customer', 'logout']);
    }

    public function index(Request $request)
    {

        $type = [
            1 => '見積書',
            2 => '請求書',
            3 => '納品書'
        ];
         // Initialize the query builder
        $query = Mail::query();

        // Apply filters based on the request parameters
        if ($request->SUBJECT) {
            $query->where('SUBJECT', 'like', '%' . $request->SUBJECT . '%');
        }

        if ($request->CUSTOMER_CHARGE) {
            $query->where('RCV_NAME', 'like', '%' . $request->CUSTOMER_CHARGE . '%');
        }

        if ($request->STATUS) {
            $query->whereIn('STATUS', $request->STATUS);
        }

        if ($request->TYPE) {
            $query->whereIn('TYPE', $request->TYPE);
        }

        // Fetch the paginated results
        $paginator = $query->orderBy('RCV_DATE')->paginate(20);

        $list = $paginator->items();

        $searchData = $request ? $request: "";
        $searchStatus = $request->STATUS;
        $searchType = $request->TYPE;

        // dd($request->STATUS);

        return view('mail.index', [
            'mailstatus' => config('constants.MailStatusCode'),
            'type' => $type,
            'main_title' => '確認メール',
            'title_text' => '帳票管理',
            'title' => "抹茶請求書",
            'list' => $list,
            'paginator' => $paginator,
            'searchData' => $searchData,
            'searchStatus' => $searchStatus,
            'searchType' => $searchType,
        ]);
    }

    public function check($tml_id)
    {
        $result = Mail::where('TML_ID', $tml_id)->first();

        if (!$result) {
            return redirect('/mails');
        }

        if (!$this->Get_Edit_Authority($result->USR_ID)) {
            Session::flash('message', '不正な操作が行われました');
            return redirect('/mails');
        }

        $status = config('constants.MailStatusCode');
        $title = '';

        switch ($result->TYPE) {
            case 1:
                $title = '見積書';
                break;
            case 2:
                $title = '請求書';
                break;
            case 3:
                $title = '納品書';
                break;
        }

        return view('mail.check', [
            'reptime' => $result->RCV_DATE,
            'mail_status' => $status[$result->STATUS],
            'sender' => $result->SENDER,
            'receiver' => $result->RECEIVER,
            'comment' => nl2br(e($result->RCV_MESSAGE)),
            'rcv_name' => $result->RCV_NAME,
            'snd_name' => $result->SND_NAME,
            'subject' => $result->SUBJECT,
            'main_title' => "{$title}確認メール",
            'title_text' => '帳票管理',
            'title' => '抹茶請求書'
        ]);
    }

    public function customer(Request $request)
    {
        $type = '';

        if ($request->isMethod('post')) {
            foreach ($request->all() as $key => $value) {
                if (preg_match("/^(.*)_x$/", $key, $result)) {
                    $type = $result[1];
                }
            }
        }

        switch ($type) {
            case 'send':
                if (!$this->Common->checkOneTimeToken('_cml', $request->input('Mail.tkn'))) {
                    abort(404);
                }

                $this->Common->deleteOneTimeToken('_cml', $request->input('Mail.tkn'));

                $param = $request->input('Mail');
                $param['RCV_MESSAGE'] = $param['COMMENT'];

                $time_limit = Carbon::now()->subDays(config('constants.MailLoginTerm'))->toDateTimeString();

                $check = Mail::where('TOKEN', $param['TOKEN'])
                             ->where('SND_DATE', '>=', $time_limit)
                             ->first();

                if (empty($check)) {
                    abort(404);
                }

                if ($this->Mail->res_mail($check, $param['Mail'])) {
                    $param['Mail']['TOKEN'] = null;
                    $param['Mail']['RCV_DATE'] = Carbon::now();

                    Mail::where('id', $check->id)->update($param);

                    return redirect('/mails/logout');
                }

                break;

            case 'reaffirmation':
                if (!$this->Common->checkOneTimeToken('_cml', $request->input('Mail.tkn'))) {
                    abort(404);
                }

                $this->validate($request, [
                    'Mail' => 'required'
                ]);

                $sta = [
                    1 => '確認済み',
                    2 => '修正願い'
                ];

                return view('mail.c_reaffirmation', [
                    'status' => $sta[$request->input('Mail.STATUS')],
                    'comment' => nl2br(e($request->input('Mail.COMMENT')))
                ]);

            default:
                $time_limit = Carbon::now()->subDays(config('constants.MailLoginTerm'))->toDateTimeString();

                $token = $request->input('Mail.TOKEN');
                $pass = $request->input('Mail.PASSWORD');
                $check = Mail::where('TOKEN', $token)
                             ->where('PASSWORD', bcrypt($pass))
                             ->where('SND_DATE', '>=', $time_limit)
                             ->first();

                if (!$check) {
                    Session::flash('message', 'パスワードが違います');
                    return redirect("mails/login/{$token}");
                }

                if ($check->TYPE == 1) {
                    $type = "quotes";
                } elseif ($check->TYPE == 2) {
                    $type = "bills";
                } else {
                    $type = "deliveries";
                }

                return view('mail.customer', [
                    'frm_id' => $check->FRM_ID,
                    'type' => $type,
                    'token' => $token,
                    'subject' => $check->SUBJECT,
                    'snd_name' => $check->SND_NAME,
                    'rcv_name' => $check->RCV_NAME
                ]);
        }
    }

    public function login($token)
    {
        $time_limit = Carbon::now()->subDays(config('constants.MailLoginTerm'))->toDateTimeString();

        $check = Mail::where('TOKEN', $token)
                     ->where('SND_DATE', '>=', $time_limit)
                     ->first();

        if (empty($check)) {
            abort(404);
        }

        return view('mail.login', [
            'customer_charge' => $check->RCV_NAME,
            'charge' => $check->SND_NAME,
            'token' => $token
        ]);
    }

    public function logout()
    {
        return view('mail.logout');
    }

    public function sendmail(Request $request)
    {
        $type = '';

        if ($request->isMethod('post')) {
            foreach ($request->all() as $key => $value) {
                if (preg_match("/^(.*)_x$/", $key, $result)) {
                    $type = $result[1];
                }
            }
        }

        switch ($type) {
            case 'send':
                if (!$this->Common->checkOneTimeToken('_ml', $request->input('Mail.tkn'))) {
                    Session::flash('message', '初めから登録してください');
                    return redirect("/mails");
                }

                $this->Common->deleteOneTimeToken('_ml', $request->input('Mail.tkn'));

                $request->input('Mail.PASSWORD', bcrypt($request->input('Mail.PASSWORD1')));

                if ($this->Mail->Send_Mail($request->input('Mail'))) {
                    $pass = $request->input('Mail.PASSWORD1');

                    return view('mail.completion', [
                        'main_title' => '確認依頼',
                        'title_text' => '帳票管理',
                        'title' => '抹茶請求書',
                        'pass' => $pass
                    ]);
                } else {
                    Session::flash('message', 'メールの送信に失敗しました');
                    return redirect("/mails");
                }

                break;

            case 'reaffirmation':
                if (!$this->Common->checkOneTimeToken('_ml', $request->input('Mail.tkn'))) {
                    Session::flash('message', '初めから登録してください');
                    return redirect("/mails");
                }

                $this->validate($request, [
                    'Mail' => 'required'
                ]);

                $param = $request->input('Mail');

                return view('mail.reaffirmation', [
                    'main_title' => '確認依頼',
                    'title_text' => '帳票管理',
                    'title' => '抹茶請求書',
                    'param' => $param
                ]);

            default:
                $tkn = $this->Common->createOneTimeToken('_ml');
                $time_limit = Carbon::now()->subDays(config('constants.MailSendTerm'))->toDateTimeString();

                $all_quotes = Quote::where('USR_ID', Auth::id())
                                  ->where('SND_DATE', '>=', $time_limit)
                                  ->get();

                $all_bills = Bill::where('USR_ID', Auth::id())
                                ->where('SND_DATE', '>=', $time_limit)
                                ->get();

                $all_deliveries = Delivery::where('USR_ID', Auth::id())
                                          ->where('SND_DATE', '>=', $time_limit)
                                          ->get();

                return view('mail.sendmail', [
                    'quotes' => $all_quotes,
                    'bills' => $all_bills,
                    'deliveries' => $all_deliveries,
                    'token' => $tkn,
                    'main_title' => '確認依頼',
                    'title_text' => '帳票管理',
                    'title' => '抹茶請求書',
                ]);
        }
    }

}
