<?php

namespace App\Http\Controllers;
use PDF; // Facade for DomPDF
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function generatePDF()
    {
        $users = User::all(); // Fetch data from the database

        $data = [
            'title' => 'User List',
            'date' => date('m/d/Y'),
            'users' => $users
        ];

        $pdf = PDF::loadView('pdf_template', $data);
        return $pdf->download('user_list.pdf');
    }
}


