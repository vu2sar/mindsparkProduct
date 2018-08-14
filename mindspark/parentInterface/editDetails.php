<?php
include("header.php");
//error_reporting(E_ALL);


if(isset($_POST['childSelectedID'])){
	$userID=$_POST['childSelectedID'];
	$userArray = getDetails($_POST['childSelectedID']);
	$category = $userArray['category'];
	$subcategory = $userArray['subcategory'];
	$gender = $userArray['gender'];
}

if(isset($_POST) && array_key_exists('updateRecord',$_POST)){
	if(db_saveUserDetails($_POST['userIDSelected'])){
		$message = 'Your details have been updated successfully!';
	}
	else{
		$message = 'Failed to update. Please verify your data.';
	}
	$userArray = getDetails($_POST['userIDSelected']);
	$category = $userArray['category'];
	$subcategory = $userArray['subcategory'];
	$gender = $userArray['gender'];
}





?>
<title>View Student Details</title>
<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css?ver=2" rel="stylesheet" type="text/css">
<link href="css/help.css" rel="stylesheet" type="text/css">
<link href="css/accountManagement.css" rel="stylesheet" type="text/css">
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
	/*function validateForm()
		{
			var x=document.forms["updateRecordForm"]["childEmail"].value;
			var atpos=x.indexOf("@");
			var dotpos=x.lastIndexOf(".");
			var y=document.forms["updateRecordForm"]["parentemail"].value;
			var atpos1=y.indexOf("@");
			var dotpos1=y.lastIndexOf(".");
			var z=document.forms["updateRecordForm"]["gender"].value;
			var parent=document.forms["updateRecordForm"]["parentname"].value;
			if(z==""){
				alert("Please select Gender!");
					return false;
			}
			if(parent==""){
				alert("Please enter Parent's name!");
					return false;
			}
			if(x != ""){
				if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
				{
					alert("Student e-mail address is not valid");
					return false;
				}
			}
			if(y!=""){
				if (atpos1<1 || dotpos1<atpos1+2 || dotpos1+2>=y.length)
				{
					alert("Parents e-mail address is not valid");
					return false;
				}
			}
			setTryingToUnload();
			alert("Details updated succesfully!");
		}*/
		/*$("input[type=text]").live("keypress",function(e){
//				if($(this).val().length>30){
//					return false;
//				}
				if($(this).attr("id")=="country_code" || $(this).attr("id")=="std_code" || $(this).attr("id")=="residence_no" || $(this).attr("id")=="cell_no"){
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
				}
			}
		});*/
</script>
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
            <td width="33%" id="sectionRemediation" class=""><div class="smallCircle red"></div><label class="textRed" value="secRemediation">View Student Details</label></a></td>
        </table>
        <?php include('referAFriendIcon.php') ?>
		<table class="studentDetails">
				<tr>
					<th>Child Name</th>
					<td>
						: <?=$userArray['childName']?>
					</td>
				</tr>
				<tr>
					<th>Mindspark Userame</th>
					<td>
						: <?=$userArray['username']?>
					</td>
				</tr>
				<tr>
					<th>Class</th>
					<td>
						: <?=$userArray['childClass']?>
					</td>
				</tr>
				<tr>
					<th>School Name</th>
					<td>
						: <?=$userArray['schoolName']?>
					</td>
				</tr>
				<tr>
					<th>City</th>
					<td>
						: <?=$userArray['city']?>
					</td>
				</tr>
				<tr>
					<th>Package Expiry Date</th>
					<td>
						: <?=$userArray['endDate']?>
					</td>
				</tr>
				<tr>
					<th>Parent Email ID</th>
					<td>
						: <?=$userArray['parentEmail']?>
					</td>
				</tr>
				<tr>
					<th>Parent Contact Number</th>
					<td>
						: <?=$userArray['contactno_cel']?>
					</td>
				</tr>
				<tr>
			</table>
		<!--<form action="editDetails.php" name="updateRecordForm" id="updateRecordForm"  method="POST">
		<input type="hidden" name="userIDSelected" value="<?=$userID?>"/>
		<div id="container_form">
		<div id="student"><b>Mindspark Username :</b> <?=$userArray['username']?></div>
		<div id="student"><b>Package Expiry Date :</b> <?=$userArray['endDate']?></div>
		<div id="student">Student e-mail  :</div>
                <input type="text" class="Box" name="childEmail" id="childEmail" value="<?=$userArray['childEmail']?>" <?=$disabledStdEmailStatus?> maxlength="100" size="100"></input>
		<div id="school">School  :</div>
                <input type="text" class="Box" name="schoolname" id="schoolname" value="<?=$userArray['schoolName']?>" maxlength="30" size="30"></input>
		<br/><br/>
		<div id="gender">Gender*  :</div>
		<select name="gender" id="genderStudent">
			<option value="">Select</option>
			<option value="Boy" <?php if($gender=="Boy"){echo "selected";}?>>Boy</option>
			<option value="Girl" <?php if($gender=="Girl"){echo "selected";}?>>Girl</option>
		</select>
		<div id="parentN">Parent's Name*  :</div>
        <input type="text" class="Box" name="parentname" id="parentname" value="<?=$userArray['parentName']?>" maxlength="30" size="30"></input>
		<div id="parentE">Parent's e-mail  :</div>
        <input type="text" class="Box" name="parentemail" id="parentemail" value="<?=$userArray['parentEmail']?>" maxlength="100" size="100"></input>
		<br/><br/>
		<div id="contactA">
        <?php
			$exploded_no = explode("-", $userArray['contactno_res']);
		 ?>
        Phone Number : +<input type="text" class="Box1" name="country_code" id="country_code" value="<?=$exploded_no[0]?>"></input><input type="text" class="Box2" name="std_code" id="std_code" value="<?=$exploded_no[1]?>"></input><input type="text" class="Box3" name="residence_no" id="residence_no" value="<?=$exploded_no[2]?>"></input><br/><br/> Phone :+(Mobile)<input type="text" class="Box" name="cell_no" id="cell_no" value="<?=$userArray['contactno_cel']?>" style="margin-left:20px;"></input>
		</div>
		<div id="address">
                    Address : <textarea name="address" class="Box4" id="address" rows="4" cols="30"><?=$userArray['address']?></textarea>
		</div>
		<div id="note">Note : *indicates mandatory fields</div>
        <input type="submit" value="Submit" class="loginButton greenButton" name="updateRecord" id="submit_button" onclick="return validateForm()"></input>
        <a href="home.php" id="cancel_button"><div id="nextQuestion" class="loginButton greenButton">Cancel</div></a>
		</div>
	</form>  -->
    </div>

<?php include("footer.php") ?>
<?php
/**
 * Get user details from DB
 *
 * @param string $userID
 * @return DB result resource refereance
 */
/*function db_saveUserDetails($userID)
{
	$contactno_res = trim($_POST['country_code'])."-".trim($_POST['std_code'])."-".trim($_POST['residence_no']);

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
        if($_POST['gender']!='')
            $updateGender = $_POST['gender']=='Boy'?'B':'G';
	$query = "UPDATE educatio_educat.common_user_details SET
			childEmail = '".mysql_escape_string($_POST['childEmail'])."',
			gender = '".mysql_escape_string($_POST['gender'])."',
			parentName = '".mysql_escape_string($_POST['parentname'])."',
			additionalEmail = '".mysql_escape_string($_POST['parentemail'])."',
			contactno_res = '".mysql_escape_string($contactno_res)."',
			contactno_cel = '".mysql_escape_string($_POST['cell_no'])."',
                        updated_by = username,
                        updated_dt = now(),
			address = '".mysql_escape_string($_POST['address'])."'";
	if(trim($_POST['schoolname']))
		$query .= ", schoolName='".mysql_escape_string(trim($_POST['schoolname']))."'";

	$query .= " WHERE MS_userID = $userID";
    $result = mysql_query($query) or die("# $query #".mysql_error());// get res

    //var_dump(mysql_error());
  if (mysql_error()){
    	return false;
     }
    else return true;
}*/
function getDetails($userID) {
$query = 'SELECT username, secretQues, secretAns, password, childName, childClass, childSection,DATE_FORMAT(childDob,"%d-%m-%Y") as dob, childEmail,parentName, parentEmail, city,state,country,pincode,gender,
		             contactno_res, contactno_cel, schoolName, address, DATE_FORMAT(startDate,"%d-%m-%Y") as startDate, DATE_FORMAT(endDate,"%d-%m-%Y") as endDate, category, subcategory
              FROM   adepts_userDetails WHERE userID = '.$userID;

    $result = mysql_query( $query);
    if( mysql_num_rows( $result)) {
        $row = mysql_fetch_array( $result, MYSQL_ASSOC);
        return $row;
    }
    else
        return;
}
?>