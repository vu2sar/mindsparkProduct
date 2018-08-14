<?php
$serverProtocol = "http";
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false)
       $serverProtocol='https';
if ($_SERVER['SERVER_NAME'] == 'mindspark.in') {
    header("Location: $serverProtocol://www.mindspark.in/mindspark/login/"); //change this
    exit(0);
}
//For live

require_once("../userInterface/dbconf.php");
include("../userInterface/constants.php");
require 'constants.php';
$sparkieChampArr = array();
$sparkieChampArr = getSparkieChampDetails();
$sparkieChampDateRange = $sparkieChampArr['dateRange'];
$sparkieChampDetails = $sparkieChampArr['champsList'];
$sparkieChampStr = "";

$parentSignUp="";
$usernameParentSignup="";
$passwordParentSignup="";
if(isset($_POST['parentSignUp']))
{
    $parentSignUp= isset($_POST['parentSignUp']) ? $_POST['parentSignUp'] : "";
    $usernameParentSignup = isset($_POST['username']) ? $_POST['username'] : "";
    $passwordParentSignup = isset($_POST['password']) ? $_POST['password'] : "";

}
$dayflag = 0;
$dayarrays = array('Sun','Wed','Thu','Fri','Sat');
$imKey=0;
if (in_array(date("D"), $dayarrays) && SERVER_TYPE=="LIVE") {   //For offline servers, show sparkie champ all days
	$dayflag = 1;
    $imKey = array_search(date("D"), $dayarrays);
}
	
foreach ($sparkieChampDetails as $class => $arrDetails)
{
    $sparkieChampStr .= "<span class='classSlot'>Class " . $class . ":</span><br/>";
    $sparkieChampStr .= "<span style='float:left'>".$arrDetails["name"];
        if($arrDetails["school"] != "")
            $sparkieChampStr .= ",";
        $sparkieChampStr .= "</span>";
        $sparkieChampStr .= "<span style='float:right; margin-right:10%;'>".$arrDetails["noOfSparkies"]."<img src='../userInterface/assets/sparkie.png' style='height:30px'></span>";
        if($arrDetails["school"] != "")
            $sparkieChampStr .= "<span style='clear:both'><br>" . $arrDetails["school"]."</span>";  
		else
			$sparkieChampStr .= "<br/>";
        $sparkieChampStr .= "<br/>"; 
}
session_start();
$loginPageMsg = (empty($_SESSION['loginPageMsg'])) ? 0 : $_SESSION['loginPageMsg'];	// to show login error message
session_unset();
session_destroy();
session_start();
$_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection

$iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");

$homeUsage = 0;
$startTime = strtotime("07:00:00");  //School start time
$endTime = strtotime("16:00:00");  //School end time
$now = time();
//Consider after 5 p.m. and before 7 a.m. as home usage and Sundays
if ((($now < $startTime) || ($now > $endTime) ) || date("D") == "Sun") {
    $homeUsage = 1;
}

$prefillUsername = '';
if(isset($_REQUEST['prefill']))
    $prefillUsername = $_REQUEST['prefill'];

$alternateLinkFlag[1]=0;
$alternateLinkFlag[2]=0;
if (SERVER_TYPE=="LIVE") {
	$alternateLink[1]="http://www.aqad.in/";
	$alternateLink[2]="http://www.aqad.in/";
}

if(isset($_COOKIE['alternate'])) 
{
	if($_COOKIE['alternate']<2)
	{
		$alternateCookie=$_COOKIE['alternate']+1;
		
		setcookie ( "alternate", $alternateCookie, time()+60 * 60 * 24 ); //change the cookie value for next image
		
	}
	else
	{
		$alternateCookie=1;
		setcookie ( "alternate",$alternateCookie, time()+60 * 60 * 24 );
		
	}
}
else
{
	$alternateCookie=1;
	setcookie ( "alternate", $alternateCookie, time()+60 * 60 * 24 );
	
}
$string = file_get_contents(WHATSNEW.'Login_Image/imagelist.json');
$json_a = json_decode(stripcslashes($string),true);

$today=date("Y-m-d");

if (isset($json_a[$today])){
    
    $loginHome=(isset($json_a[$today]["$alternateCookie"])?$json_a[$today]["$alternateCookie"]:$json_a[$today][0]);
    $loginHomeLink=$loginHome['link'];
    $loginHomeImg=$loginHome['image'];
    $loginTalentSearch=(isset($json_a[$today]["$alternateCookie"])?$json_a[$today]["$alternateCookie"]:$json_a[$today][0]);
    $loginTalentSearchLink=$loginTalentSearch['link'];
    $loginTalentSearchImg=$loginTalentSearch['image'];
}
else if (SERVER_TYPE=="LIVE") {
    
    $loginHome=(isset($json_a['default'][$dayflag]["$alternateCookie"])?$json_a['default'][$dayflag]["$alternateCookie"]:$json_a['default'][$dayflag][0]);
    $loginHomeLink=$loginHome['link'];
    $loginHomeImg=$loginHome['image'];

    $loginTalentSearch=(isset($json_a['default'][$dayflag]["$alternateCookie"])?$json_a['default'][$dayflag]["$alternateCookie"]:$json_a['default'][$dayflag][0]);
    $loginTalentSearchLink=$loginTalentSearch['link'];
    $loginTalentSearchImg=$loginTalentSearch['image'];
}
else {
	$loginHomeLink = "";
	$loginHomeImg = "loginTalentSearchAlternate1.jpg";
}
?>

<!DOCTYPE HTML>
<html>
    <title>Login</title>
    <head>
        
        <meta http-equiv="X-UA-Compatible" content="IE=9">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="js/jquery.1.11.1.min.js"></script>
        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!--[if lt IE 9]>
      <script src="js/html5shiv.min.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
        <!--For Live-->
        <link href="../userInterface/css/login/login.css?ver=13" rel="stylesheet" type="text/css">
        <link href="../userInterface/css/prompt.css" rel="stylesheet" type="text/css">
        <!--<script type="text/javascript" src="../userInterface/libs/jquery.js"></script>-->
        <!--For local-->
        <!--<link href="css/login/login.css?ver=2" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="libs/jquery.js"></script>-->
        <script type="text/javascript" src="../userInterface/libs/prompt.js"></script>
        <script type="text/javascript" src="../userInterface/libs/combined.js?ver=1"></script>
        <script type="text/javascript" src="../userInterface/libs/brwsniff.js?ver=9"></script>
        <style>
            #help{
                height:40px;
                width:40px;
                background-size: 40px 40px;
            }
        </style>
        <script>
			try {
				var parentLocalStorage = localStorage.getItem("ngStorage-parentData");
				var parentLocalStorageArr = $.parseJSON(parentLocalStorage);
				var accessToken = parentLocalStorageArr["data"]["access_token"];
				if(accessToken!="")
				{
					$.post("../userInterface/errorLog.php", "accessToken="+accessToken, function (data) {
                            
                    });
				}
				localStorage.removeItem("questionNumber");
			}
			catch(err) {
				
			}
            var prefillUsername = '<?php echo $prefillUsername; ?>';
            
            var parentSignUp = '<?php echo $parentSignUp; ?>'
            var usernameParentSignup = '<?php echo $usernameParentSignup; ?>';
            var passwordParentSignup = '<?php echo $passwordParentSignup; ?>';
            var interval;

            localStorage.removeItem('tab_info');
            localStorage.removeItem('questionNumber');
            $(document).ready(
                    function() {
                        setTryingToUnload();
//                        var finalHeight = $('#loginHeading').height() + $('#loginContainer').height() + $('#loginHelp').height() + $('#loginParent').height() + $('#loginOpenID').height() + 150;
//                        $('#rightDiv').height(finalHeight);
                        if(prefillUsername!='')
                            $('#username').val(prefillUsername);

                        if(parentSignUp)
                        {
                            $('#username').val(usernameParentSignup);
                            $('#password').val(passwordParentSignup);
                            $('#login').trigger('click');

                        }
                <?php if($dayflag == 0) {?>
                /*----- Code for auto scrolling of Sparkie champ -----*/  
                        var mousein = false;
                        //$(".paneldata").html($(".paneldata").html()+$(".paneldata").html());
                        resizeSparkieChamp();
                        //$("#sparkieChampInner").css("height",parseInt(t)-120+"px");
                        function autoScroll() {
                            if (mousein) return;
                            if($('#sparkieChampInner')[0].scrollTop + parseInt($("#sparkieChampInner").css("height")) + 2 >= $("#sparkieChampInner")[0].scrollHeight){
                                $('#sparkieChampInner')[0].scrollTop = -parseInt($("#sparkieChampInner").css("height"));
                            }
                            $('#sparkieChampInner')[0].scrollTop += 1.5;
                        }
                        $('#sparkieChampInner').bind('mouseout', function(){mousein = false;})
                        $('#sparkieChampInner').bind('mouseover', function(){mousein = true;})
                        interval = setInterval(autoScroll, 35);
                /* ---- code for auto scrolling end here ---- */   
                <?php } ?>

                    }
            );
            
			if(window.sessionStorage)
            {
                sessionStorage.setItem('windowName','');
                window.name='';
            }
            else
                window.name='';
            var browser = new Array();
			try {
				if(window.sessionStorage)
				{
                    sessionStorage.setItem('windowName','');
                    window.name='';
                }   
                else
                    window.name='';
			}
			catch (er)
			{
				
			}
<?php if(SERVER_TYPE=="LIVE") { ?>
            $(window).load(function()
            {
                preload();
            });
<?php } ?>			
            function resizeSparkieChamp(){
                var t = $("#sparkieChampInner").css("height");
                var extraFlowing = $('<div></div>').appendTo("#sparkieChampInner").html($(".paneldata").html()).css('overflow',"hidden");
                if($(".paneldata").height() > $("#sparkieChampInner").height())
                    {
                        $(".marginDiv").css("height",parseInt(t)/3+"px");
                        extraFlowing.css('height',2*parseInt(t)/3+"px");
                    }
                else 
                    {
                        $(".marginDiv").css("height","0");
                        extraFlowing.css('height',"0px");

                    }
            }
        function preload () {
            var i = 0,
                max = 0,
                o = null,
         
                // list of stuff to preload
                preload = [
                    '<?php echo HTML5_COMMON_LIB; ?>/PxLoader.js',
                    '<?php echo HTML5_COMMON_LIB; ?>/PxLoaderImage.js',
                    '<?php echo HTML5_COMMON_LIB; ?>/loadxmldoc.js',
                    '<?php echo HTML5_COMMON_LIB; ?>/common.js'
                ],
                isIE = navigator.appName.indexOf('Microsoft') === 0;
         
            for (i = 0, max = preload.length; i < max; i += 1) {
                
                if (isIE) {
                    new Image().src = preload[i];
                    continue;
                }
                o = document.createElement('object');
                o.data = preload[i];
                
                // IE stuff, otherwise 0x0 is OK
                //o.width = 1;
                //o.height = 1;
                //o.style.visibility = "hidden";
                //o.type = "text/plain"; // IE 
                o.width  = 0;
                o.height = 0;
                
                
                // only FF appends to the head
                // all others require body
                document.body.appendChild(o);
            }  
        };
<?php
$query = "SELECT browser,category FROM adepts_browserSupport";
$result = mysql_query($query) or die(mysql_error());
while ($line = mysql_fetch_array($result)) {
    echo "browser['$line[0]'] = new Array('$line[1]');\r\n";
}
?>
            var br = new Array(4);
            var os = new Array(2);
            var browserCheck;
            var fullstr = getFullUAString();
            var br = getBrowser();

            var os = getOS();

            /*added by nivedita*/
            var osDetails = os[0]+os[1];
            osDetails     = osDetails.replace(/ /g,'');
            /*added by nivedita end*/

            var jsver = jsVersion();
            var cookies = navigator.cookieEnabled;
            var localStorageV = (!!window.localStorage);
            var browser = br[0] + " " + br[1];
            browser += ", OS: " + os[0] + " " + os[1];
            var timestamp = new Date().getTime();//for image tag...
            var image = new Image();
            var image1 = "";
            var image2 = "";
<?php	if(SERVER_TYPE=="LIVE")	{ ?>	
            var imageCounter = 1;
            var imageCounter1 = 1;
            var imageSource = "<?php echo IMAGES_FOLDER; ?>";
            var imageSource1 = "<?php echo IMAGES_FOLDER_S3; ?>";
            image.src = imageSource + '/sparkyForImageCheck.png?' + timestamp;
            image.onload = function() {
                image1 = "loaded";
            };
            image.onerror = function errorImage() {
                imageCounter++;
                if (imageCounter <= 5) {
                    var timestamp = new Date().getTime();
                    image.src = imageSource + '/sparkyForImageCheck.png?' + timestamp;
                    setTimeout(function() {
                        errorImage();
                    }, 500);
                } else {
                    image1 = "notloaded";
                }
            };
            var image3 = new Image();
            image3.src = imageSource1 + '/sparkyForImageCheck.png?' + timestamp;
            image3.onload = function() {
                image2 = "loaded";
            };
            image3.onerror = function errorImage() {
                imageCounter1++;
                if (imageCounter1 <= 5) {
                    var timestamp = new Date().getTime();
                    image3.src = imageSource1 + '/sparkyForImageCheck.png?' + timestamp;
                    setTimeout(function() {
                        errorImage();
                    }, 500);
                } else {
                    image2 = "notloaded";
                }
            };
<?php } else { ?>
				image1 = "loaded";
				image2 = "loaded";
<?php } ?>						
            function loginSubmit()
            {
                if (document.getElementById("username").value == "")
                {
                    alert("Please specify the username!");
                    document.getElementById("username").focus();
                    return false;
                }
                var screenResolution = screen.width+"X"+screen.height + " || " + getViewPortSize();
                document.getElementById('image1').value = image1;
                document.getElementById('image2').value = image2;
                document.getElementById('browser').value = browser+" ~ "+screenResolution;
                document.getElementById('browserName').value = br[0].replace(/ /g,'');
                document.getElementById('browserVersion').value = br[1];
                document.getElementById('jsver').value = jsver;
                document.getElementById('cookies').value = cookies;
                document.getElementById('localStorage').value = localStorageV;

                /*added by nivedita*/
                document.getElementById('osDetails').value = osDetails;
                /*added by nivedita end*/
                document.getElementById("formSubmit").action = "validateLogin.php?image1=" + image1 + "&image2=" + image2 + "&browser=" + br[0] + "$brwver=" + br[1] + "&jsver=" + jsver + "&cookies=" + cookies+ "&localStorage=" + localStorageV;

            }

            function getViewPortSize()
            {
                var viewportwidth;
                var viewportheight;
            
                //Standards compliant browsers (mozilla/netscape/opera/IE7)
                if (typeof window.innerWidth != 'undefined')
                {
                    viewportwidth = window.innerWidth,
                    viewportheight = window.innerHeight
                }
            
                // IE6
                else if (typeof document.documentElement != 'undefined'
                && typeof document.documentElement.clientWidth !=
                'undefined' && document.documentElement.clientWidth != 0)
                {
                    viewportwidth = document.documentElement.clientWidth,
                    viewportheight = document.documentElement.clientHeight
                }
            
                //Older IE
                else
                {
                    viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
                    viewportheight = document.getElementsByTagName('body')[0].clientHeight
                }
            
                return viewportwidth + "X" + viewportheight;
            }

            function qs()
            {

                var query = window.location.search.substring(1);
				var loginPageMsg = <?php echo $loginPageMsg; ?>;

                var parms = query.split("&");
				//alert(document.documentMode);
				
				if(br[0]=="Internet Explorer" && (br[1]<=8 || document.documentMode<=8)){
					if(br[1]>document.documentMode){
						var showbrowser = document.documentMode;
					}else{
						var showbrowser = br[1];
					}
					$("#formSubmit").html("<span style='color:red;font-size:0.9em;margin-top:10px;margin-right:10px;'>Mindspark is not supported in "+br[0]+" "+showbrowser+". Please <a href='../userInterface/recommendedBrowsers.php' target='_blank' style='text-decoration:underline;color:blue;cursor:pointer;'>upgrade</a> your browser.</span>");
					$("#formSubmit").css("text-align","center");
					$("#formSubmit").css("margin-right","28px");
					$("#formSubmit").css("height","150px");
				}
                for (var i = 0; i < parms.length; i++) {
                    var pos = parms[i].indexOf("=");
                    if (pos > 0) {
                        var key = parms[i].substring(0, pos);
                        var val = parms[i].substring(pos + 1);
                        if (key == "login" && val == "0" && loginPageMsg == 1)
                        {
                            document.getElementById("rowErrMsg").innerHTML = "Username and password do not match";
                        }
                        else if (key == 'login' && val == '1' && loginPageMsg == 1)
                            document.getElementById("rowErrMsg").innerHTML = "Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.";
                        else if (key == "login" && val == "9" && loginPageMsg == 1)
                        {
                            document.getElementById("rowErrMsg").innerHTML = "Your Mindspark account has been locked because you failed to select the correct picture password. A request has been sent to your teacher. Please be a little patient.";
                        }
                        else if (key == "login" && val == "2")
                        {
                            var prompts = new Prompt({
                                text: "Sorry for the inconvenience! You won't be able to login temporary due to class up-gradation in progress. Please try again later.<br><br>(If you are not able to login after 24 hours, please contact your teacher or customer support)",
                                type: 'alert',
                                func1: function() {
                                    jQuery("#prmptContainer_classUpgrade").remove();
                                },
                                promptId: 'classUpgrade'
                            });
                        }
                        else if (key == "login" && val == "3")
                        {
                            var prompts = new Prompt({
                                text: "You still have not verified you account. Please click on link provided in email sent to your email address to verify account.",
                                type: 'alert',
                                func1: function() {
                                    jQuery("#prmptContainer_accountVerification").remove();
                                },
                                promptId: 'accountVerification'
                            });
                        }
                        else if (key == "login" && val == "4")
                        {
                            var prompts = new Prompt({
                                text: "You account is disabled. Please contact Mindspark customer care(<a href='mailto:mindspark@ei-india.com'>mindspark@ei-india.com</a> for further information.",
                                type: 'alert',
                                func1: function() {
                                    jQuery("#prmptContainer_accountDisable").remove();
                                },
                                promptId: 'accountDisable'
                            });
                        }
                        else if (key == "login" && val == "5")
                        {
                            var prompts = new Prompt({
                                text: "You can not use this email address to login with username as it is registered to login with Google/Facebook account. Please use login with Google/Facebook. Please contact Mindspark customer care(<a href='mailto:mindspark@ei-india.com'>mindspark@ei-india.com</a> for further information.",
                                type: 'alert',
                                func1: function() {
                                    jQuery("#prmptContainer_openID").remove();
                                },
                                promptId: 'openID'
                            });
                        }
                        /**
                            Added for english as renewal page is not developed. 
                        */
                        else if (key == "login" && val == "10")
                        {
                            document.getElementById("rowErrMsg").innerHTML = "Username and password do not match";
                        }
                        else if (key == "login" && val == "11")
                        {
                            var prompts = new Prompt({
                                text: "Kindly subscribe to the product.",
                                type: 'alert',
                                func1: function() {
                                    jQuery("#prmptContainer_openID").remove();
                                },
                                promptId: 'openID'
                            });
                        }
                        else if (key == 'login' && val == '12')
                            document.getElementById("rowErrMsg").innerHTML = "Your account is temporarily deactivated.<br/>Please contact your school or Mindspark customer care for more information.";
                         else if (key == 'login' && val == '13')
                            document.getElementById("rowErrMsg").innerHTML = "You have already logged in on some other system. Please logout from the other system to login again.";
                         
                    }
                }
            }



        </script>
<?php if ($iPad == true || $Android == true) { ?>
            <script>
                function doOnOrientationChange() {
//                    if (!jQuery('.promptContainer').is(":visible")) {
//                        var prompts = new Prompt({
//                            text: 'Mindspark is best viewed and worked with in the landscape (horizontal) mode.<br><br>Please shift to landscape mode to proceed further. ',
//                            type: 'block',
//                            func1: function() {
//                                jQuery("#prmptContainer").remove();
//                            }
//                        });
//                        jQuery("#promptText").css('font-size', '160%');
//                    }
                    //jQuery('.promptContainer').css('display','none');
                    var windowheight = jQuery(window).height();
                    var windowwidth = jQuery(window).width();
                    var pagecenterW = windowwidth / 2;
                    var pagecenterH = windowheight / 2;
                    jQuery("#promptBox").css({'margin-top': pagecenterH - 130 + 'px', 'margin-left': pagecenterW - 175 + 'px'});
                }

            </script>
    <?php
}
?>
    </head>
    <body onload="qs()" onresize="resizeSparkieChamp()" class="translation">
<!--        <div id="header">
            <div id="eiColors">
                <div id="orange"></div>
                <div id="yellow"></div>
                <div id="blue"></div>
                <div id="green"></div>
            </div>
        </div>-->
<!--        <div id="head" style=""> <a href="../">
                <div id="logo" ></div>
            </a> <a href="../../faq.php" target="_blank">
                <div id="help"> </div>
            </a> </div>-->
<section id="main-content">

    <div class="row" style="margin-right: inherit!important; margin-left: inherit!important;">
        <div id="header">
            <div id="eiColors">
                <div id="orange"></div>
                <div id="yellow"></div>
                <div id="blue"></div>
                <div id="green"></div>
            </div>
        </div>
        <div id="head" style=""> 
            <a href="../"><div id="logo" ></div></a> 
            <a href="../../faq.php" target="_blank"><div id="help"> </div></a> 
        </div>
    </div>
    <section class="wrapper">
        <div class="container" >
            <div class="col-md-12">
                
        <!-- <div id="head" style=""> <a href="../">
                <div id="logo" ></div>
            </a> <a href="../../faq.php" target="_blank">
                <div id="help"> </div>
            </a> </div>-->
            </div>
        <!-- <div class="col-xs-12">
                <div id="head" style=""> <a href="../">
                <div id="logo" ></div>
            </a> <a href="../../faq.php" target="_blank">
                <div id="help"> </div>
            </a> </div>
            </div>
            </div>-->

        <div class="col-sm-6 col-sm-push-6 col-md-4 col-md-push-8" >
    
                <div id="rightDiv"  >
                   
                    <div id="loginHeading" >LOGIN</div>
                    <div id="loginContainer">
                        
                        <form method="POST" id="formSubmit" action="validateLogin.php" onSubmit="return loginSubmit();" role="form">
                            <div style="">USERNAME</div>
                            <input type="text" class="input_box" name="username" id="username" autofocus/>
                            <br>
                            <br>
                            <div style="">PASSWORD</div>
                            <input type="password" class="input_box" name="password" id="password"/>
                            <br>
                            <br>
                            <div id="rowErrMsg" style="padding-bottom:2px;"> </div>
                            <div id="break">
                            </div>
                            <div style="margin-left:10%;">
                                <input type="submit" class="loginButton" id="login" value="Enter" />
                                <br>
                            </div>
                            <input type="hidden" name="image1" id="image1" value="" />
                            <input type="hidden" name="image2" id="image2" value="" />
                            <input type="hidden" name="browser" id="browser" value="" />
                            <input type="hidden" name="browserName" id="browserName" value="" />
                            <input type="hidden" name="browserVersion" id="browserVersion" value="" />
                            <input type="hidden" name="jsver" id="jsver" value="" />
                            <input type="hidden" name="cookies" id="cookies" value="" />    

                            <!--added by nivedita-->                                                           
                            <input type="hidden" name="osDetails" id="osDetails" value="" />    
                            <!--added by nivedita end-->                                                           

                            <input type="hidden" name="localStorage" id="localStorage" value="" />                                                           
							<input type="hidden" id="offline" name="offline" value="0" />
                        </form>
                    </div>
                    <div id="loginHelp" class="login-help">
                        <!--                                            For live-->
                        <p>Forgot your password? <a href="forgotPassword.php"><u>Click here</u></a> to reset it.</p>
                    </div>
<?php if (SERVER_TYPE=="LIVE") {	?>
                    <div id="loginParent" class="login-parent"><b>Login to Parent Connect with:</b></div>
                    <div id="loginOpenID" class="login-openid">

                        <!--                            For local and live -->
                        <a href="<?php echo str_replace('[state]', $_SESSION['state'], GOOGLEURL); ?>"><img src="assets/google.png" style="border: 0px;width: 30%;"></a>
                        <a href="<?php echo str_replace('[state]', $_SESSION['state'], FBURL); ?>"><img src="assets/fb.png" style="border: 0px;width: 30%;"></a>
                        <a href="../parentApp/parentSignUpNew.php"><img src="assets/newParent.png" style="border: 0px;width: 30%;"></a>
                    </div>
<?php } ?>					
                </div>
        </div>
            
        <?php if($dayflag == 0) {?>
            <div class="col-sm-6 col-sm-pull-6 col-md-4 col-md-pull-4" >
                    <div id="imgdiv">
                        <?php 
                        $posterLink=array('','');
                        if($homeUsage==1 && $loginHomeLink!=''){ 
                            $posterLink[0]='<a href="'.$loginHomeLink.'" target="_blank" style="text-decoration:none">';$posterLink[1]='</a>';
                        }
                        ?>
						<div style=""><?php echo $posterLink[0]; ?><img src="<?php echo WHATSNEW.'Login_Image/'.$loginHomeImg; ?>" height="320px"/><?php echo $posterLink[1]; ?></div>
                    </div>
            </div>
        <?php } else { ?>
            <div class="col-sm-6 col-sm-pull-6 col-md-7 col-md-pull-4" >
                    <div id="imgdiv">
                        <?php 
                        $posterLink=array('','');
                        if($homeUsage==1 && $loginTalentSearchLink!=''){ 
                            $posterLink[0]='<a href="'.$loginTalentSearchLink.'" target="_blank" style="text-decoration:none">';$posterLink[1]='</a>';
                        }
                        ?>
                        <div style=""><?php echo $posterLink[0]; ?><img src="<?php echo WHATSNEW.'Login_Image/'.$loginTalentSearchImg; ?>"/><?php echo $posterLink[1]; ?></div>
                    </div>
            </div>
        <?php } ?>
                 
        <?php if($dayflag == 0) {?>
            <div class="col-sm-12 col-md-4 col-md-pull-4" id="sparkieChamp" >
                <div id="sparkieChampHeading">SPARKIE CHAMPS</div>
                <span id="sparkieChampHeading2" style="color:rgb(78, 7, 35);position:absolute;margin-left:15px;margin-top:-25px;width:230px;text-align:center;">Based on sparkies earned between <?=$sparkieChampDateRange['from']?> and <?=$sparkieChampDateRange['till']?></span> <br>
                <div id="champ" class="champ"></div>

                <div id="sparkieChampInner">
                    <div class="marginDiv"></div>
                    <div class="paneldata">
                        <?= $sparkieChampStr ?>
                    </div>
                    <div class="marginDiv"></div>
                </div>
            </div>
        <?php } ?>
        </div>
    </section>
</section>
<?php
if(date("D")!='Sun' && date("H")<15 && SERVER_TYPE=="LIVE")
{
?>	
<script>

$.ajax({url: "http://mindsparkserver/mindspark/login/index.php",
	dataType: "jsonp",
	statusCode: {
		200: function (response) {
			$("#offline").val("1");
		},
		404: function (response) {
			$("#offline").val("0");
		}
	} 
 });
</script>	
<?php } ?>
<?php
$_SESSION['loginPageMsg'] = 0; // to not display login error message on refresh of page

if(count($sparkieChampDetails) == 0)
{ ?>
    <script> 
        $("#sparkieChampHeading").hide();//style.visibility = "hidden";
        $("#sparkieChampHeading2").hide();//style.visibility = "hidden";
        $("#champ").hide();//style.visibility = "hidden";
    </script>
<?php }

include("../userInterface/footer.php");
function getSparkieChampDetails() {
    $arrSparkieChampDetails = array();
    $tilldate = date('Y-m-d', strtotime('last sunday'));
    $datearr = explode("-", $tilldate);
    $timestamp = mktime(0, 0, 0, $datearr[1], $datearr[2], $datearr[0]);
    $newtimestamp = strtotime("-6 days", $timestamp);
    $fromdate = strftime("%Y-%m-%d", $newtimestamp);

    $query = "SELECT studentName, studentClass, schoolName, noOfSparkies
				 FROM adepts_loginPageDetails WHERE fromDate='$fromdate' AND tillDate='$tilldate' ORDER BY cast(studentClass as unsigned)";
	/*$query = "SELECT studentName, studentClass, schoolName
				 FROM adepts_loginPageDetails WHERE fromDate='$fromdate' AND tillDate='$tilldate' ORDER BY cast(studentClass as unsigned)";*/			 
    $result = mysql_query($query) or die(mysql_error());
    while ($line = mysql_fetch_array($result)) {        
        $arrSparkieChampDetails[$line['studentClass']]['name'] = $line['studentName'];
        $arrSparkieChampDetails[$line['studentClass']]['school'] = $line['schoolName'];
        $arrSparkieChampDetails[$line['studentClass']]['noOfSparkies'] = $line['noOfSparkies'];
    }
    return array(
        'champsList'  => $arrSparkieChampDetails,
        'dateRange' => array(
            'from' => date('j M Y', strtotime($fromdate)),
            'till' => date('j M Y', strtotime($tilldate)),
        ),
    );
}
?>