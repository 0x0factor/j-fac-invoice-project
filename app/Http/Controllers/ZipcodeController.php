<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ZipcodeController extends Controller
{
    public function index()
    {
        $count = $this->getZipcodeCount();

        $main_title = "郵便番号の管理";
        $title_text = "管理者メニュー";
        $title = "抹茶請求書";

        return view('postcode.index', compact('count', 'main_title', 'title_text', 'title'));

    }

    public function update(Request $request)
    {
        // Handle the file upload and processing logic here
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            // Process the uploaded CSV file
            // For example, store it in the storage directory
            $path = $file->storeAs('uploads', 'KEN_ALL.CSV');
            // Add your CSV processing logic here
        }

        return redirect()->route('postcode.index')->with('status', 'CSV file uploaded successfully.');
    }

    public function reset()
    {
        // Handle the reset logic here
        return redirect()->route('postcode.index')->with('status', 'Zipcodes have been reset to initial state.');
    }

    private function getZipcodeCount()
    {
        // Replace with actual logic to count zipcodes
        return 100; // Example count
    }
}
