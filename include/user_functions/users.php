<?php
include_once "database/db.inc";
$invalid = isset($_POST["searchInvalid"])?$_POST["searchInvalid"]:null;
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}

$stmt;
if($invalid===null)
{
    $stmt = $conn->prepare("select users.no as no,users.name as name ,account,role.name as role,effective_st_day,customer.name as cname,users.invalid as invalid
                                                                                                from users 
                                                                                                inner join role on users.role=role.no
                                                                                                left join customer on users.customer=customer.no where users.invalid=0 
                                                                                                ORDER BY users.name ASC");
}
else
{
    $stmt = $conn->prepare("select users.no as no,users.name as name ,account,role.name as role,effective_st_day,customer.name as cname,users.invalid as invalid
                                                                                                from users 
                                                                                                inner join role on users.role=role.no
                                                                                                left join customer on users.customer=customer.no
                                                                                                ORDER BY users.name ASC");
}

$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->get_result();
    $resultSet = $result;
}

$roleStmt = $conn->prepare("select no,name from role where invalid=0 ORDER BY sort_order ASC");
$roleResult = execute($roleStmt,$conn);
if($roleResult==TRUE)
{
    $roleResult=$roleStmt->get_result();
    $roleResultSet = $roleResult;
}

$custStmt = $conn->prepare("select no,name from customer where invalid=0 ORDER BY cd ASC");
$custResult = execute($custStmt,$conn);
if($custResult==TRUE)
{
    $custResult=$custStmt->get_result();
    $custResultSet = $custResult;
}

$officeStmt = $conn->prepare("select no,name from office ORDER BY name ASC");
$officeResult = execute($officeStmt,$conn);
if($officeResult==TRUE)
{
    $officeResult=$officeStmt->get_result();
    $officeResultSet = $officeResult;
}

?>?