<?php

define("DBUSER","root");
define("DBPASS","");
define("DBSERVER","localhost");
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

