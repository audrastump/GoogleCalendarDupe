<?php
//header for each php file
require 'database.php';
ini_set("session.cookie_httponly", 1);
session_start();
header("Content-Type: application/json");

//getting all our edited event information
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
$new_title = $mysqli->real_escape_string((string)$json_obj['newEventTitle']);
$new_day = $mysqli->real_escape_string((string)$json_obj['newDay']);
$new_month = $mysqli->real_escape_string((string)$json_obj['newMonth']);
$new_year = $mysqli->real_escape_string((string)$json_obj['newYear']);
$new_time = $mysqli->real_escape_string((string)$json_obj["newTime"]);
$event_id = $mysqli->real_escape_string($json_obj["newEventID"]);
$token = $json_obj['token'];
//checking for token validation
if (!hash_equals($_SESSION["token"], $token)){
    echo json_encode(array(
        "success" => false,
        "message" => "Request forgery detected.",

    ));
    exit; 
}
//checking that username is set
if (!isset($_SESSION['username'])) {
    echo json_encode(array(
        "loggedin" => htmlentities((bool)false)
    ));
    exit;
} else {
    $username = $_SESSION['username'];
    //if any variables are empty, returning error array
    if (empty($new_title) || empty($new_time) || empty($new_month) || empty($new_year) || empty($new_day) || empty($event_id)) {
        echo json_encode(array(
            "success" => htmlentities((bool)false),
            "message" => htmlentities((string)"empty inputs")
        ));
        exit;
    } else {
        //making sure event exists
        $stmt = $mysqli->prepare("SELECT COUNT(*) from events where id=? AND username=?");
        
        $stmt->bind_param('is', $event_id, $username);
        if (!$stmt) {
            echo json_encode(array(
                "success" => htmlentities((bool)false),
                "message" => htmlentities((string)"ERROR checking database")
            ));
            exit;
        }
        $stmt->execute();

        
        $stmt->bind_result($cnt);
        $stmt->fetch();
        //if there is no event, return error array
        if ($cnt == 0) {
            echo json_encode(array(
                "success" => htmlentities(false),
                "message" => htmlentities("No event exists")
            ));
            exit;
        }
        $stmt->close();
        //updating event in mysql table
        $stmt = $mysqli->prepare("UPDATE events set title=?, day = ?, month = ?, time = ? where id=? AND username = ?");
        if (!$stmt) {
            echo json_encode(array(
                "success" => htmlentities(false),
                "message" => htmlentities("Could not insert into database")
            ));
            exit;
        } else {
            $stmt->bind_param('siisis', $new_title, $new_day, $new_month, $new_time, $event_id, $username);
            //if can execute, return success
            if($stmt->execute()){
                echo json_encode(array(
                    "success" => htmlentities(true)
                    
                ));
            }else{
                echo json_encode(array(
                    "success" => htmlentities(false)
                ));
            }
            
            $stm1t->close();
        }
        
       

    }
}
?>