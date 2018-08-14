var mins, secs, TimerRunning, TimerID, startClockId, timedTestVersion, autoSubmit, startIdealTimeout, timeTaken, ansSubmited = 0;
TimerRunning = false;
var ddStatus = 1;
var closeDD = 0;
var timedTestruning = true;
var autoSubmitUsed = 0;
var timeOfQuestions = [];
jQuery(document).ready(function () {
    jQuery("#wildcardImg").live("click", function () {
        jQuery("#wildcardInfo").dialog({
            width: "400px",
            position: "right",
            draggable: false,
            resizable: false,
            modal: true
        });
    });
    jQuery(".ui-widget-overlay").live("click", function () {
        jQuery("#wildcardInfo").dialog("close");
    });
    jQuery("#wildcardInfo").live("click", function () {
        jQuery("#wildcardInfo").dialog("close");
    });
    jQuery("#ui-dialog-titlebar").live("click", function () {
        jQuery("#wildcardInfo").dialog("close");
    });


    //for blank type questions
    timedTestVersion = jQuery("#timedTestVersion").val();
    autoSubmit = jQuery("#autoSubmit").val();
    jQuery("#tblWorkSheetMain input[type=text]").keyup(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        var txtidfocus = jQuery(this).attr("id");
        if (document.getElementById('disableAutoSubmit').checked) {
            autoSubmitUsed = 1;
        }
        if (keycode == '13') //if enter key pressed
        {
            if (timedTestruning)
                nextQues(txtidfocus, 'enter');
        }
        else if (/*autoSubmit == 1 && */!document.getElementById('disableAutoSubmit').checked) //auto submit
        {
            setTimeout(function () {
                if (timedTestruning)
                    nextQues(txtidfocus, '');
            }, 500);
        }
    });
    //for mcq type questions
    jQuery("#tblWorkSheetMain input[type=radio]").click(function (event) {
        setTimeout(function () {
            if (timedTestruning)
                nextQues('', '');
        }, 500);
    });
    //for drop down
    jQuery("#tblWorkSheetMain select").change(function (event) {
        txtidfocus = jQuery(this).attr("id");
        if (timedTestruning)
            nextQues(txtidfocus, '');
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            txtidfocus = jQuery(this).attr("id");
            if (timedTestruning)
                nextQues(txtidfocus, 'enter');
        }
    });
});

function Init() //call the Init function when u need to start the timer
{
    load();
    mins = document.getElementById('txtDuration').value;
    totalTime = mins * 60;
    secs = timeTakenSecs = 0;
    initializeTextBoxValues();
    StopTimer();
    startTimer();
    startClock();
}
var angle = 0;

function startClock() {
    var interval = 360 / totalTime;
    if (angle > 359)
        StopTimer();
    else {
        startClockId = setTimeout("startClock()", 1000);
        angle = angle + interval;
    }
    jQuery("#clockHand").css({ "transform": "rotate(" + angle + "deg)" });
    jQuery("#clockHand").css({ msTransform: "rotate(" + angle + "deg)" });
    jQuery("#clockHand").css({ "-moz-transform": "rotate(" + angle + "deg)" });
    jQuery("#clockHand").css({ "-webkit-transform": "rotate(" + angle + "deg)" });
    jQuery("#clockHand").css({ "-o-transform": "rotate(" + angle + "deg)" });
}
function initializeTextBoxValues() {
    var objArray = document.getElementsByTagName("input");
    for (var i = 0; i < objArray.length; i++) {
        if (objArray[i].id.substring(0, 6) == "txtAns") {
            objArray[i].value = "";
        }
    }
}

function StopTimer() {
    if (TimerRunning) {
        clearTimeout(TimerID);
        clearTimeout(startClockId);
    }
    TimerRunning = false;
}

function startTimer() {
    TimerRunning = 1;
    Check();
    if (mins == 0 && secs == 0)
        StopTimer();
    else {
        TimerID = setTimeout("startTimer()", 1000);
    }
    document.getElementById('spnTime').innerHTML = Pad(mins) + ":" + Pad(secs);
    if (secs == 0) {
        mins--;
        secs = 60;
    }
    secs--;
}
function Check() {
    if (mins == 0 && secs == 0) {
        //document.getElementById('test').style.display = "";
        document.getElementById('pnlEndSessionMsg').style.display = "none";
        alert("Your alloted time is over.");
        StopTimer();
        getScore();
        if (timedTestVersion == 1) {
            document.getElementById('btnFinish').disabled = true;
            document.getElementById('btnFinish').style.display = "none";
        }
    }
}

function Pad(number) //pads the mins/secs with a 0 if its less than 10
{
    if (number < 10)
        number = 0 + "" + number;
    return number;
}
function getFlashMovieObject(movieName) {
    /*if(document.embeds[movieName])	//Firefox
    return document.embeds[movieName];
    if(window.document[movieName])//IE
    return window.document[movieName];
    if(window[movieName])
    return window[movieName];
    if(document[movieName])
    return document[movieName];
    return null;*/
    if (window.document[movieName]) {
        return window.document[movieName];
    }
    if (navigator.appName.indexOf("Microsoft Internet") == -1) {
        if (document.embeds && document.embeds[movieName])
            return document.embeds[movieName];
    }
    else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
    {
        return document.getElementById(movieName);
    }
}
var ansArray = new Array();
var correctAns = 0;
var lastQTime = 0;

function nextQues(txtidfocus, mode) {
    var nextQues = document.getElementById('dispQues').value;
    var totalQues = document.getElementById('totQues').value;
    if ((parseInt(nextQues)) > (parseInt(totalQues) + 1))
        return false;
    var minNoOfQues = document.getElementById('noOfQuesInTimedTest').value;
    var isAnswered = false;

    //condition when function runs on dom load the next question no is 1
    if (nextQues == 1){
        quesType = document.getElementById('quesType' + nextQues).value;
        lastQTime = totalTime;
    }
    //condition when function runs when user respond
    if (nextQues > 1) {
        quesType = document.getElementById('quesType' + (nextQues - 1)).value;
        document.getElementById('timeOfQ' + (nextQues - 1)).value = lastQTime - ((mins*60)+secs);
        lastQTime = (mins*60)+secs;

        if (quesType == "D")
            ansStr = ansArray[nextQues - 2].split("|");
        else
            ansStr = ansArray[nextQues - 2].split("|");

        if (quesType == "Blank") {
            var txtno = new Array();
            blankNo = txtidfocus.charAt(txtidfocus.length - 1); //the blank no with focus
            totalBlank = ansStr.length; //total blank in the question
            for (var j = 0; j < ansStr.length; j++) {
                ansStrBreak = ansStr[j].split("~");
                var blanknos = j + 1;
                var objStr = 'txtAns' + (nextQues - 1) + '_' + blanknos;
                correctAnsLength = ansStrBreak[0].length;
                userAnsLength = trim(document.getElementById(objStr).value).length;
                if (correctAnsLength > userAnsLength && mode == '' && blankNo == j + 1)
                    return false;

                if (document.getElementById(objStr).value != "" || ansStrBreak[0] == '') {
                    txtno[j] = 'notblank';
                }
                else {
                    txtno[j] = 'blank';
                }
            }

            for (var b = blankNo; b <= totalBlank; b++) {
                if (b == totalBlank) {
                    for (var b = 0; b <= blankNo; b++) {
                        if (txtno[b] == 'blank') {
                            setTimeout(function(){document.getElementById("txtAns" + (nextQues - 1) + "_" + (parseInt(b) + 1)).focus();},200);
                            return false;
                        }
                    }
                    break;
                }
                if (txtno[b] == 'blank') {
                    setTimeout(function(){document.getElementById("txtAns" + (nextQues - 1) + "_" + (parseInt(b) + 1)).focus();},200);
                    return false;
                }
            }
            correctAns = checkAns("Blank", ansArray[nextQues - 2], nextQues - 1, correctAns);
        }
        else if (quesType == "D") {
            var dropno = new Array();
            totalDropDown = ansStr.length;
            selectNo = txtidfocus.charAt(txtidfocus.length - 1);
            for (var t = 0; t < totalDropDown; t++) {
                var blanknos = t;
                var objStr = 'lstOpt' + (nextQues - 1) + '_' + blanknos;
                if (document.getElementById(objStr).value != "") {
                    dropno[t] = 'notblank';
                }
                else {
                    dropno[t] = 'blank';
                }
            }
            for (var d = selectNo; d <= totalDropDown; d++) {
                if (d == totalDropDown) {
                    for (var d = 0; d <= selectNo; d++) {
                        if (dropno[d] == 'blank') {
                            document.getElementById("lstOpt" + (nextQues - 1) + "_" + parseInt(d)).focus();
                            return false;
                        }
                    }
                    break;
                }
                if (dropno[d] == 'blank') {
                    document.getElementById("lstOpt" + (nextQues - 1) + "_" + parseInt(d)).focus();
                    return false;
                }
            }
            correctAns = checkAns("D", ansArray[nextQues - 2], nextQues - 1, correctAns);
        }
        else {
            //do nothing
            correctAns = checkAns(quesType, ansArray[nextQues - 2], nextQues - 1, correctAns);
        }

        if (timedTestVersion == 3) {
            if (parseInt(nextQues) > parseInt(minNoOfQues))
                jQuery("#dispAttemptedQues" + nextQues).css("display", '');

            //if(correctAns>14 && correctAns<25)
            //	var widthPerQues	=	19;
            //else if(correctAns>24)
            //	var widthPerQues	=	12;
            //else
            //	var widthPerQues	=	30;
            widthPerQues = (450 / totalQues);
            var progTotal = parseFloat(widthPerQues) * parseInt(correctAns);
            var attemptTotal = parseInt(widthPerQues) * parseInt(nextQues - 1);
            jQuery("#progressBarDiv").animate({ width: progTotal }, 'slow');
            jQuery("#totalCorrect").text(correctAns);
            if (correctAns == 15) {
                jQuery("#ques1Bord").css("margin-left", (widthPerQues * 5) + "px");
                jQuery("#ques1BordDisp").css("margin-left", ((widthPerQues * 5) - 5) + "px");
                jQuery("#ques2Bord").css("margin-left", (widthPerQues * 10) + "px");
                jQuery("#ques2BordDisp").css("margin-left", ((widthPerQues * 10) - 9) + "px");
                jQuery("#ques3Bord").css("margin-left", (widthPerQues * 15) + "px");
                jQuery("#ques3BordDisp").css("margin-left", ((widthPerQues * 15) - 9) + "px");
                jQuery("#ques4Bord").css("display", '');
                jQuery("#ques4Bord").css("margin-left", (widthPerQues * 20) + "px");
                jQuery("#ques4BordDisp").css("display", '');
                jQuery("#ques4BordDisp").css("margin-left", ((widthPerQues * 20) - 9) + "px");
                jQuery("#ques5Bord").css("display", '');
                jQuery("#ques5Bord").css("margin-left", (widthPerQues * 25) + "px");
                jQuery("#ques5BordDisp").css("display", '');
                jQuery("#ques5BordDisp").css("margin-left", ((widthPerQues * 25) - 9) + "px");
            }
            if (correctAns == 25) {
                jQuery("#ques1Bord").css("margin-left", (widthPerQues * 10) + "px");
                jQuery("#ques1BordDisp").css("margin-left", ((widthPerQues * 10) - 9) + "px");
                jQuery("#ques1BordDisp").text(10);
                jQuery("#ques2Bord").css("margin-left", (widthPerQues * 20) + "px");
                jQuery("#ques2BordDisp").css("margin-left", ((widthPerQues * 20) - 9) + "px");
                jQuery("#ques2BordDisp").text(20);
                jQuery("#ques3Bord").css("margin-left", (widthPerQues * 30) + "px");
                jQuery("#ques3BordDisp").css("margin-left", ((widthPerQues * 30) - 9) + "px");
                jQuery("#ques3BordDisp").text(30);
                jQuery("#ques4Bord").css("display", '');
                jQuery("#ques4Bord").css("margin-left", (widthPerQues * 40) + "px");
                jQuery("#ques4BordDisp").css("display", '');
                jQuery("#ques4BordDisp").css("margin-left", ((widthPerQues * 40) - 9) + "px");
                jQuery("#ques4BordDisp").text(40);
                jQuery("#ques5Bord").css("display", 'none');
                jQuery("#ques5BordDisp").css("display", 'none');
            }
        }
        if (timedTestVersion == 2) {
            jQuery("#totalAttempted").text(parseInt(nextQues) - 1);
            jQuery("#totalCorrect").text(correctAns);
        }
    }

    for (i = 1; i <= totalQues; i++) {
        document.getElementById("single" + i).style.display = "none";
    }
    jQuery('#disableAutoSubmit,#disableAutoSubmit+label').hide();
    if (parseInt(nextQues) == (parseInt(totalQues) + 1)) {
        timedTestruning = false;
        document.getElementById('dispQues').value = parseInt(nextQues) + 1;
        StopTimer();
        getScore();
    }
    else {
        document.getElementById("single" + nextQues).style.display = "";
        document.getElementById("single" + nextQues).p
        if (jQuery('#quesType' + nextQues).val() == "Blank") {
            setTimeout(function(){document.getElementById("txtAns" + nextQues + "_1").focus();},200);
            jQuery('#disableAutoSubmit,#disableAutoSubmit+label').show();
        }
        else if (jQuery('#quesType' + nextQues).val() == "D") {
            setTimeout(function(){document.getElementById("lstOpt" + nextQues + "_0").focus();},200);
        }
        document.getElementById('dispQues').value = parseInt(nextQues) + 1;
    }
}

var ansArray = new Array();
function checkAnswers() {
    jQuery('#timedTestContainer').animate({ 'min-height': '0px' }, 600);
    var allAnswered = true;
    var qno;
    var quesType = '';

    for (var i = 0; i < ansArray.length; i++) {
        qno = i + 1;
        quesType = document.getElementById('quesType' + qno).value;
        ansStr = ansArray[i].split("|");
        var isAnswered = false;
        if (quesType == "Blank") {
            for (var j = 0; j < ansStr.length && !isAnswered; j++) {
                var blankno = j + 1;
                var objStr = 'txtAns' + qno + '_' + blankno;
                if (document.getElementById(objStr).value != "")
                    isAnswered = true;
            }
        }
        else if (quesType == "I") {
            var flashMovie = getFlashMovieObject("simplemovie" + qno);
            try {
                var userResp = flashMovie.GetVariable("userResponse");
                if (!isNaN(userResp))
                    isAnswered = true;
            } catch (err) { }
        }
        else if (quesType == "D") {
            var correct = 1;
            ansStr = ansArray[i].split("|");
            var totalDropdown = ansStr.length;
            for (var d = 0; d < totalDropdown; d++) {
                var objStr = 'lstOpt' + (i + 1) + '_' + d;
                var userResponse = document.getElementById(objStr).selectedIndex;
                if (userResponse == "")
                    isAnswered = false;
                else {
                    document.getElementById(objStr).disabled = true;
                    isAnswered = true;
                }
                if (ansStr[d] != userResponse)
                    correct = 0;
            }
        }
        else {
            obj = eval("document.frmTimedTest.ansRadio" + qno);
            for (r = 0; r < obj.length; r++) {
                if (obj[r].checked)
                    isAnswered = true;
            }
        }
        if (!isAnswered) {
            allAnswered = false;
            break;
        }
    }
    if (!allAnswered) {
        alert("Please fill in the answers for all the questions!");
        if (quesType == "D")
            jQuery(".dropDown").attr("disabled", false);
        //return false;
    }
    else {
        document.getElementById('btnFinish').disabled = true;
        document.getElementById('btnFinish').style.display = "none";
        //var timerObj = getFlashMovieObject("CircularTimer");
        //timerObj.SetVariable("flag",0);
        StopTimer();
        getScore();
    }
}

function getScore() {
    var qno;
    var right = 0;
    var noofques = ansArray.length;
    var ans, ansStr, correct;
    var quesType = '';
    var b;
    var noOfQuesAttempted = 0;
    var userResponse;
    var userAnsArray = new Array();
    if (timedTestVersion == 3) {
        if (document.getElementById('dispQues')) {
            var totalQues = document.getElementById('totQues').value;
            for (i = 1; i <= totalQues; i++) {
                document.getElementById("single" + i).style.display = "";
            }
        }
        jQuery("#tblWorkSheetMain").contents().find("input[type=text],input[type=radio]:checked,select").each(function (index, value) {
            userAnsArray.push(jQuery(this).val());
        });
        var k = 0;
        var radioName = '';
        jQuery("#tblWorkSheetOther").contents().find("input[type=text],input[type=radio],select").each(function (index, value) {
            if (jQuery(this).attr("type") == 'radio') {
                if (jQuery(this).val() == userAnsArray[k]) {
                    if (radioName == jQuery(this).attr("name")) {
                        //continue;
                    }
                    else {
                        jQuery(this).attr("checked", "checked");
                        radioName = jQuery(this).attr("name");
                        k++;
                    }
                }
            }
            else {
                jQuery(this).val(userAnsArray[k]);
                k++;
            }
        });

        document.getElementById("tblWorkSheetMain").innerHTML = '';
        document.getElementById("outerQuesDiv").style.display = 'none';
        document.getElementById("outerScoreDiv").style.display = '';
    }
    if (timedTestVersion == 3) {
        var minNoOfQues = document.getElementById('noOfQuesInTimedTest').value;
    }
    for (var i = 0; i < noofques; i++) {
        userResponse = '';
        b = '';
        var isAnswered = false;
        qno = i + 1;
        correct = 0;
        quesType = document.getElementById('quesType' + qno).value;
        if (quesType == "Blank") {
            ansStr = ansArray[i].split("|");
            var flag = 0;
            for (var j = 0; j < ansStr.length; j++) {
                ans = ansStr[j];
                waysToAns = ans.split("~");
                waysToAns = unique(waysToAns);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)
                var objStr = 'txtAns' + qno + '_' + (j + 1);
                b = document.getElementById(objStr).value;
                userResponse += b + "|";

                if (b != "")
                    isAnswered = true;
                b = b.replace(/\\/g, "/");
                for (var k = 0; k < waysToAns.length; k++) {
                    if (b == '' && waysToAns[k] == '') {
                        flag++;
                        break;
                    }
                    else {
                        b1 = trim(b);
                        ans_blank = trim(waysToAns[k]);
                        if (ans_blank != "" && b1 != "" && !isNaN(b1) && !isNaN(ans_blank)) {
                            b1 = parseFloat(b1);
                            ans_blank = parseFloat(ans_blank);
                        }
                        if (b1 == ans_blank) {
                            flag++;
                            break;
                        }
                    }
                }
                document.getElementById(objStr).disabled = true;
            }
			ans = ansArray[i];
            if (flag == ansStr.length)
                correct = 1;
            userResponse = userResponse.substring(0, userResponse.length - 1);
        }
        else if (quesType == "I") {
            var flashMovie = getFlashMovieObject("simplemovie" + qno);
            try {
                flashMovie.SetVariable("Completed", 1);
                var userResp = flashMovie.GetVariable("userResponse");
                userResponse = userResp;
                if (userResp != '')
                    isAnswered = true;
            } catch (err) { }
            ans = ansArray[i];
            if (!isNaN(ans) && !isNaN(userResp)) {
                userResp = parseFloat(userResp);
                ans = parseFloat(ans);
            }
            if (ans == userResp)
                correct = 1;
        }
        else if (quesType == "D") {
            var correct = 1;ans = ansArray[i];
            ansStr = ansArray[i].split("|");
            var totalDropdown = ansStr.length;
			
            for (var d = 0; d < totalDropdown; d++) {
                var objStr = 'lstOpt' + (i + 1) + '_' + d;
				
				b = document.getElementById(objStr).value;
				b = b.replace("Yes", "y"); 
				b = b.replace("No", "n"); 
                userResponse += b + "|";
                var userResponsecheck = document.getElementById(objStr).selectedIndex;
                document.getElementById(objStr).disabled = true;
                if (ansStr[d] != userResponsecheck)
                    correct = 0;
            }
        }
        else {
            ans = ansArray[i];
            obj = eval("document.frmTimedTest.ansRadio" + qno);
            for (r = 0; r < obj.length; r++) {
                if (obj[r].checked)
                    b = obj[r].value;
                obj[r].disabled = true;
            }
            userResponse = b;
            if (b == ans)
                correct = 1;
            if (b != "")
                isAnswered = true;
        }

        if (isAnswered)
            noOfQuesAttempted++;

        if (timedTestVersion != 2) //for showing correct and wrong at the end.....not in second version
        {
            if (correct) {
                if (timedTestVersion == 3 && qno <= minNoOfQues) {
                    right++;
                }
                else if (timedTestVersion != 3) {
                    right++;
                }
                document.getElementById('spnQno' + qno).innerHTML += "<img src='assets/timedTest/right.gif'>";
                document.getElementById('result' + qno).value = 1;
            }
            else {
                document.getElementById('spnQno' + qno).innerHTML += "<img src='assets/timedTest/wrong.gif'>";
                document.getElementById('result' + qno).value = 0;
            }
        }
        else if (timedTestVersion == 2) // for version 2
        {
            if (correct) {
                right++;
                document.getElementById('result' + qno).value = 1;
            }
            else {
                document.getElementById('result' + qno).value = 0;
            }
        }
		if (quesType == "D") {
			userResponse = userResponse.substring(0, userResponse.length-1);
		}
        document.getElementById('userResp' + qno).value = userResponse;
		document.getElementById('correctAns' + qno).value = ans;
    }
    document.getElementById('noOfQuesAttempted').value = noOfQuesAttempted;
    if (timedTestVersion == 3) {
        var per = Math.round((right / minNoOfQues) * 100);
    }
    else if (timedTestVersion != 3) {
        var per = Math.round((right / noofques) * 100);
    }
    var msg1 = '', msg2 = '';

    if (per >= 75) {
        msg1 = i18n.t("timedTestPage.finalScorePass", { per: per, right: right, noofques: noofques });
        var noOfSparkies = 0;
        var attemptNo = document.getElementById('attemptNo').value;
        if (attemptNo == 1)
            noOfSparkies = 5;
        else if (attemptNo == 2)
            noOfSparkies = 4;
        else if (attemptNo == 3)
            noOfSparkies = 3;
        else if (attemptNo == 4)
            noOfSparkies = 2;
        else if (attemptNo >= 5)
            noOfSparkies = 1;
        if (jQuery("#quesCategory").val() == "wildcard" || jQuery("#quesCategory").val() == "comprehensive")
            noOfSparkies = 3;

        if (noOfSparkies > 0) {
            if (document.getElementById('cls').value < 8 || rewardSystem == 1) {
                i18n.t("timedTestPage.sparkieWon", { noOfSparkies: noOfSparkies });
            }
            else {
                if (rewardSystem == 1) {
                    i18n.t("timedTestPage.sparkieWon", { noOfSparkies: noOfSparkies });
                } else {
                    msg2 += i18n.t("timedTestPage.rewardWon", { noOfSparkies: parseInt(noOfSparkies) * 10 });
                }
            }
        }
    }
    else {
        msg1 = i18n.t("timedTestPage.finalScoreFail", { per: per, right: right, noofques: noofques });
        if (jQuery("#quesCategory").val() != "wildcard" && jQuery("#quesCategory").val() != "comprehensive")
            msg2 += i18n.t("timedTestPage.finalMsg");
        else {
            if (document.getElementById('cls').value < 8)
                msg2 += i18n.t("timedTestPage.finalMsgwildLower");
            else
                msg2 += i18n.t("timedTestPage.finalMsgwildHigher");
        }
    }


    var totalTime = parseInt(document.getElementById('txtDuration').value) * 60;
    var timeRemaining = (mins * 60) + secs;
    timeTaken = totalTime - timeRemaining;

    if (timedTestVersion == 1) {
        if (document.getElementById('cls').value <= 3) {
            document.getElementById('spnResultMsg').innerHTML = msg1 + msg2;
            document.getElementById('spnResultMsg').style.display = 'inline';
        }
        else {
            document.getElementById('spnResultMsg1').innerHTML = msg1;
            document.getElementById('spnResultMsg2').innerHTML = msg2;
        }
    }
    else if (timedTestVersion == 2) {
        document.getElementById('spnResultMsg').innerHTML = msg1 + msg2;
        jQuery("#boardLeftPart").hide();
        jQuery("#displayResult").show();
    }
    //temp condition	
    else if (timedTestVersion == 3) {
        document.getElementById('dispScoreTemp').innerHTML = msg1 + msg2;
    }
    //----------
    jQuery("#timdTestFeedback,#feedbackLink").show();
    jQuery("#sideBar").css("height", "750px");
    jQuery("html, body").animate({ scrollTop: jQuery("#timdTestFeedback").offset().top }, "slow");
    saveTimedTestResult(timeTaken, per, right, noOfQuesAttempted, noOfSparkies,quesType);
}

function checkAns(quesType, ansArray, qno, correctAns) {
    var correct = 0;
    var userResponse = '';
    var b = '';
    if (quesType == "Blank") {
        correct = 0;
        ansStr = ansArray.split("|");
        var flag = 0;
        for (var j = 0; j < ansStr.length; j++) {
            ans = ansStr[j];
            waysToAns = ans.split("~");
            waysToAns = unique(waysToAns);    //Remove the duplicates in the answer e.g. AB~ab would result into AB (since case insensitive comparison)
            var objStr = 'txtAns' + qno + '_' + (j + 1);
            b = document.getElementById(objStr).value;
            userResponse += b + "|";

            b = b.replace(/\\/g, "/");
            for (var k = 0; k < waysToAns.length; k++) {
                if (b == '' && waysToAns[k] == '') {
                    flag++;
                    break;
                }
                else {
                    b1 = trim(b);
                    ans_blank = trim(waysToAns[k]);
                    if (ans_blank != "" && b1 != "" && !isNaN(b1) && !isNaN(ans_blank)) {
                        b1 = parseFloat(b1);
                        ans_blank = parseFloat(ans_blank);
                    }
                    if (b1 == ans_blank) {
                        flag++;
                        break;
                    }
                }
            }
        }
        if (flag == ansStr.length)
            correct = 1;
    }
    else if (quesType == "D") {
		ans = ansStr[i];
        ansStr = ansArray.split("|");
        var totalDropdown = ansStr.length;
        for (var i = 0; i < totalDropdown; i++) {
            var objStr = 'lstOpt' + qno + '_' + i;
            var userResponse = document.getElementById(objStr).selectedIndex;
            if (ansStr[i] == userResponse)
                correct = 1;
            else {
                correct = 0;
                break;
            }
        }
    }
    else {
        ans = ansArray;
        obj = eval("document.frmTimedTest.ansRadio" + qno);
        for (r = 0; r < obj.length; r++) {
            if (obj[r].checked)
                b = obj[r].value;
            obj[r].disabled = true;
        }
        userResponse = b;
        if (b == ans)
            correct = 1;
    }
    correctAns = correctAns + correct;
    jQuery("#userScore").text(correctAns);
    return correctAns;
}

function saveTimedTestResult(timeTaken, perCorrect, quesCorrect, noOfQuesAttempted, noOfSparkies,quesType) {
    var params = "";
    params += "mode=timedtest";
    params += "&timeTaken=" + timeTaken;
    params += "&perCorrect=" + perCorrect;
    params += "&quesCorrect=" + quesCorrect;
    params += "&noOfQuesAttempted=" + noOfQuesAttempted;
    params += "&noOfSparkies=" + noOfSparkies;
    params += "&timedTestCode=" + document.getElementById('hdnTimedTestCode').value;
    params += "&timedTestDesc=" + document.getElementById('hdnTimedTestDesc').value;
    params += "&timedTestVersion=" + document.getElementById('timedTestVersion').value;
    params += "&quesCategory=" + jQuery("#quesCategory").val();
    params += "&attemptNo=" + jQuery("#attemptNo").val();
    params += "&autoSubmitUsed=" + autoSubmitUsed;
     var request = new Ajax.Request('controller.php',
		{
		    method: 'post',
		    parameters: params,
		    onSuccess: function (transport) {
		        userIdealCondition();
		        resp = transport.responseText;
		        if (resp == "dummyresponse")
		            redirect();
		        tmpArray = resp.split("~");
		        document.getElementById('mode').value = tmpArray[0];
		        document.getElementById('timedTestAttemptID').value = tmpArray[1];
		        //document.getElementById('lnkComment').style.display = "";
                if(perCorrect >= 75){
                    ddStatus = "pass";
                }else{
                    ddStatus = "fail";
                }
		        if (jQuery("#quesCategory").val() == "wildcard" || jQuery("#quesCategory").val() == "comprehensive") {
		            jQuery("#hdnmode").val("saveTimedTestResearchQuestions");
		            saveTimedTestResearchQues(1);
		        }
                else if(quesType == "D"){
                    jQuery("#hdnmode").val("saveTimedTestResearchQuestions");
                    saveTimedTestResearchQues(0);
                }
		        else {
		            jQuery("#btnContinue").show();
		            jQuery("#btnContinue").focus();
		            jQuery('#disableAutoSubmit,#disableAutoSubmit+label').hide();
		        }
				saveTimedTestComment(0);
		    },
		    onFailure: function () {
		        alert('Something went wrong while saving...');
		    }
		}
		
	);
}

function endSession() {
    if (timedTestVersion == 1) {
        if (document.getElementById('btnFinish').style.display == "none") //implies completed the timed test
            var msg = i18n.t("timedTestPage.endSessionMsg1");
        else
            var msg = i18n.t("timedTestPage.endSessionMsg2");
    }
    else if (timedTestVersion == 2) {
        if (document.getElementById('btnContinue').style.display == "") //implies completed the timed test
            var msg = i18n.t("timedTestPage.endSessionMsg1");
        else
            var msg = i18n.t("timedTestPage.endSessionMsg2");
    }
    else if (timedTestVersion == 3) {
        if (document.getElementById('outerQuesDiv').style.display == "none") //implies completed the timed test
            var msg = i18n.t("timedTestPage.endSessionMsg1");
        else
            var msg = i18n.t("timedTestPage.endSessionMsg2");
    }
    if (confirm(msg)) {
        processEndSessionAns(1);
    }
}

/*function endSession()
{
if(timedTestVersion==1)
{
document.getElementById('test').style.display = "none";
if(document.getElementById('btnFinish').style.display =="none") //implies completed the timed test
var msg = i18n.t("timedTestPage.endSessionMsg1");
else
var msg = i18n.t("timedTestPage.endSessionMsg2");
}
else if(timedTestVersion==2)
{
document.getElementById('test').style.display = "none";
if(document.getElementById('btnContinue').style.display =="") //implies completed the timed test
var msg = i18n.t("timedTestPage.endSessionMsg1");
else
var msg = i18n.t("timedTestPage.endSessionMsg2");
}
else if(timedTestVersion==3)
{
document.getElementById('test').style.display = "none";
if(document.getElementById('outerQuesDiv').style.display =="none") //implies completed the timed test
var msg = i18n.t("timedTestPage.endSessionMsg1");
else
var msg = i18n.t("timedTestPage.endSessionMsg2");
}
if(jQuery("#quesCategory").val()!="wildcard" && jQuery("#quesCategory").val()!="comprehensive")
document.getElementById('pnlComment').style.display = "none";
document.getElementById('pnlEndSessionMsg').style.display = "block";
document.getElementById('endSessionMsg').innerHTML = msg;
}*/

/*function processEndSessionAns(ans)
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
document.getElementById('frmTimedTest').action = "endSessionReport.php";
document.getElementById('frmTimedTest').submit();
}
else
{
if(timedTestVersion==3)
{
var timeRemainFull	=	document.getElementById('spnTime').innerHTML;
var flashTimer	=	'';
var timeRemains	=	timeRemainFull.split(":");
var timeRemain	=	parseInt(timeRemains[0])*60 + parseInt(timeRemains[1]);
					
flashTimer	=	'<OBJECT id="CircularTimer" height="150" width="150" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">';
flashTimer	+=	'<param name="movie" value="images/Timer.swf?timeInSec=';
flashTimer	+=	timeRemain;
flashTimer	+=	'">';
flashTimer	+=	'<param name="movie" value="images/Timer.swf?timeInSec=';
flashTimer	+=	timeRemain;
flashTimer	+=	'">';
flashTimer	+=	'<PARAM NAME="quality" VALUE="high">';
flashTimer	+=	'<PARAM NAME="quality" VALUE="high">';
flashTimer	+=	'<PARAM name="wmode" VALUE="transparent">';
flashTimer	+=	'<PARAM NAME="bgcolor" VALUE="#FFFFFF">';
flashTimer	+=	'<PARAM NAME="allowScriptAccess" VALUE="always"><center>';
flashTimer	+=	'<EMBED src="images/Timer.swf?timeInSec='+timeRemain+'" swliveconnect="true" quality=high bgcolor="#FFFFFF" WMODE="transparent" allowScriptAccess="always" WIDTH="150" HEIGHT="150" NAME="CircularTimer" ALIGN=""></';
flashTimer	+=	'EMBED></';
flashTimer	+=	'OBJECT>';
}
document.getElementById('pnlEndSessionMsg').style.display = "none";
if(timedTestVersion!=2)
{
document.getElementById('test').style.display = "";
}
if(timedTestVersion==3)
{
document.getElementById('clockFlashTimer').innerHTML	=	"";
document.getElementById('clockFlashTimer').innerHTML	=	flashTimer;
}
if(timedTestVersion==2)
{
document.getElementById('test').style.display = "";
}
}
}*/

function processEndSessionAns(ans) {
    code = 1;
    document.getElementById('mode').value = code;
    var params = "mode=endsession";
    params += "&code=" + code;
    try {
        var request = new Ajax.Request('controller.php',
		{
		    method: 'post',
		    parameters: params,
		    onSuccess: function (transport) {
		        resp = transport.responseText || "no response text";
		    },
		    onFailure: function () {
		        //alert('Something went wrong...');
		    }
		}
		);
    }
    catch (err) { }
    document.getElementById('frmTimedTest').action = "endSessionReport.php";
    setTryingToUnload();
    document.getElementById('frmTimedTest').submit();
}



function trim(query) {
    //return query.replace(/^\s+|\s+$/g,"");
    var s = query.replace(/\s+/g, "");
    return s.toUpperCase();
}

function toggleState(state) {
    var obj = document.getElementsByTagName("input");
    for (var i = 0; i < obj.length; i++) {
        if (obj[i].id.substring(0, 6) == "txtAns") {
            obj[i].disabled = state;
        }
    }
}

function findPos(obj) {
    var curleft = curtop = 0;
    if (obj.offsetParent) {
        do {
            curleft += obj.offsetLeft;
            curtop += obj.offsetTop;

        } while (obj = obj.offsetParent);

    }
    return curleft + "~" + curtop;
}

function setFracPos() {
    var fracObj = document.getElementsByTagName('img');
    for (var i = 0; i < fracObj.length; i++) {
        if (fracObj[i].id.substring(0, 3) == "img") {
            var index = fracObj[i].id.substring(3);

            offset = findPos(document.getElementById('img' + index)).split("~");
            offsetLeftPos = parseInt(offset[0]);
            offsetTopPos = parseInt(offset[1]);
            document.getElementById('fracbound' + index).style.top = offsetTopPos + 12;
            document.getElementById('fracbound' + index).style.left = offsetLeftPos;
        }
    }
}
function getNextInFlow() {
    jQuery("#frmTimedTest").attr("action", "controller.php");
    jQuery("#mode").val("comprehensiveAfterActivity");
    setTryingToUnload();
    jQuery("#frmTimedTest").submit();
}

function getNextInKstFlow() {
    setTryingToUnload();
    document.getElementById('kstdiagnosticTest').submit();
}

function redirect() {
    if (document.getElementById('practiceModuleRedirection')){
        closeDD=1;return false;
    }
	var gameRedirect = document.getElementById('gameRedirection').value;
    var comprehensiveModuleFlag = document.getElementById('comprehensiveModuleFlag').value;	
    if(comprehensiveModuleFlag !='')
    {
        var mode='diagnosticTest';
        document.getElementById('quesCategory').value = mode;
    }
    else
        var mode = document.getElementById('qcode').value;

    if (mode != '-2' && mode != '-3' && mode != '-1' && mode != '-8' && mode!='diagnosticTest') {
        mode = document.getElementById('mode').value;
        mode = mode.replace(/^\s*|\s*$/g, "");
    }
    if (mode == '-2' || mode == '-3' || mode == '-5' || mode == '-6') {
        document.getElementById('mode').value = mode;
        document.getElementById('frmTimedTest').action = "endSessionReport.php";
    }
    else if (mode == '-8' && gameRedirect!=1) {
        document.getElementById('frmTimedTest').action = "classLevelCompletion.php";
    }
    else if (mode == '-1') {
        document.getElementById('mode').value = mode;
        document.getElementById('frmTimedTest').action = "dashboard.php";
    }
    else if (document.getElementById('remedialMode').value == 1) {
        document.getElementById('frmTimedTest').action = "remedialItem.php";
    }
    else {
		if(gameRedirect==1)
		{
			setTryingToUnload();
			window.location.href = "controller.php?mode=game";
		}
		
		else
        document.getElementById('frmTimedTest').action = "question.php";
    }
	
	if(gameRedirect!=1)
	{
		setTryingToUnload();
   	 	document.getElementById('frmTimedTest').submit();
	}
   
}

function setFocus(obj) {
    if (!obj.disabled)
        obj.focus();
}

function unique(a) {
    var r = new Array();
    o: for (var i = 0, n = a.length; i < n; i++) {
        for (var x = 0, y = r.length; x < y; x++) {

            if (trim(r[x]) == trim(a[i]))
                continue o;
        }
        r[r.length] = a[i];
    }
    return r;
}
/*function showCommentBox()
{
//document.getElementById('test').style.display = "none";
document.getElementById('pnlEndSessionMsg').style.display = "none";
document.getElementById('pnlComment').style.display = 'block';
}*/
function showCommentBox() {
    jQuery("#commentMain").show();
}
function hideCommentBox() {
    jQuery("#commentMain").hide();
}

/*function hideCommentBox() {
//document.getElementById('test').style.display = "block";
document.getElementById('pnlEndSessionMsg').style.display = "none";
document.getElementById('pnlComment').style.display = 'none';
}*/
function saveTimedTestComment(fromComments) {
	jQuery("#fromComments").val(fromComments);
	jQuery("#hdnmode").val("saveTimedTestQuestions");
    var params = Form.serialize("frmTimedTestData");
    params += "&comment=" + document.getElementById('comment').value;
    if (document.getElementById('comment').value == "" && fromComments==1) {
        alert("You can't leave the feedback blank.");
        return false;
    }
    try {
        var request = new Ajax.Request('controller.php',
		{
		    method: 'post',
		    parameters: params,
		    onSuccess: function (transport) {
		        //alert(transport.responseText);
				if(fromComments==1)
				{
					hideCommentBox();
					jQuery("#feedbackLink").css("visibility", "hidden");
				}
		        //do nothing;
		        //window.status = "Update time taken for explanation";
		    }
		}
		);
    }
    catch (err) { alert("updateTimeTakenForExpln " + err.description); }
}
function saveTimedTestResearchQues(isResearch) {
    var params = Form.serialize("frmTimedTestData");
    try {
        var request = new Ajax.Request('controller.php',
		{
		    method: 'post',
		    parameters: params,
		    onSuccess: function (transport) {
		        if (isResearch){
                    jQuery("#qcode").val(-1);
		            jQuery("#quesCategory").val("normal");
                }
		        jQuery("#btnContinue").show();
		        jQuery("#btnContinue").focus();
		    }
		}
		);
    }
    catch (err) { alert("updateTimeTakenForExpln " + err.description); }
}

function userIdealCondition() {
    clearInterval(startIdealTimeout);
    startIdealTimeout = setInterval(function () {
        ansSubmited++;
        if (ansSubmited == 120)
            redirect();
    }, 1000);
}

function CheckIfNumeric(evt) {
    evt = (evt) ? evt : window.event;
    if(/Firefox/.test(window.navigator.userAgent) && evt.keyCode!=0) {
        return true;
    }
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode != 46 && charCode != 47 && charCode != 45 && (charCode < 48 || charCode > 57) || (charCode==45 && (evt.target.selectionStart!=0 || evt.target.value.indexOf('-')>=0)) || (charCode==46 && evt.target.value.indexOf('.')>=0) || (charCode==47 && (evt.target.selectionStart==0 || evt.target.value.indexOf('/')>=0))) {
        if (!keypadPresent)
        {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "Please enter only numbers.", 
                    type: 'alert', 
                    promptId: "numberValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_numberValidation").remove();
                    }
                });
            }
        }
        return false;
    }
    return true;
}
