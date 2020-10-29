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
$searchQuery = "";
$edit_disabled = "";
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    
    $searchCd = "";
    $searchMaker = "";
    $searchCustomer = "";
    $searchSupport = "";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $display=TRUE;
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
            $searchCustomer = " and customer = ".$customer;
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

        $findstmt;
        $findresult;
        
        if(!empty($searchCustomer))
        {
            $findstmt = $conn->prepare("SELECT  customer.sales from customer where no=".$customer);
            $findresult = execute($findstmt,$conn);

            if($findresult==TRUE)
            {
                $findresult = $findstmt->store_result();
                $findresultSet = $findresult;
            }

            if($findstmt->num_rows>0)
            {
                while($row = fetchAssocStatement($findstmt))
                {
                    $edit_disabled = ($row["sales"]!=$_SESSION["loginUserId"] && $_SESSION["loginRole"]!=1)?"disabled":"";
                }
            }
        }
        
    }



$stmt;
$result="";

            if(!empty($searchCustomer))
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
                a.approvalday,
                inventory_mark.hidden as hidden,
                date(a.upday) as LAST_UPDATE,
                customer.sales as sales
                from selling_price a
                INNER JOIN commodity on a.commodity=commodity.no
                INNER JOIN inventory on inventory.commodity=commodity.no
                INNER JOIN maker on commodity.maker=maker.no
                INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
                INNER JOIN print_type on print_type.no=commodity.print_type
                INNER JOIN customer on a.customer=customer.no		
                WHERE a.seq = (select max(seq) from selling_price b WHERE b.inday IS NOT NULL ".$searchCustomer." )
                AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."
                GROUP BY a.customer,a.commodity,a.num
                ORDER BY maker.name,print_type.no,code ASC");
                $result = execute($stmt,$conn);
            }

if($result===TRUE)
{
    $result = $stmt->store_result();
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
		WHERE a.seq = (select max(seq) from selling_price b WHERE b.inday IS NOT NULL ".$searchCustomer.")
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."
		GROUP BY a.customer,a.commodity,a.num
        ORDER BY maker.name,print_type.no,code ASC");

    $oldresult = execute($oldstmt,$conn);
    if($oldresult==TRUE)
    {
        $oldresult = $oldstmt->store_result();
        $oldresultSet = $oldresult;
    }

    $pricestmt = $conn->prepare("SELECT  distinct print_type.name as type,
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
		WHERE a.seq = (select max(seq) from selling_price b WHERE b.approvalday IS NOT NULL ".$searchCustomer.")
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."
		GROUP BY a.customer,a.commodity,a.num
        ORDER BY maker.name,print_type.no,code ASC");

    $priceresult = execute($pricestmt,$conn);
    if($priceresult==TRUE)
    {
        $priceresult = $pricestmt->store_result();
        $priceresultSet = $priceresult;
    }

    if($invalid==null && !empty($searchCustomer))
    {
        $addstmt = $conn->prepare("SELECT  distinct print_type.name as type,
                    commodity.no as commodity,
                    maker.name as maker,
                    commodity.cd as code,
                    commodity.price as original,
                    commodity.num as qty,
                    commodity.printer_support as support,
                    inventory_mark.display as display,
                    inventory_mark.hidden as hidden
                    from inventory 
                    INNER JOIN commodity on inventory.commodity=commodity.no AND commodity.invalid=0
                    LEFT OUTER JOIN selling_price on inventory.commodity=selling_price.commodity
                    INNER JOIN maker on commodity.maker=maker.no AND maker.invalid=0
                    INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no AND inventory_mark.hidden=0
                    INNER JOIN print_type on print_type.no=commodity.print_type		
                    WHERE selling_price.commodity IS NULL ".$searchMaker.$searchSupport.$searchCd."
                    ORDER BY maker.name,print_type.no,code ASC");

            $addresult = execute($addstmt,$conn);
            if($addresult==TRUE)
            {
                $addresult = $addstmt->store_result();
                $addresultSet = $addresult;
            }
    }

$makerStmt = $conn->prepare("select no,name from maker where invalid=0");
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->store_result();
    $makerResultSet = $makerResult;
}

$customerStmt = $conn->prepare("select no,name from customer where invalid=0");
$customerResult = execute($customerStmt,$conn);
if($customerResult==TRUE)
{
    $customerResult=$customerStmt->store_result();
    $customerResultSet = $customerResult;
}

$last_updated="";
$dateStmt = $conn->prepare("SELECT  distinct 
        date(a.upday) as LAST_UPDATE
		from selling_price a
        INNER JOIN commodity on a.commodity=commodity.no
        INNER JOIN inventory on inventory.commodity=commodity.no
        INNER JOIN maker on commodity.maker=maker.no
        INNER JOIN inventory_mark on inventory.inventory_mark=inventory_mark.no
        INNER JOIN print_type on print_type.no=commodity.print_type
		INNER JOIN customer on a.customer=customer.no		
		WHERE a.seq = (select max(seq) from selling_price b WHERE (b.approvalday IS NOT NULL OR b.seq=0) ".$searchCustomer." )
        AND commodity.invalid=0 AND maker.invalid=0 AND customer.invalid=0 ".$searchQuery."");
$dateResult = execute($dateStmt,$conn);
if($dateResult==true)
{
    $dateResult = $dateStmt->store_result();
    $dateResultSet = $dateResult;
}
$last_updated="";
while($row = fetchAssocStatement($dateStmt))
{
    $last_updated = !empty($row["LAST_UPDATE"])?$row["LAST_UPDATE"]:"まだ更新されていません";
}

if(empty($searchCustomer))
    $last_updated="-";
?>