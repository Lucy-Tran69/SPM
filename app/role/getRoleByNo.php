<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    if (isset($_SESSION["loginAccount"])) {
        if(isset($_GET["no"]) && !empty(trim($_GET["no"])) && is_numeric($_GET['no'])){
           $no = $_GET['no'];

           //get detail role
           $sqlRole = "select no, name, outside, sort_order, invalid from role where no=?";

           $stmtRole = $conn->prepare($sqlRole);

           $stmtRole->bind_param('i', $no);

           $stmtRole->execute();

           if(!$stmtRole->error) {
            $rs = $stmtRole->get_result();
            $role = $rs->fetch_array(MYSQLI_ASSOC);
            }

            //get menu by role
            $sqlRoleMenu = "select role, menu from role_menu where role='$no'";
            $getRoleMenu = mysqli_query($conn, $sqlRoleMenu);
            $menuLst = array();
            if(mysqli_num_rows($getRoleMenu) > 0) {
                while ($row = mysqli_fetch_array($getRoleMenu))
                {
                    array_push($menuLst, array('role' => $row['role'], 
                        'menu' => $row['menu']));
                }
            }
            $stmtRole->close();
            $conn->close();
        }
    }
   
?>