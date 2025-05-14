<?php
/**
 * Database Connection
 * 
 * Establishes a connection to the PostgreSQL database using environment variables
 */

/**
 * Get PDO database connection
 * 
 * @return PDO The database connection
 */
function getDbConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = getenv('PGHOST');
        $port = getenv('PGPORT');
        $dbname = getenv('PGDATABASE');
        $user = getenv('PGUSER');
        $password = getenv('PGPASSWORD');
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        
        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            // Log the error but don't expose sensitive information
            error_log('Database connection error: ' . $e->getMessage());
            throw new Exception('Database connection failed. Please check the configuration.');
        }
    }
    
    return $pdo;
}

/**
 * Execute a SQL query with parameters
 * 
 * @param string $sql The SQL query
 * @param array $params The parameters for the query
 * @return PDOStatement The executed statement
 */
function executeQuery($sql, $params = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch a single row from the database
 * 
 * @param string $sql The SQL query
 * @param array $params The parameters for the query
 * @return array|false The row or false if not found
 */
function fetchRow($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Fetch all rows from the database
 * 
 * @param string $sql The SQL query
 * @param array $params The parameters for the query
 * @return array The rows
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Insert a row into a table and return the ID
 * 
 * @param string $table The table name
 * @param array $data The data to insert (column => value)
 * @return int The ID of the inserted row
 */
function insertRow($table, $data) {
    $columns = array_keys($data);
    $placeholders = array_map(function($col) {
        return ":$col";
    }, $columns);
    
    $sql = sprintf(
        "INSERT INTO %s (%s) VALUES (%s) RETURNING id",
        $table,
        implode(', ', $columns),
        implode(', ', $placeholders)
    );
    
    $stmt = executeQuery($sql, $data);
    return $stmt->fetchColumn();
}

/**
 * Update a row in a table
 * 
 * @param string $table The table name
 * @param array $data The data to update (column => value)
 * @param string $whereClause The WHERE clause (e.g., "id = :id")
 * @param array $whereParams The parameters for the WHERE clause
 * @return int The number of rows affected
 */
function updateRow($table, $data, $whereClause, $whereParams = []) {
    $setClauses = [];
    foreach (array_keys($data) as $column) {
        $setClauses[] = "$column = :$column";
    }
    
    $sql = sprintf(
        "UPDATE %s SET %s, updated_at = CURRENT_TIMESTAMP WHERE %s",
        $table,
        implode(', ', $setClauses),
        $whereClause
    );
    
    $params = array_merge($data, $whereParams);
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}

/**
 * Delete a row from a table
 * 
 * @param string $table The table name
 * @param string $whereClause The WHERE clause (e.g., "id = :id")
 * @param array $whereParams The parameters for the WHERE clause
 * @return int The number of rows affected
 */
function deleteRow($table, $whereClause, $whereParams = []) {
    $sql = sprintf("DELETE FROM %s WHERE %s", $table, $whereClause);
    $stmt = executeQuery($sql, $whereParams);
    return $stmt->rowCount();
}