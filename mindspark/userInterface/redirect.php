<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Validate</title>
<script src="libs/jquery.js"></script>
<script language="javascript" type="text/javascript">
var currentWindowName;
if(window.sessionStorage)
currentWindowName=sessionStorage.getItem('windowName');
else
currentWindowName=window.name;
if (currentWindowName == "") {
	$.post("controller.php","mode=createWindowName",function(data) {
		if(data)
		{
			//window.name = data;
			if(window.sessionStorage)
			sessionStorage.setItem('windowName',data);
			else
			window.name=data;
			window.location.href = "home.php";
			/*var windowFeatures ='channelmode=0, directories=0, location=1, menubar=0,resizable=1, scrollbars=1,status=1,titlebar=0,toolbar=0,top=0,left=0,width=100%,height=100%';
			window.open("home.php", data);
			window.opener = top;
			window.close();*/
		}
	});
}
else if (currentWindowName == "invalidAccess") {
	alert("Multitab not allowed.");
    window.open("newTab.php", "_self");
}
else {
	alert("Multitab not allowed.");
    
	if(window.sessionStorage)
	sessionStorage.setItem('windowName','invalidAccess');
	else
	window.name = "invalidAccess";
    window.open("newTab.php", "_self");
}
</script>
</head>

<body>
</body>
</html>