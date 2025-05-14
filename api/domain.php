<?php
/**
 * Domain API Handler
 * Manages domain analysis, keyword research, and backlink analysis
 */

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get action from URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$segments = explode('/', $path);

// Remove 'api' and 'domain' from segments
array_shift($segments); // removes 'api'
array_shift($segments); // removes 'domain'

// Get the action (analyze, keywords, backlinks)
$action = $segments[0] ?? '';

// Handle different HTTP methods and actions
if ($method === 'POST' && $action === 'analyze') {
    // Handle domain analysis
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid JSON',
            'message' => 'The request body must be valid JSON.'
        ]);
        exit;
    }
    
    handleDomainAnalysis($data);
} 
elseif ($method === 'GET' && $action === 'keywords') {
    // Handle keywords retrieval
    $domain = $segments[1] ?? '';
    
    if (empty($domain)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Domain parameter is required.'
        ]);
        exit;
    }
    
    handleKeywordsRetrieval($domain);
}
elseif ($method === 'GET' && $action === 'backlinks') {
    // Handle backlinks retrieval
    $domain = $segments[1] ?? '';
    
    if (empty($domain)) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Domain parameter is required.'
        ]);
        exit;
    }
    
    handleBacklinksRetrieval($domain);
}
else {
    http_response_code(404);
    echo json_encode([
        'error' => 'Not Found',
        'message' => 'The requested endpoint does not exist or method is not allowed.'
    ]);
}

/**
 * Handle domain analysis request
 * 
 * @param array $data The request data
 */
function handleDomainAnalysis($data) {
    // Validate input
    if (empty($data['domain'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Domain is a required field.'
        ]);
        return;
    }
    
    $domain = $data['domain'];
    
    // In a real application, we would analyze the domain using various SEO tools and APIs
    // For demonstration, we'll return dummy analysis data
    echo json_encode([
        'success' => true,
        'domain' => $domain,
        'analysis' => [
            'domainAuthority' => 45,
            'pageAuthority' => 38,
            'spamScore' => 2,
            'performanceMetrics' => [
                'loadTime' => '2.3s',
                'mobileCompatibility' => 'Good',
                'pagespeedScore' => 85
            ],
            'seoIssues' => [
                'missingAltTags' => 12,
                'brokenLinks' => 3,
                'duplicateContent' => 2,
                'missingMetaDescriptions' => 5
            ],
            'competitorComparison' => [
                'competitorA.com' => [
                    'domainAuthority' => 52,
                    'commonKeywords' => 145
                ],
                'competitorB.com' => [
                    'domainAuthority' => 38,
                    'commonKeywords' => 98
                ]
            ]
        ]
    ]);
}

/**
 * Handle keywords retrieval request
 * 
 * @param string $domain The domain to analyze
 */
function handleKeywordsRetrieval($domain) {
    // In a real application, we would retrieve keywords data from an SEO API
    // For demonstration, we'll return dummy keyword data
    echo json_encode([
        'success' => true,
        'domain' => $domain,
        'keywords' => [
            [
                'keyword' => 'seo tools',
                'position' => 12,
                'searchVolume' => 5400,
                'difficulty' => 68,
                'cpc' => 4.20
            ],
            [
                'keyword' => 'content optimization',
                'position' => 8,
                'searchVolume' => 2900,
                'difficulty' => 54,
                'cpc' => 3.80
            ],
            [
                'keyword' => 'keyword research tool',
                'position' => 15,
                'searchVolume' => 8100,
                'difficulty' => 72,
                'cpc' => 5.10
            ],
            [
                'keyword' => 'backlink analyzer',
                'position' => 6,
                'searchVolume' => 3200,
                'difficulty' => 61,
                'cpc' => 4.50
            ],
            [
                'keyword' => 'domain authority checker',
                'position' => 4,
                'searchVolume' => 1800,
                'difficulty' => 49,
                'cpc' => 2.90
            ]
        ],
        'topPerformingPage' => 'https://' . $domain . '/blog/seo-strategies-2023',
        'suggestedKeywords' => [
            'seo ranking factors',
            'on-page optimization',
            'technical seo guide',
            'seo competitive analysis',
            'local seo strategy'
        ]
    ]);
}

/**
 * Handle backlinks retrieval request
 * 
 * @param string $domain The domain to analyze
 */
function handleBacklinksRetrieval($domain) {
    // In a real application, we would retrieve backlink data from an SEO API
    // For demonstration, we'll return dummy backlink data
    echo json_encode([
        'success' => true,
        'domain' => $domain,
        'backlinksOverview' => [
            'totalBacklinks' => 1832,
            'uniqueDomains' => 246,
            'dofollow' => 1254,
            'nofollow' => 578,
            'averageDomainAuthority' => 42
        ],
        'topBacklinks' => [
            [
                'source' => 'example-blog.com',
                'targetUrl' => 'https://' . $domain . '/features',
                'anchorText' => 'best SEO analysis tool',
                'domainAuthority' => 68,
                'dofollow' => true,
                'firstSeen' => '2023-02-15'
            ],
            [
                'source' => 'marketing-guide.com',
                'targetUrl' => 'https://' . $domain . '/blog/content-optimization',
                'anchorText' => 'content optimization techniques',
                'domainAuthority' => 72,
                'dofollow' => true,
                'firstSeen' => '2023-03-22'
            ],
            [
                'source' => 'digitalmarketer.org',
                'targetUrl' => 'https://' . $domain,
                'anchorText' => 'SEO platform',
                'domainAuthority' => 65,
                'dofollow' => false,
                'firstSeen' => '2022-11-08'
            ],
            [
                'source' => 'tech-reviews.net',
                'targetUrl' => 'https://' . $domain . '/pricing',
                'anchorText' => 'affordable SEO tools',
                'domainAuthority' => 58,
                'dofollow' => true,
                'firstSeen' => '2023-01-30'
            ],
            [
                'source' => 'webmaster-forums.com',
                'targetUrl' => 'https://' . $domain . '/blog/backlink-strategies',
                'anchorText' => 'click here',
                'domainAuthority' => 61,
                'dofollow' => false,
                'firstSeen' => '2023-04-05'
            ]
        ],
        'backlinksGrowth' => [
            'lastMonth' => 87,
            'last3Months' => 234,
            'last6Months' => 512
        ]
    ]);
}