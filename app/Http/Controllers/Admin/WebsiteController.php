<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Website;
use App\Services\DomainAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebsiteController extends Controller
{
    protected $domainAnalysisService;
    
    public function __construct(DomainAnalysisService $domainAnalysisService)
    {
        $this->domainAnalysisService = $domainAnalysisService;
    }

    /**
     * Display a listing of the websites.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $websites = Website::with(['user', 'latestDomainAnalysis'])
            ->latest()
            ->paginate(10);
            
        return view('admin.websites.index', compact('websites'));
    }

    /**
     * Show the form for creating a new website.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::all();
        return view('admin.websites.create', compact('users'));
    }

    /**
     * Store a newly created website in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'url' => 'required|url|unique:websites',
            'name' => 'required|string|max:255',
            'primary_keyword' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $website = Website::create($validated);

        // Run domain analysis in background (or queue) if needed
        try {
            $this->domainAnalysisService->analyze($website);
        } catch (\Exception $e) {
            Log::error('Domain analysis failed for website ' . $website->id . ': ' . $e->getMessage());
            // Don't stop the process, just log the error
        }

        return redirect()->route('admin.websites.index')
            ->with('success', 'Website created successfully and analysis initiated.');
    }

    /**
     * Show the form for editing the specified website.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\View\View
     */
    public function edit(Website $website)
    {
        $users = User::all();
        return view('admin.websites.edit', compact('website', 'users'));
    }

    /**
     * Update the specified website in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Website $website)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'url' => 'required|url|unique:websites,url,' . $website->id,
            'name' => 'required|string|max:255',
            'primary_keyword' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $website->update($validated);

        return redirect()->route('admin.websites.index')
            ->with('success', 'Website updated successfully.');
    }

    /**
     * Remove the specified website from storage.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Website $website)
    {
        $website->delete();

        return redirect()->route('admin.websites.index')
            ->with('success', 'Website deleted successfully.');
    }
    
    /**
     * Run a new domain analysis for the specified website.
     *
     * @param  \App\Models\Website  $website
     * @return \Illuminate\Http\RedirectResponse
     */
    public function analyze(Website $website)
    {
        try {
            $this->domainAnalysisService->analyze($website);
            return redirect()->route('admin.websites.edit', $website)
                ->with('success', 'Domain analysis completed successfully.');
        } catch (\Exception $e) {
            Log::error('Domain analysis failed for website ' . $website->id . ': ' . $e->getMessage());
            return redirect()->route('admin.websites.edit', $website)
                ->with('error', 'Domain analysis failed: ' . $e->getMessage());
        }
    }
}
