<?php
require 'Database.php';
require 'Salary.php';
session_start();
if($_SESSION['role']!=='admin'){
header("Location:login.php");
exit;
}
$database=new Database();
$db=$database->getConnection();
$salary=new Salary($db);
$department=$_SESSION['department'];
//fetch employees
$stmt=$db->prepare("SELECT id,name,employee_code from employees where status = :status and department=:department");
$stmt->execute([
       "status" => "active" ,
       "department"=>$department
  ]);
$employees=$stmt->fetchAll(PDO::FETCH_ASSOC);
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['generate_payslip']) ){
    $employee_id=$_POST['employee_id'];
    $month=$_POST['month'];
    $year=$_POST['year'];
    $salary->generatePayslip($employee_id,$month,$year);
    echo "<script>alert('Payslip for $month $year has generated');</script>";
}
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Generate Payslips</title>
</head>
<body>
    <h2>Generate Employee Payslips</h2>
    
    <form method="post">
        <label>Select Employee:</label>
        <select name="employee_id">
            <?php foreach ($employees as $emp) { ?>
                <option value="<?php echo $emp['id']; ?>"><?php echo $emp['employee_code'] . ' - ' . $emp['name']; ?></option>
            <?php } ?>
        </select>
        
        <label>Select Month:</label>
        <select name="month">
            <?php 
            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            foreach ($months as $month) { 
                echo "<option value='$month'>$month</option>";
            } 
            ?>
        </select>

        <label>Select Year:</label>
        <select name="year">
            <?php 
            $currentYear = date("Y");
            for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                echo "<option value='$i'>$i</option>";
            }
            ?>
        </select>

        <button type="submit" name="generate_payslip">Generate Payslip</button>
    </form>

    <h3>All Payslips</h3>
    <ul>
        <?php foreach ($employees as $emp) { 
            foreach ($months as $month) {
                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                    $file = "payslips/{$emp['employee_code']}_{$month}_{$i}.pdf";
                    if (file_exists($file)) {
                        echo "<li><a href='$file' download>Download Payslip for {$emp['name']} ($month $i)</a></li>";
                    }
                }
            }
        } ?>
    </ul>

</body>
</html>

