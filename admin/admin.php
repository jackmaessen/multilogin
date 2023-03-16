<?php 
include 'session-admin.php';
include '../includes/encryption.php'; 

$adminLoginfile = '../data/admin/login.json';
$adminloginObj[] = json_decode(file_get_contents($adminLoginfile), true); 
foreach($adminloginObj as $key => $val) {
	$adminUsername = $val['username'];
	$adminPassword = $val['password'];
	$adminEmail = $val['email'];
	$decryptedAdminEmail = openssl_decrypt ($adminEmail, $ciphering, $decryption_key, $options, $decryption_iv);
}

$adminEmailsettingsfile = '../data/emailsettings.json';
$adminEmailsettingsObj[] = json_decode(file_get_contents($adminEmailsettingsfile), true); 
foreach($adminEmailsettingsObj as $key => $val) {
	$from_name = $val['from_name'];
	$from_email = $val['from_email'];
	$smtp_host = $val['smtp_host'];
	$smtp_port = $val['smtp_port'];
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<!-- jquery api -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- bootstrap css -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
<!-- custom css -->
<link rel="stylesheet" href="css/style.css" />
<!-- bootstrap js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>	
<!-- FontAwesome kit -->
<script src="https://kit.fontawesome.com/5ea89e2aa1.js" crossorigin="anonymous"></script>

</head>
<body>
<script>
// prevent form resubmission after refresh
if ( window.history.replaceState ) {
	window.history.replaceState( null, null, window.location.href );
}
</script>


<div class="response ms-4"></div> <!-- ajax callback -->

<div class="container">


	<div class="row">
			
		<div class="col-lg-12">	

			<!-- Top navigation-->
			<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
				<div class="container-fluid">
					
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav ms-auto mt-2 mt-lg-0">
							<li class="nav-item active"><a class="nav-link" href="admin.php">Home</a></li>
							<!--<li class="nav-item"><a class="nav-link" href="#!">Link</a></li>-->
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Account</a>
								<div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
									<a class="dropdown-item" href="?settings=1">Settings</a>
									<a class="dropdown-item" href="#">Another action</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="logout.php">Logout</a>
								</div>
							</li>
							
						</ul>
					</div>
				</div>
			</nav>
			
			<!-- SPINNER -->
			<div class="overlay-spinner" style="display: none;">
				<div class="d-flex justify-content-center">  
					<div class="spinner-grow text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
			</div>

			
		</div>
	</div>		


	<div class="row">
				
		<div class="col-md-9 col-lg-9">
						
			<div class="confirmed userlist mt-5">
				<h3 class="header">Confirmed users</h3>
				<!-- CREATE TEBALE OF CONFIRMED USERS -->
				<table class="table confirmed">
					<thead>
						<tr>
						   <th scope="col">#</th>
						   <th scope="col">Id</th>
						   <th scope="col">Username</th>
						   <th scope="col">Email</th>
						   <th scope="col">Delete</th>
						</tr>
					</thead>
						<tbody>
				<?php

				// All Registered users
				$allFiles = glob('../data/users/*.json'); // array of all registered users
				foreach ($allFiles as $singelFile) {
					$allUsersObj[] = json_decode(file_get_contents($singelFile), true); // php assoc array
				}
				// sort array by username; make username lowercase
				$key_values = array_column($allUsersObj, 'username'); // sort by username
				array_multisort( array_map('strtolower', $key_values), SORT_ASC, $allUsersObj );
				
				// insert pagination
				include 'pagination.php';
				
				$i = '0';
				foreach($allUsersObj as $key => $val) {
					$i++;
					$id = $val['id'];
					$username = $val['username'];
					$password = $val['password'];
					$encrypted_email = $val['email'];
					
					// decrypt email
					$decrypted_email = openssl_decrypt($encrypted_email, $ciphering, $decryption_key, $options, $decryption_iv);
					
					if($i > $startRecord && $i <= $endRecord) { // limit the records to show
				?>					 
						<tr>
							<th scope="row"><?php echo $i; ?></th>
							<td class="id" id="<?php echo $id; ?>" data-value='<?php echo $id; ?>'><?php echo $id; ?></td>
							<td class="username"><?php echo $username; ?></td>							
							<td class="email"><?php echo $decrypted_email; ?></td>
							<td class=""><i class="fa-solid fa-trash text-danger delete-user"></i></td>
			
						</tr>														 					
				<?php 
					}
				}
				
				?>
					</tbody>
				</table>
			</div>
			
			<!-- PAGINATION -->
			<nav class="mt-5">
			    <ul class="pagination">
					<li class="page-item disabled prev"><a class="page-link " data-value="prev" href="javascript:void(0);">Prev</a></li>
					<li class="page-item"><a class="page-link page-1 active" data-value="1" href="javascript:void(0);">1</a></li>
					<?php 
						for($page = $newStart; $page < $newEnd + 1; $page++) { ?>
						<li class="page-item flex"><a class="page-link page-<?php echo $page; ?>" data-value="<?php echo $page; ?>" href="javascript:void(0);"><?php echo $page; ?></a></li>									
					<?php 
						} 
						if($pagination) { // > bool; 5 pages = true ?>					
						<li class="page-item jump-next"><a class="page-link" data-value="jump-next" href="javascript:void(0);">...</a></li>
					<?php } // endif pagination 
						if($lastPage != 1) {
						?>
						<li class="page-item"><a class="page-link page-<?php echo $lastPage; ?>" data-value="<?php echo $lastPage; ?>" href="javascript:void(0);"><?php echo $lastPage; ?></a></li>						
						<li class="page-item next"><a class="page-link" data-value="next" href="javascript:void(0);">Next</a></li>
					<?php } else { ?>
						<li class="page-item disabled next"><a class="page-link" data-value="next" href="javascript:void(0);">Next</a></li>
					<?php } ?>
					
				</ul>
			</nav>
			
			<div class="test"></div>
			
			
			<!-- SETTINGS -->
			<div class="settings mt-5" style="display: none;">
							
				<h3 class="header-settings mb-5">Settings</h3>
				
				<!-- login settings -->
				<h5 class="header-settings">Login Settings Admin</h5>
				<form id="loginsettings" method="POST" class="" action="" >
					
					<div class="form-floating mb-3">							
						<input type="text" class="form-control admin-username" value="<?php echo $adminUsername; ?>" />
						<label class="form-label">Adminstrator Username</label>
					</div>
					<div class="input-group form-floating mb-3">							
						<input type="password" class="form-control admin-new-password" value="" />
						<label class="form-label">Administrator New Password <span class="text-danger">(leave blank if you don't want to change)</span></label>
						<div class="input-group-text reveal-passw" role="button"><i class="fas fa-eye-slash"></i></div>
					</div>
					<div class="input-group form-floating mb-3">							
						<input type="email" class="form-control admin-email" value="<?php echo $decryptedAdminEmail; ?>" />
						<label class="form-label">Administrator Email</label>
					</div>
					

					<!-- Submit -->
					<div class="mb-3">
						<button type="submit" class="btn btn-success w-100 submit" name="submit">Submit Login Settings</button>
					</div>
										
				</form>
				
					<!-- Email settings users-->
				<h5 class="header-settings mt-5">Email Settings Users</h5>
				<form id="emailsettings" method="POST" class="" action="" >
					
					<div class="form-floating mb-3">							
						<input type="text" class="form-control from-name" value="<?php echo $from_name; ?>" />
						<label class="form-label">From name in email</label>
					</div>
					<div class="input-group form-floating mb-3">							
						<input type="email" class="form-control from-email" value="<?php echo $from_email; ?>" />
						<label class="form-label">From email in email</label>

					</div>
					<div class="input-group form-floating mb-3">							
						<input type="text" class="form-control smtp-host" value="<?php echo $smtp_host; ?>" />
						<label class="form-label">SMTP host (e.g. localhost)</label>
					</div>
					<div class="input-group form-floating mb-3">							
						<input type="text" class="form-control smtp-port" value="<?php echo $smtp_port; ?>" />
						<label class="form-label">SMTP port (e.g. 25)</label>
					</div>					

					<!-- Submit -->
					<div class="mb-5">
						<button type="submit" class="btn btn-success w-100 submit" name="submit">Submit Email Settings</button>
					</div>
										
				</form>
				
			</div>
			
		
			
		</div>	<!-- end col-9 -->
		
		<!-- SIDECOLUMN -->
		
		<div class="col-md-3 col-lg-3">
			
			<h5 class="page-header mt-5">Filter Confirmed/Unconfirmed</h5>				
			<!-- filter category -->
			<button class="btn btn-primary w-100 mb-1 select" value="confirmed" >Confirmed</button>
			<button class="btn btn-secondary w-100 select" value="unconfirmed" >Unconfirmed</button>
			
			<h5 class="page-header mt-5">Search for name or email</h5>				
			<!-- search -->
				
				<div class="input-group mb-3">
					<input class="form-control search-input" type="search" name="search" placeholder="Search for..." />
					<div class="input-group-append">				
						<button class="btn btn-primary search">Search</button>
					</div>
				</div>							
		
			<br />
			
			<!-- Add user manually; button trigger modal -->
			<button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#add">
			  Add User Manually
			</button>
			
			<!-- Modal Add user manually-->
			<div class="modal fade" id="add" tabindex="-1" aria-labelledby="" aria-hidden="true">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="">Add User</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body">
					
						<form id="addUser" method="POST" class="" action="" >
														
							<div class="form-floating mb-3">							
								<input type="text" class="form-control add-username" value="" />
								<label class="form-label">Username</label>
							</div>
							<div class="input-group form-floating mb-3">							
								<input type="password" class="form-control add-password" value="" />
								<label class="form-label">Password</label>
								<div class="input-group-text reveal-passw" role="button"><i class="fas fa-eye-slash"></i></div>
							</div>
							<div class="input-group form-floating mb-3">							
								<input type="email" class="form-control add-email" value="" />
								<label class="form-label">Email</label>
							</div>
							
							<!-- Submit -->
							<div class="mb-3">
								<button type="submit" class="btn btn-success w-100 add-user" name="submit">Add User</button>
							</div>
							
						</form>	
					
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				  </div>
				</div>
			  </div>
			</div>

			
			
			<!-- Settings -->
			<button type="button" class="btn btn-primary mt-3 w-100 btn-settings" value="settings">Settings</button>
			
			
			
			
			
						
			
		</div>
		
	</div>
</div>


<script>


location.hash = 'confirmed&1'; // set default url hash


$('.page-1').addClass('active'); // pagination pagenumber 1 default active

// Switch Settings content
$('.btn-settings').on('click', function () {				
	$('.userlist').hide();
	$('.settings').show();
	location.hash = $(this).val();
	$('.pagination').hide();
});

function readHash() {
	// grab values form url hash
	var query = location.hash.replace('#', '');
	const queryArray = query.split('&');
	var currentUsers = queryArray[0];
	var currentPage = parseInt(queryArray[1]); // current pagenumber read from hash and make integer
	return {currentUsers: currentUsers, currentPage: currentPage};
	
}



// Confirmed/Unconfirmed users
$('.select').on('click', function (e) {				
	e.preventDefault();
	$('.userlist').show();
	$('.settings').hide();
								
	// Grab values from button click
	var selectedUsers = $(this).val(); // confirmed/unconfirmed value
	
	/*if(selectedUsers == currentUsers) { 
		return false;	// confirmed/unconfirmed selction already displayed	
	}*/
		
	location.hash = $(this).val()+'&1'; // set a hash tag in url with pagenumber 1
				
	$.ajax({
		url: 'process-admin.php',
		data: {
				select_users: 1,
				selectedUsers: selectedUsers			
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
			$('.header').html(data.header); // callback header in h3
			$('ul.pagination').show(); // show pagination			
			$('tbody').html(data.echo); // callback userdata in tbody
			$('ul.pagination').html(data.pagerange); // create new pagerange
			
			// hide/ show triple dots
			if(data.hide == 'jump-prev') {
				$('.jump-prev').hide();
			}
			else if(data.hide == 'jump-next') {
				$('.jump-next').hide();
			}
			var activepage = data.pagenumber;
			$('.page-link').removeClass("active"); // remove current active class
			$('.page-'+activepage).addClass("active"); // make page anchor active
			// disable/enable prev/next  button
			if(data.prev == 'enabled') {
				$('.prev').removeClass('disabled');			
			}
			else if(data.prev == 'disabled') {
				$('.prev').addClass('disabled');
			}
			if(data.next == 'disabled') {
				$('.next').addClass('disabled');
			}
			else if(data.next == 'enabled') {
				$('.next').removeClass('disabled');
			}
			
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
																	
		},
									
	});	
		
});

// Pagination
$(document).on('click', '.page-link', function(e) { 
	e.preventDefault();
	
	var dataHash = readHash();
	var currentUsers = dataHash.currentUsers;
	var currentPage = dataHash.currentPage;
		
	var clickedVal = $(this).data('value'); // value which is clicked and make integer
	
	// switch for clicked values
	let pageNumber;
	switch(clickedVal) {
		case 'prev':
			pageNumber = currentPage - 1;
			break;
		case 'next':
			pageNumber = currentPage + 1;
			break;
		case 'jump-prev':
			pageNumber = currentPage - <?php echo $jumpRecords; ?>;
			break;
		case 'jump-next':
			pageNumber = currentPage + <?php echo $jumpRecords; ?>;
			break;
		default:
			pageNumber = parseInt(clickedVal);														
	}
	
	// prevent pagenumber <1 & > lastpage
	if(pageNumber < 1) {
		pageNumber = 1;
	}
	if(pageNumber > <?php echo $lastPage; ?>) {
		pageNumber = <?php echo $lastPage; ?>
	}
		
	// set new hash in url
	if(currentUsers == 'confirmed') {	
		location.hash = 'confirmed&' + pageNumber; // set new hash with confirmed and pagenumber
	}
	else if(currentUsers == 'unconfirmed') {
		location.hash = 'unconfirmed&' + pageNumber; // set new hash with unconfirmed and pagenumber
	}
	
	$.ajax({
		url: "process-admin.php",
		data: {
			pagination: "1",
			pageNumber: pageNumber,
			currentUsers: currentUsers			
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
			$('tbody').html(data.echo); // callback userdata in tbody
			$('ul.pagination').html(data.pagerange); // create new pagerange
			
			// hide/ show triple dots
			if(data.hideprev == '1') {
				$('.jump-prev').hide();
			}
			if(data.hidenext == '1') {
				$('.jump-next').hide();
			}
			var activepage = data.pagenumber;
			$('.page-link').removeClass("active"); // remove current active class
			$('.page-'+activepage).addClass("active"); // make page anchor active
			// disable/enable prev/next  button
			if(data.prev == 'enabled') {
				$('.prev').removeClass('disabled');			
			}
			else if(data.prev == 'disabled') {
				$('.prev').addClass('disabled');
			}
			if(data.next == 'disabled') {
				$('.next').addClass('disabled');
			}
			else if(data.next == 'enabled') {
				$('.next').removeClass('disabled');
			}
			
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner		
														
		},
		
	});

});

// Delete user
$(document).on('click', '.delete-user', function() { 

	var file_id = $(this).closest("tr").find(".id").data('value'); // grab id from data value
	
	$.ajax({
		url: "process-admin.php",
		type: "POST",
		dataType: "json",
		data: {
			delete_user: "1",
			file_id : file_id						
		},
		success: function(data) {
			if (data.id != '') { // file found 
				var user_id = data.id;
				$("#" + user_id).closest('tr').remove(); // remove td from DOM
			}			
			$('.response').html(data.echo);							
		}
		
	});
});


// Search
$('.search').on('click', function (e) {				
	e.preventDefault();
	
	var dataHash = readHash();
	var currentUsers = dataHash.currentUsers;

	var search = $('.search-input').val();  // search input val
			
	$.ajax({
		url: 'process-admin.php',
		data: {
				search_user: 1,
				currentUsers: currentUsers,
				search: search				
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
			$('.header').html(data.header); // change header test
			$('tbody').html(data.echo); // put search data in tbody
			$('.page-link').removeClass("active"); // remove current active class
			$('.response').html(data.warning);
			$('ul.pagination').hide(); // hide pagination
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner & pagination										
		},
									
	});	
		
});

// Add User Manually
$('#addUser').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from form input
	var username = $('.add-username').val();  // username user
	var password =  $('.add-password').val();  // password user
	var email =  $('.add-email').val();  // email user
			
	$.ajax({
		url: 'process-admin.php',
		data: {
				add_user: 1,
				username: username,
				password: password,
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
			$('tbody').prepend(data.add); // add tr new user in tbody
			$('.response').html(data.echo).hide().fadeIn('slow'); // success in div .response
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
														
		},
									
	});	
		
});

// Admin Login Settings
$('#loginsettings').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from form input
	var admin_username = $('.admin-username').val();
	var admin_new_password = $('.admin-new-password').val();
	var admin_email = $('.admin-email').val();
			
	$.ajax({
		url: 'process-admin.php',
		data: {
				admin_login: 1,
				admin_username: admin_username,
				admin_new_password: admin_new_password,
				admin_email: admin_email
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
			$('.response').html(data.echo); //
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
														
		},
									
	});	
		
});

// Email Settings Users
$('#emailsettings').on('submit', function (e) {				
	e.preventDefault();
							
	// Grab values from form input
	var from_name = $('.from-name').val();
	var from_email = $('.from-email').val();
	var smtp_host = $('.smtp-host').val();
	var smtp_port = $('.smtp-port').val();
			
	$.ajax({
		url: 'process-admin.php',
		data: {
				admin_emailsettings: 1,
				from_name: from_name,
				from_email: from_email,
				smtp_host: smtp_host,
				smtp_port: smtp_port				
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
			$('.response').html(data.echo); //
			$('.alert').delay(5000).fadeOut('slow'); // auto-close alert after 5 sec
			$('.modal').modal('hide'); // close modal
			$('.overlay-spinner').hide(); // hide spinner
														
		},
									
	});	
		
});

// reveal password
$(".reveal-passw").on('click',function() {
	var pwd = $(".admin-new-password");
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





