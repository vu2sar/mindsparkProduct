var feedbackResponse = new Array();
var questionStr = new Array();
var responseJSON = {};
function logoff()
{
	setTryingToUnload();
	window.location="logout.php";
}

function trim(str) // Strip leading and trailing white-space
{
	return str.replace(/^\s*|\s*$/g, "");
}

function showFeedbackForm(formName, question_details_jsonObj, qids, feedbackset) 	// shows feed back form based on question object
{
	var html = "";
	var quesArr = qids.split(",");
	$(".feedbackHeading").html(formName);

	html += "<table id='previewTable'>";

	for(i=0; i<question_details_jsonObj.length;i++)
	{
		questionStr.push(quesArr[i].split("~")[0]);  
		html += "<tr class='question'>";
		html += "<td class='Col1'>"+(i+1)+".</td>";
		html += "<td id='ques_"+parseInt(quesArr[i].split("~")[0])+"'>"+stripslashes(question_details_jsonObj[i].question);
		if(parseInt(quesArr[i].split("~")[1]) == 1)
		{
			html += "<sup id='mandatoryAst' style='color:red;'>*</sup>";
		}
		html += "<input type='hidden' name='hidden_"+parseInt(quesArr[i].split("~")[0])+"' id='hidden_"+parseInt(quesArr[i].split("~")[0])+"' value='"+question_details_jsonObj[i].quesType+"'/>";
		html += "</td>";
		html += "</tr>";

		if(question_details_jsonObj[i].helpText != "")
		{
			html += "<tr class='helpText'>";
			html += "<td class='Col1'>Help: </td>";
			html += "<td>"+stripslashes(question_details_jsonObj[i].helpText)+"</td>";
			html += "</tr>";
		}

		html += "<tr class='options'>";
		html += "<td class='Col1'></td>";
		html += "<td class='optionSecondCol' id='option_"+parseInt(quesArr[i].split("~")[0])+"'>";		
		/*for(j=0; j<Object.keys(question_details_jsonObj[i].options).length; j++)
		{
			console.log(question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]);			
		}
		*/
		switch(question_details_jsonObj[i].quesType)
		{
			case "text":
			{
				html += "<textarea placeholder='Enter your answer'></textarea>";
				break;
			}
			case "CHECK":
			{
				for(j=0; j<Object.keys(question_details_jsonObj[i].options).length; j++)
				{
					html += '<div><input type="checkbox" class="options" value="'+question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]+'"/>';
					html += stripslashes(question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]);
					html += '</div>';
				}
				html += "</td>"
				break;
			}
			case "Dropdown":
			{
				html += "<select id='dropdown'>";
				html += "<option>Select</option>";
				for(j=0; j<Object.keys(question_details_jsonObj[i].options).length; j++)
				{
					html += '<option value="'+question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]+'">';
					html += stripslashes(question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]);
					html += '</option>';
				}
				html += "</td>"
				break;
			}
			case "GRIDVIEW":
			{
				html += "<table id='gridTable'>";
				for(var j=0; j<=Object.keys(question_details_jsonObj[i].options.rows).length; j++)
				{
					html += "<tr>";
						for(var k=0; k<=Object.keys(question_details_jsonObj[i].options.cols).length; k++)
						{
							if(j==0 && k==0)
							{
								html += "<td></td>";
							}
							else
							{
								if(j==0 && k!=0)
								{
									html += "<td>"+stripslashes(question_details_jsonObj[i].options.cols[k])+"</td>";
								}
								else if(j!=0 && k==0)
								{
									html += "<td>"+stripslashes(question_details_jsonObj[i].options.rows[j])+"</td>";
								}
								else
								{
									html += "<td class='radio'><input type='radio' name='"+parseInt(quesArr[i].split("~")[0])+"_r"+j+"' id='r"+j+"' value=\""+question_details_jsonObj[i].options.rows[j]+"~"+question_details_jsonObj[i].options.cols[k]+"\"/></td>";
								}
							}
						}
					html += "</tr>";
				}
				html += "</table>";
				html += "</td>";
				break;
			}
			case "MCQ":
			{
				var length;
				if(question_details_jsonObj[i].options.hasOwnProperty('other'))
					length = Object.keys(question_details_jsonObj[i].options).length-1;
				else
					length = Object.keys(question_details_jsonObj[i].options).length;

				for(j=0; j<length; j++)
				{
					html += '<div><input type="radio" name="options'+(i+1)+'" id="options'+(i+1)+'" class="options" value="'+question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]+'"/>';
					html += stripslashes(question_details_jsonObj[i].options[Object.keys(question_details_jsonObj[i].options)[j]]);
					html += '</div>';
				}
				if(parseInt(question_details_jsonObj[i].options.other) == 1)
					html += '<div><input type="radio" name="options'+(i+1)+'" id="options'+(i+1)+'" class="options" value="Other"/><input type="text" class="optionsOther" placeholder="Other"/></div>';
				html += "</td>"
				break;
			}
			case "scale":
			{
				html += "<table id='scaleTable'>"
				html += "<tr id='values'>";
				html += "<td rowspan=2 id='minLabelDiv'>"+stripslashes(question_details_jsonObj[i].options.minLabel)+"</td>";
				for(j=question_details_jsonObj[i].options.minVal; j<=question_details_jsonObj[i].options.maxVal; j++)
				{
					html += "<td class='valueForScale' class='value'>"+j+"</td>";
				}
				html += "<td rowspan=2 id='maxLabelDiv'>"+stripslashes(question_details_jsonObj[i].options.maxLabel)+"</td>";
				html += "</tr>";
				html += "<tr id='radio'>";
				for(j=question_details_jsonObj[i].options.minVal; j<=question_details_jsonObj[i].options.maxVal; j++)
				{
					html += "<td class='radioForScale'><input class='radio' type='radio' id='scale"+(i+1)+"' name='scale"+(i+1)+"' value='"+j+"'/></td>";
				}
				html += "</tr>";
				html += "</table>";
				html += "</td>";
				break;
			}			
			case "blank":
			{
				html += "<input type='text' placeholder='Enter your answer'/>";
				html += "</td>";
				break;
			}
		}

		html += "</tr>";	
	}
	html += "</table>";
	html +='<div id="submitButtonDiv1" align="center">';
    html +='<input type="submit" id="btnSubmit1" class="button1" name ="submit" value="Submit" onClick="return validate()">';
    html +='</div>';
    html +='<div id="skipBtnDiv" align="center">';
    html +='<input type="button" id="btnSkip" class="button1" name ="btnSkip" value="Remind me later" onClick="tryingToUnloadPage=true;window.location.href=\'home.php\';">';
    html +='</div>';
   	html += '</div>';

   	html += '<input type="hidden" name="feedbackset" id="feedbackset" value="'+feedbackset+'" />';
   	html += '<input type="hidden" name="qids" id="qids" value="'+quesArr+'" />';
   	html += '<input type="hidden" name="feedbackResponse" id="feedbackResponse"/>';
   	html += '<input type="hidden" name="feedbacktype" id="feedbacktype"/>';

   	$("#frmFeedback").html(html);
}

function validate()		//validate the form
{
	var msg = "";
	var partMsg = "";
	var absentAnsarr = new Array();
	var quesArr = $("#qids").val().split(",");
	for(ind=0; ind<quesArr.length; ind++)
	{
		if(returnValueOfOption($("#hidden_"+quesArr[ind].split("~")[0]).val() , quesArr[ind].split("~")[0]) && parseInt(quesArr[ind].split("~")[1]) == 1)
		{
			absentAnsarr.push(ind+1);
		}
	}
	if(absentAnsarr.length > 1)
	{
		msg = "Please answer question(s) ";
		for(j=0; j<absentAnsarr.length-1; j++)
		{
			partMsg += absentAnsarr[j]+", ";
		}
		partMsg = partMsg.slice(0,partMsg.length-2);
		partMsg += " and "+absentAnsarr[absentAnsarr.length-1];	
		msg += partMsg;
		msg += " before submitting the form.";	
	}
	else if(absentAnsarr.length == 1)
	{
		msg = "Please answer question(s) ";
		partMsg += absentAnsarr[0];
		msg += partMsg;
		msg += " before submitting the form.";
	}

	if(msg != "")
	{
		alert(msg);
		feedbackResponse = [];
		return false;		
	}
	tryingToUnloadPage = true;
	feedbackResponse = JSON.stringify(responseJSON);
	$("#feedbackResponse").val(feedbackResponse);	
	return true;
}

function returnValueOfOption(quesType , qid)	// checks that whether user has given the answer of mandatory question
{
	switch(quesType)
	{
		case "blank":
		{
			var childrenArr = $("#option_"+qid).children();
			if($(childrenArr[0]).val() == "")
			{
				return true;
			}
			else
			{
				responseJSON[qid] = $(childrenArr[0]).val();
				return false;				
			}
			break;
		}
		case "CHECK":
		{	
			var childrenArr = $("#option_"+qid).children();
			var checkedCounter = 0;
			var checkStr = "";
			for(i=0; i<childrenArr.length; i++)
			{
				if(!$($(childrenArr[i]).find("input")).is(":checked"))
				{
					checkedCounter++;
				}
				else
				{
					checkStr += $($(childrenArr[i]).find("input")).val()+"|";
				}
			}
			checkStr = checkStr.slice(0,checkStr.length-1);
			if(checkedCounter == childrenArr.length)
			{
				return true;
			}
			else
			{
				responseJSON[qid] = checkStr;
				return false;
			}
			break;
		}
		case "Dropdown":
		{
			var childrenArr = $("#option_"+qid).children();
			if($(childrenArr[0]).val() == "Select")
			{
				return true;
			}
			else
			{
				responseJSON[qid] = $(childrenArr[0]).val();
				return false;
			}
			break;
		}
		case "GRIDVIEW":
		{
			var flagArr = new Array();
			var gridCounter = 0;
			var gridRes = "";
			var flagCounter = 0;
			var childrenArr = $("#option_"+qid).children();
			var rowArr = $(childrenArr[0]).find("tr");
			var colArr = new Array();
			for(i=1; i<rowArr.length; i++)
			{
				if($("input[type='radio'][name='"+qid+"_r"+i+"']:checked").length == 0)
					gridCounter++;
				else
					gridRes += $("input[type='radio'][name='"+qid+"_r"+i+"']:checked").val()+"|";
			}
			gridRes = gridRes.slice(0,gridRes.length-1);
			if(gridCounter > 0)
			{
				return true;
			}
			else
			{
				responseJSON[qid] = gridRes;
				return false;				
			}
			break;
		}
		case "MCQ":
		{
			var childrenArr = $("#option_"+qid).children();
			var radioCounter = 0;
			for(i=0; i<childrenArr.length; i++)
			{
				if(!$($(childrenArr[i]).find("input")).is(":checked"))
					radioCounter++;
				else
				{
					if($($(childrenArr[i]).find("input")).hasClass('optionsOther'))
						responseJSON[qid] = $($(childrenArr[i]).find("input[type='text']")).val();
					else
						responseJSON[qid] = $($(childrenArr[i]).find("input")).val();
				}
			}
			if(radioCounter == childrenArr.length)
				return true;
			return false;
			break;
		}
		case "scale":
		{
			var childrenArr = $("#option_"+qid).children();
			var checkedCounter = 0;
			var radioScaleArr = $(childrenArr[0]).find("td.radioForScale");
			for(i=0; i<radioScaleArr.length; i++)
			{
				if(!$($(radioScaleArr[i]).find("input")).is(":checked"))
					checkedCounter++;
				else
				{
					responseJSON[qid] = $($(radioScaleArr[i]).find("input")).val();
				}
			}
			if(checkedCounter == radioScaleArr.length)
				return true;
			return false;
			break;
		}
		case "text":
		{
			var childrenArr = $("#option_"+qid).children();
			if($(childrenArr[0]).val() == "")
			{
				return true;
			}
			else
			{
				responseJSON[qid] = $(childrenArr[0]).val();
				return false;
			}
			break;
		}
	}
}
function stripslashes(str)
{
	return (str + '').replace(/\\(.?)/g, function (s, n1) {
    switch (n1) {
    case '\\':
      return '\\';
    case '0':
      return '\u0000';
    case '':
      return '';
    default:
      return n1;
    }
  });
}