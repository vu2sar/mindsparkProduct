<?php

set_time_limit (0);   //Otherwise quits with "Fatal error: minimum execution time of 30 seconds exceeded"
error_reporting(E_ERROR | E_PARSE |E_CORE_ERROR | E_CORE_WARNING);
@include("../userInterface/check1.php");

$userID     = $_SESSION['userID'];
$md5UserID = md5($userID);
if(isset($_REQUEST['cls']) && isset($_REQUEST['ttCode']) && isset($_SESSION['userID'])) { ?>
	<html>
	<head><script src="libs/jquery-1.10.2.js"></script></head>
	<body>
		<script>
			var myClass=<?=$_REQUEST['cls']?>;
			var ttCode="<?=$_REQUEST['ttCode']?>";
			$(document).ready(function(){
		       var baseUrl = "../app/worksheet/";
		       var createWorksheetUrl = baseUrl + 'api/dashboard/create_worksheet';
		       // proceed if any token/s are selected:
		       $.ajax({
		           url: createWorksheetUrl,
		           data: {class: myClass,user_id:'<?=$md5UserID?>',ajax:1},
		           type: 'post',
		           async: 'false',
		           success: function (id) {
		               if($.isNumeric(id))
		               {
		                   document.getElementById("wsm_id").value=id;
		                   document.getElementById("tt_code").value=ttCode;
		                   document.getElementById("frmBrowser").submit();
		               }
		               else
		               {
		                   var obj = jQuery.parseJSON(id);
		                   if(obj.eiCode == 'sessionError')
		                   {
		                       alert(obj.eiMsg);
		                       window.open('','_self').close();
		                   }
		                   else
		                   {
		                       alert(obj.eiMsg);
		                   }
		               }
		           },
				   async: 'false'
		       }); 
		       return false; 
			});
		</script>
	 	<form name="frmBrowser" id="frmBrowser" method="POST" action="../app/worksheet/api/dashboard/make_worksheet">
           <input type="hidden" name="wsm_id" id="wsm_id" value="'+id+'">
           <input type="hidden" name="tt_code" id="tt_code" value="'+ttCode+'">
           <input type="hidden" name="fromTopicDeactivation" id="fromTopicDeactivation" value="1">
        </form>
    </body>
	</html>
<?php } ?>