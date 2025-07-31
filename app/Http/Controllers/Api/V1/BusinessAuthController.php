<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class BusinessAuthController extends Controller
{
    /**
     * Business login
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => [
                    'username' => ['Invalid username or password.']
                ]
            ], 401);
        }

        // Check if business account is active
        if (!$business->isBusinessActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Account not active',
                'errors' => [
                    'username' => ['Your account is not active. Please contact administrator.']
                ]
            ], 403);
        }

        // Attempt authentication
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('business-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
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
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => config('sanctum.expiration') * 60, // Convert to seconds
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'errors' => [
                'username' => ['Invalid username or password.']
            ]
        ], 401);
    }

    /**
     * Business logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Get business profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
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
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]
        ], 200);
    }

    /**
     * Update business profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $request->validate([
            'business_name' => 'sometimes|string|max:255',
            'contact_email' => 'sometimes|email|max:255',
            'business_description' => 'sometimes|string|max:1000',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
        ]);

        $user->update($request->only([
            'business_name',
            'contact_email',
            'business_description',
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => [
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
                    'updated_at' => $user->updated_at,
                ]
            ]
        ], 200);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['Current password is incorrect.']
                ]
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ], 200);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        if (!$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Business account required.'
            ], 403);
        }

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('business-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('sanctum.expiration') * 60, // Convert to seconds
            ]
        ], 200);
    }

    /**
     * Check authentication status
     */
    public function checkAuth(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isBusiness()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated or invalid account type'
            ], 401);
        }

        if (!$user->isBusinessActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Account is not active'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Authenticated',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'business_name' => $user->business_name,
                    'username' => $user->username,
                    'is_active' => $user->is_active,
                    'expires_at' => $user->expires_at,
                ]
            ]
        ], 200);
    }
}
