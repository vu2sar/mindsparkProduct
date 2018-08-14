 var androidVersionCheck=0;
$(document).ready(function(e) {
	i18n.init({ lng: langType,useCookie: false }, function(t) {
		$(".translation").i18n();
	});
	var ua = navigator.userAgent;
	if( ua.indexOf("Android") >= 0 )
	{
	  var androidversion = parseFloat(ua.slice(ua.indexOf("Android")+8)); 
	  if (androidversion < 3.2)
	  {
		  androidVersionCheck=1;
	  }
	  else{
		androidVersionCheck=0;
	  }
	}
});
