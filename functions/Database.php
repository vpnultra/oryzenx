<?php
/**
 * Database Connection Helper
 */

class Database {
    private $host;
    private $db_name;
    private $user;
    private $pass;
    private $conn;

    public function __construct() {
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
    }

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db_name);
        
        if ($this->conn->connect_error) {
            die('Connection Error: ' . $this->conn->connect_error);
        }
        
        $this->conn->set_charset(DB_CHARSET);
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    public function getLastId() {
        return $this->conn->insert_id;
    }

    public function getError() {
        return $this->conn->error;
    }

    public function close() {
        $this->conn->close();
    }
}

// Create global database connection
$db = new Database();
$db->connect();
?>