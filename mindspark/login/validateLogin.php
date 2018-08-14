<?php
//error_reporting(E_ALL);
include ("../userInterface/check1.php");
include ("../userInterface/classes/clsUser.php");
//include("../userInterface/classes/clsSession.php");
include("loginFunctions.php");
//error_reporting(E_ALL);
if(isset($_SESSION['userID'])){
    session_unset();
	session_destroy();
	session_start();
}
if(isset($_GET['image1'])){
    $image1 = $_GET['image1'];
}
if(isset($_GET['image2'])){
    $image2 = $_GET['image2'];
}
if (isset($_POST['image1'])) {
    $image1 = $_POST['image1'];
}
if (isset($_POST['image2'])) {
    $image2 = $_POST['image2'];
}
/*added by nivedita*/

if (isset($_POST['osDetails'])) {
    $osDetails = $_POST['osDetails'];
    $_SESSION['osDetails'] = $osDetails;
}


/*added by nivedita end*/
$betaArray = array('akash.m203','angel.k','anilkumar.n','arun.p24','augustine.s','gouthami.d','guna.m','gurucharan.eb','haravindar.r','karthik.m128','keerthana.s188','mariaangeline.t','meenakshi.s128','monisha.b','pragathi.m14','reeta.k','rohit.m53','shakthi.roushang','shivaraja.s','shreya.p80','varsha.k73','varsha.s90053','vikranth.r16','aksha.k','balaji.k58','bhumana.geethas','chandru.d','devaraj.s18','dhanalakshmi.b10','furqan.a','gowthami.n26','lavanya.sa','mahalakshmi.p37','manjula.n','mohammed.reman','nanda.kumar38','narayana.h','prashanth.m30','radhika.s20','sandesh.j','sowjanya.m','sugeetha.k','surya.p59','tejashwini','thirumalai.a','usha.k13','abhishana.a','anushaa.a','archanap.p','arunk.k','ashamarya.a','ayyanagowdar.gowdar','beenar.r','bhagyashrees.s','brindap.p','dilipm.m','dineshramesh.ramesh','gokulrais.rais','gopikak.k','gouthamj.j','isaacjoek.k','jacobg.g','jancy','keerthanav.v','manasaa.a','manikandans.s','maryjessical.l','monicam.m','namithas.s','nikithas.s','pavithras.s','prabhakaranr.r','priyas.s','roselinp.p','sandhyar.r','sanjivinib.b','savithak.k','shruthis.s','shwethar.r','sumithrav.v','syedrehans.s','ajay.b44','alex.y','anthonyraj.a','anu.e','bavani.b','bhagyashree.c14','chowdeshwari.s','david.g','deepak.b30','indra.p','janani.s207','jasmin.v','kaveri.m','kirubairaj.a','lingamurthy.d','maheshwari.m','mallikarjunh.rao','nishan.v','rahil.n18','ramya.r166','renukamba.l','sakthivel.s51','sangeetha.s27','santhosh.s138','santhosh.v44','shivakumar.s','shivagami.b','thenmozhi.m16','venkatesh.s41','vijayalakshmi.r26','vikram.v23','vishal.p69','yeshwanth.m22','ramanujan6.1');
$jsver = $_POST['jsver'];
$cookies = $_POST['cookies'];
$localStorage = $_POST['localStorage'];
//echo $browser = $_POST['browser'];
$browserName = $_POST['browserName'];
$_SESSION['browserName']=$browserName;
$browserVersion = $_POST['browserVersion'];
$_SESSION['browserVersion']=$browserVersion;
$username = isset($_POST['username']) ? $_POST['username'] : "";

if(in_array($username, $betaArray) && date("Y-m-d")>'2018-01-14')
{
	header("location:http://beta.mindspark.in/Mindspark/Login/auth/login");
	exit();
}
$pwd = isset($_POST['password']) ? $_POST['password'] : "";
$userID = $_SESSION['userID'];
$_SESSION['offlineURL'] = $_POST["offline"];

$masterPassword = isset($_POST['masterPassword']) ? $_POST['masterPassword'] : false;

$sqMaster = "SELECT configKey,configValue FROM msConfig WHERE configValue=PASSWORD('$pwd')";
$rsMaster = mysql_query($sqMaster);
$rsMaster = mysql_fetch_assoc($rsMaster);

if(isset($rsMaster["configKey"]) && $rsMaster["configKey"]!="" && !isset($_REQUEST['usernameei'])) { 
//header("Location: masterPasswordUse.php?image1=".$image1."&image2=".$image2);
	if(SERVER_TYPE=="LIVE")
	{
		echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=../userInterface/error.php?code=1'></noscript>";
		echo '<form name="frmSession" id="frmSession" method="POST" action="' . '../userInterface/masterPasswordUse.php?image1='.$image1.'&image2='.$image2 . '">';
		echo '<input type="hidden" name="password" id="password" value="'.$rsMaster["configValue"].'"/>';
		echo '<input type="hidden" name="configKey" id="password" value="'.$rsMaster["configKey"].'"/>';
		echo '<input type="hidden" name="username" id="username" value="'.$username.'"/>';
		//added  by nivedita
		echo '<input type="hidden" name="browserName" id="browserName" value="'.$browserName.'"/>';
		echo '<input type="hidden" name="browserVersion" id="browserVersion" value="'.$browserVersion.'"/>';
		echo '<input type="hidden" name="osDetails" id="osDetails" value="'.$osDetails.'"/>';
		//end
		
		echo '</form>';
		echo '<script>document.frmSession.submit();</script>';
		exit();
	}
	else
	{
		echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=error.php?code=1'></noscript>";
		echo '<form name="frmSession" id="frmSession" method="POST" action="' . '../login/validateLogin.php?image1='.$image1.'&image2='.$image2 . '">';
		echo '<input type="hidden" name="masterPassword" id="masterPassword" value="true"/>';
		echo '<input type="hidden" name="username" id="username" value="'.$username.'"/>';
		echo '<input type="hidden" name="usernameei" id="usernameei" value="offlineCheck"/>';

		echo '<input type="hidden" name="browserName" id="browserName" value="'.$browserName.'"/>';
		echo '<input type="hidden" name="browserVersion" id="browserVersion" value="'.$browserVersion.'"/>';
		echo '<input type="hidden" name="osDetails" id="osDetails" value="'.$osDetails.'"/>';
		
		echo '</form>';
		echo '<script>document.frmSession.submit();</script>';
		exit();    
	}
}
    
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
<?PHP 
}

?>
<!--<link href="css/define/style.css" rel="stylesheet" type="text/css" />-->
<script>

	function renew(userID)
	{
		document.getElementById('userID').value = userID;
		document.getElementById('frmRenew').action = "http://www.mindspark.in/renew.php";
		document.getElementById('frmRenew').submit();
	}


</script>
</head>
<body>
	
	<form name="paramFrm" id="paramFrm" method="POST" action="landingPage.php">
		<?php
			echo '<input type="hidden" name="image1" id="image1" value="'.$image1.'"/>';
			echo '<input type="hidden" name="image2" id="image2" value="'.$image2.'"/>';
			echo '<input type="hidden" name="browser" id="browser" value="'.$browser.'"/>';
			echo '<input type="hidden" name="browserName" id="browserName" value="'.$browserName.'"/>';
			echo '<input type="hidden" name="browserVersion" id="browserVersion" value="'.$browserVersion.'"/>';
			echo '<input type="hidden" name="jsver" id="jsver" value="'.$jsver.'"/>';
			echo '<input type="hidden" name="cookies" id="cookies" value="'.$cookies.'"/>';
			echo '<input type="hidden" name="localStorage" id="localStorage" value="'.$localStorage.'"/>';
		?>
	</form>
		
	<div class="left_cover" id="left_cover">
        <div class="top_bar">
        </div>
        <div class="mid_bar">
        </div>
        <div class="bot_bar" id="b_bar_left">
        </div>
    </div>
    <div class="right_cover" id="right_cover">
                        <div class="top_bar">
                        </div>
                        <div class="mid_bar">
                        </div>
                        <div class="bot_bar" id="b_bar_right">
                        </div>
    </div>
    <div class="container" id="container">
                        <div class="navbar">
                                <a class="logo">
                                </a>                                
                        </div>
                        <div class="nav_cont_separ">
                        </div>
                        <div class="content" id="content">	
<?php	
		$parentLogin = false;
		if(isset($_SESSION['parentMaster']) && $_SESSION['parentMaster'] === true)
		{
			unset($_SESSION['parentMaster']);
			$parentLogin = true;
		}
		$objUser = new User();
		$status_data = $objUser->validateLogin($username, $pwd, $masterPassword, $parentLogin);
		if($username == $pwd)
		{
			// Set flag for same user name and password check
			$_SESSION['samePasswordFlag'] = true;
		}

		if($status_data['status'] != 0)
		{

			if($status_data['status'] == 1)
			{
				$_SESSION['ms_user_id']  = $status_data['ms_user_id'];
      			header("Location: mindsparkMaths.php?image1=".$image1."&image2=".$image2."&browser=".$browser."&browser=".$browser."&browserName=".$browserName."&browserVersion=".$browserVersion."&jsver=".$jsver."&cookies=".$cookies."&localStorage=".$localStorage);
      			exit;
			}
			else if($status_data['status'] == 2)
			{

				$_SESSION['mse_user_id'] = $status_data['mse_user_id'];
				echo '<form name="english_interface" id="english_interface" method="POST" action="../../mindspark/ms_english/Language/login/index/'.$status_data["mse_user_id"].'/'.$osDetails.'/'.$browserName.'/'.$browserVersion.'">';
				echo '</form>';
				// Added by rochak for english interface to get reference call for multitab. 
            	echo '<script> sessionStorage.setItem("user","yes");  document.getElementById("english_interface").submit(); </script>';
                // -- Code added ends here -- //
            	exit;
			}
			// Logged in.
			// Landing Page.
			else if($status_data['status'] == 3)
			{
				$_SESSION['ms_user_id'] = $status_data['ms_user_id'];
				$_SESSION['mse_user_id'] = $status_data['mse_user_id'];
				ob_start();
				echo "<script>";
				//echo "document.paramFrm.action = 'landingPage.php?image1=".$image1."&image2=".$image2."&browser=".$browser."&browserName=".$browserName."&browserVersion=".$browserVersion."&jsver=".$jsver."&cookies=".$cookies."&localStorage=".$localStorage."';";
				echo "document.paramFrm.submit();";
				echo "</script>";
				exit;
			}
			// Parent Login success.
			else if($status_data['status']  ==  4 )
			{
				if (strpos($browser, 'Firefox') !== false || strpos($browser, 'Internet Explorer') !== false)
				{
					unset($_SESSION);
					echo "<script>";
					echo "alert('Parent Connect does not work perfectly on Internet Explorer and Mozilla Firefox, we recommend that you open it in Google Chrome to have a seamless experience.');";
					echo "window.location.href='index.php';";
					echo "</script>";
				}
				else
				{
					header("Location: /mindspark/login/mindsparkMaths.php?image1=".$image1."&image2=".$image2."&browser=".$browser."&browser=".$browser."&browserName=".$browserName."&browserVersion=".$browserVersion."&jsver=".$jsver."&cookies=".$cookies."&localStorage=".$localStorage);
				}
				exit;
			}
			/*else if($status_data['status'] == 'maths')
			{
				$_SESSION['ms_user_id'] = $status_data['ms_user_id'];
				$_SESSION['mse_user_id'] = $status_data['mse_user_id'];
				header("Location: landingPage.php");
			}*/
			/*else if($status_data['status'] == 'english')
			{
				$_SESSION['ms_user_id'] = $status_data['ms_user_id'];
				$_SESSION['mse_user_id'] = $status_data['mse_user_id'];
				header("Location: landingPage.php");
			}*/
			
		}
		else
		{
			// Incorrect user name and password

			$_SESSION['loginPageMsg'] = 1;
            header("Location: /techmCodeCommit/mindsparkProduct/mindspark/login/?login=0");
            exit;
		}

?>
		</div>
	</div>
</body>
</html>