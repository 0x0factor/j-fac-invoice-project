<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Models\ViewOption;

class ViewOptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // メイン用
    public function index()
    {
        $main_title = "デザイン設定確認";
        $title_text = "管理者メニュー";
        $options = ViewOption::get_option(); // Implement this method in your ViewOption model

        return view('view_options.index', compact('main_title', 'title_text', 'options'));
    }

    // 編集用
    public function edit(Request $request)
    {
        $main_title = "デザイン設定編集";
        $title_text = "管理者メニュー";
        $options = ViewOption::get_option(); // Implement this method in your ViewOption model

        $errors = $this->validateViewOption($request->all());

        if ($request->has('cancel_x')) {
            return redirect()->route('view_options.index');
        }

        if ($request->isMethod('post')) {
            $this->validate($request, [
                'Security.token' => 'required|string',
                'ViewOption.logo' => 'nullable|image|mimes:jpeg,png|max:1024', // Add more validation rules as needed
            ]);

            if ($request->hasFile('ViewOption.logo')) {
                $logoFile = $request->file('ViewOption.logo');
                $logoError = $this->logoValidation($logoFile);

                if (!$logoError) {
                    $uploadDir = config('app.upload_dir'); // Define this in your config
                    $filename = $this->handleFileUpload($logoFile, $uploadDir);

                    if ($filename) {
                        $request->merge(['ViewOption.logo' => $filename]);
                        ViewOption::update_data($request->all()); // Implement this method in your ViewOption model
                        Session::flash('status', '設定を保存しました');
                        return redirect()->route('view_options.index');
                    } else {
                        Session::flash('error', 'アップロード画像の移動に失敗しました');
                        return redirect()->route('view_options.index');
                    }
                } else {
                    return view('view_options.edit', compact('main_title', 'title_text', 'options', 'logoError'));
                }
            } else if ($request->has('ViewOption.logo') && empty($request->file('ViewOption.logo'))) {
                ViewOption::update_data($request->all()); // Implement this method in your ViewOption model
                Session::flash('status', '設定を保存しました');
                return redirect()->route('view_options.index');
            } else {
                Session::flash('error', '画像のアップロードに失敗しました');
                return redirect()->route('view_options.index');
            }
        } else {
            return view('view_options.edit', compact('main_title', 'title_text', 'options', 'errors'));
        }
    }

    // ロゴ画像のバリデーション
    private function logoValidation($logo)
    {
        $error = 0;
        $validMimeTypes = ['image/jpeg', 'image/png'];

        if (!in_array($logo->getMimeType(), $validMimeTypes)) {
            return 1;
        }

        if ($logo->getSize() > 1000000) { // 1MB
            return 2;
        }

        $dataCheck = file_get_contents($logo->getPathname());
        if (strpos($dataCheck, '<?php') !== false || preg_match('/<\?php/i', $dataCheck)) {
            return 1;
        }

        return $error;
    }

    private function handleFileUpload($file, $uploadDir)
    {
        $filename = $file->getClientOriginalName();
        $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $i = 1;

        while (file_exists($uploadDir . $filename)) {
            $filename = $filenameWithoutExt . '_' . $i . '.' . $extension;
            $i++;
        }

        $file->move($uploadDir, $filename);
        return $filename;
    }

    private function validateViewOption(array $data)
    {
        $validator = \Validator::make($data, [
            'Security.token' => 'required|string',
            'ViewOption.logo' => 'nullable|image|mimes:jpeg,png|max:1024',
        ]);

        return $validator->errors();
    }
}
