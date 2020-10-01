<?php
if (session_status() == PHP_SESSION_NONE) 
{
	session_start();
}
include_once "../../include/database/db.inc";
include_once "../../include/template/FlashMessages.php";

$err_msg = "";
$conn  = getConnection();
$msg = new \Plasticbrain\FlashMessages\FlashMessages();

if($conn->connect_error) {
	die("Failed to connect to database. ".$conn->connect_error);
}

?>