<?php

	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	@include("../userInterface/check1.php");
	include("../userInterface/constants.php");
	include("../userInterface/classes/clsUser.php");
	// including file to config forum feature to user
	include('teacherforumsupport/config.php');
	// included to get notification on reload rest we will call this file to update the same through ajax
    include_once('teacherforumsupport/notifications.php');
	
	include('checkForPoster.php');

	if(!isset($_SESSION['userID']))
	{
		header("Location:../logout.php");
		exit;
	}
	
	if($_SERVER['HTTP_REFERER'] != '' || $_SERVER["REQUEST_URI"]!= '')
	{
		$pageurl = $_SERVER["REQUEST_URI"];	
		
		$pageurl = explode ('/',$pageurl);
		$interfaceName=$pageurl[2];
		$pageurl = $pageurl[3];
		
		
		if (strpos($pageurl,'?') !== false) {
			$pageurl = substr($pageurl, 0, strpos($pageurl, '?'));
		}
		$type="";
		if($interfaceName=='teacherInterface')
			$type='teacherInterface';
		

		$query = "SELECT pageID FROM trackingPageDetails WHERE pageName='".$pageurl."' and pageType='".$type."'";
		$result = mysql_query($query);
		$l = mysql_fetch_array($result);
		
		$pageid = $l[0];
		if($pageid)
		{
			$query = "insert into trackingTeacherInterface (userID, sessionID, pageID, lastmodified) values (".$_SESSION['userID'].",".$_SESSION['sessionID'].",$pageid,now())";
			mysql_query($query) or die(mysql_error());
		}

	}
	$category   = $_SESSION['admin'];  
    if(strcasecmp($category,"School Admin")!=0 && strcasecmp($category,"Teacher")!=0)
	{ ?>
		<script  type="text/javascript">
		window.location.href="/mindspark/userInterface/newTab.php";
		</script>
	<?php }

	/*header('Content-Type:text/html; charset=UTF-8');*/
	/*checkInterface($_SESSION['userID']);
	function checkInterface($userID)
	{

		$sq	=	"SELECT interfaceFlag FROM adepts_teacherInterfaceScreen WHERE userID=$userID";
		$rs	=	mysql_query($sq);
		if(mysql_num_rows($rs)!=0)
		{
			$rw	=	mysql_fetch_array($rs);
			if($rw[0]==0)
				header("location:../ti_home.php");
		}
		else
			header("location:../ti_home.php");
	}*/
	
	if($_SESSION["sessionID"]!="")
	{
		$query  = "UPDATE adepts_sessionStatus set endTime=NOW() where sessionID=".$_SESSION["sessionID"];
		$result = mysql_query($query) or die(mysql_error());
	}

	$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
	
	$offlineMode = false; //school is using in offline mode
	if(SERVER_TYPE=='LOCAL')
		$offlineMode = true;
?>

<!DOCTYPE HTML>
<html>
<head>
<?php 
if(intval($_SESSION['browserVersion'])==9)
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<?php 
}
else
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php 
}
if(isset($_SESSION["offlineActive"]) && $_SESSION["offlineActive"]==1 && SERVER_TYPE=='LIVE')
{
	checkForOfflineURL($schoolCode);
	unset($_SESSION["offlineActive"]);
}
	
function checkForOfflineURL($schoolCode)
{
	echo '<script src="../userInterface/libs/jquery.js"></script>
	<script type="text/javascript" src="../userInterface/libs/jquery-ui-1.8.16.custom.min.js"></script>
	<script src="../userInterface/libs/jquery.colorbox-min.js" type="text/javascript"></script>
	<link href="../userInterface/css/login/login.css?ver=11" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="../userInterface/css/colorbox.css">
	<script type="text/javascript">
	function redierectUser()
	{
		$.fn.colorbox({"href":"#moveToOfflineMessage","inline":true,"open":true,"escKey":false,"overlayClose": false, "height":310, "width":510});
		$("#cboxClose").hide();
	}
	function redirectTooffline()
	{
		window.location.href = "http://mindsparkserver/mindspark/login/";
	}
	function redirectToonline()
	{
		$("#cboxClose").click();
	}
	';
	echo '</script><div style="display:none"><div id="moveToOfflineMessage" style="padding:15px;font-family:Conv_HelveticaLTStd-LightCond;font-size: 16px;"><div><h2 align="center">Welcome to Mindspark!</h2><p><b>Hold on a second... You seem to be logging in from school, but into the Mindspark internet server. Click on continue if you want to continue on Mindspark internet server or click cancel for Mindspark School Server.</p></div><br><div onclick="redirectToonline()" style="border-style: solid;border-color: #bbb #888 #666 #aaa;border-width: 3px 4px 4px 3px;width: 9em;height: 2em;background: #ccc;color: #333;line-height: 2;text-align: center;text-decoration: none;font-weight: 900;cursor: pointer;margin-left: 50px;display: inline-block;">Continue</div><div onclick="redirectTooffline()" style="border-style: solid;border-color: #bbb #888 #666 #aaa;border-width: 3px 4px 4px 3px;width: 9em;height: 2em;background: #ccc;color: #333;line-height: 2;text-align: center;text-decoration: none;font-weight: 900;cursor: pointer;margin-left: 40px;display: inline-block;">Cancel</div></div></div><script>redierectUser();</script></body></html>';
}
?>
<script src="../userInterface/libs/css_browser_selector.js" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html;"/>
<!-- <meta
  name="viewport"
  content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" /> -->
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1, minimum-scale=1" />
<!--<meta content="text/html; charset=UTF-8"/>-->
<!--<meta http-equiv="Content-Type" content="text/html" />-->
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui.js"></script>
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script type="text/javascript" src="/mindspark/userInterface/libs/prompt.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var currentUrl = document.URL;
	if(currentUrl.indexOf("home.php") < 0){
		$("#aqad").hide();
		$("#kudos-top-div").hide();
	}
});

</script>
<?php if( $iPad ==true|| $Android ==true){?>
    <script>
        function doOnOrientationChange() {
            if (!jQuery('.promptContainer').is(":visible")) {
                var prompts = new Prompt({
                    text: 'Mindspark is best viewed and worked with in the landscape (horizontal) mode.<br><br>Please shift to landscape mode to proceed further. ',
                    type: 'block',
                    func1: function () {
                        jQuery("#prmptContainer").remove();
                    }
                });
                jQuery("#promptText").css('font-size', '160%');
            }
            jQuery('#promptContainer').css('display','none');
            var windowheight = jQuery(window).height();
            var windowwidth = jQuery(window).width();
            var pagecenterW = windowwidth / 2;
            var pagecenterH = windowheight / 2;
            /*jQuery("#promptBox").css({ 'margin-left': pagecenterW - 300 + 'px'});*/
        }

    </script>
  

<?php } ?>