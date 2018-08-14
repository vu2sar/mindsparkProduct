<?php

	$link = mysql_connect("192.168.0.10","root","")  or die (mysql_errno()."-".mysql_error()."Could not connect to localhost");
	mysql_select_db ("educatio_educat")  or die ("Could not select database".mysql_error());
	putenv('TZ=IST-5:30');
?>