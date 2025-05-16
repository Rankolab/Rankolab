
<?php
require_once '../db/connection.php';
require_once '../models/User.php';

header('Content-Type: application/json');

function generateToken($userId) {
    return bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch($data['action']) {
            case 'login':
                if (!isset($data['email']) || !isset($data['password'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Email and password required']);
                    exit;
                }

                // TODO: Implement actual login logic with database
                $token = generateToken(1);
                echo json_encode(['token' => $token]);
                break;

            case 'register':
                if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Name, email and password required']);
                    exit;
                }

                // TODO: Implement actual registration logic with database
                echo json_encode(['success' => true]);
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Action required']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
