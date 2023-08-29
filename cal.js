//functions given to us
(function () { Date.prototype.deltaDays = function (c) { return new Date(this.getFullYear(), this.getMonth(), this.getDate() + c) }; Date.prototype.getSunday = function () { return this.deltaDays(-1 * this.getDay()) } })();
function Week(c) { this.sunday = c.getSunday(); this.nextWeek = function () { return new Week(this.sunday.deltaDays(7)) }; this.prevWeek = function () { return new Week(this.sunday.deltaDays(-7)) }; this.contains = function (b) { return this.sunday.valueOf() === b.getSunday().valueOf() }; this.getDates = function () { for (var b = [], a = 0; 7 > a; a++)b.push(this.sunday.deltaDays(a)); return b } }
function Month(c, b) { this.year = c; this.month = b; this.nextMonth = function () { return new Month(c + Math.floor((b + 1) / 12), (b + 1) % 12) }; this.prevMonth = function () { return new Month(c + Math.floor((b - 1) / 12), (b + 11) % 12) }; this.getDateObject = function (a) { return new Date(this.year, this.month, a) }; this.getWeeks = function () { var a = this.getDateObject(1), b = this.nextMonth().getDateObject(0), c = [], a = new Week(a); for (c.push(a); !a.contains(b);)a = a.nextWeek(), c.push(a); return c } };

// we will start in october of 2022
let currentMonth = new Month(2022, 9);

//getting our next month and our previous month buttons
document.getElementById("nextMonth").addEventListener("click", function (event) {
  currentMonth = currentMonth.nextMonth();
  updateCalendar();
  window.onload  = clearCalendar();

 
}, false);

document.getElementById("prevMonth").addEventListener("click", function (event) {
  currentMonth = currentMonth.prevMonth();
  updateCalendar();
  window.onload = clearCalendar();

}, false);

let matcher=document.getElementById("calendarGrid").getElementsByClassName("days");
let point = matcher[0].getElementsByClassName("day");
document.addEventListener("load",updateCalendar(),false);

function updateCalendar() {
  //allows us to find the current month and its specific week structure
  for (var i = 0; i < 42; i++) {
    point[i].textContent = null;
  }
  let weeks = currentMonth.getWeeks();
//finding the month title
  let monthTitle = "";
  if (currentMonth.month == 0) {
    monthTitle = "January ";
  } else if (currentMonth.month == 1) {
    monthTitle = "February ";
  } else if (currentMonth.month == 2) {
    monthTitle = "March ";
  } else if (currentMonth.month == 3) {
    monthTitle = "April ";
  } else if (currentMonth.month == 4) {
    monthTitle = "May ";
  } else if (currentMonth.month == 5) {
    monthTitle = "June ";
  } else if (currentMonth.month == 6) {
    monthTitle = "July ";
  } else if (currentMonth.month == 7) {
    monthTitle = "August ";
  } else if (currentMonth.month == 8) {
    monthTitle = "September ";
  } else if (currentMonth.month == 9) {
    monthTitle = "October ";
  } else if (currentMonth.month == 10) {
    monthTitle = "November ";
  } else if (currentMonth.month == 11) {
    monthTitle = "December ";
  }
  //making our calendar structure
  document.getElementById("month_name").innerHTML = monthTitle + "<br><span style='font-size:18px'>" + currentMonth.year + "</span>";
  let dataId = 0;
  for (let w in weeks) {
    let days = weeks[w].getDates();
    for (let d in days) {
      //only display days in this current month
      if (days[d].getMonth() ==currentMonth.month){
        point[dataId].textContent = days[d].toString().split(" ")[2];
      }
      else{
        point[dataId].textContent = "";
      }
      dataId = dataId + 1;
    }

  }
  //displaying each event in a list format 

  const personData = document.getElementById('personData').checked;
  const data = {"personData":personData};

  console.log(data);
  clearCalendar();
  //display will fetch all events associated with a user from php
            fetch("displayevents.php",{
                method: 'POST',
                body: JSON.stringify(data),
                headers: { 'content-type': 'application/json' }
            })
            .then(response => response.json())
                .then(data => {
                    if (data.loggedin != false) {
                        console.log('Success', JSON.stringify(data));
                        //console.log(data);
                        parseEvents(data);
                    }
                    else{
                      $("#calendarGrid").hide();
                      $("#logout").hide();
                      $("#login").show();
                      $("#register").show();
                      $("#personData").hide();
                      $("#addEvent").hide();
                      $("#edit").hide();
                      $("#share").hide();
                      $("#searchEvents").hide();
                      $("#searchResult").hide();
                      $("#deleteEvent").hide();
                    }
                })
              

  }

function parseEvents(response) {
  let jsonData = JSON.parse(JSON.stringify(response));
  //getting all the days that match the day element
  let calendar_days = document.getElementsByClassName("day");
  //clearCalendar();
  //iterating through the days in our calendar
  for (let i = 0; i < calendar_days.length; i++) {
    
      
      //iterating through all the jsonData (returns all events corresponding to this user)
      for (let n = 0; n < jsonData.length; n++) {
        //getting the day number from each day (ex: the 4th would be 4)
      let calendar_date = calendar_days[i].innerHTML;
        //if the calendar_date is the same as the day and the current month and year the calendar is flipped to are the same
        if (+calendar_date < 10){
          calendar_date = calendar_date.slice(1);
        }
          if (calendar_date == jsonData[n].day && (currentMonth.month+1) == jsonData[n].month && currentMonth.year ==jsonData[n].year) {
                //creating a cell to hold our event text
                  let tablecell = document.createElement("td");
                  tablecell.appendChild(document.createTextNode(jsonData[n].title + " at " + jsonData[n].time));
                  tablecell.setAttribute("class", "editable");
                  let actualId = "0";
                  if (+calendar_date < 10){
                    calendar_date = "0"+calendar_date;
                  }
                  for (let k = 0; k<calendar_days.length; k++){
                    if (calendar_days[i].innerHTML.includes(calendar_date)){
                      actualId = calendar_days[i].id;
                      
                    }
                  }
                  document.getElementById(actualId).appendChild(tablecell);
                  //loading the events
                  window.onload = listEvents(jsonData[n].title, jsonData[n].time, jsonData[n].day,jsonData[n].month, jsonData[n].year, jsonData[n].event_id);
                  //updateCalendar();
                
              }

          }
      }
  }
  //lists each event in the personData when passed in
  function listEvents(title, time, day, month, year, id) {
      
      let e = document.getElementById("personData");
      let listcell = document.createElement("p");
      listcell.appendChild(document.createTextNode("Event ID: " + id + " ~ " + title + " at " + time + " " + month + "-"+ day + "-" + year));
      listcell.setAttribute("class", "editable");
      e.appendChild(listcell);
                  
  }
  //clears our person data list
  function clearCalendar(){
    let div = document.getElementById('personData');
    while(div.firstChild){
      div.removeChild(div.firstChild);
    }
  }
  

//deletes events
  function deleteEvent(event) {
    const deletedId = document.getElementById("delete_event_id").value; // fetch our deleted id value
    console.log(deletedId);
    const data = { 'deletedId': deletedId, 'token' : document.getElementById("csrf_token").value};
    fetch('delete.php', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'content-type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success){
        alert("event deleted");
      }
      else{
        alert(`Could not be deleted: ${data.message}`);
      }
        updateCalendar();
    })
    .catch(err => console.error(err));
}
document.getElementById("deleteButton").addEventListener("click", deleteEvent, false); // Bind the AJAX call to button click
//edits our events
function editEvent(event) {
  //get our values from the html
  const title = document.getElementById("newEventTitle").value;
  const time = document.getElementById("newTime").value;
  const day = document.getElementById("newDay").value;
  const month = document.getElementById("newMonth").value;
  const year = document.getElementById("newYear").value;
  const id = document.getElementById("newEventID").value;

  const data = {'newEventTitle': title, 'newTime': time, 'newDay': day, 'newMonth':month, 'newYear': year, 'newEventID': id, 'token' : document.getElementById("csrf_token").value};
  console.log(data);
  fetch('editevent.php', {
      method: 'POST',
      body: JSON.stringify(data),
      headers: { 'content-type': 'application/json' }
  })
      .then(response => response.json())
      .then(data => {
          //updateCalendar();
          if (data.success){
            updateCalendar();
            alert("event changed successfully");
          }
          else{
            alert( `Could not be edited: ${data.message}`);
            console.log(data.title);
            console.log(data.event_id);
            console.log(data.username);
          }
      })

      //.catch(err => console.error(err));
      //clearing our forms
  document.getElementById('newEventTitle').value = "";
  document.getElementById('newTime').value = "";
  document.getElementById('newDay').value = "";
  document.getElementById('newMonth').value = "";
  document.getElementById('newYear').value = "";
  document.getElementById('newEventID').value = "";
}
document.getElementById("editButton").addEventListener("click", editEvent, false);
//sends an event to another users calendar
function shareEvent(event){
  const sharedId = document.getElementById("sharedId").value;
  const recipient = document.getElementById("recipient").value;
  const data = {'sharedId': sharedId, 'recipient':recipient, 'token' : document.getElementById("csrf_token").value};
  console.log(data);
  fetch('share.php', {
      method: 'POST',
      body: JSON.stringify(data),
      headers: { 'content-type': 'application/json' }
  })
      .then(response => response.json())
      .then(data => {
          if (data.success){
            updateCalendar();
            alert("event shared");
          }
          else{
            alert( `Could not be edited: ${data.message}`);
            
          }
      })

      //.catch(err => console.error(err));
  document.getElementById('sharedId').value = "";
  document.getElementById('recipient').value = "";
}
document.getElementById("shareButton").addEventListener("click", shareEvent, false);
//finds an event's title
function searchEvent(event){
  const searchName = document.getElementById("searchName").value;
  const data = {'searchName': searchName};
  console.log(data);
  //getting rid of the current search result to add a new one
  let div = document.getElementById('searchResult');
    while(div.hasChildNodes()){
      div.removeChild(div.firstChild);
  }
  fetch('searchevent.php', {
      method: 'POST',
      body: JSON.stringify(data),
      headers: {'content-type': 'application/json' }
  })
      .then(response => response.json())
      .then(data => {
          if (data.success){
            //creating a text to hold the results
            let listcell = document.createElement("p");
            listcell.appendChild(document.createTextNode("EVENT " + data.id + ":" + data.title + " at " + data.time + " " + data.month + "-"+ data.day + "-" + data.year));
            listcell.setAttribute("class", "editable");
            document.getElementById('searchResult').appendChild(listcell);
            $("#searchResult").show();
          }
          else{
            alert( `Could not be searched: ${data.message}`);
            
          }
      })

      //.catch(err => console.error(err));
  document.getElementById('searchName').value = "";
  
}
document.getElementById("searchButton").addEventListener("click", searchEvent, false);
//converts date into a user friendly format
function dateConvertion(initialFormat) {
  let num = initialFormat;
  let newNum = num.toString();
  let year = newNum.slice(0, 4);
  let date = newNum.slice(6, 8);
  let month = newNum.slice(4, 6);
  let newDate = new Date(month + "/" + date + "/" + year);
  let dateOfTheWeek = newDate.getUTCDay();
  let dateOut = document.getElementById('weatherDate');
  dateOut.innerHTML = "Today is " + newDate;
  
 
}
function getWeather(event) {
    //fetching our api
    fetch('http://www.7timer.info/bin/api.pl?lon=-90.19&lat=38.627&product=civillight&output=json')
    .then((res) => { return res.json(); })
    .then((data) => { 
       //getting our data from the api
        let path = data.dataseries[0];
        let date = path.date;
        dateConvertion(date);
        let weather = path.weather;
        let tempHigh = path.temp2m.max;
        let tempLow = path.temp2m.min;
        //setting our weather info on the html pa
        document.getElementById('weatherInfo').innerHTML = "Today we will see a high of " + tempHigh + " C and a low of " + tempLow + " C in St. Louis";
    })
    .catch(  );
}
document.getElementById("getWeatherButton").addEventListener("click", getWeather, false); // Bind the AJAX call to button click
