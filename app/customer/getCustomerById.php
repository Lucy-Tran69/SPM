<?php
    include_once "database/db.inc";

    
    $conn  = getConnection();

    if (isset($_SESSION["loginAccount"])) {
        if(isset($_GET["id"]) && !empty(trim($_GET["id"])) && is_numeric($_GET['id'])){
    
            if($conn->connect_error) {
                die("Failed to connect to database. ".$conn->connect_error);
            }        
    
            $id = $_GET['id'];
            $sql = "SELECT no, cd, name, tel, zip, address, charge, sales, supervisor, invalid, displaylimit FROM customer WHERE no= ?";
            $stm = $conn->prepare($sql);
            $stm->bind_param('i', $id);
            $stm->execute();
            if (!$stm->error) {
                $stm->store_result();
                $row = fetchAssocStatement($stm);
            }
            $stm->close();
            
            // if($stm->num_rows>0)
            // {
            //     $row = fetchAssocStatement($stm);
            // }

        //     $sqlRole = "select no, name, outside, sort_order, invalid from role where no=?";
        //     $stmtRole = $conn->prepare($sqlRole);
        //     $stmtRole->bind_param('i', $id);
        //     $stmtRole->execute();
 
        //    if(!$stmtRole->error) {
        //       $stmtRole->store_result();
        //       $role = fetchAssocStatement($stmtRole);
        //    }

        }
    }
    $conn->close();
?>