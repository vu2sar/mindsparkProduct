var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
var eventer = window[eventMethod];
var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

// Listen to message from child window
eventer(messageEvent,function(e) {
	//console.log("Message Received By Child");
	if(1)
	{
		if (typeof(afterSubmit) !== 'undefined' && afterSubmit === true)
		{
			_onSubmitClick();
		}
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
	if (typeof(displayAnswerQuery) !== 'undefined') {
		process_string = result+"||"+userResponse+"||"+extraParameters+"||"+displayAnswerQuery;
	}
	else
	{
		process_string = result+"||"+userResponse+"||"+extraParameters;
	}
	return process_string;
}