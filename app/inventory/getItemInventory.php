<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

$cusAcc = $_SESSION["loginUserId"];
    // print_r($cusAcc);die();

$cusStmt = "SELECT customer FROM users WHERE users.no = ".$cusAcc;
$cusResult = mysqli_query($conn,$cusStmt);
$row = mysqli_fetch_assoc($cusResult);
$cusNo = $row['customer'];

$makerStmt = $conn-> prepare("SELECT m.name FROM maker m
                                INNER JOIN(select PT.name AS print_type, M.name AS maker, C.name, inv.commodity, inv.display 
                                        FROM commodity C 
                                        LEFT JOIN print_type PT ON C.print_type = PT.no 
                                        LEFT JOIN maker M ON C.maker = M.no 
                                        INNER JOIN (select I.commodity, im.display 
                                                        FROM inventory I
                                                        INNER JOIN inventory_mark im ON im.no = I.inventory_mark AND (im.no = 2 OR im.no = 1)) inv ON inv.commodity = C.no 
                                        WHERE C.invalid = 0) A GROUP BY m.name") ;
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->get_result();
    $makerResultSet = $makerResult;
    // print_r($makerResultSet);
}

$conn->close();
?>