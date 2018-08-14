<?php
include_once("clsTeacherTopic.php");


class topicProgress
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
	var $class;

	function topicProgress($ttCode, $class, $flow, $subjectno=2)
	{
		$this->subjectNo = $subjectno;
		$this->totalSDLs = 0;
		$this->totalLowerLimit = 0;
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
		$this->class = $class;
	}

	//function getAccuracy($userID, $tillDate="",$onlyPractice=false)
	
	function getProgressInTT($userID, $tillDate="")
	{
		$ttProgress = $this->getProgressOfNormal($userID, $tillDate);
		//Lock the topic progress when topic completed (this is needed due to change in the content flow where topic progress is affected
	    if($ttProgress<100 && $tillDate == "")
	    {
	        //Check if topic completed atleast once - if so lock the topic progress to 100
	        $query = "SELECT count(ttAttemptID) FROM ".TBL_TOPIC_STATUS." WHERE userID=$userID AND teacherTopicCode='".$this->objTT->ttCode."' AND (result='SUCCESS' or classLevelResult='SUCCESS')";
	        $result = mysql_query($query);
	        $line  = mysql_fetch_array($result);
	        if($line[0]>0)
	           $ttProgress = 100;
	    }
	    return $ttProgress;
	}

	
	function getProgressOfNormal($userID, $tillDate="")
	{
		$tableExtension = "";
		//$this->higherLevel = 0;
		$totalClusters = count($this->clusterArray);
		$clusterAttemptIDs = "";
		$clAttemptQuery = "SELECT clusterAttemptID FROM ".TBL_CLUSTER_STATUS." a, ".TBL_TOPIC_STATUS." b
		                   WHERE  b.ttAttemptID=a.ttAttemptID AND b.userID=a.userID AND b.userID=$userID AND b.teacherTopicCode='".$this->objTT->ttCode."'";

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
		          WHERE  a.qcode=c.qcode AND a.clustercode=c.clusterCode AND userID=$userID AND a.clusterAttemptID in ($clusterAttemptIDs) AND R=1";
		    if($tillDate!="")
				$q .= " AND attemptedDate <= '$tillDate'";

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
		    	if($maxCluster==$this->clusterArray[$clusterNo] && $clusterNo<$totalClusters)// && !in_array($this->clusterArray[$clusterNo],$this->pcClusterArray))
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
		    		//$this->higherLevel = 1;
		    	}
		    }
		}
		$this->noOFSDLsCleared = $noOFSDLsCleared;
	    $ttProgress = round($noOFSDLsCleared/$this->totalSDLs *100,1);
	    return $ttProgress;
	}
	

}

?>