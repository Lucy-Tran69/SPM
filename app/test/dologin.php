<?php  include_once "common/session.php"; ?>
<?php

if (isset($_SESSION["er_msg"])) {
    unset($_SESSION["er_msg"]);
}

if ($_POST["username"] && $_POST["pass"]) {
    die($_POST["username"].$_POST["pass"]);
} else {
    $_SESSION["er_msg"] = "ユーザー名又はパスワードが間違っています";
    header("Location: ../test/login.html"); 
    
}



?>