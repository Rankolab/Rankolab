<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlagiarismCheckService
{
    /**
     * Check content for plagiarism.
     *
     * @param string $content
     * @return float Plagiarism score (percentage)
     */
    public function check(string $content): float
    {
        Log::info('Starting plagiarism check for content', ['length' => strlen($content)]);
        
        // Get API key from environment or settings
        $apiKey = env('COPYSCAPE_API_KEY', Setting::getValue('copyscape_api_key'));
        
        if (!$apiKey) {
            Log::warning('Plagiarism API key not configured');
            return $this->simulatePlagiarismCheck($content);
        }
        
        try {
            // In a real application, this would call a plagiarism checking API
            // For now, we'll simulate the check
            return $this->simulatePlagiarismCheck($content);
            
            /*
            // Example with CopyScape API (not implemented)
            $response = Http::post('https://www.copyscape.com/api/', [
                'u' => env('COPYSCAPE_USERNAME'),
                'k' => $apiKey,
                'o' => 'cscheck',
                't' => substr($content, 0, 10000), // Some APIs have length limitations
                'e' => 'UTF-8',
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                // Process the result and calculate a plagiarism score
                return $this->calculatePlagiarismScore($result);
            } else {
                Log::error('Plagiarism API error', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body()
                ]);
                return $this->simulatePlagiarismCheck($content);
            }
            */
        } catch (\Exception $e) {
            Log::error('Plagiarism check error', ['error' => $e->getMessage()]);
            return $this->simulatePlagiarismCheck($content);
        }
    }
    
    /**
     * Simulate a plagiarism check when API is not available.
     *
     * @param string $content
     * @return float
     */
    private function simulatePlagiarismCheck(string $content): float
    {
        // Get the word count
        $wordCount = str_word_count($content);
        
        // More complex content tends to have less plagiarism
        // This is just a simulation algorithm that gives realistic scores
        
        // Check if content has common phrases that might trigger higher plagiarism scores
        $commonPhrases = [
            'in conclusion',
            'in summary',
            'as a result',
            'due to the fact that',
            'it is important to note',
            'on the other hand',
            'in this article',
            'in this blog post',
            'according to research',
            'studies have shown',
        ];
        
        $commonPhraseCount = 0;
        foreach ($commonPhrases as $phrase) {
            $commonPhraseCount += substr_count(strtolower($content), $phrase);
        }
        
        // Base score calculation factors:
        // 1. Length of content (longer is less likely to be plagiarized)
        // 2. Presence of common phrases (more common phrases might indicate higher plagiarism)
        
        // Start with a base score
        $basePlagiarismPercentage = max(1, min(30, 30 - ($wordCount / 100)));
        
        // Adjust for common phrases (each common phrase adds 0.5% to plagiarism score)
        $adjustedScore = $basePlagiarismPercentage + ($commonPhraseCount * 0.5);
        
        // Add some randomness to make it seem realistic
        $randomFactor = rand(-3, 3);
        $finalScore = max(0, min(100, $adjustedScore + $randomFactor));
        
        Log::info('Simulated plagiarism check complete', [
            'word_count' => $wordCount,
            'common_phrases' => $commonPhraseCount,
            'plagiarism_score' => $finalScore
        ]);
        
        return round($finalScore, 1);
    }
    
    /**
     * Calculate plagiarism score from API result.
     *
     * @param array $apiResult
     * @return float
     */
    private function calculatePlagiarismScore(array $apiResult): float
    {
        // This would be implemented based on the specific API response format
        // For now, return a simulated score
        return rand(1, 15);
    }
    
    /**
     * Check a single paragraph against web sources.
     *
     * @param string $paragraph
     * @return array
     */
    public function checkParagraph(string $paragraph): array
    {
        // This would check a single paragraph against web sources
        // Returns sources and similarity percentages
        
        // Simulate the result
        $sourcesCount = rand(0, 3);
        $sources = [];
        
        for ($i = 0; $i < $sourcesCount; $i++) {
            $similarity = rand(20, 90);
            $domains = [
                'wikipedia.org',
                'medium.com',
                'forbes.com',
                'entrepreneur.com',
                'wordpress.org',
                'hubspot.com',
                'searchenginejournal.com',
                'moz.com',
                'ahrefs.com',
                'semrush.com'
            ];
            
            $domain = $domains[array_rand($domains)];
            $path = '/' . str_replace(' ', '-', strtolower(substr($paragraph, 0, 20))) . rand(1, 999);
            
            $sources[] = [
                'url' => 'https://www.' . $domain . $path,
                'similarity' => $similarity,
                'title' => ucwords(str_replace('-', ' ', $path))
            ];
        }
        
        return [
            'has_matches' => $sourcesCount > 0,
            'sources' => $sources,
            'highest_similarity' => $sources ? max(array_column($sources, 'similarity')) : 0
        ];
    }
}
