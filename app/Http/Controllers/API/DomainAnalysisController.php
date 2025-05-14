<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DomainAnalysis;
use App\Models\License;
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
     * Analyze a domain and return SEO metrics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Check if license is active
        if ($license->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'License is ' . $license->status,
            ], 401);
        }
        
        // Check if domain is allowed
        $domains = $license->domains ? json_decode($license->domains, true) : [];
        
        if (!in_array($validated['domain'], $domains)) {
            return response()->json([
                'success' => false,
                'message' => 'Domain not registered with this license',
            ], 401);
        }
        
        // Get or create website
        $website = Website::firstOrCreate(
            ['url' => $validated['domain']],
            [
                'user_id' => $license->user_id,
                'name' => $validated['domain'],
            ]
        );
        
        try {
            // Run domain analysis
            $analysis = $this->domainAnalysisService->analyze($website);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'analysis_id' => $analysis->id,
                    'website' => $website->url,
                    'seo_score' => $analysis->seo_score,
                    'website_authority' => $analysis->website_authority,
                    'backlinks_count' => $analysis->backlinks_count,
                    'website_speed' => $analysis->website_speed,
                    'issues' => json_decode($analysis->issues),
                    'recommendations' => json_decode($analysis->recommendations),
                    'analyzed_at' => $analysis->created_at->toDateTimeString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Domain analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Domain analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get the latest domain analysis for a website.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestAnalysis(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Find website
        $website = Website::where('url', $validated['domain'])->first();
        
        if (!$website) {
            return response()->json([
                'success' => false,
                'message' => 'Website not found',
            ], 404);
        }
        
        // Check if website belongs to user
        if ($website->user_id !== $license->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this website',
            ], 403);
        }
        
        // Get latest analysis
        $analysis = $website->analyses()->latest()->first();
        
        if (!$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'No analysis found for this domain',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'analysis_id' => $analysis->id,
                'website' => $website->url,
                'seo_score' => $analysis->seo_score,
                'website_authority' => $analysis->website_authority,
                'backlinks_count' => $analysis->backlinks_count,
                'website_speed' => $analysis->website_speed,
                'issues' => json_decode($analysis->issues),
                'recommendations' => json_decode($analysis->recommendations),
                'analyzed_at' => $analysis->created_at->toDateTimeString(),
            ],
        ]);
    }
    
    /**
     * Get keyword suggestions for a domain.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKeywordSuggestions(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
            'seed_keyword' => 'required|string|max:100',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        try {
            // Get keyword suggestions
            $suggestions = $this->domainAnalysisService->getKeywordSuggestions(
                $validated['domain'],
                $validated['seed_keyword']
            );
            
            return response()->json([
                'success' => true,
                'data' => [
                    'seed_keyword' => $validated['seed_keyword'],
                    'domain' => $validated['domain'],
                    'suggestions' => $suggestions,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Keyword suggestions failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Keyword suggestions failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
