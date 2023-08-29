<?php
//header for each php file
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json"); 
//getting our new event info
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$json_obj = json_decode($json_str, true);
$title=$mysqli->real_escape_string($json_obj['title']);
$time=$mysqli->real_escape_string($json_obj['time']);
$day=$mysqli->real_escape_string($json_obj['day']);
$month=$mysqli->real_escape_string($json_obj['month']);
$year=$mysqli->real_escape_string($json_obj['year']);
$token = $json_obj['token'];
//token validation
if (!hash_equals($_SESSION["token"], $token)){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("Request forgery detected.")

    ));
    exit(); 
}
//session username validation
if(!isset($_SESSION['username'])){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("token invalid")
    ));
    exit;
}
else{
    $username=$_SESSION['username'];
    //if user did not enter an event info
    if(empty($title) || empty($time) || empty($year)|| empty($month)){
        echo json_encode(array(
            "success"=> htmlentities(false),
            "message"=> htmlentities("empty inputs" )
        ));
        exit();
    }

    //title validation
    if( preg_match('/[|\#$%*+<>=?^_`{}~]+/', $title) ){
        echo json_encode(array(
            "success" => htmlentities(false),
            "message" => htmlentities("invalid title")
        ));
        exit();
    }
    //inserting into the events
    $stmt=$mysqli->prepare("INSERT into events(username, title, day, month, year, time) values(?,?,?,?,?,?)");
    if (!$stmt) {
        echo json_encode(array(
            "success" => htmlentities(false),
            "message" => htmlentities("error inserting into database")
        ));
        exit;
    }
    $stmt->bind_param('ssiiis',$username,$title,$day,$month, $year,$time);
    $stmt->execute();
    echo json_encode(array(
        "success" => htmlentities(true),
        "message" => htmlentities("event insertion successful")
    ));
    $stmt->close();
}
?>