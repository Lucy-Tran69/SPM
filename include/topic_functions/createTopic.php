<?php ob_start();
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

include_once "../../include/database/db.inc";
include_once "../../include/template/FlashMessages.php";

$err_msg = "";
$conn  = getConnection();
$msg = new \Plasticbrain\FlashMessages\FlashMessages();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION["loginAccount"])) {
	$title = isset($_POST["title"]) ? $_POST["title"] : '';
	$body = isset($_POST["body"]) ? $_POST["body"] : '';
	$file = isset($_FILES["imgFile"]["name"]) ? $_FILES["imgFile"]["name"] : '';
	$titleLink = isset($_POST["titleLink"]) ? $_POST["titleLink"] : '';
	$urlImage = isset($_POST["urlImage"]) ? $_POST["urlImage"] : '';
	$openDay = isset($_POST["openDay"]) ? $_POST["openDay"] : date('Y-m-d h:i:s');;
	$closeDay = isset($_POST["closeDay"]) ? $_POST["closeDay"] : null;
	$checkOK = 1;

	if (empty($title) || mb_strlen($title, 'Shift-JIS') > 1024) {
		$msg->error('Title error!');
		$checkOK = 0;
	}

	if (empty($body) || mb_strlen($body, 'Shift-JIS') > 60000) {
		$msg->error('Body error');
		$checkOK = 0;
	}

	if (empty($file)) {
		$msg->error('Please select file');
		$checkOK = 0;
	}

	// var_dump($closeDay); die();
	if (!empty($closeDay)) {
		if (strtotime($openDay) > strtotime($closeDay)) {
			$msg->error('Please select open day is less than or equal to close day');
			$checkOK = 0;
		}
	}

	if (mb_strlen($titleLink, 'Shift-JIS') > 1024) {
		$msg->error('Title no more than 1024!');
		$checkOK = 0;
	}

	if (mb_strlen($urlImage, 'Shift-JIS') > 1024) {
		$msg->error('Link URL no more than 1024!');
		$checkOK = 0;
	}

	//upload image
	$target_dir = "../../app/images/topics/";
	$target_file = $target_dir . basename($file);
	// print_r($target_file); die();
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

	// Check if image file is a actual image or fake image
	$check = getimagesize($_FILES["imgFile"]["tmp_name"]);
	if ($check !== false) {
		$uploadOk = 1;
		$checkOK = 1;
	} else {
		$msg->error('File is not an image.');
		$uploadOk = 0;
		$checkOK = 0;
	}

	// Check if file already exists
	if (file_exists($target_file)) {
		$msg->error('Sorry, file already exists.');
		$uploadOk = 0;
		$checkOK = 0;
	}

	// Check file size
	if ($_FILES["imgFile"]["size"] > 102400000) {
		$msg->error('Sorry, your file is too large.');
		$uploadOk = 0;
		$checkOK = 0;
	}

	// Allow certain file formats
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
		$msg->error('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
		$uploadOk = 0;
		$checkOK = 0;
	}

	//check URL invalid

	if ($checkOK == 1) {
		$title = strip_tags($title);
		$body = strip_tags($body);
		$image = $file;
		$imageLink = isset($_POST["imgLink"]) ? remove_special_character($_POST["imgLink"]) : null;
		$titleLink = isset($titleLink) ? strip_tags($titleLink) : null;
		$urlImage = isset($urlImage) ? remove_special_character($urlImage) : null;
		$insert_user = $_SESSION["loginUserId"];

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$msg->error('Sorry, your file was not uploaded.');
			// if everything is ok, try to upload file
		} else {
			if (!move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
				$msg->error('Sorry, there was an error uploading your file.');
			}
		}

		$stmt = $conn->prepare("INSERT INTO topics(title, body, opday, clday, image, image_link, link_title, link_url, inuser) 
			VALUES(?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('sssssssss', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user);

		$stmt->execute();

		if ($stmt->affected_rows > 0) {
			header("Location: ../../app/topic/topics.html");
			exit();
		} else {
			echo "Error " . mysqli_error($conn);
			header("Location: ../../app/topic/topics.html");
			exit();
		}
		$stmt->close();
	}
	$msg->display();
	exit;
	ob_end_clean();
}

function remove_special_character($string)
{

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
