<?php
//paina
define("TEST", 0); 

if(TEST == 0) {
	include_once("../../slave_connectivity.php");
	error_reporting(E_ERROR & ~E_DEPRECATED) ;
	mysql_select_db("educatio_adepts") or die (mysql_errno());	
}

//include("../../userInterface/common/constants.php");	
include_once("../../userInterface/classes/clsTopicProgress.php");
include_once("../../userInterface/classes/clsTeacherTopic.php");

#################### CONSTANTS ######################################
//TODO - use commmon
define("TBL_USER_DETAILS", "adepts_userDetails");
define("TBL_TOPIC_MASTER", "adepts_teacherTopicMaster"); //change name
define("TBL_CLUSTER_MASTER", "adepts_clusterMaster");
define("TBL_GAME_DETAILS", "adepts_userGameDetails");
define("TBL_GAME_MASTER", "adepts_gamesMaster");
define("TBL_REMEDIAL_ITEM_MASTER", "adepts_remedialItemMaster");
define("TBL_REMEDIAL_ITEM_ATTEMPT", "adepts_remedialItemAttempts");
define("TBL_TT_CLUSTER_MASTER", "adepts_teacherTopicClusterMaster");
define("TBL_TT_ACTIVATION", "adepts_teacherTopicActivation");
define("TBL_TT_CLUSTER_STATUS", "adepts_teacherTopicClusterStatus");
define( 'TBL_SESSION_STATUS', "adepts_sessionStatus" );
define( 'TBL_TOPIC_STATUS', "adepts_teacherTopicStatus" );
define( 'TBL_CLUSTER_STATUS', "adepts_teacherTopicClusterStatus" );
define( 'TBL_QUES_ATTEMPT', "adepts_teacherTopicQuesAttempt" );
define( 'TBL_CURRENT_STATUS', "adepts_ttUserCurrentStatus" );
define( 'TBL_TOPIC_REVISION', "adepts_topicRevisionDetails" );
define( 'TBL_QUES_ATTEMPT_CLASS', "adepts_teacherTopicQuesAttempt_class");
define('TBL_QUESTIONS', "adepts_questions");
define('TBL_MISCONCEPTIONS_MASTER', "educatio_educat.misconception_master");
define('TBL_TIMED_TEST_DETAILS', "adepts_timedTestDetails");
define('TBL_TIMED_TEST_MASTER', "adepts_timedTestMaster");
define('TBL_SECTIONWISE_DAILY_DETAILS', "sectionWiseDailyDetails");
define('TBL_HOME_USAGE', "adepts_homeSchoolUsage");
define("SUBJECTNO", 2);

#################### FUNCTIONS ######################################
//TODO 
#1. Use startTime_int
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

#-------------------------------Impact Summary-------------------------------------------------------------------------
// TODO
# 1. Higher Level Reached

function getImpactSummaryDetails($schoolCode, $class, $section, $startDate, $endDate) {
	$impactSummaryDetails = array();
	$sectionStudentDetails = getStudentDetailsBySection($schoolCode,$class, $section);
	$numOfStudents = sizeof($sectionStudentDetails);
	$studentsArr = array_column($sectionStudentDetails, 'userID');
	
	$totalQuestionsAttempted = getTotalQuestionsAttemptedByClass($studentsArr, $startDate, $endDate);
	$classAttemptAverage = ($numOfStudents > 0)? round($totalQuestionsAttempted/$numOfStudents) : "NA";

	$impactSummaryDetails['questions'] = array("total" => $totalQuestionsAttempted, "average" => $classAttemptAverage);
	$impactSummaryDetails['remedials'] = getTotalRemedialsClearedByClass($studentsArr, $startDate, $endDate);
	$impactSummaryDetails['activities'] = getTotalActivitiesAttemptedByClass($studentsArr, $startDate, $endDate);
	$impactSummaryDetails['higherLevel'] = 4; //TODO #1
	return $impactSummaryDetails;
}

function getTotalQuestionsAttemptedByClass($studentsArr, $startDate, $endDate) {
	$query = "SELECT COALESCE(SUM(a.totalQ)+SUM(a.totalCQ)+SUM(a.totalMonRevQ)+SUM(a.totalTopRevQ),0) AS total FROM ".TBL_SESSION_STATUS." a, ". TBL_USER_DETAILS ." b 
		WHERE a.userID=b.userID 
		AND a.startTime_int >= ".getIntDate($startDate)." AND a.startTime_int <= ".getIntDate($endDate)."  
		AND b.userID IN (". implodeArrayForQuery($studentsArr).")";
	$result = mysql_query($query);
	$line = mysql_fetch_assoc($result);
	$total = $line['total'];
	return $total;
}

function getTotalRemedialsClearedByClass($studentsArr, $startDate, $endDate) {
	$remedialsArr = getRemedialsClearedByClass($studentsArr, $startDate, $endDate);
	return sizeof($remedialsArr);	
}

function getRemedialsClearedByClass($studentsArr, $startDate, $endDate) {

	$remedialsArr = array();

	$query = "SELECT distinct(remedialItemCode) AS remedialItemCode FROM ".TBL_REMEDIAL_ITEM_ATTEMPT." a INNER JOIN ".TBL_SESSION_STATUS." b ON a.sessionID = b.sessionID WHERE b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." AND a.result=1 AND a.userID IN (". implodeArrayForQuery($studentsArr).")";

	$result = mysql_query($query) or die(mysql_errno());
	while($line = mysql_fetch_assoc($result)) {
		$remedialsArr[] = $line['remedialItemCode'];
	}

	return $remedialsArr;	
}

function getRemedialDesc($remedialsArr) {
	$remedialDescArr = array();
	$query = "SELECT remedialItemCode, remedialItemDesc FROM ".TBL_REMEDIAL_ITEM_MASTER." WHERE remedialItemCode IN (".implodeArrayForQuery($remedialsArr) . ")";

	$result = mysql_query($query) or die(mysql_errno());
	while($line = mysql_fetch_assoc($result)) {
		$remedialDescArr[$line['remedialItemCode']] = $line['remedialItemDesc'];
	}
	
	return $remedialDescArr;
}

function getTotalActivitiesAttemptedByClass($studentsArr, $startDate, $endDate) {

	$query = "SELECT count(a.userID) AS attempts 
	FROM ".TBL_GAME_DETAILS." a INNER JOIN ".TBL_SESSION_STATUS." b 
	ON a.sessionID = b.sessionID WHERE b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." AND a.userID IN (". implodeArrayForQuery($studentsArr).")"; 

	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	$attempts = $row['attempts'];
	return $attempts;
}

#-------------------------------Change in Topic Progress---------------------------------------------------------------
//ippudu

function getTopicProgressSummaryDetails ($schoolCode, $class, $section, $startDate, $endDate) {
	$summary = array();
	$higherLevelStudents = array();
	$totalHigherLevelReached = 0;
	$changeInTopicProgressArr = getChangeInTeacherTopicProgress($schoolCode, $class, $section, $startDate, $endDate);
	$ttCodeArr = array_keys($changeInTopicProgressArr);
	$ttDescArr = getTopicDescFromTTCodes($ttCodeArr);
	// printArr($changeInTopicProgressArr);
	foreach ($ttCodeArr as $ttCode) {
		$ttObj = array();
		$ttObj['ttCode'] = $ttCode;
		$ttObj['ttDesc'] = $ttDescArr[$ttCode];
		$ttObj['startProgress'] = $changeInTopicProgressArr[$ttCode]['startProgress'];
		$ttObj['endProgress'] = $changeInTopicProgressArr[$ttCode]['endProgress'];
		if(array_key_exists('currentProgress', $changeInTopicProgressArr[$ttCode])) {
			$ttObj['currentProgress'] = $changeInTopicProgressArr[$ttCode]['currentProgress'];
		}
		$summary[] = $ttObj;
		$totalHigherLevelReached += $changeInTopicProgressArr[$ttCode]['higherLevelReached'];
		$higherLevelStudents = array_unique(array_merge($higherLevelStudents, $changeInTopicProgressArr[$ttCode]['higherLevelStudents']));
	}

	$namesArr = getNamesFromUserIDs($higherLevelStudents);
	return array(
				"class" => $class,
				"section" => $section,
				"dateRange" => $startDate."~".$endDate,
				"ttProgress" => $summary, 
				"totalHigherLevelReached" => $totalHigherLevelReached, 
				"higherLevelStudents" => array_values($namesArr)
				);
}

function getChangeInTeacherTopicProgress($schoolCode, $class, $section, $startDate, $endDate) {
	$sectionStudentDetails = getStudentDetailsBySection($schoolCode,$class, $section);
	$studentsArr = array_column($sectionStudentDetails, 'userID');
	$ttCodeArr = getActiveTopicsForDateRange($schoolCode, $class, $section, $startDate, $endDate);
	$ttStartProgress = getTeacherTopicProgressForSection($class, $studentsArr, $ttCodeArr, $startDate);
	$ttEndProgress = getTeacherTopicProgressForSection($class, $studentsArr, $ttCodeArr, $endDate);
	
	if(isShowCurrentProgress($endDate)) {
		$ttCurrentProgress = getTeacherTopicProgressForSection($class, $studentsArr, $ttCodeArr, date("Y-m-d"));
		$changeInTopicProgressArr = mergeProgressArray($ttStartProgress, $ttEndProgress, $ttCurrentProgress);
	} else {
		$changeInTopicProgressArr = mergeProgressArray($ttStartProgress, $ttEndProgress);	
	}

	return $changeInTopicProgressArr;
}
function getTopicProgresFromTT($studentId,$ttCode) // Function to get progress and desc of each topic based on student Id and Topic Codes
{
 $topicProgressArr=array();
 $query="select ast.teacherTopicCode, att.teacherTopicDesc as description,SUM(noOfQuesAttempted) as noOfQuesAttempted, MAX(progress) as prg from ".TBL_TOPIC_STATUS." as ast
 inner join ".TBL_TOPIC_MASTER." as att on ast.teacherTopicCode=att.teacherTopicCode
 where ast.teacherTopicCode IN ('".implode("','", $ttCode)."') and userID=".$studentId." GROUP BY teacherTopicCode";
 //echo $query;
 $result = mysql_query($query) or die (mysql_errno());
$k=0;
  while($line = mysql_fetch_assoc($result)) {
  	
  	$topicProgressArr[$k]['desc']=$line['description'];
    $topicProgressArr[$k]['progress'] = $line['prg'];	
  	$k++;
  }
  return $topicProgressArr;

}

function getTopicDescFromTTCodes($ttCodeArr) {
	$topicNameArr = array();
	$query = "SELECT teacherTopicCode, teacherTopicDesc FROM ".TBL_TOPIC_MASTER." WHERE teacherTopicCode IN (". implodeArrayForQuery($ttCodeArr) .")";
	$result = mysql_query($query) or die (mysql_errno());
	while($line = mysql_fetch_assoc($result)) {
		$topicNameArr[$line['teacherTopicCode']] = $line['teacherTopicDesc'];	
	}
	return $topicNameArr;
}

function isShowCurrentProgress($endDate) {
	$nowDate = date("Y-m-d");
	if(strtotime($endDate) <= strtotime($nowDate))
		return TRUE;
	else 
		return FALSE;
}

function mergeProgressArray($startArr, $endArr, $currentArr = array()) {
	$mergedArr = array();
	$isMergeCurrent = (sizeof($currentArr) > 0)? TRUE : FALSE;
	foreach (array_keys($startArr) as $ttCode) {
		$mergedArr[$ttCode]['startProgress'] = $startArr[$ttCode]['avgProgress'];
		$mergedArr[$ttCode]['endProgress'] = $endArr[$ttCode]['avgProgress'];
		if($isMergeCurrent) {
			$mergedArr[$ttCode]['currentProgress'] = $currentArr[$ttCode]['avgProgress'];	
		}
		$mergedArr[$ttCode]['higherLevelReached'] = $endArr[$ttCode]['higherLevelReached'];
		$mergedArr[$ttCode]['higherLevelStudents'] = array_unique(array_merge($endArr[$ttCode]['higherLevelStudents'], $startArr[$ttCode]['higherLevelStudents'])); 

		}
		uasort($mergedArr, "progressArraySortHelper");
		return $mergedArr;
}

function getTotalHigherLevelReached($mergedProgressArr) {
	$sum = 0;
	foreach ($mergeProgressArray as $ttCode => $progressDetails) {
		$sum += $progressDetails['higherLevelReached'];		
	}
	return $sum;
}


function progressArraySortHelper($a, $b) {
	if($a['endProgress']-$a['startProgress'] >= $b['endProgress']-$b['startProgress']) 
		return -1;
	 else 
		return 1;
}

function getActiveTopicsForDateRange($schoolCode, $class, $section, $startDate, $endDate) {
	$activeTopicsArr = array();
	$query = "SELECT teacherTopicCode FROM ".TBL_TT_ACTIVATION." WHERE schoolCode=$schoolCode AND class=$class AND activationDate <= '$endDate' AND (deactivationDate = '0000-00-00' OR (deactivationDate > '$startDate' AND  deactivationDate < '$endDate')) ";
	if($section != "") {
		$sectionsArray = explode(",",$section);
			$query .= " AND section IN (". implodeArrayForQuery($sectionsArray) . ")";
	}
	
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $activeTopicsArr[] = $line['teacherTopicCode'];
	}
	return $activeTopicsArr;
}

function getTeacherTopicProgressForSection($class, $studentsArr, $ttCodeArr, $tillDate) {
	$ttProgressArr = array();
	foreach ($ttCodeArr as $ttCode) {	
		$ttProg = getTeacherTopicProgress($ttCode, $studentsArr, $class, $tillDate);
		$ttProgressArr[$ttCode]['avgProgress'] = $ttProg['avgProgress'];
		$ttProgressArr[$ttCode]['higherLevelReached'] = $ttProg['higherLevelReached'];
		$ttProgressArr[$ttCode]['higherLevelStudents'] = $ttProg['higherLevelStudents'];
	}

	return $ttProgressArr;
}

function getTeacherTopicProgress($ttCode, $studentsArr, $cls, $tillDate) {

	$progress = array();
	$higherLevelStudents = array();
	$higherLevelReached = 0;	
	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID IN (".implodeArrayForQuery($studentsArr).") AND teacherTopicCode='".$ttCode."'";
  $r = mysql_query($q);
  while($l = mysql_fetch_array($r))
  {
  	$flowN = $l[0];
  	$flowStr = str_replace(" ","_",$flowN);
  	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
  }

  $sq	=	"SELECT userID, MAX(progress), SUM(noOfQuesAttempted),ROUND(SUM(perCorrect*noOfQuesAttempted)/SUM(noOfQuesAttempted),2),
  		 MAX(ttAttemptNo), GROUP_CONCAT(ttAttemptID), flow 
  		 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID IN (".implodeArrayForQuery($studentsArr).") GROUP BY userID";
  $rs	=	mysql_query($sq);
  while($rw=mysql_fetch_array($rs)) {
		$flowK	=	$rw[6];
    $flowK	=	str_replace(" ","_",$flowK);
	$teacherTopicDetails[$rw[0]]["progress"]	=	${"objTopicProgress".$flowK}->getProgressInTT($rw[0], $tillDate);
  	$teacherTopicDetails[$rw[0]]["higherLevel"] = ${"objTopicProgress".$flowK}->getHigherLevel($rw[0], $tillDate);
  	$progress[] = $teacherTopicDetails[$rw[0]]["progress"];
  	$higherLevelReached += $teacherTopicDetails[$rw[0]]["higherLevel"];
  	if($teacherTopicDetails[$rw[0]]["higherLevel"] == 1) {
  		$higherLevelStudents[] = $rw[0];
  	}
  }

  $avgProgress = round(array_sum($progress)/sizeof($studentsArr),2);
  // printArr($higherLevelStudents);
  return array("avgProgress" => $avgProgress, "higherLevelReached" => $higherLevelReached, "higherLevelStudents" => $higherLevelStudents);
}

#-------------------------------Areas Handled by Mindspark-------------------------------------------------------------

function getAreasHandledByMindsparkDetails($schoolCode, $class, $section, $startDate, $endDate) {
	$areasHandledByMindspark = array();
	$sectionStudentDetails = getStudentDetailsBySection($schoolCode,$class, $section);
	$studentsArr = array_column($sectionStudentDetails, 'userID');
	$areasHandledByMindspark['mostClearedRemedial'] = getMostClearedRemedial($studentsArr, $startDate, $endDate);
	$areasHandledByMindspark['mostAttemptedActivity'] = getMostAttemptedActivity($studentsArr, $startDate, $endDate);
	$areasHandledByMindspark['top3MostAttemptedPracticeModules'] = getTop3MostAttemptedPracticeModules($studentsArr, $startDate, $endDate);
	return $areasHandledByMindspark;
}

function getMostClearedRemedial($studentsArr, $startDate, $endDate) {

		$query_remedial = "SELECT remedialItemCode, remedialItemDesc, linkedToCluster FROM ".TBL_REMEDIAL_ITEM_MASTER." WHERE remedialItemCode IN 
			(SELECT kk.remedialItemCode FROM (SELECT a.remedialItemCode, sum(IF(a.result = 1, 1, 0)) AS count
					FROM ".TBL_REMEDIAL_ITEM_ATTEMPT." a INNER JOIN ".TBL_SESSION_STATUS." b 
					ON a.sessionID = b.sessionID
					WHERE b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." AND a.userID 
					IN (". implodeArrayForQuery($studentsArr).") GROUP BY a.remedialItemCode ORDER BY count desc limit 1) as kk)";
	
		$result_remedial = mysql_query($query_remedial) or die (mysql_errno());
		$row = mysql_fetch_assoc($result_remedial);
		$remedialItemDesc = $row['remedialItemDesc'];
		$clusterCode = $row['linkedToCluster'];
		$remedialItemCode = $row['remedialItemCode'];

		$query_topicName = "SELECT teacherTopicDesc FROM ".TBL_TOPIC_MASTER." 
			WHERE teacherTopicCode IN (SELECT teacherTopicCode FROM ".TBL_TT_CLUSTER_MASTER." WHERE clusterCode = '$clusterCode') limit 1";
			//get topic name that belongs to the section
		$result_topicName = mysql_query($query_topicName) or die (mysql_errno());
		$line = mysql_fetch_assoc($result_topicName);
		$topicName = $line['teacherTopicDesc'];
		return array("remedialItemCode" => $remedialItemCode, "remedialItemDesc" => $remedialItemDesc, "topicName" => $topicName);
}

function getMostAttemptedActivity($studentsArr, $startDate, $endDate) {
	
	$query = "SELECT a.gameID, d.gameDesc AS gameDesc, count(a.userID) AS attempts 
	FROM ".TBL_GAME_DETAILS." a INNER JOIN ".TBL_SESSION_STATUS." b INNER JOIN ".TBL_GAME_MASTER." d 
	ON a.sessionID = b.sessionID AND a.gameID = d.gameID 
	WHERE b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." AND a.userID IN (". implodeArrayForQuery($studentsArr).") 
	GROUP BY a.gameID ORDER BY attempts DESC LIMIT 1";	

	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);
	$gameID = $row['gameID'];
	$gameDesc = $row['gameDesc'];
	return array("gameID" => $gameID, "gameDesc" => $gameDesc);
}

function getTop3MostAttemptedPracticeModules($studentsArr, $startDate, $endDate) 
{
	$tempReturnArr = array();
	$returnArr = array();
	$clusterArr = array();
	$query = "SELECT a.practiseModuleId AS practiceModuleId, a.description AS practiceModuleName, a.linkedToCluster AS linkedToCluster, COUNT(DISTINCT(b.userID)) AS studentsAttempted, DATE(MAX(b.lastModified)) AS attemptedDate FROM practiseModuleDetails a, practiseModulesTestStatus b WHERE a.practiseModuleId=b.practiseModuleId AND a.`status`='Approved' AND a.dailyDrill=1 AND b.userID IN(".implodeArrayForQuery($studentsArr).") AND DATE(b.lastModified)>='".$startDate."' AND DATE(b.lastModified)<='".$endDate."' GROUP BY a.practiseModuleId HAVING studentsAttempted > ROUND(0.20*".count($studentsArr).") ORDER BY studentsAttempted DESC, b.lastModified DESC LIMIT 3";
	$result = mysql_query($query);
	$cnt = 0;
	while ($row=mysql_fetch_assoc($result)) {
		$returnArr[$cnt]['practiceModuleId'] = $row['practiceModuleId'];
		$returnArr[$cnt]['practiceModuleName'] = $row['practiceModuleName'];
		$returnArr[$cnt]['studentsAttempted'] = $row['studentsAttempted'];
		$returnArr[$cnt]['totalStudents'] = count($studentsArr);
		$clusterArr[$cnt] = $row['linkedToCluster'];
		$cnt++;
	}

	$queryTopics = "Select a.clusterCode, b.teacherTopicCode AS topic, count(a.clusterAttemptID) noOfAttempts from adepts_teacherTopicClusterStatus a, adepts_teacherTopicStatus b WHERE a.ttAttemptID = b.ttAttemptID AND a.clusterCode IN(".implodeArrayForQuery($clusterArr).") AND a.userID IN (".implodeArrayForQuery($studentsArr).") GROUP BY b.teacherTopicCode ORDER BY noOfAttempts DESC";
	$resultTopics = mysql_query($queryTopics);
	$tempClusterArr = array();
	$cnt = 0;
	while ($row=mysql_fetch_array($resultTopics)) 
	{
		if(!in_array($row[0], $tempClusterArr))
		{
			$returnArr[$cnt]['topic'] = $row[1];
			array_push($tempClusterArr, $row[0]);		
			$cnt++;			
		}
	}
	return $returnArr;
}
#-------------------------------Overall Accuracy-----------------------------------------------------------------------

function getOverallAccuracySummary($schoolCode, $class, $section, $startDate, $endDate) {
	$overallAccuracySummary = array();
	$sectionStudentDetails = getStudentDetailsBySection($schoolCode,$class, $section);
	$numOfStudents = sizeof($sectionStudentDetails);
	$studentsArr = array_column($sectionStudentDetails, 'userID');

	$clusterCodeArr = getClustersAttemptedBySection($studentsArr, $startDate, $endDate, $class);

	$accuracyArr = getAccuracyAndUsageForClusters($clusterCodeArr, $studentsArr, $class, $startDate, $endDate, $numOfStudents);
	uasort($accuracyArr, "sortByAccuracyHelper");	
	$overallAccuracySummary['accuracySummaryGraphDetails'] = getAccuracySummaryGraphDetails($accuracyArr,$numOfStudents);
	$overallAccuracySummary['greatAccuracyClusters'] = getAccuracyCategoryClusterNames($accuracyArr, "great");
	$overallAccuracySummary['lowAccuracyClusters'] = getAccuracyCategoryClusterNames($accuracyArr, "low");
	$overallAccuracySummary['misconceptionsIdentified'] = getMisconceptionsIdentified($studentsArr, $startDate, $endDate);
	$overallAccuracySummary['clusterList'] = $clusterCodeArr;

//	getTTCodeStringForClusters($schoolCode, $class, $section, $studentsArr, $clusterCodeArr);	
	return $overallAccuracySummary;
}

function sortByAccuracyHelper($a, $b) {
	if($a['accuracy'] < $b['accuracy']) {
		return 1;
	} elseif($a['accuracy'] > $b['accuracy']) {
		return -1;
	}
	return 0;
}

function getMisconceptionsIdentified($studentsArr, $startDate, $endDate) {
	$remedialsArr = getRemedialsClearedByClass($studentsArr, $startDate, $endDate);
	$remedialDescArr = getRemedialDesc($remedialsArr);
	return array_values($remedialDescArr);
}

function getAccuracyCategoryClusterNames($accuracyArr, $category) {
	$clusterNames = array();
	foreach ($accuracyArr as $clusterCode => $clusterDetails) {
		if(strcmp($clusterDetails['category'], $category) == 0) {
			$clusterNames[] = $clusterDetails['name'];
		}
	}
	return $clusterNames;
}

function getAccuracySummaryGraphDetails($accuracyArr,$numOfStudents) {
	$categories = array_column($accuracyArr, 'category');
	$accuracySummaryGraphDetails = array_count_values($categories);			
	$accuracySummaryGraphDetails['classAvg'] = categorizeAccuracy(getAverageAccuracy($accuracyArr));	
	return $accuracySummaryGraphDetails;
}

function getAverageAccuracy($accuracyArr) {
	$sum = 0;
	$avg = 0;
	$notEnoughUsage = 0;
	if(sizeof($accuracyArr) != 0) {
		foreach ($accuracyArr as $clusterCode => $clusterDetails) {
			if(isset($clusterDetails['category']) && strcmp($clusterDetails['category'], 'notEnoughUsage') != 0)
				$sum += $clusterDetails['accuracy'];
			else 
				$notEnoughUsage += 1; 
		}

		$divider = sizeof($accuracyArr) - $notEnoughUsage;
		$avg  = $divider != 0? round($sum/$divider, 2):-1;
				
	}
	return $avg;
}

function isEnoughUsage($userCount, $numOfStudents) {
	if($userCount/$numOfStudents >= 0.5)
		return TRUE;
	return FALSE;
}

function getClustersAttemptedBySection($studentsArr, $startDate, $endDate, $class) {
  $clusterCodeArr = array();
	$query = "SELECT DISTINCT a.clusterCode AS clusterCode
		FROM ".TBL_QUES_ATTEMPT."_class$class a INNER JOIN ".TBL_SESSION_STATUS." b ON a.sessionID = b.sessionID 
		WHERE b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." AND a.userID IN (". implodeArrayForQuery($studentsArr).")";
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $clusterCodeArr[] = $line['clusterCode'];
	}
	return $clusterCodeArr;
}

function getClustersAttemptedWithinDateRange($studentsArr, $startDate, $endDate, $class) {
  $clusterCodeArr = array();
        $sessionIDArr = array();
        $query = "SELECT sessionID FROM ".TBL_SESSION_STATUS." WHERE startTime_int >= ".getIntDate($startDate)." AND startTime_int <= ".getIntDate($endDate)." AND userID IN (". implodeArrayForQuery($studentsArr).")";
        $result = mysql_query($query) or die("Error in query"); 

        while($line = mysql_fetch_array($result))
        {
            array_push($sessionIDArr, $line['sessionID']);    
        }
	/*$query = "SELECT DISTINCT a.clusterCode AS clusterCode, a.teacherTopicCode AS teacherTopicCode FROM ".TBL_QUES_ATTEMPT."_class$class a INNER JOIN ".TBL_SESSION_STATUS." b ON a.sessionID = b.sessionID WHERE b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." AND a.userID IN (". implodeArrayForQuery($studentsArr).")";*/
        if(count($sessionIDArr)>0) {        
                $query = "SELECT DISTINCT clusterCode , teacherTopicCode  FROM ".TBL_QUES_ATTEMPT."_class$class  WHERE sessionID IN (".implodeArrayForQuery($sessionIDArr).") AND userID IN (". implodeArrayForQuery($studentsArr).")";
                // echo $query;
        	$result = mysql_query($query) or die(mysql_errno());        	
        	while($line=mysql_fetch_array($result)) {
        		 $clusterCodeArr[$line['clusterCode']] = $line['teacherTopicCode'];
        	}
        }
	return $clusterCodeArr;
}

function getTTCodeStringForClusters($schoolCode, $class, $section, $studentsArr, $clusterCodeArr) {
	$ttCodeArr = getActivatedAndDeactivatedTopicsForClass($schoolCode, $class, $section);
	// printArr("getActivatedAndDeactivatedTopicsForClass=" . sizeof($ttCodeArr));
	$ccArr = getClustersForTopics($studentsArr, array_keys($ttCodeArr), $class);
	$ttArr = array();
	foreach ($clusterCodeArr as $cc) {
		if(array_key_exists($cc, $ccArr) == 1)
			$ttArr[$cc] = array("topicCode" => $ccArr[$cc]['topicCode'], "topicDesc" => $ccArr[$cc]['topicDesc']);
	}
	// printArr("ttArr=" . sizeof($ttArr));

	return $ttArr;
}

function getAccuracyAndUsageForClusters($clusterCodeArr, $studentsArr, $class, $startDate, $endDate, $numOfStudents) {
	$clusterUsageArr = array();
	$ttCode = isset($_POST['topic'])?$_POST['topic']:"";

	if(!empty($ttCode))
		$query_accuracy = "SELECT a.clusterCode, COUNT(a.srno) AS total, SUM(IF(a.R = 1, 1, 0)) as correct, COUNT(DISTINCT a.userID) as userCount, a.teacherTopicCode AS ttCode 
	    FROM ".TBL_QUES_ATTEMPT."_class$class a 
	    WHERE a.clusterCode IN ( ". implodeArrayForQuery($clusterCodeArr) ." ) AND a.teacherTopicCode IN ('". $ttCode ."')
			AND a.userID IN ( ". implodeArrayForQuery($studentsArr) ." ) 
			GROUP BY a.clusterCode";
	else
		$query_accuracy = "SELECT a.clusterCode, COUNT(a.srno) AS total, SUM(IF(a.R = 1, 1, 0)) as correct, COUNT(DISTINCT a.userID) as userCount, a.teacherTopicCode AS ttCode 
	    FROM ".TBL_QUES_ATTEMPT."_class$class a 
	    WHERE a.clusterCode IN ( ". implodeArrayForQuery($clusterCodeArr) ." ) 
			AND a.userID IN ( ". implodeArrayForQuery($studentsArr) ." ) 
			GROUP BY a.clusterCode";
		 		
	$result_accuracy = mysql_query($query_accuracy) or die(mysql_errno());
	while($line=mysql_fetch_assoc($result_accuracy)) {
		$clusterUsageArr[$line['clusterCode']]['attempted'] = $line['total'];
		$clusterUsageArr[$line['clusterCode']]['correct'] = $line['correct'];
		$clusterUsageArr[$line['clusterCode']]['accuracy'] = round($line['correct']*100/$line['total'], 2);
		$clusterUsageArr[$line['clusterCode']]['userCount'] = $line['userCount'];
		$clusterUsageArr[$line['clusterCode']]['ttCode'] = $line['ttCode'];		
	
		if(isEnoughUsage($line['userCount'], $numOfStudents)) {
			$clusterUsageArr[$line['clusterCode']]['category'] = categorizeAccuracy($clusterUsageArr[$line['clusterCode']]['accuracy']);
		} else {
			$clusterUsageArr[$line['clusterCode']]['category'] = 'notEnoughUsage';
		}
	}

	$query_cluster = "SELECT a.cluster, a.clusterCode,b.teacherTopicCode,c.teacherTopicDesc FROM ". TBL_CLUSTER_MASTER . " a,adepts_teacherTopicClusterMaster b,adepts_teacherTopicMaster c WHERE a.clusterCode IN (" . implodeArrayForQuery($clusterCodeArr) . ") AND a.clusterCode=b.clusterCode AND b.teacherTopicCode=c.teacherTopicCode";

	$result_cluster = mysql_query($query_cluster) or die("error query: $query_cluster");

	while($line=mysql_fetch_assoc($result_cluster)) {
		$clusterUsageArr[$line['clusterCode']]['name'] = $line['cluster'];
		$clusterUsageArr[$line['clusterCode']]['topicName'] = $line['teacherTopicDesc'];
		$clusterUsageArr[$line['clusterCode']]['topicCode'] = $line['teacherTopicCode'];
	}

	return $clusterUsageArr;
}

function getAccuracyAndUsageForClusters2($ctArr, $studentsArr, $class, $startDate, $endDate, $numOfStudents) {

	$clusterCodeArr = array_keys($ctArr);
	$clusterUsageArr = $ttArr = array();


	$query_accuracy = "SELECT clusterCode, SUM(IFNULL(noOfQuesAttempted,0)) total, ROUND(SUM(IFNULL(perCorrect,0))/COUNT(*),2) perCorr, COUNT(DISTINCT userID) userCount
					FROM adepts_teacherTopicClusterStatus a
					WHERE a.clusterCode IN ( ". implodeArrayForQuery($clusterCodeArr) ." ) AND a.userID IN ( ". implodeArrayForQuery($studentsArr) ." ) 
					GROUP BY a.clusterCode";					
	/*$query_accuracy = "SELECT a.clusterCode, COUNT(a.srno) AS total, SUM(IF(a.R = 1, 1, 0)) as correct, COUNT(DISTINCT a.userID) as userCount, a.teacherTopicCode AS ttCode 
    FROM ".TBL_QUES_ATTEMPT."_class$class a INNER JOIN ".TBL_SESSION_STATUS." b ON a.sessionID = b.sessionID
    WHERE a.clusterCode IN ( ". implodeArrayForQuery($clusterCodeArr) ." ) 
		AND a.userID IN ( ". implodeArrayForQuery($studentsArr) ." ) 
		GROUP BY a.clusterCode";*/
	// printArr($query_accuracy);

	$result_accuracy = mysql_query($query_accuracy) or die(mysql_errno());


	while($line=mysql_fetch_assoc($result_accuracy)) {
		$clusterUsageArr[$line['clusterCode']]['attempted'] = $line['total'];
		$clusterUsageArr[$line['clusterCode']]['correct'] = round($line['perCorr']*$line['total']/100);
		$clusterUsageArr[$line['clusterCode']]['accuracy'] = $line['perCorr'];
		$clusterUsageArr[$line['clusterCode']]['userCount'] = $line['userCount'];
		$clusterUsageArr[$line['clusterCode']]['ttCode'] = $ctArr[$line['clusterCode']];		
		if(isEnoughUsage($line['userCount'], $numOfStudents)) {
			$clusterUsageArr[$line['clusterCode']]['category'] = categorizeAccuracy($clusterUsageArr[$line['clusterCode']]['accuracy']);
		} else {
			$clusterUsageArr[$line['clusterCode']]['category'] = 'notEnoughUsage';
		}
		$ttArr[$line['clusterCode']] = $ctArr[$line['clusterCode']];
	}
	// $ttArr = array_unique(array_values($ctArr));

	if(!empty($clusterUsageArr)){			
		$ttDescArr = getTopicDescFromTTCodes(array_unique(array_values($ttArr)));
		$query_cluster = "SELECT a.cluster, a.clusterCode FROM ". TBL_CLUSTER_MASTER . " a WHERE a.clusterCode IN (" . implodeArrayForQuery(array_keys($clusterUsageArr)) . ")";

		$result_cluster = mysql_query($query_cluster) or die("error query: $query_cluster");

		while($line=mysql_fetch_assoc($result_cluster)) {
			$clusterUsageArr[$line['clusterCode']]['name'] = $line['cluster'];
			$clusterUsageArr[$line['clusterCode']]['topicName'] = $ttDescArr[$ttArr[$line['clusterCode']]];
			$clusterUsageArr[$line['clusterCode']]['topicCode'] = $ttArr[$line['clusterCode']];
		}
	}		
	return $clusterUsageArr;
}

function categorizeAccuracy($accuracy) {
	$category = "";
	if($accuracy>=0 && $accuracy < 40) {
		$category = 'low';
	} elseif ($accuracy>=40 && $accuracy<=80) {
		$category = 'good';
	} elseif ($accuracy>80 && $accuracy<=100) {
		$category = 'great';
	} else {
		$category = 'notEnoughUsage';
	}
	return $category;
}

function implodeArrayForQuery($arr) {
	$str = "'" . implode("','", $arr) . "'";
	return $str;
}

#-------------------------------Overall Usage--------------------------------------------------------------------------
function getUsageAndAccuracyForStudent($studentId, $schoolCode, $class, $section, $startDate, $endDate) {
	
	$overallUsageSummary = array();
	$studentsArr = array($studentId);
	$timeSpentArr=array();
	$timeSpentArr=getTotalAndAvgTimeSpent($studentId, $startDate, $endDate, $class);
	//print_r($timeSpentArr);
	$timeSpentInhours= $timeSpentArr['totalTimeSpent'];
	$avgtimeSpent = $timeSpentArr['avgTimeSpent'];
	$timeSpent=$timeSpentArr['totalTimeSpentInSec'];

	$accuracy=getAccuracyForStudent($studentId, $class, $startDate, $endDate);
	
	$overallUsageSummary['timeSpent']=$timeSpentInhours;

	$overallUsageSummary['avgtimeSpent']=$avgtimeSpent;
	$overallUsageSummary['usage'] = categorizeUsage($timeSpent, $startDate, $endDate);
	$overallUsageSummary['accuracy'] = $accuracy;
	$overallUsageSummary['accuracyusage']=categorizeAccuracy($accuracy);

	//$usageArr = getUsageArrayForSection($schoolCode, $class, $section, $startDate, $endDate);
	//...	
	//print_r($overallUsageSummary);

	return $overallUsageSummary;

}

function getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate) {
	$overallUsageSummary = array();
	$studentsArr = getStudentDetailsBySection($schoolCode, $class, $section);
	$usageArr = getUsageArrayForSection($schoolCode, $class, $section, $startDate, $endDate);
	uasort($usageArr, "sortByUsageHelper");
	$overallUsageSummary['usageSummaryGraphDetails'] = getUsageSummaryForSection($usageArr, $startDate, $endDate);
	$overallUsageSummary['lowUsageNames'] = getLowUsageNames($usageArr);
	$overallUsageSummary['lowAccuracyNames'] = getLowAccuracyNames($usageArr);
	$overallUsageSummary['allTopicsCompletedNames'] = isShowAllTopicsCompletedNames($endDate) ? getAllTopicsCompletedNames(array_column($studentsArr, 'userID'), $schoolCode, $class, $section) : array();

	$overallUsageSummary['numerousAttemptsFailureNames'] = getNumerousAttemptsFailedStudentNames(array_column($studentsArr, 'userID'), $startDate, $endDate);
	$overallUsageSummary['zeroUsageNames'] = getZeroUsageNames($usageArr);
	return $overallUsageSummary;
}

function sortByUsageHelper($a, $b) {
	if($a['usage'] < $b['usage']) {
		return 1;
	} elseif($a['usage'] > $b['usage']) {
		return -1;
	}
	return 0;
}

function getZeroUsageNames($usageArr) {
	$zeroUsageNames = array();
	foreach ($usageArr as $usageDetails) {
		if($usageDetails['timeSpent'] == 0) {
			$zeroUsageNames[] = $usageDetails['name'];
		}
	}
	return $zeroUsageNames;
}

function getUsageArrayForSection($schoolCode, $class, $section, $startDate, $endDate) {
	$usageArr = array();
	$userArray = getStudentDetailsBySection($schoolCode, $class, $section);
	foreach ($userArray as $userDetails) {
		$timeSpent = getTimeSpent($userDetails['userID'], $startDate, $endDate, $class);
		$userDetails['timeSpent'] = $timeSpent;
		$userDetails['usage'] = categorizeUsage($timeSpent, $startDate, $endDate);
		$userDetails['accuracy'] = getAccuracyForStudent($userDetails['userID'], $class, $startDate, $endDate);
		$usageArr[] = $userDetails;
	}

	return $usageArr;
}

function isShowAllTopicsCompletedNames($endDate) {
	$nowDate = date("Y-m-d");
	if(strtotime($endDate) >= strtotime("-7 days"))
		return TRUE;
	else
		return FALSE;
}

function getNumerousAttemptsFailedStudentNames($studentsArr, $startDate, $endDate) {
	$failedStudentsArr = array();
	$query = "SELECT a.userID FROM ".TBL_TT_CLUSTER_STATUS." a inner join ".TBL_SESSION_STATUS." b on a.endSessionID = b.sessionID 
		WHERE a.result = 'FAILURE' AND a.clusterAttemptNo = 4 AND b.startTime_int >= ".getIntDate($startDate)." AND b.startTime_int <= ".getIntDate($endDate)." 
		AND a.userID IN (". implodeArrayForQuery($studentsArr) . ")";

	$result = mysql_query($query) or die(mysql_errno());

	while($line=mysql_fetch_array($result)) {
			 $failedStudentsArr[] = $line['userID'];	
	}
	$failedStudentNames = getNamesFromUserIDs($failedStudentsArr);
	return array_values($failedStudentNames);
}

function getAllTopicsCompletedNames($studentsArr, $schoolCode, $class, $section) {
	$completedStudentsArr = getAllActiveTopicsCompletedStudents($studentsArr, $schoolCode, $class, $section);
	return array_values($completedStudentsArr);
}
function getTotalTopicsAttempted($studentId,$class,$startDate,$endDate)
{
	//$noofTopicAttempted=0;
	$topicarr=array();
	$query="SELECT group_concat(DISTINCT(a.teacherTopicCode)) as tt,count(DISTINCT(a.teacherTopicCode)) as total FROM adepts_teacherTopicQuesAttempt_class".$class." as a 
	where a.attemptedDate >= '".$startDate."' and a.attemptedDate <= '".$endDate."' and a.userID=".$studentId." group by a.userID";
	//echo $query;
	$result = mysql_query($query) or die(mysql_error());

	while($line=mysql_fetch_array($result)){
	//$noofTopicAttempted =$line['total'];
	$topicarr['total']=$line['total'];
	$topicarr['tt']=$line['tt'];
	}
	//print_r($topicarr);
	return $topicarr; 
}
function getLowUsageNames($usageArr) {
	$lowUsageNames = array();
	usort($usageArr, "cmp_timeSpent");
	foreach ($usageArr as $userDetails) {
		if(strcmp($userDetails['usage'], 'low') ==0) {
			$lowUsageNames[] = $userDetails['name'];
		}
	}
	return $lowUsageNames;
}

function getLowAccuracyNames($usageArr) {
	$lowAccuracyNames = array();
	usort($usageArr, "cmp_accuracy");
	foreach ($usageArr as $userDetails) {
		if($userDetails['timeSpent']>0 && $userDetails['accuracy']<20) {
			$lowAccuracyNames[] = $userDetails['name'];
		}
	}
	return $lowAccuracyNames;
}

function getUsageSummaryForSection($usageArr, $startDate, $endDate) {	
	$cumulativeTime = 0;	
	foreach ($usageArr as $userDetails) {
		$cumulativeTime += $userDetails['timeSpent']; 
		switch ($userDetails['usage']) {
			case 'zero':
			$usageSummary['zero']++;				
			break;
			case 'low':
			$usageSummary['low']++;
			break;
			case 'average':
			$usageSummary['average']++;				
			break;
			case 'good':
			$usageSummary['good']++;
			break;
			case 'great':
			$usageSummary['great']++;				
			break;				
			default:
			break;
		}
	}

	$classAvg = $cumulativeTime/sizeof($usageArr);
	$classAvgCategory = categorizeUsage($classAvg, $startDate, $endDate);
	$usageSummary['classAvg'] = $classAvgCategory;

	return $usageSummary;	
}

function categorizeUsage($timeSpentInSecs, $startDate, $endDate) {
	$category = '';

	$datediff = strtotime($endDate) - strtotime($startDate);
	//echo strtotime($endDate).'-'.strtotime($startDate);
	//echo "DateDiff".$datediff."TimeSpentInsec".$timeSpentInSecs;
	$numDays = floor($datediff/(60*60*24)) + 1;
	//echo "\nDays NNo.".$numDays;
	$avgTimeSpentPerDay = $timeSpentInSecs/$numDays;

	if($avgTimeSpentPerDay == 0) {
		$category = 'zero';
	} elseif($avgTimeSpentPerDay < 257) {
		$category = 'low';
	} elseif ($avgTimeSpentPerDay>=257 && $avgTimeSpentPerDay<514) {
		$category = 'average';
	} elseif ($avgTimeSpentPerDay>=514 && $avgTimeSpentPerDay<1028) {
		$category = 'good';
	} elseif ($avgTimeSpentPerDay>=1028) {
		$category = 'great';
	} 
	return $category;
}

function getAccuracyForStudent($userID, $class, $startDate, $endDate) {
	$query = "SELECT count(srno) AS total, sum(if(R=1,1,0)) as correct, round(sum(s)/60) timeSpent, group_concat(distinct teacherTopicCode) as ttString
		FROM ".TBL_QUES_ATTEMPT."_class$class B 
		WHERE B.userID=$userID AND attemptedDate >= ".getIntDate($startDate)." AND attemptedDate <= ".getIntDate($endDate);
	//echo $query;
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$attempted = $row['total'];
	$correct = $row['correct'];
	$accuracy = ($attempted > 0)? round($correct*100/$attempted, 1) : 0;
	return $accuracy;	
}

function getActiveTopicsForSection($schoolCode, $class, $section, $tillDate="0000-00-00") {

	$activeTopicsArr = array();

	$query = "SELECT teacherTopicCode FROM ".TBL_TT_ACTIVATION." WHERE schoolCode=$schoolCode AND class=$class AND deactivationDate <= '$tillDate' ";
	if($section != "") {
		$query .= " AND section='$section' ";
	}
	
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $activeTopicsArr[] = $line['teacherTopicCode'];
	}
	return $activeTopicsArr;
}

function getAllActiveTopicsCompletedStudents($studentsArr, $schoolCode, $class, $section) {
	$completedStudentsArr = array();
	$activeTopicsArr = getActiveTopicsForSection($schoolCode, $class, $section);
	$activeTopicsCount = sizeof($activeTopicsArr);
	$query = "SELECT userID, count(teacherTopicCode) AS completed FROM ".TBL_TOPIC_STATUS." WHERE userID IN ( ". implodeArrayForQuery($studentsArr) ." ) AND teacherTopicCode IN ( ". implodeArrayForQuery($activeTopicsArr) ." ) AND (result='SUCCESS' or classLevelResult='SUCCESS') GROUP BY userID";
	$result = mysql_query($query) or die(mysql_errno());

	while($line=mysql_fetch_array($result)) {
		 if($line['completed'] == $activeTopicsCount) {
			 $completedStudentsArr[] = $line['userID'];	
		 }
	}
	$completedStudentNames = getNamesFromUserIDs($completedStudentsArr);
	return $completedStudentNames;
}

function getNamesFromUserIDs($userArr) {
	$nameArr = array();
	$query = "SELECT childName, userID FROM ".TBL_USER_DETAILS." WHERE userID IN (". implodeArrayForQuery($userArr) . ")";
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $nameArr[$line['userID']] = $line['childName'];
	}
	return $nameArr;	
}

function getStudentDetailsBySection($schoolCode, $class, $section) {
	$userArray = array();
	$tbl_userDetails = TBL_USER_DETAILS;

	$query = "SELECT userID, childName,username FROM $tbl_userDetails WHERE schoolCode = '$schoolCode' AND childClass = '$class' "; 
	
	if($section != "") {
		$sectionsArray = explode(",",$section);
		if(count($sectionsArray)>1){
		$section = str_replace(",", "','", $section);	
			$query .= " AND childSection IN('$section') ";
		}
		else{
			$query .= " AND childSection = '$sectionsArray[0]' ";			
		}
	}

	$query .= " AND enabled = 1 AND endDate >= curdate() AND subjects like '%". SUBJECTNO ."%' AND category = 'STUDENT' ORDER BY childName";
	$result = mysql_query($query) or die (mysql_error());

	while($line=mysql_fetch_array($result))
	{
		$userDetails = array();
		$userDetails['userID'] = $line['userID'];
		$userDetails['name'] = $line['childName'];
		$userDetails['username']=$line['username'];
			//Add more details if required			

		$userArray[] = $userDetails;
	}
	return $userArray;
}

function getTotalAndAvgTimeSpent($userID,$startDate,$endDate,$class)
{
	$tbl_sessionStatus = TBL_SESSION_STATUS;
	$tbl_quesAttempt = TBL_QUES_ATTEMPT;
	$timeusagearr=array();
	$datediff = strtotime($endDate) - strtotime($startDate);
    $days= floor($datediff/(60*60*24));
   
    if($days==0)
    {
    	$noofdays='';
    }
    else
    	$noofdays='/'.$days;
    
	$query = "SELECT DISTINCT sessionID, startTime, endTime, tmLastQues,SEC_TO_TIME(sum(TIMESTAMPDIFF(SECOND,startTime,endTime)))  as total,SEC_TO_TIME(sum(TIMESTAMPDIFF(SECOND,startTime,endTime))$noofdays) as avg, sum(TIMESTAMPDIFF(SECOND,startTime,endTime)$noofdays) as avgsec,
	sum(TIMESTAMPDIFF(SECOND,startTime,endTime)) as totalsec
	FROM $tbl_sessionStatus WHERE userID=$userID AND startTime_int >= ".getIntDate($startDate)." AND startTime_int <= ".getIntDate($endDate).""; 
	//echo $query;
	$time_result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($time_result))
	{
		$timeusagearr['totalTimeSpent']=$line['total'];
		$timeusagearr['avgTimeSpent']=$line['avg'];
		$timeusagearr['avgTimeinSec']=$line['avgsec'];
		$timeusagearr['totalTimeSpentInSec']=$line['totalsec'];
	}
	//print_r($timeusagearr);
	return $timeusagearr;

}
function getTimeSpent($userID, $startDate, $endDate, $class)
{

	//TODO : use adepts_homeSchoolUsage (filled by cron) - cannot be used for today but can be used for month and custom range

	$tbl_sessionStatus = TBL_SESSION_STATUS;
	$tbl_quesAttempt = TBL_QUES_ATTEMPT;

	$query = "SELECT DISTINCT sessionID, startTime, endTime, tmLastQues FROM $tbl_sessionStatus WHERE userID=$userID AND startTime_int >= ".getIntDate($startDate)." AND startTime_int <= ".getIntDate($endDate).""; 
	
	$time_result = mysql_query($query) or die(mysql_error());
	$timeSpent = 0;
	while ($time_line = mysql_fetch_array($time_result))
	{
		$startTime = convertToTime($time_line[1]);
		if($time_line[2]!="")
			$endTime = convertToTime($time_line[2]);
		elseif ($time_line[3]!="")
			$endTime = convertToTime($time_line[3]);
		else
		{
			$query = "SELECT max(lastModified) FROM ".TBL_QUES_ATTEMPT."_class$class WHERE sessionID=".$time_line[0]." AND userID=".$userID;
			$r     = mysql_query($query);
			$l     = mysql_fetch_array($r);
			if($l[0]=="")
				continue;
			else
				$endTime = convertToTime($l[0]);
		}
		//echo "Start Time".$startTime. " and ".$endTime; 
        $timeSpent = $timeSpent + ($endTime - $startTime);    //in secs
  }
  
 

  return $timeSpent;
}

function formatToHMS($timeInSeconds) {
	$hours = str_pad(intval($timeInSeconds/3600),2,"0",STR_PAD_LEFT);    //converting secs to hours.
	$timeInSeconds = $timeInSeconds%3600;
	$mins  = str_pad(intval($timeInSeconds/60),2,"0", STR_PAD_LEFT);
	$timeInSeconds = $timeInSeconds%60;
	$secs  = str_pad($timeInSeconds,2,"0",STR_PAD_LEFT);
	return $hours.":".$mins.":".$secs;
}

function convertToTime($date)
{
	$hr   = substr($date,11,2);
	$mm   = substr($date,14,2);
	$ss   = substr($date,17,2);
	$day  = substr($date,8,2);
	$mnth = substr($date,5,2);
	$yr   = substr($date,0,4);
	$time = mktime($hr,$mm,$ss,$mnth,$day,$yr);
	return $time;
}

/**
* Sort by timeSpent function for usort 
* */
function cmp_timeSpent($a, $b) {
	if($a['timeSpent'] == $b['timeSpent']) {		
		return 0;
	}
	return ($a['timeSpent'] < $b['timeSpent'])? -1 : 1;
}

/**
* Sort by accuracy function for usort 
* */
function cmp_accuracy($a, $b) {
	if($a['accuracy'] == $b['accuracy']) {		
		return 0;
	}
	return ($a['accuracy'] < $b['accuracy'])? -1 : 1;
}

################################ TOPIC REPORT ############################################################
//TODO major refactor
function getTopicReport($schoolCode, $class, $section, $topicArr, $mode=0, $startDate="", $endDate="") {
	$topicReport = array();
	$studentDetailsArray = getStudentDetailsBySection($schoolCode, $class, $section);
	$studentsArr = array_column($studentDetailsArray, 'userID');
	$clusterCodeArr = array();
	
	if($mode==1) {
		//We passed cluster codes instead of topicCodes in topicArr
		$clusterCodeArr = $topicArr;

	} else {
		$topicClusterArr = getClustersForTopics($studentsArr, $topicArr, $class);		
		$clusterCodeArr = array_keys($topicClusterArr);
		$flow = getFlowForTopic($schoolCode, $class, $section, $topicArr[0]);
	}

	$numOfStudents = sizeof($studentsArr);
	$clusterUsageArr = getAccuracyAndUsageForClusters($clusterCodeArr, $studentsArr, $class, "", "", $numOfStudents);
	// printArr($clusterUsageArr);
	$avgAccuracy = getAverageAccuracy($clusterUsageArr);	
	$avgAccuracyCategory = categorizeAccuracy($avgAccuracy);
	$clusterFlowArr = addFlowToClusterUsageArr($clusterCodeArr, $topicArr, $clusterUsageArr, $mode);
	// printArr($clusterFlowArr);
	uasort($clusterFlowArr, "sortHelper");

	$sno = 1;
	foreach ($clusterFlowArr as $clusterCode => $clusterDetails) {
			$topicReport[] = array(
				"flow" => $sno,
				"luName" => $clusterDetails['name'],
				"topic" => $clusterDetails['topicDesc'],
				"numAttempted" => $clusterDetails['userCount'] . " of " . $numOfStudents,
				"percentAttempted" => ($numOfStudents>0)? round($clusterDetails['userCount']*100/$numOfStudents)."%" : "0%",
				"accuracy" => (strcmp($clusterDetails['accuracy'], "--") == 0)? "Not enough usage": round($clusterDetails['accuracy'])."%"
				);
			$sno++;				
	}

	$retArr = array("avgAccuracy" => $avgAccuracy, "avgAccuracyCategory" => $avgAccuracyCategory, "topicReport" => $topicReport);
	if($mode == 0) {
		$retArr['flow'] = $flow;
	}
	// printArr(sizeof($retArr['topicReport']) . "::" . sizeof($clusterCodeArr));
	return $retArr;
}
	
//TODO refactor
function getTopicReport2($schoolCode, $class, $section, $startDate, $endDate) {
	$topicReport = array();
	unset($_POST['topic']);
	$studentDetailsArray = getStudentDetailsBySection($schoolCode, $class, $section);
	$studentsArr = array_column($studentDetailsArray, 'userID');
	$clusterCodeArr = array();

	$ctArr = getClustersAttemptedWithinDateRange($studentsArr, $startDate, $endDate, $class);
	$clusterCodeArr = array_keys($ctArr);	
	$numOfStudents = sizeof($studentsArr);
	$clusterUsageArr = getAccuracyAndUsageForClusters($clusterCodeArr, $studentsArr, $class, "", "", $numOfStudents);	
	$avgAccuracy = getAverageAccuracy($clusterUsageArr);	
	$avgAccuracyCategory = categorizeAccuracy($avgAccuracy);
	$clusterFlowArr = addFlowToClusterUsageArr($clusterCodeArr, array(), $clusterUsageArr, 1);
	// printArr($clusterFlowArr);
	uasort($clusterFlowArr, "sortHelper");

	$sno = 1;
	foreach ($clusterFlowArr as $clusterCode => $clusterDetails) {
			$topicReport[] = array(
				"flow" => $sno,
				"luName" => $clusterDetails['name'],
				"topic" => $clusterDetails['topicName'],
				"numAttempted" => $clusterDetails['userCount'] . " of " . $numOfStudents,
				"percentAttempted" => ($numOfStudents>0)? round($clusterDetails['userCount']*100/$numOfStudents)."%" : "0%",
				"accuracy" => (strcmp($clusterDetails['accuracy'], "--") == 0)? "Not enough usage": round($clusterDetails['accuracy'])."%"
				);
			$sno++;				
	}

	$retArr = array("avgAccuracy" => $avgAccuracy, "avgAccuracyCategory" => $avgAccuracyCategory, "topicReport" => $topicReport);

	return $retArr;
}


function getFlowForTopic($schoolCode, $class, $section, $ttCode) {
	$flow = "";
	$query = "SELECT flow FROM ".TBL_TT_ACTIVATION." WHERE schoolCode=$schoolCode AND class=$class AND teacherTopicCode='$ttCode'";	
	if($section != "") {
		$query.= " AND section='$section'";
	}
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		$flow = $line['flow'];
	}

	return $flow;
}

function sortHelper($a, $b) {
	if(strcmp($a['topicDesc'], $b['topicDesc']) == 0) {
		if($a['flowno'] < $b['flowno']) {
			return -1;
		} else {
			return 1;
		}
	} else if(strcmp($a['topicDesc'], $b['topicDesc']) < 0) {
		return -1;
	} else {
		return 1;
	}
}

function getActivatedAndDeactivatedTopicsForClass($schoolCode, $class, $section) {
	$ttCodeArr = array();
	$studentDetailsArray = getStudentDetailsBySection($schoolCode, $class, $section);
	$studentsArr = array_column($studentDetailsArray, 'userID');
	$query = "SELECT DISTINCT (a.teacherTopicCode), b.teacherTopicDesc FROM ".TBL_TOPIC_STATUS." a INNER JOIN ".TBL_TOPIC_MASTER." b on a.teacherTopicCode = b.teacherTopicCode WHERE a.userID IN (" . implodeArrayForQuery($studentsArr) . ") ";		
	$result = mysql_query($query) or die (mysql_error());
	while($line=mysql_fetch_array($result))
	{
		$ttCodeArr[$line['teacherTopicCode']] = $line['teacherTopicDesc'];
	}
	return $ttCodeArr;	
}

function getClustersForTopic($ttCode, $class, $flow) {
	$clusterCodeArr = array();
	$ttObj = new teacherTopic($ttCode, $class, $flow);
	foreach ($ttObj->getClustersOfLevel($ttObj->startingLevel) as $value) {
		$clusterCodeArr[] = $value;
	}	
	return $clusterCodeArr;
}

function getClustersForTopics($studentsArr, $ttCodeArr, $class) {
	$clusterCodeArr = array();
	$query = "SELECT DISTINCT (teacherTopicCode), flow FROM ".TBL_TOPIC_STATUS." WHERE userID IN (" . implodeArrayForQuery($studentsArr) . ") ";
	if(sizeof($ttCodeArr) > 0) {
		$query .= "AND teacherTopicCode IN (".implodeArrayForQuery($ttCodeArr).")";
	}	
	// echo $query;
	$topicNameArr = getTopicDescFromTTCodes($ttCodeArr);
	$result = mysql_query($query) or die (mysql_error());
	while($line=mysql_fetch_array($result))
	{
		$thisTopicClusters = getClustersForTopic($line['teacherTopicCode'], $class, $line['flow']);
		foreach($thisTopicClusters as $clusterCode) {
			$topicClusterArr[$clusterCode]['topicDesc'] = $topicNameArr[$line['teacherTopicCode']];
			$topicClusterArr[$clusterCode]['topicCode'] = $line['teacherTopicCode'];			
		}
		$clusterCodeArr = array_merge($clusterCodeArr, $topicClusterArr);
	}
	return $clusterCodeArr;	
}

function addFlowToClusterUsageArr($clusterCodeArr, $topicArr, $clusterUsageArr, $mode) {	
	$query = "SELECT clusterCode, flowno, teacherTopicDesc FROM ".TBL_TT_CLUSTER_MASTER." a INNER JOIN ".TBL_TOPIC_MASTER." b on a.teacherTopicCode = b.teacherTopicCode WHERE clusterCode IN (". implodeArrayForQuery($clusterCodeArr) . ")";

	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		$clusterUsageArr[$line['clusterCode']]['flowno'] = $line['flowno'];
		if($mode ==0) {
			$ttDescArr = getTopicDescFromTTCodes($topicArr);
			$ttDesc = $ttDescArr[$topicArr[0]];
			$clusterUsageArr[$line['clusterCode']]['topicDesc'] = $ttDesc;						
		} else {
			$clusterUsageArr[$line['clusterCode']]['topicDesc'] = $line['teacherTopicDesc'];			
		}

		if(!isset($clusterUsageArr[$line['clusterCode']]['userCount'])) {
			$clusterUsageArr[$line['clusterCode']]['userCount'] = 0;
			$clusterUsageArr[$line['clusterCode']]['attempted'] = 0;
			$clusterUsageArr[$line['clusterCode']]['correct'] = 0;
			$clusterUsageArr[$line['clusterCode']]['category'] = 'notEnoughUsage';
			$clusterUsageArr[$line['clusterCode']]['accuracy'] = '--';
		} else {
			if(strcmp($clusterUsageArr[$line['clusterCode']]['category'], 'notEnoughUsage') ==0) {
			$clusterUsageArr[$line['clusterCode']]['accuracy'] = '--';
			}
		}
	}
	return $clusterUsageArr;
}

function getTimedTestReport ($schoolCode, $class, $section, $ttCode, $ttDesc) {
	$topicReport = array();
	$studentDetailsArray = getStudentDetailsBySection($schoolCode, $class, $section);
	$studentsArr = array_column($studentDetailsArray, 'userID');
	$numOfStudents = sizeof($studentsArr);
	$topicClusterArr = getClustersForTopics($studentsArr, array($ttCode), $class);
	$clusterCodeArr = array_keys($topicClusterArr);
	$timedTestSummary = getTimedTestSummary($studentsArr, $clusterCodeArr);
	$sno = 1;
	foreach ($timedTestSummary as $timedTestCode => $details) {
		if(isset($details['attempted'])) {
			$topicReport[$timedTestCode]['sno'] = $sno;
			$sno++;
			$topicReport[$timedTestCode]['testName'] = '<a href="timedTestReport.php?childClass='.$class.'&childSection='.$section.'&timedtest='.$timedTestCode.'" target="_blank">'. $details['name'] . '</a>';
			$topicReport[$timedTestCode]['topic'] = $ttDesc;
			$topicReport[$timedTestCode]['numAttempted'] = $details['attempted'] . " of " . $numOfStudents;
			$topicReport[$timedTestCode]['percentAttempted'] = $details['percentAttempted'] . "%";
			$topicReport[$timedTestCode]['accuracy'] = $details['accuracy'] . "%";
		}
	}
	return $topicReport;
}

function getTimedTestSummary($studentsArr, $clusterCodeArr) {
//1. name 2. topic 3. number of children who attempted it 4. % of students 5. avg. accuracy
	$summaryArr = array();
	$timedTestArr = array();
	$numOfStudents = sizeof($studentsArr);

	$query1 = "SELECT timedTestCode, description, linkedToCluster FROM ".TBL_TIMED_TEST_MASTER." WHERE linkedToCluster IN (" . implodeArrayForQuery($clusterCodeArr) . ")";
	$result1 = mysql_query($query1) or die (mysql_error());
	while($line=mysql_fetch_array($result1))
	{
		$summaryArr[$line['timedTestCode']]['name'] = $line['description'];
		$timedTestArr[] = $line['timedTestCode'];
	}

	$query2 = "SELECT count(distinct userID) as attempted, avg(perCorrect) as accuracy, timedTestCode FROM ".TBL_TIMED_TEST_DETAILS." where userID in (". implodeArrayForQuery($studentsArr) .") AND timedTestCode IN (". implodeArrayForQuery($timedTestArr) . ") GROUP BY timedTestCode";

	$result2 = mysql_query($query2) or die (mysql_error());
	while($line=mysql_fetch_array($result2))
	{
		$summaryArr[$line['timedTestCode']]['attempted'] = $line['attempted'];
		$summaryArr[$line['timedTestCode']]['percentAttempted'] = round($line['attempted']*100/$numOfStudents);
		$summaryArr[$line['timedTestCode']]['accuracy'] = round($line['accuracy']);
	}

	return $summaryArr;
}

function getDailyPracticeReport($schoolCode, $class, $section, $ttCode, $startDate="", $endDate="")
{
	$dateCond = "";
	if($startDate!="" && $endDate!="")
	{
		$dateCond = " AND DATE(b.lastModified)>='".$startDate."' AND DATE(b.lastModified)<='".$endDate."'";
	}
	$dailyPracticeReport = array();
	$dailyPracticeAccuracyDataArray = array();
	$studentDetailsArray = getStudentDetailsBySection($schoolCode, $class, $section);
	$studentsArr = array_column($studentDetailsArray, 'userID');
	$numOfStudents = sizeof($studentsArr);
	$topicClusterArr = getClustersForTopics($studentsArr, array($ttCode), $class);
	$clusterCodeArr = array_keys($topicClusterArr);
	$sno = 1;

	$query = "SELECT  a.practiseModuleId AS practiceModuleId, a.description AS practiceModuleName, a.linkedToCluster as clusterCode, d.cluster as clusterName, GROUP_CONCAT(DISTINCT(b.userID)) as attemptedStudentIDs from practiseModuleDetails a, practiseModulesTestStatus b, adepts_userDetails c, adepts_clusterMaster d where a.practiseModuleId=b.practiseModuleId and b.userID=c.userID and a.linkedToCluster=d.clusterCode and a.linkedToCluster IN(".implodeArrayForQuery($clusterCodeArr).") and a.dailyDrill=1 and a.`status`='Approved' and c.category='STUDENT' and b.userID IN(".implodeArrayForQuery($studentsArr).")".$dateCond." GROUP BY a.practiseModuleId";
	$result = mysql_query($query) or die (mysql_error());
	$cnt = 1;
	$studentsAttemptedStr = "";
	$studentsCompletedStr = "";
	$accuracyStr = "";
	while($line=mysql_fetch_array($result))
	{
		$dailyPracticeReport[$line['practiceModuleId']]['srno'] = $cnt;
		$dailyPracticeReport[$line['practiceModuleId']]['pmName'] = $line['practiceModuleName'];
		$dailyPracticeReport[$line['practiceModuleId']]['luName'] = $line['clusterName'];

		$dailyPracticeAccuracyDataArray = getDailyPracticeAccuracyData($line['practiceModuleId'], $studentsArr, $startDate, $endDate);
		
		if(count(explode(",", $line["attemptedStudentIDs"])) > 0)
		{
			$studentsAttemptedStr = count(explode(",", $line["attemptedStudentIDs"]))." of ".count($studentsArr);
			if($dailyPracticeAccuracyDataArray['studentsCompleted'] > 0)
			{
				$studentsCompletedStr = $dailyPracticeAccuracyDataArray['studentsCompleted']." of ".count(explode(",", $line["attemptedStudentIDs"]));
				$accuracyStr = $dailyPracticeAccuracyDataArray['avgAccuracy']."%";
			}
			else
			{
				$studentsCompletedStr = "Not enough usage";
				$accuracyStr = "Not enough usage";
			}
		}
		else
		{
			$studentsAttemptedStr = "Not enough usage";
			$studentsCompletedStr = "Not enough usage";
			$accuracyStr = "Not enough usage";
		}
		$dailyPracticeReport[$line['practiceModuleId']]['studentsAttempted'] = $studentsAttemptedStr;
		$dailyPracticeReport[$line['practiceModuleId']]['studentsCompleted'] = $studentsCompletedStr;
		$dailyPracticeReport[$line['practiceModuleId']]['avgAccuracy'] = $accuracyStr;
		$dailyPracticeReport[$line['practiceModuleId']]['leastAccuracyStudents'] = $dailyPracticeAccuracyDataArray['leastAccuracyStudents'];
		$cnt++;
	}
	return $dailyPracticeReport;
}

function getDailyPracticeAccuracyData($practiceModuleId, $studentsArr, $startDate ="", $endDate="")
{
	$dateCond = "";
	if($startDate!="" && $endDate!="")
	{
		$dateCond = " AND DATE(b.lastModified)>='".$startDate."' AND DATE(b.lastModified)<='".$endDate."'";
	}
	$returnArr = array();
	$userAccuracyArray = array();
	$query = "SELECT pmStatusId, uId, childName, ((SUM(correctQ)/SUM(totalQ))*100) as accuracy FROM ((SELECT a.id as pmStatusId,a.userID as uId,c.childName as childName,SUM(IF(b.R=1,1,0)) as correctQ, COUNT(b.R) as totalQ FROM practiseModulesTestStatus a,practiseModulesQuestionAttemptDetails b, adepts_userDetails c where a.id=b.practiseModuleTestStatusId and a.userID=c.userID and a.status='completed' and c.category='STUDENT' and a.userID IN(".implodeArrayForQuery($studentsArr).")".$dateCond." and a.practiseModuleId='".$practiceModuleId."' GROUP BY a.id) UNION ALL (SELECT a.id as pmStatusId,a.userID as uId,d.childName as childName,SUM(IF(c.result=1,1,0)) as correctQ, COUNT(c.result) as totalQ FROM practiseModulesTestStatus a,practiseModulesTimedTestAttempt b, adepts_timedTestQuesAttempt c, adepts_userDetails d where a.id=b.practiseModuleTestStatusId and b.timedTestAttemptId=c.timedTestID and a.userID=d.userID and a.status='completed' and d.category='STUDENT' and a.userID IN(".implodeArrayForQuery($studentsArr).")".$dateCond." and a.practiseModuleId='".$practiceModuleId."' GROUP BY a.id)) e1 GROUP BY pmStatusId order by uId desc,accuracy desc";
	$result = mysql_query($query) or die(mysql_error());
	while($line = mysql_fetch_array($result))
	{
		if(!array_key_exists($line['uId'], $userAccuracyArray))
		{
			$userAccuracyArray[$line['uId']]['practiceModuleStatusId'] = $line['pmStatusId'];
			$userAccuracyArray[$line['uId']]['childName'] = $line['childName'];
			$userAccuracyArray[$line['uId']]['avgAccuracy'] = $line['accuracy'];
		}
	}
	uasort($userAccuracyArray, 'sortStudentsBasedOnAvgAccuracy');
	$cnt = 0;
	$totAcc = 0;
	$returnArr['leastAccuracyStudents'] = "";
	foreach ($userAccuracyArray as $userId => $accData) 
	{
		$totAcc += $accData['avgAccuracy'];
		if(count(explode(",", $returnArr['leastAccuracyStudents'])) <= 5 && floatval($accData['avgAccuracy']) < 70)
		{
			$returnArr['leastAccuracyStudents'] .= "<a href='studentTrail.php?practice_passed_id=".$accData['practiceModuleStatusId']."&user_passed_id=".$userId."' target='_blank'>".$accData['childName']."</a>, ";
		}
		$cnt++;
	}	
	if($returnArr['leastAccuracyStudents']!="")
		$returnArr['leastAccuracyStudents'] = rtrim($returnArr['leastAccuracyStudents'],", ");
	else 
		$returnArr['leastAccuracyStudents'] = "None";		
	$returnArr['studentsCompleted'] = $cnt;
	$returnArr['avgAccuracy'] = $cnt>0?round($totAcc/$cnt):"0";
	return $returnArr;
}

function sortStudentsBasedOnAvgAccuracy($a, $b)
{
	if ( $a['avgAccuracy'] == $b['avgAccuracy'] )
        return 0;
    if ( $a['avgAccuracy'] < $b['avgAccuracy'] )
         return -1;
    return 1;
}

function getMisconceptionsReport($schoolCode, $class, $section, $ttCodeArr) {
	$studentDetailsArray = getStudentDetailsBySection($schoolCode, $class, $section);
	$studentsArr = array_column($studentDetailsArray, 'userID');
	$misconceptionArr = getMisconceptionsDetected($class, $studentsArr, $ttCodeArr);
	return $misconceptionArr;
}

function getMisconceptionsDetected($class, $studentsArr, $ttCodeArr) {
	$misconceptionArr = array();
	$query = "SELECT misconception, count(misconception) as count 
		FROM ".TBL_QUESTIONS." a, ".TBL_QUES_ATTEMPT."_class$class b 
		WHERE a.qcode=b.qcode AND R=0 AND teacherTopicCode IN (".implodeArrayForQuery($ttCodeArr).") 
		AND userID IN (". implodeArrayForQuery($studentsArr).") 
		AND misconception<>'' AND !isNull(misconception) GROUP BY misconception ORDER BY count DESC LIMIT 2";

	$result = mysql_query($query) or die (mysql_error());
	while($line=mysql_fetch_array($result))
	{
		$a = explode(",", $line['misconception']);
		$misconceptionArr[$a[0]]['count'] = $line['count'];
	}

	$query2 = "SELECT id, description FROM ".TBL_MISCONCEPTIONS_MASTER." WHERE id IN (".implodeArrayForQuery(array_keys($misconceptionArr)).")";
	$result2 = mysql_query($query2) or die (mysql_error());
	while($line=mysql_fetch_array($result2)) 
	{
		$misconceptionArr[$line['id']]['description'] = $line['description'];
	}

	return $misconceptionArr;
}

function getIntDate($date) {
	return str_replace('-', '', $date);
}



function printArr($obj) {
	if(TEST == 1) {
		echo "<pre>";
		print_r($obj);		
	}
}

function usort_callback($a, $b)
{
	if ( $a['accuracy'] == $b['accuracy'] )
		return 0;

	return ( $a['accuracy'] > $b['accuracy'] ) ? -1 : 1;
}
function usort_callbac1($a, $b)
{
	if ( $a['accuracy'] == $b['accuracy'] )
		return 0;

	return ( $a['accuracy'] > $b['accuracy'] ) ? 1 : -1;
}
function getAccuracyUsageSectiowise($schoolCode,$startDate, $endDate)
{
	
	$accuracyUsageArray = $accuracyUsageSectionWise = array();
	$schoolStudentsCounts = getSchoolStudentsCounts($schoolCode);	
	$query = "SELECT a.class,a.section,ROUND(SUM(IFNULL(a.perCorrect,0))/COUNT(a.id),2) perCorrAverage,ROUND(SUM(IFNULL(a.totalTimeSpentInt,0)),2) totalTimeSpent,COUNT(a.id) as total,a.schoolCode FROM ".TBL_SECTIONWISE_DAILY_DETAILS." a where a.schoolCode = $schoolCode and a.dateInt<=".getIntDate($endDate)." and a.dateInt>=".getIntDate($startDate)."  group by a.class,a.section ORDER BY a.class,a.section";
	
	$result = mysql_query($query) or die (mysql_error());
	$i = 0;
	while($line=mysql_fetch_array($result)) 
	{				
		$accuracyUsageArray[$i] = $line;		 
		$i++;
	}
	foreach($accuracyUsageArray as $key1 => $accuracyUsage)
	{
		foreach ($schoolStudentsCounts as $key2 => $value) {		
			if((strcmp($value['childClass'],trim($accuracyUsage['class'])) == 0) && (strcmp($value['childSection'],trim($accuracyUsage['section'])) == 0)) 
			{								
			 $accuracyUsageSectionWise[$key1]['classsection'] =$value['childSection'] != '' ? $value['childClass'].'-'.$value['childSection'] : $value['childClass'];		

			 $accuracyUsageSectionWise[$key1]['usage'] = ROUND($accuracyUsage['totalTimeSpent']/$value['totalStudents'],2);
			 $accuracyUsageSectionWise[$key1]['accuracy'] = $accuracyUsage['perCorrAverage'];			
			}
		}		
	}	
	return $accuracyUsageSectionWise;
}

function categorizeUsageSectionwise($timeSpentInSecs) {
	$category = '';	
	if($timeSpentInSecs == 0) {
		$category = 'zero';
	} elseif($timeSpentInSecs < 1800) {
		$category = 'low';
	} elseif ($timeSpentInSecs>=1800 && $timeSpentInSecs<3600) {
		$category = 'average';
	} elseif ($timeSpentInSecs>=3600 && $timeSpentInSecs<7200) {
		$category = 'good';
	} elseif ($timeSpentInSecs>=7200) {
		$category = 'great';
	} 
	return $category;
}

function getSchoolStudentsCounts($schoolCode){
	$userArray = array();
	$tbl_userDetails = TBL_USER_DETAILS;

	$query = "SELECT childClass,childSection,COUNT(userID) as totalStudents FROM $tbl_userDetails WHERE schoolCode = '$schoolCode'";
	$query .= " AND enabled = 1 AND endDate >= curdate() AND subjects like '%". SUBJECTNO ."%' AND category = 'STUDENT' GROUP BY childClass,childSection ORDER BY childClass,childSection";		
	$result = mysql_query($query) or die (mysql_error());	
	while($line=mysql_fetch_array($result))
	{
		$userDetails = array();		
		$userDetails['childClass']= trim($line['childClass']);
		$userDetails['childSection']=trim($line['childSection']);
		$userDetails['totalStudents']=$line['totalStudents'];
			//Add more details if required			

		$userArray[] = $userDetails;
	}
	return $userArray;
}
################################ TEST ####################################################################
//kindha

if(TEST == 1) {
		$link = mysql_connect("192.168.0.15","ms_analysis","sl@vedb@e!")  or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");
	mysql_select_db ("educatio_adepts")  or die ("Could not select database".mysql_error());
	putenv('TZ=IST-5:30');

	$schoolCode = "9582";
	$class = '4';
	$section = 'A';
	$startDate = '2015-05-25';
	$endDate = '2015-06-01';
	// $endDate = date ( "Y-m-d" );
	// $startDate=date ( "Y-m-d" ,strtotime("-30 day"));
	$topics = 'MEA011,MEA027,MEA022,MEA019,MEA014,MEA029,WNO105,WNO012,WNO016,WNO013';
	// $topicList = "TT020,TT115,TT014,TT097,TT017,TT018,TT016,TT015,TT021,TT007,TT009,TT083,TT005,TT100,TT010,TT012,TT006,TT104,TT002,TT004,TT008,TT106,TT110,TT113,TT013,TT105";
	// $topicArr = explode(",", $topicList);
	$topicArr = explode(",", $topics);
	$startDate = '2015-05-08';
	$endDate = '2015-05-15';
	// $topics = 'GEO002,DEC003,DEC004,DEC067,RAP032,GEO010,MEA006,MEA004,REA018,WNO012,GEO016,MEA033,MEA034,REA003,REA004,REA023,REA017,REA022,REA001,DEC062,DEC014,DEC066';
	// $topicArr = explode(",", $topics);
	// $topicList = 'TT18968';
	// $topicList = "GEO020,WNC039,SHA004,SHA006,GEO038,NTH002,GEO032,GEO057,NTH011";
	// $topicList = "GEO020,WNC039,SHA004,SHA006,GEO038,ADG039,GEO019,GEO018,GEO056,ADG016,GEO037,SHA003,MEA004,WNO031,NTH001,MEA005,GEO008,NTH004,NTH002,GEO032,GEO057,NTH011,NTH006,NTH003,MEA044,MEA019,SHA005,MEA022,PSB002,PSB001,PSB008,MEA024,MEA038,MEA006,MEA039,PSB003";
	// $topicArr = explode(",", $topicList);


	// $result = getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate);
// 	$result = getImpactSummaryDetails($schoolCode, "8", "A,B,C,D", $startDate, $endDate);
	// echo '<pre>';
	// print_r($result);
	// exit;
	

//	schoolCode=2470230&cls=3&sec=A&topics=TT14464,TT16247&startDate=2014-12-1&endDate=2015-03-01
	// $arr = getOverallUsageSummary($schoolCode, $class, $section, $startDate, $endDate);
	// $arr = getOverallAccuracySummary($schoolCode, $class, $section, $startDate, $endDate);
	// $arr = getActivatedAndDeactivatedTopicsForClass($schoolCode, $class, $section);
	// $arr = getAreasHandledByMindsparkDetails($schoolCode, $class, $section, $startDate, $endDate);
	// $arr = getImpactSummaryDetails($schoolCode, $class, $section, $startDate, $endDate);
	$arr = getTopicProgressSummaryDetails($schoolCode, $class, $section, $startDate, $endDate);
	// $a = getActivatedAndDeactivatedTopicsForClass($schoolCode, $class, $section);
	// $arr = getMisconceptionsReport($schoolCode, $class, $section, $topicArr);
	// $arr = getTimedTestReport($schoolCode, $class, $section, 'TT16247', "description");
	// $arr = getTopicReport2($schoolCode, $class, $section, $startDate, $endDate);
	// $sectionStudentDetails= getStudentDetailsBySection($schoolCode,$class, $section);
	// $studentsArr = array_column($sectionStudentDetails, 'userID');
	// $clusterCodeArr = getClustersAttemptedBySection($studentsArr, $startDate, $endDate, $class);
	// $acu = getAccuracyAndUsageForClusters($clusterCodeArr, $studentsArr, $class, $startDate, $endDate, sizeof($studentsArr));
	
	printArr($arr);	
	
	
// 	usort($acu, 'usort_callback');
// 	usort($acu1, 'usort_callback');
// 	$finalArray = array_merge($acu,$acu1);
// 	usort($finalArray, 'usort_callback');
	
	// $top5 = array_slice($finalArray, 0, 5);
// 	$top51 = array_slice($acu1, 0, 5);
	// echo '<pre>';
	// print_r($acu);
// 	print_r($top51);
	
	// exit;
// 	$sectionStudentDetails1 = getStudentDetailsBySection($schoolCode,"8", "A,B,C,D");
// 	$n = array_merge($sectionStudentDetails , $sectionStudentDetails1);
	
	// $clusterCodeArr = getClustersAttemptedBySection($studentsArr, $startDate, $endDate, '8');
	// $acu = getAccuracyAndUsageForClusters($clusterCodeArr, $studentsArr, $class, $startDate, $endDate, sizeof($studentsArr));
// 	$arr = getOverallAccuracySummary($schoolCode, $class, $section, $startDate, $endDate);
// 	$arr = getOverallAccuracySummary($schoolCode, '8', 'A,B,C,D', $startDate, $endDate);
// 	$accArr = array_column($acu , 'accuracy');
// 	$result = getAccuracySummaryGraphDetails($acu);
	// $arr = getTopicReport($schoolCode, $class, $section, $topicArr, 1);
	// echo "<pre>";
	// echo json_encode($summary) ;
	// print_r($a);
	// print_r($sectionStudentDetails);
	// echo 'COunt is : '.count($acu);
	// echo json_encode($arr);
	// echo json_encode($arr);
	// echo "<br />";
	// print_r($accuracyArr);
	// print_r($accuracySummary);
	// $avg = getAverageProgressForActiveTopics(33850, $schoolCode, $class, $section);
	// echo "progress = $avg";	
}

?>
