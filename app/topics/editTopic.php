<?php
include_once "common/session.php";
include_once "database/db.inc";

/**
 * This is used to edit a topic.
 *
 * Check input condition, if invalid then error.
 *
 * If valid, update to database.
 */

define('UPLOAD_DIR', '../../app/refer/images/topics/');

$conn  = getConnection();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$no = isset($_POST["no"]) ? (int)$_POST["no"] : '';
	$title = isset($_POST["title"]) ? trim(strip_tags($_POST["title"])) : '';
	$body = isset($_POST["body"]) ? trim(strip_tags($_POST["body"])) : '';
	$imgFileOld = isset($_POST["imgFileOld"]) ? $_POST["imgFileOld"] : '';
	$imgFileNew = isset($_FILES["imgFile"]) ? $_FILES["imgFile"] : '';
	$titleLink = isset($_POST["titleLink"]) ? trim(strip_tags($_POST["titleLink"])) : '';
	$urlImage = isset($_POST["urlImage"]) ? trim(remove_special_character($_POST["urlImage"])) : '';
	$imageLink = isset($_POST["imgLink"]) ? trim(remove_special_character($_POST["imgLink"])) : null;
	$openDay = isset($_POST["openDay"]) ? $_POST["openDay"] : date('Y-m-d h:i:s');;
	$closeDay = isset($_POST["closeDay"]) ? $_POST["closeDay"] : NULL;
	$statusImage = isset($_POST["statusImage"]) ? $_POST["statusImage"] : '';
	$returnURL = isset($_POST["returnURL"]) ? $_POST["returnURL"] : '';

	$insert_user = $_SESSION["loginUserId"];
	$checkOK = 1;

	if (empty($title)) {
		$msg->error('タイトルをご入力ください。');
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($title, "UTF-8")) > 512) {
		$msg->error('タイトルは1024文字以内でご入力ください。');
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
			array_push($errmsg, "公開日は終了日より未来の日付を指定してください。");
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

	if(!empty($imgFileNew['name'])){
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($imgFileNew['name'], PATHINFO_EXTENSION));

	// Check file size
		if ($imgFileNew["size"] > 102400000) {
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
			$image = $imgFileOld;
			$filePath = UPLOAD_DIR . basename($image);
			//check delete image
			if ($statusImage == 'true' && !empty($image)) {
				if (file_exists($filePath)) {
						unlink($filePath);
					}
				$image = null;
			}

			if(!empty($imgFileNew['name'])) {
				if ($uploadOk === 0) {
					$msg->error('指定されたファイルはアップロードできません。');
					$checkOK = 0;
				} else {
					if (!empty($image)) {
						if (file_exists($filePath)) {
							unlink($filePath);
						}
					}
				
					$image = $imgFileNew['name'];
					$image = update_file_name($image, $no);
					if (!move_uploaded_file($imgFileNew['tmp_name'], UPLOAD_DIR.$image)) {
						$msg->error('アップロードでエラーが発生しました。もう一度お試しください。');
						$checkOK = 0;
					}
				}
			}
		}

	if ($checkOK === 1) {
		

		$sql = "UPDATE topics SET title=?, body=?, opday=?, clday=?, image=?, image_link=?, link_title=?, link_url=?, inuser=? WHERE no=?";
		$stmt = $conn->prepare($sql);

		$stmt->bind_param('ssssssssii', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user, $no);

		$stmt->execute();	

		if ($stmt->error) {
			$msg->error('「'.$title.'」トピックスの変更に失敗しました。');
			$msg->display();
		}
		else {
			$msg->success('「'.$title.'」トピックスの変更に成功しました。');
		} 
	
		$stmt->close();
	}
	else {
		$msg->display();
	}

	$conn->close();
}

/**
 * This is a function to remove special character for url link.
 */
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

/**
 * This is a function to rename file.
 *
 * New filename is defined as topic + no.
 *
 * return new file name
 */
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
