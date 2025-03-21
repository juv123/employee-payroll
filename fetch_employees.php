<?php
session_start();
include 'Database.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Fetch all employees
$query = "SELECT * FROM employees where status='active'";
$stmt = $db->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($employees);
?>
