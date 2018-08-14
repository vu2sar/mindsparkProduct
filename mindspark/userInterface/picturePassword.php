<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");
include("../login/loginFunctions.php");
if (!isset($_SESSION['userID'])) {
    echo "<script>window.location='logout.php'</script>";
    exit();
}

$userID = $_SESSION['userID'];
$objUser = new User($userID);
$_SESSION['subcategory'] = $objUser->subcategory;
$_SESSION['schoolCode'] = $objUser->schoolCode;
$userName = $objUser->username;
$childName = explode(" ", $objUser->childName);
$childName = $childName[0];
$childClass = $objUser->childClass;
$childSection = $objUser->childSection;
$showMessage=0;
$message='';
$attempts_left = 5;
$wrongPassword = 0;

if (!$_REQUEST['butYes'] == '') {
    login();
}

$query = "select password from educatio_educat.common_user_details where username='" . $userName . "'";
$result = mysql_query($query);
$rw = mysql_fetch_array($result);
if ($rw[0] != "") {
    $setPassword = 1;
} else {
    $setPassword = 0;
}

if (isset($_POST['passwordValue'])) {
    $passwordValue = $_POST['passwordValue'];
    if ($setPassword == 0) {
        $query = "update educatio_educat.common_user_details set password=password('" . $passwordValue . "') where username='" . $userName . "'";
        $setPassword = 1;
		$showMessage=1;
        $result = mysql_query($query);
    } else {
        $query = "select password from educatio_educat.common_user_details where password=password('" . $passwordValue . "') and username='" . $userName . "'";
		
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            $rw = mysql_fetch_array($result);
			
			// remove wrong password attempts
			$remove = "DELETE FROM accountBlock WHERE userID=" . $_SESSION['userID'];
			$exec_remove = mysql_query($remove);
			
            validationProcess($objUser, $_SESSION['browserName'], $_SESSION['browserVersion'], $_SESSION['image1'], $_SESSION['image2'], $_SESSION['browser']);
			
        } else {
            $wrongPassword = 1;
            $query = "select * from accountBlock where userID=" . $_SESSION['userID'];
            $result = mysql_query($query) or die(mysql_error());
            if (mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result);
                $updateQuery = "update accountBlock set wrongAttemptCount=wrongAttemptCount+1 where userID=" . $_SESSION['userID'];
                $result = mysql_query($updateQuery) or die(mysql_error());
            } else {
				$insertQuery = "INSERT into accountBlock(userID,wrongAttemptCount) values(".$_SESSION['userID'].",1)";
				$result = mysql_query($insertQuery) or die(mysql_error());
			}
        }
    }
}

// get remaining attempts
$get_attempts = "SELECT wrongAttemptCount FROM accountBlock WHERE userID = ".$_SESSION['userID'];
$exec_attempts = mysql_query($get_attempts);
if(mysql_num_rows($exec_attempts) > 0) {
	$row_attempts = mysql_fetch_array($exec_attempts);
	$attempts_left -= $row_attempts['wrongAttemptCount'];
}

if($attempts_left <= 0) {
	$_SESSION['wrongPasswordBlock'] = 1;
	$message = 'You have made 5 wrong attempts. Your account has been locked. Please contact your school teacher to unlock your account.';
	
	if(!empty($_GET['reqTeacher'])) {
		$get_teachers = "SELECT userID, childEmail FROM adepts_userDetails WHERE schoolCode = ".$_SESSION['schoolCode']." AND 
			(category = 'TEACHER' OR category = 'School Admin') AND enabled = 1 AND endDate >= CURDATE()";
		$exec_teachers = mysql_query($get_teachers);
		$no_of_users = mysql_num_rows($exec_teachers);
		$teacherUserID = 0;
		$childUserID = $_SESSION['userID'];
		$teacherEmail = "";
		if($no_of_users == 1) {
			$row_single_teacher = mysql_fetch_array($exec_teachers);
			$teacherUserID = $row_single_teacher['userID'];
			$teacherEmail = $row_single_teacher['childEmail'];
		} else {
			$temp_teachers = array();
			while($row_teachers = mysql_fetch_array($exec_teachers)) {
				$userstring .= $row_teachers['userID'].",";
				$temp_teachers[$row_teachers['userID']] = $row_teachers['childEmail'];
			}
			$userstring = rtrim($userstring, ",");
			
			$class_teacher = "SELECT userID FROM adepts_teacherClassMapping WHERE userID IN (".$userstring.") AND class = ".$childClass." 
				AND section = '".$childSection."'";
			$exec_class_teacher = mysql_query($class_teacher);
			if(mysql_num_rows($exec_class_teacher) > 0) {
				$row_class_teacher = mysql_fetch_array($exec_class_teacher);
				$teacherUserID = $row_class_teacher['userID'];
				$teacherEmail = $temp_teachers[$teacherUserID];
			}
		}
		
		$add_notification = "INSERT INTO adepts_forgetPassNotification (childUserID, teacherUserID, category, status, requestDate) VALUES 
			(".$childUserID.", ".$teacherUserID.", 2, 0, NOW())";
		$exec_notification = mysql_query($add_notification);
		
		if(!empty($teacherEmail)) {
			$req_date = date("Y-m-d H:i:s");
			mailToTeacherUnlockAccount($teacherEmail, $objUser->childName, $userName, $req_date);
		}
		
		$_SESSION['loginPageMsg'] = 1;
		header("Location: ../login/index.php?login=9");
		exit;
	}
}
?>
<?php include("header.php"); ?>
<title>Mindspark</title>
<?php if ($theme == 1) { ?>
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<link href="css/generic/lowerClass.css" rel="stylesheet" type="text/css">
<link href="css/picturePassword/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if ($theme == 2) { ?>
<link rel="stylesheet" href="css/commonMidClass.css" />
<link rel="stylesheet" href="css/generic/midClass.css" />
<?php } else { ?>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/generic/higherClass.css" />
<?php } ?>
<script>var langType = '<?= $language; ?>';</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script language="javascript">
function load() {
	init();
	<?php if ($theme == 1) { ?>
	var a = window.innerHeight - (180);
	$('#pnlContainer').css("height", a + "px");
	$('#pictureContainer').css("height", a + "px");
	<?php } else if ($theme == 2) { ?>
	var a = window.innerHeight - (170);
	$('#pnlContainer').css("height", a + "px");
	<?php } else if ($theme == 3) { ?>
	var a = window.innerHeight - (170);
	var b = window.innerHeight - (610);
	$('#pnlContainer').css({"height": a + "px"});
	$('#sideBar').css({"height": a + "px"});
	$('#main_bar').css({"height": a + "px"});
	$('#menubar').css({"height": a + "px"});
	<?php } ?>
}

function logoff()
{
	window.location = "logout.php";
}
function init()
{
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins

}

function checkPassword(value) {
	$("#passwordValue").attr("value", value);
	$("#passwordForm").submit();
}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
    <div id="top_bar">
        <div class="logo">
        </div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?= $childName ?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?= $childClass . $childSection ?></span></div>
            </div>
        </div>
        <div id="studentInfoLowerClass" class="forHighestOnly">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?= $childName ?>&nbsp;&#9660;</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?= $childClass . $childSection ?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
            <div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" class="hidden">
            <div class="logout" onClick="logoff()"></div>
            <div class="logoutText" data-i18n="common.logout"></div>
        </div>
        <div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

    <div id="container">
        <div id="info_bar" class="forLowerOnly hidden">
            <div id="blankWhiteSpace"></div>
        </div>
        <div id="info_bar" class="forHigherOnly hidden">


            <div id="studentInfo">
                <div id="studentInfoUpper">
                    <div id="childClassDiv"><span data-i18n="common.class"></span> <?= $childClass . $childSection ?></div>
                    <div id="childNameDiv"><?= $childName ?></div>
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
            <form action="" method="POST" id="passwordForm">
                <div id="pictureContainer">
                    <div id="insideContainer">
					<?php if($message==''){ ?>
						<?php if ($showMessage == 0) { ?>
							<?php if ($setPassword == 0 && $wrongPassword == 0) { ?>
	                            <div id="blankWhiteSpace1">Please select your password</div>
							<?php } else if($wrongPassword == 0) { ?>
	                            <div id="blankWhiteSpace1">Please enter your password</div>
							<?php } ?>
							<?php if ($wrongPassword == 1) { ?>
	                            <div id="blankWhiteSpace1" style="color:red;font-size: 1em;">
								Sorry! This is not the correct picture/image! Please try again!<br />
								(Note – You have <?php echo $attempts_left; ?> more chances to try!)
								</div>
							<?php } ?>
	                        <div class="individualContainer dog" onClick="checkPassword('dog')">
	                        </div>
	                        <div class="individualContainer flubber" onClick="checkPassword('flubber')">
	                        </div>
	                        <div class="individualContainer ant" onClick="checkPassword('ant')">
	                        </div>
	                        <div class="individualContainer teddy" onClick="checkPassword('teddy')">
	                        </div>
	                        <div class="individualContainer mouse" onClick="checkPassword('mouse')">
	                        </div>
	                        <div class="individualContainer owl" onClick="checkPassword('owl')">
	                        </div>
	                        <div class="individualContainer monster" onClick="checkPassword('monster')">
	                        </div>
	                        <div class="individualContainer elephant" onClick="checkPassword('elephant')">
	                        </div>
	                        <div class="individualContainer lion" onClick="checkPassword('lion')">
	                        </div>
	                        <div class="individualContainer zebra" onClick="checkPassword('zebra')">
	                        </div>
	                        <div class="individualContainer peacock" onClick="checkPassword('peacock')">
	                        </div>
	                        <div class="individualContainer bird" onClick="checkPassword('bird')">
	                        </div>
	                        <input type="hidden" id="passwordValue" name="passwordValue" value=""/>
	                        <div style="clear:both">
	                        </div>
						<?php } else if($showMessage==1){ ?>
							<div class="blankContainer"></div>
							<div class="scaleImage individualContainer <?=$passwordValue?>" onClick="checkPassword('<?=$passwordValue?>')">
	                        </div>
							<div id="blankWhiteSpace2">
							From today, this image will help you to get into Mindspark! So you have to remember it!<br />
							Now, click/tap this image again, to continue!<br /><br />
							Note - this image is very important, so don't forget it!
							</div>
							<input type="hidden" id="passwordValue" name="passwordValue" value=""/>
						<?php } ?>
					<?php }else{ ?>
						<div id="blankWhiteSpace2" style="margin-top: 0px; font-size: 20px; line-height: 26px;">
						Sorry! It seems you are unable to remember your password.<br />No problem, you can - <br /><br />
						Request your teacher for help.<br /><a href="picturePassword.php?reqTeacher=1">Click here to send a request to your teacher.</a><br /><br />
						OR<br /><br />
						Ask your parent to write to us at <a href="maito:mindspark@ei-india.com">mindspark@ei-india.com</a><br /><br />
						(Note - It may take a day or two for your teacher to reset your password, please be patient!)
						</div>
					<?php } ?>
                    </div>
                </div>
            </form>

        </div>
    </div>
<?php include("footer.php"); ?>