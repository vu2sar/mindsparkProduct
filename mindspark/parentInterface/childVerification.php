<?php
error_reporting(E_ALL);
//use BLL;
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../login/logout.php");
    exit;
}
require_once 'constants.php';
require_once DIR_BLL . 'Common.php';
require_once DIR_BLL . 'UserDetail.php';
require_once DIR_BLL . 'parentDetails.php';
require_once DIR_BLL . 'ParentChildMapping.php';
//@include("../userInterface/dbconf.php");
//include_once "../userInterface/classes/clsUser.php";
require_once 'common.php';

error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$msg = '';
$registrationError = false;
$redirect = false;
$setDropDown = FALSE;
if ($_POST['userName']) {
    $studentName = $_POST['studentName'];
    $userName = $_POST['userName'];
    $schoolName = $_POST['schoolName'];
    $childClass = $_POST['childClass'];
    $city = $_POST['city'];
    $dob = $_POST['dob'];
    $dobFormatted = date('Y-m-d', strtotime($dob));
    require_once DIR_CLASSES . 'slaveConnection.php';
    require_once DIR_CLASSES . 'masterConnection.php';
    $registrationError = false;
    //Add child verification information provided by user for analysis
    $common = new BLL\Common($db);
    $resultChildVerificationInfo = $common->addChildVerificationDetail($_SESSION['sessionID'], $studentName, $userName, $schoolName, $childClass, $city, $dobFormatted);
    //Search for user for provided info
    $userDetail = new BLL\UserDetail($sdb);
    $childUser = $userDetail->searchUsers($userName, $childClass, $dobFormatted);

    if (count($childUser) > 0) {
        $parentDetail = new BLL\ParentDetails($sdb, $_SESSION['parentUserID']);
        $childrenMapped = $parentDetail->getChildrenMapped();
        $childFilter = array_filter($childrenMapped, function($obj) use($childUser) {
            if ($obj['childUserID'] == $childUser[0]['userID'])
                return true;
        });
        if (count($childFilter) > 0) {
            $msg .= "Your account is already registered with user $userName.";
            $registrationError = true;
        }
        $childUser = $childUser[0];
        $parentEmail = $childUser['parentEmail'];

//        $secondaryParentEmail = $childUser['secondaryParentEmail'];
//        $secondayrParentEmailArray = explode(',', $secondaryParentEmail);
        if (count($childrenMapped) >= 5) {
            $msg .= "You have reached your limit of mapping upto 5 children with your account. You can not attach any other child with your account.";
            $registrationError = true;
        }
        if (!$registrationError) {
            $userDetail->db = $db;

            if ($parentEmail == '')
                $resultRegistration = $userDetail->updateParentEmail($_SESSION['openIDEmail'], $childUser['userID']);

            $parentChildMapping = new BLL\ParentChildMapping($db);
            $parentChildMapping->parentUserID = $_SESSION['parentUserID'];
            $parentChildMapping->childUserID = $childUser['userID'];
            $resultRegistration = $parentChildMapping->addUserMapping();
            if ($resultRegistration === false) {
                $msg .= "There was an error while mapping this child. Please try again. If issue persists please write to <a href='mailto:mindspark@ei-india.com'>mindspark@ei-india.com</a> with following details:";
                $msg .= "<ol><li>";
                $msg .= "Mindspark username </li><li>";
                $msg .= "Class of the student </li><li>";
                $msg .= "Date of Birth of the student </li></ol>";
                $registrationError = true;
            } else {
                $msg .= 'Your child\'s details are successfully mapped to your account(' . $_SESSION['openIDEmail'] . '). ';
                if (!$innerStudentMapPage)
                    $msg.='Click on one of the options below to proceed further: 
                    <br><br>    <input type="button" class="greenButton" id="option2" name="option2" value="Go to the Parent Connect" onclick="javascript:optionRedirect(2);"/>&nbsp;
                                <input type="button" class="greenButton" id="option3" name="option3" value="Login to '.$userName.'\'s account " onclick="javascript:optionRedirect(3);"/>&nbsp;';
                $common->addChangeLog('adepts_userDetails', 'parentEmail', $childUser['userID'], '', $_SESSION['openIDEmail'], 'Parent Interface:Child verification', $_SESSION['openIDEmail']);
                $redirect = true;
                $parentDetail->db=$db;
                $childrenMapped = $parentDetail->getChildrenMapped();
                if (count($childrenMapped) > 0) {
                    setSesssionValues($childrenMapped);
                    $userIDSelected = $childUser['userID'];
                    $childSelected = array_filter($childrenMapped, function($obj) use($userIDSelected) {
                        if ($obj['childUserID'] == $userIDSelected)
                            return true;
                        else
                            return false;
                    });
                    $childSelected = array_values($childSelected);
                    $childSelected1 = $childSelected[0];
                    $childSelected = new BLL\UserDetail($sdb, $childSelected1->childUserID);
                    if (!$innerStudentMapPage) {
//                        $childSelected = $childSelected[0];
                        $_SESSION['childIDUsed'] = $childSelected->userID;
                        $_SESSION['childNameUsed'] = $childSelected->childName;
                        $_SESSION['childClassUsed'] = $childSelected->childClass;
                    } else {
                        unset($_SESSION['childIDUsed']);
                        $setDropDown = true;
                        
                        $childUserID = $childUser['userID'];
                        $childNameNew = $childUser['childName'];
                        unset($studentName);
                        unset($userName);
                        unset($schoolName);
                        unset($childClass);
                        unset($city);
                        unset($dob);
                        unset($dobFormatted);
                    }
                }
            }
        }
    } else {
        $registrationError = true;
        $msg .= "We could not track the student in the Mindspark database based on the username and other details submitted. You may-<br/>";
        $msg .= "1. Recheck the details submitted, make necessary changes and retry.<br/>";
        $msg .= "Or<br/>";
        $msg .= "2. Email us the following details at <a href='mailto:mindspark@ei-india.com'>mindspark@ei-india.com</a>";
        $msg .= "<ul> <li>Username</li><li>Class</li><li>DOB</li><li>School Name</li><li>City</li> </ul>";
    }
    $disableRegister = (!$registrationError && $msg != '' && !$innerStudentMapPage);
//        $registrationError = true;
//        $msg = 'Please provide username.';
}

//function setSesssionValues($childrenMapped) {
////    session_start();
//    require 'constants.php';
//    require DIR_CLASSES . 'slaveConnection.php';
////    global $sdb;
//    $childID = [];
//    $i = 0;
//    foreach ($childrenMapped as $child1) {
//        $childID[$i] = $child1['childUserID'];
//        $child[$i] = new BLL\UserDetail($sdb, $childID[$i]);
//        $childName[$i] = $child[$i]->childName;
//        $childFreeTrial[$i] = $child1['freeTrial'];
//        if ($i == 0) {
//            $childSelected = $child[$i];
//            $_SESSION['childID'] = $childSelected->userID;
//            $_SESSION['childIDUsed'] = $childSelected->userID;
//            $_SESSION['childNameUsed'] = $childSelected->childName;
//            $_SESSION['childClassUsed'] = $childSelected->childClass;
//            $_SESSION['childSubcategory'] = $childSelected->subcategory;
//            $_SESSION['packageExpiryDate'] = $childSelected->endDate;
//        }
//        $i++;
//    }
//    $_SESSION['arrChildID'] = $childID;
//    $_SESSION['arrChildName'] = $childName;
//    $_SESSION['arrChildFreeTrial'] = $childFreeTrial;
//}
?>
<title>Parent e-mail registration</title>
<meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
<meta
    name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
<script src="libs/jquery-1.9.1.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />

<script src="libs/jquery-ui.min.js"></script>
<link href="css/common.css?ver=1" rel="stylesheet" type="text/css">
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/dateValidatorChildVerification.js"></script>
<script>
    var langType = '<?= $language; ?>';
    function redirect(URL)
    {
        setTryingToUnload();
        window.location = URL;
    }
    $(function() {
        $(".datepicker").datepicker({changeMonth: true, changeYear: true, yearRange: '1995:<?= date("Y") ?>', dateFormat: 'dd-mm-yy'});
    });
    function openCalender(id) {
        var id = id;
        if (id == "from") {
            $("#dob").focus();
        }
    }
    function DateFormat(txt, keyCode)
    {
        if (keyCode == 16)
            isShift = true;
        //Validate that its Numeric
        if (((keyCode >= 48 && keyCode <= 57) || keyCode == 8 ||
                keyCode <= 37 || keyCode <= 39 ||
                (keyCode >= 96 && keyCode <= 105)) && isShift == false)
        {
            if ((txt.value.length == 2 || txt.value.length == 5) && keyCode != 8)
            {
                txt.value += seperator;
            }
            return true;
        }
        else
        {
            return false;
        }
    }
    function validate()
    {
        errorMsg = '';
        if (document.getElementById('userName').value == '')
            errorMsg += 'Please provide the username.\n';
        if (document.getElementById('selGrade').value == '')
            errorMsg += 'Please provide the class.\n';
        if (document.getElementById('dob').value == '')
            errorMsg += 'Please provide the date of birth.\n';
        else
        {
            var dobValidation = isDate(document.getElementById('dob').value);
            var isDobValid = (dobValidation === 'true');
            if (!isDobValid)
                errorMsg += dobValidation + ' for date of birth.\n';
            var dob = getDateObject(document.getElementById('dob').value, "-");
            var currentDate = new Date();
            if (currentDate < dob)
                errorMsg += 'Date of birth can not be greater than current date.';
        }
        if (errorMsg != '')
        {
            alert(errorMsg);
            return false;
        }
        setTryingToUnload();
        document.getElementById('formVerification').submit();
    }
    function getDateObject(dateString, dateSeperator)
    {
        //This function return a date object after accepting a date string and dateseparator as arguments
        var curValue = dateString;
        var sepChar = dateSeperator;
        var curPos = 0;
        var cDate, cMonth, cYear;
        //extract date portion
        curPos = dateString.indexOf(sepChar);
        cDate = parseInt(dateString.substring(0, curPos), 10);

        //extract month portion
        endPos = dateString.indexOf(sepChar, curPos + 1);
        cMonth = parseInt(dateString.substring(curPos + 1, endPos), 10);

        //extract date portion
        curPos = endPos;
        //endPos=curPos+5;
        cYear = parseInt(curValue.substring(curPos + 1), 10);

        //Create Date Object
        dtObject = new Date();
        dtObject.setFullYear(cYear, cMonth - 1, cDate);

        return dtObject;
    }
    
</script>

<body  style="height: 100%; vertical-align: central" height="100%">

    <div class="divVerification" <?php echo ($innerStudentMapPage ? 'style="font-size: 1.3em;"' : ''); ?>>
        <div>
            <span style="<?php echo ($disableRegister ? 'display:none;' : 'display: block;'); ?>"> Please share the following information to register your email ID.</span>
            <span style='color:<?= ($registrationError ? 'red' : 'green') ?>; padding-top:7px; display: <?= $msg != '' ? 'block' : 'none' ?>'><?= $msg ?></span>
        </div>
        <div style='padding-top: 15px; display: block;'>
            <form Method="POST" name='formVerification' id="formVerification" style="border: solid 1px black; padding: 5px; <?php echo ($disableRegister ? 'display:none;' : ''); ?>">
                <script>
                    function redirect(URL)
                    {
                        setTryingToUnload();
                        window.location = URL;
                        return false;
                    }
<?php if ($setDropDown) { ?>
                        $(document).ready(function() {
                            $('#childSelectedID').append('<option value="<?= $childUserID ?>"><?= $childNameNew ?></option>');
                            $("#childSelectedID").prop("disabled", false);
                        });
<?php } ?>
                    if (<?= ((($redirect == true) && !$innerStudentMapPage) ? 'true' : 'false') ?>)
                    {
                        window.setTimeout(function() {
                            setTryingToUnload();
                            // window.location = 'accountManagement.php';
                        }, 7000);
                    }

                    function optionRedirect(i) {

                        switch(i){

                            case 1: 
                            setTryingToUnload();
                            window.location = 'childregistrationOuter.php';
                            break;

                            case 2: 
                            setTryingToUnload();
                            window.location = 'accountManagement.php?showMessage';
                            break;

                            case 3: 
                            setTryingToUnload();
                            window.location = '../logout.php';
                            break;

                            default: 
                            break;

                        }


                    }


                </script>

                <table width='100%'>
                    <tr>
                        <td width='15%'>
                            Student's Name: 
                        </td>
                        <td width='25%'>
                            <input type="text" class="Box" name="studentName" maxlength="100" id="studentName" value="<?= $studentName ?>" size='30'></input>
                        </td>
                        <td width='15%'>
                            Mindspark Username<span class="mandatory">*</span>: 
                        </td>
                        <td width='35%'>
                            <input type="text" class="Box" name="userName" maxlength="50" id="userName" value="<?= $userName ?>" size='30'></input>
                        </td>
                    </tr>
                    <tr>
                        <td width='10%'>
                            School Name: 
                        </td>
                        <td width='25%'>
                            <input type="text" class="Box" name="schoolName" maxlength="100" id="schoolName" value="<?= $schoolName ?>" size='30'></input>
                        </td>
                        <td width='25%'>
                            Class<span class="mandatory">*</span>: 
                        </td>
                        <td width='25%'>
                            <select name="childClass" id="selGrade">
                                <option value="">Select</option>
<?php
for ($i = 1; $i <= 10; $i++) {
    echo "<option value='$i'  " . ($childClass == $i ? " selected" : '') . ">$i</option>";
}
?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width='10%'>
                            City: 
                        </td>
                        <td width='25%'>
                            <input type="text" class="Box" name="city" maxlength="50" id="city" value="<?= $city ?>" size='30'></input>
                        </td>
                        <td width='15%'>
                            Student's Date of Birth<span class="mandatory">*</span>:<br/>
                            (As registered with Mindspark)
                        </td>
                        <td    width='35%' >
                            <input type="text" name="dob" class="datepicker floatLeft" id="dob" value="<?= $dob ?>" autocomplete="off" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" size="15"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='4'>&nbsp;
                            
                        </td>
                    </tr>
                    <tr >
                        <td></td>
                        <td colspan='3'>
                            <input type="button" class="loginButton" id="verify" name="verify" value="Verify" onclick='javscript:validate();'/>
<?php if (!$innerStudentMapPage) { ?>
                                <input type="button" class="loginButton" id="verifyCancel" name="verifyCancel" value="Cancel" onClick="javascript:redirect('../logout.php')"/>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>    
    </div>

</body>
<!--</html>-->