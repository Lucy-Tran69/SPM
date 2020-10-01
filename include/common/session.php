<?php
//if(session_id() == '' || !isset($_SESSION)) {
if (session_status() != PHP_SESSION_ACTIVE) {    
    // session isn't started
    session_start();
}
?>