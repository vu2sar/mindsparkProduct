<?php
    set_time_limit(0);   //Otherwise quits with "Fatal error: Maximum execution time of 30 seconds exceeded"
    
    @include("check1.php");
    include("constants.php");
    include("classes/clsUser.php");
    $usernameMS=isset($_REQUEST['username'])?$_REQUEST['username']:"";
	$configKey=isset($_REQUEST['configKey'])?$_REQUEST['configKey']:"";

    //added by nivedita
    $browserName    = isset($_REQUEST['browserName'])?$_REQUEST['browserName']:"";
    $browserVersion = isset($_REQUEST['browserVersion'])?$_REQUEST['browserVersion']:"";
    $osDetails      = isset($_REQUEST['osDetails'])?$_REQUEST['osDetails']:"";
    //end

    if (isset($_GET['image1'])) {
        $image1 = $_GET['image1'];
    }
    
    if (isset($_GET['image2'])) {
        $image2 = $_GET['image2'];
    }
    $errorMsg = '';
    $nextPage = $_POST['nextPage'];
    if (isset($_POST['usernameEI'])) {
        $username = $_POST['usernameEI'];
        $password = $_POST['password'];
        $reason =$_POST['reason'];
		$query = "SELECT name FROM educatio_educat.marketing WHERE name='$username' AND password=PASSWORD('$password')";
        $userid_result = mysql_query($query) or die(mysql_error());

        if (mysql_num_rows($userid_result) > 0) {
            	$ms_user_details = "";
				$ms_parent_id = "";
                $mse_user_details = "";
				if($configKey=="masterPasswordParent")
				{
					$queryUserID = "SELECT parentUserID from educatio_adepts.parentUserDetails where username='$usernameMS'";
					$resultUserID = mysql_query($queryUserID);
					$ms_parent_id_arr = mysql_fetch_assoc($resultUserID);
					$ms_parent_id = $ms_parent_id_arr["parentUserID"];
				}
				else
				{

					$queryUserID = "SELECT MS_userID,MSE_userID from educatio_educat.common_user_details where username='$usernameMS'";
					$resultUserID = mysql_query($queryUserID);
					$ms_user_details_arr = mysql_fetch_assoc($resultUserID);
					$ms_user_details = $ms_user_details_arr["MS_userID"];
                    $mse_user_details = $ms_user_details_arr["MSE_userID"];
				}
                $objUser = new User();
                $status = $objUser->mathsValidateLogin($ms_user_details,$ms_parent_id,true);

                $productStatus = $objUser->validateLogin($usernameMS, $pwd='', true, false);
                
                $statusEng = $objUser->englishValidateLogin($mse_user_details,true);
                $setFields = false;

                /*if($productStatus['status'] == 3 || $productStatus['status'] == 2 ||)
                {*/
                    if($productStatus['status'] == 3 || $productStatus['status'] == 4 || $productStatus['status'] == 1)
                    {
                        if($status==1 || $status==4)
                        {
                            if($status==1)
                            {
                                $query = "INSERT INTO educatio_adepts.adepts_masterPasswordUsage(username,MSUserID,reason) VALUES ('$username'," . $objUser->userID . ",'$reason')";
                             }
                            else
                            {   
                                $_SESSION['parentMaster'] = true;
                                $query = "INSERT INTO educatio_adepts.adepts_masterPasswordUsage(username,MSUserID,reason) VALUES ('$username'," . $ms_parent_id . ",'$reason')";
                            }
                            $result = mysql_query($query) or die($query.mysql_error());
                            $setFields = true;
                        }
                        else
                        {
                            $setFields = false;
                            $errorMsg = "Wrong mindspark username.";
                        }
                    }
                    
                    if(($productStatus['status'] == 3 || $productStatus['status'] == 2) && $statusEng == 1)
                    {
                        /*FOR ENGLISH*/
                        if($statusEng == 1)
                        {

                            $errorMsg = '';
                            $queryEng = "INSERT INTO educatio_msenglish.masterPasswordUsage(username,MSEUserID,reason) VALUES ('$username'," . $mse_user_details . ",'$reason')";
                            $result = mysql_query($queryEng) or die($queryEng.mysql_error());
                            $setFields = true;
                            
                        }
                        else
                        {
                            $setFields = false;
                            $errorMsg = "Wrong mindspark username.";   
                        }
                        /*FOR ENGLISH END */
                    }
                    if($setFields)
                    {
                        echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=error.php?code=1'></noscript>";
                        echo '<form name="frmSession" id="frmSession" method="POST" action="' . '../login/validateLogin.php?image1='.$image1.'&image2='.$image2 . '">';
                        echo '<input type="hidden" name="masterPassword" id="masterPassword" value="true"/>';
                        echo '<input type="hidden" name="username" id="username" value="'.$usernameMS.'"/>';
                        echo '<input type="hidden" name="usernameei" id="usernameei" value="'.$username.'"/>';
                        echo '<input type="hidden" name="configKey" id="configKey" value="'.$configKey.'"/>';
                        echo '<input type="hidden" name="browserName" id="browserName" value="'.$browserName.'"/>';
                        echo '<input type="hidden" name="browserVersion" id="browserVersion" value="'.$browserVersion.'"/>';
                        echo '<input type="hidden" name="osDetails" id="osDetails" value="'.$osDetails.'"/>';
                        echo '</form>';
                        echo '<script>document.frmSession.submit();</script>';
                        exit();
                    }
                    
                    
               /* }
                else{
                   $errorMsg = "Wrong MS username.";
                }*/
            /*if($status==1 || $status==4){
				if($status==1)
                {
                    echo "string m here";
                    $query = "INSERT INTO educatio_adepts.adepts_masterpasswordusage(username,MSUserID,reason) VALUES ('$username'," . $objUser->userID . ",'$reason')";
                 }
				else
				{	
					$_SESSION['parentMaster'] = true;
					$query = "INSERT INTO educatio_adepts.adepts_masterpasswordusage(username,MSUserID,reason) VALUES ('$username'," . $ms_parent_id . ",'$reason')";
				}
                $result = mysql_query($query) or die($query.mysql_error());
                

                echo "<noscript><META HTTP-EQUIV='Refresh' CONTENT='0;URL=error.php?code=1'></noscript>";
                echo '<form name="frmSession" id="frmSession" method="POST" action="' . '../login/validateLogin.php?image1='.$image1.'&image2='.$image2 . '">';
               echo '<input type="hidden" name="masterPassword" id="masterPassword" value="true"/>';
                echo '<input type="hidden" name="username" id="username" value="'.$usernameMS.'"/>';
                echo '<input type="hidden" name="usernameei" id="usernameei" value="'.$username.'"/>';
				echo '<input type="hidden" name="configKey" id="configKey" value="'.$configKey.'"/>';
                echo '<input type="hidden" name="browserName" id="browserName" value="'.$browserName.'"/>';
                echo '<input type="hidden" name="browserVersion" id="browserVersion" value="'.$browserVersion.'"/>';
               echo '</form>';
                echo '<script>document.frmSession.submit();</script>';
                exit();
            }
            else{
               $errorMsg = "Wrong MS username.";
            }*/
            
        }
        else
        {
            $errorMsg = "Username and password do not match";
        }
    
    }
    $theme=1;
?>

<?php //include("header.php"); ?>
<title>Master password usage</title>
<?php if ($theme == 1) { ?>
<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
<link href="css/generic/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else if ($theme == 2) { ?>
<link rel="stylesheet" href="css/commonMidClass.css" />
<link rel="stylesheet" href="css/generic/midClass.css" />
<?php } else if ($theme == 3) { ?>
<link rel="stylesheet" href="css/commonHigherClass.css" />
<link rel="stylesheet" href="css/generic/higherClass.css" />
<?php } ?>

<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<link rel="stylesheet" type="text/css" href="css/colorbox.css">
<script src="libs/jquery.colorbox-min.js" type="text/javascript"></script>

<script>
        var langType = '<?= $language; ?>';
        function load() {
    <?php if ($theme == 1) { ?>
                var a = window.innerHeight - (130);
                $('#pnlContainer').css("height", a + "px");
    <?php } else if ($theme == 2) { ?>
                var a = window.innerHeight - (125);
                if (window.innerHeight < 600) {
                    var a = window.innerHeight - (515);
                }
                $('#pnlContainer').css("height", a + "px");
    <?php } else if ($theme == 3) { ?>
                var a = window.innerHeight - (125);
                var b = window.innerHeight - (610);
                $('#pnlContainer').css({"height": a + "px"});
                $('#topicInfoContainer').css({"height": a + "px"});
    <?php } ?>
    
        }
        function logoff()
        {
            window.location = "logout.php";
        }
        function storageEnabled() {
            try {
                localStorage.setItem("__test", "data");
            } catch (e) {
                if (/QUOTA_?EXCEEDED/i.test(e.name)) {
                    return false;
                }
            }
            return true;
        }
       
</script>
<script>
    function validate() {
        if (document.getElementById('usernameEI').value == '') {
            alert("please enter username");
        }
        else if (document.getElementById('password').value == '') {
            alert("please enter password");
        }
        else if (document.getElementById('reason').value == '') {
            alert("please enter reason");
        }
        else {
            document.getElementById('formSubmit').submit();
        }
    }
</script>
<style>
        .back {
            margin:0;
            font-family             :       Helvetica, Arial, sans-serif;
            font-size               :       12px;
        }
        #formContainer{
            width: 60%;
            margin-left: 20%;
            text-align: left;
            margin-bottom: 100px;
        }
</style>
</head>
<body id="body" onLoad="load()" onResize="load();" class="translation">
    <input type="hidden" id="packageType" value="<?= $objUser->packageType ?>">
    <div id="top_bar">
        <div class="logo">
        </div>

        <div id="studentInfoLowerClass" class="forLowerOnly hidden">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id="cssmenu">
                        <ul>
                            <li class="has-sub "><a href="javascript:void(0)"><span><?= $usernameMS ?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <!--<div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?= $childClass . $childSection ?></span></div>-->
            </div>
        </div>
        <div id="studentInfoLowerClass" class="forHighestOnly">
            <div id="nameIcon"></div>
            <div id="infoBarLeft">
                <div id="nameDiv">
                    <div id="cssmenu">
                        <ul>
                            <!--<li class="has-sub "><a href="javascript:void(0)"><span id="nameC"><?= $childName ?>&nbsp;&#9660;</span></a>-->
                            </li>
                        </ul>
                    </div>
                </div>
                <!--<div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?= $childClass . $childSection ?></span></div>-->
            </div>
        </div>
        <div id="help" style="visibility:hidden">
            <div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout" class="hidden">
            <div class="logout" onClick="logoff()"></div>
            <div class="logoutText" data-i18n="common.logout"></div>
        </div>
        <div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>
    <div id="container">
        <div id="info_bar" class="forLowerOnly hidden">
            <div id="blankWhiteSpace"></div>
        </div>
        <div id="info_bar" class="forHigherOnly hidden">
            <div id="studentInfo">
                <div id="studentInfoUpper">
                    <div id="childClassDiv"><span data-i18n="common.class"></span>: <?= $childClass . $childSection ?></div>
                    <div id="childNameDiv" class="Name"><?= $childName ?></div>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="clear"></div>
        </div>
        <div id="info_bar" class="forHighestOnly">
            <div id="dashboard" class="forHighestOnly">
                <div id="dashboardIcon"></div>
            </div>
            <div class="arrow-right"></div>
        </div>
        <div id="pnlContainer" style="overflow:auto;">
            <div id="checkMessage">
            </div>
            <div id="formContainer">
                <span style="color:maroon">Please use intranet credentials to login.</span>
                <form method="POST" id="formSubmit" onSubmit="return validate();">
                    <?php if($errorMsg!='') { ?>
                    <div style="color:red;"><?= $errorMsg ?></div>
                    <?php } ?>
                    <span style="">Username* :</span>
                    <input type="text" class="input_box" name="usernameEI" id="usernameEI" />
                    <br>
                    <br>
                    <span style="">Password* :</span>
                    <input type="password" class="input_box" name="password" id="password" />
                    <br><br>
                    <span>Reason* :</span>
                    <textarea name="reason" rows="4" id="reason" cols="50"></textarea>
                    <br>
                    <div id="rowErrMsg" style="">
                    </div>
                    <div id="break">
                    </div>
                    <div>
                        <input type="hidden" name="username" id="username"  value="<?=$usernameMS?>"/>
                        <input type="hidden" name="nextPage" value="<?= $nextPage ?>" />
						<input type="hidden" name="configKey" id="configKey" value="<?=$configKey?>"/>
                        <input type="hidden" name="browserName" id="browserName" value="<?= $browserName ?>"/>
                        <input type="hidden" name="browserVersion" id="browserVersion" value="<?= $browserVersion ?>"/>
                        <input type="hidden" name="osDetails" id="osDetails" value="<?= $osDetails ?>"/>
                        <!--<input type="submit" class="loginButton" id="login" value="Enter" />-->
                        <!--<button class="loginButton" id="login" value="Enter" >Enter</button>-->
                        <br>
                    </div>
                </form>
                <button class="loginButton" id="login" value="Enter"  onclick="validate()">Enter</button>
                <br>
            </div>
            <?php include("footer.php"); ?>
