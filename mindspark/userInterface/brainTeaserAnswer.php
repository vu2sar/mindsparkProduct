<?php
	//Trick names taken form database are stored in this array.
	
	global $j;
	set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);

	@include_once("check1.php");
	include("classes/clsUser.php");
	include("constants.php");
	include("functions/functions.php");
	include_once("classes/clsTopicProgress.php");
	
	/*$link = mysql_connect("ec2-54-251-4-141.ap-southeast-1.compute.amazonaws.com","ms_analysis","ARE001") or die("Could not connect : " . mysql_error());
	mysql_select_db("educatio_adepts",$link) or die("Could not select database");*/
	
	if(!isset($_SESSION['userID']))
	{
		header("Location:logout.php");
		exit;
	}
	if(isset($_SESSION['revisionSessionTTArray']) && count($_SESSION['revisionSessionTTArray'])>0)
    {
        header("Location: controller.php?mode=login");
    }
	$userID = $_SESSION['userID'];

	$objUser = new User($userID);
	
	$Name = explode(" ", $_SESSION['childName']);
	$Name = $Name[0];
	$childName 	   = $objUser->childName;
	$schoolCode    = $objUser->schoolCode;
	$childClass    = $objUser->childClass;
	$childSection  = $objUser->childSection;
	$category 	   = $objUser->category;
	$subcategory   = $objUser->subcategory;
	$schoolCode    = $objUser->schoolCode;
	//$arrActivities = getActivities($childClass,"",$objUser->packageType);
	$today = date("Y-m-d");
	$lastMon = date("Y-m-d",strtotime("last Monday"));
	/*$date = $_GET['datem'];*/
	$variable = $_POST['ans1'];
	$v = split ("\-", $variable);
	$TeaserNo=$v[1];
	$response = $v[0];
	
	
	date_default_timezone_set('Asia/Kolkata');
	$today = getdate();
	$day = $today["wday"];
	
	
	
	$sql = "SELECT `userResponse` FROM `adepts_exploreTeaserStudent` WHERE `Teaser_No` = '$TeaserNo' and `userID`='$userID'";
	$result1 = mysql_query($sql) or die(mysql_error());
	$j = 0;
	while($row1 = mysql_fetch_array($result1))
	{
 		$res[$j] = $row1['userResponse'];
 		$j++;
	}
	$r = $res[0];
	
	if (!($r == NULL)) {
    $msg = "You have already answered";
	echo '<script type="text/javascript">alert("' .$msg. '"); </script>';
	echo '<script type="text/javascript">window.history.back(); </script>';
	}
	else
	{
		$sql =  "UPDATE `adepts_exploreTeaserStudent` set userResponse= '$response' WHERE userID ='$userID' and Teaser_No= '$TeaserNo' "  ; 
		
	mysql_query($sql) or die(mysql_error());
	$msg = "Thank you your response has been recorded.";
	echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
	/*echo '<script type="text/javascript">window.location = "explore.php"; </script>';*/
	
		if($day == 1)
		{
			$set_date = date('Y-m-d');
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeaser.php?datem=$set_date';
    </SCRIPT>");
		}
		elseif($day == 2)
		{
			$set_date = date('Y-m-d', strtotime($date .' -1 day'));
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeaser.php?datem=$set_date';
    </SCRIPT>");
		}
		elseif($day == 3)
		{
			$set_date = date('Y-m-d', strtotime($date .' -2 day'));
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeaser.php?datem=$set_date';
    </SCRIPT>");
		}
		elseif($day == 4)
		{
			$set_date = date('Y-m-d');
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeasers.php?datet=$set_date';
    </SCRIPT>");
		}
		elseif($day == 5)
		{
			$set_date = date('Y-m-d', strtotime($date .' -1 day'));
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeasers.php?datet=$set_date';
    </SCRIPT>");
		}
		elseif($day == 6)
		{
			$set_date = date('Y-m-d', strtotime($date .' -2 day'));
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeasers.php?datet=$set_date';
    </SCRIPT>");
		}
		elseif($day == 0)
		{
			$set_date = date('Y-m-d', strtotime($date .' -3 day'));
			echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.location.href='brainTeasers.php?datet=$set_date';
    </SCRIPT>");
		}
	
	}
	
	
	
?>

<?php include("header.php");?>

<title>Explore Zone</title>
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"> </Script>
	<script>
		
    </script>
</head>
<body  class="translation">
	
<?php include("footer.php");?>