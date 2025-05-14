<?php
// Include necessary model files
require_once __DIR__ . '/../../models/DomainAnalysis.php';

// Get all domain analyses with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get domain analyses from the database
$domainAnalyses = DomainAnalysis::getAll($perPage, $offset);
$totalDomainAnalyses = DomainAnalysis::count();
$totalPages = ceil($totalDomainAnalyses / $perPage);

// Handle view specific domain analysis
$viewDomain = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $domainId = (int)$_GET['view'];
    $viewDomain = DomainAnalysis::getById($domainId);
    
    // Get related data if available
    if ($viewDomain) {
        // Get domain keywords
        $keywords = DomainAnalysis::getKeywords($domainId);
        
        // Get domain backlinks
        $backlinks = DomainAnalysis::getBacklinks($domainId);
        
        // Get domain competitors
        $competitors = DomainAnalysis::getCompetitors($domainId);
    }
}

// Handle form submissions for domain deletion
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $domainId = $_POST['domain_id'];
        $result = DomainAnalysis::delete($domainId);
        
        if ($result) {
            $message = "Domain analysis deleted successfully";
        } else {
            $message = "Failed to delete domain analysis";
        }
        
        // Redirect to the main page to refresh the view
        header("Location: domains.php?success=1");
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
    <title>Domain Analysis - Rankolab Admin</title>
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
        .back-button {
            margin-bottom: 20px;
        }
        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 30px;
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
            font-size: 18px;
            font-weight: 500;
        }
        .score-indicator {
            width: 100%;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }
        .score-bar {
            height: 100%;
            background-color: #28a745;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            margin-bottom: -1px;
        }
        .tab.active {
            border-color: #dee2e6 #dee2e6 #fff;
            background-color: #fff;
            color: #495057;
            font-weight: 500;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .keyword-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .keyword-item {
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .keyword-position {
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            margin-left: 8px;
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
                <li><a href="/admin/content.php">Content</a></li>
                <li><a href="/admin/domains.php" class="active">Domain Analysis</a></li>
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
        
        <?php if ($viewDomain): ?>
            <!-- Domain Detail View Mode -->
            <div class="panel">
                <div class="back-button">
                    <a href="domains.php" class="btn btn-primary">&laquo; Back to Domains List</a>
                </div>
                
                <h2><?php echo htmlspecialchars($viewDomain['domain_name']); ?></h2>
                <p>
                    <strong>Analyzed by:</strong> <?php echo htmlspecialchars($viewDomain['user_name']); ?> 
                    (<?php echo htmlspecialchars($viewDomain['user_email']); ?>)
                </p>
                <p>
                    <strong>Analyzed on:</strong> <?php echo date('Y-m-d H:i', strtotime($viewDomain['created_at'])); ?>
                </p>
                
                <div class="metrics">
                    <div class="metric-card">
                        <h4>Domain Authority</h4>
                        <p><?php echo $viewDomain['domain_authority']; ?>/100</p>
                        <div class="score-indicator">
                            <div class="score-bar" style="width: <?php echo $viewDomain['domain_authority']; ?>%"></div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <h4>Page Count</h4>
                        <p><?php echo number_format($viewDomain['page_count']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h4>Backlink Count</h4>
                        <p><?php echo number_format($viewDomain['backlink_count']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h4>Organic Traffic</h4>
                        <p><?php echo number_format($viewDomain['organic_traffic']); ?></p>
                    </div>
                </div>
                
                <div class="tabs">
                    <div class="tab active" onclick="showTab('overview')">Overview</div>
                    <div class="tab" onclick="showTab('keywords')">Keywords</div>
                    <div class="tab" onclick="showTab('backlinks')">Backlinks</div>
                    <div class="tab" onclick="showTab('competitors')">Competitors</div>
                </div>
                
                <div id="overview" class="tab-content active">
                    <h3>Overview</h3>
                    <div class="metrics">
                        <div class="metric-card">
                            <h4>Title Tags</h4>
                            <p><?php echo $viewDomain['seo_score_title']; ?>/100</p>
                            <div class="score-indicator">
                                <div class="score-bar" style="width: <?php echo $viewDomain['seo_score_title']; ?>%"></div>
                            </div>
                        </div>
                        <div class="metric-card">
                            <h4>Meta Descriptions</h4>
                            <p><?php echo $viewDomain['seo_score_meta']; ?>/100</p>
                            <div class="score-indicator">
                                <div class="score-bar" style="width: <?php echo $viewDomain['seo_score_meta']; ?>%"></div>
                            </div>
                        </div>
                        <div class="metric-card">
                            <h4>Content Quality</h4>
                            <p><?php echo $viewDomain['seo_score_content']; ?>/100</p>
                            <div class="score-indicator">
                                <div class="score-bar" style="width: <?php echo $viewDomain['seo_score_content']; ?>%"></div>
                            </div>
                        </div>
                        <div class="metric-card">
                            <h4>Page Speed</h4>
                            <p><?php echo $viewDomain['page_speed_score']; ?>/100</p>
                            <div class="score-indicator">
                                <div class="score-bar" style="width: <?php echo $viewDomain['page_speed_score']; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Technical Issues</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Issue Type</th>
                                <th>Severity</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Broken Links</td>
                                <td>
                                    <span class="badge badge-danger">High</span>
                                </td>
                                <td><?php echo $viewDomain['issue_count_broken_links']; ?></td>
                            </tr>
                            <tr>
                                <td>Missing Alt Text</td>
                                <td>
                                    <span class="badge badge-warning">Medium</span>
                                </td>
                                <td><?php echo $viewDomain['issue_count_missing_alt']; ?></td>
                            </tr>
                            <tr>
                                <td>Duplicate Content</td>
                                <td>
                                    <span class="badge badge-danger">High</span>
                                </td>
                                <td><?php echo $viewDomain['issue_count_duplicate_content']; ?></td>
                            </tr>
                            <tr>
                                <td>Missing Headers</td>
                                <td>
                                    <span class="badge badge-warning">Medium</span>
                                </td>
                                <td><?php echo $viewDomain['issue_count_missing_headers']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div id="keywords" class="tab-content">
                    <h3>Ranking Keywords</h3>
                    <?php if (!empty($keywords)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Keyword</th>
                                    <th>Position</th>
                                    <th>Search Volume</th>
                                    <th>Difficulty</th>
                                    <th>Traffic Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keywords as $keyword): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($keyword['keyword']); ?></td>
                                        <td><?php echo $keyword['position']; ?></td>
                                        <td><?php echo number_format($keyword['search_volume']); ?></td>
                                        <td><?php echo $keyword['difficulty']; ?>/100</td>
                                        <td><?php echo $keyword['traffic_share']; ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No keyword data available for this domain.</p>
                    <?php endif; ?>
                </div>
                
                <div id="backlinks" class="tab-content">
                    <h3>Top Backlinks</h3>
                    <?php if (!empty($backlinks)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Source Domain</th>
                                    <th>Target URL</th>
                                    <th>Domain Authority</th>
                                    <th>Link Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backlinks as $backlink): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($backlink['source_domain']); ?></td>
                                        <td><?php echo htmlspecialchars($backlink['target_url']); ?></td>
                                        <td><?php echo $backlink['domain_authority']; ?></td>
                                        <td>
                                            <?php
                                                $linkTypeClass = 'badge-success';
                                                if ($backlink['link_type'] === 'nofollow') {
                                                    $linkTypeClass = 'badge-warning';
                                                }
                                            ?>
                                            <span class="badge <?php echo $linkTypeClass; ?>">
                                                <?php echo ucfirst($backlink['link_type']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No backlink data available for this domain.</p>
                    <?php endif; ?>
                </div>
                
                <div id="competitors" class="tab-content">
                    <h3>Top Competitors</h3>
                    <?php if (!empty($competitors)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Competitor Domain</th>
                                    <th>Common Keywords</th>
                                    <th>Domain Authority</th>
                                    <th>Organic Traffic</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($competitors as $competitor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($competitor['domain_name']); ?></td>
                                        <td><?php echo $competitor['common_keywords']; ?></td>
                                        <td><?php echo $competitor['domain_authority']; ?></td>
                                        <td><?php echo number_format($competitor['organic_traffic']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No competitor data available for this domain.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Domain List Mode -->
            <div class="panel">
                <h2>Domain Analysis</h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Domain</th>
                            <th>User</th>
                            <th>DA Score</th>
                            <th>Created</th>
                            <th>Issues</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($domainAnalyses as $domain): ?>
                            <tr>
                                <td><?php echo $domain['id']; ?></td>
                                <td><?php echo htmlspecialchars($domain['domain_name']); ?></td>
                                <td><?php echo htmlspecialchars($domain['user_name']); ?></td>
                                <td><?php echo $domain['domain_authority']; ?>/100</td>
                                <td><?php echo date('Y-m-d', strtotime($domain['created_at'])); ?></td>
                                <td>
                                    <?php
                                        $totalIssues = $domain['issue_count_broken_links'] + 
                                                      $domain['issue_count_missing_alt'] + 
                                                      $domain['issue_count_duplicate_content'] + 
                                                      $domain['issue_count_missing_headers'];
                                        
                                        $issueClass = 'badge-success';
                                        if ($totalIssues > 50) {
                                            $issueClass = 'badge-danger';
                                        } elseif ($totalIssues > 20) {
                                            $issueClass = 'badge-warning';
                                        }
                                    ?>
                                    <span class="badge <?php echo $issueClass; ?>">
                                        <?php echo $totalIssues; ?> issues
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="?view=<?php echo $domain['id']; ?>" class="btn btn-primary">View</a>
                                    <form method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this domain analysis?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($domainAnalyses)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">No domain analyses found</td>
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

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Add active class to the clicked tab
            document.querySelectorAll('.tab').forEach(tab => {
                if (tab.getAttribute('onclick').includes(tabId)) {
                    tab.classList.add('active');
                }
            });
        }
    </script>
</body>
</html>