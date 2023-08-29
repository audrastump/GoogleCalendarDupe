<?php
//header for each php file
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json"); 

//get our event id to delete and username
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$event_id=(int)$mysqli->real_escape_string($json_obj["deletedId"]);
$username = $_SESSION['username'];
$token = $json_obj['token'];

//token validation
if (!hash_equals($_SESSION["token"], $token)){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("Request forgery detected.")

    ));
    exit; 
}

//username validation 
if(!isset($_SESSION['username'])){
    echo json_encode(array(
        "loggedin"=>htmlentities((bool)false)
    ));
    exit;
}
else{
    //making sure that there is an event with that id
    $stmt = $mysqli->prepare("SELECT COUNT(*) from events where id=? AND username=?");
    
    $stmt->bind_param('is', $event_id, $username);
    if (!$stmt) {
        echo json_encode(array(
            "success" => htmlentities(false),
            "message" => htmlentities("Could not check database")
        ));
        exit;
    }
    $stmt->execute();

    
    $stmt->bind_result($cnt);
    $stmt->fetch();
    //return false if no event exists
    if ($cnt == 0) {
        echo json_encode(array(
            "success" =>htmlentities(false),
            "message" => htmlentities("No event exists with that id")
        ));
        exit;
    }
    $stmt->close();
   //otherwise, delete from events
    $stmt=$mysqli->prepare("delete from events where id=? and username=?");
    if (!$stmt) {
        echo json_encode(array(
            "success" => htmlentities(false),
            "message" => htmlentities((string)"ERROR checking database")
        ));
        exit;
    }
    $stmt->bind_param('is',$event_id,$username);
    $stmt->execute();
    echo json_encode(array(
        "success" => htmlentities(true)
    ));
    $stmt->close();


}




?>