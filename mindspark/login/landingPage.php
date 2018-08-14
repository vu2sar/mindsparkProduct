<?php
	// ----------- Include section --------------- //

	include ("../userInterface/check1.php");
	include("loginFunctions.php");
	
	session_start();
	$subject_array = array();
	if(!isset($_SESSION['mse_user_id']) && !isset($_SESSION['ms_user_id']))
	{
		$_SESSION['loginPageMsg'] = 1;
        header("Location: index.php?login=0");
        exit;	
	}
	if(isset($_SESSION['ms_user_id']) && $_SESSION['ms_user_id'] != 0)
	{
		array_push($subject_array, 'maths');
	}	
	if(isset($_SESSION['mse_user_id']) && $_SESSION['mse_user_id'] != 0)
	{
		array_push($subject_array, 'english');
	}

	if(isset($_SESSION['mse_user_id']) && $_SESSION['mse_user_id'] == 0 && isset($_SESSION['ms_user_id']) && $_SESSION['ms_user_id'] == 0 )
	{
		$_SESSION['loginPageMsg'] = 1;
        header("Location: index.php?login=0");
        exit;
	}

?>
<html>
<head>
	<title></title>
	<meta http-equiv="X-UA-Compatible" content="IE=9">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="js/jquery.1.11.1.min.js"></script>
	<!-- Bootstrap core CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<style>
		@font-face {
		    font-family: 'Gotham-Book';
		    src: url('/mindspark/ms_english/theme/fonts/Gotham-Book.eot');
		    src: local('?'), url('/mindspark/ms_english/theme/fonts/Gotham-Book.woff') format('woff'), url('/mindspark/ms_english/theme/fonts/Gotham-Book.ttf') format('truetype'), url('/mindspark/ms_english/theme/fonts/Gotham-Book.svg') format('svg');
		    font-weight: normal;
		    font-style: normal;
		}
		body
		{
			background: url('../userInterface/assets/landingpage.jpg');
			font-family: "Gotham-Book";
		    background-size: 100%;
		}
		button
		{
			font-family: "Gotham-Book";
		}
		.7-height
		{
			height: 19%;
		}
		.full-height
		{
			height: 80%;
		}
		.subject_box
		{
			width  : 40%;
			display: inline-block;
			border-radius: 50%;
    		position: relative;
    		top: 10%;
		    cursor: pointer;
		}
		.subject_box img
		{
			border-radius: 50%;
		    -webkit-box-shadow: 1px 1px 60px 1px white;
		    -moz-box-shadow: 1px 1px 60px 1px white;
		    -ms-box-shadow: 1px 1px 60px 1px white;
		    -o-box-shadow: 1px 1px 60px 1px white;
		    box-shadow: 1px 1px 60px 1px white;
		}
		
		.seperator
		{
			position: relative;
			display: inline-block;
		}
		.seperator-vertical-bar
		{
			height: 50%;
			width: 4px;
			background: #6e6e6e;
			

		}
		.seperator-or
		{
			position: absolute;
		    background: #D8CC28;
	        border: 4px solid #A2971F;
		    font-size: larger;
		    font-weight: bolder;
		    border-radius: 50%;
		    width: 60px;
		    height: 60px;
		    left: -28px;
			top: 50%;
			-webkit-transform: translateY(-50%);
			-moz-transform: translateY(-50%);
			-ms-transform: translateY(-50%);
			-o-transform: translateY(-50%);
			transform: translateY(-50%);
			outline: none;
			color:#3e3e3e;
			cursor: default;
		}
		.full-height span
		{
			color:white;
			font-size:2em;
		}
		#logo
		{
		    background: url(/mindspark/login/assets/logo_header.png) no-repeat 0 0;
		    width: 213px;
		    height: 40px;
		    float: left;
		    margin-left: 30px;
		    padding-bottom: 20px;
		}
		/*footer*/
		.footer
		{
			text-align:right;
			position: absolute; 
			bottom: 0px; 
			overflow:hidden; 
			/*float: right;*/
			right: 20px;
			/*border:1px solid #054f7f; 
			background-color: #054f7f; */
			/*height:25px; */
			/*color:white; */
			/*width:100%;*/
		}
	</style>
</head>
<body>
	<?php
		echo "<input id='ms_user_id' type='hidden' value='".$_SESSION['ms_user_id']."'/>";
		echo "<input id='mse_user_id' type='hidden' value='".$_SESSION['mse_user_id']."'/>";
		echo '<input type="hidden" name="image1" id="image1" value="'.$image1.'"/>';
		echo '<input type="hidden" name="image2" id="image2" value="'.$image2.'"/>';
		echo '<input type="hidden" name="browser" id="browser" value="'.$browser.'"/>';
		echo '<input type="hidden" name="browserName" id="browserName" value="'.$browserName.'"/>';
		echo '<input type="hidden" name="browserVersion" id="browserVersion" value="'.$browserVersion.'"/>';
		echo '<input type="hidden" name="jsver" id="jsver" value="'.$jsver.'"/>';
		echo '<input type="hidden" name="cookies" id="cookies" value="'.$cookies.'"/>';
		echo '<input type="hidden" name="localStorage" id="localStorage" value="'.$localStorage.'"/>';

		$col_no = 12/count($subject_array);

	?>
	<div class="container">
		<div class="row 7-height">
			<div id="logo" class="pull-left">
			</div>
			<div class="pull-right">
				<a href="/mindspark/login/" style="margin-top:10px" class="btn btn-danger">Logout</a>
			</div>
		</div>
		<div class="row text-center">
			<span><h1 style="color:white">Choose One</h1></span>
		</div>
		<div class="row text-center">
			<?php 
				if(count($subject_array) == 1)
				{
					echo "<div class=\"col-xs-".$col_no." full-height\">"; 
						echo "<div class=\"subject_box\" img-data=\"".$subject_array[0]."\">";
							echo "<img src=\"../userInterface/assets/common-login-icons-".$subject_array[0].".png\" class=\"img-responsive\"/>"; 
							echo "<div class='text-center'>";
								echo "<span>".strtoupper($subject_array[0])."</span>";
							echo "</div>";
						echo "</div>";
							
					echo "</div>";
				}
				else
				{
					echo "<div class=\"col-xs-".$col_no." full-height\">";
						echo "<div class=\"subject_box\" img-data=\"".$subject_array[0]."\">";
							echo "<img src=\"../userInterface/assets/common-login-icons-".$subject_array[0].".png\" class=\"img-responsive\"/>"; 
							echo "<div class='text-center'>";
								echo "<span>".strtoupper($subject_array[0])."</span>";
							echo "</div>" ;
						echo "</div>";
							
						/*echo "<div class=\"seperator pull-right\">";
							echo "<div class=\"seperator-vertical-bar\"></div>";
							echo "<div class=\"seperator-or\">OR</div>";
							echo "<div class=\"seperator-vertical-bar\"></div>";
						echo "</div>";*/
					echo "</div>";

					for( $i = 1 ; $i < count($subject_array) - 1 ; $i++ )
					{
						echo "<div class=\"col-xs-".$col_no." full-height\">"; 
							echo "<div class=\"subject_box\" img-data=\"".$subject_array[$i]."\">";
								echo "<img src=\"../userInterface/assets/common-login-icons-".$subject_array[$i].".png\" class=\"img-responsive\"/>"; 
								echo "<div class='text-center'>";
									echo "<span>".strtoupper($subject_array[$i])."</span>";
								echo "</div>";
							echo "</div>";
								
							/*echo "<div class=\"seperator pull-right\">";
								echo "<div class=\"seperator-vertical-bar\"></div>";
								echo "<div class=\"seperator-or\">OR</div>";
								echo "<div class=\"seperator-vertical-bar\"></div>";
							echo "</div>";*/
						echo "</div>";
					}
						
					echo "<div class=\"col-xs-".$col_no." full-height\">"; 
						echo "<div class=\"subject_box\" img-data=\"".$subject_array[count($subject_array) - 1]."\">";
							echo "<img src=\"../userInterface/assets/common-login-icons-".$subject_array[count($subject_array) - 1].".png\" class=\"img-responsive\"/>"; 
							echo "<div class='text-center'>";
								echo "<span>".strtoupper($subject_array[count($subject_array) - 1])."</span>";
							echo "</div>";
						echo "</div>";
							
					echo "</div>";
				}

		?>
		<div class="row footer"> &copy 2009-2016, Educational Initiatives Pvt. Ltd.</div>
		<script type="text/javascript" src="../userInterface/libs/brwsniff.js?ver=9"></script>	
	<script>
		var user_data = {
			ms_user_id : $('#ms_user_id').val(),
			mse_user_id : $("#mse_user_id").val()
		};
		Object.freeze(user_data);

		if( $('#ms_user_id').val() === '' && $('#mse_user_id').val() === '')
		{
			window.location = '/mindspark/login/index.php';
		}

		$(".subject_box").click(function(){
			var subject = $(this).attr('img-data');

			if(subject == "maths")
			{
				window.location = "mindsparkMaths.php?image1="+$('#image1').val()+"&image2="+$('#image2').val()+"&browser="+$('#browser').val()+"&browserName="+$('#browserName').val()+"&browserVersion="+$('#browserVersion').val()+"&jsver="+$('#jsver').val()+"&cookies="+$('#cookies').val()+"&localStorage="+$('#localStorage').val();
			}
			else if(subject == "english")
			{
				// Added by rochak for english interface to get reference call for multitab. 
                	sessionStorage.setItem('user','yes');
                // -- Code added ends here -- //
				$('#ms_user_id').val('');
				$('#mse_user_id').val('');
				var os = getOS();
    			var osDetails =  os[0]+os[1];
    			osDetails = osDetails.replace(/ /g,'');
    			var browserName = $('#browserName').val();
    			var browserVersion = $('#browserVersion').val();
				window.location = "../ms_english/Language/login/index/"+user_data.mse_user_id+"/"+osDetails+"/"+browserName+"/"+browserVersion;
			}
		});
	</script>
</body>
</html>