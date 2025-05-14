<?php
/**
 * DomainAnalysis Model
 * 
 * Handles domain analysis, keyword research, and backlink analysis
 */

require_once __DIR__ . '/../db/connection.php';

class DomainAnalysis {
    /**
     * Get a domain analysis by ID
     * 
     * @param int $id The analysis ID
     * @return array|false The analysis data or false if not found
     */
    public static function getById($id) {
        return fetchRow("SELECT * FROM domain_analyses WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Get domain analyses by user ID
     * 
     * @param int $userId The user ID
     * @param int $limit The maximum number of records to return
     * @param int $offset The offset for pagination
     * @return array The domain analyses
     */
    public static function getByUserId($userId, $limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT * FROM domain_analyses WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Get a domain analysis by domain name and user ID
     * 
     * @param string $domainName The domain name
     * @param int $userId The user ID
     * @return array|false The analysis data or false if not found
     */
    public static function getByDomain($domainName, $userId) {
        return fetchRow(
            "SELECT * FROM domain_analyses WHERE domain_name = :domain_name AND user_id = :user_id ORDER BY created_at DESC LIMIT 1",
            ['domain_name' => $domainName, 'user_id' => $userId]
        );
    }
    
    /**
     * Create a new domain analysis
     * 
     * @param int $userId The user ID
     * @param string $domainName The domain name
     * @param float $domainAuthority The domain authority score
     * @param float $pageAuthority The page authority score
     * @param float $spamScore The spam score
     * @param string $loadTime The load time
     * @param string $mobileCompatibility The mobile compatibility
     * @param int $pagespeedScore The pagespeed score
     * @return int The ID of the created analysis
     */
    public static function create($userId, $domainName, $domainAuthority, $pageAuthority, $spamScore, $loadTime, $mobileCompatibility, $pagespeedScore) {
        return insertRow('domain_analyses', [
            'user_id' => $userId,
            'domain_name' => $domainName,
            'domain_authority' => $domainAuthority,
            'page_authority' => $pageAuthority,
            'spam_score' => $spamScore,
            'load_time' => $loadTime,
            'mobile_compatibility' => $mobileCompatibility,
            'pagespeed_score' => $pagespeedScore
        ]);
    }
    
    /**
     * Update a domain analysis
     * 
     * @param int $id The analysis ID
     * @param array $data The data to update
     * @return int The number of rows affected
     */
    public static function update($id, array $data) {
        return updateRow('domain_analyses', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete a domain analysis
     * 
     * @param int $id The analysis ID
     * @return int The number of rows affected
     */
    public static function delete($id) {
        // First delete all associated SEO issues
        deleteRow('seo_issues', 'analysis_id = :analysis_id', ['analysis_id' => $id]);
        
        // Then delete the analysis itself
        return deleteRow('domain_analyses', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Add an SEO issue to a domain analysis
     * 
     * @param int $analysisId The analysis ID
     * @param string $issueType The issue type
     * @param int $issueCount The issue count
     * @param string $details The issue details (optional)
     * @return int The ID of the created SEO issue
     */
    public static function addSeoIssue($analysisId, $issueType, $issueCount, $details = null) {
        return insertRow('seo_issues', [
            'analysis_id' => $analysisId,
            'issue_type' => $issueType,
            'issue_count' => $issueCount,
            'details' => $details
        ]);
    }
    
    /**
     * Get SEO issues for a domain analysis
     * 
     * @param int $analysisId The analysis ID
     * @return array The SEO issues
     */
    public static function getSeoIssues($analysisId) {
        return fetchAll(
            "SELECT * FROM seo_issues WHERE analysis_id = :analysis_id",
            ['analysis_id' => $analysisId]
        );
    }
    
    /**
     * Analyze a domain
     * 
     * In a real application, this would call external SEO APIs
     * For demonstration, we're returning dummy analysis data
     * 
     * @param int $userId The user ID
     * @param string $domain The domain to analyze
     * @param bool $includeCompetitors Whether to include competitor analysis
     * @return array The analysis results
     */
    public static function analyzeDomain($userId, $domain, $includeCompetitors = false) {
        // In a real application, this would call external SEO APIs
        // For demonstration, we'll create a realistic-looking analysis
        
        // Generate random but realistic-looking scores
        $domainAuthority = mt_rand(30, 60);
        $pageAuthority = mt_rand(25, 55);
        $spamScore = mt_rand(1, 5);
        $loadTime = (mt_rand(15, 35) / 10) . 's';
        $mobileCompatibility = ['Poor', 'Fair', 'Good', 'Excellent'][mt_rand(1, 3)];
        $pagespeedScore = mt_rand(60, 95);
        
        // Create the analysis record
        $analysisId = self::create(
            $userId, 
            $domain, 
            $domainAuthority, 
            $pageAuthority, 
            $spamScore, 
            $loadTime, 
            $mobileCompatibility, 
            $pagespeedScore
        );
        
        // Add some SEO issues
        $seoIssues = [
            'missingAltTags' => mt_rand(5, 20),
            'brokenLinks' => mt_rand(1, 5),
            'duplicateContent' => mt_rand(0, 3),
            'missingMetaDescriptions' => mt_rand(3, 10)
        ];
        
        foreach ($seoIssues as $issueType => $count) {
            self::addSeoIssue($analysisId, $issueType, $count);
        }
        
        // Build the response
        $analysis = [
            'domainAuthority' => $domainAuthority,
            'pageAuthority' => $pageAuthority,
            'spamScore' => $spamScore,
            'performanceMetrics' => [
                'loadTime' => $loadTime,
                'mobileCompatibility' => $mobileCompatibility,
                'pagespeedScore' => $pagespeedScore
            ],
            'seoIssues' => $seoIssues
        ];
        
        // Add competitor analysis if requested
        if ($includeCompetitors) {
            $competitors = self::generateCompetitorAnalysis($domain, $domainAuthority);
            $analysis['competitorComparison'] = $competitors;
        }
        
        return [
            'success' => true,
            'domain' => $domain,
            'analysis' => $analysis
        ];
    }
    
    /**
     * Generate competitor analysis
     * 
     * @param string $domain The main domain
     * @param int $domainAuthority The main domain's authority
     * @return array The competitor analysis
     */
    private static function generateCompetitorAnalysis($domain, $domainAuthority) {
        // Extract TLD from domain
        $parts = explode('.', $domain);
        $tld = end($parts);
        
        // Create some realistic-looking competitor domains
        $competitors = [
            'competitor' . mt_rand(1, 999) . '.' . $tld => [
                'domainAuthority' => min(100, $domainAuthority + mt_rand(-10, 15)),
                'commonKeywords' => mt_rand(50, 200)
            ],
            'competitor' . mt_rand(1000, 9999) . '.' . $tld => [
                'domainAuthority' => min(100, $domainAuthority + mt_rand(-15, 10)),
                'commonKeywords' => mt_rand(30, 150)
            ]
        ];
        
        return $competitors;
    }
    
    /**
     * Get keywords for a domain
     * 
     * In a real application, this would call external keyword research APIs
     * For demonstration, we're returning dummy keyword data
     * 
     * @param string $domain The domain to get keywords for
     * @return array The keyword data
     */
    public static function getKeywords($domain) {
        // In a real application, this would call external APIs
        // For demonstration, we'll return dummy keywords
        
        // Check if we have stored keywords for this domain
        $existingKeywords = fetchAll(
            "SELECT * FROM domain_keywords WHERE domain_name = :domain_name ORDER BY position ASC LIMIT 5",
            ['domain_name' => $domain]
        );
        
        // If we have stored keywords, return them
        if (count($existingKeywords) > 0) {
            $keywords = $existingKeywords;
        } else {
            // Otherwise, generate some dummy keywords and store them
            $keywords = self::generateDummyKeywords($domain);
            
            // Store the keywords
            foreach ($keywords as $keyword) {
                insertRow('domain_keywords', [
                    'domain_name' => $domain,
                    'keyword' => $keyword['keyword'],
                    'position' => $keyword['position'],
                    'search_volume' => $keyword['searchVolume'],
                    'difficulty' => $keyword['difficulty'],
                    'cpc' => $keyword['cpc']
                ]);
            }
        }
        
        // Generate a top performing page URL
        $topPerformingPage = 'https://' . $domain . '/blog/' . self::generateSlug(mt_rand(2, 4));
        
        // Generate suggested keywords
        $suggestedKeywords = self::generateSuggestedKeywords();
        
        return [
            'success' => true,
            'domain' => $domain,
            'keywords' => $keywords,
            'topPerformingPage' => $topPerformingPage,
            'suggestedKeywords' => $suggestedKeywords
        ];
    }
    
    /**
     * Generate dummy keywords for a domain
     * 
     * @param string $domain The domain
     * @return array The dummy keywords
     */
    private static function generateDummyKeywords($domain) {
        // Common SEO-related keywords
        $keywordTemplates = [
            'seo tools',
            'content optimization',
            'keyword research tool',
            'backlink analyzer',
            'domain authority checker',
            'seo ranking factors',
            'on-page optimization',
            'technical seo guide',
            'seo competitive analysis',
            'local seo strategy'
        ];
        
        // Generate 5 random keywords
        $keywords = [];
        $usedIndexes = [];
        
        for ($i = 0; $i < 5; $i++) {
            // Ensure we don't use the same keyword twice
            do {
                $index = mt_rand(0, count($keywordTemplates) - 1);
            } while (in_array($index, $usedIndexes));
            
            $usedIndexes[] = $index;
            $keyword = $keywordTemplates[$index];
            
            $keywords[] = [
                'keyword' => $keyword,
                'position' => mt_rand(1, 20),
                'searchVolume' => mt_rand(1000, 10000),
                'difficulty' => mt_rand(30, 80),
                'cpc' => round(mt_rand(200, 600) / 100, 2)
            ];
        }
        
        return $keywords;
    }
    
    /**
     * Generate suggested keywords
     * 
     * @return array The suggested keywords
     */
    private static function generateSuggestedKeywords() {
        $suggestedKeywords = [
            'seo ranking factors',
            'on-page optimization',
            'technical seo guide',
            'seo competitive analysis',
            'local seo strategy'
        ];
        
        // Shuffle and return a subset
        shuffle($suggestedKeywords);
        return array_slice($suggestedKeywords, 0, mt_rand(3, 5));
    }
    
    /**
     * Get backlinks for a domain
     * 
     * In a real application, this would call external backlink analysis APIs
     * For demonstration, we're returning dummy backlink data
     * 
     * @param string $domain The domain to get backlinks for
     * @return array The backlink data
     */
    public static function getBacklinks($domain) {
        // In a real application, this would call external APIs
        // For demonstration, we'll return dummy backlinks
        
        // Check if we have stored backlinks for this domain
        $existingBacklinks = fetchAll(
            "SELECT * FROM domain_backlinks WHERE domain_name = :domain_name ORDER BY domain_authority DESC LIMIT 5",
            ['domain_name' => $domain]
        );
        
        // If we have stored backlinks, return them
        if (count($existingBacklinks) > 0) {
            $backlinks = [];
            foreach ($existingBacklinks as $backlink) {
                $backlinks[] = [
                    'source' => $backlink['source_domain'],
                    'targetUrl' => $backlink['target_url'],
                    'anchorText' => $backlink['anchor_text'],
                    'domainAuthority' => $backlink['domain_authority'],
                    'dofollow' => (bool)$backlink['is_dofollow'],
                    'firstSeen' => $backlink['first_seen']
                ];
            }
        } else {
            // Otherwise, generate some dummy backlinks and store them
            $backlinks = self::generateDummyBacklinks($domain);
            
            // Store the backlinks
            foreach ($backlinks as $backlink) {
                insertRow('domain_backlinks', [
                    'domain_name' => $domain,
                    'source_domain' => $backlink['source'],
                    'target_url' => $backlink['targetUrl'],
                    'anchor_text' => $backlink['anchorText'],
                    'domain_authority' => $backlink['domainAuthority'],
                    'is_dofollow' => $backlink['dofollow'],
                    'first_seen' => $backlink['firstSeen']
                ]);
            }
        }
        
        // Generate backlink overview statistics
        $backlinksOverview = [
            'totalBacklinks' => mt_rand(1000, 3000),
            'uniqueDomains' => mt_rand(200, 500),
            'dofollow' => mt_rand(800, 1500),
            'nofollow' => mt_rand(300, 800),
            'averageDomainAuthority' => mt_rand(35, 50)
        ];
        
        // Generate backlinks growth statistics
        $backlinksGrowth = [
            'lastMonth' => mt_rand(50, 150),
            'last3Months' => mt_rand(150, 350),
            'last6Months' => mt_rand(350, 700)
        ];
        
        return [
            'success' => true,
            'domain' => $domain,
            'backlinksOverview' => $backlinksOverview,
            'topBacklinks' => $backlinks,
            'backlinksGrowth' => $backlinksGrowth
        ];
    }
    
    /**
     * Generate dummy backlinks for a domain
     * 
     * @param string $domain The domain
     * @return array The dummy backlinks
     */
    private static function generateDummyBacklinks($domain) {
        // Common backlink sources
        $sourceDomains = [
            'example-blog.com',
            'marketing-guide.com',
            'digitalmarketer.org',
            'tech-reviews.net',
            'webmaster-forums.com',
            'seo-journal.com',
            'content-creators.net',
            'digital-strategy.org'
        ];
        
        // Common anchor texts
        $anchorTexts = [
            'best SEO analysis tool',
            'content optimization techniques',
            'SEO platform',
            'affordable SEO tools',
            'click here',
            'read more',
            'useful SEO resource',
            'top SEO tool'
        ];
        
        // Target URLs
        $targetUrls = [
            'https://' . $domain,
            'https://' . $domain . '/features',
            'https://' . $domain . '/pricing',
            'https://' . $domain . '/blog/content-optimization',
            'https://' . $domain . '/blog/backlink-strategies'
        ];
        
        // Generate 5 random backlinks
        $backlinks = [];
        $usedIndexes = [];
        
        for ($i = 0; $i < 5; $i++) {
            // Ensure we don't use the same source domain twice
            do {
                $sourceIndex = mt_rand(0, count($sourceDomains) - 1);
            } while (in_array($sourceIndex, $usedIndexes));
            
            $usedIndexes[] = $sourceIndex;
            
            // Generate a random date within the last year
            $firstSeen = date('Y-m-d', strtotime('-' . mt_rand(1, 365) . ' days'));
            
            $backlinks[] = [
                'source' => $sourceDomains[$sourceIndex],
                'targetUrl' => $targetUrls[mt_rand(0, count($targetUrls) - 1)],
                'anchorText' => $anchorTexts[mt_rand(0, count($anchorTexts) - 1)],
                'domainAuthority' => mt_rand(40, 75),
                'dofollow' => (mt_rand(0, 1) === 1),
                'firstSeen' => $firstSeen
            ];
        }
        
        return $backlinks;
    }
    
    /**
     * Generate a URL slug from random words
     * 
     * @param int $wordCount The number of words
     * @return string The generated slug
     */
    private static function generateSlug($wordCount = 3) {
        $words = [
            'seo', 'content', 'marketing', 'strategy', 'tips', 'guide',
            'optimization', 'keyword', 'backlink', 'ranking', 'analysis',
            'research', 'tools', 'techniques', 'best', 'practices'
        ];
        
        $slug = [];
        for ($i = 0; $i < $wordCount; $i++) {
            $slug[] = $words[array_rand($words)];
        }
        
        return implode('-', $slug);
    }
    
    /**
     * Get all domain analyses
     * 
     * @param int $limit The maximum number of records to return
     * @param int $offset The offset for pagination
     * @return array The domain analyses
     */
    public static function getAll($limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT da.*, u.name as user_name, u.email as user_email 
             FROM domain_analyses da 
             JOIN users u ON da.user_id = u.id 
             ORDER BY da.created_at DESC 
             LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Count the total number of domain analyses
     * 
     * @return int The number of domain analyses
     */
    public static function count() {
        $result = fetchRow("SELECT COUNT(*) as count FROM domain_analyses");
        return $result['count'];
    }
    
    /**
     * Get keywords for a domain analysis
     * 
     * @param int $domainId The domain analysis ID
     * @return array The keywords data
     */
    public static function getKeywords($domainId) {
        return fetchAll(
            "SELECT * FROM domain_keywords WHERE domain_id = :domain_id ORDER BY position ASC",
            ['domain_id' => $domainId]
        );
    }
    
    /**
     * Get backlinks for a domain analysis
     * 
     * @param int $domainId The domain analysis ID
     * @return array The backlinks data
     */
    public static function getBacklinks($domainId) {
        return fetchAll(
            "SELECT * FROM domain_backlinks WHERE domain_id = :domain_id ORDER BY domain_authority DESC",
            ['domain_id' => $domainId]
        );
    }
    
    /**
     * Get competitors for a domain analysis
     * 
     * @param int $domainId The domain analysis ID
     * @return array The competitors data
     */
    public static function getCompetitors($domainId) {
        return fetchAll(
            "SELECT * FROM domain_competitors WHERE domain_id = :domain_id ORDER BY common_keywords DESC",
            ['domain_id' => $domainId]
        );
    }
}