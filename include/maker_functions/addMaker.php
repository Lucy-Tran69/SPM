<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";
$msg = "";
$status = "OK";
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $name = $_POST["inputName"];
    $upday = date('Y-m-d H:i:s');
    $upuser = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

if(empty($name))
{
    $msg = "Name cannot be blank";
    $status="";
}

if($status=="OK")
{
    $stmt = $conn->prepare("insert INTO maker(name,invalid,inday,inuser,upday,upuser) VALUES (?,0,?,?,?,?)");
    $stmt->bind_param('ssisi',$name,$upday,$upuser,$upday,$upuser);
    $result = execute($stmt,$conn);
    if($result)
    {
        $msg = "Maker added";
        // header("Location: ../../app/maker/");  
    }
    else
    {
        $status="";
        $msg = "Database Error Occured";
    }
}

echo json_encode(array(
    "status"=>$status,
    "message"=>$msg
));
?>