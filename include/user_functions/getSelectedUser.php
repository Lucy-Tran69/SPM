<?php
if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
}
header("content-type: text/html; charset=UTF-8");
include_once "database/db.inc";

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['id']))
{
    $user = getDetails($_POST['id']);

    if($user)
    {   
       echo json_encode(array(
            "status"=>"success",
            "name"=>$user["name"],
            "account"=>$user["account"],
            "role"=>$user["role"],
            "expire"=>$user["effective_en_day"],
            "start"=>$user["effective_st_day"],
            "customer"=>$user["cust"],
            "office"=>$user["office"],
            "invalid"=>$user["invalid"],
            "email"=>$user["email"]
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

    $stmt = $conn->prepare("select users.no as no,
                            users.name as name,
                            account,
                            role.no as role,
                            effective_st_day,
                            effective_en_day,
                            users.email as email,
                            users.customer as cust,
                            users.office as office,
                            users.invalid as invalid
                            from users inner join role on users.role=role.no where users.no=?");
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
