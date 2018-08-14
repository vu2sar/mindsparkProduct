<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");

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

$teacherTopicName = $_SESSION['teacherTopicName'];
$clusterAttemptID = $_POST['clusterAttemptID'];
$sql = "SELECT SUM(R) as corrects, COUNT(R) as attempts FROM ".TBL_QUES_ATTEMPT_CLASS." WHERE clusterAttemptID='$clusterAttemptID'";
$result = mysql_query($sql);
$row = mysql_fetch_assoc($result);
$attempts = $row['attempts'];
$corrects = $row['corrects'];
$wrongs = $attempts - $corrects;
$sparkies = floor($corrects / 3);

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
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>


<script language="javascript">

function load(){
	 init();
<?php if($theme==1) { ?>
	var a= window.innerHeight - (180);
	$('#pnlContainer').css("height",a+"px");
<?php } else if($theme==2) { ?>
	var a= window.innerHeight - (250);
	$('#pnlContainer').css("height",a+"px");
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#pnlContainer').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
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
<body class="translation" onLoad="load()" onResize="load()">
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
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
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
        	<div class="logout" onClick="logoff();"></div>
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
             <div id="home">
                <div id="homeIcon" ></div>
                <div id="dashboardHeading" class="forLowerOnly"> > <?=$teacherTopicName?> > <span data-i18n="common.practiceReport"></span></div>
                <div class="clear"></div>
            </div>
        </div>
		<div id="info_bar" class="forHigherOnly hidden">
			<div id="topic">
				<div id="home">
                    <!--<div id="homeIcon" ></div>
                    <div id="homeText"><span class="removeDecoration" data-i18n="dashboardPage.home"></span> > </div>-->
                    <div id="topic_name"><?=$teacherTopicName?> > <span data-i18n="common.practiceReport"></span></div>
                    <div class="clear"></div>

				</div>
                <div class="clear"></div>

			</div>

			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div id="childClassDiv"><span data-i18n="common.class"></span> <?=$childClass.$childSection?></div>
                	<div id="childNameDiv"><?=$childName?></div>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="clear"></div>
		</div>
		<div id="info_bar" class="forHighestOnly">
				<a href="home.php" style="text-decoration:none;color:inherit"><div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase" data-i18n="homePage.recent"></span></div>
                </div></a>
				<div class="arrow-right"></div>
				<div id="topic_name"><?=$teacherTopicName?> > <span data-i18n="common.practiceReport"></span></div>
		</div>
			<div id="pnlContainer">
            	<div id="formContainer">
                    <table cellspacing="5" align="center" width="350px" style="width:350px;">
                        <tr>
                            <td align="center"><div class="correctAnsIcon"></div></td>
                            <td><span class="highFont">&nbsp;=</span></td>
                            <td><span class="highFont">&nbsp;<?= $corrects ?></span></td>
                        </tr>
                        <tr>
                            <td align="center"><div class="wrongAnsIcon"></div></td>
                            <td><span class="highFont">&nbsp;=</span></td>
                            <td><span class="highFont">&nbsp;<?= $wrongs ?></span></td>
                        </tr>
                        <tr>
                            <td><div style="margin:auto;display:block;height:35px;width:25px;"><img src="assets/sparkie.png" alt="sparkie" style="height:35px;width:25px;" /></div></td>
                            <td><span class="highFont">&nbsp;=</span></td>
                            <td><span class="highFont">&nbsp;<?= $sparkies ?></span></td>
                        </tr>
                    </table>
                    <br>
                    <br>
                    <div align="center">
                        <input type="button" class="button1" name="submitbutton"  data-i18n="[value]common.ok" onClick="document.frmContinueQues.submit();">
                    </div>

                    <form name="frmContinueQues" id="frmContinueQues" action="question.php" method="post">
                        <input type="hidden" name="qode" id="qode" value="<?= $_SESSION['qcode'] ?>">
                        <?php
                        $qNo = isset($_SESSION['qno']) ? $_SESSION['qno'] : "1";
                        ?>
                        <input type="hidden" name="qno" id="qno" value="<?= $qNo ?>">
                        <input type="hidden" name="quesCategory" id="quesCategory" value="normal">
                        <input type="hidden" name="showAnswer" id="showAnswer" value="1">
                    </form>
                </div>
	        </div>
	</div>
<?php include("footer.php"); ?>
</body>
</html>
