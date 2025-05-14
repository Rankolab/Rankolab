<?php
// Include necessary model files
require_once __DIR__ . '/../../models/Content.php';

// Get all content generations with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get content generations from the database
$contentGenerations = Content::getAll($perPage, $offset);
$totalContentGenerations = Content::count();
$totalPages = ceil($totalContentGenerations / $perPage);

// Handle view specific content
$viewContent = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $contentId = (int)$_GET['view'];
    $viewContent = Content::getById($contentId);
}

// Handle form submissions for content deletion
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $contentId = $_POST['content_id'];
        $result = Content::delete($contentId);
        
        if ($result) {
            $message = "Content generation deleted successfully";
        } else {
            $message = "Failed to delete content generation";
        }
        
        // Redirect to the main page to refresh the view
        header("Location: content.php?success=1");
        exit;
    }
}

// Check if there's a success message from redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Operation completed successfully";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management - Rankolab Admin</title>
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
        .badge-info {
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
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
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
        .content-view {
            white-space: pre-wrap;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            margin-top: 20px;
        }
        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .metric-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .metric-card h4 {
            margin-top: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .metric-card p {
            margin-bottom: 0;
            font-size: 16px;
            font-weight: 500;
        }
        .back-button {
            margin-bottom: 20px;
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
                <li><a href="/admin/">Dashboard</a></li>
                <li><a href="/admin/licenses.php">Licenses</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/content.php" class="active">Content</a></li>
                <li><a href="/admin/domains.php">Domain Analysis</a></li>
                <li><a href="/admin/settings.php">Settings</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($viewContent): ?>
            <!-- Content View Mode -->
            <div class="panel">
                <div class="back-button">
                    <a href="content.php" class="btn btn-primary">&laquo; Back to Content List</a>
                </div>
                
                <h2><?php echo htmlspecialchars($viewContent['title']); ?></h2>
                <p>
                    <strong>Generated by:</strong> <?php echo htmlspecialchars($viewContent['user_name']); ?> 
                    (<?php echo htmlspecialchars($viewContent['user_email']); ?>)
                </p>
                <p>
                    <strong>Generated on:</strong> <?php echo date('Y-m-d H:i', strtotime($viewContent['created_at'])); ?>
                </p>
                <p>
                    <strong>Content Type:</strong> <?php echo ucfirst(htmlspecialchars($viewContent['content_type'])); ?>
                </p>
                
                <?php if (!empty($viewContent['keywords'])): ?>
                    <p>
                        <strong>Keywords:</strong> <?php echo htmlspecialchars($viewContent['keywords']); ?>
                    </p>
                <?php endif; ?>
                
                <div class="metrics">
                    <div class="metric-card">
                        <h4>Word Count</h4>
                        <p><?php echo str_word_count($viewContent['content']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h4>Character Count</h4>
                        <p><?php echo strlen($viewContent['content']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h4>Readability Score</h4>
                        <p><?php echo $viewContent['readability_score'] ? $viewContent['readability_score'] : 'N/A'; ?></p>
                    </div>
                    <div class="metric-card">
                        <h4>Uniqueness Score</h4>
                        <p><?php echo $viewContent['uniqueness_score'] ? $viewContent['uniqueness_score'] . '%' : 'N/A'; ?></p>
                    </div>
                </div>
                
                <h3>Content</h3>
                <div class="content-view">
                    <?php echo nl2br(htmlspecialchars($viewContent['content'])); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Content List Mode -->
            <div class="panel">
                <h2>Content Management</h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Created</th>
                            <th>Word Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contentGenerations as $content): ?>
                            <tr>
                                <td><?php echo $content['id']; ?></td>
                                <td><?php echo htmlspecialchars($content['title']); ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo ucfirst($content['content_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($content['user_name']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($content['created_at'])); ?></td>
                                <td><?php echo str_word_count($content['content']); ?></td>
                                <td class="actions">
                                    <a href="?view=<?php echo $content['id']; ?>" class="btn btn-primary">View</a>
                                    <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this content generation?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="content_id" value="<?php echo $content['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($contentGenerations)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No content generations found</td>
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
        <?php endif; ?>
    </div>
</body>
</html>