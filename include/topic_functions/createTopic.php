<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    include_once "database/db.inc";

    $err_msg = "";
    $conn  = getConnection();

    if($conn->connect_error) {
        die("Failed to connect to database. ".$conn->connect_error);
    }

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
	{
	   // $title = $_POST["title"];
		echo 'Success';
	}


$stmt = $conn->prepare("");
$stmt->bind_param();

$result = execute($stmt,$conn);
if($result)
{
    header("Location: ../../app/topic/topics.html");  
}
else
{
    echo "SQL Error";
}
?>