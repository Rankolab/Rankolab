<?php
require_once '../db/connection.php';
require_once '../models/DomainAnalysis.php';

header('Content-Type: application/json');

function analyzeDomain($domain) {
    // Basic domain metrics analysis
    $metrics = [
        'domain' => $domain,
        'seo_score' => rand(30, 100), // Mock score for now
        'performance_score' => rand(50, 100),
        'backlinks' => rand(10, 1000),
        'da' => rand(1, 100),
        'indexed_pages' => rand(10, 1000)
    ];

    return $metrics;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['domain'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Domain is required']);
        exit;
    }

    $domain = filter_var($data['domain'], FILTER_SANITIZE_URL);
    $result = analyzeDomain($domain);

    echo json_encode($result);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}