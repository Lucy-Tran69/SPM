<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

// $ssmsg = array();
// $errmsg = array();
include_once "../../include/database/db.inc";

$conn  = getConnection();

if ($conn->connect_error) {
	die("Failed to connect to database. " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION["loginAccount"])) {
	$insert_user = $_SESSION["loginUserId"];
    $upday = date('Y-m-d H:i:s');
	$id = $_POST["id"];
	$cd = $_POST["cd"];
   	$checkOK = 1;
   	//echo $id;
   	if (empty($cd)) {
		   $msg->error('取引先コードを入力してください。');
    	// array_push($errmsg,'取引先コードを入力してください。');
    	$checkOK = 0;
  	}
  	if (isset($cd) && mb_strlen(mb_convert_encoding($cd, "UTF-8")) >  8) {
		  $msg->error('取引先コードは8文字以内で入力してください。');
    	// array_push($errmsg,'取引先コードは8文字以内で入力してください。');
    	$checkOK = 0;
  	} else {
	    $cd = test_input($_POST["cd"]);
	    // check if name only contains letters and whitespace
	    if (!preg_match("/^[a-z A-Z 0-9]*$/",$cd)) {
			$msg->error('数字・文字のみ（特殊文字を含めない）を入力してください。');
	    //   array_push($errmsg,'数字・文字のみ（特殊文字を含めない）を入力してください。');
	      $checkOK = 0;
    	}
  	}

  	$name = $_POST["name"];
  	if (empty($name)) {
		  $msg->error('取引先名を入力してください。');
    	// array_push($errmsg,'取引先名を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($name) && mb_strlen(mb_convert_encoding($name, "UTF-8")) > 126 ) {
		  $msg->error('取引先名は126文字以内で入力してください。');
  		// array_push($errmsg,'取引先名は126文字以内で入力してください。');
  		$checkOK = 0;
  	}

  	$tel = $_POST["tel"];
  	if (empty($tel)) {
		  $msg->error('電話番号を入力してください。');
    	// array_push($errmsg,'電話番号を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($tel) && mb_strlen(mb_convert_encoding($tel, "UTF-8")) > 16 ) {
		  $msg->error('電話番号は数字16桁以内で入力してください。');
  		// array_push($errmsg,'電話番号は数字16桁以内で入力してください。');
  		$checkOK = 0;
  	}

  	$zip = $_POST["zip"];
  	if (empty($zip)) {
		  $msg->error('郵便番号を入力してください。');
    	// array_push($errmsg,'郵便番号を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($zip) && mb_strlen(mb_convert_encoding($zip, "UTF-8")) > 16 ) {
		  $msg->error('郵便番号は数字16桁以内で入力してください。');
  		// array_push($errmsg,'郵便番号は数字16桁以内で入力してください。');
  		$checkOK = 0;
  	}

  	$address = $_POST["address"];
  	if (empty($address)) {
		  $msg->error('住所を入力してください。');
    	// array_push($errmsg,'住所を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($address) && mb_strlen(mb_convert_encoding($address, "UTF-8")) > 512 ) {
		  $msg->error('住所は512文字以内で入力してください。');
  		// array_push($errmsg,'住所は512文字以内で入力してください。');
  		$checkOK = 0;
  	}

  	$charge = $_POST["charge"];
  	if (empty($charge)) {
		  $msg->error('担当者名を入力してください。');
    	// array_push($errmsg,'担当者名を入力してください。');
    	$checkOK = 0;
  	}elseif (isset($charge) && mb_strlen(mb_convert_encoding($charge, "UTF-8")) > 512 ) {
		  $msg->error('担当者名は512文字以内で入力してください。');
  		// array_push($errmsg,'担当者名は512文字以内で入力してください。');
  		$checkOK = 0;
  	}

  	$sale = $_POST["sale"];
  	if (empty($sale)) {
		  $msg->error('営業担当を選択してください。');
    	// array_push($errmsg,'営業担当を選択してください。');
    	$checkOK = 0;
  	}

  	$supervisor = $_POST["supervisor"];
  	if (empty($supervisor)) {
		$msg->error('承認者を選択してください。');
		// array_push($errmsg,'承認者を選択してください。');
    	$checkOK = 0;
  	}

  	$invalid = isset($_POST["invalid"]) ? $_POST["invalid"] : 0;

    $checkDupCdInDb = "select count(*) as count_row from customer where cd = '$cd' and no != $id";
    $NumDupCdInDb = mysqli_query($conn,$checkDupCdInDb);

    $row = mysqli_fetch_assoc($NumDupCdInDb);
    $num = $row['count_row'];
    if($num > 0){
		$msg->error('この取引先コードは既に存在しています。');
    //   array_push($errmsg,'この取引先コードは既に存在しています。');
      $checkOK = 0;
    }

  	if ($checkOK == 1) {
	    $sql = "update customer set cd = '$cd', name = '$name', tel = '$tel', zip = '$zip', address = '$address', charge = '$charge', sales = $sale, supervisor = $supervisor, invalid= $invalid, 
	    			upday =  '$upday' , upuser = $insert_user 
	      			where no = $id";
	                // print_r($sql);die();
	    if (mysqli_query($conn, $sql)) {
		  //header("Location: ../../app/customer/customer.html");
		  $msg->success('取引先編集に成功しました。');
	    //    array_push($ssmsg, "取引先編集に成功しました。");
        //   $_SESSION["success_msg"]=$ssmsg;
        //   echo json_encode(array("statusCode"=>200, "msg" =>  $ssmsg));
	        // header("Location: ../../app/customer/customer.html");
	        // exit;
	        //header('Location:customer.html');
	    } else {
			$msg->error('mysqli_error($conn)');
			$msg->display();
	    //    echo json_encode(array("statusCode"=>201, "msg" =>  mysqli_error($conn)));
	    }
  	}else{
		  $msg->display();
    //   echo json_encode(array("statusCode"=>201, "msg" =>  $errmsg));
    }
}

$conn->close();

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}