<?php
error_reporting(E_ERROR);
// set_time_limit(0);
@include("check1.php");
include_once("constants.php");
include("functions/functions.php");
include("classes/clsUser.php");
include_once("classes/clsTopicProgress.php");
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
$packageType   = $objUser->packageType;
$isHomeUsage = 0;
if(!in_array($childClass,array(4,5,6,7)))
{
	header("Location: dashboard.php?homeUse=$isHomeUsage");	
}
if(strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"School")==0 || strcasecmp($subcategory,"Home Center")==0))
{
    $isAllowedDeactivatedTopicsForHomeUse = isAllowedDeactivatedTopicsForHome($schoolCode, $childClass, $childSection);
    if($isAllowedDeactivatedTopicsForHomeUse)
		$isHomeUsage = isHomeUsage($schoolCode, $childClass);
}


$programMode = "";
$dailyDrillAvailableToday=0;
$broadTopics = array();
$teacherTopics = array();
$tmpTopicArray = array();
$showAllTopics=0;
$baseurl = IMAGES_FOLDER."/newUserInterface/";

$freeTrilaTopicArray = array();

if($_SESSION['freeTrialTopics']==1)
{
	$freeTrilaTopicArray = getTopicsForFreeTrial($childClass);
	$programMode = "freeTrial";
}

$arrTopicsActivated = getTopicActivatedTillDate($userID,$childClass,$childSection, $objUser->category,$objUser->subcategory,$objUser->schoolCode, SUBJECTNO, $objUser->packageType,$showAllTopics,$programMode);
$topicsAttempted = getTopicsAttempted($userID, SUBJECTNO);
if(!count($topicsAttempted)==0)
{
	$topicWiseDetails = getTopicWiseDetailsNew($topicsAttempted, $userID, $childClass);		
	/*  0 = topicname
		1 = progress
		2 = higherlevel
		3 = last attemptedon
		4 = no of attempts on this topic
		5 = result of first attempt
	*/	
}
$sparkieImage = $_SESSION['sparkieImage'];
$topicAttemptDetails = getAttemptDetailsOnTopic($userID,array_keys($topicsAttempted));
if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0 && $programMode!="freeTrial") {
$topicsForOtherGrades = getTopicsForOtherGrades($childClass,$topicWiseDetails,$packageType);
}
if($programMode=="freeTrial")
	$arrTopicsActivated = sortArrayByArray($arrTopicsActivated, $freeTrilaTopicArray);
?>

<?php include("header.php"); ?>

	<title>Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="stylesheet" href="css/studentDashboard/midClass.css?ver=7" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/colorbox.css">
	<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>	
	<script type="text/javascript" src="/mindspark/userInterface/libs/combined.js?ver=1"></script>
	
	<script>
		var langType = '<?=$language;?>';		
		var messageCheck=0;
		var pendingTimedTestOnTT	=	new Array();
		var counter=0;				
		var checkedForOtherGrades=false;
		var attemptArray = new Array();	
		var isAndroid = false;
	    var isIpad = false;

	    if(navigator.userAgent.indexOf("Android") != -1)
	    {
	        isAndroid = true;
	    }
	    else if(window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
	    {
	        isIpad = true;
	    }				
		$(document).ready(function(e) {
			$.post("commonAjax.php","mode=checkPendingTimedTest",function(data) {
				pendingTimedTestOnTT	=	$.parseJSON(data);
			});

			if(isAndroid)
		    {
		        $("#sessionWiseReport").css("line-height","1.5");
		        $(".actionBtn").css("line-height","1.5");
		        $(".topicNameInnerP").css("margin-top","10px");
		        $(".twoLines").css({"height":"35px"});
		    }
		    if(isIpad)
		    {
		    	$("#sessionWiseReport").css({"line-height":"1.5","margin-right":"260px"});
		    }
		});
			
		<?php
		foreach ($topicAttemptDetails as $ttCode=>$noOfAttempts) {
		?>
		attemptArray["<?=$ttCode?>"] = <?=$noOfAttempts?>;
		<?php } ?>				
		function load() 
		{
			init();
			var a= window.innerHeight - (80 + 25 + 55);
			// $('#topicInfoContainer').css("height",a+"px");
			$(".forLowerOnly").remove();
			if(androidVersionCheck==1)
			{
				$('#topicInfoContainer').css("height","auto");
				$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
				$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
				$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
			}
		}

		
		function init()
		{
			setTimeout("logoff()", 600000);	//log off if idle for 10 mins
			if($("#viewAllTopics").css("display")=="none"){
				$("#sessionWiseReport").css("margin-top","-50px");
			}
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
		function showTopicsFromOtherGrades()
		{
			if($("#squaredOne").is(":checked"))
			{
				$(".chkBox").removeClass("checked");
				$("#tableContainerOther").hide();
				$('#tableContainer').animate({
			        scrollTop: 0},
			        'slow');				
			}
			else
			{
				$(".chkBox").addClass("checked");
				$("#tableContainerOther").show();
				var scrollHeight = $("#tableContainerMain").height();
				$('#tableContainer').animate({
			        scrollTop: scrollHeight},
			        'slow');

				
			}
		}
	</script>

	<script>
		
		
	</script>

	<!-- Code for Daily Drill starts here.-->
	<script>
		var DailyDrillTimer=0;
		function startDdTimer(duration, display) {
			var timer = duration, minutes, seconds;
			DailyDrillTimer = setInterval(function () {
				minutes = parseInt(timer / 60, 10)
				seconds = parseInt(timer % 60, 10);

				seconds = seconds < 10 ?  seconds : seconds;

				display.text( seconds);

				//update table every 10 secounds.
				
				if (--timer < 0) {
					clearInterval(DailyDrillTimer);
					$("#prmptContainer_ttprompt").remove();
					setTryingToUnload();
					document.getElementById('frmDailyDrill').submit();
				}
			}, 1000);
		}
		var checkPromptOpen=0;
		var dd_description="",dd_CanSparkies=0;dd_btnTxt='';
		function startPractise(html,buttonText){
		  	if (html=='') return;
			obj = JSON.parse(html);
			if (typeof obj.dailyDrillForDayCompleted!='undefined') return;
			
			document.getElementById("qnoFotDailyDrill").value=0;
			
			document.getElementById("timeTakenForDd").value=0;
			document.getElementById("isInternelRequest").value=obj.isInternalRequest;

		    document.getElementById("practiseModuleTestStatusId").value=obj.practiseModuleTestStatusId;
		    document.getElementById("practiseModuleId").value=obj.practiseModuleId;
			document.getElementById("timeTakenForDd").value=obj.remainingTime;
			document.getElementById("scoreForDd").value=obj.currentScore;
			document.getElementById("attemptNo").value=obj.attemptNo;
			dd_description=obj.description;
			dd_CanSparkies=obj.canSparkies;
			dd_btnTxt=buttonText;

			if (!$("#prmptContainer_dailyPracticePrompt").is(":visible")) {
				new Prompt({
					text: "<label>Let us first quickly revise with "+(document.getElementById("timeTakenForDd").value==300?"5 minutes of ":"")+"<b>Daily Practice</b>!</label><br>"+(document.getElementById("timeTakenForDd").value==300?"":"<div style='font-size:0.7em;'>Practice Time left for today: "+toDisplayTime(document.getElementById("timeTakenForDd").value)+"</div>")+"<img src='assets/practiseModule.png'><br><div style='font-size:0.8em;'>Topic: "+dd_description+"</div><br><div style='font-size:0.7em;'>Aim for higher accuracy.<br>Solve questions as fast as you can.</div> <br><br>You have "+dd_CanSparkies+" sparkies for grabs.<label> Practice starts in <b id='DdCountDown'></b> s.</label>",
					type: "alert",
					label1 : dd_btnTxt,
					func1: function () {
							setTryingToUnload();clearInterval(DailyDrillTimer);//return;
						  	$("#prmptContainer_dailyPracticePrompt").remove();
							document.getElementById('frmDailyDrill').submit();
						},
					promptId: "dailyPracticePrompt",
					
				});
				var display = $('#DdCountDown');
				startDdTimer(15, display);
			}
		}
		function toDisplayTime(s){
			var m=Math.floor(s/60);s=s%60;
			return "0"+m+':'+(s<10?"0"+s:s);
		}
	</script>
	<?php
		function isPractiseTime($pmID,$pmMsg){
			$userID = $_SESSION['userID'];
			global $dailyDrillAvailableToday;
			$practiseModuleId = $pmID;
			$status = "";
			$testId = "";
			$firstLoginToday=isUserFirstTimeLoggedInToday($userID);
			$q="SELECT * FROM practiseModulesTestStatus WHERE lastModified>=CURDATE() AND userID=$userID";
			$r=mysql_query($q) or die(mysql_error().$q);
			if ($firstLoginToday && mysql_num_rows($r)>0) 
				$firstLoginToday=0;
			if (mysql_num_rows($r)==0)
				$firstLoginToday=1;

			unset($_SESSION['dailyDrillArray']);
			$dailyDrillForDayCompleted=array();
			$insertRecord = 0;
			
				$selectRecordSQL = "SELECT id, status FROM practiseModulesTestStatus where lastModified>CURDATE() AND status='completed' AND userID=".$_SESSION['userID']." ORDER BY id DESC";
				$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
				if (mysql_num_rows($selectRecordQuery)>0){
					$dailyDrillForDayCompleted['dailyDrillForDayCompleted']=1;					
					return;
				}
				$selectRecordSQL = "SELECT id, status FROM practiseModulesTestStatus where practiseModuleId='$practiseModuleId' AND userID=".$_SESSION['userID']." ORDER BY id DESC";
				$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
				$status="";
				if (mysql_num_rows($selectRecordQuery)>0){
					$selectRecordResult = mysql_fetch_row($selectRecordQuery);
					$status=$selectRecordResult[1];
					$testId=$selectRecordResult[0];
				}
			
			if($status == ""){
				$insertRecord = 1;
				$attemptNo = 1;
			}else if($status == "in-progress"){
				$attemptNo = 1;
				$practiseModuleTestStatusId = $testId;
				$selectRecordSQL = "SELECT remainingTime,score,attemptNo,currentLevel FROM practiseModulesTestStatus where id = $testId AND practiseModuleId='$practiseModuleId'";
				$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
				$selectRecordResult = mysql_fetch_row($selectRecordQuery);
				$remainingTime = $selectRecordResult[0];
				$score = $selectRecordResult[1];
				$attemptNo = $selectRecordResult[2];
				$currentLevel = $selectRecordResult[3];
				$currentScore = $score;
				
			}else if($status == "completed"){
				// get last attempt No.
				$attemptNoSQL = "SELECT MAX(f.attemptNo) FROM practiseModulesTestStatus f where f.practiseModuleId = '$practiseModuleId' AND userID = $userID";
				$attemptNoQuery = mysql_query($attemptNoSQL) or die(mysql_error().$attemptNoSQL);
				$attemptNoResult = mysql_fetch_row($attemptNoQuery);
				$attemptNo = $attemptNoResult[0] + 1;
				$insertRecord = 1;
			}
			
			if($insertRecord == 1){
				// Insert New Record
				$insertSQL = "INSERT INTO practiseModulesTestStatus (userID,status,remainingTime,lastAttemptQue,practiseModuleId,score,attemptNo, currentLevel) VALUES ($userID, 'in-progress',300,0,'$practiseModuleId',0,$attemptNo,1)";
				mysql_query($insertSQL) or die(mysql_error().$insertSQL);
				$practiseModuleTestStatusId = mysql_insert_id();
				$currentLevel = 1;
				$currentScore = 0;
				// remainingTime not in use for now until we implement Daily Drill.
				$remainingTime = 300;
			}			
				if($firstLoginToday){
					$remainingTime=300;
					$query="UPDATE practiseModulesTestStatus SET remainingTime=300 WHERE id=$practiseModuleTestStatusId";
					mysql_query($query) or die(mysql_error().$query);
				}
				else if ($remainingTime<30) {
					$dailyDrillForDayCompleted['dailyDrillForDayCompleted']=1;
					return;
				}
			
			$_SESSION['dailyDrillArray']['remainingTime'] = $remainingTime;
			$_SESSION['dailyDrillArray']['isInternalRequest'] = isset($_SESSION['msAsStudent'])?$_SESSION['msAsStudent']:0;
			$_SESSION['dailyDrillArray']['practiseModuleTestStatusId'] = $practiseModuleTestStatusId;
			// add all values to session.
			
			$DDsql = "SELECT description, linkedToCluster, numberOfLevels, dailyDrill FROM practiseModuleDetails WHERE practiseModuleId = '$practiseModuleId'";
			$resultSet = mysql_query($DDsql) or die(mysql_error().$DDsql);
			while($rw = mysql_fetch_array($resultSet,MYSQL_ASSOC)){
				$_SESSION['dailyDrillArray']['isDailyDrill']=$rw['dailyDrill'];
				$_SESSION['dailyDrillArray']['clusterCode'] = $rw['linkedToCluster'];
				$_SESSION['dailyDrillArray']['practiseModuleId'] = $practiseModuleId;
				$_SESSION['dailyDrillArray']['description'] = $rw['description'];
				$_SESSION['dailyDrillArray']['numberOfLevels'] = $rw['numberOfLevels'];
			}
			$DDsql = "SELECT SUM(IF(qCodes!='',1,0)) qLevels, SUM(IF(timedTest!='',1,0)) tLevels FROM practiseModuleLevels WHERE practiseModuleId = '$practiseModuleId'";
			$resultSet = mysql_query($DDsql) or die(mysql_error().$DDsql);
			while($rw = mysql_fetch_array($resultSet,MYSQL_ASSOC)){
				$attemptPer=($attemptNo>6)?0:(6-$attemptNo)*2/10;
				$_SESSION['dailyDrillArray']['canSparkies']	= round(($rw['tLevels']*5+5)*$attemptPer);
			}
			$query="UPDATE practiseModulesTestStatus SET lastModified=NOW() WHERE id=$practiseModuleTestStatusId";
			mysql_query($query) or die(mysql_error().$query);
			$_SESSION['dailyDrillArray']['currentLevel'] = $currentLevel;
			$_SESSION['dailyDrillArray']['currentScore'] = $currentScore;			
			$_SESSION['dailyDrillArray']['attemptNo'] = $attemptNo;			
			$dailyDrillAvailableToday=array('pmArray'=>$_SESSION['dailyDrillArray'],'pmMsg'=>$pmMsg);
			
		}
		function checkForPractise(){
			$userID = $_SESSION['userID'];
			$completedPractiseStr = getCompletedDailyPractise($userID);
			$attemptedPracticeSQL="SELECT DISTINCT ad, practiseModuleId, pmAttemptID, status,lastModified FROM(
				SELECT DISTINCT attemptdate ad, q.practiseModuleId, practiseModuleTestStatusId pmAttemptID, a.`status`,a.lastModified 
					FROM practiseModulesQuestionAttemptDetails q, practiseModulesTestStatus a, practiseModuleDetails e  WHERE q.userID=$userID AND a.id=q.practiseModuleTestStatusId AND e.practiseModuleId=q.practiseModuleId AND e.dailyDrill=1 and  e.`status`='Approved'
					UNION	
				SELECT DISTINCT attemptDate ad, q.practiseModuleId, practiseModuleTestStatusId pmAttemptID, a.`status`,a.lastModified
					FROM practiseModulesTimedTestAttempt q, practiseModulesTestStatus a, practiseModuleDetails e  WHERE q.userID=$userID AND a.id=q.practiseModuleTestStatusId AND e.practiseModuleId=q.practiseModuleId AND e.dailyDrill=1 and  e.`status`='Approved'
				) e	WHERE ad<CURDATE() and practiseModuleId NOT IN($completedPractiseStr) ORDER by lastModified DESC";
				
			$attemptedPracticeSQL = mysql_query($attemptedPracticeSQL) or die(mysql_error().$attemptedPracticeSQL);
			//check for PractiseModule content Attempts
			if(mysql_num_rows($attemptedPracticeSQL) > 0){
				$row = mysql_fetch_array($attemptedPracticeSQL);
				$row1 = mysql_num_rows($attemptedPracticeSQL)>1?mysql_fetch_array($attemptedPracticeSQL):array('pmAttemptID'=>0);
				if ($row['status']=="in-progress"){
					if ($row['pmAttemptID']!=$row1['pmAttemptID']){ //only 1 continuous attempt for incomplete PM - give same PM
						isPractiseTime($row[1],"Resume");
						return;
					}
				}
			}

			$checkTodaysPractiseSQL="SELECT a.practiseModuleId, a.status,remainingTime, a.lastModified FROM practiseModulesTestStatus a, practiseModuleDetails e
									WHERE DATE(a.lastModified)=CURDATE() AND a.userID=$userID AND e.practiseModuleId=a.practiseModuleId AND e.dailyDrill=1 and  e.`status`='Approved' and a.practiseModuleId NOT IN($completedPractiseStr)
									ORDER BY a.lastModified ";
			$checkTodaysPractiseSQL = mysql_query($checkTodaysPractiseSQL) or die(mysql_error().$checkTodaysPractiseSQL);
			//check for PractiseModule content Attempts
			if(mysql_num_rows($checkTodaysPractiseSQL) > 0){
				$row = mysql_fetch_array($checkTodaysPractiseSQL);
				if ($row['status']=="in-progress" && $row['remainingTime']>=30){
					isPractiseTime($row[0],"Resume");
					return;
				}
				else {
					return;
				}
			}
			$checkNewPractiseSQL="	SELECT e.practiseModuleId, e.description, e.dailyDrill, c.clusterCode as clusterToUse, SUM(IF(d.`status`='completed',1,0)) c, SUM(IF(d.`status`='in-progress',1,0)) p
									FROM adepts_teacherTopicClusterStatus c JOIN practiseModuleDetails e ON e.linkedToCluster=c.clusterCode	
									LEFT JOIN practiseModulesTestStatus d ON d.practiseModuleId=e.practiseModuleId AND d.userID=$userID 
									WHERE  c.userID = $userID  AND c.result='SUCCESS' AND c.lastModified<CURDATE() AND e.dailyDrill=1 AND e.`status`='Approved' 
									GROUP BY e.practiseModuleId HAVING c=0 AND p=0 ORDER BY c.lastModified DESC, c";
			$checkNewPractiseSQL = mysql_query($checkNewPractiseSQL) or die(mysql_error().$checkNewPractiseSQL);
			if(mysql_num_rows($checkNewPractiseSQL) > 0){
				$row = mysql_fetch_array($checkNewPractiseSQL);
				isPractiseTime($row[0],"Start");
				return;
			}
			$checkOpenPractiseSQL=" SELECT e.practiseModuleId, e.description, e.dailyDrill FROM  practiseModuleDetails e,practiseModulesTestStatus d 
									WHERE d.practiseModuleId=e.practiseModuleId AND d.userID=$userID AND e.dailyDrill=1 AND e.`status`='Approved' AND d.`status`='in-progress' and d.practiseModuleId NOT IN($completedPractiseStr) 
									ORDER BY d.lastModified DESC";
			$checkOpenPractiseSQL = mysql_query($checkOpenPractiseSQL) or die(mysql_error().$checkOpenPractiseSQL);
			if(mysql_num_rows($checkOpenPractiseSQL) > 0){
				$row = mysql_fetch_array($checkOpenPractiseSQL);
				isPractiseTime($row[0],"Resume");
				return;
			}
			$checkOldPractiseSQL= " SELECT * FROM(SELECT e.practiseModuleId, e.description, e.dailyDrill, d.id, Max(d.lastModified) lastModified 
									FROM  practiseModuleDetails e,practiseModulesTestStatus d 
									WHERE d.practiseModuleId=e.practiseModuleId AND d.userID=$userID AND d.`status`='completed' and  e.dailyDrill=1 and  e.`status`='Approved'
									group by practiseModuleId
									ORDER BY d.lastModified DESC) w where w.lastModified<CURDATE() -INTERVAL 3 MONTH ";									
			$checkOldPractiseSQL = mysql_query($checkOldPractiseSQL) or die(mysql_error().$checkOldPractiseSQL);
			if(mysql_num_rows($checkOldPractiseSQL) > 0){
				$row = mysql_fetch_array($checkOldPractiseSQL);
				isPractiseTime($row[0],"Start");
				return;
			}
		}
		
		if (isDailyDrillSchool($childClass)) checkForPractise();
		
	?>
	<script>
	function goToTopicPage(ttCode,isDeactive,higherLevel){
			<?php if($dailyDrillAvailableToday !== 0 && json_encode($dailyDrillAvailableToday['pmArray'])!="") { 
				echo 'startPractise("'.addslashes(json_encode($dailyDrillAvailableToday['pmArray'])).'","'.$dailyDrillAvailableToday['pmMsg'].'");';
			} else { ?>
			setTryingToUnload();			
			document.getElementById('ttCode').value=ttCode;			
			document.getElementById('isDeactive').value=isDeactive;
			document.getElementById('higherLevel').value = higherLevel;
			document.getElementById('frmTeacherTopicSelection').action='topicPage.php';			
			document.getElementById('frmTeacherTopicSelection').submit();
			<?php } ?>			
		}
		function startRevision(ttCode)
		{		
		<?php if($dailyDrillAvailableToday !== 0 && json_encode($dailyDrillAvailableToday['pmArray'])!="") { 
				echo 'startPractise("'.addslashes(json_encode($dailyDrillAvailableToday['pmArray'])).'","'.$dailyDrillAvailableToday['pmMsg'].'");';
			} else { ?>	
				setTryingToUnload();
				messageCheck=1;
				document.getElementById("mode").value="topicRevision";
				document.getElementById("ttCode").value=ttCode;
				document.getElementById("frmTeacherTopicSelection").action="controller.php";
				document.getElementById("frmTeacherTopicSelection").submit();
				<?php } ?>				
		}
	function startExam(mode,ttCode, attemptNo, category, subcategory,topicPage,higherLevel)
	{
		<?php if($dailyDrillAvailableToday !== 0 && json_encode($dailyDrillAvailableToday['pmArray'])!="") { 
				echo 'startPractise("'.addslashes(json_encode($dailyDrillAvailableToday['pmArray'])).'","'.$dailyDrillAvailableToday['pmMsg'].'");';
		} else { ?>
			if(topicPage)
			{
				goToTopicPage(ttCode,0,higherLevel);
				return true;
			}			
			else if(pendingTimedTestOnTT[ttCode])
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
		<?php } 
			
		?>
	}
	
	</script>
	<!--  code for Daily Drill ends here.	-->
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
	    </div>
		
		<div id="container">
			<div id="info_bar" class="forHigherOnly studentDashboardTopicHeaderBg">
				<div id="topic">
	                <div id="home">
	                    <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase pointerClass" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard" ></span></font></div>
	                </div>

	                <div id="activatedAtHome" class="forLowerOnly hidden">
	                	<div id="activatedAtHomeIcon"></div>
	                    <div id="activatedAtHomeText" data-i18n="dashboardPage.msgActiveHome"></div>
	                    <div class="clear"></div>
	                </div>
					<div class="clear"></div>
				</div>
				<div class="class hidden">
					<strong><span data-i18n="common.class">Class</span> </strong> <?=$childClass.$childSection?>
				</div>
				<div class="Name hidden">
					<strong><?=$Name?></strong>
				</div>
	            <div class="clear"></div>

	            <a href="sessionWiseReport.php"  onclick="javascript:setTryingToUnload();" class="removeDecoration forHigherOnly">
	            	<div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div>
	            </a>
			<?php  if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0 && $programMode!="freeTrial") { ?>
	            <div class="squaredOne">
			      <input type="checkbox" value="0" id="squaredOne" name="squaredOne" checked onClick="showTopicsFromOtherGrades();" />
			      <label class='chkLbl' for="squaredOne">
			      	<span class='chkBox'></span>
			      	<span class='chkText'>Show topics from other grades</span>
			      </label>
			    </div>
			    <?php  } ?>
			</div>
			<div id='tableContainer'>
		        <div id="tableContainerMain">
		            <div id="topicInfoContainer">
		            	<div id='topicHeaderText' class='studentDashboardTopicHeaderBg'>
		            		Topics
		            	</div>
		            	<div id='topicHeaderUpperBorder'>
		            	</div>
		            	
		            	<table id='topicTable'>
		            	<?php
		            	foreach($arrTopicsActivated as $topicCode=>$detail) { 
		            		$topicTitle = '';
		            		$progress = 0;
							if(!$topicWiseDetails[$topicCode])
							{
								$topicWiseDetails[$topicCode][0] = $detail['description'];
								$topicWiseDetails[$topicCode][1] = 0;
								$topicWiseDetails[$topicCode][2] = 0;
								$topicWiseDetails[$topicCode][3] = 0;
								$topicWiseDetails[$topicCode][4] = 0;
							}
							$isDeactive = 0;
							if(strcasecmp($subcategory,"School")==0 || strcasecmp($subcategory,"Center")==0) {
								if($isHomeUsage==0)
								{
									if($detail["deactivationDate"]=="0000-00-00")
										$isDeactive	=	0;
									else
										$isDeactive	=	1;
								}
								else
									$isDeactive = 0;
							}
							else
							{
								$isHomeUsage = 1;
								$isDeactive = 0;
								if($programMode=="freeTrial" && !in_array($topicCode,$freeTrilaTopicArray))
								{
									$isDeactive = 1;
									$deactiveTitle = "For attempting other Topics - Purchase Mindspark.";
								}
							}

							$dataSynchronised = true;
							if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==2 || $_SESSION['offlineStatus']==4))
							{
								$dataSynchronised = false;
								$isDeactive = 0;
								$isHomeUsage = 1;
								$deactiveTitle = "Activate at home";
							}
							
							$progress = round($topicWiseDetails[$topicCode][1]);
							$topicTitle = $topicWiseDetails[$topicCode][0];
							if($progress == 0)
							{
								$actionText = 'Start';
								$topicStateIcon = 'learnIcon';
								$topicAction  = "startExam('ttSelection','$topicCode','$topicAttemptDetails[$topicCode]','$category','$subcategory',0,$topicWiseDetails[$topicCode][2])";
							}
							elseif($progress>0 && $progress<100) {
								$actionText = 'Complete it!';
								$topicStateIcon = 'learnIcon';
								$topicAction  = "startExam('ttSelection','$topicCode','$topicAttemptDetails[$topicCode]','$category','$subcategory',0,$topicWiseDetails[$topicCode][2])";
							}
							else
							{
								if($topicAttemptDetails[$topicCode][4]>1 || $topicWiseDetails[$topicCode][5] != '')
								{
									$actionText = 'Revise' ;
									$topicAction = "startRevision('$topicCode')";									
								}
								else
								{
									$actionText = 'Continue';
									if($topicWiseDetails[$topicCode][2] == 1)
										$higherlevel = 0;
									else
										$higherlevel = 1;

									$topicAction  = "startExam('ttSelection','$topicCode','$topicAttemptDetails[$topicCode]','$category','$subcategory',$higherlevel,$topicWiseDetails[$topicCode][2])";
									
								}

								if($topicWiseDetails[$topicCode][2] == 1)
									$topicTitle.=" (higher level)";

								$topicStateIcon = 'trophyIcon';		
							}					
							?>
						<tr>
	            			<td class='firstCol <?php echo ($isDeactive==1)?'disabled': "" ?>' onClick="goToTopicPage('<?php echo $topicCode; ?>',<?php echo $isDeactive; ?>,<?php echo $topicWiseDetails[$topicCode][2] ?>)">
	            				<div class='topicIcon <?php echo $topicStateIcon; ?>'></div>
		            				<div class='topicName'>
		            					<div class='topicNameInnerDiv1'>
			             					<p class="topicNameInnerP <?php if(strlen($topicTitle)>64) echo 'twoLines'; ?>" title="<?php echo $topicTitle;?>"><?php echo $topicTitle;?></p>
			            				</div>
		            				</div>
	            			</td>
		            			<td class='secondCol'>
		            				<div class='newProgressBarDiv  <?php echo ($isDeactive==1)?'disabled': "" ?>'  onClick="goToTopicPage('<?php echo $topicCode; ?>',<?php echo $isDeactive; ?>,<?php echo $topicWiseDetails[$topicCode][2] ?>)">
		            					<div class='progressText'>
		            						<?php echo $progress;?>%
		            					</div>
		            					<div class='newProgressBar'>
		            						<div class='newProgressBarLower'>
		            						</div>
		            						<div class='newProgressBarUpper' style="width:<?php echo $progress;?>%">
		            						</div>
		            					</div>
		            				</div>
		            				<div class='actionBtnDiv'>
			            				<div class='actionBtn <?php echo ($isDeactive==1 && $actionText!= 'Revise' ) ? "disabled": "" ?>' <?php if((($isDeactive==1 && $progress == "100" && $actionText== 'Revise') ||($isDeactive==0)) && $dataSynchronised===true) { ?> onclick="<?php echo $topicAction; ?>"  title="<?php echo $actionText; ?>" <?php } else if($dataSynchronised===false) { ?> title="Synchronization has not happened, So this feature is not available." <?php } else { ?> title="<?=$deactiveTitle?>" <?php } ?> ><?php echo $actionText; ?>
			            				</div>
			            			</div>
		            			</td>
		            		</tr>
		            	<?php  } ?>
		            	</table>
		            	<div id='topicHeaderLowerBorder'>
		            	</div>
		            </div>
		        </div>

		        <div id="tableContainerOther" style="display: none;">
		            <div id="topicInfoContainerOther">
		            	<div id='topicHeaderTextOther' class='studentDashboardTopicHeaderBg'>
		            		Topics - Other Grades
		            	</div>
		            	<div id='topicHeaderUpperBorderOther'>
		            	</div>
		            	
		            	<table id='topicTableOther'>
		            	<?php
		            	foreach($topicsForOtherGrades as $topicCode=>$detail) {

		            	if (strpos($detail[3], '-') !== false) {
   								$topicTitle = $detail[0]." (Grades ".$detail[3].")";				
   							}
   							else
   							{
   								$topicTitle = $detail[0]." (Grade ".$detail[3].")";				
   							} 						
							$progress = round($detail[1]);
							if($progress == 0)
							{
								$actionText = 'Start';
								$topicStateIcon = 'learnIcon';
								$topicAction  = "startExam('ttSelection','$topicCode','$topicAttemptDetails[$topicCode]','$category','$subcategory',0,$topicWiseDetails[$topicCode][2])";
							}
							elseif($progress>0 && $progress<100) {
								$actionText = 'Complete it!';
								$topicStateIcon = 'learnIcon';
								$topicAction  = "startExam('ttSelection','$topicCode','$topicAttemptDetails[$topicCode]','$category','$subcategory',0,$topicWiseDetails[$topicCode][2])";
							}
							else
							{								
								if($detail[4]!='' || $topicAttemptDetails[$topicCode][4]>1)
								{
									$actionText = 'Revise' ;
									$topicAction = "startRevision('$topicCode')";
								}
								else
								{
									if($detail[2] == 1)
										$higherlevel = 0;
									else
										$higherlevel = 1;
									$actionText = 'Continue';
									$topicAction  = "startExam('ttSelection','$topicCode','$topicAttemptDetails[$topicCode]','$category','$subcategory',$higherlevel,$topicWiseDetails[$topicCode][2])";
								}
								
								$topicStateIcon = 'trophyIcon';		
							}					
							?>

		            		<tr class='enabled' >
		            			<td class='firstCol' onClick="goToTopicPage('<?php echo $topicCode; ?>',0,<?php echo $detail[2] ?>);">
		            				<div class='topicIcon <?php echo $topicStateIcon; ?>'></div>
			            				<div class='topicName'>
			            					<div class='topicNameInnerDiv1'>
				             					<p class='topicNameInnerP' title="<?php echo $topicTitle;?>">
				             							<?php echo $topicTitle; ?>
				            					</p>
				            				</div>
			            				</div>
		            			</td>
		            			<td class='secondCol'>
		            				<div class='newProgressBarDiv' onClick="goToTopicPage('<?php echo $topicCode; ?>',0,<?php echo $detail[2] ?>);">
		            					<div class='progressText'>
		            						<?php echo $progress;?>%
		            					</div>
		            					<div class='newProgressBar'>
		            						<div class='newProgressBarLower'>
		            						</div>
		            						<div class='newProgressBarUpper' style="width:<?php echo $progress;?>%">
		            						</div>
		            					</div>
		            				</div>
		            				<div class='actionBtnDiv'>
			            				<div class='actionBtn'  onclick="<?php echo $topicAction; ?>"  title="<?php echo $actionText; ?>" ><?php echo $actionText; ?>
			            				</div>
			            			</div>
		            			</td>
		            		</tr>
		            	<?php  } ?>
		            	</table>
		            	<div id='topicHeaderLowerBorder'>
		            	</div>
		            </div>
		        </div>
		    </div>
		</div>
	    
	    <input type="hidden" name='mode' id="mode">
	    <input type="hidden" name='ttCode' id="ttCode">	    
	    <input type="hidden" name='isDeactive' id="isDeactive">
	    <input type="hidden" name='higherLevel' id="higherLevel">
	    <input type="hidden" name='topicDesc' id="topicDesc">
	    <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
		<input type="hidden" name="timedTestCode" id="timedTestCode">
		<input type="hidden" name="pendingTopicTimedTest" id="pendingTopicTimedTest">
	    <input type="hidden" name="cls" id="cls" value="<?=$objUser->childClass?>">
	</form>    
<?php include("footer.php"); mysql_close(); ?>

<?php 
function checkIfTopicDeactive($schoolCode,$childClass,$childSection,$ttCode)
{
	$sq	=	"SELECT srno FROM adepts_teacherTopicActivation
			 WHERE teacherTopicCode='$ttCode' AND schoolCode='$schoolCode' AND class=$childClass AND section='$childSection' AND deactivationDate<>'0000-00-00'";
	$rs	=	mysql_query($sq);
	return mysql_num_rows($rs);
}
?>