<?php include("header.php");
	include("../slave_connectivity.php");
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);	
	include_once("functions/functions.php");
	include_once("../userInterface/classes/clsTopicProgress.php");
	include_once("../userInterface/classes/clsTeacherTopic.php");

	$userID     = $_SESSION['userID'];
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$user   = new User($userID);
	if(strcasecmp($user->category,"Teacher")==0 || strcasecmp($user->category,"School Admin")==0) {
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}

	$class	=	$_GET['cls'];
	$section	=	$_GET['section'];
	$ttCode	=	$_GET['ttCode'];
	$userDetails	=	getStudentDetails($class, $schoolCode, $section);  //arr[userID][0] => userName , arr[userID][1] => Class section
	$userIDs	=	array_keys($userDetails);
	$userIDstr	=	implode(",",$userIDs);
	$q = "SELECT DISTINCT flow FROM ".TBL_TOPIC_STATUS." WHERE  userID in (".$userIDstr.") AND teacherTopicCode='".$ttCode."'";	
    $r = mysql_query($q);   
    while($l = mysql_fetch_array($r))
    {
    	$flowArray[] = $l[0];
    }   
   	foreach($flowArray as $flow)
   	{   		
		$ttObj = new teacherTopic($ttCode,$class,$flow);
		$clusterArray[] =	$ttObj->getClustersOfLevel($class);
   	}
   	$clusters = array_unique(array_reduce($clusterArray, 'array_merge', array()));     	
	$clusterString = implode("','", $clusters);
	$assessmentFlag = 0;
	$assessmentDetails = array();
	$coteacherTopicFlag = checkCoteacherTopic($ttCode,$class,$clusters);	
	if($coteacherTopicFlag)
	{
		$assessmentFlag = checkForAssessment($schoolCode,$class,$clusterString);		
		if($assessmentFlag)
			$assessmentDetails = getAssessmentDetails($ttCode,$userDetails); 
			
	}		
	$ttDetails	=	getTeacherTopicProgress($ttCode,$userIDstr,$class);

	$topicReportArray['coteacherTopicFlag'] = $coteacherTopicFlag;
	$topicReportArray['assessmentFlag'] = $assessmentFlag;
	$topicReportArray['assessmentDetails'] = $assessmentDetails;
	$topicReportArray['ttDetails'] = $ttDetails;
	echo "<pre>";
	print_r($topicReportArray);
	$topicJson = json_encode($topicReportArray);
	echo $topicJson;
?>

<?php

function checkForAssessment($schoolCode,$class,$clusterString){
	$assessmentFlag = 0;
	$querySettings = "SELECT * from userInterfaceSettings a where a.schoolCode=".$schoolCode." and a.class=".$class." and a.settingName='comprehensiveModuleActivation' and a.settingValue=1";		
	$settingsResult = mysql_query($querySettings);	
	if(mysql_num_rows($settingsResult) > 0)
	{
		$cmQuery = "SELECT a.comprehensiveModuleCode,a.linkedToDiagnosticTest from adepts_comprehensiveModuleMaster a JOIN adepts_diagnosticTestMaster b ON a.linkedToDiagnosticTest=b.diagnosticTestID and b.linkToCluster IN('$clusterString') where FIND_IN_SET('$clusterString',a.linkedToCluster) and a.status='Live' and b.status = 1 and b.testType='Assessment'";		
		$cmResult = mysql_query($cmQuery);	
		if($cmLine = mysql_fetch_row($cmResult))
			$assessmentFlag = 1;
	}

	return $assessmentFlag;
}
function checkCoteacherTopic($ttCode,$class,$topicClusters)
{
	$coteacherTopicFlag = 0;
	//check if topic is custom topic or parent
	$topicQuery = "SELECT customTopic,parentTeacherTopicCode,customCode from adepts_teacherTopicMaster where teacherTopicCode='$ttCode'";	
	$topicResult = mysql_query($topicQuery);
	$topicLine = mysql_fetch_row($topicResult);
	$customTopic = 	$topicLine[0];
	$customeCode = $topicLine[2];	
	$teacherTopicCode = $customTopic == 1? $topicLine[1] : $ttCode;	
	//check for if topic has entry for coteacher topics
	$ctQuery = "SELECT flow from coteacherTopicDetails where teacherTopicCode='$teacherTopicCode' and find_in_set($class,class) and status=1";
	 // echo $ctQuery;
	$ctResult = mysql_query($ctQuery);	
	while($ctLine = mysql_fetch_array($ctResult))
    {
    	$flowArray[] = $ctLine[0];
    } 
    if(!empty($flowArray))  
    {	
    	// making array of coteacher topic clusters
    	foreach($flowArray as $flow)
	   	{   		
			$ttObj = new teacherTopic($teacherTopicCode,$class,$flow);
			$clusterArray[] =	$ttObj->getClustersOfLevel($class);
	   	}
		$coteacherClusters = array_unique(array_reduce($clusterArray, 'array_merge', array()));
		if(!empty($coteacherClusters))
		{
			if (array_intersect($topicClusters, $coteacherClusters) == $topicClusters)
	    			$coteacherTopicFlag = 1;
	    }	   	
	   
    }
    return $coteacherTopicFlag;
   	
}
function getFailedClusters($ttAttemptID, $class, $objTopicProgress)
{
	//Get the failed clusters in the last completed attempt, if any, or the current attempt
	$failedClusterArray = array();
	$query  = "SELECT ttAttemptID, result, failedClusters FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID in ($ttAttemptID) ORDER BY ttAttemptID DESC";
	$result = mysql_query($query);
	$noOfAttempts = mysql_num_rows($result);
	while ($line = mysql_fetch_array($result))
	{
		if(($line[1]!="" && $noOfAttempts>1) || ($noOfAttempts==1))
		{
			if($line[2]!="")
			{
				$tmpCluster = explode(",",$line[2]);
				for($i=0; $i<count($tmpCluster); $i++)
				{
					$clusterCode = trim($tmpCluster[$i]);
					$levelArray = $objTopicProgress->objTT->getClusterLevel($clusterCode);
					if($levelArray[0] <= $class )	//Do not show  the clusters failed of  a higher level.
						array_push($failedClusterArray,trim($tmpCluster[$i]));
				}
			}
			break;
		}
	}
	return $failedClusterArray;
}

function getTeacherTopicProgress($ttCode,$userIDstr,$cls)
{
	$progress	=	array();
	$flowN	=	array();
	$total	=	0;
	
	$totalSDLS = 0;
    $clusterArray    = array();
    $sdls            = array();
    $clusterLevelArray      = array();
	$userTopicAttemptArray = array();

	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID in (".$userIDstr.") AND teacherTopicCode='".$ttCode."'";
    $r = mysql_query($q);
    while($l = mysql_fetch_array($r))
    {
    	$flowN = $l[0];
    	$flowStr = str_replace(" ","_",$flowN);
    	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
    }
	
	$sq	=	"SELECT userID, MAX(progress), SUM(noOfQuesAttempted),ROUND(SUM(perCorrect*noOfQuesAttempted)/SUM(noOfQuesAttempted),2),
			 MAX(ttAttemptNo), GROUP_CONCAT(ttAttemptID), flow ,result
			 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID IN ($userIDstr) GROUP BY userID";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$flowK	=	$rw[6];
    	$flowK	=	str_replace(" ","_",$flowK);
		//$teacherTopicDetails[$rw[0]]["progress"]	=	$rw[1];
		$teacherTopicDetails[$rw[0]]["progress"]	=	max($rw[1],${"objTopicProgress".$flowK}->getProgressInTT($rw[0]));
		//$teacherTopicDetails[$rw[0]]["higherLevel"] = ${"objTopicProgress".$flowK}->higherLevel;
		$teacherTopicDetails[$rw[0]]["higherLevel"] = ${"objTopicProgress".$flowK}->getHigherLevel($rw[0]);
		$arrayQuesDetails	=	getQuesAccuracy($rw[0],$ttCode,$cls);
		$teacherTopicDetails[$rw[0]]["totalQuesAttmpt"]	=	$arrayQuesDetails["totalQ"];
		$teacherTopicDetails[$rw[0]]["accuracy"]	=	$arrayQuesDetails["accuracy"];			
		$teacherTopicDetails[$rw[0]]["attempt"]	=	$rw[4];
		$teacherTopicDetails[$rw[0]]["failedCluster"]	=	getFailedClusters($rw[5], $cls, ${"objTopicProgress".$flowK});
		$teacherTopicDetails[$rw[0]]["flow"]	=	$rw[6];
		$teacherTopicDetails[$rw[0]]["result"]	=	$rw[7];
		$progress[]	=	$teacherTopicDetails[$rw[0]]["progress"];
		$flow[]	=	$rw[6];
		if(round($rw[1])==100)
			$total++;
	}
	$ttObj = new teacherTopic($ttCode,$cls,$flow[0]);
	$clusterArray	=	$ttObj->getClustersOfLevel($cls);
	
	$estimatedTimeToComplete = getEstimatedTimeToComplete($clusterArray,$cls);
	$learningUnitSummary = getLearningUnitSummary($clusterArray,$userIDstr,$cls);
	
	$totalProgress	=	round(array_sum($progress)/(substr_count($userIDstr,",")+1),2);	
	$teacherTopicDetails["avgProgress"]	=	$totalProgress;
	$teacherTopicDetails["totalCompleted"]	=	$total;
	$teacherTopicDetails["learningUnitSummary"] = $learningUnitSummary;
	$flow	=	array_unique($flow);
	//$teacherTopicDetails["flow"]	=	$flow;
	return $teacherTopicDetails;
}

function getLearningUnitSummary($clusterArray,$userIDstr,$cls)
{
	$arrayLearningUnitSummary = array();
	$totalUsers = count(explode(",",$userIDstr));
	$totalPassed = 0;
	foreach($clusterArray as $clusterCode)
	{
		$totalAttempted = 0;
		$totalCorrect = 0;
		$totalUserAttempted = 0;
		$clusterAttemptString = "";
		//gives most recent attempt
		$sq = "SELECT userID,clusterAttemptID,result from adepts_teacherTopicClusterStatus where clusterAttemptID IN (select MAX(clusterAttemptID) from adepts_teacherTopicClusterStatus where userID IN ($userIDstr) AND clusterCode='$clusterCode' and result IN('SUCCESS','FAILURE')  GROUP BY userID )";		
		$rs = mysql_query($sq);
		while($rw = mysql_fetch_array($rs))
		{
			$totalUserAttempted++;
			if($rw[2]=="SUCCESS")
				$totalPassed++;
			$clusterAttemptString .= $rw[1].",";
		}
		$clusterAttemptString = substr($clusterAttemptString,0,-1);
		$sqAccuracy = "SELECT round((SUM(R)/COUNT(srno))*100) FROM adepts_teacherTopicQuesAttempt_class$cls WHERE clusterAttemptID IN ($clusterAttemptString)";
		$rsAccuracy = mysql_query($sqAccuracy);
		$rwAccuracy = mysql_fetch_array($rsAccuracy);
		
		$arrayLearningUnitSummary[$clusterCode]["accuracy"] = $rwAccuracy[0];
		$arrayLearningUnitSummary[$clusterCode]["tick"] = 0;
		$arrayLearningUnitSummary[$clusterCode]["dash"] = 0;		
		if(($totalPassed/$totalUsers) > 0.75)
			$arrayLearningUnitSummary[$clusterCode]["tick"] = 1;
		if(($totalUserAttempted/$totalUsers) < 0.40)
			$arrayLearningUnitSummary[$clusterCode]["dash"] = 1;
	}
	return $arrayLearningUnitSummary;
}

function getDaysActivation($teacherTopiCode,$schoolCode,$childClass,$childSection)
{
	$arrayActivationDetails = array();
	$sq = "SELECT activationDate,deactivationDate,if(deactivationDate='0000-00-00',DATEDIFF(CURDATE(),activationDate),DATEDIFF(deactivationDate,activationDate)) FROM adepts_teacherTopicActivation WHERE teacherTopiCode='$teacherTopiCode' AND schoolCode='$schoolCode' AND class=$childClass AND section='=$childSection' ORDER by srno DESC LIMIT 1";
	$rs = mysql_query($sq);
	$rw = mysql_fetch_array($rs);
	if($rw[1]=="0000-00-00")
	{
		$activationFlag = 1;
		$daysActivation = date("d M Y",$rw[1])." (".$rw[2]." days ago)";
	}
	else
	{
		$activationFlag = 0;
		$daysActivation = date("d M Y",$rw[1])." (was active for ".$rw[2]." days.)";
	}
	$arrayActivationDetails["activationFlag"] = $activationFlag;
	$arrayActivationDetails["activationText"] = $daysActivation;
}

function getEstimatedTimeToComplete($clusterArray,$cls)
{
	$arrayTimeToComplete = array();
	$arrayClusterWiseTime = array();
	foreach($clusterArray as $clusterCode)
	{
		$sq = "SELECT SUM(time) FROM estimatedTimePerCluster1 WHERE clusterCode='$clusterCode' AND class=$cls";
		$rs = mysql_query($sq);
		if($rw = mysql_fetch_array($rs))
		{
			$arrayClusterWiseTime[$clusterCode] = $rw[0];
		}
	}
	$timeToComplete = array_sum($arrayClusterWiseTime) + ((array_sum($arrayClusterWiseTime)/count($arrayClusterWiseTime))*(count($clusterArray) - count($arrayClusterWiseTime)));
	
	$timeToComplete = $rw[0];
	$arrayTimeToComplete["minsToComplete"] = round($timeToComplete/60);
	$arrayTimeToComplete["sessionToComplete"] = ceil($timeToComplete/20);
}

function progressSummary($ttDetails)
{
	$array0to50	=	array();
	$array50to100	=	array();
	$array100	=	array();
	foreach($ttDetails as $userID=>$otherDetails)
	{
		if($otherDetails["progress"]<50)
			$arrayProgressDetails["array0to50"][$userID] = $otherDetails;
		else if($otherDetails["progress"]>=50 && $otherDetails["progress"]<100)
			$arrayProgressDetails["array50to100"][$userID] = $otherDetails;
		else
			$arrayProgressDetails["array100"][$userID] = $otherDetails;
	}
	return $arrayProgressDetails;
}

function getAssessmentDetails($ttCode,$userDetails)
{
	$assessmentArray = $arrayProgressDetails = 	array();
	$correctQuestions = $totalQuestions = 0;
	$userIDs	=	array_keys($userDetails);
	$userIDstr	=	implode(",",$userIDs);
	$sq = "SELECT GROUP_CONCAT(a.ttAttemptID) from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN($userIDstr)";
	$rs = mysql_query($sq);
	if($line=mysql_fetch_array($rs))
	{		
		$sq = "SELECT a.userID,SUM(c.R),count(c.srno) from adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b ON a.srno=b.srno JOIN adepts_diagnosticQuestionAttempt c ON c.attemptID=a.srno JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=b.diagnosticTestID where a.ttAttemptID IN($line[0]) and d.testType ='Assessment' and a.`status`=1 GROUP BY a.srno";		
		$rs = mysql_query($sq);
		while($line = mysql_fetch_array($rs))
		{	
			$correctQuestions += $line[1];
			$totalQuestions += $line[2];			
			$assessmentArray[$line[0]]['name']=$userDetails[$line[0]][0];			
			$assessmentArray[$line[0]]['accuracy']=ROUND($line[1]/$line[2]*100,2);	
		}
	}
	$userIDsAttempted = array_keys($assessmentArray);	
	$allUsersArray = explode(',', $userIDstr);
	foreach ($allUsersArray as $key => $value) {
		if(!in_array($value,$userIDsAttempted))
			$userIDnotCompleted[$value] = $userDetails[$value][0];
	}
	$arrayProgressDetails['totalStudents'] = count($userDetails);
	$arrayProgressDetails['completedStudents'] = count($assessmentArray);
	$arrayProgressDetails['avgAccuracy'] = ROUND($correctQuestions/$totalQuestions*100);
	$arrayProgressDetails['classAccuracyReport'] = assessmentAccuracySummary($assessmentArray);
	$arrayProgressDetails['classAccuracyReport']['userIDnotCompleted'] = $userIDnotCompleted;
	return $arrayProgressDetails;
}

function assessmentAccuracySummary($assessmentDetails)
{
	$arrayAccuracyDetails["array0to40"] = $arrayAccuracyDetails["array40to80"] = $arrayAccuracyDetails["arrayGt80"]= array();	
	foreach($assessmentDetails as $userID=>$otherDetails)
	{
		if($otherDetails["accuracy"]<40)
			$arrayAccuracyDetails["array0to40"][$userID] = $otherDetails;
		else if($otherDetails["accuracy"]>=40 && $otherDetails["accuracy"]<80)
			$arrayAccuracyDetails["array40to80"][$userID] = $otherDetails;
		else
			$arrayAccuracyDetails["arrayGt80"][$userID] = $otherDetails;	
	}
	return $arrayAccuracyDetails;
}

?>