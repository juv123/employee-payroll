<?php
require_once 'Database.php';
session_start();

class Leave
{
private $conn;
private $table_name='leave_applications';
public function __construct(){
    $db=new Database();
    $this->conn=$db->getConnection();
}
public function applyLeave($leave_type,$start_date,$end_date,$reason){
 
//calculate total allowed leaves
date_default_timezone_set("Asia/Kolkata"); // Set timezone
$start=new DateTime($start_date);
$end=new DateTime($end_date);
$interval=$start->diff($end);
$total_days=$interval->days+1;
$date=date('Y-m-d');
//check leave balance
$query = "SELECT allowed_leaves FROM employees WHERE id = :id";
$stmt = $this->conn->prepare($query);
$employee_id=$_SESSION['userid'];
$stmt->bindValue(':id', $employee_id, PDO::PARAM_INT); // Ensure it's an integer
$stmt->execute();
$employee_leaves = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single row
//print_r($employee_leaves);
if (!$employee_leaves || $employee_leaves['allowed_leaves'] < $total_days) {
    return "You don't have enough leave balance.";
}
else{
    //insert to leave_applications
    $stmt = $this->conn->prepare("INSERT INTO ".$this->table_name."  (employee_id, leave_type, applied_on, from_date, to_date, reason, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$employee_id, $leave_type, $date, $start_date, $end_date, $reason]);
    return "Leave applied Successfully!";
    

}
}
public function sanctionLeave($leave_id, $status) {
    // Fetch leave details
    $stmt = $this->conn->prepare("
        SELECT employee_id, from_date, to_date,reason,applied_on, status 
        FROM leave_applications
        WHERE id = :leave_id
    ");
    $stmt->bindValue(':leave_id', $leave_id, PDO::PARAM_INT);
    $stmt->execute();
    $leave = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$leave) {
        return "Leave request not found.";
    }

    // Prevent duplicate approval
    if ($leave['status'] == 'approved' || $leave['status'] == 'rejected') {
        return "This leave request has already been processed.";
    }

    $employee_id = $leave['employee_id'];
    $start_date = new DateTime($leave['from_date']);
    $end_date = new DateTime($leave['to_date']);
    $total_days = $start_date->diff($end_date)->days + 1;
    //echo $total_days;
    try {
        $this->conn->beginTransaction();
          
      
            // Get employee leave balance
            $stmt = $this->conn->prepare("SELECT name,employee_code,allowed_leaves,emailid FROM employees WHERE id = :id and status=:status");
            $stmt->bindParam(':id', $employee_id);
            $leave_status='active';
            $stmt->bindParam(':status', $leave_status);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            //print_r($employee);
            if (!$employee) {
                $this->conn->rollBack();
                return "Employee not found.";
            }
             
            $allowed_leaves = $employee['allowed_leaves'];
            //echo $allowed_leaves;
            if ($allowed_leaves < $total_days) {
                $this->conn->rollBack();
                return "Not enough leave balance for approval.";
            }
             // Update leave status
        $stmt = $this->conn->prepare("
        UPDATE leave_applications SET status = :status WHERE id = :leave_id
    ");
    if ($stmt->execute(['status' => $status, 'leave_id' => $leave_id])) {
        // Send email notification
        $emailSent = sendEmailNotification($employee['emailid'], $employee['name'],$leave['applied_on'], $status);
        if ($emailSent === true) {
            echo "Leave request $status successfully. Email sent.";
        } else {
            echo "Leave request $status, but email failed: $emailSent";
        }
    } else {
        echo "Failed to update leave request.";
    }

            
            if (strtolower($status) == 'approved') {
                $new_leave_balance = $allowed_leaves - $total_days;
            //echo $new_leave_balance;
            
            $stmt = $this->conn->prepare("
                UPDATE employees SET allowed_leaves = :new_leave_balance WHERE id = :id
            ");
            $stmt->execute([
                ':new_leave_balance' => $new_leave_balance,
                ':id' => $employee_id
            ]);
            }

       

        $this->conn->commit();
        return "Leave has been {$status} successfully!";
    } catch (Exception $e) {
        $this->conn->rollBack();
        return "Error processing leave: " . $e->getMessage();
    }
}

}