<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $name = $_POST["insertName"];
    $code = $_POST["insertCode"];
    $print_type = $_POST["insertType"];
    $num = $_POST["insertPaid"]==null?0:$_POST["insertPaid"];
    $price = $_POST["insertPrice"];
    $maker = $_POST["insertMaker"];
    $printer_support = $_POST["insertPrinters"];
    $note = $_POST["insertNote"];
    $memo = $_POST["insertMemo"];
    $green = $_POST["insertGreen"]=='true'? 1:0;
    $invalid = $_POST["insertInvalid"]=='true'? 1:0;
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
    echo json_encode(array(
        "status"=>"success"
    ));  
}
else
{
    echo json_encode(array(
        "status"=>"fail"
    ));
}

?>