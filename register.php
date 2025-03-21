<?php
require 'Database.php';
require 'User.php';
$db=(new Database())->getConnection();
$user=new User($db);
if($_SERVER['REQUEST_METHOD']==='POST'){
$username=$_POST['username'];
$password=$_POST['password'];
$role=$_POST['role'];
if($user->register($username,$password,$role)){
    echo '<script type="text/javascript">
        alert("User Registered Successfully!");
    </script>';
    header("location:registration_form.php");
}
}
?>