<?php
/**
 * Initialize database with default data
 * 
 * This script inserts default settings and demo data into the database
 */

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/License.php';
require_once __DIR__ . '/../models/Settings.php';

// Initialize settings
echo "Initializing settings...\n";
Settings::initDefaults();
echo "Settings initialized successfully.\n";

// Create demo admin user
echo "Creating admin user...\n";
$adminId = User::create('Administrator', 'admin@rankolab.com', 'admin123', 'admin');
echo "Admin user created with ID: $adminId\n";

// Create demo regular user
echo "Creating demo user...\n";
$userId = User::create('Demo User', 'demo@rankolab.com', 'demo123', 'user');
echo "Demo user created with ID: $userId\n";

// Create demo licenses
echo "Creating demo licenses...\n";
$licenseKeys = [
    'RANKO-PRO-1234-5678-9ABC',
    'RANKO-BUSINESS-2345-6789-ABCD',
    'RANKO-ENTERPRISE-3456-7890-BCDE',
    'RANKO-PRO-EXPIRED-1234-5678'
];

$licenseData = [
    [
        'plan' => 'pro',
        'status' => 'active',
        'maxDomains' => 10,
        'expires' => '+1 year'
    ],
    [
        'plan' => 'business',
        'status' => 'active',
        'maxDomains' => 25,
        'expires' => '+2 years'
    ],
    [
        'plan' => 'enterprise',
        'status' => 'active',
        'maxDomains' => 100,
        'expires' => '+3 years'
    ],
    [
        'plan' => 'pro',
        'status' => 'expired',
        'maxDomains' => 10,
        'expires' => '-1 year'
    ]
];

foreach ($licenseKeys as $index => $key) {
    $data = $licenseData[$index];
    $expiresAt = date('Y-m-d H:i:s', strtotime($data['expires']));
    
    $licenseId = License::create(
        $key,
        $userId,
        $data['plan'],
        $data['status'],
        $data['maxDomains'],
        $expiresAt
    );
    
    echo "License created with ID: $licenseId, Key: $key\n";
}

echo "Database initialization complete!\n";