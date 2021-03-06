<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    /**
     * This is used to add a new role.
     *
     * If sort order is duplicate, then error.
     *
     * If sort order is null, default max sort order.
     *
     * Check input condition, if invalid then error.
     *
     * If valid, insert to database.
     */

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $roleName = isset($_POST["roleName"]) ? trim(strip_tags(preg_replace('#<script(.*?)>(.*?)</script>#is', '', $_POST["roleName"]))) : '';
       $outSide = isset($_POST["outSide"]) ? $_POST["outSide"] : '';
       $sortOrder = isset($_POST["sortOrder"]) ? $_POST["sortOrder"] : '';
       $status = isset($_POST["status"]) ? $_POST["status"] : 0;
       $menu = isset($_POST["menu"]) ? $_POST["menu"] : '';

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
        $checkDupSortOrder = "select count(*) as count_row from role where sort_order = ?";
        $stmtCheckDup = $conn->prepare($checkDupSortOrder);

        $stmtCheckDup->bind_param('i', $sortOrder);

        $stmtCheckDup->execute();

        $stmtCheckDup->store_result();
          $row = fetchAssocStatement($stmtCheckDup);
          if($row["count_row"] > 0)
          {
            $msg->error('表示順は既に存在しています。');
            $checkOK = 0;
          }

        $stmtCheckDup->close();

        if (empty($sortOrder)) {
            $getLastSortOrder = "select sort_order from role order by sort_order desc limit 1";
            $stmLastOrder = $conn->prepare($getLastSortOrder);
            $stmLastOrder->execute();
            $stmLastOrder->store_result();
            $rowSortOrder = fetchAssocStatement($stmLastOrder);
            $sortOrder = $rowSortOrder['sort_order'] + 1;
            $stmLastOrder->close();
        }

        if ($checkOK == 1) {
            $sql = "insert into role(name, outside, sort_order, invalid, inuser) values(?,?,?,?,?)";
            $stmt = $conn->prepare($sql);

            $stmt->bind_param('siiii', $roleName, $outSide, $sortOrder, $status, $insert_user);

            $stmt->execute();

            $lastId = mysqli_insert_id($conn);

            if (!empty($menu)) {
               $roleMenu = "insert into role_menu(role, menu, inuser) values(?,?,?)";
               $stm = $conn->prepare($roleMenu);
               foreach ($menu as $value) {
                 $stm->bind_param('iii', $lastId, $value, $insert_user);
                 $stm->execute();
                }
                $stm->close();
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