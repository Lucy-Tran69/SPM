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

$stmt = $conn->prepare("select  commodity.no,
                                commodity.invalid as invalid,
                                commodity.cd as code,
                                commodity.name as name,
                                maker.name as maker,
                                commodity.memo as memo 
                                from commodity 
                                left join maker on commodity.maker=maker.no");
$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}

$makerStmt = $conn->prepare("select no,name from maker where invalid=0");
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->get_result();
    $makerResultSet = $makerResult;
}

?>?