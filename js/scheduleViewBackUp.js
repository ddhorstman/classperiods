// //I've left in an example schedule here for reference. It will always be named "tues-thurs".
// //The database doesn't currently have data on class "type", but I'd like for users to be able to set colors in the future.
// var dateName;
// var schedule = {
//     "tues-thurs": 
//   [{
//         "name": "Tefilla",
//         "start": "8:25",
//         "end": "8:55",
//       },
//       {
//         "name": "1",
//         "start": "9:05",
//         "end": "9:45",
//       },
//       {
//         "name": "2",
//         "start": "9:48",
//         "end": "10:28",
//       },
//       {
//         "name": "3",
//         "start": "10:31",
//         "end": "11:11",
//       },
//       {
//         "name": "4",
//         "start": "11:14",
//         "end": "11:54",
//       },
//       {
//         "name": "5",
//         "start": "11:57",
//         "end": "12:37",
//       "type":"class"
//       },
//       {
//         "name": "6",
//         "start": "12:40",
//         "end": "13:20",
//       },
//       {
//         "name": "7",
//         "start": "13:23",
//         "end": "14:03",
//       },
//       {
//         "name": "8",
//         "start": "14:06",
//         "end": "14:46",
//       },
//       {
//         "name": "9",
//         "start": "14:49",
//         "end": "15:29",
//       },
//       {
//         "name": "Mincha",
//         "start": "15:32",
//         "end": "15:45",
//       },
//       {
//         "name": "10",
//         "start": "15:48",
//         "end": "16:28",
//       },
//       {
//         "name": "11",
//         "start": "16:30",
//         "end": "17:10",
//       }
//     ]
  
// }



// //example class schedule. room and custom color are planned but not yet implemented

// var classes = [{"Period":"4","Name":"Freshmen 09","Room":null,"customColor":"0"},
// {"Period":"7","Name":"Sophomores 10","Room":null,"customColor":"0"},
// {"Period":"9","Name":"Sophomores 11","Room":null,"customColor":"0"},
// {"Period":"11","Name":"Juniors","Room":null,"customColor":"0"}];

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

// var sound;
// function preload() {
//   soundFormats('mp3', 'ogg');
//   sound = loadSound('js/assets/bell.mp3');
// }

// function preload(){
    
// song = loadSound('assets/gong.mp3');   
// }

var myClasses;
function setup() {
    
// 	dateName=day();
  scaleFactor = min(windowWidth * 2, windowHeight);
  createCanvas(scaleFactor / 2, scaleFactor);
  paddingLeft=width*0.2;
  calanderHeight = height - paddingTop * 2;
  calanderWidth = width - paddingLeft * 3;
  
  calanderPadding=calanderWidth*0.05;

  //console.log(classes)
  
  if(typeof(classes) != "undefined"){
  	myClasses=new ClassSchedule(classes);
  }else{
    myClasses=new ClassSchedule([]);
  }
  scheduleTracker = new ScheduleTracker();
  
  frameRate(1);

  // AutoScrollDiv=createDiv('Auto Scroll');
  // AutoScrollDiv.position(0,0)
  // AutoScrollDiv.style('padding','10px')
  // AutoScrollDiv.style('background-color', '#aaa');
  // AutoScrollDiv.style('color', '#fff');

  //   AMAssemblyScrollDiv=createDiv('Set AM Assembly');
  //   AMAssemblyScrollDiv.position(0,0)
  //   AMAssemblyScrollDiv.style('padding','10px')
  //   AMAssemblyScrollDiv.style('background-color', '#eee');
  //   AMAssemblyScrollDiv.style('color', '#fff');

  //   PMAssemblyScrollDiv=createDiv('Set PM Assembly');
  //   PMAssemblyScrollDiv.position(200,0)
  //   PMAssemblyScrollDiv.style('padding','10px')
  //   PMAssemblyScrollDiv.style('background-color', '#eee');
  //   PMAssemblyScrollDiv.style('color', '#fff');

  //var scrollPos=paddingTop+scheduleTracker.parseTimeToPosition([hour()-6,minute()]);
  //window.scrollBy(0, 200);
  //window.scrollBy(0, scrollPos);
  
  
  //firstPiriodTime=this.parseTime(schedule[dayOfWeek][0].start)//



}



// function mousePressed() {
//     playSound()
// //   if ( sound.isPlaying() ) { // .isPlaying() returns a boolean
// //     sound.stop();
// //     background(255,0,0);
// //   } else {
// //     sound.play();
// //     background(0,255,0);
// //   }
// }

function playSound(){
    
  //   if ( sound.isPlaying() ) { // .isPlaying() returns a boolean
  //   sound.stop();
  //   background(255,0,0);
  // } else {
  //   sound.play();
  //   background(0,255,0);
  // }
}

function draw() {
    
    
    

  background(255);
  
  scheduleTracker.displaySchedule();
  scheduleTracker.displayCurTime();
  
  //firstPiriodTime=this.parseTime(schedule[dayOfWeek][0].start)//
  //scheduleTracker.timerTillPeriodEnds();

}
var dayOfWeek;

function ScheduleTracker() {


  this.date = new Date();


  this.displaySchedule = function() {
    //Date selection is now done in the backend - left as "tues-thurs" for combatibility
    dayOfWeek = "tues-thurs";

    
    var curPeriod=this.getCurrentPeriod();
    //print date (passed in as dateName from PHP)
    fill(0);
    textAlign(LEFT,BOTTOM);
    text(dateName,paddingLeft-30,paddingTop-10);
    
    //show class periods
    for (var i = 0; i < schedule[dayOfWeek].length; i++) {

      var hour_minStart = this.parseTime(schedule[dayOfWeek][i].start);
      var hour_minEnd = this.parseTime(schedule[dayOfWeek][i].end);

      var curColor = this.getColor(schedule[dayOfWeek][i].name);
      //console.log(curColor)
      
      noStroke();
      
      if(curPeriod.name==schedule[dayOfWeek][i].name){
        fill(red(curColor),green(curColor), blue(curColor),255);
      }else{
      	fill(curColor);
      }

      var blockTopPos = paddingTop + this.parseTimeToPosition(hour_minStart);
      var blockHeight = this.parseTimeToPosition(hour_minEnd) - this.parseTimeToPosition(hour_minStart);

      rect(paddingLeft, blockTopPos,
        calanderWidth, blockHeight);
      
      push()
      noStroke();
      textAlign(LEFT, CENTER);
      fill(255);

      text(schedule[dayOfWeek][i].name, paddingLeft + 5, blockTopPos + blockHeight / 2);
      pop();

     if(myClasses.isTeaching(schedule[dayOfWeek][i].name)){
         
        push()
        fill(255)
        rect(paddingLeft, blockTopPos,
        calanderWidth, blockHeight);
        pop()
        
        fill(red(curColor),green(curColor), blue(curColor),180);
         rect(paddingLeft, blockTopPos,
        calanderWidth, blockHeight);//+calanderPadding*.5
        
        rect(paddingLeft+calanderWidth+2, blockTopPos,
        2, blockHeight);
        
        
        push();
        fill(255);
        
        
        
        textSize(myClasses.textSize);
        
        //console.log("text width  " +textWidth(myClasses.getClassName(i)));
        textAlign(RIGHT,CENTER);
        
        //console.log(textWidth(myClasses.getClassName(i)));
        text(myClasses.getClassName(schedule[dayOfWeek][i].name),
             paddingLeft*1+calanderWidth-calanderPadding,
             blockTopPos+blockHeight/2);
             
        pop();
    	}

      textAlign(RIGHT, CENTER);

      var startTime = this.parseTime(schedule[dayOfWeek][i].start);

     
      noStroke();
      text(((startTime[0] - 1) % 12 + 1) + ":" + pad(startTime[1], 2), paddingLeft - 5, blockTopPos);
			
      
//       noStroke();
//       textAlign(LEFT, CENTER);
//       fill(255);

//       text(schedule[dayOfWeek][i].name, paddingLeft + 5, blockTopPos + blockHeight / 2);

    }
  }

  this.displayClockTime = function() {
   
    if(this.parseTime(schedule[dayOfWeek][schedule[dayOfWeek].length-1].end)[0]*1.0  >= hour()){
       
      return true;
    }else{
      return false
    }
  }

  this.displayCurTime = function() {
    if (this.displayClockTime()) {
      var currentTimePos = paddingTop + this.parseTimeToPosition([hour(), minute()]); //remove -6 ???
      stroke(254, 127, 45);
      strokeWeight(3);

      line(paddingLeft +calanderWidth, currentTimePos,
        paddingLeft * 2.8 + calanderWidth, currentTimePos);

      // var scrollTarget=currentTimePos-windowHeight*.5;
      // var scrollAmount=window.pageYOffset+(scrollTarget-window.pageYOffset)*.01
      // window.scrollTo(0, scrollAmount)//window.pageYOffset-currentTimePos)
      noStroke();
      fill(254, 127, 45);
      textAlign(RIGHT, CENTER);
      
      textSize(scaleFactor/40);
      text(((hour() - 1) % 12 + 1) + ":" + pad(minute(), 2) + ":" + pad(second(), 2), paddingLeft * 2.8 + calanderWidth, currentTimePos - 10);
      
      
      
      var timeTillEnd=this.timerTillPeriodEnds();
      if(timeTillEnd){
        push()
        
        textSize(scaleFactor/10);
        fill(100);
        if(hour()<=11){//????????????????????????????/ 11
          textAlign(LEFT, CENTER);
        	translate(width-scaleFactor/20,currentTimePos+20)
          
        }else{
          translate(width-scaleFactor/20,currentTimePos-40)
        }
        rotate(PI/2)
        //console.log(timeTillEnd);
        text(timeTillEnd[0] + " : " + pad(timeTillEnd[1],2), 0,0)
        pop()
      }
      //scheduleTracker.timerTillPeriodEnds();
    }
  }

  this.getColor = function(name) {
    var opacityOff=130
    if (/^\d+$/.test(name)) {
      return color(35, 61, 71,opacityOff);
    } else if (name == "Tefilla" || name == "Tefillah" || name == "Mincha") {
      return color(161, 193, 129,opacityOff);
    } else {
      return color(232, 182, 70,opacityOff);
    }
    //else return color(35,61,71);
    //noStroke();



    // if(name=="class"){
    //   return color(35,61,71);
    // }else if(name=="Tefilla"){
    //   return color(161,193,129);
    // }else if(name=="assembly"){
    //   return color(252,202,70);
    // }
    // else return color(35,61,71);
    // noStroke();
  }
  this.parseTime = function(stringTime) {
    var hourMin = stringTime.split(":");
    
    hourMin[0]=hourMin[0]*1.0;
		hourMin[1]=hourMin[1]*1.0;
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
  this.hasTimePassed=function(t1){
    if(t1[0]*1.0==hour()){
      if(t1[1]*1.0>minute()){
       	return -1 
      }else if(t1[1]*1.0==minute() && second()==0){//is the time
        return 0
      } else{
        return 1
      }
    }if(t1[0]*1.0>hour()){//current hour is less the scedualed
      
      return -1;
    }else{
      return 1;
    }
    
  }
  
  this.timerTillPeriodEnds=function(){
    
    
    var curPeriod=this.getCurrentPeriod();
    if(curPeriod){
      
      var totalSeconds= this.secondsTillPeriodEnds(curPeriod);
      var minutesTillEnd=floor(totalSeconds/60);
      var secondsTillEnd=totalSeconds%60;
      
      //console.log([minutesTillEnd,secondsTillEnd])
      return [minutesTillEnd,secondsTillEnd];
      // push()
      // textSize(40);
      // fill(100);
      // text(minutesTillEnd + " : " + pad(secondsTillEnd,2), 200,200)
      // pop()
    }
    
  }
  this.secondsTillPeriodEnds=function(curPeriod_){//in seconds
    
    
      var endPiriod= this.parseTime(curPeriod_.end);
      return (endPiriod[0]-hour())*60*60+(endPiriod[1]-minute())*60 -second();
      //console.log((endPiriod[0]-hour())*60*60+(endPiriod[1]-minute())*60 -second());
      //console.log((endPiriod[1]*1.0-min()));
    
    
  }
  this.getCurrentPeriod=function(){
    for(var i=0;i<schedule[dayOfWeek].length;i++){
      
      if(this.hasTimePassed(this.parseTime(schedule[dayOfWeek][i].start))==1 &&
        this.hasTimePassed(this.parseTime(schedule[dayOfWeek][i].end))==-1){
        
        return schedule[dayOfWeek][i];
      }
    //   else if(this.hasTimePassed(this.parseTime(schedule[dayOfWeek][i].end))==0 )  {
    //       console.log("*********************************????");
    //       playSound();
    //   }
    }
    return false;
      
      //this.parseTime(schedule[dayOfWeek][schedule[dayOfWeek].length-1].end)
  }
}

function pad(num, size) {
  var s = "000000000" + num;
  return s.substr(s.length - size);
}

function ClassSchedule(dataIn){//[{"Period":"4","Name":"Freshmen 09","Room":null,"customColor":"0"},...]
  
  this.textSize;
  this.numClasses=0;
  

  // this.classesTeaching=[]
  
  this.classesTeachingDict={}
  for(var i=0;i<dataIn.length;i++){
    // this.classesTeaching.push(dataIn[i]["Period"]*1.0);
    this.classesTeachingDict[dataIn[i]["Period"]]=dataIn[i];
    //console.log("teachingPiriods " + dataIn[i]["Period"]);
    this.numClasses++;
  }
   //console.log(this.classesTeachingDict);
  
  this.setTextSize=function(){
    this.textSize=scaleFactor/50;
    var longestClassNameKey="";
    var longestNameWidth=0;
    
    for (var curKey in this.classesTeachingDict) {
        if(longestNameWidth<textWidth(this.classesTeachingDict[curKey].Name)){
          longestNameWidth=textWidth(this.classesTeachingDict[curKey].Name);
          longestClassNameKey=curKey;
        }
      
    }
    
    this.textSize=100;
    push();
    textSize(this.textSize);
    
    if(longestClassNameKey!=""){
      while(calanderWidth-calanderPadding*2<textWidth(this.classesTeachingDict[longestClassNameKey].Name)){
        //console.log(textWidth(this.classesTeachingDict[longestClassNameKey].Name))
        this.textSize--;
        textSize(this.textSize);
      }
    }
    //console.log(textWidth(this.classesTeachingDict[longestClassNameKey].Name) + " : " + calanderWidth);
    
		pop()
    // for(var i=0;i<numClasses;i++){
    //   if(longestNameWidth<
    // }
  }
  //console.log(this.classesTeaching)
  
  
  this.isTeaching=function(classNum){
    return this.classesTeachingDict[classNum]!=undefined;
  }
  this.getClassName=function(classNum){
    //   console.log(classNum)
    //   console.log(this.classesTeachingDict)
    try{
    return this.classesTeachingDict[classNum]["Name"];
    }catch(e){
        
        
    }
    
  }
  
  
  this.setTextSize();
  
}