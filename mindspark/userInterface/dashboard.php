<?php
error_reporting(1);
set_time_limit(0);
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
$isHomeUsage = 0;



if(strcasecmp($category,"STUDENT")==0 && (strcasecmp($subcategory,"School")==0 || strcasecmp($subcategory,"Home Center")==0))
{
    $isAllowedDeactivatedTopicsForHomeUse = isAllowedDeactivatedTopicsForHome($schoolCode, $childClass, $childSection);
    if($isAllowedDeactivatedTopicsForHomeUse)
		$isHomeUsage = isHomeUsage($schoolCode, $childClass);
}

if(in_array($childClass,array(4,5,6,7)))
{
	header("Location: studentDashboard.php?homeUse=$isHomeUsage");	
}
		
			
/*if($programMode == "summerProgram" && strcasecmp($subcategory,"Individual")==0 && $childClass > 5 && $childClass < 9)
{
	//Show summer program
}
else
{*/
	$programMode = "";
//}
$dailyDrillAvailableToday=0;
$broadTopics = array();
$teacherTopics = array();
$tmpTopicArray = array();
$showAllTopics=0;
$baseurl = IMAGES_FOLDER."/newUserInterface/";
if(isset($_POST['chkAllTopics']))
	$showAllTopics = 1;
if($theme==3){
	if(isset($_POST['chkAllTopics1']))
		$showAllTopics = 1;
	else{
		$showAllTopics = 0;
	}
}

	
//$arrTopicsActivated = getTopicActivated($userID,$childClass,$childSection, $objUser->category,$objUser->subcategory,$objUser->schoolCode, SUBJECTNO, $objUser->packageType);
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
	//$topicWiseDetails = SortDataSet($topicWiseDetails,2,true);
}
$sparkieImage = $_SESSION['sparkieImage'];
$topicAttemptDetails = getAttemptDetailsOnTopic($userID,array_keys($topicsAttempted));

?>

<?php include("header.php"); ?>

<title>Dashboard</title>
<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/dashboard/lowerClass.css" rel="stylesheet" type="text/css">
	<script>function activateTopic(i,j,a,b,c){}</script>
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/dashboard/midClass.css" />
    <link rel="stylesheet" href="css/commonMidClass.css" />
	<script>function activateTopic(i,j,a,b,c){}</script>
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/dashboard/higherClass.css?ver=1.1" />
	<script>
		var counter=0;
		var arrayDisablePractice;
		var arrayDisableNormal;
		function activateTopic(i,j,a,b,c){
			var i =i;
			var j=j;
			var a=a;
			var b=b;
			var c=c;
			if(t==i){
				counter++;
			}
			if(counter==1){
				alert("Please select an option from left side.");
				counter=0;
			}
			if(t!=null){
				$("#percentCircle"+t).css({'width':'40px','height':'34px','margin-top':'-11px','margin-right':'3px','-moz-border-radius': '39px','-webkit-border-radius': '39px','border-radius': '39px','padding-top':'15px'});
				$("#outerCircle"+t).css({'width':'50px','height':'43px','margin-top':'10px','margin-right':'10px','-moz-border-radius': '36px','-webkit-border-radius': '36px','border-radius': '36px','padding-top':'15px'});
				$("#"+t).css("display","none");
				$("#topicProgressInnerDiv"+t).css("margin-left","0px");
			}
			$("#percentCircle"+i).css({'width':"70px",'height':'48px','margin-top':'-26px','margin-right':'3px','-moz-border-radius': '45px','-webkit-border-radius': '45px','border-radius': '45px','padding-top':'30px'});
			$("#outerCircle"+i).css({'width':"80px",'height':'55px','margin-top':'-5px','margin-right':'-30px','-moz-border-radius': '45px','-webkit-border-radius': '45px','border-radius': '45px','padding-top':'30px'});
			$("#reportIcon").css("background","url('assets/higherClass/dashboard/report.png') no-repeat 4px -24px");
			$("#reportIcon").css("background-color","black");
			$("#reportLink").attr("onclick","javascript:showReport('"+i+"',0)");
			$("#trailLink").attr("onclick","javascript:showQuesTrail('"+i+"','"+j+"',0)");
			$("#report").attr("onClick","");
			$("#questionTrail").attr("onClick","");
			$("#complete").attr("onClick","startExam('ttSelection','"+i+"','"+a+"','"+b+"','"+c+"')");
			$("#completeIcon").css("background","url('assets/higherClass/dashboard/complete.png') no-repeat 8px -24px");
			$("#practiceIcon").css("background","url('assets/higherClass/dashboard/practice.png') no-repeat 1px -20px");
			$("#completeIcon").css("background-color","black");
			$("#practiceIcon").css("background-color","black");
			$("#practice").attr("onClick","startRevision('"+i+"')");
			$("#questionTrailIcon").css("background","url('assets/higherClass/dashboard/questiontrail.png') no-repeat 3px -17px");
			$("#questionTrailIcon").css("background-color","black");
			$("#"+i).css("display","block");
			$("#topicProgressInnerDiv"+i).css("margin-left","5px");
			t=i;
		}
	</script>
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/closeDetection.js"></script>-->
<script type="text/javascript" src="/mindspark/userInterface/libs/combined.js"></script>
<script>
var langType = '<?=$language;?>';
var infoClick=0;
var messageCheck=0;
var pendingTimedTestOnTT	=	new Array();
$(document).ready(function(e) {
	$.post("commonAjax.php","mode=checkPendingTimedTest",function(data) {
		pendingTimedTestOnTT	=	$.parseJSON(data);
	});
});

function load() {
	 init();
<?php if($theme==1) { ?>	
	var a= window.innerHeight - (47 + 70 + 50 + 23 + 50 + 20);
	$('#topicInfoContainer').css("height",a+"px");
	$(".forHigherOnly").remove();
<?php } else if($theme==2) { ?>
	var a= window.innerHeight - (80 + 19 + 140 + 55);
	$('#topicInfoContainer').css("height",a+"px");
	$(".forLowerOnly").remove();
<?php } else  { ?>
	var a= window.innerHeight - (240);
	$('#topicInfoContainer').css("height",a+"px");
	$('#main_bar').css("height",a+"px");
<?php } ?>	
	if(androidVersionCheck==1){
		$('#topicInfoContainer').css("height","auto");
		$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
		$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
		$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
	}
}

function notActive(){
	if(messageCheck!=1){
	<?php if($programMode=="freeTrial") { ?>	
		alert("For attempting other Topics - Purchase Mindspark.");
	<?php } else { ?>	
		alert("This topic has been de-activated.");
	<?php } ?>	
	}
}


function activeAtHome(){
	if(messageCheck!=1){
		alert("This topic can be done only after 4PM.");
	}
}

function goToPractisePage(ttCode){
	setTryingToUnload();
	location.href="practisePage.php?ttCode="+ttCode;
}
function startRevision(ttCode)
{
	if($.inArray(ttCode,arrayDisablePractice)>=0)
	{
		//alert($.inArray(ttCode,arrayDisablePractice));
		//alert("This topic practice can not be done right now.");
		return false;
	}
	else
	{
		//alert($.inArray(ttCode,arrayDisablePractice));
		setTryingToUnload();
		messageCheck=1;
		document.getElementById("mode").value="topicRevision";
		document.getElementById("ttCode").value=ttCode;
		document.getElementById("frmTeacherTopicSelection").action="controller.php";
		document.getElementById("frmTeacherTopicSelection").submit();
	}
}
function showReport(ttCode,deactiveLink)
{
	/*if(deactiveLink==1)
		return false;*/
	setTryingToUnload();
	document.getElementById("ttCode").value = ttCode;
	document.getElementById("frmTeacherTopicSelection").action = "studentTopicReport.php";
	document.getElementById("frmTeacherTopicSelection").submit();
}
function showQuesTrail(ttCode, ttDesc, deactiveLink)
{
	/*if(deactiveLink==1)
		return false;*/
	setTryingToUnload();
	document.getElementById("ttCode").value = ttCode;
	document.getElementById("topicDesc").value = ttDesc;
	document.getElementById("frmTeacherTopicSelection").action = "topicWiseQuesTrail.php";
	document.getElementById("frmTeacherTopicSelection").submit();
}
function openHelp()
{
	var k = window.innerWidth;
	<?php if($theme==1) { ?> var helpSource= "<?=$baseurl?>theme1/index.html";
	<?php } else if($theme==2) { ?>var helpSource= "<?=$baseurl?>theme2/index.html";
	<?php } else if($theme==3) { ?>var helpSource= "<?=$baseurl?>theme3/index.html"; <?php } ?>
	if(k>1024)
	{
		$("#iframeHelp").attr("height","440px");
		$("#iframeHelp").attr("width","960px");
		$("#iframeHelp").attr("src",helpSource);
		$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':570, 'width':1024});
	}
	else
	{
		$("#iframeHelp").attr("height","390px");
		$("#iframeHelp").attr("width","700px");
		$("#iframeHelp").attr("src",helpSource);
		$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':500, 'width':800});
	}
}
function init()
{
	/*if(parseInt(document.getElementById("pnlTeacherTopicSelection").offsetHeight)<300)
		document.getElementById("pnlTeacherTopicSelection").style.height = "300px";*/
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
function hideBar(){
	if (infoClick==0){
		$("#hideShowBar").text("+");
		$('#viewAllTopics,#homeIcon').fadeOut(300);
		$('#sessionWiseReport').animate({'margin-right':'340px','margin-top':'-104px'},600);
		$('#info_bar').animate({'height':'60px'},600);
		var a= window.innerHeight -130 -27 -57;
		if(androidVersionCheck==1){
		$('#topicInfoContainer').css("height","auto");
		}else{
			$('#topicInfoContainer').animate({'height':a},600);
		}
		infoClick=1;
	}
	else if(infoClick==1){
		$("#hideShowBar").text("-");
        $('#viewAllTopics,#homeIcon').fadeIn(300);
		<?php if($theme==2) { ?>
			$(".forHighestOnly").css("display","none");
		<?php } ?>
		$('#sessionWiseReport').animate({'margin-right':'30px','margin-top':'-69px'},600);
		$('#info_bar').animate({'height':'140px'},600);
		var a= window.innerHeight - (80 + 20 + 140 + 55);
		if(androidVersionCheck==1){
		$('#topicInfoContainer').css("height","auto");
		}else{
			$('#topicInfoContainer').animate({'height':a},600);
		}
		infoClick=0;
	}
}
</script>
<script>
var attemptArray = new Array();
var click=0;
var t=0;
<?php
foreach ($topicAttemptDetails as $ttCode=>$noOfAttempts) {
?>
attemptArray["<?=$ttCode?>"] = <?=$noOfAttempts?>;
<?php } ?>

function showAllTopics()
{
	setTryingToUnload();
	document.getElementById('frmTeacherTopicSelection').submit();
}
function openMainBar(){
	
	if(click==0){
		if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
		
		$("#vertical").css("display","none");
		click=1;
	}
	else if(click==1){
		$("#main_bar").animate({'width':'22px'},600);
		$("#plus").animate({'margin-left':'4px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}
function topicAlert(){
	alert("Please select a topic First.");
}
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
		//attemptNo, currentScore,currentLevel,numberOfLevels,description,practiseModuleId,clusterCode,isDailyDrill,practiseModuleTestStatusId,isInternalRequest,remainingTime
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
	//if (isset($_POST['forDailyDrill'])){
		$selectRecordSQL = "SELECT id, status FROM practiseModulesTestStatus where lastModified>CURDATE() AND status='completed' AND userID=".$_SESSION['userID']." ORDER BY id DESC";
		$selectRecordQuery = mysql_query($selectRecordSQL) or die (mysql_error().$selectRecordSQL);
		if (mysql_num_rows($selectRecordQuery)>0){
			$dailyDrillForDayCompleted['dailyDrillForDayCompleted']=1;
			//echo '<script>$(document).ready(function(){startPractise("'.addcslashes(json_encode($dailyDrillForDayCompleted)).'","'.$pmMsg.'");});</script>';
			//print_r(json_encode($dailyDrillForDayCompleted));
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
	//}
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
	//if (isset($_POST['forDailyDrill'])){
		if($firstLoginToday){
			$remainingTime=300;
			$query="UPDATE practiseModulesTestStatus SET remainingTime=300 WHERE id=$practiseModuleTestStatusId";
			mysql_query($query) or die(mysql_error().$query);
		}
		else if ($remainingTime<30) {
			$dailyDrillForDayCompleted['dailyDrillForDayCompleted']=1;
			//echo '<script>$(document).ready(function(){startPractise("'.addcslashes(json_encode($dailyDrillForDayCompleted)).'","'.$pmMsg.'");});</script>';
			//print_r(json_encode($dailyDrillForDayCompleted));
			return;
		}
	//}
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
	//print_r(json_encode($_SESSION['dailyDrillArray']));
	$dailyDrillAvailableToday=array('pmArray'=>$_SESSION['dailyDrillArray'],'pmMsg'=>$pmMsg);
	//echo '<script>$(document).ready(function(){startPractise("'.addslashes(json_encode($_SESSION['dailyDrillArray'])).'","'.$pmMsg.'");});</script>';
	// check user attainted questions code ends
}
function checkForPractise(){
	$userID = $_SESSION['userID'];	
	$attemptedPracticeSQL="SELECT DISTINCT ad, practiseModuleId, pmAttemptID, status FROM(
		SELECT DISTINCT attemptdate ad, q.practiseModuleId, practiseModuleTestStatusId pmAttemptID, a.`status` 
			FROM practiseModulesQuestionAttemptDetails q, practiseModulesTestStatus a, practiseModuleDetails e  WHERE q.userID=$userID AND a.id=q.practiseModuleTestStatusId AND e.practiseModuleId=q.practiseModuleId AND e.dailyDrill=1 and  e.`status`='Approved'
			UNION	
		SELECT DISTINCT attemptDate ad, q.practiseModuleId, practiseModuleTestStatusId pmAttemptID, a.`status` 
			FROM practiseModulesTimedTestAttempt q, practiseModulesTestStatus a, practiseModuleDetails e  WHERE q.userID=$userID AND a.id=q.practiseModuleTestStatusId AND e.practiseModuleId=q.practiseModuleId AND e.dailyDrill=1 and  e.`status`='Approved'
		) e	WHERE ad<CURDATE()  ORDER by ad DESC";
	$attemptedPracticeSQL = mysql_query($attemptedPracticeSQL) or die(mysql_error().$attemptedPracticeSQL);
	//check for PractiseModule content Attempts
	if(mysql_num_rows($attemptedPracticeSQL) > 0){
		$row = mysql_fetch_array($attemptedPracticeSQL);$row1 = mysql_num_rows($attemptedPracticeSQL)>1?mysql_fetch_array($attemptedPracticeSQL):array('pmAttemptID'=>0);
		if ($row['status']=="in-progress"){
			if ($row['pmAttemptID']!=$row1['pmAttemptID']){ //only 1 continuous attempt for incomplete PM - give same PM
				isPractiseTime($row[1],"Resume");
				return;
			}
		}
	}
	//echo '<script>alert(1);</script>';

	$checkTodaysPractiseSQL="SELECT a.practiseModuleId, a.status,remainingTime, a.lastModified FROM practiseModulesTestStatus a, practiseModuleDetails e
							WHERE DATE(a.lastModified)=CURDATE() AND a.userID=$userID AND e.practiseModuleId=a.practiseModuleId AND e.dailyDrill=1 and  e.`status`='Approved' 
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
	//echo '<script>alert(1);</script>';
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
	//echo '<script>alert(1);</script>';
	$checkOpenPractiseSQL=" SELECT e.practiseModuleId, e.description, e.dailyDrill FROM  practiseModuleDetails e,practiseModulesTestStatus d 
							WHERE d.practiseModuleId=e.practiseModuleId AND d.userID=$userID AND e.dailyDrill=1 AND e.`status`='Approved' AND d.`status`='in-progress' 
							ORDER BY d.lastModified DESC";
	$checkOpenPractiseSQL = mysql_query($checkOpenPractiseSQL) or die(mysql_error().$checkOpenPractiseSQL);
	if(mysql_num_rows($checkOpenPractiseSQL) > 0){
		$row = mysql_fetch_array($checkOpenPractiseSQL);
		isPractiseTime($row[0],"Resume");
		return;
	}
	//echo '<script>alert(1);</script>';
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
function startExam(mode,ttCode, attemptNo, category, subcategory)
{
	<?php if($dailyDrillAvailableToday !== 0 && json_encode($dailyDrillAvailableToday['pmArray'])!="") { 
			echo 'startPractise("'.addslashes(json_encode($dailyDrillAvailableToday['pmArray'])).'","'.$dailyDrillAvailableToday['pmMsg'].'");';
	} else { ?>
		if($.inArray(ttCode,arrayDisableNormal)>=0)
		{
			//alert("This topic can not be done right now.");
			return false;
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
				//alert("You have attempted this topic 20 times already.\nThere are other topics active for you. Please work on the other topics and come back to this when you complete them.");
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
	<?php } ?>
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
        
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.myDetails"></span></a></li>
									<li><a href='changePassword.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.changePassword"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php' onClick="javascript:setTryingToUnload();"><span data-i18n="common.whatsNew"></span></a></li>
									<li><a href='javascript:void(0)' onClick="openHelp()"><span data-i18n="common.help"></span></a></li>
                                    <li><a href='logout.php' onClick="javascript:setTryingToUnload();"><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" onClick="logoff();" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
	
	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
                <div id="home">
                   	<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
                    <div id="dashboardHeading" class="forLowerOnly">&ndash;&nbsp;<span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                    <div class="clear"></div>
                </div>
        </div>
		<div id="info_bar" class="forHigherOnly">
			<div id="topic">
                <div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard" ></span></font></div>
                </div>
                
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div>
				<div class="arrow-right"></div>
				
				<?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0 && $programMode!="freeTrial") { ?>
            <div id="viewAllTopics" class="forHighestOnly" style="margin-left: 260px;margin-top: 26px;position: absolute;">
            	<input type="checkbox" id="chkAllTopics1" name="chkAllTopics1" onClick="showAllTopics()" <?php if($showAllTopics) echo " checked"?>/>Click here to see all topics&nbsp;&nbsp;&nbsp;&nbsp;
				</div>
            <?php } ?>
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
            <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0 && $programMode!="freeTrial") { ?>
            <div id="viewAllTopics">
            	<input type="checkbox" id="chkAllTopics" name="chkAllTopics" onClick="showAllTopics()" <?php if($showAllTopics) echo " checked"?>/>Click here to see all topics&nbsp;&nbsp;&nbsp;&nbsp;
				</div>
            <?php } ?>
            <a href="sessionWiseReport.php"  onclick="javascript:setTryingToUnload();" class="removeDecoration forHigherOnly"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise" style=" <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0 && $programMode!="freeTrial" && $theme==3) echo "margin-top:-120px !important;"?>"></div></a>
		</div>
        
        <div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">-</div>
        
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php"  onclick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php"  onclick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php" onClick="javascript:setTryingToUnload();"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="explore.php" onClick="javascript:setTryingToUnload();"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<a href="viewComments.php?from=links&mode=1" onClick="javascript:setTryingToUnload();"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		
        <div id="tableContainerMain">
			<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			<a id="reportLink" title="Click here to view detailed report" class="report_link removeDecoration">
				<div id="report" onClick="topicAlert();">
					<span id="reportText">Report</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
				<a id="trailLink" title="Click here to view question-wise trail" class="report_link removeDecoration">
				<div id="questionTrail" onClick="topicAlert();">
					<span id="questionTrailText">Question Trail</span>
					<div id="questionTrailIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
				<div class="empty">
				</div>
				<div id="complete" onClick="topicAlert();">
					<span id="completeText" data-i18n="homePage.completeIt"></span>
					<div id="completeIcon" class="circle"><div class="arrow-s"></div></div>
				</div>
				<?php if($programMode!="summerProgram") { ?>
				<div id="practice" onClick="topicAlert();">
					<span id="practiceText" data-i18n="dashboardPage.practice"></span>
					<div id="practiceIcon" class="circle"><div class="arrow-s"></div></div>
				</div>
				<?php } ?>
				<div class="empty">
				</div>
				<div id="higherLevelInfo" class="forLowerOnly"><div id="higherLevelIcon"></div><span data-i18n="dashboardPage.higherLevel"></span></div>
			</div>
			</div>
            <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0) { ?>
        	<div id="viewAllTopics" class="forLowerOnly hidden">
				<input type="checkbox" id="chkAllTopics" name="chkAllTopics" value="1" onClick="showAllTopics()" <?php if($showAllTopics==1) echo " checked"?>/>Click here to see all topics&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <?php } ?>
            <a href="sessionWiseReport.php"  onclick="javascript:setTryingToUnload();" class="removeDecoration forLowerOnly hidden"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
        	<div id="higherLevelInfo" class="forLowerOnly hidden">
            	<div id="higherLevelIcon"></div><span data-i18n="dashboardPage.higherLevel"></span>
            </div>
            <div class="clear"></div>
			<table width="<?php if($theme==1) echo '99%;';else if($theme==2) echo '100%';else echo '100%';?>" border="0" class="endSessionTbl" align="center">
                <tr class="trHead">
                    <td id="menuPadding" width="480px">&nbsp;</td>
                    <td data-i18n="dashboardPage.noOfAttempts" class="td1"></td>
                    <td data-i18n="dashboardPage.noOfQAttempts" class="td2"></td>
                    <td data-i18n="dashboardPage.perCorrect" class="td3"></td>
                    <td data-i18n="dashboardPage.practiceQAttempt" class="td4"></td>
					<?php if(strcasecmp($subcategory,"Individual")==0 || strcasecmp($category,"GUEST")==0){?><td class="newTd" data-i18n="dashboardPage.grades"></td><?php }?>
                    <td width="55px" class="hidden">&nbsp;</td>
                    <td width="115px" class="hidden">&nbsp;</td>
                </tr>
				<?php if(strcasecmp($subcategory,"Individual")==0 || strcasecmp($category,"GUEST")==0){?>
				<tr class="forLowerOnly"><td colspan="8" class="yellowBackground"></td></tr>
				<?php }else {?>
				<tr class="forLowerOnly"><td colspan="7" class="yellowBackground"></td></tr>
				<?php }?>
			</table>
            <div id="topicInfoContainer">
                <table width="<?php if($theme==1) echo '99%;';else if($theme==2) echo '100%';else echo '100%';?>" border="0" class="endSessionTbl" align="center">
                   
					<?php if(strcasecmp($subcategory,"Individual")==0 || strcasecmp($category,"GUEST")==0){?>
                    <tr class="forLowerOnly"><td colspan="8"></td></tr>
					<?php }else {?>
                    <tr class="forLowerOnly"><td colspan="7"></td></tr>
					<?php }?>
					<?php   if($theme==1)
					{
						$diffWidth=230;
						$diffMargin=250;
					}
					else
					{
						$diffWidth=470;
						$diffMargin=480;
					}
					if($programMode=="freeTrial")
						$arrTopicsActivated = sortArrayByArray($arrTopicsActivated, $freeTrilaTopicArray);

					foreach($arrTopicsActivated as $topicCode=>$detail) { 

						if(!$topicWiseDetails[$topicCode])
						{
							$topicWiseDetails[$topicCode][0] = $detail['description'];
							$topicWiseDetails[$topicCode][1] = 0;
							$topicWiseDetails[$topicCode][2] = 0;
							$topicWiseDetails[$topicCode][3] = 0;
							$topicWiseDetails[$topicCode][4] = 0;
						}
						
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

						if(strcasecmp($subcategory,"Individual")==0 || strcasecmp($category,"GUEST")==0){
							$clsLevelArray = getClassLevel($topicCode);                                                        
							if(count($clsLevelArray)>0)
							{                                                                 
								$max_grade = max($clsLevelArray);
								$min_grade = min($clsLevelArray);
								if ($max_grade==$min_grade)
								{
									$topicWiseDetails[$ttCode][5] = $min_grade;
								}
								else
								{
									$topicWiseDetails[$ttCode][5] = $min_grade."-".$max_grade;
								}
								if($max_grade=="" && $min_grade==""){
									$topicWiseDetails[$ttCode][5] = $childClass;
								}
							}else{        //if there are no clusters mapped for the given flow for the topic, do not show it.
								//$topicWiseDetails[$ttCode][5] = $childClass;
	                                                                  continue;
							}
						}
						$quesAttemptedArray	=	getNoOfQuesAttemptedInTheTopic($userID, $topicCode);
						$practiceQuesAttemptedArray = getNoOfPracticeQuesAttemptedInTheTopic($userID, $topicCode);
						$widthProgress	=	(($topicWiseDetails[$topicCode][1]/100)*$diffWidth);
						$widthRemaining	=	$diffWidth - $widthProgress;
						if($theme==1)
							$marginProgressText	=	($topicWiseDetails[$topicCode][1]/100)*$diffMargin;
						
			?>
                   <tr>
                        <td id="td" width="480px">
                        <div class="topicDetailsMainDiv">
                            <div class="topicIconsDiv" <?php if($topicWiseDetails[$topicCode][2]!=1) echo 'style="visibility:hidden"';?>>
                                <div class="starIcon"></div>
                            </div>
                            <div class="topicProgressMainDiv">
                                <div id="topicProgressInnerDiv<?=$topicCode?>" class="topicProgressInnerDiv <?php if($isDeactive==1) echo 'deactiveBorder';?>" onClick="<?php if($isDeactive==0) { ?> activateTopic('<?=$topicCode?>','<?=$topicWiseDetails[$topicCode][0]?>','<?=$topicAttemptDetails[$topicCode]?>','<?=$category?>','<?=$subcategory?>'); <?php } else if($isDeactive==1 && $isAllowedDeactivatedTopicsForHomeUse==0) { ?> notActive();<?php } else if($isDeactive==1 && $isAllowedDeactivatedTopicsForHomeUse==1) { ?> activeAtHome();<?php } ?>">
									<div id="outerCircle<?=$topicCode?>" class="outerCircle">
									<div id="percentCircle<?=$topicCode?>" class="progressCircle forHighestOnly circleColor<?=round($topicWiseDetails[$topicCode][1]/10)?> <?php if($isDeactive==1) echo 'deactiveText'?>" style="margin-left:<?=$marginProgressText?>px"><?=round($topicWiseDetails[$topicCode][1]);?>%</div>
									</div>
                                    
                                    <div class="topicName <?php if($isDeactive==1) echo 'deactiveText'?>" title="<?=$topicWiseDetails[$topicCode][0]?>" <?php if(strlen($topicWiseDetails[$topicCode][0])>47 && $theme==2) echo "style='font-size:1.2em'";?>><?=$topicWiseDetails[$topicCode][0];?></div>
                                    <div class="completeItDiv <?php if($isDeactive==1 || $dataSynchronised===false) { echo 'deactiveBg'; }?>" <?php if($isDeactive!=1 && $dataSynchronised===true) { ?> onClick="startExam('ttSelection','<?=$topicCode?>','<?=$topicAttemptDetails[$topicCode]?>','<?=$category?>','<?=$subcategory?>')"  <?php } else if($dataSynchronised===false) { ?> title="Synchronization has not happened, So this feature is not available." <?php } else {  $arrayDisableNormal[]=$topicCode;?> title="<?=$deactiveTitle?>" <?php } ?> <?php if($topicWiseDetails[$topicCode][1]==100) echo 'data-i18n="homePage.hundered"'; else echo 'data-i18n="homePage.completeIt"';?> ></div>
									
									<div class="practiceDiv <?php if(($isHomeUsage==0 && $topicWiseDetails[$topicCode][1]!=100) || ($programMode=="freeTrial" && $isDeactive==1)) {  echo 'deactiveBg';  } ?>" <?php if((($isHomeUsage==1 || $topicWiseDetails[$topicCode][1]==100) && $programMode!="freeTrial") || ($programMode=="freeTrial" && $isDeactive==0)) { ?>onclick=startRevision('<?=$topicCode?>') <?php } else { $arrayDisablePractice[]=$topicCode;?> title="<?=$deactiveTitle?>" <?php } ?> data-i18n="dashboardPage.practice"></div> 
                                    
                                    
                                    <div class="clear forLowerOnly"></div>
                                    <div class="progressRun" style="margin-left:<?=$widthProgress-10?>px"></div>
                                    <div class="clear forHigherOnly"></div>
                                    <div class="progressPercent <?php if($isDeactive==1) echo 'deactiveText'?>" <?php if($theme==2){ ?> style="margin-left:<?=$widthProgress?>px"<?php } ?> ><?=round($topicWiseDetails[$topicCode][1])?>%</div>
                                    <div class="clear forLowerOnly"></div>
                                    <div class="topicProgressGrad <?php if($isDeactive==1) {echo 'deactiveBg'; }?>" style="width:<?=$widthProgress?>px"></div>
                                    <div class="topicProgressCurrent"></div>
                                    <div class="topicProgressRemain" style="width:<?=$widthRemaining?>px"></div>
                                    <div class="topicProgressEnd"></div>
									<div id="<?=$topicCode?>" class="arrow-left1"></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </td>
                        <td class="tblDataFont" width="<?php if($theme==1) echo '123px;';else if($theme==2) echo '215px;';else echo '142px';?>"><?=$topicWiseDetails[$topicCode][4]?></td>
                        <td class="tblDataFont" width="<?php if($theme==1) echo '153px;';else if($theme==2) echo '60px;';else echo '142px';?>"><?=$quesAttemptedArray["quesAttempted"]?></td>
                        <td class="tblDataFont" width="<?php if($theme==1) echo '84px;';else if($theme==2) echo '116px;';else echo '143px';?>"><?=$quesAttemptedArray["perCorrect"]?></td>
                        <td class="tblDataFont" width="<?php if($theme==1) echo '210px;';else if($theme==2) echo '170px;';else echo '143px';?>"><?=$practiceQuesAttemptedArray["quesAttempted"]?></td>
						<?php if(strcasecmp($subcategory,"Individual")==0  || strcasecmp($category,"GUEST")==0){?><td class="tblDataFont <?php if($isDeactive==1) echo 'deactiveText'?>" width="<?php if($theme==1) echo '185px;';else if($theme==2) echo '68px';else echo '140px';?>"><?=$topicWiseDetails[$ttCode][5];?></td><?php }?>
                        <td width="<?php if($theme==1) echo '55px;';else if($theme==2) echo ''; else echo '0px';?>><a href="javascript:void(0)" onClick="showReport('<?=$topicCode?>',<?=$isDeactive?>)" <?php if($isDeactive==0) { ?> title="Click here to view detailed report" <?php } ?> class="report_link removeDecoration">
                            <div class="reportDiv">
                                <div class="reportDivImote"></div>
                                <div class="reportDivText" data-i18n="dashboardPage.report"></div>
                                <div class="reportDivUl"></div>
                            </div></a>
                        </td>
                        <td width="<?php if($theme==1) echo '115px;';else if($theme==2) echo '130px';else echo '0px';?>><a href="javascript:void(0)" onClick="showQuesTrail('<?=$topicCode?>','<?=$topicWiseDetails[$topicCode][0]?>',<?=$isDeactive?>)" <?php if($isDeactive==0) { ?> title="Click here to view question-wise trail" <?php } ?> class="report_link removeDecoration">
                            <div class="quesTrail">
                                <div class="quesTrailImote"></div>
                                <div class="quesTrailText" data-i18n="dashboardPage.quesTrail"></div>
                                <div class="quesTrailUl"></div>
                            </div></a>
                        </td>
                    </tr>
		<?php } 
			echo "<script>arrayDisablePractice = Array('".implode("','",$arrayDisablePractice)."'); arrayDisableNormal = Array('".implode("','",$arrayDisableNormal)."');</script>";

	?>
        
                </table>
            </div>
        </div>
	</div>
    
    <input type="hidden" name='mode' id="mode">
    <input type="hidden" name='ttCode' id="ttCode">
    <input type="hidden" name='topicDesc' id="topicDesc">
    <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
	<input type="hidden" name="timedTestCode" id="timedTestCode">
	<input type="hidden" name="pendingTopicTimedTest" id="pendingTopicTimedTest">
    <input type="hidden" name="cls" id="cls" value="<?=$objUser->childClass?>">
</form>    
<div style="display:none">
        <div id="openHelp">
			<h2 align="center">Quick Tutorial</h2>
            <iframe id="iframeHelp" width="960px" height="440px" scrolling="no"></iframe>
        </div>
    </div>
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