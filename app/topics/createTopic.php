<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION["loginAccount"])) {
	$title = isset($_POST["title"]) ? strip_tags($_POST["title"]) : '';
	$body = isset($_POST["body"]) ? strip_tags($_POST["body"]) : '';
	$file = isset($_FILES["imgFile"]["name"]) ? $_FILES["imgFile"]["name"] : '';
	$titleLink = isset($_POST["titleLink"]) ? strip_tags($_POST["titleLink"]) : '';
	$urlImage = isset($_POST["urlImage"]) ? remove_special_character($_POST["urlImage"]) : '';
	$imageLink = isset($_POST["imgLink"]) ? remove_special_character($_POST["imgLink"]) : null;
	$openDay = isset($_POST["openDay"]) ? $_POST["openDay"] : date('Y-m-d h:i:s');;
	$closeDay = isset($_POST["closeDay"]) ? $_POST["closeDay"] : NULL;

	$insert_user = $_SESSION["loginUserId"];

	$checkOK = 1;

	if (empty($title)) {
		$_SESSION["err_msg"]= array("タイトルをご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($title, "UTF-8")) > 512) {
		$_SESSION["err_msg"]= array("タイトルは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (empty($body)) {
		$_SESSION["err_msg"]= array("本文をご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($body, "UTF-8")) > 30000) {
		$_SESSION["err_msg"]= array("本文は60000文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (empty($file)) {
		$_SESSION["err_msg"]= array("タアップロードファイルをご指定ください。");
		$checkOK = 0;
	}

	if (!empty($closeDay)) {
		if (strtotime($openDay) > strtotime($closeDay)) {
			$_SESSION["err_msg"]= array("公開日は終了日より未来の日付を指定してください。");
			$checkOK = 0;
		}
	} else {
		$closeDay = NULL;
	}

	if (mb_strlen(mb_convert_encoding($titleLink, "UTF-8")) > 512) {
		$_SESSION["err_msg"]= array("タイトルのリンクは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($urlImage, "UTF-8")) > 512) {
		$_SESSION["err_msg"]= array("URLのリンクは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (!empty($file)) {
		//upload image
		$target_dir = "../../app/refer/images/topics/";
		$target_file = $target_dir . basename($file);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

	// Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["imgFile"]["tmp_name"]);
		if ($check !== false) {
			$uploadOk = 1;
		} else {
			$_SESSION["err_msg"]= array("ファイル形式は画像形式ではありません。もう一度お試しください。");
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Check if file already exists
		if (file_exists($target_file)) {
			$_SESSION["err_msg"]= array("このファイルが既に存在しています。");
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Check file size
		if ($_FILES["imgFile"]["size"] > 102400000) {
			$_SESSION["err_msg"]= array("アップロードされたファイルサイズは100MBを超えています。100MB以下にしてください。");
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Allow certain file formats
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
			$_SESSION["err_msg"]= array("アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。");
			$uploadOk = 0;
			$checkOK = 0;
		}
	}
	
	if ($checkOK == 1) {
		$image = $file;

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$_SESSION["err_msg"]= array("指定されたファイルはアップロードできません。");
			// if everything is ok, try to upload file
		} else {
			if (!move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
				$_SESSION["err_msg"]= array("アップロードでエラーが発生しました。もう一度お試しください。");
			}
		}

		$stmt = $conn->prepare("INSERT INTO topics(title, body, opday, clday, image, image_link, link_title, link_url, inuser) 
			VALUES(?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('sssssssss', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user);

		$stmt->execute();

		if ($stmt->affected_rows > 0) {
			$_SESSION["success_msg"]=array($title.'トピックスの追加に成功しました。');
			header("Location: index.html");
			exit();
		} else {
			$_SESSION["err_msg"]= array("Error " . mysqli_error($conn));
			header("Location: index.html");
			exit();
		}
		
		$stmt->close();
	}
	else {
		header("Location: add-topic.html");
		exit();
	}
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
