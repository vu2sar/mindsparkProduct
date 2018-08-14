<?php
	include("header.php");
	
	include("../userInterface/functions/functions.php");

	/* MODEL - Save data */
	if(isset($_POST) && array_key_exists('subBtnCngPass',$_POST)){
		$userArray = getDetails(); // find users details - based on username set in session
		$message = ''; //init
		#-- validate data
		if($_POST['oldPassword'] == $_POST['newPasswordalpha'])
		{
			$message = '<b>New password cannot be the same as current password!</b>';
		}
		if($userArray['username'] == $_POST['newPasswordalpha'])
		{
			$message = '<b>Login name and Password cannot be the same.</b>';
		}
		if(!validateOldPassword($_SESSION['userID'],$_POST['oldPassword']))
		{
			$message = '<b>Old password does not match.</b>';
		}
		# save to DB
		if(empty($message)){
			db_updateChangedPassDetails($_SESSION['userID']);
			$flag_secretq = 0;
			$flag_secretans = 0;
			if(trim($userArray['secretQues']) != trim($_POST['secretQuestion']))
			{
				$flag_secretq=1;
			}
			if(trim($userArray['secretAns']) != trim($_POST['secretAnswer']))
			{
				$flag_secretans=1;
			}
			if($flag_secretans and $flag_secretq)
			{
				$message = '<b>Your password, secret question, and secret answer have been changed</b>';
			}
			elseif (($flag_secretans==1) and ($flag_secretq==0) )
			{
				$message = '<b>Your password, secret answer have been changed</b>';
			}
			elseif (($flag_secretans==0) and ($flag_secretq==1) )
			{
				$message = '<b>Your password, secret question have been changed</b>';
			}
			else
				$message = '<b>Your password has been changed</b>';

			$message .= ', <b>Click on Home to continue</b>';
		}
	}


	/* Get fresh data to display in below form */
	$userArray = getDetails(); // find users details - based on username set in session
	$category = $userArray['category'];
	$subcategory = $userArray['subcategory'];
	$homePage = "home.php";
	if(strcasecmp($category,"School Admin")==0 || strcasecmp($category,"Teacher")==0 || strcasecmp($category,"Home Center Admin")==0)
		$homePage = "home.php";

	/*init ques type*/
	$question_array = array('What is your place of birth?','Who is your favourite cricketer?','What is your favourite car?','Who is your favourite actor or actress?','What is your favourite colour?');
?>


<title>Change Password</title>

<link href="libs/css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<link href="css/changePassword.css" rel="stylesheet" type="text/css">
<!-- <script src="libs/jquery.js"></script> -->
<script type="text/javascript" src="libs/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="libs/i18next.js"></script>
<script type="text/javascript" src="libs/translation.js"></script>
<script type="text/javascript" src="../userInterface/libs/closeDetection.js"></script>
<script>
	var langType = '<?=$language;?>';
	function load(){
		var fixedSideBarHeight = window.innerHeight;
		var sideBarHeight = window.innerHeight-95;
		var containerHeight = window.innerHeight-115;
		$("#fixedSideBar").css("height",fixedSideBarHeight+"px");
		$("#sideBar").css("height",sideBarHeight+"px");
		$("#container").css("height",containerHeight+"px");
	}
	
	function goHome()
	{
		setTryingToUnload();
		window.location = "home.php";
	}
</script>
			   
                <script type="text/javascript" src="libs/getscreen.js"></script>
                <script language="JavaScript" src="libs/gen_validatorv31.js" type="text/javascript"></script>
                <script type="text/javascript">
                //<![CDATA[
                        var is_ie6 = ('ActiveXObject' in window && !('XMLHttpRequest' in window));
                //]]>
                </script>
                <script type="text/javascript">
                        $(document).ready(function() {
                                //$( "div[rel]").overlay();
                                var bodyHeight = jQuery( "body").height();
                                var contWidth = pageWidth();
                                if( contWidth > 1024) {
                                        jQuery( ".hlp_img").css( "width", "1024px");
                                        jQuery( ".hlp_img img").css( "width", "1024px");
                                        contWidth = (contWidth - 1024)/2;
                                }
                                else {
                                        someval = ( contWidth * 75)/100;
                                        jQuery( ".hlp_img").css( "width", ( someval + "px"));
                                        jQuery( ".hlp_img img").css( "width", ( someval + "px"));
                                        contWidth = 0;
                                }
                                someother = ( jQuery( "body").height() - ( ( 768*75)/100)) /2;
                                someother = ( someother > 0)? someother : 20;
                                jQuery( ".hlp_img").css( "margin-top", someother + "px");
                                $(document).keyup(function(event){
                                        if (event.keyCode == 27) {
                                                jQuery( ".help_image").css( "display", "none");
                                        }
                                });

                                jQuery( ".gray_outer").bind( "click", function(e) {
                                        jQuery( ".help_image").css( "display", "none");
                                });
                        });

                        var spanxHeight;
                        var firstRun = 1;
                        function showHelp() {
                                jQuery( ".help_image").css( "display", "block");

                                if( firstRun == 1) {
                                        firstRun = 0;
                                        ratio = jQuery( "#help_img_home").width()/1024;
                                        jQuery.each( jQuery( "area"), function() {
                                                var coord = jQuery( this)[0].coords.split( ",");
                                                var a = coord[0] + "," +coord[1] + "," +coord[2] + "," +coord[3];
                                                for ( var i in coord) {
                                                        coord[ i ] = coord[ i ] * ratio;
                                                }
                                                jQuery( this)[0].coords = coord[0] + "," +coord[1] + "," +coord[2] + "," +coord[3];
                                        });

                                        jQuery.each( jQuery( "area"), function() {
                                                var offsetd = jQuery( this).context.coords.split(",")
                                                var offsets = jQuery( "#help_img_home").offset();
                                                var oTop = offsets.top + parseInt( offsetd[ 1 ]) - 130;
                                                var oLeft = offsets.left + parseInt( offsetd[ 0 ]);
                                                var mid = jQuery( this).context.id;
                                                if( oTop > 0) {
                                                        var customdiv = "<div class=\"tooltip_box_img\" id=\"tooltip_" + mid + "\" style=\"position: absolute; top: " + oTop + "px; left: " + oLeft + "px\">" + jQuery( this).context.alt +"</div>";
                                                }
                                                else {
                                                        oTop = offsets.top + parseInt( offsetd[ 3 ]);
                                                        var customdiv = "<div class=\"tooltip_box_img_invert\" id=\"tooltip_" + mid + "\" style=\"position: absolute; top: " + oTop + "px; left: " + oLeft + "px\">" + jQuery( this).context.alt +"</div>";
                                                }
                                                $( "body").append( customdiv);
                                        });

                                        jQuery( "area").bind( "mouseover", function( e) {
                                                jQuery( "#tooltip_" + jQuery( this).context.id).css( "display", "block");
                                        });

                                        jQuery( "area").bind( "mouseout", function( e) {
                                                jQuery( "#tooltip_" + jQuery( this).context.id).css( "display", "none");
                                        });
                                }
                        }

                        window.onload = function() {
                                var bodyHeight = pageHeight();
                                var contWidth = pageWidth();

                                if( contWidth > 1024) {
                                        contWidth = (contWidth - 1024)/2;
                                } else {
                                        contWidth = 0;
                                }

                                var contentHeight = ( pageHeight()>1024?1024:pageHeight()) - 55;
                                if( contentHeight < 560 ) {
                                        contentHeight = 560;
                                }

                                contentHeight -= 30;
                                spanxHeight = contentHeight;

                                var bHString = bodyHeight;
                                var chString = contentHeight;
                                var wString = contWidth;
                                var cWidth = pageWidth() - contWidth - contWidth -5;

                                $( "#score_page").css( {
                                        height: bHString
                                });

                                $( "#content").css( {
                                        height: ( spanxHeight)
                                });

                                if( is_ie6) {
                                        $( "#container").css( {width: cWidth});
                                }

                                if( wString > 0) {
                                        $( "#left_cover").css( {
                                                width: wString
                                        });

                                        $( "#right_cover").css( {
                                                width: wString
                                        });
                                }
                                else {
                                        $( "#left_cover").css( {
                                                "display": "none"
                                        });
                                        $( "#right_cover").css( {
                                                "display": "none"
                                        });
                                }

                                $( "#b_bar_left").css( {
                                        height: chString
                                });

                                $( "#b_bar_right").css( {
                                        height: chString
                                });

                                $( "#score_page").css( {
                                        display: "block"
                                });
                        }
                </script>
</head>
<body class="translation" onload="load()" onresize="load()">
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
		<div id="innerContainer">
			
			<div id="containerHead">
				<div id="triangle"> </div>
				<span>Change Password</span>
			</div>
			<div id="containerBody">
			<form action="changePassword.php" id="frmChngPwd" name="frmChngPwd" method="POST">
                                                    
                                                       
				<table  id="contentTab" cellpadding="3">
		
		<tr>
		
		
		
		<td style="padding: 0px 300px 0px 6px;"> <label for="teacherID">NAME</label>  </td>
		<td ><label style="" for="type">LOGIN ID</label></td> 
		
		
		
		
		</tr>
		
		
			<tr>
			<td><span style="color:#9ec956;"><?=$userArray['childName']?></span></td>
			<td >
				<span style="color:#9ec956;"><?=$userArray['username']?></span>
			</td>
			
			
			
		</tr>
		
		<tr>
		<td></td><td></td> 
		</tr>
		
		<tr>
		<td></td><td></td> 
		</tr>
		
		<tr>
			
			<td><span style="" >SECRET QUESTION </span></td>
			<td >
				<span >SECRET ANSWER </span>
			</td>
			
			
			
		</tr>
		
		<tr>
			
			<td>
				
				<select name="secretQuestion" id="secretQuestion" tabindex="1">
					<option value="">Select</option>';
					<?php
				    foreach ($question_array as $secQus)
					{
					if($userArray['secretQues'] ==$secQus){
					echo "<option value='$secQus' selected >$secQus</option>";
					}
					else
					echo "<option value='$secQus'>$secQus</option>";
					}
					?>
				</select>
			</td>
			<td >
				<input type="text" name="secretAnswer" id="secretAnswer" value="<?=$userArray['secretAns']?>"></input>
			</td>
			
			
			
		</tr>
		
		<tr>
		<td></td><td></td> 
		</tr>
		
		<tr>
		<td></td><td></td> 
		</tr>
		
		<tr>
			
			<td><span style="" >CURRENT PASSWORD </span></td>
			<td >
				<span >CHOOSE NEW PASSWORD </span>
			</td>
			
			
			
		</tr>
		
		<tr>
			
			<td>
				
				<input type="password" name="oldPassword" id="p1"></input>
			</td>
			<td >
				<input type="password" name="newPasswordalpha" id="newPasswordalpha" maxlength="15"></input>
			</td>
			
			
			
		</tr>
		
		<tr>
		<td></td><td></td> 
		</tr>
		
		<tr>
		<td></td><td></td> 
		</tr>
		
		<tr>
			
			<td><span style="" >RE-ENTER NEW PASSWORD </span></td>
			
			
			
		</tr>
		
		<tr>
			
			<td>
				
				<input type="password"  name="newPasswordbeta" id="newPasswordbeta" maxlength="15"></input>
			</td>
			
			
			
			
		</tr>
		</table>
			
				 <input type="submit" value="Submit" name="subBtnCngPass" onclick="setTryingToUnload();" id="submit_button" class="buttons"></input>&nbsp;&nbsp;&nbsp;
				<input type="button" id="button2"  value="Cancel" onclick="goHome();" class="buttons">
				
				<div style="margin-top:2%;">
				 <?php
				 if (isset($message)){
				  echo "<span class='flashMsg' style='color:red'>$message</span>";
					unset($message);
					}
					?>						                     
				</div>
			</div>
			</form>
			
			  <script language="JavaScript" type="text/javascript">
	     	var frmvalidator = new Validator("frmChngPwd");

	     	frmvalidator.addValidation("secretQuestion",'req','You do not have a secret question - please choose one and the answer to it. You will need to fill it to change your password in future.');
	     	frmvalidator.addValidation("secretAnswer",'req','Please specify the secret answer');

	     	//frmvalidator.addValidation("oldPassword",'req','Please specify the current password');

	     	frmvalidator.addValidation("newPasswordalpha", "req", "Please specify the new password");

	     	frmvalidator.addValidation("newPasswordalpha", "maxlen=15", "Your new password cannot be more than 15 characters");

	     	frmvalidator.addValidation("newPasswordbeta", "req", "Please re-enter your new password");

	     	frmvalidator.setAddnlValidationFunction("changePassValidation");
	     	</script>
		</div>
		
		
	</div>

<?php
/**
 * update user details table
 *
 * @param string $userID
 * @return Boolean T/F
 */
function db_updateChangedPassDetails($userID)
{
	if($userID and $_POST['newPasswordalpha']){
//	$query = "UPDATE adepts_userDetails SET
//			 password = password('".$_POST['newPasswordalpha']."'),
//			 secretQues = '".mysql_escape_string($_POST['secretQuestion'])."',
//			 secretAns = '".mysql_escape_string($_POST['secretAnswer'])."'
//			 WHERE userID = $userID ";
        $query = "UPDATE educatio_educat.common_user_details SET
			 password = password('".$_POST['newPasswordalpha']."'),
			 secretQues = '".mysql_escape_string($_POST['secretQuestion'])."',
			 secretAns = '".mysql_escape_string($_POST['secretAnswer'])."'
			 WHERE MS_userID = $userID ";
    //$result = mysql_query($query) or die("# $query #".mysql_error());// get res

    //var_dump(mysql_error());
    if(mysql_query($query)){
    	return true;
     }
     else return false;
	}
}
?>

<?php
/**
 * validate old pass for given user
 * @param string $userID
 * @param string $oldPassword
 * @return Boolean T/F
 */



?>

<?php include("footer.php") ?>