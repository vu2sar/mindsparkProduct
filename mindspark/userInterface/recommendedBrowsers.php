<?php

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
<link rel="stylesheet" href="css/commonMidClass.css" />
<title>Recommended Browsers</title>
<style>
	.browserTbl {
	font-size:14px;
	border-collapse: collapse;
}

body{
	font-family:"arial";
}

.trHead{
	height:50px;
}

#specifyMessage{
	width:45%;
	margin-left:28%;
	margin-top:6%;
	margin-bottom:30px;
	font-size:1.5em;
	font-weight:bold;
}
#recommended{
	width:45%;
	margin-left:28%;
	margin-bottom:30px;
	font-size:1.4em;
	color:green;
	font-weight:bold;
}
#secondary{
	margin-left:28%;
	margin-top:40px;
	width:45%;
	margin-bottom:30px;
	font-size:1.2em;
	color:blue;
	font-weight:bold;
}

.chrome {
	background: url('assets/chrome.gif') no-repeat 3px 7px;
	width: 45px;
	height: 40px;
}

.explorer {
	background: url('assets/explorer.gif') no-repeat 3px 7px;
	width: 45px;
	height: 40px;
}
.firefox {
	background: url('assets/firefox.gif') no-repeat 3px 7px;
	width: 45px;
	height: 40px;
}

.safari {
	background: url('assets/safari.gif') no-repeat 3px 7px;
	width: 45px;
	height: 40px;
}

</style>
</head>
<body>
	<div id="specifyMessage">Please upgrade your browser to a version as listed below.</div>
	<div id="recommended">Mindspark recommend that you install and use Windows OS with Chrome for best results.</div>
	<table align='center' valign='center' border='0' class='browserTbl' width='45%;'><tr class='trHead'><td class="chrome"></td><td><a href='https://www.google.com/intl/en/chrome/browser/' target='_blank' style='text-decoration:underline;color:blue;'>Install the latest version of Chrome </a></td></tr></table>
	<div id="secondary">You may use latest versions of other browsers too. Chose the best one suitable for your system:</div>
	<table align='center' valign='center' border='0' class='browserTbl' width='45%;'><tr class='trHead'><td class="firefox"></td><td><a href='http://www.mozilla.org/en-US/firefox/new/' target='_blank' style='text-decoration:underline;color:blue;'>Install latest version of Firefox </a></td></tr><tr class='trHead'><td class="explorer"></td><td><a href='http://windows.microsoft.com/en-IN/internet-explorer/download-ie' target='_blank' style='text-decoration:underline;color:blue;'>Install latest version of Internet Explorer </a>(only for Windows 7/ Windows 8/ Vista)</td></tr><tr class='trHead'><td class="safari"></td><td><a href='http://support.apple.com/downloads/#safari' target='_blank' style='text-decoration:underline;color:blue;'> Install the latest version of Safari </a>(Only for Mac)</td></tr></table>
</body>
</html>
