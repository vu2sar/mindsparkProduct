<?php
define('TBL_HOME_USAGE', "adepts_homeSchoolUsage");
define( 'TBL_SESSION_STATUS', "adepts_sessionStatus" );
define( 'TBL_QUES_ATTEMPT', "adepts_teacherTopicQuesAttempt" );
define("TBL_CLUSTER_MASTER", "adepts_clusterMaster");
define("TBL_TT_ACTIVATION", "adepts_teacherTopicActivation");
define( 'TBL_TOPIC_STATUS', "adepts_teacherTopicStatus" );
define( 'TBL_CLUSTER_STATUS', "adepts_teacherTopicClusterStatus" );
define("TBL_TOPIC_MASTER", "adepts_teacherTopicMaster"); //change name
define("SUBJECTNO", 2);
function getIntDateRange($date) {
	return str_replace('-', '', $date);
}

function getcountofTopicsAndQues($studentID, $class, $section, $startDate, $endDate)
{
 $totalcountSummary=array();
 $query="Select count(qCode) as totalques, count(DISTINCT clusterCode) as totalclusters, count(DISTINCT teacherTopicCode) as totaltopics from adepts_teacherTopicQuesAttempt_class$class
 where userID=".$studentID." and attemptedDate >= '".$startDate."' and attemptedDate <='".$endDate."'";

 $result=mysql_query($query) or die (mysql_error());
  while($line=mysql_fetch_assoc($result))
  { 
     $totalcountSummary['totalques']=$line['totalques'];
     $totalcountSummary['totalclusters']=$line['totalclusters'];
     $totalcountSummary['totaltopics']=$line['totaltopics'];
  }
  
  return $totalcountSummary;
}

function getTimeSpentHomeAndSchool($studentID, $class, $section, $startDate, $endDate) {
$usagehomeandschool=array();

$query="Select sum(timeSpent) as timeSpent,flag from ".TBL_HOME_USAGE." where userID= $studentID and class=$class 
and section='$section' and startTime_int >= ".getIntDateRange($startDate)." and startTime_int <= ".getIntDateRange($endDate)." 
group by flag";
//echo $query;
$result = mysql_query($query) or die (mysql_error());
$noofrow=mysql_num_rows($result);
$homespent=0;
$schoolspent=0;
		if($noofrow>0){
			while($line=mysql_fetch_assoc($result))
			{ 
				  if($line['flag']=='home')
				  $homespent= ($line['timeSpent']!="") ? $line['timeSpent']:0;
				  if($line['flag']=='school')
				  $schoolspent=($line['timeSpent']!="")?$line['timeSpent']:0;		 
				 $usagehomeandschool['home']=($homespent!="")?$homespent:0;
				 $usagehomeandschool['school']=($schoolspent!="")?$schoolspent:0;		 
			}
		}
return json_encode($usagehomeandschool);
}

function getTimeSpentAcrossTopics($studentID,$class,$startDate,$endDate)
{
	$timeSpentAcrosstopics=array();
	$query="select am.teacherTopicDesc,ac.teacherTopicCode,SUM(S+timeTakenForExpln)as timeSpent 
	from adepts_teacherTopicQuesAttempt_class".$class." as ac,
	adepts_teacherTopicMaster as am
	where userID=".$studentID." and attemptedDate >= '".$startDate."' and attemptedDate <= '".$endDate."' and 
    am.teacherTopicCode=ac.teacherTopicCode
    group by ac.teacherTopicCode";
    //echo $query;
    $result = mysql_query($query) or die (mysql_error());
	$noofrow=mysql_num_rows($result);
	if($noofrow>0){
		while($line=mysql_fetch_assoc($result))
		{ 
			$timeSpentAcrosstopics[$line['teacherTopicDesc']]=$line['timeSpent'];
			//$timeSpentAcrosstopics[$j]['TimeSpent']=;
			
		}
	}
  return json_encode($timeSpentAcrosstopics);
}

function getTimeSpentActivitiesAndQuestions($studentID,$class,$startDate,$endDate)
{
	$timeSpentActQuest=array();
	$query="Select COALESCE(sum(timeTaken)) as timeSpent from adepts_userGameDetails 
    where userID=".$studentID."  and attemptedDate >= '".$startDate."' and attemptedDate <= '".$endDate."'";
 	$result = mysql_query($query) or die (mysql_error());
	//echo $query;
		while($line=mysql_fetch_assoc($result))
		{ if($line['timeSpent']!="")
			$timeSpentActQuest['Activities']=$line['timeSpent'];			
		}
	
	$query2="select COALESCE(SUM(S+timeTakenForExpln))as timeSpent 
    from adepts_teacherTopicQuesAttempt_class".$class." as ac
    where userID=$studentID and attemptedDate >= '".$startDate."' and attemptedDate <= '".$endDate."'";
	//echo $query2; 
	$result2 = mysql_query($query2) or die (mysql_error());

		while($line=mysql_fetch_assoc($result2))
		{   if($line['timeSpent']!="")
			$timeSpentActQuest['Questions']=$line['timeSpent'];			
		}
        
 return json_encode($timeSpentActQuest);
}

function categorizeProgress($topicArr) {
	$lowcategory = 0;
	$goodcategory=0;
	$greatcategory=0;
	$category=array();
	
	for($i=0; $i<count($topicArr); $i++)
	{		
	if($topicArr[$i]['progress'] >=0 && $topicArr[$i]['progress'] < 30) {
		$lowcategory++;
	} elseif ($topicArr[$i]['progress']>30 && $topicArr[$i]['progress']<80) {
		$goodcategory++;
	} elseif ($topicArr[$i]['progress']>=80 && $topicArr[$i]['progress']<=100) {
		$greatcategory++;
	} 

	}
$category=array("great"=>$greatcategory,"good"=>$goodcategory,"low"=>$lowcategory);	
	return $category;
}


function getTopicProgressAcrossCategories($studentID,$class,$startDate,$endDate)
{
 $topicProgressCat=array();
 $topicArray=array();
 $topics=array();
 $query="Select  DISTINCT(teacherTopicCode) as ttCode from adepts_teacherTopicQuesAttempt_class".$class." 
 where userID=".$studentID." and attemptedDate >='".$startDate."' and attemptedDate <= '".$endDate."'";
//echo $query;
 $result = mysql_query($query) or die (mysql_error());

 		while($line=mysql_fetch_assoc($result))
		{ 
			$topics[]=$line['ttCode'];			
		}

$query2="select teacherTopicCode,  COALESCE(MAX(progress)) as prg from adepts_teacherTopicStatus where teacherTopicCode IN ('".implode("','", $topics)."') 
and userID=".$studentID." GROUP BY teacherTopicCode";
//echo $query2;
$result = mysql_query($query2) or die(mysql_error());
					while ($row = mysql_fetch_assoc($result)) {
					$topicArray[]=array("topic_id"=>$row['teacherTopicCode'],"progress"=>$row['prg']);
					
					}
$catprogress=array();					
$catprogress=categorizeProgress($topicArray);
return json_encode($catprogress);				

}




function getNoofQuesHomeSchool($studentID,$startDate,$endDate)
{

$getquesthomeschool=array();
$homespent=0;$schoolspent=0;
$query="SELECT flag, COUNT(*) ,sum(IF (ISnull(totalQ),0,totalQ)) sum FROM adepts_sessionStatus as ast, adepts_homeSchoolUsage as ahs
where ast.userID=".$studentID." and ast.startTime_int >= ".getIntDateRange($startDate)." and ast.startTime_int <= ".getIntDateRange($endDate)."  
and ahs.sessionID=ast.sessionID
group by flag";
//echo $query;
$result = mysql_query($query) or die(mysql_error());

while($row = mysql_fetch_assoc($result)) 
{
  if($row['flag']=='home')
  $homespent= $row['sum'];
  if($row['flag']=='school')
  $schoolspent=$row['sum'];	 
	 $getquesthomeschool['home']=$homespent;
	 $getquesthomeschool['school']=$schoolspent;
}
return json_encode($getquesthomeschool);

}

function getNoofQuestAcrossTopics($studentID,$class,$startDate,$endDate)
{
  $getQuestAcrossTopics=array();
  $query="select am.teacherTopicDesc,ac.teacherTopicCode, count(*) as cnt
	from adepts_teacherTopicQuesAttempt_class".$class." as ac,
	adepts_teacherTopicMaster as am
	where userID=".$studentID." and attemptedDate >= '".$startDate."' and attemptedDate <= '".$endDate."' 
	and am.teacherTopicCode=ac.teacherTopicCode
    group by ac.teacherTopicCode";
	
	//echo $query;
    $result = mysql_query($query) or die(mysql_error());

  while($row = mysql_fetch_assoc($result)) 
  {
 	$getQuestAcrossTopics[$row['teacherTopicDesc']]=$row['cnt'];
  }
  
  return json_encode($getQuestAcrossTopics);

}


function timeSpentperDayBarChart($studentID,$startDate,$endDate)
{
//$studentID=126449;
	//$startDate='2015-01-22';
	//$endDate='2015-04-28';
	
	$timespentBar=array();
	$tabledataArray=array();

	$query="SELECT startTime_int as day, SUM(TIME_TO_SEC(TIMEDIFF(endTime,startTime))) as timespentperday FROM
			adepts_sessionStatus WHERE userID=".$studentID." and startTime_int>= ".getIntDateRange($startDate)."  AND startTime_int <=".getIntDateRange($endDate)." 
		    GROUP BY startTime_int DESC";


 	/*$query="Select startTime_int as day,SUM(timeSpent) as timespentperday from adepts_homeSchoolUsage 
where userID=".$studentID." and startTime_int >= ".getIntDateRange($startDate)." and startTime_int <= ".getIntDateRange($endDate)."  
group by startTime_int order by startTime_int DESC";*/
 //	echo $query;
 	$result = mysql_query($query) or die(mysql_error());


	while($row = mysql_fetch_assoc($result)) 
	{
	  $timespentBar[$row['day']]=$row['timespentperday'];
	}

	
	$date1=date_create($startDate);
	$date2=date_create($endDate);

	$diff=date_diff($date1,$date2);
	$day_diff = ($diff->format("%a")*1) +1;
	$tabledata=array();
	//echo "Diff ".$day_diff;
	if($day_diff <=7)
	{   //echo "Start".$startDate. " Date ";
		for($k=0; $k<$day_diff;$k++)
		{
			//$d=getIntDateRange(date($endDate, strtotime("-".$k." days")));
			$d=getIntDateRange(date("Y-m-d",strtotime(($endDate) ."-".$k." days")));
			$displaydate=date("j M Y",strtotime(($endDate) ."-".$k." days"));
			//echo "Dis".$d."Change to ".$displaydate."\n";
			//echo "$k ".$d."\n";
			$tabledata[$displaydate]=isset($timespentBar[$d])?$timespentBar[$d]:0;
		}
		//print_r($tabledata);
		$tdataType='days';
	}
	else if($day_diff>7 && $day_diff<40)
	{
		$lastWeekEnding=0;$tempArr=array();
		for($k=0; $k<$day_diff;$k++)
		{
			$d=getIntDateRange(date("Y-m-d",strtotime(($endDate) ."-".$k." days")));
			//$dispdate=getIntDateRange(date("j M Y"),strtotime(($endDate)."-".$k." days")));
			//$d=getIntDateRange(date($endDate, strtotime("-".$k." days")));
			if($k%7==0)
			{
				$weekEnding=$d;
				if ($lastWeekEnding!=0) 
					$tempArr['Week-'.$lastWeekEnding]['dateRange']=getIntDateRange(date("Y-m-d",strtotime(($endDate) ."-".($k-1)." days"))).'-'.$lastWeekEnding;
				$tempArr['Week-'.$weekEnding]=array('dateRange'=>'-'.$weekEnding,'time'=>0);
				$lastWeekEnding=$weekEnding;
		    }
		   
			$tempArr['Week-'.$weekEnding]['time']+=isset($timespentBar[$d])?$timespentBar[$d]:0;
		}	
		$dispdate=getIntDateRange(date("j M Y",strtotime(($endDate) ."-".($k-1)." days")));	
		$tempArr['Week-'.$lastWeekEnding]['dateRange']=getIntDateRange(date("Y-m-d",strtotime(($endDate) ."-".($k-1)." days"))).'-'.$lastWeekEnding;

		
		ksort($tempArr);

		foreach($tempArr as $key => $value) {
			$dateArr=explode('-',$value['dateRange']);
			$startDateRange=date("j M Y",strtotime($dateArr[0]));
			$endDateRange=date("j M Y",strtotime($dateArr[1]));
			//echo "THE".$startDateRange.'~'.$endDateRange;
			$displayRange=$startDateRange.'-'.$endDateRange;
			//print_r($dateArr);
			$tabledata[substr($key,0,4).'| '.$displayRange]=$value['time'];
		}
	
		$tdataType='weeks';
		
	}
	else
	{	
		$m=-1;
		for($k=0; $k<$day_diff;$k++)
		{
		//	$d=getIntDateRange(date($endDate, strtotime("-".$k." days")));
			$d=getIntDateRange(date("Y-m-d",strtotime(($endDate) ."-".$k." days")));
			$displaydate=date("j M Y",strtotime(($endDate) ."-".$k." days"));

			if(!array_key_exists(floor($d/100).'', $tabledata))
			{
				$monthGroup=floor($d/100);
				$m++;
				$tabledata[$monthGroup]=0;
		    }
			$tabledata[$monthGroup]+=isset($timespentBar[$d])?$timespentBar[$d]:0;
		}
		
		$tempArr= array();
		$jj=0;
		foreach($tabledata as $key => $value) {
			$jj++;

			$year=substr($key,0,-2);
			$mnth=substr($key,4,6);
			$dispmnth=date("M ",mktime(0,0,0,$mnth,10));
			$labelmonthyear="Month ".$dispmnth.$year;
			$tempArr[$mnth]["$labelmonthyear"]=$value;	
			//echo "Disp ".$key." ".$dispyear."\n"." month".$dispmnth."\n";
		}
		
		ksort($tempArr);
		unset($tabledata);
		$ttArr=array();
		$ttArr=array_values($tempArr);
		$j=0;
		foreach($ttArr as $row)
		{   foreach($row as $v=>$line)
			{
				$j++;
				//$str_month=str_replace(":","-".$j,$v);
				$tabledata[$v]=$line;

			}
		}
	
		$tdataType='months';
	}
	$tabledataArray=array("tableData"=>$tabledata,"tag"=>$tdataType);
 
	return $tabledataArray;
}


function getAccuracyForStudentCategories($studentID, $class, $startDate, $endDate) {
	$overallAccuracySummary = array();
	$numOfStudents=1;
	$clusterCodeArr = getClustersAttemptedBySection2($studentID, $startDate, $endDate, $class);
	$accuracyArr = getAccuracyAndUsageForClusters3($clusterCodeArr, $studentID, $class, $startDate, 
	$endDate, $numOfStudents);
	$overallAccuracySummary=getAccuracyJson($accuracyArr);
	return json_encode($overallAccuracySummary);
}

function getAccuracyJson($accuracyArr)
{
	$accuracyjson=array();
	$goodcategory=0;
	$lowcategory=0;
	$greatcategory=0;

   foreach($accuracyArr as $key=>$value)
   { 
	 switch($accuracyArr[$key]['category'])
	 {
	  case 'good' : $goodcategory++;break;
	  case 'great': $greatcategory++; break;
	  case 'low' :  $lowcategory++; break;
	 }
   }

   $category=array("great"=>$greatcategory,"good"=>$goodcategory,"low"=>$lowcategory);	
   return $category;
}


function getClustersAttemptedBySection2($studentID, $startDate, $endDate, $class) {
  $clusterCodeArr = array();
  $query = "SELECT DISTINCT a.clusterCode AS clusterCode
  FROM ".TBL_QUES_ATTEMPT."_class$class a INNER JOIN ".TBL_SESSION_STATUS." b ON a.sessionID = b.sessionID WHERE b.startTime_int >= ".getIntDateRange($startDate)." AND b.startTime_int <= ".getIntDateRange($endDate)." AND a.userID =".$studentID."";
  //echo $query;
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $clusterCodeArr[] = $line['clusterCode'];
	}
	return $clusterCodeArr;
}
function implodeArrayForQueryResult($arr) {
	
	if(isset($arr) && (is_array($arr))){
	 $str = "'" . implode("','", $arr) . "'";
    }
    else
    	$str =$arr;
	return $str;
}


function getAccuracyAndUsageForClusters3($clusterCodeArr, $studentID, $class, $startDate, $endDate, 
$numOfStudents) 
{
	$clusterUsageArr = array();
	$query_accuracy = "SELECT a.clusterCode, COUNT(a.srno) AS total, SUM(IF(a.R = 1, 1, 0)) as correct, COUNT(DISTINCT a.userID) as userCount, a.teacherTopicCode AS ttCode 
    FROM ".TBL_QUES_ATTEMPT."_class$class a INNER JOIN ".TBL_SESSION_STATUS." b ON a.sessionID = b.sessionID  WHERE a.clusterCode IN ( ". implodeArrayForQueryResult($clusterCodeArr) ." ) 
		AND a.userID =".$studentID." 
		GROUP BY a.clusterCode";
	
		//print_r($query_accuracy);
		//exit;

	$result_accuracy = mysql_query($query_accuracy) or die(mysql_errno());

	while($line=mysql_fetch_assoc($result_accuracy)) {
			$clusterUsageArr[$line['clusterCode']]['attempted'] = $line['total'];
			$clusterUsageArr[$line['clusterCode']]['correct'] = $line['correct'];
			$clusterUsageArr[$line['clusterCode']]['accuracy'] = round($line['correct']*100/$line['total'], 2);
			$clusterUsageArr[$line['clusterCode']]['userCount'] = $line['userCount'];
			$clusterUsageArr[$line['clusterCode']]['ttCode'] = $line['ttCode'];

			if(isEnoughUsage2($line['userCount'], $numOfStudents)) {
			$clusterUsageArr[$line['clusterCode']]['category'] = categorizeAccuracy2($clusterUsageArr[$line['clusterCode']]['accuracy']);
			} else {
			$clusterUsageArr[$line['clusterCode']]['category'] = 'notEnoughUsage';
			}
	}

	$query_cluster = "SELECT a.cluster, a.clusterCode,b.teacherTopicCode,c.teacherTopicDesc FROM ". TBL_CLUSTER_MASTER . " a,adepts_teacherTopicClusterMaster b,adepts_teacherTopicMaster c WHERE a.clusterCode IN (" . implodeArrayForQueryResult($clusterCodeArr) . ") AND a.clusterCode=b.clusterCode AND b.teacherTopicCode=c.teacherTopicCode";

	$result_cluster = mysql_query($query_cluster) or die("error query: $query_cluster");

	while($line=mysql_fetch_assoc($result_cluster)) {
		$clusterUsageArr[$line['clusterCode']]['name'] = $line['cluster'];
		$clusterUsageArr[$line['clusterCode']]['topicName'] = $line['teacherTopicDesc'];
		$clusterUsageArr[$line['clusterCode']]['topicCode'] = $line['teacherTopicCode'];
	}

	return $clusterUsageArr;
}

function isEnoughUsage2($userCount, $numOfStudents) {
	if($userCount/$numOfStudents >= 0.5)
		return TRUE;
	return FALSE;
}

function categorizeAccuracy2($accuracy) {
	$category = "";
	if($accuracy >=0 && $accuracy < 40) {		// for the task 12358
		$category = 'low';
	} elseif ($accuracy>=40 && $accuracy<80) {
		$category = 'good';
	} elseif ($accuracy>=80 && $accuracy<=100) {
		$category = 'great';
	} else {
		$category = 'notEnoughUsage';
	}
	return $category;
}

function mergeProgressArray2($startArr, $endArr, $currentArr = array()) {
	$mergedArr = array();
	$isMergeCurrent = (sizeof($currentArr) > 0)? TRUE : FALSE;
	foreach (array_keys($startArr) as $ttCode) {
		$mergedArr[$ttCode]['startProgress'] = $startArr[$ttCode]['avgProgress'];
		$mergedArr[$ttCode]['endProgress'] = $endArr[$ttCode]['avgProgress'];
		if($isMergeCurrent) {
			$mergedArr[$ttCode]['currentProgress'] = $currentArr[$ttCode]['avgProgress'];	
		}
	
		}
		uasort($mergedArr, "progressArraySortHelper2");
		return $mergedArr;
}

function progressArraySortHelper2($a, $b) {
	if($a['endProgress']-$a['startProgress'] >= $b['endProgress']-$b['startProgress']) 
		return -1;
	 else 
		return 1;
}

function getTeacherTopicProgressDetails($ttCode, $studentID, $cls, $tillDate) {

	$progress = array();
	$higherLevelStudents = array();
	$higherLevelReached = 0;	
	$q = "SELECT distinct flow FROM ".TBL_TOPIC_STATUS." WHERE  userID =".$studentID." AND teacherTopicCode='".$ttCode."'";
  $r = mysql_query($q);
  while($l = mysql_fetch_array($r))
  {
  	$flowN = $l[0];
  	$flowStr = str_replace(" ","_",$flowN);
  	${"objTopicProgress".$flowStr} = new topicProgress($ttCode, $cls, $flowN, SUBJECTNO);
  }

  $sq	=	"SELECT userID, MAX(progress), SUM(noOfQuesAttempted),ROUND(SUM(perCorrect*noOfQuesAttempted)/SUM(noOfQuesAttempted),2),
  		 MAX(ttAttemptNo), GROUP_CONCAT(ttAttemptID), flow 
  		 FROM ".TBL_TOPIC_STATUS." WHERE teacherTopicCode='$ttCode' AND userID =".$studentID." GROUP BY userID";
  $rs	=	mysql_query($sq);
  while($rw=mysql_fetch_array($rs)) {
		$flowK	=	$rw[6];
    $flowK	=	str_replace(" ","_",$flowK);
	$teacherTopicDetails[$rw[0]]["progress"]	=	${"objTopicProgress".$flowK}->getProgressInTT($rw[0], $tillDate);
 	$progress[] = $teacherTopicDetails[$rw[0]]["progress"];
 
  }

  $avgProgress = round(array_sum($progress)/sizeof($studentID),2);
  
  return array("avgProgress" => $avgProgress);
}

function getTeacherTopicProgressForSection2($class, $studentID, $ttCodeArr, $tillDate) {
	$ttProgressArr = array();
	foreach ($ttCodeArr as $ttCode) {	
		$ttProg = getTeacherTopicProgressDetails($ttCode, $studentID, $class, $tillDate);
		$ttProgressArr[$ttCode]['avgProgress'] = $ttProg['avgProgress'];
	
	}

	return $ttProgressArr;
}

function getActiveTopicsForDateRange2($schoolCode, $class, $section, $startDate, $endDate) {
	$activeTopicsArr = array();
	$query = "SELECT teacherTopicCode FROM ".TBL_TT_ACTIVATION." WHERE schoolCode=$schoolCode AND class=$class AND activationDate <= '$endDate' AND (deactivationDate = '0000-00-00' OR (deactivationDate >= '$startDate' AND  deactivationDate <= '$endDate')) ";
	if($section != "") {
			$query .= " AND section =".$section."";
	}
	//echo $query;
	$result = mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $activeTopicsArr[] = $line['teacherTopicCode'];
	}
	return $activeTopicsArr;
}

function isShowCurrentProgress2($endDate) {
	$nowDate = date("Y-m-d");
	if(strtotime($endDate) < strtotime($nowDate))
		return TRUE;
	else 
		return FALSE;
}
function getChangeInTeacherTopicProgress2($studentID, $class, $section, $startDate, $endDate) {
	//$sectionStudentDetails = getStudentDetailsBySection($schoolCode,$class, $section);
	//$studentsArr = array_column($sectionStudentDetails, 'userID');
	$query="Select schoolCode from adepts_userDetails as ad where ad.userID=".$studentID."";
	$result= mysql_query($query) or die(mysql_errno());
	while($line=mysql_fetch_array($result)) {
		 $schoolCode = $line['schoolCode'];
	}
	$ttCodeArr = getActiveTopicsForDateRange2($schoolCode, $class, $section, $startDate, $endDate);
   	
	$startDate = date('Y-m-d', strtotime('-1 day', strtotime($startDate)));

	$ttStartProgress = getTeacherTopicProgressForSection2($class, $studentID, $ttCodeArr, $startDate);
    
	$ttEndProgress = getTeacherTopicProgressForSection2($class, $studentID, $ttCodeArr, $endDate);

	//if(isShowCurrentProgress2($endDate)) {
		$ttCurrentProgress = getTeacherTopicProgressForSection2($class, $studentID, $ttCodeArr,date('Y-m-d'));
		$changeInTopicProgressArr = mergeProgressArray2($ttStartProgress, $ttEndProgress, $ttCurrentProgress);
	/*} else {
		$changeInTopicProgressArr = mergeProgressArray2($ttStartProgress, $ttEndProgress);	
	}
*/
	return $changeInTopicProgressArr;
}

function getTopicDescFromTTCodes2($ttCodeArr) {
	$topicNameArr = array();
		$query = "SELECT teacherTopicCode, teacherTopicDesc FROM ".TBL_TOPIC_MASTER." WHERE teacherTopicCode IN (". implodeArrayForQueryResult($ttCodeArr) .")";
	$result = mysql_query($query) or die (mysql_errno());
	while($line = mysql_fetch_assoc($result)) {
		$topicNameArr[$line['teacherTopicCode']] = $line['teacherTopicDesc'];	
	}
	return $topicNameArr;
}

function getTopicProgressSummaryDetailsChart($studentID, $class, $section, $startDate, $endDate) {
	
	
	$summary = array();
	$higherLevelStudents = array();
	$totalHigherLevelReached = 0;
$changeInTopicProgressArr = getChangeInTeacherTopicProgress2($studentID, $class, $section, $startDate, $endDate);

	$ttCodeArr = array_keys($changeInTopicProgressArr);
	$ttDescArr = getTopicDescFromTTCodes2($ttCodeArr);
	// print_r($changeInTopicProgressArr);
	foreach ($ttCodeArr as $ttCode) {
		$ttObj = array();
		$ttObj['ttCode'] = $ttCode;
		$ttObj['ttDesc'] = $ttDescArr[$ttCode];
		$ttObj['startProgress'] = $changeInTopicProgressArr[$ttCode]['startProgress'];
		$ttObj['endProgress'] = $changeInTopicProgressArr[$ttCode]['endProgress'];
		if(array_key_exists('currentProgress', $changeInTopicProgressArr[$ttCode])) {
			$ttObj['currentProgress'] = $changeInTopicProgressArr[$ttCode]['currentProgress'];
		}
		$summary[] = $ttObj;
		
	}
	//echo "Summary Details";
	//print_r($summary);
	//$namesArr = getNamesFromUserIDs($higherLevelStudents);
	return json_encode(array("ttProgress" => $summary));
}
