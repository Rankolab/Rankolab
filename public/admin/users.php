<?php
// Include necessary model files
require_once __DIR__ . '/../../models/User.php';

// Get all users with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get users from the database
$users = User::getAll($perPage, $offset);
$totalUsers = User::count();
$totalPages = ceil($totalUsers / $perPage);

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Create a new user
                $name = $_POST['name'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $role = $_POST['role'];
                
                // Check if email is already in use
                $existingUser = User::getByEmail($email);
                if ($existingUser) {
                    $message = "Email is already in use";
                } else {
                    $userId = User::create($name, $email, $password, $role);
                    if ($userId) {
                        $message = "User created successfully";
                    } else {
                        $message = "Failed to create user";
                    }
                }
                break;
                
            case 'update':
                // Update an existing user
                $userId = $_POST['user_id'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $role = $_POST['role'];
                
                // Check if a new password was provided
                $data = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ];
                
                if (!empty($_POST['password'])) {
                    $data['password'] = $_POST['password'];
                }
                
                $result = User::update($userId, $data);
                
                if ($result) {
                    $message = "User updated successfully";
                } else {
                    $message = "Failed to update user";
                }
                break;
                
            case 'delete':
                // Delete a user
                $userId = $_POST['user_id'];
                $result = User::delete($userId);
                
                if ($result) {
                    $message = "User deleted successfully";
                } else {
                    $message = "Failed to delete user";
                }
                break;
        }
    }
    
    // Refresh the page to show updated data
    header('Location: users.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Rankolab Admin</title>
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
        .badge-admin {
            background-color: #d4edda;
            color: #28a745;
        }
        .badge-user {
            background-color: #cce5ff;
            color: #007bff;
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
                <li><a href="/admin/licenses.php">Licenses</a></li>
                <li><a href="/admin/users.php" class="active">Users</a></li>
                <li><a href="/admin/content.php">Content</a></li>
                <li><a href="/admin/domains.php">Domain Analysis</a></li>
                <li><a href="/admin/settings.php">Settings</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="panel">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>User Management</h2>
                <button class="btn btn-primary" onclick="showModal('createUserModal')">Create New User</button>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
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
                            <td class="actions">
                                <button class="btn btn-primary" onclick="showEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                                <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No users found</td>
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

    <!-- Create User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New User</h2>
                <span class="close" onclick="hideModal('createUserModal')">&times;</span>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Create User</button>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit User</h2>
                <span class="close" onclick="hideModal('editUserModal')">&times;</span>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="edit_user_id">
                
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="edit_password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select name="role" id="edit_role" class="form-control" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Update User</button>
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
        
        function showEditModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            
            showModal('editUserModal');
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