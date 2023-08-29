<?php
//header for each php file
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json"); 

//getting our searched object and the session username
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$title=$mysqli->real_escape_string(($json_obj['searchName']));
$username=$_SESSION['username'];
//if our key word is not set return
if (empty($title)){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("no title entered")
    ));
    exit;
}
//if the session username is not set, return
if (empty($username)){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("not signed in")
    ));
    exit;
}
//getting all event details from the user where the title matches 
$stmt = $mysqli->prepare("SELECT id, month, day, year, time from events where title= ? AND username = ?");
if(!$stmt){
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("could not prep query")
    ));
    exit;
}  
$stmt->bind_param('ss', $title, $username);
if ($stmt->execute()){
    $stmt->bind_result($id, $month, $day, $year, $time);
    $stmt->fetch();
    //if they are empty, this means could not find event
    if (empty($id) || empty($month) || empty($year) ||empty($day|| empty($time) )){
        echo json_encode(array(
            "success" => htmlentities(false),
            "message" => htmlentities("could not find event")
        ));
    }
    else{
        //returning our information
        echo json_encode(array(
            "success" => htmlentities(true),
            "message" => htmlentities("event found"),
            "day"=>htmlentities($day),
            "month"=>htmlentities($month),
            "year"=>htmlentities($year),
            "id"=>htmlentities($id),
            "title"=>htmlentities($title),
            "time" => htmlentities($time)
        ));
    }
    
}
else{
    echo json_encode(array(
        "success" => htmlentities(false),
        "message" => htmlentities("could not execute request")
    ));
}
                 
?>
