var toolClick=0;
var click=0;
var infoClick=0;
var secs,secsTaken = 0;
var plstart = new Date();
var timerID = null;
var timerRunning = false;
var timerActive = true;
var slowLoadTimer;
var logoffTimer,TimerID;
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
var redirect = 1;
var counter = 0;
var etTimer = null;
var glossaryDescArray = new Array();
var autoHideDisplayBar;
var emoteToolbarTagCount = 0;
minutes= 0;
seconds = 0;
var reviewed = 0;
var autoSaveTimer = 0;
var isTimerRunning=0;

var objQuestion, objNextQuestion;
//var objFrustration = new frustrationModel();
var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1;

if(document.addEventListener)
    document.addEventListener("keydown", my_onkeydown_handler,true);
else if (document.attachEvent)
    document.attachEvent("onkeydown", my_onkeydown_handler); 

window.history.forward(1);
document.onkeypress = checkKeyPress;
if (document.layers) document.captureEvents(Event.KEYPRESS);


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
  	window.location.hash.length = 1;
	var category	=	$('#quesCategory').val();
	var qcode		=	$('#qcode').val();
	var qno = document.getElementById('qno').value;
  	params="qcode="+qcode+"&mode=firstQuestion&quesCategory="+category+"&qno="+qno;
  	getNextQues(params,"normal");
	fetchNextQues();
  }
  else
  {
        var request = $.ajax('controller.php',
        {
            	type: 'post',
		data: "mode=back_refresh",
		success: function(transport)
		{
			//do nothing;
		}
	});
    	window.location = "error.php";
  }
  
	var timeLeft	=	$('#timeLeft').val();
	minutes	=	parseInt(timeLeft/60);
	seconds	=	timeLeft - minutes*60;
	timeTakenSecs	=	0;
	//StopTimer();
	
	StartTimer();
}

function refreshScrollBar()
{
    var pane = $('#scroll').jScrollPane({showArrows: true, arrowSize: 17, autoReinitialise: true}).data('jsp');
    pane.reinitialise();
}
$(document).ready(function(e) {
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
    $("#markedWrong").change(function(e) {
        $(".styledLable").toggleClass("checked");
        if($("#markedWrong").is(":checked"))
        {
		$("#markedWrongTextTR").show("slow",function(){
			$(this).focus();
		});
		$("#pnlAnswer").height($("#pnlAnswer").height()+135);//Textarea row height..
        }
        else
        {
		$("#markedWrongTextTR").hide();
		$("#pnlAnswer").height($("#pnlAnswer").height()-135);//Textarea row height..
        }
        //refreshScrollBar();
    });

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
							singleQuestionEE += $(this)[0].contentWindow.storeAnswer('') + "|";
							singleQuestionEE += $(this)[0].contentWindow.tools.save() + "|";
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

	//---hints
	$("#showHint").live('click',function(){
		$("#showHint").hide();
		$(".hintDiv").fadeIn(1000);
		$("#hintUsed").val(1);
	});

	$("#nextHint").live('click',function(){
		var totalHints	=	objQuestion.hintAvailable;
		for(var k=1;k<totalHints;k++)
		{
			if(document.getElementById("hintText"+k))
			{
				if($("#hintText"+k).is(":visible"))
				{
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

	$(".commentOn").live("click",function(){
		if($(this).val()==2)
		{
			var selCategory	=	$("#selCategory").val();
			$("#selCategory").val("");
			$("#commentBox").hide();
			$("#commentOn1").attr("checked",true);
			//$("#commentInfo").html(prevQuesHtml);
			prevQuesClone.appendTo("#commentInfo");
			$("#commentInfo .markedWrong").remove();
			$("#commentInfo #markedWrongTextTR").next("tr").remove();
			$("#commentInfo #markedWrongTextTR").remove();
			$("#commentInfo #mainHint").remove();
			$("#commentInfo #mainHint").next("br").remove();
			$("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("Please click the Next Question button to continue.",""));
			$("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("<br>Please click the Next Question button to continue!!<br><br>",""));
			$("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("<br>",""));

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
		$.post("controller.php","mode=tagThisQcode&qcode="+qcodeTag+"&msg="+msg+"&type="+$("#quesCategory").val(),function(data){
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
	if (params.search(/nextQuesLoaded=1/)!=-1)
	{
		return;
	}

	$.ajax("controller.php", {
		type: 'post',
		data: params,
		success:function process(transport,ajaxStatus){
			if (ajaxStatus != 0)
			{
				var response = transport;
				if(trim(response)=="")
				{
					reviewed=1;
					$("#reviewQuestions").val("1");
					reviewQuestion();
				}
				else if (trim(response) == "DUPLICATE" || trim(response)=="SESSION_EXPIRED")
				{
					//alert("Duplicate Session!!");
					redirect = 0;
					window.location = 'error.php';
					return;
				}
				else if (trim(response) == "DUPLICATE_QUESTION")
				{
					alert("Due to some error - either by pressing of an invalid key or some other reason - a question has been repeated.\n\nPlease log in again to continue your regular session.");
					redirect = 0;
					window.location = 'logout.php';
					return;
				}
				else if (mode == "normal")
				{
					var responseArray = $.parseJSON(response);

					try{
                        if (responseArray["tmpMode"] == "practice" || responseArray["tmpMode"] == "NCERT")
                        {
                            objNextQuestion = new Array();
                            var correctAnsArr = responseArray["correctAnswer"].split("##");
                            var ques_typeArr = responseArray["quesType"].split("##");
                            var dropDownAnsArr = responseArray["dropdownAns"].split("##");
                            var noOfBlanksArr = responseArray["noOfBlanks"].split("##");
                            var qcodeArr = responseArray["qcode"].split("##");
                            var dynamicQuesArr = responseArray["dynamicQues"].split("##");
                            var eeIconArr = responseArray["eeIcon"].split('##'); //Equation Editor Icon Flag
                            /* Pending integration for options in case of MCQ */
                            for (var i = 0; i < qcodeArr.length; i++)
                                objNextQuestion.push(new QuestionObj({qcode: qcodeArr[i], clusterCode: responseArray["clusterCode"], noOfTrials: responseArray["noOfTrials"], hintAvailable: hintAvailable, quesType: ques_typeArr[i], correctAnswer: correctAnsArr[i], noOfBlanks: noOfBlanksArr[i], dropdownAns: dropDownAnsArr[i], dynamicQues: dynamicQuesArr[i], eeIcon: eeIconArr[i]}));
                        }
                        else
                        {
                            objNextQuestion = new QuestionObj(responseArray);
                        }
						document.getElementById("qcode").value = responseArray["qcode"]; //qcode
						document.getElementById("tmpMode").value = responseArray["tmpMode"]; //tmpMode
						document.getElementById("quesCategory").value = responseArray["quesCategory"];
						document.getElementById("showAnswer").value = responseArray["showAnswer"]; //showAnswer
						document.getElementById("quesType").value = responseArray["quesType"]; //quesType
						document.getElementById("clusterCode").value = responseArray["clusterCode"]; //clusterCode
						document.getElementById("hasExpln").value = responseArray["hasExpln"];
						document.getElementById("signature").value = responseArray["signature"];

						Q1 = responseArray["Q1"];;
						if(Q1!="")
						{
							document.getElementById("qno").value = Q1; //qno
							$("#curQuesNumber").text(Q1);
						}
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
						//document.getElementById("topicChangeMsg").innerHTML	=	responseArray["topicChangeMsg"]; //Pending integration - can be removed from controller and added in json
						document.getElementById("noOfTrialsAllowed").value = responseArray["noOfTrials"];
						document.getElementById("nextQuesLoaded").value = 1;
						if(parseInt(Q1) > parseInt($("#totalQues").val()/2))
						{
							$("#reviewQuestion,#reviewArrow").css("visibility","visible");
						}
					}
					catch(err)
					{
						alert("getNextQues " + err.description);
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
    //slowLoadTimer = setTimeout("showSlowLoadingMsg()",30000);
	$("#markedWrong").attr("checked",false);
	$("#markedWrongTextTR").hide();
	$("#markedWrongText").text('').val(placeHolderText).removeClass("required");
	$(".styledLable").removeClass("checked");

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
	$('#questionType').css("display","none");

    $(".groupQues").hide(); // Added For Practice Cluster..
	$(".groupQues").empty(); // Added For Practice Cluster..
    hideSubmitBar();
	var infobarHeight = document.getElementById("info_bar").offsetHeight;
	var b= window.innerHeight -infobarHeight - 80 - 17;
	$('#pnlLoading').css({"display":"block","height":b});
	clearTimeout(TimerID);
	isTimerRunning=0;
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
					document.quesform.action = 'controller.php?mode=ttSelection&completedPostTest=1';
					document.quesform.submit();
				}
				else if (tmpMode == "game")
				{
					redirect = 0;
					document.getElementById('mode').value = tmpMode;
					document.quesform.action = 'controller.php';
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
				$('.option').css("width","30%");
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
		document.getElementById("hintAvailable").value = hintAvailable1;
                document.getElementById("quesVoiceOver").value = objQuestion.quesVoiceOver;
		document.getElementById("ansVoiceOver").value = objQuestion.ansVoiceOver;
		$("#hintUsed").val(0);
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
				$("#mainHint,#showHint").show();
		}
		document.getElementById('userResponse').value = "";
		document.getElementById('eeResponse').value = "";
		document.getElementById('noOfTrialsTaken').value = 0;
		$(".question_text").contents().find("#q1").show();
			var qType = objQuestion.quesType;
		if( !(qType=="MCQ-2" || qType=="MCQ-3" || qType=="MCQ-4"))
		{
			showSubmitButton();
			$("#mcqText").hide();
		}
		else
		{
			showSubmitButton();
			/*$("#submitQuestion").hide();
			$("#submitQuestion2").hide();*/
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
				if (Q7Arr[index] && $.trim(Q7Arr[index]) != "")
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
		if($("#quesStem").css("margin-top")=="-40px")
		{
			$("#quesStem").css("margin-top","-30px");
		}
	}

	$('#pnlLoading').css("display", "none");
	if(isTimerRunning==0)
		StartTimer();
	$('#result').val("");
	//loadbuddy(); //Pending integration - old buddy, if replaced with intelligent buddy, fun not needed
    adjustScreenElements();
    document.getElementById('questionText').scrollIntoView(1);


	$("#emotToolBar").contents().find("input:radio").removeAttr("disabled");
	$("#emotToolBar").contents().find("input:radio").removeAttr("checked");
	$("#emotToolBar").contents().find("label").removeClass("hoverClass");
	if(document.getElementById('quesType').value!="Blank" && document.getElementById('quesType').value!="D")
		showGlossary(document.getElementById("qcode").value,"");
	$.post("controller.php","mode=sbaTestAnswer&qcode="+$("#qcode").val()+"&qno="+Q1,function(userPrevAns){
			userPrevAns	=	$.trim(userPrevAns);
			if($("#quesType").val()=="Blank")
			{
				tempAnsArr	=	userPrevAns.split("|");
				for(var n=0;n<tempAnsArr.length;n++)
				{
					$('#b'+(n+1)).val(tempAnsArr[n]);
				}
			}
			else
			{
				markOption(userPrevAns);
				$(".optionX").addClass("optionInactive");
			}
		});
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
			niftyplayer('niftyPlayer1').load($('#quesVoiceOver').val());
		if ($('#ansVoiceOver').val() != '')
			niftyplayer('niftyPlayer1').load($('#ansVoiceOver').val());
	}
	catch(err){}
	document.onselectstart = function () { return false; } // ie
	logoffTimer = setTimeout('logoff()', 600000);        //log off if idle for 10 mins
	allowed=1;
	try{
	jsMath.Process(document);
	}catch(err){};
}
function submitAnswer()
{
	hideCommentBox(); //Pending integration
	prevQcode = $("#qcode").val(); // For Recording previous QCode...
	prevQuesCategory = $("#quesCategory").val(); // For Recording previous Question Category...
	allowed = 0;
	/*try {*/
	if(document.getElementById("tmpMode").value != "practice" && document.getElementById("tmpMode").value != "NCERT")
	{
		disableSubmitButton();
        if (objQuestion.eeIcon == "1") {
            var eeLine = "";
			try {
				eeLine += $("iframe.openEnded")[0].contentWindow.storeAnswer('') + "|";
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
                            f = document.getElementById(objStr).value;
                            b = stripFrac(document.getElementById(objStr).value);
                            fracboxCheck[j] = true;
                        }
                        else
                            b = document.getElementById(objStr).value;
                    }
                    else
                    {
                        if ($('#fracV_' + blankno).hasClass('fracboxvalue'))
                        {
                            b = $('#fracV_' + blankno).val();
                        }
                        else
                        {
                            f = $('#fracV_' + blankno).val();
                            b = $('#fracS_' + blankno).val();
                            fracboxCheck[j] = true;
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
					if ($('#fracV_'+(i+1)).hasClass('fracboxvalue'))
						b	=	document.getElementById('fracV_'+(i+1)).value;
					else
					{
						b	=	document.getElementById('fracS_'+(i+1)).value;
						fracboxCheck[i]=true;
					}
					f	=	document.getElementById('fracV_'+(i+1)).value;
				}
				if(f=="")
					userRealAns.push(b);
				else
					userRealAns.push(f);
				userAns.push(b);
			}
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
			processAnswer(result, objQuestion.quesType);
		}
        else if(objQuestion.quesType!="I")
        {
			if(document.getElementById('userResponse').value=="")
			{
				if(reviewed==1)
				{
					$("#reviewQuestions").val("1");
				}
				$('.optionX').removeClass("optionActive");
                var userAns = "";
                if(arguments.length>0)
				{
                    var userAns = arguments[0];
					markOption(userAns);
					document.getElementById('userResponse').value = userAns;
					processAnswer(objQuestion.checkAnswerMCQ(userAns), objQuestion.quesType);
				}
				else
				{
					result=2;
					processAnswer(result, objQuestion.quesType);
				}
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
}

function processAnswer(result, quesType)
{
	if(trim($("#userResponse").val())=="")
		result='';
	else if(result!=2)
	{
		$("#ques_"+$("#qcode").val()).removeClass("notAttemptedTd");
		$("#ques_"+$("#qcode").val()).addClass("attemptedTd");
		$("#ques_"+$("#qcode").val()).text("Answered");
	}
    $('#result').val(result);
    showAnswer(result, quesType, secs);
	
    $('#secsTaken').val(secs);
    secs = 0;
    $('#refresh').val("1");
    $('#mode').val("submitAnswer");
    var eeResponseByUser = "";
    if($("iframe.openEnded").length > 0)
    {
    	eeResponseByUser += $("iframe.openEnded")[0].contentWindow.storeAnswer('') + "|";
    	eeResponseByUser += $("iframe.openEnded")[0].contentWindow.tools.save();
    }
    else
    {
    	eeResponseByUser = "NO_EE"; // WHERE question does not contain equation editor, for this constant nothing will be saved..
    }
    $("#eeResponse").val(removeInvalidChars(eeResponseByUser));
    disableSubmitButton();
    var params = $("#quesform").serialize();
    if($("#qcode").val()=="")	//exception, temp work around
    {
    	document.quesform.action = 'home.php';
    	document.quesform.submit();
    }
    else
    	getNextQues(params,"normal");
}

function showAnswer(result, ques_type,  timeTaken)
{
	handleClose();
	allowed = 1;
}

try {
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
	var frame = document.getElementById("quesInteractive");
	// Listen to message from child window
	eventer(messageEvent,function(e) {
		  response = e.data;
		  responseArray = response.split("||");
		  result = parseInt(responseArray[0]);
		  $('#userResponse').val(responseArray[1]);
		  processAnswer(result,objQuestion.quesType);
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
                eeLine += $(this)[0].contentWindow.storeAnswer('') + "|";
                eeLine += $(this)[0].contentWindow.tools.save() + "|";
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
			else if ($(this).attr("type") == "hidden")
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
            }
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
	
	window.status = '';
	plstart = new Date();
	stopTheTimer();
    modifyDOMforPC();
	fetchNextQues();
}
function updateTimeTakenForExpln(secs,markedWrong,markedWrongText)
{
	var params = "mode=timeTakenForExpln";
	params += "&timeTaken="+secs;
	params += "&markedWrong="+markedWrong;
	params += "&markedWrongText="+markedWrongText;
	params += "&qcode="+prevQcode;
	params += "&question_type="+prevQuesCategory;
	try {
		var request = $.ajax('controller.php',
		{
			type: 'post',
			data: params,
			success: function(transport)
			{
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

function redirResult(endType)
{
	redirect = 0;
	document.quesform.action = 'error.php';
	document.quesform.submit();
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
	$('#quesform').attr("action","controller.php");
	document.forms[0].submit();
}
function endsession(){
	$("#endSessionClick").css("display","block");
}
function quitTopic()
{
	redirect = 0;
	$('#mode').val("topicQuit");
	$('#quesform').attr("action","controller.php");
	document.forms[0].submit();
}
function sendMail(params)
{
	try {
		var request = $.ajax('controller.php',
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
	if(timerRunning)
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
	counter++;
	if (counter >= 15)
	{
		// disable onerror to prevent endless loop
		a.onerror = "";
		counter = 0;
	}
	a.src = a.src + '?' + (new Date()).getTime();
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
		if(!$('#reviewDiv').is(":visible"))
		{
			if ($('#result').val() == "")
				submitAnswer();
			else if($("#tmpMode").val() != "practice" && $("#tmpMode").val() != "NCERT"  && !$('pnlLoading').is(":visible") )
			{
				if (allowed == 1)
					handleClose();
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
		$.post("controller.php","qcode="+qcode+"&emotID="+emotID+"&mode=saveEmot",function(data){
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

function StartTimer()
{
	isTimerRunning=1;
    if(minutes==0 && seconds==0)
	{
       StopTimer();
	   reviewed=1;
	   reviewQuestion();
	   completeTest();
	}
    else
    {
        TimerID = setTimeout("StartTimer()",1000);
    }
	$('#timeLeftDisp').html(Pad(minutes)+":"+Pad(seconds));
    if(seconds==0)
    {
       minutes--;
       seconds=60;
    }
    seconds--;
	autoSaveTimer++;
	if(autoSaveTimer==20)
	{
		autoSaveTimer = 0;
		$.post("controller.php","mode=timeTakenSbaTest&totalTime="+$("#totalTimeAllowed").val()+"&timeLeft="+$("#timeLeft").val(),function(response){
			
		});
	}
	$('#timeLeft').val(minutes*60 + seconds);
}

function StopTimer()
{
    if(timerActive)
       clearTimeout(TimerID);
    timerActive=false;
}

function Pad(number) //pads the mins/secs with a 0 if its less than 10
{
    if(number<10)
       number=0+""+number;
    return number;
}

function displayQuestion(qno,qcode)
{
	if(timerActive)
	{
		$("#reviewDiv,#completeTest").hide();
		var category	=	$('#quesCategory').val();
		params="qcode="+qcode+"&mode=firstQuestion&quesCategory="+category+"&qno="+qno;
		getNextQues(params,"normal");
		fetchNextQues();	
		$("#question,#reviewQuestion").show();
		$("#reviewArrow").css("visibility","visible");
	}
	else
	{
		alert("Time over");
	}
}

function reviewQuestion()
{
	if(reviewed==0)
	{
		if(confirm("You should click on 'Review Test' only if you have completed or almost completed the test. Once you reach the 'Review Test' screen, you can review questions including the ones you have skipped.\nIf you have many questions left to answer, it is better to click 'Cancel' below and answer those questions first."))
		{
			reviewed=1;
			$("#reviewQuestions").val("1");
			submitAnswer();
		}
	}
	else
	{
		if($("#reviewQuestions").val()=="0")
		{
			$("#reviewQuestions").val("1");
			submitAnswer();
		}
		else
		{
			$("#reviewQuestions").val("0");
			$("#reviewDiv,#completeTest,#pnlQuestion,#submit_bar,#submit_bar1,#submitArrow").show();
			$("#question,#reviewQuestion,#submitQuestion,#submitQuestion1,#submitQuestion2,#mcqText,#pnlLoading").hide();
			$("#reviewArrow").css("visibility","hidden");
			if(isTimerRunning==0)
				StartTimer();
		}
	}
}

function completeTest()
{
	$.post("controller.php","mode=finishSbaTest",function(response){
		if(timerActive || $("#completeTest").text()=="Continue")
		{
			alert("Thank you for completing the test.\nYou scored "+response+"% in the test.\nWatch out for the detailed report. It will be available after 24 hours.");
			window.location.href	=	"home.php";
		}
		else
		{
			alert("Thank you for completing the test.\nYou scored "+response+"% in the test.\nWatch out for the detailed report. It will be available after 24 hours.");
			$("#completeTest").text("Continue");
		}
	});
}

function submitTest()
{
	if(confirm("Are you sure you want to submit this test."))
	{
		completeTest();
	}
}