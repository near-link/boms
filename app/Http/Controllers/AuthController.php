<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isVendor()
                ? redirect()->route('vendor.dashboard')
                : redirect()->route('shop.index');
        }

        return view('auth.login');
    }

    /**
     * Handle vendor login.
     */
    public function loginVendor(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'vendor']))) {
            $request->session()->regenerate();
            return redirect()->route('vendor.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid vendor credentials.'])->withInput();
    }

    /**
     * Handle customer login.
     */
    public function loginCustomer(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'customer']))) {
            $request->session()->regenerate();
            return redirect()->route('shop.index');
        }

        return back()->withErrors(['email' => 'Invalid customer credentials.'])->withInput();
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
