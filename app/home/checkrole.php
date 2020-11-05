<?php
include_once "common/session.php";
include_once "database/db.inc";

function CheckRole()
{
    $conn  = getConnection();
    
    $sql = "select count(*) as cnt from users a
            inner join role b ON a.role = b.no
            where a.no = ".$_SESSION["loginUserId"]."  and b.invalid = 0";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $stmt->store_result();

    $cnt = 0;
    if(!$stmt->error) {
        while ($row = fetchAssocStatement($stmt))
        {
            $cnt = $row["cnt"];
        }
    }        
    $conn->close();

    return $cnt;
}


?>