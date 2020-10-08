<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";
$err_msg = "";
$conn  = getConnection();
$display=FALSE;
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    $searchQuery = "";
    $searchCustomer = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $customer = isset($_POST["selectedCustomer"])?$_POST["selectedCustomer"]:null;
       // echo json_encode ($status);die();
        if(!empty($customer)){
            $searchCustomer = " and customer.no = ".$customer;
        }
        if (!empty($searchCustomer)) {
            $display=TRUE;
            if (!empty($searchCustomer))
            {
                $searchQuery = $searchQuery.$searchCustomer;
            }
        }
    
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
		WHERE a.seq = (select max(seq) from selling_price WHERE approvalday IS NULL".$searchCustomer.")
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."
		GROUP BY a.customer,a.commodity,a.num
        ORDER BY code ASC");

    $result = execute($stmt,$conn);
    if($result==TRUE)
    {
        $result = $stmt->get_result();
        $resultSet = $result;
    }

    $oldstmt = $conn->prepare("SELECT  distinct print_type.name as type,
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
		WHERE a.seq = (select max(seq)-1 from selling_price WHERE a.approvalday IS NOT NULL".$searchCustomer.")
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."
		GROUP BY a.customer,a.commodity,a.num
        ORDER BY code ASC");

    $oldresult = execute($oldstmt,$conn);
    if($oldresult==TRUE)
    {
        $oldresult = $oldstmt->get_result();
        $oldresultSet = $oldresult;
    }
}

$customerStmt = $conn->prepare("select no,name from customer where invalid=0");
$customerResult = execute($customerStmt,$conn);
if($customerResult==TRUE)
{
    $customerResult=$customerStmt->get_result();
    $customerResultSet = $customerResult;
}
?>?