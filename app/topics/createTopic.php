<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
		$msg->error("タイトルをご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($title, "UTF-8")) > 512) {
		$msg->error("タイトルは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (empty($body)) {
		$msg->error('本文をご入力ください。');
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($body, "UTF-8")) > 30000) {
		$msg->error('本文は60000文字以内でご入力ください。');
		$checkOK = 0;
	}

	if (empty($file)) {
		$msg->error('タアップロードファイルをご指定ください。');
		$checkOK = 0;
	}

	if (!empty($closeDay)) {
		if (strtotime($openDay) > strtotime($closeDay)) {
			$msg->error('公開日は終了日より未来の日付を指定してください。');
			$checkOK = 0;
		}
	} else {
		$closeDay = NULL;
	}

	if (mb_strlen(mb_convert_encoding($titleLink, "UTF-8")) > 512) {
		$msg->error('タイトルのリンクは1024文字以内でご入力ください。');
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($urlImage, "UTF-8")) > 512) {
		$msg->error('URLのリンクは1024文字以内でご入力ください。');
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
			$msg->error('ファイル形式は画像形式ではありません。もう一度お試しください。');
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Check if file already exists
		if (file_exists($target_file)) {
			$msg->error('このファイルが既に存在しています。');
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Check file size
		if ($_FILES["imgFile"]["size"] > 102400000) {
			$msg->error('アップロードされたファイルサイズは100MBを超えています。100MB以下にしてください。');
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Allow certain file formats
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
			$msg->error('アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。');
			$uploadOk = 0;
			$checkOK = 0;
		}
	}
	
	if ($checkOK == 1) {
		$image = $file;

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$msg->error('指定されたファイルはアップロードできません。');
			// if everything is ok, try to upload file
		} else {
			if (!move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
				$msg->error('アップロードでエラーが発生しました。もう一度お試しください。');
			}
		}


		$sql = "INSERT INTO topics(title, body, opday, clday, image, image_link, link_title, link_url, inuser) 
			VALUES(?,?,?,?,?,?,?,?,?)";
		$stmt = $conn->prepare($sql);

		$stmt->bind_param('ssssssssi', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user);

		$stmt->execute();
		$msg->success($title.'トピックスの追加に成功しました。');
		$msg->display();	

		// if ($stmt->error) {
		// 	$msg->error($title.'トピックスの追加に失敗しました。');
		// 	$msg->display();
		// } else {
		// 	$msg->success($title.'トピックスの追加に成功しました。');
		// }
		
		$stmt->close();
	}
	else {
		$msg->display();
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
