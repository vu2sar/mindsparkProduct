var textBx;
var levelsAttempted 	= "L1";
var levelWiseStatus 	= 0;
var levelWiseScore 		= 0;
var levelWiseTimeTaken 	= 0;
var extraParameters 	= '';
var completed 	= 0;
var currentScreen=0;
var attemptCounter=0;
var quotientCounter=-1;
var previousQuotientCounter=0;
var quotientAttempt=0;
var rowCount=3;
var coloumCount=4;
var commonFlowFlag = 0;
var setFlow = [''];

function division(divisor, dividend) {
    this.numberLanguage = "english";
    this.divisor = divisor;
    this.dividend = dividend;
    this.answer = this.dividend / this.divisor;
    this.dividendArray = this.dividend.toString().split("");
    var index = this.dividend.toString().indexOf('.');
    if (index != -1) {
        this.dividendArray.splice(index, 1);
        this.dividendArray[index] = '.' + this.dividendArray[index];
    }
    this.answerArray = this.answer.toString().split("");
}

// Create the grid and fill the numbers by system
division.prototype.createGrid = function () {
    container = "divisionTable";
    var data = '';
    data += '<div id="tablePanel" class="inline">';
    data += '<table id="myTable">';
    for (var i = 1; i <= 12; i++) {
        data += '<tr id="row' + i + '">';
        for (var j = 1; j <= 12; j++) {
            data += '<td id="coloum' + j + '">&nbsp;</td>';
        }
        data += '</tr>';
    }
    data += '</table>';
    data += '</div>';
    $("#" + container).html(data);

    // Will create an extra textbox for the carry purpose
    var ind = this.answer.toString().indexOf('.');
    var answerIndex = 0;
    if (ind != -1) {
        for (var i = 0; i < (this.answer.toString().length - ind - 1); i++) {
            $("#row3 #coloum" + (5 + this.dividendArray.length - 1 + i)).css({ 'border-top': '2px solid' });
            //if (i == 0) {
                $("#row3 #coloum" + (5 + this.dividendArray.length - 1 + i)).html('<input type="text" class="systemTextBox" data="0">');
            //}
            //else {
            //    $("#row3 #coloum" + (5 + this.dividendArray.length - 1 + i)).html('<input type="text" class="systemTextBox" data="' + this.answerArray[answerIndex++] + '">');
            //}
        }
    }

}
var number1;
var number2;
// Contains the screen prompts that are loaded when incorrect attempts are made
division.prototype.promptPanel = function () {
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
    data += '<button class="button" onclick="moveToMainScreen()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen4" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['digit_incorrect1'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="moveToMainScreen()">' + miscArr['ok'] + '</button>';
    data += '</div>';
    data += '<div id="screen5" class="hide screen" >';
    data += '<div>' + replaceDynamicText(promptArr['digit_incorrect2'], gameObj.numberLanguage, 'gameObj') + '</div>';
    data += '<button class="button" onclick="moveToMainScreen()">' + miscArr['ok'] + '</button>';
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
    data += '<button class="button" onclick="setFocus()"></button>'
    data += '</div>';
    data += '<div id="screen11" class="hide screen">';
    data += '</div>';
    data += '<div id="screen9" class="hide screen" >';

    data += '<div id="mess"></div>';
    //_divisor, _enteredValue, _number, _element  (divisor*multiplicationFactor),enteredValue,
    data += '<button class="button" onclick="tablePanel()">' + miscArr['yes'] + '</button>';
    data += '<button class="button" onclick="setFocus()">' + miscArr['no'] + '</button>';
    data += '<button class="button" onclick="tablePanel()">' + miscArr['notsure'] + '</button>';
    data += '</div>';
    data += '</div>';
    data += '</div>';
    $("#" + container).append(data);
    attemptCounter = 0;
    currentScreen = 1;
    textBx.createTextBoxes();
    quotientCounter++;
    moveToMainScreen();
    if (arrayCounter == setFlow.length - 2) {
        alert();
    }
}
// This will move to the screen on with the options Place the Decimal and Place the Digit button
function moveToMainScreen() {

    $("#sparkiePromptBubble").hide();
    $("#screen" + currentScreen).hide();
    $("#screen1").show();
    currentScreen = 1;
    if (attemptCounter == 2) {
        attemptCounter = 0;
        $($("#row2 input")[quotientCounter]).attr('disabled', false);
        $($("#row2 input")[quotientCounter]).focus();
        $("#sparkieMessage").html(promptArr['enterQuotient']);
        $("#sparkiePromptBubble").show();
        $("#screen1").hide();
        currentScreen = 1;
    }
}

// Called when press decimal button
function placeDecimal() {
    if (parseInt(textBx.answerArray[quotientCounter]) > 0 && attemptCounter < 2) {
        $("#sparkieMessage").html(promptArr['readCareFully']);
        attemptCounter++;
        $("#screen" + currentScreen).hide();
        if (attemptCounter == 1) {
            $("#screen2").show();
            currentScreen = 2;
        }
        else {
            $("#screen3").show();
            currentScreen = 3;
        }
        $("#sparkiePromptBubble").show();
    }
    else {
        arrayCounter++;
        $(setFlow[arrayCounter]).attr('disabled',false);
        $(setFlow[arrayCounter]).focus();
        $("#screen1").hide();
    }
}
// Called when press digit button
function placeDigit() {
    if (parseInt(textBx.answerArray[quotientCounter]) < 0 && attemptCounter < 2) {
        $("#sparkieMessage").html(promptArr['readCareFully']);
        attemptCounter++;
        $("#screen" + currentScreen).hide();
        if (attemptCounter == 1) {
            $("#screen4").show();
            currentScreen = 4;
        }
        else {
            $("#screen5").show();
            currentScreen = 5;
        }
        $("#sparkiePromptBubble").show();
    }
    else {
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
        $("#screen" + currentScreen).hide();chec
        $("#sparkiePromptBubble").show();
        $("#sparkiePromptBubble").html(promptArr['sparkiePrompt3']);
    }
    else {
        $("#screen" + currentScreen).hide();
        $("#screen7").show();
        currentScreen = 7;
    }
}

function showSubScreen8() {
    $("#screen8_button1").hide();
    $("#subScreen8").show();
    currentScreen = 8;
}

function setFocus() {
    //Set focus to the next textbox for user input
    $("#screen" + currentScreen).hide();
    $($("#row2 input")[quotientCounter]).attr('disabled', false);
    $($("#row2 input")[quotientCounter]).focus();
}
var enteredValue;
division.prototype.createTextBoxes = function () {
    // Step 1 Textbox  ( Will start creating the text box from the 4th row and 4th coloum)
    var rowCounter = 4;
    var coloumCounter = 4;
    var staticColoum = 4;
    var createLength = 0;
    var answer = 0;
    var result = 0;
    var decimalCheck = 0;
    var dividendCounter = 0;
    var greater = this.dividendArray[0];
    var result = 0;
    var multiplyResult = 0;
    var index = 0;
    var ansIndex = 0;
    for (var i = 0; i < (this.answerArray.length); i++) {
        if (this.answerArray[i] == '.') {
            this.answerArray[i] = '.' + this.answerArray[i + 1];
            this.answerArray.splice(i + 1, 1);
            //answerIndex++;
        }
    }
    if (this.divisor > greater) {
        index++;
        if (this.answerArray[0] == "0")
        { ansIndex++; }
    }
    else {
        if (this.answerArray[0] == "0") {
            index++;

        }
    }

    for (var i = index; i < this.answerArray.length; i++) {
        if (this.divisor > greater) {
            if (this.dividendArray[i] != undefined) {
                if (this.dividendArray[i].indexOf('.') == -1)
                    greater = parseInt(greater) + '' + parseInt(this.dividendArray[i]);
                else
                    greater = parseInt(greater) + '' + this.dividendArray[i].substr(1, this.dividendArray[i].length);
            }
            else {
                greater = greater + '0';
            }
        }

        if (this.answerArray[i] == "0" && i < 1) {
            i++;
        }
        if (this.answerArray[ansIndex].indexOf('.') == -1)
            multiplyResult = this.divisor * this.answerArray[ansIndex];
        else
            multiplyResult = this.divisor * this.answerArray[ansIndex].substr(1, this.answerArray[ansIndex].length);

        ansIndex++;
        result = greater % this.divisor;
        if (multiplyResult.toString().length > result.toString().length) {
            result = '0' + result;
        }
        console.log("DividendArray " + this.dividendArray[i]);
        console.log('Greater' + greater);
        console.log("Multiply" + multiplyResult);
        console.log('Result' + result);
        greater = parseInt(result);
    }


    //result = this.divisor * this.answerArray[0];
    //createLength = result.toString().length;
    //var counter = createLength;
    //// Create Quotient Textboxes
    //var index = 4;
    //var answerIndex = 0;
    //if (parseInt(textBx.dividendArray[0]) < textBx.divisor) {
    //    this.answerArray = "0" + this.answer;
    //    this.answerArray = this.answerArray.split("");
    //}
    //for (var i = index; i < (this.answerArray.length + index); i++) {
    //    if (this.answerArray[answerIndex] == '.') {
    //        this.answerArray[answerIndex] = '.' + this.answerArray[answerIndex + 1];
    //        this.answerArray.splice(answerIndex + 1, 1);
    //        //answerIndex++;
    //    }
    //    $("#row2 #coloum" + i).html('<input type="text" class="inputBox quotientTextBox" value="' + this.answerArray[answerIndex] + '" data="' + this.answerArray[answerIndex] + '">');
    //    answerIndex++;

    //}
    //var smaller = 0;
    //var result = 0;
    //var numberLength = 0;
    //var resultLength = 0;
    //var multiplyResult = 0;
    //var dividendPointer = 0;
    //var greater = parseInt(this.dividendArray[dividendPointer]);
    //if (this.divisor <= greater) {
    //    //greater=parseInt(this.dividendArray[dividendPointer]);
    //    smaller = this.divisor;
    //    staticColoum++;

    //}
    //else {
    //    if (this.dividendArray[dividendPointer + 1] != undefined) {
    //        greater = parseInt(greater + '' + parseInt(this.dividendArray[dividendPointer + 1]));
    //        $("#row" + (rowCounter) + " #coloum" + (staticColoum + dividendPointer)).html('<input type="text" class="inputBox valueTextBox validate" value="' + parseInt(this.dividendArray[dividendPointer + 1]) + '" data="' + parseInt(this.dividendArray[dividendPointer + 1]) + '">');
    //    }
    //    else {
    //        greater = parseInt(greater + '' + parseInt(0));
    //        $("#row" + (rowCounter) + " #coloum" + (staticColoum + dividendPointer)).html('<input type="text" class="inputBox valueTextBox validate" value="' + this.answerArray[answerIndex] + '" data="' + this.answerArray[answerIndex] + '">');
    //    }
    //    dividendPointer++;
    //    smaller = this.divisor;
    //}
    //var startIndex = 0;
    //if (this.answerArray[0] == "0") {
    //    startIndex++;
    //}


    //for (var i = startIndex; i < this.answerArray.length; i++) {
    //    console.log("Multiply :" + multiplyResult);
    //    console.log("Greater : " + greater);
    //    console.log("Smaller : " + smaller);
    //    if (this.answerArray[i].indexOf('.') == -1) {
    //        multiplyResult = (this.divisor * this.answerArray[i]);
    //        result = parseInt(greater) - parseInt(multiplyResult);
    //    }
    //    else {
    //        multiplyResult = (this.divisor * (this.answerArray[i].toString().substr(1, this.answerArray[i].toString().length)));
    //        result = parseInt(greater) - parseInt(multiplyResult);
    //    }

    //    numberLength = greater.toString().length;
    //    resultLength = result.toString().length;
    //    greater = result;

    //    if (numberLength > resultLength) {
    //        result = '0' + result;
    //    }
    //    createLength = numberLength;
    //    if (multiplyResult.toString().length < createLength) {
    //        multiplyResult = "0" + multiplyResult;
    //    }
    //    for (var j = 0; j < createLength; j++) {
    //        $("#row" + (rowCounter) + " #coloum" + (coloumCounter + j)).html('<input id="' + j + '" type="text" class="inputBox valueTextBox validate" value="' + multiplyResult.toString()[j] + '" data="' + multiplyResult.toString()[j] + '">');
    //        setFlow.push("#row" + (rowCounter) + " #coloum" + (coloumCounter + j) + ' input');
    //    }
    //    rowCounter++;
    //    for (var j = createLength - 1; j >= 0; j--) {
    //        $("#row" + (rowCounter) + " #coloum" + (coloumCounter + j)).html('<input id="' + j + '" type="text" class="inputBox resultTextBox validate" value="' + result.toString()[j] + '" data="' + result.toString()[j] + '">');
    //        setFlow.push("#row" + (rowCounter) + " #coloum" + (coloumCounter + j) + ' input');
    //    }

    //    coloumCounter += (createLength - 1);
    //    if (this.divisor <= parseInt(greater)) {
    //        //greater=parseInt(this.dividendArray[dividendPointer]);
    //        smaller = this.divisor;
    //        staticColoum++;
    //    }
    //    else {
    //        console.log("Carry" + parseInt(this.dividendArray[dividendPointer + 1]));
    //        if (this.dividendArray[dividendPointer + 1] != undefined) {
    //            greater = parseInt(greater + '' + parseInt(this.dividendArray[dividendPointer + 1]));
    //            if (this.dividendArray[dividendPointer + 1].indexOf('.') == -1)
    //                $("#row" + (rowCounter) + " #coloum" + (coloumCounter + 1)).html('<input type="text" class="inputBox valueTextBox validate" carry="true" value="' + parseInt(this.dividendArray[dividendPointer + 1]) + '" data="' + parseInt(this.dividendArray[dividendPointer + 1]) + '">');
    //            else
    //                $("#row" + (rowCounter) + " #coloum" + (coloumCounter + 1)).html('<input type="text" class="inputBox valueTextBox validate" carry="true" value="' + parseInt(this.dividendArray[dividendPointer + 1].substr('1', this.dividendArray[dividendPointer + 1].length)) + '" data="' + parseInt(this.dividendArray[dividendPointer + 1].substr('1', this.dividendArray[dividendPointer + 1].length)) + '">');


    //            setFlow.push(("#row" + (rowCounter) + " #coloum" + (coloumCounter + 1) + " input"));
    //        }
    //        else {
    //            greater = parseInt(greater + '' + parseInt(0));
    //            $("#row" + (rowCounter) + " #coloum" + (coloumCounter + 1)).html('<input type="text" class="inputBox valueTextBox validate" carry="true" value="0" data="0">');
    //            setFlow.push(("#row3 #coloum" + (coloumCounter + 1) + " input"));
    //            setFlow.push(("#row" + (rowCounter) + " #coloum" + (coloumCounter + 1) + " input"));
    //        }
    //        dividendPointer++;
    //        smaller = this.divisor;
    //    }
    //    rowCounter++;
    //}
    //$("#row3 #coloum3").html(this.divisor);
    //$("#row3 #coloum4").css({ 'border-left': '2px solid', 'border-top': '2px solid' });
    //$("#row3 #coloum4").html('<input type="text" class="systemTextBox validate" disabled value="' + this.dividendArray[0] + '" data=' + this.dividendArray[0] + '>');
    //for (var i = 5; i < 5 + this.dividendArray.length - 1; i++) {
    //    $("#row3 #coloum" + (i)).css({ 'border-top': '2px solid' });
    //    $("#row3 #coloum" + (i)).html('<input type="text" class="systemTextBox validate" disabled value="' + this.dividendArray[i - 4] + '" data=' + this.dividendArray[i - 4] + '>');
    //}

    // Validation
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

                if (t.val() == t.attr('data'))
                    $(setFlow[++arrayCounter]).focus();
                else {
                    //Incorrect carry
                    tablePanel();
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
            checkQuotient($(this));
            e.preventDefault();
        }
        if (e.keyCode != 9 && e.keyCode != 8)// && e.keyCode!=37 && e.keyCode!=38 && e.keyCode!=39 && e.keyCode!=40)
        {
            if ($(this).val().length > 1 || ((e.keyCode < 48 || e.keyCode > 57))) {
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

}

division.prototype.proceedFlow = function () {

}

function checkQuotient(element) {
    quotientAttempt++;
    if (quotientCounter == 0 && $(element).attr('data') == 0) {
        if ($(element).attr('data') == $(element).val()) {
            $($("#row2 input")[quotientCounter]).attr('disabled', true);
            quotientCounter++;
            $("#sparkieMessage").html("You need not to write 0");
            $("#sparkiePromptBubble").show();
            $($("#row2 input")[quotientCounter]).attr('disabled', false);
            $($("#row2 input")[quotientCounter]).focus();
            //rowCount+=2;
            //$($("#row"+rowCount+" input")).attr('disabled',false);
            //$($("#row"+rowCount+" input")[0]).focus();

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
        if ($(element).attr('data') == $(element).val()) {
            $($("#row2 input")[quotientCounter]).attr('disabled', true);
            quotientCounter++;
            $($("#row" + rowCount + " input")).attr('disabled', true);
            rowCount++;
            $($("#row" + rowCount + " input")).attr('disabled', false);
            $($("#row" + rowCount + " input")[0]).focus();
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
    var tempArr = $($("#row" + (rowCount) + " input"));
    $($("#row" + (rowCount) + " input")).show();
    var parentArr = $($("#row" + (rowCount-1) + " input"));
    for (var i = 0; i < tempArr.length; i++) {
        if ($(tempArr[i]).val() == "") {
            $(tempArr[i]).focus();
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
    if(!$(tempArr[tempArr.length-2]).hasClass('resultTextBox'))
        arrayCounter += (tempArr.length);
    for (var i = 0; i < tempArr.length; i++) {
        if($(tempArr[i]).val() == $(tempArr[i]).attr('data') && $(tempArr[i]).attr('carry')=="true") {
            //arrayCounter--;
            moveToMainScreen();
            return;
        }
    }
    if (flag == 0) {

        //Correct
        $($("#row" + rowCount + " input")).attr('disabled', true);
        rowCount++;
        $($("#row" + rowCount + " input")).attr('disabled', false);
        setTimeout(function () {
            $(setFlow[++arrayCounter]).focus();
        }, 200);        
    }
    else {
        //wrongCheck++;
        //switch (wrongCheck) {
        //    case 1:
        //        $("#sparkiePromptBubble").show();
        //        $("#sparkiePromptBubble").html("This is incorrrect, check the difference again.");
        //        break;
        //        case 2:

        
    }
}

var arrayCounter = 0;
var wrongCheck = 0;
function checkResult() {
    var flag = 0;
    var tempArr = $($("#row" + rowCount + " input"));
    for (var i = 0; i < tempArr.length; i++) {
        if ($(tempArr[i]).val() == "" && $(tempArr[i]).attr('carry') != "true") {
            $(tempArr[i]).focus();
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
        if ($(tempArr[i]).val() == "" && $(tempArr[i]).attr('carry') == "true" && quotientCounter < textBx.answerArray.length) {
            if(textBx.answerArray[quotientCounter].indexOf('.') != -1)
            {
                moveToMainScreen();
                //return;    
            }
            
        }
    }
    if (flag == 0) {
        //Correct
        wrongCheck = 0;
        if (quotientCounter >= textBx.answerArray.length) {
            alert();
            attemptCounter = 0;
            //nextQuestion
            
        }
        else {
            if ($(tempArr[tempArr.length - 1]).attr('carry') == "true")
                arrayCounter++;
            setTimeout(function () {
                $(setFlow[arrayCounter]).attr('disabled', false);
                $(setFlow[arrayCounter]).focus();       
            }, 200);
        }
    }
    else {
        wrongCheck++;
        switch (wrongCheck) {
            case 1:
                $("#sparkiePromptBubble").show();
                $("#sparkiePromptBubble").html("This is incorrect check the difference again!");
                break;
            case 2:
                $("#screen" + currentScreen).hide();
                $("#screen10").show();
                $("#screen10").prepend("<span style='color:red'>Subtract " + tempCorrect + " </span>from " + actual);
                currentScreen = 10;
                break;
            case 3:
                wrongCheck = 0;
                $("#screen" + currentScreen).hide();
                $("#screen10").show();
                var t = tempCorrect - actual;
                $("#screen10").prepend(actual + " - " + tempCorrect + " = " + t);
                currentScreen = 10;
                break;
        }
    }
}

function tablePanel(_divisor, _enteredValue, _number, _element) {
	//creating table here
	$('#bottomPanel .hide').hide();
	$(_element).show();
	
	var tip = document.createElement('div');
	tip.innerHTML = "Let's multiply " + _divisor + " with " + _number + " and see if it works.";
	
	var table = document.createElement('table');
	table.className = 'smallCalcTable';
	for(var i = 0 ; i < 5; i++){
		var row = table.insertRow(-1);
		for(var j = 0; j  < 4; j++){
			var cell = row.insertCell(-1);
			cell.width = 25;
			cell.height = 25;
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

	$(table.rows[3].cells[1]).html(input1);
	$(table.rows[3].cells[2]).html(input2);
	
	var button = document.createElement('button');
	button.innerHTML ='check';
	
	var div = document.createElement('div');
	div.className = 'additionalDiv';
	
	$(_element).html('');
	$(_element).append(tip);
	$(_element).append(table);
	$(_element).append(button);
	$(_element).append(div);
	
	button.addEventListener('click', function(){
		var table = $('.smallCalcTable')[0];
		var input = $('.smallCalcTable input');
		var num1 = input[0].value; 
		var num2 = input[1].value; 
		
		var finalAnswer = num1 + num2;
		
		var num3 = parseInt(table.rows[1].cells[2].innerHTML);
		var num4 = parseInt(table.rows[2].cells[2].innerHTML);
		
		if(num3 * num4 == parseFloat(finalAnswer)){
			$('.additionalDiv').html('Yes, that is right.');			
		}
		else{
			$('.additionalDiv').html('Check your answer.');			
		}
	}, false);
};
function commonFlow(element) {
    if (commonFlowFlag == 0) {
        $("#sparkieMessage").html(promptArr['readCareFully']);
        $("#sparkiePromptBubble").show();
        if (parseInt($(element).attr('data')) > parseInt($(element).val())) {
            $("#mess").html(replaceDynamicText(promptArr['wrong_quotient_less'], gameObj.numberLanguage, 'gameObj'));
            $("#" + currentScreen).hide();
            $("#screen9").show();
            currentScreen = 9;
        }
        else if (parseInt($(element).attr('data')) < parseInt($(element).val())) {
            $("#mess").html(replaceDynamicText(promptArr['wrong_quotient_more'], gameObj.numberLanguage, 'gameObj'));
            $("#" + currentScreen).hide();
            $("#screen9").show();
            currentScreen = 9;
        }
        commonFlowFlag = 1;
    }
    else {
        tablePanel();
    }
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