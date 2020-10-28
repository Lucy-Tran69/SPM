<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $sql = "SELECT no, name, outside FROM menu WHERE invalid = 0 ORDER BY sort_order ASC";

    $query = mysqli_query($conn, $sql) or die ('エラーが発生しました。もう一度お試しください。');

    $result = array();
    while ($row = mysqli_fetch_array($query))
    {
        array_push($result, array('no' => $row['no'], 
                                  'name' => $row['name'],
                                  'outside' => $row['outside']));
    }

    if (isset($_SESSION["loginAccount"])) {
        if(isset($_GET["no"]) && !empty(trim($_GET["no"])) && is_numeric($_GET['no'])){
           $id = $_GET['no'];

            $menuLst = array();
            //get menu by role
            $sqlRoleMenu = "select role, menu from role_menu where role=?";
            $stmtRoleMenu = $conn->prepare($sqlRoleMenu);
            $stmtRoleMenu->bind_param('i', $id);
            $stmtRoleMenu->execute();
            $stmtRoleMenu->store_result();
           
            if(!$stmtRoleMenu->error) {
                while ($row = fetchAssocStatement($stmtRoleMenu))
                {
                    array_push($menuLst, array('role' => $row['role'], 
                        'menu' => $row['menu']));
                }
            }
        }
    }

    $stmtRoleMenu->close();
    $conn->close();
?>