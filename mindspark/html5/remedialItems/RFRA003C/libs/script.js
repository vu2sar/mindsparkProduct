var interactiveObj;
var completed = 0;
var levelWiseTimeTaken=0;
var typeWiseScore = "0|0|0|0";	// scores to be recorded separately for all 4 types of questions. Pipe seprated.  10 points for correct at 1st attempt and 5 at 2nd attempt
var levelsAttempted = "L1";
var levelWiseStatus = 0;
var extraParameters = 0;
var levelWiseScore = 0;

var correctAnswerCount = 0;
var html;
var html1;
var html2;
var number1 = 0;

var c = 0;
var a = 0;

var correctQues_1_flag = 0;
var correctQues_2_flag = 0;
var correctQues_3_flag = 0;
var correctQues_4_flag = 0;

var correctQues_2_flagVisible = 0;
var correctQues_3_flagVisible = 0;

var notInLowestFormFlag = 0;
var attempt3_C_NLF = 0;
var notInLowestFormAgain_Flag = 0;
var notInLowestFormFinal_Flag = 0;

var attemp_Ques3 = 0;
var timer;
var timer2;

// Variables for Question 2//

var correct_firstAttemptQ2 = 0;
var correct_secondAttemptQ2 = 0;
var correct_thirdAttemptQ2 = 0;

var Incorrect_firstAttemptQ2 = 0;
var Incorrect_secondAttemptQ2 = 0;

// Variables for Question 3//

// Variables for Question 3//

var correct_firstAttempt = 0;
var correct_secondAttempt = 0;
var correct_thirdAttempt = 0;
var correct_fourthAttempt = 0;

var Incorrect_firstAttempt = 0;
var Incorrect_secondAttempt = 0;
var Incorrect_thirdAttempt = 0;
var Incorrect_fourthAttempt = 0;

var Q3_correct_LF_A1 = 0;
var Q3_correct_LF_A2 = 0;
var Q3_correct_LF_A3 = 0;
var Q3_correct_LF_A4 = 0;

var Q3_Incorrect_A1 = 0;
var Q3_Incorrect_A2 = 0;
var Q3_Incorrect_A3 = 0;
var Q3_Incorrect_A4 = 0;

// Variables for Question 3//

// Variables for Question 4//
var Incorrect_firstAttemptQ4 = 0;
var Incorrect_secondAttemptQ4 = 0;
var Incorrect_thirdAttemptQ4 = 0;

var correct_firstAttemptQ4 = 0;
var correct_secondAttemptQ4 = 0;
var correct_thirdAttemptQ4 = 0;
var notInLowestFormFlagQ4 = 0;
var attemp_Ques4 = 0;

var Q4_correct_LF_A1 = 0;
var Q4_correct_LF_A2 = 0;
var Q4_correct_LF_A3 = 0;

var Q4_Incorrect_A1 = 0;
var Q4_Incorrect_A2 = 0;
var Q4_Incorrect_A3 = 0;


// Variables for Question 4//

// Variables for Question 1//
var Incorrect_firstAttemptQ1 = 0;
var Incorrect_secondAttemptQ1 = 0;
// Variables for Question 1//

//------varibales to get the stats-----//

var AdditionalQues = new Array();
var AdditionalQues2 = new Array();
var Ques = new Array();
var GenerateQ5 = 0;
var GenerateQ6 = 0;

var Question5_Visible = 0;
var attemp_Ques5 = 0;

var correct_firstAttemptQ5 = 0;
var correct_secondAttemptQ5 = 0;
var correct_thirdAttemptQ5 = 0;


var Q5_correct_LF_A1 = 0;
var Q5_correct_LF_A2 = 0;
var Q5_correct_LF_A3 = 0;

var Q5_Incorrect_A1 = 0;
var Q5_Incorrect_A2 = 0;
var Q5_Incorrect_A3 = 0;

var notInLowestFormQ5 = 0;
var called1 = 0;
var called2 = 0;
var called3 = 0;
var called4 = 0;
var called11 = 0;
var called21 = 0;
var called31 = 0;
var called41 = 0;
//-----------------------------------//

//-----in case two questions are wrong-----------//
var Question6_Visible = 0;
var Question7_Visible = 0;

// varibales for question 6//
var attemp_Ques6 = 0;
var notInLowestFormQ6 = 0;

var Q6_correct_LF_A1 = 0;
var Q6_correct_LF_A2 = 0;
var Q6_correct_LF_A3 = 0;

var Q6_Incorrect_A1 = 0;
var Q6_Incorrect_A2 = 0;
var Q6_Incorrect_A3 = 0;

var Incorrect_firstAttemptQ6 = 0;
var Incorrect_secondAttemptQ6 = 0;
var Incorrect_thirdAttemptQ6 = 0;

var correct_firstAttemptQ6 = 0;
var correct_secondAttemptQ6 = 0;
var correct_thirdAttemptQ6 = 0;

//ques 7/

var attemp_Ques7 = 0;
var notInLowestFormQ7 = 0;

var Q7_correct_LF_A1 = 0;
var Q7_correct_LF_A2 = 0;
var Q7_correct_LF_A3 = 0;

var Q7_Incorrect_A1 = 0;
var Q7_Incorrect_A2 = 0;
var Q7_Incorrect_A3 = 0;

var Incorrect_firstAttemptQ7 = 0;
var Incorrect_secondAttemptQ7 = 0;
var Incorrect_thirdAttemptQ7 = 0;

var correct_firstAttemptQ7 = 0;
var correct_secondAttemptQ7 = 0;
var correct_thirdAttemptQ7 = 0;
var tempController;
var totaltimetaken = 0;
//--------//
var typeWiseScore = 0;
var arrOfPrimeNo = ['0.1', '0.3', '0.7'];
var arrOfEvenNo = ['0.2', '0.4', '0.6', '0.8'];

function questionInteractive()
{
    this.number_1 = 0;
    this.answer = "";
    this.answer2 = "";
    this.answer3 = "";
    this.answer4 = "";
    this.question_Type = '';
    this.attemptQues_1 = 0;
    this.attemptQues_2 = 0;
    this.attemptQues_3 = 0;
    this.attemptQues_4 = 0;
    this.local = 0;

    this.correctAnswer = 0;
    this.correctAnswer2 = 0;
    this.correctAnswer3 = 0;
    this.correctAnswer4 = 0;

    this.x = 0;
    this.y = 0;
    this.magic = 0;
    this.i = 0;

    this.answer_3_num = 0;
    this.answer_3_denom = 0;
    this.divisibleBy = 0;

    this.number_Add_1 = 0;
    this.number_Add_2 = 0;
    this.number_Add_3 = 0;
    this.number_Add_4 = 0;

    this.fifth = 0;
    this.correctAnswer5 = 0;
    this.parameterNotSetFlag = 0;
    this.counter = 0;

    this.userResponse_Q1_A1 = 0;
    this.userResponse_Q1_A2 = 0;

    this.userResponse_Q2_A1 = 0;
    this.userResponse_Q2_A2 = 0;

    this.userResponse_Q3_A1 = 0;
    this.userResponse_Q3_A2 = 0;
    this.userResponse_Q3_A3 = 0;

    this.userResponse_Q4_A1 = 0;
    this.userResponse_Q4_A2 = 0;
    this.userResponse_Q4_A3 = 0;

    this.userResponse_Q5_A1 = 0;
    this.userResponse_Q5_A2 = 0;
    this.userResponse_Q5_A3 = 0;

    this.userResponse_Q6_A1 = 0;
    this.userResponse_Q6_A2 = 0;
    this.userResponse_Q6_A3 = 0;

    this.userResponse_Q7_A1 = 0;
    this.userResponse_Q7_A2 = 0;
    this.userResponse_Q7_A3 = 0;

    this.scoreType1 = 0;
    this.scoreType2 = 0;
    this.scoreType3 = 0;
    this.scoreType4 = 0;

    this.correct_Counter = 0;

    if (typeof getParameters['noOfLevels'] == "undefined" || parseInt(getParameters['noOfLevels']) != 1)
    {
        this.parameterNotSetFlag = 1;

        // //alert("Set noOfLevels to 3");
        $("#container").html("<h2><center>Parameter noOfLevels Not Set.</center></h2>");
        return;
    }
    else
        this.noOfLevels = getParameters['noOfLevels'];

    if (typeof getParameters['numberLanguage'] == "undefined")
    {
        this.numberLanguage = 'english';
    }
    else
        this.numberLanguage = getParameters['numberLanguage'];

    if (typeof getParameters['language'] == "undefined")
    {
        this.language = 'english';
    }
    else
        this.language = getParameters['language'];

}

questionInteractive.prototype.init = function()
{
    if (interactiveObj.parameterNotSetFlag == 0)
    {
        loadBody = setTimeout("interactiveObj.loadBody();", 1000);
    }
    else
    {
        $("#container").html("<h2><center>Parameter noOfLevels Missing OR incorrect. Set to 1</center></h2>");
    }

    $("input").live("keypress", function(e) {
        e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // Mozilla hack..

        if (e.keyCode == 13) {   // on Enter
            ////alert($(this).attr('id'));

            var value = $(this).val();
            if (value == "") {
                ////alert("Enter Some Value");
                $("#enterAnswer").css('visibility', 'visible');
                $("#enterAnswer").draggable();
                $(".buttonPrompt_wellDone").focus();
                $("#button_1").css('visibility', 'hidden');
                $("#button_2").css('visibility', 'hidden');
                $("#button_3").css('visibility', 'hidden');
                $("#button_4").css('visibility', 'hidden');
                $("#button_5").css('visibility', 'hidden');
                $("#button_6").css('visibility', 'hidden');
                $("#answer1").attr('disabled', 'true');
                $("#answer2").attr('disabled', 'true');
                $("#answer3").attr('disabled', 'true');
                $("#answer4").attr('disabled', 'true');
                $("#answer5").attr('disabled', 'true');
                $("#answer6").attr('disabled', 'true');
                $("#answer7").attr('disabled', 'true');
                $("Input_num3").attr('disabled', 'true');
                $("Input_num4").attr('disabled', 'true');
                $("Input_num6").attr('disabled', 'true');
                $("Input_num7").attr('disabled', 'true');
                $("Input_denom3").attr('disabled', 'true');
                $("Input_denom4").attr('disabled', 'true');
                $("Input_denom6").attr('disabled', 'true');
                $("Input_denom7").attr('disabled', 'true');


            }
            else {
                if ($(this).attr('id') == 'answer1') {
                    $(this).attr('readonly', true);
                    interactiveObj.checkAnswer("one");
                }
                else if ($(this).attr('id') == 'answer2') {
                    $(this).attr('readonly', true);
                    interactiveObj.checkAnswer("two");
                }
                else if ($(this).attr('id') == 'answer3') {
                    $(this).attr('readonly', true);
                    interactiveObj.checkAnswer("three");
                }
                //------------// for question no 3
                else if ($(this).attr('id') == 'Input_num3')
                {
                    if ($("#Input_num3").val() != '') 
                    {
				
                        $("#Input_denom3").focus();
                    }
                    else
                    {
                        $("#Input_num3").focus();
                    }
                }
                else if ($(this).attr('id') == 'Input_denom3') {
                    if ($("#Input_denom3").val() != '')
                    {	////alert("function called");
                        interactiveObj.checkAnswer("three");
                    }
                    else
                    {
                        $("#Input_denom3").focus();
                    }
                }
                //-------------//	
                else if ($(this).attr('id') == 'answer4') {
                    $(this).attr('readonly', true);
                    interactiveObj.checkAnswer("four");
                }
                //------------//for question no 4
                else if ($(this).attr('id') == 'Input_num4')
                {
                    if ($("#Input_num4").val() != '')
                    {
                        ////alert("set numerator");
                        $("#Input_denom4").focus();
                    }
                    else
                    {
                        $("#Input_num4").focus();
                    }
                }
                else if ($(this).attr('id') == 'Input_denom4') {
                    if ($("#Input_denom4").val() != '')
                    {	////alert("function called");
                        interactiveObj.checkAnswer("four");
                    }
                    else
                    {
                        $("#Input_denom4").focus();
                    }
                }
                //-------------//	


                //---------new question inserted from here--------//
                else if ($(this).attr('id') == 'answer6') {
                    $(this).attr('readonly', true);
                    interactiveObj.checkAnswer("six");
                }
         
                //--------for question 6 -------------//
                else if ($(this).attr('id') == 'Input_num6')
                {
                    if ($("#Input_num6").val() != '')
                    {
                        ////alert("set numerator");
                        $("#Input_denom6").focus();
                    }
                    else
                    {
                        $("#Input_num6").focus();
                    }
                }
                else if ($(this).attr('id') == 'Input_denom6') {
                    if ($("#Input_denom6").val() != '')
                    {	////alert("function called");
                        interactiveObj.checkAnswer("six");
                    }
                    else
                    {
                        $("#Input_denom6").focus();
                    }
                }
                //-----------------------------------//
                          //------------for question 7---------------//
                else if ($(this).attr('id') == 'Input_num7')
                {
                    if ($("#Input_num7").val() != '')
                    {
                        ////alert("set numerator");
                        $("#Input_denom7").focus();
                    }
                    else
                    {
                        $("#Input_num7").focus();
                    }
                }
                else if ($(this).attr('id') == 'Input_denom7') {
                    if ($("#Input_denom7").val() != '')
                    {	////alert("function called");
                        interactiveObj.checkAnswer("seven");
                    }
                    else
                    {
                        $("#Input_denom7").focus();
                    }
                }
                //----------------------------------------//
                else if ($(this).attr('id') == 'answer7') {
                    $(this).attr('readonly', true);
                    interactiveObj.checkAnswer("seven");
                }

            }
            return false;
        }

        else {
         if($(this).attr('id')=="Input_num3" && e.keyCode == 47 )
	   	{
			return false;
		} 
	 if($(this).attr('id')=="Input_denom3" && e.keyCode == 47 )
	   	{
			return false;
		} 
	 if($(this).attr('id')=="Input_num4" && e.keyCode == 47 )
	   	{
			return false;
		} 		
	 if($(this).attr('id')=="Input_denom4" && e.keyCode == 47 )
	   	{
			return false;
		} 
	 if($(this).attr('id')=="Input_num6" && e.keyCode == 47 )
	   	{
			return false;
		} 	
	 if($(this).attr('id')=="Input_denom6" && e.keyCode == 47 )
	   	{
			return false;
		} 	
	 if($(this).attr('id')=="Input_num7" && e.keyCode == 47 )
	   	{
			return false;
		} 	
	 if($(this).attr('id')=="Input_denom7" && e.keyCode == 47 )
	   	{
			return false;
		} 	
			
	   if (($(this).val().length == 6 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 47)
	   {
           	   	 return false;
            }
	   
        }
    });
}
questionInteractive.prototype.randomNumberGeneratorType_1 = function()
{

    interactiveObj.number1 = arrOfPrimeNo[Math.floor(Math.random() * arrOfPrimeNo.length)];
    var temp = $.inArray(interactiveObj.number1, arrOfPrimeNo);
    ////alert(temp);
    arrOfPrimeNo.splice(temp, 1);
}
questionInteractive.prototype.randomNumberGeneratorType_2 = function()
{
    interactiveObj.number2 = parseInt(Math.floor((Math.random() * 87) + 11));     // TYPE 1 Numbers

    if (interactiveObj.number2 % 2 == 0 || interactiveObj.number2 % 5 == 0 || interactiveObj.number2 % 3 == 0 || interactiveObj.number2 % 11 == 0)
    {
        interactiveObj.randomNumberGeneratorType_2();
    }
    else
    {
        interactiveObj.number2 = parseFloat("0." + interactiveObj.number2);

    }
}
questionInteractive.prototype.randomNumberGeneratorType_3 = function()
{

    interactiveObj.number3 = arrOfEvenNo[Math.floor(Math.random() * arrOfEvenNo.length)];
    var temp2 = $.inArray(interactiveObj.number3, arrOfEvenNo);
    ////alert(temp2);
    arrOfEvenNo.splice(temp2, 1);
}
questionInteractive.prototype.randomNumberGeneratorType_4 = function()
{
    interactiveObj.number4 = parseInt(Math.floor((Math.random() * 88) + 11));    // TYPE 1 Numbers
    ////console.log("num generated="+interactiveObj.number4)

    if (interactiveObj.number4 % 2 == 0 || interactiveObj.number4 % 5 == 0 || interactiveObj.number4 == 56 || interactiveObj.number4 == 58)
    {
        interactiveObj.number4 = ("0." + interactiveObj.number4);
    }
    else
    {
        interactiveObj.randomNumberGeneratorType_4();
    }
    // //console.log("number 4="+interactiveObj.number4);
}
questionInteractive.prototype.randomNumber_Add_1 = function()
{
    //console.log("Called Type 1");
    interactiveObj.number_1 = arrOfPrimeNo[Math.floor(Math.random() * arrOfPrimeNo.length)];
}
questionInteractive.prototype.randomNumber_Add_2 = function()
{
    ////console.log("Called Type 2");
    interactiveObj.number_2 = parseInt(Math.floor((Math.random() * 87) + 10));     // TYPE 1 Numbers

    if (interactiveObj.number_2 % 2 == 0 || interactiveObj.number_2 % 5 == 0 || interactiveObj.number_2 % 3 == 0 || interactiveObj.number_2 % 11 == 0 || interactiveObj.number_2 == interactiveObj.number2)
    {
        interactiveObj.randomNumber_Add_2();
    }
    else
    {

        interactiveObj.number_2 = parseFloat("0." + interactiveObj.number_2);
        //console.log("Number 2=" + interactiveObj.number_2);
    }
}
questionInteractive.prototype.randomNumber_Add_3 = function()
{

    interactiveObj.number_3 = arrOfEvenNo[Math.floor(Math.random() * arrOfEvenNo.length)];
}
questionInteractive.prototype.randomNumber_Add_4 = function()
{
    ////console.log("Called Type 4");
    interactiveObj.number_4 = parseInt(Math.floor((Math.random() * 88) + 10));    // TYPE 1 Numbers
    interactiveObj.number4 = parseFloat(interactiveObj.number4);
    if (interactiveObj.number_4 % 2 == 0 || interactiveObj.number_4 % 5 == 0)
    {
        if (interactiveObj.number_4 != interactiveObj.number4)
        {
            interactiveObj.number_4 = parseFloat(("0." + interactiveObj.number_4)).toFixed(2);
            //console.log("Number 4=" + interactiveObj.number_1);
        }
        else
        {
            interactiveObj.randomNumber_Add_4();
        }

    }
    else
    {
        interactiveObj.randomNumber_Add_4();
    }
}
questionInteractive.prototype.loadBody = function()
{

    html = "";
    interactiveObj.randomNumberGeneratorType_1();
    interactiveObj.randomNumberGeneratorType_2();
    interactiveObj.randomNumberGeneratorType_3();
    interactiveObj.randomNumberGeneratorType_4();

    html += '<div id="base">';
    html += '<div id="background"></div>';
    html += '<div id="header">' + replaceDynamicText(promptArr['text_1'], interactiveObj.numberLanguage, "interactiveObj") + '</div>';
    html += '</div>';

    interactiveObj.loadQuestions();   // calls the function that loads the question

    $("#container").html(html);

    tempController = window.setInterval(function() {
        levelWiseTimeTaken = totaltimetaken++;
    }, 1000)
}
questionInteractive.prototype.loadQuestions = function()
{

    html = "";

    html += '<div id="base">';
    html += '<div id="background"></div>';
    html += '<div id="header">' + promptArr['text_1'] + '</div>';

    html += '<div id="firstQuestion">';
    html += '<div id="firstQuestion_1">' + replaceDynamicText(interactiveObj.number1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div>';
    html += '<input id="answer1" autofocus="autofocus" pattern="[0-9]*"></input>';
    html += '<div id="fractionAnswer1" style="visibility:hidden;"></div>';
    //html+='<button id="button_1" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("one");>'+promptArr['txt_17']+'</button>';
    html += '</div>';

    html += '<div id="secondQuestion">';
    html += '<div id="secondQuestion_2">' + replaceDynamicText(interactiveObj.number2, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div>'
    html += '<input id="answer2" pattern="[0-9]*"></input>';
    html += '<div id="fractionAnswer2"></div>';
    //html+='<button id="button_2" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("two");>'+promptArr['txt_17']+'</button>';
    html += '</div>';

    html += '<div id="thirdQuestion">';
    html += '<div id="thirdQuestion_3">' + replaceDynamicText(interactiveObj.number3, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div>'
    html += '<input id="answer3" pattern="[0-9]*"></input>';
    html += '<div id="fractionAnswer3" style="visibility:hidden;"></div>';
    html += '<div id="FractionDivided"></div>';
    //html+='<button id="button_3" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("three");>'+promptArr['txt_17']+'</button>';
    html += '</div>';


    html += '<div id="fourthQuestion">';
    html += '<div id="fourthQuestion_4">' + replaceDynamicText(interactiveObj.number4, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div>'
    html += '<input id="answer4" pattern="[0-9]*"></input>';
    html += '<div id="fractionAnswer4"></div>';
    html += '<div id="FractionDivided4"></div>';
    //html+='<button id="button_4" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("four");>'+promptArr['txt_17']+'</button>';
    html += '</div>';



    html += '<div id="fifthQuestion"></div>';


    html += '<div id="sixthQuestion">';
    html += '</div>';

    html += '<div id="seventhQuestion">';
    html += '</div>';


    html += '<div id="prompts"></div>';
    html += '</div>';

    html += '<div id="enterAnswer" style="top: 250px;left: 298px;" class="correct"><div class="sparkie"></div><div id="blankEnter">' + promptArr['txt_21'] + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone" style="top: 86px;left: 158px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
    $("#container").html(html);



    //------------------setting the visibility of other questions----------------------//
    if (correctQues_1_flag == 1)
    {
        $("#fractionAnswer3").show();
        $("#showAns2").css('visibility', 'hidden');
        $("#message2").css('visibility', 'hidden');
        $("#wellDone2").css('visibility', 'hidden');

        $("#firstQuestion").css('visibility', 'visible');
        $("#button_1").css('visibility', 'hidden');
        $("#answer1").hide();
        $("#fractionAnswer1").css('visibility', 'visible');
        $('#fractionAnswer1').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.correctAnswer * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

        //$("#fractionAnswer1").css('border','1px solid green');
        $("#fractionAnswer1").css('border-radius', '11px');

        $("#fractionAnswer1").css('-webkit-box-shadow', '0 0 20px green');
        $("#fractionAnswer1").css('-moz-box-shadow', ' 0 0 20px green');
        $("#fractionAnswer1").css('box-shadow', '0 0 20px green');

        $("#answer1").removeAttr('autofocus');

        if (Incorrect_secondAttemptQ1 == 1)
        {

            // //alert("Inside Incorredt");
            $("#fractionAnswer1").css('visibility', 'visible');
            $('#fractionAnswer1').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.correctAnswer * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            //  $("#fractionAnswer1").css('border','1px solid blue');
            $("#fractionAnswer1").css('border-radius', '11px');

            $("#fractionAnswer1").css('-webkit-box-shadow', '0 0 20px blue');
            $("#fractionAnswer1").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#fractionAnswer1").css('box-shadow', '0 0 20px blue');
            $("#answer1").removeAttr('autofocus');
        }
        if (interactiveObj.attemptQues_1 == 2 && interactiveObj.local == interactiveObj.correctAnswer)
        {
            $("#fractionAnswer1").css('visibility', 'visible');
            $('#fractionAnswer1').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.correctAnswer * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            // $("#fractionAnswer1").css('border','1px solid green');
            $("#fractionAnswer1").css('border-radius', '11px');
            $("#fractionAnswer1").css('-webkit-box-shadow', '0 0 20px green');
            $("#fractionAnswer1").css('-moz-box-shadow', ' 0 0 20px green');
            $("#fractionAnswer1").css('box-shadow', '0 0 20px green');
            $("#answer1").removeAttr('autofocus');

        }

    }
    if (correctQues_2_flagVisible == 1)
    {

        $("#showAns2").css('visibility', 'hidden');
        $("#message2").css('visibility', 'hidden');
        $("#wellDone2").css('visibility', 'hidden');

        $("#firstQuestion").css('visibility', 'visible');
        $("#secondQuestion").css('visibility', 'visible');    // ------Setting question 1 visible and and disabling the properties-----------//

        $("#answer2").focus();
    }
    if (correctQues_2_flag == 1)
    {


        $("#button_2").css('visibility', 'hidden');

        $("#showAns2").css('visibility', 'hidden');
        $("#message2").css('visibility', 'hidden');
        $("#wellDone2").css('visibility', 'hidden');

        $("#firstQuestion").css('visibility', 'visible');
        $("#secondQuestion").css('visibility', 'visible');    // ------Setting question 1 visible and and disabling the properties-----------//


        $("#answer2").hide();

        $("#fractionAnswer2").css('visibility', 'visible');
        $('#fractionAnswer2').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

        if (correct_firstAttemptQ2 == 1)
        {
            $("#fractionAnswer2").css('visibility', 'visible');
            $('#fractionAnswer2').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            //$("#fractionAnswer2").css('border','1px solid green');
            $("#fractionAnswer2").css('border-radius', '11px');

            $("#fractionAnswer2").css('-webkit-box-shadow', '0 0 20px green');
            $("#fractionAnswer2").css('-moz-box-shadow', ' 0 0 20px green');
            $("#fractionAnswer2").css('box-shadow', '0 0 20px green');
        }
        if (correct_secondAttemptQ2 == 1)
        {
            $("#fractionAnswer2").css('visibility', 'visible');
            $('#fractionAnswer2').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            //$("#fractionAnswer2").css('border','1px solid green');
            $("#fractionAnswer2").css('border-radius', '11px');
            $("#fractionAnswer2").css('-webkit-box-shadow', '0 0 20px green');
            $("#fractionAnswer2").css('-moz-box-shadow', ' 0 0 20px green');
            $("#fractionAnswer2").css('box-shadow', '0 0 20px green');
        }

        if (Incorrect_secondAttemptQ2 == 1)
        {
            $("#fractionAnswer2").css('visibility', 'visible');
            $('#fractionAnswer2').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            //$("#fractionAnswer2").css('border','1px solid blue');
            $("#fractionAnswer2").css('border-radius', '11px');
            $("#fractionAnswer2").css('-webkit-box-shadow', '0 0 20px blue');
            $("#fractionAnswer2").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#fractionAnswer2").css('box-shadow', '0 0 20px blue');
        }

    }
    if (correctQues_3_flagVisible == 1)
    {
        ////alert("ques 3 flag visible true");
        $("#thirdQuestion").css('visibility', 'visible')
        $("#answer3").focus();
        $("#fractionAnswer3").show();

        if (notInLowestFormFlag == 1 && attemp_Ques3 == 1 && correct_firstAttempt == 1)
        {
            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><input id="Input_num3" pattern="[0-9]*"></input><input id="Input_denom3" pattern="[0-9]*"></input></div>');

            $("#Input_num3").focus();

            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });
        }
        if (notInLowestFormFlag == 1 && attemp_Ques3 == 2 && correct_secondAttempt == 1)
        {
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });

            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions" style="left:-12px;">=</div><div id="inputField"><input id="Input_num3" pattern="[0-9]*"></input><input id="Input_denom3" pattern="[0-9]*"></input></div>');
            $("#Input_num3").focus();
        }
        if (notInLowestFormFlag == 1 && attemp_Ques3 == 3 && correct_thirdAttempt == 1 && interactiveObj.counter == 1)// additional conditions
        {
            //alert("in previous");
            /*    $("#button_3").css({
             'position':'absolute',
             'left':'158px'
             });
             $("#button_3").css('visibility','hidden');
             
             $("#answer3").hide();
             $("#answer2").hide();
             
             $("#fractionAnswer3").css('visibility','visible');
             
             $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">'+replaceDynamicText(interactiveObj.y*10,interactiveObj.numberLanguage,"interactiveObj")+'</div><div class="frac">'+replaceDynamicText(10,interactiveObj.numberLanguage,"interactiveObj")+'</div></div>');
             
             $("#FractionDivided").css('visibility','visible');
             $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">'+replaceDynamicText(interactiveObj.y*10/interactiveObj.divisor,interactiveObj.numberLanguage,"interactiveObj")+'</div><div class="frac">'+replaceDynamicText(10/interactiveObj.divisor,interactiveObj.numberLanguage,"interactiveObj")+'</div></div></div>');
             
             $("#FractionDivided").css('border-radius','11px');  
             $("#FractionDivided").css('-webkit-box-shadow','0 0 20px green'); 
             $("#FractionDivided").css('-moz-box-shadow',' 0 0 20px green'); 
             $("#FractionDivided").css('box-shadow','0 0 20px green');*/

            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });

            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions"  style="left:-12px;">=</div><div id="inputField"><input id="Input_num3"></input><input id="Input_denom3"></input></div>');
            $("#Input_num3").focus();
        }
        if (notInLowestFormFlag == 1 && attemp_Ques3 == 3 && correct_thirdAttempt == 1 && interactiveObj.counter == 2)
        {
            //alert("inside magic condition");
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });
            $("#button_3").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();

            $("#fractionAnswer3").css('visibility', 'visible');

            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            $("#FractionDivided").css('border-radius', '11px');
            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px blue');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#FractionDivided").css('box-shadow', '0 0 20px blue');
        }
        if (notInLowestFormFlag == 1 && attemp_Ques3 == 4 && correct_fourthAttempt == 1)
        {
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });
            $("#button_3").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();

            $("#fractionAnswer3").css('visibility', 'visible');

            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            $("#FractionDivided").css('border-radius', '11px');
            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided").css('box-shadow', '0 0 20px green');
        }
        if (notInLowestFormFlag == 0 && attemp_Ques3 == 4 && correct_fourthAttempt == 1)
        {
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });
            $("#button_3").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();

            $("#fractionAnswer3").css('visibility', 'visible');

            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            $("#FractionDivided").css('border-radius', '11px');
            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided").css('box-shadow', '0 0 20px green');
        }
        if (notInLowestFormFlag == 0 && attemp_Ques3 == 4 && Incorrect_fourthAttempt == 1)
        {
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });
            $("#button_3").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();

            $("#fractionAnswer3").css('visibility', 'visible');

            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            $("#FractionDivided").css('border-radius', '11px');
            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px blue');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#FractionDivided").css('box-shadow', '0 0 20px blue');
        }
        if (notInLowestFormFlag == 1 && attemp_Ques3 == 4 && Incorrect_fourthAttempt == 1)
        {
            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });
            $("#button_3").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();

            $("#fractionAnswer3").css('visibility', 'visible');

            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            $("#FractionDivided").css('border-radius', '11px');
            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px blue');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#FractionDivided").css('box-shadow', '0 0 20px blue');
        }
        if (notInLowestFormFlag == 0 && attemp_Ques3 == 1 && correct_firstAttempt == 1)
        {
            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.y * 10) / 2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#button_3").css({
                'position': 'absolute',
                'left': '235px'
            });
            $("#button_3").css('visibility', 'hidden');

            //$("#fractionAnswer3").css('border','1px solid green');
            $("#fractionAnswer3").css('border-radius', '11px');
            $("#fractionAnswer3").css('left', '2px');

            $("#fractionAnswer3").css('-webkit-box-shadow', '0 0 20px green');
            $("#fractionAnswer3").css('-moz-box-shadow', ' 0 0 20px green');
            $("#fractionAnswer3").css('box-shadow', '0 0 20px green');
        }
        if (notInLowestFormFlag == 0 && attemp_Ques3 == 2 && correct_secondAttempt == 1)
        {
            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.y * 10), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions"  style="left:-12px;">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.y * 10) / 2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            $("#button_3").css('visibility', 'hidden');

            //$("#FractionDivided").css('border','1px solid blue');
            $("#FractionDivided").css('border-radius', '11px');

            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided").css('box-shadow', '0 0 20px green');
        }
        if (notInLowestFormFlag == 0 && attemp_Ques3 == 3 && correct_thirdAttempt == 1)
        {

            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.y * 10) / 2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');


            //$("#button_3").css({'position':'absolute','left':'235px'});
            //$("#button_3").css('visibility','hidden');
            $("#button_3").css('visibility', 'hidden');

            //$("#FractionDivided").css('border','1px solid blue');
            $("#FractionDivided").css('border-radius', '11px');
            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided").css('box-shadow', '0 0 20px green');
        }
        if (Incorrect_firstAttempt == 1 && attemp_Ques3 == 1)
        {
        }
        if (Incorrect_secondAttempt == 1 && attemp_Ques3 == 2)
        {

            $("#button_3").css({
                'position': 'absolute',
                'left': '158px'
            });

            $("#answer3").hide();
            $("#fractionAnswer3").css('visibility', 'visible');
            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions"  style="left:-12px;">=</div><div id="inputField"><input id="Input_num3" pattern="[0-9]*"></input><input id="Input_denom3" pattern="[0-9]*"></input></div>');
            $("#Input_num3").focus();
        }
        if (Incorrect_thirdAttempt == 1 && attemp_Ques3 == 3)
        {
            $("#button_3").css({
                'position': 'absolute',
                'left': '235px'
            });
            $("#button_3").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();

            $("#fractionAnswer3").css('visibility', 'visible');
            $("#fractionAnswer3").css('left', '-21px');

            $('#fractionAnswer3').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided").css('visibility', 'visible');
            $('#FractionDivided').append('<div class="equal_Questions">=</div><div id="inputField"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.y * 10) / interactiveObj.divisor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText((10 / interactiveObj.divisor), interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

            //$("#FractionDivided").css('border','1px solid red');
            $("#FractionDivided").css('border-radius', '11px');

            $("#FractionDivided").css('-webkit-box-shadow', '0 0 20px blue');
            $("#FractionDivided").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#FractionDivided").css('box-shadow', '0 0 20px blue');

            correctQues_4_flag = 1;
        }
    }
    if (correctQues_4_flag == 1)    //------------------ QUESTION 4 VISIBILITY SETTINGS ------------- //
    {
        $("#fourthQuestion").css('visibility', 'visible');

        $("#answer4").focus();

        if (notInLowestFormFlagQ4 == 1 && attemp_Ques4 == 1 && correct_firstAttemptQ4 == 1)
        {
            $("#FractionDivided4").css('visibility', 'visible');
            $('#FractionDivided4').append('<div class="equal_Questions">=</div><div id="inputField"><input id="Input_num4" pattern="[0-9]*"></input><input id="Input_denom4" pattern="[0-9]*"></input></div>');
            $("#Input_num4").focus();

            $("#answer4").hide();
            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseInt(interactiveObj.xander[1]), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#button_4").css({
                'position': 'absolute',
                'left': '165px'
            });
        }
        if (notInLowestFormFlagQ4 == 1 && attemp_Ques4 == 2 && correct_secondAttemptQ4 == 1)
        {
            // //alert("in this condition");

            $("#button_4").css({
                'position': 'absolute',
                'left': '165px'
            });
            $("#answer4").hide();
            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseInt(interactiveObj.xander[1]), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided4").css('visibility', 'visible');
            $('#FractionDivided4').append('<div class="equal_Questions">=</div><div id="inputField"><input id="Input_num4" pattern="[0-9]*"></input><input id="Input_denom4" pattern="[0-9]*"></input></div>');
            $("#Input_num4").focus();
        }
        if (notInLowestFormFlagQ4 == 1 && attemp_Ques4 == 3 && correct_thirdAttemptQ4 == 1)
        {
            $("#button_4").css({
                'position': 'absolute',
                'left': '165px'
            });
            $("#button_4").css('visibility', 'hidden');

            $("#answer3").hide();
            $("#answer2").hide();
            $("#answer4").hide();

            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseInt(interactiveObj.xander[1]), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            $("#FractionDivided4").css('visibility', 'visible');
            $("#FractionDivided4").css('top', '-1px');
            $('#FractionDivided4').append('<div class="equal_Questions" style="top: 11px;">=</div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseInt(interactiveObj.xander[1]) / interactiveObj.divisibleBy, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + 100 / interactiveObj.divisibleBy + '</div></div>');

            //	 $("#FractionDivided4").css('border','1px solid blue');
            $("#FractionDivided4").css('border-radius', '11px');

            $("#FractionDivided4").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided4").css('box-shadow', '0 0 20px green');
            $("#FractionDivided4").css('top', '3px');
        }
        // WHEN ANSWER IS  IN LOWEST FORM
        if (notInLowestFormFlagQ4 == 0 && attemp_Ques4 == 1 && correct_firstAttemptQ4 == 1)
        {
            $("#answer4").hide();
            $("#button_4").css('visibility', 'hidden');
            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.a, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(parseInt(interactiveObj.answer_4[1]), interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            //$("#fractionAnswer4").css('border','1px solid green');
            $("#fractionAnswer4").css('border-radius', '11px');
            $("#fractionAnswer4").css('left', '4px');

            $("#fractionAnswer4").css('-webkit-box-shadow', '0 0 20px green');
            $("#fractionAnswer4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#fractionAnswer4").css('box-shadow', '0 0 20px green');

        }
        if (notInLowestFormFlagQ4 == 0 && attemp_Ques4 == 2 && correct_secondAttemptQ4 == 1)
        {
            ////alert("xxx");
            $("#answer4").hide();
            $("#button_4").css('visibility', 'hidden');
            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseFloat(interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#FractionDivided4").css('visibility', 'visible');
            $('#FractionDivided4').append('<div class="equal_Questions" style="top:11px;">=</div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseInt(interactiveObj.xander[1]) / interactiveObj.divisibleBy, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + 100 / interactiveObj.divisibleBy + '</div></div>');

            //$("#FractionDivided4").css('border','1px solid blue');
            $("#FractionDivided4").css('border-radius', '11px');
            //$("#FractionDivided4").css('top','-1px');

            $("#FractionDivided4").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided4").css('box-shadow', '0 0 20px green');
            $("#FractionDivided4").css('top', '0px');


        }
        if (notInLowestFormFlagQ4 == 0 && attemp_Ques4 == 3 && correct_thirdAttemptQ4 == 1)
        {
            ////alert("xxx");
            $("#answer4").hide();
            $("#button_4").css('visibility', 'hidden');
            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseFloat(interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#FractionDivided4").css('visibility', 'visible');
            $("#FractionDivided4").css('top', '-1px');
            $('#FractionDivided4').append('<div class="equal_Questions" style="top: 11px;">=</div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseInt(interactiveObj.number4 * 100) / interactiveObj.divisibleBy, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.divisibleBy, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

            //$("#FractionDivided4").css('border','1px solid blue');
            $("#FractionDivided4").css('border-radius', '11px');
            $("#FractionDivided4").css('top', '-1px');

            $("#FractionDivided4").css('-webkit-box-shadow', '0 0 20px green');
            $("#FractionDivided4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#FractionDivided4").css('box-shadow', '0 0 20px green');

        }
        if (Incorrect_secondAttemptQ4 == 1 && attemp_Ques4 == 2)
        {
            $("#answer4").hide();
            $("#button_4").css({
                'position': 'absolute',
                'left': '165px'
            });
            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#FractionDivided4").css('visibility', 'visible');
            $('#FractionDivided4').append('<div class="equal_Questions">=</div><div id="inputField"><input id="Input_num4" pattern="[0-9]*"></input><input id="Input_denom4" pattern="[0-9]*"></input></div>');
            $("#Input_num4").focus();
        }
        if (Incorrect_thirdAttemptQ4 == 1 && attemp_Ques4 == 3)
        {
            $("#answer4").hide();
            $("#button_4").hide();

            $("#fractionAnswer4").css('visibility', 'visible');
            $('#fractionAnswer4').append('<div class="fraction"><div class="frac numerator">' + replaceDynamicText(parseFloat(interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');
            $("#FractionDivided4").css('visibility', 'visible');
            // $('#FractionDivided4').append('<div class="fraction"><div class="frac numerator">'+parseInt(interactiveObj.number4*100)/interactiveObj.divisibleBy+'</div><div class="frac">'+100/interactiveObj.divisibleBy+'</div></div>');
            $("#FractionDivided4").css('top', '-1px');
            $('#FractionDivided4').append('<div class="equal_Questions" style="top: 11px;">=</div><div class="fraction" style="top:2px;"><div class="frac numerator">' + replaceDynamicText(parseFloat(interactiveObj.number4 * 100).toFixed(0) / interactiveObj.divisibleBy, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.divisibleBy, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');


            //$("#FractionDivided4").css('border','1px solid red');
            $("#FractionDivided4").css('border-radius', '11px');

            $("#FractionDivided4").css('-webkit-box-shadow', '0 0 20px blue');
            $("#FractionDivided4").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#FractionDivided4").css('box-shadow', '0 0 20px blue');
        }
    }
    //  if(Question5_Visible==1)
    /*{
        $("#fifthQuestion").css('visibility', 'visible');

        if (c == 1)
        {
            if (called1 == 1 || called2 == 1)		// true when child makes error only in type 1 or type 2 question
            {
                if (called1 == 1)
                {
                    interactiveObj.visible = parseInt(interactiveObj.correctAnswer5 * 10);
                    interactiveObj.denominator = 10;
                }
                if (called2 == 1)
                {
                    interactiveObj.visible = parseInt(interactiveObj.correctAnswer5 * 100);
                    interactiveObj.denominator = 100;
                }

                $("#fifthQuestion").append('<div id="firstQuestion_1">' + interactiveObj.fifth + '</div><input id="answer5"></input><div id="fractionAnswer5"></div><button id="button_5" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("five");>' + promptArr['txt_17'] + '</button>');
                $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">10</div></div>');
            }

            if (called1 == 1 || called2 == 1)
            {
                //   //alert("Inside Counter 1 called")
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 1 && Q5_correct_LF_A1 == 1)
                {
                    //   //alert("C011");
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');

                    $("#fractionAnswer5").css('border', '1px solid green');
                    $("#fractionAnswer5").css('border-radius', '11px');



                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 2 && Q5_correct_LF_A2 == 1)
                {
                    //    //alert("C021");
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');

                    $("#fractionAnswer5").css('border', '1px solid blue');
                    $("#fractionAnswer5").css('border-radius', '11px');

                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 3 && Q5_correct_LF_A3 == 1)
                {
                    //    //alert("C031");
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');

                    $("#fractionAnswer5").css('border', '1px solid blue');
                    $("#fractionAnswer5").css('border-radius', '11px');

                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 1 && Q5_Incorrect_A1 == 1)
                {
                    //   //alert("011")
                    $("#answer5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'visible');
                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 2 && Q5_Incorrect_A2 == 1)
                {
                    //     //alert("020")
                    $("#answer5").css('visibility', 'hidden');
                    $("#button_5").css('visibility', 'hidden');

                    $("#fractionAnswer5").css('visibility', 'visible');

                    $("#fractionAnswer5").css('border', '1px solid red');
                    $("#fractionAnswer5").css('border-radius', '11px');

                    //Question6_Visible=1;
                }

            }
            if (called3 == 1 || called4 == 1)		// true when child makes error only in type 1 or type 2 question
            {
                if (called3 == 1)
                {
                    interactiveObj.visible = parseInt(interactiveObj.correctAnswer5 * 10);
                }
                if (called4 == 1)
                {
                    interactiveObj.visible = parseInt(interactiveObj.correctAnswer5 * 100);
                }

                $("#fifthQuestion").append('<div id="fifthQuestion_5">' + interactiveObj.fifth + '</div><input id="answer5"></input><div id="fractionAnswer5"></div><div id="FractionDivided5"></div><button id="button_5" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("five");>' + promptArr['txt_17'] + '</button>');

                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 1 && Q5_Incorrect_A1 == 1)
                {
                    $("#answer5").css('visibility', 'visible');
                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 2 && Q5_Incorrect_A2 == 1)
                {
                    //   //alert("incorrect visibility");
                    $("#button_5").css('left', '167px');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="inputField"><input id="Input_num5"></input><input id="Input_denom5"></input></div>');
                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 3 && Q5_Incorrect_A3 == 1)
                {
                    //     //alert("finally");
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="frac_Q5"><div class="fraction"><div class="frac numerator">' + interactiveObj.visible / interactiveObj.divisor + '</div><div class="frac">' + interactiveObj.divide / interactiveObj.divisor + '</div></div></div>');

                    $("#FractionDivided5").css('border', '1px solid red');
                    $("#FractionDivided5").css('border-radius', '11px');
                }

                if (notInLowestFormQ5 == 1 && attemp_Ques5 == 1 && correct_firstAttemptQ5 == 1)
                {
                    //   //alert("C111");
                    $("#button_5").css('left', '167px');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="inputField"><input id="Input_num5"></input><input id="Input_denom5"></input></div>');

                }
                if (notInLowestFormQ5 == 1 && attemp_Ques5 == 2 && correct_secondAttemptQ5 == 1)
                {
                    //    //alert("C121");
                    $("#button_5").css('left', '167px');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="inputField"><input id="Input_num5"></input><input id="Input_denom5"></input></div>');

                }
                if (notInLowestFormQ5 == 1 && attemp_Ques5 == 3 && correct_thirdAttemptQ5 == 1)
                {
                    //    //alert("C131");
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="frac_Q5"><div class="fraction"><div class="frac numerator">' + interactiveObj.visible / interactiveObj.divisor + '</div><div class="frac">' + interactiveObj.divide / interactiveObj.divisor + '</div></div></div>');

                    $("#FractionDivided5").css('border', '1px solid blue');
                    $("#FractionDivided5").css('border-radius', '11px');

                }

                //------------check----------//
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 1 && correct_firstAttemptQ5 == 1)
                {
                    //   //alert("C011");
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('border', '1px solid green');
                    $("#FractionDivided5").css('border-radius', '11px');

                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 2 && correct_secondAttemptQ5 == 1)
                {
                    //  //alert("C021");
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="frac_Q5"><div class="fraction"><div class="frac numerator">' + interactiveObj.visible / interactiveObj.divisor + '</div><div class="frac">' + interactiveObj.divide / interactiveObj.divisor + '</div></div></div>');

                    $("#FractionDivided5").css('border', '1px solid blue');
                    $("#FractionDivided5").css('border-radius', '11px');

                }
                if (notInLowestFormQ5 == 0 && attemp_Ques5 == 3 && correct_thirdAttemptQ5 == 1)
                {
                    //   //alert("C031");
                    $("#button_5").css('visibility', 'hidden');
                    $("#answer5").css('visibility', 'hidden');
                    $("#fractionAnswer5").css('visibility', 'visible');
                    $('#fractionAnswer5').append('<div class="fraction"><div class="frac numerator">' + interactiveObj.visible + '</div><div class="frac">' + interactiveObj.divide + '</div></div>');
                    $("#FractionDivided5").css('visibility', 'visible');
                    $('#FractionDivided5').append('<div id="frac_Q5"><div class="fraction"><div class="frac numerator">' + interactiveObj.visible / interactiveObj.divisor + '</div><div class="frac">' + interactiveObj.divide / interactiveObj.divisor + '</div></div></div>');

                    $("#FractionDivided5").css('border', '1px solid blue');
                    $("#FractionDivided5").css('border-radius', '11px');

                }

            }

        }

    }*/

    //--------------//Question 6 starts
    if (Question6_Visible == 1)
    {
        $("#sixthQuestion").css('visibility', 'visible');

        if (called1 == 1)
        {
            //$("#sixthQuestion").append('<div id="sixthQuestion_1">'+replaceDynamicText(interactiveObj.sixth,interactiveObj.numberLanguage,"interactiveObj")+'&nbsp;=</div><input id="answer6"></input><div id="fractionAnswer6"></div><button id="button_6" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("six");>'+promptArr['txt_17']+'</button>');  	 
            $("#sixthQuestion").append('<div id="sixthQuestion_1">' + replaceDynamicText(interactiveObj.sixth, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div><input id="answer6" pattern="[0-9]*"></input><div id="fractionAnswer6"></div>');

            $("#answer6").focus();

            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 1 && Q6_correct_LF_A1 == 1)
            {
                //	//alert("Ques6 011" );


                $("#button_6").css('visibility', 'hidden');
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                //$("#fractionAnswer6").css('border','1px solid green');
                $("#fractionAnswer6").css('border-radius', '11px');

                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 2 && Q6_correct_LF_A2 == 1)
            {
                //	//alert("Ques6 021" );
                $("#button_6").css('visibility', 'hidden');
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                //$("#fractionAnswer6").css('border','1px solid blue');
                $("#fractionAnswer6").css('border-radius', '11px');

                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px green');

            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 1 && Q6_Incorrect_A1 == 1)
            {
                //	//alert("Incorrect Ques6 011" );
                $("#button_6").css('visibility', 'visible');
                $("#answer6").css('visibility', 'visible');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 2 && Q6_Incorrect_A2 == 1)
            {
                //	//alert("Incorrect Ques6 021" );
                $("#button_6").css('visibility', 'hidden');
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                // $("#fractionAnswer6").css('border','1px solid red');
                $("#fractionAnswer6").css('border-radius', '11px');

                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px blue');
            }

        }
        else if (called2 == 1)
        {
            //$("#sixthQuestion").append('<div id="sixthQuestion_1">'+replaceDynamicText(interactiveObj.sixth,interactiveObj.numberLanguage,"interactiveObj")+'&nbsp;=</div><input id="answer6"></input><div id="fractionAnswer6"></div><button id="button_6" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("six");>'+promptArr['txt_17']+'</button>');  	 
            $("#sixthQuestion").append('<div id="sixthQuestion_1">' + replaceDynamicText(interactiveObj.sixth, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div><input id="answer6" pattern="[0-9]*"></input><div id="fractionAnswer6"></div>');

            $("#answer6").focus();
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 1 && Q6_correct_LF_A1 == 1)
            {
                //	//alert("called 2 Ques6 011" );
                $("#button_6").css('visibility', 'hidden');
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6" style="left: -5px;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                // $("#fractionAnswer6").css('border','1px solid green');
                $("#fractionAnswer6").css('border-radius', '11px');

                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 2 && Q6_correct_LF_A2 == 1)
            {
                //	//alert("called 2 Ques6 021" );
                $("#button_6").css('visibility', 'hidden');
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                //	 $("#fractionAnswer6").css('border','1px solid blue');
                $("#fractionAnswer6").css('border-radius', '11px');

                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 1 && Q6_Incorrect_A1 == 1)
            {
                //	//alert("called 2 Incorrect Ques6 011" );
                $("#button_6").css('visibility', 'visible');
                $("#answer6").css('visibility', 'visible');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 2 && Q6_Incorrect_A2 == 1)
            {
                //	//alert("called 2 Incorrect Ques6 021" );
                $("#button_6").css('visibility', 'hidden');
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"style="left: -5px;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                // $("#fractionAnswer6").css('border','1px solid red');
                $("#fractionAnswer6").css('border-radius', '11px');

                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px blue');
            }

        }
        else if (called3 == 1)
        {
            //$("#sixthQuestion").append('<div id="sixthQuestion_6">'+replaceDynamicText(interactiveObj.sixth,interactiveObj.numberLanguage,"interactiveObj")+'&nbsp;=</div><input id="answer6"></input><div id="fractionAnswer6"></div><div id="FractionDivided6"></div><button id="button_6" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("six");>'+promptArr['txt_17']+'</button>');  		
            $("#sixthQuestion").append('<div id="sixthQuestion_6">' + replaceDynamicText(interactiveObj.sixth, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div><input id="answer6" pattern="[0-9]*"></input><div id="fractionAnswer6"></div><div id="FractionDivided6"></div>');

            $("#answer6").focus();
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 1 && correct_firstAttemptQ6 == 1)
            {
                //	//alert("correct 011")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_6").css('visibility', 'hidden');


                $("#fractionAnswer6").css('border-radius', '11px');


                $("#fractionAnswer6").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer6").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 2 && correct_secondAttemptQ6 == 1)
            {
                //	//alert("correct 021")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").css('left', '-3px');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                //$("button_6").css('visibility','hidden');
                $("#button_6").css('visibility', 'hidden');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions" style="top:11px;left: -24px;">=</div><div id="fraction_final"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

                // $("#fractionAnswer6").css('border','1px solid blue');
                $("#FractionDivided6").css('border-radius', '11px');

                $("#FractionDivided6").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided6").css('box-shadow', '0 0 20px green');
                $("#FractionDivided6").css('top', '2px');
                $("#FractionDivided6").css('left', '92px');
            }
            if (notInLowestFormQ6 == 0 && attemp_Ques6 == 3 && Q6_correct_LF_A3 == 1)
            {
                //	//alert("correct 031")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_6").css('visibility', 'hidden');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions" style="top: 11px;">=</div><div id="fraction_final"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                // $("#fractionAnswer6").css('border','1px solid blue');
                $("#FractionDivided6").css('border-radius', '11px');

                $("#FractionDivided6").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided6").css('box-shadow', '0 0 20px green');
                $("#FractionDivided6").css('top', '2px');
                $("#FractionDivided6").css('left', '92px');
            }
            if (notInLowestFormQ6 == 1 && attemp_Ques6 == 1 && correct_firstAttemptQ6 == 1)
            {
                //	//alert("not in lowest form 111")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer6").css('left', '-15px');
                $("#button_6").css('left', '152px');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions">=</div><div id="inputField6"><input id="Input_num6" pattern="[0-9]*"></input><input id="Input_denom6" pattern="[0-9]*"></input></div>');

                $("#Input_num6").focus();
            }
            if (notInLowestFormQ6 == 1 && attemp_Ques6 == 2 && correct_secondAttemptQ6 == 1)
            {
                //	//alert("not in lowest form 121")

                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_6").css('left', '177px');
                $("#fractionAnswer6").css('left', '-15px');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions">=</div><div id="inputField6" ><input id="Input_num6" pattern="[0-9]*"></input><input id="Input_denom6" pattern="[0-9]*"></input></div>');
                $("#Input_num6").focus();
            }
            if (notInLowestFormQ6 == 1 && attemp_Ques6 == 3 && correct_thirdAttemptQ6 == 1)
            {
                //	//alert("not in lowest form 131")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('left', '-15px');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions"  style="top: 11px;">=</div><div id="fraction_final"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

                // $("#FractionDivided6").css('border','1px solid green');
                $("#FractionDivided6").css('border-radius', '11px');
                $("#FractionDivided6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#FractionDivided6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#FractionDivided6").css('box-shadow', '0 0 20px blue');
                $("#FractionDivided6").css('top', '2px');
                $("#FractionDivided6").css('left', '92px');
            }
            if (attemp_Ques6 == 1 && Incorrect_firstAttemptQ6 == 1)
            {
                //	//alert("Incorrect 11")
                $("#answer6").css('visibility', 'visible');
            }
            if (attemp_Ques6 == 2 && Incorrect_secondAttemptQ6 == 1)
            {
                //	//alert("Incorrect 21")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer6").css('left', '-15px');
                $("#button_6").css('left', '135px');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions">=</div><div id="inputField6"><input id="Input_num6" pattern="[0-9]*"></input><input id="Input_denom6" pattern="[0-9]*"></input></div>');

                $("#Input_num6").focus();
            }
            if (attemp_Ques6 == 3 && Incorrect_thirdAttemptQ6 == 1)
            {
                //	//alert("Incorrect 31")
                $("#answer6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('visibility', 'visible');
                $("#fractionAnswer6").append('<div id="fraction6"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_6").css('visibility', 'hidden');
                $("#fractionAnswer6").css('left', '-15px');
                $("#FractionDivided6").css('visibility', 'visible');
                $("#FractionDivided6").append('<div class="equal_Questions"style="top: 11px;">=</div><div id="fraction_final"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6 / interactiveObj.hcf, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + 10 / interactiveObj.hcf + '</div></div>');

                // $("#FractionDivided6").css('border','1px solid red');
                $("#FractionDivided6").css('border-radius', '11px');
                $("#FractionDivided6").css('top', '2px');
                $("#FractionDivided6").css('left', '64px');

                $("#FractionDivided6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#FractionDivided6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#FractionDivided6").css('box-shadow', '0 0 20px blue');
            }

        }
        /*else if(called4==1)
         {
         $("#sixthQuestion").append('<div id="sixthQuestion_6">'+interactiveObj.sixth+'</div><input id="answer6"></input><div id="fractionAnswer6"></div><div id="FractionDivided6"></div><button id="button_6" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("six");>'+promptArr['txt_17']+'</button>');
         }
         */

    }
    //------------//Question 6 Ends

    if (Question7_Visible == 1)
    {//question 7 starts
        ////alert("In ques 7")
        $("#seventhQuestion").css('visibility', 'visible');

        //case not possible//
        if (called21 == 1)
        {
            //$("#seventhQuestion").append('<div id="seventhQuestion_7">'+replaceDynamicText(interactiveObj.seventh,interactiveObj.numberLanguage,"interactiveObj")+'&nbsp;=</div><input id="answer7"></input><div id="fractionAnswer7"></div><button id="button_7" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("seven");>'+promptArr['txt_17']+'</button>');  	
            $("#seventhQuestion").append('<div id="seventhQuestion_7">' + replaceDynamicText(interactiveObj.seventh, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div><input id="answer7" pattern="[0-9]*"></input><div id="fractionAnswer7"></div>');

            $("#answer7").focus();
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 1 && Q7_correct_LF_A1 == 1)
            {
                //alert("called 21 Ques6 011");
                $("#button_7").css('visibility', 'hidden');
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '70px');
                // $("#fractionAnswer7").css('border','1px solid green');
                $("#fractionAnswer7").css('border-radius', '11px');
                $("#fractionAnswer7").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer7").css('box-shadow', '0 0 20px green');

            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 2 && Q7_correct_LF_A2 == 1)
            {
                //alert("called 21 Ques6 021");
                $("#button_7").css('visibility', 'hidden');
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '61px');
                $("#fractionAnswer7").css('border', '1px solid blue');
                $("#fractionAnswer7").css('border-radius', '11px');

                $("#fractionAnswer7").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer7").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 1 && Q7_Incorrect_A1 == 1)
            {
                //alert("called 21 Incorrect Ques7 011");
                $("#button_7").css('visibility', 'visible');
                $("#answer7").css('visibility', 'visible');
            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 2 && Q7_Incorrect_A2 == 1)
            {
                //alert("called 21Incorrect Ques7 021");
                $("#button_7").css('visibility', 'hidden');
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '77px');
                //$("#fractionAnswer7").css('border','1px solid red');
                $("#fractionAnswer7").css('border-radius', '11px');

                $("#fractionAnswer7").css('-webkit-box-shadow', '0 0 20px blue');
                $("#fractionAnswer7").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#fractionAnswer7").css('box-shadow', '0 0 20px blue');
            }
        }
        else if (called31 == 1)
        {//----if for called 3 starts
            //$("#seventhQuestion").append('<div id="seventhQuestion_7">'+replaceDynamicText(interactiveObj.seventh,interactiveObj.numberLanguage,"interactiveObj")+'&nbsp;=</div><input id="answer7"></input><div id="fractionAnswer7"></div><div id="FractionDivided7"></div><button id="button_7" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("seven");>'+promptArr['txt_17']+'</button>');
            $("#seventhQuestion").append('<div id="seventhQuestion_7">' + replaceDynamicText(interactiveObj.seventh, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div><input id="answer7"></input><div id="fractionAnswer7"></div><div id="FractionDivided7">');

            $("#answer7").focus();

            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 1 && correct_firstAttemptQ7 == 1)
            {
                //alert("correct 011")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_7").css('visibility', 'hidden');

                //$("#fractionAnswer7").css('border','1px solid green');
                $("#fractionAnswer7").css('border-radius', '11px');
                $("#fractionAnswer7").css('left', '75px');
                $("#fractionAnswer7").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer7").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 2 && correct_secondAttemptQ7 == 1)
            {
                //alert("correct 021")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                //$("button_6").css('visibility','hidden');
                $("#button_7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top: 11px;left: -24px;">&nbsp;=&nbsp;</div><div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                //$("#FractionDivided7").css('border','1px solid blue');
                $("#FractionDivided7").css('border-radius', '11px');
                $("#FractionDivided7").css('left', '115px');

                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided7").css('box-shadow', '0 0 20px green');

            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 3 && Q7_correct_LF_A3 == 1)
            {
                //alert("correct 031")
                $("#answer7").css('visibility', 'hidden');
                $("#button_7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("answer7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").css('top', '-16px');
                $("#FractionDivided7").css('left', '112px');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top:2px;">&nbsp;=&nbsp;</div><div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                //	$("#FractionDivided7").css('border','1px solid blue');
                $("#FractionDivided7").css('border-radius', '11px');

                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided7").css('box-shadow', '0 0 20px green');

            }

            if (notInLowestFormQ7 == 1 && attemp_Ques7 == 1 && correct_firstAttemptQ7 == 1)
            {
                //alert("not in lowest form 111")
                $("#button_7").css('left', '177px');
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#answer7").css('left', '177px');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top: 12px;left: 10px;">&nbsp;=&nbsp;</div><div id="inputField7"><input id="Input_num7" pattern="[0-9]*"></input><input id="Input_denom7" pattern="[0-9]*"></input></div>');

                $("#Input_num7").focus();

            }
            if (notInLowestFormQ7 == 1 && attemp_Ques7 == 2 && correct_secondAttemptQ7 == 1)
            {
                //alert("not in lowest form 121")

                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_7").css('left', '177px');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions"style="top: 12px;left: 10px;">&nbsp;=&nbsp;</div><div id="inputField7"><input id="Input_num7" pattern="[0-9]*"></input><input id="Input_denom7" pattern="[0-9]*"></input></div>');

                $("#Input_num7").focus();


            }
            if (notInLowestFormQ7 == 1 && attemp_Ques7 == 3 && correct_thirdAttemptQ7 == 1)
            {
                //alert("not in lowest form 131")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions"style="top: 12px;left: -22px;">&nbsp;=&nbsp;</div><div id="fraction_final"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

                $("#FractionDivided7").css('left', '111px');
                //$("#FractionDivided7").css('border','1px solid blue');
                $("#FractionDivided7").css('border-radius', '11px');

                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px blue');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#FractionDivided7").css('box-shadow', '0 0 20px blue');

            }
            if (attemp_Ques7 == 1 && Incorrect_firstAttemptQ7 == 1)
            {
                //alert("Incorrect 11")
                $("#answer7").css('visibility', 'visible');
                $("#button_7").css('visibility', 'visible');
            }
            if (attemp_Ques7 == 2 && Incorrect_secondAttemptQ7 == 1)
            {
                //alert("Incorrect 21")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_7").css('visibility', 'visible');
                $("#button_7").css('left', '189px');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions"style="top: 12px;left: 10px;">&nbsp;=&nbsp;</div><div id="inputField7"><input id="Input_num7" pattern="[0-9]*"></input><input id="Input_denom7" pattern="[0-9]*"></input></div>');

                $("#Input_num7").focus();
            }
            if (attemp_Ques7 == 3 && Incorrect_thirdAttemptQ7 == 1)
            {
                //alert("Incorrect 31")
                $("#answer7").css('visibility', 'hidden');
                $("#button_7").css('visibility', 'hidden');

                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#answer7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top:11px;left: -34px;">&nbsp;=&nbsp;<div id="fraction_final7" style="top:-6px;left: 29px;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

                //$("#FractionDivided7").css('border','1px solid red');
                $("#FractionDivided7").css('border-radius', '11px');
                $("#FractionDivided7").css('top', '-16px');
                $("#FractionDivided7").css('left', '134px');
                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px blue');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#FractionDivided7").css('box-shadow', '0 0 20px blue');
            }
        }// if for called 3 closes
        else if (called41 == 1)
        {//----if for called 3 starts
            //	//alert("seventh question wud be of type 4")
            //$("#seventhQuestion").append('<div id="seventhQuestion_7">'+replaceDynamicText(interactiveObj.seventh,interactiveObj.numberLanguage,"interactiveObj")+'&nbsp;=</div><input id="answer7"></input><div id="fractionAnswer7"></div><div id="FractionDivided7"></div><button id="button_7" type="button" name="" value="" class="css3button" onclick=interactiveObj.checkAnswer("seven");>'+promptArr['txt_17']+'</button>');
            $("#seventhQuestion").append('<div id="seventhQuestion_7">' + replaceDynamicText(interactiveObj.seventh, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=</div><input id="answer7" pattern="[0-9]*"></input><div id="fractionAnswer7"></div><div id="FractionDivided7"></div>');

            $("#answer7").focus();

            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 1 && correct_firstAttemptQ7 == 1)
            {
                //alert("correct 011")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#button_7").css('visibility', 'hidden');

                //$("#fractionAnswer7").css('border','1px solid green');
                $("#fractionAnswer7").css('border-radius', '11px');
                $("#fractionAnswer7").css('left', '68px');


                $("#fractionAnswer7").css('-webkit-box-shadow', '0 0 20px green');
                $("#fractionAnswer7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#fractionAnswer7").css('box-shadow', '0 0 20px green');
            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 2 && correct_secondAttemptQ7 == 1)
            {
                //alert("correct 021")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                //$("button_6").css('visibility','hidden');
                $("#button_7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#inputField7").css('top', '-12px');
                $("#inputField7").css('left', '81px');
                $("#FractionDivided7").append('<div class="equal_Questions"style="top: 12px;left: -18px;">=</div><div id="inputField7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#inputField7").css('top', '6px');
                $("#inputField7").css('left', '-8px');

                //$("#FractionDivided7").css('border','1px solid blue');
                $("#FractionDivided7").css('border-radius', '11px');
                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided7").css('box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('top', '-19px');
                $("#FractionDivided7").css('left', '130px');

            }
            if (notInLowestFormQ7 == 0 && attemp_Ques7 == 3 && correct_thirdAttemptQ7 == 1)
            {
                //alert("correct 031")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#button_7").css('visibility', 'hidden');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '40px');
                $("answer7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions"style="top: 12px;left: -18px;">=</div><div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');

                //$("#FractionDivided7").css('border','1px solid blue');
                $("#FractionDivided7").css('border-radius', '11px');

                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided7").css('box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('top', '-18px');
                $("#FractionDivided7").css('left', '141px');

            }

            if (notInLowestFormQ7 == 1 && attemp_Ques7 == 1 && correct_firstAttemptQ7 == 1)
            {
                //alert("not in lowest form 111")
                $("#button_7").css('visibility', 'visible');
                $("#button_7").css('left', '195px');
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '40px');
                //$("#answer7").css('left','177px');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top: 12px;left: 27px;">=</div><div id="inputField7"><input id="Input_num7" pattern="[0-9]*"></input><input id="Input_denom7" pattern="[0-9]*"></input></div>');

                $("#Input_num7").focus();

            }
            if (notInLowestFormQ7 == 1 && attemp_Ques7 == 2 && correct_secondAttemptQ7 == 1)
            {
                //alert("not in lowest form 121")

                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '40px');
                $("#button_7").css('left', '195px');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top: 12px;left: 27px;">=</div><div id="inputField7"><input id="Input_num7" pattern="[0-9]*"></input><input id="Input_denom7" pattern="[0-9]*"></input></div>');
                $("inputField7").css('top', '25px');

                $("#Input_num7").focus();

            }
            if (notInLowestFormQ7 == 1 && attemp_Ques7 == 3 && correct_thirdAttemptQ7 == 1)
            {
                //alert("not in lowest form 131")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '40px');
                $("#button_7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top: 16px;left: -18px;">=</div><div id="fraction_final"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');

                //$("#FractionDivided7").css('border','1px solid red');
                $("#FractionDivided7").css('border-radius', '11px');
                //$("#FractionDivided7").css('left','132px'); 
                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px green');
                $("#FractionDivided7").css('box-shadow', '0 0 20px green');
                $("#FractionDivided7").css('top', '-19px');
                $("#FractionDivided7").css('left', '130px');

            }
            if (attemp_Ques7 == 1 && Incorrect_firstAttemptQ7 == 1)
            {
                //alert("Incorrect 11")
                $("#answer7").css('visibility', 'visible');
                $("#button_7").css('visibility', 'visible');
            }
            if (attemp_Ques7 == 2 && Incorrect_secondAttemptQ7 == 1)
            {
                //alert("Incorrect 21")
                $("#answer7").css('visibility', 'hidden');
                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '40px');
                $("#button_7").css('visibility', 'visible');
                $("#button_7").css('left', '195px');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions"style="top: 12px;left: 27px;">=</div><div id="inputField7"><input id="Input_num7" pattern="[0-9]*"></input><input id="Input_denom7" pattern="[0-9]*"></input></div>');

                $("#Input_num7").focus();

            }
            if (attemp_Ques7 == 3 && Incorrect_thirdAttemptQ7 == 1)
            {
                //alert("Incorrect 31")
                $("#answer7").css('visibility', 'hidden');
                $("#button_7").css('visibility', 'hidden');

                $("#fractionAnswer7").css('visibility', 'visible');
                $("#fractionAnswer7").append('<div id="fraction7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>');
                $("#fractionAnswer7").css('left', '40px');
                $("#answer7").css('visibility', 'hidden');
                $("#FractionDivided7").css('visibility', 'visible');
                $("#FractionDivided7").append('<div class="equal_Questions" style="top: 16px;left: -18px;">=</div><div id="fraction_final7"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100 / interactiveObj.highestCommonFactor, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>');


                //$("#FractionDivided7").css('border','1px solid red');
                $("#FractionDivided7").css('border-radius', '11px');
                $("#fraction_final7").css('top', '4px');
                $("#fraction_final7").css('left', '-8px');

                $("#FractionDivided7").css('-webkit-box-shadow', '0 0 20px blue');
                $("#FractionDivided7").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#FractionDivided7").css('box-shadow', '0 0 20px blue');
                $("#FractionDivided7").css('top', '-19px');
                $("#FractionDivided7").css('left', '130px');

            }
        }// if for called 3 closes
    }//question 7 ends	*/
}
questionInteractive.prototype.checkAnswer = function(id)
{
    /*if($("#answer1").val()=='')  // button click validation not working now. Will enable it some time..!!! Dt:- 20th March,2013
     {
     $("#enterAnswer").css('visibility','visible');
     $("#enterAnswer").draggable();	
     return false;	
     }
     else
     {*/

    interactiveObj.question_Type = id;

    //alert("In check answer");



    if (interactiveObj.question_Type == "one")
    {
        html2 = "";
        interactiveObj.attemptQues_1 += 1;
        interactiveObj.answer_1 = $("#answer1").val().split("/");


        if (interactiveObj.attemptQues_1 == 1)
        {

            interactiveObj.userResponse_Q1_A1 = interactiveObj.answer_1;

        }
        if (interactiveObj.attemptQues_1 == 2)
        {

            interactiveObj.userResponse_Q1_A2 = interactiveObj.answer_1;

        }

        interactiveObj.correctAnswer = interactiveObj.number1;

        interactiveObj.x = interactiveObj.number1;

        html2 += '<div id="message" class="correct"><div class="sparkie"></div><div id="textCorrect1">' + replaceDynamicText(interactiveObj.x, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.x * 10, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="showAns" class="correct"><div class="sparkie"></div><div id="textCorrect2" style="left: 19px;">' + replaceDynamicText(interactiveObj.x, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp; ' + replaceDynamicText(interactiveObj.x * 10, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;<div class="fra2"><div class="upper">' + replaceDynamicText(parseFloat(interactiveObj.x * 10).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left: 84px;">10</div></div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt"  style="left:145px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        interactiveObj.answer = parseFloat(parseInt(interactiveObj.answer_1[0]) / parseInt(interactiveObj.answer_1[1]));

        interactiveObj.local = interactiveObj.answer;

        ////console.log("value of correct answer="+interactiveObj.correctAnswer);
        ////console.log("value of interactiveObj.x="+interactiveObj.x);

        $("#prompts").html(html2);

        $("#wellDone").draggable({containment: "#container"});
        $("#showAns").draggable({containment: "#container"});
        $("#message").draggable({containment: "#container"});

    }
    if (interactiveObj.question_Type == "two")
    {
        html2 = "";

        interactiveObj.answer_2 = $("#answer2").val().split("/");
        interactiveObj.attemptQues_2 += 1;

        if (interactiveObj.attemptQues_2 == 1)//user response
        {
            interactiveObj.userResponse_Q2_A1 = interactiveObj.answer_2;
            //extraParameters+="(Q2:-"+interactiveObj.number2+" | "+interactiveObj.userResponse_Q2_A1+")";
        }
        if (interactiveObj.attemptQues_2 == 2)//user response
        {
            interactiveObj.userResponse_Q2_A2 = interactiveObj.answer_2;
            //extraParameters+="|"+interactiveObj.userResponse_Q2_A2+")";
        }

        interactiveObj.correctAnswer2 = interactiveObj.number2;			// getting the actual correct answer
        interactiveObj.x = interactiveObj.correctAnswer2;					// saving the actual answer in a local variable for prompt messages

        html2 += '<div id="message2" class="correct"><div class="sparkie"></div><div id="textCorrect1"style="left: 36px;width: 211px;">' + replaceDynamicText(interactiveObj.x, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(parseFloat(interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="showAns2" class="correct"><div class="sparkie"></div><div id="textCorrect2" style="left: 30px;">' + replaceDynamicText(interactiveObj.x, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;' + replaceDynamicText((interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;=&nbsp;&nbsp;<div class="fra2" style="left: 128px;"><div class="upper">' + replaceDynamicText(parseFloat(interactiveObj.x * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left: 79px;">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt"  style="left:145px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone2" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        $("#prompts").html(html2);

        $("#wellDone2").draggable({containment: "#container"});
        $("#showAns2").draggable({containment: "#container"});
        $("#message2").draggable({containment: "#container"});

        interactiveObj.answer2 = parseFloat(parseInt(interactiveObj.answer_2[0]) / parseInt(interactiveObj.answer_2[1]));   // calculating the answer from user
        interactiveObj.local = interactiveObj.answer2;

    }
    if (interactiveObj.question_Type == "three")
    {
        html2 = "";
        attemp_Ques3 += 1;

        // GETTING THE VALUES IN EACH ATTEMPT//

        if (attemp_Ques3 == 1)     // IF THE USERS ANSWERS IS NOT IN LOWEST FORM
        {
            //     //alert("in 1st attempt");

            notInLowestFormFlag = 0;

            interactiveObj.answer_3 = $("#answer3").val().split("/");

            interactiveObj.userResponse_Q3_A1 = interactiveObj.answer_3;
            //extraParameters+="(Q3:-"+interactiveObj.number3+" | "+interactiveObj.userResponse_Q3_A1;

            interactiveObj.correctAnswer3 = interactiveObj.number3;
            interactiveObj.answer3 = parseFloat(parseInt(interactiveObj.answer_3[0]) / parseInt(interactiveObj.answer_3[1]));  //getting the answer from user
            interactiveObj.local = interactiveObj.answer3;
            interactiveObj.y = interactiveObj.number3;

            interactiveObj.hcf = interactiveObj.number3 * 10;

            for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.hcf; interactiveObj.i++)     // gets the higest common factor
            {
                if (interactiveObj.hcf % interactiveObj.i == 0 && 10 % interactiveObj.i == 0)
                {
                    interactiveObj.divisor = interactiveObj.i;
                }
            }

            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                if (interactiveObj.answer_3[0] % 2 == 0 && interactiveObj.answer_3[1] % 2 == 0)	// CHECKING IF THE USERS ANSWER IS IN LOWEST FO
                {
                    notInLowestFormFlag = 1;
                    ////alert("Flag set to 1==="+interactiveObj.answer_3[0]);
                }

            }
        }

        //if(notInLowestFormFlag==0 && attemp_Ques3==2)
        if (attemp_Ques3 == 2)
        {
            // //alert("in 2nd attempt");

            notInLowestFormFlag = 0;

            if (Incorrect_firstAttempt == 1)
            {
                interactiveObj.answer_3 = $("#answer3").val().split("/");
                interactiveObj.local = parseFloat(parseInt(interactiveObj.answer_3[0]) / parseInt(interactiveObj.answer_3[1]));

                interactiveObj.userResponse_Q3_A2 = interactiveObj.answer_3;   //user response

                //extraParameters+="|"+interactiveObj.userResponse_Q3_A2;
                //------------//
                if (interactiveObj.local == interactiveObj.correctAnswer3)
                {
                    if (interactiveObj.answer_3[0] % 2 == 0 && interactiveObj.answer_3[1] % 2 == 0)	// CHECKING IF THE USERS ANSWER IS IN LOWEST FO
                    {
                        notInLowestFormFlag = 1;
                        ////alert("Flag set to 1==="+interactiveObj.answer_3[0]);
                    }
                }
                //------------//

            }

            if (Incorrect_firstAttempt == 0)
            {
                interactiveObj.answer_3_num = parseInt($("#Input_num3").val());
                interactiveObj.answer_3_denom = parseInt($("#Input_denom3").val());
                interactiveObj.local = interactiveObj.answer_3_num / interactiveObj.answer_3_denom;

                interactiveObj.userResponse_Q3_A2 = interactiveObj.answer_3_num + "," + interactiveObj.answer_3_denom;   //user response
                extraParameters += "|" + interactiveObj.userResponse_Q3_A2;
                //------------//
                if (interactiveObj.local == interactiveObj.correctAnswer3)
                {
                    if (interactiveObj.answer_3_num % 2 == 0 && interactiveObj.answer_3_denom % 2 == 0)		// CHECKING IF THE USERS ANSWER IS IN LOWEST FORM
                    {
                        notInLowestFormFlag = 1;
                        ////alert("Flag set to 1 in second attempt");
                    }
                }
                //-----------//
            }

            interactiveObj.correctAnswer3 = interactiveObj.number3;
            interactiveObj.y = interactiveObj.number3;			//saving the correct answer in a locak variable for display on prompt

        }
        //if(notInLowestFormFlag==0 && attemp_Ques3==3)
        if (attemp_Ques3 == 3)
        {
            //  //alert("in 3rd attempt");

            notInLowestFormFlag = 0;

            interactiveObj.answer_3_num = parseInt($("#Input_num3").val());
            interactiveObj.answer_3_denom = parseInt($("#Input_denom3").val());
            interactiveObj.local = interactiveObj.answer_3_num / interactiveObj.answer_3_denom;

            interactiveObj.userResponse_Q3_A3 = interactiveObj.answer_3_num + "," + interactiveObj.answer_3_denom;   //user response

            //extraParameters+="|"+interactiveObj.userResponse_Q3_A3;

            interactiveObj.correctAnswer3 = interactiveObj.number3;
            interactiveObj.y = interactiveObj.number3;			//saving the correct answer in a locak variable for display on prompt

            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                if (interactiveObj.answer_3_num % 2 == 0 && interactiveObj.answer_3_denom % 2 == 0)		// CHECKING IF THE USERS ANSWER IS IN LOWEST FORM
                {
                    notInLowestFormFlag = 1;
                    ////alert("Flag set to 1 in second attempt");
                }
            }
        }

        if (attemp_Ques3 == 4)
        {
            notInLowestFormFlag = 0;

            interactiveObj.answer_3_num = parseInt($("#Input_num3").val());
            interactiveObj.answer_3_denom = parseInt($("#Input_denom3").val());
            interactiveObj.local = interactiveObj.answer_3_num / interactiveObj.answer_3_denom;

            interactiveObj.userResponse_Q3_A4 = interactiveObj.answer_3_num + "," + interactiveObj.answer_3_denom;   //user response

            //extraParameters+="|"+interactiveObj.userResponse_Q3_A4+")";

            interactiveObj.correctAnswer3 = interactiveObj.number3;
            interactiveObj.y = interactiveObj.number3;			//saving the correct answer in a locak variable for display on prompt

            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                if (interactiveObj.answer_3_num % 2 == 0 && interactiveObj.answer_3_denom % 2 == 0)		// CHECKING IF THE USERS ANSWER IS IN LOWEST FORM
                {
                    notInLowestFormFlag = 1;
                    ////alert("Flag set to 1 in second attempt");
                }
            }
        }


        //-------------- CORRECT BUT NOT  IN LOWEST FORM PROMPTS----------------//
        html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="fraction_wellDone_NLF"> <div class="fraction" style="left: 26px;font-size: 17px;"><div class="frac numerator"> ' + replaceDynamicText(interactiveObj.number3 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt2" style="left:166px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


        html2 += '<div id="wellDone_NLF_A" class="correct"><div class="sparkie"></div><div id="textCorrect" style="top: 57px;left: 57px;font-size: 16px;"><div id="fra_wellDone_NLF_A"><div class="fraction" style="font-size:16px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt" style="left: 88px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        html2 += '<div id="wellDone_NLF_Final" class="correct"><div class="textCorrect_WellDone_NLF_Final">' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '<br/>' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + ' <div class="fraction_wellDone_NLF_Final"> <div class="fraction"><div class="frac numerator"> ' + replaceDynamicText(interactiveObj.number3 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="left:130px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        html2 += '<div id="message3" class="correct"><div class="sparkie"></div><div id="textCorrect1">' + replaceDynamicText(interactiveObj.y, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        html2 += '<div id="showAns3" class="correct"><div class="sparkie"></div><div id="textCorrect3" style="left: -44px;">' + replaceDynamicText(interactiveObj.y, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;<div class="fraction" style="left: 138px;top: -7px;font-size: 18px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><div class="tag2">' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="fra4" ><div class="upper"style="left:86px;">' + replaceDynamicText(interactiveObj.y * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left:90px;">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


        $("#prompts").html(html2);

        $("#showAns3").draggable({containment: "#container"});
        $("#message3").draggable({containment: "#container"});
        $("#wellDone_LF").draggable({containment: "#container"});
        $("#wellDone_NLF_Final").draggable({containment: "#container"});
        $("#wellDone_NLF_A").draggable({containment: "#container"});
        $("#wellDone_NLF").draggable({containment: "#container"});

    }
    if (interactiveObj.question_Type == "four")
    {

        html2 = "";
        attemp_Ques4 += 1;
        notInLowestFormFlagQ4 = 0;
        if (attemp_Ques4 == 1)
        {
            interactiveObj.answer_4 = $("#answer4").val().split("/");
            interactiveObj.correctAnswer4 = interactiveObj.number4;
            interactiveObj.answer4 = parseFloat(parseInt(interactiveObj.answer_4[0]) / parseInt(interactiveObj.answer_4[1]));// answer from user
            interactiveObj.local = interactiveObj.answer4;
            interactiveObj.z = interactiveObj.local;

            interactiveObj.userResponse_Q4_A1 = interactiveObj.answer_4;
            interactiveObj.xander = interactiveObj.number4.split(".");

            if ((parseInt(interactiveObj.answer_4[0]) % 2 == 0 && parseInt(interactiveObj.answer_4[1]) % 2 == 0) || (parseInt(interactiveObj.answer_4[0]) % 5 == 0 && parseInt(interactiveObj.answer_4[1]) % 5 == 0))
            {
                notInLowestFormFlagQ4 = 1;
            }

            if (notInLowestFormFlagQ4 == 1)
            {
                // finding the highest common factor betwen numerator and denominator

                for (interactiveObj.i = 1; interactiveObj.i <= parseInt(interactiveObj.xander[1]); interactiveObj.i++)  // gets the HCF of Num/ Denom
                {
                    if ((parseInt(interactiveObj.xander[1]) % interactiveObj.i) == 0 && (parseInt(100) % interactiveObj.i) == 0)
                    {
                        interactiveObj.divisibleBy = interactiveObj.i;
                    }////console.log("Divisible by="+interactiveObj.divisibleBy);
                }
            }
            else
            {
                interactiveObj.divisibleBy = parseInt(interactiveObj.answer_4[1]);
                interactiveObj.a = parseInt(interactiveObj.answer_4[0]);
            }

        }

        if (attemp_Ques4 == 2)
        {
            notInLowestFormFlagQ4 = 0;

            if (Incorrect_firstAttemptQ4 == 1)
            {
                interactiveObj.answer_4 = $("#answer4").val().split("/");
                interactiveObj.correctAnswer4 = interactiveObj.number4;
                interactiveObj.answer4 = parseFloat(parseInt(interactiveObj.answer_4[0]) / parseInt(interactiveObj.answer_4[1]));// answer from user
                interactiveObj.local = interactiveObj.answer4;
                interactiveObj.z = interactiveObj.local;

                interactiveObj.xander = interactiveObj.number4.split(".");

                interactiveObj.userResponse_Q4_A2 = interactiveObj.answer_4;   // user response


                if ((parseInt(interactiveObj.answer_4[0]) % 2 == 0 && parseInt(interactiveObj.answer_4[1]) % 2 == 0) || (parseInt(interactiveObj.answer_4[0]) % 5 == 0 && parseInt(interactiveObj.answer_4[1]) % 5 == 0))
                {
                    notInLowestFormFlagQ4 = 1;
                }

            }
            else
            {
                interactiveObj.answer_4_num = parseInt($("#Input_num4").val());
                interactiveObj.answer_4_denom = parseInt($("#Input_denom4").val());
                interactiveObj.local = interactiveObj.answer_4_num / interactiveObj.answer_4_denom;  // answer from user
                interactiveObj.correctAnswer4 = interactiveObj.number4;
                interactiveObj.xander = interactiveObj.number4.split(".");

                interactiveObj.userResponse_Q4_A2 = interactiveObj.answer_4_num + "," + interactiveObj.answer_4_denom;   // user response



                //checking for lowest form
                if ((parseInt(interactiveObj.answer_4_num) % 2 == 0 && parseInt(interactiveObj.answer_4_denom) % 2 == 0) || (parseInt(interactiveObj.answer_4_num) % 5 == 0 && parseInt(interactiveObj.answer_4_denom) % 5 == 0))
                {
                    notInLowestFormFlagQ4 = 1;
                }
            }
            // finding the highest common factor

            for (interactiveObj.i = 1; interactiveObj.i <= parseInt(interactiveObj.xander[1]); interactiveObj.i++)  // gets the HCF of Num/ Denom
            {
                if ((parseInt(interactiveObj.xander[1]) % interactiveObj.i) == 0 && (parseInt(100) % interactiveObj.i) == 0)
                {
                    interactiveObj.divisibleBy = interactiveObj.i;
                }////console.log("Divisible by="+interactiveObj.divisibleBy);
            }

        }

        if (attemp_Ques4 == 3)
        {
            notInLowestFormFlagQ4 = 0;

            interactiveObj.answer_4_num = parseInt($("#Input_num4").val());
            interactiveObj.answer_4_denom = parseInt($("#Input_denom4").val());
            interactiveObj.local = interactiveObj.answer_4_num / interactiveObj.answer_4_denom;  // answer from user

            interactiveObj.correctAnswer4 = interactiveObj.number4;
            interactiveObj.xander = interactiveObj.number4.split(".");
            interactiveObj.userResponse_Q4_A3 = interactiveObj.answer_4_num + "," + interactiveObj.answer_4_denom;   // user response

            //checking for lowest form
            if ((parseInt(interactiveObj.answer_4_num) % 2 == 0 && parseInt(interactiveObj.answer_4_denom) % 2 == 0) || (parseInt(interactiveObj.answer_4_num) % 5 == 0 && parseInt(interactiveObj.answer_4_denom) % 5 == 0))
            {
                notInLowestFormFlagQ4 = 1;
            }

            // finding the highest common factor

            for (interactiveObj.i = 1; interactiveObj.i <= parseInt(interactiveObj.xander[1]); interactiveObj.i++)  // gets the HCF of Num/ Denom
            {
                if ((parseInt(interactiveObj.xander[1]) % interactiveObj.i) == 0 && (parseInt(100) % interactiveObj.i) == 0)
                {
                    interactiveObj.divisibleBy = interactiveObj.i;
                }////console.log("Divisible by="+interactiveObj.divisibleBy);
            }
        }



        html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="fraction_wellDone_NLF" style="top: 58px;"><div class="fraction" style="left: 26px;font-size: 15px;"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


        html2 += '<div id="wellDone_NLF_A" class="correct"><div class="sparkie"></div><div id="textCorrect" style="top: 57px;left: 57px;font-size: 16px;"><div id="fra_wellDone_NLF_A"><div class="fraction" style="font-size:16px;"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt" style="left:85px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        html2 += '<div id="showAns4" class="correct"><div class="sparkie"></div><div id="textCorrect3" style="left: -44px;">' + replaceDynamicText(interactiveObj.number4, interactiveObj.numberLanguage, "interactiveObj") + ' =&nbsp;<div class="fraction" style="left: 143px;top: -7px;font-size: 18px;"><div class="frac numerator"> ' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><div class="tag2">' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="fra4" ><div class="upper"style="left:86px;"> ' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left:84px;">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        html2 += '<div id="wellDone_NLF_Final" class="correct"><div class="textCorrect_WellDone_NLF_Final4">' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction_wellDone_NLF_Final4"><div class="fraction" style="font-size:16px;top:10px;"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="left:133px">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


        html2 += '<div id="message4" class="correct"><div class="sparkie"></div><div id="textCorrect1" style="left: 34px;width: 201px;">' + replaceDynamicText(interactiveObj.number4, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText((interactiveObj.number4 * 100).toFixed(0), interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt" style="left: 88px;">' + promptArr['txt_7'] + '</button></div>';

        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        $("#prompts").html(html2);

        $("#showAns4").draggable({containment: "#container"});
        $("#message4").draggable({containment: "#container"});
        $("#wellDone_LF").draggable({containment: "#container"});
        $("#wellDone_NLF_Final").draggable({containment: "#container"});
        $("#wellDone_NLF_A").draggable({containment: "#container"});
        $("#wellDone_NLF").draggable({containment: "#container"});
    }


   if (interactiveObj.question_Type == "five")
    {

        html2 = "";
        attemp_Ques5 += 1;

        if (GenerateQ5 == 1)
        {
            interactiveObj.correctAnswer5 = interactiveObj.number_1;
            interactiveObj.xander = parseInt(interactiveObj.number_1 * 10);
            interactiveObj.hcf = interactiveObj.number_1 * 10;
            interactiveObj.message = interactiveObj.correctAnswer5 * 10;
            interactiveObj.d = parseInt(10);
        }
        if (GenerateQ5 == 2)
        {
            interactiveObj.correctAnswer5 = interactiveObj.number_2;
            interactiveObj.xander = parseInt(interactiveObj.number_2 * 100);
            interactiveObj.hcf = interactiveObj.number_2 * 100;
            interactiveObj.message = interactiveObj.correctAnswer5 * 100;
            interactiveObj.d = parseInt(100);
        }
        if (GenerateQ5 == 3)
        {
            interactiveObj.correctAnswer5 = interactiveObj.number_3;
            interactiveObj.xander = parseInt(interactiveObj.number_3 * 10);
            //interactiveObj.hcf=interactiveObj.number_3*10;
            interactiveObj.message = interactiveObj.correctAnswer5 * 10;
            interactiveObj.d = parseInt(10);
            //checking for lowest form
            if (interactiveObj.local == interactiveObj.correctAnswer5)
            {
                if (interactiveObj.answer_5[0] % 2 == 0 && interactiveObj.answer_5[1] % 2 == 0)	// CHECKING IF THE USERS ANSWER IS IN LOWEST FO
                {
                    notInLowestFormFlagQ5 = 1;
                    ////alert("Flag set to 1==="+interactiveObj.answer_3[0]);
                }
            }
            for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.xander; interactiveObj.i++)
            {
                if (interactiveObj.xander % interactiveObj.i == 0 && 10 % interactiveObj.i == 0)
                {
                    interactiveObj.divisor = interactiveObj.i;
                }
            }


        }

        if (GenerateQ5 == 4)
        {
            interactiveObj.correctAnswer5 = interactiveObj.number_4;
            interactiveObj.xander = parseInt(interactiveObj.number_4 * 100);
            interactiveObj.hcf = interactiveObj.number_4 * 100;
            interactiveObj.message = interactiveObj.correctAnswer5 * 100;
            interactiveObj.d = parseInt(100);
            //checking for hcf



            /*html2+='<div id="wellDone_LF" class="correct"><div id="textCorrect">Well Done<button id="button_prompt" onclick=interactiveObj.loadQuestions(); class="css3button2">OK</button></div>';
             
             $("#prompts").html(html2); */

        }


        if (called1 == 1 || called2 == 1)
        {
            interactiveObj.answer_5 = $("#answer5").val().split("/");
            interactiveObj.answer5 = parseFloat(parseInt(interactiveObj.answer_5[0]) / parseInt(interactiveObj.answer_5[1]));    // answer from user
            interactiveObj.local = interactiveObj.answer5;

            if (attemp_Ques5 == 1)
            {
                interactiveObj.extraParameters_Q5_A1 = interactiveObj.answer_5;
                extraParameters += "(Q5:-" + interactiveObj.correctAnswer5 + "|" + interactiveObj.extraParameters_Q5_A1;
            }
            if (attemp_Ques5 == 2)
            {
                interactiveObj.extraParameters_Q5_A2 = interactiveObj.answer_5;
                extraParameters += "|" + interactiveObj.extraParameters_Q5_A1 + ")";
            }


        }

        if (called3 == 1 || called4 == 1)
        {
            if (called3 == 1)
            {
                interactiveObj.divide = parseInt(10);
                interactiveObj.string = interactiveObj.number_3;
            }
            if (called4 == 1)
            {
                interactiveObj.divide = parseInt(100);
                interactiveObj.string = interactiveObj.number_4;
            }


            if (attemp_Ques5 == 1)
            {
                notInLowestFormQ5 = 0;
                interactiveObj.answer_5 = $("#answer5").val().split("/");
                interactiveObj.answer5 = parseFloat(parseInt(interactiveObj.answer_5[0]) / parseInt(interactiveObj.answer_5[1]));    // answer from user
                interactiveObj.local = interactiveObj.answer5;
                ////alert("answer entered by user="+interactiveObj.local);

                interactiveObj.userResponse_Q5_A1 = interactiveObj.answer_5;
                extraParameters += "(Q5:-" + interactiveObj.correctAnswer5 + "|" + interactiveObj.extraParameters_Q5_A1;

                if ((parseInt(interactiveObj.answer_5[0]) % 2 == 0 && parseInt(interactiveObj.answer_5[1]) % 2 == 0) || (parseInt(interactiveObj.answer_5[0]) % 5 == 0 && parseInt(interactiveObj.answer_5[1]) % 5 == 0))
                {
                    notInLowestFormQ5 = 1;
                }
                if (notInLowestFormQ5 == 1)
                {
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.hcf; interactiveObj.i++)     // gets the higest common factor
                    {
                        if (interactiveObj.hcf % interactiveObj.i == 0 && interactiveObj.divide % interactiveObj.i == 0)
                        {
                            interactiveObj.divisor = interactiveObj.i;
                        }
                    }
                }
            }
            if (attemp_Ques5 == 2)
            {
                notInLowestFormQ5 = 0;

                if (Q5_Incorrect_A1 == 1)
                {
                    interactiveObj.answer_5 = $("#answer5").val().split("/");
                    interactiveObj.answer5 = parseFloat(parseInt(interactiveObj.answer_5[0]) / parseInt(interactiveObj.answer_5[1]));    // answer from user
                    interactiveObj.local = interactiveObj.answer5;
                    // //alert("answer entered by user="+interactiveObj.local);

                    interactiveObj.userResponse_Q5_A2 = interactiveObj.answer_5;
                    extraParameters += "|" + interactiveObj.userResponse_Q5_A2;

                    if ((parseInt(interactiveObj.answer_5[0]) % 2 == 0 && parseInt(interactiveObj.answer_5[1]) % 2 == 0) || (parseInt(interactiveObj.answer_5[0]) % 5 == 0 && parseInt(interactiveObj.answer_5[1]) % 5 == 0))
                    {
                        notInLowestFormQ5 = 1;
                    }

                    if (notInLowestFormQ5 == 1)
                    {
                        for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.hcf; interactiveObj.i++)     // gets the higest common factor
                        {
                            if (interactiveObj.hcf % interactiveObj.i == 0 && interactiveObj.divide % interactiveObj.i == 0)
                            {
                                interactiveObj.divisor = interactiveObj.i;
                            }
                        }
                    }
                }
                else
                {
                    interactiveObj.answer_5_num = parseInt($("#Input_num5").val());
                    interactiveObj.answer_5_denom = parseInt($("#Input_denom5").val());
                    interactiveObj.local = parseFloat(interactiveObj.answer_5_num / interactiveObj.answer_5_denom);  // answer from user	
                    // //alert("answer entered by user="+interactiveObj.local);

                    interactiveObj.userResponse_Q5_A2 = interactiveObj.answer_5_num + "," + interactiveObj.answer_5_denom;

                    extraParameters += "|" + interactiveObj.userResponse_Q5_A2;

                    if ((parseInt(interactiveObj.answer_5_num) % 2 == 0 && parseInt(interactiveObj.answer_5_denom) % 2 == 0) || (parseInt(interactiveObj.answer_5_num) % 5 == 0 && parseInt(interactiveObj.answer_5_denom) % 5 == 0))
                    {
                        notInLowestFormQ5 = 1;
                    }

                    if (notInLowestFormQ5 == 1)
                    {
                        for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.hcf; interactiveObj.i++)     // gets the higest common factor
                        {
                            if (interactiveObj.hcf % interactiveObj.i == 0 && interactiveObj.divide % interactiveObj.i == 0)
                            {
                                interactiveObj.divisor = interactiveObj.i;
                            }
                        }
                    }

                }
            }
            if (attemp_Ques5 == 3)
            {
                // //alert("in attempt 3")
                notInLowestFormQ5 = 0;

                interactiveObj.answer_5_num = parseInt($("#Input_num5").val());
                interactiveObj.answer_5_denom = parseInt($("#Input_denom5").val());
                interactiveObj.local = parseFloat(interactiveObj.answer_5_num / interactiveObj.answer_5_denom);  // answer from user	
                // //alert("answer entered by user="+interactiveObj.local);

                interactiveObj.userResponse_Q5_A3 = interactiveObj.answer_5_num + "," + interactiveObj.answer_5_denom;

                extraParameters += "|" + interactiveObj.userResponse_Q5_A3 + ")";

                if ((parseInt(interactiveObj.answer_5_num) % 2 == 0 && parseInt(interactiveObj.answer_5_denom) % 2 == 0) || (parseInt(interactiveObj.answer_5_num) % 5 == 0 && parseInt(interactiveObj.answer_5_denom) % 5 == 0))
                {
                    //   //alert("3rd attempt inside lowest form loop")
                    notInLowestFormQ5 = 1;
                }

                if (notInLowestFormQ5 == 1)
                {
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.hcf; interactiveObj.i++)     // gets the higest common factor
                    {
                        if (interactiveObj.hcf % interactiveObj.i == 0 && interactiveObj.divide % interactiveObj.i == 0)
                        {
                            interactiveObj.divisor = interactiveObj.i;
                        }
                    }
                }
                else
                {
                    interactiveObj.answer_5_num = parseInt($("#Input_num5").val());
                    interactiveObj.answer_5_denom = parseInt($("#Input_denom5").val());
                    interactiveObj.local = parseFloat(interactiveObj.answer_5_num / interactiveObj.answer_5_denom);  // answer from user	

                }

            }

        }


        html2 += '<div id="wellDone_LF" class="correct"><div id="textCorrect">' + promptArr['txt_4'] + '<button id="button_prompt" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">OK</button></div>';

        html2 += '<div id="message5" class="correct"><div id="textCorrect1">' + interactiveObj.correctAnswer5 + ' ' + promptArr['txt_5'] + ' ' + interactiveObj.message + ' ' + promptArr['txt_6'] + '.<br/>' + promptArr['txt_3'] + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['txt_7'] + '</button></div>';

        html2 += '<div id="showAns5" class="correct"><div id="textCorrect2" style="left: -44px;">' + interactiveObj.correctAnswer5 + ' = ' + interactiveObj.message + '' + promptArr['txt_6'] + ' = ' + interactiveObj.message / interactiveObj.d + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['txt_7'] + '</button></div>';

        html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="textCorrect">' + promptArr['txt_10'] + '<br/> ' + promptArr['txt_11'] + ' ' + interactiveObj.message + '/ 10 ' + promptArr['txt_12'] + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_prompt2" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['txt_7'] + '</button></div>';

        html2 += '<div id="wellDone_NLF_A" class="correct"><div id="textCorrect">' + promptArr['txt_9'] + '</div><button id="button_prompt3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['txt_7'] + '</button></div>';

        html2 += '<div id="wellDone_NLF" class="correct"><div id="textCorrect">' + (promptArr['txt_2']).replace("#number#", interactiveObj.number_4 * interactiveObj.d + "/" + 100) + '</div><button id="button_prompt1" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['txt_7'] + '</button></div>';

        html2 += '<div id="wellDone_NLF_A" class="correct"><div id="textCorrect">' + promptArr['txt_9'] + '</div><button id="button_prompt3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['txt_7'] + '</button></div>';

        $("#prompts").html(html2);
    }


    //------------sixth question getting values--------------//
    if (interactiveObj.question_Type == "six")
    {//------parent if opens//
        attemp_Ques6 += 1;
        notInLowestFormQ6 = 0;
        html2 = "";

        if (GenerateQ5 == 1)
        {
            interactiveObj.correctAnswer6 = interactiveObj.number_1;
            interactiveObj.visible6 = parseFloat(interactiveObj.correctAnswer6 * 10).toFixed(0);
            interactiveObj.divide6 = parseInt(10);
            //answer from user//
            interactiveObj.answer6 = $("#answer6").val().split("/");
            interactiveObj.answer6_num = parseInt(interactiveObj.answer6[0]);
            interactiveObj.answer6_denom = parseInt(interactiveObj.answer6[1]);
            interactiveObj.local = parseFloat(interactiveObj.answer6_num / interactiveObj.answer6_denom);

            if (attemp_Ques6 == 1)  //user response
            {
                interactiveObj.userResponse_Q6_A1 = interactiveObj.answer6;

            }
            if (attemp_Ques6 == 2) 	//user response
            {
                interactiveObj.userResponse_Q6_A2 = interactiveObj.answer6;

            }

            // html2+='<div id="wellDone_LF" class="correct"><div id="textCorrect">'+promptArr['txt_4']+'<button id="button_prompt" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">'+promptArr['txt_7']+'</button></div>';

            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="message5" class="correct"><div class="sparkie"></div><div id="textCorrect1">' + replaceDynamicText(interactiveObj.correctAnswer6, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="showAns5" class="correct"><div class="sparkie"></div><div id="textCorrect2" style="left: 19px;">' + replaceDynamicText(interactiveObj.correctAnswer6, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;=&nbsp;&nbsp;<div class="fra2" style="left: 105px;"><div class="upper">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left: 85px;">10</div></div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt"  style="left:112px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            $("#prompts").html(html2);

            $("#wellDone_LF").draggable({containment: "#container"});
            $("#showAns5").draggable({containment: "#container"});
            $("#message5").draggable({containment: "#container"});
        }
        if (GenerateQ5 == 2)
        {
            interactiveObj.correctAnswer6 = interactiveObj.number_2;
            interactiveObj.visible6 = parseFloat(interactiveObj.correctAnswer6 * 100).toFixed(0);
            interactiveObj.divide6 = parseInt(100);
            //answer from user//
            interactiveObj.answer6 = $("#answer6").val().split("/");
            interactiveObj.answer6_num = parseInt(interactiveObj.answer6[0]);
            interactiveObj.answer6_denom = parseInt(interactiveObj.answer6[1]);
            interactiveObj.local = parseFloat(interactiveObj.answer6_num / interactiveObj.answer6_denom);

            if (attemp_Ques6 == 1)  //user response
            {
                interactiveObj.userResponse_Q6_A1 = interactiveObj.answer6;

            }
            if (attemp_Ques6 == 2) 	//user response
            {
                interactiveObj.userResponse_Q6_A2 = interactiveObj.answer6;


            }

            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="message5" class="correct"><div class="sparkie"></div><div id="textCorrect1" style="left: 37px;width: 200px;">' + replaceDynamicText(interactiveObj.correctAnswer6, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="showAns5" class="correct"><div class="sparkie"></div><div id="textCorrect2" style="left: 34px;">' + replaceDynamicText(interactiveObj.correctAnswer6, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;=&nbsp;&nbsp;<div class="fra2" style="left: 130px;"><div class="upper">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left: 78px;">' + replaceDynamicText(interactiveObj.divide6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt"  style="left:123px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            $("#prompts").html(html2);
            $("#wellDone_LF").draggable({containment: "#container"});
            $("#showAns5").draggable({containment: "#container"});
            $("#message5").draggable({containment: "#container"});


        }
        if (GenerateQ5 == 3)
        {
            interactiveObj.correctAnswer6 = interactiveObj.number_3;
            interactiveObj.visible6 = parseFloat(interactiveObj.correctAnswer6 * 10).toFixed(0);
            interactiveObj.divide6 = parseInt(10);
            //answer from user//
            if (attemp_Ques6 == 1)
            {
                interactiveObj.answer6 = $("#answer6").val().split("/");
                interactiveObj.answer6_num = parseInt(interactiveObj.answer6[0]);
                interactiveObj.answer6_denom = parseInt(interactiveObj.answer6[1]);
                interactiveObj.local = parseFloat(interactiveObj.answer6_num / interactiveObj.answer6_denom);

                interactiveObj.userResponse_Q6_A1 = interactiveObj.answer6;


                //getting hcf//
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.visible6; interactiveObj.i++)
                {
                    if (interactiveObj.visible6 % interactiveObj.i == 0 && interactiveObj.divide6 % interactiveObj.i == 0)
                    {
                        interactiveObj.hcf = interactiveObj.i;
                    }

                }
                //checking if users answer is in lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer6_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer6_num % 2 == 0 && interactiveObj.answer6_denom % 2 == 0) || (interactiveObj.answer6_num % 5 == 0 && interactiveObj.answer6_denom % 5 == 0))
                    {
                        notInLowestFormQ6 = 1;
                    }
                }

            }

            if (attemp_Ques6 == 2)
            {
                if (Q6_Incorrect_A1 == 1)
                {
                    interactiveObj.answer6 = $("#answer6").val().split("/");
                    interactiveObj.answer6_num = parseInt(interactiveObj.answer6[0]);
                    interactiveObj.answer6_denom = parseInt(interactiveObj.answer6[1]);
                    interactiveObj.local = parseFloat(interactiveObj.answer6_num / interactiveObj.answer6_denom);

                    interactiveObj.userResponse_Q6_A2 = interactiveObj.answer6;


                    //getting hcf//
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.visible6; interactiveObj.i++)
                    {
                        if (interactiveObj.visible6 % interactiveObj.i == 0 && interactiveObj.divide6 % interactiveObj.i == 0)
                        {
                            interactiveObj.hcf = interactiveObj.i;
                        }

                    }
                    //checking if users answer is in lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer6_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer6_num % 2 == 0 && interactiveObj.answer6_denom % 2 == 0) || (interactiveObj.answer6_num % 5 == 0 && interactiveObj.answer6_denom % 5 == 0))
                        {
                            notInLowestFormQ6 = 1;
                        }
                    }

                }
                else
                {
                    interactiveObj.answer_6_num = parseInt($("#Input_num6").val());
                    interactiveObj.answer_6_denom = parseInt($("#Input_denom6").val());
                    interactiveObj.local = parseFloat(interactiveObj.answer_6_num / interactiveObj.answer_6_denom);  // answer from user	
                    //   //alert("answer entered by user="+interactiveObj.local);

                    interactiveObj.userResponse_Q6_A2 = interactiveObj.answer_6_num + "," + interactiveObj.answer_6_denom;



                    //checking whether this answer is not lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer_6_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer_6_num % 2 == 0 && interactiveObj.answer_6_denom % 2 == 0) || (interactiveObj.answer_6_num % 5 == 0 && interactiveObj.answer_6_denom % 5 == 0))
                        {
                            notInLowestFormQ6 = 1;
                        }
                    }
                }
            }

            if (attemp_Ques6 == 3)
            {
                interactiveObj.answer_6_num = parseInt($("#Input_num6").val());
                interactiveObj.answer_6_denom = parseInt($("#Input_denom6").val());
                interactiveObj.local = parseFloat(interactiveObj.answer_6_num / interactiveObj.answer_6_denom);  // answer from user	
                //  //alert("answer entered by user="+interactiveObj.local);

                interactiveObj.userResponse_Q6_A3 = interactiveObj.answer_6_num + "," + interactiveObj.answer_6_denom;



                //checking whether this answer is not lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer_6_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer_6_num % 2 == 0 && interactiveObj.answer_6_denom % 2 == 0) || (interactiveObj.answer_6_num % 5 == 0 && interactiveObj.answer_6_denom % 5 == 0))
                    {
                        notInLowestFormQ6 = 1;
                    }
                }
            }



            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            html2 += '<div id="message5" class="correct"><div class="sparkie"></div><div id="textCorrect1">' + replaceDynamicText(interactiveObj.correctAnswer6, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';



            html2 += '<div id="showAns5" class="correct"><div class="sparkie"></div><div id="textCorrect2" style="left: -44px;">' + replaceDynamicText(interactiveObj.correctAnswer6, interactiveObj.numberLanguage, "interactiveObj") + '=&nbsp;<div class="fraction" style="left: 135px;top: -7px;font-size: 18px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><div class="tag2">' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="fra4" ><div class="upper"style="left:86px;">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left:90px;">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            html2 += '<div id="wellDone_NLF_Final" class="correct"><div class="textCorrect_WellDone_NLF_Final4">' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction_wellDone_NLF_Final4"><div class="fraction" style="font-size:16px;top:10px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="left: 130px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="wellDone_NLF_A" class="correct"><div class="sparkie"></div><div id="textCorrect" style="top: 57px;left: 57px;font-size: 16px;"><div id="fra_wellDone_NLF_A"><div class="fraction" style="font-size:16px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.correctAnswer6 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numbeLanguage, "interactiveObj") + '</button></div>';



            html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="fraction_wellDone_NLF"> <div class="fraction" style="left: 26px;font-size: 17px;"><div class="frac numerator"> ' + replaceDynamicText(interactiveObj.correctAnswer6 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            $("#prompts").html(html2);

            $("#wellDone_NLF").draggable({containment: "#container"});
            $("#wellDone_NLF_A").draggable({containment: "#container"});
            $("#wellDone_NLF_Final").draggable({containment: "#container"});
            $("#showAns5").draggable({containment: "#container"});
            $("#message5").draggable({containment: "#container"});
            $("#wellDone_LF").draggable({containment: "#container"});

        }
        if (GenerateQ5 == 4)
        {
            interactiveObj.correctAnswer6 = interactiveObj.number_4;
            interactiveObj.visible6 = parseFloat(interactiveObj.correctAnswer6 * 100).toFixed(0);
            interactiveObj.divide6 = parseInt(100);
            //answer from user//
            if (attemp_Ques6 == 1)
            {
                interactiveObj.answer6 = $("#answer6").val().split("/");
                interactiveObj.answer6_num = parseInt(interactiveObj.answer6[0]);
                interactiveObj.answer6_denom = parseInt(interactiveObj.answer6[1]);
                interactiveObj.local = parseFloat(interactiveObj.answer6_num / interactiveObj.answer6_denom);

                interactiveObj.userResponse_Q6_A1 = interactiveObj.answer6;


                //getting hcf//
                for (interactiveObj.i = 0; interactiveObj.i < interactiveObj.visible6; interactiveObj.i++)
                {
                    if (interactiveObj.visible6 % interactiveObj.i == 0 && interactiveObj.divide6 % interactiveObj.i == 0)
                    {
                        interactiveObj.hcf = interactiveObj.i;
                    }

                }
                //checking if users answer is in lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer6_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer6_num % 2 == 0 && interactiveObj.answer6_denom % 2 == 0) || (interactiveObj.answer6_num % 5 == 0 && interactiveObj.answer6_denom % 5 == 0))
                    {
                        notInLowestFormQ6 = 1;
                    }
                }

            }

            if (attemp_Ques6 == 2)
            {
                if (Q6_Incorrect_A1 == 1)
                {
                    interactiveObj.answer6 = $("#answer6").val().split("/");
                    interactiveObj.answer6_num = parseInt(interactiveObj.answer6[0]);
                    interactiveObj.answer6_denom = parseInt(interactiveObj.answer6[1]);
                    interactiveObj.local = parseFloat(interactiveObj.answer6_num / interactiveObj.answer6_denom);

                    interactiveObj.userResponse_Q6_A2 = interactiveObj.answer6;


                    //getting hcf//
                    for (interactiveObj.i = 0; interactiveObj.i < interactiveObj.visible6; interactiveObj.i++)
                    {
                        if (interactiveObj.visible6 % interactiveObj.i == 0 && interactiveObj.divide6 % interactiveObj.i == 0)
                        {
                            interactiveObj.hcf = interactiveObj.i;
                        }

                    }
                    //checking if users answer is in lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer6_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer6_num % 2 == 0 && interactiveObj.answer6_denom % 2 == 0) || (interactiveObj.answer6_num % 5 == 0 && interactiveObj.answer6_denom % 5 == 0))
                        {
                            notInLowestFormQ6 = 1;
                        }
                    }

                }
                else
                {
                    interactiveObj.answer_6_num = parseInt($("#Input_num6").val());
                    interactiveObj.answer_6_denom = parseInt($("#Input_denom6").val());
                    interactiveObj.local = parseFloat(interactiveObj.answer_6_num / interactiveObj.answer_6_denom);  // answer from user	


                    interactiveObj.userResponse_Q6_A2 = interactiveObj.answer_6_num + "," + interactiveObj.answer_6_denom;

                    //checking whether this answer is not lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer6_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer6_num % 2 == 0 && interactiveObj.answer6_denom % 2 == 0) || (interactiveObj.answer6_num % 5 == 0 && interactiveObj.answer_6_denom % 5 == 0))
                        {
                            notInLowestFormQ6 = 1;
                        }
                    }
                }
            }
            if (attemp_Ques6 == 3)
            {
                interactiveObj.answer_6_num = parseInt($("#Input_num6").val());
                interactiveObj.answer_6_denom = parseInt($("#Input_denom6").val());
                interactiveObj.local = parseFloat(interactiveObj.answer_6_num / interactiveObj.answer_6_denom);  // answer from user	

                interactiveObj.userResponse_Q6_A3 = interactiveObj.answer_6_num + "," + interactiveObj.answer_6_denom;



                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer6_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer6_num % 2 == 0 && interactiveObj.answer6_denom % 2 == 0) || (interactiveObj.answer6_num % 5 == 0 && interactiveObj.answer_6_denom % 5 == 0))
                    {
                        notInLowestFormQ6 = 1;
                    }
                }

            }
            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + promptArr['txt_4'] + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + promptArr['txt_7'] + '</button></div>';

            html2 += '<div id="message5" class="correct"><div class="sparkie"></div><div id="textCorrect1">' + interactiveObj.correctAnswer6 + ' ' + promptArr['text_5'] + ' ' + interactiveObj.visible6 + ' ' + promptArr['text_6'] + '.<br/>' + promptArr['txt_3'] + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['text_7'] + '</button></div>';

            html2 += '<div id="showAns5" class="correct"><div class="sparkie"></div><div id="textCorrect2">' + interactiveObj.correctAnswer6 + ' = ' + interactiveObj.visible6 + '' + promptArr['text_6'] + ' = ' + interactiveObj.visible6 + '/' + interactiveObj.divide6 + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['text_7'] + '</button></div>';

            html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="textCorrect">' + promptArr['text_10'] + '<br/>' + promptArr['text_11'] + '  ' + interactiveObj.visible6 + '/' + interactiveObj.divide6 + ' ' + promptArr['text_12'] + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_prompt2" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['text_7'] + '</button></div>';

            html2 += '<div id="wellDone_NLF_A" class="correct"><div class="sparkie"></div><div id="textCorrect">' + promptArr['text_9'] + '</div><button id="button_prompt3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['text_7'] + '</button></div>';

            html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div id="textCorrect">' + (promptArr['txt_2']).replace("#number#", interactiveObj.correctAnswer6 + "/" + interactiveObj.divide6) + '</div><button id="button_prompt1" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + promptArr['text_7'] + '</button></div>';



            $("#prompts").html(html2);
        }

    }//------parent if closes//
    //------------sixth question getting values--------------//
    if (interactiveObj.question_Type == "seven")//getting the values for question seven
    {// parent if starst question 7
        attemp_Ques7 += 1;
        notInLowestFormQ7 = 0;
        html2 = "";

        if (GenerateQ6 == 2)  //when type of question is 2
        {
            interactiveObj.correctAnswer7 = interactiveObj.number_2;
            interactiveObj.visible7 = parseFloat(interactiveObj.correctAnswer7 * 100).toFixed(0);
            interactiveObj.divide7 = parseInt(100);
            //answer from user//
            interactiveObj.answer7 = $("#answer7").val().split("/");
            interactiveObj.answer7_num = parseInt(interactiveObj.answer7[0]);
            interactiveObj.answer7_denom = parseInt(interactiveObj.answer7[1]);
            interactiveObj.local = parseFloat(interactiveObj.answer7_num / interactiveObj.answer7_denom);

            if (attemp_Ques7 == 1)
            {
                interactiveObj.userResponse_Q7_A1 = interactiveObj.answer7;

            }
            if (attemp_Ques7 == 2)
            {
                interactiveObj.userResponse_Q7_A2 = interactiveObj.answer7;

            }

            html2 += '<div id="wellDone_LF" class="correct" style="top:155px;"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="message7" class="correct" style="top:157px;"><div class="sparkie"></div><div id="textCorrect1" style="left: 38px;width: 200px;top: 21px;">' + replaceDynamicText(interactiveObj.correctAnswer7, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt" style="left: 93px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="showAns7" class="correct" style="top:157px;"><div class="sparkie"></div><div id="textCorrect2" style="left: 29px;">' + replaceDynamicText(interactiveObj.correctAnswer7, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;=&nbsp;&nbsp;<div class="fra2" style="left: 128px;"><div class="upper">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left: 78px;">' + replaceDynamicText(interactiveObj.divide7, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt"  style="left:111px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            $("#prompts").html(html2);

            $("#showAns7").draggable({containment: "#container"});
            $("#message7").draggable({containment: "#container"});
            $("#wellDone_LF").draggable({containment: "#container"});

        }
        if (GenerateQ6 == 3)
        {
            interactiveObj.correctAnswer7 = interactiveObj.number_3;
            interactiveObj.visible7 = parseFloat(interactiveObj.correctAnswer7 * 10).toFixed(0);
            interactiveObj.divide7 = parseInt(10);
            //answer from user//
            if (attemp_Ques7 == 1)
            {
                interactiveObj.answer7 = $("#answer7").val().split("/");
                interactiveObj.answer7_num = parseInt(interactiveObj.answer7[0]);
                interactiveObj.answer7_denom = parseInt(interactiveObj.answer7[1]);
                interactiveObj.local = parseFloat(interactiveObj.answer7_num / interactiveObj.answer7_denom);

                interactiveObj.userResponse_Q7_A1 = interactiveObj.answer7;


                //getting hcf//
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.visible7; interactiveObj.i++)
                {
                    if (interactiveObj.visible7 % interactiveObj.i == 0 && interactiveObj.divide7 % interactiveObj.i == 0)
                    {
                        interactiveObj.highestCommonFactor = interactiveObj.i;
                    }

                }
                //checking if users answer is in lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer7_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer7_num % 2 == 0 && interactiveObj.answer7_denom % 2 == 0) || (interactiveObj.answer7_num % 5 == 0 && interactiveObj.answer7_denom % 5 == 0))
                    {
                        notInLowestFormQ7 = 1;
                    }
                }

            }

            if (attemp_Ques7 == 2)
            {
                if (Q7_Incorrect_A1 == 1)
                {
                    interactiveObj.answer7 = $("#answer7").val().split("/");
                    interactiveObj.answer7_num = parseInt(interactiveObj.answer7[0]);
                    interactiveObj.answer7_denom = parseInt(interactiveObj.answer7[1]);
                    interactiveObj.local = parseFloat(interactiveObj.answer7_num / interactiveObj.answer7_denom);

                    interactiveObj.userResponse_Q7_A2 = interactiveObj.answer7;


                    //getting hcf//
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.visible7; interactiveObj.i++)
                    {
                        if (interactiveObj.visible7 % interactiveObj.i == 0 && interactiveObj.divide7 % interactiveObj.i == 0)
                        {
                            interactiveObj.highestCommonFactor = interactiveObj.i;
                        }

                    }
                    //checking if users answer is in lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer7_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer7_num % 2 == 0 && interactiveObj.answer7_denom % 2 == 0) || (interactiveObj.answer7_num % 5 == 0 && interactiveObj.answer7_denom % 5 == 0))
                        {
                            notInLowestFormQ7 = 1;
                        }
                    }

                }
                else
                {
                    interactiveObj.answer_7_num = parseInt($("#Input_num7").val());
                    interactiveObj.answer_7_denom = parseInt($("#Input_denom7").val());
                    interactiveObj.local = parseFloat(interactiveObj.answer_7_num / interactiveObj.answer_7_denom);  // answer from user	
                    //  //alert("answer entered by user="+interactiveObj.local);

                    interactiveObj.userResponse_Q7_A2 = interactiveObj.answer_7_num + "," + interactiveObj.answer_7_denom;


                    //checking whether this answer is not lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer_7_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer_7_num % 2 == 0 && interactiveObj.answer_7_denom % 2 == 0) || (interactiveObj.answer_7_num % 5 == 0 && interactiveObj.answer_7_denom % 5 == 0))
                        {
                            notInLowestFormQ7 = 1;
                        }
                    }
                }
            }

            if (attemp_Ques7 == 3)
            {
                interactiveObj.answer_7_num = parseInt($("#Input_num7").val());
                interactiveObj.answer_7_denom = parseInt($("#Input_denom7").val());
                interactiveObj.local = parseFloat(interactiveObj.answer_7_num / interactiveObj.answer_7_denom);  // answer from user	
                //     //alert("answer entered by user="+interactiveObj.local);

                interactiveObj.userResponse_Q7_A3 = interactiveObj.answer_7_num + "," + interactiveObj.answer_7_denom;


                //checking whether this answer is not lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer_7_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer_7_num % 2 == 0 && interactiveObj.answer_7_denom % 2 == 0) || (interactiveObj.answer_7_num % 5 == 0 && interactiveObj.answer_7_denom % 5 == 0))
                    {
                        notInLowestFormQ7 = 1;
                    }
                }
            }

            html2 += '<div id="wellDone_LF" class="correct" style="top:155px"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="message7" class="correct" style="top: 162px;"><div class="sparkie"></div><div id="textCorrect1">' + replaceDynamicText(interactiveObj.correctAnswer7, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_6'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="showAns5" class="correct" style="top: 155px;"><div class="sparkie"></div><div id="textCorrect2" style="left: -44px;">' + replaceDynamicText(interactiveObj.correctAnswer7, interactiveObj.numberLanguage, "interactiveObj") + '=&nbsp;<div class="fraction" style="left: 135px;top: -7px;font-size: 18px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac"> ' + replaceDynamicText(interactiveObj.divide7, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><div class="tag2">' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="fra4" ><div class="upper"style="left:86px;">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left:90px;">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';



            html2 += '<div id="wellDone_NLF_Final" class="correct" style="top: 162px;"><div class="textCorrect_WellDone_NLF_Final4">' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction_wellDone_NLF_Final4"><div class="fraction" style="font-size:16px;top:10px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="left: 129px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            html2 += '<div id="wellDone_NLF_A" class="correct" style="top: 162px;"><div class="sparkie"></div><div id="textCorrect" style="top: 57px;left: 57px;font-size: 16px;"><div id="fra_wellDone_NLF_A"><div class="fraction" style="font-size:16px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.correctAnswer7 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="wellDone_NLF" class="correct" style="top: 162px;"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="fraction_wellDone_NLF"> <div class="fraction" style="left: 26px;font-size: 17px;"><div class="frac numerator"> ' + replaceDynamicText(interactiveObj.correctAnswer7 * 10, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(10, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            $("#prompts").html(html2);

            $("#wellDone_NLF").draggable({containment: "#container"});
            $("#wellDone_NLF_A").draggable({containment: "#container"});
            $("#wellDone_NLF_Final").draggable({containment: "#container"});
            $("#showAns5").draggable({containment: "#container"});
            $("#message7").draggable({containment: "#container"});
            $("#wellDone_LF").draggable({containment: "#container"});


        }
        if (GenerateQ6 == 4)
        {
            interactiveObj.correctAnswer7 = interactiveObj.number_4;
            interactiveObj.visible7 = parseFloat(interactiveObj.correctAnswer7 * 100).toFixed(0);
            interactiveObj.divide7 = parseInt(100);
            //answer from user//
            if (attemp_Ques7 == 1)
            {
                interactiveObj.answer7 = $("#answer7").val().split("/");
                interactiveObj.answer7_num = parseInt(interactiveObj.answer7[0]);
                interactiveObj.answer7_denom = parseInt(interactiveObj.answer7[1]);
                interactiveObj.local = parseFloat(interactiveObj.answer7_num / interactiveObj.answer7_denom);

                interactiveObj.userResponse_Q7_A1 = interactiveObj.answer6;

                //getting hcf//
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.visible7; interactiveObj.i++)
                {
                    if (interactiveObj.visible7 % interactiveObj.i == 0 && interactiveObj.divide7 % interactiveObj.i == 0)
                    {
                        interactiveObj.highestCommonFactor = interactiveObj.i;
                    }

                }
                //checking if users answer is in lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer7_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer7_num % 2 == 0 && interactiveObj.answer7_denom % 2 == 0) || (interactiveObj.answer7_num % 5 == 0 && interactiveObj.answer7_denom % 5 == 0))
                    {
                        notInLowestFormQ7 = 1;
                    }
                }

            }

            if (attemp_Ques7 == 2)
            {
                if (Q7_Incorrect_A1 == 1)
                {
                    interactiveObj.answer7 = $("#answer7").val().split("/");
                    interactiveObj.answer7_num = parseInt(interactiveObj.answer7[0]);
                    interactiveObj.answer7_denom = parseInt(interactiveObj.answer7[1]);
                    interactiveObj.local = parseFloat(interactiveObj.answer7_num / interactiveObj.answer7_denom);

                    interactiveObj.userResponse_Q7_A2 = interactiveObj.answer7;


                    //getting hcf//
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.visible7; interactiveObj.i++)
                    {
                        if (interactiveObj.visible7 % interactiveObj.i == 0 && interactiveObj.divide7 % interactiveObj.i == 0)
                        {
                            interactiveObj.highestCommonFactor = interactiveObj.i;
                        }

                    }
                    //checking if users answer is in lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer7_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer7_num % 2 == 0 && interactiveObj.answer7_denom % 2 == 0) || (interactiveObj.answer7_num % 5 == 0 && interactiveObj.answer7_denom % 5 == 0))
                        {
                            notInLowestFormQ7 = 1;
                        }
                    }

                }
                else
                {
                    interactiveObj.answer_7_num = parseInt($("#Input_num7").val());
                    interactiveObj.answer_7_denom = parseInt($("#Input_denom7").val());
                    interactiveObj.local = parseFloat(interactiveObj.answer_7_num / interactiveObj.answer_7_denom);  // answer from user	
                    //   //alert("answer entered by user="+interactiveObj.local);

                    interactiveObj.userResponse_Q7_A2 = interactiveObj.answer_7_num + "," + interactiveObj.answer_7_denom;


                    //checking whether this answer is not lowest form
                    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer_7_num; interactiveObj.i++)
                    {
                        if ((interactiveObj.answer_7_num % 2 == 0 && interactiveObj.answer_7_denom % 2 == 0) || (interactiveObj.answer_7_num % 5 == 0 && interactiveObj.answer_7_denom % 5 == 0))
                        {
                            notInLowestFormQ7 = 1;
                        }
                    }
                }
            }

            if (attemp_Ques7 == 3)
            {
                interactiveObj.answer_7_num = parseInt($("#Input_num7").val());
                interactiveObj.answer_7_denom = parseInt($("#Input_denom7").val());
                interactiveObj.local = parseFloat(interactiveObj.answer_7_num / interactiveObj.answer_7_denom);  // answer from user	
                //   //alert("answer entered by user="+interactiveObj.local);

                interactiveObj.userResponse_Q7_A3 = interactiveObj.answer_7_num + "," + interactiveObj.answer_7_denom;



                //checking whether this answer is not lowest form
                for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.answer_7_num; interactiveObj.i++)
                {
                    if ((interactiveObj.answer_7_num % 2 == 0 && interactiveObj.answer_7_denom % 2 == 0) || (interactiveObj.answer_7_num % 5 == 0 && interactiveObj.answer_7_denom % 5 == 0))
                    {
                        notInLowestFormQ7 = 1;
                    }
                }
            }

            //  html2+='<div id="wellDone_LF" class="correct"><div id="textCorrect">'+promptArr['txt_3']+'<button id="button_prompt" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">'+promptArr['text_7']+'</button></div>';

            html2 += '<div id="wellDone_LF" class="correct" style="top:150px;"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            html2 += '<div id="message7" class="correct" style="top:155px;"><div class="sparkie"></div><div id="textCorrect1" style="width: 208px;left: 37px;">' + replaceDynamicText(interactiveObj.correctAnswer7, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '.<br/>' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt" style="left: 93px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            html2 += '<div id="showAns7" class="correct" style="top:150px;"><div class="sparkie"></div><div id="textCorrect3" style="left: -44px;">' + replaceDynamicText(interactiveObj.correctAnswer7, interactiveObj.numberLanguage, "interactiveObj") + ' =&nbsp;<div class="fraction" style="left: 145px;top: -7px;font-size: 18px;"><div class="frac numerator"> ' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><div class="tag2">' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="fra4" ><div class="upper"style="left:86px;"> ' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="lower" style="left:84px;">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            html2 += '<div id="wellDone_NLF_Final" class="correct" style="top:156px;"><div class="textCorrect_WellDone_NLF_Final4">' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction_wellDone_NLF_Final4"><div class="fraction" style="font-size:16px;top:10px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<br/></div><div id="animation"></div><div id="animation2"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';



            html2 += '<div id="wellDone_NLF_A" class="correct"><div class="sparkie"></div><div id="textCorrect" style="top: 57px;left: 57px;font-size: 16px;"><div id="fra_wellDone_NLF_A" style="left: -59px;"><div class="fraction" style="font-size:16px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div class="fraction_wellDone_NLF" style="top: 58px;"> <div class="fraction" style="left: 26px;font-size: 15px;"><div class="frac numerator">' + replaceDynamicText(interactiveObj.visible7, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(100, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div>&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';


            $("#prompts").html(html2);
            $("#wellDone_NLF").draggable({containment: "#container"});
            $("#wellDone_NLF_A").draggable({containment: "#container"});
            $("#wellDone_NLF_Final").draggable({containment: "#container"});
            $("#showAns7").draggable({containment: "#container"});
            $("#message7").draggable({containment: "#container"});
            $("#wellDone_LF").draggable({containment: "#container"});

        }
    }// parent if closes question 7

    //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

    //-Comparing the answer and setting the visibilty of next question--//

    //-----------------------QUES NO 1----------------------------//

    if (interactiveObj.question_Type == "one" && interactiveObj.local == interactiveObj.correctAnswer)
    {
        if (interactiveObj.attemptQues_1 == 1)
        {


            $("#wellDone").css('visibility', 'visible').animate({'opacity': '1'}, 500);
            $(".buttonPrompt_wellDone").focus();
            $("#answer1").attr('disabled', true);

            $("#button_1").css('visibility', 'hidden');
            correctQues_1_flag = 1;
            correctQues_2_flagVisible = 1;
            //typeWiseScore="10";
            interactiveObj.scoreType1 = 10;
            typeWiseScore = +interactiveObj.scoreType1 + "|" + 0 + "|" + 0 + "|" + 0;
            interactiveObj.correct_Counter += 1;
            extraParameters = "(Q1:" + interactiveObj.number1 + ":" + interactiveObj.userResponse_Q1_A1 + ")|";
            levelWiseScore += interactiveObj.scoreType1;

        }
        if (interactiveObj.attemptQues_1 == 2)
        {
            $("#wellDone").css('visibility', 'visible');
            $(".buttonPrompt_wellDone").focus();
            $("#answer1").attr('disabled', true);
            $("#button_1").css('visibility', 'hidden');
            correctQues_1_flag = 1;
            correctQues_2_flagVisible = 1;
            interactiveObj.scoreType1 = 5;
            typeWiseScore = interactiveObj.scoreType1 + "|" + 0 + "|" + 0 + "|" + 0;
            interactiveObj.correct_Counter += 1;
            extraParameters += "|" + interactiveObj.userResponse_Q1_A2 + ")|";
            levelWiseScore += interactiveObj.scoreType1;

        }
    }
    else if (interactiveObj.question_Type == "one" && interactiveObj.local != interactiveObj.correctAnswer && interactiveObj.attemptQues_1 == 1)
    {
        Incorrect_firstAttemptQ1 = 1;
        $("#message").css('visibility', 'visible');
        $(".buttonPrompt").focus();
        $("#answer1").attr('disabled', true);
        $("#button_1").css('visibility', 'hidden');

        typeWiseScore = "0";
        extraParameters = "(Q1:" + interactiveObj.number1 + ":" + interactiveObj.userResponse_Q1_A1;
        levelWiseScore += interactiveObj.scoreType1;
    }
    else if (interactiveObj.question_Type == "one" && interactiveObj.local != interactiveObj.correctAnswer && interactiveObj.attemptQues_1 == 2)
    {
        Incorrect_secondAttemptQ1 = 1;

        $("#showAns").css('visibility', 'visible');
        $(".buttonPrompt").focus();
        $("#answer1").attr('disabled', true);
        $("#button_1").css('visibility', 'hidden');

        typeWiseScore += "|0";
        correctQues_1_flag = 1;
        correctQues_2_flagVisible = 1;
        extraParameters += "|" + interactiveObj.userResponse_Q1_A2 + ")|";
        levelWiseScore += interactiveObj.scoreType1;
    }
    //---------------------------QUES NO 1 ENDS----------------------//
    //------------------------QUES NO 2 --------------------------------//

    if (interactiveObj.question_Type == "two" && interactiveObj.local == interactiveObj.correctAnswer2)
    {
        if (interactiveObj.question_Type == "two" && interactiveObj.attemptQues_2 == 1)
        {
            ////alert(1);
            $("#prompts").css('visibility', 'visible');

            $("#wellDone2").css('visibility', 'visible');
            // $("#wellDone2").css('visibility','visible').animate({opacity:1},1000);
            $(".buttonPrompt_wellDone").focus();
            $("#answer2").attr('disabled', true);
            $("#button_2").css('visibility', 'hidden');
            correct_firstAttemptQ2 = 1;

            correctQues_1_flag = 1;
            correctQues_2_flagVisible = 1;
            correctQues_2_flag = 1;
            correctQues_3_flagVisible = 1;
            //correctQues_3_flag=1;
            interactiveObj.scoreType2 = 10;
            typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + 0 + "|" + 0;
            interactiveObj.correct_Counter += 1;
            extraParameters += "(Q2:" + interactiveObj.number2 + ":" + interactiveObj.userResponse_Q2_A1 + ")|";
            levelWiseScore += interactiveObj.scoreType2;


        }
        else if (interactiveObj.question_Type == "two" && interactiveObj.attemptQues_2 == 2)
        {
            ////alert(2);
            correct_secondAttemptQ2 = 1;

            $("#prompts").css('visibility', 'visible');

            $("#wellDone2").css('visibility', 'visible');
            $(".buttonPrompt_wellDone").focus();
            $("#answer2").attr('disabled', true);
            $("#button_2").css('visibility', 'hidden');
            correctQues_1_flag = 1;
            correctQues_2_flagVisible = 1;
            correctQues_2_flag = 1;
            correctQues_3_flagVisible = 1;
            //correctQues_3_flag=1;
            // typeWiseScore+="|5";
            interactiveObj.scoreType2 = 5;
            typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + 0 + "|" + 0;
            interactiveObj.correct_Counter += 1;
            extraParameters += "|" + interactiveObj.userResponse_Q2_A2 + ")|";
            levelWiseScore += interactiveObj.scoreType2;

        }
    }
    else if (interactiveObj.question_Type == "two" && interactiveObj.local != interactiveObj.correctAnswer2 && interactiveObj.attemptQues_2 == 1)
    {
        ////alert(3);
        Incorrect_firstAttemptQ2 = 1;

        $("#prompts").css('visibility', 'visible');

        $("#message2").css('visibility', 'visible');
        $(".buttonPrompt").focus();
        $("#answer2").attr('disabled', true);
        $("#button_2").css('visibility', 'hidden');

        extraParameters += "(Q2:" + interactiveObj.number2 + ":" + interactiveObj.userResponse_Q2_A1 + "|";
        levelWiseScore += interactiveObj.scoreType2;

    }
    else if (interactiveObj.question_Type == "two" && interactiveObj.local != interactiveObj.correctAnswer2 && interactiveObj.attemptQues_2 == 2)
    {
        ////alert(4);
        Incorrect_secondAttemptQ2 = 1;

        $("#prompts").css('visibility', 'visible');

        $("#showAns2").css('visibility', 'visible');
        $(".buttonPrompt").focus();
        $("#answer2").attr('disabled', true);
        $("#button_2").css('visibility', 'hidden');

        correctQues_1_flag = 1;
        correctQues_2_flagVisible = 1;
        correctQues_2_flag = 1;
        correctQues_3_flagVisible = 1;

        extraParameters += "|" + interactiveObj.userResponse_Q2_A1 + ")|";
        levelWiseScore += interactiveObj.scoreType2;
    }


    //------------------------QUES NO 2  ENDS--------------------------------//
    //------------------------------QUES 3--CHECKING COMPARING THE ANSWERS----------------//
    if (interactiveObj.question_Type == "three")
    {
        if (attemp_Ques3 == 1)
        {
            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                correct_firstAttempt = 1;

                if (notInLowestFormFlag == 0)
                {
                    // //alert("attempt 1  in lowest form");
                    Q3_correct_LF_A1 = 1;

                    correctQues_4_flag = 1;		//sets the visibility of next question

                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#answer3").attr('disabled', true);
                    $("#button_3").css('visibility', 'hidden');
                    //typeWiseScore+="|10";
                    interactiveObj.scoreType3 = 10;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + 0;
                    interactiveObj.correct_Counter += 1;
                    extraParameters += "(Q3:" + interactiveObj.number3 + ":" + interactiveObj.userResponse_Q3_A1 + ")|";
                    levelWiseScore += interactiveObj.scoreType3;


                }
                else
                {
                    // //alert("attempt 1  No not  in lowest form");
                    $("#wellDone_NLF").css('visibility', 'visible');
                    $(".buttonPrompt2").focus();
                    $("#answer3").attr('disabled', true);
                    $("#button_3").css('visibility', 'visible');
                    extraParameters += "(Q3:" + interactiveObj.number3 + ":" + interactiveObj.userResponse_Q3_A1 + "|";
                }
            }
            else
            {
                // if the answer is wrong at 1st attempt

                // //alert("Wrong answer");
                Incorrect_firstAttempt = 1;

                Q3_Incorrect_A1 = 1;
                extraParameters += "(Q3:" + interactiveObj.number3 + ":" + interactiveObj.userResponse_Q3_A1 + "|";
                $("#message3").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $("#answer3").attr('disabled', true);
                $("#button_3").css('visibility', 'hidden');
            }
        }
        if (attemp_Ques3 == 2)
        {
            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                correct_secondAttempt = 1;
                if (notInLowestFormFlag == 0)
                {
                    correctQues_4_flag = 1;
                    Q3_correct_LF_A2 = 1;	//sets the visibility of next question

                    // //alert("attempt 2  in lowest form");
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#answer3").attr('disabled', true);
                    $("#Input_num3").attr('disabled', true);
                    $("#Input_denom3").attr('disabled', true);
                    $("#button_3").css('visibility', 'hidden');

                    interactiveObj.scoreType3 = 5;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + 0;
                    interactiveObj.correct_Counter += 1;
                    extraParameters += interactiveObj.userResponse_Q3_A2 + ")|";
                    levelWiseScore += interactiveObj.scoreType3;

                }
                else
                {
                    //  //alert("attempt 2 not in lowest form");
                    $("#button_3").css('visibility', 'hidden');
                    $("#wellDone_NLF_A").css('visibility', 'visible');
                    $("#answer3").attr('disabled', true);
                    $(".buttonPrompt").focus();
                    extraParameters += interactiveObj.userResponse_Q3_A2 + "|";
                }
            }
            else
            {
                // if the answer is wrong at 1st attempt
                ////alert("Wrong answer 2nd time");

                Incorrect_secondAttempt = 1;
                Q3_Incorrect_A2 = 1;

                $("#message3").css('visibility', 'hidden');
                $("#showAns3").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $("#answer3").attr('disabled', true);
                $("#Input_num3").attr('disabled', true);
                $("#Input_denom3").attr('disabled', true);
                $("#button_3").css('visibility', 'hidden');
                extraParameters += interactiveObj.userResponse_Q3_A2 + "|";
            }
        }

        if (attemp_Ques3 == 3)
        {
            //alert("third attempt")
            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                correct_thirdAttempt = 1;
                if (notInLowestFormFlag == 0)
                {
                    correctQues_4_flag = 1;
                    Q3_correct_LF_A3 = 1;		//sets the visibility of next question


                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#Input_num3").attr('disabled', true);
                    $("#Input_denom3").attr('disabled', true);
                    $("#button_3").css('visibility', 'hidden');

                    interactiveObj.scoreType3 = 5;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + 0;
                    interactiveObj.correct_Counter += 1;
                    extraParameters += interactiveObj.userResponse_Q3_A3 + ")|";
                    levelWiseScore += interactiveObj.scoreType3;

                }
                else
                {
                    correctQues_4_flag = 1;
                    interactiveObj.counter = 2;
                    // $("#wellDone_NLF_A").css('visibility','hidden');
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    //$("#wellDone_NLF_Final").css('visibility','visible');
                    $(".buttonPrompt3").focus();
                    $("#Input_num3").attr('disabled', true);
                    $("#Input_denom3").attr('disabled', true);
                    $("#button_3").css('visibility', 'hidden');
                    interactiveObj.correct_Counter += 1;
                    extraParameters += interactiveObj.userResponse_Q3_A3 + ")|";
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);


                }
            }
            else
            {

                Incorrect_thirdAttempt = 1;
                correctQues_4_flag == 1;
                //alert("third attemp incorrect")
                Q3_Incorrect_A3 = 1;

                $("#wellDone_NLF_Final").css('visibility', 'visible');
                $(".buttonPrompt3").focus();
                $("#answer3").attr('disabled', true);
                $("#Input_num3").attr('disabled', true);
                $("#Input_denom3").attr('disabled', true);
                $("#button_3").css('visibility', 'hidden');

                extraParameters += interactiveObj.userResponse_Q3_A3 + ")|";
                interactiveObj.scoreType3 = 0;
                typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + 0;
                timer = setTimeout("interactiveObj.showAnimation();", 1000);
            }
        }

        if (attemp_Ques3 == 4)
        {
            if (interactiveObj.local == interactiveObj.correctAnswer3)
            {
                correct_fourthAttempt = 1;
                if (notInLowestFormFlag == 0)
                {
                    correctQues_4_flag = 1;
                    Q3_correct_LF_A4 = 1;		//sets the visibility of next question


                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#Input_num3").attr('disabled', true);
                    $("#Input_denom3").attr('disabled', true);
                    $("#button_3").css('visibility', 'hidden');

                    interactiveObj.scoreType3 = 5;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + 0;
                    interactiveObj.correct_Counter += 1;

                    extraParameters += interactiveObj.userResponse_Q3_A4 + ")|";

                }
                else
                {
                    correctQues_4_flag = 1;

                    // $("#wellDone_NLF_A").css('visibility','hidden');
                    $("#wellDone_NLF_A").css('visibility', 'hidden');
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    $("#answer3").attr('disabled', true);
                    $("#button_3").css('visibility', 'hidden');
                    interactiveObj.correct_Counter += 1;
                    extraParameters += interactiveObj.userResponse_Q3_A4 + ")";
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);

                }
            }
            else
            {

                Incorrect_fourthAttempt = 1;
                correctQues_4_flag == 1;

                Q3_Incorrect_A4 = 1;

                //   $("#message3").css('visibility','hidden');
                //   $("#showAns3").css('visibility','hidden');

                $("#wellDone_NLF_Final").css('visibility', 'visible');
                $(".buttonPrompt3").focus();
                $("#answer3").attr('disabled', true);
                $("#button_3").css('visibility', 'hidden');

                interactiveObj.scoreType3 = 0;
                typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + 0;
                extraParameters += interactiveObj.userResponse_Q3_A4 + ")";
                timer = setTimeout("interactiveObj.showAnimation();", 1000);
            }

        }


    }

    //----------------------------QUES 3 ENDS-----------------------------------//

    //-------------QUESTION 4-----CHECKING AND COMPARING USER ANSWER--------------//

    if (interactiveObj.question_Type == "four")
    {
        if (attemp_Ques4 == 1)
        {
            if (interactiveObj.local == interactiveObj.correctAnswer4)
            {
                correct_firstAttemptQ4 = 1;
                if (notInLowestFormFlagQ4 == 0)		//  correct answer is in lowest form
                {
                    // //alert("attempt 1  in lowest form");
                    Q4_correct_LF_A1 = 1;

                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#answer4").attr('disabled', true);

                    $("#button_4").css('visibility', 'hidden');
                    interactiveObj.scoreType4 = 10;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    interactiveObj.correct_Counter += 1;

                    extraParameters += "(Q4:" + interactiveObj.number4 + ":" + interactiveObj.userResponse_Q4_A1 + ")|";
                    levelWiseScore += interactiveObj.scoreType4;
                    interactiveObj.getStats();

                }
                else				// correcr answer not in lowest form
                {
                    //  //alert("attempt 1  No not  in lowest form");
                    $("#wellDone_NLF").css('visibility', 'visible');
                    $(".buttonPrompt2").focus();
                    $("#answer4").attr('disabled', true);
                    $("#button_4").css('visibility', 'hidden');
                    extraParameters += "(Q4:" + interactiveObj.number4 + ":" + interactiveObj.userResponse_Q4_A1 + "|";
                }
            }
            else
            {
                // if the answer is wrong at 1st attempt
                Q4_Incorrect_A1 = 1;
                // //alert("Wrong answer 1st attempt");
                Incorrect_firstAttemptQ4 = 1;

                $("#message4").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $("#answer4").attr('disabled', true);
                $("#button_4").css('visibility', 'hidden');
                extraParameters += "(Q4:" + interactiveObj.number4 + ":" + interactiveObj.userResponse_Q4_A1 + "|";
            }
        }
        if (attemp_Ques4 == 2)
        {
            if (interactiveObj.local == interactiveObj.correctAnswer4)
            {
                correct_secondAttemptQ4 = 1;
                if (notInLowestFormFlagQ4 == 0)
                {
                    Q4_correct_LF_A2 = 1;
                    // //alert("attempt 2  in lowest form");
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#answer4").attr('disabled', true);
                    $("#Input_num4").attr('disabled', true);
                    $("#Input_denom4").attr('disabled', true);
                    $("#button_4").css('visibility', 'hidden');
                    interactiveObj.scoreType4 = 5;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    interactiveObj.correct_Counter += 1;
                    levelWiseScore += interactiveObj.scoreType4;

                    extraParameters += interactiveObj.userResponse_Q4_A2 + ")|";
                    interactiveObj.getStats();
                }
                else
                {
                    ////alert("attempt 2 not in lowest form");
                    $("#button_4").css('visibility', 'hidden');
                    $("#wellDone_NLF_A").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer4").attr('disabled', true);
                    $("#Input_num4").attr('disabled', true);
                    $("#Input_denom4").attr('disabled', true);
                    $("#answer4").attr('disabled', true);
                    extraParameters += interactiveObj.userResponse_Q4_A2 + "|";
                }
            }
            else
            {
                // if the answer is wrong at 1st attempt
                ////alert("Wrong answer 2nd time");
                Q4_Incorrect_A2 = 1;
                Incorrect_secondAttemptQ4 = 1;

                $("#message4").css('visibility', 'hidden');
                $("#showAns4").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $("#answer4").attr('disabled', true);
                $("#Input_num4").attr('disabled', true);
                $("#Input_denom4").attr('disabled', true);
                $("#button_4").css('visibility', 'hidden');
                extraParameters += interactiveObj.userResponse_Q4_A2 + "|";
            }
        }
        if (attemp_Ques4 == 3)
        {
            if (interactiveObj.local == interactiveObj.correctAnswer4)
            {
                correct_thirdAttemptQ4 = 1;

                if (notInLowestFormFlagQ4 == 0)
                {
                    Q4_correct_LF_A3 = 1;
                    // //alert("attempt 3  in lowest form");
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt_wellDone").focus();
                    $("#answer4").attr('disabled', true);
                    $("#Input_num4").attr('disabled', true);
                    $("#Input_denom4").attr('disabled', true);
                    $("#button_4").css('visibility', 'hidden');
                    interactiveObj.scoreType4 = 5;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    interactiveObj.correct_Counter += 1;
                    extraParameters += interactiveObj.userResponse_Q4_A3 + ")|";
                    levelWiseScore += interactiveObj.scoreType4;
                    interactiveObj.getStats();
                }
                else
                {
                    // //alert("attempt 3 not in lowest form");

                    $("#wellDone_NLF_A").css('visibility', 'hidden');

                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    $("#answer4").attr('disabled', true);
                    $("#Input_num4").attr('disabled', true);
                    $("#Input_denom4").attr('disabled', true);
                    $("#button_4").css('visibility', 'hidden');
                    interactiveObj.correct_Counter += 1;
                    extraParameters += interactiveObj.userResponse_Q4_A3 + ")|";
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                }
            }
            else
            {
                // if the answer is wrong at 1st attempt
                ////alert("Wrong answer 3rd time");
                Incorrect_thirdAttemptQ4 = 1;

                Q4_Incorrect_A3 = 1;

                $("#message4").css('visibility', 'hidden');
                $("#showAns4").css('visibility', 'hidden');

                $("#wellDone_NLF_Final").css('visibility', 'visible');
                $(".buttonPrompt3").focus();
                $("#answer4").attr('disabled', true);
                $("#Input_num4").attr('disabled', true);
                $("#Input_denom4").attr('disabled', true);
                $("#button_4").css('visibility', 'hidden');
                interactiveObj.scoreType4 = 0;
                typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                extraParameters += interactiveObj.userResponse_Q4_A3 + ")|";
                timer = setTimeout("interactiveObj.showAnimation();", 1000);

                interactiveObj.getStats();
            }
        }
    }
    //-------------Q4 ENDS---------------------//
    //-------------Q5--------------------//
    /*   if(interactiveObj.question_Type=="five")*/
    {
        //attemp_Ques5+=1;  

        if (called1 == 1 || called2 == 1)  // in case generated question is of type 1 or 2
        {
            if (attemp_Ques5 == 1)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer5)
                {
                    correct_firstAttemptQ5 = 1;
                    if (notInLowestFormQ5 == 0)		//  correct answer is in lowest form
                    {
                        //  //alert("attempt 1  in lowest form");
                        Q5_correct_LF_A1 = 1;

                        typeWiseScore += "|10";

                        $("#wellDone_LF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');

                    }
                    else				// correcr answer not in lowest form
                    {
                        //  //alert("attempt 1  No not  in lowest form");
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');

                    }
                }
                else
                {
                    // if the answer is wrong at 1st attempt
                    Q5_Incorrect_A1 = 1;
                    // //alert("Wrong answer 1st attempt");

                    $("#message5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                }
            }
            if (attemp_Ques5 == 2)
            {
                ////alert("In second attempt");
                if (interactiveObj.local == interactiveObj.correctAnswer5)
                {
                    correct_secondAttemptQ5 = 1;
                    if (notInLowestFormQ5 == 0)		//  correct answer is in lowest form
                    {
                        ////alert("attempt 2  in lowest form");
                        Q5_correct_LF_A2 = 1;
                        typeWiseScore += "|5";
                        completed = 1;
                        window.clearInterval(tempController);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');

                    }
                    else				// correcr answer not in lowest form
                    {
                        // //alert("attempt 2  No not  in lowest form");
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');
                        completed = 1;
                    }
                }
                else
                {
                    // if the answer is wrong at 2nd attempt
                    Q5_Incorrect_A2 = 1;
                    //           //alert("Wrong answer 2nd attempt");
                    $("#showAns5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                    completed = 1;
                }
            }
            /*    if(attemp_Ques5==3)
             {
             
             if(interactiveObj.local==interactiveObj.correctAnswer5)
             {
             correct_thirdAttemptQ5=1;
             if(notInLowestFormQ5==0)		//  correct answer is in lowest form
             {
             //     //alert("attempt 3  in lowest form");
             Q5_correct_LF_A3=1;
             
             $("#wellDone_LF").css('visibility','visible');
             $("#button_5").css('visibility','hidden');
             
             }
             else				// correcr answer not in lowest form
             {
             //alert("attempt 3  No not  in lowest form");
             $("#wellDone_NLF").css('visibility','visible');
             $("#button_5").css('visibility','hidden');
             }
             }
             else
             {
             // if the answer is wrong at 1st attempt
             Q5_Incorrect_A3=1;
             //alert("Wrong answer 3rd attempt");
             $("#showAns5").css('visibility','visible');
             $("#button_5").css('visibility','hidden');
             }
             }																											
             */
        }

        if (called3 == 1 || called4 == 1)
        {

            if (attemp_Ques5 == 1)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer5)
                {
                    correct_firstAttemptQ5 = 1;
                    if (notInLowestFormQ5 == 0)		//  correct answer is in lowest form
                    {
                        //   //alert("attempt 1  in lowest form");
                        Q5_correct_LF_A1 = 1;

                        typeWiseScore += "|10";

                        $("#wellDone_LF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');


                    }
                    else				// correcr answer not in lowest form
                    {
                        //    //alert("attempt 1  No not  in lowest form");
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');
                    }
                }
                else
                {
                    // if the answer is wrong at 1st attempt
                    Q5_Incorrect_A1 = 1;
                    //    //alert("Wrong answer 1st attempt");
                    Incorrect_firstAttemptQ5 = 1;

                    $("#message5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                }
            }

            if (attemp_Ques5 == 2)
            {
                //    //alert("In second attempt");
                if (interactiveObj.local == interactiveObj.correctAnswer5)
                {
                    correct_secondAttemptQ5 = 1;
                    if (notInLowestFormQ5 == 0)
                    {
                        Q5_correct_LF_A2 = 1;
                        typeWiseScore += "|5";
                        //	//alert("attempt 2  in lowest form");

                        $("#wellDone_LF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');

                    }
                    else
                    {
                        //    //alert("attempt 2 not in lowest form");
                        $("#button_5").css('visibility', 'hidden');
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                    }
                }
                else
                {
                    // if the answer is wrong at 1st attempt
                    //       //alert("Wrong answer 2nd time");
                    Q5_Incorrect_A2 = 1;
                    Incorrect_secondAttemptQ4 = 1;

                    $("#message5").css('visibility', 'hidden');
                    $("#showAns5").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                }
            }

            if (attemp_Ques5 == 3)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer5)
                {
                    correct_thirdAttemptQ5 = 1;

                    if (notInLowestFormQ5 == 0)
                    {
                        Q5_correct_LF_A3 = 1;

                        typeWiseScore += "|5";
                        //     //alert("attempt 3  in lowest form");
                        $("#wellDone_LF").css('visibility', 'visible');
                        $("#button_5").css('visibility', 'hidden');
                        completed = 1;
                        window.clearInterval(tempController);

                    }
                    else
                    {
                        //     //alert("attempt 3 not in lowest form");

                        $("#wellDone_NLF_A").css('visibility', 'hidden');

                        $("#wellDone_NLF_Final").css('visibility', 'visible');

                        $("#button_5").css('visibility', 'hidden');
                        completed = 1;
                        window.clearInterval(tempController);
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }
                }
                else
                {
                    // if the answer is wrong at 1st attempt
                    //     //alert("Wrong answer 3rd time");
                    Incorrect_thirdAttemptQ5 = 1;

                    Q5_Incorrect_A3 = 1;

                    $("#message5").css('visibility', 'hidden');
                    $("#showAns5").css('visibility', 'hidden');

                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $("#button_5").css('visibility', 'hidden');
                    typeWiseScore += "|0";
                    completed = 1;
                    window.clearInterval(tempController);
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                }
            }
        }
    }
    //---------------Q5..........................//
    if (interactiveObj.question_Type == "six")
    {//parent if for question 6 starts//
        if (called1 == 1)
        {										////alert("In called 1")
            if (attemp_Ques6 == 1)
            {													////alert("In First Attempt")
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_firstAttemptQ6 = 1;

                    if (notInLowestFormQ6 == 0)
                    {										////alert("First Attempt Correct and in Lowest Form")
                        Q6_correct_LF_A1 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        Question7_Visible = 1;
                        interactiveObj.scoreType1 += 10;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        extraParameters += "(Q5:" + interactiveObj.number_1 + ":" + interactiveObj.userResponse_Q6_A1 + ")";
                        levelWiseScore += interactiveObj.scoreType1;
                    }
                    else
                    {									////alert("First Attempt Correct but not  in Lowest Form")
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += "(Q5:" + interactiveObj.number_1 + ":" + interactiveObj.userResponse_Q6_A1 + "|";
                    }
                }
                else
                {////alert("First Attempt Incorrect")
                    Incorrect_firstAttemptQ6 = 1;
                    Q6_Incorrect_A1 = 1;
                    $("#message5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');
                    extraParameters += "(Q5:" + interactiveObj.number_1 + ":" + interactiveObj.userResponse_Q6_A1 + "|";
                }
            }
            if (attemp_Ques6 == 2)
            {						////alert("In second Attempt")
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_secondAttemptQ6 = 1;
                    if (notInLowestFormQ6 == 0)
                    {					////alert("second Attempt Correct and in Lowest Form")

                        Q6_correct_LF_A2 = 1;

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        Question7_Visible = 1;
                        interactiveObj.scoreType1 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        extraParameters += interactiveObj.userResponse_Q6_A2 + ")|";
                        levelWiseScore += interactiveObj.scoreType1;

                    }
                    else
                    {					//	//alert("second Attempt Correct but not in Lowest Form")
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += interactiveObj.userResponse_Q6_A2 + ")";
                    }
                }
                else
                {
                    Q6_Incorrect_A2 = 1;
                    Incorrect_secondAttemptQ6 = 1;
                    ////alert("second Attempt IN correct");
                    $("#showAns5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');
                    Question7_Visible = 1;

                    interactiveObj.scoreType1 += 0;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    extraParameters += interactiveObj.userResponse_Q6_A2 + ")";


                }
            }
        }
        if (called2 == 1)
        {									//	//alert("In called 1")
            if (attemp_Ques6 == 1)
            {													////alert("In First Attempt")
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_firstAttemptQ6 = 1;

                    if (notInLowestFormQ6 == 0)
                    {									//	//alert("First Attempt Correct and in Lowest Form")
                        Q6_correct_LF_A1 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        Question7_Visible = 1;
                        interactiveObj.scoreType2 += 10;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;

                        interactiveObj.correct_Counter += 1;
                        extraParameters += "(Q5:" + interactiveObj.number_2 + ":" + interactiveObj.userResponse_Q6_A1 + ")|";
                        levelWiseScore += interactiveObj.scoreType2;
                    }
                    else
                    {								//	//alert("First Attempt Correct but not  in Lowest Form")
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += "(Q5:" + interactiveObj.number_2 + ":" + interactiveObj.userResponse_Q6_A1 + "|";
                    }
                }
                else
                {////alert("First Attempt Incorrect")
                    Incorrect_firstAttemptQ6 = 1;
                    Q6_Incorrect_A1 = 1;
                    $("#message5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');
                    extraParameters += "(Q5:" + interactiveObj.number_2 + ":" + interactiveObj.userResponse_Q6_A1 + "|";
                }
            }
            if (attemp_Ques6 == 2)
            {					//	//alert("In second Attempt")
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_secondAttemptQ6 = 1;
                    if (notInLowestFormQ6 == 0)
                    {					////alert("second Attempt Correct and in Lowest Form")

                        Q6_correct_LF_A2 = 1;

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        Question7_Visible = 1;
                        interactiveObj.scoreType2 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        extraParameters += interactiveObj.userResponse_Q6_A2 + ")|";
                        levelWiseScore += interactiveObj.scoreType2;

                    }
                    else
                    {						////alert("second Attempt Correct but not in Lowest Form")
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += interactiveObj.userResponse_Q6_A2 + ")|";
                    }
                }
                else
                {
                    Q6_Incorrect_A2 = 1;
                    Incorrect_secondAttemptQ6 = 1;
                    ////alert("second Attempt IN correct");
                    $("#showAns5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');
                    Question7_Visible = 1;
                    interactiveObj.scoreType2 += 0;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    extraParameters += interactiveObj.userResponse_Q6_A2 + ")|";
                }
            }
        }
        if (called3 == 1)
        {
            if (attemp_Ques6 == 1)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_firstAttemptQ6 = 1;
                    if (notInLowestFormQ6 == 0)
                    {
                        Q6_correct_LF_A1 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');

                        interactiveObj.scoreType3 += 10;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        Question7_Visible = 1; //show last question
                        extraParameters += "(Q5:" + interactiveObj.number_3 + ":" + interactiveObj.userResponse_Q6_A1 + ")|";
                        levelWiseScore += interactiveObj.scoreType3;

                    }
                    else
                    {
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                        $("#answer6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += "(Q5:" + interactiveObj.number_3 + ":" + interactiveObj.userResponse_Q6_A1 + "|";
                    }
                }
                else
                {
                    //incorrect answer

                    Q6_Incorrect_A1 = 1;
                    Incorrect_firstAttemptQ6 = 1;
                    $("#message5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');
                    extraParameters += "(Q5:" + interactiveObj.number_3 + ":" + interactiveObj.userResponse_Q6_A1 + "|";


                }
            }
            if (attemp_Ques6 == 2)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_secondAttemptQ6 = 1;
                    if (notInLowestFormQ6 == 0)
                    {
                        Q6_correct_LF_A2 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#Input_num6").attr('disabled', true);
                        $("#Input_denom6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');

                        interactiveObj.scoreType3 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        Question7_Visible = 1; //show last question
                        extraParameters += interactiveObj.userResponse_Q6_A2 + ")|";
                        levelWiseScore += interactiveObj.scoreType3;

                    }
                    else
                    {
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer6").attr('disabled', true);
                        $("#Input_num6").attr('disabled', true);
                        $("#Input_denom6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += interactiveObj.userResponse_Q6_A2 + "|";
                    }
                }
                else
                {
                    //incorrect answer second timer

                    Incorrect_secondAttemptQ6 = 1;
                    Q6_Incorrect_A2 = 1;
                    $("#showAns5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer6").attr('disabled', true);
                    $("#Input_num6").attr('disabled', true);
                    $("#Input_denom6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');

                    interactiveObj.scoreType3 += 0;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    extraParameters += interactiveObj.userResponse_Q6_A2 + "|";
                }
            }
            if (attemp_Ques6 == 3)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer6)
                {
                    correct_thirdAttemptQ6 = 1;
                    if (notInLowestFormQ6 == 0)
                    {

                        Q6_correct_LF_A3 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer6").attr('disabled', true);
                        $("#Input_num6").attr('disabled', true);
                        $("#Input_denom6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');

                        interactiveObj.scoreType3 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;

                        Question7_Visible = 1; //show last question
                        interactiveObj.correct_Counter += 1;
                        extraParameters += interactiveObj.userResponse_Q6_A3 + ")|";
                        levelWiseScore += interactiveObj.scoreType3;

                    }
                    else
                    {

                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        $("#answer6").attr('disabled', true);
                        $("#Input_num6").attr('disabled', true);
                        $("#Input_denom6").attr('disabled', true);
                        $("#button_6").css('visibility', 'hidden');
                        extraParameters += interactiveObj.userResponse_Q6_A3 + ")|";
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);

                        Question7_Visible = 1;
                    }
                }
                else
                {
                    //incorrect 3rd timer

                    Q6_Incorrect_A3 = 1;
                    Incorrect_thirdAttemptQ6 = 1;
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    $("#answer6").attr('disabled', true);
                    $("#Input_num6").attr('disabled', true);
                    $("#Input_denom6").attr('disabled', true);
                    $("#button_6").css('visibility', 'hidden');
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    Question7_Visible = 1; //show last question

                    interactiveObj.scoreType3 += 0;
                    typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                    extraParameters += interactiveObj.userResponse_Q6_A3 + ")|";
                }
            }

        }

        /*if(called4==1)
         {
         
         }	*/
    }// parent if closes question6

    //---------//---------------//	
    if (interactiveObj.question_Type == "seven")
    {
        //alert("In question 7")
        if (called21 == 1)
        {
            if (attemp_Ques7 == 1)
            {
                //alert("In First Attempt")
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_firstAttemptQ7 = 1;

                    if (notInLowestFormQ7 == 0)
                    {
                        //alert("First Attempt Correct and in Lowest Form")
                        Q7_correct_LF_A1 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);

                        $("#button_7").css('visibility', 'hidden');

                        interactiveObj.scoreType2 += 10;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        extraParameters += "(Q6:" + interactiveObj.number_2 + ":" + interactiveObj.userResponse_Q7_A1 + ")";
                        levelWiseScore += interactiveObj.scoreType2;
                        interactiveObj.getResult();
                    }
                    else
                    {
                        //alert("First Attempt Correct but not  in Lowest Form")
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');
                        extraParameters += "(Q6:" + interactiveObj.number_2 + ":" + interactiveObj.userResponse_Q7_A1 + "|";
                    }
                }
                else
                {
                    //alert("First Attempt Incorrect")
                    Incorrect_firstAttemptQ7 = 1;
                    Q7_Incorrect_A1 = 1;
                    $("#message7").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += "(Q6:" + interactiveObj.number_2 + ":" + interactiveObj.userResponse_Q7_A1 + "|";
                }
            }
            if (attemp_Ques7 == 2)
            {
                //alert("In second Attempt")
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_secondAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {
                        //alert("second Attempt Correct and in Lowest Form")

                        Q7_correct_LF_A2 = 1;
                        completed = 1;
                        window.clearInterval(tempController);
                        interactiveObj.scoreType2 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.correct_Counter += 1;
                        interactiveObj.getResult();
                        levelWiseScore += interactiveObj.scoreType2;
                        extraParameters += interactiveObj.userResponse_Q7_A2 + ")";

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);

                        $("#button_7").css('visibility', 'hidden');
			             interactiveObj.getResult();

                    }
                    else
                    {
                        //alert("second Attempt Correct but not in Lowest Form")
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer7").attr('disabled', true);

                        $("#button_7").css('visibility', 'hidden');
                        completed = 1;
                        extraParameters += interactiveObj.userResponse_Q7_A2 + ")|";
			             interactiveObj.getResult();
                    }
                }
                else
                {
                    Q7_Incorrect_A2 = 1;
                    Incorrect_secondAttemptQ7 = 1;
                    //alert("second Attempt IN correct");
                    $("#showAns7").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer7").attr('disabled', true);

                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += interactiveObj.userResponse_Q7_A2 + ")|";
                    completed = 1;
                    window.clearInterval(tempController);
		              interactiveObj.getResult();


                }
            }
        }//----if called two closes
        if (called31 == 1)
        {
            if (attemp_Ques7 == 1)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_firstAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {
                        Q7_correct_LF_A1 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');
                        interactiveObj.scoreType3 += 10;
                        levelWiseScore += interactiveObj.scoreType3;
                        extraParameters += "(Q6:" + interactiveObj.number_3 + ":" + interactiveObj.userResponse_Q7_A1 + ")";
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        interactiveObj.getResult();

                    }
                    else
                    {
                        //alert("itype 3 question answer correct but not in lowest form");
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#button_7").css('visibility', 'hidden');
                        extraParameters += "(Q6:" + interactiveObj.number_3 + ":" + interactiveObj.userResponse_Q7_A1 + "|";
                    }
                }
                else
                {
                    //incorrect answer
                    //alert("type 3 question answer INcorrect");
                    Q7_Incorrect_A1 = 1;
                    Incorrect_firstAttemptQ7 = 1;
                    $("#message7").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer7").attr('disabled', true);
                    $("#Input_num7").attr('disabled', true);
                    $("#Input_denom7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += "(Q6:" + interactiveObj.number_3 + ":" + interactiveObj.userResponse_Q7_A1 + "|";


                }
            }
            if (attemp_Ques7 == 2)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_secondAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {
                        Q7_correct_LF_A2 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');

                        interactiveObj.scoreType3 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        extraParameters += interactiveObj.userResponse_Q7_A2 + ")|";
                        interactiveObj.correct_Counter += 1;
                        levelWiseScore += interactiveObj.scoreType3;
                        interactiveObj.getResult();

                    }
                    else
                    {
                        //alert("type 3 question answrr NOT in lowest form");
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');
                        extraParameters += interactiveObj.userResponse_Q7_A2 + "|";
			
                    }
                }
                else
                {
                    //incorrect answer second timer
                    //alert("type 3 question answrr INcorrect ");
                    Incorrect_secondAttemptQ7 = 1;
                    Q7_Incorrect_A2 = 1;
                    $("#showAns5").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer7").attr('disabled', true);
                    $("#Input_num7").attr('disabled', true);
                    $("#Input_denom7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += interactiveObj.userResponse_Q7_A2 + "|";
		  
                }
            }
            if (attemp_Ques7 == 3)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_thirdAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {

                        Q7_correct_LF_A3 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');

                        interactiveObj.scoreType3 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        extraParameters += interactiveObj.userResponse_Q7_A3 + ")";
                        completed = 1;
                        window.clearInterval(tempController);
                        interactiveObj.correct_Counter += 1;
                        levelWiseScore += interactiveObj.scoreType3;
                        interactiveObj.getResult();

                    }
                    else
                    {

                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');
                        completed = 1;
                        extraParameters += interactiveObj.userResponse_Q7_A3 + ")";
                        window.clearInterval(tempController);
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
			 interactiveObj.getResult();
                    }
                }
                else
                {
                    //incorrect 3rd timer

                    Q7_Incorrect_A3 = 1;
                    Incorrect_thirdAttemptQ7 = 1;
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    $("#answer7").attr('disabled', true);
                    $("#Input_num7").attr('disabled', true);
                    $("#Input_denom7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    //called31=0;
                    extraParameters += interactiveObj.userResponse_Q7_A3 + ")";
		  
                    completed = 1;
                    window.clearInterval(tempController);
		   interactiveObj.getResult();



                }
            }

        }//----if called threeone closes
        if (called41 == 1)
        {
            if (attemp_Ques7 == 1)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_firstAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {
                        Q7_correct_LF_A1 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);

                        $("#button_7").css('visibility', 'hidden');

                        interactiveObj.scoreType4 += 10;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        extraParameters += "(Q6:" + interactiveObj.number_4 + ":" + interactiveObj.userResponse_Q7_A1 + ")";
                        interactiveObj.correct_Counter += 1;
                        levelWiseScore += interactiveObj.scoreType4;
                        interactiveObj.getResult();
                    }
                    else
                    {
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer7").attr('disabled', true);
                        $("#wellDone_NLF_A").css('top', '155px');
                        $("#button_7").css('visibility', 'hidden');
                        extraParameters += "(Q6:" + interactiveObj.number_4 + ":" + interactiveObj.userResponse_Q7_A1 + "|";
                    }
                }
                else
                {

                    Q7_Incorrect_A1 = 1;
                    Incorrect_firstAttemptQ7 = 1;
                    $("#message7").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += "(Q6:" + interactiveObj.number_4 + ":" + interactiveObj.userResponse_Q7_A1 + "|";


                }
            }
            if (attemp_Ques7 == 2)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    correct_secondAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {
                        Q7_correct_LF_A2 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');

                        interactiveObj.scoreType4 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        extraParameters += interactiveObj.userResponse_Q7_A2 + ")|";
                        interactiveObj.correct_Counter += 1;
                        levelWiseScore += interactiveObj.scoreType4;
                        interactiveObj.getResult();

                    }
                    else
                    {
                        $("#wellDone_NLF_A").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#wellDone_NLF_A").css('top', '146px');
                        $("#button_7").css('visibility', 'hidden');
                        extraParameters += interactiveObj.userResponse_Q7_A2 + "|";
                    }
                }
                else
                {
                    //incorrect answer second timer
                    //alert("type 3 question answrr INcorrect ");
                    Incorrect_secondAttemptQ7 = 1;
                    Q7_Incorrect_A2 = 1;
                    $("#showAns7").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $("#answer7").attr('disabled', true);
                    $("#Input_num7").attr('disabled', true);
                    $("#Input_denom7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += interactiveObj.userResponse_Q7_A2 + "|";
                }
            }
            if (attemp_Ques7 == 3)
            {
                if (interactiveObj.local == interactiveObj.correctAnswer7)
                {
                    //alert("third attempt");
                    correct_thirdAttemptQ7 = 1;
                    if (notInLowestFormQ7 == 0)
                    {
                        //alert("attempt 3rd in lowest form")
                        Q7_correct_LF_A3 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt_wellDone").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');

                        interactiveObj.scoreType4 += 5;
                        typeWiseScore = interactiveObj.scoreType1 + "|" + interactiveObj.scoreType2 + "|" + interactiveObj.scoreType3 + "|" + interactiveObj.scoreType4;
                        completed = 1;
                        window.clearInterval(tempController);
                        interactiveObj.correct_Counter += 1;
                        levelWiseScore += interactiveObj.scoreType4;
                        extraParameters += interactiveObj.userResponse_Q7_A3 + ")";
			 interactiveObj.getResult();

                    }
                    else
                    {
                        //alert("attempt third not in lowest form")
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        $("#answer7").attr('disabled', true);
                        $("#Input_num7").attr('disabled', true);
                        $("#Input_denom7").attr('disabled', true);
                        $("#button_7").css('visibility', 'hidden');
                        completed = 1;
                        extraParameters += interactiveObj.userResponse_Q7_A3 + ")";
                        window.clearInterval(tempController);
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
			 interactiveObj.getResult();
                    }
                }
                else
                {
                    //incorrect 3rd timer
                    //alert("incorrect third time")
                    Q7_Incorrect_A3 = 1;
                    Incorrect_thirdAttemptQ7 = 1;
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    $("#answer7").attr('disabled', true);
                    $("#Input_num7").attr('disabled', true);
                    $("#Input_denom7").attr('disabled', true);
                    $("#button_7").css('visibility', 'hidden');
                    extraParameters += interactiveObj.userResponse_Q7_A3 + ")";
                    completed = 1;
                    window.clearInterval(tempController);
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    interactiveObj.getResult();

                }
            }

        }//----if called three closes
    }
    //-------------//------------//

}

//}
questionInteractive.prototype.showAnimation = function()
{
    //alert("In show Animation");
    if (interactiveObj.question_Type == "three")
    {
        interactiveObj.text0 = interactiveObj.number3;
        interactiveObj.text1 = parseInt(interactiveObj.y * 10);
        interactiveObj.text2 = interactiveObj.text1 / interactiveObj.divisor;
        interactiveObj.text3 = parseInt(interactiveObj.number3 * 10);
        interactiveObj.text4 = 10;
        interactiveObj.text5 = 2;
        interactiveObj.text6 = interactiveObj.text4 / interactiveObj.text5;
    }
    if (interactiveObj.question_Type == "four")
    {
        interactiveObj.text0 = interactiveObj.number4;
        interactiveObj.text1 = (interactiveObj.number4 * 100).toFixed(0);
        interactiveObj.text2 = interactiveObj.text1 / interactiveObj.divisibleBy;
        interactiveObj.text3 = interactiveObj.text1;
        interactiveObj.text4 = 100;
        interactiveObj.text5 = interactiveObj.divisibleBy;
        interactiveObj.text6 = interactiveObj.text4 / interactiveObj.divisibleBy;

    }

    if (interactiveObj.question_Type == "five")
    {
        if (called3 == 1)
        {
            interactiveObj.text0 = interactiveObj.number_3;
            interactiveObj.text1 = (interactiveObj.number_3 * 10).toFixed(0);
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.divisor;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = 10;
            interactiveObj.text5 = interactiveObj.divisor;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.divisor;
        }
        if (called4 == 1)
        {
            interactiveObj.text0 = interactiveObj.number_4;
            interactiveObj.text1 = (interactiveObj.number_4 * 100).toFixed(0);
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.divisor;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = 100;
            interactiveObj.text5 = interactiveObj.divisor;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.divisor;
        }
    }

    if (interactiveObj.question_Type == "six")
    {

        if (called3 == 1)
        {
            interactiveObj.text0 = interactiveObj.number_3;
            interactiveObj.text1 = (interactiveObj.number_3 * 10).toFixed(0);
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.divisor;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = 10;
            interactiveObj.text5 = interactiveObj.divisor;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.divisor;
        }


    }

    if (interactiveObj.question_Type == "seven")
    {
        if (called31 == 1)
        {
            interactiveObj.text0 = interactiveObj.number_3;
            interactiveObj.text1 = (interactiveObj.number_3 * 10).toFixed(0);
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.divisor;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = 10;
            interactiveObj.text5 = interactiveObj.highestCommonFactor;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.highestCommonFactor;
        }
        if (called41 == 1)
        {
            interactiveObj.text0 = interactiveObj.number_4;
            interactiveObj.text1 = (interactiveObj.number_4 * 100).toFixed(0);
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.highestCommonFactor;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = parseInt(100);
            interactiveObj.text5 = interactiveObj.highestCommonFactor;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.highestCommonFactor;

        }

    }

    $("#animation").css('visibility', 'visible');

    $('#animation').append('<div id="actualNumber">' + replaceDynamicText(interactiveObj.text0, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;</div><div id="firstFraction"class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.text1, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.text4, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><div id="EqualTo" style="left: 112px;">=</div><div id="secondFraction" class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.text2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.text6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><div id="text">' + promptArr['txt_18'] + ' ' + replaceDynamicText(interactiveObj.text3, interactiveObj.numberLanguage, "interactiveObj") + ' ' + promptArr['txt_19'] + ' ' + replaceDynamicText(interactiveObj.text4, interactiveObj.numberLanguage, "interactiveObj") + ' ' + promptArr['txt_20'] + ' ' + replaceDynamicText(interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + ' </div>');

    $("#animation1").delay(100).animate({
        'opacity': '1'
    }, 500);
    $("#secondFraction").delay(7000).animate({
        'opacity': '1'
    }, 500);

    timer2 = setTimeout("interactiveObj.secondAnimation();", 1000);
}
questionInteractive.prototype.secondAnimation = function()
{

 

    $("#animation2").animate({
        'opacity': '1'
    }, 500);

    $('#animation2').append('<div id="actualNumber2">' + replaceDynamicText(interactiveObj.text0, interactiveObj.numberLanguage, "interactiveObj") + ' =</div><div id="firstFraction2" class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.text1, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.text4, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><div id="strike_upper">&#8725;<span id="smallNum">' + replaceDynamicText(interactiveObj.text2, interactiveObj.numberLanguage, "interactiveObj") + '</span></div><div id="strike_lower">&#8725;<span id="smallDenom">' + replaceDynamicText(interactiveObj.text4 / interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</span></div><div id="secondFraction2" class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.text2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.text4 / interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><div id="EqualTo_2">=</div><div id="replayButton" class="replay" onclick=interactiveObj.Replay();></div>');

    $("#firstFraction2").delay(1500).animate({
        'opacity': '1'
    }, 500);
    $("#strike_upper").delay(2500).animate({
        'opacity': '1'
    }, 500);
    $("#strike_lower").delay(3500).animate({
        'opacity': '1'
    }, 500);
    $("#assingment").delay(4500).animate({
        'opacity': '1'
    }, 500);
    $("#EqualTo_2").delay(4500).animate({
        'opacity': '1'
    }, 500);
    $("#secondFraction2").delay(5500).animate({
        'opacity': '1'
    }, 500);
    $("#replayButton").delay(6000).animate({
        'opacity': '1'
    }, 500);

}
questionInteractive.prototype.Replay = function()
{
    clearTimeout(timer2);
   $("#animation2").html("");

    interactiveObj.secondAnimation();


}
questionInteractive.prototype.getStats = function()
{
    ////alert("hello getting stats");

    AdditionalQues[0] = Incorrect_secondAttemptQ1;
    AdditionalQues[1] = Incorrect_secondAttemptQ2;
    AdditionalQues[2] = Q3_Incorrect_A3;
    AdditionalQues[3] = Q4_Incorrect_A3;

    //console.log("Final Stats=" + AdditionalQues);

    for (interactiveObj.i = 0; interactiveObj.i < 4; interactiveObj.i++)
    {
        if (AdditionalQues[interactiveObj.i] == 1)
        {
            //alert("call type=" + interactiveObj.i);
            c += 1;
            Ques[a] = parseInt(interactiveObj.i + 1);
            a++;
        }
    }
    if (c == 0)
    {
        levelWiseStatus = 1;
        completed = 1;
        extraParameters += "<-->Type Wise Score=" + "(" + typeWiseScore + ")";
        window.clearInterval(tempController);

    }
    if (c == 1)
    {
        levelWiseStatus =2;
        completed = 1;
        extraParameters += "<-->Type Wise Score=" + "(" + typeWiseScore + ")";
        window.clearInterval(tempController);
    }

    /*    if(c==1)  // when one question gets wrong
     {
     GenerateQ5=Ques[0];  
     
     Question5_Visible=1;
     if(GenerateQ5==1)
     {
     interactiveObj.randomNumber_Add_1();
     interactiveObj.fifth=interactiveObj.number_1;
     called1=1;
     }
     if(GenerateQ5==2)
     {
     interactiveObj.randomNumber_Add_2();
     interactiveObj.fifth=interactiveObj.number_2;
     called2=1;
     }
     if(GenerateQ5==3)
     {
     interactiveObj.randomNumber_Add_3();
     interactiveObj.fifth=interactiveObj.number_3;
     called3=1;
     }
     if(GenerateQ5==4)
     {
     interactiveObj.randomNumber_Add_4();
     interactiveObj.fifth=interactiveObj.number_4;
     called4=1;
     }
     }
     */
    if (c > 1)  // when two questions get wrong
    {
        Question6_Visible = 1;
        //alert("In get stats")
        GenerateQ5 = Ques[0];
        GenerateQ6 = Ques[1];
	levelWiseStatus=2;
        //console.log("Type 1 is of type " + GenerateQ5);
        //console.log("Type 2 is of type " + GenerateQ6);
        var temp = 'interactiveObj.randomNumber_Add_' + GenerateQ5 + '();';
        eval(temp);
        var temp2 = 'interactiveObj.randomNumber_Add_' + GenerateQ6 + '();';
        eval(temp2);

        if (GenerateQ5 == 1)
        {
            interactiveObj.randomNumber_Add_1();
            interactiveObj.sixth = interactiveObj.number_1;
            called1 = 1;
        }
        if (GenerateQ5 == 2)
        {
            interactiveObj.randomNumber_Add_2();
            interactiveObj.sixth = interactiveObj.number_2;
            called2 = 1;
        }
        if (GenerateQ5 == 3)
        {
            interactiveObj.randomNumber_Add_3();
            interactiveObj.sixth = interactiveObj.number_3;
            called3 = 1;
        }
        if (GenerateQ5 == 4)
        {
            interactiveObj.randomNumber_Add_4();
            interactiveObj.sixth = interactiveObj.number_4;
            called4 = 1;
        }


        if (GenerateQ6 == 1)
        {
            interactiveObj.randomNumber_Add_1();
            interactiveObj.seventh = interactiveObj.number_1;
            called11 = 1;
        }
        if (GenerateQ6 == 2)
        {
            interactiveObj.randomNumber_Add_2();
            interactiveObj.seventh = interactiveObj.number_2;
            called21 = 1;
        }
        if (GenerateQ6 == 3)
        {
            //alert("Generate Type 3")
            interactiveObj.randomNumber_Add_3();
            interactiveObj.seventh = interactiveObj.number_3;
            called31 = 1;
        }
        if (GenerateQ6 == 4)
        {
            //alert("Generate Type 4")
            interactiveObj.randomNumber_Add_4();
            interactiveObj.seventh = interactiveObj.number_4;
            called41 = 1;
        }

        //console.log("Generate Type 1=" + interactiveObj.sixth);
        //console.log("Generate Type 2=" + interactiveObj.seventh);
    }
}

questionInteractive.prototype.getResult = function()
{
    //alert("In get Result function")
    if (interactiveObj.correct_Counter == 4)
    {
        levelWiseStatus = 1;
        completed = 1;
	window.clearInterval(tempController);
        extraParameters += "<-->Type Wise Score=" + "(" + typeWiseScore + ")";
        ////alert("Pass")
    }
    else
    {
        levelWiseStatus =2;
        completed = 1;
	window.clearInterval(tempController);
        extraParameters += "<-->Type Wise Score=" + "(" + typeWiseScore + ")";
        ////alert("Fail")
    }
}



function resize()
{
    if (window.innerHeight < $("#container").height()) {
        scaleFactor = parseFloat(window.innerHeight / $("#container").height());
    }
    else if (window.innerWidth < $("#container").width()) {
        scaleFactor = parseFloat(window.innerWidth / $("#container").width());
    } else {
        scaleFactor = 1;
    }
    $("#container").css({
        "-webkit-transform": "scale(" + scaleFactor + ")"
    });
    $("#container").css({
        "-moz-transform": "scale(" + scaleFactor + ")"
    });
    $("#container").css({
        "-o-transform": "scale(" + scaleFactor + ")"
    });
    $("#container").css({
        "-ms-transform": "scale(" + scaleFactor + ")"
    });
    $("#container").css({
        "transform": "scale(" + scaleFactor + ")"
    });
}

