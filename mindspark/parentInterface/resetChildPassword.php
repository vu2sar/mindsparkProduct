<?php
include("header.php");
include("../website/mail_functions.php");
// error_reporting(E_ALL);
// print_r($_SESSION);

$txtPassword="";
$txtConfirmPassword="";

if(isset($_POST['txtPassword'])) {

    $userID = $_SESSION['childIDUsed'];
    $password = $_POST['txtPassword'];
    $success_message = '';

    $getUserQuery = "SELECT userID, username, childName, childEmail  
                     FROM adepts_userDetails 
                     WHERE userID = ".mysql_real_escape_string($userID)." AND enabled = 1"; /*AND endDate >= CURDATE()*/
    $executeUserQuery = mysql_query($getUserQuery);
    if(mysql_num_rows($executeUserQuery) > 0) {
        $rowUser = mysql_fetch_array($executeUserQuery);
        $username = $rowUser['username'];
        $childName = ucwords($rowUser['childName']);
        $childEmail = $rowUser['childEmail'];
        $parentName = ucwords($_SESSION['name']);
        $parentEmail = $_SESSION['openIDEmail'];
    }

    $update_password = "UPDATE educatio_educat.common_user_details SET password = PASSWORD('".$password."') WHERE MS_userID = ".$userID;
    $exec_password = mysql_query($update_password);
    
    if(!empty($_SESSION['openIDEmail'])) 
    {
        mailPassword($parentEmail, $password, $username, $parentName, $childEmail); //forParent
    }

    $success_message = "Your Child's password has been reset successfully.";

}
    
?>
<!DOCTYPE HTML>
<html>
<title>Reset Child's Password</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
<script src="libs/jquery.js"></script>
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="libs/closeDetection.js"></script>
<script>
    var langType = '<?= $language; ?>';
    function load() {
        var sideBarHeight = window.innerHeight - 95;
        var containerHeight = window.innerHeight - 115;
        $("#sideBar").css("height", sideBarHeight + "px");
        /*$("#container").css("height",containerHeight+"px");*/
    }

    function validate()
    {
        var txtPassword = document.getElementById('txtPassword').value;
        var txtConfirmPassword = document.getElementById('txtConfirmPassword').value;

        if (txtPassword == '')
        {
            alert('Please provide a new password.'); 
            return false;
        }

        if (txtPassword.length<5)
        {
            alert('The password should be atleast 5 characters in length.');
            return false;
        }
        if (txtConfirmPassword == '')
        {
            alert('Please enter the new password in the confirm password field too.');
            return false;
        }
        if (txtPassword != txtConfirmPassword)
        {
            alert('The new password and confirmation password do not match.');
            return false;
        }

        setTryingToUnload();
        document.getElementById('frmFP').submit();
    }

</script>
<style>
.frmLbl { font-family: 'Conv_HelveticaLTStd-Cond'; font-size: 14px; }
.loginButton { background-color: #2F99CB !important; color: #000 !important; }
</style>
</head>
<body  onload="load()" onresize="load()">
        <?php include("eiColors.php") ?>
    <div id="fixedSideBar">
<?php include("fixedSideBar.php") ?>
    </div>
    <div id="topBar">
<?php include("topBar.php") ?>
    </div>
    <div id="sideBar">
<?php include("sideBar.php") ?>
    </div>

    <div id="container">        
        <table id="childDetails">
            <td width="33%" id="sectionRemediation" class=""><div class="smallCircle red"></div><label class="textRed" value="secRemediation">RESET CHILD'S PASSWORD</label></a></td>
        </table>
        <?php include('referAFriendIcon.php') ?>
        
    <div id="tab" style="margin-left:40px;">
        <form id="frmFP" name="frmFP" method="POST" autocomplete="off">
        <table width="450" cellpadding="7">
            <tr>
                <td colspan="2" class="frmLbl">
                <span style="color: green; line-height: 16px;"><?php if(!empty($success_message)) echo '<br />'.$success_message; ?></span>
                </td>
            </tr>
            <tr>
                <td width="150" class="frmLbl">Child Name</td>
                <td width="300"><input class="Box" tabindex="1" type="text" disabled name="txtUsername" id="txtUsername" value="<?php echo $_SESSION['childNameUsed']; ?>" /></td>
            </tr>
            <tr>
                <td class="frmLbl">New Password</td>
                <td ><input class="Box" tabindex="1" type="text" name="txtPassword" id="txtPassword" value="<?php echo $txtPassword; ?>" /></td>
            </tr>
            <tr>
                <td class="frmLbl">Confirm Password</td>
                <td><input class="Box" tabindex="4" type="text" name="txtConfirmPassword" id="txtConfirmPassword" /></td>
            </tr>
            <tr>
                <td colspan="2" class="frmLbl">
                <input type="button" class="loginButton" id="btnSubmit" name="btnSubmit" value="Reset Password" onclick="javascript:validate();" />
                </td>
            </tr>
        </table>
        </form>
    </div>

    </div>

<?php include("footer.php"); ?>