<!doctype html>
<?php

set_time_limit(0);
include('../check.php');
checkPermission('MNU');

include('common_functions.php');
if(isset($_POST['hdnAction']) && $_POST['hdnAction'] == 'sendKudo')
{
	sendKudo();
	echo "<script>window.location=\"home.php\"</script>";
}

$myWall = FALSE;

if(isset($_REQUEST['wall']) && $_REQUEST['wall']=='my')
	$myWall = TRUE;

$arrKudos = getAllKudos($myWall);
	
?>
<html>
	<head>
		<title>
			Kudos - Home
		</title>
		<link rel="stylesheet" href="styles/inputosaurus.css" />
		<link rel="stylesheet" href="styles/jquery-ui.css" />		
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
		<script src="js/jquery-ui.js"></script>
		<script type="text/javascript" src="js/inputosaurus.js"></script>		
        
		<script>
			$(function() 
			{	
					
					var to = $( "#userid" ),
			  message = $( "#txtMessage" ),
			  allFields = $( [] ).add( to ).add( message ),
			  tips = $( ".validateTips" );	
			
				function split( val ) {
                return val.split( /,\s*/ );
            }
            function extractLast( term ) {
                return split( term ).pop();
            }

            $('#userid').inputosaurus({
				width : '245px',
				autoCompleteSource : 'names_ajax.php',
				activateFinalResult : true,
				change : function(ev){
					$('#useridval').val(ev.target.value);
				}
			});

			function updateTips( t ) {
			  tips
			    .text( t )
			    .addClass( "ui-state-highlight" );
			  setTimeout(function() {
			    tips.removeClass( "ui-state-highlight", 1500 );
			  }, 500 );
			}

			function checkLength( o, n, min, max ) {
			  if ( o.val().length > max || o.val().length <= min ) {
			   // o.addClass( "ui-state-error" );
			    alert( "Length of " + n + " must be between " +
			      min + " and " + max + "." );
			    return false;
			  } else {
			    return true;
			  }
			}

			function checkRegexp( o, regexp, n ) {
			  if ( !( regexp.test( o.val() ) ) ) {
			    o.addClass( "ui-state-error" );
			    updateTips( n );
			    return false;
			  } else {
			    return true;
			  }
			}
			
			$( "#btnSendKudo" ) 
			.button()
			 .click(function() 
			 {
			  	 var bValid = true;
			      allFields.removeClass( "ui-state-error" );

			     var userid = $( '#useridval' ).val();
			     var useridAutoComp = $( '#userid' ).val();
			     var msg = $( '#txtMessage' ).val();
				 msg = $.trim(msg);
				 msg = msg.replace(/\s{2,}/g, ' ');
				 $( '#txtMessage' ).val(msg);
				 var self = '<?php echo $_SESSION["username"]; ?>';
				  
				  if(userid.length == 0)
				  {
					  	alert('Please fill in the person you wish to send a Kudo to');
						bValid = false;
						return false;
				  }
				  /*else if(userid.length > 30)
				  {
				  		alert('Length of To should be less than 30');
						bValid = false;
						return false;
				  }*/
				  else if(self == userid)
				  {
				  		alert('You cannot send a kudo to yourself');
						bValid = false;
						return false;	
				  }
				  
				  if(msg.length == 0)
				  {
				  		alert('Please fill in the message for the Kudo');
						bValid = false;
						return false;
				  }
				  else if(msg.length < 25)
				  {
				  		alert('Message length is too short \n It should atleast be 25 characters');
						bValid = false;
						return false;
				  }
				  else if(msg.length > 300)
				  {
				  		alert('Length of Message should be less than 300');
						bValid = false;
						return false;
				  }

			      if ( bValid ) 
				  {
				  	if(confirm("Do you wish to send the kudo?"))
					{
						$( "#hdnAction" ).val('sendKudo');
						$( "#hdnTo" ).val($( "#useridval" ).val());
						$( "#hdnMessage" ).val($( "#txtMessage" ).val());
						$( "#formmain" ).submit();
					}			       					
			      }
			  });

			$( "#sendKudo" ).dialog({
			  dialogClass: "sendKudoDialog",
			  resizable: false,
			  draggable: false,
			  autoOpen: false,
			  title: "Send a Kudos..!!",
			  height: 450,
			  width: 300,
			  modal: true,
			  
			 /* show: {
		        effect: "blind",
		        duration: 1000
		      },
			  
		      hide: {
		        effect: "clip",
		        duration: 1000
		      },*/
			  
				/*$('btnThankYou').disabled = true;
				$('btnGoodWork').disabled = true;
				$('btnImpressive').disabled = true;
				$('btnExceptional').disabled = true;*/
			  
			  
			  
			  close: function() {
			  	$( "#userid" ).val('');
				$( "#txtMessage" ).val('');
				//$( "input" ).val('');
			    allFields.val( "" ).removeClass( "ui-state-error" );				
			  }			
			});

			$( "#btnThankYou" )
			  .button()
			  .click(function() {
			  	$( "#hdnType" ).val('Thank You');
				var srcImg = "images/thankyou.png";
            	$( '#typeImage' ).attr("src", srcImg);
			    $( "#sendKudo" ).dialog( "open" );
			  });
			  $( "#btnGoodWork" )
			  .button()
			  .click(function() {
			  	$( "#hdnType" ).val('Good Work');
				var srcImg = "images/goodwork.png";
            	$( '#typeImage' ).attr("src", srcImg);
			    $( "#sendKudo" ).dialog( "open" );
			  });
			  $( "#btnImpressive" )
			  .button()
			  .click(function() {
			  	$( "#hdnType" ).val('Impressive');
				var srcImg = "images/impressive.png";
            	$( '#typeImage' ).attr("src", srcImg);
			    $( "#sendKudo" ).dialog( "open" );
			  });
			  $( "#btnExceptional" )
			  .button()
			  .click(function() {
			  	$( "#hdnType" ).val('Exceptional');
				var srcImg = "images/exceptional.png";
            	$( '#typeImage' ).attr("src", srcImg);
			    $( "#sendKudo" ).dialog( "open" );
			  });
			});
		</script>
	</head>
	<body>
		<br/>
		<br/>
		<form name="formmain" id="formmain" method="POST">	
			<input type="hidden" name="hdnAction" id="hdnAction" value="<?php echo  $_POST['hdnAction']?>"/>	
			<input type="hidden" name="hdnType" id="hdnType" value="<?php echo  $_POST['hdnType']?>"/>	
			<input type="hidden" name="hdnTo" id="hdnTo" value="<?php echo  $_POST['hdnTo']?>"/>	
			<input type="hidden" name="hdnMessage" id="hdnMessage" value="<?php echo  $_POST['hdnMessage']?>"/>
			<div id='kudos_header' class="kudos_header">
			<?php
				include('kudos_header.php');
			?>
			<table align="center" width="100%" border="0" class="tblTypes" style="background-image: url('../images/background/bg.png')">
				<tr>
					<td>
						<input type="button" class="TypeButton" name="btnThankYou" id="btnThankYou" value="Thank You" style="background-color: #FF9146;"/>
					</td>
					<td>
						<input type="button" class="TypeButton" name="btnGoodWork" id="btnGoodWork" value="Good Work"/>
					</td>
					<td>
						<input type="button" class="TypeButton" name="btnImpressive" id="btnImpressive" value="Impressive"/>
					</td>
					<td>
						<input type="button" class="TypeButton" name="btnExceptional" id="btnExceptional" value="Exceptional"/>
					</td>
				</tr>
			</table>
			</div>
			<div id="kudo_body">
			<table id="TblKudos" align="center" width="95%" border="0">
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
						$imageSrc = preg_replace('/\s+/', '', $type);
						$imageSrc = strtolower($imageSrc);
						
						if($onGoingMonth != $month || $onGoingYear != $year)	
						{
							$onGoingMonth = $month;
							$monthCnt++;
							$onGoingYear = $year;
							$i = 0;
						?>	
							<tr>
								<td width="33%">
									<hr />
								</td>
								<td id="Divider" width="33%">
									- - - - - <?php echo  date('F, Y', strtotime("01-$month-$year"))?> - - - - -
								</td>
								<td width="33%">
									<hr/>
								</td>							
							</tr>
						<?php
						}		
						if($i%3==0)
							echo '<tr>';
						?>
							<td width="33%">
								<table border="0" width="100%" id="KudoSummary">
									<tr>
										<td rowspan="2" id='Figurine'>
											<img src="images/<?php echo  $gender?>.png" title="<?php echo  fetchFullName($kudo_details['receiver'])?>" height="50px" width="50px"/>
										</td>
										<td colspan="2" style="text-align: left;">
											<?php echo  fetchFullName($kudo_details['receiver'])?> received <?php echo  $type?> from <?php echo  fetchFullName($kudo_details['sender'])?>
										</td>
									</tr>
									<tr>
										<td id='Date'>
											<?php echo  date('d F, Y', strtotime($kudo_details['sent_date']))?>
										</td>
										<td width="40%" style="text-align: right;">
											<img title="<?php echo  $kudo_details['message']?>" src="images/<?php echo  $imageSrc?>.png" height="100px" width="100px">
										</td>
									</tr>
								</table>
							</td>
						<?php
						if($i%3==2)
							echo '</tr>';
						$i++;
					}
				}
				?>
			</table>
			</div>			
			<? // MODAL BOX FOR SENDING KUDOS ?>
			<table id="sendKudo" class="sendKudo" border="0" width="100%">
					<tr>
						<td>
							<img id='typeImage'/>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" autocomplete=off name="userid" class="To" id="userid" placeholder="To" required="" spellcheck="false" title="To"/>
							<input type="hidden" name="useridval" id="useridval">
						</td>
					</tr>
					<tr>
						<td>
							<textarea class="Message" name="txtMessage" id="txtMessage" rows="5" cols="30" placeholder="Message" title="Message"></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<hr/>
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" id="btnSendKudo" name="btnSendKudo" class="btnSendKudo" value="Send" />
						</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
					</tr>
			</table>
		</form>
	</body>
</html>