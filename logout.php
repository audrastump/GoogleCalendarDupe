<?php
//header for each php file
    header("Content-Type: application/json");
    ini_set("session.cookie_httponly", 1);
    session_start();
    //unset our session variables and destroy session
    unset($_SESSION['username']);
    unset($_SESSION['token']);
    session_unset();
    session_destroy();
    echo json_encode(array(
  		"success" => htmlentities(true),
      "message" => htmlentities("You have successfully log out!")
  	));
    exit;
?>