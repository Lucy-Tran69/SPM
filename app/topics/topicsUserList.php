<?php
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    ## Read value
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    if ($page < 1) {
        $page = 1;
    }
    
    // record in page
    $limit = 5;
    
    // start
    $start = ($limit * $page) - $limit;
    
    $date = date("Y-m-d H:i:s");

    ## Fetch records
    $empQuery = "SELECT no, title, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day FROM topics WHERE invalid = 0 AND opday <= '$date' order by open_day desc limit  $start,".($limit + 1);

    $query = mysqli_query($conn, $empQuery) or die ('Lỗi câu truy vấn');
    
    $result = array();
    while ($row = mysqli_fetch_array($query))
    {
        array_push($result, $row);
    }

    // display data
    $total = count($result);
   
    if ($total > $limit){
        for ($i = 0; $i < $total - 1; $i++)
        { ?>
            <tr data-id="<?php echo $result[$i]['no']; ?>" class="item-topic" data-toggle="modal" data-target="#modal-detail-topic">
                <td><a href="javascript:void(0)"><?php echo $result[$i]['open_day']; ?></a></td>
                <td>
                    <a href="javascript:void(0)">
                        <?php
                        $openDay = date('Y-m-d H:i:s', strtotime($result[$i]['open_day']. ' + 7 days'));
                         if ($openDay >= $date) { ?>
                            New!
                        <?php } ?>
                    <?php echo $result[$i]['title']; ?>
                        
                    </a>
                </td>
            </tr>
    <?php }
    }
    else{
        for ($i = 0; $i < $total; $i++)
        { ?>
            <tr data-id="<?php echo $result[$i]['no']; ?>" class="item-topic" data-toggle="modal" data-target="#modal-detail-topic">
                <td><a href="javascript:void(0)"><?php echo $result[$i]['open_day']; ?></a></td>
                <td><a href="javascript:void(0)">
                    <?php
                        $openDay = date('Y-m-d H:i:s', strtotime($result[$i]['open_day']. ' + 7 days'));
                         if ($openDay >= $date) { ?>
                            New!
                    <?php } ?>
                    <?php echo $result[$i]['title']; ?>
                        
                    </a>
                </td>
            </tr>
    <?php }
    }
    
    // check record > limit
    if ($total <= $limit){
        echo '<script language="javascript">stopped = true; </script>';
    }
?>