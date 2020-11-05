<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

$makerStmt = $conn-> prepare("SELECT m.no, m.name FROM maker m
                                INNER JOIN(select PT.name AS print_type, M.name AS maker, C.name, inv.commodity, inv.display 
                                        FROM commodity C 
                                        LEFT JOIN print_type PT ON C.print_type = PT.no 
                                        LEFT JOIN maker M ON C.maker = M.no 
                                        INNER JOIN (select I.commodity, im.display 
                                                        FROM inventory I
                                                        INNER JOIN inventory_mark im ON im.no = I.inventory_mark AND (im.no = 2 OR im.no = 1 OR im.no = 3) AND im.hidden != 1) inv ON inv.commodity = C.no 
                                        WHERE C.invalid = 0) A WHERE m.invalid != 1 GROUP BY m.name") ;
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