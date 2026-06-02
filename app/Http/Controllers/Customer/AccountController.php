<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Show the customer account page.
     */
    public function index()
    {
        $user = Auth::user();
        $orderCount = $user->orders()->count();
        $totalSpent = $user->orders()->whereNotIn('status', ['cancelled', 'rejected'])->sum('total');
        $recentOrders = $user->orders()->latest()->limit(5)->get();

        return view('customer.account', compact('user', 'orderCount', 'totalSpent', 'recentOrders'));
    }
}
