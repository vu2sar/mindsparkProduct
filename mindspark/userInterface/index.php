<?php
header("Location: http://".$_SERVER['HTTP_HOST']."/mindspark/login/"); //change this
    exit(0);
if($_SERVER['SERVER_NAME']=='mindspark.in')
{
    header("Location: http://www.mindspark.in/login/"); //change this
    exit(0);
}
//For live
require_once("../mindspark/dbconf.php");
include("../mindspark/constants.php");
//For local
//require_once("dbconf.php");
//include("constants.php");

$sparkieChampDetails = getSparkieChampDetails();
$sparkieChampStr = "";

foreach ($sparkieChampDetails as $category=>$str)
    $sparkieChampStr .= "<span class='classSlot'>Class ".$category."</span>:<br/>$str<br/>";

session_start();
session_unset();
session_destroy();
session_start();
$_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
$domain = 'www.mindspark.in';
//$domain = 'ec2-122-248-236-40.ap-southeast-1.compute.amazonaws.com';
//$domain = 'localhost';
$domain = $_SERVER['HTTP_HOST'];
$app_id = "548639571895188"; //change this
$redirect_url = "http://$domain/mindspark/userInterface/callbackFB.php";

$fbdialog_url = "https://www.facebook.com/dialog/oauth?client_id="
        . $app_id . "&redirect_uri=" . urlencode($redirect_url) . "&state="
        . $_SESSION['state'] . "&response_type=code&scope=email";

$clientID = "561957817913-dk0oi64c86kaisk01l9d15ltedl86vge.apps.googleusercontent.com";

$redirect_url_google = "http://$domain/mindspark/userInterface/callbackGoogle.php";

$google_url = "https://accounts.google.com/o/oauth2/auth?client_id="
        . $clientID . "&redirect_uri=" . urlencode($redirect_url_google) . "&state="
        . $_SESSION['state'] . "&scope=openid%20email+profile&response_type=code";
$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");

$homeUsage = 0;
$startTime = strtotime("07:00:00");  //School start time
$endTime   = strtotime("16:00:00");  //School end time
//$_tH = (int)date('G');
$now = time();
//Consider after 5 p.m. and before 7 a.m. as home usage and Sundays
//if ((($_tH >= 17) || ($_tH < 7) ) || date("D")=="Sun")
if ((($now < $startTime) || ($now > $endTime) ) || date("D")=="Sun")
{
    $homeUsage = 1;
}


?>

<!DOCTYPE HTML>
<html>
<title>Login</title>
<head>
<!--<meta http-equiv="X-UA-Compatible" content="IE=9">-->
<!--For Live-->
<link href="../mindspark/userInterface/css/login/login.css?ver=2" rel="stylesheet" type="text/css">
<link href="../mindspark/userInterface/css/prompt.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../mindspark/userInterface/libs/jquery.js"></script>
<!--For local-->
<!--<link href="css/login/login.css?ver=2" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/jquery.js"></script>-->
<script type="text/javascript" src="../mindspark/userInterface/libs/prompt.js"></script>
<script>
    $(document).ready(
        function(){
        var finalHeight = $('#loginHeading').height()+$('#loginContainer').height()+$('#loginHelp').height()+$('#loginParent').height()+$('#loginOpenID').height()+150;
        $('#rightDiv').height(finalHeight);
        }
        );
window.name = "";
sessionStorage.setItem('windowName','');
var timestamp = new Date().getTime();//for image tag...
var image=new Image();
var image1="";
var image2="";
var imageCounter=1;
var imageCounter1=1;
var imageSource="http://d2tl1spkm4qpax.cloudfront.net/content_images";
var imageSource1="http://mindspark-ei.s3.amazonaws.com/content_images";
image.src= imageSource+'/sparkyForImageCheck.png?'+timestamp;
image.onload= function(){
	image1="loaded";
};
image.onerror= function errorImage(){
	imageCounter++;
	if(imageCounter<=5){
		var timestamp = new Date().getTime();
		image.src= imageSource+'/sparkyForImageCheck.png?'+timestamp;
		setTimeout(function(){errorImage();},500);
	}else{
		image1="notloaded";
	}
};
var image3=new Image();
image3.src= imageSource1+'/sparkyForImageCheck.png?'+timestamp;
image3.onload= function(){
	image2="loaded";
};
image3.onerror= function errorImage(){
	imageCounter1++;
	if(imageCounter1<=5){
		var timestamp = new Date().getTime();
		image3.src= imageSource1+'/sparkyForImageCheck.png?'+timestamp;
		setTimeout(function(){errorImage();},500);
	}else{
		image2="notloaded";	
	}
};
function loginSubmit()
{
	if(document.getElementById("username").value=="")
	{
		alert("Please specify the username!");
		document.getElementById("username").focus();
		return false;
	}
//        For Live
	document.getElementById("formSubmit").action="/mindspark/validateLogin.php?image1="+image1+"&image2="+image2;
//        For local
//        document.getElementById("formSubmit").action="validateLogin.php?image1="+image1+"&image2="+image2;
}

function qs()
{

	var query = window.location.search.substring(1);

	var parms = query.split("&");
	for (var i=0; i<parms.length; i++) {
		var pos = parms[i].indexOf("=");
		if (pos > 0) {
			var key = parms[i].substring(0,pos);
			var val = parms[i].substring(pos+1);
			if(key=="login" && val=="0")
			{
				document.getElementById("rowErrMsg").innerHTML = "Username and password do not match";
			}
            else if(key=='login' && val=='1')
                    document.getElementById("rowErrMsg").innerHTML = "Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.";
            else if(key=="login" && val=="2")
			{
				 var prompts = new Prompt({
                    text: "Sorry for the inconvenience! You won't be able to login temporary due to class up-gradation in progress. Please try again later.<br><br>(If you are not able to login after 24 hours, please contact your teacher or customer support)" ,
                    type: 'alert',
                    func1: function () {
                        jQuery("#prmptContainer_classUpgrade").remove();
                    },
                    promptId:'classUpgrade'
                });
			}
		}
	}
}



</script>
<?php if( $iPad ==true|| $Android ==true){?>
 <script>
        function doOnOrientationChange() {
             if (!jQuery('.promptContainer').is(":visible")) {
                var prompts = new Prompt({
                    text: 'Mindspark is best viewed and worked with in the landscape (horizontal) mode.<br><br>Please shift to landscape mode to proceed further. ' ,
                    type: 'block',
                    func1: function () {
                        jQuery("#prmptContainer").remove();
                    }
                });
                jQuery("#promptText").css('font-size', '160%');
            }
            //jQuery('.promptContainer').css('display','none');
            var windowheight = jQuery(window).height();
            var windowwidth = jQuery(window).width();
            var pagecenterW = windowwidth / 2;
            var pagecenterH = windowheight / 2;
            jQuery("#promptBox").css({ 'margin-top': pagecenterH - 130 + 'px', 'margin-left': pagecenterW - 175 + 'px' });
        }

    </script>
<?php
 }
?>
</head>
<body onload="qs()" >
<div id="header">
	<div id="eiColors">
		<div id="orange"></div>
		<div id="yellow"></div>
		<div id="blue"></div>
		<div id="green"></div>
	</div>
</div>
<div id="head" style=""> <a href="../">
	<div id="logo" ></div>
	</a> <a href="../faq.php" target="_blank">
	<div id="help"> </div>
	</a> </div>
<div id="tab">
	<table id="containerTable">
		<tr>
			<td id="col1" style="width:360px;"><div id="leftDiv" >
			
					<?php if($homeUsage==1){ ?>
						<div style=""><a href="http://blog.ei-india.com/clash-of-the-wordsmiths-essay-writer-is-back/" target="_blank" style="text-decoration:none"><img src="<?=WHATSNEW?>Login_Image/loginHome.jpg" height="320px" width="95%"/></a></div>
					<?php } else{ ?>
						<div style=""><a href="https://plus.google.com/106077666989088271985/posts" target="_blank" style="text-decoration:none"><img src="<?=WHATSNEW?>Login_Image/login.jpg" height="320px" width="95%"/></a></div>
					<?php } ?>
					
				</div></td>
			<td><div style="float:left;margin-top: -10px;" ><img src="../mindspark/userInterface/assets/login/blue.jpg" height="340px" width="1px"></div></td>
			<td id="col2" style="width:300px"><div id="sparkieChamps" >
					<div id="sparkieChampHeading">SPARKIE CHAMP</div>
					<br>
					<span style="color:rgb(78, 7, 35)">Sparkie Champ for the week gone by: </span> <br>
					<br>
					<div class="champ"></div>
					<div class="paneldata">
						<?=$sparkieChampStr?>
					</div>
				</div></td>
			<td id="col3" style="width:500px"><div id="rightDiv">
					<div id="loginHeading" >LOGIN</div>
					<div id="loginContainer">
<!--                                            For live-->
						<form method="POST" id="formSubmit" action="/mindspark/validateLogin.php" onSubmit="return loginSubmit();">
<!--                                                For Local-->
<!--                                                <form method="POST" id="formSubmit" action="validateLogin.php" onSubmit="return loginSubmit();">-->
							<div style="">USERNAME</div>
							<input type="text" class="input_box" name="username" id="username"/>
							<br>
							<br>
							<div style="">PASSWORD</div>
							<input type="password" class="input_box" name="password" id="password"/>
							<br>
							<br>
							<div id="rowErrMsg" style="padding-bottom: 2px;"> </div>
							<div id="break">
							</div>
							<div style="margin-left:10%;">
								<input type="submit" class="loginButton" id="login" value="Enter" />
								<br>
							</div>
						</form>
					</div>
					<div id="loginHelp" class="login-help">
<!--                                            For live-->
						<p>Forgot your password? <a href="../mindspark/userInterface/forgotPassword.php"><u>Click here</u></a> to reset it.</p>
<!--                                                For local-->
                                            <!--<p>Forgot your password? <a href="forgotPassword.php"><u>Click here</u></a> to reset it.</p>-->
					</div>
                                        <div id="loginParent" class="login-parent"><b>Login to Parent Portal with:</b></div>
                            <div id="loginOpenID" class="login-openid">
<!--                                For live-->
                                <a href="<?php echo $google_url; ?>"><img src="../mindspark/userInterface/assets/login/google.png" style="border: 0px;"></a>
                                <a href="<?php echo $fbdialog_url; ?>"><img src="../mindspark/userInterface/assets/login/fb.png" style="border: 0px;"></a>
<!--                                    For local-->
                                <!--<a href="<?php //echo $google_url; ?>"><img src="assets/login/google.png" style="border: 0px;"></a>
                                <a href="<?php //echo $fbdialog_url; ?>"><img src="assets/login/fb.png" style="border: 0px;"></a>-->
                            </div>
				</div></td>
		</tr>
	</table>
	<div id="bottom_bar">
		<div id="copyright">&copy; 2009-2014, Educational Initiatives Pvt. Ltd.</div>
	</div>
</div>
</body>
</html>
<?php include("../mindspark/userInterface/footer.php");

function getSparkieChampDetails()
{
    $studentDetails = array();
    $tilldate  = date( 'Y-m-d', strtotime( 'last sunday'));
    $datearr   = explode("-",$tilldate);
    $timestamp = mktime(0,0,0,$datearr[1],$datearr[2],$datearr[0]);
    $newtimestamp = strtotime("-6 days",$timestamp);
    $fromdate  = strftime("%Y-%m-%d",$newtimestamp);

	$query	=	"SELECT studentName, studentClass, schoolName
				 FROM adepts_loginPageDetails WHERE fromDate='$fromdate' AND tillDate='$tilldate' ORDER BY cast(studentClass as unsigned)";
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result)) {
        $str = $line[0].", Class ".$line[1].",<br>".$line[2];
        if($line['studentClass']>=1 && $line['studentClass']<=3)
            $studentDetails["1 to 3"] = $str;
        else if($line['studentClass']>=4 && $line['studentClass']<=7)
            $studentDetails["4 to 7"] = $str;
        else if($line['studentClass']>=8 && $line['studentClass']<=10)
            $studentDetails["8 to 10"] = $str;
    }
    return $studentDetails;
}

?>