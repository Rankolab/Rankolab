<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\License;
use App\Models\Website;
use App\Services\ContentGenerationService;
use App\Services\PlagiarismCheckService;
use App\Services\ReadabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentGenerationController extends Controller
{
    protected $contentService;
    protected $plagiarismService;
    protected $readabilityService;
    
    public function __construct(
        ContentGenerationService $contentService,
        PlagiarismCheckService $plagiarismService,
        ReadabilityService $readabilityService
    ) {
        $this->contentService = $contentService;
        $this->plagiarismService = $plagiarismService;
        $this->readabilityService = $readabilityService;
    }

    /**
     * Generate content based on provided parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'required|string',
            'title' => 'required|string|max:255',
            'keywords' => 'required|string|max:255',
            'min_words' => 'required|integer|min:1000|max:2500',
            'instructions' => 'nullable|string|max:2000',
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
        
        // Check if license has expired
        if ($license->expires_at && now()->gt($license->expires_at)) {
            $license->update(['status' => 'expired']);
            
            return response()->json([
                'success' => false,
                'message' => 'License has expired',
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
        
        // Check content generation limit for the month
        $contentThisMonth = $license->user->websites()
            ->with(['contents' => function ($query) {
                $query->whereYear('created_at', now()->year)
                      ->whereMonth('created_at', now()->month);
            }])
            ->get()
            ->pluck('contents')
            ->flatten()
            ->count();
            
        if ($contentThisMonth >= $license->max_content_per_month) {
            return response()->json([
                'success' => false,
                'message' => 'Monthly content generation limit reached',
            ], 403);
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
            // Generate content
            $generatedContent = $this->contentService->generateContent(
                $website,
                $validated['title'],
                $validated['keywords'],
                $validated['min_words'],
                $validated['instructions'] ?? null,
                $license->plan
            );
            
            // Save content
            $content = Content::create([
                'website_id' => $website->id,
                'title' => $validated['title'],
                'target_keywords' => $validated['keywords'],
                'content' => $generatedContent,
                'min_words' => $validated['min_words'],
                'word_count' => str_word_count($generatedContent),
                'status' => 'generated',
                'instructions' => $validated['instructions'] ?? null,
                'generated_at' => now(),
            ]);
            
            // Check quality if plan supports it
            $features = $this->getFeaturesForPlan($license->plan);
            
            $response = [
                'success' => true,
                'message' => 'Content generated successfully',
                'data' => [
                    'content_id' => $content->id,
                    'title' => $content->title,
                    'content' => $content->content,
                    'word_count' => $content->word_count,
                ]
            ];
            
            // Only run these checks if the plan supports them
            if ($features['readability_check']) {
                $readabilityScore = $this->readabilityService->analyze($generatedContent);
                $content->update(['readability_score' => $readabilityScore]);
                $response['data']['readability_score'] = $readabilityScore;
            }
            
            if ($features['plagiarism_check']) {
                $plagiarismScore = $this->plagiarismService->check($generatedContent);
                $content->update(['plagiarism_score' => $plagiarismScore]);
                $response['data']['plagiarism_score'] = $plagiarismScore;
            }
            
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Content generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Content generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get a specific content record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContent(Request $request, $id)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Find content
        $content = Content::find($id);
        
        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found',
            ], 404);
        }
        
        // Check if content belongs to user's websites
        $userWebsiteIds = $license->user->websites()->pluck('id')->toArray();
        
        if (!in_array($content->website_id, $userWebsiteIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this content',
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'content_id' => $content->id,
                'title' => $content->title,
                'content' => $content->content,
                'keywords' => $content->target_keywords,
                'word_count' => $content->word_count,
                'readability_score' => $content->readability_score,
                'plagiarism_score' => $content->plagiarism_score,
                'generated_at' => $content->generated_at->toDateTimeString(),
            ],
        ]);
    }
    
    /**
     * List all content for a license.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listContent(Request $request)
    {
        $validated = $request->validate([
            'license_key' => 'required|string',
            'domain' => 'nullable|string',
        ]);
        
        // Validate license
        $license = License::where('license_key', $validated['license_key'])->first();
        
        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid license key',
            ], 401);
        }
        
        // Get websites for user
        $query = $license->user->websites();
        
        // Filter by domain if provided
        if ($request->has('domain') && $request->domain) {
            $query = $query->where('url', $validated['domain']);
        }
        
        $websites = $query->with(['contents' => function ($query) {
            $query->select('id', 'website_id', 'title', 'status', 'word_count', 'created_at')
                  ->orderBy('created_at', 'desc');
        }])->get();
        
        $contents = [];
        
        foreach ($websites as $website) {
            foreach ($website->contents as $content) {
                $contents[] = [
                    'content_id' => $content->id,
                    'website' => $website->url,
                    'title' => $content->title,
                    'status' => $content->status,
                    'word_count' => $content->word_count,
                    'created_at' => $content->created_at->toDateTimeString(),
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $contents,
        ]);
    }
    
    /**
     * Get features available for a specific plan.
     *
     * @param  string  $plan
     * @return array
     */
    private function getFeaturesForPlan($plan)
    {
        $features = [
            'free' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => false,
                'ai_detection_bypass' => false,
                'seo_optimization' => false,
                'search_console_integration' => false,
            ],
            'basic' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => true,
                'ai_detection_bypass' => false,
                'seo_optimization' => true,
                'search_console_integration' => false,
            ],
            'pro' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => true,
                'ai_detection_bypass' => true,
                'seo_optimization' => true,
                'search_console_integration' => true,
            ],
            'enterprise' => [
                'content_generation' => true,
                'domain_analysis' => true,
                'readability_check' => true,
                'plagiarism_check' => true,
                'ai_detection_bypass' => true,
                'seo_optimization' => true,
                'search_console_integration' => true,
                'custom_ai_model' => true,
                'priority_support' => true,
            ],
        ];
        
        return $features[$plan] ?? $features['free'];
    }
}
