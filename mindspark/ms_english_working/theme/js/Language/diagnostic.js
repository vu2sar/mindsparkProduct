/**
 * @author Kalpesh Awathare
 *
 * load this script only for taking diagnostic test and showing its answers.
 */

var dtQuestions = new Array();
//holds all the question data
var dt = {
    'quesNo' : 0,
    'currentQuestionCompletion' : 0,
    'userID' : 0,
    'maxAttempted' : 0,
    'timeTaken' : 0,
    'userName' : ''
};

var essayReportAlreadyShown = false;
var pqData = {};
var hideTimeoutID = null;

var diagnosticTest = {
    var _myself = this; 
    fetchQuestions : function() {
        $.ajax({
            context : this,
            url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/getTestFlowData',
        }).done(function(data){ Helpers.ajax_response( _myself.getTestFlowData , data , [] ); });
    },
    getTestFlowData : function(data, extraParams)
    {
        _myself.initialize(data);
    },
    initialize : function(data) {
        dtQuestions = JSON.parse(data);
        for (var i = 0; i < dtQuestions.length; i++) {
            dtQuestions[i].value = dtQuestions[i].value.trim();
        }
        dt.maxAttempted = this.getMaxAttempted();
        dt.quesNo = dt.maxAttempted;

        if (sessionData.mode == 'diagnostic') {
            dt.timeTaken = parseInt(sessionData.timeTaken);
            if (!dt.timeTaken || dt.timeTaken == 0) {
                dt.timeTaken = 0;
                this.showInstructions();
            } else {
                var minutesLeft = Math.ceil(((90 * 60) - dt.timeTaken) / 60) - 1;
                if (dt.timeTaken < 5400) {
                    alert("Welcome back, " + dt.userName + "! You can continue to attempt the test. You have " + minutesLeft + " minutes to finish it.");
                    if (dt.timeTaken >= 5100)
                        $('#testTimer').css('color', '#ED1C24');
                } else {
                    diagnosticTestComplete = true;
                    alert('You have already completed this test');
                    logOut();
                }
            }

            if (!diagnosticTestComplete)
                this.startTimerInterval();
        } else if (sessionData.mode == 'report') {
            dt.quesNo = 0;
        }
        this.setDom();
        this.setBindings();

        diagnosticTest.setQuestion(dtQuestions[dt.quesNo]);
    },
    setDom : function() {
        stopAndHideOtherActivities();

        sidebarToggle(false);
        $('#sidebarToggle').hide();

        $('.the_classroom').show();
        $('#diagnosticTest').show();
        $('#legendContainer').show();
        $('#diagnosticTestContainer').show();
        $('#flagger').show();

        this.setDiagnosticProgressPanel();
    },
    setBindings : function() {
        var textarea = document.getElementById('essay');
        textarea.addEventListener('keydown', Helpers.checkNumWords);
        textarea.addEventListener('keyup', Helpers.checkNumWords);

        $('.bubble').bind('click', function() {
            var toLocation = parseInt($(this).attr('increment')) - 1;
            if (toLocation <= dt.maxAttempted) {
                dt.quesNo = toLocation;
                diagnosticTest.setQuestion(dtQuestions[dt.quesNo]);
            }
        });

        $('.openInternalQuestionsPanel').bind('mouseover', function() {
            if ($(this).hasClass('unAttempted')) {
                return;
            }
            clearTimeout(hideTimeoutID);
            var data = $(this).attr('data');
            $('.passageQuestionsBubbleContainer').hide();
            var bubblesContainer = $('#passageQuestionsBubbleContainer' + data);
            bubblesContainer.show();
            bubblesContainer.css({
                'left' : $(this).offset().left - bubblesContainer.width() / 2 + $(this).width() / 2 + 'px',
                'top' : $('#diagnosticProgressPanel').height() + 5 + 'px'
            });
            $('#passageQuestionsBubbleContainer' + data).show();
        });

        $('.passageQuestionsBubbleContainer').bind('mouseover', function() {
            clearTimeout(hideTimeoutID);
        });

        $('.openInternalQuestionsPanel,.passageQuestionsBubbleContainer').bind('mouseout', function() {
            hideTimeoutID = setTimeout(function() {
                $('.passageQuestionsBubbleContainer').hide();
            }, 1500);
        });

        $('#rightButton').bind('click', function() {
            if (dtQuestions[dt.quesNo].quesType == 'passage') {
                if ( typeof passageObject.controls.nextPage() != 'number')
                    diagnosticTest.nextQuestion();
            } else {
                diagnosticTest.nextQuestion();
            }
        });

        $('#leftButton').bind('click', function() {
            if (dtQuestions[dt.quesNo].quesType == 'passage') {
                if ( typeof passageObject.controls.previousPage() != 'number')
                    diagnosticTest.previousQuestion();
            } else {
                diagnosticTest.previousQuestion();
            }
        });
    },
    getMaxAttempted : function() {
        var maxAttempted = 0;
        for (var i = 0; i < dtQuestions.length; i++) {
            if (dtQuestions[i].info.info != 'NA') {
                maxAttempted = i;
            }
        }

        if (maxAttempted >= dtQuestions.length) {
            maxAttempted = dtQuestions.length - 1;
        }
        return maxAttempted;
    },
    setQuestion: function(object){
        if(previousQuestion){
            diagnosticTest.saveQuestion();
        }
        stopAndHideOtherActivities(true);
        setQuestion(object);
        diagnosticTest.setCurrentInPanel();
    },
    setDiagnosticProgressPanel : function() {
        var insertQuestionsInside = false;
        var internalPassageElement;
        var pCounter = 0;
        var lCounter = 0;
        for (var i = 1; i <= dtQuestions.length; i++) {
            var currentQuestion = dtQuestions[i - 1];
            if (insertQuestionsInside) {
                if (currentQuestion.quesType === 'passageQues') {
                    $(internalPassageElement).append(this.createProgressBubble('Q' + (i - (pCounter + lCounter)), i));
                } else {
                    insertQuestionInside = false;
                }
            }

            if (currentQuestion.quesType === 'passage') {
                if (currentQuestion.quesTypeLabel == 'passage')
                    pCounter++;
                insertQuestionsInside = true;
                internalPassageElement = document.createElement('div');
                internalPassageElement.className = 'passageQuestionsBubbleContainer';
                internalPassageElement.id = 'passageQuestionsBubbleContainerP' + pCounter;
                var label = 'P' + pCounter;
                var data = 'P' + pCounter;
                if (currentQuestion.quesTypeLabel == 'Listening') {
                    lCounter++;
                    label = 'L' + lCounter;
                    internalPassageElement.id = 'passageQuestionsBubbleContainerL' + lCounter;
                    data = 'L' + lCounter;
                }
                var pBubble = this.createProgressBubble(label, i);
                $(pBubble).attr('data', data);
                $(pBubble).addClass('openInternalQuestionsPanel');
                $('#innerBubblesHolder').append(internalPassageElement);
                $('#progressPanelBubblesContainer').append(pBubble);
            } else if (currentQuestion.quesType === 'freeQues') {
                $('#progressPanelBubblesContainer').append(this.createProgressBubble('Q' + (i - (pCounter + lCounter)), i));
            } else if (currentQuestion.quesType == 'essay') {
                $('#progressPanelBubblesContainer').append(this.createProgressBubble('W', i));
            }
        }
    },
    createProgressBubble : function(html, increment) {
        var bubble = document.createElement('div');
        bubble.innerHTML = html;
        bubble.className = 'bubble';
        $(bubble).attr('increment', increment);

        if (dtQuestions[increment - 1].info.info == "SKIP") {
            $(bubble).addClass('skipped');
        }

        if (dtQuestions[increment - 1].info.info == "FLAGGED") {
            $(bubble).addClass('flagged');
        }

        if (increment > (dt.maxAttempted + 1)) {
            $(bubble).addClass('unAttempted');
        }

        return bubble;
    },
    setCurrentInPanel : function() {
        var bubble = $('.bubble[increment="' + (dt.quesNo + 1) + '"]');
        $('.bubble').removeClass('current');
        $('.bubble').removeClass('innerCurrent');
        $(bubble).removeClass('unAttempted');
        $(bubble).addClass('current');
        if ($(bubble).parent().hasClass('passageQuestionsBubbleContainer')) {
            $(bubble).parent().next('.bubble').addClass('innerCurrent');
        }
    },
    skipQuestion : function(question) {
        if (question.quesType == 'freeQues' || question.quesType == 'passageQues') {
            if (question.info.info == 'NA') {
                question.info.info = 'SKIP';
            }

            $('.bubble[increment="' + (dtQuestions.indexOf(question) + 1) + '"]').addClass('skipped');
        }
    },
    unSkipQuestion : function(question) {
        if (question.quesType == 'freeQues' || question.quesType == 'passageQues') {
            question.info.info = 'A';

            var bubble = $('.bubble[increment="' + (dtQuestions.indexOf(question) + 1) + '"]');
            $(bubble).removeClass('skipped');
        }
    },
    previousQuestion : function() {
        if (dt.quesNo != 0)
            dt.quesNo--;
        
        diagnosticTest.setQuestion(dtQuestions[dt.quesNo]);
    },
    nextQuestion : function() {
        dt.quesNo++;
        
        stopAndHideOtherActivities(true);
        if (dt.quesNo > dt.maxAttempted) {
            dt.maxAttempted = dt.quesNo;
        }

        if (dt.quesNo < dtQuestions.length) {
            setQuestion(dtQuestions[dt.quesNo]);
        } else {
            this.showEndDiagnosticTest();
        }
    },
    flagQuestion : function() {
        var bubble = $('.bubble[increment="' + (dt.quesNo + 1) + '"]');

        if ($(bubble).hasClass('flagged')) {
            $(bubble).removeClass('flagged');
            flagType = 'UNFLAGGED';
            if ($(bubble).parent().hasClass('passageQuestionsBubbleContainer')) {
                var siblings = $(bubble).siblings();
                var remove = true;
                for (var i = 0; i < siblings.length; i++) {
                    if ($(siblings[i]).hasClass('flagged')) {
                        remove = false;
                        break;
                    }
                }
                if (remove)
                    $(bubble).parent().next('.bubble').removeClass('innerFlagged');
            }
        } else {
            $(bubble).addClass('flagged');
            flagType = 'FLAGGED';
            if ($(bubble).parent().hasClass('passageQuestionsBubbleContainer')) {
                $(bubble).parent().next('.bubble').addClass('innerFlagged');
            }
        }

        $.ajax({
            //url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/flagUnFlagQues/' + flagType + '/' + dt.userID + '/' + dtQuestions[dt.quesNo].value.split('||')[0] + '/' + (dt.quesNo + 1)
            url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/flagUnFlagQues/' + flagType + '/' + dtQuestions[dt.quesNo].value.split('||')[0] + '/' + (dt.quesNo + 1)
        });
    },
    showEndDiagnosticTest : function() {
        if (sessionData.mode == 'report') {
            dt.quesNo--;
            return;
        }

        dt.quesNo--;
        if (currentQuestion.quesTypeLabel == 'essay') {
            this.saveEssay();
        }
        $('#diagnosticProgressPanel').hide();
        $('#flagger').hide();
        $('#legendContainer').hide();
        clearInterval(dtTimerIntervalId);
        stopAndHideOtherActivities();
        //$('#diagnosticEnd').show();
        Helpers.prompt({
            text : 'Invalid Session',
            okText : 'FINISH',
            okFunction : diagnosticTest.endDiagnosticTest,
            noClose : true,
        });
        var html = '<div id="diagnosticEnd" class="none"><div id="endNote">You have reached the end of the test. To finish and submit your responses click on FINISH, to return and change your responses click on BACK.</div><button id="finishTest" onclick="diagnosticTest.endDiagnosticTest()">FINISH</button><button id="goBackToTest" onclick="diagnosticTest.resumeTest()">BACK</button></div>';

        $('#loader').hide();
    },
    endDiagnosticTest : function() {
        $('#feedbackForm').show();
        diagnosticTest.saveEssay(1);
        if (sessionData.mode == 'demo') {
            $('#feedbackClose').show();
        }
    },
    resumeTest : function() {
        diagnosticTest.setQuestion(dtQuestions[dt.quesNo]);
        diagnosticTest.startTimerInterval();
        $('#diagnosticProgressPanel').show();
        $('#flagger').show();
        $('#legendContainer').show();
    },
    showInstructions : function() {
        dt.seeingInstructions = true;
        $('#modalBlocker').show();
        $('#instructions').show();
    },
    saveQuestion : function(response) {
        if (sessionData.mode == 'report') {
            return;
        }
        
        if(!/freeQues|passageQues/.test(currentQuestion.quesType)){
            return;
        }

        pqData = dtQuestions[dt.quesNo];
        pqData.questionNo = dt.quesNo + 1;
        pqData.location = dt.quesNo;
        pqData.qcode = dtQuestions[dt.quesNo].value.split('||')[0];

        pqData.completed = questionObject.model.currentQuestion.completed;
        pqData.userResponse = questionObject.model.currentQuestion.userRespones;
        pqData.correct = questionObject.model.currentQuestion.correct;
        pqData.extraParam = questionObject.model.currentQuestion.extraParam;
        pqData.score = questionObject.model.currentQuestion.score;

        if (pqData.completed == 1) {
            pqData.info.info = 'A';
            diagnosticTest.unSkipQuestion(pqData);
        } else {
            if (pqData.info.userResponse == null || pqData.info.userResponse == '') {
                pqData.info.info = 'SKIP';
                //info . SKIP is passed in the info rather than the userResponse. Need to
                diagnosticTest.skipQuestion(pqData);
            }
        }

        $.ajax({
            type : 'POST',
            url : Helpers.constants['CONTROLLER_PATH'] + 'diagnosticTest/submitResponse',
            data : pqData
        });
    },
    startTimerInterval : function() {
        this.dtTimerIntervalId = setInterval(function() {
            if ($('#modalBlocker').is(':visible')) {
                if (!dt.seeingInstructions)
                    return;
            }

            dt.timeTaken++;

            var countDownTime = 5400 - dt.timeTaken;

            var minutes = Math.floor(countDownTime / 60);
            var seconds = countDownTime % 60;

            if (seconds < 10) {
                seconds = '0' + seconds;
            }

            if (minutes < 10) {
                minutes = '0' + minutes;
            }

            if (minutes == 5 && seconds == 0) {
                $('#testTimer').css('color', '#ED1C24');
                alert('You have 5 minutes to complete the test.');
            }

            $('#dtMinutes').html(minutes);
            $('#dtSeconds').html(seconds);

            if (dt.timeTaken % 10 == 0) {
                logTime();
            }

            if (parseInt(minutes) == 0 && seconds == 0) {
                diagnosticTest.beforeLogOut();
                alert('Sorry! Your time is up');
                clearInterval(dtTimerIntervalId);
                logOut();
            }
        }, 1000);
    },
    initializeReport : function() {
        $('#testTimer').hide();
        $('#alertButton').hide();
        $('#helpButton').hide();
        $('#saveEssay').hide();
        $('#flagger').hide();
        clearInterval(this.dtTimerIntervalId);
    },
}; 