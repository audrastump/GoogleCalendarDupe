<?php
//validating our mysql connection
$mysqli = new mysqli('localhost', 'mod5user', 'mod5pass', 'module5');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>