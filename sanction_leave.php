<?php
require 'Database.php';
require 'Leave.php';
require 'send_email.php';

//session_start(); // Ensure session is started

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$database = new Database();
$db = $database->getConnection();
$leave = new Leave($db);

$department = $_SESSION['department'] ?? null;
if (!$department) {
    die("Department not found in session.");
}

// Fetch pending leave requests for employees in admin’s department
$stmt = $db->prepare("
    SELECT lr.id, lr.employee_id, lr.leave_type, lr.from_date, lr.to_date, lr.reason, lr.applied_on,
           e.employee_code, e.name 
    FROM leave_applications lr
    JOIN employees e ON lr.employee_id = e.id
    WHERE e.department = :department AND lr.status = 'pending'
");
$stmt->execute([':department' => $department]);
$pendingLeaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_id = $_POST['leave_id'];
    $status = $_POST['status']; // Approved or Rejected

    $result = $leave->sanctionLeave($leave_id, $status);
    echo "<script>alert('$result'); window.location.href='sanction_leave.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leave Sanction - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Sanction Leave Requests</h2>

        <form method="post" class="space-y-4">
            <div>
                <label for="leave_id" class="block text-gray-600 font-medium">Select Pending Leave Request:</label>
                <select name="leave_id" required class="w-full p-2 border rounded-lg">
                    <option value="">-- Select Leave Request --</option>
                    <?php foreach ($pendingLeaves as $leave) { ?>
                        <option value="<?php echo htmlspecialchars($leave['id']); ?>">
                            <?php echo htmlspecialchars($leave['employee_code']) . " - " . htmlspecialchars($leave['name']); ?>
                            (<?php echo htmlspecialchars($leave['leave_type']); ?>) - 
                            <?php echo htmlspecialchars($leave['from_date']); ?> to <?php echo htmlspecialchars($leave['to_date']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label for="status" class="block text-gray-600 font-medium">Action:</label>
                <select name="status" required class="w-full p-2 border rounded-lg">
                    <option value="Approved">Approve</option>
                    <option value="Rejected">Reject</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                Submit
            </button>
        </form>
    </div>

</body>
</html>
