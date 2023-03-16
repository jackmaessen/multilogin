<?php 
	session_start();
	unset($_SESSION['adminMUL']);
	unset($_SESSION['last_visited']);
	header('Location: login.php');
?>
