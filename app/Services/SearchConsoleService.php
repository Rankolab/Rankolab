<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchConsoleService
{
    /**
     * Submit a URL to Google Search Console for indexing.
     *
     * @param string $domain
     * @param string $pageTitle
     * @return bool
     */
    public function submitUrl(string $domain, string $pageTitle): bool
    {
        Log::info('Submitting URL to Search Console', ['domain' => $domain, 'title' => $pageTitle]);
        
        // Extract the domain and generate a URL path from the title
        $parsedUrl = parse_url($domain);
        $baseUrl = isset($parsedUrl['scheme']) ? $domain : 'https://' . $domain;
        
        // Create URL slug from title
        $slug = $this->createSlug($pageTitle);
        $fullUrl = rtrim($baseUrl, '/') . '/' . $slug;
        
        // Get API key from environment or settings
        $apiKey = env('GOOGLE_API_KEY', Setting::getValue('google_api_key'));
        
        if (!$apiKey) {
            Log::warning('Google API key not configured for Search Console submission');
            return false;
        }
        
        try {
            // In a real-world scenario, this would use the actual Search Console API
            // This is a simplified implementation
            /*
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://searchconsole.googleapis.com/v1/urlNotifications:publish', [
                'url' => $fullUrl,
                'type' => 'URL_UPDATED'
            ]);
            
            if ($response->successful()) {
                Log::info('URL successfully submitted to Search Console', ['url' => $fullUrl]);
                return true;
            } else {
                Log::error('Search Console API error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }
            */
            
            // For now, log the attempt and return success
            Log::info('Simulated URL submission to Search Console', ['url' => $fullUrl]);
            return true;
        } catch (\Exception $e) {
            Log::error('Search Console submission error', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Get indexing status for a URL.
     *
     * @param string $url
     * @return array
     */
    public function getIndexingStatus(string $url): array
    {
        // Get API key from environment or settings
        $apiKey = env('GOOGLE_API_KEY', Setting::getValue('google_api_key'));
        
        if (!$apiKey) {
            Log::warning('Google API key not configured for Search Console status check');
            return [
                'indexed' => false,
                'status' => 'unknown',
                'last_crawled' => null,
                'errors' => ['API key not configured']
            ];
        }
        
        try {
            // In a real-world scenario, this would use the actual Search Console API
            // This is a simplified implementation that returns simulated data
            
            // Randomly determine if the URL is indexed (for demonstration)
            $isIndexed = (rand(0, 100) > 30);
            $status = $isIndexed ? 'indexed' : (rand(0, 100) > 50 ? 'pending' : 'not_indexed');
            $lastCrawled = $isIndexed ? now()->subHours(rand(1, 72))->toDateTimeString() : null;
            
            return [
                'indexed' => $isIndexed,
                'status' => $status,
                'last_crawled' => $lastCrawled,
                'errors' => []
            ];
        } catch (\Exception $e) {
            Log::error('Search Console status check error', ['error' => $e->getMessage()]);
            
            return [
                'indexed' => false,
                'status' => 'error',
                'last_crawled' => null,
                'errors' => [$e->getMessage()]
            ];
        }
    }
    
    /**
     * Get search performance data for a domain.
     *
     * @param string $domain
     * @param int $days
     * @return array
     */
    public function getSearchPerformance(string $domain, int $days = 30): array
    {
        // Get API key from environment or settings
        $apiKey = env('GOOGLE_API_KEY', Setting::getValue('google_api_key'));
        
        if (!$apiKey) {
            Log::warning('Google API key not configured for Search Console performance data');
            return [
                'total_clicks' => 0,
                'total_impressions' => 0,
                'average_ctr' => 0,
                'average_position' => 0,
                'daily_data' => [],
                'top_queries' => [],
                'top_pages' => [],
                'errors' => ['API key not configured']
            ];
        }
        
        try {
            // In a real-world scenario, this would use the actual Search Console API
            // This is a simplified implementation that returns simulated data
            
            $dailyData = [];
            $totalClicks = 0;
            $totalImpressions = 0;
            
            // Generate simulated daily data for the requested period
            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $impressions = rand(50, 1000);
                $clicks = rand(0, round($impressions * 0.2)); // CTR between 0-20%
                $position = rand(5, 100) / 10; // Position between 0.5 and 10.0
                
                $dailyData[] = [
                    'date' => $date,
                    'clicks' => $clicks,
                    'impressions' => $impressions,
                    'ctr' => $impressions > 0 ? round(($clicks / $impressions) * 100, 2) : 0,
                    'position' => $position
                ];
                
                $totalClicks += $clicks;
                $totalImpressions += $impressions;
            }
            
            // Generate top queries
            $queryTerms = [
                'how to', 'best', 'tutorial', 'guide', 'review', 'vs', 
                'comparison', 'examples', 'tips', 'tricks', 'benefits'
            ];
            
            $topicTerms = [
                'seo', 'marketing', 'content', 'website', 'wordpress', 
                'blogging', 'social media', 'affiliate', 'online business'
            ];
            
            $topQueries = [];
            for ($i = 0; $i < 10; $i++) {
                $query = $queryTerms[array_rand($queryTerms)] . ' ' . $topicTerms[array_rand($topicTerms)];
                $queryImpressions = rand(10, 500);
                $queryClicks = rand(0, round($queryImpressions * 0.3));
                
                $topQueries[] = [
                    'query' => $query,
                    'clicks' => $queryClicks,
                    'impressions' => $queryImpressions,
                    'ctr' => $queryImpressions > 0 ? round(($queryClicks / $queryImpressions) * 100, 2) : 0,
                    'position' => rand(1, 100) / 10
                ];
            }
            
            // Sort by impressions (descending)
            usort($topQueries, function($a, $b) {
                return $b['impressions'] - $a['impressions'];
            });
            
            // Generate top pages
            $pages = [
                '/' => 'Homepage',
                '/blog' => 'Blog',
                '/about' => 'About Us',
                '/contact' => 'Contact',
                '/services' => 'Services',
                '/products' => 'Products'
            ];
            
            $topPages = [];
            foreach ($pages as $path => $title) {
                $pageImpressions = rand(10, 300);
                $pageClicks = rand(0, round($pageImpressions * 0.25));
                
                $topPages[] = [
                    'path' => $path,
                    'title' => $title,
                    'clicks' => $pageClicks,
                    'impressions' => $pageImpressions,
                    'ctr' => $pageImpressions > 0 ? round(($pageClicks / $pageImpressions) * 100, 2) : 0,
                    'position' => rand(1, 100) / 10
                ];
            }
            
            // Sort by impressions (descending)
            usort($topPages, function($a, $b) {
                return $b['impressions'] - $a['impressions'];
            });
            
            // Calculate averages
            $averageCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
            $averagePosition = array_sum(array_column($dailyData, 'position')) / count($dailyData);
            
            return [
                'total_clicks' => $totalClicks,
                'total_impressions' => $totalImpressions,
                'average_ctr' => $averageCtr,
                'average_position' => round($averagePosition, 2),
                'daily_data' => $dailyData,
                'top_queries' => $topQueries,
                'top_pages' => $topPages,
                'errors' => []
            ];
        } catch (\Exception $e) {
            Log::error('Search Console performance data error', ['error' => $e->getMessage()]);
            
            return [
                'total_clicks' => 0,
                'total_impressions' => 0,
                'average_ctr' => 0,
                'average_position' => 0,
                'daily_data' => [],
                'top_queries' => [],
                'top_pages' => [],
                'errors' => [$e->getMessage()]
            ];
        }
    }
    
    /**
     * Create a URL-friendly slug from a title.
     *
     * @param string $title
     * @return string
     */
    private function createSlug(string $title): string
    {
        // Remove special characters
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
        
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(str_replace(' ', '-', $slug));
        
        // Remove multiple hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Trim hyphens from beginning and end
        return trim($slug, '-');
    }
}
