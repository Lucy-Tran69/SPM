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

    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
	{
		if(!isset($_POST["title"]) || mb_strlen($_POST["title"], 'Shift-JIS') > 1024){
			$msg->error('Title error!');
		}

		if(!isset($_POST["body"]) || mb_strlen($_POST["body"], 'Shift-JIS') > 60000){
			$msg->error('Body error');
		}

		if(!isset($_FILES["imgFile"]["name"])){
			$msg->error('Please select file');
		}

		$openDay = isset($_POST["openDay"]) ? $_POST["openDay"] : date('Y-m-d h:i:s');
		if(isset($_POST["closeDay"]) && (strtotime($openDay) > strtotime($_POST["closeDay"]))){
			$msg->error('Please select open day is less than or equal to close day');
		}

		if(mb_strlen($_POST["titleLink"], 'Shift-JIS') > 1024){
			$msg->error('Title no more than 1024!');
		}

		if(mb_strlen($_POST["urlImage"], 'Shift-JIS') > 1024){
			$msg->error('Link URL no more than 1024!');
		}

		//check URL invalid

		$title = strip_tags($_POST["title"]);
		$body = strip_tags($_POST["body"]);
		$closeDay = isset($_POST["closeDay"]) ? $_POST["closeDay"] : '';
		$image = $_FILES["imgFile"]["name"];
		$imageLink = isset($_POST["imgLink"]) ? $_POST["imgLink"] : '';
		$titleLink = isset($_POST["titleLink"]) ? strip_tags($_POST["titleLink"]) : '';
		$urlImage = isset($_POST["urlImage"]) ? $_POST["urlImage"] : '';
		$insert_user = $_SESSION["loginUserId"];

		//upload image
		$target_dir = "../../app/images/";
		$target_file = $target_dir . basename($_FILES["imgFile"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

		// Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["imgFile"]["tmp_name"]);
		if($check !== false) {
			$uploadOk = 1;
		} else {
			$msg->error('File is not an image.');
			$uploadOk = 0;
		}

		// Check if file already exists
		if (file_exists($target_file)) {
		  $msg->error('Sorry, file already exists.');
		  $uploadOk = 0;
		}

		// Check file size
		if ($_FILES["imgFile"]["size"] > 102400000) {
		  $msg->error('Sorry, your file is too large.');
		  $uploadOk = 0;
		}

		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		  $msg->error('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
		  $uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		  $msg->error('Sorry, your file was not uploaded.');
		// if everything is ok, try to upload file
		} else {
		  if (move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
		    $msg->error('The file ". basename( $_FILES["imgFile"]["name"]). " has been uploaded.');
		  } else {
		    $msg->error('Sorry, there was an error uploading your file.');
		  }
		}

		$stmt = $conn->prepare("insert into topics(title, body, opday, clday, image, image_link, link_title, link_url, inuser) 
                        values(?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('ssissiiissi',$title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user);

		$result = execute($stmt,$conn);
		if($result)
		{
			header("Location: ../../app/topic/topics.html");  
		}
		else
		{
			$msg->error('SQL Error.');
		}

		$msg->display();
		$conn->close();
	}



?>

<?php 
function remove_special_character($string) {

	$t = $string;

	$specChars = array(
		' ' => '-', '!' => '', '"' => '',
		'#' => '', '$' => '', '%' => '',
		'&amp;' => '', '\'' => '', '(' => '',
		')' => '', '*' => '', '+' => '',
		',' => '', '₹' => '', '.' => '',
		'/-' => '', ':' => '', ';' => '',
		'<' => '', '=' => '', '>' => '',
		'?' => '', '@' => '', '[' => '',
		'\\' => '', ']' => '', '^' => '',
		'_' => '', '`' => '', '{' => '',
		'|' => '', '}' => '', '~' => '',
		'-----' => '-', '----' => '-', '---' => '-',
		'/' => '', '--' => '-', '/_' => '-',

	);

	foreach ($specChars as $k => $v) {
		$t = str_replace($k, $v, $t);
	}

	return $t;
}
?>