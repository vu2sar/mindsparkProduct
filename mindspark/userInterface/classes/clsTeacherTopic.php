<?php
class teacherTopic
{
	var $ttCode;
	var $ttDescription;
	var $flow;
	var $meantForClasses;
	var $clusterFlowArray;
	var $startingLevel;
	var $avgTimePerSdl;
	var $customTopic;
	var $parentTTCode;

	function teacherTopic($teacherTopicCode, $cls, $flow)
	{
		$this->ttCode = $teacherTopicCode;
		$this->flow   = $flow;
		$this->meantForClasses  = array();
		$this->clusterFlowArray = array();
		$query  = "SELECT teacherTopicDesc, customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$teacherTopicCode'";
		$result = mysql_query($query);
		$line   = mysql_fetch_array($result);
		$this->ttDescription = $line[0];
		$this->customTopic   = $line[1];
		$this->parentTTCode  = $line[2];


		if($flow=="MS" || $flow=="CBSE" || $flow=="ICSE" || $flow=="IGCSE" || $flow=="")
		{
			$fieldName = "level";
			if($flow=="MS")
				$fieldName = "ms_level";
			else if($flow=="CBSE")
				$fieldName = "cbse_level";
			else if($flow=="ICSE")
				$fieldName = "icse_level";
			else if($flow=="IGCSE")
				$fieldName = "igcse_level";
			$this->getClassesMapped($teacherTopicCode, $fieldName);
			$this->startingLevel = $this->determineStartingLevel($cls);
			$this->populateClusterCodes($fieldName);
		}
		else if(strcasecmp(substr($flow,0,6),"Custom")==0)
		{
			$customizedFlow = substr($flow,9);
			$query   = "SELECT clustercodes FROM adepts_customizedTopicDetails WHERE code=$customizedFlow";

			$result  = mysql_query($query) or die("Error in getting the details of customization for the topic!");
			if($line = mysql_fetch_array($result))
			{
				$clusterCodeArray   = explode(",",$line[0]);
				$clusterStatusAndTypeArray = $this->getClusterStatus($clusterCodeArray);
				$srno = 0;

				//Include lower level clusters i.e. the clusters whose flow no is less than the first one selected in the customized flow
				if($this->customTopic)
				{
                    $ll_query = "SELECT b.*, c.ms_level,c.clusterType FROM adepts_teacherTopicClusterMaster a, adepts_teacherTopicClusterMaster b, adepts_clusterMaster c
        				         WHERE  a.teacherTopicCode=b.teacherTopicCode AND b.clusterCode=c.clusterCode AND c.status='live' AND a.teacherTopicCode='".$this->parentTTCode."' AND a.clusterCode='".$clusterCodeArray[0]."' AND b.flowno < a.flowno AND c.clusterCode NOT in ('".implode("','",$clusterCodeArray)."') ORDER BY flowno";
				}
				else
				{
    				$ll_query = "SELECT b.*, c.ms_level,c.clusterType FROM adepts_teacherTopicClusterMaster a, adepts_teacherTopicClusterMaster b, adepts_clusterMaster c
        				         WHERE  a.teacherTopicCode=b.teacherTopicCode AND b.clusterCode=c.clusterCode AND c.status='live' AND a.teacherTopicCode='".$this->ttCode."' AND a.clusterCode='".$clusterCodeArray[0]."' AND b.flowno<a.flowno ORDER BY flowno";
				}

				$ll_result = mysql_query($ll_query);
				while ($ll_line = mysql_fetch_array($ll_result))
				{
				    $ms_level = array();
				    if($ll_line['ms_level']!="" && !in_array($ll_line['clusterCode'], $clusterCodeArray) )
				    {
				        //Check the class level - if the cluster
				        $ms_level = explode(",",$ll_line['ms_level']);
				        $maxLevel = max($ms_level);
						$clusterType = $ll_line['clusterType'];
				        if($maxLevel>=$cls)
				        $maxLevel = $cls-1;   //if the class level is the same as the selected class, to treat is as lower level change it to class-1.
				        $this->clusterFlowArray[$srno][0] = $ll_line['clusterCode'];
				        $this->clusterFlowArray[$srno][1] = $maxLevel;
						$this->clusterFlowArray[$srno][2] = ($clusterType=="")?"":$clusterType;
				        $srno++;
				    }
				}
				for($i=0; $i<count($clusterCodeArray); $i++)
				{
					if(strcasecmp($clusterStatusAndTypeArray[$clusterCodeArray[$i]][0],"live")==0)
					{
						$this->clusterFlowArray[$srno][0] = $clusterCodeArray[$i];
						$this->clusterFlowArray[$srno][1] = $cls;
						$this->clusterFlowArray[$srno][2] = ($clusterStatusAndTypeArray[$clusterCodeArray[$i]][1]=="")?"":$clusterStatusAndTypeArray[$clusterCodeArray[$i]][1];
						$srno++;
					}
				}

				//Include higher level clusters i.e. the clusters whose flow no is greater than the last one selected in the customized flow
				if($this->customTopic)
				{
				    $hh_query = "SELECT b.*, c.ms_level, c.clusterType FROM adepts_teacherTopicClusterMaster a, adepts_teacherTopicClusterMaster b, adepts_clusterMaster c
        				         WHERE  a.teacherTopicCode=b.teacherTopicCode AND b.clusterCode=c.clusterCode AND c.status='live' AND a.teacherTopicCode='".$this->parentTTCode."' AND a.clusterCode='".$clusterCodeArray[$i-1]."' AND b.flowno>a.flowno AND c.clusterCode NOT in ('".implode("','",$clusterCodeArray)."') ORDER BY flowno";
				}
				else
				{
    				$hh_query = "SELECT b.*, c.ms_level, c.clusterType FROM adepts_teacherTopicClusterMaster a, adepts_teacherTopicClusterMaster b, adepts_clusterMaster c
        				         WHERE  a.teacherTopicCode=b.teacherTopicCode AND b.clusterCode=c.clusterCode AND c.status='live' AND a.teacherTopicCode='".$this->ttCode."' AND a.clusterCode='".$clusterCodeArray[$i-1]."' AND b.flowno>a.flowno ORDER BY flowno";
				}
				$hh_result = mysql_query($hh_query);
				while ($hh_line = mysql_fetch_array($hh_result))
				{
				    $ms_level = array();
				    if($hh_line['ms_level']!=""  && !in_array($hh_line['clusterCode'], $clusterCodeArray))
				    {
				        $ms_level = explode(",",$hh_line['ms_level']);
				        $minLevel = min($ms_level);
				        if($minLevel<=$cls)
				        $minLevel = $cls+1;     //if the class level is the same as the selected class, to treat is as higher level change it to class+1.
				        $this->clusterFlowArray[$srno][0] = $hh_line['clusterCode'];
				        $this->clusterFlowArray[$srno][1] = $minLevel;
						$this->clusterFlowArray[$srno][2] = ($hh_line['clusterType']=="")?"":$hh_line['clusterType'];
				        $srno++;
				    }
				}
			}
			$this->startingLevel   = $cls;
			$this->meantForClasses = $cls;
		}

		if($this->customTopic)
		    $query = "SELECT timePerSdl FROM adepts_timepersdl WHERE teacherTopicCode='".$this->parentTTCode."' AND class='$cls'";
		else
		    $query = "SELECT timePerSdl FROM adepts_timepersdl WHERE teacherTopicCode='$teacherTopicCode' AND class='$cls'";
		$result = mysql_query($query);
		if($line = mysql_fetch_array($result))
		{
			$this->avgTimePerSdl = $line["timePerSdl"];

		}
		else
		    $this->avgTimePerSdl = 2; //If not found, take 2 as the default value
	}

	function getClassesMapped($teacherTopicCode, $fieldName)
	{
		$query = "SELECT distinct $fieldName
	              FROM   adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
	              WHERE  a.clusterCode=b.clusterCode AND teacherTopicCode='$teacherTopicCode' AND b.status='live' AND a.clusterCode NOT LIKE '%100'";
		$result = mysql_query($query) or die(mysql_error());
		$classes = array();
		while($user_row = mysql_fetch_array($result))
		{
			if($user_row[0]=="")
				continue;
			$tmpClassArr = explode(",",$user_row[0]);
			for($i=0; $i<count($tmpClassArr); $i++)
			    array_push($classes,trim($tmpClassArr[$i]));
		}
		$classes = array_unique($classes);
		sort($classes,SORT_NUMERIC);
		$this->meantForClasses = $classes;
	}

	function determineStartingLevel($cls)
	{
		$startingLevel = "";
		if($cls!="")
		{
			if($cls > $this->meantForClasses[count($this->meantForClasses)-1])	//if class level of the user is higher than the max level of the TT, starting level will be from the first cluster of the highest level
				$startingLevel = $this->meantForClasses[count($this->meantForClasses)-1];
			elseif ($cls <= $this->meantForClasses[0])	//if class level of the user is lower than the min level of the TT, starting level will be from the first level
				$startingLevel = $this->meantForClasses[0];
			else	//else start from the first cluster of the user class level
			{
				for($i=0; $i<count($this->meantForClasses) && $this->meantForClasses[$i]!=$cls; $i++);
				if($i==count($this->meantForClasses))	//it means there are no clusters mapped for the user class level
				{
					//This case will happen when say a TT is mapped to classes 6,7,8 & 10 and the user is of class 9, in that case start from one lower level
					for($i=0; $i<count($this->meantForClasses) && $this->meantForClasses[$i]<$cls; $i++);
					$i--;
				}
				$startingLevel = $this->meantForClasses[$i];

			}
		}
		else
			$startingLevel = $this->meantForClasses[0];
		return $startingLevel;
	}

	function populateClusterCodes($fieldName)
	{
		$clusterFlowArray = array();
		$classLevelClusterArray = array();
		$query = "SELECT a.clusterCode, $fieldName, status, a.flowno, b.clusterType FROM adepts_teacherTopicClusterMaster a, adepts_clusterMaster b
              	  WHERE  a.clusterCode=b.clusterCode AND a.teacherTopicCode='".$this->ttCode."' AND b.status='live' AND
              		     a.clusterCode NOT LIKE '%100' ORDER BY a.flowno";
		//echo $query;
		$result = mysql_query($query) or die(mysql_error());
		$srno = 0;
		$clustersCoveredArray = array();
		for($clsCounter=0; $clsCounter<count($this->meantForClasses); $clsCounter++)
		{
			mysql_data_seek($result,0);
			$curLevel = $this->meantForClasses[$clsCounter];
			while($line   = mysql_fetch_array($result))
			{
				if($line[1]=="")	//if no classes mapped to the cluster for the selected flow, it means that cluster is not to be considered for this flow
					continue;
				$levelArray = explode(",",$line[1]);
				if(!in_array($line['clusterCode'],$clustersCoveredArray) && in_array($curLevel,$levelArray))
				{
					if(($curLevel==$this->startingLevel) || (!in_array($this->startingLevel,$levelArray)))
					{
					    $clusterFlowArray[$srno][0] = $line['clusterCode'];
					    $clusterFlowArray[$srno][1] = $line[1];
						$clusterFlowArray[$srno][2] = ($line['clusterType']=="")?"":$line['clusterType'];
					    array_push($clustersCoveredArray, $line['clusterCode']);
					    $srno++;
					}
				}
			}
		}
		$this->clusterFlowArray = $clusterFlowArray;
	}

	/**
     * function to get the first/starting cluster in the flow of a teacher topic for the starting level
     */
    function findFirstCluster()
    {
    	$clusterCode = $this->getEarliestClusterOfGrade($this->startingLevel);
		if ($clusterCode == "")
                $clusterCode = -1;
        return $clusterCode;
    }

    function getEarliestClusterOfGrade($startingLevel)
    {
    	$clusterCode = "";
        for($i=0; $i<count($this->clusterFlowArray) && $clusterCode==""; $i++)
        {
        	$classLevels = explode(",",$this->clusterFlowArray[$i][1]);
        	for($j=0; $j<count($classLevels); $j++)
        	{
        		if($classLevels[$j]==$startingLevel)
        		{
        			$clusterCode = $this->clusterFlowArray[$i][0];
        			break;
        		}
        	}
        }
        return  $clusterCode;
    }

    function getOneClusterLowerInFlow($clusterCode)
    {
        $nextCluster  = "";
        $fall = 1;
        $nextCluster = $this->getNLowerCluster($clusterCode,$fall);
        return $nextCluster;
    }

    //Function will return the cluster in the TT "n" level back from the current one.
    function getNLowerCluster($clusterCode, $n)
    {
		//filter out practice clusters..
		//$filteredClusterFlow = $this->removePracticeClusters($this->clusterFlowArray);
		$filteredClusterFlow = $this->clusterFlowArray;
		//Get the position of the current cluster.
		for($pos=0; $pos<count($filteredClusterFlow) && $clusterCode!=$filteredClusterFlow[$pos][0]; $pos++);
		if($pos==count($filteredClusterFlow))		//just a second check, this case should not arise
			return $clusterCode;
		$count = 0 ;
		while ($pos>0 && $count<$n) {
			$pos--;
			$count++;
		}
		$clusterCode = $filteredClusterFlow[$pos][0];
		return $clusterCode;
    }

    function findRemedialCluster($clusterCode)
    {
    	$nextClusterCode = "";
        $query  = "SELECT a.remedialCluster, b.status FROM adepts_clusterMaster a, adepts_clusterMaster b
                   WHERE  b.clusterCode=a.remedialCluster AND a.clusterCode='$clusterCode'";
        $result = mysql_query($query) or die(mysql_error());
        if(mysql_num_rows($result)==0)     //Implies no remedial cluster for this cluster code for this TT
            $nextClusterCode = $clusterCode;    //The cluster itself is its remedial cluster.
        if($user_row = mysql_fetch_array($result))
        {
            $nextClusterCode = $user_row["remedialCluster"];
            $status              = $user_row["status"];
            if(strcasecmp($status,"live")!=0)
                $nextClusterCode = $clusterCode;
        }
        return $nextClusterCode;
    }

    function findNextNormalCluster($clusterCode)
    {
        $nextCluster=-1;    //Initialize
        //Get the position of the current cluster.
		for($pos=0; $pos<count($this->clusterFlowArray) && $clusterCode!=$this->clusterFlowArray[$pos][0]; $pos++);
		if($pos==count($this->clusterFlowArray))		//just a second check, this case should not arise
			return $nextCluster;
		else
		{
			$pos++;
			if($pos==count($this->clusterFlowArray)) //This wud be the case in the last cluster in the flow.
				$nextCluster = -1;
			else
				$nextCluster = $this->clusterFlowArray[$pos][0];
		}
        return $nextCluster;
    }
    function hashigherClassInFlow($cls)
    {
        $nextCluster=-1;    //Initialize
        //Get the position of the current cluster.
		for($pos=0; $pos<count($this->clusterFlowArray);$pos++){
			$clusterLevelArray = explode(",",$this->clusterFlowArray[$pos][1]);
			$minClass=min($clusterLevelArray);
			if ($cls<$minClass) break;
		}
		if($pos==count($this->clusterFlowArray)) //This wud be the case in the last cluster in the flow.
			$nextCluster = -1;
		else 
			$nextCluster = $this->clusterFlowArray[$pos][0];
        return $nextCluster;
    }

    /**
     *	function to obtain the class level that the cluster is mapped to for a TT
     */
    function getClusterLevel($clusterCode)
    {
    	for($pos=0; $pos<count($this->clusterFlowArray) && $clusterCode!=$this->clusterFlowArray[$pos][0]; $pos++);
        $clusterLevelArray = explode(",",$this->clusterFlowArray[$pos][1]);
		sort($clusterLevelArray, SORT_NUMERIC);
		return $clusterLevelArray;
    }

    function getClusterStatus($clusterCodeArray)
    {
    	$clusterStatusAndTypeArray = array();
    	$clusterCodeStr = "'".implode("','",$clusterCodeArray)."'";
    	$query  = "SELECT clusterCode, status, clusterType FROM adepts_clusterMaster WHERE clusterCode in ($clusterCodeStr)";
    	$result = mysql_query($query);
    	while ($line = mysql_fetch_array($result))
    	{
    		$clusterStatusAndTypeArray[$line['clusterCode']][0] = $line['status'];
    		$clusterStatusAndTypeArray[$line['clusterCode']][1] = $line['clusterType'];
    	}
    	return $clusterStatusAndTypeArray;
    }

    /**
     *	function to obtain the no. of clusters of a given level
     */
    function getNoOfClustersOfLevel($level)
	{
		$noOfClusters = 0;
		for($i=0; $i<count($this->clusterFlowArray); $i++)
        {
        	$classLevels = explode(",",$this->clusterFlowArray[$i][1]);
        	if(in_array($level,$classLevels))
        	{
        		$noOfClusters++;
        	}

        }
	    return $noOfClusters;
	}

	function getClustersOfLevel($level)
	{
		$clusterArray = array();
		for($i=0; $i<count($this->clusterFlowArray); $i++)
        {
        	$classLevels = explode(",",$this->clusterFlowArray[$i][1]);
        	if(in_array($level,$classLevels) || $level=="All")
        	{
        		array_push($clusterArray, $this->clusterFlowArray[$i][0]);
        	}
        }
	    return $clusterArray;
	}

	function getSDLDetails($clusterArray)
	{
		$sdlArray = array();
		//$clusterArray = array_values(array_diff($clusterArray,$pcClusterArray));	//Remove practice cluster, since the questions of pc are not mapped to sdl
		$clusterStr = "'".implode("','",$clusterArray)."'";
	    $query = "SELECT clusterCode, group_concat(DISTINCT subdifficultylevel ORDER BY subdifficultylevel)
	              FROM   adepts_questions
	              WHERE  clusterCode IN ($clusterStr) AND status=3
	              GROUP BY clusterCode";
		$result = mysql_query($query) or die("Error in getting SDL details!");
		while ($line = mysql_fetch_array($result))
		{
			$sdlArray[$line['clusterCode']] = $line[1];
		}
		return $sdlArray;
	}

	function getNumberOfSDLs($clusterCode)
	{
		$noOfSdls = 0;
		$query = "SELECT count(distinct subdifficultylevel) FROM adepts_questions WHERE clusterCode='$clusterCode' AND status=3";
		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		$noOfSdls = $line[0];
		return $noOfSdls;
	}

	function getLowerLevelClusters()
	{
		$clusterCodeArray = array();
		$startingLevel = $this->startingLevel;
        for($i=0; $i<count($this->clusterFlowArray); $i++)
        {
        	$classLevels = explode(",",$this->clusterFlowArray[$i][1]);
        	if($classLevels[count($classLevels)-1]<$startingLevel)
        	{
        		array_push($clusterCodeArray, $this->clusterFlowArray[$i][0]);
        	}
        }
        return  $clusterCodeArray;
	}

	function getClusterOrder()
	{
		$clusterStr = "";
		for($pos=0; $pos<count($this->clusterFlowArray); $pos++)
			$clusterStr .= "'".$this->clusterFlowArray[$pos][0]."',";
		$clusterStr = substr($clusterStr,0,-1);
		return $clusterStr;
	}

	function getSDLNumberDetails()
	{
		$sdlNumberArray = array();
		$clusterStr = $this->getClusterOrder();
		if($clusterStr != "")
		{
			$query  = "SELECT distinct clusterCode, subdifficultylevel FROM adepts_questions WHERE clusterCode IN ($clusterStr) AND status=3 ORDER BY FIELD(clusterCode, $clusterStr), subdifficultylevel";
			$result = mysql_query($query) or die($this->ttCode.$query.": ".mysql_error());
			$srno=1;
			while ($line = mysql_fetch_array($result))
			{
				$sdlNumberArray[$line['clusterCode']][$line['subdifficultylevel']] = $srno;
				$srno++;
			}
		}
		return $sdlNumberArray;
	}

	function getMinLevelOfClass($level)
	{
		$minLevel = 0;
		$sdlNumberArray = $this->getSDLNumberDetails();
		$clusterArray = $this->getClustersOfLevel($level);
		$minCluster = $clusterArray[0];
		foreach ($sdlNumberArray as $clusterCode=>$sdlArray)
		{
			if($minCluster==$clusterCode)
			{
				$tmpArray = array_keys($sdlArray);
				$minLevel = $sdlNumberArray[$minCluster][$tmpArray[0]];
				break;
			}
		}
		return $minLevel;
	}
	
	function getMaxLevelOfClass($level)
	{
		$maxLevel = 0;
		$sdlNumberArray = $this->getSDLNumberDetails();
		$clusterArray = $this->getClustersOfLevel($level);
		$maxCluster = $clusterArray[count($clusterArray)-1];
		foreach ($sdlNumberArray as $clusterCode=>$sdlArray)
		{
			if($maxCluster==$clusterCode)
			{
				$tmpArray = array_keys($sdlArray);
				$maxLevel = $sdlNumberArray[$maxCluster][$tmpArray[count($tmpArray)-1]];
				break;
			}
		}
		return $maxLevel;
	}

	function getProgressDetailsAtSDL()
	{
		$totalSDLs = 0;
		$clusterArray  = $this->getClustersOfLevel($this->startingLevel);		
		$sdlArray      = $this->getSDLDetails($clusterArray);
		foreach ($sdlArray as $ccode=>$sdlstr)
		{
		  	$totalSDLs += count(explode(",",$sdlstr));
		}
		$contributionPerSDL = 100/$totalSDLs;		
		$progressContributionArray = array();
		$lastProgressPer = 0;
		foreach($clusterArray as $ccode)
		{
			$sdlDetails = explode(",",$sdlArray[$ccode]);
			foreach($sdlDetails as $sdl)
			{
				$lastProgressPer += $contributionPerSDL;			
				$progressContributionArray[$ccode][$sdl] = $lastProgressPer;
			}
		}
		return $progressContributionArray;
	}
}

?>
