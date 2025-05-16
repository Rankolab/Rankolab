<?php
// Include authentication check
require_once __DIR__ . '/auth_check.php';

// Get current page for navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Rankolab Admin</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="includes/admin_enhancements.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
            --white-color: #fff;
            --body-bg: #f8f9fa;
            --sidebar-width: 250px;
            --header-height: 70px;
            --border-color: #e3e6f0;
            --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--body-bg);
            color: var(--dark-color);
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            color: var(--white-color);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-size: 1.2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-brand a {
            color: var(--white-color);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand i {
            font-size: 2rem;
            margin-right: 0.5rem;
        }
        
        .sidebar-divider {
            margin: 0 1rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .sidebar-heading {
            padding: 0 1rem;
            font-weight: 600;

<!-- Dark Mode Toggle -->
<button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle Dark Mode">
    <i class="fas fa-moon"></i>
</button>

<script>
    // Dark mode functionality
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const icon = darkModeToggle.querySelector('i');
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.setAttribute('data-theme', 'dark');
        icon.classList.replace('fa-moon', 'fa-sun');
    }
    
    darkModeToggle.addEventListener('click', () => {
        if (body.getAttribute('data-theme') === 'dark') {
            body.setAttribute('data-theme', 'light');
            icon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('darkMode', 'disabled');
        } else {
            body.setAttribute('data-theme', 'dark');
            icon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('darkMode', 'enabled');
        }
    });
</script>

            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.13rem;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .nav-item {
            position: relative;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: var(--white-color);
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: var(--white-color);
            font-weight: 600;
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .nav-item.dropdown .nav-link::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.3s;
        }
        
        .nav-item.dropdown.show .nav-link::after {
            transform: rotate(180deg);
        }
        
        .collapse-inner {
            padding: 0.5rem 0;
            min-width: 10rem;
            font-size: 0.85rem;
            margin: 0 0 1rem 1rem;
            border-left: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .collapse-inner a {
            display: block;
            padding: 0.5rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .collapse-inner a:hover {
            color: var(--white-color);
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .collapse-inner a.active {
            color: var(--white-color);
            font-weight: 600;
        }
        
        /* Content Area */
        .content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }
        
        .navbar {
            height: var(--header-height);
            background-color: var(--white-color);
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 90;
        }
        
        .navbar-toggle {
            color: var(--secondary-color);
            background-color: transparent;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
        }
        
        .navbar-search {
            width: 30rem;
            position: relative;
        }
        
        .navbar-search input {
            width: 100%;
            height: 2.5rem;
            padding: 0 1rem 0 3rem;
            border: 1px solid var(--border-color);
            border-radius: 0.35rem;
            font-size: 0.85rem;
        }
        
        .navbar-search i {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }
        
        .navbar-nav {
            display: flex;
            align-items: center;
        }
        
        .nav-divider {
            width: 0;
            border-right: 1px solid var(--border-color);
            height: 2.5rem;
            margin: 0 1rem;
        }
        
        .nav-user {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--secondary-color);
            padding: 0 1rem;
            position: relative;
        }
        
        .nav-user span {
            margin-right: 0.5rem;
            font-weight: 600;
        }
        
        .nav-user img {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .nav-user .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 12rem;
            background-color: var(--white-color);
            border: 1px solid var(--border-color);
            border-radius: 0.35rem;
            box-shadow: var(--shadow);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            display: none;
            z-index: 95;
        }
        
        .nav-user:hover .dropdown-menu {
            display: block;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 1.5rem;
            color: var(--dark-color);
            text-decoration: none;
            font-size: 0.85rem;
        }
        
        .dropdown-item i {
            width: 1.25rem;
            margin-right: 0.5rem;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid var(--border-color);
        }
        
        .container-fluid {
            padding: 1.5rem;
        }
        
        .page-title {
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        /* Cards */
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: var(--white-color);
            background-clip: border-box;
            border: 1px solid var(--border-color);
            border-radius: 0.35rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: #f8f9fc;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            border-left: 0.25rem solid;
            border-radius: 0.35rem;
            box-shadow: var(--shadow);
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white-color);
        }
        
        .stat-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .stat-card.success {
            border-left-color: var(--success-color);
        }
        
        .stat-card.info {
            border-left-color: var(--info-color);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card-content {
            flex: 1;
        }
        
        .stat-card-label {
            text-transform: uppercase;
            margin-bottom: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .stat-card.success .stat-card-label {
            color: var(--success-color);
        }
        
        .stat-card.info .stat-card-label {
            color: var(--info-color);
        }
        
        .stat-card.warning .stat-card-label {
            color: var(--warning-color);
        }
        
        .stat-card-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .stat-card-icon {
            font-size: 2rem;
            color: #dddfeb;
        }
        
        /* Charts */
        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
        }
        
        /* Tables */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid var(--border-color);
        }
        
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid var(--border-color);
            background-color: #f8f9fc;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .table tbody + tbody {
            border-top: 2px solid var(--border-color);
        }
        
        .table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .content {
                margin-left: 0;
            }
            
            .navbar-toggle {
                display: block;
            }
            
            .navbar-search {
                width: auto;
                flex: 1;
                margin: 0 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Super Admin specific styling */
        .badge-superadmin {
            background-color: var(--primary-color);
            color: var(--white-color);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        
        /* AI Agent Section */
        .ai-agent-card {
            border: 1px solid rgba(0, 123, 255, 0.2);
            background: linear-gradient(to right, rgba(78, 115, 223, 0.05), rgba(0, 123, 255, 0.1));
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        
        .ai-agent-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .ai-agent-icon {
            width: 3rem;
            height: 3rem;
            background-color: rgba(78, 115, 223, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .ai-agent-icon i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .ai-agent-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }
        
        .ai-agent-chat {
            background-color: var(--white-color);
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.35rem;
            height: 300px;
            overflow-y: auto;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .ai-agent-input {
            display: flex;
        }
        
        .ai-agent-input input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.35rem 0 0 0.35rem;
            font-size: 0.9rem;
        }
        
        .ai-agent-input button {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: var(--white-color);
            border: none;
            border-radius: 0 0.35rem 0.35rem 0;
            cursor: pointer;
        }
        
        /* Modal styling */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        
        .modal-backdrop.show {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-content {
            width: 90%;
            max-width: 600px;
            background-color: var(--white-color);
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-50px);
            transition: transform 0.3s;
        }
        
        .modal-backdrop.show .modal-content {
            transform: translateY(0);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-header h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .modal-header button {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            color: var(--secondary-color);
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        
        /* Custom form controls */
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #6e707e;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            color: #6e707e;
            background-color: #fff;
            border-color: #bac8f3;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            font-weight: 400;
            color: #858796;
            text-align: center;
            vertical-align: middle;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.35rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            cursor: pointer;
        }
        
        .btn-primary {
            color: #fff;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            color: #fff;
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-danger {
            color: #fff;
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-secondary {
            color: #fff;
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.35rem;
        }
        
        .badge-primary {
            color: #fff;
            background-color: var(--primary-color);
        }
        
        .badge-success {
            color: #fff;
            background-color: var(--success-color);
        }
        
        .badge-warning {
            color: #fff;
            background-color: var(--warning-color);
        }
        
        .badge-danger {
            color: #fff;
            background-color: var(--danger-color);
        }
        
        .badge-info {
            color: #fff;
            background-color: var(--info-color);
        }
        
        /* Alerts */
        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.35rem;
        }
        
        .alert-success {
            color: #0f6848;
            background-color: #d1f2e8;
            border-color: #bee5d8;
        }
        
        .alert-warning {
            color: #8a6d3b;
            background-color: #fdf6e3;
            border-color: #faebcc;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-dismissible {
            padding-right: 4rem;
        }
        
        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.75rem 1.25rem;
            color: inherit;
            background: transparent;
            border: 0;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <a href="/admin/">
                <i class="fas fa-chart-line"></i>
                <span>Rankolab</span>
            </a>
        </div>
        
        <div class="sidebar-divider"></div>
        
        <div class="sidebar-heading">Core</div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="/admin/">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="sidebar-divider"></div>
        
        <div class="sidebar-heading">User Management</div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'users' ? 'active' : ''; ?>" href="/admin/users.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Users</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'licenses' ? 'active' : ''; ?>" href="/admin/licenses.php">
                <i class="fas fa-fw fa-key"></i>
                <span>Licenses</span>
            </a>
        </div>
        
        <?php if ($isSuperAdmin): ?>
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'roles' ? 'active' : ''; ?>" href="/admin/roles.php">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Roles & Permissions</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="sidebar-divider"></div>
        
        <div class="sidebar-heading">Content</div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'content' ? 'active' : ''; ?>" href="/admin/content.php">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>Content Generator</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'domains' ? 'active' : ''; ?>" href="/admin/domains.php">
                <i class="fas fa-fw fa-globe"></i>
                <span>Domain Analysis</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'blog' ? 'active' : ''; ?>" href="/admin/blog.php">
                <i class="fas fa-fw fa-blog"></i>
                <span>Blog Management</span>
            </a>
        </div>
        
        <div class="sidebar-divider"></div>
        
        <div class="sidebar-heading">System</div>
        
        <?php if ($isSuperAdmin): ?>
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'api' ? 'active' : ''; ?>" href="/admin/api.php">
                <i class="fas fa-fw fa-code"></i>
                <span>API Manager</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'payments' ? 'active' : ''; ?>" href="/admin/payments.php">
                <i class="fas fa-fw fa-credit-card"></i>
                <span>Payment Manager</span>
            </a>
        </div>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'ai_agent' ? 'active' : ''; ?>" href="/admin/ai_agent.php">
                <i class="fas fa-fw fa-robot"></i>
                <span>AI Agent</span>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>" href="/admin/settings.php">
                <i class="fas fa-fw fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        
        <div class="sidebar-divider"></div>
        
        <div class="nav-item">
            <a class="nav-link" href="/admin/logout.php">
                <i class="fas fa-fw fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
    
    <!-- Content Wrapper -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar">
            <button class="navbar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="navbar-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search for...">
            </div>
            
            <div class="navbar-nav">
                <a href="#" class="nav-link">
                    <i class="fas fa-bell"></i>
                </a>
                
                <a href="#" class="nav-link">
                    <i class="fas fa-envelope"></i>
                </a>
                
                <div class="nav-divider"></div>
                
                <div class="nav-user">
                    <span><?php echo $_SESSION['user_name']; ?></span>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=random" alt="User">
                    
                    <div class="dropdown-menu">
                        <a href="/admin/profile.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>
                        <a href="/admin/settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i>
                            Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="/admin/logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
                
                <?php if ($isSuperAdmin): ?>
                <span class="badge-superadmin">Super Admin</span>
                <?php endif; ?>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="container-fluid">