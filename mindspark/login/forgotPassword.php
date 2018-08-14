<?php
require_once("../userInterface/dbconf.php");
@include_once("../userInterface/mail/insert_mail.php");
include("loginFunctions.php");

session_start();
if(isset($_SESSION['userID']))
{
	header("Location: ../userInterface/logout.php");
	exit;
}

$error_message = '';
$success_message = '';
$question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favorite colour?');

if(!isset($_SESSION['wrongAttemptLeft']))
	$_SESSION['wrongAttemptLeft'] = 3;

$txtUsername = "";
$txtDOB = "";
$lstQuestion = "";
$txtAnswer = "";
if(!empty($_POST['btnSubmit'])) {
	$txtUsername = (empty($_POST['txtUsername'])) ? "" : trim($_POST['txtUsername']);
	$txtDOB = (empty($_POST['txtDOB'])) ? "" : trim($_POST['txtDOB']);
	$lstQuestion = (empty($_POST['lstQuestion'])) ? "" : trim($_POST['lstQuestion']);
	$txtAnswer = (empty($_POST['txtAnswer'])) ? "" : trim($_POST['txtAnswer']);
	// Get the user
	//$get_user = "SELECT userID, childEmail, childDob, childName, username, secretQues, secretAns, schoolCode, category, subcategory, childClass, childSection  
	//			   FROM adepts_userDetails WHERE username = '".mysql_real_escape_string($txtUsername)."' AND enabled = 1 AND endDate >= CURDATE()";
	$get_user = "SELECT MS_userID, MSE_userID, childEmail, dob, Name, username, secretQues, secretAns, schoolCode, category, subcategory, class, section, MS_enabled, MSE_enabled, endDate, MSE_endDate 
				 FROM educatio_educat.common_user_details WHERE username = '".mysql_real_escape_string($txtUsername)."' AND ((MS_enabled = 1 AND endDate >= CURDATE()) OR (MSE_enabled = 1 AND MSE_endDate >= CURDATE()))";
	$exec_user = mysql_query($get_user);
	if(mysql_num_rows($exec_user) > 0) {
		$row_user = mysql_fetch_array($exec_user);
		$schoolCode = $row_user['schoolCode'];
		$user_email = $row_user['childEmail'];
		$password = $row_user['username'];
		$childClass = $row_user['class'];
		$childSection = $row_user['section'];
		$childDOB = $row_user['dob'];
		$user_id = $row_user['MS_userID'];
		$mse_user_id = $row_user['MSE_userID'];
		
		/*$pilot_schools = array(2387554, 2285293);
		if(!in_array($schoolCode, $pilot_schools))
		{
			$_SESSION['wrongAttemptLeft'] = 3;
		}*/
		
		if($txtDOB == date("d-m-Y", strtotime($childDOB))) {
			if($_SESSION['wrongAttemptLeft'] <= 0) {
				// Send request to the teacher if 3 attempts are failed.
				//$get_teachers = "SELECT userID, childEmail FROM adepts_userDetails WHERE schoolCode = ".$schoolCode." AND 
				//				(category = 'TEACHER' OR category = 'School Admin') AND enabled = 1 AND endDate >= CURDATE()";
				$get_teachers = "SELECT MS_userID, MSE_userID, MS_enabled, MSE_enabled, childEmail FROM educatio_educat.common_user_details WHERE schoolCode = ".$schoolCode." AND 
								(category = 'TEACHER' OR category = 'School Admin')  AND ((MS_enabled = 1 AND endDate >= CURDATE()) OR (MSE_enabled = 1 AND MSE_endDate >= CURDATE()))";
				
				$exec_teachers = mysql_query($get_teachers);
				$no_of_users = mysql_num_rows($exec_teachers);
				$teacherUserID = 0;
				$childUserID = $user_id;
				$english_childUserID = $mse_user_id;
				$teacherEmail = "";
				if($no_of_users == 1) {
					$row_single_teacher = mysql_fetch_array($exec_teachers);
					if($row_single_teacher['MS_userID'] != 0 && $row_single_teacher['MS_enabled'] == 1)
					{
						$teacherUserID = $row_single_teacher['MS_userID'];
						$teacherEmail = $row_single_teacher['childEmail'];
					}
					if($row_single_teacher['MSE_userID'] != 0  && $row_single_teacher['MSE_enabled'] == 1)
					{
						$english_teacherUserID = $row_single_teacher['MSE_userID'];
						$english_teacherEmail = $row_single_teacher['childEmail'];
					}
				} else {
					$temp_teachers = array();
					$english_temp_teachers = array();
					while($row_teachers = mysql_fetch_array($exec_teachers)) {
						if($row_teachers['MS_userID'] != 0  && $row_teachers['MS_enabled'] == 1)
						{
							$userstring .= $row_teachers['MS_userID'].",";
							$temp_teachers[$row_teachers['MS_userID']] = $row_teachers['childEmail'];
						}
						if($row_teachers['MSE_userID'] != 0 && $row_teachers['MSE_enabled'] == 1)
						{
							$english_userstring .= $row_teachers['MSE_userID'].",";
							$english_temp_teachers[$row_teachers['MSE_userID']] = $row_teachers['childEmail'];
						}
					}
					$userstring = rtrim($userstring, ",");
					$english_userstring = rtrim($english_userstring, ",");
					
					if($row_user['MS_userID'] != 0 && $row_user['MS_enabled'] == 1)
					{
						$class_teacher = "SELECT userID FROM adepts_teacherClassMapping WHERE userID IN (".$userstring.") AND class = ".$childClass." 
							AND section = '".$childSection."'";
						$exec_class_teacher = mysql_query($class_teacher);
						if(mysql_num_rows($exec_class_teacher) > 0) {
							$row_class_teacher = mysql_fetch_array($exec_class_teacher);
							$teacherUserID = $row_class_teacher['userID'];
							$teacherEmail = $temp_teachers[$teacherUserID];
						}
					}
					if($row_user['MSE_userID'] != 0 && $row_user['MSE_enabled'] == 1)
					{
						$english_class_teacher = "SELECT userID FROM educatio_msenglish.teacherClassMapping WHERE userID IN (".$english_userstring.") AND childClass = ".$childClass." 
							AND childSection = '".$childSection."' AND isPrimaryClass = '1'";
						
						$english_exec_class_teacher = mysql_query($english_class_teacher);
						if(mysql_num_rows($english_exec_class_teacher) > 0) {
							$row_class_teacher = mysql_fetch_array($english_exec_class_teacher);
							$english_teacherUserID = $row_class_teacher['userID'];
							$english_teacherEmail = $english_temp_teachers[$english_teacherUserID];
						}
					}

				}
				
				if($row_user['MS_userID'] != 0)
				{
					$check_entry = "select * from adepts_forgetPassNotification where
										childUserID = $childUserID and teacherUserID = $teacherUserID and status = 0";
					
					$exiting_entry = mysql_query($check_entry);
					$exiting_maths_entry = mysql_num_rows($exiting_entry);
					if($exiting_maths_entry > 0) {
						$success_message = 'Your password reset request has already been sent to your teacher. It may take a day or two for your teacher to reset your password, please be patient!';
						$_SESSION['wrongAttemptLeft'] = 3;

					}
					else
					{
						$add_notification = "INSERT INTO adepts_forgetPassNotification (childUserID, teacherUserID, category, status, requestDate) VALUES 
											(".$childUserID.", ".$teacherUserID.", 1, 0, NOW())";
						$exec_notification = mysql_query($add_notification);
						$success_message = 'Your password reset request has been sent to your teacher. It may take a day or two for your teacher to reset your password, please be patient!';
					}


				}
				if($row_user['MSE_userID'] != 0)
				{
					$check_entry = "select * from educatio_msenglish.forgetPassNotification where
										childUserID = $english_childUserID and teacherUserID = $english_teacherUserID and status = 0";
					
					$exiting_entry = mysql_query($check_entry);
					$exiting_english_entry = mysql_num_rows($exiting_entry);
					if($exiting_english_entry > 0) {
						$success_message = 'Your password reset request has already been sent to your teacher. It may take a day or two for your teacher to reset your password, please be patient!';
						$_SESSION['wrongAttemptLeft'] = 3;

					}
					else
					{
						$add_notification = "INSERT INTO educatio_msenglish.forgetPassNotification (childUserID, teacherUserID, category, status, requestDate) VALUES 
											(".$english_childUserID.", ".$english_teacherUserID.", 1, 0, NOW())";
						$exec_notification = mysql_query($add_notification);
						$success_message = 'Your password reset request has been sent to your teacher. It may take a day or two for your teacher to reset your password, please be patient!';
					}
				}
				
				if(!empty($teacherEmail) && $exiting_maths_entry > 0) {
					$req_date = date("Y-m-d H:i:s");
					mailToTeacherResetPassword($teacherEmail, $row_user['Name'], $row_user['username'], $req_date);
				}
				if(!empty($english_teacherEmail) && $exiting_english_entry > 0) {
					$req_date = date("Y-m-d H:i:s");
					mailToTeacherResetPassword($english_teacherEmail, $row_user['Name'], $row_user['username'], $req_date);
				}
				

				
				$_SESSION['wrongAttemptLeft'] = 3;
				
			} else {
				if($lstQuestion == $row_user['secretQues'] && $txtAnswer == $row_user['secretAns']) {
					if($childClass < 3 && strcasecmp($row_user['category'],"STUDENT")==0)
						$password = '';
					
					// Update password for MS and MSE Users.
					$update_password = "UPDATE educatio_educat.common_user_details SET password = PASSWORD('".$password."') WHERE MS_userID = ".$user_id." and MSE_userID = ".$mse_user_id;
					$exec_password = mysql_query($update_password);
			
					if(!empty($user_email)) {
						mailPassword($user_email, $password, $row_user['username'], $row_user['Name']);
					}
					
					$success_message = 'Your password has been reset successfully.';
					$_SESSION['wrongAttemptLeft'] = 3;		
					
				} else {
					$error_message = 'Secret Question or Secret Answer is incorrect.';
					$_SESSION['wrongAttemptLeft']--;
				}
			}
		} else {
			$error_message = 'DOB does not match.';
			$_SESSION['wrongAttemptLeft'] = 3;
		}
	} else {
		$error_message = 'Invalid user credentials.';
		$_SESSION['wrongAttemptLeft'] = 3;
	}
}


if($_SESSION['wrongAttemptLeft'] <= 0) {
	$error_message = 'Sorry! It seems you are unable to remember your secret question or secret answer.<br /><br />';
	$error_message .= 'You can click on "Request Password" button to request your teacher to reset your password.<br />';
	$error_message .= 'OR you can ask your parent to write to us at <a href="maito:mindspark@ei-india.com">mindspark@ei-india.com</a>';
}

$iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
?>
<!DOCTYPE HTML>
<html>
<title>Forgot Password</title>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<!--For Live-->
<link href="../userInterface/css/login/login.css?ver=2" rel="stylesheet" type="text/css">
<link href="../userInterface/css/prompt.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../userInterface/libs/jquery.js"></script>
<script type="text/javascript" src="../userInterface/libs/prompt.js"></script>
<link rel="stylesheet" type="text/css" href="../userInterface/css/CalendarControl.css" />
<script type="text/javascript" src="../userInterface/libs/CalendarControl.js" language="javascript"></script>
<script type="text/javascript" src="../userInterface/libs/calendarDateInput.js" language="javascript"></script>
<script type="text/javascript" src="../userInterface/libs/dateValidator.js"></script>
<script type="text/javascript" src="../userInterface/libs/combined.js"></script>
<style>
.frmLbl { font-family: 'Conv_HelveticaLTStd-Cond'; font-size: 14px; }
.loginButton { background-color: #2F99CB !important; color: #000 !important; }
</style>
<?php if ($iPad == true || $Android == true) { ?>
<script>
function doOnOrientationChange() {
	if (!jQuery('.promptContainer').is(":visible")) {
		var prompts = new Prompt({
			text: 'Mindspark is best viewed and worked with in the landscape (horizontal) mode.<br><br>Please shift to landscape mode to proceed further. ',
			type: 'block',
			func1: function() {
				jQuery("#prmptContainer").remove();
			}
		});
		jQuery("#promptText").css('font-size', '160%');
	}
	//jQuery('.promptContainer').css('display','none');
	var windowheight = jQuery(window).height();
	var windowwidth = jQuery(window).width();
	var pagecenterW = windowwidth / 2;
	var pagecenterH = windowheight / 2;
	jQuery("#promptBox").css({'margin-top': pagecenterH - 130 + 'px', 'margin-left': pagecenterW - 175 + 'px'});
}
</script>
<?php } ?>
<script>
function validateForm() {
	var txtUsername = $.trim($("#txtUsername").val());
	var txtDOB = $.trim($("#txtDOB").val());
	var lstQuestion = $.trim($("#lstQuestion").val());
	var txtAnswer = $.trim($("#txtAnswer").val());
	
	if(txtUsername == "") {
		alert("Please enter your username.");
		$("#txtUsername").focus();
		return false;
	}
	if(txtDOB == "") {
		alert("Please select your date of birth.");
		$("#txtDOB").focus();
		return false;
	}
	<?php if($_SESSION['wrongAttemptLeft'] > 0) { ?>
	if(lstQuestion == "") {
		alert("Please select secret question.");
		$("#lstQuestion").focus();
		return false;
	}
	if(txtAnswer == "") {
		alert("Please enter your answer.");
		$("#txtAnswer").focus();
		return false;
	}
	<?php } ?>
	
	return true;
}
$(document).ready(function(){
	setTryingToUnload();
});
</script>
</head>
<body class="translation">
<div id="header">
	<div id="eiColors">
		<div id="orange"></div>
		<div id="yellow"></div>
		<div id="blue"></div>
		<div id="green"></div>
	</div>
</div>
<div id="head"><a href="../"><div id="logo" ></div></a><a href="../../faq.php" target="_blank"><div id="help"></div></a></div>
<div id="tab" style="float:none;">
	<form id="frmFP" name="frmFP" method="POST" onSubmit="return validateForm();">
	<table width="450" cellpadding="7">
		<tr>
			<td colspan="2" class="frmLbl">
			<span style="font-size: 24px; line-height: 40px;">Reset Password</span><br />
			<span style="color: red; line-height: 16px;"><?php if(!empty($error_message)) echo '<br />'.$error_message; ?></span>
			</td>
		</tr>
		<tr>
			<td width="150" class="frmLbl">Username</td>
			<td width="300"><input class="Box" tabindex="1" type="text" name="txtUsername" id="txtUsername" value="<?php echo $txtUsername; ?>" /></td>
		</tr>
		<tr>
			<td class="frmLbl">DOB (DD-MM-YYYY)</td>
			<td><input class="Box" tabindex="2" type="text" name="txtDOB" id="txtDOB" maxlength="10" onFocus="showCalendarControl(this);" size="10" onKeyUp="showCalendarControl(this);"  onBlur="validateDate(this);" value="<?php echo $txtDOB; ?>" /></td>
		</tr>
		<?php if($_SESSION['wrongAttemptLeft'] > 0) { ?>
		<tr>
			<td class="frmLbl">Secret Question</td>
			<td>
				<select tabindex="3" name="lstQuestion" id="lstQuestion" class="Box1">
					<option value="">Select</option>
					<?php
					foreach ($question_array as $secQus) {
						echo "<option value='$secQus'>$secQus</option>";
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="frmLbl">Secret Answer</td>
			<td><input class="Box" tabindex="4" type="text" name="txtAnswer" id="txtAnswer" maxlength="30" /></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="2" class="frmLbl">
			<input type="submit" class="loginButton" id="btnSubmit" name="btnSubmit" value="Request Password" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="frmLbl">
			Remember your password? Login <a href="./">here</a>
			</td>
		</tr>
	</table>
	</form>
</div>

<?php

if(!empty($success_message)) {
?>
<script>
$(document).ready(function(){
	alert('<?php echo $success_message; ?>');
	window.location.href='./index.php';
});
</script>
<?php
}
include("../userInterface/footer.php");
?>