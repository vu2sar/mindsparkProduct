<?php
//set_time_limit (0);   //Otherwise quits with "Fatal error: Maximum execution time of 30 seconds exceeded"
include("connect.php");

$upload = "";
$keys = array_keys($_REQUEST);
foreach ($keys as $key){
	${$key} = $_REQUEST[$key];
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>MS Math Guj - upload files</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" href="css/admin_style.css" rel="stylesheet">
<script src="js/menubar.js" type="text/javascript"></script>
<!-- style tag for setting font size, border, height etc -->
<style>
TH
{
	COLOR: Black; FONT-FAMILY: verdana, arial, helvetica; FONT-SIZE: 13px; FONT-WEIGHT: normal
}
TD
{
	COLOR: Black; FONT-FAMILY: verdana, arial, helvetica; FONT-SIZE: 11px; FONT-WEIGHT: normal
}
input
{
	COLOR: Black; FONT-FAMILY: verdana, arial, helvetica; FONT-SIZE: 11px; FONT-WEIGHT: normal
}
</style>
<link rel="stylesheet" style="text/css" href="../css/CalendarControl.css" >
</head>
<body>

<form method="POST" action="" id="uploadFile" name='uploadFile' enctype="multipart/form-data">
<table>
	<input type="radio" value="img" id="rdUploadType" name="rdUploadType" checked>Other file  
	<input type="radio" value="question_files" id="rdUploadType" name="rdUploadType">question_files
	<input type="radio" value="image" id="rdUploadType" name="rdUploadType">image
	<tr>
		<td ><b>Upload </b>  </td><td>:</td>
		<td ><input type="file" name="file1" id="file1" ></td>
	</tr>
	<tr>
		<td ><input type="submit" name="upload" id="upload" value="Upload" ></td>
	</tr>
</table>

<?
if($upload=="Upload")
{
		$filename = $_FILES['file1']['name'];
		$temp_filename=$_FILES['file1']['tmp_name'];
		if($filename!="")
		{
			
			if($rdUploadType == "question_files")
			{
				move_uploaded_file($temp_filename,"question_files/".$filename) or die("Error");
			}				
			else if($rdUploadType == "image")
			{
				move_uploaded_file($temp_filename,"image/".$filename) or die("Error");
			}	
			else
			{
				move_uploaded_file($temp_filename,$filename) or die("Error");	
			}
			
							
			echo "<b>Uploaded successfully.</b><br>";
		}
		else
			echo "<b>Please select file to upload.</b><br>";
	
}
?>
</form>
</body>
</html>