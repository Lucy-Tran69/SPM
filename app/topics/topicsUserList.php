<?php
    include_once "database/db.inc";

    /**
     * Get a list of topic user in open day descending.
     *
     * The first, just take 5 topics.
     *
     * Then load more, each button press will load 5 more topics.
     */

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
    $empQuery = "SELECT no, title, DATE_FORMAT(opday, '%Y/%m/%d') AS open_day FROM topics WHERE invalid = 0 AND opday <= '$date' AND (clday >= '$date' OR clday is null) order by open_day desc limit  $start,".($limit + 1);

    $stmEmpQuery = $conn->prepare($empQuery);
    $stmEmpQuery->execute();
    $stmEmpQuery->store_result();
    $query = mysqli_query($conn, $empQuery) or die ('エラーが発生しました。もう一度お試しください。');
    
    $result = array();
    while ($row = fetchAssocStatement($stmEmpQuery))
    {
        array_push($result, $row);
    }
   
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        die (json_encode($result));
    } else{
        $total = count($result);
        for ($i = 0; $i < $total - 1; $i++)
        { ?>
            <tr data-id="<?php echo $result[$i]['no']; ?>" class="item-topic">
                <td><a href="javascript:void(0)"><?php echo $result[$i]['open_day']; ?></a></td>
                <td> <?php
                        $openDay = date('Y-m-d H:i:s', strtotime($result[$i]['open_day']. ' + 7 days'));
                         if ($openDay >= $date) { ?>
                            <b style="color: red;">New!</b>
                    <?php } ?>
                    <a href="javascript:void(0)">
                   
                    <?php echo $result[$i]['title']; ?>
                    </a>
                </td>
            </tr>
    <?php }
    }
?>