<?php
@include("check1.php");
include_once("constants.php");
include("functions/functions.php");
include("classes/clsUser.php");
include("functions/prePostTest_functions.php");
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
$sparkieImage = $_SESSION['sparkieImage'];
$motivationalQuotesStr	=	getMotivationalQuotes(date("Y-m-d"));
$motivationalQuotesArr	=	explode("~",$motivationalQuotesStr);
$motivationalQuotes		=	$motivationalQuotesArr[0];
$class	=	$motivationalQuotesArr[1];

?>

<?php include("header.php"); ?>

<title>EXAM CORNER</title>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/examCorner/higherClass.css" />
<link rel="stylesheet" href="css/examCorner/eventCalendar.css">
<link rel="stylesheet" href="css/examCorner/eventCalendar_theme_responsive.css">
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>-->
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<!--<script src="libs/closeDetection.js"></script>-->
<script>
var langType = '<?=$language;?>';
var click=0;
function load()
{
	if(androidVersionCheck==1)
	{
		$('#topicInfoContainer').css("height","auto");
		$('#main_bar').css("height",$('#topicInfoContainer').css("height"));
		$('#menu_bar').css("height",$('#topicInfoContainer').css("height"));
		$('#sideBar').css("height",$('#topicInfoContainer').css("height"));
	}
	else
	{
		var a= window.innerHeight - (170);
		$('#topicInfoContainer').css({"height":a+"px"});
		$('#menuBar').css({"height":a+"px"});
		$('#main_bar').css({"height":a+"px"});
		$('#sideBar').css({"height":a+"px"});
	}
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

$(document).ready(function(e) {
    $(".deleteEvent").live("click", function() {
		$("#deleteBtn,#doneBtn").show();
	});
	$("#saveComment").click(function(){
		var commentText	=	$.trim($("#commentTxt").val());
		if(commentText=="")
		{
			alert("Please enter your comments.");
			return false;
		}
		else
		{
			$.post("event.php","mode=leaveComments&commentText="+commentText,function(data){
				$("#report").hide();
				$("#cboxClose").click();
				alert("Thank you for the feedback.");
			});
		}
	});
});

function openInstruction()
{  
	$.fn.colorbox({'href':'#instruction','inline':true,'open':true,'escKey':true, 'height':410, 'width':570});
}
function leaveComments()
{  
	$.fn.colorbox({'href':'#leaveComments','inline':true,'open':true,'escKey':true, 'height':350, 'width':500});
}
function countChar1(val)
{
	var len = val.value.length;
	if (len >= 200) {
		val.value = val.value.substring(0, 200);
	} else {
		$('#charNum1').text(200 - len);
	}
}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
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
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$objUser->childName?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='javascript:void(0)'><span data-i18n="homePage.myDetails"></span></a></li>
                                    <li><a href='javascript:void(0)'><span data-i18n="homePage.changePassword"></span></a></li>
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
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?php echo $Name ?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
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
                    <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>
                
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">EXAM CORNER</span></div>
                </div>
				<div class="arrow-right"></div>
				<div id="textAtLastMain"><div id="textAtLast" class="<?=$class?>"><?=$motivationalQuotes?></div></div>
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
            <?php if(strcasecmp($subcategory,"School")!=0 && strcasecmp($subcategory,"Center")!=0) { ?>
            <div id="viewAllTopics">
            	<input type="checkbox" id="chkAllTopics" name="chkAllTopics" onClick="showAllTopics()" <?php if($showAllTopics) echo " checked"?>/>Click here to see all topics&nbsp;&nbsp;&nbsp;&nbsp;
            <?php } ?>
            </div>
            <a href="sessionWiseReport.php" class="removeDecoration hidden"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
		</div>
        
        <div id="hideShowBar" class="forHigherOnly hidden" onClick="hideBar();">-</div>
        
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="dashboard.php"><div id="drawer2"><div id="drawer2Icon"></div>DASHBOARD
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		
        <div id="tableContainerMain">
			<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div onClick="leaveComments();" id="report">
					<span id="reportText">Post Comment</span>
					<div class="circle11" id="reportIcon"><div class="arrow-s"></div></div>
				</div>
			</div>
			</div>
            <div id="topicInfoContainer">
				<div class="head"></div>
                <div id="leftContainer">
                    <a href="competitiveExam.php"><div class="topicProgressInnerDiv"><div class="topicName">COMPETITIVE EXAMS</div></div></a>
                    <a href="summarySheets.php"><div class="topicProgressInnerDiv"><div class="topicName">TOPIC SUMMARIES</div></div></a>
                    <a href="improveConcepts.php"><div class="topicProgressInnerDiv"><div class="topicName">IMPROVE YOUR CONCEPTS</div></div></a>
                    <a href="examTips.php"><div class="topicProgressInnerDiv"><div class="topicName">EXAM TIPS</div></div></a>
                </div>
                <div id="eventContainer">
                	<div class="row">
                    	<div class="heading">Exam Planner</div>
                        <div class="g4">
                            <div id="eventCalendarNoCache"></div>
                            <script>
                                $(document).ready(function() {
                                    $("#eventCalendarNoCache").eventCalendar({
                                        eventsjson: 'json/events.json.php',
                                        cacheJson: false
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
	</div>
    
    <div style="display:none">
        <div id="instruction" style="font-family: 'Conv_HelveticaLTStd-Light';font-size:18px;">
            <h3 align="center">Instructions</h3>
            <ol>
                <li>Click on the date to add tasks.</li>
                <li>Click on <img src="assets/examCorner/add.png" height="15" width="15"> symbol to add description of your task. After adding the description click on <input type="button" style="background-color:#9EC956;color:#FFF" value="Add"> to add the task.</li>
                <li>Click on check box and then on <img src="assets/examCorner/delete.png" height="15" width="15"> symbol if you want to delete task(s).</li>
                <li>Click on check box and then on <img src="assets/examCorner/done.png" height="15" width="15"> symbol if you have completed the task(s).</li>
                <li>Click on <img src="assets/examCorner/refresh.png" height="15" width="15"> to go back to default calendar.</li>
                <li>To review tasks added on a particular date, click on that date.</li>
            </ol>
            <div style="text-align:center">*********</div>
        </div>
        <div id="leaveComments" style="font-family: 'Conv_HelveticaLTStd-Light';font-size:18px;">
            <h3 align="center">Please share your feedback on Exam Corner</h3>
            <div style="text-align:center"><textarea name="commentTxt" id="commentTxt" cols="35" rows="8" onKeyUp="countChar1(this)" placeholder="Enter your comments." style="resize:none"></textarea><br><span id="charNum1">200</span> character(s) left</div>
            <div style="text-align:center"><input type="button" id="saveComment" value="Save" style="background-color:#9EC956;color:#FFF"></div>
        </div>
    </div>

    <input type="hidden" name='mode' id="mode">
    <input type="hidden" name="postTestFlag" id="postTestFlag" value="<?=$_SESSION['prePostTestFlag']?>">
    <input type="hidden" name="postTestTopic" id="postTestTopic" value="<?=$_SESSION['prePostTestTopic']?>">
    <input type="hidden" name='ttCode' id="ttCode">
    <input type="hidden" name='topicDesc' id="topicDesc">
    <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
    <input type="hidden" name="cls" id="cls" value="<?=$user->childClass?>">
</form>

<script src="libs/examCorner/jquery.eventCalendar.js" type="text/javascript"></script>
<?php include("footer.php") ?>

<?php

function getMotivationalQuotes($date)
{
	$class	=	"";
	$sq	=	"SELECT id,quote,presentQuote,author  FROM adepts_motivationalQuotes ORDER BY RAND()";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	$quotes	=	'"'.$rw[1].'"';
	if($rw[3]!="")
	{
		if(strlen($quotes)>200)
		{
			$quotes	=	$quotes."<br class='breakIt'> - <b>".$rw[3]."</b><div id='like' onClick='likeQuote(".$rw[0].")' title='Like'></div><div id='disLike' onClick='disLikeQuote(".$rw[0].")' title='Dislike'></div>";
			$class="quoteFonts1";
		}
		else if(strlen($quotes)>90)
		{
			$quotes	=	$quotes."<br class='breakIt'> - <b>".$rw[3]."</b><div id='like' onClick='likeQuote(".$rw[0].")' title='Like'></div><div id='disLike' onClick='disLikeQuote(".$rw[0].")' title='Dislike'></div>";
			$class="quoteFonts2";
		}
		else
		{
			$quotes	=	$quotes."<br>- <b>".$rw[3]."</b><div id='like' onClick='likeQuote(".$rw[0].")' title='Like'></div><div id='disLike' onClick='disLikeQuote(".$rw[0].")' title='Dislike'></div>";
		}
	}
	else
	{
		$quotes	=	$quotes."<div id='like' onClick='likeQuote(".$rw[0].")' title='Like'></div><div id='disLike' onClick='disLikeQuote(".$rw[0].")' title='Dislike'></div>";
	}
	return $quotes."~".$class;
}

?>