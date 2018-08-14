function QuestionObj(quesParams)
{
	this.qcode = quesParams.qcode || "";
	this.clusterCode = quesParams.clusterCode || "";
	this.quesType = quesParams.quesType || "MCQ-2";
    this.dynamic   = quesParams.dynamicQues || 0;
    this.optionA = quesParams.optionA || "";
    this.optionB = quesParams.optionB || "";
    this.optionC = quesParams.optionC || "";
    this.optionD = quesParams.optionD || "";
	this.enA = quesParams.correctAnswer|| "";
	this.encryptedDropDownAns = quesParams.dropdownAns || "";
	this.encryptedDispAns   = quesParams.dispAns || "";
    this.encryptedDispAnsA = quesParams.dispAnsA || "";
	this.encryptedDispAnsB = quesParams.dispAnsB || "";
	this.encryptedDispAnsC = quesParams.dispAnsC || "";
	this.encryptedDispAnsD = quesParams.dispAnsD || "";
	this.noOfTrialsAllowed = quesParams.noOfTrials || 1;
	this.noOfBlanks = quesParams.noOfBlanks || 0;
	this.hasExpln = quesParams.hasExpln || 0;
	this.hintAvailable = quesParams.hintAvailable || 0;
	this.quesVoiceOver = quesParams.quesVoiceOver || "";
	this.ansVoiceOver = quesParams.ansVoiceOver || "";
    this.eeIcon = quesParams.eeIcon || 0;
	this.commonError = '';
}
QuestionObj.prototype.checkAnswerMCQ = function(userAns)
{
	var result;

	if(userAns == "")
		result = 2;
	else if(userAns == nAth(this.enA))
		result = 1;
	else
		result = 0;
	return result;
};
QuestionObj.prototype.checkIndividualBlank = function(expectedAns, userResponse, fracboxCheck, blankNo)
{
    blankNo = blankNo || 0;
    var result = 0;
    var arrExpectedAns = new Array();
    arrExpectedAns = expectedAns.split("~");		//Split the different way to answer
    arrExpectedAns = unique(arrExpectedAns);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)
    //userResponse = userResponse.replace(/\\/g,"/");
	
	// flag to perform auto rounding or not (perform auto rounding only if correct answer is specified only in fraction, not in decimal)
	var checkAutoRounding = false;
	if(!(expectedAns.indexOf("/") != -1 && expectedAns.indexOf(".") != -1) && !isNaN(userResponse)) {
		checkAutoRounding = true;
	}
	
    //check if blank left unanswered
    if(trim(userResponse)=="")
    {
    	var unanswered = 1;
    	for(var j=0;j<arrExpectedAns.length;j++)
    		if(trim(arrExpectedAns[j])=='')
    			unanswered = 0;
    }
    if(unanswered)
    	result = 2;
	else
    {
		var flagCommonError = 0;
        if (this.commonError == '')
            flagCommonError = 1;
        for(var j=0;j<arrExpectedAns.length;j++)
        {
            if(userResponse=='')
            {
        		if(trim(arrExpectedAns[j])=='')
        		{
        		    result = 1;
               	    break;
        		}
            }
            else
            {
                b1   = trim(userResponse);
                ans_blank = trim(arrExpectedAns[j]);
                /*if(fracboxCheck===true)
                {*/
					//var indexOFSlash	=	ans_blank.toString().indexOf("/");
					var indexOFbracket	=	arrExpectedAns[j].toString().indexOf("}/{");
					//if(indexOFSlash>-1 && indexOFbracket==-1)
					if(indexOFbracket>-1)
					{
						//ans_blank	=	"{"+ans_blank+"}";
						//ans_blank	=	ans_blank.toString().replace(/\//gi, "}/{");
						arrExpectedAns[j]	=	arrExpectedAns[j].toString().replace(/\}\/\{/gi, "/");
						arrExpectedAns[j]	=	arrExpectedAns[j].toString().replace(/\}/gi, "");
						arrExpectedAns[j]	=	arrExpectedAns[j].toString().replace(/\{/gi, " ");
					}
					ans_blank	=	ans_blank.replace(/\s+/g, '');
					ans_blank	=	ans_blank.replace(/&nbsp;/g,'');
                /*}*/
                if(b1==ans_blank) {
                    result = 1;
                    break;
                }
                else
                {
                    //if(!(fracboxCheck===true))
                    {
                        try 
                        {
							try 
                            {
								if(expComp.compEqs(arrExpectedAns[j], userResponse, 1, checkAutoRounding))
								{
									result = 1;
									break;
								}
							} 
                            catch(err) 
                            {
								// do nothing, continue execution
							}
							
							var cAnswer = nParser.parse(arrExpectedAns[j]);
							var uAnswer = nParser.parse(userResponse);
							
							if(uAnswer == cAnswer)
							{
								result = 1;
								break;
							}
							else if(checkAutoRounding && ((document.getElementById("b"+blankNo)) || document.getElementById("tmpMode").value == "NCERT"))
							{
								// auto rounding should happen when textbox exists (to avoid frac box auto rounding)
								if(checkRoundValue(userResponse, arrExpectedAns[j])) 
                                {
									result = 1;
									break;
								}
							}
						}
                        catch (err) 
                        {
							//console.log(String(err));
						}
                    }
					if (!(fracboxCheck === true)) {
						try {
							var cAnswer = nParser.parse(arrExpectedAns[j]);
						}
						catch (err) {
							var cAnswer = trim(arrExpectedAns[j]);
                        }
                        try {
                            if (typeof this.commonError.userAlert == "undefined" || this.commonError.userAlert=="") { 
                                this.commonError = new commonErrors(cAnswer, b1);
                                if (this.commonError.parse() == 1) {
                                    result = 1;
                                    break;
                                }
                            }
                        } catch (err) {

                        }
                    }
                    if(blankNo==1 && this.noOfBlanks>1)    //temporary - to handle case where in case of multiple blanks, answer typed twice in first blank which is not replicated
                    {
                        var ansToCompare = ans_blank.toString() + ans_blank.toString();
                        if(ansToCompare==b1) {
                           result = 1;
                           break;
                        }
                        else
                           result = 0;
                    }
                    else
                       result = 0;
                }
            }
        }
    }
    return result;
}
QuestionObj.prototype.checkAnswerBlank = function(arrUserAns, arrFracboxCheck, otherResult, subQuesNo)
{
    var result="";
    var arrCorrectAns  = nAth(this.enA).split("|");
    var blankResultArray = new Array();
    var cntBlanksRight=0;
	
    for(var i=0;i<this.noOfBlanks;i++)
    {
    	var blankResult = 0;
    	blankResult = this.checkIndividualBlank(arrCorrectAns[i],arrUserAns[i],arrFracboxCheck[i], (i+1));
    	if(blankResult==2)
    	{
    		result = 2;
    		if(document.getElementById("b"+(i+1)))
    		 	document.getElementById("b"+(i+1)).focus();
    		break;
    	}
    	else
    	{
    		blankResultArray[i] = blankResult;
    		if(blankResult==1)
               cntBlanksRight++;
    	}
    }
    if(result!=2)
    {
        if(cntBlanksRight==this.noOfBlanks && otherResult === true)//All Blank must be right
            result = 1;
        else
            result = 0;
		
         for(var i=0; i<blankResultArray.length; i++)
         {
            if(subQuesNo !== undefined)
            {
                if($($('.singleQuestion')[parseInt(subQuesNo)]).find('#b'+(i+1)))
                {
                     if(blankResultArray[i]==0)
                        $($('.singleQuestion')[parseInt(subQuesNo)]).find('#b'+(i+1)).addClass("incorrectblank");
                    else
                        $($('.singleQuestion')[parseInt(subQuesNo)]).find('#b'+(i+1)).addClass("correctblank");
                }
            }
            else
            {
                if(document.getElementById("b"+(i+1)))
                {
                    if(blankResultArray[i]==0)
                        document.getElementById("b"+(i+1)).className = "incorrectblank";
                    else
                        document.getElementById("b"+(i+1)).className = "correctblank";
                }
            }
         }
    }
    return result;
};
QuestionObj.prototype.checkAnswerDropDown = function(dropDownUserAns, arrBlankUserAns, arrFracboxCheck, subQuesNo)
{
    var expectedDropDownAns = nAth(this.encryptedDropDownAns);
    var result;
    var ddlUserAns_temp = dropDownUserAns.replace(/\|/g,"").replace(/0/g,"");
    var blankUserAns = arrBlankUserAns.join("|");
    var blankUserAns_temp = blankUserAns.replace(/\|/g,"");
	if(ddlUserAns_temp=="")
		result = 2;
   	else if(dropDownUserAns==expectedDropDownAns)
   	{
        if(this.noOfBlanks==0)
            result = 1;
	    else
        {
            if(subQuesNo !== undefined)
                result = this.checkAnswerBlank(arrBlankUserAns, arrFracboxCheck, true, subQuesNo);
            else
                result = this.checkAnswerBlank(arrBlankUserAns, arrFracboxCheck, true);
        }
   	}
   	else
    {
        if(subQuesNo !== undefined)
    	    result = this.checkAnswerBlank(arrBlankUserAns, arrFracboxCheck, false, subQuesNo);
        else
            result = this.checkAnswerBlank(arrBlankUserAns, arrFracboxCheck, false);
    }
		
	if(result!=2)
    {
        if(subQuesNo !== undefined)
    		this.changeBorderColor(dropDownUserAns, expectedDropDownAns, subQuesNo);
        else
            this.changeBorderColor(dropDownUserAns, expectedDropDownAns);
    }   

	return result;
};

QuestionObj.prototype.changeBorderColor = function(dropDownUserAns, expectedDropDownAns, subQuesNo)
{
	arrDropDownUserAns	= dropDownUserAns.split("|");
	arrExpectedDropDownAns = expectedDropDownAns.split("|");
	for(var i=0; i<arrDropDownUserAns.length; i++)
	{
        if(subQuesNo !== undefined)
        {
            if($($('.singleQuestion')[parseInt(subQuesNo)]).find('#lstOpt'+i))
            {
                 if(arrDropDownUserAns[i]==arrExpectedDropDownAns[i])
                    $($('.singleQuestion')[parseInt(subQuesNo)]).find('#lstOpt'+i).addClass("correctblank");
                else
                    $($('.singleQuestion')[parseInt(subQuesNo)]).find('#lstOpt'+i).addClass("incorrectblank");
            }
        }
        else
        {
            if(document.getElementById("lstOpt"+i))
            {
                if(arrDropDownUserAns[i]==arrExpectedDropDownAns[i])
                    document.getElementById("lstOpt"+i).className = "correctblank";
                else
                    document.getElementById("lstOpt"+i).className = "incorrectblank";
            }
        }
	}
};

QuestionObj.prototype.checkAnswerOpenEnded = function(userAnswer)
{	
    return((trim(userAnswer)=='')?2:3);
};

// auto rounding related function
function checkRoundValue(uAns, cAns) {
	// check if correct answer is in fraction and user answer is in decimal
	if(cAns.indexOf("/") != -1 && uAns.indexOf(".") != -1) {
		// convert values in numeric value
		uAns = nParser.parse(uAns);
		cAns = nParser.parse(cAns);
		
		// find decimal points in user answer
		var temp_ans = new String(uAns);
		var arr_data = temp_ans.split(".");
		var dec_len = (arr_data.length > 1) ? arr_data[1].length : 0;
		
		// if decimal points in user answer are more than 1 then round the correct answer value and check it
		if(dec_len > 1) {
			cAns = parseFloat(parseFloat(cAns).toFixed(dec_len));
			if(cAns == uAns)
				return true;
			else
				return false;
		} else {
			return false;
		}
	}
	return false;
}