var totalQues = 0;
var totalCorrect = 0;
$(document).ready(function(e) {
    $(".blankSpace").css("min-width",$("#q1").width()+40+"px");
	startGameTimer();
	setInterval(function(){
		$.post("saveDiagnosticTest.php","mode=pingServer",function(dataResponse) { 
			
		});	
	},60000);	
});
function startGameTimer()
{
	timeTaken++;
	window.setTimeout(startGameTimer,1000);
}
function submitAnswers()
{
	var jsonPostArr = new Array();
	var result = evalGroupAnswers();
	var diagnosticTestID = $("#diagnosticTestID").val();
	if(result != false)
	{
		$("#submitBtn").hide();
		$("#submitBtn").attr("disabled","disabled");
		var indexArr = 0;
		$("#result").val(result);
		$(".singleQuestion").each(function(index, element) {
			var groupNo = $(this).attr("data-group-no");
			var qcode = $(this).attr("data-qcode");
			var displayAnswer = $(this).attr("data-display-ans");
			var result = $("#result").val().split("##")[indexArr];
			var userAnswer = $("#userResponse").val().split("##")[indexArr];
			var applyClass = (result == 1)?"correct":"wrong";
			if(result==1)
				totalCorrect++;
			totalQues++;
				
			$(this).contents().find("td:first div").addClass(applyClass);
			if($.trim(displayAnswer) != "")
				$(this).append("Answer: "+decrypt(displayAnswer));
			jsonPostArr.push({"groupNo":groupNo,"qcode":qcode,"result":result,"userAnswer":userAnswer});
			indexArr++;
		});
		$('input:radio').attr("disabled",true);
		jsMath.Process();
		$.post("saveDiagnosticTest.php",{"debug":debug,"timeTaken":timeTaken,"TestID":diagnosticTestID,"Data":JSON.stringify(jsonPostArr)},function(dataResponse) { 
			if(debug)
				alert(dataResponse)
			else
			{
				if(dataResponse!="Saved")
				{
					var nextStep	=	dataResponse.split("$");
					if(nextStep[1]=="remedial")
					{
						$("#remedialMode").val(1);
						$("#qcode").val(nextStep[0]);
					}
					else if(nextStep[1]=="timedTest")
					{
						$("#timedtestMode").val(1);
						$("#timedTest").val(nextStep[0]);
					}
					else if(nextStep[1]=="activity")
					{
						$("#activityMode").val(1);
						$("#gameID").val(nextStep[0]);
						$("#mode").val("comprehensive");
					}
					else if(nextStep[1]=="cluster")
					{
						$("#clusterMode").val(1);
					}
					if(dataResponse=="nextcluster")
					{
						$("#mode").val("completeComprhensive");
					}
					else
						alert("Thanks for taking the test! You have answered "+totalCorrect+" out of "+totalQues+" questions correctly.\nLet us strengthen some of the concepts and get back.");
				}
				else
				{
					alert("Congratulations! You have mastered concepts tested here well!");
				}
				$("#continueBtn").show();
			}
		})
	}
}
function evalGroupAnswers()
{
	var questionCount = 0;
	var requireAns = new Array();
	var returnStr = "";
	var userAns = new Array();
	var userDdlAns = new Array();
	var userDdlAnsByVal = new Array();
	var userResponse = new Array();
    var eeResponse = new Array();
	var correctAnswerArr = new Array();
	var ques_typeArr = new Array();
	correctAnswerArr = $("#correctAnswer").val().split("##");
	correctDdlAnswerArr = $("#dropdownAns").val().split("##");
	ques_typeArr = $("#quesType").val().split("##");
	$(".singleQuestion").each(function(){
		questionCount++;
		if(questionCount > ques_typeArr.length)
			return;
		var line = "";
		var ddlLine = "";
		var ddlLineByVal = "";
        var eeLine = "";
		$(this).contents().find("select").each(function(){
		    ddlLine += $(this).attr("selectedIndex") + "|";
		    ddlLineByVal += $(this).val() + "|";
		})
		$(this).contents().find("input,iframe").each(function(){
			if($(this).hasClass("openEnded"))
			{
                eeLine += $(this)[0].contentWindow.storeAnswer('') + "|";
				eeLine += $(this)[0].contentWindow.tools.save() + "|";
            }
            else if($(this).attr("type") == "text")
            {			
				line += $(this).val() + "|";
			}
			else if($(this).attr("type") == "hidden")
			{
				var txtbxID	=	$(this).attr("id");
				if (txtbxID.split("_")[0] == "fracV" && $('#'+txtbxID).hasClass('fracboxvalue')) 
				{
					line += $(this).val() + "|";
				}
				else if(txtbxID.split("_")[0] == "fracS")
				{
					b	=	$(this).val();
					b	=	b.replace(/{/gi, "");
					line += b.replace(/}/gi, "") + "|";
				}
				if(txtbxID.split("_")[1])
					jQuery('#fracB_'+txtbxID.split("_")[1]).removeAttr('contenteditable');
			}
			else if($(this).attr("type") == "radio")
			{
				if($(this).is(":checked"))
				{
					line += $(this).val() + "|";
				}
			}
		})
		line = line.substring(0, line.length - 1);
        eeLine = eeLine.substring(0, eeLine.length - 1);
		ddlLine = ddlLine.substring(0, ddlLine.length - 1);
		ddlLineByVal = ddlLineByVal.substring(0, ddlLineByVal.length - 1);
		userAns.push(line);
        eeResponse.push(removeInvalidChars(eeLine));
		userDdlAns.push(ddlLine);        
		userDdlAnsByVal.push(ddlLineByVal);
		return;
	})
	for(var k=0; k<questionCount; k++)
	{
		var ques_type = ques_typeArr[k];
		var correctanswer = (correctAnswerArr[k]!="")?decrypt(correctAnswerArr[k]):"";
		var correctddlanswer = (correctDdlAnswerArr[k]!="")?decrypt(correctDdlAnswerArr[k]):"";
		var blankUserAns = userAns[k];
		var ddlUserAns = userDdlAns[k];
		var ddlUserAnsByVal = userDdlAnsByVal[k];

		if(ques_type == 'D')
		{
		    var result = checkDropDownAns(ddlUserAnsByVal,ddlUserAns,blankUserAns,correctddlanswer,correctanswer);
			if(userAns[k] != "")
				var fullUserAnswer = userDdlAnsByVal[k] + '|'+ userAns[k];
			else
				var fullUserAnswer = userDdlAnsByVal[k];
		}
        else if(ques_type == 'Open Ended')
		{
			var result = 3;
		}
		else 
		{
		    var userRealResponse = "";
		    var result = evaluateResponse(blankUserAns,correctanswer,ques_type,userRealResponse);
			var fullUserAnswer = userAns[k];
		}
	    if(result == 2)
	        requireAns.push(k+1);
	    returnStr += result+"##";
		userResponse.push(fullUserAnswer);
	}
	if(requireAns == "")
	{
		var tempUserResponse = userResponse.join("##");
		$("#userResponse").val(tempUserResponse);
        $("#eeResponse").val(eeResponse.join("##"));
		return(returnStr);
	}
	else
	{
		var remainQues = requireAns.join(",");
		alert("Please complete the answer(s) for question(s) "+remainQues+".");
		return false;
	}
}

function removeInvalidChars(tmp) 
{		
	tmp = escape(tmp);
	tmp = tmp.replace( /%D7/g, "&times;" );
	tmp = tmp.replace( /%F7/g, "&divide;" );
	tmp = tmp.replace( /%AB/g, "&laquo;" );
	tmp = tmp.replace( /%B0/g, "&deg;" );
	tmp = tmp.replace( /%BB/g, "&raquo;" );
	tmp = tmp.replace( /%u2220/g, "&ang;" );
	tmp = tmp.replace( /%u03B1/g, "&alpha;" );
	tmp = tmp.replace( /%u03B2/g, "&beta;" );
	tmp = tmp.replace( /%u03B3/g, "&gamma;" );
	tmp = tmp.replace( /%u0394/g, "&Delta;" );
	tmp = tmp.replace( /%u03BB/g, "&lambda;" );
	tmp = tmp.replace( /%u03B8/g, "&theta;" );
	tmp = tmp.replace( /%u03C0/g, "&pi;" );
	tmp = tmp.replace( /%u2211/g, "&sum;" );
	tmp = tmp.replace( /%u221A/g, "&#8730;" );
	tmp = tmp.replace( /%u221B/g, "&#8731;" );
	tmp = tmp.replace( /%BB/g, "&raquo;" );
	tmp = tmp.replace( /%AB/g, "&laquo;" );
	tmp = tmp.replace( /%F7/g, "&divide;" );
	tmp = tmp.replace( /%D7/g, "&times;" );
	tmp = tmp.replace( /%u2264/g, "&le;" );
	tmp = tmp.replace( /%u2265/g, "&ge;" );
	tmp = tmp.replace( /%u22A5/g, "&perp;" );
	tmp = tmp.replace( /%u03B4/g, "&delta;" );
	tmp = tmp.replace( /%u03C3/g, "&sigma;" );
	tmp = tmp.replace( /%u2282/g, "&sub;" );
	tmp = tmp.replace( /%u2284/g, "&#8836;" );
	tmp = tmp.replace( /%u2245/g, "&cong;" );
	tmp = tmp.replace( /%u2260/g, "&ne;" );
	tmp = tmp.replace( /%u2208/g, "&#8712;" );
	tmp = tmp.replace( /%u2209/g, "&#8713;" );
	tmp = tmp.replace( /%u222A/g, "&#8746;" );
	tmp = tmp.replace( /%u2229/g, "&#8745;" );
	tmp = tmp.replace( /%u223C/g, "&#8764;" );
    tmp = tmp.replace( /%B1/g, "&plusmn;" );
	tmp = tmp.replace( /%u2234/g, "&there4;" );
	tmp = unescape(tmp);
	return tmp;
}
function checkDropDownAns(ddlUserAnsByVal,ddlUserAns,blankUserAns,ddlAns,blankAns)
{	
    var ddlUserAns_temp = ddlUserAns.replace(/\|/g,"").replace(/0/g,"");
    var blankUserAns_temp = blankUserAns.replace(/\|/g,"");
	if(blankUserAns != "")
		document.getElementById('userResponse').value = ddlUserAnsByVal+"|"+blankUserAns;
	else
		document.getElementById('userResponse').value = ddlUserAnsByVal;
		if(ddlUserAns_temp.length==0 && trim(blankAns)=="")
			return 2;
		else if(blankUserAns_temp.length==0 && ddlUserAns_temp.length==0)
			return 2;
   		else if(ddlUserAns==ddlAns)
   		{			
			if(trim(blankAns)=="")
    		    return 1;
    	    else
    		{
					ansStr = blankAns.split("|");
					userAnsStr = blankUserAns.split("|");
					var flag = 0;
					for(var j=0; j<ansStr.length; j++)
					{
						ans = ansStr[j];
						waysToAns = ans.split("~");
			            waysToAns = unique(waysToAns);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)
			            var b = userAnsStr[j]
						b = b.replace(/\\/g,"/");
						for(var k=0;k<waysToAns.length;k++)
			            {
				        	if(b=='' && waysToAns[k]=='')
				            {
				            	flag++;
				                break;
				            }
				            else
				            {
				 				b1   = trim(b);
				 				ans_blank = trim(waysToAns[k]);
				 				if(ans_blank!="" && b1!="" && !isNaN(b1) && !isNaN(ans_blank))
				 				{
				 					b1 = parseFloat(b1);
				 					ans_blank = parseFloat(ans_blank);
				 				}
				 				else if(!isNaN(ans_blank) && ans_blank!="" ) //Remove comma only when expected answer is numeric && ans_blank!=""
			                    {
			                         b1AfterCommaRemoval = b1.replace(/,/g,"");

			                         if(!isNaN(b1AfterCommaRemoval) && b1AfterCommaRemoval!="")
			                            b1= b1AfterCommaRemoval;
			                         if(!isNaN(b1))
			                            b1 = parseFloat(b1);
			                         ans_blank = parseFloat(ans_blank);

			                    }
				 				if(b1==ans_blank)
				 				{
				 					flag++;
				 					break;
				 				}
			                }
			       	 	}
					}
					if(flag == ansStr.length)
						return 1;
					else
						return 0;

    		}
    	}
    	else
    		return 0;
}

function evaluateResponse(userAnswer,correctanswer,ques_type,userRealResponse)
{
	if(ques_type == "Open Ended")
	{
		return((trim(userAnswer)=='')?2:3);
	}
    var result;
	var b1;
    var part_ans = new Array();
    var user_ans = new Array();
        //Split the Answers - For Blanks
    part_ans = correctanswer.split("|");
    user_ans = userAnswer.split("|");
	if(userRealResponse=="")
	    document.getElementById('userResponse').value = userAnswer;
	else
		document.getElementById('userResponse').value = userRealResponse;
    var flag=0;
    var count = part_ans.length;        
    var checkblanks=0;
	if(ques_type=='MCQ-4' || ques_type=='MCQ-3' || ques_type=='MCQ-2')
    {
		b = user_ans[0];
		ans = part_ans[0];
		if(b=="")
			return 2;
		else if(b==ans)
		{
			$('#result').val("1");	
			return 1;
		}			
		else	
		{
			$('#result').val("0");	
			return 0;
		}        
    }        
	else 
	{
		var blankResultArray = new Array();			
	    for(var i=0;i<part_ans.length;i++)
	    {
			var blankResult = 0;
	        var ans = new Array();
	        //Split the different way to answer
	        ans = part_ans[i].split("~");
	        ans = unique(ans);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)	
	        var b = '';
	        b = eval("user_ans[i]");
	        b = b.replace(/\\/g,"/");
	        //check if blank left unanswered
			if(b=="")
			{
				var unanswered = 1;
				for(var j=0;j<ans.length;j++)	
					if(ans[j]=='')
						unanswered = 0;
				if(unanswered)
				{
					if(document.getElementById("b"+(i+1)))
					 		document.getElementById("b"+(i+1)).focus();
					return 2;
				}
			}
			
	        for(var j=0;j<ans.length;j++)
	        {
	            if(b=='')
	            {
					if(ans[j]=='')
					{						
						flag++;
						if(document.getElementById("b"+(i+1)))
					 		document.getElementById("b"+(i+1)).className = "correctblank";
	                 	break;	
					}					
	            }
	            else
	            {
	                 b1   = trim(b);
	                 ans_blank = trim(ans[j]);
	                 if(b1!="" && ans_blank!="" && !isNaN(b) && !isNaN(ans_blank))
	                 {
	                    b1 = parseFloat(b1);
	                    ans_blank = parseFloat(ans_blank);
	                 }
	                 else if(!isNaN(ans_blank) && ans_blank!="" ) //Remove comma only when expected answer is numeric && ans_blank!=""
	                 {
	                     b1AfterCommaRemoval = b1.replace(/,/g,"");	
	                     if(!isNaN(b1AfterCommaRemoval) && b1AfterCommaRemoval!="")
	                        b1= b1AfterCommaRemoval;
	                     if(!isNaN(b1))
	                        b1 = parseFloat(b1);
	                     ans_blank = parseFloat(ans_blank);
	                 }
	                 if(b1==ans_blank) {					     
					 	 blankResult = 1;
	                     flag++;
	                     break;
	                 }
					 else 			
					 {
					 	blankResult = 0;
					 }
					     
	            }                 
	        }
			blankResultArray[i] = blankResult;			
	    }	    
		for(var i=0; i<blankResultArray.length; i++)
		{
			if(document.getElementById("b"+(i+1)))
			{
				if(blankResultArray[i]==0)
				    document.getElementById("b"+(i+1)).className = "incorrectblank";	
				else
					document.getElementById("b"+(i+1)).className = "correctblank";					
			}
		}
	    //All Blank must be right
	    if(count==flag)
	    {
	        $('#result').val("1");
	        return 1;
	    }
	    else
	    {	        
	        $('#result').val("0");
	        return 0;
	    }
	}
    return 1;
}
function decrypt(str) {
	var strtodecrypt = str.split("-");
	var msglength = strtodecrypt.length;
	decrypted_message = "";
	for (var position = 0; position < msglength; position++)        {
		ascii_num_byte_to_decrypt = strtodecrypt[position];
		ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt / 2;
		ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt - 5;
		decrypted_byte = String.fromCharCode(ascii_num_byte_to_decrypt);
		decrypted_message += decrypted_byte;
	}
	return decrypted_message;
}

function redirect()
{
	var mode = document.getElementById('nextQcode').value;
	
	if(mode!='-2' && mode!='-3' && mode!='-1' && mode!='-8')
	{
		mode = document.getElementById('mode').value;
		mode = mode.replace(/^\s*|\s*$/g, "");
	}
	
	if($("#mode").val()=='completeComprhensive')
	{
		$("#diagnosticTest").attr("action","dashboard.php");
	}
	else if(mode=='-2' || mode=='-3' || mode=='-5' || mode=='-6')
	{
		document.getElementById('mode').value = mode;
		document.getElementById('diagnosticTest').action = "endSessionReport.php";
	}
	else if(mode=='-8')
	{
	    document.getElementById('diagnosticTest').action = "classLevelCompletion.php";
	}
	else if(mode=='-1')
	{
		document.getElementById('mode').value = mode;
		document.getElementById('diagnosticTest').action = "dashboard.php";
	}
	else if(document.getElementById('remedialMode').value==1)
	{
		document.getElementById('diagnosticTest').action = "remedialItem.php";
	}
	else if(document.getElementById('timedtestMode').value==1)
	{
		document.getElementById('diagnosticTest').action = "timedTest.php";
	}
	else if(document.getElementById('activityMode').value==1)
	{
		document.getElementById('diagnosticTest').action = "enrichmentModule.php";
	}
	else if(document.getElementById('clusterMode').value==1)
	{
		document.getElementById('mode').value = "comprehensiveAfterActivity";
		document.getElementById('diagnosticTest').action = "controller.php";
	}
	else
	{
		document.getElementById('diagnosticTest').action = "question.php";
	}
	document.getElementById('diagnosticTest').submit();
}

function endSession()
{
	$("#contentQues").hide();
	if(document.getElementById('submitBtn').style.display =="none") //implies completed the timed test
		var msg = "Are you sure you want to end this session?";	
	else
		var msg = "You have not completed this test. If you end your session now, you will get this test again the next time you attempt this topic. Do you still want to end this session?";
	if(confirm(msg))
	{
		processEndSessionAns(1);
	}
}

function processEndSessionAns(ans)
{
	if(ans)
	{
		code = 1;
		document.getElementById('mode').value = code;
	    var params= "mode=endsession";
	    params += "&code="+code;
	    try {
	    	var request = new Ajax.Request('controller.php',
	    	{
	    		method:'post',
	    		parameters: params,
	    		onSuccess: function(transport)
	    		{
	    			resp = transport.responseText|| "no response text";
	    		},
	    		onFailure: function()
	    		{
	    			//alert('Something went wrong...');
	    		}
	    	}
	    	);
	    }
	    catch(err) {}
		document.getElementById('diagnosticTest').action = "endSessionReport.php";
        document.getElementById('diagnosticTest').submit();
	}
	else
	{
		
	}
}
function unique(a) {
	var r = new Array();
	o:for(var i = 0, n = a.length; i < n; i++) {
		for(var x = 0, y = r.length; x < y; x++)
		{
			if (trim(r[x]) == trim(a[i]))
			continue o;
		}
		r[r.length] = a[i];
	}
	return r;
}
function trim(query)
{
	var s = query.replace(/\s+/g,"");
	return s.toUpperCase();
}