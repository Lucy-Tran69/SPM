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

function fetchAssocStatement($stmt)
{
    if($stmt->num_rows>0)
    {
        $result = array();
        $md = $stmt->result_metadata();
        $params = array();
        while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        if($stmt->fetch())
            return $result;
    }

    return null;
}
?>

