<?php
session_start();

$error = '';
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {	

	if(isset($_POST['username'],$_POST['password'])) {
				
		/***********************************************/
		$inputUser = $_POST['username'];
		$inputPassword = $_POST['password'];
		
		$allUsers = glob('data/users/*.json'); // array with all json files
								
		foreach ($allUsers as $singelUser) {
			$loginObj[] = json_decode(file_get_contents($singelUser), true); 
		}
		foreach($loginObj as $key => $val) {
				$id = $val['id'];
				$username = $val['username'];
				$password = $val['password'];
				$email = $val['email'];
				
				if ($inputUser == $username && password_verify($inputPassword, $val['password'])) {
					$loggedIn = true;
					$_SESSION['id'] = $id;										

					$loginRedirect = 'user.php?id='.$id;
					
					header("Location: $loginRedirect");
					break;
				}
				else {
					$loggedIn = false;
					$error = '<div class="alert alert-danger"><b>&excl;</b>&nbsp;Incorrect login data</div>';
				}			
		}
		
		
	}
}


if($_GET['session'] == 'expired') {
	$error = '<div class="alert alert-danger">Login-session experid! Login again</div>';
}
if($_GET['already-registered']) {
	$error = '<div class="alert alert-danger">User does not exist or already registered!</div>';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap css-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<!-- custom css -->
<link rel="stylesheet" href="css/style.css" />
	
</head>
<body>
<br /><br />
	<div class="container">
		<div class="row">
		
			<div class="col-md-4"></div> <!-- empty -->
		
			<div class="col-md-4">	
			
				<?php if(!isset($_SESSION['username_user'])) { ?>
				<div class='set-settings card mt-5'>
					<div class="card-body bg-light border">
												
						<?php 
						echo $error;
						if($_GET['confirmed']) { 
						?>
						
							<div class="alert alert-success"><b>&check;</b>&nbsp;Registration confirmed! You now can Login</div>
						<?php } ?>
						
						<form  method="post" action="login.php">
							<h5 class="page-header">Login</h5>
							<div class="mb-3">
								<input class="form-control" placeholder="Username" name="username" type="text">
							</div>
							<div class="mb-3">
								<input class="form-control" placeholder="Password" name="password" type="password" value="">
							</div>
							<div class="mb-3">
								<input class="btn btn-lg btn-success w-100" type="submit" value="Login">
							</div>
							<span><a href="requestpass.php">Forget Username or Password?</a></span><br />
							<span>Don't have an account? <a href="register.php">Register</a></span>
						</form>
						
					</div>	
				</div>
			
				
				<?php
				}
				else {
					
					include 'data/pages/user.php';
				}	
				?>
				
			</div>
			
			<div class="col-md-4"></div> <!-- empty -->
			
		</div>
	</div>
	
</body>
</html>
