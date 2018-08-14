<?php
$userID = $_SESSION['userID'];
$rewardsUser = new Sparkies($userID);
$recentBadge =  $rewardsUser->recentBadge();
$sparkieLevel = $rewardsUser->getSparkieLevel();
//$_SESSION['sparkieImage'] = 'level1';
if($sparkieLevel==1){
	$_SESSION['sparkieImage'] = 'level1';
}else if($sparkieLevel==2){
	$_SESSION['sparkieImage'] = 'level2';
}else{
	$_SESSION['sparkieImage'] = 'level3';
}


// Prompt for parent email address
$is_using_at_home = isHomeUsage($schoolCode, $childClass);
$parentStrategy = 0;

if(strcasecmp($objUser->subcategory,"School")==0 && $is_using_at_home && $childClass > 2)
	$parentStrategy	=	checkForMindsparkParentStrategy($userID,$schoolCode);

$sparkieImage = $_SESSION['sparkieImage'];
$array = $rewardsUser->getPendingNotification();
$badge_bonusChamp = $badge_consistentUsageMonthly = $badge_highestAccuracy = $badge_homeChamp  = 0;
$badge_mileStone1 = $badge_mileStone2 =$badge_mileStone3 = $badge_boy = $badge_light = $badge_consistentUsageWeekly = $badge_topicCompletion = 0;
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$badgesArray = array();

foreach($array as $arrays){    
    foreach($arrays as $key=>$value){
        array_push($badgesArray,$key);
    }
}

// Badges Prompts

for($i=0;$i<count($array);$i++) {
$badge = $badgesArray[$i];
	if($array[$i][$badge]["notification"]==0 && $array[$i][$badge]["notification"]!=null){
		${'badge_'.$badge} = 1;
		$rewardsUser->updateUserBadge($badge);
	}
}

// Get sparkie level
	$sparkieLevel = $rewardsUser->getSparkieLevel();


//studentPoster prompt
	$studentPosterFlag=0;
	$studentPoster = studentHasPoster($userID,$schoolCode,$childClass);
	if ($studentPoster!="" && !isset($_SESSION['studentPoster'])) {$studentPosterFlag=1;$_SESSION['studentPoster']=1;}

	
?>	

<script>
var langType = '<?=$language;?>';
var donePrompt = new Array(15);
var parentStrategy	=	<?=$parentStrategy?>;
var kudosPrompt = 0;
var promptsCompleted=0;

<?php
	if($priorityAssigned && firstSession($_SESSION["userID"]))
	{
		echo "var priorityPrompt = 1;";
		$sq = "INSERT INTO adepts_userBadges SET batchType='priorityNotification', batchDate=CURDATE(), userID=$userID, sparkieEarned=0, sparkieConsumed=0, badgeDescription='For priority prompt', notification=1";
		mysql_query($sq);

	}
	else
		echo "var priorityPrompt = 0;";
	if(!isset($_SESSION["bcqPrompt"]))	
		$_SESSION["bcqPrompt"] = 1;	
	if(firstSessionAfterBCQ($_SESSION["userID"],$_SESSION["childClass"]) && $_SESSION["bcqPrompt"]==1)
	{
		echo "var bcqPrompt = 1;";
		$_SESSION["bcqPrompt"] = 2;
	}
	else
		echo "var bcqPrompt = 0;";
?>


for (var i = 0; i < 16; i++) donePrompt[i] = 0; 
function myPrompts()
{
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		$(".translation").i18n();
	
		<?php if($sparkieLevel==3) { ?>
			$('#sparkie').css('background-image', 'url("assets/rewards/L.01_info.png")');
			$('#sparkie').css('height','65px');
			$('#sparkie').css('width','60px'); 
		<?php } ?>
		<?php if($sparkieLevel==2) {?>
			$('#sparkie').css('background-image', 'url("assets/rewards/Level1_L0.png")');
			$('#sparkie').css('height','73px');
			$('#sparkie').css('width','69px'); 
			$('#sparkie').css('left','-3px'); 
			$('#sparkie').css('top','43%'); 
		<?php } ?>
		<?php if($sparkieLevel==1) {?>
			$('#sparkie').css('background-image', 'url("assets/rewards/LevelZeroSparkies.png")');
			$('#sparkie').css('height','56px');
			$('#sparkie').css('width','44px'); 
		<?php } ?>
		
		$('#image').removeAttr('style');
		$('#image .hiddenForPoster').show();
		$('#image #closeButton').hide();
		$('#image #studentPoster').remove();
		if((<?php echo $studentPosterFlag ?>)==1 && donePrompt[16]!=1) {

			donePrompt[16] = 1 ;
			$('#image').css({'background-image': 'none',"top":'100px','text-align':'center'});
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$('#image *:visible').addClass('hiddenForPoster').hide();
			//$("#desc1").css({"left":"70px","top":"24%","font-size":"1.7em","width":"90%"});
			//$("#desc1").html();
			$("#image").append('<img src="assets/posters/<?php echo $studentPoster ?>" id="studentPoster" style="box-shadow: darkgrey 0px 0px 7px 7px;">');
			$("#image").append('<div id="closeButton" onclick="sample();" style="display:none;margin: 20px; margin-left: auto; margin-right: auto;text-align: center;bottom: 10px;line-height: 30px;width: 80px;padding: 10px;height: 30px;font-size: 23px;background: white;cursor:pointer;"> OK </div>');
			$('#image #closeButton').show();
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
		}
		else if((<?php echo $badge_bonusChamp ?>)==1 && donePrompt[0]!=1) {

			donePrompt[0] = 1 ;
			$('#badge').css('background-image', 'url("assets/rewards/BonusSpecial_120.png")');
			$("#prompt").css("display","block");
			$("#desc1").html(i18n.t("homePage.bonus1"));
			$("#desc2").html(i18n.t("homePage.bonus2"));
			$("#desc1").css("left","251px");
			$("#desc2").css("left","237px");
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_consistentUsageMonthly ?>)==1 && donePrompt[1]!=1) {
			/*alert("2");*/
			donePrompt[1] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/Monthly_Speed_120.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block");
			$("#desc1").html(i18n.t("homePage.consistency1"));
			$("#desc2").html(i18n.t("homePage.consistency2")); 
			$("#desc1").css("left","266px");
			$("#desc2").css("left","216px");
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_highestAccuracy ?>)==1 && donePrompt[2]!=1) {
			/*alert("3");*/
			donePrompt[2] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/Monthly_Accuracy_120_Unlocked.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").html(i18n.t("homePage.accuracy1"));
			$("#desc2").html(i18n.t("homePage.accuracy2"));
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_homeChamp?>)==1 && donePrompt[3]!=1) {
			/*alert("4");*/
			donePrompt[3] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/Monthly_Home_120.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","225px");
			$("#desc2").css("left","187px");
			$("#desc1").html(i18n.t("homePage.home1"));
			$("#desc2").html(i18n.t("homePage.home2"));
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_mileStone1 ?>)==1 && donePrompt[4]!=1) {
			/*alert("5");*/
			donePrompt[4] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/LevelOneSparkie.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","271px");
			$("#desc2").css("left","277px");
			$("#badge").css("left","338px");
			$("#desc1").html(i18n.t("homePage.milestone1_1"));
			$("#desc2").html(i18n.t("homePage.milestone1_2"));
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_mileStone2 ?>)==1 && donePrompt[5]!=1) {
			/*alert("6");*/
			donePrompt[5] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/LevelTwoSparkie.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","271px");
			$("#desc2").css("left","277px");
			$("#badge").css("left","338px");
			$("#desc1").html(i18n.t("homePage.milestone2_1"));
			$("#desc2").html(i18n.t("homePage.milestone2_2"));
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_boy ?>)==1 && donePrompt[6]!=1) {
			/*alert("7");*/
			donePrompt[6] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/sparkies_notification.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","271px");
			$("#desc2").css("left","287px");
			$("#badge").css("left","338px");
			$("#desc1").html(i18n.t("homePage.theme1_1"));
			$("#desc2").html(i18n.t("homePage.theme1_2"));
			$("#bottom_info1").html("Well Done!");
			$("#bottom_info2").html("Activate Theme?");
			$("#bottom_info2").css("width","119px");
			$("#bottom_info1").css("left","581px");
			$("#bottom_info1").css("font-size","20px");
			$("#bottom_info2").css("left","582px");
			$("#bottom_info2").css("width","98px");
			$("#bottom_info2").css("font-size","12px");
			
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/themesDashboard.php";
			
		}
		else if((<?php echo $badge_light ?>)==1 && donePrompt[7]!=1) {
			/*alert("8");*/
			donePrompt[7] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/sparkies_notification.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","271px");
			$("#desc2").css("left","287px");
			$("#badge").css("left","338px");
			$("#desc1").html(i18n.t("homePage.theme2_1"));
			$("#desc2").html(i18n.t("homePage.theme2_2"));
			$("#bottom_info1").html("Well Done!");
			$("#bottom_info2").html("Activate Theme?");
			$("#bottom_info2").css("width","119px");
			$("#bottom_info1").css("left","581px");
			$("#bottom_info1").css("font-size","20px");
			$("#bottom_info2").css("left","582px");
			$("#bottom_info2").css("width","98px");
			$("#bottom_info2").css("font-size","12px");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/themesDashboard.php";
		}
		else if((<?php echo $badge_consistentUsageWeekly ?>)==1 && donePrompt[8]!=1) {
			/*alert("9");*/
			donePrompt[8] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/sparkies_notification.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","226px");
			$("#desc2").css("left","317px");
			$("#badge").css("left","338px");
			$("#desc1").html(i18n.t("homePage.mindsparkUsage1"));
			$("#desc2").html(i18n.t("homePage.mindsparkUsage2"));
			$("#desc1").css("left","237px");
			$("#bottom_info1").html("Well Done!");
			$("#bottom_info2").css('background-color','#ffffff')
			$("#bottom_info2").css("width","119px");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
		}
		else if((<?php echo $badge_mileStone3 ?>)==1 && donePrompt[10]!=1) {
			/*alert("11");*/
			donePrompt[10] = 1;
			$('#badge').css('background-image', 'url("assets/rewards/badgesRewardSection/Unlocked/Level1500.png")');
			$('#badge').css('width','112px');
			$("#prompt").css("display","block"); 
			$("#desc1").css("left","271px");
			$("#desc2").css("left","272px");
			$("#badge").css("left","338px");
			$("#desc1").html(i18n.t("homePage.milestone3_1"));
			$("#desc2").html(i18n.t("homePage.milestone3_2"));
			$("#bottom_info1").html("Goodluck!");
			$("#bottom_info2").html("Learn more");
			var a = document.getElementById('rewardsLink');
			a.href = "src/rewards/rewardsDashboard.php";
			
		}
		else if(parentStrategy==1 && donePrompt[11]==0)
		{
			donePrompt[11] = 1;
			$("#congrats").html("Sparkies!");
			
			$("#congrats").css({"font-weight":"bolder","font-size":"30px"});
			$("#congrats_name").remove();
			$('#badge').remove();
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$("#desc1").css({"left":"70px","top":"54%","font-size":"1.6em","width":"90%"});
			$("#desc2").css({"left":"70px","top":"78%","width":"90%"});
			$("#desc1").html(i18n.t("homePage.parentEmailNotification1"));
			$("#desc2").html("<input type='text' name='emailids' placeholder='Parent`s email id' id='emailids'>&nbsp;&nbsp;&nbsp;<input type='button' name='saveEmailids' id='saveEmailids' value='Share' onclick='shareParentEmailIds()'>&nbsp;&nbsp;&nbsp;<input type='button' name='sendReminder' id='sendReminder' value='Remind Me Later' onclick='remindParentEmailIds()'>");
			$("#desc2").append("<div style='clear:both; float: right; color: #2F99CB; font-weight: bold; font-size: 14px; margin-top: 12px; margin-right: 18px; cursor: pointer; text-decoration:underline;'><a onclick='openNotes();'>Why are we asking for parents' email IDs?</a></div>");
			$("#desc2").find("input:text").css({"border":"1px solid #000","font-size":"16px","width":"63%","height":"28px"});
			$("#saveEmailids").css({"height":"33px","margin":"0px","color":"#fff","padding":"5px","text-align":"center","width":"10%","background-color":"#F26722","border":"1px solid #000","cursor":"pointer"});
			$("#sendReminder").css({"height":"33px","margin":"0px","color":"#fff","padding":"5px","text-align":"center","width":"20%","background-color":"#F26722","border":"1px solid #000","cursor":"pointer"});
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
			$("#emailids").focus();
		}
		else if(parentStrategy == 1 && donePrompt[11] == 2) {
			donePrompt[11] = 1;
			$("#congrats").html("Sparkies!");
			$("#congrats").css({"font-weight":"bolder","font-size":"30px"});
			$("#congrats_name").remove();
			$('#badge').remove();
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$("#desc1").css({"left":"70px","top":"64%","font-size":"1.7em","width":"90%"});
			$("#desc1").html(i18n.t("homePage.parentEmailNotification2"));
			$("#desc2").html("");
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
		}
		else if(kudosPrompt == 1 && donePrompt[13] != 1) {
			donePrompt[13] = 1;
			$("#congrats").html("Kudos!");
			$("#congrats").css({"font-weight":"bolder","font-size":"30px","left":"15%"});
			$("#congrats_name").remove();
			$('#badge').remove();
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$("#desc1").css({"left":"70px","top":"64%","font-size":"1.7em","width":"90%"});
			$("#desc1").html("Thank you for all the kudos you have sent. However, due to misuse of the feature by some students, you will no longer be able to send a kudos to your teachers. They will still be able to send them to you to appreciate your good work.");
			$("#desc2").html("");
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
		}
		else if(priorityPrompt == 1 && donePrompt[12] != 1) {
			donePrompt[12] = 1;
			$("#congrats").html("Priorities!");
			$("#congrats").css({"font-weight":"bolder","font-size":"30px","left":"10%"});
			$("#congrats_name").remove();
			$('#badge').remove();
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$("#desc1").css({"left":"70px","top":"64%","font-size":"1.7em","width":"90%"});
			$("#desc1").html(i18n.t("homePage.priorityText"));
			$("#desc2").html("");
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
		}
		else if(bcqPrompt == 1 && donePrompt[14] != 1) {
			donePrompt[14] = 1;
			$("#congrats").html("Challenge<br>Questions!");
			$("#congrats").css({"font-weight":"bolder","font-size":"28px","left":"10%"});
			$("#congrats_name").remove();
			$('#badge').remove();
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$("#desc1").css({"left":"70px","top":"51%","font-size":"1.4em","width":"90%"});
			$("#desc1").html("Many of you asked for it and we are now giving it!<br>You get more challenge questions (CQs) to solve now if you complete a learning unit with high accuracy. You will get upto 3 challenge questions back to back in such a case.<br>Take the challenge and enhance your learning by thought provoking CQs. Keep learning Maths! Keep enjoying Mindspark!<br><br>Team Mindspark");
			$("#desc2").html("");
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
		}
		
		<?php /*?>else if((<?php echo $_SESSION['festivalTheme'] ?>) == 1 && donePrompt[12]!=1 && (<?php echo $childClass ?>) > 3) {
			donePrompt[12] = 1;
			$("#congrats").html("Happy <br/>Children's day!");
			$("#congrats").css({"font-weight":"bolder","font-size":"20px","left":"12%","color": "#30314B"});
			$("#congrats_name").remove();
			$('#badge').remove();
			$("#prompt").css({"display":"block","top":"-10%"}); 
			$("#desc1").css({"left":"152px","top":"62%","font-size":"1.7em","width":"90%","color": "#30314B"});
			$("#desc1").html("<i>Children's day is here. We have a surprise theme for you.<br/> Happy Mindsparking!!</i>");
			$("#desc2").html("");
			$("#bottom_info1").remove();
			$("#bottom_info2").remove();
		}<?php */?>
		else {
			$("#prompt").css("display","none");
			promptsCompleted=1; 
		}
	});
}

function sample()
{
	$("#prompt").css("display","none");
	window.setTimeout(function() {
        myPrompts();
    }, 400);
}

function openNotes() {
	var left = (screen.width/2)-250;
	var top = (screen.height/2)-150;
	window.open("https://www.mindspark.in/mindspark/userInterface/parentEmailNotification.html", "ParentEmailNotification", "height=300,width=500,top="+top+",left="+left);
}

</script>

<!--Prompt 1-->
	
<?php $Name = $Name=strtoupper($Name); ?>
<div id=prompt>
	<div id="image">
		<div id="congrats">
			<b>Congratulations</b>
		</div>
		<div id="congrats_name">
		<b><?=$Name?>!</b>
		</div>
		<div id="desc1">
		</div>
		<div id="desc2">
		</div>
		<div id="sparkie">
		</div>
		<div id="badge" style="width:137px;height:120px;position: relative;top:64;left: 328px;">
		</div>
		<div id="close_rewards" onclick="sample();">
		</div>
		 <div id="bottom_info1">
		</div>
		<a id="rewardsLink" href="">
		<div id="bottom_info2">
		</div> </a>
	</div>
</div>

<?php
function studentHasPoster($userID,$schoolCode,$childClass)
{
	$studentPoster="";
	if (isDailyDrillSchool($childClass)){
		if (getLoginDaysSince('20160130')<4 && isUserFirstTimeLoggedInToday($userID)) 
			$studentPoster='dailyDrillPoster.jpg';
	}
	return $studentPoster;
}
function checkForMindsparkParentStrategy($userID,$schoolCode)
{
	// Don't show prompt in offline mode
	if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==1 || $_SESSION['offlineStatus']==2)) {
		return 0;	// don't show prompt
	}
	
	// Don't show prompt if school request to not show
	$checkValidSchool = "SELECT schoolCode FROM parentEmailSchools WHERE schoolCode = ".mysql_real_escape_string($schoolCode);
	$execValidSchool = mysql_query($checkValidSchool);
	if(mysql_num_rows($execValidSchool) > 0) {
		return 0;	// don't show prompt
	}
	
	// If user's email id is already present then don't show him the prompt
	$check_valid_user = "SELECT parentEmail FROM adepts_userDetails WHERE userID = ".mysql_real_escape_string($userID);
	$exec_valid_user = mysql_query($check_valid_user);
	$row_valid_user = mysql_fetch_array($exec_valid_user);
	if(!empty($row_valid_user['parentEmail'])) {
		return 0;	// don't show prompt
	}
	
	$check_notification = "SELECT userID, email, added_on, verification_code, verified, verified_on, prompt_on, prompt_count, reminder_count 
		FROM adepts_parentEmailNotification WHERE userID = ".mysql_real_escape_string($userID);
	$exec_notification = mysql_query($check_notification);
	if(mysql_num_rows($exec_notification) == 0) {
		$insert_entry = "INSERT INTO adepts_parentEmailNotification (userID, email, added_on, verification_code, verified, verified_on, prompt_on, 
			prompt_count, reminder_count) VALUES (".mysql_real_escape_string($userID).", NULL, '0000-00-00 00:00:00', NULL, 0, 
			'0000-00-00 00:00:00', NOW(), 1, 0)";
		$exec_entry = mysql_query($insert_entry);
		
		return 1;	// prompt student to add email address
	}
	
	$row_notification = mysql_fetch_array($exec_notification);
	$total_prompt_count = $row_notification['prompt_count'];
	$total_reminder_count = $row_notification['reminder_count'];
	$after_date_condition1 = strtotime('2014-05-20 00:00:00');
	$after_date_condition2 = strtotime($row_notification['prompt_on']);
	
	if(empty($row_notification['email']) && $after_date_condition2 > $after_date_condition1) {
		// if not verified and not added email address, check when to prompt him
		$last_prompt_date = date("Ymd", strtotime($row_notification['prompt_on']));
		$current_date = date("Ymd");
		
		if($total_reminder_count == 0 && $last_prompt_date != $current_date) {
			$update_entry = "UPDATE adepts_parentEmailNotification SET prompt_on = NOW(), prompt_count = prompt_count + 1, reminder_count = 0 WHERE 
				userID = ".mysql_real_escape_string($userID);
			$exec_update_entry = mysql_query($update_entry);
			
			return 1;	// show prompt again
		} else if($total_reminder_count == 1 && ($current_date - $last_prompt_date) > 3) {
			$update_entry = "UPDATE adepts_parentEmailNotification SET prompt_on = NOW(), prompt_count = prompt_count + 1, reminder_count = 0 WHERE 
				userID = ".mysql_real_escape_string($userID);
			$exec_update_entry = mysql_query($update_entry);
			
			return 1;	// show prompt again
		} else {
			return 0;	// don't show prompt
		}
	} else {
		return 0;	// don't show prompt
	}
}

function firstSession($userID)
{
	$sq = "SELECT userID FROM adepts_userBadges WHERE batchType='priorityNotification' AND userID=$userID";
	$rs = mysql_query($sq);
	$num = mysql_num_rows($rs);
	if($num>0)
		return false;
	else
		return true;
}

function firstSessionAfterBCQ($userID,$childClass)
{
	$flag = false;
	if($childClass>=4 && $childClass<=7)
	{
		$sq = "SELECT COUNT(sessionID) FROM adepts_sessionStatus where userID=$userID AND startTime_int>=20150831";
		$rs = mysql_query($sq);
		$rw = mysql_fetch_array($rs);
		if($rw[0]==1)
			$flag = true;
	}
	return $flag;
}
?>