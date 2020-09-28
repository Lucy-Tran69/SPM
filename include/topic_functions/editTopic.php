<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

include_once "../../include/database/db.inc";
include_once "../../include/template/FlashMessages.php";

$conn  = getConnection();
$msg = new \Plasticbrain\FlashMessages\FlashMessages();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION["loginAccount"])) {
	$no = isset($_POST["no"]) ? (int)$_POST["no"] : '';
	$title = isset($_POST["title"]) ? strip_tags($_POST["title"]) : '';
	$body = isset($_POST["body"]) ? strip_tags($_POST["body"]) : '';
	$imgFileOld = isset($_POST["imgFileOld"]) ? $_POST["imgFileOld"] : '';
	$imgFileNew = isset($_FILES["imgFile"]["name"]) ? $_FILES["imgFile"]["name"] : '';
	$titleLink = isset($_POST["titleLink"]) ? strip_tags($_POST["titleLink"]) : '';
	$urlImage = isset($_POST["urlImage"]) ? remove_special_character($_POST["urlImage"]) : '';
	$imageLink = isset($_POST["imgLink"]) ? remove_special_character($_POST["imgLink"]) : null;
	$openDay = isset($_POST["openDay"]) ? $_POST["openDay"] : date('Y-m-d h:i:s');;
	$closeDay = isset($_POST["closeDay"]) ? $_POST["closeDay"] : NULL;

	$insert_user = $_SESSION["loginUserId"];
	$checkOK = 1;
	// var_dump($imgFileNew);
	// die();
	if (empty($title) || mb_strlen(mb_convert_encoding($title, "UTF-8")) > 512) {
		$msg->error('Title error!');
		$checkOK = 0;
	}

	if (empty($body) || mb_strlen(mb_convert_encoding($body, "UTF-8")) > 512) {
		$msg->error('Body error');
		$checkOK = 0;
	}

	if (empty($imgFileOld) && empty($imgFileNew)) {
		$msg->error('Please select file');
		$checkOK = 0;
	}

	if (!empty($closeDay)) {
		if (strtotime($openDay) > strtotime($closeDay)) {
			$msg->error('Please select open day is less than or equal to close day');
			$checkOK = 0;
		}
	} else {
		$closeDay = NULL;
	}

	if (mb_strlen(mb_convert_encoding($titleLink, "UTF-8")) > 512) {
		$msg->error('Title no more than 1024!');
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($urlImage, "UTF-8")) > 512) {
		$msg->error('Link URL no more than 1024!');
		$checkOK = 0;
	}

	if(!empty($imgFileNew)){
		//upload image
		$target_dir = "../../app/images/topics/";
		$target_file = $target_dir . basename($imgFileNew);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

	// Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["imgFile"]["tmp_name"]);
		if ($check !== false) {
			$uploadOk = 1;
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
		
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk === 0) {
			$msg->error('Sorry, your file was not uploaded.');
			// if everything is ok, try to upload file
		} else {
			if (!move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
				$msg->error('Sorry, there was an error uploading your file.');
			}
		}
	}

	if ($checkOK === 1) {
		$image =  !empty($imgFileNew) ? $imgFileNew : $imgFileOld;

		$sql = "UPDATE topics SET title=?, body=?, opday=?, clday=?, image=?, image_link=?, link_title=?, link_url=?, inuser=? WHERE no=?";
		if($stmt = mysqli_prepare($conn, $sql)){
			mysqli_stmt_bind_param($stmt, 'sssssssssi', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user, $no);

			// Cố gắng thực thi câu lệnh đã chuẩn bị
            if(mysqli_stmt_execute($stmt)){
                // Update thành công. Chuyển hướng đến trang đích
                header("Location: ../../app/topic/topics.html?status=success&title=$title");
				exit();
            } else{
                echo "Error " . mysqli_error($conn);exit();
				header("Location: ../../app/topic/topics.html?status=faill&title=$title");
            }
		}
		// $stmt->bind_param('sssssssssi', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user, $no);
		// $stmt->execute();

		// if ($stmt->affected_rows > 0) {
		// 	header("Location: ../../app/topic/topics.html?status=success&title=$title");
		// 	exit();
		// } else {
		// 	echo "Error " . mysqli_error($conn);exit();
		// 	header("Location: ../../app/topic/topics.html?status=faill&title=$title");
			
		// }
		$stmt->close();
	}
	$msg->display();
}

function remove_special_character($string) {
	$t = $string;

	$specChars = array(
		' ' => '-', '!' => '', '"' => '',
		'#' => '', '$' => '', '&amp;' => '', 
		'\'' => '', '(' => '',
		')' => '', '*' => '',
		',' => '', '₹' => '', ';' => '',
		'<' => '', '>' => '',
		'@' => '', '[' => '',
		'\\' => '', ']' => '', '^' => '',
		 '`' => '', '{' => '',
		'|' => '', '}' => '', '~' => '',

	);

	foreach ($specChars as $k => $v) {
		$t = str_replace($k, $v, $t);
	}

	return $t;
}
