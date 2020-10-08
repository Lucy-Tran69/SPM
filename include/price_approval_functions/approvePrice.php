<?php
include_once "database/db.inc";
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $cust = $_POST["cust"];
    $commodity = $_POST["commodity"];
    $seq = $_POST["seq"];
    $user = $_POST["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}
$stmt = $conn->prepare("UPDATE selling_price SET approver=".$user.",approvalday=sysdate() 
                                WHERE customer=".$cust." AND commodity=".$commodity." AND seq=".$seq."");

$result = execute($stmt,$conn);
if($result)
{
   // header("Location: ../../app/login/inventory.html");  
}
else
{
    echo "SQL Error";
}

?>