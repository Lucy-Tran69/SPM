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
$stmt = $conn->prepare("UPDATE selling_price SET status=1,upday=sysdate(),upuser=".$user." WHERE customer=".$cust." AND commodity=".$commodity." AND seq=".$seq);

$result = execute($stmt,$conn);
if($result)
{
   // header("Location: ../../app/login/inventory.html");  
}
else
{
    echo "SQL Error";
}


// the message
$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
$success = mail("r.aneesh@zenken.co.jp","Rejected",$msg);
if (!$success) {
    $errorMessage = error_get_last()['message'];
}
?>