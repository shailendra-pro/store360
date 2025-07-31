<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businesses = User::where('role', 'business')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.businesses.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.businesses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_email' => 'required|email|unique:users,contact_email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $data = [
            'name' => $request->business_name,
            'business_name' => $request->business_name,
            'email' => $request->contact_email,
            'contact_email' => $request->contact_email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'business',
            'is_active' => $request->has('is_active'),
            'business_description' => $request->business_description,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'expires_at' => $request->expires_at,
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('business-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        User::create($data);

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business account created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        return view('admin.businesses.show', compact('business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        return view('admin.businesses.edit', compact('business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_email' => ['required', 'email', Rule::unique('users', 'contact_email')->ignore($business->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($business->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_description' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'expires_at' => 'nullable|date',
        ]);

        $data = [
            'name' => $request->business_name,
            'business_name' => $request->business_name,
            'email' => $request->contact_email,
            'contact_email' => $request->contact_email,
            'username' => $request->username,
            'is_active' => $request->has('is_active'),
            'business_description' => $request->business_description,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'expires_at' => $request->expires_at,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($business->logo_path && Storage::disk('public')->exists($business->logo_path)) {
                Storage::disk('public')->delete($business->logo_path);
            }

            $logoPath = $request->file('logo')->store('business-logos', 'public');
            $data['logo_path'] = $logoPath;
        }

        $business->update($data);

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business account updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        // Delete logo if exists
        if ($business->logo_path && Storage::disk('public')->exists($business->logo_path)) {
            Storage::disk('public')->delete($business->logo_path);
        }

        $business->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business account deleted successfully!');
    }

    /**
     * Toggle the active status of a business
     */
    public function toggleStatus(User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        $business->update([
            'is_active' => !$business->is_active
        ]);

        $status = $business->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.businesses.index')
            ->with('success', "Business account {$status} successfully!");
    }

    /**
     * Update business credentials (username and password)
     */
    public function updateCredentials(Request $request, User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($business->id)],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $business->update([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.businesses.show', $business)
            ->with('success', 'Business credentials updated successfully!');
    }

    /**
     * Update business logo
     */
    public function updateLogo(Request $request, User $business)
    {
        if ($business->role !== 'business') {
            abort(404);
        }

        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old logo if exists
        if ($business->logo_path && Storage::disk('public')->exists($business->logo_path)) {
            Storage::disk('public')->delete($business->logo_path);
        }

        $logoPath = $request->file('logo')->store('business-logos', 'public');
        $business->update(['logo_path' => $logoPath]);

        return redirect()->route('admin.businesses.show', $business)
            ->with('success', 'Business logo updated successfully!');
    }
}
