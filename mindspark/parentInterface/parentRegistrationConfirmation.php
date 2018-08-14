<?php
session_start();

//use BLL;
//error_reporting(E_ALL);
if (!class_exists('db')) {
    require_once('../parentInterface/classes/class.db.php');
}
require_once 'constants.php';
require_once DIR_CLASSES . 'slaveConnection.php';
require_once 'classes/BLL/parentDetails.php';
require_once DIR_BLL . 'ParentSession.php';
include_once "common.php";
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../logout.php");
    exit;
}
$emailID = $_SESSION['openIDEmail'];
$givenName = $_SESSION['firstName'];
$familyName = $_SESSION['lastName'];
$openIDMethod = $_SESSION['openIDMethod'];
$openIDProvider = $_SESSION['openIDProvider'];
error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$msg = '';
//$registrationError = false;
//$redirect = false;
$parentDetails = new BLL\ParentDetails($sdb);
$checkDuplicateParent = $parentDetails->getParentDetailsByEmail($emailID);
if (count($checkDuplicateParent) > 0) {
    $parentDetails->parentUserID=$checkDuplicateParent[0]['parentUserID'];
    $childrenMapped = $parentDetails->getChildrenMapped();
    $_SESSION['parentUserID'] = $parentDetails->parentUserID;
    $childID = array();
    $i = 0;
    if (count($childrenMapped) > 0) {
        setSesssionValues($childrenMapped);
        header("Location:accountManagement.php");
        exit;
}
}
$innerStudentMapPage = FALSE;
if ($_POST['emailID']) {
    $emailID = $_POST['emailID'];
    $firstName = $_POST['givenName'];
    $lastName = $_POST['familyName'];
    $openIDMethod = $_POST['openIDMethod'];
    $phoneNumber = $_POST['phoneNumber'];
    require_once('../parentInterface/classes/masterConnection.php');
    $parentDetails = new BLL\ParentDetails($db);
    $parentDetails->username = $emailID;
    $parentDetails->firstName = $firstName;
    $parentDetails->lastName = $lastName;
    $parentDetails->phoneNumber = $phoneNumber;
    $parentDetails->enabled = 1;
    $parentDetails->verified = TRUE;
    $parentDetails->loginType = 2;
    $parentDetails->registrationDate = date('Y-m-d');
    $parentUserID = $parentDetails->addParentDetails();
    $parentDetails->parentUserID = $parentUserID;
    $_SESSION['parentUserID'] = $parentUserID;
    $parentSession = new BLL\ParentSession($db);
    $ids = array();
    $students = implode(',', $ids);
        $startTime = date("Y-m-d H:i:s");
        $parentSession->parentEmail = $_SESSION['openIDEmail'];
        $parentSession->provider = "Google";
        $parentSession->students = $students;
        $parentSession->startTime = $startTime;
        $parentSession->parentUserID = $_SESSION['parentUserID'];
        $_SESSION['sessionID'] = $parentSession->saveParentStatus();
    $childrenMapped = $parentDetails->getChildrenMapped();
    $childID = array();
    $i = 0;
    if (count($childrenMapped) > 0) {
        setSesssionValues($childrenMapped);
        header("Location:accountManagement.php?showMessage");
        exit;
    } else {
        header("Location:childRegistrationOuter.php");
        exit;
    }
}
?>
<title>Register as Parent</title>
<meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=9">
<meta
    name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
<script src="libs/jquery-1.9.1.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />

<script src="libs/jquery-ui.min.js"></script>
<!--<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">-->
<link href="css/common.css?ver=4" rel="stylesheet" type="text/css">
<link href="css/parentConfirm.css" rel="stylesheet" type="text/css">
<!--<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>-->
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script>
    var langType = '<?= $language; ?>';
    function redirect(URL)
    {
        setTryingToUnload();
        window.location = URL;
    }
</script>

</head>
<body class="translation" style="height: 100%; vertical-align: central" height="100%">
    <?php include("eiColors.php") ?>
    <div class="logo1">
    </div>
    <script>
        if (<?= ($redirect == true ? 'true' : 'false') ?>)
        {
            window.setTimeout(function() {
                setTryingToUnload();
                window.location = 'home.php';
            }, 7000);
        }
        function trim(str) {
		// Strip leading and trailing white-space
		return str.replace(/^\s*|\s*$/g, "");
	}
        function validate()
        {
            var phoneNumber = trim(document.getElementById('phoneNumber').value);
            var errorMsg = '';
            if(phoneNumber=='')
                errorMsg = 'Please specify contact number.';
            if(errorMsg!='')
            {
                alert(errorMsg);
                return false;
            }
            document.getElementById('formRegistration').submit();
        }
        function contanctKeypress(evt) {
    evt = evt || window.event;
    if (!evt.ctrlKey && !evt.metaKey && !evt.altKey) {
        var charCode = (typeof evt.which == "undefined") ? evt.keyCode : evt.which;
        if (charCode) {
			var key_codes = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];

                 if (!((charCode>=48 && charCode<=57) || charCode==0 || charCode==8)) {
                   evt.preventDefault();
                 }            
        }
    }
	};
    </script>
	<div id="imageContainer"></div>
    <div class="divRegistration">
        <div >
            <h3>
                You are about to create an account as a parent on Mindspark<br/>
            </h3>
        </div>
        <div style="width:100%">
            Using login from:<br/>
            <?php echo $openIDProvider ?> (<?php echo $emailID; ?>)<br/><br/>
            <!--<label class="radioLabel1" style="vertical-align: top;"><input type="checkbox" id="chkTnC" name="chkTnC">I have read and agree to the <a href="http://www.mindspark.in/terms_conditions.php?mode=<?= $type ?>" target="_blank" style="text-decoration:none;">Terms and conditions</a></label><br/><br/>-->
            <form Method="POST" name="formRegistration" id="formRegistration">
                <input type="hidden" name="emailID" value="<?php echo $emailID; ?>"/>
                <input type="hidden" name="givenName" value="<?php echo $givenName; ?>"/>
                <input type="hidden" name="familyName" value="<?php echo $familyName; ?>"/>
                <input type="hidden" name="openIDMethod" value="<?php echo $openIDMethod; ?>"/>
                Mobile Number<span style="color:red;">*</span>: <input type="text" name="phoneNumber" id="phoneNumber" value="" onkeypress="return contanctKeypress(event)"/><br/><br/>
                <input type="text" name="thepot" class="thepot" />
                <input type="button" class="confirmButton" onclick="javscript:return validate();" id="confirm" name="confirm" value="Confirm And Create This Account" /><a href="http://www.mindspark.in" style="padding:2px; vertical-align: baseline" class="confirmButton" id="confirm1" >Cancel</a><br/><br/>
            </form>
            <i>By confirming, you agree to the <a href="http://www.mindspark.in/terms_conditions.php?mode=<?= $type ?>" target="_blank" style="text-decoration:underline;color:blue">Terms and conditions</a>.</i>
        </div>
    </div>
	
    <div id="bottom_bar" style="position:fixed; width:100%; padding:0px; bottom:0px;">
        <div id="copyright" data-i18n="[html]common.copyright">&copy; 2009-2014, Educational Initiatives Pvt. Ltd.</div>
    </div>   
</body>
</html>