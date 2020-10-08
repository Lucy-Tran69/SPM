<?php
echo "\xEF\xBB\xBF";/// Byte Order Mark
include_once "database/db.inc";
$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$err_msg = "";
$conn  = getConnection();
$display=FALSE;
$filename = "prices_csv.csv";
$fp = fopen('php://output', 'w');
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    $searchQuery = "";
    $searchCd = "";
    $searchMaker = "";
    $searchCustomer = "";
    $searchSupport = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $maker = isset($_POST["selectedMaker"])?$_POST["selectedMaker"]:null;
        $customer = isset($_POST["selectedCustomer"])?$_POST["selectedCustomer"]:null;
        $cart = isset($_POST["searchCartridge"])?$_POST["searchCartridge"]:null;
        $support = isset($_POST["searchSupport"])?$_POST["searchSupport"]:null;
       // echo json_encode ($status);die();
        if(!empty($cart)){
            $searchCd = " and commodity.cd like '%$cart%' ";
        }
        if(!empty($maker)){
            $searchMaker = " and maker.no = ".$maker;
        }
        if(!empty($customer)){
            $searchCustomer = " and customer.no = ".$customer;
        }
        if(!empty($support)){
            $searchSupport = " and commodity.printer_support like '%$support%'";
        }
       
        if (!empty($searchCd) || !empty($searchMaker) || !empty($searchCustomer) || !empty($searchSupport)) {
            $display=TRUE;
            if (!empty($searchCd))
            {
                $searchQuery = $searchQuery.$searchCd ;
            }
            if (!empty($searchMaker))
            {
                $searchQuery = $searchQuery.$searchMaker;
            }
            if (!empty($searchCustomer))
            {
                $searchQuery = $searchQuery.$searchCustomer;
            }
            if (!empty($searchSupport))
            {
                $searchQuery = $searchQuery.$searchSupport;
            }
        }
    }
$stmt;
$result;
  if($invalid==null && !empty($searchCustomer))
    {
        $stmt = $conn->prepare("SELECT  distinct print_type.name as type,
		maker.name as maker,
		commodity.cd as code,
		IF(commodity.green=1,'●','') as green,
        '' as blank,
        commodity.price as original,
		a.price as sp,
		commodity.num as qty,
		commodity.printer_support as support,
        '' as blank2,
		inventory_mark.display as display
		from selling_price a
        INNER JOIN commodity on a.commodity=commodity.no
        INNER JOIN inventory on inventory.commodity=commodity.no
        INNER JOIN maker on commodity.maker=maker.no
        INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
        INNER JOIN print_type on print_type.no=commodity.print_type
		INNER JOIN customer on a.customer=customer.no		
		WHERE a.seq = (select max(seq) from selling_price WHERE approvalday IS NOT NULL".$searchCustomer.")
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 AND inventory_mark.hidden=0 ".$searchQuery."
		GROUP BY a.customer,a.commodity,a.num
        ORDER BY code ASC");
        $result = execute($stmt,$conn);
    }
    else if(!empty($invalid) && !empty($searchCustomer))
    {
        $stmt = $conn->prepare("SELECT  distinct print_type.name as type,
		maker.name as maker,
		commodity.cd as code,
		IF(commodity.green=1,'●','') as green,
        '' as blank,
        commodity.price as original,
		a.price as sp,
		commodity.num as qty,
		commodity.printer_support as support,
        '' as blank2,
		inventory_mark.display as display
		from selling_price a
        INNER JOIN commodity on a.commodity=commodity.no
        INNER JOIN inventory on inventory.commodity=commodity.no
        INNER JOIN maker on commodity.maker=maker.no
        INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
        INNER JOIN print_type on print_type.no=commodity.print_type
		INNER JOIN customer on a.customer=customer.no		
		WHERE a.seq = (select max(seq) from selling_price WHERE ".$searchCustomer." AND approvalday IS NOT NULL)
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 AND inventory_mark.hidden=0 ".$searchQuery."
		GROUP BY a.customer,a.commodity,a.num
        ORDER BY code ASC");
        $result = execute($stmt,$conn);
    }


if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}

$header = array("区分","ﾒｰｶｰ名","弊社型番（商品名)","ｸﾞﾘｰﾝ購入法","-","純正品参考価格","再生価格","再生回数","対応ﾌﾟﾘﾝﾀｰ","備考","");
header('Content-Encoding: UTF-8');
header('Content-Type: application/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename='.$filename);
fputcsv($fp, $header);
while($row = mysqli_fetch_row($result)) {
	fputcsv($fp, $row);
}
?>?