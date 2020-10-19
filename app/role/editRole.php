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

        if ($checkOK == 1) {
            $sql = "update role set name=?, outside=?, sort_order=?, invalid=?, inuser=? where no=?";
            $stmt = $conn->prepare($sql);

            $stmt->bind_param('siiiii', $roleName, $outSide, $sortOrder, $status, $insert_user, $no);

            $stmt->execute();

         if (!empty($menu)) {
                $imMenu = implode(",",$menu);
                
                // get id menu deleted
                $sql = "SELECT menu FROM role_menu WHERE menu not in ($imMenu) and role=$no";
                $query = mysqli_query($conn, $sql) or die ('エラーが発生しました。もう一度お試しください。');

                $idDelArr = array();
                while ($row = mysqli_fetch_array($query))
                {
                    array_push($idDelArr, $row["menu"]);
                }

                // get id menu existed
                $sql = "SELECT menu FROM role_menu WHERE menu in ($imMenu) and role=$no";
                $query = mysqli_query($conn, $sql) or die ('エラーが発生しました。もう一度お試しください。');

                $idInsArr = array();
                while ($row = mysqli_fetch_array($query))
                {
                    array_push($idInsArr, $row["menu"]);
                }

                // delete id menu deleted
                $idDel= implode(",",$idDelArr);
                $sqlDel = "DELETE from role_menu where menu in (?) and role=?";
                $stmtDel = $conn->prepare($sqlDel);
                foreach ($idDelArr as $value) {
                    $stmtDel->bind_param('ii', $value, $no);
                    $stmtDel->execute();
                }

                // add id menu not exist, existed is not added
                $sqlIns = "INSERT INTO role_menu(role, menu, inuser) VALUES(?,?,?)";
                $stmtIns = $conn->prepare($sqlIns);
                foreach($idInsArr as $key){
                    $keyToDelete = array_search($key, $menu);
                    unset($menu[$keyToDelete]);
                }

                foreach ($menu as $value) {
                    $stmtIns->bind_param('iii', $no, $value, $insert_user);
                    $stmtIns->execute();
                }
                
                $stmtDel->close();
                $stmtIns->close();
            } else {
                // delete all id menu
                $del = "delete from role_menu where role=?";
                $stmDel = $conn->prepare($del);
                $stmDel->bind_param('i', $no);
                $stmDel->execute();
                $stmDel->close();
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