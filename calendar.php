<?php 
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=calendar.ics');

//Start writing the calendar  out before include statement
//so that a BOM being added doesn't prevent the file from being verified
echo "BEGIN:VCALENDAR\r\n"."PRODID:-//David ";
include_once "common/base.php";


if(!isset($_GET['extID'])){
    exit();
}
class iCalGenerator
{
  
 
  /**
     * The database object
     *
     * @var object
     */
    private $_db;
    private $userid;
    private $email = "davidzilla27@gmail.com";
    private $date_created = "";
    //The full VERSION is "PRODID:-//David Horstman//ClassPeriods//EN"
    //the actual header starts above to avoid a calendar validation bug
    //when the BOM is added to the file with an include statement 
    const VERSION = "Horstman//ClassPeriods//EN";
    const EOL = "\r\n";
    private $header = self::VERSION.self::EOL
    ."VERSION:2.0".self::EOL
    ."METHOD:PUBLISH".self::EOL
    ."X-WR-CALNAME:Class Schedule".self::EOL
      ."BEGIN:VTIMEZONE".self::EOL
  ."TZID:America/New_York".self::EOL
  ."X-LIC-LOCATION:America/New_York".self::EOL
  ."BEGIN:DAYLIGHT".self::EOL
  ."TZOFFSETFROM:-0500".self::EOL
  ."TZOFFSETTO:-0400".self::EOL
  ."TZNAME:EDT".self::EOL
  ."DTSTART:19700308T020000".self::EOL
  ."RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU".self::EOL
  ."END:DAYLIGHT".self::EOL
  ."BEGIN:STANDARD".self::EOL
  ."TZOFFSETFROM:-0400".self::EOL
  ."TZOFFSETTO:-0500".self::EOL
  ."TZNAME:EST".self::EOL
  ."DTSTART:19701101T020000".self::EOL
  ."RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU".self::EOL
  ."END:STANDARD".self::EOL
  ."END:VTIMEZONE".self::EOL;
    private  $footer = "END:VCALENDAR";
    private  $eventsample = "BEGIN:VEVENT".self::EOL
    ."DTSTART;TZID=America/New_York:20180901T120500".self::EOL
    ."DTEND;TZID=America/New_York:20180901T124500".self::EOL
    ."DTSTAMP:20170717T061400".self::EOL
    ."SUMMARY:AlgIITrig".self::EOL
    ."END:VEVENT".self::EOL;
    
   private  $body = "";
  private $school_days = array();
  private  $bell_schedules = array();
  private  $class_schedule = array();
    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db
     * @return void
     */
    public function __construct($db=NULL)
    {
        if(is_object($db))
        {
            $this->_db = $db;
        }
        else
        {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
        
     
        
    }
   public function getClassSchedule($extID){
   	$class_days = array("M","A","B","C","T","F");
   	$result = array();
   	foreach($class_days as $day_name){
   		$result[$day_name]=$this->getClassScheduleByDay($extID,$day_name);
   	}
   	return $result;
   }
   public function getClassScheduleByDay($extID,$class_day){
    $sql = "SELECT ClassName, ClassPeriod
    		FROM schedule_items
    		WHERE UserID=(SELECT UserID
    					FROM users
    					WHERE ExtID=:extid)
    		AND ClassDay=:classday
    		ORDER BY ClassPeriod";
    try{
    	$stmt = $this->_db->prepare($sql);
    	$stmt->bindParam(":extid",$extID);
    	$stmt->bindParam("classday",$class_day);
    	$stmt->execute();
    	$result = array();
    	while($row=$stmt->fetch()){
    		$period = $row['ClassPeriod'];
    		$name = $row['ClassName'];
    		$result[$period]=$name;
    	}
    	$stmt->closeCursor();
    	return $result;
    }
    catch(PDOException $e){
    	die($e->getMessage());
    }
   }
    public function getBellSchedules(){
    	$sql = "SELECT BellScheduleName, BellScheduleID
    			FROM bell_schedules";
    	try{
    		$stmt = $this->_db->prepare($sql);
    		$stmt->execute();
    		$result = array();
    		while($row = $stmt->fetch()){
    			$name = $row['BellScheduleName'];
    			$result[$name]=$this->getBellScheduleTimes($row['BellScheduleID']);
    		}
    		$stmt->closeCursor();
    		return $result;
    	}
    	catch(PDOException $e)
            {
                die($e->getMessage());
            }
    }
    public function getBellScheduleTimes($bellID){
    	$sql = "SELECT PeriodName, StartTime, EndTime
    			FROM bell_schedules_items
    			WHERE BellScheduleID=:bellid
    			ORDER BY StartTime";
    	try{
    		$stmt = $this->_db->prepare($sql);
    		$stmt->bindParam(":bellid", $bellID);
    		$stmt->execute();
    		$result = array();
    		while($row = $stmt->fetch()){
    			$name = $row['PeriodName'];
    			$start = date('His',strtotime($row['StartTime']));
    			$end = date('His',strtotime($row['EndTime']));
    			$times = array("StartTime"=>$start, "EndTime"=>$end);
    			$result[$name]=$times;
    		}
    		$stmt->closeCursor();
    		return $result;
    	}
    	       catch(PDOException $e)
            {
                throw $e;
                return array(array(""));
            }
    }
    
    public function getSchoolDays($schoolid){
    	 $sql = "SELECT SchoolDate, DayName, BellScheduleName
                FROM school_days
                 WHERE SchoolYearID=:schoolid";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":schoolid", $schoolid);
                $stmt->execute();
                $result = array();
                while($row = $stmt->fetch()){
                    $row = $this->processSchoolDay($row);
                    $result[] = $row;
                }
                $stmt->closeCursor();
                return $result;
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
    }
    public function processSchoolDay($row){
    	$result = array();
    	$result['SchoolDate']=date('Ymd',strtotime($row['SchoolDate']));
    	$result['DayName']=$row['DayName'];
    	$result['BellScheduleName']=$row['BellScheduleName'];
    	return $result;
    }
    public function setUserUsedCalendar($extID){
    	$usedcalendar = 1;
    $sql = "UPDATE users
    		SET UsedCalendar=:usedcalendar
    		WHERE  ExtID=:extid";
    try{
    	$stmt = $this->_db->prepare($sql);
    	$stmt->bindParam(":extid",$extID);
    	$stmt->bindParam(":usedcalendar",$usedcalendar);
    	$stmt->execute();
    	$stmt->closeCursor();
    	return true;
    }
    catch(PDOException $e){
    	die($e->getMessage());
    }
    }
    
    public function generateCalendar($extID){
    	$date_created = date('Ymd')."T".date('His');
    	$schoolid = 1;
    	$didUserUseCalendar = $this->setUserUsedCalendar($extID);
        $this->school_days = $this->getSchoolDays($schoolid);
         $this->bell_schedules = $this->getBellSchedules();
         $this->class_schedule = $this->getClassSchedule($extID);
         foreach($this->school_days as $school_day_info){
         	$this->body .= $this->addSchoolDay($school_day_info);
         }
        // $testarray = print_r($this->body, true);
        // return '<pre>'.$testarray.'</pre>';
       // return date('Ymd\THis');
        return $this->header.$this->body.$this->footer;
        
    }
    public function addSchoolDay($school_day_info){
    	$result = "";
    	$date = $school_day_info['SchoolDate'];
    	$bell_schedule_name = $school_day_info['BellScheduleName'];
    	if(isset($this->bell_schedules[$bell_schedule_name])){
    		$bells_today = $this->bell_schedules[$bell_schedule_name];
    	}
    	else {return "";}//if no valid bell schedule found, skip this day
    	$week_day = $school_day_info['DayName'];
    	if(isset($this->class_schedule[$week_day])){
    		$classes_today = $this->class_schedule[$week_day];
    	}
    	else {return "";}//if no classes today, skip this day
    	foreach($classes_today as $period=>$class_name){
    		if(isset($bells_today[$period])){
    			$start = $bells_today[$period]['StartTime'];
    			$end = $bells_today[$period]['EndTime'];
    			$result .= "BEGIN:VEVENT".self::EOL
    			."UID:".uniqid()."@yuhsgschedule.com".self::EOL
    			."DTSTART;TZID=America/New_York:".$date."T".$start.self::EOL
   				."DTEND;TZID=America/New_York:".$date."T".$end.self::EOL
   				."DTSTAMP:".date('Ymd\THis').self::EOL
   				."SUMMARY:".$class_name.self::EOL
    			."END:VEVENT".self::EOL;
    		}
    	}
    	return $result;
    	//return print_r($classes_today,true);
	}
}
 $calendar = new iCalGenerator($db);
  $calendar_data = $calendar->generateCalendar($_GET['extID']);
 echo $calendar_data;
//echo $header.$eventsample.$footer;
exit();
?>