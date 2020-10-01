<?php
include_once "database/db.inc";
$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$selectedType = isset($_POST["selectedType"])?$_POST["selectedType"]:null;
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$stmt;
$typeQuery="";
if($invalid===null)
{
    if($selectedType==1)
    {
        $typeQuery=" and commodity.print_type=1";
    }
    elseif($selectedType==2)
    {
        $typeQuery=" and commodity.print_type=2";
    }
    $stmt = $conn->prepare("select  commodity.no,
                                commodity.invalid as invalid,
                                commodity.cd as code,
                                commodity.name as name,
                                maker.name as maker,
                                commodity.memo as memo,
                                commodity.print_type as type
                                from commodity 
                                left join maker on commodity.maker=maker.no where commodity.invalid=0".$typeQuery);
}
else
{
    if($selectedType==1)
    {
        $typeQuery=" where commodity.print_type=1";
    }
    elseif($selectedType==2)
    {
        $typeQuery=" where commodity.print_type=2";
    }
    $stmt = $conn->prepare("select  commodity.no,
                                commodity.invalid as invalid,
                                commodity.cd as code,
                                commodity.name as name,
                                maker.name as maker,
                                commodity.memo as memo,
                                commodity.print_type as type
                                from commodity 
                                left join maker on commodity.maker=maker.no".$typeQuery);
}
$result = execute($stmt,$conn);
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

?>?