 <?php
 class SchoolCalendar
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
  public function validateAdmin(){

    $sql = "SELECT IsAdmin
    FROM users
    Where Username=:user";
    try{
      $stmt = $this->_db->prepare($sql);
      $stmt->bindParam(":user",$_SESSION['Username']);
      $stmt->execute();
      $result = $stmt->fetch();
      $stmt->closeCursor();
      if($result['IsAdmin']==1)return true;
      else return false;
    }
    catch(PDOException $e){
      die($e->getMessage());
    }
  }
  public function getBellScheduleNames(){
    $sql = "SELECT BellScheduleName, AlternateName
            FROM bell_schedules";
    try{
      $stmt = $this->_db->prepare($sql);
      $stmt->execute();
      $result = array();
      $result[] = ["delete","No Classes"];
      while($row = $stmt->fetch()){
        if(isset($row["BellScheduleName"])&&$row["BellScheduleName"]!=null){
          $row1 = array();
          $row1[0] = $row["BellScheduleName"];
          $row1[1] = $row["AlternateName"];
          $result[] = $row1;
        }
      }
      $stmt->closeCursor();
      return $result;

     }
     catch(PDOException $e)
     {
       die($e->getMessage());
       return;
     }
     return '[["delete","No School"],["Regular","Regular (M-Th)"],["Friday","Friday"],["Early Dismissal","Early Dism (4:40)"],["Fast Day","Fast Day (1:45)"],["AM Assembly","AM Assembly"],["PM Assembly","PM Assembly"],["Custom","Custom"]]';
  }
public function getDayNames(){
return '[["delete","No Classes"],["M","Monday"],["A","A Day"],["B","B Day"],["C","C Day"],["T","Thursday"],["F","Friday"]]';

}


  public function getValidSchoolDays($currentMonth){
    $schoolid = 1;
    $sql = "SELECT SchoolDate, DayName, BellScheduleName
    FROM school_days
    WHERE SchoolYearID=:schoolid";
    try{
      $stmt = $this->_db->prepare($sql);
      $stmt->bindParam(":schoolid", $schoolid);
      $stmt->execute();
      $result = array();

      while($row = $stmt->fetch()){
        if(isset($row["SchoolDate"])&&$row["SchoolDate"]!=null){
          $month = date('m',strtotime($row["SchoolDate"]));
          settype($month,"integer");
          settype($currentMonth,"integer");
             //return "Month is ".$month." vs ".$currentMonth;
          if($month===$currentMonth){
            $row1 = array();
            $row1["Date"]=$row["SchoolDate"];
            $row1["Classes"]=$row["DayName"];
            $row1["Bells"]=$row["BellScheduleName"];
            $result[] = $row1;
            //  $result[]=$month;
          }
        }
      }
      $stmt->closeCursor();
      return $result;
    }
    catch(PDOException $e)
    {
      die($e->getMessage());
    }
  }

    public function updateSchoolDay($date,$class_schedule,$bell_schedule){

    //Test to see if there's an existing entry for the given day
     $sql = "SELECT COUNT(SchoolDayID) AS theCount
     FROM school_days
     WHERE SchoolDate=:date_test";
     if($stmt = $this->_db->prepare($sql)) {
      $stmt->bindParam(":date_test", $date);
      $stmt->execute();
      $row = $stmt->fetch();
      $stmt->closeCursor();
    //if classes are scheduled
      if($row['theCount']!=0) {
      //if we're supposed to be deleting the class
        if(strtolower($class_schedule)===strtolower("delete")||strtolower($class_schedule)===strtolower("\"delete\"")){ 
        //delete if (1) classes are scheduled and (2) we're doing deletion
          return $this->deleteSchoolDay($date);
        }
      //modify if classes are scheduled, but we aren't doing deletion
        return $this->modifySchoolDay($date,$class_schedule,$bell_schedule);
      }
      //add if classes aren't scheduled yet
      return $this->addSchoolDay($date,$class_schedule,$bell_schedule);
    }
    //if the SQL operation failed, return an error
    return "Unable to determine if classes are scheduled on $date.";
  }

  public function deleteSchoolDay($date){
    $sql = "DELETE FROM school_days 
    WHERE SchoolDate=:date_to_delete";
    if($stmt=$this->_db->prepare($sql)){
      $stmt->bindParam(":date_to_delete", $date);
      $stmt->execute();
      $stmt->closeCursor();
      return "Deleted classes scheduled on $date.";
    }
    return "Unable to delete classes on $date.";
  }


  public function addSchoolDay($date,$class_schedule,$bell_schedule){
    //Safety - If someone tries to "delete" a school day which already has no classes scheduled
    if(strtolower($class_schedule)===strtolower("delete")||strtolower($class_schedule)===strtolower("\"delete\"")){ 
      return "Error: no classes are scheduled on $date - cannot delete nonexistent day.";
    }

    $sql="  INSERT INTO school_days 
          (`SchoolYearID`, `SchoolDate`, `DayName`, `BellScheduleName`)
          VALUES ('1', :date_insert, :classes, :bells)";
    if($stmt=$this->_db->prepare($sql)){
      $stmt->bindParam(":date_insert", $date);
      $stmt->bindParam(":classes", $class_schedule);
      $stmt->bindParam(":bells", $bell_schedule);
      $stmt->execute();
      $stmt->closeCursor();
      return "Added classes on $date: $class_schedule day with $bell_schedule bells.";
      }

    return "Unable to add classes on $date.";    
  }
  public function modifySchoolDay($date,$class_schedule,$bell_schedule){
    $sql="UPDATE `school_days` 
          SET `DayName` = :classes, `BellScheduleName` = :bells
          WHERE `school_days`.`SchoolDate` = :date_change";
    if($stmt=$this->_db->prepare($sql)){
      $stmt->bindParam(":date_change", $date);
      $stmt->bindParam(":classes", $class_schedule);
      $stmt->bindParam(":bells", $bell_schedule);
      $stmt->execute();
      $stmt->closeCursor();
      return "Changed schedules for $date: $class_schedule day with $bell_schedule bells.";
      }
    return "Unable to modify classes on $date.";    
  }
}
?>

