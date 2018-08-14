<?php
session_start();
session_destroy();

if(isset($_SERVER['HTTP_COOKIE']))
{
	$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
	foreach ($cookies as $cookie)
	{
		$parts = explode('=', $cookie);
		$name = trim($parts[0]);
		setcookie($name, '', time() - 1000);
		setcookie($name, '', time() - 1000, '/');
	}
}

//include("header.php");
?>
<title>Mindspark | Error Page</title>

<link href="css/login/style.css" rel="stylesheet" type="text/css">
<style>
.middleConatainer
{
	width:625px;
	height:360px;
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
	/*border-right:#000 double;*/
}
ol li
{
	font-size:14px;
	line-height:20px;
}
.rightPart
{
	float: left;
    /* width: 150px; */
    padding: 16px;
    margin-bottom: 12px;
    margin-left: 43%;
    /* opacity: 0.8; */
    text-align: center;
    background-color: #9ec956;
    /* margin: 60px 0px 0px 10px; */
    /* line-height: 26px; */
    cursor: pointer;
    color: black;
}
.rightPart:hover {
	transform : scale(1.05);
    -webkit-transform : scale(1.05);
    -moz-transform : scale(1.05);
    -o-transform: scale(1.05);
    -ms-transform: scale(1.05);
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
<script>
	function redirectToLogin(){
		if(window.top==window.self){
			window.location.href="../login/index.php";
		}
		else {
			window.top.location.href="/mindspark/login/index.php";
		}
	}
</script>
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
			<?php if(isset($_REQUEST["mode"]) && $_REQUEST["mode"]==4) { ?>
				<br><br><br><br>
				<h4>YOU WILL HAVE TO UPGRADE YOUR BROWSER TO CONTINUE USING MINDSPARK.</h4>
			
			<?php } else { ?>
				<h4>You have been logged out, this error has occured for one of the following reasons:</h4>
                <ol>
                    <li>You have used Back/Refresh button of your browser.</li>
                    <li>You have logged in from another browser window or computer.</li>
                    <li>You have kept the browser window idle for long time.</li>
                    <li>You do not have javascript enabled on your browser.</li>
                       <li>Your session has expired.</li>
					   <li>You might have opened Mindspark in multiple tabs. </li>
                </ol>
			<?php } ?>
                <br>
            </div>
            <a href="#" onClick="redirectToLogin();">
            <div class="rightPart">
                Login Again 
            </div>
            </a>
            <div style="clear:both"></div>
            <br>
        </div>
    </div>
</div>
<?php include("footer.php");?>