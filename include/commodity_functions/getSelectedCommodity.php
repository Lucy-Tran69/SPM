<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id']))
{
    $commodity = getDetails($_POST['id']);

    if($commodity)
    {   
       echo json_encode(array(
            "status"=>"success",
            "name"=>$commodity["name"],
            "no"=>$commodity["no"],
            "cd"=>$commodity["cd"],
            "print_type"=>$commodity["print_type"],
            "maker"=>$commodity["maker"],
            "price"=>$commodity["price"],
            "invalid"=>$commodity["invalid"],
            "num"=>$commodity["num"],
            "green"=>$commodity["green"],
            "printer_support"=>$commodity["printer_support"],
            "note"=>$commodity["note"],
            "memo"=>$commodity["memo"],
        ));
    }
    else
    {
       echo json_encode(array("status"=>"fail"));
    }
}
function getDetails($id)
{
    $conn  = getConnection();
    if ($conn->connect_error) 
    {
        die("Failed to connect to database. " . $conn->connect_error);
    }

    $stmt = $conn->prepare("select commodity.no,
                                   commodity.cd,
                                   commodity.name,
                                   commodity.print_type,
                                   commodity.num,
                                   commodity.price,
                                   commodity.maker,
                                   commodity.green,
                                   commodity.printer_support,
                                   commodity.note,
                                   commodity.memo,
                                   commodity.invalid
                                   from commodity
                                   where commodity.no=?                                 
                                    ");
    $stmt->bind_param('i',$id);
    $result = execute($stmt,$conn);

    if($result==TRUE)
        $result = $stmt->get_result();

    if($result->num_rows==1)
    {
        $row = $result->fetch_assoc();
        return $row;
    }
    else
    {
        return null;
    }

}
