var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
// Listen to message from child window
eventer(messageEvent,function(e) {
	//console.log("Message Received By Child");
	//console.log(e.origin);
	if(1)
	{
		//console.log("Domain Verified..");
		var returnMsg = generate_params();//timeTaken+"||"+completed+"||"+score+"||"+extraParams;
		parent.postMessage(returnMsg,'*');
	}
	else
	{
		//console.log("Domain Verification Failed..");
		//console.log(e.origin);
	}
},false);
function generate_params()
{
	/*process_string = duration+"||"+completed+"||"+percentage+"||"+extraParams;*/
	process_string = totalTimeTakenInt +"||"+result+"||"+allStageStatus+"||"+totalPromptUsed;
	return process_string;
}