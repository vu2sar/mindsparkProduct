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
	$currentYear =date('Y');
	//$currentMonth=date('m');
	$currentMonth=13;
	
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];
	$childName 	   = $objUser->childName;
	$schoolCode    = $objUser->schoolCode;
	$childClass    = $objUser->childClass;
	$childSection  = $objUser->childSection;
	$category 	   = $objUser->category;
	$subcategory   = $objUser->subcategory;	
	$schoolSectionCondition="";	
	if(!(($category=='STUDENT' && $subcategory=='Individual') || $category=='GUEST')){
		$schoolSectionCondition=(is_null($childSection)?"AND ISNULL(b.childSection)":"AND b.childSection='".$childSection."'");
		$schoolSectionCondition.=" and b.schoolCode=".$schoolCode;
	}
	$sparkieInformation = new Sparkies($userID);
	$sparkieStarClass = array(5,6,7);
	for($i=1;$i<=12;$i++){
		if($i<10){
			$j= "0".$i;
		}else{
			$j=$i;
		}
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='bonusChamp' and b.childClass=".$childClass." AND MONTH(batchDate)=$j $schoolSectionCondition";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$bonusChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$bonusChampArray[$i]=$cName;
		if(in_array($childClass, $sparkieStarClass))
		{			
			$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='sparkieStar' and b.childClass=".$childClass." AND MONTH(batchDate)=$j $schoolSectionCondition";
			$result = mysql_query($query);
			$line	=	mysql_fetch_array($result);
			$sparkieStarArrayId[$i]=$line[0];
			$query1= "select childName from adepts_userDetails where userID=".$line[0];
			$result1 = mysql_query($query1);
			$line1	=	mysql_fetch_array($result1);
			$cName = explode(" ", $line1[0]);
			$cName = $cName[0];
			$sparkieStarArray[$i]=$cName;
		}
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='accuracyMonthly' and b.childClass=".$childClass." AND MONTH(batchDate)=$j $schoolSectionCondition";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$accuracyChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$accuracyChampArray[$i]=$cName;
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='consistentUsageMonthly' and b.childClass=".$childClass." AND MONTH(batchDate)=$j $schoolSectionCondition";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$consistencyChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$consistencyChampArray[$i]=$cName;
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='homeUsageChamp' and b.childClass=".$childClass." AND MONTH(batchDate)=$j $schoolSectionCondition";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$homeChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$homeChampArray[$i]=$cName;
	}
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
	$sparkieImage = $_SESSION['sparkieImage'];
?>

<?php include("../../header.php");?>

<title>Rewards Central</title>
	<script src="../../libs/jquery.js"></script>
	<?php
	if($theme==2) { ?>
	<link href="../../css/classLeaderBoard/midClass.css?ver=2" rel="stylesheet" type="text/css">
    <link href="../../css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		var infoClick=0;
		var a=0;
		var b=0;
		var e=0;
		function load(){
			$('#activitiesContainer1').remove();
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
				var a= window.innerHeight -130 -45;
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
		<link href="../../css/classLeaderBoard/higherClass.css" rel="stylesheet" type="text/css">
		<script>
		function load(){
			var a= window.innerHeight - (170);
			$('#activitiesContainer').remove();
			$('#activitiesContainer1').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menu_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			var ua1 = navigator.userAgent;
			if( ua1.indexOf("Android") >= 0 )
			{
				$("#activitiesContainer1").css("height","auto");
				$('#menu_bar').css("height",$('#activitiesContainer1').css("height"));
				$("#sideBar").css("height",$("#activitiesContainer1").css("height"));
				$("#main_bar").css("height",$("#activitiesContainer1").css("height"));
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
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
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
						<div id="textMain">YOUR CLASS LEADER BOARD</div>
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
        </div>
		<a href="historyDashboard.php">
        <div id="history" class="hidden forHigherOnly">
            <span id="classText">Sparkie Bank</span>
            <div id="hM" class="pointed2">
            </div>
        </div></a>
		<a href="themesDashboard.php">
		<div id="themes" class="hidden forHigherOnly">
            <span id="classText">Themes</span>
            <div id="tM" class="pointed3">
            </div>
        </div></a>
		<div id="leaderBoard" class="hidden forHigherOnly">
			<div id="leaderBoardImage"></div>
            <span id="classText">Class LeaderBoard</span>
            <div id="lM" class="pointed4">
            </div>
        </div>
		<div id="sparkieBarMid" class="forHigherOnly hidden">
			<div id="leftSparkieBar">
				<div id="textMain">YOUR CLASS LEADER BOARD</div>
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
<form name="frmActivitySelection" id="frmActivitySelection" method="POST" action="enrichmentModule.php">
	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="report">
					<span id="reportText">Your Class LeaderBoard</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div>
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
				<a href="themesDashboard.php">
				<div id="themes" onClick="redirect()" class="forHighestOnly">
					<div id="aM" class="pointed3">
					</div></br>
					Themes
				</div></a>
			</div>
			</div>
	<div id="activitiesContainer">
		<table border="0" width="100%" style="margin-top:10px">
			<tr style="color: #B3B3B3;font-family: 'Conv_HelveticaLTStd-BoldCond';font-size: 1.3em;">
				<td width="6.3%" align="center"></td>
				<td width="12.6%" align="center"></td>
				<td width="6.6%" align="center" <?php if($currentMonth>1) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">JAN</td>
				<td width="6.6%" align="center" <?php if($currentMonth>2) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">FEB</td>
				<td width="6.6%" align="center" <?php if($currentMonth>3) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">MARCH</td>
				<td width="6.6%" align="center" <?php if($currentMonth>4) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">APRIL</td>
				<td width="6.6%" align="center" <?php if($currentMonth>5) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">MAY</td>
				<td width="6.6%" align="center" <?php if($currentMonth>6) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">JUNE</td>
				<td width="6.6%" align="center" <?php if($currentMonth>7) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">JULY</td>
				<td width="6.6%" align="center" <?php if($currentMonth>8) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">AUGUST</td>
				<td width="6.6%" align="center" <?php if($currentMonth>9) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">SEPT</td>
				<td width="6.6%" align="center" <?php if($currentMonth>10) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">OCT</td>
				<td width="6.6%" align="center" <?php if($currentMonth>11) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">NOV</td>
				<td width="8.5%" align="center" <?php if($currentMonth>=12) echo "class='colorCalender'"; ?> style="border-right: 1px solid gray;padding:7px;">DEC</td>
			</tr>
			<?php if(in_array($childClass, $sparkieStarClass))
			{?>
			<tr>
				<td width="6.3%"><div class="badgeText">SPARKIE STAR</div></td>
				<td width="12.6%"><img src="../../assets/rewards/monthlyBadges/sparkieStar.png" width="120px"/></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[1]==$userID) echo 'greatMessage'; else if($currentMonth>1 && $sparkieStarArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($sparkieStarArrayId[1]==$userID) echo 'GREAT GOING, '; else if($currentMonth>1 && $sparkieStarArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[1]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[2]==$userID) echo 'greatMessage'; else if($currentMonth>2 && $sparkieStarArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($sparkieStarArrayId[2]==$userID) echo 'GREAT GOING, '; else if($currentMonth>2 && $sparkieStarArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[2]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[3]==$userID) echo 'greatMessage'; else if($currentMonth>3 && $sparkieStarArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($sparkieStarArrayId[3]==$userID) echo 'GREAT GOING, '; else if($currentMonth>3 && $sparkieStarArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[3]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[4]==$userID) echo 'greatMessage'; else if($currentMonth>4 && $sparkieStarArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($sparkieStarArrayId[4]==$userID) echo 'GREAT GOING, '; else if($currentMonth>4 && $sparkieStarArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[4]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[5]==$userID) echo 'greatMessage'; else if($currentMonth>5 && $sparkieStarArrayId[5]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[5]==$userID) echo 'GREAT GOING, '; else if($currentMonth>5 && $sparkieStarArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[5]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[6]==$userID) echo 'greatMessage'; else if($currentMonth>6 && $sparkieStarArrayId[6]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[6]==$userID) echo 'GREAT GOING, '; else if($currentMonth>6 && $sparkieStarArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[6]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[7]==$userID) echo 'greatMessage'; else if($currentMonth>7 && $sparkieStarArrayId[7]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[7]==$userID) echo 'GREAT GOING, '; else if($currentMonth>7 && $sparkieStarArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[7]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[8]==$userID) echo 'greatMessage'; else if($currentMonth>8 && $sparkieStarArrayId[8]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[8]==$userID) echo 'GREAT GOING, '; else if($currentMonth>8 && $sparkieStarArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[8]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[9]==$userID) echo 'greatMessage'; else if($currentMonth>9 && $sparkieStarArrayId[9]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[9]==$userID) echo 'GREAT GOING, '; else if($currentMonth>9 && $sparkieStarArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[9]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[10]==$userID) echo 'greatMessage'; else if($currentMonth>10 && $sparkieStarArrayId[10]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[10]==$userID) echo 'GREAT GOING, '; else if($currentMonth>10 && $sparkieStarArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[10]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[11]==$userID) echo 'greatMessage'; else if($currentMonth>11 && $sparkieStarArrayId[11]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[11]==$userID) echo 'GREAT GOING, '; else if($currentMonth>11 && $sparkieStarArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[11]?></td>
				<td width="8.5%" align="center" valign="center" class="calenderTableData <?php if($sparkieStarArrayId[12]==$userID) echo 'greatMessage'; else if($currentMonth>12 && $sparkieStarArrayId[12]!="") echo 'elseMessage'; ?>" ><?php if($sparkieStarArrayId[12]==$userID) echo 'GREAT GOING, '; else if($currentMonth>12 && $sparkieStarArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$sparkieStarArray[12]?></td>
			</tr>
			<?php } ?>
			<tr>
				<td width="6.3%"><div class="badgeText">MARKSMAN</div></td>
				<td width="12.6%"><img src="../../assets/rewards/monthlyBadges/monthlyAccuracy.png" width="120px"/></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[1]==$userID) echo 'greatMessage'; else if($currentMonth>1 && $accuracyChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[1]==$userID) echo 'GREAT GOING, '; else if($currentMonth>1 && $accuracyChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[1]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[2]==$userID) echo 'greatMessage'; else if($currentMonth>2 && $accuracyChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[2]==$userID) echo 'GREAT GOING, '; else if($currentMonth>2 && $accuracyChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[2]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[3]==$userID) echo 'greatMessage'; else if($currentMonth>3 && $accuracyChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[3]==$userID) echo 'GREAT GOING, '; else if($currentMonth>3 && $accuracyChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[3]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[4]==$userID) echo 'greatMessage'; else if($currentMonth>4 && $accuracyChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[4]==$userID) echo 'GREAT GOING, '; else if($currentMonth>4 && $accuracyChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[4]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[5]==$userID) echo 'greatMessage'; else if($currentMonth>5 && $accuracyChampArrayId[5]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[5]==$userID) echo 'GREAT GOING, '; else if($currentMonth>5 && $accuracyChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[5]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[6]==$userID) echo 'greatMessage'; else if($currentMonth>6 && $accuracyChampArrayId[6]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[6]==$userID) echo 'GREAT GOING, '; else if($currentMonth>6 && $accuracyChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[6]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[7]==$userID) echo 'greatMessage'; else if($currentMonth>7 && $accuracyChampArrayId[7]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[7]==$userID) echo 'GREAT GOING, '; else if($currentMonth>7 && $accuracyChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[7]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[8]==$userID) echo 'greatMessage'; else if($currentMonth>8 && $accuracyChampArrayId[8]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[8]==$userID) echo 'GREAT GOING, '; else if($currentMonth>8 && $accuracyChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[8]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[9]==$userID) echo 'greatMessage'; else if($currentMonth>9 && $accuracyChampArrayId[9]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[9]==$userID) echo 'GREAT GOING, '; else if($currentMonth>9 && $accuracyChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[9]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[10]==$userID) echo 'greatMessage'; else if($currentMonth>10 && $accuracyChampArrayId[10]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[10]==$userID) echo 'GREAT GOING, '; else if($currentMonth>10 && $accuracyChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[10]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[11]==$userID) echo 'greatMessage'; else if($currentMonth>11 && $accuracyChampArrayId[11]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[11]==$userID) echo 'GREAT GOING, '; else if($currentMonth>11 && $accuracyChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[11]?></td>
				<td width="8.5%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[12]==$userID) echo 'greatMessage'; else if($currentMonth>12 && $accuracyChampArrayId[12]!="") echo 'elseMessage'; ?>" ><?php if($accuracyChampArrayId[12]==$userID) echo 'GREAT GOING, '; else if($currentMonth>12 && $accuracyChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$accuracyChampArray[12]?></td>
			</tr>
			<tr>
				<td width="6.3%"><div class="badgeText">STEADFAST</div></td>
				<td width="12.6%"><img src="../../assets/rewards/monthlyBadges/monthlyConsistency.png"/></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[1]==$userID) echo 'greatMessage'; else if($currentMonth>1 && $consistencyChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[1]==$userID) echo 'GREAT GOING, '; else if($currentMonth>1 && $consistencyChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[1]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[2]==$userID) echo 'greatMessage'; else if($currentMonth>2 && $consistencyChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[2]==$userID) echo 'GREAT GOING, '; else if($currentMonth>2 && $consistencyChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[2]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[3]==$userID) echo 'greatMessage'; else if($currentMonth>3 && $consistencyChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[3]==$userID) echo 'GREAT GOING, '; else if($currentMonth>3 && $consistencyChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[3]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[4]==$userID) echo 'greatMessage'; else if($currentMonth>4 && $consistencyChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[4]==$userID) echo 'GREAT GOING, '; else if($currentMonth>4 && $consistencyChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[4]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[5]==$userID) echo 'greatMessage'; else if($currentMonth>5 && $consistencyChampArrayId[5]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[5]==$userID) echo 'GREAT GOING, '; else if($currentMonth>5 && $consistencyChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[5]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[6]==$userID) echo 'greatMessage'; else if($currentMonth>6 && $consistencyChampArrayId[6]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[6]==$userID) echo 'GREAT GOING, '; else if($currentMonth>6 && $consistencyChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[6]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[7]==$userID) echo 'greatMessage'; else if($currentMonth>7 && $consistencyChampArrayId[7]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[7]==$userID) echo 'GREAT GOING, '; else if($currentMonth>7 && $consistencyChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[7]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[8]==$userID) echo 'greatMessage'; else if($currentMonth>8 && $consistencyChampArrayId[8]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[8]==$userID) echo 'GREAT GOING, '; else if($currentMonth>8 && $consistencyChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[8]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[9]==$userID) echo 'greatMessage'; else if($currentMonth>9 && $consistencyChampArrayId[9]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[9]==$userID) echo 'GREAT GOING, '; else if($currentMonth>9 && $consistencyChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[9]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[10]==$userID) echo 'greatMessage'; else if($currentMonth>10 && $consistencyChampArrayId[10]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[10]==$userID) echo 'GREAT GOING, '; else if($currentMonth>10 && $consistencyChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[10]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[11]==$userID) echo 'greatMessage'; else if($currentMonth>11 && $consistencyChampArrayId[11]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[11]==$userID) echo 'GREAT GOING, '; else if($currentMonth>11 && $consistencyChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[11]?></td>
				<td width="8.5%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[12]==$userID) echo 'greatMessage'; else if($currentMonth>12 && $consistencyChampArrayId[12]!="") echo 'elseMessage'; ?>" ><?php if($consistencyChampArrayId[12]==$userID) echo 'GREAT GOING, '; else if($currentMonth>12 && $consistencyChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$consistencyChampArray[12]?></td>
				
			</tr>
			<tr>
				<td width="6.3%"><div class="badgeText">BONUS <br/>CHAMP</div></td>
				<td width="12.6%"><img src="../../assets/rewards/monthlyBadges/monthlyBonusSpecial.png"/></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[1]==$userID) echo 'greatMessage'; else if($currentMonth>1 && $bonusChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[1]==$userID) echo 'GREAT GOING, '; else if($currentMonth>1 && $bonusChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[1]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[2]==$userID) echo 'greatMessage'; else if($currentMonth>2 && $bonusChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[2]==$userID) echo 'GREAT GOING, '; else if($currentMonth>2 && $bonusChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[2]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[3]==$userID) echo 'greatMessage'; else if($currentMonth>3 && $bonusChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[3]==$userID) echo 'GREAT GOING, '; else if($currentMonth>3 && $bonusChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[3]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[4]==$userID) echo 'greatMessage'; else if($currentMonth>4 && $bonusChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[4]==$userID) echo 'GREAT GOING, '; else if($currentMonth>4 && $bonusChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[4]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[5]==$userID) echo 'greatMessage'; else if($currentMonth>5 && $bonusChampArrayId[5]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[5]==$userID) echo 'GREAT GOING, '; else if($currentMonth>5 && $bonusChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[5]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[6]==$userID) echo 'greatMessage'; else if($currentMonth>6 && $bonusChampArrayId[6]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[6]==$userID) echo 'GREAT GOING, '; else if($currentMonth>6 && $bonusChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[6]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[7]==$userID) echo 'greatMessage'; else if($currentMonth>7 && $bonusChampArrayId[7]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[7]==$userID) echo 'GREAT GOING, '; else if($currentMonth>7 && $bonusChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[7]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[8]==$userID) echo 'greatMessage'; else if($currentMonth>8 && $bonusChampArrayId[8]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[8]==$userID) echo 'GREAT GOING, '; else if($currentMonth>8 && $bonusChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[8]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[9]==$userID) echo 'greatMessage'; else if($currentMonth>9 && $bonusChampArrayId[9]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[9]==$userID) echo 'GREAT GOING, '; else if($currentMonth>9 && $bonusChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[9]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[10]==$userID) echo 'greatMessage'; else if($currentMonth>10 && $bonusChampArrayId[10]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[10]==$userID) echo 'GREAT GOING, '; else if($currentMonth>10 && $bonusChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[10]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[11]==$userID) echo 'greatMessage'; else if($currentMonth>11 && $bonusChampArrayId[11]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[11]==$userID) echo 'GREAT GOING, '; else if($currentMonth>11 && $bonusChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[11]?></td>
				<td width="8.5%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[12]==$userID) echo 'greatMessage'; else if($currentMonth>12 && $bonusChampArrayId[12]!="") echo 'elseMessage'; ?>" ><?php if($bonusChampArrayId[12]==$userID) echo 'GREAT GOING, '; else if($currentMonth>12 && $bonusChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$bonusChampArray[12]?></td>
			</tr>
			<tr>
				<td width="6.3%"><div class="badgeText">OVERCLOCK</div></td>
				<td width="12.6%"><img src="../../assets/rewards/monthlyBadges/monthlyHome.png"/></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[1]==$userID) echo 'greatMessage'; else if($currentMonth>1 && $homeChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[1]==$userID) echo 'GREAT GOING, '; else if($currentMonth>1 && $homeChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[1]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[2]==$userID) echo 'greatMessage'; else if($currentMonth>2 && $homeChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[2]==$userID) echo 'GREAT GOING, '; else if($currentMonth>2 && $homeChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[2]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[3]==$userID) echo 'greatMessage'; else if($currentMonth>3 && $homeChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[3]==$userID) echo 'GREAT GOING, '; else if($currentMonth>3 && $homeChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[3]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[4]==$userID) echo 'greatMessage'; else if($currentMonth>4 && $homeChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[4]==$userID) echo 'GREAT GOING, '; else if($currentMonth>4 && $homeChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[4]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[5]==$userID) echo 'greatMessage'; else if($currentMonth>5 && $homeChampArrayId[5]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[5]==$userID) echo 'GREAT GOING, '; else if($currentMonth>5 && $homeChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[5]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[6]==$userID) echo 'greatMessage'; else if($currentMonth>6 && $homeChampArrayId[6]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[6]==$userID) echo 'GREAT GOING, '; else if($currentMonth>6 && $homeChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[6]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[7]==$userID) echo 'greatMessage'; else if($currentMonth>7 && $homeChampArrayId[7]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[7]==$userID) echo 'GREAT GOING, '; else if($currentMonth>7 && $homeChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[7]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[8]==$userID) echo 'greatMessage'; else if($currentMonth>8 && $homeChampArrayId[8]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[8]==$userID) echo 'GREAT GOING, '; else if($currentMonth>8 && $homeChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[8]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[9]==$userID) echo 'greatMessage'; else if($currentMonth>9 && $homeChampArrayId[9]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[9]==$userID) echo 'GREAT GOING, '; else if($currentMonth>9 && $homeChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[9]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[10]==$userID) echo 'greatMessage'; else if($currentMonth>10 && $homeChampArrayId[10]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[10]==$userID) echo 'GREAT GOING, '; else if($currentMonth>10 && $homeChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[10]?></td>
				<td width="6.6%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[11]==$userID) echo 'greatMessage'; else if($currentMonth>11 && $homeChampArrayId[11]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[11]==$userID) echo 'GREAT GOING, '; else if($currentMonth>11 && $homeChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[11]?></td>
				<td width="8.5%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[12]==$userID) echo 'greatMessage'; else if($currentMonth>12 && $homeChampArrayId[12]!="") echo 'elseMessage'; ?>" ><?php if($homeChampArrayId[12]==$userID) echo 'GREAT GOING, '; else if($currentMonth>12 && $homeChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/><br/>'; ?><?=$homeChampArray[12]?></td>
			</tr>
		</table>
	</div>
	<div id="activitiesContainer1">
		<table border="0" width="100%" align="center">
			<tr>
				<td width="8%"></td>
				<td width="23%" align="center" class="rightBorder"><img src="../../assets/rewards/monthlyBadges/monthlyAccuracy.png" width="60%"/></td>
				<td width="23%" align="center" class="rightBorder"><img src="../../assets/rewards/monthlyBadges/monthlyConsistency.png" width="60%"/></td>
				<td width="23%" align="center" class="rightBorder"><img src="../../assets/rewards/monthlyBadges/monthlyBonusSpecial.png" width="60%"/></td>
				<td width="23%" align="center"><img src="../../assets/rewards/monthlyBadges/monthlyHome.png" width="60%"/></td>
			</tr>
			<tr>
				<td width="8%"></td>
				<td width="23%" valign="top" class="rightBorder" height="40px" align="center"><div class="badgeText">MARKSMAN</div></td>
				<td width="23%" valign="top" class="rightBorder" height="40px" align="center"><div class="badgeText">STEADFAST</div></td>
				<td width="23%" valign="top" class="rightBorder" height="40px" align="center"><div class="badgeText">BONUS CHAMP</div></td>
				<td width="23%" valign="top" height="40px" align="center"><div class="badgeText">OVERCLOCK</div></td>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>1) echo " colorCalender"; ?>" align="right">JAN</td>
				<?php if($currentMonth>1){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[1]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[1]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[1]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[1]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[1]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[1]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[1]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[1]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[1]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[1]==$userID) echo 'greatMessage'; else if($homeChampArrayId[1]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[1]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[1]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[1]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>2) echo " colorCalender"; ?>" align="right">FEB</td>
				<?php if($currentMonth>2){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[2]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[2]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[2]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[2]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[2]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[2]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[2]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[2]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[2]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[2]==$userID) echo 'greatMessage'; else if($homeChampArrayId[2]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[2]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[2]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[2]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>3) echo " colorCalender"; ?>" align="right">MARCH</td>
				<?php if($currentMonth>3){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[3]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[3]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[3]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[3]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[3]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[3]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[3]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[3]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[3]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[3]==$userID) echo 'greatMessage'; else if($homeChampArrayId[3]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[3]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[3]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[3]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>4) echo " colorCalender"; ?>" align="right">APRIL</td>
				<?php if($currentMonth>4){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[4]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[4]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[4]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[4]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[4]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[4]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[4]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[4]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[4]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[4]==$userID) echo 'greatMessage'; else if($homeChampArrayId[4]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[4]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[4]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[4]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>5) echo " colorCalender"; ?>" align="right">MAY</td>
				<?php if($currentMonth>5){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[5]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[5]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[5]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[5]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[5]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[5]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[5]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[5]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[5]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[51]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[5]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[5]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[5]==$userID) echo 'greatMessage'; else if($homeChampArrayId[5]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[5]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[5]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[5]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>6) echo " colorCalender"; ?>" align="right">JUNE</td>
				<?php if($currentMonth>6){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[6]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[6]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[6]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[6]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[6]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[6]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[6]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[6]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[6]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[6]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[6]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[6]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[6]==$userID) echo 'greatMessage'; else if($homeChampArrayId[6]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[6]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[6]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[6]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>7) echo " colorCalender"; ?>" align="right">JULY</td>
				<?php if($currentMonth>7){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[7]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[7]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[7]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[7]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[7]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[7]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[7]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[7]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[7]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[7]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[7]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[7]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[7]==$userID) echo 'greatMessage'; else if($homeChampArrayId[7]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[7]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[7]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[7]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>8) echo " colorCalender"; ?>" align="right">AUGUST</td>
				<?php if($currentMonth>8){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[8]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[8]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[8]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[8]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[8]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[8]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[8]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[8]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[8]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[8]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[8]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[8]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[8]==$userID) echo 'greatMessage'; else if($homeChampArrayId[8]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[8]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[8]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[8]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>9) echo " colorCalender"; ?>" align="right">SEPT</td>
				<?php if($currentMonth>9){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[9]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[9]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[9]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[9]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[9]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[9]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[9]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[9]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[9]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[9]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[9]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[9]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[9]==$userID) echo 'greatMessage'; else if($homeChampArrayId[9]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[9]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[9]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[9]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>10) echo " colorCalender"; ?>" align="right">OCT</td>
				<?php if($currentMonth>10){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[10]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[10]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[10]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[10]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[10]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[10]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[10]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/>'; ?><?=$consistencyChampArray[10]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[10]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[10]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[10]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[10]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[10]==$userID) echo 'greatMessage'; else if($homeChampArrayId[10]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[10]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[10]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[10]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>11) echo " colorCalender"; ?>" align="right">NOV</td>
				<?php if($currentMonth>11){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[11]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[11]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[11]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[11]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[11]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[11]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[11]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$consistencyChampArray[11]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[11]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[11]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[11]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[11]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[11]==$userID) echo 'greatMessage'; else if($homeChampArrayId[11]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[11]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[11]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[11]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="8%" class="calenderTable<?php if($currentMonth>12) echo " colorCalender"; ?>" align="right">DEC</td>
				<?php if($currentMonth>12){
				?>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($accuracyChampArrayId[12]==$userID) echo 'greatMessage'; else if($accuracyChampArrayId[12]!="") echo 'elseMessage'; ?>"><?php if($accuracyChampArrayId[12]==$userID) echo 'GREAT GOING,<br/> '; else if($accuracyChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$accuracyChampArray[12]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($consistencyChampArrayId[12]==$userID) echo 'greatMessage'; else if($consistencyChampArrayId[12]!="") echo 'elseMessage'; ?>"><?php if($consistencyChampArrayId[12]==$userID) echo 'GREAT GOING,<br/> '; else if($consistencyChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span> <br/>'; ?><?=$consistencyChampArray[12]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($bonusChampArrayId[12]==$userID) echo 'greatMessage'; else if($bonusChampArrayId[12]!="") echo 'elseMessage'; ?>"><?php if($bonusChampArrayId[12]==$userID) echo 'GREAT GOING,<br/> '; else if($bonusChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$bonusChampArray[12]?></td>
					<td width="22%" align="center" valign="center" class="calenderTableData <?php if($homeChampArrayId[12]==$userID) echo 'greatMessage'; else if($homeChampArrayId[12]!="") echo 'elseMessage'; ?>"><?php if($homeChampArrayId[12]==$userID) echo 'GREAT GOING,<br/> '; else if($homeChampArrayId[12]!="") echo '<span style="color:#666666">GREAT GOING!</span><br/>'; ?><?=$homeChampArray[12]?></td>
				<?php } else{
				?>	
					<td colspan="4" class="noDataFeild"></td>
				<?php } ?>
			</tr>
		</table>
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