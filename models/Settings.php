<?php
/**
 * Settings Model
 * 
 * Manages application settings
 */

require_once __DIR__ . '/../db/connection.php';

class Settings {
    /**
     * Get a setting by key
     * 
     * @param string $key The setting key
     * @return string|null The setting value or null if not found
     */
    public static function get($key) {
        $setting = fetchRow("SELECT setting_value FROM settings WHERE setting_key = :key", ['key' => $key]);
        return $setting ? $setting['setting_value'] : null;
    }
    
    /**
     * Set a setting
     * 
     * @param string $key The setting key
     * @param string $value The setting value
     * @param string $group The setting group (optional)
     * @param bool $isPublic Whether the setting is publicly accessible (default: false)
     * @return bool Whether the operation was successful
     */
    public static function set($key, $value, $group = null, $isPublic = false) {
        // Convert boolean to true/false string for PostgreSQL
        $isPublicValue = $isPublic ? 'true' : 'false';
        
        // Check if the setting already exists
        $existing = fetchRow("SELECT id FROM settings WHERE setting_key = :key", ['key' => $key]);
        
        if ($existing) {
            // Update existing setting
            $result = updateRow(
                'settings',
                ['setting_value' => $value, 'setting_group' => $group, 'is_public' => $isPublicValue],
                'id = :id',
                ['id' => $existing['id']]
            );
            
            return $result > 0;
        } else {
            // Insert new setting
            $id = insertRow('settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
                'is_public' => $isPublicValue
            ]);
            
            return $id > 0;
        }
    }
    
    /**
     * Delete a setting
     * 
     * @param string $key The setting key
     * @return bool Whether the operation was successful
     */
    public static function delete($key) {
        $result = deleteRow('settings', 'setting_key = :key', ['key' => $key]);
        return $result > 0;
    }
    
    /**
     * Get all settings in a group
     * 
     * @param string $group The setting group
     * @param bool $publicOnly Whether to return only public settings
     * @return array The settings
     */
    public static function getGroup($group, $publicOnly = false) {
        $sql = "SELECT setting_key, setting_value FROM settings WHERE setting_group = :group";
        $params = ['group' => $group];
        
        if ($publicOnly) {
            $sql .= " AND is_public = TRUE";
        }
        
        $settings = fetchAll($sql, $params);
        
        // Convert to associative array
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }
    
    /**
     * Get all settings
     * 
     * @param bool $publicOnly Whether to return only public settings
     * @return array The settings grouped by group
     */
    public static function getAll($publicOnly = false) {
        $sql = "SELECT setting_key, setting_value, setting_group FROM settings";
        
        if ($publicOnly) {
            $sql .= " WHERE is_public = TRUE";
        }
        
        $settings = fetchAll($sql);
        
        // Group by setting_group
        $result = [];
        foreach ($settings as $setting) {
            $group = $setting['setting_group'] ?: 'default';
            if (!isset($result[$group])) {
                $result[$group] = [];
            }
            $result[$group][$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }
    
    /**
     * Initialize default settings
     * 
     * @return bool Whether the operation was successful
     */
    public static function initDefaults() {
        $defaults = [
            // General settings
            'site_name' => ['value' => 'Rankolab', 'group' => 'general', 'public' => true],
            'site_description' => ['value' => 'SEO and Content Management Platform', 'group' => 'general', 'public' => true],
            'admin_email' => ['value' => 'admin@rankolab.com', 'group' => 'general', 'public' => false],
            
            // API settings
            'api_rate_limit' => ['value' => '100', 'group' => 'api', 'public' => true],
            'api_version' => ['value' => '1.0', 'group' => 'api', 'public' => true],
            
            // Content generation settings
            'max_content_length' => ['value' => '5000', 'group' => 'content', 'public' => true],
            'plagiarism_threshold' => ['value' => '20', 'group' => 'content', 'public' => true],
            
            // License settings
            'license_plans' => ['value' => 'starter,pro,business,enterprise', 'group' => 'license', 'public' => true],
            'max_domains_starter' => ['value' => '1', 'group' => 'license', 'public' => true],
            'max_domains_pro' => ['value' => '5', 'group' => 'license', 'public' => true],
            'max_domains_business' => ['value' => '20', 'group' => 'license', 'public' => true],
            'max_domains_enterprise' => ['value' => '100', 'group' => 'license', 'public' => true]
        ];
        
        $success = true;
        
        foreach ($defaults as $key => $setting) {
            $result = self::set($key, $setting['value'], $setting['group'], $setting['public']);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
}