<?php
// load phpmailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$adminEmailsettingsfile = 'data/emailsettings.json';
$adminEmailsettingsObj[] = json_decode(file_get_contents($adminEmailsettingsfile), 1); 
foreach($adminEmailsettingsObj as $key => $val) {
	$from_name = $val['from_name'];
	$from_email = $val['from_email'];
	$smtp_host = $val['smtp_host'];
	$smtp_port = $val['smtp_port'];
}


//////////////////////////////////////////////////////////////////////////////////
// email settings




// mail to yourself if someone has subscribed
$confirm_mail_to_you =  false; // 'true' or 'false'. Set to 'true' if you want a confirmation someone has subscribed. Else, set to 'false' 
$youremail = 'jonhdoe@email.org'; // if above set to 'true', fill in your email-address
$yourname = 'John'; // if above set to 'true', fill in your name



$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

include 'includes/encryption.php';

$result = []; // json array

// REGISTER
if( isset($_POST['register']) ) {
	
	$id = 'id_'.uniqid(); // store a unique id for each user
	$username = $_POST['username'];
	$password = $_POST['password'];
	$email = $_POST['email'];
	$token = bin2hex(openssl_random_pseudo_bytes(16));
	// check empty input
	if( $username == '' || $password == '' || $email == '' ) { 
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Fill in all the fields!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	// check clean username; only alphanmeric chars allowed
	if(!ctype_alnum($username)) {
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Only alphanumeric characters allowed and no spaces in Username!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	// check valid email
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Invalid emailaddress!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	
	// check if username & email already exists
	$allFiles = glob('data/users/*.json'); // array with all json files
								
	foreach ($allFiles as $singleFile) {
		$registerObj[] = json_decode(file_get_contents($singleFile), true); 
	}
	foreach($registerObj as $key => $val) {
		// check username
		if($username == $val['username']) {
			$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Username already exists!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
			echo json_encode($result);
			exit;
		}
		// check email
		if( $email == openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv) ) { 
			$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Email already exists!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
			echo json_encode($result);
			exit;
		}
	}

	
		
	// hash password
	$hash_password = password_hash($password, PASSWORD_DEFAULT);	
	// encrypt email
	$encrypted_email = openssl_encrypt($email, $ciphering, $encryption_key, $options, $encryption_iv);

	// json data
	$data = [];
	$data['id'] = $id;
	$data['username'] = $username;
	$data['password'] = $hash_password; 
	$data['email'] = $encrypted_email;
	$data['token'] = $token;	
	
	$registerfile = 'data/users/unconfirmed/'.$id.'.json';
		
	if(is_file($registerfile)) {			
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
				<b>&excl;</b>&nbsp;Username already exists!
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';
	}
	else {
		$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		file_put_contents($registerfile,$jsonData);
		
			
		/* sending confirmation link to emailaddress*/
		// if no url string, request comes from register form. First send confirm link via email
		if ( parse_url($url, PHP_URL_QUERY) == '' ) {
															
			// Send confirmation email for registering						
			$mail = new PHPMailer;
			//$mail->IsSMTP();								//Sets Mailer to send message using SMTP
			$mail->Host = $smtp_host;		                //Sets the SMTP hosts of your Email hosting, this for Godaddy
			$mail->Port = $smtp_port;						//Sets the default SMTP server port
			$mail->setFrom($from_email, $from_name);		//Sets the From email & name of the message
			$mail->IsHTML(true);							//Sets message type to HTML				
			$mail->Subject = "Confirm registration";		//Sets the Subject of the message
			$mail->addAddress($email, $username);           //Adds a "To" address 
			if($confirm_mail_to_you) {
				$mail->addBCC($youremail, $yourname);           //send yourself email when some has registred
			}
											
			$confirm_text = 'You received this email because you have registered .<br />Please confirm your email by clicking on the link below:<br />';
			$confirm_urlpath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
									
			$mail->Body = $confirm_text.'<a href="'.$confirm_urlpath.'?id='.urlencode($id).'&token='.urlencode($token).'">Confirm your email</a>';	
		
			if($mail->send()) {	
											
				$result['echo'] = '<div class="alert alert-info alert-dismissible flyup">
									<b>&check;</b>&nbsp;A confirmation link is sent to:<b> '.$email.'</b><br />
									Click on the link in the email to confirm your registration!
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>';											
			} 
			else {
				$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup">
									<b>&excl;</b>&nbsp;Mailer Error (' . htmlspecialchars($email) . ') ' . $mail->ErrorInfo . '
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>';
			}
		
			
		}		
		
	}
	
	echo json_encode($result);
	
}


// CONFIRM REGISTRATION
if ( parse_url($url, PHP_URL_QUERY) != '' ) {
	$url_id = urldecode($_GET['id']);
	$url_token = urldecode($_GET['token']);

	$toconfirmFile = 'data/users/unconfirmed/'.$url_id.'.json';
	if( !file_exists($toconfirmFile) ) { // if file does not exist, user probably registered for 2nd time
		$redirect = "login.php?already-registered=1";
		header("Location: $redirect");
		exit;
	}
	$uncObj[] = json_decode(file_get_contents($toconfirmFile), true);

	foreach($uncObj as $key => $val) {
		$toconfirm_id = $val['id'];
		$toconfirm_token = $val['token'];
		$toconfirm_user = $val['username'];
	}
	// check if GET parameters correspond with the ones in the file; must be identical
	if($url_id == $toconfirm_id && $url_token == $toconfirm_token) { 
		// move file from data/users/unconfirmed folder to data/users folder
		rename('data/users/unconfirmed/'.$url_id.'.json' , 'data/users/'.$url_id.'.json');
		// redirect to loginpage
		$redirect = "login.php?confirmed=1";
				
		header("Location: $redirect");
		exit;
	}
	
}

// FORGOTPASSW
if( isset($_POST['forgotpass']) ) {
	
	// post values
	$id = $_POST['id'];
	$token = $_POST['token'];
	$password_1 = $_POST['password_1'];
	$password_2 = $_POST['password_2'];
		
	$userfile = 'data/users/'.$id.'.json';

	// check empty input
	if( $id == '' || $token == '' || $password_1 == '' || $password_2 == '' ) { 
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;Fill in all the fields!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;		
	}
	// check if 2 input passwords are identical
	if($password_1 != $password_2) {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;Passwords do not match!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	// check if userfile exists
	if( file_exists($userfile) ) {
		$userfileObj[] = json_decode(file_get_contents($userfile), true); // read json file and create php assoc array
		foreach($userfileObj as $key => $val) {
			if( $id == $val['id'] && $token == $val['token'] ) { // if ids and tokens identical

				$hash_password = password_hash($password_1, PASSWORD_DEFAULT); // hashed password
				
				// collect data new userfile
				$data = [];
				$data['id'] = $val['id'];
				$data['username'] = $val['username'];
				$data['password'] = $hash_password; 
				$data['email'] = $val['email'];
				$data['token'] = bin2hex(openssl_random_pseudo_bytes(16)); // set a new token for the user
				
				// overwrite userfile with updated password
				$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				file_put_contents($userfile,$jsonData); // write data to new file
							
				$result['echo'] = '<div class="alert alert-success flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;New password is set!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
				$result['loginlink'] = '<a href="login.php" class="btn btn-info w-100">Login</a>';					
				
				echo json_encode($result);
				exit;
			}
			else {
				$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;Link expired!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
				echo json_encode($result);
				exit;
			}
		}
	}
	else {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;User does not exists!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	
	
}


// REQUEST USERNAME/PASSWORD
if( isset($_POST['requestpass']) ) {
		
	$email = $_POST['email'];

	// check valid email
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;Invalid emailaddress!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}	
	
	// check if username & email already exists
	$allFiles = glob('data/users/*.json'); // array with all json files
								
	foreach ($allFiles as $singleFile) {
		$requestPassObj[] = json_decode(file_get_contents($singleFile), true); 
	}
	foreach($requestPassObj as $key => $val) {
		// grab email
		if( $email == openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv) ) { 
			$id = $val['id']; // grab id user
			$username = $val['username']; // grab username user
			//$email_user = $val['email_user']; // grab encrypted email user
			//$decrypted_email_user = openssl_decrypt($val['email_user'], $ciphering, $decryption_key, $options, $decryption_iv);
			$token = $val['token']; // grab token user
			$result['echo'] = $email;
			$emailRegistered = true;
			break; 
			exit;
		}
		
	}
	
	if(!$emailRegistered) {
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Email not registered!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	
	
									
	// Send confirmation email for request password						
	$mail = new PHPMailer;
	//$mail->IsSMTP();								//Sets Mailer to send message using SMTP
	$mail->Host = $smtp_host;		                //Sets the SMTP hosts of your Email hosting, this for Godaddy
	$mail->Port = $smtp_port;						//Sets the default SMTP server port
	$mail->setFrom($from_email, $from_name);		//Sets the From email & name of the message
	$mail->IsHTML(true);							//Sets message type to HTML				
	$mail->Subject = "Request Password";		    //Sets the Subject of the message
	$mail->addAddress($email, 'administrator');     //Adds a "To" address 
	if($confirm_mail_to_you) {
		$mail->addBCC($youremail, $yourname);           //send yourself email when someone has requested password
	}
	
						
	$requestpass_text = 'You received this email because you have requested your Username/Password .<br />
						Your Username that corresponds to this email address is: <b>'.$username.'</b><br />
						Click on the link below to set a new Password<br />';
	$requestpass_urlpath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/resetpass.php";
	
		$mail->Body = $requestpass_text.'<a href="'.$requestpass_urlpath.'?id='.urlencode($id).'&username='.$username.'&token='.urlencode($token).'">Reset Password</a>';

	if($mail->send()) {	
		$result['echo'] = '<div class="alert alert-info alert-dismissible flyup">
							<b>&check;</b>&nbsp;An email with your Username <br />and a link to reset your Password is sent to:<br /><b> '.$email.'</b>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>';					
	} 
	else {
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup">
							<b>&excl;</b>&nbsp;Mailer Error (' . htmlspecialchars($email) . ') ' . $mail->ErrorInfo . '
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>';
	}
			
	echo json_encode($result);
			
}	

// NEW EMAIL
if( isset($_POST['newemail']) ) {
	
	// CHECK SESSION 
	include 'includes/session.php';
	
	// post values
	$id = $_POST['id'];
	$new_email = $_POST['new_email'];
	$new_encrypted_email = openssl_encrypt($new_email, $ciphering, $encryption_key, $options, $encryption_iv);
	$password = $_POST['password'];	
	$userfile = 'data/users/'.$id.'.json';
	
	// check if email already exists
	$allFiles = glob('data/users/*.json'); // array with all json files								
	foreach ($allFiles as $singleFile) {
		$dataObj[] = json_decode(file_get_contents($singleFile), true); 
	}
	foreach($dataObj as $key => $val) {		
		// check email
		if( $new_email == openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv) ) { 
			$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Email already exists!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
			echo json_encode($result);
			exit;
		}
	}
	
	// check valid email
	if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Invalid emailaddress!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	
	// check empty input id & password
	if( $id == '' || $password == '' ) { 
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;Fill in all the fields!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;		
	}

	// check if userfile exists
	if( file_exists($userfile) ) {
		$userfileObj[] = json_decode(file_get_contents($userfile), true); // read json file and create php assoc array
		foreach($userfileObj as $key => $val) {
			if( $id == $val['id'] && $password == password_verify($password, $val['password']) ) { // if id & password correspond with the stored ones				
				// collect data and overwrite userfile with new email
				$data = [];
				$data['id'] = $val['id']; // existing id
				$data['username'] = $val['username']; // existing username
				$data['password'] = $val['password'];  // existing password
				$data['email'] = $new_encrypted_email; // new encrypted email
				$data['token'] = bin2hex(openssl_random_pseudo_bytes(16)); // set a new token for the user

				// overwrite userfile with updated email
				$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				file_put_contents($userfile,$jsonData); // write data to new file
							
				$result['echo'] = '<div class="alert alert-success flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;New email is set!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
				$result['renew'] = $new_email; // refresh immediately existing email				
				
				echo json_encode($result);
				exit;
			}
			else {
				$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;Password not correct!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
				echo json_encode($result);
				exit;
			}
		}
	}
	else {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;User does not exists!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	
}

// NEW PASWORD
if( isset($_POST['newpassword']) ) {
	
	// CHECK SESSION 
	include 'includes/session.php';
	
	// post values
	$id = $_POST['id'];
	$old_password = $_POST['old_password'];
	$new_password_1 = $_POST['new_password_1'];
	$new_password_2 = $_POST['new_password_2'];
		
	$userfile = 'data/users/'.$id.'.json';

	// check empty input
	if( $id == '' || $old_password == '' || $new_password_1 == '' || $new_password_2 == '' ) { 
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;Fill in all the fields!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;		
	}
	// check if 2 input passwords are identical
	if($new_password_1 != $new_password_2) {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;Passwords do not match!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	// check if userfile exists
	if( file_exists($userfile) ) {
		$userfileObj[] = json_decode(file_get_contents($userfile), true); // read json file and create php assoc array
		foreach($userfileObj as $key => $val) {
			// compare input values with stored json values
			if( $id == $val['id'] && $old_password == password_verify($old_password, $val['password']))  { // if id and old password match with the stored ones

				$hash_password = password_hash($new_password_1, PASSWORD_DEFAULT); //hashed password
				
				// collect data new userfile
				$data = [];
				$data['id'] = $id;
				$data['username'] = $val['username'];
				$data['password'] = $hash_password; 
				$data['email'] = $val['email'];
				$data['token'] = bin2hex(openssl_random_pseudo_bytes(16)); // set a new token for the user
				
				// overwrite userfile with updated password
				$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
				file_put_contents($userfile,$jsonData); // write data to new file
							
				$result['echo'] = '<div class="alert alert-success flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;New password is set!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
							
				
				echo json_encode($result);
				exit;
			}
			else {
				$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;Old Password not correct!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
				echo json_encode($result);
				exit;
			}
		}
	}
	else {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;User does not exists!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}	
		
	
}



	
// DELETE ACCOUNT
if( isset($_POST['delete_account']) ) {
	
	// CHECK SESSION 
	include 'includes/session.php';
	
	$id = $_POST['id'];
	$token = $_POST['token'];
	
	$userfile = 'data/users/'.$id.'.json';
	
	// check if userfile exists
	if( file_exists($userfile) ) {
		$userfileObj[] = json_decode(file_get_contents($userfile), true); // read json file and create php assoc array
		foreach($userfileObj as $key => $val) {
			if( $id == $val['id'] && $token == $val['token'] ) { // compare sended id & token with the stored ones
				unlink($userfile); // delete userfile
				// unset Session of user
				session_start();
				$_SESSION['id'] = $id;
				unset($_SESSION['id']);
				
				$username = $val['username'];
			}
		}
		$result['echo'] = '<div class="alert alert-success alert-dismissible flyup">
							<b>&check;</b>&nbsp;Your Account with username <b>'.$username.'</b> is removed!
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>';
	}
	else {
		$result['echo'] = '<div class="alert alert-danger alert-dismissible flyup">
							<b>&excl;</b>&nbsp;Account with this username could not be found!
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>';
	}
	
	echo json_encode($result);
	
}

?>


