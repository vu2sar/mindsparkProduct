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
	$currentMonth=intval(date('m'));
	
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];
	$childName 	   = $objUser->childName;
	$schoolCode    = $objUser->schoolCode;
	$childClass    = $objUser->childClass;
	$childSection  = $objUser->childSection;
	$category 	   = $objUser->category;
	$subcategory   = $objUser->subcategory;
	$schoolCode    = $objUser->schoolCode;
	$sparkies = 0;
	$superSparkies = 0;
	$megaSparkies = 0;
	$sparkieStarClass = array(5,6,7);
	for($i=0;$i<$currentMonth;$i++){
		$bonusChampArray[$i]="#CCCCCC";
		$accuracyChampArray[$i]="#CCCCCC";
		$sparkieStarArray[$i]="#CCCCCC";
		$consistencyChampArray[$i]="#CCCCCC";
		$homeChampArray[$i]="#CCCCCC";
	}
	for($i=$currentMonth;$i<12;$i++){
		$bonusChampArray[$i]="#fff";
		$accuracyChampArray[$i]="#fff";
		$sparkieStarArray[$i]="#fff";
		$consistencyChampArray[$i]="#fff";
		$homeChampArray[$i]="#fff";
	}
	$sparkiesExplanation = "Buck up! Earn 250 sparkies to upgrade.";
	$superSparkiesExplanation = "Buck up! Earn 250 sparkies to upgrade.";
	$megaSparkiesExplanation = "Buck up! Earn 750 sparkies to upgrade.";
	$sparkieInformation = new Sparkies($userID);
	$badgesArray = $sparkieInformation->checkForBadgesEarned();	
	for($i=0;$i<sizeOf($badgesArray);$i++){
		if(isset($badgesArray[$i]['mileStone1'])){
			$milestone1 = 1;
		}else if(isset($badgesArray[$i]['mileStone2'])){
			$milestone2 = 1;
		}else if(isset($badgesArray[$i]['mileStone3'])){
			$milestone3 = 1;
		}else if(isset($badgesArray[$i]['bonusChamp'])){
			$bonusChampArray[intval(substr($badgesArray[$i]['bonusChamp']['batchDate'],5,2))-1] = "#4D4D4D";
			$bonusChampValue++;
			$bonusChampUnlocked=1;
		}else if(isset($badgesArray[$i]['accuracyMonthly'])){
			$accuracyChampArray[intval(substr($badgesArray[$i]['accuracyMonthly']['batchDate'],5,2))-1] = "#4D4D4D";
			$accuracyChampValue++;
			$accuracyChampUnlocked=1;
		}else if(isset($badgesArray[$i]['sparkieStar'])){
			$sparkieStarArray[intval(substr($badgesArray[$i]['sparkieStar']['batchDate'],5,2))-1] = "#4D4D4D";
			$sparkieStarValue++;
			$sparkieStarUnlocked=1;
		}else if(isset($badgesArray[$i]['consistentUsageMonthly'])){
			$consistencyChampArray[intval(substr($badgesArray[$i]['consistentUsageMonthly']['batchDate'],5,2))-1] = "#4D4D4D";
			$consistencyChampValue++;
			$consistencyChampUnlocked=1;
		}else if(isset($badgesArray[$i]['homeUsageChamp'])){
			$homeChampArray[intval(substr($badgesArray[$i]['homeUsageChamp']['batchDate'],5,2))-1] = "#4D4D4D";
			$homeChampValue++;
			$homeChampUnlocked=1;	
		}
	}
	$timelineSparkie1="";
	$sparkiesEarned = $sparkieInformation->getTotalSparkies();
	$sparkieLogic = $sparkieInformation->getSparkieLogic();
	if($sparkiesEarned > $sparkieLogic['mileStone1']['sparkieNeeded']){
		$sparkies = $sparkieLogic['mileStone1']['sparkieNeeded'];
		$timelineSparkie1 = "milestone1";
		$sparkiesExplanation = "Great going! You made it to next level!";
		$superSparkiesExplanation = "Goodluck! This will be a challenging level.";
		if($sparkiesEarned > $sparkieLogic['mileStone2']['sparkieNeeded']){
			$superSparkiesExplanation = "Great going! You made it to next level!";
			$megaSparkiesExplanation = "Goodluck! This will be a challenging level.";
			$superSparkies = $sparkieLogic['mileStone2']['sparkieNeeded'] - $sparkieLogic['mileStone1']['sparkieNeeded'];
			$timelineSparkie1 = "milestone2";
			if($sparkiesEarned > $sparkieLogic['mileStone3']['sparkieNeeded']){
				$timelineSparkie1 = "milestone3";
				$megaSparkiesExplanation = "Great going! You made it to next level!";
				$megaSparkies = $sparkiesEarned - $sparkieLogic['mileStone2']['sparkieNeeded'];
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
	<link href="../../css/rewardsDashboard/midClass.css?ver=3" rel="stylesheet" type="text/css">
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
			var bonusChampColor = <?php echo json_encode($bonusChampArray); ?>;
			CreatePieChart("bonusPie",bonusChampColor);
			var accuracyChampColor = <?php echo json_encode($accuracyChampArray); ?>;
			CreatePieChart("accuracyPie",accuracyChampColor);
			var sparkieStarColor = <?php echo json_encode($sparkieStarArray); ?>;
			CreatePieChart("SparkieStarPie",sparkieStarColor);
			var consistencyChampColor = <?php echo json_encode($consistencyChampArray); ?>;
			CreatePieChart("consistencyPie",consistencyChampColor);
			var homeChampColor = <?php echo json_encode($homeChampArray); ?>;
			CreatePieChart("homePie",homeChampColor);
			
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
		<link href="../../css/rewardsDashboard/higherClass.css" rel="stylesheet" type="text/css">
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
			var bonusChampColor = <?php echo json_encode($bonusChampArray); ?>;
			CreatePieChart("bonusPie",bonusChampColor);
			var accuracyChampColor = <?php echo json_encode($accuracyChampArray); ?>;
			CreatePieChart("accuracyPie",accuracyChampColor);
			var sparkieStarColor = <?php echo json_encode($sparkieStarArray); ?>;
			CreatePieChart("SparkieStarPie",sparkieStarColor);
			var consistencyChampColor = <?php echo json_encode($consistencyChampArray); ?>;
			CreatePieChart("consistencyPie",consistencyChampColor);
			var homeChampColor = <?php echo json_encode($homeChampArray); ?>;
			CreatePieChart("homePie",homeChampColor);
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
	<script>

    function CreatePieChart(canvas,myColor)
    {
		var canvasName = canvas;
		var myColor = myColor;
        var canvas = document.getElementById(canvasName);
		var ctx = canvas.getContext("2d");
		var lastend = 4.71238898;
		var data = [30,30,30,30,30,30,30,30,30,30,30,30];
		var myTotal = 0;

		for(var e = 0; e < data.length; e++)
		{
		  myTotal += data[e];
		}

		for (var i = 0; i < data.length; i++) 
		{
		    ctx.fillStyle = myColor[i];
		    ctx.beginPath();
		    ctx.lineTo(canvas.width/2,canvas.height/2);
			ctx.strokeStyle = '#ADA7A7';
			ctx.lineWidth = 1;
			ctx.stroke();
		    // Arc Parameters: x, y, radius, startingAngle (radians), endingAngle (radians), antiClockwise (boolean)
		    ctx.arc(canvas.width/2,canvas.height/2,canvas.height/2,lastend,lastend+(Math.PI*2*(data[i]/myTotal)),false);
		    ctx.lineTo(canvas.width/2,canvas.height/2);
			ctx.lineWidth = 1;
			ctx.stroke();
		    ctx.fill();
			ctx.closePath();
		    lastend += Math.PI*2*(data[i]/myTotal);
		}
    }
	$("#container").live('click',function(e){
		if ($(e.target).attr("id")=="activitiesContainer") {
		   $(".hideClass").hide();
		}
	});
	function showMsg(id){
		var class1=id;
		$(".hideClass").hide();
		$("."+class1).show();
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
						<div id="textMain">THESE ARE YOUR REWARDS</div>
						<div id="textthumbnail">Click on rewards badges to get more information.</div>
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
		<a href="classLeaderBoard.php">
		<div id="leaderBoard" class="hidden forHigherOnly">
			<div id="leaderBoardImage"></div>
            <span id="classText">Class LeaderBoard</span>
            <div id="lM" class="pointed4">
            </div>
        </div></a>
		<div id="sparkieBarMid" class="hidden forHigherOnly">
			<div id="leftSparkieBar">
				<div id="textMain">THESE ARE YOUR REWARDS</div>
				<div id="textthumbnail">Click on rewards badges to get more information.</div>
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
	<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<a href="classLeaderBoard.php">
				<div id="report">
					<span id="reportText">Your Class LeaderBoard</span>
					<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>
				</div></a>
				<div id="rewards" class="forHighestOnly">
					<div id="naM" class="pointed1">
					</div></br>
					Rewards
				</div>
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
		<div id="rewardsContainer">
			
		<div class="notAttemptedText">Sparkies</div><br/><br/><br/>
			<div class="rewardsContainer <?php if($sparkies=="0") echo "locked"; ?>">
				<div id="sparkieImage"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie"><?=$sparkies?></div></div>
					<div class="sparkieText">Sparkies</div>
					<div class="explanationText"><?=$sparkiesExplanation?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($superSparkies=="0") echo "locked"; ?>">
				<div id="superSparkiesImage"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie"><?=$superSparkies?></div></div>
					<div class="sparkieText">SuperSparkies</div>
					<div class="explanationText"><?=$superSparkiesExplanation?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($megaSparkies=="0") echo "locked"; ?>">
				<div id="megaSparkiesImage"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie"><?=$megaSparkies?></div></div>
					<div class="sparkieText">MegaSparkies</div>
					<div class="explanationText"><?=$megaSparkiesExplanation?></div>
				</div>
			</div>
			<div style="clear:both"></div>
			<div class="notAttemptedText">Badges</div><br/><br/><br/>
			<?php if(in_array($childClass, $sparkieStarClass))
			{?>
					<div class="rewardsContainer <?php if($sparkieStarUnlocked!=1) echo 'locked'; ?>">
					<div id="monthlySparkieStar<?php if($sparkieStarUnlocked!=1) echo 'Locked'; ?>" onClick="showMsg(id)">
					<div class="hideClass monthlySparkieStar<?php if($sparkieStarUnlocked!=1) echo 'Locked'; ?>">
						<div class="arrow-top"></div>
						<div class="badgeTitle">
							Highest sparkies in class for a month.
						</div>
					</div>
					</div>
					<div class="sparkieTextContainer">
						<div class="circleContainer"><canvas id="SparkieStarPie" width="44" height="44" style="margin-left:3px;margin-top:3px;" /></div>
						<div class="sparkieText">Sparkie Star</div>
						<div class="explanationText">
						<?php if($sparkieStarUnlocked!=1) 
								echo "You haven't got a Sparkie Star badge yet."; 
							else { 
								echo "You earned ".$sparkieStarValue." Sparkie Star";if($sparkieStarValue>1) echo "s";
								echo ".<br/>Collect it from your teacher."; 
						}?>
						</div>
					</div>
				</div>
				<div class="redSeperator"></div>
			<?php } ?>
			<div class="rewardsContainer <?php if($accuracyChampUnlocked!=1) echo 'locked'; ?>">
				<div id="monthlyAccuracy<?php if($accuracyChampUnlocked!=1) echo 'Locked'; ?>" onClick="showMsg(id)">
				<div class="hideClass monthlyAccuracy<?php if($accuracyChampUnlocked!=1) echo 'Locked'; ?>">
					<div class="arrow-top"></div>
					<div class="badgeTitle">
						Be the "Marksman" of the class by doing a minimum of 100 questions with the best accuracy in class.
					</div>
				</div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><canvas id="accuracyPie" width="44" height="44" style="margin-left:3px;margin-top:3px;" /></div>
					<div class="sparkieText">Marksman</div>
					<div class="explanationText"><?php if($accuracyChampUnlocked!=1) echo "You haven't got a Marksman badge yet"; else echo "You have got ".$accuracyChampValue." Marksman badge";if($accuracyChampValue>1) echo "s";echo "."; ?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($consistencyChampUnlocked!=1) echo 'locked'; ?>">
				<div id="monthlySpeed<?php if($consistencyChampUnlocked!=1) echo 'Locked'; ?>" onClick="showMsg(id)">
				<div class="hideClass monthlySpeed<?php if($consistencyChampUnlocked!=1) echo 'Locked'; ?>">
					<div class="arrow-top"></div>
					<div class="badgeTitle">
						Get the "Steadfast" badge by using Mindspark more than anyone else in your class in a month.
					</div>
				</div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><canvas id="consistencyPie" width="44" height="44" style="margin-left:3px;margin-top:3px;" /></div>
					<div class="sparkieText">Steadfast</div>
					<div class="explanationText"><?php if($consistencyChampUnlocked!=1) echo "You haven't got a Steadfast badge yet"; else echo "You have got ".$consistencyChampValue." Steadfast badge";if($consistencyChampValue>1) echo "s";echo "."; ?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($homeChampUnlocked!=1) echo 'locked'; ?>">
				<div id="monthlyHome<?php if($homeChampUnlocked!=1) echo 'Locked'; ?>" onClick="showMsg(id)">
				<div class="hideClass monthlyHome<?php if($homeChampUnlocked!=1) echo 'Locked'; ?>">
					<div class="arrow-top"></div>
					<div class="badgeTitle1">
						Get the "Overclock" badge by spending more time on Mindspark, than any of your classmates at home, in a month.
					</div>
				</div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><canvas id="homePie" width="44" height="44" style="margin-left:3px;margin-top:3px;" /></div>
					<div class="sparkieText">Overclock</div>
					<div class="explanationText"><?php if($homeChampUnlocked!=1) echo "You haven't got a Overclock badge yet"; else echo "You have got ".$homeChampValue." Overclock badge";if($homeChampValue>1) echo "s";echo "."; ?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($bonusChampUnlocked!=1) echo 'locked'; ?>">
				<div id="bonusChamp<?php if($bonusChampUnlocked!=1) echo 'Locked'; ?>" onClick="showMsg(id)">
				<div class="hideClass bonusChamp<?php if($bonusChampUnlocked!=1) echo 'Locked'; ?>">
					<div class="arrow-top"></div>
					<div class="badgeTitle1">
						Be the "Bonus Champ" of the class by doing better than anyone else on challenge questions, timed tests and higher level questions.
					</div>
				</div>
				</div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><canvas id="bonusPie" width="44" height="44" style="margin-left:3px;margin-top:3px;" /></div>
					<div class="sparkieText">Bonus Champ</div>
					<div class="explanationText"><?php if($bonusChampUnlocked!=1) echo "You haven't got a Bonus Champ badge yet"; else echo "You have got ".$bonusChampValue." Bonus Champ badge";if($bonusChampValue>1) echo "s";echo "."; ?></div>
				</div>
			</div>
			<div style="clear:both"></div>
			<div class="notAttemptedText">Milestones</div><br/><br/><br/>
			<!--<div class="rewardsContainer">
				<div id="megaSparkiesImage"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"></div>
					<div class="sparkieText">Go 100!</div>
					<div class="explanationText">Great going! You got to 100 Sparkies!</div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div id="megaSparkiesImage"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"></div>
					<div class="sparkieText">Go 250!</div>
					<div class="explanationText">WooHoo! Congrats on the 250 SuperSparkies!</div>
				</div>
			</div>
			<div class="redSeperator"></div>-->
			<div class="rewardsContainer <?php if($milestone1!=1) echo 'locked'; ?>">
				<div id="Level250<?php if($milestone1!=1) echo 'Locked'; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie"><?php if($milestone1==1) echo "1"; else echo "0"; ?></div></div>
					<div class="sparkieText">Level 250</div>
					<div class="explanationText"><?php if($milestone1!=1) echo "For this badge, earn 250 Sparkies.";else echo "Congrats! You reached the 1st milestone mark."; ?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($milestone2!=1) echo 'locked'; ?>">
				<div id="Level750<?php if($milestone2!=1) echo 'Locked'; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie"><?php if($milestone2==1) echo "1"; else echo "0"; ?></div></div>
					<div class="sparkieText">Level 750</div>
					<div class="explanationText"><?php if($milestone2!=1) echo "For this badge, earn 750 Sparkies.";else echo "Congrats! You reached the 2nd milestone mark."; ?></div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer <?php if($milestone3!=1) echo 'locked'; ?>">
				<div id="Level1500<?php if($milestone3!=1) echo 'Locked'; ?>"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie"><?php if($milestone3==1) echo "1"; else echo "0"; ?></div></div>
					<div class="sparkieText">Level 1500</div>
					<div class="explanationText"><?php if($milestone3!=1) echo "For this badge, earn 1500 Sparkies.";else echo "Congrats! You reached the 3rd milestone mark."; ?></div>
				</div>
			</div>
			<!--<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div id="year1"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie">1</div></div>
					<div class="sparkieText">Year ONE!</div>
					<div class="explanationText">You completed 1 full year of Mindspark!</div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer">
				<div id="year2"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie">2</div></div>
					<div class="sparkieText">Year TWO!</div>
					<div class="explanationText">You completed 2 full year of Mindspark!</div>
				</div>
			</div>
			<div class="redSeperator"></div>
			<div class="rewardsContainer locked">
				<div id="year3Locked"></div>
				<div class="sparkieTextContainer">
					<div class="circleContainer"><div class="countSparkie">0</div></div>
					<div class="sparkieText">Year THREE!</div>
					<div class="explanationText">You completed 3 full year of Mindspark!</div>
				</div>
			</div>-->
		</div>
	</div>	
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