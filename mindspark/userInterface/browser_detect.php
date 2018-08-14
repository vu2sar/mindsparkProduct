<?php
set_time_limit(0);   //Otherwise quits with "Fatal error: Maximum execution time of 30 seconds exceeded"

@include("check1.php");
include("constants.php");
include("classes/clsUser.php");
$userID = $_SESSION['userID'];
$objUser = new User($userID);
$childClass = $objUser->childClass;
$childSection = $objUser->childSection;
$category = $objUser->category;
$childName = explode(" ",$objUser->childName);
$childName = $childName[0];
$s3BucketLink = IMAGES_FOLDER;
if(isset($_GET['image1'])){
    $image1 = $_GET['image1'];
}

if(isset($_GET['image2'])){
    $image2 = $_GET['image2'];
}

if(isset($_POST['continueTeacher']))
{
	/*if (isset($_REQUEST['from']) && ($_REQUEST['from']=="link"))
	{
		echo "<script language='JavaScript'>history.go(-2)</script>";
	}
	else
	{*/
		$_SESSION['browserColor']=$_REQUEST['browserColor'];
		$browser = $_REQUEST['browser'];
		$browser1=explode(",",$browser);
		$_SESSION['browser']=$browser1[0];
		$hasFlash = $_REQUEST['hasFlash'];
		$sq	=	"UPDATE adepts_teacherInterfaceScreen SET interfaceFlag=0 WHERE userID=$userID";
		$rs	=	mysql_query($sq);
		echo "<script language='JavaScript'>window.location='controller.php?mode=saveBrowser&browser=".$browser."&hasFlash=".$hasFlash."'</script>";
		//window.location='controller.php?mode=login'
	//}
}

if(isset($_POST['continue']))
{
	/*if (isset($_REQUEST['from']) && ($_REQUEST['from']=="link"))
	{
		echo "<script language='JavaScript'>history.go(-2)</script>";
	}
	else
	{*/
		
		$_SESSION['browserColor']=$_REQUEST['browserColor'];
		$browser = $_REQUEST['browser'];
		$browser1=explode(",",$browser);
		$_SESSION['browser']=$browser1[0];
		$hasFlash = $_REQUEST['hasFlash'];
		echo "<script language='JavaScript'>window.location='controller.php?mode=saveBrowser&browser=".$browser."&hasFlash=".$hasFlash."'</script>";
		//window.location='controller.php?mode=login'
	//}
}

?>

<?php include("header.php"); ?>
<title>Browser Detection</title>
<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/generic/lowerClass.css?ver=2" rel="stylesheet" type="text/css">
<?php } else if($theme==2){ ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/generic/midClass.css" />
<?php } else if($theme==3){ ?>
    <link rel="stylesheet" href="css/commonHigherClass.css" />
    <link rel="stylesheet" href="css/generic/higherClass.css" />
<?php } ?>

<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<!--<script type="text/javascript" src="libs/i18next.js"></script>-->
<!--<script type="text/javascript" src="libs/translation.js"></script>-->
<script type="text/javascript" src="libs/brwsniff.js?ver=10"></script>
<script type="text/javascript" src="libs/AC_OETags1.js"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<!--<script type="text/javascript" src="libs/closeDetection.js"></script>-->
<script type="text/javascript" src="/mindspark/userInterface/libs/combined.js"></script>

<script>
    var langType = '<?=$language;?>';
	var category = <?php echo json_encode($category); ?>;
	var blinkTimer = null;
	var blinkCount = 0;
	var blinkDivID = "blinkMsg";
	var timestamp = new Date().getTime();//for image tag...
		
    function load(){
<?php if($theme==1) { ?>
	var a= window.innerHeight - (130);
	$('#pnlContainer').css("height",a+"px");
<?php } else if($theme==2) { ?>
	var a= window.innerHeight - (125);
	if(window.innerHeight<600){
		var a= window.innerHeight - (515);
	}
	$('#pnlContainer').css("height",a+"px");
<?php } else if($theme==3) { ?>
			var a= window.innerHeight - (125);
			var b= window.innerHeight - (610);
			$('#pnlContainer').css({"height":a+"px"});
			$('#topicInfoContainer').css({"height":a+"px"});
		<?php } ?>
		
		/*$("#click").click(function (){
                //$(this).animate(function(){
                    $('#pnlContainer,body,html').animate({
                        scrollTop: $("#browserMessage").offset().top
                    }, 500);
                //});
            });*/
    
}


var screenResolution = screen.width+"X"+screen.height + " || " + getViewPortSize();
var browser  = new Array();

<?php
	$query = "SELECT browser,category FROM adepts_browserSupport";
	$result = mysql_query($query) or die(mysql_error());
	while($line = mysql_fetch_array($result))
	{
		echo "browser['$line[0]'] = new Array('$line[1]');\r\n";
	}
?>

function logoff()
{
	setTryingToUnload();
    window.location = "logout.php";
}
function blinkFunc()
{
	blinkCount++;
	$('#'+blinkDivID).fadeOut().fadeIn();
	if(blinkCount < 4)
		blinkTimer = window.setTimeout(blinkFunc,750);	
	else
	{
		blinkCount = 0;	
		blinkDivID = "";
	}
}

function openInstructionBox(){
	$.fn.colorbox({'href':'#MT4','inline':true,'open':true,'escKey':true, 'height':570, 'width':750});
}

function openInstructionBox1(){
	$.fn.colorbox({'href':'#MT5','inline':true,'open':true,'escKey':true, 'height':320, 'width':550});
}
function storageEnabled() { 
	//return false;

	//if (!window.sessionStorage) return false;
	if (!window.localStorage) return false;
    try {
        localStorage.setItem("__test", "data");
    } catch (e) {
        if (/QUOTA_?EXCEEDED/i.test(e.name)) {
            return false;
        }
    }
    return true;
}

function getViewPortSize()
{
    var viewportwidth;
    var viewportheight;

    //Standards compliant browsers (mozilla/netscape/opera/IE7)
    if (typeof window.innerWidth != 'undefined')
    {
        viewportwidth = window.innerWidth,
        viewportheight = window.innerHeight
    }

    // IE6
    else if (typeof document.documentElement != 'undefined'
    && typeof document.documentElement.clientWidth !=
    'undefined' && document.documentElement.clientWidth != 0)
    {
        viewportwidth = document.documentElement.clientWidth,
        viewportheight = document.documentElement.clientHeight
    }

    //Older IE
    else
    {
        viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
        viewportheight = document.getElementsByTagName('body')[0].clientHeight
    }

    return viewportwidth + "X" + viewportheight;
}

</script>
<style>
.back {
	margin:0;
	font-family             :       Helvetica, Arial, sans-serif;
    font-size               :       12px;
}
</style>
</head>
<body id="body" onLoad="load()" onResize="load();" class="translation" >
<input type="hidden" id="packageType" value="<?=$objUser->packageType?>">
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
		<div id="pnlContainer" style="overflow:auto;">
			<div id="checkMessage">
			</div>
            <div id="formContainer">
<script>
blinkFunc();
var br=new Array(4);
var os=new Array(2);
var browserCheck;
var fullstr = getFullUAString();
br=getBrowser();

os=getOS();
jsver = jsVersion();
var ua = navigator.userAgent.toLowerCase();
var isiPad = ua.match(/ipad/i) != null;
var isAndroid = ua.indexOf("android") > -1;
var isiPhone = ua.match(/iphone/i) != null;
var someVarName = 0;
if (storageEnabled())
{
	localStorage.setItem("slowWrongCount", someVarName);
	localStorage.setItem("blockSubmit",someVarName);
	localStorage.setItem("globalResult","");
	localStorage.setItem("resetPrompt",someVarName);
	localStorage.setItem("previousPrompted",someVarName);
	localStorage.setItem("captureReset",someVarName);
}
if (storageEnabled()) localStorage.setItem("toughDisabled", 'false');
if (storageEnabled()) localStorage.setItem("toughInstances", 0);
    
// Variable set for fast questions answered in common script of questions page
if(localStorage.getItem("sessionID") && localStorage.getItem("sessionID") !=0 && storageEnabled())
{
	$.post("errorLog.php","params=Slow internet connection error at the time of relogin&sessionID=" + localStorage.getItem("sessionID")+"&qno=" + localStorage.getItem("qno")+"&errorTypee=" + localStorage.getItem("errorType") + "&qcode=" + localStorage.getItem("qcode") + "&typeErrorLog=slow internet connection",function(data) { 
		
	});
}
if(storageEnabled())
{
	localStorage.setItem("sessionID",someVarName);
	localStorage.setItem("qcode",someVarName);
	localStorage.setItem("qno",someVarName);
	localStorage.setItem("errorType",someVarName);
}

var os_met = "correctAnsIcon";
var os_critical = "-";
var os_critical_level = "not";

var browser_met = "";
var browser_critical = "";
var browser_critical_level = "";
var browser_link = "";

function detectIE() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
       // Edge (IE 12+) => return version number
       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    // other browser
    return false;
}
if(br[0]=="Mozilla Firefox" || br[0]=="Google Chrome" || br[0]=="Safari")
{
	//alert("mozilla");
	//browser_met = "images/correctAnsIcon.gif";
	browser_met = "correctAnsIcon";
	browser_critical = "-";
	browser_critical_level = "not";
	browser_link = "";
}
// else if((br[0]=="Internet Explorer")&&(br[1]>=6))
// {
// 	if ((br[0]=="Internet Explorer")&&(br[1]<9))
// 	{
// 		//browser_met = "images/wrong.gif";
// 		browser_met = "wrongAnsIcon";
// 		browser_critical = "-";
// 		browser_critical_level = "nonsevere";
// 		browser_link = "";
// 	}
// 	else
// 	{
// 		//browser_met = "images/correctAnsIcon.gif";
// 		browser_met ="correctAnsIcon";
// 		browser_critical = "-";
// 		browser_critical_level = "not";
// 		browser_link = "";
// 	}
// }
else if( detectIE() >= 6)
{
		if (detectIE()<9)
	{
		//browser_met = "images/wrong.gif";
		browser_met = "wrongAnsIcon";
		browser_critical = "-";
		browser_critical_level = "nonsevere";
		browser_link = "";
	}
	else
	{
		//browser_met = "images/correctAnsIcon.gif";
		browser_met ="correctAnsIcon";
		browser_critical = "-";
		browser_critical_level = "not";
		browser_link = "";
	}
}
else
{
	
	// if ((br[0]=="Internet Explorer")&&(br[1]<10))
	// {
	// 	//browser_met = "images/wrong.gif";
	// 	browser_met = "correctAnsIcon";
	// 	browser_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
	// 	browser_critical_level = "severe";
	// 	browser_link = "http://www.microsoft.com/windows/downloads/ie/getitnow.mspx";
	// }
	if(detectIE() < 10)
	{
		browser_met = "correctAnsIcon";
		browser_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
		browser_critical_level = "severe";
		browser_link = "http://www.microsoft.com/windows/downloads/ie/getitnow.mspx";
	}
	else
	{
		//browser_met = "images/wrongAnsIcon.gif";
		browser_met = "wrongAnsIcon";
		browser_critical = "-";
		browser_critical_level = "nonsevere";
		browser_link = "http://www.microsoft.com/windows/downloads/ie/getitnow.mspx";
	}

	//alert(br[1]);
}
var flashVersion = "";

var hasFlash = "";
var versionStr = GetSwfVer();
var packageType = document.getElementById("packageType").value;

if (versionStr == -1)
{
	flashVersion = "false";
	hasFlash = "false";
}
else if (versionStr != 0)
{
	if(isIE && isWin && !isOpera)
	{
		// Given "WIN 2,0,0,11"
		tempArray         = versionStr.split(" "); 	// ["WIN", "2,0,0,11"]
		tempString        = tempArray[1];			// "2,0,0,11"
		versionArray      = tempString.split(",");	// ['2', '0', '0', '11']
	}
	else
	{
		versionArray      = versionStr.split(".");
	}

	var versionMajor      = versionArray[0];
	var versionMinor      = versionArray[1];
	var versionRevision   = versionArray[2];
	hasFlash = "true";
	flashVersion = ""+versionMajor+"."+versionMinor+"."+versionRevision+"";
}


//var java_met = (jsver>=1.0)?"images/correctAnsIcon.gif":"images/wrongAnsIcon.gif";
var java_met = (jsver>=1.0)?"correctAnsIcon":"wrongAnsIcon";
var java_critical = (jsver>=1.0)?"-":"-";

var cookies = "";
var cookie_met = "";
var cookie_critical = "";
var cookie_critical_level = "";

if (navigator.cookieEnabled == 0)
{
	//alert("You need to enable cookies for this site to load properly!")
	cookies = "Disabled";
	//cookie_met = "images/wrongAnsIcon.gif";
	cookie_met = "wrongAnsIcon";
	cookie_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
	cookie_critical_level = "severe";
}
else
{
	cookies = "Enabled";
	//cookie_met = "images/correctAnsIcon.gif";
	cookie_met = "correctAnsIcon";
	cookie_critical = "-";
	cookie_critical_level = "not";
	//document.Form1.cookieexists.value ="true"
}
var localStorages = "";
var localStorage_met = "";
var localStorage_critical = "";
var localStorage_critical_level = "";

if (!storageEnabled())
{
	//alert("You need to enable localStorages for this site to load properly!")
	localStorages = "Disabled";
	//localStorage_met = "images/wrongAnsIcon.gif";
	localStorage_met = "wrongAnsIcon";
	localStorage_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
	localStorage_critical_level = "severe";
}
else
{
	localStorages = "Enabled";
	//localStorage_met = "images/correctAnsIcon.gif";
	localStorage_met = "correctAnsIcon";
	localStorage_critical = "-";
	localStorage_critical_level = "not";
	//document.Form1.cookieexists.value ="true"
}

//for image tag...
var images = "Images loaded";
var images_met = "correctAnsIcon";
var images_critical = "-";
var images_critical_level = "not";
var imageAdd="<td>Images</td><td>Checking...</td><td>Checking...</td><td align=\'center\'>Checking...</td><td>Checking...</td>";
var imageCounter=1;
var imageCounter1=1;
var image1=0;
var image2=0;
var imageCheck=0;
var interval;
var imageCheckCounter1=0;
var imageControllerCheck=<?php echo json_encode($image1); ?>;
var imageControllerCheck1=<?php echo json_encode($image2); ?>;
var image= new Image();
var image3= new Image();

if(imageControllerCheck=="" || imageControllerCheck1=="") {
	$("#body").css("display","none");
	var imageSource="<?=SPARKIE_IMAGE_SOURCE;?>";
	var imageSource1="<?=SPARKIE_IMAGE_SOURCE_S3;?>";
	image.src= imageSource+'/sparkyForImageCheck.png?'+timestamp;
	image.onload= function(){
		image1=1;
		if(image2!=0){
			images = "Images loaded";
			images_met = "correctAnsIcon";
			images_critical = "-";
			images_critical_level = "not";
			var macCheck = os[0]+" "+os[1];
			if (macCheck.indexOf('Mac OS  X ') > -1) 
				{
	imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\">N/A</td>";
}else{
			imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\"><div class='"+images_met+"'></div></td><td>"+images_critical+"</td>"; 
}
			$("#imagesCheck").html(imageAdd);
			if(critical_level=="nonsevereimg"){
				$("#body").css("display","none");
			var browser = br[0]+" "+br[1];
			if(hasFlash)
				browser += ", Flash:"+flashVersion;
				browser += ", OS: "+os[0]+" "+os[1];
				setTryingToUnload();
				window.location="controller.php?mode=saveBrowser&browser="+browser+" ~ "+screenResolution+"&hasFlash="+hasFlash;
			}
			else{
				$("#body").css("display","block");
			}
		}
	};
	image.onerror= function errorImage(){
		$("#body").css("display","block");
		imageCounter++;
		if(imageCounter<=5){
			var timestamp = new Date().getTime();
			image.src= imageSource+'/sparkyForImageCheck.png?'+timestamp;
			setTimeout(function(){errorImage();},500);
		}
		else{
			image1=2;
			if(image2!=0){
				images = "Images not loaded";
				images_met = "wrongAnsIcon";
				images_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
				images_critical_level = "severe";
				var imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\"><div class='"+images_met+"'></div></td><td>"+images_critical+"</td>";
				$("#imagesCheck").html(imageAdd);
				$("#formContainer").css("display","block");
				$("#messageForBank1").css("display","block");
			}
		}
	};
	image3.src= imageSource1+'/sparkyForImageCheck.png?'+timestamp;
	image3.onload= function(){
		image2=1;
		if(image1!=0){
			images = "Images loaded";
			images_met = "correctAnsIcon";
			images_critical = "-";
			images_critical_level = "not";
			imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\"><div class='"+images_met+"'></div></td><td>"+images_critical+"</td>";
			$("#imagesCheck").html(imageAdd);
			if(critical_level=="nonsevereimg"){
				$("#body").css("display","none");
			var browser = br[0]+" "+br[1];
			if(hasFlash)
				browser += ", Flash:"+flashVersion;
			browser += ", OS: "+os[0]+" "+os[1];
			setTryingToUnload();
			window.location="controller.php?mode=saveBrowser&browser="+browser+" ~ "+screenResolution+"&hasFlash="+hasFlash;
			}
			else{
				$("#body").css("display","block");
			}
		}
	};
	image3.onerror= function errorImage(){
		$("#body").css("display","block");
		imageCounter1++;
		if(imageCounter1<=5){
			var timestamp = new Date().getTime();
			image3.src= imageSource1+'/sparkyForImageCheck.png?'+timestamp;
			setTimeout(function(){errorImage();},500);
		}else{
			image2=2;
			if(image1!=0){
				images = "Images not loaded";
				images_met = "wrongAnsIcon";
				images_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
				images_critical_level = "severe";
				var imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\"><div class='"+images_met+"'></div></td><td>"+images_critical+"</td>";
				$("#imagesCheck").html(imageAdd);
				$("#formContainer").css("display","block");
				$("#messageForBank2").css("display","block");
			}
		}
	};
}
else{
	if(imageControllerCheck=="loaded" && imageControllerCheck1=="loaded"){
		images = "Images loaded";
		images_met = "correctAnsIcon";
		images_critical = "-";
		images_critical_level = "severe";
		imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\"><div class='"+images_met+"'></div></td><td>"+images_critical+"</td>";
		$("#formContainer").css("display","block");
		$("#body").css("display","block");
	}else{
		images = "Images not loaded";
		images_met = "wrongAnsIcon";
		images_critical = "Severe<font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>";
		images_critical_level = "severe";
		var imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\"><div class='"+images_met+"'></div></td><td>"+images_critical+"</td>";
		$("#formContainer").css("display","block");
	}
}

var canContinue = "";
var critical_level = "";
if((browser_critical_level=="severe") || (cookie_critical_level=="severe") || (localStorage_critical_level=="severe"))
{
	//alert("yes severe error");
	canContinue="false";
	critical_level="severe";
}
else if((os_critical_level=="nonsevere") || (browser_critical_level=="nonsevere") || (cookie_critical_level=="nonsevere") || (localStorage_critical_level=="nonsevere"))
{
	//alert("YES NON CRITICAL ERROR");
	canContinue="false";
	critical_level="nonsevere";
}
else
{
	canContinue="true";
	critical_level="not";
}

if(!isiPad && !isiPhone && !isAndroid){
	if(getMajorVersion(br[1])>=534){
		canContinue = "true";
	}
}

function redirect(link)
{
	<?php //include("logout.php"); ?>
	setTryingToUnload();
	window.location=""+link+"";
}

var currentBrowser = br[0]+" "+getMajorVersion(br[1]);
if(!browser[currentBrowser]){ 
	// if((br[0]=="Internet Explorer" && getMajorVersion(br[1]) >10) || (br[0]=="Mozilla Firefox" && getMajorVersion(br[1]) >26) || (br[0]=="Google Chrome" && getMajorVersion(br[1]) >26)){		
		if((detectIE() >10) || (br[0]=="Mozilla Firefox" && getMajorVersion(br[1]) >26) || (br[0]=="Google Chrome" && getMajorVersion(br[1]) >26)){
		var browserColor="green";
	}else{
		var browserColor="white";
	}

}
else{
	var browserColor=browser[currentBrowser];
}
var macCheck = os[0]+" "+os[1];
if (macCheck.indexOf('Mac OS  X ') > -1) {
	if(br[0]=="Safari"){
			browserColor="green";
	}
}
if(isAndroid ||isiPad){
	if(br[0]=="Safari"){
			browserColor="green";
	}
}
if((br[0]=="Safari")&&((browser_critical_level=="severe") || (cookie_critical_level=="severe") || (localStorage_critical_level=="severe")))
{
	//alert("yes severe error");
	canContinue="false";
	critical_level="severe";
	browserColor="red";
}
if(browserColor == "green"){
	
}
else if(browserColor == "white"){
	critical_level="nonsevere";
	browser_met = "wrongAnsIcon";
	browserCheck=1;
	
}else if(browserColor == "red"){
	critical_level="severe";
	browser_met = "wrongAnsIcon";
	canContinue = "false";
	browserCheck=2;
	
}

if(isAndroid ||isiPad){
	if(critical_level=="not"){
		critical_level="not";
	}else{
		critical_level="nonsevere";
		browserCheck=3;
	}
}

/*var macCheck = os[0]+" "+os[1];
if (macCheck.indexOf('Mac OS  X ') > -1) {
	critical_level="nonsevere";
	browser_met = "wrongAnsIcon";
	canContinue = "true";
	browserCheck=1;
	os_met = "wrongAnsIcon";
	images_critical_level= "";
	imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\">N/A</td>";
}*/

if(browserCheck==2){
	var checkMsg ="<span style='font-weight:bold'><font color='red'>You are using an unsupported browser ("+br[0]+" "+br[1]+"). Old browsers are slow, more open to virus attacks and do not support many of the important features of Mindspark.</font></span><b><br/><br/><div style='color:white;background:red;width:70%;margin-left:15%;'>YOU WILL HAVE TO UPGRADE YOUR BROWSER TO CONTINUE USING MINDSPARK.</div><br/><div id='blinkMsg' style='color:green'>Upgrading your browser is free and easy. Please click <a href='recommendedBrowsers.php' target='_blank' style='text-decoration:underline;color:blue;cursor:pointer;font-size:1.2em;'>here</a> to upgrade to your browser.</div></b>";
	if (br[0]=="Safari" && ((cookie_critical_level=="severe") || (localStorage_critical_level=="severe")))
		checkMsg ="<span style='font-weight:bold'><font color='red'>You may be trying to access Mindspark in the private browsing mode.</font></span><b><br/><br/><div style='color:white;background:red;width:70%;margin-left:15%;'>Please disable private browsing to continue using Mindspark.</div>";
	$("#checkMessage").html(checkMsg);
	browser_critical="severe";
}

else if(browserCheck==1){
	var checkMsg ="<span style='font-weight:bold'><font color='red'>You are using a browser which does not fully support many of the important features of Mindspark.</font></span><b><br/><br/><div id='blinkMsg' style='color:green'>Upgrading your browser is free and easy. Please click <a href='recommendedBrowsers.php' target='_blank' style='text-decoration:underline;color:blue;cursor:pointer;font-size:1.2em;'>here</a> to upgrade to a supported browser.";
	browser_critical="severe";
	if(canContinue == "true"){
		checkMsg+= " You can continue using Mindspark by clicking 'Continue' button and hope for the best!</div></b>";
	}else{
		checkMsg+= "</div></b>";
	}
	$("#checkMessage").html(checkMsg);
}

if(critical_level=="not"){
	if(images_critical_level=="not"){
		critical_level="nonsevereimg";
	}
}

if(imageControllerCheck=="loaded" && imageControllerCheck1=="loaded"){
	if(critical_level=="nonsevereimg"){
		$("#body").css("display","none");
		var browser = br[0]+" "+br[1];
		if(hasFlash)
			browser += ", Flash:"+flashVersion;
		browser += ", OS: "+os[0]+" "+os[1];
		setTryingToUnload();
		window.location="controller.php?mode=saveBrowser&browser="+browser+" ~ "+screenResolution+"&hasFlash="+hasFlash;
	}
}

if(imageControllerCheck=="notloaded"){
	critical_level="nonsevere";
}

if(images_critical_level=="severe"){
	if(critical_level=="severe"){
		imageAdd+="<td>Follow this <a href='assets/MS_firewall_settings_cyberoam.pdf' target='_blank' style='text-decoration:underline;color:blue;'>guide</a> <br/>(For Cyberoam users only)</td>";
	}
}

if(category!="STUDENT" && category!="GUEST"){
		canContinue = "true";
}

if (macCheck.indexOf('Mac OS  X ') > -1) {
	imageAdd="<td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\">N/A</td>";
}
if((br[0]=="Safari")&&((browser_critical_level=="severe") || (cookie_critical_level=="severe") || (localStorage_critical_level=="severe")))
{
	//alert("yes severe error");
	canContinue="false";
	critical_level="severe";
	browserColor="red";
}
var from_link = <?php if (isset($_REQUEST['from']) && ($_REQUEST['from']=="link")) echo "true"; else echo "false"; ?>;
if (from_link || critical_level!="not")
{
	$('body').addClass('back');
	var page = "";
	if(critical_level=="severe") {page += "<center><font color=\"#0000FF\"><b><sup>&#43;</sup></b></font>&nbsp;Certain features are critical for correct usage of Mindspark, please correct them before proceeding.</center>";}
	else if(critical_level=="nonsevere" && browserCheck!=1) {page += "<center><font color=\"#0000FF\"><sup>&#43;</sup></font>&nbsp;Some of the system requirements are not met. You may still proceed ahead, but we recommend you to rectify the same.</center>";}
	else if(browserCheck!=1){page += "<center>Your system configuration against our requirement is as follows.</center>";}
    page += "<form name=\"frmBrowserDetect\" method=\"POST\">";
	page += "<div id='dataTableDiv'><table align=\"center\" cellpadding=\"3\" valign=\"top\" cellspacing=\"0\" border=\"0\" width=\"80%\" class='tblContent'>";
	page += "<tr class='trHead'><td>Requirement</td><td>Recommended</td><td>System Status</td><td>Meets Requirement</td><td>Criticality</td>"+(critical_level=="severe"?"<td>Steps to Rectify</td>":" ")+"</tr>";
	if(macCheck.indexOf('Mac OS  X ') > -1)
		page += "<tr><td>Operating System</td><td>Any Operating System</td><td>"+os[0]+" "+os[1]+"</td><td align=\"center\"><span style='font-size:10px;margin-left:20px;position:relative;'><font color=\"#0000FF\">#</font</span><div class='"+os_met+"'></div></td><td>"+os_critical+"</td>"+(critical_level!="severe"?" ":"<td>-</td>")+"</tr>";
	else
		page += "<tr><td>Operating System</td><td>Any Operating System</td><td>"+os[0]+" "+os[1]+"</td><td align=\"center\"><div class='"+os_met+"'></div></td><td>"+os_critical+"</td>"+(critical_level!="severe"?" ":"<td>-</td>")+"</tr>";
	if(macCheck.indexOf('Mac OS  X ') > -1)
		page += "<tr><td>Browser</td><td>Internet Explorer 9+ / Mozilla Firefox 18+/ Chrome 27+</td><td>"+br[0]+" "+br[1]+"</td><td align=\"center\">N/A</td><td>"+browser_critical+"</td>"+(critical_level!="severe"?" ":(browser_critical=="severe"?"<td><a target='_blank' href='recommendedBrowsers.php'>Upgrade/Install</a></td>":"<td>-</td>"))+"</tr>";
	else if(browserCheck!=3)
		page += "<tr><td>Browser</td><td>Internet Explorer 9+ / Mozilla Firefox 18+/ Chrome 27+</td><td>"+br[0]+" "+br[1]+"</td><td align=\"center\"><div class='"+browser_met+"'></div></td><td>"+browser_critical+"</td>"+(critical_level!="severe"?" ":(browser_critical=="severe"?"<td><a target='_blank' href='recommendedBrowsers.php'>Upgrade/Install</a></td>":"<td>-</td>"))+"</tr>";
	else page += "<tr><td>Browser</td><td>Internet Explorer 9+ / Mozilla Firefox 18+/ Chrome 27+/ Safari</td><td>"+br[0]+" "+br[1]+"</td><td align=\"center\"><div class='"+browser_met+"'></div></td><td>"+browser_critical+"</td>"+(critical_level!="severe"?" ":(browser_critical=="severe"?"<td><a target='_blank' href='recommendedBrowsers.php'>Upgrade/Install</a></td>":"<td>-</td>"))+"</tr>";

	page += "<tr><td>Javascript</td><td>Javascript Engine version 1.0 or higher</td><td>"+jsver+"</td><td align=\"center\">";
	if(macCheck.indexOf('Mac OS  X ') > -1)
	{
		page +="N/A";
	}
	else
	{
		page +="<div class='"+java_met+"'></div>";
	}
	page +="</td><td>"+java_critical+"</td>"+(critical_level!="severe"?" ":(jsver>=1.0)?"<td>-</td>":"<td>Please Upgrade Java</td>")+"</tr>";
	page += "<tr><td>Cookies</td><td>Should be enabled</td><td>"+cookies+"</td><td align=\"center\">";
	if(macCheck.indexOf('Mac OS  X ') > -1)
	{
		page +="N/A";
	}
	else
	{
		page +="<div class='"+cookie_met+"'></div>";
	}
	page +="</td><td>"+cookie_critical+"</td>"+(critical_level!="severe"?"":(cookie_critical_level=="severe"?"<td>Please Enable cookies</td>":"<td>-</td>"))+"</tr>";
	page += "<tr><td>Local Storage</td><td>Should be enabled</td><td>"+localStorages+"</td><td align=\"center\">";
			page +="<div class='"+localStorage_met+"'></div>";
	page +="</td><td>"+localStorage_critical+"</td>"+(critical_level!="severe"?"":(localStorage_critical_level=="severe"?"<td>Please Enable Local Storage</td>":"<td>-</td>"))+"</tr>";


	if(macCheck.indexOf('Mac OS  X ') > -1)
		{
		
		page +="<tr id='imagesCheck' valign='top'><td width='15%'>Image check</td><td>Firewall settings should allow Mindspark image access and internet speed should be ok.<br/><b onclick='openInstructionBox1()' style='text-decoration:underline;cursor:pointer;color:blue;font-size:12px;'>(what is this?)</b><br/></td><td>"+images+"</td><td align=\"center\">N/A</td></tr>";
		}
	else{
		page += "<tr id='imagesCheck' valign='top'>"+imageAdd+"</tr>";//for image tag...
	}
	var storageTestKey = 'test';
	var storage = window.sessionStorage;
	var privateMode = false;
	try {
	  storage.setItem(storageTestKey, 'test');
	  storage.removeItem(storageTestKey);
	} catch (e) {
	  if (e.code === DOMException.QUOTA_EXCEEDED_ERR && storage.length === 0) {
	    privateMode=true;
	  } else {
	    
	  }
	}
	if (privateMode)
		page += "<tr><td colspan=\"6\" align='center'>Mindspark does not work in the PRIVATE BROWSING mode. Please disable PRIVATE BROWSING to continue Mindspark.</td></tr>";
	if(category!="STUDENT" && category!="GUEST"){
		page += "<tr><td colspan=\"6\" align='center'><input type=\"button\" name=\"logout\" value=\"Logout\" class=\"buttonDetect\" onclick=\"javascript:setTryingToUnload();window.location='logout.php'\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"continueTeacher\" class=\"buttonDetect\" onClick=\"setTryingToUnload()\" value=\"Continue\" class=\"button\" "+((!privateMode && canContinue=="true")?"enabled":"disabled")+"></td></tr>";
	}else{
		page += "<tr><td colspan=\"6\" align='center'><input type=\"button\" name=\"logout\" value=\"Logout\" class=\"buttonDetect\" onclick=\"javascript:setTryingToUnload();window.location='logout.php'\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"continue\" class=\"buttonDetect\" value=\"Continue\" onClick=\"setTryingToUnload()\" class=\"button\" "+((!privateMode && canContinue=="true")?"enabled":"disabled")+"></td></tr>";
	}
	page += "<tr><td colspan=\"6\" align='center'>";
	page += "<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">";
	/*if ((br[0]=="Internet Explorer")&&(br[1]>=8)) {page += "<tr><td><sup><font color=\"#FF0000\">*</font></sup>&nbsp;<big>Internet Explorer 9 is preferred.</big></td></tr>";}*/
	if (browserCheck==1) {page += "<tr><td style='border:1px solid black;font-size:14px;' align='center'><font color=\"#FF0000\">CONTINUE USING MINDSPARK,SOME FEATURES MAY NOT WORK PERFECTLY</font></td></tr>";}
	if ((os[0]=="Mac OS")||(os[0]=="Mac Classic") && (!isiPad)) {page += "<tr><td><sup><font color=\"#0000FF\">#</font></sup>&nbsp;Mindspark has not been officially tested on Mac, but users have tried it and it works. Please report problems if you encounter issues on Mac.</td></tr>";}
	//page += "<tr><td><sup><font color=\"#FF0000\">*</font></sup>&nbsp;Mindspark on Google Chrome is under Beta testing. We encourage users to use Google Chrome and email us at <a href=\"mailto:mindspark@ei-india.com?subject=Problem Encountered on Google Chrome\">mindspark@ei-india.com</a> if any problems are encountered.</td></tr>";
	page += "</table>";
	page += "</td></tr>";
	page += "</table></div>";

	page += "<input type=\"hidden\" name=\"browser\" id=\"browser\" value=\""+br[0]+" "+br[1]+", Flash:"+flashVersion+", OS: "+os[0]+" "+os[1]+" ~ "+screenResolution+"\">";
	page += "<input type=\"hidden\" name=\"hasFlash\" id=\"hasFlash\" value=\""+hasFlash+"\">";
	page += "<input type=\"hidden\" name=\"browserColor\" id=\"browserColor\" value=\""+browserColor+"\">";
	page += "</form>";
	if(browserCheck==1 || browserCheck==2){
		page += "<div id='browserMessage'><br/>Mindspark has not been officially tested on Linux with Google chrome, but it will work. Please report problems if you encounter any issues.<br/><br/>";
		if(br[0]=="Internet Explorer"){
			page +="Please make sure that your browser mode is IE9 or Higher and it is not in Compatibility mode.<br/> <span>To check how to fix this <b onclick='openInstructionBox()' style='text-decoration:underline;cursor:pointer;'>Click Here</b></span>";
		}
		page +="</div><br/>";
	}
	page += "<div id='browserMessage'><br/>For any queries please contact mindspark@ei-india.com.<br/><br/></div><br/><br/>";
	//document.write(page);
    $("#formContainer").html(page);
	if(canContinue=="false"){
		$.post("controller.php","mode=saveBrowser&forceSave=1&blockMindspark=1&browser="+br[0]+" "+br[1]+", Flash:"+flashVersion+", OS: "+os[0]+" "+os[1]+" ~ "+screenResolution,function(data) { 

		});
	}else{
		$.post("controller.php","mode=saveBrowser&forceSave=1&browser="+br[0]+" "+br[1]+", Flash:"+flashVersion+", OS: "+os[0]+" "+os[1]+" ~ "+screenResolution,function(data) { 

		});
	}
}
else
{
	var browser = br[0]+" "+br[1];
	if(hasFlash)
		browser += ", Flash:"+flashVersion;
	browser += ", OS: "+os[0]+" "+os[1];
	setTryingToUnload();
	window.location="controller.php?mode=saveBrowser&browser="+browser+" ~ "+screenResolution+"&hasFlash="+hasFlash;
}
</script>
<noscript>
  <table align="center">
    <tbody>
      <tr>
        <td><b><font color="#FF0000">You do not have javascript enabled on your browser. Contents on this site may not display properly. Please enable Javascript.</font></b></td>
      </tr>

      <tr>
        <td>
          <p><b>If you are using Internet Explorer</b></p>
        </td>
      </tr>

      <tr>
        <td>
          <ol>
            <li>In Internet Explorer, click the <b>Tools</b> button, and then click <b>Internet Options</b>.</li>

            <li>Click the&nbsp;<strong>Security</strong> tab, and then&nbsp;select <b>Internet</b> and click on <b>Custom Level</b>&nbsp;&nbsp;&nbsp;</li>

            <li>From the window that pops up, scroll down to <b>Scripting</b> section.&nbsp;</li>

            <li>Select the&nbsp;<strong>Enable</strong>&nbsp;button in <b>Active scripting</b>, and then click <b>OK</b>.</li>

            <li>In warning message click on <b>Yes</b>. and then <b>OK</b>.</li>
          </ol>
        </td>
      </tr>
	  
	  <tr>
        <td><b>If you are using Google Chrome</b></td>
      </tr>
	  
	  <tr>
        <td>
          <ol>
            <li>On the web browser menu click on <strong>Settings</strong> icon.</li>

            <li>In the <strong>Settings</strong> section click on the <strong>Show advanced settings...</strong></li>

            <li>Under the the <strong>Privacy</strong> click on the <strong>Content settings...</strong>.</li>
			
			<li>When the dialog window opens, look for the <strong>JavaScript</strong> section and select <strong>Allow all sites to run JavaScript (recommended)</strong>.</li>

            <li>Click on <b>OK</b> button to close it.</li>
			<li>Close the <b>Settings</b> tab.</li>
          </ol>
        </td>
      </tr>

      <tr>
        <td><b>If you are using Mozila Firefox</b></td>
      </tr>

      <tr>
        <td>
          <ol>
            <li>In Mozilla Firefox, click on <strong>Tools</strong> button, and then click on <strong>Options</strong>.</li>

            <li>From the window that pops up, click on <strong>Content</strong> Tab.</li>

            <li>Check the box beside "<strong>Enable JavaScript</strong>".</li>

            <li>Click on <b>OK</b>.</li>
          </ol>
        </td>
      </tr>
    </tbody>
  </table>
</noscript>
        </div>
    </div>
</div>

<div style="display:none;">
<div class="sbody" id="MT4" style="margin-left: 40px;">
Following the below steps to rectify this problem. <br><ol><li>Press <b>[</b><b>F12]</b> on the Internet Explorer.<br>You will see the developer tools.<br><br><div class="kb_nowrapper" translate="false"><div class="kb_nowrapper"></div><img class="graphic" src="assets/ie1.png" alt="" title=""></div><br><br></li><li>Click on the <b>[</b><b>Browser Mode]</b>.<br>You are able to see all the previous version of Internet Explorer such as, IE9, IE8 and IE7.<br><br><div class="kb_nowrapper" translate="false"><div class="kb_nowrapper"></div><img class="graphic" src="assets/ie2.png" alt="" title=""></div><br><br></li><li>From here choose one from <b>[Internet Explorer 9]</b> or <b>[Internet Explorer 10]</b>. Do not choose the compatibility view browser.<br>The screen will then reload.<br><br></li><li>Close all the browsers and reopen it will automatically reset to the mode you have selected.</li></ol></div>
</div>

<div style="display:none;">
	<div class="sbody" id="MT5" style="margin-left: 40px;">
		<br>
		<ol>
			<li>
				We use 2 image folder(banks) to load certain images in mindspark. Please set your firewall to access these two links.<br/><br/>
				1) image bank 1 : https://d2tl1spkm4qpax.cloudfront.net<br/>
				2) image bank 2 : https://mindspark-ei.s3.amazonaws.com<br/><br/><br/>
			</li>
			<div id="messageForBank1">
			<li style="color:red;">
				Image bank 1 was blocked on your machine probably due to your firewall settings.<br/><br/>
			</li></div>
			<div id="messageForBank2">
			<li style="color:red;">
				Image bank 2 was blocked on your machine probably due to your firewall settings.
			</li></div>
			<?php
				if($image1=="notloaded"){
			?>
			<li style="color:red;">
				Image bank 1 was blocked on your machine probably due to your firewall settings.<br/><br/>
			</li>
			<?php
				}
				if($image2=="notloaded"){
			?>
			<li style="color:red;">
				Image bank 2 was blocked on your machine probably due to your firewall settings.
			</li>
			<?php
				}
			?>
		</ol>
	</div>
</div>

<?php include("footer.php"); ?>