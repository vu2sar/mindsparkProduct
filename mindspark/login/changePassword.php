<?php
require_once("../userInterface/dbconf.php");
include("../userInterface/check1.php");
include("../userInterface/constants.php");
include("../userInterface/classes/clsUser.php");
include("loginFunctions.php");
/*require_once("../userInterface/functions/functions.php");*/
session_start();
if(!isset($_SESSION['userID']))
{
	header("Location: ../userInterface/logout.php");
	exit;
}
$userID = $_SESSION['userID'];
$objUser = new User($userID);
$username		= 	$objUser->username;
$childName		=	$objUser->childName;
$childClass		=	$objUser->childClass;
$childSection	=	$objUser->childSection;
$lstQuestion =   $objUser->secretQues;
$txtAnswer 	 =   $objUser->secretAns;
$lstQuestion1 =   $objUser->secretQues;
$txtAnswer1 	 =   $objUser->secretAns;
$categoryFlag = 0;
if(strcasecmp($objUser->category,"Teacher") == 0 || strcasecmp($objUser->category,"School Admin") == 0)
	$categoryFlag = 1;
$theme = $_SESSION['theme'];
if(empty($_SESSION['CPStep'])) $_SESSION['CPStep'] = 1;

$error_message = "";
$fromTI = 0;
$firstLogin = 0;
if(!empty($_REQUEST)) {
	// for step 1
	if(!empty($_REQUEST['proceed'])) {
		if($_REQUEST['proceed'] == 1) {
			$_SESSION['CPStep'] = 2;
		} else if($_REQUEST['proceed'] == 2) {
			login();
			exit;
		}
	if($_REQUEST['fromTI'])
		$fromTI = $_REQUEST['fromTI'];
	}
	if($_REQUEST['firstLogin'])
	{
		$firstLogin = 1;
	}
	// for step 2
	if(!empty($_POST['btnSubmit'])) {
		$oldPassword = trim($_POST['oldPassword']);
		$txtPassword = trim($_POST['txtPassword']);
		$txtPassword1 = trim($_POST['txtPassword1']);
		$lstQuestion = trim($_POST['lstQuestion']);
		$txtAnswer = trim($_POST['txtAnswer']);
		$birthdate = trim($birthDate = substr($_POST['birthdate'],6,4)."-".substr($_POST['birthdate'],3,2)."-".substr($_POST['birthdate'],0,2));
		
		if(!validateOldPassword($_SESSION['userID'],$oldPassword)) $error_message .= "Current password does not match.<br/>";
		if($txtPassword == "") $error_message .= "Please enter new password.<br />";
		if(strlen($txtPassword) < 5) $error_message .= "New password should be at least 5 characters long.<br />";
		if($txtPassword == $username) $error_message .= "New password can not be same as username.<br />";
		if($txtPassword1 == "") $error_message .= "Please enter confirm new password.<br />";
		if($txtPassword != $txtPassword1) $error_message .= "New password and Confirm New Password do not match.<br />";
		if($categoryFlag)
		{
			if($fromTI)
			{
				if($lstQuestion == "") $error_message .= "Please select secret question.<br />";
				if($txtAnswer == "") $error_message .= "Please enter secret answer.<br />";				
			}
		}
		else
		{
			if($lstQuestion == "") $error_message .= "Please select secret question.<br />";
			if($txtAnswer == "") $error_message .= "Please enter secret answer.<br />";
		}
		
		if(empty($error_message)) {
			$update_data = "UPDATE educatio_educat.common_user_details SET password = PASSWORD('".$txtPassword."')"; 
			if($fromTI || $firstLogin)
				$update_data .= ", secretQues = '".mysql_real_escape_string($lstQuestion)."', secretAns = '".mysql_real_escape_string($txtAnswer)."', dob = '".$birthdate."'"; 
			$update_data .= " WHERE MS_userID = ".$_SESSION['userID'];
			$exec_data = mysql_query($update_data);
			if($exec_data) {
				$_SESSION['CPStep'] = 3;
				header("Location: changePassword.php");
				exit;
			} else {
				$error_message = "Something went wrong. Please try again later.";
			}
		}
	}
	
	// for step 3
	if(!empty($_POST['proceedLogin'])) {
		login();
		exit;
	}
}

$question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favorite colour?');

$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$language = isset($_REQUEST["language"])?$_REQUEST["language"]:"en";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;">
<meta http-equiv="X-UA-Compatible" content="IE=9">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<script type="text/javascript" src="../userInterface/libs/prompt.js"></script>
<script type="text/javascript" src="../userInterface/libs/jquery.js"></script>
<script>
var langType = '<?=$language;?>';
$(document).ready(function(e) {
	i18n.init({ lng: langType,useCookie: false }, function(t) {});
});
</script>
<script type="text/javascript" src="../userInterface/libs/i18next.js"></script>
<script type="text/javascript" src="../userInterface/libs/translation.js"></script>
<link rel="stylesheet" type="text/css" href="css/CalendarControl.css" >
<script type="text/javascript" src="libs/CalendarControl.js" language="javascript"></script>
<script type="text/javascript" src="libs/calendarDateInput.js" language="javascript"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>
<title>Change Password</title>
<?php if($categoryFlag) {?>
<link href="../teacherInterface/css/common.css" rel="stylesheet" type="text/css">
<?php }else{ if($theme==1) { ?>
<link href="../userInterface/css/commonLowerClass.css" rel="stylesheet" type="text/css">
<link href="../userInterface/css/generic/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
<link rel="stylesheet" href="../userInterface/css/commonMidClass.css" />
<link rel="stylesheet" href="../userInterface/css/generic/midClass.css" />
<?php } else { ?>
<link rel="stylesheet" href="../userInterface/css/commonHigherClass.css" />
<link rel="stylesheet" href="../userInterface/css/generic/higherClass.css" />
<?php } }?>
<style>
.frmLbl { font-family: 'Conv_HelveticaLTStd-Cond'; font-size: 17px; }
.promptText1 { font-size: 22px; line-height: 30px; color: #000; }
.promptText3 { font-size: 18px; line-height: 26px; color: #000; }
.frmButtons1 { padding: 5px; width: 70px; color: #000; border: 2px solid #000; background-color: #fff;font-weight: bold; cursor: pointer; }
.frmButtons3 { padding: 5px; color: #000; border: 2px solid #000; background-color: #fff;font-weight: bold; cursor: pointer; }
</style>
<script language="javascript">
function load(){
	 init();
	<?php if($theme==1) { ?>
	var a= window.innerHeight - (180);
	$('#pnlContainer').css("height",a+"px");
	<?php } else if($theme==2) { ?>
	var a= window.innerHeight - (170);
	$('#pnlContainer').css("height",a+"px");
	<?php } else if($theme==3) { ?>
	var a= window.innerHeight - (170);
	var b= window.innerHeight - (610);
	$('#pnlContainer').css({"height":a+"px"});
	$('#sideBar').css({"height":a+"px"});
	$('#main_bar').css({"height":a+"px"});
	$('#menubar').css({"height":a+"px"});
	<?php } ?>

	<?php if($categoryFlag)
		echo "hideUnwantedElements();"
	?>

}

function hideUnwantedElements()
{
	$("#studentInfoUpper").css("display","none");
	$(".arrow-right").css("display","none");
	var length = $("#nameDiv li").length;
	$("#nameDiv li").hide();
	$($("#nameDiv li")[0]).show();
	$($("#nameDiv li")[length-1]).show();
	$($($("#nameDiv li")[length-1]).children()[0]).attr("href","../teacherInterface/logout.php");
	$(".logo").parent().attr("href","../teacherInterface/home.php")
	$("#container").css({"width":"100%","min-height":"470px"});
	$("#kudos-top-div").css("display","none");
	$("#changeUnamePwdTbl").css("margin-top","60px");
	$("#oldInterfaceUrl").css("display","none");
	$("#noticeicons").hide();
}

function logoff()
{
	<?php if($categoryFlag){
		echo "window.location='../teacherInterface/logout.php';";
	}
	else
	{
		echo "window.location='../userInterface/logout.php';";
	} ?>
}
function init()
{
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins
}

function doAction(val) {
	$("#proceed").attr("value", val);
	document.frmStep1.submit();
}

function doAction2()
{
	<?php
		if ($objUser->category == "School Admin" && $objUser->subcategory == "All") {
			if($fromTI)
			{
				$nextPage = "../teacherInterface/home.php";
			}
			else
			{
		        $newInterfaceFlag = $objUser->isSetNewInterface();
		        if ($newInterfaceFlag == 2)
		            $nextPage = "../teacherInterface/redirect.php?admin=1";
		        else if ($newInterfaceFlag)
		            $nextPage = "../teacherInterface/getSchoolDetails.php";
		        else
		            $nextPage = "../getSchoolDetails.php";			
			}
	    }
	    elseif (strcasecmp($objUser->category, "Teacher") == 0 || $objUser->category == "School Admin" || $objUser->category == "Home Center Admin") {
	        $nextPage = "../ti_home.php";
	        $newInterfaceFlag = $objUser->isSetNewInterface();
	        if ($newInterfaceFlag == 2)
	            $nextPage = "../teacherInterface/redirect.php";
	        else if ($newInterfaceFlag == 1 || $newInterfaceFlag == 3)
	            $nextPage = "../teacherInterface/home.php";
	    }
	?>
	window.location='<?=$nextPage;?>';
}

function validateForm() {
	var txtUsername = '<?php echo $username; ?>';
	var oldPassword = $.trim($("#oldPassword").val());
	var txtPassword = $.trim($("#txtPassword").val());
	var txtPassword1 = $.trim($("#txtPassword1").val());
	var lstQuestion = $.trim($("#lstQuestion").val());
	var txtAnswer = $.trim($("#txtAnswer").val());
	var birthdate = $.trim($("#birthdate").val());
	
	<?php if($categoryFlag){
			if($fromTI || $firstLogin){
	?>
	if(birthdate == "")
	{
		alert("Please enter your date of birth.");
		$("#birthdate").focus();
		return false;
	}
	<?php }}
	?>

	if(oldPassword == "") {
		alert("Please enter current password.");
		$("#oldPassword").focus();
		return false;
	}

	if(txtPassword == "") {
		alert("Please enter new password.");
		$("#txtPassword").focus();
		return false;
	}
	if(txtPassword.length < 5) {
		alert("New password should be at least 5 characters long.");
		$("#txtPassword").attr("value", "");
		$("#txtPassword1").attr("value", "");
		$("#txtPassword").focus();
		return false;
	}
	if(txtPassword == txtUsername) {
		alert("New password can not be same as username.");
		$("#txtPassword").focus();
		return false;
	}
	if(txtPassword1 == "") {
		alert("Please enter confirm new password.");
		$("#txtPassword1").focus();
		return false;
	}
	if(txtPassword != txtPassword1) {
		alert("New password and Confirm New Password do not match.");
		$("#txtPassword1").focus();
		return false;
	}
	<?php if($categoryFlag){
			if($fromTI || $firstLogin){
	?>
	if(lstQuestion == "") {
		alert("Please select secret question.");
		$("#lstQuestion").focus();
		return false;
		}
	if(txtAnswer == "") {
		alert("Please enter secret answer.");
		$("#txtAnswer").focus();
		return false;
		}
	<?php }}else{ ?>
	if(lstQuestion == "") {
		alert("Please select secret question.");
		$("#lstQuestion").focus();
		return false;
		}
	if(txtAnswer == "") {
		alert("Please enter secret answer.");
		$("#txtAnswer").focus();
		return false;
		}
	<?php } ?>
	
	return true;
}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
	<!-- <div id="topBar"> -->
	<?php include("../teacherInterface/eiColors.php")?>
	<?php if($categoryFlag)
		  {	
				include("../teacherInterface/topBar.php");
		  }	
		  else
		  {
	 ?>
	<div id="top_bar">
		<div class="logo">
		</div>
        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$childName?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$childName?>&nbsp;&#9660;</span></a>
							<ul>
								<li><a href="javascript:logoff();"><span data-i18n="common.logout"></span></a></li>
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
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
    </div>
    <?php
    	  }
    ?>

	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div id="childClassDiv"><span data-i18n="common.class"></span>: <?=$childClass.$childSection?></div>
                	<div id="childNameDiv"><?=$childName?></div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>
		</div>
		<div id="info_bar" class="forHighestOnly">
			<div id="dashboard" class="forHighestOnly" >
				<div id="dashboardIcon"></div>
			</div>
			<div class="arrow-right"></div>
		</div>
		<div id="pnlContainer">
			<?php
			if($_SESSION['CPStep'] == 1) {
			?>
			<br /><br />
			<form id="frmStep1" name="frmStep1" method="POST">
			<table id="changeUnamePwdTbl" width="700" align="center" style="border: 3px solid #000;" cellpadding="10">
				<tr>
					<td colspan="2" width="700">
						<br />
						<span class="promptText1">
						<?php if(strcasecmp($objUser->category,"Student") == 0 ) {?>
						<?php if($childClass < 6) { ?>
						<table>
							<tr>
								<td valign="middle"><img src="../userInterface/assets/sparkie.png" width="35" style="margin-right: 10px;" /></td>
								<td>We notice that your password and your username are the same.<br />We request you to change your password.</td>
							</tr>
						</table>
						<?php } else { ?>
							Your password and your username are the same. We recommend that you have a new password, which is different from your username!
						<?php } } else { ?>
							<table>
								<tr>
									<td style='font-size:22px;'>Your password and your username are the same. You must have a new password, which is different from your username! This is to avoid misuse of your account by any of your students.</td>
								</tr>
							</table>
						<?php } ?>
						</span>
					</td>
				</tr>
				<tr>
					<td width="400" valign="middle">
					<span class="promptText1">Would you like to change your password now?</span>
					<br /><br />
					</td>
					<td width="300" align="right" valign="middle">
						<input class="frmButtons1" type="button" id="btnChangePassword" value="Yes" onClick="doAction(1);" /> &nbsp;
						<input class="frmButtons1" type="button" id="btnSkip" value="No" onClick="doAction(2);" />
						<br /><br />
					</td>
				</tr>
			</table>
			<input type="hidden" value="0" id="proceed" name="proceed" />
			</form>
			<?php
			} else if($_SESSION['CPStep'] == 2) {
			?>
			<br /><br />
			<form id="frmStep2" name="frmStep2" method="POST" onSubmit="return validateForm();">
			<input type='text' style='display:none' id='fakeUserNameField'/> <!-- For preventing auto fill in new chrome -->
			<input type='password' style='display:none' id='fakePasswordField'/> <!-- For preventing auto fill in new chrome -->
			<table align="center" cellpadding="5">
				<tr>
					<td colspan="2" class="frmLbl">
					<!-- <span style="font-size: 22px; line-height: 40px;">Change Password</span><br /> -->
					<span style="color: #f00; line-height: 16px;">
					All fields are required.
					<?php if(!empty($error_message)) echo '<br /><br />'.$error_message; ?>
					</span>
					<br /><br />
					</td>
				</tr>
				<?php if($fromTI || $firstLogin){ ?>
				<tr>
					<td width="120" class="frmLbl">Username: </td>
					<td width="300" class="frmLbl"><?php echo $username; ?></td>
				</tr>
				<tr>
					<td width="120" class="frmLbl">Date of Birth: </td>
					<td width="300"><input type="text" name="birthdate" id="birthdate" onFocus="showCalendarControl(this,'<?=$childClass?>');" size="10" onKeyUp="showCalendarControl(this);"  onBlur="validateDate(this);" value="<?php if($fromTI){echo $objUser->childDob;} ?>"> <span class="frmLbl">(dd-mm-yyyy)</span></td>
				</tr>
				<?php } ?>
				<tr>
					<td width="120" class="frmLbl">Current Password: </td>
					<td width="300"><input class="Box" tabindex="1" type="password" name="oldPassword" id="oldPassword" maxlength="50" /></td>
				</tr>
				<tr>
					<td width="120" class="frmLbl">New Password: </td>
					<td width="300"><input class="Box" tabindex="1" type="password" name="txtPassword" id="txtPassword" maxlength="50" /></td>
				</tr>
				<tr>
					<td width='185' class="frmLbl">Confirm New Password: </td>
					<td width="300"><input class="Box" tabindex="2" type="password" name="txtPassword1" id="txtPassword1" maxlength="50" /></td>
				</tr>
				<?php if($fromTI || $firstLogin){ ?>
				<tr>
					<td class="frmLbl">Secret Question: </td>
					<td>
						<select tabindex="3" name="lstQuestion" id="lstQuestion" class="Box1">
							<option value="">Select</option>
							<?php
							foreach ($question_array as $secQus) {
								if(empty($error_message))
								{
									if($lstQuestion == $secQus)
										echo "<option value='$secQus' selected>$secQus</option>";
									else
										echo "<option value='$secQus'>$secQus</option>";
								}
								else
								{
									if($lstQuestion1 == $secQus)
										echo "<option value='$lstQuestion1' selected>$lstQuestion1</option>";
									else
										echo "<option value='$secQus'>$secQus</option>";
								}

							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="frmLbl">Secret Answer: </td>
					<td><input class="Box" tabindex="4" type="text" name="txtAnswer" id="txtAnswer" maxlength="30" value="<?php if(empty($error_message)){echo $txtAnswer;}else{echo $txtAnswer1;} ?>" /></td>
				</tr>
				<?php }if($firstLogin){ ?>
				<tr>
					<td colspan="2">
						<input type="submit" value="Submit" id="btnSubmit" name="btnSubmit" style="margin-left:190px;"/>
					</td>
				</tr>
				<?php }else { ?>
				<tr>
					<td>
						<input type="submit" value="Submit" id="btnSubmit" name="btnSubmit" style="margin-left:60px;"/>
					</td>
					<td>
						<input type="button" value="Cancel" style="margin-left:60px;" id"btnCancel" name"btnCancel" onClick="doAction2();"/>
					</td>
				</tr>
				<?php
					}

				?>
			</table>
			</form>
			<?php
			} else {
			?>
			<br /><br />
			<form id="frmStep3" name="frmStep3" method="POST">
			<table width="700" align="center" style="border: 3px solid #000;" cellpadding="10">
				<tr>
					<td>
						<br />
						<span class="promptText3">
						<?php if($childClass < 6 && !$categoryFlag) { ?>
						<table>
							<tr>
								<td valign="middle"><img src="../userInterface/assets/sparkie.png" width="35" style="margin-right: 10px;" /></td>
								<td><b>Your password has been changed successfully!</b></td>
							</tr>
						</table>
						Remember, you <u>have to use the new password the next time you log-in to Mindspark!!</u>
						<?php } else { ?>
						<b>Your password has been changed successfully!</b><br />
						Remember, you <u>have to use the new password the next time you log-in to Mindspark!!</u>
						<?php } ?>
						</span>
					</td>
				</tr>
				<tr>
					<td align="center">
						<input class="frmButtons3" type="submit" value="Go To Home Page" id="proceedLogin" name="proceedLogin" />
						<br /><br />
					</td>
				</tr>
			</table>
			</form>
			<?php
			}
			?>
		</div>
	</div>
	<div id="bottom_bar">
		<div id="copyright" data-i18n="[html]common.copyright"></div>
	</div>
	<?php if( $iPad ==true|| $Android ==true) { ?>
	<script>
		function doOnOrientationChange() {
			if (!jQuery('.promptContainer').is(":visible")) {
				var prompts = new Prompt({
					text: 'Mindspark is best viewed and worked with in the landscape (horizontal) mode.<br><br>Please shift to landscape mode to proceed further. ',
					type: 'block',
					func1: function () {
						jQuery("#prmptContainer").remove();
					}
				});
				jQuery("#promptText").css('font-size', '160%');
			}
			jQuery('#promptContainer').css('display','none');
			var windowheight = jQuery(window).height();
			var windowwidth = jQuery(window).width();
			var pagecenterW = windowwidth / 2;
			var pagecenterH = windowheight / 2;
		}
		doOnOrientationChange();
	</script>
	<style>
	#prmptContainer {
		display: none;
	}

	@media all and (orientation:portrait) {
		#prmptContainer {
			display: block;
		}
	   
	}
	</style>
	<? } ?>
</body>
</html>