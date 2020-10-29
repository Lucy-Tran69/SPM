<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $cusAcc = $_SESSION["loginUserId"];

    ## get no customer
    $cusStmt = "SELECT customer FROM users WHERE users.no = ".$cusAcc;
    $cusResult = mysqli_query($conn,$cusStmt);
    $row = mysqli_fetch_assoc($cusResult);
    $cusNo = $row['customer'];

    ## get displaylimit customer
    $customerDisplaylimit = $_SESSION["loginUserId"];

    $customerDisplaylimitStmt = "SELECT customer.displaylimit FROM customer INNER JOIN (SELECT customer FROM users WHERE users.no = ".$customerDisplaylimit.") A WHERE A.customer = customer.no ";
    $customerDisplaylimitResult = mysqli_query($conn,$customerDisplaylimitStmt);
    $row = mysqli_fetch_assoc($customerDisplaylimitResult);
    $customerDisplaylimit = $row['displaylimit'];

    ## Read value
    $draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
    $rowa = isset($_POST['start']) ? $_POST['start'] : 0;
    $rowperpage = isset($_POST['length']) ? $_POST['length'] : 10; // Rows display per page

    ## Search 
    $searchQuery = "";
    $searchMaker = "";
    $searchFreeWord = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $maker = isset($_POST["maker"]) ? $_POST["maker"] : '';

        $freeword = isset($_POST["freeword"]) ? $_POST["freeword"] : '';
        $freeword = removeWhitespaceAtBeginAndEndOfString($freeword);

        $searchQuery = "";
        if(!empty($maker)){
            $searchMaker = " M.name = '$maker' ";
        }

        if(!empty($freeword)){
            $freeword = mysqli_real_escape_string($conn, $freeword);
            $searchFreeWord = " (C.name LIKE '%$freeword%' OR C.printer_support LIKE '%$freeword%') ";
        }
        if (!empty($searchMaker) || !empty($searchFreeWord)) {
            if (!empty($searchMaker)){
                if (!empty($searchQuery)){
                    $searchQuery = $searchQuery." AND ";
                }
                $searchQuery = $searchQuery.$searchMaker ;
            }
            if (!empty($searchFreeWord)){
                if (!empty($searchQuery)){
                    $searchQuery = $searchQuery." AND ";
                }
                $searchQuery = $searchQuery.$searchFreeWord ;
            }
            $searchQuery = "AND ".$searchQuery;        
        } 
    }

    // if ($customerDisplaylimit == 1){
    //     $sel = mysqli_query($conn, "select count(*) as allcount from (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
    //                                                             FROM commodity C
    //                                                             LEFT JOIN print_type PT on C.print_type = PT.no    
    //                                                             INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
    //                                                             LEFT JOIN
    //                                                                 (select SP.customer, SP.commodity, SP.seq, SP.price 
    //                                                                 FROM selling_price SP
    //                                                                 INNER JOIN 
    //                                                                     (SELECT commodity, MAX(seq) as seq_last FROM selling_price WHERE approvalday IS NOT NULL GROUP BY commodity) A on SP.seq = A.seq_last and SP.commodity = A.commodity) B on B.commodity = C.no AND B.customer = ".$cusNo."
    //                                                             LEFT JOIN
    //                                                                 (select SP.customer, SP.commodity, SP.seq, SP.price 
    //                                                                 FROM selling_price SP
    //                                                                 INNER JOIN
    //                                                                     (SELECT commodity, MAX(seq) as seq_last 
    //                                                                     FROM selling_price SP3 
    //                                                                     WHERE approvalday IS NOT NULL AND seq NOT IN (SELECT MAX(seq) 
    //                                                                                                                                         FROM selling_price 
    //                                                                                                                                         WHERE approvalday IS NOT NULL AND commodity = SP3.commodity)
    //                                                                     GROUP BY commodity) D on SP.seq = D.seq_last and SP.commodity = D.commodity) E on E.commodity = C.no AND E.customer = ".$cusNo."
    //                                                             INNER JOIN
    //                                                                 (SELECT commodity,inventory.inventory_mark, im.display 
    //                                                                 FROM inventory 
    //                                                                 INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no  WHERE C.invalid != 1 
    //                                                             ".$searchQuery.") A");
    // }else{
    //     $sel = mysqli_query($conn, "select count(*) as allcount from (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
    //                                                             FROM commodity C
    //                                                             LEFT JOIN print_type PT on C.print_type = PT.no    
    //                                                             INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
    //                                                             INNER JOIN
    //                                                                 (select SP.customer, SP.commodity, SP.seq, SP.price 
    //                                                                 FROM selling_price SP
    //                                                                 INNER JOIN 
    //                                                                     (SELECT commodity, MAX(seq) as seq_last FROM selling_price WHERE approvalday IS NOT NULL GROUP BY commodity) A on SP.seq = A.seq_last and SP.commodity = A.commodity) B on B.commodity = C.no AND B.customer = ".$cusNo."
    //                                                             INNER JOIN
    //                                                                 (select SP.customer, SP.commodity, SP.seq, SP.price 
    //                                                                 FROM selling_price SP
    //                                                                 INNER JOIN
    //                                                                     (SELECT commodity, MAX(seq) as seq_last 
    //                                                                     FROM selling_price SP3 
    //                                                                     WHERE approvalday IS NOT NULL AND seq NOT IN (SELECT MAX(seq) 
    //                                                                                                                                         FROM selling_price 
    //                                                                                                                                         WHERE approvalday IS NOT NULL AND commodity = SP3.commodity)
    //                                                                     GROUP BY commodity) D on SP.seq = D.seq_last and SP.commodity = D.commodity) E on E.commodity = C.no AND E.customer = ".$cusNo."
    //                                                             INNER JOIN
    //                                                                 (SELECT commodity,inventory.inventory_mark, im.display 
    //                                                                 FROM inventory 
    //                                                                 INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no
    //                                                             ".$searchQuery.") A");
    // }
    
    // $records = mysqli_fetch_assoc($sel);
    // $totalRecordwithFilter = $records['allcount'];

    if ($customerDisplaylimit == 1){
        $sel = "select count(*) as allcount from (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
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
                                                                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no  WHERE C.invalid != 1 
                                                                ".$searchQuery.") A";
    }else{
        $sel = "select count(*) as allcount from (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
                                                                FROM commodity C
                                                                LEFT JOIN print_type PT on C.print_type = PT.no    
                                                                INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
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
                                                                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no
                                                                ".$searchQuery.") A";
    }
    $stmSel = $conn->prepare($sel);
    $stmSel->execute();
    $stmSel->store_result();
    $records = fetchAssocStatement($stmSel);
    $totalRecordwithFilter = $records['allcount'];

    ## Get newestDate
    $newestDateQuery = "select DATE_FORMAT(MAX(newestDate),'%Y/%m/%d') AS newestDate
                            FROM(SELECT commodity, im.display ,
                            CASE WHEN i.inday > i.upday THEN i.inday
                            WHEN i.upday > i.inday THEN i.upday
                            ELSE i.inday
                            END AS newestDate
                            FROM inventory i
                            INNER JOIN inventory_mark im ON im.no = i.inventory_mark AND im.hidden != 1) a";
    $stmNewestDate = $conn->prepare($newestDateQuery);
    $stmNewestDate->execute();
    $stmNewestDate->store_result();
    $row = fetchAssocStatement($stmNewestDate);
    $newestDate = $row['newestDate'];

    ## Fetch records
    if($customerDisplaylimit == 1){
        $empQuery = "select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
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
                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no  WHERE C.invalid != 1
                ".$searchQuery." ORDER BY maker, print_type, C.cd ASC LIMIT ".$rowa.",".$rowperpage;
    }else{
        $empQuery = "select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
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
                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1 AND (im.no = 2 OR im.no = 1 OR im.no = 3)) D	on D.commodity = C.no  WHERE C.invalid != 1
                ".$searchQuery." ORDER BY maker, print_type, C.cd ASC LIMIT ".$rowa.",".$rowperpage;   
    }

    // echo($empQuery);die();
    $empRecords = mysqli_query($conn, $empQuery);
    
    $data = array();
    
    while ($row = mysqli_fetch_array($empRecords)) {
        $data[] = array(
                "print_type" => $row['print_type'],
                "maker" => $row['maker'],
                "name" => $row['name'],
                "price1" => is_numeric($row['price1']) ? "¥".number_format($row['price1'],0) : $row['price1'],
                "price2" => ($row['price2'] == '') ? '-' :'¥'.$row['price2'],
                "price3" => ($row['price3'] == '') ? '-' :'¥'.$row['price3'],
                "num" => $row['num'],
                "printer_support" => $row['printer_support'],
                "display" => $row['display']
            );
    }
    
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data,
        "newestDate" => $newestDate
    );

    echo json_encode($response);
    $conn->close();

    function removeWhitespaceAtBeginAndEndOfString($data) {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        $data = mb_ereg_replace("^[\n\r\s\t　]+", '', $data);
        $data = mb_ereg_replace("[\n\r\s\t　]+$", '', $data);
        $data = trim($data);
        $data = str_replace('_', '\\_', $data);
        $data = str_replace('%', '\\%', $data);
        return $data;
    }
?>