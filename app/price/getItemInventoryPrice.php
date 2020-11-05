<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

$cusAcc = $_SESSION["loginUserId"];

$customer = "SELECT customer FROM users WHERE users.no = ?";
$stmtcustomer = $conn->prepare($customer);
$stmtcustomer->bind_param('i', $cusAcc);
$stmtcustomer->execute();
$stmtcustomer->store_result();
$row = fetchAssocStatement($stmtcustomer);
$cusNo = $row['customer'];

## get displaylimit customer
$customerDisplaylimit = "SELECT customer.displaylimit FROM customer INNER JOIN (SELECT customer FROM users WHERE users.no = ? ) A WHERE A.customer = customer.no ";

$customerDisplaylimitStmt = $conn->prepare($customerDisplaylimit);
$customerDisplaylimitStmt->bind_param('i', $cusAcc);
$customerDisplaylimitStmt->execute();
$customerDisplaylimitStmt->store_result();
$row = fetchAssocStatement($customerDisplaylimitStmt);
$customerDisplaylimit = $row['displaylimit'];

if($customerDisplaylimit == 1){
    $makerStmt = $conn-> prepare("SELECT m.no, m.name FROM maker m
                                INNER JOIN
                                (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
                                FROM commodity C
                                LEFT JOIN print_type PT on C.print_type = PT.no    
                                INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
                                LEFT JOIN
                                    (select SP.customer, SP.commodity, SP.seq, SP.price 
                                    FROM selling_price SP
                                    INNER JOIN 
                                        (SELECT commodity, MAX(seq) as seq_last FROM selling_price WHERE approvalday IS NOT NULL GROUP BY commodity) A on SP.seq = A.seq_last and SP.commodity = A.commodity) B on B.commodity = C.no AND B.customer = ".$cusNo."
                                LEFT JOIN
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
                                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no)T ON T.maker = m.name GROUP BY m.name") ;
}else{
    $makerStmt = $conn-> prepare("SELECT m.no, m.name FROM maker m
                                INNER JOIN
                                (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
                                FROM commodity C
                                LEFT JOIN print_type PT on C.print_type = PT.no    
                                INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
                                INNER JOIN
                                    (select SP.customer, SP.commodity, SP.seq, SP.price 
                                    FROM selling_price SP
                                    INNER JOIN 
                                        (SELECT commodity, MAX(seq) as seq_last FROM selling_price WHERE approvalday IS NOT NULL GROUP BY commodity) A on SP.seq = A.seq_last and SP.commodity = A.commodity) B on B.commodity = C.no AND B.customer = ".$cusNo."
                                LEFT JOIN
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
                                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no)T ON T.maker = m.name GROUP BY m.name") ;
}

$makerStmt->execute();
$makerStmt->store_result();
$makerdb = array();
if(!$makerStmt->error) {
    while ($row = fetchAssocStatement($makerStmt))
    {
        array_push($makerdb, array('no' => $row['no'], 'name' => $row['name']));
    }
}

$conn->close();
?>