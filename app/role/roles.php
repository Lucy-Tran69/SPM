<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $sql = "SELECT role.no as no, role.name as name, role.sort_order as sort_order, outside.name as outside_name FROM role INNER JOIN outside ON role.outside = outside.no WHERE invalid = 0 ORDER BY sort_order ASC";

    $query = mysqli_query($conn, $sql) or die ('エラーが発生しました。もう一度お試しください。');

    $data = array();
    while ($row = mysqli_fetch_array($query))
    {
        array_push($data, array('no' => $row['no'], 
                                  'name' => $row['name'],
                                  'sort_order' => $row['sort_order'],
                                  'outside_name' => $row['outside_name']));
    }
    $conn->close();
?>