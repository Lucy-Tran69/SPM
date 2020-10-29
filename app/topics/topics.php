<?php
    include_once "common/session.php";
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
    $searchQuery = "";
     $title = "";
     $status = "";

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = isset($_POST["title"]) ? check_keyword_search($_POST["title"]) : '';
        $status = isset($_POST["status"]) ? $_POST["status"] : '';
        $topicID = isset($_POST["topicID"]) ? $_POST["topicID"] : '';
        $topicTitle = isset($_POST["topicTitle"]) ? $_POST["topicTitle"] : '';

        $currentDate = date("Y-m-d H:i:s");
        
        if(!empty($title) && empty($status)){
            $searchQuery = " and title like '%?%'";
        }
        if(empty($title) && !empty($status)){
            $searchQuery = " and opday <= '?' and (clday >= '?' or clday is null) ";
        }
        if(!empty($title) && !empty($status)) {
            $searchQuery = " and title like '%?%' and opday <= '?' and (clday >= '?' or clday is null) ";
        }
        if(!empty($topicID)) {
            $empQuery = "UPDATE topics SET invalid = 1 WHERE no = $topicID";
            mysqli_query($conn, $empQuery);
        }
    }


    $invalid = 0;
    ## Total number of records without filtering
    $sel = "select count(*) as allcount from topics where invalid = ?";
    $stmSel = $conn->prepare($sel);
    $stmSel->bind_param('i', $invalid);
    $stmSel->execute();
    $stmSel->store_result();
    $records = fetchAssocStatement($stmSel);
    $totalRecords = $records['allcount'];

     ## Total number of records with filtering
    // $sel = "select count(*) as allcount from topics WHERE invalid = ? " .$searchQuery;
    // $stmSel = $conn->prepare($sel);
    if($title != "" && $status == ""){
         $sel = "select count(*) as allcount from topics WHERE invalid = ? " .$searchQuery;
         $stmSel = $conn->prepare($sel);
      $stmSel->bind_param('is', $invalid, $title);
    }
    if(empty($title) && !empty($status)){
        $stmSel->bind_param('iss', $invalid, $status, $status);
    }
    if(!empty($title) && !empty($status)) {
        $stmSel->bind_param('isss', $invalid, $title, $status, $status);
    }

    if ($title == "" && $status == "") {
         $sel = "select count(*) as allcount from topics WHERE invalid = ? " .$searchQuery;
    $stmSel = $conn->prepare($sel);
        $stmSel->bind_param('i', $invalid);
    }
    
    $stmSel->execute();
    $stmSel->store_result();
    $records = fetchAssocStatement($stmSel);
    $totalRecordwithFilter = $records['allcount'];
    $stmSel->close();

    ## Fetch records
    // $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? ".$searchQuery." order by open_day desc limit ".$row.",".$rowperpage;
    // $stmt = $conn->prepare($empQuery);
    if($title != "" && $status == ""){
        // $searchQuery = " and title like '%?%'";
         $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? and title like '%?%' order by open_day desc limit ".$row.",".$rowperpage;
         var_dump($empQuery); die();
         $stmt = $conn->prepare($empQuery);
      $stmt->bind_param('is', $invalid, $title);
    

    }
    if(empty($title) && !empty($status)){
        $stmt->bind_param('iss', $invalid, $status, $status);
    }
    if(!empty($title) && !empty($status)) {
        $stmt->bind_param('iss', $invalid, $title, $status, $status);
    }

    if ($title == "" && $status == "") {
          $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? ".$searchQuery." order by open_day desc limit ".$row.",".$rowperpage;
    $stmt = $conn->prepare($empQuery);
        $stmt->bind_param('i', $invalid);
    }

    $stmt->execute();
    $stmt->store_result();
     $data = array();
     if(!$stmt->error) {
        while ($row = fetchAssocStatement($stmt))
        {
            array_push($data, array( "no" => $row['no'],
            "insert_day" => $row['insert_day'],
            "open_day" => $row['open_day'],
            "close_day" => $row['close_day'],
            "title" => $row['title']));
        }
    }

    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);
    $conn->close();

 function check_keyword_search($data) {
    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');
    $data = mb_ereg_replace("^[\n\r\s\t　]+", '', $data);
    $data = mb_ereg_replace("[\n\r\s\t　]+$", '', $data);
    $data = trim($data);
    $data = str_replace('_', '\\_', $data);
    $data = str_replace('%', '\\%', $data);
    $data = str_replace("'", "\\'", $data);
    $data = str_replace('"', '\\"', $data);
    return $data;
}
?>

