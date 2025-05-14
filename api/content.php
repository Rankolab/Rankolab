<?php
/**
 * Content API Handler
 * Manages content generation, plagiarism checking, and readability assessment
 */

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
    
    // Handle different actions
    switch ($action) {
        case 'generate':
            // Process content generation
            handleContentGeneration($data);
            break;
            
        case 'check-plagiarism':
            // Process plagiarism checking
            handlePlagiarismCheck($data);
            break;
            
        case 'check-readability':
            // Process readability assessment
            handleReadabilityCheck($data);
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
 * @param array $data The request data
 */
function handleContentGeneration($data) {
    // Validate input
    if (empty($data['topic']) || empty($data['keywords'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Topic and keywords are required fields.'
        ]);
        return;
    }
    
    // In a real application, we would call a service or external API to generate content
    // For demonstration, we'll return some dummy content
    $generatedContent = generateContentForTopic($data['topic'], $data['keywords'], $data['wordCount'] ?? 500);
    
    // Return the generated content
    echo json_encode([
        'success' => true,
        'content' => $generatedContent,
        'statistics' => [
            'wordCount' => str_word_count($generatedContent),
            'readabilityScore' => 75, // Dummy score
            'keywordDensity' => calculateKeywordDensity($generatedContent, $data['keywords'])
        ]
    ]);
}

/**
 * Handle plagiarism check request
 * 
 * @param array $data The request data
 */
function handlePlagiarismCheck($data) {
    // Validate input
    if (empty($data['content'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Content is a required field.'
        ]);
        return;
    }
    
    // In a real application, we would call a plagiarism checking service
    // For demonstration, we'll return dummy results
    echo json_encode([
        'success' => true,
        'plagiarismScore' => 3.2, // Percentage of potentially plagiarized content
        'matches' => [
            [
                'text' => substr($data['content'], 20, 40),
                'source' => 'https://example.com/article1',
                'matchPercentage' => 95
            ],
            [
                'text' => substr($data['content'], 100, 40),
                'source' => 'https://blog.example.com/seo-tips',
                'matchPercentage' => 85
            ]
        ]
    ]);
}

/**
 * Handle readability check request
 * 
 * @param array $data The request data
 */
function handleReadabilityCheck($data) {
    // Validate input
    if (empty($data['content'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'Content is a required field.'
        ]);
        return;
    }
    
    // In a real application, we would analyze the text for readability
    // For demonstration, we'll return dummy scores
    echo json_encode([
        'success' => true,
        'scores' => [
            'fleschKincaid' => 68.5,
            'smog' => 8.2,
            'colemanLiau' => 10.1,
            'automatedReadability' => 9.6,
            'overallGrade' => 'B'
        ],
        'suggestions' => [
            'Use shorter sentences in the third paragraph.',
            'Consider simplifying vocabulary in the introduction.',
            'Add more transition words between paragraphs.'
        ]
    ]);
}

/**
 * Generate content based on topic and keywords
 * 
 * @param string $topic The content topic
 * @param array|string $keywords Keywords to include
 * @param int $wordCount Target word count
 * @return string The generated content
 */
function generateContentForTopic($topic, $keywords, $wordCount = 500) {
    // Convert keywords to array if it's a string
    if (is_string($keywords)) {
        $keywords = explode(',', $keywords);
        $keywords = array_map('trim', $keywords);
    }
    
    // This is a dummy implementation - in a real app, this would call an AI service
    $paragraphs = [
        "The topic of {$topic} has been gaining significant attention in recent years. As businesses and individuals look to optimize their online presence, understanding the fundamental principles of {$topic} becomes crucial. With search engines constantly evolving their algorithms, staying updated with the latest trends is essential for success.",
        
        "When considering {$topic}, several key factors come into play. " . ucfirst($keywords[0] ?? "Research") . " shows that implementing strategic approaches can significantly improve results. " . ucfirst($keywords[1] ?? "Analysis") . " of market trends indicates a growing emphasis on user experience and content quality.",
        
        "Experts in the field recommend focusing on " . ($keywords[2] ?? "engagement") . " and " . ($keywords[3] ?? "optimization") . " to achieve optimal outcomes. By consistently applying best practices and monitoring performance metrics, organizations can enhance their {$topic} strategy and gain a competitive edge in the digital landscape.",
        
        "In conclusion, {$topic} remains a vital component of any comprehensive digital strategy. By leveraging the power of " . ($keywords[0] ?? "key principles") . " and implementing effective " . ($keywords[1] ?? "tactics") . ", businesses can achieve sustainable growth and improved visibility in an increasingly competitive online environment."
    ];
    
    return implode("\n\n", $paragraphs);
}

/**
 * Calculate keyword density in content
 * 
 * @param string $content The content to analyze
 * @param array|string $keywords Keywords to check
 * @return array Keyword density information
 */
function calculateKeywordDensity($content, $keywords) {
    // Convert keywords to array if it's a string
    if (is_string($keywords)) {
        $keywords = explode(',', $keywords);
        $keywords = array_map('trim', $keywords);
    }
    
    $content = strtolower($content);
    $wordCount = str_word_count($content);
    
    $result = [];
    
    foreach ($keywords as $keyword) {
        $keyword = strtolower(trim($keyword));
        $count = substr_count($content, $keyword);
        $density = ($wordCount > 0) ? ($count / $wordCount) * 100 : 0;
        
        $result[$keyword] = [
            'count' => $count,
            'density' => round($density, 2)
        ];
    }
    
    return $result;
}