<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $no = isset($_POST["no"]) ? (int)$_POST["no"] : '';
       $roleName = isset($_POST["roleName"]) ? trim(strip_tags(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $_POST["roleName"]))) : '';
       $outSide = isset($_POST["outSide"]) ? $_POST["outSide"] : '';
       $sortOrderOld = isset($_POST["sortOrderOld"]) ? $_POST["sortOrderOld"] : '';
       $sortOrderNew = isset($_POST["sortOrderNew"]) ? $_POST["sortOrderNew"] : '';
       $status = isset($_POST["status"]) ? $_POST["status"] : 0;
       $menu = isset($_POST["menu"]) ? $_POST["menu"] : '';
       $sortOrder = $sortOrderOld;

       $insert_user = $_SESSION["loginUserId"];

       $checkOK = 1;

        if (empty($roleName)) {
            $msg->error("権限名をご入力ください。");
            $checkOK = 0;
        }

        if (mb_strlen(mb_convert_encoding($roleName, "UTF-8")) > 128) {
            $msg->error("権限名は256文字以内でご入力ください。");
            $checkOK = 0;
        }

        //check duplicate sort order
        if (!empty($sortOrderNew) && ($sortOrderNew != $sortOrderOld)) {
          $checkDupSortOrder = "select count(*) as count_row from role where sort_order = ?";
          $stmtCheckDup = $conn->prepare($checkDupSortOrder);

          $stmtCheckDup->bind_param('i', $sortOrderNew);

          $stmtCheckDup->execute();

          if(!$stmtCheckDup->error) {
            $rs = $stmtCheckDup->get_result();
            $row = $rs->fetch_array(MYSQLI_ASSOC);
          }
          
          $num = $row['count_row'];
          if($num > 0){
            $msg->error('表示順は既に存在しています。');
            $checkOK = 0;
          }

          $sortOrder = $sortOrderNew;
          $stmtCheckDup->close();
        }
       

        if (empty($sortOrderNew)) {
            $getLastSortOrder = "select sort_order from role order by no desc limit 1";
            $lastSortOrder = mysqli_query($conn,$getLastSortOrder);
            $rowSortOrder = mysqli_fetch_assoc($lastSortOrder);
            $sortOrder = $rowSortOrder['sort_order'] + 1;
        }

        // print_r($sortOrder);die();

        if ($checkOK == 1) {
            $sql = "update role set name=?, outside=?, sort_order=?, invalid=?, inuser=? where no=?";
            $stmt = $conn->prepare($sql);

            $stmt->bind_param('siiiii', $roleName, $outSide, $sortOrder, $status, $insert_user, $no);

            $stmt->execute();

            if (!empty($menu)) {
                 // $del = "delete from role_menu where role=?";
                 // $stmDel = $conn->prepare($del);
                 // $stmDel->bind_param('i', $no);
                 // $stmDel->execute();

                 // $roleMenu = "insert into role_menu(role, menu, inuser) values(?,?,?)";
                 // $stm = $conn->prepare($roleMenu);
                 // foreach ($menu as $value) {
                 //      $stm->bind_param('iii', $no, $value, $insert_user);
                 //      $stm->execute();
                 //  }
                 //  $stm->close();
print_r($menu);
               $del = "select menu from role_menu where menu not in ? and role=?";
                 $stmDel = $conn->prepare($del);
                 $stmDel->bind_param('si',$menu, $no);
                 $stmDel->execute();
                 $rsMenu = $stmDel->get_result();
                $row = $rsMenu->fetch_array(MYSQLI_ASSOC);
                print_r($row);
            }

            if ($stmt->error) {
                $msg->error('権限の追加に失敗しました。');
            } else {
                $msg->success('権限の追加に成功しました。');
            }
            $stmt->close();
            $conn->close();
        }
        else {
            $msg->display();
        }
    }

?>