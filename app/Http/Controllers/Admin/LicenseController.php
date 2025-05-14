<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    /**
     * Display a listing of licenses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $licenses = License::with('user')
            ->latest()
            ->paginate(10);
            
        return view('admin.licenses.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new license.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::all();
        return view('admin.licenses.create', compact('users'));
    }

    /**
     * Store a newly created license in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan' => 'required|string|in:free,basic,pro,enterprise',
            'status' => 'required|string|in:pending,active,expired,cancelled',
            'max_websites' => 'required|integer|min:1',
            'max_content_per_month' => 'required|integer|min:1',
            'expires_at' => 'required|date',
        ]);

        // Generate a unique license key
        $licenseKey = strtoupper(Str::random(16));

        License::create([
            'user_id' => $validated['user_id'],
            'license_key' => $licenseKey,
            'plan' => $validated['plan'],
            'status' => $validated['status'],
            'max_websites' => $validated['max_websites'],
            'max_content_per_month' => $validated['max_content_per_month'],
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->route('admin.licenses.index')
            ->with('success', 'License created successfully.');
    }

    /**
     * Show the form for editing the specified license.
     *
     * @param  \App\Models\License  $license
     * @return \Illuminate\View\View
     */
    public function edit(License $license)
    {
        $users = User::all();
        return view('admin.licenses.edit', compact('license', 'users'));
    }

    /**
     * Update the specified license in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, License $license)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan' => 'required|string|in:free,basic,pro,enterprise',
            'status' => 'required|string|in:pending,active,expired,cancelled',
            'max_websites' => 'required|integer|min:1',
            'max_content_per_month' => 'required|integer|min:1',
            'expires_at' => 'required|date',
        ]);

        $license->update([
            'user_id' => $validated['user_id'],
            'plan' => $validated['plan'],
            'status' => $validated['status'],
            'max_websites' => $validated['max_websites'],
            'max_content_per_month' => $validated['max_content_per_month'],
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->route('admin.licenses.index')
            ->with('success', 'License updated successfully.');
    }

    /**
     * Remove the specified license from storage.
     *
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(License $license)
    {
        $license->delete();

        return redirect()->route('admin.licenses.index')
            ->with('success', 'License deleted successfully.');
    }
    
    /**
     * Generate a new license key for the specified license.
     *
     * @param  \App\Models\License  $license
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerateKey(License $license)
    {
        $licenseKey = strtoupper(Str::random(16));
        $license->update(['license_key' => $licenseKey]);
        
        return redirect()->route('admin.licenses.edit', $license)
            ->with('success', 'License key regenerated successfully.');
    }
}
