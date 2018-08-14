<?php
if (!$innerPage) {
    session_start();
}

//error_reporting(E_ALL);

use BLL;

require_once("../userInterface/check1.php");
require_once("../userInterface/classes/clsUser.php");
if (!$innerPage) {
    require_once 'constants.php';
    require_once "common.php";
}
//require_once DIR_BLL . 'Common.php';
//require_once DIR_BLL . 'UserDetail.php';
//require_once DIR_BLL . 'parentDetails.php';

require_once '../login/loginFunctions.php';
require_once '../website/mail_functions.php';

if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../login/logout.php");
    exit;
}
//error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$msg = '';
$registrationError = false;
$redirect = false;
if ($_POST['newPassword']) {
    $newPassword = $_POST['newPassword'];
    $oldPassword = '';
    if ($innerPage)
        $oldPassword = $_POST['oldPassword'];

//    require_once('/classes/masterConnection.php');
    $parentUserID = $_SESSION['parentUserID'];
//    $parentDetail = new BLL\ParentDetails($db, $parentUserID);
//    $parentDetail->password = $newPassword;
//    $result = $parentDetail->updatePassword();
    $parentCount = 0;
    if ($innerPage == true) {
        $query = "SELECT * from parentUserDetails where password=password('$oldPassword') and parentUserID=$parentUserID";
        $result = mysql_query($query) or die(mysql_error());
        $parentCount = mysql_num_rows($result);
    }
    if ($parentCount == 0 && $innerPage == true) {
        $registrationError = true;
        $msg = "Your current password is incorrect.";
    } else {
        $query = "UPDATE parentUserDetails SET password=password('$newPassword') WHERE parentUserID=$parentUserID";
		mysql_close();
		include(DIR_BASE . "userInterface/dbconf.php");
        $result = mysql_query($query) or die(mysql_error());
		mysql_close();
		include(DIR_BASE . "slave_connectivity.php");
        if ($result == true) {
            if($innerPage != true)
                {sendParentCredentials($_SESSION['openIDEmail'], $newPassword, $_SESSION['firstName'].' '.$_SESSION['lastName']);parentLogin($_SESSION['parentUserID'], false);}//All redirects handled in this function. Everything below is immaterial for the outer page.
            $redirect = true;
            $registrationError = false;
            $msg = "Your password has been changed succesfully.";
            if (!$innerPage)
                $msg .= " You will be redirected to next page shortly.";
            if (isset($_SESSION['arrChildID']) && count($_SESSION['arrChildID']) > 0) {
                $nextPage = 'accountManagement.php';
            } else
                $nextPage = 'childRegistrationOuter.php';
            if ($innerPage != true) {
                header("Location:$nextPage".(isset($_GET['showMessage'])?'?showMessage':''));
                exit;
            }
        } else {
            $registrationError = true;
            $msg = "There was an error while password reset. Please try later or contact Mindspark support at <a href='mailto:mindspark@ei-india.com'>mindspark@ei-india.com</a>.";
        }
    }
}
?>
<meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
<meta
    name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
<script src="libs/jquery-1.9.1.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />

<script src="libs/jquery-ui.min.js"></script>
<link href="css/common.css?ver=1" rel="stylesheet" type="text/css">
<link href="css/childRegistration.css?ver=1" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/ajaxValidation.js"></script>
<script type="text/javascript" src="libs/dateValidatorChildVerification.js"></script>
<script>
    var langType = '<?= $language; ?>';
    var innerPage = '<?php echo $innerPage; ?>'
    function redirect(URL)
    {
        setTryingToUnload();
        window.location = URL;
    }

    function validate()
    {
        var errorMsg = '';
        if (document.getElementById('oldPassword') && document.getElementById('oldPassword').value == '' && innerPage == true)
            errorMsg += 'Please provide your current password.\n';
        if (document.getElementById('newPassword').value == '')
            errorMsg += 'Please provide a new password.\n';
        if (document.getElementById('newPassword').value.length<5)
            errorMsg += "The Password should be atleast 5 characters.\n";
        if (document.getElementById('confirmPassword').value == '')
            errorMsg += 'Please enter the new password in the Confirm Password field too.   \n';
        if (document.getElementById('newPassword').value != document.getElementById('confirmPassword').value)
            errorMsg += 'The new password and confirmation password do not match.';
        if (errorMsg != '')
        {
            alert(errorMsg);
            return false;
        }
        setTryingToUnload();
        document.getElementById('formVerification').submit();
    }

    function echeck(str) {
        var at = "@";
        var dot = ".";
        var lat = str.indexOf(at);
        var lstr = str.length;
        var ldot = str.indexOf(dot);
        if (str.indexOf(at) == -1) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(at) == -1 || str.indexOf(at) == 0 || str.indexOf(at) == lstr) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(dot) == -1 || str.indexOf(dot) == 0 || str.indexOf(dot) == lstr) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(at, (lat + 1)) != -1) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.substring(lat - 1, lat) == dot || str.substring(lat + 1, lat + 2) == dot) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(dot, (lat + 2)) == -1) {
            //alert("Invalid e-mail");
            return false;
        }
        if (str.indexOf(" ") != -1) {
            //alert("Invalid e-mail");
            return false;
        }
        return true;
    }
    function redirect(URL)
    {
        setTryingToUnload();
        window.location = URL;
        return false;
    }
    if (<?= ((($redirect == true) && !$innerPage) ? 'true' : 'false') ?>)
    {
        window.setTimeout(function() {
            setTryingToUnload();
            window.location = 'home.php';
        }, 7000);
    }
</script>

<body  style="height: 100%; vertical-align: central" height="70%" width="70%">

    <!--<div id="imageContainer"></div>-->
    <div class="divVerification" <?php echo ($innerPage ? 'style="font-size: 1.3em;"' : ''); ?>>
        <div>
          <?php if(!$innerPage) { ?>  <h3 style="display: block"><?php echo  'Parent Connect Password'; ?></h3> <?php } ?>
            <span style='color:<?= ($registrationError ? 'red' : 'green') ?>; padding-top:7px; display: <?= $msg != '' ? 'block' : 'none' ?>'><?= $msg ?></span>
        </div>
        <div style='padding-top: 15px; display: block;'>
            <form Method="POST" name='formVerification' id="formVerification" style='padding: 5px' autocomplete="off">

                <table  width="50%" border="0" align="left" style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10pt; border: 1px solid black; padding: 12px; margin-left: 25%;">
<?php if ($innerPage) {
    ?>
                        <tr>
                            <td>Current Password*:</td>
                            <td><input id="oldPassword" name="oldPassword" type="password" size="30" maxlength="30" ></td>
                        </tr>
<?php } ?>
                    <tr>
                        <td>New Password*:</td>
                        <td><input id="newPassword" name="newPassword" type="password" size="30" maxlength="30" ></td>
                    </tr>
                    <tr>
                        <td>Confirm New Password*:</td>
                        <td><input id="confirmPassword" name="confirmPassword" type="password" size="30" maxlength="30"></td>
                    </tr>                                
                    <tr>
                        <td colspan="2">
                            <input type="button" class="loginButton greenButton" id="Change" name="Change" value="Change" onclick='javscript:validate();'/>
                        </td>
                    </tr>

                </table>                
            </form>
        </div>    
    </div>

</body>
<!--</html>-->