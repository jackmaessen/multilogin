<?php
session_start();
$user = false;
$admin = false;
if(isset($_SESSION['login_user'])) {
	$user = true;
	$admin = false;	
}
else if(isset($_SESSION['login_admin'])) {
	$admin = true;
}
else {
	//header("Location: login.php");
	$result = [];
	$result['session'] = 0;
	echo json_encode($result);
	exit();
}
//header("Location: login.php");

/*
$result = [];
$result['session'] = 0;
echo json_encode($result);
exit;

//$user = true;
//$admin = true;*/
?>