<?php
//incude this file on the header of each file
//OR just include the header.inc file which has already included this file

include_once "const/system.inc";

//check if not login and current url is not login url
if (!isset($_SESSION[AUTH]) && strpos($_SERVER['REQUEST_URI'], "login/") === false) {
    
    //redirect to login page
    header("Location: ../");
}
// if logged in and request uri is login
elseif (isset($_SESSION[AUTH]) && strpos($_SERVER['REQUEST_URI'], "login/") !== false) {
    
    //for example redirect to home
    header("Location: ../home/"); 
}

?>