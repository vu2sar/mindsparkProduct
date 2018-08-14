var makingWordQuestionObject;
var maxWords = 10;

function makingWordQuestion(view) {

    var allSndArr        = new Array();
    var matchHtmlContent = '';
    var totalOptions     = view.quesDataArr.totalOptions;
    var answers          = {};
    var given_answer     = {};
    var left_blocks      = {};
    var right_blocks     = {};
    var userResponse     = '';
    var correct          = 0;
    var timerNew         = [];
    var options          = [];
    var right;
    var maxHeight     = 0;
    var minWordToMake = view.quesDataArr.queParams.MinWordToBeMade;
    var wordToBeUsed  = view.quesDataArr.queParams.WordToBeUsed;
    wordToBeUsed = wordToBeUsed.trim();

    var minWordLength = view.quesDataArr.queParams.MinWordLen;

    var inputs;
    var possibleOptions = new Array();
   
    var createColumns = function() {
        //pushing the possible options in array
        jQuery.ajax({
            type : "POST",
            url : Helpers.constants.MSLANGUAGE_PATH + 'ajax/makewords.php?word='+wordToBeUsed,
            contentType:"application/json; charset=utf-8",
            "async" : false,
            dataType:'json',
            success: function(data) 
            {      
                jQuery.each(data, function(key,value) 
                {
                    if(value.length >= minWordLength && value != wordToBeUsed)
                    {
                      possibleOptions.push(value.toLowerCase());
                      sessionData.possibleOptions = possibleOptions; //added for session report
                    }
                }); 
            }
        });

       /* jQuery.ajax({
            type : "POST",
            url : Helpers.constants.MSLANGUAGE_PATH + 'ajax/makewords.php?word='+wordToBeUsed,
            url : Helpers.constants.CONTROLLER_PATH + 'questionspage/makeWords',
            //url : Helpers.constants.CONTROLLER_PATH + 'makewords',
            //data:{'wordToBeUsed' : wordToBeUsed},
            "async" : false,
            dataType:'json',
            success: function(data) 
            {      
                Helpers.ajax_response( getMakeWordsLibResult, data, [possibleOptions, minWordLength]);
            }
        });*/

        // pushing question sound
        if(view.quesDataArr.quesAudioIconSound != "") {  
            allSndArr.push(view.quesDataArr.quesAudioIconSound);
        }
        // adding question text
        matchHtmlContent = '<div id="makingWordContainer"><div id="makingWordTitle">' + view.quesDataArr.quesText + '</div>';


        matchHtmlContent += '<div id="correctWrongSign"> </div></div>';

        /*forming html*/
        //matchHtmlContent += '<div id="qtypeContainerOpt"><div id="qtypeTopBufferDiv" class="row qtypeTopBuffer">';
        matchHtmlContent += '<div id="qtypeContainerOptMaking"><div id="qtypeTopBufferDiv" class="row qtypeTopBuffer">';
        for (i = 0; i < minWordToMake; i++) 
        {
            var sr_no = i + 1;

            if(sr_no == minWordToMake)
                matchHtmlContent += '<div id="contentContainer'+sr_no+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+i+'"><div class="row" id="divlabId'+i+'">'+sr_no+'</div></div><div id="optId'+i+'" class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options"><input type="text" value="" class="form-control get_response" id="cb_Id'+i+'" count="'+i+'" name="opt_CB"><br></div><div id="addDiv'+sr_no+'" class="col-md-2 col-lg-1"><div class="row"><button id="addButton'+sr_no+'" class="addButton" onClick="addAnother('+sr_no+', '+minWordToMake+')"><i class="fa fa-plus-square" aria-hidden="true"></i></button></div></div></div>';   
            else
                matchHtmlContent += '<div id="contentContainer'+sr_no+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+i+'"><div class="row" id="divlabId'+i+'">'+sr_no+'</div></div><div id="optId'+i+'" class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options"><input type="text" value="" class="form-control get_response" id="cb_Id'+i+'" count="'+i+'" name="opt_CB"><br></div></div>';   
        }
         matchHtmlContent += '</div></div>';
        /*end*/
        $(view.container).find('div').append(matchHtmlContent);

        inputs = $('input', matchHtmlContent);
        
        setBindings();
        $(".columns").css({
            "height":maxHeight+"px"
        });
    };

    var setBindings = function() {
        $("#loader").hide();
    };

    var showExplanation = function(){
        /*alert(sessionData.delayNextBtn);
        alert('making exp');*/
        if(sessionData.delayNextBtn !== undefined && sessionData.delayNextBtn != '' && sessionData.delayNextBtn == true)
        {
            delayText = 'You seem to be answering questions hurriedly. Please read the questions carefully before proceeding. You will be able to go to the next question (and to other pages) after 10 seconds.';
            delayText = delayText.bold();
        }
        else
            delayText = '';
        var randomPossibleOptions = new Array();
        
        for (var i = 0; i < minWordToMake; i++) 
        {
            var randomValue = possibleOptions[Math.floor(Math.random() * possibleOptions.length)];
            if(jQuery.inArray(randomValue, randomPossibleOptions) == -1)
            {
                randomPossibleOptions.push(randomValue);
                possibleOptions.splice($.inArray(randomValue, possibleOptions),1);
            }
        }
        
        var finalString = randomPossibleOptions.join(', ');
        finalString     = finalString.bold();
        var text        = 'Some of the possible word(s) are '+finalString+'.';

         Helpers.prompt({
            title : "Explanation",
            text : text+'</br><span style="color:red;">'+delayText+'</span>',
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
        //}
    };
    
  


    var createQuestion = function() {
        $(view.container).html(matchHtmlStructure);
        createColumns();
        //view.onSubmit = submitButton;
        view.onSubmit = checkResponse;
    };


    var checkResponse = function() {
        var responseBlank = false;
        var responseRegex = false;
        userResponse      = '';
        right             = true;

        if (view.quesDataArr.qType == 'openEnded') 
        {
            if(textarea.val().trim() == '')
            {
                responseBlank = true;
            }
            else
            {
                //restrictQuestions();
                showExplanation();
                userResponse = textarea.val();
            }
        } 
        else 
        {

            //inputs.each(function() {
            jQuery('.get_response').each(function() {
                if (!this.value.trim().toLowerCase()) 
                {
                    responseBlank = true;
                }
                
                if(/^[a-zA-Z]*$/.test(this.value.trim().toLowerCase()) == false) 
                {
                    //right = false;
                    responseBlank = true;
                    responseRegex = true;
                }
                                
                //userResponse += this.id + ":" + this.value + '|';
                userResponse += this.value + '~';
            });
        }

        if (responseBlank) 
        {
            if(view.quesDataArr.qType == 'openEnded' || view.quesDataArr.qType == 'spelling')
                responseText = 'Fill answer before proceeding';
            else
            {
                if(responseRegex)
                    responseText = 'Invalid characters are not allowed';
                else
                    responseText = 'Fill answer before proceeding';
            }
            Helpers.prompt(responseText);
            return false;
        } 
        else 
        {
            if (/normal|preview/.test(view.model.questionMode) && view.quesDataArr.qType != 'openEnded') 
            {
                markResponse();
            }

            disableInput();
            setParameters();
            view.onAttempt();
        }
        
        return true;
    };

    var markResponse = function() {
        //inputs.each(function() {
        var checkResponseDuplicate = new Array();
        jQuery('.get_response').each(function() 
        {
            var get_count = $(this).attr('count');
            if (jQuery.inArray(this.value.trim().toLowerCase(), checkResponseDuplicate) == -1 && jQuery.inArray(this.value.trim().toLowerCase(), possibleOptions) !== -1) 
            {
                checkResponseDuplicate.push(this.value.trim().toLowerCase()); //if not in array then push in array
                correct += 1;
                $(this).addClass('right');
                $("#divlabId"+get_count).append('<div class="signCls" id="signC'+get_count+'"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                $("#signC"+get_count+" img").height("35px");
                $("#signC"+get_count+" img").width("35px");
                $("#divlabId" + get_count).addClass("bgcolorGreen");
                $("#parentDivlabId" + get_count).addClass("bgcolorGreen");
                $("#signC"+get_count).css({
                    'position' : 'absolute',
                    'left' : '-23px',
                    'top' : '0px'
                });
            }
            else
            {
                $(this).addClass('wrong');
                $("#divlabId"+get_count).append('<div class="signCls" id="signC'+get_count+'"><img src="' + assetsPath + 'wrongCmark.png"/></div>');
                $("#signC"+get_count+" img").height("35px");
                $("#signC"+get_count+" img").width("35px");
                $("#divlabId" + get_count).addClass("bgcolorRed");
                $("#parentDivlabId" + get_count).addClass("bgcolorRed");
                $("#signC"+get_count).css({
                    'position' : 'absolute',
                    'left' : '-23px',
                    'top' : '0px'
                });
            }

            /*if (jQuery.inArray(this.value.trim().toLowerCase(), possibleOptions) == -1) 
            { 
                $(this).addClass('wrong');
                $("#divlabId"+get_count).append('<div class="signCls" id="signC'+get_count+'"><img src="' + assetsPath + 'wrongCmark.png"/></div>');
                $("#signC"+get_count+" img").height("35px");
                $("#signC"+get_count+" img").width("35px");
                $("#divlabId" + get_count).addClass("bgcolorRed");
                $("#parentDivlabId" + get_count).addClass("bgcolorRed");
                $("#signC"+get_count).css({
                    'position' : 'absolute',
                    'left' : '-23px',
                    'top' : '0px'
                });
            } 
            else 
            {
                correct += 1;
                $(this).addClass('right');
                $("#divlabId"+get_count).append('<div class="signCls" id="signC'+get_count+'"><img src="' + assetsPath + 'greenCmark.png"/></div>');
                $("#signC"+get_count+" img").height("35px");
                $("#signC"+get_count+" img").width("35px");
                $("#divlabId" + get_count).addClass("bgcolorGreen");
                $("#parentDivlabId" + get_count).addClass("bgcolorGreen");
                $("#signC"+get_count).css({
                    'position' : 'absolute',
                    'left' : '-23px',
                    'top' : '0px'
                });
                // We do not evaluate these type of question.
                //score++;
            }*/
        });

        if(correct >= minWordToMake)
        {
            right = true;
            var correctanswer = 1;
            //refQuesViewObj.playSound[0].play();
            $(".correct_audio")[0].play();
        }
        else
        {
            right = false;
            var correctanswer = 0;
            //refQuesViewObj.playSound[1].play();
            $(".wrong_audio")[0].play();
        }

        restrictQuestions(correctanswer);
        showExplanation();

        //setTimeout(makeRight, 2000);
    };

    var disableInput = function() {
        jQuery('.get_response').attr('disabled', 'disabled');
        jQuery('.addButton').attr('disabled', 'disabled');
        jQuery('.removeButton').attr('disabled', 'disabled');
    };

    var setParameters = function() {
        view.model.currentQuestion.correct = right ? 1 : 0;
        view.model.currentQuestion.userResponse = userResponse;
        view.model.currentQuestion.extraParam = '';
        view.model.currentQuestion.score = right ? 1 : 0;
        view.model.currentQuestion.completed = 1;
    };


    return {
        show : createQuestion,
    };
};

function showMakingWordQuestion(view) {
    makingWordQuestionObject = makingWordQuestion(view);
    makingWordQuestionObject.show();
}

function getMakeWordsLibResult(data, extraParams)
{
    var possibleOptions = extraParams[0];
    var minWordLength   = extraParams[1];

    jQuery.each(data, function(key,value) 
    {
        if(value.length >= minWordLength)
        {
          possibleOptions.push(value.toLowerCase());
        }
    }); 
}

function addAnother(sr_no_count, minWordToMake)
{
    var sr_no = sr_no_count + 1;

    if(sr_no <= maxWords)
    {
        $("#addButton"+sr_no_count).remove();
        var html = '<div id="contentContainer'+sr_no+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+sr_no_count+'"><div class="row" id="divlabId'+sr_no_count+'">'+sr_no+'</div></div><div class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options" id="optId'+sr_no_count+'"><input type="text" name="opt_CB" count="'+sr_no_count+'" id="cb_Id'+sr_no_count+'" class="form-control get_response" value=""><br></div><div id="addDiv'+sr_no+'" class="col-md-2 col-lg-3"><div class="row">';
        if(sr_no != maxWords)
            html += '<button id="addButton'+sr_no+'" class="addButton" onClick="addAnother('+sr_no+', '+minWordToMake+')"><i class="fa fa-plus-square" aria-hidden="true"></i></button>';

        html +='<button id="removeButton'+sr_no+'" class="removeButton" onClick="removeBtn('+sr_no+', '+minWordToMake+')"><i class="fa fa-minus-square" aria-hidden="true"></i></button></div></div></div>';
        $("#qtypeTopBufferDiv").append(html);
    }
}

function removeBtn(sr_no_count, minWordToMake)
{
    var newHtmlContent = '';
    $("#contentContainer"+sr_no_count).remove();
    var numItems = $('.contentContainer').length;
    var valueArr = new Array();
    
    jQuery('.get_response').each(function(){
        valueArr.push($(this).val());
    });

    
    $(".contentContainer").remove();
    for (var i = 1; i <= numItems; i++) 
    {
        var j = i - 1;
        
        if(i == numItems && i != minWordToMake)
            newHtmlContent += '<div id="contentContainer'+i+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+j+'"><div class="row" id="divlabId'+j+'">'+i+'</div></div><div id="optId'+j+'" class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options"><input type="text" value="'+valueArr[j]+'" class="form-control get_response" id="cb_Id'+j+'" count="'+j+'" name="opt_CB"><br></div><div id="addDiv'+i+'" class="col-md-2 col-lg-3"><div class="row"><button id="addButton'+i+'" class="addButton" onClick="addAnother('+i+', '+minWordToMake+')"><i class="fa fa-plus-square" aria-hidden="true"></i></button><button id="removeButton'+i+'" class="removeButton" onClick="removeBtn('+i+', '+minWordToMake+')"><i class="fa fa-minus-square" aria-hidden="true"></i></button></div></div></div>';   

        else if(i > minWordToMake)
            newHtmlContent += '<div id="contentContainer'+i+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+j+'"><div class="row" id="divlabId'+j+'">'+i+'</div></div><div id="optId'+j+'" class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options"><input type="text" value="'+valueArr[j]+'" class="form-control get_response" id="cb_Id'+j+'" count="'+j+'" name="opt_CB"><br></div><div id="addDiv'+i+'" class="col-md-2 col-lg-3"><div class="row"><button id="removeButton'+i+'" class="removeButton" onClick="removeBtn('+i+', '+minWordToMake+')"><i class="fa fa-minus-square" aria-hidden="true"></i></button></div></div></div>';   
        else
            newHtmlContent += '<div id="contentContainer'+i+'" class="col-md-5 col-lg-6 contentContainer"><div class="qtypeClLabelCheckBoxOpt options col-md-1" id="parentDivlabId'+j+'"><div class="row" id="divlabId'+j+'">'+i+'</div></div><div id="optId'+j+'" class="col-md-8 col-lg-7 qtypeClOptDiv qtypeNoLpadding qtypeClOptSndIcon options"><input type="text" value="'+valueArr[j]+'" class="form-control get_response" id="cb_Id'+j+'" count="'+j+'" name="opt_CB"><br></div></div>';
    }
    $("#qtypeTopBufferDiv").append(newHtmlContent);

    if(numItems == minWordToMake)
    {
        var sr_no = sr_no_count - 1;
        
        var tempHtml = '<div id="addDiv'+minWordToMake+'" class="col-md-2 col-lg-1"><div class="row"><button onclick="addAnother('+minWordToMake+', '+minWordToMake+')" class="addButton" id="addButton'+minWordToMake+'"><i aria-hidden="true" class="fa fa-plus-square"></i></button></div></div>';
        $("#addDiv"+minWordToMake).remove();
        $("#contentContainer"+minWordToMake).append(tempHtml);
    }

}
