<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $account = $_POST["insertAccount"];
    $name = $_POST["insertName"];
    $role = $_POST["insertRole"];
    $email = $_POST["insertEmail"];
    $expire = $_POST["insertExpire"];
    $customer = $_POST["insertCustomer"]==""?NULL:$_POST["insertCustomer"];
    $office = $_POST["insertOffice"]==""?NULL:$_POST["insertOffice"];
    $invalid = $_POST["insertInvalid"]==null? 0:1;
    $password = password_hash($_POST["insertPass"],PASSWORD_DEFAULT);
    $start = date('Y-m-d');
    $insert_user = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$stmt = $conn->prepare("insert into users(name,account,role,email,effective_en_day,customer,office,invalid,password,effective_st_day,inuser) 
                        values(?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param('ssissiiissi',$name,$account,$role,$email,$expire,$customer,$office,$invalid,$password,$start,$insert_user);


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