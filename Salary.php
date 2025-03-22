<?php
require __DIR__ . '/vendor/setasign/fpdf/fpdf.php';
require_once 'Database.php';
class Salary{
    private $conn;
    public function __construct($db){
        $this->conn=$db;
    }
    //fetch emp salary details
    public function getEmployeeSalaryDetails($employee_id){
        $stmt = $this->conn->prepare("SELECT id, name, employee_code, department, position, salary 
        FROM employees 
        WHERE id = :id AND status = :status");
        $status='active';
   $stmt->execute([
  "id" => $employee_id,
   "status" =>$status // Ensure this matches your DB value
]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function generatePayslip($employee_id,$month,$year){
     $employee_salary=$this->getEmployeeSalaryDetails($employee_id);
     if(!$employee_salary)
     return "Employee salary details not found";
     //calculations
     $fixedPay=$employee_salary['salary']*0.90;
     $variablePay=$employee_salary['salary']*0.10;
     $pf=3600;
     $totalSalary=$fixedPay+$variablePay;
     $netPay=$totalSalary-$pf;
      
     //generate payslip pdf
     $pdf=new FPDF();
     $pdf->AddPage();
     $pdf->SetFont('Arial','B',16);
     $pdf->cell(0,10,"Payslip for ".$month."/".$year,0,1,'C');

     $pdf->SetFont('Arial','',12);
     $pdf->cell(50,10,"Employee Code: ".$employee_salary['employee_code'],0,1);
     $pdf->cell(50,10,"Name: ".$employee_salary['name'],0,1);
     $pdf->cell(50,10,"Department: ".$employee_salary['department'],0,1);
     $pdf->cell(50,10,"Position: ".$employee_salary['position'],0,1);
     $pdf->Ln(10);
 
     $pdf->cell(50,10,"Fixed Pay(90%): ₹".number_format($fixedPay, 2),0,1);
     $pdf->cell(50,10,"Variable Pay(10%): ₹".number_format($variablePay, 2),0,1);
     $pdf->cell(50,10,"PF: ₹".number_format($pf, 2),0,1);
     $pdf->cell(50,10,"Total Salary: ₹".number_format($totalSalary, 2),0,1);
     $pdf->cell(50,10,"Net Pay: ₹".number_format($netPay, 2),0,1);
     
     $pdfFileName = "payslips/{$employee_salary['employee_code']}_{$month}_{$year}.pdf";
     $pdf->Output($pdfFileName, 'F'); // Save PDF file
     return $pdfFileName;




    }
   

}
