<?php
class User{
    private $conn;
    private $table_name="users";
    public function __construct($db){
    $this->conn=$db;
    }
    //user registration
    public function register($username,$password,$role,$department){
    $query="insert into ".$this->table_name."(username,password,role,department) values (:username,:password,:role,:department)";
    $stmt=$this->conn->prepare($query);
    $stmt->bindParam(":username",$username);
    $cleanPassword = str_replace(' ', '', $password); // Remove spaces
    $hashedPassword = password_hash($cleanPassword, PASSWORD_BCRYPT);
    $stmt->bindParam(":password", $hashedPassword);
    $stmt->bindParam(":role",$role);
    $stmt->bindParam(":department",$department);
    return $stmt->execute();
    }
    //user authentication
    public function login($username,$password){
    $query="select id,username,password,role,department from ".$this->table_name." where username=:username";
    $stmt=$this->conn->prepare($query);
    $stmt->bindParam(":username",$username);
     $stmt->execute();
    $user=$stmt->fetch(PDO::FETCH_ASSOC);
    if($user && password_verify($password,$user['password'])){
        return $user;
              
    }
    else
    return false;
    }
    public function resetPassword($username,$newpassword){
        $query="update ".$this->table_name."set password=:password where username=:username ";
        $stmt=$this->conn->prepare($query);
        $stmt->bindParam(":username",$username);
        $stmt->bindParam(":password", password_hash($newpassword,PASSWORD_BCRYPT));
        return $stmt->execute();
       
    }
    public function getEmployeeId($username){
        $query="select id from employees where emailid=:emailid and status=:status";
        $stmt=$this->conn->prepare($query);
        $stmt->execute([':emailid'=>$username,':status'=>'active']);
        $employee=$stmt->fetch(PDO::FETCH_ASSOC);
        $employee_id=$employee['id'];
        return $employee_id;
    }
}
?>