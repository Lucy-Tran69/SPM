<?php
    include_once "database/db.inc";
    
    /**
     * This is the function used to get max sort order to display in add/edit role page.
     */

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }
   
    $getLastSortOrder = "select sort_order from role order by sort_order desc limit 1";
    $stmLastOrder = $conn->prepare($getLastSortOrder);
    $stmLastOrder->execute();
    $stmLastOrder->store_result();
    $rowSortOrder = fetchAssocStatement($stmLastOrder);
    $sortOrder = $rowSortOrder['sort_order'] + 1;

    $stmLastOrder->close();
    $conn->close();
?>