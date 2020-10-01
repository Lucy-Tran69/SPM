<?php
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
    $insert_user = $_SESSION["loginUserId"];
   	$cd = $_POST["cd"];
   	$checkOK = 1;
   	//echo $cd;
   	if (empty($cd)) {
    	$msg->error('Code customer is required.');
    	$checkOK = 0;
  	}
  	if (isset($cd) && mb_strlen(mb_convert_encoding($cd, "UTF-8")) >  8) {
    	$msg->error('Code customer over 8 charecter.');
    	$checkOK = 0;
  	} else {
	    $cd = test_input($_POST["cd"]);
	    // check if name only contains letters and whitespace
	    if (!preg_match("/^[a-z A-Z 0-9]*$/",$cd)) {
	      $msg->error('Please enter charecter haftSize.');
	      $checkOK = 0;
    	}
  	}

  	$name = $_POST["name"];
  	if (empty($name)) {
    	$msg->error('Name customer is required.');
    	$checkOK = 0;
  	}elseif (isset($name) && mb_strlen(mb_convert_encoding($name, "UTF-8")) > 126 ) {
  		$msg->error('Name customer over 126 charecter.');
  		$checkOK = 0;
  	}

  	$tel = $_POST["tel"];
  	if (empty($tel)) {
    	$msg->error('tel customer is required.');
    	$checkOK = 0;
  	}elseif (isset($tel) && mb_strlen(mb_convert_encoding($tel, "UTF-8")) > 16 ) {
  		$msg->error('Telephon over 16 charecter.');
  		$checkOK = 0;
  	}

  	$zip = $_POST["zip"];
  	if (empty($zip)) {
    	$msg->error('zip customer is required.');
    	$checkOK = 0;
  	}elseif (isset($zip) && mb_strlen(mb_convert_encoding($zip, "UTF-8")) > 16 ) {
  		$msg->error('Zip code over 16 charecter.');
  		$checkOK = 0;
  	}

  	$address = $_POST["address"];
  	if (empty($address)) {
    	$msg->error('address customer is required.');
    	$checkOK = 0;
  	}elseif (isset($address) && mb_strlen(mb_convert_encoding($address, "UTF-8")) > 512 ) {
  		$msg->error('Address code over 512 charecter.');
  		$checkOK = 0;
  	}

  	$charge = $_POST["charge"];
  	if (empty($charge)) {
    	$msg->error('charge customer is required.');
    	$checkOK = 0;
  	}elseif (isset($charge) && mb_strlen(mb_convert_encoding($charge, "UTF-8")) > 512 ) {
  		$msg->error('Charge code over 512 charecter.');
  		$checkOK = 0;
  	}

  	$sale = $_POST["sale"];
  	if (empty($sale)) {
    	$msg->error('Sale customer is required.');
    	$checkOK = 0;
  	}

  	$supervisor = $_POST["supervisor"];
  	if (empty($supervisor)) {
    	$msg->error('Supervisor customer is required.');
    	$checkOK = 0;
  	}

  	$invalid = isset($_POST["invalid"]) ? 1 : 0;

  	//echo $invalid;
  	//print_r($checkOK);die();

  	if ($checkOK == 1) {
  		//save db
  		$sql = "insert into customer (cd, name, tel, zip, address, charge, sales, supervisor, invalid, inuser)
                values ('$cd','$name','$tel','$zip','$address','$charge',$sale,$supervisor,$invalid, $insert_user)";
								//print_r($sql);die();
  		if (mysqli_query($conn, $sql)) {
  			//header("Location: ../../app/customer/customer.html");
  		  	$msg->success("New record created successfully");
  		  	// header("Location: ../../app/customer/customer.html");
    			// exit;
  		    //header('Location:customer.html');
  		} else {
  		  $msg->error(mysqli_error($conn));
  		  $checkOK = 0;
  		}
  	}

  	$msg->display();
}

$userStmt = $conn->prepare("select no,name from users where invalid=0 and role = 3 order by name ");
$userResult = execute($userStmt,$conn);
if($userResult==TRUE)
{
    $userResult=$userStmt->get_result();
    $saleUserResultSet = $userResult;
}

$userStmt = $conn->prepare("select no,name from users where invalid=0 and role = 4 or role = 1 order by name");
$userResult = execute($userStmt,$conn);
if($userResult==TRUE)
{
    $userResult=$userStmt->get_result();
    $approveUserResultSet = $userResult;
}

$conn->close();

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
