<?php
	include("../userInterface/check1.php");
	include("../userInterface/classes/clsUser.php");
	if(!isset($_SESSION['userID']))
	{
		header("Location:error.php");
		exit();
	}
	$userID       = $_SESSION['userID'];
	$sessionID    = $_SESSION['sessionID'];
	$feedbackset  = $_POST['feedbackset'];
	$category     = $_SESSION['admin'];
	if(isset($_POST["feedbacktype"]))
		$feedbacktype = $_POST["feedbacktype"];
	else
		$feedbacktype = "";
	$feedbackResponse = get_magic_quotes_gpc()?json_decode(stripslashes($_POST["feedbackResponse"])):json_decode($_POST["feedbackResponse"]);
?>
<html>
<body>
<form method="POST" action="home.php" id="frmHidForm">
	<div>Thank you for providing the feedback! <br/> Please wait, loading the next screen....</div>
	<input type="hidden" name="mode" value="nextAction">
</form>

<?php
	foreach ($feedbackResponse as $key => $value) 
	{
		$query  = "INSERT INTO adepts_feedbackresponse (userID, qid, response, feedbackset, feedbackdate, category) VALUES ($userID, $key, \"".mysql_real_escape_string($value)."\", $feedbackset, now(),'$category')";
		mysql_query($query) or die(mysql_error().$query);		
	}
?>
<script>
	document.getElementById('frmHidForm').submit();
</script>
</body>
</html>