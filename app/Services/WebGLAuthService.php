<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WebGLAuthService
{
    /**
     * Validate secure link and get user data
     */
    public function validateSecureLink(string $secureLink): array
    {
        $user = User::where('secure_link', $secureLink)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid secure link',
                'code' => 404
            ];
        }

        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'User account is inactive',
                'code' => 403
            ];
        }

        // Handle different user types
        if ($user->role === 'user') {
            if (!$user->isSecureLinkValid()) {
                return [
                    'success' => false,
                    'message' => 'Secure link has expired',
                    'expired_at' => $user->expires_at,
                    'code' => 401
                ];
            }

            $company = User::where('role', 'business')
                          ->where('business_name', $user->company)
                          ->first();

            if (!$company) {
                return [
                    'success' => false,
                    'message' => 'Company not found',
                    'code' => 404
                ];
            }

        } elseif ($user->role === 'business') {
            if (!$user->isBusinessActive()) {
                return [
                    'success' => false,
                    'message' => 'Business account is not active',
                    'code' => 403
                ];
            }

            $company = $user;
        } else {
            return [
                'success' => false,
                'message' => 'Invalid user type',
                'code' => 403
            ];
        }

        return [
            'success' => true,
            'user' => $user,
            'company' => $company,
            'user_type' => $user->role === 'business' ? 'business' : 'end_user'
        ];
    }

    /**
     * Authenticate user for WebGL
     */
    public function authenticate(string $secureLink, string $email, string $password): array
    {
        // First validate the secure link
        $validation = $this->validateSecureLink($secureLink);

        if (!$validation['success']) {
            return $validation;
        }

        $user = $validation['user'];
        $company = $validation['company'];
        $userType = $validation['user_type'];

        // Validate email matches
        $expectedEmail = $user->role === 'business' ? $user->contact_email : $user->email;

        if ($expectedEmail !== $email) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
                'code' => 401
            ];
        }

        // Validate password based on user type
        if ($user->role === 'user') {
            // End users use simple password
            if ($password !== 'webgl123') {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'code' => 401
                ];
            }
            $sessionExpiry = $user->expires_at ?? now()->addHours(48);
        } else {
            // Business users use hashed password
            if (!Hash::check($password, $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'code' => 401
                ];
            }
            $sessionExpiry = now()->addHours(48);
        }

        // Generate session token
        $sessionToken = Str::random(64);

        // Store session in cache
        Cache::put(
            "webgl_session_{$sessionToken}",
            [
                'user_id' => $user->id,
                'company_id' => $company->id,
                'user_type' => $userType,
                'secure_link' => $secureLink,
                'expires_at' => $sessionExpiry
            ],
            $sessionExpiry
        );

        return [
            'success' => true,
            'session_token' => $sessionToken,
            'user' => $user,
            'company' => $company,
            'user_type' => $userType,
            'session_expiry' => $sessionExpiry
        ];
    }

    /**
     * Validate session token
     */
    public function validateSession(string $sessionToken): array
    {
        $sessionData = Cache::get("webgl_session_{$sessionToken}");

        if (!$sessionData) {
            return [
                'success' => false,
                'message' => 'Invalid or expired session',
                'code' => 401
            ];
        }

        if (Carbon::parse($sessionData['expires_at'])->isPast()) {
            Cache::forget("webgl_session_{$sessionToken}");
            return [
                'success' => false,
                'message' => 'Session has expired',
                'code' => 401
            ];
        }

        $user = User::find($sessionData['user_id']);
        $company = User::find($sessionData['company_id']);

        if (!$user || !$company) {
            return [
                'success' => false,
                'message' => 'User or company not found',
                'code' => 404
            ];
        }

        return [
            'success' => true,
            'user' => $user,
            'company' => $company,
            'session_data' => $sessionData
        ];
    }

    /**
     * Logout and invalidate session
     */
    public function logout(string $sessionToken): bool
    {
        return Cache::forget("webgl_session_{$sessionToken}");
    }
}
