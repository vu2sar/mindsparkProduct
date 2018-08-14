<?php 
require_once("dbconf.php");
include("constants.php");
$sparkieChampDetails = getSparkieChampDetails();
$sparkieChampStr = "";
$s3BucketLink = IMAGES_FOLDER;
foreach ($sparkieChampDetails as $category=>$str)
    $sparkieChampStr .= "<span class='classSlot'>Class ".$category."</span>:<br/>$str<br/>";
?>


<html>
<title>Login</title>
<head>
<link href="css/login/login.css" rel="stylesheet" type="text/css">
<script>
var timestamp = new Date().getTime();//for image tag...
var image=new Image();
var imageCounter=1;
var imageSource="http://d2tl1spkm4qpax.cloudfront.net/content_images";
var s3=<?php echo json_encode($s3BucketLink); ?>;
image.src= s3+'/sparkyForImageCheck.png?'+timestamp;
image.onload= function(){
	document.getElementById("formSubmit").action="validateLogin.php?image=loaded";
};
image.onerror= function errorImage(){
	if(imageCounter<=5){
		setTimeout(function(){errorImage();},500);
	}
	document.getElementById("formSubmit").action="validateLogin.php?image=notloaded";
};
function loginSubmit()
{
	if(document.getElementById("username").value=="")
	{
		alert("Please specify the username!");
		document.getElementById("username").focus();
		return false;
	}
	
	if(document.getElementById("username").value=="")
	{
		alert("Please specify the username!");
		document.getElementById("username").focus();
		return false;
	}
	
	/*if(document.getElementById("password").value=="")
	{
		alert("Please specify the password!");
		document.getElementById("password").focus();
		return false;
	}*/
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

<div id="logo"></div>
<table>
<tr>
<td id="col1" style="width:360px;">
<div id="leftDiv">
	
	<div style=""><img src="assets/login/Archimedes.jpg" height="320px" width="95%"/></div>
	
    </div>
</td>
<td>
	<div style="float:left"><img src="assets/login/blue.jpg" height="340px" width="1px"></div>
</td>
<td id="col2" style="width:300px">  
	<div id="sparkieChamps">
				<div id="sparkieChampHeading">SPARKIE CHAMP</div>
             
				<br>
				<span style="color:rgb(78, 7, 35)">Sparkie Champ for the week gone by: </span>
				<br>
				<br>
				<div class="champ"></div>
				<div class="paneldata">
                 <?=$sparkieChampStr?>
                </div>
				
     </div>
</td>
<td id="col3" style="width:500px">	 
	 <div id="rightDiv">
    	<div id="loginHeading" >LOGIN</div>
        <div id="loginContainer">
            <form method="POST" id="formSubmit" action="validateLogin.php" onSubmit="return loginSubmit();">
                <div style="">USERNAME</div>
                <input type="text" class="input_box" name="username" id="username"/><br><br>
                 <div style="">PASSWORD</div>
                <input type="password" class="input_box" name="password" id="password"/><br><br>
				 <div id="rowErrMsg" style="">
				
                 </div><br>
				 <div style="margin-left:10%;">
                <input type="submit" class="loginButton" id="login" value="Enter" /><br>
				</div>
     
            </form>
        </div>
		<div class="login-help">
            <p>Forgot your password? <a href="forgotPassword.php"><u>Click here</u></a> to reset it.</p>
          </div>
    </div>
</td>
</tr>
</table>			
			

</body>
</html>

<?php 

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