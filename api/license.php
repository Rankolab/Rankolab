<?php
/**
 * License API Handler
 * Manages license validation, activation, and deactivation
 */

// Include the License model
require_once __DIR__ . '/../models/License.php';
require_once __DIR__ . '/../models/User.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get action from URI
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = ltrim($path, '/');
$segments = explode('/', $path);

// Remove 'api' and 'license' from segments
array_shift($segments); // removes 'api'
array_shift($segments); // removes 'license'

// Get the action (validate, activate, deactivate)
$action = $segments[0] ?? '';

// Track API request (in a real app, we would save this to the database)
$licenseKey = isset($_POST['licenseKey']) ? $_POST['licenseKey'] : null;
if (!$licenseKey && isset($_SERVER['HTTP_X_API_KEY'])) {
    $licenseKey = $_SERVER['HTTP_X_API_KEY'];
}

// Only allow POST requests for license operations
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
        case 'validate':
            handleLicenseValidation($data);
            break;
            
        case 'activate':
            handleLicenseActivation($data);
            break;
            
        case 'deactivate':
            handleLicenseDeactivation($data);
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
        'message' => 'Only POST requests are allowed for license operations.'
    ]);
}

/**
 * Handle license validation request
 * 
 * @param array $data The request data
 */
function handleLicenseValidation($data) {
    // Validate input
    if (empty($data['licenseKey'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'License key is required.'
        ]);
        return;
    }
    
    $licenseKey = $data['licenseKey'];
    $domain = $data['domain'] ?? '';
    
    // Validate the license using the License model
    $result = License::validate($licenseKey, $domain);
    
    // Output the validation result
    echo json_encode($result);
}

/**
 * Handle license activation request
 * 
 * @param array $data The request data
 */
function handleLicenseActivation($data) {
    // Validate input
    if (empty($data['licenseKey']) || empty($data['domain'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'License key and domain are required.'
        ]);
        return;
    }
    
    $licenseKey = $data['licenseKey'];
    $domain = $data['domain'];
    $email = $data['email'] ?? '';
    
    // Activate the license using the License model
    $result = License::activate($licenseKey, $domain, $email);
    
    // Output the activation result
    echo json_encode($result);
}

/**
 * Handle license deactivation request
 * 
 * @param array $data The request data
 */
function handleLicenseDeactivation($data) {
    // Validate input
    if (empty($data['licenseKey']) || empty($data['domain'])) {
        http_response_code(400);
        echo json_encode([
            'error' => 'Bad Request',
            'message' => 'License key and domain are required.'
        ]);
        return;
    }
    
    $licenseKey = $data['licenseKey'];
    $domain = $data['domain'];
    
    // Deactivate the license using the License model
    $result = License::deactivate($licenseKey, $domain);
    
    // Output the deactivation result
    echo json_encode($result);
}