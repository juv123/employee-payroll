<?php
class Database {
    private $host = "localhost";   // Correct host for XAMPP
    private $db_name = "employee-payroll";  // Ensure this database exists
    private $username = "root";   // Default XAMPP username is 'root'
    private $password = "";       // Default XAMPP password is empty (leave this blank)
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage(); // Detailed error message
        }
        return $this->conn;
    }
}
?>
