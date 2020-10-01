<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();
$errmsg = array();
$ssmsg = array();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$fileImage = $_POST["imgFile"];
	print_r($fileImage); die();
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

	if (empty($title)) {
		array_push($errmsg, "タイトルをご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($title, "UTF-8")) > 512) {
		array_push($errmsg, "タイトルは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (empty($body)) {
		array_push($errmsg, "本文をご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($body, "UTF-8")) > 30000) {
		array_push($errmsg, "本文は60000文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (empty($imgFileOld) && empty($imgFileNew)) {
		array_push($errmsg, "アップロードファイルをご指定ください。");
		$checkOK = 0;
	}

	if (!empty($closeDay)) {
		if (strtotime($openDay) > strtotime($closeDay)) {
			array_push($errmsg, "公開日は終了日より未来の日付を指定してください。");
			$checkOK = 0;
		}
	} else {
		$closeDay = NULL;
	}

	if (mb_strlen(mb_convert_encoding($titleLink, "UTF-8")) > 512) {
		array_push($errmsg, "タイトルのリンクは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if (mb_strlen(mb_convert_encoding($urlImage, "UTF-8")) > 512) {
		array_push($errmsg, "URLのリンクは1024文字以内でご入力ください。");
		$checkOK = 0;
	}

	if(!empty($imgFileNew)){
		//upload image
		$target_dir = "../../app/refer/images/topics/";
		$target_file = $target_dir . basename($imgFileNew);
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

	// Check if image file is a actual image or fake image
		$check = getimagesize($_FILES["imgFile"]["tmp_name"]);
		if ($check !== false) {
			$uploadOk = 1;
		} else {
			array_push($errmsg, "ファイル形式は画像形式ではありません。もう一度お試しください。");
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Check if file already exists
		if (file_exists($target_file)) {
 			array_push($errmsg, "このファイルが既に存在しています。");
    
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Check file size
		if ($_FILES["imgFile"]["size"] > 102400000) {
			array_push($errmsg, "アップロードされたファイルサイズは100MBを超えています。100MB以下にしてください。");
			$uploadOk = 0;
			$checkOK = 0;
		}

	// Allow certain file formats
		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
			array_push($errmsg, "アップロードできる画像形式はJPG、JPEG、PNG、GIFのみご入力ください。");
			$uploadOk = 0;
			$checkOK = 0;
		}
	}
	// print_r($msg->error()); die();

	if ($checkOK === 1) {
		$image = $imgFileOld;
		
		if(!empty($imgFileNew)) {
			$image = $imgFileNew;
			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk === 0) {
				array_push($errmsg, "指定されたファイルはアップロードできません。");
				// if everything is ok, try to upload file
			} else {
				if (!move_uploaded_file($_FILES["imgFile"]["tmp_name"], $target_file)) {
					array_push($errmsg, "アップロードでエラーが発生しました。もう一度お試しください。");
				}
			}
		}

		$sql = "UPDATE topics SET title=?, body=?, opday=?, clday=?, image=?, image_link=?, link_title=?, link_url=?, inuser=? WHERE no=?";
		if($stmt = mysqli_prepare($conn, $sql)){
			mysqli_stmt_bind_param($stmt, 'sssssssssi', $title, $body, $openDay, $closeDay, $image, $imageLink, $titleLink, $urlImage, $insert_user, $no);

            if(mysqli_stmt_execute($stmt)){
            	array_push($ssmsg, $title.'トピックスの編集に成功しました。');
            	$_SESSION["success_msg"]=$ssmsg;
                // header("Location: index.html");
				exit();
            } else{
            	array_push($errmsg, "Error " . mysqli_error($conn));
            	$_SESSION["err_msg"]= $errmsg;
				// header("Location: index.html");
				exit();
            }
		}
	
		$stmt->close();
	}
	else {
		$_SESSION["err_msg"]= $errmsg;
	}
}

include_once "html/showmessage.inc";

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
