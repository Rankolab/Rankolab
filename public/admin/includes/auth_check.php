<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin_role'])) {
    // Not logged in, redirect to login page
    header('Location: /admin/login.php');
    exit;
}

// Define variables for permission checks
$isSuperAdmin = ($_SESSION['admin_role'] === 'superadmin');
$isAdmin = ($_SESSION['admin_role'] === 'admin' || $_SESSION['admin_role'] === 'superadmin');

// Function to check if user has access to a specific module
function hasAccess($module) {
    global $isSuperAdmin;
    
    // Super admins have access to everything
    if ($isSuperAdmin) {
        return true;
    }
    
    // Regular admins have limited access
    $regularAdminModules = [
        'dashboard', 'users', 'licenses', 'content', 'domains', 'blog'
    ];
    
    // Super admin only modules
    $superAdminModules = [
        'settings', 'payments', 'api', 'roles', 'ai_agent', 'system'
    ];
    
    if (in_array($module, $regularAdminModules)) {
        return true;
    }
    
    return false;
}