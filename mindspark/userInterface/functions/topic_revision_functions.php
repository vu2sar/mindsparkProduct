<?php
function initializeQuesArrayForTopicRevision($clusterArray)
{
	$quesAttemptedArray = array();
	$totalClusters = count($clusterArray);
	for($counter=0; $counter<$totalClusters; $counter++)
	{
		$quesAttemptedArray[$clusterArray[$counter]] = '';
	}
	return $quesAttemptedArray;
}

function findNextQuesForTopicRevision($clusterArray, $prevCluster, $quesNo)
{
	$qcodeArray  = array();
	$nextCluster = "";
	$totalClusters = count($clusterArray);
	$quesAttemptedArray = $_SESSION['quesAttempted'];
	$clustersRemaining = 0;
	for($counter=0; $counter<$totalClusters; $counter++)
	{
		if($quesAttemptedArray[$clusterArray[$counter]]=="")
			$clustersRemaining++;
		else
		{
			$noOfQuesAttempted = explode(",",$quesAttemptedArray[$clusterArray[$counter]]);
			if(count($noOfQuesAttempted) < 2)
				$clustersRemaining++;
		}
	}
	if(($clustersRemaining==0 && $quesNo>15) || $quesNo>40)
		return  "";
	if($prevCluster=="")	//First question
		$counter = 0;
	else
		for($counter=0; $counter<$totalClusters && $clusterArray[$counter]!=$prevCluster; $counter++);

	$clusterCode = $clusterArray[$counter];
	$quesGiven = $quesAttemptedArray[$clusterCode];
	if($quesGiven!="")
	{
		$qcodeArray = explode(",",$quesGiven);
		if(count($qcodeArray)%2==0)	//implies questions from current cluster are given, look for next cluster
		{
			$temp = count($qcodeArray)/2;
			if($temp==1)	//if 2 questions are given, then go to the next cluster if any
				$counter++;
			else
			{
				$counter--;
				if($counter<0)
					$counter = $totalClusters - 1;
			}
			if($counter==$totalClusters)
			{
				$counter = 0;
				$nextCluster = $clusterArray[$counter];
			}
			else
			{
				$nextCluster = $clusterArray[$counter];
				$quesGiven = $quesAttemptedArray[$nextCluster];
			}
		}
		else
			$nextCluster = $clusterCode;
	}
	else
	{
		$nextCluster = $clusterCode;
	}

	$qcode = getNextQuesForTopicRevision($nextCluster,$qcodeArray);
	if($qcode!="")
	{
		if($quesGiven=="")
			$qcodeStr = $qcode;
		else
			$qcodeStr = $quesGiven.",".$qcode;
		$_SESSION['quesAttempted'][$nextCluster] = $qcodeStr;
	}
	return $nextCluster."-".$qcode;
}

function getNextQuesForTopicRevision($clusterCode,$quesGivenArray)
{
	$questionArray = array();
	$sdlsChecked   = array();
	$qcode = "";
	$context            = isset($_SESSION['country'])?$_SESSION['country']:"India";
	$query  = "SELECT subdifficultylevel, group_concat(qcode) as qcodes FROM adepts_questions
	           WHERE clusterCode='$clusterCode'  AND status='3' AND context in ('Global','$context')
	           GROUP BY subdifficultylevel";
	$result = mysql_query($query) or die(mysql_error());
	while ($line = mysql_fetch_array($result))
	{
		$questionArray[$line['subdifficultylevel']] = $line['qcodes'];
	}
	$sdlArray = array_keys($questionArray);
	$totalSDLs = count($sdlArray);
	sort($sdlArray,SORT_NUMERIC);
	$startPosition = floor($totalSDLs*50/100);
	if($totalSDLs>0)
	{
		$i=0;
		do {
			$i++;
			$randomNo = mt_rand($startPosition,($totalSDLs-1));
			$sdl = $sdlArray[$randomNo];
			if(!in_array($sdl,$sdlsChecked))
				array_push($sdlsChecked,$sdl);
			$qcodeArray = array_diff(explode(",",$questionArray[$sdl]),$quesGivenArray);
			if(count($qcodeArray)>0)
			{
				$keysArray = array_keys($qcodeArray);
				$randomNo = mt_rand(0,(count($keysArray)-1));
				$qcode = $qcodeArray[$keysArray[$randomNo]];
			}
		}while ($qcode=="" && count($sdlsChecked)!=$totalSDLs && $i<=16);
	}
	return $qcode;
}

function getClusters($teacherTopicCode, $class, $userID)
{
	$clusterArray = array();
	$flow  = getFlowForTT($userID, $teacherTopicCode);
	$objTT = new teacherTopic($teacherTopicCode,$class,$flow);
	$startingLevel = $objTT->startingLevel;
	$clusterArray  = $objTT->getClustersOfLevel($startingLevel);

    //if more than 20 clusters, select 20 clusters at random since only max. 40 questions can be given
    if(count($clusterArray)>20)
    {
		$keys = array_rand($clusterArray, 20);
		$tmpClusterArray = array();
		foreach ($keys as $key)
		{
			array_push($tmpClusterArray, $clusterArray[$key]);
		}
		$clusterArray = $tmpClusterArray;
    }
    return $clusterArray;
}

function saveTopicRevisionQuesResponse($userID,$sessionID, $teacherTopicCode,$questionNo, $qcode,$response,$seconds,$responseResult, $dynamic, $dynamicParams)
{
	$query = "INSERT INTO ".TBL_TOPIC_REVISION."
                  (userID,attemptedDate,questionNo,qcode,A,S,R,sessionID, teacherTopicCode, lastModified)
              VALUES
             ($userID,'".date("Y-m-d")."','$questionNo',$qcode,'$response','$seconds',$responseResult,$sessionID,'$teacherTopicCode', '".date("Y-m-d H:i:s")."')";
    //echo $query;
    mysql_query($query) or die($query."");
    $quesAttempt_srno = mysql_insert_id();
    if($dynamic)
    {
      	$query = "INSERT INTO adepts_dynamicParameters (userID, qcode, quesAttempt_srno, parameters, mode, class, lastModified) VALUES
         				($userID, $qcode, ".$quesAttempt_srno.", '$dynamicParams', 'topicRevision', '".$_SESSION['childClass']."', '".date("Y-m-d H:i:s")."')";
       	mysql_query($query);
    }
}
function calculateRevisionSparkies($teacherTopicCode,$sessionID,$topicRevisionAttemptNo,$totalCorrect,$totalQues)
{
	$sqUpdate = "UPDATE teacherTopicRevisionStatus SET noOfQuesAttempted=$totalQues,totalCorrect=$totalCorrect,status='Completed' WHERE sessionID=$sessionID AND teacherTopicCode='$teacherTopicCode' AND attemptNo=$topicRevisionAttemptNo";
	mysql_query($sqUpdate) or die(mysql_error().$sqUpdate);
	$sparkie = 0;
	if($topicRevisionAttemptNo<=5)
	{
		$revisionAccuracy = ($totalCorrect/$totalQues)*100;	
		if($revisionAccuracy==100)
			$sparkie = 5;
		else if($revisionAccuracy<100 && $revisionAccuracy>=70)
			$sparkie = 3;
		else
			$sparkie = 2;
	}
	return $sparkie;
}

function updateRevisionQues($teacherTopicCode,$sessionID,$topicRevisionAttemptNo,$totalCorrect,$totalQues)
{
	$sqUpdate = "UPDATE teacherTopicRevisionStatus SET noOfQuesAttempted=$totalQues,totalCorrect=$totalCorrect WHERE sessionID=$sessionID AND teacherTopicCode='$teacherTopicCode' AND attemptNo=$topicRevisionAttemptNo";
	mysql_query($sqUpdate) or die(mysql_error().$sqUpdate);
}

function getTopicRevisionAttemptNo($teacherTopicCode,$userID,$sessionID)
{
	$attemptNo = 0;
	$sq = "SELECT COUNT(id) FROM teacherTopicRevisionStatus WHERE teacherTopicCode='$teacherTopicCode' AND userID=$userID";
	$rs = mysql_query($sq);
	$rw = mysql_fetch_array($rs);
	$attemptNo = $rw[0]+1;
		
	$sqInsert = "INSERT INTO teacherTopicRevisionStatus SET userID=$userID, teacherTopicCode='$teacherTopicCode',attemptNo=$attemptNo, sessionID=$sessionID";
	mysql_query($sqInsert);
	return $attemptNo;
}
?>