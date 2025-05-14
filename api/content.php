<?php
/**
 * Content API Handler
 * Manages content generation, plagiarism checking, and readability assessment
 */

// Include the Content model
require_once __DIR__ . '/../models/Content.php';
require_once __DIR__ . '/../models/User.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get action from URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$segments = explode('/', $path);

// Remove 'api' and 'content' from segments
array_shift($segments); // removes 'api'
array_shift($segments); // removes 'content'

// Get the action (generate, check-plagiarism, check-readability)
$action = $segments[0] ?? '';

// Track API request (in a real app, we would save this to the database)
$licenseKey = isset($_POST['licenseKey']) ? $_POST['licenseKey'] : null;
if (!$licenseKey && isset($_SERVER['HTTP_X_API_KEY'])) {
    $licenseKey = $_SERVER['HTTP_X_API_KEY'];
}

if ($method === 'POST') {
    // Get JSON data from request body
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
    
    // For simplicity, we'll use a default user ID in this demo
    // In a real application, we would validate the license key and get the associated user
    $userId = 1;
    
    // Handle different actions
    switch ($action) {
        case 'generate':
            // Process content generation
            handleContentGeneration($userId, $data);
            break;
            
        case 'check-plagiarism':
            // Process plagiarism checking
            handlePlagiarismCheck($userId, $data);
            break;
            
        case 'check-readability':
            // Process readability assessment
            handleReadabilityCheck($userId, $data);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Not Found',
                'message' => 'The requested action does not exist.'
            ]);
            break;
    }
} else {
    http_response_code(405);
    echo json_encode([
        'error' => 'Method Not Allowed',
        'message' => 'Only POST requests are allowed for this endpoint.'
    ]);
}

/**
 * Handle content generation request
 * 
 * @param int $userId The user ID
 * @param array $data The request data
 */
function handleContentGeneration($userId, $data) {
    // Validate input
    if (empty($data['topic']) || empty($data['keywords'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Topic and keywords are required fields.'
        ]);
        return;
    }
    
    $topic = $data['topic'];
    $keywords = $data['keywords'];
    $wordCount = $data['wordCount'] ?? 500;
    $toneOfVoice = $data['toneOfVoice'] ?? 'professional';
    $targetAudience = $data['targetAudience'] ?? null;
    
    // Generate content using the Content model
    $generatedContent = Content::generateContent($topic, $keywords, $wordCount, $toneOfVoice, $targetAudience);
    
    // Calculate keyword density
    $keywordDensity = Content::calculateKeywordDensity($generatedContent, $keywords);
    
    // Save the generated content to the database
    $readabilityScore = 75; // We'd normally calculate this
    Content::create(
        $userId,
        $topic,
        $keywords,
        $wordCount,
        $toneOfVoice,
        $targetAudience,
        $generatedContent,
        $readabilityScore
    );
    
    // Return the generated content
    echo json_encode([
        'success' => true,
        'content' => $generatedContent,
        'statistics' => [
            'wordCount' => str_word_count($generatedContent),
            'readabilityScore' => $readabilityScore,
            'keywordDensity' => $keywordDensity
        ]
    ]);
}

/**
 * Handle plagiarism check request
 * 
 * @param int $userId The user ID
 * @param array $data The request data
 */
function handlePlagiarismCheck($userId, $data) {
    // Validate input
    if (empty($data['content'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Content is a required field.'
        ]);
        return;
    }
    
    $content = $data['content'];
    
    // Check plagiarism using the Content model
    $result = Content::checkPlagiarism($userId, $content);
    
    echo json_encode([
        'success' => true,
        'plagiarismScore' => $result['plagiarismScore'],
        'matches' => $result['matches']
    ]);
}

/**
 * Handle readability check request
 * 
 * @param int $userId The user ID
 * @param array $data The request data
 */
function handleReadabilityCheck($userId, $data) {
    // Validate input
    if (empty($data['content'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Content is a required field.'
        ]);
        return;
    }
    
    $content = $data['content'];
    
    // Assess readability using the Content model
    $result = Content::assessReadability($userId, $content);
    
    echo json_encode([
        'success' => true,
        'scores' => $result['scores'],
        'suggestions' => $result['suggestions']
    ]);
}