<?php 
echo "\xEF\xBB\xBF";/// Byte Order Mark
include_once('database/db.inc');
$invalid = isset($_POST["csvInvalid"])?$_POST["csvInvalid"]:null;
$inventory= isset($_POST["csvInventory"])?$_POST["csvInventory"]:null;
$filename = "inventory_csv.csv";
$fp = fopen('php://output', 'w');
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$searchQuery = "";
$searchCd = "";
$searchMaker = "";

    $maker = isset($_POST["selectedMaker"])?$_POST["selectedMaker"]:null;
    $code = isset($_POST["searchCartridge"])?$_POST["searchCartridge"]:null;
   // echo json_encode ($status);die();
    if(!empty($code)){
        $searchCd = " and commodity.name like '%$code%' ";
    }
    if(!empty($maker)){
        $searchMaker = " and maker.no = ".$maker;
    }
   
    if (!empty($searchCd) || !empty($searchMaker)) {
        if (!empty($searchCd))
        {
            $searchQuery = $searchQuery.$searchCd ;
        }
        if (!empty($searchMaker))
        {
            $searchQuery = $searchQuery.$searchMaker;
        }
    }
    $stmt;
    if($inventory==null)
    {
    if($invalid==null)
    {
        $stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        coalesce(inventory_mark.display,'NEW') as display  
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." order by commodity.cd ASC");
    }
    else
    {
        $stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        coalesce(inventory_mark.display,'NEW') as display 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery. " AND inventory.inventory_mark<>4 order by commodity.cd ASC");
    }
    }
    else
    {   
    if($invalid==null)
    {
        $stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        coalesce(inventory_mark.display,'NEW') as display 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." AND (inventory_mark.no=5 OR inventory_mark.no IS NULL) order by commodity.cd ASC");
    }
    else
    {
        $stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        coalesce(inventory_mark.display,'NEW') as display   
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." AND (inventory_mark.no=5 OR inventory_mark.no IS NULL) order by commodity.cd ASC");
    }
    }
$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->store_result();
    $resultSet = $result;
}
$currentDate = array(date('Y-m-d'));
$header = array("メーカー","完成品在庫","在庫状況");
header('Content-Encoding: UTF-8');
header('Content-Type: application/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename='.$filename);
fputcsv($fp,$currentDate);
fputcsv($fp, $header);
while($row = fetchAssocStatement($stmt)) {
	fputcsv($fp, $row);
}

?>