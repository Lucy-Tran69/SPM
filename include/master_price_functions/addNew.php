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
$seqStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE customer=".$cust);
if($seqStmt==true)
{
    $seqResult = execute($seqStmt,$conn);
    $seqResult = $seqStmt->get_result();
}
$sequence_no=0;
while($row = $seqResult->fetch_assoc())
{
    if($row["SEQ"]!=null)
        $sequence_no=$row["SEQ"];
}
$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}
    $stmt = $conn->prepare("insert into selling_price values(
                            ".$cust.",
                            ".$sequence_no.",
                            ".$commodity.",
                            (select num from commodity where no=".$commodity."),
                            ".$price.",
                            ".$user.",
                            null,
                            0,
                            sysdate(),
                            ".$user.",
                            null,
                            null)");

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