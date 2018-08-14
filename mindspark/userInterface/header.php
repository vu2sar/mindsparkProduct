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
<?PHP 
} 
?>
<?php

if($_SERVER['HTTP_REFERER'] != '' || $_SERVER["REQUEST_URI"]!= '')
{	
	 
		$pageurl = $_SERVER["REQUEST_URI"];
		$string=$pageurl;	
		$pageurl = explode ('/',$pageurl);
		$pageCounterIndex=count($pageurl)-1;
		//echo $pageCounterIndex;
		$interfaceName=$pageurl[2];
		$pageurl = $pageurl[$pageCounterIndex];
		if (strpos($pageurl,'?') !== false) {
			$pageurl = substr($pageurl, 0, strpos($pageurl, '?'));
		}

		$type="";
		if($interfaceName=='userInterface')
			$type='userInterface';
		
		$query = "SELECT * FROM trackingPageDetails WHERE pageName LIKE '%".$pageurl."%' and pageType='".$type."'";
	    $result = mysql_query($query);

		$l = mysql_fetch_array($result);
		
	    $pageid = $l[0];
		if($pageid)
		{ 
			$query = "insert into trackingUserInterface (userID, sessionID, pageID, lastmodified) values (".$_SESSION['userID'].",".$_SESSION['sessionID'].",$pageid,now())";
			mysql_query($query) or die(mysql_error());
		}
}
$context = isset($_SESSION['context'])?$_SESSION['context']:"India";	
?>
<script type="text/javascript">
/* Same browser - different tabs - same login */
function check_localstorage()
{ 
	var test = 'test';
	try {
		localStorage.setItem(test, test);
		localStorage.removeItem(test);        
		return true;
	} catch(e) {  
		alert("You in incognito mode")  ;	
		return false;
	}
}

function register_tab_GUID()
{
	// detect local storage available
	if(check_localstorage())
	{
		if (typeof (Storage) !== "undefined") 
		{
			// get (set if not) tab GUID and store in tab session
			if (sessionStorage["tabGUID"] == null) sessionStorage["tabGUID"] = tab_GUID();
			var guid = sessionStorage["tabGUID"];
			// add eventlistener to local storage
			window.addEventListener("storage", storage_Handler, false);
			// set tab GUID in local storage
			localStorage["tabGUID"] = guid;
			if(sessionStorage.getItem('sessionID') == null)
			{
				sessionStorage.setItem('sessionID','<?=$_SESSION["sessionID"]?>');
				sessionStorage.setItem('startTime','<?=$_SESSION['__StartTime']?>');
			}
		}
	}
}

function storage_Handler(e) {
    // if tabGUID does not match then more than one tab and GUID
      if(!document.hasFocus()){
	    if (e.key == 'tabGUID') {
	        if (e.oldValue != e.newValue) tab_Warning();
	    }
	}
}

function tab_GUID() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
          .toString(16)
          .substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
      s4() + '-' + s4() + s4() + s4();
}

function tab_Warning() {
	if(typeof setTryingToUnload == "function")
	{
	   setTryingToUnload();
	}
	var oldSessionID = sessionStorage.getItem("sessionID");
	var oldStartTime = sessionStorage.getItem("startTime");
	if(msPageNameArr.indexOf(msPageNameSplit[msPageNameSplit.length - 1]) == -1)  //alert if window name not matching
	{
		window.location.href="/mindspark/userInterface/newTab.php?oldSessionID="+oldSessionID+"&oldStartTime="+oldStartTime;
	}
}
/* window.onload = (function() {	
 	alert("there");
 	if(check_localstorage())
 	{		
 		 register_tab_GUID();
 	}
 
   onLoad();
 });
*/

if($context=="US")
{
	echo "var langType = 'en-us';";
}
else 
{
	echo "var langType = 'en';";
}
</script>
	
	
<meta http-equiv="Content-Type" content="text/html;">
<meta name="viewport" content="width=device-width,initial-scale=1" />
<script type="text/javascript" src="/mindspark/userInterface/libs/prompt.js"></script>

<?php

if(isset($_SESSION['blockMindspark']) && $_SESSION['blockMindspark']==1)
	header("Location: /mindspark/userInterface/error.php?mode=4");



function time_elapsed_A($secs){
    $bit = array(
        'y' => $secs / 31556926 % 12,
        'w' => $secs / 604800 % 52,
        'd' => $secs / 86400 % 7,
        'h' => $secs / 3600 % 24,
        'm' => $secs / 60 % 60,
        's' => $secs % 60
        );
        
    foreach($bit as $k => $v)
        if($v > 0)$ret[] = $v;
        
    return join(' ', $ret);
    }
	date_default_timezone_set('Asia/Calcutta');
	$date = new DateTime($_SESSION['sessionStartTime']);
	$time1 = $date->getTimestamp();
	$nowtime = time();
	$oldtime = $time1;

	$timeShow = time_elapsed_A($nowtime-$oldtime);
	$timeShow = explode(" ",$timeShow);
	if($timeShow[2]){
		$seconds = $timeShow[2];
		$minutes = $timeShow[1];
		$hours = $timeShow[0];
	}else if($timeShow[1]){
		$seconds = $timeShow[1];
		$minutes = $timeShow[0];
		$hours = 0;
	}else{
		$seconds = $timeShow[0];
		$minutes = 0;
		$hours = 0;
	}
if($_SESSION["userType"]!="teacherAsStudent" && $_SESSION["userType"]!="msAsStudent" && $_SESSION['admin']!="School Admin" && $_SESSION['admin']!="TEACHER") { 
	if(isset($_SESSION['sessionID']) && $_SESSION['sessionID']!="")
	{
		$sq	=	"SELECT logout_flag FROM adepts_sessionStatus WHERE sessionID=".$_SESSION['sessionID'];
		$rs	=	mysql_query($sq);
		$rw	=	mysql_fetch_assoc($rs);
		if($rw['logout_flag']==1)
			header("Location: /mindspark/userInterface/error.php");
	}
    $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
	
?>
<script>

//var msPageNameArr	=	new Array("removeOtherSession.php","forgotPassword.php","picturePassword.php","browser_detect.php","error.php");   //pages where we allow multitab
var msPageNameArr	=	new Array("removeOtherSession.php","browser_detect.php","validateLogin.php","error.php","revisionSessionQuestion.php","revisionSessionInstructions.php","revisionSessionReport.php","forgotPassword.php");   //pages where we allow multitab
var msPageName	=	location.pathname;				//page url
var msPageNameSplit	=	msPageName.split("/");			//spliting to get page name

try {

	<?php if(isset($_SESSION["windowName"]) && $_SESSION["windowName"]!="")
	 { ?>
	 // 	var newwindowName	=   <?=$_SESSION["windowName"];?>;  		
		// if(windowName != '' && (typeof  windowName != 'undefined'))
		// {	
		// 	if(newwindowName != windowName)
		// 	{
		// 		window.location.href="/mindspark/userInterface/newTab.php";
		// 	}
		// }
		
	var windowName	=   <?=$_SESSION["windowName"];?>;  //set window name - coming from session controller.php
	<?php } else { ?>
	var windowName	=	'';  //set window name - coming from session controller.php
	<?php } ?>

	var currentWindowName = "";
	if(window.sessionStorage)
	{
		if(sessionStorage.getItem('windowName') == null)
			currentWindowName = 0;
		else	
			currentWindowName=parseInt(sessionStorage.getItem('windowName'));
	}
	else
		currentWindowName=parseInt(window.name);
	var msPageNameArr	=	new Array("removeOtherSession.php","browser_detect.php","validateLogin.php","error.php","revisionSessionQuestion.php","revisionSessionInstructions.php","revisionSessionReport.php","forgotPassword.php");   //pages where we allow multitab
	var msPageName	=	location.pathname;				//page url
	var msPageNameSplit	=	msPageName.split("/");			//spliting to get page name
	if(msPageNameArr.indexOf(msPageNameSplit[msPageNameSplit.length - 1]) == -1 && windowName!="" && !isNaN(currentWindowName))  //alert if window name not matching
	{
		if(currentWindowName != parseInt(windowName))
		{
			//alert("New tab not allowed.");
			var oldSessionID = sessionStorage.getItem("sessionID");
			var oldStartTime = sessionStorage.getItem("startTime");
			window.location.href="/mindspark/userInterface/newTab.php?oldSessionID="+oldSessionID+"&oldStartTime="+oldStartTime;
			
			//window.open('', '_self', ''); 
			//window.close(); 
		}		
	}
	

}
catch(er) {	
}
</script>

<?php } 

/* logic for Same browser - different tabs - different logins */
	

	?>
<script language=javaScript>
var hours= <?php echo json_encode($hours);?>;
 var minutes=<?php echo json_encode($minutes);?>;
 var seconds=<?php echo json_encode($seconds);?>;
 var hours1=0,minutes1=0,seconds1=0;
function clockon() {
 thistime= new Date();
 seconds++;
 if(seconds==60){
 	seconds = 0;
	minutes++;
 }
 if(minutes==60){
 	minutes = 0;
	hours++;
 }
 if (eval(hours) <10) {hours1=hours;}
 else{
 	hours1=hours;
 }
 if (eval(minutes) < 10) {minutes1="0"+minutes;}
  else{
 	minutes1=minutes;
 }
 if (seconds < 10) {seconds1="0"+seconds;}
  else{
 	seconds1=seconds;
 }
 thistime = hours1+":"+minutes1;
 bgclocknoshade.innerHTML=thistime;
 var timer=setTimeout("clockon()",1000);
}

/*window.onload = init1;

function init1(){
	clockon();
}*/
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
            //jQuery("#promptBox").css({ 'margin-top': pagecenterH - 130 + 'px', 'margin-left': pagecenterW - 175 + 'px' });
        }

    </script>
  

<?php }

if(isset($_SESSION["limitExceeded"]) && $_SESSION["limitExceeded"]!="")
{
	$requestUri = $_SERVER["REQUEST_URI"];
	if(strpos($requestUri,"endSessionReport.php") === false && strpos($requestUri,"dashboard.php") === false && strpos($requestUri,"sessionWiseReport") === false && strpos($requestUri,"topicWiseQuesTrail.php") === false && strpos($requestUri,"studentTopicReport.php") === false && strpos($requestUri,"quesWiseReport.php") === false)
	{
		$nextPage = "/mindspark/userInterface/endSessionReport.php?mode=".$_SESSION["limitExceeded"];
		header("location:$nextPage");
	}
}

if(strcasecmp($_SESSION['admin'],"STUDENT")!=0 && strcasecmp($_SESSION['admin'],"GUEST")!=0 && $_SESSION["userType"]!="msAsStudent" && $_SESSION["userType"]!="teacherAsStudent")
{ 
?>
	<script type="text/javascript">
	var msPageNameArr	=	new Array("removeOtherSession.php","browser_detect.php","validateLogin.php","error.php","revisionSessionQuestion.php","revisionSessionInstructions.php","revisionSessionReport.php","forgotPassword.php");   //pages where we allow multitab
	var msPageName	=	location.pathname;				//page url
	var msPageNameSplit	=	msPageName.split("/");			//spliting to get page name
	if(msPageNameArr.indexOf(msPageNameSplit[msPageNameSplit.length - 1]) == -1)  //alert if window name not matching
	{
		var oldSessionID = sessionStorage.getItem("sessionID");
		var oldStartTime = sessionStorage.getItem("startTime");
		window.location.href="/mindspark/userInterface/newTab.php?oldSessionID="+oldSessionID+"&oldStartTime="+oldStartTime;
	</script>
<?php }
?>

<script type="text/javascript">
/*duplicate tab logic */
document.addEventListener('DOMContentLoaded', function () {
	var body = document.getElementsByTagName("body")[0];	 
	body.addEventListener("load", onLoad(), false);
	body.addEventListener("load", register_tab_GUID(), false);
	 
});

function onLoad()
{	
	if(document.getElementById("myStateInput").value == '')
	{
		document.getElementById("myStateInput").value = 'already loaded';
	}
	else
	{
		if(typeof setTryingToUnload == "function")
	    {
		   setTryingToUnload();
		}
		if(msPageNameArr.indexOf(msPageNameSplit[msPageNameSplit.length - 1]) == -1)  //alert if window name not matching
		{
			var oldSessionID = sessionStorage.getItem("sessionID");
			var oldStartTime = sessionStorage.getItem("startTime");
			window.location.href="/mindspark/userInterface/newTab.php?oldSessionID="+oldSessionID+"&oldStartTime="+oldStartTime;
		}
	}	

$(window).on('beforeunload', function() // Back or Forward buttons
{
   document.getElementById("myStateInput").value =''; // Blank the state out.
});
}


</script>