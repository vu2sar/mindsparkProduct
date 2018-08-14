<?php

//$currentSessionString	=	currentSession($userID);
function getCurrentAccuracy($userID,$childClass,$ttCode,$currentSessionString)
{
	$sqTopic	=	"SELECT SUM(R),COUNT(srno) FROM adepts_teacherTopicQuesAttempt_class$childClass WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rsTopic	=	mysql_query($sqTopic);
	$rwTopic	=	mysql_fetch_array($rsTopic);
	$arrAccuracyDetails["attempts"] = $rwTopic[1];
	$arrAccuracyDetails["accuracy"] = round(($rwTopic[0]/$rwTopic[1])*100,1);
	return $arrAccuracyDetails;
}

function loggedInStatus($userID,$currentSessionString){
	$sq1	=	"SELECT logout_flag from adepts_sessionStatus where userID=".$userID." AND sessionID IN ($currentSessionString) order by sessionID desc limit 1";
	$rs1	=	mysql_query($sq1);
	if($rw1=mysql_fetch_array($rs1)){
		$logout_flag = $rw1['logout_flag'];
	}else{
		$logout_flag =0;
	}
	if($logout_flag==0){
	$sq	=	"SELECT b.userID,childClass,childSection,time_to_sec(timediff(now(),starttime))/60 minlogged,schoolCode FROM adepts_sessionStatus a,adepts_userDetails b 
			 WHERE logout_flag=0 AND a.userid=b.userid AND category='STUDENT' AND b.userID=".$userID." AND startTime_int=".date("Ymd")." 
			 HAVING minlogged<45";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$loggedInFlag = 1;
	}else{
		$loggedInFlag = 0;
	}
		
	}else{
		$loggedInFlag=0;
	}
	return $loggedInFlag;
}

function getTeacherTopic($userID){
	$sq1	=	"SELECT teacherTopicDesc,cs.teacherTopicCode as teacherTopicCode, cs.clusterCode as clusterCode FROM adepts_ttUserCurrentStatus cs left join adepts_teacherTopicMaster tm on tm.teacherTopicCode=cs.teacherTopicCode
			 WHERE status=1 and cs.userID=".$userID;
	$rs1	=	mysql_query($sq1);
	if($rw1=mysql_fetch_array($rs1))
	{
		 $teacherTopicDesc[0] = $rw1[0];
		 $teacherTopicDesc[1] = $rw1[1];
		 $teacherTopicDesc[2] = $rw1[2];
	}else{
		 $teacherTopicDesc[0] = "-";
	}
	return $teacherTopicDesc;
}

function idleUser($userID,$currentSessionString)
{
	$arrLastModified	=	array();
	$sq	=	"SELECT MAX(lastModified) FROM adepts_ttUserCurrentStatus WHERE userID=$userID 
			 UNION SELECT MAX(lastModified) FROM adepts_researchQuesAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString) 
			 UNION SELECT MAX(lastModified) FROM adepts_diagnosticQuestionAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_revisionSessionDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_topicRevisionDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_competitiveExamQuesAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_bucketClusterAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_remedialItemAttempts WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_userGameDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_ncertQuesAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM practiseModulesQuestionAttemptDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM practiseModulesTimedTestAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_timedTestDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arrLastModified[]	=	strtotime($rw[0]);
	}
	rsort($arrLastModified);
	return $arrLastModified;
}

function doingQuestions($userID,$currentSessionString,$class){
	$sq = "SELECT MAX(lastModified) FROM adepts_teacherTopicQuesAttempt_class$class WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}

function doingRevisionSession($userID,$currentSessionString){
	$sq = "SELECT MAX(lastModified) FROM adepts_revisionSessionDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}

function doingTopicPractice($userID,$currentSessionString){
	$sq = "SELECT MAX(lastModified) FROM adepts_topicRevisionDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}
function doingPracticeModules($userID,$currentSessionString){
	$sq = "SELECT MAX(lM) FROM (SELECT MAX(lastModified) lM FROM practiseModulesTimedTestAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)
			UNION SELECT MAX(lastModified) lM FROM practiseModulesQuestionAttemptDetails WHERE userID=$userID AND sessionID IN ($currentSessionString))";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}

function doingCompetetiveExam($userID,$currentSessionString){
	$sq = "SELECT MAX(lastModified) FROM adepts_competitiveExamQuesAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}

function doingImproveConcepts($userID,$currentSessionString){
	$sq = "SELECT MAX(lastModified) FROM adepts_bucketClusterAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}

function ncertQuestionAttempt($userID,$currentSessionString){
	$sq = "SELECT MAX(lastModified) FROM adepts_ncertQuesAttempt WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$lastModified	=	strtotime($rw[0]);
	}
	return $lastModified;
}

function activityAttempt($userID,$currentSessionString){
	$sq = "SELECT MAX(lastModified) FROM adepts_remedialItemAttempts WHERE userID=$userID AND sessionID IN ($currentSessionString)
			 UNION SELECT MAX(lastModified) FROM adepts_userGameDetails WHERE userID=$userID AND sessionID IN ($currentSessionString)";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arrLastModified[]	=	strtotime($rw[0]);
	}
	rsort($arrLastModified);
	return $arrLastModified[0];
}



function currentSession($userID)
{
	$arrAccuracyDetails	=	array();
	$currentDate	=	date("Ymd");
	$sqSession	=	"SELECT GROUP_CONCAT(sessionID) FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int=$currentDate";
	$rsSession	=	mysql_query($sqSession);
	$rwSession	=	mysql_fetch_array($rsSession);
	return $rwSession[0];
}

?>