<?php
	@include_once("check1.php");
	include_once("classes/clsUser.php");
	include_once("constants.php");
	include("functions/functions.php");
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	$userID = $_SESSION['userID'];
	$objUser = new User($userID);

	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];

	$childName 	   = $objUser->childName;
	$schoolCode    = $objUser->schoolCode;
	$childClass    = $objUser->childClass;
	$childSection  = $objUser->childSection;
	$category 	   = $objUser->category;
	$subcategory   = $objUser->subcategory;

	if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0)
	{
		header("Location:ti_home.php");
		exit;
	}

	$broadTopics = array();
	$teacherTopics = array();
	$activeTopics = array();
	$completeTopics = array();
	$incompleteTopics = array();
	$query = "SELECT exerciseCode, result FROM adepts_ncertHomeworkStatus WHERE userID=$userID";
	$result = mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		if($row[1] == 'SUCCESS')
			array_push($completeTopics,$row[0]);
		else
			array_push($incompleteTopics,$row[0]);
	}
	$activeQuery = "SELECT exerciseCode FROM adepts_ncertHomeworkActivation WHERE activationDate<='".date("Y-m-d")."' AND schoolCode='$schoolCode'";
	
	if($childClass != "")
		$activeQuery .= " AND class=$childClass";
	if($childSection != "")
		$activeQuery .= " AND section ='$childSection'";
	$result = mysql_query($activeQuery) or die(mysql_error());
	while($row = mysql_fetch_array($result))
	{
		array_push($activeTopics,$row[0]);
	}
	
	$whereCond = "";
	if(!isset($_POST['seeAll']) || $_POST['seeAll']!="yes")
	{
		$topicsToShow = array_merge($incompleteTopics,$activeTopics);
		$topicsToShowList = "'".implode("','",$topicsToShow)."'";
		$whereCond = " AND b.exerciseCode IN (".$topicsToShowList.")";
	}
	$query = "SELECT description, status, a.exerciseCode, chapterName, exerciseNo, pageNo, chapterNo, deactivationDate FROM adepts_ncertExerciseMaster a LEFT JOIN adepts_ncertHomeworkActivation b ON a.exerciseCode=b.exerciseCode WHERE a.class='$childClass' ";
	if($childSection != "")
	$query.=" AND b.section='$childSection' ";
	$query.=" AND schoolCode='$schoolCode' AND status='Live' ".$whereCond." ORDER BY chapterNo";
	$result = mysql_query($query) or die(mysql_error());
	if(mysql_num_rows($result)!=0)
	{
		$flag = 0;
		$topic = "";
		while ($line=mysql_fetch_array($result))
		{
			$lineCluster = $line['chapterName'];
			if($topic!=$lineCluster)
			{
				array_push($broadTopics,$lineCluster);
				$topic = $lineCluster;
				$srno=0;
			}
			$teacherTopics[$topic][$srno][0] = $line[0];
			$teacherTopics[$topic][$srno][1] = $line[1];
			$teacherTopics[$topic][$srno][2] = $line[2];
			$teacherTopics[$topic][$srno][3] = $line[4];
			$teacherTopics[$topic][$srno][4] = $line[5];
			$teacherTopics[$topic][$srno][5] = $line[6];
			$teacherTopics[$topic][$srno][6] = ($line[7] != "")?date("d-m-Y",strtotime($line[7])):"";
			$srno++;
		}
	}
	$totalQuestions = array();
	$query = "SELECT COUNT(qcode), a.exerciseCode, COUNT(DISTINCT groupID) FROM adepts_ncertQuestions a, adepts_ncertExerciseMaster b WHERE a.exerciseCode=b.exerciseCode AND a.status='3' AND b.class='$childClass' AND b.status='Live'".$whereCond." GROUP BY a.exerciseCode";
	$result= mysql_query($query);
	while($row = mysql_fetch_array($result))
	{
		$totalQuestions[$row[1]][0] = $row[0];
		$totalQuestions[$row[1]][1] = $row[2];
	}
	$sparkieImage = $_SESSION['sparkieImage'];
?>

<?php include("header.php"); ?>

<title>NCERT Exercise Page</title>

<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type='text/javascript' src='libs/combined.js'></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/closeDetection.js"></script>-->
<script>
var langType = '<?=$language;?>';
</script>
	<?php
	if($theme==2) { ?>
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
    <link href="css/homeworkSelection/midClass.css?ver=2" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight -240;
			$('#reportContainer').css("height",a);
		}
	</script>
	<?php } else if($theme==3) { ?>
			<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
            <link href="css/homeworkSelection/higherClass.css?ver=2" rel="stylesheet" type="text/css">
	<?php }?>
    <script>
	$(document).ready(function(e) {
		if (window.location.href.indexOf("localhost") > -1) {	
		    var langType = 'en-us';
		}
	<?php if(isset($_GET['instruction']) && $_GET['instruction'] == "yes") { ?>
		openInstruction();
	<?php } ?>
		i18n.init({ lng: langType,useCookie: false }, function(t) {
			$(".translation").i18n();
			$(document).attr("title",i18n.t("homeworkSelectionPage.title"));
			if($("#exitMsg"))
			{
				$("#exitMsg").html(i18n.t("homeworkSelectionPage.title"));
			}
		});
	});
	function load(){
			var a= window.innerHeight - (170);
			$('#reportContainer').css({"height":a+"px"});
			$('#topicInfoContainer').css({"height":a+"px"});
			$('#menuBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			<?php if($theme==2) { ?>
				var a= window.innerHeight - (240);
				$('#reportContainer').css({"height":a+"px"});
			<?php } ?>
		}
	var click=0;
	function openInstruction()
	{  
		$.fn.colorbox({'href':'#instruction','inline':true,'open':true,'escKey':true, 'height':350, 'width':500});
	}
	function ncertHomeworkAttempt(exerciseCode, linkType)
	{
		setTryingToUnload();
		if(linkType == "question")
		{
			document.getElementById("exerciseCode").value = exerciseCode;
			document.getElementById("frmTeacherTopicSelection").action = "controller.php";
			document.getElementById("frmTeacherTopicSelection").submit();
		}
		else
		{
			document.getElementById("exercise").value = exerciseCode;
			document.getElementById("frmTeacherTopicSelection").action = "topicWiseQuesTrail.php";
			document.getElementById("frmTeacherTopicSelection").submit();
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
		$("#main_bar").animate({'width':'26px'},600);
		$("#plus").animate({'margin-left':'7px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}
	</script>
</head>
<body onLoad="load();" onResize="load();" class="translation">
	<div id="top_bar" class="top_bar_part4">
		<div class="logo">
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
		<div id="logout" class="hidden">
        	<div class="logout" onClick="logoff()"></div>
        	<div class="logoutText"  onclick="logoff()">Logout</div>
        </div>
    </div>

	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="topic">
				<div id="clickText" data-i18n="homeworkSelectionPage.activityTxt"></div>
				<div id="home">
					<div class="icon_text1" style="cursor: default;"><span class="textUppercase" id="homeText" data-i18n="dashboardPage.home" onClick="getHome()"></span> > <font color="#606062"><span class="textUppercase" data-i18n="homeworkSelectionPage.title"></span></font></div>
				</div>
			</div>
			<div class="class">
				<strong><span data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="locked" onClick="javascript: setTryingToUnload(); document.frmTeacherTopicSelection.submit();">
				<div class="icon_text textUppercase" data-i18n="homeworkSelectionPage.allExer"></div>
				<div id="pointed">
				</div>
			</div>
            <div id="new" onClick="openInstruction()">
                <div class="icon_text textUppercase" data-i18n="homeworkSelectionPage.readInst"></div>
                <div id="pointed">
                </div>
            </div>
		</div>
		<div id="info_bar" class="forHighestOnly">
			<div id="topic">
                <div id="home">
                	<a href="home.php">
                        <div id="homeIcon"></div>
                    </a>
                    <div id="homeText" class="hidden"><span class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard"></span></font></div>
                </div>
				
				<a href="home.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div></a>
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
            <a href="sessionWiseReport.php"><div id="sessionWiseReport" class="textUppercase" data-i18n="dashboardPage.sessionWise"></div></a>
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
<form name="frmTeacherTopicSelection" action="homeworkSelection.php" id="frmTeacherTopicSelection" method="POST">
	<div id="reportContainer">
	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="report" onClick="setTryingToUnload();document.frmTeacherTopicSelection.submit();">
					<span id="reportText">ALL EXERCISES</span>
					<div id="reportIcon" class="circle11"></div>
				</div>
				<div id="questionTrail" onClick="openInstruction()">
					<span id="questionTrailText">READ DIRECTIONS</span>
					<div id="questionTrailIcon" class="circle11"></div>
				</div>
				<div class="empty">
				</div>
			</div>
			</div>
<?php
	if(count($broadTopics)==0)
	{
		echo "<div align='center' id='exitMsg'><strong><br/><br/><br/>No exercises are activated!<br/><br/>Please contact your Mindspark co-ordinator.</strong></div>";
	}
	else
	{
	?>
	<div align="center">
    <img src="assets/right.gif" style="margin-left:30px;" alt="Topic Status" height="24px" width="24px" />&nbsp; - <span data-i18n="homeworkSelectionPage.completed"></span>
    <img src="assets/incomplete.png" style="margin-left:30px;" alt="Topic Status" height="24px" width="24px" />&nbsp; - <span data-i18n="homeworkSelectionPage.pending"></span>
    <img src="assets/notAssigned.png" style="margin-left:30px;" alt="Topic Status" height="24px" width="24px" />&nbsp; - <span data-i18n="homeworkSelectionPage.notAssigned"></span>
	</div><br /><br>

		<table id="heading" width="86%" align="center">
			<tr>
				<td class="headingText" align="center" id="head" data-i18n="homeworkSelectionPage.exerName" width="15%"></td>
				<td class="headingText" align="center" data-i18n="homeworkSelectionPage.dueDate" width="25%"></td>
				<td class="headingText" align="center" data-i18n="homeworkSelectionPage.totalQues" width="15%"></td>
				<td class="headingText" align="center" data-i18n="homeworkSelectionPage.quesAttempted" width="25%"></td>
				<td class="headingText" align="center" data-i18n="homeworkSelectionPage.accuracy" width="25%"></td>
			</tr>
		</table>
		<br/><br/>

<?php	for($i=0; $i<count($broadTopics); $i++)
		{ ?>
			<div class="topicName"><?=$broadTopics[$i]?></div><br/><br/>
	        <table class="topicPosition" align="center" width="85%">
<?php       $ttArray = $teacherTopics[$broadTopics[$i]];
            for($j=0; $j<count($teacherTopics[$broadTopics[$i]]); $j++)
            {
				$statusImg = "notAssigned.png";
				$link = false;
				$linkType = "";
				if(in_array($teacherTopics[$broadTopics[$i]][$j][2],$activeTopics))
				{
					$statusImg = "incomplete.png";
					$link = true;
					$linkType = "question";
				}
				if(in_array($teacherTopics[$broadTopics[$i]][$j][2],$completeTopics))
				{
					$statusImg = "right.gif";
					$link = true;
					$linkType = "report";
				}
				else if(in_array($teacherTopics[$broadTopics[$i]][$j][2],$incompleteTopics))
				{
					$statusImg = "incomplete.png";
					$link = true;
					$linkType = "question";
				}
				$details = getAttemptedExDetail($teacherTopics[$broadTopics[$i]][$j][2]);
			?>
				<tr class="exContainer">
				<td class="exercise" width="15%"><a href="javascript:void(0)" class="topic <?php if(!$link) echo "disabledLink"; ?>" <?php if($link){ ?>onclick="ncertHomeworkAttempt('<?=$teacherTopics[$broadTopics[$i]][$j][2]?>','<?=$linkType?>')"<?php } ?>><span data-i18n="homeworkSelectionPage.exercise"></span> <?=$teacherTopics[$broadTopics[$i]][$j][5].".".$teacherTopics[$broadTopics[$i]][$j][3]?></a></td>
				<td class="date" width="25%"><?=$teacherTopics[$broadTopics[$i]][$j][6]?></td>
				<td class="TQ" width="15%">
                	<?php
						if(isset($totalQuestions[$teacherTopics[$broadTopics[$i]][$j][2]]))
						{
							$questions = $totalQuestions[$teacherTopics[$broadTopics[$i]][$j][2]][0];
							$groups = $totalQuestions[$teacherTopics[$broadTopics[$i]][$j][2]][1];
							echo "$groups ($questions)";
						}
						else
							echo "0 (0)";
					?>
                </td>
				<td class="QA" width="25%"><?php echo "$details[2] ($details[0])"; ?></td>
				<td class="accuracy" width="25%"><?php if ($details[1]==0) echo "0.0"; else echo round($details[1],1)?></td>
				</tr>
<?php		} ?>
		</table>
		<br/>
		<br/>
<?php	} ?>
        <br/>
        <br/>
			<span id="belowText" data-i18n="homeworkSelectionPage.exerciseText"></span>
	</div>
	<?php
	}
	?>
<input type="hidden" name="mode" id="mode" value="ncert">
<input type="hidden" name="trailType" id="trailType" value="ncert">
<input type="hidden" name="exerciseCode" id="exerciseCode">
<input type="hidden" name="exercise" id="exercise">
<input type="hidden" name="section" id="mode" value="<?=$childSection?>">
<input type="hidden" name="student_userID" id="student_userID" value="<?=$userID?>">
<input type="hidden" name="accessFromStudentInterface" id="accessFromStudentInterface" value="1">
<input type="hidden" name="userType" id="userType" value="">
</form>
	</div>

<div style="display:none">
	<div id="instruction" style="font-family: 'Conv_HelveticaLTStd-Light';font-size:18px;">
		<h3 align="center">Instructions</h3>
    	<ol>
        	<li>Answers to questions will be auto saved, but will be submitted to the teacher only after clicking 'Submit' button.</li>
			<li>Once submitted, the answers cannot be changed.</li>
			<li>If any answer in an exercise is submitted after the due date, the submission will be considered late.</li>
			<li>Please keep checking exercises submitted to teacher for comments on your answers.</li>
        </ol>
    </div>
</div>

<?php include("footer.php"); ?>

<?php
function getAttemptedExDetail($exerciseCode)
{
	$detail = array(0,0);
	$query = "SELECT MAX(noOfQuesAttempted), MAX(perCorrect), COUNT(DISTINCT(groupID)) FROM adepts_ncertHomeworkStatus a, adepts_ncertQuesAttempt b, adepts_ncertQuestions c WHERE b.qcode=c.qcode AND a.exerciseCode=b.exerciseCode AND a.exerciseCode=c.exerciseCode AND a.userID='".$_SESSION['userID']."' AND a.exerciseCode='$exerciseCode' AND R!=-1";
	$result = mysql_query($query);
	if(mysql_num_rows($result) != 0)
	{
		$row = mysql_fetch_array($result);
		$detail[0] = $row[0];
		$detail[1] = $row[1];
		$detail[2] = $row[2];
	}
	if($detail[0] == "")
	{
		$detail[0] = 0;
	}
	if($detail[1] == "")
	{
		$detail[1] = 0;
	}
	if($detail[2] == "")
	{
		$detail[2] = 0;
	}
	return($detail);
}
?>