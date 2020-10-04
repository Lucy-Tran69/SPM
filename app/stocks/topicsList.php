<?php
    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    ## Read value
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Kiểm tra trang hiện tại có bé hơn 1 hay không
    if ($page < 1) {
        $page = 1;
    }
    
    // Số record trên một trang
    $limit = 5;
    
    // Tìm start
    $start = ($limit * $page) - $limit;
    
    $date = date("Y-m-d H:i:s");

    ## Fetch records
    $empQuery = "SELECT no, title, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day FROM topics WHERE invalid = 0 AND opday <= '$date' order by open_day desc limit  $start,".($limit + 1);

    // Thực hiện câu truy vấn
    $query = mysqli_query($conn, $empQuery) or die ('Lỗi câu truy vấn');
    
    // Duyệt kết quả rồi đưa vào mảng result
    $result = array();
    while ($row = mysqli_fetch_array($query))
    {
        // Thêm vào result
        array_push($result, $row);
    }

    // Hiển thị dữ liệu
    $total = count($result);
    // Bỏ đi kết quả cuối cùng vì kết quả này dùng để check phân trang thôi
    // Tuy nhiên chỉ bỏ ở trường hợp ($total > $limit) nếu không ở trang cuối cùng sẽ mất một row
    if ($total > $limit){
        for ($i = 0; $i < $total - 1; $i++)
        { ?>
            <tr data-id="<?php echo $result[$i]['no']; ?>" class="item-topic" data-toggle="modal" data-target="#modal-detail-topic">
                <td><a href="javascript:void(0)"><?php echo $result[$i]['open_day']; ?></a></td>
                <td><a href="javascript:void(0)"><?php echo $result[$i]['title']; ?></a></td>
            </tr>
    <?php }
    }
    else{
        for ($i = 0; $i < $total; $i++)
        { ?>
            <tr data-id="<?php echo $result[$i]['no']; ?>" class="item-topic" data-toggle="modal" data-target="#modal-detail-topic">
                <td><a href="javascript:void(0)"><?php echo $result[$i]['open_day']; ?></a></td>
                <td><a href="javascript:void(0)"><?php echo $result[$i]['title']; ?></a></td>
            </tr>
    <?php }
    }
    
    // Nếu hết dữ liệu thì stop không phan trang nữa
    // Ta chỉ cần kiểm tra xem tổng số record có nhiều hơn limit hay không
    // vì trong câu truy vấn mình select với limit = limit + 1
    if ($total <= $limit){
        echo '<script language="javascript">stopped = true; </script>';
    }
?>