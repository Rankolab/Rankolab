<?php
/**
 * License Model
 * 
 * Represents a license in the Rankolab system
 */

require_once __DIR__ . '/../db/connection.php';

class License {
    /**
     * Get a license by ID
     * 
     * @param int $id The license ID
     * @return array|false The license data or false if not found
     */
    public static function getById($id) {
        return fetchRow("SELECT * FROM licenses WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Get a license by key
     * 
     * @param string $licenseKey The license key
     * @return array|false The license data or false if not found
     */
    public static function getByKey($licenseKey) {
        return fetchRow("SELECT * FROM licenses WHERE license_key = :license_key", 
                      ['license_key' => $licenseKey]);
    }
    
    /**
     * Get licenses by user ID
     * 
     * @param int $userId The user ID
     * @return array The licenses
     */
    public static function getByUserId($userId) {
        return fetchAll("SELECT * FROM licenses WHERE user_id = :user_id", 
                      ['user_id' => $userId]);
    }
    
    /**
     * Create a new license
     * 
     * @param string $licenseKey The license key
     * @param int $userId The user ID
     * @param string $plan The license plan
     * @param string $status The license status (default: 'inactive')
     * @param int $maxDomains The maximum number of domains (default: 1)
     * @param string $expiresAt The expiration date (default: 1 year from now)
     * @return int The ID of the created license
     */
    public static function create($licenseKey, $userId, $plan, $status = 'inactive', 
                               $maxDomains = 1, $expiresAt = null) {
        if ($expiresAt === null) {
            // Default expiration is 1 year from now
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 year'));
        }
        
        return insertRow('licenses', [
            'license_key' => $licenseKey,
            'user_id' => $userId,
            'plan' => $plan,
            'status' => $status,
            'max_domains' => $maxDomains,
            'expires_at' => $expiresAt
        ]);
    }
    
    /**
     * Update a license
     * 
     * @param int $id The license ID
     * @param array $data The data to update
     * @return int The number of rows affected
     */
    public static function update($id, array $data) {
        return updateRow('licenses', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete a license
     * 
     * @param int $id The license ID
     * @return int The number of rows affected
     */
    public static function delete($id) {
        return deleteRow('licenses', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Validate a license
     * 
     * @param string $licenseKey The license key
     * @param string $domain The domain to validate (optional)
     * @return array The validation result
     */
    public static function validate($licenseKey, $domain = null) {
        $license = self::getByKey($licenseKey);
        
        if (!$license) {
            return [
                'success' => false,
                'message' => 'Invalid license key.'
            ];
        }
        
        // Check if license has expired
        if ($license['status'] === 'expired' || strtotime($license['expires_at']) < time()) {
            // If it's not already marked as expired, update it
            if ($license['status'] !== 'expired') {
                self::update($license['id'], ['status' => 'expired']);
            }
            
            return [
                'success' => false,
                'message' => 'This license key has expired.',
                'licenseDetails' => [
                    'licenseKey' => $licenseKey,
                    'status' => 'expired',
                    'expiryDate' => $license['expires_at'],
                    'renewalUrl' => 'https://rankolab.com/renew?key=' . urlencode($licenseKey)
                ]
            ];
        }
        
        $result = [
            'success' => true,
            'licenseDetails' => [
                'licenseKey' => $licenseKey,
                'plan' => $license['plan'],
                'status' => $license['status'],
                'expiryDate' => $license['expires_at'],
                'maxDomains' => $license['max_domains']
            ]
        ];
        
        // If a domain is provided, check if it's activated for this license
        if ($domain) {
            $domainStatus = self::getDomainStatus($license['id'], $domain);
            $result['licenseDetails']['domainStatus'] = $domainStatus;
            
            // Count active domains
            $activeDomains = self::countActiveDomains($license['id']);
            $result['licenseDetails']['activeDomains'] = $activeDomains;
        }
        
        return $result;
    }
    
    /**
     * Activate a license for a domain
     * 
     * @param string $licenseKey The license key
     * @param string $domain The domain to activate
     * @param string $email The user's email (optional)
     * @return array The activation result
     */
    public static function activate($licenseKey, $domain, $email = null) {
        $license = self::getByKey($licenseKey);
        
        if (!$license) {
            return [
                'success' => false,
                'message' => 'Invalid license key.'
            ];
        }
        
        // Check if license has expired
        if ($license['status'] === 'expired' || strtotime($license['expires_at']) < time()) {
            return [
                'success' => false,
                'message' => 'This license key has expired and cannot be activated.',
                'renewalUrl' => 'https://rankolab.com/renew?key=' . urlencode($licenseKey)
            ];
        }
        
        // Check if the domain is already activated
        $existingDomain = fetchRow(
            "SELECT * FROM domains WHERE license_id = :license_id AND domain_name = :domain_name",
            ['license_id' => $license['id'], 'domain_name' => $domain]
        );
        
        if ($existingDomain) {
            if ($existingDomain['status'] === 'active') {
                return [
                    'success' => true,
                    'message' => 'This domain is already activated for this license.',
                    'activationDetails' => [
                        'licenseKey' => $licenseKey,
                        'domain' => $domain,
                        'activationDate' => $existingDomain['activation_date'],
                        'activationId' => $existingDomain['id'],
                        'expiryDate' => $license['expires_at']
                    ]
                ];
            } else {
                // Reactivate a previously deactivated domain
                updateRow('domains', 
                       ['status' => 'active', 'deactivation_date' => null], 
                       'id = :id', 
                       ['id' => $existingDomain['id']]);
                
                return [
                    'success' => true,
                    'message' => 'Domain successfully reactivated for ' . $domain,
                    'activationDetails' => [
                        'licenseKey' => $licenseKey,
                        'domain' => $domain,
                        'activationDate' => date('Y-m-d H:i:s'),
                        'activationId' => $existingDomain['id'],
                        'expiryDate' => $license['expires_at']
                    ]
                ];
            }
        }
        
        // Check if max domains limit reached
        $activeDomains = self::countActiveDomains($license['id']);
        
        if ($activeDomains >= $license['max_domains']) {
            return [
                'success' => false,
                'message' => 'Maximum number of domains reached for this license. Please deactivate a domain or upgrade your plan.',
                'upgradeUrl' => 'https://rankolab.com/upgrade?key=' . urlencode($licenseKey)
            ];
        }
        
        // Activate the license if it's inactive
        if ($license['status'] === 'inactive') {
            self::update($license['id'], ['status' => 'active']);
        }
        
        // Insert the domain
        $domainId = insertRow('domains', [
            'license_id' => $license['id'],
            'domain_name' => $domain,
            'status' => 'active'
        ]);
        
        // Generate unique activation ID
        $activationId = 'ACT-' . date('ymd') . '-' . substr(md5($licenseKey . $domain . date('ymd')), 0, 10);
        
        return [
            'success' => true,
            'message' => 'License successfully activated for ' . $domain,
            'activationDetails' => [
                'licenseKey' => $licenseKey,
                'domain' => $domain,
                'activationDate' => date('Y-m-d H:i:s'),
                'activationId' => $activationId,
                'expiryDate' => $license['expires_at']
            ]
        ];
    }
    
    /**
     * Deactivate a license for a domain
     * 
     * @param string $licenseKey The license key
     * @param string $domain The domain to deactivate
     * @return array The deactivation result
     */
    public static function deactivate($licenseKey, $domain) {
        $license = self::getByKey($licenseKey);
        
        if (!$license) {
            return [
                'success' => false,
                'message' => 'Invalid license key.'
            ];
        }
        
        // Check if the domain is activated for this license
        $existingDomain = fetchRow(
            "SELECT * FROM domains WHERE license_id = :license_id AND domain_name = :domain_name AND status = 'active'",
            ['license_id' => $license['id'], 'domain_name' => $domain]
        );
        
        if (!$existingDomain) {
            return [
                'success' => false,
                'message' => 'This domain is not activated for this license.'
            ];
        }
        
        // Deactivate the domain
        updateRow('domains', 
               ['status' => 'inactive', 'deactivation_date' => date('Y-m-d H:i:s')], 
               'id = :id', 
               ['id' => $existingDomain['id']]);
        
        // If no more active domains, set license to inactive
        $activeDomains = self::countActiveDomains($license['id']);
        
        if ($activeDomains === 0) {
            self::update($license['id'], ['status' => 'inactive']);
        }
        
        return [
            'success' => true,
            'message' => 'License successfully deactivated for ' . $domain,
            'deactivationDetails' => [
                'licenseKey' => $licenseKey,
                'domain' => $domain,
                'deactivationDate' => date('Y-m-d H:i:s')
            ]
        ];
    }
    
    /**
     * Get the status of a domain for a license
     * 
     * @param int $licenseId The license ID
     * @param string $domain The domain name
     * @return string The domain status ('active', 'inactive', or 'not_found')
     */
    public static function getDomainStatus($licenseId, $domain) {
        $existingDomain = fetchRow(
            "SELECT * FROM domains WHERE license_id = :license_id AND domain_name = :domain_name",
            ['license_id' => $licenseId, 'domain_name' => $domain]
        );
        
        if (!$existingDomain) {
            return 'not_found';
        }
        
        return $existingDomain['status'];
    }
    
    /**
     * Count the number of active domains for a license
     * 
     * @param int $licenseId The license ID
     * @return int The number of active domains
     */
    public static function countActiveDomains($licenseId) {
        $result = fetchRow(
            "SELECT COUNT(*) as count FROM domains WHERE license_id = :license_id AND status = 'active'",
            ['license_id' => $licenseId]
        );
        
        return $result['count'];
    }
    
    /**
     * Get all licenses
     * 
     * @param int $limit The maximum number of licenses to return
     * @param int $offset The offset for pagination
     * @return array The licenses
     */
    public static function getAll($limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT l.*, u.email, u.name as user_name, 
                (SELECT COUNT(*) FROM domains d WHERE d.license_id = l.id AND d.status = 'active') as active_domains 
             FROM licenses l 
             JOIN users u ON l.user_id = u.id 
             ORDER BY l.created_at DESC 
             LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Count the total number of licenses
     * 
     * @return int The number of licenses
     */
    public static function count() {
        $result = fetchRow("SELECT COUNT(*) as count FROM licenses");
        return $result['count'];
    }
    
    /**
     * Count the number of licenses by status
     * 
     * @param string $status The status to count
     * @return int The number of licenses with the specified status
     */
    public static function countByStatus($status) {
        $result = fetchRow("SELECT COUNT(*) as count FROM licenses WHERE status = :status", ['status' => $status]);
        return $result['count'];
    }
    
    /**
     * Generate a unique license key
     * 
     * @param string $plan The license plan
     * @return string The generated license key
     */
    public static function generateKey($plan) {
        $prefix = 'RANKO-' . strtoupper($plan);
        $random = strtoupper(bin2hex(random_bytes(8)));
        
        // Format as RANKO-PLAN-XXXX-XXXX-XXXX
        $parts = str_split($random, 4);
        return $prefix . '-' . implode('-', $parts);
    }
}