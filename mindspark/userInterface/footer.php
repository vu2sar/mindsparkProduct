<?php
	$addressString = $_SERVER['REQUEST_URI'];
	$address = explode("/",$addressString);
	$pageAddress =$address[3];
	$rewardTheme=$_SESSION["rewardTheme"];
	if($pageAddress=="home.php" && date("Y-m-d")>='2017-01-26' && date("Y-m-d")<='2017-02-02')
		$rewardTheme='festival';

	if($rewardTheme!="default" && $rewardTheme!="") { 
		if($theme==1 && $rewardTheme=='festival' && $pageAddress=="home.php") { ?>
		<link rel="stylesheet" href="/mindspark/userInterface/css/themes/lowerClass/<?php echo $rewardTheme; ?>.css?ver=3" />
		<?php } else if($theme==2 && $rewardTheme=='festival' && $pageAddress=="home.php") { ?>
	   		<link rel="stylesheet" href="/mindspark/userInterface/css/themes/midClass/<?php echo $rewardTheme; ?>.css?ver=3" />
		<?php } else if($theme==3 && $rewardTheme=='festival' && $pageAddress=="home.php") { ?>
			  <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css?ver=3" />
		<?php }	
		else if($theme==1 && $pageAddress!="question.php") { ?>
		<link rel="stylesheet" href="/mindspark/userInterface/css/themes/lowerClass/<?php echo $rewardTheme; ?>.css?ver=2" />
		<?php } else if($theme==2 && ($pageAddress!="question.php"  && $pageAddress!="topicPage.php")) { ?>
	   		<link rel="stylesheet" href="/mindspark/userInterface/css/themes/midClass/<?php echo $rewardTheme; ?>.css?ver=3" />
		<?php } else if($theme==3 && $pageAddress!="question.php") { ?>
			  <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css?ver=2" />
		<?php } else if($theme==3 && $pageAddress=="question.php" && ($rewardTheme=="girl" || $rewardTheme=="boy")) { ?>
			   <link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/<?php echo $rewardTheme; ?>.css?ver=2" />
				<link rel="stylesheet" href="/mindspark/userInterface/css/themes/higherClass/question/<?php echo $rewardTheme; ?>.css?ver=2" />
		<?php } 
}
if(basename($_SERVER['PHP_SELF']) != "index.php" && basename($_SERVER['PHP_SELF']) != "error.php" && basename($_SERVER['PHP_SELF']) != "newTab.php") 
	mysql_close();
?>
<?php if( $iPad ==true|| $Android ==true){?>
<script>
    doOnOrientationChange();
</script>
<style>
    #prmptContainer {
    display: none;
}

@media all and (orientation:portrait) {
    #prmptContainer {
        display: block;
    }
   #promptBox
   {
   	margin-left: 50px;
   }
}
</style>
<? }?>
<div id="bottom_bar">
    <div id="copyright" data-i18n="[html]common.copyright"></div>
</div>
<?php if(basename($_SERVER['PHP_SELF']) != "index.php" && basename($_SERVER['PHP_SELF']) != "enrichmentModule.php" && basename($_SERVER['PHP_SELF']) != "remedialItem.php") { ?>
<script>
var inactivityTimeout;
var idealTimeOut;
inactivityLogout();

jQuery(document).keypress(function(e) { 
	if(e.keyCode < 112 || e.keyCode > 123)
	{
		if(!jQuery("#inactivityPrompt").is(":visible"))
			inactivityLogout();
	}
});
jQuery(document).bind('touchstart mousemove mousedown DOMMouseScroll mousewheel', function(){
	if(!jQuery("#inactivityPrompt").is(":visible"))
		inactivityLogout();
});

function inactivityLogout()
{
	clearInterval(idealTimeOut);
	if(inactivityTimeout) { 
		clearTimeout(inactivityTimeout);
		inactivityTimeout = 0;
	}
	inactivityTimeout = setTimeout(function() {
		//redirectPage();
		if (!jQuery("link[href='css/colorbox.css']").length)
    		jQuery('<link href="css/colorbox.css" rel="stylesheet">').appendTo("head");
		jQuery.getScript("libs/jquery.colorbox-min.js", function( data, textStatus, jqxhr ) {
			jQuery.fn.colorbox({'href':'#inactivityPrompt','inline':true,'open':true,'escKey':true, 'height':300, 'width':400});	
		});
		startIdealTimer();
	}, 540000);
}
function redirectToLogout()
{
	jQuery.ajax({
		type: "POST",
		url: "/mindspark/userInterface/controller.php",
		data: "mode=endSessionType&endType=4&timeSpent="+inactivityTimeout,
		"async": false,
		success: function(msg) {
			tryingToUnloadPage = true;
			//alert("You have been logged out as Mindspark has not detected any input from you in the last 10 minutes. Login again to continue.");
			window.location.href = "/mindspark/userInterface/error.php";
		}
	});
}
function startIdealTimer()
{
	var idealTimer=60;
	jQuery("#idealTimeRemaining").text(idealTimer);
	idealTimeOut = setInterval(function(){
		idealTimer--;
		if(idealTimer==0)
			redirectToLogout();
		else	
			jQuery("#idealTimeRemaining").text(idealTimer);
	},1000);
}


</script>
<?php } ?>
<div style="display:none">
	<div id="inactivityPrompt" style="font-family: 'Conv_HelveticaLTStd-Light';font-size:18px;text-align: center;padding: 34px;">
		Mindspark has not detected any input from you in the last 9 minutes. You will be logged out in <span id="idealTimeRemaining" style="color:#F00;"></span> seconds.<br />
		<div class="buttonSessionContinue" id="buttonSessionContinue" onClick="jQuery.fn.colorbox.close();">Continue</div>
	</div>
</div>
<input type="hidden" name="myStateInput" id="myStateInput" />
</body>
</html>