var androidVersionCheck=0;
var ipadVersionCheck=false;
$(document).ready(function(e) {
	if (window.location.href.indexOf("localhost") > -1) {	
	    var langType = 'en-us';
	}
	if(typeof langType === "undefined")
		var langType = 'en';
	i18n.init({ lng: langType,useCookie: false, useLocalStorage: false, }, function(t) {

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
	  else {
		androidVersionCheck=0;
	  }
	}
	if(ua.match(/ipad/i) != null){
		ipadVersionCheck=true;
	}
});
