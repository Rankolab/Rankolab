<?php

namespace App\Services;

use App\Models\DomainAnalysis;
use App\Models\Setting;
use App\Models\Website;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DomainAnalysisService
{
    /**
     * Analyze a website domain and generate comprehensive SEO report.
     *
     * @param Website $website
     * @return DomainAnalysis
     */
    public function analyze(Website $website): DomainAnalysis
    {
        Log::info('Starting domain analysis for: ' . $website->url);
        
        try {
            // Get domain data from multiple sources
            $domainData = $this->fetchDomainData($website->url);
            
            // Extract and organize data
            $seoScore = $this->calculateSeoScore($domainData);
            $websiteAuthority = $domainData['domain_authority'] ?? rand(10, 70); // Fallback to random
            $backlinksCount = $domainData['backlinks_count'] ?? rand(10, 500); // Fallback to random
            $websiteSpeed = $domainData['page_speed_score'] ?? rand(50, 95); // Fallback to random
            
            // Generate issues and recommendations
            $issues = $this->findIssues($domainData);
            $recommendations = $this->generateRecommendations($issues, $domainData);
            
            // Create domain analysis record
            $analysis = DomainAnalysis::create([
                'website_id' => $website->id,
                'seo_score' => $seoScore,
                'website_authority' => $websiteAuthority, 
                'backlinks_count' => $backlinksCount,
                'website_speed' => $websiteSpeed,
                'issues' => json_encode($issues),
                'recommendations' => json_encode($recommendations),
                'raw_data' => json_encode($domainData),
            ]);
            
            Log::info('Domain analysis completed for: ' . $website->url, [
                'seo_score' => $seoScore,
                'authority' => $websiteAuthority,
                'backlinks' => $backlinksCount
            ]);
            
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Domain analysis error: ' . $e->getMessage(), [
                'website' => $website->url,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Domain analysis failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Fetch domain data from various APIs and sources.
     *
     * @param string $domain
     * @return array
     */
    private function fetchDomainData(string $domain): array
    {
        $data = [
            'domain' => $domain,
            'analyzed_at' => now()->toDateTimeString(),
        ];
        
        // Extract just the domain name
        $parsedUrl = parse_url($domain);
        $domainName = isset($parsedUrl['host']) ? $parsedUrl['host'] : $domain;
        
        // Try to get data from Moz API if available
        $mozAccessId = env('MOZ_ACCESS_ID', Setting::getValue('moz_access_id'));
        $mozSecretKey = env('MOZ_SECRET_KEY', Setting::getValue('moz_secret_key'));
        
        if ($mozAccessId && $mozSecretKey) {
            try {
                $mozData = $this->getMozData($domainName, $mozAccessId, $mozSecretKey);
                $data = array_merge($data, $mozData);
            } catch (\Exception $e) {
                Log::warning('Failed to get Moz data: ' . $e->getMessage());
            }
        }
        
        // Try to get data from Google PageSpeed Insights if available
        $googleApiKey = env('GOOGLE_API_KEY', Setting::getValue('google_api_key'));
        
        if ($googleApiKey) {
            try {
                $pageSpeedData = $this->getPageSpeedData($domain, $googleApiKey);
                $data = array_merge($data, $pageSpeedData);
            } catch (\Exception $e) {
                Log::warning('Failed to get PageSpeed data: ' . $e->getMessage());
            }
        }
        
        // If API data is not available, perform basic domain checks
        if (!isset($data['domain_authority']) || !isset($data['page_speed_score'])) {
            $basicData = $this->performBasicDomainChecks($domain);
            $data = array_merge($data, $basicData);
        }
        
        return $data;
    }
    
    /**
     * Get domain data from Moz API.
     *
     * @param string $domain
     * @param string $accessId
     * @param string $secretKey
     * @return array
     */
    private function getMozData(string $domain, string $accessId, string $secretKey): array
    {
        // This would be implemented with actual Moz API
        // For now, return simulated data
        return [
            'domain_authority' => rand(10, 80),
            'page_authority' => rand(10, 80),
            'backlinks_count' => rand(50, 5000),
            'spam_score' => rand(1, 15),
            'ranking_keywords' => rand(10, 1000),
            'moz_rank' => rand(1, 10) / 10,
        ];
    }
    
    /**
     * Get page speed data from Google PageSpeed Insights API.
     *
     * @param string $url
     * @param string $apiKey
     * @return array
     */
    private function getPageSpeedData(string $url, string $apiKey): array
    {
        // This would be implemented with actual Google PageSpeed API
        // For now, return simulated data
        return [
            'page_speed_score' => rand(40, 100),
            'first_contentful_paint' => rand(0.5, 3.0),
            'speed_index' => rand(0.8, 5.0),
            'largest_contentful_paint' => rand(0.8, 5.0),
            'total_blocking_time' => rand(0, 500),
            'cumulative_layout_shift' => rand(0, 50) / 100,
        ];
    }
    
    /**
     * Perform basic domain checks when APIs are not available.
     *
     * @param string $domain
     * @return array
     */
    private function performBasicDomainChecks(string $domain): array
    {
        $data = [];
        
        // Check if website is reachable
        try {
            $response = Http::timeout(10)->get($domain);
            $data['is_reachable'] = $response->successful();
            $data['http_status'] = $response->status();
            
            // Check for SSL
            $data['has_ssl'] = str_starts_with($response->effectiveUri(), 'https://');
            
            // Parse HTML for basic checks
            $html = $response->body();
            
            // Meta tags check
            $hasTitle = preg_match('/<title>(.*?)<\/title>/i', $html);
            $hasDescription = preg_match('/<meta name="description"[^>]*>/i', $html);
            $hasKeywords = preg_match('/<meta name="keywords"[^>]*>/i', $html);
            
            $data['has_title'] = $hasTitle > 0;
            $data['has_meta_description'] = $hasDescription > 0;
            $data['has_meta_keywords'] = $hasKeywords > 0;
            
            // Headings check
            $hasH1 = preg_match('/<h1[^>]*>/i', $html);
            $data['has_h1'] = $hasH1 > 0;
            
            // Mobile responsive check (very basic)
            $hasViewport = preg_match('/<meta name="viewport"[^>]*>/i', $html);
            $data['has_viewport_meta'] = $hasViewport > 0;
            
            // Calculate a basic domain authority based on these factors
            $authorityScore = 20; // Base score
            
            if ($data['has_ssl']) $authorityScore += 10;
            if ($data['has_title']) $authorityScore += 5;
            if ($data['has_meta_description']) $authorityScore += 5;
            if ($data['has_meta_keywords']) $authorityScore += 3;
            if ($data['has_h1']) $authorityScore += 5;
            if ($data['has_viewport_meta']) $authorityScore += 7;
            
            $data['domain_authority'] = $authorityScore;
            $data['page_speed_score'] = rand(60, 90); // Estimated
            
        } catch (\Exception $e) {
            Log::error('Basic domain check failed: ' . $e->getMessage());
            
            // Domain unreachable, set low scores
            $data['is_reachable'] = false;
            $data['domain_authority'] = 10;
            $data['page_speed_score'] = 30;
        }
        
        return $data;
    }
    
    /**
     * Calculate SEO score based on various factors.
     *
     * @param array $domainData
     * @return int
     */
    private function calculateSeoScore(array $domainData): int
    {
        $score = 50; // Base score
        
        // Domain authority factor (up to 20 points)
        if (isset($domainData['domain_authority'])) {
            $score += min(20, $domainData['domain_authority'] / 5);
        }
        
        // Page speed factor (up to 15 points)
        if (isset($domainData['page_speed_score'])) {
            $score += min(15, $domainData['page_speed_score'] / 7);
        }
        
        // Basic on-page factors (up to 15 points)
        if (isset($domainData['has_title']) && $domainData['has_title']) $score += 3;
        if (isset($domainData['has_meta_description']) && $domainData['has_meta_description']) $score += 3;
        if (isset($domainData['has_meta_keywords']) && $domainData['has_meta_keywords']) $score += 2;
        if (isset($domainData['has_h1']) && $domainData['has_h1']) $score += 3;
        if (isset($domainData['has_viewport_meta']) && $domainData['has_viewport_meta']) $score += 2;
        if (isset($domainData['has_ssl']) && $domainData['has_ssl']) $score += 2;
        
        return min(100, round($score));
    }
    
    /**
     * Find issues based on domain data.
     *
     * @param array $domainData
     * @return array
     */
    private function findIssues(array $domainData): array
    {
        $issues = [];
        
        // Check for SSL
        if (isset($domainData['has_ssl']) && !$domainData['has_ssl']) {
            $issues[] = [
                'title' => 'Missing SSL Certificate',
                'description' => 'Your website is not using HTTPS. A secure connection is important for SEO and user trust.',
                'severity' => 'high'
            ];
        }
        
        // Check for meta tags
        if (isset($domainData['has_title']) && !$domainData['has_title']) {
            $issues[] = [
                'title' => 'Missing Title Tag',
                'description' => 'Your website does not have a title tag, which is crucial for SEO.',
                'severity' => 'high'
            ];
        }
        
        if (isset($domainData['has_meta_description']) && !$domainData['has_meta_description']) {
            $issues[] = [
                'title' => 'Missing Meta Description',
                'description' => 'Your website does not have a meta description, which helps with click-through rates from search results.',
                'severity' => 'medium'
            ];
        }
        
        // Check for H1
        if (isset($domainData['has_h1']) && !$domainData['has_h1']) {
            $issues[] = [
                'title' => 'Missing H1 Heading',
                'description' => 'Your website does not have an H1 heading, which is important for page structure and SEO.',
                'severity' => 'medium'
            ];
        }
        
        // Check for mobile responsiveness
        if (isset($domainData['has_viewport_meta']) && !$domainData['has_viewport_meta']) {
            $issues[] = [
                'title' => 'Not Mobile-Friendly',
                'description' => 'Your website does not have a viewport meta tag, which is essential for mobile responsiveness.',
                'severity' => 'high'
            ];
        }
        
        // Check page speed
        if (isset($domainData['page_speed_score']) && $domainData['page_speed_score'] < 50) {
            $issues[] = [
                'title' => 'Slow Page Speed',
                'description' => 'Your website has a low page speed score, which affects user experience and SEO.',
                'severity' => 'high'
            ];
        } elseif (isset($domainData['page_speed_score']) && $domainData['page_speed_score'] < 80) {
            $issues[] = [
                'title' => 'Average Page Speed',
                'description' => 'Your website has an average page speed score. There is room for improvement.',
                'severity' => 'medium'
            ];
        }
        
        // Check domain authority
        if (isset($domainData['domain_authority']) && $domainData['domain_authority'] < 20) {
            $issues[] = [
                'title' => 'Low Domain Authority',
                'description' => 'Your website has a low domain authority, which may affect your search rankings.',
                'severity' => 'medium'
            ];
        }
        
        return $issues;
    }
    
    /**
     * Generate recommendations based on issues and domain data.
     *
     * @param array $issues
     * @param array $domainData
     * @return array
     */
    private function generateRecommendations(array $issues, array $domainData): array
    {
        $recommendations = [];
        
        // Generate recommendations based on issues
        foreach ($issues as $issue) {
            switch ($issue['title']) {
                case 'Missing SSL Certificate':
                    $recommendations[] = [
                        'title' => 'Add SSL Certificate',
                        'description' => 'Implement HTTPS by adding an SSL certificate to your website. Most hosting providers offer free SSL certificates via Let\'s Encrypt.',
                        'priority' => 'high'
                    ];
                    break;
                
                case 'Missing Title Tag':
                    $recommendations[] = [
                        'title' => 'Add Title Tag',
                        'description' => 'Add a descriptive title tag to your website that includes your main keyword. Keep it under 60 characters.',
                        'priority' => 'high'
                    ];
                    break;
                
                case 'Missing Meta Description':
                    $recommendations[] = [
                        'title' => 'Add Meta Description',
                        'description' => 'Add a compelling meta description that accurately summarizes your page content and includes key phrases. Keep it under 160 characters.',
                        'priority' => 'medium'
                    ];
                    break;
                
                case 'Missing H1 Heading':
                    $recommendations[] = [
                        'title' => 'Add H1 Heading',
                        'description' => 'Add a clear H1 heading to your website that includes your main keyword and describes your page content.',
                        'priority' => 'medium'
                    ];
                    break;
                
                case 'Not Mobile-Friendly':
                    $recommendations[] = [
                        'title' => 'Make Website Mobile-Friendly',
                        'description' => 'Add a viewport meta tag and ensure your website is responsive across all devices. Google predominantly uses mobile-first indexing.',
                        'priority' => 'high'
                    ];
                    break;
                
                case 'Slow Page Speed':
                case 'Average Page Speed':
                    $recommendations[] = [
                        'title' => 'Improve Page Speed',
                        'description' => 'Optimize image sizes, enable browser caching, minimize CSS/JavaScript, and consider a content delivery network (CDN) to improve loading times.',
                        'priority' => $issue['title'] === 'Slow Page Speed' ? 'high' : 'medium'
                    ];
                    break;
                
                case 'Low Domain Authority':
                    $recommendations[] = [
                        'title' => 'Build Domain Authority',
                        'description' => 'Create high-quality content and build quality backlinks from reputable websites in your niche to improve domain authority.',
                        'priority' => 'medium'
                    ];
                    break;
            }
        }
        
        // Add general recommendations if needed
        if (count($recommendations) < 3) {
            // Content recommendations
            $recommendations[] = [
                'title' => 'Create Quality Content',
                'description' => 'Regularly publish high-quality, relevant content that provides value to your audience and targets your key phrases.',
                'priority' => 'medium'
            ];
            
            // Backlink recommendations
            $recommendations[] = [
                'title' => 'Improve Backlink Profile',
                'description' => 'Focus on earning high-quality backlinks from authoritative sites in your industry rather than quantity.',
                'priority' => 'medium'
            ];
            
            // Internal linking
            $recommendations[] = [
                'title' => 'Optimize Internal Linking',
                'description' => 'Create a logical internal linking structure to help search engines understand your site hierarchy and distribute link equity.',
                'priority' => 'medium'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Get keyword suggestions for a domain and seed keyword.
     *
     * @param string $domain
     * @param string $seedKeyword
     * @return array
     */
    public function getKeywordSuggestions(string $domain, string $seedKeyword): array
    {
        // In a real application, this would call a keyword research API
        // For demonstration, we'll return simulated data
        
        $baseKeywords = [
            'how to',
            'best',
            'top',
            'guide to',
            'tips for',
            'ways to',
            'vs',
            'review',
            'tutorial',
            'examples of',
            'benefits of',
        ];
        
        $industryKeywords = [
            'seo',
            'content marketing',
            'blogging',
            'wordpress',
            'website design',
            'online business',
            'digital marketing',
            'affiliate marketing',
            'social media',
            'email marketing',
            'lead generation',
        ];
        
        $suggestions = [];
        
        // Generate combinations with the seed keyword
        foreach ($baseKeywords as $base) {
            $suggestions[] = [
                'keyword' => $base . ' ' . $seedKeyword,
                'search_volume' => rand(100, 10000),
                'competition' => rand(1, 100) / 100,
                'cpc' => rand(50, 500) / 100
            ];
            
            $suggestions[] = [
                'keyword' => $seedKeyword . ' ' . $base,
                'search_volume' => rand(100, 10000),
                'competition' => rand(1, 100) / 100,
                'cpc' => rand(50, 500) / 100
            ];
        }
        
        // Generate combinations with industry keywords
        foreach ($industryKeywords as $industry) {
            $suggestions[] = [
                'keyword' => $seedKeyword . ' ' . $industry,
                'search_volume' => rand(100, 10000),
                'competition' => rand(1, 100) / 100,
                'cpc' => rand(50, 500) / 100
            ];
            
            $suggestions[] = [
                'keyword' => $industry . ' ' . $seedKeyword,
                'search_volume' => rand(100, 10000),
                'competition' => rand(1, 100) / 100,
                'cpc' => rand(50, 500) / 100
            ];
        }
        
        // Sort by search volume (descending)
        usort($suggestions, function($a, $b) {
            return $b['search_volume'] - $a['search_volume'];
        });
        
        // Take only the top 20 suggestions
        return array_slice($suggestions, 0, 20);
    }
}
