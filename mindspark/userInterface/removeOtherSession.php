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

if(isset($_GET['image1'])){
    $image = $_GET['image1'];
}

if(isset($_GET['image2'])){
    $image = $_GET['image2'];
}



$userID = $_SESSION['userID'];
$objUser = new User($userID);

$childName    = explode(" ",$objUser->childName);
$childName    = $childName[0];
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;

if(isset($_REQUEST['butYes']))
{
    mysql_query("UPDATE ".TBL_SESSION_STATUS." SET logout_flag=1 WHERE logout_flag=0 AND userID=".$_SESSION["userID"]." AND sessionID!=".$_SESSION["sessionID"]) or die(mysql_error());
    login();
}
else{
?>

<?php include("header.php"); ?>

<title>Mindspark</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/generic/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
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
function setAction(arg)
{
	if(arg=='Yes')
	{
		document.frmDuplicate.action='removeOtherSession.php';
	}
	else
	{
		document.frmDuplicate.action='logout.php';
	}
}

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
}

function logoff()
{
	window.location="logout.php";
}
function init()
{
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins

}
window.history.forward(1);


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
                	<div id="childClassDiv"><span data-i18n="common.class"></span> <?=$childClass.$childSection?></div>
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
            	<div id="formContainer">
                    <form name="frmDuplicate" id="frmDuplicate" method="POST">
                    <input type="hidden" name="sessionID" id="sessionID" value="<?=$_REQUEST['sessionID']?>">
                    <input type="hidden" name="forSubject" id="forSubject" value="<?=$_REQUEST['forSubject']?>">

                        <div align='center'><h1>You are already logged into Mindspark !!</br></br></h1>
                        <u>Another session is active for this account else where. Do you wish to continue and log out the other session?</u></div>
                        <br/>
                        <center><input type='submit' name='butYes' class='button1' onClick="setAction('Yes')" value='Yes'> &nbsp;&nbsp;&nbsp;
                        <input type='submit' name='butNo' class="button1" onClick="setAction('No')" value='No'></center>
                    </form>            
                </div>
	        </div>
	</div>
<?php include("footer.php"); }?>