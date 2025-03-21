<?php
require_once 'Database.php';
require 'ExitProcess.php';

$database = new Database();
$db = $database->getConnection();
$exitProcess = new ExitProcess($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exit_id = $_POST['exit_id'];
    $status = $_POST['status'];
    $final_settlement = $_POST['final_settlement'] ?? 0;
    $exit_date = $_POST['exit_date'] ?? NULL;
    $exitProcess->processExitRequest($exit_id, $status, $final_settlement, $exit_date);
    echo "<script>alert('Exit request processed successfully.'); window.location.reload();</script>";
}

// Fetch pending requests
$requests = $exitProcess->fetchPendingRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Exit Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .btn-group {
            display: flex;
            gap: 5px;
        }
        .form-control {
            width: auto;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Admin Exit Approvals</h2>

    <?php if (!empty($requests)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Email</th>
                        <th>Reason</th>
                        <th>Applied Date</th>
                        <th>Final Settlement</th>
                        <th>Exit Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?= htmlspecialchars($req['name']) ?></td>
                            <td><?= htmlspecialchars($req['emailid']) ?></td>
                            <td><?= htmlspecialchars($req['reason']) ?></td>
                            <td><?= htmlspecialchars($req['applied_date']) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="exit_id" value="<?= $req['id'] ?>">
                                    <input type="number" name="final_settlement" class="form-control" placeholder="Amount">
                            </td>
                            <td>
                                    <input type="date" name="exit_date" class="form-control">
                            </td>
                            <td class="btn-group">
                                    <button type="submit" name="status" value="approved" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="alert alert-warning text-center">No pending exit requests.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
