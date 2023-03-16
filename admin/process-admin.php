<?php
include 'session-admin.php';
include '../includes/encryption.php';

$result = [];

// ADMIN LOGIN SETTINGS
if( isset($_POST['admin_login']) ) {
			
	$username = $_POST['admin_username'];
	$email = $_POST['admin_email'];
	$encrypted_email = openssl_encrypt($email, $ciphering, $encryption_key, $options, $encryption_iv);
	
	// check empty input
	if( $username == '' || $email == '' ) { 
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
	
	// grab current password 
	$adminLoginfile = '../data/admin/login.json';
	$adminloginObj[] = json_decode(file_get_contents($adminLoginfile), true); 
	foreach($adminloginObj as $key => $val) {
		$CurrentAdminPassword = $val['password'];
	}
	
	
	if(!empty($_POST['admin_new_password'])) { // new password is set
		$password = $_POST['admin_new_password'];
		$hash_password = password_hash($password, PASSWORD_DEFAULT);
	}
	else {
		$hash_password = $CurrentAdminPassword; // save the current password 
	}
	
	
	// json data
	$data = [];
	$data['username'] = $username;
	$data['password'] = $hash_password; 
	$data['email'] = $encrypted_email;
	
	$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	file_put_contents($adminLoginfile,$jsonData);
	
	$result['echo'] = '<div class="alert alert-success alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Logindata saved!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
	echo json_encode($result);

	
}


// ADMIN EMAIL SETTINGS
if( isset($_POST['admin_emailsettings']) ) {
	
	$from_name = $_POST['from_name'];
	$from_email = $_POST['from_email'];
	$smtp_host = $_POST['smtp_host'];
	$smtp_port= $_POST['smtp_port'];

	
	$adminEmailsettingsfile = '../data/emailsettings.json';
	
	// json data
	$data = [];
	$data['from_name'] = $from_name;
	$data['from_email'] = $from_email; 
	$data['smtp_host'] = $smtp_host;
	$data['smtp_port'] = (int)$smtp_port; //integer
	
	$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	file_put_contents($adminEmailsettingsfile,$jsonData);
	
	$result['echo'] = '<div class="alert alert-success alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Emailsettings saved!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
				
	echo json_encode($result);
	
	
}

// PAGINATION
if( isset($_POST['pagination']) || isset($_POST['select_users']) ) {
	if( empty($_POST['pageNumber']) ) { // in this case, confirmed/unconfirmed button is clicked 
		$pageNumber = 1;		
	}
	else {
		$pageNumber = (int)$_POST['pageNumber'];
	}
	
	$currentUsers = $_POST['currentUsers'];
	$selectedUsers = $_POST['selectedUsers'];
	
	if($currentUsers == 'confirmed' || $selectedUsers == 'confirmed') {
		$allFiles = glob('../data/users/*.json'); // array with all confirmed json files
		$result['header'] = 'Confirmed users';		
	}
	else {
		$allFiles = glob('../data/users/unconfirmed/*.json'); // array with all unconfirmed json files 
		$result['header'] = 'Unconfirmed users';
	}
	
	include 'pagination.php';
	if(!$onePage) {
		// change current pagination
		$result['pagerange'] = '<li class="page-item disabled prev"><a class="page-link" data-value="prev" href="javascript:void(0);">Prev</a></li>';
		$result['pagerange'] .= '<li class="page-item"><a class="page-link page-1 active" data-value="1" href="javascript:void(0);">1</a></li>';
		if($pagination) {
			$result['pagerange'] .= '<li class="page-item jump-prev"><a class="page-link" data-value="jump-prev" href="javascript:void(0);">...</a></li>';
		}
		
		for($page = $newStart; $page <= $newEnd; $page++) {
			$result['pagerange'] .= '<li class="page-item flex"><a class="page-link page-'.$page.'" data-value="'.$page.'" href="javascript:void(0);">'.$page.'</a></li>';		
		}
		
		if($pagination) {
			$result['pagerange'] .= '<li class="page-item jump-next"><a class="page-link" data-value="jump-next" href="javascript:void(0);">...</a></li>';
		}
		if($lastPage != 1) {		
			$result['pagerange'] .= '<li class="page-item"><a class="page-link page-'.$lastPage.'" data-value="'.$lastPage.'" href="javascript:void(0);">'.$lastPage.'</a></li>';
		}
		$result['pagerange'] .= '<li class="page-item next"><a class="page-link" data-value="next" href="javascript:void(0);">Next</a></li>';
		
	}


	$i = 0;
	$result['echo'] = '';
	
	
	foreach($allFiles as $singleFile) {
		$recordsObj[] = json_decode(file_get_contents($singleFile), true);
	}
	if( empty($recordsObj) ) {
		$result['echo'] = '<div class="text-danger">No files found!</div>';
		echo json_encode($result);
		exit;
	}
	// sort array by username, make username lowercase
	$key_values = array_column($recordsObj, 'username'); // sort by username
	array_multisort( array_map('strtolower', $key_values), SORT_ASC, $recordsObj );
	
	// calculate records pagination	
	if($pageNumber != 1) {
		$startRecord = ($pageNumber - 1) * $records;		
	}
	$endRecord = $startRecord + $records;
	
	foreach($recordsObj as $key => $val) {		
		$i++;
		
		$id = $val['id'];
		$username = $val['username'];
		$password = $val['password'];
		$decrypted_email = openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv);
		
		if($i > $startRecord && $i <= $endRecord) {
			$result['echo'] .= '<tr>';
			$result['echo'] .= 		'<th scope="row">'.$i.'</th>';
			$result['echo'] .= 		'<td id="'.$id.'" class="id" data-value="'.$id.'">'.$id.'</td>';
			$result['echo'] .= 		'<td>'.$username.'</td>';
			$result['echo'] .= 		'<td>'.$decrypted_email.'</td>';
			$result['echo'] .= 		'<td class=""><i class="fa-solid fa-trash text-danger delete-user"></i></td>';
			$result['echo'] .= '<tr>';
		}
		
							
	}
	
	$result['pagenumber'] = $pageNumber;
	
	echo json_encode($result);
		
			
}

// CONFIRMED/UNCONFIRMED USERS
/*if( isset($_POST['select_users']) ) {
	
	
	$cu = $_POST['cu'];
	if($cu == 'confirmed') {
		$allFiles = glob('../data/users/*.json'); // array with all json files 
		$result['header'] = 'Confirmed users';
		
	}
	elseif($cu == 'unconfirmed') {
		$allFiles = glob('../data/users/unconfirmed/*.json'); // array with all json files in unconfirmed map
		$result['header'] = 'Unconfirmed users';
	}
		
	foreach ($allFiles as $singleFile) {
		$selectObj[] = json_decode(file_get_contents($singleFile), true); //php assoc array
	}
	// sort array
	//$key_values = array_column($selectObj, 'username'); // sort by username
	//array_multisort($key_values, SORT_ASC, $selectObj);
	
	if( empty($selectObj) ) {
		$result['echo'] = '<div class="text-danger">No files found!</div>';
		echo json_encode($result);
	}
	else {
						
		// insert pagination
		include 'pagination.php';
		
		$result['echo'] = '';
		$i = 0;			
		foreach($selectObj as $key => $val) {
			$i++;
			
			$id = $val['id'];
			$username = $val['username'];
			$password = $val['password'];
			$decrypted_email = openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv);
			
			if($i > $startRecord && $i <= $endRecord) {			
				$result['echo'] .= '<tr>';
				$result['echo'] .= 		'<th scope="row">'.$i.'</th>';
				$result['echo'] .= 		'<td id="'.$id.'" class="id" data-value="'.$id.'">'.$id.'</td>';
				$result['echo'] .= 		'<td>'.$username.'</td>';
				$result['echo'] .= 		'<td>'.$decrypted_email.'</td>';
				$result['echo'] .= 		'<td class=""><i class="fa-solid fa-trash text-danger delete-user"></i></td>';
				$result['echo'] .= '<tr>';
			}											
		}
						
		echo json_encode($result);
			
	}
	
}*/

// DELETING USERS
if( isset($_POST['delete_user']) ) {

	$file_id = $_POST['file_id'];
	if( file_exists('../data/users/unconfirmed/'.$file_id.'.json') ) {
		
		$userFile = '../data/users/unconfirmed/'.$file_id.'.json'; 
		$userObj[] = json_decode(file_get_contents($userFile), true); 
		foreach($userObj as $key => $val) {
			$username = $val['username'];
		}
		
		
		unlink('../data/users/unconfirmed/'.$file_id.'.json');
		$result['echo'] = '<div class="alert alert-success flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;<b>'.$username.'</b> deleted!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		$result['id'] = $file_id;	
	}
	elseif( file_exists('../data/users/'.$file_id.'.json') ) {
		
		$userFile = '../data/users/'.$file_id.'.json'; 
		$userObj[] = json_decode(file_get_contents($userFile), true); 
		foreach($userObj as $key => $val) {
			$username = $val['username'];
		}
		
		unlink('../data/users/'.$file_id.'.json');
		$result['echo'] = '<div class="alert alert-success flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;<b>'.$username.'</b> deleted!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		$result['id'] = $file_id;
		// unset Session of user
		session_start();
		$_SESSION['id'] = $file_id;
		unset($_SESSION['id']);
	}
	else {
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
					<b>&excl;</b>&nbsp;User does not exist anymore!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
			
	}
	
	echo json_encode($result);

}

// 	SEARCH USERNAME OR EMAIL
if( isset($_POST['search_user']) ) {
	
	$currentUsers = $_POST['currentUsers'];
	$search = $_POST['search'];
	$result['header'] = 'Search results for: <i>'.$search.'</i> in '.ucfirst($currentUsers).' users'; // for changing header 

	if($currentUsers == 'settings') {
		$result['warning'] = '<div class="alert alert-danger alert-dismissible flyup w-100">
					<b>&excl;</b>&nbsp;Select first Confirmed or Unconfirmed when search!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
		echo json_encode($result);
		exit;
	}
	elseif($currentUsers == 'confirmed') {
		$allFiles = glob('../data/users/*.json'); // array with all json files 
	}
	else {
		$allFiles = glob('../data/users/unconfirmed/*.json'); // array with all json files in unconfirmed map
	}
	
	foreach ($allFiles as $singleFile) {
		$searchObj[] = json_decode(file_get_contents($singleFile), true); 
	}
	// sort array by username; make username lowercase
	$key_values = array_column($searchObj, 'username'); // sort by username
	array_multisort( array_map('strtolower', $key_values), SORT_ASC, $searchObj );
	
	$i = 0;
	foreach($searchObj as $key => $val) {
		$i++;
		if( $search == $val['username'] || $search == strtolower($val['username']) || $search == openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv)) {
			$id = $val['id'];
			$username = $val['username'];
			$password = $val['password'];
			$decrypted_email = openssl_decrypt($val['email'], $ciphering, $decryption_key, $options, $decryption_iv);
			
			
			$result['echo'] = '<tr>';
			$result['echo'] .= 		'<th scope="row">'.$i.'</th>';
			$result['echo'] .= 		'<td id="'.$id.'" class="id" data-value="'.$id.'">'.$id.'</td>';
			$result['echo'] .= 		'<td>'.$username.'</td>';
			$result['echo'] .= 		'<td>'.$decrypted_email.'</td>';
			$result['echo'] .= 		'<td class=""><i class="fa-solid fa-trash text-danger delete-user"></i></td>';
			$result['echo'] .= '<tr>';
			echo json_encode($result);
			$match = true;
			break;
			
		}
		else {
			$match = false;
		}
		
	
		
	}
	// no match for username or email found
	if(!$match) {
		$result['echo'] = '<div class="text-danger">Username or Email not found</div>';
		echo json_encode($result);
	}
	
	
}



// ADD USER MANUALLY
if( isset($_POST['add_user']) ) {
	
	$id = 'id_'.uniqid(); // store a unique id for user
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
	$allFiles = glob('../data/users/*.json'); // array with all json files
								
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
	
	$registerfile = '../data/users/'.$id.'.json';
	
	if(is_file($registerfile)) {	
		$result['echo'] = '<div class="alert alert-danger flyup alert-dismissible w-100">
				<b>&excl;</b>&nbsp;Username already exists!
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';
	
	}
	else {
		$jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		file_put_contents($registerfile,$jsonData);
		
		$result['echo'] = '<div class="alert alert-success flyup alert-dismissible w-100">
					<b>&check;</b>&nbsp;<b>'.$username.'</b> added successful!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>';
	}
	
	echo json_encode($result);

	
}


?>