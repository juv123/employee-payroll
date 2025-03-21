<?php
session_start();
require_once 'Database.php';
require 'ExitProcess.php';

$database = new Database();
$db = $database->getConnection();
$exitProcess = new ExitProcess($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_SESSION['employee_id'];
    $reason = $_POST['reason'];
    $exitProcess->requestEmployeeExit($employee_id, $reason);
    echo "<script>alert('Exit request submitted successfully.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Exit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        button {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Request Exit</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Reason:</label>
            <textarea name="reason" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Submit Exit Request</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
