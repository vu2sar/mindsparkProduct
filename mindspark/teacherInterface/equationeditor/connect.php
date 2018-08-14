<?
	$link = mysql_connect("10.138.162.98","educatio_educat","livedb615")  or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");
	mysql_select_db ("educatio_msguj")  or die ("Could not select database...");
	//$link = mysql_connect("programserver","root","")  or die ("Could not connect to localhost");
	//mysql_select_db ("educatio_educat")  or die ("Could not select database");

	putenv('TZ=IST-5:30');
?>
