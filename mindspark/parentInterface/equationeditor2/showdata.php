<?php
//include "connect.php";
mysql_connect("122.248.235.120","educatio_educat","livedb615");
mysql_select_db("educatio_msguj");
	
	$query1 = "SELECT qID, data, imagefile, timeTaken, lastModified FROM PS_studentResp ORDER BY sno";
	$result1 = mysql_query($query1) or die (mysql_error());
?>
<!DOCTYPE HTML>
<html>
<head><title>Show Student data</title>
<link href="divst.css" rel="stylesheet" type="text/css">
</head>
<body>
<table border="1">
<tr><th>qID</th><th>data</th><th>image</th><th>timeTaken (secs)</th><th>lastModified</th></tr>
<?
if (mysql_num_rows($result1)!=0){
	while ($line=mysql_fetch_array($result1)){
		echo "<tr><td>".$line[0]."</td><td>".rawurldecode($line[1])."</td><td><img src='".$line[2]."' /></td><td>".$line[3]."</td><td>".$line[4]."</td></tr>";
	}
}
?>
</table>
</body>
</html>
