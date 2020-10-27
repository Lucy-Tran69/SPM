<?php
include_once "database/db.inc";
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $arr = $_POST["arr"];
    // $cust = $_POST["cust"];
    // $commodity = $_POST["commodity"];
    // $price = $_POST["value"];
    // $user = $_POST["loginUserId"];
    // $flag = $_POST["flag"];
}
$arr = json_decode($arr);
$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$cno = $arr[0]->cust;
$custStmt = $conn->prepare("select customer.name,users.email from customer inner join users on customer.supervisor=users.no where customer.no=?");
$custStmt->bind_param('i',$cno);
$custResult = execute($custStmt, $conn);
$cust_name = "";
$toEmail = "";
if($custResult==true)
{
    $custResult = $custStmt->get_result();
    $custResultSet = $custResult;
   if($custResultSet->num_rows > 0)
   {
    while($row = $custResultSet->fetch_assoc())
    {
        $cust_name = $row["name"];
        $toEmail = $row["email"];
    }
   }
}

for($i=0;$i<count($arr);$i++)
{
    $temp = $arr[$i];
    $sequence_no=0;
    $max_seq=0;
    $stmt;
    $seqStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE approvalday IS NULL AND customer=".$temp->cust." AND commodity=".$temp->commodity);
    $seqResult = execute($seqStmt,$conn);
    $seqResult = $seqStmt->get_result();
    while($row = $seqResult->fetch_assoc())
    {
        if($row["SEQ"]!=null)
            $sequence_no=$row["SEQ"];
    }
    
    $maxseqStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE customer=".$temp->cust);
    $maxseqResult = execute($maxseqStmt,$conn);
    $maxseqResult = $maxseqStmt->get_result();
    
    while($row = $maxseqResult->fetch_assoc())
    {
        if($row["SEQ"]!=null)
            $max_seq=$row["SEQ"];
    }
    if($temp->flag==false)
    {
        $seStmt = $conn->prepare("select max(seq) as SEQ from selling_price WHERE customer=".$temp->cust);
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
            ".$temp->cust.",
            ".$seq_no.",
            ".$temp->commodity.",
            (select num from commodity where no=".$temp->commodity."),
            ".$temp->value.",
            ".$temp->loginUserId.",
            null,
            2,
            sysdate(),
            ".$temp->loginUserId.",
            null,
            null)");
    }
    else if($max_seq==$sequence_no && $max_seq!=0)
    {
        // $max_seq=$max_seq+1;    
        $stmt=$conn->prepare("UPDATE selling_price SET status=2, 
                                                    price=".$temp->value." ,
                                                    upday=sysdate(),
                                                    upuser=".$temp->loginUserId." 
                                                    WHERE customer=".$temp->cust." 
                                                    AND commodity=".$temp->commodity." 
                                                    AND seq=".$max_seq);
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
                                                        ".$temp->cust.",
                                                        max(seq)+1,
                                                        commodity,
                                                        num,
                                                        ".$temp->value.",
                                                        ".$temp->cust.",
                                                        null,
                                                        ".$temp->loginUserId.",
                                                        sysdate(),
                                                        ".$temp->loginUserId.",
                                                        sysdate(),
                                                        2
                                                     from selling_price
                                                    where
                                                    customer=".$temp->cust." AND commodity=".$temp->commodity." 
                                                    AND seq=(select max(seq) from selling_price WHERE customer=".$temp->cust." AND commodity=".$temp->commodity.")");
    }
    
    $result = execute($stmt,$conn);
}


if($result)
{
           // the message
           $msg = "お疲れ様です\n価格の承認をお願いいたします。\n取引先：".$cust_name."様";

           // use wordwrap() if lines are longer than 70 characters
           $msg = wordwrap($msg,70);
       
           // send email
           $success = mail($toEmail,"価格承認依頼",$msg);
           if (!$success) {
               $errorMessage = error_get_last()['message'];
           }  
}
else
{
    echo "SQL Error";
}

?>