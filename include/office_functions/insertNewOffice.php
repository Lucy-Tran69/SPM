<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $address = $_POST["inputAddress"];
    $name = $_POST["inputName"];
    $tel = $_POST["inputTel"];
    $title = $_POST["inputTitle"];
    $zip = $_POST["inputZip"];
    $fax= $_POST["inputFax"];
    $inday = date('Y-m-d H:i:s');
    $inuser = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$stmt = $conn->prepare("insert into office(name,tel,fax,zip,address,title,inday,inuser) values(?,?,?,?,?,?,?,?)");
$stmt->bind_param('sssssssi',$name,$tel,$fax,$zip,$address,$title,$inday,$inuser);


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