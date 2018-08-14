<?php

@include("../userInterface/check1.php");
@include("../slave_connectivity.php");

include("../userInterface/constants.php");
include("../userInterface/classes/clsUser.php");
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../logout.php");
    exit;
}
set_time_limit(0);
//$waittimeoutquery = "SET session wait_timeout=120";
//$waittimeoutdbqry = mysql_query($waittimeoutquery);
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$userID = $_SESSION['childID'];
if ($userID == '')
    $userID = 113619;
$class = $_SESSION['childClassUsed'];
if ($class == '')
    $class = 5;
$startDate = date('Y-m-d', strtotime("-15 days"));
$endDate = date('Y-m-d');
$studentName = $_SESSION['childNameUsed'];
//if($endDate=='')
//    $endDate = date('Y-m-d');
$startDate_int = str_replace("-", "", $startDate);
$endDate_int = str_replace("-", "", $endDate);
    function getTimeSpent($userID, $startDate_int, $endDate_int) {
//    $query = "SELECT DISTINCT sessionID, startTime, endTime, tmLastQues FROM adepts_sessionStatus
//			  WHERE  userID=" . $userID . " AND startTime_int <=$endDate_int AND startTime_int>=$startDate_int";
        $query = "SELECT sum(TIMESTAMPDIFF(SECOND, startTime,endTime)) FROM adepts_sessionStatus b
WHERE  b.userID=$userID AND startTime_int >= $startDate_int and startTime_int <=$endDate_int and endTime is not null;";
        $time_result = mysql_query($query) or die($query . mysql_error());
        $line = mysql_fetch_array($time_result);
        return $line[0];
    }
    
function getTimeSpentForClass($class, $startDate_int, $endDate_int) {
    $query = "SELECT sum(TIMESTAMPDIFF(SECOND, startTime,endTime))/count(distinct a.userID) FROM adepts_userDetails a, adepts_sessionStatus b
	      WHERE  a.userID=b.userID AND category='STUDENT' AND childClass=$class AND startTime_int >= $startDate_int and startTime_int <= $endDate_int 
            and endTime is not null;";
    $time_result = mysql_query($query)  or die($query . mysql_error());
    $line = mysql_fetch_array($time_result);
    return $line[0];
}
//echo date('m/d/Y h:i:s a');
$timeSpent = getTimeSpent($userID, $startDate_int, $endDate_int);
$timeSpentClass = getTimeSpentForClass($class, $startDate_int, $endDate_int);
//$timeSpentClass = 72*60;
$timeSpentDetails = array();
$timeSpentDetails['studentName'] = $studentName;
$timeSpentDetails['timeSpentStudent'] = round($timeSpent / 60);
$timeSpentDetails['studentClass'] = $class;
$timeSpentDetails['timeSpentClass'] = round($timeSpentClass / 60);
//echo $timeSpentDetails;
echo json_encode($timeSpentDetails);



if (!function_exists('convertToTime')) {
    function convertToTime($date) {
        $hr = substr($date, 11, 2);
        $mm = substr($date, 14, 2);
        $ss = substr($date, 17, 2);
        $day = substr($date, 8, 2);
        $mnth = substr($date, 5, 2);
        $yr = substr($date, 0, 4);
        $time = mktime($hr, $mm, $ss, $mnth, $day, $yr);
        return $time;
    }
}
?>