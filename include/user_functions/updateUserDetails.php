<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $account = $_POST["inputAccount"];
    $name = $_POST["inputName"];
    $password = !empty($_POST["inputPass"])?password_hash($_POST["inputPass"],PASSWORD_DEFAULT):null;
    $role = $_POST["inputRole"];
    $email = $_POST["inputEmail"];
    $start = $_POST["inputStart"];
    $expire = $_POST["inputExpire"]==""?null:$_POST["inputExpire"];
    $customer = $_POST["inputCustomer"]==""?null:$_POST["inputCustomer"];
    $office = $_POST["inputOffice"]==""?null:$_POST["inputOffice"];
    $invalid = $_POST["inputInvalid"]==null? 0:1;
    $id = $_POST["inputId"];
    $upday = date('Y-m-d H:i:s');
    $upuser = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$stmt;
if(empty($password))
{
    $stmt = $conn->prepare("update users set name=?,account=?,role=?,email=?,effective_st_day=?,effective_en_day=?,customer=?,office=?,invalid=?,upday=?,upuser=? where no=?");
    $stmt->bind_param('ssisssiiisii',$name,$account,$role,$email,$start,$expire,$customer,$office,$invalid,$upday,$upuser,$id);
}
else
{
    $stmt = $conn->prepare("update users set name=?,account=?,role=?,email=?,effective_st_day=?,effective_en_day=?,customer=?,office=?,invalid=?,upday=?,upuser=?,password=? where no=?");
    $stmt->bind_param('ssisssiiisisi',$name,$account,$role,$email,$start,$expire,$customer,$office,$invalid,$upday,$upuser,$password,$id);
}


$result = execute($stmt,$conn);
if($result)
{
    header("Location: ../../app/users/");  
}
else
{
    echo "SQL Error";
}

?>