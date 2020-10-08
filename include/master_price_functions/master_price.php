<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";
$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$err_msg = "";
$conn  = getConnection();
$display=FALSE;
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
		customer.no as customer,
        commodity.no as commodity,
		a.seq as seq,
		maker.name as maker,
		commodity.cd as code,
		commodity.price as original,
		a.price as sp,
		a.approver as approver,
		commodity.num as qty,
		commodity.printer_support as support,
		inventory_mark.display as display,
        a.approvalday
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
		customer.no as customer,
        commodity.no as commodity,
		a.seq as seq,
		maker.name as maker,
		commodity.cd as code,
		commodity.price as original,
		a.price as sp,
		a.approver as approver,
		commodity.num as qty,
		commodity.printer_support as support,
		inventory_mark.display as display,
        a.approvalday
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

$makerStmt = $conn->prepare("select no,name from maker where invalid=0");
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->get_result();
    $makerResultSet = $makerResult;
}

$customerStmt = $conn->prepare("select no,name from customer where invalid=0");
$customerResult = execute($customerStmt,$conn);
if($customerResult==TRUE)
{
    $customerResult=$customerStmt->get_result();
    $customerResultSet = $customerResult;
}
?>?