<?php
include 'includes/session.php'; 
include 'includes/encryption.php';
$id = $_SESSION['id'];
$userFile = 'data/users/'.$id.'.json'; // userfile
								
$userObj[] = json_decode(file_get_contents($userFile), true); // php assoc array

foreach($userObj as $key => $val) {
	$id = $val['id'];
	$username = $val['username'];
	$email = openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv);
	$token = $val['token'];
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>My Account</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<!-- BS css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<!-- custom css -->
<link rel="stylesheet" href="css/style.css" />
<!-- jQuery api -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- BS JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
<!-- font awesome kit -->
<script src="https://kit.fontawesome.com/3a46605f9c.js" crossorigin="anonymous"></script>

</head>
<body>
<script>
// prevent form resubmission after refresh
if ( window.history.replaceState ) {
	window.history.replaceState( null, null, window.location.href );
}
</script>

<!-- SPINNER -->
<div class="overlay-spinner" style="display: none;">
    <div class="d-flex justify-content-center">  
        <div class="spinner-grow text-primary" role="status">
		    <span class="visually-hidden">Loading...</span>
		</div>
    </div>
</div>



<div class="response ms-4"></div> <!-- ajax callback -->



<section>

	<div class="container">
		<div class="row">
			
			<div class="col-lg-12">	

				<!-- Top navigation-->
				<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
					<div class="container-fluid">
						
						<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
						<div class="collapse navbar-collapse" id="navbarSupportedContent">
							<ul class="navbar-nav ms-auto mt-2 mt-lg-0">
								<li class="nav-item active"><a class="nav-link" href="user.php">Home</a></li>
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

				<div class='account card mt-5 w-25'>
					<div class="card-body bg-light border">
																		
						<!-- Account settings -->
						<h5 class="page-header">My Account</h5>							
							
						<input type="hidden" class="form-control id" value="<?php echo $id; ?>" />
						

						<div class="input-group form-floating mb-3">							
							<input type="text" class="form-control username" value="<?php echo $username; ?>" disabled />								
							<label class="form-label">Username</label>
						</div>
						<div class="input-group form-floating mb-3">							
							<input type="text" class="form-control current-email" value="<?php echo $email; ?>" disabled />								
							<label class="form-label">Email</label>
						</div>
						
						<!-- form new email -->						
						<form id="newemail" method="POST" class="" action="" >	
							<!-- new email -->
							<button type="button" class="btn btn-primary toggle-email mb-3 w-100">Set new Email 🠗</button>
							
							<div class="show-hide-email" style="display: none;">
								<div class="input-group form-floating mb-3">							
									<input type="email" class="form-control new-email" value="" />								
									<label class="form-label">New Email</label>
								</div>
								<div class="input-group form-floating mb-3">							
									<input type="password" class="form-control password" value="" />								
									<label class="form-label">Password</label>
									<div class="input-group-text reveal-passw" role="button"><i class="fas fa-eye-slash"></i></div>
								</div>
								<!-- Submit -->
								<div class="mb-3">
									<button type="submit" class="btn btn-success w-100 submit-resetpass" name="submit">Submit</button>
								</div>
							</div>
							
						</form>
						
						<!-- Form new password -->
						<form id="newpassword" method="POST" class="" action="" >
						
							<button type="button" class="btn btn-primary toggle-password mb-3 w-100">Set new Password 🠗</button>
							<div class="show-hide-password" style="display: none;">
								<div class="input-group form-floating mb-3">							
									<input type="password" class="form-control old-password" value="" />								
									<label class="form-label">Old Password</label>
									<div class="input-group-text reveal-passw" role="button"><i class="fas fa-eye-slash"></i></div>
								</div>
								<div class="input-group form-floating mb-3">							
									<input type="password" class="form-control new-password-1" value="" />								
									<label class="form-label">New Password</label>
									<div class="input-group-text reveal-passw" role="button"><i class="fas fa-eye-slash"></i></div>
								</div>
								<div class="input-group form-floating mb-3">							
									<input type="password" class="form-control new-password-2" value="" />								
									<label class="form-label">Retype New Password</label>
									<div class="input-group-text reveal-passw" role="button"><i class="fas fa-eye-slash"></i></div>
								</div>
								<!-- Submit -->
								<div class="">
									<button type="submit" class="btn btn-success w-100 submit-resetpass" name="submit">Submit</button>
								</div>
							</div>	
								
							
						</form>	

						<!-- Delete account -->
						<form id="deleteaccount" method="POST" class="" action="" >
							<input type="hidden" class="form-control delete-id" value="<?php echo $id; ?>" />
							<input type="hidden" class="form-control delete-token" value="<?php echo $token; ?>" />
							<button type="submit" class="btn btn-danger w-100 submit-delete-account mt-3" name="submit">Delete My Account</button>
						
						</form>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
</section>


<script>

$(".toggle-email").on('click', function() {
	var text = $(this).text();
	if(text == 'Set new Email 🠗') { 
		$('.show-hide-email').show();
		$(this).text('Hide 🠕'); 
	}
	else {
		$('.show-hide-email').hide();
		$(this).text('Set new Email 🠗');
	}
});

$(".toggle-password").on('click', function() {
	var text = $(this).text();
	if(text == 'Set new Password 🠗') { 
		$('.show-hide-password').show();
		$(this).text('Hide 🠕'); 
	}
	else {
		$('.show-hide-password').hide();
		$(this).text('Set new Password 🠗');
	}
});

// newemail
$('#newemail').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from form input
	var id = $('.id').val();  // id user
	var password = $('.password').val();  // password user
	var new_email = $('.new-email').val();  // email user
			
	$.ajax({
		url: 'process.php',
		data: {
				newemail: 1,
				id: id,
				password: password,
				new_email: new_email
				
		},
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			$('.overlay-spinner').show(); // show spinner
		},
		success: function (data) {
			if (data.session == 0) { // if login session has expired
				window.location.href = 'login.php?session=expired'; // redirect to login page
			}
			
			$('.response').html(data.echo).hide().fadeIn('slow'); // success in div .response
			$('.current-email').val(data.renew); // refresh email
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
														
		},
									
	});	
		
});

// newpassword
$('#newpassword').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from form input
	var id = $('.id').val();  // id user
	var old_password =  $('.old-password').val();  // old password user
	var new_password_1=  $('.new-password-1').val();  // new password 1 user
	var new_password_2 =  $('.new-password-2').val();  // new password 2 user
			
	$.ajax({
		url: 'process.php',
		data: {
				newpassword: 1,
				id: id,
				old_password: old_password,
				new_password_1: new_password_1,
				new_password_2: new_password_2			
		},
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			$('.overlay-spinner').show(); // show spinner
		},
		success: function (data) {
			if (data.session == 0) { // if login session has expired
				window.location.href = 'login.php?session=expired'; // redirect to login page
			}
			
			$('.response').html(data.echo).hide().fadeIn('slow'); // success in div .response
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
														
		},
									
	});	
		
});

// Delete account
$('#deleteaccount').on('submit', function (e) {				
	e.preventDefault();
	
	var id = $('.delete-id').val();  // id
	var token = $('.delete-token').val();  // token
		
	$.ajax({
		url: 'process.php',
		data: {
				delete_account: 1,
				id: id,
				token: token			
		},
		type: "POST",
		dataType: "json",
		beforeSend: function() {
			$('.overlay-spinner').show(); // show spinner
		},
		success: function (data) {
			if (data.session == 0) { // if login session has expired
				window.location.href = 'login.php?session=expired'; // redirect to login page
			}
			
			$('.response').html(data.echo).hide().fadeIn('slow'); // success in div .response
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
														
		},
									
	});
		
});

// reveal password
$(".reveal-passw").on('click',function() {
	var pwd = $(".old-password, .new-password-1, .new-password-2");
    if (pwd.attr('type') === 'password') {
        pwd.attr('type', 'text');
		$('.reveal-passw').html('<i class="fas fa-eye"></i>');
    } else {
        pwd.attr('type', 'password');
		$('.reveal-passw').html('<i class="fas fa-eye-slash"></i>');
    }
});



</script>



</body>
</html>