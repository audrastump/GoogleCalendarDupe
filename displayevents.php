<?php
//header for each php file
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json"); 
$token = (string)$_SESSION['token'];

//checking that username is set
if(isset($_SESSION['username'])==false){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" =>htmlentities("user not logged in")
    ));
    exit;
}

$username = (string)$_SESSION['username'];
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
//getting all events from user
$stmt = $mysqli->prepare("SELECT title, day, month, year, time, id FROM events where username=?");
if(!$stmt){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" =>htmlentities("error getting data")
    ));
    exit;
}
$stmt->bind_param('s',$username);
$stmt->execute();
$stmt->bind_result($title, $day, $month, $year, $time, $id);
$events= array();
//fetching events from server
while($stmt->fetch()){   
    array_push($events,array(
        'username'=>$username,
        'token' => $token,
        "event_id"=>array(htmlentities($id)),
        "title"=>array(htmlentities($title)),
        "day"=>array(htmlentities($day)),
        "month"=>array(htmlentities($month)),
        "year"=>array(htmlentities($year)),
        "time"=>array(htmlentities($time)),
    ));
}
//if no events yet, return false
if (empty($events)){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" =>htmlentities("no events yet")
    ));
    exit;
}
//otherwise, return events
else{
    echo json_encode($events);
}

$stmt->close();