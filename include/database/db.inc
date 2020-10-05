<?php

define("DBUSER","nci_ims");
define("DBPASS","XLBphSrbPqNQ7uVg");
define("DBSERVER","192.168.111.98");
define("DBNAME","nci_ims");

//DB Connection Functions

function getConnection()
{
    $conn = mysqli_connect(DBSERVER,DBUSER,DBPASS,DBNAME);
    mysqli_set_charset($conn,"utf8");
    return $conn;
}

function freeConnection($conn)
{
    mysqli_close($conn);
}

function load()
{

}

function execute($statement,$conn)
{
    if($statement->execute()==TRUE)
    {
        return TRUE;
    }
    else
    {
        $conn->rollback();
        throw new Exception($conn->error);
        return FALSE;
    }
}
?>

