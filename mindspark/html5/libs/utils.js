function getUrlVars() {
    var vars = {};
    var strurl= unescape(window.location.href);
    var parts = strurl.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

function translateNumber(number,lang){

	return number;
}
