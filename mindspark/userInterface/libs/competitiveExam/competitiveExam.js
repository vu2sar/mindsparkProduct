// JavaScript Document
var timeSpent = 0;
var q;
var testRunning = 0;
var completed = 2;
var autoSaveInterval, totalScore = 0;
var pendingChallenge = 0;
var keypadPresent = 0;
var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1;
var isIpad = ua.indexOf("ipad") > -1;
$(document).ready(function (e) {
    $(".optionA,.optionB,.optionC,.optionD").live("click", function () {
        if (testRunning != 0) {
            var divClickedID = $(this).closest('div').attr('id');
            $("#" + divClickedID).find("span").removeClass('optionA_selected').removeClass('optionB_selected').removeClass('optionC_selected').removeClass('optionD_selected');
            if ($(this).hasClass("optionA"))
                $(this).addClass("optionA_selected");
            else if ($(this).hasClass("optionB"))
                $(this).addClass("optionB_selected");
            else if ($(this).hasClass("optionC"))
                $(this).addClass("optionC_selected");
            else if ($(this).hasClass("optionD"))
                $(this).addClass("optionD_selected");
            $(this).find("input").attr("checked", true);
        }
    });
    if (screen.width < 900)
        $("body").css({ "font-size": "16px" });
    else if (screen.width >= 900 && screen.width < 1025)
        $("body").css({ "font-size": "17px" });

    $("#selectAll").click(function () {
        if ($(this).attr('checked'))
            $(".topicChkCls").attr('checked', true);
        else
            $(".topicChkCls").attr('checked', false);
    });

    $("#faqDiv").click(function () {
        $("#newChallengeSelectDiv,#questionDiv,#startChallengeBtn,.arrowSubmit,#prevReportDiv,#startText,.arrowBottom,#pastReportDivHead,#challengeNoText,#studenScoreMain").hide();
        $("#faqDispDiv,#faqDivHead,#arrowBottomFaq,.arrowDetail").show();
        $("#minQuesValidation").hide();
        $("#faqDiv").css({ "color": "#9EC956", "border-color": "#9EC956" });
        $("#reportDiv").css({ "color": "#999999", "border-top-color": "#9EC956", "border-bottom-color": "#626060" });
        $("#reportDiv").removeClass("selectedDiv");
        $(this).addClass("selectedDiv");
    });

    $("#reportDiv,#reportDivTab").click(function () {
        if ($("#pendingChallengePrompt").is(":visible"))
            return false;

        $("#reportDivTab").prev("div").show();
        $("#reportDivTab").show();
        $("#challengeNo").closest("div").hide();
        $("#challengeNo").closest("div").prev("div").hide();
        $("#faqDispDiv,#faqDivHead").hide();
        $("#pendingChallengeMsg").hide();
        $("#finalScore,.responseImg,.dlgAnswer_inner,.arrowBottom,#challengeNoText,#studenScoreMain,.arrowSubmit").hide();
        $(".options,.quesTexts,.dlgAnswer_inner").html("");
        $("#qcodes,#ansPrevStr,#scorePrevStr").val("");
        $("#questionDiv,#minQuesValidation,#startChallengeBtn").hide();

        $("#faqDiv").css({ "color": "#999999", "border-color": "#626060" });
        $("#reportDiv").css({ "color": "#9EC956", "border-color": "#9EC956" });

        $("#prevReportDiv,#startText,#pastReportDivHead,#arrowBottomReport,.arrowDetail").show();
        $("#newChallengeSelectDiv").hide();
        if ($("#reportTable tr").length > 1) {
            $("#startText").text("Click on challenge name to see the questions.");
            $("#leftDiv").css({ "visibility": "hidden" });
        }
        else
            $("#startText").html("You have not attempted any challenges yet!<br>Click <span id='startChallengeSpn'>here</span> to start new challenge.");
        $("#startText").show();

        if ($("#pendingChallenge").val() != "") {
            //$("#pendingChallengeMsg").show();
        }
        else {
            $("#reportDiv").removeClass("selectedDiv");
            if ($(this).attr("id") != "reportDivTab")
                $(this).addClass("selectedDiv");
        }
        //$("#leftDiv").css({"visibility":"hidden"});
    });

    $("#startChallengeSpn").live("click", function () {
        $("#faqDispDiv").hide();
        $("#newChallengeSelectDiv").show();
        $("#prevReportDiv").hide();
        $("#startText").hide();
        $("#reportDivTab").prev("div").hide();
        $("#reportDivTab").hide();
    });

    $("#startNew").click(function () {
        $("#newChallengeSelectDiv").show();
        $("#faqDispDiv,#prevReportDiv").hide();
    });
    //---start challenge	
    $("#startChallengeBtn").click(function () {
        var pendingChallengeNo=0;
        if (pendingChallenge != 2) {
            if (pendingChallenge == 0) {
                if (parseInt($("#totQues").text()) < 10) {
                    $("#msgPrompt").hide();
                    $("#msgPrompt").text("Selection should have atleast 10 questions.");
                    $("#msgPrompt").show();
                    $("#msgPrompt").fadeOut(5000);
                    return false;
                }
                else {
                    $("#challengeNo").closest("div").show();
                    $("#challengeNo").closest("div").prev("div").show();

                    $("#saveAnswerBtn,#endChallengeBtn,.arrowDetail").show();
                    $("#faqDiv,#reportDiv,#startChallengeBtn,#minQuesValidation").hide();
                }
            }
            else if (pendingChallenge == 1) {
                pendingChallengeNo=$("#challengeNoHid").val();
                $("#challengeNo").closest("div").show();
                $("#challengeNo").closest("div").prev("div").show();
            }
            $("#reportDivTab").prev("div").hide();
            $("#reportDivTab").hide();
            $("#newChallengeSelectDiv").hide();
            $("#leftDiv").hide();
            $("#rightDiv").css({ "width": "99%" });
            $(".quesSource").show();
        }
        //remove select panel		
        $("#newChallengeDiv,#reportDiv,#homeDiv").removeClass("selectedDiv");
        $(this).addClass("selectedDiv");

        var sourceValues = "";
        var topicValuesSel = "";
        var qcodeStr = $("#qcodes").val();
        var forTTcode = $("#forTTcode").val();
		var higherGrade = 0;
		if($("#higherGradeCls").is(":checked"))
			var higherGrade = 1;
        if (qcodeStr != "") {
            var userID = $("#userID").val();
            $(".sourceCls").each(function (index, element) {
                if ($(this).is(':checked')) {
                    sourceValues += $(this).val() + ",";
                }
            });
            $(".topicChkCls").each(function (index, element) {
                if ($(this).is(':checked')) {
                    topicValuesSel += $(this).val() + ",";
                }
            });
            $(".sourceCls").attr('checked', false);
            $(".topicChkCls").attr('checked', false);
            //$(".topicChkCls").attr('disabled', true);
            $(".topicChkCls").css("visibility", "hidden");
            $("#totQues").text(0);
            $.post("competitiveExamAjax.php", "mode=getQuestions&qcodeStr=" + qcodeStr + "&userID=" + userID + "&sources=" + sourceValues + "&topics=" + topicValuesSel + "&pendingChallenge=" + pendingChallenge + "&challengeNo=" + pendingChallengeNo + "&forTTcode=" + forTTcode + "&higherGrade=" + higherGrade, function (jsonData1) {
                if (jsonData1) {
                    $(".responseImg").removeClass("wrong");
                    $(".responseImg").removeClass("correct");
                    $("#questionDiv").show();
                    var questionArray = $.parseJSON(jsonData1);
                    if (pendingChallenge != 0) {
                        var prevAns = $("#ansPrevStr").val();
                        prevAnsArr = prevAns.split("*$&");
                    }
                    if (pendingChallenge != 2) {
                        $("#challengeNoText").html("CHALLENGE " + questionArray["challengeNo"]).show();
                        $("#challengeNo").text(questionArray["challengeNo"]);
                        $("#challengeNoHid").val(questionArray["challengeNo"]);
                    }

                    if (pendingChallenge == 2) {
                        var userPrevAnsStr = $("#scorePrevStr").val();
                        var userPrevAnsArr = userPrevAnsStr.split("*$&");
                    }
                    totalScore = 0;
                    for (q = 1; q <= 10; q++) {
                        $("#qcode_" + q).val(questionArray[q]["qcode"]);
                        $(".sourceQues_" + q).html(questionArray[q]["source"]);
                        $("#quesText_" + q).html(questionArray[q]["quesText"]);
                        $("#option_" + q).html(questionArray[q]["options"]);
                        $("#qtype_" + q).val(questionArray[q]["quesType"]);
                        $("#correctAns_" + q).val(questionArray[q]["correctAns"]);
                        $("#dlgAnswer_inner_" + q).html(questionArray[q]["quesDisplayAns"]);
                        if (pendingChallenge == 1 || pendingChallenge == 2) {
                            //for displaying reports ----------pendingChallenge==2
                            correctanswer = decrypt(questionArray[q]["correctAns"]);
                            if (pendingChallenge == 2) {
                                var msg = "";
                                if (userPrevAnsArr[q - 1] == 1) {
                                    $("#responseImg_" + q).show();
                                    //$("#responseImg_"+q).html("<img src='images/right.gif' style='vertical-align:middle'>");
                                    $("#responseImg_" + q).addClass("correct");
                                    totalScore++;
                                }
                                else if (userPrevAnsArr[q - 1] == 0) {
									var userResponseTemp = prevAnsArr[q - 1].replace(/(\||,)/g, "");
									if(userResponseTemp!="")
									{
										$("#responseImg_" + q).show();
										//$("#responseImg_"+q).html("<img src='images/wrong.gif' style='vertical-align:middle'>");
										$("#responseImg_" + q).addClass("wrong");
									}
									else
									{
										$("#responseImg_" + q).show();
										$("#responseImg_" + q).next().html("Not attempted");
										//$("#responseImg_"+q).html("<img src='images/wrong.gif' style='vertical-align:middle'>");
										$("#responseImg_" + q).addClass("notAttempted");
									}
                                }
                                msg += "<span style='vertical-align:top'>Correct answer: </span>";
                                var tmpAns = decrypt($('#dlgAnswer_inner_' + q).html());
                                if ((questionArray[q]["quesType"] == 'MCQ-4' || questionArray[q]["quesType"] == 'MCQ-3' || questionArray[q]["quesType"] == 'MCQ-2') && $.trim(tmpAns) != correctanswer)
                                    msg += correctanswer + ": ";
                                msg += tmpAns;
                                msg += "<br><br>";
                                $("#dispAns_" + q).html("<span>" + msg + "</span");
                                $(".desk_block").show();
                                /*$('#dlgAnswer_inner_'+q).html(msg);
                                $("#dlgAnswer_inner_"+q).show();*/
                                $("#bottomBtn").hide();
                            }

                            if (questionArray[q]["quesType"] == "Blank") {
                                tempAnsArr = prevAnsArr[q - 1].split("|");
                                for (var n = 0; n < tempAnsArr.length; n++) {
                                    $("#quesText_" + q).find('#b' + (n + 1)).val(tempAnsArr[n]);
                                }
                            }
                            else {
                                $(".ansRadio" + q).each(function () {
                                    if ($(this).val() == prevAnsArr[q - 1]) {
                                        $(this).attr("checked", true);
                                        $("#option_" + q).find(".option" + prevAnsArr[q - 1]).addClass("option" + prevAnsArr[q - 1] + "_selected");
                                        if (pendingChallenge == 2)
                                            $("#option_" + q).find(".option" + prevAnsArr[q - 1]).removeClass("option" + prevAnsArr[q - 1]);
                                    }
                                });
                            }
                        }
                        $("#responseImageText_" + q).show();
                    }
                    try {
                        jsMath.Process(document);
                    } catch (err) { };
                    if (pendingChallenge == 2) {
                        testRunning = 0;
                        completed = 1;
                        $("#studentScore").text(totalScore);
                        $("#studenScoreMain").show();
                        $("input[type=text],input[type=radio]").attr("disabled", true);
                        $(".sourceCls").removeAttr('disabled');
                        $(".questionDispDiv").css({ "width": "75%" });
                    }
                    else {
                        $(".questionDispDiv").css({ "width": "90%" });
                        testRunning = 1;
                        completed = 0;
                    }
                    if ((isIpad || isAndroid)&&$("#fracB_1").length==0) {
                        attachKeypad('competetiveExam');
                    }
                }
            });
        }
    });
    //---start challenge-----ends here

    //----select sources------starts here
    $(".sourceCls,.topicChkCls,#defaultSelect").click(function () {
        pendingChallenge = 0;
        var clickedClass = $(this).attr("class");
        var sourceVal = "";
        var topicValSel = "";
        var sourceID = $(this).attr("id");
        $(".sourceCls").each(function (index, element) {
            if ($(this).is(':checked')) {
                sourceVal += $(this).val() + ",";
            }
        });
        if (clickedClass == "topicChkCls") {
            $(".topicChkCls").each(function (index, element) {
                if (!$(this).is(':checked')) {
                    topicValSel += $(this).val() + ",";
                }
            });
        }
        $(".sourceCls").attr('disabled', true);
        if (sourceVal == "") {
            $(".sourceCls").attr('disabled', false);
            $(".topicChkCls").attr('checked', false);
            //$(".topicChkCls").attr('disabled', true);
            $(".topicChkCls").css("visibility", "hidden");
            $("#totQues").text(0);
        }
        else {
            var topicVal = "";
            var userID = $("#userID").val();
			var higherGrade = 0;
			if($("#higherGradeCls").is(":checked"))
				var higherGrade = 1;
            $.post("competitiveExamAjax.php", "mode=checkTopic&sources=" + sourceVal + "&topics=" + topicValSel + "&userID=" + userID + "&higherGrade=" + higherGrade, function (data) {
                if (data) {
                    $(".sourceCls").attr('disabled', false);
                    $(".topicChkCls").attr('checked', false);
                    if (clickedClass != "topicChkCls") {
                        //$(".topicChkCls").attr('disabled', true);
                        $(".topicChkCls").css("visibility", "hidden");
                        $("#defaultSelect").attr('checked', true);
                        $("#defaultSelect").attr('disabled', true);
                    }
                    else {
                        $("#defaultSelect").attr('disabled', false);
                        $("#defaultSelect").attr('checked', false);
                    }
                    var dataArr = data.split("||");
                    srcArr = dataArr[0];
                    totalQues = dataArr[1];
                    if (totalQues > 9)
                        $("#qcodes").val(dataArr[2]);
                    $("#totQues").text(totalQues);

                    $(".topicChkCls").each(function (index, element) {
                        topicVal = $(this).val();
                        if (srcArr.indexOf(topicVal) > -1) {
                            $(this).removeAttr("disabled");
                            $(this).css("visibility", "visible");
                            $(this).attr('checked', true);
                        }
                    });
                }
            });
        }
    });
    //----select sources------ends here	

    $("#endChallengeBtn").click(function () {
        $(".questionDispDiv input").attr("disabled", true);
        $("#confirmPrompt").show();
    });

    $("#clickYes,#clickNo").click(function () {
        $(".questionDispDiv input").removeAttr("disabled");
        $("#confirmPrompt").hide();
        if ($(this).attr("id") == "clickYes") {
            completed = 1;
            saveAnswers();
            testRunning = 0;
        }
    });

    autoSaveInterval = setInterval(function () {
        if (completed == 0) {
            timeSpent = 30;
            saveAnswers();
        }
    }, 30000);

    $("#saveAnswerBtn").click(function () {
        if (completed == 0) {
            timeSpent = 0;
            saveAnswers();
        }
    });

    $(".showMoreLess").live("click", function () {
        if ($("#pendingChallengePrompt").is(":visible"))
            return false;
        if ($(this).attr("id") == "showMore") {
            $(this).hide();
            $(this).next("span").show();
            $(this).next().next("span").show();
        }
        else {
            $(this).hide();
            $(this).prev("span").hide();
            $(this).prev().prev("span").show();
        }
    });
    /*$(".showMoreLess").click(function(){
    $(this).hide();
    $(this).next("span").show();	
    });*/
    $("#pendingNo").click(function () {
        $("#pendingChallengePrompt").hide();
    });


});
function closeFromChoice () {
    if (testRunning==1 && completed!=1 && fromChoice==true){
        alert('You can complete the challenge later from the Exam Corner page.');
    }
    return true;
}

function saveAnswers() {
    var challengeNo = $("#challengeNoHid").val();
    var userID = $("#userID").val();
    if (testRunning == 1) {
        for (var a = 1; a <= 10; a++)
        {
            var qcode = $("#qcode_" + a).val();
            var ques_type = $('#qtype_' + a).val();
            
            var part_ans = new Array();
            //Split the Answers - For Blanks
            var correctanswer = decrypt($('#correctAns_' + a).val());            
            var userAns = new Array();            
            part_ans = correctanswer.split("|");
            for (var i = 0; i < part_ans.length; i++) {
                var b = '';
                // if (ques_type == 'Blank') {
                //     if (document.getElementById('b' + (i + 1) + '')) {
                //         b = $("#quesText_" + a).find('#b' + (i + 1)).val();
                //         alert(b)
                //         userAns.push(b);
                //     }
                // }
                if (ques_type == 'Blank') {                    
                   if (($("#quesText_" + a).find('#b' + (i + 1)).length)>0) {
                       b = ($("#quesText_" + a).find('#b' + (i + 1)).val()).trim();                       
                       userAns.push(b);
                   }
                   else {                                                         
                       b=($("#quesText_" +a).find("#fracB_" + (i + 1))[0].contentWindow.getData()).replace(/\{([^\{]*)\}\/\{([^\{]*)\}/g,replaceFraction).trim();                                                                                                                               
                       userAns.push(b);                      
                   }
               }
                else {
                    b = $("#option_" + a).find("input:checked").val();
                    if (b) {
                        userAns.push(b);
                    }
                }

            }
            var userAnswer = userAns.join("|");            
            var result = evaluateResponse(userAnswer, correctanswer, ques_type);
            if (a != 1)
                timeSpent = 0;

            if (completed == 1) {
                $("#saveAnswerBtn,#endChallengeBtn,.arrowSubmit").hide();
                $("#faqDiv,#reportDiv").show();
                var msg = "";
                if (result == 1) {
                    $("#responseImg_" + a).show();
                    //$("#responseImg_"+a).html("<img src='images/right.gif' style='vertical-align:middle'>");
                    $("#responseImg_" + a).addClass("correct");
                    totalScore++;
                }
                else if (result == 0) {
					var userResponseTemp = userAnswer.replace(/(\||,)/g, "");
					if(userResponseTemp!="")
					{
						$("#responseImg_" + a).show();
                    	$("#responseImg_" + a).addClass("wrong");
					}
					else
					{
						$("#responseImg_" + a).show();
						$("#responseImg_" + a).next().html("Not attempted");
                    	$("#responseImg_" + a).addClass("notAttempted");
					}
                }
                msg += "<span style='vertical-align:top'><strong>Correct answer: </strong></span>";
                var tmpAns = decrypt($('#dlgAnswer_inner_' + a).html());
                if ((ques_type == 'MCQ-4' || ques_type == 'MCQ-3' || ques_type == 'MCQ-2') && $.trim(tmpAns) != correctanswer)
                    msg += correctanswer + ": ";
                msg += tmpAns;
                msg += "<br><br>";
                $("#dispAns_" + a).html("<span>" + msg + "</span");

                try {
                    jsMath.ProcessBeforeShowing(document.getElementById("dispAns_" + a));
                } catch (err) { };
                $(".desk_block").show();
                $("#responseImageText_" + a).show();
                /*$('#dlgAnswer_inner_'+a).html(msg);
                $("#dlgAnswer_inner_"+a).show();*/
                if (a == 10) {
                    $("input").attr("disabled", true); 
                    for (var c = 1; c <= 10; c++) {
                        for (var i = 0; i < part_ans.length; i++) {
                    $("#quesText_" +c).find("#fracB_" + (i + 1)).css({"background":"rgb(235, 235, 228)","pointer-events":"none"});                  
                }
                }
                    $(".sourceCls").removeAttr('disabled');
                    $("#bottomBtn,.quesSource").hide();
                    $("#leftDiv").show();
                    $("#leftDiv").css({ "visibility": "visible" });
                    $("#studentScore").text(totalScore);
                    $("#studenScoreMain").show();
                    $('#topicInfoContainer').animate({ scrollTop: 0 }, 'slow');
                }
                $(".responseBar").show();
                $(".questionDispDiv").css({ "width": "75%" });
            }
            if (timeSpent == 0)
                $("#msgAutoPrompt").text("Answers saved successfully.");
            else
                $("#msgAutoPrompt").text("Answers auto saved.");
            $.post("competitiveExamAjax.php", "mode=updateQuesAttempt&qcode=" + qcode + "&userAnswer=" + userAnswer + "&userID=" + userID + "&result=" + result + "&timeSpent=" + timeSpent + "&challengeNo=" + challengeNo + "&completed=" + completed + "&totalScore=" + totalScore + "&quesNo=" + a, function (dataNew) {
                if (dataNew) {
                    $("#msgPrompt").hide();
                    if (completed == 0) {
                        $("#msgAutoPrompt").show();
                        $("#msgAutoPrompt").fadeOut(5000);
                    }
                    if (dataNew.length > 30) {
                        if (completed == 1) {
                            if (a == 11) {
                                var tableData = $.parseJSON(dataNew);
                                if ($("#pendingChallenge").val() != "") {
                                    $("#pendingChallenge").val("");
                                    //$("#reportTable tr:eq("+(tbleTrLen-1)+")").remove();
                                    $("#reportTable tr:eq(1)").remove();
                                }
                                $("#reportTable").show();
                                var tbleTrLen = $("#reportTable tr").length;
                                if ($("#reportTable tr").length == 1) {
                                    $("#reportTable").append(tableData);
                                }
                                else
                                    $("#reportTable tr:eq(1)").before(tableData);
                            }
                        }
                    }
                }
            });
        }
    }
}

function replaceFraction(){
    return arguments[1].trim()+'/'+arguments[2].trim();
}
function evaluateResponse(userAnswer, correctanswer, ques_type) {
    var result;
    var b1;
    var part_ans = new Array();
    var user_ans = new Array();
    part_ans = correctanswer.split("|");
    user_ans = userAnswer.split("|");
    /*if(userRealResponse=="")
    document.getElementById('userResponse').value = userAnswer;
    else
    document.getElementById('userResponse').value = userRealResponse;*/
    var flag = 0;
    var count = part_ans.length;
    var correct_answer_str = '';
    var checkblanks = 0;
    for (var i = 0; i < part_ans.length; i++) {
        var correct_answer = '';
        var ans = new Array();
        //Split the different way to answer
        ans = part_ans[i].split("~");
        ans = unique(ans);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)

        var b = '';
        if (ques_type == 'Blank') {
            b = eval("user_ans[i]");
            b = b.replace(/\\/g, "/");
            if (b == '')
                checkblanks++;
        }
        else {
            b = eval("user_ans[i]");
        }

        for (var j = 0; j < ans.length; j++) {
            if (b == '' && ans[j] == '') {
                flag++;
                break;
            }
            else {
                b1 = $.trim(b);
                ans_blank = $.trim(ans[j]);
                if (b1 != "" && ans_blank != "" && !isNaN(b) && !isNaN(ans_blank)) {
                    b1 = parseFloat(b1);
                    ans_blank = parseFloat(ans_blank);
                }
                else if (!isNaN(ans_blank) && ans_blank != "") //Remove comma only when expected answer is numeric && ans_blank!=""
                {
                    b1AfterCommaRemoval = b1.replace(/,/g, "");

                    if (!isNaN(b1AfterCommaRemoval) && b1AfterCommaRemoval != "")
                        b1 = b1AfterCommaRemoval;
                    if (!isNaN(b1))
                        b1 = parseFloat(b1);
                    ans_blank = parseFloat(ans_blank);
                }
                if (b1 == ans_blank) {
                    flag++;
                    break;
                }
                else
                {      
                     b1 = $.trim(b);
                     ans_blank = $.trim(ans[j]);
                    try
                    {
                        try
                        {
                            if(expComp.compEqs(ans_blank, b1))
                            {
                                flag++;
                                break;
                            }
                        }
                        catch(err)
                        {
                            // do nothing, continue execution
                        }
                        var cAnswer = nParser.parse(ans_blank);
                        var uAnswer = nParser.parse(b1);
                        
                        if(uAnswer == cAnswer)
                        {
                            flag++;
                            break;
                        }
                        else if(checkAutoRounding && ((document.getElementById("b"+(j+1))) || document.getElementById("tmpMode").value == "NCERT"))
                        {
                            // auto rounding should happen when textbox exists (to avoid frac box auto rounding)
                            if(checkRoundValue(ans_blank, b1)) 
                            {
                                flag++;
                                break;
                            }
                        }
                    }
                    catch(err)
                    {
                        // do nothing, continue execution
                    }
                }                

            }
            correct_answer = correct_answer + ans[j] + " Or ";
        }
        if (ques_type == 'Blank')
            correct_answer_str = correct_answer_str + correct_answer.substring(0, correct_answer.length - 4) + ", ";
        else
            correct_answer_str = correct_answer_str + correct_answer.substring(0, correct_answer.length - 4);
    }
    if (b == '' && (ques_type == 'MCQ-4' || ques_type == 'MCQ-3' || ques_type == 'MCQ-2')) {
        //alert("Please submit your answer.");
        return 0;
    }

    if (ques_type == 'Blank')
        correct_answer_str = correct_answer_str.substring(0, correct_answer_str.length - 2);

    //All Blank must be right
    if (count == flag) {
        $('#result').val("1");
        return 1;
    }
    else {
        if (b == '' && checkblanks == count) {
            return 0;
        }
        $('#result').val("0");
        return 0;
    }
    return 1;
}


function decrypt(str) {
    var strtodecrypt = str.split("-");
    var msglength = strtodecrypt.length;
    decrypted_message = "";
    for (var position = 0; position < msglength; position++) {
        ascii_num_byte_to_decrypt = strtodecrypt[position];
        ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt / 2;
        ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt - 5;
        decrypted_byte = String.fromCharCode(ascii_num_byte_to_decrypt);
        decrypted_message += decrypted_byte;
    }
    return decrypted_message;
}

function unique(a) {
    var r = new Array();
    o: for (var i = 0, n = a.length; i < n; i++) {
        for (var x = 0, y = r.length; x < y; x++) {
            if ($.trim(r[x]) == $.trim(a[i]))
                continue o;
        }
        r[r.length] = a[i];
    }
    return r;
}
function setFocus() {
}

function getReport(challengeNo) {
    $("#prevReportDiv,.arrowSubmit,#startText,#pastReportDivHead,.quesSource").hide();
    $("#startText").hide();
    $("#challengeNo").closest("div").show();
    $("#challengeNo").closest("div").prev("div").show();
    $(",.responseBar").show();
    userID = $("#userID").val();
    $.post("competitiveExamAjax.php", "mode=getReportQues&challengeNo=" + challengeNo + "&userID=" + userID, function (ques) {
        if (ques) {
            quesAnsArray = ques.split("$#$");
            $("#qcodes").val(quesAnsArray[0]);
            $("#ansPrevStr").val(quesAnsArray[1]);
            $("#scorePrevStr").val(quesAnsArray[2]);
            pendingChallenge = 2;
            $("#challengeNoText").html("CHALLENGE " + challengeNo).show();
            $("#challengeNo").text(challengeNo);
            $("#challengeNoHid").val(challengeNo);
            $("#startChallengeBtn").click();
        }
    });
    setTimeout(function () {
        tryingToUnloadPage = false;
    }, 1000);
}

function startPending(pendingChallengeNo) {
    /*if($("#pendingChallengePrompt").is(":visible"))
    return false;*/
    $("#finalScore,.responseImg,.dlgAnswer_inner,.desk_block,#faqDispDiv,#startChallengeBtn,#faqDiv,#reportDiv,#pastReportDivHead,.responseBar").hide();
    $(".options,.quesTexts,.dlgAnswer_inner").html("");
    $("#qcodes,#ansPrevStr,#scorePrevStr").val("");
    $("#questionDiv").hide();
    $("#prevReportDiv").hide();
    $("#startText").hide();
    $("#pendingChallengeMsg").hide();
    $("#pendingChallengePrompt").hide();
    userID = $("#userID").val();
    $.post("competitiveExamAjax.php", "mode=getReportQues&challengeNo=" + pendingChallengeNo + "&userID=" + userID, function (ques) {
        if (ques) {
            $("#endChallengeBtn,#saveAnswerBtn,.arrowDetail,.arrowSubmit").show();
            $("#challengeNoText").html("CHALLENGE " + pendingChallengeNo).show();
            quesAnsArray = ques.split("$#$");
            $("#qcodes").val(quesAnsArray[0]);
            $("#ansPrevStr").val(quesAnsArray[1]);
            pendingChallenge = 1;
            $("#challengeNo").text(pendingChallengeNo);
            $("#scorePrevStr").val(quesAnsArray[2]);
            $("#challengeNoHid").val(pendingChallengeNo);
            $("#startChallengeBtn").click();
        }
    });
    setTimeout(function () {
        tryingToUnloadPage = false;
    }, 1000);
}
function logoff() {
    setTryingToUnload();
    window.location = "logout.php";
}
function CheckIfNumeric(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode != 46 && charCode != 47 && charCode != 45 && (charCode < 48 || charCode > 57)) {
        if (!keypadPresent)
            alert("Please enter only numbers.");
        return false;
    }
    return true;
}
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