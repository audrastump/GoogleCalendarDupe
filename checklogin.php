<?php
//header for each php file
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json"); 

//checking if username is set for the session
if(!isset($_SESSION['username'])){
    echo json_encode(array(
        "loggedin"=>htmlentities(false),
        "token" => $_SESSION['token'] 
    ));
    exit;
}
else{
    echo json_encode(array(
        "loggedin"=>htmlentities(true),
        "token" => $_SESSION['token'] 
    ));
    exit;
}
?>