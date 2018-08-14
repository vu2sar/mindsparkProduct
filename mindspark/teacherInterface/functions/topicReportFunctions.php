<?php
function checkForAssessment($schoolCode,$class,$clusterString,$clusters){
	$assessmentFlag = 0;
	$querySettings = "SELECT * from userInterfaceSettings a where a.schoolCode=".$schoolCode." and a.class=".$class." and a.settingName='comprehensiveModuleActivation' and a.settingValue=1";		
	$settingsResult = mysql_query($querySettings);	
	if(mysql_num_rows($settingsResult) > 0)
	{
		foreach($clusters as $cluster)
		{
			$cmQueryArray[]= "FIND_IN_SET('$cluster',a.linkedToCluster)";
		}	
		$cmQuery = "SELECT a.comprehensiveModuleCode,a.linkedToDiagnosticTest from adepts_comprehensiveModuleMaster a JOIN adepts_diagnosticTestMaster b ON a.linkedToDiagnosticTest=b.diagnosticTestID and b.linkToCluster IN('$clusterString') where a.status='Live' and b.status = 1 and b.testType='Assessment' ";
		$cmQuery .= " AND ( ".implode(' OR ',$cmQueryArray)." ) ";				
		$cmResult = mysql_query($cmQuery);	
		if($cmLine = mysql_fetch_row($cmResult))
			$assessmentFlag = 1;
	}

	return $assessmentFlag;
}

function getAssessmentDetails($ttCode,$userDetails)
{
	$assessmentArray = $arrayProgressDetails = 	$userIDnotCompleted = array();
	$correctQuestions = $totalQuestions = 0;
	$userIDs	=	array_keys($userDetails);
	$userIDstr	=	implode(",",$userIDs);
	
	$sq = "SELECT GROUP_CONCAT(a.srno),SUM(b.status),count(b.attemptID),GROUP_CONCAT(b.attemptID) from adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b ON a.srno=b.srno  JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=b.diagnosticTestID JOIN adepts_teacherTopicStatus e ON e.ttAttemptID=b.ttAttemptID  where d.testType ='Assessment' and e.teacherTopicCode='$ttCode' and a.userID IN($userIDstr) GROUP BY a.ttAttemptID";	
	$rs = mysql_query($sq);
	while($row = mysql_fetch_array($rs))
	{
		if($row[1] == $row[2])
		{
			$query = "SELECT c.userID,SUM(c.R),count(c.srno) from adepts_diagnosticQuestionAttempt c where c.attemptID IN($row[0]) GROUP BY c.userID";						
			$result = mysql_query($query);
			while($line = mysql_fetch_array($result))
			{										
					$correctQuestions += $line[1];
					$totalQuestions += $line[2];			
					$assessmentArray[$line[0]]['name']=$userDetails[$line[0]][0];			
					$assessmentArray[$line[0]]['accuracy']=ROUND($line[1]/$line[2]*100,2);	
					$assessmentArray[$line[0]]['id']=$row[3];					
			}
		}
	}
	if(!empty($assessmentArray))
	{		
		$userIDsAttempted = array_keys($assessmentArray);	
		$allUsersArray = explode(',', $userIDstr);
		foreach ($allUsersArray as $key => $value) {
			if(!in_array($value,$userIDsAttempted))
			{
				$userIDnotCompleted[$value]['name'] = $userDetails[$value][0];
				$userIDnotCompleted[$value]['accuracy'] = "-";
			}
		}
	}
	$arrayProgressDetails['totalStudents'] = count($userDetails);
	$arrayProgressDetails['completedStudents'] = count($assessmentArray);
	$arrayProgressDetails['avgAccuracy'] = ROUND($correctQuestions/$totalQuestions*100);
	$arrayProgressDetails['classAccuracyReport'] = !empty($assessmentArray) ? assessmentAccuracySummary($assessmentArray,$userIDnotCompleted) : array();
	return $arrayProgressDetails;
}
function getIncompleteAssessmentDetails($ttCode,$userDetails,$userIDstr)
{
	$userArray = $completeUserId = array();		
	$sq = "SELECT GROUP_CONCAT(a.ttAttemptID) from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN($userIDstr)";
	$rs = mysql_query($sq);
	if($line=mysql_fetch_array($rs))
	{		
		$sq = "SELECT a.userID as users from adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b ON a.srno=b.srno JOIN adepts_diagnosticQuestionAttempt c ON c.attemptID=a.srno JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=b.diagnosticTestID where a.ttAttemptID IN($line[0]) and d.testType ='Assessment' and b.`status`= 1 GROUP BY a.srno";			
		$rs = mysql_query($sq);
		while($line = mysql_fetch_array($rs))
		{				
			$completeUserId[]=$line[0];						
		}
		$incompleteUserIds = array_diff(array_keys($userDetails), $completeUserId);		
		foreach ($incompleteUserIds as $key => $value) {
			$userArray[$userDetails[$value][1]]++;
		}
	}
	return $userArray;	
}

function assessmentAccuracySummary($assessmentDetails,$userIDnotCompleted)
{
	$arrayAccuracyDetails[0] = $arrayAccuracyDetails[1] = $arrayAccuracyDetails[2]=$arrayAccuracyDetails[3] = array();	
	foreach($assessmentDetails as $userID=>$otherDetails)
	{
		if($otherDetails["accuracy"]<40)
			$arrayAccuracyDetails[0][$userID] = $otherDetails;
		else if($otherDetails["accuracy"]>=40 && $otherDetails["accuracy"]<80)
			$arrayAccuracyDetails[1][$userID] = $otherDetails;
		else
			$arrayAccuracyDetails[2][$userID] = $otherDetails;
	}
	foreach ($userIDnotCompleted as $userID => $otherDetails) {		
		$arrayAccuracyDetails[3][$userID] = $otherDetails;
	}
	return $arrayAccuracyDetails;
}

function getTeacherTopicProgress($ttCode,$userIDstr,$cls,$userDetails)
{
	$progress	=	array();
	$flowN	=	array();
	$total	= $totalProgress =	0;	
    $clusterArray    = array();    
	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID in (".$userIDstr.") AND teacherTopicCode='".$ttCode."'";	
    $r = mysql_query($q);
    while($l = mysql_fetch_array($r))
    {
    	$flowN = $l[0];
    	$flowStr = str_replace(" ","_",$flowN);
    	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
    }
	
	$sq	=	"SELECT userID, MAX(progress), flow
			 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID IN ($userIDstr) GROUP BY userID";			 		
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$flowK	=	$rw[2];
    	$flowK	=	str_replace(" ","_",$flowK);
    	$studentProgress = round(max($rw[1],${"objTopicProgress".$flowK}->getProgressInTT($rw[0])));		
		$teacherTopicArray[$rw[0]]["progress"]	=	$studentProgress > 100 ? 100 : $studentProgress;
		$teacherTopicArray[$rw[0]]["name"] = $userDetails[$rw[0]][0];		
		$progress[]	=	$teacherTopicArray[$rw[0]]["progress"];				
		$total++;
	}
	foreach ($userDetails as $key => $value) {
		if(!empty($teacherTopicArray[$key]))
		{
			$teacherTopicDetails[$key] = $teacherTopicArray[$key];				
		}
		else
		{
			$teacherTopicDetails[$key]["progress"] = 0;
			$teacherTopicDetails[$key]["name"] = $userDetails[$key][0];
		}
	}		
	$totalProgress	=	round(array_sum($progress)/(substr_count($userIDstr,",")+1));	
	$teacherTopicDetails["avgProgress"]	=	$totalProgress;	
	return $teacherTopicDetails;
}

function getLearningUnitSummary($clusterArray,$userIDstr,$cls,$ttCode)
{	
	$arrayLearningUnitSummary =  $clusterAttemptIdArray = array();
	$totalUsers = count(explode(",",$userIDstr));
	$totalPassed = 0;
	$query= "SELECT GROUP_CONCAT(DISTINCT a.ttAttemptID) as ttAttemptID from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN ($userIDstr)";		
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);		
	foreach($clusterArray as $clusterCode=>$clusterDetail)
	{
		$totalAttempted = 0;
		$totalPassed = 0;
		$totalUserAttempted = 0;
		$clusterAttemptString = "";
		$clusterAttemptIdArray = array();
		$arrayLearningUnitSummary[$clusterCode]["accuracy"] = 0;
		$arrayLearningUnitSummary[$clusterCode]["cluster"] = $clusterDetail;
		$arrayLearningUnitSummary[$clusterCode]["tick"] = 0;
		$arrayLearningUnitSummary[$clusterCode]["dash"] = 1;
		if($row[0] != '')
		{			
			//gives most recent attempt
			$sq = "SELECT MAX(clusterAttemptID) from adepts_teacherTopicClusterStatus where clusterCode='$clusterCode' and result IN('SUCCESS','FAILURE') and ttAttemptID IN($row[0]) GROUP BY userID ";					
			$rs = mysql_query($sq);
			while($rw = mysql_fetch_array($rs))
			{
				$clusterAttemptIdArray[] = $rw[0];
			}
			if(!empty($clusterAttemptIdArray))
			{			
				$clusterAttemptIdStr = implode(',', $clusterAttemptIdArray);
				$sq = "SELECT userID,clusterAttemptID,result from adepts_teacherTopicClusterStatus where clusterAttemptID IN($clusterAttemptIdStr)";				
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
						
				if(($totalPassed/$totalUsers) >= 0.75)
					$arrayLearningUnitSummary[$clusterCode]["tick"] = 1;		
				if(($totalUserAttempted/$totalUsers) >= 0.50)
					$arrayLearningUnitSummary[$clusterCode]["dash"] = 0;
			}
		}
	}			
	return $arrayLearningUnitSummary;
}

function getEstimatedTimeToComplete($clusterArray,$cls,$userIDstr)
{
	$arrayTimeToComplete = array();
	$arrayClusterWiseTime = array();
	$arrayComprehensiveDetails = getSubModuleMapped($clusterArray);		
	foreach($clusterArray as $clusterCode)
	{
		$diagnosticEstimation = 0;
		$comprehensiveEstimation = 0;
		$sq = "SELECT SUM(time) FROM estimatedTimePerCluster WHERE clusterCode='$clusterCode' AND class=$cls";		
		$rs = mysql_query($sq);
		if($rw = mysql_fetch_array($rs))
		{
			if(isset($arrayComprehensiveDetails[$clusterCode]))
			{
				$diagnosticEstimation = ($rw[0]*2)/15;
				if($arrayComprehensiveDetails[$clusterCode]["subModule"]==1)
					$comprehensiveEstimation = $rw[0]/2;
			}
			$arrayClusterWiseTime[$clusterCode] = round($rw[0] + $diagnosticEstimation + $comprehensiveEstimation);
		}
	}
	$estimatedTimeToComplete = array_sum($arrayClusterWiseTime) + ((array_sum($arrayClusterWiseTime)/count($arrayClusterWiseTime))*(count($clusterArray) - count($arrayClusterWiseTime)));
	$actualTimeSpent = getActualTimeSpent($clusterArray,$cls,$userIDstr,$arrayComprehensiveDetails);
	
	$timeToComplete = $estimatedTimeToComplete - $actualTimeSpent;
	if($timeToComplete<0)
		$timeToComplete = 0;
	$arrayTimeToComplete["minsToComplete"] = round($timeToComplete/60);
	$arrayTimeToComplete["sessionToComplete"] = ceil($timeToComplete/(60*20));
	return $arrayTimeToComplete;
}

function getActualTimeSpent($clusterArray,$cls,$userIDstr,$arrayComprehensiveDetails)
{
	$comprehensiveModuleCode = array();
	$diagnostic = array();	
	$minUsers = count(explode(',', $userIDstr))*0.3;	
	foreach($arrayComprehensiveDetails as $key=>$valArray)
	{
		$comprehensiveModuleCode[] = $valArray["comprehensiveModuleCode"];
		$diagnostic[] = $valArray["diagnostic"];
	}
	$timeSpent=0;
	$sq = "select A.ttAttemptID, sum(B.S)+sum(B.timeTakenForExpln) as timeToAns from adepts_teacherTopicClusterStatus A, adepts_teacherTopicQuesAttempt_class$cls B, adepts_teacherTopicStatus C where A.userID IN ($userIDstr) AND A.clusterAttemptID = B.clusterAttemptID and A.ttAttemptID = C.ttAttemptID and A.clusterCode IN ('".implode("','",$clusterArray)."') and C.ttAttemptNo=1 and A.result in ('SUCCESS','FAILURE') group by A.ttAttemptID having timeToAns>0 ORDER by timeToAns";	
	$rs = mysql_query($sq);
	while($rw = mysql_fetch_array($rs))
	{
		$sqDiagnostic = "SELECT SUM(timeTaken) FROM adepts_diagnosticTestAttempts WHERE diagnosticTestID IN ('".implode("','",$diagnostic)."') AND ttAttemptID='".$rw[0]."'";		
		$rsDiagnostic = mysql_query($sqDiagnostic);
		$rwDiagnostic = mysql_fetch_array($rsDiagnostic);
		
		$sqSubModule = "SELECT SUM(A.timeTaken) FROM adepts_userComprehensiveFlow A, adepts_comprehensiveModuleAttempt B WHERE B.ttAttemptID='".$rw[0]."' AND A.moduleType='cluster' AND A.srno=B.srno AND comprehensiveModuleCode IN ('".implode("','",$comprehensiveModuleCode)."')";		
		$rsSubModule = mysql_query($sqSubModule);
		$rwSubModule = mysql_fetch_array($rsSubModule);
		
		$arrayTime[] = round($rw[1]+$rwDiagnostic[0]+$rwSubModule[0]);		
		if(count($arrayTime)>$minUsers)
		{
			asort($arrayTime);
			$key = round(count($arrayTime)*0.9);
			$timeSpent = $arrayTime[$key-1];
		}
	}
	return $timeSpent;
}

function getSubModuleMapped($clusterArray)
{
	$arrayComprehensiveDetails = array();
	$sq = "SELECT linkedToCluster,comprehensiveModuleCode,linkedToDiagnosticTest FROM adepts_comprehensiveModuleMaster A, adepts_diagnosticTestMaster B WHERE A.linkedToDiagnosticTest=B.diagnosticTestID AND testType='Prerequisite' AND linkedToCluster IN ('".implode("','",$clusterArray)."') GROUP BY linkedToCluster";
	$rs = mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arrayComprehensiveDetails[$rw[0]]["diagnostic"] = $rw[2];
		$arrayComprehensiveDetails[$rw[0]]["comprehensiveModuleCode"] = $rw[1];
		$sq1 = "SELECT moduleCode FROM adepts_comprehensiveSubModuleMaster WHERE comprehensiveModuleCode='".$rw[1]."'";		
		$rs1 = mysql_query($sq1);
		if(mysql_num_rows($rs1)>0)
			$arrayComprehensiveDetails[$rw[0]]["subModule"] = 1;
		else
			$arrayComprehensiveDetails[$rw[0]]["subModule"] = 0;
	}
	return $arrayComprehensiveDetails;
}
function progressSummary($ttDetails)
{	
	$arrayProgressDetails[0] = array();
	$arrayProgressDetails[1] = array();
	$arrayProgressDetails[2] = array();
	foreach($ttDetails as $userID=>$otherDetails)
	{
		if($otherDetails["progress"]<50)
			$arrayProgressDetails[0][$userID] = $otherDetails;
		else if($otherDetails["progress"]>=50 && $otherDetails["progress"]<100)
			$arrayProgressDetails[1][$userID] = $otherDetails;
		else
			$arrayProgressDetails[2][$userID] = $otherDetails;
	}
	return $arrayProgressDetails;
}

function getFailedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$limit)
{
	$failedClusterArray = $failedClusterDetails = array();
	$fcQuery= "SELECT a.clusterCode,a.userID,SUM(IF(result='FAILURE',1,0)) as failureCount,SUM(IF(result='SUCCESS',1,0)) as successCount,date(MAX(a.lastModified)) as lastAttemptDate from adepts_teacherTopicClusterStatus a JOIN adepts_clusterMaster b ON a.clusterCode=b.clusterCode where a.ttAttemptID IN($ttAttemptIDStr) and a.clusterCode IN('$clusterString') GROUP BY a.clusterCode,a.userID having failureCount>1 and successCount=0 and DATEDIFF(CURDATE(),lastAttemptDate)<15 order by failureCount DESC";	
	$fcResult = mysql_query($fcQuery);
	while($fcLine = mysql_fetch_array($fcResult))
	{
		$failedClusterArray[$fcLine[0]]['students'][] = $userDetails[$fcLine[2]][0];
		$failedClusterArray[$fcLine[0]]['description'] = $clusterDetail[$fcLine[0]];
	}			
	if(!empty($failedClusterArray))
	{			
		$message = ' immediate help with ';	
		$failedClusterArray = array_slice($failedClusterArray,0,$limit-1,true);			
		$failedClusterDetails = make_message($failedClusterArray,$message,'sampleQuestion');
	}				
	return $failedClusterDetails;
}
function getPassedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$limit)
{
	$passedClusterArray = $passedClusterDetails = array();
	$i=0;
	$pcQuery= "SELECT a.clusterCode,a.userID,GROUP_CONCAT(a.result order by a.clusterAttemptID ASC) as result from adepts_teacherTopicClusterStatus a where a.ttAttemptID IN($ttAttemptIDStr) and a.clusterCode IN('$clusterString') GROUP BY a.clusterCode,a.userID having result like '%FAILURE,SUCCESS%'";		
	// echo $pcQuery;
	$pcResult = mysql_query($pcQuery);
	while($pcLine = mysql_fetch_array($pcResult))
	{
		$passedClusterArray[$pcLine[0]]['students'][] = $userDetails[$pcLine[1]][0];
		$passedClusterArray[$pcLine[0]]['description'] = $clusterDetail[$pcLine[0]];
	}			
	if(!empty($passedClusterArray))
	{			

		$passedClusterArray = array_slice($passedClusterArray,0,$limit-1,true);
		$message = ' underwent remediation and cleared the concept ';		
		foreach($passedClusterArray as $clusterCode=>$clusterValue)
		{
			if(count($clusterValue['students']) < 3 )			
				$studentNames = implode(' & ',$clusterValue['students']);							
			else			
				$studentNames = $clusterValue['students'][0].' & '.(count($clusterValue['students'])-1).' more students';				
			$passedClusterDetails[$i]['type'] = "sampleQuestion";
			$passedClusterDetails[$i]['id'] = $clusterCode;
			$passedClusterDetails[$i]['message'] = $studentNames.$message.$clusterValue['description'].".";
			$i++;
		}		
	}				
	return $passedClusterDetails;
}
function getLessAccuracyCluster($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$limit)
{	
	$lessAccuracyClusterArray = $lessAccuracyClusterDetails = $clusterAttemptIdArray = array();
	$accQuery = "SELECT MAX(a.clusterAttemptID) from adepts_teacherTopicClusterStatus a where a.result='SUCCESS' and a.ttAttemptID IN($ttAttemptIDStr) and a.clusterCode IN('$clusterString') group by a.clusterCode,a.userID";	
	$accResult = mysql_query($accQuery);	
	while($accLine = mysql_fetch_array($accResult))
	{
		$clusterAttemptIdArray[] = $accLine[0];
	}
	if(!empty($clusterAttemptIdArray))
	{			
		$clusterAttemptIdStr = implode(',', $clusterAttemptIdArray);
		$sq = "SELECT clusterCode,userID,perCorrect from adepts_teacherTopicClusterStatus where clusterAttemptID IN($clusterAttemptIdStr) having perCorrect<40 order by perCorrect ASC";		
		$rs = mysql_query($sq);	
		while($rw = mysql_fetch_array($rs))
		{
			$lessAccuracyClusterArray[$rw[0]]['students'][] = $userDetails[$rw[1]][0];
			$lessAccuracyClusterArray[$rw[0]]['description'] = $clusterDetail[$rw[0]];
		}
	}		
	if(!empty($lessAccuracyClusterArray))
	{			
		$message = ' concept clarity in ';
		$lessAccuracyClusterArray = array_slice($lessAccuracyClusterArray,0,$limit-1,true);				
		$lessAccuracyClusterDetails = make_message($lessAccuracyClusterArray,$message,'sampleQuestion');
	}	
	return $lessAccuracyClusterDetails;
}
function getDiagnostictTestDetails($clusterString,$ttCode,$userIDstr)
{
	$dtArray = array();
	$dtQuery="SELECT count(DISTINCT a.userID) as users,c.description from adepts_diagnosticTestAttempts a JOIN adepts_diagnosticTestMaster b ON a.diagnosticTestID=b.diagnosticTestID JOIN adepts_comprehensiveSubModuleMaster c ON FIND_IN_SET(c.misconceptionCode,a.misconceptionCodes) JOIN adepts_teacherTopicStatus d ON d.ttAttemptID=a.ttAttemptID 
		where b.testType='Prerequisite' and b.linkToCluster IN('$clusterString') and d.teacherTopicCode='$ttCode' AND a.userID IN ($userIDstr) GROUP by c.misconceptionCode having (users>5) order by users DESC limit 2";			
	$dtResult = mysql_query($dtQuery);		
	$i=0;
	while($dtLine = mysql_fetch_array($dtResult))
	{			
		$prerequisiteArray[$i] = $dtLine['description'];
		$i++;
	}
	
	if(count($prerequisiteArray)==1)
	{
		$dtArray[0]['type'] = 'message';
		$dtArray[0]['message'] = 'Mindspark revised one of the prerequisite concepts: '.$prerequisiteArray[0].'.';
		
	}			
	else if(count($prerequisiteArray) > 1)
	{
		$dtArray[0]['type'] = 'message';
		$dtArray[0]['message'] = 'Mindspark revised'.$prerequisiteArray[0].' & '.$prerequisiteArray[1].'  from the prequisite concepts.';
		
	}					
	return $dtArray;
}
function getLessAccuracyDailyPractice($clusterString,$userIDstr,$userDetails,$dailyPractiseArray,$limit)
{		
	$lessAccuracyDPDetails = $lessAccuracyArray = $practiseAttemptIdArray = array();
	$sq = "SELECT max(a.id) from practiseModulesTestStatus a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId  where a.`status`='completed' and b.linkedToCluster IN('$clusterString') and a.userID IN($userIDstr) group by a.practiseModuleId,a.userID ";			
	$rs = mysql_query($sq);
	while($rw = mysql_fetch_array($rs))
	{
		$practiseAttemptIdArray[] = $rw[0];
	}
	if(!empty($practiseAttemptIdArray))
	{			
		$practiseAttemptIdStr = implode(',', $practiseAttemptIdArray);
		$accQuery = "SELECT ROUND((SUM(R)/COUNT(b.id))*100) as accuracy,b.practiseModuleId,b.userID from practiseModulesQuestionAttemptDetails b where b.practiseModuleTestStatusId IN($practiseAttemptIdStr) group by b.practiseModuleId,b.userID having accuracy < 50 order by accuracy ASC";		
		$accResult = mysql_query($accQuery);
		while($accLine = mysql_fetch_array($accResult))
		{
			$lessAccuracyArray[$accLine[1]]['students'][] = $userDetails[$accLine[2]][0];
			$lessAccuracyArray[$accLine[1]]['description'] = $dailyPractiseArray[$accLine[1]];
		}
	}
	if(!empty($lessAccuracyArray))
	{
		$message = ' more practice in ';
		$lessAccuracyArray = array_slice($lessAccuracyArray,0,$limit-1,true);
		$lessAccuracyDPDetails = make_message($lessAccuracyArray,$message,'dailyPractice');			
	}		
	return $lessAccuracyDPDetails;
}
function getMoreAccuracyDailyPractice($clusterString,$userIDstr,$dailyPractiseArray,$needMinUsers,$limit)
{		
	$moreAccuracyDPDetails = $moreAccuracyArray = $practiseAttemptIdArray = array();	
	$i=0;	
	$sq = "SELECT max(a.id) from practiseModulesTestStatus a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId  where a.`status`='completed' and b.linkedToCluster IN('$clusterString') and a.userID IN($userIDstr) group by a.practiseModuleId,a.userID";		
	$rs = mysql_query($sq);
	while($rw = mysql_fetch_array($rs))
	{
		$practiseAttemptIdArray[] = $rw[0];
	}
	if(!empty($practiseAttemptIdArray))
	{			
		$practiseAttemptIdStr = implode(',', $practiseAttemptIdArray);
		$accQuery = "SELECT ROUND((SUM(R)/COUNT(b.id))*100) as accuracy,b.practiseModuleId,count(DISTINCT b.userID) as users from practiseModulesQuestionAttemptDetails b where b.practiseModuleTestStatusId IN($practiseAttemptIdStr) group by b.practiseModuleId having users>$needMinUsers and accuracy>75 limit $limit";	
		$accResult = mysql_query($accQuery);
		while($accLine = mysql_fetch_array($accResult))
		{			
			$moreAccuracyArray[$accLine[1]] = $dailyPractiseArray[$accLine[1]];
		}
	}
	if(!empty($moreAccuracyArray))
	{
		foreach($moreAccuracyArray as $key=> $value)
		{
			$moreAccuracyDPDetails[$i]['type'] = "dailyPractice";
			$moreAccuracyDPDetails[$i]['id'] = $key;
			$moreAccuracyDPDetails[$i]['message'] = "Daily practice on Conversion in ".$value." has helped the class to build fluency.";
			$i++;
		}				
	}		
	return $moreAccuracyDPDetails;
}
function getFirstStudent($ttCode,$userIDstr,$userDetails)
{
	$firstStudentArray = array();
	$fsQuery = "SELECT a.userID from adepts_userBadges a where a.badgeDescription='$ttCode' and  a.batchType='topicCompletion' and a.userID IN($userIDstr)  order by a.batchDate,a.lastModified ASC limit 1";			
	$fcResult = mysql_query($fsQuery);
	if($fcLine = mysql_fetch_row($fcResult))
		$username = $userDetails[$fcLine[0]][0];

	if($username != '')
	{
		$firstStudentArray[0]['type'] = "message";
		$firstStudentArray[0]['message'] = $username." is the first one to complete the topic.";
	}		
	return $firstStudentArray;
}
function make_message($clusterArray,$message,$type)
{
	$messageArray = array();
	$i = 0;
	foreach($clusterArray as $clusterCode=>$clusterValue)
	{
		if(count($clusterValue['students']) == 1 )
			$studentNames = $clusterValue['students'][0].' needs';
	 	else if(count($clusterValue['students']) == 2 )			
			$studentNames = implode(' & ',$clusterValue['students']).' need';							
		else			
			$studentNames = $clusterValue['students'][0].' & '.(count($clusterValue['students'])-1).' more students need';				
		$messageArray[$i]['type'] = $type;
		$messageArray[$i]['id'] = $clusterCode;
		$messageArray[$i]['message'] = $studentNames.$message.$clusterValue['description'].".";
		$i++;
	}

	return $messageArray;
}
function getClusterDetails($clusterString)
{
	$clusterArrayDetails = array();
	$clQuery = "SELECT clusterCode,cluster from adepts_clusterMaster where clusterCode IN('$clusterString') ORDER BY FIELD (clusterCode,'" . $clusterString . "')";			
	$clResult = mysql_query($clQuery);
	while($clLine =  mysql_fetch_array($clResult))
	{
		$clusterArrayDetails[$clLine[0]] = $clLine[1];
	}
	return $clusterArrayDetails;
}
function getDailyPractiseDetails($clusterString)
{
	$dailyPractiseDetails = array();
	$dpQuery = "SELECT practiseModuleId,description from practiseModuleDetails where linkedToCluster IN('$clusterString') ORDER BY FIELD (linkedToCluster,'" . $clusterString . "')";		
	$dpResult = mysql_query($dpQuery);
	while($dpLine =  mysql_fetch_array($dpResult))
	{
		$dailyPractiseDetails[$dpLine[0]] = $dpLine[1];
	}
	return $dailyPractiseDetails;
}
function getSchoolAvg($schoolCode,$clusterCode,$sdl,$class)
{
	$whereClause = "";
	if($schoolCode!="")
	{
		$whereClause =  "AND schoolCode=$schoolCode";
	}

	$sq = "SELECT ROUND((SUM(R)/COUNT(srno))*100,1) FROM adepts_questions A, adepts_teacherTopicQuesAttempt_class$class B, adepts_userDetails C 
			WHERE A.qcode=B.qcode AND B.userID=C.userID $whereClause AND category='STUDENT' AND A.clusterCode='$clusterCode' AND subdifficultylevel='$sdl'";			
	$rs = mysql_query($sq);		 
	$rw = mysql_fetch_array($rs);
	return $rw[0];
}
function getNationalAvg($clusterCode,$sdl,$class)
{
	$sq = "SELECT A.qcode,accuracy,B.majorVersion FROM adepts_questions A, adepts_questionPerformance B 
			WHERE A.qcode=B.qcode AND A.clusterCode='$clusterCode' AND subdifficultylevel='$sdl' AND class=$class order by B.majorVersion DESC";			
	$rs = mysql_query($sq);
	$accuracyArray = array();
	while($rw = mysql_fetch_array($rs))
	{
		if(!in_array($rw['qcode'], array_keys($accuracyArray)))
		{
			$accuracyArray[$rw['qcode']] = $rw['accuracy'];
		}
	}
	return round(array_sum($accuracyArray)/count($accuracyArray),1);
}
function getSchoolNationalAvgDT($schoolCode,$qcode,$class)
{
	$whereClause = "";
	if($schoolCode!="")
	{
		$whereClause =  "AND schoolCode=$schoolCode";
	}
	$sq = "SELECT ROUND((SUM(R)/COUNT(srno))*100,1) from adepts_diagnosticQuestionAttempt a JOIN adepts_userDetails b ON a.userID=b.userID where a.qcode=$qcode and b.category='STUDENT' and b.childClass=$class $whereClause";
	$rs = mysql_query($sq);		 
	$rw = mysql_fetch_array($rs);
	return $rw[0];
}
function getQuestionData($qcode, $schoolCode, $class, $section, $qsrn, $userIDStr, $cwaType, $ttCode,$cwaFlag)
{

	global $animationQues;
	global $totalQues;
    $mostCommonWrongAnswer = $questionStr = "";
    $question     = new Question($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

	if((strpos($question->getQuestion(), ".html") !== false || strpos($question->getQuestion(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false) && $qsrn==1)
		$animationQues++;

	if($qsrn==1)
		$totalQues++;
	if($cwaFlag == 0)
		$questionStr .= '<script type="text/javascript" src="/mindspark/js/load.js"></script>';	

    $questionStr .= "<p>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";
	$correctAns = $question->correctAnswer;
	
	$optionWiseAttempt = array();
	$answerwiseData = array();
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{
		$optionWiseAttempt[$correctAns] = 0;
		if($cwaType==1)
		{						
			$sq = "SELECT count(srno) AS CNT, A as ans FROM adepts_teacherTopicQuesAttempt_class".$class." WHERE qcode=".$qcode." AND userID IN (".$userIDStr.") AND teacherTopicCode='".$ttCode."' group by A";
		}
		else
		{
			$sq = "SELECT count(srno) AS CNT, A as ans FROM adepts_teacherTopicQuesAttempt_class".$class." A, adepts_userDetails B
					 WHERE A.userID=B.userID AND qcode=".$qcode." AND category='STUDENT' AND A.teacherTopicCode='".$ttCode."' group by A";						
		}
		$rs = mysql_query($sq);
		while($rw = mysql_fetch_array($rs))
		{			
			$optionWiseAttempt[$rw[1]] = $rw[0];
		}		
		$totalAttempt = array_sum($optionWiseAttempt);
		arsort($optionWiseAttempt);
		$mcra = 0; //most common wrong answer count
		$displayFlag = $cwaFlag==1 ? "display:none;" : '';
		foreach($optionWiseAttempt as $optionVal=>$optionCount)
		{
			if($correctAns==$optionVal)
			{
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='".$displayFlag."border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
			}
			elseif($mcra==0)
			{
				$mostCommonWrong = $optionVal;
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='".$displayFlag."border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
				$mcra++;
			}
			else
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='".$displayFlag."border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
	
			$answerwiseData[$optionVal] = $a;
		}
    
    	$questionStr .= "<table width='98%' border='0' cellpadding='3'>";

	    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
		
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='43%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	             if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				 if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='42%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-4')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td  id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='43%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	            $questionStr .= "<td id='td_4".$qcode."' width='5%'";
	            if($mostCommonWrong == 'D')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'D')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>D</strong>. </td><td align='left' width='42%'>".$question->getOptionD()." ".$answerwiseData['D']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-3')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='28%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	            if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='28%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	            $questionStr .= "<td id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='28%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	        $questionStr .= "</tr>";
	    }
	    $questionStr .= "</table>";
    }
	
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{
		$str = 'optionPercentage_'.$qcode;
		$questionStr .= '<br>';
		if($cwaFlag==1)
			$questionStr .= "<div id ='divAnswerPercentage".$str."' style='width: 170px;padding: 2px;cursor:pointer;background-color: #cbcaca;border-radius: 19px;box-shadow:2px 4px 2px #888888;font-size:14px;text-align:center;' onclick=showAnswerPercentage('".$str."',".$qcode.")>+ Option-wise performance</div>";
	}
	
    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><span class='title'>Answer</span>: ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."<br/>";
    	else
    		$questionStr .= "<br/>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<br/><span class='title'> Answer</span>: ".$question->getCorrectAnswerForDisplay()."<br/>";
	
	$showMostCommonWrongAns = 1;
	
	if($question_type!='I' && $question_type!='MCQ-4' && $question_type!='MCQ-2' && $question_type!='MCQ-3' && $question_type!='D')
	{
		$questionStr .= "<br><div id='cwa_$qcode' class='cwa'></div><input type='hidden' value='".$qcode.'#'.$dynamic.'#'.$showMostCommonWrongAns.'#'.$question_type.'#'.$question->correctAnswer.'#'.$class.'#'.$userIDStr.'#'.$cwaType."'>";			
	}
    return $questionStr;
}

function getDiagnosticQuestionData($qcode,$userIDStr,$attemptID,$cwaFlag)
{

	global $animationQues;
	global $totalQues;
    $mostCommonWrongAnswer = $questionStr = "";    
    $question = new diagnosticTestQuestion($qcode);
    $dynamic = 0;

	if($question->isDynamic())
	{
		$dynamic = 1;
		$question->generateQuestion();
	}

    $question_type = $question->quesType;

	if((strpos($question->getQuestion(), ".html") !== false || strpos($question->getQuestion(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false || strpos($question->getDisplayAnswer(), ".swf") !== false) && $qsrn==1)
		$animationQues++;

	if($qsrn==1)
		$totalQues++;
	if($cwaFlag == 0)
		$questionStr .= '<script type="text/javascript" src="/mindspark/js/load.js"></script>';	

    $questionStr .= "<p>";
    $questionStr .= $question->getQuestion()."<br/>";
    $questionStr .= "</p>";
	$correctAns = $question->correctAnswer;
	
	$optionWiseAttempt = array();
	$answerwiseData = array();
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{		
		$optionWiseAttempt[$correctAns] = 0;
		$sq = "SELECT count(srno) AS CNT, A as ans FROM adepts_diagnosticQuestionAttempt WHERE qcode=".$qcode." AND userID IN ($userIDStr) and attemptID IN($attemptID) group by A";			
		$rs = mysql_query($sq);
		while($rw = mysql_fetch_array($rs))
		{
			$optionWiseAttempt[$rw[1]] = $rw[0];
		}		
		$totalAttempt = array_sum($optionWiseAttempt);
		arsort($optionWiseAttempt);
		$mcra = 0; //most common wrong answer count
		$displayFlag = $cwaFlag==1 ? "display:none;" : '';
		foreach($optionWiseAttempt as $optionVal=>$optionCount)
		{
			if($correctAns==$optionVal)
			{
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='".$displayFlag."border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
			}
			elseif($mcra==0)
			{
				$mostCommonWrong = $optionVal;
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='".$displayFlag."border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
				$mcra++;
			}
			else
				$a = "<div class='optionPercentage_".$qcode."' id='".$optionVal.$qcode."_option' style='".$displayFlag."border: 2px solid;cursor:pointer;width:45px;padding-left:4px;padding-right:4px;text-align: center;font-size:14px'>"." ".round(($optionCount/$totalAttempt)*100,1)." %</div>";
	
			$answerwiseData[$optionVal] = $a;
		}
    
    	$questionStr .= "<table width='98%' border='0' cellpadding='3'>";

	    if($question_type=='MCQ-4' || $question_type=='MCQ-2')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
		
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='43%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	             if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				 if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='42%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-4')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td  id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='43%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	            $questionStr .= "<td id='td_4".$qcode."' width='5%'";
	            if($mostCommonWrong == 'D')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'D')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>D</strong>. </td><td align='left' width='42%'>".$question->getOptionD()." ".$answerwiseData['D']."</td>";
	        $questionStr .= "</tr>";
	    }
	    if($question_type=='MCQ-3')	{
	        $questionStr .= "<tr>";
	            $questionStr .= "<td id='td_1".$qcode."' width='5%'";
	            if($mostCommonWrong == 'A')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'A')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>A</strong>. </td><td align='left' width='28%'>".$question->getOptionA()." ".$answerwiseData['A']."</td>";
	            $questionStr .= "<td id='td_2".$qcode."' width='5%'";
	            if($mostCommonWrong == 'B')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'B')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>B</strong>. </td><td align='left' width='28%'>".$question->getOptionB()." ".$answerwiseData['B']."</td>";
	            $questionStr .= "<td id='td_3".$qcode."' width='5%'";
	            if($mostCommonWrong == 'C')
					$questionStr .= "bgcolor = '#e75903' title='Most common wrong answer'";
				if($correctAns== 'C')
					$questionStr .= "bgcolor = '#9ec955' title='Correct answer'";
	            $questionStr .= "><strong>C</strong>. </td><td align='left' width='28%'>".$question->getOptionC()." ".$answerwiseData['C']."</td>";
	        $questionStr .= "</tr>";
	    }
	    $questionStr .= "</table>";
    }
	
	if($question_type=='MCQ-4' || $question_type=='MCQ-2' || $question_type=='MCQ-3')	
	{
		$str = 'optionPercentage_'.$qcode;
		$questionStr .= '<br>';
		if($cwaFlag==1)
			$questionStr .= "<div id ='divAnswerPercentage".$str."' style='width: 170px;padding: 2px;cursor:pointer;background-color: #cbcaca;border-radius: 19px;box-shadow:2px 4px 2px #888888;font-size:14px;text-align:center;' onclick=showAnswerPercentage('".$str."',".$qcode.")>+ Option-wise performance</div>";
	}
	
    if($question->hasExplanation())
    {
    	$questionStr .= "<br/><span class='title'>Answer</span>: ";
    	if ($question_type=="Blank")
    		$questionStr .= $question->getCorrectAnswerForDisplay()."<br/>";
    	else
    		$questionStr .= "<br/>";
   		$questionStr .= $question->getDisplayAnswer()."<br/>";
    }
    elseif ($question_type=="Blank")
		$questionStr .= "<br/><span class='title'> Answer</span>: ".$question->getCorrectAnswerForDisplay()."<br/>";
	
	$showMostCommonWrongAns = 1;
	
	if($question_type!='I' && $question_type!='MCQ-4' && $question_type!='MCQ-2' && $question_type!='MCQ-3' && $question_type!='D')
	{
		$questionStr .= "<br><div id='cwa_$qcode' class='cwa'></div><input type='hidden' value='".$qcode.'#'.$dynamic.'#'.$showMostCommonWrongAns.'#'.$question_type.'#'.$question->correctAnswer.'#'.$userIDStr."'>";
	}
    return $questionStr;
}

function getTopicInfo($class,$section,$ttCode,$schoolCode)
{
	$topicArray = array();
	$query = "SELECT a.teacherTopicCode, a.teacherTopicDesc, b.activationDate, b.deactivationDate, b.flow
	          FROM   adepts_teacherTopicMaster a, adepts_teacherTopicActivation b
		      WHERE  a.teacherTopicCode=b.teacherTopicCode AND subjectno=".SUBJECTNO." AND b.schoolCode=$schoolCode AND b.class=$class AND a.teacherTopicCode='$ttCode'";
	if($section!="")
	{			
		$query .= " AND section in ('$section')";
	}		
	$query .= " order by b.srno DESC limit 1";			      	     
	$topic_result = mysql_query($query);
	if($topic_line=mysql_fetch_array($topic_result))
	{			
		$topicArray['name'] = $topic_line['teacherTopicDesc'];			
		if(strtotime($topic_line['deactivationDate']) < strtotime($topic_line['activationDate']))
		{				
			$days = getDaysTillActivated($topic_line['activationDate']);
			$topicArray['active'] = 1;
		}
		else
		{								
			$days = getDaysTillActivatedDeactive($topic_line['activationDate'],$topic_line['deactivationDate']);
			$topicArray['active'] = 0;						
		}
		$topicArray['days'] = $days;
		$topicArray['activationDate'] = $topic_line['activationDate'] ;
		$topicArray['deactivationDate'] = $topic_line['deactivationDate'] ;
		$topicArray['flow'] = $topic_line['flow'] ;
	}		
	return $topicArray;	
}

function getClusterDetailsAttemptedWithinDateRange($userIDstr, $startDate, $endDate, $class)
{
	$topicClusterArr = array();                    
    $query = "SELECT DISTINCT clusterCode, teacherTopicCode FROM ".TBL_QUES_ATTEMPT."_class$class  WHERE DATE(attemptedDate) >= '".$startDate."' AND DATE(attemptedDate) <= '".$endDate."' AND userID IN (".$userIDstr.") ";                                                  
	$result = mysql_query($query) or die(mysql_errno());        	
	while($line=mysql_fetch_array($result)) {
		 $topicClusterArr[$line['teacherTopicCode']][$line['clusterCode']] = getClusterName($line['clusterCode']);
	}           
	return $topicClusterArr;
}
function getAccuracyForClusters($ctArr,$class,$userIDstr)
{
	$clusterAccuracyArray = array();
	foreach($ctArr as $ttCode => $clusters)
	{							
		$clusterAccuracyArray[$ttCode]['name'] = getTopicName($ttCode);				
		$clusterAccuracyArray[$ttCode]['clusterDetails'] = getLearningUnitSummary($ctArr[$ttCode],$userIDstr,$class,$ttCode);
	}				
	return $clusterAccuracyArray;
}
function assessmentQuesAccuracySummary($assessmentDetails)
{
	$arrayAccuracyDetails['critical'] = $arrayAccuracyDetails['recommended'] = $arrayAccuracyDetails['performed']= array();	
	foreach($assessmentDetails as $userID=>$otherDetails)
	{
		if($otherDetails['accuracy']<40)
			$arrayAccuracyDetails['critical'][$userID] = $otherDetails;
		else if($otherDetails['accuracy']>=40 && $otherDetails['accuracy']<80)
			$arrayAccuracyDetails['recommended'][$userID] = $otherDetails;
		else
			$arrayAccuracyDetails['performed'][$userID] = $otherDetails;	
	}
	return $arrayAccuracyDetails;
}

function getMisconceptionStatementForQcode($qcode)
{
	$misconceptionStatement = "";
	$sq = "SELECT B.description FROM adepts_questions A, educatio_educat.misconception_master B WHERE A.misconception=id AND qcode=$qcode";
	$rs = mysql_query($sq);
	if($rw = mysql_fetch_array($rs))
	{
		$misconceptionStatement = $rw[0];		
	}
	return $misconceptionStatement;
}

function getQuestionsForDiscussion($ttCode,$userIDstr)
{
	$animatedQuestions=0;
	$questionsForDiscussionDetails =  array();
	$query = "SELECT GROUP_CONCAT(a.srno),SUM(b.status),count(b.attemptID),GROUP_CONCAT(b.attemptID) from adepts_comprehensiveModuleAttempt a JOIN adepts_diagnosticTestAttempts b ON a.srno=b.srno  JOIN adepts_diagnosticTestMaster d ON d.diagnosticTestID=b.diagnosticTestID JOIN adepts_teacherTopicStatus e ON e.ttAttemptID=b.ttAttemptID  where d.testType ='Assessment' and e.teacherTopicCode='$ttCode' and a.userID IN($userIDstr) GROUP BY a.ttAttemptID";		
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		if($row[1] == $row[2])
		{
			$attemptID[] = $row[0];
		}
	}
		$attemptIDStr = implode(',', $attemptID);	
		$sq = "SELECT c.qcode,(SUM(c.R)/count(c.srno))*100 as accuracy, e.question_type,GROUP_CONCAT(c.attemptID) from  adepts_diagnosticQuestionAttempt c JOIN adepts_diagnosticTestQuestions e ON c.qcode=e.qcode where c.attemptID IN($attemptIDStr) GROUP BY c.qcode";			
		$rs = mysql_query($sq);
		while($ln = mysql_fetch_array($rs))
		{													
			$assessmentQArray[$ln[0]]['accuracy']= $ln[1];
			$assessmentQArray[$ln[0]]['attemptID'] = $ln[3];
			if($ln[2] == 'I')
			{
				$animatedQuestions++;
			}	
			$qcodeStrForDownload['DTtest'][]=$ln[0];
		}
		$arrayAccuracyDetails = assessmentQuesAccuracySummary($assessmentQArray);
		foreach ($arrayAccuracyDetails as $accuracyKey => $range) {
			$index =0;
			$cwaDetails[$accuracyKey] = array();
			foreach ($range as $key => $value) {
				$cwaDetails[$accuracyKey][$index]['accuracy'] = ROUND($value['accuracy'],1);
				$cwaDetails[$accuracyKey][$index]['mode'] = 'getDiagnosticQuestion';
				$cwaDetails[$accuracyKey][$index]['qcodeListData']=$key."~".$userIDstr."~".$value['attemptID'];

				$index++;
			}
		}
	
	$questionsForDiscussionDetails['cwaDetails'] = $cwaDetails;				
	$questionsForDiscussionDetails['downloadStr']	=  json_encode($qcodeStrForDownload);
	$questionsForDiscussionDetails['animatedQuestions'] = $animatedQuestions;
	return $questionsForDiscussionDetails;
}

function getCommonWrongAnswer($clusters,$ttCode,$userIDstr,$class,$section,$userIDs)
{
	$k=0;			
	$noofsdls = $animatedQuestions = 0;
	$cwaQuesDetails = $cwaDTDetails = array();									
	$minUsers = ROUND(count($userIDs)*25/100);		
	$qcodeStrForDownload = array();			
	/* Fetched all the SDLs of the clusters */
	foreach ($clusters as $val)
	{				

		$query = "SELECT a.clusterCode, subdifficultylevel,(SUM(R)/count(srno))*100 as accuracy ,group_concat(distinct q.qcode) as qcodes,GROUP_CONCAT(flag) as flag,SUM(R) as correct,count(srno) as total FROM  ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q, ".TBL_CLUSTER_STATUS." cs, ".TBL_TOPIC_STATUS." ts
				  WHERE a.clusterAttemptID = cs.clusterAttemptID AND
						cs.userID = ts.userID AND
						ts.userID IN ($userIDstr) AND
						a.qcode = q.qcode AND
						cs.ttAttemptID = ts.ttAttemptID AND
						cs.clusterCode='$val' AND
						ts.teacherTopicCode = '$ttCode'
				 GROUP BY subdifficultylevel,a.userID having accuracy<50 order by accuracy ASC";	

		$sdl_result = mysql_query($query);				
		while($sdl_row = mysql_fetch_array($sdl_result))
		{										
			$commonWrongAns[$noofsdls]['sdl'] = $sdl_row[1];
			$commonWrongAns[$noofsdls]['clusterCode'] = $sdl_row[0];
			$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['noOfStudents'] += 1; 
			$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['qcodes'][] = explode(',',$sdl_row[3]);
			$commonWrongAns[$sdl_row[0]][$sdl_row[1]]['flag'][] = $sdl_row[4];
			$commonWrongAnsAccuracy[$noofsdls] = $sdl_row[2];																									
			$noofsdls++;													
		}				
	}			
	if($noofsdls > 0)
	{		
		$wrongClusters = array();
		foreach($commonWrongAnsAccuracy as $key => $SDLAccuracy)
		{				
			$clusterCode = $commonWrongAns[$key]['clusterCode'];
			$sdl = $commonWrongAns[$key]['sdl'];				
			$wrongClusters[$clusterCode][$sdl]['noOfStudents']=$commonWrongAns[$clusterCode][$sdl]['noOfStudents'];
			// remove blank values into array for flag field 
			$wrongClusters[$clusterCode][$sdl]['flag']=implode(',',array_filter($commonWrongAns[$clusterCode][$sdl]['flag']));
			// convert two dimensional array to one dimensional array and pic unique qcodes
			$wrongClusters[$clusterCode][$sdl]['qcodes']=implode(',',array_unique(array_reduce($commonWrongAns[$clusterCode][$sdl]['qcodes'], 'array_merge', array())));					
		}							
		$finalSDLClusterArray = array();     // Contain Cat1 and Cat2 clusters
		$finalSDLArray = array();            // Contain Cat1 and Cat2 SDLs
		$finalSDLQuesArray = array();		 // Contain the questions of that SDL							
		$finalSDLPerClassArray = array();				
		$finalSDLPerSchoolArray = array();
		$finalSDLPerNationalArray = array();				
		$harderSDLnum = 0;
		$harderSDLCat2num = 0;
		foreach($wrongClusters as $cluster=>$sdls)
		{	
			foreach($sdls as $sdl=>$sdlsValue)
			{
				if($sdlsValue['flag'] !='' && $sdlsValue['noOfStudents'] > $minUsers && $harderSDLnum < 10)
				{
					array_push($finalSDLClusterArray, $cluster);
					array_push($finalSDLArray,$sdl);
					array_push($finalSDLQuesArray,$sdlsValue['qcodes']);								
					array_push($finalSDLPerSchoolArray, getSchoolAvg($schoolCode,$cluster,$sdl,$class));
					array_push($finalSDLPerNationalArray, getNationalAvg($cluster,$sdl,$class));						
					$harderSDLnum++;
				}
			}
			if($harderSDLnum < 10)
			{	
				foreach($sdls as $sdl=>$sdlsValue)
				{
					if($sdlsValue['flag'] =='' && $sdlsValue['noOfStudents'] > $minUsers && $harderSDLCat2num < (10-$harderSDLnum))
					{
						array_push($finalSDLClusterArray, $cluster);
						array_push($finalSDLArray,$sdl);
						array_push($finalSDLQuesArray,$sdlsValue['qcodes']);									
						array_push($finalSDLPerSchoolArray, getSchoolAvg($schoolCode,$cluster,$sdl,$class));
						array_push($finalSDLPerNationalArray, getNationalAvg($cluster,$sdl,$class));						
						$harderSDLCat2num++;
					}
				}	
			}	
		}
		$countOfQueNo =  count($finalSDLClusterArray);													
		$jCnt = 0;
		foreach ($finalSDLClusterArray as $i => $value)
		{

			$currentTempCluster = $finalSDLClusterArray[$i];
			$currentTempSDL = $finalSDLArray[$i];
			$currentTempSDLQues = $finalSDLQuesArray[$i];
			$SDLQuesArray = array();
			$SDLQuesArray = explode(',',$currentTempSDLQues);
			if($currentTempSDL=="")		//For practice cluster, sdl will be blank - currently ignore such questions
				continue;
			$SDLsrno = 1;							
			$clusterAtttempt_query = "SELECT clusterAttemptID FROM ".TBL_TOPIC_STATUS." a, ".TBL_CLUSTER_STATUS." b WHERE a.ttAttemptID=b.ttAttemptID AND a.userID in ($userIDstr) AND teacherTopicCode='$ttCode' AND clusterCode='$currentTempCluster'";
			$clusterAttempt_result = mysql_query($clusterAtttempt_query);
			$clusterAttemptStr  = "";
			while ($clusterAttempt_line = mysql_fetch_array($clusterAttempt_result))
			   $clusterAttemptStr .= $clusterAttempt_line[0].",";
			$clusterAttemptStr = substr($clusterAttemptStr,0,-1);

			$student_name_array = array();
			$neverRightStudent = 0;
			if($clusterAttemptStr!="")
			{
				$student_name_query = "SELECT u.userID, childName, childClass, childSection, sum(R), count(srno) as cnt
									   FROM   adepts_userDetails u, ".TBL_QUES_ATTEMPT."_class$class a, adepts_questions q
									   WHERE  u.userID IN ($userIDstr) AND
											  a.userID= u.userID       AND
											  a.clusterAttemptID in ($clusterAttemptStr) AND
											  q.clusterCode='$currentTempCluster' AND
											  q.subdifficultylevel=$currentTempSDL AND
											  a.clusterCode = q.clusterCode AND
											  q.qcode = a.qcode";
				if (isset($section) && $section!="")
				{
					$student_name_query .= " AND childSection = '$section'";
				}
				$student_name_query .= " GROUP BY u.userID";										
				$student_name_result = mysql_query($student_name_query);
				$total_count = mysql_num_rows($student_name_result);

				while($student_name_data=mysql_fetch_array($student_name_result))
				{
					if ($student_name_data['sum(R)']==0 && $student_name_data['cnt'] >= 3)  //Show only the list of students who have not got it correct even once
					{								
						$student_name_array[] = $student_name_data['childName'];
						$neverRightStudent++;
					}
				}					
			}					
			$leastPerformancequery = "SELECT (sum(R)/COUNT(srno))*100 as accr , a.qcode ,b.question_type FROM ".TBL_QUES_ATTEMPT."_class$class a JOIN adepts_questions b ON a.qcode=b.qcode WHERE a.userID IN ($userIDstr) AND a.qcode in ($currentTempSDLQues) 
										AND teacherTopicCode='$ttCode' group by a.qcode order by accr";																			
			$Qcode_result = mysql_query($leastPerformancequery);
			$qcodeList = mysql_fetch_row($Qcode_result);
			
			$leastPerformedQcode = $qcodeList[1];
			$classAccuracy = $qcodeList[0];
			$cwaQuesDetails[$jCnt]["mode"] = "getQuestion";
			$cwaQuesDetails[$jCnt]["qcodeListData"] = $leastPerformedQcode."~".$schoolCode."~".$class."~".$section."~".$SDLsrno."~".$userIDstr."~".$ttCode;	
		$cwaQuesDetails[$jCnt]["misconception"] = strip_tags(getMisconceptionStatementForQcode($leastPerformedQcode));							
			$cwaQuesDetails[$jCnt]["nationalAVG"] = round($finalSDLPerNationalArray[$i],1);
			$cwaQuesDetails[$jCnt]["schoolAVG"] = round($finalSDLPerSchoolArray[$i],1);
			$cwaQuesDetails[$jCnt]["classWisePerformance"] = round($classAccuracy,1);
			$cwaQuesDetails[$jCnt]["failedStudentList"] = $student_name_array;
			$qcodeStrForDownload['topic'][] =$leastPerformedQcode;
			if($qcodeList[2] == 'I')
				$animatedQuestions++;

			$SDLsrno = $SDLsrno + 1;
			$jCnt++;
		}
	}
	if($harderSDLnum+$harderSDLCat2num <10)
	{
		$harderDTnum=0;
		$halfStudents = ROUND(count($userIDs)*50/100);			
		//fetching ttattemptIds of teacher topic code				
		$query = "SELECT GROUP_CONCAT(a.ttAttemptID) from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN($userIDstr)";			
		$result = mysql_query($query);		
		if($line=mysql_fetch_array($result))
		{					
			// fetching all qcodes of ttattempt id order by accuracy
			$dtQuery = "SELECT a.qcode,(SUM(a.R)/count(a.srno))*100 as accuracy,count(DISTINCT a.userID) as noOfUsers,d.question_type,GROUP_CONCAT(a.attemptID) from adepts_diagnosticQuestionAttempt a JOIN adepts_diagnosticTestMaster b ON a.diagnosticTestID=b.diagnosticTestID JOIN adepts_diagnosticTestAttempts c ON a.attemptID=c.srno JOIN adepts_diagnosticTestQuestions d ON a.qcode=d.qcode where b.testType='Prerequisite' and c.ttAttemptID IN($line[0]) group by a.qcode order by accuracy ASC";								
			$dtResult = mysql_query($dtQuery);							
			while($dtLine = mysql_fetch_array($dtResult))
			{														
				if(ROUND($dtLine[1])<=40 && $dtLine[2]>$halfStudents && $harderDTnum < (10-($harderSDLnum+$harderSDLCat2num)))
				{		
					// fetching all users who has given wrong answer for question						
					$stdQuery="SELECT GROUP_CONCAT(DISTINCT d.childName order by d.childName ASC) from adepts_diagnosticQuestionAttempt a JOIN adepts_diagnosticTestAttempts c ON a.attemptID=c.srno JOIN adepts_userDetails d ON d.userID=a.userID where a.qcode=$dtLine[0] and R=0 and c.ttAttemptID IN($line[0]) order by d.childName ASC";								
					$stdResult = mysql_query($stdQuery);
					$stdLine = mysql_fetch_row($stdResult);
					$cwaDTDetails[$harderDTnum]["mode"] = "getDiagnosticQuestion";
					$cwaDTDetails[$harderDTnum]["qcodeListData"] = $dtLine[0]."~".$userIDstr."~".$dtLine[4];
					$cwaDTDetails[$harderDTnum]["misconception"] = "";
					$cwaDTDetails[$harderDTnum]["nationalAVG"] = round(getSchoolNationalAvgDT('',$dtLine[0],$class),1);
					$cwaDTDetails[$harderDTnum]["schoolAVG"] = round(getSchoolNationalAvgDT($schoolCode,$dtLine[0],$class),1);
					$cwaDTDetails[$harderDTnum]["classWisePerformance"] = round($dtLine[1],1);
					$cwaDTDetails[$harderDTnum]["failedStudentList"] = explode(',',$stdLine[0]);
					$qcodeStrForDownload['DTtest'][] =$dtLine[0];
					if($dtLine[3] == 'I')
						$animatedQuestions++;
					$harderDTnum++;
				}																		
			}											
		}
	}															
								
	$cwaDetails['cwaDetails'] = array_merge($cwaQuesDetails,$cwaDTDetails);
	$cwaDetails['downloadStr']	=  json_encode($qcodeStrForDownload);
	$cwaDetails['animatedQuestions'] = $animatedQuestions;	
	return $cwaDetails;				
}
?>