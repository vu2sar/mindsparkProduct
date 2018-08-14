<?php 
include("header.php");
include("../slave_connectivity.php");
include("../userInterface/constants.php");


?>

<title>What's New</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="../userInterface/css/colorbox.css" rel="stylesheet" type="text/css">
<link href="css/whatsNew.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script language="javascript" type="text/javascript" src="libs/jquery.colorbox-min.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#sideBar").css("height",sideBarHeight+"px");
		/*$("#container").css("height",containerHeight+"px");*/
	}
</script>
<script>
function scrollToElement(ele) {
    $(window).scrollTop(ele.offset().top).scrollLeft(ele.offset().left);
}
$(document).ready(function(e) {
	$(".titleLink,.downloadLink").colorbox({inline:true, width:"80%", height:"95%",
		onClosed:function(){
			$("#iFrame").attr("src","");
		},
		onOpen: function(){
			tryingToUnloadPage = false;				
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
</script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
		<?php include('referAFriendIcon.php') ?>
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>What is New</span>
			</div>
			<div id="line"> </div>
			
			<div id="containerBody">
			<div id="formContainer">

	<table align="center" width="98%" border="0" cellpadding="3" cellspacing="3">
        <tr id="trWhatsNew" <?php echo (($secId=='' || $secId!=2)?'':"style='display:none;'"); ?>>
        <td>
        <div style='width:100%; padding-bottom: 35px; padding-top: 20px;'>
        <div id="pnlWhatsNew" class="sideHead">What's new in Mindspark?</div>
        <div class="linkToMore hidden">
        <a class="links removeDecoration" href="#whatsKeepingBusy" <?php echo (($secId=='')?'':"style='display:none;'"); ?> onclick="setTimeout(function() {tryingToUnloadPage = false;},500);">What's been keeping us busy</a>
        <a class="links removeDecoration" href="whatsNew.php" <?php echo (($secId=='')?"style='display:none;'":''); ?>>Go Back</a>
        </div>
        </div>
        </td>
        </tr>
        <tr><td>
        <table align="left" width="91%" border="0" cellpadding="3" cellspacing="3" style="font-size: 1.2em;">
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
            <div class="imagediv" align="center"><img src="'.WHATSNEW.$detail['thumbnailURL'] .'" border="0" style="width:102px; height:82px;"></img></div>
            </div>
            <div align="right" style="margin-left:150px;height:86px;">
            <div class="activityHead" style="width:100%;padding-top:3px;" align="left">
            '.$detail['featureTitle'].'&nbsp;<div class="tag'.$detail['featureType'].' hidden" title="'.$detail['featureType'].'">'.substr($detail['featureType'],0,1).'</div>
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
        <div><a href="whatsNew.php?sec=1" class="lnkAllFeatures"><u>All features</u></a></div>

        </td>
        </tr>
        <tr><td><br/></td></tr>
        <tr <?php echo (($secId=='' || $secId==2)?'':"style='display:none;'"); ?>>
        <td>
        <div id="whatsKeepingBusy" style='width:100%; padding-bottom: 35px;'>
        <div class="sideHead">What's been keeping us busy...</div>
        <div class="linkToMore hidden">
        <a class="links removeDecoration" href="#pnlWhatsNew" <?php echo (($secId=='')?'':"style='display:none;'"); ?> onclick="setTimeout(function() {tryingToUnloadPage = false;},500);">What's new in Mindspark?</a>
        <a class="links removeDecoration" href="whatsNew.php" <?php echo (($secId=='')?"style='display:none;'":''); ?>>Go Back</a>
        </div>
        </div>
        </td>
        </tr>
        <tr><td>
        <table align="left" width="93%" border="0" cellpadding="3" cellspacing="3" style="font-size: 1.2em;">
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
            <div class="imagediv" align="center"><img src="'.WHATSNEW.$detail['thumbnailURL'] .'" border="0" style="width:102px; height:82px;"></img></div>
            </div>
            <div align="right" style="margin-left:150px;height:86px;">
            <div class="activityHead" style="width:100%;padding-top:3px;" align="left">
            '.$detail['featureTitle'] .'
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
		
		
	</div>
<div style="display:none">
    <div id="modelView">
        <div id="modelTitleText" style="float:left; width:100%;">
        </div>
        <div style="clear:both"></div>
        <div style="float:left; width:98%; height:480px">
        	<iframe id="iFrame" class="iFrame" height="100%" width="100%" src="" allowtransparency="true"></iframe>
        </div>                
    </div>
</div>	
	

<?php include("footer.php") ?>

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