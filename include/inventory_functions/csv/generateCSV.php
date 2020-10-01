<?php 
echo "\xEF\xBB\xBF";/// Byte Order Mark
include_once('database/db.inc');
$filename = "inventory_csv.csv";
$fp = fopen('php://output', 'w');
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        inventory_mark.display as display 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        LEFT JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL order by commodity.no ASC");
$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}

$header = array("メーカー","完成品在庫","在庫状況");
header('Content-Encoding: UTF-8');
header('Content-Type: application/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename='.$filename);
fputcsv($fp, $header);
while($row = mysqli_fetch_row($result)) {
	fputcsv($fp, $row);
}

?>