<?php
/**
 * Rankolab Backend
 * Simple API endpoint handler without Laravel
 */

// Set content type to JSON
header('Content-Type: application/json');

// Parse URI path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$segments = explode('/', $path);

// Basic routing logic
if (empty($segments[0]) || $segments[0] === 'index.php') {
    // Home route
    echo json_encode([
        'name' => 'Rankolab API',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
    exit;
}

// Handle API routes
if ($segments[0] === 'api') {
    // Remove 'api' from segments
    array_shift($segments);
    
    if (empty($segments)) {
        // API home route
        echo json_encode([
            'endpoints' => [
                '/api/content/generate',
                '/api/content/check-plagiarism',
                '/api/content/check-readability',
                '/api/domain/analyze',
                '/api/domain/keywords/{domain}',
                '/api/domain/backlinks/{domain}',
                '/api/license/validate',
                '/api/license/activate',
                '/api/license/deactivate'
            ]
        ]);
        exit;
    }
    
    // Include the appropriate API handler based on the path
    $handler_file = __DIR__ . '/../api/' . $segments[0] . '.php';
    
    if (file_exists($handler_file)) {
        require $handler_file;
        exit;
    }
}

// If no route matches, return 404
http_response_code(404);
echo json_encode([
    'error' => 'Not Found',
    'message' => 'The requested endpoint does not exist.'
]);