<!doctype html>
<?php
//error_reporting(E_ALL);
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
 
include_once("../../check1.php");
include("../../constants.php");
include("../../classes/clsUser.php");
include('common_functions.php');
 
$userID = $_SESSION['userID'];
$objUser = new User($userID);
$schoolCode    = $objUser->schoolCode;
$childClass    = $objUser->childClass;
$childSection  = $objUser->childSection;
$childName = $objUser->childName;
$userName= $objUser->username;
$category = $objUser->category;
$_SESSION['category']=$category;
//print_r($objUser);
 
//echo fetchFullName($userName);
//echo "</br>".$childName;
 
if( !isset($_SESSION['userID'])) {
    header( "Location: error.php");
}
if(isset($_SESSION['revisionSessionTTArray']) && count($_SESSION['revisionSessionTTArray'])>0)
{
    header("Location: controller.php?mode=login");
}
$dataSynchronised = true;
if($_SESSION['isOffline'] === true && ($_SESSION['offlineStatus']==2 || $_SESSION['offlineStatus']==4))
    $dataSynchronised = false;
 
if(!isset($_SESSION['notice'])){
    $notice=1;
    $_SESSION['notice']=1;
}
else {
    $notice=0;
}
//exit();
 
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$baseurl = IMAGES_FOLDER."/newUserInterface/";
 
/*
echo "User ID is: ".$userID;
echo "</br>School Code is: ".$schoolCode;
echo "</br>Child Class is: ".$childClass;
echo "</br>Child Section is: ".$childSection;*/
 
//KUDOS PHP STARTS HERE
 
//set_time_limit(0);
//include('../check.php');
//checkPermission('MNU');
 
//mysql_connect("192.168.0.7","root","") or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");
//mysql_select_db ("educatio_adepts")  or die ("Could not select database".mysql_error());
 
 
 
if(isset($_POST['hdnAction']) && $_POST['hdnAction'] == 'sendKudo')
{
    sendKudo($userName, $schoolCode, $childClass, $childSection, $category);
    echo "<script>window.location=\"kudosHomeLowerClass.php\"</script>";
}
 
$myWall = FALSE;
 
if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='my')
    $myWall = TRUE;
 
$arrKudos = getAllKudos($myWall, $schoolCode, $childClass, $childSection, $userName , $category);
//print_r($arrKudos);
?>

<html>
	<head>
		<title>
			Kudos - Home
		</title>
		<title>Kudos - Home</title>
        
        <link rel="stylesheet" href="styles/jquery-ui.css" />
        <link rel="stylesheet" href="styles/style.css?ver=3"/>
        <link href="../../css/commonLowerClass.css?ver=1237" rel="stylesheet" type="text/css">
        <link href="../../css/home/lowerClass.css?ver=1" rel="stylesheet" type="text/css">
        <script src="../../libs/jquery.js"></script>
        <!--<script type="text/javascript" src="js/jquery-autocomplete.js"></script>-->
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="styles/inputosaurus.css" />
        <script type="text/javascript" src="js/inputosaurus.js"></script>   
		<script type="text/javascript" src="../../libs/i18next.js"></script>
		<script type="text/javascript" src="../../libs/translation.js"></script>
        <script src="../../libs/closeDetection.js"></script>
        <link rel="stylesheet" type="text/css" href="../../css/colorbox.css">
		<script src="../../libs/jquery.colorbox-min.js" type="text/javascript"></script>
		<script src="js/kudosStudentHome.js?ver=2" type="text/javascript"></script>
        <script>
		var langType = '<?=$language;?>';
		function getHome()
		{
		setTryingToUnload();
		window.location.href	=	"../../home.php";
		}
		
		function logoff()
		{
			setTryingToUnload();
			window.location="../../logout.php";
		}
		
        function addKudoTable()
        {
            var html = '';
            <?php            
                if(is_array($arrKudos) && count($arrKudos)>0)
                {
                    $onGoingMonth = 0;
                    $onGoingYear = 0;
                    $i = 0;
                    $monthCnt = 0;
                    foreach($arrKudos as $kudo_id=>$kudo_details)
                    { 
                        $month = date('m', strtotime($kudo_details['sent_date']));                  
                        $year = date('Y', strtotime($kudo_details['sent_date']));
                        $gender = getGender($kudo_details['receiver']); 
                        $type = $kudo_details['kudo_type']; 
                        $message = $kudo_details['message'];
                        $message = preg_replace("/[\r\n]+/", " ", $message);
                        $imageSrc = preg_replace('/\s+/', '', $type);
                        $imageSrc = strtolower($imageSrc);
                        if($gender=='Boy'||$gender=='Girl'){ $genderToShow=$gender;} else {$genderToShow='noGender';}
                        //echo "onGoingMonth - ".$onGoingMonth." onGoingYear - ".$onGoingYear."<br>";
                        if($onGoingMonth != $month || $onGoingYear != $year)    
                        {
                            //echo "inside if <br>";
                            $onGoingMonth = $month;
                            $monthCnt++;
                            $onGoingYear = $year;
                            $i = 0;
                          
                           $html .= "<table class=\"kudoMonthYearTbl\"><tr>";   
                           $html .= "<td class=\"kudoMonthYearTd\"><hr></td><td class=\"kudoMonthYearTd\">- - - -  ".date("F, Y", strtotime("01-$month-$year"))."  - - - -</td><td class=\"kudoMonthYearTd\"><hr></td>";
                           $html .= "</table>";
                        }       
                            $html .= "<div id=\"kudosTd-$kudo_id\" class=\"kudosTd\">";
                               $html .= "<table border=\"0\" width=\"100%\" id=\"KudoSummary\" class=\"KudoSummary\">";
                                   $html .= "<tr>";
                                       $html .= "<td id=\"Figurine\">";
                                           $html .= "<img src=\"../kudos/images/".$genderToShow.".png\" title=\"".fetchFullName($kudo_details['receiver'])."\" height=\"50px\" width=\"50px\"/>";
                                       $html .= "</td>";
                                       $html .= "<td class=\"kudoTitle\">";
                                           $html .= "<span style=\"color:black;font-weight:bold;\">".fetchFullName($kudo_details['receiver'])." received ".$type." from ".fetchFullName($kudo_details['sender'])."</span> <br/>";
                                           $html .= "<span style=\"color:#4D4D4D;\">".date('d F, Y', strtotime($kudo_details['sent_date']))."<span>";
                                       $html .= "</td>";
                                       $html .= "<td style=\"text-align: right;vertical-align:top;\">";
                                           $html .= "<img  src=\"../kudos/images/".$imageSrc.".png\" height=\"100px\" width=\"100px\">";
                                       $html .= "</td>";
                                   $html .= "</tr>";
                                   $html .= "<tr>";
                                       $html .= "<td colspan=\"4\" class=\"message\">";
                                           $html .= $message;
                                       $html .= "</td>";                                   
                                   $html .= "</tr>";
                                $html .= "</table>";
                           $html .= "</div>";
                    }
                }
                ?>
            $("#divKudos").html('<?=$html?>');
            if(window.navigator.userAgent.indexOf("MSIE") > 0)
                $(".deleteKudoTd").css({"width":"0px"});
        }

		</script>
		
		</head>
	<body class='translation' style="overflow-x:hidden;">
   	
    <div id="body_kudo_lower">
        
    <div id="top_bar">
    <div class="logo">
  	</div>
		<div id="studentInfoLowerClassKudos" class="forLowerOnly hidden">
        	<div id="home" class="linkPointer" onClick="getHome()"></div>
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                	<li><a href='../../myDetailsPage.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.myDetails">My Details</span></a></li>
									<li><a href='../../changePassword.php' onClick="javascript:setTryingToUnload();"><span data-i18n="homePage.changePassword">Change Password</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff();" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout">Logout</div>		
        </div>
	</div>
    
    
		<br/>
		<br/>
		<form name="formmain" id="formmain" method="POST">	
			<input type="hidden" name="hdnAction" id="hdnAction" value="<?=$_POST['hdnAction']?>"/>	
			<input type="hidden" name="hdnType" id="hdnType" value="<?=$_POST['hdnType']?>"/>	
			<input type="hidden" name="hdnTo" id="hdnTo" value="<?=$_POST['hdnTo']?>"/>
            <input type="hidden" name="hdnToClass" id="hdnToClass" value="<?=$_POST['hdnToClass']?>"/>	
			<input type="hidden" name="hdnMessage" id="hdnMessage" value="<?=$_POST['hdnMessage']?>"/>  
			<div id='kudos_header_lower' class="kudos_header_lower">
			<?php
				include('kudos_header.php');
			?>
			<!--send kudos button removed - reffer previous revisions-->
			
			</div>
			<div id="kudo_body">
			<div id="divKudos"></div>
            <script type="text/javascript">addKudoTable();</script>
			</div>			
			<? // MODAL BOX FOR SENDING KUDOS (removed reffer previous version)?>
		</form>
        
        <div style="display:none"><div id="certificateModal" class="certificateModal" style="width:1000px; height:513px;"></div></div>
        <input id="childClass" type="text" value="<?= $childClass ?>" style="display:none">
        <?php include("../../footer.php");?>
        <?php 
        if(!(is_array($arrKudos) && count($arrKudos)>0)) {?>
        <script type="text/javascript">;
            $("#copyright").css({"position":"fixed","bottom":"0px", "left":"0px"});
            $("#body_kudo_lower").css({"min-height":"600px", "background":"url(../../assets/lowerClass/schoolBackground.png) no-repeat 50% 180px"});
        </script>;
        <?php } ?>
       
        </body>