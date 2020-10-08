<?php
include_once "database/db.inc";
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $cust = $_POST["cust"];
    $commodity = $_POST["commodity"];
    $price = $_POST["value"];
    $user = $_POST["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}
$stmt = $conn->prepare("insert into selling_price(customer,
                                                    seq,
                                                    commodity,
                                                    num,
                                                    price,
                                                    approver,
                                                    approvalday,
                                                    inuser,
                                                    inday,
                                                    upuser,
                                                    upday)
                                                select
                                                    customer,
                                                    max(seq)+1,
                                                    commodity,
                                                    num,
                                                    ".$price.",
                                                    customer,
                                                    null,
                                                    ".$user.",
                                                    sysdate(),
                                                    null,
                                                    null
                                                 from selling_price
                                                where
                                                customer=".$cust." AND commodity=".$commodity." AND seq=(select max(seq) from 
                                                selling_price WHERE customer=".$cust." AND commodity=".$commodity.")");

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