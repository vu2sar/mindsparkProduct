<?php
@include("check1.php");
include("classes/clsUser.php");
include("functions/functions.php");
$userID 	= $_SESSION['userID'];

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


/* MODEL - Save data */
if(isset($_POST) && array_key_exists('updateRecord',$_POST)){
	if(db_saveUserDetails($userID)){
		$message = 'Your details have been updated successfully!';
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
	<link href="css/myDetailsPage/lowerClass.css" rel="stylesheet" type="text/css">
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
	<link href="css/myDetailsPage/midClass.css" rel="stylesheet" type="text/css">
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
	<link href="css/myDetailsPage/higherClass.css" rel="stylesheet" type="text/css">
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
	<script src="libs/jquery.js"></script>
	<link rel="stylesheet" href="libs/css/jquery-ui-1.8.16.custom.css" />
	<script src="libs/jquery_ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="libs/i18next.js"></script>
	<script type="text/javascript" src="libs/translation.js"></script>
	<script type="text/javascript" src="/mindspark/js/load.js"></script>
	<script type="text/javascript" src="libs/closeDetection.js"></script>
	<link rel="stylesheet" href="/mindspark/js/plugins/countryFlagPlugin/css/intlTelInput.css">
    <script src="/mindspark/js/plugins/countryFlagPlugin/js/intlTelInput.js"></script>
	<link rel="stylesheet" style="text/css" href="/mindspark/css/CalendarControl.css" >
	<script language="javascript" type="text/javascript" src="/mindspark/js/CalendarControl.js" ></script>
	<script language="javascript" type="text/javascript" src="/mindspark/js/dateValidator.js"></script>

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
			var atpos=x.indexOf("@");
			var dotpos=x.lastIndexOf(".");
			var y=document.forms["updateRecordForm"]["parentemail"].value;
			var mothery=document.forms["updateRecordForm"]["motheremail"].value;
			var atpos1=y.indexOf("@");
			var dotpos1=y.lastIndexOf(".");
			var atpos2=mothery.indexOf("@");
			var dotpos2=mothery.lastIndexOf(".");
			var z=document.forms["updateRecordForm"]["gender"].value;
			var parent=document.forms["updateRecordForm"]["parentname"].value;
			var mother=document.forms["updateRecordForm"]["mothername"].value;
			var dob=document.forms["updateRecordForm"]["startDate"].value;
			
			if(dob != '')
			{
				var EnteredDate = dob; 
				var date = EnteredDate.substring(0, 2);
				var month = EnteredDate.substring(3, 5);
				var year = EnteredDate.substring(6, 10);
				var myDate = new Date(year, month - 1, date);
				var today = new Date();
				if (myDate > today) {
						alert("Entered date is greater than today's date ");
					return false;
				}
			}
			if(z==""){
				alert("Please select Gender!");
					return false;
			}
			if(parent==""){
					if(mother=="")
				{
					alert("Please enter Father / Mother Name!");
					return false;
				}
			}
			if(x != ""){
				if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
				{
					alert("Student e-mail address is not valid");
					return false;
				}
			}
			
			if(y){
				if (atpos1<1 || dotpos1<atpos1+2 || dotpos1+2>=y.length)
				{
					alert("Father e-mail address is not valid");
					return false;
				}
			}
			
			if(y==""){
					if(mothery=="")
				{
					alert("Please enter Father / Mother e-mail address");
					return false
				}
			}
			
			if(mothery){

					if (atpos2<1 || dotpos2<atpos2+2 || dotpos2+2>=mothery.length)
				{
					alert("Mother e-mail address is not valid");
					return false;
				}
			}

			if(!document.forms["updateRecordForm"]["parentcontact"].value)
			{
				if(!document.forms["updateRecordForm"]["motherscontact"].value)
				{
					alert("Please enter Father / Mother Mobile Number.");
					return false;
				}
			}


			setTryingToUnload();
			alert("Details updated successfully.");
		}
		$("input[type=text]").live("keypress",function(e){
//				if($(this).val().length>30){
//					return false;
//				}
				if($(this).attr("id")=="country_code" || $(this).attr("id")=="std_code" || $(this).attr("id")=="parentcontact" || $(this).attr("id")=="motherscontact" || $(this).attr("id")=="residence_no" || $(this).attr("id")=="cell_no"){
				e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
				if(e.keyCode == 13) {
					//alert($(this).attr('id'));
					var value = $(this).val();
					if (value == "") {
						//alert("null value checking");
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
					if($(this).val().length == 8 && $(this).attr("id")=="residence_no" && e.keyCode != 8 && e.keyCode!=46){
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
									<li><a href='javascript:void(0)'><span data-i18n="homePage.myBuddy"></span></a></li>
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
				<div id="packageEnd">Package End Date - <?=$endDate?>
				</div>
				<div id="packageStart">Package Start Date - <?=$startDate?>
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
				<div id="packageEnd">Package End Date - <?=$endDate?>
				</div>
				<div id="packageStart">Package Start Date - <?=$startDate?>
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
    <form action="myDetailsPageTheme2.php" name="updateRecordForm" id="updateRecordForm"  method="POST">
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
								<input style="border-color: grey;height: 20px;" type="text" name="startDate" id="startDate" onFocus="showCalendarControl(this);" size="18" onKeyUp="showCalendarControl(this);"  onBlur="validateDate(this);"   value="<?=$userArray['dob']?>"> 
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
			
			<td style="padding-left: 128px;">
				<table>
					<tr><td><div id="gender">Student Email ID</div></td></tr>
					<tr><td>
					<input type="text"  name="childEmail" id="childEmail" value="<?=$userArray['childEmail']?>" <?=$disabledStdEmailStatus?> maxlength="100" size="25" style="padding-left: 7px;border-color: grey;height: 20px;"></input>
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
		
		<td style="width:340px;padding-left: 56px;">
				<div id="school">City</div>
				<!--	<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="city" id="city" value="<?=$userArray['city']?>" maxlength="50" size="25"></input> -->

					<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;"  name="city" id="city" value="<?=$userArray['city']?>" >
		</td></tr></table>
		
		
			<div id="contact">Please fill in all details of either the father or the mother:</div>
		<table cellpadding="3" cellspacing="3">
		<tr>
		<td colspan='2' style='text-align:center;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name</td>
		<td style='padding-left: 40px;'>Email ID</td>
		<td style='padding-left: 35px;'>Mobile Number</td>
		</tr>

		<tr>
		<?php
			$pmname = explode(',', $userArray['parentName']);
			$pmemail = explode(',', $userArray['parentEmail']);
			$pmno = explode(',', $userArray['contactno_cel']);

			$pmnodetails = explode('-', $pmno[0]);
			$pmnomotherdetails = explode('-', $pmno[1]);
		
		?>
		<script>
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
					<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="parentemail" id="parentemail" value="<?=$pmemail[0]?>" maxlength="100" size="20"></input>
			</td>

			<td style="width:210px;padding-right: 40px; padding-top: 6px;">
				<input id="cell_no" size="3" maxlength="3" style="color: white;border-radius: 0px;width:26px; height:25px; border-color: grey;margin-top: 5px;" name="cell_no" class="Box" type="text" value="<?=$pmnodetails[0]?>" onblur="getfatherisdcode()"></input>
				<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="parentcontact" id="parentcontact" value="<?=$pmnodetails[1]?>" maxlength="100" size="16"></input>
			</td>

			<input type="hidden" name="fatherisdcode" id="fatherisdcode" value="<?=$pmnodetails[0]?>">
			
		
		</tr>

		<tr>
			<td>Mother</td>
			<td style="width:140px">
			<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="mothername" id="mothername" value="<?=$pmname[1]?>" maxlength="100" size="20"></input>
			</td>


			<td style="width:140px; ">
					<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="motheremail" id="motheremail" value="<?=$pmemail[1]?>" maxlength="100" size="20"></input>
			</td>

			<td style="width:202px; ">
				<input id="mothercell_no" size="3" style="color: white;border-radius: 0px;width:46px; height:25px; border-color: grey;margin-top: 5px;" name="mothercell_no" class="Box" type="text" value="<?=$pmnomotherdetails[0]?>" onblur="getmotherisdcode()"></input>
				<input type="text" style="padding-left: 7px;border-color: grey;height: 20px;margin-top: 5px;" name="motherscontact" id="motherscontact" value="<?=$pmnomotherdetails[1]?>" maxlength="100" size="16"></input>
			</td>

			<input type="hidden" name="motherisdcode" id="motherisdcode" value="<?=$pmnomotherdetails[0]?>">
			

		
		</tr>
		</table>
		<br>
			<table>
				<tr>
					
					<td>
					
					<?php
						$exploded_no = explode("-", $userArray['contactno_res']);
					 ?>
					<table style='padding-left:212px;'>
					<tr>
					<td><table>
						<tr><td rowspan='2' style='padding-bottom: 18px;'>Residential Phone Number:</td></tr></table></td>
					 <td><table>
						<tr><td>
						<script>
							function getisdcode()
								{
									document.getElementById("isdcode").value = document.getElementById("country_code").value;
								}
								function gotohome()
								{
									window.location.assign('home.php');
								}
						</script>

						<input id="country_code" size="3" maxlength="3" style="color: white;width:40px; height:24px; border-color: grey;border-radius: 0px;" name="country_code" class="Box" type="text" value="<?=$exploded_no[0]?>" onblur="getisdcode()" height="4"></input>

						<input type="hidden" name="isdcode" id="isdcode" value="<?=$exploded_no[0]?>">

						
	
					
					</td></tr>
						<tr><td style='text-align:center;font-size: 10px;'>ISD code</td>
						</tr>
					 </table></td>
					 <td> <table>
						<tr><td> <input type="text" size="6"  style="padding-bottom:3px;padding-left: 7px;border-color: grey;height: 17px;margin-top: 5px;" name="std_code" id="std_code" value="<?=$exploded_no[1]?>"></input></td></tr>
						<tr><td style='padding-bottom:4px;text-align:center;font-size: 10px;'>Area code</td></tr>
					 </table></td>
					<td>  <table>
						<tr><td> <input type="text" size="15"  style="padding-bottom:3px;padding-left: 7px;border-color: grey;height: 17px;margin-top: 5px;" name="residence_no" id="residence_no" value="<?=$exploded_no[2]?>"></input></td></tr>
						<tr><td style='padding-bottom:5px;text-align:center;font-size: 10px;'>Landline no.</td></tr>
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
      <!--  <a href="home.php" id="cancel_button"><div id="nextQuestion" class="button1">Cancel</div></a> -->
		<input style="margin-left: 0;color:white; background:#F46521;width=20px; padding: 2px 10px;" type="button" value="Cancel" onclick="gotohome();" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<font size='2px;'>Note: <font style='color:red;'>*</font>indicates mandatory fields</font>  
		</div>
		<br><br>
	</form>        
	</div>
		
	</div>
	 <script>
			 $("#cell_no").intlTelInput();
			 $("#country_code").intlTelInput();
			 $("#mothercell_no").intlTelInput();
    </script>


	<!--<div style='margin-left:942px;font-size: 1.0em;float:right;'  id="copyright">&copy; 2009-2014, Educational Initiatives Pvt. Ltd.</div>-->
<?php	if($theme==2) { ?>
<div style='margin-left:975px;font-size: 0.750em;'>
	<?php include("footer.php");?> </div> 
	<? } else {?>
	<div style='margin-left:695px;font-size: 1.0em !important;'>
	&copy; 2009-2014, Educational Initiatives Pvt. Ltd. </div>
<?php
}
/**
 * Get user details from DB
 *
 * @param string $userID
 * @return DB result resource refereance
 */

 function db_saveUserDetails($userID)
{
	$contactno_res = trim($_POST['isdcode'])."-".trim($_POST['std_code'])."-".trim($_POST['residence_no']);
	$changed = 0;
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


$parentname = $_POST['parentname'].','.$_POST['mothername'];
$parentemail = $_POST['parentemail'].','.$_POST['motheremail']; 

$fathercellno = $_POST['fatherisdcode'].'-'.$_POST['parentcontact'];
$mothercellno = $_POST['motherisdcode'].'-'.$_POST['motherscontact'];
$cell_no = $fathercellno.','.$mothercellno;


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

?>