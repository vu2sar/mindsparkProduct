<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include_once("../../check1.php");
	include("../../classes/clsUser.php");
	include("../../constants.php");
	include("../../classes/clsRewardSystem.php");
	if(!isset($_SESSION['userID']))
	{
		header("Location:../../logout.php");
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
	$schoolCode    = $objUser->schoolCode;
	$sparkieInformation = new Sparkies($userID);
	if(isset($_POST['themeChanged'])){
		$themeChanged = $_POST['themeChanged'];
		if($_POST['tradeActivate']==0){
			$changeTheme=$sparkieInformation->tradeTheme($themeChanged);
		}else{
			$changeTheme=$sparkieInformation->updateTheme($themeChanged);
		}
	}
	$sparkieInformation = new Sparkies($userID);
	$rewardTheme = $sparkieInformation->rewardTheme;
	$badgesArray = $sparkieInformation->checkForBadgesEarned();
	for($i=0;$i<sizeOf($badgesArray);$i++){
		if(isset($badgesArray[$i]['light'])){
			$light = 1;
			if($badgesArray[$i]['light']['sparkieConsumed']>0){
				$lightTraded=1;
			}else{
				$lightTraded=0;
			}
		}else if(isset($badgesArray[$i]['dark'])){
			$dark = 1;
			if($badgesArray[$i]['dark']['sparkieConsumed']>0){
				$darkTraded=1;
			}else{
				$darkTraded=0;
			}
		}else if(isset($badgesArray[$i]['girl'])){
			$girl = 1;
			if($badgesArray[$i]['girl']['sparkieConsumed']>0){
				$girlTraded=1;
			}else{
				$girlTraded=0;
			}
		}else if(isset($badgesArray[$i]['boy'])){
			$boy = 1;
			if($badgesArray[$i]['boy']['sparkieConsumed']>0){
				$boyTraded=1;
			}else{
				$boyTraded=0;
			}
		}
	}
	$sparkieImage = $_SESSION['sparkieImage'];
	$sparkiesEarned = $sparkieInformation->getTotalSparkies();
	$sparkieLogic = $sparkieInformation->getSparkieLogic();
	if($sparkiesEarned > $sparkieLogic['mileStone1']['sparkieNeeded']){
		$sparkies = $sparkieLogic['mileStone1']['sparkieNeeded'];
		$timelineSparkie1 = "milestone1";
		$sparkiesExplanation = "Great going! You made it to next level!";
		if($sparkiesEarned > $sparkieLogic['mileStone2']['sparkieNeeded']){
			$superSparkies = $sparkieLogic['mileStone2']['sparkieNeeded'] - $sparkieLogic['mileStone1']['sparkieNeeded'];
			$timelineSparkie1 = "milestone2";
			if($sparkiesEarned > $sparkieLogic['mileStone3']['sparkieNeeded']){
				$timelineSparkie1 = "milestone3";
				$megaSparkies = $sparkieLogic['mileStone3']['sparkieNeeded'] - $sparkieLogic['mileStone2']['sparkieNeeded'] - $sparkieLogic['mileStone1']['sparkieNeeded'];
			}else{
				$megaSparkies = $sparkiesEarned - $sparkieLogic['mileStone2']['sparkieNeeded'];
				$megaSparkiesExplanation = "Goodluck! This will be a challenging level.";
			}
		}else{
			$superSparkies = $sparkiesEarned - $sparkieLogic['mileStone1']['sparkieNeeded'];
			$superSparkiesExplanation = "Goodluck! This will be a challenging level.";
		}
	}else{
		$sparkies = $sparkiesEarned;
		$sparkiesExplanation = "Goodluck! This will be a challenging level.";
	}
?>

<?php include("../../header.php");?>

<title>Rewards Central</title>
	<script src="../../libs/jquery.js"></script>
	<?php
	if($theme==2) { ?>
	<link href="../../css/themesDashboard/midClass.css?ver=1" rel="stylesheet" type="text/css">
    <link href="../../css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		var infoClick=0;
		var a=0;
		var b=0;
		var e=0;
		function load(){
			$(".notAttempted").show();
			$("#largeContainer").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer1").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			$("#largeContainer2").attr("style","width:<?=(count($arrActivities)+3)*220?>px");
			var a= window.innerHeight - (100+ 140 );
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
		}
		function showHideBar(){
			if (infoClick==0){
				$("#hideShowBar").text("+");
				$('#info_bar').animate({'height':'75px'},600);
				$('#topic').animate({'height':'55px'},600);
				$('#clickText').animate({'margin-top':'1px'},600);
				$('#sparkieBarMid').hide();
				$('.Name').hide();
				$('.class').hide();
				var a= window.innerHeight -150 -45;
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=1;
			}
			else if(infoClick==1){
				$("#hideShowBar").text("-");
				$('#info_bar').animate({'height':'140px'},600);
				$('#topic').animate({'height':'115px'},600);
				$('#clickText').animate({'margin-top':'10px'},600);
				$('.Name').show();
				$('#sparkieBarMid').show(500);
				$('.class').show();
				var a= window.innerHeight - (100+ 140 );
				if(androidVersionCheck==1){
				$('#activitiesContainer').animate({'height':'auto'},600);
				}
				else{
					$('#activitiesContainer').animate({'height':a},600);
				}
				infoClick=0;
			}
		}
	</script>
	<?php } else if($theme==3) { ?>
	    <link href="../../css/commonHigherClass.css" rel="stylesheet" type="text/css">
		<link href="../../css/themesDashboard/higherClass.css" rel="stylesheet" type="text/css">
		<script>
		function load(){
			var a= window.innerHeight - (170);
			$('#activitiesContainer').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			var ua1 = navigator.userAgent;
			if( ua1.indexOf("Android") >= 0 )
			{
				$("#activitiesContainer").css("height","auto");
				$('#menu_bar').css("height",$('#activitiesContainer').css("height"));
				$("#sideBar").css("height",$("#activitiesContainer").css("height"));
				$("#main_bar").css("height",$("#activitiesContainer").css("height"));
			}
		}
	</script>
	<?php } ?>
	<script type="text/javascript" src="../../libs/i18next.js"></script>
	<script type="text/javascript" src="../../libs/translation.js"></script>
	<script type="text/javascript" src="../../libs/closeDetection.js"></script>
    <script>
	var langType	=	'<?=$language?>';
	var click=0;
	function getHome()
	{
		setTryingToUnload(); 
		window.location.href	=	"../../home.php";
	}
	function logoff()
	{
		setTryingToUnload();
		window.location="../../logout.php";
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
	function changeTheme(id,value){
		var id=id;
		var value=value;
		$("#themeChanged").attr("value",id);
		$("#tradeActivate").attr("value",value);
		setTryingToUnload();
		if(id=="light" || id=="dark"){
			var noOfSparkie=30;
		}else{
			var noOfSparkie=50;
		}
		if(value==0){
			var r = confirm("Are you sure you want to buy it for "+noOfSparkie+" sparkies? Your total sparkie count will reduce by "+noOfSparkie+" then.");
			if (r == true) {
			    $("#rewardsForm").submit();
			}
		}else{
			var r = confirm("Are you sure you want to activate this theme?");
			if (r == true) {
			    $("#rewardsForm").submit();
			}
		}
	}
    </script>
</head>
<body onLoad="load();" onResize="load();" class="translation">
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='../../myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='../../changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='../../whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='../../logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff();" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<div id="home" class="linkPointer" onClick="getHome()"></div>
				<div class="icon_text2"> - <span class="textUppercase">Rewards Central</span></font></div>
			</div>
			<div id="topic">
					<div class="icon_text1"><span onClick="getHome()" class="textUppercase linkPointer" data-i18n="dashboardPage.home"></span> > <font color="#606062"> <span class="textUppercase">Rewards Central</span></font></div>
			</div>
			<div class="class">
				<strong><span id="classText" data-i18n="common.class"></span> </strong> <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<div id="locked" onClick="attempt(id)" class="forLowerOnly">
				<div class="icon_text" id="lck" data-i18n="activityPage.locked"></div>
				<div id="pointed" class="lckPoited">
				</div>
			</div>
			<div id="new">
				<div class="icon_text" data-i18n="activityPage.new"></div>
				<div id="pointed">
				</div>
			</div>
		</div>
		<div id="hideShowBar" class="forHigherOnly hidden" onClick="showHideBar()">-</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard">
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Rewards Central</span></div>
                </div>
				<div class="arrow-right"></div>
				<div id="sparkieBar" class="forHighestOnly">
					<div id="leftSparkieBar">
						<div id="textMain">YOUR THEMES</div>
					</div>
					<div id="rightSparkieBar">
			<div id="textMain1">&nbsp;</div>
			<?php if($timelineSparkie1==""){ ?>
				<div id="sparkieCounter" style="float:left">0</div>
				<div id="spakieImage" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone1"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==400){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div class="sparkieImage1" style="margin-left:<?=(400-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone2"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==1000){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div class="sparkieImage1" style="margin-left:<?=(1000-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone3']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone3"){?>
				<div id="sparkieCounter" style="float:left"><?=1500*(intval($sparkiesEarned/1500))?></div>
				<div id="spakieImage" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=1500*(intval($sparkiesEarned/1500) + 1)?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } ?>	
			</div>
				</div>
				<div class="clear"></div>
				
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="../../dashboard.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			DASHBOARD
			</div></a>
			<a href="../../examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="../../home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="../../explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			
			<a href="../../activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
			<div id="drawer5" style="font-size:1.4em;">
			<div id="drawer5Icon"></div>
			ACTIVITIES
			</div></a>
			<!--<a href="../../viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<a href="rewardsDashboard.php">
		<div id="rewards" class="hidden forHigherOnly">
            <span id="classText">Rewards</span>
            <div id="rM" class="pointed1">
            </div>
        </div></a>
		<a href="historyDashboard.php">
        <div id="history" class="hidden forHigherOnly">
            <span id="classText">Sparkie Bank</span>
            <div id="hM" class="pointed2">
            </div>
        </div></a>
		<div id="themes" class="hidden forHigherOnly">
            <span id="classText">Themes</span>
            <div id="tM" class="pointed3">
            </div>
        </div>
		<a href="classLeaderBoard.php">
		<div id="leaderBoard" class="hidden forHigherOnly">
			<div id="leaderBoardImage"></div>
            <span id="classText">Class LeaderBoard</span>
            <div id="lM" class="pointed4">
            </div>
        </div></a>
		<div id="sparkieBarMid" class="forHigherOnly hidden">
			<div id="leftSparkieBar">
				<div id="textMain">YOUR THEMES</div>
			</div>
			<div id="rightSparkieBar">
			<div id="textMain1">&nbsp;</div>
			<?php if($timelineSparkie1==""){ ?>
				<div id="sparkieCounter" style="float:left">0</div>
				<div id="spakieImage" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=$sparkiesEarned*340/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone1"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone1']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==400){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div class="sparkieImage1" style="margin-left:<?=(400-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded'] ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone1']['sparkieNeeded'])*170/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone2"){?>	
				<div id="sparkieCounter" style="float:left"><?=$sparkieLogic['mileStone2']['sparkieNeeded']?></div>
				<div id="spakieImage" <?php if($sparkiesEarned==1000){echo 'class="sparkieImage1"';}?> style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div class="sparkieImage1" style="margin-left:<?=(1000-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=$sparkieLogic['mileStone3']['sparkieNeeded']?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-$sparkieLogic['mileStone2']['sparkieNeeded'])*115/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } else if($timelineSparkie1=="milestone3"){?>
				<div id="sparkieCounter" style="float:left"><?=1500*(intval($sparkiesEarned/1500))?></div>
				<div id="spakieImage" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded'] +13 ?>px;"></div>
				<div id="sparkieCircle" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"><?=$sparkiesEarned?></div>
				<div id="sparkieCounter" style="position: relative;float:right"><?=1500*(intval($sparkiesEarned/1500) + 1)?></div>
				<div id="gradientSparkie">
					<div class="afterSparkie" style="margin-left:<?=($sparkiesEarned-(1500*(intval($sparkiesEarned/1500))))*56/$sparkieLogic['mileStone1']['sparkieNeeded']?>px;"></div>
				</div>
			<?php } ?>	
			</div>
		</div>
        <div id="lock" onClick="attempt(id);">
            <span id="classText" data-i18n="activityPage.locked"></span>
        </div>
<form name="rewardsForm" id="rewardsForm" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<a href="classLeaderBoard.php">
				<div id="report">
					<span id="reportText">Your Class LeaderBoard</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
				<a href="rewardsDashboard.php">
				<div id="rewards" class="forHighestOnly">
					<div id="naM" class="pointed1">
					</div></br>
					Rewards
				</div></a>
				<a href="historyDashboard.php">
				<div id="history" onClick="redirect()" class="forHighestOnly">
					<div id="aM" class="pointed2">
					</div></br>
					Sparkie Bank
				</div></a>
				<div id="themes" onClick="redirect()" class="forHighestOnly">
					<div id="aM" class="pointed3">
					</div></br>
					Themes
				</div>
			</div>
			</div>
	<div id="activitiesContainer">
		<div id="rewardsContainer">
			<div class="rewardsContainer<?php if($light!=1) echo " locked"; ?>">
				<div id="lightTheme<?php if($light!=1) echo "Locked"; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><?php if($light==1 && $lightTraded==1) echo "<div class='activated'></div>"; else if($light==1 && $lightTraded!=1) echo "<div class='trade'></div>"; else if($light!=1) echo "<div class='notActivated'></div>"; ?></div>
					<div class="sparkieText">Light</div>
					<div class="explanationText"><?php if($lightTraded==1 && $light==1) echo "Congratulations! Enjoy your special theme."; else if($light==1 && $lightTraded!=1) echo "Trade 30 Sparkies to activate this theme"; else if($light!=1) echo "Earn 400 sparkies to unlock this theme."; ?></div>
				</div>
				<div id='light' class='activateButton<?php if($rewardTheme=="light" && $light==1) echo " inactiveButton';'"; else if($light!=1) echo " lockedButton'";else echo "' onclick='changeTheme(id,$lightTraded);'" ?>>
				<?php if($rewardTheme=="light" && $light==1) echo "This theme is ACTIVATED<div class='pointedRed'></div>"; else if($light==1 && $lightTraded!=1) echo "TRADE<div class='pointedWhite'></div>";else if($lightTraded==1 && $rewardTheme!="light") echo "ACTIVATE<div class='pointedWhite'></div>"; else if($light!=1) echo "LOCKED<div class='pointedWhite'></div>"; ?>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer<?php if($dark!=1) echo " locked"; ?>">
				<div id="darkTheme<?php if($dark!=1) echo "Locked"; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><?php if($darkTraded==1 && $dark==1) echo "<div class='activated'></div>"; else if($dark==1 && $darkTraded!=1) echo "<div class='trade'></div>"; else if($dark!=1) echo "<div class='notActivated'></div>"; ?></div>
					<div class="sparkieText">Dark</div>
					<div class="explanationText"><?php if($darkTraded==1 && $dark==1) echo "Congratulations! Enjoy your special theme."; else if($dark==1 && $darkTraded!=1) echo "Trade 30 Sparkies to activate this theme"; else if($dark!=1) echo "Earn 400 sparkies to unlock this theme."; ?></div>
				</div>
				<div id='dark' class='activateButton<?php if($rewardTheme=="dark" && $dark==1) echo " inactiveButton';'"; else if($dark!=1) echo " lockedButton'";else echo "' onclick='changeTheme(id,$darkTraded);'" ?>>
				<?php if($rewardTheme=="dark" && $dark==1) echo "This theme is ACTIVATED<div class='pointedRed'></div>"; else if($dark==1 && $darkTraded!=1) echo "TRADE<div class='pointedWhite'></div>";else if($darkTraded==1 && $rewardTheme!="dark") echo "ACTIVATE<div class='pointedWhite'></div>";else if($dark!=1) echo "LOCKED<div class='pointedWhite'></div>"; ?>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer<?php if($girl!=1) echo " locked"; ?>">
				<div id="girlTheme<?php if($girl!=1) echo "Locked"; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><?php if($girlTraded==1 && $girl==1) echo "<div class='activated'></div>"; else if($girl==1 && $girlTraded!=1) echo "<div class='trade'></div>"; else if($girl!=1) echo "<div class='notActivated'></div>"; ?></div>
					<div class="sparkieText">Princess</div>
					<div class="explanationText"><?php if($girlTraded==1 && $girl==1) echo "Congratulations! Enjoy your special theme."; else if($girl==1 && $girlTraded!=1) echo "Trade 50 SuperSparkies to activate this theme"; else if($girl!=1) echo "Earn 1000 sparkies to unlock this theme."; ?></div>
				</div>
				<div id='girl' class='activateButton<?php if($rewardTheme=="girl" && $girl==1) echo " inactiveButton';'"; else if($girl!=1) echo " lockedButton'";else echo "' onclick='changeTheme(id,$girlTraded);'" ?>>
				<?php if($rewardTheme=="girl" && $girl==1) echo "This theme is ACTIVATED<div class='pointedRed'></div>"; else if($girl==1 && $girlTraded!=1) echo "TRADE<div class='pointedWhite'></div>";else if($girlTraded==1 && $rewardTheme!="girl") echo "ACTIVATE<div class='pointedWhite'></div>"; else if($girl!=1) echo "LOCKED<div class='pointedWhite'></div>"; ?>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer<?php if($boy!=1) echo " locked"; ?>">
				<div id="boyTheme<?php if($boy!=1) echo "Locked"; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><?php if($boyTraded==1 && $boy==1) echo "<div class='activated'></div>"; else if($boy==1 && $boyTraded!=1) echo "<div class='trade'></div>"; else if($boy!=1) echo "<div class='notActivated'></div>"; ?></div>
					<div class="sparkieText"><?php if($theme==2) echo "Sporty";else echo "Super Hero"; ?></div>
					<div class="explanationText"><?php if($boyTraded==1 && $boy==1) echo "Congratulations! Enjoy your special theme."; else if($boy==1 && $boyTraded!=1) echo "Trade 50 SuperSparkies to activate this theme"; else if($boy!=1) echo "Earn 1000 sparkies to unlock this theme."; ?></div>
				</div>
				<div id='boy' class='activateButton<?php if($rewardTheme=="boy" && $boy==1) echo " inactiveButton';'"; else if($boy!=1) echo " lockedButton'";else echo "' onclick='changeTheme(id,$boyTraded);'" ?>>
				<?php if($rewardTheme=="boy" && $boy==1) echo "This theme is ACTIVATED<div class='pointedRed'></div>"; else if($boy==1 && $boyTraded!=1) echo "TRADE<div class='pointedWhite'></div>";else if($boyTraded==1 && $rewardTheme!="boy") echo "ACTIVATE<div class='pointedWhite'></div>"; else if($boy!=1) echo "LOCKED<div class='pointedWhite'></div>"; ?>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div id="lightTheme"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class='activated'></div></div>
					<div class="sparkieText">Default</div>
					<div class="explanationText">Restore to default theme</div>
				</div>
				<div id='default' class='activateButton<?php if($rewardTheme=="default" || $rewardTheme=="") echo " inactiveButton';'";else echo "' onclick='changeTheme(id,1);'" ?>>
				<?php if($rewardTheme=="default" || $rewardTheme=="") echo "Default theme is ACTIVATED<div class='pointedRed'></div>";else echo "ACTIVATE<div class='pointedWhite'></div>"; ?>
				</div>
			</div>
			<input type="hidden" name="themeChanged" id="themeChanged" value=""/>
			<input type="hidden" name="tradeActivate" id="tradeActivate" value=""/>
			<!--
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div id="anniversaryTheme"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="activated"></div></div>
					<div class="sparkieText">Anniversary</div>
					<div class="explanationText">Congratulations! Enjoy your special theme</div>
				</div>
				<div class="activateButton">ACTIVATE<div class="pointedWhite"></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div id="sparkieTheme"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="activated"></div></div>
					<div class="sparkieText">SparkieSpecial</div>
					<div class="explanationText">Congratulations! Enjoy your special theme</div>
				</div>
				<div class="activateButton">ACTIVATE<div class="pointedWhite"></div>
				</div>
			</div>-->
		</div>
		<!--<div class="redSeperatorVertical"></div>
		<div id="enrichmentContainer">
			<div id="leftSparkieBar1">
				<div id="textMain">These are your Enrichments!</div>
				<div id="textthumbnail">Click on the enrichment thumbnail to get more information</div>
			</div>
			<div class="redSeperatorVertical"></div>
			<div class="seperator"></div>
			<div class="redSeperator1"></div>
			<div class="rewardsContainer">
				<div class="imageContainer">
					<div class="thumbnailContainer"></div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="activated"></div></div>
					<div class="sparkieText">Light</div>
					<div class="explanationText">Great going! You made it to next level!</div>
				</div>
				<div class="activateButton">ACTIVATE<div class="pointedWhite"></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div class="imageContainer">
					<div class="thumbnailContainer"></div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="activated"></div></div>
					<div class="sparkieText">Dark</div>
					<div class="explanationText">Great going! You made it to next level!</div>
				</div>
				<div class="activateButton inactiveButton">This theme is ACTIVATED<div class="pointedRed"></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div class="imageContainer">
					<div class="thumbnailContainer"></div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="trade"></div></div>
					<div class="sparkieText">Princess</div>
					<div class="explanationText">Trade 20 SuperSparkies to activate this theme</div>
				</div>
				<div class="activateButton">TRADE<div class="pointedWhite"></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer locked">
				<div class="imageContainer">
					<div class="thumbnailContainer"></div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="notActivated"></div></div>
					<div class="sparkieText">Sporty</div>
					<div class="explanationText">Earn 650 SuperSparkies to unlock this theme</div>
				</div>
				<div class="activateButton lockedButton">LOCKED<div class="pointedWhite"></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div class="imageContainer">
					<div class="thumbnailContainer"></div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="activated"></div></div>
					<div class="sparkieText">Anniversary</div>
					<div class="explanationText">Congratulations! Enjoy your special theme</div>
				</div>
				<div class="activateButton">ACTIVATE<div class="pointedWhite"></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div class="imageContainer">
					<div class="thumbnailContainer"></div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="activated"></div></div>
					<div class="sparkieText">SparkieSpecial</div>
					<div class="explanationText">Congratulations! Enjoy your special theme</div>
				</div>
				<div class="activateButton">ACTIVATE<div class="pointedWhite"></div>
				</div>
			</div>
		</div>-->
	</div>
</form>		
	</div>
    <?php
include("/mindspark/userInterface/classes/clsRewardSystem.php");
$userID = $_SESSION['userID'];
$sparkieInformation = new Sparkies($userID);
$rewardTheme = $sparkieInformation->rewardTheme;
?>
<?php if($rewardTheme!="default") { ?>
<?php if($theme==2) { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/midClass/<?php echo $rewardTheme; ?>.css" />
<?php } else if($theme==3) { ?>
    <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css" />
<?php } }?>
<?php include("../../footer.php");?>
