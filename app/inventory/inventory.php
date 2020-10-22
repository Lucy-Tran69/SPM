<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

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
            $searchMaker = " AND M.name = '$maker' ";
        }

        if(!empty($freeword)){
            $freeword = mysqli_real_escape_string($conn, $freeword);
            $searchFreeWord = "AND C.name LIKE '%$freeword%' ";
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
                    $searchQuery = $searchQuery;
                }
                $searchQuery = $searchQuery.$searchFreeWord ;
            }       
        } 
    }

    $sel = mysqli_query($conn, "select count(*) as allcount from (select PT.name AS print_type, M.name AS maker, C.name, inv.commodity, inv.display 
                                                                    FROM commodity C 
                                                                    LEFT JOIN print_type PT ON C.print_type = PT.no 
                                                                    INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
                                                                    INNER JOIN (select I.commodity, im.display 
                                                                                FROM inventory I
                                                                                INNER JOIN inventory_mark im ON im.no = I.inventory_mark AND (im.no = 2 OR im.no = 1 OR im.no = 3) AND im.hidden != 1) inv ON inv.commodity = C.no 
                                                                    WHERE C.invalid = 0 ".$searchQuery.") A");


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

    ## Fetch records
    $empQuery = "select PT.name AS print_type, M.name AS maker, C.name, inv.commodity, inv.display 
                    FROM commodity C 
                    LEFT JOIN print_type PT ON C.print_type = PT.no 
                    INNER JOIN maker M ON M.no = C.maker AND M.invalid != 1
                    INNER JOIN (select I.commodity, im.display 
                                FROM inventory I
                                INNER JOIN inventory_mark im ON im.no = I.inventory_mark AND (im.no = 2 OR im.no = 1 OR im.no = 3)) inv ON inv.commodity = C.no 
                    WHERE C.invalid = 0 ".$searchQuery." ORDER BY maker, print_type, C.cd ASC LIMIT ".$rowa.",".$rowperpage;

    $empRecords = mysqli_query($conn, $empQuery);
    
    $data = array();
    
    while ($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array(
                "print_type" => $row['print_type'],
                "maker" => $row['maker'],
                "name" => $row['name'],
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