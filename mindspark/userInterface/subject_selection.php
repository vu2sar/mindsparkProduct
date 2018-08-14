<?php
include("check1.php");
include("constants.php");
include("classes/clsUser.php");

if(!isset($_SESSION['userID']))
{
	header("Location:logout.php");
	exit();
}

if(isset($_POST['subjectno']))
{
	$_SESSION['subjectno'] = $_POST['subjectno'];
	header("Location:controller.php?mode=createSession");
}

$userID = $_SESSION['userID'];
$objUser = new User($userID);

$childName    = explode(" ",$objUser->childName);
$childName    = $childName[0];
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;


?>

<?php include("header.php"); ?>

<title>Mindspark</title>

<?php if($theme==1) { ?>
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<link href="css/generic/lowerClass.css" rel="stylesheet" type="text/css">
<?php } else { ?>
    <link rel="stylesheet" href="css/commonMidClass.css" />
    <link rel="stylesheet" href="css/generic/midClass.css" />
<?php } ?>
    <style>
        .maths {
            display         :       block;
            border          :       none;
            float           :       left;
            margin-left	    :       30%;
            background      :       url(assets//maths_1.png) 0px 0px no-repeat;
            width           :       163px;
            height          :       172px;
            cursor			:		pointer;
        }

        .maths:hover {
            background      :       url(assets/maths_2.png) 0px 0px no-repeat;
        }
        .science {
            float			:		left;
            display         :       block;
            border          :       none;
            margin-left		:       50px;
            background      :       url(assets/science_1.png) 0px 0px no-repeat;
            width           :       163px;
            height          :       172px;
            cursor			:		pointer;

        }

        .science:hover {
            background      :       url(assets/science_2.png) 0px 0px no-repeat;
        }
    </style>
    <script>var langType = '<?=$language;?>';</script>
<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>


<script language="javascript">

function load(){
	 init();
<?php if($theme==1) { ?>
	var a= window.innerHeight - (180);
	$('#pnlContainer').css("height",a+"px");
<?php } else { ?>
	var a= window.innerHeight - (170);
	$('#pnlContainer').css("height",a+"px");
<?php } ?>
}

function logoff()
{
	window.location="logout.php";
}
function init()
{
	setTimeout("logoff()", 600000);	//log off if idle for 10 mins

}
window.history.forward(1);
function setSubject(subjectno)
{
	document.getElementById('subjectno').value = subjectno;
	document.getElementById('frmSubjectSelection').submit();
}

</script>
</head>
<body class="translation" onLoad="load()">
	<div id="top_bar">
		<div class="logo">
		</div>

        <div id="studentInfoLowerClass" class="forLowerOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span><?=$childName?>&nbsp;</span></a></li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
        <div id="help" style="visibility:hidden">
        	<div class="help"></div>
            <div class="helpText" data-i18n="common.help"></div>
        </div>
        <div id="logout">
        	<div class="logout" onClick="logoff()"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>
        </div>
		<div id="whatsNew" style="visibility:hidden">
            <div class="whatsNew"></div>
            <div class="whatsNewText" data-i18n="common.whatsNew"></div>
        </div>
    </div>

	<div id="container">
    	<div id="info_bar" class="forLowerOnly">
        	<div id="blankWhiteSpace"></div>
        </div>
		<div id="info_bar" class="forHigherOnly">


			<div id="studentInfo">
            	<div id="studentInfoUpper">
                	<div id="childClassDiv"><span data-i18n="common.class"></span> <?=$childClass.$childSection?></div>
                	<div id="childNameDiv"><?=$childName?></div>
                    <div class="clear"></div>
                </div>
            </div>

            <div class="clear"></div>
		</div>

			<div id="pnlContainer">
            	<div id="formContainer">
                    <form id="frmSubjectSelection" action="<?=$_SERVER['PHP_SELF']?>" method="post">
							<div align="center" ><span data-i18n="common.welcome"></span> <?php echo $childName; ?>!</div>
							<div align="center">
								<input type="button" data-i18n="[title]common.selectMaths" class="maths"  onclick="setSubject(2)">
								<input type="button" data-i18n="[title]common.selectScience" class="science" onClick="setSubject(3)">
							</div>
							<input type="hidden" name="subjectno" id="subjectno">
							<input type="hidden" name="mode" value="createSession">
					</form>
                </div>
	        </div>
	</div>
<?php include("footer.php"); ?>
</body>
</html>
