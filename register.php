<?php
//header for each php file
require 'database.php';
ini_set('session.cookie_httponly', 1);
header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
//getting our content and setting it to variables
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$username = $mysqli->real_escape_string((string)$json_obj['new-username']);
$password = $mysqli->real_escape_string((string)$json_obj['password']);

//if username is wonky, we will reject it
if (!preg_match('/^[\w_\.\-]+$/', $username)) {
	echo json_encode(array(
		"success" => htmlentities(false),
		"message" => htmlentities("Invalid Username")
	));
	exit;
}
//hashing our password 
$pwd_hash = password_hash($password, PASSWORD_DEFAULT);

//checking if the username is taken
$stmt2 = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
if (!$stmt2) {
	echo json_encode(array(
		"success" => htmlentities(false),
		"message" => htmlentities("Can not create new user!")
	));

	exit;
}
$stmt2->bind_param('s', $username);
$stmt2->execute();
$stmt2->bind_result($cnt);
$stmt2->fetch();
$stmt2->close();

if ($cnt == 1) {
	echo json_encode(array(
		"success" => htmlentities(false),
		"message" => htmlentities("User already exists")
	));
	exit;
}
//insert the new username into our users table
$stmt = $mysqli->prepare("insert into users (username, password) values (?, ?)");
if (!$stmt) {
	echo json_encode(array(
		"success" => htmlentities(false),
		"message" => htmlentities("Can not create new user!")
	));
	exit;
}
$stmt->bind_param('ss', $username, $pwd_hash);
$stmt->execute();
$stmt->close();
echo json_encode(array(
	"success" => htmlentities(true)
	
));
