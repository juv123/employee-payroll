<?php
require 'Database.php';
require 'Salary.php';

session_start();

$database = new Database();
$db = $database->getConnection();
$salary = new Salary($db);

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    die("Unauthorized access!");
}

$username = $_SESSION['username'];
//echo $username;
$role = $_SESSION['role'];

// Initialize variables to prevent "Undefined variable" error
$month = date('F'); // Default to current month
$year = date('Y'); // Default to current year

if ($role === 'employee') {
    // Fetch employee ID and name
    $stmt = $db->prepare("SELECT id, name FROM employees WHERE emailid = :emailid");
    $stmt->execute([':emailid' => $username]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    //print_r($employee);
    if (!$employee) {
        die("Employee not found!");
    }

    $employee_id = $employee['id'];
    $employee_name = $employee['name'];

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['month'])) {
            $month = $_POST['month'];
        }
        if (isset($_POST['year'])) {
            $year = $_POST['year'];
        }

        // Generate payslip using Salary class
        $payslipPath = $salary->generatePayslip($employee_id, $month, $year);

        if ($payslipPath !== true) {
            echo "<p style='color: red;'>Error: $payslipPath</p>";
        } else {
            // Redirect to download the generated payslip
            header("Location: $payslipPath");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employee Payslip</title>
</head>
<body>
    <h2>Download Payslip</h2>

    <form method="post">
        <?php if ($role === 'employee') { ?>
            <p>Employee: <?php echo htmlspecialchars($employee_name); ?></p>

            <label>Select Month:</label>
            <select name="month">
                <?php 
                $months = [
                    "January", "February", "March", "April", "May", "June", 
                    "July", "August", "September", "October", "November", "December"
                ];
                foreach ($months as $m) { 
                    $selected = ($m == $month) ? "selected" : "";
                    echo "<option value='$m' $selected>$m</option>";
                }
                ?>
            </select>

            <label>Select Year:</label>
            <select name="year">
                <?php 
                $currentYear = date("Y");
                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                    $selected = ($i == $year) ? "selected" : "";
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>

            <button type="submit" name="generate_payslip">Generate & Download Payslip</button>
        <?php } ?>
    </form>
</body>
</html>
