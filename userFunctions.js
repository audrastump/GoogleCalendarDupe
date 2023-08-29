//loading in jquery
let script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
document.getElementsByTagName('head')[0].appendChild(script);

//checks to see if user is logged in
function checkLogin(event) {
    let loggedin = false;
    const check = {'loggedin':loggedin};
    fetch("checklogin.php", {
        method: 'POST',
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(check => {
            //if the user is not logged in
            if (check.loggedin ==false) {
                $("#calendarGrid").hide();
                $("#logout").hide();
                $("#login").show();
                $("#register").show();
                $("#personData").hide();
                $("#addEvent").hide();
                $("#deleteEvent").hide();
                $("#edit").hide();
                $("#share").hide();
                $("#searchEvents").hide();
                $("#searchResult").hide();
                $("#deleteEvent").hide();
            }
            else{
                //if the user is logged in
                $("#calendarGrid").show();
                $("#logout").show();
                $("#login").hide();
                $("#register").hide();
                $("#personData").show();
                $("#addEvent").show();
                $("#edit").show();
                $("#share").show();
                $("#searchResult").hide();
                $("#deleteEvent").show();
                
            }
           
        })
        .catch(err => (err.message));
}
//we weant to do this ALL THE TIME
document.addEventListener("DOMContentLoaded", checkLogin, false);

//adds events
function addEvent(event) {
    //getting our details from the php file
    let title = document.getElementById("eventName").value;
    let time = document.getElementById("eventTime").value;
    let day = document.getElementById("day").value;
    let month = document.getElementById("month").value;
    let year = document.getElementById("year").value;
    let data = { 'title': title, 'day': day, 'month': month,'year':year, 'time': time, 'token': document.getElementById("csrf_token").value};
    fetch("addevent.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
        
            if (data.success) {
                updateCalendar();
                alert(data.message);
                console.log(data.message);
            }
            else {
                alert("Could not add event: " + data.message);
                console.log(data.message);
            }
            //resetting our form values
            document.getElementById('eventName').value = "";
            document.getElementById('day').value = "";
            document.getElementById('month').value = "";
            document.getElementById('year').value = "";
            document.getElementById('eventTime').value = "";
        })
        .catch(err => console.error(err));
        //gotta update the calendar
        updateCalendar();
        

}
document.getElementById("add_event_button").addEventListener("click", addEvent, false); // Bind the AJAX call to button click

function login(event) {
    const username = document.getElementById("username").value; // Get the username from the form
    const password = document.getElementById("password").value; // Get the password from the form

    // pass in the post data
    const data = { 'username': username, 'password': password };
    let loginToken = null;
    fetch("login.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("You've been logged in");
                alert("Login in success!");
                loginToken = data.token;
                $("#calendarGrid").show();
                $("#logout").show();
                $("#login").hide();
                $("#register").hide();
                $("#personData").show();
                $("#addEvent").show();
                $("#edit").show();
                $("#share").show();
                $("#searchEvents").show();
                $("#deleteEvent").show();
                $("#searchResult").show();
                //store our token and update calendar
                sessionStorage.setItem("token", data.token);
                document.getElementById("csrf_token").value=data.token;
                updateCalendar();
            } else {
                console.log("login fail");
                alert(data.message);
                console.log(data.message);
            }
        })
    document.getElementById("csrf_token").value=loginToken;
    document.getElementById('username').value = "";
    document.getElementById('password').value = "";
}
document.getElementById("login_button").addEventListener("click", login, false); // Bind the AJAX call to button click
//register a new user
function register(event) {
    const newUsername = document.getElementById("new-username").value; // Get the username from the form
    const newPassword = document.getElementById("new-password").value; // Get the password from the form

    // pass in our post data
    const data = { 'new-username': newUsername, 'password': newPassword };

    fetch("register.php", {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            data.success ? alert("You've been signed up!") : alert(`You failed to sign up: ${data.message}`);
        })
        .catch(err => console.error(err));
//resetting our username and password form 
    document.getElementById('new-username').value = "";
    document.getElementById('new-password').value = "";
}
document.getElementById("register_button").addEventListener("click", register, false); // Bind the AJAX call to button click
//logs a user out
function logout(event) {
    fetch("logout.php", {
        method: 'GET',
        headers: { 'content-type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                clearCalendar();
                //remove our session variables
                window.sessionStorage.removeItem("username");
                window.sessionStorage.removeItem("token");
                alert("log out successful");
                $("#calendarGrid").hide();
                $("#logout").hide();
                $("#login").show();
                $("#register").show();
                $("#personData").hide();
                $("#addEvent").hide();
                $("#deleteEvent").hide();
                $("#edit").hide();
                $("#share").hide();
                $("#searchEvents").hide();
                $("#searchResult").hide();
                
                checkLogin();
            }
            else{
                alert("you could not be logged out");
            }
        })
        .catch(err => (err.message));
}
document.getElementById("logout_button").addEventListener("click", logout, false); // Bind the AJAX call to button click

//getting the token to stay when reloading screen
window.onload = fixToken();
function fixToken() {
    let data = sessionStorage.getItem("token");
    document.getElementById("csrf_token").value = data;
};

