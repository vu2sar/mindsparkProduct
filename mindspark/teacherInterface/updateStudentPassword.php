 <?php
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
include("header.php");
include("../userInterface/clsUser.php");

$userID     = $_SESSION['userID'];
$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
$user   = new User($userID);

if(!empty($_POST)) {
	$process_id = $_POST['process_id'];
	$reqType = $_POST['reqType'];
	$action = $_POST['action'];
	
	$get_data = "SELECT * FROM adepts_forgetPassNotification WHERE id = ".$process_id;
	$exec_data = mysql_query($get_data);
	if(mysql_num_rows($exec_data) > 0) {
		$row_data = mysql_fetch_array($exec_data);
		$update_userID = $row_data['childUserID'];

		// Update the notifications in english
		$get_common_details = "SELECT MSE_userID FROM educatio_educat.common_user_details WHERE MS_userID = ".$update_userID;
		$english_userID = mysql_query($get_common_details);
		$common_details = mysql_fetch_array($english_userID);
		
		if($reqType == 1 && $action == 1) {
			$get_user_details = "SELECT childClass, username FROM adepts_userDetails WHERE userID = ".$update_userID;
			$exec_user_details = mysql_query($get_user_details);
			if(mysql_num_rows($exec_user_details) > 0) {
				$row_user_details = mysql_fetch_array($exec_user_details);
				$pass_to_set = $row_user_details['username'];
				if($row_user_details['childClass'] < 3)
					$pass_to_set = '';
				
				$update_password = "UPDATE educatio_educat.common_user_details SET password = PASSWORD('".$pass_to_set."') WHERE MS_userID = ".$update_userID;
				$exec_password = mysql_query($update_password);
				if($exec_password) {
					$update_request = "UPDATE adepts_forgetPassNotification SET status = 1 WHERE id = ".$process_id;
					$exec_request = mysql_query($update_request);
					echo "Password reset successfully!";
				}
			}
		} else if($reqType == 2 && $action == 1) {
			$update_lock = "UPDATE educatio_educat.common_user_details SET password = PASSWORD('') WHERE MS_userID = ".$update_userID;
			$exec_lock = mysql_query($update_lock);
			if($exec_lock) {
				$update_request = "UPDATE adepts_forgetPassNotification SET status = 1 WHERE id = ".$process_id;
				$exec_request = mysql_query($update_request);
				
				$remove = "DELETE FROM accountBlock WHERE userID = ".$update_userID;
				$exec_remove = mysql_query($remove);
				
				echo "Account unlocked successfully!";
			}
		} else if($action == 2) {
			$update_request = "UPDATE adepts_forgetPassNotification SET status = 2 WHERE id = ".$process_id;
			$exec_request = mysql_query($update_request);
			echo "Request ignored successfully!";
		}

		if($action != 2) 
		{
			if($common_details['MSE_userID'] != 0)
			{
				$update_english = "update educatio_msenglish.forgetPassNotification set status = 1 where childUserID = ".$common_details['MSE_userID']." and status = 0";
				$updated_notification = mysql_query($update_english);
			}
		}
		else if($action ==2)
		{
			if($common_details['MSE_userID'] != 0)
			{
				$update_english = "update educatio_msenglish.forgetPassNotification set status = 2 where childUserID = ".$common_details['MSE_userID']." and status = 0";
				$updated_notification = mysql_query($update_english);
			}	
		}

	}
	exit;
}
?>
<title>Student Requests</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/resetStudentPassword.css" rel="stylesheet" type="text/css">
<link href="css/editStudentDetails.css" rel="stylesheet" type="text/css">
<script src="libs/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="libs/css/jquery-ui.css" />
<script src="libs/jquery-ui.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
		$("#students").css("font-size","1.4em");
		$("#students").css("margin-left","40px");
		$(".arrow-right-yellow").css("margin-left","10px");
		$(".rectangle-right-yellow").css("display","block");
		$(".arrow-right-yellow").css("margin-top","3px");
		$(".rectangle-right-yellow").css("margin-top","3px");
	}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load()">
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
		<?php
		$add_space = '<br /><br />';
		
		if(strcasecmp($user->category,"School Admin")==0)
		{
			$get_reset = "SELECT a.userID, a.username, a.childName, a.childClass, b.requestDate, b.id FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND b.category=1 AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%".SUBJECTNO."%' ORDER BY b.requestDate DESC";
		}
		else
		{
			$get_reset = "SELECT a.userID, a.username, a.childName, a.childClass, b.requestDate, b.id FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND b.teacherUserID=$userID AND b.category=1 AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%".SUBJECTNO."%' ORDER BY b.requestDate DESC";
		}
		$exec_reset = mysql_query($get_reset);
		if(mysql_num_rows($exec_reset) > 0) {
			?>
			<h2 style="margin-left: 1%;">Password Reset Request</h2>
			<table class="gridtable dataPresent" width="98%" align="center"  cellspacing="0" cellpadding="3" border="1">
				<thead>
					<tr>
						<td class="header" width="3%" align="center"><b>#</b></td>
						<td class="header" width="23%" align="center"><b>Username</b></td>
						<td class="header" width="30%" align="center"><b>Child Name</b></td>
						<td class="header" width="5%" align="center"><b>Class</b></td>
						<td class="header" width="15%" align="center"><b>Requested On</b></td>
						<td class="header" width="27%" align="center"><b>Action</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$n = 1;
					while($row_reset = mysql_fetch_array($exec_reset)) {
						?>
						<tr>
							<td align="center"><?php echo $n; ?></td>
							<td><?php echo $row_reset['username']; ?></td>
							<td><?php echo $row_reset['childName']; ?></td>
							<td align="center"><?php echo $row_reset['childClass']; ?></td>
							<td align="center"><?php echo $row_reset['requestDate']; ?></td>
							<td align="center">
								<a style="cursor: pointer;" onClick="submitForm('<?php echo $row_reset['id']; ?>','1','1');">Reset Password</a> | 
								<a style="cursor: pointer;" onClick="submitForm('<?php echo $row_reset['id']; ?>','1','2');">No Action</a>
							</td>
						</tr>
						<?php
						$n++;
					}
					?>
				</tbody>
			</table>
			<?php
		} else {
			$add_space = '';
		}
		
		echo $add_space;
		if(strcasecmp($user->category,"School Admin")==0)
		{
			$get_lock = "SELECT a.userID, a.username, a.childName, a.childClass, b.requestDate, b.id FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND b.category=2 AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%".SUBJECTNO."%' ORDER BY b.requestDate DESC";
		}
		else
		{
			$get_lock = "SELECT a.userID, a.username, a.childName, a.childClass, b.requestDate, b.id FROM adepts_userDetails a, adepts_forgetPassNotification b 
					   WHERE a.userID=b.childUserID AND b.status=0 AND b.teacherUserID=$userID AND b.category=2 AND schoolCode=$schoolCode AND a.category='STUDENT' AND subcategory='School' 
					   AND enabled=1 AND endDate>=curdate() AND subjects LIKE '%".SUBJECTNO."%' ORDER BY b.requestDate DESC";
		}
		$exec_lock = mysql_query($get_lock);
		if(mysql_num_rows($exec_lock) > 0) {
			?>
			<h2 style="margin-left: 1%;">Account Unlock Request</h2>
			<table class="gridtable dataPresent" width="98%" align="center"  cellspacing="0" cellpadding="3" border="1">
				<thead>
					<tr>
						<td class="header" width="3%" align="center"><b>#</b></td>
						<td class="header" width="23%" align="center"><b>Username</b></td>
						<td class="header" width="30%" align="center"><b>Child Name</b></td>
						<td class="header" width="5%" align="center"><b>Class</b></td>
						<td class="header" width="15%" align="center"><b>Locked On</b></td>
						<td class="header" width="24%" align="center"><b>Action</b></td>
					</tr>
				</thead>
				<tbody>
					<?php
					$n = 1;
					while($row_lock = mysql_fetch_array($exec_lock)) {
						?>
						<tr>
							<td align="center"><?php echo $n; ?></td>
							<td><?php echo $row_lock['username']; ?></td>
							<td><?php echo $row_lock['childName']; ?></td>
							<td align="center"><?php echo $row_lock['childClass']; ?></td>
							<td align="center"><?php echo $row_lock['requestDate']; ?></td>
							<td align="center">
								<a style="cursor: pointer;" onClick="submitForm('<?php echo $row_lock['id']; ?>','2','1');">Allow Access</a> | 
								<a style="cursor: pointer;" onClick="submitForm('<?php echo $row_lock['id']; ?>','2','2');">No Action</a>
							</td>
						</tr>
						<?php
						$n++;
					}
					?>
				</tbody>
			</table>
			<?php
		} else if(empty($add_space)) {
			?>
			<br />
			<br />
			<br />
			<br />
			<br />
			<center>
			No request is pending for password reset / account unlock.
			</center>
			<?php
		}
		?>
	</div>
<script>
function submitForm(process_id, reqType, action) {
	if(reqType == 1 && action == 1) {
		var con = confirm("Do you want to reset password of this student?");
		if(!con) return;
		
	} else if(reqType == 2 && action == 1) {
		var con = confirm("Do you want to unlock account of this student?");
		if(!con) return;
		
	} else if(action == 2) {
		var con = confirm("Do you want to ignore this request?");
		if(!con) return;
	}
	
	$.ajax({
		url: 'updateStudentPassword.php',
		type: 'post',
		data: "reqType="+reqType+"&process_id="+process_id+"&action="+action,
		success: function(response) {
			if(reqType == 1 && action == 1)
				alert("Password reset successfully!");
			else if(reqType == 2 && action == 1)
				alert("Account unlocked successfully!");
			window.location.reload(true);
		},
		error: function(xhr, desc, err) {
			alert("Something went wrong. Please try again later.");
		}
    });
}
</script>
<?php include("footer.php"); ?>