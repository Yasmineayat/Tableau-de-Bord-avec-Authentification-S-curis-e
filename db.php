<?php
class Database {
    private $host = "localhost";
    private $db_name = "test123";
    private $username = "root";
    private $password = "";
    private $port = "3306"; 
    public $conn;

    public function getConnection(): PDO {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
