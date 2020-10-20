<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $name = $_POST["copyName"];
    $code = $_POST["copyCode"];
    $print_type = $_POST["copyType"];
    $num = $_POST["copyPaid"];
    $price = $_POST["copyPrice"];
    $maker = $_POST["copyMaker"];
    $printer_support = $_POST["copyPrinters"];
    $note = $_POST["copyNote"];
    $memo = $_POST["copyMemo"];
    $green = $_POST["copyGreen"]==null? 0:1;
    $invalid = $_POST["copyInvalid"]==null? 0:1;
    $insert_date = date('Y-m-d');
    $insert_user = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$stmt = $conn->prepare("insert into commodity(name,cd,print_type,num,price,maker,green,printer_support,note,memo,invalid,inday,inuser) 
                        values(?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt->bind_param('ssiississsisi',$name,$code,$print_type,$num,$price,$maker,$green,$printer_support,$note,$memo,$invalid,$insert_date,$insert_user);

$result = execute($stmt,$conn);
if($result)
{
    header("Location: ../../app/commodity/");  
}
else
{
    echo "SQL Error";
}

?>