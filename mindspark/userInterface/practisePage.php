<?php
error_reporting ( 0 );

@include ("check1.php");
include_once ("constants.php");
include ("functions/functions.php");
include ("classes/clsUser.php");
include_once ("classes/clsTopicProgress.php");
if (! isset ( $_SESSION ['userID'] )) {
	header ( "Location: error.php" );
}
$userID = $_SESSION ['userID'];
$Name = explode ( " ", $_SESSION ['childName'] );
$Name = $Name [0];

$objUser = new User ( $userID );
$schoolCode = $objUser->schoolCode;
$childClass = $objUser->childClass;
$childSection = $objUser->childSection;
$category = $objUser->category;
$subcategory = $objUser->subcategory;

if (strcasecmp ( $category, "STUDENT" ) == 0 && (strcasecmp ( $subcategory, "School" ) == 0 || strcasecmp ( $subcategory, "Home Center" ) == 0)) {
	$isAllowedDeactivatedTopicsForHomeUse = isAllowedDeactivatedTopicsForHome ( $schoolCode, $childClass, $childSection );
	if ($isAllowedDeactivatedTopicsForHomeUse)
		$isHomeUsage = isHomeUsage ( $schoolCode, $childClass );
}

if (($_POST["userType"]=="msAsStudent" || $_POST["userType"]=="teacherAsStudent") && isset($_POST["clusterCode"]) ){
	$_SESSION['windowName']="";$_SESSION['msAsStudent']=1;
	$_SESSION["userType"]="msAsStudent";
	if($_POST["userType"] == "teacherAsStudent")
	{ 
		$_SESSION["userTypeS"] = "teacherAsStudent";
		$_SESSION['childClass'] = $_POST['childClass'];
		$_SESSION["theme"] = 2;

	}
	$practiseModuleId=$_POST["clusterCode"];
	$testId=0;$status='';
	$query=" SELECT  w.practiseModuleId, r.id, r.status FROM practiseModuleDetails w, practiseModulesTestStatus r WHERE w.practiseModuleId='$practiseModuleId' AND r.practiseModuleId = w.practiseModuleId AND r.userID = $userID ORDER BY attemptNo DESC";
	$result = mysql_query ( $query ) or die ( mysql_error ( $query ) . $query );
	if (mysql_num_rows ( $result ) > 0) {
		$row = mysql_fetch_array ( $result);
		$updateSQL = "UPDATE practiseModulesTestStatus SET status='completed', remainingTime=300,lastAttemptQue=0,score=0 WHERE userID=$userID AND practiseModuleId='$row[0]' AND id=$row[1]";
		mysql_query($updateSQL) or die(mysql_error().$updateSQL);
		$testId=$row[1];$status='completed';
	}
}
if ($_POST["userType"]=="msAsStudent" && isset($_POST["paperCode"])){
	$_SESSION['windowName']="";$_SESSION['msAsStudent']=1;
	$_SESSION["userType"]="msAsStudent";
	$daPaperCode=$_POST["paperCode"];
}
if (isset($_POST['mode']) && $_POST['mode']=='choiceScreen' && isset($_POST['practiseModuleId']) && $_POST['practiseModuleId']!=''){
	$practiseModuleId=$_POST['practiseModuleId'];
	$testId=0;$status="";
	$query=mysql_query("SELECT  r.id, r.status FROM practiseModulesTestStatus r WHERE r.practiseModuleId = '$practiseModuleId' AND r.userID = $userID AND r.status='in-progress' ORDER BY attemptNo DESC");
	if (mysql_num_rows($query)>0){
		$res=mysql_fetch_assoc($query);
		$testId=$res['id'];$status=$res['status'];
	}
}
include ("header.php");

?>
<title>Practise Page</title>
<link rel="stylesheet" href="css/dashboard/midClass.css" />
<link rel="stylesheet" href="css/commonMidClass.css" />
<link rel="stylesheet" href="css/commonMidClass.css" />
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script src="libs/closeDetection.js"></script>
<script type="text/javascript"
	src="/mindspark/userInterface/libs/combined.js"></script>

<script>

function startPractise(practiseModuleId,status,testId){
	var datatoPass ="mode=createDdContent&practiseModuleId="+practiseModuleId+"&status="+status+"&testId="+testId;
	$.ajax({
		  url: "commonAjax.php",
		  data : datatoPass ,
		  cache: false,
		  type: "POST",
		  success: function(html){
				obj = JSON.parse(html);//console.log(html);
			    document.getElementById("practiseModuleTestStatusId").value=obj.practiseModuleTestStatusId;
			    document.getElementById("practiseModuleId").value=obj.practiseModuleId;
				document.getElementById("timeTakenForDd").value=obj.remainingTime;
				document.getElementById("scoreForDd").value=obj.currentScore;
				document.getElementById("attemptNo").value=obj.attemptNo;
				setTryingToUnload();
				document.getElementById('frmDailyDrill').submit();				
		  }
		});
	
}
</script>

<script>
var langType = '<?=$language;?>';
function load() {
	init();
	<?php
		if ((($_POST["userType"]=="msAsStudent" || $_POST["userType"]=="teacherAsStudent") && isset($_POST["clusterCode"])) || (isset($_POST['mode']) && $_POST['mode']=='choiceScreen' && isset($_POST['practiseModuleId']) && $_POST['practiseModuleId']!='')) {
			echo "startPractise('$practiseModuleId','$status',$testId);";
		}
		else if ($_POST["userType"]=="msAsStudent" && isset($_POST["paperCode"])){ 
			echo 'setTryingToUnload();document.getElementById("frmDaTest").submit();';
		}
	?>
	var a= window.innerHeight - (80 + 19 + 140 + 55);
	$('#topicInfoContainer').css("height",a+"px");
	$(".forLowerOnly").remove();

}
function init()
{
	/*if(parseInt(document.getElementById("pnlTeacherTopicSelection").offsetHeight)<300)
		document.getElementById("pnlTeacherTopicSelection").style.height = "300px";*/
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins
	if($("#viewAllTopics").css("display")=="none"){
		$("#sessionWiseReport").css("margin-top","-50px");
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
function hideBar(){
	if (infoClick==0){
		$("#hideShowBar").text("+");
		$('#viewAllTopics,#homeIcon').fadeOut(300);
		$('#sessionWiseReport').animate({'margin-right':'340px','margin-top':'-104px'},600);
		$('#info_bar').animate({'height':'60px'},600);
		var a= window.innerHeight -130 -27 -57;
		if(androidVersionCheck==1){
		$('#topicInfoContainer').css("height","auto");
		}else{
			$('#topicInfoContainer').animate({'height':a},600);
		}
		infoClick=1;
	}
	else if(infoClick==1){
		$("#hideShowBar").text("-");
        $('#viewAllTopics,#homeIcon').fadeIn(300);
		<?php if($theme==2) { ?>
			$(".forHighestOnly").css("display","none");
		<?php } ?>
		$('#sessionWiseReport').animate({'margin-right':'30px','margin-top':'-69px'},600);
		$('#info_bar').animate({'height':'140px'},600);
		var a= window.innerHeight - (80 + 20 + 140 + 55);
		if(androidVersionCheck==1){
		$('#topicInfoContainer').css("height","auto");
		}else{
			$('#topicInfoContainer').animate({'height':a},600);
		}
		infoClick=0;
	}
}

</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
	<?php if (!((($_POST["userType"]=="msAsStudent" || $_POST["userType"]=="teacherAsStudent") && isset($_POST["clusterCode"])) || (isset($_POST['mode']) && $_POST['mode']=='choiceScreen' && isset($_POST['practiseModuleId']) && $_POST['practiseModuleId']!=''))) { ?>
		<form name="frmTeacherTopicSelection" id="frmTeacherTopicSelection"
			method="POST">
			<div id="top_bar">
				<div class="logo"></div>

				<div id="studentInfoLowerClass" class="forLowerOnly hidden">
					<div id="nameIcon"></div>
					<div id="infoBarLeft">
						<div id="nameDiv">
							<div id='cssmenu'>
								<ul>
									<li class='has-sub '><a href='javascript:void(0)'><span	id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
										<ul>
											<li><a href='myDetailsPage.php'
												onClick="javascript:setTryingToUnload();"><span
													data-i18n="homePage.myDetails"></span></a></li>
											<li><a href='changePassword.php'
												onClick="javascript:setTryingToUnload();"><span
													data-i18n="homePage.changePassword"></span></a></li>
										</ul>
									</li>
								</ul>
							</div>
						</div>
						<div id="classDiv">
							<span id="classText" data-i18n="common.class"></span> <span
								id="userClass"><?=$childClass.$childSection?></span>
						</div>
					</div>
				</div>
				<div id="studentInfoLowerClass" class="forHighestOnly">
					<div id="nameIcon"></div>
					<div id="infoBarLeft">
						<div id="nameDiv">
							<div id='cssmenu'>
								<ul>
									<li class='has-sub '><a href='javascript:void(0)'><span
											id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
										<ul>
											<li><a href='myDetailsPage.php'
												onClick="javascript:setTryingToUnload();"><span
													data-i18n="homePage.myDetails"></span></a></li>
											<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
											<li><a href='changePassword.php'
												onClick="javascript:setTryingToUnload();"><span
													data-i18n="homePage.changePassword"></span></a></li>
											<li><a href='whatsNew.php'
												onClick="javascript:setTryingToUnload();"><span
													data-i18n="common.whatsNew"></span></a></li>
											<li><a href='javascript:void(0)' onClick="openHelp()"><span
													data-i18n="common.help"></span></a></li>
											<li><a href='logout.php'
												onClick="javascript:setTryingToUnload();"><span
													data-i18n="common.logout"></span></a></li>
										</ul>
									</li>
								</ul>
							</div>
						</div>
						<div id="classDiv">
							<span id="classText" data-i18n="common.class"></span> <span
								id="userClass"><?=$childClass.$childSection?></span>
						</div>
					</div>
				</div>
				<div id="help" style="visibility: hidden">
					<div class="help"></div>
					<div class="helpText" data-i18n="common.help"></div>
				</div>
				<div id="logout" onClick="logoff();" class="hidden">
					<div class="logout"></div>
					<div class="logoutText" data-i18n="common.logout"></div>
				</div>
				<div id="whatsNew" style="visibility: hidden">
					<div class="whatsNew"></div>
					<div class="whatsNewText" data-i18n="common.whatsNew"></div>
				</div>
			</div>

			<div id="container">
				<div id="info_bar" class="forLowerOnly hidden">
					<div id="blankWhiteSpace"></div>
					<div id="home">
						<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
						<div id="dashboardHeading" class="forLowerOnly">
							&ndash;&nbsp;<span class="textUppercase">Practise Page</span>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<div id="info_bar" class="forHigherOnly">
					<div id="topic">
						<div id="home">
							<div id="homeIcon" onClick="getHome()"></div>
							<div id="homeText" class="hidden">
								<span onClick="getHome()" class="textUppercase"
									data-i18n="dashboardPage.home"></span> > <font color="#606062">
									<span class="textUppercase">Practise Page</span>
								</font>
							</div>
						</div>

						<div id="dashboard" class="forHighestOnly">
							<div id="dashboardIcon"></div>
							<div id="dashboardText">
								<span class="textUppercase" data-i18n="dashboardPage.dashboard"></span>
							</div>
						</div>
						<div class="arrow-right"></div>

						<div id="activatedAtHome" class="forLowerOnly hidden">
							<div id="activatedAtHomeIcon"></div>
							<div id="activatedAtHomeText"
								data-i18n="dashboardPage.msgActiveHome"></div>
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
				</div>


				<div id="main_bar" class="forHighestOnly">
					<div id="drawer1">
						<a href="activity.php" onclick="javascript:setTryingToUnload();"
							style="text-decoration: none; color: inherit">
							<div id="drawer1Icon"></div> ACTIVITIES 
					
					</div>
					</a> <a href="examCorner.php"
						onclick="javascript:setTryingToUnload();"
						style="text-decoration: none; color: inherit"><div id="drawer2">
							<div id="drawer2Icon"></div>
							EXAM CORNER
						</div></a> <a href="home.php"
						onClick="javascript:setTryingToUnload();"><div id="drawer3">
							<div id="drawer3Icon"></div>
							HOME
						</div></a> <a href="explore.php"
						onClick="javascript:setTryingToUnload();"><div id="drawer4">
							<div id="drawer4Icon"></div>
							EXPLORE ZONE
						</div></a>
					<div id="plus" onClick="openMainBar();">
						<div id="vertical"></div>
						<div id="horizontal"></div>
					</div>
					<a href="src/rewards/rewardsDashboard.php"
						onClick="javascript:setTryingToUnload();"
						style="text-decoration: none;"><div id="drawer5">
							<div id="drawer5Icon"
								<?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?>
								class="<?=$sparkieImage?>">
								<div class="redCircle"><?=$sparkieWon?></div>
							</div>
							REWARDS CENTRAL
						</div></a>
					<!--<a href="viewComments.php?from=links&mode=1" onClick="javascript:setTryingToUnload();"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
				</div></a>-->
				</div>

				<div id="tableContainerMain">
					<div class="Heading">Practise Module</div>
	        			<?php
					
									$ttcode = $_REQUEST ['ttCode'];
									$practiseSQL = "
										SELECT a.cluster,a.clusterCode as clusterToUse,e.id,e.status, e.score, e.currentLevel,e.attemptNo,d.practiseModuleId FROM adepts_clusterMaster a INNER JOIN adepts_teacherTopicClusterMaster b ON a.clusterCode = b.clusterCode INNER JOIN adepts_teacherTopicClusterStatus c ON b.clusterCode = c.clusterCode  INNER JOIN practiseModules  d ON c.clusterCode=d.linkedToCluster  LEFT JOIN practiseModulesTestStatus e ON d.practiseModuleId=e.practiseModuleId AND e.userID = $userID AND e.attemptNo=(SELECT MAX(f.attemptNo) FROM practiseModulesTestStatus f where f.practiseModuleId = e.practiseModuleId AND f.userID = $userID )  WHERE b.teacherTopicCode = '$ttcode' AND  c.userID = $userID AND c.result='SUCCESS' AND c.lastModified > '2015-03-25 00:00:00'
										 UNION
										SELECT a.cluster, a.clusterCode as clusterToUse,e.id,e.status, e.score,e.currentLevel,e.attemptNo,d.practiseModuleId 
										FROM adepts_teacherTopicMaster b, adepts_customizedTopicDetails c,adepts_clusterMaster a
											INNER JOIN adepts_teacherTopicClusterStatus q ON a.clusterCode = q.clusterCode  
											INNER JOIN practiseModuleDetails  d ON a.clusterCode=d.linkedToCluster  
											LEFT JOIN practiseModulesTestStatus e ON d.practiseModuleId=e.practiseModuleId AND e.userID = $userID 
											AND e.attemptNo=(SELECT MAX(f.attemptNo) FROM practiseModulesTestStatus f where f.practiseModuleId = e.practiseModuleId AND f.userID = $userID )  
										WHERE b.teacherTopicCode = '$ttcode' AND b.customCode=c.code AND b.customTopic=1 AND FIND_IN_SET(a.clusterCode ,c.clusterCodes)
										 AND  q.userID = $userID AND q.result='SUCCESS'
										 GROUP BY id";
									
									$practiseQuery = mysql_query ( $practiseSQL ) or die ( mysql_error ( $practiseSQL ) . $practiseSQL );
									if (mysql_num_rows ( $practiseQuery ) > 0) {
										$i = 1;
										while ( $row = mysql_fetch_array ( $practiseQuery, MYSQL_ASSOC ) ) {
											$clusterCode = $row ['clusterToUse'];
											$practiseModuleId = $row['practiseModuleId'];
											$score = $row ['score'] != "" ? $row ['score'] : 0;
											$status = $row ['status'] ;
											$testId = $row['id'];
											$showReplay = "";
											$showStatusImage = "...";
											if($row ['status'] != ''  && $row ['status'] =="completed"){
												$attemptId = $row ['attemptId'] + 1;
												$showReplay = '<span class="replay-span"><img src="assets/reply.png" /></span>';
												$showStatusImage = '<img src="assets/correct_answer.png" />';
											}
											else{
												$attemptId = $row['attemptId'] == ""? 1 : $row['attemptId'];
											}
											$currentLevel=$row['currentLevel'];
										?>
												<a href="javascript:void(0);" onclick="startPractise('<?=$practiseModuleId?>','<?=$status?>','<?= $testId?>')">
													<div class="practise-elements">
														<div class="elements">
															<div class="element-bunddle">
																<span class="index-count"><?=$i?>.</span> 
																<span class="topic-name"><?=$row['cluster']?></span> 
																<span class="dd-completion-status">Score: <?= $score;?><br />Level : <?= $currentLevel;?></span>
																<?=$showReplay;?>
																<span class="status-icon"><?= $showStatusImage ?></span>
															</div>
															
														</div>
													</div>
												</a>
										<?php
											$i ++;
										}
									}
						?>
				</div>
			</div>

			<input type="hidden" name='mode' id="mode"> <input type="hidden"
				name='ttCode' id="ttCode"> <input type="hidden" name='topicDesc'
				id="topicDesc"> <input type="hidden" name="userID" id="userID"
				value="<?=$userID?>"> <input type="hidden" name="timedTestCode"
				id="timedTestCode"> <input type="hidden" name="pendingTopicTimedTest"
				id="pendingTopicTimedTest"> <input type="hidden" name="cls" id="cls"
				value="<?=$user->childClass?>">
		</form>
	<?php } ?>
	<div style="display: none">
		<?php 
		if ($_POST["userType"]=="msAsStudent" && isset($_POST["paperCode"])){ ?>
		<form name="frmDaTest" action="question.php" id="frmDaTest" method="POST">
			<input type="hidden" name="mode" value="firstQuestion">
			<input type="hidden" name="daTestCode" value="<?=$daPaperCode;?>">
			<input type="hidden" name="quesCategory" value="daTest">
		</form>
		<?php } ?>
		<form method="POST" id="frmDailyDrill" action="question.php"
			name="frmDailyDrill">
			<input type="hidden" value="firstQuestion" name="mode"> 
			<input type="hidden" value="" name="practiseModuleId" id="practiseModuleId"> 
			<input type="hidden" value="" name="practiseModuleTestStatusId" id="practiseModuleTestStatusId"> 
			<input type="hidden" value="practiseModule" name="quesCategory"> 
			<input type="hidden" value="" name="timeTakenForDd" id="timeTakenForDd"> 
			<input type="hidden" value="" name="scoreForDd" id="scoreForDd"> 
			<input type="hidden" value="1" name="fromPractisePage" />
			<?php
			if (isset($_POST['mode']) && $_POST['mode']=='choiceScreen' && isset($_POST['practiseModuleId']) && $_POST['practiseModuleId']!=''){
				if (isset($_POST['returnTo'])) echo '<input type="hidden" value="'.$_POST['returnTo'].'" name="returnTo" />';
				echo '<input type="hidden" value="1" name="fromChoiceScreen" />';
			}
			?>
			<input type="hidden" value="" name="attemptNo" id="attemptNo" />
			
 		</form>
	</div>

	
<?php include("footer.php"); mysql_close(); ?>
