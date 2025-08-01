<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;

class WebGLController extends Controller
{
    /**
     * Validate secure link and show WebGL login page or expired message
     */
    public function validateAccess(Request $request)
    {
        $secureLink = $request->query('link') ?? $request->query('token');

        if (!$secureLink) {
            return view('webgl.expired', [
                'message' => 'Invalid access link. Please contact support for a valid link.',
                'title' => 'Invalid Access'
            ]);
        }

        // Find user with this secure link
        $user = User::where('secure_link', $secureLink)->first();

        if (!$user) {
            return view('webgl.expired', [
                'message' => 'Invalid access link. Please contact support for a valid link.',
                'title' => 'Invalid Access'
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            return view('webgl.expired', [
                'message' => 'Your account has been deactivated. Please contact support.',
                'title' => 'Account Deactivated'
            ]);
        }

        // Handle different user types
        if ($user->role === 'user') {
            // End user - check session expiry
            if (!$user->isSecureLinkValid()) {
                return view('webgl.expired', [
                    'message' => 'Your access link has expired. Please contact support or request new access.',
                    'title' => 'Access Expired',
                    'expired_at' => $user->expires_at,
                    'company' => $user->company
                ]);
            }

            // Get company information
            $company = User::where('role', 'business')
                          ->where('business_name', $user->company)
                          ->first();

            if (!$company) {
                return view('webgl.expired', [
                    'message' => 'Company not found. Please contact support.',
                    'title' => 'Company Not Found'
                ]);
            }

            $userType = 'end_user';
            $userEmail = $user->email;
            $userName = $user->name;
            $companyName = $company->business_name;
            $companyLogo = $company->logo_url;

        } elseif ($user->role === 'business') {
            // Business user - check if business account is active
            if (!$user->isBusinessActive()) {
                return view('webgl.expired', [
                    'message' => 'Your business account is not active. Please contact administrator.',
                    'title' => 'Account Inactive'
                ]);
            }

            $userType = 'business';
            $userEmail = $user->contact_email;
            $userName = $user->business_name;
            $companyName = $user->business_name;
            $companyLogo = $user->logo_url;

        } else {
            return view('webgl.expired', [
                'message' => 'Invalid user type. Please contact support.',
                'title' => 'Invalid User Type'
            ]);
        }

        // Redirect to WebGL login page with parameters
        $webglLoginUrl = config('app.webgl_login_url', 'https://your-webgl-app.com/login');

        $redirectUrl = $webglLoginUrl;

        return redirect($redirectUrl);
    }

    /**
     * Show expired access page
     */
    public function expired()
    {
        return view('webgl.expired', [
            'message' => 'Your access link has expired or is invalid.',
            'title' => 'Access Expired'
        ]);
    }
}
