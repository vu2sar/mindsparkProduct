<?php
$colorCounter = 0;
function getClassesAssigned($schoolCode, $category, $userID)
{
	$arrClassSection = array();
	if(strcasecmp($category,"School Admin")==0)
	{
		$query  = "SELECT   distinct  childClass as class, childSection as section
		           FROM     adepts_userDetails
		           WHERE    schoolCode=$schoolCode AND category='STUDENT' AND subcategory='School' AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND enddate>=curdate()
		           ORDER BY childClass, childSection ";
	}
	elseif (strcasecmp($category,"Teacher")==0)
	{
		 $query  = "SELECT distinct  class, section FROM adepts_teacherClassMapping WHERE userID = $userID AND subjectno=".SUBJECTNO." ORDER BY class, section";
	}
	elseif (strcasecmp($category,"Home Center Admin")==0)
	{
		$query  = "SELECT   distinct childClass as class,  childSection as section
		           FROM     adepts_userDetails
		           WHERE    category='STUDENT' AND subcategory='Home Center' AND schoolCode=$schoolCode AND enabled=1 AND subjects like '%".SUBJECTNO."%' AND enddate>=curdate()
		           ORDER BY childClass, childSection";
	}
	$result = mysql_query($query) or die("Error in fetching class details");
	$srno=0;
	while($line = mysql_fetch_array($result))
	{
		$arrClassSection[$srno]["class"] = $line['class'];
		$arrClassSection[$srno]["section"] = $line['section'];
		$srno++;
	}
	return $arrClassSection;
}
function getStudentDetails($cls, $schoolCode, $section)
{
	$userArray = array();
    $query = "SELECT userID, childName, concat(childClass,if(isnull(childSection),'', childSection)) as cls
              FROM   adepts_userDetails
              WHERE  category='STUDENT' AND endDate>=curdate() AND enabled=1  AND schoolCode=$schoolCode AND childClass='$cls' AND subjects like '%".SUBJECTNO."%'";		  
    if($section!="") {    	
		if(strpos($section, ',') > 0) // for the task 11269
		{
			$query .= " AND childSection IN ('".str_replace(',', "','", $section)."')";			
		} 
		else 
		{
			$query .= " AND childSection = '$section'";
		}

    }

    $query .= " ORDER BY childName";
	$r = mysql_query($query) or die($query."<br/>".mysql_error());
	while($l = mysql_fetch_array($r))
	{
	    $userArray[$l[0]][0] = $l[1];
	    $userArray[$l[0]][1] = $l[2];	    
	}	
	return $userArray;
}
function getTeacherIDs($schoolCode)
{
	include_once("classes/testTeacherIDs.php");
	$arrTeachers = array();
	$query = "SELECT userID, childName FROM adepts_userDetails WHERE schoolCode=$schoolCode AND category='TEACHER' AND endDate>=curdate() AND enabled=1 AND username not in ('".implode(',', $testIDArray)."') ORDER BY childName";
	$result = mysql_query($query);
	while($line = mysql_fetch_array($result))
		$arrTeachers[$line['userID']] = $line[1];
	return $arrTeachers;
}
function getColor()
{
	global $colorCounter;
	$colors = array("99FFFF","66CCFF","66BBFF","66AAFF","6699FF","6677FF","6655FF","6633FF","003366","000000");
	$colorCounter = ($colorCounter == 9)?0:$colorCounter++;
	$colorCounter++;
	return $colors[4];
	
}
function daysInMonth($year, $month)
{
	$timestamp = mktime(0,0,0,$month,1,$year);
	$noofdays = date("t",$timestamp);
	return $noofdays;
}
function getAverageProgress($studentWiseProgress)
{
	return round(array_sum($studentWiseProgress) / count($studentWiseProgress),1);
}
function getStudentProgress($ttCode,$userArray,$cls)
{
	foreach($userArray as $userID)
	{
		$flow_query = "SELECT flow FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode'";
		$flow_result = mysql_query($flow_query);
		$flow_line = mysql_fetch_array($flow_result);
		$flow = $flow_line['flow'];
		$flow = str_replace(" ","_",$flow);
		${"objTopicProgress".$flow} = new topicProgress($ttCode, $cls, $flow, SUBJECTNO);
		$studentProgress[$userID] = ${"objTopicProgress".$flow}->getProgressInTT($userID);
	}
	return $studentProgress;
}
//$activationPeriod must be in days
function getTTsActivated($cls, $schoolCode, $section,$mode="active",$limit=0,$activationPeriod=0)
{
    $ttAttemptedArray = array();
    $query = "SELECT A.teacherTopicCode,teacherTopicDesc FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
		      WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND A.section='$section' AND A.teacherTopicCode=B.teacherTopicCode";
	if($mode=="active")
	{
		$query .= " AND ISNULL(deactivationDate)";
	}
	if($activationPeriod != 0)
	{
		$lastDate = date('Y-m-d',strtotime("-20 days"));
		$query .= " AND A.activationDate<'$lastDate'";
	}
	$query .= " ORDER by A.activationDate desc";
	if($limit != 0)
		$query .= " LIMIT $limit";
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result))
    {
		$ttAttemptedArray[$line[0]]	= $line[1];
    }
    return $ttAttemptedArray;
}

function getTTs($cls, $schoolCode, $section,$mode="active",$limit=0,$activationPeriod=0)
{
    $ttAttemptedArray = array();
    $query = "SELECT A.teacherTopicCode,teacherTopicDesc FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B  
		      WHERE A.schoolcode='$schoolCode' AND A.class='$cls' AND ";
	if(strpos($section, ",")>0)
	{
		$section = str_replace(",", "','", $section);
		$query .= "A.section IN('$section') ";
	}
	else
		$query .= "A.section='$section' ";

	$query .= "AND A.teacherTopicCode=B.teacherTopicCode ORDER by A.activationDate desc";	
	if($limit != 0)
		$query .= " LIMIT $limit";
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result))
    {
		$ttAttemptedArray[$line[0]]	= $line[1];
    }
    return $ttAttemptedArray;
}

function averageTopicAccuracy($userIDs, $ttCode)
{
	$accuracy = 0;
	$sql = "SELECT SUM(R) as correct, COUNT(R) as total FROM ".TBL_QUES_ATTEMPT." WHERE userID IN ($userIDs) AND teacherTopicCode='$ttCode'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$correct = $row["correct"];
	$total = $row["total"];
	if($total != 0)
		$accuracy = round(($correct/$total)*100,2);
	return($accuracy);
}
function perClusterFailure($userIDs, $ttCode)
{
	$sql = "SELECT COUNT(clusterAttemptID) as clusterAttempts FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b WHERE b.userID IN ($userIDs) AND teacherTopicCode='$ttCode' AND a.ttAttemptID=b.ttAttemptID";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$clusterAttempts = $row["clusterAttempts"];
	
	$sql = "SELECT COUNT(clusterAttemptID) as failedCount FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b WHERE b.userID IN ($userIDs) AND teacherTopicCode='$ttCode' AND a.ttAttemptID=b.ttAttemptID AND a.result='FAILURE'";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$failedAttempts = $row["failedCount"];
	
	$clusterAccuracy = round(($failedAttempts/$clusterAttempts)*100,2);
	return($clusterAccuracy);
}

function getQuestionAttempted($userArray,$ttCode)
{
    $questionArray = array();
	foreach ($userArray as $userID)
	{
		$sq	=	"SELECT COUNT(srno) AS total, SUM(IF(R=1,1,0)) AS correct, SUM(s) AS timeSpent FROM ".TBL_QUES_ATTEMPT."
				 WHERE userID=$userID AND teacherTopicCode='$ttCode'";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_assoc($rs))
		{
			$questionArray[$userID]["quesAttempt"] = $rw['total'];
			$questionArray[$userID]["perCorrect"] = 0;
	        if($rw['total']>0)
			    $questionArray[$userID]["perCorrect"]	=	round($rw['correct']/$rw['total']*100,1);
			$questionArray[$userID]["timeTaken"] = $rw['timeSpent'];
		}
	}
	return $questionArray;
}
function getTopicsAttemptNo($userArray,$ttCode)
{
	$topicAttemptNo	=	array();
	foreach ($userArray as $userID=>$userDetail)
	{
		$sq	=	"SELECT MAX(ttAttemptNo) AS attempt FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode'";
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_assoc($rs);
		$topicAttemptNo[$userID]	=	$rw['attempt'];
	}
	return $topicAttemptNo;
}
function getTimeWiseDetails($userArray,$fromDate,$tillDate)
{
	foreach ($userArray as $userID=>$userDetail)
	{
		$sq	=	"SELECT count(srno) AS total, sum(if(R=1,1,0)) as correct, round(sum(s)/60) timeSpent, group_concat(distinct teacherTopicCode) as ttString
				 FROM ".TBL_SESSION_STATUS." A , ".TBL_QUES_ATTEMPT." B 
				 WHERE A.userID=$userID AND A.sessionID=B.sessionID AND A.userID=B.userID AND startTime > '$fromDate' AND startTime < '$tillDate'";
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_assoc($rs);
		$studentTimeWiseDetails[$userID]['total']	=	$rw['total'];
		$studentTimeWiseDetails[$userID]['timeSpent']	=	$rw['timeSpent'];
		$studentTimeWiseDetails[$userID]['ttCodes']	=	$rw['ttString'];
		if($rw['total']>0)
			$studentTimeWiseDetails[$userID]["perCorrect"]	=	round($rw['correct']/$rw['total']*100,1);
	}
	return $studentTimeWiseDetails;
}

function deactivateTopic($schoolCode,$cls,$section,$ttcode,$modifiedBy)
{

	$query = "select priority,flow from adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND teacherTopicCode='$ttcode' AND deactivationDate='0000-00-00'";
	$result = mysql_query($query);
	if($line = mysql_fetch_array($result)){
		$flow = $line['flow']; 
		$priority = $line['priority']; 
	}

	$query = "select max(priority) as priority from adepts_teacherTopicActivation where priority<100 and schoolCode=$schoolCode AND class=$cls AND section='$section' AND deactivationDate='0000-00-00'";

	$result = mysql_query($query);
	if($maxPriority = mysql_fetch_row($result))
	{
		if(!is_null($maxPriority[0]) && $priority != 100)
			$maxPriority = $maxPriority[0];
		else
			$maxPriority = 100;
	}
	
  $deactivequery="UPDATE adepts_teacherTopicActivation SET priority = priority-1 WHERE priority > $priority and schoolCode=$schoolCode AND class=$cls AND section='$section' AND deactivationDate='0000-00-00' AND priority <> 100";

	$result	=	mysql_query($deactivequery);

	$sq	=	"UPDATE adepts_teacherTopicActivation SET deactivationDate=CURDATE(),priority = $maxPriority,lastModifiedBy='$modifiedBy'
			 WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND teacherTopicCode='$ttcode' AND deactivationDate='0000-00-00'";
	$rs	=	mysql_query($sq);
	if(substr($flow,0,6)=="Custom") {
		//$ttcode   = trim(substr($flow,9));
		$isCustom = 1; 
	}
	else
		$isCustom = 0;
	$ttNameCode=getTopicNameCode($ttcode,$isCustom);
	if (!isMainTTWithinClassRange($schoolCode,$ttNameCode[1],$cls)){
		$ttNameCode[1]=0;
	}
	return implode("|~|", $ttNameCode)." deactivated successfully";
}
function isMainTTWithinClassRange($schoolCode,$ttCode,$class){
	$querySchoolFlow = "SELECT settingValue as defaultFlow FROM (userInterfaceSettings) WHERE schoolCode = '$schoolCode' AND settingName = 'curriculum' LIMIT 1";
	$result = mysql_query($querySchoolFlow);
	$line = mysql_fetch_array($result);
	$schoolDefaultFlow = strtolower($line[0]);
	if ($schoolDefaultFlow!='cbse' && $schoolDefaultFlow!='icse' && $schoolDefaultFlow!='igcse' && $schoolDefaultFlow!='ms')
		$schoolDefaultFlow='ms';

	$queryLUcount = "SELECT tm.teacherTopicCode, tm.teacherTopicDesc
					FROM adepts_teacherTopicMaster AS tm 
						LEFT JOIN adepts_teacherTopicClusterMaster AS tcm ON tm.teacherTopicCode=tcm.teacherTopicCode 
						LEFT JOIN adepts_clusterMaster AS cm ON tcm.clusterCode=cm.clusterCode 
					WHERE tm.customTopic=0 AND tm.subjectno=2 AND tm.live=1 AND tm.teacherTopicCode = '$ttCode'
					AND (FIND_IN_SET('$class',cm.".$schoolDefaultFlow."_level) OR FIND_IN_SET('".($class-1)."',cm.".$schoolDefaultFlow."_level) OR FIND_IN_SET('".($class+1)."',cm.".$schoolDefaultFlow."_level))";
	$result = mysql_query($queryLUcount);
	if (mysql_num_rows($result)>0) return true;
	else return false;
}
function topicAvailableForActivation($schoolCode,$cls,$section,$ttcode,$flow,$isClusterArray=false)
{		
	$currentTTClusters = array();
	$liveClusters = array();
	$commonClustersTopic = array();
	
	if ($isClusterArray)
		$currentTTCluster= "'".str_replace(",","','",$ttcode)."'";
	else 
		$currentTTCluster = getTTClusters($ttcode,$schoolCode,$cls,$section,$flow);
				
	$liveCluster = disableLiveClusters($ttcode,$schoolCode,$cls,$section,$flow);
	
	$liveClusters = explode(',', $liveCluster);
	$currentTTClusters = explode(',', $currentTTCluster);
	
	$commonClusters = array_intersect($currentTTClusters,$liveClusters);
	if (!$isClusterArray){
		$sqCheck = "SELECT srno FROM adepts_teacherTopicActivation 
					WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND teacherTopicCode='$ttcode' AND deactivationDate='0000-00-00'";
		$rsCheck = mysql_query($sqCheck);
		if($rwCheck = mysql_fetch_array($rsCheck))
		{
			return 0;
		}
	}
	$string = "''";	
	if(count($commonClusters)>0 && strcmp($commonClusters[0],$string)!=0)
	{
		return -1;
	}
	$topicsActivated = 0;
	$query = "SELECT COUNT(srno) FROM adepts_teacherTopicActivation WHERE schoolcode=$schoolCode AND class=$cls AND section='$section' AND deactivationDate='0000-00-00'";
	$rs = mysql_query($query) or die(mysql_error().$query);
	if($rw = mysql_fetch_array($rs))
	{
		$topicsActivated = $rw[0];
	}
	if ($topicsActivated>=15) return -2;
	return 1;
}
function activatedTopic($schoolCode,$cls,$section,$ttcode,$flow,$modifiedBy,$notCovered=0,$fromDate='',$toDate='')
{
	$currentTTClusters = array();
	$liveClusters = array();
	$commonClustersTopic = array();

	$currentTTCluster = getTTClusters($ttcode,$schoolCode,$cls,$section,$flow);
	$liveCluster = disableLiveClusters($ttcode,$schoolCode,$cls,$section,$flow);
	$topicsMappedToCluster = disableLiveClusters($ttcode,$schoolCode,$cls,$section,$flow,"getTopicsMappedToLiveClusters");
	$liveClusters = explode(',', $liveCluster);
	$topicsMappedToClusters = explode('~', $topicsMappedToCluster);
	$currentTTClusters = explode(',', $currentTTCluster);
	$commonClusters = array_intersect($currentTTClusters,$liveClusters);
	$string = "''";
	if(count($commonClusters)>0 && strcmp($commonClusters[0],$string)!=0) //Double quote nahi hai
		{
			if(substr($flow,0,6)=="Custom") {
				$isCustom = 1; 
			}
			else
				$isCustom = 0;
			$topicName = getTopicName($ttcode,$isCustom);
			$msg = "$topicName could not be activated since some of its learning unit(s) are already live. ";
			echo $msg."\r\n";
			echo "Following topic(s) contain those repeated learning units : "."\r\n";
			foreach($commonClusters as $value)
			{
				$keys = array_search($value, $liveClusters);
				array_push($commonClustersTopic,$topicsMappedToClusters[$keys]); //List of clusters for creating subset
			}
			foreach(array_unique($commonClustersTopic) as $value)
			echo substr($value,1,-1)."\r\n";
			exit;
		}
	$sqCheck = "SELECT srno FROM adepts_teacherTopicActivation
				WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND teacherTopicCode='$ttcode' AND deactivationDate='0000-00-00'";
	$rsCheck = mysql_query($sqCheck);
	if($rwCheck = mysql_fetch_array($rsCheck))
	{
		return "Already Activated";
	}
	else
	{
			$activequery	=	"UPDATE adepts_teacherTopicActivation SET priority = priority+1 
								WHERE schoolCode=$schoolCode AND class=$cls AND section='$section' AND priority != 100 ";
			mysql_query($activequery);
			if($flow=="undefined" || $flow=="Custom")
			{
				$message = "INSERT INTO adepts_teacherTopicActivation
							SET schoolCode=$schoolCode,class=$cls,section='$section',teacherTopicCode='$ttcode',activationDate=CURDATE(),flow='$flow', priority = 1 ,lastModifiedBy='$modifiedBy',notCovered=$notCovered,fromDate='$fromDate',toDate='$toDate'";
				mail("chirag.vijay@ei-india.com,khushboo.thakkar@ei-india.com","flow-undefined",$message);
				$flow = "MS";
			}
			$sq	=	"INSERT INTO adepts_teacherTopicActivation
					SET schoolCode=$schoolCode,class=$cls,section='$section',teacherTopicCode='$ttcode',activationDate=CURDATE(),flow='$flow', priority = 1 ,lastModifiedBy='$modifiedBy',notCovered=$notCovered,fromDate='$fromDate',toDate='$toDate'";
			mysql_query($sq);
			//Added the function which will create CSV, obtain parent learning objectives and store the required details in DB
			obtainClustersForDiagnosticTest($currentTTCluster,$flow,$ttcode,$schoolCode,$cls,$section);
			return "Activated Successfully";
	}
}

function getActivatedTopicsForSelectedClassAndSection($schoolCode, $class, $section="", $category, $fromDate="", $tillDate="") //for the task 11269
{
	$topicArray = array();
	if (strcasecmp($category,"Home Center Admin")==0)
	{
		$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
		          FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
			      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND b.schoolCode=$schoolCode AND b.class=".$class;
	}
	else
	{
		$query = "SELECT b.class, a.teacherTopicCode, a.teacherTopicDesc
		          FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
			      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND b.schoolCode=$schoolCode AND b.class=".$class;
	}

	if($section!="")
	{
		if(strpos($section, '\'') === false)	// if section is not quoted
		{
			$section = "'".str_replace(",", "','", $section)."'";
		}
		$query .= " AND section in ($section)";
	}
	$query .= " ORDER BY teacherTopicDesc";
	$topic_result = mysql_query($query) or die(mysql_error());
	while ($topic_line=mysql_fetch_array($topic_result))
	{
		$topicArray[$topic_line['class']][$topic_line['teacherTopicCode']] = $topic_line['teacherTopicDesc'];
	}
	if(count($topicArray)>0)
		return $topicArray;
	else 
		return array();
}

function getTTClusters($ttCode,$schoolCode,$class,$section,$flow)
{

	$currentTTClusters = "";
	
	if(substr($flow,0,6)=="Custom") 
	{
		$code   = trim(substr($flow,9));
		$sq = "Select clusterCodes from adepts_customizedTopicDetails where code=$code";
		$results	=	mysql_query($sq);
		while($rows=mysql_fetch_array($results))
		{
			/*array_push($currentTTClusters,$rows[0]);*/
			$currentTTClusters	=	$currentTTClusters.$rows[0].",";
		}
		
	} 
	else
	{
		$level = strtolower($flow)."_level";
		$sql = "select a.clusterCode from adepts_clusterMaster a,adepts_teacherTopicClusterMaster b where a.clusterCode = b.clusterCode and b.teacherTopicCode='$ttCode'and find_in_set($class,$level)>0;";
		
		$result = mysql_query($sql);
		while($line=mysql_fetch_array($result))
		{
			/*array_push($currentTTClusters,$line[0]);*/
			$currentTTClusters	=	$currentTTClusters.$line[0].",";
		}
	}
	$currentTTClusters	=	substr($currentTTClusters,0,-1);
	$currentTTClusters	=	"'".str_replace(",","','",$currentTTClusters)."'";
	return $currentTTClusters;	
}

function getTopicName($ttcode,$isCustom=0)
{
	//if($isCustom==0)
	//{
		$sql = "select teacherTopicDesc from adepts_teacherTopicMaster where teacherTopicCode='$ttcode'";
		$result=mysql_query($sql);
		$row = mysql_result($result,0);
	//}
	/*else
	{
		$sql = "select teacherTopicDesc from adepts_teacherTopicMaster where customCode='$ttcode'";
		$result=mysql_query($sql);
		$row = mysql_result($result,0);
		
	}*/
	return $row;
}
function getTopicNameCode($ttcode,$isCustom=0)
{
	//if($isCustom==0)
	{
		$sql = "select teacherTopicDesc,IF(customTopic=1,parentTeacherTopicCode,teacherTopicCode) from adepts_teacherTopicMaster where teacherTopicCode='$ttcode'";
		$result=mysql_query($sql);
		$row = mysql_fetch_array($result);
		$ttName=$row[0];
		$mainTTcode=$row[1];
	}
	/*else
	{
		$sql = "select teacherTopicDesc,parentTeacherTopicCode from adepts_teacherTopicMaster where customCode='$ttcode'";
		$result=mysql_query($sql);
		$row = mysql_fetch_array($result);
		
	}*/
	return array($ttName,$mainTTcode);
}

function checkTeacherSchoolCode($schoolcodeAnnounce, $schoolCode) {
	$valid = '';
	$schoolcodeAnnounce = ',' . $schoolcodeAnnounce . ',';
	$chkschool = stripos ( $schoolcodeAnnounce, ',' . $schoolCode . ',' );

	if ($chkschool !== false) {
		$valid = "valid";
	}
	return $valid;
}

function getAnnouncements($currentDate,$schoolCode,$userID)
{
	$announcementsArray	=	array();
	$sq	=	"SELECT id,contentId,title,category,schoolCode,class,status FROM adepts_teacherAnnouncements WHERE status='Approved' AND '$currentDate' BETWEEN fromDate AND tillDate";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_assoc($rs))
	{
		$schoolcodeAnnounce	=	$rw['schoolCode'];
		$id					=	$rw['id'];
		$class				=	$rw['class'];

	//check class belong to teacher or not
		if($class=='All')
			$classValidity	=	'valid';
		else
			$classValidity	=	checkTeacherClass($class,$userID);

	//check for schoolcode
		if ($schoolcodeAnnounce=='All')
			$schoolcodeValidity	=	'valid';
		else
			$schoolcodeValidity	=	checkTeacherSchoolCode($schoolcodeAnnounce,$schoolCode);

		if($classValidity=='' || $schoolcodeValidity=='')
			continue;

		if ($rw['category']!='Other')
		{
			if($rw['category']=='Topic')
			{
				$announcementsArray[$id]['link']	=	"announcements.php?id=".$rw['id'];
			}

			else if($rw['category']=='Remedial')
			{
				$announcementsArray[$id]['link']	=	"remedialItem.php?qcode=".$rw['contentId'];
			}

			else if($rw['category']=='Cluster')
			{
			    $ttCode = getTTCode($rw['contentId']);
				$announcementsArray[$id]['link']	=	"sample_ques.php?ttCode=$ttCode&learningunit=".$rw['contentId'];
			}

			else if($rw['category']=='Timed test')
			{
				$announcementsArray[$id]['link']	=	"timedTest.php?timedTest=".$rw['contentId']."&tmpMode=sample";
			}

			else if($rw['category']=='Games')
			{
				$announcementsArray[$id]['link']	=	"enrichmentModule.php?gameID=".$rw['contentId'];
			}

			$announcementsArray[$id]['title']		=	$rw['title'];
			$announcementsArray[$id]['contentId']	=	$rw['contentId'];
				//display title on the page
		}
		else if ($rw['category']=='Other' and $rw['status']=='Approved')
		{
			$announcementsArray[$id]['title']	=	$rw['title'];
			$announcementsArray[$id]['class']	=	$rw['class'];
			if($rw['id']==43)
				$announcementsArray[$id]['link']	=	"feedbackform_content.php";
			else
				$announcementsArray[$id]['link']	=	"announcements.php?id=".$rw['id'];
		}
	}
	return $announcementsArray;
}


function getTTCode($clusterCode)
{
    $query  = "SELECT teacherTopicCode FROM adepts_teacherTopicClusterMaster WHERE clusterCode='$clusterCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    return $line[0];
}

/*function getTTName($clusterCode)
{
    $query  = "SELECT teacherTopicCode FROM adepts_teacherTopicClusterMaster WHERE clusterCode='$clusterCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
	$ttCode = $line[0];
	
	$query = "Select teacherTopicDesc from adepts_teacherTopicMaster where teacherTopicCode='$ttCode'";

	$result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    return $line[0];
}*/

/*function getTopicName($ttCode)
{
    $query  = "SELECT teacherTopicDesc FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";
    $result = mysql_query($query);
    $line   = mysql_fetch_array($result);
    return $line[0];
}*/

function trimTopicName($topicName,$strLength)
{
	if(strlen($topicName) > ($strLength+3))
		$smallTopicName = substr($topicName,0,32)."...";
	else
		$smallTopicName = $topicName;
	return $smallTopicName;
}

function getSavedMappingOfTT($schoolCode, $cls, $section)
{
	$savedMappingArray = array();
	$query = "SELECT teacherTopicCode, flow FROM adepts_schoolTeacherTopicFlow
	          WHERE  schoolCode=$schoolCode AND class=$cls";
	if($section!="")
		$query .= " AND section='$section'";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($line = mysql_fetch_array($result))
	{
		$flow = $line['flow'];
		/*if(substr($flow,0,6)=="Custom")
			$flow = "Custom";*/
		$savedMappingArray[$line['teacherTopicCode']] = $flow;
	}
	return $savedMappingArray;
}
function getClassLevel($teacherTopicCode, $flow="MS")
{
	$field = "";
	if(strcasecmp($flow,"MS")==0)
		$field = "ms_level";
	elseif (strcasecmp($flow,"CBSE")==0)
		$field = "cbse_level";
	elseif (strcasecmp($flow,"ICSE")==0)
		$field = "icse_level";
	elseif (strcasecmp($flow,"IGCSE")==0)
		$field = "igcse_level";
	else
		$field = "level";
	$query = "SELECT distinct $field
			  FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
			  WHERE  teacherTopicCode='$teacherTopicCode' AND a.clusterCode=b.clusterCode AND b.status='live' AND a.clusterCode NOT LIKE '%100'";
	$result = mysql_query($query) or die(mysql_error());
	$classes = array();
	while($user_row = mysql_fetch_array($result))
	{
		$tmpClassArr = explode(",",$user_row[0]);
		for($i=0; $i<count($tmpClassArr); $i++)
		{
			if(trim($tmpClassArr[$i])!="")
				array_push($classes,trim($tmpClassArr[$i]));
		}

	}
	$classes = array_unique($classes);
	sort($classes,SORT_NUMERIC);
	return $classes;
}
function getTopicsActivatedInOldFlow($schoolCode, $cls, $section)
{
	$topicsFollowingOldFlow = array();
	$query = "SELECT distinct a.teacherTopicCode
	          FROM   ".TBL_TOPIC_STATUS." a, adepts_userDetails b
	          WHERE  a.userID=b.userID AND enabled=1 AND
	                 b.schoolcode=$schoolCode AND
	                 b.childClass='$cls' AND
	                 isnull(a.flow)";
	if($section!="")
	    $query .= " AND childsection ='$section'";
	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
		array_push($topicsFollowingOldFlow, $line[0]);
	return $topicsFollowingOldFlow;
}
function getCustomTTs($schoolCode, $cls, $section, $masterTopic="")
{
    $customTopicArray = array();
   $query  = "SELECT teacherTopicCode, teacherTopicDesc, parentTeacherTopicCode, customCode FROM adepts_teacherTopicMaster
               WHERE  subjectno=".SUBJECTNO." AND schoolCode=$schoolCode AND class=$cls AND customTopic=1 AND live=1 AND teacherTopicCode 
			   NOT IN (SELECT DISTINCT(teacherTopicCode) FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$cls";
	if ($section!="")
	{
		$query .= " AND section='$section'";
	}
	$query.= ") ";
	if($masterTopic!="")
	{
		$query .= " AND classification='$masterTopic'";
	}
	$query.= " ORDER BY parentTeacherTopicCode, lastModified";

    $result = mysql_query($query) or die(mysql_error());
    $prevTT= "";
    while ($line = mysql_fetch_array($result)) {
        if($prevTT!=$line['parentTeacherTopicCode'])
            $srno = 0;
    	$customTopicArray[$line['parentTeacherTopicCode']][$srno][0] = $line['teacherTopicCode'];
    	$customTopicArray[$line['parentTeacherTopicCode']][$srno][1] = $line['teacherTopicDesc'];
    	$customTopicArray[$line['parentTeacherTopicCode']][$srno][2] = $line['customCode'];
    	$customTopicArray[$line['parentTeacherTopicCode']][$srno][3] = $cls;   //since the custom TT is mapped to this class only
    	$prevTT = $line['parentTeacherTopicCode'];
    	$srno++;
    }
    return $customTopicArray;
}
function getDaysTillActivated($start)
{
	$end	=	date("Y-m-d");
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}
function getDaysTillActivatedDeactive($start,$end)
{
	$start_ts = strtotime($start);
	$end_ts = strtotime($end);
	$diff = $end_ts - $start_ts;
	return round($diff / 86400);
}
function setDateFormate($date)
{
	$dateArr	=	explode("-",$date);
	return $dateArr[2]."-".$dateArr[1]."-".$dateArr[0];
}

function disableLiveClusters($ttCode,$schoolCode,$class,$section,$current_flow,$mode="")
{
	$liveTopicList = array();
	$liveTopicFlow = array();
	$liveClusterList = "";
	$liveClusterToicNameList = "";
	$k=0;

	$sql = "Select teacherTopicCode,flow from adepts_teacherTopicActivation where schoolCode=$schoolCode AND class=$class AND section='$section' and deactivationDate = '0000-00-00'";
	$result	=	mysql_query($sql);
	while($row=mysql_fetch_array($result))
	{
		array_push($liveTopicList,$row[0]);
		array_push($liveTopicFlow,$row[1]);
	}
		
	foreach($liveTopicList as $values)
	{	
		$topic_name = "";
		$flow = $liveTopicFlow[$k];
		$k++;
		$tmpArray = array();
		
		if($mode=="getTopicsMappedToLiveClusters")
		{
			$sql_topic = "select teacherTopicDesc from adepts_teacherTopicMaster where teacherTopicCode='$values'";
			$results_topic	=	mysql_query($sql_topic);
			$rows_topic=mysql_fetch_array($results_topic);
			$topic_name = $rows_topic[0];
		}
		
		if(substr($flow,0,6)=="Custom") 
		{
			
			$tmpList = "";
			$code   = trim(substr($flow,9));
			$sq = "Select clusterCodes from adepts_customizedTopicDetails where code=$code";
			$results	=	mysql_query($sq);

			while($rows=mysql_fetch_array($results))
			{
				$liveClusterList	=	$liveClusterList.$rows[0].",";
				$tmpList = $tmpList.$rows[0].",";
			}
			
			if($mode=="getTopicsMappedToLiveClusters") {
				$tmpArray = explode(',', $tmpList);
				for($i=0;$i<count($tmpArray)-1;$i++)
				$liveClusterToicNameList = $liveClusterToicNameList.$topic_name."~";
			}
			
			
			continue ;
		} 
		/*if($flow != $current_flow && $_REQUEST['flow'] != "") continue ;*/
		$flow_level = $flow."_level";
		$liveTopicList[$values] = array();
		$sql = "Select clusterCode from adepts_teacherTopicClusterMaster where teacherTopicCode='$values'";
		$result	=	mysql_query($sql);
		while($row=mysql_fetch_array($result))
		{
			array_push($liveTopicList[$values],$row[0]);
		}
		
		
			foreach($liveTopicList[$values] as $val)
			{	
						
				$sq = "Select clusterCode from adepts_clusterMaster where FIND_IN_SET($class,$flow_level) and clusterCode='$val'"; 
				$results	=	mysql_query($sq);
				while($rows=mysql_fetch_array($results))
				{
					$liveClusterList	=	$liveClusterList.$rows[0].",";
					if($mode=="getTopicsMappedToLiveClusters")
					$liveClusterToicNameList = $liveClusterToicNameList.$topic_name."~";

				}

			}
	}	
	$liveClusterList	=	substr($liveClusterList,0,-1);
	$liveClusterList	=	"'".str_replace(",","','",$liveClusterList)."'";
	
	$liveClusterToicNameList	=	substr($liveClusterToicNameList,0,-1);
	$liveClusterToicNameList	=	"'".str_replace("~","'~'",$liveClusterToicNameList)."'";
	
	if($mode=="getTopicsMappedToLiveClusters")
	return $liveClusterToicNameList;
	else
	return $liveClusterList;
}

function getQuesAccuracy($userID,$ttCode,$class)
{
	$sq	=	"SELECT COUNT(srno),SUM(R) FROM adepts_teacherTopicQuesAttempt_class$class A, adepts_teacherTopicClusterStatus B
			 WHERE A.userID=$userID AND teacherTopicCode='$ttCode' AND A.clusterAttemptID=B.clusterAttemptID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$arrayDetails["totalQ"]		=	$rw[0];
	$arrayDetails["accuracy"]	=	round(($rw[1]/$rw[0])*100,1);
	return $arrayDetails;
}

function getClusterName($clusterCodes)
{
	if(is_array($clusterCodes))
		$clusterCodes	=	implode("','",$clusterCodes);
	/*if($clusterCodes!="")
		$clusterCodes	=	str_replace(",","','",$clusterCodes);*/
	$sq	=	"SELECT cluster FROM adepts_clusterMaster WHERE clusterCode IN ('$clusterCodes')";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$clusterFailed	.=	$rw[0].", ";
	}
	return $clusterFailed	=	substr($clusterFailed,0,-2);
}

function search_array($needle, $haystack) {
     if(in_array($needle, $haystack)) {
          return true;
     }
     foreach($haystack as $element) {
          if(is_array($element) && search_array($needle, $element))
               return true;
     }
   return false;
}

function getCurrentStatus($userID,$ttCode)
{
	$arrayCurrentStatus	=	array();
	$sq =	"SELECT clusterCode, currentSDL FROM ".TBL_CURRENT_STATUS." WHERE userID=$userID AND teacherTopicCode='$ttCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$arrayCurrentStatus["currentCluster"]	=	$rw[0];
	$arrayCurrentStatus["currentSdl"]	=	$rw[1];
	return $arrayCurrentStatus;
}

function showTopicProgress($userID, $progress, $failedClusters, $clusterArray, $sdlArray, $currentCluster, $currentSDL,$class,$result)
{
	global $ttCode;
	$totalSDLs = 0;
	$perContribution = array();
	$currentStageSdlContribution = 0;
	$currentStageClusterContribution = 0;
	$clusterFoundBool = 1;
	$clearedSDLsInCluster = 0;
	$failedCluster=0;
	for($counter=0; $counter<count($clusterArray); $counter++)
	{
		$noOfSDLsInCluster = count(explode(",",$sdlArray[$clusterArray[$counter]]));
		$totalSDLs += $noOfSDLsInCluster;
	}
	for($counter=0; $counter<count($clusterArray); $counter++)
	{
		$noOfSDLsInCluster = count(explode(",",$sdlArray[$clusterArray[$counter]]));
		$perContribution[$clusterArray[$counter]] = $noOfSDLsInCluster*100/$totalSDLs;
		if($currentCluster != "NotFound" && $currentSDL != "NotFound" && $clusterArray[$counter] == $currentCluster)
		{
			$sdlInClusterArray = explode(",",$sdlArray[$currentCluster]);
			foreach($sdlInClusterArray as $sdlLevel)
			{
				if($sdlLevel < $currentSDL && $class>3)
					$clearedSDLsInCluster++;
				else if($sdlLevel <= $currentSDL && $class<=3)
					$clearedSDLsInCluster++;					
			}
			$sdlCompletionInCluster = $clearedSDLsInCluster*100/$noOfSDLsInCluster;
			$currentStageSdlContribution = $sdlCompletionInCluster*$perContribution[$clusterArray[$counter]]/100;
			if(($clearedSDLsInCluster/$noOfSDLsInCluster)*100<75){
				$failedCluster=1;
			}
		}
	}

    if(round($progress,1)=='100' && count($clusterArray)>1){
		$htmlForProgress = '<div class="progress-container" style="background:#32CD32 !important">';
	}else{
		$htmlForProgress = '<div class="progress-container">';
	}
	$perCovered = 0;
	$tempCount = 0;
	
	for($counter=0; $counter<count($clusterArray) && $perCovered<$progress; $counter++)
	{
		
		$clusterCode = $clusterArray[$counter];
		$per = $perContribution[$clusterCode];
		$per = $per;

		if($clusterCode == $currentCluster)
			$clusterFoundBool = 0;
		if($perCovered + $per > 100)
			$per = 100 - $perCovered;
		if(($perCovered + $per) > $progress)
		{
			
			$width = $progress - $perCovered;
			if(in_array($clusterCode,$failedClusters))
			{
				//$htmlForProgress .= '<div style="width:'.($per - $width).'%;" class="red"></div>';
				$roundedVal = $per - 2;
				$htmlForProgress .= '<div style="width:'.$roundedVal.'%;" class="green"></div>';
				$htmlForProgress .= '<div style="width:2%;" title="'.$clusterCode.'" class="red"></div>';
				$tempCount = $per;
			}
			else
			{
				$roundedVal = $width;
				$htmlForProgress .= '<div style="width:'.$roundedVal.'%;" class="green"></div>';
				$tempCount = $width;
			}
		}
		else if(!in_array($clusterCode, $failedClusters))
		{
			
			$roundedVal = $per;
			$htmlForProgress .= '<div style="width:'.$roundedVal.'%" class="green"></div>';
			$tempCount = $per;
		}
		else
		{
			
			$roundedVal = $per - 2;
			$htmlForProgress .= '<div style="width:'.$roundedVal.'%;" class="green"></div>';
			$htmlForProgress .= '<div style="width:2%;" title="'.getClusterName($clusterCode).'" class="red"></div>';
			$tempCount = $per;
		}
		$perCovered += $per;

		if($clusterCode != "NotFound" && $clusterFoundBool == 1)
			$currentStageClusterContribution += $tempCount;
			

		if($clusterCode == $currentCluster && $currentStageSdlContribution > $tempCount)
			$currentStageSdlContribution = $tempCount;
		//echo $currentStageSdlContribution;
	}
	
	$htmlForProgress .= '</div>';
	$htmlForProgress .= "<span>".round($progress,1)."%</span>";
	$currentStageTotal = $currentStageSdlContribution + $currentStageClusterContribution;
	//print_r($failedClusters);
	if($currentStageTotal != 100 && round($currentStageTotal,1)<round($progress,1))
	{
		$dummyArray[0] = $currentCluster;
		$clusterDesc = getClusterDesc($dummyArray);
		$clusterDesc = substr($clusterDesc,3,strlen($clusterDesc)-8);
		$leftMargin = $currentStageTotal - 2; // For little deviation to display arrow at proper position.
		$currentStageHtml = '<div class="progress-container" style="border:none;background-color:#E6E6E6;">';
		if(count(explode(",",userttAttemptedArray($userID,$ttCode))) > 1)
			$currentStageHtml .= '<span style="margin-left:'.$leftMargin.'%;font-size:1em;" title="Current Position: '.round($currentStageTotal,1).'% , Repeated Attempt, '.$clusterDesc.'">&darr;<sub>R</sub>';
		else if($failedCluster==1 && $result!="SUCCESS")
			$currentStageHtml .= '<span style="margin-left:'.$leftMargin.'%;font-size:1.2em;" title="Current Position: '.round($currentStageTotal,1).'% , '.$clusterDesc.'">&darr;';
		$currentStageHtml .= '</span></div><div style="clear:both;"></div>';
		$htmlForProgress = $currentStageHtml.$htmlForProgress;
	}
	return $htmlForProgress;
}

function getClusterDesc($failedClusterArray)
{
	$clusterStr = "";
	$failedClusterStr = "";
	if(!is_array($failedClusterArray) || count($failedClusterArray)==0)
		return $clusterStr;
	for($i=0; $i<count($failedClusterArray); $i++)
		$clusterStr .= "'".trim($failedClusterArray[$i])."',";
	$clusterStr = substr($clusterStr,0,-1);
	if($clusterStr!="")
	{
		$query  = "SELECT cluster FROM adepts_clusterMaster WHERE clusterCode in ($clusterStr)";
		//echo $query;
		$result = mysql_query($query);
		$srno = 1;
		while ($line   = mysql_fetch_array($result))
		{
			$failedClusterStr .= $srno.". ".$line[0]."<br/>";
			$srno++;
		}
		//$failedClusterStr = substr($failedClusterStr,0,-2);
	}
	return $failedClusterStr;
}

function userttAttemptedArray($userID,$ttCode)
{
	$attempt_query = "SELECT ttAttemptID FROM ".TBL_TOPIC_STATUS." 
					  WHERE userID=$userID AND teacherTopicCode='$ttCode' ORDER BY ttAttemptID";
	$attempt_result = mysql_query($attempt_query);
	if(mysql_num_rows($attempt_result)==0)
	   continue;
	$ttAttemptArray = array();
	while($attempt_line = mysql_fetch_array($attempt_result))
	{
		array_push($ttAttemptArray,$attempt_line[0]);
	}
	$ttAttempts = implode(",",$ttAttemptArray);
	return $ttAttempts;
}

function newkudosCounter($userName)
{
	//echo " USERNAME IS- ". $_SESSION['username'];
	$query="Select count(*) as newKudosCount from kudosMaster where view=1 and receiver='$userName'";
	$result = mysql_query($query);
	if($row = mysql_fetch_array($result))
	{
		$newKudosCount=$row['newKudosCount'];		
	}
	return $newKudosCount;	
}

function resetKudosCounter($userName)
{
	//echo "IN RESET KUDOS COUNTER";
    //echo " USERNAME IS - ".$userName;
	
	$query = "UPDATE educatio_adepts.kudosMaster set view=0 where view=1 and receiver='$userName'";
	$result = mysql_query($query) or die(' Update Kudos Counter Query Failed: '.mysql_error());
		
}

function getDefaultFlowForTheSchool($schoolCode){

	$defaultFlow = 'MS';

	$flow_query  = "SELECT settingValue FROM userInterfaceSettings WHERE schoolCode='$schoolCode' and settingName='curriculum' limit 1";

	$flow_result = mysql_query($flow_query);

	if($flow_line=mysql_fetch_assoc($flow_result))
	{
				$defaultFlow = $flow_line['settingValue'];		
	}
	
	return $defaultFlow;

}

function getTopicPageLink($passedttCode,$passedClass,$passedSection){
	$schoolCode = $_SESSION['schoolCode'];
	
/*	$flow_query  = "SELECT defaultFlow, allowDeactivatedTopicsAtHome FROM adepts_schoolRegistration WHERE school_code=$schoolCode";
	$flow_result = mysql_query($flow_query);
	if($flow_line=mysql_fetch_array($flow_result))
	{
		$defaultFlow = $flow_line[0];
	}*/

	$defaultFlow = getDefaultFlowForTheSchool($schoolCode);
	
	$sql = "select a.teacherTopicDesc,b.teacherTopicCode,b.flow,a.parentTeacherTopicCode,a.customCode,a.customTopic from adepts_teacherTopicMaster a left join adepts_teacherTopicActivation b on a.teacherTopicCode=b.teacherTopicCode and b.class=$passedClass and b.section='$passedSection' and  b.schoolCode=$schoolCode where a.teacherTopicCode='$passedttCode'";
	$query =  mysql_query($sql)or die(mysql_error());
	$result=mysql_fetch_row($query);
	
	$flowToPass = $result[2] == ''? $defaultFlow : $result[2];
	$flow = $flowToPass;
	if($result[5] != 0){
	 	$clsLevelArray = getClassLevel($result[3],$defaultFlow);
	 	if($result[1] == ''){
	 		$flow = 'Custom - '.$result[4];
	 	}
	 }
	else{
		$clsLevelArray = getClassLevel($passedttCode,$flowToPass);
	}
	
	$activeMode ="";
	if($result[1] == '' && $result[5] == 0){
		$activeMode = "&activateMode=yes";
	}
	
	
	$clsLevel = "";
	if(count($clsLevelArray)>0)
		$clsLevel = implode(",",$clsLevelArray);
	$class_explode = explode(",",$clsLevel);
	$max_grade = max($class_explode);
	$min_grade = min($class_explode);
	for($a=$min_grade;$a<=$max_grade;$a++){
		if($a==$cls){
			$pos=1;
		}
	}
	if ($max_grade==$min_grade)
	{
		$grade = $min_grade;
	}
	else
	{
		$grade = $min_grade."-".$max_grade;
	}
	if($max_grade=="" && $min_grade==""){
		$grade = $cls;
	}
	if ($max_grade == $cls && $min_grade == $cls)
	{
		$grade = $cls;
	}
	return "mytopics.php?ttCode=$passedttCode&cls=$passedClass&section=$passedSection&flow=$flow&interface=new&gradeRange=$grade.$activeMode";
}
function checkForCoteacherTopic($ttCode,$class,$customTopic,$parentTeacherTopicCode='',$topicFlow)
{
	$coteacherTopicFlag = 0;		
	$where = '';
	$teacherTopicCode = $customTopic == 1? $parentTeacherTopicCode : $ttCode;	
	$ttObj = new teacherTopic($ttCode,$class,$topicFlow);
	$topicClusterArray[] =	$ttObj->getClustersOfLevel($class);
	$topicClusters = array_unique(array_reduce($topicClusterArray, 'array_merge', array()));	
	if($customTopic == 0)
	{
		//check for if topic has entry for coteacher topics
		$ctQuery = "SELECT flow,class from coteacherTopicDetails where teacherTopicCode='$teacherTopicCode' and find_in_set($class,class) and status=1";				
		$ctResult = mysql_query($ctQuery);	
		if(mysql_num_rows($ctResult) > 0)
	    {
			$coteacherTopicFlag = 1;
		}
	}
	else
	{		
		$ctQuery = "SELECT flow,class from coteacherTopicDetails where teacherTopicCode='$teacherTopicCode' and status=1";			
		$ctResult = mysql_query($ctQuery);	
		if(mysql_num_rows($ctResult) > 0)
	    {
    		while($ctLine = mysql_fetch_array($ctResult))
    		{    			
	    		if($ctLine[0]=="MS")
					$fieldName = "ms_level";
				else if($ctLine[0]=="CBSE")
					$fieldName = "cbse_level";
				else if($ctLine[0]=="ICSE")
					$fieldName = "icse_level";
				else if($ctLine[0]=="IGCSE")
					$fieldName = "igcse_level";

				$classes = explode(',', $ctLine[1]);
				foreach($classes as $coteacherClass)
				{
	    			$where[] = " FIND_IN_SET($coteacherClass,$fieldName)";
				}
		    }
	    	$whereStr = implode(' OR ', $where);
	    	if($whereStr != '')
	    	{	    		
		    	$query = "SELECT DISTINCT(a.clusterCode) as clusterCode from adepts_teacherTopicClusterMaster a JOIN adepts_clusterMaster b ON a.clusterCode=b.clusterCode where a.teacherTopicCode='$teacherTopicCode' and ($whereStr)";	
		    	$result = mysql_query($query);	
				while($row = mysql_fetch_array($result))
    			{   	
					$coteacherClusters[] = $row[0];
				}						
				if(!empty($coteacherClusters))
				{
					if (array_intersect($topicClusters, $coteacherClusters) == $topicClusters)
			    			$coteacherTopicFlag = 1;
			    }
	    	}
		}	   	
    }   
    return $coteacherTopicFlag;
   	
}

function obtainClustersForDiagnosticTest($clusters,$flow,$ttcode,$schoolCode,$cls,$section){
	$pre_req = array();
	$learning_qCode = array();
	$currentlySelected_learningObjectives = array();
	//Obtain learning objectives for all the selected clusters
	$learning_qCode = obtain_learning_objective_qcode($clusters,$flow);
	$currentlySelected_learningObjectives = $learning_qCode;
	//Check if the current class is eligibile for KST.
	$isAdaptive = checkAdaptiveEligibility($ttcode,$schoolCode,$flow);
	if(isset($learning_qCode) && (count($learning_qCode) > 0) && $isAdaptive == 1 ){
		//Obtain parent learning objectives for all selected learning objectives
		$pre_req = array_unique(parent_learning_obj($learning_qCode));
		$learning_qCode = array_merge($learning_qCode,$pre_req);
		for($i =0; $i<count($pre_req); $i++)
		{
			$pre_req = array_unique(parent_learning_obj($pre_req));
			$learning_qCode = array_unique(array_merge($learning_qCode,$pre_req));
		}
		$pre_req = array_diff($learning_qCode, $currentlySelected_learningObjectives);
		//Call api to generate CSV dynamically for selected data.
		$isCSV = generateCSVFromAPI($ttcode, $currentlySelected_learningObjectives, $pre_req, $schoolCode,$cls,$section,$flow);
		if($isCSV == 1){
			//Insert subset data into DB
			insertSubsetData($ttcode, $clusters, $currentlySelected_learningObjectives, $pre_req, $flow);
		}
	}
		return ;
}

//Obtain learning objectives for all the selected clusters
    function obtain_learning_objective_qcode($clusterCode){
		$learning_objective_qcode = array();
        if(is_array($clusterCode)){
            $clusterCode = "'" . implode( "','",$clusterCode) . "'";
        }
        $query = "SELECT learning_objective_qcode FROM educatio_adepts.learning_objective_qcode_cluster_mapping_Fractions where mapped_cluster in ($clusterCode)";
		$result = mysql_query($query) or die( "Invalid query".$query);
        while($row=mysql_fetch_array($result)){
			$learning_objective_qcode[] = $row['learning_objective_qcode'];
        }
        return $learning_objective_qcode;
    }
    //Obtain parent learning objectives for all selected learning objectives
    function parent_learning_obj($learning_objective_qcode){
        $pre_req = array();
        $qcode = "'" . implode( "','",$learning_objective_qcode) . "'";
        $query = "SELECT parent_learning_obj from educatio_adepts.kst_skill_tree_tuple where child_learning_obj in ($qcode)";
        $result = mysql_query($query) or die( "Invalid query".$query);
        while($row=mysql_fetch_array($result)){
			$pre_req[] = $row['parent_learning_obj'];
        }
        return $pre_req;
    }
    //Insert subset data into DB
    function insertSubsetData($ttcode, $clusters, $currentlySelected_learningObjectives, $pre_req, $flow){
        $list_clusters = explode(",",$clusters);
        foreach($list_clusters as $data){
            $cluster_list[] = trim($data,"'");
        }
        $cluster_list = "'" . implode( ",",$cluster_list) . "'";
        $current = "'" . implode( ",",$currentlySelected_learningObjectives) . "'";
        $parent = "'" . implode( ",",$pre_req) . "'";
        $query = "INSERT INTO educatio_adepts.kst_subsetListForDiagnosticTest (teacherTopicCode, list_of_clusters, currently_selectedClusters, parent_learning_objectives, flow)
								  SELECT * FROM (SELECT '".$ttcode."', $cluster_list, $current, $parent, '".$flow."') AS tmp
								  WHERE NOT EXISTS (SELECT teacherTopicCode FROM educatio_adepts.kst_subsetListForDiagnosticTest WHERE teacherTopicCode = '$ttcode' ) LIMIT 1";
        return mysql_query($query);
    }
       //Call api to generate CSV dynamically for selected data.
    function generateCSVFromAPI($ttcode, $currentlySelected_learningObjectives, $pre_req,$schoolCode,$cls,$section,$flow){
		$subset_list = array();
        if (preg_match('#^Custom#', $flow) === 1) {
			$query = "SELECT parentTeacherTopicCode FROM educatio_adepts.adepts_teacherTopicMaster where teacherTopicCode='$ttcode'";
			$result = mysql_query($query) or die( "Invalid query".$query);
			$line = mysql_fetch_array( $result);
			$ptopicCode = $line[0];
			$query = "SELECT filename FROM educatio_adepts.kst_masterMappingTable where find_in_set('$ptopicCode',teacherTopicCode)";
		} else {
			$query = "SELECT filename FROM educatio_adepts.kst_masterMappingTable where find_in_set('$ttcode',teacherTopicCode)";
		}
		$result = mysql_query($query) or die( "Unable to fetch master filename ".$query);
		$line = mysql_fetch_array( $result);
		$master_fileName = $line[0];
		$subset_list = array_merge($currentlySelected_learningObjectives, $pre_req);
		$ttcode = $schoolCode."_".$cls."_".$section."_".$ttcode;
		$request = array('subset_list' => json_encode($subset_list), 'ttcode'=>$ttcode, 'master_file'=>$master_fileName);
		$url = 'http://127.0.0.1:5000/subsetList/'; //write a constant here.
        // Send using curl
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url); // URL to post
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // return into a variable
        curl_setopt( $ch, CURLOPT_HTTPHEADER, 'Content-Type:application/x-www-form-urlencoded' ); // headers from above
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_PORT, 5000);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$request);
        $result = curl_exec( $ch ); // runs the post
		//perform tasks on $result
        curl_close($ch);
        return $result;
    }
//Check if the current class,school is eligibile for KST.
    function checkAdaptiveEligibility($ttcode,$schoolCode,$flow){

        if (preg_match('#^Custom#', $flow) === 1) {
                $query = "SELECT parentTeacherTopicCode FROM educatio_adepts.adepts_teacherTopicMaster where teacherTopicCode='$ttcode'";
                $result = mysql_query($query) or die( "Invalid query".$query);
				$line = mysql_fetch_array( $result);
				$ptopicCode = $line[0];

                $query = "SELECT isActive FROM educatio_adepts.adepts_actdeact_feature_schools WHERE schoolCode=$schoolCode and find_in_set('$ptopicCode',teacherTopicCode)";
                $result = mysql_query($query) or die( "Invalid query".$query);
				$line = mysql_fetch_array( $result);
				return $line[0];

        } else {
                $query = "SELECT isActive FROM educatio_adepts.adepts_actdeact_feature_schools WHERE schoolCode=$schoolCode and find_in_set('$ttcode',teacherTopicCode)";
				$result = mysql_query($query) or die( "Invalid query".$query);
				$line = mysql_fetch_array( $result);
				return $line[0];
        }
	}

?>
