<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\Post;

class PostcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 一覧
    public function index()
    {
        $count = Post::count();
        $main_title = "郵便番号の管理";
        $title_text = "管理者メニュー";

        return view('postcode.index', compact('count', 'main_title', 'title_text'));
    }

    // 更新
    public function update(Request $request)
    {
        $count = Post::count();
        $main_title = "郵便番号の管理";
        $title_text = "管理者メニュー";

        $ex_csv = ['csv', 'CSV'];
        $mime_csv = ['application/vnd.ms-excel'];

        if ($request->hasFile('Post.Csv')) {
            $file = $request->file('Post.Csv');
            $fileName = $file->getClientOriginalName();
            $filePath = $file->storeAs('csv', $fileName);
            $extension = $file->getClientOriginalExtension();
            $mime = $file->getMimeType();

            // 拡張子、MIME Typeのチェック
            if (!in_array($extension, $ex_csv) || !in_array($mime, $mime_csv)) {
                Storage::delete($filePath);
                $backup = $this->_dumpCurrentPostCode();
                Storage::delete('sql/' . $backup);

                Session::flash('error', 'CSV形式のファイルをアップロードしてください');
                return redirect()->action([PostcodeController::class, 'index']);
            }

            // 改行コードの変換
            $contents = file_get_contents($file->getRealPath());
            $convertedContents = $this->_convertEOL($contents);
            file_put_contents(storage_path('app/csv/' . $fileName), $convertedContents);

            if (substr($fileName, 0, 7) == 'KEN_ALL') {
                $action = "一括更新";
                $sqlRes = $this->_getSQLFile(storage_path('app/csv/' . $fileName), false, true);
            } else {
                Storage::delete($filePath);
                $backup = $this->_dumpCurrentPostCode();
                Storage::delete('sql/' . $backup);

                Session::flash('error', 'KEN_ALL.CSV以外のファイルはアップロードできません。');
                return redirect()->action([PostcodeController::class, 'index']);
            }

            if (!$sqlRes) {
                Session::flash('error', 'CSVの内容に誤りがあります。');
                Storage::delete($filePath);
                Storage::delete('sql/' . $backup);
                return redirect()->action([PostcodeController::class, 'index']);
            }

            Storage::delete($filePath);
            return view('postcode.confirm', ['sqlRes' => $sqlRes, 'action' => $action]);
        } else {
            Session::flash('error', '通信エラーまたはPHPの設定により、アップロードが行えませんでした。PHPの設定で、アップロードできるファイルサイズが制限されている場合があるので、php.iniまたは.htaccessでpost_max_size, upload_max_filesizeの設定を行ってください。');
            return redirect()->action([PostcodeController::class, 'index']);
        }
    }

    public function reset()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::unprepared(file_get_contents(storage_path('app/sql/postcode.sql')));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Session::flash('status', '郵便番号データを初期状態に戻しました。');
        return redirect()->action([PostcodeController::class, 'index']);
    }

    private function _dumpCurrentPostCode()
    {
        $fileName = sprintf('%s.sql', now()->format('YmdHis'));
        $path = storage_path('app/sql/' . $fileName);
        $dbConfig = config('database.connections.mysql');
        $cmd = sprintf(
            "mysqldump -t -u %s --password='%s' %s %sM_POST > %s",
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $dbConfig['prefix'],
            $path
        );
        try {
            exec($cmd);
        } catch (\Exception $e) {
            return false;
        }
        return $fileName;
    }

    public function query(Request $request)
    {
        $sqlFile = $request->query('sql');
        $backupFile = $request->query('backup');
        $dbConfig = config('database.connections.mysql');

        if (!DB::connection()->getPdo()) {
            Session::flash('error', 'DBに接続できません。');
            return redirect()->action([PostcodeController::class, 'index']);
        }

        $success = $this->__executeSQLScript(storage_path('app/sql/' . $sqlFile));
        if ($success) {
            Storage::delete('sql/' . $sqlFile);
            Storage::delete('sql/' . $backupFile);
            Session::flash('status', '更新しました。');
            return redirect()->action([PostcodeController::class, 'index']);
        } else {
            Post::truncate();
            if ($this->__executeSQLScript(storage_path('app/sql/' . $backupFile))) {
                Session::flash('status', '更新に失敗したため、前回の状態に戻しました。');
            } else {
                Session::flash('error', '更新に失敗しました。「郵便番号の修復」より、初期状態に戻してください');
            }
            return redirect()->action([PostcodeController::class, 'index']);
        }
    }

    private function _getSQLFile($filePath, $del = false, $all = false)
    {
        $error = false;
        if (empty($filePath)) {
            return redirect()->action([PostcodeController::class, 'index']);
        }

        $pref_code = array_flip(config('custom.prefecture_code'));
        $sqlFile = md5(time()) . '.sql';
        $sqlPath = storage_path('app/sql/' . $sqlFile);
        $fp_sql = fopen($sqlPath, "a");
        $fp_csv = fopen($filePath, "r");

        if ($del) {
            // 廃止データの場合
            fwrite($fp_sql, "DELETE FROM `M_POST` WHERE POSTCODE IN (\n");
            $line = fgetcsv($fp_csv);
            $count = 1;

            while (($line = fgetcsv($fp_csv)) !== false) {
                $tmp_post_code = mb_convert_encoding(sprintf("%07d", $line[2]), 'UTF-8', 'Shift_JIS');
                $tmp_val = "'" . $tmp_post_code . "'";
                fwrite($fp_sql, ($count > 1 ? ",\n" : "") . $tmp_val);
                $count++;
            }
            fwrite($fp_sql, ");");
        } elseif ($all) {
            // 全国版データの一括更新の場合
            fwrite($fp_sql, "DELETE FROM `M_POST` WHERE 1=1;\n");
            fwrite($fp_sql, "INSERT INTO `M_POST` (`POSTCODE`, `CNT_ID`, `CITY`, `AREA`) VALUES\n");
            $line = fgetcsv($fp_csv);
            $count = 1;

            while (($line = fgetcsv($fp_csv)) !== false) {
                $tmp_pref = mb_convert_encoding($line[6], 'UTF-8', 'Shift_JIS');
                $tmp_city = mb_convert_encoding($line[7], 'UTF-8', 'Shift_JIS');
                $tmp_area = mb_convert_encoding($line[8], 'UTF-8', 'Shift_JIS');
                $tmp_post_code = mb_convert_encoding(sprintf("%07d", $line[2]), 'UTF-8', 'Shift_JIS');
                $tmp_pref_code = mb_convert_encoding($pref_code[$tmp_pref], 'UTF-8', 'Shift_JIS');

                if (empty($pref_code[$tmp_pref]) || empty($tmp_pref) || empty($tmp_city) || empty($tmp_area) || empty($tmp_post_code) || empty($tmp_pref_code)) {
                    $error = true;
                    break;
                }

                $tmp_val = "('" . $tmp_post_code . "', " . $tmp_pref_code . ", '" . $tmp_city . "', '" . $tmp_area . "')";
                fwrite($fp_sql, ($count % 2000 == 0 ? ";\nINSERT INTO `M_POST` (`POSTCODE`, `CNT_ID`, `CITY`, `AREA`) VALUES\n" : ",\n") . $tmp_val);
                $count++;
            }
            fwrite($fp_sql, ";");
        } else {
            // 追加データの場合
            fwrite($fp_sql, "INSERT INTO `M_POST` (`POSTCODE`, `CNT_ID`, `CITY`, `AREA`) VALUES\n");
            $line = fgetcsv($fp_csv);
            $count = 1;

            while (($line = fgetcsv($fp_csv)) !== false) {
                $tmp_pref = mb_convert_encoding($line[6], 'UTF-8', 'Shift_JIS');
                $tmp_city = mb_convert_encoding($line[7], 'UTF-8', 'Shift_JIS');
                $tmp_area = mb_convert_encoding($line[8], 'UTF-8', 'Shift_JIS');
                $tmp_post_code = mb_convert_encoding(sprintf("%07d", $line[2]), 'UTF-8', 'Shift_JIS');
                $tmp_pref_code = mb_convert_encoding($pref_code[$tmp_pref], 'UTF-8', 'Shift_JIS');

                if (empty($tmp_pref) || empty($tmp_city) || empty($tmp_area) || empty($tmp_post_code) || empty($tmp_pref_code)) {
                    $error = true;
                }

                $tmp_val = "('" . $tmp_post_code . "', " . $tmp_pref_code . ", '" . $tmp_city . "', '" . $tmp_area . "')";
                fwrite($fp_sql, ($count > 1 ? ",\n" : "") . $tmp_val);
                $count++;
            }
            fwrite($fp_sql, ";");
        }

        fclose($fp_sql);

        if ($error) {
            Storage::delete($sqlPath);
            return false;
        } else {
            return [
                'sql' => $sqlFile,
                'count' => $count,
                'del' => $del
            ];
        }
    }

    private function _convertEOL($string, $to = "\n")
    {
        return preg_replace("/\r\n|\r|\n/", $to, $string);
    }

    private function __executeSQLScript($fileName)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $statements = file_get_contents($fileName);
        $statements = explode(';', $statements);
        $prefix = config('database.connections.mysql.prefix');

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement != '') {
                $pattern = [
                    '/(DROP TABLE IF EXISTS `)([a-z_]+)(`)/i',
                    '/(CREATE TABLE IF NOT EXISTS `)([a-z_]+)(`)/i',
                    '/(INSERT INTO `)([a-z_]+)(`)/i'
                ];
                $statement = preg_replace($pattern, '$1' . $prefix . '$2$3', $statement);
                DB::statement($statement);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return empty(DB::connection()->errorInfo());
    }
}
