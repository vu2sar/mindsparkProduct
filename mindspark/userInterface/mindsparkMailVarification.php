<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

if(!empty($_GET['verify'])) {
	include("dbconf.php");

	$verification_code = trim($_GET["verify"]);

	$check_valid_entry = "SELECT userID, email, verified, prompt_on FROM adepts_parentEmailNotification WHERE 
		verification_code = '".mysql_real_escape_string($verification_code)."'";
	$exec_valid_entry = mysql_query($check_valid_entry);
	if(mysql_num_rows($exec_valid_entry) > 0) {
		$row_valid_entry = mysql_fetch_array($exec_valid_entry);
		
		$userID = trim($row_valid_entry['userID']);
		$email = trim($row_valid_entry['email']);
		$verified = trim($row_valid_entry['verified']);
		$prompt_on = strtotime($row_valid_entry['prompt_on']);
		$date_before_reward_sparkies = strtotime("2014-05-20 00:00:00");
		
		// verify email and assign sparkies
		$user_details = "SELECT parentEmail, secondaryParentEmail FROM adepts_userDetails WHERE 
			userID = ".mysql_real_escape_string($userID);
		$exec_user_details = mysql_query($user_details);
		if(mysql_num_rows($exec_user_details) > 0) {
			$row_user_details = mysql_fetch_array($exec_user_details);
			
			$primary_mail = trim($row_user_details['parentEmail']);
			$secondary_mail = trim($row_user_details['secondaryParentEmail']);
			$secondary_mail_arr = explode(",", $secondary_mail);
			$proceed = true;
			
			if(empty($primary_mail)) {
				// if primary email doesn't exist
				$update_user = "UPDATE educatio_educat.common_user_details SET additionalEmail = '".mysql_real_escape_string($email)."' WHERE 
					MS_userID = ".mysql_real_escape_string($userID);
				$exec_update_user = mysql_query($update_user);
				if($exec_update_user) $proceed = false;
				
			} else {
				$not_valid = checkIfNotValid($primary_mail);
				
				if($primary_mail != $email && $not_valid) {
					// update primary email if previous email is bounced
					$update_user = "UPDATE educatio_educat.common_user_details SET additionalEmail = '".mysql_real_escape_string($email)."' 
						WHERE MS_userID = ".mysql_real_escape_string($userID);
					$exec_update_user = mysql_query($update_user);
					if($exec_update_user) $proceed = false;
					
				} else if(!in_array($email,$secondary_mail_arr) && $primary_mail != $email) {
					// add email in secondary email list
					$text_to_update = implode(",", $secondary_mail_arr);
					$text_to_update = ($text_to_update == "") ? $email : $text_to_update.",".$email;
					
					$update_user = "UPDATE adepts_userDetails SET secondaryParentEmail = '".mysql_real_escape_string($text_to_update)."' WHERE 
						userID = ".mysql_real_escape_string($userID);
					$exec_update_user = mysql_query($update_user);
					if($exec_update_user) $proceed = false;
				}
			}
			
			if($proceed) {
				if($prompt_on < $date_before_reward_sparkies) {
					// check if already sparkies are alloted
					$sparkie_check = "SELECT userID FROM adepts_userBadges WHERE userID = ".mysql_real_escape_string($userID)." AND batchType = 'emailVarification'";
					$exec_sparkie_check = mysql_query($sparkie_check);
					if(mysql_num_rows($exec_sparkie_check) == 0) {
						$add_sparkies = "INSERT INTO adepts_userBadges (userID, batchType, batchDate, sparkieEarned, sparkieConsumed, badgeDescription, 
							notification) VALUES (".$userID.", 'emailVarification', NOW(), 30, 0, 
							'".mysql_real_escape_string($verification_code)."', 1)";
						$exec_add_sparkies = mysql_query($add_sparkies);
						
						if($exec_add_sparkies) {
							// give sparkies to user
							$get_current_sparkies = "SELECT sparkies FROM adepts_rewardPoints WHERE userID=".mysql_real_escape_string($userID);
							$exec_current_sparkies = mysql_query($get_current_sparkies);
							if(mysql_num_rows($exec_current_sparkies) > 0) {
								$update_sparkies = "UPDATE adepts_rewardPoints SET sparkies = sparkies + 30 WHERE userID = ".mysql_real_escape_string($userID);
							} else {
								$update_sparkies = "INSERT INTO adepts_rewardPoints SET sparkies = 30, userID = ".mysql_real_escape_string($userID).", 
									startDate = CURDATE()";
							}
							$exec_update_sparkies = mysql_query($update_sparkies);
							
							$message = '<h1 class="success">Congratulations!</h1>';
							$message .= '<br />Thank you for confirming your email address. Your child will be rewarded with 30 sparkies.<br /><br />';
							$message .= '<b>We take this opportunity to draw your attention to the Mindspark Parent Connect - a personalized website to track your child\'s progress.</b><br /><br />';
							$message .= 'Login today!<br /><br />';
							$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
							$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
						}
						
					} else {
						$message = '<h1 class="warning">Warning!</h1>';
						$message .= '<br />Your email address is already verified and your child has been already rewarded with 30 sparkies.<br /><br />';
						$message .= '<b>We take this opportunity to draw your attention to the Mindspark Parent Connect - a personalized website to track your child\'s progress.</b><br /><br />';
						$message .= 'Login today!<br /><br />';
						$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
						$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
					}
				} else {
					if($verified == 0) {
						$message = '<h1 class="success">Congratulations!</h1>';
						$message .= '<br />Thank you for confirming your email address.<br /><br />';
						$message .= '<b>We take this opportunity to draw your attention to the Mindspark Parent Connect - a personalized website to track your child\'s progress.</b><br /><br />';
						$message .= 'Login today!<br /><br />';
						$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
						$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
					} else {
						$message = '<h1 class="warning">Warning!</h1>';
						$message .= '<br />Your email address is already verified.<br /><br />';
						$message .= '<b>We take this opportunity to draw your attention to the Mindspark Parent Connect - a personalized website to track your child\'s progress.</b><br /><br />';
						$message .= 'Login today!<br /><br />';
						$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
						$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
					}
				}
				
				if($verified == 0) {
					$update_notification = "UPDATE adepts_parentEmailNotification SET verified = 1, verified_on = NOW() WHERE userID = ".mysql_real_escape_string($userID);
					$exec_update_notification = mysql_query($update_notification);
				}
			} else {
				$message = '<h1 class="error">Oopss! Couldn\'t process your request!</h1>';
				$message .= '<br />Your request seems to be invalid. You will be redirected to Mindspark for login.<br /><br />';
				$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
				$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
				if(empty($_GET['re'])) {
					header("Location: mindsparkMailVarification.php?verify=".$_GET["verify"]."&re=1");
					exit;
				}
			}
		}
	} else {
		$message = '<h1 class="error">Invalid request!</h1>';
		$message .= '<br />Your request seems to be invalid. You will be redirected to Mindspark for login.<br /><br />';
		$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
		$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
	}
} else {
	$message = '<h1 class="error">Oopss! Something went wrong!</h1>';
	$message .= '<br />Your request seems to be invalid. You will be redirected to Mindspark for login.<br /><br />';
	$message .= 'You will be redirected to Mindspark Parent Connect login page.<br /><br />';
	$message .= 'If you are not redirected automatically, click <a href="https://www.mindspark.in/mindspark/login/">here<a/><br />​';
}

function checkIfNotValid($emailID)
{
	$sq	=	"SELECT count(emailID) FROM educatio_educat.email_bounce_details WHERE emailID='$emailID'";
	$rs	=	mysql_query($sq);
	$rw	=	mysql_fetch_array($rs);
	if($rw[0]>20)
		return(true);
	else
		return(false);
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Mindspark - Email Verification</title>
<meta http-equiv="refresh" content="10;url=https://www.mindspark.in/mindspark/login/">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style>
body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000;
	line-height: 18px;
	padding: 5px;
	border: 15px solid;
	border-top-color: #2f99cb;
	border-right-color: #fbd212;
	border-bottom-color: #e75903;
	border-left-color: #9ec956;
	width: 600px;
	margin: 0px auto;
	margin-top: 5%;
}
.success {
	color: #9ec956;
	text-align: center;
}
.error {
	color: #ff0000;
	text-align: center;
}
.warning {
	color: #e75903;
	text-align: center;
}
</style>
</head>
<body>
<?php echo $message; ?>
<br /><br />
</body>
</html>