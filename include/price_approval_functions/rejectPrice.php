<?php
include_once "database/db.inc";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $arr = $_POST["arr"];
    // $cust = $_POST["cust"];
    // $commodity = $_POST["commodity"];
    // $seq = $_POST["seq"];
    // $user = $_POST["loginUserId"];
}
$arr = json_decode($arr);
$conn = getConnection();
if ($conn->connect_error) {
    die("Failed to connect to database. " . $conn->connect_error);
}

$cno = $arr[0]->cust;
$custStmt = $conn->prepare("select customer.name,users.email from customer inner join users on customer.sales=users.no where customer.no=?");
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
    $stmt = $conn->prepare("UPDATE selling_price SET status=1,
                                                 upday=sysdate(),
                                                 upuser=" . $temp->loginUserId . " 
                                                 WHERE customer=" . $temp->cust . " 
                                                 AND commodity=" . $temp->commodity . " 
                                                 AND seq=" . $temp->seq);
    
    $result = execute($stmt, $conn);
}

if ($result) {
    // the message
    $msg = "お疲れ様です\n価格が否認されました。\n取引先：".$cust_name."様";

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);

    // send email
    $success = mail($toEmail,"価格承認差戻",$msg);
    if (!$success) {
        $errorMessage = error_get_last()['message'];
}
} else {
    echo "SQL Error";
}



?>
