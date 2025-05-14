<?php
// Set page title
$pageTitle = 'Dashboard';

// Include header
require_once __DIR__ . '/includes/header.php';

// Get statistics for dashboard
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/License.php';
require_once __DIR__ . '/../../models/Content.php';
require_once __DIR__ . '/../../models/DomainAnalysis.php';
require_once __DIR__ . '/../../models/Settings.php';

// User statistics
$totalUsers = User::count();
$totalAdmins = User::countByRole('admin');
$totalSuperAdmins = User::countByRole('superadmin');
$recentUsers = User::getAll(5, 0);

// License statistics
$totalLicenses = License::count();
$activeLicenses = License::countByStatus('active');
$expiredLicenses = License::countByStatus('expired');
$recentLicenses = License::getAll(5, 0);

// Content statistics
$totalContent = Content::count();
$recentContent = Content::getAll(5, 0);

// Domain statistics
$totalDomainAnalyses = DomainAnalysis::count();
$recentDomainAnalyses = DomainAnalysis::getAll(5, 0);

// Quick stats for super admin
$systemSettings = null;
$systemStats = null;
if ($isSuperAdmin) {
    // Get API usage stats
    $apiUsageStats = [
        'total_requests' => 15762,
        'success_rate' => 99.2,
        'average_response_time' => 245
    ];
    
    // Get payment stats
    $paymentStats = [
        'total_revenue' => 12850.75,
        'month_revenue' => 3240.50,
        'pending_payments' => 2,
        'refunds' => 1
    ];
    
    // Get system health metrics
    $systemStats = [
        'cpu_usage' => 42,
        'memory_usage' => 68,
        'disk_usage' => 35,
        'api_status' => 'Operational'
    ];
    
    // Get system settings
    $systemSettings = Settings::getAll();
}

// Get monthly stats for charts
$monthlyUsers = [20, 25, 30, 35, 25, 38, 42, 47, 55, 60, 65, 70];
$monthlyContent = [150, 200, 250, 300, 280, 350, 400, 420, 500, 550, 600, 650];
$monthlyDomains = [80, 100, 120, 90, 95, 110, 105, 115, 140, 160, 180, 200];
$monthlyRevenue = [2500, 3000, 2800, 3500, 4000, 4200, 4500, 5000, 5500, 5800, 6200, 6500];
?>

<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <a href="/admin/licenses.php" class="btn btn-primary">
            <i class="fas fa-plus-circle mr-1"></i> Create License
        </a>
        <a href="/admin/users.php" class="btn btn-primary">
            <i class="fas fa-user-plus mr-1"></i> Add User
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-card-content">
            <div class="stat-card-label">Total Users</div>
            <div class="stat-card-value"><?php echo $totalUsers; ?></div>
        </div>
        <div class="stat-card-icon">
            <i class="fas fa-users"></i>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-card-content">
            <div class="stat-card-label">Active Licenses</div>
            <div class="stat-card-value"><?php echo $activeLicenses; ?></div>
        </div>
        <div class="stat-card-icon">
            <i class="fas fa-key"></i>
        </div>
    </div>
    
    <div class="stat-card info">
        <div class="stat-card-content">
            <div class="stat-card-label">Content Generations</div>
            <div class="stat-card-value"><?php echo $totalContent; ?></div>
        </div>
        <div class="stat-card-icon">
            <i class="fas fa-file-alt"></i>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-card-content">
            <div class="stat-card-label">Domain Analyses</div>
            <div class="stat-card-value"><?php echo $totalDomainAnalyses; ?></div>
        </div>
        <div class="stat-card-icon">
            <i class="fas fa-globe"></i>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6>Usage Overview</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h6>License Distribution</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="licenseChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Super Admin Section -->
<?php if ($isSuperAdmin): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6>Super Admin Dashboard</h6>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-card-content">
                            <div class="stat-card-label">API Requests</div>
                            <div class="stat-card-value"><?php echo number_format($apiUsageStats['total_requests']); ?></div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-code"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-card-content">
                            <div class="stat-card-label">Total Revenue</div>
                            <div class="stat-card-value">$<?php echo number_format($paymentStats['total_revenue'], 2); ?></div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-card-content">
                            <div class="stat-card-label">System Load</div>
                            <div class="stat-card-value"><?php echo $systemStats['cpu_usage']; ?>%</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card info">
                        <div class="stat-card-content">
                            <div class="stat-card-label">API Status</div>
                            <div class="stat-card-value"><?php echo $systemStats['api_status']; ?></div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-xl-8 col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h6>Monthly Revenue</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h6>AI Agent</h6>
                            </div>
                            <div class="card-body">
                                <div class="ai-agent-card">
                                    <div class="ai-agent-header">
                                        <div class="ai-agent-icon">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <h5 class="ai-agent-title">Rankolab AI Assistant</h5>
                                    </div>
                                    <p>Your personal AI assistant for system maintenance and problem-solving.</p>
                                    <a href="/admin/ai_agent.php" class="btn btn-primary">Access AI Agent</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Data Tables -->
<div class="row mt-4">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6>Recent Users</h6>
                <a href="/admin/users.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
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
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'superadmin'): ?>
                                    <span class="badge badge-primary">Super Admin</span>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                    <span class="badge badge-info">Admin</span>
                                    <?php else: ?>
                                    <span class="badge badge-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($recentUsers)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No users found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6>Recent Licenses</h6>
                <a href="/admin/licenses.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>User</th>
                                <th>Plan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLicenses as $license): ?>
                            <tr>
                                <td><?php echo substr($license['license_key'], 0, 12) . '...'; ?></td>
                                <td><?php echo htmlspecialchars($license['user_name']); ?></td>
                                <td><?php echo ucfirst($license['plan']); ?></td>
                                <td>
                                    <?php if ($license['status'] === 'active'): ?>
                                    <span class="badge badge-success">Active</span>
                                    <?php elseif ($license['status'] === 'expired'): ?>
                                    <span class="badge badge-danger">Expired</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($recentLicenses)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No licenses found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>

<script>
    // Usage Chart
    const usageCtx = document.getElementById('usageChart').getContext('2d');
    const usageChart = new Chart(usageCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'New Users',
                    data: <?php echo json_encode($monthlyUsers); ?>,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Content Generated',
                    data: <?php echo json_encode($monthlyContent); ?>,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Domain Analyses',
                    data: <?php echo json_encode($monthlyDomains); ?>,
                    borderColor: '#36b9cc',
                    backgroundColor: 'rgba(54, 185, 204, 0.05)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
    
    // License Chart
    const licenseCtx = document.getElementById('licenseChart').getContext('2d');
    const licenseChart = new Chart(licenseCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Expired', 'Inactive'],
            datasets: [
                {
                    data: [<?php echo $activeLicenses; ?>, <?php echo $expiredLicenses; ?>, <?php echo $totalLicenses - $activeLicenses - $expiredLicenses; ?>],
                    backgroundColor: ['#1cc88a', '#e74a3b', '#f6c23e'],
                    hoverBackgroundColor: ['#17a673', '#e02d1b', '#f4b619'],
                    hoverBorderColor: 'rgba(234, 236, 244, 1)'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
    
    <?php if ($isSuperAdmin): ?>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: <?php echo json_encode($monthlyRevenue); ?>,
                    backgroundColor: '#4e73df',
                    borderRadius: 4
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    <?php endif; ?>
</script>