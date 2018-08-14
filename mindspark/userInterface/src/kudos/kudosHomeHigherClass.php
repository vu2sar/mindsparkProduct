<?php
//error_reporting(E_ALL);
set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
//error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
 
include_once("../../check1.php");
include("../../constants.php");
include("../../classes/clsUser.php");
include('common_functions.php');
include("../../header.php");
 
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
    echo "<script>window.location=\"kudosHomeHigherClass.php\"</script>";
}
 
$myWall = FALSE;
 
if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='my')
    $myWall = TRUE;
 
$arrKudos = getAllKudos($myWall, $schoolCode, $childClass, $childSection, $userName , $category);
//print_r($arrKudos);
 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<title>Kudos - Home</title>

<link rel="stylesheet" href="styles/jquery-ui.css" />
<link href="../../css/commonHigherClass.css" rel="stylesheet" type="text/css">
<link href="../../css/kudos/higherClass.css?ver=3" rel="stylesheet" type="text/css"/>
<link href="../../css/rewardsDashboard/higherClass.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="styles/style.css?ver=3"/>
<script src="../../libs/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="styles/inputosaurus.css" />
<script type="text/javascript" src="js/inputosaurus.js"></script>
<script type="text/javascript" src="../../libs/i18next.js"></script>
<script type="text/javascript" src="../../libs/translation.js"></script>
<script type="text/javascript" src="../../libs/closeDetection.js"></script>
<link rel="stylesheet" type="text/css" href="../../css/colorbox.css">
<script src="../../libs/jquery.colorbox-min.js" type="text/javascript"></script>
<script src="js/kudosStudentHome.js?ver=2" type="text/javascript"></script>
<script>
var langType = '<?=$language;?>';
	function load(){
		var a= window.innerHeight - (170);
		$('#activitiesContainer').css({"height":a+"px"});
		$('#main_bar').css({"height":a+"px"});
		$('#menu_bar').css({"height":a+"px"});
		$('#sideBar').css({"height":a+"px"});
		var ua1 = navigator.userAgent;
		if( ua1.indexOf("Android") >= 0 )
		{
			$("#activitiesContainer").css("height","auto");
			$('#menu_bar').css("height",$('#activitiesContainer').css("height"));
			$("#sideBar").css("height",$("#activitiesContainer").css("height"));
			$("#main_bar").css("height",$("#activitiesContainer").css("height"));
		}
		var bonusChampColor = <?php echo json_encode($bonusChampArray); ?>;
		CreatePieChart("bonusPie",bonusChampColor);
		var accuracyChampColor = <?php echo json_encode($accuracyChampArray); ?>;
		CreatePieChart("accuracyPie",accuracyChampColor);
		var consistencyChampColor = <?php echo json_encode($consistencyChampArray); ?>;
		CreatePieChart("consistencyPie",consistencyChampColor);
		var homeChampColor = <?php echo json_encode($homeChampArray); ?>;
		CreatePieChart("homePie",homeChampColor);
	}
	
	
	function onChangeSubmit()				
			{
			 var actionUrl = "names_ajax.php";
    		 var categorySelect = $("#category-select-dropdown option:selected").val();
			  
			  $.post(actionUrl,{categorySelect:categorySelect});			 	
			}
</script>
	
	<script>
	var langType	=	'<?=$language?>';
	var click=0;
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
	function openMainBar(){
	
		if(click==0){
			if(window.innerWidth>1024){
			$("#main_bar").animate({'width':'245px'},600);
			$("#plus").animate({'margin-left':'227px'},600);
		}
		else{
			$("#main_bar").animate({'width':'200px'},600);
			$("#plus").animate({'margin-left':'182px'},600);
		}
			$("#vertical").css("display","none");
			click=1;
		}
		else if(click==1){
			$("#main_bar").animate({'width':'26px'},600);
			$("#plus").animate({'margin-left':'7px'},600);
			$("#vertical").css("display","block");
			click=0;
		}
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

<body class='translation'>
     
     <div id="top_bar">
		<div class="logo">
		</div>
		<div id="studentInfoLowerClass" class="forHighestOnly">
        	<div id="nameIcon"></div>
        	<div id="infoBarLeft">
            	<div id="nameDiv">
                    <div id='cssmenu'>
                        <ul>
                            <li class='has-sub '><a href='javascript:void(0)'><span id="nameC"><?=$Name?>&nbsp;&#9660;</span></a>
                                <ul>
                                    <li><a href='../../myDetailsPage.php'><span data-i18n="homePage.myDetails">My Details</span></a></li>
									<li><a href='../../changePassword.php'><span data-i18n="homePage.changePassword">Change Password</span></a></li>
                                    <li><a href='../../whatsNew.php'><span data-i18n="common.whatsNew">What's New</span></a></li>
                                    <li><a href='../../logout.php'><span data-i18n="common.logout">Logout</span></a></li>
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
        	<div class="logoutText" data-i18n="common.logout"></div>		
        </div>
    </div>   
    
 	
    
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard">
                    <div id="kudosIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">Kudos</span></div>
               

                </div>
				<div class="arrow-right"></div>
				<div id="sparkieBar" class="forHighestOnly">
					<div id="leftSparkieBar">
			
            <form name="formmain" id="formmain" method="POST">	
			<input type="hidden" name="hdnAction" id="hdnAction" value="<?=$_POST['hdnAction']?>"/>	
			<input type="hidden" name="hdnType" id="hdnType" value="<?=$_POST['hdnType']?>"/>	
			<input type="hidden" name="hdnTo" id="hdnTo" value="<?=$_POST['hdnTo']?>"/>	
            <input type="hidden" name="hdnToClass" id="hdnToClass" value="<?=$_POST['hdnToClass']?>"/>
			<input type="hidden" name="hdnMessage" id="hdnMessage" value="<?=$_POST['hdnMessage']?>"/>	
            <input type="hidden" name="hdnCategoryDropdown" id="hdnCategoryDropdown" value="<?=$_POST['hdnCategoryDropdown']?>"/>		
			
		<!--send kudos button removed - reffer previous revisions-->
					</div>
					
				</div>
				
		</div>
    
    <div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="../../dashboard.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			DASHBOARD
			</div></a>
			<a href="../../examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="../../home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="../../explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<div id="plus" onClick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			
			<a href="../../activity.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;color:inherit">
			<div id="drawer5" style="font-size:1.4em;">
			<div id="drawer5Icon"></div>
			ACTIVITIES
			</div></a>
			<!--<a href="../../viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
	</div>
       
    <div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				
				<div id="report">
					<span id="reportText"></span>
					<!--<div id="reportIcon" class="circle"><div class="arrow-s"></div></div>-->
				</div>
                <a href="kudosHomeHigherClass.php">
				<div id="kudosWallOfFame" class="forHighestOnly">
					<div id="naM" class="pointed1">
					</div></br>
					Wall of Fame
				</div></a>
				<a href="kudosHomeHigherClass.php?wall=my">
				<div id="kudosMyWall" class="forHighestOnly">
					<div id="aM" class="pointed1">
					</div></br>
					My Wall
				</div></a>
			</div>
	</div>
    

			
		<div id="kudosContainer" style="height:150px;">
        	
			<div id="kudo_body_higher">
            <div id="divKudos"></div>
            <script type="text/javascript">addKudoTable();</script>
            </div>
            <? // MODAL BOX FOR SENDING KUDOS (removed reffer previous version) ?>
		</form>
        
       </div>
    
   <div style="display:none"><div id="certificateModal" class="certificateModal" style="width:1000px; height:513px;"></div></div>
    <?php include("../../footer.php");?>
</body>
</html>