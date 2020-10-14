<?php
    include_once "database/db.inc";

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
        $checkDupSortOrder = "select count(*) as count_row from role where sort_order = '$sortOrder'";
        $numDupSortOrder = mysqli_query($conn,$checkDupSortOrder);

        $row = mysqli_fetch_assoc($numDupSortOrder);
        $num = $row['count_row'];
        if($num > 0){
            $msg->error('この表示順は既に存在しています。');
            $checkOK = 0;
        }

        if (empty($sortOrder)) {
            $getLastSortOrder = "select sort_order from role order by no desc limit 1";
            $lastSortOrder = mysqli_query($conn,$getLastSortOrder);
            $rowSortOrder = mysqli_fetch_assoc($lastSortOrder);
            $sortOrder = $rowSortOrder['sort_order'] + 1;
        }

        if ($checkOK == 1) {
            //check temp increment NO
            $no = "select no from role order by no desc limit 1";
            $lastNo = mysqli_query($conn,$no);
            $rowNo = mysqli_fetch_assoc($lastNo);
            $NO = $rowNo['no'] + 1;

            $sql = "insert into role(no, name, outside, sort_order, invalid, inuser) values(?,?,?,?,?,?)";
            $stmt = $conn->prepare($sql);

            $stmt->bind_param('isiiii', $NO, $roleName, $outSide, $sortOrder, $status, $insert_user);

            $stmt->execute();

            // $lastId = mysqli_insert_id($conn);

            if ($stmt->error) {
                $msg->error('権限の追加に失敗しました。');
            } else {
                foreach ($menu as $value) {
                   $roleMenu = "insert into role_menu(role, menu, inuser) values(?,?,?)";
                   $stm = $conn->prepare($roleMenu);

                   $stm->bind_param('iii', $NO, $value, $insert_user);

                   $stm->execute();
                }
                if ($stm->error) {
                    $msg->error('権限の追加に失敗しました。');
                }
               else {
                    $msg->success('権限の追加に成功しました。');
                    echo("<script>location.href = 'index.html';</script>");
               }
            }
            $conn->close();
        }
        else {
            $msg->display();
        }
    }

?>