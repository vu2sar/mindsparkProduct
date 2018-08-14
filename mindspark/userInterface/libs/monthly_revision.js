var secs,secsTaken=0;
var timerID = null;
var timerRunning = false;
var delay = 1000;
var slowLoadTimer;
var logoffTimer;
var msgArray = new Array('Admirable','Amazing','Awesome','Bravo','Brilliant','Congratulations','Cool','Dazzling','Dynamite','Excellent','Extraordinary','Fabulous','Fantastic','First-class','First-rate work','Good job','Good one','Good work','Grand','Great work','Great job','Impressive','Incredible ','Keep going','Marvelous','Nice work','Nicely done','Outstanding','Phenomenal','Remarkable','Smashing','Splendid','Superb','Bullseye','Terrific','Very good','Way to go','Well done','Wonderful','Not Bad');

var allowed=1; //For concecutive enter stop the enter key press till scroll part complete 1 - allow , 0 - cancel (Submit & Next Question)

var plstart = new Date();

// Added to get responses for html interactives

try {
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
    var frame = document.getElementById("quesInteractive");
    // Listen to message from child window
    eventer(messageEvent, function (e) {
        if (e.origin == "http://mindspark-ei.s3.amazonaws.com" || e.origin == "http://d2tl1spkm4qpax.cloudfront.net" || e.origin == "mindspark-ei.s3.amazonaws.com" || e.origin == "d2tl1spkm4qpax.cloudfront.net" || $("#offlineStatus").val()=="3" || $("#offlineStatus").val()=="4" || $("#offlineStatus").val()=="7") {			
            var response1 = "";
            response1 = e.data;
            if (response1.indexOf("||") != -1) {
                response1Array = response1.split("||");
                result = parseInt(response1Array[0]);
                $('#userResponse').val(response1Array[1]);
				document.getElementById('result').value = result;
                if (result > 2) {
                    $.post("errorLog.php", "params=" + $("#quesform").find("input").serialize() + "&type=3", function (data) {

                    });
                }
				calcAnswer(result);
            }
        }
    }, false);
}
catch (ex) {
    alert('error in getting the response from interactive');
}

function setFocus(obj)
{
    //if(!obj.disabled)
    obj.focus();
}
function init()
{
    document.getElementById('btnSubmit').disabled=true;
    checkLoadingComplete();
    try
    {
	    if(document.getElementById('quesVoiceOver').value!='')
	        niftyplayer('niftyPlayer1').load(document.getElementById('quesVoiceOver').value);
	    if(document.getElementById('ansVoiceOver').value!='')
	        niftyplayer('niftyPlayer1').load(document.getElementById('ansVoiceOver').value);
    }
    catch(err){}
    document.onselectstart = function () { return false; } // ie
    logoffTimer = setTimeout('logoff()', 600000);        //log off if idle for 10 mins
}
function handleClose()
{
	window.status='';
	plstart = new Date();
    stopTheTimer();
    updateTimeTakenForExpln(secs);
    try{
          niftyplayer('niftyPlayer1').stop();
    }catch(err){};
    if(document.getElementById('btnNextQues').value != "Ok")
    {
    	slowLoadTimer = setTimeout("showSlowLoadingMsg()",30000);
    	fetchNextQues();
    }
    else
    {
        document.getElementById('nextQuesLoaded').value = "0";
        document.getElementById('showAnswer').value = 1;
    }
}
function fetchNextQues()
{
    var params = Form.serialize("quesform");
    Form.disable($('quesform'));
	document.getElementById('pnlQuestion').style.display="none";
	document.getElementById('dlgAnswer').style.display="none";
	document.getElementById('displayanswer').innerHTML = '';
	document.getElementById('pnlButton').style.display="none";
	if(document.getElementById('pnlCQ'))
		document.getElementById('pnlCQ').style.display="none";
	document.getElementById('pnlLoading').style.display="inline";

	isNextQuesLoaded();
}
function isNextQuesLoaded()
{
    if($('#nextQuesLoaded').val()=="0")
        self.setTimeout("isNextQuesLoaded()", 500);
    else
    {
        if($('#nextQuesLoaded').val()=="-1")
        {
            redirect=0;
        	document.quesform.action='logout.php';
            document.quesform.submit();
        }
        else
        {
            Form.enable("quesform");
            document.getElementById('btnSubmit').disabled=true;
            var code = $("#qcode").val();
            code = code.replace(/^\s*|\s*$/g, "");
            document.getElementById('refresh').value = 0;
            if(code=="-2") {
                finalSubmit(code);                //Pass the code End of topic - failure
                return false;
            }
            else if(code=="-3") {
                finalSubmit(code);                //Pass the code End of topic - success
                return false;
            }
            else if(code=="-4") {
                alert("Error in finding minimum SDL");
                finalSubmit(code);
                //return false;
            }
            else if(code=="-5" || code=="-6" || code=="-7")        {
                finalSubmit(code);
                return false;
            }
            else if(code=="-8") {
                showClassLevelCompletion();
                return false;
            }
            else {
                var tmpMode = $("#tmpMode").val().replace(/^\s*|\s*$/g, "");
                if(tmpMode=="timedtest")
                {
                	redirect=0;
                	document.quesform.action='timedTest.php';
                	document.quesform.submit();
                }
                else if(tmpMode=="game")
                {
                    redirect=0;
                	document.getElementById('mode').value=tmpMode;
                    document.quesform.action='controller.php';
                    document.quesform.submit();
                }
                else
                {
                    document.quesform.action='Question.php';
	                showNextQuestion();
                }
            }
        }
    }
}
var attempt=0;
function getNextQues(params,mode)
{
    if(params.search(/nextQuesLoaded=1/)!=-1)
    {
        return;
    }
	attempt++;
	var url="controller.php";
	new Ajax.Request(url, {
      method: 'post',
      parameters: params,
      onSuccess:
      function process(transport) {

			var response = transport.responseText;
			//alert("Response : " + response);

			if (window.ActiveXObject)        {  // code for IE
		        var doc=new ActiveXObject("Microsoft.XMLDOM");
		        doc.async="false";
		        doc.loadXML(response);
		    }
		    else  { // code for Mozilla, Firefox, Opera, etc.
		        var parser=new DOMParser();
		        var doc=parser.parseFromString(response,"text/xml");
		    }

		    if(mode=="normal")
		    {
		    	try{
		    		var response=doc.documentElement;

		    		if(response.childNodes[0].childNodes[0].nodeValue)
		    			document.getElementById("qcode").value = response.childNodes[0].childNodes[0].nodeValue;
		    		else
		    			document.getElementById("qcode").value = '';
		            if(response.childNodes[1].childNodes[0].nodeValue!="ERROR")
		              	document.getElementById("tmpMode").value = response.childNodes[1].childNodes[0].nodeValue;
			        if(response.childNodes[2].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("quesCategory").value = response.childNodes[2].childNodes[0].nodeValue;
			        else
			        	document.getElementById("quesCategory").value = '';
		            if(response.childNodes[3].childNodes[0].nodeValue)
			        	document.getElementById("showAnswer").value = response.childNodes[3].childNodes[0].nodeValue;
			        else
			        	document.getElementById("showAnswer").value = '';
			        if(response.childNodes[4].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("correctAnswer").value = response.childNodes[4].childNodes[0].nodeValue;
			        else
			        	document.getElementById("correctAnswer").value = '';
			        if(response.childNodes[5].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("quesType").value = response.childNodes[5].childNodes[0].nodeValue;
			        else
			        	document.getElementById("quesType").value = '';
			        if(response.childNodes[6].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("clusterCode").value = response.childNodes[6].childNodes[0].nodeValue;
			        else
			        	document.getElementById("clusterCode").value = '';
			        if(response.childNodes[7].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("hasExpln").value = response.childNodes[7].childNodes[0].nodeValue;
			        else
			        	document.getElementById("hasExpln").value = '';
			        if(response.childNodes[8].childNodes[0].nodeValue!="ERROR")
			        {
			        	Q1 = response.childNodes[8].childNodes[0].nodeValue;
			        	document.getElementById("qno").value = Q1;
			        }
			        else
			        	Q1 = '';
			        if(response.childNodes[9].childNodes[0].nodeValue!="ERROR")
			        	Q2 = response.childNodes[9].childNodes[0].nodeValue;
			        else
			        	Q2 = '';
			        if(response.childNodes[10].childNodes[0].nodeValue!="ERROR")
			        	Q4 = response.childNodes[10].childNodes[0].nodeValue;
			        else
			        	Q4 = '';
			        if(response.childNodes[11].childNodes[0].nodeValue!="ERROR")
			        	Q5 = response.childNodes[11].childNodes[0].nodeValue;
			        else
			        	Q5 = '';
			        if(response.childNodes[12].childNodes[0].nodeValue!="ERROR")
			        	footerBar = response.childNodes[12].childNodes[0].nodeValue;
			        else
			        	footerBar = '';
			        if(response.childNodes[13].childNodes[0].nodeValue!="ERROR")
			        	sparkie = response.childNodes[13].childNodes[0].nodeValue;
			        else
			        	sparkie = '';
			        if(response.childNodes[14].childNodes[0].nodeValue!="ERROR")
			        	pnlCQ1 = response.childNodes[14].childNodes[0].nodeValue;
			        else
			        	pnlCQ1 = '';
			        if(response.childNodes[15].childNodes[0].nodeValue!="ERROR")
			        	voiceover1 = response.childNodes[15].childNodes[0].nodeValue;
			        else
			        	voiceover1 = '';
			        if(response.childNodes[16].childNodes[0].nodeValue!="ERROR")
			        	hint1 = response.childNodes[16].childNodes[0].nodeValue;
			        else
			        	hint1 = '';
			        if(response.childNodes[17].childNodes[0].nodeValue!="ERROR")
			        	 document.getElementById("dropdownAns").value = response.childNodes[17].childNodes[0].nodeValue;
			        else
			        	 document.getElementById("dropdownAns").value = '';
			        if(response.childNodes[18].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("dynamicQues").value = response.childNodes[18].childNodes[0].nodeValue;
			        else
			        	document.getElementById("dynamicQues").value = '';
			        if(response.childNodes[19].childNodes[0].nodeValue!="ERROR")
			        	 document.getElementById("dynamicParams").value = response.childNodes[19].childNodes[0].nodeValue;
			        else
			        	 document.getElementById("dynamicParams").value = '';
			        if(response.childNodes[20].childNodes[0].nodeValue!="ERROR")
			        	preload1 = response.childNodes[20].childNodes[0].nodeValue;
			        else
			        	preload1 = '';

			        checkImage(preload1);

			        if(response.childNodes[21].childNodes[0].nodeValue!="ERROR")
			        	problemid1 = response.childNodes[21].childNodes[0].nodeValue;
			        else
			        	problemid1 = '';
			        if(response.childNodes[22].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("topicChangeMsg").innerHTML = response.childNodes[22].childNodes[0].nodeValue;
			        if(response.childNodes[23].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("quesVoiceOver").value = response.childNodes[23].childNodes[0].nodeValue;
			        else
			        	document.getElementById("quesVoiceOver").value = '';
			        if(response.childNodes[24].childNodes[0].nodeValue!="ERROR")
			        	document.getElementById("ansVoiceOver").value = response.childNodes[24].childNodes[0].nodeValue;
			        else
			        	document.getElementById("ansVoiceOver").value = '';

					document.getElementById("nextQuesLoaded").value = 1;

					window.status = 'Complete....';
		        }
		        catch(err)
		        {
		           	alert("getNextQues " + err.description);
		        	document.getElementById('nextQuesLoaded').value = "-1";
		        }
		    }
		}
		,
      onFailure: function() {
      	alert("The connection get failed");
    },
    onLoading: function()
    {
    	window.status = 'Saving...';
    	/*if(attempt > 100)
    		ajaxFunction(params,"normal");*/
    },
    onComplete: function()
    {
    	window.status = 'Complete...';
    	attempt = 0;
    }

  });
}
function saveEoLCQResp(params,mode)
{
    if(params.search(/nextQuesLoaded=1/)!=-1)
        return;
    var request = new Ajax.Request('controller.php',
    {
        method:'post',
        parameters: params,
        onSuccess: function(transport)
        {
            resp = transport.responseText;
            if(trim(resp)=="")  // code for IE
                return;
            if (window.ActiveXObject)        {
                var doc=new ActiveXObject("Microsoft.XMLDOM");
                doc.async="false";
                doc.loadXML(resp);
            }
            else  { // code for Mozilla, Firefox, Opera, etc.
                var parser=new DOMParser();
                var doc=parser.parseFromString(resp,"text/xml");
            }
            if(mode=="normal")        {
                try {
                    var response=doc.documentElement;
                    var code = response.childNodes[0].childNodes[0].nodeValue;

                    document.getElementById("qcode").value        = code;
                    document.getElementById("qno").value          = response.childNodes[1].childNodes[0].nodeValue;
                    document.getElementById("quesCategory").value = response.childNodes[2].childNodes[0].nodeValue;
                    document.getElementById("showAnswer").value   = response.childNodes[3].childNodes[0].nodeValue;
                    document.getElementById("tmpMode").value      = response.childNodes[4].childNodes[0].nodeValue;
                    document.getElementById('nextQuesLoaded').value = "1";
                }
                catch(err)
                {
                    alert("saveEoLCQResp " + err.description);
                	document.getElementById('nextQuesLoaded').value = "-1";
                }
            }
        },
        onFailure: function()
        {
            document.getElementById('nextQuesLoaded').value = "-1";
        }
    }
    );
}
function getFlashMovieObject(simplemovieQ)
{
    if (window.document.simplemovieQ)
        return window.document.simplemovieQ;
    if (navigator.appName.indexOf("Microsoft Internet")==-1)
    {
        if (window.document.controller.embeds && window.document.controller.embeds[simplemovieQ])
        {
            return window.document.controller.embeds[simplemovieQ];
        }
    }
    else
        return window.document.getElementById("simplemovieQ");
}
function checkDropDownAns()
{
    var objArray = document.getElementsByTagName("select");
    var ans = new Array();
    var isanswered = false;
    var blankAns = $('#correctAnswer').val();
    var userResp = "";
    //Check for drop down ans - if atleast one filled in
    for(var i=0; i<objArray.length; i++)
    {
        ans[objArray[i].id.substr(6)] = objArray[i].selectedIndex;
        userResp += objArray[i].value + "|";
        if(objArray[i].value!="")
            isanswered = true;
    }
    if(!isanswered && trim(blankAns)=="")
        return 2;
    else
    {
        var blanksAnswered = false;
        if(trim(blankAns)!="")
        {
            var ansStr = blankAns.split("|");
            for(var j=0; j<ansStr.length; j++)
            {
                var blankno = j + 1;
                var objStr = 'b'+blankno;
                userResp += document.getElementById(objStr).value + "|";
                if(document.getElementById(objStr).value!="")
                    blanksAnswered = true;
            }
        }
        if(!blanksAnswered && !isanswered)
            return 2;
    }
    userResp = removeInvalidChars(userResp.substr(0,userResp.length - 1));
    document.getElementById('userResponse').value = userResp;
    var ansStr = ans.join('|');
    var correctanswer = decrypt($('#dropdownAns').val());
    if(ansStr==correctanswer)
    {
        if(trim(blankAns)=="")
            return 1;
        else
        {
            blankAns = decrypt(blankAns);
            ansStr = blankAns.split("|");
            var flag = 0;
            for(var j=0; j<ansStr.length; j++)
            {
                ans = ansStr[j];
                waysToAns = ans.split("~");
                waysToAns = unique(waysToAns);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)
                var objStr = 'b'+(j+1);
                b = document.getElementById(objStr).value;
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
                        else if(!isNaN(ans_blank) && ans_blank!="") //Remove comma only when expected answer is numeric
                        {
                            b1AfterCommaRemoval = b1.replace(/,/g,"");
                            if(!isNaN(b1AfterCommaRemoval) && b1AfterCommaRemoval!="")
                               b1= b1AfterCommaRemoval;
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
function submitAnswer()
{
	allowed=0;
	try {
        document.getElementById('btnSubmit').disabled=true;
        var quesType = document.getElementById('quesType').value;
        if(quesType=='D')
        {
            var result = checkDropDownAns();
            document.getElementById('result').value = result;
        }
        else if(quesType!='I')
            var result = evaluateResponse();
        else
        {
            var flashMovie=getFlashMovieObject("simplemovieQ");
            var result = flashMovie.GetVariable("answer");
            document.getElementById('result').value = result;
        }

        showAnswer(result, quesType);
        if(result==2) {
            document.getElementById('btnSubmit').disabled=false;
            allowed=1;
            return false;
        }
        //stopTheTimer();
        document.getElementById('secsTaken').value = secs;
        secs = 0;
        document.getElementById('refresh').value = 1;
        if($('mode'))
        {
            if(document.getElementById('quesCategory').value=="EoLCQ")
                document.getElementById('mode').value = "EoLCQ";
            else
                document.getElementById('mode').value = "submitAnswer";
        }
        //Form.enable($('quesform'));
        document.getElementById('btnSubmit').disabled=true;
        var params = Form.serialize("quesform");
        if(document.getElementById('quesCategory').value=="EoLCQ")
        {
            initializeTimer();
            saveEoLCQResp(params,"normal");
        }
        else
        {
        	//setTimeout('getNextQues(\''+params+'\',"normal")', 8000);
        	getNextQues(params,"normal");
        }
    }
    catch(err)
    {
        alert("Submit Answer "+ err.description);
        document.getElementById('btnSubmit').disabled=false;
    }
}
function removeInvalidChars(tmp) {
    tmp = escape(tmp);
	tmp = tmp.replace( /%D7/g, "&times;" ) ;
	tmp = tmp.replace( /%F7/g, "&divide;" ) ;
	tmp = tmp.replace( /%AB/g, "&laquo;" ) ;
	tmp = tmp.replace( /%B0/g, "&deg;" ) ;
	tmp = tmp.replace( /%BB/g, "&raquo;" ) ;
	tmp = tmp.replace( /%u2220/g, "&ang;" ) ;
	tmp = tmp.replace( /%u03B1/g, "&alpha;" ) ;
	tmp = tmp.replace( /%u03B2/g, "&beta;" ) ;
	tmp = tmp.replace( /%u03B3/g, "&gamma;" ) ;
	tmp = tmp.replace( /%u0394/g, "&Delta;" ) ;
	tmp = tmp.replace( /%u03BB/g, "&lambda;" ) ;
	tmp = tmp.replace( /%u03B8/g, "&theta;" ) ;
	tmp = tmp.replace( /%u03C0/g, "&pi;" ) ;
	tmp = tmp.replace( /%u2211/g, "&sum;" ) ;
	tmp = tmp.replace( /%u221A/g, "&#8730;" ) ;
	tmp = tmp.replace( /%u221B/g, "&#8731;" ) ;
	tmp = tmp.replace( /%BB/g, "&raquo;" ) ;
	tmp = tmp.replace( /%AB/g, "&laquo;" ) ;
	tmp = tmp.replace( /%F7/g, "&divide;" ) ;
	tmp = tmp.replace( /%D7/g, "&times;" ) ;
	tmp = tmp.replace( /%u2264/g, "&le;" ) ;
	tmp = tmp.replace( /%u2265/g, "&ge;" ) ;
	tmp = tmp.replace( /%u22A5/g, "&perp;" ) ;
	tmp = tmp.replace( /%u03B4/g, "&delta;" ) ;
	tmp = tmp.replace( /%u03C3/g, "&sigma;" ) ;
	tmp = tmp.replace( /%u2282/g, "&sub;" ) ;
	tmp = tmp.replace( /%u2284/g, "&#8836;" ) ;
	tmp = tmp.replace( /%u2245/g, "&cong;" ) ;
	tmp = tmp.replace( /%u2260/g, "&ne;" ) ;
	tmp = tmp.replace( /%u2208/g, "&#8712;" ) ;
	tmp = tmp.replace( /%u2209/g, "&#8713;" ) ;
	tmp = tmp.replace( /%u222A/g, "&#8746;" ) ;
	tmp = tmp.replace( /%u2229/g, "&#8745;" ) ;
	tmp = tmp.replace( /%u223C/g, "&#8764;" ) ;
	tmp = unescape(tmp);
	return tmp;
}
function evaluateResponse()
{
    var result;
    var ques_type = document.getElementById('quesType').value;
    var part_ans = new Array();
    var correctanswer = decrypt($('#correctAnswer').val());//Split the Answers - For Blanks
    part_ans = correctanswer.split("|");
    var flag=0;
    var count = part_ans.length;
    var checkblanks=0;
    var userResp  = "";
    for(var i=0;i<part_ans.length;i++)
    {
        var ans = new Array();
        //Split the different way to answer
        ans = part_ans[i].split("~");
        ans = unique(ans);        //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)
        var b = '';
        if(ques_type=='Blank')
        {
            b = eval("document.getElementById('b"+(i+1)+"').value");
            b = b.replace(/\\/g,"/");
            if(b=='')
                checkblanks++;
        }
        else
        {
            for (r=0; r<document.quesform.ansRadio.length; r++)
            {
                if (document.quesform.ansRadio[r].checked)
                    b = document.quesform.ansRadio[r].value;
            }
        }
        userResp = userResp + b  + "|";
        for(var j=0;j<ans.length;j++)
        {
            if(b=='' && ans[j]=='')
                flag++;
            else
            {
                b1   = trim(b);
                ans_blank = trim(ans[j]);
                if(b1!="" && ans_blank!="" && !isNaN(b) && !isNaN(ans_blank))
                {
                    b1 = parseFloat(b1);
                    ans_blank = parseFloat(ans_blank);
                }
                else if(!isNaN(ans_blank) && ans_blank!="") //Remove comma only when expected answer is numeric
                {
                    b1AfterCommaRemoval = b1.replace(/,/g,"");
                    if(!isNaN(b1AfterCommaRemoval) && b1AfterCommaRemoval!="")
                       b1= b1AfterCommaRemoval;
                    b1 = parseFloat(b1);
                    ans_blank = parseFloat(ans_blank);
                }
                if(b1==ans_blank) {
                    flag++;
                    break;
                }
            }
        }
    }
    userResp = userResp.substr(0,userResp.length - 1);
    document.getElementById('userResponse').value = userResp;
    if(b=='' && (ques_type=='MCQ-4' || ques_type=='MCQ-3' || ques_type=='MCQ-2'))
        return 2;
    //All Blank must be right
    if(count==flag)
    {
        document.getElementById('result').value = 1;
        return 1;
    }
    else
    {
        if(b=='' && checkblanks==count)
        {
            return 2;
        }
        document.getElementById('result').value = 0;
        return 0;
    }
    return 1;
}
function showAnswer(result, ques_type)
{
    try{
       	niftyplayer('niftyPlayer1').stop();
    }catch(err){};
    var msg;
    var correctanswer = decrypt($('#correctAnswer').val());
    if(result==2)
    {
        alert("Please specify your answer!");
        return false;
    }
    else
    {
    	if(document.getElementById('ansRadioA'))
    		document.getElementById('ansRadioA').disabled = true;
    	if(document.getElementById('ansRadioB'))
    		document.getElementById('ansRadioB').disabled = true;
    	if(document.getElementById('ansRadioC'))
    		document.getElementById('ansRadioC').disabled = true;
    	if(document.getElementById('ansRadioD'))
    		document.getElementById('ansRadioD').disabled = true;
    	for(var j=0;j<5; j++)
    	{
    		if(document.getElementById('b'+j))
    		{
    			document.getElementById('b'+j).disabled = true;
    		}
    	}
        if(result==1)
        {
            msgArray = shuffle(msgArray);
            var randomnumber=Math.floor(Math.random()*40);
            msg = "";
            if($("#quesCategory").val()!="normal")
            {
                var noOfSparkies = 5;
                if($('#showAnswer').val()==1)
                    noOfSparkies = 2;
                var cls = parseInt(document.getElementById('childClass').value);
                if(cls < 8)
                    msg += " You solved the challenge problem correctly!<br/><br/>You get " + noOfSparkies + " sparkies for this!<br/>";
                else {
                    noOfSparkies = noOfSparkies * 10;
                    msg += " You solved the challenge problem correctly!<br/><br/>You get " + noOfSparkies + " reward points for this!<br/>";
                }
            }
            if(document.getElementById('hasExpln').value==1)
                msg += "<br/><b>Answer: </b>" + decrypt($('dAnswer').innerHTML)+"<br/>";
            msg += "<br/>Please click the Next Question button to continue!!<br/><br/>";
            $("displayanswer").innerHTML = msg;
            $("tl").innerHTML = "<img src='images/right_smiley.gif' style='vertical-align:middle'> " + msgArray[randomnumber] + "!";
            $("hd").style.backgroundColor = "#00bc1b";
        }
        else if(result==0)
        {
            if(document.getElementById('ansVoiceOver').value!="")
            {    playme('A');}
            msg = "";
            if($("#quesCategory").val()=="challenge" && $('#showAnswer').val()==0)
                msg += "<br/>But don't worry, you can attempt this problem again later!<br>";
            if($("#quesCategory").val()=="EoLCQ" && $('#showAnswer').val()==0)
            {
                msg += "<br/><br/>Please Try Again!<br>";
            }
            if($("#quesCategory").val()=="normal" || ($("#quesCategory").val()=="challenge" && $('#showAnswer').val()==1)  || ($("#quesCategory").val()=="EoLCQ" && $('#showAnswer').val()==1))        //Show the display answer for normal questions only, not the challenge question.
            {
                msg += "<br>";
                msg += "<span style='vertical-align:top'>Correct answer: </span><b>";
                var tmpAns = decrypt($('dAnswer').innerHTML);
            	if((ques_type=='MCQ-4' || ques_type=='MCQ-3' || ques_type=='MCQ-2') && trim(tmpAns)!=correctanswer)
            		msg += correctanswer + ": ";
            	msg += tmpAns+"</b>";
            }
            msg += "<br><br>";
            if(!($("#quesCategory").val()=="EoLCQ" && $('#showAnswer').val()==0))
            {
                msg += "Please click the Next Question button to continue.";
                msg += "<br><br>";
            }
            $("displayanswer").innerHTML = msg;

            $("tl").innerHTML = "<img src='images/wrong_smiley.gif'  style='vertical-align:middle'> Sorry, that's incorrect!";
            $("hd").style.backgroundColor = "#CD5C5C";
            if(document.getElementById('quesType').value=='D')
            {
                var objArray = document.getElementsByTagName("select");
                var ans = new Array();
                var isanswered = false;
                for(var i=0; i<objArray.length; i++)
                    ans[objArray[i].id.substr(6)] = objArray[i].selectedIndex;
                var correctanswer = decrypt($('#dropdownAns').val());
                correctanswer = correctanswer.split(',');
                for(var j=0; j<correctanswer.length; j++)
                    if(correctanswer[j]!=ans[j] && document.getElementById('spnOpt'+j))
                        document.getElementById('spnOpt'+j).style.color = 'red';
            }
        }

        if($("#quesCategory").val()=="EoLCQ" && $('#showAnswer').val()==0 && result==0)
            document.getElementById('btnNextQues').value = "Ok";
        else
            document.getElementById('btnNextQues').value = "Next Question";
        $('btnNextQues').style.display = "inline";

        document.getElementById('pnlButton').scrollIntoView(1);
        document.getElementById('dlgAnswer').style.display="none";
        FoldElement('dlgAnswer');
        document.getElementById('btnSubmit').style.display="none";
    }
}
shuffle = function(o){ //v1.0
        for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
        return o;
};
function unique(a) {
    var r = new Array();
    o:for(var i = 0, n = a.length; i < n; i++) {
        for(var x = 0, y = r.length; x < y; x++)
        {
            if(trim(r[x])==trim(a[i]))
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
function checkLoadingComplete()
{
    var currForm = document.images;
    var flag=true;
    for (var eLoop=0; eLoop<currForm.length; eLoop++)
    {
        if(currForm[eLoop].complete==false)
            flag=false;
        if (typeof currForm[eLoop].naturalWidth!= "undefined" && currForm[eLoop].naturalWidth== 0)
            flag = false;
    }
    if(flag)
    {
    	var plend = new Date();
		// calculate the elapsed time between the start and the end. // This is in milliseconds
		if(plstart!=null)
		{
			plstart = new Date(plstart);
		}
		else
		{
			plstart = plend;
		}
		var plload = (plend.getTime() - plstart.getTime())/1000;
		if(document.getElementById('pageloadtime'))
			document.getElementById('pageloadtime').value = plload;

        initializeTimer();
    }
    else
        self.setTimeout("checkLoadingComplete()", 500);
}
function initializeTimer()
{
    secs=0;
    document.getElementById('btnSubmit').disabled=false;
    if(document.getElementById('btnTopicChange'))
    	document.getElementById('btnTopicChange').disabled=false;
    startTheTimer();
}
function stopTheTimer()
{
    if(timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}
function startTheTimer()
{
    secs = secs + 1;
    timerRunning = true;
    timerID = self.setTimeout("startTheTimer()", delay);
}
function logoff()
{
	setTryingToUnload();
    redirect=0;
	finalSubmit(6);
}
function submit_exam()
{
    redirect=0;
	if($('#quesCategory').val()=="challenge")
        msg = "If you end now, you will lose the chance to answer this question. Do you still want to end the current session?"
    else
        msg = "Are you sure you want to end the current session?";
    var ans = confirm(msg);
    if(ans)
        finalSubmit(1); //1 implies End session clicked.
}
function finalSubmit(code)
{
    document.getElementById('mode').value = code;
    if(code==1 || code==-5 || code==6 || code==-6 || code==-7)        //i.e. 1 means End Session button clicked.
    {
        var params= "mode=endsession";
        params += "&code="+code;
        try {
			$.post("controller.php","parameters="+params,function(transport){
				
					resp = transport.responseText|| "no response text";
                    redirResult();
				
			});
        }
        catch(err) {alert("Final Submit " + err.description);}
    }
    else
        redirResult();
}
function redirResult()
{
    redirect=0;
	document.quesform.action='logout.php';
    document.quesform.submit();
}
function makeQuestionVisible()                //Code to set focus on first textbox
{
    if(!isIpad)
    {
        history.go(+1);
    }
    var obj="";
    var bFound = false;
    for(i=0; i < document.forms[0].length && !bFound; i++)
    {
        if (document.forms[0][i].type != "hidden")
        {
            if (document.forms[0][i].disabled != true)
            {
                var tabindex = document.forms[0][i].getAttribute("tabindex");
                if(tabindex==1) {
                    obj = document.forms[0][i];
                    bFound = true;
                }
                else if (document.forms[0][i].name == 'blank_1') {
                    obj = document.forms[0][i];
                }
            }
        }
    }
    if(obj)
        obj.focus();
}
function showCommentBox()
{
	if(allowed==1)
	{
		document.getElementById('pnlQuestion').style.display="none";
		document.getElementById('pnlTopicChangeMsg').style.display="none";

		if(document.getElementById('result').value!="")
		{
			document.getElementById('dlgAnswer').style.display="none";
		}
	    $('pnlComment').style.display='inline';
	}
}
function hideCommentBox() {
    document.getElementById('comment').value = "";
    if(document.getElementById('result').value!="")
	{
		document.getElementById('dlgAnswer').style.display="block";
	}
	document.getElementById('pnlQuestion').style.display="inline";
	document.getElementById('pnlComment').style.display = 'none';
}
function mailComment()
{
	if(trim($('#comment').val())=="")
    {
        alert("Please enter a comment!");
        $('comment').focus();
        return false;
    }
    else
        sendMail($('#comment').val());
    hideCommentBox();
}
function sendMail(comment)
{
    var params="";
    params+= "problemid=" + escape($('#problemid').val());
    params+= "&comment=" + escape(comment);
    params+= "&quesNo=" + escape($('#qno').val());
    params+= "&qcode=" + escape($('#qcode').val());
    params+= "&mode=comment";
    try {
        var request = new Ajax.Request('controller.php',
        {
            method:'post',
            parameters: params,
            onSuccess: function(transport)
            {
                resp = transport.responseText || "no response text";
                document.getElementById('comment').value="";
            },
            onFailure: function()
            {
                //alert('Something went wrong...');
            }
        }
        );
    }
    catch(err){alert("sendMail "+err.description);}
}
function switchTopic()
{
	if(allowed==1)
	{
		if(document.getElementById('result').value!="")
		{
			document.getElementById('dlgAnswer').style.display="none";
		}
		document.getElementById('pnlQuestion').style.display="none";
		document.getElementById('pnlComment').style.display="none";
	    if($('pnlTopicChangeMsg'))
	        $('pnlTopicChangeMsg').style.display = 'inline';
	    if($('pnlTopicChngBtn'))
	        $('pnlTopicChngBtn').style.display = "inline";
	}
}
function hideTopicChangeDlg()
{
	if(document.getElementById('result').value!="")
	{
		document.getElementById('dlgAnswer').style.display="block";
	}
	document.getElementById('pnlQuestion').style.display="inline";
	document.getElementById('pnlTopicChangeMsg').style.display = 'none';
}
function changeTopic()
{
	redirect=0;
	document.getElementById('mode').value = "topicSwitch";
    $('quesform').action = "controller.php";
    $('quesform').submit();
}
function checkKeyPress(e) {

	var keyPressed = e ? e.which : window.event.keyCode;
    if(keyPressed == 13)        //13 implies enter key
    {
		if(document.getElementById('result').value=="")
			submitAnswer();
	    else
	    {
	    	if(allowed==1)
	    		handleClose();
	    }
    }
}
function checkSubmit()
{
    return false;
}
function setOpt(opt)
{
    if(document.getElementById('ansRadio'+opt))
        document.getElementById('ansRadio'+opt).checked = true;
}
function decrypt(str) {

    var strtodecrypt = str.split("-");
    var msglength = strtodecrypt.length;
    decrypted_message="";

    for (var position = 0; position < msglength; position++)        {
        ascii_num_byte_to_decrypt = strtodecrypt[position];
        ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt / 2;
        ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt - 5;
        decrypted_byte = String.fromCharCode(ascii_num_byte_to_decrypt);
        decrypted_message += decrypted_byte;
    }
    return decrypted_message;
}
function updateTimeTakenForExpln(secs)
{
    var params= "mode=timeTakenForExpln";
    params += "&timeTaken="+secs;
    try {
        var request = new Ajax.Request('controller.php',
        {
            method:'post',
            parameters: params,
            onSuccess: function(transport)
            {
                //do nothing;
                //window.status = "Update time taken for explanation";
            }
        }
        );
    }
    catch(err) {alert("updateTimeTakenForExpln " + err.description);}
}
function showClassLevelCompletion()
{
	redirect=0;
	document.quesform.action='classLevelCompletion.php';
    document.quesform.submit();
}
function FoldElement(contentId)
{
	var contentElement = document.getElementById(contentId);

	if (contentElement.style.display == 'none')
	{
		TransitionShowNode(contentElement);
	}
	else if (contentElement.style.display == 'block' || !contentElement.style.display)
	{
		TransitionHideNode(contentElement);
	}
}
function TransitionHideNode(contentElement)
{
	SlideElement(contentElement.id, false);
}
function TransitionShowNode(contentElement)
{
	SlideElement(contentElement.id, true);
}
function SlideElement(elementId, show)
{
	var slideSpeed = 10;
	var slideTimer = 1;
	var content = document.getElementById(elementId);
	var content_inner = document.getElementById(elementId + "_inner");

	var height = content.offsetHeight;
	//var height = content.clientHeight;
	if (height == 0)
	{
		height = content.offsetHeight;
		content.style.display = 'block';
	}
	height = height + (show ? slideSpeed : -slideSpeed);

	var rerun = true;

	if (height >= content_inner.offsetHeight)
	{
		height = content_inner.offsetHeight;
		rerun = false;
	}
	else if (height <= 1)
	{
		height = 1;
		rerun = false;
	}
	content.style.height = height + 'px';
	var topPos = height - content_inner.offsetHeight;
	if (topPos > 0)
	{
		topPos = 0;
	}
	content_inner.style.top = topPos + 'px';

	if (rerun)
	{
		setTimeout("SlideElement('"+ elementId + "', " + show + ");",slideTimer);
	}
	else
	{
		if (height <= 1)
			content.style.display = 'none';
		else
			showFractions();

		allowed=1;
	}
}
function showFractions()
{
	try {
	var objArray = document.getElementsByClassName('num');
    for(k=0; k<objArray.length; k++)
    {
    	objArray[k].style.top = "-1.3ex";
    }
    var objArray = document.getElementsByClassName('den');
    for(k=0; k<objArray.length; k++)
    {
    	objArray[k].style.top = "1.2ex";
    }
	}catch(err) {}
}
function showSlowLoadingMsg()
{
	clearTimeout(slowLoadTimer);
	if(document.getElementById('pnlSlowLoading'))
	{
		document.getElementById('pnlSlowLoading').innerHTML = "This seems to be taking time. Press F5 to try again.";
		document.getElementById('pnlSlowLoading').style.display = "block";
	}
}
function showNextQuestion()
{
	//try
	//{
		document.getElementById("q1").innerHTML=Q1;
		document.getElementById("q2").align = "left";
		document.getElementById("q2").innerHTML=Q2;
		document.getElementById("q4").innerHTML=Q4;
		document.getElementById("dAnswer").innerHTML=Q5;
		document.getElementById("footer1").innerHTML=footerBar;
		document.getElementById("sparkie1").innerHTML=sparkie;
		document.getElementById("pnlCQ").innerHTML=pnlCQ1;
		document.getElementById("voiceover").innerHTML=voiceover1;
		document.getElementById("hint").innerHTML=hint1;

		window.location.hash = 1;

		document.getElementById("problemid").value = problemid1;
		if(document.getElementById("subject"))
			document.getElementById("subject").value = problemid1;
		document.getElementById('pnlQuestion').style.display="inline";

		clearTimeout(slowLoadTimer);
		clearTimeout(logoffTimer);

		makeQuestionVisible(); init();

		document.getElementById("nextQuesLoaded").value = 0;
		document.getElementById('pnlButton').style.display="block";
		if(document.getElementById('pnlCQ'))
			document.getElementById('pnlCQ').style.display="block";

		document.getElementById('pnlLoading').style.display="none";
		document.getElementById('pnlSlowLoading').innerHTML = "";
		document.getElementById('pnlSlowLoading').style.display="none";
		document.getElementById('btnSubmit').style.display="inline";
		document.getElementById('result').value="";
		document.getElementById('btnNextQues').value = "Next Question";

	/*}
	catch(err)
	{
		alert("showNextQuestion " + err.description);
	}*/
}
var counter = 0;
function noimage(a)
{
	counter++;
	//document.getElementById("img_error").innerHTML = "Image Load Attempt "+counter;
	if(counter>=15)
	{
		// disable onerror to prevent endless loop
		a.onerror = "";
		counter=0;
		//document.getElementById("img_error").innerHTML = "<div><u>Your net is slow/down, please do end session and login again</u></div>";
	}
	a.src = a.src + '?' + (new Date()).getTime();
}
var pic = new Array();
function checkImage(str)
{
	var col_array=str.split(",");
	var part_num=0;

	while (part_num < col_array.length-1)
	{
	  	imgName = col_array[part_num];
	    pic[part_num] = new Image();
	    pic[part_num].src= col_array[part_num];
	  	part_num+=1;
	}
}