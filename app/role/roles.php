<?php
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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $menu = isset($_POST["menu"]) ? $_POST["menu"] : '';
        $outSide = isset($_POST["outSide"]) ? $_POST["outSide"] : '';
        $status = isset($_POST["status"]) ? $_POST["status"] : 0;
        $sortOrder = isset($_POST["sortOrder"]) ? $_POST["sortOrder"] : '';

        if(!empty($menu)){
            $searchMenu = "role_menu.menu = $menu";
            $join = "inner join role_menu on role.no = role_menu.role ";
        }

        if(!empty($outSide)){
            if ($outSide == 0){
                $searchOutSide = "";
            }
            else {
                $searchOutSide = "outside.no = $outSide";
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


            if(!empty($searchQuery))
                {
                    $searchQuery = $searchQuery." and ";
                }
        }

        if (!empty($sortOrder)) {
            foreach ($sortOrder as $value) {
                $sort = $value['sortOrder'];
                $no = $value['no'];
                $empQuery = "update role set sort_order = $sort where no = $no";
                mysqli_query($conn, $empQuery);
            }
        }
    }

    $searchQuery = $searchQuery."role.invalid = $status ";

    $searchQuery = "where ".$searchQuery;

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
                <div class="row justify-content-center w-200px">
                    <a href="" class="btn btn-success m-b-5px m-r-0 m-b-0"><i class="fas fa-pencil-alt mr-2"></i>変更</a>
                </div>
            </td>
        </tr>
    <?php   }

    $conn->close();
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