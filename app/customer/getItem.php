<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

$saleStmt = $conn->prepare("select no,name from users where invalid=0 and role = 3 order by name ");
$saleStmt->execute();
$saleStmt->store_result();

$sale = array();
if(!$saleStmt->error) {
    while ($row = fetchAssocStatement($saleStmt))
    {
        array_push($sale, array('name' => $row['name'], 'no' => $row['no']));
    }
}

//$saleResult = execute($saleStmt,$conn);
// if($saleResult==TRUE)
// {
//     $saleResult=$saleStmt->store_result();
//     $saleUserResultSet = $saleResult;
// }

$approveUserStmt = $conn->prepare("select no,name from users where invalid=0 and (role = 4 or role = 1) order by name");
$approveUserStmt->execute();
$approveUserStmt->store_result();
$approve = array();
if(!$approveUserStmt->error) {
    while ($row = fetchAssocStatement($approveUserStmt))
    {
        array_push($approve, array('name' => $row['name'], 'no' => $row['no']));
    }
}

// $approveUserResult = execute($approveUserStmt,$conn);
// if($approveUserResult==TRUE)
// {
//     $approveUserResult=$approveUserStmt->store_result();
//     $approveUserResultSet = $approveUserResult;
// }

// $stmtRoleMenu = $conn->prepare($sqlRoleMenu);
// $stmtRoleMenu->bind_param('i', $id);
// $stmtRoleMenu->execute();
// $stmtRoleMenu->store_result();

$conn->close();
?>