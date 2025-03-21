<?php
require_once 'Database.php';
require __DIR__ . '/vendor/setasign/fpdf/fpdf.php';
require 'send_exit_status.php'; // Email Notification
//session_start();

class ExitProcess
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Employee submits exit request
    public function requestEmployeeExit($employee_id, $reason)
    {
        $applied_date = date('Y-m-d');
        $exit_date = date('Y-m-d', strtotime($applied_date . ' +30 days')); // Applied Date + 30 Days
        $stmt = $this->conn->prepare("INSERT INTO exits (employee_id, applied_date, exit_date, final_settlement_paid, reason, status) 
            VALUES (:employee_id, :applied_date, $exit_date, 0, :reason, 'pending')");

        if ($stmt->execute([':employee_id' => $employee_id, ':applied_date' => $applied_date, ':reason' => $reason])) {
            echo "Exit request submitted. Waiting for admin approval.";
        } else {
            echo "Error submitting request.";
        }
    }

    // Fetch admin email based on department
    private function getAdminEmail($department)
    {
        $stmt = $this->conn->prepare("
            SELECT e.emailid, e.name 
            FROM users u 
            JOIN employees e ON CAST(u.username AS CHAR) = CAST(e.emailid AS CHAR)
            WHERE u.role = :role AND e.department = :department");

        $stmt->execute([':role' => 'admin', ':department' => $department]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Admin processes exit request
    public function processExitRequest($exit_id, $status, $final_settlement, $exit_date)
    {
        $stmt = $this->conn->prepare("UPDATE exits 
            SET status = :status, exit_date = :exit_date, final_settlement_paid = :final_settlement 
            WHERE id = :exit_id");

        if ($stmt->execute([':status' => $status, ':exit_date' => $exit_date, ':final_settlement' => $final_settlement, ':exit_id' => $exit_id])) {
            $this->notifyEmployee($exit_id, $status);
            if ($status == 'approved') {
                $this->generateExperienceLetter($exit_id);
            }
            echo "Exit request $status successfully.";
            //fetch empid
            $stmt = $this->conn->prepare("SELECT employee_id 
            FROM exits 
            WHERE id = :exit_id");

        $stmt->execute([':exit_id' => $exit_id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare("UPDATE employees 
            SET status = :status where id = :id");
            $stmt->execute([':status' => 'inactive', ':id'=>$employee['employee_id']]);

        } else {
            echo "Error processing request.";
        }
    }

    // Notify Employee via Email
    private function notifyEmployee($exit_id, $status)
    {
        $stmt = $this->conn->prepare("SELECT emp.name, emp.emailid 
            FROM exits e 
            JOIN employees emp ON e.employee_id = emp.id 
            WHERE e.id = :exit_id");

        $stmt->execute([':exit_id' => $exit_id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($employee) {
            sendExitNotification($employee['emailid'], $employee['name'], $status);
        }
    }

    // Generate PDF Experience Letter
    public function generateExperienceLetter($exit_id)
    {
        $stmt = $this->conn->prepare("SELECT emp.name,emp.employee_code,emp.department,emp.position, emp.date_of_joining, e.exit_date 
            FROM exits e 
            JOIN employees emp ON e.employee_id = emp.id 
            WHERE e.id = :exit_id");

        $stmt->execute([':exit_id' => $exit_id]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            die("Employee not found!");
        }

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Experience Certificate', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, "This is to certify that " . $employee['name'] ."-".$employee['employee_code'] .
            " worked as " . $employee['position'] . " In Department- ".$employee['department']." In our company from " . 
            $employee['date_of_joining'] . " to " . $employee['exit_date'] . 
            ". We appreciate their contributions and wish them success.");

        $pdf->Output("D", "Experience_Letter_" . $employee['name'] . ".pdf");
    }

    // Fetch Pending Requests (For Admin)
    public function fetchPendingRequests()
    {
        $stmt = $this->conn->prepare("SELECT e.id, emp.name, emp.emailid, e.reason, e.applied_date 
            FROM exits e 
            JOIN employees emp ON e.employee_id = emp.id 
            WHERE e.status = 'pending'");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
