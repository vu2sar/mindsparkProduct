var textBx;
var levelsAttempted = "L1";
var levelWiseStatus = 0;
var levelWiseScore = 0;
var levelWiseTimeTaken = 0;
var extraParameters = '';
var completed = 0;
var currentScreen = 0;
var attemptCounter = 0;
var quotientCounter = -1;
var previousQuotientCounter = 0;
var quotientAttempt = 0;
var rowCount = 3;
var columnCount = 4;
var commonFlowFlag = 0;
var setFlow = [''];
var alreadyTriedNumbersInTablePanel =[];
var specialPrompts = [];
var comingFromWrongProduct = false;
var wrongProductAttempter = 0;
var incorrectCarryCounter=0;
var promptCorrectAnswer =0;
var currentValue='';
var minusArray=[];
var decimalFlag=0;
function division(divisor, dividend) {
    currentScreen=0;
    attemptCounter=0;
    quotientCounter=-1;
    previousQuotientCounter=0;
    quotientAttempt=0;
    rowCount=3;
    columnCount=4;
    commonFlowFlag = 0;
    setFlow = [''];
    this.numberLanguage = "english";
    this.divisor = divisor;
    this.dividend = dividend;
    decimalFlag=0;
    this.answer = this.dividend / this.divisor;
    this.dividendArray = this.dividend.toString().split("");
    var index = this.dividend.toString().indexOf('.');
    if (index != -1) {
        this.dividendArray.splice(index, 1);
        this.dividendArray[index] = '.' + this.dividendArray[index];
    }
    this.answerArray = this.answer.toString().split("");
    var repeatCount = 0;
    startLocation = 0;
    var javascriptPissed = false;
    for(var i = 0; i < this.answerArray.length - 1; i++){
    	if(this.answerArray[i] == this.answerArray[i+1]){
    		repeatCount++;
    		if(repeatCount == 1)
    			startLocation = i;
    		if(repeatCount >= 5){
    			javascriptPissed = true;
    			break;
    		}
    	}
    	else{
    		repeatCount = 0;
    	}
    }
    
    if(javascriptPissed){
	    var indexOfPoint = this.answerArray.indexOf('.');
	    var difference = startLocation - indexOfPoint;
	    
	    var joinedAnswer = Math.round(parseFloat(this.answerArray.join().replace(/,/g,'')) * Math.pow(10,difference - 1)).toString().split('');
	    if(this.answerArray[0] == '0'){
		    joinedAnswer.splice(0, 0,'0');
	    }
	    joinedAnswer.splice(indexOfPoint,0,'.');
	    this.answerArray = joinedAnswer;
    }
    this.answer = parseFloat(this.answerArray.join().replace(/,/g,''));
}

// Create the grid and fill the numbers by system
division.prototype.createGrid = function () {
    container = "divisionTable";
    var data = '';
    data += '<div id="tablePanel" class="inline">';
    data += '<table id="myTable">';
    for (var i = 1; i <= 15; i++) {
        data += '<tr id="row' + i + '">';
        for (var j = 1; j <= 12; j++) {
            data += '<td id="column' + j + '">&nbsp;</td>';
        }
        data += '</tr>';
    }
    data += '</table>';
    data += '</div>';
    $("#" + container).html(data);
    
    var image = document.createElement('img');
    image.src = '../assets/pointer.png';
    image.id = 'quotientPointer';
    $('#divisionTable').append(image);
    
    var image2 = document.createElement('img');
    image2.src = '../assets/cross.png';
    image2.id = 'quotientCross';
    $('#divisionTable').append(image2);
};

var number1;
var number2;

// Contains the screen prompts that are loaded when incorrect attempts are made
division.prototype.promptPanel = function () {
    //console.log("Hello");
    var data = '';
    data += '<div id="promptPanel" class="inline">';
    data += '<div id="sparkiePrompt">';
    data += '<div id="sparkie"></div>';
    data += '<div id="sparkiePromptBubble">';
    data += '<div id="sparkieMessage"></div>';
    //data+='<button id="closeSparkie" class="button">'+miscArr['ok']+'</button>';
    data += '</div>';
    data += '</div>';
    data += '<div id="bottomPanel">';
    data += '<div id="screen1" class="hide">';
    data += '<div id="mainMessage">' + replaceDynamicText(promptArr['mainMessage'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<div>';
    data += '<button id="placeDecimal" class="button optionButton" onclick="placeDecimal()">' + miscArr['placeDecimal'] + '</button>';
    data += '<button id="placeDigit" class="button optionButton" onclick="placeDigit()">' + miscArr['placeDigit'] + '</button>';
    data += '</div>';
    data += '</div>';
    data += '<div id="screen2" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['decimal_incorrect1'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="moveToMainScreen()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen3" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['decimal_incorrect2'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" id="screen3Button" onclick="onScreen3Button()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen4" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['digit_incorrect1'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="onScreen5Button()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen5" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['digit_incorrect2'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" id="screen5Button" onclick="onScreen5Button()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen6" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['wrong_quotient_1'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="wrongQuotientYes()">' + miscArr['yes'] + '</button>';
    data += '<button class="button" onclick="wrongQuotientNo()">' + miscArr['no'] + '</button>';
    data += '<button class="button" onclick="wrongQuotientYes()">' + miscArr['notsure'] + '</button>';
    data += '</div>';
    number1 = (dividend * multiplicationFactor).toString();
    number1 = number1[0];
    number2 = Math.floor(divisor * multiplicationFactor);
    data += '<div id="screen7" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['wrong_quotient_yes'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="sparkiePrompt()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen8" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['wrong_quotient_2'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button id="screen8_button1" class="button" onclick="showSubScreen8(this)">' + miscArr['ok'] + '</button>';
    data += '<div id="subScreen8" class="hide">';
    var temp = (dividend * multiplicationFactor).toString();
    number1 = number1 + temp[1];
    data += '<div>' + replaceDynamicText(promptArr['wrong_quotient_3'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="sparkiePrompt1()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '</div>';
    data += '<div id="screen10" class="hide screen">';
    data += '<div id="screen10Info"> </div>';
    data += '<button class="button" id="onDifferenceWrongCheckButton" onclick="onDifferenceWrong()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen11" class="hide screen">';
    data += '</div>';
    data += '<div id="screen9" class="hide screen" >';

    data += '<div id="mess"></div>';
    //_divisor, _enteredValue, _number, _element  (divisor*multiplicationFactor),enteredValue,
    data += '<button id="yes" class="button" >' + miscArr['yes'] + '</button>';
    data += '<button class="button" onclick="onScreen9NoClick()">' + miscArr['no'] + '</button>';
    data += '<button id="notSure" class="button">' + miscArr['notsure'] + '</button>';
    data += '</div>';
    data += '</div>';
    data += '<div id="screen12"></div>';
    data += '<div id="screen13"  class="hide screen"><div id="screen13Info"></div><button id="screen13Button" onclick="returnFromWrongProduct()">OK</button></div>';
 

    $("#" + container).append(data);
    attemptCounter = 0;
    currentScreen = 1;
    textBx.createTextBoxes();
    quotientCounter++;
    moveToMainScreen();
    
    $("#notSure, #yes").live('click', function (){callTablePanel();});
};

var differenceWrongCount = 0;

function onScreen5Button(){
	if(attemptCounter >= 2){
		actualPlaceDecimal();
		showPrompt(promptArr['enterQuotient']);
	}
	else{
		moveToMainScreen();
	}
}

function onScreen3Button(){
	if(attemptCounter >= 2){
		$("#screen" + currentScreen).hide();
		attemptCounter = 0;
	    $($("#row2 input")[quotientCounter]).attr('disabled', false);
	    $($("#row2 input")[quotientCounter]).focus();

	    $($("#row2 input")[quotientCounter]).addClass('answeredInput');
	    $("#sparkieMessage").html(promptArr['enterQuotient']);
	    $("#sparkiePromptBubble").show();
	    $("#screen1").hide();
	    currentScreen = 1;
	    showPrompt(promptArr['enterQuotient']);
	}
	else{
		moveToMainScreen();
	}
}
function returnFromWrongProduct(){
	$('#screen13').hide();
	$(setFlow[arrayCounter]).removeAttr('disabled');
	$(setFlow[arrayCounter]).focus();
}

function onScreen9NoClick(){
	showPrompt(promptArr['sparkiePrompt1']);
	setFocus();
}

function onDifferenceWrong(){
	differenceWrongCount++;
	 $('#row' + rowCount + ' .answeredInput').removeAttr('disabled');
	if(differenceWrongCount % 2 == 1 ){
		$(setFlow[arrayCounter]).focus();
	}
	else{
		//arrayCounter++;
		/*if($(setFlow[arrayCounter+1]).hasClass('answeredInput'))	{
			arrayCounter++;
		}*/
		
		for(var i = 0; i <= arrayCounter; i++){
			if(setFlow[i] != 'moveToMainScreen()'){
				var toEnter = parseFloat($(setFlow[i]).attr('data'));
				toEnter = toEnter < 1 ? (toEnter * 10): toEnter; 
				$(setFlow[i]).attr('value', toEnter);
				$(setFlow[i]).attr('disabled', 'disabled');
			}
		}
		checkResult();
	}
	$('#screen10').hide();
}

function callTablePanel(later){
	var stats = getCurrentStatus(later);
	var valueEntered;
	if(later){
		valueEntered = parseInt($('#row2 input')[quotientCounter - 1].value);
	}
	else{
		valueEntered = parseInt($('#row2 input')[quotientCounter].value);
	}
    tablePanel(parseInt(textBx.divisor), valueEntered, stats[1], $("#screen11"));
    
    if(comingFromWrongProduct){
    	input = $('.smallCalcTable input');
    	
    	result = textBx.divisor * valueEntered;
    	result = '000000' + result;
    	
    	result = result.substring(result.length - 2);
    	
    	input[0].value = result[0];
    	input[1].value = result[1];
    	
    	$('#tablePanelCheck').html(miscArr['ok']);
    	$('#additionalDiv').html(miscArr['okToContinue']);
    }
}

function getCurrentStatus(later){
	var quotient1 = parseFloat($($("#row2 input")[later ? (quotientCounter - 1):quotientCounter]).attr("data")); //getting the right quotient
	if(quotient1 < 1){
		quotient1 = quotient1 * 10;
	}
    //getting the right dividend
    var remainingToDivide = divArr.toString().replace(/\,/g,'');
    remainingToDivide = parseFloat(remainingToDivide.replace(/\./g,''));
    
    var partOfRemainingToDivide = 0;
    var limit;
    
    if(later){
    	limit = quotientCounter;
    }
    else{
    	limit = quotientCounter + 1;
    }
    
    var currentQuotient;
    for(var i = 0; i < limit; i++){
    	currentQuotient = parseFloat(textBx.answerArray[i]);
    	currentQuotient = currentQuotient < 1? currentQuotient * 10: currentQuotient;
    	
    	var start = 0;
    	partOfRemainingToDivide = 0;
    	while(partOfRemainingToDivide < (textBx.divisor * currentQuotient) || ((partOfRemainingToDivide == 0) && (textBx.divisor * currentQuotient == 0))){
    		partOfRemainingToDivide *= 10;
    		partOfRemainingToDivide += parseInt(remainingToDivide.toString().substring(start,start + 1), 10);
    		start++;
    	}
    	
    	//partOfRemainingToDivide = (partOfRemainingToDivide == 0) ? '': partOfRemainingToDivide;
    	var string2 = remainingToDivide.toString().substring(partOfRemainingToDivide == 0 ? 0 : partOfRemainingToDivide.toString().length);
    	var remainder = partOfRemainingToDivide - (textBx.divisor * currentQuotient);
    	remainingToDivide = parseFloat(remainder.toString() + string2);
    }
    
    textBx.currentDividend = partOfRemainingToDivide;
    var returnArray = [quotient1, partOfRemainingToDivide];
    return returnArray;
}

division.prototype.multiplicationPanel = function () {
	var data = '';
    data += '<div id="multiplicationPanel" class="inline">';
    data += '<div id="multiplicationHeader">TABLES</div>';
    data += '<div id="multiplicationTables"></div>';
	$("#" + container).append(data);
};

// This will move to the screen on with the options Place the Decimal and Place the Digit button
function moveToMainScreen() {
    $('#sparkiePromptBubble').hide();
    $("#screen" + currentScreen).hide();
    $("#screen1").show();
    quotientAttempt=0;
    for(var i = 0 ; i < arrayCounter; i++){
    	if(setFlow[i] != 'moveToMainScreen()'){
    		$(setFlow[i]).attr('disabled','disabled');	
    	}
    }
    
    currentScreen = 1;
}

// Called when press decimal button
function placeDecimal() {
    var quotientInputs = $('#row2 input');
   	var rightAnswer = $(quotientInputs[quotientCounter]).attr('data');
   // if (parseFloat(textBx.answerArray[quotientCounter]) < 1 && attemptCounter < 2 && (!(quotientCounter == 0 && textBx.answerArray[quotientCounter] == 0)) ) {
   	if(rightAnswer.indexOf('.') == -1){
        showPrompt(promptArr['readCareFully']);
        attemptCounter++;
        $("#screen" + currentScreen).hide();
        if (attemptCounter == 1) {
            $("#screen2").show();
            currentScreen = 2;
        }
        else if(attemptCounter == 2){
            $("#screen3").show();
            currentScreen = 3;
        }
    }
    else {
    	//setFocus();
    	actualPlaceDecimal();
    }
}
var theElement='';
function actualPlaceDecimal(){
	$("#screen" + currentScreen).hide();
	
	theElement = $($("#row2 input")[quotientCounter]);
	theElement.addClass('answeredInput');

    if(parseFloat(theElement.attr('data')) < 1){
		var dot = document.createElement('div');
		dot.id = 'decimalDot';
		$('#divisionTable').append(dot);
		$(dot).css({
			'width' : '2px',
			'height': '2px',
			'position': 'absolute',
			'top':  theElement.offset().top - parseInt($('#divisionTable').css('top')) + 18 + 'px',
			'left':  theElement.offset().left - parseInt($('#divisionTable').css('left')) + 3 + 'px',
			'background': 'black'
		});
		
		var containsDecimal = false;
		for(var j = 0 ; j < this.divArr.length; j++){
			var item = parseInt(this.divArr[j]);
			if(isNaN(item))
				containsDecimal = true;
		}
		
		if(!containsDecimal){
			var dot2 = document.createElement('div');
			dot2.id = 'decimalDot2';
			$('#divisionTable').append(dot2);
			$(dot2).css({
				'width' : '2px',
				'height': '2px',
				'position': 'absolute',
				'top':  theElement.offset().top + theElement.height() - parseInt($('#divisionTable').css('top')) + 18 + 'px',
				'left':  theElement.offset().left - parseInt($('#divisionTable').css('left')) + 3 + 'px',
				'background': 'black'
			});	
		}
    } 
    
    arrayCounter++;
    $(setFlow[arrayCounter]).removeClass('containsZero');
    $(setFlow[arrayCounter]).attr('disabled',false);
    $(setFlow[arrayCounter]).focus();
    $(setFlow[arrayCounter]).addClass('answeredInput');
    $("#screen1").hide();
    
    if(showSpecialPrompts(arrayCounter) == -1){
    	var element = $(setFlow[arrayCounter])[0];
    	var column = $(element).parent();
    	var columnNo = column[0].id;
    	columnNo = parseInt(columnNo.replace('column', ''));
    	
    	var row = $(column).parent();
    	var rowNo = row[0].id;
    	rowNo = parseInt(rowNo.replace('row', ''));
    	
    	if(rowNo%2 == 1){
    		var nextElement = $('#row' + rowNo + ' #column' + (columnNo + 1) + ' input')[0];
    		if(nextElement == undefined)
		    	showPrompt(promptArr['bringDown']);
    	}
    };
    
    //now remove all mainMenuScreen wale loche
    for(var i = arrayCounter; i < setFlow.length; i++){
    	if(setFlow[i] == 'moveToMainScreen()')
    	{
    		setFlow.splice(i,1);
    	}
    }
}

// Called when press digit button
function placeDigit() {
   	var quotientInputs = $('#row2 input');
   	var rightAnswer = $(quotientInputs[quotientCounter]).attr('data');
   // if (parseFloat(textBx.answerArray[quotientCounter]) < 1 && attemptCounter < 2 && (!(quotientCounter == 0 && textBx.answerArray[quotientCounter] == 0)) ) {
   	if(rightAnswer.indexOf('.') != -1){
        showPrompt(promptArr['readCareFully']);
        attemptCounter++;
        $("#screen" + currentScreen).hide();
        if (attemptCounter == 1) {
            $("#screen4").show();
            currentScreen = 4;
        }
        else{
            $("#screen5").show();
            currentScreen = 5;
        }
    }
    else {
    	showPrompt(promptArr['startQuotient']);
        setFocus();
    }
}

// When wrong quotient is entered.
function wrongQuotient() {
    attemptCounter++;
    if (attemptCounter == 1) {
        $("#screen" + currentScreen).hide();
        $("#screen6").show();
        $("#sparkieMessage").html(promptArr['readCareFully']);
        $("#sparkiePromptBubble").show();
        currentScreen = 6;
    }
    else {
        $("#screen" + currentScreen).hide();
        $("#screen8").show();
        
        $("#sparkieMessage").html(promptArr['readCareFully']);
        $("#sparkiePromptBubble").show();
        $("#screen8_button1").show();
        currentScreen = 8;
    }
}
// When Yes button is pressed from the three button options.
function wrongQuotientYes() {
    $("#screen" + currentScreen).hide();
    $("#screen7").show();
    currentScreen = 7;
}

// When No button is pressed from the three button options.
var wrongAttempt = 0;
function wrongQuotientNo() {
    wrongAttempt++;
    if (wrongAttempt == 1) {
        $("#screen" + currentScreen).hide();
        $("#sparkiePromptBubble").show();
        $("#sparkiePromptBubble").html(promptArr['sparkiePrompt3']);
    }
    else {
        $("#screen" + currentScreen).hide();
        $("#screen7").show();
        currentScreen = 7;
    }
    $($('#row2 input')[quotientCounter]).removeAttr('disabled');
    $('#row2 input')[quotientCounter].focus();
}

function showSubScreen8() {
    $("#screen8_button1").hide();
    $("#subScreen8").show();
    $('#quotientPointer').show();
    $('#quotientCross').show();
    currentScreen = 8;
}
var theElement1='';
function setFocus() {
    //Set focus to the next textbox for user input
    $("#screen" + currentScreen).hide();
    theElement1 = $($("#row2 input")[quotientCounter]);

    theElement1.attr('disabled', false);
    theElement1.focus();
    theElement1.addClass('answeredInput');
    if(parseFloat(theElement1.attr('data')) < 1 && parseFloat(theElement1.attr('data')) > 0){
		var dot = document.createElement('div');
		dot.id = 'decimalDot';
		$('#divisionTable').append(dot);
		$(dot).css({
			'width' : '2px',
			'height': '2px',
			'position': 'absolute',
			'top':  theElement1.offset().top - parseInt($('#divisionTable').css('top')) + 18 + 'px',
			'left':  theElement1.offset().left - parseInt($('#divisionTable').css('left')) + 3 + 'px',
			'background': 'black'
		});	
    } 
}

var enteredValue;
division.prototype.createTextBoxes = function () {
    // Step 1 Textbox  ( Will start creating the text box from the 4th row and 4th column)
    var rowCounter = 4;
    var columnCounter = 4;
    var staticcolumn = 4;
    var answer = 0;
    var result = 0;
    var decimalCheck = 0;
    var dividendCounter = 0;
    var currentDigitForDivision = this.dividendArray[0];
    var result = 0;
    var multiplyResult = 0;
    var ansIndex = 0;
    
    //resetting states
    arrayCounter = 0;
    alreadyTriedNumbersInTablePanel = [];
    
    //adds an (unnecessary) zero in the beginning of the answerArray if divisor is more than first digit of dividend.
    if (this.divisor > this.dividendArray[0] && (this.divisor < this.dividend)) {
        this.answerArray.splice(0, 0, "0");
    }
    
    //separator
    $("#row3 #column3").html(this.divisor).css('border-right', '2px solid');
    
    //merging the dot with the next integer and writing the answer digits in respective table cells.
    for (var i = 0; i < (this.answerArray.length); i++) {
        if (this.answerArray[i] == '.') {
            this.answerArray[i] = '.' + this.answerArray[i + 1];
            this.answerArray.splice(i + 1, 1);
        }
        $("#row2 #column" + (i + columnCounter)).html('<input type="text" class="inputBox quotientTextBox" data="' + this.answerArray[i] + '">');
    }
    
    //getting the full dividend without the decimal point and trailing zeros.
    var answer = this.answer.toString().replace('.','');
    answer = parseInt(answer*this.divisor);
    answer = answer.toString();
    divArr=this.dividendArray.slice();
    for(var i = this.dividendArray.length; i < answer.length; i++){
    	if(answer[i] == '0'){
    		divArr.push('0');
    	}
    }
	
	//adding the dividend in their respective text boxes
	for (var i = 0; i < divArr.length; i++) {
		$("#row3 #column" + (4 + i)).css({ 'border-top': '2px solid' });
	    
	    //add all dividend zero, but if its a trailing zero then don't
	    if(i < this.dividendArray.length){
		 	$("#row3 #column" + (4 + i)).html('<input type="text" class="systemTextBox validate" disabled value=' + divArr[i] + ' data=' + divArr[i] + '>');
	    }
	    else{
		 	$("#row3 #column" + (4 + i)).html('<input type="text" class="systemTextBox validate containsZero" disabled value="" data=' + divArr[i] + '>');
	    }
	}
	 
    var startIndex = 0;
    if (this.answerArray[0] == "0" && this.answerArray[1].indexOf('.') == -1) {
        startIndex = 1;
    }
	
	for( ansIndex = startIndex ; ansIndex < this.answerArray.length; ansIndex++)
    {	 
    	//if divisor is more than current digit for division then add the next digit also
	    if (this.divisor > currentDigitForDivision) {
            if (this.dividendArray[ansIndex] != undefined) {
                if (this.dividendArray[ansIndex].indexOf('.') == -1){
                	if(!(ansIndex == 0 && this.answerArray[0] == "0" && this.dividendArray[ansIndex +1].indexOf('.') != -1))
                    	currentDigitForDivision = parseInt(currentDigitForDivision) + '' + parseInt(this.dividendArray[ansIndex]);
                }
                else{
	                 currentDigitForDivision = parseInt(currentDigitForDivision) + '' + this.dividendArray[ansIndex].substr(1, this.dividendArray[ansIndex].length);
                }
            }
            else {
                currentDigitForDivision = currentDigitForDivision + '0';
            }
        }
        currentDigitForDivision = currentDigitForDivision.toString();
		
		//storing multiplication result for setting it in data of answer inputs	
        if (this.answerArray[ansIndex] != undefined){
            if (this.answerArray[ansIndex].indexOf('.') == -1) {
                multiplyResult = this.divisor * this.answerArray[ansIndex];
            }
            else {
                multiplyResult = this.divisor * this.answerArray[ansIndex].substr(1, this.answerArray[ansIndex].length);
            }
        }
        multiplyResult = multiplyResult.toString();
        
        //ansIndex++;
        result = currentDigitForDivision % this.divisor;
		result = result.toString();
		
		//adding zeros prior to multiplication result if current digit is having more digits than multiplication result.
		multiplyResult = (currentDigitForDivision.length > multiplyResult.length)? '0' + multiplyResult: multiplyResult;

		//adding zero prior to remainder result if the multiplication result is having more digits than result
        result = (multiplyResult.length > result.length)? '0' + result : result;
		
        for (var j = 0; j < multiplyResult.length; j++) {
            if(j==0)
            {
                $("#row" + (rowCounter) + " #column" + (columnCounter + j - 1)).html('<span style="display:none">-</span>');
                minusArray.push("#row" + (rowCounter) + " #column" + (columnCounter + j - 1)+' span');
            }    
            $("#row" + (rowCounter) + " #column" + (columnCounter + j)).html('<input id="' + j + '" type="text" class="inputBox valueTextBox validate" data="' + multiplyResult.toString()[j] + '">');
            setFlow.push("#row" + (rowCounter) + " #column" + (columnCounter + j) + ' input');
        }
        rowCounter++;

        for (var j = multiplyResult.length - 1; j >= 0; j--) {
            $("#row" + (rowCounter) + " #column" + (columnCounter + j)).html('<input id="' + j + '" type="text" class="inputBox resultTextBox validate" data="' + result.toString()[j] + '">');
            setFlow.push("#row" + (rowCounter) + " #column" + (columnCounter + j) + ' input');
        }
		
        if ((ansIndex < this.answerArray.length - 1)) {
        	
            if (!isNaN(parseInt(this.dividendArray[ansIndex + 1]))) {
                $("#row" + (rowCounter) + " #column" + (columnCounter + multiplyResult.length)).html('<input id="' + j + '" type="text" class="inputBox resultTextBox validate" data="' + divArr[ansIndex + 1] + '" carry="true">');
                setFlow.push("#row" + (rowCounter) + " #column" + (columnCounter + multiplyResult.length) + ' input');
                setFlow.push("moveToMainScreen()");
            }
            else {
            	var actualAnswer = $('#row3 #column' + (columnCounter + multiplyResult.length) + ' input').attr('data');
		        actualAnswer = (parseFloat(actualAnswer) < 1) ? actualAnswer.substring(1) : '' + actualAnswer; 
		        actualAnswer = (actualAnswer == '')? '' + 0 : actualAnswer;
		         
                $("#row" + (rowCounter) + " #column" + (columnCounter + multiplyResult.length)).html('<input id="' + j + '" type="text" class="inputBox resultTextBox validate" data=' + actualAnswer + ' carry="true">');
                setFlow.push("moveToMainScreen()");
                setFlow.push("#row" + (rowCounter) + " #column" + (columnCounter + multiplyResult.length) + ' input');
            }
        }
		rowCounter++;
		
        columnCounter += (multiplyResult.length - 1);
        currentDigitForDivision = parseInt(result);
    }
    
    minusArray=minusArray.reverse();
    //inserting last zeros entry in dividend
    var pointlessLength = this.dividendArray.join();
    pointlessLength = pointlessLength.replace(/,/g, '');
    pointlessLength = pointlessLength.split('.');
    pointlessLength = pointlessLength[0];
    
    for(var incrementor = 0; incrementor < setFlow.length; incrementor++){
    	if(setFlow[incrementor] == "moveToMainScreen()"){
    		continue;
    	}
    	
    	var element = $(setFlow[incrementor])[0];
    	
    	if( element == undefined){
    		continue;
    	}
    	
    	var column = $(element).parent();
    	var columnNo = column[0].id;
    	columnNo = columnNo.replace('column', '');
    	
    	var row = $(column).parent();
    	var rowNo = row[0].id;
    	rowNo = rowNo.replace('row', '');
    	
    	if(rowNo % 2 == 1){
    		var nextDigit = parseInt(columnNo) + 1;
    		var nextElement = $('#row' + rowNo + ' #column' + nextDigit + ' input')[0];
    		
    		if(nextElement != undefined){
    			continue;
    		}
    		
    		if(columnNo - 3 > pointlessLength.length){
    			var stringToAdd = '#row3 #column' + columnNo + ' input';
    			if(setFlow.indexOf(stringToAdd) == -1){
    				if($(stringToAdd).attr('value') == ''){		
    					specialPrompts.push(stringToAdd);
    					specialPrompts.push(promptArr['whatDigitCanBePlacedHere']);
		    			setFlow.splice(incrementor, 0 , stringToAdd);
						specialPrompts.push('#row' + rowNo + ' #column' + columnNo + ' input');
						specialPrompts.push(promptArr['bringDown']);
						incrementor++;
					}
	    			stringToAdd = '#row2 #column' + columnNo + ' input';
	    			setFlow.splice(incrementor + 1, 0, stringToAdd);
	    			specialPrompts.push(stringToAdd);
					specialPrompts.push(promptArr['nowEnterNextQuotient']);
    			}
    		}
    	}
    }
    

    $(".validate").bind('keypress', function (e) {
        e.stopPropagation();
        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
        if (e.keyCode != 9 && e.keyCode != 8)// && e.keyCode!=37 && e.keyCode!=38 && e.keyCode!=39 && e.keyCode!=40)
        {
            if ($(this).val().length > 0 || ((e.keyCode < 48 || e.keyCode > 57))) {
                e.preventDefault();
            }
        }
    });
     
    $(".systemTextBox").bind('keypress', function (e) {
        e.stopPropagation();
        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
        if (e.keyCode == 13) {
            var t = $(this);
            setTimeout(function () {
                if (t.val() == t.attr('data')){
                	$(this).attr('disabled', 'disabled');
                    $(setFlow[++arrayCounter]).removeAttr('disabled');
                    $(setFlow[arrayCounter]).focus();
                    $(setFlow[arrayCounter]).addClass('answeredInput');
                    showSpecialPrompts(arrayCounter);
                }
                else {
                    //Incorrect carry
                    incorrectCarryCounter++;
                    if(incorrectCarryCounter==1)
                    {
                        currentValue='';
                        $('.systemTextBox').each(function()
                        {
                            if( $('#row3' + ' #' + $(theElement).parent()[0].id + ' input')[0]==this)
                            {
                                currentValue+='.'+this.value;
                            }
                            else
                            {
                                currentValue+=this.value;
                            }
                        });

                        showPrompt(replaceDynamicText(promptArr['incorrectCarry1'],textBx.numberLanguage,'textBx'));
                    }    
                    else if(incorrectCarryCounter==2)
                    {
                        currentValue='';
                        $('.systemTextBox').each(function()
                        {
                            if($(this).val()!="")
                            {
                                if( $('#row3' + ' #' + $(theElement).parent()[0].id + ' input')[0]== this)
                                {
                                    currentValue+='.'+$(this).attr('data');
                                }
                                else
                                {
                                    currentValue+=$(this).attr('data');
                                }
                           
                            }
                        });

                            
                        showPrompt(replaceDynamicText(promptArr['incorrectCarry2'],textBx.numberLanguage,'textBx'));
                    }
                    else
                    {

                        if(isNaN(parseInt(t.val()))){
                            showPrompt(promptArr['giveValidInput']);
                            return;
                        }
                        else
                        {
                            t.val(t.attr('data')).attr('disabled',true);
                            arrayCounter++;
                            $(setFlow[arrayCounter]).addClass('answeredInput').removeAttr('disabled').focus();
                            $("#sparkiePromptBubble").hide();
                        }

                        //callTablePanel();
                        incorrectCarryCounter=0;
                    }
                }
            }, 200);
            e.preventDefault();
        }
        if (e.keyCode != 9 && e.keyCode != 8)// && e.keyCode!=37 && e.keyCode!=38 && e.keyCode!=39 && e.keyCode!=40)
        {
            if ($(this).val().length > 1 || ((e.keyCode < 48 || e.keyCode > 57))) {
                e.preventDefault();
            }
        }
    });
    
    // Quotient Text Box Events
    $(".quotientTextBox").attr('disabled', 'true');
    //$($("#row2 input")[quotientCounter]).attr('disabled',false);
    $(".quotientTextBox").bind('keypress', function (e) {
        e.stopPropagation();
        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
        if (e.keyCode == 13) {
            enteredValue = parseInt($(this).val());
            if(isNaN(enteredValue)){
            	showPrompt(promptArr['giveValidInput']);
            	return;
            }
            checkQuotient($(this));
            e.preventDefault();
        }
        if (e.keyCode != 9 && e.keyCode != 8)// && e.keyCode!=37 && e.keyCode!=38 && e.keyCode!=39 && e.keyCode!=40)
        {
            if ($(this).val().length > 0 || ((e.keyCode < 48 || e.keyCode > 57))) {
                e.preventDefault();
            }
        }
    });
    ////////////////////////////
    $(".valueTextBox").attr('disabled', 'true');
    $(".valueTextBox").bind('keypress', function (e) {
        e.stopPropagation();
        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
        if (e.keyCode == 13) {
	 	if(isNaN(parseInt(this.value))){
	    	showPrompt(promptArr['giveValidInput']);
	    	return;
	    }
        checkValue($(this));
        e.preventDefault();
        }
        if (e.keyCode != 9 && e.keyCode != 8)// && e.keyCode!=37 && e.keyCode!=38 && e.keyCode!=39 && e.keyCode!=40)
        {
            if ($(this).val().length > 1 || (e.keyCode < 48 || e.keyCode > 57)) {
                e.preventDefault();
            }
        }
    });

    $(".resultTextBox").attr('disabled', 'true');

    $(".resultTextBox").bind('keypress', function (e) {
        e.stopPropagation();
        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
        if (e.keyCode == 13) {
        	if(isNaN(parseInt(this.value))){
            	showPrompt(promptArr['giveValidInput']);
            	return;
            }
            checkResult();
            e.preventDefault();
        }
        if (e.keyCode != 9 && e.keyCode != 8)// && e.keyCode!=37 && e.keyCode!=38 && e.keyCode!=39 && e.keyCode!=40)
        {
            if ($(this).val().length > 1 || (e.keyCode < 48 || e.keyCode > 57)) {
                e.preventDefault();
            }
        }
    });
};

function checkQuotient(element) {
	$('#quotientPointer').hide();
	$('#quotientCross').hide();
	$("#sparkiePromptBubble").hide();
	
    quotientAttempt++;
    if (quotientCounter == 0 && $(element).attr('data') == 0 && ($($('#row2 input')[quotientCounter + 1]).attr('data').indexOf('.') == -1)) { 
    	var actualAnswer = $(element).attr('data');
        actualAnswer = (parseFloat(actualAnswer) < 1) ? actualAnswer.substring(1)  :'' + actualAnswer; 
        actualAnswer = (actualAnswer == '')? '' + 0 : actualAnswer; 
        if (actualAnswer == $(element).val()) {
            $($("#row2 input")[quotientCounter]).attr('disabled', true);
            quotientCounter++;
            $("#sparkieMessage").html("You need not to write 0");
            $("#sparkiePromptBubble").show();
            $($("#row2 input")[quotientCounter]).attr('disabled', false);
            $($("#row2 input")[quotientCounter]).focus();
            $($("#row2 input")[quotientCounter]).addClass('answeredInput');
            
        }
        else {
            //Wrong Place
            $(".quotientTextBox").attr('disabled', 'true');
            if (quotientAttempt == 1) {
                quotientAttempt++;
                $("#screen6").show();
                currentScreen = 6;
            }
            else if (quotientAttempt == 2 || quotientAttempt == 3) {
                quotientAttempt++;
                $("#" + currentScreen).hide();
                $("#screen8").show();
                currentScreen = 8;
            }
            else
                commonFlow(element);
        }
    }
    else {
        $(".quotientTextBox").attr('disabled', 'true');
        var actualAnswer = $(element).attr('data');
        actualAnswer = (parseFloat(actualAnswer) < 1) ? actualAnswer.substring(1)  :'' + actualAnswer; 
        actualAnswer = (actualAnswer == '')? '' + 0 : actualAnswer; 
        if (actualAnswer == $(element).val()) {
        	showPrompt(promptArr['writeProduct']);
            $($("#row2 input")[quotientCounter]).attr('disabled', true);
            quotientCounter++;
            $($("#row" + rowCount + " input")).attr('disabled', true);
            
            rowCount++;
            $($("#row" + rowCount + " input")[0]).attr('disabled', false);
            $($("#row" + rowCount + " input")[0]).focus();
            $($("#row" + rowCount + " input")[0]).addClass('answeredInput');
        }
        else {
            commonFlow(element);
        }
    }
}

var wrongCheck = 0;
var tempVal='';
var tempCorrect='';
var actual = '';

function checkValue() {
    var flag = 0;
    $('#sparkiePromptBubble').hide();
    
    var tempArr = $($("#row" + (rowCount) + " input"));
    $($("#row" + (rowCount) + " input")).show();
    var parentArr = $($("#row" + (rowCount-1) + " input"));
    for (var i = 0; i < tempArr.length; i++) {
        if ($(tempArr[i]).val() == "") {
            $(tempArr[i]).removeAttr('disabled');
            $(tempArr[i]).focus();
            $(tempArr[i]).addClass('answeredInput');
            return;
        }
    }
    tempVal='';
    tempCorrect='';
    actual = '';
    for (var i = 0; i < tempArr.length; i++) {
        tempVal += $(tempArr[i]).val();
        actual += $(parentArr[i]).val();        
        tempCorrect += $(tempArr[i]).attr('data');
        
        if ($(tempArr[i]).val() != $(tempArr[i]).attr('data')) {
            flag = 1;
            break;
            
        }
    }
    if(!$(tempArr[tempArr.length-2]).hasClass('resultTextBox') && wrongProductAttempter == 0)
        arrayCounter += (tempArr.length);
        
    for (var i = 0; i < tempArr.length; i++) {
        if($(tempArr[i]).val() == $(tempArr[i]).attr('data') && $(tempArr[i]).attr('carry')=="true") {
            //arrayCounter--;
            //moveToMainScreen();
            return;
        }
    }
    if (flag == 0) {
        //Correct
        $($("#row" + rowCount + " input")).attr('disabled', true);
        rowCount++;
        //$($("#row" + rowCount + " input")).attr('disabled', false);
        //$("#row" + rowCount + " input").addClass('answeredInput');
        showPrompt(promptArr['writeDifference']);
        wrongProductAttempter = 0;
        comingFromWrongProduct = false;
        setTimeout(function () {
            $(minusArray.pop()).show();
            $(setFlow[++arrayCounter]).attr('disabled', false);
            $(setFlow[arrayCounter]).focus();
            $(setFlow[arrayCounter]).addClass('answeredInput');
            showSpecialPrompts(arrayCounter);
        }, 200);        
    }
    else {
    	wrongProductAttempter++;
    	if(wrongProductAttempter > 1){
			comingFromWrongProduct = true;    		
        	$(setFlow[arrayCounter]).attr('disabled', 'disabled');
            callTablePanel(1);
    	}
    	else{
    		showPrompt(promptArr['readCareFully']);
    		$('#screen13').show();
    		$(setFlow[arrayCounter]).attr('disabled', 'disabled');
    		enteredInput = parseInt($('#row2 input')[quotientCounter - 1].value);
    		$('#screen13Info').html(replaceDynamicText(promptArr['multiplyWrongProduct'],gameObj.numberLanguage,''));
    	}
    }
}

var arrayCounter = 0;
var wrongCheck = 0;
var reallyReallyDumbKidIncrementor = 0;

function checkResult() {
	$('#sparkiePromptBubble').hide();
    var flag = 0;
    var tempArr = $($("#row" + rowCount + " input"));
    for (var i = 0; i < tempArr.length; i++) {
        if ($(tempArr[i]).val() == "" && $(tempArr[i]).attr('carry') != "true") {
            $(tempArr[i]).removeAttr('disabled');
            $(tempArr[i]).focus();
            $(tempArr[i]).addClass('answeredInput');
            arrayCounter++;
            return;
        }
    }
    var tempVal = '';
    var tempCorrect = '';
    for (var i = 0; i < tempArr.length; i++) {
        if ($(tempArr[i]).val() != $(tempArr[i]).attr('data') && $(tempArr[i]).attr('carry') != "true") {
            flag = 1;
            break;
        }
    }
    for (var i = 0; i < tempArr.length; i++) {
        if ($(tempArr[i]).val() == "" && $(tempArr[i]).attr('carry') == "true" && quotientCounter < textBx.answerArray.length + 1) {
            if ($(tempArr[i]).val().indexOf('.') != -1){
                //moveToMainScreen();
            }
            //return;    
        }
    }
    if (flag == 0) {
        //Correct
        wrongCheck = 0;
        var actualAnswer = $(setFlow[arrayCounter]).attr('data');
        actualAnswer = (parseFloat(actualAnswer) < 1) ? actualAnswer.substring(1)  :'' + actualAnswer; 
        actualAnswer = (actualAnswer == '')? '' + 0 : actualAnswer; 
        if($(setFlow[arrayCounter]).attr('value') != actualAnswer){
        	showPrompt(promptArr['bringDown2']);
        	reallyReallyDumbKidIncrementor++;
        	if(reallyReallyDumbKidIncrementor>1){
        		reallyReallyDumbKidIncrementor = 0;
        		$(setFlow[arrayCounter]).attr('value', $(setFlow[arrayCounter]).attr('data')).attr('disabled',true); 
                
                /* V2 Change */
                
                    arrayCounter++;
                    if (setFlow[arrayCounter] == "moveToMainScreen()") {
                        eval(setFlow[arrayCounter]);
                        return;
                    }
                    $(setFlow[arrayCounter]).attr('disabled', false);
                    $(setFlow[arrayCounter]).addClass('answeredInput');
                    $(setFlow[arrayCounter]).focus();
                /**************/
        	}
        	return;
        }
        
        if (quotientCounter >= textBx.answerArray.length) {
            
            attemptCounter = 0;
            setTimeout(function () {
                $("#row" + (rowCount) + " input").attr("disabled", 'disabled');
                $("#ques1").attr({ "value": "", "disabled": false });
                $("#ques1").focus(); 
                $("#row2 input").attr('disabled',true);
                showPrompt(promptArr['nowEnterAnswer']);
            }, 200);
        }
        else {
            if ($(tempArr[tempArr.length - 1]).attr('carry') == "true")
                arrayCounter++;
                
            setTimeout(function () {
                if (setFlow[arrayCounter] == "moveToMainScreen()") {
                    eval(setFlow[arrayCounter]);
                }
                else {
                	var rowNo = parseInt(setFlow[arrayCounter].split('row')[1], 10);
                	if(rowNo != 2 && rowNo!= 3){
                		var columnNo = parseInt(setFlow[arrayCounter].split('column')[1], 10);
                		if($('#row' + rowNo + ' #column' + (columnNo + 1) + ' input')[0] == undefined){
                			showPrompt(promptArr['bringDown']);
                		}
                	}
                	$("#row" + (rowCount) + " input").attr("disabled", 'disabled');
                    $(setFlow[arrayCounter]).attr('disabled', false);
                    $(setFlow[arrayCounter]).addClass('answeredInput');
                    $(setFlow[arrayCounter]).focus();
                    showSpecialPrompts(arrayCounter);
                }
            }, 200);
        }
    }
    else {
    	//incorrect flow
        wrongCheck++;
        switch (wrongCheck) {
            case 1:
                $("#sparkiePromptBubble").show();
                $("#sparkiePromptBubble").html("This is incorrect. check the difference again!");
                break;
            case 2:
                $("#screen" + currentScreen).hide();
                $("#screen10").show();
                var status = getCurrentStatus(1);
                $("#screen10Info").html("<span style='color:red'>Subtract " + status[1] + " </span>from " + (textBx.divisor * status[0]));
                currentScreen = 10;
		        $('#row' + rowCount + ' input').attr('disabled','disabled');
                break;
            case 3:
                wrongCheck = 0;
                $("#screen" + currentScreen).hide();
                $("#screen10").show();
                var t = tempCorrect - actual;
                var status = getCurrentStatus(1);
                $("#screen10Info").html(status[1] + " - " + (status[0] * textBx.divisor) + " = " + (status[1] - (status[0] * textBx.divisor)));
                currentScreen = 10;
		        $('#row' + rowCount + ' input').attr('disabled','disabled');
                break;
        }
    }
}

var attempts = 0;
var bestAnswer = 0;
var _number1 = 1;
var tablePanelInput;
var tablePanelObject = {};
var stupidAttempter = 0;

function tablePanel(_divisor, _enteredValue, _number) {
	 $("#sparkieMessage").html(promptArr['multiply']);
    var _element = $('#screen11')[0];

    //creating table here
    attempts = 0;
	$('#bottomPanel .hide').hide();
	$(_element).show();

	bestAnswer = parseInt(_number / _divisor);
	_number1 = _number;
	
	var tip = document.createElement('div');
	if(comingFromWrongProduct)
		tip.innerHTML = "Let's multiply " + _divisor + " with " + _enteredValue + ".";
	else{
		tip.innerHTML = "Let's multiply " + _divisor + " with " + _enteredValue + " and see if it works.";
	}
	
	var table = document.createElement('table');
	table.className = 'smallCalcTable';
	for(var i = 0 ; i < 5; i++){
		var row = table.insertRow(-1);
		for(var j = 0; j  < 4; j++){
			var cell = row.insertCell(-1);
		}
	}
	
	var cell = table.rows[1].cells[2];
	cell.innerHTML = _divisor;
	
	cell = table.rows[2].cells[1];
	cell.innerHTML = 'X';
	
	cell = table.rows[2].cells[2];
	cell.innerHTML = _enteredValue;
	
	var input1= document.createElement('input');
	var input2= document.createElement('input');
	input1.className = 'inputBox';
	input2.className = 'inputBox';
	input1.type = 'text';
	input2.type = 'text';
	$(input1).bind('keypress', onSmallCalcTableInputKeyPress);
	$(input2).bind('keypress', onSmallCalcTableInputKeyPress);
	$(input1).forceNumeric();
	$(input2).forceNumeric();

	$(table.rows[3].cells[1]).html(input1);
	$(table.rows[3].cells[2]).html(input2);
	
	var button = document.createElement('button');
	button.innerHTML ='check';
	button.id = 'tablePanelCheck';
	
	var div = document.createElement('div');
	div.className = 'additionalDiv';
	
	$(_element).html('');
	$(_element).append(tip);
	$(_element).append(table);
	$(_element).append(button);
	$(_element).append(div);

	button.addEventListener('click', onTablePanelCheckClick, false);
};

function onTablePanelCheckClick() {
	if(comingFromWrongProduct){
		for(var i = 0; i <= arrayCounter; i++){
			if(setFlow[i] != 'moveToMainScreen()'){
				var toEnter = parseFloat($(setFlow[i]).attr('data'));
				toEnter = toEnter < 1 ? (toEnter * 10): toEnter; 
				$(setFlow[i]).attr('value', toEnter);
				$(setFlow[i]).attr('disabled', 'disabled');
			}
		}
		checkValue();		
		$('#screen11').hide();
		return;
	}
    var table = $('.smallCalcTable')[0];
    var input = $('.smallCalcTable input');
    var num1 = input[0].value;
    var num2 = input[1].value;
		
    var finalAnswer = num1 + num2;
    

    var num3 = parseInt(table.rows[1].cells[2].innerHTML);
    var num4 = parseInt(table.rows[2].cells[2].innerHTML);
    
    tablePanelObject.num3 = num3;
	tablePanelObject.num4 = num4;
	tablePanelObject.num5 = num4 + 1;
	tablePanelObject.num6 = num3 * (num4 + 1);
	tablePanelObject.num7 = num4 - 1;
	tablePanelObject.num8 = num3 * (num4 - 1);
	tablePanelObject.num9 = num4 * num3;
	tablePanelObject._number1 = _number1;
	tablePanelObject.bestAnswer = bestAnswer;
    
    var localInputs = $('.smallCalcTable input');
    
    if((num3 * num4) < 10){
    	if(num2 == ''){
			$(localInputs[1]).focus();
	    	return;	
    	}
	}
	else{
		if(num2 == ''){
			$(localInputs[1]).focus();
			return;
		}
		else if(num1 == ''){
			$(localInputs[0]).focus();
			return;
		}
	}

    if (num3 * num4 == parseFloat(finalAnswer)) {
	    
       //$('#multiplyTable').append(num3 + ' x ' + num4 + ' = ' + (num3 * (num4 + 1)));
        if(alreadyTriedNumbersInTablePanel.indexOf(num4) == -1){
	        $('#multiplicationTables').append('<br>' + num3 + ' x ' + num4 + ' = ' + (num3 * num4));
        }
        alreadyTriedNumbersInTablePanel.push(num4);
        
        $('#tablePanelCheck')[0].innerHTML = 'OK';
        if (num4 == bestAnswer) {
            promptCorrectAnswer = num3 * num4;
            text = replaceDynamicText(promptArr['tablePanel1'], gameObj.numberLanguage, 'tablePanelObject');
            $('#screen11').html(text);
            var okButton = document.createElement('button');
            okButton.innerHTML = 'OK';
            okButton.id = 'anotherOneOfThoseOkButton';
            $('#screen11').append(okButton);
            $(okButton).bind('click', function () {
                //done 
                $($("#row2 input")[quotientCounter]).attr("value", num4);
                $("#screen11").hide();
                checkQuotient($('#row2 input')[quotientCounter]);
            });
        }
        else {
        	if (num4 > bestAnswer){
	            text = replaceDynamicText(promptArr['tablePanel2'], gameObj.numberLanguage, 'tablePanelObject');
        	}
        	else{
	            text = replaceDynamicText(promptArr['tablePanel3'], gameObj.numberLanguage, 'tablePanelObject');
        	}
            $('#screen11').html(text);
            
            tablePanelInput = document.createElement('input');
            $(tablePanelInput).forceNumeric();
            tablePanelInput.type = 'text';
            tablePanelInput.id = 'tablePanelInput';
            $(tablePanelInput).bind('keypress', onlyOneDigit);
            $('#screen11').append(tablePanelInput);
            $(tablePanelInput).css('display', 'block');
            $(tablePanelInput).focus();

            var illegalNextTryDiv = document.createElement('div');
            illegalNextTryDiv.id = 'illegalNextTryDiv';
			$('#screen11').append(illegalNextTryDiv);
            $(illegalNextTryDiv).css('display', 'block');	            

            var okButton = document.createElement('button');
            okButton.innerHTML = 'OK';
            okButton.id = 'tablePanelButton';
            $('#screen11').append(okButton);
            $(okButton).css('display', 'block');
            stupidAttempter = 0;
            
            $(tablePanelInput).bind('keypress', function(e){
            	 e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // mozilla hack..
    			if (e.keyCode == 13) {
            		onTablePanelButtonClick();
            	}
            });
            
            $(okButton).bind('click', onTablePanelButtonClick);
        }
    }
    else {
        attempts++;
 		showPrompt(promptArr['readCareFully']);
        if (attempts < 4) {
            $('.additionalDiv').html(promptArr['tablePanelWrong' + attempts]);
        }
        else {
	        $('.smallCalcTable input').attr('disabled','disabled');
        	$('.additionalDiv').html(miscArr['okToContinue']);
            $('#tablePanelCheck')[0].innerHTML = 'OK';
            var answer = parseInt(num3 * num4);
            if (answer > 9) {
                input[0].value = parseInt(answer / 10);
            }
            else {
                input[0].value = 0;
            }
            input[1].value = answer % 10;
            //$('#multiplicationTables').append('<br>' + num3 + ' x ' + num4 + ' = ' + (num3 * num4));
        }
    }
}

function onTablePanelButtonClick() {
	if(isNaN(parseInt(tablePanelInput.value))){
		showPrompt(promptArr['giveValidInput']);
		return;	
	}
	
	stupidAttempter++;
	if(alreadyTriedNumbersInTablePanel.indexOf(parseInt(tablePanelInput.value)) == -1 || parseInt(tablePanelInput.value) == bestAnswer){
		if((tablePanelObject.num4 > bestAnswer) && (tablePanelInput.value > tablePanelObject.num4)){
    		$('#illegalNextTryDiv').html(replaceDynamicText(promptArr['tablePanel4'], gameObj.numberLanguage, 'tablePanelObject'));
		}
		else if((tablePanelObject.num4 < bestAnswer) && (tablePanelInput.value < tablePanelObject.num4)){
    		$('#illegalNextTryDiv').html(replaceDynamicText(promptArr['tablePanel5'], gameObj.numberLanguage, 'tablePanelObject'));
		}
		else{
        	tablePanel(tablePanelObject.num3, tablePanelInput.value , tablePanelObject._number1);
		}
	}
    else{
    	$('#illegalNextTryDiv').html(promptArr['tablePanel6']);
    }
    if(stupidAttempter > 1){
    	$(tablePanelInput).attr('disabled','disabled');
    	if(tablePanelObject.num4 > bestAnswer){
    		var bestNextAnswer = tablePanelObject.num7;
    		
    		while(alreadyTriedNumbersInTablePanel.indexOf(bestNextAnswer) != -1 && bestAnswer != bestNextAnswer){
    			bestNextAnswer--;
    		}
    		
    		tablePanelObject.bestNextAnswer = bestNextAnswer;
    		tablePanelInput.value = bestNextAnswer;
    		
    		$('#illegalNextTryDiv').html(replaceDynamicText(promptArr['tablePanel7'], gameObj.numberLanguage, 'tablePanelObject'));
    	}
    	else{
    		var bestNextAnswer = tablePanelObject.num5;
    		
    		
    		while(alreadyTriedNumbersInTablePanel.indexOf(bestNextAnswer) != -1 && bestAnswer != bestNextAnswer){
    			bestNextAnswer++;
    		}
    		
    		tablePanelObject.bestNextAnswer = bestNextAnswer;
    		tablePanelInput.value = bestNextAnswer;
    		
    		$('#illegalNextTryDiv').html(replaceDynamicText(promptArr['tablePanel8'], gameObj.numberLanguage, 'tablePanelObject'));
    	}
    }
}

function commonFlow(element) {
	var status = getCurrentStatus();
    if (commonFlowFlag == 0) {
        $("#sparkieMessage").html(promptArr['tryAnswering']);
        $("#sparkiePromptBubble").show();
        var actualAnswer = $(element).attr('data');
        actualAnswer = (parseFloat(actualAnswer) < 1) ? actualAnswer.substring(1) : '' + actualAnswer; 
        actualAnswer = (actualAnswer == '')? '' + 0 : actualAnswer;
        
        if (actualAnswer > parseInt($(element).val())) {
            $("#mess").html(replaceDynamicText(promptArr['wrong_quotient_less'], gameObj.numberLanguage, 'textBx'));
        }
        else if (actualAnswer < parseInt($(element).val())) {
            $("#mess").html(replaceDynamicText(promptArr['wrong_quotient_more'], gameObj.numberLanguage, 'textBx'));
        }
        $("#" + currentScreen).hide();
        $("#screen9").show();
        currentScreen = 9;
        commonFlowFlag = 1;
    }
    else if(commonFlowFlag==1)
    {
        $("#sparkieMessage").html(promptArr['tryAnswering']);
        $("#sparkiePromptBubble").show();
        var actualAnswer = $(element).attr('data');
        actualAnswer = (parseFloat(actualAnswer) < 1) ? actualAnswer.substring(1) : '' + actualAnswer; 
        actualAnswer = (actualAnswer == '')? '' + 0 : actualAnswer;
        
        if (actualAnswer > parseInt($(element).val())) {
            $("#mess").html(replaceDynamicText(promptArr['wrong_quotient_less'], gameObj.numberLanguage, 'textBx'));
        }
        else if (actualAnswer < parseInt($(element).val())) {
            $("#mess").html(replaceDynamicText(promptArr['wrong_quotient_more'], gameObj.numberLanguage, 'textBx'));
        }
        $("#" + currentScreen).hide();
        $("#screen9").show();
        currentScreen = 9;
        commonFlowFlag=2;
    }
    else {
    	 tablePanel(parseInt(textBx.divisor), parseInt($('#row2 input')[quotientCounter].value), status[1]);
         commonFlowFlag=0;
    }
}

function showPrompt(passedText){
	$('#sparkiePromptBubble').show();
	$('#sparkiePromptBubble').html(passedText);
}

function sparkiePrompt() {
    $("#sparkiePromptBubble").show();
    $("#sparkiePromptBubble").html(promptArr['sparkiePrompt1']);
    setFocus();
}

function sparkiePrompt1() {
    $("#sparkiePromptBubble").show();
    $("#sparkiePromptBubble").html(promptArr['sparkiePrompt2']);
    $($("#row2 input")[quotientCounter]).attr('value', '');

    $($("#row2 input")[quotientCounter]).attr('disabled', true);
    quotientCounter++;
    setFocus();
}

function onSmallCalcTableInputKeyPress(e){
	onlyOneDigit(e);
	e.keyCode = e.keyCode||e.which;
	if(e.keyCode == 13){
		onTablePanelCheckClick($('#tablePanelCheck')[0]);
	}
}

function onlyOneDigit(e){
	e.keyCode = e.keyCode||e.which;
	if(e.target.value.length > 0){
		if(e.keyCode != 8 && e.keyCode!= 37 && e.keyCode!=39 && e.keyCode!=46 && e.keyCode!=9)
			e.preventDefault();
	}
}

function showSpecialPrompts(arrayCounter){
	var indexOf = specialPrompts.indexOf(setFlow[arrayCounter]);
    if(indexOf != -1){
    	showPrompt(specialPrompts[indexOf+1]);
    }
    return indexOf;
}