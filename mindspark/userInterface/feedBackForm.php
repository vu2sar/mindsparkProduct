<?php
	@include("check1.php");
	include("classes/clsUser.php");
	require_once 'constants.php';
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR);

	if(!isset($_SESSION['userID']))
	{
		header("Location: logout.php");
		exit;
	}
	$keys = array_keys($_REQUEST);
	foreach($keys as $key)
	{
		${$key} = $_REQUEST[$key] ;
	}
	$userID   = $_SESSION['userID'];
	$buddy_id = $_SESSION['buddy'];
	$childClass	=	$_SESSION['childClass'];
	$feedbackType = "";

	$sparkieImage = $_SESSION['sparkieImage'];
	$feedbackset = $_REQUEST["setNo"];

	$user = new User($userID);$feedbackFlag=$user->checkForFeedback();
	$feedbackFlag = explode("~",$feedbackFlag);
	if ($feedbackFlag[0] == 0) {
		echo '<script>window.location.href="home.php";</script>';
		exit;
	}
	else {
		$feedbackset=$feedbackFlag[1];
	}
	$query  = "SELECT questions,formName FROM adepts_userFeedbackSet WHERE setno=$feedbackset";
	$result = mysql_query($query);
	$line   = mysql_fetch_array($result);
	$qids = $line["questions"];
	$formName = $line["formName"];
	$questionArr = explode(",", $qids);
	foreach ($questionArr as $value) 
	{
		$value1 = explode("~", $value);
		$value = $value1[0];
		$query = "SELECT question, questionType, options, helpText FROM adepts_userFeedbackQuestions WHERE qid=$value";
		$result = mysql_query($query);
		$line = mysql_fetch_array($result);
		$question_details_array[] = array("question"=>$line["question"], "quesType"=>$line["questionType"],"options"=>json_decode(stripslashes($line["options"])), "helpText"=>$line["helpText"]);
	}			
	$arrQues = array();	
	include("header.php");
?>

<title>Feedback Form</title>

<?php
	if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/feedBackForm/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/feedBackForm/midClass.css" />
    <link rel="stylesheet" href="css/commonMidClass.css" />
<?php } else { ?>
	<link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/feedBackForm/higherClass.css" />
<?php } ?>
	<link rel="stylesheet" href="css/commonCssForNewFeedbackFormat.css"/>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	<script type='text/javascript' src='libs/combined.js'></script>
	<script type='text/javascript' src='libs/feedbackCommonFunc.js?ver=1'></script> 
	<!--<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script> -->
	<!-- <script type="text/javascript" src="libs/closeDetection.js"></script> -->


	<script>
		var langType = '<?=$language;?>';
		var feedbackSet = <?=$feedbackset?>;
		var androidDevice = false;
		var appleDevice = false;
		$(document).ready(function(){
			if(navigator.userAgent.indexOf("Android") != -1)
		    {
		        androidDevice = true;
		    }
		    else if(window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
		    {
		        appleDevice = true;
		    }

		    if(appleDevice || androidDevice)
		    {
		    	$("#dashboardTextHighestClass").css({"width":"115px","margin-top":"-20px","margin-left":"-5px"});
		    }
		});

	    function load()
	    {
			<?php if($theme==1) { ?>	
				var a= window.innerHeight -200;
	        	$('#feedbackFormMain').css("height",a);
			<?php } else if($theme==2){ ?>
			<?php } else if($theme==3) { ?>
				var a= window.innerHeight - (170);
				var b= window.innerHeight - (610);
				var c= parseInt($('#frmFeedback').css("height")) +150;
				$('#frmFeedback').css("height",c+"px");
				$('#feedbackFormMain').css({"height":a+"px"});
				$('#sideBar').css({"height":a+"px"});
				$('#main_bar').css({"height":a+"px"});
				$('#menuBar').css({"height":a+"px"});
			<?php } ?>
			if(androidVersionCheck==1) 
			{
				$('#frmFeedback').css("height","auto");
				$('#main_bar').css("height",$('#frmFeedback').css("height"));
				$('#menu_bar').css("height",$('#frmFeedback').css("height"));
				$('#sideBar').css("height",$('#frmFeedback').css("height"));
			}
	    }		
		var click=0;
		function openMainBar()	// Used For theme-3 to open sidebar on clicking '+' symbol
		{		
			if(click==0)
			{
				if(window.innerWidth>1024)
				{
					$("#main_bar").animate({'width':'245px'},600);
					$("#plus").animate({'margin-left':'227px'},600);
				}
				else
				{
					$("#main_bar").animate({'width':'200px'},600);
					$("#plus").animate({'margin-left':'182px'},600);
				}
				$("#vertical").css("display","none");
				click=1;
			}
			else if(click==1)
			{
				$("#main_bar").animate({'width':'26px'},600);
				$("#plus").animate({'margin-left':'7px'},600);
				$("#vertical").css("display","block");
				click=0;
			}
		}
		
	</script> 
</head>
<body class="translation" onLoad="load()" onResize="load();">
	<div id="top_bar">
		<div class="logo">
		</div>
        
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$user->childName?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.classSmall">Class</span> <span id="userClass"><?=$user->childClass?><?=$user->childSection?></span></div>
            </div>
        </div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$user->childName?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
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
        <div id="logout" onClick="logoff()" class="linkPointer hidden">
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
        	<div id="blankWhiteSpace"><div id="timedTestIcon"></div></div>
             <div id="home">
                <div id="homeIcon"></div>
                <div id="dashboardHeading" class="forLowerOnly"><div id='dashboardTextLowerClass' style="display:inline-block;" class="feedbackHeading">FEEDBACK FORM</div></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                	<div id="homeIcon"></div>
                    <div id="homeText">HOME > <div id='dashboardTextHigherClass' style="color:#606062;display:inline-block;" class="feedbackHeading"> FEEDBACK FORM</div></div>
				</div>
                <div id="feedbackInfo">Please take out a few minutes to answer the questions below:
</div>
				<div class="clear"></div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$user->childClass?><?=$user->childSection?>
			</div>
			<div class="Name">
				<strong><?=$user->childName?></strong>
			</div>
            <div class="clear"></div>
		</div>
        <div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span id='dashboardTextHighestClass' style="display:inline-block;width: 155px;margin-top: -15px;margin-left: -15px;" class="textUppercase feedbackHeading"> FEEDBACK FORM</span></div>
                </div>
				<div class="arrow-right"></div>
				<div id="feedbackInfo">Please take out a few minutes to answer the questions below:
</div>
				<div class="clear"></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
            <a href="explore.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;">
			<div id="drawer5"><div id="drawer5Icon" style='<?php if($_SESSION['rewardSystem']!=1) { echo 'position: absolute;background: url("assets/higherClass/dashboard/rewards.png") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;";';} ?>' class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>REWARDS CENTRAL</div></a>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
			</div>
		</div>
        <div id="feedbackFormMainDiv">
	        <div id="school" class="forLowerOnly hidden">School: <?=$user->schoolName?></div>
	        
	        <div id="feedbackFormMain">
	        	<div id="feedbackInfo" class="forLowerOnly hidden">Please take out a few minutes to answer the questions below:
</div>
	            <form name="feedbackform" id="frmFeedback" method="POST" action="saveFeedBack.php">
	            </form>
	        </div>
	    </div>
	</div>
<?php 
	include("footer.php");
	echo "<script type='text/javascript'>";
	echo "showFeedbackForm('".$formName."',".json_encode($question_details_array).",'".$qids."',".$feedbackset.");";
	echo "</script>";
?>