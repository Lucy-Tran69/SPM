<?php
include_once "common/session.php";
include_once "database/db.inc";

define('UPLOAD_DIR', '../../app/refer/images/topics/');

$conn  = getConnection();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$title = isset($_POST["title"]) ? strip_tags($_POST["title"]) : '';
	$body = isset($_POST["body"]) ? strip_tags($_POST["body"]) : '';
	$fileName = isset($_FILES["imgFile"]) ? $_FILES["imgFile"] : '';
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

    $uploadOk = 1;
	if (!empty($fileName['name'])) {
		$imageFileType = strtolower(pathinfo($fileName['name'], PATHINFO_EXTENSION));

	// Check file size
		if ($fileName["size"] > 102400000) {
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

		// if ($uploadOk == 0) {
		// 	$msg->error('指定されたファイルはアップロードできません。');
		// 	$checkOK = 0;
		// }
	
	if ($checkOK == 1) {

		$sql = "INSERT INTO topics(title, body, opday, clday, image, image_link, link_title, link_url, inuser) 
			VALUES(?,?,?,?,?,?,?,?,?)";
		$stmt = $conn->prepare($sql);

		$stmt->bind_param('ssssssssi', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user);

		$stmt->execute();

		$lastId = mysqli_insert_id($conn);

		if ($lastId != null && !($stmt->error) && $uploadOk == 1 && !empty($fileName)) {
				$image = $fileName['name'];

				// Check if $uploadOk is set to 0 by an error
				$image = update_file_name($fileName['name'], $lastId);

				$insertImage = "UPDATE topics SET image=? WHERE no=?";
				$stm = $conn->prepare($insertImage);

				$stm->bind_param('si', $image, $lastId);

				$stm->execute();

				if (!($stm->error)) {
					if (!move_uploaded_file($fileName['tmp_name'], UPLOAD_DIR.$image)) {
						$msg->error('アップロードでエラーが発生しました。もう一度お試しください。');
					}
				}	
		}

		if ($stmt->error) {
			$msg->error('「'.$title.'」トピックスの追加に失敗しました。');
			$msg->display();
		} else {
			$msg->success('「'.$title.'」トピックスの追加に成功しました。');
		}
		
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

// function to rename file
function update_file_name($file, $no)  {
	$pos = strrpos($file,'.');
	$ext = substr($file,$pos); 
	$dir = strrpos($file,'/');
	$dr  = substr($file,0,($dir+1)); 

	$arr = explode('/',$file);
	$fName = trim($arr[(count($arr) - 1)],$ext);

	$exist = FALSE;
	$idFile = 'topic'.$no;
	while(!$exist){
		if(file_exists($idFile)||file_exists($file)){
			continue;
		} else {
			$exist = TRUE;
			$file = $idFile.$ext;
		}
	}

	return $file;
}
