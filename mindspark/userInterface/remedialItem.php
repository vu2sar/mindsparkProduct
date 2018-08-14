<?php
error_reporting(E_ERROR);
include("check1.php");
include("constants.php");
include("errorLog.php");
include("functions/comprehensiveModuleFunction.php");
//print_r($_POST);
if(!isset($_SESSION['userID']))
{
	header( "Location: index.php");
}
if(isset($_REQUEST['qcode']))
	$remedialItemCode = $_REQUEST['qcode'];
else
{
	echo "Remedial Item not specified!";
	exit;
}
$userID	=	$_SESSION['userID'];
$userType = isset($_REQUEST['userType'])?$_REQUEST['userType']:"";
if($userType=="msAsStudent") { 
	$_SESSION["userType"]="msAsStudent";
}
$sdl = $_SESSION['currentSDL'];
if(isset($_SESSION['childName']))
	$Name = explode(" ",$_SESSION['childName']);
else
	$Name = "";
$childClass  = $_SESSION['childClass'];
$teacherTopicName = $_SESSION['teacherTopicName'];
$sessionID = $_SESSION['sessionID'];
$remedialItemDetails = getRemedialItemDetails($remedialItemCode);
$noOfLevels	=	$remedialItemDetails["noOfLevels"];
$activityType	=	"remedial";
$levelWiseMaxScores	=	$remedialItemDetails["levelWiseMaxScores"];
$activityFormat	=	"old";
//echo '<pre>';print_r($_SESSION['subModule_srno']);die;
if($noOfLevels>0)
{
	$activityFormat	=	"new";
	$previousLevelLock	=	1;
	$lastLevelClearedSrno	=	getLastAttemptData($remedialItemCode,$userID,$noOfLevels,"remedial");
	$lastLevelClearedSrnoArr	=	explode("~",$lastLevelClearedSrno);
	$lastLevelCleared	=	$lastLevelClearedSrnoArr[0];
	if($lastLevelCleared==-1)
	{
		$remedialAttemptID = insertAttemptDetails($userID, $remedialItemCode, $sessionID, $sdl, $_SESSION["clusterAttemptID"]);
		insertLevelDetails($remedialAttemptID,$noOfLevels,$activityType,$_SESSION["userID"]);
		$lastLevelCleared=0;
	}
	else
	{
		$remedialAttemptID	=	$lastLevelClearedSrnoArr[1];
	}
}

$inputParameter="";
if($activityFormat=="new")
{
	if($remedialItemDetails["passingParam"]!="")
	{
		$inputParameter	=	"?".$remedialItemDetails["passingParam"]."&";
		$inputParameter	.=	"noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
	else
	{
		$inputParameter	=	"?noOfLevels=".$noOfLevels."&levelWiseMaxScores=".$levelWiseMaxScores."&lastLevelCleared=".$lastLevelCleared."&previousLevelLock=".$previousLevelLock;
	}
}
else
{
	if($remedialItemDetails["passingParam"]!="")
		$inputParameter	=	"?".$remedialItemDetails["passingParam"];
}

if(isset($_REQUEST["viewmode"]) && $_REQUEST["viewmode"]=="html5")
{
	$remedialItemDetails["version"]="html5";
}

//$userType ="msAsStudent";

if($remedialItemDetails["version"]=="html5")
{
	if($userType=="msAsStudent")
		$flashFile = ENRICHMENT_MODULE_FOLDER_DEV."/html5/remedialItems/".$remedialItemCode."/src/index.html".$inputParameter;
	else
		$flashFile = ENRICHMENT_MODULE_FOLDER."/html5/remedialItems/".$remedialItemCode."/src/index.html".$inputParameter;
	$width = 800;
	$height = 600;
}
else
{	
	$flashFile = REMEDIAL_ITEM_FOLDER."/".$remedialItemCode.".swf";
	$imageSizeArray = getimagesize($flashFile);
	$width = $imageSizeArray[0];
	$height = $imageSizeArray[1];
	if($width =="")    $width= 800;
	if($height =="")    $height= 600;
}


?>

<?php include("header.php");?>

<title>Remedial</title>
<?php 
if (isset($_REQUEST['theme'])) 
	$theme	=	$_REQUEST['theme'];
if($theme==1) { ?>
<link href="css/activitiesDetailPage/lowerClass.css" rel="stylesheet" type="text/css">
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<script>
	function load(){
		setTimeLimit();
		jQuery('#clickText').html("Activities");
		jQuery('#clickText').css("color","blue");
		jQuery('#clickText').css("font-size","20px");
		var ua = navigator.userAgent;
			if( ua.indexOf("Android") >= 0 )
			{
			jQuery(".gameswf").css("margin-left","0%");
		}
	}
</script>
<?php } else if($theme==2) { ?>
<link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
<link href="css/activitiesDetailPage/midClass.css" rel="stylesheet" type="text/css">
<script>
	var infoClick=0;
	function load(){
		setTimeLimit();
		jQuery("body").animate({ scrollTop: jQuery(document).height() }, 1000);
		var ua = navigator.userAgent;
			if( ua.indexOf("Android") >= 0 )
			{
			jQuery(".gameswf").css("margin-left","0%");
		}
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
		<style>
.gameswf {
	margin-left:14%;
}
@media (max-width: 1024px){
		.gameswf {
			margin-left:28%;
		}
}
</style>
		<script>
	function load(){
		setTimeLimit();
		var a= window.innerHeight + (50);
			jQuery('#activitiesContainer').css({"height":a+"px"});
			jQuery('#menuBar').css({"height":a+"px"});
			jQuery('#sideBar').css({"height":a+"px"});
			var ua = navigator.userAgent;
			if( ua.indexOf("Android") >= 0 )
			{
				jQuery(".gameswf").css("margin-left","12%");
			}
			if( ua.indexOf("iPad") >= 0 )
			{
				if( ua.indexOf("OS 6") >= 0 )
				{
					jQuery(".gameswf").css("margin-left","30%");
				}else{
					jQuery(".gameswf").css("margin-left","14%");
				}		
			}
	}
	function closeFeedback(){
		jQuery("#gameCommentForm").hide();
		jQuery('#activitiesContainer').css("height","auto");
		jQuery('#sideBar').css("height",$('#activitiesContainer').css("height"));
		jQuery('#main_bar').css("height",$('#activitiesContainer').css("height"));
	}
	function hideBar(){
		
	}

</script>
<?php } ?>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script src="libs/closeDetection.js"></script>-->
<script type="text/javascript"  src="libs/prototype.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>-->

<script>
var langType	=	'<?=$language?>';

jQuery(document).ready(function(e) {
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		jQuery(".translation").i18n();
	});

    jQuery("#tagComentSave").click(function(){
		var msg	=	jQuery.trim(jQuery("#tagComment").val());
		var qcodeTag	=	jQuery("#tagQcode").val();
		if(msg=='')
		{
			alert("You can not tag a remedial without commenting.");
			document.getElementById("tagMsgBox").style.display = 'none';
			return false;
		}
		jQuery.post("controller.php","mode=tagThisQcode&qcode="+qcodeTag+"&msg="+msg+"&type=remedial&assignTO=<?=getRemedialOwner($remedialItemCode)?>",function(data) {
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
var redirect = 1;

function checkIframeLoaded() {
    // Get a handle to the iframe element
    var iframe = document.getElementById('iframe');
    var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

    // Check if loading is complete
    if (iframeDoc.readyState  == 'complete') {
        iframe.contentWindow.onload = function(){
			if($('#iframe').contents().find('html').html().indexOf("404 Not Found") > -1){
				redirect=0;
				setTryingToUnload();
				jQuery("#result").val("1");
				document.getElementById('frmRemedialItem').submit();
			}
        };        
        return;
    }
	else
    	window.setTimeout('checkIframeLoaded();', 100);
}

function checkStatus()
{
	if(redirect==1)
	{
		setTryingToUnload();
		window.location = "error.php";
	}
}

function getFlashMovieObject(movieName)
{
	if (window.document.movieName)
	{
	  return window.document.movieName;
	}
	if (navigator.appName.indexOf("Microsoft Internet")==-1)
	{
		if (window.document.embeds && window.document.embeds[movieName])
		  return window.document.embeds[movieName];
	}
	else
	{
		return window.document.getElementById(movieName);
	}
	/*if(document.embeds[movieName])	//Firefox
		return document.embeds[movieName];
	if(window.document[movieName])//IE
		return window.document[movieName];
	if(window[movieName])
		return window[movieName];
	if(document[movieName])
		return document[movieName];
	return null;*/
}

function setTimeLimit()
{
	var ver = document.getElementById('version').value;
	if(ver=='html5')
        checkRemedialCompleteHTML5();
	else
		checkRemedialComplete();
}

function recieveTextFromFlash( time,flag, learningGoalStatus, noOfPrompts,levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, extraParams) {
	//document.getElementById('htmlText').value = "learningGoalStatus = "+learningGoalStatus+"\n flag = "+flag+"\n TotalPrompt = "+noOfPrompts+"\n totalTimeTaken = "+totalTimeTaken;
	document.getElementById('result').value = flag;
	document.getElementById('totalTimeTaken').value = time;
	document.getElementById('learningGoalStatus').value = learningGoalStatus;
	document.getElementById('noOfPrompts').value = noOfPrompts;
	
	document.getElementById('levelsAttempted').value = levelsAttempted;
	document.getElementById('levelWiseStatus').value = levelWiseStatus;
	document.getElementById('levelWiseScore').value = levelWiseScore;
	document.getElementById('levelWiseTimeTaken').value = levelWiseTimeTaken;
	document.getElementById('extraParameters').value = extraParams;
}

function checkRemedialComplete()
{
	try {
		var flashMovie=getFlashMovieObject("remedialItemSwf");
		flashMovie.send('recieveTextFromFlash');
	}catch(err){}
	var flag = document.getElementById('result').value;

	if(flag!=0)
	{
		redirect=0;
		alert('You have completed this exercise!');
		<?php if($userType=="msAsStudent") { ?>
		document.getElementById('frmRemedialItem').action='dashboard.php';
		<?php } ?>
		setTryingToUnload();
		document.getElementById('frmRemedialItem').submit();
	}
	else
	{
		self.setTimeout("checkRemedialComplete()", 5000);
	}
}
function logoff()
{
	setTryingToUnload();
	window.location="logout.php";
}

<?php if($remedialItemDetails["version"]=="html5") { ?>
// Create IE + others compatible event handler
var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
var activityFormat	=	'<?=$activityFormat?>';
var currentLevel	=	1;
var currentPos		=	0;
var scriptTimer		=	0;
// Listen to message from child window
eventer(messageEvent,function(e) {
  var dataReceived = e.data;
  if(dataReceived.indexOf('#@') === -1)
  {
    var arrData = dataReceived.split("||");
  }
  else
  {
  	var arrData = dataReceived.split("#@");
  }
<?php  if($activityFormat=="old") { ?> 
  
	var flag = arrData[1];
	var noOfPrompts = arrData[3];
	var time = arrData[0];
	var learningGoalStatus =  arrData[2];
	var endSessionTimeout = arrData[4];
	//for new
  	var extraParams,levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken;
<?php } else { ?>
  
	var flag = arrData[1];
	var extraParams =  arrData[0];
	var levelsAttempted	=	arrData[2];
	var levelWiseStatus	=	arrData[3];
	var levelWiseScore	=	arrData[4];
	var levelWiseTimeTaken	=	arrData[5];
	var endSessionTimeout = arrData[6];
	//for old remedial  
	var noOfPrompts,time,learningGoalStatus;
<?php } ?>

if(endSessionTimeout == "true")
{
	jQuery.ajax({
		type: "POST",
		url: "/mindspark/userInterface/controller.php?mode=endSessionType&endType=4",
		"async": false,
		success: function(msg) {
			tryingToUnloadPage = true;
			alert("You have been logged out as Mindspark has not detected any input from you in the last 10 minutes. Login again to continue.");
			window.location.href = "/mindspark/userInterface/error.php";
		}
	});
}
//-----  
//console.log(extraParams+"-"+levelsAttempted+"-"+levelWiseStatus+"-"+levelWiseScore+"-"+levelWiseTimeTaken);
  recieveTextFromFlash( time,flag, learningGoalStatus, noOfPrompts,levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, extraParams);
  <?php if($userType=="msAsStudent") echo "showParameters(time, noOfPrompts, learningGoalStatus, flag, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, extraParams);";?>
  if(flag!=0)
  {
    	redirect=0;
		alert('You have completed this exercise!');
		<?php if($userType=="msAsStudent") { ?>
		document.getElementById('frmRemedialItem').action='dashboard.php';
		<?php } ?>
		setTryingToUnload();
		document.getElementById('frmRemedialItem').submit();
  }
  else
  {
		if(activityFormat=="new")
		{
			var arrLevel	=	levelsAttempted.split("|");
			var arrStatus	=	levelWiseStatus.split("|");
			var arrScore	=	levelWiseScore.split("|");
			var arrTime		=	levelWiseTimeTaken.split("|");
			var arrExtraParams	=	extraParams.split("|");
			scriptTimer++;

			<?php if($userType!="msAsStudent") { ?>
					if(arrStatus[currentPos]==0 && currentLevel>0 && scriptTimer==12)
					{	
						saveGameResult(arrExtraParams[currentPos], arrLevel[currentPos], arrStatus[currentPos], arrScore[currentPos], arrTime[currentPos], currentLevel);
						scriptTimer=0;
					}
			<?php } ?>
			if(arrStatus[currentPos]!=0 && currentLevel>0)
			{
				<?php if($userType!="msAsStudent")
							echo "saveGameResult(arrExtraParams[currentPos], arrLevel[currentPos], arrStatus[currentPos], arrScore[currentPos], arrTime[currentPos], currentLevel);";?>
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
		self.setTimeout("checkRemedialCompleteHTML5()", 5000);
		/*if(levelWiseStatus!="" && levelWiseStatus.indexOf('0')==-1){
		    redirect=0;
		    document.getElementById('result').value=1;
		    alert('You have completed this exercise!');
		    <?php if($userType=="msAsStudent") { ?>
		    document.getElementById('frmRemedialItem').action='dashboard.php';
		    <?php } ?>
			setTryingToUnload();
		    document.getElementById('frmRemedialItem').submit();
		}*/
  }  

},false);

<?php  } ?>
function  checkRemedialCompleteHTML5()
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
		setTryingToUnload();
    	document.getElementById('mode').value = 1;
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
    				document.getElementById('frmRemedialItem').action='endSessionReport.php';
        			document.getElementById('frmRemedialItem').submit();
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

function saveGameResult(extraParams,levelsAttempted,levelWiseStatus,levelWiseScore,levelWiseTimeTaken,currentLevel)
{
	var params="";
	params += "mode=saveRmedialLevel";
	params += "&gameAttempt_srno=" + document.getElementById('remedialAttemptID').value;
	params += "&extraParams=" + extraParams;
	params += "&levelsAttempted=" + levelsAttempted;
	params += "&levelWiseStatus=" + levelWiseStatus;
	params += "&levelWiseScore=" + levelWiseScore;
	params += "&levelWiseTimeTaken=" + levelWiseTimeTaken;
	params += "&currentLevel=" + currentLevel;
	params += "&activityFormat=<?=$activityFormat?>";
	params += "&sessionID=<?=$sessionID?>";
	params += "&type=remedial";
	var request = new Ajax.Request('saveGameDetailsAjax.php',
		{
			method:'post',
			parameters: params,
			onSuccess: function(transport)
			{
				resp = transport.responseText;
			},
			onFailure: function()
			{
				//alert('Something went wrong while saving...');
			}
		}
		);
}

function showParameters(time, noOfPrompts, learningGoalStatus, flag, levelsAttempted, levelWiseStatus, levelWiseScore, levelWiseTimeTaken, extraParams)
{
<?php if($activityFormat=="old") { ?>	
	jQuery("#devPrompt").text(noOfPrompts);
	jQuery("#devTime").text(time);
	jQuery("#devlearningGoalStatus").text(learningGoalStatus);
<?php } else { ?>	
	jQuery("#devlevelsAttempted").text(levelsAttempted);
	jQuery("#devlevelWiseStatus").text(levelWiseStatus);
	jQuery("#devlevelWiseScore").text(levelWiseScore);
	jQuery("#devlevelWiseTimeTaken").text(levelWiseTimeTaken);
	jQuery("#devExtra").text(extraParams);
<?php } ?>	
	jQuery("#devComplete").text(flag);
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
<body onLoad="load()" class="translation">
<form name="frmRemedialItem" id="frmRemedialItem" method="post" action="controller.php" autocomplete='off'>
<input type="hidden" name="remedialItemCode" id="remedialItemCode" value="<?=$remedialItemCode?>">
<input type="hidden" name="result" id="result" value="0">
<input type="hidden" name="totalTimeTaken" id="totalTimeTaken" value="0">
<input type="hidden" name="learningGoalStatus" id="learningGoalStatus" value="0">
<input type="hidden" name="noOfPrompts" id="noOfPrompts" value="0">
<input type="hidden" name="fromSDL" id="fromSDL" value="<?=$sdl?>">
<input type="hidden" name="mode" id="mode" value="saveremedialItemStatus">
<input type="hidden" name="version" id="version" value="<?=$remedialItemDetails["version"]?>">
<input type="hidden" name="activityFormat" id="activityFormat" value="<?=$activityFormat?>">
<input type="hidden" name="levelsAttempted" id="levelsAttempted" value="0">
<input type="hidden" name="levelWiseStatus" id="levelWiseStatus" value="0">
<input type="hidden" name="levelWiseScore" id="levelWiseScore" value="0">
<input type="hidden" name="levelWiseTimeTaken" id="levelWiseTimeTaken" value="0">
<input type="hidden" name="extraParameters" id="extraParameters" value="0">
<input type="hidden" name="noOfLevels" id="noOfLevels" value="<?=$noOfLevels?>">
<input type="hidden" name="remedialAttemptID" id="remedialAttemptID" value="<?=$remedialAttemptID?>">

	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name[0]?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a onClick="javascript:setTryingToUnload();" href='logout.php' ><span data-i18n="common.logout"></span></a></li>
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
        <div id="logout" class="linkPointer hidden" onClick="logoff()">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="topic"><!-- onClick="hideBar();">-->
				<div id="home" class="linkPointer" <?php if($homeLinkActive==1){ ?> onClick="getHome()"<?php } ?>></div>
				<div class="icon_text1"><span onClick="getHome()" class="textUppercase linkPointer" <?php if($theme==2){ ?> data-i18n="dashboardPage.home"<?php } ?>></span> > <font color="#606062"> <span class="textUppercase linkPointer" data-i18n="homePage.activity"  <?php if($homeLinkActive==1){ ?> onClick="javascript:window.location.href='activity.php'"<?php } ?>></span></font><font color="#606062"><span class="forHigherOnly"> : <?=$gameDetails["desc"]?></span></font></div>
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
				<strong><?=$Name[0]?></strong>
			</div>
			<div id="new" onClick="endSession()">
				<div class="icon_text textUppercase" data-i18n="questionPage.endSession"></div>
				<div id="pointed">
				</div>
			</div>
		</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly">
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.activity"></span></div>
                </div><div class="arrow-right"></div>
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
<?php if($userType=="msAsStudent") { ?>
        <div id="devParameter" style="float:left;<?php if($theme==3){ echo "margin-left:15%;";} ?>">
        <h5>Parameters</h5>
        <?php if($activityFormat=="old") { ?>
            <span><b>Prompts:</b></span>&nbsp;<span id="devPrompt">0</span><br>
            <span><b>Time Taken:</b></span>&nbsp;<span id="devTime">0</span><br>
            <span><b>Learning Goal:</b></span>&nbsp;<span id="devlearningGoalStatus">0</span><br>
        <?php } else { ?>
            <span><b>Levels Attempted:</b></span>&nbsp;<span id="devlevelsAttempted">0</span><br>
            <span><b>Level Wise Status:</b></span>&nbsp;<span id="devlevelWiseStatus">0</span><br>
            <span><b>Level Wise Score:</b></span>&nbsp;<span id="devlevelWiseScore">0</span><br>
            <span><b>Levle Wise Time Taken:</b></span>&nbsp;<span id="devlevelWiseTimeTaken">0</span><br>
            <span><b>Extra Params:</b></span>&nbsp;<span id="devExtra">0</span><br>
        <?php } ?>
            <span><b><?php if($activityFormat=="old") echo "Result:"; else echo "Completed:"; ?></b></span>&nbsp;<span id="devComplete">0</span>
        </div>
<?php } ?>

        <div class="gameswf" style="z-index:1;">
			<?php if($remedialItemDetails["version"]=="html5") { ?>
                <iframe id="iframe" src="<?=$flashFile?>" height="<?=$height?>px" width="<?=$width?>px" frameborder="0"></iframe>
            <?php } else  {?>
                <OBJECT id="remedialItemSwf" height="<?=$height?>px" width="<?=$width?>px" classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
                <param name="movie" value="<?=$flashFile?>">
                <PARAM NAME="quality" VALUE="high">
                <param name="allowScriptAccess" value="always" />
                <PARAM name='menu' VALUE='false'>
                <PARAM name='wmode' VALUE='transparent'>
                    <EMBED src="<?=$flashFile?>" swliveconnect="true" WMODE="transparent" quality=high allowScriptAccess="always"	WIDTH="<?=$width?>px" HEIGHT="<?=$height?>px" NAME="remedialItemSwf" id="remedialItemSwf" ALIGN="center" menu="false" type="application/x-shockwave-flash"></EMBED>
                </OBJECT>
            <?php } ?>
        </div>
		</div>
</form>

<div id="tagMsgBox" style="position: fixed; right:90px; bottom:90px; background-color: #00FFFF;width: 230px;padding: 10px;color: black;border: #0000cc 2px dashed;display: none;">
    <table>
        <tr><td><span id="showTaggedQcode"></span><br><strong>Comment:</strong></td></tr>
        <tr><td><textarea rows="4" cols="25" id="tagComment" name="tagComment"></textarea><input type="hidden" name="tagQcode" id="tagQcode" value=""></td></tr>
        <tr><td align="center"><input type="submit" id="tagComentSave" name="tagCommentSave" value="Save"><input type="button" id="closeBox" name="closeBox" value="Close" onClick="showTagBox('tagMsgBox', 'none', '');"></td></tr>
    </table>
</div>
<script>
/*checking activity file for offline*/  
	<?php if(($_SESSION['offlineStatus']==3 || $_SESSION['offlineStatus']==4 || $_SESSION['offlineStatus']==7) && !file_exists(realpath(dirname(__FILE__) . '/../html5')."/remedialItems/".$remedialItemCode."/src/index.html")) { ?>
		redirect=0;
		setTryingToUnload();
		jQuery("#result").val("1");
		document.getElementById('frmRemedialItem').submit();
	<?php } ?>
/*checking activity file for offline*/
</script>
	
<?php include("footer.php");?>

<?php 
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

function getRemedialOwner($remedialItemCode)
{
	$sq	=	"SELECT A.owner1,A.owner2,D.owner1 FROM adepts_topicMaster A , adepts_subTopicMaster B , adepts_clusterMaster C , adepts_remedialItemMaster D 
			 WHERE B.subTopicCode=C.subTopicCode AND A.topicCode=B.topicCode AND C.clusterCode=D.linkedToCluster AND D.remedialItemCode='$remedialItemCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]!="")
		return $rw[0];
	else if($rw[1]!="")
		return $rw[1];
	else if($rw[2]!="")
		return $rw[2];
}

function getLastAttemptData($remedialItemCode,$userID,$noOfLevels,$gameType)
{
	$sq	=	"SELECT remedialAttemptID,result,clusterAttemptID FROM adepts_remedialItemAttempts WHERE remedialItemCode='$remedialItemCode' AND userID=$userID ";
	if(isset($_SESSION["clusterAttemptID"]))
		$sq	.=	" AND clusterAttemptID=".$_SESSION["clusterAttemptID"];
	else
		$sq	.=	" AND clusterAttemptID=0";
	$sq	.=	" ORDER BY remedialAttemptID DESC LIMIT 1";
	$rs	=	mysql_query($sq);
	if($rw=mysql_fetch_array($rs))
	{
		if($rw["result"]==0)
		{
			$sqLevel	=	"SELECT level,status FROM adepts_activityLevelDetails WHERE srno=".$rw["remedialAttemptID"]." AND status=0 AND type='$gameType' ORDER BY id LIMIT 1";
			$rsLevel	=	mysql_query($sqLevel);
			if($rwLevel=mysql_fetch_array($rsLevel))
				return ($rwLevel[0]-1)."~".$rw["remedialAttemptID"];
			else
			{
				$sqError	=	"UPDATE adepts_remedialItemAttempts SET result=1 WHERE remedialAttemptID=".$rw["remedialAttemptID"]." AND userID=".$userID;
				$rsError	=	mysql_query($sqError);
				$sqAttempt	=	"SELECT qcode FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE clusterAttemptID=".$rw["clusterAttemptID"]." ORDER BY srno DESC LIMIT 1";
				$rsAttempt	=	mysql_query($sqAttempt);
				$rwAttempt	=	mysql_fetch_array($rsAttempt);
				$sqStatus	=	"UPDATE ".TBL_CURRENT_STATUS." SET qcode=".$rwAttempt[0].",remedialMode=0 WHERE userID=$userID AND clusterAttemptID=".$rw["clusterAttemptID"];
				$rsStatus	=	mysql_query($sqStatus);
				sendDataCheckMail("Problem in remedial ".$remedialItemCode." result is 0","remedial","","",$remedialItemCode);
				header("location:home.php");
				exit();
			}
		}
		else if($rw["result"]==1 && $_SESSION["userType"]!="msAsStudent")
		{
			if($_SESSION["subModule"]!="")
			{
			
				$_SESSION['game'] = false;
				if($remedialItemAttemptID!="")
					$activityAttemptID	=	$remedialItemAttemptID;
				else
					$activityAttemptID	=	$remedialAttemptID;
				$sq	=	"UPDATE educatio_adepts.kst_userFlowDetails SET activityAttemptID=$rw[0] ,timeTaken='', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"]." and moduleType='remedial'";
				$rs	=	mysql_query($sq) or die(mysql_error().$sq);
				$sqSetCurrentActivityDetail	=	"UPDATE educatio_adepts.kst_ModuleAttempt SET currentActivityDetail='' WHERE srno=".$_SESSION['subModule_srno'];
				mysql_query($sqSetCurrentActivityDetail);
				$_SESSION["currentSDL"] = "";
				
				getNextKstSubModuleInflow();
				redirectToKstFlow();
			
				if($_SESSION['subModule']=="")
				{
					$nextPage = "question.php";
					$qcode = -1;
					$quesno = $_SESSION["qno"];
					echo '<html>';
					echo '<body>';
					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
					echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
					echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
					echo '<input type="hidden" name="quesCategory" id="quesCategory" value="normal">';
					echo '<input type="hidden" name="showAnswer" id="showAnswer" value="1">';
					echo '<input type="hidden" name="mode" id="mode" value="">';
					echo '</form>';
					echo "<script>
							 document.getElementById('frmHidForm').submit();
						  </script>";
					echo '</body>';
					echo '</html>';
				}
			} else if($_SESSION["comprehensiveModule"]!="") {

				$_SESSION['game'] = false;
				if($remedialItemAttemptID!="")
					$activityAttemptID	=	$remedialItemAttemptID;
				else
					$activityAttemptID	=	$remedialAttemptID;
				$sq	=	"UPDATE adepts_userComprehensiveFlow SET activityAttemptID=$rw[0] ,timeTaken='', status=1 WHERE flowAttemptID=".$_SESSION["currentFlowID"]." and moduleType='remedial'";
				$rs	=	mysql_query($sq) or die(mysql_error().$sq);
				getNextComprehensiveInflow();
				redirectToFlow();
				if($_SESSION['comprehensiveModule']=="")
				{
					$nextPage = "question.php";
					$qcode = -1;
					$quesno = $_SESSION["qno"];
					echo '<html>';
					echo '<body>';
					echo '<form id="frmHidForm" action="'.$nextPage.'" method="post">';
					echo '<input type="hidden" name="qcode" id="qcode" value="'.$qcode.'">';
					echo '<input type="hidden" name="qno" id="qno" value="'.$quesno.'">';
					echo '<input type="hidden" name="quesCategory" id="quesCategory" value="normal">';
					echo '<input type="hidden" name="showAnswer" id="showAnswer" value="1">';
					echo '<input type="hidden" name="mode" id="mode" value="">';
					echo '</form>';
					echo "<script>
							 document.getElementById('frmHidForm').submit();
						  </script>";
					echo '</body>';
					echo '</html>';
				}
			}
			else
			{
				$sqAttempt	=	"SELECT qcode FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE clusterAttemptID=".$rw["clusterAttemptID"]." ORDER BY srno DESC LIMIT 1";
				$rsAttempt	=	mysql_query($sqAttempt);
				$rwAttempt	=	mysql_fetch_array($rsAttempt);
				$sqStatus	=	"UPDATE ".TBL_CURRENT_STATUS." SET qcode=".$rwAttempt[0].",remedialMode=0 WHERE userID=$userID AND clusterAttemptID=".$rw["clusterAttemptID"];
				$rsStatus	=	mysql_query($sqStatus) or die(mysql_errno().$sqStatus);
				sendDataCheckMail("Problem in remedial ".$remedialItemCode." result is 1 and remedial mode is on","remedial","","",$remedialItemCode);
				header("location:home.php");
				exit();
			}
		}
		else 
		{
			return -1;
		}
	}
	else
	{
		return -1;
	}
}

function insertAttemptDetails($userID, $remedialItemCode, $sessionID, $fromSDL, $clusterAttemptID)
{
	$query	=	"INSERT INTO adepts_remedialItemAttempts (userID, sessionID, remedialItemCode, clusterAttemptID, result, timeTaken, fromSDL)
				 VALUES ($userID, '$sessionID', '$remedialItemCode', '$clusterAttemptID', 0, 0, '$fromSDL')";
    $result = mysql_query($query) or die(mysql_error());
    $remedialAttemptID = mysql_insert_id();
    return $remedialAttemptID;
}

function insertLevelDetails($remedialAttemptID,$noOfLevels, $activityType,$userID)
{
	for($i=1;$i<=$noOfLevels;$i++)
	{
		//$sq	=	"INSERT INTO adepts_activityLevelDetails SET srno=$remedialAttemptID,type='$activityType',status=0,level=$i";
		$sq	=	"INSERT INTO adepts_activityLevelDetails SET srno=$remedialAttemptID,type='$activityType',status=0,level=$i,userID=$userID";
		$rs	=	mysql_query($sq);
	}
}
?>
