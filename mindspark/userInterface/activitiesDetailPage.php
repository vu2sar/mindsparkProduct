<?php
$cls = $_GET["cls"];

@include("check1.php");
include("constants.php");
include_once("functions/functions.php");
include("classes/clsUser.php");
if(!isset($_SESSION['userID']))
{
	header( "Location: index.php");
}

$ttAttemptID = $_SESSION['teacherTopicAttemptID'];
$userType = isset($_REQUEST['userType'])?$_REQUEST['userType']:"";

if(isset($_REQUEST['gameCode']))
{
	$gameCode = $_REQUEST['gameCode'];
	$ativityType	=	"noRemedial";
}

if(isset($_REQUEST['gameID']))
{
	$gameID = $_REQUEST['gameID'];
	$ativityType	=	"noRemedial";
}
else if(isset($_SESSION['gameID']))
{
	$gameID = $_SESSION['gameID'];
	$ativityType	=	"noRemedial";
}
else if(isset($_REQUEST['qcode']) && !is_numeric($_REQUEST['qcode']))
{
	$remedialItemCode = $_REQUEST['qcode'];
	$ativityType	=	"remedial";
}
else
{
	echo "Activity not selected!";
	exit;
}
if($gameID == 56)
	header("Location: FCT.php");
if(isset($_SESSION['childName']))
	$Name = explode(" ",$_SESSION['childName']);
else
	$Name = "";
//$cls = $childClass  = $_SESSION['childClass'];
$childClass  = $_SESSION['childClass'];
$_SESSION['game'] = $gameID;
$userID = isset($_SESSION['userID'])?$_SESSION['userID']:"";
$sessionID = isset($_SESSION['sessionID'])?$_SESSION['sessionID']:"";

if($ativityType=="remedial")
{
	$activityDetails	=	getRemedialItemDetails($remedialItemCode);
}
else 
{
	$gameMode = isset($_POST['gameMode'])?$_POST['gameMode']:"";
	if($gameMode=="")
		$gameMode = isset($_POST['mode'])?$_POST['mode']:"";
	$activityDetails 	=	getActivityDetails($gameID);
}


$noOfLevels	=	$activityDetails["noOfLevels"];
$activityType	=	$activityDetails["type"];
$levelWiseMaxScores	=	$activityDetails["levelWiseMaxScores"];
$activityFormat	=	"old";
$activityAttempt_srno = "";

if(($userID!="" && $gameID!=18 && $gameID!=20 && $userType!="msAsStudent" && $noOfLevels==0 && $ativityType!="remedial") || ($gameMode=="DCTstage"))
{
	$activityAttempt_srno = insertAttemptDetailsActivity($userID, $gameID, $sessionID);
}
else if(($userID!="" && $gameID!=18 && $gameID!=20 && $userType!="msAsStudent" && $noOfLevels>0) || ($gameMode=="DCTstage"))
{
	$activityFormat	=	"new";
	if($activityDetails["type"]=="regular" || $ativityType=="remedial")
	{
		$previousLevelLock	=	1;
		if($ativityType=="remedial")
			$lastLevelClearedSrno	=	getLastAttemptDataRemedial($remedialItemCode,$userID,$activityDetails["noOfLevels"],"remedial");
		else
			$lastLevelClearedSrno	=	getLastAttemptDataActivity($gameID,$userID,$activityDetails["noOfLevels"],"regular");
		$lastLevelClearedSrnoArr	=	explode("~",$lastLevelClearedSrno);
		$lastLevelCleared	=	$lastLevelClearedSrnoArr[0];
		if($lastLevelCleared==-1)
		{
			if($ativityType=="remedial")
			{
				$activityAttempt_srno = insertAttemptDetailsRemedial($userID, $gameID, $sessionID);
				insertLevelDetailsRemedial($activityAttempt_srno,$noOfLevels,$activityType);
			}
			else
			{
				$activityAttempt_srno = insertAttemptDetailsActivity($userID, $gameID, $sessionID);
				insertLevelDetailsActivity($activityAttempt_srno,$noOfLevels,$activityType);
			}
			$lastLevelCleared=0;
		}
		else
		{
			$activityAttempt_srno	=	$lastLevelClearedSrnoArr[1];
		}
	}
	else
	{
		$previousLevelLock	=	0;
		$lastLevelCleared	=	getLastAttemptDataActivity($gameID,$userID,$noOfLevels,"optional");
		$activityAttempt_srno = insertAttemptDetailsActivity($userID, $gameID, $sessionID);
		insertLevelDetailsActivity($activityAttempt_srno,$noOfLevels, $activityType);
	}
}
else if($noOfLevels>0)
{
	$activityFormat	=	"new";
	$lastLevelCleared=0;
	$previousLevelLock	=	1;
}
$inputParameter="";
if($activityFormat=="new")
{
	if($activityDetails["passingParam"]!="")
	{
		$inputParameter	=	"?".$activityDetails["passingParam"]."&";
		$inputParameter	.=	"noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
	else
	{
		$inputParameter	=	"?noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
}
else
{
	if($activityDetails["passingParam"]!="")
		$inputParameter	=	"?".$activityDetails["passingParam"];
}

if($activityDetails["version"]=="html5")
{
	if($ativityType=="remedial")
	{
		$flashFile = ENRICHMENT_MODULE_FOLDER."/html5/remedialItems/".$remedialItemCode."/src/index.html".$inputParameter;
		$width = 800;
		$height = 600;
	}
	else
		$flashFile = ENRICHMENT_MODULE_FOLDER."/html5/".$activityDetails["flashFile"].$inputParameter;
}
else if($gameID!=18 && $gameID!=20 && $gameID!=47)
    $flashFile = ENRICHMENT_MODULE_FOLDER."/".$activityDetails["flashFile"];
else
{
	if($ativityType=="remedial")
	{
    	$flashFile = REMEDIAL_ITEM_FOLDER."/".$remedialItemCode.".swf";
		$imageSizeArray = getimagesize($flashFile);
		$width = $imageSizeArray[0];
		$height = $imageSizeArray[1];
		if($width =="")    $width= 800;
		if($height =="")    $height= 600;
	}
	else
		$flashFile = "Enrichment_Modules/".$activityDetails["flashFile"];
}

$imageSizeArray = @getimagesize(BASE_FOLDER."/".$flashFile);
$width = $imageSizeArray[0];
$height = $imageSizeArray[1];
if($width=="")
{
    if($gameID==34)
        $width=1024; else $width = "800"; }
if($height=="")
{if($gameID==34) $height="768";else  $height = "600";}

$allowed = 1;

$today = date("Y-m-d");
$arrActivities = array($gameID=>$activityDetails["desc"]);
$timeSpentOnActivitiesToday = 0;
if($userID!="" && $userType!="msAsStudent" && $activityType!="remedial")
{
    $timeSpentOnActivitiesToday = getTimeSpentOnActivities($userID,$arrActivities,$today,$today);
}

if($activityDetails["timeLimit"]!="" && $timeSpentOnActivitiesToday[$gameID] > $activityDetails["timeLimit"] && $activityType!="remedial")
    $allowed = 0;
//If user Comes from DCT flow it should always allow...
if($gameMode == "DCTstage" || $gameMode=="afterCluster" || $gameMode=="researchModule" || $userType=="msAsStudent" || $gameMode="comprehensive")
	$allowed = 1;
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="cache-control" charset="utf-8">
<title>Activities Detail Page</title>
<?php if($childClass<=3) { ?>
<link href="css/activitiesDetailPage/lowerClass.css" rel="stylesheet" type="text/css">
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<script>
	function load(){
		$('#clickText').html("Activities");
		$('#clickText').css("color","blue");
		$('#clickText').css("font-size","20px");
		var a= window.innerHeight -65;
		$('#activitiesContainer').css("height",a);
	}
</script>
<?php } else { ?>
<link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
<link href="css/activitiesDetailPage/midClass.css" rel="stylesheet" type="text/css">
<script>
	function load(){
		var a= window.innerHeight -230;
		$('#activitiesContainer').css("height",a);
	}
</script>
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>

<script>
var redirect = 1;
jQuery(document).ready(function(e) {

    jQuery("#feedbackYes").click(function(){
		jQuery("#gameCommentForm").show();
		jQuery("#askFeedbackDiv").hide();
		if(jQuery("#gameCommentForm").is(":visible"))
		{
			if(jQuery("#gameFeedbackText1"));
				jQuery("#gameFeedbackText1").focus();
		}
		jQuery("html, body").animate({scrollTop:jQuery(".feedBackForm").offset().top},"slow");
	});
	jQuery("#feedbackNo").click(function(){
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
		var formType = jQuery(this).attr("class");
		if(formType == "type1")
		{
			if(!jQuery("input[type=checkbox]").is(":checked") && jQuery("#gameFeedbackText1").val() == "")
			{
				alert("Please add atleast one comment !");
				return false;
			}
			var val104 = (jQuery("input[name=funRadio]:checked").val())?jQuery("input[name=funRadio]:checked").val():"";
			dataArray = {'104': val104, '105': jQuery("#gameFeedbackText1").val()};
		}
		else
		{
			if(!jQuery("input[type=checkbox]").is(":checked") && jQuery("#gameFeedbackText2").val() == "")
			{
				alert("Please add atleast one comment !");
				return false;
			}
			/*var val106 = (jQuery("input[name=gameConcept]:checked").val())?jQuery("input[name=gameConcept]:checked").val():"";*/
			var val107 = (jQuery("input[name=designSound]:checked").val())?jQuery("input[name=designSound]:checked").val():"";
			var val108 = (jQuery("input[name=interesting]:checked").val())?jQuery("input[name=interesting]:checked").val():"";
			var val109 = (jQuery("input[name=maths]:checked").val())?jQuery("input[name=maths]:checked").val():"";
			dataArray = {'107': val107, '108': val108, '109': val109, '110':jQuery("#gameFeedbackText2").val() };
		}
		var formData =
		{
			gameID: <?=$gameID?>,
			formType: formType,
			mode: "gameFeedBack",
			data: JSON.stringify(dataArray)
		}
		jQuery("#gameCommentForm").hide();
		jQuery.post("controller.php",formData,function(data){
			if(jQuery.trim(data) == "success")
			{
				//jQuery("#gameCommentForm").html('<h4>Comment submitted !</h4>');
				jQuery("#gameCommentForm").hide();
				feedbackBool = true;
				/*if(formType == "type1")
					jQuery("#gameCommentForm").css("padding-top","120px");*/
				finishActivity();
				jQuery("#flash").show();
			}
			jQuery("#gameCommentForm").show();
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
	
	<?php if($userType=="msAsStudent") echo 'jQuery(".gameswf").css({"margin-left":"10px"});'; ?>
});
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
	if(redirect==1)
		window.location = "error.php";
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
    if(gameID!="18" && gameID!="20")
    {
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

<?php if($activityDetails["version"]=="html5") { ?>
// Create IE + others compatible event handler
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
var activityFormat	=	'<?=$activityFormat?>';
var currentLevel	=	0;
var currentPos		=	0;

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
 
//----  
  
<?php if($activityFormat=="old") { ?>
	var time = arrData[0];
	var score = arrData[2];
	var extraParams =  arrData[3];
	var levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken;
<?php } else { ?>
	var extraParams =  arrData[0];
	var levelsAttempted	=	arrData[2];
	var levelWiseStatus	=	arrData[3];
	var levelWiseScore	=	arrData[4];
	var levelWiseTimeTaken	=	arrData[5];
	var time,score;
<?php } ?>

  
  //console.log(levelsAttempted+"@@"+levelWiseStatus);
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
  if(arrData[1]!=1)
  {
  	self.setTimeout("checkGameCompleteHTML5()", 1000);
	if(activityFormat=="new") //if game is of new formate find current level data
	{
		if(arrStatus[currentPos]!=0 && currentLevel>0)
		{
			<?php if($userType!="msAsStudent")
						echo "saveGameResult(score, time, arrExtraParams[currentPos], arrLevel[currentPos], arrStatus[currentPos], arrScore[currentPos], arrTime[currentPos], currentLevel);";?>
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
	
  	if(parseInt(time)%30==0)
	{
	    <?php if($userType!="msAsStudent") {
				if($activityFormat=="new")
					echo "saveGameResult(score,time,arrExtraParams[j],arrLevel[j],arrStatus[j],arrScore[j],arrTime[j],currentLevel);";
				else
					echo "saveGameResult(score,time,extraParams,'','','','','');";
		} ?>
	}
  }
  else
  {
  	document.getElementById('completed').value = "1";
  	<?php if($userType!="msAsStudent")	echo "saveGameResult(score, time, extraParams, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, currentLevel);"?>
	return false;
  }
},false);

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
	msg = "Are you sure you want to end the current session?";
    var ans = confirm(msg);
    if(ans)
    {
    	var params= "mode=endsession";
    	params += "&code="+1;
    	try {
    		var request = new Ajax.Request('controller.php',
    		{
    			method:'post',
    			parameters: params,
    			onSuccess: function(transport)
    			{

    				resp = transport.responseText|| "no response text";
    				document.getElementById('frmEnrichmentModule').action='endSessionReport.php';
        			document.getElementById('frmEnrichmentModule').submit();
    			},
    			onFailure: function()
    			{
    				alert('Something went wrong...');
    			}
    		}
    		);
    	}
    	catch(err) {}
    }
}

function saveGameResult(score, time, extraParams,levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken,currentLevel)
{
	var params="";
	params += "mode=saveGameDetails";
	params += "&activityAttempt_srno=" + document.getElementById('activityAttempt_srno').value;
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
	params += "&ttAttemptID=" + document.getElementById('ttAttemptID').value;
	params += "&completed=" + document.getElementById('completed').value;
	params += "&gameMode=<?=$gameMode?>";
	params += "&type=<?=$activityType?>";
	if(document.getElementById('gameCode'))
	{
		params += "&gameCode=" + document.getElementById('gameCode').value;
	}
	//alert(params);
	var request = new Ajax.Request('ajax/saveGameDetails.php',
		{
			method:'post',
			parameters: params,
			onSuccess: function(transport)
			{
				resp = transport.responseText;
				//alert(resp);
				if(document.getElementById('completed').value==1)
				{
					<?php if($_SESSION["comprehensiveModule"]!="") { ?>
						getNextInFlow();
					<?php } else { ?>
						finishActivity();
					<?php } ?>
				}

			},
			onFailure: function()
			{
				//alert('Something went wrong while saving...');
			}
		}
		);
}

function getNextInFlow()
{
	jQuery("#frmEnrichmentModule").attr("action","controller.php");
	jQuery("#frmEnrichmentModule").submit();
}

function finishActivity()
{
	if(!feedbackBool && !jQuery("#askFeedbackDiv").is(":visible"))
	{
		jQuery("html, body").animate({scrollTop:0},"slow",function(){
			jQuery("#askFeedbackDiv").fadeIn("slow");
		})
	}
	else
	{
		<?php
		//If user comes from DCT flow next action should submit params to question.php page to continue....
		if($gameMode == "DCTstage" || $gameMode=="afterCluster" || $gameMode=="researchModule" || $gameMode="comprehensive")
		{
		?>
		if(document.getElementById('completed').value==1)
			document.getElementById('frmContinueQues').submit();
		<?php
		}

		else if($gameID!=18 && $gameID!=20)
		{
		?>
			if(confirm("Do you want to play this game again?"))
				document.forms[0].submit();
			else
				window.location="activities.php";
		<?php
		}

		else
		{
		?>
			window.location="activities.php";
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
</script>
</head>
<body onload="load()" onresize="load();">
<form name="frmEnrichmentModule" id="frmEnrichmentModule" action="" method="post" autocomplete="off">
    <input type="hidden" id="ttAttemptID" value="<?php echo $ttAttemptID; ?>" /><?php
    if(isset($gameCode) && $gameCode!= "")
    { ?>
		<input type="hidden" id="gameCode" value="<?php echo $gameCode; ?>" />
    <?php  }
    if($_SESSION["comprehensiveModule"]!="") { ?>
		<input type="hidden" name="mode" id="mode" value="comprehensiveAfterActivity" />
    <?php } ?>
    <input type="hidden" id="completed" value="-1" />
    <input type="hidden" name="gameID" id="gameID" value="<?=$gameID?>">
    <input type="hidden" name="swfversion" id="swfversion" value="<?=$activityDetails["version"]?>">
    <input type="hidden" name="gameType" id="gameType" value="<?=$activityDetails["type"]?>">
    <input type="hidden" name="activityAttempt_srno" id="activityAttempt_srno" value="<?=$activityAttempt_srno?>">
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="logout">
        	<div class="logout"></div>
        	<div class="logoutText">Logout</div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar">
			<div id="topic">
				<div id="home">
				<div class="icon_text1">HOME > <font color="#606062"> ACTIVITIES</font></div>
				</div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass?>
			</div>
			<div class="Name">
				<strong><?=$Name[0]." ".$Name[1]?></strong>
			</div>
			<div id="new">
				<div class="icon_text">END SESSION</div>
				<div id="pointed">
				</div>
			</div>
		</div>
	<div id="activitiesContainer" align="center">
<?php $userType="msAsStudent"; if($userType=="msAsStudent") { ?>
        <div id="devParameter">
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
        if(($_SESSION["userType"]=="msAsStudent" || $userType=="msAsStudent") && $activityDetails["live"]=="Live") { ?>
            <span><input type="button" onclick="showTagBox('tagMsgBox', '', '<?=$gameID?>');" value="Need to modify" id="tagtoModify" name="tagModify"></span>
        <?php } ?>
        </div>
	<?php } ?>

        <div class="gameswf" id="flash" style="z-index:1">
        <?php if($activityDetails["version"]=="html5") { ?>
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
	</div>
</form>

<div id="tagMsgBox" style="position: fixed; right:90px; bottom:90px; background-color: #00FFFF;width: 230px;padding: 10px;color: black;border: #0000cc 2px dashed;display: none;">
    <table>
        <tr><td><span id="showTaggedQcode"></span><br><strong>Comment:</strong></td></tr>
        <tr><td><textarea rows="4" cols="25" id="tagComment" name="tagComment"></textarea><input type="hidden" name="tagQcode" id="tagQcode" value=""></td></tr>
        <tr><td align="center"><input type="submit" id="tagComentSave" name="tagCommentSave" value="Save"><input type="button" id="closeBox" name="closeBox" value="Close" onclick="showTagBox('tagMsgBox', 'none', '');"></td></tr>
    </table>
</div>
    
    <form name="frmContinueQues" id="frmContinueQues" action="<?php if($gameMode=="researchModule") echo "researchModule.php"; else echo "question.php";?>" method="post">
        <input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['qcode']?>">
        <?php $qNo = isset($_SESSION['qno'])?$_SESSION['qno']:"1"; ?>
        <input type="hidden" name="qno" id="qno" value="<?=$qNo?>">
        <input type="hidden" name="quesCategory" id="quesCategory" value="normal">
        <input type="hidden" name="showAnswer" id="showAnswer" value="1">
    </form>

	<div id="bottom_bar">
		<div id="copyright">© 2013 Educational Initiatives Pvt. Ltd.
		</div>
    </div>
	
</body>
</html>
<?php
function getActivityDetails($gameID)
{
    $activityDetails = array();
    $query  = "SELECT gameFile, ver, type, timeLimit, gameDesc, live, noOfLevels, levelWiseMaxScores, passingParam
				 FROM adepts_gamesMaster WHERE gameID=".mysql_real_escape_string($gameID);
    $result = mysql_query($query) or die(mysql_error());
    $line   = mysql_fetch_array($result);
    $activityDetails["flashFile"] = $line[0];
    $activityDetails["version"]   = $line[1];
    $activityDetails["type"]      = $line[2];
    $activityDetails["timeLimit"] = $line[3];
    $activityDetails["desc"] = $line[4];
	$activityDetails["live"] = $line[5];
	$activityDetails["noOfLevels"] = $line[6];
	$activityDetails["levelWiseMaxScores"] = $line[7];
	$activityDetails["passingParam"] = $line[8];
    return $activityDetails;
}

function insertAttemptDetailsActivity($userID, $gameID, $sessionID)
{
    $query  = "INSERT INTO adepts_userGameDetails SET userID=$userID, gameID=$gameID, sessionID=$sessionID, attemptedDate='".date("Y-m-d")."', timeTaken=0, completed=0, noOfJumps=0";
    $result = mysql_query($query) or die(mysql_error());
    $activityAttempt_srno = mysql_insert_id();
    return $activityAttempt_srno;
}

function getActivityOwner($gameID)
{
	$sq	=	"SELECT B.owner1,B.owner2,owner FROM adepts_gamesMaster A , adepts_topicMaster B WHERE A.topicCode=B.topicCode AND A.gameID=$gameID";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else if($rw[1]!="")
		return $rw[1];
	else if($rw[2]!="")
		return $rw[2];
}

function getLastAttemptDataActivity($gameID,$userID,$noOfLevels,$gameType)
{
	$sq	=	"SELECT srno,completed FROM adepts_userGameDetails WHERE gameID=$gameID AND userID=$userID ORDER BY srno DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		if($rw["completed"]==0)
		{
			$sqLevel	=	"SELECT level,status FROM adepts_activityLevelDetails WHERE srno=".$rw["srno"]." AND status=0 ORDER BY id LIMIT 1";
			$rsLevel	=	mysql_query($sqLevel);
			$rwLevel	=	mysql_fetch_array($rsLevel);
			return ($rwLevel[0]-1)."~".$rw["srno"];
		}
		else 
		{
			if($gameType=="regular")
				return 0;
			else
			{
				$sqLevel	=	"SELECT MAX(level) FROM adepts_userGameDetails A , adepts_activityLevelDetails B
								 WHERE A.gameID=$gameID AND A.userID=$userID AND A.srno=B.srno AND B.status=1";
				$rsLevel	=	mysql_query($sqLevel);
				if($rwLevel=mysql_fetch_array($rsLevel))
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

function insertLevelDetailsActivity($activityAttempt_srno,$noOfLevels, $activityType)
{
	for($i=1;$i<=$noOfLevels;$i++)
	{
		$sq	=	"INSERT INTO adepts_activityLevelDetails SET srno=$activityAttempt_srno,type='$activityType',status=0,level=$i,userID=".$_SESSION["userID"];
		$rs	=	mysql_query($sq);
	}
}

//for remedials

function getRemedialItemDetails($remedialItemCode)
{
    $remedialItemDetails = array();
	$query	=	"SELECT remedialItemDesc, noOfLearningGoals, version, status, noOfLevels, levelWiseMaxScores, passingParam
				 FROM adepts_remedialItemMaster
				 WHERE remedialItemCode='".mysql_real_escape_string($remedialItemCode)."'";
    $result = mysql_query($query) or die(mysql_error());
    $line   = mysql_fetch_array($result);
    $remedialItemDetails["desc"] = $line[0];
	$remedialItemDetails["noOfLearningGoals"]   = $line[1];
    $remedialItemDetails["version"]   = $line[2];
	$remedialItemDetails["status"]   = $line[3];
	$remedialItemDetails["noOfLevels"] = $line[4];
	$remedialItemDetails["levelWiseMaxScores"] = $line[5];
	$remedialItemDetails["passingParam"] = $line[6];
    return $remedialItemDetails;
}

function getLastAttemptDataRemedial($remedialItemCode,$userID,$noOfLevels,$gameType)
{
	$sq	=	"SELECT remedialAttemptID,result FROM adepts_remedialItemAttempts WHERE remedialItemCode='$remedialItemCode' AND userID=$userID
			 ORDER BY remedialAttemptID DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		if($rw["result"]==0)
		{
			$sqLevel	=	"SELECT level,status FROM adepts_activityLevelDetails WHERE srno=".$rw["remedialAttemptID"]." AND status=0 AND type='$gameType' ORDER BY id LIMIT 1";
			$rsLevel	=	mysql_query($sqLevel);
			$rwLevel	=	mysql_fetch_array($rsLevel);
			return ($rwLevel[0]-1)."~".$rw["remedialAttemptID"];
		}
		else 
		{
			return 0;
		}
	}
	else
	{
		return -1;
	}
}

function insertAttemptDetailsRemedial($userID, $remedialItemCode, $sessionID, $fromSDL, $clusterAttemptID)
{
	$query	=	"INSERT INTO adepts_remedialItemAttempts (userID, sessionID, remedialItemCode, clusterAttemptID, result, timeTaken, fromSDL)
				 VALUES ($userID, '$sessionID', '$remedialItemCode', '$clusterAttemptID', 0, 0, '$fromSDL')";
    $result = mysql_query($query) or die(mysql_error());
    $remedialAttemptID = mysql_insert_id();
    return $remedialAttemptID;
}
?>