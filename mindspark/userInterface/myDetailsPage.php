
<?php
@include("check1.php");
require_once 'constants.php';
include("classes/clsUser.php");
include("functions/functions.php");

$userID 	= $_SESSION['userID'];
if(isset($_GET['first']) && $_GET['first']==1)
	$firstLogin=1;
	
//exit();
$objUser		=	new User($userID);
$schoolCode		=	$objUser->schoolCode;
$childName		=	$objUser->childName;
$childClass		=	$objUser->childClass;
$childSection 	=	$objUser->childSection;
$childDob		=	$objUser->childDob;
$startDate		=	$objUser->startDate;
$endDate		=	$objUser->endDate;
$startDate = date('d-m-Y', strtotime($startDate));
$Name = explode(" ", $_SESSION['childName']);
$Name = $Name[0];
$secretQuestion =   $objUser->secretQues;
$secretAnswer =   $objUser->secretAns;

//print_r($objUser);
//exit();

$question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favorite colour?');


/* MODEL - Save data */
if(isset($_POST) && array_key_exists('updateRecord',$_POST)){
	if(db_saveUserDetails($userID)){
		$message = 'Your details have been updated successfully!';
		if($firstLogin==1)
			echo '<script>window.location="home.php";</script>';
	}
	else{
		$message = 'Failed to update. Please verify your data.';
	}
}

$userArray = getDetails();
$category = $userArray['category'];
$subcategory = $userArray['subcategory'];
$gender = $userArray['gender'];
$sparkieImage = $_SESSION['sparkieImage'];
$childDob		=	$userArray['dob'];




?>
<!DOCTYPE HTML>
<html>
<?php include("header.php");?>
<title>My Details Page</title>
	<?php
	if($theme==1) { ?>
	<link href="css/myDetailsPage/lowerClass.css?ver=1" rel="stylesheet" type="text/css">
	<link href="css/commonLowerClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			$('#clickText').html("");
			var a= window.innerHeight -75;
			var b= window.innerHeight -220;
			$('#formContainer').css("height",a);
			$('#container_form').css("height",b);
			if(androidVersionCheck==1){
				$('#container_form').css("height","auto");
				$('#formContainer').css("height","700px");
			}
		}	
	</script>
	<?php } else if($theme==2) { ?>
	<link href="css/myDetailsPage/midClass.css?ver=2" rel="stylesheet" type="text/css">
    <link href="css/commonMidClass.css" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= window.innerHeight -220;
			//$('#formContainer').css("height",a);
			if(androidVersionCheck==1){
				$('#container_form').css("height","auto");
				$('#formContainer').css("height","700px");
			}
		}
	</script>
	<?php } else { ?>
	<link href="css/commonHigherClass.css" rel="stylesheet" type="text/css">
	<link href="css/myDetailsPage/higherClass.css?ver=1" rel="stylesheet" type="text/css">
	<script>
		function load(){
			var a= parseInt(window.innerHeight) - (100);
			$('#formContainer').css("height",a+"px");
			$('#menuBar').css({"height":a+"px"});
			$('#main_bar').css({"height":a+"px"});
			$('#sideBar').css({"height":a+"px"});
			if(androidVersionCheck==1){
				$('#container_form').css("height","auto");
				$('#formContainer').css("height","700px");
				$('#main_bar').css("height",$('#formContainer').css("height"));
				$('#menu_bar').css("height",$('#formContainer').css("height"));
				$('#sideBar').css("height",$('#formContainer').css("height"));
			}
		}
	</script>
	<?php } ?>
	<meta charset="utf-8">
		<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery-1.7.1.min.js"></script>
        <script type='text/javascript' src='libs/combined.js'></script>
	<link rel="stylesheet" href="libs/css/jquery-ui-1.8.16.custom.css" />
	<script src="<?php echo HTML5_COMMON_LIB; ?>/jquery_ui.js" type="text/javascript"></script>
<!--	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>-->
	<script type="text/javascript" src="/mindspark/js/load.js"></script>
<!--	<script type="text/javascript" src="libs/closeDetection.js"></script>-->
	<link rel="stylesheet" href="/mindspark/js/plugins/countryFlagPlugin/css/intlTelInput.css">
    <script src="/mindspark/js/plugins/countryFlagPlugin/js/intlTelInput.js"></script>
	<link rel="stylesheet" style="text/css" href="/mindspark/css/CalendarControl.css" >
	<script language="javascript" type="text/javascript" src="/mindspark/js/CalendarControl.js" ></script>
	<!-- <script language="javascript" type="text/javascript" src="/mindspark/js/dateValidator.js"></script> -->
	<script language="javascript" type="text/javascript" src="../../script/form_validation.js?ver=1"></script>
	<style type="text/css">
    .ui-menu .ui-menu-item a,
    .ui-menu .ui-menu-item a.ui-state-hover,
    .ui-menu .ui-menu-item a.ui-state-active {
        font-weight: normal;
        margin: -1px;
        text-align:left;
        font-size:14px;
    }
    .ui-autocomplete-loading {
        background: white url("libs/css/ajax.gif") right center no-repeat;
    }
    #city {
        width: 172px;
    }

	ul {
   width: 250px;
}
</style>

	<script type="text/javascript">
    function split(val) {
        return val.split(/,\s*/);
    }

    function extractLast(term) {
        return split(term).pop();
    }

    function extractFirst(term) {
        return split(term)[0];
    }

    jQuery(function () {
        var $citiesField = jQuery("#city");

        $citiesField.autocomplete({
            source: function (request, response) {
                jQuery.getJSON(
                    "http://gd.geobytes.com/AutoCompleteCity?callback=?&q=" + extractLast(request.term),
                    function (data) {
                        response(data);
                    }
                );
            },
            minLength: 3,
            select: function (event, ui) {
                var selectedObj = ui.item;
                placeName = selectedObj.value;
                if (typeof placeName == "undefined") placeName = $citiesField.val();

                if (placeName) {
                    var terms = split($citiesField.val());
                    // remove the current input
                    terms.pop();
                    // add the selected item (city only)
                    terms.push(extractFirst(placeName));
                    // add placeholder to get the comma-and-space at the end
                   // terms.push("");
                    $citiesField.val(terms.join(", "));
                }

                return false;
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            },
        });

        $citiesField.autocomplete("option", "delay", 100);
    });
</script>


   
	<script>
	var langType = '<?=$language;?>';
	</script>
    <script>
		var click=0;
    	function redirect()
		{
			setTryingToUnload();
			window.location.href	=	"changePassword.php";
		}
		function logoff()
		{
			setTryingToUnload();
			window.location="logout.php";
		}
		function validateForm()
		{
			var x=document.forms["updateRecordForm"]["childEmail"].value;
			// var atpos=x.indexOf("@");
			// var dotpos=x.lastIndexOf(".");
			var y=document.forms["updateRecordForm"]["parentemail"].value;
			var mothery=document.forms["updateRecordForm"]["motheremail"].value;
			// var atpos1=y.indexOf("@");
			// var dotpos1=y.lastIndexOf(".");
			// var atpos2=mothery.indexOf("@");
			// var dotpos2=mothery.lastIndexOf(".");
			var z=document.forms["updateRecordForm"]["gender"].value;
			var parent=document.forms["updateRecordForm"]["parentname"].value;
			var mother=document.forms["updateRecordForm"]["mothername"].value;
			var dob=document.forms["updateRecordForm"]["startDate"].value;
			var fatherContact= document.forms["updateRecordForm"]["parentcontact"].value;
			var motherContact= document.forms["updateRecordForm"]["motherscontact"].value;
			var residentContact= document.forms["updateRecordForm"]["residence_no"].value;
			if(dob != '' && dob!='00-00-0000')
			{
				// var EnteredDate = dob; 
				// var date = EnteredDate.substring(0, 2);
				// var month = EnteredDate.substring(3, 5);
				// var year = EnteredDate.substring(6, 10);
				// var myDate = new Date(year, month - 1, date);
				// var today = new Date();
				if(!validateDate(document.forms["updateRecordForm"]["startDate"],'<?=$userArray['dob']?>')){
					return false;
				}
				// if (myDate > today) {
				// 		alert("Entered date is greater than today's date ");
				// 	return false;
				// }
			}
			if(dob=='' || dob=='00-00-0000'){
				alert("Please enter date of birth!");
				document.forms["updateRecordForm"]["startDate"].focus();
				return false;
			}
			if(z==""){
				alert("Please select Gender!");
					return false;
			}
			if(parent==""){
					if(mother=="")
				{
					alert("Please enter Father / Mother Name!");
					document.forms["updateRecordForm"]["parentname"].focus();
					return false;
				}
			}
			if(x != ""){
				// if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
				// {
				// 	alert("Student e-mail address is not valid");
				// 	return false;
				// }
				if(!validateEmail(x)){
					alert("Student e-mail address is not valid");
					document.forms["updateRecordForm"]["childEmail"].focus();
				 	return false;
				}
			}
			
			if(y){
				// if (atpos1<1 || dotpos1<atpos1+2 || dotpos1+2>=y.length)
				// {
				// 	alert("Father e-mail address is not valid");
				// 	return false;
				// }
				if(!validateEmail(y)){
					alert("Father e-mail address is not valid");
					document.forms["updateRecordForm"]["parentemail"].focus();
				 	return false;
				}
			}
			
			if(y==""){
					if(mothery=="")
				{
					alert("Please enter Father / Mother e-mail address");
					document.forms["updateRecordForm"]["parentemail"].focus();
					return false
				}
			}
			
			if(mothery){

				// 	if (atpos2<1 || dotpos2<atpos2+2 || dotpos2+2>=mothery.length)
				// {
				// 	alert("Mother e-mail address is not valid");
				// 	return false;
				// }
				if(!validateEmail(mothery)){
					alert("Mother e-mail address is not valid");
					document.forms["updateRecordForm"]["motheremail"].focus();
				 	return false;
				}
			}
			// var phoneno = /^\d{10}$/;
			if(!fatherContact)
			{
				if(!motherContact)
				{
					alert("Please enter Father / Mother Mobile Number.");
					document.forms["updateRecordForm"]["parentcontact"].focus();
					return false;
				}
			}
			// else if(!document.forms["updateRecordForm"]["parentcontact"].value.match(phoneno) && document.forms["updateRecordForm"]["parentcontact"].value.length < 8){
			// 	alert("Invalid Mobile number. Mobile number should contain at least 8 digits.");
			// 	return false;
			// }
			if(fatherContact!='' && !validatePhoneNo(fatherContact, document.getElementById('cell_no').value)){
				document.forms["updateRecordForm"]["parentcontact"].focus();
				return false;
			}		

			if(motherContact)
			{
				// if(!document.forms["updateRecordForm"]["motherscontact"].value.match(phoneno) && document.forms["updateRecordForm"]["motherscontact"].value.length < 8){
				// 	alert("Invalid Mobile number. Mobile number should contain at least 8 digits.");
				// 	return false;
				// }
				if(!validatePhoneNo(motherContact, document.getElementById('mothercell_no').value)){
					document.forms["updateRecordForm"]["motherscontact"].focus();
					return false;
				}					
			} 
			if(residentContact)
			{
				if(!validateLandlineNo(residentContact, document.getElementById('std_code').value)){
					document.forms["updateRecordForm"]["residence_no"].focus();
					return false;
				}	
			}
			
		if (document.getElementById('secretQuestion'))
			{
				if (document.getElementById('secretQuestion').value=="")
				{
					alert("Please select secret question.");
					document.getElementById('secretQuestion').focus();
					return false;
				}
			}
			
			if (document.getElementById('secretAnswer'))
			{
				
				if (document.getElementById('secretAnswer').value=="")
				{
					alert("Please fill secret answer.");
					document.getElementById('secretAnswer').focus();
					return false;
				}
			}
			if((document.getElementById('secretAnswer')))
			{
				if (document.getElementById('secretAnswer').value!="")
				{
					var string_length = document.getElementById('secretAnswer').value.length;
					if (string_length > 50)
					{
						alert("Secret answer lenght must not exceed >50");						
						return false;
					}
				}
			}				
			setTryingToUnload();
			alert("Details updated successfully.");
			
		
		}
		$("input[type=text]").live("keypress",function(e){

				if($(this).attr("id")=="country_code" || $(this).attr("id")=="std_code" || $(this).attr("id")=="parentcontact" || $(this).attr("id")=="motherscontact" || $(this).attr("id")=="residence_no" || $(this).attr("id")=="cell_no"){
				e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
				if(e.keyCode == 13) {
					
					var value = $(this).val();
					if (value == "") {						
						showPrompt(miscArr['misc103']);
					}
					else {
						checkAnswer(value);
					}					
					return false;
				}
				else {
					if((e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8 && e.keyCode!=45 && e.keyCode!=46) {
						return false;
					}
					if($(this).val().length == 3 && $(this).attr("id")=="country_code" && e.keyCode != 8 && e.keyCode!=46){
						return false;
					}
					if($(this).val().length == 5 && $(this).attr("id")=="std_code" && e.keyCode != 8 && e.keyCode!=46){
						return false;
					}
					if($(this).val().length == 10 && $(this).attr("id")=="residence_no" && e.keyCode != 8 && e.keyCode!=46){
						return false;
					}
					if($(this).val().length == 11 && $(this).attr("id")=="cell_no" && e.keyCode != 8 && e.keyCode!=46){
						return false;
					}
					if($(this).val().length == 11 && $(this).attr("id")=="parentcontact" && e.keyCode != 8 && e.keyCode!=46){
						return false;
					}
					if($(this).val().length == 11 && $(this).attr("id")=="motherscontact" && e.keyCode != 8 && e.keyCode!=46){
						return false;
					}
				}
			}
		});
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
    </script>
</head>
<body onload="load()" onresize="load();" class="translation">
	<div id="top_bar" class="top_bar_part4">
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
								<!--	<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>-->
                                    <li><a href='whatsNew.php'><span data-i18n="common.whatsNew"></span></a></li>
                                    <li><a href='logout.php'><span data-i18n="common.logout"></span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="classDiv"><span id="classText" data-i18n="common.class"></span> <span id="userClass"><?=$childClass.$childSection?></span></div>
            </div>
        </div>
		<div id="logout" onClick="logoff()" class="hidden">
        	<div class="logout"></div>
        	<div class="logoutText" data-i18n="common.logout"></div>		
        </div>
    </div>
	
	<div id="container">
		<div id="info_bar" class="hidden">
			<div id="lowerClassProgress">
				<a href="home.php"><div id="homeIcon"></div></a>
				<div class="icon_text2" data-i18n="myDetailsPage.myDetailsLower"></div>
				<div id="package">
				<div id="packageEnd">Package Start Date - <?=$startDate?>
				</div>
				<div id="packageStart">Package End Date - <?=$endDate?>
				</div>
				</div>
			</div>
			<div id="topic">
				<a href="home.php"><div id="home">
				</div></a>
				<div class="icon_text1"><a href="home.php" style="text-decoration:none;color:inherit">HOME</a> > <font color="#606062"> MY DETAILS</font></div>
				<div id="package">
				<div id="packageEnd">Package Start Date - <?=$startDate?>
				</div>
				<div id="packageStart">Package End Date - <?=$endDate?>
				</div>
				</div>
			</div>
			<div class="class">
				<strong>Class </strong>: <?=$childClass.$childSection?>
			</div>
			<div class="Name">
				<strong><?=$Name?></strong>
			</div>
			<!--<div id="dob">Date of Birth - <?=$childDob?>
			</div>-->
		</div>
		<div id="info_bar" class="forHighestOnly">
				<div id="dashboard" class="forHighestOnly" >
                    <div id="dashboardIcon"></div>
                    <div id="dashboardText"><span class="textUppercase">student information</span></div>
                </div>
				<div class="arrow-right"></div>
				<div id="package">
				<div id="packageEnd">Package Start Date - <?=$startDate?>
				</div>
				<div id="packageStart">Package End Date - <?=$endDate?>
				</div>
				</div>
				<!--<div id="dob">Date of Birth - <?=$childDob?>
				</div>-->
				<div class="clear"></div>
		</div>
		<div id="main_bar" class="forHighestOnly">
			<div id="drawer1">
			<a href="activity.php" style="text-decoration:none;color:inherit"> 
			<div id="drawer1Icon"></div>
			ACTIVITIES
			</div></a>
			<a href="examCorner.php" style="text-decoration:none;color:inherit"><div id="drawer2"><div id="drawer2Icon"></div>EXAM CORNER
			</div></a>
			<a href="home.php"><div id="drawer3"><div id="drawer3Icon"></div>HOME
			</div></a>
			<a href="explore.php"><div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div></a>
			<!--<div id="drawer4"><div id="drawer4Icon"></div>EXPLORE ZONE
			</div>-->
			<div id="plus" onclick="openMainBar();">
				<div id="vertical"></div>
				<div id="horizontal"></div>
			</div>
			<a href="src/rewards/rewardsDashboard.php" onClick="javascript:setTryingToUnload();" style="text-decoration:none;"><div id="drawer5"><div id="drawer5Icon" <?php if($_SESSION['rewardSystem']!=1) { echo "style='position: absolute;background: url(\"assets/higherClass/dashboard/rewards.png\") no-repeat 0 0 !important;width: 50px;height: 50px;margin-top: -30px;margin-left: -50px;'";} ?> class="<?=$sparkieImage?>"><div class="redCircle"><?=$sparkieWon?></div></div>
			REWARDS CENTRAL
			</div></a>
			<!--<a href="viewComments.php?from=links&mode=1"><div id="drawer6"><div id="drawer6Icon"></div>NOTIFICATIONS
			</div></a>-->
		</div>
		<div id="NA" class="hidden">
					Personal Info
					<div id="naM" class="pointed1">
					</div>
				</div>
				<div id="A" onclick="redirect()" class="hidden">
					Change Password
					<div id="aM" class="pointed2">
					</div>
				</div>
				<div id="menuBar" class="forHighestOnly">
			<div id="sideBar">
				<div id="NA">
					<div id="naM" class="pointed1">
					</div></br>
					My Details
				</div>
				<div id="A" onclick="redirect()">
					<div id="aM" class="pointed2">
					</div></br>
					Change Password
				</div>
			</div>
			</div>
	<div id="formContainer">
    <form action="myDetailsPage.php<?=$firstLogin==1?"?first=1":""?>" name="updateRecordForm" id="updateRecordForm"  method="POST">
		<div id="container_form">
		<table style='table-layout: fixed;width:auto;'>
			<tr><td>
			
				<label style="color:#F46521;content: attr(title);padding: 4px 8px;white-space: nowrap;z-index: 20px;font-weight: bold;border-color: grey;border-radius: 5px;height: 20px;margin-top: 5px;padding-left: 7px;">Name: <?=ucfirst($userArray['childName'])?></label>
		
			</td></tr>
		</table>

		<table style='table-layout: fixed;width:auto;'>
		<tr>
			<td style="width:340px;">
					<table>
						<tr>
							<td><font style='color:red;'>*</font>Date Of Birth</td>
							<td style="padding-left:80px"> <div id="gender"><font style='color:red;'>*</font>Gender</div></td>
						</tr>
						<tr>
							<td>
								
								<input style="border-color: grey;height: 20px;" type="text" name="startDate" id="startDate" readonly onFocus="setTryingToUnload();showCalendarControl(this);" size="18" onKeyUp="setTryingToUnload();showCalendarControl(this);"  onBlur="setTryingToUnload();validateDate(this,'<?=$userArray['dob']?>');"   value="<?=$userArray['dob']?>"/>
															

							</td>
							<td style="padding-left:80px">
								<select name="gender" id="genderStudent" style="border-color: grey;height: 26px;width:115px;">
									<option value="">Select</option>
									<option value="Boy" <?php if($gender=="Boy"){echo "selected";}?>>Boy</option>
									<option value="Girl" <?php if($gender=="Girl"){echo "selected";}?>>Girl</option>
								</select>
							</td>
						</tr>
					</table>
			</td>
			
			<td style="padding-left: 85px;">
				<table>
					<tr><td><div id="gender">Student Email ID</div></td></tr>
					<tr><td>
					<input type="text" maxlength="50" name="childEmail" id="childEmail" value="<?=$userArray['childEmail']?>" <?=$disabledStdEmailStatus?> maxlength="50" size="25" style="padding-left: 7px;border-color: grey;height: 20px;"></input>
					</td></tr>
				</table>
			</td>
			
			</tr>


		</table>

		<table>
		<tr>
		 <?php
			 $disabledSchNameStatus ='';
			 if(strcasecmp($category,"student")==0 && strcasecmp($subcategory,"school")==0)
			 {
				$disabledSchNameStatus = 'disabled';
			 }
	    ?>
		<td style="width:340px;">
		<div id="school">School Name</div>
                <input type="text" style="border-color: grey;height: 20px;margin-top: 5px;padding-left: 7px;"  name="schoolname" id="schoolname"  <?=$disabledSchNameStatus?> value="<?=$userArray['schoolName']?>" maxlength="100" size="62" ></input>

		</td>
		
		<td style="width:340px;padding-left: 20px;">
				<div id="school">City</div>
				<!--	<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="city" id="city" value="<?=$userArray['city']?>" maxlength="50" size="25"></input> -->

					<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;"  name="city" id="city" value="<?=$userArray['city']?>" maxlength="50" >
		</td></tr>
		</table>
		<br>
			
		<table>
			<tr>
				<td style="width:340px;">
					<div id="question">Secret Question<font style="color:red">*</font> :</div>
						<select name="secretQuestion" id="secretQuestion"  style="border-color:grey;height:26px;font-size:13px;font-family:Arial;">
							<option id="select" value="">Select</option>
							<?php
							foreach ($question_array as $secQus)
							{
								if($secretQuestion ==$secQus){
									echo "<option value='$secQus' selected >$secQus</option>";
								}
								else
									echo "<option value='$secQus'>$secQus</option>";
							}
							?>
						</select>
				</td>
				<td style="padding-left:85px">
					<div id="answer" class="cellpadding">Secret Answer<font style="color:red">*</font> :</div>
					<input style="border-color:grey;height:20px;font-family:Arial;padding-left:7px;" type="text" name="secretAnswer" id="secretAnswer" maxlength="30" value="<?=$secretAnswer?>"></input>
				</td>
			</tr>
			</table>

		<div id="contact">Please fill in all details of either the father or the mother:</div>
		<table cellpadding="3" cellspacing="3">
		<tr>
		<td colspan='2' style='text-align:center;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style='color:red;'>*</font>Name</td>
		<td style='padding-left: 40px;'><font style='color:red;'>*</font>Email ID</td>
		<!-- <td style='padding-left: 35px;'><font style='color:red;'>*</font>Mobile Number</td> -->
		<td style='padding-left: 35px;'><font style='color:red;'>*</font>Mobile</td>
		</tr>

		<tr>
		<?php
			$pmname = explode(',', $userArray['parentName']);
			$pmemail = explode(',', $userArray['parentEmail']);
			if($userArray['contactno_cel'] != '-,-' || $userArray['contactno_cel'] != ',' || $userArray['contactno_cel'] != '--')
			{
				$pmno = explode(',', $userArray['contactno_cel']);

				$pmnodetails = explode('-', $pmno[0]);
				$pmnomotherdetails = explode('-', $pmno[1]);
			}
			
		
		?>
		<script>
		// function contanctKeypress(evt) {
		// 	evt = evt || window.event;
		// 	if (!evt.ctrlKey && !evt.metaKey && !evt.altKey) {
		// 		var charCode = (typeof evt.which == "undefined") ? evt.keyCode : evt.which;
		// 		if (charCode) {
		// 			var key_codes = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];

		// 				 if (!((charCode>=48 && charCode<=57) || charCode==0 || charCode==8)) {
		// 				   evt.preventDefault();
		// 				 }            
		// 		}
		// 	}
		// 	};
		$(window).load(function(){
			document.getElementById("fatherisdcode").value = document.getElementById("cell_no").value;
			document.getElementById("motherisdcode").value = document.getElementById("mothercell_no").value;
			document.getElementById("isdcode").value = document.getElementById("country_code").value;
		});
							function getfatherisdcode()
								{
									document.getElementById("fatherisdcode").value = document.getElementById("cell_no").value;
									document.getElementById('parentcontact').focus();
								}
								
								function getmotherisdcode()
								{
									document.getElementById("motherisdcode").value = document.getElementById("mothercell_no").value;
								}
								
								
								</script>
			<td >Father</td>
			<td style="width:140px;padding-right: 40px;">
			<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="parentname" id="parentname" value="<?=$pmname[0]?>" maxlength="100" size="20"></input>
			</td>


			<td style="width:140px;padding-right: 40px; ">
					<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="parentemail" id="parentemail" value="<?=$pmemail[0]?>" maxlength="50" size="20"></input>
			</td>

			<td style="width:210px;padding-right: 40px; padding-top: 6px;">
				<input id="cell_no" size="3" maxlength="3" style="color: white;border-radius: 0px;width:26px; height:25px; border-color: grey;margin-top: 5px;" name="cell_no" class="Box" type="text" value="<?= (isset($pmnodetails[0]) && $pmnodetails[0]!= "")?$pmnodetails[0]:"+91"?>" onblur="getfatherisdcode()"></input>
				<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="parentcontact" id="parentcontact" value="<?= isset($pmnodetails[1])?$pmnodetails[1] : "";?>" maxlength="10" size="16" onkeypress="return contanctKeypress(event)"></input>
			</td>

			<input type="hidden" name="fatherisdcode" id="fatherisdcode" value="<?= (isset($pmnodetails[0]) && $pmnodetails[0]!="")?$pmnodetails[0] : "+91"; ?>">
			
		
		</tr>

		<tr>
			<td>Mother</td>
			<td style="width:140px">
			<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="mothername" id="mothername" value="<?=$pmname[1]?>" maxlength="100" size="20"></input>
			</td>


			<td style="width:140px; ">
					<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="motheremail" id="motheremail" value="<?=$pmemail[1]?>" maxlength="50" size="20"></input>
			</td>

			<td style="width:202px; ">
				<input id="mothercell_no" size="3" style="color: white;border-radius: 0px;width:46px; height:25px; border-color: grey;margin-top: 5px;" name="mothercell_no" class="Box" type="text" value="<?= (isset($pmnomotherdetails[0]) && $pmnomotherdetails[0] != '')?$pmnomotherdetails[0] : "+91)";?>" onblur="getmotherisdcode()"></input>
				<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="motherscontact" id="motherscontact" value="<?= isset($pmnomotherdetails[1])?$pmnomotherdetails[1]:"";?>" maxlength="10" size="16" onkeypress="return contanctKeypress(event)" ></input>
			</td>

			<input type="hidden" name="motherisdcode" id="motherisdcode" value="<?= (isset($pmnomotherdetails[0]) && $pmnomotherdetails[0]!='') ? $pmnomotherdetails[0] : ""; ?>">
			

		
		</tr>
		</table>
		<br>
			<table>
				<tr>
					
					<td>
					
					<?php
					if($userArray['contactno_res'] != '--')
					{
						$exploded_no = explode("-", $userArray['contactno_res']);
					}
						
					 ?>
					<table style='padding-left:190px;'>
					<tr>
					<td><table>
						<tr>
						<td rowspan='2' style='padding-bottom: 18px;'>Landline:</td></tr></table></td>
						<!-- <td rowspan='2' style='padding-bottom: 18px;'>Landline:</td></tr></table></td> -->
					 <td><table>
						<tr><td>
						<script>
							function getisdcode()
								{
									document.getElementById("isdcode").value = document.getElementById("country_code").value;
								}
								function gotohome()
								{
									setTryingToUnload();
									window.location.assign('home.php');
								}
						</script>

						<input id="country_code" size="3" maxlength="3" style="color: white;width:40px; height:24px; border-color: grey;border-radius: 0px;" name="country_code" class="Box" type="text" value="<?= (isset($exploded_no[0]) && $exploded_no[0]!= '') ? $exploded_no[0] : "+91";?>" onblur="getisdcode()" height="4"></input>

						<input type="hidden" name="isdcode" id="isdcode" value="<?=(isset($exploded_no[0]) && $exploded_no[0]!='') ? $exploded_no[0] : "+91"; ?>">

						
	
					
					</td></tr>
						<tr><td style='text-align:center;font-size: 10px;'>ISD code</td>
						</tr>
					 </table></td>
					 <td> <table>
						<tr><td> <input type="text" size="6"  style="padding-bottom:3px;padding-left: 7px;border-color: grey;height: 17px;margin-top: 5px;" name="std_code" id="std_code" value="<?=$exploded_no[1]?>"></input></td></tr>
						<tr><td style='padding-bottom:4px;text-align:center;font-size: 10px;'>Area code</td></tr>
					 </table></td>
					<td>  <table>
						<tr><td> <input type="text" size="15"  style="padding-bottom:3px;padding-left: 7px;border-color: grey;height: 17px;margin-top: 5px;" name="residence_no" id="residence_no" value="<?=$exploded_no[2]?>" maxlength="10"></input></td></tr>
						<tr><td style='padding-bottom:5px;text-align:center;font-size: 10px;'>Landline</td></tr>
					 </table></td>
					</tr><table>
					</td>
				</tr>
			</table>
			<br>
		<!--<table>
		<tr><td style="width:640px;">
		<div id="address">
                    Address:</div> <textarea name="address" style="padding-left: 7px;border-color: grey;border-radius: 5px;height: 80px;margin-top: 5px;" id="address" rows="4" cols="73"><?=$userArray['address']?></textarea>
		</div>
		</td></tr>
		</table>-->
		<div style='padding-left:230px;'>
        <input type="submit" value="Submit"  name="updateRecord" id="submit_button" onclick="return validateForm()" style="margin-left: 0;color:white; background:#F46521;width=20px; padding: 2px 10px;"></input>
		<?php
		if(!isset($_GET['first']))
		{
		?>
      <!--  <a href="home.php" id="cancel_button"><div id="nextQuestion" class="button1">Cancel</div></a> -->
		<input style="margin-left: 0;color:white; background:#F46521;width=20px; padding: 2px 10px;" type="button"	value="Cancel" onclick="gotohome();" />
		<?php
		}
		?><!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
		<font size='2px;'>Note: <font style='color:red;'>*</font>indicates mandatory fields</font>  
		</div>
		<br><br>
		<?php
		if(isset($_GET['first']) && $_GET['first']==1)
		{
		?>
		<div style='padding-left:230px;'>
		<font size='4px;'>Please Update your profile details.</font>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" style="margin-left: 0;color:white; background:#F46521;width=20px; padding: 5px 50px;" value="Skip & Continue" onClick="gotohome();"/>
		<br>
		<br>
		</div>
		</td>
		</tr>
		
		</table>
		</div>
		<?php
		}
		?>
	</form>        
	</div>
		
	</div>
	 <script>
			 $("#cell_no").intlTelInput();
			 $("#country_code").intlTelInput();
			 $("#mothercell_no").intlTelInput();
    </script>


	<!--<div style='margin-left:942px;font-size: 1.0em;float:right;'  id="copyright">&copy; 2009-2014, Educational Initiatives Pvt. Ltd.</div>-->
<div style='margin-left:676px;font-size:12px;'>
<?php include("footer.php");?>
</div>
<?php
/**
 * Get user details from DB
 *
 * @param string $userID
 * @return DB result resource refereance
 */

 function db_saveUserDetails($userID)
{

	if($_POST['std_code'] != ''  && $_POST['residence_no'] != ''){
		$contactno_res = trim($_POST['isdcode'])."-".trim($_POST['std_code'])."-".trim($_POST['residence_no']);
	}
	else{
		$contactno_res = "";
	}
	
	$changed = 0;$phone_array = $name_array=$email_array=array();
	$cell_no = $parentname= $parentemail= '';
//	$query = "UPDATE adepts_userDetails SET
//			childEmail = '".mysql_escape_string($_POST['childEmail'])."',
//			gender = '".mysql_escape_string($_POST['gender'])."',
//			parentName = '".mysql_escape_string($_POST['parentname'])."',
//			parentEmail = '".mysql_escape_string($_POST['parentemail'])."',
//			contactno_res = '".mysql_escape_string($contactno_res)."',
//			contactno_cel = '".mysql_escape_string($_POST['cell_no'])."',
//			address = '".mysql_escape_string($_POST['address'])."'";
//	if(trim($_POST['schoolname']))
//		$query .= ", schoolName='".mysql_escape_string(trim($_POST['schoolname']))."'";
//
//	$query .= " WHERE userID = $userID";

$childbirthDate = explode("-",$_POST['startDate']);
$childbirthDate = $childbirthDate[2].'-'.$childbirthDate[1].'-'.$childbirthDate[0];


// $parentname = trim(formatallnames($_POST['parentname'])).','.trim(formatallnames($_POST['mothername']));
$parentemail = $_POST['parentemail'].','.$_POST['motheremail']; 
if($_POST['parentname'] != '')
{
	$fathername = trim(formatallnames($_POST['parentname']));
	array_push($name_array, $fathername);		
}
if($_POST['mothername'] != '')
{
	$mothername = trim(formatallnames($_POST['mothername']));
	array_push($name_array, $mothername);		
}
if($_POST['parentemail'] != '')
{
	$fatheremail = trim($_POST['parentemail']);
	array_push($email_array, $fatheremail);		
}
if($_POST['motheremail'] != '')
{
	$motheremail = trim($_POST['motheremail']);
	array_push($email_array, $motheremail);		
}
if($_POST['parentcontact'] != ''){
	$fathercellno = trim($_POST['fatherisdcode']).'-'.$_POST['parentcontact'];
	array_push($phone_array, $fathercellno);		
}
// else{
// 	$fathercellno = "";
// }

if($_POST['motherscontact'] != ''){
	$mothercellno = trim($_POST['motherisdcode']).'-'.$_POST['motherscontact'];	
	array_push($phone_array, $mothercellno);		
}
// else{
// 	$mothercellno = "";
// }
//$cell_no = $fathercellno.','.$mothercellno;

if(!empty($phone_array)){
$cell_no = implode(',', $phone_array);
}
if(!empty($name_array)){
$parentname = implode(',', $name_array);
}
if(!empty($email_array)){
$parentemail = implode(',', $email_array);
}

        if($_POST['gender']!='')
            $updateGender = $_POST['gender']=='Boy'?'B':'G';
			$query = "UPDATE educatio_educat.common_user_details SET
			childEmail = '".mysql_escape_string($_POST['childEmail'])."',
			gender = '".mysql_escape_string($_POST['gender'])."',
			dob = '".$childbirthDate."',
			parentName = '".mysql_escape_string($parentname)."',
			additionalEmail = '".mysql_escape_string($parentemail)."',
			contactno_res = '".mysql_escape_string($contactno_res)."',
			contactno_cel = '".mysql_escape_string($cell_no)."',
                        updated_by = username,
                        updated_dt = now(),
			city = '".mysql_escape_string($_POST['city'])."',
			address = '".mysql_escape_string($_POST['address'])."'";
	if(trim($_POST['schoolname']))
		$query .= ", schoolName='".mysql_escape_string(trim($_POST['schoolname']))."'";

	//new lines added for secret question & answer
	$secretQuestion = $_POST['secretQuestion'];
	
	if($secretQuestion != "")
	{
		$query .= ", secretQues = '".mysql_escape_string($_POST['secretQuestion'])."', secretAns='".mysql_escape_string($_POST['secretAnswer'])."'";
	}

	$query .= " WHERE MS_userID = $userID";	
    $result = mysql_query($query) or die("# $query #".mysql_error());// get res

	/*if($result)
	{
		echo "<script>alert('Details updated succesfully!');</script>";
		}*/

    //var_dump(mysql_error());
	if (mysql_error()){
    	return false;
     }
    else return true;
}
function formatallnames($str)
{
	$lowercase  = strtolower($str);
	return ucfirst($lowercase);
}
?>