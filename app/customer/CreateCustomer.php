<?php
include_once "common/session.php";
include_once "database/db.inc";

$conn  = getConnection();

if($conn->connect_error) {
	die("Failed to connect to database. ".$conn->connect_error);
}

if($_SERVER['REQUEST_METHOD']=='POST' && isset($_SESSION["loginAccount"]))
{
    $insert_user = $_SESSION["loginUserId"];
   	$cd = $_POST["cd"];
   	$checkOK = 1;
	$cd = removeWhitespaceAtBeginAndEndOfString($_POST["cd"]);
   	if (empty($cd)) {
		$msg->error('取引先コードを入力してください。');
    	$checkOK = 0;
	}
	
  	if (isset($cd) && mb_strlen(mb_convert_encoding($cd, "UTF-8"), "UTF-8") >  16) {
       	$msg->error('取引先コードは16文字以内で入力してください。');
    	$checkOK = 0;
  	} else {
	    if (!preg_match("/^[a-z A-Z 0-9]*$/",$cd)) {
			$msg->error('数字・文字のみ（特殊文字を含めない）を入力してください。');
	      $checkOK = 0;
    	}
  	}

	$name = removeWhitespaceAtBeginAndEndOfString($_POST["name"]);
  	if (empty($name)) {
		  $msg->error('取引先名を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($name) && mb_strlen(mb_convert_encoding($name, "UTF-8")) > 126 ) {
		  $msg->error('取引先名は126文字以内で入力してください。');
  		$checkOK = 0;
  	}

	$tel = removeWhitespaceAtBeginAndEndOfString($_POST["tel"]);
  	if (empty($tel)) {
		  $msg->error('電話番号を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($tel) && mb_strlen(mb_convert_encoding($tel, "UTF-8")) > 16 ) {
		  $msg->error('電話番号は数字16桁以内で入力してください。');
  		$checkOK = 0;
  	}

	$zip = removeWhitespaceAtBeginAndEndOfString($_POST["zip"]);
  	if (empty($zip)) {
		  $msg->error('郵便番号を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($zip) && mb_strlen(mb_convert_encoding($zip, "UTF-8")) > 16 ) {
		  $msg->error('郵便番号は数字16桁以内で入力してください。');
  		$checkOK = 0;
  	}

	$address = removeWhitespaceAtBeginAndEndOfString($_POST["address"]);
  	if (empty($address)) {
		  $msg->error('住所を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($address) && mb_strlen(mb_convert_encoding($address, "UTF-8")) > 512 ) {
		  $msg->error('住所は512文字以内で入力してください。');
  		$checkOK = 0;
  	}

	$charge = removeWhitespaceAtBeginAndEndOfString($_POST["charge"]);
  	if (empty($charge)) {
		  $msg->error('担当者名を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($charge) && mb_strlen(mb_convert_encoding($charge, "UTF-8")) > 512 ) {
		  $msg->error('担当者名は512文字以内で入力してください。');
  		$checkOK = 0;
  	}

  	$sales = $_POST["sale"];
  	if (empty($sales)) {
		  $msg->error('営業担当を選択してください。');
    	$checkOK = 0;
  	}

  	$supervisor = $_POST["supervisor"];
  	if (empty($supervisor)) {
		  $msg->error('承認者を選択してください。');
    	$checkOK = 0;
	  }
	  
	$displaylimit = isset($_POST["displaylimit"]) ? $_POST["displaylimit"] : 1;

    $invalid = isset($_POST["invalid"]) ? $_POST["invalid"] : 0;

	$checkDupCdInDb = "select count(*) as count_row from customer where cd = ?";
	
    $stmtCheckDupCdInDb = $conn->prepare($checkDupCdInDb);
	$stmtCheckDupCdInDb->bind_param('s', $cd);
    $stmtCheckDupCdInDb->execute();
    $stmtCheckDupCdInDb->store_result();
    $row = fetchAssocStatement($stmtCheckDupCdInDb);
    $num = $row['count_row'];
    if($num > 0){
		$msg->error('この取引先コードは既に存在しています。');
      $checkOK = 0;
    }

  	if ($checkOK == 1) {
  		//save db
  		$sql = "insert into customer (cd, name, tel, zip, address, charge, sales, supervisor, invalid, inuser, displaylimit)
				values (?,?,?,?,?,?,?,?,?,?,?)";
				
		$stmt = $conn->prepare($sql);

		$stmt->bind_param('ssssssiiiii', $cd, $name, $tel, $zip, $address, $charge, $sales, $supervisor, $invalid, $insert_user, $displaylimit);

		$stmt->execute();

  		if (!($stmt->error)) {
		  $msg->success('新規取引先の追加に成功しました。');
  		} else {
			if($stmt->error){
				$msg->error('新規取引先の追加時にエラーが発生しました。もう一度お試しください。');
				$msg->display();
			}
		  }
		$stmt->close();
  	}else{
	  $msg->display();
	}
	$conn->close();
}

function removeWhitespaceAtBeginAndEndOfString($data) {
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}
?>
