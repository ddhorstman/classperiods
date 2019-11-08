 <?php
class ClassScheduleItems
{
 
  /**
     * The database object
     *
     * @var object
     */
    private $_db;
    private $userid;
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
    
    public function getClassSchedule($user){
                $sql = "SELECT ClassTimeSlot, ClassName 
                FROM schedule_items
                 WHERE UserID=(SELECT UserID 
                    FROM users 
                    WHERE Username=:email
                    LIMIT 1)";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":email", $user);
                $stmt->execute();
                $row = array();
                while($row1 = $stmt->fetch()){
                    $row1 = $this->processClassSchedule($row1);
                    $row = array_merge($row,$row1);
                }
                $stmt->closeCursor();
                return $row;
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
        
        
        
       // return "Hello!";
    }
    private function processClassSchedule($row){
        $result = array();
        $result[$row['ClassTimeSlot']]=$row['ClassName'];
      //  $result['Name'] = $row['ClassName'];
        
        return($result);
    }
    
   
    
   public function updateScheduleItem($key,$val){
       $period = $key;
       $name = strip_tags(urldecode(trim($val)), WHITELIST);
       $sql = "SELECT UserID
                FROM users
                WHERE Username=:user";
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            $stmt->closeCursor();
            $userid= $row['UserID'];
        }
        catch(PDOException $e)
        {
            return "<p>Encountered user id error $e. Please try again.</p>";
        }
        
      
       $sql = "SELECT COUNT(ClassTimeSlot) AS theCount
                FROM schedule_items
                WHERE ClassTimeSlot=:period
                AND UserID=:userid";
        if($stmt = $this->_db->prepare($sql)) {
            $stmt->bindParam(":period", $period, PDO::PARAM_STR);
            $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch();
            $stmt->closeCursor();
            if($row['theCount']!=0) {
                
        if(strtolower($name)==strtolower("delete")||strtolower($name)==strtolower("\"delete\"")){ 
            $sql = "DELETE FROM schedule_items
                    WHERE UserID=:userid
                    AND ClassTimeSlot=:period";
            try
            {
                $stmt = $this->_db->prepare($sql);
                 $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
              $stmt->bindParam(":period", $period, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
 
            }
            catch(PDOException $e)
            {
                return "<p>Class in period $period was found but could not be updated</p>";
            }
           
           
           
           return "<p>Deleted class from time slot $period.</p>";
       }
                else {
                $sql = "UPDATE schedule_items
                    SET ClassName=:name
                    WHERE UserID=:userid
                    AND ClassTimeSlot=:period";
            try
            {
                $stmt = $this->_db->prepare($sql);
                 $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
             $stmt->bindParam(":name", $name, PDO::PARAM_STR);
              $stmt->bindParam(":period", $period, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
 
                return "<p>Updated class $name in time slot $period.</p>";
            }
            catch(PDOException $e)
            {
                return "<p>Class in period $period was found but could not be updated.</p>";
            }
                
            }    
            }
            
        } else { return "<p>Error querying database for user information</p>";}
    
 
     $day = substr($key,0,1);
       $periodnumber = substr($key,-2);
    
    if(strtolower($name)==strtolower("delete")||strtolower($name)==strtolower("\"delete\"")){
        return "<p>You can't add a class named delete!.</p>";
    }
    $zero = 0;
    $sql = "INSERT INTO schedule_items(UserID, ClassName, ClassDay, ClassPeriod, ClassTimeSlot, ClassColor)
                VALUES(:userid, :name, :day, :periodnumber, :period, :color)";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":userid", $userid);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":day", $day, PDO::PARAM_STR);
            $stmt->bindParam(":periodnumber", $periodnumber, PDO::PARAM_STR);
            $stmt->bindParam(":period", $period, PDO::PARAM_STR);
            $stmt->bindParam("color", $zero);
            $stmt->execute();
            $stmt->closeCursor();
            return "<p>Added class $name in time slot $period.</p>";
        }
    catch (PDOException $e){
        return $e;
    }
    
    
   }
    
    public function getValidSchoolDays(){
        $schoolid = 1;
             $sql = "SELECT SchoolDate
                FROM school_days
                 WHERE SchoolYearID=:schoolid";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":schoolid", $schoolid);
                $stmt->execute();
                $result = array();
                while($row = $stmt->fetch()){
                    $row = $row["SchoolDate"];
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
    public function getSchoolDayInfo($date){
        
        $sql ="SELECT DayName, BellScheduleName
                FROM school_days
                WHERE SchoolDate=:date
                LIMIT 1";
        
        try{
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $row = $stmt->fetch();
            $stmt->closeCursor();
            $dateformat = date('l, F jS, Y', strtotime($date));
            if($stmt->rowCount()==0) return ["<p><b>Could not find schedule info for $dateformat. Try a different day to continue.</b></p>","Error"];
            return $row;
            
            
        }
        catch(PDOException $e){
            
            return ["<h2>$e</h2>","Error"];
        }
        
        return ["<h2>Colud not find schedule info for this date. Is it a school day?</h2>","Error"];
    }
    
    public function getBellSchedule($bell_schedule){
        $sql = "SELECT PeriodName, StartTime, EndTime 
                FROM bell_schedules_items
                 WHERE BellScheduleID=(SELECT BellScheduleID 
                    FROM bell_schedules 
                    WHERE BellScheduleName=:bellname
                    LIMIT 1)
                ORDER BY StartTime";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":bellname", $bell_schedule);
                $stmt->execute();
                $row = $stmt->fetch();
                $row = $this->processBellSchedule($row);
                while($row1 = $stmt->fetch()){
                    $row1 = $this->processBellSchedule($row1);
                    $row = array_merge($row, $row1);
                }
                $stmt->closeCursor();
                unset($row['PeriodName']);
                unset($row['StartTime']);
                unset($row['EndTime']);
                return $row;
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
    }
        public function processBellSchedule($row){
        //remove leading 0 from one-digit periods
         if(strlen($row[0])==2&&intval($row[0]<10))$row[0]=substr($row[0],-1);
         //trim times before 10:00
            if(substr($row[1],0,1)==0)$row[1]=substr($row[1],1,-3);
        //trim times between 10 and 12
            else if(substr($row[1],1,1)<3)$row[1]=substr($row[1],0,-3);
        //trim "PM" times
            else {
                $temp = substr($row[1],1,1)-2;
                $temp .= substr($row[1],2,-3);
                $row[1] = $temp;
            }
        //trim times before 10:00
            if(substr($row[2],0,1)==0)$row[2]=substr($row[2],1,-3);
        //trim times between 10 and 12
            else if(substr($row[2],1,1)<3)$row[2]=substr($row[2],0,-3);
        //trim "PM" times
            else {
                $temp = substr($row[2],1,1)-2;
                $temp .= substr($row[2],2,-3);
                $row[2] = $temp;
            }
        return $row;
    }
    public function getBellsNew($bell_schedule){
        $sql = "SELECT PeriodName, StartTime, EndTime 
                FROM bell_schedules_items
                 WHERE BellScheduleID=(SELECT BellScheduleID 
                    FROM bell_schedules 
                    WHERE BellScheduleName=:bellname
                    LIMIT 1)
                ORDER BY StartTime";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":bellname", $bell_schedule);
                $stmt->execute();
                $bellsResult = array();
                while($row1 = $stmt->fetch()){
                    $row1 = $this->processBellsNew($row1);
                    $bellsResult[] = $row1;
                }
                $stmt->closeCursor();
              /*  unset($row['PeriodName']);
                unset($row['StartTime']);
                unset($row['EndTime']);
                */
               // $bellsResult['mon']=$bellsResult[0];
                //unset($bellsResult[0]);
                return $bellsResult;
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
    }
    public function processBellsNew($row){
        $result = array();
        if(substr($row['PeriodName'],0,1)==0&&substr($row['PeriodName'],1,1)!=0)
            $name = substr($row['PeriodName'],1,1);
        else $name = $row['PeriodName'];
        $result['name']=$name;
        if(substr($row['StartTime'],0,1)==0) $start = substr($row['StartTime'],1,4);
        else $start = substr($row['StartTime'],0,5);
         $result['start']=$start;
         if(substr($row['EndTime'],0,1)==0) $end = substr($row['EndTime'],1,4);
        else $end = substr($row['EndTime'],0,5);
          $result['end']=$end;
          $result['type']="class";
        return $result;
    }
  public function getClassesNew($class_day){
        $sql = "SELECT ClassPeriod, ClassName, ClassRoom, ClassColor 
                FROM schedule_items
                 WHERE ClassDay=:day
                 AND UserID=(SELECT UserID 
                    FROM users 
                    WHERE Username=:email
                    LIMIT 1)
                ORDER BY ClassPeriod";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":day", $class_day);
                $stmt->bindParam(":email", $_SESSION['Username']);
                $stmt->execute();
                $result = array();
               // $row = $stmt->fetch();
                //$row = $this->processClassPeriods($row);
                while($row1 = $stmt->fetch()){
                    $row1 = $this->processClassPeriodsNew($row1);
                   $result[]=$row1;
                }
                $stmt->closeCursor();
                return $result;
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
    }
public function processClassPeriodsNew($row){
   // if(strlen($row[0])==2&&intval($row[0]<10))$row[0]=substr($row[0],-1);
   $processed_row = array();
    if(strlen($row['ClassPeriod'])==2&&intval($row['ClassPeriod']<10))
        $period=substr($row['ClassPeriod'],-1);
    else $period = $row['ClassPeriod'];
   $processed_row['Period'] = $period;
   $processed_row['Name'] = $row['ClassName'];
   $processed_row['Room'] = $row['ClassRoom'];
   $processed_row['customColor'] = $row['ClassColor'];
    return $processed_row;
}
   
  public function getClassNames($class_day){
        $sql = "SELECT ClassPeriod, ClassName 
                FROM schedule_items
                 WHERE ClassDay=:day
                 AND UserID=(SELECT UserID 
                    FROM users 
                    WHERE Username=:email
                    LIMIT 1)
                ORDER BY ClassPeriod";
        try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":day", $class_day);
                $stmt->bindParam(":email", $_SESSION['Username']);
                $stmt->execute();
                $row = $stmt->fetch();
                $row = $this->processClassPeriods($row);
                while($row1 = $stmt->fetch()){
                    $row1 = $this->processClassPeriods($row1);
                    $row = array_merge($row, $row1);
                }
                $stmt->closeCursor();
            /*    $i = count($row) - 1;
                $j = count($row);
                $row[$i]=$row['ClassPeriod'];
                $row[$j]=$row['ClassName'];
              */ unset($row['ClassPeriod']);
                unset($row['ClassName']);
                return $row;
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
    }
public function processClassPeriods($row){
    if(strlen($row[0])==2&&intval($row[0]<10))$row[0]=substr($row[0],-1);
    return $row;
}
    
    
}
    ?>
