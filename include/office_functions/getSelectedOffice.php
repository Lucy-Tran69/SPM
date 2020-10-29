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
            "tel"=>$user["tel"],
            "zip"=>$user["zip"],
            "address"=>$user["address"],
            "title"=>$user["title"],
            "fax"=>$user["fax"]
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

    $stmt = $conn->prepare("select office.no,
                                    office.name,
                                    office.tel,
                                    office.zip,
                                    office.address,
                                    office.title,
                                    office.fax
                                    from office where office.no=?");
    $stmt->bind_param('i',$id);
    $result = execute($stmt,$conn);

    if($result==TRUE)
        $result = $stmt->store_result();

    if($stmt->num_rows==1)
    {
        $row = fetchAssocStatement($stmt);
        return $row;
    }
    else
    {
        return null;
    }

}
