<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");
include("../login/loginFunctions.php");
if(!isset($_SESSION['userID']))
{
	echo "<script>window.location='logout.php'</script>";
	exit();
}

$userID = $_SESSION['userID'];
$objUser = new User($userID);
$childName    = explode(" ",$objUser->childName);
$childName    = $childName[0];
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;

$question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favorite colour?');
$flag = 0;
if(isset($_POST['continue']))
{
	$birthDate = $_POST['birthdate'];
	if($birthDate=="")
	{
		echo "Please specify the birthdate!!";
	}
	else
	{
		$birthDate = substr($_POST['birthdate'],6,4)."-".substr($_POST['birthdate'],3,2)."-".substr($_POST['birthdate'],0,2);
		$secretQuestion = $_POST['secretQuestion'];
		$secretAnswer = strtolower($_POST['secretAnswer']);
		$objUser->updateDoB($birthDate, $secretQuestion, $secretAnswer);
                login();//Internally redirected
//		echo "<script language='JavaScript'>window.location='controller.php?mode=login'</script>";
	}
}

?>

<?php include("header.php"); ?>

<title>Mindspark</title>
<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/generic/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/generic/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/generic/higherClass.css" />
<?php } ?>
<script>var langType = '<?=$language;?>';</script>
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>

<link rel="stylesheet" type="text/css" href="css/CalendarControl.css" >
<script type="text/javascript" src="libs/CalendarControl.js" language="javascript"></script>
<script type="text/javascript" src="libs/calendarDateInput.js" language="javascript"></script>
<script type="text/javascript" src="libs/dateValidator.js"></script>

<script language="javascript">
function validate()
{
	if(document.getElementById('birthdate').value == "")
	{
		alert(i18n.t("getBirthDate.DoBMsg"));
		return false;
	}
	if (document.getElementById('secretQuestion').value=="")
	{
		alert(i18n.t("getBirthDate.secretQuesMsg"));
		document.getElementById('secretQuestion').focus();
		return false;
	}
	if (document.getElementById('secretAnswer').value=="")
	{
		alert(i18n.t("getBirthDate.secretAnsMsg1"));
		document.getElementById('secretAnswer').focus();
		return false;
	}
	if (document.getElementById('secretAnswer').value!="")
	{
		var string_length = document.getElementById('secretAnswer').value.length;
		if (string_length > 50)
		{
			alert(i18n.t("getBirthDate.secretAnsMsg2"));
			document.getElementById('secretAnswer').focus();
			return false;
		}
	}
	return true;

}

function load(){
	 init();
<?php if($theme==1) { ?>
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (170);
	$('#pnlContainer').css("height",a+"px");
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#pnlContainer').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menuBar').css({"height":a+"px"});
		<?php } ?>
}

function logoff()
{
	window.location="logout.php";
}
function getHome()
{
	window.location.href	=	"home.php";
}
function init()
{
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins

}

window.history.forward(1);

function logoff()
{
	window.location="logout.php";
}

</script>
</head>
<body class="translation" onLoad="load();" onResize="load();">
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$childName?>&nbsp;</span></a>
							<ul>
								<li><a href='logout.php' onClick="javascript:setTryingToUnload();"><span data-i18n="common.logout"></span></a></li>
							</ul>
							</li>
							
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
								<li><a href='logout.php' onClick="javascript:setTryingToUnload();"><span data-i18n="common.logout"></span></a></li>
							</ul>
                            </li>
							
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" class="hidden">
        	<div class="logout" onClick="logoff()"></div>
        	<div class="logoutText" data-i18n="common.logout"  onclick="logoff()"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace"></div>
             <!--<div id="home">
                <!--<div id="homeIcon" onClick="getHome()"></div>
                <div id="dashboardHeading" class="forLowerOnly"></div>
                <div class="clear"></div>
            </div>-->
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<!--<div id="topic">
				<div id="home">
                    <div id="homeIcon" onClick="getHome()"></div>
                    <div id="homeText"><span class="removeDecoration" onClick="getHome()" data-i18n="dashboardPage.home"></span></div>
                    <div class="clear"></div>
				</div>
                <div class="clear"></div>

			</div>-->

			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div id="childClassDiv"><span data-i18n="common.class"></span> <?=$childClass.$childSection?></div>
                	<div id="childNameDiv" class="Name"><?=$childName?></div>
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
        <form name= "frmDetails" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<div id="pnlContainer" style="width: 96%;min-height: 480px;">
            	<div id="formContainer">
						<div id='infomessg'>
							Please provide following information. It will be required to reset your password in future.
						</div>
                        <div class="message">
							<br/>
                        	<b><span data-i18n="getBirthDate.userName"></span></b>: <?=$objUser->username?>
                        </div>
                        <br/>
						<b><span data-i18n="getBirthDate.DoB"></span></b>:
						<br/>
						<input type="text" name="birthdate" id="birthdate" onFocus="showCalendarControl(this,'<?=$childClass?>');" size="10" onKeyUp="showCalendarControl(this);"  onBlur="validateDate(this);"> (dd-mm-yyyy)
						<br/>
						<br/>
						<b><span data-i18n="getBirthDate.secretQuestion"></span></b>:
						<br/>
						<select name="secretQuestion" id="secretQuestion">
							<option value="">Select</option>
							<?php
								for($i=0; $i<count($question_array); $i++)
								{
									echo "<option value=\"$question_array[$i]\">$question_array[$i]</option>";
								}
							?>
						</select>
						<br/><br/>
						<b><span data-i18n="getBirthDate.secretAnswer"></span></b>:
						<br/>
                        <input type="text" name="secretAnswer" id="secretAnswer" size="20" maxlength="50">
						<br/><br/>
						<input type="submit" name="continue" class="button1" tabindex="4" data-i18n="[value]common.continue" onclick="return validate();" class="submit_button">
                </div>
	        </div>
    	</form>
	</div>
<?php include("footer.php"); ?>