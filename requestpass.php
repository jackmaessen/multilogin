
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Request Username/Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<!-- BS css -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<!-- custom css -->
<link rel="stylesheet" href="css/style.css" />
<!-- jQuery core -->
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
			<div class="col-lg-4"></div>
			<div class="col-lg-4">				

				<div class='set-settings card mt-5'>
					<div class="card-body bg-light border">
						<form id="requestpass" method="POST" class="" action="" >
							
							<h5 class="page-header">Request Username/Password</h5>
														
							<div class="input-group form-floating mb-3">							
								<input type="email" class="form-control email" value="" />
								<label class="form-label">Email</label>
							</div>
							

							<!-- Submit -->
							<div class="">
								<button type="submit" class="btn btn-success w-100 submit-setpass" name="submit">Submit</button>
							</div>
							
						</form>	
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-4"></div>
</section>

<script>

// SET PASSWORD
$('#requestpass').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from form input
	var email =  $('.email').val();  // email user
			
	$.ajax({
		url: 'process.php',
		data: {
				requestpass: 1,				
				email: email
				
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





</script>



</body>
</html>