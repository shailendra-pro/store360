<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * Get business dashboard data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        // Get basic statistics
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'users_with_secure_links' => User::where('role', 'user')->whereNotNull('secure_link')->count(),
            'valid_secure_links' => User::where('role', 'user')
                                        ->whereNotNull('secure_link')
                                        ->where('secure_link_expires_at', '>', now())
                                        ->count(),
        ];

        // Get recent users (last 5)
        $recentUsers = User::where('role', 'user')
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get(['id', 'name', 'email', 'company', 'created_at']);

        // Get users with expiring secure links (next 24 hours)
        $expiringLinks = User::where('role', 'user')
                            ->whereNotNull('secure_link')
                            ->where('secure_link_expires_at', '>', now())
                            ->where('secure_link_expires_at', '<', now()->addDay())
                            ->get(['id', 'name', 'email', 'secure_link_expires_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'business' => [
                    'id' => $user->id,
                    'business_name' => $user->business_name,
                    'contact_email' => $user->contact_email,
                    'username' => $user->username,
                    'logo_url' => $user->logo_url,
                    'is_active' => $user->is_active,
                    'expires_at' => $user->expires_at,
                    'business_description' => $user->business_description,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'city' => $user->city,
                    'state' => $user->state,
                    'postal_code' => $user->postal_code,
                    'country' => $user->country,
                ],
                'statistics' => $stats,
                'recent_users' => $recentUsers,
                'expiring_links' => $expiringLinks,
            ]
        ], 200);
    }

    /**
     * Get users list (paginated)
     */
    public function getUsers(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $query = User::where('role', 'user');

        // Apply filters
        if ($request->filled('company')) {
            $query->where('company', 'like', '%' . $request->company . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('has_secure_link')) {
            if ($request->has_secure_link === 'true') {
                $query->whereNotNull('secure_link');
            } elseif ($request->has_secure_link === 'false') {
                $query->whereNull('secure_link');
            }
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('company', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ]
            ]
        ], 200);
    }

    /**
     * Get user details
     */
    public function getUser(Request $request, $userId)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $targetUser = User::where('id', $userId)
                         ->where('role', 'user')
                         ->first();

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                    'company' => $targetUser->company,
                    'role' => $targetUser->role,
                    'is_active' => $targetUser->is_active,
                    'secure_link' => $targetUser->secure_link ? true : false,
                    'secure_link_expires_at' => $targetUser->secure_link_expires_at,
                    'remaining_hours' => $targetUser->remaining_hours,
                    'custom_hours' => $targetUser->custom_hours,
                    'created_at' => $targetUser->created_at,
                    'updated_at' => $targetUser->updated_at,
                ]
            ]
        ], 200);
    }

    /**
     * Update business logo
     */
    public function updateLogo(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Delete old logo if exists
        if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
            Storage::disk('public')->delete($user->logo_path);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('business-logos', 'public');

        $user->update(['logo_path' => $logoPath]);

        return response()->json([
            'success' => true,
            'message' => 'Logo updated successfully',
            'data' => [
                'logo_url' => $user->logo_url
            ]
        ], 200);
    }

    /**
     * Get business statistics
     */
    public function getStatistics(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'active_users' => User::where('role', 'user')->where('is_active', true)->count(),
            'inactive_users' => User::where('role', 'user')->where('is_active', false)->count(),
            'users_with_secure_links' => User::where('role', 'user')->whereNotNull('secure_link')->count(),
            'valid_secure_links' => User::where('role', 'user')
                                        ->whereNotNull('secure_link')
                                        ->where('secure_link_expires_at', '>', now())
                                        ->count(),
            'expired_secure_links' => User::where('role', 'user')
                                         ->whereNotNull('secure_link')
                                         ->where('secure_link_expires_at', '<', now())
                                         ->count(),
            'users_by_company' => User::where('role', 'user')
                                     ->whereNotNull('company')
                                     ->selectRaw('company, count(*) as count')
                                     ->groupBy('company')
                                     ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ], 200);
    }

    /**
     * Get companies list
     */
    public function getCompanies(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $companies = User::where('role', 'user')
                        ->whereNotNull('company')
                        ->distinct()
                        ->pluck('company');

        return response()->json([
            'success' => true,
            'data' => [
                'companies' => $companies
            ]
        ], 200);
    }

    /**
     * Get users with expiring secure links
     */
    public function getExpiringLinks(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $hours = $request->get('hours', 24); // Default 24 hours

        $expiringUsers = User::where('role', 'user')
                            ->whereNotNull('secure_link')
                            ->where('secure_link_expires_at', '>', now())
                            ->where('secure_link_expires_at', '<', now()->addHours($hours))
                            ->orderBy('secure_link_expires_at', 'asc')
                            ->get(['id', 'name', 'email', 'company', 'secure_link_expires_at', 'remaining_hours']);

        return response()->json([
            'success' => true,
            'data' => [
                'expiring_users' => $expiringUsers,
                'hours_threshold' => $hours
            ]
        ], 200);
    }
}
