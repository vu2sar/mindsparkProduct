<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");

if(!isset($_SESSION['userID']))
{
	header("Location:logout.php");
	exit();
}

$userID = $_SESSION['userID'];
$objUser = new User($userID);
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$childClass    = $objUser->childClass;
$secId	=	isset($_GET["sec"])?$_GET["sec"]:"";
$sparkieImage = $_SESSION['sparkieImage'];

?>

<?php include("header.php"); ?>

<title>Mindspark</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/whatsNew/lowerClass.css?datetime=2017.01.05.22.54.56" rel="stylesheet" type="text/css">
<?php } else if($theme==2) { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/whatsNew/midClass.css?datetime=2017.01.04.11.16.50" />
<?php } else { ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/whatsNew/higherClass.css?datetime=2017.01.05.22.54.56" />
<?php } ?>
<link href="css/colorbox.css" rel="stylesheet" type="text/css">
<script>var langType = '<?=$language;?>';</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/combined.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>-->
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>
<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->
<script language="javascript">

function load(){
<?php if($theme==1) { ?>
	var a= window.innerHeight - (205);
	$('#formContainer').css("height",a+"px");
<?php } else if($theme==2){ ?>
	var a= window.innerHeight - (120);
	$('#pnlContainer').css("height","auto");
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (170);
			var b= window.innerHeight - (610);
			$('#formContainer').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#menubar').css({"height":a+"px"});
		<?php } ?>
		if(androidVersionCheck==1){
			$('#formContainer').css("height","auto");
			$('#main_bar').css("height",$('#formContainer').css("height"));
			$('#menu_bar').css("height",$('#formContainer').css("height"));
			$('#sideBar').css("height",$('#formContainer').css("height"));
		}
}

function logoff()
{
	setTryingToUnload();
	window.location="logout.php";
}

function scrollToElement(ele) {
    $(window).scrollTop(ele.offset().top).scrollLeft(ele.offset().left);
}
function scrolltoTop(){
	$(window).scrollTop(0);
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
		linkText = "https://docs.google.com/viewer?url="+encodeURI(<?= "'".WHATSNEW."'" ?>+linkText)+"&embedded=true";
	$("#iFrame").attr("src",linkText);
	$("#modelTitleText").text(titleText);
}
function showPrevComments()
{
	setTryingToUnload();
	window.location = "viewComments.php?from=links&mode=1";
}
var click=0;
function openMainBar(){
	
	if(click==0){
		if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
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
function getHome()
{
	setTryingToUnload();
	window.location.href	=	"home.php";
}
</script>
</head>
<body class="translation" onLoad="load()" onResize="load();">
	<div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='myDetailsPage.php'><span data-i18n="homePage.myDetails"></span></a></li>
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li> -->
									<li><a href='changePassword.php'><span data-i18n="homePage.changePassword"></span></a></li>
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
		<div id="home" class="forMidOnly">
			<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
			<div id="homeText" class="linkPointer" onClick="getHome()">Home</div>
		</div>
    </div>

	<div id="container">
    	<div id="info_bar" class="forLowerOnly hidden">
        	<div id="blankWhiteSpace">
				<div id="home" class="forLowerOnly">
					<div id="homeIcon" class="linkPointer" onClick="getHome()"></div>
				</div>
			</div>
        </div>
		 <div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span>NOTIFICATIONS</span></div>
                </div><div class="arrow-right"></div>
				<div id="endSessionHeading">WHAT'S NEW</div>
				<div class="clear"></div>
			</div>
			<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<!--<div id="info_bar" class="forHigherOnly">
			<div id="heading">What's New</div>
		</div>-->
		<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="report">
					<a href="#whatsKeepingBusy" <?php echo (($secId=='')?'':"style='display:none;'"); ?><span id="reportText">What's keeping us busy!</span></a>
				</div>
				<div class="empty1">
				</div>
				<div id="NA1" onClick="showPrevComments();">
					<div id="naM" class="pointed2">
					</div></br>
					COMMENT
				</div>
				<a class="links removeDecoration" href="whatsNew.php"><div id="A1">
					<div id="aM" class="pointed1">
					</div></br>
					WHATS NEW
				</div></a>
			</div>
			</div>
			<div id="pnlContainer">
            	<div id="formContainer">

	<table align="center" width="98%" border="0" cellpadding="3" cellspacing="3">
        <tr id="trWhatsNew" <?php echo (($secId=='' || $secId!=2)?'':"style='display:none;'"); ?>>
        <td>
        <div style='width:100%; padding-bottom: 35px; padding-top: 20px;'>
        <div id="pnlWhatsNew" class="sideHead">What's new in Mindspark?</div>
        <div class="linkToMore hidden">
        <a class="links removeDecoration" href="#whatsKeepingBusy" <?php echo (($secId=='')?'':"style='display:none;'"); ?>>What's keeping us busy!</a>
        <a class="links removeDecoration" href="whatsNew.php" <?php echo (($secId=='')?"style='display:none;'":''); ?>>Go Back</a>
        </div>
        </div>
        </td>
        </tr>
        <tr><td>
        <table align="center" width="91%" border="0" cellpadding="3" cellspacing="3">
        <?php
        if($secId=='' || $secId!=2)
        {
        $allWhatsNew	=	getWhatsNewList(1, $secId);
        foreach ($allWhatsNew as $id=>$detail)
        {
        echo '<tr>
            <td>
            <div style="width:100%;">
            <div style="width:100%;">
            <div align="left" style=" float:left; width:118px;">
            <div class="imagediv" align="center">';
			if(trim($detail['thumbnailURL'])!="") { 
				echo '<img src="'.WHATSNEW.$detail['thumbnailURL'] .'" border="0" style="width:102px; height:82px;"></img>';
			}
		echo '</div>
            </div>
            <div align="right" style="margin-left:150px;height:86px;">
            <div class="activityHead" style="width:100%;padding-top:3px;" align="left">
            '.$detail['featureTitle'].'&nbsp;<div class="tag'.$detail['featureType'].' hidden" tooltip="'.$detail['featureType'].'">'.substr($detail['featureType'],0,1).'</div><div style="float:right;color:black">'.$detail['approveDate'].'</div>
            </div>
            <div class="whatsNewContent" align="left" style=" margin-top:2px;">
            <span>'.$detail['description'] .' &nbsp;'.($detail['documentURL']==''?'':' <a href="#modelView" class="titleLink" onclick="setURL(\''.$detail['documentURL'].'\',\''. addslashes($detail['featureTitle']) .'\')">Learn More</a>').'</span>
            </div>
            </div>
            </div>
            </div>
            </td>
        </tr>';
        }
        }
        ?>

        </table>
        </td></tr>
        <tr <?php echo (($secId=='')?'':"style='display:none;'"); ?>>
        <td>
        <div><a href="whatsNew.php?sec=1" class="lnkAllFeatures">All features</a></div>

        </td>
        </tr>
        <tr><td><br/></td></tr>
        <tr <?php echo (($secId=='' || $secId==2)?'':"style='display:none;'"); ?>>
        <td>
        <div id="whatsKeepingBusy" style='width:100%; padding-bottom: 35px;'>
        <div class="sideHead">What's keeping us busy!</div>
        <div class="linkToMore hidden">
        <a class="links removeDecoration" onclick= "scrolltoTop()" <?php echo (($secId=='')?'':"style='display:none;'"); ?>>What's new in Mindspark?</a>
        <a class="links removeDecoration" href="whatsNew.php" <?php echo (($secId=='')?"style='display:none;'":''); ?>>Go Back</a>
        </div>
        </div>
        </td>
        </tr>
        <tr><td>
        <table align="center" width="93%" border="0" cellpadding="3" cellspacing="3">
        <?php
        if($secId=='' || $secId==2)
        {
        $allWhatsKeepingBusy=getWhatsNewList(2, $secId);
        foreach ($allWhatsKeepingBusy as $id=>$detail)
        {
        echo '<tr>
            <td>
            <div style="width:100%;">
            <div style="width:100%;">
            <div align="left" style=" float:left; width:118px;">
            <div class="imagediv" align="center">';
		if(trim($detail['thumbnailURL'])!="") { 
			echo '<img src="'.WHATSNEW.$detail['thumbnailURL'] .'" border="0" style="width:102px; height:82px;"></img>';
		}			
		echo '</div>
            </div>
            <div align="right" style="margin-left:150px;height:86px;">
            <div class="activityHead" style="width:100%;padding-top:3px;" align="left">
            '.$detail['featureTitle'] .'<div style="float:right;color:black">'.$detail['approveDate'].'</div>
            </div>
            <div class="whatsNewContent" align="left" style=" margin-top:2px;">
            <span>'.$detail['description'] .' &nbsp;'.($detail['documentURL']==''?'':' <a href="#modelView" class="titleLink" onclick="setURL(\''.$detail['documentURL'].'\',\''. addslashes($detail['featureTitle']) .'\')">Learn More</a>').'</span>
            </div>
            </div>
            </div>
            </div>
            </td>
        </tr>';
        }
        }
        ?>
        </table></td></tr>
        <tr style='display:none;'>
        <td>
            <div><a href="whatsNew.php?sec=2" class="lnkAllFeatures">All features</a></div>
        </td>
        </tr>
        <tr><td><br/></td></tr>
        </table>
                </div>
	        </div>
	</div>
	<div style="display:none">
    <div id="modelView">
        <div id="modelTitleText" style="float:left; width:100%;">
        </div>
        <div style="clear:both"></div>
        <div style="float:left; width:98%;height: 460px;">
        	<iframe id="iFrame" class="iFrame" height="100%" width="100%" src="" allowtransparency="true"></iframe>
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
		$old_date_timestamp = strtotime($allWhatsNew[$Id]['approveDate']);
		$allWhatsNew[$Id]['approveDate'] = date('F d, Y', $old_date_timestamp);
		$allWhatsNew[$Id]['thumbnailURL']	=	$rw['thumbnailURL'];
		$allWhatsNew[$Id]['documentURL']	=	$rw['documentURL'];
		$allWhatsNew[$Id]['lastModified']	=	$rw['lastModified'];
	}
	return $allWhatsNew;
}

?>