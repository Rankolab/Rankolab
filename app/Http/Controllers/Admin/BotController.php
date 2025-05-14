<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContentGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{
    protected $contentService;
    
    public function __construct(ContentGenerationService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Display the bot testing interface.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.bot.index');
    }
    
    /**
     * Test content generation with given parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testGenerate(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'keywords' => 'required|string|max:255',
            'length' => 'required|integer|min:100|max:2500',
            'instructions' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Get the AI model from settings
            $aiModel = config('services.ai.model', 'gpt-3.5-turbo');
            
            // Generate test content
            $content = $this->contentService->testGeneration(
                $validated['topic'],
                $validated['keywords'],
                $validated['length'],
                $validated['instructions'] ?? null,
                $aiModel
            );
            
            return response()->json([
                'success' => true,
                'content' => $content,
                'word_count' => str_word_count($content),
                'model_used' => $aiModel,
            ]);
        } catch (\Exception $e) {
            Log::error('Bot test generation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Analyze content for SEO and readability.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function analyzeContent(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'keywords' => 'required|string|max:255',
        ]);
        
        try {
            // In a real app, this would call SEO analysis services
            $analysis = $this->contentService->analyzeContent(
                $validated['content'],
                $validated['keywords']
            );
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
            ]);
        } catch (\Exception $e) {
            Log::error('Content analysis failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Check if content passes AI detection tests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAiDetection(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);
        
        try {
            // In a real app, this would call AI detection APIs
            $result = $this->contentService->checkAiDetection($validated['content']);
            
            return response()->json([
                'success' => true,
                'passes_detection' => $result['passes'],
                'humanlike_score' => $result['score'],
                'recommendations' => $result['recommendations'],
            ]);
        } catch (\Exception $e) {
            Log::error('AI detection check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
