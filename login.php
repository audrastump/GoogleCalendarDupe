<?php
//header for each php file
	require 'database.php';
	header("Content-Type: application/json");
	$json_str = file_get_contents('php://input');
	//This will store the data into an associative array
	$json_obj = json_decode($json_str, true);
	$username = (string)$json_obj['username'];
	$pwd_guess = $mysqli->real_escape_string($json_obj['password']);
	$actualhash = password_hash($pwd_guess, PASSWORD_DEFAULT);
	$stmt = $mysqli->prepare("SELECT COUNT(*), password FROM users WHERE username=?");
	// Bind the parameter
	$stmt->bind_param('s', $username);
	if(!preg_match('/^[\w_\.\-]+$/', $username) ){
		echo json_encode(array(
			"success" => htmlentities(false),
			"message" => htmlentities("Username character issue")
		));
		exit;
	}
	$stmt->execute();
	// Bind the results
	$stmt->bind_result($cnt, $pwd_hash);
	$stmt->fetch();
	$stmt->close();
	// Compare the submitted password to the actual password hash
	if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
		// Login succeeded!
		session_start();
		ini_set("session.cookie_httponly", 1);
		$_SESSION['username'] = $username;
		$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // generate a 32-byte random string
		echo json_encode(array(
			"success" => htmlentities(true),
			"username" => htmlentities($_SESSION['username']),
			"token" => $_SESSION['token']
		));
		exit;
	} else{
		echo json_encode(array(
			"success" => htmlentities(false),
			"message" => htmlentities("Incorrect Username or Password")
			
			
		));
		exit;
	}
?>
<?php
