<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $address = $_POST["updateAddress"];
    $name = $_POST["updateName"];
    $tel = $_POST["updateTel"];
    $title = $_POST["updateTitle"];
    $zip = $_POST["updateZip"];
    $fax = $_POST["updateFax"];
    $id = $_POST["updateId"];
    $upday = date('Y-m-d H:i:s');
    $upuser = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$stmt;
    $stmt = $conn->prepare("update office set name=?,
                                            address=?,
                                            tel=?,
                                            fax=?,
                                            title=?,
                                            zip=?,
                                            upday=?,
                                            upuser=?
                                            where no=?");
    $stmt->bind_param('sssssssii',$name,$address,$tel,$fax,$title,$zip,$upday,$upuser,$id);


$result = execute($stmt,$conn);
if($result)
{
    header("Location: ../../app/office/");  
}
else
{
    echo "SQL Error";
}

?>