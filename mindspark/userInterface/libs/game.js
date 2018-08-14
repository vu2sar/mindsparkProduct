
function saveGameResult(gameID, level, score, time)
{

	var params="";
	params += "mode=saveGameDetails";
	params += "&gameID=" + gameID;
	params += "&level=" + level;
	params += "&totalScore=" + score;
	params += "&timeTaken=" + time;
	var request = new Ajax.Request('controller.php',
		{
			method:'post',
			parameters: params,
			onSuccess: function(transport)
			{
				resp = transport.responseText;
				var nextLevelScore = 120;
				if(gameID==13)
					nextLevelScore = 200;
				document.getElementById('mode').value = resp;
				if(trim(resp)=="-10" || gameID==11 || gameID==12 || gameID==17)
					redirect();
				else if(score>=nextLevelScore)
				{
					if(level!=3)
					{
						level +=1;
						document.getElementById('level').value = level;
						document.getElementById('frmGame').submit();
					}
					else
					{
						/*document.getElementById('level').value = level;
						document.getElementById('frmGame').submit();*/
						redirect();
					}

				}
				else
				{
					document.getElementById('level').value = level;
					document.getElementById('frmGame').submit();
					//redirect();
				}
				// code for IE
				//document.getElementById('btnContinue').style.display = "inline";


			},
			onFailure: function()
			{

				alert('Something went wrong while saving...');
			}
		}
		);
}

function trim(query)
{
	//return query.replace(/^\s+|\s+$/g,"");
	var s = query.replace(/\s+/g,"");
	return s.toUpperCase();
}

function redirect()
{
	var mode = document.getElementById('qcode').value;
	if(mode!='-2' && mode!='-3' && mode!='-1' && mode!='-8')
	{
		mode = document.getElementById('mode').value;
		mode = mode.replace(/^\s*|\s*$/g, "");
	}
	if(mode=='-2' || mode=='-3' || mode=='-5' || mode=='-6')
	{
		document.getElementById('mode').value = mode;
		document.getElementById('frmGame').action = "endSessionReport.php";
	}
	else if(mode=='-1')
	{
		document.getElementById('mode').value = mode;
		document.getElementById('frmGame').action = "dashboard.php";
	}
	else if(mode=='-8')
	{
		document.getElementById('mode').value = mode;
		document.getElementById('frmGame').action = "classLevelCompletion.php";
	}
	else
	{
		document.getElementById('frmGame').action = "question.php";
	}
	document.getElementById('frmGame').submit();
}

function endSession()
{
	msg = "Are you sure you want to end the current session?";
    var ans = confirm(msg);
    if(ans)
    {
    	document.getElementById('mode').value = 1;
    	var params= "mode=endsession";
    	params += "&code="+1;
    	try {
    		var request = new Ajax.Request('controller.php',
    		{
    			method:'post',
    			parameters: params,
    			onSuccess: function(transport)
    			{

    				resp = transport.responseText|| "no response text";
    				document.getElementById('frmGame').action='endSessionReport.php';
        			document.getElementById('frmGame').submit();
    			},
    			onFailure: function()
    			{
    				alert('Something went wrong...');
    			}
    		}
    		);
    	}
    	catch(err) {}
    }
}

function blinkIt() {
 if (!document.all) return;
 else {

      s=document.getElementById('pnlMsg');
      s.style.visibility=(s.style.visibility=='visible')?'hidden':'visible';
   }

}
