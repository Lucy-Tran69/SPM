<?php
include_once "common/session.php";
$ssmsg = array();
$errmsg = array();

if (isset($_POST["email"]) && isset($_POST["pass"]) && $_POST["email"]!= "" && $_POST["pass"]!="" ) {
    array_push($ssmsg, "success");
    
    $_SESSION["success_msg"]=$ssmsg;
    //header("location: test.html");
} else {
    array_push($errmsg, "error");
    $_SESSION["err_msg"]= $errmsg;
    //header("location: form.html");
}

include_once "html/showmessage.inc";



?>