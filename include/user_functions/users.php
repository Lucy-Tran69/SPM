<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
include_once "database/db.inc";
$err_msg = "";
$conn  = getConnection();
if($conn->connect_error)
{
    die("Failed to connect to database. ".$conn->connect_error);
}
$searchQuery = "";
$searchCust = "";
$searchName = "";
$searchRole = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = isset($_POST["selectedRole"])?$_POST["selectedRole"]:0;
    $cust = isset($_POST["searchCustomer"])?$_POST["searchCustomer"]:null;
    $name = isset($_POST["searchName"])?$_POST["searchName"]:null;
    $invalid = isset($_POST["searchInvalid"])?1:0;
   // echo json_encode ($status);die();
    $searchQuery = "";
    if(!empty($cust)){
        $searchCust = " customer.name like '%$cust%' ";
    }
    if(!empty($name)){
        $searchName = " users.name like '%$name%' ";
    }
    if(!empty($role)){
        $searchRole = " users.role = ".$role;
    }
     if($invalid === 0) {
        $searchInvalid = " users.invalid = ".$invalid;
    }
    
   
    if (!empty($searchCust) || !empty($searchName) || !empty($searchRole) || !empty($searchInvalid)) {
        if (!empty($searchCust))
        {
            if(!empty($searchQuery))
            {
                $searchQuery = $searchQuery." and ";
            }
            $searchQuery = $searchQuery.$searchCust ;
        }
        if (!empty($searchName))
        {
            if(!empty($searchQuery))
            {
                $searchQuery = $searchQuery." and ";
            }
            $searchQuery = $searchQuery.$searchName;
        }
        if (!empty($searchRole))
        {
            if(!empty($searchQuery))
            {
                $searchQuery = $searchQuery." and ";
            }
            $searchQuery = $searchQuery.$searchRole;
        }
        if (!empty($searchInvalid))
        {
            if(!empty($searchQuery))
            {
                $searchQuery = $searchQuery." and ";
            }
            $searchQuery = $searchQuery.$searchInvalid;
        }
        $searchQuery = "where ".$searchQuery;
    }
}
$stmt;
$stmt = $conn->prepare("select users.no as no,
                               users.name as name ,
                               account,
                               role.name as role,
                               effective_st_day,
                               effective_en_day,
                               customer.name as cname,
                               users.invalid as invalid
                               from users 
                               inner join role on users.role=role.no
                               left join customer on users.customer=customer.no ".$searchQuery."
                               ORDER BY users.name ASC");

$result = execute($stmt,$conn);
if($result==TRUE)
{
    $result = $stmt->store_result();
    $resultSet = $result;
}

$roleStmt = $conn->prepare("select no,name from role where invalid=0 ORDER BY sort_order ASC");
$roleResult = execute($roleStmt,$conn);
if($roleResult==TRUE)
{
    $roleResult=$roleStmt->store_result();
    $roleResultSet = $roleResult;
}

$custStmt = $conn->prepare("select no,name from customer where invalid=0 ORDER BY cd ASC");
$custResult = execute($custStmt,$conn);
if($custResult==TRUE)
{
    $custResult=$custStmt->store_result();
    $custResultSet = $custResult;
}

$officeStmt = $conn->prepare("select no,name from office ORDER BY no ASC");
$officeResult = execute($officeStmt,$conn);
if($officeResult==TRUE)
{
    $officeResult=$officeStmt->store_result();
    $officeResultSet = $officeResult;
}

?>?