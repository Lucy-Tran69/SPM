<?php
    include_once "common/session.php";
    include_once "database/db.inc";

    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

     ## Search 
    $searchQuery = "";
    $searchMenu = "";
    $searchOutSide = "";
    $status = 0;
    $join = "";
    
    if(isset($_SESSION['search']) && count($_SESSION['search'])>0) {
        // print_r($_SESSION['search']);
        $menu = isset($_SESSION['search']["menu"]) ? $_SESSION['search']["menu"] : '';
        $outSide = isset($_SESSION['search']["outSide"]) ? $_SESSION['search']["outSide"] : '';
        $status = isset($_SESSION['search']["status"]) ? $_SESSION['search']["status"] : 0;

        if(!empty($menu)){
            $searchMenu = "role_menu.menu = $menu ";
            $join = "inner join role_menu on role.no = role_menu.role ";
        }

        if(!empty($outSide)){
            if ($outSide == 0){
                $searchOutSide = "";
            }
            else {
                $searchOutSide = "outside.no = $outSide ";
            }
        }

        if (!empty($searchMenu) || !empty($searchOutSide)) {
            if (!empty($searchMenu))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchMenu ;
            }

            if (!empty($searchOutSide))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchOutSide ;
            }
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $menu = isset($_POST["menu"]) ? $_POST["menu"] : '';
        // print_r($_SESSION['menu']);
        $outSide = isset($_POST["outSide"]) ? $_POST["outSide"] : '';
        $status = isset($_POST["status"]) ? $_POST["status"] : 0;
        $sortOrder = isset($_POST["sortOrder"]) ? $_POST["sortOrder"] : '';

        if(!empty($menu)){
            $searchMenu = "role_menu.menu = $menu ";
            $join = "inner join role_menu on role.no = role_menu.role ";
        }

        if(!empty($outSide)){
            if ($outSide == 0){
                $searchOutSide = "";
            }
            else {
                $searchOutSide = "outside.no = $outSide ";
            }
        }

        if (!empty($searchMenu) || !empty($searchOutSide)) {
            if (!empty($searchMenu))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchMenu ;
            }

            if (!empty($searchOutSide))
            {
                if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
                $searchQuery = $searchQuery.$searchOutSide ;
            }
        }

        if (!empty($sortOrder)) {
            $empQuery = "update role set sort_order = ? where no = ?";
            $stmSort = $conn->prepare($empQuery);
            foreach ($sortOrder as $value) {
                $sort = $value['sortOrder'];
                $no = $value['no'];

                $stmSort->bind_param('ii', $sort, $no);
                $stmSort->execute();
            }

            if($stmSort->error) {
                $msg->error('表示順の更新に失敗しました。');
            }
            else {
                $msg->success('表示順の更新に成功しました。');
            }
        }

        // $_SESSION['menu'] = $menu;
        $_SESSION['search'] = array('menu' => $menu, 'outSide' => $outSide, 'status' => $status);
    }

    if ($status != 1) {
        if(!empty($searchQuery))
        {
            $searchQuery = $searchQuery." and ";
        }
        $searchQuery = $searchQuery."role.invalid = $status ";
    }

    if (!empty($searchQuery)) {
        $searchQuery = "where ".$searchQuery;
    }

    $sql = " select role.no as no, role.name as name, role.sort_order as sort_order, outside.name as outside_name from role inner join outside on role.outside = outside.no ".$join.$searchQuery."order by sort_order asc";

    $query = mysqli_query($conn, $sql) or die ('エラーが発生しました。もう一度お試しください。');

    $data = array();
    while ($row = mysqli_fetch_array($query))
    {
        array_push($data, array('no' => $row['no'], 
                              'name' => $row['name'],
                              'sort_order' => $row['sort_order'],
                              'outside_name' => $row['outside_name']));
    }

    foreach ($data as $value) { ?>
        <tr data-id="<?php echo $value['no']?>"> 
            <td class="text-center"><?php echo $value['name']?></td>
            <td class="text-center"><?php echo $value['outside_name']?></td>
            <td class="text-right sort" contenteditable='true'><?php echo $value['sort_order']?></td>
            <td class="width-100px">
                <div class="row justify-content-center w-200px custom-button">
                    <a href="edit-role.html?no=<?php echo $value['no']?>" class="btn btn-success m-b-5px m-r-0 m-b-0"><i class="fas fa-pencil-alt mr-2"></i>変更</a>
                </div>
            </td>
        </tr>
    <?php   }

    $conn->close();
if (!empty($data)) {
    ?>
    <tr>
        <td class="text-center"></td>
        <td class="text-center"></td>
        <td class="text-right"> 
            <div class="row justify-content-end w-200px">
                <input type="button" class="btn btn-secondary change-sort-order" value="確定" />
            </div>
        </td>
        <td class="width-100px">
        </td>
    </tr>

    <?php } ?>