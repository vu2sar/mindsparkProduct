<?php
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	include("header.php");

	@include_once("../userInterface/check1.php");
	include("../userInterface/constants.php");
	include("../userInterface/classes/clsRewardSystem.php");
	
	
	$userID = $_SESSION['userID'];
	$objUser = new User($userID);
	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
	$objUser   = new User($userID);
	$category	=	$objUser->category;
	if(strcasecmp($category,"Teacher")==0 || strcasecmp($category,"School Admin")==0) {
		$query = "SELECT schoolname FROM educatio_educat.schools WHERE schoolno=".$schoolCode;
		$r = mysql_query($query);
		$l = mysql_fetch_array($r);
		$schoolName = $l[0];
	}
	$currentYear =date('Y');
	//$currentMonth=date('m');
	$currentMonth=13;
	$childClass	=	$_GET['class'];
	$childSection	=	$_GET['section'];
	for($i=1;$i<=12;$i++){
		if($i<10){
			$j= "0".$i;
		}else{
			$j=$i;
		}
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='bonusChamp' and MONTH(batchDate)=$j AND b.schoolCode=".$schoolCode." and b.childClass=".$childClass." and b.childSection='".$childSection."'";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$bonusChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$bonusChampArray[$i]=$cName;
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='accuracyMonthly' and MONTH(batchDate)=$j and b.schoolCode=".$schoolCode." and b.childClass=".$childClass." and b.childSection='".$childSection."'";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$accuracyChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$accuracyChampArray[$i]=$cName;
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='consistentUsageMonthly' AND MONTH(batchDate)=$j and b.schoolCode=".$schoolCode." and b.childClass=".$childClass." and b.childSection='".$childSection."'";
		$result = mysql_query($query);
		$line	=	mysql_fetch_array($result);
		$consistencyChampArrayId[$i]=$line[0];
		$query1= "select childName from adepts_userDetails where userID=".$line[0];
		$result1 = mysql_query($query1);
		$line1	=	mysql_fetch_array($result1);
		$cName = explode(" ", $line1[0]);
		$cName = $cName[0];
		$consistencyChampArray[$i]=$cName;
		$query = "select a.userID from adepts_userBadges a join adepts_userDetails b on a.userID=b.userID where a.batchType='homeUsageChamp' and MONTH(batchDate)=$j and b.schoolCode=".$schoolCode." and b.childClass=".$childClass." and b.childSection='".$childSection."'";
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
?>


<title>Class Leader Board</title>
	<script src="../userInterface/libs/jquery.js"></script>
<link href="css/myClasses.css" rel="stylesheet" type="text/css">
	<link href="../userInterface/css/classLeaderBoard/midClass.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<style>
#home{
	position: relative;
	background:none;
}
.arrow-right {
	margin-left:0px;
	margin-top:0px;
}
</style>
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
	<script type="text/javascript" src="../userInterface/libs/i18next.js"></script>
	<script type="text/javascript" src="../userInterface/libs/translation.js"></script>
	<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
    <script>
	var langType	=	'<?=$language?>';
	var click=0;
	function getHome()
	{
		setTryingToUnload();
		window.location.href	=	"../userInterface/home.php";
	}
	function logoff()
	{
		setTryingToUnload();
		window.location="../userInterface/logout.php";
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
	<?php include("eiColors.php") ?>
	<div id="fixedSideBar">
		<?php include("fixedSideBar.php") ?>
	</div>
	<div id="topBar">
		<?php include("topBar.php") ?>
	</div>
	<div id="sideBar">
		<?php include("sideBar.php") ?>
	</div>
	
	<div id="container">
		
		<table border="0" width="100%" style="margin-top:10px">
			<tr style="color: #B3B3B3;font-family: 'Conv_HelveticaLTStd-BoldCond';font-size: 1.3em;">
				
				<td width="18.9%" align="center" colspan="2" style="color:#666666;">Class <?=$childClass?><?=$childSection?></td>
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
				<td width="8.5%" align="center" <?php if($currentMonth>12) echo "class='colorCalender'"; ?>>DEC</td>
			</tr>
			<tr>
				<td width="6.3%"><div class="badgeText">ACCURACY CHAMPION</div></td>
				<td width="12.6%"><img src="../userInterface/assets/rewards/monthlyBadges/monthlyAccuracy.png" width="120px"/></td>
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
				<td width="12.6%"><img src="../userInterface/assets/rewards/monthlyBadges/monthlyConsistency.png"/></td>
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
				<td width="12.6%"><img src="../userInterface/assets/rewards/monthlyBadges/monthlyBonusSpecial.png"/></td>
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
				<td width="12.6%"><img src="../userInterface/assets/rewards/monthlyBadges/monthlyHome.png"/></td>
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
	<?php include("footer.php") ?>
    