
window.onload = function () {
	if (self.initButtons)
		initButtons();
	document.designMode = 'On'; 		// Moz, Op, Saf
	document.body.contentEditable = true;	// Op, IE, Saf
/*	document.execCommand("styleWithCSS",false,false); */ // makes Moz use tags instead of style
}

function inBetween(command,bool,value) {
/*	var enabled = document.queryCommandSupported(command);
	if (!enabled) {
		alert('Command not available');
		return false;
	}
*/
	var returnValue = document.execCommand(command,bool,value);
	if (returnValue) return returnValue;
}