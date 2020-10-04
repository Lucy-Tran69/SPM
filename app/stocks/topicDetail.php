<?php
    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : '';

    $data = array();

    if(!empty($id)) {
        ## Fetch records
        $sql = "SELECT title, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day, image, image_link, body, link_title, link_url FROM topics WHERE no=$id";

        // Thực hiện câu truy vấn
        $query = mysqli_query($conn, $sql) or die ('Lỗi câu truy vấn');

        // Duyệt kết quả rồi đưa vào mảng result
        $result = array();
        while ($row = mysqli_fetch_array($query))
        {
            $data = array(
                "title" => $row['title'],
                "open_day" => $row['open_day'],
                "image" => $row['image'],
                "image_link" => $row['image_link'],
                "body" => $row['body'],
                "link_title" => $row['link_title'],
                "link_url" => $row['link_url'],
            );
        }
    }
    echo json_encode($data);
    // die();
    $conn->close();
?>