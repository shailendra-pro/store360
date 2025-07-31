<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show admin login form
     */
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is admin
            if (!$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have admin privileges.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get dynamic statistics
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_businesses' => User::where('role', 'business')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'active_businesses' => User::where('role', 'business')->where('is_active', true)->count(),
            'users_with_secure_links' => User::where('role', 'user')->whereNotNull('secure_link')->count(),
            'valid_secure_links' => User::where('role', 'user')
                                        ->whereNotNull('secure_link')
                                        ->where('secure_link_expires_at', '>', now())
                                        ->count(),
            'expired_secure_links' => User::where('role', 'user')
                                         ->whereNotNull('secure_link')
                                         ->where('secure_link_expires_at', '<', now())
                                         ->count(),
            'total_categories' => \App\Models\Category::count(),
            'active_categories' => \App\Models\Category::count(),
            'total_subcategories' => \App\Models\Subcategory::count(),
            'active_subcategories' => \App\Models\Subcategory::where('is_active', true)->count(),
        ];

        // Get recent users (last 5)
        $recentUsers = User::where('role', 'user')
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get(['id', 'name', 'email', 'company', 'is_active', 'created_at']);

        // Get recent businesses (last 5)
        $recentBusinesses = User::where('role', 'business')
                               ->orderBy('created_at', 'desc')
                               ->limit(5)
                               ->get(['id', 'business_name', 'contact_email', 'username', 'is_active', 'created_at']);

        // Get users with expiring secure links (next 24 hours)
        $expiringLinks = User::where('role', 'user')
                            ->whereNotNull('secure_link')
                            ->where('secure_link_expires_at', '>', now())
                            ->where('secure_link_expires_at', '<', now()->addDay())
                            ->get(['id', 'name', 'email', 'secure_link_expires_at']);

        // Get companies with most users
        $topCompanies = User::where('role', 'user')
                           ->whereNotNull('company')
                           ->selectRaw('company, count(*) as user_count')
                           ->groupBy('company')
                           ->orderBy('user_count', 'desc')
                           ->limit(5)
                           ->get();

        // Get monthly user growth (last 6 months)
        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $monthlyGrowth[] = [
                'month' => $date->format('M Y'),
                'users' => User::where('role', 'user')
                              ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                              ->count(),
                'businesses' => User::where('role', 'business')
                                   ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                   ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'user',
            'stats',
            'recentUsers',
            'recentBusinesses',
            'expiringLinks',
            'topCompanies',
            'monthlyGrowth'
        ));
    }

    /**
     * Show admin settings
     */
    public function settings()
    {
        $user = Auth::user();
        return view('admin.settings', compact('user'));
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'new_password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'new_password_confirmation.same' => 'The password confirmation does not match.',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Check if new password is different from current password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors([
                'new_password' => 'The new password must be different from the current password.',
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Logout user and redirect to login with success message
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login')->with('success', 'Password updated successfully! Please login with your new password.');
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
