// //I've left in an example schedule here for reference. It will always be named "tues-thurs".
// //The database doesn't currently have data on class "type", but I'd like for users to be able to set colors in the future.
// var dateName;
// var schedule = {"tues-thurs":[{
//   "name": "Tefilla",
//   "start": "8:25",
//   "end": "8:55",
// }, ...
// }]}






// // //example class schedule. room and custom color are planned but not yet implemented

// var classes = [{
//   "Period": "4",
//   "Name": "Freshmen 09",
//   "Room": null,
//   "customColor": "0"
// }, ...
// }];



var divs = [];

//if there is no schedule, var classes will not exist.

var calanderWidth;
var calanderHeight;
var calanderPadding;
var scaleFactor;
//var FortyMinHight;
var paddingLeft;
var paddingTop = 50;
var scheduleTracker;
var assembly = false; //"am-assembly"; //"am-assembly" , pm-assembly
var firstPiriodTime;

var timeLineDiv;
var currentTimeTextDiv;
var coundownDiv;
var date;

// var sound;
// function preload() {
//   soundFormats('mp3', 'ogg');
//   sound = loadSound('js/assets/bell.mp3');
// }

// function preload(){

// song = loadSound('assets/gong.mp3');   
// }

var myClasses;
var openSans;
var timeOffset = +0;

var mainCalanderDiv;

function reInitializeScheduleView(abcDay_){

  var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  var d = new Date(picker.get('select').pick);
  var dayName = days[d.getDay()];

  document.getElementById("dayOfWeek").innerHTML=dayName;

  if(!abcDay_){abcDay_="";}
  if(abcDay_==="A"||abcDay_==="B"||abcDay_==="C"){
    document.getElementById("dayOfWeek").innerHTML+=" ("+abcDay_+" Day)";
  }
	clearCalander()
	resetInitalizeScedual()
	divs = [];
  
	

	// console.log(schedule)
	// console.log(classes)

    
   //code here to delete and re-make everything

}

function clearCalander(){
document.getElementById("main-calander").innerHTML=""; 
// document.body.innerHTML+=   '<div id="main-calander"></div>'
// mainCalanderDiv=document.getElementById("main-calander")

}


function setup() {
	// document.body.innerHTML+=   '<div id="main-calander"></div>'
	mainCalanderDiv=document.getElementById("main-calander")

	document.getElementById("datepicker").addEventListener("click", function(){

    getInfoForNewDate();
    
  })

  // alert("Hello!");
  setValidDates()
	resetInitalizeScedual()

  //frameRate(2);


}


function resetInitalizeScedual(){
	

//note: getInfoForNewDate() is currently defined inline in index.php
	


  
  if((schedule["tues-thurs"])!==undefined){
    schedule=schedule["tues-thurs"];
  }
  date=new Date();
  dateName = date.getDay();
  // scaleFactor = min(windowWidth * 2, windowHeight);
  scaleFactor = Math.max(800, window.innerHeight);
  //createCanvas(scaleFactor / 2, scaleFactor);
  paddingLeft = scaleFactor / 2 * 0.2;
  calanderHeight = scaleFactor - paddingTop * 2;
  calanderWidth = scaleFactor / 2 - paddingLeft * 3;

  calanderPadding = calanderWidth * 0.05;


  if (typeof(classes) !== 'undefined') {
    myClasses = new ClassSchedule(classes);
  } else {
    myClasses = new ClassSchedule([]);
  }
  scheduleTracker = new ScheduleTracker();

  scheduleTracker.createDivs();
}

document.addEventListener('DOMContentLoaded', function () {
    // ...
    // Place code here.
    // ...
    console.log("DOMContentLoaded !!!!!!!!!!!!!!!!!!!!!!!!!")
    setup();
    //draw();
    window.setInterval(draw, 1000);
});





function playSound() {

  //   if ( sound.isPlaying() ) { // .isPlaying() returns a boolean
  //   sound.stop();
  //   background(255,0,0);
  // } else {
  //   sound.play();
  //   background(0,255,0);
  // }
}

function draw() {
  date=new Date();

  scheduleTracker.displayCurTime();
  scheduleTracker.highlightCurrentPeriod();
}


function ScheduleTracker() {

  this.date = new Date();
  // this.displaySchedule = function() {

  // }

  this.displayClockTime = function() {

    if (this.parseTime(schedule[schedule.length - 1].end)[0] * 1.0 >= date.getHours()) {

      return true;
    } else {
      return false
    }
  }

  this.createDivs = function() {
    for (var i = 0; i < schedule.length; i++) {


      var hour_minStart = this.parseTime(schedule[i].start);
      var hour_minEnd = this.parseTime(schedule[i].end);
      var blockTopPos = paddingTop + this.parseTimeToPosition(hour_minStart);
      var blockHeight = this.parseTimeToPosition(hour_minEnd) - this.parseTimeToPosition(hour_minStart);


      var piriodHeight = blockHeight;


      var newPiriodDiv = document.createElement("div");
      mainCalanderDiv.appendChild(newPiriodDiv);


      schedule[i].divs = {}
      schedule[i].divs.main = newPiriodDiv


      var periodBackgroundMain = document.createElement("div");
      schedule[i].divs.backGroundMain = periodBackgroundMain
      var periodBackgroundSide = document.createElement("div");
      schedule[i].divs.backGroundSide = periodBackgroundSide;
      schedule[i].divs.backGroundSide.classList.add("hide-side-bar");

      var timeFontSize = 15

      var timeDiv = document.createElement("div");
      mainCalanderDiv.appendChild(timeDiv);
      timeDiv.innerHTML=(((hour_minStart[0] - 1) % 12 + 1) + ":" + pad(hour_minStart[1], 2));
      // console.log(timeDiv)
      schedule[i].divs.time = timeDiv

      timeDiv.style.color = this.getColor(schedule[i].name);

      timeDiv.classList.add('start-time-text');
      

      var textDiv = document.createElement("div");
      textDiv.innerHTML = schedule[i].name;
      textDiv.classList.add("text-period-name");


      var textClassNameDiv = document.createElement("div");
      textClassNameDiv.classList.add("text-class-name");


      periodBackgroundMain.classList.add("period-main-background");
      periodBackgroundSide.classList.add("period-side-background");

      if (typeof(classes) !== 'undefined') {
        for (var j = 0; j < classes.length; j++) {
          schedule[i].divs.backGroundSide.classList.add("hide-side-bar");
          if (classes[j].Period == schedule[i].name) {
            schedule[i].teaching = classes[j]

            textClassNameDiv.innerHTML = (schedule[i].teaching.Name)
            newPiriodDiv.classList.add("is-teaching");

            

            schedule[i].divs.backGroundSide.classList.remove("hide-side-bar"); //"hide-side-bar")
            // schedule[i].divs.backGroundSide.className.replace(/\bhide-side-bar\b/g, ""); //"
            schedule[i].divs.backGroundSide.classList.add("show-side-bar");

          }
        }


      }




      periodBackgroundMain.appendChild(textDiv)


      // console.log(typeof(newPiriodDiv) + " : " + newPiriodDiv)
      newPiriodDiv.appendChild(periodBackgroundMain);
      newPiriodDiv.appendChild(periodBackgroundSide);
      periodBackgroundMain.appendChild(textClassNameDiv)

      periodBackgroundMain.style['background-color'] = this.getColor(schedule[i].name);
      periodBackgroundSide.style['background-color'] = this.getColor(schedule[i].name);

      newPiriodDiv.style['line-height'] = piriodHeight + 'px';
      newPiriodDiv.style['height'] = piriodHeight + 'px';
      newPiriodDiv.style.width = calanderWidth + 3 + 'px';

      newPiriodDiv.classList.add("class-period");

      newPiriodDiv.style['position'] = "absolute";
      newPiriodDiv.style.left = paddingLeft + "px";
      newPiriodDiv.style.top = blockTopPos + "px";
      //(paddingLeft, blockTopPos)
      //timeDiv.style.position=(paddingLeft - 55, blockTopPos) ))// - timeFontSize / 2
      //divs.push(newPiriodDiv)
      timeDiv.style.position = "absolute";
      timeDiv.style.left = paddingLeft - 55 + "px";
      timeDiv.style.top = blockTopPos + "px";




      //   strokeWeight(3);

      // line(paddingLeft + calanderWidth, currentTimePos,
      //   paddingLeft * 2.8 + calanderWidth, currentTimePos);


    }
    spacerDiv= document.createElement("div");
    mainCalanderDiv.appendChild(spacerDiv);
    spacerDiv.style.width="10px";
    // spacerDiv.style["background-color"]="red";
    spacerDiv.style.height="10px";
    spacerDiv.style.position = "absolute";
    // timeDiv.style.left = paddingLeft - 55 + "px";
    spacerDiv.style.top = blockTopPos +100 + "px";
    
    

    currentTimeTextDiv = document.createElement("div");
    mainCalanderDiv.appendChild(currentTimeTextDiv)
    currentTimeTextDiv.id = "time-line-text";
    currentTimeTextDiv.style['width'] = paddingLeft * 1.8 + 'px';

    coundownDiv = document.createElement("div");
    mainCalanderDiv.appendChild(coundownDiv)
    coundownDiv.id = "countdown-clock";
    // coundownDiv.addClass("clock-countdown-bottom")
    // coundownDiv.addClass("clock-countdown-top")
    // coundownDiv.style('width', paddingLeft * 1.8 + 'px');
    this.countdownPosition('t')

    timeLineDiv = document.createElement("div");
    mainCalanderDiv.appendChild(timeLineDiv);
    timeLineDiv.id = "time-line";
    timeLineDiv.className = "time-line";
    // timeLineDiv.style['height']= 200 + 'px';
    timeLineDiv.style['height'] = 2 + 'px';
    timeLineDiv.style['width'] = paddingLeft * 1.8 + 'px';
    // timeLineDiv.position(paddingLeft + calanderWidth, getCurrentTimePos())

    timeLineDiv.style.position = "absolute";
    timeLineDiv.style.left = paddingLeft + calanderWidth + "px";
    timeLineDiv.style.top = getCurrentTimePos() + "px";
  }

  this.countdownPosition = function(pos_) {
    if (pos_ == "b") {


      coundownDiv.className=coundownDiv.classList.remove("clock-countdown-top"); //("clock-countdown-top")
      // coundownDiv.className=coundownDiv.className.replace(/\bclock-countdown-bottom\b/g, ""); //("clock-countdown-top")
      coundownDiv.classList.add("clock-countdown-bottom");
    } else if (pos_ == "t") {
      coundownDiv.className.replace(/\bclock-countdown-bottom\b/g, "");
      // coundownDiv.className.replace(/\bclock-countdown-top\b/g, "");
      coundownDiv.classList.add("clock-countdown-top");
      //coundownDiv.addClass("clock-countdown-top")
    }
  }


  this.displayCurTime = function() {

    if (this.displayClockTime() || true) { //??????

    	//time-line

    	currentTimeTextDiv.classList.remove("hide")
    	timeLineDiv.classList.remove("hide")

      var currentTimePos = getCurrentTimePos() //remove -6 ???
      console.log(currentTimePos)


      //line
      //timeLineDiv.position(paddingLeft + calanderWidth, getCurrentTimePos())
      timeLineDiv.style.position = "absolute";
      timeLineDiv.style.left = paddingLeft + calanderWidth + "px";
      timeLineDiv.style.top = getCurrentTimePos() + "px";

      //clock
      //currentTimeTextDiv.position(paddingLeft + calanderWidth, getCurrentTimePos())
      currentTimeTextDiv.style.position = "absolute";
      currentTimeTextDiv.style.left = paddingLeft + calanderWidth + "px";
      currentTimeTextDiv.style.top = getCurrentTimePos() + "px";


      currentTimeTextDiv.innerHTML = (((date.getHours() - 1) % 12 + 1) + ":" + pad(date.getMinutes(), 2) + ":" + pad(date.getSeconds(), 2))

      //countdown





      var timeTillEnd = this.timerTillPeriodEnds();
      //coundownDiv.position(paddingLeft * 2.8 + calanderWidth, getCurrentTimePos() - 8) //midle for filpping up or down
      coundownDiv.style['position'] = "absolute";
      coundownDiv.style.left = paddingLeft * 2.8 + calanderWidth + "px";
      coundownDiv.style.top = getCurrentTimePos() - 8 + "px";


      if (timeTillEnd) {

        if (date.getHours() + timeOffset <= 11) { //????????????????????????????/ 11
          coundownDiv.innerHTML =(timeTillEnd[0] + " : " + pad(timeTillEnd[1], 2));
          this.countdownPosition('b')

        } else {
          coundownDiv.innerHTML = pad(timeTillEnd[1], 2) + " : " + timeTillEnd[0];
          this.countdownPosition('t')

        }
      }
    }else{
    	currentTimeTextDiv.classList.add("hide")
    	timeLineDiv.classList.add("hide")
    }
  }

  this.highlightCurrentPeriod = function() {
    var curPeriod = this.getCurrentPeriod();
    
   
    if (curPeriod) {
      for (var i = 0; i < schedule.length; i++) {
        //schedule[i].divs.main.className.replace(/\bcurrent-period\b/g, "");

        schedule[i].divs.main.classList.remove("current-period");
        //curPeriod.divs.main.removeClass("current-period")
      }
      curPeriod.divs.main.classList.add("current-period");
    }
  }

  this.getColor = function(name) {
    //var opacityOff = 130
    if (/^\d+$/.test(name)) {
      return "rgb(35, 61, 71)";//color(35, 61, 71)
    } else if (name == "Tefilla" || name == "Tefillah" || name == "Mincha") {
      return "rgb(161, 193, 129)"; //color(161, 193, 129);
    } else {
      return "rgb(232, 182, 70)"; //color(232, 182, 70);
    }
  }
  this.parseTime = function(stringTime) {

    var hourMin = stringTime.split(":");

    hourMin[0] = hourMin[0] * 1.0;
    hourMin[1] = hourMin[1] * 1.0;
    return hourMin;
  }
  this.parseTimeToPosition = function(hour_min) {

    var startHour = 8;
    var startMin = 25;
    var numMin = ((hour_min[0] * 60) + hour_min[1] * 1.0) - (startHour * 60 + startMin) * 1.0;
    //(firstPiriodTime[0]*60.0 + firstPiriodTime[1]*1.0)
    //(8 * 60 + 25) * 1.0;
    var totalMin = (8 * 60 + 45);

    var scaleCal = calanderHeight / totalMin;

    return numMin * scaleCal;
  }

  //-1 for time has not passed
  this.hasTimePassed = function(t1) {
   
    // console.log(t1[0] * 1.0)
    // console.log(date.getHours() + timeOffset)
    // console.log(t1[0] * 1.0 + " == " + (date.getHours() + timeOffset) +":::::::::" + t1[0] * 1.0 == (date.getHours() + timeOffset));
    if (t1[0] * 1.0 == (date.getHours() + timeOffset)) {
      if (t1[1] * 1.0 > date.getMinutes()) { //this seems to always be true????????????
        // console.log(t1[1] * 1.0)
        return -1
      } else if (t1[1] * 1.0 == date.getMinutes() && date.getSeconds() == 0) { //is the time
        // console.log("*******************************")
        return 0
      } else {
        return 1
        // console.log("*******************************")
      }
    }
    if (t1[0] * 1.0 > date.getHours()) { //current hour is less the scedualed

      return -1;
    } else {
      return 1;
    }

  }

  this.timerTillPeriodEnds = function() {


    var curPeriod = this.getCurrentPeriod();
    //console.log(curPeriod)
    if (curPeriod) {

      var totalSeconds = this.secondsTillPeriodEnds(curPeriod);
      var minutesTillEnd = Math.floor(totalSeconds / 60);
      var secondsTillEnd = totalSeconds % 60;

      //console.log([minutesTillEnd,secondsTillEnd])
      return [minutesTillEnd, secondsTillEnd];
      // push()
      // textSize(40);
      // fill(100);
      // text(minutesTillEnd + " : " + pad(secondsTillEnd,2), 200,200)
      // pop()
    }

  }
  this.secondsTillPeriodEnds = function(curPeriod_) { //in seconds


    var endPiriod = this.parseTime(curPeriod_.end);
    return (endPiriod[0] - (date.getHours() + timeOffset)) * 60 * 60 + (endPiriod[1] - date.getMinutes()) * 60 - date.getSeconds();
    //console.log((endPiriod[0]-hour())*60*60+(endPiriod[1]-minute())*60 -second());
    //console.log((endPiriod[1]*1.0-min()));


  }
  this.getCurrentPeriod = function() {
    for (var i = 0; i < schedule.length; i++) {
      //console.log(this.parseTime(schedule[i].start)[1])
      // console.log(this.hasTimePassed(this.parseTime(schedule[i].start)))
      if (this.hasTimePassed(this.parseTime(schedule[i].start)) == 1 &&
        this.hasTimePassed(this.parseTime(schedule[i].end)) == -1) {

        return schedule[i];
      }

    }
    return false;
  }
}

function pad(num, size) {
  var s = "000000000" + num;
  return s.substr(s.length - size);
}

function ClassSchedule(dataIn) { //[{"Period":"4","Name":"Freshmen 09","Room":null,"customColor":"0"},...]

  this.textSize;
  this.numClasses = 0;


  // this.classesTeaching=[]

  this.classesTeachingDict = {}
  for (var i = 0; i < dataIn.length; i++) {
    // this.classesTeaching.push(dataIn[i]["Period"]*1.0);
    this.classesTeachingDict[dataIn[i]["Period"]] = dataIn[i];
    this.numClasses++;
  }

  // this.setTextSize = function() {
  //     this.textSize = scaleFactor / 50;
  //     var longestClassNameKey = "";
  //     var longestNameWidth = 0;

  //     for (var curKey in this.classesTeachingDict) {
  //       if (longestNameWidth < textWidth(this.classesTeachingDict[curKey].Name)) {
  //         longestNameWidth = textWidth(this.classesTeachingDict[curKey].Name);
  //         longestClassNameKey = curKey;
  //       }

  //     }

  //     this.textSize = 100;
  //     push();
  //     textSize(this.textSize);

  //     if (longestClassNameKey != "") {
  //       while (calanderWidth - calanderPadding * 2 < textWidth(this.classesTeachingDict[longestClassNameKey].Name)) {
  //         //console.log(textWidth(this.classesTeachingDict[longestClassNameKey].Name))
  //         this.textSize--;
  //         textSize(this.textSize);
  //       }
  //     }

  //     pop()
  //   }
    //console.log(this.classesTeaching)


  this.isTeaching = function(classNum) {
    return this.classesTeachingDict[classNum] != undefined;
  }
  this.getClassName = function(classNum) {
    return this.classesTeachingDict[classNum]["Name"];
  }


  // this.setTextSize();

}

function getCurrentTimePos() {
  return paddingTop + scheduleTracker.parseTimeToPosition([date.getHours() + timeOffset, date.getMinutes()]);

}


function backgroundColor(){
  if(document.body.style['background-color']=="black"){
    document.body.style['background-color'] = "white";
    document.body.style.color = "black";
    
  }else{
    document.body.style['background-color']="black";
    document.body.style.color = "white";
  }
  //background-color: black
  

}


