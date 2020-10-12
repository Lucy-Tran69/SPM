<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $sql = "SELECT no, name FROM menu ORDER BY sort_order ASC";

    $query = mysqli_query($conn, $sql) or die ('エラーが発生しました。もう一度お試しください。');

    $result = array();
    while ($row = mysqli_fetch_array($query))
    {
        array_push($result, array('no' => $row['no'], 'name' => $row['name']));
    }

    $conn->close();
?>