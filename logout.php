<?php 
	session_start();
	unset($_SESSION['id']);
	unset($_SESSION['id_admin']);
	unset($_SESSION['last_visited']);
	header('Location: login.php');
?>
