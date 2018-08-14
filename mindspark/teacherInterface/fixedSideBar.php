	<?php
	error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
	$todaysDate = date ( "d" );
	$username = $_SESSION ['username'];
	$userID = $_SESSION ['userID'];
	include("notifications.php");
	
	?>

<div class="menu-heading" style="height:5%;">
	<span style="color: white; font-size: 1.4em;">Shortcuts</span>

</div>
<a href="liveClasses.php" title="Live Class" style="height:12%;"><div class="icons">
		<div class="liveClass linkPointer" id="live"></div>
		<div class="iconText">Live Class</div>
	</div></a>
<a href="myClasses.php?openTab=3"  title="Activate Topic" style="height:12%;"><div class="icons">
		<div class="activateTopic linkPointer"></div>
		<div class="iconText">Activate Topic</div>
	</div></a>
<a href="resetStudentPassword.php" title="Reset Student Password" style="height:14%;"><div class="icons spacer">
		<div class="resetPassword linkPointer"></div>
		<div class="iconText">Reset Student Password</div>
	</div></a>
<a href="revisionSession.php" title='Revision Session' style="height:14%;">
	<div class="icons spacer">
		<div class="monthlyRevision linkPointer"></div>
		<div class="iconText">Activate Revision</div>
	</div>
</a>
<a href="cwa.php" title="Common Wrong Answers" style="height:16%;"><div class="icons">
		<div class="commonWrongAnswers linkPointer"></div>
		<div class="iconText">Common Wrong Answers</div>
	</div></a>

<?php
if(SERVER_TYPE!="LOCAL") { //dont show in offline mode
	$noOfNotifications = get_notification ( $username );
	
	?>
<a href="teacherforum/" onclick="trackTeacherForum();" title="Teacher Forum" style="height:12%;">
	<div class="icons">
    	<?php if($noOfNotifications>0) { ?>             
			<div class="notificationforum"><?=$noOfNotifications?></div>
        <?php  } ?>
		<div class="teacherForum linkPointer"></div>
		<div class="iconText">Teacher Forum</div>
	</div>
</a>
<?php } ?>

<a href="Comments.php" title='Comments' style="height:12%;">
	<div class="icons">
		<div class="comments linkPointer">
		<?php 
			  $commentNotification = getCommentNotification($userID);
			  if($commentNotification)
			  {
			  	echo '<label class="notification-container"><span class="notification-counter">'.$commentNotification.'</span></label>';
			  }	
		?>		    
		</div>
		<div class="iconText">Comments</div>
	</div>
</a>

<script>
		function trackTeacher()
		{
			$.ajax({
					  url: 'ajaxRequest.php',
					  type: 'post',
					  data: {'mode': 'doasmindspark','blog':true},
					  success: function(response) {
							  }
				  });
				  window.open("http://blog.ei-india.com/",'_blank');
		}
                function trackTeacherForum()
		{
			$.ajax({
					  url: 'ajaxRequest.php',
					  type: 'post',
					  data: {'mode': 'teacherForum'},
					  success: function(response) {
							  }
				  });				  
		}

			function updatecount()
			{
				$.ajax({
					  url: 'teacherforumsupport/ajax_forum.php',
					  type: 'post',
					  data: {'username':  "<?=$_SESSION['username']?>"},
					  success: function(response) {
									$('.notificationforum').html(response);
								  }
				  });
				
			}
	<?php
	if(SERVER_TYPE!="LOCAL") { //dont show in offline mode
		if (check_allow_forum ( $_SESSION ['username'], $_SESSION ['schoolCode'] )) {
			?>
			 setInterval(function(){updatecount() },300000);
			<?php
		}
	}
	?>
	 

	</script>