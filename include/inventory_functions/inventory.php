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

$stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        inventory.inventory_mark as mark,
                        inventory_mark.display as display,
                        inventory_mark.hidden as hidden,
                        commodity.no as id 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL order by commodity.no ASC");
$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}

$markStmt = $conn->prepare("select no,display from inventory_mark");
$markResult = execute($markStmt,$conn);
if($markResult==TRUE)
{
    $markResult=$markStmt->get_result();
    $markResultSet = $markResult;
}

$newStmt = $conn->prepare("select no,display from inventory_mark where no=5");
$newResult = execute($newStmt,$conn);
if($newResult==TRUE)
{
    $newResult=$newStmt->get_result();
    $newResultSet = $newResult;
}

while($newrow = $newResultSet->fetch_assoc())
{
    $markNew = $newrow["display"];
}

?>?