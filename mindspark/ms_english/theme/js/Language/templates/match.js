var matchQuestionObject;

function matchQuestion(view) {
    var allSndArr= new Array();
    var matchHtmlContent = '';
    var totalOptions = view.quesDataArr.totalOptions;
    var leftAnswers = [];
    var dbAnswer=[];
    var rightAnswers=[];
    var userResponse = '';
    var correct = 0;
    var timerNew = [];
    var options = [];
    var right;
    var maxHeight = 0;
    var leftSubType = view.quesDataArr.optSubType.split("And")[0];
    var rightSubType = view.quesDataArr.optSubType.split("And")[1];
    var quesLeftSubType = view.quesDataArr.quesSubType.split("And")[0];
    var quesRightSubType = view.quesDataArr.quesSubType.split("And")[1];
    var wrongAns=[];
    var correctAns=[];
    var createColumns = function() {
        // pushing question sound
        if(view.quesDataArr.quesAudioIconSound != "") {  
            allSndArr.push(view.quesDataArr.quesAudioIconSound);
        }
        // adding question text
        matchHtmlContent = '<div id="matchContainer"><div id="matchTitle">' + view.quesDataArr.quesText + '</div>';
        // showing audio icon in question
        if(quesLeftSubType == "audioicon" || quesRightSubType == "audioicon") {  
            matchHtmlContent += '<img src="' + assetsPath + 'soundIconGrey.png" class="audioQues"/><br>';
        }
        // showing image in question
        if(quesLeftSubType == "image" || quesRightSubType == "image") {
            matchHtmlContent += '<img src="' + view.imagePath + view.quesDataArr.quesImage+'" class="imageQues"/><br>';
        }

        for ( i = 0; i < totalOptions; i++) {
            options[i] = optLabelForCorrectAns[i];
        }
        
        Helpers.randomize(options);
        for ( i = 0; i < totalOptions; i++) {
            var optionText = "view.quesDataArr.option_" + options[i];
            var optionsArr = eval(optionText).split('~');
            dbAnswer.push(eval(optionText));
            var leftoption = $.trim(optionsArr[0].replace("/",""));
            var rightoption = $.trim(optionsArr[1].replace("/",""));
            if (i == 0)
                matchHtmlContent += '<div style="position:relative; margin-top:-60px"><div id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left" data-val="'+handleQuotes(leftoption)+'"><span>' + leftoption + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
            else
                matchHtmlContent += '<div style="position:relative;"><div id="left' + i + '" data-pos=' + i + ' data-side="Left" class="columns left" data-val="'+handleQuotes(leftoption)+'"><span>' + leftoption + '</span><div class="labels leftLabels">'+parseInt(i+1)+'</div></div>';
            matchHtmlContent += '<div id="right' + i + '" data-pos=' + i + ' data-side="Right"  class="columns right" data-val="'+handleQuotes(rightoption)+'"><span>' + rightoption + '</span></div></div>';
        }
        $(view.container).find('div').css({
            "height" : "800px"
        });

        matchHtmlContent += '<div id="correctWrongSign"> </div></div>';
        $(view.container).find('div').append(matchHtmlContent);

        $("[data-side='Left'], [data-side='Right']").draggable({
            revert : "invalid",
            containment : $(view.container)
        });

        $("[data-side='Left']").droppable({
            accept : "[data-side='Right']",
            tolerance : "touch",
            drop : handleDrop
        });

        $("[data-side='Right']").droppable({
            accept : "[data-side='Left']",
            tolerance : "touch",
            drop : handleDrop
        });
        shuffleHTML();
        for(i=0; i<totalOptions; i++) {
            //answers[$("#left"+i).attr("data-val")+'$'+$("#left" + i).find('.labels').html()] = $("#right"+i).attr("data-val");
            rightAnswers.push($("#right"+i).attr("data-val"));
            leftAnswers.push($("#left"+i).attr("data-val"));
            if(leftSubType == "image" || rightSubType == "image") {  // Handling images in options
                maxHeight = 120;
                if(leftSubType == "image") {
                    var imgName = $("[data-pos='"+i+"'].left").attr("data-val");
                    var imgTag = "<img src="+view.imagePath+imgName+" />";
                    $("[data-pos='"+i+"'].left span").html(imgTag);
                }
                if(rightSubType == "image") {
                    var imgName = $("[data-pos='"+i+"'].right").attr("data-val");
                    var imgTag = "<img src="+view.imagePath+imgName+" />";
                    $("[data-pos='"+i+"'].right span").html(imgTag);
                }
            }
            if(leftSubType == "audio" || rightSubType == "audio") {  // Handling audio in options
                maxHeight = 120;
                if(leftSubType == "audio") {
                    var audioName = $("[data-pos='"+i+"'].left").attr("data-val");
                    allSndArr.push(audioName);
                    var imgTag = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                    $("[data-pos='"+i+"'].left span").html(imgTag);
                }
                if(rightSubType == "audio") {
                    var audioName = $("[data-pos='"+i+"'].right").attr("data-val");
                    allSndArr.push(audioName);
                    var imgTag = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                    $("[data-pos='"+i+"'].right span").html(imgTag);
                }
            }
            if(leftSubType == "text" && rightSubType == "text") {    // Handling text in options
                if(maxHeight < $("[data-pos='"+i+"'].left").outerHeight() || maxHeight < $("[data-pos='"+i+"'].right").outerHeight()) {
                    if($("[data-pos='"+i+"'].left").outerHeight() < $("[data-pos='"+i+"'].right").outerHeight())
                        maxHeight = $("[data-pos='"+i+"'].right").outerHeight()+10;
                    else
                        maxHeight = $("[data-pos='"+i+"'].left").outerHeight()+10;
                }
            }
        }
        
        setBindings();
        $(".columns").css({
            "height":maxHeight+"px" 
        });
        var initialHeight = 70;
        for(i=1;i<totalOptions;i++)
        {
            initialHeight = initialHeight + maxHeight+10;
            $("[data-pos="+i+"]").css({"top":initialHeight+"px"});
        }
    };

    // handling single and double quotes
    var handleQuotes = function(str) {
        var len = str.length;
        if(str.indexOf("\"")!=-1) {
            str = str.replace(/"/g,"");
        }
        if(str.indexOf("'")!=-1) {
            str = str.replace(/'/g,"");
        }
        return str;
    };
    var setBindings = function() {
        $("#loader").hide();
        // enabling click for questions with audio icon
        if(quesLeftSubType=="audioicon" || quesRightSubType=="audioicon") {
            $(".audioQues").click(function(){
                Helpers.sndPlayLoadErrEnd(view.soundPath, "allAudio", view.quesDataArr.quesAudioIconSound);
            });
        }
        // enabling click for options with audio
        if(leftSubType == "audio" || rightSubType == "audio") {  
            Helpers.loadAllSounds(allSndArr,null, view.soundPath);
            if(leftSubType == "audio") {
                $("#matchContainer .columns.left").click(function(){
                    Helpers.sndPlayLoadErrEnd(view.soundPath, "allAudio", $(this).attr("data-val"));
                });
            }
            if(rightSubType == "audio") {
                $("#matchContainer .columns.right").click(function(){
                     Helpers.sndPlayLoadErrEnd(view.soundPath, "allAudio", $(this).attr("data-val"));
                });
            }
        }
    };
    var submitButton = function () {
        correct = 0;
        for (var i = 0; i < totalOptions; i++) {
            var leftElm = $('[data-pos="' + i + '"].left');
            var rightElm = $('[data-pos="' + i + '"].right');
            var userAnswer = leftElm.attr('data-val') + '~' + rightElm.attr('data-val');
            var answerCheck = 0;
            for (var j = 0; j < dbAnswer.length; j++) {
				
				if(dbAnswer[j].indexOf("\"")!=-1) {dbAnswer[j] = dbAnswer[j].replace(/"/g,"");}
				if(dbAnswer[j].indexOf("'")!=-1) {dbAnswer[j] = dbAnswer[j].replace(/'/g,"");}
			 
             var dbAnswerCheck=$.trim(dbAnswer[j].replace(new RegExp("/", "g"), ""));
             var userAnswer=$.trim(userAnswer.replace(new RegExp("/", "g"), ""));
            
            if (userAnswer ===dbAnswerCheck ) {
                    dbAnswer[j] = "";
                    answerCheck = 1;
                    j=dbAnswer.length;
                }
            }
            if (answerCheck === 1) {
                correctFunc(i);
            } else {
                wrongFunc(i);
                wrongAns.push(i);
                $('[data-pos="' + i + '"].right').addClass('clsWrongR');
            }
            var userAnswerLeft = leftElm.attr("data-val");
            var userAnswerRight = rightElm.attr("data-val");
            userResponse += userAnswerLeft + ":" + userAnswerRight + '|';
        }
        end();
        return true;
    };

    var shuffleHTML = function() {
        var topPosition = [];
        for ( i = 0; i < totalOptions; i++) {
            topPosition[i] = $('[data-pos="' + i + '"].right').attr("data-pos");
        }
        Helpers.randomize(topPosition);
        for ( i = 0; i < totalOptions; i++) {
            $('.right')[i].setAttribute("data-pos", topPosition[i]);
            $($('.right')[i]).append('<div class="labels rightLabels">'+String.fromCharCode(65+parseInt(topPosition[i]))+'</div>');
        }
    };

    var handleDrop = function(event, ui) {
        var arrayDataSide = ['Left', 'Right'];
        arrayDataSide.splice(arrayDataSide.indexOf($(this).attr("data-side")), 1);
        arrayDataSide = arrayDataSide[0].toLowerCase();
        var emptyDataPos = $(ui.draggable).attr('data-pos');
        var dropDataPos = $(this).attr('data-pos');
        var existingElementInPos = $('[data-pos="' + dropDataPos + '"].' + arrayDataSide + '');
        $(this).addClass('conn');
        existingElementInPos.removeClass('conn');
        if (existingElementInPos[0] !== ui.draggable[0]) {
            existingElementInPos.removeAttr('style');
            existingElementInPos.attr('data-pos', emptyDataPos);
            $(ui.draggable).attr('data-pos', dropDataPos);
            $('[data-pos="' + emptyDataPos + '"]').removeClass('conn');
        }
        if ($(ui.draggable).hasClass('conn'))
            $('[data-pos="' + emptyDataPos + '"]').addClass('conn');
        else
            $(ui.draggable).addClass('conn');
        $(this).removeAttr('style');
        $(ui.draggable).removeAttr('style');
        buttonShow();
        $(".columns").css({
        	"height":maxHeight+"px"
        });

        var initialHeight = 70;
         for(i=1;i<totalOptions;i++)
        {
            initialHeight = initialHeight + maxHeight+10;
            $("[data-pos="+i+"]").css({"top":initialHeight+"px"});
        }
        return false;
    };

    var buttonShow = function() {
        check = 1;
        for (var i = 0; i < totalOptions; i++) {
            var trueCheck = $("#left" + i).hasClass('conn');
            if (trueCheck == true)
                check++;
        }
        if (check > totalOptions)
            $('#questionSubmitButton').show();
        else
            $('#questionSubmitButton').hide();
    };

    var correctFunc = function(i) {
        correct += 1;
        $("#correctWrongSign").append('<div class="signCls" id="signC1' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/greenCmark.png"/></div>');
        signMarkDisplay($('[data-pos="' + i + '"].right'), ("signC1" + i), 18);
    };

    var wrongFunc = function(i) {
        $("#correctWrongSign").append('<div class="signClsW" id="signC2' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/wrongCmark.png"/></div>');
        signMarkDisplay($('[data-pos="' + i + '"].right'), ("signC2" + i), 18);
        
    };

    var end = function() {
        if (correct == totalOptions) {
            $(".correct_audio")[0].play();
            right = true;
            var correctanswer = 1;
            restrictQuestions(correctanswer);
            showExplanation();
            setParameters();
            view.onAttempt();
        } else {            
            $(".wrong_audio")[0].play();
            right = false;
            var correctanswer = 0;
            setParameters();
            setTimeout(function() {
                restrictQuestions(correctanswer);
                showExplanation();
                view.onAttempt();
            }, 6000);
        }
        timerNew = [];
        window.clearInterval(timerNew);
        document.getElementById("matchTitle").style.cursor = "default";
        $("[data-side='Left']").droppable('disable');
        $("[data-side='Right']").droppable('disable');
        $("[data-side='Left'], [data-side='Right']").draggable('disable');
        $(".columns").css("cursor", "default");
       window.setTimeout(function() {
            animateAns();
        }, 3000);
    };
    var showExplanation = function(){
        if(sessionData.delayNextBtn !== undefined && sessionData.delayNextBtn != '' && sessionData.delayNextBtn == true)
        {
            delayText = 'You seem to be answering questions hurriedly. Please read the questions carefully before proceeding. You will be able to go to the next question (and to other pages) after 10 seconds.';
            delayText = delayText.bold();
        }
        else
            delayText = '';
    	if (!Helpers.isBlank(view.model.currentQuestion.json.explanation)) {
            Helpers.prompt({
                title : "Explanation",
                text : view.model.currentQuestion.json.explanation+'</br><span style="color:red;">'+delayText+'</span>',
                class : 'diaLog-explanation',
                modal : true,
                width : '600px',
                my : 'left top',
                at : 'left+200 top+5',
                ofObject : '.characterArrow',
                callback: function(){
                    $('.diaLog-explanation').prev('div').css( {'opacity':0.0} );
                }
            });
        }
    };
    var animateAns = function(i) {       
        var leftCorrectOption=[];
        var rightCorrectOption=[];
        for (var i = 0; i < dbAnswer.length; i++) {
            if (dbAnswer[i] != "") {
                var optionsArr = dbAnswer[i].split('~');
                leftCorrectOption.push($.trim(optionsArr[0].replace("/", "")));
                rightCorrectOption.push($.trim(optionsArr[1].replace("/", "")));
            }
        }
        var rightArr=[];
        var count=0;
        for(var i=0;i<wrongAns.length;i++){
            $("#signC2" + wrongAns[i]).css("display", "none");
            $("#correctWrongSign").append('<div class="signCls" id="signC3' + wrongAns[i] + '" style="visibility:hidden"><img src="' + Helpers.constants.THEME_PATH + 'img/Language/match/blueCmark.png"/></div>');
            var leftPosition = $('[data-pos="' + wrongAns[i] + '"].left');
            var leftID = leftPosition.attr('id');
            var topPos = $('#' + leftID).position().top;
            var leftPositionFromArr = $.inArray($.trim(leftPosition.attr('data-val')), leftCorrectOption);
            var right = $('[data-val="' + $.trim(rightCorrectOption[leftPositionFromArr]) + '"].clsWrongR');
            rightArr.push(right);
            var signId = wrongAns;
            $(right).animate({top: topPos + "px"}, 2000, function () {
                signMarkDisplay(rightArr[count], ("signC3" + signId[count]), 18);
                count++;
            });
            rightCorrectOption[leftPositionFromArr] = -1;
            leftCorrectOption[leftPositionFromArr] = -1;
        }
    };   
    var signMarkDisplay = function(targObj, signObj, topPos) {
    	$(".signCls").show();
        var targetObj = targObj;
        var toLeft = $(targetObj).position().left;
        var toTop = $(targetObj).position().top;
        $("#" + signObj).css({
            'display' : ''
        });
        var leftPos=$('#'+targetObj.attr('id')).outerWidth()-23;
        document.getElementById(signObj).style.visibility = "visible";
        document.getElementById(signObj).style.position = "absolute";
        document.getElementById(signObj).style.left = (toLeft + leftPos) + "px";
        document.getElementById(signObj).style.top = (toTop + topPos) + "px";
    };

    var setParameters = function() {
        view.model.currentQuestion.correct = right ? 1 : 0;
        view.model.currentQuestion.userResponse = userResponse;
        view.model.currentQuestion.extraParam = '';
        view.model.currentQuestion.score = right ? 1 : 0;
        view.model.currentQuestion.completed = 1;
    };

    var createQuestion = function() {
        $(view.container).html(matchHtmlStructure);
        $(view.container).append(allAudio);
        createColumns();
        view.onSubmit = submitButton;
    };

    return {
        show : createQuestion,
        subCorrect : correctFunc,
        subWrong : wrongFunc,
        stopFun : end,

    };
};

function showMatchQuestion(view) {
    matchQuestionObject = matchQuestion(view);
    matchQuestionObject.show();
    
    $( "img" )
      .error(function() {
        imgNotLoading();
    })
}
