<?php
error_reporting(1);
set_time_limit(0);
@include("check1.php");
include_once("constants.php");
include("functions/functions.php");
include("classes/clsUser.php");
include_once("classes/clsTopicProgress.php");
include_once ("classes/clsTeacherTopic.php");
if( !isset($_SESSION['userID'])) {
	header( "Location: error.php");
}
$userID = $_SESSION['userID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];

$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$category 	   = $objUser->category;
$subcategory   = $objUser->subcategory;

$teacherTopicCode = $_POST['ttCode'];
$isDeactive = $_POST['isDeactive'];
$higherLevel = $_POST['higherLevel'];
$enableActionButton = 1;
/* condition to enable learn button*/
if(strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"School")==0 || strcasecmp($subcategory,"Home Center")==0))
{
	if($isDeactive)
	{
		 $isAllowedDeactivatedTopicsForHomeUse = isAllowedDeactivatedTopicsForHome($schoolCode, $childClass, $childSection);
	    if($isAllowedDeactivatedTopicsForHomeUse)
	    {
			$isHomeUsage = isHomeUsage($schoolCode, $childClass);
			if(!$isHomeUsage)
				$enableActionButton = 2;
		}
		else
		{
			$enableActionButton = 3;
		}   
    }    
}

$flow = getTopicFlow($schoolCode,$childClass,$childSection,$teacherTopicCode);
$clusterDetails = $revision = array ();
$srno = $conceptsLearned = $totalDailyPractise=$completedDailyPractise=$notAttempteDailyPractice=$dailyDrillAvailableToday=0;
$topicAttemptDetails = getAttemptDetailsOnTopic($userID,array(0=>$teacherTopicCode));
$today = date("Y-m-d");
$lastMon = date("Y-m-d",strtotime("last Sunday"));
$arrActivities = getActivities($childClass,"",$objUser->packageType);	
$timeSpentOnActivitiesThisWeek = getTimeSpentOnActivities($userID,$arrActivities,$lastMon,$today);	
$totalTimeSpentThisWeek = array_sum($timeSpentOnActivitiesThisWeek);	
$completedClusters = getActiveTopicsAndCompletedClusters($schoolCode,$childClass,$childSection,$userID,$category,$subcategory,$teacherTopicCode);
$objTT = new teacherTopic($teacherTopicCode,$childClass,$flow);

$noOfClusters = $objTT->getNoOfClustersOfLevel($childClass);
/* if no of clusters is zero than we will fetch clusters for childclass-1  
	if after that also clusters are empty than we will fetch clusters of childClass+1 */
if($noOfClusters != 0)
{
	$clusterArray = $objTT->getClustersOfLevel($childClass);
}
else
{	
	for($i=$childClass-1 ; $i>0 ; $i--)
	{		
		$clusterArray = $objTT->getClustersOfLevel($i);
		if(!empty($clusterArray))
			break;
	}
	if(empty($clusterArray))
	{
		for($j=$childClass+1 ; $j<=10 ; $j++)
		{
			$clusterArray = $objTT->getClustersOfLevel($j);
			if(!empty($clusterArray))
				break;
		}
	}
}
$ttProgress = getProgressInTopic($teacherTopicCode, $childClass, $userID, 'dashboard');
$ttattemptID = $ttProgress['ttAttemptID'];
/* fetch cluster description */
$clusters = getClusterDetails($clusterArray);
/* fetch cluster overall sdl details for cluster attempt IDs */
$sdlPerCluster = getSDLDetails($clusterArray,$childClass,$teacherTopicCode,$userID);
$clusterAccuray = getAccuracyPerCluster($childClass,$teacherTopicCode,$userID);
$rctFlag=checkForRCT($userID);
foreach($clusters as $cluster=>$line)
{				
	$clusterDetails [$cluster] ['clusterDesc'] = $line;
	$clusterDetails [$cluster] ['accuracy'] = !empty($clusterAccuray[$cluster]) ? $clusterAccuray[$cluster] : '';
	$clusterDetails [$cluster] ['clusterStatus'] = isClusterPassedFailedSuccesfully($cluster,$userID,$teacherTopicCode);
	$clusterDetails [$cluster] ['clusterStatusOverall'] = isClusterCompletedSuccesfully($cluster);
	$clusterDetails [$cluster] ['clusterPassedDate'] = $clusterDetails [$cluster] ['clusterStatusOverall'] == 1 ? clusterPassedDate($cluster,$userID) : '';
	$clusterDetails [$cluster] ['numberOfSdls'] = count($sdlPerCluster[$cluster]);
	$clusterDetails [$cluster] ['sdls'] = $sdlPerCluster[$cluster];	
	$clusterDetails [$cluster] ['dailyPractise'] = $rctFlag == 0 ? getDailyPractiseMappedToClusterCode($cluster,$userID) : array();	
	$clusterDetails [$cluster] ['activity'] = getActivitiesMappedToClusterCode($userID,$childClass,$category,$subcategory,$clusterDetails [$cluster] ['clusterStatusOverall'],$cluster,$completedClusters,$totalTimeSpentThisWeek);
	if(!empty($clusterDetails [$cluster] ['dailyPractise']))
	{
		$totalDailyPractise++;
		foreach ($clusterDetails [$cluster] ['dailyPractise'] as $key => $value)
		{
			if($value['status'] == 'completed')
				$completedDailyPractise++;
			if($value['status'] == 'notattempted')
				$notAttempteDailyPractice++;
		}
	}	
	
}	
	/* fetch enrichmments mapped to ttcode */
	$parentTtCode = isCustomizedTopic($teacherTopicCode);	
	$ttActivities = getActivitiesMappedTottCode(round($ttProgress['progress']),$userID,$childClass,$totalTimeSpentThisWeek,$parentTtCode);
	/*counting passed cluster in first attempt*/
	$conceptsLearned = countPassedClusters($ttattemptID);	
	$progressDetails['ttDescription'] = $objTT->ttDescription;
	$progressDetails['overallProgress'] = round($ttProgress['progress']);	
	$progressDetails['totalConcepts'] = count($clusterDetails);
	$conceptsNotLearned = ($progressDetails['totalConcepts']-$conceptsLearned) < 10 && ($progressDetails['totalConcepts']-$conceptsLearned) !=0 ? "0".($progressDetails['totalConcepts']-$conceptsLearned) : ($progressDetails['totalConcepts']-$conceptsLearned);	
	$progressDetails['conceptsLearned'] = $conceptsLearned;
	$progressDetails['conceptsNotLearned'] = $conceptsNotLearned;
	$progressDetails['totalDailyPractise'] = $totalDailyPractise;
	$progressDetails['completedDailyPractise'] = $completedDailyPractise;
	$progressDetails['ttInfo'] = getTTinformation($parentTtCode);
	$topicAccuracy = getNoOfQuesAttemptedInTheTopic($userID, $teacherTopicCode);
	$progressDetails['accuracy'] = !empty($topicAccuracy['perCorrect'])?$topicAccuracy['perCorrect']:0;
	if($progressDetails['overallProgress'] == 100)
	$revision['revisionAccuracy'] = getRevisionDetails($userID,$teacherTopicCode);	

	function getTTinformation($teacherTopicCode)
	{			
		$query = "SELECT A.teacherTopicInfo from adepts_teacherTopicMaster A where A.teacherTopicCode='$teacherTopicCode'";					
		$rs	=	mysql_query($query);
		$rw	=	mysql_fetch_row($rs);
		return $rw[0];
	}
	function isCustomizedTopic($ttCode)
	{
		$parentTT = "";
		$query = "SELECT customTopic, parentTeacherTopicCode FROM adepts_teacherTopicMaster WHERE teacherTopicCode='$ttCode'";		
		$result = mysql_query ( $query );
		$line = mysql_fetch_array ( $result );
		if ($line [0] == 1)
			$parentTT = $line [1];
		else
			$parentTT = $ttCode;
		return $parentTT;
	}
	/*overall accuracy of topic revsion */
	function getRevisionDetails($userID,$teacherTopicCode)
	{
		$revisionAccuracy= 0;
		$query = "SELECT ROUND((SUM(A.R)/COUNT(A.srno))*100) AS revisionAccuracy from adepts_topicRevisionDetails A where A.userID=$userID and A.teacherTopicCode='$teacherTopicCode'";
		$rs	=	mysql_query($query);				
		if($rw	=	mysql_fetch_row($rs))		
			$revisionAccuracy=	$rw[0];

		return $revisionAccuracy;
	}
	function getSDLDetails($clusterArray,$class,$teacherTopicCode,$userID)
	{
		$sdlArray = array();	
		$clusterStr = "'".implode("','", $clusterArray)."'";	
		$query = "SELECT clusterCode,subdifficultylevel,group_concat(qcode)
		FROM   adepts_questions
		WHERE  clusterCode IN($clusterStr) AND status=3
		group by subdifficultylevel,clusterCode order by subdifficultylevel";				
		$result = mysql_query($query) or die("Error in getting SDL details!");
		$srno = 0;
		while ($line = mysql_fetch_array($result))
		{			
			$sdlArray[$line[0]][$line[1]]['qcodeList']= $line[2];	
			$sdlArray[$line[0]][$line[1]]['sdlAccuracy'] = getQuesAccuracy($class,$line[2],$teacherTopicCode,$userID); 		
			$srno++;
		}				
		return $sdlArray;
	}
	/* get SDL accuracy
	if accuracy = 0 than SDL is failed
	else
	SDL is Passed
	*/
	function getQuesAccuracy($class,$qcodeList,$teacherTopicCode,$userID)
	{
		$arrayDetails = ''; 
		$sq	=	"SELECT ROUND((SUM(A.R)/COUNT(A.srno))*100) as SDLAccuracy FROM adepts_teacherTopicQuesAttempt_class$class A, adepts_teacherTopicClusterStatus B,adepts_teacherTopicStatus C
				 WHERE A.clusterAttemptID=B.clusterAttemptID and B.ttAttemptID=C.ttAttemptID and C.teacherTopicCode = '$teacherTopicCode'  and A.qcode IN($qcodeList) and A.userID=$userID";				 				 		 				 					 		
		$rs	=	mysql_query($sq);
		if($rw	=	mysql_fetch_row($rs))
			$arrayDetails=	$rw[0];

		return $arrayDetails;
	}
	/* get overall accuracy per cluster where cluster is completed */
	function getAccuracyPerCluster($class,$teacherTopicCode,$userID)
	{		
		$clusterAccuray = array();			
		$query = "SELECT a.clusterCode,ROUND((SUM(a.R)/COUNT(a.srno))*100) as accuracy FROM adepts_teacherTopicQuesAttempt_class$class a, adepts_teacherTopicClusterStatus b ,adepts_teacherTopicStatus c WHERE a.clusterAttemptID=b.clusterAttemptID AND  b.ttAttemptID=c.ttAttemptID AND c.teacherTopicCode = '$teacherTopicCode' AND b.result IS NOT NULL and b.userID=$userID GROUP BY a.clusterCode"	;		
		$result = mysql_query ( $query );				
		while ($line = mysql_fetch_array($result))
		{
			$clusterAccuray[$line[0]] = $line[1];
		}	
		return $clusterAccuray;					
	}
	
	function getActivitiesMappedTottCode($ttProgress,$userID,$childClass,$totalTimeSpentThisWeek,$teacherTopicCode)
	{				
		$totalTimeAllowedForAcitivitesInaWeek = 60;
		$activitiesArray = array();
		$query = "SELECT gameID, gameDesc,topicCompletion FROM adepts_gamesMaster WHERE teacherTopicCode='$teacherTopicCode' AND live='Live' AND type = 'enrichment' AND find_in_set('$childClass',class)>0";		
		$result = mysql_query ( $query ) or die ( "Error in fetching cluster details" );		
		while ( $line = mysql_fetch_array ( $result ) ) {					
			$activitiesArray [$line [0]] ["desc"] = $line [1];			
			if($totalTimeSpentThisWeek > $totalTimeAllowedForAcitivitesInaWeek)
			{
				$activitiesArray [$line [0]]['locked'] = 1;			
			}
			else if(($line[2] !='' && $line[2] !=0 && $ttProgress>= $line[2]) || $ttProgress == 100)
			{							
				if(isActivityCompleted($line[0],$userID,$childClass))
				{
					$activitiesArray [$line [0]]['locked'] = 2;			
				}
				else
				{
					$activitiesArray [$line [0]]['locked'] = 0;
				}					 				
			}	
			else
			{
				$activitiesArray [$line [0]]['locked'] = 1;
			}
			$activitiesArray [$line [0]]['topicCompletion'] = $line[2] != 0 && $line[2] != ''? $line[2]: 100;
		}		
		return $activitiesArray;
	}
	function getActivitiesMappedToClusterCode($userID,$childClass,$category,$subcategory,$clusterStatus,$clusterCode,$completedClusters,$totalTimeSpentThisWeek,$teacherTopicCode) {			
		$activitiesArray = array ();
		$totalTimeAllowedForAcitivitesInaWeek = 60;
		$query = "SELECT gameID, gameDesc FROM adepts_gamesMaster WHERE linkedToCluster='$clusterCode'      AND live='Live' AND type IN('regular','optional') AND find_in_set('$childClass',class)>0";		
		$result = mysql_query ( $query ) or die ( "Error in fetching cluster details" );		
		while ( $line = mysql_fetch_array ( $result ) ) {
					
			$activitiesArray [$line [0]] ["desc"] = $line [1];				
			if($totalTimeSpentThisWeek > $totalTimeAllowedForAcitivitesInaWeek)
			{
				$activitiesArray [$line [0]]['locked'] = 1;			
			}				
			else
			{
				$activitiesArray [$line [0]]['locked'] = getActivitiesAttemptData($line [0],$userID,$childClass,$category,$subcategory,$clusterStatus,$clusterCode,$completedClusters);
			}			
								
			/* 1 = locked
			   0 = unlocked
			   2 = completed
			*/	
		}
		return $activitiesArray;
	}

	
	function getActivitiesAttemptData($activityID,$userID,$childClass,$category,$subcategory,$clusterStatus,$clusterCode,$completedClusters)
	{						
		if(isActivityCompleted($activityID,$userID,$childClass))		
			$locked = 2;						
		else if (($clusterStatus == 1) || in_array($clusterCode,$completedClusters['completedClusters']))		
			$locked=0;								
		else 
			$locked=1;
			
		return $locked;	
	}
	function isActivityCompleted($activityID,$userID,$childClass)
	 {
	 	$query	= "SELECT count(a.gameID) FROM adepts_userGameDetails a, adepts_gamesMaster b
				 WHERE a.gameID=b.gameID AND userID=$userID AND live='Live' AND a.gameID=$activityID AND a.completed = 1 limit 1";				 					
		$result = mysql_query($query);
		$line = mysql_fetch_row($result);		
		return $line[0];
	 }

	 function getDailyPractiseMappedToClusterCode($clusterCode,$userID) {
		$dailyPrcaticeArray = array ();		
		$query = "SELECT practiseModuleId, description,dailyDrill FROM practiseModuleDetails WHERE linkedToCluster='$clusterCode' AND status='Approved'";		
		$result = mysql_query ( $query ) or die ( "Error in fetching practice module details" );
		while ( $line = mysql_fetch_array ( $result ) ) {
			$dailyPrcaticeArray [$line [0]]  = getDailyPractiseAttemptData($line [0],$userID);
			$dailyPrcaticeArray [$line [0]] ["desc"] = $line [1];
			$dailyPrcaticeArray [$line [0]] ["drill"] = $line [2];
			
		}
		return $dailyPrcaticeArray;
	}
	function getDailyPractiseAttemptData($practiseModuleId,$userID)
	{	 	
	 	$returnArray = $practiseModuleTestStatusIdCompleted= array();
	 	$practiseModuleTestStatusIdIncomplete = 0;		
	 	$query = "SELECT a.id,a.status from practiseModulesTestStatus a JOIN practiseModuleDetails b ON a.practiseModuleId=b.practiseModuleId  where a.userID='$userID' AND a.practiseModuleId = '$practiseModuleId'";	 	
	 	$result = mysql_query ( $query );
		while ( $line = mysql_fetch_array ( $result ))
		{						
			if($line[1] == 'completed')
			{
				$practiseModuleTestStatusIdCompleted[] = $line[0];
			}
			else
			{
				$practiseModuleTestStatusIdIncomplete = $line[0];
			}
					
		}		
		if(!empty($practiseModuleTestStatusIdCompleted))
		{
			/* fetch overall accuracy of all completed dailypractise */
			$practiseModuleTestStatusId = implode(',',$practiseModuleTestStatusIdCompleted);
			$status = 'completed';
			$sq	=	"SELECT  ROUND((sum(if(a.R=1,1,0))/count(a.id))*100) as accuracy
		             FROM   practiseModulesQuestionAttemptDetails  a 
					 WHERE a.userID='$userID' AND a.practiseModuleId  = '$practiseModuleId' AND a.practiseModuleTestStatusId IN ($practiseModuleTestStatusId)";									 				 
			$rs	= mysql_query($sq);
			$rw = mysql_fetch_row($rs);				
			$returnArray['accuracy'] =	$rw[0];	
			$returnArray['status'] = $status;	
		}
		elseif($practiseModuleTestStatusIdIncomplete != 0)
		{			
			$returnArray['accuracy'] =	0;	
			$returnArray['status'] = 'incomplete';	
		}		
	 	else
	 	{
	 		$returnArray['accuracy'] =	'';	
			$returnArray['status'] = 'notattempted';
	 	}
		return $returnArray;
	 }

	 function getTopicFlow($schoolCode,$childClass,$childSection,$teacherTopicCode)
	 {	 
	 	$flow = "MS";	
	 	$sq = "SELECT A.flow
					FROM adepts_teacherTopicActivation A , adepts_teacherTopicMaster B 
					 WHERE A.teacherTopicCode=B.teacherTopicCode AND A.schoolCode=$schoolCode AND A.class=$childClass AND A.section='$childSection' AND A.teacherTopicCode='$teacherTopicCode'";					
		$rs	= mysql_query($sq);
		if($rw = mysql_fetch_row($rs))
			$flow = $rw[0];		
		return $flow;
	 }
	 function getClusterDetails($clusterArray)
	 {
	 	$cluster = array();
	 	$clusterStr = "'".implode("','", $clusterArray)."'";	 	
	 	$query = "SELECT clusterCode,cluster from adepts_clusterMaster where clusterCode IN($clusterStr) ORDER BY FIELD (clusterCode,$clusterStr)";
	 	$result	= mysql_query($query);		
		while ( $line = mysql_fetch_array ( $result ) ) {
			$cluster[$line[0]] = $line[1];			
		}			
		return $cluster;
	 }
	 /* count passed clusters*/
	 function countPassedClusters($ttattemptID)
    {
    	$clusterStr = "'".implode("','", $clusterArray)."'";
    	$query  = "SELECT count(DISTINCT clusterCode) FROM adepts_teacherTopicClusterStatus WHERE ttattemptID IN($ttattemptID) AND result='SUCCESS' ";          	    
    	$rs=mysql_query($query);
    	if($rw=mysql_fetch_row($rs))
			return $rw[0];
		else
			return 0;
    }     
	
?>

<?php include("header.php"); ?>

	<title>Topic Page</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="stylesheet" href="css/commonMidClass.css" />
	<link rel="stylesheet" href="css/topicPage/midClass.css?ver=5" />
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/colorbox.css">
	<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>	
	<script type="text/javascript" src="/mindspark/userInterface/libs/combined.js?ver=1"></script>
	<script type="text/javascript" src="/mindspark/userInterface/libs/dist/Chart.min.js"></script>
	<script src="libs/topicPage.js?ver=2" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			drawDoughNutChart(<?php echo $progressDetails['overallProgress'];?>);
			$.post("commonAjax.php","mode=checkPendingTimedTest",function(data) {
				pendingTimedTestOnTT	=	$.parseJSON(data);
			});
		});
		var isAndroid = false;
    	var isIpad = false;
		var pendingTimedTestOnTT	=	new Array();
		var attemptArray = new Array();
		function drawDoughNutChart(topicsLearned)
		{    
		    if(isAndroid)
		    {
		    	if(topicsLearned == 0)	// hack for one color(0 or 100 progress) chart.js not showing doughnut in android
		    		topicsLearned = 0.001;
		    	if(topicsLearned == 100)
		    		topicsLearned = 99.999;
		    }
		    topicsNotLearned = 100 - topicsLearned;
		    var donutChartCanvas = document.getElementById("chartArea").getContext("2d");;
	        var donutChart = new Chart(donutChartCanvas);
	        var donutData = [
	          {
	            value: topicsLearned,
	            color: "#2f99cb",
	            highlight: "#2f99cb",
	          },
	          {
	            value: topicsNotLearned,
	            color: "#dddddd",
	            highlight: "#dddddd",
	          },
	        ];
	        var donutOptions = {
	          //Number - The percentage of the chart that we cut out of the middle
	          percentageInnerCutout: 80, // This is 0 for pie charts
	          responsive: true,
	          // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
	          maintainAspectRatio: true,
	          showTooltips: false,
	        };
	        donutChart.Doughnut(donutData, donutOptions);
		}
		function showQuesTrail(ttCode, ttDesc)
		{		
			setTryingToUnload();
			document.getElementById("ttCode").value = ttCode;
			document.getElementById("topicDesc").value = ttDesc;			
			document.getElementById("frmTeacherTopicSelection").action = "topicWiseQuesTrail.php";
			document.getElementById("frmTeacherTopicSelection").submit();
		}
		function load() 
		{
			 setTimeout("logoff()", 600000);		
		}
		
		function logoff()
		{
			setTryingToUnload();
			window.location="logout.php";
		}
		function getHome()
		{
			setTryingToUnload();
			window.location.href	=	"home.php";
		}
		function getDashboard()
		{
			<?php if($isHomeUsage) { ?>
				var homeUse = 1;
			<?php } else { ?>
				var homeUse = 0;
			<?php } ?>
			setTryingToUnload();
			window.location = "studentDashboard.php?homeUse="+homeUse;
		}
		function showActivity(id)
		{
			document.getElementById('gameID').value = id;
			setTryingToUnload();
			document.getElementById('frmActivitySelection').submit();
		}
		<?php foreach ($topicAttemptDetails as $ttCode=>$noOfAttempts) {
		?>
		attemptArray["<?=$ttCode?>"] = <?=$noOfAttempts?>;
		<?php } ?>

		function checkForPractiseAction(practiseModuleId)
		{			
			jQuery.ajax({
		        type: "POST",
		        url: 'commonAjax.php',
		        data: {mode: 'checkForPractiseTopicPage', practiseModuleId: practiseModuleId}, 
		         success:function(data) {
			         obj = JSON.parse(data);
			         startPractise(obj.pmArray); 		                
		         }
    		});			
		}
		function startExam(mode,ttCode, attemptNo, category, subcategory)
		{			
				if(pendingTimedTestOnTT[ttCode])
				{
					alert(i18n.t("homePage.timedTestText"));
					mode	=	"nextAction";
					$("#pendingTopicTimedTest").val("yes");
					$("#ttCode").val(ttCode);
					$("#timedTestCode").val(pendingTimedTestOnTT[ttCode]);
				}
				else if(attemptNo>=20 && category.toLowerCase()=="student" && subcategory.toLowerCase()=="school")
				{
					var flag = 0;
					var len = attemptArray.length;
					for(ttCode1 in attemptArray)
					{
						if(attemptArray[ttCode1]<1)  //implies some topic pending for completion
						{
							flag =1;
							break;
						}
					}
					if(flag)
					{						
						alert(i18n.t("dashboardPage.alertMaxTopic"));
						return false;
					}
				}
				if ($('#frmTeacherTopicSelection #mode').length==0) $('<input type="hidden" name="mode" id="mode">').appendTo('#frmTeacherTopicSelection');
				document.getElementById('mode').value=mode;
				if ($('#frmTeacherTopicSelection #ttCode').length==0) $('<input type="hidden" name="ttCode" id="ttCode">').appendTo('#frmTeacherTopicSelection');
				document.getElementById('ttCode').value=ttCode;
				document.getElementById('frmTeacherTopicSelection').action='controller.php';
				setTryingToUnload();
				document.getElementById('frmTeacherTopicSelection').submit();
			
		}		
		
		function startPractise(html){
		  	if (html=='') return;
			obj = JSON.parse(html);			
			
			document.getElementById("qnoFotDailyDrill").value=0;			
			document.getElementById("timeTakenForDd").value=0;
			document.getElementById("isInternelRequest").value=obj.isInternalRequest;
		    document.getElementById("practiseModuleTestStatusId").value=obj.practiseModuleTestStatusId;
		    document.getElementById("practiseModuleId").value=obj.practiseModuleId;
			document.getElementById("timeTakenForDd").value=obj.remainingTime;
			document.getElementById("scoreForDd").value=obj.currentScore;
			document.getElementById("attemptNo").value=obj.attemptNo;
			
			setTryingToUnload();
			document.getElementById('frmDailyDrill').submit();
			
		}
		
		function startRevision(ttCode)
		{			
				setTryingToUnload();
				messageCheck=1;
				document.getElementById("mode").value="topicRevision";
				document.getElementById("ttCode").value=ttCode;
				document.getElementById("frmTeacherTopicSelection").action="controller.php";
				document.getElementById("frmTeacherTopicSelection").submit();			
		}
		function activeAtHome(deactivateFlag)
		{	
			if(deactivateFlag == 2)	
				alert("You will be able to attempt the topic after 4 p.m.");
			else
				alert("This topic has been de-activated.");
		}
	</script>
	
</head>

<body class="translation" onLoad="load()" onResize="load()">
	<div style="display: none">
		<form method="POST" id="frmDailyDrill" action="question.php" name="frmDailyDrill">
			<input type="hidden" value="firstQuestion" name="mode"> 
			<input type="hidden" value="" name="practiseModuleTestStatusId" id="practiseModuleTestStatusId"> 
			<input type="hidden" value="practiseModule" name="quesCategory"> 
			<input type="hidden" value="" name="practiseModuleId" id="practiseModuleId"> 
			<input type="hidden" value="0" name="qnoFotDailyDrill" id="qnoFotDailyDrill"> 
			<input type="hidden" value="" name="timeTakenForDd" id="timeTakenForDd"> 
			<input type="hidden" value="" name="scoreForDd" id="scoreForDd"> 
			<input type="hidden" value="0" name="fromPractisePage" />
			<input type="hidden" value="" name="attemptNo" id="attemptNo" />
			<input type="hidden" value="" name="isInternelRequest" id="isInternelRequest" />
			<input type="hidden" value="topicPage" name="pageName" id="pageName" />	
		</form>
	</div>
	<form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection" method="POST">
		<div id="top_bar">
			<div class="logo">
			</div>  
	        <div id="logout" onClick="logoff();" class="hidden">
	        	<div class="logout"></div>
	        	<div class="logoutText" data-i18n="common.logout"></div>
	        </div>
			<div class="class hidden">
				<strong><span data-i18n="common.class">Class</span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name hidden">
				<strong><?=$Name?></strong>
			</div>
            <div class="clear"></div>      
	    </div>
		
		<div id="container">
			<div id='leftPart'>
				<div id="info_bar">
                    <div id="homeText" class="hidden">
                    	<span id="home" onClick="getHome()"></span> 
                    	<span id='homeTextInner'>
	                		> 
	                		<span class="textUppercase pointerClass" data-i18n="dashboardPage.dashboard" onClick="getDashboard()"></span>
	                    	<font>
	                    		> 
	                    		<span class="textUppercase" data-i18n="dashboardPage.topicPage"></span>
	                    	</font>
                    	</span>
                    </div>
				</div>
				<div id='topicName'><?php echo $progressDetails['ttDescription']; ?></div>
				<div id='doughNutDiv'>
					<div id='topicProgressText'>Topic Progress</div>
					<div id='doughNutChart'>
						<div id="canvasHolder" class="canvasHolder">
						 	<div id="doughNutInner" class="donut-inner">
							    <div  id="doughNutAccuPer"><?php echo $progressDetails['overallProgress']; ?>%</div>
							    <div class="doughNutAccuText">Learning <br>progress</div>
							</div>
					        <canvas id="chartArea" height="125"/>					       
					    </div>
					    <div id='progressData'>
							<div id='conceptLearned'> <div class='tpNum'><?php if($progressDetails['totalConcepts'] !=0 ) { echo $progressDetails['conceptsLearned'].'/' ; } ?><?php echo $progressDetails['totalConcepts']; ?> </div><div class='tpTxt'>concepts learned </div></div>
							<?php if($progressDetails['totalDailyPractise'] != 0) { ?>
							<div id='conceptPracticed'><div class='tpNum'>
							<?php echo $progressDetails['completedDailyPractise']."/".$progressDetails['totalDailyPractise']; ?></div><div class='tpTxt'> concepts practiced</div></div>
							<?php } ?>
							<div id='conceptNotLearned'><div class='tpNum'> <?php echo $progressDetails['conceptsNotLearned']; ?></div><div class='tpTxt'> concepts not learned</div></div>
							<div id="topicAccuracy">							
							<div class='tpNum'><?php echo $progressDetails['accuracy']."%"; ?></div><div class='tpTxt'>topic accuracy</div>
							</div>
						</div>
					</div>
				</div>
				<div id='topicInfo'>
					<div id='topicInfoTitle'>
						Topic Information
					</div>
					<div id='topicInfoText'><?php echo $progressDetails['ttInfo'];?>
						
					</div>
				</div>
				<div id='questionTrail'>
					<div id='trailTitle'>Question Trail</div>
					<div id='trailLink'><a href="javascript:void();" onClick="showQuesTrail('<?=$teacherTopicCode?>','<?=$progressDetails["ttDescription"]?>')">Click here to see the question trail for the topic</a></div>
				</div>
			</div>
			<div id='rightPart'>
				<?php 
				$practiceContent = $learningContent = '';				 			
				$countClusters = 1;
				$noOfClusters = 0;
				$practiseModuleCount = 0;
				if($enableActionButton == 1)
				$learnAction = "startExam('ttSelection','".$teacherTopicCode."','".$topicAttemptDetails[$teacherTopicCode]."','".$category."','".$subcategory."')" ;
				else
					$learnAction = "activeAtHome($enableActionButton)";

				if($enableActionButton != 1)
					$disableClass = 'disabled';
				else
					$disableClass = '';

				$learningContent .= "<div>
					<div class='partHeader'>
						<div class='partHeaderIcon upperPartHeaderIcon'></div>
						<div class='partHeaderText'>Learn concepts in ".$progressDetails['ttDescription']."</div>
					</div>
					<div class='partContent'>";
						$noOfClusters = count($clusterDetails);																	
						foreach ($clusterDetails as $key => $clusterValue)
						{

							$lineFirstClass = ($countClusters == 1) ? 'lineFirstCluster' : '';													
							$lastClusterClass = ($countClusters == $progressDetails['totalConcepts']) ? 'lastCluster' : '';
							if($clusterValue['clusterStatus'] == 1)
							{
								$textClass = 'blueText';
								$textStatus = "Completed with ". $clusterValue['accuracy']."% accuracy";
								if($clusterValue['accuracy'] >= 60 )
								{
									$circleClass = 'greenCircle';
									$lineClass = 'greenLine';
								}
								else
								{
									$circleClass = 'redCircle';
									$lineClass = 'redLine';
								}
								
							}
							else if($clusterValue['clusterStatus'] == 0)
							{
																
								$textClass = 'blueText';
								$textStatus = 'Yet to complete';
								$circleClass = 'yellowCircle';
								$lineClass = 'yellowLine';
							}
							else
							{
								$textClass = 'greyText';
								$textStatus = 'Yet to start';
								$circleClass = 'greyCircle';
								$lineClass = 'greyLine';
							}							
							$clusterContent .=  "<div class='topicCluster ".$lastClusterClass."'>";
							$clusterContent .= "<div class='progressIndicatorDiv'>
										<div class='circle ".$circleClass."'></div>";
							if($noOfClusters > 1)
							{
								if($countClusters < $noOfClusters)
									$clusterContent .= "<div class='line ".$lineClass." ".$lineFirstClass."'></div>";
								else
									$clusterContent .= "<div class='line ".$lineClass." lineLastCluster'></div>";
								
							}

							$clusterContent .= "</div>
									<div class='clusterDetailDiv'>
										<div class='clusterName'>".$clusterValue['clusterDesc']."</div>
										<div class='sdlDiv'>
											<div class='clusterSDLs'>";
											if($clusterValue['clusterStatus'] == 2)
											{
												for($i=0; $i<$clusterValue['numberOfSdls'] ; $i++)
												{
													$clusterContent .="<div class='sdl sdlNotAttempted'></div>";
												}
											}
											else
											{
												foreach ($clusterValue['sdls'] as $key => $sdls) {
													if(trim($sdls['sdlAccuracy']) == '')
													{
														$clusterContent .="<div class='sdl sdlNotAttempted'></div>";
													}
													elseif($sdls['sdlAccuracy'] == 0)
													{
														$clusterContent .="<div class='sdl sdlFailed'></div>";
													}
													else
													{
														$clusterContent .="<div class='sdl sdlPassed'></div>";
													}
												}
											}
											
							$clusterContent .= "</div>";
								
								$clusterContent .= "<div class='clusterStatus ".$textClass."'>".$textStatus."</div>
										</div>
									</div>
								</div>";
							$countClusters++;
						}
						$learningContent .= $clusterContent;
												
					$learningContent .= "</div></div>";					
					
				$practiceContent .= "<div>
					<div class='partHeader'>
						<div class='partHeaderIcon lowerPartHeaderIcon'></div>
						<div class='partHeaderText'>Practice concepts in ".$progressDetails['ttDescription']."</div>
					</div>
					<div class='partContent'>";					
						
						foreach ($clusterDetails as $key => $clusterValue)
						{
							$allPractiseData = $allActivityData = '';				
							if(!empty($clusterValue['dailyPractise']))
							{

								foreach($clusterValue['dailyPractise'] as $key=>$dailyPracitseValue)
								{																	
									$practiceData = '';
									$textColor = 'greyText';
									$buttonText = 'Practice';
									if($clusterValue['clusterStatusOverall'] == 0)
									{	
										$practiseText2 = 'Yet to start';
										$practiseText1 = "Unlock by learning '".$clusterValue['clusterDesc']."'";
										$circleClass = 'greyCircle';
										$lockedClass = 'locked';
										$buttonText = $practiceAction = '';										
									}
									else if($clusterValue['clusterStatusOverall'] == 1 && $clusterValue['clusterPassedDate'] != '' &&  strtotime($clusterValue['clusterPassedDate']) == strtotime($today))
									{
										$practiseText2 = 'Yet to start';
										$practiseText1 = "Available for practice from tomorrow onwards";
										$circleClass = 'greyCircle';
										$lockedClass = 'locked';
										$buttonText = $practiceAction = '';
									}
									else
									{
										$practiceAction = "checkForPractiseAction('".$key."')";
										$lockedClass = $practiseText1 = '';
										if($dailyPracitseValue['status'] == 'completed')
										{
											if($dailyPracitseValue['accuracy'] >= 60)
												$circleClass = 'greenCircle';
											else
												$circleClass = 'redCircle';

											$practiseText2 = 'Completed with '.$dailyPracitseValue['accuracy'].'% accuracy';
											$textColor = 'blueText';
											
										}
										elseif($dailyPracitseValue['status'] == 'incomplete')
										{
											$circleClass = 'yellowCircle';
											$practiseText2 = 'Yet to complete';																								
										}
										else
										{
											$circleClass = 'greyCircle';
											$practiseText2 = 'Yet to start';											
										}
									}
									

										$practiceData .= "<div class='topicCluster'>
										<div class='progressIndicatorDiv'>
											<div class='circle ".$circleClass."'></div>
										</div>
										<div class='practiceClusterDetailDiv'>
											<div class='practiceClusterName'> Practice - ".$dailyPracitseValue['desc']."</div>
											<div class='practiceClusterInstr ".$textColor."'>".$practiseText1."</div>
											<div class='practiceClusterStatus ".$textColor."'>".$practiseText2."</div>
										</div>";
									
										$practiceData .="<div class='practiceActionBtnDiv'>
												<div class='practiceActionBtn ".$lockedClass."' onClick=".$practiceAction." >".$buttonText."</div>
											</div>";
									
									$practiceData .= "</div>";
									if($notAttempteDailyPractice != $progressDetails['totalDailyPractise'])
									{																
										$practiceContent .= $practiceData;
																		
									}

									$allPractiseData .= $practiceData;
								}
								
							}

							if(!empty($clusterValue['activity']))
							{
																	
								foreach($clusterValue['activity'] as $key=>$activityValue)
								{
									$activeData = '';
									if($activityValue['locked'] == 2)
									{
										$activityCircleClass = 'greenCircle';
										$activityText1 = "Unlocked by learning '".$clusterValue['clusterDesc']."'";
										$activityText2 = 'Completed';
										$activityTextColor1 = 'greyText';
										$activityTextColor2 = 'blueText';
										$lockedActivityClass='';
										$playText = 'Play';
										$activityAction = ' onClick = "showActivity('.$key.')"';
										
									}
									elseif($activityValue['locked'] == 0)
									{
										$activityCircleClass = 'greyCircle';
										$activityText1 = "Unlocked by learning '".$clusterValue['clusterDesc']."'";
										$activityText2 = 'Yet to complete';
										$activityTextColor1 = 'greyText';
										$activityTextColor2 = 'greyText';
										$lockedActivityClass='';
										$playText = 'Play';
										$activityAction = ' onClick = "showActivity('.$key.')"';
									}
									else
									{
										$activityCircleClass = 'greyCircle';
										$activityText1 = "Unlock by learning '".$clusterValue['clusterDesc']."'";
										$activityText2 = 'Yet to start';
										$activityTextColor1 = 'greyText';
										$activityTextColor2 = 'greyText';
										$lockedActivityClass = 'locked';
										$playText = $activityAction= '';
									}
									$activeData .="<div class='topicCluster'>
											<div class='progressIndicatorDiv'>
												<div class='circle ".$activityCircleClass."'></div>
											</div>
											<div class='practiceClusterDetailDiv'>
												<div class='practiceClusterName'> Activity - ".$activityValue['desc']."</div>
												<div class='practiceClusterInstr ".$activityTextColor1."'>".$activityText1."</div>
												<div class='practiceClusterStatus ".$activityTextColor2."'>".$activityText2."</div>
											</div>";

									$activeData .= "<div class='practiceActionBtnDiv'>
													<div class='practiceActionBtn ".$lockedActivityClass."' $activityAction>".$playText."</div>
												</div>";
									$activeData .= "</div>";
									if($notAttempteDailyPractice != $progressDetails['totalDailyPractise'])
									{																	
										$practiceContent .= $activeData;
																		
									}	
									$allActivityData .= $activeData;		
								}
																
							}	
							if($notAttempteDailyPractice == $progressDetails['totalDailyPractise'])
							{							
								$practiceContent .=$allPractiseData;
								$practiceContent .=$allActivityData;							
							}
						}
						if(!empty($ttActivities))
						{				
							$ttActiveData = '';			
							foreach($ttActivities as $key=>$activityValue)
								{									
									if($activityValue['locked'] == 2)
									{
										$activityCircleClass = 'greenCircle';
										$activityText1 = "";
										$activityText2 = 'Completed';
										$activityTextColor1 = 'greyText';
										$activityTextColor2 = 'blueText';
										$lockedActivityClass='';
										$playText = 'Play';
										$activityAction = ' onClick = "showActivity('.$key.')"';
										
									}
									elseif($activityValue['locked'] == 0)
									{
										$activityCircleClass = 'greyCircle';						
										$activityText1 = "";				
										$activityText2 = 'Yet to complete';
										$activityTextColor1 = 'greyText';
										$activityTextColor2 = 'greyText';
										$lockedActivityClass='';
										$playText = 'Play';
										$activityAction = ' onClick = "showActivity('.$key.')"';
									}
									else
									{
										$activityCircleClass = 'greyCircle';						
										$activityText1 = "Unlock on ".$activityValue['topicCompletion']."% topic progress.";			
										$activityText2 = 'Yet to start';
										$activityTextColor1 = 'greyText';
										$activityTextColor2 = 'greyText';
										$lockedActivityClass = 'locked';
										$playText = $activityAction= '';
									}
									$ttActiveData .="<div class='topicCluster'>
											<div class='progressIndicatorDiv'>
												<div class='circle ".$activityCircleClass."'></div>
											</div>
											<div class='practiceClusterDetailDiv'>
												<div class='practiceClusterName'> Activity - ".$activityValue['desc']."</div>
												<div class='practiceClusterInstr ".$activityTextColor1."'>".$activityText1."</div>
												<div class='practiceClusterStatus ".$activityTextColor2."'>".$activityText2."</div>
											</div>";

									$ttActiveData .= "<div class='practiceActionBtnDiv'>
													<div class='practiceActionBtn ".$lockedActivityClass."' $activityAction>".$playText."</div>
												</div>";
									$ttActiveData .= "</div>";			
								}
						}
						
						if($progressDetails['overallProgress'] == 100)
						{
							$revsionLockedClass = '';
							$revisionButtonText = 'Revise';
							$revisionAction = "startRevision('".$teacherTopicCode."')";							
							if(empty($revision['revisionAccuracy']))
							{
								$revisionCircleClass = 'greyCircle';
								$revisionText1 = 'Yet to start';
								$revisionTextColor = 'greyText';
								$revisionText2 = '';
							}
							else
							{
								$revisionCircleClass = 'greenCircle';
								$revisionText1 = "Attempted with ".$revision['revisionAccuracy']."% accuracy";
								$revisionTextColor = 'blueText';
								$revisionText2= '';
							}
							
						} 
						else
						{
							$revisionText2 = 'Unlock on 100% topic progress';
							$revsionLockedClass = 'locked';
							$revisionButtonText = $revisionText1 = $revisionAction ='';
							$revisionCircleClass = 'greyCircle';
							$revisionTextColor = 'greyText';
						}
						
						$revisionContent .= "<div class='topicCluster lastCluster'>
							<div class='progressIndicatorDiv'>
								<div class='circle ".$revisionCircleClass."'></div>
							</div>
							<div class='practiceClusterDetailDiv'>
								<div class='practiceClusterName'>Topic Revision - ".$progressDetails['ttDescription']."</div>
								<div class='practiceClusterInstr ".$revisionTextColor."'>".$revisionText2."</div>
								<div class='practiceClusterStatus ".$revisionTextColor."'>".$revisionText1."</div>
							</div>
							<div class='practiceActionBtnDiv'>
								<div class='practiceActionBtn ".$revsionLockedClass."' onClick=".$revisionAction.">".$revisionButtonText."</div>
							</div>
						</div>";									
						
														
					$practiceContent .= $ttActiveData;	
					$practiceContent .= $revisionContent;					 
					$practiceContent .= "</div></div>";
					$upperPart = ($progressDetails['overallProgress'] == 100 && $higherLevel!=1)?$practiceContent:$learningContent;
					$lowerPart = ($progressDetails['overallProgress'] == 100 && $higherLevel!=1)?$learningContent:$practiceContent;					
					
					?>
					<div id='upperPart' ><div class='partActionBtn <?=$disableClass?>' onclick="<?=$learnAction?>">Learn</div><?= $upperPart ; ?></div>
					<div  id='lowerPart' ><?= $lowerPart ; ?></div>
			</div>
		</div>
		 <input type="hidden" name='mode' id="mode">
	    <input type="hidden" name='ttCode' id="ttCode">
	    <input type="hidden" name='topicDesc' id="topicDesc">
	    <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
		<input type="hidden" name="timedTestCode" id="timedTestCode">
		<input type="hidden" name="pendingTopicTimedTest" id="pendingTopicTimedTest">
		<input type="hidden" value="topicPage" name="pageName" id="pageName" />	
		<input type="hidden" name='isDeactive' id="isDeactive" value="<?=$isDeactive;?>" >
	    <input type="hidden" name='higherLevel' id="higherLevel" value="<?=$higherLevel; ?>">
	    <input type="hidden" name="cls" id="cls" value="<?=$objUser->childClass?>">
	</form>    
	<form name="frmActivitySelection" id="frmActivitySelection" method="POST" action="enrichmentModule.php">
		 <input type="hidden" name='gameID' id="gameID">
	</form>	
<?php include("footer.php"); mysql_close(); ?>

