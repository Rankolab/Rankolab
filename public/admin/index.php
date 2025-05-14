<?php
// Include necessary model files
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/License.php';
require_once __DIR__ . '/../../models/Content.php';
require_once __DIR__ . '/../../models/DomainAnalysis.php';

// Get statistics
$totalUsers = User::count();
$totalLicenses = License::count();
$totalContent = Content::count();
$totalDomainAnalyses = DomainAnalysis::count();

// Get recent licenses
$recentLicenses = License::getAll(5, 0);

// Get recent users
$recentUsers = User::getAll(5, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rankolab</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        nav {
            background-color: #495057;
            padding: 10px 20px;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav ul li {
            margin-right: 20px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
        }
        nav ul li a:hover {
            text-decoration: underline;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .stat-card h3 {
            margin-top: 0;
            color: #495057;
            font-size: 16px;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
            color: #212529;
        }
        .panel {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .panel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        table tr:hover {
            background-color: #f8f9fa;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-success {
            background-color: #d4edda;
            color: #28a745;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #ffc107;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #dc3545;
        }
        .badge-admin {
            background-color: #d4edda;
            color: #28a745;
        }
        .badge-user {
            background-color: #cce5ff;
            color: #007bff;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0069d9;
        }
        .view-all {
            display: block;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Rankolab Admin Dashboard</h1>
            <div style="display: flex; align-items: center;">
                <div style="margin-right: 20px;">
                    <a href="/" style="color: white; margin-right: 15px;">Home</a>
                    <a href="/docs.php" style="color: white;">API Docs</a>
                </div>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="/admin/" class="active">Dashboard</a></li>
                <li><a href="/admin/licenses.php">Licenses</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/content.php">Content</a></li>
                <li><a href="/admin/domains.php">Domain Analysis</a></li>
                <li><a href="/admin/settings.php">Settings</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="value"><?php echo $totalUsers; ?></div>
                <a href="/admin/users.php" class="btn">Manage Users</a>
            </div>
            <div class="stat-card">
                <h3>Total Licenses</h3>
                <div class="value"><?php echo $totalLicenses; ?></div>
                <a href="/admin/licenses.php" class="btn">Manage Licenses</a>
            </div>
            <div class="stat-card">
                <h3>Content Generations</h3>
                <div class="value"><?php echo $totalContent; ?></div>
                <a href="/admin/content.php" class="btn">View Content</a>
            </div>
            <div class="stat-card">
                <h3>Domain Analyses</h3>
                <div class="value"><?php echo $totalDomainAnalyses; ?></div>
                <a href="/admin/domains.php" class="btn">View Analyses</a>
            </div>
        </div>

        <div class="panel-grid">
            <div class="panel">
                <h2>Recent Licenses</h2>
                <table>
                    <thead>
                        <tr>
                            <th>License Key</th>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLicenses as $license): ?>
                            <tr>
                                <td><?php echo $license['license_key']; ?></td>
                                <td><?php echo $license['user_name']; ?></td>
                                <td><?php echo ucfirst($license['plan']); ?></td>
                                <td>
                                    <?php
                                        $statusClass = 'badge-success';
                                        if ($license['status'] === 'expired') {
                                            $statusClass = 'badge-danger';
                                        } elseif ($license['status'] === 'inactive') {
                                            $statusClass = 'badge-warning';
                                        }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($license['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recentLicenses)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No licenses found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="view-all">
                    <a href="/admin/licenses.php">View All Licenses</a>
                </div>
            </div>

            <div class="panel">
                <h2>Recent Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <?php
                                        $roleClass = 'badge-user';
                                        if ($user['role'] === 'admin') {
                                            $roleClass = 'badge-admin';
                                        }
                                    ?>
                                    <span class="badge <?php echo $roleClass; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recentUsers)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="view-all">
                    <a href="/admin/users.php">View All Users</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>