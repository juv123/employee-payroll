<?php
//session_start();
require_once "Leave.php";

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$leave = new Leave();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_SESSION['userid'];
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    $message = $leave->applyLeave($leave_type, $start_date, $end_date, $reason);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Leave</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add CSS if needed -->
</head>
<body>
    <h2>Apply for Leave</h2>

    <?php if (isset($message)) { echo "<p style='color:red;'>$message</p>"; } ?>

    <form method="POST" action="">
        <label for="leave_type">Leave Type:</label>
        <select name="leave_type" required>
            <option value="sick">Sick Leave</option>
            <option value="casual">Casual Leave</option>
            <option value="paid">Paid Leave</option>
        </select>

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" required>

        <label for="reason">Reason:</label>
        <textarea name="reason" required></textarea>

        <button type="submit">Apply Leave</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
