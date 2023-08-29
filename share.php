<?php
//header for each php file
require 'database.php';
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
session_start();
//if the session username is empty we want to exit
if (empty($_SESSION["username"])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Not signed in"
    ));
    exit;
} else {
    //otherwise set current user to the session user 
    $username = $_SESSION["username"];
}



//getting our receiver and event id that we want to share
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$event_id=$mysqli->real_escape_string((int)$json_obj["sharedId"]);
$token = $json_obj['token'];
$receiver=$mysqli->real_escape_string((string)$json_obj["recipient"]);
if (!hash_equals($_SESSION["token"], $token)){
    echo json_encode(array(
        "success" => false,
        "message" => "Request forgery detected.",
        "sessiontoken" => $_SESSION["token"],
        "passedintoken"=> $token
    ));
    exit; 
}
//retrieving the event from the logged in user's database
$stmt = $mysqli->prepare("SELECT title, year, month, day, time from events where id = ? AND username = ?");
//if cannot do this
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Could not find an event with that id"
    ));
    exit;
}
$stmt->bind_param('is', $event_id, $username);
//if cannot execute, return error message
if (!$stmt->execute()) {
    echo json_encode(array(
        "success" => false,
        "message" => "Could not find an event with that id"
    ));
    exit;
} else {
    //otherwise, bind the results
    $stmt->bind_result($title, $year, $month, $day, $time);
    $stmt->fetch();
    $stmt->close();

    if (empty($title) || empty($year) || empty($month) || empty($day) || empty ($time)){
        echo json_encode(array(
            "success" => false,
            "message" => "Could not find an event with that id: please check id again",
            "id" => $event_id,
            "username" => $username
        ));
        exit;
    }
    //inserting into the other users database
    $stmt2 = $mysqli->prepare("INSERT into events (year, month, day, time, title, username) VALUES (?, ?, ?,?, ?, ?)");
    if (!$stmt2) {
        echo json_encode(array(
            "success" => false,
            "message" => "Error accessing database"
        ));
        exit;
    }
    else{
        $stmt2->bind_param('iiisss', $year, $month, $day, $time, $title, $receiver);
        if ($stmt2->execute()){
            echo json_encode(array(
                "success" => true
            ));
            $stmt2->close();
            exit;
        }
        else{
            echo json_encode(array(
                "success" => false,
                "message" => "could not execute insertion into database"
            ));
            exit;
        }
    }
    exit;
    
    

}
?>
