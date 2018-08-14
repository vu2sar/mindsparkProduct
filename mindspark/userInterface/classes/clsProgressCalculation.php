<?php
class topicProgressCalculation
{
	var $objTT;
	var $clusterArray;
	var $sdlArray;
	var $totalSDLs;
	var $subjectNo;
	var $clusterStr;
	var $noOFSDLsCleared;	//should not be in this class - but kept temporarily till the final structure of classes is ready
	var $higherLevel;		//should not be in this class - but kept temporarily till the final structure of classes is ready
	var $maxSDL;
	var $ttAttemptID;
	var $class;

	function topicProgressCalculation($ttCode, $class, $flow, $ttAttemptID, $subjectno=2)
	{
		$this->subjectNo = $subjectno;
		$this->ttAttemptID = $ttAttemptID;
		$this->totalSDLs = 0;
		$this->noOFSDLsCleared = 0;
		$this->higherLevel = 0;
		$this->clusterArray = array();
		$this->sdlArray     = array();
		$objTT = new teacherTopic($ttCode, $class, $flow);
        $this->objTT = $objTT;
		$this->clusterArray  = $objTT->getClustersOfLevel($objTT->startingLevel);
		$this->sdlArray      = $objTT->getSDLDetails($this->clusterArray);
		foreach ($this->sdlArray as $ccode=>$sdlstr)
		{
		  	$this->totalSDLs += count(explode(",",$sdlstr));
		}
		$this->clusterStr = $objTT->getClusterOrder();
		$this->class  = $class;
	}

	function getAccuracy()
	{
		$accuracy = "";
		$clusterStr = "'".implode("','",$this->clusterArray)."'";
		$clusterAttemptIDs = "";
		$clAttemptQuery = "SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b WHERE  b.ttAttemptID=a.ttAttemptID AND a.clusterCode IN ($clusterStr) AND b.teacherTopicCode='".$this->objTT->ttCode."' AND b.ttAttemptID='$this->ttAttemptID'";
		$clAttemptResult = mysql_query($clAttemptQuery) or die("error:  $clAttemptResult  ".mysql_error());
		while ($clAttemptLine = mysql_fetch_array($clAttemptResult))
		{
			$clusterAttemptIDs .= $clAttemptLine[0].",";
		}
		$clusterAttemptIDs = substr($clusterAttemptIDs,0,-1);

		if($clusterAttemptIDs != "")
		{
			$sql = "SELECT SUM(R) as corrects, COUNT(R) as attempts FROM ".TBL_QUES_ATTEMPT."_class".$this->class." WHERE clusterAttemptID IN ($clusterAttemptIDs) AND R<2";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$totalAttempts = $row['attempts'];
			$totalCorrects = $row['corrects'];
			if($totalAttempts != 0)
				$accuracy = round((($totalCorrects/$totalAttempts)*100),2);
		}
		return($accuracy);
	}
	function getProgressInTT()
	{
		return round($this->getProgressOfNormal(),1);
	}

	function getTotalQuestionAttempted()
	{
		$totalQuesAttempted = 0;
		$clusterStr = "'".implode("','",$this->clusterArray)."'";
		$clusterAttemptIDs = "";
		$clAttemptQuery = "SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b WHERE  b.ttAttemptID=a.ttAttemptID AND a.clusterCode IN ($clusterStr) AND b.teacherTopicCode='".$this->objTT->ttCode."' AND b.ttAttemptID='$this->ttAttemptID'";
		$clAttemptResult = mysql_query($clAttemptQuery) or die("error:  $clAttemptResult  ".mysql_error());
		while ($clAttemptLine = mysql_fetch_array($clAttemptResult))
		{
			$clusterAttemptIDs .= $clAttemptLine[0].",";
		}
		if($clusterAttemptIDs != "")
		{
			$clusterAttemptIDs = substr($clusterAttemptIDs,0,-1);

			$sql = "SELECT COUNT(R) as attempts FROM ".TBL_QUES_ATTEMPT."_class".$this->class." WHERE clusterAttemptID IN ($clusterAttemptIDs)";
			$result = mysql_query($sql);
			$row = mysql_fetch_assoc($result);
			$totalQuesAttempted = $row["attempts"];
		}
		return($totalQuesAttempted);
	}

	function getProgressOfNormal()
	{
		$tableExtension = "";
		$this->higherLevel = 0;
		$totalClusters = count($this->clusterArray);
		$clusterAttemptIDs = "";
		$clAttemptQuery = "SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b
		                   WHERE  b.ttAttemptID=a.ttAttemptID AND b.teacherTopicCode='".$this->objTT->ttCode."' AND b.ttAttemptID='$this->ttAttemptID'";

		$clAttemptResult = mysql_query($clAttemptQuery) or die("error:  $clAttemptResult  ".mysql_error());
		while ($clAttemptLine = mysql_fetch_array($clAttemptResult)) {
			$clusterAttemptIDs .= $clAttemptLine[0].",";
		}
		$clusterAttemptIDs = substr($clusterAttemptIDs,0,-1);
		$maxCluster = $maxSDL = "";
	    $noOFSDLsCleared = 0;
		if($clusterAttemptIDs!="" && $this->clusterStr!="")
		{
		    //Query for max SDL cleared in the TT
		    $q = "SELECT a.clustercode, subdifficultylevel
		          FROM   ".TBL_QUES_ATTEMPT."_class".$this->class." a, adepts_questions c
		          WHERE  a.qcode=c.qcode AND a.clustercode=c.clusterCode AND a.clusterAttemptID in ($clusterAttemptIDs) AND R=1";

		    $q .= " ORDER BY FIELD(a.clusterCode, ".$this->clusterStr.") DESC, subdifficultylevel DESC LIMIT 1";


		    $r = mysql_query($q) or die("error:  $q  ".mysql_error());
		    $l = mysql_fetch_array($r);

		    $maxCluster = $l[0];
		    $maxSDL     = $l[1];

		    if(in_array($maxCluster,$this->clusterArray))
		    {
		    	$clusterNo = 0;

		    	while ($maxCluster!=$this->clusterArray[$clusterNo] && $clusterNo<$totalClusters)
		    	{
		    		$noOFSDLsCleared += count(explode(",",$this->sdlArray[$this->clusterArray[$clusterNo]]));
		    		$clusterNo++;
		    	}
				if($maxCluster==$this->clusterArray[$clusterNo] && $clusterNo<$totalClusters)
		    	{
		    		$sdlArray = explode(",",$this->sdlArray[$this->clusterArray[$clusterNo]]);
		    		for($k=0; $k<count($sdlArray); $k++)
		    		{
		    			if($sdlArray[$k]!=$maxSDL)
		    			{
		    				$noOFSDLsCleared += 1;
		    			}
		    			else
		    				break;
		    		}
		    		$noOFSDLsCleared++;
		    	}
		    }
		    elseif($maxCluster!="")
		    {
				$maxClusterLevel = $this->objTT->getClusterLevel($maxCluster);
		    	if($maxClusterLevel[0]>$this->objTT->startingLevel)    //implies gone to a higher level
		    	{
		    		$noOFSDLsCleared = $this->totalSDLs;
		    		$this->higherLevel = 1;
		    	}
		    }
		}
		$this->noOFSDLsCleared = $noOFSDLsCleared;
	    $ttProgress = round($noOFSDLsCleared/$this->totalSDLs *100,1);
	    return $ttProgress;
	}

	function updateProgress()
	{
		$ttCode = $this->objTT->ttCode;
		$accuracy = $this->getAccuracy();
		$progress = $this->getProgressInTT();
		$quesAttempts = $this->getTotalQuestionAttempted();
		$sqProgress = "SELECT classLevelResult, progress, noOfQuesAttempted, perCorrect FROM ".TBL_TOPIC_STATUS." WHERE ttAttemptID='$this->ttAttemptID'";
		$rsProgress = mysql_query($sqProgress);
		$rwProgress = mysql_fetch_assoc($rsProgress);
		if($rwProgress["classLevelResult"] == "SUCCESS")
		{
			if($progress==0)
			{
				$log_error_data = "INSERT INTO adepts_errorLogs SET bugType='TopicProgress', 
									bugText='ttAttemptID - $this->ttAttemptID , ttCode - $ttCode, oldProgress - ".$rwProgress["progress"]." , 
									oldAccuracy - ".$rwProgress["perCorrect"]." , oldQuesAttempts - ".$rwProgress["noOfQuesAttempted"]." , 
									newProgress - $progress , newAccuracy - $accuracy , newQuesAttempts - $quesAttempts', 
									qcode='".$_SESSION['qcode']."', 
									userID='".mysql_real_escape_string($_SESSION['userID'])."', 
									sessionID='".mysql_real_escape_string($_SESSION['sessionID'])."', 
									schoolCode='".mysql_real_escape_string($_SESSION['schoolCode'])."'";
				$exec_log_error_data = mysql_query($log_error_data);
			}
			$progress = 100;
		}
		$sql = "UPDATE ".TBL_TOPIC_STATUS." SET progress='$progress', perCorrect='$accuracy', noOfQuesAttempted='$quesAttempts'
			    WHERE ttAttemptID='$this->ttAttemptID'";
		$result = mysql_query($sql);

		$sql = "UPDATE ".TBL_CURRENT_STATUS." SET progressUpdate='0' WHERE ttAttemptID='$this->ttAttemptID'";
		$result = mysql_query($sql);
	}
}

?>