<?php
session_start();

if(!isset($_SESSION['adminMUL'])) {
	header("Location: login.php");
	exit;
}


?>