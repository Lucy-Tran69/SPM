<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";

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

$stmt = $conn->prepare("update maker set invalid=0,upday=?,upuser=? where no=?");
$stmt->bind_param('sii',$upday,$upuser,$id);

$result = execute($stmt,$conn);
if($result)
{
    header("Location: ../../app/login/maker.html");  
}
else
{
    echo "SQL Error";
}

?>