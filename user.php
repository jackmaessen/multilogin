<?php 
include 'includes/session.php';
include 'includes/encryption.php';
$id = $_SESSION['id'];
$userFile = 'data/users/'.$id.'.json'; // userfile
								
$userObj[] = json_decode(file_get_contents($userFile), true); // php assoc array

foreach($userObj as $key => $val) {
	$id = $val['id'];
	$username = $val['username'];
	$email = openssl_decrypt ($val['email'], $ciphering, $decryption_key, $options, $decryption_iv);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
<meta name="description" content="" />
<meta name="author" content="" />
<title>Personal Page</title>
<!-- Favicon-->
<link rel="icon" type="image/x-icon" href="" />
<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap css-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

</head>
<body>
	<div class="container" id="wrapper">
		<div class="row">
			<div class="col-lg-12">
		
				<!-- Page content wrapper-->
				<div id="page-content-wrapper">
					<!-- Top navigation-->
					<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
						<div class="container-fluid">
							
							<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
							<div class="collapse navbar-collapse" id="navbarSupportedContent">
								<ul class="navbar-nav ms-auto mt-2 mt-lg-0">
									<li class="nav-item active"><a class="nav-link" href="#">Home</a></li>
									<!--<li class="nav-item"><a class="nav-link" href="#!">Link</a></li>-->
									<li class="nav-item dropdown">
										<a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Account</a>
										<div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
											<a class="dropdown-item" href="account.php">My Account</a>
											<a class="dropdown-item" href="#!">Another action</a>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item" href="logout.php">Logout</a>
										</div>
									</li>
									
								</ul>
							</div>
						</div>
					</nav>
					<!-- Page content-->
					<div class="container-fluid">				
							<h2 class="mt-4">Usercontent comes here</h2>
							This is the page of <b><?php echo $username; ?></b>			
					</div>
				</div>
				
			</div>						
		</div>
	</div>

</body>
</html>
