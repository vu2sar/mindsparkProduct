<?php
include("check1.php");
set_time_limit(0);
include_once("constants.php");
include("functions/functions.php");
include("classes/clsUser.php");
include_once("functions/topic_revision_functions.php");
include_once("classes/clsTopicProgress.php");
include_once("functions/sbaTestFunctions.php");
include("classes/clsRewardSystem.php");
if( !isset($_SESSION['userID'])) {
	header( "Location: error.php");
} 
//echo "NEW KUDOS COUNT IS- ".newKudosCounter();
if(isset($_SESSION['revisionSessionTTArray']) && count($_SESSION['revisionSessionTTArray'])>0)
{
	header("Location: controller.php?mode=login");
}

$dataSynchronised = true;
if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==2 || $_SESSION['offlineStatus']==4))
	$dataSynchronised = false;

if(!isset($_SESSION['notice'])){
	$notice=1;
	$_SESSION['notice']=1;
}
else {
	$notice=0;
}
//exit();

$userID = $_SESSION['userID'];
$sessionID = $_SESSION['sessionID'];
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$baseurl = IMAGES_FOLDER."/newUserInterface/";

$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$userName  = $objUser->username;
$subCategory = $objUser->subcategory;
$currentKudosCount= newKudosCounter($userName);
if($currentKudosCount>0)
{$kudosNotification=1;}
//echo $schoolCode;
$dailyDrillAvailableToday=0;

if(strcasecmp($objUser->category,"School Admin")==0 || strcasecmp($objUser->category,"Teacher")==0)
{
	header("location:../teacherInterface/home.php?live=2");
	exit;
}

$sparkieImg	=	'<img src="assets/sparkie.png" width="15%" height="50%">';
$blackBoardMsg = "<div class='blackboard_message' style='font-size:1em;'>";

if($currentKudosCount>0 && strcasecmp($subCategory,"School")==0)		
	{ $blackBoardMsg.= "<span id='sba_message' style='font-size:1em;'>Someone has sent you a kudos!</span><br/>"; } 

$blackBoardMsgCounter =0;
$commentCounter =0;
$worksheetCounter =0;
$commentMsg = "<div class='comment_message' style='padding-top:0;'>";
if(strcasecmp($objUser->category,"School Admin")==0 || strcasecmp($objUser->category,"Teacher")==0)
{
	header("Location:../teacherInterface/home.php?live=2");
	exit;
}
//Get the comment for the notice board, if any, added by the teacher
$hasWorksheet=array();
if(strcasecmp($objUser->category,"STUDENT")==0 && (strcasecmp($objUser->subcategory,"School")==0 || strcasecmp($objUser->subcategory,"Home Center")==0 || strcasecmp($objUser->subcategory,"Individual")==0) && $schoolCode!="")
{
	$blackBoardMsg .= "<ul style='text-align:left;padding-left: 20px;'>";
	$commentMsg .= "<ul style='text-align:left;padding-left: 25px;'>";
	$query = "SELECT srno, comment FROM adepts_noticeBoardComments WHERE subjectno=".SUBJECTNO." AND schoolCode=$schoolCode AND (class='$childClass' OR class='All')";
	if($childSection!="")
		$query .= " AND (section='$childSection' OR section='All')";
	$query .= " AND datediff(curdate(),date)<noOfDays ORDER BY srno";
	$result = mysql_query($query) or die(mysql_error());
	while($line   = mysql_fetch_array($result))
	{
		if($theme!=3){
			$blackBoardMsg .= "<li style='padding-bottom: 4px;'>".$line['comment']."</li>";
		}
		$commentMsg .= "<li style='padding-bottom: 4px;'>".$line['comment']."</li>";
		$commentCounter++;
	}

	$query = "SELECT a.wsm_id, a.wsm_name, ud.userID, DATE_FORMAT(a.end_datetime, '%d-%b-%Y  %l:%i %p') dueDate
			FROM (worksheet_master a JOIN adepts_userDetails ud ON a.schoolCode=ud.schoolCode AND a.class=ud.childClass)
			LEFT JOIN worksheet_attempt_status b on a.wsm_id=b.wsm_id and ud.userID=b.userID
			WHERE a.assign_flag=1 and a.start_datetime<NOW() and a.end_datetime>NOW() and (ISNULL(b.`status`) OR b.`status`!='completed') and a.schoolCode=$schoolCode and ud.childClass='$childClass' and ud.userID=$userID";
	if($childSection!="")
		$query .= " AND FIND_IN_SET('$childSection',a.section_list)";
	//$query .= " ORDER BY a.end_datetime";echo $query;
	$result = mysql_query($query) or die(mysql_error());
	while($line   = mysql_fetch_array($result))
	{
		array_push($hasWorksheet,$line['wsm_id']);
		//if($theme!=3){
			$blackBoardMsg .= '<li style="padding-bottom: 4px;">Worksheet - <a style="color:inherit;text-decoration:underline;" href="javascript:setTryingToUnload(); showWorksheet(\''.$line['wsm_id'].'\',\'normal\',1);">'.stripcslashes($line['wsm_name']).'</a>, due by '.$line['dueDate'].'</li>';
			$worksheetCounter++;
		//}
	}
	$blackBoardMsg .= "</ul>";
	$commentMsg .= "</ul>";
}

$blackBoardMsg .= "</div>";
$commentMsg .= "</div>";
$qcode = -1;
if(SUBJECTNO==2)
{
	$daTestPrompt = 0;
	$showAqadPrompt = 0;
	$aqadUsage = 0;
	if(SERVER_TYPE == 'LIVE') //dont show in offline mode
	{
		$daTestPrompt = 0;
		$daFeedbackFormFlag = false;
		if(((strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"Individual")==0) || strcasecmp($objUser->category,"GUEST")==0) && $childClass>=4)
		{
			$sqDaTest="SELECT a.paperCode, topicName, 
						IF(ISNULL(weekNumber) AND testStartDate!='0000-00-00',testStartDate,IF(!ISNULL(weekNumber),STR_TO_DATE(CONCAT(YEAR(CURDATE()),((weekNumber+11)%52),' Friday'), '%X%V %W'),''))	testSDate, 
						IF(ISNULL(weekNumber) AND testStartDate!='0000-00-00',testEndDate,IF(!ISNULL(weekNumber),STR_TO_DATE(CONCAT(YEAR(CURDATE()),((weekNumber+11)%52)+1,' Sunday'), '%X%V %W'),''))	testEDate, 
						IFNULL(b.status,0) hasTaken, b.lastmodified testTakenOn
						FROM da_paperCodeMaster a LEFT JOIN da_questionTestStatus b ON a.paperCode=b.paperCode AND b.userID=$userID
						WHERE class=$childClass  AND (weekNumber+12)%52=WEEKOFYEAR(CURDATE()) AND weekNumber>0
						HAVING (isNULL(testTakenOn) OR testTakenON>testSDate)";
			$rsDaTest = mysql_query($sqDaTest);
			if (mysql_num_rows($rsDaTest)>0){
				$rwDaTest = mysql_fetch_array($rsDaTest);
				$daTopicName = $rwDaTest[1];
				$daAnnounceDate = strtotime(date("Y-m-d"));
				$daTestDate = strtotime($rwDaTest[2]);
				if(($daTestDate - $daAnnounceDate)>0)
					$daysLeftForDa = ceil(abs($daTestDate - $daAnnounceDate) / 86400);
				else 
					$daysLeftForDa = 0;
					
				$_SESSION['daPaperCode']= $rwDaTest[0];
				$daTestPrompt = 1;

				if(isset($_SESSION['showDaPrompt']))
					$_SESSION['showDaPrompt']=0;
				else
					$_SESSION['showDaPrompt']=1;
				$daTestStatus = $rwDaTest[4];
				
				if(date("Y-m-d") <= date('Y-m-d', strtotime($rwDaTest[2].' -2 day')) && $rwDaTest[4]!=0)	
				{
					$daTestPrompt = 0;
					$daysLeftForDa = -1;
				}
				if(date("Y-m-d") >= date('Y-m-d', strtotime($rwDaTest[3].' +1 day')) && $rwDaTest[4]!=0)	
				{
					$daTestPrompt = 0;
					$daysLeftForDa = -1;
				}
				if($daysLeftForDa<=0)
				{
					//Get count of completed tests for the user, if the child has taken even one test then the report should be accessible through the icon.
					$query = "SELECT count(id) FROM da_questionTestStatus WHERE userID = $userID AND status='completed' ";
					$result_da = mysql_query($query);
					$line_da = mysql_fetch_array($result_da);
					$daCompletedTestCount = $line_da[0];
				}
				$daPaperCode = $_SESSION['daPaperCode'];
				if($daTestStatus=="completed") {            
					$sqlCheck = "SELECT * FROM da_feedback WHERE userId = $userID and paperCode = '$daPaperCode' ";
					$resultCheck =  mysql_query($sqlCheck) or die(mysql_error().$sqlCheck);
					if(mysql_num_rows($resultCheck) == 0)
						$daFeedbackFormFlag = true;
				}
			}
			else {
				$query = "SELECT count(id) FROM da_questionTestStatus WHERE userID = $userID AND status='completed' ";
				$result_da = mysql_query($query);
				$line_da = mysql_fetch_array($result_da);
				$daCompletedTestCount = $line_da[0];
			}
		}
		
		//da test
	}
	if($dataSynchronised && $daTestPrompt==0){
		$timedTest = checkPendingTimedTest($userID);
	}
	if($timedTest!='')
	{
		$_SESSION['timedTest'] = $timedTest;
		if(isset($_POST['qcode']))
			$_SESSION['qcode'] = $_POST['qcode'];
		$qcode = $_SESSION['qcode'];
		$blackBoardMsg .= '<div id="blackboard_message1" style="font-size:1em;padding-left:20px;">You are due for giving a timed test.<br/>Click <a href="javascript:setTryingToUnload(); showTimeTest(\''.$qcode.'\',\'normal\',1);">here</a> to continue.</div>';
		$blackBoardMsg .= '<input type="hidden" name="pendingTimeTest" id="pendingTimeTest" value="yes">';
		$blackBoardMsgCounter++;
	}
	else{
		$blackBoardMsg .= '<input type="hidden" name="pendingTimeTest" id="pendingTimeTest" value="no">';
	}
		
}
else{
	$blackBoardMsg .= '<input type="hidden" name="pendingTimeTest" id="pendingTimeTest" value="no">';
	$blackBoardMsgCounter++;
}

if($blackBoardMsg !="")
	$blackBoardMsg .= "<br>";
//$blackBoardMsgCounter++;
	
$arrTopicsActivated = getTopicActivated($userID,$childClass,$childSection, $objUser->category,$objUser->subcategory,$objUser->schoolCode, SUBJECTNO, $objUser->packageType,$_SESSION["freeTrialTopics"]);
$priorityAssigned = false;
if(strcasecmp($objUser->category,"STUDENT")==0 && (strcasecmp($objUser->subcategory,"School")==0 || strcasecmp($objUser->subcategory,"Home Center")==0))
	$priorityAssigned = checkForPriority($arrTopicsActivated,$schoolCode,$childClass,$childSection);
$lastSessionTopics = lastsessiontopics($userID, $childClass, $arrTopicsActivated,$priorityAssigned);
if($_SESSION["rewardSystem"]==1)
{
	$objRewards = new Sparkies($userID);
	$sparkieWon	=	$objRewards->getTotalSparkies();
}
else
{
	$sparkieWon = getSparks($userID, $objUser->childClass);
}


	/*$query="INSERT INTO adepts_errorLogs (bugType,bugText,sessionID,userID,schoolCode) VALUES ('sparkieCountShown','$sparkieWon',$sessionID,$userID,'$schoolCode')";
	$result = mysql_query($query) or die(mysql_error());*/



if(strcasecmp($objUser->category,"STUDENT")==0 && (strcasecmp($objUser->subcategory,"School")==0 || strcasecmp($objUser->subcategory,"Home Center")==0))
{
    $isAllowedDeactivatedTopicsForHomeUse = isAllowedDeactivatedTopicsForHome($schoolCode,$childClass,$childSection);
    if($isAllowedDeactivatedTopicsForHomeUse)
		$isHomeUsage = isHomeUsage($schoolCode, $childClass);
	$homeworkLink = homeworkNotification($schoolCode,$childClass,$childSection);
}

if(SERVER_TYPE == 'LIVE') //dont show in offline mode
{
	$query = "SELECT count(*) as countSession from adepts_sessionStatus WHERE userID=$userID limit 5";
	$result = mysql_query($query) or die(mysql_error());
	$line   = mysql_fetch_array($result);
	if($line['countSession']>1){
		$freeTrialMessage=1;
	}else{
		$freeTrialMessage=0;
	}
	$aqadUsage = 0;
	$startTime = 7;  //School start time
	$endTime = 15;  //School end time
	$now = intval(date("H"));
	//Consider after 5 p.m. and before 7 a.m. as home usage and Sundays	
	if($objUser->subcategory=="School" && ($now < $startTime || $now > $endTime || date("D") == "Sun"))
		$aqadUsage = 1;
	else if($objUser->subcategory=="School")
		$aqadUsage = 3;
	else
		$aqadUsage = 1;
	
	$showAqadPrompt = 0;
	
	$todaysDate = date("Y-m-d");
	
		$query = "select * from educatio_educat.aqad_responses where studentID='".mysql_real_escape_string($userID)."' and entered_date like '%".$todaysDate."%' limit 1";
		$result = mysql_query($query) or die(mysql_error());
		if($line  = mysql_fetch_array($result)){
			$_SESSION['aqadBlink']=0;
		}else{
			$_SESSION['aqadBlink']=1;
		}
	if(!isset($_SESSION['showAqadPrompt'])){
		$query = "select * from educatio_educat.aqad_responses where studentID='".mysql_real_escape_string($userID)."' limit 1";
		$result = mysql_query($query) or die(mysql_error());
		if($line  = mysql_fetch_array($result)){
			$showAqadPrompt=0;
			$_SESSION['showAqadPrompt']=0;
		}else{
			$showAqadPrompt=1;
			$_SESSION['showAqadPrompt'] = 1;
		}
	}else{
		$showAqadPrompt=0;
		$_SESSION['showAqadPrompt'] = 0;
	}
	
	
	if(date("D") == "Sun" && $childClass>=5){
		$aqadUsage=0;
	}
	
	if((date("D") == "Sun" || date("D") == "Sat") && ($childClass==3 || $childClass==4)){
		$aqadUsage=0;
	}
}
?>

<?php include("header.php") ?>

<title>Home</title>

<?php
if($theme==1) { ?>
	<link href="css/commonLowerClass.css?ver=1240" rel="stylesheet" type="text/css">
	<link href="css/home/lowerClass.css?ver=4" rel="stylesheet" type="text/css">
<?php } 
else if($theme==2) { ?>
    <link href="css/commonMidClass.css?ver=1240" rel="stylesheet" type="text/css">
    <link href="css/home/midClass.css?ver=9" rel="stylesheet" type="text/css">
<?php //showing feeds 
	$feedsSchool=$schoolCode;if (!$feedsSchool) $feedsSchool=0; 
	echo '<script>var childClass='.$childClass.',schoolCode='.$feedsSchool.',userID='.$userID.';</script>';?>
    <link href="css/home/feedsMid.css" rel="stylesheet" type="text/css">
    <script src="libs/home/feeds.js?ver=1"></script>
	<script>
	function startDaTest()
	{
		setTryingToUnload();
		$("#frmDaTest").submit();
	}
	function showReport()
	{
		setTryingToUnload();
		window.location.href = "daTestTopic.php";
	}
	function showDaFeedback()
	{
		setTryingToUnload();
		window.location.href = "da_feedbackForm.php";
	}
	</script>
<?php } 
else if($theme==3) { ?>
    <link href="css/commonHigherClass.css?ver=1241" rel="stylesheet" type="text/css">
    <link href="css/home/higherClass.css?ver=3" rel="stylesheet" type="text/css">
	<script>
	function startDaTest()
	{
		setTryingToUnload();
		$("#frmDaTest").submit();
	}
	function showReport()
	{
		setTryingToUnload();
		window.location.href = "daTestTopic.php";
	}
	function showDaFeedback()
	{
		setTryingToUnload();
		window.location.href = "da_feedbackForm.php";
	}
	var counter=0;
	function showNoticeBoard(){
			$("#noticeBoardMainDiv").css("display","block");
			$("#note").html("0");
	}
	function closeNoticeBoard(){
		setTryingToUnload();		
		$("#noticeBoardMainDiv").css("display","none");
	}
	</script>
<?php } ?>

<link href="css/home/prompt1.css?ver=5" rel="stylesheet" type="text/css">
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<!-- <link rel="stylesheet" type="text/css" href="css/colorbox.css"> -->
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js?ver=11"></script>
<script src="libs/closeDetection.js"></script>
<script>
function mindsparkHandshake()
{
	$.ajax('commonAjax.php', {
		type: 'post',
		async : false,
		data: "mode=setAqadSession&func=checkLogin&username=<?=$userName?>",
		success: function (response) {
			if($.trim(response))
			{
				$("#aqadSession").val($.trim(response));
				$("#startAqad").submit();
			}
		},
		error: function () {
			//alert('Something went wrong...');
		}
	});
	/*$.post("commonAjax.php","mode=setAqadSession&func=checkLogin&username=<?=$userName?>",function(response){ //Logging in...
		if($.trim(response))
		{
			$("#aqadSession").val($.trim(response));
			$("#startAqad").submit();
		}
	});*/
}
<?php
if (count($hasWorksheet)>0){
	?>
	function showWorksheet(wsID,type,flag){
		var validWorksheets=JSON.parse('<?=json_encode($hasWorksheet)?>');
		if (validWorksheets.indexOf(wsID)<0) return;
		document.getElementById("wsm_id").value = wsID;
		document.getElementById("frmWorksheet").action = "controller.php";
		document.getElementById("frmWorksheet").submit();
	}
	<?php
} ?>

var pendingTimedTestOnTT	=	new Array();
function storageEnabled() {
    try {
        localStorage.setItem("__test", "data");
    } catch (e) {
        if (/QUOTA_?EXCEEDED/i.test(e.name)) {
            return false;
        }
    }
    return true;
}
function blink1()
{
	if(x%2) 
	{
		$("#helpMsg").css("color","#fbd212");
	}
	else
	{
		$("#helpMsg").css("color","#FFF");
	}
	
	x++;
	if(x>2){x=1};
	setTimeout("blink1()",1000);
}
var langType = '<?=$language;?>';
	function load(){
		<?php if($notice==1 && $theme==3) { ?>
            if($("#noticeBoardDetails").text()!="")
			showNoticeBoard();
		<?php }?>
		
		<?php if($theme==1) { ?>
			//var a= window.innerHeight - (80);
			//$('#container1').css({"height":a+"px","overflow-y","auto"});
		<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			var c= window.innerHeight - (530);
			$('#leftInfo').css({"height":a+"px"});
			$('#topicInfoContainer').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			if(innerHeight<610){
				$('#viewAllDivLowerClass').css({"margin-top":"35px"});
				$('#noticeBoardMainDiv').css({"margin-top":"125px"});
			}else{
				$('#viewAllDivLowerClass').css({"margin-top":b+"px"});
				$('#noticeBoardMainDiv').css({"margin-top":c+"px"});
			}

		<?php } ?>
		<?php if($_SESSION["rewardTheme"]=="boy" && $theme==3) { ?>
			$("#main_bar").css("background-color","#646464");
			$("body").css("background-image","url('assets/rewards/themeBoy/PageBG.png')");
			$("#drawer5").css("border-bottom", "2px solid #7FA76E");
		<?php }else if($_SESSION["rewardTheme"]=="girl" && $theme==3) { ?>
			$("body").css("background-image","url('assets/rewards/themeGirl/treeIMG.png')");
			$("body").css("background-position","bottom right");
			$("#drawer5").css("border-bottom", "2px solid #7FA76E");
		<?php } ?>
	}

$(document).ready(function(e) {
    if (!storageEnabled()&&!$('.promptContainer').is(":visible")){
          var prompts = new Prompt({
                text: 'You are currently working in the private browsing mode. We recommend that you switch to normal browsing mode as some features of Mindspark may not work in this mode.<br><a href="http://ipad.about.com/od/ipad_basics/ss/How-To-Enable-Cookies-Turn-On-Private-Browsing-And-Other-iPad-Safari-Settings.htm">click here to learn to switch to normal mode.</a>',
                type: 'alert',
                func1: function () {
                    $("#prmptContainer_private").remove();
                },
                promptId:'private'
            });
    }
	
<?php
	if($_SESSION["schoolCode"]==47745 && $_SESSION["childClass"]==3) 
		echo '$("#backToOld").hide();';
	if($theme==2)
	{ ?>
	$(window).resize(function() {
		var i=1;
		$(".progressBar").each(function(index, element) {
			$("#progressTextTopic"+i).css({"margin-left":parseInt($("#progressBarTopic"+i).css('margin-left')) + $("#progressBarTopic"+i).width() + 10 +"px"});
			i++;
		});
	});
<?php } ?>
    <?php
		if($theme==2 ||$theme==3)
		{ ?>
			$(".topicProgressDiv").mouseover(function(){
				$(".topicProgressDiv").removeClass("topicProgressDivFocus");
				$(".eiColors").hide();
				
		<?php if($priorityAssigned) { ?>
				$(".topicProgressInnerDiv").removeClass("topicProgressInnerDivFocusPriority");
				$(".priorityNo").removeClass("priorityNoFocus");				
		<?php } else { ?>		
				$(".topicProgressInnerDiv").removeClass("topicProgressInnerDivFocus");
		<?php } ?>
				$(".topicName").removeClass("topicNameFocus");
				$(this).addClass("topicProgressDivFocus");
				$(this).find(".eiColors").show();
				$(this).find(".topicProgressInnerDiv").addClass("topicProgressInnerDivFocus");
				$(this).find(".priorityNo").addClass("priorityNoFocus");
				$(this).find(".topicName").addClass("topicNameFocus");
			});
			$(document).ready(function(e) {
				$(".progressRun").remove();
			});
	<?php	}
		$activatedTopicCodeArr	=	array_keys($arrTopicsActivated);
		for($i=0;$i<4;$i++) { //$lastSessionTopics[$i][2]=100;
			if($lastSessionTopics[$i])
			{
				unset($arrTopicsActivated[$lastSessionTopics[$i][0]]);
			}
			else
			{
				if(count($arrTopicsActivated)>0)
				{
					$activatedTopicCodeArr		=	array_keys($arrTopicsActivated);
					$lastSessionTopics[$i][0]	=	$activatedTopicCodeArr[0];
					$lastSessionTopics[$i][1]	=	$arrTopicsActivated[$activatedTopicCodeArr[0]];
					$lastSessionTopics[$i][2]	=	0;
					unset($arrTopicsActivated[$lastSessionTopics[$i][0]]);
				}
				else
				{?>
					$(".topicProgressInnerDiv:eq(<?=$i?>)").css("visibility","hidden");<?php
				}
			}
			if($theme==1) { 	?>
				$("#progressTopic<?=$i+1?>").css("width","<?=$lastSessionTopics[$i][2]?>%");
				$("#incompleteTopic<?=$i+1?>").css("width","<?=(100-$lastSessionTopics[$i][2])?>%");
				$("#progressRun<?=$i+1?>").css("margin-left","<?=round((62 * $lastSessionTopics[$i][2]) / 100);?>%");
			<?php }
			else { 
				if(round($lastSessionTopics[$i][2])==100) { ?>
					$("#progressTextTopic<?=$i+1?>").hide();
					$("#progressBarTopic<?=$i+1?> .progressBarText").css("margin-left","25px");
				<?php } ?>
				$("#progressTopic<?=$i+1?>").css("width","<?=round(($lastSessionTopics[$i][2]) * (89/100))?>%");
				$("#incompleteTopic<?=$i+1?>").css("width","<?=round((100-$lastSessionTopics[$i][2]) * (89/100))?>%");
				$("#progressBarTopic<?=$i+1?>").css("margin-left","<?=round((95 * $lastSessionTopics[$i][2]) / 100);?>%");
				$("#progressTextTopic<?=$i+1?>").css({"margin-left":parseInt($("#progressBarTopic<?=$i+1?>").css('margin-left')) + $("#progressBarTopic<?=$i+1?>").width() + 10 +"px"});
			<?php } 
		}?>
	
	
	
	$(".priorityNo").mouseover(function(e){
		var xPos = e.pageX;
		var yPos = e.pageY;
		$("#tooltipForPriority").css({"top":yPos,"left":xPos});
		$("#tooltipForPriority").show();
	});
	$(".priorityNo").mouseleave(function(e){
		$("#tooltipForPriority").hide();
	});
	$(document).on('touchstart', '.priorityNo', function(e) {
		var xPos = e.originalEvent.touches[0].pageX;
		var yPos = e.originalEvent.touches[0].pageY;
		$("#tooltipForPriority").css({"top":yPos,"left":xPos});
		$("#tooltipForPriority").show();
	});
	$(document).on('touchend', '.priorityNo', function(e) {
		$("#tooltipForPriority").hide();
	});
});

$(document).ready(function(e) {
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	<?php if($freeTrialMessage==0 && $_SESSION['freeTrial']==1) {
		$_SESSION['freeTrial']=0;
		 ?>
		$.fn.colorbox({'href':'#freeTrialMessage','inline':true,'open':true,'escKey':true, 'height':390, 'width':700});
	<?php } ?>
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		$(".translation").i18n();
		$(document).attr("title",i18n.t("homePage.title"));
		var lastTopicText	=	i18n.t("homePage.lastTopicText");
		$("#sparkieWonText").html(i18n.t("homePage.sparkieWon"));
	});

	 $("#sparkieInfoDivText").click(function() {
		$("#sparkieInfo").dialog({
			width: "400px",
			position: "right",
			draggable: false,
			resizable: false,
			modal: true
		});
	});

	$(".ui-widget-overlay").live ("click",function () {
		$("#sparkieInfo").dialog( "close" );
	});
	 $("#sparkieInfo").live ("click",function () {
		$("#sparkieInfo").dialog( "close" );
	});
	$("#ui-dialog-titlebar").live ("click",function () {
		$("#sparkieInfo").dialog( "close" );
	});

<?php //if($linkSummer=="" && $dataSynchronised === true) {
	if($dataSynchronised === true) { ?>
	$.post("commonAjax.php","mode=checkPendingTimedTest",function(data) {
		pendingTimedTestOnTT	=	$.parseJSON(data);
	});		
<?php } ?>	
<?php //if($feedBackFlag===1) { ?>
	//summerFeedback();
<?php //} ?>

});
var attemptArray = new Array();
<?php
$total=0;
foreach ($lastSessionTopics as $i=>$noOfAttempts) {
	if($total<4)
	{
	if(!isset($noOfAttempts[5]))
		$noOfAttempts[5] = 0;
?>
attemptArray[<?=$i?>] = <?=$noOfAttempts[5]?>;
<?php $total++; } } ?>

var col = new String();
var x=1;

function showPrompt(){
	var prompts = new Prompt({
        text: "Here's how long you've been Mindspark-ing!<br/>Time is in h:mm format.",
        type: 'alert',
        func1: function() {
            jQuery("#prmptContainer_openID").remove();
        },
        promptId: 'openID'
    });
}

function init()
{
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins
}
function logoff()
{
	setTryingToUnload();
    window.location="logout.php";
}
function viewAll()
{
	<?php if($isHomeUsage) { ?>
		var homeUse = 1;
	<?php } else { ?>
		var homeUse = 0;
	<?php } ?>

	if(document.getElementById("pendingTimeTest").value=="yes")
	{
		alert(i18n.t("homePage.timedTestText"));
		showTimeTest('<?=$qcode?>','normal',1);
		return;
	}
	else{
		setTryingToUnload();		
		window.location = "dashboard.php?homeUse="+homeUse;		
	}
}
function doExercise()
{
	setTryingToUnload();
	window.location = "exercise_selection.php";
}
function showTimeTest(qcode,quesCategory,showAnswer)
{
	continueTopic();
}
function showPrevComments()
{
	setTryingToUnload();
	window.location = "viewComments.php?from=links&mode=1";
}
function openHelp()
{
	setTryingToUnload();
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

/*function buddyMsg()
{
	alert("Coming soon!");
} */

/*function summerFeedback(){	
	$.fn.colorbox({'href':'#summerFeedback','inline':true,'open':true,'escKey':true, 'height':280, 'width':600});
}

function submitFeedback() {	
	var feedBack	=	$("#taSummerFeedback").val();
	feedBack = feedBack.trim();
	if(feedBack.trim() == "") {
		alert("Please enter your feedback.");
		$("#taSummerFeedback").focus();
	} else {
		$('.buttonFBsubmit').hide();
		$.post("commonAjax.php","mode=summerFeedback&feedBack="+feedBack,function(data){
			if(data)
			{
				$.fn.colorbox.close();
			}
			else
				$('.buttonFBsubmit').show();
		});
		
	}
}*/

function sendAQADResponse(qcode,correct){
	if (document.getElementById('option1').checked) {
	  var userAnswer = document.getElementById('option1').value;
	}else if (document.getElementById('option2').checked) {
	  var userAnswer = document.getElementById('option2').value;
	}else if (document.getElementById('option3').checked) {
	  var userAnswer = document.getElementById('option3').value;
	}else if (document.getElementById('option4').checked) {
	  var userAnswer = document.getElementById('option4').value;
	}else{
		alert("Please select an option!");
		return true;
	}
	$('.submitButtonAQAD').attr('onclick','');
	var paperCode = document.getElementById('papercode').innerHTML;
	if(correct==1){
		correct = 'A';
	}else if(correct==2){
		correct = 'B';
	}else if(correct==3){
		correct = 'C';
	}else if(correct==4){
		correct = 'D';
	}
	if(correct==userAnswer){
		var score=1;
	}else{
		var score=0;
	}
	var userID =<?php echo json_encode($userID); ?>;
	var explaination = document.getElementById('aqadExplaination').value;
	var aqadDate = $("#aqadDate").val();
	$.post("aqadResponse.php","studentID="+userID+"&paperCode="+paperCode+"&product='MS'&qcode="+qcode+"&student_answer="+userAnswer+"&score="+score+"&explaination="+encodeURIComponent(explaination)+"&class=<?php echo $childClass; ?>&aqadDate="+aqadDate,function(data){
		if(data)
		{
			$(".submitButtonAQAD").css("display","none");
			$("#divExplain").css("display","none");
                        if(explaination!='')
                        {
			$("#userResponse").html("<b>Your Answer: "+userAnswer+"</b><br/><br/><div><b>Your Explanation:</b>"+explaination+"<br/><br/>Come back tomorrow to see the correct answer</b></div>");
                    }
                    else
                    {
                        $("#userResponse").html("<b>Your Answer: "+userAnswer+"<br/><br/>Come back tomorrow to see the correct answer</b></div>");
                    }
			//$("#userResponse").css("display","block");
			$("#userResponse").show();
			$("#aqadIcon").removeClass('aqadBlink');
			$(".aqad").removeClass('aqadBlink');
			$(".redCircleAqad").html("0");
		}else{
			alert("Please answer again!");
		}
	});
	var datatoPass ="mode=ReloadAQADAfterUserAnswer&class=<?php echo $childClass; ?>&student_answer="+userAnswer+"&studentID="+userID;
	$.ajax({
		  url: "../userInterface/commonAjax.php",
		  data : datatoPass ,
		  cache: false,
		  type: "POST",
		  success: function(html){
		    $("#common-aqad-div").html(html);
		  }
		});
}

// function goToByScroll(id){
//       // Remove "link" from the ID
// 	 $("#11").css("display","none");
// 	  $("#22").css("display","none");
// 	   $("#33").css("display","none");
//     $("#"+id).css("display","block");
// }

/*function cancelFeedback(){
	$('.buttonFBsubmit').hide();
	var feedBack = "Ignored";	// On No thanks button press ignored will store in db.
	$.post("commonAjax.php","mode=summerFeedback&feedBack="+feedBack,function(data){
		if(data)
		{
			$.fn.colorbox.close();
		}
		else
			$('.buttonFBsubmit').show();
	});
}*/

function askMeLater(){
	$.fn.colorbox.close();
}

function shareParentEmailIds()
{
	var emailids	=	$("#emailids").val();
	
	if($.trim(emailids) == "") {
		alert("Please enter email id.");
		$("#emailids").focus();
		
	} else if(validateEmail(emailids)) {
		$("#saveEmailids").attr("disabled", true);
		$("#sendReminder").attr("disabled", true);
		
		$.post("commonAjax.php","mode=shareParentEmailIds&emailids="+emailids,function(data){
			if($.trim(data) == "0") {
				alert("Error in sending email. Please try again.");
				$("#saveEmailids").attr("disabled", false);
				$("#sendReminder").attr("disabled", false);
			} else {
				donePrompt[11] = 2;
				sample();
			}
		});
		
	} else {
		alert("Please check email id.");
		$("#emailids").focus();
	}
}
function remindParentEmailIds()
{
	$("#saveEmailids").attr("disabled", true);
	$("#sendReminder").attr("disabled", true);
	$.post("commonAjax.php","mode=remindParentEmailIds",function(data){
		if($.trim(data) == "0") {
			alert("Something went wrong! Please try again.");
			$("#saveEmailids").attr("disabled", false);
			$("#sendReminder").attr("disabled", false);
		} else {
			donePrompt[11] = 1;
			sample();
		}
	});
}
function validateEmail(email)
{
	var reg = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
	if (reg.test(email)) {
		return true; 
	}
	else {
		return false;
	}
}
// function showAQAD(){
// 	$.fn.colorbox({'href':'#aqadContainer','inline':true,'open':true,'escKey':true, 'height':600, 'width':800});
// }
// function closePromptAQAD(){
// 	$(".aqadPrompt").css("display","none");
// }
function closeDaTestPrompt(){
	$(".daTestPrompt").css("display","none");
}
function schoolUserShowAQAD()
{
	$("#schoolUsersPrompt").css("display","block");
	$(".aqadPrompt").css("display","block");
}
</script>
<script>

function resetKudosCounter(path)
{
	if(<?=date("H")?><16)
	{
		alert("You will be able to see the kudos sent to you only after 4pm.");
	}
	else
	{
		$.ajax({
			type:'POST',
			url: "kudosAjax.php",
			data: {resetKudos: "YES",userName: "<?= $userName ?>"},
			success: function() { 
				setTryingToUnload();
				window.location.href = path;
			} // or function=date if you want date
		});
	}
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
	// check user attainted questions code ends
}


function checkForPractiseTopicPage(){
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
		$row = mysql_fetch_array($attemptedPracticeSQL);$row1 = mysql_num_rows($attemptedPracticeSQL)>1?mysql_fetch_array($attemptedPracticeSQL):array('pmAttemptID'=>0);
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
							WHERE d.practiseModuleId=e.practiseModuleId AND d.userID=$userID AND e.dailyDrill=1 AND e.`status`='Approved' AND d.`status`='in-progress' 
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
$schoolCodeArray = array('3332611','524522','252071','650967','420525','411876','2387554');
if(in_array($schoolCode, $schoolCodeArray) && in_array($childClass,array(4,5,6,7)))
{		
	if (isDailyDrillSchool($childClass)) checkForPractiseTopicPage();
}
else
{	
	if (isDailyDrillSchool($childClass)) checkForPractise();
}


?>
<!--  code for Daily Drill ends here.	-->
<script>
function continueTopic()
{
	<?php if($dataSynchronised === false) { ?>
		alert("Synchronization has not happened, So this feature is not available.");
	<?php } else if($dailyDrillAvailableToday !== 0 && json_encode($dailyDrillAvailableToday['pmArray'])!="") { 
		echo 'startPractise("'.addslashes(json_encode($dailyDrillAvailableToday['pmArray'])).'","'.$dailyDrillAvailableToday['pmMsg'].'");';
	 	} else { ?>
		var ttCode=document.getElementById("ttCode").value;
		document.getElementById("frmTeacherTopicSelection").action="controller.php";
		setTryingToUnload();
		document.getElementById("frmTeacherTopicSelection").submit();
	<?php } ?>
}
function startExam(mode,ttCode, attemptNo) //ttAttemptno 1279
{
	<?php if($dataSynchronised === true) { 
		if($dailyDrillAvailableToday !== 0 && json_encode($dailyDrillAvailableToday['pmArray'])!="") { 
			echo 'startPractise("'.addslashes(json_encode($dailyDrillAvailableToday['pmArray'])).'","'.$dailyDrillAvailableToday['pmMsg'].'");';
		} 
		else { ?>
			if(pendingTimedTestOnTT[ttCode])
			{
				alert(i18n.t("homePage.timedTestText"));
				$("#pendingTopicTimedTest").val("yes");
				$("#ttCode").val(ttCode);
				$("#timedTestCode").val(pendingTimedTestOnTT[ttCode]);
				showTimeTest(pendingTimedTestOnTT[ttCode],'normal',1);
				return false;
			}
			else if(document.getElementById("pendingTimeTest").value=="yes")
			{
				alert(i18n.t("homePage.timedTestText"));
				showTimeTest('<?=$qcode?>','normal',1);
				return false;
			}
			else if(attemptNo>=20)
			{
				var flag = 0;
				for(ttCode1 in attemptArray)
				{
					if(attemptArray[ttCode1]<=1)  //implies some topic pending for completion
					{
						flag =1;
						break;
					}
				}
				if(flag)
				{
					alert(i18n.t("homePage.completedText"));
					return false;
				}
			}
			$('#frmTeacherTopicSelection #mode').val(mode);
			$('#frmTeacherTopicSelection #ttCode').val(ttCode);
			document.getElementById("frmTeacherTopicSelection").action="controller.php";
			setTryingToUnload();
			//console.log($('#frmTeacherTopicSelection').find('input').serialize());return;
			document.getElementById("frmTeacherTopicSelection").submit();
		<?php }
	} else { ?>
		alert("Synchronization has not happened, So this feature is not available.");
	<?php } ?>
}
</script>


<style>
#offlineBlock {
	width:100%;
	height:15px;
	background-color:#FF0000;
	color:#FFFFFF;
	font-weight:bolder;
	text-align:center;
}
.submitButtonAQAD{
    margin-left: inherit;
}
.spanExplainNote
{
    font-style: italic;
    font-size: small;
    font-weight: bold;
}
</style>
</head>
<?php if($_SESSION['rewardSystem']==1) { include("prompt.php"); }else{ include("prompt.php"); $_SESSION['sparkieImage'] = 'level1'; } ?>
<body class="translation" onLoad="load(); myPrompts();" onResize="load()">
	<div style="display: none">
		<?php if (count($hasWorksheet)>0){ ?>
		<form method="POST" id="frmWorksheet" action="controller.php" name="frmWorksheet">
			<input type="hidden" name="mode" id="mode" value="worksheet">
			<input type="hidden" name="wsm_id" id="wsm_id">
			<input type="hidden" name="worksheetID" id="worksheetID">
			<input type="hidden" name="quesCategory" id="quesCategory" value="worksheet">
			<input type="hidden" name="section" id="section" value="<?=$childSection?>">
			<input type="hidden" name="student_userID" id="student_userID" value="<?=$userID?>">
			<input type="hidden" name="accessFromStudentInterface" id="accessFromStudentInterface" value="1">
			<input type="hidden" name="userType" id="userType" value="">
		</form>
		<?php } ?>
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
<span id="tooltipForPriority" data-i18n="homePage.priorityText"></span>
<?php if($dataSynchronised === false) { ?>
<div id="offlineBlock">Scheduled sync has not happened, certain features will be blocked.</div>
<?php }?>
	<form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection" method="POST">
    <div id="teaserDiv"></div>
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
                                    <li><a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();" ><span data-i18n="homePage.myDetails"></span></a></li>
									<li><a href='changePassword.php' onClick="javascript:setTryingToUnload();" ><span data-i18n="homePage.changePassword"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>

		<div id="studentInfoLowerClass" class="forHighestOnly">
			<?php if($childClass!=1 && $childClass!=2 && $childClass!=10 && $aqadUsage==1){ ?>
			<div id="aqadIcon" onClick="mindsparkHandshake()" <?php if($_SESSION['aqadBlink']==1) echo '';/*'class="aqadBlink"';*/ ?>><?php if($_SESSION['aqadBlink']==1) echo '<div class="redCircleAqad">1</div>'; else echo ''; ?></div>
			<?php } ?>
			<?php if($childClass!=1 && $childClass!=2 && $childClass!=10 && $aqadUsage==3 && $context=="India"){ ?>
			<div id="aqadIcon" onClick="schoolUserShowAQAD()" <?php if($_SESSION['aqadBlink']==1) echo '';/*'class="aqadBlink"';*/ ?>><?php if($_SESSION['aqadBlink']==1) echo '<div class="redCircleAqad">1</div>'; else echo ''; ?></div>
			<?php } ?>
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();" ><span data-i18n="homePage.myDetails"></span></a></li>
									<!--<li><a href='javascript:void(0)' onClick="buddyMsg()"><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
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

        <a href='javascript:void(0)' onClick="openHelp();" class="removeDecoration">
        <div id="help" class="hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        </a>
        <a href="logout.php" onClick="javascript:setTryingToUnload();" class="removeDecoration">
            <div id="logout" class="hidden">
                <div class="logout"></div>
                <div class="logoutText" data-i18n="common.logout"></div>
            </div>
        </a>
        <a href="whatsNew.php" class="removeDecoration">
            <div id="whatsNew" class="hidden">
                <div class="whatsNew"></div>
                <div class="whatsNewText" data-i18n="common.whatsNew"></div>
            </div>
        </a>
		<?php if($childClass!=1 && $childClass!=2 && $childClass!=10 && $aqadUsage==1){ ?>
            <div id="aqad" class="hidden" onClick="mindsparkHandshake()">
				<?php if($_SESSION['aqadBlink']==1) echo '<div class="redCircleAqad">1</div>'; else echo ''; ?>
                <div class="aqad<?php if($_SESSION['aqadBlink']==1) echo '';/*' aqadBlink';*/ ?>" ></div>
				<div class="logoutText">AQAD</div>
            </div>
		<?php } ?>
		<?php if($childClass!=1 && $childClass!=2 && $childClass!=10 && $aqadUsage==3){ ?>
            <div id="aqad" class="hidden" onClick="schoolUserShowAQAD()">
            	<?php if($_SESSION['aqadBlink']==1) echo '<div class="redCircleAqad">1</div>'; else echo ''; ?>
                <div class="aqad<?php if($_SESSION['aqadBlink']==1) echo '';/*' aqadBlink';*/ ?>" ></div>
				<div class="logoutText">AQAD</div>
            </div>
		<?php } ?>
		<?php if($daTestPrompt==1 || $daTestStatus=="completed" || $daCompletedTestCount>0) { ?> 
		<a href="javascript:void(0)" onClick="<?php if($daTestStatus!="completed" && $daTestPrompt==1) { ?>javascript:$('.daTestPrompt').show()<?php } else if($daFeedbackFormFlag == true) { ?>showDaFeedback();<?php } else { ?>showReport(); <?php } ?>" class="removeDecoration">
			<div id="daTest" class="<?= $theme != 3 ? 'hidden':'';?>">
                <div class="daTest"></div>
                <div class="daTestText">Super Test</div>
            </div>
		</a>
		<?php } ?>
    </div>
    <div class="clear"></div>
    <div id="container1">
    	<div id="info_bar" class="forHighestOnly">
			<div id="themeImage"></div>
			<div id="topic">
                <div id="home">
                	<a href="home.php" onClick="javascript:setTryingToUnload();">
                        <div id="homeIcon"></div>
                    </a>
                    <div id="homeText" class="hidden"><span class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>

				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div>
				<div class="arrow-right"></div>
                <div id="activatedAtHome" class="forLowerOnly">
                	<div id="activatedAtHomeIcon"></div>
                    <div class="clear"></div>
                </div>
				<div class="clear"></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$objUser->childName?></strong>
			</div>
            <div class="clear"></div>
            <a href="sessionWiseReport.php" onClick="javascript:setTryingToUnload();"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<?php if($_SESSION['rewardSystem']==1) {
			?>
			<!-- <div id="badges">
					<br/>
					<?php if(count($recentBadge)==1){
					?>
					<div class="circleContainer" id="circleContainer1"><div id="badge1" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[0]?>.png') no-repeat 0 0;"></div></div>
					<?php } ?>
					<?php if(count($recentBadge)==2){
					?>
					<div class="circleContainer" id="circleContainer1"><div id="badge1" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[0]?>.png') no-repeat 0 0;"></div></div>
					<div class="circleContainer" id="circleContainer2"><div id="badge2" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[1]?>.png') no-repeat 0 0;"></div></div>
					<?php } ?>
					<?php if(count($recentBadge)==3){
					?>
					<div class="circleContainer" id="circleContainer1"><div id="badge1" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[0]?>.png') no-repeat 0 0;"></div></div>
					<div class="circleContainer" id="circleContainer2"><div id="badge2" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[1]?>.png') no-repeat 0 0;"></div></div>
					<div class="circleContainer" id="circleContainer3"><div id="badge3" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[2]?>.png') no-repeat 0 0;"></div></div>
					<?php } ?>
				</div> -->
			<?php } ?>
			<a href="activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
			<div id="drawer1">
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<div id="drawer3" onClick="viewAll()"><div id="drawer3Icon"></div>DASHBOARD
			</div>
			<div id="drawer4"><a href="explore.php" onclick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<table><tr><td><a href="../kudos/home.php"><img src="assets/kudos.png" width=46px height=46px /></a></td><td><h2>Kudos!</h2></td></tr></table>-->
        <?php if(strcasecmp($subCategory,"School")==0) { ?>
        <div onClick="resetKudosCounter('src/kudos/kudosHomeHigherClass.php?wall=my')" id="drawer7"><div id="drawer7Icon"><div class="redCircleKudos" id="note1"><?php echo $currentKudosCount; ?></div></div>KUDOS
			</div>
			<div id="drawer6" onClick="showNoticeBoard();"><div id="drawer6Icon"><div class="redCircle" id="note"><?=($blackBoardMsgCounter+$commentCounter+$kudosNotification+$worksheetCounter)?></div></div>NOTIFICATIONS
			</div>
		</div> <?php } else { ?>
        <div id="drawer6" onClick="showNoticeBoard();"><div id="drawer6Icon"><div class="redCircle" id="note"><?=$blackBoardMsgCounter + $commentCounter+$worksheetCounter ?></div></div>NOTIFICATIONS
			</div></div><?php } ?>
                
    	<div id="sparkieInfo" title="Sparkie Information" style="display: none;"  class="hidden">
            <table border="0" bgcolor="white" cellpadding="5" cellspacing="3">
                <tr>
                    <th width="30%" data-i18n="homePage.sparkieInfoContainer.category" align="left">Category</th>
					<th width="40%" data-i18n="homePage.sparkieInfoContainer.condition" align="left">Condition</th>
					<th width="30%" data-i18n="homePage.sparkieInfoContainer.sparkie" align="left">Sparkie</th>
                </tr>
                <tr>
                    <td rowspan="3" data-i18n="homePage.sparkieInfoContainer.Q1">Mindspark Question</td>
                    <td><span data-i18n="homePage.sparkieInfoContainer.S1">3 correct in a row</span><br><span data-i18n="homePage.sparkieInfoContainer.S1a" style="color: rgb(127, 127, 127); font-size: 0.9em;">1st and 2nd topic attempt</span></td>
                    <td align="center"><?=$sparkieImg?></td>
                </tr>
                <tr>
                    <td><span data-i18n="homePage.sparkieInfoContainer.S2">4 correct in a row</span><br><span data-i18n="homePage.sparkieInfoContainer.S2a" style="color: rgb(127, 127, 127); font-size: 0.9em;">3rd, 4th and 5th attempt</span></td>
                    <td align="center"><?=$sparkieImg?></td>
                </tr>
                <tr>
                    <td><span data-i18n="homePage.sparkieInfoContainer.S3">5 correct in a row</span><br><span data-i18n="homePage.sparkieInfoContainer.S3a" style="color: rgb(127, 127, 127); font-size: 0.9em;">6th,7th,8th & 9th attempt</span></td>
                    <td align="center"><?=$sparkieImg?></td>
                </tr>
                <tr>
                    <td rowspan="2" data-i18n="homePage.sparkieInfoContainer.Q2">Challenge Question</td>
                    <td data-i18n="homePage.sparkieInfoContainer.Q3" style="border-top: 1px solid black;">Correct on 1st attempt</td>
                    <td align="center"><?=$sparkieImg.$sparkieImg.$sparkieImg.$sparkieImg.$sparkieImg?></td>
                </tr>
                <tr>
                    <td data-i18n="homePage.sparkieInfoContainer.Q4">Correct on 2nd attempt</td>
                    <td align="center"><?=$sparkieImg.$sparkieImg?></td>
                </tr>
                <tr>
                    <td data-i18n="homePage.sparkieInfoContainer.TC">Topic Completion</td>
                    <td data-i18n="homePage.sparkieInfoContainer.TCS" style="border-top: 1px solid black;">Cleared on 1st attempt</td>
                    <td align="center">15 <?=$sparkieImg?></td>
                </tr>
                <tr>
                    <td rowspan="2" data-i18n="homePage.sparkieInfoContainer.Q5">Timed test</td>
                    <td data-i18n="homePage.sparkieInfoContainer.Q3" style="border-top: 1px solid black;">Correct on 1st attempt</td>
                    <td align="center"><?=$sparkieImg.$sparkieImg.$sparkieImg.$sparkieImg.$sparkieImg?></td>
                </tr>
                <tr>
                    <td data-i18n="homePage.sparkieInfoContainer.Q4">Correct on 2nd attempt</td>
                    <td align="center"><?=$sparkieImg.$sparkieImg.$sparkieImg.$sparkieImg?></td>
                </tr>
                <tr>
                    <td>
	                    <?php if(!(strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"Individual")==0)) { ?>
                    	<span data-i18n="homePage.sparkieInfoContainer.Q6">Revision Session</span><br><br>
	                    <?php } ?>
                    	<span data-i18n="homePage.sparkieInfoContainer.DP">Daily Practice</span>
                	</td>
                    <td data-i18n="homePage.sparkieInfoContainer.Q7" style="border-top: 1px solid black;">More correct questions, more sparkies</td>
                </tr>
                <tr>
                    <td data-i18n="homePage.sparkieInfoContainer.Q8">Games</td>
                    <td data-i18n="homePage.sparkieInfoContainer.Q9" style="border-top: 1px solid black;">More points, more sparkies</td>
                </tr>
				<tr><td></td></tr>
				<!-- <tr>
                    <td colspan="3"><i>You can earn more sparkies by reading explanations and using Mindspark regularly every week!</i></td>
                </tr> -->
            </table>
        </div>

    	<div class="forLowerOnly hidden">
            <div id="sparkieWonDiv">
            	<div id="sparkieWonText"></div>
                <div id="sparkieWon"><?=$sparkieWon?></div>
            </div>
        </div>
    	<div id="leftInfo">
<!--continue bar-->

        	<div id="continueDiv">
            	<div id="continueText" class="textUppercase hidden" data-i18n="homePage.continueLearning"></div>
                <div id="inProgress" class="hidden">
                	<div id="inProgressIcon"></div>
                    <div id="inProgressText" class="textUppercase" data-i18n="homePage.topicInProgress"></div>
                </div>
                <div id="continueButtonDiv" class="handPointer" onClick="continueTopic()">
                    <div id="continueEmotText" class="textUppercase" data-i18n="common.continue"></div>
                    <div id="continueEmot"></div>
                    <div id="lastSessionText" class="hidden" data-i18n="homePage.continueText"></div>
                    <!--<div class="clear"></div>-->
                </div>
                <?php if(strcasecmp($subCategory,"School")==0) { ?>
                <div class="forLowerOnly hidden" id="kudosDiv" >
                <div onClick="resetKudosCounter('src/kudos/kudosHomeLowerClass.php?wall=my')" id="kudosIcon">
                	<div class="redCircleKudos" id="note1">
                		<div style=" line-height:14px; text-align:center;font-size:18px"><?php echo $currentKudosCount; ?></div>
                    </div>
                	<div style="margin-top:28px"><text style="font-size:18px" >KUDOS</text></div>
                </div>
                
                </div> <?php } ?>            
                <div class="clear"></div>
            </div>      

			<div id="noticeBoardMainDiv" class="forHighestOnly" style="display:none;">
			<div id="closeButton" onClick="closeNoticeBoard();"></div>
			<div class="redCircle" id="latest">
            <?php if(strcasecmp($subCategory,"School")==0) { ?>
            <div class="blackBoardMsgCounter"><?=$blackBoardMsgCounter + $kudosNotification + $worksheetCounter?></div>
            <?php } else { ?>
            <div class="blackBoardMsgCounter"><?=$blackBoardMsgCounter ?></div>
            <?php } ?>    
            <div class="boardText">Latest Updates</div></div>
			<div id="noticeBoardDiv">

                <div id="noticeBoardDetails" <?php if(strlen($blackBoardMsg)>20) echo 'style="font-size:14px;text-align:center"'; ?>><?=$blackBoardMsg?><?=$commentMsg?></div>

               

            </div>
			<div class="redCircle" id="recent" style="display:none"><div class="blackBoardMsgCounter"><?=$commentCounter?></div><div class="boardText" style="cursor:pointer;"  onClick="showPrevComments()">Recent Comments</div></div>
			<div id="commentDiv">
                <div id="commentDetails" class="boardText"><a href="javascript:void(0)" onclick='showPrevComments()'>View Comments</a></div>
            </div>
			</div>
            
<!--topic bars-->
            <div id="topicProgressDiv">
                <?php
                $currentActiveTopicsCount = count($lastSessionTopics); 
                for($i=0;$i<4;$i++) {
                	$display =  $i >= $currentActiveTopicsCount ? "visibility:hidden;" : "";
                	?>
                <div class="topicProgressDiv <?php if($i==0) echo 'topicProgressDivFocus'?>" <?php if($theme==1 && $i >= $currentActiveTopicsCount) echo 'style="'.$display.'"';?> >
                	<div class="eiColors" <?php if($i==0) echo 'style="display:block"'?>>
                    	<div class="blue eiFlag"></div>
                        <div class="green eiFlag"></div>
                        <div class="orange eiFlag"></div>
                        <div class="yellow eiFlag"></div>
                    </div>
					<?php if(strcasecmp($objUser->subcategory,"School")==0 && $priorityAssigned) { ?>
							<div class="priorityNo forMidOnly <?php if($i==0) echo 'priorityNoFocus'?>" style="<?= $display; ?>"><?=$i+1?></div>
					<?php } ?>

                	<div class="topicProgressInnerDiv <?php if($priorityAssigned) echo "topicProgressInnerDivPriority"; if($i==0 && $priorityAssigned) echo ' topicProgressInnerDivFocusPriority'; else ' topicProgressInnerDivFocus';?>" <?php if($theme==3) { ?> style="width:<?=($lastSessionTopics[$i][2]*0.75)+25?>%"  onClick="startExam('ttSelection','<?=$lastSessionTopics[$i][0]?>','<?=$lastSessionTopics[$i][5];?>')" <?php } ?>>

						<div id="outerCircle<?=$topicWiseDetails[$i][0]?>" class="outerCircle">
							<div class="progressCircle forHighestOnly circleColor<?=round($lastSessionTopics[$i][2]/10)?> <?php if($isDeactive==1) echo 'deactiveText'?>" style="margin-left:<?=$marginProgressText?>px"><?=round($lastSessionTopics[$i][2])?>%</div>
						</div>
						
						<div class="forHighestOnly">
						<?php if(strcasecmp($objUser->subcategory,"School")==0 && $priorityAssigned) {	?>
								<div class="priorityNo" style="<?= $display; ?>"><?=$i+1?></div>
						<?php } ?>
                    	<div class="topicName <?php if($priorityAssigned) echo "topicNamePriority";?>" id="topicName1">						
						<?php 

						if(strpos($lastSessionTopics[$i][1],'Custom') !== false)
						{
							$tmpArray = explode('Custom',$lastSessionTopics[$i][1]);
						$tmpArray[0] = substr($tmpArray[0],0,-2);
						if(strlen($tmpArray[0])>35) echo substr($tmpArray[0],0,35)."..."; else echo $tmpArray[0];
						}
						else
						{
							if(strlen($lastSessionTopics[$i][1])>35) echo substr($lastSessionTopics[$i][1],0,35)."..."; else echo $lastSessionTopics[$i][1];
						}
						?>
						</div>
						<div class="topicName <?php if($priorityAssigned) echo "topicNamePriority";?>" id="topicName2">
						<?php
						if(strpos($lastSessionTopics[$i][1],'Custom') !== false)
						echo "Custom - ".$tmpArray[1]
						?>
						</div>
						</div>
					
					<?php if($theme==1) { 
						$thisTopicImage=$lastSessionTopics[$i][7]?$lastSessionTopics[$i][7]:"defaultTopicImage.png";
						$thisTopicImage=SPARKIE_IMAGE_SOURCE."/topic_images/".$thisTopicImage;
						?>
						<div class="priorityNo forLowerOnly" style="<?= $display; ?>;background-image:url(<?= $thisTopicImage; ?>)">
						</div>
					<?php	} ?>
						<div class="hidden topicInfoForLower">
							<?php $shortTopicName = (strlen($lastSessionTopics[$i][1])>47)?substr($lastSessionTopics[$i][1],0,47)."...":$lastSessionTopics[$i][1]; ?>
						    <div class="topicName <?php if($lastSessionTopics[$i][2]==0) echo 'topicNotStarted';?>" title="<?=$lastSessionTopics[$i][1]?>"><?=$shortTopicName?></div>
					<?php if($theme==1) { ?>
							<div class="topicCurrentStatusInfo">
								<?php if($lastSessionTopics[$i][2]==0) {?>
									<div class="topicStartMsg">Let's get started</div>
								<?php } else if($lastSessionTopics[$i][2]==100) {?>
									<div class="topicCompletedTrophy"></div>
									<div class="topicCompletedMsg"><b>Cheers!</b><br>Topic completed</div>
								<?php } else {?>
									<div class="newProgressForLower" title="<?php echo round($lastSessionTopics[$i][2]); ?>%">
										<span class="newProgressBar" style="width:<?php echo floor($lastSessionTopics[$i][2]/20)*25; ?>%;"></span>
										<span class="newProgressBubble p20 <?php if($lastSessionTopics[$i][2]>20) echo 'filledBubble'; elseif($lastSessionTopics[$i][2]>0) echo 'workingBubble'; ?>"></span>
										<span class="newProgressBubble p40 <?php if($lastSessionTopics[$i][2]>40) echo 'filledBubble'; elseif($lastSessionTopics[$i][2]>20) echo 'workingBubble';  ?>"></span>
										<span class="newProgressBubble p60 <?php if($lastSessionTopics[$i][2]>60) echo 'filledBubble'; elseif($lastSessionTopics[$i][2]>40) echo 'workingBubble'; ?>"></span>
										<span class="newProgressBubble p80 <?php if($lastSessionTopics[$i][2]>80) echo 'filledBubble'; elseif($lastSessionTopics[$i][2]>60) echo 'workingBubble'; ?>"></span>
										<span class="newProgressBubble p100 <?php if($lastSessionTopics[$i][2]==100) echo 'filledBubble'; elseif($lastSessionTopics[$i][2]>80) echo 'workingBubble'; ?>"></span>
									</div>
								<?php }?>
							</div>
					<?php } ?>								    
					<?php if(strcasecmp($objUser->subcategory,"School")==0) { ?>
							<div class="clear forLowerOnly"></div>
					<?php } ?>
						</div>
						   	
                        <div class="progressBar hidden" id="progressBarTopic<?=$i+1?>" onClick="startExam('ttSelection','<?=$lastSessionTopics[$i][0]?>','<?=$lastSessionTopics[$i][5];?>')">
                        	<div class="progressBarText" <?php if($lastSessionTopics[$i][2]!=100) { ?> data-i18n="homePage.completeIt"<?php } ?>>100%</div>
                            <div class="progressBarEmot"></div>
                            <div class="clear"></div>
                        </div>
                        <div class="progressRun hidden" id="progressRun<?=$i+1?>"></div>
                        <div class="progressPercent hidden" id="progressTextTopic<?=$i+1?>"><?=round($lastSessionTopics[$i][2])?>%</div>
                        <div class="clear"></div>
                        <div class="progressContainer hidden">
                            <div class="topicProgressGrad" id="progressTopic<?=$i+1?>"></div>
                            <div class="topicProgressCurrent"></div>
                            <div class="topicProgressRemain" id="incompleteTopic<?=$i+1?>"></div>
                            <div class="topicProgressEnd"></div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
				<div class="clear"></div>
                <?php } ?>

                <div id="viewAllDivLowerClass" class="textUppercase">
					<?php if($homeworkLink!="" && $dataSynchronised === true) { ?>
						<a href="<?=$homeworkLink?>"  onclick="javascript:setTryingToUnload();" class="removeDecoration">
							<div class="buttonView"><div id="ncertIcon"></div>NCERT</div>
						</a>
						<?php 
							if($childClass>3 && (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"School")==0)) { ?>
							<a href="worksheetSelection.php?instruction=yes"  onclick="javascript:setTryingToUnload();" class="removeDecoration">
								<div class="buttonView1" style="margin-left:2%;width: 160px;position:relative;" ><?php if ($worksheetCounter>0) {?><div class="redCircleWorksheet"><?=$worksheetCounter?></div><?php } ?><div id="worksheetIcon"></div>Worksheets</div>
							</a>
						<?php } ?>
						<div class="buttonView1" style="margin-left:2%" onClick="viewAll();"><div id="viewIcon"></div>DASHBOARD</div>
					<?php } ?>
					
					<?php 
					if($homeworkLink=="") {
						if($childClass>3 && (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"School")==0)) { ?>
								<a href="worksheetSelection.php?instruction=yes"  onclick="javascript:setTryingToUnload();" class="removeDecoration">
									<div class="buttonView" style="width:160px;position:relative;"><?php if ($worksheetCounter>0) {?><div class="redCircleWorksheet"><?=$worksheetCounter?></div><?php } ?><div id="worksheetIcon"></div>Worksheets</div>
								</a>
								<div class="buttonView1"  style="margin-left:2%" onClick="viewAll();"><div id="viewIcon"></div>DASHBOARD</div>
							<?php } 
							else{
							?><div class="buttonView1" onClick="viewAll();"><div id="viewIcon"></div>DASHBOARD</div><?php
							} 
						} ?>
				</div>
                <div id="viewActivitiesDivLowerClass" class="textUppercase forLowerOnly">
                	<a href="activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
                		<div class="buttonView1"  data-i18n="homePage.activity">ACTIVITIES</div>
                	</a>
				</div>
				<!-- worksheet button added for class 3 -->
				<?php if($childClass==3 && (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"School")==0)) { ?>
				<div id="viewWorksheetDivLowerClass" class="textUppercase">
					<a href="worksheetSelection.php?instruction=yes" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
		                <div id="worksheetDiv" class="handPointer">
		                    <?php if ($worksheetCounter>0) {?><div class="redCircleWorksheet"><?=$worksheetCounter?></div><?php } ?>				                    		                    
		                        <div id="worksheetText" class="textUppercase" >Worksheets</div>		
		                    <div class="clear"></div>
		                </div>
					</a>
				</div>
				<?php } ?>
            </div>
            <div id="leftLowerInfo" class="hidden">
            	<div id="viewAllDiv" class="handPointer" onClick="viewAll()" <?php if ($homeworkLink!="" && $dataSynchronised === true) echo 'style="width:28.4%"';?>>
                    <div id="viewAllEmote"></div>
                    <div id="viewAllRight">
                        <div id="viewAllText" class="textUppercase" data-i18n="homePage.viewAll"></div>
                        <div id="viewAllUl"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php if($homeworkLink!="" && $dataSynchronised === true) { ?>
                <a href="<?=$homeworkLink?>" onClick="javascript:setTryingToUnload();">
                <div id="ncertDiv" class="handPointer">
                    <div id="ncertEmote"></div>
                    <div id="ncertRight">
                        <div id="ncertText" class="textUppercase" data-i18n="homePage.ncert"></div>
                        <div id="ncertUl"></div>
                    </div>
                    <div class="clear"></div>
                </div>
				</a>
                <?php } ?>
                <?php if($childClass>3 && $dataSynchronised === true && (strcasecmp($objUser->category,"STUDENT")==0 && strcasecmp($objUser->subcategory,"School")==0)) { /* data-i18n="homePage.ncert" */ ?>
                <a href="worksheetSelection.php?instruction=yes" onClick="javascript:setTryingToUnload();">
                <div id="worksheetDiv" class="handPointer" <?php if (!($homeworkLink!="" && $dataSynchronised === true)) echo 'style="margin-left:15%"';?>>
                    <?php if ($worksheetCounter>0) {?><div class="redCircleWorksheet"><?=$worksheetCounter?></div><?php } ?>
                    <div id="worksheetEmote"></div>
                    <div id="worksheetRight">
                        <div id="worksheetText" class="textUppercase" >Worksheets</div>
                        <div id="worksheetUl"></div>
                    </div>
                    <div class="clear"></div>
                </div>
				</a>
                <?php } ?>

				<?php //if($linkSummer!="") { ?>
                <!--<a href="<?=$linkSummer?>" onClick="javascript:setTryingToUnload();">
                <div id="summerProgramDiv" class="handPointer">
                    <div class="clear"></div>
                </div>
				</a>-->
                <?php //} ?>
                <a href="activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
                    <div id="activityDivLowerClass">
                        <div id="activityLowerClassEmote">
                            <div id="activityLowerClassText" class="textUppercase" data-i18n="homePage.activity"></div>
                        </div>
                    </div>
                </a>
                <div class="clear"></div>
            </div>

        </div>

        <div id="midInfo" class="hidden">
        	<div id="sparkieInfoDiv">
				<?php if($_SESSION['rewardSystem']==1){
				?>
				<div id="badges">
					<?php if(count($recentBadge)==1){
					?>
					<div class="circleContainer" id="circleContainer1" style="margin-left: 66px;margin-top: -13px;"><div id="badge1" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[0]?>.png') no-repeat 0 0;"></div></div>
					<?php } ?>
					<?php if(count($recentBadge)==2){
					?>
					<div class="circleContainer" id="circleContainer1" style="margin-left: 30px;margin-top: -11px;"><div id="badge1" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[0]?>.png') no-repeat 0 0;"></div></div>
					<div class="circleContainer" id="circleContainer2" style="margin-left: 97px;margin-top: -11px;"><div id="badge2" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[1]?>.png') no-repeat 0 0;"></div></div>
					<?php } ?>
					<?php if(count($recentBadge)==3){
					?>
					<div class="circleContainer" id="circleContainer1"><div id="badge1" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[0]?>.png') no-repeat 0 0;"></div></div>
					<div class="circleContainer" id="circleContainer2"><div id="badge2" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[1]?>.png') no-repeat 0 0;"></div></div>
					<div class="circleContainer" id="circleContainer3"><div id="badge3" style="background: url('assets/rewards/badgesMyHistorySection/<?=$recentBadge[2]?>.png') no-repeat 0 0;"></div></div>
					<?php } ?>
				</div>
				<div id="rewardsText">Click here for <br/></div>
				<!--<div type="button" id="sparkieInfoDivText" class="buttonDashboard">Sparkie Information</div>-->
				<div id="sparkieInfoDivText" type="button" class="buttonDashboard">Sparkie Information</div>
				<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div type="button" class="buttonDashboard">Rewards Central</div></a>
				<?php } else{
				?>
				<div id="sparkieInfoDivText" class="textUppercase" data-i18n="homePage.sparkieInfo"></div>
				<?php } ?>
            </div>
            <div id="sparkieBulbDiv">
            	<div id="sparkieBulbDivText"><div class="<?=$sparkieImage?>"></div><?=$sparkieWon?>
				<?php if($_SESSION['rewardSystem']==1){ ?><div style="font-size: 0.25em;">(this year)</div><?php } ?></div>
            </div>
            <a href="activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
                <div id="activityDiv" class="handPointer">
                    <div id="activityEmote"></div>
                        <div id="activityRight">
                            <div id="activityText" class="textUppercase" data-i18n="homePage.activity"></div>
                            <div id="activityUl"></div>
                        </div>
                    <div class="clear"></div>
                </div>
            </a>
        </div>

        <div id="rightInfo" class="hidden">
        	<div id="studentInfo"> 
            	<div id="studentInfoUpper" align="center" style="text-align:center">
                	<div id="childNameDivCustomForHome" class="Name"><?=$Name?></div>
                    <div class="classCustomForHome" id="childClassMid"><span class="textUppercase" data-i18n="common.class"></span><span>  <?=$childClass.$childSection?></span></div>
                    <div class="clear"></div>
                </div>
            	<div id="studentInfoLower">
                	<a href='myDetailsPage.php' onClick="javascript:setTryingToUnload();" class="removeDecoration">
                	<div id="myDetails">
                    	<div id="myDetailsImote"></div>
                        <div id="myDetailsText" class="textUppercase" data-i18n="homePage.myDetails"></div>
                        <div id="myDetailsUl"></div>
                    </div>
                    </a>
               <?php if(strcasecmp($subCategory,"School")==0) { ?>     
			   <div onClick="resetKudosCounter('src/kudos/kudosHomeMidClass.php?wall=my')" id="kudos">
                        <div id="kudosImote">
                        	<div class="redCircleKudos" id="note1">
                				<div style=" line-height:7px; text-align:center;font-size:14px"><?php echo $currentKudosCount; ?></div>
                    		</div>
                        </div>
                        <div id="kudosText" class="textUppercase" text-align="centre" data-i18n="kudos"></div>
                        <div id="kudosUl"></div>
               </div><?php } ?>
                    <div class="clear"></div>
                </div>
            </div>

            <div id="commentDiv">
            	<div id="commentEmote"></div>
                <div id="commentRight">
                	<div id="commentText" class="textUppercase handPointer" data-i18n="homePage.viewComments" onClick="showPrevComments()"></div>
                    <div id="commentUl"></div>
                </div>
                <div class="clear"></div>
            </div>

            <div id="noticeBoardDiv">
            	<div id="noticeBoardHeading" class="textUppercase" data-i18n="homePage.noticeBoard"></div>
                <div id="noticeBoardDetails" <?php if(strlen($blackBoardMsg)>100){if($theme!=1) echo 'style="font-size:14px;padding-left: 5px;padding-right: 5px;padding-bottom: 5px;height: 120px;overflow-y: auto;"'; else echo 'style="font-size:14px;padding-left: 5px;padding-right: 5px;height: 170px;overflow-y: auto;"';} ?>><?=$blackBoardMsg?></div>
				<div class="noticeBoardImage"></div>
            </div>
			<?php if($theme==2) { ?>
			<div id="feedBox">
				<div id="feedBoxHeading" class="textUppercase"><span data-i18n="homePage.feedBox"></span></div>
				<div id="feeds"></div>
				<div id="feedExtnd"></div>
			</div>
			<?php } ?>
            <div id="lowerClassIconDiv">
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>

    <input type="hidden" name="mode" id="mode" value="nextAction">
    <input type="hidden" name="ttCode" id="ttCode">
	<input type="hidden" name="timedTestCode" id="timedTestCode">
	<input type="hidden" name="pendingTopicTimedTest" id="pendingTopicTimedTest">
</form>
<form name="frmOldVersion" action="../home.php" id="frmOldVersion" method="POST">
    <input type="hidden" name="oldVersion" value="4">
</form>
<div style="display:none">
        <div id="openHelp">
			<h2 align="center">Quick Tutorial</h2>
            <iframe id="iframeHelp" width="960px" height="440px" scrolling="no"></iframe>
        </div>
    </div>
<div style="display:none">
        <div id="freeTrialMessage">
			<h2 align="center">Welcome to Mindspark!</h2>
			<div style="width: 100%;height:2px;"></div>
            <p>We hope you have exciting sessions ahead. You can keep practicing for free and build on a strong Maths foundation during the free trial. </p>
			<p>Students will benefit most if they practice on Mindspark for 30 minutes a day for minimum 4 days a week. The minimum recommended practice is 30 minutes each day for three days</p>
			
			<p>Before you start working on Mindspark, we want you to go through a quick tutorial session which will help you to understand the various features of Mindspark and will help you to use it effectively.</p>
			<p>Quick tutorial session. <a href="javascript:void(0)" style="text-decoration:underline;color:blue;" onClick="openHelp()">Click here</a> to start</p>
			<p>For any query, do write to us at mindspark@ei-india.com</p>
			
			<p>Warm regards,<br/>Team Mindspark.</p>
        </div>
    </div>
	<!--<div style="display:none">
        <div id="summerFeedback" align="center">
			<?php
				//$randNum = rand(0,4);				
			?>
			<h2 align="center"><?php //echo $summerFeedbackTitle[$randNum] ?></h2>
			<textarea name="taSummerFeedback" id="taSummerFeedback" rows="4" style="width: 80%; vertical-align: text-top;" placeholder="Share your feedback."></textarea><br>
			<div class="buttonFBsubmit" id="btnSubmit" onClick="submitFeedback()">Submit</div>
			<div class="buttonFBsubmit" id="btnCancel" onClick="cancelFeedback()">No thanks</div>
			<div class="buttonFBsubmit" id="btnAskMeLater" onClick="askMeLater()">Ask me later</div>
        </div>
    </div>-->
	<?php if($daTestPrompt==0 && $showAqadPrompt==1 && $aqadUsage==1 && $childClass!=1 && $childClass!=2 && $childClass!=10){ ?>
		<div class="aqadPrompt">
			<!--<div class="closeAqad" onclick="closePromptAQAD();">x</div>-->
			<div class="promptHeading">ASSET Question-A-Day (AQAD)</div>

			<p style="margin-left:10px;">
				Check this out for a daily question on English, Science, Maths or Social studies from ASSET.<br/><br/>
				To start, click on AQAD. 
			</p>
			<div class="aqadOK" onClick="closePromptAQAD();">Okay, got it</div>
			
			<p style="margin-left:10px;color: #ACACAC">
				Note: To school students, AQAD is only available during home usage.
			</p>
		</div>
	<?php } ?>
	<?php if($daTestPrompt==0 && $aqadUsage == 3 && $childClass!=1 && $childClass!=2 && $childClass!=10){ ?>
	<div id="schoolUsersPrompt" style='display:none;'>
		<div class="aqadPrompt" style='height:120px;right:30.5%'>
			<!--<div class="closeAqad" onclick="closePromptAQAD();">x</div>-->
			<div class="promptHeading">ASSET Question-A-Day (AQAD)</div>
			<p style="margin-left:10px;">
				To answer ASSET Question-A-Day, login after 4 pm IST.<br/><br/> 
			</p>
			<div class="aqadOK" onClick="closePromptAQAD();">Okay, got it</div>
		</div>
		</div>
	<?php } ?>
	<?php if($daTestPrompt==1 && $daTestStatus!="completed" && $daTestStatus!=3 && $daysLeftForDa>=0){ 
	?>
		<div class="daTestPrompt" <?php if(isset($_SESSION['showDaPrompt']) && $_SESSION['showDaPrompt']==0) echo 'style="display:none"'; ?>>
			<div class="promptHeading">Super Test</div>
			<p style="margin-left:10px;">
				<?php if($daysLeftForDa>0) { ?>
					Mindspark <b><?=$daTopicName?></b> Super Test<br><?=$daysLeftForDa?> day(s) to GO!
				<?php } else { ?>
					You have a Super Test on <b><?=$daTopicName?></b> today.
				<?php } ?>
			</p>
			<p style="margin-left:10px;color: #ACACAC">
				Duration: 30 minutes
			</p>
			<div class="aqadOK" onClick="<?php if($daysLeftForDa>0) { ?>closeDaTestPrompt();<?php } else { ?>startDaTest()<?php } ?>"><?php if($daysLeftForDa>0) { ?>Okay, got it<?php } else { ?>Take the test<?php } ?></div>
		</div>
		<form name="frmDaTest" action="question.php" id="frmDaTest" method="POST">
			<input type="hidden" name="mode" value="firstQuestion">
			<input type="hidden" name="daTestCode" value="<?=$_SESSION['daPaperCode'];?>">
			<input type="hidden" name="quesCategory" value="daTest">
		</form>
	<?php } ?>

<div id="bgclocknoshade" class="bgclockshade" style="font-size: 14px; RIGHT: 10px;font-weight:bold;<?php if($theme==3) echo 'color:#E75903;'; else if($theme==2) echo 'color:#fff;';else echo 'color:#000;';?>; position: absolute;<?php if($theme==1) echo 'top:16px;'; else echo 'top: 30px;';?>"></div>
<div class="bgclockshade" style="font-size: 14px; RIGHT: 3px;font-weight:bold; color: #fff; position: absolute;<?php if($theme==1) echo 'top:16px;'; else echo 'top: 30px;';?>" onClick="showPrompt();"><sup title="Here's how long you've been Mindspark-ing!" style='cursor:pointer;font-size:10px;color:blue;'>?</sup></div>
<script>
	<?php
		if(date("D") == "Sun" && $theme != 3)
		{
			if( $iPad ==true)
				echo '$(".daTestPrompt").css("right","38%");';
			else
				echo '$(".daTestPrompt").css("right","33%");';	
		}
		else if(date("D") == "Sat" && $childClass == 4)
		{
			if( $iPad ==true)
				echo '$(".daTestPrompt").css("right","40%");';
			else
				echo '$(".daTestPrompt").css("right","33%");';
		}
	?>
	clockon();
<?php if($theme==2)	{ ?>
	<?php if($objUser->subcategory=="Individual" || $objUser->category!="STUDENT"){?>feedPuller.startFeeds(userID,schoolCode,childClass,0,<?php echo '"'.ENRICHMENT_MODULE_FOLDER.'"' ?>);
	<?php } else {?>feedPuller.startFeeds(userID,schoolCode,childClass,1,<?php echo '"'.ENRICHMENT_MODULE_FOLDER.'"' ?>);	<?php } ?>
<?php } ?>	
</script>
<form name="startAqad" action="<?=AQAD_URL?>mindsparkHandshake.php" id="startAqad" method="POST" target='_blank'>
	<input type="hidden" name="aqadSession" id="aqadSession" value="">
</form>
<?php include("commonAQAD.php"); ?>
<?php include("footer.php");?>
