<?php
include_once "database/db.inc";

$conn  = getConnection();

$userStmt = $conn->prepare("select no,name from users where invalid=0 and role = 3 order by name ");
$userResult = execute($userStmt,$conn);
if($userResult==TRUE)
{
    $userResult=$userStmt->get_result();
    $saleUserResultSet = $userResult;
}

$userStmt = $conn->prepare("select no,name from users where invalid=0 and role = 4 or role = 1 order by name");
$userResult = execute($userStmt,$conn);
if($userResult==TRUE)
{
    $userResult=$userStmt->get_result();
    $approveUserResultSet = $userResult;
}

$conn->close();
?>