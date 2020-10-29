<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    if (isset($_SESSION["loginAccount"])) {
        if(isset($_GET["no"]) && !empty(trim($_GET["no"])) && is_numeric($_GET['no'])){
           $id = $_GET['no'];

           //get detail role
           $sqlRole = "select no, name, outside, sort_order, invalid from role where no=?";
           $stmtRole = $conn->prepare($sqlRole);
           $stmtRole->bind_param('i', $id);
           $stmtRole->execute();

          if(!$stmtRole->error) {
             $stmtRole->store_result();
             $role = fetchAssocStatement($stmtRole);
          }
          $stmtRole->close();
        }
        $conn->close();
    }
   
?>