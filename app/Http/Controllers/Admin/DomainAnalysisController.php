<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainAnalysis;
use App\Models\Website;
use App\Services\DomainAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DomainAnalysisController extends Controller
{
    protected $domainAnalysisService;
    
    public function __construct(DomainAnalysisService $domainAnalysisService)
    {
        $this->domainAnalysisService = $domainAnalysisService;
    }

    /**
     * Display a listing of domain analyses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $analyses = DomainAnalysis::with('website')
            ->latest()
            ->paginate(10);
            
        return view('admin.domain-analysis.index', compact('analyses'));
    }

    /**
     * Display the specified domain analysis.
     *
     * @param  \App\Models\DomainAnalysis  $analysis
     * @return \Illuminate\View\View
     */
    public function show(DomainAnalysis $analysis)
    {
        return view('admin.domain-analysis.view', compact('analysis'));
    }
    
    /**
     * Run a domain analysis for the specified website.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
        ]);
        
        $website = Website::findOrFail($validated['website_id']);
        
        try {
            $analysis = $this->domainAnalysisService->analyze($website);
            
            return redirect()->route('admin.domain-analysis.show', $analysis)
                ->with('success', 'Domain analysis completed successfully.');
        } catch (\Exception $e) {
            Log::error('Domain analysis failed: ' . $e->getMessage());
            
            return redirect()->route('admin.domain-analysis.index')
                ->with('error', 'Domain analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified domain analysis from storage.
     *
     * @param  \App\Models\DomainAnalysis  $analysis
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(DomainAnalysis $analysis)
    {
        $analysis->delete();

        return redirect()->route('admin.domain-analysis.index')
            ->with('success', 'Domain analysis deleted successfully.');
    }
    
    /**
     * Export the domain analysis as PDF or JSON.
     *
     * @param  \App\Models\DomainAnalysis  $analysis
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(DomainAnalysis $analysis, Request $request)
    {
        $format = $request->query('format', 'json');
        
        if ($format === 'json') {
            $data = [
                'website' => $analysis->website->url,
                'date' => $analysis->created_at->toDateTimeString(),
                'seo_score' => $analysis->seo_score,
                'website_authority' => $analysis->website_authority,
                'backlinks_count' => $analysis->backlinks_count,
                'website_speed' => $analysis->website_speed,
                'issues' => json_decode($analysis->issues),
                'recommendations' => json_decode($analysis->recommendations),
            ];
            
            return response()->json($data);
        } elseif ($format === 'pdf') {
            // In a real application, you would generate a PDF here
            // For this example, we'll return a message
            return redirect()->route('admin.domain-analysis.show', $analysis)
                ->with('error', 'PDF export not implemented in this demo.');
        } else {
            return redirect()->route('admin.domain-analysis.show', $analysis)
                ->with('error', 'Invalid export format.');
        }
    }
}
