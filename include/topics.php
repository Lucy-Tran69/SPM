<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    ## Read value
    $draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
    $row = isset($_POST['start']) ? $_POST['start'] : 0;
    $rowperpage = isset($_POST['length']) ? $_POST['length'] : 10; // Rows display per page

    ## Search 
    $searchQuery = "";;

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = isset($_POST["title"]) ? $_POST["title"] : '';
        $status = isset($_POST["status"]) ? $_POST["status"] : '';
        $topicID = isset($_POST["topicID"]) ? $_POST["topicID"] : '';
        
        if(!empty($title) && empty($status)){
            $searchQuery = "and title like '%$title%'";
        }
        if(empty($title) && !empty($status)){
            $searchQuery = "and opday <= '$status'";
        }
        if(!empty($title) && !empty($status)) {
            $searchQuery = "and title like '%$title%' and opday <= '$status'";
        }
        if(!empty($topicID)) {
            $empQuery = "UPDATE topics SET invalid = 1 WHERE no = $topicID";
            mysqli_query($conn, $empQuery);
        }
    }

    ## Total number of records without filtering
    $sel = mysqli_query($conn, "select count(*) as allcount from topics where invalid = 0");
    $records = mysqli_fetch_assoc($sel);
    $totalRecords = $records['allcount'];

    ## Total number of records with filtering
    $sel = mysqli_query($conn, "select count(*) as allcount from topics WHERE invalid = 0 " .$searchQuery);
    $records = mysqli_fetch_assoc($sel);
    $totalRecordwithFilter = $records['allcount'];

    ## Fetch records
    $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = 0 ".$searchQuery." order by open_day desc limit ".$row.",".$rowperpage;
    
    // echo $empQuery;
    $empRecords = mysqli_query($conn, $empQuery);
    $data = array();

    while ($row = mysqli_fetch_assoc($empRecords)) {
        $data[] = array(
                "no" => $row['no'],
                "insert_day" => $row['insert_day'],
                "open_day" => $row['open_day'],
                "close_day" => $row['close_day'],
                "title" => $row['title']
            );
    }

    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    // header('Content-type: application/json');
    echo json_encode($response);
    // die();
    $conn->close();
?>