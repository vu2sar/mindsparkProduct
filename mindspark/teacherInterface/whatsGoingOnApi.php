<?php 

	include("header.php");
	include("../slave_connectivity.php");
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	
	include("functions/functions.php");
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
	// echo $userIDstr;
	$q = "SELECT DISTINCT flow FROM ".TBL_TOPIC_STATUS." WHERE  userID in (".$userIDstr.") AND teacherTopicCode='".$ttCode."'";	
    $r = mysql_query($q);   
    while($l = mysql_fetch_array($r))
    {
    	$flowArray[] = $l[0];
    }   
   	foreach($flowArray as $flow)
   	{   		
		$ttObj = new teacherTopic($ttCode,$cls,$flow);
		$clusterArray[] =	$ttObj->getClustersOfLevel($cls);
   	}
   	$clusters = array_unique(array_reduce($clusterArray, 'array_merge', array()));   
	$clusterString = implode("','", $clusters);
	//get cluster detail
	$clusterDetail =  getClusterDetails($clusterString);	
	$dailyPractiseArray = getDailyPractiseDetails($clusterString);
	//find most recent ttattemptIds of users who have completed topic
	$query= "SELECT MAX(a.ttAttemptID) as ttAttemptID,a.userID from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and a.userID IN ($userIDstr) and (a.classLevelResult IN('SUCCESS','FAILURE') OR a.result IN('SUCCESS','FAILURE')) GROUP BY a.userID";
	$result =  mysql_query($query);
	while($line = mysql_fetch_array($result))
	{
		$ttAttemptID[] = $line[0];		
	}
	$ttAttemptIDStr = implode(',', $ttAttemptID);
	$positiveArray= $positiveOldArray = array();
	$negativeArray= $negativeOldArray = array();
	$positiveLimit = $negativeLimit=5;

	$negativeOldArray = getFailedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$negativeLimit);	
	if(!empty($negativeOldArray))
		array_push($negativeArray,$negativeOldArray);	
	$negativeLimit = $negativeLimit - count($negativeArray) ;
	if($negativeLimit !=0)
	{
		$negativeOldArray = getLessAccuracyCluster($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$negativeLimit);
		if(!empty($negativeOldArray))
			array_push($negativeArray,$negativeOldArray);
		$negativeLimit = $negativeLimit - count($negativeArray) ;
		if($negativeLimit !=0)
		{			
			$negativeOldArray = getLessAccuracyDailyPractice($clusterString,$userIDstr,$userDetails,$dailyPractiseArray,$negativeLimit);
			if(!empty($negativeOldArray))
				array_push($negativeArray,$negativeOldArray);			
		}
	}	
	$positiveOldArray = getPassedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$positiveLimit);
	if(!empty($positiveOldArray))
		array_push($positiveArray,$positiveOldArray);
	$positiveLimit = $positiveLimit - count($positiveArray) ;
	if($positiveLimit !=0)
	{
		$positiveOldArray = getDiagnostictTestDetails($clusterString,$ttCode,$userIDstr);
		if(!empty($positiveOldArray))
			array_push($positiveArray,$positiveOldArray);
		$positiveLimit = $positiveLimit - count($positiveArray);
		if($positiveLimit !=0)
		{			
			$needMinUsers = ROUND(count($userIDs)/2);
			$positiveOldArray = getMoreAccuracyDailyPractice($clusterString,$userIDstr,$dailyPractiseArray,$needMinUsers,$positiveLimit);
			if(!empty($positiveOldArray))
				array_push($positiveArray,$positiveOldArray);			
			$positiveLimit = $positiveLimit - count($positiveArray);
			if($positiveLimit !=0)
			{
				$positiveOldArray = getFirstStudent($ttCode,$userIDstr,$userDetails);
				if(!empty($positiveOldArray))
					array_push($positiveArray,$positiveOldArray);					
			}
		}
	}	
	echo "<pre>";
	print_r($negativeArray);
	echo "<pre>";
	print_r($positiveArray);
	function getFailedClusterDetails($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$limit)
	{
		$failedClusterArray = $failedClusterDetails = array();
		$fcQuery= "SELECT a.clusterCode,count(a.clusterAttemptID) as failureCount,a.userID,date(MAX(a.lastModified)) as lastAttemptDate from adepts_teacherTopicClusterStatus a where a.result='FAILURE' and a.ttAttemptID IN($ttAttemptIDStr) and a.clusterCode IN('$clusterString') GROUP BY a.clusterCode,a.userID having failureCount>1 and DATEDIFF(CURDATE(),lastAttemptDate)<15 order by failureCount DESC";
		// echo $fcQuery;
		$fcResult = mysql_query($fcQuery);
		while($fcLine = mysql_fetch_array($fcResult))
		{
			$failedClusterArray[$fcLine[0]]['students'][] = $userDetails[$fcLine[2]][0];
			$failedClusterArray[$fcLine[0]]['description'] = $clusterDetail[$fcLine[0]];
		}	
		// echo "<pre>";
		// print_r($failedClusterArray);
		if(!empty($failedClusterArray))
		{			
			$message = ' immediate help with ';	
			$failedClusterArray = array_slice($failedClusterArray,0,$limit-1,true);			
			$failedClusterDetails = make_message($failedClusterArray,$message,'sampleQuestion');
		}		
		// echo "<pre>";
		// print_r($failedClusterDetails);
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
		// echo "<pre>";
		// print_r($passedClusterArray);
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
				$passedClusterDetails[$i][] = "sampleQuestion";
				$passedClusterDetails[$i][] = $clusterCode;
				$passedClusterDetails[$i][] = $studentNames.$message.$clusterValue['description'].".";
				$i++;
			}		
		}		
		// echo "<pre>";
		// print_r($passedClusterDetails);
		return $passedClusterDetails;
	}
	function getLessAccuracyCluster($ttAttemptIDStr,$userDetails,$clusterString,$clusterDetail,$limit)
	{	
		$lessAccuracyClusterArray = $lessAccuracyClusterDetails = array();
		$accQuery = "SELECT clusterCode,userID,perCorrect from adepts_teacherTopicClusterStatus where clusterAttemptID IN(SELECT MAX(a.clusterAttemptID) from adepts_teacherTopicClusterStatus a where a.result='SUCCESS' and a.ttAttemptID IN($ttAttemptIDStr) and a.clusterCode IN('$clusterString') group by a.clusterCode,a.userID ) having perCorrect<40 order by perCorrect ASC";
		$accResult = mysql_query($accQuery);
		while($accLine = mysql_fetch_array($accResult))
		{
			$lessAccuracyClusterArray[$accLine[0]]['students'][]=$userDetails[$accLine[1]][0];
			$lessAccuracyClusterArray[$accLine[0]]['description'] = $clusterDetail[$accLine[0]];
		}
		// echo "<pre>";
		// print_r($lessAccuracyClusterArray);
		if(!empty($lessAccuracyClusterArray))
		{			
			$message = ' concept clarity in ';
			$lessAccuracyClusterArray = array_slice($lessAccuracyClusterArray,0,$limit-1,true);				
			$lessAccuracyClusterDetails = make_message($lessAccuracyClusterArray,$message,'sampleQuestion');
		}	

		// echo "<pre>";
		// print_r($lessAccuracyClusterDetails) ;
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
		// echo "<pre>";
		// print_r($prerequisiteArray);
		
		if(count($prerequisiteArray)==1)
		{
			$dtArray[0][] = 'message';
			$dtArray[0][] = 'Mindspark revised one of the prerequisite concepts: '.$prerequisiteArray[0].'.';
			
		}			
		else
		{
			$dtArray[0][] = 'message';
			$dtArray[0][] = 'Mindspark revised'.$prerequisiteArray[0].' & '.$prerequisiteArray[1].'  from the prequisite concepts.';
			
		}			
		// echo "<br>";
		// echo $dtArray;
		return $dtArray;
	}
	function getLessAccuracyDailyPractice($clusterString,$userIDstr,$userDetails,$dailyPractiseArray,$limit)
	{		
		$lessAccuracyDPDetails = array();
		$accQuery = "SELECT ROUND((SUM(R)/COUNT(b.id))*100) as accuracy,b.practiseModuleId,b.userID from practiseModulesQuestionAttemptDetails b where b.practiseModuleTestStatusId IN(SELECT max(a.id) from practiseModulesTestStatus a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId  where a.`status`='completed' and b.linkedToCluster IN('$clusterString') and a.userID IN($userIDstr) group by a.practiseModuleId,a.userID) group by b.practiseModuleId,b.userID having accuracy < 50 order by accuracy ASC";
		// echo $accQuery;
		$accResult = mysql_query($accQuery);
		while($accLine = mysql_fetch_array($accResult))
		{
			$lessAccuracyArray[$accLine[1]]['students'][] = $userDetails[$accLine[2]][0];
			$lessAccuracyArray[$accLine[1]]['description'] = $dailyPractiseArray[$accLine[1]];
		}
		if(!empty($lessAccuracyArray))
		{
			$message = ' more practice in ';
			$lessAccuracyArray = array_slice($lessAccuracyArray,0,$limit-1,true);
			$lessAccuracyDPDetails = make_message($lessAccuracyArray,$message,'dailyPractice');			
		}
		// echo "<pre>";
		// print_r($lessAccuracyDPDetails);
		return $lessAccuracyDPDetails;
	}
	function getMoreAccuracyDailyPractice($clusterString,$userIDstr,$dailyPractiseArray,$needMinUsers,$limit)
	{		
		$moreAccuracyDPDetails = array();	
		$i=0;	
		$accQuery = "SELECT ROUND((SUM(R)/COUNT(b.id))*100) as accuracy,b.practiseModuleId,count(DISTINCT b.userID) as users from practiseModulesQuestionAttemptDetails b where b.practiseModuleTestStatusId IN(SELECT max(a.id) from practiseModulesTestStatus a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId  where a.`status`='completed' and b.linkedToCluster IN('$clusterString') and a.userID IN($userIDstr) group by a.practiseModuleId,a.userID) group by b.practiseModuleId having users>$needMinUsers and accuracy>75 limit $limit";
		// echo $accQuery;
		$accResult = mysql_query($accQuery);
		while($accLine = mysql_fetch_array($accResult))
		{			
			$moreAccuracyArray[$accLine[1]] = $dailyPractiseArray[$accLine[1]];
		}
		if(!empty($moreAccuracyArray))
		{
			foreach($moreAccuracyArray as $key=> $value)
			{
				$moreAccuracyDPDetails[$i][] = "dailyPractice";
				$moreAccuracyDPDetails[$i][] = $key;
				$moreAccuracyDPDetails[$i][] = "Daily practice on Conversion in ".$value." has helped the class to build fluency.";
				$i++;
			}				
		}
		// echo "<pre>";
		// print_r($moreAccuracyDPDetails);
		return $moreAccuracyDPDetails;
	}
	function getFirstStudent($ttCode,$userIDstr,$userDetails)
	{
		$firstStudentArray = array();
		$fsQuery = "SELECT a.userID from adepts_teacherTopicStatus a where a.teacherTopicCode='$ttCode' and (a.result IN('SUCCESS','FAILURE') OR a.classLevelResult IN('SUCCESS','FAILURE')) and a.userID IN($userIDstr) limit 1";
		 // echo $fsQuery;
		$fcResult = mysql_query($fsQuery);
		if($fcLine = mysql_fetch_row($fcResult))
			$username = $userDetails[$fcLine[0]][0];

		if($username != '')
		{
			$firstStudentArray[0][] = "message";
			$firstStudentArray[0][] = $username." is the first one to complete the topic.";
		}
		// echo "<br>";
		// echo $firstStudentArray;
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
			$messageArray[$i][] = $type;
			$messageArray[$i][] = $clusterCode;
			$messageArray[$i][] = $studentNames.$message.$clusterValue['description'].".";
			$i++;
		}

		return $messageArray;
	}
	function getClusterDetails($clusterString)
	{
		$clusterArrayDetails = array();
		$clQuery = "SELECT clusterCode,cluster from adepts_clusterMaster where clusterCode IN('$clusterString')";			
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
		$dpQuery = "SELECT practiseModuleId,description from practiseModuleDetails where linkedToCluster IN('$clusterString')";		
		$dpResult = mysql_query($dpQuery);
		while($dpLine =  mysql_fetch_array($dpResult))
		{
			$dailyPractiseDetails[$dpLine[0]] = $dpLine[1];
		}
		return $dailyPractiseDetails;
	}
	// function getWrongQuestions($ttAttemptIDStr,$clusterString,$class,$minusers)
	// {
	// 	$misconceptionArray  = $misconceptionDetail = array();
	// 	$cwaQuery = "SELECT a.qcode,count(DISTINCT a.userID) as users,c.misconception from adepts_teacherTopicQuesAttempt_class$class a JOIN adepts_teacherTopicClusterStatus b ON a.clusterAttemptID=b.clusterAttemptID JOIN adepts_questions c ON a.qcode=c.qcode where b.ttAttemptID IN($ttAttemptIDStr) and b.clusterCode IN('$clusterString') and a.R=0 and c.misconception != '' and flag IN(2,3) GROUP BY a.qcode having users>$minusers";

	// 	// echo $cwaQuery;
	// 	$cwaResult = mysql_query($cwaQuery);
	// 	while($cwaLine = mysql_fetch_array($cwaResult))
	// 	{
	// 		$misconceptionArray[$cwaLine[0]] = $cwaLine[2];
	// 	}
	// 	print_r($misconceptionArray);
	// 	if(!empty($misconceptionArray))
	// 	{
	// 		foreach($misconceptionArray as $misconception)
	// 		{
	// 				$misArray[] = explode(',',$misconception);		
	// 		}
	// 	}
	// 	$uniqueMis = array_unique(array_reduce($misArray, 'array_merge', array()));
	// 	$uniqueMisString =  implode(',', $uniqueMis);
	// 	$misQuery = "SELECT description from educatio_educat.misconception_master where id IN($uniqueMisString)";
	// 	echo $misQuery;
	// 	$misResult = mysql_query($misQuery);
	// 	$message = 'Students in the class need your help in addressing the misconception/error: ';
	// 	while($misLine = mysql_fetch_array($misResult))
	// 	{
	// 		$misconceptionDetail[] = $message.$misLine[0];
	// 	}
	// 	echo "<pre>";
	// 	print_r($misconceptionDetail);
	// 	return $misconceptionDetail;
	// }
?>
