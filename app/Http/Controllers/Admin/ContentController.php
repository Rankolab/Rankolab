<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Website;
use App\Services\ContentGenerationService;
use App\Services\PlagiarismCheckService;
use App\Services\ReadabilityService;
use App\Services\SearchConsoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
    protected $contentService;
    protected $plagiarismService;
    protected $readabilityService;
    protected $searchConsoleService;
    
    public function __construct(
        ContentGenerationService $contentService,
        PlagiarismCheckService $plagiarismService,
        ReadabilityService $readabilityService,
        SearchConsoleService $searchConsoleService
    ) {
        $this->contentService = $contentService;
        $this->plagiarismService = $plagiarismService;
        $this->readabilityService = $readabilityService;
        $this->searchConsoleService = $searchConsoleService;
    }

    /**
     * Display a listing of the contents.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $contents = Content::with(['website', 'website.user'])
            ->latest()
            ->paginate(10);
            
        return view('admin.contents.index', compact('contents'));
    }

    /**
     * Show the form for creating a new content.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $websites = Website::all();
        return view('admin.contents.create', compact('websites'));
    }

    /**
     * Store a newly created content in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'title' => 'required|string|max:255',
            'target_keywords' => 'required|string|max:255',
            'min_words' => 'required|integer|min:1000|max:2500',
            'instructions' => 'nullable|string|max:2000',
        ]);
        
        // Create the content record
        $content = Content::create([
            'website_id' => $validated['website_id'],
            'title' => $validated['title'],
            'target_keywords' => $validated['target_keywords'],
            'min_words' => $validated['min_words'],
            'status' => 'pending',
            'instructions' => $validated['instructions'] ?? null,
        ]);
        
        // Generate the content
        try {
            $generatedContent = $this->contentService->generateContent(
                $content->website,
                $validated['title'],
                $validated['target_keywords'],
                $validated['min_words'],
                $validated['instructions'] ?? null
            );
            
            // Update the content with generated text
            $content->update([
                'content' => $generatedContent,
                'status' => 'generated',
                'generated_at' => now(),
            ]);
            
            // Check plagiarism in background or queue if needed
            $this->checkQuality($content);
            
            return redirect()->route('admin.contents.view', $content)
                ->with('success', 'Content generated successfully.');
        } catch (\Exception $e) {
            Log::error('Content generation failed: ' . $e->getMessage());
            $content->update(['status' => 'failed']);
            
            return redirect()->route('admin.contents.index')
                ->with('error', 'Content generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified content.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\View\View
     */
    public function show(Content $content)
    {
        return view('admin.contents.view', compact('content'));
    }

    /**
     * Show the form for editing the specified content.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\View\View
     */
    public function edit(Content $content)
    {
        $websites = Website::all();
        return view('admin.contents.edit', compact('content', 'websites'));
    }

    /**
     * Update the specified content in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'title' => 'required|string|max:255',
            'target_keywords' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:pending,generated,published,failed',
        ]);

        $content->update($validated);

        // If content was modified, recheck quality
        if ($request->has('content') && $content->content != $request->input('content')) {
            $this->checkQuality($content);
        }

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified content from storage.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Content $content)
    {
        $content->delete();

        return redirect()->route('admin.contents.index')
            ->with('success', 'Content deleted successfully.');
    }
    
    /**
     * Publish content to the website and submit to search console.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish(Content $content)
    {
        try {
            // Mark as published (in a real system, this would push to WordPress via API)
            $content->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
            
            // Submit to Search Console
            $this->searchConsoleService->submitUrl($content->website->url, $content->title);
            
            return redirect()->route('admin.contents.view', $content)
                ->with('success', 'Content published successfully and submitted to Search Console.');
        } catch (\Exception $e) {
            Log::error('Content publishing failed: ' . $e->getMessage());
            
            return redirect()->route('admin.contents.view', $content)
                ->with('error', 'Content publishing failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Check content quality (plagiarism and readability)
     *
     * @param  \App\Models\Content  $content
     * @return void
     */
    private function checkQuality(Content $content)
    {
        try {
            // Check plagiarism
            $plagiarismScore = $this->plagiarismService->check($content->content);
            
            // Check readability
            $readabilityScore = $this->readabilityService->analyze($content->content);
            
            // Update content with quality metrics
            $content->update([
                'plagiarism_score' => $plagiarismScore,
                'readability_score' => $readabilityScore,
                'quality_checked_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Content quality check failed: ' . $e->getMessage());
            // Continue without failing, just log the error
        }
    }
    
    /**
     * Regenerate the content.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function regenerate(Content $content)
    {
        try {
            $generatedContent = $this->contentService->generateContent(
                $content->website,
                $content->title,
                $content->target_keywords,
                $content->min_words,
                $content->instructions
            );
            
            // Update the content with new generated text
            $content->update([
                'content' => $generatedContent,
                'status' => 'generated',
                'generated_at' => now(),
            ]);
            
            // Check quality
            $this->checkQuality($content);
            
            return redirect()->route('admin.contents.view', $content)
                ->with('success', 'Content regenerated successfully.');
        } catch (\Exception $e) {
            Log::error('Content regeneration failed: ' . $e->getMessage());
            
            return redirect()->route('admin.contents.view', $content)
                ->with('error', 'Content regeneration failed: ' . $e->getMessage());
        }
    }
}
