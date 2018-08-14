<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");

if(!isset($_SESSION['userID']))
{
	header("Location:logout.php");
	exit();
}

$userID = $_SESSION['userID'];
$objUser = new User($userID);

$childClass    = $objUser->childClass;
$Name = explode(" ",$objUser->childName);

$folder = MOUSE_ACTIVITY_FOLDER;
$activityNo  = 1;

if(isset($_POST['activityNo']))
    $activityNo = $_POST['activityNo'];


$flashFile = $folder."/Excercise".$activityNo.".swf";


?>

<?php include("header.php"); ?>

<title>Mindspark</title>

<?php if($childClass<=3) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/whatsNew/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/whatsNew/midClass.css" />
<?php } ?>
<script>var langType = '<?=$language;?>';</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>

<script language="javascript">

function load(){
<?php if($childClass<=3) { ?>
	var a= window.innerHeight - (205);
	$('#formContainer').css("height",a+"px");
<?php } else { ?>
	var a= window.innerHeight - (120);
	$('#pnlContainer').css("height",a+"px");
<?php } ?>
}

function logoff()
{
	window.location="logout.php";
}

var secs=0;
var timerID;
var flag1 = true;
function getFlashMovieObject(movieName)
{
	//var movieName = "FLVPlayer";
	if(document.embeds[movieName])	//Firefox
		return document.embeds[movieName];
	if(window.document[movieName])//IE
		return window.document[movieName];
	if(window[movieName])
		return window[movieName];
	if(document[movieName])
		return document[movieName];
	return null;
}



function checkActivityComplete()
{
	var flashMovie=getFlashMovieObject("simplemovie");
	flag = flashMovie.GetVariable("Game_Completed");

	if(flag=="1")
	{
	    if(flag1)
	    {
		  //document.getElementById('btnContinue').style.display="inline";
		  startTheTimer();
		  flag1 = false;
	    }
	}
	else
	{
	    clearTimeout(timerID);
	    secs=0;
	    //document.getElementById('btnContinue').style.display="none";
	    flag1 = true;
	}
	self.setTimeout("checkActivityComplete()", 1000);
}
function startTheTimer()
{
    if(secs==20)
    {
        stopTheTimer();
    }
    else {
        secs = secs + 1;
        timerID = self.setTimeout("startTheTimer()", 1000);
    }
}
function stopTheTimer()
{
    clearTimeout(timerID);
    showNext();
}
function showNext()
{
	var activityNo = parseInt(document.getElementById('activityNo').value);
	if(activityNo!=3)
	{
	   activityNo += 1;
	   document.getElementById('activityNo').value = activityNo;
	   document.getElementById('frmActivity').submit();
	}
	else
	{
	    redirectToMSSession();
	}
}

function redirectToMSSession()
{
    //document.getElementById('mode').value="nextAction";
    document.getElementById('mode').value="login";
    document.getElementById('activityFinished').value="1";
    document.getElementById('frmActivity').action = "controller.php";
    document.getElementById('frmActivity').submit();
}

function showActivity(activityNo)
{
    document.getElementById('activityNo').value = activityNo;
    document.getElementById('frmActivity').submit();
}

function skipActivities()
{
    if(confirm("Are you sure you want to skip these activities?"))
    {
        redirectToMSSession();
    }
}

function endSession()
{
	msg = "Are you sure you want to end the current session?";
    var ans = confirm(msg);
    if(ans)
    {
    	document.getElementById('mode').value = 1;
    	var params= "mode=endsession";
    	params += "&code="+1;
    	try {
    		var request = new Ajax.Request('controller.php',
    		{
    			method:'post',
    			parameters: params,
    			onSuccess: function(transport)
    			{

    				resp = transport.responseText|| "no response text";

    			},
    			onFailure: function()
    			{
    				alert('Something went wrong...');
    			}
    		}
    		);
    	}
    	catch(err) {}
    	document.getElementById('frmActivity').action='logout.php';
        document.getElementById('frmActivity').submit();
    }
}
</script>
</head>
<body class="translation" onLoad="load()">
	<div id="top_bar">
		<div class="logo">
		</div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout">
        	<div class="logout linkPointer" onClick="logoff()"></div>
        	<div class="logoutText linkPointer" data-i18n="common.logout" onClick="logoff()"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
    	<div id="info_bar">
        	<div id="blankWhiteSpace" class="forLowerOnly">
                <div style='font-size:1.8em; margin-top: 20px; padding-left: 5%;'>Keyboard and Mouse Activities</div>
            </div>
        </div>

			<div id="pnlContainer">
            	<div id="formContainer">
                    <form name="frmActivity" id="frmActivity" method="post" action="<?=$_SERVER['PHP_SELF']?>" autocomplete='off'>

                        <input type="hidden" name="activityNo" id="activityNo" value="<?=$activityNo?>">
                        <input type="hidden" name="mode" id="mode" value="">
                        <input type="hidden" name="activityFinished" id="activityFinished" value="0">

                        <table width="100%" border="0">
                            <tr>
                                <td align="right" style='font-size: 1.5em;'>
                                <?php if($activityNo==3)  { ?>
                                    <a href="#" onClick='showActivity(1)' title="Click here for Activity 1">Activity 1</a> |
                                    <a href="#" onClick='showActivity(2)' title="Click here for Activity 2">Activity 2</a> |
                                <?php } elseif($activityNo==2)  { ?>
                                    <a href="#" onClick='showActivity(1)' title="Click here for Activity 1">Activity 1</a> |
                                    <a href="#" onClick='showActivity(3)' title="Click here for Activity 3">Activity 3</a> |
                                <?php } elseif($activityNo==1)  { ?>
                                    <a href="#" onClick='showActivity(2)' title="Click here for Activity 2">Activity 2</a> |
                                    <a href="#" onClick='showActivity(3)' title="Click here for Activity 3">Activity 3</a> |
                                <?php } ?>
                                <a href="#" onClick='skipActivities()'>Go to Questions</a>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" width="100%" height="100%">
                                    <center>
                                        <OBJECT id="simplemovie"
                                        height="462" width="665" classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
                                        <param name="movie" value="<?=$flashFile?>">
                                        <PARAM NAME="quality" VALUE="high">
                                        <PARAM NAME="SCALE" VALUE="exactfit">
                                        <PARAM NAME="WMODE" VALUE="transparent">
                                            <EMBED src="<?=$flashFile?>" swliveconnect="true" WMODE="transparent" quality=high	WIDTH="665" HEIGHT="430" NAME="simplemovie" ALIGN="center" SCALE="exactfit"></EMBED>
                                        </OBJECT>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
	        </div>
	</div>
<?php include("footer.php"); ?>
