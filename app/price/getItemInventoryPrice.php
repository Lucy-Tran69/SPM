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
                                INNER JOIN
                                (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
                                FROM commodity C
                                LEFT JOIN print_type PT on C.print_type = PT.no    
                                LEFT JOIN maker M ON M.no = C.maker
                                INNER JOIN
                                    (select SP.customer, SP.commodity, SP.seq, SP.price 
                                    FROM selling_price SP
                                    INNER JOIN 
                                        (SELECT commodity, MAX(seq) as seq_last FROM selling_price WHERE approvalday IS NOT NULL GROUP BY commodity) A on SP.seq = A.seq_last and SP.commodity = A.commodity) B on B.commodity = C.no AND B.customer = ".$cusNo."
                                INNER JOIN
                                    (select SP.customer, SP.commodity, SP.seq, SP.price 
                                    FROM selling_price SP
                                    INNER JOIN
                                        (SELECT commodity, MAX(seq) as seq_last 
                                        FROM selling_price SP3 
                                        WHERE approvalday IS NOT NULL AND seq NOT IN (SELECT MAX(seq) 
                                                                                                            FROM selling_price 
                                                                                                            WHERE approvalday IS NOT NULL AND commodity = SP3.commodity)
                                        GROUP BY commodity) D on SP.seq = D.seq_last and SP.commodity = D.commodity) E on E.commodity = C.no AND E.customer = ".$cusNo."
                                INNER JOIN
                                    (SELECT commodity,inventory.inventory_mark, im.display 
                                    FROM inventory 
                                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1) D	on D.commodity = C.no)T ON T.maker = m.name GROUP BY m.name") ;
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->get_result();
    $makerResultSet = $makerResult;
    // echo($makerResultSet);
}

$conn->close();
?>