<?php
/**
 * License API Handler
 * Manages license validation, activation, and deactivation
 */

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
    
    // In a real application, we would check the license against a database
    // For demonstration, we'll validate a few hardcoded license keys
    $validLicenses = [
        'RANKO-PRO-1234-5678-9ABC' => [
            'plan' => 'pro',
            'status' => 'active',
            'expiryDate' => '2023-12-31',
            'maxDomains' => 10,
            'activeDomains' => 3
        ],
        'RANKO-BUSINESS-2345-6789-ABCD' => [
            'plan' => 'business',
            'status' => 'active',
            'expiryDate' => '2024-06-30',
            'maxDomains' => 25,
            'activeDomains' => 12
        ],
        'RANKO-ENTERPRISE-3456-7890-BCDE' => [
            'plan' => 'enterprise',
            'status' => 'active',
            'expiryDate' => '2025-01-15',
            'maxDomains' => 100,
            'activeDomains' => 45
        ],
        'RANKO-PRO-EXPIRED-1234-5678' => [
            'plan' => 'pro',
            'status' => 'expired',
            'expiryDate' => '2023-01-31',
            'maxDomains' => 10,
            'activeDomains' => 0
        ]
    ];
    
    if (array_key_exists($licenseKey, $validLicenses)) {
        $license = $validLicenses[$licenseKey];
        
        // Check if the license has expired
        if ($license['status'] === 'expired') {
            echo json_encode([
                'success' => false,
                'message' => 'This license key has expired.',
                'licenseDetails' => [
                    'licenseKey' => $licenseKey,
                    'status' => 'expired',
                    'expiryDate' => $license['expiryDate'],
                    'renewalUrl' => 'https://rankolab.com/renew?key=' . urlencode($licenseKey)
                ]
            ]);
            return;
        }
        
        // Check if the domain is valid for this license
        if (!empty($domain)) {
            $domainStatus = checkDomainForLicense($licenseKey, $domain);
            
            echo json_encode([
                'success' => true,
                'licenseDetails' => [
                    'licenseKey' => $licenseKey,
                    'plan' => $license['plan'],
                    'status' => $license['status'],
                    'expiryDate' => $license['expiryDate'],
                    'maxDomains' => $license['maxDomains'],
                    'activeDomains' => $license['activeDomains'],
                    'domainStatus' => $domainStatus
                ]
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'licenseDetails' => [
                    'licenseKey' => $licenseKey,
                    'plan' => $license['plan'],
                    'status' => $license['status'],
                    'expiryDate' => $license['expiryDate'],
                    'maxDomains' => $license['maxDomains'],
                    'activeDomains' => $license['activeDomains']
                ]
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid license key.'
        ]);
    }
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
    
    // In a real application, we would check and update the license in a database
    // For demonstration, we'll validate a few hardcoded license keys
    $validLicenses = [
        'RANKO-PRO-1234-5678-9ABC' => [
            'plan' => 'pro',
            'status' => 'active',
            'expiryDate' => '2023-12-31',
            'maxDomains' => 10,
            'activeDomains' => 3
        ],
        'RANKO-BUSINESS-2345-6789-ABCD' => [
            'plan' => 'business',
            'status' => 'active',
            'expiryDate' => '2024-06-30',
            'maxDomains' => 25,
            'activeDomains' => 12
        ],
        'RANKO-ENTERPRISE-3456-7890-BCDE' => [
            'plan' => 'enterprise',
            'status' => 'active',
            'expiryDate' => '2025-01-15',
            'maxDomains' => 100,
            'activeDomains' => 45
        ],
        'RANKO-PRO-EXPIRED-1234-5678' => [
            'plan' => 'pro',
            'status' => 'expired',
            'expiryDate' => '2023-01-31',
            'maxDomains' => 10,
            'activeDomains' => 0
        ]
    ];
    
    if (!array_key_exists($licenseKey, $validLicenses)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid license key.'
        ]);
        return;
    }
    
    $license = $validLicenses[$licenseKey];
    
    // Check if the license has expired
    if ($license['status'] === 'expired') {
        echo json_encode([
            'success' => false,
            'message' => 'This license key has expired and cannot be activated.',
            'renewalUrl' => 'https://rankolab.com/renew?key=' . urlencode($licenseKey)
        ]);
        return;
    }
    
    // Check if max domains limit reached
    if ($license['activeDomains'] >= $license['maxDomains']) {
        echo json_encode([
            'success' => false,
            'message' => 'Maximum number of domains reached for this license. Please deactivate a domain or upgrade your plan.',
            'upgradeUrl' => 'https://rankolab.com/upgrade?key=' . urlencode($licenseKey)
        ]);
        return;
    }
    
    // Simulate activation
    echo json_encode([
        'success' => true,
        'message' => 'License successfully activated for ' . $domain,
        'activationDetails' => [
            'licenseKey' => $licenseKey,
            'domain' => $domain,
            'activationDate' => date('Y-m-d'),
            'activationId' => generateActivationId($licenseKey, $domain),
            'expiryDate' => $license['expiryDate']
        ]
    ]);
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
    
    // In a real application, we would update the license in the database
    // For demonstration, we'll pretend to deactivate the domain
    echo json_encode([
        'success' => true,
        'message' => 'License successfully deactivated for ' . $domain,
        'deactivationDetails' => [
            'licenseKey' => $licenseKey,
            'domain' => $domain,
            'deactivationDate' => date('Y-m-d')
        ]
    ]);
}

/**
 * Check if a domain is activated for a license
 * 
 * @param string $licenseKey The license key
 * @param string $domain The domain to check
 * @return string Domain status (active, inactive, pending)
 */
function checkDomainForLicense($licenseKey, $domain) {
    // In a real application, we would check this in a database
    // For demonstration, we'll pretend some domains are active
    $activeDomains = [
        'RANKO-PRO-1234-5678-9ABC' => [
            'example.com' => 'active',
            'mysite.org' => 'active',
            'blog.example.net' => 'active'
        ],
        'RANKO-BUSINESS-2345-6789-ABCD' => [
            'business-site.com' => 'active',
            'company.org' => 'active'
        ],
        'RANKO-ENTERPRISE-3456-7890-BCDE' => [
            'enterprise.com' => 'active',
            'corporate.org' => 'active',
            'subsidiary.net' => 'active'
        ]
    ];
    
    if (isset($activeDomains[$licenseKey]) && isset($activeDomains[$licenseKey][$domain])) {
        return $activeDomains[$licenseKey][$domain];
    }
    
    return 'inactive';
}

/**
 * Generate a unique activation ID
 * 
 * @param string $licenseKey The license key
 * @param string $domain The domain
 * @return string Unique activation ID
 */
function generateActivationId($licenseKey, $domain) {
    $dateCode = date('ymd');
    $hash = substr(md5($licenseKey . $domain . $dateCode), 0, 10);
    return 'ACT-' . $dateCode . '-' . $hash;
}