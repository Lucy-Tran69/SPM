<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

if ($conn->connect_error) {
    die("Failed to connect to database. " . $conn->connect_error);
}
if (isset($_SESSION["loginAccount"])) {
    if(isset($_GET["id"]) && !empty(trim($_GET["id"])) && is_numeric($_GET['id'])){

        $id = $_GET['id'];
        $sql = "select no, title, opday, clday, body, image, image_link, link_title, link_url from topics where no=?";
        $stm = $conn->prepare($sql);
        $stm->bind_param('i', $id);
        $stm->execute();
        $stm->store_result();

        if($stm->num_rows>0)
        {
            $row = fetchAssocStatement($stm);
        }
    }
    $conn->close();
}
?>

