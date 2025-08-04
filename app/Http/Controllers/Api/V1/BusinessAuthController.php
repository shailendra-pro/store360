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
     * Business and End User login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username or email (both business and end user)
        $user = User::where('username', $request->username)
                   ->orWhere('email', $request->username)
                   ->orWhere('contact_email', $request->username)
                   ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => [
                    'username' => ['Invalid username or password.']
                ]
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account not active',
                'errors' => [
                    'username' => ['Your account is not active. Please contact administrator.']
                ]
            ], 403);
        }

        // Handle different user types
        if ($user->role === 'business') {
            // Business user login
            if (!$user->isBusinessActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not active',
                    'errors' => [
                        'username' => ['Your account is not active. Please contact administrator.']
                    ]
                ], 403);
            }

            // Attempt authentication for business (try username, email, or contact_email)
            $credentials = [
                'password' => $request->password
            ];

            // Try different login fields
            if (Auth::attempt(['username' => $request->username, 'password' => $request->password]) ||
                Auth::attempt(['email' => $request->username, 'password' => $request->password]) ||
                Auth::attempt(['contact_email' => $request->username, 'password' => $request->password])) {
                $authenticatedUser = Auth::user();
                $token = $authenticatedUser->createToken('business-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'role' => $authenticatedUser->role,
                        'user' => [
                            'id' => $authenticatedUser->id,
                            'business_name' => $authenticatedUser->business_name,
                            'contact_email' => $authenticatedUser->contact_email,
                            'username' => $authenticatedUser->username,
                            'logo_url' => $authenticatedUser->logo_url,
                            'is_active' => $authenticatedUser->is_active,
                            'expires_at' => $authenticatedUser->expires_at,
                            'business_description' => $authenticatedUser->business_description,
                            'phone' => $authenticatedUser->phone,
                            'address' => $authenticatedUser->address,
                            'city' => $authenticatedUser->city,
                            'state' => $authenticatedUser->state,
                            'postal_code' => $authenticatedUser->postal_code,
                            'country' => $authenticatedUser->country,
                        ],
                        'store_config' => [
                            'store_name' => $authenticatedUser->business_name,
                            'logo_url' => $authenticatedUser->logo_url,
                            'theme_color' => '#0d6efd',
                            'splash_duration' => 3000,
                        ],
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'expires_in' => config('sanctum.expiration') * 60,
                    ]
                ], 200);
            }

        } elseif ($user->role === 'user') {
            //dd($user->secure_link_expires_at);
            if ($user->secure_link_expires_at && now()->greaterThan($user->secure_link_expires_at) || $user->secure_link_expires_at === null ) {

                return response()->json([
                    'success' => false,
                    'message' => 'Account expired',
                    'errors' => [
                        'username' => ['Your account has expired. Please contact administrator.']
                    ]
                ], 403);
            }
            // End user login - use proper password validation
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'errors' => [
                        'username' => ['Invalid username or password.']
                    ]
                ], 401);
            }

            // Get business details for end user
            $business = User::where('role', 'business')
                          ->where('business_name', $user->company)
                          ->first();

            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found',
                    'errors' => [
                        'username' => ['Company not found. Please contact administrator.']
                    ]
                ], 404);
            }

            // Create token for end user
            $token = $user->createToken('enduser-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'role' => $user->role,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username,
                        'company' => $user->company,
                        'is_active' => $user->is_active,
                        'expires_at' => $user->expires_at,
                    ],
                    'business' => [
                        'id' => $business->id,
                        'business_name' => $business->business_name,
                        'contact_email' => $business->contact_email,
                        'logo_url' => $business->logo_url,
                        'business_description' => $business->business_description,
                        'phone' => $business->phone,
                        'address' => $business->address,
                        'city' => $business->city,
                        'state' => $business->state,
                        'postal_code' => $business->postal_code,
                        'country' => $business->country,
                    ],
                    'store_config' => [
                        'store_name' => $business->business_name,
                        'logo_url' => $business->logo_url,
                        'theme_color' => '#0d6efd',
                        'splash_duration' => 3000,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => config('sanctum.expiration') * 60,
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
       // dd(Auth::user());
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        if ($user->role === 'business') {
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
        } elseif ($user->role === 'user') {
            // Get business details for end user
            $business = \App\Models\User::where('role', 'business')
                ->where('business_name', $user->company)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username,
                        'company' => $user->company,
                        'is_active' => $user->is_active,
                        'expires_at' => $user->expires_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'business' => $business ? [
                        'id' => $business->id,
                        'business_name' => $business->business_name,
                        'contact_email' => $business->contact_email,
                        'logo_url' => $business->logo_url,
                        'business_description' => $business->business_description,
                        'phone' => $business->phone,
                        'address' => $business->address,
                        'city' => $business->city,
                        'state' => $business->state,
                        'postal_code' => $business->postal_code,
                        'country' => $business->country,
                    ] : null,
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid account type.'
        ], 403);
    }

    /**
     * Update business profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'business') {
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
        } elseif ($user->role === 'user') {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255',
                'company' => 'sometimes|string|max:255',
            ]);

            $user->update($request->only([
                'name',
                'email',
                'company',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username,
                        'company' => $user->company,
                        'is_active' => $user->is_active,
                        'expires_at' => $user->expires_at,
                        'updated_at' => $user->updated_at,
                    ]
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid account type.'
        ], 403);
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
