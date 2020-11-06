<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

if ($conn->connect_error) {
    die("Failed to connect to database. " . $conn->connect_error);
}

## Read value
$invalid = 0;
$draw = isset($_POST['draw']) ? $_POST['draw'] : 1;
$row = isset($_POST['start']) ? $_POST['start'] : 0;
$rowperpage = isset($_POST['length']) ? $_POST['length'] : 10; // Rows display per page

## Search 
$searchQuery = "";
$title = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST["title"]) ? check_keyword_search($_POST["title"]) : '';
    $title1 = mb_convert_kana($title, "KVC");
    $title2 = mb_convert_kana($title, "kVC");
    $title3 = mb_convert_kana($title1, "kVC");
    $status = isset($_POST["status"]) ? $_POST["status"] : '';
    $topicID = isset($_POST["topicID"]) ? $_POST["topicID"] : '';
    $topicTitle = isset($_POST["topicTitle"]) ? $_POST["topicTitle"] : '';

    $currentDate = date('Y-m-d h:i:s', time());
    $invalid = 0;

   
    if (empty($title) && empty($status)) {
        $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? order by open_day desc limit ?,?";
        $stmt = $conn->prepare($empQuery);
        $stmt->bind_param('iii', $invalid, $row, $rowperpage);

        $sqlTotalFilter = "select count(*) as allcount from topics WHERE invalid = ?";
        $stmSel = $conn->prepare($sqlTotalFilter);
        $stmSel->bind_param('i', $invalid);
    }

    if (!empty($title) && empty($status)) {
        $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? and (title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%')) order by open_day desc limit ?,?";
        $stmt = $conn->prepare($empQuery);
        $stmt->bind_param('issssii', $invalid, $title, $title1, $title2, $title3, $row, $rowperpage);

        $sqlTotalFilter = "select count(*) as allcount from topics WHERE invalid = ? and (title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%'))";
        $stmSel = $conn->prepare($sqlTotalFilter);
        $stmSel->bind_param('issss', $invalid, $title, $title1, $title2, $title3);
    }
    if (empty($title) && !empty($status) && $status == 1) {
        $status = $currentDate;
        $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? and opday <= CONCAT(?) and (clday >= CONCAT(?) or clday is null) order by open_day desc limit ?,?";
        $stmt = $conn->prepare($empQuery);
        $stmt->bind_param('issii', $invalid, $status, $status, $row, $rowperpage);

        $sqlTotalFilter = "select count(*) as allcount from topics WHERE invalid = ? and opday <= CONCAT(?) and (clday >= CONCAT(?) or clday is null)";
        $stmSel = $conn->prepare($sqlTotalFilter);
        $stmSel->bind_param('iss', $invalid, $status, $status);
    }
    if (!empty($title) && !empty($status) && $status == 1) {
        $status = $currentDate;
        $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = ? and (title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%')) and opday <= CONCAT(?) and (clday >= CONCAT(?) or clday is null) order by open_day desc limit ?,?";
        $stmt = $conn->prepare($empQuery);
        $stmt->bind_param('issssssii', $invalid, $title, $title1, $title2, $title3, $status, $status, $row, $rowperpage);

        $sqlTotalFilter = "select count(*) as allcount from topics WHERE invalid = ? and (title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%') or title like CONCAT('%',?,'%')) and opday <= CONCAT(?) and (clday >= CONCAT(?) or clday is null)";
        $stmSel = $conn->prepare($sqlTotalFilter);
        $stmSel->bind_param('issssss', $invalid, $title, $title1, $title2, $title3, $status, $status);
    }

    if (!empty($status) && (is_string($status) || (is_numeric($status) && $status != 0 && $status != 1))) {
        die();
        $empQuery = "select no, title, DATE_FORMAT(inday, '%Y/%m/%d') AS insert_day, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, DATE_FORMAT(clday, '%Y/%m/%d') AS close_day from topics WHERE invalid = 2";
        $stmt = $conn->prepare($empQuery);

        $sqlTotalFilter = "select count(*) as allcount from topics WHERE invalid = 2";
        $stmSel = $conn->prepare($sqlTotalFilter);
    }
    
    if (!empty($topicID)) {
        $empQuery = "UPDATE topics SET invalid = 1 WHERE no = $topicID";
        mysqli_query($conn, $empQuery);
    }
}


## Total number of records without filtering
$sel = "select count(*) as allcount from topics where invalid = ?";
$stmSel2 = $conn->prepare($sel);
$stmSel2->bind_param('i', $invalid);
$stmSel2->execute();
$stmSel2->store_result();
$records = fetchAssocStatement($stmSel2);
$totalRecords = $records['allcount'];
$stmSel2->close();

## Total number of records with filtering
$stmSel->execute();
$stmSel->store_result();
$records = fetchAssocStatement($stmSel);
$totalRecordwithFilter = $records['allcount'];
$stmSel->close();

## Fetch records
$stmt->execute();
$stmt->store_result();
$data = array();
if (!$stmt->error) {
    while ($row = fetchAssocStatement($stmt)) {
        array_push($data, array(
            "no" => $row['no'],
            "insert_day" => $row['insert_day'],
            "open_day" => $row['open_day'],
            "close_day" => $row['close_day'],
            "title" => $row['title']
        ));
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

function check_keyword_search($data)
{
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
