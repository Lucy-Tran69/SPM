<?php
//incude this file on the header of each file
//OR just include the header.inc file which has already included this file

session_start();

include_once "const/system.inc";

//check if not login and current url is not login url
if (!isset($_SESSION[AUTH]) && strpos($_SERVER['REQUEST_URI'], "login.html") === false) {
    
    //redirect to login page
    header("Location: ../test/login.html");
}
// if logged in and request uri is login
elseif (strpos($_SERVER['REQUEST_URI'], "login.html") === true) {
    
    //for example redirect to home
    header("Location: ../test/test.html"); 
}

?>