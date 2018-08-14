/**
 * @author eicpu92
 */

var blankQuestionObject;

function blankQuestion(view) {
    var selects;
    var inputs;
    var textarea;
    var userResponse = '';
    var score = 0;
    var right;
    var numCharacters = 1000;
    
    var createMarkup = function() {
        var text = view.quesDataArr.quesText;
        var html2;
        var html3;
        var attempt_audio = Helpers.createElement('audio', {
                id : "attempt_audio",
                html : '<source src="" type="audio/mp3" /><source src="" type="audio/ogg"/>'
            });

        if (view.quesDataArr.quesAudioIconSound != '') {
            html2 = Helpers.createElement('div', {
                className : 'blankAudioIcon',
                click : function() {
                    Helpers.sndPlayLoadErrEnd(Helpers.constants.LIVE_CONTENT_PATH + "templates_qtype/sounds/", "allAudio", view.quesDataArr.quesAudioIconSound);
                }
            });
            html3 = Helpers.createElement('audio', {
                id : "allAudio",
                html : '<source src="" type="audio/mp3" /><source src="" type="audio/ogg"/>'
            });
        };

        //replace blank with inputs       
        text = text.replace(/\[(Blank_\d+)\]/gi, '<input id="$1" type="text">');
        //replace dropDowns with select tags
        text = text.replace(/\[(Dropdown_\d+)\]/gi, '<select id="$1"></select>');
        if(view.quesDataArr.qType == 'openEnded')
        {
            //add textArea for openended questions
            text += '<div class="col-md-10 text-center"><textarea class="form-control blank-textarea" spellcheck="false" rows="5" cols="50" maxlength="1000" id="$1"></textarea><div class="textAreaAfter">[ Words entered: 0 ]</div></div>';
        }
        if(view.quesDataArr.qType == "spelling")
        {
            text = '<input id="Blank_1" type="text">';
        }
        var blankContainer = Helpers.createElement('div', {
            className : 'blankContainer qtypeClQuesDiv',
            html : text,
        });
        $(blankContainer).prepend(attempt_audio);
        $(blankContainer).prepend(html3);
        $(blankContainer).prepend(html2);
        if (view.quesDataArr.quesText != '') {
            // special handling for spelling qtype
            if(view.quesDataArr.qType == "spelling") {
                var title = view.quesDataArr.quesText.replace(/\[(.*?)\]/g,"");
                $(blankContainer).prepend('<div style="position: relative; height: auto; width: 100%">' + title + '</div>');
            }
        }
        if (view.quesDataArr.qType == 'spelling') {
            $('input', blankContainer).addClass('specialInput');
        }
        // view.quesDataArr.quesAudioIconSound = 'correct1.mp3'
        var that = view;

        selects = $('select', blankContainer);
        selects.each(function(element) {
            var questionOption=that.quesDataArr.queParams[this.id];
            var res = questionOption.replace(/&#8212;/g,"&#8212");
            var newElements= res.split(';');
            var elements=[];
            jQuery.each( newElements, function( i, val ) {
               elements.push(val.replace("&#8212", "&#8212;"));
            });
             this.rightAnswer = elements[0];
             Helpers.populateSelectElement(this, Helpers.shuffleArray(elements));
        });

        inputs = $('input', blankContainer);
        inputs.each(function(element) {
            var tmpArr=[];
			var textBoxWidth=220; //initialize default width
            $.each(that.quesDataArr.queParams[this.id].split(';'), function(){
                tmpArr.push($.trim(this));
            });            
            this.rightAnswer = tmpArr.join("|");
			
			var widthMultiplication=1;
			if(this.rightAnswer.length>15){
		        widthMultiplication=this.rightAnswer.length / 15;
                widthMultiplication=Math.round(widthMultiplication);
                widthMultiplication+=1;
            }
			
			textBoxWidth=textBoxWidth*widthMultiplication;
			
			var questionContainerWidth=$("#questionContainer").outerWidth();
			
			if(textBoxWidth>questionContainerWidth){
				textBoxWidth=questionContainerWidth;
			}
			var textBoxID='input#'+this.id;
			setTimeout(function(){ $(textBoxID).css('width',textBoxWidth+'px');  }, 10);			
           //this.rightAnswer = that.quesDataArr.queParams[this.id].replace(/;/g, '|');
        });

        textarea = $('textarea', blankContainer);
        $(view.container).html(blankContainer);
        textarea.bind('keypress', function(e) {

            if(textarea.val().length > 999 && textarea.is(":focus"))
            {
                Helpers.prompt("You have reached maximum character limit");
                e.preventDefault();
            }
        });
        textarea.bind('keyup', function(e) {
            // uncomment following if character left count is to be displayed
            /*var regex = new RegExp("^[a-zA-Z]+$");
            e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
            var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
            if (regex.test(str) || e.keyCode == 32 || e.keyCode == 8 || e.keyCode == 46) {
                $(".textAreaAfter").html("[ Characters left: "+(numCharacters-parseInt($(textarea).val().length))+" ]");
                return true;
            }

            e.preventDefault();
            return false;*/
            var count = Helpers.checkMinMaxWords(textarea.val(),"count",0);
            $(".blankContainer .textAreaAfter").html("[ Words entered: "+count+" ]");
        });
    };

    var disableInput = function() {
        selects.attr('disabled', 'disabled');
        inputs.attr('disabled', 'disabled');
        textarea.attr('disabled', 'disabled');
    };

    var markResponse = function() {
        //alert('mark response');
        var correct_flag = 1;
        selects.each(function() {
            if (Helpers.getSelectedOption(this).value != this.rightAnswer) {
                $(this).addClass('wrong');
                correct_flag = 0;
            } else {
                $(this).addClass('right');
                // We do not evaluate these type of question.
                //score++;
            }
        });
        inputs.each(function() {
            var reg = new RegExp('^(' + this.rightAnswer.trim() + ')$', 'i');
            if (!reg.test(this.value.trim())) {
                $(this).addClass('wrong');
                correct_flag = 0;
            } else {
                $(this).addClass('right');
                // We do not evaluate these type of question.
                //score++;
            }
        });

        var correct_sound = 'correct2.mp3';
        var wrong_sound = 'wrong2.mp3';
        if(view.quesDataArr.qType != 'openEnded')
        {
            if(correct_flag == 1){
                //Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "attempt_audio", correct_sound);
                //refQuesViewObj.playSound[0].play();
                $(".correct_audio")[0].play();
            }
            else{
                //Helpers.sndPlayLoadErrEnd(refQuesViewObj.soundPath, "attempt_audio", wrong_sound);
                //refQuesViewObj.playSound[1].play();
                $(".wrong_audio")[0].play();
            }

        }
        //alert('call restrict questions from blank.js');
        restrictQuestions(correct_flag);
        showExplanation();

        setTimeout(makeRight, 2000);
    };
    var explanationTitle;
    var showExplanation = function() {
        if(sessionData.delayNextBtn !== undefined && sessionData.delayNextBtn != '' && sessionData.delayNextBtn == true)
        {
            //alert('m in delay text');
            //alert(sessionData.delayNextBtn);
           var delayText = 'You seem to be answering questions hurriedly. Please read the questions carefully before proceeding. You will be able to go to the next question (and to other pages) after 10 seconds.';
            delayText = delayText.bold();
        }
        else
            delayText = '';
        if (!Helpers.isBlank(view.model.currentQuestion.json.explanation)) {
            if(view.quesDataArr.qType == 'openEnded')
                explanationTitle = 'Explanation';
            else
                explanationTitle = 'Explanation';
            Helpers.prompt({
                title : explanationTitle,
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

    var makeRight = function() {
        $('.wrong').each(function() {
            this.value = this.rightAnswer;
            $(this).addClass('corrected');
        });
    };
    var responseText;
    
    var checkResponse = function() {
        var responseBlank = false;
        userResponse = '';
        right = true;

        if (view.quesDataArr.qType == 'openEnded') {
            if(textarea.val().trim() == '')
            {
                responseBlank = true;
            }
            else
            {
                var correctanswer = 1;
                restrictQuestions(correctanswer);
                //showExplanation();
                userResponse = textarea.val();
            }
        } else {
            selects.each(function() {
                if (this.selectedIndex == 0) {
                    responseBlank = true;
                }
                if (Helpers.getSelectedOption(this).value != this.rightAnswer) {
                    right = false;
                }

                userResponse += this.id + ":" + Helpers.getSelectedOption(this).value + '|';
            });

            inputs.each(function() {
                if (!this.value.trim()) {
                    responseBlank = true;
                }

                var reg = new RegExp('^(' + this.rightAnswer + ')$', 'i');
                if (!reg.test(this.value.trim())) {
                    right = false;
                }
                                
                userResponse += this.id + ":" + this.value + '|';
            });
        }

        if (responseBlank) {
            if(view.quesDataArr.qType == 'openEnded' || view.quesDataArr.qType == 'spelling')
                responseText = 'Fill answer before proceeding';
            else
                responseText = 'Fill/Select answer before proceeding';
            Helpers.prompt(responseText);
            return false;
        } else {
            if (/normal|preview/.test(view.model.questionMode) && view.quesDataArr.qType != 'openEnded') {
                markResponse();
            }

            disableInput();
            setParameters();
            view.onAttempt();
        }
        
        return true;
    };

    var setParameters = function() {
        if(view.quesDataArr.qType == 'openEnded')
            view.model.currentQuestion.correct = 0;
        else
            view.model.currentQuestion.correct = right ? 1 : 0;
        
        view.model.currentQuestion.userResponse = userResponse;
        view.model.currentQuestion.extraParam = '';
        if (view.quesDataArr.qType == '')
            view.model.currentQuestion.score = score / (inputs.length + selects.length);
        else
            view.model.currentQuestion.score = 0;
        view.model.currentQuestion.completed = 1;
    };

    var createQuestion = function() {
        createMarkup();
        view.onSubmit = checkResponse;
        view.giveExplanation = showExplanation;
    };

    return {
        show : createQuestion,
    };
};

function showBlankQuestion(view) {
    if (blankQuestionObject === undefined) {
        blankQuestionObject = blankQuestion(view);
    }
    $( "img" )
      .error(function() {
        imgNotLoading();
    })
    blankQuestionObject.show();
}