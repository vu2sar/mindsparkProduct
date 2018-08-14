var sequenceObject;
function sequence(view) {
	var allSndArr= new Array();
	var finalSentenceArr;
	var correctAnswer = [];
	var missedIdArr = [];
	var totalOptions = view.quesDataArr.totalOptions;
	var right;
	var userResponse='';
	var score = 0;
	var timerNew = [];
	var maxHeight = 0;
	
	var leftSubType = view.quesDataArr.optSubType.split("And")[0];
	if(leftSubType)
		leftSubType = leftSubType.toLowerCase();
    
    var rightSubType = view.quesDataArr.optSubType.split("And")[1];
	if(rightSubType)
		rightSubType = rightSubType.toLowerCase();
    
    var quesLeftSubType = view.quesDataArr.quesSubType.split("And")[0];
	if(quesLeftSubType)
		quesLeftSubType = quesLeftSubType.toLowerCase();
    
    var quesRightSubType = view.quesDataArr.quesSubType.split("And")[1];
	if(quesRightSubType)
		quesRightSubType = quesRightSubType.toLowerCase();
    var optionsColorArr = ["hover1","hover2","hover3","hover4","hover5","hover6"];
	var createElements = function(view) {
		var html;
		var list = [];
		var list2 = [];
		var arr = [];
		totalOptions = view.quesDataArr.totalOptions;
		correctAnswer = [];
		var soundArr = [];
		for(i=0; i<totalOptions; i++) {
			arr.push(eval("view.quesDataArr.option_" + optLabelForCorrectAns[i]));
			correctAnswer.push(eval("view.quesDataArr.option_" + optLabelForCorrectAns[i]));
			soundArr.push(eval("view.quesDataArr.sound_" + optLabelForCorrectAns[i]));
		}
		optionsColorArr = Helpers.randomize(optionsColorArr);
		for(i=0; i<totalOptions; i++) {
			list[i] ='<div class="ui-state-default questions numbers '+optionsColorArr[i]+'" id="quesId'+i+'" data_sound="'+soundArr[i]+'">'+arr[i]+""+'</div><div class="hiddenQuestions" id="special'+i+'" ></div>';
			list2[i] ='<div class="answers numbers '+optionsColorArr[i]+'" id="ansId'+i+'" data_sound="'+soundArr[i]+'">'+arr[i]+""+'</div>';
		}
		list = Helpers.randomize(list);
		// Create a sequence container
		html  = '<div id="sequenceContainer">';

		$(view.container).find('div').html(html);

		// Html to be added in the sequencing container.
		html = '<div id="sequenceTitle">' + view.quesDataArr.quesText + '</div>';
		// showing audio icon in question
        if(quesLeftSubType == "audioicon" || quesRightSubType == "audioicon") {  
            html += '<img src="' + assetsPath + 'soundIconGrey.png" class="audioQues"/><br>';
        }
        // showing image in question
        if(quesLeftSubType == "image" || quesRightSubType == "image") {
            html += '<img src="' + view.imagePath + view.quesDataArr.quesImage+'" class="imageQues"/><br>';
        }
		html += '<div id="sortable">'+ list.join("") +'</div><div id="nonSortable">'+ list2.join("") +'</div><div id="correctWrongSign"> </div></div>';
		
		//$(view.container).find('div').html(html);
		$("#sequenceContainer").html(html);

		if (view.quesDataArr.titleText != '') {
            $("#sequenceContainer").prepend('<div id="sequenceTitleText">' + view.quesDataArr.titleText + '</div>');
        }
		for(i=0; i<totalOptions; i++) {
			$("#sequenceContainer #ansId"+i).css({
				"top": $("#sequenceContainer #quesId"+i).position().top+"px",
				"display":"none"
			});
			$($(".answers")[i]).attr("data-pos",i);
			$($(".questions")[i]).attr("data-pos",i);
		}
		for(i=0; i<totalOptions; i++) {
			var optionText = $("#sequenceContainer #quesId"+i).html();
			if(leftSubType == "text" || rightSubType == "text") {    // Handling text in options
	            if(maxHeight < $("#sequenceContainer #quesId"+i).outerHeight()) 
                    maxHeight = $("#sequenceContainer #quesId"+i).outerHeight()+10;
	        }
	        if(leftSubType == "image" || rightSubType == "image") {
	        	if(maxHeight < 120)
		        	maxHeight = 120;
		        if(leftSubType == "image") {
		        	var imgName = optionText.split("~")[0];
		        	var txtName = optionText.split("~")[1];
		        }
	        	else {
		        	var imgName = optionText.split("~")[1];
		        	var txtName = optionText.split("~")[0];
	        	}

		        if(leftSubType == "text" || rightSubType == "text") {
		        	// mixed version
	                $("#sequenceContainer #quesId"+i).html("<img src="+view.imagePath+imgName+" /><text>"+txtName+"</text>");
	                $("#sequenceContainer #ansId"+i).html("<img src="+view.imagePath+imgName+" /><text>"+txtName+"</text>");
		        }
		        else {
		        	// pure image
	                $("#sequenceContainer #quesId"+i).html("<img src="+view.imagePath+imgName+" style='margin-left: 40%'/>");
	                $("#sequenceContainer #ansId"+i).html("<img src="+view.imagePath+imgName+" style='margin-left: 40%'/>");
		        }
		        $("#sequenceContainer #quesId"+i).attr("data_image",imgName);
	        	$("#sequenceContainer #ansId"+i).attr("data_image",imgName);
	        }
	        if(leftSubType == "audioicon" || rightSubType == "audioicon") {
	        	if(maxHeight < 110)
		        	maxHeight = 110;
		        var audioName = $("#sequenceContainer #quesId"+i).attr("data_sound");
                allSndArr.push(audioName);
		        var imgName = '<img src="' + assetsPath + 'soundIconGrey.png" class="audioOptions"/>';
                $("#sequenceContainer #quesId"+i).html(imgName);
                $("#sequenceContainer #ansId"+i).html(imgName);
	        }
		}
		setHeight(maxHeight);
		setBindings();
		if(leftSubType == "audioicon" || rightSubType == "audioicon") {
			for (i = 0; i < totalOptions; i++) {
	            $($('.questions')[i]).append('<div class="labels rightLabels">'+String.fromCharCode(65+i)+'</div>');
	            var id = $($('.questions')[i]).attr("id");
	            $("#sequenceContainer #ansId"+id.substr(6,1)).append('<div class="labels rightLabels">'+String.fromCharCode(65+i)+'</div>');
	        }
	    }
	};
	var setHeight = function(maxHeight) {
		$(".questions,.answers,.hiddenQuestions").css({
			"height":maxHeight+"px"
		});
		var offset = $( "#sequenceContainer #sortable" ).offset().top;
		$( "#sequenceContainer #sortable" ).height((maxHeight+15)*totalOptions);
		$( "#sequenceContainer" ).height(((maxHeight+15)*totalOptions)+offset+maxHeight);
	}
	var setBindings = function() {
		// enabling click for questions with audio icon
        if(quesLeftSubType=="audioicon" || quesRightSubType=="audioicon") {
            $(".audioQues").click(function(){
                Helpers.sndPlayLoadErrEnd(view.soundPath, "allAudio", view.quesDataArr.quesAudioIconSound);
            });
        }
        // enabling click for options with audio
        if(leftSubType == "audioicon" || rightSubType == "audioicon") {  
            Helpers.loadAllSounds(allSndArr,null, view.soundPath);
            $(".questions").click(function(){
                Helpers.sndPlayLoadErrEnd(view.soundPath, "allAudio", $(this).attr("data_sound"));
            });
			$(".answers").click(function(){
			    Helpers.sndPlayLoadErrEnd(view.soundPath, "allAudio", $(this).attr("data_sound"));
			});
        }
        // enabling sortable
		$( "#sequenceContainer #sortable" ).sortable({
				placeholder: "ui-state-highlight",
				cursor: "move",
				axis: 'y',
				scroll: false,
				containment: "#sequenceContainer",
				tolerance: "pointer",
				sort: function(event, ui) {  
		        ui.helper.css({'top' : ui.position.top + $(window).scrollTop() + 'px'});
		    },
		    start: function (event,ui) {
		    	var id = $(ui.item).attr("id");
		    	var dataPos = $(ui.item).attr("data-pos");
		    	id = id.substr(6,1);
		    	$("#sequenceContainer #special"+id).show();
		    	$("#sequenceContainer #special"+id).attr("data-pos",dataPos);
		    	$("#sequenceContainer #special"+id).addClass("numbers");
		    	$(ui.item).removeAttr("data-pos");
		    },
			beforeStop: function (event, ui) {
				itemContext = ui.item.context;	
				var id = $(ui.item).attr("id");
		    	id = id.substr(6,1);
		    	var dataPos = $("#sequenceContainer #special"+id).attr("data-pos");
				$("#sequenceContainer #special"+id).removeAttr("data-pos");
		    	$("#sequenceContainer #special"+id).removeClass("numbers");
		    	$(ui.item).attr("data-pos",dataPos);		 
		    	$("#sequenceContainer #special"+id).insertAfter("#sequenceContainer #quesId"+id);
			},
			receive: function(event,ui) {
				var obj= ui.item.attr("id");
				var tmpStr=obj.substr(5,2);
				$("#sequenceContainer #"+obj).html("");
				$(itemContext).attr("id", "quesId" + tmpStr);
				currentId=("quesId" + tmpStr);		      
			},		
			stop: function(event, ui) {
			    var data = "";
				var dataArr= new Array();
			     $("#sequenceContainer #sortable .questions").each(function(i, el){
			     	if(leftSubType == "text" && rightSubType == "image") {
						data += $(el).attr("data_image") + ":" + $(el).text() +"|";	
			     	}
			     	else if(leftSubType == "text" || rightSubType == "text") {
						data += $(el).text() + "|";	
			     	}
			     	else if(leftSubType == "image" || rightSubType == "image") {
						data += $(el).attr("data_image") + "|";	
			     	}
			     	else if(leftSubType == "audioicon" || rightSubType == "audioicon") {
						data += $(el).attr("data_sound") + "|";	
			     	}
					userResponse =  data;
			    });
		    	$(".hiddenQuestions").hide();
			 },
			update: function(event, ui) {
				finalSentenceArr = $(this).sortable('toArray');
				for(i=0; i<finalSentenceArr.length; i++) {
					// for removing elements with special class from array
					if($("#sequenceContainer #"+finalSentenceArr[i]).hasClass("hiddenQuestions"))
						finalSentenceArr[i] = "dummy";
				}
				finalSentenceArr = finalSentenceArr.filter(function(value) { return value != 'dummy' });
				for(i=0; i<finalSentenceArr.length; i++) {
					$("#sequenceContainer #"+finalSentenceArr[i]).attr("data-pos",i);						
				}
				$('#questionSubmitButton').show(); // common button for all qtypes
			}						
		});
		$( "#sequenceContainer #sortable" ).disableSelection();
	};
	var submitButton = function() {
		missedIdArr = [];
		$("#sequenceContainer #correctWrongSign").html('');
		$('#questionSubmitButton').hide();
		$( "#sequenceContainer #sortable" ).sortable( "disable" );
			var correctChk=1;
			var topPos = [];
			for(var k=0; k<totalOptions; k++) {
				var actual_ans = correctAnswer[k];
				var user_ans = $("#"+finalSentenceArr[k]).html().toString();
				//var subStrTmp=finalSentenceArr[k].substr(6,2);
				//if(subStrTmp==k) {
				if(actual_ans == user_ans) {
					score++;
					correctFunc(k);
				}
				else {
					missedIdArr.push(k);			
					correctChk=0;
					wrongFunc(k);
				}
				topPos[k] = $("#sequenceContainer #quesId"+k).position().top;
				$("#sequenceContainer #ansId"+k).css({
					"top": $("#sequenceContainer #quesId"+k).position().top+"px",
					"display":"none"
				});
			}
			topPos.sort(function(a,b){return a-b;});
			if(correctChk==0) {
				var list = $("#sequenceContainer #sortable").children();
				dragAnimationObj(0,topPos);
				right=false;			
				var correctanswer = 0;
				restrictQuestions(correctanswer);
				//refQuesViewObj.playSound[1].play();
				$(".wrong_audio")[0].play();
			}
			else {
				//refQuesViewObj.playSound[0].play();
				$(".correct_audio")[0].play();
				right=true;
				completed=1;
				var correctanswer = 1;
				restrictQuestions(correctanswer);
				showExplanation();
				setParameters();
				view.onAttempt();
			}
			// Increment the question count shown on the interface.
			refQuesViewObj.incrementQuestionCount();
	};
	var correctFunc = function(i) {
        $("#sequenceContainer #correctWrongSign").append('<div class="signCls" id="signC1' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/greenCmark.png"/></div>');
        signMarkDisplay($('[data-pos="' + i + '"]'), ("signC1" + i), 620, 18);
    };

    var wrongFunc = function(i) {
        $("#sequenceContainer #correctWrongSign").append('<div class="signClsW" id="signC2' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/wrongCmark.png"/></div>');
        signMarkDisplay($('[data-pos="' + i + '"]'), ("signC2" + i), 620, 18);
    };
    var showAns = function(i) {
        $("#sequenceContainer #signC2" + i).css("display", "none");
        $("#sequenceContainer #correctWrongSign").append('<div class="signCls" id="signC3' + i + '"><img src="'+ Helpers.constants.THEME_PATH +'img/Language/match/blueCmark.png"/></div>');
        signMarkDisplay($('[data-pos="' + i + '"]'), ("signC3" + i), 620, 18);
    };
    var signMarkDisplay = function(targObj, signObj, leftPos, topPos) {
    	$("#sequenceContainer .signCls").show();
        var targetObj = targObj;
        var toLeft = $(targetObj).position().left;
        var toTop = $(targetObj).position().top;
        $("#sequenceContainer #" + signObj).css({
            'display' : ''
        });
        document.getElementById(signObj).style.visibility = "visible";
        document.getElementById(signObj).style.position = "absolute";
        document.getElementById(signObj).style.left = (toLeft + leftPos) + "px";
        document.getElementById(signObj).style.top = (toTop + topPos) + "px";
    };
	var showExplanation = function(){
		/*alert(sessionData.delayNextBtn);
		alert('sq exp');*/
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
	var dragAnimationObj = function(k,topPos) {
		$(".answers").show();
		if(k<totalOptions) {
			var index = finalSentenceArr[k].substr(6);
			var actual_ans = correctAnswer[k];
			var user_ans = $("#"+finalSentenceArr[k]).html().toString();
			if(actual_ans != user_ans) {
				var temp;
				var div1 = $('#sequenceContainer #'+finalSentenceArr[k]);
				var div2 = $('#sequenceContainer #'+finalSentenceArr[index]);
			    div1.css({
			    	"visibility":"hidden"
			    });
			    div2.css({
			    	"visibility":"hidden"
			    });
			    $("#sequenceContainer #ansId"+k).show();
			    $("#sequenceContainer #ansId"+index).show();
				$("#sequenceContainer #quesId"+k).css({
			    	"visibility":"hidden"
				});
				if(!$("#sequenceContainer #ansId"+k).hasClass("checkHover"))
				{
					var className1 = $("#sequenceContainer #ansId"+k).attr("class").split(" ").pop().substr(5,1);
					className1 = "checkHover hovered"+className1;
					$("#sequenceContainer #ansId"+k).addClass(className1);
				}
				if(!$("#sequenceContainer #ansId"+index).hasClass("checkHover"))
				{
					var className2 = $("#sequenceContainer #ansId"+index).attr("class").split(" ").pop().substr(5,1);
					className2 = "checkHover hovered"+className2;
					$("#sequenceContainer #ansId"+index).addClass(className2);
				}

			    $("#sequenceContainer #ansId"+k).animate({top:topPos[k]+"px"},1000);
		    	$("#sequenceContainer #ansId"+index).animate({top:topPos[index]+"px"},1000,function() {
					setTimeout(function() {
						temp = finalSentenceArr[k];
						finalSentenceArr[k] = finalSentenceArr[index];
						finalSentenceArr[index] = temp;
						k++;
						dragAnimationObj(k,topPos);
					},1000);
				});
			}
			else {
				k++;
				dragAnimationObj(k,topPos);
			}
		}
		else {
			resetHover();
			for(i=0; i<missedIdArr.length; i++)
			{
	            showAns(missedIdArr[i]);
			}
			//restrictQuestions();
			showExplanation();
			setParameters();
			view.onAttempt();
			return;
		}
	};
	var resetHover = function() {
		for(i=0; i<totalOptions; i++)
		{
			if($("#sequenceContainer #ansId"+i).hasClass("checkHover"))
				var className = $("#sequenceContainer #ansId"+i).attr("class").split(" ").pop().substr(7,1);
			else
				var className = $("#sequenceContainer #ansId"+i).attr("class").split(" ").pop().substr(5,1);

			className = "hovered"+className;
			$("#sequenceContainer #ansId"+i).removeClass(className);
		}
	};
	var createQuestion = function() {
        $(view.container).html(sequenceHtmlStructure);
        $(view.container).append(allAudio);
		createElements(view);		
		view.onSubmit = submitButton;
    };
    var setParameters = function() {
        view.model.currentQuestion.correct = right ? 1 : 0;
        view.model.currentQuestion.userResponse = userResponse;
        view.model.currentQuestion.extraParam = '';
        view.model.currentQuestion.score = score / totalOptions;
        view.model.currentQuestion.completed = 1;
    };
    return {
        show : createQuestion,
    };
}
function showSequence(view) {
    if (sequenceObject === undefined) {
        sequenceObject = sequence(view);
    }
    $( "img" )
      .error(function() {
        imgNotLoading();
    });

    sequenceObject.show();
}