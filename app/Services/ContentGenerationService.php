<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Website;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentGenerationService
{
    /**
     * Generate content based on website information and keywords.
     *
     * @param Website $website
     * @param string $title
     * @param string $keywords
     * @param int $minWords
     * @param string|null $instructions
     * @param string $plan
     * @return string
     */
    public function generateContent(
        Website $website, 
        string $title, 
        string $keywords, 
        int $minWords = 1000, 
        ?string $instructions = null,
        string $plan = 'free'
    ): string {
        // Get domain analysis for context if available
        $domainAnalysis = $website->latestDomainAnalysis;
        $websiteContext = '';
        
        if ($domainAnalysis) {
            $websiteContext = "Website SEO Score: {$domainAnalysis->seo_score}/100. ";
            $websiteContext .= "Website Authority: {$domainAnalysis->website_authority}/100. ";
            
            if (!empty($domainAnalysis->issues)) {
                $issues = json_decode($domainAnalysis->issues, true);
                if (is_array($issues) && count($issues) > 0) {
                    $websiteContext .= "Top SEO issues: ";
                    foreach (array_slice($issues, 0, 3) as $issue) {
                        $websiteContext .= "{$issue['title']} - {$issue['description']}. ";
                    }
                }
            }
        }
        
        // Prepare prompt
        $prompt = $this->buildContentPrompt(
            $title,
            $keywords,
            $minWords,
            $website->url,
            $website->primary_keyword ?? '',
            $instructions,
            $websiteContext,
            $plan
        );
        
        // Get AI model based on plan
        $aiModel = $this->getAiModelForPlan($plan);
        
        // Call OpenAI API
        return $this->callContentGenerationApi($prompt, $aiModel);
    }
    
    /**
     * Test content generation with direct parameters.
     *
     * @param string $topic
     * @param string $keywords
     * @param int $length
     * @param string|null $instructions
     * @param string $model
     * @return string
     */
    public function testGeneration(
        string $topic,
        string $keywords,
        int $length = 1000,
        ?string $instructions = null,
        string $model = 'gpt-3.5-turbo'
    ): string {
        $prompt = $this->buildContentPrompt(
            $topic,
            $keywords,
            $length,
            'example.com',
            '',
            $instructions
        );
        
        return $this->callContentGenerationApi($prompt, $model);
    }
    
    /**
     * Build the prompt for content generation.
     *
     * @param string $title
     * @param string $keywords
     * @param int $wordCount
     * @param string $websiteUrl
     * @param string $primaryKeyword
     * @param string|null $instructions
     * @param string $websiteContext
     * @param string $plan
     * @return string
     */
    private function buildContentPrompt(
        string $title,
        string $keywords,
        int $wordCount,
        string $websiteUrl,
        string $primaryKeyword = '',
        ?string $instructions = null,
        string $websiteContext = '',
        string $plan = 'free'
    ): string {
        $bypassAiDetection = in_array($plan, ['pro', 'enterprise']);
        
        $prompt = "Write a high-quality, SEO-optimized blog post on the topic: \"{$title}\". ";
        $prompt .= "The post should be at least {$wordCount} words long and target these keywords: {$keywords}. ";
        
        if ($primaryKeyword) {
            $prompt .= "The primary keyword for the website is: {$primaryKeyword}. ";
        }
        
        if ($websiteContext) {
            $prompt .= "Website Context: {$websiteContext} ";
        }
        
        $prompt .= "The blog post should include:
        1. A catchy introduction that hooks the reader
        2. A table of contents with clear headings and subheadings
        3. Well-structured content with bullet points and numbered lists where appropriate
        4. A conclusion that summarizes key points
        5. Simple language suitable for a general audience (around 6th grade reading level)
        
        SEO Requirements:
        - Include the main keyword in the first paragraph
        - Use semantic variations of keywords throughout
        - Include at least 3 subheadings (H2s)
        - Maintain a keyword density of around 1-2%
        - Include transition phrases between sections
        
        Content Quality:
        - Ensure the content is 100% unique and plagiarism-free
        - Write in a conversational, engaging tone
        - Avoid overly complex sentences and jargon
        - Provide actionable advice and valuable information";
        
        if ($bypassAiDetection) {
            $prompt .= "\n\nVery important: The content must appear completely human-written and pass AI detection tools. Vary sentence structures, use idioms, occasional colloquialisms, and imperfect grammar where it sounds natural. Avoid repetitive patterns and predictable transitions.";
        }
        
        if ($instructions) {
            $prompt .= "\n\nAdditional Instructions: {$instructions}";
        }
        
        return $prompt;
    }
    
    /**
     * Call content generation API.
     *
     * @param string $prompt
     * @param string $model
     * @return string
     */
    private function callContentGenerationApi(string $prompt, string $model): string
    {
        // Get API key from environment
        $apiKey = env('OPENAI_API_KEY', Setting::getValue('openai_api_key'));
        
        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }
        
        try {
            // Call OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert SEO content writer. Write engaging, informative content optimized for search engines.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.7,
                'max_tokens' => 4000
            ]);
            
            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';
                
                if (empty($content)) {
                    throw new \Exception('Empty response from API');
                }
                
                return $content;
            } else {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new \Exception('API error: ' . ($response->json()['error']['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Content generation error', ['error' => $e->getMessage()]);
            throw new \Exception('Content generation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the AI model to use based on plan.
     *
     * @param string $plan
     * @return string
     */
    private function getAiModelForPlan(string $plan): string
    {
        $models = [
            'free' => 'gpt-3.5-turbo',
            'basic' => 'gpt-3.5-turbo',
            'pro' => 'gpt-4',
            'enterprise' => env('CUSTOM_AI_MODEL', 'gpt-4')
        ];
        
        return $models[$plan] ?? 'gpt-3.5-turbo';
    }
    
    /**
     * Analyze content for SEO optimization and readability.
     *
     * @param string $content
     * @param string $keywords
     * @return array
     */
    public function analyzeContent(string $content, string $keywords): array
    {
        $keywordsArray = array_map('trim', explode(',', $keywords));
        $wordCount = str_word_count($content);
        
        $analysis = [
            'word_count' => $wordCount,
            'keyword_density' => [],
            'readability_score' => 0,
            'headings_count' => 0,
            'issues' => [],
            'recommendations' => [],
        ];
        
        // Check keyword density
        foreach ($keywordsArray as $keyword) {
            $count = substr_count(strtolower($content), strtolower($keyword));
            $density = round(($count / $wordCount) * 100, 2);
            
            $analysis['keyword_density'][$keyword] = $density;
            
            if ($density < 0.5) {
                $analysis['issues'][] = "Low keyword density for '{$keyword}' ({$density}%)";
                $analysis['recommendations'][] = "Try to increase mentions of '{$keyword}' to reach at least 0.5%";
            } elseif ($density > 3) {
                $analysis['issues'][] = "Keyword stuffing detected for '{$keyword}' ({$density}%)";
                $analysis['recommendations'][] = "Reduce mentions of '{$keyword}' to below 3% to avoid over-optimization";
            }
        }
        
        // Count headings
        preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $headings);
        $analysis['headings_count'] = count($headings[0]);
        
        if ($analysis['headings_count'] < 3) {
            $analysis['issues'][] = "Only {$analysis['headings_count']} headings found";
            $analysis['recommendations'][] = "Add more section headings (h2, h3) to improve structure";
        }
        
        // Check content length
        if ($wordCount < 600) {
            $analysis['issues'][] = "Content length is below recommended minimum (current: {$wordCount} words)";
            $analysis['recommendations'][] = "Add more content to reach at least 600 words for better SEO performance";
        }
        
        // Calculate basic readability (simplified)
        $sentences = preg_split('/[.!?]+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        $sentenceCount = count($sentences);
        
        if ($sentenceCount > 0) {
            $totalWords = str_word_count($content);
            $wordsPerSentence = $totalWords / $sentenceCount;
            
            // Very simplified readability calculation
            if ($wordsPerSentence > 25) {
                $analysis['issues'][] = "Sentences are too long (average: ".round($wordsPerSentence, 1)." words)";
                $analysis['recommendations'][] = "Shorten sentences to improve readability";
                $analysis['readability_score'] = 60;
            } elseif ($wordsPerSentence > 20) {
                $analysis['readability_score'] = 70;
            } elseif ($wordsPerSentence > 15) {
                $analysis['readability_score'] = 80;
            } else {
                $analysis['readability_score'] = 90;
            }
        }
        
        return $analysis;
    }
    
    /**
     * Check if content passes AI detection.
     *
     * @param string $content
     * @return array
     */
    public function checkAiDetection(string $content): array
    {
        // In a real application, this would call an actual AI detection API
        // For now, we'll simulate it with a basic analysis
        
        $contentSample = substr($content, 0, 1000); // Analyze first 1000 chars
        
        // Count sentence patterns for variety
        preg_match_all('/[.!?]+/', $contentSample, $sentenceEnds);
        $sentenceCount = count($sentenceEnds[0]);
        
        // Check for variety in sentence endings
        $uniqueSentenceEnds = count(array_unique($sentenceEnds[0]));
        $sentenceVarietyRatio = $sentenceCount > 0 ? $uniqueSentenceEnds / $sentenceCount : 0;
        
        // Check for passive voice (simplified check)
        $passiveVoiceCount = substr_count(strtolower($contentSample), ' was ') + 
                            substr_count(strtolower($contentSample), ' were ') +
                            substr_count(strtolower($contentSample), ' been ') +
                            substr_count(strtolower($contentSample), ' be ') +
                            substr_count(strtolower($contentSample), ' being ');
        
        $passiveRatio = $sentenceCount > 0 ? $passiveVoiceCount / $sentenceCount : 0;
        
        // Calculate humanlike score (0-100)
        $scoreFactors = [
            'sentence_variety' => $sentenceVarietyRatio * 30, // 0-30 points
            'passive_voice' => (1 - min(1, $passiveRatio * 3)) * 20, // 0-20 points
            'word_variety' => 0, // Will be calculated below
            'natural_flow' => 0  // Will be calculated below
        ];
        
        // Word variety check (unique words ratio)
        $words = str_word_count(strtolower($contentSample), 1);
        $uniqueWords = count(array_unique($words));
        $wordVarietyRatio = count($words) > 0 ? $uniqueWords / count($words) : 0;
        $scoreFactors['word_variety'] = min(30, $wordVarietyRatio * 50); // 0-30 points
        
        // Natural flow - transition words
        $transitionWords = ['however', 'therefore', 'consequently', 'furthermore', 'moreover', 
                          'besides', 'meanwhile', 'nevertheless', 'otherwise', 'thus', 
                          'in addition', 'in contrast', 'as a result', 'for example'];
        
        $transitionCount = 0;
        foreach ($transitionWords as $word) {
            $transitionCount += substr_count(strtolower($contentSample), $word);
        }
        
        $idealTransitionRatio = 0.1; // About 1 transition per 10 sentences
        $actualTransitionRatio = $sentenceCount > 0 ? $transitionCount / $sentenceCount : 0;
        $transitionScore = 1 - min(1, abs($actualTransitionRatio - $idealTransitionRatio) * 5);
        $scoreFactors['natural_flow'] = $transitionScore * 20; // 0-20 points
        
        // Final score
        $humanlikeScore = array_sum($scoreFactors);
        
        // Recommendations based on score
        $recommendations = [];
        
        if ($scoreFactors['sentence_variety'] < 20) {
            $recommendations[] = "Vary your sentence structures more. Mix short and long sentences.";
        }
        
        if ($scoreFactors['passive_voice'] < 15) {
            $recommendations[] = "Reduce use of passive voice. Use active voice for more human-like writing.";
        }
        
        if ($scoreFactors['word_variety'] < 20) {
            $recommendations[] = "Use a wider vocabulary. Avoid repeating the same words too frequently.";
        }
        
        if ($scoreFactors['natural_flow'] < 15) {
            $recommendations[] = "Add natural transitions between thoughts to improve flow.";
        }
        
        $passes = $humanlikeScore >= 75;
        
        if (!$passes && empty($recommendations)) {
            $recommendations[] = "Make your writing more naturally imperfect and less formulaic.";
        }
        
        return [
            'passes' => $passes,
            'score' => $humanlikeScore,
            'recommendations' => $recommendations
        ];
    }
}
