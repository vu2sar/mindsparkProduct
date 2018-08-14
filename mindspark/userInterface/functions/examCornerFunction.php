<?php
function getTopicAttempted($userID, $childClass, $childSection, $category, $subcategory, $schoolCode, $subjectno, $packageType="All")
{
	$sq	=	"SELECT DISTINCT A.teacherTopicCode,teacherTopicDesc,classification FROM ".TBL_TOPIC_STATUS." A, adepts_teacherTopicMaster B
			 WHERE userID=$userID AND A.teacherTopicCode=B.teacherTopicCode AND progress > 50 ORDER BY classification,teacherTopicDesc";
	$rs	=	mysql_query($sq);
	while ($rw=mysql_fetch_array($rs))
	{
		$teacherTopics[$rw[0]]["topicName"] = $rw[1];
		$teacherTopics[$rw[0]]["classification"] = $rw[2];
	}
	return $teacherTopics;
}

function getClusterForBucketing($userID,$ttCode)
{
	$sq	=	"SELECT clusterCode,perCorrect,lastModified from (SELECT B.clusterCode,B.perCorrect,B.lastModified FROM ".TBL_TOPIC_STATUS." A, ".TBL_CLUSTER_STATUS." B, adepts_clusterMaster C WHERE A.userID=$userID AND A.teacherTopicCode='$ttCode' AND A.ttAttemptID=B.ttAttemptID AND B.result<>'' AND B.clusterCode=C.clusterCode AND (C.clusterType NOT IN ('challenge', 'pre', 'post', 'practice') OR isNull(C.clusterType)) ORDER BY B.clusterAttemptID desc) dateSorted group by clusterCode";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$perCorrect	=	getClusterExamCorner($rw[0],$rw[2],$userID);
		$arrayClusterList[$rw[0]]	=	$rw[1];
		if($perCorrect!="")
			$arrayClusterList[$rw[0]]	=	$perCorrect;
	}
	return $arrayClusterList;
}

function getClusterExamCorner($clusterCode,$attemptDate,$userID)
{
	$perCorrect = "";
	$sq	=	"SELECT perCorrect FROM adepts_examcornerClusterStatus
			 WHERE userID=$userID AND clusterCode='$clusterCode' AND lastModified > '$attemptDate' AND result<>'' ORDER BY attemptID DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		$perCorrect	=	$rw[0];
	}
	return $perCorrect;
}

function bucketLogicCalculation($userID,$ttCode,$arrayClusterList)
{
	$arrayClusterBucketing	=	array();
	$bronzeBucket	=	array();
	$silverBucket	=	array();
	$goldBucket		=	array();
	foreach($arrayClusterList as $clusterCode=>$perCorrect)
	{
		if($perCorrect==100)
		{
			$goldBucket[]	=	$clusterCode;
			continue;
		}
		/*$sqB	=	"SELECT count(clusterCode) FROM ".TBL_CLUSTER_STATUS." WHERE clusterCode='$clusterCode' AND perCorrect < $perCorrect AND result<>''";
		$rsB	=	mysql_query($sqB);
		$rwB	=	mysql_fetch_array($rsB);
		if($rwB[0] > 0)
		{
			$perBelowUser	=	$rwB[0] + 1 ;
		}
		$sqT	=	"SELECT count(clusterCode) FROM ".TBL_CLUSTER_STATUS." WHERE clusterCode='$clusterCode' AND perCorrect < $perCorrect AND result<>''";
		$rsT	=	mysql_query($sqT);
		$rwT	=	mysql_query($rwT);
		$totalAttempt	=	$rwT[0];
		
		if($totalAttempt>0)
			$perCorrectNew	=	round(($perCorrect + (($perBelowUser/$totalAttempt) * 100 ))/2);
		else*/
			$perCorrectNew	=	round($perCorrect);
			
		if($perCorrectNew >= 85)
			$goldBucket[]	=	$clusterCode;
		else if($perCorrectNew >= 75 && $perCorrectNew < 85)
			$silverBucket[]	=	$clusterCode;
		else
			$bronzeBucket[]	=	$clusterCode;
	}
	$sq	=	"SELECT bronzeBucket,silverBucket,goldBucket FROM adepts_userBucketing WHERE userID=$userID AND teacherTopicCode='".$ttCode."' ORDER BY id DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	
	if($rw[0]!="")
		$bronzeBucketPrev	=	explode(",",$rw[0]);
	else
		$bronzeBucketPrev	=	array();
		
	if($rw[1]!="")
		$silverBucketPrev	=	explode(",",$rw[1]);
	else
		$silverBucketPrev	=	array();
		
	if($rw[2]!="")
		$goldBucketPrev		=	explode(",",$rw[2]);
	else
		$goldBucketPrev	=	array();
	
	if(count(array_intersect($bronzeBucketPrev,$bronzeBucket))==count($bronzeBucket) && count(array_intersect($silverBucketPrev,$silverBucket))==count($silverBucket) && count(array_intersect($goldBucketPrev,$goldBucket))==count($goldBucket))
	{
		$arrayClusterBucketing[0]	=	$bronzeBucketPrev;
		$arrayClusterBucketing[1]	=	$silverBucketPrev;
		$arrayClusterBucketing[2]	=	$goldBucketPrev;
	}
	else
	{
		$arrayClusterBucketing[0]	=	$bronzeBucket;
		$arrayClusterBucketing[1]	=	$silverBucket;
		$arrayClusterBucketing[2]	=	$goldBucket;
		$sqSave	=	"INSERT INTO adepts_userBucketing SET userID=$userID, teacherTopicCode='$ttCode', bronzeBucket='".implode(",",$bronzeBucket)."',
					 silverBucket='".implode(",",$silverBucket)."', goldBucket='".implode(",",$goldBucket)."'";
		$rsSave	=	mysql_query($sqSave);
	}
	return $arrayClusterBucketing;
}
function getMovedCluster($arrayClusterBucketing,$userID,$ttCode)
{
	$arrayMovedCluster	=	array();
	$sq	=	"SELECT bronzeBucket,silverBucket,goldBucket FROM adepts_userBucketing WHERE userID=$userID AND teacherTopicCode='".$ttCode."' ORDER BY id DESC LIMIT 1,1";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	
	$arrayMovedCluster[0]	=	array_intersect(explode(",",$rw[0]),$arrayClusterBucketing[1]); //bronze to silver
	$arrayMovedCluster[1]	=	array_intersect(explode(",",$rw[0]),$arrayClusterBucketing[2]); //bronze to gold
	$arrayMovedCluster[2]	=	array_intersect(explode(",",$rw[1]),$arrayClusterBucketing[0]); //silver to bronze
	$arrayMovedCluster[3]	=	array_intersect(explode(",",$rw[1]),$arrayClusterBucketing[2]); //silver to gold
	$arrayMovedCluster[4]	=	array_intersect(explode(",",$rw[2]),$arrayClusterBucketing[0]); //gold to bronze
	$arrayMovedCluster[5]	=	array_intersect(explode(",",$rw[2]),$arrayClusterBucketing[1]); //gold to silver
	return $arrayMovedCluster;
}

function getClusterDesc($clusterCode)
{
	$arrClusterDesc	=	array();
	$sq	=	"SELECT cluster,clusterCode FROM adepts_clusterMaster WHERE clusterCode IN ('".implode("','",$clusterCode)."') ORDER BY cluster";
	$rs	=	mysql_query($sq);
	while($rw=mysql_fetch_array($rs))
	{
		$arrClusterDesc[$rw[1]]	=	$rw[0];
	}
	return $arrClusterDesc;
}

function setExamCornerQcode($clusterCode,$clusterType,$ttCode)
{
	$userID	=	$_SESSION["userID"];
	$_SESSION["qcode"] = "";
	if($clusterType=="bronze")
	{
		$sqQcode	=	"SELECT qcode,subdifficultylevel FROM adepts_questions WHERE clusterCode='$clusterCode' AND status=3";
		if($_SESSION['flashContent'] == 0)
			$sqQcode .= " AND (question NOT LIKE '%[%.swf%]%')";
		$sqQcode .= " ORDER BY subdifficultylevel";
	}
	else 
	{
		setImportantQuestions($clusterCode,$userID);
		$sqQcode	=	"SELECT qcode,subdifficultylevel FROM adepts_questions WHERE clusterCode='$clusterCode' AND status=3";
		if($_SESSION['flashContent'] == 0)
			$sqQcode .= " AND (question NOT LIKE '%[%.swf%]%')";
		$sqQcode .= " GROUP BY subdifficultylevel ORDER BY subdifficultylevel";
	}
	$rsQcode	=	mysql_query($sqQcode);
	while($rwQcode = mysql_fetch_array($rsQcode))
	{
		$qcode = $rwQcode["qcode"];
		$sdl   = $rwQcode["subdifficultylevel"];
		if($k==0)
		{
			if($_SESSION["qcode"]=="")
			{
				$_SESSION["qcode"]	=	$qcode;
				$_SESSION["currentSdl"]	=	$sdl;
			}
			$k++;
			continue;
		}
		else if($_SESSION["qcode"]==$qcode)
			continue;
		
		$dynamic = $rwQcode["dynamic"];

		if($dynamic)
		{
			for($d=0;$d<5;$d++)
			{
				if(!isset($allQuestionsArray[$sdl]))
					$allQuestionsArray[$sdl]=array();
				array_push($allQuestionsArray[$sdl],$qcode);
			}
		}
		else
		{
			if(!isset($allQuestionsArray[$sdl]))
				$allQuestionsArray[$sdl]=array();
			array_push($allQuestionsArray[$sdl],$qcode);		
		}
	}
	$_SESSION['examCornerCluster']	=	1;
	$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;
	return $_SESSION["qcode"];
}

function setImportantQuestions($clusterCode,$userID)
{
	$allQuestionsArray	=	array();
	$sqImportant	=	"SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS."
						 WHERE clusterCode='$clusterCode' AND userID=$userID AND result='SUCCESS' ORDER BY clusterAttemptID LIMIT 1";
	$rsImportant	=	mysql_query($sqImportant);
	if($rwImportant=mysql_fetch_array($rsImportant))
	{
		$clusterAttemptID	=	$rwImportant[0];
		$sq	=	"SELECT GROUP_CONCAT(A.qcode),subdifficultylevel FROM ".TBL_QUES_ATTEMPT_CLASS." A, adepts_questions B
				 WHERE clusterAttemptID=$clusterAttemptID AND A.qcode=B.qcode AND userID=$userID AND R=0 AND B.status=3 GROUP BY subdifficultylevel ORDER BY subdifficultylevel";
		$rs	=	mysql_query($sq);
		while($rw=mysql_fetch_array($rs))
		{
			$qcodeArr	=	explode(",",$rw[0]);
			if(count($qcodeArr)>=3)
			{
				$allQuestionsArray[$rw[1]]	=	array();
				array_push($allQuestionsArray[$rw[1]],array_rand($qcodeArr,1));
			}
		}
	}
	$sqIQ	=	"SELECT qcode,subdifficultylevel FROM adepts_questions WHERE misconception<>'' AND clusterCode='$clusterCode' AND status=3";
	if($_SESSION['flashContent'] == 0)
		$sqIQ .= " AND (question NOT LIKE '%[%.swf%]%')";
	$sqIQ .= " GROUP BY subdifficultylevel ORDER BY subdifficultylevel";
	$rsIQ	=	mysql_query($sqIQ);
	while($rwIQ=mysql_fetch_array($rsIQ))
	{
		if(!in_array($rwIQ[1],$allQuestionsArray))
		{
			$allQuestionsArray[$rwIQ[1]]	=	array();
			array_push($allQuestionsArray[$rwIQ[1]],$rwIQ[0]);
		}
	}
	$_SESSION["importantQuestions"]	=	$allQuestionsArray;
}

function getFirstImportantQuetion()
{
	$allQuestionsArray	=	$_SESSION["allQuestionsArray"];
	foreach($allQuestionsArray as $sdl=>$totalQcodes)
	{
		$_SESSION["currentSdl"]	=	$sdl;
		$_SESSION["qcode"]		=	$totalQcodes[0];
		unset($allQuestionsArray[$sdl]);
		$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;
		break;
	}
}

function getNextBucketClusterQuetion($lastqcode,$responseResult)
{
	$_SESSION["qcode"] = -16;
	$allQuestionsArray	=	$_SESSION["allQuestionsArray"];
	if($responseResult==1)
		unset($allQuestionsArray[$_SESSION["currentSdl"]]);
	foreach($allQuestionsArray as $sdl=>$totalQcodes)
	{
		if($sdl==$_SESSION["currentSdl"])
		{
			shuffle($totalQcodes);
			$_SESSION["qcode"]	=	$totalQcodes[0];
		}
		else if($sdl > $_SESSION["currentSdl"])
		{
			shuffle($totalQcodes);
			$_SESSION["qcode"]	=	$totalQcodes[0];
			$_SESSION["currentSdl"]	=	$sdl;
		}
		if(count($totalQcodes)==1)
			unset($allQuestionsArray[$sdl]);
		else
			unset($allQuestionsArray[$sdl][array_search($totalQcodes[0],$allQuestionsArray[$sdl])]);
		break;
	}
	$_SESSION["allQuestionsArray"]	=	$allQuestionsArray;
	return $_SESSION["qcode"]."~0";
}

function finishBucketCluster()
{
	$attemptID	=	$_SESSION['bucketAttemptID'];
	$clusterCode	=	$_SESSION['bucketClusterCode'];
	$sq	=	"SELECT SUM(R)/COUNT(srno)*100 AS perCorrect FROM adepts_bucketClusterAttempt WHERE attemptID=$attemptID AND userID=".$_SESSION["userID"];
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$perCorrect	=	$rw[0];
	if($perCorrect >= 75)
		$result	=	"SUCCESS";
	else
		$result	=	"FAILURE";
	$sq	=	"UPDATE adepts_examcornerClusterStatus SET result='$result', perCorrect=$perCorrect WHERE attemptID=$attemptID AND userID=".$_SESSION["userID"];
	$rs	=	mysql_query($sq);
}

?>