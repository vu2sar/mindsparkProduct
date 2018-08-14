<?php
set_time_limit(0);

$startDate = date('Y-m-d',strtotime("-15 days"));
$startDate1 = date("d-m-Y", strtotime($startDate));
$endDate = date('Y-m-d');
$endDate1 = date("d-m-Y", strtotime($endDate));
$userID = $_SESSION['childID'];
$counterMore = 0;
$query = "SELECT childName, category, subcategory, schoolCode, childClass, childSection, gender from adepts_userDetails where userID=$userID";
$result = mysql_query($query) or die(mysql_error());
if($line = mysql_fetch_array($result))
{  
    $Name = $line[0];
     $category = $line[1];
  $subcategory = $line[2];
    $schoolCode = $line[3];
     $class = $line[4];
      $section = $line[5];
	  $gender = $line[6];
 }
else
{
    echo "Invalid userID"; exit;     
 }
$firstName = explode(" ", $Name);
$firstName = $firstName[0];

if ($gender == ""){
	$dispayName = $firstName;
	$dispayName1 = $firstName;
}else if($gender == "Girl"){
	$dispayName = "She";
	$dispayName1 = "Her";
}else{
	$dispayName = "He";
	$dispayName1 = "Him";
}

$msg = "";
$showMore=0;

$recommendedUsage = 90*60 ; //secs (90 mins) (multiply this with the no. of weeks)

$timeSpent = getTimeSpent($userID, $startDate, $endDate);

if($timeSpent>0)
{    
    if($timeSpent < (($recommendedUsage*60)/100))
        $usage = "less";
    else  if($timeSpent < (($recommendedUsage*80)/100))
        $usage = "reasonable";
     else 
        $usage = "good";
     $usageMsg = "spent $usage time in Mindspark";
}
else
       $usageMsg = "not used Mindspark";

$msg.= "<b>Report for the period: $startDate1 till $endDate1 of $Name:</b><br/><br/>";
$msg.= "During this period $Name has -<br/>";
$msg .= "<ul>";
$msg .= "<li>$usageMsg</li>";
$counterMore++;


if($timeSpent>0) {  
        $arrRewards = getNoOfSparkies($userID,$class, $startDate, $endDate);
		if($arrRewards["number"]>0){
			 $msg .= "<li>";
                 $msg .=  "got ".$arrRewards["number"].' '.$arrRewards["type"];
             $msg .= "</li>";  
 			$counterMore++;
		}
        $conceptsCovered = getLearningUnitsCovered($userID,$startDate,$endDate);
        if($conceptsCovered!="")
        {
			if($counterMore<3){
				$msg .= "<li>";
                 $msg .=  $conceptsCovered;
             	$msg .= "</li>";
			 	$counterMore++;
			}else{
            $msg .= "<div class='showFullSummary'><li>";
                 $msg .=  $conceptsCovered;
              $msg .= "</li></div>";
			   $msg .= "<div id='showFullSummaryButton'><input type='button' id='buttonValue' class='showFullSummaryButton' value='More >>' onclick='showSummary()'></input></div>";
			   $showMore=1;
			 }
        }
         
        $failedtLU = getFailedLU($userID, $startDate, $endDate);
        if(count($failedtLU)>0)
        {
			if($counterMore<3){
             $msg .= "<li>";   
            $msg.= "was not able to clear the following learning units:<br>";
            foreach($failedtLU as $d)
                 $msg.= "<ul class='listTopic'>".$d."</ul>";
           $msg .= "</li>";  
		   $counterMore++; 
		   }else{
		   	$msg .= "<div class='showFullSummary'><li>";   
            $msg.= "was not able to clear the following learning units:<br>";
            foreach($failedtLU as $d)
                 $msg.= "<ul class='listTopic'>".$d."</ul>";
           $msg .= "</li></div>";
			   $showMore=1;
		   }
        }
  }

if($category == "STUDENT" && $subcategory == "School")
{
    $currentlyActivatedTopics = getCurrentActivatedTopics($schoolCode, $class, $section);
     $topicsNotCompletedBeforeDeactivation = getUnfinishedTopics($userID, $currentlyActivatedTopics);
	$isAllowedDeactivatedTopicsForHomeUse = isAllowedDeactivatedTopicsForHome($schoolCode);
     if(count($topicsNotCompletedBeforeDeactivation)>0) 
      {
	  	if($counterMore<3){
			$msg .= "<li>";   
	          $msg .= "could not complete the following topics as teacher has deactivated the topic at school:<br/>";        //add msg if teacher has allowed the deactivated topics from home
			  if($isAllowedDeactivatedTopicsForHomeUse){
			  	$msg .= "However topics Deactivated in school can now be attempted at home.<br/>";
			  }
	            foreach($topicsNotCompletedBeforeDeactivation as $d)
	         $msg.= "<ul class='listTopic'>".$d."</ul>";
	           $msg .= "</li>";
			   $counterMore++;
		}else{
	          $msg .= "<div class='showFullSummary'><li>";   
	          $msg .= "could not complete the following topics as teacher has deactivated the topic at school:<br/>";        //add msg if teacher has allowed the deactivated topics from home
			  if($isAllowedDeactivatedTopicsForHomeUse){
			  	$msg .= "However topics Deactivated in school can now be attempted at home.<br/>";
			  }
	            foreach($topicsNotCompletedBeforeDeactivation as $d)
	         $msg.= "<ul class='listTopic'>".$d."</ul>";
	           $msg .= "</li></div>";
			   $showMore=1;
		   }
      }
}

$msg .= "</ul>";

if($showMore==1){
	 $msg .= "<div id='showFullSummaryButton'><input type='button' id='buttonValue' class='showFullSummaryButton' value='More >>' onclick='showSummary()'></input></div>";
}

echo $msg;

function getUnfinishedTopics($userID, $currentlyActivatedTopics)
{
    $arrUnfinishedTopics = array();
     $query = "SELECT distinct a.teacherTopicCode, teacherTopicDesc FROM adepts_teacherTopicMaster a, adepts_teacherTopicStatus b WHERE a.teacherTopicCode=b.teacherTopicCode AND userID=$userID and   
                              isnull(result) AND isnull(classLevelResult) AND a.teacherTopicCode NOT IN ('".implode("','",$currentlyActivatedTopics)."')";
      $result = mysql_query($query) or die("2".mysql_error());
      while($line = mysql_fetch_array($result))
          array_push($arrUnfinishedTopics, $line[1]);
    return $arrUnfinishedTopics;      
}
function getCurrentActivatedTopics($schoolCode, $class, $section)
{
     $arrActiveTT = array();
    $query = "SELECT distinct teacherTopicCode FROM adepts_teacherTopicActivation WHERE schoolCode=$schoolCode AND class=$class AND deactivationDate='0000-00-00'";
     if($section!="")
          $query .= " AND section='$section'";
    $result = mysql_query($query) or die("1".mysql_error());
     while($line = mysql_fetch_array($result))
      {
          array_push($arrActiveTT, $line[0]);    
      }
      return $arrActiveTT;
}
function getFailedLU($userID, $startDate, $endDate)
{
     $failedLU = array();   
    $query = "SELECT distinct a.ttAttemptID, b.clusterCode, cluster FROM adepts_teacherTopicClusterStatus a, adepts_clusterMaster b WHERE userID=$userID AND a.clustercode=b.clusterCode and clusterAttemptNo=4 and result='FAILURE' 
                          AND a.lastModified>='$startDate' AND a.lastModified<='$endDate 23:59:59' ";    
    $result = mysql_query($query) or die(mysql_error());
    while($line = mysql_fetch_array($result))
     {
              $q = "SELECT count(ttAttemptID) FROM adepts_teacherTopicStatus WHERE ttAttemptID=".$line[0]." AND find_in_set('".$line[1]."',failedClusters)>0";              
              //echo $q;
              $r = mysql_query($q) or die(mysql_error());
              $l = mysql_fetch_array($r);
              if($l[0]>0)
                  array_push($failedLU,$line[2]);
      }   
      return $failedLU;      
}

function getTimeSpent($userID, $startDate, $endDate)
{
     $startDate_int = str_replace("-","",$startDate);
     $endDate_int =  str_replace("-","",$endDate);
    $dateArray  = array();
    $query = "SELECT DISTINCT sessionID, startTime, endTime, tmLastQues FROM adepts_sessionStatus  WHERE  userID=$userID AND startTime_int >=$startDate_int AND startTime_int <= $endDate_int";
    
	$time_result = mysql_query($query) or die($query."<br/>".mysql_error());
	$timeSpent = 0;         
	while ($time_line = mysql_fetch_array($time_result))
	{             
	    $dateStr = substr($time_line[1],0,10);
            	if(!in_array($dateStr,$dateArray))
            		array_push($dateArray, $dateStr);
            	$startTime = convertToTime($time_line[1]);
                  
            	if($time_line[2]!="")
            		$endTime = convertToTime($time_line[2]);
            	else
            	{                           
            		if($time_line[3]=="")
            			continue;
            		else
            			$endTime = convertToTime($time_line[3]);
            	}
            	$timeSpent = $timeSpent + ($endTime - $startTime);	//in secs
	}
	$daysLoggedIn = count($dateArray);         
	return $timeSpent;
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

function getLearningUnitsCovered($userID,$startDate,$endDate)
{
        
	$arrayCluster		=	array();
	$arrayClusterCode	=	array();
	$arrayNeedAttention	=	array();
	$topicsAttemptedArray = "";
	$clusterOld	=	array();
	$disp	=	'';

          $query = "SELECT distinct teacherTopicCode FROM adepts_teacherTopicStatus WHERE  userID=$userID";
	$topic_result = mysql_query($query) or die(mysql_error());	
         while($topic_line = mysql_fetch_array($topic_result))
              $topicsAttemptedArray[]	=	$topic_line[0];
			  
	if($topicsAttemptedArray != ""){
		$topicsAttempted		=	implode("','",$topicsAttemptedArray);
	}else{
		$topicsAttempted		=	"";
	}
	

	//Select all learning unit which are complete in the given duration
	$sq1	=	"SELECT DISTINCT D.teacherTopicDesc,B.cluster,B.clusterCode
				 FROM adepts_teacherTopicClusterStatus A , adepts_clusterMaster B , adepts_teacherTopicStatus C , adepts_teacherTopicMaster D
				 WHERE A.userID='$userID' AND A.result='SUCCESS' AND A.clusterCode=B.clusterCode AND A.ttAttemptID=C.ttAttemptID
				 	AND C.teacherTopicCode=D.teacherTopicCode AND C.teacherTopicCode IN ('$topicsAttempted') AND A.lastModified BETWEEN '$startDate' AND '$endDate 23:59:59'";
	$rs1	=	mysql_query($sq1);
	while ($rw1=mysql_fetch_assoc($rs1))
	{
		$arrayClusterCode[]	=	$rw1['clusterCode'];
		$teacherTopicDesc	=	$rw1['teacherTopicDesc'];
		if (array_key_exists($teacherTopicDesc, $arrayCluster))
		{
			$cluster	=	$rw1['cluster'];
			$arrayCluster[$teacherTopicDesc]['clusterCleared']	.=	 $cluster."--";
		}
		else
		{
			$cluster	=	$rw1['cluster'];
			$arrayCluster[$teacherTopicDesc]['clusterCleared']	=	$cluster."--";
		}
	}

	//Remove -- at the end of cluster cleared.
	foreach ($arrayCluster as $tt=>$arrTemp)
	{

	    $arrayCluster[$tt]['clusterCleared'] = substr( $arrTemp['clusterCleared'],0,-2);
	}

	$clusterCodeStr	=	implode("','", $arrayClusterCode);
	if ($clusterCodeStr!='')
	{
		//select learning unit which are completed before the given duration from above data row list
		$sq	=	"SELECT distinct B.cluster FROM adepts_teacherTopicClusterStatus A , adepts_clusterMaster B
				 WHERE userID='$userID' AND result='SUCCESS' AND B.clusterCode IN ('$clusterCodeStr') AND A.clusterCode=B.clusterCode
				 AND A.lastModified < '$startDate'";
		$rs	=	mysql_query($sq);
		while ($rw=mysql_fetch_assoc($rs))
		{
			//remove previously comleted clusters from array
			$clusterOld[]	=	$rw['cluster'];
		}
	}

	//$arrayLearningUnitDetails	=	clusterNeedAttention($userID,$startDate,$endDate,$arrayCluster,$topicsAttempted);


	$perFormanceDetailsTable = "";
	$disp = "";
	foreach ($arrayCluster as $topicName=>$clusterName)
	{
	    $clusterClearedHTML = $clusterNeedingAttentionHTML = "";
		$clusterCleared	=	$clusterName['clusterCleared'];
		if ($clusterCleared!='' )
		{
			$clusterDisp	=	explode("--",$clusterCleared);
			$clusterDisp	=	array_unique($clusterDisp);
			$clusterDisp	=	array_diff($clusterDisp,$clusterOld);

			if(count($clusterDisp)>0)
			{
			    $clusterClearedHTML	.=	"<tr><th align='left' colspan='3'>&nbsp;&nbsp;&nbsp;&nbsp;Concepts cleared</th></tr>";
    			foreach ($clusterDisp as $cluster)
    			{
    				if($cluster!='')
    					$clusterClearedHTML	.=	"<tr><td width='2%'></td><td align='left' colspan='2'><li>$cluster</li></td></tr>";
    			}
			}
			$clusterCleared	=	'';
		}

		/*$clusterNeedAttention	=	$clusterName['clusterNeedAttention'];
		if ($clusterNeedAttention!='')
		{
			$clusterNeedingAttentionHTML	.=	"<tr><th align='left' colspan='3'>&nbsp;&nbsp;&nbsp;&nbsp;Concepts needing attention</th></tr>";
			$clusterNeedAtt			=	explode("--",$clusterNeedAttention);
			$clusterNeedAtt			=	array_unique($clusterNeedAtt);
			foreach ($clusterNeedAtt as $cluster1)
			{
				if($cluster1!='')
					$clusterNeedingAttentionHTML	.=	"<tr><td width='2%'></td><td align='left' colspan='2'><li>$cluster1</li></td></tr>";
			}
			$clusterNeedAttention	=	'';
		}*/
		if($clusterClearedHTML!="" || $clusterNeedingAttentionHTML!="")
		{
		    $disp.=	"<tr><th align='left' width='5%'>Topic:</th><td align='left' colspan='2'>$topicName</td></tr>";
		    $disp .= $clusterClearedHTML;
		    //$disp .= $clusterNeedingAttentionHTML;
		}
	}
	if($disp!="")
	{
    	$perFormanceDetailsTable	=	"<br/><table border='0' width='80%' style='margin-top: -16px;'><tr><th colspan='3' align='left'>Cleared the following Learning units</th></tr>";
    	$perFormanceDetailsTable .= $disp;
    	$perFormanceDetailsTable	.=	"</table>";
	}
	return $perFormanceDetailsTable;
}

function getNoOfSparkies($userID, $childClass, $startDate, $endDate)
{
        $arrRewards = array();
	$noOfSparkies = 0;
	$query = "SELECT sum(noOfJumps) sparkies FROM adepts_sessionStatus WHERE userID=$userID AND startTime_int>=".str_replace("-","",$startDate).'  AND startTime_int<='.str_replace("-","",$endDate) ;
	$result = mysql_query($query);
	$line = mysql_fetch_array($result);
	if($line['sparkies']!="")
		$noOfSparkies = $line['sparkies'];
	/*if($subjectNo==2)
	{
		$query = "SELECT sum(noOfSparkies) sparkies FROM adepts_revisionSessionStatus WHERE userID=$userID";
		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		if($line['sparkies']!="")
			$noOfSparkies += $line['sparkies'];
	}*/
	if($childClass>=8)
         {
       		$noOfSparkies = $noOfSparkies * 10; //1 Sparkie = 10 Reward points
                   $arrRewards["type"] = "Reward points";
                   $arrRewards["number"] = $noOfSparkies;
        }
        else {
               $arrRewards["type"] = ($noOfSparkies>1?"Sparkies":"Sparkie");
               $arrRewards["number"] = $noOfSparkies;
         }
	return $arrRewards;
}

function isAllowedDeactivatedTopicsForHome($schoolCode)
        {
            $allowed = 0;
            $query = "SELECT allowDeactivatedTopicsAtHome FROM adepts_schoolRegistration WHERE school_code=$schoolCode";
            $result = mysql_query($query) or die(mysql_error());
            if(mysql_num_rows($result)>0)
            {
                $line = mysql_fetch_array($result);
                if($line[0]==1) //i.e. admin has allowed deactivated topics to be visible at home
                    $allowed =1;
            }
            return $allowed;
        }

function clusterNeedAttention($userID,$startDate,$endDate,$arrayCluster,$topicsAttempted)
{

	$sq	=	"SELECT failedClusters,teacherTopicCode,ttAttemptID FROM adepts_teacherTopicStatus
			 WHERE userID='$userID' AND failedClusters!='' AND teacherTopicCode IN ('$topicsAttempted') AND lastModified > '$startDate'";
	$rs	=	mysql_query($sq);
	$num=	mysql_num_rows($rs);
	if($num!='')
	{
		while ($rw=mysql_fetch_assoc($rs))
		{
			$teacherTopicCode	=	$rw['teacherTopicCode']; //teacher topic code
			$ttAttemptID		=	$rw['ttAttemptID'];		//tt atemptd id
			$failedClusters		=	$rw['failedClusters'];
			$failedClusters		=	str_ireplace(",", "','", $failedClusters);

			$sq1	=	"SELECT B.cluster,C.teacherTopicDesc FROM adepts_teacherTopicClusterStatus A , adepts_clusterMaster B , adepts_teacherTopicMaster C
						 WHERE A.userID='$userID' AND A.clusterCode IN ('$failedClusters') AND A.clusterAttemptNo = 4 AND A.clusterCode=B.clusterCode
						 AND C.teacherTopicCode='$teacherTopicCode' AND A.ttAttemptID='$ttAttemptID' AND A.result='FAILURE' AND A.lastModified BETWEEN '$startDate' AND '$endDate 23:59:59'";
			$rs1	=	mysql_query($sq1);
			$num1	=	mysql_num_rows($rs1);
			if ($num1!='')
			{
				while ($rw1=mysql_fetch_assoc($rs1))
				{
					$teacherTopicDesc	=	$rw1['teacherTopicDesc'];

					if (array_key_exists($teacherTopicDesc, $arrayCluster))
					{
						$cluster	=	$rw1['cluster'];
						$arrayCluster[$teacherTopicDesc]['clusterNeedAttention']	.=	 $cluster."--";
					}
					else
					{
						$cluster	=	$rw1['cluster'];
						$arrayCluster[$teacherTopicDesc]['clusterNeedAttention']	=	$cluster."--";
					}
				}
			}
		}
	}

	return $arrayCluster;
}



?>