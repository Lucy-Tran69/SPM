<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $no = $_POST["no"];
    $val = $_POST["val"];
    $upday = date('Y-m-d H:i:s');
    $upuser = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}
$stmt = $conn->prepare("insert into inventory(commodity,inventory_mark,inday,inuser) values(?,?,?,?) 
                        ON DUPLICATE KEY update inventory_mark=?,upday=?,upuser=?");
$stmt->bind_param('iisiisi',$no,$val,$upday,$upuser,$val,$upday,$upuser);

$result = execute($stmt,$conn);
if($result)
{
   // header("Location: ../../app/login/inventory.html");  
}
else
{
    echo "SQL Error";
}

?>