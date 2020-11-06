<?php
include_once "common/session.php";
include_once "database/db.inc";

/**
 * Get a list of all roles in ascending sort order.
 *
 * Search role according to menu, outside and status.
 *
 * Update the sort order if there is a change in sequence.
 */

$conn  = getConnection();

if ($conn->connect_error) {
    die("Failed to connect to database. " . $conn->connect_error);
}

## Search 
$status = 0;
$join = "";
$wheres = [];
$wheresBind = [];
$newsearchQuery= "";
$newsearchLimit= "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu = isset($_POST["menu"]) ? $_POST["menu"] : '';
    $outSide = isset($_POST["outSide"]) ? $_POST["outSide"] : '';
    $status = isset($_POST["status"]) ? $_POST["status"] : '';
    $sortOrder = isset($_POST["sortOrder"]) ? $_POST["sortOrder"] : '';

    if (!is_numeric($status)) {
        $status = 2;
    }

    if ($menu) {
        array_push($wheres, " role_menu.menu = ? ");
        array_push($wheresBind, "$menu");
        $newsearchLimit = $newsearchLimit . "i";
        $join = "inner join role_menu on role.no = role_menu.role ";
    }

    if ($outSide) {
        array_push($wheres, " outside.no = ? ");
        array_push($wheresBind, "$outSide");
        $newsearchLimit = $newsearchLimit . "i";
    }

    if ($status != 1) {
        array_push($wheres, " role.invalid = ? ");
        array_push($wheresBind, $status);
        $newsearchLimit = $newsearchLimit . "i";
    } 

    if (count($wheres) > 0) {
        $newsearchQuery = " where ".implode(" and ", $wheres);
    }

     /**
     * Update sort order
     */
    if (!empty($sortOrder)) {
        $empQuery = "update role set sort_order = ? where no = ?";
        $stmSort = $conn->prepare($empQuery);
        foreach ($sortOrder as $value) {
            $sort = $value['sortOrder'];
            $no = $value['no'];

            $stmSort->bind_param('ii', $sort, $no);
            $stmSort->execute();
        }

        if ($stmSort->error) {
            $msg->error('表示順の更新に失敗しました。');
        } else {
            $msg->success('表示順の更新に成功しました。');
        }

        $stmSort->close();
    }
    
}

$sql = "select role.no as no, role.name as name, role.sort_order as sort_order, outside.name as outside_name from role inner join outside on role.outside = outside.no " . $join . $newsearchQuery . "order by sort_order asc";

$stmt = $conn->prepare($sql);
if(count($wheresBind) == 0) {
 $newsearchLimit = "";
} else if(count($wheresBind) == 1) {
 
 $stmt->bind_param("i",$wheresBind[0]);
} 
 else if(count($wheresBind) == 2) {
 $stmt->bind_param("ii",$wheresBind[0],$wheresBind[1]);
} 
 else if(count($wheresBind) == 3) {
  $stmt->bind_param("iii",$wheresBind[0],$wheresBind[1], $wheresBind[2]);
} 

$stmt->execute();
$stmt->store_result();
$data = array();


while ($row = fetchAssocStatement($stmt)) {
    array_push($data, array(
        'no' => $row['no'],
        'name' => $row['name'],
        'sort_order' => $row['sort_order'],
        'outside_name' => $row['outside_name']
    ));
}

$conn->close();

if (!empty($data)) {
    foreach ($data as $value) { ?>
        <tr data-id="<?php echo $value['no'] ?>">
            <td class="text-left text-nowrap"><?php echo $value['name'] ?></td>
            <td class="text-left text-nowrap"><?php echo $value['outside_name'] ?></td>
            <td class="text-right sort" contenteditable='true'><?php echo $value['sort_order'] ?></td>
            <td class="width-100px">
                <div class="row justify-content-center w-200px custom-button">
                    <a href="edit-role.html?no=<?php echo $value['no'] ?>" class="btn btn-success m-b-5px m-r-0 m-b-0"><i class="fas fa-pencil-alt mr-2"></i>変更</a>
                </div>
            </td>
        </tr>
    <?php }?>
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

<?php }
else {
   ?>
   <row>
    <td class="text-center" colspan="4">データがありません。</td>
</row>
<?php } ?>
