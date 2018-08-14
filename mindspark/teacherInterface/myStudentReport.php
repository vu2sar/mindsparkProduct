<?php

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/check1.php");
include("../userInterface/constants.php");
include("functions/functions.php");
include("functions/dashboardFunctions.php");
include("../slave_connectivity.php");
/*
function getTeacherTopicProgress2($teacherTopics,$cls,$userID)
{	
	$flowN	=	array();
	$total	=	0;
	$teacherTopicDetails = array();	

	foreach($teacherTopics as $ttCode=>$ttDesc)
	{		
                $teacherTopicDetails[$ttCode]["desc"]	=	$ttDesc;
        	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID = $userID AND teacherTopicCode='".$ttCode."'";
                $r = mysql_query($q);
                if(mysql_num_rows($r)>0) {            
                        while($l = mysql_fetch_array($r))
                        {
                        	$flowN = $l[0];
                        	$flowStr = str_replace(" ","_",$flowN);
                        	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
                        }
                
                	$sq	=	"SELECT userID, MAX(progress), SUM(noOfQuesAttempted),ROUND(SUM(perCorrect*noOfQuesAttempted)/SUM(noOfQuesAttempted),2),
                			 MAX(ttAttemptNo), GROUP_CONCAT(ttAttemptID), flow 
                			 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID = $userID";
	               	$rs	=	mysql_query($sq);
                	while($rw=mysql_fetch_array($rs))
                	{
                		$flowK	=	$rw[6];
                        $flowK	=	str_replace(" ","_",$flowK);
                		//$teacherTopicDetails[$rw[0]]["progress"]	=	$rw[1];
                		$teacherTopicDetails[$ttCode]["progress"]	=	${"objTopicProgress".$flowK}->getProgressInTT($rw[0]);                                
                		//$teacherTopicDetails[$ttCode]["higherLevel"] = ${"objTopicProgress".$flowK}->higherLevel;
						$teacherTopicDetails[$ttCode]["higherLevel"] = ${"objTopicProgress".$flowK}->getHigherLevel($rw[0]);
                		$arrayQuesDetails	=	getQuesAccuracy($rw[0],$ttCode,$cls);
                		$teacherTopicDetails[$ttCode]["totalQuesAttmpt"]	=	$arrayQuesDetails["totalQ"];
                		$teacherTopicDetails[$ttCode]["accuracy"]	=	$arrayQuesDetails["accuracy"];			
                		$teacherTopicDetails[$ttCode]["attempt"]	=	$rw[4];
                		$teacherTopicDetails[$ttCode]["failedCluster"]	=	getFailedLUs($rw[5], $cls, ${"objTopicProgress".$flowK});
                		$teacherTopicDetails[$ttCode]["flow"]	=	$rw[6];                		
                	}
                }
                else
                {
                        $teacherTopicDetails[$ttCode]["progress"]	=	"";
                        $teacherTopicDetails[$ttCode]["higherLevel"] = "";
                        $teacherTopicDetails[$ttCode]["totalQuesAttmpt"] = 0;
                        $teacherTopicDetails[$ttCode]["accuracy"] = "";
                        $teacherTopicDetails[$ttCode]["attempt"] = 0;
                        $teacherTopicDetails[$ttCode]["failedCluster"] = "";
                        $teacherTopicDetails[$ttCode]["flow"] = "";
                }
	}
	return $teacherTopicDetails;
}*/
$schoolCode=isset($_POST['schoolCode'])?$_POST['schoolCode']:"";
$username = isset($_POST['username'])?$_POST['username']:""; 
$class    = isset($_POST['childclass'])?$_POST['childclass']:"";
$section  = isset($_POST['section'])?stripslashes($_POST['section']):"";
$tillDate = isset($_POST['tillDate'])?$_POST['tillDate']:date("d-m-Y");

$fromDate = isset($_POST['fromDate'])?$_POST['fromDate']:$lastWeek;
$today = date("Y-m-d");
$tillDate = date('Y-m-d', strtotime(str_replace('-', '-', $tillDate)));
$fromDate = date('Y-m-d', strtotime(str_replace('-', '-', $fromDate)));
$getStudentDetails = array();
//$teacherTopicsArray	=	getTTs($class, $schoolCode, $section);
//print_r($teacherTopicsArray);
$allStudentDetails=array();
$getStudentDetails=getStudentDetailsBySection($schoolCode,$class,$section);
//print_r($getStudentDetails);

$allStudentDetails = array_column($getStudentDetails, 'username');


//print_r($allStudentDetails);
echo json_encode($getStudentDetails);
?>
