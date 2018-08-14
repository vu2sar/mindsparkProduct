<?php
	session_start();

	include("check1.php");
	include("constants.php");
	include("classes/clsUser.php");
	include ("functions/functionsForDynamicQues.php");
	include ("functions/functions.php");
	include("classes/clsQuestion.php");
	include("functions/orig2htm.php");
	include_once("functions/next_question.php");
	include("classes/clsDiagnosticTestQuestion.php");	

	if(!isset($_SESSION['userID']))
	{
		header("Location:error.php");
		exit;
	}
	$userID = $_SESSION['userID'];
	//error_reporting(E_ERROR);
	$user = new User($userID);
	$Name = explode(" ", $user->childName);
	$Name = $Name[0];

	$childName 	   = $user->childName;
	$schoolCode    = $user->schoolCode;
	$childClass    = $user->childClass;
	$childSection  = $user->childSection;
	$category 	   = $user->category;
	$subcategory   = $user->subcategory;
	$endDate 	   = $user->endDate;
	$userName	   = $user->username;
	$_SESSION['questionType'] = "normal";

	$query = "SELECT timeAllowedPerDay from adepts_userDetails where userID='$userID';";
	$data=mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_row($data);
	$timePerDay=$row[0];

	if($timePerDay=="")
		$timePerDay = 40;

	//echo $mode;
	//$_SESSION['total_questions']=$_SESSION['maxQues'];

	$today = date("Y-m-d");
	$ttAttemptID = $_SESSION['teacherTopicAttemptID'];

	if(isset($_POST['sessionID']))
		$sessionID = $_POST['sessionID'];
	else
		$sessionID	= $_SESSION['sessionID'];


	if(isset($_GET['practiseTest']))
	{
		$practiseTest = $_GET['practiseTest'];
		$scorepractisetest = $_GET['sr'];
	}



	if(isset($_POST['reportDate']))
	{
		$reportDate = $_POST['reportDate'];
		$reportDate = date('Y-m-d', strtotime($reportDate));
	}

	if(!isset($mode))
		$mode = "";
	if($mode=="login")
		$mode = $_GET["mode"];
	
	$newKudosCountInSession=newKudosCounterInSession($userID, $userName, $sessionID, $childClass);
	if($newKudosCountInSession>0) {$kudosNotification=1;}	

	//echo 'NEW KUDOS COUNT IN SESSION IS-'.$newKudosCountInSession.' TEST '.$kudosNotification;

	?>

	<?php include("header.php"); ?>

	<title>End-Session Report</title>

	<?php
		if($theme==1) { ?>
		<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
		<link href="css/endSesssionReport/lowerClass.css?ver=1" rel="stylesheet" type="text/css">
	<?php } else if($theme==2) { ?>
	    <link rel="stylesheet" href="css/commonMidClass.css" />
	    <link rel="stylesheet" href="css/endSesssionReport/midClass.css?ver=1" />
		<link href="css/home/prompt2.css" rel="stylesheet" type="text/css">
	<?php } else { ?>
	    <link rel="stylesheet" href="css/commonHigherClass.css" />
	    <link rel="stylesheet" href="css/endSesssionReport/higherClass.css" />
		<link href="css/home/prompt2.css" rel="stylesheet" type="text/css">
	<?php } ?>
	<link rel="stylesheet" type="text/css" href="css/colorbox.css">
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
	<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>
	<script>

		var langType = '<?=$language;?>';
		var click=0;
		// renaming 'load' to 'load_end_session_report' as it is clashing with the one in prompt2.php
		function load_end_session_report() {

		<?php if($theme==1) { ?>
			var a= window.innerHeight - (47 + 70 + 55 + 30);
			$('#endSessionDataDivMain').css("height",a+"px");
			$(".forHigherOnly").remove();
		<?php } else if($theme==2) { ?>
			//var a= window.innerHeight - (80 + 25 + 140 );		// for mantis task 11978 start
			//$('#endSessionDataDivMain').css("height",a+"px");	 
			$('#endSessionDataDivMain').css("height","100%");	// for mantis task 11978 end
			$(".forLowerOnly").remove();
		<?php } else if($theme==3) { ?>
					var a= window.innerHeight - (170);
					var b= window.innerHeight - (610);
					$('#endSessionDataDivMain').css("height",a+"px");
					$('#dataTableDiv').css({"height":a+"px"});
					$('#sideBar').css({"height":a+"px"});
					$('#main_bar').css({"height":a+"px"});
					$('#menubar').css({"height":a+"px"});
		<?php } ?>
		<?php //if(showSurveyAlert($userID,$_SESSION['sessionID']))	{ ?>
			//openFeedback();
		<?php //} ?>
			if(androidVersionCheck==1){
					$('#dataTableDiv').css("height","auto");
					$('#endSessionDataDivMain').css("height","auto");
					$('#main_bar').css("height",$('#endSessionDataDivMain').css("height"));
					$('#menu_bar').css("height",$('#endSessionDataDivMain').css("height"));
					$('#sideBar').css("height",$('#endSessionDataDivMain').css("height"));
			}
		}
		$(document).ready(function(){
			load_end_session_report();			
			$('html, body').animate({
        scrollTop: $("#scroll_<?php echo $sessionID ?>").offset().top-140
    }, 1000);
		});
		function showQues(qcode, qno, srno)	{
			document.getElementById("hidqcode").value = qcode;
			document.getElementById("hidqno").value = qno;
			document.getElementById("hidsrno").value = srno;
			document.getElementById("frmReport").submit();
		}
		function showPracticeQues(qcode, qno, srno)	{
			document.getElementById("hidqcode").value = qcode;
			document.getElementById("hidqno").value = qno;
			document.getElementById("hidsrno").value = srno;
			document.getElementById("mode").value = "topicRevision";
			document.getElementById("frmReport").submit();
		}
		function showPrevComments()
		{
			window.location = "viewComments.php?from=links&mode=1";
		}
		function logoff()
		{
			window.location="logout.php";
		}
		function getHome()
		{
			window.location.href	=	"controller.php?mode=topicSwitch&from=endSession";
		}

		function renew(userID){
			window.open("http://mindspark.in/registration.php?userID="+userID,"_newtab");
		}
		function openMainBar(){
			if(click==0){
				$("#main_bar").animate({'width':'245px'},600);
				$("#plus").animate({'margin-left':'227px'},600);
				$("#vertical").css("display","none");
				click=1;
			}
			else if(click==1){
				$("#main_bar").animate({'width':'26px'},600);
				$("#plus").animate({'margin-left':'7px'},600);
				$("#vertical").css("display","block");
				click=0;
			}
		}
		function openFeedback()
		{
			$.fn.colorbox({'href':'#sendFeedback','inline':true,'open':true,'escKey':true, 'height':350, 'width':500});
		}

		function resetKudosCounter()
		{
			username='<?= $userName ?>';
			$.ajax({
			type:'POST',
			url: "kudosAjax.php",
			data: {resetKudos: "YES",userName: username},
			success: function(){} 
			});

			
		}
	</script>
	<style>
		#feedbakText{
			margin-top:50px;
			font-size:18px;
			padding-left:50px;
			padding-right:50px;
		}
		.practiseModule{
		}
		.headElement.practiseModule td{
			background: #c3d3ed;
			/* border-top: 2px solid #a0a0a0; */
			padding-top: 5px;
		}
		.buttonTemp1 {
			background-color:transparent;
			-moz-border-radius:2px;
			-webkit-border-radius:2px;
			border-radius:2px;
			border:1px solid #2f99cb;
			display:inline-block;
			color:#2f99cb;
			font-size:1.1em;
			margin-top:10px;
			margin-left:70px;
			padding:6px 24px;
			text-decoration:none;
			cursor:pointer;
		}.buttonTemp1:active {
			position:relative;
			top:4px;
			cursor:pointer;
		}
		<?php
		if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1) { ?>
			#top_bar {
			    background: none repeat scroll 0 0 #9EC956 !important;
			}
			#dashboard {
			    background-color: #9EC956 !important;
			}
			#dashboardIcon {
			    background: url("assets/higherClass/landingScreen/examcorner.png") repeat scroll 0 0 transparent !important;
				margin-left: 24px !important;
			}
			.arrow-right {
			    border-left: 15px solid #9EC956 !important;
			}
			#nameIcon {
				border: 2px solid #9EC956 !important;
			}
			#cssmenu a {
				color: #9EC956 !important;
			}
			#infoBarLeft {
				color: #9EC956 !important;
			}
		<?php } ?>
		</style>
	</head>
	<?php if($_SESSION['rewardSystem']==1 && $_SESSION['sessionID']==$sessionID) include("prompt2.php")?>
<body class="translation" onLoad="load_end_session_report()" onResize="load_end_session_report()" style="overflow-x:hidden;overflow-y:auto;" >
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$Name?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.classSmall">Class</span> <span id="userClass"><?=$childClass.$childSection?></span></div>
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
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
									<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='javascript:void(0)' onClick="logoff()"><span data-i18n="common.logout"></span></a></li>
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
        <div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
		<?php
			if(($mode== -2 || $mode==-3) && $theme!=3) {
				$msg = "";
				if($mode == -2)
					$msg = "You have been trying questions from the topic: <strong>".$_SESSION['teacherTopicName']."</strong>. Click <a href=\"dashboard.php\" style='text-decoration:underline;color:blue;'>here</a> to restart this topic or select another topic";
				elseif ($mode == -3)
					$msg = "<br/>Congratulations! You have successfully completed the topic <strong>".$_SESSION['teacherTopicName']."</strong>. <br/>You can select another topic or click <a href=\"dashboard.php\" style='text-decoration:underline;color:blue;'>here</a> to do this topic again.";
				echo "<div align='center' class='msg'>$msg</div>";
			}
			if($mode!=-5 && $mode!=-6 && $mode!=-7 && $mode!="refresh") 	{ //Show this link if the daily/weekly/session limit has not been completed
				//pending for showing home button or not
					/*echo	'<a href="controller.php?mode=topicSwitch" class="home bcrumb"></a>
							<div class="separator"></div>
							<a href="endSessionReport.php" class="scorecarded bcrumb"></a>';*/
				$deactiveLinks=0;
			}
			else
			{
				$deactiveLinks=1;
			}

			$challengeQuesAttempted = 0;
			if(SUBJECTNO==2)
			{
				if($theme==2)
				{	
					if(isset($reportDate))
						$date = $reportDate;
					else
						$date = date('Y-m-d');
					$query  = "SELECT count(IF(R=1 OR R=0, 1,0)), sum(IF(R=1,1,0)) FROM adepts_ttChallengeQuesAttempt WHERE userID=$userID and lastModified BETWEEN '$date' AND '$date 23:59:59'";
				}
				else
				{
					$query  = "SELECT count(IF(R=1 OR R=0, 1,0)), sum(IF(R=1,1,0)) FROM adepts_ttChallengeQuesAttempt WHERE sessionID=$sessionID";
				}
				//$query  = "SELECT count(srno), sum(R) FROM adepts_ttChallengeQuesAttempt WHERE sessionID=$sessionID";
				
				$result = mysql_query($query);
				$line   = mysql_fetch_array($result);
				$challengeQuesAttempted = $line[0];
				if($line[1]=="")
					$challengeQuesCorrect   = 0;
				else
					$challengeQuesCorrect   = $line[1];
			}

			//$resArr = lastSession($userID,$sessionID);\
			if($theme==2)
			{
				if(isset($reportDate))
						$date = $reportDate;
					else
						$date = date('Y-m-d');

					
				$resArr = lastdaySession($userID,$date);
				$timedTestArray = getTimedTestAttemptedIndaySession($userID,$date);
				$gamesArray = getGamesAttemptedInDAYSession($userID,$date);
				$remedialItemAttemptArray = getRemedialItemAttemptsInDaySession($userID,$date);
				$challengeQuesArray = getChallengeQuesAttemptedInDaySession($userID,$date);						
			}else
			{
				$resArr = lastSession($userID,$sessionID);
				$timedTestArray = getTimedTestAttemptedInSession($userID,$sessionID);
				$gamesArray = getGamesAttemptedInSession($userID,$sessionID);
				$remedialItemAttemptArray = getRemedialItemAttempts($userID,$sessionID);
				$challengeQuesArray = getChallengeQuesAttemptedInSession($userID,$sessionID);
			}
			//print_r($resArr);
			$totalQuestions = 0;
			$score = 0;
			$timesum = 0;
			$quesAttemptedArray = array();

			foreach( $resArr as $val) {
				if( $val[ 5 ] == 1)
					$score++;
				$timesum+= $val[ 6 ];
				if( $val[ 2 ] != "")
					$totalQuestions++;
			}
			//$score = $score + $totalScore;

			$timespent_this_session = 0.00;
			$timespent_query = "SELECT date_format(startTime,'%d-%b-%Y %H:%i:%s') starttime,date_format(endTime,'%d-%b-%Y %H:%i:%s') endtime,date_format(tmLastQues,'%d-%b-%Y %H:%i:%s') tmLastQues, time_format(timediff(if(endTime>ifnull(tmLastQues,0),endTime,tmLastQues),startTime),'%H:%i:%s') duration, date_format(lastModified,'%d-%b-%Y %H:%i:%s') as lastModified, noOfJumps, time_format(timediff(NOW(),startTime),'%H:%i:%s') durationCurrent FROM ".TBL_SESSION_STATUS." WHERE sessionID='".$sessionID."'";
			$timespent_result = mysql_query($timespent_query);
			$timespent_data = mysql_fetch_array($timespent_result);
			//$starttime_timestamp = strtotime($timespent_data['startTime']);
		    $sparkiesInThisSession = $timespent_data['noOfJumps'];
			
			//$timespent_this_session = number_format((($endtime_timestamp - $starttime_timestamp)/60), 2, ".", "");
			if($timespent_data['duration']=="")
				$timespent_data['duration'] = $timespent_data['durationCurrent'];
			$exploded_time_spent = explode(":", $timespent_data['duration']);
			if($exploded_time_spent[0]>0){
				$exploded_time_spent[0] = $exploded_time_spent[0]*60 + $exploded_time_spent[1];
				$exploded_time_spent[1] = $exploded_time_spent[2];
			}else{
				$exploded_time_spent[0] = $exploded_time_spent[1];
				$exploded_time_spent[1] = $exploded_time_spent[2];
			}
			if ($exploded_time_spent[1]>60)
			{
				$exploded_time_spent[0] = $exploded_time_spent[0] + 1;
				$exploded_time_spent[1] = $exploded_time_spent[1] - 60;
			}
			else if ($exploded_time_spent[1]==60 || $exploded_time_spent[1]==6)
			{
				$exploded_time_spent[0] = $exploded_time_spent[0] + 1;
				$exploded_time_spent[1] = "00";
			}
			else if ($exploded_time_spent[1]==0 || $exploded_time_spent[1]=="")
			{
				$exploded_time_spent[1] = "00";
			}

		?>
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
             <div id="home">
                <div id="homeIcon" class="linkPointer"<?php if($deactiveLinks!=1) { ?> onClick="getHome() "<?php } ?> ></div>

				<?php if(!isset($practiseTest)) { ?>
                 	<div id="dashboardHeading" class="forLowerOnly"> - <a class="removeDecoration textUppercase" href="<?php if($deactiveLinks!=1) { ?>dashboard.php <?php } else {?>javascript:void(0);<?php } ?>" data-i18n="dashboardPage.dashboard"></a> - <a class="removeDecoration" href="<?php if($deactiveLinks!=1) { ?>sessionWiseReport.php <?php } else {?>javascript:void(0);<?php } ?>" data-i18n="sessionWiseReportPage.sessionWiseReport"></a> - <span class="textUppercase" data-i18n="endSessionReportPage.scoreCard"></span></div>
				<?php } 
				else  { ?>
				<div id="dashboardHeading" class="forLowerOnly"> - <a class="removeDecoration textUppercase" href="<?php if($deactiveLinks!=1) { ?>dashboard.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="dashboardPage.dashboard"></a></div>
				<?php }  ?>

				<div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <div id="homeIcon"<?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>"></div>
                    <div id="homeText" class="forHigherOnly"><span <?php if($deactiveLinks!=1) { ?> onClick="getHome() <?php } ?>" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> &#10140; &nbsp; <font color="#606062"><a class="removeDecoration textUppercase" href="<?php if($deactiveLinks!=1) { ?>dashboard.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="dashboardPage.dashboard"></a></font> &#10140; &nbsp; <font color="#606062"><a class="removeDecoration" href="<?php if($deactiveLinks!=1) { ?>sessionWiseReport.php<?php } else {?>javascript:void(0);<?php } ?>" data-i18n="sessionWiseReportPage.sessionWiseReport"></a></font> &#10140; &nbsp; <font color="#606062"> <span class="textUppercase" data-i18n="endSessionReportPage.scoreCard"></span></font></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>
			</div>

			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div class="class"><span data-i18n="common.class">Class</span>  <?=$childClass.$childSection?></div>
                	<div class="Name"><?=$Name?></div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--a href="<?php if($deactiveLinks!=1) { ?>sessionWiseReport.php<?php } else {?>javascript:void(0);<?php } ?>" class="removeDecoration"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
            <div class="clear"></div-->

			<?php  if($theme!=2)  { ?>
	            <div id="session">
	                <span data-i18n="common.sessionID"></span>: <font color="#39a9e0"><?=$sessionID?></font>
	            </div>
	            <div id="duration">
	                <span data-i18n="endSessionReportPage.currentSessionTime"></span>: <font color="#39a9e0"><?=$exploded_time_spent[0].":".$exploded_time_spent[1]?> <span data-i18n="endSessionReportPage.minutes"></span></font>
	            </div>
	            <div id="sparkieInfo">
	                <span data-i18n="[html]endSessionReportPage.sparkieEarned"></span>: <?=$sparkiesInThisSession?>
	            </div>

			<?php } ?>
		</div>
        <div id="info_bar" class="forHighestOnly">
        	<?php if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
				{ ?>
				<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Exam Corner</span></div>
                </div></a>
         	<?php } else { ?>
         		<a href="dashboard.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></div>
                </div></a>
         	<?php } ?>
			<div class="arrow-right"></div>
			<div id="endSessionHeading">End-Session Report</div>
			<div id="session">
                <span data-i18n="common.sessionID"></span>: <font id="sessionColor"><?=$sessionID?></font>
            </div>
			<div id="duration">
                <span data-i18n="endSessionReportPage.currentSessionTime"></span>: <font id="durationColor"><?=$exploded_time_spent[0].":".$exploded_time_spent[1]?> <span data-i18n="endSessionReportPage.minutes"></span></font>
            </div>
            <div id="challengeQues">
				CHALLENGE QUESTIONS
                <span class="correct_bar">CORRECT : <?=$challengeQuesCorrect?> &nbsp;&nbsp; ATTEMPTED : <?=$challengeQuesAttempted?></span>
            </div>
            <div id="totalQuestionCorrect">
                QUESTIONS
                <span class="correct_bar">CORRECT : <?=$score?> &nbsp;&nbsp; ATTEMPTED : <?=$totalQuestions?></span>
            </div>               
                
            <?php  if($kudosNotification==1) {echo '<a href="src/kudos/kudosHomeHigherClass.php?wall=my"><div onClick="resetKudosCounter();" style="cursor:pointer;" id="kudosDuringSession">KUDOS<span class="kudos_bar"><img style="width:15px; height:15px; margin-bottom:-3px;" src="assets/kudos.png"/>&nbsp; RECEIVED: '.$newKudosCountInSession.' </span></div></a>'; } ?>
			<div class="clear"></div>
		</div>
	</div>
	<div id="menuBar" class="forHighestOnly">
		<div id="sideBar">
         <?php	if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
			{ ?>
			<a href="improveConcepts.php">
			<div id="report">
				<span id="reportText">IMPROVE YOUR CONCEPTS</span>
				<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
			</div></a>
          <?php } else { ?>
          	<a href="sessionWiseReport.php">
			<div id="report">
				<span id="reportText">SESSION-WISE REPORT</span>
				<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
			</div></a>
          <?php } ?>
			<div class="empty">
            </div>
            
            <div id="customEndSessionKudos">
            
            <?php  {echo '<a href="src/kudos/kudosHomeHigherClass.php?wall=my"><div onClick="resetKudosCounter();" style="cursor:pointer;" id="kudosDuringSessionCustom"><span class="kudos_bar_custom"><img style="width:15px; height:15px; margin-bottom:-3px;" src="assets/kudos.png"/>&nbsp; KUDOS RECEIVED: '.$newKudosCountInSession.' </span></div></a>'; } ?>
            </div>
            
		</div>
	</div>
    <div id="endSessionDataDivMain">
        <?php
			if(($mode== -2 || $mode==-3) && $theme==3) {
			  	$msg = "";
			  	if($mode == -2)
					$msg = "You have been trying questions from the topic: <strong>".$_SESSION['teacherTopicName']."</strong>. Click <a href=\"dashboard.php\" style='text-decoration:underline;color:blue;'>here</a> to restart this topic or select another topic";
				elseif ($mode == -3)
					$msg = "<br/>Congratulations! You have successfully completed the topic <strong>".$_SESSION['teacherTopicName']."</strong>. <br/>You can select another topic or click <a href=\"dashboard.php\" style='text-decoration:underline;color:blue;'>here</a> to do this topic again.<br/>";
				echo "<div align='center' class='msg'>$msg</div><br/>";
			}
			if($mode=="refresh") {
				echo "<div align='center'>";
				echo "It seems you have pressed refresh.<br/>Kindly <a href='logout.php'>login</a> again!";
				echo "</div>";
			}
			else if ($mode== -7 )	{
				echo "<div align='center'><br/>";
				echo "<h2><font color='red'>You have completed your ".$timePerDay." minute session!</font><br/> We hope you enjoyed it. Please login again if you want to continue.</h2>";
				echo "</div>";
			}
			else if ($mode== -5 )	{
				echo "<div align='center'><br/>";
				echo "You have completed your Mindspark quota for the week! <br/>You can login again tomorrow to enjoy Mindspark!";
				echo "</div>";
				//echo "<div align=center><div align='left'><br/>We have instituted a daily and weekly time limit mainly to ensure that you do Mindspark regularly without ignoring the other subjects. This has been done in consultation with the school authorities who would like you to progress at an even pace throughout the year.</div></div>";
			}
			else if ($mode== -6 )	{
				echo "<div align='center'><br/>";
				echo "You have completed your session for the day!<br/>You can login again tomorrow to enjoy Mindspark!";
				echo "</div>";
				//echo "<div align=center><div align='left' ><br/>We have instituted a daily and weekly time limit mainly to ensure that you do Mindspark regularly without ignoring the other subjects. This has been done in consultation with the school authorities who would like you to progress at an even pace throughout the year.</div></div>";
			}
			else if ($mode== 6 )	{
				echo "<div align='center'><br/>";
				echo "Your session is timed out due to inactivity.<br>Please <a href='index.php'>login again</a> to continue.";
				echo "</div>";
			}
			if($user->subscriptionDaysRemaining!="" && $user->subscriptionDaysRemaining<10)
			{
				if ($category=="STUDENT" && strcasecmp($subcategory,"Individual")==0)
				{
					$msg = "<br>Your subscription period will end on ".$endDate;
					$msg .= ". <a href=\"javascript:renew('".$userID."')\" style='text-decoration:underline;color:blue;'>Click here</a> to renew.";
					echo "<div align='center' class='msg'>$msg</div>";
				}
			}
		?>
    	<div id="headingDiv" class="forHigherOnly textUppercase" data-i18n="endSessionReportPage.endSessionReport"></div>
        <div id="sessionInfo" class="forHigherOnly hidden">
            <div id="totalQuestion">
                <span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span>
                <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span>:
                <span id="totalQuestionDigit"><?=$totalQuestions?></span>
                <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span>:
                <span id="totalQuestionCorrectDigit"><?=$score?></span>
            </div>
            <div id="challengeQues">
                <span id="challengeQuesText" data-i18n="endSessionReportPage.challengeQues"></span>
                <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span>:
                <span id="challengeQuesAttemptedDigit"><?=$challengeQuesAttempted?></span>
                <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span>:
                <span id="challengeQuesCorrectDigit"><?=$challengeQuesCorrect?></span>
            </div>
            <div id="totalQuestionCorrect">
				<span id="timeTakenQuesText" data-i18n="endSessionReportPage.quesAttemptTime"></span>:
                <span id="timeTakenQuesDigit"><?=convertSecs($timesum)?></span>
                <span style="font-color:#606062;" data-i18n="endSessionReportPage.minutes"></span>
            </div>
            <!--<div id="totalQuestionCorrect">
                <span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
                <span id="totalQuestionCorrectDigit"><?=$score?></span>
            </div>-->

            <div class="clear"></div>
        </div>
        <div id="detail_bar" class="forLowerOnly hidden">
        	<div id="session">
                <span data-i18n="common.sessionID" class="fontWeight"></span> : <font color="#39a9e0"><?=$sessionID?></font>
            </div>
			<?php if(!isset($practiseTest)) { ?>
            <div id="duration">
                <span data-i18n="endSessionReportPage.currentSessionTime" class="fontWeight"></span> : <font color="#39a9e0"><?=$exploded_time_spent[0]."minutes  ".$exploded_time_spent[1]?> <span>seconds</span></font>
            </div>
			
            <div id="sparkieInfo">
                <span data-i18n="[html]endSessionReportPage.sparkieEarned" class="fontWeight"></span> : <?=$sparkiesInThisSession?>
            </div>
			<?php } ?>
            <div id="totalQuestionCorrect">
                <span id="timeTakenQuesText" data-i18n="endSessionReportPage.quesAttemptTime" class="fontWeight"></span> :
                <span id="timeTakenQuesDigit"><?=convertSecs($timesum)?></span>
            </div>
            <a style="text-decoration:none;" href="src/kudos/kudosHomeLowerClass.php?wall=my"><div onClick="resetKudosCounter();" class="fontWeight" id="kudosReceived">
            <p style="font-size:14px; color:#000; text-decoration:none;"><?php if($kudosNotification==1) {echo '<img style="width:18px; height:18px;" src="assets/kudos.png"/> Kudos Received : '.$newKudosCountInSession; } ?></p>
            </div></a>
            <div class="clear"></div>
            <div id="totalQuestion">
				<?php if(!isset($practiseTest)) { ?>
				
            	<span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span> -
                <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span> :
            	<span id="totalQuestionDigit"><?=$totalQuestions?></span> 
                <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span> :
                <span id="totalQuestionCorrectDigit"><?=$score?></span>
				
				<?php } else { ?> 
					<div style="margin-left:184px;">
            	<span id="totalQuestionText" data-i18n="endSessionReportPage.quesAnswered"></span> -
                <span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span> :
            	<span id="totalQuestionDigit"><?=$totalQuestions?></span> 
				&nbsp;&nbsp;&nbsp;&nbsp;
                <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span> :
                <span id="totalQuestionCorrectDigit"><?=$score?></span>
				</div>
				<?php } ?>
			</div>
            <!--<div id="totalQuestionCorrect">
            	<span id="totalQuestionCorrectText" data-i18n="endSessionReportPage.quesAnsweredCorrectly"></span>:
            	<span id="totalQuestionCorrectDigit"><?=$score?></span>
			</div>-->
            <div class="clear"></div>
			<?php if(!isset($practiseTest) && $childClass>2) { ?>
                <div id="challengeQues">
                	<span id="challengeQuesText" data-i18n="endSessionReportPage.challengeQues"></span> -
                	<span id="challengeQuesAttemptedText" data-i18n="endSessionReportPage.attempted"></span>:
                    <span id="challengeQuesAttemptedDigit"><?=$challengeQuesAttempted?></span>
                    <span id="challengeQuesCorrectText" data-i18n="endSessionReportPage.correct"></span>:
                    <span id="challengeQuesCorrectDigit"><?=$challengeQuesCorrect?></span>
				</div>
			<?php } ?>
                           
			<?php if(isset($practiseTest)) { ?>
				<div style="margin-left:196px;">
					<div id="challengeQues">
						<span id="challengeQuesText">Your Score : </span><span id="challengeQuesAttemptedDigit"><?php echo $scorepractisetest; ?> </span>
					</div>
				</div>
				<a href="practisetest.php" class="removeDecoration"><div id="prevReportImote">Go Back To Practice Zone</div></a>
			<?php } ?>

			<?php if(!isset($practiseTest)) { ?>
            	<a href="sessionWiseReport.php?mode=<?=$mode?>" class="removeDecoration">
            	<div id="prevReportImote">
                	<div id="prevReportText" data-i18n="endSessionReportPage.prevReport"></div>
            	</div></a>
			<?php } ?>
        </div>
		<?php if($theme==2) { ?>
            <div id="dataTableDiv">
                <table width="100%" border="0" class="endSessionTbl" align="center">
                    <tr class="trHead">
						<td>S. No.</td>
                       <!-- <td>Ques. No</td> -->
                        <td>My Answer</td>
                        <td>Correct Answer</td>
                        <td>Result</td>
                    </tr>
                    <tr class="forLowerOnly"><td colspan="4" class="yellowBackground"></td></tr>
                    <tr class="forLowerOnly"><td colspan="4" class="forLowerOnly"></td></tr>
					<?php //echo "<pre>"; print_r($resArr); echo "</pre>";
				
					$counter = 1;
					$totalQuestions = 1;

					$return = array();
					foreach($resArr as $v) {
						$return[$v[8]][] = $v;
					}
					$result = array(); 
					foreach ($return as $key => $value) { 
						if (is_array($value)) { 
						  $result = array_merge($result,$value); 
						} 
					}
		  			function sorting($a, $b) {
						return $a['sessionID'] - $b['sessionID'];
					}
					function sorting_by_date($a, $b) {
						return ($a<$b) ? -1 : 1;
					}
				  	$currntsession = '';$lastElement=0;
					foreach($return as $session_id=> $session_stuff) {
						$sq ="select endTime from adepts_sessionStatus WHERE sessionID='".$session_id."'";
						$endtimeresult_result = mysql_query($sq);
						$endtimeresult_result = mysql_fetch_array($endtimeresult_result);
						//echo $endtimeresult_result['endTime']."<br>";

						if($endtimeresult_result['endTime'] == '0000-00-00 00:00:00' || is_null($endtimeresult_result['endTime']))
						{
							$test = removenull($userID,$childClass,$session_id);
						}

						$timespent_query = "SELECT date_format(startTime,'%d-%b-%Y %H:%i:%s') starttime,date_format(endTime,'%d-%b-%Y %H:%i:%s') endtime,date_format(tmLastQues,'%d-%b-%Y %H:%i:%s') tmLastQues, time_format(timediff(if(endTime>ifnull(tmLastQues,0),endTime,tmLastQues),startTime),'%H:%i:%s') duration, date_format(lastModified,'%d-%b-%Y %H:%i:%s') as lastModified, noOfJumps FROM ".TBL_SESSION_STATUS." WHERE sessionID='".$session_id."'";

						//echo $timespent_query; exit;

						$timespent_result = mysql_query($timespent_query);
						$timespent_data = mysql_fetch_array($timespent_result);
						//$starttime_timestamp = strtotime($timespent_data['startTime']);
					    $sparkiesInThisSession = $timespent_data['noOfJumps'];
												
						$exploded_time_use = explode(":", $timespent_data['duration']);
						if($exploded_time_use[0]>0){
							$exploded_time_use[0] = $exploded_time_use[0]*60 + $exploded_time_use[1];
							$exploded_time_use[1] = $exploded_time_use[2];
						}else{
							$exploded_time_use[0] = $exploded_time_use[1];
							$exploded_time_use[1] = $exploded_time_use[2];
						}
						if ($exploded_time_use[1]>60)
						{
							$exploded_time_use[0] = $exploded_time_use[0] + 1;
							$exploded_time_use[1] = $exploded_time_use[1] - 60;
						}
						else if ($exploded_time_use[1]==60 || $exploded_time_use[1]==6)
						{
							$exploded_time_use[0] = $exploded_time_use[0] + 1;
							$exploded_time_use[1] = "00";
						}
						else if ($exploded_time_use[1]==0 || $exploded_time_use[1]=="")
						{
							$exploded_time_use[1] = "00";
						}	
						
						?>
				
						<tr class="myclass" style='font-size: 10px;padding: 0px;'>
							<td colspan='4' class="sessiontd" id="scroll_<?=$session_id?>">
								<div   style='background-color: red;padding-bottom: 0;margin-top: 2px;'>
									Session ID: <?=$session_id?>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									
									<span data-i18n="endSessionReportPage.currentSessionTime"></span>: <?=$exploded_time_use[0].":".$exploded_time_use[1]?> <span data-i18n="endSessionReportPage.minutes"></span>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									
									Sparkies Earned in this session: <?=$sparkiesInThisSession?>
		                            
		                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		                            
		                            <?php if($kudosNotification==1 && $session_id==$sessionID) {echo '<a style="text-decoration: none; color:#000" href="src/kudos/kudosHomeMidClass.php?wall=my"><div onClick="resetKudosCounter();" style="cursor:pointer; text-decoration: none; margin-left:70%;">Kudos Received In Session: '.$newKudosCountInSession.'</div></a>'; } ?>
								</div>
						 	</td>
						</tr> 
				
						<?php     
						$games_in_session = array();
						foreach ($gamesArray as $value) {
						 	if($value['sessionID']==$session_id)
							 	$games_in_session[] = $value;
						}
						$timed_tests_in_session = array();
						foreach ($timedTestArray as $value) {
						 	if($value['sessionID']==$session_id)
							 	$timed_tests_in_session[] = $value;
						}
						$remedials_in_session = array();
						foreach ($remedialItemAttemptArray as $value) {
						 	if($value['sessionID']==$session_id)
								$remedials_in_session[] = $value;
						}						
						$lastElement=0;
			            foreach($session_stuff as $val) {			            
			            	$nonQuestionContent=array();			            	
							$game_index = count($games_in_session);
			            	while($game_index--) {
				            	if($games_in_session[$game_index]["attemptedOn"] < $val[7]) {
									
									if($games_in_session[$game_index]["activityTimeTaken"]==0)
										$timeTaken	=	$games_in_session[$game_index]["timeTaken"];
									else
										$timeTaken	=	$games_in_session[$game_index]["activityTimeTaken"];
								 	$thisHtml='<tr>
				                        <td colspan="4">
				                        	<div class="activityDispDiv">
				                            	<div class="activityDesc">Game : '.$games_in_session[$game_index]["description"].'</div>
				                                <div class="ativityInfo">
				                                    <div class="activityLevel">Levels cleared: '.$games_in_session[$game_index]["levelCleared"].' out of '.$games_in_session[$game_index]["totalLevel"].'</div>
				                                    <div class="activityTimeTaken">Time taken : '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
				                                    <div class="clear"></div>
				                                </div>
				                            </div>
				                        </td>
				                    </tr>';
				                    $nonQuestionContent['"'.$games_in_session[$game_index]["attemptedOn"].'"']=$thisHtml;
									$lastElement=$games_in_session[$game_index]["description"];
									array_splice($games_in_session, $game_index, 1);   
				            	}   
				            }	
			            
			            	$timed_test_index = count($timed_tests_in_session);
			            	while($timed_test_index--) {
								if($timed_tests_in_session[$timed_test_index]["attemptedOn"] < $val[7])
								{
				                    $thisHtml='<tr>
				                        <td colspan="4">
				                        	<div class="timdTestDispDiv">
												<div id="timedTestIcon"></div>
				                            	<div class="timdTestDesc">Timed Test : '.$timed_tests_in_session[$timed_test_index]["description"].'</div>
				                                <div class="timdTestInfo">
				                                    <div class="timdTestLevel">Status: Complete % correct : '.$timed_tests_in_session[$timed_test_index]["perCorrect"].'%</div>
				                                    <div class="timdTestTimeTaken">Time taken : '.convertSecs($timed_tests_in_session[$timed_test_index]["timeTaken"]).' <span data-i18n="endSessionReportPage.minutes"></span></div>
				                                    <div class="clear"></div>
				                                </div>
				                            </div>
				                        </td>
				                    </tr>';
				                    $nonQuestionContent['"'.$timed_tests_in_session[$timed_test_index]["attemptedOn"].'"']=$thisHtml;
									$lastElement=$timed_tests_in_session[$timed_test_index]["description"]; 
									array_splice($timed_tests_in_session, $timed_test_index, 1);  
				            	}   
			            	}	
							$remedial_index = count($remedials_in_session);
							while($remedial_index--) {
								if($remedials_in_session[$remedial_index]["attemptedOn"] < $val[7]) {
							
									if($remedials_in_session[$remedial_index]["activityTimeTaken"]==0)
										$timeTaken	=	$remedials_in_session[$remedial_index]["timeTaken"];
									else
										$timeTaken	=	$remedials_in_session[$remedial_index]["activityTimeTaken"];
							 		
									$thisHtml='<tr>
				                        <td colspan="4">
				                        	<div class="activityDispDiv">
				                            	<div class="activityDesc">Remedial : '.$remedials_in_session[$remedial_index]["description"].'</div>
				                                <div class="ativityInfo">
				                                    <div class="activityLevel">Levels cleared: '.$remedials_in_session[$remedial_index]["levelCleared"].' out of '.$remedials_in_session[$remedial_index]["totalLevel"].'</div>
				                                    <div class="activityTimeTaken">Time taken : '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
				                                    <div class="clear"></div>
				                                </div>
				                            </div>
				                        </td>
				                    </tr>';
									$nonQuestionContent['"'.$remedials_in_session[$remedial_index]["attemptedOn"].'"']=$thisHtml;
									$lastElement=$remedials_in_session[$remedial_index]["description"];  
									array_splice($remedials_in_session, $remedial_index, 1);  

			        			}
			        		}
			    
			        		ksort($nonQuestionContent);
			        		foreach($nonQuestionContent as $html){
			        			echo $html;
			        		}
							if($val[2] != '') { ?>
									<?php if(strpos($val[1],"WCQ") > -1) { $counter = $counter - 1; $lastElement='WCQ';  ?>
									<tr class="higherAllign">
					               		<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$lastElement?></a></td>
									<?php } else if(strpos($val[1],"Practice")>-1) { $counter = $counter - 1; 
										$practiseModuleId=$val[9];$practiseModuleDesc=$val[11];$practiseModuleAttempt=$val[10];
										if ($lastElement!==$practiseModuleId.'|'.$practiseModuleAttempt){ 
											echo '<tr class="higherAllign headElement practiseModule"><td colspan="4" style="text-align:left;padding-left:36px;">Practice: '.$practiseModuleDesc.' &nbsp; &nbsp; | &nbsp; &nbsp; Attempt: '.$practiseModuleAttempt.'</td></tr>';
										}
										$lastElement=$practiseModuleId.'|'.$practiseModuleAttempt;
										?>
									<tr class="higherAllign practiseModule" rel="<?=$practiseModuleId?>">
					               		<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
									<?php }
									else if(strpos($val[1],"D") > -1) {									
									 ?>
									<tr class="higherAllign">
					               		<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1] ?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
									<?php }
									 else if(strpos($val[1],"COMPREHENSIVE") > -1){?>
					            	 <tr class="higherAllign">
					               	<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')">
					               	<?= str_replace('COMPREHENSIVE','',$val[1])?></a></td>
									<?php }
									 else {
									 $lastElement="Q$counter";?>
									<tr class="higherAllign" rel="<?=$lastElement?>">
									<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$counter?>','<?=$val[0]?>')"><?=$counter?></a></td>
									<?php }?>
					                <td ><?=$val[3]?></td>
					                <td ><?=$val[4]?></td>
					                <td align="center"><div class="<?php if($val[5]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
					            </tr>
								<?php $counter++; 
							} 
							$currntsession = $session_id; 
						}
						if(!empty($games_in_session)) {
							array_reverse($games_in_session);
							foreach($games_in_session as $game) {
								if($games_in_session[$game_index]["activityTimeTaken"]==0)
									$timeTaken	=	$game["timeTaken"];
								else
									$timeTaken	=	$game["activityTimeTaken"];
								$thisHtml='<tr>
			                        <td colspan="4">
			                        	<div class="activityDispDiv">
			                            	<div class="activityDesc">Game: '.$game["description"].'</div>
			                                <div class="ativityInfo">
			                                    <div class="activityLevel">Levels cleared: '.$game["levelCleared"].' out of '.$game["totalLevel"].'</div>
			                                    <div class="activityTimeTaken">Time taken: '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
			                                    <div class="clear"></div>
			                                </div>
			                            </div>
			                        </td>
			                    </tr>';
			                    $nonQuestionContent['"'.$game["attemptedOn"].'"']=$thisHtml;
								
							}
						}
						if(!empty($timed_tests_in_session)) {
							array_reverse($timed_tests_in_session);
							foreach($timed_tests_in_session as $timed_test) {
			                    $thisHtml='<tr>
			                        <td colspan="4">
			                        	<div class="timdTestDispDiv">
											<div id="timedTestIcon"></div>
			                            	<div class="timdTestDesc">Timed Test: '.$timed_test["description"].'</div>
			                                <div class="timdTestInfo">
			                                    <div class="timdTestLevel">Status: Completed &nbsp; &nbsp; &nbsp; &nbsp;Accuracy: '.$timed_test["perCorrect"].'%</div>
			                                    <div class="timdTestTimeTaken">Time taken: '.convertSecs($timed_test["timeTaken"]).' <span data-i18n="endSessionReportPage.minutes"></span></div>
			                                    <div class="clear"></div>
			                                </div>
			                            </div>
			                        </td>
			                    </tr>';
			                    $nonQuestionContent['"'.$timed_test["attemptedOn"].'"']=$thisHtml;
			                }
						}
						if(!empty($remedials_in_session)) {
							array_reverse($remedials_in_session);
							foreach($remedials_in_session as $remedial) {
								if($remedial["activityTimeTaken"]==0)
									$timeTaken	=	$remedial["timeTaken"];
								else
									$timeTaken	=	$remedial["activityTimeTaken"];
								$thisHtml='<tr>
			                        <td colspan="4">
			                        	<div class="activityDispDiv">
			                            	<div class="activityDesc">Remedial: '.$remedial["description"].'</div>
			                                <div class="ativityInfo">
			                                    <div class="activityLevel">Levels cleared: '.$remedial["levelCleared"].' out of '.$remedial["totalLevel"].'</div>
			                                    <div class="activityTimeTaken">Time taken: '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
			                                    <div class="clear"></div>
			                                </div>
			                            </div>
			                        </td>
			                    </tr>';
								$nonQuestionContent['"'.$remedial["attemptedOn"].'"']=$thisHtml;
							}
						}
						ksort($nonQuestionContent);
						foreach($nonQuestionContent as $html){
							echo $html;
						}
						if(count($quesAttemptedArray)>0) { ?>
							<tr>
					            <td colspan="4">
					            	<div class="activityDispDiv">
					                	<div class="activityDesc">Topic Revision Questions</div>
					                    <br/>
					                </div>
					            </td>
					        </tr>
							<?php 
						}
						foreach( $quesAttemptedArray as $key=>$val) { ?>
				            <tr>
				                <td><a style="text-decoration:underline;color:blue;" href="javascript:showPracticeQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
				                <td width="280px"><?=$val[3]?></td>
				                <td width="280px"><?=$val[6]?></td>
				                <td align="center"><div class="<?php if($val[4]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
				            </tr>
							<?php   
						} 
					}?>
				</table>
	        </div>
			<?php 
		}
		else { ?>
			<div id="dataTableDiv">
	            <table width="100%" border="0" class="endSessionTbl" align="center">
	                <tr class="trHead">
	                    <td>Ques. No.</td>
	                    <td>My Answer</td>
	                    <td >Correct Answer</td>
	                    <td>Result</td>
	                </tr>
					<?php if(!isset($practiseTest))
					{ ?>
					
	                    <tr class="forLowerOnly"><td colspan="4" class="yellowBackground"></td></tr>
	                    <tr class="forLowerOnly"><td colspan="4" class="forLowerOnly"></td></tr>
						<?php //echo "<pre>"; print_r($resArr); echo "</pre>";exit;						
						$lastElement=0;
						foreach( $resArr as $val) {
							$nonQuestionContent=array();
							foreach ($timedTestArray as $timedTest){
								if(count($timedTestArray)>0 && $timedTestArray[count($timedTestArray)-1]["attemptedOn"] < $val[7])
								{
									$thisHtml='<tr>
				                        <td colspan="4">
				                        	<div class="timdTestDispDiv">
												<div id="timedTestIcon"></div>
				                            	<div class="timdTestDesc">Timed Test : '.$timedTestArray[count($timedTestArray)-1]["description"].'</div>
				                                <div class="timdTestInfo">
				                                    <div class="timdTestLevel">Status: Complete % correct : '.$timedTestArray[count($timedTestArray)-1]["perCorrect"].'%</div>
				                                    <div class="timdTestTimeTaken">Time taken : '.convertSecs($timedTestArray[count($timedTestArray)-1]["timeTaken"]).' <span data-i18n="endSessionReportPage.minutes"></span></div>
				                                    <div class="clear"></div>
				                                </div>
				                            </div>
				                        </td>
			                    	</tr>';
									$nonQuestionContent['"'.$timedTestArray[count($timedTestArray)-1]["attemptedOn"].'"']=$thisHtml;
								 	$lastElement=$timedTestArray[count($timedTestArray)-1]["description"];
								 	array_pop($timedTestArray); 
			            		}
			            	}
		            		foreach ($gamesArray as $game){
								if(count($gamesArray)>0 && $gamesArray[count($gamesArray)-1]["attemptedOn"] < $val[7]) {
									if($gamesArray[count($gamesArray)-1]["activityTimeTaken"]==0)
										$timeTaken	=	$gamesArray[count($gamesArray)-1]["timeTaken"];
									else
										$timeTaken	=	$gamesArray[count($gamesArray)-1]["activityTimeTaken"];
						 			$thisHtml='
										<tr>
					                        <td colspan="4">
					                        	<div class="activityDispDiv">
													<div id="gameIcon"></div>
					                            	<div class="activityDesc">Game : '.$gamesArray[count($gamesArray)-1]["description"].'</div>
					                                <div class="ativityInfo">
					                                    <div class="activityLevel">Levels cleared: '.$gamesArray[count($gamesArray)-1]["levelCleared"].' out of '.$gamesArray[count($gamesArray)-1]["totalLevel"].'</div>
														<div class="activityTimeTaken">Time taken : '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
					                                    <div class="clear"></div>
					                                </div>
					                            </div>
					                        </td>
					                    </tr>';
					                $nonQuestionContent['"'.$gamesArray[count($gamesArray)-1]["attemptedOn"].'"']=$thisHtml;
					                $lastElement=$gamesArray[count($gamesArray)-1]["description"];
				            		array_pop($gamesArray); 
				        		}
		        			}
		        			foreach ($remedialItemAttemptArray as $remedialItemAttempt){
								if(count($remedialItemAttemptArray)>0 && $remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["attemptedOn"] < $val[7]) {
									if($remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["activityTimeTaken"]==0)
										$timeTaken	=	$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["timeTaken"];
									else
										$timeTaken	=	$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["activityTimeTaken"];
								 	$thisHtml='
										<tr>
					                        <td colspan="4">
					                        	<div class="activityDispDiv">
					                            	<div class="activityDesc">Remedial : '.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["description"].'</div>
					                                <div class="ativityInfo">
					                                    <div class="activityLevel">Levels cleared: '.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["levelCleared"].' out of '.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["totalLevel"].'</div>
					                                    <div class="activityTimeTaken">Time taken : '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
					                                    <div class="clear"></div>
					                                </div>
					                            </div>
					                        </td>
					                    </tr>';
					                $nonQuestionContent['"'.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["attemptedOn"].'"']=$thisHtml;
					                $lastElement=$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["description"];
				            		array_pop($remedialItemAttemptArray); 
				        		}
				        	}
				 			ksort($nonQuestionContent);
				 			foreach($nonQuestionContent as $html){
				 				echo $html;
				 			}
				 			if($val[2] != '') { ?>
				 				<?php if(strpos($val[1],"Practice")>-1) { 
				 					$practiseModuleId=$val[9];$practiseModuleDesc=$val[11];$practiseModuleAttempt=$val[10];
				 					if ($lastElement!==$practiseModuleId.'|'.$practiseModuleAttempt){
				 						echo '<tr class="higherAllign headElement practiseModule"><td colspan="4" style="text-align:left;padding-left:20px;">Practice: '.$practiseModuleDesc.' &nbsp; &nbsp; | &nbsp; &nbsp; Attempt: '.$practiseModuleAttempt.'</td></tr>';
				 					}
				 					$lastElement=$practiseModuleId.'|'.$practiseModuleAttempt;
				 					?>
								<tr class="higherAllign practiseModule" rel="<?=$practiseModuleId?>">
					               	<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
					            <?php }else if(strpos($val[1],"WCQ") > -1){$counter = $counter - 1; $lastElement='WCQ';?>
					            <tr class="higherAllign">
					               	<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$lastElement?></a></td>
					            <?php }
					            else if(strpos($val[1],"COMPREHENSIVE") > -1){?>
					            	 <tr class="higherAllign">
					               	<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')">
					               	<?= str_replace('COMPREHENSIVE','',$val[1])?></a></td>
								<?php }
					             else { $lastElement="Q$counter";?>
					            <tr class="higherAllign">
					               	<td><a style="text-decoration:underline;color:blue;" href="javascript:showQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
								<?php } ?>
					                <td ><?=$val[3]?></td>
					                <td ><?=$val[4]?></td>
					                <td align="center">
										<?php
											if($val[5]==0) echo '<div class="wrongAnsIcon"></div>';
											else if($val[5]==1) echo '<div class="correctAnsIcon"></div>';
											else echo "-";
										?>
									</td>
					            </tr>
							<?php }
						}
					}
					if(count($quesAttemptedArray)>0) {
					 	?>
					 	<tr>
		                    <td colspan="4">
		                    	<div class="activityDispDiv">
		                        	<div class="activityDesc">Topic Revision Questions</div>
		                            <br/>
		                        </div>
		                    </td>
		                </tr>
					 
						<?php 
					}
					foreach( $quesAttemptedArray as $key=>$val) { ?>
			            <tr>
			                <td><a style="text-decoration:underline;color:blue;" href="javascript:showPracticeQues('<?=$val[2]?>','<?=$val[1]?>','<?=$val[0]?>')"><?=$val[1]?></a></td>
			                <td width="280px"><?=$val[3]?></td>
			                <td width="280px"><?=$val[6]?></td>
			                <td align="center"><div class="<?php if($val[4]==0) echo 'wrongAnsIcon'; else echo 'correctAnsIcon';?>"></div></td>
			            </tr>
						<?php 
					}
					$nonQuestionContent=array();
					foreach ($timedTestArray as $timedTest){
						if(count($timedTestArray)>0)
						{
							$thisHtml='<tr>
		                        <td colspan="4">
		                        	<div class="timdTestDispDiv">
										<div id="timedTestIcon"></div>
		                            	<div class="timdTestDesc">Timed Test : '.$timedTestArray[count($timedTestArray)-1]["description"].'</div>
		                                <div class="timdTestInfo">
		                                    <div class="timdTestLevel">Status: Complete % correct : '.$timedTestArray[count($timedTestArray)-1]["perCorrect"].'%</div>
		                                    <div class="timdTestTimeTaken">Time taken : '.convertSecs($timedTestArray[count($timedTestArray)-1]["timeTaken"]).' <span data-i18n="endSessionReportPage.minutes"></span></div>
		                                    <div class="clear"></div>
		                                </div>
		                            </div>
		                        </td>
	                    	</tr>';
							$nonQuestionContent['"'.$timedTestArray[count($timedTestArray)-1]["attemptedOn"].'"']=$thisHtml;
						 	array_pop($timedTestArray); 
	            		}
	            	}
	        		foreach ($gamesArray as $game){
						if(count($gamesArray)>0) {
							if($gamesArray[count($gamesArray)-1]["activityTimeTaken"]==0)
								$timeTaken	=	$gamesArray[count($gamesArray)-1]["timeTaken"];
							else
								$timeTaken	=	$gamesArray[count($gamesArray)-1]["activityTimeTaken"];
				 			$thisHtml='
								<tr>
			                        <td colspan="4">
			                        	<div class="activityDispDiv">
											<div id="gameIcon"></div>
			                            	<div class="activityDesc">Game : '.$gamesArray[count($gamesArray)-1]["description"].'</div>
			                                <div class="ativityInfo">
			                                    <div class="activityLevel">Levels cleared: '.$gamesArray[count($gamesArray)-1]["levelCleared"].' out of '.$gamesArray[count($gamesArray)-1]["totalLevel"].'</div>
												<div class="activityTimeTaken">Time taken : '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
			                                    <div class="clear"></div>
			                                </div>
			                            </div>
			                        </td>
			                    </tr>';
			                $nonQuestionContent['"'.$gamesArray[count($gamesArray)-1]["attemptedOn"].'"']=$thisHtml;
		            		array_pop($gamesArray); 
		        		}
	    			}
	    			foreach ($remedialItemAttemptArray as $remedialItemAttempt){
						if(count($remedialItemAttemptArray)>0) {
							if($remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["activityTimeTaken"]==0)
								$timeTaken	=	$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["timeTaken"];
							else
								$timeTaken	=	$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["activityTimeTaken"];
						 	$thisHtml='
								<tr>
			                        <td colspan="4">
			                        	<div class="activityDispDiv">
			                            	<div class="activityDesc">Remedial : '.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["description"].'</div>
			                                <div class="ativityInfo">
			                                    <div class="activityLevel">Levels cleared: '.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["levelCleared"].' out of '.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["totalLevel"].'</div>
			                                    <div class="activityTimeTaken">Time taken : '.convertSecs($timeTaken).' <span data-i18n="endSessionReportPage.minutes"></span></div>
			                                    <div class="clear"></div>
			                                </div>
			                            </div>
			                        </td>
			                    </tr>';
			                $nonQuestionContent['"'.$remedialItemAttemptArray[count($remedialItemAttemptArray)-1]["attemptedOn"].'"']=$thisHtml;
		            		array_pop($remedialItemAttemptArray); 
		        		}
		        	}
		 			ksort($nonQuestionContent);
		 			foreach($nonQuestionContent as $html){
		 				echo $html;
		 			}
		        	?>
            	</table>
            </div>
			<?php 
		} ?>
        </div>
	</div>
    <div style="display:none">
        <div id="sendFeedback">
            <div id="feedbakText">Do you want to give feedback</div><br/>
            <div class="buttonTemp1" onClick="javascript:window.location.href='surveyForm.php';">Yes</div>
            <div class="buttonTemp1" onClick="javascript:$('#cboxClose').click();">Remind me later</div>
        </div>
    </div>

    <form id="frmReport" action="quesWiseReport.php" method="POST">
        <input type="hidden" name="userID" id="userID" value="<?=$userID?>">
        <input type="hidden" name="sessionIDNew" id="sessionIDNew" value="<?=$sessionID?>">
		<input type="hidden" name="reportDate" id="reportDate" value="<?=$reportDate?>">
		<input type="hidden" name="modeCheck" id="modeCheck" value="2">
        <input type="hidden" name="qcode" id="hidqcode">
        <input type="hidden" name="srno" id="hidsrno">
        <input type="hidden" name="qno" id="hidqno">
        <input type="hidden" name="bucketAttemptID" id="bucketAttemptID" value="<?=isset($_SESSION['bucketAttemptID'])?$_SESSION['bucketAttemptID']:""?>">
        <input type="hidden" name="mode" id="mode" value="normal">
    </form>
	

<?php 
$parentFileNameStr = explode("/",$_SERVER['HTTP_REFERER']);
$parentFileNameStr1 = array_pop($parentFileNameStr);
$parentFileNameStr2 = explode("?",$parentFileNameStr1);
$parentFileNameStr = $parentFileNameStr2[0];
//echo $parentFileNameStr;

if($parentFileNameStr=="question.php")
{
	if($endType==0)
		mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'S1(".$totalQuestions.")') WHERE sessionID=".$sessionID) or die(mysql_error());
	else
		mysql_query("UPDATE ".TBL_SESSION_STATUS." SET endType=concat_ws(',',endType,'".$endType."','S1(".$totalQuestions.")') WHERE sessionID=".$sessionID) or die(mysql_error());
}

if(isset($_SESSION['examCornerCluster']) && $_SESSION['examCornerCluster']==1)
{
	unset($_SESSION['bucketAttemptID'],$_SESSION['bucketClusterCode'],$_SESSION['examCornerCluster'],$_SESSION["importantQuestions"],$_SESSION['bucketTopicName']);
}

include("footer.php");?>

<?php


function convertSecs($secs)
{
	if($secs==0)
		return "0";
	else if($secs<60)
		return "0:".str_pad($secs,2,"0",STR_PAD_LEFT);
	else
	{
		$temp = explode(".",$secs/60);
		return str_pad($temp[0],2,"0",STR_PAD_LEFT). ":". str_pad($secs%60,2,"0", STR_PAD_LEFT);
	}
}

function getChallengeQuesAttemptedInSession($userID, $sessionID)
{
	$challengeQuesArray = array();
	$cq_query = "SELECT qcode,A,S,R,ttAttemptID,questionNo,sessionID
                     FROM   adepts_ttChallengeQuesAttempt
					 WHERE  userID=$userID AND sessionID=".$sessionID." ORDER BY srno";
	//echo $cq_query;
	$cq_result = mysql_query($cq_query) or die(mysql_error());
	$cqno = 0;
	while ($cq_line=mysql_fetch_array($cq_result))
	{
		$challengeQuesArray[$cqno][0] = $cq_line['qcode'];
		$challengeQuesArray[$cqno][1] = $cq_line['A'];
		$challengeQuesArray[$cqno][2] = $cq_line['S'];
		$challengeQuesArray[$cqno][3] = $cq_line['R'];
		$challengeQuesArray[$cqno][4] = $cq_line['questionNo'];
		$challengeQuesArray[$cqno][5] = $cq_line['ttAttemptID'];
		$challengeQuesArray[$cqno][6] = $cq_line['sessionID'];
		$cqno++;
	}
	return $challengeQuesArray;
}
function getChallengeQuesAttemptedInDaySession($userID, $date)
{
	$challengeQuesArray = array();
	$cq_query = "SELECT qcode,A,S,R,ttAttemptID,questionNo, sessionID
                     FROM   adepts_ttChallengeQuesAttempt
					 WHERE  userID=$userID AND lastModified BETWEEN '$date' AND '$date 23:59:59' ORDER BY srno";
	//echo $cq_query;
	$cq_result = mysql_query($cq_query) or die(mysql_error());
	$cqno = 0;
	while ($cq_line=mysql_fetch_array($cq_result))
	{
		$challengeQuesArray[$cqno][0] = $cq_line['qcode'];
		$challengeQuesArray[$cqno][1] = $cq_line['A'];
		$challengeQuesArray[$cqno][2] = $cq_line['S'];
		$challengeQuesArray[$cqno][3] = $cq_line['R'];
		$challengeQuesArray[$cqno][4] = $cq_line['questionNo'];
		$challengeQuesArray[$cqno][5] = $cq_line['ttAttemptID'];
		$challengeQuesArray[$cqno][6] = $cq_line['sessionID'];
		$cqno++;
	}
	return $challengeQuesArray;
}
function getClusterdesc($clusterCode)
{
	$sq	=	"SELECT cluster FROM adepts_clusterMaster WHERE clusterCode='$clusterCode'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	return $rw[0];
}

function showSurveyAlert($userID,$sessionID)
{
	$sqF	=	"SELECT userID FROM adepts_feedbackresponse WHERE userID=$userID AND qid=24";
	$rsF	=	mysql_query($sqF);
	if(mysql_num_rows($rsF)==0)
	{
		$sq	=	"SELECT startTime FROM ".TBL_SESSION_STATUS." WHERE sessionID=".$sessionID;
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_array($rs);
		$startTime	=	$rw[0];
		$diff = strtotime(date("Y-m-d H:i:s")) - strtotime($startTime);
		if($diff > 1200)
			return true;
		else
			return false;
	}
	else
	{
		return false;
	}
}


function removenull($userID,$childClass,$sessionid)
{
	
    $yesterday = date("Ymd", strtotime("today"));
   
        $userIDStr = "";
        $userIDStr = $userID;
        if($userIDStr!="")
        {

            $query = "SELECT z.sessionID sessID, z.lastModified lastMod, count(distinct a.srno) totalq, count(distinct g.srno) totalqd, count(distinct b.srno) totalcq, count(distinct c.timedTestID) totTmTst, count(distinct d.gameID) totgms,  count(distinct e.srno) totmonrev, count(distinct f.srno) tottoprev,
            greatest(IFNULL(max(a.lastModified),0),IFNULL(max(b.lastModified),0),IFNULL(max(c.lastModified),0),IFNULL(max(d.lastModified),0),IFNULL(max(e.lastModified),0),IFNULL(max(f.lastModified),0),IFNULL(max(g.lastModified),0)) maxLastTime
            FROM ((((((adepts_sessionStatus z LEFT JOIN adepts_teacherTopicQuesAttempt_class".$childClass." a on z.sessionID=a.sessionID)
            LEFT JOIN  adepts_ttChallengeQuesAttempt b on z.sessionID=b.sessionID)
            LEFT JOIN adepts_timedTestDetails c on z.sessionID=c.sessionID)
            LEFT JOIN adepts_userGameDetails d on z.sessionID=d.sessionID)
            LEFT JOIN adepts_revisionSessionDetails e on z.sessionID=e.sessionID)
            LEFT JOIN adepts_topicRevisionDetails f on z.sessionID=f.sessionID)
			LEFT JOIN adepts_diagnosticQuestionAttempt g on z.sessionID=g.sessionID
			LEFT JOIN educatio_adepts.kst_diagnosticQuestionAttempt h on z.sessionID=h.sessionID
            WHERE z.userID = $userIDStr AND startTime_int=$yesterday group by z.sessionID order by z.sessionID";


                $result = mysql_query($query) or die(mysql_error());

                while ($line = mysql_fetch_array($result)) {
                    if($line['maxLastTime']!=0 && $line['sessID'] == $sessionid)
                    {
                        $update_query = "UPDATE adepts_sessionStatus SET ";
                        $update_query .= "totalQ=".($line['totalq']+$line['totalqd']).",";
                        $update_query .= "totalCQ=".$line['totalcq'].",";
                        $update_query .= "totalTmTst=".$line['totTmTst'].",";
                        $update_query .= "totalGms=".$line['totgms'].",";
                        $update_query .= "totalMonRevQ=".$line['totmonrev'].",";
                        $update_query .= "totalTopRevQ=".$line['tottoprev'].",";
                        $update_query .= "tmLastQues = '".$line['maxLastTime']."',";
                        $update_query .= "lastModified='".$line['lastMod']."' WHERE  sessionID=".$line['sessID'];

						//echo $update_query."<br>";
                        mysql_query($update_query) or die($update_query."<br>".mysql_error());
                    }
                }
        }	
}
?>
