<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";
$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$inventory= isset($_POST["searchInventory"])?$_POST["searchInventory"]:null;
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
    $searchQuery = "";
    $searchCd = "";
    $searchMaker = "";

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    }
$stmt;
if($inventory==null)
{
    if($invalid==null)
    {
        $stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        inventory.inventory_mark as mark,
                        inventory_mark.display as display,
                        inventory_mark.hidden as hidden,
                        commodity.no as id 
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
                        inventory.inventory_mark as mark,
                        inventory_mark.display as display,
                        inventory_mark.hidden as hidden,
                        commodity.no as id 
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
                        inventory.inventory_mark as mark,
                        inventory_mark.display as display,
                        inventory_mark.hidden as hidden,
                        commodity.no as id 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        inner JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." order by commodity.cd ASC");
    }
    else
    {
        $stmt = $conn->prepare("select maker.name as mName,
                        commodity.name as cName,
                        inventory.inventory_mark as mark,
                        inventory_mark.display as display,
                        inventory_mark.hidden as hidden,
                        commodity.no as id 
                        from maker 
                        LEFT JOIN commodity on maker.no=commodity.maker
                        inner JOIN inventory on inventory.commodity=commodity.no
                        LEFT JOIN inventory_mark on inventory_mark.no=inventory.inventory_mark
                        where commodity.name IS NOT NULL AND maker.invalid=0 ".$searchQuery." AND inventory.inventory_mark<>4 order by commodity.cd ASC");
    }
}
$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}

$markStmt = $conn->prepare("select no,display from inventory_mark");
$markResult = execute($markStmt,$conn);
if($markResult==TRUE)
{
    $markResult=$markStmt->get_result();
    $markResultSet = $markResult;
}

$newStmt = $conn->prepare("select no,display from inventory_mark where no=5");
$newResult = execute($newStmt,$conn);
if($newResult==TRUE)
{
    $newResult=$newStmt->get_result();
    $newResultSet = $newResult;
}

while($newrow = $newResultSet->fetch_assoc())
{
    $markNew = $newrow["display"];
}

$makerStmt = $conn->prepare("select no,name from maker where invalid=0");
$makerResult = execute($makerStmt,$conn);
if($makerResult==TRUE)
{
    $makerResult=$makerStmt->get_result();
    $makerResultSet = $makerResult;
}

?>?