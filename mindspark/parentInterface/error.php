<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE HTML>
<html>
<head>
<!--<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">-->
<meta http-equiv="Content-Type" content="text/html;">
<meta http-equiv="X-UA-Compatible" content="IE=9">
<title>Mindspark | Error Page</title>

<link href="css/style.css" rel="stylesheet" type="text/css">
<style>
.middleConatainer
{
	width:625px;
	height:300px;
	margin:80px auto 120px auto;
	background-color:rgba(0,0,0,0.1);
	border-radius:20px;
	-webkit-border-radius:20px;
	-moz-border-radius:20px;
	padding:10px 20px 0px 20px;
    font-family: 'Conv_HelveticaLTStd-Roman';
}
.leftPart
{
	float:left;
	width:450px;
	margin:30px 0px 0px 10px;
	border-right:#000 double;
}
ol li
{
	font-size:14px;
	line-height:20px;
}
.rightPart
{
	float:left;
	width:150px;
	margin:60px 0px 0px 10px;
	line-height:20px;
}
</style>
<!--[if lt IE 9]>
<style type="text/css">
.middleConatainer
{
	background:#C0C0C0;
	background:transparent;
	filter:progid:DXImageTransform.Microsoft.Gradient(GradientType=1, StartColorstr='#C0C0C0',EndColorstr='#C0C0C0');
	zoom: 1;
}
</style>
<![endif]-->
</head>

<body>

<div id="header">
	<div id="eiColors">
    	<div id="orange"></div>
        <div id="yellow"></div>
        <div id="blue"></div>
        <div id="green"></div>
    </div>
</div>

<div id="continer">
	<div id="leftDiv">
    	<div id="logo"></div>
    </div>
    <div class="clear"></div>
    <div>
    	<div class="middleConatainer">
            <div class="leftPart">
                <h4>You have been logged out, this error has occured for one of the following reasons:</h4>
                <ol>
<!--                    <li>You have used Back/Refresh button of your browser.</li>
                    <li>You have logged in from another browser window or computer.</li>-->
                    <li>You have kept the browser window idle for long time.</li>
                    <li>You do not have javascript enabled on your browser.</li>
                       <li>Your session has expired.</li>
                </ol>
                <br>
            </div>
            <div class="rightPart">
                <h5>Click <a href="../userInterface/index2.php">here</a> to login again and start a new session.</h5>
            </div>
            <div style="clear:both"></div>
            <br>
        </div>
    </div>
</div>
<?php include("footer.php");?>