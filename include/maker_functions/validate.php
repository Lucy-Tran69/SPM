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
    $id = $_POST["inputId"];
    $upday = date('Y-m-d H:i:s');
    $upuser = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

if($status=="OK")
{
    $stmt = $conn->prepare("update maker set invalid=0,upday=?,upuser=? where no=?");
    $stmt->bind_param('sii',$upday,$upuser,$id);
    
    $result = execute($stmt,$conn);
    if($result)
    {
        $msg="有効にしました。";
    }
    else
    {
        $msg = "Database Error Occured";
        $status="";
    }
}

echo json_encode(array(
    "status"=>$status,
    "message"=>$msg
));

?>