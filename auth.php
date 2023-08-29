<?php
//header for each php file
	require 'database.php';
	header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json
  ini_set("session.cookie_httponly", 1);
  session_start();
 
  if (isset($_SESSION['username'])){
    echo json_encode(array(
			"success" => true,
      "username" => $_SESSION['username'],
      "token" => $_SESSION["token"]
  		));
	
  } else {
    echo json_encode(array(
			"success" => false,
      "message" => "Could not log in"
  		));
  }
  exit;
?>