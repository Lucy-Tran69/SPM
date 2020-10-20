<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $no = $_POST["inputId"];
    $name = $_POST["inputName"];
    $code = $_POST["inputCode"];
    $print_type = $_POST["inputType"];
    $num = $_POST["inputPaid"];
    $price = $_POST["inputPrice"];
    $maker = $_POST["inputMaker"];
    $printer_support = $_POST["inputPrinters"];
    $note = $_POST["inputNote"];
    $memo = $_POST["inputMemo"];
    $green = $_POST["inputGreen"]==null? 0:1;
    $invalid = $_POST["insertInvalid"]==null? 0:1;
    $update_date = date('Y-m-d');
    $update_user = $_SESSION["loginUserId"];
}

$conn = getConnection();
if ($conn->connect_error) 
{
    die("Failed to connect to database. " . $conn->connect_error);
}

$stmt = $conn->prepare("update commodity set name=?,cd=?,print_type=?,num=?,price=?,maker=?,green=?,printer_support=?,note=?,memo=?,invalid=?,upday=?,upuser=? where no=?");
$stmt->bind_param('ssiississsisii',$name,$code,$print_type,$num,$price,$maker,$green,$printer_support,$note,$memo,$invalid,$insert_date,$insert_user,$no);

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