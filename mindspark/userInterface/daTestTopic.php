<?php
@include("check1.php");
include("classes/clsUser.php");

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR);

if(!isset($_SESSION['userID']))
{
	header("Location: logout.php");
	exit;
}
$userID = $_SESSION['userID'];
$childClass = $_SESSION['childClass'];
$user = new User($userID);
?>

<?php include("header.php");?>
<?php 
if($theme == 2){
?>
<link rel="stylesheet" href="css/feedBackForm/midClass.css" />
<link rel="stylesheet" href="css/commonMidClass.css" />
<link rel="stylesheet" href="css/dashboard/midClass.css" />
<?php 	
}else if($theme == 3){
	?>
		<link rel="stylesheet" href="css/commonHigherClass.css" />
    	<link rel="stylesheet" href="css/feedBackForm/higherClass.css" />
		<link rel="stylesheet" href="css/dashboard/higherClass.css" />
    	
	<?php 
}
?>
<title>Mindspark  - Super Test Reports</title>
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
var langType = '<?=$language;?>';
</script>
<script>
function load(){
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
	if(androidVersionCheck==1) {
		$('#frmFeedback').css("height","auto");
		$('#main_bar').css("height",$('#frmFeedback').css("height"));
		$('#menu_bar').css("height",$('#frmFeedback').css("height"));
		$('#sideBar').css("height",$('#frmFeedback').css("height"));
	}
}
	function logoff()
	{
		window.location="logout.php";
	}

	function getReport(data)
	{
		$("#daPaperCode").val(data);
		$("#reportForm").submit();
	}

	function getHome()
	{
		window.location.href	=	"home.php";
	}
	 var click=0;
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

 <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>daTestTopic</title>
</head>
<body class="translation" onLoad="load()">

<div id="top_bar">
		<div class="logo">
		</div>
        <div id="logout" onClick="logoff()" class="linkPointer">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		
    </div>
	
	<div id="container" style='height:100% !important;' class="da-test-topic">


		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="forHigherOnly"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase">Super Test Report</span></font></div>
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
            <div class="clear"></div>
		</div>
		<div id="info_bar" class="forHighestOnly">
			<div id="topic">
                <div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText" class="hidden"><span onClick="getHome()" class="textUppercase" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase" data-i18n="dashboardPage.dashboard" ></span></font></div>
                </div>
                
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" >SUPER TEST REPORT</span></div>
                </div>
				<div class="arrow-right"></div>
				
				<div class="clear"></div>
			</div>
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
        <div id="feedbackFormMainDiv" style="min-height:420px">
			<div id="school" class="forLowerOnly hidden">School: <?=$user->schoolName?></div>
			
			<div id="feedbackFormMain">
				<div id="feedbackInfo" class="forLowerOnly hidden">Please take a few minutes out to answer the questions below</div>

    <form id='reportForm' action="daTestReport.php" method="POST">
	<?php 
		$sqlCheck = "SELECT A.paperCode, class, topicName, B.lastmodified FROM da_paperCodeMaster A, da_questionTestStatus B 
			 		 WHERE userID=$userID AND A.paperCode=B.paperCode AND status=3 AND class=$childClass";
		$result =  mysql_query($sqlCheck) or die(mysql_error().$sqlCheck);
		while($rw=mysql_fetch_array($result))
		{
                        $scoreOutOfTen = getScore($userID, $rw['paperCode']);
                        $timeSpent = getTimeSpentOnTest($userID, $rw['paperCode']);
	 ?>
		<table>
		<tr>
			<td>
				<div class="topicDetailsMainDiv">
						<div style="visibility:hidden" class="topicIconsDiv">
							<div class="starIcon"></div>
						</div>
						<div class="topicProgressMainDiv">
							<div class="topicProgressInnerDiv">
								<div class="topicName "><? echo $rw['topicName']; ?></div>
								
								<div style="width:378.82px;margin-top: 33px;" class="topicProgressGrad "></div>
							</div>
						</div>
				</div>
			</td>
            <td style="padding-top: 66px;">
                    <div><strong>Score:</strong> <?php echo $scoreOutOfTen."/10"; ?></div>
                    <div><strong>Time Spent:</strong> <?php echo $timeSpent; ?></div>
            </td>
			<td  class="report_link removeDecoration" style=' padding-left: 60px;padding-top: 66px;'>
				<div class="reportDiv" style="cursor:pointer;display:block;">
					<div class="reportDivImote" onClick="getReport('<?=$rw['paperCode']?>')" ></div>
					<div data-i18n="dashboardPage.report" class="reportDivText " onClick="getReport('<?=$rw['paperCode']?>')" >REPORT</div>
				</div>
			</td>
		</tr>
		</table>
	<? } ?>
		<input type='hidden' value='' name='daPaperCode' id='daPaperCode'>
    </form>
	</div>
	</div>
<?php include("footer.php");?>

<?php 
function getScore($userID, $daPaperCode)
{
        $scoreOutOfTen = 0;
        $query= "SELECT id, qno, qcode, R FROM da_questionAttemptDetails WHERE userID=".$userID." and paperCode = '".$daPaperCode."' ORDER BY qno";
	$result=mysql_query($query) or die(mysql_error().$sq);	
	while($line=mysql_fetch_array($result))
	{		
		$totalScore += $line["R"];					
		$totalQuestions++;
	}
        if($totalQuestions>0)
            $scoreOutOfTen = round(($totalScore/$totalQuestions)*10,1);
        return $scoreOutOfTen;
}

function getTimeSpentOnTest($userID, $daPaperCode)
{
        $sqTimeTaken = "SELECT spendTime, lastmodified FROM da_questionTestStatus WHERE userID=$userID and paperCode = '$daPaperCode' ";
	$rsTimeTaken = mysql_query($sqTimeTaken);
	$rwTimeTaken = mysql_fetch_array($rsTimeTaken);
	$timeSpent = intval((1800 - $rwTimeTaken["spendTime"])/60)." minute(s)";    
        return $timeSpent;
}
?>