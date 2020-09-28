<?php
if (session_status() == PHP_SESSION_NONE) 
{
	session_start();
}
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

}

$saleUserStmt = $conn->prepare("select no, name from users where invalid=0 AND role = 3 ORDER BY name");
$saleUserResult = execute($saleUserStmt,$conn);
if($saleUserResult==TRUE)
{
    $saleUserResult=$saleUserStmt->get_result();
    $saleUserResultSet = $saleUserResult;
}

$approveUserStmt = $conn->prepare("select no, name from users where invalid=0 AND role = 1 or role = 4 ORDER BY name");
$approveUserResult = execute($approveUserStmt,$conn);
if($approveUserResult==TRUE)
{
    $approveUserResult=$approveUserStmt->get_result();
    $approveUserResultSet = $approveUserResult;
}


?>

<?php 
function remove_special_character($string) {

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
?>