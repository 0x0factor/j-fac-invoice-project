<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Company;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 一覧用
    public function index()
    {
        $list = 5;
        $excises = Config::get('ExciseCode');
        $main_title = "商品管理";
        $title_text = "自社情報設定";
        $title = "抹茶請求書";

        $page_title = "Company";

        $condition = [];
        $paginator = Item::where($condition)
        ->orderBy('INSERT_DATE')
        ->paginate(20);
        $list = $paginator->items();
// dd($paginator);
        return view('item.index', compact('excises', 'main_title', 'title_text', 'title', 'page_title', 'paginator', 'list'));
    }

    // 登録用
    public function add(Request $request)
    {
        $main_title = "商品登録";
        $title_text = "自社情報設定";
        $title = "抹茶請求書";

        if ($request->has('cancel_x')) {
            return redirect('/items');
        }

        if ($request->has('submit_x')) {
            // トークンチェック
            $this->validate($request, [
                'Security.token' => 'required|csrf_token'
            ]);

            // データのインサート
            $setdata = Item::set_data($request->input('data'));
            if (!isset($setdata['error'])) {
                // 成功
                Session::flash('status', '商品を保存しました');
                return redirect('/items/check/' . $setdata['Item']['ITM_ID']);
            }
        }



        $user = Auth::user();
        $company = Company::first();
        $request->merge(['Item.TAX_CLASS' => $company->EXCISE + 1]);

        $excises = Config::get('ExciseCode');

        return view('item.add', compact('main_title', 'title_text', 'title', 'excises'));
    }

    // 編集用
    public function check($item_ID, Request $request)
    {
        $main_title = "商品確認";
        $title_text = "自社情報設定";
        $title = "抹茶請求書";

        if (!$request->has('data')) {
            // 初期データの取得
            $data = Item::edit_select($item_ID);
            if (!$data) {
                return redirect('/items');
            }
        } else {
            $data = $request->input('data');
        }

        $editauth = $this->Get_Edit_Authority($data['Item']['USR_ID']);
        if (!$this->Get_Check_Authority($data['Item']['USR_ID'])) {
            Session::flash('status', 'ページを開く権限がありません');
            return redirect('/items');
        }

        $excises = Config::get('ExciseCode');

        return view('item.check', compact('main_title', 'title_text', 'title', 'excises', 'editauth', 'data'));
    }

    // 編集用
    public function edit($item_ID, Request $request)
    {
        $main_title = "商品編集";
        $title_text = "自社情報設定";
        $title = "抹茶請求書";

        if ($request->has('cancel_x')) {
            return redirect('/items');
        }

        if (!$request->has('data')) {
            $data = Item::edit_select($item_ID);
            if (!$data) {
                return redirect('/items');
            }
            if (!$this->Get_Edit_Authority($data['Item']['USR_ID'])) {
                Session::flash('status', 'ページを開く権限がありません');
                return redirect('/items/index/');
            }
        } else {
            // トークンチェック
            $this->validate($request, [
                'Security.token' => 'required|csrf_token'
            ]);

            // データのインサート
            $setdata = Item::set_data($request->input('data'));
            if (!isset($setdata['error'])) {
                // 成功
                Session::flash('status', '商品を保存しました');
                return redirect('/items/check/' . $setdata['Item']['ITM_ID']);
            }
        }

        $excises = Config::get('ExciseCode');

        return view('item.edit', compact('main_title', 'title_text', 'title', 'excises'));
    }

    // 削除用
    public function delete(Request $request)
    {
        // トークンチェック
        $this->validate($request, [
            'Security.token' => 'required|csrf_token'
        ]);

        // 削除
        $result = Item::index_delete($request->input('data'));
        if ($result) {
            // 成功
            Session::flash('status', '商品を削除しました');
        } else {
            // 失敗
            Session::flash('status', '商品が削除できませんでした');
        }

        return redirect('/items/index');
    }
}
