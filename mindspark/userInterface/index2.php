<?php 
if($_SERVER['SERVER_NAME']=='mindspark.in')
{
    header("Location: http://www.mindspark.in/mindspark/userInterface/index2.php"); //change this
    exit(0);
}
//require_once("../mindspark/dbconf.php");
//include("../mindspark/constants.php");
require_once("dbconf.php");
include("constants.php");

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
//$domain = 'www.educationalinitiatives.com';
//$domain = 'localhost';
$app_id = "548639571895188"; //change this
$redirect_url = "http://$domain/mindspark/userInterface/callbackFB.php"; //change this
//$redirect_url = "http://www.educationalinitiatives.com/mindspark/userInterface/callbackFB.php"; //change this
//$redirect_url = "http://localhost/mindspark/userInterface/callbackFB.php"; //change this

$fbdialog_url = "https://www.facebook.com/dialog/oauth?client_id="
        . $app_id . "&redirect_uri=" . urlencode($redirect_url) . "&state="
        . $_SESSION['state'] . "&response_type=code&scope=email";

$clientID = "561957817913-dk0oi64c86kaisk01l9d15ltedl86vge.apps.googleusercontent.com";
//$clientID = "288984402212.apps.googleusercontent.com";

//$redirect_url_google = "http://localhost/mindspark/userInterface/callbackGoogle.php"; //change this
//$redirect_url_google = "http://www.educationalinitiatives.com/mindspark/userInterface/callbackGoogle.php"; //change this
$redirect_url_google = "http://$domain/mindspark/userInterface/callbackGoogle.php"; //change this

$google_url = "https://accounts.google.com/o/oauth2/auth?client_id="
        . $clientID . "&redirect_uri=" . urlencode($redirect_url_google) . "&state="
        . $_SESSION['state'] . "&scope=openid%20email+profile&response_type=code";
?>

<!DOCTYPE HTML>
<html>
<title>Login</title>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<!--<link href="../mindspark/userInterface/css/login/login.css" rel="stylesheet" type="text/css">-->
<link href="css/login/login2.css" rel="stylesheet" type="text/css">
<script>
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
		setTimeout(function(){errorImage();},500);
	}
	image1="notloaded";
};
var image3=new Image();
image3.src= imageSource1+'/sparkyForImageCheck.png?'+timestamp;
image3.onload= function(){
	image2="loaded";
};
image3.onerror= function errorImage(){
	imageCounter1++;
	if(imageCounter1<=5){
		setTimeout(function(){errorImage();},500);
	}
	image2="notloaded";
};
function loginSubmit()
{
	if(document.getElementById("username").value=="")
	{
		alert("Please specify the username!");
		document.getElementById("username").focus();
		return false;
	}
	
//	document.getElementById("formSubmit").action="/mindspark/validateLogin.php?image1="+image1+"&image2="+image2;
document.getElementById("formSubmit").action="../validateLogin.php?image1="+image1+"&image2="+image2;
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
		}
	}
}



</script>
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
<!--					<div style=""><img src="../mindspark/userInterface/assets/login/Archimedes.jpg" height="320px" width="95%"/></div>-->
                                <div style=""><img src="assets/login/Archimedes.jpg" height="320px" width="95%"/></div>
				</div></td>
<!--			<td><div style="float:left;margin-top: -10px;" ><img src="../mindspark/userInterface/assets/login/blue.jpg" height="340px" width="1px"></div></td>-->
                                <td><div style="float:left;margin-top: -10px;" ><img src="assets/login/blue.jpg" height="340px" width="1px"></div></td>
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
<!--						<form method="POST" id="formSubmit" action="/mindspark/validateLogin.php" onSubmit="return loginSubmit();">-->
<form method="POST" id="formSubmit" action="../validateLogin.php" onSubmit="return loginSubmit();">
							<div style="">USERNAME</div>
							<input type="text" class="input_box" name="username" id="username"/>
							<br>
							<br>
							<div style="">PASSWORD</div>
							<input type="password" class="input_box" name="password" id="password"/>
							<br>
							<br>
							<div id="rowErrMsg" style=""> </div>
							<div id="break">
							</div>
							<div style="margin-left:10%;">
								<input type="submit" class="loginButton" id="login" value="Enter" />
								<br>
							</div>
						</form>
					</div>
					<div class="login-help">
<!--						<p>Forgot your password? <a href="../mindspark/userInterface/forgotPassword.php"><u>Click here</u></a> to reset it.</p>-->
                                            <p>Forgot your password? <a href="forgotPassword.php"><u>Click here</u></a> to reset it.</p>
					</div>
                                        <div class="login-parent"><b>Login to Parent Portal with:</b></div>
                            <div class="login-openid">                                
                                <a href="<?php echo $google_url; ?>"><img src="assets/login/google.png" style="border: 0px;"></a>
                                <a href="<?php echo $fbdialog_url; ?>"><img src="assets/login/fb.png" style="border: 0px;"></a>
                            </div>
				</div></td>
		</tr>
	</table>
	<div id="bottom_bar">
		<div id="copyright">&copy; 2009-2013, Educational Initiatives Pvt. Ltd.</div>
	</div>
</div>
</body>
</html>
<?php // include("../mindspark/userInterface/footer.php"); 
include("footer.php"); 

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