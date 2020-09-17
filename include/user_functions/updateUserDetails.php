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
    $role = $_POST["inputRole"];
    $email = $_POST["inputEmail"];
    $expire = $_POST["inputExpire"];
    $customer = $_POST["inputCustomer"];
    $office = $_POST["inputOffice"];
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

$stmt = $conn->prepare("update users set name=?,account=?,role=?,email=?,effective_en_day=?,customer=?,office=?,invalid=?,upday=?,upuser=? where no=?");
$stmt->bind_param('ssissiiisii',$name,$account,$role,$email,$expire,$customer,$office,$invalid,$upday,$upuser,$id);

$result = execute($stmt,$conn);
if($result)
{
    header("Location: ../../app/login/users.html");  
}
else
{
    echo "SQL Error";
}

?>