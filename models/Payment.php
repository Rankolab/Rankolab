<?php
/**
 * Payment Model
 * 
 * Handles payment processing and management
 */

require_once __DIR__ . '/../db/connection.php';

class Payment {
    /**
     * Get a payment by ID
     * 
     * @param int $id The payment ID
     * @return array|false The payment data or false if not found
     */
    public static function getById($id) {
        return fetchRow("SELECT * FROM payments WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Get payments for a specific user
     * 
     * @param int $userId The user ID
     * @param int $limit The maximum number of payments to return
     * @param int $offset The offset for pagination
     * @return array The payments
     */
    public static function getByUser($userId, $limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT * FROM payments WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Get all payments with pagination
     * 
     * @param int $limit The maximum number of payments to return
     * @param int $offset The offset for pagination
     * @return array The payments
     */
    public static function getAll($limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT p.*, u.name as user_name, u.email as user_email, l.license_key 
             FROM payments p 
             LEFT JOIN users u ON p.user_id = u.id 
             LEFT JOIN licenses l ON p.license_id = l.id 
             ORDER BY p.created_at DESC 
             LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Create a new payment
     * 
     * @param int $userId The user ID
     * @param int $licenseId The license ID (optional)
     * @param string $type The payment type (e.g., 'subscription', 'one-time')
     * @param float $amount The payment amount
     * @param string $currency The payment currency (default: 'USD')
     * @param string $status The payment status
     * @param string $provider The payment provider (e.g., 'stripe', 'paypal')
     * @param string $transactionId The transaction ID from the provider
     * @param array $metadata Additional metadata for the payment
     * @return int The ID of the created payment
     */
    public static function create($userId, $licenseId, $type, $amount, $currency = 'USD', $status, $provider, $transactionId, $metadata = []) {
        return insertRow('payments', [
            'user_id' => $userId,
            'license_id' => $licenseId,
            'payment_type' => $type,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'provider' => $provider,
            'transaction_id' => $transactionId,
            'metadata' => json_encode($metadata)
        ]);
    }
    
    /**
     * Update a payment
     * 
     * @param int $id The payment ID
     * @param array $data The data to update
     * @return int The number of rows affected
     */
    public static function update($id, array $data) {
        // If metadata is being updated, encode it as JSON
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $data['metadata'] = json_encode($data['metadata']);
        }
        
        return updateRow('payments', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update the status of a payment
     * 
     * @param int $id The payment ID
     * @param string $status The new status
     * @return int The number of rows affected
     */
    public static function updateStatus($id, $status) {
        return self::update($id, ['status' => $status]);
    }
    
    /**
     * Delete a payment
     * 
     * @param int $id The payment ID
     * @return int The number of rows affected
     */
    public static function delete($id) {
        return deleteRow('payments', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count the total number of payments
     * 
     * @return int The number of payments
     */
    public static function count() {
        $result = fetchRow("SELECT COUNT(*) as count FROM payments");
        return $result['count'];
    }
    
    /**
     * Count the number of payments by status
     * 
     * @param string $status The status to count
     * @return int The number of payments with the specified status
     */
    public static function countByStatus($status) {
        $result = fetchRow("SELECT COUNT(*) as count FROM payments WHERE status = :status", ['status' => $status]);
        return $result['count'];
    }
    
    /**
     * Get total revenue
     * 
     * @param string $currency The currency to filter by (optional)
     * @return float The total revenue
     */
    public static function getTotalRevenue($currency = null) {
        $sql = "SELECT SUM(amount) as total FROM payments WHERE status = 'completed'";
        $params = [];
        
        if ($currency) {
            $sql .= " AND currency = :currency";
            $params['currency'] = $currency;
        }
        
        $result = fetchRow($sql, $params);
        return $result['total'] ?: 0;
    }
    
    /**
     * Get monthly revenue for the current year
     * 
     * @param string $currency The currency to filter by (optional)
     * @return array An array of monthly revenue data
     */
    public static function getMonthlyRevenue($currency = null) {
        $year = date('Y');
        $months = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthStart = sprintf('%d-%02d-01', $year, $i);
            $monthEnd = date('Y-m-t', strtotime($monthStart));
            
            $sql = "SELECT SUM(amount) as total FROM payments 
                   WHERE status = 'completed' 
                   AND created_at >= :start_date 
                   AND created_at <= :end_date";
            
            $params = [
                'start_date' => $monthStart,
                'end_date' => $monthEnd . ' 23:59:59'
            ];
            
            if ($currency) {
                $sql .= " AND currency = :currency";
                $params['currency'] = $currency;
            }
            
            $result = fetchRow($sql, $params);
            $months[] = $result['total'] ?: 0;
        }
        
        return $months;
    }
    
    /**
     * Process a refund for a payment
     * 
     * @param int $paymentId The payment ID
     * @param float $amount The refund amount (if partial)
     * @param string $reason The reason for the refund
     * @return bool Whether the refund was successful
     */
    public static function processRefund($paymentId, $amount = null, $reason = '') {
        // Get the payment details
        $payment = self::getById($paymentId);
        
        if (!$payment || $payment['status'] !== 'completed') {
            return false;
        }
        
        // For a full refund, use the original amount
        if ($amount === null) {
            $amount = $payment['amount'];
        }
        
        // Record the refund in the database
        $refundId = insertRow('payment_refunds', [
            'payment_id' => $paymentId,
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'completed'
        ]);
        
        if ($refundId) {
            // Update the payment status
            self::updateStatus($paymentId, 'refunded');
            return true;
        }
        
        return false;
    }
    
    /**
     * Get list of payment providers
     * 
     * @return array List of available payment providers
     */
    public static function getProviders() {
        return [
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'bank_transfer' => 'Bank Transfer',
            'manual' => 'Manual Payment'
        ];
    }
    
    /**
     * Get list of payment statuses
     * 
     * @return array List of available payment statuses
     */
    public static function getStatuses() {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'cancelled' => 'Cancelled'
        ];
    }
}