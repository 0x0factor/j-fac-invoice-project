<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $main_title = "操作履歴";
        $title_text = "管理者メニュー";
        $title = "抹茶請求書";

        // Fetching paginated data
        $condition = [];
        $histories = History::where($condition)
            ->orderBy('ACTION_DATE')
            ->orderBy('ACTION')
            ->paginate(15); // Adjust pagination limit as needed
        $paginator = $histories;

        // dd($paginator);

        $ids = [];
        foreach ($histories as $key => $val) {
            $id_data = History::select('RPT_ID')
                ->where('ACTION_DATE', $val->ACTION_DATE)
                ->where('ACTION', $val->ACTION)
                ->orderBy('RPT_ID', 'asc')
                ->get();

            foreach ($id_data as $ival) {
                $ids[$key][] = $ival->RPT_ID;
            }
        }

        $action = Config::get('ActionCode');

        return view('history.index', compact('main_title', 'title_text', 'title', 'paginator', 'histories', 'ids', 'action'));
    }
}
