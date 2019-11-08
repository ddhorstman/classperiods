<?php 
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=calendar.ics');
include_once "common/base.php";

if(!isset($_GET['extID'])){
    exit();
}

  $eol = "\r\n";
     $version = "PRODID:-//David Horstman//Central Calendar Creator 1.0//EN";
     $header = "BEGIN:VCALENDAR".$eol
    ."VERSION:2.0".$eol
    ."METHOD:PUBLISH".$eol
    ."X-WR-CALNAME:Class Schedule".$eol
      ."BEGIN:VTIMEZONE".$eol
  ."TZID:America/New_York".$eol
  ."X-LIC-LOCATION:America/New_York".$eol
  ."BEGIN:DAYLIGHT".$eol
  ."TZOFFSETFROM:-0500".$eol
  ."TZOFFSETTO:-0400".$eol
  ."TZNAME:EDT".$eol
  ."DTSTART:19700308T020000".$eol
  ."RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU".$eol
  ."END:DAYLIGHT".$eol
  ."BEGIN:STANDARD".$eol
  ."TZOFFSETFROM:-0400".$eol
  ."TZOFFSETTO:-0500".$eol
  ."TZNAME:EST".$eol
  ."DTSTART:19701101T020000".$eol
  ."RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU".$eol
  ."END:STANDARD".$eol
  ."END:VTIMEZONE".$eol;

 $footer = "END:VCALENDAR";

 $eventsample = "BEGIN:VEVENT".$eol
    ."DTSTART:20180901T120500".$eol
    ."DTEND:20180901T124500".$eol
    ."DTSTAMP:20170717T061400".$eol
    ."SUMMARY:AlgIITrig".$eol
    ."END:VEVENT".$eol;



  include_once "common/inc/class.ics_generation.inc.php";
 $calendar = new iCalGenerator($db);
  $calendar_data = $calendar->generateCalendar($_GET['extID']);
 echo $calendar_data;
//echo $header.$eventsample.$footer;
exit();
?>