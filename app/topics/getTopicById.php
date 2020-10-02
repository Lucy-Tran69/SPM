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
        $sql = "SELECT no, title, opday, clday, body, image, image_link, link_title, link_url FROM topics WHERE no='$id'";
        $getTopic = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($getTopic) > 0) {
            $row = mysqli_fetch_assoc($getTopic); 
        }
    }
}
?>