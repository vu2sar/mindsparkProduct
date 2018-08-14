<?php
session_start();

//echo dirname(dirname(__FILE__)).'/parentInterface/ChildCredential.html';
//exit;
//error_reporting(E_ALL);
use BLL;

require_once 'constants.php';
require_once DIR_BLL . 'Common.php';
require_once DIR_BLL . 'UserDetail.php';
require_once DIR_BLL . 'parentDetails.php';
require_once DIR_BLL . 'ASSETOnline.php';
include("../userInterface/check1.php"); 
require_once '../common/functions/sendSMS.php';
include_once "common.php";
if (!isset($_SESSION['openIDEmail'])) {
    header("Location:../login/logout.php");
    exit;
}

error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING);
$msg = '';
$registrationError = false;
$redirect = false;
$setDropDown = false;
if ($_POST['username']) { 
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $childClass = $_POST['childClass'];
    $schoolName = $_POST['schoolName'];
    $city = $_POST['city'];
    $dob = $_POST['dob'];
    $board = $_POST['board'];
    if ($board == 'Other')
        $board = $_POST['txtBoard'];
    $homeSchool = $_POST['homeSchool'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $password = $_POST['password']; //for password given by user new
//    $upgrade_month = $_POST['upgrade_month'];
    $classUpgradationDateDisp = $_POST['dateUpgradation'];
//    $relation = $_POST['relation'];
//    if($relation=='Other')
//        $relation = $_POST['txtRelation'];
    $dobFormatted = date('Y-m-d', strtotime($dob));
    $parentEmail = $_SESSION['openIDEmail'];
    $registrationError = false;
    require_once('../parentInterface/classes/slaveConnection.php');
    $common = new BLL\Common($sdb);
    $emailCheck = $common->checkIfEmailExist($username);
    $parentUserID = $_SESSION['parentUserID'];
    $parentDetail = new BLL\ParentDetails($sdb, $parentUserID);
    $childrenMapped = $parentDetail->getChildrenMapped();
    if (count($childrenMapped) < 5) {
        if ($emailCheck == 0) {
                require_once('../parentInterface/classes/masterConnection.php');
            $userDetail = new BLL\UserDetail($db);  
            $userDetail->firstName = $firstName;
            // $password = generatePassword(6);//commented for password given by user new
            $userDetail->password = $password;
            $userDetail->lastName = $lastName;
            $userDetail->parentName = $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
            $userDetail->childClass = $childClass;
            $userDetail->schoolName = $schoolName;
            $userDetail->city = $city;
            $userDetail->childDob = $dobFormatted;
            $userDetail->gender = $gender;
            $userDetail->board = $board;
            $userDetail->homeSchool = $homeSchool;
            $userDetail->contactno_cel = $parentDetail->phoneNumber; 
//            $userDetail->childEmail = $childEmail;
//            $userDetail->upgrade_month = $upgrade_month; 
            $date = explode('-', $classUpgradationDateDisp);
            $classUpgradationDate = $date[2].'-'.$date[1].'-'.$date[0];
            $userDetail->classUpgradationDate = $classUpgradationDate;
            $userDetail->username = $username;
            $userDetail->parentEmail = $parentEmail;
            $userDetail->category = 'STUDENT';
            $userDetail->subcategory = 'Individual';
            $userDetail->updated_by = $parentEmail;
            $date = strtotime(date("Y-m-d"));
            $date = strtotime("+6 day", $date);
            $userDetail->endDate = date("Y-m-d", $date);
            $userDetail->type = 'F'; 
            $userID = $userDetail->addUser();
            $asset = new BLL\ASSETOnline($db);
            $asset->MSUserID = $userID;
            $asset->username = $username;
            $asset->password = $password;
            $asset->fatherName = $_SESSION['firstName'] . ' ' . $_SESSION['lastName'];
            $asset->childFirstName = $firstName;
            $asset->childLastName = $lastName;
            $asset->class = $childClass;
            $asset->gender = ($gender=='G'?'Girl':'Boy');
            $asset->schoolName = $schoolName;
            $asset->cityName = $city;
//$asset->country='India';
            $asset->dob = $dobFormatted;
            $asset->phoneMob = $parentDetail->phoneNumber;
            $asset->email = $parentEmail;
            $asset->enteredBy = $parentEmail; 
            $assetUsername = $asset->insertUser(); 
            $parentDetail->db = $db;
//            $parentDetail->mapChild($userID, FALSE, $relation);
            $parentDetail->mapChild($userID, FALSE);
            $registrationError = FALSE;
            $parentName = $parentDetail->firstName . ' ' . $parentDetail->lastName;
            $childName = $userDetail->firstName . ' ' . $userDetail->lastName;
//            var_dump($userDetail);
            childCredentialMail($parentEmail, $parentName, $childName, $userDetail->firstName, $userDetail->username, $userDetail->password, $userDetail->gender, $userDetail->childClass);
            if ($parentDetail->phoneNumber != '') {
                $smsText = "Congratulations on registering with Mindspark! Your child's Mindspark login credentials are emailed to you. Write to mindspark@ei-india.com for any query.";
                SendSMS($parentDetail->phoneNumber, $smsText);
            }
            $msg = "We have signed up your child for 7 day free trial.  Mindspark login credentials of $childName is emailed to you. Please check the email. ";
            if (!$innerStudentMapPage)
                $msg.='Click on one of the options below to proceed further: 
                    <br><br>    <input type="button" class="loginButton btn btn-success" id="option2" name="option2" value="Go to the Parent Connect" onclick="javascript:optionRedirect(2);"/>&nbsp;
                                <input type="button" class="loginButton btn btn-success" id="option3" name="option3" value="Login to '.$childName.'\'s account " onclick="javascript:optionRedirect(3);"/>&nbsp;';
            
            $redirect = TRUE;
            $childrenMapped = array(
                0 => array("childUserID" => $userID, "freeTrial" => 1)
            );
            setSesssionValues($childrenMapped);
            if ($innerStudentMapPage) {
                unset($_SESSION['childIDUsed']);
                $setDropDown = true;
                $childUserID = $userID;
                $childNameNew = $childName;
                unset($studentName);
                unset($userName);
                unset($username);
                unset($schoolName);
                unset($childClass);
                unset($city);
                unset($dob);
                unset($firstName);
                unset($lastName);
                unset($dobFormatted);
                unset($gender);
                unset($board);
                unset($homeSchool);
            }
        } else {
            $registrationError = TRUE;
            $msg = "Username already in use please choose another username.";
        }
    } else {
        $registrationError = TRUE;
        $msg = "You have already registered maximum number of children allowed(upto 5).";
    }
    $disableRegister = (!$registrationError && $msg != '' && !$innerStudentMapPage);
}

function childCredentialMail($parentEmail, $parentName, $childName, $firstName, $username, $password, $gender, $class) {
    $subject = "Mindspark Account Details";
    $headers .= "From:mindspark@ei-india.com\r\n";
    $headers .= "Bcc:notification@ei-india.com\r\n";
    $headers .= "Reply-to:mindspark@ei-india.com\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $hisHer = 'his/her';
    $heShe = 'he/she';
    if ($gender == 'B') {
        $hisHer = 'his';
        $heShe = 'he';
    } else if ($gender == 'G') {
        $hisHer = 'her';
        $heShe = 'she';
    }
    if ($class < 3 || $class == 10) {
        $showASSEET = "";
    } else {
        $showASSEET = "<div><b>ASSET Question-A-Day (AQAD) - will be made freely available to you to try for 1 year!</b><br/>ASSET Question-a-day are thought provoking questions designed by team of educational experts at EI. These questions are aimed at providing greater exposure to application orientated questions. Access these questions from Mindspark student homepage and the Parent Connect. (Note: AQAD is available for classes 3 to 9)<br/><br /></div>";
    }
    $body = file_get_contents(dirname(dirname(__FILE__)) . '/parentInterface/ChildCredential.html');
    $body = str_replace('[ParentName]', formatName($parentName), $body);
    $body = str_replace('[ChildName]', formatName($childName), $body);
    $body = str_replace('[ChildFirstName]', formatName($firstName), $body);
    $body = str_replace('[ChildUsername]', $username, $body);
    $body = str_replace('[Password]', $password, $body);
    $body = str_replace('[His/Her]', $hisHer, $body);
    $body = str_replace('[He/She]', $heShe, $body);
    $body = str_replace('[ASSETDisplay]', $showASSEET, $body);
    $body = wordwrap($body, 70);
     // echo $body;
     //   exit;
    $success = 0;
    if ($parentEmail != "") {
        $success = mail($parentEmail, $subject, $body, $headers);
    }
    if ($success != 0) {
//		insert(16,$parentEmail,"","notification@ei-india.com","mindspark@ei-india.com","",1);
    } else {
//		insert(16,$parentEmail,"","notification@ei-india.com","mindspark@ei-india.com","",0);
    }
}

function formatName($name) {
    $nameArray1 = explode(' ', $name);
    $nameArray2 = array_map('strtolower', $nameArray1);
    $nameArray = array_map('ucfirst', $nameArray2);
    $name = implode(' ', $nameArray);
    return $name;
}


?>
<meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
<meta
    name="viewport"
    content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
<script src="libs/jquery-1.9.1.min.js"></script>
<script src="libs/jquery-ui.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/ajaxValidation.js"></script>
<script type="text/javascript" src="libs/dateValidatorChildVerification.js"></script>
<style>.ui-datepicker-calendar{color:black !important;}</style> <!--Overriding all other css for calendar color-->
<script>
    var langType = '<?= $language; ?>';
    function redirect(URL)
    {
        setTryingToUnload();
        window.location = URL;
    }
    $(function() {
        $( "#dateUpgradation" ).datepicker({changeMonth: true, changeYear: true, yearRange: '<?php echo date('Y');?>:2020', dateFormat: 'dd-mm-yy'});
    });
    $(function () {
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
        var errorMsg = '';
        if (document.getElementById('firstName').value == '')
            errorMsg += 'Please provide child\'s first name.\n';
        if (document.getElementById('lastName').value == '')
            errorMsg += 'Please provide child\'s last name.\n';
        if (document.getElementById('selGrade').value == '')
            errorMsg += 'Please provide the class.\n';
        if (document.getElementById('board').value == '')
            errorMsg += 'Please provide the board.\n';
        else if (document.getElementById('board').value == 'Other')
        {
            if (document.getElementById('txtBoard').value == '')
                errorMsg += 'Please provide the board name.\n';
        }
        if (document.getElementById('homeSchool').value == '')
            errorMsg += 'Please provide the home schooling information.\n';
        else if (document.getElementById('homeSchool').value == '0')
        {
            if (document.getElementById('schoolName').value == '')
                errorMsg += 'Please provide the school name.\n';
        }
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
        if (document.getElementById('dateUpgradation').value == '')
            errorMsg += 'Please provide the class change date.\n';
        else
        {
            var dateUpgradationValidation = isDate(document.getElementById('dateUpgradation').value);
            var isdateUpgradationValid = (dateUpgradationValidation === 'true');
            if (!isdateUpgradationValid)
                errorMsg += dateUpgradationValidation + ' for class change date.\n';
            var dateUpgradation = getDateObject(document.getElementById('dateUpgradation').value, "-");
            var currentDate = new Date();
            if (currentDate > dateUpgradation)
                errorMsg += 'Date of class change can not be lesser than current date.';
        }
        if (document.getElementById('txtUsername').value == '')
            errorMsg += "Please provide desired username.\n";
        else if (document.getElementById('txtUsername').value.length<5)
            errorMsg += "Username should be atleast 5 characters.\n";

        if (document.getElementById('password').value == '')
            errorMsg += "Please provide a password.\n";
        else if (document.getElementById('password').value.length<5)
            errorMsg += "Password should be atleast 5 characters.\n";

        if (document.getElementById('password').value != document.getElementById('confirmPassword').value)
            errorMsg += 'The new password and confirmation password do not match.';
//        var dateUpgradation = trim(document.getElementById('dateUpgradation').value);
//        if (dateUpgradation == "")
//            errorMsg += "Please specify the date on which you want to upgrade to higher class.\n"; 
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
    function showHideOther(board)
    {
        if (board == "Other")
            document.getElementById('pnlBoardOther').style.display = "inline";
        else
            document.getElementById('pnlBoardOther').style.display = "none";
    }
    function showHideOtherRelation(relation)
    {
        if (relation == "Other")
            document.getElementById('pnlRelationOther').style.display = "inline";
        else
            document.getElementById('pnlRelationOther').style.display = "none";
    }
    function showHideSchoolName(homeSchool)
    {
        if (homeSchool == "0")
            $('#trSchoolName').show();
        else
            $('#trSchoolName').hide();
    }
    $(document).ready(function () {
        $('#trSchoolName').hide();
    });
<?php if ($setDropDown) { ?>
        $(document).ready(function () {
            $('#childSelectedID').append('<option value="<?= $childUserID ?>"><?= $childNameNew ?></option>');
            $("#childSelectedID").prop("disabled", false);

        });
<?php } ?>
    if (<?= ((($redirect == true) && !$innerStudentMapPage) ? 'true' : 'false') ?>)
    {   
        window.setTimeout(function () {
            setTryingToUnload();
            //window.location = '../parentApp/index.php'; 
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


    function textonly(e) {
        var code;
        if (!e)
            var e = window.event;
        if (e.keyCode)
            code = e.keyCode;
        else if (e.which)
            code = e.which;
        var character = String.fromCharCode(code);
//alert('Character was ' + character);
        //alert(code);
        //if (code == 8) return true;
        var AllowRegex = /^[\ba-zA-Z\s-]$/;
        if (AllowRegex.test(character))
            return true;
        return false;
    }
    function blockSpecialChar(e) {
        var code;
        if (!e)
            var e = window.event;
        if (e.keyCode)
            code = e.keyCode;
        else if (e.which)
            code = e.which;
        var character = String.fromCharCode(code);
        //alert('Character was ' + character);
        //alert(code);
        //if (code == 8) return true;
        var AllowRegex = /^[\bA-Za-z0-9_.-]$/;
        if (AllowRegex.test(character))
            return true;
        return false;
    }
    function showClassChangeAlert()
    {
    //    alert("This information helps Mindspark always track your class level correctly.\nMention the month when the new academic year starts (usually April, or June, or July in India).");
        alert("This information helps Mindspark always track your class level correctly. Mention the date when the new academic year starts (usually April, or June, or July in India). Class will be automatically upgraded on the selected date.");
        return false;
    }
</script>

<body  style="height: 100%; vertical-align: central" height="70%" width="70%">

<div class="row">
                  <div class="col-lg-6">
                      <section class="panel">
                          <header class="panel-heading"><br/><br/>
                          Child Registration
                          </header>
                          <div class="divVerification panel-body" <?php echo ($innerStudentMapPage ? 'style="font-size: 1.3em;"' : ''); ?> >
                              <span style='color:<?= ($registrationError ? 'red' : '#78CD51') ?>; padding-top:7px; display: <?= $msg != '' ? 'block' : 'none' ?>'><?= $msg ?></span>
                              <form Method="POST" name='formVerification' id="formVerification" style="padding: 5px; <?php echo ($disableRegister ? 'display:none;' : ''); ?>">
                                      
                                <?php echo $innerStudentMapPage; if (!$innerStudentMapPage) {
                                    ?>

                                <div class="form-group">
                                                       
                                <a href="childVerificationOuter.php" class="linkClass" style="color:fff;"><u>Click here if your child is already using Mindspark.</u></a>
                                                                        
                                </div>

                                <?php } ?> 


                                  <div class="form-group">
                                      <label for="InputFirstName">Child's First Name*</label>
                                      <input id="firstName" name="firstName" type="text" size="30" maxlength="100" value="<?php echo $firstName; ?>" class="form-control" placeholder="First Name"  onKeyPress="return textonly(event);" style="display:inline-block;">
                                  </div>

                                  <div class="form-group">  
                                      <label for="InputLastName">Child's Last Name*</label>
                                      <input id="lastName" name="lastName" type="text" size="30" maxlength="100" value="<?php echo $lastName; ?>" class="form-control" placeholder="Last Name"  onKeyPress="return textonly(event);" style="display:inline-block; ">

                                  </div>
                                                                                               
                                  <div class="form-group">
                                    <label for="InputClass">Class*</label>
                                    <select class="form-control" name="childClass" id="selGrade" style="display:inline-block; width:30%;">
                                        <option value="">Select</option>
                                            <?php
                                            for ($i = 1; $i <= 10; $i++) {
                                                echo "<option value='$i'  " . ($childClass == $i ? " selected" : '') . ">$i</option>";
                                            }
                                            ?>
                                    </select>

                                    <label for="InputBoard">Board*</label> 
                                    <select class="form-control" name="board" id="board" class="textBoxes form-control" onchange="showHideOther(this.value);" style="display:inline-block; width:30%;">
                                        <option value="">Select</option>
                                        <option value="CBSE"  <?php if ($board == "CBSE") echo " selected"; ?>>CBSE</option>
                                        <option value="State"  <?php if ($board == "State") echo " selected"; ?>>State</option>                                  
                                        <option value="IGCSE"  <?php if ($board == "IGCSE") echo " selected"; ?>>IGCSE</option>
                                        <option value="ICSE"  <?php if ($board == "ICSE") echo " selected"; ?>>ICSE</option>                                  
                                        <option value="IB"  <?php if ($board == "IB") echo " selected"; ?>>IB</option>
                                        <option value="Matriculation"  <?php if ($board == "Matriculation") echo " selected"; ?>>Matriculation</option>                                  
                                        <option value="NCERT"  <?php if ($board == "NCERT") echo " selected"; ?>>NCERT</option>
                                        <option value="Other"  <?php if ($board == "Other") echo " selected"; ?>>Other</option>                                  
                                    </select>
                                  </div> 

                                  <div class="form-group" style="display:none" id="pnlBoardOther">
                                        Please Specify: <input type="text" id="txtBoard" style="width:100px;" name="txtBoard">
                                  </div>

                                  <div class="form-group">
                                    <label for="InputHomeSchooling">Home-Schooling?*</label><br/> 
                                    <select name="homeSchool" id="homeSchool" id="homeSchool" class="textBoxes form-control" onchange="showHideSchoolName(this.value);">
                                        <option value="">Select</option>
                                        <option value="1"  <?php if ($homeSchool == "1") echo " selected"; ?>>Yes</option>
                                        <option value="0"  <?php if ($homeSchool == "0") echo " selected"; ?>>No</option>                                  
                                    </select>
                                  </div>
                                 
                                 <div class="form-group" id="trSchoolName">
                                 <label for="InputSchoolName">School Name*</label> 
                                 <input id="schoolName" name="schoolName" type="text" size="30" maxlength="100" class="textBoxes form-control" value="<?php echo $schoolName; ?>">
                                 </div>

                                 <div class="form-group">
                                 <label for="InputCity">City</label> 
                                 <input id="city" name="city" type="text" size="30" maxlength="100" class="textBoxes form-control" value="<?php echo $city; ?>">
                                 </div>

                                 <div class="form-group">
                                 <label for="InputDateOfBirth">Date of Birth:*</label>
                                 <input type="text" name="dob" class="datepicker floatLeft form-control" id="dob" value="<?= $dob ?>" autocomplete="off" onKeyDown="return DateFormat(this, event.keyCode)" maxlength="10" size="15"/><div class="calenderImage linkPointer" id="from" onClick="openCalender(id)"></div>
                                 </div>

                                <div class="form-group"> 
                                <label for="InputGender">Gender*</label>    
                                <select name="gender" style="width:190px;" id="gender" class="textBoxes form-control">
                                    <option value="">Select</option>
                                    <option value="B"  <?php if ($gender == "B") echo " selected"; ?>>Boy</option>
                                    <option value="G"  <?php if ($gender == "G") echo " selected"; ?>>Girl</option>                                  
                                </select>
                                </div>

                                <div class="form-group">
                                    <label for="InputUsername">Desired Username*</label> 
                                    <input class="textBoxes usingPlaceHolder form-control" type="text" name="username" id="txtUsername"  style="width:180px;" size="40" maxlength="100" onBlur="ajaxValidation(this.value, true);" value="<?php echo $username; ?>" onKeyPress="return blockSpecialChar(event);"/>
                                    <input type="hidden" name="existsEmail" id="existsEmail" value="0">
                                    <div id='divError' style='display:inline'>
                                        <span class="errormsg" id="errMsg" style="display:none;">Username already exists!</span>
                                        <span class="errormsg" id="errMsg2" style="display:none;">Username address already registered, please use a different Username!</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="InputPassword">Password*</label> 
                                    <input id="password" name="password" type="password" style="width:180px;" size="40" maxlength="40" class="textBoxes form-control" value="<?php echo $password; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="InputConfirmPassword">Confirm Password*</label> 
                                    <input id="confirmPassword" name="confirmPassword" type="password" style="width:180px;" size="40" maxlength="40" class="textBoxes form-control" value="<?php echo $password; ?>">
                                </div>
                                
                                <div class="form-group">

                                    <label for="InputClassChangesIn">My class changes in*</label><a href="#" onClick="return showClassChangeAlert();" tabindex="-1"><img src="assets/WhatsThis.png" id="imgWhatsThis"></a>                    
                                    <input class="textBoxes usingPlaceHolder form-control" type="text" id="dateUpgradation" name="dateUpgradation" value="<?php echo $classUpgradationDateDisp; ?>" style="width:180px;">&nbsp;<a href="#" onClick="return showClassChangeAlert();" tabindex="-1"></a>
                                    
                                </div>

                                    <input type="button" class="loginButton btn btn-success greenButton <?php echo ($disableRegister ? 'buttonDisabled' : ''); ?>" id="Register" name="Register" value="Register" onclick='javscript:validate();' <?php echo ($disableRegister ? 'disabled' : ''); ?>/>  
                             </form>

                              </div>
                          </section>
                      </div>  
        </div> 
</body>
<!--</html>-->