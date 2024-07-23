<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Home;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        if ($this->getUserAuthority() != 1) {
            $bill = Home::getRecentForms('t_bill');
            $quote = Home::getRecentForms('t_quote');
            $delivery = Home::getRecentForms('t_delivery');
        } else {
            $bill = Home::getRecentForms('t_bill', $this->getUserId());
            $quote = Home::getRecentForms('t_quote', $this->getUserId());
            $delivery = Home::getRecentForms('t_delivery', $this->getUserId());
        }

        $users = User::select('USR_ID', 'NAME')->get();

        $title = '抹茶請求書';
        return view('homes.index', [
            'title'=>$title,
            'main_title' => 'HOME',
            'bill' => $bill,
            'quote' => $quote,
            'delivery' => $delivery,
            'users' => $users
        ]);
    }

    // private function getUserAuthority()
    // {
    //     // Implement your logic to get the user's authority
    //     // This is a placeholder for the actual implementation
    //     return auth()->user()->authority;
    // }

    // private function getUserId()
    // {
    //     // Implement your logic to get the user's ID
    //     // This is a placeholder for the actual implementation
    //     return auth()->user()->id;
    // }
    private function getUserAuthority()
    {
        if (auth()->check()) {
            return auth()->user()->authority;
        }
        return null;
    }

    private function getUserId()
    {
        if (auth()->check()) {
            return auth()->user()->id;
        }
        return null;
    }
}
