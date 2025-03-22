<?php 
session_start();
class Attendance{
    private $conn;
    private $table_name="attendance";
    public function __construct($db){
    $this->conn=$db;
    }
    public function markAttendance($employeeid,$status){
        //check attendance already taken:
        $date=date('Y-m-d');
        date_default_timezone_set("Asia/Kolkata"); // Set timezone
        $time=date('H:i:s');
        $enteredBy=$_SESSION['userid'];
        if(isset($_POST))
        $status=$_POST['status'];
        $query='select id from attendance where employee_id=:employee_id and date=:date';
        $stmt=$this->conn->prepare($query);
        $stmt->bindParam(':employee_id',$employeeid);
        $stmt->bindParam(':date',$date);
        $stmt->execute();
        //echo $stmt->rowCount();
       if($stmt->rowCount()>0) 
       {
        die("Attendance already Marked");
        exit();
       }
       else{
        //insert
        $query='insert into '.$this->table_name.' (employee_id,date,status,time,entered_by) values(:employee_id,:date,:status,:time,:entered_by)';
        $stmt=$this->conn->prepare($query);
        $stmt->bindParam(':employee_id',$employeeid);
        $stmt->bindParam(':date',$date);
        $stmt->bindParam(':status',$status);
        $stmt->bindParam(':time',$time);
        $stmt->bindParam(':entered_by',$enteredBy);
        return $stmt->execute() ? "Attendance Marked Successfully":"Error in Attendance Marking";
       
       }


    }
    public function getAllAttendance(){
        $query="select  a.id, e.employee_code, e.name, a.date, a.status  from ".$this->table_name." a JOIN employees e on e.id=a.employee_id where e.status=:status order by a.date desc";
        $stmt=$this->conn->prepare($query);
        $status='active';
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateAttendance($id,$status){
        $query="update ".$this->table_name." set status=:status where id=:id";
        $stmt=$this->conn->prepare($query);
       return $stmt->execute(['status' => $status, 'id' => $id]);
    }


}