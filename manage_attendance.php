<?php
//session_start();
require 'Database.php';
require 'Attendance.php';

$database = new Database();
$db = $database->getConnection();
$attendance = new Attendance($db);

// Fetch active employees
$query = "SELECT id, employee_code, name FROM employees WHERE status=:status";
$stmt = $db->prepare($query);
$status='active';
$stmt->execute(['status'=>$status]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Attendance Data
$attendanceRecords = $attendance->getAllAttendance();

// Handle Attendance Marking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $employee_id = $_POST['employee_id'];
    $status = $_POST['status'];

    $message = $attendance->markAttendance($employee_id, $status);
}

// Handle Attendance Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_attendance'])) {
    $att_id = $_POST['attendance_id'];
    $new_status = $_POST['status'];

    $attendance->updateAttendance($att_id, $new_status);
    header("Location: manage_attendance.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white text-center">
                <h3>Manage Attendance</h3>
            </div>
            <div class="card-body">
                
                <!-- Attendance Marking Form -->
                <h4 class="mb-3">Mark Attendance</h4>
                <?php if (isset($message)) { echo "<p class='alert alert-info'>$message</p>"; } ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><strong>Select Employee:</strong></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- Select Employee --</option>
                            <?php foreach ($employees as $employee) { ?>
                                <option value="<?php echo $employee['id']; ?>">
                                    <?php echo $employee['employee_code'] . " - " . $employee['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Status:</strong></label>
                        <select name="status" class="form-select" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </div>

                    <button type="submit" name="mark_attendance" class="btn btn-success w-100">Mark Attendance</button>
                </form>

                <hr>

                <!-- Attendance Sheet -->
                <h4 class="mb-3">Attendance Sheet</h4>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceRecords as $record) { ?>
                            <tr>
                                <td><?php echo $record['id']; ?></td>
                                <td><?php echo $record['employee_code']; ?></td>
                                <td><?php echo $record['name']; ?></td>
                                <td><?php echo $record['date']; ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="attendance_id" value="<?php echo $record['id']; ?>">
                                        <select name="status" class="form-select">
                                            <option value="Present" <?php echo ($record['status'] == 'present') ? 'selected' : ''; ?>>Present</option>
                                            <option value="Absent" <?php echo ($record['status'] == 'absent') ? 'selected' : ''; ?>>Absent</option>
                                        </select>
                        </td>
                        <td>
                                        <button type="submit" name="update_attendance" class="btn btn-warning btn-sm mt-1">Update</button>
                                        </td></form>
                               
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
