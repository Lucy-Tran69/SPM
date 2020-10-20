<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$stmt;
$stmt = $conn->prepare("select office.no,
                                office.name,
                                office.tel,
                                office.zip,
                                office.address,
                                office.title
                                from office");


$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}


?>?