<?php
include("check1.php");
include("constants.php");
include_once("functions/functions.php");
include("classes/clsUser.php");
include_once("classes/clsTeacherTopic.php");
if(!isset($_SESSION['userID']))
{
	header( "Location: error.php");
}

$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
$userType = isset($_REQUEST['userType'])?$_REQUEST['userType']:"";
if($userType=="msAsStudent") {
	$_SESSION["userType"]="msAsStudent";
}
$homeLinkValidate = $_SERVER['HTTP_REFERER'];
if(strpos($homeLinkValidate,"activity.php")!==false || strpos($homeLinkValidate,"topicPage.php")!==false){
	$homeLinkActive=1;
}
else{
	$homeLinkActive=0;
}
$gameCode = "";
if(isset($_REQUEST['gameCode']))
{
	$gameCode = $_REQUEST['gameCode'];
	if($gameCode!="")
	{
		$gameCode = "?".$gameCode;
	}
}
if(isset($_REQUEST['gameID']))
	$gameID = $_REQUEST['gameID'];
else if(isset($_SESSION['gameID']))
	$gameID = $_SESSION['gameID'];
else
{
	echo "Game not selected!";
	exit;
}
if($gameCode=="")
{
	if($gameID == 25)
		$gameCode = "?gameCode=DA";
	if($gameID == 22)
		$gameCode = "?gameCode=FPH";
}
if($gameID == 56)
	header("Location: FCT.php");
if(isset($_SESSION['childName']))
	$Name = explode(" ",$_SESSION['childName']);
else
	$Name = "";
$childClass  = $_SESSION['childClass'];
$_SESSION['game'] = $gameID;
$userID = isset($_SESSION['userID'])?$_SESSION['userID']:"";
$sessionID = isset($_SESSION['sessionID'])?$_SESSION['sessionID']:"";

$gameMode = isset($_POST['gameMode'])?$_POST['gameMode']:"";
$returnToPage = isset($_POST['returnTo'])?$_POST['returnTo']:"";
if($gameMode=="")
	$gameMode = isset($_POST['mode'])?$_POST['mode']:"";
$gameDetails = getFlashFileDetails($gameID);
$noOfLevels	=	$gameDetails["noOfLevels"];
$activityType	=	$gameDetails["type"];
$levelWiseMaxScores	=	$gameDetails["levelWiseMaxScores"];
$activityFormat	=	"old";
$gameAttempt_srno = "";
$Name = $Name[0];
//echo $_SESSION['subModule'];die;
if($userID!="" && $userType!="msAsStudent" && $noOfLevels==0)
{
	$gameAttempt_srno = insertAttemptDetails($userID, $gameID, $sessionID);
    $activityFormat	=	"old";
    $skipAllowed=getSkipValidity($userID,$gameID,$activityFormat,$activityType,$gameAttempt_srno,$userType);
}
else if($userID!="" && $userType!="msAsStudent" && $noOfLevels>0)
{
	$activityFormat	=	"new";
    if($gameDetails["type"]=="regular" || $gameDetails["type"]=="comprehensive")
	{
		$previousLevelLock	=	1;
		$lastLevelClearedSrno	=	getLastAttemptData($gameID,$userID,$gameDetails["noOfLevels"],"regular");
		$lastLevelClearedSrnoArr	=	explode("~",$lastLevelClearedSrno);
		$lastLevelCleared	=	$lastLevelClearedSrnoArr[0];
		if($lastLevelCleared==-1)
		{
            $temp='old';
			$gameAttempt_srno = insertAttemptDetails($userID, $gameID, $sessionID);
			insertLevelDetails($gameAttempt_srno,$noOfLevels,$activityType,$_SESSION["userID"]);
			$lastLevelCleared=0;
		}
		else
		{
            $temp='new';
			$gameAttempt_srno	=	$lastLevelClearedSrnoArr[1];
            $query  = "UPDATE adepts_userGameDetails SET attemptCnt=attemptCnt+1 where gameid=".$gameID." and userid=".$userID." and srno=".$gameAttempt_srno;
            $result = mysql_query($query) or die(mysql_error().$query);
		}
        if($gameDetails['type']=='regular'){
            
            $skipAllowed=getSkipValidity($userID,$gameID,$activityFormat,$activityType,$gameAttempt_srno,$userType);
        }
	}
	else
	{
		$previousLevelLock	=	0;
		$lastLevelCleared	=	getLastAttemptData($gameID,$userID,$noOfLevels,"optional");
		$gameAttempt_srno = insertAttemptDetails($userID, $gameID, $sessionID);
		insertLevelDetails($gameAttempt_srno,$noOfLevels, $activityType,$_SESSION["userID"]);
	}
}
else if($noOfLevels>0)
{
	$activityFormat	=	"new";
	$lastLevelCleared=0;
	$previousLevelLock	=	1;
}
if($noOfLevels==1)
{
	$lastLevelCleared=0;
	$previousLevelLock=1;
}

$inputParameter="";
if($activityFormat=="new")
{
	if($gameDetails["passingParam"]!="")
	{
		$inputParameter	=	"?".$gameDetails["passingParam"]."&";
		$inputParameter	.=	"noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
	else
	{
		$inputParameter	=	"?noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
}
else
{
	if($gameDetails["passingParam"]!="")
		$inputParameter	=	"?".$gameDetails["passingParam"];
}
if(isset($_REQUEST["viewmode"]) && $_REQUEST["viewmode"]=="html5")
{
	$gameDetails["version"]="html5";
	if($activityType=="introduction")
	{
		$gameDetails["flashFile"]	=	"introduction/".$gameID."/src/index.html";
	}
	else if($activityType=="enrichment")
	{
		$gameDetails["flashFile"]	=	"enrichments/".$gameID."/src/index.html";
	}
	else
	{
		$gameDetails["flashFile"]	=	"games/".$gameID."/src/index.html";
	}
}
if($gameDetails["version"]=="html5")
{
	if($activityType=='multiplayer')
		$inputParameter .= "&multiplayerDetails=".multiplayerEncrypt( "gameID=".$gameID.";userID=".$userID.";childName=".$_SESSION['childName'].";profilePicture=".( isset($_SESSION['profilePicture'])?$_SESSION['profilePicture']:'none' ).";childClass=".$_SESSION['childClass'].";schoolCode=".( ($_SESSION['admin']=='STUDENT' && $_SESSION['subcategory']=='Individual')?'retail':$_SESSION['schoolCode'] ) );
	if($userType=="msAsStudent")
		$flashFile = ENRICHMENT_MODULE_FOLDER_DEV."/html5/".$gameDetails["flashFile"].$inputParameter;
	else
		$flashFile = ENRICHMENT_MODULE_FOLDER."/html5/".$gameDetails["flashFile"].$inputParameter;
}
else if($gameID!=47)
    $flashFile = ENRICHMENT_MODULE_FOLDER."/".$gameDetails["flashFile"];
else
    $flashFile = "Enrichment_Modules/".$gameDetails["flashFile"];

/*$imageSizeArray = @getimagesize(BASE_FOLDER."/".$flashFile);
$width = $imageSizeArray[0];
$height = $imageSizeArray[1];*/
$width = "";
$height = "";
if($width=="")
{
    if($gameID==34)
        $width=1024; else $width = "800"; }
if($height=="")
{if($gameID==34) $height="768";else  $height = "600";}

$allowed = 1;

$today = date("Y-m-d");
$arrActivities = array($gameID=>$gameDetails["desc"]);
$timeSpentOnActivitiesToday = 0;

if($userID!="" && $userType!="msAsStudent" && $_SESSION['admin']!="GUEST" && $_SESSION['schoolCode']!=2387554)
{
    $timeSpentOnActivitiesToday = getTimeSpentOnActivities($userID,$arrActivities,$today,$today);
}

if($gameDetails["timeLimit"]!="" && $timeSpentOnActivitiesToday[$gameID] > $gameDetails["timeLimit"])
    $allowed = 0;
//If user Comes from DCT flow it should always allow...
if($gameMode == "DCTstage" || $gameMode=="afterCluster" || $gameMode=="researchModule" || $userType=="msAsStudent" || $gameMode=="comprehensive" || $gameMode=="groupInstruction" || $gameMode=="choiceScreen")
	$allowed = 1;
	
$gameAttempt = isGameAttempted($gameID,$userID);
?>

<?php include("header.php"); ?>

<title>Activities Detail Page</title>
<link href="css/question/common.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type='text/javascript' src='libs/combined.js'></script>
<!--<script src="libs/closeDetection.js"></script>-->
<?php

if (isset($_REQUEST['theme'])) 
	$theme	=	$_REQUEST['theme'];
if($theme==1) { ?>
<link href="css/activitiesDetailPage/lowerClass.css" rel="stylesheet" type="text/css">
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<script>
	function load(){
		init();
		jQuery('#clickText').html("Activities");
		jQuery('#clickText').css("color","blue");
		jQuery('#clickText').css("font-size","20px");
	}
</script>
<?php } else if($theme==2){ ?>
<link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
<link href="css/activitiesDetailPage/midClass.css" rel="stylesheet" type="text/css">
<script>
    
	var infoClick=0;
	function load(){
		init();
		jQuery("body").animate({ scrollTop: jQuery(document).height() }, 1000);
	}
	function hideBar(){
			if (infoClick==0){
				jQuery('#info_bar').animate({'height':'55px'},600);
				var a= window.innerHeight -130 -27;
				jQuery('#activitiesContainer').animate({'height':a},600);
				jQuery(".icon_text1").animate({'margin-top':'12px','margin-left':'55px'},600);
				jQuery('#topic').css("border-left-style","none");
				infoClick=1;
			}
			else if(infoClick==1){
				jQuery('#info_bar').animate({'height':'130px'},600);
				var a= window.innerHeight -210 -17;
				jQuery('#activitiesContainer').animate({'height':a},600);
				jQuery(".icon_text1").animate({'margin-top':'45px','margin-left':'0px'},600);
				jQuery('#topic').css("border-left-style","solid");
				infoClick=0;
			}
		}
</script>
<?php } else if($theme==3) { ?>
	    <link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
		<link href="css/activitiesDetailPage/higherClass.css" rel="stylesheet" type="text/css">
		<script>
	function load(){
		init();
		var a= window.innerHeight + (50);
			$('#activitiesContainer').css({"height":a+"px"});
			$('#menuBar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
	}
	function closeFeedback(){
		jQuery("#gameCommentForm").hide();
		$('#activitiesContainer').css("height","630px");
		$('#sideBar').css("height","630px");
		$('#main_bar').css("height","630px");
	}
	function hideBar(){
		
	}

</script>
<?php } ?>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/prompt.js"></script>-->

<script>
<?php if(isset($_SESSION["windowName"]) && $_SESSION["windowName"]!="") { ?>
var windowName="";
<?php } ?>
var langType	=	'<?=$language?>';
var redirect = 1;
jQuery(document).ready(function(e) {
	
    jQuery("#feedbackYes").click(function(){
		jQuery("#gameCommentForm").show();
		jQuery("#askFeedbackDiv").hide();
		var ua = navigator.userAgent;
		if( ua.indexOf("Android") >= 0 )
		{
			var a= window.innerHeight +385;
		}else{
			var a= window.innerHeight +265;
		}
		/*jQuery('#activitiesContainer').css("height",a);*/
		$('#sideBar').css("height",a);
		$('#main_bar').css("height",a);
		if(jQuery("#gameCommentForm").is(":visible"))
		{
			if(jQuery("#gameFeedbackText1"));
				jQuery("#gameFeedbackText1").focus();
		}
		jQuery("html, body").animate({scrollTop:jQuery(".feedBackForm").offset().top},"slow");
		
		<?php 
		$_SESSION['game'] = false;
		if($gameMode=='groupInstruction' && $activityFormat=='old') { ?>
		$('#quitBtn').css('display','none');
		<?php } ?>
	});
	jQuery("#feedbackNo").click(function(){
		<?php $_SESSION['game'] = false; ?>
		jQuery("#askFeedbackDiv").hide();
		feedbackBool = true;
		finishActivity();
	});
	jQuery(".radioEnabled td").not(".rowTH,.colTH").click(function(e) {
		if(jQuery(this).children("input[type=checkbox]"))
		{
			var currentCheckID = jQuery(this).children("input[type=checkbox]").attr("id");
			jQuery(this).parent().children("td").removeClass("selectedLableTD");
			jQuery(this).parent().children("td").children("input[type=checkbox]").not("#"+currentCheckID).attr("checked",false);
			if(jQuery(this).children("input[type=checkbox]").is(":checked"))
				jQuery(this).children("input[type=checkbox]").attr("checked",false);
			else
				jQuery(this).children("input[type=checkbox]").attr("checked",true);
			if(jQuery(this).children("input[type=checkbox]").is(":checked"))
				jQuery(this).addClass("selectedLableTD");
			if(jQuery("#gameFeedbackText1"));
				jQuery("#gameFeedbackText1").focus();
		}
    });
	jQuery("#gameFeedbackButton").click(function(e){
		e.preventDefault();
		var dataArray = "";
		if(jQuery(this).hasClass("type1"))
		{
			var formType	=	"type1";
			if(!jQuery("input[type=checkbox]").is(":checked") && jQuery("#gameFeedbackText1").val() == "")
			{
				jQuery("#gameFeedbackText1").focus();
				alert("Please add atleast one comment !");
				return false;
			}
			
			var val104 = (jQuery("input[name=funRadio]:checked").val())?jQuery("input[name=funRadio]:checked").val():"";
			dataArray = {'104': val104, '105': jQuery("#gameFeedbackText1").val()};
		}
		else if(jQuery(this).hasClass("type2"))
		{
			var formType	=	"type2";
			if(!jQuery("input[type=checkbox]").is(":checked") && jQuery("#gameFeedbackText2").val() == "")
			{
				alert("Please add atleast one comment !");
				jQuery("#gameFeedbackText2").focus();
				return false;
			}
			/*var val106 = (jQuery("input[name=gameConcept]:checked").val())?jQuery("input[name=gameConcept]:checked").val():"";*/
			var val107 = (jQuery("input[name=designSound]:checked").val())?jQuery("input[name=designSound]:checked").val():"";
			var val108 = (jQuery("input[name=interesting]:checked").val())?jQuery("input[name=interesting]:checked").val():"";
			var val109 = (jQuery("input[name=maths]:checked").val())?jQuery("input[name=maths]:checked").val():"";
			dataArray = {'107': val107, '108': val108, '109': val109, '110':jQuery("#gameFeedbackText2").val() };
		}
		else
		{
			var formType	=	"type3";
			var radio_buttons = $("input[name='activityHelp']");
			if (!$("input[name='funRadio1']:checked").val() && !$("input[name='funRadio2']:checked").val() && radio_buttons.filter(':checked').length == 0 && $('#gameFeedbackText1').val() == "") 
			{
				alert("You need to answer atleast one question");
				jQuery("#gameFeedbackText1").focus();
				return false;
				
			}
			var val160 = (jQuery("input[name=funRadio1]:checked").val())?jQuery("input[name=funRadio1]:checked").val():"";
			var val161 = (jQuery(".activityHelp:checked").val())?$('.activityHelp:checked').map(function() {return this.value;}).get().join(','):"";
			var val162 = (jQuery("input[name=funRadio2]:checked").val())?jQuery("input[name=funRadio2]:checked").val():"";
			dataArray = {'160':val160, '161':val161,'162':val162,'163': jQuery("#gameFeedbackText1").val()};
		}
		
		
		var formData =
		{
			gameID: <?=$gameID?>,
			formType: formType,
			mode: "gameFeedBack",
			data: JSON.stringify(dataArray)
		}
		<?php if($gameMode!='groupInstruction' || ($gameMode=='groupInstruction' && $activityFormat=='new')) { ?>
		jQuery("#gameCommentForm").hide();
		<?php } ?>
		jQuery.post("controller.php",formData,function(data){
			if(jQuery.trim(data) == "success")
			{
				<?php if($gameMode!='groupInstruction' || ($gameMode=='groupInstruction' && $activityFormat=='new')) { ?>
					
					//jQuery("#gameCommentForm").html('<h4>Comment submitted !</h4>');
					jQuery("#gameCommentForm").hide();
					feedbackBool = true;
					/*if(formType == "type1")
						jQuery("#gameCommentForm").css("padding-top","120px");*/
					finishActivity();
					jQuery("#flash").show();
					
				<?php } else { ?>
					setTryingToUnload();
					document.getElementById('frmContinueQues').submit();

				<?php } ?>
				
			}
			<?php if($gameMode!='groupInstruction' || ($gameMode=='groupInstruction' && $activityFormat=='new')) { ?>
			jQuery("#gameCommentForm").show();
			<?php } ?>
		});
	});
	
	jQuery("#tagComentSave").click(function(){
		var msg	=	jQuery.trim(jQuery("#tagComment").val());
		var qcodeTag	=	jQuery("#tagQcode").val();
		if(msg=='')
		{
			alert("You can not tag a Activity without commenting.");
			document.getElementById("tagMsgBox").style.display = 'none';
			return false;
		}
		jQuery.post("controller.php","mode=tagThisQcode&qcode="+qcodeTag+"&msg="+msg+"&type=activity&assignTO=<?=getActivityOwner($gameID)?>",function(data) { 
			if(data)
			{
				jQuery("#tagtoModify").css("visibility","hidden");
			}
		});
		jQuery("#tagMsgBox").hide();
	});
	<?php if(SERVER_TYPE=="LOCAL") { ?>
	/*checking activity file for offline*/
		checkIframeLoaded();
	/*checking activity file for offline*/
	<?php } ?>
});

function checkIframeLoaded() {
    // Get a handle to the iframe element
    var iframe = document.getElementById('iframe');
    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

    // Check if loading is complete
    if (iframeDoc.readyState  == 'complete') {
        iframe.contentWindow.onload = function(){
			if($('#iframe').contents().find('html').html().indexOf("404 Not Found") > -1){
				document.getElementById('completed').value = "3";
				var score, time, extraParams, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, currentLevel;
				saveGameResult(score, time, extraParams, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, currentLevel);
				alert("Not available in offline mode.");
				$("#feedbackNo").click();
			}
        };        
        return;
    }
	else
    	window.setTimeout('checkIframeLoaded();', 100);
}


function isEmailFormat(eid)
{
	/*var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;*/
	if(eid == "" || eid == null)
		return true;
	else
		return re.test(eid);
}
function checkStatus()
{
	if(redirect==1){
		setTryingToUnload();
		window.location = "error.php";
	}		
}

function getFlashMovieObject(movieName)
{
	if(document.embeds[movieName])	//Firefox
		return document.embeds[movieName];
	if(window.document[movieName])//IE
		return window.document[movieName];
	if(window[movieName])
		return window[movieName];
	if(document[movieName])
		return document[movieName];
	return null;
}

function init()
{
    var type = document.getElementById('gameType').value;
    var ver = document.getElementById('swfversion').value;
	var gameID = document.getElementById('gameID').value;
	var ver = document.getElementById('swfversion').value;
	if(ver=='as2')
		checkGameCompleteAS2();
	else if(ver=='as3')
		checkGameCompleteAS3();
	else if(ver=='html5')
	{
		checkGameCompleteHTML5();
	}
}

function recieveTextFromFlash( time,completed,score, extraParams) {

	var levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken;
<?php if($userType=="msAsStudent")	echo "showParameters(score, time, extraParams, completed);";?>
	if(completed==1)
	{
		document.getElementById('completed').value = "1";
		<?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams);"?>
	    return false;
	}
	else
	{
	    if(parseInt(time)%30==0)
	    {
	       <?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams);"?>
	    }
		self.setTimeout("checkGameCompleteAS3()", 1000);
	}
}
function checkGameCompleteAS2()
{
	var levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken;
    var flag = 0;
    try{
	var flashMovie=getFlashMovieObject("activitySwf");
	var score = flashMovie.GetVariable("score");
	var time = flashMovie.GetVariable("totalTimeTaken");
	var extraParams =  flashMovie.GetVariable("extraParameters");
	flag = flashMovie.GetVariable("completed");
    }
    catch(err){self.setTimeout("checkGameCompleteAS2()", 1000);}
	<?php if($userType=="msAsStudent")	echo "showParameters(score, time, extraParams, flag);";?>
	if(flag=="1")
	{
		document.getElementById('completed').value = "1";
	    <?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams);"?>
	    return false;
	}
	else
	{
	    if(parseInt(time)%30==0)
	    {
	       <?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams);"?>
	    }
		self.setTimeout("checkGameCompleteAS2()", 1000);
	}
}

function checkGameCompleteAS3()
{
    try {
		var flashMovie=getFlashMovieObject("activitySwf");
		flashMovie.send('recieveTextFromFlash');
	}catch(err){ self.setTimeout("checkGameCompleteAS3()", 1000);}
}

<?php if($gameDetails["version"]=="html5") { ?>
// Create IE + others compatible event handler
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
var activityFormat	=	'<?=$activityFormat?>';
var currentLevel	=	0;
var currentPos		=	0;
var timeCounter=0;
var timerID = null;
var skipped = 0;
var timerRunning = false;
var secs = 0;
var lastSavedLevel=-1;

<?php 
if($gameMode!="groupInstruction" || ($gameMode=="groupInstruction" && $activityFormat=="new")) { ?>
	// Listen to message from child window
	eventer(messageEvent,function(e) {
	  	//console.log('Message Received By Parent:  ',e.data);
		var dataReceived = e.data;
		if(dataReceived.indexOf('#@') === -1)
		{
		  var arrData = dataReceived.split("||");
		}
		else
		{
			var arrData = dataReceived.split("#@");
		}
		
	 	//console.log(arrData);
		//----  
	  
		<?php if($activityFormat=="old" ) { ?> 
			var time = arrData[0];
			var score = arrData[2];
			var extraParams =  arrData[3];
			var endSessionTimeout = arrData[4];
			var levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken;
		<?php } else { ?>
			var extraParams =  arrData[0];
			var levelsAttempted	=	arrData[2];
			var levelWiseStatus	=	arrData[3];
			var levelWiseScore	=	arrData[4];
			var levelWiseTimeTaken	=	arrData[5];
			var endSessionTimeout = arrData[6];
			var time,score;
		<?php } ?>
		if(endSessionTimeout == "true")
		{
			jQuery.ajax({
				type: "POST",
				url: "/mindspark/userInterface/controller.php?mode=endSessionType&endType=4",
				"async": false,
				success: function(msg) {
					tryingToUnloadPage = true;
					//alert("You have been logged out as Mindspark has not detected any input from you in the last 10 minutes. Login again to continue.");
					window.location.href = "/mindspark/userInterface/error.php";
				}
			});
		}
	  
	  
		//-----
	 	<?php if($userType=="msAsStudent")	echo "showParameters(score, time, extraParams, arrData[1], levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken);";?>
		if(activityFormat=="new")
		{
			var arrLevel	=	levelsAttempted.split("|");
			var arrStatus	=	levelWiseStatus.split("|");
			var arrScore	=	levelWiseScore.split("|");
			var arrTime		=	levelWiseTimeTaken.split("|");
			var arrExtraParams	=	extraParams.split("|");
		}
		timeCounter++;
		if(arrData[1]!=1 && skipped!=1)
		{
			self.setTimeout("checkGameCompleteHTML5()", 1000);
			if(activityFormat=="new") //if game is of new formate find current level data
			{
				if(arrStatus[currentPos]!=0 && currentLevel>0 && lastSavedLevel!=currentLevel)
				{
					<?php if($userType!="msAsStudent")
								echo "saveGameResult(score, time, arrExtraParams[currentPos], arrLevel[currentPos], arrStatus[currentPos], arrScore[currentPos], arrTime[currentPos], currentLevel);";?>
					lastSavedLevel=currentLevel;			
				}
				if(arrLevel[j]!=0)
				{
					for(var j=0;j<arrLevel.length;j++)
					{
						if(arrStatus[j]==0)
						{
							currentPos		=	j;
							currentLevel	=	arrLevel[j].replace("L","");
						}
					}
				}
			}
			if((time>0 && parseInt(time)%60==0) || parseInt(timeCounter)%15==0)
			{
			    timeCounter=0;
			     <?php if($userType!="msAsStudent") {
						if($activityFormat=="new") {
			               echo "saveGameResult(score, time, arrExtraParams[currentPos], arrLevel[currentPos], arrStatus[currentPos], arrScore[currentPos], arrTime[currentPos], currentLevel);";
			            }
						else
						{
							echo "saveGameResult(score,time,extraParams,'','','','','');";
						}
				} ?>
			}
		}
		else if(skipped==1){
		   document.getElementById('completed').value = "3";
		    <?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, currentLevel);"?>
		    return false;
		 }
		else
		{
			document.getElementById('completed').value = "1";
			<?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, currentLevel);"?>
			return false;
		}
	},false);
	<?php 
} 
else {?>

	initializeTimer();
	<?php  
} ?>

<?php  } ?>
function  checkGameCompleteHTML5()
{
	//console.log("Sending Message");
	var frame = document.getElementById("iframe");
	var win = frame.contentWindow;
	try{
		win.postMessage("checkGameComplete",'*');
	}catch(ex){
		//console.log(ex);
		alert('error');
	}
}

function endSession()
{
	redirect=0;
	//msg = "Are you sure you want to end the current session?";
	msg = i18n.t("enrichmentModulePage.endsessionMsg");
    var ans = confirm(msg);
    if(ans)
    {
		setTryingToUnload();
    	var params= "mode=endsession";
    	params += "&code="+1;
    	<?php if ($gameMode=='choiceScreen'){ ?>
    		if (document.getElementById('completed').value!="1") alert('Activity Unlocked! \nYou can now complete the activity from the activities page.');
    	<?php  } ?>
		$.post("controller.php",""+params+"",function(data){
			resp = data+"|| no response text";
			document.getElementById('frmEnrichmentModule').action='endSessionReport.php';
			document.getElementById('frmEnrichmentModule').submit();
		});
		
    	/*try {
    		var request = new Ajax.Request('controller.php',
    		{
    			method:'post',
    			parameters: params,
    			onSuccess: function(transport)
    			{
					alert("abcd");

    				resp = transport.responseText|| "no response text";
    				document.getElementById('frmEnrichmentModule').action='endSessionReport.php';
        			document.getElementById('frmEnrichmentModule').submit();
    			},
    			onFailure: function()
    			{
    				alert(i18n.t("enrichmentModulePage.problemMsg"));
    			}
    		}
    		);
    	}
    	catch(err) {}*/
    }
}

function saveGameResult(score, time, extraParams,levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken,currentLevel,hasQuitIntroduction)
{
	if(hasQuitIntroduction == 1)
	document.getElementById('completed').value = 1;
	var params="";
	params += "mode=saveGameDetailsAjax";
	if(document.getElementById('gameAttempt_srno'))
	params += "&gameAttempt_srno=" + document.getElementById('gameAttempt_srno').value;
	if(document.getElementById('gameID'))
	params += "&gameID=" + document.getElementById('gameID').value;
	params += "&totalScore=" + score;
	params += "&timeTaken=" + time;
	params += "&extraParams=" + extraParams;
	params += "&levelsAttempted=" + levelsAttempted;
	params += "&levelWiseStatus=" + levelWiseStatus;
	params += "&levelWiseScore=" + levelWiseScore;
	params += "&levelWiseTimeTaken=" + levelWiseTimeTaken;
	params += "&currentLevel=" + currentLevel;
	params += "&activityFormat=<?=$activityFormat?>";
	if(document.getElementById('ttAttemptID'))
	params += "&ttAttemptID=" + document.getElementById('ttAttemptID').value;
	if(document.getElementById('completed'))
	params += "&completed=" + document.getElementById('completed').value;
	params += "&gameMode=<?=$gameMode?>";
	params += "&type=<?=$activityType?>";
	params += "&sessionID=<?=$sessionID?>";
	params += "&windowName="+windowName;
	if(document.getElementById('gameCode'))
	{
		params += "&gameCode=" + document.getElementById('gameCode').value;
	}
	
	$.post("saveGameDetailsAjax.php",""+params+"",function(transport){
		if(transport=="error.php")
		{
			setTryingToUnload();
			window.location.href="error.php";
		}
		resp = transport.responseText;
		<?php if($gameMode!='groupInstruction' || ($gameMode=='groupInstruction' && $activityFormat=='new')) { ?>
		if(document.getElementById('completed').value==1||document.getElementById('completed').value==3)
		{
			<?php if($_SESSION["comprehensiveModule"]!="") { ?>
				getNextInFlow();
			<?php } else if($_SESSION["subModule"]!="" ) {?>
				getNextKstInFlow();
			<? } else {?>
				finishActivity();
			<?php } ?>
		}
		<?php } ?>
	});
}

function getNextInFlow()
{
	jQuery("#frmEnrichmentModule").attr("action","controller.php");
	setTryingToUnload();
	jQuery("#frmEnrichmentModule").submit();
}

function getNextKstInFlow()
{
	jQuery("#kstdiagnosticTest").attr("action","controller.php");
	setTryingToUnload();
	jQuery("#kstdiagnosticTest").submit();
}

function finishActivity()
{
	completitionFlag = 0;
	<?php if($gameAttempt==1 || ($gameMode=="groupInstruction"  || $gameMode=="choiceScreen")) { ?>
	completitionFlag = 1;
	<?php } ?>
	if(!feedbackBool && !jQuery("#askFeedbackDiv").is(":visible") && completitionFlag!=1)
	{
		jQuery("html, body").animate({scrollTop:0},"slow",function(){
			jQuery("#askFeedbackDiv").fadeIn("slow");
		})	
	}
	else
	{
		<?php
		//If user comes from DCT flow next action should submit params to Question.php page to continue....
		if($gameMode == "DCTstage" || $gameMode=="afterCluster" || $gameMode=="researchModule" || $gameMode=="comprehensive" || $gameMode=="groupInstruction"  || $gameMode=="choiceScreen")
		{
		?>
		if(document.getElementById('completed').value==1||document.getElementById('completed').value==3 || completitionFlag==1){
			$('#gameCommentForm').hide();
			setTryingToUnload();
			document.getElementById('frmContinueQues').submit();
		}			
		<?php
		}

		else
		{
		?>
			if(confirm(i18n.t("enrichmentModulePage.playAgainMsg"))){
				setTryingToUnload();
				document.forms[0].submit();
			}				
			else{
				setTryingToUnload();
				window.location="activity.php";
			}
		<?php
		}
		?>
	}
}

function showParameters(score, time, extraParams, completed, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken)
{
<?php if($activityFormat=="old") { ?>	
	jQuery("#devScore").text(score);
	jQuery("#devTime").text(time);
<?php } else { ?>
	jQuery("#devlevelsAttempted").text(levelsAttempted);
	jQuery("#devlevelWiseStatus").text(levelWiseStatus);
	jQuery("#devlevelWiseScore").text(levelWiseScore);
	jQuery("#devlevelWiseTimeTaken").text(levelWiseTimeTaken);
<?php } ?>	
	jQuery("#devExtra").text(extraParams);
	jQuery("#devComplete").text(completed);
}

function showTagBox(id, visibility, qcode) 
{
	document.getElementById('showTaggedQcode').innerHTML = "Need to modify activity code: "+qcode;
	document.getElementById("tagQcode").value	=	qcode;
	document.getElementById(id).style.display = visibility;
	document.getElementById("tagComment").value	=	'';
	document.getElementById("tagComment").focus();
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
 function skipGame(){
	 	setTryingToUnload();
        <?php 
            if($activityFormat=='old' &&$userType!="msAsStudent"){
                $query2  = "select sum(timeTaken) as times from adepts_userGameDetails where userid=".$userID." and gameid=".mysql_real_escape_string($gameID);
                $result2 = mysql_query($query2) or die(mysql_error().$query2);
                $line2   = mysql_fetch_array($result2);
                $timespent=$line2[0];
            }
            if($activityFormat=='new'&&$userType!="msAsStudent"){
                //$query3  = "select sum(a.timeTaken) as times from adepts_activityLevelDetails a, adepts_userGameDetails b where b.userid=".$userID." and b.gameid=".mysql_real_escape_string($gameID)." and a.srno=b.srno";
                //$result3 = mysql_query($query3) or die(mysql_error());
                //$line3   = mysql_fetch_array($result3);
                //$timespent=$line3[0];
                $query2  = " select sum(timeTaken) as times from adepts_userGameDetails where userid=".$userID." and srno=".$gameAttempt_srno." and gameid=".mysql_real_escape_string($gameID);
                $result2 = mysql_query($query2) or die(mysql_error().$query2);
                $line2   = mysql_fetch_array($result2);
                $timespent=$line2[0];
            }

            if($timespent<300 && $gameMode!='choiceScreen'){
         ?>	
    var timespent=<?php echo json_encode($timespent); ?>;
      var prompts=new Prompt({
            text:'This game needs to be completed to proceed.',
            type:'alert',
            func1:function(){
                $("#prmptContainer_skip").remove();
            },
            promptId:"skip"
        });
        <?php        
            } 
            else if ($gameMode=='choiceScreen'){?>
            	alert('Activity Unlocked! \nYou can now complete the activity from the activities page.');
            	$("#prmptContainer_skip").remove();
            	skipGameYes();
        <?php }
            elseif($timespent>=300){
        ?>
        //var res=confirm('Would you like to skip this game? ');
        var prompts=new Prompt({
            text:'Would you like to skip this game? ',
            type:'confirm',
            func1:skipGameYes,
            func2:function(){
                $("#prmptContainer_skip").remove();
            },
            promptId:"skip"
        });
        <?php
            }
        ?>
    }
    function skipGameYes(){
		setTryingToUnload();
		<?php if($theme==1){ ?>
			var text1 = 'State your reason for wanting to skip this game.<br><br><div style="text-align:left"><input type="radio" value="5" id="like" name="radios"/><label for="like">I dont like the game </label><br><input type="radio" value="2" id="tough" name="radios"/><label for="tough">The game is too tough </label><br><input type="radio" value="3" id="loading" name="radios"/><label for="loading">The game is not loading  </label></div>';
		<?php }else{ ?>
			var text1 = 'State your reason for wanting to skip this game.<br><br><div style="text-align:left"><input type="radio" value="5" id="like" name="radios"/><label for="like">I dont like the game </label><br><input type="radio" value="2" id="tough" name="radios"/><label for="tough">The game is too tough </label><br><input type="radio" value="3" id="loading" name="radios"/><label for="loading">The game is not loading  </label><br><input type="radio" value="4" id="other" name="radios"/><label for="other">Other reason</label><textarea placeholder="Please enter your reason here" id="t1" style="width: 245px;height: 40px;"></textarea></div>';
		<?php } ?>
       var prompts=new Prompt({
            text: text1,
            type:'alert',
            func1:saveFeedback,
			promptId:"skipFeedback"
        });
    }
    function saveFeedback(){
		var theme= <?php echo json_encode($theme); ?>;
		if(theme==1){
			var comment = "notApplicable";
		}else{
			var comment=$("#t1").val();
		}
        
        var radioSel=$("input[name='radios']:checked").val();
        if(radioSel!=undefined){
            var id=$('input[value="'+radioSel+'"]').attr('id');
            var txt=$('label[for="'+id+'"]').text();
        }
        if((comment==''||comment=='notApplicable')&&radioSel==undefined)
            alert('Select atleast one option!');
        else if(radioSel=='4'&&comment=='')
		{
			 $("#t1").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast");
			 $("#t1").addClass('required');
			 setTimeout(function (){
			 $("#t1").removeClass('required');
        	 }, 1150);
			/*alert('Specify the reason for skipping!');*/
		}
        else if(comment!=''||radioSel!=undefined){
            skipped=1;
			if(comment=='notApplicable'){
				data1={140:txt}
			}else{
				data1={140:txt,141:comment}
			}
            
            var formData =
            {
                gameID: <?=$gameID?>,
                formType: 'skipped',
                mode: "skipFeedback",
                data: JSON.stringify(data1)
            }
            jQuery(".prmptbutton").hide();
            jQuery.post("controller.php",formData,function(data){console.log(data);
                if(jQuery.trim(data) == "success")
                {
                    //jQuery("#gameCommentForm").html('<h4>Comment submitted !</h4>');
                    jQuery("#prmptContainer").remove();
                    feedbackBool = true;
                    /*if(formType == "type1")
                        jQuery("#gameCommentForm").css("padding-top","120px");*/
                    //document.getElementById('completed').value = "1";
                    finishActivity();
                    jQuery("#flash").show();
                }
                jQuery("#prmptContainer").remove();
            });
        }
		
    }
	
function submitForm(completitionFlag)
{

	<?php
	$_SESSION['game'] = false;
	if($gameAttempt==1 && $gameMode=="groupInstruction") { ?>
	completitionFlag = 1;
	<?php } ?>
	var childClass = '<?=$childClass;?>';
	if(completitionFlag!=1)
	{	
		jQuery("html, body").animate({scrollTop:0},"slow",function(){
			jQuery("#askFeedbackDiv").fadeIn("slow");
		})
		
		<?php	echo "saveGameResult('', window.secs, '','','','','','',1);"; ?>
	}
	else
	{
		setTryingToUnload();
		document.getElementById('frmContinueQues').submit();
	}
	clearTimeout(timerID);
}

function initializeTimer() {
    secs = 0;
    startTheTimer();
}

function startTheTimer() {
    secs = secs + 15;
	/*alert(secs);*/
	<?php if($userType!="msAsStudent") {
				
    echo "saveGameResult('', secs, '','','','','','');";
				
		} ?>
    timerRunning = true;
    timerID = window.setTimeout("startTheTimer()", 15000);
}

function stopTheTimer() {
    if (timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}
</script>

</head>

<body onLoad="load()" class="translation">
	<div id="top_bar">
		<div class="logo"></div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="topic"><!-- onClick="hideBar();">-->
				<div id="home" class="linkPointer" <?php if($homeLinkActive==1){ ?> onClick="getHome()"<?php } ?>></div>
				<div class="icon_text1"><span onClick="getHome()" class="textUppercase linkPointer" <?php if($theme==2){ ?> data-i18n="dashboardPage.home"<?php } ?>></span> > <font color="#606062"> <span class="textUppercase linkPointer" data-i18n="homePage.activity"  <?php if($homeLinkActive==1){ ?> onClick="javascript:setTryingToUnload(); window.location.href='activity.php'"<?php } ?>></span></font><font color="#606062"><span class="forHigherOnly"> : <?=$gameDetails["desc"]?></span></font></div>
			</div>
			<div id="linkBar" class="forLowerOnly hidden">
					<div id="endSessionDiv" class="lowerClassIcon linkPointer" onClick="endSession();">
                        <div class="endSessionDiv"></div>
                        <div class="quesLinkText" data-i18n="questionPage.endSession"></div>
                    </div>
                </div>
			<div class="class">
				<strong><span data-i18n="common.class"></span> </strong> <?=$childClass?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="new" onClick="endSession()">
				<div class="icon_text textUppercase" data-i18n="questionPage.endSession"></div>
				<div id="pointed">
				</div>
			</div>
		</div>
		<div id="info_bar" class="forHighestOnly">
				<a href="activity.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.activity"></span></div>
                </div><div class="arrow-right"></div></a>
				<div class="clear"></div>
		</div>
		<div id="white"></div>
	<div id="activitiesContainer" align="center">
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="report" onClick="endSession();">
					<span id="reportText">End Session</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div>
				<div class="empty">
				</div>			 
			</div>
			</div>
			<? if($_SESSION["subModule"]!="" ) { ?>
				<form name="kstdiagnosticTest" id="kstdiagnosticTest" action = "" method="post">
				<input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['teacherTopicCode']?>">
				<input type="hidden" name="mode" id="mode" value="ttSelection">
				<input type="hidden" name="isActivity" id="isActivity" value="1">
				<input type="hidden" name="gameAttempt_srno" id="gameAttempt_srno" value="<?=$gameAttempt_srno?>">
				<input type="hidden" name="kstAttemptNo" id="kstAttemptNo" value="<?=$_SESSION['topicAttemptNo']?>">
				</form>
			<? } ?>
		<form name="frmEnrichmentModule" id="frmEnrichmentModule" action="" method="post" autocomplete="off">
            <input type="hidden" id="ttAttemptID" value="<?php echo $ttAttemptID; ?>" />
			<?php
            if(isset($gameCode) && $gameCode!= "")
            {
            ?>
            <input type="hidden" id="gameCode" value="<?php echo $gameCode; ?>" />
            <?php
            }
            if($_SESSION["comprehensiveModule"]!="") {
                
            ?>
            <input type="hidden" name="mode" id="mode" value="comprehensiveAfterActivity" />
            <?php } ?>
            <input type="hidden" id="completed" value="0" />
            <input type="hidden" name="gameID" id="gameID" value="<?=$gameID?>">
            <input type="hidden" name="swfversion" id="swfversion" value="<?=$gameDetails["version"]?>">
            <input type="hidden" name="gameType" id="gameType" value="<?=$gameDetails["type"]?>">
            <input type="hidden" name="gameAttempt_srno" id="gameAttempt_srno" value="<?=$gameAttempt_srno?>">
            
<?php if($allowed==1)
	  { ?>
        	<div align="center" id="askFeedbackDiv">
                <h3 data-i18n="enrichmentModulePage.feedbackMsg"></h3>
                <input class="button1" type="button" name="feedbackYes" value="Yes" id="feedbackYes" />
                <input class="button1" type="button" name="feedbackNo" value="Skip" id="feedbackNo" />
            </div>
            <br /> 
			<?php if($userType=="msAsStudent") { ?>
        <div id="devParameter" style="float:left">
        	<h5>Parameters</h5>
			<?php if($activityFormat=="old") { ?>
                <span><b>Total Score:</b></span>&nbsp;<span id="devScore">0</span><br>
                <span><b>Time Taken:</b></span>&nbsp;<span id="devTime">0</span><br>
            <?php } else { ?>
                <span><b>Levels Attempted:</b></span>&nbsp;<span id="devlevelsAttempted">0</span><br>
                <span><b>Level Wise Status:</b></span>&nbsp;<span id="devlevelWiseStatus">0</span><br>
                <span><b>Level Wise Score:</b></span>&nbsp;<span id="devlevelWiseScore">0</span><br>
                <span><b>Level Wise Time Taken:</b></span>&nbsp;<span id="devlevelWiseTimeTaken">0</span><br>
            <?php } ?>    
                <span><b>Extra Params:</b></span>&nbsp;<span id="devExtra">0</span><br>
                <span><b>Completed:</b></span>&nbsp;<span id="devComplete">0</span><br><br><br>
            <?php
            if(($_SESSION["userType"]=="msAsStudent" || $userType=="msAsStudent") && $gameDetails["live"]=="Live") { ?>
                <span><input type="button" onClick="showTagBox('tagMsgBox', '', '<?=$gameID?>');" value="Need to modify" id="tagtoModify" name="tagModify"></span>
            <?php } ?>
        </div>
	<?php } ?>

        	<div class="gameswf" id="flash" style="z-index:1">
			<?php if($gameDetails["version"]=="html5") { ?>
                <iframe id="iframe" src="<?=$flashFile.$gameCode?>" height="<?=$height?>px" width="<?=$width?>px" frameborder="0" scrolling="no"></iframe>
            <?php } else  {?>
                <OBJECT id="activitySwf" height="<?=$height?>px" width="<?=$width?>px" classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
                    <param name="movie" value="<?=$flashFile.$gameCode?>">
                    <PARAM NAME="quality" VALUE="high">
                    <param name="allowScriptAccess" value="always" />
                    <param name="wmode" value="transparent" />
                    <PARAM name='menu' VALUE='false'>
                    <EMBED src="<?=$flashFile.$gameCode?>" swliveconnect="true" wmode="transparent" menu='false' quality=high allowScriptAccess="always" WIDTH="<?=$width?>px" HEIGHT="<?=$height?>px" NAME="activitySwf" id="activitySwf" ALIGN="center" type="application/x-shockwave-flash"></EMBED>
                </OBJECT>
            <?php } ?>
        </div>
        
		<?php 
		if($gameMode=='groupInstruction' && ($activityFormat=='old' || $gameAttempt==1)) { ?>
		<div align='center'>	
        	<input type='button' class='button1' id="quitBtn" onClick="submitForm(<?=checkGICompletition($userID,$gameID);?>)" name='btnSubmit' id="btnSubmit" value="Quit">
            <!--<a href="javascript:submitForm()" class="continue" style="display:block;"></a>-->
        </div>
		<?php } ?>
			<?php if($userID!=""){ ?>
<div class="feedBackForm" align="center">
	<!--<a href="javascript:void(0)" id="gameCommentFormLink">Add comment</a>-->
    <div id="gameCommentForm" style="display:none;">
<?php
//If feedback is not given on this game...
$sql = "SELECT COUNT(qid) FROM adepts_feedbackresponse WHERE userID='$userID' AND type='$gameID' GROUP BY qid";
$result = mysql_query($sql) or die(mysql_error().$sql);
$row = mysql_fetch_array($result);
$rows = $row[0];
if($rows < 3)
{
	$feedBackBool = "false";
	/*echo $sql;
	echo $rows;*/
?>

  <?php if($gameMode!='groupInstruction') { ?>
	<?php if($theme==1 || $theme==2 ) { $formType="type1"; ?>
    <br><br>
	<h4 class="feedbackHeader">Had fun playing the game</h4>
    <p style="margin-left:20px;">
        <table cellpadding="1" cellspacing="4" class="funRatingTable radioEnabled">
            <tr>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio" value="Yes" id="funRadio_0" />
                </td>
            	<td style="width:30%;">
                    <label class="radioLabel" for="funRadio_0">Yes</label>
                </td>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio" value="No" id="funRadio_1" />
                </td>
            	<td style="width:30%;">
                    <label class="radioLabel" for="funRadio_1">No</label>
                </td>
            </tr>
        </table>
    </p>
	<h4 class="feedbackHeader">Write about the game.</h4>
    	<textarea name="gameFeedbackText1" id="gameFeedbackText1" class="gameFeedbackText" rows="4" data-maxsize="250" wrap="virtual"style="width:80%; border:none;border: 1px solid #2f99cb;"></textarea>
        <br>
    	<input name="gameFeedbackButton" id="gameFeedbackButton" class="<?=$formType?> button1" type="button" value="Submit"></input>
		<!--<div id="textSubmit">Submit</div>-->
	<?php } else { $formType="type2"; ?>
	<h3 align="center" class="hidden">Add comment</h3>
	<h3 align="left" class="forHighestOnly">Give your feedback in the table below.</h3>
	<div class="forHighestOnly ratingTable radioEnabled">
		<div class="odd">
		<div class="headingDiv1">&nbsp;</div>
        <div class="headingDiv2">Poor</div>
        <div class="headingDiv2">Bad</div>
        <div class="headingDiv2">Average</div>
        <div class="headingDiv2">Good</div>
        <div class="headingDiv2">Excellent</div>
		</div>
		<div style="clear:both"></div>
      <div class="odd">
        <div class="headingDiv1">Design/Sound</div>
        <div class="headingDiv">&nbsp;<input name="designSound" type="checkbox" id="designSoundPoor" class="radio" value="Poor" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="designSound" type="checkbox" id="designSoundBad" class="radio" value="Bad" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="designSound" type="checkbox" id="designSoundAverage" class="radio" value="Average" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="designSound" type="checkbox" id="designSoundGood" class="radio" value="Good" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="designSound" type="checkbox" id="designSoundExcellent" class="radio" value="Excellent" />&nbsp;</div>
      </div>
	  <div style="clear:both"></div>
      <div class="odd">
        <div class="headingDiv1">Interesting</div>
        <div class="headingDiv">&nbsp;<input name="interesting"  type="checkbox" id="interestingPoor" class="radio" value="Poor" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="interesting" type="checkbox" id="interestingBad" class="radio" value="Bad" />&nbsp;</div>
       <div class="headingDiv"> &nbsp;<input name="interesting" type="checkbox" id="interestingAverage" class="radio" value="Average" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="interesting" type="checkbox" id="interestingGood" class="radio" value="Good" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="interesting" type="checkbox" id="interestingExcellent" class="radio" value="Excellent" />&nbsp;</div>
      </div>
	  
		<div style="clear:both"></div>
      <div>
        <div class="headingDiv1">Useful for Maths</div>
       <div class="headingDiv">&nbsp;<input name="maths" type="checkbox" id="mathsPoor" class="radio" value="Poor" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="maths" type="checkbox" id="mathsBad" class="radio" value="Bad" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="maths" type="checkbox" id="mathsAverage" class="radio" value="Average" />&nbsp;</div>
        <div class="headingDiv">&nbsp;<input name="maths" type="checkbox" id="mathsGood" class="radio" value="Good" />&nbsp;</div>
       <div class="headingDiv">&nbsp;<input name="maths" type="checkbox" id="mathsExcellent" class="radio" value="Excellent" />&nbsp;</div>
      </div>
	  
		<div style="clear:both"></div>
    </div>
	<br><br>
    <h4 class="feedbackHeader" align="left" style="margin-top: -14px;" data-i18n="enrichmentModulePage.commentHeading">Suggestions/Comments :</h4>
	<div id="commentBoxTr" class="forHighestOnly">
                            <textarea id="txtcomment" name="comment" data-maxsize="250" data-output="status2" wrap="virtual"></textarea>
                            <!-- Pending Integration <div id="status2" style="width:60%;font-weight:bold;text-align:right;font-size:x-small;"></div>-->
                            <div id="gameFeedbackButton" class="button1" data-i18n="common.submit" ></div><br/>
                            <div class="button1" data-i18n="common.cancel" onClick="closeFeedback();"></div>
                        </div>
    <p>
    	<textarea name="gameFeedbackText2" id="gameFeedbackText2" class="gameFeedbackText hidden" rows="4" style="width:400px;"></textarea>
    </p>
    <p align="center">
    	<input name="gameFeedbackButton" id="gameFeedbackButton" class="hidden <?=$formType?>" type="button" value="Submit">
    </p>
	<?php } ?>
	
<?php } else { $formType = "type3"; ?>

<h4 class="feedbackHeader">Did you find the activity useful?</h4>
    <p style="margin-left:20px;">
        <table cellpadding="1" cellspacing="4" class="funRatingTable radioEnabled">
            <tr>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio1" value="Yes" id="funRadio_0" />
                </td>
            	<td style="width:30%;">
                    <label class="radioLabel" >Yes</label>
                </td>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio1" value="No" id="funRadio_1" />
                </td>
            	<td style="width:30%;">
                    <label class="radioLabel">No</label>
                </td>
            </tr>
        </table>
    </p>
<?php if($childClass>5) { 
if (strpos($_SESSION['teacherTopicName'],'- Custom') !== false) 
{
	$topicName = explode('- Custom',$_SESSION['teacherTopicName']);
}

?>
<h4 class="feedbackHeader">How interested are you in learning more about <?=$topicName[0]?> now because of this activity?</h4>
    <p style="margin-left:20px;">
        <table cellpadding="1" cellspacing="4" class="funRatingTable radioEnabled">
            <tr>
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio2" value="Highly interested" id="funRadio_10" />
                </td>
            	<td style="width:24%;">
                    <label class="radioLabel" >Highly interested</label>
                </td>
				
            	<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio2" value="Somewhat interested" id="funRadio_11" />
                </td>
            	<td style="width:24%;">
                    <label class="radioLabel" >Somewhat interested</label>
                </td>
				
				<td>
                	<div class="squareBlackBox"></div>
					<input type="radio" class="radio" name="funRadio2" value="Not interested at all" id="funRadio_12" />
                </td>
            	<td style="width:24%;">
                    <label class="radioLabel" >Not interested at all</label>
                </td>
            </tr>
        </table>
    </p>

	<div> <h4 class="feedbackHeader"> How did this activity help you? (You can select more than 1 option) </h4> </div>
	<div style="width:280px;">
       <div style="text-align:left">&nbsp;<input name="activityHelp" type="checkbox"  class="activityHelp" value="It increased my interest in the topic." />It increased my interest in the topic.</div>
        <div  style="text-align:left">&nbsp;<input name="activityHelp" type="checkbox"  class="activityHelp" value="It made me realize the importance of this topic." />It made me realize the importance of this topic.</div>
        <div  style="text-align:left">&nbsp;<input name="activityHelp" type="checkbox"  class="activityHelp" value="It will help me understand the topic more." />It will help me understand the topic more.</div>
       <div style="text-align:left">&nbsp;<input name="activityHelp" type="checkbox"  class="activityHelp" value="It was not helpful. " />It was not helpful.  </div>
      
	</div>
<?php } ?>	
	<h4 class="feedbackHeader">Share your thoughts about the activity.</h4>
    	<textarea name="gameFeedbackText1" id="gameFeedbackText1" class="gameFeedbackText" rows="4" data-maxsize="250" wrap="virtual"style="width:80%; border:none;border: 1px solid #2f99cb;"></textarea>
        <br>
    	<input name="gameFeedbackButton" id="gameFeedbackButton" class="<?=$formType?> button1" type="button" value="Submit"></input>
	<!--Place quit button here-->

<?php } 
}
else
{
	$feedBackBool = "true";
?>
   	  <h4 data-i18n="enrichmentModulePage.commentLimitMsg"></h4>
<?php
}
?>
</div>
</div>
<?php
} ?>
        
<?php  }

	 else  { ?>
    		<div align="center" data-i18n="enrichmentModulePage.limitCrossedMsg"></div>
<?php  } ?>
		</form>
	</div>

    <div id="tagMsgBox" style="position: fixed; right:90px; bottom:90px; background-color: #00FFFF;width: 230px;padding: 10px;color: black;border: #0000cc 2px dashed;display: none;">
        <table>
            <tr><td><span id="showTaggedQcode"></span><br><strong data-i18n="enrichmentModulePage.comment">Comment:</strong></td></tr>
            <tr><td><textarea rows="4" cols="25" id="tagComment" name="tagComment"></textarea><input type="hidden" name="tagQcode" id="tagQcode" value=""></td></tr>
            <tr><td align="center"><input type="button" id="tagComentSave" name="tagCommentSave" value="Save"><input type="button" id="closeBox" name="closeBox" value="Close" onClick="showTagBox('tagMsgBox', 'none', '');"></td></tr>
        </table>
	</div>
	
	<?php
	
		$arraySize = 0;
		$objTT       = new teacherTopic($_SESSION['teacherTopicCode'],$childClass,$_SESSION['flow']);
		$highestClassInTopic = $objTT->meantForClasses[count($objTT->meantForClasses)-1];
		foreach($_SESSION['classSpecificClustersForTT'] as $key=>$val)
		{
			if($val[0]==$_SESSION['clusterCode'])
			$arrPos = $key;
			$arraySize++;
			
			if($_SESSION['classSpecificClustersForTT'][$key][2]!='timedTest' && $_SESSION['classSpecificClustersForTT'][$key][2]!='activity')
			$lastCluster = $_SESSION['classSpecificClustersForTT'][$key][0];
		}

		$currentCluster = $_SESSION['classSpecificClustersForTT'][$arrPos][0];
		if(($_SESSION['classSpecificClustersForTT'][$arraySize-1][2]=='activity' || ($_SESSION['classSpecificClustersForTT'][$arraySize-2][2]=='activity' && $_SESSION['classSpecificClustersForTT'][$arraySize-1][2]=='timedTest')) && $currentCluster==$lastCluster)
		{
			if($highestClassInTopic>$childClass)
				$redirectionPage = 'classLevelCompletion.php';
			else
				$redirectionPage = 'endSessionReport.php?mode=-3';
		}
		else
		{
			$redirectionPage = 'question.php';
		}
		if ($gameMode=="choiceScreen" && $activityType!='enrichment') $skipAllowed=1;
		if($gameMode=="groupInstruction" || $gameMode=="choiceScreen")
			$redirectionPage = 'question.php';
		if ($returnToPage=='topic' && $gameMode=="choiceScreen"){
			$redirectionPage = 'controller.php';
			?>
			<form name="frmContinueQues" id="frmContinueQues" action="<?=$redirectionPage;?>" method="post">
			    <input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['teacherTopicCode']?>">
			    <input type="hidden" name="mode" id="mode" value="ttSelection">
			</form>		
		<?php 
		} else {
		?>
			<form name="frmContinueQues" id="frmContinueQues" action="<?php if($gameMode=="researchModule") echo "researchModule.php"; else echo $redirectionPage;?>" method="post">
			    <input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['qcode']?>">
			    <?php $qNo = isset($_SESSION['qno'])?$_SESSION['qno']:"1"; ?>
			    <input type="hidden" name="qno" id="qno" value="<?=$qNo?>">
			    <input type="hidden" name="quesCategory" id="quesCategory" value="normal">
			    <input type="hidden" name="showAnswer" id="showAnswer" value="1">
			</form>	
	    
	    <?php
		}

            if($skipAllowed==1){
        ?>
        <input type="button" id="gameSkip" class="button1" value="skip" onClick="skipGame()" style="z-index:100;">
        <?php            }?>
	<script type="text/javascript">
        //var srno=<?=$gameAttempt_srno?>;
         var feedbackBool = <?=($feedBackBool !="")?$feedBackBool:'null';?>;
	</script>
 
<?php include("footer.php")?>

<?php
function getFlashFileDetails($gameID)
{
    $gameDetails = array();
    $query  = "SELECT gameFile, ver, type, timeLimit, gameDesc, live, noOfLevels, levelWiseMaxScores, passingParam
				 FROM adepts_gamesMaster WHERE gameID=".mysql_real_escape_string($gameID);
    $result = mysql_query($query) or die(mysql_error().$query);
    $line   = mysql_fetch_array($result);
    $gameDetails["flashFile"] = $line[0];
    $gameDetails["version"]   = $line[1];
    $gameDetails["type"]      = $line[2];
    $gameDetails["timeLimit"] = $line[3];
    $gameDetails["desc"] = $line[4];
	$gameDetails["live"] = $line[5];
	$gameDetails["noOfLevels"] = $line[6];
	$gameDetails["levelWiseMaxScores"] = $line[7];
	$gameDetails["passingParam"] = $line[8];
    return $gameDetails;
}

function insertAttemptDetails($userID, $gameID, $sessionID)
{
    $query  = "INSERT INTO adepts_userGameDetails SET userID=$userID, gameID=$gameID, sessionID=$sessionID, attemptedDate='".date("Y-m-d")."', timeTaken=0, completed=0, noOfJumps=0";
    $result = mysql_query($query) or die(mysql_error().$query);
    $gameAttempt_srno = mysql_insert_id();
    return $gameAttempt_srno;
}

function getActivityOwner($gameID)
{
	$sq	=	"SELECT B.owner1,B.owner2,owner FROM adepts_gamesMaster A , adepts_topicMaster B WHERE A.topicCode=B.topicCode AND A.gameID=$gameID";
	$rs	=	mysql_query($sq) or die(mysql_error().$sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else if($rw[1]!="")
		return $rw[1];
	else if($rw[2]!="")
		return $rw[2];
}

function getLastAttemptData($gameID,$userID,$noOfLevels,$gameType)
{
	$sq	=	"SELECT srno,completed FROM adepts_userGameDetails WHERE gameID=$gameID AND userID=$userID ORDER BY srno DESC LIMIT 1";
	$rs	=	mysql_query($sq) or die(mysql_error().$sq);
	if($rw=mysql_fetch_array($rs))
	{
		if(($rw["completed"]==0 || $rw["completed"]==-1) && $gameType=="regular")
		{
			$sqLevel	=	"SELECT level,status FROM adepts_activityLevelDetails WHERE srno=".$rw["srno"]." AND status=0 ORDER BY id LIMIT 1";
			$rsLevel	=	mysql_query($sqLevel) or die(mysql_error().$sqLevel);
			$rwLevel	=	mysql_fetch_array($rsLevel);
			return ($rwLevel[0]-1)."~".$rw["srno"];
		}
		else 
		{
			if($gameType=="regular")
				return -1;
			else
			{
				$sqLevel	=	"SELECT MAX(level) FROM adepts_userGameDetails A , adepts_activityLevelDetails B
								 WHERE A.gameID=$gameID AND A.userID=$userID AND A.srno=B.srno AND B.status=1";
				$rsLevel	=	mysql_query($sqLevel) or die(mysql_error().$sqLevel);
				$rwLevel	=	mysql_fetch_array($rsLevel);
				if($rwLevel[0]=="")
					return 0;
				else
					return $rwLevel[0];
			}
		}
	}
	else
	{
		if($gameType=="regular")
			return -1;
		else
			return 0;
	}
}

function insertLevelDetails($gameAttempt_srno,$noOfLevels, $activityType, $userID)
{
	for($i=1;$i<=$noOfLevels;$i++)
	{
		//$sq	=	"INSERT INTO adepts_activityLevelDetails SET srno=$gameAttempt_srno,type='$activityType',status=0,level=$i";
		$sq	=	"INSERT INTO adepts_activityLevelDetails SET srno=$gameAttempt_srno,type='$activityType',status=0,level=$i,userID=$userID";
		$rs	=	mysql_query($sq) or die(mysql_error().$sq);
	}
}
function getSkipValidity($userID,$gameID,$activityFormat,$activityType,$gameAttempt_srno,$userType)
{
    if($userType!="msAsStudent"){
	    if($activityFormat=='old'){
            $query  = "select count(*) as attempts from adepts_userGameDetails where userid=".$userID." and gameid=".mysql_real_escape_string($gameID);
            $result = mysql_query($query) or die(mysql_error().$query);
            $line   = mysql_fetch_array($result);
            $attempts=$line[0];

            $query2  = "select sum(timeTaken) as times from adepts_userGameDetails where userid=".$userID." and gameid=".mysql_real_escape_string($gameID);
            $result2 = mysql_query($query2) or die(mysql_error().$query2);
            $line2   = mysql_fetch_array($result2);
            $timespent=$line2[0];
        }
        elseif($activityFormat=='new'){
            $query  = "select attemptCnt from adepts_userGameDetails where gameid=".mysql_real_escape_string($gameID)." and srno=".$gameAttempt_srno." and userid=".$userID;
            $result = mysql_query($query) or die(mysql_error().$query);
            $line   = mysql_fetch_array($result);
            $attempts=$line[0];
            //$attempts=$attempts-1;
            $query2  = "select sum(timeTaken) as times from adepts_userGameDetails where userid=".$userID." and srno=".$gameAttempt_srno." and gameid=".mysql_real_escape_string($gameID);
            $result2 = mysql_query($query2) or die(mysql_error().$query2);
            $line2   = mysql_fetch_array($result2);
            $timespent=$line2[0];
        }
         $allow=0;
       if(($attempts>4||$timespent>300) && $activityType=='regular'){
            $allow=1;
        }
        return $allow;
    }
    else{
        $allow=0;
        return $allow;
        
    }
}

function checkGICompletition($userID,$gameID)
{
	
        $query2  = "select completed from adepts_userGameDetails where userid=".$userID." and gameid=".mysql_real_escape_string($gameID);
        $result2 = mysql_query($query2) or die(mysql_error().$query2);
		while($line2   = mysql_fetch_array($result2))
  		{
			 $completedIntroduction=$line2[0];
			 if($completedIntroduction==1)
			 break;
		}
		return $completedIntroduction;

}

function isGameAttempted($gameID,$userID)
{
	$sq	=	"SELECT COUNT(*) FROM adepts_userGameDetails WHERE gameID=$gameID AND userID=$userID and completed<>0";
	$rs	=	mysql_query($sq) or die(mysql_error().$sq);
	$rw=mysql_fetch_array($rs);
	if ($rw[0]>=1) return 1;
	else return 0;
}

function multiplayerEncrypt($str_message) {
	$len_str_message=strlen($str_message);
	$Str_Encrypted_Message="";
	for ($Position = 0;$Position<$len_str_message;$Position++) {
		$Byte_To_Be_Encrypted = substr($str_message, $Position, 1);
		$Ascii_Num_Byte_To_Encrypt = ord($Byte_To_Be_Encrypted);
		$Ascii_Num_Byte_To_Encrypt = ($Ascii_Num_Byte_To_Encrypt-6)*4+5;
		$Str_Encrypted_Message .= $Ascii_Num_Byte_To_Encrypt;
	}
	return $Str_Encrypted_Message;
}
?>
