var levelsAttempted = 'L1';
var levelWiseStatus = '0';
var levelWiseScore = 0;
var levelWiseAttempt = 0;
var extraParameters = '';
var completed = 0;
var noOfLevels;
var lastLevelCleared;
var previousLevelLock = 0;
var interactiveObj;
var totalTimeTaken = 0;
var levelWiseTimeTaken = "";
var timer = [];
timer[0] = 0;
var counter = 0;
var currentQues = 0;
var parameterMissing = '';
var mode = 'A';
var step = 0;
var questionNo = 0;
var dividend;
var divisor;
var multiplicationFactor;
var temp1, temp2, temp3, temp4;
var wrongAnswer1 = 0;
var wrongAnswer2 = 0;
var wrongAnswer4 = 0;
var fractionQuestion='';
var convertedDivisor = 0;
var convertedDividend = 0;
var correctAnswer='';
var attemptCounterMain=0;
$(document).ready(function (e) {
    $(function () {
        gameObj = new questionInteractive();
        var imageArray = new Array('');
        var loader = new PxLoader();
        $.each(imageArray, function (key, value1) {
            var pxImage = new PxLoaderImage('../assets/' + value1);
            loader.add(pxImage);
        });
        loader.addCompletionListener(function () {
            loadXML("xml.xml", startModule);
        });
        loader.start();
    });
});

function forceAlternate(){
	wrongAnswer1 = 1;
	//questionNo = 4;
};

function startModule() {
	//forceAlternate();
	arrayCounter = 0;
	StartTimer();
	levelWiseStatus = '0';
	levelsAttempted = 'L1';
	
	$("#loader").hide();
	//xmlText();
	if (counter > 0) {
	    $("#container").html(parameterMissing + " are missing");
	}
	else if (parseInt(lastLevelCleared) > 1) {
	    $("#container").show();
	    $("#container").html("lastLevelCleared parameter is not set properly..!!");
	}
	else {
	    if (previousLevelLock == 0) {
	        previousLevelLock = 1;
	        $("#landingPage").show();
	    }
	    else {
	        displayQuestion();
	        setTimeout(function () {
	            $("#ques1").focus();
	        }, 200);
	        $("#landingPage").hide();
	        $("#content").show();
	    }
	    $("#title").html(promptArr['title']);
	}
	
	$("#start").bind('click', function () {
	    displayQuestion();
	    setTimeout(function () {
	        $("#ques1").focus();
	    }, 200);
	    $("#landingPage").hide();
	    $("#content").show();
	});
	
	$(".inp").forceNumeric();
	$(".inp").live('keypress', onKeyPress);
	
	$("#promptContainer").draggable({
	    containment: "#container"
	});
	
	$("#button1").live('click', function () {
	    //currentQues = 'ques2';
	    $("#incorrectPrompt").hide();
	    $("#incorrectHint").show();
	    $("#incorrectHint").html("<div class='fraction'><div class='frac numerator'>" + dividend + "</div><div class='frac'>" + divisor + "</div></div> = <div style='margin-top:-10px' class='fraction'><div class='frac numerator'>" + dividend + "&nbsp;&#215;&nbsp;" + "<input id='ques2' class='inp' />" + "</div><div class='frac'>" + divisor + "&nbsp;&#215;&nbsp;" + multiplicationFactor + "</div></div>");
	    $("#ques2").forceNumeric();
	    setTimeout(function () {
	        $("#ques2").focus();
	    }, 200);
	});
	
	$("#prompt_k").click(function () {
	    $("#promptContainer").hide();
	    switch (step) {
	        case 1:
	            nextQuestion();
	            break;
	        case 2:
	            //empty
	            $("#" + currentQues).attr({ "disabled": false, "value": "" });
	            setTimeout(function () {
	                $("#" + currentQues).focus();
	            }, 200);
	            break;
	        case 3:
	            //incorrect next hint
	            $("#promptContainer").hide();
                $("#incorrectHint").hide();
                $("#quesMainExp").hide();
                textBx = new division(divisor, dividend);
                textBx.createGrid();
                textBx.promptPanel();
                textBx.multiplicationPanel();
	            break;
	        case 4:
	            nextQuestion();
	            break; 
		}
    });
}

function StartTimer() {
    var timer1 = [];
    var i = 0;
    var j = 0;
    if (completed != 1) {
        CounterForInterval = setInterval(function () {
            levelWiseTimeTaken++;
        }, 1000);
    }    
    else {
        clearInterval(CounterForInterval);
    }
}

function onKeyPress(e) {
    var key = e.which || e.keyCode;
    if ($(this).val().length > 8 && key != 8 && key != 13) {
        e.preventDefault();
    }
    else {
        if (key == 13) {
            if (this.id == "ques1") {
                var ans = dividend/divisor;
            }
            resultEval(e, this.id, ans);
        }
    }   
}

function questionInteractive() {
    if (typeof getParameters['noOfLevels'] == "undefined") {
        counter++;
        parameterMissing += ' noOfLevels ';
    }
    else {
        noOfLevels = getParameters['noOfLevels'];
        if (noOfLevels != 1) {
            $("#container").show();
            $("#container").html("noOfLevels parameter is not correct");
        }
    }
    if (typeof getParameters['lastLevelCleared'] == "undefined") {
        counter++;
        parameterMissing += ' lastLevelCleared ';
    }
    else {
        lastLevelCleared = parseInt(getParameters['lastLevelCleared']);
    }
    if (typeof getParameters['previousLevelLock'] == "undefined") {
        counter++;
        parameterMissing += ' previousLevelLock ';
    }
    else {
        previousLevelLock = parseInt(getParameters['previousLevelLock']);
    }
    if (typeof getParameters['numberLanguage'] == "undefined") {
        this.numberLanguage = "english";
    }
    else {
        this.numberLanguage = getParameters['numberLanguage'];
    }
    if (typeof getParameters['language'] == "undefined") {
        this.language = "english";
    }
    else {
        this.language = getParameters['language'];
    }
    if (typeof getParameters['mode'] == "undefined") {
        mode = "A";
    }
    else {
        mode = getParameters['mode'];
    }
}

jQuery.fn.forceNumeric = function () {
    return this.each(function () {
        $(this).keydown(function (e) {
            var key = e.which || e.keyCode;
            if (!e.shiftKey && !e.altKey && !e.ctrlKey &&
            // numbers
            key >= 46 && key <= 57 ||
            // Numeric keypad
            key >= 96 && key <= 105 ||
            // Backspace and Tab 
            key == 8 || key == 9 || key == 13 ||
            // Home and End
            key == 35 || key == 36 ||
            // left and right arrows
            key == 37 || key == 39 || key == 14 || key == 15 || key == 110 || key == 190 || key == 114)
            //alert(key);
                return true;

            return false;
        });
    });
};
var numberArray = [2,4,5,8];
function generateNumbers() {

    switch (mode) {
        case "A":

            var a = parseInt(Math.random() * 4);
            var a = numberArray[a];
            
            var b = parseInt(Math.random() * 90)+10;
            
            var x = b;
            var y = a;
            var factor = 1;
            break;
    }
    
    var arrayQ = [x, y, factor];
    return arrayQ;
}

function displayQuestion() {
    questionNo++;
    if (questionNo > 4) {
        completed = 1;
        levelWiseStatus = 1;
        $("#container").html("<div id='endPage'> Thanks!</div>");
        return;
    }
    var temp = '' ;
    var answer = '.12345678910';
    
   do
    {
        temp = generateNumbers();
        dividend = temp[0];
        divisor = temp[1];
        temp2 = new division(divisor, dividend);
        answer = temp2.answer;
        answer = answer.toString();
        answer = divisor * dividend;
        
    }while(dividend%divisor==0)
    correctAnswer=dividend/divisor;
    extraParameters+= 'Q'+questionNo+' -- '+dividend+'/'+divisor+'->';
    attemptCounterMain=0;
   /* while(answer.length > 9 || answer.indexOf('.')==-1){
    	temp = generateNumbers();
    	dividend = temp[0];
    	divisor = temp[1];
    	temp2 = new division(divisor, dividend);
    	answer = temp2.answer;
    	answer = answer.toString();

    	//console.log('stuckHere');	
    }
    */
    multiplicationFactor=temp[2] ;
    convertedDivisor = divisor.toString().split('');
    convertedDividend = dividend.toString().split('');
    $('#incorrectHint').hide();
    $('#quesMainExp').hide();
    
	textBx = new division(dividend, divisor);
	
    fractionQuestion="<div class='fraction'><div class='numerator frac'>" + dividend + "</div><div class='frac'>" + divisor + "</div></div>";
    
    $("#quesMain").html(promptArr['mainQ'] + "<br />" + "<span style='position:relative;top:5px'>"+fractionQuestion+"&nbsp;= <input id='ques1' class='inp'></input>");
    $("#quesMainExp").html(replaceDynamicText(promptArr['prompt6'], gameObj.numberLanguage, "")+" "+replaceDynamicText(convertedDividend,gameObj.numberLanguage,"")+" by <input id='ques4' class='inp'>");
}

var textBx;
var currentQues = '';
function resultEval(e, w, ans) {
    $("#promptContainer").show();
    //correct answer
    if (parseFloat($("#" + w).val()) == parseFloat(ans)) {
        ++attemptCounterMain;
        $("#" + w).addClass("green");
        $("#" + w).attr({ "disabled": true });
        $("#promptText").html(promptArr['prompt3']);
        extraParameters += 'A'+attemptCounterMain+'->'+$("#ques1").val()+'#';
         step = 1;
       
    }
    else {
        //empty box
        $("#" + w).attr({ "disabled": true });
        if ($("#" + w).val() == "") {
            $("#promptText").html(promptArr['prompt1']);
            currentQues = w;
            step = 2;
        }
        else {
            //incorrect answer
            ++attemptCounterMain;
            $("#" + w).addClass("red");
            $("#" + w).attr({ "disabled": true });
            switch (w) {
                case 'ques1':
                    wrongAnswer1++;
                    if (wrongAnswer1 == 1) {
                        extraParameters += 'A'+attemptCounterMain+'->'+$("#ques1").val()+'#';
                        $("#promptText").html(promptArr['prompt_incorrect1']);
                        step = 3;
                    }
                    else if (wrongAnswer1 == 2) {
                        extraParameters += 'A'+attemptCounterMain+'->'+$("#ques1").val()+'#';
                        $("#promptText").html(replaceDynamicText(promptArr['prompt_incorrect2'],gameObj.numberLanguage,'gameObj'));
                        step=4;
                    }
                    
                    break;
            }
            //else if()
        }

    }
    setTimeout(function () {
        $("#prompt_k").focus();
    }, 200);
    e.preventDefault();
}

function incorrectP() { 
    $("#incorrectPrompt").show();
    $("#incorrectPrompt").html(promptArr['incorrectLine1']);
    setTimeout(function () {
        $("#incorrectPrompt").append("<br /><br />" + promptArr['incorrectLine2']);
        var temp = generateNumbers();
        temp1 = temp[0];
        temp3 = temp[0];
        temp2 = temp[1];
        temp4 = temp[1];
        
        multiplicationFactor=temp[2] ;
	    temp4 = temp4.toString().split('');
	    temp3 = temp3.toString().split('');
	    
	    var shiftTimes = multiplicationFactor.toString().length - 1;
	    if(temp4.indexOf('.') != -1){
		    temp4.push('0','0','0','0','0','0');
	    	var currentLocation = temp4.indexOf('.');
	    	temp4.splice(currentLocation,1);
	    	temp4.splice(currentLocation + shiftTimes, 0, '.');
	    }
		temp4 = temp4.join();
		temp4 = temp4.replace(/,/g ,'');
		temp4 = parseFloat(temp4);
	    
	    if(temp3.indexOf('.') != -1){
		    temp3.push('0','0','0','0','0','0');
	    	var currentLocation = temp3.indexOf('.');
	    	temp3.splice(currentLocation,1);
	    	temp3.splice(currentLocation + shiftTimes, 0, '.');
	    }
		temp3 = temp3.join();
		temp3 = temp3.replace(/,/g ,'');
		temp3 = parseFloat(temp3);
		
        setTimeout(function () {
            $("#incorrectPrompt").append("<br /><div class='fraction'><div class='frac numerator'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + temp1 + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class='frac'>" + temp2 + "</div></div>");
            $("#incorrectPrompt").append("<div id='explaination1'>" + replaceDynamicText(promptArr['incorrectLine3'], gameObj.numberLanguage, "") + "</div>");
            setTimeout(function () {
                $("#incorrectPrompt").append("<br /><br />= <div class='fraction'><div class='frac numerator'>" + temp1 + "&nbsp;&#215;&nbsp;" + multiplicationFactor + "</div><div class='frac'>" + temp2 + "&nbsp;&#215;&nbsp;" + multiplicationFactor + "</div></div>");
                $("#incorrectPrompt").append("<div id='explaination2'>" + replaceDynamicText(promptArr['incorrectLine4'], gameObj.numberLanguage, "") + "</div>");
                setTimeout(function () {
                    $("#incorrectPrompt").append("<br /><br />= <div class='fraction'><div class='frac numerator'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + temp3 + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class='frac'>" + temp4 + "</div></div>");
                    $("#incorrectPrompt").append("<div id='explaination3'>" + replaceDynamicText(promptArr['incorrectLine5'], gameObj.numberLanguage, "") + "</div>");
                    setTimeout(function () {
                        $("#incorrectPrompt").append("<button id='button1'>OK</button>");
                        setTimeout(function () {
                            $("#button1").focus();
                        }, 200);
                    }, 1000);
                }, 1000);
            }, 1000);
        }, 1000);
    }, 1000);
}

var stopBlink = 0;
var cntr = 0;
function blinkEq() {
    if (stopBlink == 1) {
        cntr = 0;
        $("#incorrectHint").removeClass('red');   
        $("#incorrectHint").removeClass('blue');   
    }
    cntr++;
    if (cntr % 2 == 0) {
        setTimeout(function () {
            $("#incorrectHint").removeClass('blue');
            $("#incorrectHint").addClass('red');
            blinkEq();
        }, 500);
    }
    else { 
        setTimeout(function () {
            $("#incorrectHint").removeClass('red');
            $("#incorrectHint").addClass('blue');
            blinkEq();
        }, 500);
    }
}

function nextQuestion()
{
    //next Question
    $("#tablePanel").remove();
    $("#promptPanel").remove();
    $('#multiplicationPanel').remove();
    $('#decimalDot').remove();
    $('#decimalDot2').remove();
    correctAnswer='';
    fractionQuestion='';
    wrongAnswer1 = 0;
    wrongAnswer2 = 0;
    wrongAnswer3 = 0;
    wrongAnswer4 = 0;
    displayQuestion();
    $("#ques1").attr({ "disabled": false });
    $("#ques1").attr({ "value": "" });
    $("#ques1").forceNumeric();
    setTimeout(function () {
        $("#ques1").focus();
    }, 200);
}