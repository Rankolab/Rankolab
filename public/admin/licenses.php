<?php
// Include necessary model files
require_once __DIR__ . '/../../models/License.php';
require_once __DIR__ . '/../../models/User.php';

// Get all licenses with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get licenses from the database
$licenses = License::getAll($perPage, $offset);
$totalLicenses = License::count();
$totalPages = ceil($totalLicenses / $perPage);

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Create a new license
                $userId = $_POST['user_id'];
                $plan = $_POST['plan'];
                $maxDomains = $_POST['max_domains'];
                $licenseKey = License::generateKey($plan);
                $expiresAt = date('Y-m-d H:i:s', strtotime($_POST['expires_at']));
                
                $licenseId = License::create($licenseKey, $userId, $plan, 'active', $maxDomains, $expiresAt);
                if ($licenseId) {
                    $message = "License created successfully with key: $licenseKey";
                } else {
                    $message = "Failed to create license";
                }
                break;
                
            case 'update':
                // Update an existing license
                $licenseId = $_POST['license_id'];
                $status = $_POST['status'];
                $maxDomains = $_POST['max_domains'];
                $expiresAt = date('Y-m-d H:i:s', strtotime($_POST['expires_at']));
                
                $result = License::update($licenseId, [
                    'status' => $status,
                    'max_domains' => $maxDomains,
                    'expires_at' => $expiresAt
                ]);
                
                if ($result) {
                    $message = "License updated successfully";
                } else {
                    $message = "Failed to update license";
                }
                break;
                
            case 'delete':
                // Delete a license
                $licenseId = $_POST['license_id'];
                $result = License::delete($licenseId);
                
                if ($result) {
                    $message = "License deleted successfully";
                } else {
                    $message = "Failed to delete license";
                }
                break;
        }
    }
    
    // Refresh the page to show updated data
    header('Location: licenses.php');
    exit;
}

// Get all users for the create form
$users = User::getAll(100, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Management - Rankolab Admin</title>
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
        .panel {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
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
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }
        .pagination a:hover {
            background-color: #f8f9fa;
        }
        .pagination span {
            background-color: #007bff;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 20px;
        }
        .close {
            font-size: 24px;
            cursor: pointer;
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
                <li><a href="/admin.php">Dashboard</a></li>
                <li><a href="/admin/licenses.php" class="active">Licenses</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/content.php">Content</a></li>
                <li><a href="/admin/domains.php">Domain Analysis</a></li>
                <li><a href="/admin/settings.php">Settings</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>License Management</h2>
                <button class="btn btn-primary" onclick="showModal('createLicenseModal')">Create New License</button>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>License Key</th>
                        <th>User</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Max Domains</th>
                        <th>Active Domains</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $license): ?>
                        <tr>
                            <td><?php echo $license['id']; ?></td>
                            <td><?php echo $license['license_key']; ?></td>
                            <td><?php echo $license['user_name'] . ' (' . $license['email'] . ')'; ?></td>
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
                            <td><?php echo $license['max_domains']; ?></td>
                            <td><?php echo $license['active_domains']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($license['expires_at'])); ?></td>
                            <td class="actions">
                                <button class="btn btn-primary" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($license)); ?>)">Edit</button>
                                <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this license?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="license_id" value="<?php echo $license['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($licenses)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">No licenses found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create License Modal -->
    <div id="createLicenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New License</h2>
                <span class="close" onclick="hideModal('createLicenseModal')">&times;</span>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">Select a user</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo $user['name'] . ' (' . $user['email'] . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="plan">Plan</label>
                    <select name="plan" id="plan" class="form-control" required>
                        <option value="starter">Starter</option>
                        <option value="pro">Pro</option>
                        <option value="business">Business</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="max_domains">Max Domains</label>
                    <input type="number" name="max_domains" id="max_domains" class="form-control" value="1" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="expires_at">Expires At</label>
                    <input type="date" name="expires_at" id="expires_at" class="form-control" 
                           value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Create License</button>
            </form>
        </div>
    </div>

    <!-- Edit License Modal -->
    <div id="editLicenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit License</h2>
                <span class="close" onclick="hideModal('editLicenseModal')">&times;</span>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="license_id" id="edit_license_id">
                
                <div class="form-group">
                    <label for="license_key_display">License Key</label>
                    <input type="text" id="license_key_display" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_max_domains">Max Domains</label>
                    <input type="number" name="max_domains" id="edit_max_domains" class="form-control" min="1" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_expires_at">Expires At</label>
                    <input type="date" name="expires_at" id="edit_expires_at" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Update License</button>
            </form>
        </div>
    </div>

    <script>
        function showModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function hideModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        function showEditModal(license) {
            document.getElementById('edit_license_id').value = license.id;
            document.getElementById('license_key_display').value = license.license_key;
            document.getElementById('status').value = license.status;
            document.getElementById('edit_max_domains').value = license.max_domains;
            
            // Format date for the input (YYYY-MM-DD)
            const expiryDate = new Date(license.expires_at);
            const formattedDate = expiryDate.toISOString().split('T')[0];
            document.getElementById('edit_expires_at').value = formattedDate;
            
            showModal('editLicenseModal');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>