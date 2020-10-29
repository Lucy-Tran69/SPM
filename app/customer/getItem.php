<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

$saleStmt = $conn->prepare("select no,name from users where invalid=0 and role = 3 order by name ");
$saleResult = execute($saleStmt,$conn);
if($saleResult==TRUE)
{
    // $userResult=$userStmt->get_result();
    $saleResult=$saleStmt->store_result();
    $saleUserResultSet = $saleResult;
}

$approveUserStmt = $conn->prepare("select no,name from users where invalid=0 and role = 4 or role = 1 order by name");
$approveUserResult = execute($approveUserStmt,$conn);
if($approveUserResult==TRUE)
{
    // $approveUserResult=$approveUserStmt->get_result();
    $approveUserResult=$approveUserStmt->store_result();
    $approveUserResultSet = $approveUserResult;
}

$conn->close();
?>