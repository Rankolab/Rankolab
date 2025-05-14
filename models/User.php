<?php
/**
 * User Model
 * 
 * Represents a user in the Rankolab system
 */

require_once __DIR__ . '/../db/connection.php';

class User {
    /**
     * Get a user by ID
     * 
     * @param int $id The user ID
     * @return array|false The user data or false if not found
     */
    public static function getById($id) {
        return fetchRow("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }
    
    /**
     * Get a user by email
     * 
     * @param string $email The user email
     * @return array|false The user data or false if not found
     */
    public static function getByEmail($email) {
        return fetchRow("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }
    
    /**
     * Create a new user
     * 
     * @param string $name The user's name
     * @param string $email The user's email
     * @param string $password The plain text password
     * @param string $role The user role (default: 'user')
     * @return int The ID of the created user
     */
    public static function create($name, $email, $password, $role = 'user') {
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the user
        return insertRow('users', [
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role
        ]);
    }
    
    /**
     * Update a user
     * 
     * @param int $id The user ID
     * @param array $data The data to update
     * @return int The number of rows affected
     */
    public static function update($id, array $data) {
        // If password is being updated, hash it
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return updateRow('users', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete a user
     * 
     * @param int $id The user ID
     * @return int The number of rows affected
     */
    public static function delete($id) {
        return deleteRow('users', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Verify a user's password
     * 
     * @param string $email The user's email
     * @param string $password The plain text password
     * @return array|false The user data or false if authentication fails
     */
    public static function authenticate($email, $password) {
        $user = self::getByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get all users
     * 
     * @param int $limit The maximum number of users to return
     * @param int $offset The offset for pagination
     * @return array The users
     */
    public static function getAll($limit = 10, $offset = 0) {
        return fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    /**
     * Count the total number of users
     * 
     * @return int The number of users
     */
    public static function count() {
        $result = fetchRow("SELECT COUNT(*) as count FROM users");
        return $result['count'];
    }
    
    /**
     * Count the number of users by role
     * 
     * @param string $role The role to count
     * @return int The number of users with the specified role
     */
    public static function countByRole($role) {
        $result = fetchRow("SELECT COUNT(*) as count FROM users WHERE role = :role", ['role' => $role]);
        return $result['count'];
    }
}