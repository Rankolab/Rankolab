<?php
// Set page title
$pageTitle = 'Payment Management';

// Include header
require_once __DIR__ . '/includes/header.php';

// Check if user is super admin for certain operations
if (!$isSuperAdmin) {
    // Some operations might be restricted to super admins
}

// Include the Payment model
require_once __DIR__ . '/../../models/Payment.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/License.php';

// Handle form submissions
$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Create a new payment (manual entry)
                $userId = (int)$_POST['user_id'];
                $licenseId = !empty($_POST['license_id']) ? (int)$_POST['license_id'] : null;
                $type = $_POST['payment_type'];
                $amount = (float)$_POST['amount'];
                $currency = $_POST['currency'];
                $status = $_POST['status'];
                $provider = $_POST['provider'];
                $transactionId = $_POST['transaction_id'] ?: 'manual-' . time();
                
                $metadata = [
                    'notes' => $_POST['notes'],
                    'created_by_admin' => $_SESSION['user_id'],
                    'manual_entry' => true
                ];
                
                $paymentId = Payment::create(
                    $userId, $licenseId, $type, $amount, $currency, 
                    $status, $provider, $transactionId, $metadata
                );
                
                if ($paymentId) {
                    $message = "Payment created successfully with ID: $paymentId";
                } else {
                    $message = "Failed to create payment";
                    $messageType = 'danger';
                }
                break;
                
            case 'update_status':
                // Update payment status
                $paymentId = (int)$_POST['payment_id'];
                $status = $_POST['status'];
                
                $result = Payment::updateStatus($paymentId, $status);
                
                if ($result) {
                    $message = "Payment status updated successfully";
                } else {
                    $message = "Failed to update payment status";
                    $messageType = 'danger';
                }
                break;
                
            case 'refund':
                // Process refund
                $paymentId = (int)$_POST['payment_id'];
                $amount = !empty($_POST['refund_amount']) ? (float)$_POST['refund_amount'] : null;
                $reason = $_POST['refund_reason'] ?? '';
                
                $result = Payment::processRefund($paymentId, $amount, $reason);
                
                if ($result) {
                    $message = "Refund processed successfully";
                } else {
                    $message = "Failed to process refund";
                    $messageType = 'danger';
                }
                break;
                
            case 'delete':
                // Delete payment record (should be restricted or just marked as deleted in real implementation)
                if ($isSuperAdmin) {
                    $paymentId = (int)$_POST['payment_id'];
                    $result = Payment::delete($paymentId);
                    
                    if ($result) {
                        $message = "Payment deleted successfully";
                    } else {
                        $message = "Failed to delete payment";
                        $messageType = 'danger';
                    }
                } else {
                    $message = "Only super admins can delete payment records";
                    $messageType = 'danger';
                }
                break;
        }
    }
}

// Get payment data for listing
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filter by status if set
$status = isset($_GET['status']) ? $_GET['status'] : null;
$payments = $status ? Payment::getByStatus($status, $perPage, $offset) : Payment::getAll($perPage, $offset);

$totalPayments = Payment::count();
$totalPages = ceil($totalPayments / $perPage);

// Get counts by status for stats
$pendingCount = Payment::countByStatus('pending');
$completedCount = Payment::countByStatus('completed');
$refundedCount = Payment::countByStatus('refunded');
$failedCount = Payment::countByStatus('failed');

// Calculate revenue metrics
$totalRevenue = Payment::getTotalRevenue();
$monthlyRevenue = Payment::getMonthlyRevenue();
$currentMonthRevenue = $monthlyRevenue[date('n') - 1];
$previousMonthRevenue = $monthlyRevenue[date('n') - 2 >= 0 ? date('n') - 2 : 11];

// For the create payment form
$users = User::getAll(100, 0);
$licenses = License::getAll(100, 0);
$providers = Payment::getProviders();
$statuses = Payment::getStatuses();

// Get payment details if viewing a specific payment
$viewPayment = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $paymentId = (int)$_GET['view'];
    $viewPayment = Payment::getById($paymentId);
    
    if ($viewPayment) {
        // Get related user and license
        $viewPayment['user'] = User::getById($viewPayment['user_id']);
        
        if ($viewPayment['license_id']) {
            $viewPayment['license'] = License::getById($viewPayment['license_id']);
        }
        
        // Parse metadata
        if (!empty($viewPayment['metadata'])) {
            $viewPayment['metadata'] = json_decode($viewPayment['metadata'], true);
        } else {
            $viewPayment['metadata'] = [];
        }
    }
}
?>

<!-- Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Payment Management</h1>
    
    <?php if ($isSuperAdmin): ?>
    <div>
        <button class="btn btn-primary" onclick="openModal('createPaymentModal')">
            <i class="fas fa-plus-circle mr-1"></i> Record Manual Payment
        </button>
    </div>
    <?php endif; ?>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<?php if ($viewPayment): ?>
<!-- Payment Detail View -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0">Payment Details - #<?php echo $viewPayment['id']; ?></h6>
            <a href="payments.php" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Payments
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h5 class="text-primary">Payment Information</h5>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Transaction ID:</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($viewPayment['transaction_id']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Amount:</div>
                        <div class="col-sm-8">
                            <?php echo number_format($viewPayment['amount'], 2); ?> 
                            <?php echo htmlspecialchars($viewPayment['currency']); ?>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Status:</div>
                        <div class="col-sm-8">
                            <?php
                            $statusClass = 'badge-secondary';
                            switch ($viewPayment['status']) {
                                case 'completed':
                                    $statusClass = 'badge-success';
                                    break;
                                case 'pending':
                                    $statusClass = 'badge-warning';
                                    break;
                                case 'refunded':
                                    $statusClass = 'badge-info';
                                    break;
                                case 'failed':
                                    $statusClass = 'badge-danger';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst(htmlspecialchars($viewPayment['status'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Type:</div>
                        <div class="col-sm-8"><?php echo ucfirst(htmlspecialchars($viewPayment['payment_type'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Provider:</div>
                        <div class="col-sm-8"><?php echo ucfirst(htmlspecialchars($viewPayment['provider'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Date:</div>
                        <div class="col-sm-8"><?php echo date('M d, Y h:i A', strtotime($viewPayment['created_at'])); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($viewPayment['metadata'])): ?>
                <div class="mb-4">
                    <h5 class="text-primary">Additional Information</h5>
                    <?php foreach ($viewPayment['metadata'] as $key => $value): ?>
                        <?php if ($key !== 'created_by_admin' && $key !== 'manual_entry'): ?>
                        <div class="row mb-2">
                            <div class="col-sm-4 font-weight-bold"><?php echo ucwords(str_replace('_', ' ', $key)); ?>:</div>
                            <div class="col-sm-8">
                                <?php 
                                if (is_array($value)) {
                                    echo json_encode($value);
                                } elseif (is_bool($value)) {
                                    echo $value ? 'Yes' : 'No';
                                } else {
                                    echo htmlspecialchars($value);
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (isset($viewPayment['metadata']['manual_entry']) && $viewPayment['metadata']['manual_entry']): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle mr-1"></i> This payment was manually recorded by an administrator.
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <div class="mb-4">
                    <h5 class="text-primary">Customer Information</h5>
                    <?php if (isset($viewPayment['user'])): ?>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Name:</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($viewPayment['user']['name']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Email:</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($viewPayment['user']['email']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">User ID:</div>
                        <div class="col-sm-8"><?php echo $viewPayment['user']['id']; ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Joined:</div>
                        <div class="col-sm-8"><?php echo date('M d, Y', strtotime($viewPayment['user']['created_at'])); ?></div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Customer information not found.
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($viewPayment['license'])): ?>
                <div class="mb-4">
                    <h5 class="text-primary">License Information</h5>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">License Key:</div>
                        <div class="col-sm-8"><?php echo htmlspecialchars($viewPayment['license']['license_key']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Plan:</div>
                        <div class="col-sm-8"><?php echo ucfirst(htmlspecialchars($viewPayment['license']['plan'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Status:</div>
                        <div class="col-sm-8"><?php echo ucfirst(htmlspecialchars($viewPayment['license']['status'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 font-weight-bold">Expires:</div>
                        <div class="col-sm-8"><?php echo date('M d, Y', strtotime($viewPayment['license']['expires_at'])); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Payment Actions -->
                <?php if ($isSuperAdmin): ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="m-0">Payment Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <form method="post">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="payment_id" value="<?php echo $viewPayment['id']; ?>">
                                    
                                    <div class="form-group">
                                        <label for="status">Update Status</label>
                                        <select name="status" id="status" class="form-control form-control-sm">
                                            <?php foreach ($statuses as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo $viewPayment['status'] === $value ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-sm btn-primary">Update Status</button>
                                </form>
                            </div>
                            
                            <div class="col-6">
                                <?php if ($viewPayment['status'] === 'completed'): ?>
                                <button class="btn btn-sm btn-warning" onclick="openModal('refundModal')">Process Refund</button>
                                <?php endif; ?>
                                
                                <button class="btn btn-sm btn-danger mt-2" onclick="confirmDelete(<?php echo $viewPayment['id']; ?>)">
                                    Delete Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($isSuperAdmin && $viewPayment['status'] === 'completed'): ?>
<!-- Refund Modal -->
<div id="refundModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Process Refund</h5>
            <button type="button" onclick="closeModal('refundModal')">&times;</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="refund">
            <input type="hidden" name="payment_id" value="<?php echo $viewPayment['id']; ?>">
            
            <div class="modal-body">
                <p>You are about to process a refund for payment #<?php echo $viewPayment['id']; ?></p>
                
                <div class="form-group">
                    <label for="refund_amount">Refund Amount (<?php echo $viewPayment['currency']; ?>)</label>
                    <input type="number" name="refund_amount" id="refund_amount" class="form-control" 
                           value="<?php echo $viewPayment['amount']; ?>" min="0.01" 
                           max="<?php echo $viewPayment['amount']; ?>" step="0.01">
                    <small class="form-text text-muted">Leave blank for full refund</small>
                </div>
                
                <div class="form-group">
                    <label for="refund_reason">Reason for Refund</label>
                    <textarea name="refund_reason" id="refund_reason" class="form-control" rows="3"></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('refundModal')">Cancel</button>
                <button type="submit" class="btn btn-warning">Process Refund</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Delete Confirmation Form (hidden) -->
<form id="deleteForm" method="post" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="payment_id" id="delete_payment_id">
</form>

<?php else: ?>
<!-- Payments Summary & Listing -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Revenue
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?php echo number_format($totalRevenue, 2); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            This Month
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            $<?php echo number_format($currentMonthRevenue, 2); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Completed Payments
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $completedCount; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Payments
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $pendingCount; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue</h6>
    </div>
    <div class="card-body">
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Payment Transactions</h6>
            
            <div>
                <a href="?status=completed" class="btn btn-sm btn-outline-success mr-1">Completed</a>
                <a href="?status=pending" class="btn btn-sm btn-outline-warning mr-1">Pending</a>
                <a href="?status=refunded" class="btn btn-sm btn-outline-info mr-1">Refunded</a>
                <a href="?status=failed" class="btn btn-sm btn-outline-danger mr-1">Failed</a>
                <a href="?" class="btn btn-sm btn-outline-secondary">All</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Provider</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" class="text-center">No payments found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo $payment['id']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($payment['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['user_name']) . ' (' . htmlspecialchars($payment['user_email']) . ')'; ?></td>
                        <td>
                            <?php echo number_format($payment['amount'], 2); ?> 
                            <?php echo $payment['currency']; ?>
                        </td>
                        <td>
                            <?php
                            $statusClass = 'badge-secondary';
                            switch ($payment['status']) {
                                case 'completed':
                                    $statusClass = 'badge-success';
                                    break;
                                case 'pending':
                                    $statusClass = 'badge-warning';
                                    break;
                                case 'refunded':
                                    $statusClass = 'badge-info';
                                    break;
                                case 'failed':
                                    $statusClass = 'badge-danger';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </td>
                        <td><?php echo ucfirst($payment['payment_type']); ?></td>
                        <td><?php echo ucfirst($payment['provider']); ?></td>
                        <td>
                            <a href="?view=<?php echo $payment['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if ($isSuperAdmin): ?>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $payment['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . $status : ''; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . $status : ''; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($isSuperAdmin): ?>
<!-- Create Payment Modal -->
<div id="createPaymentModal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Record Manual Payment</h5>
            <button type="button" onclick="closeModal('createPaymentModal')">&times;</button>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="create">
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">Select User</option>
                        <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>">
                            <?php echo htmlspecialchars($user['name']) . ' (' . htmlspecialchars($user['email']) . ')'; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="license_id">License (Optional)</label>
                    <select name="license_id" id="license_id" class="form-control">
                        <option value="">No License</option>
                        <?php foreach ($licenses as $license): ?>
                        <option value="<?php echo $license['id']; ?>">
                            <?php echo htmlspecialchars($license['license_key']) . ' - ' . ucfirst($license['plan']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="amount">Amount</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="currency">Currency</label>
                        <select name="currency" id="currency" class="form-control">
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="payment_type">Payment Type</label>
                        <select name="payment_type" id="payment_type" class="form-control">
                            <option value="subscription">Subscription</option>
                            <option value="one-time">One-time Purchase</option>
                            <option value="renewal">Renewal</option>
                            <option value="upgrade">Upgrade</option>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="provider">Payment Provider</label>
                        <select name="provider" id="provider" class="form-control">
                            <?php foreach ($providers as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <?php foreach ($statuses as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="transaction_id">Transaction ID (Optional)</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control">
                        <small class="form-text text-muted">Leave blank to generate automatically</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('createPaymentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Record Payment</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<script>
    // Modal management
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('show');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }
    
    // Delete confirmation
    function confirmDelete(paymentId) {
        if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
            document.getElementById('delete_payment_id').value = paymentId;
            document.getElementById('deleteForm').submit();
        }
    }
    
    // Revenue Chart
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!$viewPayment): ?>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: <?php echo json_encode($monthlyRevenue); ?>,
                    backgroundColor: '#4e73df',
                    borderRadius: 4
                }]
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
    });
</script>

<style>
    /* Custom Styles for Payment Management */
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    
    .card-body h5.text-primary {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e3e6f0;
    }
</style>

<?php
// Include footer
require_once __DIR__ . '/includes/footer.php';
?>