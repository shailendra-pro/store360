<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by company name
        if ($request->filled('company')) {
            $query->byCompany($request->company);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->where("role", "user")->orderBy('created_at', 'desc')->paginate(15);

        // Get unique companies for filter dropdown
        $companies = User::whereNotNull('company')->distinct()->pluck('company');

        return view('admin.users.index', compact('users', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get unique companies for dropdown
        $companies = User::whereNotNull('company')->distinct()->pluck('company');

        return view('admin.users.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'company' => 'nullable|string|max:255',
            'role' => 'required|in:user,admin,business',
            'custom_hours' => 'nullable|integer|min:1|max:8760', // Max 1 year
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'company' => $request->company,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_active' => $request->has('is_active'),
        ]);

        // Generate secure link if custom hours provided
        if ($request->filled('custom_hours')) {
            $user->generateSecureLink($request->custom_hours);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Get unique companies for dropdown
        $companies = User::whereNotNull('company')->distinct()->pluck('company');

        return view('admin.users.edit', compact('user', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'company' => 'nullable|string|max:255',
            'role' => 'required|in:user,admin,business',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'company' => $request->company,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Generate secure link for user
     */
    public function generateSecureLink(Request $request, User $user)
    {
        $request->validate([
            'hours' => 'required|integer|min:1|max:8760', // Max 1 year
        ]);

        $secureLink = $user->generateSecureLink($request->hours);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "Secure link generated successfully! Expires in {$request->hours} hours.");
    }

    /**
     * Extend secure link expiration
     */
    public function extendSecureLink(Request $request, User $user)
    {
        $request->validate([
            'additional_hours' => 'required|integer|min:1|max:8760', // Max 1 year
        ]);

        if (!$user->secure_link) {
            return back()->withErrors(['message' => 'No secure link exists for this user.']);
        }

        $user->extendSecureLink($request->additional_hours);

        return redirect()->route('admin.users.show', $user)
            ->with('success', "Secure link extended by {$request->additional_hours} hours.");
    }

    /**
     * Revoke secure link
     */
    public function revokeSecureLink(User $user)
    {
        $user->update([
            'secure_link' => null,
            'secure_link_expires_at' => null,
            'custom_hours' => null,
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Secure link revoked successfully!');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully!");
    }

    /**
     * Get secure link statistics
     */
    public function secureLinkStats()
    {
        $stats = [
            'total_users' => User::count(),
            'users_with_links' => User::whereNotNull('secure_link')->count(),
            'valid_links' => User::withValidSecureLinks()->count(),
            'expired_links' => User::whereNotNull('secure_link')
                                 ->where('secure_link_expires_at', '<', now())
                                 ->count(),
        ];

        return response()->json($stats);
    }
}
