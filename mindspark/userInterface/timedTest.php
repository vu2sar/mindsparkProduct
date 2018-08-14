<?php

include("check1.php");
include_once("functions/orig2htm.php");
include_once("functions/functionsForDynamicQues.php");
include('classes/clsTimedTest.php');
include_once("constants.php");
error_reporting(E_ERROR);

if(!isset($_SESSION['userID']))
{
	echo "Your session has expired! Kindly login again<br/>";
	exit;
}
$userID = $_SESSION['userID'];
$Name = explode(" ",$_SESSION['childName']);
$childClass  = $_SESSION['childClass'];
$teacherTopicName = $_SESSION['teacherTopicName'];
$comprehensiveModuleFlag = $_SESSION['comprehensiveModule'];
$qcode = $currQues = $quesCategory = $showAnswer = $tmpMode = $childClass = "";
if(isset($_POST['qcode']))
{
	$qcode 		  = $_POST['qcode'];
	$currQues 	  = $_POST['qno'];
	$quesCategory = $_POST['quesCategory'];
	$showAnswer   = $_POST['showAnswer'];
}
//echo $quesCategory;die;
if($quesCategory!="")
	$quesCategory	=	$_SESSION["quesCategory"];
else
	$_SESSION["quesCategory"]	=	$quesCategory;
	
//echo $_SESSION["quesCategory"];die;
if(isset($_REQUEST['tmpMode']))
	$tmpMode 	  = $_REQUEST['tmpMode'];
$remedialMode =  isset($_SESSION['remedialMode'])?$_SESSION['remedialMode']:0;

if(isset($_REQUEST['timedTest']))
	$timedTestCode = $_REQUEST['timedTest'];
elseif(isset($_SESSION['timedTest']))
	$timedTestCode = $_SESSION['timedTest'];
if (isset($_GET['class']))
	$childClass = $_GET['class'];
elseif(isset($_SESSION['childClass']))
	$childClass = $_SESSION['childClass'];
if($childClass=="")
    $childClass = 5;
if(isset($_GET['mode']))
	$tmpMode = $_GET['mode'];


if($tmpMode!="sample" && $quesCategory!="wildcard" && $quesCategory!="comprehensive")
{
    $attemptNo = getTimedTestAttemptNo($userID,$timedTestCode);
    $noOfSparkies = getNoOfSparkies($attemptNo);
    $sparkieMsg = "You will get ";
    if($childClass<8 || $_SESSION['rewardSystem']==1)
    {
        $sparkieMsg .= $noOfSparkies;
        if($noOfSparkies==1)    $sparkieMsg.=" Sparkie ";
        else $sparkieMsg .= " Sparkies ";
    }
    else
    {
        $sparkieMsg .= $noOfSparkies*10;
        $sparkieMsg .= " reward points ";
    }
    $sparkieMsg   .= " if you finish this timed test successfully!!";
}

$query  = "SELECT duration_cl$childClass as duration, noOfQues_cl$childClass as noOfQues, quesTypes, noOfColumns, instruction, hint, fontname, fontsize, alignment, dynamic,description,timedTestVersion,autoSubmit FROM adepts_timedTestMaster WHERE timedTestCode='$timedTestCode'";

$result = mysql_query($query);
$line   = mysql_fetch_array($result);
$totalQues 		= $line['noOfQues'];
$duration  		= $line['duration'];
$quesTypeCodesArray = explode(",",$line['quesTypes']);
$noOfColumns 	= $line['noOfColumns'];
$fontName       = $line['fontname'];
$fontSize		= $line['fontsize'];
$instruction    = $line['instruction'];
$hint    		= $line['hint'];
$align          = $line['alignment'];
$dynamic        = $line['dynamic'];
$desc           = $line['description'];
$timedTestVersionStr	=	$line['timedTestVersion'];
$autoSubmit	=	$line['autoSubmit'];
$redirectToGame = 0;
$arraySize = 0;
if($timedTestVersionStr=="")
{
	$timedTestVersion=2;
}
else
{
	$timedTestVersionArray	=	explode(",",$timedTestVersionStr);
	$timedTestVersion	=	$timedTestVersionArray[array_rand($timedTestVersionArray)];
}
if ($timedTestVersion==1){  //additional check to prevent version 1
	$timedTestVersion=2;
}
if($_SESSION['clusterCode']!="" || $_SESSION['classSpecificClustersForTT']!="")
{

	foreach($_SESSION['classSpecificClustersForTT'] as $key=>$val)
	{
		if($val[0]==$_SESSION['clusterCode'])
		$arrPos = $key;
		$arraySize++;
		
		if($_SESSION['classSpecificClustersForTT'][$key][2]!='timedTest' && $_SESSION['classSpecificClustersForTT'][$key][2]!='activity')
		$lastCluster = $_SESSION['classSpecificClustersForTT'][$key][0];
	}

	$currentCluster = $_SESSION['classSpecificClustersForTT'][$arrPos][0];

	if(($_SESSION['classSpecificClustersForTT'][$arraySize-2][2]=='activity' && $_SESSION['classSpecificClustersForTT'][$arraySize-1][2]=='timedTest') && $lastCluster==$currentCluster)
	{
			$redirectToGame = 1;
	}
	else
	{
		if($arrPos-2>=0)
		if($_SESSION['classSpecificClustersForTT'][$arrPos-1][2]=="timedTest" && $_SESSION['classSpecificClustersForTT'][$arrPos-2][2]=="activity")
		{
			$redirectToGame = 1;
		}
	}
	
}	



//$childClass=3;
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
?>

<?php include("header.php");?>

<title>Timed Test</title>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>-->

<script type="text/javascript"  src="libs/prototype.js"></script>
<script type="text/javascript" src="libs/swfobject.js"></script>
<script type="text/javascript" src="/mindspark/js/load.js"></script>
<script type="text/javascript" src="libs/timedTest/timedtest.js?ver=72"></script>
<!--<script src="libs/closeDetection.js"></script>-->
<?php if( $iPad ==true|| $Android ==true){?>
	 <link type="text/css" href="libs/question/keypad2/jquery.keypad.css" rel="stylesheet">
     <link type="text/css" href="libs/question/keypad2/keypadCustomStyle.css?q" rel="stylesheet">
     <script type="text/javascript" src="libs/question/keypad2/jquery.keypad.js?1"></script>
     <script type="text/javascript" src="libs/question/keypad2/keypad2.js?1"></script>
<?php }?>
<script>
var androidVersionCheck=0;
jQuery(document).ready(function(e) {
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		jQuery(".translation").i18n();
	});
	var ua = navigator.userAgent;
	if( ua.indexOf("Android") >= 0 )
	{
	  var androidversion = parseFloat(ua.slice(ua.indexOf("Android")+8)); 
	  if (androidversion < 3.2)
	  {
		  androidVersionCheck=1;
	  }
	  else{
		androidVersionCheck=0;
	  }
	}
     if(osDetection() && !androidVersionCheck){
            attachKeypad('timedTest');
        }
});
function osDetection() {

    return (
        (navigator.userAgent.indexOf("iPhone") != -1) ||
        (navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1) || (navigator.userAgent.indexOf("Android") != -1)
    );
}
var rewardSystem=<?php echo json_encode($_SESSION['rewardSystem']); ?>;
</script>
<?php 
if (isset($_REQUEST['theme'])) 
	$theme	=	$_REQUEST['theme'];
if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/timedTest/lowerClass.css" rel="stylesheet" type="text/css">
	<script>
		function load11(){
		}
		jQuery(document).ready(function(e) {
			jQuery(".forHigherOnly,.forHighestOnly").remove();	
		});
	</script>
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/timedTest/midClass.css" />
	<link rel="stylesheet" href="css/commonMidClass.css" />
	<script>
		function load11(){
		}
		jQuery(document).ready(function(e) {
			jQuery(".forLowerOnly,.forHighestOnly").remove();	
		});
		if (window.frameElement) {
			setTryingToUnload();
			jQuery("head").append('<link href="css/question/iframe.css" rel="stylesheet" type="text/css">');
		}
	</script>
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/timedTest/higherClass.css" />
	<script>
		jQuery(document).ready(function(e) {
			jQuery(".hidden").remove();	
		});
		function load11(){
			jQuery(".forHigherOnly,.forLowerOnly").remove();	
			var html="";
			html+='<div id="timdTestFeedback"><div id="scoreDiv" ';
			html+='><span id="spnResultMsg1"></span></div>';
			html+='<div id="feedbackLink" class="hidden" onClick="showCommentBox()" data-i18n="timedTestPage.feedback">Feedback</div><div id="feedbackExplain" ';
			html+='><span id="spnResultMsg2"></span></div><div class="clear"></div><div id="commentMain"><div id="commentBoxDiv"><textarea id="comment" name="comment" name="comment" cols="50" rows="5"></textarea></div><div class="textUppercase button1" id="submitBtn" data-i18n="common.submit" onClick="saveTimedTestComment(1);">Submit</div><div class="textUppercase button1" onClick="hideCommentBox()" id="cancelBtn" data-i18n="common.cancel">Cancel</div></div></div><div class="clear"></div>';
			jQuery('#insertDiv').html(html);
		}
	</script>
<?php } 
if($timedTestVersion==2) { ?>
	<link href="css/timedTest/styleVersion_2.css?ver=1" rel="stylesheet" type="text/css">
<?php } 
if($timedTestVersion==3) { ?>
	<link href="css/timedTest/styleVersion_3.css?ver=1" rel="stylesheet" type="text/css">
<?php }?>
<style>
	.timedTestRadio{
		display: none;
	}
	.timedTestRadio+label{
		width: 25px;
		height: 25px;
		font-size: 16px;
		/* color: #FFFFFF; */
		display: inline-block;
		line-height: 25px;
		background-color: transparent;
		border: 2px solid white;
		border-radius: 30px;
		text-align: center;
		vertical-align: middle;
	}

	.timedTestRadio:checked+label,.timedTestRadio+label:active {
	    background-color: black;
	    color: white;
	}
	#tblWorkSheetMain input[type=radio]+label{
		cursor: pointer;
	}
	#tblWorkSheetMain.ver3 .timedTestRadio+label{
		border: 2px solid black;
	}
	#tblWorkSheetMain label,#divUserResponse label{
		margin-right: 10px;
		border-radius: 30px;
	}
	p>label:active{
		background: rgba(128, 128, 128, 0.5);
	}
</style>
<script>
	var langType = "<?=$language?>";
	function load(){
		<?php if(isset($_SESSION['rewardSystem'])) echo "var rewardSystem=".$_SESSION['rewardSystem'].";"; else { echo "var rewardSystem=0;";$_SESSION['rewardSystem']=0; } ?>
		<?php if($theme==3) { ?>	
		jQuery('#sideBar').css("height",jQuery('#scrollContainerForLower').css("height"));
		jQuery('#menuBar').css("height",jQuery('#scrollContainerForLower').css("height"));
		<?php } ?>
		<?php if($theme==1) { ?> 
			jQuery(".forHigherOnly").remove(); 
		<?php } else { ?>
			jQuery(".forLowerOnly").remove();
		<?php } ?>
		
	}
	jQuery(document).ready(function(e) {
		if (window.location.href.indexOf("localhost") > -1) {	
		    var langType = 'en-us';
		}
		<?php if(isset($_REQUEST['practiseModule'])){ ?> setTimeout(function(){jQuery("#prmptContainer").remove();},1000); <?php } ?>
		i18n.init({ lng: langType,useCookie: false }, function(t) {
			jQuery(".translation").i18n();
			jQuery(document).attr("title",i18n.t("timedTestPage.title"));
			<?php if($theme==1 || $theme==2 || $_SESSION["rewardSystem"]==1) { ?>
			jQuery("#sparkieMsg").html(i18n.t("timedTestPage.sparkieInfo", { noOfSparkie: jQuery("#sparkieMsg").text(), rewardType : '<img src="assets/lowerClass/icon.sparkie.png" width="15px" height="15px">' }));
			<?php } else { ?>
			jQuery("#sparkieMsg").html(i18n.t("timedTestPage.sparkieInfo", { noOfSparkie: jQuery("#sparkieMsg").text(), rewardType : 'reward points' }));<?php
			} ?>
		});
	});
	var infoClick=0;
	function hideBar(){
		if (infoClick==0){
			jQuery("#hideShowBar").text("+");
			jQuery('#homeIcon').fadeOut(300);
			jQuery('#endSession').fadeOut(300);
			jQuery('#clock').fadeOut(300);
			jQuery('#session').animate({'margin-left':'8%','margin-top':'-78px'},600);
			jQuery('#duration').animate({'margin-left':'20%','margin-top':'-78px'},600);
			jQuery('#timeRemaining').animate({'margin-top':'-3px'},600);
			jQuery('#totalQuestion').animate({'margin-left':'50%','margin-top':'-11px'},600);
			jQuery('#info_bar').animate({'height':'60px'},600);
			var a= window.innerHeight - (20 + 19 + 280 );
			var b= window.innerHeight - (20 + 140 );
			jQuery('#timedTestContainer').animate({'min-height':a+'px'},600);
			jQuery('#timedTestDataDivMain').animate({'height':b+'px'},600);
			jQuery('#endSession').fadeOut(300);
			infoClick=1;
		}
		else if(infoClick==1){
			jQuery("#hideShowBar").text("-");
	        jQuery('#homeIcon').fadeIn(300);
			jQuery('#endSession').fadeIn(300);
			jQuery('#clock').fadeIn(300);
			jQuery('#session').animate({'margin-left':'7%','margin-top':'0px'},600);
			jQuery('#duration').animate({'margin-left':'7%','margin-top':'0px'},600);
			jQuery('#timeRemaining').animate({'margin-top':'0px'},600);
			jQuery('#totalQuestion').animate({'margin-left':'0%','margin-top':'0px'},600);
			jQuery('#info_bar').animate({'height':'140px'},600);
			var a= window.innerHeight - (80 + 19 + 280 );
			var b= window.innerHeight - (80 + 17 + 140 );
			jQuery('#timedTestContainer').animate({'min-height':a+'px'},600);
			jQuery('#timedTestDataDivMain').animate({'height':b+'px'},600);
			infoClick=0;
		}
	}

	var message="Sorry, right-click has been disabled";

	function clickIE() {if (document.all) {(message);return false;}}
	function clickNS(e) {
	    if(document.layers||(document.getElementById&&!document.all)) {
	        if (e.which==2||e.which==3) {(message);return false;}
	    }
	}
	if (document.layers){
	    document.captureEvents(Event.MOUSEDOWN);document.onmousedown=clickNS;
	}
	else {
	    document.onmouseup=clickNS;
	    document.oncontextmenu=clickIE;
	}
	document.oncontextmenu=new Function("return false");

	function checkEnter(e) {
		if(!jQuery.browser.mozilla)
		{
			e = e || event;
			return (e.keyCode || event.which || event.charCode || 0) !== 13;
		}
	}
	function logoff()
	{
		setTryingToUnload();
		window.location="logout.php";
	}

	window.history.forward();
	function noBack() { window.history.forward(); }
</script>
<style>
	input.supscpt {
		position:relative;
		top:-15px;
		width: 40px!important;
		height: 20px!important;
		font-size:20px!important;
	}
	input.base{
		width: 15px;
	}
	.flashHide {
	  left:-99999px;
	}
</style>
</head>

<body class="translation" onpageshow="if (event.persisted) noBack();" onload="noBack();Init();load11();nextQues('','');" >

<?php if($quesCategory=="wildcard") {
if($theme==3) {
	$msg	=	"Here is an opportunity to earn 30 reward points by answering a wild card timed test!!! A wild card timed test may contain questions that are not related to the topic that you are currently doing. These questions are asked to test your alertness and ability to answer questions from other topics. You will get 30 reward points for answering more than 75% of the questions from the wild card timed test.";
?>
	<div align="center" id="pnlWC" name="pnlWC" style="display:none"><img id="wildcardImg" src='images/wildcardtimedtest.png' alt='Wild card Question' align='center' title="Here is an opportunity to earn 30 reward points by answering a wild card timed test!!! A wild card timed test may contain questions that are not related to the topic that you are currently doing. These questions are asked to test your alertness and ability to answer questions from other topics. You will get 30 reward points for answering more than 75% of the questions from the wild card timed test."></div>
	<div id="wildcardInfo" title="Wildcard Information" style="display: none;">
    <table border="0" bgcolor="#DEB887" cellpadding="5" cellspacing="3">
        <tr bgcolor="#FFD39B">
            <th>Here is an opportunity to earn 30 reward points by answering a wild card timed test!!!</th>
        </tr>
        <tr bgcolor="#FFD39B">
            <th>A wild card timed test may contain questions that are not related to the topic that you are currently doing. These questions are asked to test your alertness and ability to answer questions from other topics. You will get 30 reward points for answering more than 75% of the questions from the wild card timed test.</th>
        </tr>
    </table>
    </div>
<?php } else { 
	$msg	=	"Here is an opportunity to earn 3 full Sparkies by answering a wild card timed test!!! A wild card timed test may contain questions that are not related to the topic that you are currently doing. These questions are asked to test your alertness and ability to answer questions from other topics. You will get 3 Sparkies for answering more than 75% of the questions from the wild card timed test. Remember that wild card questions will not affect your performance in the regular topics.";
?>
	<div align="center" id="pnlWC" name="pnlWC" style="display:none"><img id="wildcardImg" src='images/wildcardtimedtest.png' alt='Wild card Question' align='center' title="Here is an opportunity to earn 3 full Sparkies by answering a wild card timed test!!! A wild card timed test may contain questions that are not related to the topic that you are currently doing. These questions are asked to test your alertness and ability to answer questions from other topics. You will get 3 Sparkies for answering more than 75% of the questions from the wild card timed test. Remember that wild card questions will not affect your performance in the regular topics."></div>
    <div id="wildcardInfo" title="Wildcard Information" style="display: none;">
    <table border="0" bgcolor="#DEB887" cellpadding="5" cellspacing="3">
        <tr bgcolor="#FFD39B">
            <th>Here is an opportunity to earn 3 full Sparkies by answering a wild card timed test!!!</th>
        </tr>
        <tr bgcolor="#FFD39B">
            <th>A wild card timed test may contain questions that are not related to the topic that you are currently doing. These questions are asked to test your alertness and ability to answer questions from other topics. You will get 3 Sparkies for answering more than 75% of the questions from the wild card timed test. Remember that wild card questions will not affect your performance in the regular topics.</th>
        </tr>
    </table>
    </div>
<?php	
	}
} ?>
<? if($_SESSION['subModule'] != "" ) { ?>
	<form name="kstdiagnosticTest" id="kstdiagnosticTest" action="controller.php" method="post">
	<input type="hidden" name="ttCode" id="ttCode" value="<?=$_SESSION['teacherTopicCode']?>">
	<input type="hidden" name="mode" id="mode" value="ttSelection">
	<input type="hidden" name="isTimedTest" id="isTimedTest" value="1">
	<input type="hidden" name="kstAttemptNo" id="kstAttemptNo" value="<?=$_SESSION['topicAttemptNo']?>">
	</form>
<? } ?>
<form id="frmTimedTest" name="frmTimedTest" method="post" autocomplete='off' onKeyPress="return checkEnter(event)">
	<input type="hidden" name="qcode" id="qcode" value="<?=$qcode?>">
	<input type="hidden" name="qno" id="qno" value="<?=$currQues?>">
	<input type="hidden" name="refresh" id="refresh" value="0">
	<input type="hidden" name="quesCategory" id="quesCategory" value="<?=$quesCategory?>">
	<input type="hidden" name="showAnswer" id="showAnswer" value="<?=$showAnswer?>">
	<input type="hidden" name="tmpMode" id="tmpMode" value="<?=$tmpMode?>">
	<input type="hidden" name="mode" id="mode">
	<input type="hidden" name="noOfQuesAttempted" id="noOfQuesAttempted">
	<input type="hidden" name="remedialMode" id="remedialMode" value="<?=$remedialMode?>">
	<input type="hidden" name="attemptNo" id="attemptNo" value="<?=$attemptNo?>">
	<input type="hidden" name="cls" id="cls" value="<?=$childClass?>">
	<input type="hidden" name="timedTestVersion" id="timedTestVersion" value="<?=$timedTestVersion?>">
    <input type="hidden" name="duration" id="txtDuration" value="<?=$duration?>">
    <input type="hidden" name="autoSubmit" id="autoSubmit" value="<?=$autoSubmit?>">
	<input type="hidden" name="gameRedirection" id="gameRedirection" value="<?=$redirectToGame?>">
	<input type="hidden" name="comprehensiveModuleFlag" id="comprehensiveModuleFlag" value="<?=$comprehensiveModuleFlag?>"></input>
<?php
	if(isset($_REQUEST['practiseModule'])){
		echo '<input type="hidden" name="practiceModuleRedirection" id="practiceModuleRedirection" value="1">';
	}

	if($timedTestCode=="" )
	{
		if(!isset($_SESSION['userID']) || !isset($_POST['qcode']))
		{
			echo "You are not authorised to access this page!<br/>";
			echo "<a href='logout.php' onclick='javascript:setTryingToUnload();'>Click here</a> to login";
			exit;
		}
		else
		{
			echo "<script>redirect();</script>";
		}
	}
	
	if($duration=="")   //double check, not needed as such
		$duration = 10;
	//$duration	=	1;
	if($timedTestVersion==3 || $timedTestVersion==2)
	{
		//$totalQuesMax	=	40;
		$totalQuesMax	=	$totalQues;
	}
	else
	{
		$totalQuesMax	=	$totalQues;
	}
	
	if($dynamic)
	{
		$quesArray = getDynamicQues($timedTestCode,$totalQuesMax,$quesTypeCodesArray);
	}
	else 
	{
		$quesArray = getStaticQues($timedTestCode,$totalQues);
	}
	
	//print_r($quesArray); exit;
?>
	<div id="top_bar">
		<div class="logo">
		</div>
        
        <div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name[0]?>&nbsp;</span></a>
                                <ul>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>

		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
	
	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden" >
        	<div id="blankWhiteSpace"><div id="timedTestIcon"></div></div>
             <div id="home">
                <div id="homeIcon"></div>
                <div id="dashboardHeading" class="forLowerOnly"> - <span data-i18n="timedTestPage.timedTest"></span>
                <?php if($quesCategory=="wildcard") {?>:<span title="<?=$msg?>"> Wildcard</span> <?php } ?>
                </div>
                <div class="clear"></div>
            </div>
			<div id="linkBar" class="forLowerOnly hidden">
				<div id="endSessionDiv" class="lowerClassIcon linkPointer" onClick="endSession();">
                    <div class="endSessionDiv"></div>
                    <div class="quesLinkText" data-i18n="questionPage.endSession"></div>
                </div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden" >
			<div id="topic">
				<div id="home">
                	<div id="homeIcon"></div>
                    <div id="homeText"><span data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span data-i18n="timedTestPage.timedTest"></span></font>
                    <?php if($quesCategory=="wildcard") {?>:<font color="#606062" title="<?=$msg?>"> Wildcard</font> <?php } ?>
                    </div>
				</div>
                <div class="clear"></div>
			</div>
			<div class="class">
				<strong><span data-i18n="common.class"></span> </strong> <?=$childClass?>
			</div>
			<div class="Name">
				<strong><?=$Name[0]?></strong>
			</div>
            <div id="endSession" onClick="endSession();">
				<div class="icon_text" data-i18n="questionPage.endSession"></div>
				<div id="pointed"></div>
			</div>
            <div class="clear"></div>
            <div id="session">
                <span data-i18n="common.sessionID"></span> : <font color="#39a9e0"><?=$_SESSION["sessionID"]?></font>
            </div>
            <div class="clear"></div>
		</div>
        <div id="info_bar" class="forHighestOnly" onClick="load11();" >
			<div id="topic">
                <div id="home">
                	<a href="home.php" onClick="javascript:setTryingToUnload();">
                        <div id="homeIcon"></div>
                    </a>
                    <div id="homeText" class="hidden"><span class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>
				
				<a style="text-decoration:none;color:inherit"> <div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div></a>
				<div class="arrow-ss"></div>
                <div id="activatedAtHome" class="forLowerOnly">
                	<div id="activatedAtHomeIcon"></div>
					<div id="activatedAtHomeText"></div>
                    <div class="clear"></div>
                </div>
				<div id="timedTestText" class="forHighestOnly">
					<div id="timedTest"><font color="#E75903">&nbsp;&nbsp; <span data-i18n="timedTestPage.timedTest"></span></font> > <?=$teacherTopicName?><span></span></div>
				</div>
				<div id="timedTestVersion1DetailsUpper">
            	<div id="session">
                    <span data-i18n="common.sessionID"></span> : <font color="#606062" style="font-weight:bold"><?=$_SESSION["sessionID"]?></font>
                </div>
            <div class="clear"></div>
            </div>
				<div class="clear"></div>
			</div>
		</div>
    	<div id="timedTestDataDivMain">   
			<div id="menuBar" class="forHighestOnly">
				<div id="sideBar">
					<div id="report" onClick="endSession();">
						<span id="reportText">End Session</span>
						<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
					</div></a>
				</div>
			</div>
    	<div id="timedTestVersion1Details" class="forLowerOnly">
        	<div id="timedTestVersion1DetailsUpper">
            	<div id="session">
                    <span data-i18n="common.sessionID"></span> : <font color="#39a9e0"><?=$_SESSION["sessionID"]?></font>
                </div>
            <div class="clear"></div>
            </div>
        </div>
        <div id="scrollContainerForLower">
        <div id="timdTestFeedback" class="forLowerOnly hidden" align="center">
        	<div id="feedbackMain">
                <div id="feedbackLink" class="hidden" onClick="showCommentBox()" data-i18n="timedTestPage.feedback"></div>
            </div>
            <div id="commentMain">
                <div id="commentBoxDiv" align="center"><textarea id="comment" name="comment" name="comment" cols="50" rows="5"></textarea></div>
                <div align="center" style="padding-bottom:10px">
                    <div id="submitBtn" class="button textUppercase" onClick="saveTimedTestComment(1);" data-i18n="common.submit"></div>
                    <div id="cancelBtn" class="button textUppercase" data-i18n="common.cancel" onClick="hideCommentBox()" ></div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div id="timedTestContainer">
            <div id="timedTestContainerQues" align="center">
				<?php if($timedTestVersion==2) { ?>
                    <div id="outerDiv">
                        <div id="boardLeftPart">
                            <table width="100%" border="0">
                                <?php if($noOfSparkies!=0) { ?><tr><td><div align="center" id="sparkieMsg"><?=$noOfSparkies?></div></td></tr><?php } ?>
                                <tr><td><div id="instructionDiv"><?=$instruction?></div></td></tr>
                                <tr><td><?php if($hint!="") { ?><div id="hintDiv"><?=convertTips($hint)?></div><?php } ?></td></tr>
                                <tr>
                                    <td>
                                        <div id="tblWorkSheetMain" class="ver2">
                                            <table border="0"  align="center" id="tblWorkSheetMainTbl" cellspacing="5" cellpadding="2" width="95%">
                                                <?php
                                                    $quesNo = 0 ;
                                                    for($quesNo=1; $quesNo<=$totalQuesMax; $quesNo++)
                                                    {
                                                        $style='';
                                                        if($quesNo!=1)
                                                            $style	=	"style='display:none'";
                                                        echo "<tr id='single$quesNo' $style>";
                                                        echo "<td align='center'>";
                                                        echo "<table id='showUserQues$quesNo' border='0' width='100%' class='data' id=ques$quesNo>";
                                                        echo "<tr>";
                                                        echo '<td valign="middle" align="center" width="90%" >';
                                                        echo "";
                                                        $ques = $quesArray[$quesNo-1][0];
                                                        echo $ques;
                                                        echo "";
                                                        echo "<input type='hidden' name='quesType$quesNo' id='quesType$quesNo' value='".$quesArray[$quesNo-1][2]."'>";
                                                        echo '<td>';
                                                        echo "</tr>";
                                                        echo "</table>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                ?>
                                                <span id="spnTime" style="display:none"></span>
                                                <input type="hidden" name="dispQues" id="dispQues" value="1">
                                                <input type="hidden" name="totQues" id="totQues" value="<?=$totalQuesMax?>">
                                                <input type="hidden" name="minQues" id="minQues" value="<?=$totalQues?>">
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                       
                                    </td>
                                </tr>
                            </table>
                            <br>
                             <input type="checkbox" name="disableAutoSubmit" id="disableAutoSubmit" style="margin-left:-100px"><label for="disableAutoSubmit" style="font-size:60%;">Submit answer only after pressing enter</label>
                        </div>
                        <div id="displayResult">
                            <span id="spnResultMsg"></span><br /><br />
							<input type="button" name="btnContinue" id="btnContinue" value="" onClick="<?php
							 if($quesCategory=="comprehensive"){
								echo 'getNextInFlow();';
							} elseif($quesCategory=="subModuleKst") { 
								echo 'getNextInKstFlow();';
							} else  {
								echo'redirect();';
							}
							 ?>" style="background: url('assets/timedTest/continueTimeTest.png') no-repeat scroll 0 0 transparent;border: medium none;cursor: pointer;height: 62px;text-decoration: none;width: 200px;">
                        </div>
                        <div id="boardRightPart">
                            <table width="100%" border="0">
                                <tr><td>&nbsp;</td></tr>
                                <tr>
                                    <td>
                                        <div id="clock">
                                            <div id="clockHand"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr>
                                    <td><span data-i18n="common.correct"></span>: <span id="totalCorrect">0</span></td>
                                </tr>
                                <tr>
                                    <td><span data-i18n="timedTestPage.attempted"></span>: <span id="totalAttempted">0</span></td>
                                </tr>
                                <tr>
                                    <td><span data-i18n="timedTestPage.total"></span>: <span id="totalQuestions"><?=$totalQuesMax?></span></td>
                                </tr>
                                <tr>
                                    <td><span data-i18n="timedTestPage.time"></span>: <?=$duration?> <span data-i18n="timedTestPage.timeUnit"></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="clear-both"></div>
                    </div>
                <?php } else if($timedTestVersion==3) { ?>
                    <table width="820" height="100%" border="0" align="center" id="tableBggrad" cellpadding="0" cellspacing="0">
                      <tr>
                        <td colspan="3" valign="middle">
                        	<?php if($noOfSparkies!=0) { ?><div align="center" id="sparkieMsg"><?=$noOfSparkies?></div><?php } ?>
                        	<div id="dispDiscription"<?php if(strlen($instruction)>30)echo "style='font-size:20px'" ?>><?=$instruction?></div>
                        	<?php if($hint!="") { ?><div id="dispHint" <?php if(strlen($instruction)>30) echo "style='font-size:15px'" ?>><?=convertTips($hint)?></div><?php } ?>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3" align="center">
                            <div id="outerQuesDiv">
                                <div id="innerQuesDiv">
                                <table width="100%" height="100%" border="0">
                                    <tr style="height:100px;overflow:auto">
                                        <td width="80%">
                                        <div id="tblWorkSheetMain" class="ver3">
                                            <table border="0"  align="center" id="tblWorkSheetMainTbl" cellspacing="5" cellpadding="2" width="95%">
                                                <?php
                                                    $quesNo = 0 ;
                                                    for($quesNo=1; $quesNo<=$totalQuesMax; $quesNo++)
                                                    {
                                                        $style='';
                                                        if($quesNo!=1)
                                                            $style	=	"style='display:none'";
                                                        echo "<tr id='single$quesNo' $style>";
                                                        echo "<td align='center'>";
                                                        echo "<table id='showUserQues$quesNo' border='0' width='100%' class='data' id=ques$quesNo>";
                                                        echo "<tr>";
                                                        echo '<td valign="middle" align="center"';
                                                        $ques = $quesArray[$quesNo-1][0];
                                                        $str = strip_tags($ques, '');
                                                        $quesLength	=	strlen(trim($str));
                                                        if($quesLength>25 && $quesLength<60)
                                                        {
                                                            echo 'style="font-size:30px"';
                                                            $txtBox	=	"<style> .newFont { font-size:30px; }</style>";
                                                        }
                                                        else  if($quesLength>59)
                                                        {
                                                            echo 'style="font-size:20px"';
                                                            $txtBox	=	"<style> .newFont { font-size:20px; }</style>";
                                                        }
                                                        else
                                                            $txtBox	=	"<style> .newFont { font-size:40px; }</style>";
                                                        echo ' width="90%" >';
                                                        echo "";
                                                        echo $ques;
                                                        echo "";
                                                        echo "<input type='hidden' name='quesType$quesNo' id='quesType$quesNo' value='".$quesArray[$quesNo-1][2]."'>";
                                                        echo '<td>';
                                                        echo "</tr>";
                                                        echo "</table>";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                    echo $txtBox;
                                                ?>
                                                <input type="hidden" name="dispQues" id="dispQues" value="1">
                                                <input type="hidden" name="totQues" id="totQues" value="<?=$totalQuesMax?>">
                                                <input type="hidden" name="minQues" id="minQues" value="<?=$totalQues?>">
                                            </table>
                                        </div>
                                        </td>
                                        <td>
                                            <table border="0" width="100%" height="100%">
                                                <tr valign="top">
                                                    <td align="center">
                                                        <div id="clockFlashTimer">
                                                        <div id="clock">
								                            <div id="clockHand"></div>
								                        </div>
                                                        </div>
                                                    </td>
                                                </tr>
												<tr><td style="font-size:0.3em;">Time Remaining:<span id="spnTime"></span></td></tr>
                                                <tr>
                                                    <td align="center">
                                                        <span id="spnTime" style="display:none"></span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <input type="checkbox" name="disableAutoSubmit" id="disableAutoSubmit" style="margin-left:-350px"><label for="disableAutoSubmit" style="font-size:40%">Submit answer only after pressing enter</label>
                                </div>
                            </div>
                            <div id="outerScoreDiv" style="display:none">
                                <table border="0" width="100%" height="100%">
                                    <tr>
                                        <td width="50%">
                                        <table border="0" width="100%" height="100%" cellpadding="10" cellspacing="10">
                                            <tr>
                                                <td align="center">
                                                    <div id="dispScoreTemp">
                                                        <span id="dispScore">You Scored: </span><span id="userScore"></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center"><input type="button" name="btnContinue" id="btnContinue" value="" onClick="<?php
												if($quesCategory=="comprehensive"){
													echo 'getNextInFlow();';
												} elseif($quesCategory=="subModuleKst") { 
													echo 'getNextInKstFlow();';
												} else  {
													echo'redirect();';
												}
												?>"></td>
                                            </tr>
                                        </table>
                                        </td>
                                        <td align="center">
                                            <div id="divUserResponse" class="ver3">
                                                <table border="0" width="95%" align="center" id="tblWorkSheetOther">
                                                <?php
                                                    $quesNo = $rowNo = $colNo = 0 ;
                                                    for($quesNo=1; $quesNo<=$totalQuesMax; $quesNo++)
                                                    {
                                                        if($quesNo <= $totalQues)
                                                            $style	=	'';
                                                        else
                                                            $style	=	"style='display:none'";
                                                        echo "<tr id='dispAttemptedQues$quesNo' $style>";
                                                        echo '<td valign="middle" align="left" width="10px"><span><strong>'.$quesNo.'.</strong></span>&nbsp;&nbsp;</td>';
                                                        echo "<td>";
                                                        echo "<table id='dispUserResponse$quesNo' border='0' width='100%' class='data' cellspacing='2' cellpadding='1'>";
                                                        echo "<tr>";
                                                        echo '<td valign="middle" align="left"';
                                                        $ques = $quesArray[$quesNo-1][0];
                                                        echo ' width="90%" >';
                                                        echo $ques;
                                                        echo "<input type='hidden' name='quesType$quesNo' id='quesType$quesNo' value='".$quesArray[$quesNo-1][2]."'>";
                                                        echo '&nbsp;<span id="spnQno'.$quesNo.'"></span<td>';
                                                        echo "</tr>";
                                                        echo "</table>";
                                                        echo '<div>';
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                ?>
                                                </table>
                                                
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                            </div>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="3">&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="3" align="center">
                          <table width="100%" border="0" id="progressTableBggrad">
                              <tr>
                                <td width="65">&nbsp;</td>
                                <td width="474" height="175px" valign="middle">
                                    <div id="progressOuterDiv" >
                                        <div id="progressBarDiv"></div>
                                        <div id="ques0Bord" class="quesBord" style="margin-left:0px"></div>
                                        <div id="ques1Bord" class="quesBord" style="margin-left:150px"></div>
                                        <div id="ques2Bord" class="quesBord" style="margin-left:300px"></div>
                                        <div id="ques3Bord" class="quesBord" style="margin-left:450px"></div>
                                        <div id="ques4Bord" class="quesBord" style="margin-left:0px;display:none"></div>
                                        <div id="ques5Bord" class="quesBord" style="margin-left:0px;display:none"></div>
                                    </div>
                                    <div id="dispQuesNo">
                                        <div id="ques1BordDisp" class="quesBordDisp" style="margin-left:-4px">0</div>
                                        <div id="ques2BordDisp" class="quesBordDisp" style="margin-left:291px;display:none"><?=$totalQues/3?></div>
                                        <div id="ques3BordDisp" class="quesBordDisp" style="margin-left:445px"><?=$totalQues?></div>
                                        <div id="ques4BordDisp" class="quesBordDisp" style="margin-left:0px;display:none">20</div>
                                        <div id="ques5BordDisp" class="quesBordDisp" style="margin-left:0px;display:none">25</div>
                                    </div>
                                </td>
                                <td width="240" rowspan="3" align="center" valign="middle">
                                    <div id="progressCorrectDiv" >
                                        <table border="0" width="100%" height="100%">
                                            <tr><td align="center" id="totalCorrect">0</td></tr>
                                        </table>
                                    </div>
                                </td>
                                <td width="120" rowspan="3" align="center" valign="middle">
                                    <div id="totalQuesDiv" >
                                        <table border="0" width="100%" height="100%">
                                            <tr><td align="center"><?=$totalQues?></td></tr>
                                        </table>
                                    </div>
                                </td>
                              </tr>
                            </table>
                        </td>
                      </tr>
                    
                    </table>
                <?php } ?>
				<div id="insertDiv" class="forHighestOnly"><div></div>
				</div>
            </div>
        </div>
		<div>
			<div style="">
				<div id="timdTestFeedback" class="forHigherOnly">
		            <div id="scoreDiv" class="notVisible"><span id="spnResultMsg1"></span></div>
		            <div id="feedbackLink" class="hidden" onClick="showCommentBox()" data-i18n="timedTestPage.feedback"></div>
		            <div id="feedbackExplain" class="notVisible"><span id="spnResultMsg2"></span></div>
		            <div class="clear"></div>
		            <div id="commentMain">
		                <div id="commentBoxDiv"><textarea id="comment" name="comment" name="comment" cols="50" rows="5"></textarea></div>
		                <div class="textUppercase" id="submitBtn" data-i18n="common.submit" onClick="saveTimedTestComment(1);"></div>
		                <div class="textUppercase" onClick="hideCommentBox()" id="cancelBtn" data-i18n="common.cancel"></div>
		            </div>
		        </div>
			</div>
		</div>
	</div>
    	
	</div>
    <div id="pnlEndSessionMsg" style="display:none;">
        <fieldset style="width:60%; text-align:center;">
        <legend>End Session</legend>
        <div id="endSessionMsg" style="margin-top:20px;"></div>
        <div style="margin-top:20px;">
            <input type="button" class="button" value="Yes" onClick="processEndSessionAns(1);">&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" class="button" value="No" onClick="processEndSessionAns(0);">
        </div>
        </fieldset>
    </div>
    </div>
    <?php
		echo "<script>";
		for($i=0;$i<$totalQuesMax;$i++)
		{
			echo "ansArray[$i] = \"".$quesArray[$i][1]."\";\n";
		}
		echo "</script>";
	?>
    </form>
	<?php if(isset($_REQUEST['practiseModule'])){ ?>
	<style>
		#info_bar{
			height: 100px;
		}
		#topic{
			height: 70px;
			margin-top: 0px;
		}
	</style>
	<?php } ?>
    
    <form id="frmTimedTestData" method="post">
        <input type="hidden" name="noOfQuesInTimedTest" id="noOfQuesInTimedTest" value="<?=$totalQues?>">
        <input type="hidden" name="mode" id="hdnmode" value="saveTimedTestQuestions">
        <input type="hidden" name="timedTestAttemptID" id="timedTestAttemptID" value="">
        <input type="hidden" name="timedTestCode" id="hdnTimedTestCode" value="<?=$timedTestCode?>">
        <input type="hidden" name="timedTestDesc" id="hdnTimedTestDesc" value="<?=$desc?>">
        <input type="hidden" name="checkQuesCategory" id="checkQuesCategory" value="<?=$quesCategory?>">
		<input type="hidden" name="fromComments" id="fromComments" value="">
		
		
    <?php for($quesNo=1; $quesNo<=$totalQuesMax; $quesNo++) { 
    $quesArray[$quesNo-1][3]	=	str_replace("'","\"",$quesArray[$quesNo-1][3]); ?>
        <input type='hidden' name='quesText<?=$quesNo?>' id='quesText<?=$quesNo?>' value='<?php echo addslashes($quesArray[$quesNo-1][3])?>'>
        <input type='hidden' name='result<?=$quesNo?>' id='result<?=$quesNo?>' value=''>
        <input type='hidden' name='userResp<?=$quesNo?>' id='userResp<?=$quesNo?>' value=''>
        <input type='hidden' name='correctAns<?=$quesNo?>' id='correctAns<?=$quesNo?>' value=''>
        <input type='hidden' name='timeOfQ<?=$quesNo?>'  id='timeOfQ<?=$quesNo?>' value='0'>
    <?php } ?>
    </form>

    <?php include("footer.php");?>
<?php

function getTimedTestAttemptNo($userID, $timedTestCode)
{
    $attemptNo = 1;
    $query = "SELECT count(timedTestID) as cnt FROM adepts_timedTestDetails WHERE userID=$userID AND timedTestCode='$timedTestCode'";
    $result = mysql_query($query);
    if($line = mysql_fetch_array($result))
        $attemptNo = $line['cnt'] + 1;
    return $attemptNo;
}
function getNoOfSparkies($attemptNo)
{
    $noOfSparkies = 0;
    if($attemptNo==1)
        $noOfSparkies = 5;
    elseif ($attemptNo==2)
        $noOfSparkies = 4;
    elseif ($attemptNo==3)
        $noOfSparkies = 3;
    elseif ($attemptNo==4)
        $noOfSparkies = 2;
    elseif ($attemptNo>=5)
        $noOfSparkies = 1;
    return $noOfSparkies;
}

function getDynamicQues($timedTestCode, $totalQues, $quesTypeCodesArray)
{
    $quesArray = array();
    $tempQuesArray = array();
    $tmpSrno = 0;
    $srno = 0;
    $tmpTotal = 0;
    $qType=0;
    $arrayDynamicQues	=	getDynamicQcodes($quesTypeCodesArray,$totalQues);
    
    for($quesNo=1; $quesNo<=$totalQues; $quesNo++)
	{
		$qcode	=	$arrayDynamicQues[$quesNo-1];  //qcode array
		for($j=0; $j<10; $j++)
		{
			$objQuestion = new timedTestQuestion($qcode,$quesNo);
			$objQuestion->generateQuestion();
			$ques	=	$objQuestion->getQuestion($quesNo);
			$quesChk	=	strip_tags($ques,"<img>");
            if(!in_array($quesChk,$tempQuesArray))
				break;
		}
      	$optA	=	$objQuestion->getOptionA();
        $optB	=	$objQuestion->getOptionB();
        $optC	=	$objQuestion->getOptionC();
        $optD	=	$objQuestion->getOptionD();
		$quesType	=	$objQuestion->quesType;
		if($quesType=="MCQ-2")
		{
			$ques .= "<table>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."A' value='A'><label for='ansRadio".$quesNo."A'>A</label>".$optA."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."B' value='B'><label for='ansRadio".$quesNo."B'>B</label>".$optB."</td></tr>";
			$ques .= "</table>";
		}
		else if($quesType=="MCQ-3")
		{
			$ques .= "<table>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."A' value='A'><label for='ansRadio".$quesNo."A'>A</label>".$optA."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."B' value='B'><label for='ansRadio".$quesNo."B'>B</label>".$optB."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."C' value='C'><label for='ansRadio".$quesNo."C'>C</label>".$optC."</td></tr>";
			$ques .= "</table>";
		}
		else if($quesType=="MCQ-4")
		{
			$ques .= "<table>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."A' value='A'><label for='ansRadio".$quesNo."A'>A</label>".$optA."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."B' value='B'><label for='ansRadio".$quesNo."B'>B</label>".$optB."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."C' value='C'><label for='ansRadio".$quesNo."C'>C</label>".$optC."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."D' value='D'><label for='ansRadio".$quesNo."D'>D</label>".$optD."</td></tr>";
			$ques .= "</table>";
		}

        $correctAnswer	=	$objQuestion->correctAnswer;

		array_push($tempQuesArray,strip_tags($ques,"<img>"));
		$quesArray[$srno][0] = $ques;
		$quesArray[$srno][1] = $correctAnswer;
		$quesArray[$srno][2] = $quesType;
		$quesArray[$srno][3] = $ques;
		$srno++;
	}
    return $quesArray;
}

function getStaticQues($timedTestCode, $totalQue)
{
    $allQuesArray = array();
    $qcodeArray	=	array();
    $query = "SELECT qcode FROM adepts_timedTest_questions WHERE timedTestCode='$timedTestCode'";
	$result = mysql_query($query) or die(mysql_error()." error - ".$query);
	while($line=mysql_fetch_array($result))
	{
	    $qcode = $line['qcode'];
		array_push($qcodeArray,$qcode);
	}

    $quesArray = array();
	shuffle($qcodeArray);$totalQue=min($totalQue,count($qcodeArray));
	global $totalQues,$totalQuesMax;$totalQues=$totalQue;$totalQuesMax=$totalQue;
    for($quesNo = 1; $quesNo <= $totalQues; $quesNo++)
    {
		$qcode	=	$qcodeArray[$quesNo - 1];
        $objQuestion = new timedTestQuestion($qcode,$quesNo);
        $ques	=	$objQuestion->getQuestion($quesNo);
        $optA	=	$objQuestion->getOptionA();
        $optB	=	$objQuestion->getOptionB();
        $optC	=	$objQuestion->getOptionC();
        $optD	=	$objQuestion->getOptionD();
        $quesType	=	$objQuestion->quesType;

        if($quesType=="MCQ-2")
		{
			$ques .= "<table>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."A' value='A'><label for='ansRadio".$quesNo."A'>A</label>".$optA."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."B' value='B'><label for='ansRadio".$quesNo."B'>B</label>".$optB."</td></tr>";
			$ques .= "</table>";
		}
		else if($quesType=="MCQ-3")
		{
			$ques .= "<table>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."A' value='A'><label for='ansRadio".$quesNo."A'>A</label>".$optA."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."B' value='B'><label for='ansRadio".$quesNo."B'>B</label>".$optB."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."C' value='C'><label for='ansRadio".$quesNo."C'>C</label>".$optC."</td></tr>";
			$ques .= "</table>";
		}
		else if($quesType=="MCQ-4")
		{
			$ques .= "<table>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."A' value='A'><label for='ansRadio".$quesNo."A'>A</label>".$optA."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."B' value='B'><label for='ansRadio".$quesNo."B'>B</label>".$optB."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."C' value='C'><label for='ansRadio".$quesNo."C'>C</label>".$optC."</td></tr>";
			$ques.= "<tr><td align='left'><input type='radio' class='timedTestRadio' name='ansRadio$quesNo' id='ansRadio".$quesNo."D' value='D'><label for='ansRadio".$quesNo."D'>D</label>".$optD."</td></tr>";
			$ques .= "</table>";
		}
        $quesArray[$quesNo - 1][0] = $ques;
        if($quesType=="D")
        	$quesArray[$quesNo - 1][1] = $objQuestion->dropDownAns;
        else 
        	$quesArray[$quesNo - 1][1] = $objQuestion->correctAnswer;
        $quesArray[$quesNo - 1][2] = $quesType;
        $quesArray[$quesNo - 1][3] = $qcode;

        array_push($tempQuesArray,$qcode);
    }
    return $quesArray;
}

function getDynamicQcodes($quesTypeCodesArray,$totalQues)
{
	$arrayDynamicQues	=	array();
	$arrDynamicQues	=	array();
	$tmpQues	=	0;
	for($iterator=0; $iterator<count($quesTypeCodesArray); $iterator++)
	{
		$quesTypeCodeStr = trim($quesTypeCodesArray[$iterator]);
		$openingBrackPos = strpos($quesTypeCodeStr,"{");
		if($openingBrackPos===false)
		{
			$arrayDynamicQues[$iterator]['qcode']	=	$quesTypeCodeStr;
			$arrayDynamicQues[$iterator]['totQues']	=	$totalQues;
		}
		else
		{
			$closingBrackPos = strpos($quesTypeCodeStr,"}");
			$quesCode = trim(substr($quesTypeCodeStr,0,$openingBrackPos));
			$noOfQuesPer = substr($quesTypeCodeStr,$openingBrackPos+1,$closingBrackPos-$openingBrackPos-1);
			$noOfQues = round(($totalQues)*($noOfQuesPer/100));
			$arrayDynamicQues[$iterator]['qcode']	=	$quesCode;
			$arrayDynamicQues[$iterator]['totQues']	=	$noOfQues;
		}
	}
	foreach($arrayDynamicQues as $itr=>$quesDetails)
	{
		for($i=0;$i<$quesDetails['totQues'];$i++)
		{
			$arrDynamicQues[]	=	$quesDetails['qcode'];
		}
	}
	for ($i=count($arrDynamicQues);$i<$totalQues;$i++){
		$a	=	array_rand($arrayDynamicQues);
		$arrDynamicQues[]	=	$arrayDynamicQues[$a]['qcode'];
	}
	//print_r($arrDynamicQues);
	shuffle($arrDynamicQues);
	return $arrDynamicQues;
}

function convertTips($hint)
{
	$pattern	=	'/(([A-Za-z0-9])+\^([A-Za-z0-9]))+/';
	$replace	=	"$2<sup>$3</sup>";
	$hint = preg_replace($pattern, $replace, $hint);
	return $hint;
}
?>
