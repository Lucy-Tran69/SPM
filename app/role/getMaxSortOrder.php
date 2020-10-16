<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $getLastSortOrder = "select sort_order from role order by no desc limit 1";
    $lastSortOrder = mysqli_query($conn,$getLastSortOrder);
    $rowSortOrder = mysqli_fetch_assoc($lastSortOrder);
    $sortOrder = $rowSortOrder['sort_order'] + 1;

    $conn->close();
?>