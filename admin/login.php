<?php
session_start();

$error = '';

// login file
$loginfile = '../data/admin/login.json'; 
$loginObj[] = json_decode(file_get_contents($loginfile), 1); 
foreach($loginObj as $key => $val) {
	$savedUsername = $val['username']; // saved username admin
	$savedPassword = $val['password']; // saved password	admin
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
	if(isset($_POST['username'],$_POST['password'])) {

		$username = $_POST['username']; // input username
		$password = $_POST['password']; // input password
		
		// compare username and password 
		if($username == $savedUsername && $password == password_verify($password, $savedPassword)) {
			
			$_SESSION['adminMUL'] = $username;
			if(isset($_SESSION['lastvisited'])) {
				 $lastvisited = $_SESSION['lastvisited']; // holds url for last page visited.
			}
			else {
				$lastvisited = "admin.php"; // default page for 
			}
			header("Location: $lastvisited"); // redirect last visited
		}
		else {
			$error = '<div class="alert alert-danger">Incorrect login data</div>';			
		}
		
	}
}
if($_GET['session'] == 'expired') {
	$error = '<div class="alert alert-danger">Login-session expired! Login again</div>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<title>Login Admin Panel</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap css-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

	
</head>
<body>
<br /><br />
	<div class="container">
		<div class="row">
		
			<div class="col-md-4 col-md-offset-4"></div> <!-- empty -->
		
			<div class="col-md-4 col-md-offset-4">				
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">							
							Login Administrator
						</h3>
					</div>
					<div class="panel-body">
						<?php echo $error; ?>
						<form  method="post" action="login.php">
							
								<div class="mb-3">
									<input class="form-control" placeholder="Username" name="username" type="text">
								</div>
								<div class="mb-3">
									<input class="form-control" placeholder="Password" name="password" type="password" value="">
								</div>
								<div class="mb-3">
									<input class="btn btn-lg btn-success w-100" type="submit" value="Login">
								</div>
						</form>
					</div>
				</div>								
			</div>
			
			<div class="col-md-4 col-md-offset-4"></div> <!-- empty -->
			
		</div>
	</div>
	
</body>
</html>
