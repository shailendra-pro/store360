<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecureLinkMail;
use Illuminate\support\Facades\Log;

class BusinessController extends Controller
{
    /**
     * Show company dashboard with user management
     */
    public function dashboard()
    {
        $business = Auth::user();

        // Get company's end users
        $users = User::where('role', 'user')
                    ->where('company', $business->business_name)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Get statistics
        $stats = [
            'total_users' => $users->count(),
            'active_users' => $users->where('is_active', true)->count(),
            'users_with_links' => $users->whereNotNull('secure_link')->count(),
            'valid_links' => $users->whereNotNull('secure_link')
                                 ->where('secure_link_expires_at', '>', now())
                                 ->count(),
        ];

        return view('business.dashboard', compact('business', 'users', 'stats'));
    }

    /**
     * Show company user management page
     */
    public function users()
    {
        $business = Auth::user();

        $users = User::where('role', 'user')
                    ->where('company', $business->business_name)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('business.users.index', compact('business', 'users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        $business = Auth::user();
        return view('business.users.create', compact('business'));
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $business = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ]);

        // Generate secure link
        $secureLink = Str::random(32);
        $expiresAt = now()->addHours(48);
        $password = $request->password;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 'user',
            'password' => Hash::make($password),
            'company' => $business->business_name,
            'is_active' => true,
            'secure_link' => $secureLink,
            'secure_link_expires_at' => $expiresAt,
        ]);

        // Send email with secure link
        $this->sendSecureLinkEmail($user);

        return redirect()->route('business.users.index')
                        ->with('success', 'User created successfully. Secure link sent to their email.');
    }

    /**
     * Show user details
     */
    public function showUser($id)
    {
        $business = Auth::user();

        $user = User::where('role', 'user')
                    ->where('company', $business->business_name)
                    ->findOrFail($id);

        return view('business.users.show', compact('business', 'user'));
    }

    /**
     * Toggle user status
     */
    public function toggleUserStatus($id)
    {
        $business = Auth::user();

        $user = User::where('role', 'user')
                   ->where('company', $business->business_name)
                   ->findOrFail($id);

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Generate new secure link for user
     */
    public function generateSecureLink($id)
    {
        $business = Auth::user();

        $user = User::where('role', 'user')
                   ->where('company', $business->business_name)
                   ->findOrFail($id);

        $secureLink = Str::random(32);
        $expiresAt = now()->addHours(48);

        $user->update([
            'secure_link' => $secureLink,
            'secure_link_expires_at' => $expiresAt,
        ]);

        // Send email with new secure link
        $this->sendSecureLinkEmail($user);

        return back()->with('success', 'New secure link generated and sent to user.');
    }

    /**
     * Extend secure link expiration
     */
    public function extendSecureLink(Request $request, $id)
    {
        $business = Auth::user();

        $user = User::where('role', 'user')
                   ->where('company', $business->business_name)
                   ->findOrFail($id);

        $hours = $request->input('hours', 24);
        $user->update([
            'secure_link_expires_at' => $user->secure_link_expires_at->addHours($hours)
        ]);

        return back()->with('success', "Secure link extended by {$hours} hours.");
    }

    /**
     * Show logo management page
     */
    public function logo()
    {
        $business = Auth::user();
        return view('business.logo.index', compact('business'));
    }

    /**
     * Update company logo
     */
    public function updateLogo(Request $request)
    {
        $business = Auth::user();

        $request->validate([
            'logo' => 'required|image|mimes:png|max:2048|dimensions:min_width=300,min_height=100',
        ]);

        // Delete old logo if exists
        if ($business->logo && Storage::disk('public')->exists($business->logo)) {
            Storage::disk('public')->delete($business->logo);
        }

        // Store new logo
        $logoPath = $request->file('logo')->store('business-logos', 'public');

        $business->update(['logo_path' => $logoPath]);

        return back()->with('success', 'Logo updated successfully.');
    }

    /**
     * Delete company logo
     */
    public function deleteLogo()
    {
        $business = Auth::user();

        if ($business->logo && Storage::disk('public')->exists($business->logo)) {
            Storage::disk('public')->delete($business->logo);
        }

        $business->update(['logo' => null]);

        return back()->with('success', 'Logo deleted successfully.');
    }

    /**
     * Send secure link email to user
     */
    private function sendSecureLinkEmail($user)
    {
        $secureUrl = route('secure.access', $user->secure_link);

        // For now, we'll just log the email. In production, you'd send actual emails
        \Log::info("Secure link email would be sent to {$user->email}: {$secureUrl}");

        // TODO: Implement actual email sending
        // Mail::to($user->email)->send(new SecureLinkMail($user, $secureUrl));
    }
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8'
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('password'));

        return back()->with('success', 'User updated successfully.');
    }

    public function resendSecureLinkEmail(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->secure_link) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No secure link exists for this user.'
                ], 404);
            }

            return back()->withErrors(['message' => 'No secure link exists for this user.']);
        }

        try {
            $response = Mail::to($user->email)->send(new SecureLinkMail($user, $user->secure_link_url, $user->email));
            \Log::info('Mail send response:', ['response' => $response]);
        } catch (\Throwable $e) {
            \Log::error('Mail send error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Secure link email resent successfully.'
            ]);
        }

        return back()->with('success', 'Secure link email resent successfully.');
    }

}
