<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $cusAcc = $_SESSION["loginUserId"];
    // print_r($cusAcc);die();

    $cusStmt = "SELECT customer FROM users WHERE users.no = ".$cusAcc;
    $cusResult = mysqli_query($conn,$cusStmt);
    $row = mysqli_fetch_assoc($cusResult);
    $cusNo = $row['customer'];
    // print_r($row);die();

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
        // print_r($searchMaker);
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
            $searchQuery = "WHERE ".$searchQuery;        
        } 
        // print_r($searchQuery);
    }
    // die();

    $sel = mysqli_query($conn, "select count(*) as allcount from (select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
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
                                                                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1) D	on D.commodity = C.no
                                                                ".$searchQuery.") A");

    $records = mysqli_fetch_assoc($sel);
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
    $newestDateResult = mysqli_query($conn,$newestDateQuery);
    $row = mysqli_fetch_assoc($newestDateResult);
    $newestDate = $row['newestDate'];
    // print_r($newestDate);

    ## Fetch records
    $empQuery = "select C.no, C.cd ,PT.name AS print_type,  M.name AS maker, C.name, C.price AS price1, FORMAT(B.price, 0 ) As price2, FORMAT(E.price, 0 ) As price3, C.num, C.printer_support, D.display 
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
                    INNER JOIN inventory_mark im ON im.no = inventory.inventory_mark AND im.hidden != 1) D	on D.commodity = C.no
                ".$searchQuery." ORDER BY C.cd ASC LIMIT ".$rowa.",".$rowperpage;
    // print_r($empQuery);die();

    $empRecords = mysqli_query($conn, $empQuery);
    // print_r($empRecords); //die();
    
    $data = array();
    
    // print_r($empQuery); die();
    while ($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array(
                "print_type" => $row['print_type'],
                "maker" => $row['maker'],
                "name" => $row['name'],
                "price1" => is_numeric($row['price1']) ? "¥".number_format($row['price1'],0) : $row['price1'],
                "price2" => "¥".$row['price2'],
                "price3" => "¥".$row['price3'],
                "num" => $row['num'],
                "printer_support" => $row['printer_support'],
                "display" => $row['display']
            );
    }
    // print_r($data); die();
    
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