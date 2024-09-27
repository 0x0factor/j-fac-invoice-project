<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Home;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            // Redirect to the login page if not authenticated
            return redirect()->route('login');
        }

        if ($this->getUserAuthority() != 1) {
            $bill = Home::getRecentForms('T_BILL');
            $quote = Home::getRecentForms('T_QUOTE');
            $delivery = Home::getRecentForms('T_DELIVERY');
        } else {
            $bill = Home::getRecentForms('T_BILL', $this->getUserId());
            $quote = Home::getRecentForms('T_QUOTE', $this->getUserId());
            $delivery = Home::getRecentForms('T_DELIVERY', $this->getUserId());
        }

        $users = User::select('USR_ID', 'NAME')->get();

        $title = '抹茶請求書';
        $controller_name = 'Home';
        return view('homes.index', [
            'title'=>$title,
            'main_title' => 'HOME',
            'bill' => $bill,
            'quote' => $quote,
            'delivery' => $delivery,
            'users' => $users,
            'controller_name' => $controller_name,
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
