var toolClick=0;
var click=0;
var infoClick=0;
var secs,secsTaken = 0;
var plstart = new Date();
var timerID = null;
var timerRunning = false;
var slowLoadTimer;
var logoffTimer;
var emotID = "";
var prevQcode = ""; // For EmotToolBar, Checkbox for 'My answer was marked wrong' & NCERT Exercise..
var prevQuesCategory = "";
var prevQcodeAfterNext = ""; // For Student comment on prevquestions
var prevQuesCategoryAfterNext =  "";
var prevQuesHtml =  "";
var commenttype="";
var prevQuesClone = "";
var allowed = 1; //For concecutive enter stop the enter key press till scroll part complete 1 - allow , 0 - cancel (Submit & Next Question)
var placeHolderText = "Explain why you think your answer is correct.\nOR\nUncheck the check-box above and click on 'Next question'.";
var placeHolderRepeatText = "Explain whether this question is an exact repeat or is similar to a previous question.\nOR\nUncheck the check-box above and click on 'Next question'.";
var previousCommonError="";
var allowCommonError=true;
var redirect = 1;
var counter = 0;
var etTimer = null;
var glossaryDescArray = new Array();
var autoHideDisplayBar;
var emoteToolbarTagCount = 0;
var noOfCondition=0;
var conCheck=0;
var condition;
var action;
var ano=1;
var totalAttempts;
var timeToAnswer = 0; //used for hints 
var timeToAnswerID;
var userAllAnswers='';
var timeTakenToViewHintID,timeTakenForAllHintStr;
var timeTakenToViewHint=0;
var timeTakenForAllHints = new Array();
var questionsDoneEC=0;
var quesCorrectEC=0;
var junkCommentNo=0;
var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1;
var isIpad = ua.indexOf("ipad") > -1;
var objQuestion, objNextQuestion;
var objFrustration = new frustrationModel();
if(document.addEventListener)
    document.addEventListener("keydown", my_onkeydown_handler,true);
else if (document.attachEvent)
    document.attachEvent("onkeydown", my_onkeydown_handler);

window.history.forward(1);
document.onkeypress = checkKeyPress;
if (document.layers) document.captureEvents(Event.KEYPRESS);

/*try { 
  window.onerror = function(err, url, line) {
  	//suppress browser error messages
	var suppressErrors = false;
	if(err.indexOf('ReferenceError: setFocus is not defined')==-1 && url.indexOf('fracbox.js')==-1)
	{		
    //api url
    var apiUrl = 'errorLog.php';        
	//var errMsg =  "Error: "+err+"~\r\nerrLine:"+line+" ~\r\nerrURL:"+url;	
    $.post("errorLog.php","params="+err+"&errorURL="+url+"&errorline="+line+"&typeErrorLog=10",function(data) {
						
		});		
	}
    return suppressErrors;
  };
} catch(e) { }*/

function noenter(e) {
    e = e || window.event;
    var key = e.keyCode || e.charCode;
    return key !== 13; 
}

function checkLoadingComplete()
{
	var currForm = document.images;
	var flag = true;
	for (var eLoop = 0; eLoop < currForm.length; eLoop++)
	{
		if (currForm[eLoop].complete == false)
			flag = false;
		if (typeof currForm[eLoop].naturalWidth != "undefined" && currForm[eLoop].naturalWidth == 0)
			flag = false;
	}
	if (flag)
	{
		var plend = new Date();
		// calculate the elapsed time between the start and the end. // This is in milliseconds
		if (plstart != null)
			plstart = new Date(plstart);
		else
			plstart = plend;
		var plload = (plend.getTime() - plstart.getTime())/1000;
		if (document.getElementById('pageloadtime'))
			$('#pageloadtime').val(plload);
		initializeTimer();
	}
	else
		window.setTimeout("checkLoadingComplete()", 500);
}


window.onload=function()
{
  if(window.location.hash.length == 0)
  {
	if($("#quesCategory").val()=="topicRevision")
	{
	  $("#progress_bar").hide();
	}
	if(!isAndroid && !isIpad) {
	  	autoHideDisplayBar	=	setTimeout(function(){
			if(infoClick==0)
				hideBar();
			else
				clearTimeout(autoHideDisplayBar);
		},5000);
	}
  	window.location.hash.length = 1;
	var category = $('#quesCategory').val();
	var tmpMode = $('#tmpMode').val();
	var qno = document.getElementById('qno').value;
	var qcode = document.getElementById('qcode').value;
	params="qcode="+qcode+"&mode=firstQuestion&quesCategory="+category+"&qno="+qno+"&tmpMode="+tmpMode;
  	getNextQues(params,"normal");
	
	fetchNextQues();
	if(document.getElementById('ichar'))
		buddyinit();
    if(category=="NCERT"){
		$("#topic_ncert").css("display","block");
		$(".bubble").css("display","none");
		$("#chart").css("display","block");
		$("#boxRed").css("display","block");
		$("#boxGreen").css("display","block");
		$(".pending").css("display","inline-block");
		$(".complete").css("display","inline-block");
		$(".PS").css("display","block");
		$("#nextQuestion").css("display","block");
		$("#submitQuestion").css("display","none");
		$("#submitQuestion2").css("display","none");
		$("#question").css("margin-top","0px");
		$("#toolContainer").css("margin-top","150px");
	}
  }
  else
  {
        var request = $.ajax('controller_dev.php',
        {
            	type: 'post',
		data: "mode=back_refresh",
		success: function(transport)
		{
			//do nothing;
		}
	});
		window.open('', '_self', '');
    	window.close();
  }
}

function refreshScrollBar()
{
    var pane = $('#scroll').jScrollPane({showArrows: true, arrowSize: 17, autoReinitialise: true}).data('jsp');
    pane.reinitialise();
}
$(document).ready(function(e) {
	$("#msAsStudentInfo,#quitHigherLevel,#endTopicDiv,#toolContainer,#emotToolBar,#radioButtons,#report").css("visibility","hidden");
	$("#changeTopic").remove();
	setformfieldsize(jQuery('#markedWrongText'), 250, 'status1');
	setformfieldsize(jQuery('#markedRepeatText'), 250, 'status2');
	
    $("#wildcardImg").live("click",function() {
        $("#wildcardInfo").dialog({
               width: "400px",
               position: "right",
               draggable: false,
                resizable: false,
                modal: true
         });
    });

	$(".eqEditorToggler").live("click",function(){
		$(this).parents("td:first").next().find(".eqEditorConatiner:first").toggle("slow");
	});
    //Pending Integration
    $(".ui-widget-overlay").live ("click",function () {
        $("#wildcardInfo,#commentInfo").dialog( "close" );
        $("#commentInfo").html("");
    });
    $("#wildcardInfo").live ("click",function () {
        $("#wildcardInfo,#commentInfo").dialog( "close" );
        $("#commentInfo").html("");
    });
    $("#ui-dialog-titlebar").live ("click",function () {
        $("#wildcardInfo,#commentInfo").dialog( "close" );
        $("#commentInfo").html("");
    });

    //added to change the id's of fracbox in practice cluster
    if(document.getElementById("tmpMode").value == "practice")
    {
        var idNo = 1;
        $(".fracBox").each(function(index, element) {
            $(this).attr("id", "fracB_" + idNo);
            $(this).next("input").attr("id", "fracV_" + idNo);
            $(this).next().next("input").attr("id", "fracS_" + idNo);
            idNo++;
        });
    }
//-----ends here
    	$("#markedCheck").change(function(e) {
		
		adjustScreenElements();
		
        if($("#markedCheck").is(":checked"))
        {
			
			$(".markedWrong").css("display","block");
			$(".markedRepeat").css("display","block");
			$("#pnlAnswer").height($("#pnlAnswer").height()+80);
			$("#sideBar").height($("#sideBar").height()+80);
			$("#submitQuestion2").hide();
		}
		else
		{
			$(".markedWrong").css("display","none");
			$(".markedRepeat").css("display","none");
			$("#pnlAnswer").height($("#pnlAnswer").height()-80);
			$("#sideBar").height($("#sideBar").height()-80);
			$("#submitQuestion2").hide();
			
			if($("#markedWrong").is(":checked") && $("#markedRepeat").is(":checked"))
			{
				$('#markedWrong').prop('checked', false); 
				$('#markedRepeat').prop('checked', false); 
				$("#markedWrongTextTR").hide();
				$("#markedRepeatTextTR").hide();
				$("#pnlAnswer").height($("#pnlAnswer").height()-390);
				$("#sideBar").height($("#sideBar").height()-390);
			}
			
			if($("#markedWrong").is(":checked"))
			{
				$('#markedWrong').prop('checked', false); 
				$("#markedWrongTextTR").hide();
				$("#pnlAnswer").height($("#pnlAnswer").height()-180);
				$("#sideBar").height($("#sideBar").height()-180);
			}
			
			if($("#markedRepeat").is(":checked"))
			{
				$('#markedRepeat').prop('checked', false); 
				$("#markedRepeatTextTR").hide();
				$("#pnlAnswer").height($("#pnlAnswer").height()-210);
				$("#sideBar").height($("#sideBar").height()-210);
			}
			
		}
		adjustScreenElements();
    });
	
	$("#markedCheck1").change(function(e) {
		adjustScreenElements();
        if($("#markedCheck1").is(":checked"))
        {
			$("#submitQuestion2").hide();
			$(".markedWrong").css("display","none");
			$(".markedRepeat").css("display","block")
			$("#pnlAnswer").height($("#pnlAnswer").height()+80);
			$("#sideBar").height($("#sideBar").height()+80);
		}
		else
		{
			$(".markedWrong").css("display","none");
			$(".markedRepeat").css("display","none");
			$("#pnlAnswer").height($("#pnlAnswer").height()-80);
			$("#sideBar").height($("#sideBar").height()-80);
			$("#submitQuestion2").hide();
			
			if($("#markedRepeat").is(":checked"))
			{
				$('#markedRepeat').prop('checked', false); 
				$("#markedRepeatTextTR").hide();
				$("#pnlAnswer").height($("#pnlAnswer").height()-210);
				$("#sideBar").height($("#sideBar").height()-210);
			}
			
		}
		adjustScreenElements();
    });


    $("#markedWrong").change(function(e) {
		adjustScreenElements();
        $(".styledLable").toggleClass("checked");
        if($("#markedWrong").is(":checked"))
        {
			$("#submitQuestion2").hide();
			$("#markedWrongTextTR").show("slow",function(){
			$(this).focus();
		
		});
		$("#pnlAnswer").height($("#pnlAnswer").height()+180);//Textarea row height..
		$("#sideBar").height($("#sideBar").height()+180);
        }
        else
        {
		$("#submitQuestion2").hide();
		$("#markedWrongTextTR").hide();
		$("#pnlAnswer").height($("#pnlAnswer").height()-180);//Textarea row height..
		$("#sideBar").height($("#sideBar").height()-180);
        }
        //refreshScrollBar();
		adjustScreenElements();
    });
	
	 $("#markedRepeat").change(function(e) {
	 	adjustScreenElements();
        $(".styledLable").toggleClass("checked");
        if($("#markedRepeat").is(":checked"))
        {
			$("#submitQuestion2").hide();
			$("#markedRepeatTextTR").show("slow",function(){
			$(this).focus();
			
		});
		$("#pnlAnswer").height($("#pnlAnswer").height()+210);//Textarea row height..
		$("#sideBar").height($("#sideBar").height()+210);
		
		
	
        }
        else
        {
		$("#submitQuestion2").hide();
		$("#markedRepeatTextTR").hide();
		$("#pnlAnswer").height($("#pnlAnswer").height()-210);//Textarea row height..
		$("#sideBar").height($("#sideBar").height()-210);
        }
        //refreshScrollBar();
		adjustScreenElements();
    });

    /*Pending Integration
    $(".emotIcons").width(0);
    $(".emotIcons").hide();
    $("#emotToolBar").show();
    $("#emotToolBar").mousemove(function(e) {
        window.clearTimeout(etTimer);
    });
    $("#emotToolBar").mouseleave(function(e) {
        if($(".emotIcons").is(":visible"))
        {
            etTimer = window.setTimeout(hideEmotToolbar,3000);
        }
     });
	$(".openToolbar").click(function(e) {
		if($(this).hasClass("closeToolbar"))
			hideEmotToolbar();
		else
			showEmotToolbar();
    }); //end pending integration*/

	$(".groupNav").click(function(e) {
        var groupNo = $(this).attr("id").replace(/groupNav/g,"");
		if(!$(this).hasClass("current"))
		{
			allowed = 1;
			if(!$("#submitQuestion").is(":visible"))
			{
				$('#userResponse').val("-1");
			}
			else
			{
				$('#quesform').attr("disabled",false);
				var autoSaveAnswer = new Array();
                var equationEditorAnswer = new Array();
				$(".singleQuestion").each(function(index, element) {
					var singleQuestion = "";
                                               var singleQuestionEE = "";
					$(this).contents().find("select").each(function(){
						singleQuestion += $(this).val() + "|";
					})
					$(this).contents().find("input, iframe").each(function(){
						if($(this).hasClass("openEnded"))
						{
							singleQuestionEE += $(this)[0].contentWindow.storeAnswer('') + "@$*@";
							singleQuestionEE += $(this)[0].contentWindow.tools.save() + "@$*@";
						}
						else if($(this).attr("type") == "text")
						{
								singleQuestion += $(this).val() + "|";
						}
						else if($(this).attr("type") == "radio")
						{
							if($(this).is(":checked"))
							{
								singleQuestion += $(this).val() + "|";
							}
						}
					})
					if(singleQuestion != "")
						singleQuestion = singleQuestion.substring(0,singleQuestion.length-1);
                    if (singleQuestionEE != "")
                        singleQuestionEE = singleQuestionEE.substring(0, singleQuestionEE.length - 1);
                    autoSaveAnswer.push(singleQuestion);
                    equationEditorAnswer.push(removeInvalidChars(singleQuestionEE));
				});
				var autoSavedAnswer = autoSaveAnswer.join("##");
                var equationEditorAnswer = equationEditorAnswer.join('##');
                $('#userResponse').val(autoSavedAnswer);
                $('#eeResponse').val(equationEditorAnswer);
			}

			$("#mode").val("submitAnswer");
			$('#mode').val("fetchNCERTQuestion");
			$('#result').val(groupNo);
			$('#nextQuesLoaded').val("0");
			var params = $("#quesform").serialize();
			getNextQues(params,"normal");
			fetchNextQues();
		}
    });
	if($("input:text").length>0)
		$("input:text:first").focus();

    //Pending integration
	$(document).click(function(e){
		$(".moreOption").hide();
	});

	$('#emotToolBar *').click(function(e){
		e.stopPropagation();
	});
	$("input[name=moreOption]").click(function () {
		saveEmot($(this).val(),$(this));
	});

	$("input[name=emotRespond]").click(function(){
		$(".close").click();
		$(".hoverClass").removeClass("hoverClass");
			saveEmot($(this).val(),$(this));
	});

	//---hints
	$("#showHint").live('click',function(){
		if(objQuestion.quesType.substring(0, 3) == "MCQ" && objQuestion.noOfTrialsAllowed==1 && timeToAnswer<10)
		{
			alert("You seem to be using the hint very soon. Did you read the question completely?");
		}
		else
		{
			for (var h=1;h<objQuestion.hintAvailable;h++)
			{
				$("#timeTakenHints").val($("#timeTakenHints").val()+"||0");
			}
			startHintTimer();
			$("#showHint").hide();
			$("#isHintUsefull").attr("checked",false);
			$("#isHintUsefull").val("0");
			$(".hintDiv").fadeIn(1000);
			$("#hintUsed").val(1);
		}
	});

	$("#nextHint").live('click',function(){
		var totalHints	=	objQuestion.hintAvailable;
		for(var k=1;k<totalHints;k++)
		{
			if(document.getElementById("hintText"+k))
			{
				if($("#hintText"+k).is(":visible"))
				{
					timeTakenForAllHints	=	$("#timeTakenHints").val().split("||");
					timeTakenForAllHints[k-1] =	parseInt(timeTakenForAllHints[k-1]) + timeTakenToViewHint;
					timeTakenForAllHintStr	=	timeTakenForAllHints.join("||");
					$("#timeTakenHints").val(timeTakenForAllHintStr);
					stopHintTimer();
					startHintTimer();
					if(!$("#prevHint").is(":visible"))
						$("#prevHint").show();
					$("#hintText"+k).hide();
					$("#hintText"+(k+1)).show();
					if($("#hintUsed").val() < k+1)
						$("#hintUsed").val(k+1);
					if(k==totalHints-1)
						$("#nextHint").hide();
					break;
				}
			}
		}
	});

	$("#prevHint").live('click',function(){
		var totalHints	=	objQuestion.hintAvailable;
		for(var k=2;k<totalHints+1;k++)
		{
			if(document.getElementById("hintText"+k))
			{
				if($("#hintText"+k).is(":visible"))
				{
					timeTakenForAllHints	=	$("#timeTakenHints").val().split("||");
					timeTakenForAllHints[k-1] =	parseInt(timeTakenForAllHints[k-1]) + timeTakenToViewHint;
					timeTakenForAllHintStr	=	timeTakenForAllHints.join("||");
					$("#timeTakenHints").val(timeTakenForAllHintStr);
					stopHintTimer();
					startHintTimer();
					if(!$("#prevHint").is(":visible"))
						$("#prevHint").show();
					$("#hintText"+k).hide();
					$("#hintText"+(k-1)).show();
					if(k==2)
						$("#prevHint").hide();
					if(k==totalHints)
						$("#nextHint").show();
					break;
				}
			}
		}
	});
	
	$("#isHintUsefull").click(function(){
		if($("#isHintUsefull").is(":checked"))
			$("#isHintUsefull").val("1");
		else
			$("#isHintUsefull").val("0");
	});
	
	$(".commentOn").live("click",function(){
		if($(this).val()==2)
		{
			var selCategory	=	$("#selCategory").val();
			$("#selCategory").val("");
			$("#commentBox").hide();
			$("#commentOn1").attr("checked",true);
			//$("#commentInfo").html(prevQuesHtml);
			prevQuesClone.appendTo("#commentInfo");
			$("#commentInfo .markedCheck").remove();
			$("#commentInfo .markedCheck1").remove();
			$("#commentInfo .markedWrong").remove();
			$("#commentInfo #markedWrongTextTR").next("tr").remove();
			$("#commentInfo #markedWrongTextTR").remove();
			$("#commentInfo .markedRepeat").remove();
			$("#commentInfo #markedRepeatTextTR").next("tr").remove();
			$("#commentInfo #markedRepeatTextTR").remove();
			$("#commentInfo #mainHint").remove();
			$("#commentInfo #mainHint").next("br").remove();
			$("#commentInfo #submit_bar1").remove();
			$("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("Please click the Next Question button to continue.",""));
			$("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("<br>Please click the Next Question button to continue!!<br><br>",""));
			$("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("<br>",""));
			
			$("#commentInfo #feedback_header").css("font-size","1.2em");

			//$("#commentInfo #question").css("height", $("#q2").height()+$("#pnlOptions").height()+$("#pnlAnswer").height()+"px");
			/*$("#commentInfo #question").css("height",($("#commentInfo #question").height()-100)+"px");
			$("#commentInfo #pnlAnswer").css("height",($("#commentInfo #pnlAnswer").height()-100)+"px");*/

			$("#commentInfo").append("<div style='clear:both'></div>");


			$("#commentInfo #question .optionX").removeAttr("onclick");

			$("#commentBox").clone().appendTo("#commentInfo");
			$("#commentInfo #questionSelect").remove();
			if(selCategory!="")
				$("#commentInfo #selCategory").val(selCategory);
			$("#commentInfo #commentBox").show();
			$("#commentInfo #commentBoxTr").show();
			$("#commentInfo").dialog({
				width: "90%",
				height: "600",
				position: "center",
				draggable: false,
				resizable: false,
				modal: true
			});
			$(".ui-widget-header").css("background","#39A9E0");
			$(".ui-widget-content").css("background","#FFFFFF");
			$("#commentInfo #selCategory").css({"font-family":"Helvetica,Arial,sans-serif","font-size":"17px"});
			$("#commentInfo #comment").css({"font-family":"Helvetica,Arial,sans-serif","font-size":"17px"});
			$("#commentInfo").animate({ scrollTop: $(document).height() }, 1000);
		}
	});
	$("#selCategory").change(function(){
		$("#commentBoxTr").show();
	});

	$("#tagComentSave").click(function(){
		var msg	=	$("#tagComment").val();
		var qcodeTag	=	$("#tagQcode").val();
		if(msg=='')
		{
			alert("You can not tag a question without commenting.");
			document.getElementById("tagMsgBox").style.display = 'none';
			return false;
		}
		$.post("controller_dev.php","mode=tagThisQcode&qcode="+qcodeTag+"&msg="+msg+"&type="+$("#quesCategory").val(),function(data){
			if(data)
			{
				$("#tagtoModify"+qcodeTag).hide();
			}
		});
		$("#tagMsgBox").hide();
	});

    var objTemp = readCookie("mindspark");
    if(objTemp != null)
    {
        objTemp = JSON.parse(objTemp);
        objFrustration.F_current = objTemp.F_current;
        objFrustration.arrQuesResult = objTemp.arrQuesResult;
        objFrustration.F_prev = objTemp.F_prev;
        objFrustration.frustInst = objTemp.frustInst;
    }
});

function getNextQues(params,mode)
{
	$.ajax("controller_dev.php", {
		type: 'post',
		data: params,
		success:function process(transport,ajaxStatus){
			if (ajaxStatus != 0)
			{
				var response = transport;
				if (trim(response) == "DUPLICATE" || trim(response)=="SESSION_EXPIRED")
				{
					//alert("Duplicate Session!!");
					redirect = 0;
					window.open('', '_self', '');
    				window.close();
					return;
				}
				if (trim(response) == "DUPLICATE_QUESTION")
				{
					alert("Due to some error - either by pressing of an invalid key or some other reason - a question has been repeated.\n\nPlease log in again to continue your regular session.");
					
					redirect = 0;
					window.open('', '_self', '');
    				window.close();
					return;
				}
				if (trim(response) == "DUPLICATE_TAB")
				{
					alert("It seems mindspark is open in multiple tabs.Please re-login again.");
					redirect = 0;
					window.open('', '_self', '');
    				window.close();
					return;
				}
				
				if (response.indexOf("Mindspark | Error Page") >= 0)
				{
					window.open('', '_self', '');
    				window.close();
					return false;
				}

				try {
					var responseArray = $.parseJSON(response);
				}
				catch(err)
				{
					/*$.post("errorLog.php","params="+err+"request- "+$("#quesform").serialize()+" response-"+encodeURIComponent(response)+"&qcode=" + $("#qcode").val() +"&typeErrorLog=7",function(data) {
						
					});*/
				}
				if (mode == "normal")
				{
					try{
                        if (responseArray["tmpMode"] == "practice" || responseArray["tmpMode"] == "NCERT")
                        {
                            objNextQuestion = new Array();
                            var correctAnsArr = responseArray["correctAnswer"].split("##");
                            var ques_typeArr = responseArray["quesType"].split("##");
                            var dropDownAnsArr = responseArray["dropdownAns"].split("##");
                            var noOfBlanksArr = responseArray["noOfBlanks"].split("##");
                            var qcodeArr = responseArray["qcode"].toString().split("##");
                            var dynamicQuesArr = responseArray["dynamicQues"].split("##");
                            var eeIconArr = responseArray["eeIcon"].split('##'); //Equation Editor Icon Flag
                            /* Pending integration for options in case of MCQ */
                            for (var i = 0; i < qcodeArr.length; i++)
                                objNextQuestion.push(new QuestionObj({qcode: qcodeArr[i], clusterCode: responseArray["clusterCode"], noOfTrials: responseArray["noOfTrials"], hintAvailable: responseArray["hintAvailable"], quesType: ques_typeArr[i], correctAnswer: correctAnsArr[i], noOfBlanks: noOfBlanksArr[i], dropdownAns: dropDownAnsArr[i], dynamicQues: dynamicQuesArr[i], eeIcon: eeIconArr[i], quesVoiceOver:responseArray["quesVoiceOver"]}));
                        }
                        else
                        {
							try{
                            	objNextQuestion = new QuestionObj(responseArray);
							}
							catch(err)
							{
								/*$.post("errorLog.php","params="+err+" response-"+encodeURIComponent(response)+"&qcode=" + $("#qcode").val() +"&typeErrorLog=11",function(data) { 
								
								});*/
							}
							condition = responseArray["condition"].split("||");
							action = responseArray["action"].split("||");
							
							if(responseArray["noOfCondition"]!="")
								noOfCondition = responseArray["noOfCondition"];
							else
								noOfCondition = 0;
                        }
						document.getElementById("qcode").value = responseArray["qcode"]; //qcode
						document.getElementById("tmpMode").value = responseArray["tmpMode"]; //tmpMode
						document.getElementById("quesCategory").value = responseArray["quesCategory"];
						document.getElementById("showAnswer").value = responseArray["showAnswer"]; //showAnswer
						document.getElementById("quesType").value = responseArray["quesType"]; //quesType
						document.getElementById("clusterCode").value = responseArray["clusterCode"]; //clusterCode
						document.getElementById("hasExpln").value = responseArray["hasExpln"];
						document.getElementById("signature").value = responseArray["signature"];
						document.getElementById("validToken").value = responseArray["validToken"];

						Q1 = responseArray["Q1"];
						if(Q1!="")
							document.getElementById("qno").value = Q1; //qno
						Q2 = responseArray["Q2"]; //question text
						Q4 = responseArray["Q4"]; //option
						Q5 = responseArray["dispAns"];
						Q6 = responseArray["dispAnsA"];
						Q7 = responseArray["dispAnsB"];
						Q8 = responseArray["dispAnsC"];
						Q9 = responseArray["dispAnsD"];
						footerBar = responseArray["footer"];
						sparkie = responseArray["sparkie"];
						pnlCQ1 = responseArray["pnlCQ"];
						pnlWC1 = responseArray["pnlWC"];
						voiceover1 = responseArray["voiceover"];
						hint1 = responseArray["hint"];
						hintAvailable1 = responseArray["hintAvailable"];
						document.getElementById("dynamicQues").value = responseArray["dynamicQues"];
						document.getElementById("dynamicParams").value = responseArray["dynamicParams"];
						preload1 = responseArray["preloadDisplayAnswerImage"];
						checkImage(preload1);
						problemid1 = responseArray["problemid"];
						ano=1; //for conditional alert - attempt no.
						//document.getElementById("topicChangeMsg").innerHTML	=	responseArray["topicChangeMsg"]; //Pending integration - can be removed from controller and added in json
						showProgressDetails(responseArray["quesAttemptedInTopic"], responseArray["quesCorrectInTopic"], responseArray["progressInTopic"], responseArray["lowerLevel"]); //Pending integration
						document.getElementById("noOfTrialsAllowed").value = responseArray["noOfTrials"];
						totalAttempts = 10;
						document.getElementById("nextQuesLoaded").value = 1;
					}
					catch(err)
					{
						/*$.post("errorLog.php","params="+err+" response-"+encodeURIComponent(response)+"&qcode=" + $("#qcode").val() +"&typeErrorLog=8",function(data) { 
						
						});*/
						//alert("getNextQues " + err.description);
						document.getElementById('nextQuesLoaded').value = "-1";
					}
				}
			}
			else
			{
				alert("1.1: The internet connection has failed");
				document.getElementById('nextQuesLoaded').value = "-1";
			}
		},
		error: function() {
			alert("1.2: The internet connection has failed");
			document.getElementById('nextQuesLoaded').value = "-1";
		},
		complete: function() {
			window.status = 'Complete...';
		}
	}
	); //Ending Ajax Request
}
function fetchNextQues()
{
	
		$("#markedWrongText").text('').val('');
		$("#markedRepeatText").text('').val('');
		setformfieldsize(jQuery('#markedWrongText'), 250, 'status1');
		setformfieldsize(jQuery('#markedRepeatText'), 250, 'status2');
	
	
    slowLoadTimer = setTimeout("showSlowLoadingMsg()",30000);
	$("#markedCheck").attr("checked",false);
	$("#markedCheck1").attr("checked",false);
	$("#markedWrong").attr("checked",false);
	$("#markedRepeat").attr("checked",false);
	$(".markedWrong").css("display","none");
	$(".markedRepeat").css("display","none");
	$("#markedWrongTextTR").hide();
	$("#markedWrongText").text('').val(placeHolderText).removeClass("required");
	$("#markedRepeatTextTR").hide();
	$("#markedRepeatText").text('').val(placeHolderRepeatText).removeClass("required");
	$("#markedWrongText").removeClass("required");
	$("#markedRepeatText").removeClass("required");
	$(".markedCheck").removeClass("required");
	$(".markedCheck1").removeClass("required");
	$(".styledLable").removeClass("checked");
	
	
	
	stopHintTimer();
//--for comments
	if($("#q1").html()!="")
	{
		prevQuesClone	=	"";
		prevQcodeAfterNext	=	prevQcode;
		prevQuesCategoryAfterNext	=	prevQuesCategory;
		prevQuesHtml	=	$("#dlgAnswer").html();
		prevQuesClone	=	$("#question").clone();
		$("#commentOn2").attr('disabled', false);
	}
	else
	{
		$("#commentOn2").attr('disabled', true);
	}
//--for comments

	var params = $("#quesform").serialize();
	$('#quesform').attr("disabled",true);
	$('#pnlQuestion').css("display","none");
	$('#pnlAnswer').css("display","none");
	$('#displayanswer').html("");
	//document.getElementById('pnlButton').style.display = "none";    Pending integration - check if needed
	//if (document.getElementById('pnlCQ'))
		$('#questionType').css("display","none");
	/*if (document.getElementById('pnlWC'))
		document.getElementById('pnlWC').style.display = "none";*/

    $(".groupQues").hide(); // Added For Practice Cluster..
	$(".groupQues").empty(); // Added For Practice Cluster..
    hideSubmitBar();
	var infobarHeight = document.getElementById("info_bar").offsetHeight;
	var b= window.innerHeight -infobarHeight - 80 - 17;
	$('#pnlLoading').css({"display":"block","height":b});
	//$('#pnlLoading').css({"display":"block"});
	//return false;
	isNextQuesLoaded();
}
function isNextQuesLoaded()
{
	if ($('#nextQuesLoaded').val() == "0")
	    window.setTimeout("isNextQuesLoaded()", 500);
	else
	{
		if ($('#nextQuesLoaded').val() == "-1")
		{
			redirResult(9); //Ajax failure
		}
		else
		{
			$("#quesform").attr("disabled",false);
			//    $('#btnSubmit').attr("disabled",true);    Pending
			var code = $("#qcode").val();
			code = code.replace(/^\s*|\s*$/g, "");
			$('#refresh').val("0");
			if (code == "-2") {
				finalSubmit(code);                //Pass the code End of topic - failure
				return false;
			}
			else if (code == "-3") {
				finalSubmit(code);                //Pass the code End of topic - success
				return false;
			}
			else if (code == "-4") {
				alert("Error in finding minimum SDL");
				finalSubmit(code);
				//return false;
			}
			else if (code == "-5" || code == "-6" || code == "-7") {
				finalSubmit(code);
				return false;
			}
			else if (code == "-9") {
				finalSubmit(code);                //Pass the code End of topic - success
				return false;
			}
			else if(code == "-10"){
				showExerciseCompletion();
				return false;
			}
			else if(code == "-12"){
				//Create and submit form
				redirect = 0;
				var form = document.createElement("form");
				form.setAttribute("method", "post");
				form.setAttribute("action", "practiceClusterReport.php");

				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "clusterAttemptID");
				hiddenField.setAttribute("value", document.getElementById("tmpMode").value);
				form.appendChild(hiddenField);

				document.body.appendChild(form);
				form.submit();

			}
			else if(code == "-14"){
				if($(".groupNav").length == $(".complete").length)
				{
					alert("You have completed the exercise.")
					redirect = 0;
					window.location.href="homeworkSelection.php";
				}
				else
				{
					alert("You have some unanswered questions. Please submit all questions to complete the exercise.");
					$(".groupNav").each(function(index, element) {
                        if (!$(this).hasClass("complete"))
                        {
                            $(this).click();
                            return(false);
                        }
                    });
				}
			}
			else if(code == "-11"){
				redirect = 0;
				var dctCodeResponse = document.getElementById("tmpMode").value;
				var dctCodeResponseArray = dctCodeResponse.split("~");
				if(dctCodeResponseArray[0] == "DCT")
				{
					//Create and submit form
				    var form = document.createElement("form");
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "DCT.php");
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "redirect");
                    hiddenField.setAttribute("value", dctCodeResponseArray[1]);
                    form.appendChild(hiddenField);
                    document.body.appendChild(form);
                    form.submit();
				}
				else
				{
					var game = document.getElementById("tmpMode").value;
					var gameArray = game.split("~");
					var gameID = gameArray[0];
					var gameCode = gameArray[1];

					//Create and submit form
					var form = document.createElement("form");
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "enrichmentModule.php");

					var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "gameID");
                    hiddenField.setAttribute("value", gameID);
                    form.appendChild(hiddenField);

					if(typeof(gameArray[1]) != "undefined")
					{
						var hiddenField = document.createElement("input");
						hiddenField.setAttribute("type", "hidden");
						hiddenField.setAttribute("name", "gameCode");
						hiddenField.setAttribute("value", gameCode);
						form.appendChild(hiddenField);
					}

					var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "gameMode");
                    hiddenField.setAttribute("value", "DCTstage");
                    form.appendChild(hiddenField);

					document.body.appendChild(form);
    				form.submit();
				}
				return false;
			}
			else if(code== "-13")
			{
			    redirect=0;
				document.quesform.action='researchModule.php';
				document.quesform.submit();
			}
			else if (code == "-16") {
				finalSubmit(code);                //Completed exam corner cluster
				return false;
			}
			else {
				var tmpMode = $("#tmpMode").val().replace(/^\s*|\s*$/g, "");
				if(tmpMode=="commoninstruction")
				{
					redirect=0;
					document.quesform.action='group_instruction.php';
					document.quesform.submit();
				}
				else if (tmpMode == "timedtest")
				{
					redirect = 0;
					document.quesform.action = 'timedTest.php';
					document.quesform.submit();
				}
				else if (tmpMode == "diagnosticTest")
				{
					redirect = 0;
					document.quesform.action = 'diagnosticTest.php';
					document.quesform.submit();
				}
				else if (code == "-8") {    //Added after timed test to ensure timed test is given first before the class level completion message
				    showClassLevelCompletion();
				    return false;
			    }
				else if(code == "-12")
				{
					redirect = 0;
					document.getElementById('mode').value ="ttSelection";
					document.quesform.action = 'controller_dev.php?mode=ttSelection&completedPostTest=1';
					document.quesform.submit();
				}
				else if (tmpMode == "game")
				{
					redirect = 0;
					document.getElementById('mode').value = tmpMode;
					document.quesform.action = 'controller_dev.php';
					document.quesform.submit();
				}
				else if (tmpMode == "remedial")
				{
					redirect = 0;
					document.quesform.action = 'remedialItem.php';
					document.quesform.submit();
				}
				else
				{
					showNextQuestion();
				}
			}
		}
	}
}

function showNextQuestion()
{
        /* Pending Integration
	if($("#tagMsgBox").is(":visible"))
		$("#tagMsgBox").hide();*/
	if(document.getElementById('ichar'))
	{
		updateBuddy(2);
		$("#ichar").removeClass("buddyOpacity");
	}
	
	window.location.hash = 1;
	allowed=1;
    objQuestion = objNextQuestion;
	if(document.getElementById("tmpMode").value != "practice" && document.getElementById("tmpMode").value != "NCERT")
	{
		$("#q1").html(Q1);
        $('#lblQuestionNo').html(Q1);
        $('#lblQuestionNoCircle').html(Q1);
		//document.getElementById("q2").align = "left";
		$("#q2").html(Q2);
        if (objQuestion.quesType.substring(0, 3) == "MCQ")
        {
            $('#pnlOptions').css("display", "block");
            $('#pnlOptionTextA').html(objQuestion.optionA);
            $('#pnlOptionTextB').html(objQuestion.optionB);
            $('#optionC').css("display", "none");
            $('#optionD').css("display", "none");
			$('#optionC').removeClass("clear");
            if (objQuestion.quesType == "MCQ-3" || objQuestion.quesType == "MCQ-4")
            {
                $('#pnlOptionTextC').html(objQuestion.optionC);
                $('#optionC').css("display", "block");
                $('#optionD').css("display", "none");
				//if(window.innerWidth>1024){
					$('.option').css("width","30%");
				/*}
				else{
					$('.option').css("width","25%");
				}*/
				$('.optionText').css("width","78%");
            }
            if (objQuestion.quesType == "MCQ-4")
            {
                $('#pnlOptionTextD').html(objQuestion.optionD);
                $('#optionD').css("display", "block");
                $('#optionC').addClass("clear");
				$('.option').css("width","47%");
				$('.optionText').css("width","80%");
            }
			if(objQuestion.quesType == "MCQ-2")
			{
				$('.option').css("width","47%");
				$('.optionText').css("width","80%");
			}
        }
        else {
            $('#pnlOptions').css("display", "none");
        }

		if($.trim($("#noOfSparkie").text())!=$.trim(sparkie) && $.trim($("#noOfSparkie").text())!="0")
		{
			$("#noOfSparkie").removeAttr("style");
			animateSparkie("noOfSparkie");
		}
		if($.trim($("#sparkieInfo").text())!=$.trim(sparkie) && $.trim($("#noOfSparkie").text())!="0")
		{
			$(".bubble").removeAttr("style");
			animateSparkie("sparkieInfo");
		}
		if(sparkie=="")
		{
			$("#noOfSparkie").html("0");
			$("#sparkieInfo").html("0");
		}
		else
		{
				$("#noOfSparkie").html(sparkie);
				$("#sparkieInfo").html(sparkie);
				var d=sparkie+":";
				var a= d.split(":");
				var b= a[1].split("<");
				$(".redCircle").html(b[0]);
		}

		$("#msAsStudentInfo").html(footerBar);

		/*Pending integration
                   document.getElementById("q4").innerHTML = Q4;
		document.getElementById("footer1").innerHTML = footerBar;
		document.getElementById("sparkie1").innerHTML = sparkie;
		document.getElementById("pnlCQ").innerHTML = pnlCQ1;
		document.getElementById("pnlWC").innerHTML	=	pnlWC1;
		document.getElementById("voiceover").innerHTML = voiceover1; //end pending integration */
		if(voiceover1==1){
			document.getElementById("voiceover").innerHTML = "<a href=\"javascript:playme('Q')\"><img src=\"assets/play_btn.png\" border=0 height='50px'></a>";
			$("#quesStem").css("margin-top","-120px");
		}
		document.getElementById("hintAvailable").value = hintAvailable1;
		$("#quesVoiceOver").val(objQuestion.quesVoiceOver);
		$("#ansVoiceOver").val(objQuestion.ansVoiceOver);

		$("#hintUsed").val(0);
		$("#timeTakenHints").val("0");
		$("#userAllAnswers").val("");
		if(hint1!="")
		{
			var hintArr	=	 hint1.split("||");
			$("#hintText1").html("<b>"+i18n.t("questionPage.hint") + " 1 - </b>"+hintArr[0]);
			if(hintArr[1])
				$("#hintText2").html("<b>"+i18n.t("questionPage.hint") + " 2 - </b>"+hintArr[1]);
			else
				$("#bottomBtn").hide();
			if(hintArr[2])
				$("#hintText3").html("<b>"+i18n.t("questionPage.hint") + " 3 - </b>"+hintArr[2]);
			if(hintArr[3])
				$("#hintText4").html("<b>"+i18n.t("questionPage.hint") + " 4 - </b>"+hintArr[3]);
			if(objQuestion.noOfTrialsAllowed==1)
			{
				if(objQuestion.quesType.substring(0, 3) == "MCQ")
				{
					startAnswerTimer();
				}
				$("#mainHint,#showHint").show();
			}
		}
		document.getElementById('userResponse').value = "";
		document.getElementById('eeResponse').value = "";
		document.getElementById('noOfTrialsTaken').value = 0;
		$(".question_text").contents().find("#q1").show();
			var qType = objQuestion.quesType;
		if( !(qType=="MCQ-2" || qType=="MCQ-3" || qType=="MCQ-4"))
		{
			showSubmitButton();
		}
		else
		{
			showSubmitButton();
			$("#submitQuestion").hide();
			$("#submitQuestion2").hide();
			$("#mcqText").show();
		}

		$(".groupQues").empty();
		$(".extraPCtext").hide();
// Pending Integration
		if($("#quesCategory").val()=="normal")
			$("#emotToolBar").show();
		else
			$("#emotToolBar").hide();

		if(objQuestion.eeIcon == "1")
			$("#eqEditorToggler").show();
		else
			$("#eqEditorToggler").hide();
	}
    else
	{
		// Change the procedure Of showing Question For Practice Cluster..
		var loopCounter = 0;
		$("#eqEditorToggler").hide();
		if($("#tmpMode").val() == "practice")
		{
			$(".extraPCtext").show();
			$("#question").contents().find("#questionText").hide();
            $("#question").contents().find("#lblQuestionNoCircle").hide();
		}
		//$("#correctQuestionCount span").html(pnlCQ1); Check - not needed now
		$("#question").contents().find("#q2").html(Q5);
		$("#q4:first").empty();
		var noOfColumns = hint1;
		var qcodeArray = $("#qcode").val().split("##");
		$(".groupNav").removeClass("current");
		$("#groupNav"+Q6).addClass("current");
		if($("#tmpMode").val() == "NCERT")
		{
			var Q1Array = Q1.split("##");
			if(Q1Array.length == 1) //Hide group text and number in this case..
			{
				$("#question").contents().find("#lblQuestionNoCircle").hide();
				$("#questionTemplate").contents().find("#q1").removeClass("subQuestion").addClass("circle1");//Adding style of circled number..
			}
			else //Otherwise show 'em.. This required because it might have hidden in prev case..
			{
				$("#question").contents().find("#lblQuestionNoCircle").show();
				$("#question").contents().find("#lblQuestionNoCircle").html(Q6);
				$("#questionTemplate").contents().find("#q1").addClass("subQuestion").removeClass("circle1");
			}
		}
		var Q2Array = Q2.split("##");
		var Q4Array = Q4.split("##");
        $.each(objQuestion, function(index, value) {
            loopCounter++;
            Q4Array[index] = Q4Array[index].replace(/ansRadio/g, "ansRadio_" + objQuestion[index].qcode);
            if ($("#tmpMode").val() == "NCERT")
            {
                if (Q1Array.length == 1)
                {
                    $("#questionTemplate").contents().find("#q1").html(Q1Array[index]);
                }
                else
                {
                    $("#questionTemplate").contents().find("#q1").html("(" + Q1Array[index] + ")");
                }
            }
            else
                $("#questionTemplate").contents().find("#q1").html(Q1);
            $("#questionTemplate").contents().find("#q2").html(Q2Array[index]);
            if (objQuestion[index].eeIcon == "1")
                $("#questionTemplate").contents().find(".eqEditorToggler").show();
            else
                $("#questionTemplate").contents().find(".eqEditorToggler").hide();
            $("#questionTemplate").contents().find("#q4").html(Q4Array[index]);
            $(".groupQues").append($("#questionTemplate").html());
            if (loopCounter % parseInt(noOfColumns) === 0)
                $(".groupQues").append('<div style="clear:both;">');
            if ($("#tmpMode").val() != "NCERT")
                Q1 = parseInt(Q1) + 1;
        });
		$(".groupQues .singleQuestion").addClass("column"+noOfColumns);
  		$(".groupQues").append('<div style="clear:both;">'); //This should be added if noOfQuestions is not divisible by noOfColumn...

		// -------- Displaying Autosaved Answers ------------------------
		if($("#tmpMode").val() == "NCERT")
		{
			var Q7Arr = Q7.split("##");
			$(".correct,.wrong").removeClass("correct").removeClass("wrong");
			if(Q8 == 1)
			{
				resultArr = Q9.split("##");
				var answerToShow = voiceover1;
				var answerToShowArr = new Array();
				answerToShowArr = answerToShow.split("##");
			}
			$(".singleQuestion").each(function(index, element) {
				if(Q8 == 1)
				{
					var applyClass = (resultArr[index] == 1)?"correct":"wrong";
					if(resultArr[index] != 3) // For unreviewed open ended question..
						$(this).contents().find("td:first div").addClass(applyClass);
					if(answerToShowArr[index])
						$(this).append(i18n.t("questionPage.correctAnswer")+decrypt(answerToShowArr[index]));
				}
				if (Q7Arr[index] && $.trim(Q7Arr[index]) != "@$*@")
                {
                    var combinedRespArray = Q7Arr[index].split("[eeresponse]");
                    var autoSavedAns = combinedRespArray[0].split("|");
                    var equationEditorSavedAns = combinedRespArray[1].split("@$*@data");
                    var inputCounter = 0;
                    $(this).contents().find("select").each(function() {
                        $(this).find("option:contains('" + autoSavedAns[inputCounter] + "')").attr("selected", true);
                        inputCounter++;
                        if (Q8 == 1)
                            $(this).attr("disabled", "disabled");
                    });
                    $(this).contents().find("input,iframe").each(function() {
                        if ($(this).hasClass("openEnded"))
                        {
                            $(this).load(function() {
                                $(this)[0].contentWindow.editable.innerHTML = equationEditorSavedAns[0];
                                $(this)[0].contentWindow.restoreImage("data" + equationEditorSavedAns[1]);
                            });
                        }
                        if ($(this).attr("type") == "text")
                        {
                            $(this).val(autoSavedAns[inputCounter]);
                            inputCounter++;
                        }
                        else if ($(this).attr("type") == "radio")
                        {
                            if ($(this).val() == autoSavedAns[inputCounter])
                            {
                                $(this).attr('checked', true);
                                inputCounter++;
                            }
                        }
                        if (Q8 == 1)
                            $(this).attr("disabled", "disabled");
                    });
                }
			});
		}
		// -------- Displaying Autosaved Answers ------------------------

		$(".groupQues").show();
		if($("#tmpMode").val() != "NCERT")
			Q1 = parseInt(Q1) - 1;
		document.getElementById("qno").value = Q1;
		if($("#tmpMode").val() == "NCERT" && Q8 == 1)
			hideSubmitBar();
		else
			showSubmitButton();
        var idNo=1;
        $(".fracBox").each(function(index, element) {
            $(this).attr("id", "fracB_" + idNo);
            $(this).next("input").attr("id", "fracV_" + idNo);
            $(this).next().next("input").attr("id", "fracS_" + idNo);
            idNo++;
        });
	}
          //End pending integration */
	document.getElementById("problemid").value = problemid1;
	if (document.getElementById("subject"))
		document.getElementById("subject").value = problemid1;
	document.getElementById('pnlQuestion').style.display = "";
	document.getElementById('noOfTrialsTaken').value = 0;
	clearTimeout(slowLoadTimer);
	clearTimeout(logoffTimer);

	makeQuestionVisible();
	document.getElementById("nextQuesLoaded").value = 0;
	//document.getElementById('pnlButton').style.display = "block"; Pending integration, check if needed
	//if (document.getElementById('pnlCQ'))
    if ($("#quesCategory").val() == "challenge")
    {
        $('#questionType').css("display", "block");
        $("#QT").html(i18n.t("questionPage.CQ"));
	}
    //if (document.getElementById('pnlWC'))
    if ($("#quesCategory").val() == "wildcard")
    {
        $('#questionType').css("display", "block");
        $("#QT").html(i18n.t("questionPage.wildCard"));
    }

	if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "wildcard")
	{
		if(infoClick==1)
			$("#topic_name").hide();
		else
		{
			if($("#quesStem").css("margin-top")=="-80px")
			{
				$("#quesStem").css("margin-top","-30px");
			}
		}
	}
	else
	{
		$("#topic_name").show();
		if($("#quesStem").css("margin-top")=="-30px")
		{
			$("#quesStem").css("margin-top","-80px");
		}
	}

	$('#pnlLoading').css("display", "none");
	$('#result').val("");
	//loadbuddy(); //Pending integration - old buddy, if replaced with intelligent buddy, fun not needed
    adjustScreenElements();
    document.getElementById('questionText').scrollIntoView(1);


	$("#emotToolBar").contents().find("input:radio").removeAttr("disabled");
	$("#emotToolBar").contents().find("input:radio").removeAttr("checked");
	$("#emotToolBar").contents().find("label").removeClass("hoverClass");
	if(document.getElementById('quesType').value!="Blank" && document.getElementById('quesType').value!="D")
		showGlossary(document.getElementById("qcode").value,"");
}
function makeQuestionVisible()                //Code to set focus on first textbox
{
	if(document.getElementById('b1'))
		document.getElementById('b1').focus();
    disableSubmitButton();
	checkLoadingComplete();
	try
	{
		
		if ($('#quesVoiceOver').val() != '')
		{
			niftyplayer('niftyPlayer1').load($('#quesVoiceOver').val());
		}
		if ($('#ansVoiceOver').val() != '')
			niftyplayer('niftyPlayer1').load($('#ansVoiceOver').val());
	}
	catch(err){alert("error...!")}
	document.onselectstart = function () { return false; } // ie
	//logoffTimer = setTimeout('logoff()', 600000);        //log off if idle for 10 mins
	allowed=1;
	try{
	jsMath.Process(document);
	}catch(err){};
}
function submitAnswer()
{
	hideCommentBox();
	prevQcode = $("#qcode").val(); // For Recording previous QCode...
	prevQuesCategory = $("#quesCategory").val(); // For Recording previous Question Category...
	allowed = 0;
	try {
	if(document.getElementById("tmpMode").value != "practice" && document.getElementById("tmpMode").value != "NCERT")
	{
		disableSubmitButton();
        if (objQuestion.eeIcon == "1") {
            var eeLine = "";
			try {
				eeLine += $("iframe.openEnded")[0].contentWindow.storeAnswer('') + "@$*@";
				eeLine += $("iframe.openEnded")[0].contentWindow.tools.save();
			}
			catch(ex){
			}
            if (eeLine != "")
                $("#eeResponse").val(eeLine);
        }
		if (objQuestion.quesType == 'D')
		{
            var allDropDownsAnswered = 1;
			var objArray = document.getElementsByTagName("select");
			var ans = new Array();
			var ansByVal = new Array();
			var ansBlank = new Array();
			var realAnsBlank = new Array();
			for(var i=0; i<objArray.length; i++)
			{
				if(objArray[i].id.substr(0,6)=="lstOpt")
				{
					ans[objArray[i].id.substr(6)] = objArray[i].selectedIndex;
					ansByVal[objArray[i].id.substr(6)] = objArray[i].value;
                    if(objArray[i].value=="")
                        allDropDownsAnswered = 0;
				}
			}
            if (objQuestion.noOfBlanks > 0)
            {
                var fracboxCheck = new Array();
                var b = '', f = '';
                for (var j = 0; j < objQuestion.noOfBlanks; j++)
                {
                    fracboxCheck[j] = false;
                    var blankno = j + 1;
                    var objStr = 'b' + blankno;
                    if (document.getElementById(objStr))
                    {
                        if ($("#" + objStr).hasClass("customfrac"))
                        {
                            //f = document.getElementById('b' + blankno).value;
							b = document.getElementById('b' + blankno).value;
							//b = stripFrac(document.getElementById('b' + blankno).value);
							fracboxCheck[i] = true;
                        }
                        else
                            b = document.getElementById(objStr).value;
                    }
                    else
                    {
                        if ($('#fracB_' + blankno).hasClass('fracboxvalue'))
                        {
                            //b = $('#fracV_' + blankno).val();
							b = $("iframe#fracB_" + blankno)[0].contentWindow.getData();
							fracboxCheck[j]=true;
                        }
                        else
                        {
                            /*f = $('#fracV_' + blankno).val();
                            b = $('#fracS_' + blankno).val();*/
							b = $("iframe#fracB_" + blankno)[0].contentWindow.getData();
                        }
                    }
                    if (f == '')
                        realAnsBlank.push(b);
                    else
                        realAnsBlank.push(f);
                    ansBlank.push(b);
                }
            }
			var blankUserRealAns = "";
			var ddlUserAns = ans.join('|');
			var ddlUserAnsByVal = ansByVal.join('|');
			var blankUserAns = ansBlank.join('|');

			if(realAnsBlank.length !=0)
				var blankUserRealAns	=	realAnsBlank.join("|");
			else
				var blankUserRealAns	=	"";
			if(blankUserAns != "")
            {
                if (blankUserRealAns == "")
                    document.getElementById('userResponse').value = ddlUserAnsByVal + "|" + blankUserAns;
                else
                    document.getElementById('userResponse').value = ddlUserAnsByVal + "|" + blankUserRealAns;
            }
            else
                document.getElementById('userResponse').value = ddlUserAnsByVal;
            if (allDropDownsAnswered)
                var result = objQuestion.checkAnswerDropDown(ddlUserAns, blankUserAns.split("|"), fracboxCheck);
            else
                var result = 2;
			processAnswer(result, objQuestion.quesType);
		}
		else if (objQuestion.quesType== 'Blank')
		{
			var userAns = new Array();
			var userRealAns = new Array();
			var fracboxCheck = new Array();
			var	isNext = 2;
			for(var i=0;i<objQuestion.noOfBlanks;i++)
			{
				var b =  "";
                var f = "";
				fracboxCheck[i]=false;
				if(document.getElementById('b'+(i+1)+''))
                {
					if ($("#b" + (i + 1)).hasClass("customfrac"))
					{
						f = document.getElementById('b' + (i + 1)).value;
						b = stripFrac(document.getElementById('b' + (i + 1)).value);
						fracboxCheck[i] = true;
					}
					else
						b = document.getElementById('b' + (i + 1)).value;
				}
				else
				{
					if ($('#fracB_'+(i+1)).hasClass('fracboxvalue'))
					{
						//b	=	document.getElementById('fracV_'+(i+1)).value;
						b = $("iframe#fracB_"+(i+1))[0].contentWindow.getData();
						fracboxCheck[i]=true;
					}
					else
					{
						//b	=	document.getElementById('fracS_'+(i+1)).value;
						b = $("iframe#fracB_"+(i+1))[0].contentWindow.getData();
					}
					//f	=	document.getElementById('fracB_'+(i+1)).value;
				}
				if(f=="")
					userRealAns.push(b);
				else
					userRealAns.push(f);
				userAns.push(b);
			}
			var conditionCheck=0;
			var priority=0;
			var userAnswer = userAns.join("|");
			
			if(userRealAns.length !=0)
				var userRealResponse	=	userRealAns.join("|");
			else
				var userRealResponse	=	"";

            if(userRealResponse=="")
                document.getElementById('userResponse').value = userAnswer;
            else
                document.getElementById('userResponse').value = userRealResponse;
            var result = objQuestion.checkAnswerBlank(userAnswer.split("|"), fracboxCheck);
			
			if(noOfCondition>0) { //for conditional alert
				if(totalAttempts>0) {
				var result = objQuestion.checkAnswerBlank(userAnswer.split("|"), fracboxCheck);
				if(totalAttempts==0) {
					var result = objQuestion.checkAnswerBlank(userAnswer.split("|"), fracboxCheck);
					}
					for(var p = 1; p <= 3; p++) // looping through priorities of the conditions
	            	{
						var isNext = 0;
						conditionCheck=0;
						for(var i = 0; i< noOfCondition; i++)
					  	{
					      	parser.inputValues(userAnswer,ano);
					    	if(parser.parse(condition[i]) == 1)
					      	{
					      		action1 = action[i];
								if(/Mark Right/i.test(action1)){
									priority=1;
								}else if(/Mark Wrong/i.test(action1)){
									priority=2;
								}else{
									priority=3;
								}
								if(priority==p){
						      		if(action1 == "Mark Right")
						      		{
										result=1;
										processAnswer(result,objQuestion.quesType);
										isNext=1;
						      		}
						      		else if(action1 == "Mark Wrong")
						      		{
										result=0;
										processAnswer(result,objQuestion.quesType);
										isNext=1;
						      		}
						      		else if(action1.search(/Mark Right$/) != -1)
						      		{
							      		var str = action1.substring(7,action1.length-17);
						      			alert(str);
										result=1;
										processAnswer(result,objQuestion.quesType);
										isNext=1;
						      		}
						      		else if(action1.search(/Mark Wrong$/) != -1)
						      		{
						      			var str = action1.substring(7,action1.length-17);
						      			alert(str);
										result=0;
										processAnswer(result,objQuestion.quesType);
										isNext=1;
						      		}
						      		else
						      		{
						      			var str = action1.substring(7,action1.length-2);
						      			alert(str);
										isNext=1;
										break;
						      		}
								}
								allowCommonError = false;
				      		}
							else{
								conditionCheck++;
								if(conditionCheck==noOfCondition){
									conCheck=1;
								}
								if(conCheck==1){
									//processAnswer(result,objQuestion.quesType);
									isNext=2;
									break;
								}
							}
				      	}
						if(isNext==1){
							break;
						}
					}
					ano++;
					totalAttempts=totalAttempts-1;
				}
				else{
					isNext=2;
					//processAnswer(result,objQuestion.quesType);
				}
			}
			if((objQuestion.commonError.userAlert!="" && objQuestion.commonError.userAlert!=undefined) && previousCommonError!=objQuestion.commonError.errorCode && allowCommonError === true){
				alert(objQuestion.commonError.userAlert);
				isNext=2;
				previousCommonError=objQuestion.commonError.errorCode;
				objQuestion.commonError="";
			}
			else if(isNext==2) {
				processAnswer(result, objQuestion.quesType);
				previousCommonError="";
			}
		}
        else if(objQuestion.quesType!="I")
        {
			if(document.getElementById('userResponse').value=="")
			{
				stopAnswerTimer();
				timeToAnswer=0;
				$('.optionX').removeClass("optionActive");
                var userAns = "";
                if(arguments.length>0)
                    var userAns = arguments[0];
				markOption(userAns);
				document.getElementById('userResponse').value = userAns;
				processAnswer(objQuestion.checkAnswerMCQ(userAns), objQuestion.quesType);
			}
        }
		else
		{
			if(typeof $("#quesInteractive").attr("src")!="undefined")
			{
				var result = "";
				try {
					var frame = document.getElementById("quesInteractive");
					var win = frame.contentWindow;
					win.postMessage("checkAnswer",'*');
				}
				catch(ex){
					alert('error in getting the response from interactive');
				}
			}
			else
			{
				var flashMovie = getFlashMovieObject("simplemovieQ");
				var result = flashMovie.GetVariable("answer");
				processAnswer(result,objQuestion.quesType);
			}
		}
	}
	else
	{
		document.getElementById('pnlQuestion').scrollIntoView(1);
		results = evalGroupAnswers();
		var answerToShow = voiceover1;
		if(results != false)
		{
			if($("#tmpMode").val() == "NCERT")
			{
				$(".current").removeClass("pending").addClass("complete");
			}
			allowed = 1;
			var questionCount = 0;
			$('#quesform').attr("disabled",false);
			$(".singleQuestion").each(function(){
				$(this).contents().find("input, select, textarea").each(function(){
					$(this).attr("disabled","disabled");
				})
			})
			hideSubmitBar();
			var resultArr = new Array();
			resultArr = results.split("##");
			var answerToShowArr = new Array();
			answerToShowArr = answerToShow.split("##");
			$(".singleQuestion").each(function(){
				questionCount++;
				var applyClass = (resultArr[questionCount-1] == 1)?"correct":"wrong";
				if(resultArr[questionCount-1] != 3) // Dont show answer is correct or wrong in case of open ended..
				{
					$(this).contents().find("td:first div").addClass(applyClass);
					if(answerToShowArr[questionCount-1])
						$(this).append(i18n.t("questionPage.correctAnswer")+decrypt(answerToShowArr[questionCount-1]));
				}
			})
			/*$("#btnNextQues_bottom").show();
			$("#btnNextQues_bottom").removeAttr("disabled");*/
            showNextButton();
			$("#mode").val("submitAnswer");
			$('#secsTaken').val(secs);
			
			$("#result").val(results);
			
			var params = $("#quesform").serialize();
			getNextQues(params,"normal");
		}
	}
	}catch(err)
	{
		/*$.post("errorLog.php","params="+err+" - "+$("#quesform").serialize()+"&qcode=" + $("#qcode").val() + "&typeErrorLog=9",function(data) {
						
		});*/
	}
}

function processAnswer(result, quesType)
{
	if(document.getElementById("spnQuesCorrectEC") && (result==1 || result==0))
	{
		questionsDoneEC++;
		if(result==1)
			quesCorrectEC++;
		$("#spnQuesCorrectEC").text(quesCorrectEC);
		$("#spnQuestionsDoneEC").text(questionsDoneEC);
	}
	$('#result').val(result);
	
	if(result>2)
	{
		/*$.post("errorLog.php","params="+$("#quesform").serialize()+"&typeErrorLog=1",function(data) {
			
		});*/
	}
    
    if (result == 2) {
        alert(i18n.t("questionPage.answerNotGivenMsg"));
        enableSubmitButton();
        allowed = 1;
        $('#result').val("");
        return false;
    }
    else {
        if(result==0  && objQuestion.noOfTrialsAllowed > 1)
        {
			if(document.getElementById("spnQuesCorrectEC"))
				questionsDoneEC--;
			var noOfTrialsTaken = parseInt(document.getElementById('noOfTrialsTaken').value);
			noOfTrialsTaken++;
			
            if(noOfTrialsTaken < objQuestion.noOfTrialsAllowed)
            {
				document.getElementById('noOfTrialsTaken').value = noOfTrialsTaken;
				$("#userAllAnswers").val($("#userAllAnswers").val()+$('#userResponse').val()+"$#@");
                $('#result,#userResponse').val('');
				if(noOfCondition==0){
					alert(i18n.t("questionPage.reviewMsg"));
				}
				if(objQuestion.quesType.substring(0, 3) == "MCQ")
				{
					$('.optionX').addClass("optionInactive");
				}
                if(objQuestion.hintAvailable!=0)
				{
					if(!$("#mainHint").is(":visible"))
						$("#mainHint,#showHint").show(); //--show hints
					$("#userResponse").val("");
					$("#usefullHint").show();
				}
                enableSubmitButton();
                allowed = 1;
                return false;
            }
        }
		if($("#hintUsed").val() > 0)
		{
			var totalHints	=	objQuestion.hintAvailable;
			for(var f=1;f<=totalHints;f++)
			{
				if(document.getElementById("hintText"+f))
				{
					if($("#hintText"+f).is(":visible"))
					{
						timeTakenForAllHints	=	$("#timeTakenHints").val().split("||");
						timeTakenForAllHints[f-1] =	parseInt(timeTakenForAllHints[f-1]) + timeTakenToViewHint;
						timeTakenForAllHintStr	=	timeTakenForAllHints.join("||");
						$("#timeTakenHints").val(timeTakenForAllHintStr);
						stopHintTimer();
					}
				}
			}
		}
        $("#mainHint,.hintDiv,#showHint,#hintText2,#hintText3,#hintText4,#prevHint,#nextHint").attr("style","");
        $("#showHint,#usefullHint").hide();
        showAnswer(result, quesType, secs);
    }
    $('#secsTaken').val(secs);
    secs = 0;
    $('#refresh').val("1");
    $('#mode').val("submitAnswer");
    var eeResponseByUser = "";
    if($("iframe.openEnded").length > 0)
    {
    	eeResponseByUser += $("iframe.openEnded")[0].contentWindow.storeAnswer('') + "@$*@";
    	eeResponseByUser += $("iframe.openEnded")[0].contentWindow.tools.save();
    }
    else
    {
    	eeResponseByUser = "NO_EE"; // WHERE question does not contain equation editor, for this constant nothing will be saved..
    }
    $("#eeResponse").val(removeInvalidChars(eeResponseByUser));
    disableSubmitButton();
	if(ipadVersionCheck==true){
		$("#sideBar").css("height",$("#pnlQuestion").css("height"));	
	}
    var params = $("#quesform").serialize();
    if($("#qcode").val()=="")	//exception, temp work around
    {
    	$("#mode").val("nextAction");
    	document.quesform.action = 'controller_dev.php';
    	document.quesform.submit();
    }
    else
    	getNextQues(params,"normal");
}
function showAnswer(result, ques_type,  timeTaken)
{
	var msg;
	var frustrationMsg;
	var correctanswer = decrypt(objQuestion.encryptedCorrectAns);
	try{
            var optArray = new Array('A','B','C','D');
            if(document.getElementById('ichar') && ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "normal"))
            {	
                frustrationMsg = objFrustration.getFrustrationMsg(result, $("#quesCategory").val(), $('#qno').val(), timeTaken);
               //frustrationMsg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
                if(frustrationMsg!="")
                    logFrustrationMsgData(frustrationMsg, objFrustration.frustInst, objFrustration.F_current, $("#quesCategory").val(), $('#qno').val());
                var tmp = JSON.stringify(objFrustration);
                 createCookie("mindspark",tmp,0);
				 
            }
			niftyplayer('niftyPlayer1').stop();    //Replace with html5 vo
	}catch(err){}
	for(var j = 0; j < 7; j++)
	{
		if (document.getElementById('b'+j))
			document.getElementById('b'+j).disabled = true;
		if(document.getElementById('fracB_'+j))
		{
			jQuery('#fracB_'+j).removeAttr('contenteditable').attr("disabled","true");
		}
	}
	var dropDownObj = document.getElementsByTagName("select");
	for(var j = 0;j < dropDownObj.length; j++)
	{
		if (dropDownObj[j].id.substr(0,6) == "lstOpt")
			dropDownObj[j].disabled = true;
	}
	var explanation = decrypt(objQuestion.encryptedDispAns);
	if (result == 1)
	{
		if ($("#quesCategory").val() != "challenge" && $("#quesCategory").val() != "prepostTestQues" && $("#quesCategory").val() != "wildcard")
		{
		$(".markedCheck1").css("display","block");
		$(".markedCheck").css("display","none");
		}
		else
		{
			$(".markedCheck1").css("display","none");
		$(".markedCheck").css("display","none");
		}
		/*$(".markedWrong").css("display","none");
		$(".markedRepeat").css("display","none")*/;
		var msgArray = i18n.t("questionPage.correctMessages").split(",");
        var randomnumber = Math.floor(Math.random()*msgArray.length);
		msg = "";
		if ($("#quesCategory").val() == "challenge")
			msg += i18n.t("questionPage.CQCorrectMsg")+"<br/>";
		else if($("#quesCategory").val() == "wildcard")
		{
			if($("#childClass").val()<8)
				msg += i18n.t("questionPage.WildCardCorrectMsg")+"<br/>";
			else
				msg += i18n.t("questionPage.WildCardCorrectMsgReward")+"<br/>";
		}
		if ($('#hasExpln').val() == 1)
			msg += "<br/><b>" + i18n.t("questionPage.answerText")+ ": </b>" + explanation+"<br/><br/>";
        $("#feedback_header_img").removeClass("ques_incorrect ").addClass("ques_correct");
        $("#feedback_header").html(msgArray[randomnumber]);
		$("#feedbackContainer_correct").removeClass("feedbackContainer_incorrect");
		//$("#tl").html("<img src='images/right_smiley.png' style='vertical-align:middle'> " + msgArray[randomnumber] + "!");;
		//$("#hd").css("background-color","#00bc1b");
	}
	else if (result == 0)
	{
		if ($("#quesCategory").val() != "challenge" && $("#quesCategory").val() != "prepostTestQues" && $("#quesCategory").val() != "wildcard")
		{
			$(".markedCheck").css("display","block");
			$(".markedCheck1").css("display","none");
			
		
				/*$(".markedWrong").css("display","block");
				$(".markedRepeat").css("display","block");*/
		}
		else
		{
			$(".markedCheck1").css("display","none");
		$(".markedCheck").css("display","none");
		}
		
		if (objQuestion.ansVoiceOver != "")
		{
			playme('A');    //Pending integration - replace with html5 vo
		}
		var osda = ""; //option specific DA
		if (ques_type == 'MCQ-4' || ques_type == 'MCQ-3' || ques_type == 'MCQ-2')
		{
			tempResponse	=	$("#userResponse").val();
			var tmpAns =  i18n.t("questionPage.osdaText", { userResponse: tempResponse});
			if(tempResponse == "A" && objQuestion.encryptedDispAnsA != "")
				osda = decrypt(objQuestion.encryptedDispAnsA);
			else if(tempResponse == "B" && objQuestion.encryptedDispAnsB != "")
				osda = decrypt(objQuestion.encryptedDispAnsB);
			else if(tempResponse == "C" && objQuestion.encryptedDispAnsC != "")
				osda = decrypt(objQuestion.encryptedDispAnsC);
			else if(tempResponse == "D" && objQuestion.encryptedDispAnsD != "")
				osda = decrypt(objQuestion.encryptedDispAnsD);
			if(osda!="")
				osda = tmpAns + "("+i18n.t("questionPage.reason")+":"+ osda+")";
			else
				osda = tmpAns;
		}
		//------End
		msg = "";
			
		if ($("#quesCategory").val() == "challenge" && $('#showAnswer').val() == 0)
			msg += "<br/>"+ i18n.t("questionPage.CQIncorrectFirstAttemptMsg")+"<br/>";
		if ($("#quesCategory").val() == "normal" || $("#quesCategory").val() == "topicRevision" || $("#quesCategory").val() == "exercise" || $("#quesCategory").val() == "wildcard" || ($("#quesCategory").val() == "challenge" && $('#showAnswer').val() == 1))        //Show the display answer for normal questions only, not the challenge question.
		{
			if($("#quesCategory").val() == "wildcard")
			{
				if($("#childClass").val()<8)
					msg += "You missed a sparkie.<br>";
				else
					msg += "You missed 10 reward points.<br>";
			}
			msg += osda + "<br><br>";
			msg += i18n.t("questionPage.correctAnswer");
			if ((ques_type == 'MCQ-4' || ques_type == 'MCQ-3' || ques_type == 'MCQ-2') && trim(explanation) != correctanswer)
				msg += "<b>"+correctanswer + "</b>: ";
			msg += explanation;
		}
		msg += "<br><br>";
        $("#feedback_header_img").removeClass("ques_correct ").addClass("ques_incorrect");
        $("#feedback_header").html(i18n.t("questionPage.QuesIncorrectMsg"));
		$("#feedbackContainer_correct").addClass("feedbackContainer_incorrect");
		//$("#tl").html("<img src='images/wrong_smiley.png'  style='vertical-align:middle'> Sorry, that's incorrect!");
		//$("#hd").css("background-color","#CD5C5C");
		if (ques_type == 'D')
		{
			var objArray = document.getElementsByTagName("select");
			var ans = new Array();
			for(var i = 0; i < objArray.length; i++)
				ans[objArray[i].id.substr(6)] = objArray[i].selectedIndex;
			var correctanswer = decrypt(objQuestion.encryptedDropDownAns);
			correctanswer = correctanswer.split('|');
			for(var j = 0; j < correctanswer.length; j++)
				if (correctanswer[j] != ans[j] && document.getElementById('spnOpt'+j))
					document.getElementById('spnOpt'+j).style.color = 'red';
		}
	}
	msg += i18n.t("questionPage.nextQuesMsg")+"<br/><br/>";
	$("#displayanswer").html(msg);
	if(document.getElementById('ichar'))
	{
		$("#ichar").addClass("buddyOpacity");
		updateBuddy(result, frustrationMsg);
	}

	//$('#btnNextQues').show();
	//document.getElementById('pnlButton').scrollIntoView(1);
	document.getElementById('pnlAnswer').style.display = "none";
	try{
		jsMath.ProcessBeforeShowing(document.getElementById('displayanswer'));
	}catch(err){};
         showNextButton();
	/*$('#btnNextQues').disabled = false;
	document.getElementById('dlgAnswer').style.display = "block";
	var height = document.getElementById('dlgAnswer_inner').offsetHeight;
        document.getElementById('dlgAnswer').style.height = height + "px";
        document.getElementById('dlgAnswer').style.display="none";
	new VerticalSlide('dlgAnswer');*/
	//document.getElementById('btnSubmit').style.display = "none";
	animateAnswerBox();
	allowed = 1;
}
try {
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
	var frame = document.getElementById("quesInteractive");
	// Listen to message from child window
	eventer(messageEvent,function(e) {
		var response = "";
		  response = e.data;
		  
		if(response.indexOf("||") !=-1)
		{
			responseArray = response.split("||");
			result = parseInt(responseArray[0]);
			$('#userResponse').val(responseArray[1]);
			if (typeof(responseArray[3]) !== 'undefined') {
				var dispalAnswerParam = responseArray[3];
				var newSource = $('iframe:eq(1)').attr("src")+"?"+dispalAnswerParam;
				alert(newSource);
				$('iframe:eq(1)').attr("src",newSource);
			}
			if(result>2)
			{
				/*$.post("errorLog.php","params="+$("#quesform").serialize()+"&type=3",function(data) {
				
				});*/
			}
			processAnswer(result,objQuestion.quesType);
		}
		  /*responseArray = response.split("||");
		  result = parseInt(responseArray[0]);
		  $('#userResponse').val(responseArray[1]);
		  processAnswer(result,objQuestion.quesType);*/
	},false);
}
catch(ex){
	alert('error in getting the response from interactive');
}
// This Function Added For Practice Cluster..
// Evaluates all answers of questions in a group by its qustionType.(Only MCQ-2, MCQ-3,MCQ-4,Blank.) and Checks for Correctness..
function evalGroupAnswers()
{
    var questionCount = objQuestion.length;
    var fracboxcount;
    var requireAns = new Array();
    var returnStr = "";
    var userAns = new Array();
    var userRealAns = new Array();
    var userDdlAns = new Array();
    var userDdlAnsByVal = new Array();
    var userResponse = new Array();
    var eeResponse = new Array();
    var fracboxCheck = new Array();
    var quesNo = 0;
	$(".singleQuestion").each(function(){
		quesNo++;
        fracboxcount = 0;
		fracboxCheck[quesNo-1]	=	new Array();
		if(quesNo> questionCount)
			return;
		var line = "";
                var userLine =  "";
		var ddlLine = "";
		var ddlLineByVal = "";
                var eeLine = "";
		$(this).contents().find("select").each(function(){
		    ddlLine += $(this).prop("selectedIndex") + "|";
		    ddlLineByVal += $(this).val() + "|";
		});
		$(this).contents().find("input,iframe").each(function(){
			fracboxCheck[quesNo-1][fracboxcount] = false;
			if ($(this).hasClass("openEnded"))
            {
                eeLine += $(this)[0].contentWindow.storeAnswer('') + "@$*@";
                eeLine += $(this)[0].contentWindow.tools.save() + "@$*@";
            }
            else if ($(this).attr("type") == "text")
            {
                if ($(this).hasClass("customfrac"))
                {
                    line += stripFrac($(this).val()) + "|";
                    userLine += $(this).val() + "|";
                    fracboxCheck[quesNo - 1][fracboxcount] = true;
                    fracboxcount++;
                }
                else {
                    line += $(this).val() + "|";
                    userLine += $(this).val() + "|";
                }
            }
			else if ($(this).hasClass("fracBox"))
            {
                var txtbxID = $(this).attr("id");
                if ($('#' + txtbxID).hasClass('fracboxvalue')) {
                    line += $("iframe#" + txtbxID)[0].contentWindow.getData() + "|";
                    userLine += $("iframe#" + txtbxID)[0].contentWindow.getData() + "|";
					fracboxCheck[quesNo - 1][fracboxcount] = true;
                    fracboxcount++;
                }
                else
                {
                    line += $(this).val() + "|";
                    userLine += $("iframe#" + txtbxID)[0].contentWindow.getData() + "|";
                }
                if (txtbxID.split("_")[1])
                    jQuery('#fracB_' + txtbxID.split("_")[1]).removeAttr('contenteditable');
            }
			/*else if ($(this).attr("type") == "hidden")
            {
                var txtbxID = $(this).attr("id");
                if (txtbxID.split("_")[0] == "fracV" && $('#' + txtbxID).hasClass('fracboxvalue')) {
                    line += $(this).val() + "|";
                    userLine += $(this).val() + "|";
                }
                else if (txtbxID.split("_")[0] == "fracS" && !($('#fracV_' + txtbxID.split("_")[1]).hasClass('fracboxvalue')))
                {
                    line += $(this).val() + "|";
                    userLine += $('#fracV_' + txtbxID.split("_")[1]).val() + "|";
                    fracboxCheck[quesNo - 1][fracboxcount] = true;
                    fracboxcount++;
                }
                if (txtbxID.split("_")[1])
                    jQuery('#fracB_' + txtbxID.split("_")[1]).removeAttr('contenteditable');
            }*/
            else if ($(this).attr("type") == "radio")
            {
                if ($(this).is(":checked"))
                {
                    line += $(this).val() + "|";
                    userLine += $(this).val() + "|";
                }
            }
		});
		line = line.substring(0, line.length - 1);
        userLine = userLine.substring(0, userLine.length - 1);
        eeLine = eeLine.substring(0, eeLine.length - 1);
        ddlLine = ddlLine.substring(0, ddlLine.length - 1);
        ddlLineByVal = ddlLineByVal.substring(0, ddlLineByVal.length - 1);
        userAns.push(line);
        userRealAns.push(userLine);
        eeResponse.push(removeInvalidChars(eeLine));
		userDdlAns.push(ddlLine);
		userDdlAnsByVal.push(ddlLineByVal);
		return;
	});
	//questionCount--; //Needed since one div extra for template
	for (var k = 0; k < questionCount; k++)
    {
        if (objQuestion[k].quesType == 'D')
        {
            var result = objQuestion[k].checkAnswerDropDown(userDdlAns[k], userAns[k].split("|"), fracboxCheck[k]);
            var fullUserAnswer = userDdlAnsByVal[k];
            if (userRealAns[k] != "")
                fullUserAnswer += '|' + userRealAns[k];
        }
        else if (objQuestion[k].quesType == 'Open Ended')
        {
            var result = objQuestion[k].checkAnswerOpenEnded(eeResponse[k]);
        }
        else if (objQuestion[k].quesType == 'Blank')
        {
			var result = objQuestion[k].checkAnswerBlank(userAns[k].split("|"), fracboxCheck[k]);
			var fullUserAnswer = userRealAns[k];
        }
        else
        {
			var result = objQuestion[k].checkAnswerMCQ(userAns[k]);
			var fullUserAnswer = userAns[k];
        }
        if (result == 2)
            requireAns.push(k + 1);
        returnStr += result + "##";
        userResponse.push(fullUserAnswer);
    }
	if (requireAns == "")
    {
        var tempUserResponse = userResponse.join("##");
        $("#userResponse").val(tempUserResponse);
        $("#eeResponse").val(eeResponse.join("##"));
        return(returnStr);
    }
    else
    {
        var remainQues = requireAns.join(",");
        alert("Please complete the answer(s) for question(s) " + remainQues + ".");
        return false;
    }
}

function handleClose()
{
	qq=0;
	toolClick=1;
	//close toolbar if open
	if(toolClick==1)
		toolbar();
	//$('#question,#pnlAnswer').removeAttr("style");
	$('.optionX').removeClass("optionActive");

	$("#cc").attr("class","emotImage comment_new");
	$('.optionX').addClass("optionInactive");
	try {
		stop();    //Pending Integration - check what is this function
		if($("#result").val()=="")
			return;
	}catch(err)	{};
	// Pending Integration
	hideCommentBox();
	
	if($("#markedWrong").is(":checked") && ($.trim($("#markedWrongText").val()) == "" || $.trim($("#markedWrongText").val()) == placeHolderText) && ($("#markedRepeat").is(":checked") && ($.trim($("#markedRepeatText").val()) == "" || $.trim($("#markedRepeatText").val()) == placeHolderRepeatText)))
	{
		$("#markedRepeatText").addClass("required").focus();
		$("#markedRepeatText").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast");
		
	}
	
	if($("#markedWrong").is(":checked") && ($.trim($("#markedWrongText").val()) == "" || $.trim($("#markedWrongText").val()) == placeHolderText))
	{
		$("#markedWrongText").addClass("required").focus();
		$("#markedWrongText").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast");
		return false;
	}
	
		if($("#markedRepeat").is(":checked") && ($.trim($("#markedRepeatText").val()) == "" || $.trim($("#markedRepeatText").val()) == placeHolderRepeatText))
	{
		$("#markedRepeatText").addClass("required").focus();
		$("#markedRepeatText").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast");
		return false;
	}
	
	if($("#markedCheck").is(":checked") && !($("#markedRepeat").is(":checked") || $("#markedWrong").is(":checked")))
	{
		
		alert("Please specify your comment");
		$("#submitQuestion2").css("display","none");
		$('#submitQuestion2').css("display","none");
		return false;
	}
	
	if($("#markedCheck1").is(":checked") && !($("#markedRepeat").is(":checked") || $("#markedWrong").is(":checked")))
	{
		
		alert("Please specify your comment");
		$("#submitQuestion2").css("display","none");
		$('#submitQuestion2').css("display","none");
		return false;
	}
	
	window.status = '';
	plstart = new Date();
	stopTheTimer();
	var markedWrong = ($("#markedWrong").is(":checked"))?"yes":"no";
	var markedWrongText = $("#markedWrongText").val();
	var markedRepeat = ($("#markedRepeat").is(":checked"))?"yes":"no";
	var markedRepeatText = $("#markedRepeatText").val();

	updateTimeTakenForExpln(secs,markedWrong,markedWrongText,markedRepeat,markedRepeatText);
		
	if(markedWrong=="no" && markedRepeat=="no")
	{
		modifyDOMforPC();
		fetchNextQues();
		junkCommentNo=0;
	}
	try {
		niftyplayer('niftyPlayer1').stop();    //Pending integration - replace with html5 vo player
	}catch(err){};
	saveEmotByAjax(emotID);    //Pending integration
	emotID = "";
	//$('#btnNextQues').disabled = true;    //Pending integration - will go in class specific files
    
}
function updateTimeTakenForExpln(secs,markedWrong,markedWrongText,markedRepeat,markedRepeatText)
		
{
	if($("#submit_bar1").is(":visible"))
		$("#nextQuestion1").hide();
	else if($("#submit_bar").is(":visible"))
		$("#nextQuestion").hide();
	else if($("#menuBar").is(":visible"))
	{
		$("#nextQuestion2").hide();
		$("#submitQuestion2").hide();
	}
		
	var params = "mode=timeTakenForExpln";
	params += "&timeTaken="+secs;
	params += "&markedWrong="+markedWrong;
	params += "&markedWrongText="+markedWrongText;
	params += "&markedRepeat="+markedRepeat;
	params += "&markedRepeatText="+markedRepeatText;
	params += "&qcode="+prevQcode;
	params += "&question_type="+prevQuesCategory;
	params += "&quesCategory="+$('#quesCategory').val();
	params += "&junkCommentNo="+junkCommentNo;
	
	
	try {
		var request = $.ajax('controller_dev.php',
		{
			type: 'post',
			data: params,
			success: function(transport)
			{
				var trans = trim(transport);
				var trans_split = trans.split("**");
				
				if(markedRepeat=="yes")
				{
					if(trans_split[0]=="JUNK" && junkCommentNo=="0")
					{
						junkCommentNo++;
						alert("Mindspark could not understand your comment.\nPlease check again.");
						
						
						if(trans_split[1]=="BOTH")
						{
							$("#markedRepeatText").val("");
							$("#markedWrongText").val("");
							$("#markedRepeatText").focus();
						}
						
						;
						if(trans_split[1]=="REPEAT")
						{
							$("#markedRepeatText").val("");
							$("#markedRepeatText").focus();
						}
						
						
						if(trans_split[1]=="WRONG")
						{
							$("#markedWrongText").val("");
							$("#markedWrongText").focus();
						}
						
						
						if($("#submit_bar1").is(":visible"))
							$("#nextQuestion1").show();
						else if($("#submit_bar").is(":visible"))
							$("#nextQuestion").show();
						else if($("#menuBar").is(":visible"))
							$("#nextQuestion2").show();
						return false;
					}
					else
					{
						if(trans_split[0]=="JUNK" && junkCommentNo=="1")
							alert("Mindspark could not understand your comment.\nClick on OK to move to the next question.");
						junkCommentNo=0;
						modifyDOMforPC();
						fetchNextQues();
					}
				}
				
				else if(markedWrong=="yes")
				{
					if(trans_split[0]=="JUNK" && junkCommentNo=="0")
					{
						junkCommentNo++;
						alert("Mindspark could not understand your comment.\nPlease check again.");
						
						if(trans_split[1]=="WRONG")
						{
							$("#markedWrongText").val("");
							$("#markedWrongText").focus();
						}
						
						
						
						if(trans_split[1]=="REPEAT")
						{
							$("#markedRepeatText").val("");
							$("#markedRepeatText").focus();
						}
						
						
						$("#markedWrongText").val("");
						if($("#submit_bar1").is(":visible"))
							$("#nextQuestion1").show();
						else if($("#submit_bar").is(":visible"))
							$("#nextQuestion").show();
						else if($("#menuBar").is(":visible"))
							$("#nextQuestion2").show();
						return false;
					}
					else
					{
						if(trans_split[0]=="JUNK" && junkCommentNo=="1")
							alert("Mindspark could not understand your comment.\nClick on OK to move to the next question.");
						junkCommentNo=0;
						modifyDOMforPC();
						fetchNextQues();
					}
				}
				//do nothing;
			}
		}
		);
	}
	catch(err) {alert("updateTimeTakenForExpln " + err.description);}
}
function modifyDOMforPC()
{
	$(".singleQuestion").each(function(){
		$(this).contents().find(".correct").removeClass("correct");
		$(this).contents().find(".wrong").removeClass("wrong");
	})
	//$("#btnNextQues_bottom").hide();
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
// return true for 1234567890-./
function CheckIfNumeric(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode!= 46 && charCode!=47 && charCode!=45 && (charCode < 48 || charCode > 57)) {
        alert("Please enter only numbers.");
        return false;
    }
    return true;
}
function showProgressDetails(quesDone, quesCorrect, progress, lowerLevel)
{
	if(quesDone=="") quesDone=0;
	if(quesCorrect=="")	quesCorrect=0; 
	if(progress=="") progress=0; 
	if(lowerLevel=="") lowerLevel=0;
	var angle = 360*progress/100;
	$(".pie").css({'-webkit-transform':'rotate('+angle +'deg)','-moz-transform':'rotate('+angle +'deg)','-o-transform':'rotate('+angle +'deg)','transform':'rotate('+angle +'deg)','-ms-transform':'rotate('+angle +'deg)'});
	if(angle<180){
		$("#pieSlice2").attr("class","hold1");
		$("#pieSlice2").css({'-webkit-transform':'rotate('+(180-angle) +'deg)','-moz-transform':'rotate('+(180-angle) +'deg)','-o-transform':'rotate('+(180-angle) +'deg)','transform':'rotate('+(180-angle) +'deg)','-ms-transform':'rotate('+(180-angle) +'deg)'});
		$(".pie").css({'-webkit-transform':'rotate(180deg)','-moz-transform':'rotate(180deg)','-o-transform':'rotate(180deg)','transform':'rotate(180deg)','-ms-transform':'rotate(180deg)'});
	}
    $("#spnProgress").html(progress + "%");
	$("#spnProgress1").html(progress + "%");
    $("#spnQuestionsDone").html(quesDone);
    $("#spnQuesCorrect").html(quesCorrect);
	
	if(document.getElementById("green"))
		document.getElementById("green").style.width = progress + "%";
    /* Pending Integration
     if(lowerLevel=="0")
     $("#spnProgress").css("color","#0B8D34");
     else
     $("#spnProgress").css("color","red");*/
}

function finalSubmit(code)
{
	$('#mode').val(code);
	if (code == 1 || code == -5 || code == 6 || code == -6 || code == -7)        //i.e. 1 means End Session button clicked.
	{
		var params = "mode=endsession";
		params += "&code="+code;
		try {
			var request = $.ajax('controller_dev.php',
			{
				type:'post',
				data: params,
				success: function(transport)
				{
					resp = transport;
					redirResult(0);
				},
				error: function()
				{
					alert('Something went wrong...');
				}
			}
			);
		}
		catch(err) {alert("Final Submit " + err.description);}
	}
	else
		redirResult(0);
}
function redirResult(endType)
{
	window.location.href="";
	window.close();
}
function showSlowLoadingMsg()
{
	clearTimeout(slowLoadTimer);
	alert(i18n.t("questionPage.slowLoadingMsg"));
	redirResult(8); //Slow loading
}
function endtopic(){
	$("#endTopic").css("display","block");
}
function changeTopic()
{
	redirect = 0;
	$('#mode').val("topicSwitch");
	$('#quesform').attr("action","controller_dev.php");
	document.forms[0].submit();
}
function endsession(){
	$("#endSessionClick").css("display","block");
}
function quitTopic()
{
	redirect = 0;
	$('#mode').val("topicQuit");
	$('#quesform').attr("action","controller_dev.php");
	document.forms[0].submit();
}
function sendMail(params)
{
	try {
		var request = $.ajax('controller_dev.php',
		{
			type:'post',
			data: params,
			success: function(transport)
			{
				resp = transport;
				$('#comment').val("");
			}
		}
		);
	}
	catch(err){alert("sendMail "+err.description);}
}
function mailComment()
{
	if($("#commentInfo").is(":visible"))
	{
		if($("#commentInfo").find("#selCategory").val()=="")
		{
			alert("Please select category!");
			$('#commentInfo #selCategory').focus();
			return false;
		}
		else if($("#commentInfo").find("#txtcomment").val()=="")
		{
			alert("Please enter a comment!");
			$('#commentInfo #comment').focus();
			return false;
		}
		else
		{
			var params = "";
			params += "problemid=" + escape($('#problemid').val());
			params += "&comment=" + escape($("#commentInfo").find("#txtcomment").val());
			params += "&quesNo=" + escape($('#qno').val()-1);
			params += "&qcode=" + escape(prevQcodeAfterNext);
			params += "&selCategory=" + escape($("#commentInfo").find("#selCategory").val());
			params += "&type=" + escape(prevQuesCategoryAfterNext);
			params += "&previousQuestionDetails=";
			params += "&mode=comment";
			sendMail(params);
		}
	}
	else
	{
		if (trim($('#selCategory').val()) == "")
		{
			alert("Please select category!");
			$('#selCategory').focus();
			return false;
		}
		if (trim($('#txtcomment').val()) == "")
		{
			alert("Please enter a comment!");
			$('#txtcomment').focus();
			return false;
		}
		else
		{
			var params = "";
			params += "problemid=" + escape($('#problemid').val());
			params += "&comment=" + escape($('#txtcomment').val());
			params += "&quesNo=" + escape($('#qno').val());
			params += "&qcode=" + escape($('#qcode').val());
			params += "&selCategory=" + escape($('#selCategory').val());
			params += "&type=" + escape($("#quesCategory").val());
			if(prevQcodeAfterNext!="")
				params += "&previousQuestionDetails=" + escape(prevQcodeAfterNext+"~"+prevQuesCategoryAfterNext);
			else
				params += "&previousQuestionDetails=";
			if($(".commentOn:checked").val()==3)
				params += "&notRelatedToQuestion=1";
			params += "&mode=comment";
			sendMail(params);
		}
	}
	hideCommentBox();
	/* Pending Integration
     $("input[name=emotRespond]").removeAttr("checked");
	$("input[name=emotRespond]").parent().removeClass("hoverClass");
    End */
}
function hideCommentBox() {

	if($("#commentInfo").is(":visible"))
	{
		$("#wildcardInfo").click();
	}
	else
	{
		$('#txtcomment').val("");
		$('#selCategory').val("");
		$("#commentOn1").attr("checked",true);

		if (document.getElementById('result').value != "")
		{
			document.getElementById('pnlAnswer').style.display = "block";
		}
		document.getElementById('pnlQuestion').style.display = "block";
		$('#commentBox').css("display","none");
        /* Pending Integration
		$("input[name=emotRespond]").removeAttr("checked");
		$("input[name=emotRespond]").parent().removeClass("hoverClass");*/
	}
}
function trim(query)
{
	var s = query.replace(/\s+/g,"");
	return s.toUpperCase();
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
function stopTheTimer()
{
	if (timerRunning)
		clearTimeout(timerID);
	timerRunning = false;
}
function startTheTimer()
{
	secs = secs + 1;
	timerRunning = true;
	timerID = window.setTimeout("startTheTimer()", 1000);
}
function initializeTimer()
{
	secs = 0;
	disableSubmitButton();
	if (document.getElementById('btnTopicChange'))
		document.getElementById('btnTopicChange').disabled = false;
	startTheTimer();
}
function noimage(a)
{
	setTimeout(function(){
		counter++;
		if (counter >= 15)
		{
			/*$.post("errorLog.php","request- "+$("#quesform").serialize()+" imgName-"+a.src+"&qcode=" + $("#qcode").val() +"&typeErrorLog=imageNotLoading",function(data) 			{
							
			});*/
			// disable onerror to prevent endless loop
			a.onerror = "";
			counter = 0;
			
		}
		a.src = a.src + '?' + (new Date()).getTime();
	},100);
}
function checkImage(str)
{
	var pic = new Array();
	var col_array = str.split(",");
	var part_num = 0;
	while (part_num < col_array.length)
	{
		imgName = col_array[part_num];
		pic[part_num] = new Image();
		pic[part_num].src = col_array[part_num];
		part_num++;
	}
}
function checkKeyPress(e) {
	var keyPressed = e ? e.which : window.event.keyCode;
	if (keyPressed == 13)        //13 implies enter key
	{
		if(!$('#commentBox').is(":visible") && !$('#markedWrongText').is(":visible") && !$('#markedRepeatText').is(":visible"))
		{
			if ($('#result').val() == "")
			{
				if(objQuestion.quesType.substring(0, 3) == "MCQ")
				{
					alert("Please specify your answer!");
				}
				else
					submitAnswer();
			}
			else if($("#tmpMode").val() != "practice" && $("#tmpMode").val() != "NCERT"  && !$('#pnlLoading').is(":visible") )
			{
				if (allowed == 1)
				{
					$('#nextQuestion1').css("display","none");
					handleClose();
				}
			}
		}
	}
}

function my_onkeydown_handler(ev)
{
	var ev = ev || window.event;

	//Code for Key press event, include third party shortcut.js works in IE & FF - 06- oct 09
	shortcut.add("Ctrl+F5",function() {
		return false;
	});
	shortcut.add("Alt+LEFT",function() {
		return false;
	});
	shortcut.add("Alt+RIGHT",function() {
		return false;
	});
	shortcut.add("Ctrl+R",function() {
		return false;
	});
	shortcut.add("Backspace",function() {
		return;
	});
	shortcut.add("Alt+Backspace",function() {
		return false;
	});
	switch (ev.keyCode)
	{
		case 13: //enter
			redirect=0;
			break;
		case 116:
			if(document.getElementById('result').value!="")
			{
				cancelKey(ev);
				return false;
			}
	}
}
function cancelKey(evt) {
	if (evt.preventDefault) {
		evt.preventDefault();
		document.getElementById('mode').value = "refresh";
		redirResult();
		return false;
	}
	else {
		evt.keyCode = 0;
		evt.returnValue = false;
		document.getElementById('mode').value = "refresh";
		redirResult();
	}
}

function createCookie(name,value,days) {
    if (days) {
    	var date = new Date();
    	date.setTime(date.getTime()+(days*24*60*60*1000));
    	var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
    	var c = ca[i];
    	while (c.charAt(0)==' ') c = c.substring(1,c.length);
    	if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

shortcut = {
	'all_shortcuts':{},//All the shortcuts are stored in this array
	'add': function(shortcut_combination,callback,opt) {
		//Provide a set of default options
		var default_options = {
			'type':'keydown',
			'propagate':false,
			'disable_in_input':true,
			'target':document,
			'keycode':false
		}
		if(!opt) opt = default_options;
		else {
			for(var dfo in default_options) {
				if(typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
			}
		}

		var ele = opt.target;
		if(typeof opt.target == 'string') ele = document.getElementById(opt.target);
		var ths = this;
		shortcut_combination = shortcut_combination.toLowerCase();

		//The function to be called at keypress
		var func = function(e) {
			e = e || window.event;

			if(opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
				var element;
				if(e.target) element=e.target;
				else if(e.srcElement) element=e.srcElement;
				if(element.nodeType==3) element=element.parentNode;

				if(element.tagName == 'INPUT' && element.type == 'text' || element.tagName == 'TEXTAREA') return;
			}

			//Find Which key is pressed
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			var character = String.fromCharCode(code).toLowerCase();

			if(code == 188) character=","; //If the user presses , when the type is onkeydown
			if(code == 190) character="."; //If the user presses , when the type is onkeydown

			var keys = shortcut_combination.split("+");
			//Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
			var kp = 0;

			//Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
			var shift_nums = {
				"`":"~",
				"1":"!",
				"2":"@",
				"3":"#",
				"4":"$",
				"5":"%",
				"6":"^",
				"7":"&",
				"8":"*",
				"9":"(",
				"0":")",
				"-":"_",
				"=":"+",
				";":":",
				"'":"\"",
				",":"<",
				".":">",
				"/":"?",
				"\\":"|"
			}
			//Special Keys - and their codes
			var special_keys = {
				'esc':27,
				'escape':27,
				'tab':9,
				'space':32,
				'return':13,
				'enter':13,
				'backspace':8,

				'scrolllock':145,
				'scroll_lock':145,
				'scroll':145,
				'capslock':20,
				'caps_lock':20,
				'caps':20,
				'numlock':144,
				'num_lock':144,
				'num':144,

				'pause':19,
				'break':19,

				'insert':45,
				'home':36,
				'delete':46,
				'end':35,

				'pageup':33,
				'page_up':33,
				'pu':33,

				'pagedown':34,
				'page_down':34,
				'pd':34,

				'left':37,
				'up':38,
				'right':39,
				'down':40,

				'f1':112,
				'f2':113,
				'f3':114,
				'f4':115,
				'f5':116,
				'f6':117,
				'f7':118,
				'f8':119,
				'f9':120,
				'f10':121,
				'f11':122,
				'f12':123
			}

			var modifiers = {

				shift: { wanted:false, pressed:false},
				ctrl : { wanted:false, pressed:false},
				alt  : { wanted:false, pressed:false},
				meta : { wanted:false, pressed:false}	//Meta is Mac specific
			};

			if(e.ctrlKey)	modifiers.ctrl.pressed = true;
			if(e.shiftKey)	modifiers.shift.pressed = true;
			if(e.altKey)	modifiers.alt.pressed = true;
			if(e.metaKey)   modifiers.meta.pressed = true;

			for(var i=0; k=keys[i],i<keys.length; i++) {
				//Modifiers
				if(k == 'ctrl' || k == 'control') {
					kp++;
					modifiers.ctrl.wanted = true;
				} else if(k == 'shift') {
					kp++;
					modifiers.shift.wanted = true;
				} else if(k == 'alt') {
					kp++;
					modifiers.alt.wanted = true;
				} else if(k == 'meta') {
					kp++;
					modifiers.meta.wanted = true;
				} else if(k.length > 1) { //If it is a special key
					if(special_keys[k] == code) kp++;

				} else if(opt['keycode']) {
					if(opt['keycode'] == code) kp++;

				} else { //The special keys did not match
					if(character == k) kp++;
					else {
						if(shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
							character = shift_nums[character];
							if(character == k) kp++;
						}
					}
				}
			}

			if(kp == keys.length &&
						modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
						modifiers.shift.pressed == modifiers.shift.wanted &&
						modifiers.alt.pressed == modifiers.alt.wanted &&
						modifiers.meta.pressed == modifiers.meta.wanted) {
				callback(e);

				if(!opt['propagate']) { //Stop the event
					//e.cancelBubble is supported by IE - this will kill the bubbling process.
					e.cancelBubble = true;
					e.returnValue = false;
					//e.stopPropagation works in Firefox.
					if (e.stopPropagation) {
						e.stopPropagation();
						e.preventDefault();
					}
					return false;
				}
			}
		}
		this.all_shortcuts[shortcut_combination] = {
			'callback':func,
			'target':ele,
			'event': opt['type']
		};
		//Attach the function with the event
		if(ele.addEventListener) ele.addEventListener(opt['type'], func, false);
		else if(ele.attachEvent) ele.attachEvent('on'+opt['type'], func);
		else ele['on'+opt['type']] = func;
	}
}

function saveEmot(emot,ele)
{
	toolClick=1;
	toolbar();
	var emotArray = new Array();
	emotArray["Like"] = 1;
	emotArray["Dislike"] = 2;
	emotArray["I am loving it!"] = 3;
	emotArray["Great question!"] = 4;
	emotArray["Too easy."] = 5;
	emotArray["I hate this question."] = 6;
	emotArray["Questions are repeating."] = 7;
	emotArray["I didn't understand this question."] = 8;
	emotArray["No idea how to solve this."] = 9;
	emotArray["Question is confusing."] = 10;
	emotArray["Question is difficult."] = 11;
	emotArray["Excited"] = 12;
	emotArray["Confused"] = 13;
	emotArray["Bored"] = 14;
	/*if(emot == "Like" || emot == "Dislike" || emot == "Comment")
	{*/
		ele.parent().addClass("hoverClass");
		if(emot == "Comment")
		{
			//showCommentBox();
		}
	/*}*/
	if(emot != "Comment")
	{
		emotID = emotArray[emot];
	}
         window.clearTimeout(etTimer); //Prevent Event Bubble
	//etTimer = window.setTimeout(function (){hideEmotToolbar(); },1500);
}

function saveEmotByAjax(emotID)
{
	//hideEmotToolbar();
	if(emotID != "")
	{
		var qcode = prevQcode;
		$.post("controller_dev.php","qcode="+qcode+"&emotID="+emotID+"&mode=saveEmot",function(data){
			emoteToolbarTagCount++;
			if(emoteToolbarTagCount ==10)
			{
				$(".emotIcons").find("label").remove();
				$(".emotIcons").append('<h4 align="center">You have already tagged 10 questions!</h4>');
			}
		})
	}
	emotID = "";
}

function animateSparkie(id)
{
	if(id=="noOfSparkie")
		var newID	=	"#noOfSparkie";
	else
		var newID	=	".bubble";
	$(newID).animate({  borderSpacing: 360 }, {
		step: function(now,fx) {
		  $(this).css('-webkit-transform','rotate('+now+'deg)');
		  $(this).css('-moz-transform','rotate('+now+'deg)');
		  $(this).css('-ms-transform','rotate('+now+'deg)');
		  $(this).css('-o-transform','rotate('+now+'deg)');
		  $(this).css('transform','rotate('+now+'deg)');
		},
		duration:2500
	},'linear');
}
function showClassLevelCompletion()
{
	redirect = 0;
	document.quesform.action = 'classLevelCompletion.php';
	document.quesform.submit();
}
function clickIE()
{
	if (document.all)
	{
		window.status = message;
		return false;
	}
}
function clickNS(e)
{
    if(document.layers || (document.getElementById && !document.all))
    {
         if (e.which==2 || e.which==3)
         {
         	window.status = message;
         	return false;
         }
    }
}
function getFlashMovieObject(simplemovieQ)
{
	if (window.document.simplemovieQ)
		return window.document.simplemovieQ;
	if (navigator.appName.indexOf("Microsoft Internet") == -1)
	{
		if (window.document.controller.embeds && window.document.controller.embeds[simplemovieQ])
		{
			return window.document.controller.embeds[simplemovieQ];
		}
	}
	else
		return window.document.getElementById("simplemovieQ");
}
function quitHigherLevel()
{
	$("#higherLevelClick").show();
}
function showTagBox(id, visibility, qcode)
{
	document.getElementById('showTaggedQcode').innerHTML = "Need to modify qcode: "+qcode;
	document.getElementById("tagQcode").value	=	qcode;
	document.getElementById(id).style.display = visibility;
	document.getElementById("tagComment").value	=	'';
	document.getElementById("tagComment").focus();
}
function stopAnswerTimer()
{
	clearTimeout(timeToAnswerID);
	timeToAnswer=0;
}
function startAnswerTimer()
{
	timeToAnswer	=	parseInt(timeToAnswer + 1);
	timeToAnswerID = window.setTimeout("startAnswerTimer()", 1000);	
}
function stopHintTimer()
{
	clearTimeout(timeTakenToViewHintID);
	timeTakenToViewHint=0;
}
function startHintTimer()
{
	timeTakenToViewHint	=	parseInt(timeTakenToViewHint + 1);
	timeTakenToViewHintID = window.setTimeout("startHintTimer()", 1000);	
}

function playme(mode){
	try{
    	niftyplayer('niftyPlayer1').stop();
		var soundFile = "";
        if(mode=='Q')
            soundFile = document.getElementById('quesVoiceOver').value;
        else
            soundFile = document.getElementById('ansVoiceOver').value;
		niftyplayer('niftyPlayer1').loadAndPlay(soundFile);
    }catch(err){alert(err.desc);}
}

function getImportantQues()
{
	if(confirm("You will get only important questions"))
	{
		$("#report").remove();
		$("#impQuesMode").val("1");
		var qno = document.getElementById('qno').value;
		params="qcode=0&mode=firstQuestion&quesCategory="+category+"&qno="+qno+"&impQuesMode=1";
		getNextQues(params,"normal");
		fetchNextQues();
	}
}

function frustrationModel()
{
    this.arrQuesResult = Array(5); //(a[i])
    this.Feature1 = 0;
    this.Feature2 = 0;
    this.Feature3 = 0;
    this.Feature4 = 0;
    this.Feature5 = 0;
    this.msgCount = 0;
    this.avgResponseTime = 22;    //22 sec
    this.responseRate = 80;    //get it from the db - %correct for the current Question    (RR))
    this.F_current = 0;    //frustration Index;
    this.F_prev = 0;
    this.frustInst = 0;
    for (var i = 0; i < 5; i++)
        this.arrQuesResult[i] = "";
}
frustrationModel.prototype.getFrustrationMsg = function(result, quesType, qno, timeTaken)
{
    var msg = "";
    this.calculateFrustrationIndex(result, quesType, qno, timeTaken);
    this.msgCount = this.Feature1 + this.Feature2 + this.Feature3 + this.Feature4;
    if (this.F_current > 0.49)
    {
        this.frustInst++;
        if($("#childClass").val()<8)
            var str = "Sparkie";
        else
            var str = "Reward Point";
        if (this.msgCount == 0)
            msg = "";
        else if (this.msgCount == 1)
            msg = this.getMsgForEvent2(quesType, timeTaken);    //event E2
        else if (this.msgCount == 2)
            msg = this.getMsgForEvent3(timeTaken);    //event E3
        else if (this.msgCount == 3)
            msg = this.getMsgForEvent4(timeTaken,str);    //event E4
        else if (this.msgCount == 4)
            msg = this.getMsgForEvent5(timeTaken,str);    //event E5
        else if (this.msgCount == 5)
            msg = this.getMsgForEvent6(timeTaken);    //event E6
    }
    return msg;
}
frustrationModel.prototype.calculateFrustrationIndex = function(result, quesType, qno, timeTaken)
{
    this.F_prev = this.F_current;
    var index = 4;
    //alert(qno + " - " + index);
    var I = quesType == "challenge" ? 1 : 0;
    if (quesType == "normal")
    {
        for (var i = 1; i < 5; i++)
        {
            this.arrQuesResult[i - 1] = this.arrQuesResult[i];
        }
        this.arrQuesResult[i - 1] = result;
    }
    if (quesType == "normal" && qno <= 4)
        this.F_current = 0;
    else
    {
        this.Feature1 = (1 - this.arrQuesResult[index]) * (1 - I);
        this.Feature2 = ((this.arrQuesResult[index - 2] * this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index])) + this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index])) * (1 - I);
        this.Feature3 = ((this.arrQuesResult[index - 4] * this.arrQuesResult[index - 3] * this.arrQuesResult[index - 2] * this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index])) + (this.arrQuesResult[index - 3] * this.arrQuesResult[index - 2] * this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index]))) * (1 - I);

        this.Feature4 = I * (1 - result);
        this.Feature5 = timeTaken;
        this.F_current = 0.8 * (0.147 + 0.423 * (this.Feature1 - 0.25) - 0.0301 * (this.Feature2 - 0.25) / 2 + 0.0115 * (this.Feature3 - 0.11) / 2 + 0.8359 * (this.Feature4 - 0.04) + 0.1864 * (this.Feature5 - 22.5) / 300) + (0.2 * this.F_prev);
    }

};
frustrationModel.prototype.getMsgForEvent2 = function(quesType, timeTaken)
{
    var msg = "";
    if (quesType == "normal")
    {
        if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
            msg = "Good, you tried hard to get the correct answer. Keep trying.";
        else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
            msg = "Try harder, you might get the correct answer next time.";
        else if (this.frustInst == 2 && this.responseRate > 50)
            msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
        else if (this.frustInst == 2 && this.responseRate < 50)
            msg = "It seems this question is tough, your friends felt the same, try again";
        else if (this.frustInst == 3)
            msg = "Would you like to give your feedback?";
    }
    else if (quesType == "challenge")
    {
        if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
            msg = "You did well in the last four questions, and have tried hard to answer this challenge question too. Keep trying, you may solve it next time.";
        else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
            msg = "You did well in the last four questions. Try harder, you may solve it next time.";
        else if (this.frustInst == 2)
            msg = "Don't worry, this is a tough question for many of your friends too. You can attempt it again.";
        else if (this.frustInst == 3)
            msg = "Would you like to give your feedback?";
    }
    return msg;
};

frustrationModel.prototype.getMsgForEvent3 = function(timeTaken)
{
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last question, and have tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last question. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this question is tough, your friends felt the same, try again";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
frustrationModel.prototype.getMsgForEvent4 = function(timeTaken, str)
{
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last two questions, and have tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last two questions. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this is a tough question for many of your friends too. Try again. You may get a "+str+" next time.";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
frustrationModel.prototype.getMsgForEvent5 = function(timeTaken, str)
{
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last three questions and got a "+str+"! You tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last three questions. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this is a tough question for many of your friends too. Try again.";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
frustrationModel.prototype.getMsgForEvent6 = function(timeTaken)
{
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last four questions, and have tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last four questions. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this is a tough question for many of your friends too. Try again. You may get a Challenge Question next time.";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};