<?php
session_start();
require 'Database.php';

// Check if user is logged in
if (!isset($_SESSION['username'], $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <div class="container-fluid">
        <div class="card shadow-lg">
            <div class="card-header">
                <h3>Dashboard</h3>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <div class="card-body text-center">
                <h5>Welcome, <strong><?php echo htmlspecialchars($username); ?></strong>!</h5>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>

                <!-- Navbar -->
                <div class="d-flex justify-content-center my-3">
    <ul class="nav nav-pills">
        <?php if ($_SESSION['role'] === 'admin') { ?>  
            <li class="nav-item">
                <button class="nav-link" id="toggleEmpBtn" >Employees</button>
            </li>
            <li class="nav-item"><a class="nav-link" href="manage_attendance.php">Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_leaves.php">Leaves</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_salary.php">Salary</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_exit.php">Exit</a></li>
        <?php } ?>
    </ul>
</div>

                <!-- Employee List (Initially Hidden) -->
                <div id="employeeList" class="full-width-container" style="display:none">
                    <h3 class="text-center">Employees List</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Date Of Joining</th>
                                    <th>Email ID</th>
                                    <th>Contact No</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Address</th>
                                    <th>Salary</th>
                                    <th>Adhar No</th>
                                    <th>Earned Leaves</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTable">
                                <!-- Employee data will be loaded here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        
        function toggleEmployees() {
            let empDiv = document.getElementById("employeeList");

            if (empDiv.style.display === "none") {
                empDiv.style.display = "block";
                fetchEmployees(); // Load employees when shown
            } else {
                empDiv.style.display = "none";
            }
        }
        document.addEventListener("DOMContentLoaded", function () {
        console.log("JavaScript loaded!"); // Debug message
    document.getElementById("toggleEmpBtn").addEventListener("click", function () {
        toggleEmployees();
    });
});
        function fetchEmployees() {
            fetch("fetch_employees.php")
                .then(response => response.json())
                .then(data => {
                    let table = document.getElementById("employeeTable");
                    table.innerHTML = "";

                    data.forEach(emp => {
                        let row = `<tr>
                            <td>${emp.employee_code}</td>
                            <td>${emp.name}</td>
                            <td>${emp.date_of_joining}</td>
                            <td>${emp.emailid}</td>
                            <td>${emp.contactno}</td>
                            <td>${emp.department}</td>
                            <td>${emp.position}</td>
                            <td>${emp.address}</td>
                            <td>${emp.salary}</td>
                            <td>${emp.adharno}</td>
                            <td>${emp.allowed_leaves}</td>
                        </tr>`;
                        table.innerHTML += row;
                    });
                })
                .catch(error => console.error("Error fetching employees:", error));
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
