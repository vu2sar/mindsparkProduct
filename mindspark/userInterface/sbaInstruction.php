<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");

if(!isset($_SESSION['userID']) || !isset($_SESSION['sbaTestID']))
{
	header("Location:logout.php");
	exit();
}

$userID = $_SESSION['userID'];
$objUser = new User($userID);

$childClass    = $objUser->childClass;
$secId	=	isset($_GET["sec"])?$_GET["sec"]:"";

?>

<?php include("header.php"); ?>

<title>Mindspark</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/sbaInstruction/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/sbaInstruction/midClass.css" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/sbaInstruction/higherClass.css" />
<?php } ?>
<link href="libs/css/colorbox.css" rel="stylesheet" type="text/css">
<script>var langType = '<?=$language;?>';</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>

<script language="javascript">

function load(){
<?php if($theme==1) { ?>
	var a= window.innerHeight - (205);
	$('#formContainer').css("height",a+"px");
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (120);
	$('#pnlContainer').css("height",a+"px");
<?php } ?>
}

function logoff()
{
	window.location="logout.php";
}

function scrollToElement(ele) {
    $(window).scrollTop(ele.offset().top).scrollLeft(ele.offset().left);
}
$(document).ready(function(e) {
	$(".titleLink,.downloadLink").colorbox({inline:true, width:"80%", height:"95%",
		onClosed:function(){
			$("#iFrame").attr("src","");
		}
	});
});
function setURL(linkText,titleText)
{
	if(linkText != "")
		linkText = "http://docs.google.com/viewer?url="+encodeURI(<?= "'".WHATSNEW."'" ?>+linkText)+"&embedded=true";
	$("#iFrame").attr("src",linkText);
	$("#modelTitleText").text(titleText);
}
function showPrevComments()
{
	window.location = "viewComments.php?from=links&mode=1";
}
var click=0;
function openMainBar(){
	
	if(click==0){
		$("#main_bar").animate({'width':'245px'},600);
		$("#plus").animate({'margin-left':'227px'},600);
		$("#vertical").css("display","none");
		click=1;
	}
	else if(click==1){
		$("#main_bar").animate({'width':'15px'},600);
		$("#plus").animate({'margin-left':'-3px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}

</script>
</head>
<body class="translation" onLoad="load()">
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$objUser->childName?>&nbsp;&#9660;</span></a>
                                <ul>
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
		 <div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                </div><div class="arrow-right"></div>
				<div id="endSessionHeading">TEST</div>
				<div class="clear"></div>
			</div>
			<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="javascript:void(0)" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="javascript:void(0)" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="javascript:void(0)"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<!--<div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div>-->
			<div id="drawer5"><div id="drawer5Icon"></div>REWARD POINT
			</div>
			<!--<a href="javascript:void(0)"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<!--<div id="info_bar" class="forHigherOnly">
			<div id="heading">What's New</div>
		</div>-->

			<div id="pnlContainer">
				<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
            	<div id="continueTest" class="button" onClick="javascript:$('#continueSba').submit()">CONTINUE</div>
				<div id="continueArrow" class="arrow-right"></div>
			</div>
			</div>
            	<div id="formContainer">
                    <form name="continueSba" id="continueSba" action="controller.php" method="POST">
                    	<input type="hidden" name="mode" id="mode" value="<?=$_POST["mode"]?>">
            			<div id="instructionText">You are going to start an assessment. Do not be nervous, just do your best.<br>You will have to answer <?=$_POST["totalQues"]?> questions in <?=$_POST["totalTime"]?> minutes.<br>If you do not understand a particular question, make an educated guess.<br>Please <u>do not</u> guess randomly.<br><br>ALL THE BEST!</div><br><br><br>
                		<div align="center" class="hidden"><input type="submit" value="Continue" class="button1" name="updateRecord" id="submit_button"></input></div>
                    </form>
                </div>
	        </div>
	</div>
<?php include("footer.php"); ?>
</body>
</html>

<?php
function getWhatsNewList($sectionId, $secId)
{
	if($secId == '')
		$limit=" Limit ".($sectionId==1?30:5);
	else
	{
		$sectionId=($secId==2?2:1);
		$limit="";
	}

	$sq	=	"SELECT `id`, `sectionId`, featureType, `featureName`, `featureTitle`, `description`, `owner`, `status`, `fkApprovedBy`, `approveDate`, `thumbnailURL`, `documentURL`, `lastModified` FROM `adepts_whatsnew` WHERE sectionId =" .$sectionId. " AND status=1 ORDER BY priority ".$limit; //sectionId =1 means What's New
	$rs	=	mysql_query($sq);
	while ($rw=mysql_fetch_assoc($rs))
	{
		$Id	=	$rw['id'];
		$allWhatsNew[$Id]['sectionId']		=	$rw['sectionId'];
		$allWhatsNew[$Id]['featureType']	=	$rw['featureType'];
		$allWhatsNew[$Id]['featureName']	=	$rw['featureName'];
		$allWhatsNew[$Id]['featureTitle']	=	$rw['featureTitle'];
		$allWhatsNew[$Id]['description']	=	$rw['description'];
		$allWhatsNew[$Id]['owner']			=	$rw['owner'];
		$allWhatsNew[$Id]['status']			=	$rw['status'];
		$allWhatsNew[$Id]['fkApprovedBy']	=	$rw['fkApprovedBy'];
		$allWhatsNew[$Id]['approveDate']	=	$rw['approveDate'];
		$allWhatsNew[$Id]['thumbnailURL']	=	$rw['thumbnailURL'];
		$allWhatsNew[$Id]['documentURL']	=	$rw['documentURL'];
		$allWhatsNew[$Id]['lastModified']	=	$rw['lastModified'];
	}
	return $allWhatsNew;
}

?>