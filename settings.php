<?php
include 'includes/session.php';
if(!$admin) {
	header("Location: login.php");
	exit;
}
// read settings
include 'includes/read-settings.php';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href='lib/main.css' rel='stylesheet' />
<!-- BS css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

<!-- jQuery core -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- BS JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

<!-- Resizing modal -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css" />
<!-- API key for TinyMCE -->
<script src="https://cdn.tiny.cloud/1/cu9iuv1soi8lkx4dfa0qp167qpr7pw81y9rj9n42dvtj1mch/tinymce/5/tinymce.min.js"></script> 
<!--jQuery API TinyMCE -->
<script src="https://cdn.tiny.cloud/1/cu9iuv1soi8lkx4dfa0qp167qpr7pw81y9rj9n42dvtj1mch/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>

<!-- font awesome kit -->
<script src="https://kit.fontawesome.com/3a46605f9c.js" crossorigin="anonymous"></script>

<!-- Date picker -->
<script type="text/javascript" src="js/bootstrap-datepicker.js"></script>

<link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.css" />



<script type="text/javascript" src="js/jquery.timepicker.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.timepicker.css" />

<!-- Calendar scripts -->	
<script src='lib/main.js'></script>
<script src='lib/locales-all.js'></script>


<!-- moment lib -->
<script src='https://cdn.jsdelivr.net/npm/moment@2.27.0/min/moment.min.js'></script>
<script src="js/moment-duration-format.js"></script>
<!-- the moment-to-fullcalendar connector. must go AFTER the moment lib -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/moment@5.5.0/main.global.min.js'></script>

<!-- rrule lib -->
<script src='https://cdn.jsdelivr.net/npm/rrule@2.6.6/dist/es5/rrule.min.js'></script>
<!-- the rrule-to-fullcalendar connector. must go AFTER the rrule lib -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@5.5.1/main.global.min.js'></script>

<!-- main css -->
<link rel="stylesheet" type="text/css" href="css/main.css" />


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

<!-- SETTINGS FORM -->
<?php 
if($admin) { ?>
<div class="response ms-4"></div> <!-- ajax callback -->

<section class="main-navigation">
	<div class="container">
		<div class="row my-5">
			<?php include 'includes/navigation.php'; ?>
		</div>		
	</div>
</section>

<section class="head">
	<div class="container">
		<div class="row my-5">
			<div class="col-lg-12">
				
				<div class="p-2 bg-light border">				
					<div class="header">						
						<h5>Instellingen</h5>						
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="settings">
	<div class="container">
		<div class="row">
			<div class="col-lg-8">				
			
				<div class='set-settings card'>
					<div class="card-body bg-light border">
						<form id="login" method="POST" class="" action="" >
							<!-- Login settings -->
							<h5 class="page-header">Login Personeel</h5>
							
							<div class="form-floating mb-3">							
								<input type="text" class="form-control set-username-user" value="<?php echo $username_user; ?>" />
								<label class="form-label">Username</label>
							</div>
							<div class="input-group form-floating mb-3">							
								<input type="password" class="form-control set-password-user" value="<?php echo $password_user; ?>" />
								<label class="form-label">Password</label>
								<div class="input-group-text reveal-passw"><i class="fas fa-eye-slash"></i></div>
							</div>
							
							<h5 class="page-header">Login Beheerder</h5>
							
							<div class="form-floating mb-3">							
								<input type="text" class="form-control set-username-admin" value="<?php echo $username_admin; ?>" />
								<label class="form-label">Username</label>
							</div>
							<div class="input-group form-floating mb-3">							
								<input type="password" class="form-control set-password-admin" value="<?php echo $password_admin; ?>" />
								<label class="form-label">Password</label>
								<div class="input-group-text reveal-passw"><i class="fas fa-eye-slash"></i></div>
							</div>
							<!-- Submit -->
							<div class="mt-5">
								<button type="submit" class="btn btn-success w-100 submit-login" name="submit">Logins Opslaan</button>
							</div>
							
						</form>	
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- TARIEVEN -->





<?php } ?>

<script>

// LOGIN
$('#login').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from modal input 
	var set_username_user = $('.set-username-user').val();  // username user
	var set_password_user =  $('.set-password-user').val();  // password user
	var set_username_admin = $('.set-username-admin').val();  // username admin
	var set_password_admin =  $('.set-password-admin').val();  // password admin
			
	$.ajax({
		url: 'process-settings.php',
		data: {
				login: 1,
				set_username_user: set_username_user,
				set_password_user: set_password_user,
				set_username_admin: set_username_admin,
				set_password_admin: set_password_admin,
				
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
	//$('.reveal-passw').html('<i class="fas fa-eye-slash"></i>');
	var pwd = $(".set-password-user,.set-password-admin");
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