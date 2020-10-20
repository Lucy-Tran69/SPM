<?php
include_once "database/db.inc";
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $cust = $_POST["cust"];
    $commodity = $_POST["commodity"];
    $price = $_POST["value"];
    $user = $_POST["loginUserId"];
    $flag = $_POST["flag"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}
$sequence_no=0;
$max_seq=0;
$stmt;
$seqStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE approvalday IS NULL AND customer=".$cust." AND commodity=".$commodity);
$seqResult = execute($seqStmt,$conn);
$seqResult = $seqStmt->get_result();
while($row = $seqResult->fetch_assoc())
{
    if($row["SEQ"]!=null)
        $sequence_no=$row["SEQ"];
}

$maxseqStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE customer=".$cust);
$maxseqResult = execute($maxseqStmt,$conn);
$maxseqResult = $maxseqStmt->get_result();

while($row = $maxseqResult->fetch_assoc())
{
    if($row["SEQ"]!=null)
        $max_seq=$row["SEQ"];
}
if($flag=="false")
{
    $seStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE customer=".$cust);
    if($seqStmt==true)
    {
        $seResult = execute($seStmt,$conn);
        $seResult = $seStmt->get_result();
    }
    $seq_no=0;
    while($row = $seResult->fetch_assoc())
    {
    if($row["SEQ"]!=null)
        $seq_no=$row["SEQ"];
    }
    $stmt = $conn->prepare("insert into selling_price values(
        ".$cust.",
        ".$seq_no.",
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
}
else if($max_seq==$sequence_no && $max_seq!=0)
{
    // $max_seq=$max_seq+1;    
    $stmt=$conn->prepare("UPDATE selling_price SET status=2, price=".$price." ,upday=sysdate(),upuser=".$user." WHERE customer=".$cust." AND commodity=".$commodity." AND seq=".$max_seq);
}
else
{
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
                                                    upday,
                                                    status)
                                                select
                                                    ".$cust.",
                                                    max(seq)+1,
                                                    commodity,
                                                    num,
                                                    ".$price.",
                                                    ".$cust.",
                                                    null,
                                                    ".$user.",
                                                    sysdate(),
                                                    ".$user.",
                                                    sysdate(),
                                                    2
                                                 from selling_price
                                                where
                                                customer=".$cust." AND commodity=".$commodity." AND seq=(select max(seq) from 
                                                selling_price WHERE customer=".$cust." AND commodity=".$commodity.")");
}

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