<?php

class Database {
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    private string $port;
    public ?PDO $conn = null;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->db_name = getenv('DB_NAME') ?: 'gestion_missions';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASS') ?: 'root123';
        $this->port = getenv('DB_PORT') ?: '3306';  
    }

    public function getConnection(): PDO {
        if ($this->conn !== null) {
            return $this->conn;
        }
        try {
            $sslCa = getenv('DB_SSL_CA');

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT  => false
            ];

            if ($sslCa && file_exists($sslCa)) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
            }

            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};port={$this->port};charset=utf8mb4",
                $this->username,
                $this->password,
                $options
            );
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            throw $exception;
        }
        return $this->conn;
    }
}
