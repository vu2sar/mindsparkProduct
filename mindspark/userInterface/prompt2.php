<?php
include("classes/clsRewardSystem.php");
$userID = $_SESSION['userID'];
$rewardsUser = new Sparkies($userID);
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
// Get sparkie level
$sparkieLevel = $rewardsUser->getSparkieLevel();
$_SESSION['questionType'] = "normal";
?>


<html>
<head>
<script>
var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1;
var isIpad = ua.indexOf("ipad") > -1;
function load()
{
		if(!isAndroid && !isIpad)
		$('body').css('overflow-y','hidden');

		<?php if($sparkieLevel==3) { ?>
		$('#sparkie').css('background-image', 'url("assets/rewards/L.01_info.png")');
		$('#sparkie').css('height','65px');
		$('#sparkie').css('width','60px'); 
		$('#sparkie').css('top','106px');
		$('#sparkie').css('left','28px');
		$('#sparkie_no').html('Mega Sparkies');
		
		
		
		<?php } ?>
		<?php if($sparkieLevel==2) {?>
		$('#sparkie').css('background-image', 'url("assets/rewards/Level1_L0.png")');
		$('#sparkie').css('height','72px');
		$('#sparkie').css('width','69px'); 
		$('#sparkie').css('top','106px');
		$('#sparkie').css('left','15px');
		$('#sparkie_no').html('Super Sparkies');
		
		$('#sparkie_center').css('background-image', 'url("assets/rewards/Level1_L0.png")');
		$('#sparkie_center').css('height','72px');
		$('#sparkie_center').css('width','69px'); 
		$('#sparkie_center').css('top','34px');
		<?php } ?>
		<?php if($sparkieLevel==1) {?>
		$('#sparkie').css('background-image', 'url("assets/rewards/LevelZeroSparkies.png")');
		$('#sparkie').css('height','56px');
		$('#sparkie').css('width','44px'); 
		$('#sparkie').css('top','106px');
		$('#sparkie').css('left','36px');
		$('#sparkie_no').html('Sparkies');
		
		$('#sparkie_center').css('background-image', 'url("assets/rewards/LevelZeroSparkies.png")');
		$('#sparkie_center').css('height','65px');
		$('#sparkie_center').css('width','60px');
		$('#sparkie_center').css('top','62px');
		$('#sparkie_center').css('margin-bottom','19px');
		<?php } ?>
}

function sample()
{
	$("#prompt").css("display","none");	
	if(!isAndroid && !isIpad)
	$('body').css('overflow-y','scroll');
}

</script>
</head>

<body onLoad="load();">
<?php if(($_SESSION['sparkie']['normal']!=null || $_SESSION['sparkie']['CQ']!=null || $_SESSION['sparkie']['wildcard']!=null || $_SESSION['sparkie']['sparkieExplaination']!=null ||$_SESSION['sparkie']['practise']!=null || $_SESSION['sparkie']['topicCompletion']!=null) && $_SESSION['sessionReportFlag']==1) { 
 $Name=strtoupper($Name);
 $_SESSION['sessionReportFlag']=0; ?>
<div id=prompt>
	<div id="image">
	
		<div id="congrats">
		<b>Great going,</b>
		</div>
		<div id="congrats_name">
		<b><?php
		
		$length=strlen($Name);
		if($length>12)
		{
			$displayName=substr($Name, 0, 12);
			echo $displayName;
		}
		else
		echo $Name;
		?>!</b>
		</div>
		
		<div id="sparkie">
		</div>
		
		<div id="desc">
		<b> Here are your sparkies for this session! </b>
		</div>
		
		<?php 
		if ($mode== -7 ){ ?>
			<div style="position:absolute;margin-top:125px;margin-left:30px;font-size:1.2em;color:red;">
			<b>You have completed your <?=$_SESSION['timePerDay']?> minute session!</b>
			</div>
		<?php } ?>
		
		<div id="close_rewards" onClick="sample();">
		</div>
		
		<div id="sparkie_center" style="visibility:hidden;">
		</div>
		
		<div id="sparkie_no">
		
		</div>
		
		<?php if($_SESSION['sparkie']['normal']!=null) {?>
		<div class="info">
		<b>Questions</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['normal']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		<?php if($_SESSION['sparkie']['CQ']!=null) {?>
		<div class="info">
		<b>Challenge Questions</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['CQ']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		<?php if($_SESSION['sparkie']['wildcard']!=null) {?>
		<div class="info">
		<b>Wild Card Questions</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['wildcard']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		<?php if($_SESSION['sparkie']['practise']!=null) {?>
		<div class="info">
		<b>Practice</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['practise']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		
		<?php if($_SESSION['sparkie']['sparkieExplaination']!=null) {?>
		<div class="info">
		<b>Reading Explanations</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['sparkieExplaination']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		
		<?php if($_SESSION['sparkie']['topicCompletion']!=null) {?>
		<div class="info">
		<b>Topic Completion</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['topicCompletion']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		<?php if($_SESSION['sparkie']['timedTest']!=null) {?>
		<div class="info">
		<b>Timed Test</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['timedTest']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		<?php if($_SESSION['sparkie']['game']!=null) {?>
		<div class="info">
		<b>Activity</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['game']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		
		<?php if($_SESSION['sparkie']['topicRevision']!=null) {?>
		<div class="info">
		<b>Topic Revision</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['topicRevision']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
		<?php if($_SESSION['sparkie']['worksheetCompletion']!=null) {?>
		<div class="info">
		<b>Worksheet Completion</b>
		</div>
		
		<div class="value" style="">
		<b><?=$_SESSION['sparkie']['worksheetCompletion']?> </b>
		</div>
		
		<div class="line">
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
</body>

</html>