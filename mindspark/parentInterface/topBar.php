<?php
//use BLL;
//error_reporting(E_ALL);

set_time_limit(0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
//include("../slave_connectivity.php");
require 'constants.php';

require DIR_CLASSES . 'slaveConnection.php';

require DIR_BLL . 'parentDetails.php';

require 'common.php';
//require '../userInterface/dbconf.php';
include("../slave_connectivity.php");
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../logout2.php");
    exit;
}
$currentDate = date("Y-m-d");
//        $userID     = $_SESSION['userID'];
//	$schoolCode = isset($_SESSION['schoolCode'])?$_SESSION['schoolCode']:"";
//	$user   = new User($userID);
if (!isset($_SESSION['childIDUsed'])) {
    $parentDetail = new BLL\ParentDetails($sdb, $_SESSION['parentUserID']);
    $childrenMapped = $parentDetail->getChildrenMapped();
    if(count($childrenMapped)>0)
    {
        setSesssionValues($childrenMapped);
        } else {
        require DIR_CLASSES . 'masterConnection.php';
        $parentDetail = new BLL\ParentDetails($db, $_SESSION['parentUserID']);
        $childrenMapped = $parentDetail->getChildrenMapped();
        if (count($childrenMapped) > 0) {
            setSesssionValues($childrenMapped);
        }
    }
//    $query = "SELECT userID FROM adepts_userDetails WHERE parentEmail='" . $_SESSION['openIDEmail'] . "'";
//    $query = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='" . $_SESSION['openIDEmail'] . "' OR FIND_IN_SET('" . $_SESSION['openIDEmail'] . "',secondaryParentEmail))";
//    $i = 0;
//    $r = mysql_query($query) or die(mysql_error());
//    
//    if(mysql_num_rows($r)==0)
//    {
//        include("../userInterface/dbconf.php");
//        $query = "SELECT userID FROM adepts_userDetails WHERE (parentEmail='" . $_SESSION['openIDEmail'] . "' OR FIND_IN_SET('" . $_SESSION['openIDEmail'] . "',secondaryParentEmail))";    
//        $r = mysql_query($query) or die(mysql_error());
//    }
//    while ($l = mysql_fetch_array($r)) {
//        $childID[$i] = $l[0];
//        $childSelected = new User($childID[0]);
//        $child[$i] = new User($childID[$i]);
//        $childName[$i] = $child[$i]->childName;
//        $i++;
//        $_SESSION['childID'] = $childID[0];
//        $_SESSION['childIDUsed'] = $childSelected->userID;
//        $_SESSION['childNameUsed'] = $childSelected->childName;
//        $_SESSION['childClassUsed'] = $childSelected->childClass;
//		$_SESSION['childSubcategory'] = $childSelected->subcategory;
//        $_SESSION['packageExpiryDate'] = $childSelected->endDate;
//    }
//    $_SESSION['arrChildID'] = $childID;    
//    $_SESSION['arrChildName'] = $childName;  
}
//if(isset($_SESSION['sessionID']))
//{
//    include("../userInterface/dbconf.php");
//    $query	=	"UPDATE adepts_parentSessionStatus SET lastModified=now() where sessionID=".$_SESSION['sessionID']." limit 1;";
//    $result = mysql_query($query);
////    mysql_close();
//}
//@include("../slave_connectivity.php");
//Temp code for testing
//if(isset($_REQUEST['userID']))
//{
//    $childSelected = new User($_REQUEST['userID']);
//    $_SESSION['childID'] = $_REQUEST['userID'];
//        $_SESSION['childIDUsed'] = $childSelected->userID;
//        $_SESSION['childNameUsed'] = $childSelected->childName;
//        $_SESSION['childClassUsed'] = $childSelected->childClass;
//	$_SESSION['childSubcategory'] = $childSelected->subcategory;
//        $_SESSION['packageExpiryDate'] = $childSelected->endDate;
//}

//Variables to fill dropdown of children
$childID = $_SESSION['arrChildID'];
$childIDFree = $_SESSION['arrChildFreeTrial'];

$childName = $_SESSION['arrChildName'];
$childUserName = $_SESSION['arrUserName'];


if(!isset($_SESSION['arrChildID'])){
	echo "<script type='text/javascript'>alert('Something went Wrong. You will be logged out.');window.location = '../logout.php';</script>";
}

if (isset($_POST['childSelectedID'])) {
    $_SESSION['childID'] = $_POST['childSelectedID'];        
    $childSelected = new User($_SESSION['childID']);
    $_SESSION['childIDUsed'] = $_POST['childSelectedID'];
    $_SESSION['childNameUsed'] = $childSelected->childName;
    $_SESSION['childClassUsed'] = $childSelected->childClass;
	$_SESSION['childSubcategory'] = $childSelected->subcategory;
	$_SESSION['packageExpiryDate'] = $childSelected->endDate;
	$_SESSION['packageExpiryDate'] = date("d-m-Y", strtotime($_SESSION['packageExpiryDate']) );
        for($i=0; $i<count($childID); $i++)	
        {
            if($childID[$i]==$_SESSION['childID'])
                $_SESSION['childFreeTrial']=$childIDFree[$i];
        }
}
if(strtotime($currentDate)>strtotime($_SESSION['packageExpiryDate'])){
$alertValue=1;
}else if(strtotime($currentDate."+15 days")>strtotime($_SESSION['packageExpiryDate'])){
	$alertValue=2;
}else{
	$alertValue=0;
}
$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
if(strpos($userAgent,'android')!==false || strpos($userAgent,'ipad')!==false || strpos($userAgent,'iphone')!==false || strpos($userAgent,'ipod')!==false || strpos($userAgent,'blackberry')!==false || strpos($userAgent,'opera mobile')!==false)
{
    require '../userInterface/dbconf.php';
    $query = "INSERT INTO parentPortalMobileUsage(parentUserID, sessionID) VALUES(".$_SESSION['parentUserID'].",".$_SESSION['sessionID'].")";
        mysql_query($query);
    header("Location:../parentApp/");
    exit;    
}
else {

}
//if (isset($_SESSION['childIDUsed'])) {
//    $childSelectedID = $_SESSION['childIDUsed'];
//    $_SESSION['childID'] = $_SESSION['childIDUsed'];
//    $childSelected = new User($_SESSION['childID']);
//    $_SESSION['childNameUsed'] = $childSelected->childName;
//    $_SESSION['childClassUsed'] = $childSelected->childClass;
//}

if (isset($_SESSION['picture'])) {
    ?>
    <style>
        #nameIcon{
            background: url('<?= $_SESSION['picture'] ?>') no-repeat -2px -2px;
            background-size:52px 51px;
            background-position:center; 
        }
    </style>
    <?php
}
?>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script>
    function selectChild() {
		setTryingToUnload();
        $("#childDropDown").submit();
    }
	function showPdf(){
		var k = window.innerWidth;
		if(k>1024)
		{
			$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true});
		}
		else
		{
			$.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true});
		}
	}
	function showMessage(){
		var alertValue=<?php echo json_encode($alertValue); ?>;
		var expiryDate=<?php echo json_encode($_SESSION['packageExpiryDate']); ?>;
		var childName=<?php echo json_encode($_SESSION['childNameUsed']); ?>;
		var subCategory = <?php echo json_encode($_SESSION['childSubcategory']); ?>;
		if(subCategory!="Individual"){
			alert(childName+"'s account will expire on "+expiryDate+".");
			alertValue=4;
		}
		if(alertValue==2){
			var messageString= childName+"'s account will expire on "+expiryDate+". An immediate renewal would ensure that there is no discontinuity in "+childName+"'s Mindspark usage as well as avail a discount. Please click OK to renew the account."
			var answer = confirm (messageString)
			if (answer)
			window.open('http://mindspark.in/renew.php', '_blank');
		}else if(alertValue==1){
			
			var messageString= childName+"'s account has expired on "+expiryDate+". An immediate renewal would ensure that there is no discontinuity in "+childName+"'s Mindspark usage as well as avail a discount. Please click OK to renew the account."
			var answer = confirm (messageString)
			if (answer)
			window.open('http://mindspark.in/renew.php', '_blank');
		}
	}
</script>

<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<div class="logo">
</div>
<div id="infoBarMiddle">
    <form id="childDropDown" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        <table class="childDetails">
            <tr>
                <td class="topText">Child</td>
                <td>
                    <select name="childSelectedID" id="childSelectedID" onchange="selectChild()" <?php if(count($childID)==1) echo "disabled" ?>>
                        <?php for ($i = 0; $i < count($childID); $i++) { ?>
                            <option value="<?= $childID[$i] ?>" <?php if ($_SESSION['childIDUsed'] == $childID[$i]) echo " selected"; ?>><?= $childName[$i] ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td style="padding-left:10px;" width="60px">
                     Class <?= $_SESSION['childClassUsed'] ?>
                </td>
                <td style="padding-left:10px;" width="120px">
                    Package Expiry Date : 
                </td>
                <td style="padding-left:10px;color: #9ec956;"  width="180px">
                    <?= $_SESSION['packageExpiryDate'] ?>
                </td>
            </tr>
            <tr>                
				<td style="padding-left:0px;" colspan="5">                                    
                    <div class="fb-like" data-href="https://www.facebook.com/Mindspark.EI" data-width="The pixel width of the plugin" data-height="The pixel height of the plugin" data-colorscheme="light" data-layout="button" data-action="like" data-show-faces="false" data-send="false"></div>
                </td>
            </tr>
        </table>
    </form>
</div>
<div id="studentInfoLowerClass">
	<div class="alert" onclick="showMessage()" <?php if($alertValue==1 || $alertValue==2) echo "style='display:block;'"; else echo "style='display:none;'"; ?> title="<?= ($alertValue==2?$_SESSION['childNameUsed']."'s package is about to expire":$_SESSION['childNameUsed']."'s package has expired") ?>"></div>
    <div id="nameIcon"></div>
    <div id="infoBarLeft">
        <div id="nameDiv">
            <div id='cssmenu'>
                <ul>
                    <li class='has-sub '><a href='javascript:void(0)'><div id="nameC">Welcome <?= $_SESSION['firstName']?>&nbsp;&#9660;</div></a>
                        <ul>
                            <?php if($_SESSION['openIDProvider']=='Mindspark')
                            { ?>
                            <li><a href='changePasswordInner.php'><span>Change Password</span></a></li>
                            <?php } ?>
                            <li><a href='resetChildPassword.php'><span>Reset Child's Password</span></a></li>
                            <li><a href='../logout.php'><span>Logout</span></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>