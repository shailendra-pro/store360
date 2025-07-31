<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BusinessAuthController extends Controller
{
    /**
     * Show the business login form
     */
    public function showLogin()
    {
        return view('business.auth.login');
    }

    /**
     * Handle business login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find business user by username
        $business = User::where('username', $request->username)
                       ->where('role', 'business')
                       ->first();

        if (!$business) {
            return back()->withErrors([
                'username' => 'Invalid username or password.',
            ]);
        }

        // Check if business account is active
        if (!$business->isBusinessActive()) {
            return back()->withErrors([
                'username' => 'Your account is not active. Please contact administrator.',
            ]);
        }

        // Attempt authentication
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->intended('/business/dashboard');
        }

        return back()->withErrors([
            'username' => 'Invalid username or password.',
        ]);
    }

    /**
     * Handle business logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/business/login');
    }

    /**
     * Show business dashboard
     */
    public function dashboard()
    {
        $business = Auth::user();

        if (!$business->isBusiness()) {
            Auth::logout();
            return redirect('/business/login')->withErrors([
                'username' => 'Access denied. Business account required.',
            ]);
        }

        return view('business.dashboard', compact('business'));
    }
}
