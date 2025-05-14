<?php
/**
 * Domain API Handler
 * Manages domain analysis, keyword research, and backlink analysis
 */

// Include the DomainAnalysis model
require_once __DIR__ . '/../models/DomainAnalysis.php';
require_once __DIR__ . '/../models/User.php';

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

// Track API request (in a real app, we would save this to the database)
$licenseKey = isset($_POST['licenseKey']) ? $_POST['licenseKey'] : null;
if (!$licenseKey && isset($_SERVER['HTTP_X_API_KEY'])) {
    $licenseKey = $_SERVER['HTTP_X_API_KEY'];
}

// For simplicity, we'll use a default user ID in this demo
// In a real application, we would validate the license key and get the associated user
$userId = 1;

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
    
    handleDomainAnalysis($userId, $data);
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
 * @param int $userId The user ID
 * @param array $data The request data
 */
function handleDomainAnalysis($userId, $data) {
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
    $includeCompetitors = isset($data['includeCompetitors']) ? (bool) $data['includeCompetitors'] : false;
    
    // Analyze the domain using the DomainAnalysis model
    $result = DomainAnalysis::analyzeDomain($userId, $domain, $includeCompetitors);
    
    // Output the analysis result
    echo json_encode($result);
}

/**
 * Handle keywords retrieval request
 * 
 * @param string $domain The domain to analyze
 */
function handleKeywordsRetrieval($domain) {
    // Get keywords using the DomainAnalysis model
    $result = DomainAnalysis::getKeywords($domain);
    
    // Output the keywords
    echo json_encode($result);
}

/**
 * Handle backlinks retrieval request
 * 
 * @param string $domain The domain to analyze
 */
function handleBacklinksRetrieval($domain) {
    // Get backlinks using the DomainAnalysis model
    $result = DomainAnalysis::getBacklinks($domain);
    
    // Output the backlinks
    echo json_encode($result);
}