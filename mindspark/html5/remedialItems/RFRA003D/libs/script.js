var interactiveObj;
var completed = '';
var levelWiseTimeTaken=0;
var typeWiseScore = "0|0|0|0";	// scores to be recorded separately for all 4 types of questions. Pipe seprated.  10 points for correct at 1st attempt and 5 at 2nd attempt
var levelsAttempted = "L1";
var levelWiseStatus = '';
var extraParameters = '';
var levelWiseScore = 0;

var html;
var html2;
var arrOfType1_1 = ['1', '3', '7', '9', '11', '13', '17', '19'];   // a  1-19 not divisible by 2 or 5
var arrOfType1_2 = ['1', '3', '7', '9'];  // x  1-9 not divisible by 2 or 5

var Ques1_visible = 1;
var Ques2_visible = 0;
var Ques3_visible = 0;
var Ques4_visible = 0;

var html3;


var called1 = 0;
var called2 = 0;
var called3 = 0;
var called4 = 0;
var Ques5_visible = 0;
var Ques6_visible = 0;
var called21 = 0;
var called31 = 0;
var called41 = 0;

var x, y;
var fraction_number = createFrac(x, y);

var stats = new Array();
var stats_Final = new Array();
var generateNo = 0;
var a = 0;
var Ques = new Array();
var GenerateQ5 = 0;
var GenerateQ6 = 0;
var totaltimetaken = 0;
var tempController;

var arrOfType2_1 = new Array(); // a for type 2 question
var arrOfType2_2 = new Array();// xy for type 2 question

var arrOfType3_1 = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];  // a for type 3
var arrOfType3_2 =[ '2','4', '5', '6','8','10','30','50','70'];// xy for type 3

var arrOfType4_1 = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];  // a for type 4
var arrOfType4_2 = ['25', '50', '75'];  // xy for type 4

//---------------SECOND LEVEL VARIABLES------------//

var L2_Arr_T1_a = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];  // a for type 1 Level 2
var L2_Arr_T1_x = new Array();
var L2_Arr_T1_x1 = new Array();
var L2_Arr_T1_y = ['10', '100'];

var L2_Arr_T2_a = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
var L2_Arr_T2_x = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];

var L2_Arr_T3_a = new Array();
var L2_Arr_T3_y = ['2', '4', '5', '20', '25', '50'];
var L2_Arr_T3_x1 = ['1', '3'];
var L2_Arr_T3_x2 = ['1', '2', '3', '4'];
var L2_Arr_T3_x3 = new Array();  //elements from x>2 ND by 2 or 5
var L2_Arr_T3_x4 = new Array(); //elements from x>5 ND by 2 or 5
var L2_Arr_T3_x5 = new Array(); //elements from x>5 ND by 2 or 5
var id2;
var Ltype = 1;
var html3;

var L2_Arr_T4_a = new Array(); // 1 to 9
var L2_Arr_T4_y = ['20', '25', '40', '50', '200', '500', '250'];
var L2_Arr_T4_x1 = new Array();// when y is 200 smaller than 200 ND by 2 or 5
var L2_Arr_T4_x2 = new Array();// when y is 40 smaller than 40 ND by 2 or 5
var L2_Arr_T4_x3 = new Array();// when y is 500 smaller than 500 ND by 2 or 5
var L2_Arr_T4_x4 = new Array();// when y is 250 smaller than 250 ND by 2 or 5

var L2_AQ=new Array();
var L2_Ques=new Array();
var totaltimetaken2=1;
var tempController2=0;
var LWT;

var levelWiseTimeTakenArr=new Array(0,0); 
var time=0;

var levelWiseScoreArr=new Array(0,0);
var tempScore;
var tempUpdate;
var tempUpdate2;
var t1;
var t2;
var n1;
var n2;
var iterator;
var result;
var extraParameterArr=new Array("","");
function questionInteractive()
{
    this.attempt_Ques1 = 0;
    this.j = 0;
    this.attempt_Ques2 = 0;
    this.k = 0;
    this.attempt_Ques3 = 0;
    this.notInLowestForm = 0;
    this.Q3_C_NLF_A1 = 0;
    this.correct_Q3_A1 = 0;
    this.correct_Q3_A2 = 0;
    this.correct_Q3_A3 = 0;
    this.correct_Q3_A4 = 0;
    this.Q3_C_NLF_A2 = 0;
    this.counter = 0;
    this.Q3_C_NLF_A3 = 0;
    this.greens = 0;

    this.closeCounter1=0;
    this.closeCounter2=0;
    this.closeCounter3=0;
    this.closeCounter4=0;
    // question 4//
    this.attempt_Ques4 = 0;
    this.notInLowestForm = 0;
    this.Q4_C_NLF_A1 = 0;
    this.correct_Q4_A1 = 0;
    this.correct_Q4_A2 = 0;
    this.correct_Q4_A3 = 0;
    this.correct_Q4_A4 = 0;
    this.Q4_C_NLF_A2 = 0;
    this.counter4 = 0;
    this.Q4_C_NLF_A3 = 0;
    //question 4//

    this.status_Q1 = 0;
    this.status_Q2 = 0;
    this.status_Q3 = 0;
    this.status_Q4 = 0;
    //q5//
    this.attempt_Ques5 = 0;
    this.counter5 = 0;
    this.notInLowestForm = 0;
    this.Q5_C_NLF_A1 = 0;
    this.correct_Q5_A1 = 0;
    this.correct_Q5_A2 = 0;
    this.correct_Q5_A3 = 0;
    this.correct_Q5_A4 = 0;
    this.Q5_C_NLF_A2 = 0;
    this.Q5_C_NLF_A3 = 0;
    //q5//

    //q6//
    this.attempt_Ques6 = 0;
    this.counter6 = 0;
    this.notInLowestForm = 0;
    this.Q6_C_NLF_A1 = 0;
    this.correct_Q6_A1 = 0;
    this.correct_Q6_A2 = 0;
    this.correct_Q6_A3 = 0;
    this.correct_Q6_A4 = 0;
    this.Q6_C_NLF_A2 = 0;
    this.Q6_C_NLF_A3 = 0;
    //q6//

    this.score_T1 = 0;
    this.score_T2 = 0;
    this.score_T3 = 0;
    this.score_T4 = 0;
    this.correctCounter = 0;

    this.number1;

    //---------------SECOND LEVEL VARIABLES------------//

    this.L2_2_attempt_Q1 = 0;
    this.L2_attempt_Q1 = 0;
    this.L2_4_attempt_Q1 = 0;

    this.L2_attempt_Q1 = 0;
    this.L2_3_attempt_Q1 = 0;
    this.L2_2_attempt_Q1 = 0;
    this.sub2_attempt = 0;
    this.Additoanl_Ques1=0;
    this.Additoanl_Ques2=0;
    this.Additoanl_Ques3=0;
    this.Additoanl_Ques4=0;
    this.AdditionalQues=0;
    var Ques= new Array();
    this.L2_attempt_Q5=0;
    this.L2_attempt_Q6=0;
    this.L2_AdditonalQues5=0;
    this.L2_AdditonalQues6=0;
    this.L2_Q6_visible=0;
    this.add=0;
    this.L2_Score=0;
    this.L2_ScoreType1=0;
    this.L2_ScoreType1=parseInt(this.L2_ScoreType1);

    this.L2_ScoreType2=0;
    this.L2_ScoreType2=parseInt(this.L2_ScoreType2);

    this.L2_ScoreType3=0;
    this.L2_ScoreType3=parseInt(this.L2_ScoreType3);

    this.L2_ScoreType4=0;
    this.L2_ScoreType4=parseInt(this.L2_ScoreType4);
    this.correctCounter2=0;

    this.L1_extraParameter;
    this.L2_extraParameter;

    if (typeof getParameters['noOfLevels'] == "undefined" || parseInt(getParameters['noOfLevels']) != 2)
    {
        this.parameterNotSetFlag = 1;

        // ////////////alert("Set noOfLevels to 3");
        $("#container").html("<h2><center>Set noOfLevels to 2.</center></h2>");
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
    containerResize();

    if (interactiveObj.noOfLevels == '2')
    {
        html = "";

        interactiveObj.randomNumberGenType1();
        interactiveObj.randomNumberGenType2();
        interactiveObj.randomNumberGenType3();
        interactiveObj.randomNumberGenType4();

        tempController = window.setInterval(function() {
            //levelWiseTimeTaken = totaltimetaken++;
            time++;
            levelWiseTimeTakenArr[Ltype-1]=time;
            levelWiseTimeTaken='';
            extraParameters='';
            
            if(Ltype==2)
            {
                levelWiseScore=tempScore+"|";
            }
           
            for(var i=0;i<=(Ltype-1);i++)
            {
                levelWiseTimeTaken+=levelWiseTimeTakenArr[i]+"|";
                extraParameters+=extraParameterArr[i]+"|";
                
                    if(Ltype==2 && i>0)
                    {
                        
                      levelWiseScore+=levelWiseScoreArr[i]+"|";   
                    }
                

            }
            levelWiseTimeTaken=levelWiseTimeTaken.substr(0,levelWiseTimeTaken.length-1);
             extraParameters=extraParameters.substr(0,extraParameters.length-1);
            
                    if(Ltype==2)
                    {
                        levelWiseScore=levelWiseScore.substr(0,levelWiseScore.length-1);
                    }
            
        }, 1000);




      loadQuestions = setTimeout("interactiveObj.loadQuestions();", 1000);
     // loadSecondStage = setTimeout("interactiveObj.loadSecondStage();", 1000);

        $("input").live("keypress", function(e) {
            e.keyCode = (e.keyCode != 0) ? e.keyCode : e.which; // Mozilla hack..
            if (e.keyCode == 13) {
                ////////////alert($(this).attr('id'));
                var value = $(this).val();
                $(".correct").draggable({containment: "#container"});
                if (value == "" || value == '0') 
                {
                    
                    if(value=='0')
                    {
                      $("#enterZero").css('visibility', 'visible');   
                      $("#enterZero").draggable({containment: "#container"});  
                      $(".buttonPrompt_wellDone").focus();   
                    }    
                    else if(value== "")
                    {

                        $("#enterAnswer").css('visibility', 'visible');
                        $("#enterAnswer").draggable({containment: "#container"});
                        $(".buttonPrompt_wellDone").focus();
                        $("#box_1").attr('disabled', 'true');
                        $("#box_2").attr('disabled', 'true');
                        $("#box_3").attr('disabled', 'true');
                        $("#box_2_1").attr('disabled', 'true');
                        $("#box_2_2").attr('disabled', 'true');
                        $("#box_2_3").attr('disabled', 'true');
                        $("#box_3_1").attr('disabled', 'true');
                        $("#box_3_2").attr('disabled', 'true');
                        $("#box_3_3").attr('disabled', 'true');
                        $("#box_3_1_1").attr('disabled', 'true');
                        $("#box_3_1_2").attr('disabled', 'true');
                        $("#box_4_1").attr('disabled', 'true');
                        $("#box_4_2").attr('disabled', 'true');
                        $("#box_4_3").attr('disabled', 'true');
                        $("#box_4_1_1").attr('disabled', 'true');
                        $("#box_4_1_2").attr('disabled', 'true');
                        $("#box_5_1").attr('disabled', 'true');
                        $("#box_5_2").attr('disabled', 'true');
                        $("#box_5_3").attr('disabled', 'true');
                        $("#box_5_1_1").attr('disabled', 'true');
                        $("#box_5_1_2").attr('disabled', 'true');
                        $("#box_6_1").attr('disabled', 'true');
                        $("#box_6_2").attr('disabled', 'true');
                        $("#box_6_3").attr('disabled', 'true');
                        $("#box_6_1_1").attr('disabled', 'true');
                        $("#box_6_1_2").attr('disabled', 'true');
                    }
                }
                else
                {
                    if ($(this).attr('id') == 'box_1')
                    {
                        if ($("#box_1").val() != '')
                        {
                            $("#box_2").focus();
                        }
                        else
                        {
                            $("#box_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_2')
                    {
                        if ($("#box_2").val() != '')
                        {
                            $("#box_3").focus();
                        }
                        else
                        {
                            $("#box_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_3')
                    {
                        if ($("#box_3").val() != '')
                        {
                            interactiveObj.checkAnswer("one");
                        }
                        else
                        {
                            $("#box_3").focus();
                        }
                    }

                    if ($(this).attr('id') == 'box_2_1')
                    {
                        if ($("#box_2_1").val() != '')
                        {
                            $("#box_2_2").focus();
                        }
                        else
                        {
                            $("#box_2_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_2_2')
                    {
                        if ($("#box_2_2").val() != '')
                        {
                            $("#box_2_3").focus();
                        }
                        else
                        {
                            $("#box_2_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_2_3')
                    {
                        if ($("#box_2_3").val() != '')
                        {
                            interactiveObj.checkAnswer("two");
                        }
                        else
                        {
                            $("#box_2_3").focus();
                        }
                    }

                    if ($(this).attr('id') == 'box_3_1')  // Question 3
                    {
                        if ($("#box_3_1").val() != '')
                        {
                            $("#closeAnswer").css('visibility','visible');
                            $("#box_3_2").focus();
                        }
                        else
                        {
                            $("#box_3_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_3_2')
                    {
                        if ($("#box_3_2").val() != '')
                        {
                            $("#box_3_3").focus();
                        }
                        else
                        {
                            $("#box_3_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_3_3')
                    {
                        if ($("#box_3_3").val() != '')
                        {
                            interactiveObj.checkAnswer("three");
                        }
                        else
                        {
                            $("#box_3_3").focus();
                        }
                    }
         
                    if ($(this).attr('id') == 'box_3_1_1')
                    {
                        if ($("#box_3_1_1").val() != '')
                        {
                            $("#box_3_1_2").focus();
                        }
                        else
                        {
                            $("#box_3_1_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_3_1_2')
                    {
                        if ($("#box_3_1_2").val() != '')
                        {
                            interactiveObj.checkAnswer("three");
                        }
                        else
                        {
                            $("#box_3_1_2").focus();
                        }
                    }
                    //---------box 4----------------//
                    if ($(this).attr('id') == 'box_4_1')
                    {
                        if ($("#box_4_1").val() != '')
                        {
                            $("#box_4_2").focus();
                        }
                        else
                        {
                            $("#box_4_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_4_2')
                    {
                        if ($("#box_4_2").val() != '')
                        {
                            $("#box_4_3").focus();
                        }
                        else
                        {
                            $("#box_4_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_4_3')
                    {
                        if ($("#box_4_3").val() != '')
                        {
                            interactiveObj.checkAnswer("four");
                        }
                        else
                        {
                            $("#box_4_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_4_1_1')
                    {
                        if ($("#box_4_1_1").val() != '')
                        {
                            $("#box_4_1_2").focus();
                        }
                        else
                        {
                            $("#box_4_1_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_4_1_2')
                    {
                        if ($("#box_4_1_2").val() != '')
                        {
                            interactiveObj.checkAnswer("four");
                        }
                        else
                        {
                            $("#box_4_1_2").focus();
                        }
                    }
                    //--------box 4----------------//
                    //box 5//
                    if ($(this).attr('id') == 'box_5_1')
                    {
                        if ($("#box_5_1").val() != '')
                        {
                            $("#box_5_2").focus();
                        }
                        else
                        {
                            $("#box_5_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_5_2')
                    {
                        if ($("#box_5_2").val() != '')
                        {
                            $("#box_5_3").focus();
                        }
                        else
                        {
                            $("#box_5_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_5_3')
                    {
                        if ($("#box_5_3").val() != '')
                        {
                            interactiveObj.checkAnswer("five");
                        }
                        else
                        {
                            $("#box_5_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_5_1_1')
                    {
                        if ($("#box_5_1_1").val() != '')
                        {
                            $("#box_5_1_2").focus();
                        }
                        else
                        {
                            $("#box_5_1_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_5_1_2')
                    {
                        if ($("#box_5_1_2").val() != '')
                        {
                            interactiveObj.checkAnswer("five");
                        }
                        else
                        {
                            $("#box_5_1_2").focus();
                        }
                    }

                    //box 5//

                    //box 6//
                    if ($(this).attr('id') == 'box_6_1')
                    {
                        if ($("#box_6_1").val() != '')
                        {
                            $("#box_6_2").focus();
                        }
                        else
                        {
                            $("#box_6_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_6_2')
                    {
                        if ($("#box_6_2").val() != '')
                        {
                            $("#box_6_3").focus();
                        }
                        else
                        {
                            $("#box_6_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_6_3')
                    {
                        if ($("#box_6_3").val() != '')
                        {
                            interactiveObj.checkAnswer("six");
                        }
                        else
                        {
                            $("#box_6_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_6_1_1')
                    {
                        if ($("#box_6_1_1").val() != '')
                        {
                            $("#box_6_1_2").focus();
                        }
                        else
                        {
                            $("#box_6_1_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'box_6_1_2')
                    {
                        if ($("#box_6_1_2").val() != '')
                        {
                            interactiveObj.checkAnswer("six");
                        }
                        else
                        {
                            $("#box_6_1_2").focus();
                        }
                    }

                    //box6//

                    //level2 inputs//

                    //box 1//
                    if ($(this).attr('id') == 'L2_Q1_1')
                    {
                        if ($("#L2_Q1_1").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("one");
                        }
                        else
                        {
                            $("#L2_Q1_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q1_2')
                    {
                        if ($("#L2_Q1_2").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("one");
                        }
                        else
                        {
                            $("#L2_Q1_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q1_3')
                    {
                        if ($("#L2_Q1_3").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("one");
                        }
                        else
                        {
                            $("#L2_Q1_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q1_4')
                    {
                        if ($("#L2_Q1_4").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("one");
                        }
                        else
                        {
                            $("#L2_Q1_4").focus();
                        }
                    }

                    //box 1//
                   
                    //box 2//
                    if ($(this).attr('id') == 'L2_Q2_1')
                    {
                        if ($("#L2_Q2_1").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("two");
                        }
                        else
                        {
                            $("#L2_Q2_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q2_2')
                    {
                        if ($("#L2_Q2_2").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("two");
                        }
                        else
                        {
                            $("#L2_Q2_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q2_3')
                    {
                        if ($("#L2_Q2_3").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("two");
                        }
                        else
                        {
                            $("#L2_Q2_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q2_4')
                    {
                        if ($("#L2_Q2_4").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("two");
                        }
                        else
                        {
                            $("#L2_Q2_4").focus();
                        }
                    }
                    //box 2//

                    //box 3//
                    if ($(this).attr('id') == 'L2_Q3_1')
                    {
                        if ($("#L2_Q3_1").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("three");
                        }
                        else
                        {
                            $("#L2_Q3_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q3_2')
                    {
                        if ($("#L2_Q3_2").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("three");
                        }
                        else
                        {
                            $("#L2_Q3_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q3_3')
                    {
                        if ($("#L2_Q3_3").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("three");
                        }
                        else
                        {
                            $("#L2_Q3_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q3_5')
                    {
                        if ($("#L2_Q3_5").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("three");
                        }
                        else
                        {
                            $("#L2_Q3_5").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q3_4')
                    {
                        if ($("#L2_Q3_4").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("three");
                        }
                        else
                        {
                            $("#L2_Q3_4").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q3_6')
                    {
                        if ($("#L2_Q3_6").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("three");
                        }
                        else
                        {
                            $("#L2_Q3_5").focus();
                        }
                    }

                    //box 3

                    //box 4//
                    if ($(this).attr('id') == 'L2_Q4_1')
                    {
                        if ($("#L2_Q4_1").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("four");
                        }
                        else
                        {
                            $("#L2_Q4_1").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q4_2')
                    {
                        if ($("#L2_Q4_2").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("four");
                        }
                        else
                        {
                            $("#L2_Q4_2").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q4_3')
                    {
                        if ($("#L2_Q4_3").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("four");
                        }
                        else
                        {
                            $("#L2_Q4_3").focus();
                        }
                    }
                    if ($(this).attr('id') == 'L2_Q4_4')
                    {
                        if ($("#L2_Q4_4").val() != '')
                        {
                            interactiveObj.L2_checkAnswer("four");
                        }
                        else
                        {
                            $("#L2_Q4_4").focus();
                        }
                    }
                    //box 4//

                    //additonal question no 5//
                    if(interactiveObj.L2_AdditonalQues5==1)// additonal question boxes
                    {
                        if ($(this).attr('id') == 'L2_Q1_5_1')
                        {
                            if ($("#L2_Q1_5_1").val() != '')
                            {
                                //////////alert("not null");
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                //////////alert("null")
                                $("#L2_Q1_5_1").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_5_2')
                        {
                            if ($("#L2_Q1_5_2").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q1_5_2").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_5_3')
                        {
                            if ($("#L2_Q1_5_3").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q1_5_3").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_5_4')
                        {
                            if ($("#L2_Q1_5_4").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q1_5_4").focus();
                            }
                        }
                    }
                    if(interactiveObj.L2_AdditonalQues5==2)
                    {
                       ////////alert(2); 
                         if ($(this).attr('id') == 'L2_Q1_5_1')
                        {
                            if ($("#L2_Q1_5_1").val() != '')
                            {
                                //////////alert("not null");
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                //////////alert("null")
                                $("#L2_Q1_5_1").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_5_2')
                        {
                            if ($("#L2_Q1_5_2").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q1_5_2").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_5_3')
                        {
                            if ($("#L2_Q1_5_3").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q1_5_3").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_5_4')
                        {
                            if ($("#L2_Q1_5_4").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q1_5_4").focus();
                            }
                        }
                    }
                    if(interactiveObj.L2_AdditonalQues5==3)
                    {
                       //////////alert(3); 
                        if ($(this).attr('id') == 'L2_Q3_5_1')
                        {
                            if ($("#L2_Q3_5_1").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q3_5_1").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_5_2')
                        {
                            if ($("#L2_Q3_5_2").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q3_5_2").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_5_3')
                        {
                            if ($("#L2_Q3_5_3").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q3_5_3").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_5_5')
                        {
                            if ($("#L2_Q3_5_5").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q3_5_5").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_5_4')
                        {
                            if ($("#L2_Q3_5_4").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q3_5_4").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_5_6')
                        {
                            if ($("#L2_Q3_5_6").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("five");
                            }
                            else
                            {
                                $("#L2_Q3_5_5").focus();
                            }
                        }
                    }

                    //additonal question no5//

                    //additonal question no6//
                    if(interactiveObj.L2_AdditonalQues6 == 2)
                    {
                       ////////alert(2); 
                         if ($(this).attr('id') == 'L2_Q1_6_1')
                        {
                            if ($("#L2_Q1_6_1").val() != '')
                            {
                                //////////alert("not null");
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                //////////alert("null")
                                $("#L2_Q1_6_1").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_6_2')
                        {
                            if ($("#L2_Q1_6_2").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q1_6_2").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_6_3')
                        {
                            if ($("#L2_Q1_6_3").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q1_6_3").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q1_6_4')
                        {
                            if ($("#L2_Q1_6_4").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q1_6_4").focus();
                            }
                        }
                    }

                    if(interactiveObj.L2_AdditonalQues6 == 3)
                    {

                       //////////alert(3); 
                        if ($(this).attr('id') == 'L2_Q3_6_1')
                        {
                            if ($("#L2_Q3_6_1").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q3_6_1").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_6_2')
                        {
                            if ($("#L2_Q3_6_2").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q3_6_2").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_6_3')
                        {
                            if ($("#L2_Q3_6_3").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q3_6_3").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_6_5')
                        {
                            if ($("#L2_Q3_6_5").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q3_6_5").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_6_4')
                        {
                            if ($("#L2_Q3_6_4").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q3_6_4").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q3_6_6')
                        {
                            if ($("#L2_Q3_6_6").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q3_6_5").focus();
                            }
                        }
                    }

                    if(interactiveObj.L2_AdditonalQues6 == 4)
                    {
                        if ($(this).attr('id') == 'L2_Q4_6_1')
                        {
                            if ($("#L2_Q4_6_1").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q4_6_1").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q4_6_2')
                        {
                            if ($("#L2_Q4_6_2").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q4_6_2").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q4_6_3')
                        {
                            if ($("#L2_Q4_6_3").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q4_6_3").focus();
                            }
                        }
                        if ($(this).attr('id') == 'L2_Q4_6_4')
                        {
                            if ($("#L2_Q4_6_4").val() != '')
                            {
                                interactiveObj.L2_checkAnswer("six");
                            }
                            else
                            {
                                $("#L2_Q4_6_4").focus();
                            }
                        }
                    }
                    //additonal question no6//
                }
                return false;
            }
            else {

                if (Ltype == 1)
                {
                    if (($(this).val().length == 3 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8)
                    {
                        return false;
                    }
                }

                if (Ltype == 2)
                {
                    if (($(this).val().length == 5 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 46)
                    {
                        return false;
                    }
                    if ($(this).attr('id') == 'L2_Q1_3' || $(this).attr('id') == 'L2_Q1_4')
                    {
                        if (($(this).val().length == 5 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 46)
                        {
                            return false;
                        }
                    }
                }

            }
        });

    }
    else
    {
        $("#container").html("<h2><center>Set noOfLevels to 2</center></h2>");
    }
}
questionInteractive.prototype.randomNumberGenType1 = function()
{
    interactiveObj.number1_1 = arrOfType1_1[Math.floor(Math.random() * arrOfType1_1.length)]; // select a random element
    var temp = $.inArray(interactiveObj.number1_1, arrOfType1_1);
    arrOfType1_1.splice(temp, 1);


    interactiveObj.number1_2 = arrOfType1_2[Math.floor(Math.random() * arrOfType1_2.length)];
    var temp2 = $.inArray(interactiveObj.number1_1, arrOfType1_2);
    arrOfType1_2.splice(temp2, 1);

    //////console.log("Number 1="+interactiveObj.number1_1);
    //////console.log("Number 2="+interactiveObj.number1_2);

    interactiveObj.number1_Final = parseFloat(interactiveObj.number1_1 + "." + interactiveObj.number1_2);
    ////console.log("Number Final="+interactiveObj.number1_Final);
}
questionInteractive.prototype.randomNumber_Add_1 = function()
{
    //////////alert("Called Additoanl Type 1")
    interactiveObj.number5_1 = arrOfType1_1[Math.floor(Math.random() * arrOfType1_1.length)]; // select a random element
    interactiveObj.number5_2 = arrOfType1_2[Math.floor(Math.random() * arrOfType1_2.length)];
    interactiveObj.Additonal_number_1 = parseFloat(interactiveObj.number5_1 + "." + interactiveObj.number5_2);
    //interactiveObj.number5_Final=interactiveObj.Additonal_number_1;
}
questionInteractive.prototype.randomNumberGenType2 = function()
{
    for (interactiveObj.i = 0; interactiveObj.i < 19; interactiveObj.i++)
    {
        arrOfType2_1[interactiveObj.i] = interactiveObj.i + 1;
    }
    ////console.log("Array ="+arrOfType2_1);

    interactiveObj.number2_1 = arrOfType2_1[Math.floor(Math.random() * arrOfType2_1.length)];
    var temp3 = $.inArray(interactiveObj.number2_1, arrOfType2_1);
    arrOfType2_1.splice(temp3, 1);
    ////console.log("type 1="+interactiveObj.number2_1);

    for (interactiveObj.i = 11; interactiveObj.i <= 99; interactiveObj.i++)
    {
        if (interactiveObj.i % 2 != 0 && interactiveObj.i % 5 != 0)
        {
            arrOfType2_2[interactiveObj.j] = interactiveObj.i;
            interactiveObj.j++;
        }
    }

    interactiveObj.number2_2 = arrOfType2_2[Math.floor(Math.random() * arrOfType2_2.length)];
    var temp4 = $.inArray(interactiveObj.number2_2, arrOfType2_2);
    arrOfType2_2.splice(temp4, 1);
    ////console.log("type 2="+interactiveObj.number2_2);

    interactiveObj.number2_Final = parseFloat(interactiveObj.number2_1 + "." + interactiveObj.number2_2);
}
questionInteractive.prototype.randomNumber_Add_2 = function()
{
    //////////alert("Called Additoanl Type 2")
    interactiveObj.number5_1 = arrOfType2_1[Math.floor(Math.random() * arrOfType2_1.length)];
    interactiveObj.number5_2 = arrOfType2_2[Math.floor(Math.random() * arrOfType2_2.length)];
    interactiveObj.Additonal_number_2 = parseFloat(interactiveObj.number5_1 + "." + interactiveObj.number5_2);
    //interactiveObj.number5_Final=interactiveObj.Additonal_number_2;
}
questionInteractive.prototype.randomNumberGenType3 = function()
{
    interactiveObj.number3_1 = parseInt(arrOfType3_1[Math.floor(Math.random() * arrOfType3_1.length)]);
    var temp4 = $.inArray(interactiveObj.number3_1, arrOfType3_1);
    arrOfType3_1.splice(temp4, 1);


    interactiveObj.number3_2 = arrOfType3_2[Math.floor(Math.random() * arrOfType3_2.length)];
    var temp5 = $.inArray(interactiveObj.number3_2, arrOfType3_2);
    arrOfType3_2.splice(temp5, 1);

    //finding the highest common factor//
    interactiveObj.number3_2=parseInt(interactiveObj.number3_2);

     if(interactiveObj.number3_2>=1 && interactiveObj.number3_2<=9)
            {
                interactiveObj.modulus3 = parseInt(10);
            }
        else
            {
                interactiveObj.modulus3 = parseInt(100);
            }
   

    if(interactiveObj.number3_2>=1 && interactiveObj.number3_2<=9)
    {
        interactiveObj.number3_Final = parseFloat(interactiveObj.number3_1 + "." + interactiveObj.number3_2);

        for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.number3_2; interactiveObj.i++)
        {
            if (interactiveObj.number3_2 % interactiveObj.i == 0 && interactiveObj.modulus3 % interactiveObj.i == 0)
            {
                interactiveObj.hcf_3 = interactiveObj.i;  // highest common factor between numeratot and denominator
            }
        }
    }
    else
    {
        interactiveObj.number3_Final = (parseFloat(interactiveObj.number3_1 + "." + interactiveObj.number3_2)).toFixed(2);

        for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.number3_2; interactiveObj.i++)
        {
            if (interactiveObj.number3_2 % interactiveObj.i == 0 && interactiveObj.modulus3 % interactiveObj.i == 0)
            {
                interactiveObj.hcf_3 = interactiveObj.i;  // highest common factor between numeratot and denominator
            }
        }   
    }    
}
questionInteractive.prototype.randomNumber_Add_3 = function()
{
    //////////alert("Called Additoanl Type 3")
    interactiveObj.number5_1 = parseInt(arrOfType3_1[Math.floor(Math.random() * arrOfType3_1.length)]);
    interactiveObj.number5_2 = arrOfType3_2[Math.floor(Math.random() * arrOfType3_2.length)];

    interactiveObj.number5_2=parseInt(interactiveObj.number5_2);

       if(interactiveObj.number5_2>=1 && interactiveObj.number5_2<=9)
            {
                interactiveObj.modulus5 = parseInt(10);
            }
            else
            {
                interactiveObj.modulus5 = parseInt(100);
            }

    if(interactiveObj.number5_2>=1 && interactiveObj.number5_2<=9)
    {
        interactiveObj.Additonal_number_3 = parseFloat(interactiveObj.number5_1 + "." + interactiveObj.number5_2);
    }
    else
    {
        interactiveObj.Additonal_number_3 = (parseFloat(interactiveObj.number5_1 + "." + interactiveObj.number5_2)).toFixed(2);   
    }   

    //finding the highest common factor//

    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.number5_2; interactiveObj.i++)
    {
        if (interactiveObj.number5_2 % interactiveObj.i == 0 && interactiveObj.modulus5 % interactiveObj.i == 0)
        {
            interactiveObj.hcf_5 = interactiveObj.i;  // highest common factor between numeratot and denominator
        }
    }
}
questionInteractive.prototype.randomNumberGenType4 = function()
{
    interactiveObj.number4_1 = arrOfType4_1[Math.floor(Math.random() * arrOfType4_1.length)];
    var temp6 = $.inArray(interactiveObj.number4_1, arrOfType4_1);
    arrOfType4_1.splice(temp6, 1);


    interactiveObj.number4_2 = arrOfType4_2[Math.floor(Math.random() * arrOfType4_2.length)];
    var temp7 = $.inArray(interactiveObj.number4_1, arrOfType4_2);
    arrOfType4_2.splice(temp7, 1);
    if (interactiveObj.number4_2 == '25' || interactiveObj.number4_2 == '75')
    {
        interactiveObj.hcf_4 = 25;
    }
    else if (interactiveObj.number4_2 == '50')
    {
        interactiveObj.hcf_4 = 50;
    }


    interactiveObj.number4_Final = parseFloat(interactiveObj.number4_1 + "." + interactiveObj.number4_2).toFixed(2);
    ////console.log(interactiveObj.number4_1);
    ////console.log(interactiveObj.number4_2);
    ////console.log(interactiveObj.number4_Final);
}
questionInteractive.prototype.randomNumber_Add_4 = function()
{
    //////////alert("Called Additoanl Type 4") 
    interactiveObj.number6_1 = arrOfType4_1[Math.floor(Math.random() * arrOfType4_1.length)];
    interactiveObj.number6_2 = arrOfType4_2[Math.floor(Math.random() * arrOfType4_2.length)];
    interactiveObj.Additonal_number_4 = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2).toFixed(2);
    //interactiveObj.number5_Final=interactiveObj.Additonal_number_4;

    if (interactiveObj.number6_2 == '25' || interactiveObj.number6_2 == '75')
    {
        interactiveObj.hcf_6 = 25;
    }
    else if (interactiveObj.number6_2 == '50')
    {
        interactiveObj.hcf_6 = 50;
    }
}
questionInteractive.prototype.randomNumber6_Add_1 = function()
{
    //////////alert("Called Additoanl Type 1")
    interactiveObj.number6_1 = arrOfType1_1[Math.floor(Math.random() * arrOfType1_1.length)]; // select a random element
    interactiveObj.number6_2 = arrOfType1_2[Math.floor(Math.random() * arrOfType1_2.length)];
    interactiveObj.Additonal_number_1 = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2);
    interactiveObj.number6_Final = interactiveObj.Additonal_number_1;
}
questionInteractive.prototype.randomNumber6_Add_2 = function()
{
    //////////alert("Called Additoanl Type 2")
    interactiveObj.number6_1 = arrOfType2_1[Math.floor(Math.random() * arrOfType2_1.length)];
    interactiveObj.number6_2 = arrOfType2_2[Math.floor(Math.random() * arrOfType2_2.length)];
    interactiveObj.Additonal_number_2 = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2);
    interactiveObj.number6_Final = interactiveObj.Additonal_number_2;
}
questionInteractive.prototype.randomNumber6_Add_3 = function()
{
    //////////alert("Called Additoanl Type 3")
    interactiveObj.number6_1 = parseInt(arrOfType3_1[Math.floor(Math.random() * arrOfType3_1.length)]);
    interactiveObj.number6_2 = arrOfType3_2[Math.floor(Math.random() * arrOfType3_2.length)];

   // interactiveObj.Additonal_number_3 = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2);
   // interactiveObj.number6_Final = interactiveObj.Additonal_number_3;

    //finding the highest common factor//
           if(interactiveObj.number6_2>=1 && interactiveObj.number6_2<=9)
            {
                interactiveObj.modulus6 = parseInt(10);
            }
            else
            {
                interactiveObj.modulus6 = parseInt(100);
            }


    for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.number6_2; interactiveObj.i++)
    {
        if (interactiveObj.number6_2 % interactiveObj.i == 0 && interactiveObj.modulus6 % interactiveObj.i == 0)
        {
            interactiveObj.hcf_6 = interactiveObj.i;  // highest common factor between numeratot and denominator
        }
    }

  

    if(interactiveObj.number6_2>=1 && interactiveObj.number6_2<=9)
    {
        interactiveObj.number6_Final = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2);
        interactiveObj.Additonal_number_3 = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2);

        for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.number6_2; interactiveObj.i++)
        {
            if (interactiveObj.number6_2 % interactiveObj.i == 0 && interactiveObj.modulus6 % interactiveObj.i == 0)
            {
                interactiveObj.hcf_6 = interactiveObj.i;  // highest common factor between numeratot and denominator
            }
        }
    }
    else
    {
        interactiveObj.number6_Final = (parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2)).toFixed(2);
        interactiveObj.Additonal_number_3 = (parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2)).toFixed(2);

        for (interactiveObj.i = 0; interactiveObj.i <= interactiveObj.number6_2; interactiveObj.i++)
        {
            if (interactiveObj.number6_2 % interactiveObj.i == 0 && interactiveObj.modulus6 % interactiveObj.i == 0)
            {
                interactiveObj.hcf_6 = interactiveObj.i;  // highest common factor between numeratot and denominator
            }
        }   
    }  
}
questionInteractive.prototype.randomNumber6_Add_4 = function()
{
    //////////alert("Called Additoanl Type 4") 
    interactiveObj.number6_1 = arrOfType4_1[Math.floor(Math.random() * arrOfType4_1.length)];
    interactiveObj.number6_2 = arrOfType4_2[Math.floor(Math.random() * arrOfType4_2.length)];
    interactiveObj.Additonal_number_4 = parseFloat(interactiveObj.number6_1 + "." + interactiveObj.number6_2).toFixed(2);
    interactiveObj.number6_Final = interactiveObj.Additonal_number_4;

    if (interactiveObj.number6_2 == '25' || interactiveObj.number6_2 == '75')
    {
        interactiveObj.hcf_6 = 25;
    }
    else if (interactiveObj.number6_2 == '50')
    {
        interactiveObj.hcf_6 = 50;
    }
}
questionInteractive.prototype.loadQuestions = function()
{


    html = "";

    html = '<div id="background">';
    html += '<div id="header">'+replaceDynamicText(promptArr['text_1'],interactiveObj.numberLanguage,"interactiveObj")+'</div>';
    html += '<div id="firstQuestion">';
    html += '<div class="Question">' + replaceDynamicText(interactiveObj.number1_Final,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp</div>';
    html += '<div id="answerBox">';
    html += '<div class="boxFirst"><input  id="box_1" class="Input_Box" autofocus=autofocus></input></div>';
    html += '<div id="mixedFraction"><div class="fraction"><div class="frac numerator"><input id="box_2" class="Input_Box"></input></div><div class="frac"><input id="box_3" class="Input_Box"></input></div></div></div>';
    html += '</div>';
    html += '</div>';



    html += '<div id="secondQuestion">';
    html += '<div class="Question">' + replaceDynamicText(interactiveObj.number2_Final,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp</div>';
    html += '<div id="answerBox2">';
    html += '<div class="boxFirst"><input  id="box_2_1" class="Input_Box" style="display:inline-block;"></input></div>';
    html += '<div id="mixedFraction"><div class="fraction"><div class="frac numerator"><input id="box_2_2" class="Input_Box"></input></div><div class="frac"><input id="box_2_3" class="Input_Box"></input></div></div></div>';
    html += '</div>';
    html += '</div>';

    html += '<div id="thirdQuestion">';
    html += '<div class="Question">' + replaceDynamicText(interactiveObj.number3_Final,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp</div>';
    html += '<div id="answerBox3">';
    html += '<div class="boxFirst"><input  id="box_3_1" class="Input_Box" autofocus=autofocus></input></div>';
    html += '<div id="mixedFraction"><div class="fraction"><div class="frac numerator"><input id="box_3_2" class="Input_Box"></input></div><div class="frac"><input id="box_3_3" class="Input_Box"></input></div></div></div>';
    html += '</div>';
    html += '</div>';

    html += '<div id="fourthQuestion">';
    html += '<div class="Question">' + replaceDynamicText(interactiveObj.number4_Final,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp</div>';
    html += '<div id="answerBox4">';
    html += '<div class="boxFirst"><input  id="box_4_1" class="Input_Box" autofocus=autofocus></input></div>';
    html += '<div id="mixedFraction"><div class="fraction"><div class="frac numerator"><input id="box_4_2" class="Input_Box"></input></div><div class="frac"><input id="box_4_3" class="Input_Box"></input></div></div></div>';
    html += '</div>';
    html += '</div>';


    html += '<div id="fifthQuestion">';
    html += '<div class="Question">' + replaceDynamicText(interactiveObj.number5_Final,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp</div>';
    html += '<div id="answerBox5">';
    html += '<div class="boxFirst"><input  id="box_5_1" class="Input_Box" autofocus=autofocus></input></div>';
    html += '<div id="mixedFraction"><div class="fraction"><div class="frac numerator"><input id="box_5_2" class="Input_Box"></input></div><div class="frac"><input id="box_5_3" class="Input_Box"></input></div></div></div>';
    html += '</div>';
    html += '</div>';

  

    html += '<div id="sixthQuestion">';
    html += '<div class="Question">' + replaceDynamicText(interactiveObj.number6_Final,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp</div>';
    html += '<div id="answerBox6">';
    html += '<div class="boxFirst"><input  id="box_6_1" class="Input_Box" autofocus=autofocus></input></div>';
    html += '<div id="mixedFraction"><div class="fraction"><div class="frac numerator"><input id="box_6_2" class="Input_Box"></input></div><div class="frac"><input id="box_6_3" class="Input_Box"></input></div></div></div>';
    html += '</div>';
    html += '</div>';
    

    html += '</div>';
    html += '<div id="prompts"></div>';
    html += '<button id="next_Level" type="button" name="" value="" class="css3button" onclick=interactiveObj.loadSecondStage();>Proceed</button>';
    html += '<div id="enterAnswer" style="top: 250px;left: 298px;" class="correct"><div class="sparkie"></div><div id="blankEnter">' + replaceDynamicText(promptArr['txt_21'],interactiveObj.numberLanguage,"interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone" style="top: 86px;left: 158px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
    html += '<div id="enterZero" style="top: 250px;left: 298px;" class="correct"><div class="sparkie"></div><div id="blankEnter">' + replaceDynamicText(promptArr['txt_38'],interactiveObj.numberLanguage,"interactiveObj") + '</div><button onclick=interactiveObj.loadQuestions(); class="buttonPrompt_wellDone" style="top: 114px;left: 158px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
  

    $("#container").html(html);



    if (Ques1_visible == '1')
    {
        if (interactiveObj.correct_Ques1_attempt1 == 1)
        {
            $("#answerBox").html("");
            $("#answerBox").css('width', '71px');
            $("#answerBox").css('height', '58px');
            $("#answerBox").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number1_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3" style="left: 32px;">' + replaceDynamicText(interactiveObj.modulus,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox").css('border-radius', '11px');

            $("#answerBox").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox").css('box-shadow', '0 0 20px green');

            Ques2_visible = 1;
        }
        if (interactiveObj.correct_Ques1_attempt2 == 1)
        {
            $("#answerBox").html("");
            $("#answerBox").css('width', '71px');
            $("#answerBox").css('height', '58px');
            $("#answerBox").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number1_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3" style="left: 32px;">' + replaceDynamicText(interactiveObj.modulus,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox").css('border-radius', '11px');

            $("#answerBox").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox").css('box-shadow', '0 0 20px green');


            Ques2_visible = 1;
        }
        if (interactiveObj.correct_Ques1_attempt3 == 1)
        {
            $("#answerBox").html("");
            $("#answerBox").css('width', '71px');
            $("#answerBox").css('height', '58px');
            $("#answerBox").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number1_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3"style="left: 32px;">' + replaceDynamicText(interactiveObj.modulus,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox").css('border-radius', '11px');

            $("#answerBox").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox").css('box-shadow', '0 0 20px green');


            Ques2_visible = 1;
        }
        if (interactiveObj.incorrect_Ques1_attempt3 == 1)
        {
            $("#answerBox").html("");
            $("#answerBox").css('width', '71px');
            $("#answerBox").css('height', '58px');
            $("#answerBox").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number1_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3"style="left: 32px;">' + replaceDynamicText(interactiveObj.modulus,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox").css('border-radius', '11px');

            $("#answerBox").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox").css('box-shadow', '0 0 20px blue');
            Ques2_visible = 1;
        }
    }
    if (Ques2_visible == '1')
    {
        $("#secondQuestion").css('visibility', 'visible');
        $("#box_2_1").focus();


        if (interactiveObj.correct_Ques2_attempt1 == 1)
        {
            $("#answerBox2").html("");
            $("#answerBox2").css('width', '71px');
            $("#answerBox2").css('height', '58px');
            //$("#answerBox2").append('<div id="second_Answer"><div class="number1">'+interactiveObj.number2_1+'</div><div class="number2" style="width:30px;>'+interactiveObj.number2_2+'</div><div class="number3">'+interactiveObj.modulus2+'</div></div>');

            $("#answerBox2").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number2_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus2,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox2").css('border-radius', '11px');

            $("#answerBox2").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox2").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox2").css('box-shadow', '0 0 20px green');


            Ques3_visible = 1;

        }
        if (interactiveObj.correct_Ques2_attempt2 == 1)
        {
            $("#answerBox2").html("");
            $("#answerBox2").css('width', '71px');
            $("#answerBox2").css('height', '58px');
            $("#answerBox2").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number2_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' +replaceDynamicText(interactiveObj.number2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus2,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');


            $("#answerBox2").css('border-radius', '11px');

            $("#answerBox2").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox2").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox2").css('box-shadow', '0 0 20px green');


            Ques3_visible = 1;
        }
        if (interactiveObj.correct_Ques2_attempt3 == 1)
        {
            $("#answerBox2").html("");
            $("#answerBox2").css('width', '71px');
            $("#answerBox2").css('height', '58px');
            $("#answerBox2").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number2_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus2,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox2").css('border-radius', '11px');

            $("#answerBox2").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox2").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox2").css('box-shadow', '0 0 20px green');

            Ques3_visible = 1;
        }
        if (interactiveObj.incorrect_Ques2_attempt3 == 1)
        {
            $("#answerBox2").html("");
            $("#answerBox2").css('width', '71px');
            $("#answerBox2").css('height', '58px');
            $("#answerBox2").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number2_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus2,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

            $("#answerBox2").css('border-radius', '11px');

            $("#answerBox2").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox2").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox2").css('box-shadow', '0 0 20px blue');
            Ques3_visible = 1;
        }
    }
    if (Ques3_visible == '1')
    {
        $("#thirdQuestion").css('visibility', 'visible');
        $("#box_3_1").focus();

        if (interactiveObj.attempt_Ques3 == 1 && interactiveObj.Q3_C_NLF_A1 == 1)
        {
            // attempt 1 correct but not in lowest form
            $("#box_3_1").attr('disabled', true);
            $("#box_3_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_3_2").attr('disabled', true);
            $("#box_3_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_3_3").attr('disabled', true);
            $("#box_3_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj")+ '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + changeLanguage(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box"></input></div></div></div></div>')

            $("#box_3_1_1").focus();

        }
        if (interactiveObj.attempt_Ques3 == 1 && interactiveObj.Q3_C_LF_A1 == 1)
        {//attempt 1 correct and in lowest form
            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>')

            $("#answerBox3").css('border-radius', '11px');
            $("#answerBox3").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox3").css('box-shadow', '0 0 20px green');

            Ques4_visible = 1;

        }
        if (interactiveObj.attempt_Ques3 == 1 && interactiveObj.incorrect_Q3_A1 == 1)
        {
            // attempt 1 is incorrect
        }
        if (interactiveObj.attempt_Ques3 == 2 && interactiveObj.Q3_C_LF_A2 == 1)
        {
            // attempt 2 correct and in lowest form
            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#answerBox3").css('border-radius', '11px');

            $("#answerBox3").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox3").css('box-shadow', '0 0 20px green');

            Ques4_visible = 1;
        }
        if (interactiveObj.attempt_Ques3 == 2 && interactiveObj.Q3_C2_C_LF_A2 == 1)
        {
            // attempt 2 correct and in lowest form
            //////////alert("A2Case 2");

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj")+ '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);

            $("#thirdQuestion").css('height', '130px');

        }
        if (interactiveObj.attempt_Ques3 == 2 && interactiveObj.Q3_C_NLF_A2 == 1)
        {
            $("#box_3_1").attr('disabled', true);
            $("#box_3_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_3_2").attr('disabled', true);
            $("#box_3_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_3_3").attr('disabled', true);
            $("#box_3_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' +replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' +replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box"></input></div></div></div></div>');

            $("#box_3_1_1").focus();
            $("#thirdQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques3 == 2 && interactiveObj.Q3_C2_C_NLF_A2 == 1)
        {
            //////////alert("A2Case22")
            $("#box_3_1").attr('disabled', true);
            $("#box_3_1").attr('placeholder', interactiveObj.box_1);

            $("#box_3_2").attr('disabled', true);
            $("#box_3_2").attr('placeholder', interactiveObj.box_2);

            $("#box_3_3").attr('disabled', true);
            $("#box_3_3").attr('placeholder', interactiveObj.box_3);

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj")+ '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box"></input></div></div></div></div>');

            $("#box_3_1_1").focus();
            $("#thirdQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques3 == 2 && interactiveObj.incorrect_Q3_A2 == 1 && interactiveObj.counter == 0)
        {
            $("#box_3_1").attr('disabled', true);
            $("#box_3_1").attr('placeholder', interactiveObj.box_1);

            $("#box_3_2").attr('disabled', true);
            $("#box_3_2").attr('placeholder', interactiveObj.box_2);

            $("#box_3_3").attr('disabled', true);
            $("#box_3_3").attr('placeholder', interactiveObj.box_3);

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box"></input></div></div></div></div>')

            $("#box_3_1_1").focus();
            $("#thirdQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques3 == 2 && interactiveObj.incorrect_Q3_A2 == 1 && interactiveObj.counter == 1)
        {
            $("#box_3_1").attr('disabled', false);
            $("#box_3_2").attr('disabled', false);
            $("#box_3_3").attr('disabled', false);
            $("#box_3_1").focus();

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            $("#thirdQuestion").css('height', '130px');

        }
        if (interactiveObj.attempt_Ques3 == 3 && interactiveObj.Q3_C_LF_A3 == 1)
        {

            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div></div></div></div>');

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") / replaceDynamicText(interactiveObj.hcf_3,interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            $("#answerBox3").css('border-radius', '11px');

            $("#answerBox3").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox3").css('box-shadow', '0 0 20px green');

            $("#thirdQuestion").css('height', '130px');

            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

            Ques4_visible = 1;
        }
        if (interactiveObj.attempt_Ques3 == 3 && interactiveObj.Q3_C_NLF_A3 == 1)
        {
            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number3_2/interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus3/interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            $("#answerBox3").css('border-radius', '11px');

            $("#answerBox3").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox3").css('box-shadow', '0 0 20px blue');

            $("#thirdQuestion").css('height', '130px');
            Ques4_visible = 1;
        }
        if (interactiveObj.attempt_Ques3 == 3 && interactiveObj.incorrect_Q3_A3 == 1)
        {
            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' +replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            $("#answerBox3").css('border-radius', '11px');

            $("#answerBox3").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox3").css('box-shadow', '0 0 20px blue');

            $("#thirdQuestion").css('height', '130px');

            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

            Ques4_visible = 1;
        }
        if (interactiveObj.attempt_Ques3 == 3 && interactiveObj.C2_Q3_C_NLF_A3 == 1)
        {
            //////////alert("Case 2");

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);

            $("#thirdQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques3 == 3 && interactiveObj.C2_Q3_C_LF_A3 == 1)
        {
            //////////alert("Case 22");

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            $("#thirdQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques3 == 3 && interactiveObj.incorrect_C2_Q3_A3 == 1)
        {
            //////////alert("Case222");

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            $("#thirdQuestion").css('height', '130px');
        }
        if ((interactiveObj.attempt_Ques3 == 4) && (interactiveObj.Q3_C_NLF_A4 == 1 || interactiveObj.Q3_C_LF_A4 == 1))
        {
            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#thirdQuestion").css('height', '130px');
            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);
            Ques4_visible = 1;
            if (interactiveObj.Q3_C_NLF_A4 == 1)
            {
                $("#answerBox3").css('border-radius', '11px');

                $("#answerBox3").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox3").css('box-shadow', '0 0 20px blue');

            }
            else
            {
                $("#answerBox3").css('border-radius', '11px');

                $("#answerBox3").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox3").css('box-shadow', '0 0 20px green');

            }
            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');
        }
        if (interactiveObj.attempt_Ques3 == 4 && interactiveObj.incorrect_Q3_A4 == 1)
        {
            //red color box
            $("#answerBox3").html("");
            $("#answerBox3").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number3_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#thirdQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number3_2 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_3_1_1").attr('disabled', true);
            $("#box_3_1_2").attr('disabled', true);

            $("#answerBox3").css('border-radius', '11px');

            $("#answerBox3").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox3").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox3").css('box-shadow', '0 0 20px blue');
            $("#thirdQuestion").css('height', '130px');
            Ques4_visible = 1;
            //-------//
            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');
        }
    }
    if (Ques4_visible == '1')
    {
        $("#fourthQuestion").css('visibility', 'visible');
        $("#box_4_1").focus();

        if (interactiveObj.attempt_Ques4 == 1 && interactiveObj.Q4_C_NLF_A1 == 1)   //box
        {
            // attempt 1 correct but not in lowest form
            $("#box_4_1").attr('disabled', true);
            $("#box_4_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_2").attr('disabled', true);
            $("#box_4_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_3").attr('disabled', true);
            $("#box_4_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box"></input></div></div></div></div>')

            $("#box_4_1_1").focus();
        }
        if (interactiveObj.attempt_Ques4 == 1 && interactiveObj.Q4_C_LF_A1 == 1)
        {
            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>')

            $("#answerBox4").css('border-radius', '11px');

            $("#answerBox4").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox4").css('box-shadow', '0 0 20px green');

            if(generateNo==0 || generateNo==1)
             {
             $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
             }
            //interactiveObj.getStats();
        }
        if (interactiveObj.attempt_Ques4 == 1 && interactiveObj.incorrect_Q4_A1 == 1)
        {
            // attempt 1 is incorrect
        }
        if (interactiveObj.attempt_Ques4 == 2 && interactiveObj.Q4_C_LF_A2 == 1)
        {
            // attempt 2 correct and in lowest form
            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');


            /*$("#thirdQuestion").append('<div id="sub_Fraction">0.'+interactiveObj.number3_2+'&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">'+interactiveObj.number3_2+'</div><div class="frac">'+interactiveObj.modulus3+'</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder='+interactiveObj.number3_2/interactiveObj.hcf_3+'></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder='+interactiveObj.modulus3/interactiveObj.hcf_3+'></input></div></div></div></div>')	
             
             $("#box_3_1_1").attr('disabled',true);
             $("#box_3_1_2").attr('disabled',true);*/
            $("#answerBox4").css('border-radius', '11px');

            $("#answerBox4").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox4").css('box-shadow', '0 0 20px green');

            if(generateNo==0 || generateNo==1)
             {
             $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
             }
        }
        if (interactiveObj.attempt_Ques4 == 2 && interactiveObj.Q4_C2_C_LF_A2 == 1)
        {
            // attempt 2 correct and in lowest form
            //////////alert("A2Case 2");

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj")+ '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
        }
        if (interactiveObj.attempt_Ques4 == 2 && interactiveObj.Q4_C_NLF_A2 == 1)
        {
            $("#box_4_1").attr('disabled', true);
            $("#box_4_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_2").attr('disabled', true);
            $("#box_4_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_3").attr('disabled', true);
            $("#box_4_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box"></input></div></div></div></div>');

            $("#box_4_1_1").focus();
            $("#fourthQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques4 == 2 && interactiveObj.Q4_C2_C_NLF_A2 == 1)
        {
            //////////alert("A2Case22")
            $("#box_4_1").attr('disabled', true);
            $("#box_4_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_2").attr('disabled', true);
            $("#box_4_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_3").attr('disabled', true);
            $("#box_4_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box"></input></div></div></div></div>');

            $("#box_4_1_1").focus();
            $("#fourthQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques4 == 2 && interactiveObj.incorrect_Q4_A2 == 1 && interactiveObj.counter4 == 0)
        {
            $("#box_4_1").attr('disabled', true);
            $("#box_4_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_2").attr('disabled', true);
            $("#box_4_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

            $("#box_4_3").attr('disabled', true);
            $("#box_4_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box"></input></div></div></div></div>')

            $("#box_4_1_1").focus();
            $("#fourthQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques4 == 2 && interactiveObj.incorrect_Q4_A2 == 1 && interactiveObj.counter4 == 1)
        {
            $("#box_4_1").attr('disabled', false);
            $("#box_4_2").attr('disabled', false);
            $("#box_4_3").attr('disabled', false);
            $("#box_4_1").focus();

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
            $("#fourthQuestion").css('height', '130px');

        }
        if (interactiveObj.attempt_Ques4 == 3 && interactiveObj.Q4_C_LF_A3 == 1)
        {

            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
            $("#answerBox4").css('border-radius', '11px');

            $("#answerBox4").css('-webkit-box-shadow', '0 0 20px green');
            $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px green');
            $("#answerBox4").css('box-shadow', '0 0 20px green');

            $("#fourthQuestion").css('height', '130px');

            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

            if(generateNo==0  || generateNo==1)
             {
             $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
             }
            //interactiveObj.getStats();

        }
        if (interactiveObj.attempt_Ques4 == 3 && interactiveObj.Q4_C_NLF_A3 == 1)
        {
            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
            $("#answerBox4").css('border-radius', '11px');

            $("#answerBox4").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox4").css('box-shadow', '0 0 20px blue');

            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

            $("#fourthQuestion").css('height', '130px');
            if(generateNo==0  || generateNo==1)
             {
             $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
             }
            //interactiveObj.getStats();

        }
        if (interactiveObj.attempt_Ques4 == 3 && interactiveObj.incorrect_Q4_A3 == 1)
        {
            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
            $("#answerBox4").css('border-radius', '11px');

            $("#answerBox4").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox4").css('box-shadow', '0 0 20px blue');

            $("#fourthQuestion").css('height', '130px');

            $("#sub_fraction_inputBox").css('border-radius', '4px');
            $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
            $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
            $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');
            if(generateNo==0  || generateNo==1)
             {
             $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
             }
            //interactiveObj.getStats();

        }
        if (interactiveObj.attempt_Ques4 == 3 && interactiveObj.C2_Q4_C_NLF_A3 == 1)
        {
            //////////alert("Case 2");

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);

            $("#fourthQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques4 == 3 && interactiveObj.C2_Q4_C_LF_A3 == 1)
        {
            //////////alert("Case 22");

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
            $("#fourthQuestion").css('height', '130px');
        }
        if (interactiveObj.attempt_Ques4 == 3 && interactiveObj.incorrect_C2_Q4_A3 == 1)
        {
            //////////alert("Case222");

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);
            $("#fourthQuestion").css('height', '130px');
        }
        if ((interactiveObj.attempt_Ques4 == 4) && (interactiveObj.Q4_C_NLF_A4 == 1 || interactiveObj.Q4_C_LF_A4 == 1))
        {
            //////////alert("this");
            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number4_2 / interactiveObj.hcf_4 + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox" class="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')




            $("#fourthQuestion").css('height', '130px');
            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);


            if (interactiveObj.Q4_C_NLF_A4 == 1)
            {
                //////////alert("this_1");
                $("#answerBox4").css('border-radius', '11px');

                $("#answerBox4").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox4").css('box-shadow', '0 0 20px blue');

                if(generateNo==0)
                 {
                 $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                 }
                //interactiveObj.getStats();
            }
            else
            {
                //////////alert("this_2");
                $("#answerBox4").css('border-radius', '11px');

                $("#answerBox4").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox4").css('box-shadow', '0 0 20px green');

                if(generateNo==0)
                 {
                 $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                 }
            }

            $(".sub_fraction_inputBox").css('border-radius', '11px');
            $(".sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 20px blue');
            $(".sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 20px blue');
            $(".sub_fraction_inputBox").css('box-shadow', '0 0 20px blue');

        }
        if (interactiveObj.attempt_Ques4 == 4 && interactiveObj.incorrect_Q4_A4 == 1)
        {
            //red color box
            //////////alert("that");
            $("#answerBox4").html("");
            $("#answerBox4").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number4_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number4_2 / interactiveObj.hcf_4 + '</div><div class="frac">' + interactiveObj.modulus4 / interactiveObj.hcf_4 + '</div></div></div></div></div>');

            $("#fourthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox" class="sub_fraction_inputBox2"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_4_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number4_2 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_4_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus4 / interactiveObj.hcf_4),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

            $("#box_4_1_1").attr('disabled', true);
            $("#box_4_1_2").attr('disabled', true);

            $("#answerBox4").css('border-radius', '11px');

            $("#answerBox4").css('-webkit-box-shadow', '0 0 20px blue');
            $("#answerBox4").css('-moz-box-shadow', ' 0 0 20px blue');
            $("#answerBox4").css('box-shadow', '0 0 20px blue');
            $("#fourthQuestion").css('height', '130px');

            $(".sub_fraction_inputBox2").css('border-radius', '11px');
            $(".sub_fraction_inputBox2").css('-webkit-box-shadow', '0 0 20px blue');
            $(".sub_fraction_inputBox2").css('-moz-box-shadow', ' 0 0 20px blue');
            $(".sub_fraction_inputBox2").css('box-shadow', '0 0 20px blue');
            
            if(generateNo==0  || generateNo==1)
             {
             $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
             }
            //interactiveObj.getStats();

        }
    }
    if (Ques5_visible == '1')
    {

        $("#fifthQuestion").css('visibility', 'visible');
        $("#box_5_1").focus();

        if (called1 == '1')
        {
            //////////alert("Called 1")
            if (interactiveObj.correct_Ques5_attempt1 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2"style="left: 24px;">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3" style="left: 28px;">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                Ques6_visible = 1;
                //interactiveObj.greens+=1;
            }
            if (interactiveObj.correct_Ques5_attempt2 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2"style="left: 24px;">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3" style="left: 28px;">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                Ques6_visible = 1;
                //interactiveObj.greens+=1;
            }
            if (interactiveObj.correct_Ques5_attempt3 == 1)//replace from here
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2"style="left: 24px;">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3" style="left: 28px;">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                Ques6_visible = 1;
                //interactiveObj.greens+=1;

            }
            if (interactiveObj.incorrect_Ques5_attempt3 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="first_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2" style="left: 24px;">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3" style="left: 28px;">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox5").css('box-shadow', '0 0 20px blue');
                Ques6_visible = 1;
            }
        }
        if (called2 == '1')
        {
            //////////alert("Called 2")
            if (interactiveObj.correct_Ques5_attempt1 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                //$("#answerBox2").append('<div id="second_Answer"><div class="number1">'+interactiveObj.number2_1+'</div><div class="number2" style="width:30px;>'+interactiveObj.number2_2+'</div><div class="number3">'+interactiveObj.modulus2+'</div></div>');

                $("#answerBox5").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                Ques6_visible = 1;
                //interactiveObj.greens+=1;
            }
            if (interactiveObj.correct_Ques5_attempt2 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');


                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                Ques6_visible = 1;
                //interactiveObj.greens+=1;
            }
            if (interactiveObj.correct_Ques5_attempt3 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                Ques6_visible = 1;
                //interactiveObj.greens+=1;
            }
            if (interactiveObj.incorrect_Ques5_attempt3 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").css('width', '71px');
                $("#answerBox5").css('height', '58px');
                $("#answerBox5").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox5").css('box-shadow', '0 0 20px blue');
                Ques6_visible = 1;
            }

        }
        if (called3 == '1')   //changing from here FLAG==1
        {
            //////////alert("Called 3")
            if (interactiveObj.attempt_Ques5 == 1 && interactiveObj.Q5_C_NLF_A1 == 1)
            {
                // attempt 1 correct but not in lowest form
                $("#box_5_1").attr('disabled', true);
                $("#box_5_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_2").attr('disabled', true);
                $("#box_5_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_3").attr('disabled', true);
                $("#box_5_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box"></input></div></div></div></div>')

                $("#box_5_1_1").focus();

            }
            if (interactiveObj.attempt_Ques5 == 1 && interactiveObj.Q5_C_LF_A1 == 1)
            {//attempt 1 correct and in lowest form
                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>')

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                //interactiveObj.greens+=1;
                Ques6_visible = 1;

            }
            if (interactiveObj.attempt_Ques5 == 1 && interactiveObj.incorrect_Q5_A1 == 1)
            {
                // attempt 1 is incorrect
            }
            if (interactiveObj.attempt_Ques5 == 2 && interactiveObj.Q5_C_LF_A2 == 1)
            {
                // attempt 2 correct and in lowest form
                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');


                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                //interactiveObj.greens+=1;
                Ques6_visible = 1;
            }
            if (interactiveObj.attempt_Ques5 == 2 && interactiveObj.Q5_C2_C_LF_A2 == 1)
            {
                // attempt 2 correct and in lowest form
                //////////alert("A2Case 2");

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);

                $("#fifthQuestion").css('height', '130px');

            }
            if (interactiveObj.attempt_Ques5 == 2 && interactiveObj.Q5_C_NLF_A2 == 1)
            {
                $("#box_5_1").attr('disabled', true);
                $("#box_5_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_2").attr('disabled', true);
                $("#box_5_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_3").attr('disabled', true);
                $("#box_5_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box"></input></div></div></div></div>');

                $("#box_5_1_1").focus();
                $("#fifthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques5 == 2 && interactiveObj.Q5_C2_C_NLF_A2 == 1)
            {
                //////////alert("A2Case22")
                $("#box_5_1").attr('disabled', true);
                $("#box_5_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_2").attr('disabled', true);
                $("#box_5_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_3").attr('disabled', true);
                $("#box_5_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box"></input></div></div></div></div>');

                $("#box_5_1_1").focus();
                $("#fifthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques5 == 2 && interactiveObj.incorrect_Q5_A2 == 1 && interactiveObj.counter5 == 0)
            {
                $("#box_5_1").attr('disabled', true);
                $("#box_5_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_2").attr('disabled', true);
                $("#box_5_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_5_3").attr('disabled', true);
                $("#box_5_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box"></input></div></div></div></div>')

                $("#box_5_1_1").focus();
                $("#fifthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques5 == 2 && interactiveObj.incorrect_Q5_A2 == 1 && interactiveObj.counter5 == 1)
            {
                $("#box_5_1").attr('disabled', false);
                $("#box_5_2").attr('disabled', false);
                $("#box_5_3").attr('disabled', false);
                $("#box_5_1").focus();

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + interactiveObj.number5_2 / interactiveObj.hcf_5 + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus5 / interactiveObj.hcf_5 + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                $("#fifthQuestion").css('height', '130px');

            }
            if (interactiveObj.attempt_Ques5 == 3 && interactiveObj.Q5_C_LF_A3 == 1)
            {

                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox5").css('box-shadow', '0 0 20px green');

                $("#sub_fraction_inputBox5").css('border-radius', '4px');
                $("#sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                $("#fifthQuestion").css('height', '130px');

             
                Ques6_visible = 1;
            }
            if (interactiveObj.attempt_Ques5 == 3 && interactiveObj.Q5_C_NLF_A3 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' +replaceDynamicText(( interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox5").css('box-shadow', '0 0 20px blue');

                $("#sub_fraction_inputBox5").css('border-radius', '4px');
                $("#sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                $("#fifthQuestion").css('height', '130px');
                Ques6_visible = 1;
            }
            if (interactiveObj.attempt_Ques5 == 3 && interactiveObj.incorrect_Q5_A3 == 1)
            {
                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number5_2 / interactiveObj.hcf_5 + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus3 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox5").css('box-shadow', '0 0 20px blue');

                $("#sub_fraction_inputBox5").css('border-radius', '4px');
                $("#sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                $("#fifthQuestion").css('height', '130px');
                Ques6_visible = 1;
            }
            if (interactiveObj.attempt_Ques5 == 3 && interactiveObj.C2_Q5_C_NLF_A3 == 1)
            {
                //////////alert("Case 2");

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);

                $("#fifthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques5 == 3 && interactiveObj.C2_Q5_C_LF_A3 == 1)
            {
                //////////alert("Case 22");

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                $("#fifthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques5 == 3 && interactiveObj.incorrect_C2_Q5_A3 == 1)
            {
                //////////alert("Case222");

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                $("#fifthQuestion").css('height', '130px');
            }
            if ((interactiveObj.attempt_Ques5 == 4) && (interactiveObj.Q5_C_NLF_A4 == 1 || interactiveObj.Q5_C_LF_A4 == 1))
            {
                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + interactiveObj.number5_2 / interactiveObj.hcf_5 + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus5 / interactiveObj.hcf_5 + '></input></div></div></div></div>')

                $("#thirdQuestion").css('height', '130px');
                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);
                Ques6_visible = 1;
                if (interactiveObj.Q5_C_NLF_A4 == 1)
                {
                    $("#answerBox5").css('border-radius', '11px');

                    $("#answerBox5").css('-webkit-box-shadow', '0 0 20px blue');
                    $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px blue');
                    $("#answerBox5").css('box-shadow', '0 0 20px blue');
                }
                else
                {
                    $("#answerBox5").css('border-radius', '11px');

                    $("#answerBox5").css('-webkit-box-shadow', '0 0 20px green');
                    $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px green');
                    $("#answerBox5").css('box-shadow', '0 0 20px green');

                    //interactiveObj.greens+=1;
                }
                $("#sub_fraction_inputBox5").css('border-radius', '4px');
                $("#sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');
            }
            if (interactiveObj.attempt_Ques5 == 4 && interactiveObj.incorrect_Q5_A4 == 1)
            {
                //red color box
                $("#answerBox5").html("");
                $("#answerBox5").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number5_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#fifthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox5" ><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_5_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number5_2 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_5_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus5 / interactiveObj.hcf_5),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_5_1_1").attr('disabled', true);
                $("#box_5_1_2").attr('disabled', true);

                $("#answerBox5").css('border-radius', '11px');

                $("#answerBox5").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox5").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox5").css('box-shadow', '0 0 20px blue');
                $("#fifthQuestion").css('height', '130px');

                $("#sub_fraction_inputBox5").css('border-radius', '4px');
                $("#sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                Ques6_visible = 1;
      
            }

        }
    }
    if (Ques6_visible == '1')
    {
        $("#sixthQuestion").css('visibility', 'visible');
        $("#box_6_1").focus();
        if (called3 == '1')
        {
            $("#sixthQuestion").css('top', '170px');
        }
        //---------start from here---------------//
        if (called21 == '1')
        {
            //////////alert("called type 21 display")
            if (interactiveObj.correct_Ques6_attempt1 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").css('width', '71px');
                $("#answerBox6").css('height', '58px');
                //$("#answerBox2").append('<div id="second_Answer"><div class="number1">'+interactiveObj.number2_1+'</div><div class="number2" style="width:30px;>'+interactiveObj.number2_2+'</div><div class="number3">'+interactiveObj.modulus2+'</div></div>');

                $("#answerBox6").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');


                //interactiveObj.greens+=1;
              
                 $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.correct_Ques6_attempt2 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").css('width', '71px');
                $("#answerBox6").css('height', '58px');
                $("#answerBox6").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');


                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                //interactiveObj.greens+=1;
               
                 $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.correct_Ques6_attempt3 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").css('width', '71px');
                $("#answerBox6").css('height', '58px');
                $("#answerBox6").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                //interactiveObj.greens+=1;
               
                 $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.incorrect_Ques6_attempt3 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").css('width', '71px');
                $("#answerBox6").css('height', '58px');
                $("#answerBox6").append('<div id="second_Answer"><div class="number1">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number2">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="number3">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>');

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');

                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                //Ques3_visible=1;
            }
        }
        if (called31 == '1')
        {
           
            if (interactiveObj.attempt_Ques6 == 1 && interactiveObj.Q6_C_NLF_A1 == 1)
            {
                // attempt 1 correct but not in lowest form
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>')

                $("#box_6_1_1").focus();

            }
            if (interactiveObj.attempt_Ques6 == 1 && interactiveObj.Q6_C_LF_A1 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>')

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                //interactiveObj.greens+=1;
                
                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.attempt_Ques6 == 1 && interactiveObj.incorrect_Q6_A1 == 1)
            {
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C_LF_A2 == 1)
            {
                // attempt 2 correct and in lowest form
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>')

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

                //interactiveObj.greens+=1;
               // interactiveObj.getResult();
                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C2_C_LF_A2 == 1)
            {
                // attempt 2 correct and in lowest form
                //////////alert("A2Case 2");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6 ),interactiveObj.numberLanguage,"interactiveObj")+ '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C_NLF_A2 == 1)
            {
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>')

                $("#box_6_1_1").focus();
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C2_C_NLF_A2 == 1)
            {
                //////////alert("A2Case22")
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>')

                $("#box_6_1_1").focus();
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.incorrect_Q6_A2 == 1 && interactiveObj.counter6 == 0)
            {
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', replaceDynamicText(interactiveObj.box_1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', replaceDynamicText(interactiveObj.box_2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', replaceDynamicText(interactiveObj.box_3,interactiveObj.numberLanguage,"interactiveObj"));

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>')

                $("#box_6_1_1").focus();
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.incorrect_Q6_A2 == 1 && interactiveObj.counter6 == 1)
            {
                $("#box_6_1").attr('disabled', false);
                $("#box_6_2").attr('disabled', false);
                $("#box_6_3").attr('disabled', false);
                $("#box_6_1").focus();

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6 ,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.Q6_C_LF_A3 == 1)
            {

                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6 ),interactiveObj.numberLanguage,"interactiveObj")+ '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2 ,interactiveObj.numberLanguage,"interactiveObj")+ '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

                //interactiveObj.greens+=1;
                interactiveObj.getResult();
                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.Q6_C_NLF_A3 == 1)
            {

                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj")+ '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' +replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.incorrect_Q6_A3 == 1)
            {

                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.C2_Q6_C_NLF_A3 == 1)
            {
                //////////alert("Case 2");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_2").attr('disabled', true);
                $("#box_6_1_1").attr('disabled', true);
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.C2_Q6_C_LF_A3 == 1)
            {
                //////////alert("Case 22");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.incorrect_C2_Q6_A3 == 1)
            {

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
            }
            if ((interactiveObj.attempt_Ques6 == 4) && (interactiveObj.Q6_C_NLF_A4 == 1 || interactiveObj.Q6_C_LF_A4 == 1))
            {
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox" class="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')


                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);

                $(".sub_fraction_inputBox5").css('border-radius', '4px');
                $(".sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $(".sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $(".sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                if (interactiveObj.Q6_C_NLF_A4 == 1)
                {
                    $("#answerBox6").css('border-radius', '11px');

                    $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                    $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                    $("#answerBox6").css('box-shadow', '0 0 20px blue');
                    $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                }
                else
                {
                    $("#answerBox6").css('border-radius', '11px');

                    $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                    $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                    $("#answerBox6").css('box-shadow', '0 0 20px green');

                    //interactiveObj.greens+=1;
                    interactiveObj.getResult();
                    $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                }
            }
            if (interactiveObj.attempt_Ques6 == 4 && interactiveObj.incorrect_Q6_A4 == 1)
            {
                //red color box
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + replaceDynamicText(interactiveObj.number6_1,interactiveObj.numberLanguage,"interactiveObj") + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox" class="sub_fraction_inputBox7"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.number6_2 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.modulus6 / interactiveObj.hcf_6),interactiveObj.numberLanguage,"interactiveObj") + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');

                $(".sub_fraction_inputBox7").css('border-radius', '4px');
                $(".sub_fraction_inputBox7").css('-webkit-box-shadow', '0 0 10px blue');
                $(".sub_fraction_inputBox7").css('-moz-box-shadow', ' 0 0 10px blue');
                $(".sub_fraction_inputBox7").css('box-shadow', '0 0 10px blue');

                //-------//
                /*$("#sub_fraction_inputBox").css('border-radius', '11px');
                 
                 $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 20px blue');
                 $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 20px blue');
                 $("#sub_fraction_inputBox").css('box-shadow', '0 0 20px blue');*/
                 $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
        }
        if (called41 == '1')
        {
            //////////alert("called type 41")

            if (interactiveObj.attempt_Ques6 == 1 && interactiveObj.Q6_C_NLF_A1 == 1)
            {
                // attempt 1 correct but not in lowest form
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', interactiveObj.box_1);

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', interactiveObj.box_2);

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', interactiveObj.box_3);

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>');

                $("#box_6_1_1").focus();
            }
            if (interactiveObj.attempt_Ques6 == 1 && interactiveObj.Q6_C_LF_A1 == 1)
            {//attempt 1 correct and in lowest form
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>')

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');


                //interactiveObj.greens+=1;
                //interactiveObj.getResult();
                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                //interactiveObj.getStats();
            }
            if (interactiveObj.attempt_Ques6 == 1 && interactiveObj.incorrect_Q6_A1 == 1)
            {
               // attempt 1 is incorrect
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C_LF_A2 == 1)
            {
                // attempt 2 correct and in lowest form
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>')


                /*$("#thirdQuestion").append('<div id="sub_Fraction">0.'+interactiveObj.number3_2+'&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">'+interactiveObj.number3_2+'</div><div class="frac">'+interactiveObj.modulus3+'</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_3_1_1" class="Input_Box" placeholder='+interactiveObj.number3_2/interactiveObj.hcf_3+'></input></div><div class="frac"><input id="box_3_1_2" class="Input_Box" placeholder='+interactiveObj.modulus3/interactiveObj.hcf_3+'></input></div></div></div></div>')	
                 
                 $("#box_3_1_1").attr('disabled',true);
                 $("#box_3_1_2").attr('disabled',true);*/
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                //interactiveObj.greens+=1;
               // interactiveObj.getResult();
                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C2_C_LF_A2 == 1)
            {
                // attempt 2 correct and in lowest form
                //////////alert("A2Case 2");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C_NLF_A2 == 1)
            {
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', interactiveObj.box_1);

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', interactiveObj.box_2);

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', interactiveObj.box_3);

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>');

                $("#box_6_1_1").focus();
                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.Q6_C2_C_NLF_A2 == 1)
            {
                //////////alert("A2Case22")
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', interactiveObj.box_1);

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', interactiveObj.box_2);

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', interactiveObj.box_3);

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>');

                $("#box_6_1_1").focus();
                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.incorrect_Q6_A2 == 1 && interactiveObj.counter6 == 0)
            {
                $("#box_6_1").attr('disabled', true);
                $("#box_6_1").attr('placeholder', interactiveObj.box_1);

                $("#box_6_2").attr('disabled', true);
                $("#box_6_2").attr('placeholder', interactiveObj.box_2);

                $("#box_6_3").attr('disabled', true);
                $("#box_6_3").attr('placeholder', interactiveObj.box_3);

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box"></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box"></input></div></div></div></div>');

                $("#box_6_1_1").focus();
                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 2 && interactiveObj.incorrect_Q6_A2 == 1 && interactiveObj.counter6 == 1)
            {
                $("#box_6_1").attr('disabled', false);
                $("#box_6_2").attr('disabled', false);
                $("#box_6_3").attr('disabled', false);
                $("#box_6_1").focus();

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.Q6_C_LF_A3 == 1)
            {

                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                $("#answerBox6").css('box-shadow', '0 0 20px green');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

                $("#sixthQuestion").css('height', '130px');
               // interactiveObj.getResult();
                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.Q6_C_NLF_A3 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

                $("#sixthQuestion").css('height', '130px');
                //interactiveObj.getStats();
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.incorrect_Q6_A3 == 1)
            {
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');


                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');

                $("#sub_fraction_inputBox").css('border-radius', '4px');
                $("#sub_fraction_inputBox").css('-webkit-box-shadow', '0 0 10px blue');
                $("#sub_fraction_inputBox").css('-moz-box-shadow', ' 0 0 10px blue');
                $("#sub_fraction_inputBox").css('box-shadow', '0 0 10px blue');

                $("#sixthQuestion").css('height', '130px');
                //interactiveObj.getStats();
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.C2_Q6_C_NLF_A3 == 1)
            {
                //////////alert("Case 2");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);

                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.C2_Q6_C_LF_A3 == 1)
            {
                //////////alert("Case 22");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#sixthQuestion").css('height', '130px');
            }
            if (interactiveObj.attempt_Ques6 == 3 && interactiveObj.incorrect_C2_Q6_A3 == 1)
            {
                //////////alert("Case222");

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);
                $("#sixthQuestion").css('height', '130px');
            }
            if ((interactiveObj.attempt_Ques6 == 4) && (interactiveObj.Q6_C_NLF_A4 == 1 || interactiveObj.Q6_C_LF_A4 == 1))
            {
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox" class="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>');

                $("#sixthQuestion").css('height', '130px');
                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);

                $(".sub_fraction_inputBox5").css('border-radius', '4px');
                $(".sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $(".sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $(".sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                if (interactiveObj.Q6_C_NLF_A4 == 1)
                {
                    $("#answerBox6").css('border-radius', '11px');

                    $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                    $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                    $("#answerBox6").css('box-shadow', '0 0 20px blue');
                    //interactiveObj.getStats();
                }
                else
                {
                    $("#answerBox6").css('border-radius', '11px');

                    $("#answerBox6").css('-webkit-box-shadow', '0 0 20px green');
                    $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px green');
                    $("#answerBox6").css('box-shadow', '0 0 20px green');

                   // interactiveObj.getResult();
                    $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);
                }
            }
            if (interactiveObj.attempt_Ques6 == 4 && interactiveObj.incorrect_Q6_A4 == 1)
            {
                //red color box
                $("#answerBox6").html("");
                $("#answerBox6").append('<div id="fraction_correct"><div id="mixed_fraction" style="display: inline-block;">' + interactiveObj.number6_1 + '<div id="doubleFraction" style="display: inline-block;"><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div></div></div>');

                $("#sixthQuestion").append('<div id="sub_Fraction">0.' + interactiveObj.number6_2 + '&nbsp=&nbsp<div id="sub_Answer"></div><div class="fraction"><div class="frac numerator">' + interactiveObj.number6_2 + '</div><div class="frac">' + interactiveObj.modulus6 + '</div></div>&nbsp;=&nbsp;<div id="sub_fraction_inputBox" class="sub_fraction_inputBox5"><div class="fraction" style="display:inline-block;"><div class="frac numerator"><input id="box_6_1_1" class="Input_Box" placeholder=' + interactiveObj.number6_2 / interactiveObj.hcf_6 + '></input></div><div class="frac"><input id="box_6_1_2" class="Input_Box" placeholder=' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '></input></div></div></div></div>')

                $("#box_6_1_1").attr('disabled', true);
                $("#box_6_1_2").attr('disabled', true);

                $("#answerBox6").css('border-radius', '11px');

                $("#answerBox6").css('-webkit-box-shadow', '0 0 20px blue');
                $("#answerBox6").css('-moz-box-shadow', ' 0 0 20px blue');
                $("#answerBox6").css('box-shadow', '0 0 20px blue');
                $("#sixthQuestion").css('height', '130px');

                $(".sub_fraction_inputBox5").css('border-radius', '4px');
                $(".sub_fraction_inputBox5").css('-webkit-box-shadow', '0 0 10px blue');
                $(".sub_fraction_inputBox5").css('-moz-box-shadow', ' 0 0 10px blue');
                $(".sub_fraction_inputBox5").css('box-shadow', '0 0 10px blue');

                $("#next_Level").css('visibility','visible').animate({'opacity':'1'},1000);

            }
        }
    }
}
questionInteractive.prototype.checkAnswer = function(id)
{
    interactiveObj.question_Type = id;

    if (interactiveObj.question_Type == "one")
    {
        //getting the values
        interactiveObj.attempt_Ques1 += 1;
        interactiveObj.box_1 = parseInt($("#box_1").val());
        interactiveObj.box_2 = parseInt($("#box_2").val());
        interactiveObj.box_3 = parseInt($("#box_3").val());
        interactiveObj.modulus = parseInt(10);    


        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_FirstAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + interactiveObj.number1_Final + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number1_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number1_2, interactiveObj.numberLanguage, "interactiveObj")+'.&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.number1_Final, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number1_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number1_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number1_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>&nbsp;' + replaceDynamicText(promptArr['txt_8'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_secondAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_thirdAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number1_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number1_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number1_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><br/><button class="buttonPrompt_Incorrect_thirdAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        $("#prompts").html(html2);
        $(".correct").draggable({containment: "#container"});


        //checking if the answer is correct
        if (interactiveObj.attempt_Ques1 == 1)
        {
            if (interactiveObj.box_1 == interactiveObj.number1_1 && interactiveObj.box_2 == interactiveObj.number1_2 && interactiveObj.box_3 == interactiveObj.modulus)
            {
                interactiveObj.correct_Ques1_attempt1 = 1;
           
                 extraParameterArr[0]+="Stage 1:-(Q1:"+interactiveObj.number1_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

                $("#wellDone_LF").css('visibility', 'visible');
                $("#box_1").attr('disabled', true);
                $("#box_2").attr('disabled', true);
                $("#box_3").attr('disabled', true);
                $(".buttonPrompt").focus();
                interactiveObj.status_Q1 = 1;
                interactiveObj.correctCounter += 1;
                interactiveObj.score_T1 = 10;
                levelWiseScore = 10;
            }
            else
            {
                interactiveObj.incorrect_Ques1_attempt1 = 1;

                extraParameterArr[0]+="(Q1:"+interactiveObj.number1_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";

                $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                $("#box_1").attr('disabled', true);
                $("#box_2").attr('disabled', true);
                $("#box_3").attr('disabled', true);
                $(".buttonPrompt_Incorrect_FirstAttempt").focus();
            }
        }
        if (interactiveObj.attempt_Ques1 == 2)
        {
            if (interactiveObj.box_1 == interactiveObj.number1_1 && interactiveObj.box_2 == interactiveObj.number1_2 && interactiveObj.box_3 == interactiveObj.modulus)
            {
                interactiveObj.correct_Ques1_attempt2 = 1;
                interactiveObj.status_Q1 = 1;
                ////////////alert("correctAnswer");	
                interactiveObj.correctCounter += 1;
                interactiveObj.score_T1 = 5;
                levelWiseScore = 5;

                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                $("#wellDone_LF").css('visibility', 'visible');
                $("#box_1").attr('disabled', true);
                $("#box_2").attr('disabled', true);
                $("#box_3").attr('disabled', true);
                $(".buttonPrompt").focus();

            }
            else
            {
                interactiveObj.incorrect_Ques1_attempt2 = 1;
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                ////////////alert("Incorrect Answer");
                $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                $("#box_1").attr('disabled', true);
                $("#box_2").attr('disabled', true);
                $("#box_3").attr('disabled', true);
                $(".buttonPrompt_Incorrect_secondAttempt").focus();
            }
        }
        if (interactiveObj.attempt_Ques1 == 3)
        {
            if (interactiveObj.box_1 == interactiveObj.number1_1 && interactiveObj.box_2 == interactiveObj.number1_2 && interactiveObj.box_3 == interactiveObj.modulus)
            {
                interactiveObj.correct_Ques1_attempt3 = 1;
                ////////////alert("correctAnswer");	
                interactiveObj.status_Q1 = 1;
                interactiveObj.correctCounter += 1;
                interactiveObj.score_T1 = 5;
                levelWiseScore = 5;

                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                $("#wellDone_LF").css('visibility', 'visible');
                $("#box_1").attr('disabled', true);
                $("#box_2").attr('disabled', true);
                $("#box_3").attr('disabled', true);
                $(".buttonPrompt").focus();
            }
            else
            {
                interactiveObj.incorrect_Ques1_attempt3 = 1;

                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                ////////////alert("Incorrect Answer");
                $("#Incorrect_thirdAttempt").css('visibility', 'visible');
                $("#box_1").attr('disabled', true);
                $("#box_2").attr('disabled', true);
                $("#box_3").attr('disabled', true);
                $(".buttonPrompt_Incorrect_thirdAttempt").focus();
            }
        }
    }
    if (interactiveObj.question_Type == "two")
    {
        html2 = "";
        $("#prompts").html("");
        //////////alert("Inside Type Two checking")
        interactiveObj.attempt_Ques2 += 1;
        interactiveObj.box_1 = parseInt($("#box_2_1").val());
        interactiveObj.box_2 = parseInt($("#box_2_2").val());
        interactiveObj.box_3 = parseInt($("#box_2_3").val());
        interactiveObj.modulus2 = parseInt(100);

        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_FirstAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + interactiveObj.number2_Final + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number2_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number2_2, interactiveObj.numberLanguage, "interactiveObj") +'.&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.number2_Final, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number2_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number2_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number2_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus2, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>&nbsp;' + replaceDynamicText(promptArr['txt_8'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_secondAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_thirdAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number2_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number2_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number2_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus2, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><br/><button class="buttonPrompt_Incorrect_thirdAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        $("#prompts").html(html2);
        $("#wellDone_LF").draggable({containment: "#container"});
        $("#Incorrect_FirstAttempt").draggable({containment: "#container"});
        $("#Incorrect_SecondAttempt").draggable({containment: "#container"});
        $("#Incorrect_thirdAttempt").draggable({containment: "#container"});

        if (interactiveObj.attempt_Ques2 == 1)
        {
            if (interactiveObj.box_1 == interactiveObj.number2_1 && interactiveObj.box_2 == interactiveObj.number2_2 && interactiveObj.box_3 == interactiveObj.modulus2)
            {
                interactiveObj.correct_Ques2_attempt1 = 1;
                ////////////alert("correctAnswer");	
                interactiveObj.status_Q2 = 1;
                interactiveObj.correctCounter += 1;
                interactiveObj.score_T2 = 10;
                levelWiseScore += 10;
                extraParameterArr[0]+="(Q2:"+interactiveObj.number2_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                $("#wellDone_LF").css('visibility', 'visible');
                $("#box_2_1").attr('disabled', true);
                $("#box_2_2").attr('disabled', true);
                $("#box_2_3").attr('disabled', true);
                $(".buttonPrompt").focus();
            }
            else
            {
                interactiveObj.incorrect_Ques2_attempt1 = 1;
                ////////////alert("Incorrect Answer");
                extraParameterArr[0]+="(Q2:"+interactiveObj.number2_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                $("#box_2_1").attr('disabled', true);
                $("#box_2_2").attr('disabled', true);
                $("#box_2_3").attr('disabled', true);
                $(".buttonPrompt_Incorrect_FirstAttempt").focus();
            }

        }
        if (interactiveObj.attempt_Ques2 == 2)
        {
            if (interactiveObj.box_1 == interactiveObj.number2_1 && interactiveObj.box_2 == interactiveObj.number2_2 && interactiveObj.box_3 == interactiveObj.modulus2)
            {
                interactiveObj.correct_Ques2_attempt2 = 1;
                ////////////alert("correctAnswer");	
                interactiveObj.status_Q2 = 1;
                interactiveObj.correctCounter += 1;
                interactiveObj.score_T2 = 5;
                levelWiseScore += 5;

                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                $("#wellDone_LF").css('visibility', 'visible');
                $("#box_2_1").attr('disabled', true);
                $("#box_2_2").attr('disabled', true);
                $("#box_2_3").attr('disabled', true);
                $(".buttonPrompt").focus();
            }
            else
            {
                interactiveObj.incorrect_Ques2_attempt2 = 1;
                ////////////alert("Incorrect Answer");
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                $("#box_2_1").attr('disabled', true);
                $("#box_2_2").attr('disabled', true);
                $("#box_2_3").attr('disabled', true);
                $(".buttonPrompt_Incorrect_secondAttempt").focus();
            }

        }
        if (interactiveObj.attempt_Ques2 == 3)
        {
            if (interactiveObj.box_1 == interactiveObj.number2_1 && interactiveObj.box_2 == interactiveObj.number2_2 && interactiveObj.box_3 == interactiveObj.modulus2)
            {
                interactiveObj.correct_Ques2_attempt3 = 1;
                ////////////alert("correctAnswer");	
                interactiveObj.status_Q2 = 1;
                interactiveObj.correctCounter += 1;
                interactiveObj.score_T2 = 5;
                levelWiseScore += 5;
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                $("#wellDone_LF").css('visibility', 'visible');
                $("#box_2_1").attr('disabled', true);
                $("#box_2_2").attr('disabled', true);
                $("#box_2_3").attr('disabled', true);
                $(".buttonPrompt").focus();
            }
            else
            {
                interactiveObj.incorrect_Ques2_attempt3 = 1;
                ////////////alert("Incorrect Answer");
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                $("#Incorrect_thirdAttempt").css('visibility', 'visible');
                $("#box_2_1").attr('disabled', true);
                $("#box_2_2").attr('disabled', true);
                $("#box_2_3").attr('disabled', true);
                $(".buttonPrompt_Incorrect_thirdAttempt").focus();
            }

        }
    }
    if (interactiveObj.question_Type == "three")
    {
        html2 = "";
        $("#prompts").html("");

        if(interactiveObj.number3_2>=1 && interactiveObj.number3_2<=9)
            {
                interactiveObj.modulus3 = parseInt(10);
            }
        else
            {
                interactiveObj.modulus3 = parseInt(100);
            }

        interactiveObj.number1 = createFrac(interactiveObj.number3_2, interactiveObj.modulus3);
        interactiveObj.attempt_Ques3 += 1;

    
        html2 += '<div id="Incorrect_FirstAttempt" class="correct" style="width: 320px;height: 115px;"><div class="sparkie"></div><div class="Incorrect" style="width: 245px;">' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(interactiveObj.number3_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; ' + replaceDynamicText(interactiveObj.number3_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number3_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp; ' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; ' + replaceDynamicText(interactiveObj.number3_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" style="top: 90px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div id="text_Incorrect_SecondAttempt">' + replaceDynamicText(interactiveObj.number3_Final, interactiveObj.numberLanguage, "interactiveObj") + '=' + replaceDynamicText(interactiveObj.number3_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_Incorrect_secondAttempt" style="top: 69px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="NLF" class="correct"><div class="sparkie" style="display:inline-block;"></div><div id="text_NLF"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_NLF" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp'+createFrac(interactiveObj.number3_2,interactiveObj.modulus3)+'&nbsp;' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button  onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="fraction_text">' + replaceDynamicText(promptArr['txt_22'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="animation2"></div><div id="animation3"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="position: absolute;top: 300px;left: 129px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</div></button></div>';
        html2 += '<div id="correctAnswer_Finally" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">Correct answer is&nbsp;' + replaceDynamicText(interactiveObj.number3_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number3_2 / interactiveObj.hcf_3, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus3 / interactiveObj.hcf_3, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.</div><br/><button class="buttonPrompt" style="left: 98px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="closeAnswer" class="correct"><div class="sparkie"></div><div id="closeText">'+promptArr['txt_42']+'</div><button class="buttonPrompt" style="left: 111px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        
        $("#prompts").html(html2);
        $(".correct").draggable({containment: "#container"});

        if (interactiveObj.attempt_Ques3 == 1)
        {
            //////////alert("in attempt 1")
            interactiveObj.box_1 = parseInt($("#box_3_1").val());
            interactiveObj.box_2 = parseInt($("#box_3_2").val());
            interactiveObj.box_3 = parseInt($("#box_3_3").val());
            interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
            interactiveObj.correctAnswer_Ques3 = interactiveObj.number3_Final;

            interactiveObj.closeAnswer3=interactiveObj.number3_1;

            //check for correct answer
            if (interactiveObj.local == interactiveObj.correctAnswer_Ques3)
            {
                //check for lowest form
                interactiveObj.correct_Q3_A1 = 1;

                interactiveObj.Q3_C_NLF_A1=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3)); 

                if (interactiveObj.Q3_C_NLF_A1 == 1)
                    {
                        extraParameterArr[0]+="(Q3:"+interactiveObj.number3_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                else
                    {
                        extraParameterArr[0]+="(Q3:"+interactiveObj.number3_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        interactiveObj.Q3_C_LF_A1 = 1;
                        interactiveObj.status_Q3 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 = 10;
                        levelWiseScore += 10;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();

                    }
            }
            else if(interactiveObj.local != interactiveObj.correctAnswer_Ques3 || interactiveObj.box_1 == interactiveObj.number3_1)   // incorrect answer
            {
               interactiveObj.closeCounter1+=1; 
               
               if(interactiveObj.box_1 == interactiveObj.number3_1 && interactiveObj.closeCounter1<=2)
               {
                extraParameterArr[0]+="(Q3:"+interactiveObj.number3_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                //interactiveObj.incorrect_Q3_A1 = 1;
                interactiveObj.attempt_Ques3-= 1;
                $(".Input_Box").attr('disabled', true);
                $("#closeAnswer").css('visibility', 'visible');
                $(".buttonPrompt").focus();
               }
               else
               {
                extraParameterArr[0]+="(Q3:"+interactiveObj.number3_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                interactiveObj.incorrect_Q3_A1 = 1;
                $(".Input_Box").attr('disabled', true);
                $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                $(".buttonPrompt_Incorrect_FirstAttempt").focus();
               }
            }
        }
        if (interactiveObj.attempt_Ques3 == 2)
        {
            if (interactiveObj.incorrect_Q3_A1 == 1)
            {
                interactiveObj.box_1 = parseInt($("#box_3_1").val());
                interactiveObj.box_2 = parseInt($("#box_3_2").val());
                interactiveObj.box_3 = parseInt($("#box_3_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques3 = interactiveObj.number3_Final;

               ////alert("last attempt was incorrect")

                if (interactiveObj.local == interactiveObj.correctAnswer_Ques3)
                {
                    interactiveObj.correct_Q3_A2 = 1;
            
                    interactiveObj.Q3_C_NLF_A2=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3)); 
                    
                    if (interactiveObj.Q3_C_NLF_A2 == 1)
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                    }
                    else
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        interactiveObj.Q3_C_LF_A2 = 1;
                        interactiveObj.status_Q3 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 = 5;
                        levelWiseScore += 5;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else // incorrect answer
                {
                   extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    interactiveObj.incorrect_Q3_A2 = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                    $(".buttonPrompt_Incorrect_secondAttempt").focus();

                }

            }
            else
            {// when first attempt was correct but was not in lowest form
               ////alert("taking input from two boxes attempt_Ques3 == 2")
                interactiveObj.box_1 = parseInt($("#box_3_1_1").val());
                interactiveObj.box_2 = parseInt($("#box_3_1_2").val());
                interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                interactiveObj.correctAnswer_Ques3 = interactiveObj.number3_2 / interactiveObj.modulus3;
                // continue frm here
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques3)
                {
                    //checking for lowest form
                    if (interactiveObj.box_1 % interactiveObj.hcf_3 == 0 && interactiveObj.box_2 % interactiveObj.hcf_3 == 0)
                    {
                        interactiveObj.Q3_C2_C_NLF_A2 = 1;
                    }
                     

                    if (interactiveObj.Q3_C2_C_NLF_A2 == 1)
                    {
                        $(".Input_Box").attr('disabled', true);
                        $("#NLF").css('visibility', 'visible');
                        $(".buttonPrompt_NLF").focus();
                    }
                    else
                    {
                        interactiveObj.Q3_C2_C_LF_A2 = 1;
                        //interactiveObj.status_Q3 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();

                    }
                }
                else // if answer is wrong
                {
                    interactiveObj.incorrect_Q3_A2 = 1;
                    interactiveObj.counter = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                }

            }
        }
        if (interactiveObj.attempt_Ques3 == 3)
        {
            if (interactiveObj.Q3_C2_C_LF_A2 == 1 || (interactiveObj.incorrect_Q3_A2 == 1 && interactiveObj.counter==1))// if focus has been shifted to 3 boxes
            {
                interactiveObj.box_1 = parseInt($("#box_3_1").val());
                interactiveObj.box_2 = parseInt($("#box_3_2").val());
                interactiveObj.box_3 = parseInt($("#box_3_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques3 = interactiveObj.number3_Final;
           

                if (interactiveObj.local == interactiveObj.correctAnswer_Ques3)
                {
                   
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    if (interactiveObj.box_2 % interactiveObj.hcf_3 == 0 && interactiveObj.box_3 % interactiveObj.hcf_3 == 0)
                    {
                        interactiveObj.Q3_C_NLF_A3 = 1;
                    }
                    if (interactiveObj.Q3_C_NLF_A3 == 1)
                    {
                        $(".Input_Box").attr('disabled', true);
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.Q3_C_LF_A3 = 1;
                        interactiveObj.status_Q3 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 = 5;
                        levelWiseScore += 5;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else// wrong answer
                {
                     extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    interactiveObj.incorrect_Q3_A3 = 1;
                    interactiveObj.status_Q3 = 0;
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }

            }
            else// if focus is still on 2 boxes
            {
                interactiveObj.box_1 = parseInt($("#box_3_1_1").val());
                interactiveObj.box_2 = parseInt($("#box_3_1_2").val());
                interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                interactiveObj.correctAnswer_Ques3 = interactiveObj.number3_2 / interactiveObj.modulus3;
                 extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques3)
                {
                    if (interactiveObj.box_1 % interactiveObj.hcf_3 == 0 && interactiveObj.box_2 % interactiveObj.hcf_3 == 0)
                    {
                        interactiveObj.C2_Q3_C_NLF_A3 = 1;
                    }
                    if (interactiveObj.C2_Q3_C_NLF_A3 == 1)
                    {//correct but not in lowest form
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.C2_Q3_C_LF_A3 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else//incorrect answer
                {
                    interactiveObj.incorrect_C2_Q3_A3 = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                }
            }
        }
        if (interactiveObj.attempt_Ques3 == 4)
        {
            interactiveObj.box_1 = parseInt($("#box_3_1").val());
            interactiveObj.box_2 = parseInt($("#box_3_2").val());
            interactiveObj.box_3 = parseInt($("#box_3_3").val());
            interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
            interactiveObj.correctAnswer_Ques3 = interactiveObj.number3_Final;

             extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

            if (interactiveObj.local == interactiveObj.correctAnswer_Ques3)
            {
                if (interactiveObj.box_2 % interactiveObj.hcf_3 == 0 && interactiveObj.box_3 % interactiveObj.hcf_3 == 0)
                {
                    interactiveObj.Q3_C_NLF_A4 = 1;
                }
                if (interactiveObj.Q3_C_NLF_A4 == 1)
                {
                    $(".Input_Box").attr('disabled', true);
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else// correct and in lowest form
                {
                    interactiveObj.Q3_C_LF_A4 = 1;
                    interactiveObj.status_Q3 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T3 = 5;
                    levelWiseScore += 5;
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
            }
            else// wrong answer
            {
                interactiveObj.incorrect_Q3_A4 = 1;
                $(".Input_Box").attr('disabled', true);
                $("#correctAnswer_Finally").css('visibility', 'visible');
                $(".buttonPrompt").focus();
            }
        }
    }// closing
    if (interactiveObj.question_Type == "four")
    {
        html2 = "";
        $("#prompts").html("");
        ////////////alert("Inside Type four checking")
        interactiveObj.attempt_Ques4 += 1;
        interactiveObj.number1 = createFrac(interactiveObj.number4_2, interactiveObj.modulus4);
        interactiveObj.modulus4 = parseInt(100);


        html2 += '<div id="Incorrect_FirstAttempt" class="correct" style="width: 320px;height: 115px;"><div class="sparkie"></div><div class="Incorrect" style="width: 245px;">' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + ' &nbsp;' + replaceDynamicText(interactiveObj.number4_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; as&nbsp; ' + replaceDynamicText(interactiveObj.number4_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number4_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp; ' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(interactiveObj.number4_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" style="top: 90px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div id="text_Incorrect_SecondAttempt">' + replaceDynamicText(interactiveObj.number4_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.number4_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_Incorrect_secondAttempt" style="top: 69px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="NLF" class="correct"><div class="sparkie" style="display:inline-block;"></div><div id="text_NLF"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus4, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>can be further reduced</div><button class="buttonPrompt_NLF" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">'+promptArr['txt_4']+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;'+createFrac(interactiveObj.number4_2,100)+'&nbsp;' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button  onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="fraction_text">' + replaceDynamicText(promptArr['txt_22'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="animation2"></div><div id="animation3"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="position: absolute;top: 300px;left: 129px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</div></button></div>';
        html2 += '<div id="correctAnswer_Finally" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">Correct answer is&nbsp;' + replaceDynamicText(interactiveObj.number4_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number4_2 / interactiveObj.hcf_4, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + interactiveObj.modulus4 / interactiveObj.hcf_4 + '</div></div>.</div><br/><button class="buttonPrompt" style="left: 98px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        html2 += '<div id="closeAnswer" class="correct"><div class="sparkie"></div><div id="closeText">'+promptArr['txt_42']+'</div><button class="buttonPrompt" style="left: 111px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        $("#prompts").html(html2);
        $(".correct").draggable({containment: "#container"});

        if (interactiveObj.attempt_Ques4 == 1)
        {
            //////////alert("in attempt 1")
            interactiveObj.box_1 = parseInt($("#box_4_1").val());
            interactiveObj.box_2 = parseInt($("#box_4_2").val());
            interactiveObj.box_3 = parseInt($("#box_4_3").val());
            interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
            interactiveObj.correctAnswer_Ques4 = interactiveObj.number4_Final;

            //check for correct answer
            if (interactiveObj.local == interactiveObj.correctAnswer_Ques4)
            {
                //check for lowest form
                interactiveObj.correct_Q4_A1 = 1;
    
                interactiveObj.Q4_C_NLF_A1=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));  
                
                if (interactiveObj.Q4_C_NLF_A1 == 1)
                {
                    extraParameterArr[0]+="(Q4:"+interactiveObj.number4_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_NLF").css('visibility', 'visible');
                    $(".buttonPrompt2").focus();
                }
                else
                {
                    extraParameterArr[0]+="(Q4:"+interactiveObj.number4_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    interactiveObj.Q4_C_LF_A1 = 1;
                    interactiveObj.status_Q4 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T4 = 10;
                    levelWiseScore += 10;
                    interactiveObj.getStats();
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
            }
            else if(interactiveObj.local != interactiveObj.correctAnswer_Ques4 || interactiveObj.box_1 == interactiveObj.number4_1)   // incorrect answer
            {
                interactiveObj.closeCounter2+=1;
                if(interactiveObj.box_1 == interactiveObj.number4_1 && interactiveObj.closeCounter2<=2)
                {
                    extraParameterArr[0]+="(Q4:"+interactiveObj.number4_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";

                   // interactiveObj.incorrect_Q4_A1 = 1;
                    interactiveObj.attempt_Ques4 -= 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#closeAnswer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();    
                }
                else
                {
                   extraParameterArr[0]+="(Q4:"+interactiveObj.number4_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";

                    interactiveObj.incorrect_Q4_A1 = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                    $(".buttonPrompt_Incorrect_FirstAttempt").focus(); 
                }
            }
        }
        if (interactiveObj.attempt_Ques4 == 2)
        {
            if (interactiveObj.incorrect_Q4_A1 == 1)
            {
                interactiveObj.box_1 = parseInt($("#box_4_1").val());
                interactiveObj.box_2 = parseInt($("#box_4_2").val());
                interactiveObj.box_3 = parseInt($("#box_4_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques4 = interactiveObj.number4_Final;

                

                if (interactiveObj.local == interactiveObj.correctAnswer_Ques4)
                {
                    interactiveObj.correct_Q4_A2 = 1;
                    //correct then check for lowest form
                    
                   /* if (interactiveObj.box_2 % interactiveObj.hcf_4 == 0 && interactiveObj.box_4 % interactiveObj.modulus4 == 0)
                    {
                        interactiveObj.Q4_C_NLF_A2 = 1;  // correct but not in lowest form
                    }*/
                   interactiveObj.Q4_C_NLF_A2=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));   
                    if (interactiveObj.Q4_C_NLF_A2 == 1)
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                    }
                    else
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        interactiveObj.Q4_C_LF_A2 = 1;
                        interactiveObj.status_Q4 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T4 = 5;
                        levelWiseScore += 5;
                        interactiveObj.getStats();
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else // incorrect answer
                {
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";

                    interactiveObj.incorrect_Q4_A2 = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                    $(".buttonPrompt_Incorrect_secondAttempt").focus();

                }

            }
            else
            {// when first attempt was correct but was not in lowest form
                //////////alert("taking input from twi boxes")
                interactiveObj.box_1 = parseInt($("#box_4_1_1").val());
                interactiveObj.box_2 = parseInt($("#box_4_1_2").val());
                interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                interactiveObj.correctAnswer_Ques4 = interactiveObj.number4_2 / interactiveObj.modulus4;
                // continue frm here
                 extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques4)
                {
                    //checking for lowest form
                    if (interactiveObj.box_1 % interactiveObj.hcf_4 == 0 && interactiveObj.box_2 % interactiveObj.hcf_4 == 0)
                    {
                        interactiveObj.Q4_C2_C_NLF_A2 = 1;
                    }
                    if (interactiveObj.Q4_C2_C_NLF_A2 == 1)
                    {
                        $(".Input_Box").attr('disabled', true);
                        $("#NLF").css('visibility', 'visible');
                        $(".buttonPrompt_NLF").focus();
                    }
                    else
                    {
                        interactiveObj.Q4_C2_C_LF_A2 = 1;
                        interactiveObj.status_Q4 = 1;
                        //interactiveObj.getStats();
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();

                    }
                }
                else // if answer is wrong
                {
                    interactiveObj.incorrect_Q4_A2 = 1;
                    interactiveObj.counter4 = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    $(".buttonPrompt3").focus();
                }

            }
        }
        if (interactiveObj.attempt_Ques4 == 3)
        {
            if (interactiveObj.Q4_C2_C_LF_A2 == 1 ||( interactiveObj.incorrect_Q4_A2 == 1 && interactiveObj.counter4==1))// if focus has been shifted to 3 boxes
            {
                ////alert("Focus shift to three boxes");
                interactiveObj.box_1 = parseInt($("#box_4_1").val());
                interactiveObj.box_2 = parseInt($("#box_4_2").val());
                interactiveObj.box_3 = parseInt($("#box_4_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques4 = interactiveObj.number4_Final;

                
                
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques4)
                {
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

                    if (interactiveObj.box_2 % interactiveObj.hcf_4 == 0 && interactiveObj.box_3 % interactiveObj.hcf_4 == 0)
                    {
                        interactiveObj.Q4_C_NLF_A3 = 1;
                    }
                    if (interactiveObj.Q4_C_NLF_A3 == 1)
                    {
                        $(".Input_Box").attr('disabled', true);
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.Q4_C_LF_A3 = 1;
                        interactiveObj.status_Q4 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T4 = 5;
                        levelWiseScore += 5;
                        interactiveObj.getStats();
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else// wrong answer
                {
                   
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    interactiveObj.incorrect_Q4_A3 = 1;
                    interactiveObj.status_Q4 = 0;
                    interactiveObj.getStats();
                    $(".Input_Box").attr('disabled', true);
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
            }
            else// if focus is still on 2 boxes
            {
                ////alert("input from two boxes attempt 3")
                interactiveObj.box_1 = parseInt($("#box_4_1_1").val());
                interactiveObj.box_2 = parseInt($("#box_4_1_2").val());
                interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.number4_2) / interactiveObj.modulus4;
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques4)
                {
                    

                    if (interactiveObj.box_1 % interactiveObj.hcf_4 == 0 && interactiveObj.box_2 % interactiveObj.hcf_4 == 0)
                    {
                        interactiveObj.C2_Q4_C_NLF_A3 = 1;
                    }
                    if (interactiveObj.C2_Q4_C_NLF_A3 == 1)
                    {//correct but not in lowest form
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.C2_Q4_C_LF_A3 = 1;
                       // interactiveObj.status_Q4 = 1;
                        //interactiveObj.getStats();
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else//incorrect answer
                {
                    interactiveObj.incorrect_C2_Q4_A3 = 1;
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_NLF_Final").css('visibility', 'visible');
                    $(".buttonPrompt3").focus();
                    timer = setTimeout("interactiveObj.showAnimation();", 1000);
                }
            }
        }
        if (interactiveObj.attempt_Ques4 == 4)
        {
            interactiveObj.box_1 = parseInt($("#box_4_1").val());
            interactiveObj.box_2 = parseInt($("#box_4_2").val());
            interactiveObj.box_3 = parseInt($("#box_4_3").val());
            interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
            interactiveObj.correctAnswer_Ques4 = interactiveObj.number4_Final;

            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

            if (interactiveObj.local == interactiveObj.correctAnswer_Ques4)
            {
                if (interactiveObj.box_2 % interactiveObj.hcf_4 == 0 && interactiveObj.box_3 % interactiveObj.hcf_4 == 0)
                {
                    interactiveObj.Q4_C_NLF_A4 = 1;
                }
                if (interactiveObj.Q4_C_NLF_A4 == 1)
                {
                    $(".Input_Box").attr('disabled', true);
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else// correct and in lowest form
                {
                    interactiveObj.Q4_C_LF_A4 = 1;
                    interactiveObj.status_Q4 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T4 = 5;
                    levelWiseScore += 5;
                    interactiveObj.getStats();
                    $(".Input_Box").attr('disabled', true);
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
            }
            else// wrong answer
            {
                interactiveObj.incorrect_Q4_A4 = 1;
                interactiveObj.getStats();
                $(".Input_Box").attr('disabled', true);
                $("#correctAnswer_Finally").css('visibility', 'visible');
                $(".buttonPrompt").focus();
            }
        }
        //closed attempts
    }
    if (interactiveObj.question_Type == "five")
    {
        if (called1 == '1')
        {
            //getting the values
            //////////alert("checking  Q5 type=1")
            html2 = "";
            $("#prompts").html("");
            interactiveObj.attempt_Ques5 += 1;
            interactiveObj.box_1 = parseInt($("#box_5_1").val());
            interactiveObj.box_2 = parseInt($("#box_5_2").val());
            interactiveObj.box_3 = parseInt($("#box_5_3").val());
            interactiveObj.modulus5 = parseInt(10);

            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_FirstAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + interactiveObj.number5_Final + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>&nbsp;' + replaceDynamicText(promptArr['txt_8'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_secondAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_thirdAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><br/><button class="buttonPrompt_Incorrect_thirdAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            $("#prompts").html(html2);
            $(".correct").draggable({containment: "#container"});
              //checking if the answer is correct
            if (interactiveObj.attempt_Ques5 == 1)
            {
                if (interactiveObj.box_1 == interactiveObj.number5_1 && interactiveObj.box_2 == interactiveObj.number5_2 && interactiveObj.box_3 == interactiveObj.modulus5)
                {
                    interactiveObj.correct_Ques5_attempt1 = 1;
                    ////////////alert("correctAnswer");	
                    extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                    interactiveObj.status_Q5 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T1 += 10;
                    levelWiseScore += 10;
                    Ques6_visible = 1;
                }
                else
                {
                    interactiveObj.incorrect_Ques5_attempt1 = 1;
                    ////////////alert("Incorrect Answer");
                    extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_FirstAttempt").focus();
                }
            }
            if (interactiveObj.attempt_Ques5 == 2)
            {
                if (interactiveObj.box_1 == interactiveObj.number5_1 && interactiveObj.box_2 == interactiveObj.number5_2 && interactiveObj.box_3 == interactiveObj.modulus5)
                {
                    interactiveObj.correct_Ques5_attempt2 = 1;
                    interactiveObj.status_Q5 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T1 += 5;
                    levelWiseScore += 5;
                    Ques6_visible = 1;
                    ////////////alert("correctAnswer");	
                 extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                }
                else
                {
                    interactiveObj.incorrect_Ques5_attempt2 = 1;
                    ////////////alert("Incorrect Answer");
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_secondAttempt").focus();
                }
            }
            if (interactiveObj.attempt_Ques5 == 3)
            {
                if (interactiveObj.box_1 == interactiveObj.number5_1 && interactiveObj.box_2 == interactiveObj.number5_2 && interactiveObj.box_3 == interactiveObj.modulus5)
                {
                    interactiveObj.correct_Ques5_attempt3 = 1;
                    ////////////alert("correctAnswer");	
                    interactiveObj.status_Q5 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T1 += 5;
                    levelWiseScore += 5;
                    Ques6_visible = 1;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                }
                else
                {
                    interactiveObj.incorrect_Ques5_attempt3 = 1;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#Incorrect_thirdAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_thirdAttempt").focus();
                }
            }
        }
        if (called2 == '1')
        {
            //////////alert("Q5 checking called type 2")
            html2 = "";
            $("#prompts").html("");

            interactiveObj.attempt_Ques5 += 1;
            interactiveObj.box_1 = parseInt($("#box_5_1").val());
            interactiveObj.box_2 = parseInt($("#box_5_2").val());
            interactiveObj.box_3 = parseInt($("#box_5_3").val());
            interactiveObj.modulus5 = parseInt(100);

            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_FirstAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + interactiveObj.number5_Final + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>&nbsp;' + replaceDynamicText(promptArr['txt_8'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_secondAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_thirdAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><br/><button class="buttonPrompt_Incorrect_thirdAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            $("#prompts").html(html2);
            $("#wellDone_LF").draggable({containment: "#container"});
            $("#Incorrect_FirstAttempt").draggable({containment: "#container"});
            $("#Incorrect_SecondAttempt").draggable({containment: "#container"});
            $("#Incorrect_thirdAttempt").draggable({containment: "#container"});

            if (interactiveObj.attempt_Ques5 == 1)
            {
                if (interactiveObj.box_1 == interactiveObj.number5_1 && interactiveObj.box_2 == interactiveObj.number5_2 && interactiveObj.box_3 == interactiveObj.modulus5)
                {
                    interactiveObj.correct_Ques5_attempt1 = 1;
                    ////////////alert("correctAnswer");
                    extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";	
                    interactiveObj.status_Q5 = 1;
                    Ques6_visible = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T2 += 10;
                    levelWiseScore += 10;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                }
                else
                {
                    interactiveObj.incorrect_Ques5_attempt1 = 1;
                    ////////////alert("Incorrect Answer");
                    extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_FirstAttempt").focus();
                }
            }
            if (interactiveObj.attempt_Ques5 == 2)
            {
                if (interactiveObj.box_1 == interactiveObj.number5_1 && interactiveObj.box_2 == interactiveObj.number5_2 && interactiveObj.box_3 == interactiveObj.modulus5)
                {
                    interactiveObj.correct_Ques5_attempt2 = 1;
                    ////////////alert("correctAnswer");	
                    interactiveObj.status_Q5 = 1;
                    Ques6_visible = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T2 += 5;
                    levelWiseScore += 5;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                }
                else
                {
                    interactiveObj.incorrect_Ques5_attempt2 = 1;
                    ////////////alert("Incorrect Answer");
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_secondAttempt").focus();
                }
            }
            if (interactiveObj.attempt_Ques5 == 3)
            {
                if (interactiveObj.box_1 == interactiveObj.number5_1 && interactiveObj.box_2 == interactiveObj.number5_2 && interactiveObj.box_3 == interactiveObj.modulus5)
                {
                    interactiveObj.correct_Ques5_attempt3 = 1;
                    ////////////alert("correctAnswer");	
                    interactiveObj.status_Q5 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T2 += 5;
                    levelWiseScore += 5;
                    Ques6_visible = 1;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                }
                else
                {
                    interactiveObj.incorrect_Ques5_attempt3 = 1;
                    ////////////alert("Incorrect Answer");
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#Incorrect_thirdAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_thirdAttempt").focus();
                }
            }
        }
        if (called3 == '1')
        {
           
            html2 = "";
            $("#prompts").html("");
        
            interactiveObj.attempt_Ques5 += 1;
            interactiveObj.number1 = createFrac(interactiveObj.number5_2, interactiveObj.modulus5);
           //interactiveObj.modulus5 = parseInt(100);

            if(interactiveObj.number5_2>=1 && interactiveObj.number5_2<=9)
            {
                interactiveObj.modulus5 = parseInt(10);
            }
            else
            {
                interactiveObj.modulus5 = parseInt(100);
            }

            html2 += '<div id="Incorrect_FirstAttempt" class="correct" style="width: 320px;height: 115px;"><div class="sparkie"></div><div class="Incorrect" style="width: 245px;">' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; ' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp; ' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; ' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" style="top: 90px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div id="text_Incorrect_SecondAttempt">' + replaceDynamicText(interactiveObj.number5_Final, interactiveObj.numberLanguage, "interactiveObj") + '=' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_Incorrect_secondAttempt" style="top: 69px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="NLF" class="correct"><div class="sparkie" style="display:inline-block;"></div><div id="text_NLF"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_NLF" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;'+createFrac(interactiveObj.number5_2,interactiveObj.modulus5)+'&nbsp;' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button  onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="fraction_text">' + replaceDynamicText(promptArr['txt_22'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="animation2"></div><div id="animation3"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="position: absolute;top: 300px;left: 129px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</div></button></div>';
            html2 += '<div id="correctAnswer_Finally" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">Correct answer is&nbsp;' + replaceDynamicText(interactiveObj.number5_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number5_2 / interactiveObj.hcf_5, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus5 / interactiveObj.hcf_5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.</div><br/><button class="buttonPrompt" style="left: 98px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="closeAnswer" class="correct"><div class="sparkie"></div><div id="closeText">'+promptArr['txt_42']+'</div><button class="buttonPrompt" style="left: 111px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            $("#prompts").html(html2);
            $(".correct").draggable({containment: "#container"});

            if (interactiveObj.attempt_Ques5 == 1)
            {
                //////////alert("in attempt 1")
                interactiveObj.box_1 = parseInt($("#box_5_1").val());
                interactiveObj.box_2 = parseInt($("#box_5_2").val());
                interactiveObj.box_3 = parseInt($("#box_5_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques5 = interactiveObj.number5_Final;

                //check for correct answer
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques5)
                {
                  
                    //check for lowest form
                    interactiveObj.correct_Q5_A1 = 1;
                    interactiveObj.Q5_C_NLF_A1=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));     
                    
                    if (interactiveObj.Q5_C_NLF_A1 == 1)
                    {
                       extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                    }
                    else
                    {
                        extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

                        interactiveObj.Q5_C_LF_A1 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 += 10;
                        levelWiseScore += 10;
                        interactiveObj.status_Q5 = 1;
                        //Ques6_visible=1;	
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else if(interactiveObj.local != interactiveObj.correctAnswer_Ques5 || interactiveObj.box_1==interactiveObj.number5_1)   // incorrect answer
                {
                    interactiveObj.closeCounter3+=1;
                    if(interactiveObj.box_1==interactiveObj.number5_1 && interactiveObj.closeCounter3<=2)
                    {
                        extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        //interactiveObj.incorrect_Q5_A1 = 1;
                        interactiveObj.attempt_Ques5 -= 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#closeAnswer").css('visibility', 'visible');
                        $(".buttonPrompt").focus();    
                    }
                    else
                    {
                        extraParameterArr[0]+="(Q5:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        interactiveObj.incorrect_Q5_A1 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                        $(".buttonPrompt_Incorrect_FirstAttempt").focus();    
                    }
                }
            }
            if (interactiveObj.attempt_Ques5 == 2)
            {
                if (interactiveObj.incorrect_Q5_A1 == 1)
                {
                    interactiveObj.box_1 = parseInt($("#box_5_1").val());
                    interactiveObj.box_2 = parseInt($("#box_5_2").val());
                    interactiveObj.box_3 = parseInt($("#box_5_3").val());
                    interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                    interactiveObj.correctAnswer_Ques5 = interactiveObj.number5_Final;

                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques5)
                    {
                       
                        interactiveObj.correct_Q5_A2 = 1;
                        //correct then check for lowest form
                       /* if (interactiveObj.box_2 % interactiveObj.hcf_5 == 0 && interactiveObj.box_3 % interactiveObj.modulus5 == 0)
                        {
                            interactiveObj.Q5_C_NLF_A2 = 1;  // correct but not in lowest form
                        }*/
                      interactiveObj.Q5_C_NLF_A2=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));       
                        if (interactiveObj.Q5_C_NLF_A2 == 1)
                        {
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_NLF").css('visibility', 'visible');
                            $(".buttonPrompt2").focus();
                        }
                        else
                        {
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                            interactiveObj.Q5_C_LF_A2 = 1;
                            interactiveObj.correctCounter += 1;
                            interactiveObj.score_T3 += 5;
                            levelWiseScore += 5;
                            interactiveObj.status_Q5 = 1;
                            //Ques6_visible=1;		
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else // incorrect answer
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        interactiveObj.incorrect_Q5_A2 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                        $(".buttonPrompt_Incorrect_secondAttempt").focus();

                    }

                }
                else
                {// when first attempt was correct but was not in lowest form
                    ////alert("taking input from two boxes attempt 2")
                    interactiveObj.box_1 = parseInt($("#box_5_1_1").val());
                    interactiveObj.box_2 = parseInt($("#box_5_1_2").val());
                    interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                    interactiveObj.correctAnswer_Ques5 = interactiveObj.number5_2 / interactiveObj.modulus5;
                    // continue frm here
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques5)
                    {
                        //checking for lowest form
                        if (interactiveObj.box_1 % interactiveObj.hcf_5 == 0 && interactiveObj.box_2 % interactiveObj.hcf_5 == 0)
                        {
                            interactiveObj.Q5_C2_C_NLF_A2 = 1;
                        }
                        if (interactiveObj.Q5_C2_C_NLF_A2 == 1)
                        {
                            $(".Input_Box").attr('disabled', true);
                            $("#NLF").css('visibility', 'visible');
                            $(".buttonPrompt_NLF").focus();
                        }
                        else
                        {
                            interactiveObj.Q5_C2_C_LF_A2 = 1;
                            interactiveObj.status_Q5 = 1;
                            //Ques6_visible=1;	
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();

                        }
                    }
                    else // if answer is wrong
                    {
                        interactiveObj.incorrect_Q5_A2 = 1;
                        interactiveObj.counter5 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }

                }
            }
            if (interactiveObj.attempt_Ques5 == 3)
            {
                if (interactiveObj.Q5_C2_C_LF_A2 == 1 || (interactiveObj.incorrect_Q5_A2==1 && interactiveObj.counter5==1))// if focus has been shifted to 3 boxes
                {
                    ////alert("Focus shift to three boxes");
                    interactiveObj.box_1 = parseInt($("#box_5_1").val());
                    interactiveObj.box_2 = parseInt($("#box_5_2").val());
                    interactiveObj.box_3 = parseInt($("#box_5_3").val());
                    interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                    interactiveObj.correctAnswer_Ques5 = interactiveObj.number5_Final;

                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques5)
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        if (interactiveObj.box_2 % interactiveObj.hcf_5 == 0 && interactiveObj.box_3 % interactiveObj.hcf_5 == 0)
                        {
                            interactiveObj.Q5_C_NLF_A3 = 1;
                        }
                        if (interactiveObj.Q5_C_NLF_A3 == 1)
                        {
                            $(".Input_Box").attr('disabled', true);
                            $("#correctAnswer_Finally").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                        else// correct and in lowest form
                        {
                            interactiveObj.Q5_C_LF_A3 = 1;
                            interactiveObj.correctCounter += 1;
                            interactiveObj.score_T3 += 5;
                            levelWiseScore += 5;
                            interactiveObj.status_Q5 = 1;
                            //Ques6_visible=1;	
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else// wrong answer
                    {
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        interactiveObj.incorrect_Q5_A3 = 1;
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }

                }
                else// if focus is still on 2 boxes
                {   ////alert("focus on input from two boxes")
                    interactiveObj.box_1 = parseInt($("#box_5_1_1").val());
                    interactiveObj.box_2 = parseInt($("#box_5_1_2").val());
                    interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                    interactiveObj.correctAnswer_Ques5 = interactiveObj.number5_2 / interactiveObj.modulus5;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques5)
                    {
                        if (interactiveObj.box_1 % interactiveObj.hcf_5 == 0 && interactiveObj.box_2 % interactiveObj.hcf_5 == 0)
                        {
                            interactiveObj.C2_Q5_C_NLF_A3 = 1;
                        }
                        if (interactiveObj.C2_Q5_C_NLF_A3 == 1)
                        {//correct but not in lowest form
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_NLF_Final").css('visibility', 'visible');
                            $(".buttonPrompt3").focus();
                            timer = setTimeout("interactiveObj.showAnimation();", 1000);
                        }
                        else// correct and in lowest form
                        {
                            interactiveObj.C2_Q5_C_LF_A3 = 1;
                            //interactiveObj.status_Q5 = 1;
                            //Ques6_visible=1;
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else//incorrect answer
                    {
                        interactiveObj.incorrect_C2_Q5_A3 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }
                }
            }
            if (interactiveObj.attempt_Ques5 == 4)
            {
                interactiveObj.box_1 = parseInt($("#box_5_1").val());
                interactiveObj.box_2 = parseInt($("#box_5_2").val());
                interactiveObj.box_3 = parseInt($("#box_5_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques5 = interactiveObj.number5_Final;
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques5)
                {

                    if (interactiveObj.box_2 % interactiveObj.hcf_5 == 0 && interactiveObj.box_3 % interactiveObj.hcf_5 == 0)
                    {
                        interactiveObj.Q5_C_NLF_A4 = 1;
                    }
                    if (interactiveObj.Q5_C_NLF_A4 == 1)
                    {
                        $(".Input_Box").attr('disabled', true);
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.Q5_C_LF_A4 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 += 5;
                        levelWiseScore += 5;
                        interactiveObj.status_Q5 = 1
                        //Ques6_visible=1;	
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else// wrong answer
                {
                    interactiveObj.incorrect_Q5_A4 = 1;
                    //Ques6_visible=1;	
                    $(".Input_Box").attr('disabled', true);
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
            }
        }
    }
    if (interactiveObj.question_Type == "six")
    {
        if (called21 == '1')
        {
            //////////alert("Q6 checking called type 2")
            html2 = "";
            $("#prompts").html("");

            interactiveObj.attempt_Ques6 += 1;
            interactiveObj.box_1 = parseInt($("#box_6_1").val());
            interactiveObj.box_2 = parseInt($("#box_6_2").val());
            interactiveObj.box_3 = parseInt($("#box_6_3").val());
            interactiveObj.modulus6 = parseInt(100);

            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_FirstAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + interactiveObj.number6_Final + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_5'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>&nbsp;' + replaceDynamicText(promptArr['txt_8'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_secondAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_thirdAttempt" class="correct"><div class="sparkie"></div><div class="Incorrect">' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><br/><button class="buttonPrompt_Incorrect_thirdAttempt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            $("#prompts").html(html2);
            $(".correct").draggable({containment: "#container"});
            
            if (interactiveObj.attempt_Ques6 == 1)
            {
                if (interactiveObj.box_1 == interactiveObj.number6_1 && interactiveObj.box_2 == interactiveObj.number6_2 && interactiveObj.box_3 == interactiveObj.modulus6)
                {
                    interactiveObj.correct_Ques6_attempt1 = 1;
                    ////////////alert("correctAnswer");	
                    interactiveObj.status_Q6 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T1 += 10;
                    levelWiseScore += 10;
                    extraParameterArr[0]+="(Q6:"+interactiveObj.number6_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                    interactiveObj.getResult();
                }
                else
                {
                    extraParameterArr[0]+="(Q6:"+interactiveObj.number6_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    interactiveObj.incorrect_Ques6_attempt1 = 1;
                    $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_FirstAttempt").focus();
                }
            }
            if (interactiveObj.attempt_Ques6 == 2)
            {
                if (interactiveObj.box_1 == interactiveObj.number6_1 && interactiveObj.box_2 == interactiveObj.number6_2 && interactiveObj.box_3 == interactiveObj.modulus6)
                {
                    interactiveObj.correct_Ques6_attempt2 = 1;
                    ////////////alert("correctAnswer");

                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

                    interactiveObj.status_Q6 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T1 += 5;
                    levelWiseScore += 5;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                    interactiveObj.getResult();
                }
                else
                {
                    interactiveObj.incorrect_Ques6_attempt2 = 1;
                     extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                    ////////////alert("Incorrect Answer");
                    $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_secondAttempt").focus();
                }
            }
            if (interactiveObj.attempt_Ques6 == 3)
            {
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";

                if (interactiveObj.box_1 == interactiveObj.number6_1 && interactiveObj.box_2 == interactiveObj.number6_2 && interactiveObj.box_3 == interactiveObj.modulus6)
                {
                    interactiveObj.correct_Ques6_attempt3 = 1;
                    ////////////alert("correctAnswer");	
                    interactiveObj.status_Q6 = 1;
                    interactiveObj.correctCounter += 1;
                    interactiveObj.score_T1 += 5;
                    levelWiseScore += 5;



                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt").focus();
                    interactiveObj.getResult();
                }
                else
                {
                    interactiveObj.incorrect_Ques6_attempt3 = 1;
                    ////////////alert("Incorrect Answer");
                    $("#Incorrect_thirdAttempt").css('visibility', 'visible');
                    $(".Input_Box").attr('disabled', true);
                    $(".buttonPrompt_Incorrect_thirdAttempt").focus();
                    interactiveObj.getResult();
                }
            }
        }
        if (called31 == '1')
        {
            //////////alert("Q6 checking of type 3")
            html2 = "";
            $("#prompts").html("");
            ////////////alert("Inside Type Three checking")
            interactiveObj.attempt_Ques6 += 1;
            interactiveObj.number1 = createFrac(interactiveObj.number6_2, interactiveObj.modulus6);
           // interactiveObj.modulus6 = parseInt(100);

            if(interactiveObj.number6_2>=1 && interactiveObj.number6_2<=9)
            {
                interactiveObj.modulus6 = parseInt(10);
            }
            else
            {
                interactiveObj.modulus6 = parseInt(100);
            }

            html2 += '<div id="Incorrect_FirstAttempt" class="correct" style="width: 320px;height: 115px;"><div class="sparkie"></div><div class="Incorrect" style="width: 245px;">' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_10'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; ' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp; ' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; ' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" style="top: 90px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div id="text_Incorrect_SecondAttempt">' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '=' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_Incorrect_secondAttempt" style="top: 69px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="NLF" class="correct"><div class="sparkie" style="display:inline-block;"></div><div id="text_NLF"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_14'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_NLF" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_4'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;'+createFrac(interactiveObj.number6_2,interactiveObj.modulus6)+'&nbsp;' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button  onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="fraction_text">' + replaceDynamicText(promptArr['txt_22'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="animation2"></div><div id="animation3"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="position: absolute;top: 300px;left: 129px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</div></button></div>';
            html2 += '<div id="correctAnswer_Finally" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">Correct answer is&nbsp;' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2 / interactiveObj.hcf_6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6 / interactiveObj.hcf_6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><br/><button class="buttonPrompt" style="left: 98px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="closeAnswer" class="correct"><div class="sparkie"></div><div id="closeText">'+promptArr['txt_42']+'</div><button class="buttonPrompt" style="left: 111px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            $("#prompts").html(html2);
            $(".correct").draggable({containment: "#container"});


            if (interactiveObj.attempt_Ques6 == 1)
            {
                //////////alert("in attempt 1")
                interactiveObj.box_1 = parseInt($("#box_6_1").val());
                interactiveObj.box_2 = parseInt($("#box_6_2").val());
                interactiveObj.box_3 = parseInt($("#box_6_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;

                //check for correct answer
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                {
                    //check for lowest form

                    interactiveObj.correct_Q6_A1 = 1;
                    interactiveObj.Q6_C_NLF_A1=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));     

                    if (interactiveObj.Q6_C_NLF_A1 == 1)
                    {
                      extraParameterArr[0]+="(Q6:"+interactiveObj.number6_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                    }
                    else
                    {
                        interactiveObj.Q6_C_LF_A1 = 1;
                        interactiveObj.status_Q6 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 += 10;
                        levelWiseScore += 10;
                        extraParameterArr[0]+="(Q6:"+interactiveObj.number6_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        interactiveObj.getResult();
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else if(interactiveObj.local!=interactiveObj.correctAnswer_Ques6 || interactiveObj.box_1==interactiveObj.number6_1)  // incorrect answer
                {
                    interactiveObj.closeCounter4+=1;

                    if(interactiveObj.box_1==interactiveObj.number6_1 && interactiveObj.closeCounter4<=2)
                    {
                       
                       // interactiveObj.incorrect_Q6_A1 = 1;
                       interactiveObj.attempt_Ques6 -= 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#closeAnswer").css('visibility', 'visible');
                        $(".buttonPrompt").focus(); 

                    }
                    else
                    {
                        extraParameterArr[0]+="(Q6:"+interactiveObj.number6_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        interactiveObj.incorrect_Q6_A1 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                        $(".buttonPrompt_Incorrect_FirstAttempt").focus();
                    }

                    
                }
            }
            if (interactiveObj.attempt_Ques6 == 2)
            {
                if (interactiveObj.incorrect_Q6_A1 == 1)
                {
                    interactiveObj.box_1 = parseInt($("#box_6_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_2").val());
                    interactiveObj.box_3 = parseInt($("#box_6_3").val());
                    interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;

                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        interactiveObj.correct_Q6_A2 = 1;
                        //correct then check for lowest form
                       /* if (interactiveObj.box_2 % interactiveObj.hcf_6 == 0 && interactiveObj.box_3 % interactiveObj.modulus6 == 0)
                        {
                            interactiveObj.Q6_C_NLF_A2 = 1;  // correct but not in lowest form
                        }*/

                      interactiveObj.Q6_C_NLF_A2=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));     
                        
                        if (interactiveObj.Q6_C_NLF_A2 == 1)
                        {
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_NLF").css('visibility', 'visible');
                            $(".buttonPrompt2").focus();
                        }
                        else
                        {
                            interactiveObj.Q6_C_LF_A2 = 1;
                            interactiveObj.status_Q6 = 1;
                            interactiveObj.correctCounter += 1;
                            interactiveObj.score_T3 += 5;
                            levelWiseScore += 5;
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                            interactiveObj.getResult();
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else // incorrect answer
                    {
                        interactiveObj.incorrect_Q6_A2 = 1;
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                        $(".buttonPrompt_Incorrect_secondAttempt").focus();

                    }

                }
                else
                {// when first attempt was correct but was not in lowest form
                    //////////alert("taking input from twi boxes")
                    interactiveObj.box_1 = parseInt($("#box_6_1_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_1_2").val());
                    interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_2 / interactiveObj.modulus6;
                    // continue frm here
                     extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        //checking for lowest form
                        if (interactiveObj.box_1 % interactiveObj.hcf_6 == 0 && interactiveObj.box_2 % interactiveObj.hcf_6 == 0)
                        {
                            interactiveObj.Q6_C2_C_NLF_A2 = 1;
                        }
                        if (interactiveObj.Q6_C2_C_NLF_A2 == 1)
                        {
                            $(".Input_Box").attr('disabled', true);
                            $("#NLF").css('visibility', 'visible');
                            $(".buttonPrompt_NLF").focus();
                        }
                        else
                        {
                            interactiveObj.Q6_C2_C_LF_A2 = 1;
                            interactiveObj.status_Q6 = 1;
                            //Ques6_visible=1;	
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();

                        }
                    }
                    else // if answer is wrong
                    {
                        interactiveObj.incorrect_Q6_A2 = 1;
                        interactiveObj.counter6 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }

                }
            }
            if (interactiveObj.attempt_Ques6 == 3)
            {
                if (interactiveObj.Q6_C2_C_LF_A2 == 1||(interactiveObj.incorrect_Q6_A2==1 && interactiveObj.counter6==1))// if focus has been shifted to 3 boxes
                {
                    //////////alert("Focus shift to three boxes");
                    interactiveObj.box_1 = parseInt($("#box_6_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_2").val());
                    interactiveObj.box_3 = parseInt($("#box_6_3").val());
                    interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;

                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        if (interactiveObj.box_2 % interactiveObj.hcf_6 == 0 && interactiveObj.box_3 % interactiveObj.hcf_6 == 0)
                        {
                            interactiveObj.Q6_C_NLF_A3 = 1;
                        }
                        if (interactiveObj.Q6_C_NLF_A3 == 1)
                        {
                            $(".Input_Box").attr('disabled', true);
                            $("#correctAnswer_Finally").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                        else// correct and in lowest form
                        {
                            interactiveObj.Q6_C_LF_A3 = 1;
                            interactiveObj.correctCounter += 1;
                            interactiveObj.score_T3 += 5;
                            levelWiseScore += 5;
                            interactiveObj.status_Q6 = 1;
                            interactiveObj.getResult();
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else// wrong answer
                    {
                        interactiveObj.incorrect_Q6_A3 = 1;
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        $(".Input_Box").attr('disabled', true);
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }

                }
                else// if focus is still on 2 boxes
                {//////////alert("focus on input from two bpxes")
                    interactiveObj.box_1 = parseInt($("#box_6_1_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_1_2").val());
                    interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_2 / interactiveObj.modulus6;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        if (interactiveObj.box_1 % interactiveObj.hcf_6 == 0 && interactiveObj.box_2 % interactiveObj.hcf_6 == 0)
                        {
                            interactiveObj.C2_Q6_C_NLF_A3 = 1;
                        }
                        if (interactiveObj.C2_Q6_C_NLF_A3 == 1)
                        {//correct but not in lowest form
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_NLF_Final").css('visibility', 'visible');
                            $(".buttonPrompt3").focus();
                            timer = setTimeout("interactiveObj.showAnimation();", 1000);
                        }
                        else// correct and in lowest form
                        {
                            interactiveObj.C2_Q6_C_LF_A3 = 1;
                            interactiveObj.status_Q6 = 1;
                            //Ques6_visible=1;
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else//incorrect answer
                    {
                        interactiveObj.incorrect_C2_Q6_A3 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }
                }
            }
            if (interactiveObj.attempt_Ques6 == 4)
            {
                interactiveObj.box_1 = parseInt($("#box_6_1").val());
                interactiveObj.box_2 = parseInt($("#box_6_2").val());
                interactiveObj.box_3 = parseInt($("#box_6_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;
                 extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                {
                    if (interactiveObj.box_2 % interactiveObj.hcf_6 == 0 && interactiveObj.box_3 % interactiveObj.hcf_6 == 0)
                    {
                        interactiveObj.Q6_C_NLF_A4 = 1;
                    }
                    if (interactiveObj.Q6_C_NLF_A4 == 1)
                    {
                        interactiveObj.getResult();
                        $(".Input_Box").attr('disabled', true);
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.Q6_C_LF_A4 = 1;
                        interactiveObj.status_Q6 = 1
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T3 += 5;
                        levelWiseScore += 5;
                        interactiveObj.getResult();
                      
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else// wrong answer
                {
                    interactiveObj.incorrect_Q6_A4 = 1;
                    //Ques6_visible=1;	
                    interactiveObj.getResult();
                    $(".Input_Box").attr('disabled', true);
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }

            }
        }
        if (called41 == '1')
        {
            html2 = "";
            $("#prompts").html("");
            //////////alert("Q6 type 4 checking")
            interactiveObj.attempt_Ques6 += 1;
            interactiveObj.number1 = createFrac(interactiveObj.number6_2, interactiveObj.modulus6);
            interactiveObj.modulus6 = parseInt(100);

            html2 += '<div id="Incorrect_FirstAttempt" class="correct" style="width: 320px;height: 115px;"><div class="sparkie"></div><div class="Incorrect" style="width: 245px;">' + replaceDynamicText(promptArr['txt_9'], interactiveObj.numberLanguage, "interactiveObj") + ' &nbsp;' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp; as&nbsp; ' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;+&nbsp;0.' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '.&nbsp; ' + replaceDynamicText(promptArr['txt_12'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(promptArr['txt_11'], interactiveObj.numberLanguage, "interactiveObj") + '</div><br/><button class="buttonPrompt_Incorrect_FirstAttempt" style="top: 90px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="Incorrect_SecondAttempt" class="correct"><div class="sparkie"></div><div id="text_Incorrect_SecondAttempt">' + replaceDynamicText(interactiveObj.number6_Final, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>.&nbsp;&nbsp;' + replaceDynamicText(promptArr['txt_3'], interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button class="buttonPrompt_Incorrect_secondAttempt" style="top: 69px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="NLF" class="correct"><div class="sparkie" style="display:inline-block;"></div><div id="text_NLF"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.modulus6, interactiveObj.numberLanguage, "interactiveObj") + '</div></div>can be further reduced</div><button class="buttonPrompt_NLF" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">Well Done</div><br/><button class="buttonPrompt" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_NLF" class="correct"><div class="sparkie"></div><div class="textCorrect_wellDone_NLF">' + replaceDynamicText(promptArr['txt_15'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;'+createFrac(interactiveObj.number6_2,100)+'&nbsp;' + replaceDynamicText(promptArr['txt_13'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button  onclick=interactiveObj.loadQuestions(); class="buttonPrompt2">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="wellDone_NLF_Final" class="correct"><div id="fraction_text">' + replaceDynamicText(promptArr['txt_22'], interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="animation2"></div><div id="animation3"></div><button id="button_3" onclick=interactiveObj.loadQuestions(); class="buttonPrompt3" style="position: absolute;top: 300px;left: 129px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</div></button></div>';
            html2 += '<div id="correctAnswer_Finally" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">' + replaceDynamicText(promptArr['txt_16'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;' + replaceDynamicText(interactiveObj.number6_1, interactiveObj.numberLanguage, "interactiveObj") + '<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.number6_2 / interactiveObj.hcf_6, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + interactiveObj.modulus6 / interactiveObj.hcf_6 + '</div></div></div><br/><button class="buttonPrompt" style="left: 98px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            html2 += '<div id="closeAnswer" class="correct"><div class="sparkie"></div><div id="closeText">'+promptArr['txt_42']+'</div><button class="buttonPrompt" style="left: 111px;" onclick=interactiveObj.loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            $("#prompts").html(html2);
            $(".correct").draggable({containment: "#container"});

            if (interactiveObj.attempt_Ques6 == 1)
            {
                //////////alert("in attempt 1")
                interactiveObj.box_1 = parseInt($("#box_6_1").val());
                interactiveObj.box_2 = parseInt($("#box_6_2").val());
                interactiveObj.box_3 = parseInt($("#box_6_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;

                //check for correct answer
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                {
                    //check for lowest form
                    interactiveObj.correct_Q6_A1 = 1;
              
                    interactiveObj.Q6_C_NLF_A1=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));     

                    if (interactiveObj.Q6_C_NLF_A1 == 1)
                    {
                        extraParameterArr[0]+="(Q6:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF").css('visibility', 'visible');
                        $(".buttonPrompt2").focus();
                    }
                    else
                    {
                        interactiveObj.Q6_C_LF_A1 = 1;
                        interactiveObj.status_Q6 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T4 += 10;
                        levelWiseScore += 10;
                        interactiveObj.getResult();
                        extraParameterArr[0]+="(Q6:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else if(interactiveObj.local!=interactiveObj.correctAnswer_Ques6 || interactiveObj.box_1==interactiveObj.number6_1)   // incorrect answer
                {
                    interactiveObj.closeCounter4+=1;
                    if(interactiveObj.box_1==interactiveObj.number6_1 && interactiveObj.closeCounter4<=2)
                    {
                        //interactiveObj.incorrect_Q6_A1 = 1;
                        interactiveObj.attempt_Ques6 -= 1;
                        
                        $(".Input_Box").attr('disabled', true);
                        $("#closeAnswer").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                    else
                    {
                       interactiveObj.incorrect_Q6_A1 = 1;
                        extraParameterArr[0]+="(Q6:"+interactiveObj.number5_Final+":"+interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $(".Input_Box").attr('disabled', true);
                        $("#Incorrect_FirstAttempt").css('visibility', 'visible');
                        $(".buttonPrompt_Incorrect_FirstAttempt").focus(); 
                    }
                }
            }
            if (interactiveObj.attempt_Ques6 == 2)
            {
                if (interactiveObj.incorrect_Q4_A1 == 1)
                {
                    interactiveObj.box_1 = parseInt($("#box_6_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_2").val());
                    interactiveObj.box_3 = parseInt($("#box_6_3").val());
                    interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;

                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        interactiveObj.correct_Q6_A2 = 1;
                        //correct then check for lowest form
                        /*if (interactiveObj.box_2 % interactiveObj.hcf_6 == 0 && interactiveObj.box_4 % interactiveObj.modulus6 == 0)
                        {
                            interactiveObj.Q6_C_NLF_A2 = 1;  // correct but not in lowest form
                        }*/

                        interactiveObj.Q6_C_NLF_A2=interactiveObj.setFlagLowestForm(parseInt(interactiveObj.box_2),parseInt(interactiveObj.box_3));     

                        if (interactiveObj.Q6_C_NLF_A2 == 1)
                        {
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_NLF").css('visibility', 'visible');
                            $(".buttonPrompt2").focus();
                        }
                        else
                        {
                            interactiveObj.Q6_C_LF_A2 = 1;
                            interactiveObj.status_Q6 = 1;
                            interactiveObj.correctCounter += 1;
                            interactiveObj.score_T4 += 5;
                            levelWiseScore += 5;
                            interactiveObj.getResult();
                            extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else // incorrect answer
                    {
                        interactiveObj.incorrect_Q6_A2 = 1;
                        $(".Input_Box").attr('disabled', true);
                        extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+"~";
                        $("#Incorrect_SecondAttempt").css('visibility', 'visible');
                        $(".buttonPrompt_Incorrect_secondAttempt").focus();

                    }

                }
                else
                {// when first attempt was correct but was not in lowest form
                    //////////alert("taking input from twi boxes")
                    interactiveObj.box_1 = parseInt($("#box_6_1_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_1_2").val());
                    interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_2 / interactiveObj.modulus6;
                    // continue frm here
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        //checking for lowest form
                        if (interactiveObj.box_1 % interactiveObj.hcf_6 == 0 && interactiveObj.box_2 % interactiveObj.hcf_6 == 0)
                        {
                            interactiveObj.Q6_C2_C_NLF_A2 = 1;
                        }
                        if (interactiveObj.Q6_C2_C_NLF_A2 == 1)
                        {
                            $(".Input_Box").attr('disabled', true);
                            $("#NLF").css('visibility', 'visible');
                            $(".buttonPrompt_NLF").focus();
                        }
                        else
                        {
                            interactiveObj.Q6_C2_C_LF_A2 = 1;
                            interactiveObj.status_Q6 = 1;
                            //interactiveObj.getStats();
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();

                        }
                    }
                    else // if answer is wrong
                    {
                        interactiveObj.incorrect_Q6_A2 = 1;
                        interactiveObj.counter6 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }

                }
            }
            if (interactiveObj.attempt_Ques6 == 3)
            {
                if (interactiveObj.Q6_C2_C_LF_A2 == 1 || (interactiveObj.incorrect_Q6_A2==1 && interactiveObj.counter6==1))// if focus has been shifted to 3 boxes
                {
                    //////////alert("Focus shift to three boxes");
                    interactiveObj.box_1 = parseInt($("#box_6_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_2").val());
                    interactiveObj.box_3 = parseInt($("#box_6_3").val());
                    interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        if (interactiveObj.box_2 % interactiveObj.hcf_6 == 0 && interactiveObj.box_3 % interactiveObj.hcf_6 == 0)
                        {
                            interactiveObj.Q6_C_NLF_A3 = 1;
                        }
                        if (interactiveObj.Q6_C_NLF_A3 == 1)
                        {
                            $(".Input_Box").attr('disabled', true);
                            $("#correctAnswer_Finally").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                        else// correct and in lowest form
                        {
                            interactiveObj.Q6_C_LF_A3 = 1;
                            interactiveObj.status_Q6 = 1;
                            interactiveObj.correctCounter += 1;
                            interactiveObj.score_T4 += 5;
                            levelWiseScore += 5;
                            interactiveObj.getResult();
                           
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else// wrong answer
                    {
                        interactiveObj.incorrect_Q6_A3 = 1;
                        $(".Input_Box").attr('disabled', true);
                        
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }

                }
                else// if focus is still on 2 boxes
                {//////////alert("focus on input from two bpxes")
                    interactiveObj.box_1 = parseInt($("#box_6_1_1").val());
                    interactiveObj.box_2 = parseInt($("#box_6_1_2").val());
                    interactiveObj.local = interactiveObj.box_1 / interactiveObj.box_2;
                    interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_2 / interactiveObj.modulus6;
                    extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+"~";
                    if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                    {
                        if (interactiveObj.box_1 % interactiveObj.hcf_6 == 0 && interactiveObj.box_2 % interactiveObj.hcf_6 == 0)
                        {
                            interactiveObj.C2_Q6_C_NLF_A3 = 1;
                        }
                        if (interactiveObj.C2_Q6_C_NLF_A3 == 1)
                        {//correct but not in lowest form
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_NLF_Final").css('visibility', 'visible');
                            $(".buttonPrompt3").focus();
                            timer = setTimeout("interactiveObj.showAnimation();", 1000);
                        }
                        else// correct and in lowest form
                        {
                            interactiveObj.C2_Q6_C_LF_A3 = 1;
                            interactiveObj.status_Q6 = 1;
                            //interactiveObj.getStats();		
                            $(".Input_Box").attr('disabled', true);
                            $("#wellDone_LF").css('visibility', 'visible');
                            $(".buttonPrompt").focus();
                        }
                    }
                    else//incorrect answer
                    {
                        interactiveObj.incorrect_C2_Q6_A3 = 1;
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_NLF_Final").css('visibility', 'visible');
                        $(".buttonPrompt3").focus();
                        timer = setTimeout("interactiveObj.showAnimation();", 1000);
                    }
                }
            }
            if (interactiveObj.attempt_Ques6 == 4)
            {
                interactiveObj.box_1 = parseInt($("#box_6_1").val());
                interactiveObj.box_2 = parseInt($("#box_6_2").val());
                interactiveObj.box_3 = parseInt($("#box_6_3").val());
                interactiveObj.local = ((interactiveObj.box_3 * interactiveObj.box_1) + interactiveObj.box_2) / interactiveObj.box_3;
                interactiveObj.correctAnswer_Ques6 = interactiveObj.number6_Final;
                extraParameterArr[0]+=interactiveObj.box_1+","+interactiveObj.box_2+","+interactiveObj.box_3+")";
                if (interactiveObj.local == interactiveObj.correctAnswer_Ques6)
                {
                    if (interactiveObj.box_2 % interactiveObj.hcf_6 == 0 && interactiveObj.box_3 % interactiveObj.hcf_6 == 0)
                    {
                        interactiveObj.Q6_C_NLF_A4 = 1;
                    }
                    if (interactiveObj.Q6_C_NLF_A4 == 1)
                    {
                        $(".Input_Box").attr('disabled', true);
                        $("#correctAnswer_Finally").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                    else// correct and in lowest form
                    {
                        interactiveObj.Q6_C_LF_A4 = 1;
                        interactiveObj.status_Q6 = 1;
                        interactiveObj.correctCounter += 1;
                        interactiveObj.score_T4 += 5;
                        levelWiseScore += 5;
                        interactiveObj.getResult();
                        
                        $(".Input_Box").attr('disabled', true);
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                    }
                }
                else// wrong answer
                {
                    interactiveObj.incorrect_Q6_A4 = 1;
                    interactiveObj.getResult();
                   
                    $(".Input_Box").attr('disabled', true);
                    $("#correctAnswer_Finally").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
            }
        }
    }

}//closing

questionInteractive.prototype.setFlagLowestForm=function(n1,n2)
{
   
    var reminder;
    while(n2!=0){
        reminder = n1 % n2;
        n1 = n2;
        n2 = reminder;
    }
    var gcd = n1;
    if(gcd==1)
    {
      return 0 ;
    }       
        
    else 
    {
      return 1;
    }
}
questionInteractive.prototype.showAnimation = function()
{
    ////////////alert("In show Animation");
    if (interactiveObj.question_Type == "three")// done
    {
        interactiveObj.text0 = interactiveObj.number3_Final;
        interactiveObj.text1 = interactiveObj.number3_2;
        interactiveObj.text2 = interactiveObj.text1 / interactiveObj.hcf_3;
        interactiveObj.text3 = interactiveObj.text1;
        interactiveObj.text4 = interactiveObj.modulus3;
        interactiveObj.text5 = interactiveObj.hcf_3;
        interactiveObj.text6 = interactiveObj.text4 / interactiveObj.hcf_3;

        if(interactiveObj.number3_2>=1&&interactiveObj.number3_2<=9)
        {
            interactiveObj.text7=(interactiveObj.text1 / interactiveObj.text4);
        }
        else
        {
            interactiveObj.text7=(interactiveObj.text1 / interactiveObj.text4).toFixed(2);   
        }
    }
    if (interactiveObj.question_Type == "four")//done
    {
        interactiveObj.text0 = interactiveObj.number4_Final;
        interactiveObj.text1 = interactiveObj.number4_2;
        interactiveObj.text2 = interactiveObj.text1 / interactiveObj.hcf_4;
        interactiveObj.text3 = interactiveObj.text1;
        interactiveObj.text4 = 100;
        interactiveObj.text5 = interactiveObj.hcf_4;
        interactiveObj.text6 = interactiveObj.text4 / interactiveObj.hcf_4;
        interactiveObj.text7 = (interactiveObj.text1 / interactiveObj.text4).toFixed(2);

    }

    if (interactiveObj.question_Type == "five")
    {

        if (called3 == 1)
        {
            interactiveObj.text0 = interactiveObj.number5_Final;
            interactiveObj.text1 = interactiveObj.number5_2;
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.hcf_5;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = interactiveObj.modulus5;
            interactiveObj.text5 = interactiveObj.hcf_5
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.hcf_5;

            if(interactiveObj.number5_2>=1&&interactiveObj.number5_2<=9)
            {
                interactiveObj.text7=(interactiveObj.text1 / interactiveObj.text4);
            }
            else
            {
                interactiveObj.text7=(interactiveObj.text1 / interactiveObj.text4).toFixed(2);   
            }
        }


    }

    if (interactiveObj.question_Type == "six")
    {
        if (called31 == 1)
        {
            interactiveObj.text0 = interactiveObj.number6_Final;
            interactiveObj.text1 = interactiveObj.number6_2;
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.hcf_6;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = interactiveObj.modulus6;
            interactiveObj.text5 = interactiveObj.hcf_6;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.hcf_6;

             if(interactiveObj.number6_2>=1&&interactiveObj.number6_2<=9)
            {
                interactiveObj.text7=(interactiveObj.text1 / interactiveObj.text4);
            }
            else
            {
                interactiveObj.text7=(interactiveObj.text1 / interactiveObj.text4).toFixed(2);   
            }
        }
        if (called41 == 1)
        {
            interactiveObj.text0 = interactiveObj.number6_Final;
            interactiveObj.text1 = interactiveObj.number6_2;
            interactiveObj.text2 = interactiveObj.text1 / interactiveObj.hcf_6;
            interactiveObj.text3 = interactiveObj.text1;
            interactiveObj.text4 = 100;
            interactiveObj.text5 = interactiveObj.hcf_6;
            interactiveObj.text6 = interactiveObj.text4 / interactiveObj.hcf_6;
            interactiveObj.text7 = (interactiveObj.text1 / interactiveObj.text4).toFixed(2);

        }

    }

    //animation//  
    $("#animation2").delay(1000).animate({'opacity': '1'}, 500);

    $("#animation2").append('<div id="animation_fraction"><div id="fraction_1">' + replaceDynamicText(interactiveObj.text7,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;=&nbsp;</div><div id="fraction_2"><div class="fraction"><div class="frac numerator">' + interactiveObj.text1 + '</div><div class="frac">' + interactiveObj.text4 + '</div></div></div><div id="fraction_3">&nbsp;=&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.text1 / interactiveObj.text5 + '</div><div class="frac">' + interactiveObj.text4 / interactiveObj.text5 + '</div></div></div></div>');



    timer2 = setTimeout("interactiveObj.secondAnimation();", 1000);
}
questionInteractive.prototype.secondAnimation = function()
{

    $("#animation3").delay(1000).animate({
        'opacity': '1'
    }, 500);

    $("#animation3").append('<div id="animation3_text">' + replaceDynamicText(promptArr['txt_17'], interactiveObj.numberLanguage, "interactiveObj") + '' + replaceDynamicText(interactiveObj.text1, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_18'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.text4, interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(promptArr['txt_19'], interactiveObj.numberLanguage, "interactiveObj") + ' ' + replaceDynamicText(interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="strike_upper">/</div><div id="strike_lower">/</div><div id="number_above">' + replaceDynamicText(interactiveObj.text1 / interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="number_below">' + replaceDynamicText(interactiveObj.text4 / interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</div><div id="animation3_fraction"><div id="animation3_fraction1">' + replaceDynamicText(interactiveObj.text7, interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp;</div><div id="animation3_fraction2"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.text1, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.text4, interactiveObj.numberLanguage, "interactiveObj") + '</div></div><div id="EqualTo">=</div></div><div id="animation3_fraction3"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.text1 / interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.text4 / interactiveObj.text5, interactiveObj.numberLanguage, "interactiveObj") + '</div></div></div><div id="replayButton" class="replay" onclick=interactiveObj.Replay();></div></div>');

    $("#animation3_text").delay(1500).animate({
        'opacity': '1'
    }, 500);
    $("#animation3_fraction1").delay(1900).animate({
        'opacity': '1'
    }, 500);
    $("#animation3_fraction2").delay(1900).animate({
        'opacity': '1'
    }, 500);
    $("#strike_upper").delay(2500).animate({
        'opacity': '1'
    }, 500);
    $("#strike_lower").delay(3000).animate({
        'opacity': '1'
    }, 500);
    $("#number_above").delay(2700).animate({
        'opacity': '1'
    }, 500);
    $("#number_below").delay(3100).animate({
        'opacity': '1'
    }, 500);
    $("#EqualTo").delay(3300).animate({
        'opacity': '1'
    }, 500);
    $("#animation3_fraction3").delay(4300).animate({
        'opacity': '1'
    }, 500);
    $("#fraction_3").delay(4900).animate({
        'opacity': '1'
    }, 500);

    $(".replay").delay(5000).animate({
        'opacity': '1'
    }, 500);
}
questionInteractive.prototype.Replay = function()
{
    clearTimeout(timer2);
    $("#animation3").delay(10).animate({
        'opacity': '0'
    }, 50);
    $("#animation3").html("");

    interactiveObj.secondAnimation();
}

questionInteractive.prototype.getStats = function()
{

    stats[0] = interactiveObj.status_Q1;
    stats[1] = interactiveObj.status_Q2;
    stats[2] = interactiveObj.status_Q3;
    stats[3] = interactiveObj.status_Q4;


    for (interactiveObj.i = 0; interactiveObj.i < 4; interactiveObj.i++)
    {
        if (stats[interactiveObj.i] == 0)
        {
            //////////alert("Getting result wrong");
            generateNo += 1;
            Ques[a] = parseInt(interactiveObj.i + 1);
            a++;
        }
    }
    if (generateNo == 0)
    {
        levelWiseStatus = 1;
        completed = 0;
        extraParameterArr[0]+="---->Type Wise Score:("+interactiveObj.score_T1+","+interactiveObj.score_T2+","+interactiveObj.score_T3+","+interactiveObj.score_T4+")";
      //  window.clearInterval(tempController);
    }
    if (generateNo == 1)
    {
        levelWiseStatus = 1;
        completed = 0;
        extraParameterArr[0]+="---->Type Wise Score:("+interactiveObj.score_T1+","+interactiveObj.score_T2+","+interactiveObj.score_T3+","+interactiveObj.score_T4+")";
      //  window.clearInterval(tempController);
    }
    if (generateNo > 1)  // when two questions get wrong
        {
            levelWiseStatus = 0;
            completed = 0;

            Ques5_visible = 1;
            ////////////alert("In get stats")
            GenerateQ5 = Ques[0];
            GenerateQ6 = Ques[1];

        if (GenerateQ5 == 1)
            {
                interactiveObj.randomNumber_Add_1();
                interactiveObj.number5_Final = interactiveObj.Additonal_number_1;
                called1 = 1;
            }
        if (GenerateQ5 == 2)
            {
                interactiveObj.randomNumber_Add_2();
                interactiveObj.number5_Final = interactiveObj.Additonal_number_2;
                called2 = 1;
            }
        if (GenerateQ5 == 3)
            {
                interactiveObj.randomNumber_Add_3();
                interactiveObj.number5_Final = interactiveObj.Additonal_number_3;
                called3 = 1;
            }

    //------------------- Q6 From here----------------------//
        if (GenerateQ6 == 2)
            {
                interactiveObj.randomNumber6_Add_2();
                interactiveObj.number6_Final = interactiveObj.Additonal_number_2;
                called21 = 1;
            }
        if (GenerateQ6 == 3)
            {
                ////////////alert("Generate Type 3")
                interactiveObj.randomNumber6_Add_3();
                interactiveObj.number6_Final = interactiveObj.Additonal_number_3;
                called31 = 1;
            }
        if (GenerateQ6 == 4)
            {
                ////////////alert("Generate Type 4")
                interactiveObj.randomNumber6_Add_4();
                interactiveObj.number6_Final = interactiveObj.Additonal_number_4;
                called41 = 1;
            }

            //////console.log("Generate Type 1=" + interactiveObj.sixth);
            //////console.log("Generate Type 2=" + interactiveObj.seventh);
        }
}
questionInteractive.prototype.getResult = function()
{
  // //////alert("Get Result called");

    if (interactiveObj.correctCounter >= 4)
    {
        levelWiseStatus = 1;
        completed = 0;
      //  window.clearInterval(tempController);
        levelWiseScore = interactiveObj.score_T1 + interactiveObj.score_T2 + interactiveObj.score_T3 + interactiveObj.score_T4;
        extraParameterArr[0]+="---->Type Wise Score:("+interactiveObj.score_T1+","+interactiveObj.score_T2+","+interactiveObj.score_T3+","+interactiveObj.score_T4+")";

    }
    else if (interactiveObj.correctCounter < 4)
    {
        levelWiseStatus = 2;
        completed = 0;
      //  window.clearInterval(tempController);
        levelWiseScore = interactiveObj.score_T1 + interactiveObj.score_T2 + interactiveObj.score_T3 + interactiveObj.score_T4;
        extraParameterArr[0]+="---->Type Wise Score:("+interactiveObj.score_T1+","+interactiveObj.score_T2+","+interactiveObj.score_T3+","+interactiveObj.score_T4+")";
    }
}

function createFrac(x, y)
{

    var m = '<div class="fraction"><div class="frac numerator">' + x + '</div><div class="frac">' + y + '</div></div>';
    return m;
}

//----------------------Level 2 Execution Starts from here---------------------------//

// ----------number generation functions-------------------//
questionInteractive.prototype.L2_RNG_T1 = function()
{
    interactiveObj.L2_T1_1 = L2_Arr_T1_a[Math.floor(Math.random() * L2_Arr_T1_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T1_1, L2_Arr_T1_a);
    L2_Arr_T1_a.splice(tempL2_1, 1);

    interactiveObj.L2_T1_3 = L2_Arr_T1_y[Math.floor(Math.random() * L2_Arr_T1_y.length)]; // Value of Y here, denominator
    var tempL2_3 = $.inArray(interactiveObj.L2_T1_3, L2_Arr_T1_y);
    L2_Arr_T1_y.splice(tempL2_3, 1);

        for (interactiveObj.i = 1; interactiveObj.i < 99; interactiveObj.i++)  // array of numbers from 11-99 created under L2_Arr_T1_x
        {
            L2_Arr_T1_x[interactiveObj.i-1] = interactiveObj.i;
        }

        for (interactiveObj.i = 1; interactiveObj.i <=9; interactiveObj.i++)  // array of numbers from 11-99 created under L2_Arr_T1_x
        {
            L2_Arr_T1_x1[interactiveObj.i-1] = interactiveObj.i;
        }

    if (interactiveObj.L2_T1_3 == 100)
    {

        interactiveObj.L2_T1_2 = L2_Arr_T1_x[Math.floor(Math.random() * L2_Arr_T1_x.length)]; // Value of X
        var tempL2_2 = $.inArray(interactiveObj.L2_T1_2, L2_Arr_T1_x);
        L2_Arr_T1_x.splice(tempL2_2, 1);
    }
    else if (interactiveObj.L2_T1_3 == 10)
    {
    
        interactiveObj.L2_T1_2 = L2_Arr_T1_x1[Math.floor(Math.random() * L2_Arr_T1_x1.length)]; // Value of X
        var tempL2_2 = $.inArray(interactiveObj.L2_T1_2, L2_Arr_T1_x1);
        L2_Arr_T1_x1.splice(tempL2_2, 1);
    }
    interactiveObj.L2_T1_1=parseInt(interactiveObj.L2_T1_1);
    interactiveObj.L2_T1_2=parseInt(interactiveObj.L2_T1_2);
    interactiveObj.L2_T1_3=parseInt(interactiveObj.L2_T1_3);

    interactiveObj.FinalAnswerType1=(interactiveObj.L2_T1_3*interactiveObj.L2_T1_1+interactiveObj.L2_T1_2)/interactiveObj.L2_T1_3;
}
questionInteractive.prototype.L2_RNG_T2 = function()
{
    interactiveObj.L2_T2_1 = L2_Arr_T2_a[Math.floor(Math.random() * L2_Arr_T2_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T2_1, L2_Arr_T2_a);
    L2_Arr_T2_a.splice(tempL2_1, 1);

    interactiveObj.L2_T2_2 = L2_Arr_T2_x[Math.floor(Math.random() * L2_Arr_T2_x.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T2_2, L2_Arr_T2_x);
    L2_Arr_T2_x.splice(tempL2_1, 1);

    interactiveObj.L2_T2_3 = parseInt(100);
    interactiveObj.L2_T2_1=parseInt(interactiveObj.L2_T2_1);
    interactiveObj.L2_T2_2=parseInt(interactiveObj.L2_T2_2);
}
questionInteractive.prototype.L2_RNG_T3 = function()
{

    for (interactiveObj.i = 1; interactiveObj.i < 9; interactiveObj.i++) // Array of L2_Arr_T3_a filled with 1-9
    {
        L2_Arr_T3_a[interactiveObj.i-1] = interactiveObj.i;
    }

    interactiveObj.L2_T3_1 = L2_Arr_T3_a[Math.floor(Math.random() * L2_Arr_T3_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T3_1, L2_Arr_T3_a);
    L2_Arr_T3_a.splice(tempL2_1, 1);  // remove that element from the array

    //number x and y

    //randomly select Y from the array  L2_Arr_T3_a

    interactiveObj.L2_T3_3 = L2_Arr_T3_y[Math.floor(Math.random() * L2_Arr_T3_y.length)]; //value of Y
    var tempL2_1 = $.inArray(interactiveObj.L2_T3_3, L2_Arr_T3_y);
    L2_Arr_T3_y.splice(tempL2_1, 1);  // remove that element from the array

    ////console.log("value of y="+interactiveObj.L2_T3_3); // returned as an interger

    if (interactiveObj.L2_T3_3 == 2)
    {
        interactiveObj.L2_T3_2 = 1;   // value of x
    }
    if (interactiveObj.L2_T3_3 == 4)
    {
        interactiveObj.L2_T3_2 = L2_Arr_T3_x1[Math.floor(Math.random() * L2_Arr_T3_x1.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2, L2_Arr_T3_y);
        L2_Arr_T3_x1.splice(tempL2_1, 1);  // remove that element from the array
    }
    if (interactiveObj.L2_T3_3 == 5)
    {
        interactiveObj.L2_T3_2 = L2_Arr_T3_x2[Math.floor(Math.random() * L2_Arr_T3_x2.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2, L2_Arr_T3_y);
        L2_Arr_T3_x2.splice(tempL2_1, 1);  // remove that element from the array
    }
   
    //-------------filling all the arrays---------------//
    k = 0;
        // here y is 20 so first fill array of x by x>20 and ND by 2 or 5

        for (interactiveObj.i = 3; interactiveObj.i <= 19; interactiveObj.i++)
        {
            if (interactiveObj.i % 2 !== 0 && interactiveObj.i % 5 != 0)
            {
                L2_Arr_T3_x3[k] = interactiveObj.i;
                k++;
            }
        }

     k = 0;
        // here y is 25 so first fill array of x by x>20 and ND  5

        for (interactiveObj.i = 2; interactiveObj.i <= 24; interactiveObj.i++)
        {
            if (interactiveObj.i % 5 != 0)
            {
                L2_Arr_T3_x4[k] = interactiveObj.i;
                k++;
            }
        }  
        
      k = 0;
        // here y is 50 so first fill array of x by x>20 and ND  5

        for (interactiveObj.i = 6; interactiveObj.i <= 50; interactiveObj.i++)
        {
            if (interactiveObj.i % 5 != 0 && interactiveObj.i % 2 != 0)
            {
                L2_Arr_T3_x5[k] = interactiveObj.i;
                k++;
            }
        } 
     //-------------filling all the arrays---------------//       

    if (interactiveObj.L2_T3_3 == 20)//working fine
    {
        interactiveObj.L2_T3_2 = L2_Arr_T3_x3[Math.floor(Math.random() * L2_Arr_T3_x3.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2, L2_Arr_T3_y);
        L2_Arr_T3_x3.splice(tempL2_1, 1);  // remove that element from the array
    }
    if (interactiveObj.L2_T3_3 == 25) //working fine
    {
        interactiveObj.L2_T3_2 = L2_Arr_T3_x4[Math.floor(Math.random() * L2_Arr_T3_x4.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2, L2_Arr_T3_y);
        L2_Arr_T3_x4.splice(tempL2_1, 1);  // remove that element from the array
    }
    if (interactiveObj.L2_T3_3 == 50)//working fine
    {
       

        interactiveObj.L2_T3_2 = L2_Arr_T3_x5[Math.floor(Math.random() * L2_Arr_T3_x5.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2, L2_Arr_T3_y);
        L2_Arr_T3_x5.splice(tempL2_1, 1);  // remove that element from the array
    }

    interactiveObj.L2_T3_1=parseInt(interactiveObj.L2_T3_1);
    interactiveObj.L2_T3_2=parseInt(interactiveObj.L2_T3_2);
    interactiveObj.L2_T3_3=parseInt(interactiveObj.L2_T3_3);
}
questionInteractive.prototype.L2_RNG_T4 = function() 
{
    //----------Value of A----------------//

    for (interactiveObj.i = 0; interactiveObj.i < 9; interactiveObj.i++)  // array of A created 
    {
        L2_Arr_T4_a[interactiveObj.i] = interactiveObj.i + 1;

    }

    interactiveObj.L2_T4_1 = L2_Arr_T4_a[Math.floor(Math.random() * L2_Arr_T4_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T4_1, L2_Arr_T4_a);
    L2_Arr_T4_a.splice(tempL2_1, 1);  // remove that element from the array
    ////console.log("Value of A selected=" + interactiveObj.L2_T4_1);

    //----------Value of A----------------//


    //----------Value of Y----------------//

    interactiveObj.L2_T4_3 = L2_Arr_T4_y[Math.floor(Math.random() * L2_Arr_T4_y.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T4_3, L2_Arr_T4_y);
    L2_Arr_T4_y.splice(tempL2_1, 1);  // remove that element from the array
   // //console.log("Value of Y selected=" + interactiveObj.L2_T4_3);

    //----------Value of Y----------------//


    //----------Value of X----------------//

      k = 0;//populating the array of L2_Arr_T4_x1
   
        for (interactiveObj.i = 1; interactiveObj.i < 200; interactiveObj.i++)
        {
            if (interactiveObj.i % 2 != 0 && interactiveObj.i % 5 != 0)
            {
                L2_Arr_T4_x1[k] = interactiveObj.i;
                k++;
            }
        }

       k = 0;  //populating the array of L2_Arr_T4_x2
    
        for (interactiveObj.i = 1; interactiveObj.i < 40; interactiveObj.i++)
        {
            if (interactiveObj.i % 2 != 0 && interactiveObj.i % 5 != 0)
            {
                L2_Arr_T4_x2[k] = interactiveObj.i;
                k++;
            }
        }

        k = 0; //populating the array of L2_Arr_T4_x3
     
        for (interactiveObj.i = 1; interactiveObj.i < 200; interactiveObj.i++)
        {
            if (interactiveObj.i % 2 != 0 && interactiveObj.i % 5 != 0)
            {
                L2_Arr_T4_x3[k] = interactiveObj.i;
                k++;
            }
        }

        k = 0; //populating the array of L2_Arr_T4_x4
      
        for (interactiveObj.i = 1; interactiveObj.i < 200; interactiveObj.i++)
        {
            if (interactiveObj.i % 2 != 0 && interactiveObj.i % 5 != 0)
            {
                L2_Arr_T4_x4[k] = interactiveObj.i;
                k++;
            }
        }

   

    if (interactiveObj.L2_T4_3 == 20 || interactiveObj.L2_T4_3 == 25 || interactiveObj.L2_T4_3 == 50)
    {
        interactiveObj.L2_T4_2 = parseInt(1);
        ////console.log("Value of X selected=" + interactiveObj.L2_T4_2);
        //interactiveObj.multiplyBy = parseInt(100);
        if(interactiveObj.L2_T4_3==20)
        {
            interactiveObj.multiplyBy=parseInt(5);
        }
        if(interactiveObj.L2_T4_3==25)
        {
            interactiveObj.multiplyBy=parseInt(4);
        }
        if(interactiveObj.L2_T4_3==50)
        {
            interactiveObj.multiplyBy=parseInt(2);
        }
    }
    else if (interactiveObj.L2_T4_3 == 200) //when y is 200.. x will be <200 ND by 2 or 5
    {
  

        interactiveObj.L2_T4_2 = L2_Arr_T4_x1[Math.floor(Math.random() * L2_Arr_T4_x1.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2, L2_Arr_T4_x1);
        L2_Arr_T4_x1.splice(tempL2_1, 1);  // remove that element from the array
        interactiveObj.multiplyBy=parseInt(5);
       // //console.log("Value of X selected=" + interactiveObj.L2_T4_2);
    }
    else if (interactiveObj.L2_T4_3 == 40) //when y is 40.. x will be <40 ND by 2 or 5
    {


        interactiveObj.L2_T4_2 = L2_Arr_T4_x2[Math.floor(Math.random() * L2_Arr_T4_x2.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2, L2_Arr_T4_x2);
        L2_Arr_T4_x2.splice(tempL2_1, 1);  // remove that element from the array  
        interactiveObj.multiplyBy=parseInt(25);   
      //  //console.log("Value of X selected=" + interactiveObj.L2_T4_2);
    }
    else if (interactiveObj.L2_T4_3 == 500) //when y is 500.. x will be <500 ND by 2 or 5
    {


        interactiveObj.L2_T4_2 = L2_Arr_T4_x3[Math.floor(Math.random() * L2_Arr_T4_x3.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2, L2_Arr_T4_x3);
        L2_Arr_T4_x3.splice(tempL2_1, 1);  // remove that element from the array   
        interactiveObj.multiplyBy=parseInt(2);
       // //console.log("Value of X selected=" + interactiveObj.L2_T4_2);
    }
    else if (interactiveObj.L2_T4_3 == 250)   //when y is 250.. x will be <250 ND by 2 or 5
    {
    

        interactiveObj.L2_T4_2 = L2_Arr_T4_x4[Math.floor(Math.random() * L2_Arr_T4_x4.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2, L2_Arr_T4_x4);
        L2_Arr_T4_x4.splice(tempL2_1, 1);  // remove that element from the array 
        interactiveObj.multiplyBy=parseInt(4);
       // //console.log("Value of X selected=" + interactiveObj.L2_T4_2);
    }

    interactiveObj.L2_T4_1=parseInt(interactiveObj.L2_T4_1);
    interactiveObj.L2_T4_2=parseInt(interactiveObj.L2_T4_2);
    interactiveObj.L2_T4_3=parseInt(interactiveObj.L2_T4_3);

    //----------Value of X----------------//
}
questionInteractive.prototype.L2_A_RNG_T1=function()
{
    interactiveObj.L2_T1_2_1 = L2_Arr_T1_a[Math.floor(Math.random() * L2_Arr_T1_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T1_2_1, L2_Arr_T1_a);
    L2_Arr_T1_a.splice(tempL2_1, 1);

   interactiveObj.L2_T1_2_3 = parseInt(L2_Arr_T1_y[Math.floor(Math.random() * L2_Arr_T1_y.length)]); // Value of Y here, denominator
    var tempL2_3 = $.inArray(interactiveObj.L2_T1_2_3, L2_Arr_T1_y);
    L2_Arr_T1_y.splice(tempL2_3, 1);

   if (interactiveObj.L2_T1_2_3 == 100)
    {
    
        interactiveObj.L2_T1_2_2 = L2_Arr_T1_x[Math.floor(Math.random() * L2_Arr_T1_x.length)]; // Value of X
        var tempL2_2 = $.inArray(interactiveObj.L2_T1_2_2, L2_Arr_T1_x);
        L2_Arr_T1_x.splice(tempL2_2, 1);
    }
    else if (interactiveObj.L2_T1_2_3 == 10)
    {
   
        interactiveObj.L2_T1_2_2 = L2_Arr_T1_x1[Math.floor(Math.random() * L2_Arr_T1_x1.length)]; // Value of X
        var tempL2_2 = $.inArray(interactiveObj.L2_T1_2_2, L2_Arr_T1_x1);
        L2_Arr_T1_x1.splice(tempL2_2, 1);
    }

        interactiveObj.L2_T1_2_1=parseInt(interactiveObj.L2_T1_2_1);
        interactiveObj.L2_T1_2_2=parseInt(interactiveObj.L2_T1_2_2);
        interactiveObj.L2_T1_2_3=parseInt(interactiveObj.L2_T1_2_3);
}
questionInteractive.prototype.L2_A_RNG_T2=function()
{
    interactiveObj.L2_T2_2_1 = L2_Arr_T2_a[Math.floor(Math.random() * L2_Arr_T2_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T2_2_1, L2_Arr_T2_a);
    L2_Arr_T2_a.splice(tempL2_1, 1);

    interactiveObj.L2_T2_2_2 = L2_Arr_T2_x[Math.floor(Math.random() * L2_Arr_T2_x.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T2_2_2, L2_Arr_T2_x);
    L2_Arr_T2_x.splice(tempL2_1, 1);

    interactiveObj.L2_T2_2_3 = parseInt(100);

        interactiveObj.L2_T2_2_1=parseInt(interactiveObj.L2_T2_2_1);
        interactiveObj.L2_T2_2_2=parseInt(interactiveObj.L2_T2_2_2);
}
questionInteractive.prototype.L2_A_RNG_T3=function()
{
    interactiveObj.L2_T3_2_1 = parseInt(L2_Arr_T3_a[Math.floor(Math.random() * L2_Arr_T3_a.length)]); //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_1, L2_Arr_T3_a);
    L2_Arr_T3_a.splice(tempL2_1, 1);  // remove that element from the array

    interactiveObj.L2_T3_2_3 = parseInt(L2_Arr_T3_y[Math.floor(Math.random() * L2_Arr_T3_y.length)]); //value of Y
    var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_3, L2_Arr_T3_y);
    L2_Arr_T3_y.splice(tempL2_1, 1);  // remove that element from the array

    if (interactiveObj.L2_T3_2_3 == 2)
    {
        interactiveObj.L2_T3_2_2 = parseInt(1);   // value of x
    }
    if (interactiveObj.L2_T3_2_3 == 4)
    {
        interactiveObj.L2_T3_2_2 = parseInt(L2_Arr_T3_x1[Math.floor(Math.random() * L2_Arr_T3_x1.length)]); //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_2, L2_Arr_T3_y);
        L2_Arr_T3_x1.splice(tempL2_1, 1);  // remove that element from the array
    }
    if (interactiveObj.L2_T3_2_3 == 5)
    {
        interactiveObj.L2_T3_2_2 = parseInt(L2_Arr_T3_x2[Math.floor(Math.random() * L2_Arr_T3_x2.length)]); //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_2, L2_Arr_T3_y);
        L2_Arr_T3_x2.splice(tempL2_1, 1);  // remove that element from the array
    }

    if (interactiveObj.L2_T3_2_3 == 20)//working fine
    {
        interactiveObj.L2_T3_2_2 =parseInt(L2_Arr_T3_x3[Math.floor(Math.random() * L2_Arr_T3_x3.length)]); //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_2, L2_Arr_T3_y);
        L2_Arr_T3_x3.splice(tempL2_1, 1);  // remove that element from the array
    }
    if (interactiveObj.L2_T3_2_3 == 25) //working fine
    {
        interactiveObj.L2_T3_2_2 = parseInt(L2_Arr_T3_x4[Math.floor(Math.random() * L2_Arr_T3_x4.length)]); //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_2, L2_Arr_T3_y);
        L2_Arr_T3_x4.splice(tempL2_1, 1);  // remove that element from the array
    }
    if (interactiveObj.L2_T3_2_3 == 50)//working fine
    {
        interactiveObj.L2_T3_2_2 = parseInt(L2_Arr_T3_x5[Math.floor(Math.random() * L2_Arr_T3_x5.length)]); //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T3_2_2, L2_Arr_T3_y);
        L2_Arr_T3_x5.splice(tempL2_1, 1);  // remove that element from the array
    }

        interactiveObj.L2_T3_2_1=parseInt(interactiveObj.L2_T3_2_1);
        interactiveObj.L2_T3_2_2=parseInt(interactiveObj.L2_T3_2_2);
        interactiveObj.L2_T3_2_3=parseInt(interactiveObj.L2_T3_2_3);
}
questionInteractive.prototype.L2_A_RNG_T4=function()
{
    interactiveObj.L2_T4_2_1 = L2_Arr_T4_a[Math.floor(Math.random() * L2_Arr_T4_a.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T4_2_1, L2_Arr_T4_a);
    L2_Arr_T4_a.splice(tempL2_1, 1);  // remove that element from the array
    ////console.log("Value of A selected=" + interactiveObj.L2_T4_2_1);

    interactiveObj.L2_T4_2_3 = L2_Arr_T4_y[Math.floor(Math.random() * L2_Arr_T4_y.length)]; //value of A
    var tempL2_1 = $.inArray(interactiveObj.L2_T4_2_3, L2_Arr_T4_y);
    L2_Arr_T4_y.splice(tempL2_1, 1);  // remove that element from the array
    //console.log("Value of Y selected=" + interactiveObj.L2_T4_3);

     if (interactiveObj.L2_T4_2_3 == 20 || interactiveObj.L2_T4_2_3 == 25 || interactiveObj.L2_T4_2_3 == 50)
    {
        interactiveObj.L2_T4_2_2 = parseInt(1);
        
        if(interactiveObj.L2_T4_2_3==20)
        {
            interactiveObj.multiplyBy2=parseInt(5);
        }
        if(interactiveObj.L2_T4_2_3==25)
        {
            interactiveObj.multiplyBy2=parseInt(4);
        }
        if(interactiveObj.L2_T4_2_3==50)
        {
            interactiveObj.multiplyBy2=parseInt(2);
        }

    }
    else if (interactiveObj.L2_T4_2_3 == 200) //when y is 200.. x will be <200 ND by 2 or 5
    {
        interactiveObj.L2_T4_2_2 = L2_Arr_T4_x1[Math.floor(Math.random() * L2_Arr_T4_x1.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2_2, L2_Arr_T4_x1);
        L2_Arr_T4_x1.splice(tempL2_1, 1);  // remove that element from the array
        interactiveObj.multiplyBy2=parseInt(5);

        ////console.log("Value of X selected=" + interactiveObj.L2_T4_2_2);
    }
    else if (interactiveObj.L2_T4_2_3 == 40) //when y is 40.. x will be <40 ND by 2 or 5
    {
        interactiveObj.L2_T4_2_2 = L2_Arr_T4_x2[Math.floor(Math.random() * L2_Arr_T4_x2.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2_2, L2_Arr_T4_x2);
        L2_Arr_T4_x2.splice(tempL2_1, 1);  // remove that element from the array   
        interactiveObj.multiplyBy2=parseInt(25);  
        ////console.log("Value of X selected=" + interactiveObj.L2_T4_2_2);
    }
    else if (interactiveObj.L2_T4_2_3 == 500) //when y is 500.. x will be <500 ND by 2 or 5
    {
        interactiveObj.L2_T4_2_2 = L2_Arr_T4_x3[Math.floor(Math.random() * L2_Arr_T4_x3.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2_2, L2_Arr_T4_x3);
        L2_Arr_T4_x3.splice(tempL2_1, 1);  // remove that element from the array  
        interactiveObj.multiplyBy2=parseInt(2); 
        ////console.log("Value of X selected=" + interactiveObj.L2_T4_2_2);
    }
    else if (interactiveObj.L2_T4_2_3 == 250)   //when y is 250.. x will be <250 ND by 2 or 5
    {
        interactiveObj.L2_T4_2_2 = L2_Arr_T4_x4[Math.floor(Math.random() * L2_Arr_T4_x4.length)]; //value of A
        var tempL2_1 = $.inArray(interactiveObj.L2_T4_2_2, L2_Arr_T4_x4);
        L2_Arr_T4_x4.splice(tempL2_1, 1);  // remove that element from the array 
        interactiveObj.multiplyBy2=parseInt(4);
       // //console.log("Value of X selected=" + interactiveObj.L2_T4_2_2);
    }

        interactiveObj.L2_T4_2_1=parseInt(interactiveObj.L2_T4_2_1);
        interactiveObj.L2_T4_2_2=parseInt(interactiveObj.L2_T4_2_2);
        interactiveObj.L2_T4_2_3=parseInt(interactiveObj.L2_T4_2_3);
}
// ----------number generation functions-------------------//
questionInteractive.prototype.loadSecondStage = function()
{

    tempScore=levelWiseScore;
    time=0;

    interactiveObj.tempLevelStatus=levelWiseStatus;

    levelsAttempted="L1|L2";
    levelWiseScore=levelWiseScore;
    levelWiseStatus=levelWiseStatus+"|"+0;
   

    interactiveObj.L2_RNG_T1();
    interactiveObj.L2_RNG_T2();
    interactiveObj.L2_RNG_T3();
    interactiveObj.L2_RNG_T4();
    

    Ltype = 2;
    html = "";
    $("#container").html(html);


    html = '<div id="background">';
    html += '<div id="header">'+replaceDynamicText(promptArr['txt_40'],interactiveObj.numberLanguage,"interactiveObj")+'</div>';

    $("#container").html(html);
    interactiveObj.L2_Q1_visible = 1;
    L2_loadQuestions = setTimeout("interactiveObj.L2_loadQuestions();", 500);
}

questionInteractive.prototype.L2_loadQuestions = function()
{
    html += '<div id="L2_Q1">';
        html += '<div id="L2_questions">';
        html += '<div id="L2_n1">' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '';
        html += '<div id="L2_fraction"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;</div>';
        html += '<div id="L2_inputBox"><input id="L2_Q1_1" class="Input_Box"></input></div>';
        html += '<div id="L2_AnswerBox1"></div>';
        html += '</div>';
        html += '</div>';
    html += '</div>';


    html += '<div id="L2_Q2">';
        html += '<div id="L2_2_questions">';
        html += '<div id="L2_n1_2">' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '';
        html += '<div id="L2_fraction_2"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;</div>';
        html += '<div id="L2_inputBox_2"><input id="L2_Q2_1" class="Input_Box"></input></div>';
        html += '<div id="L2_AnswerBox1_2"></div>';
        html += '</div>';
        html += '</div>';
    html += '</div>';



    html += '<div id="L2_Q3">';
        html += '<div id="L2_3_questions">';
        html += '<div id="L2_n1_2">' + replaceDynamicText(interactiveObj.L2_T3_1,interactiveObj.numberLanguage,"interactiveObj") + '';
        html += '<div id="L2_fraction_3"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;</div>';
        html += '<div id="L2_inputBox_3"><input id="L2_Q3_1" class="Input_Box"></input></div>';
        html += '<div id="L2_AnswerBox3_3"></div>';
        html += '<div id="L2_AnswerBox3_4"></div>';
        html += '<div id="L2_AnswerBox3_5"></div>';
        html += '</div>';
        html += '</div>';
    html += '</div>';

    html += '<div id="L2_Q4">';
        html += '<div id="L2_4_questions">';
        html += '<div id="L2_n1_2">' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + '';
        html += '<div id="L2_fraction_4"><div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div>&nbsp;=&nbsp;</div>';
        html += '<div id="L2_inputBox_4"><input id="L2_Q4_1" class="Input_Box"></input></div>';

        html += '<div id="L2_AnswerBox4_1"></div>';
        html += '<div id="L2_AnswerBox4_2"></div>';
        html += '<div id="L2_AnswerBox4_3"></div>';
        html += '<div id="L2_AnswerBox4_4"></div>';

        html += '</div>';
        html += '</div>';
    html += '</div>';


    html += '<div id="L2_popOut"></div>';
    html += '</div>';  //closing of id=background div    

    html += '<div id="enterAnswer" style="top: 250px;left: 298px;" class="correct"><div class="sparkie"></div><div id="blankEnter">' + replaceDynamicText(promptArr['txt_21'],interactiveObj.numberLanguage,"interactiveObj") + '</div><button onclick=interactiveObj.L2_loadQuestions(); class="buttonPrompt_wellDone" style="top: 86px;left: 158px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
    html += '<div id="enterZero" style="top: 250px;left: 298px;height: 123px;" class="correct"><div class="sparkie"></div><div id="blankEnter">' + replaceDynamicText(promptArr['txt_39'],interactiveObj.numberLanguage,"interactiveObj") + '</div><button onclick=interactiveObj.L2_loadQuestions(); class="buttonPrompt_wellDone" style="top: 86px;left: 158px;">' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

    $("#container").html(html);

   // $("#promptDiv").css('top',( ($("#promptDiv").scrollTop() - 320 ) + 'px'));

    if (interactiveObj.L2_Q1_visible == 1)
    {
        interactiveObj.correctAnswer_Ques1 = (interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3;
        $("#L2_Q1").css('visibility', 'visible');
        $("#L2_Q1_1").focus();

        if (interactiveObj.L2_attempt_Q1 == 1)  // display part
        {
            if (interactiveObj.L2_incorrect_1 == 1)// user answered wrong first time
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box"></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj")+ '</div></div></div>');
                $("#L2_Q1_2").focus();

                  $("#L2_Q1_1").addClass('wrongAnswer');
            }

            if (interactiveObj.L2_correct_1 == 1) //user answer correct
            {
                $("#L2_Q1_1").attr('disabled', true);
                $("#L2_Q1_1").attr('placeholder', replaceDynamicText(interactiveObj.correctAnswer_Ques1,interactiveObj.numberLanguage,"interactiveObj"));

                $("#L2_Q1_1").addClass('rightAnswer');
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 2)
        {
            if (interactiveObj.L2_incorrect_2 == 1)
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box"></input></div>');
                $("#L2_Q1_3").focus();

                $("#L2_Q1_2").addClass('wrongAnswer');
             
            }

            if (interactiveObj.L2_correct_2 == 1) //user answer correct
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box"></input></div>');
                $("#L2_Q1_3").focus();

                $("#L2_Q1_2").addClass('rightAnswer');

                //setting color of previous blocks

            }
        }
        if (interactiveObj.L2_attempt_Q1 == 3)
        {
            if (interactiveObj.L2_incorrect_3 == 1)
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box"></input></div>');
                $("#L2_Q1_3").focus();

                $("#L2_Q1_2").addClass('wrongAnswer');
            }
            if (interactiveObj.L2_correct_3 == 1) //user answer correct
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box"></input></div>');
                $("#L2_Q1_3").attr('disabled', true);
                $("#L2_Q1_4").focus();

                $("#L2_Q1_3").addClass('rightAnswer');
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 4)
        {
            if (interactiveObj.L2_incorrect_4 == 1)
            {
                //////////alert(1);
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box"></input></div>');
                $("#L2_Q1_3").attr('disabled', true);
                $("#L2_Q1_4").focus();

                $("#L2_Q1_3").addClass('wrongAnswer');
            }

            if (interactiveObj.L2_correct_4 == 1) //user answer correct
            {
                //////////alert(2);
                if (interactiveObj.L2_correct_4_1 == 1)
                {
                    $("#L2_inputBox").html('');
                    $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                    $("#L2_Q1_2").attr('disabled', true);
                    $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" ></input></div>');
                    $("#L2_Q1_3").attr('disabled', true);

                    $("#L2_Q1_4").focus();

                     $("#L2_Q1_3").addClass('rightAnswer');
                }
                else
                {
                    $("#L2_inputBox").html('');
                    $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                    $("#L2_Q1_2").attr('disabled', true);
                    $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" placeholder=' + replaceDynamicText(((interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div>');
                    //$("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;'+interactiveObj.L2_T1_1+'&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder='+interactiveObj.L2_T1_2/interactiveObj.L2_T1_3+'></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" ></input></div>');
                    $("#L2_Q1_3").attr('disabled', true);

                    $("#L2_Q1_4").attr('disabled', true);
                    //$("#L2_Q1_3").addClass('wrongAnswer');
                    $("#L2_Q1_4").addClass('rightAnswer');
                }
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 5)
        {

            if (interactiveObj.L2_incorrect_5 == 1)
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" placeholder=' + replaceDynamicText(((interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div>');
                $("#L2_Q1_3").attr('disabled', true);
                $("#L2_Q1_4").attr('disabled', true);
            
                $("#L2_Q1_4").addClass('wrongAnswer');
            }
            if (interactiveObj.L2_correct_5 == 1) //user answer correct
            {
                $("#L2_inputBox").html('');
                $("#L2_inputBox").append('<div id="sub2"><input id="L2_Q1_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T1_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T1_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q1_2").attr('disabled', true);
                $("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T1_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" placeholder=' + replaceDynamicText(((interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div>');
                $("#L2_Q1_3").attr('disabled', true);
                $("#L2_Q1_4").attr('disabled', true);

                $("#L2_Q1_4").addClass('rightAnswer');

            }
        }
    }
    if (interactiveObj.L2_Q2_visible == 1)
    {
        interactiveObj.correctAnswer_Ques2 = (interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3;
        $("#L2_Q2").css('visibility', 'visible');
        $("#L2_Q2_1").focus();

        if (interactiveObj.L2_2_attempt_Q1 == 1)  // display part done
        {
            if (interactiveObj.L2_2_incorrect_1 == 1)// user answered wrong first time
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box"></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").focus();
            }

            if (interactiveObj.L2_2_correct_1 == 1) //user answer correct
            {
                $("#L2_Q2_1").attr('disabled', true);
                $("#L2_Q2_1").attr('placeholder', replaceDynamicText(interactiveObj.correctAnswer_Ques2,interactiveObj.numberLanguage,"interactiveObj"));

                $("#L2_Q2_1").addClass('rightAnswer');
             
            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 2)    // display part done
        {
            if (interactiveObj.L2_2_incorrect_2 == 1)
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box"></input></div>');
                $("#L2_Q2_3").focus();
            }

            if (interactiveObj.L2_2_correct_2 == 1) //user answer correct
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box"></input></div>');
                $("#L2_Q2_3").focus();
                $("#L2_Q2_2").addClass('rightAnswer'); 
            
            }
            //setting color of previous blocks
        }
        if (interactiveObj.L2_2_attempt_Q1 == 3)   // display part done
        {
            if (interactiveObj.L2_2_incorrect_3 == 1)
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box"></input></div>');
                $("#L2_Q2_3").focus();

            }

            if (interactiveObj.L2_2_correct_3 == 1) //user answer correct
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q2_4" class="Input_Box"></input></div>');
                $("#L2_Q2_3").attr('disabled', true);
                $("#L2_Q2_4").focus();

            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 4)   // display part done
        {
            if (interactiveObj.L2_2_incorrect_4 == 1)
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q2_4" class="Input_Box"></input></div>');
                $("#L2_Q2_3").attr('disabled', true);
                $("#L2_Q2_4").focus();
            }

            if (interactiveObj.L2_2_correct_4 == 1) //user answer correct
            {////////alert(2);
                if (interactiveObj.L2_2_correct_4_1 == 1)
                {////////alert(21);
                    $("#L2_inputBox_2").html('');
                    $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                    $("#L2_Q2_2").attr('disabled', true);
                    $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q2_4" class="Input_Box" ></input></div>');
                    $("#L2_Q2_3").attr('disabled', true);

                    $("#L2_Q2_4").focus();

                }
                else
                {////////alert(22);
                    $("#L2_inputBox_2").html('');
                    $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                    $("#L2_Q1_2").attr('disabled', true);
                    $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q2_4" class="Input_Box" placeholder=' + replaceDynamicText(((interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div>');
                    //$("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;'+interactiveObj.L2_T1_1+'&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder='+interactiveObj.L2_T1_2/interactiveObj.L2_T1_3+'></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" ></input></div>');
                    $("#L2_Q2_3").attr('disabled', true);
                    $("#L2_Q2_2").attr('disabled', true);

                    $("#L2_Q2_4").attr('disabled', true);
                    $("#L2_Q2_4").addClass('rightAnswer');
                    interactiveObj.L2_Q3_visible=1;

                }
            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 5)   // display part done
        {

            if (interactiveObj.L2_2_incorrect_5 == 1)
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + replaceDynamicText(interactiveObj.L2_T2_2,interactiveObj.numberLanguage,"interactiveObj") + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q2_4" class="Input_Box" placeholder=' + replaceDynamicText(((interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div>');
                $("#L2_Q2_3").attr('disabled', true);
                $("#L2_Q2_4").attr('disabled', true);
                $("#L2_Q2_4").addClass('wrongAnswer');
            }
            if (interactiveObj.L2_2_correct_5 == 1) //user answer correct
            {
                $("#L2_inputBox_2").html('');
                $("#L2_inputBox_2").append('<div id="sub2"><input id="L2_Q2_2" class="Input_Box" placeholder=' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2 + '</div><div class="frac">' + replaceDynamicText(interactiveObj.L2_T2_3,interactiveObj.numberLanguage,"interactiveObj") + '</div></div></div>');
                $("#L2_Q2_2").attr('disabled', true);
                $("#L2_AnswerBox1_2").append('<div id="sub3">&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T2_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;<input id="L2_Q2_3" class="Input_Box" placeholder=' + replaceDynamicText((interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input>&nbsp;=&nbsp;<input id="L2_Q2_4" class="Input_Box" placeholder=' + replaceDynamicText(((interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3),interactiveObj.numberLanguage,"interactiveObj") + '></input></div>');
                $("#L2_Q2_3").attr('disabled', true);
                $("#L2_Q2_4").attr('disabled', true);
                $("#L2_Q2_4").addClass('rightAnswer');
            }
        }
    }
    if (interactiveObj.L2_Q3_visible == 1)
    {

        $("#L2_Q3").css('visibility', 'visible');
        $("#L2_Q3_1").focus();

        if (interactiveObj.L2_3_attempt_Q1 == 1)
        {
            if (interactiveObj.L2_Q3_incorrect_1 == 1)
            {
                $("#L2_AnswerBox3_3").css('visibility', 'visible');
                $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_Q3_2").focus();
                $("#L2_Q3_1").attr('disabled', true);
            }
            else if (interactiveObj.L2_Q3_correct_1==1)
            {
                $("#L2_Q3_1").attr('placeholder', replaceDynamicText((interactiveObj.L2_T3_3 * interactiveObj.L2_T3_1 + interactiveObj.L2_T3_2) /interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q3_1").attr('disabled', true);
                  $("#L2_Q3_1").addClass('rightAnswer');
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 2)
        {
            //append previous blocks
            $("#L2_AnswerBox3_3").css('visibility', 'visible');
            $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
            $("#L2_Q3_2").attr('disabled', true);
            $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
            $("#L2_Q3_1").attr('disabled', true);

            if (interactiveObj.L2_Q3_incorrect_2 == 1)
            {
                //blc'+promptArr['txt_7']+'s of this level
                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + replaceDynamicText(promptArr['txt_34'],interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").focus();
            }
            if (interactiveObj.L2_Q3_correct_2 == 1)
            {
                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + replaceDynamicText(promptArr['txt_34'],interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").focus();
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 3)
        {

            if (interactiveObj.L2_Q3_incorrect_3 == 1)
            {
                $("#L2_AnswerBox3_3").css('visibility', 'visible');
                $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_Q3_2").attr('disabled', true);
                $("#L2_Q3_1").attr('disabled', true);
                $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);

                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + replaceDynamicText(promptArr['txt_34'],interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").attr('disabled', true);


                $("#L2_AnswerBox3_5").css('visibility', 'visible');
                $("#L2_AnswerBox3_5").append('<div>' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')
                $("#L2_Q3_4").attr('disabled', true);
                $("#L2_Q3_6").attr('disabled', true);
                $("#L2_Q3_5").focus();
                $("#L2_Q3").css('height', '207px'); //height increased of third question

            }

            if (interactiveObj.L2_Q3_correct_3 == 1)
            {
                //////////alert("u got it right dude");
                $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
                $("#L2_AnswerBox3_3").css('visibility', 'visible');
                $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_Q3_2").attr('disabled', true);
                $("#L2_Q3_1").attr('disabled', true);
                 $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").attr('disabled', true);

                $("#L2_Q3_3").attr('placeholder', replaceDynamicText((parseInt(interactiveObj.L2_T3_2) / parseInt(interactiveObj.L2_T3_3)),interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q3_1").attr('disabled', false);
                $("#L2_Q3_1").focus();
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 4)
        {
            //append the previous blocks
            if (interactiveObj.L2_Q3_incorrect_4 == 1)
            {
                $("#L2_AnswerBox3_3").css('visibility', 'visible');
                $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_Q3_2").attr('disabled', true);
                $("#L2_Q3_1").attr('disabled', true);
                $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);

                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").attr('disabled', true);

                $("#L2_AnswerBox3_5").css('visibility', 'visible');
                $("#L2_AnswerBox3_5").append('<div>' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

                $("#L2_Q3").css('height', '207px'); //height increased of third question

                $("#L2_Q3_4").focus();
                $("#L2_Q3_6").attr('disabled', true);
                $("#L2_Q3_5").attr('disabled', true);
                $("#L2_Q3_5").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));
                
                    if(interactiveObj.L2_Q3_check==1)
                                {
                                   // //alert("finally u answered incorrectly")
                                     $("#L2_AnswerBox3_5").css('visibility', 'hidden');
                                     $("#L2_Q3").css('height', '156px');
                                      $("#L2_Q3_1").addClass('wrongAnswer');
                                      $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
                                      $("#L2_Q3_3").attr('placeholder',interactiveObj.L2_T3_2/interactiveObj.L2_T3_3); 
                                      $("#L2_Q3_1").attr('placeholder',(interactiveObj.L2_T3_3*interactiveObj.L2_T3_1+interactiveObj.L2_T3_2)/interactiveObj.L2_T3_3);
                                     interactiveObj.L2_Q4_visible = 1;
                                }
                                else if(interactiveObj.L2_Q3_check==0)
                                {
                                   
                                }

            }
            else if (interactiveObj.L2_Q3_correct_4 == 1)
            {
                $("#L2_AnswerBox3_3").css('visibility', 'visible');
                $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_Q3_2").attr('disabled', true);

                $("#L2_Q3_1").attr('disabled', true);
                $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);

                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").attr('disabled', true);

               $("#L2_AnswerBox3_5").css('visibility', 'visible');
               $("#L2_AnswerBox3_5").append('<div>' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

                 $("#L2_Q3").css('height', '207px'); //height increased of third question

                $("#L2_Q3_4").focus();
                        if(interactiveObj.L2_Q3_correct_3==1)
                        {
                          $("#L2_Q3").css('height', '156px');  
                          $("#L2_AnswerBox3_5").css('visibility', 'hidden');  
                          $("#L2_Q3_4").attr('disabled', true);
                          $("#L2_Q3_1").addClass('rightAnswer');
                          $("#L2_Q3_2").attr('placeholder',interactiveObj.L2_T3_1);
                          $("#L2_Q3_1").attr('placeholder',(interactiveObj.L2_T3_3*interactiveObj.L2_T3_1+interactiveObj.L2_T3_2)/interactiveObj.L2_T3_3);
                          $("#L2_Q3_3").attr('placeholder',interactiveObj.L2_T3_2/interactiveObj.L2_T3_3); 
                          $("#L2_Q3_4").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj")); 
                          $("#L2_Q3_6").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3 * interactiveObj.L2_T3_2),interactiveObj.numberLanguage,"interactiveObj"));  
                        }
                $("#L2_Q3_6").attr('disabled', true);
                $("#L2_Q3_5").attr('disabled', true);
                $("#L2_Q3_5").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));
            }
            else if (interactiveObj.L2_Q3_correct_3==1) //dummy...nothing happening in this loop
            {
               // ////////alert("your answer was coorect whgat to do")
               /* $("#L2_AnswerBox3_3").css('visibility', 'visible');
                $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '</div>');
                $("#L2_Q3_2").attr('disabled', true);
                $("#L2_Q3_1").attr('disabled', true);
                $("#L2_Q3_1").attr('placeholder', (parseInt(interactiveObj.L2_T3_3) * parseInt(interactiveObj.L2_T3_1) + parseInt(interactiveObj.L2_T3_2)) / parseInt(interactiveObj.L2_T3_3));

                $("#L2_AnswerBox3_4").css('visibility', 'visible');
                $("#L2_AnswerBox3_4").css('visible', 'visible');
                $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_3").attr('disabled', true);
                $("#L2_Q3_3").attr('placeholder', parseInt(interactiveObj.L2_T3_2) / parseInt(interactiveObj.L2_T3_3));
                $("#L2_Q3").css('height', '207px');*/

            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 5)
        {
            //append the previous blocks


            $("#L2_AnswerBox3_3").css('visibility', 'visible');
            $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
            $("#L2_Q3_2").attr('disabled', true);
            $("#L2_Q3_1").attr('disabled', true);
            $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
            $("#L2_AnswerBox3_4").css('visibility', 'visible');
            $("#L2_AnswerBox3_4").css('visible', 'visible');
            $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
            $("#L2_Q3_3").attr('disabled', true);

            $("#L2_AnswerBox3_5").css('visibility', 'visible');
            $("#L2_AnswerBox3_5").append('<div>' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

            $("#L2_Q3_4").attr('disabled', true);
            $("#L2_Q3_4").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3_5").attr('disabled', true);
            $("#L2_Q3_5").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));
            $("#L2_Q3_6").focus();

            $("#L2_Q3").css('height', '207px'); //height increased of third question
        }
        if (interactiveObj.L2_3_attempt_Q1 == 6)
        {
            $("#L2_AnswerBox3_3").css('visibility', 'visible');
            $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
            $("#L2_Q3_2").attr('disabled', true);
            $("#L2_Q3_1").attr('disabled', true);
            $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
            $("#L2_AnswerBox3_4").css('visibility', 'visible');
            $("#L2_AnswerBox3_4").css('visible', 'visible');
            $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
            $("#L2_Q3_3").attr('disabled', false);
            $("#L2_Q3_3").focus();

            $("#L2_AnswerBox3_5").css('visibility', 'visible');
            $("#L2_AnswerBox3_5").append('<div>' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

            $("#L2_Q3_4").attr('disabled', true);
            $("#L2_Q3_4").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3) ,interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3_5").attr('disabled', true);
            $("#L2_Q3_5").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3 ),interactiveObj.numberLanguage,"interactiveObj"));
            $("#L2_Q3_6").attr('disabled', true);
            $("#L2_Q3_6").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3 * interactiveObj.L2_T3_2),interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3").css('height', '207px'); //height increased of third question
        }
        if (interactiveObj.L2_3_attempt_Q1 == 7)
        {
            $("#L2_AnswerBox3_3").css('visibility', 'visible');
            $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
            $("#L2_Q3_2").attr('disabled', true);
            $("#L2_Q3_1").attr('disabled', false);
            $("#L2_Q3_1").focus();
            $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
            $("#L2_AnswerBox3_4").css('visibility', 'visible');
            $("#L2_AnswerBox3_4").css('visible', 'visible');
            $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
            $("#L2_Q3_3").attr('disabled', true);
            $("#L2_Q3_3").attr('placeholder', interactiveObj.L2_T3_2 / interactiveObj.L2_T3_3);

            $("#L2_AnswerBox3_5").css('visibility', 'visible');
            $("#L2_AnswerBox3_5").append('<div>' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

            $("#L2_Q3_4").attr('disabled', true);
            $("#L2_Q3_4").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3_5").attr('disabled', true);
            $("#L2_Q3_5").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));
            $("#L2_Q3_6").attr('disabled', true);
            $("#L2_Q3_6").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3 * interactiveObj.L2_T3_2),interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3").css('height', '207px'); //height increased of third question
        }
        if (interactiveObj.L2_3_attempt_Q1 == 8)
        {

            $("#L2_AnswerBox3_3").css('visibility', 'visible');
            $("#L2_AnswerBox3_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
            $("#L2_Q3_2").attr('disabled', true);
            $("#L2_Q3_1").attr('disabled', true);
            $("#L2_Q3_1").attr('placeholder', replaceDynamicText((parseInt(interactiveObj.L2_T3_3) * parseInt(interactiveObj.L2_T3_1) + parseInt(interactiveObj.L2_T3_2)) / parseInt(interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));
            $("#L2_Q3_2").attr('placeholder', interactiveObj.L2_T3_1);
            $("#L2_AnswerBox3_4").css('visibility', 'visible');
            $("#L2_AnswerBox3_4").css('visible', 'visible');
            $("#L2_AnswerBox3_4").append('<div><div style="display:inline-block;">' + replaceDynamicText(promptArr['txt_34'],interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;<input id="L2_Q3_3" class="Input_Box"/></div></div>')
            $("#L2_Q3_3").attr('disabled', true);
            $("#L2_Q3_3").attr('placeholder', interactiveObj.L2_T3_2 / interactiveObj.L2_T3_3);

            $("#L2_AnswerBox3_5").css('visibility', 'visible');
            $("#L2_AnswerBox3_5").append('<div>' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp;=&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T3_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T3_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_4" class="Input_Box"></input>', '<input id="L2_Q3_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6" class="Input_Box"></input>', replaceDynamicText(interactiveObj.num3_1,interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

            $("#L2_Q3_4").attr('disabled', true);
            $("#L2_Q3_4").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3_5").attr('disabled', true);
            $("#L2_Q3_5").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3),interactiveObj.numberLanguage,"interactiveObj"));
            $("#L2_Q3_6").attr('disabled', true);
            $("#L2_Q3_6").attr('placeholder', replaceDynamicText((interactiveObj.num3_1 / interactiveObj.L2_T3_3 * interactiveObj.L2_T3_2),interactiveObj.numberLanguage,"interactiveObj"));

            $("#L2_Q3").css('height', '207px'); //height increased of third question

              

              if(interactiveObj.L2_Q3_incorrect_8==1)
              {
                $("#L2_Q3_1").addClass('wrongAnswer');
              }
              else
              {
                $("#L2_Q3_1").addClass('rightAnswer');
              }
        }
    }
    if (interactiveObj.L2_Q4_visible == 1)
    {
        $("#L2_Q4").css('visibility', 'visible');
        $("#L2_Q4_1").focus();

        if (interactiveObj.L2_4_attempt_Q1 == 1)
        {
            if (interactiveObj.L2_4_incorrect_1 == 1)
            {
                $("#L2_Q4_1").focus();
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
            }
            else if (interactiveObj.L2_4_correct_1 == 1)
            {
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.correct_Answer4,interactiveObj.numberLanguage,"interactiveObj"));

                $("#L2_Q4_1").addClass('rightAnswer');
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 2)
        {
            if (interactiveObj.L2_4_incorrect_2 == 1)
            {
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                $("#L2_Q4_2").focus();
            }
            else if (interactiveObj.L2_4_correct_2 == 1)
            {

                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                $("#L2_Q4_2").focus();
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 3)
        {
            if (interactiveObj.L2_4_incorrect_3 == 1)
            {
                //previous blc'+promptArr['txt_7']+'s loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

                $("#L2_Q4_3").focus();
            }
            else if (interactiveObj.L2_4_correct_3 == 1)
            {
                //previous blc'+promptArr['txt_7']+'s loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', interactiveObj.multiplyBy);
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')

                $("#L2_Q4_3").focus();
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 4)
        {
            if (interactiveObj.L2_4_incorrect_4 == 1)
            {
                //previous boxes loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')
                $("#L2_Q4_3").attr('disabled', true);
                $("#L2_Q4_3").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_2 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                //
                $("#L2_AnswerBox4_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_4" class="Input_Box"></input></div>');
                $("#L2_Q4_4").focus();
            }
            else if (interactiveObj.L2_4_correct_4 == 1)
            {
                //previous boxes loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_1),interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText((interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', replaceDynamicText((interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')
                $("#L2_Q4_3").attr('disabled', true);
                $("#L2_Q4_3").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_2 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                //
                $("#L2_AnswerBox4_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_4" class="Input_Box"></input></div>');
                $("#L2_Q4_4").focus();
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 5)
        {
            if (interactiveObj.L2_4_incorrect_5 == 1)
            {
                //previous boxes loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_1),interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2, interactiveObj.L2_T4_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', replaceDynamicText((interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')
                $("#L2_Q4_3").attr('disabled', true);
                $("#L2_Q4_3").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_2 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                //
                $("#L2_AnswerBox4_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_4" class="Input_Box"></input></div>');
                $("#L2_Q4_4").focus();
            }
            else if (interactiveObj.L2_4_correct_5 == 1)
            {
                //previous boxes loaded
                $("#L2_Q4_1").attr('placeholder', interactiveObj.L2_T4_1);
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2, interactiveObj.L2_T4_3) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2, interactiveObj.L2_T4_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', interactiveObj.multiplyBy) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', interactiveObj.multiplyBy);
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', interactiveObj.L2_T4_3 * interactiveObj.multiplyBy) + '</div>')
                $("#L2_Q4_3").attr('disabled', true);
                $("#L2_Q4_3").attr('placeholder', interactiveObj.L2_T4_2 * interactiveObj.multiplyBy);
                //
                $("#L2_AnswerBox4_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_4" class="Input_Box"></input></div>');
                $("#L2_Q4_4").attr('disabled', true);
                $("#L2_Q4_4").attr('placeholder', (parseInt(interactiveObj.L2_T4_3)*parseInt(interactiveObj.L2_T4_1)+parseInt(interactiveObj.L2_T4_2))/parseInt(interactiveObj.L2_T4_3));

                $("#L2_Q4_4").addClass('rightAnswer');
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 6)
        {
            if (interactiveObj.L2_4_incorrect_6 == 1)
            {
                //previous boxes loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2, interactiveObj.L2_T4_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText((interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', interactiveObj.multiplyBy);
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')
                $("#L2_Q4_3").attr('disabled', true);
                $("#L2_Q4_3").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_2 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                //
                $("#L2_AnswerBox4_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_4" class="Input_Box"></input></div>');
                $("#L2_Q4_4").attr('disabled', true);
               $("#L2_Q4_4").attr('placeholder', replaceDynamicText(((interactiveObj.L2_T4_3*interactiveObj.L2_T4_1+interactiveObj.L2_T4_2)/interactiveObj.L2_T4_3),interactiveObj.numberLanguage,"interactiveObj"));
                 $("#L2_Q4_4").addClass('wrongAnswer');

            }
            else if (interactiveObj.L2_4_correct_6 == 1)
            {
                //previous boxes loaded
                $("#L2_Q4_1").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_1),interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_1").attr('disabled', true);
                $("#L2_AnswerBox4_1").append('<div>&nbsp;+&nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '</div>');
                $("#L2_AnswerBox4_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(replaceDynamicText(interactiveObj.L2_T4_2,interactiveObj.numberLanguage,"interactiveObj"), replaceDynamicText(interactiveObj.L2_T4_3,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj")) + '&nbsp; ) &nbsp;</div>');
                //----//
                $("#L2_Q4_2").attr('placeholder', replaceDynamicText(interactiveObj.multiplyBy,interactiveObj.numberLanguage,"interactiveObj"));
                $("#L2_Q4_2").attr('disabled', true);
                $("#L2_AnswerBox4_3").append('<div>&nbsp;=&nbsp;' + replaceDynamicText(interactiveObj.L2_T4_1,interactiveObj.numberLanguage,"interactiveObj") + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_3" class="Input_Box"></input>', replaceDynamicText((interactiveObj.L2_T4_3 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj")) + '</div>')
                $("#L2_Q4_3").attr('disabled', true);
                $("#L2_Q4_3").attr('placeholder', replaceDynamicText((interactiveObj.L2_T4_2 * interactiveObj.multiplyBy),interactiveObj.numberLanguage,"interactiveObj"));
                //
                $("#L2_AnswerBox4_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_4" class="Input_Box"></input></div>');
                $("#L2_Q4_4").attr('disabled', true);
                $("#L2_Q4_4").attr('placeholder', replaceDynamicText(((parseInt(interactiveObj.L2_T4_3)*parseInt(interactiveObj.L2_T4_1)+parseInt(interactiveObj.L2_T4_2))/parseInt(interactiveObj.L2_T4_3)),interactiveObj.numberLanguage,"interactiveObj"));
                
                  $("#L2_Q4_4").addClass('rightAnswer');

            }
        }
    }
    if(interactiveObj.L2_Q5_visible==1)
    {
        $("#L2_Q4").after('<div id="L2_Q5"></div>');
        $("#L2_Q5").css('visibility','visible');

        if(interactiveObj.L2_AdditonalQues5==1)// when 5th question is of type 1
        {
            html3="";
            html3+= '<div id="L2_questions2">';
            html3+= '<div id="L2_n1">' + interactiveObj.L2_T1_2_1 + '';
            html3+= '<div id="L2_fraction"><div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div>&nbsp;=&nbsp;</div>';
            html3+= '<div id="L2_inputBox_5"><input id="L2_Q1_5_1" class="Input_Box"></input></div>';
            html3+= '<div id="L2_AnswerBox1_5"></div>';
            html3+= '</div>';
            html3+= '</div>';

            $("#L2_Q5").append(html3);
            $("#L2_Q1_5_1").focus();

            if (interactiveObj.L2_attempt_Q5 == 1)  // display part
            {
                ////////alert("Visibility after attempt 1")
                if (interactiveObj.L2_incorrect_2_1 == 1)// user answered wrong first time
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box"></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").focus();
                }

                if (interactiveObj.L2_correct_2_1 == 1) //user answer correct
                {
                    $("#L2_Q1_5_1").attr('disabled', true);
                    $("#L2_Q1_5_1").attr('placeholder', (interactiveObj.L2_T1_2_3*interactiveObj.L2_T1_2_1+interactiveObj.L2_T1_2_2)/interactiveObj.L2_T1_2_3);

                    $("#L2_Q1_5_1").addClass('rightAnswer');
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 2)
            {
                if (interactiveObj.L2_incorrect_2 == 1)
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").focus();
                }

                if (interactiveObj.L2_correct_2 == 1) //user answer correct
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").focus();

                    //setting color of previous blocks

                }
            }
            if (interactiveObj.L2_attempt_Q5 == 3)
            {
                if (interactiveObj.L2_incorrect_2_3 == 1)
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").focus();

                }

                if (interactiveObj.L2_correct_2_3 == 1) //user answer correct
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").focus();

                }
            }
            if (interactiveObj.L2_attempt_Q5 == 4)
            {
                if (interactiveObj.L2_incorrect_4 == 1)
                {
                   // ////////alert(1);
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").focus();
                }

                if (interactiveObj.L2_correct_2_4 == 1) //user answer correct
                {
                   // ////////alert(2);
                    if (interactiveObj.L2_correct_2_4_1 == 1)
                    {
                        $("#L2_inputBox_5").html('');
                        $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                        $("#L2_Q1_5_2").attr('disabled', true);
                        $("#L2_inputBox_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" ></input></div>');
                        $("#L2_Q1_5_3").attr('disabled', true);

                        $("#L2_Q1_5_4").focus();
                    }
                    else
                    {
                        $("#L2_inputBox_5").html('');
                        $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                        $("#L2_Q1_5_2").attr('disabled', true);
                       // $("#L2_inputBox_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3 + '></input></div>');
                        //$("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;'+interactiveObj.L2_T1_1+'&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder='+interactiveObj.L2_T1_2/interactiveObj.L2_T1_3+'></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" ></input></div>');
                        $("#L2_Q1_5_3").attr('disabled', true);
                        $("#L2_Q1_5_4").attr('disabled', true);
                        $("#L2_Q1_5_4").attr('placeholder', (interactiveObj.L2_T1_2_3*interactiveObj.L2_T1_2_1+interactiveObj.L2_T1_2_2)/interactiveObj.L2_T1_2_3);
                        
                        $("#L2_Q1_5_4").addClass('wrongAnswer');

                    }
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 5)
            {

                if (interactiveObj.L2_incorrect_2_5 == 1)
                {
           
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3 + '></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").attr('disabled', true);

                    $("#L2_Q1_5_4").addClass('wrongAnswer');
                }
                if (interactiveObj.L2_correct_2_5 == 1) //user answer correct
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3 + '></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").attr('disabled', true);

                    $("#L2_Q1_5_4").addClass('rightAnswer');

                }
            }
        }//closure for type1
        if(interactiveObj.L2_AdditonalQues5==2)// when 5th question is of type 2
        {
            html3="";
            html3+= '<div id="L2_questions2">';
            html3+= '<div id="L2_n1">' + interactiveObj.L2_T2_2_1 + '';
            html3+= '<div id="L2_fraction"><div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div>&nbsp;=&nbsp;</div>';
            html3+= '<div id="L2_inputBox_5"><input id="L2_Q1_5_1" class="Input_Box"></input></div>';
            html3+= '<div id="L2_AnswerBox1_5"></div>';
            html3+= '</div>';
            html3+= '</div>';

            $("#L2_Q5").append(html3);
            $("#L2_Q1_5_1").focus();

            if (interactiveObj.L2_attempt_Q5 == 1)  // display part
            {
                ////////alert("Visibility after attempt 1")
                if (interactiveObj.L2_incorrect_2_1 == 1)// user answered wrong first time
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box"></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").focus();
                }

                if (interactiveObj.L2_correct_2_1 == 1) //user answer correct
                {
                    $("#L2_Q1_5_1").attr('disabled', true);
                    $("#L2_Q1_5_1").attr('placeholder', (interactiveObj.L2_T2_2_3*interactiveObj.L2_T2_2_1+interactiveObj.L2_T2_2_2)/interactiveObj.L2_T2_2_3);

                    $("#L2_Q1_5_1").addClass('rightAnswer');
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 2)
            {
                if (interactiveObj.L2_incorrect_2_2 == 1)
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").focus();
                }

                if (interactiveObj.L2_correct_2_2 == 1) //user answer correct
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").focus();

                    //setting color of previous blocks

                }
            }
            if (interactiveObj.L2_attempt_Q5 == 3)
            {
                if (interactiveObj.L2_incorrect_2_3 == 1)
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").focus();

                }

                if (interactiveObj.L2_correct_2_3 == 1) //user answer correct
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").focus();

                }
            }
            if (interactiveObj.L2_attempt_Q5 == 4)
            {
                if (interactiveObj.L2_incorrect_2_4 == 1)
                {
                   // ////////alert(1);
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box"></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").focus();
                }

                if (interactiveObj.L2_correct_2_4 == 1) //user answer correct
                {
                    //////////alert(2);


                    if (interactiveObj.L2_correct_2_4_1 == 1)
                    {
                        $("#L2_inputBox_5").html('');
                        $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                        $("#L2_Q1_5_2").attr('disabled', true);
                        $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" ></input></div>');
                        $("#L2_Q1_5_3").attr('disabled', true);

                        $("#L2_Q1_5_4").focus();
                    }
                    else
                    {
                        $("#L2_inputBox_5").html('');
                        $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                        $("#L2_Q1_5_2").attr('disabled', true);
                        $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '></input></div>');
                        //$("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;'+interactiveObj.L2_T1_1+'&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder='+interactiveObj.L2_T1_2/interactiveObj.L2_T1_3+'></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" ></input></div>');
                        $("#L2_Q1_5_3").attr('disabled', true);

                        $("#L2_Q1_5_4").attr('disabled', true);
                        $("#L2_Q1_5_4").addClass('wrongAnswer');
                    }


                }
            }
            if (interactiveObj.L2_attempt_Q5 == 5)
            {

                if (interactiveObj.L2_incorrect_2_5 == 1)
                {
           
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").attr('disabled', true);

                    $("#L2_Q1_5_4").addClass('wrongAnswer');
                }
                if (interactiveObj.L2_correct_2_5 == 1) //user answer correct
                {
                    $("#L2_inputBox_5").html('');
                    $("#L2_inputBox_5").append('<div id="sub2"><input id="L2_Q1_5_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_5_2").attr('disabled', true);
                    $("#L2_AnswerBox1_5").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_5_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_5_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '></input></div>');
                    $("#L2_Q1_5_3").attr('disabled', true);
                    $("#L2_Q1_5_4").attr('disabled', true);
                    $("#L2_Q1_5_4").addClass('rightAnswer');

                }
            } 
        }//closure for type2
        if(interactiveObj.L2_AdditonalQues5==3)// when 5th question is of type 3
        {


            html3="";
           
            html3 += '<div id="L2_5_questions">';
            html3 += '<div id="L2_n1_2">' + interactiveObj.L2_T3_2_1 + '';
            html3 += '<div id="L2_fraction_5"><div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T3_2_2 + '</div><div class="frac">' + interactiveObj.L2_T3_2_3 + '</div></div>&nbsp;=&nbsp;</div>';
            html3 += '<div id="L2_inputBox_5"><input id="L2_Q3_5_1" class="Input_Box"></input></div>';
            html3 += '<div id="L2_AnswerBox5_3"></div>';
            html3 += '<div id="L2_AnswerBox5_4"></div>';
            html3 += '<div id="L2_AnswerBox5_5"></div>';
            html3 += '</div>';
            html3 += '</div>';
            

            $("#L2_Q5").append(html3);
            $("#L2_Q3_5_1").focus();

            if (interactiveObj.L2_attempt_Q5 == 1)
            {
                if (interactiveObj.L2_Q3_incorrect_1 == 1)
                {
                    $("#L2_AnswerBox5_3").css('visibility', 'visible');
                    $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_5_2").focus();
                    $("#L2_Q3_5_1").attr('disabled', true);
                }
                else if (interactiveObj.L2_Q3_correct_1)
                {
                    $("#L2_Q3_5_1").attr('placeholder', (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3));
                    $("#L2_Q3_5_1").attr('disabled', true);

                    $("#L2_Q3_5_1").addClass('rightAnswer');
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 2)
            {
                //append previous blocks
                $("#L2_AnswerBox5_3").css('visibility', 'visible');
                $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_5_2").attr('disabled', true);
                $("#L2_Q3_5_1").attr('disabled', true);
                $("#L2_Q3_5_2").attr('placeholder',interactiveObj.L2_T3_2_1);

                if (interactiveObj.L2_Q3_incorrect_2 == 1)
                {
                    //blc'+promptArr['txt_7']+'s of this level
                    $("#L2_AnswerBox5_4").css('visibility', 'visible');
                    $("#L2_AnswerBox5_4").css('visible', 'visible');
                    $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_5_3").focus();
                }
                if (interactiveObj.L2_Q3_correct_2 == 1)
                {
                    $("#L2_AnswerBox5_4").css('visibility', 'visible');
                    $("#L2_AnswerBox5_4").css('visible', 'visible');
                    $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_5_3").focus();
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 3)
            {
                if (interactiveObj.L2_Q3_incorrect_2_3 == 1)
                {
                    $("#L2_AnswerBox5_3").css('visibility', 'visible');
                    $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_5_2").attr('disabled', true);
                    $("#L2_Q3_5_1").attr('disabled', true);

                    $("#L2_AnswerBox5_4").css('visibility', 'visible');
                    $("#L2_AnswerBox5_4").css('visible', 'visible');
                    $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_5_3").attr('disabled', true);
                    
                    $("#L2_Q3_5_2").attr('placeholder',interactiveObj.L2_T3_2_1);

                    $("#L2_AnswerBox5_5").css('visibility', 'visible');
                    $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')
                    $("#L2_Q3_5_4").attr('disabled', true);
                    $("#L2_Q3_5_6").attr('disabled', true);
                    $("#L2_Q3_5_5").focus();
                    $("#L2_Q5").css('height', '207px'); //height increased of third question
                }
                if (interactiveObj.L2_Q3_correct_2_3 == 1)
                {
                    //////////alert("u got it right dude");
                    $("#L2_AnswerBox5_3").css('visibility', 'visible');
                    $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '</div>');
                    $("#L2_Q3_5_2").attr('disabled', true);
                    $("#L2_Q3_5_1").attr('disabled', true);

                    $("#L2_Q3_5_3").attr('placeholder',interactiveObj.L2_T3_2_2/interactiveObj.L2_T3_2_3)
                    $("#L2_Q3_5_2").attr('placeholder',interactiveObj.L2_T3_2_1);

                    $("#L2_AnswerBox5_4").css('visibility', 'visible');
                    $("#L2_AnswerBox5_4").css('visible', 'visible');
                    $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_3").attr('disabled', true);

                    $("#L2_Q3_5_3").attr('placeholder', parseInt(interactiveObj.L2_T3_2_2) / parseInt(interactiveObj.L2_T3_2_3));
                    $("#L2_Q3_5_3").attr('disabled',true);
                    $("#L2_Q3_5_1").attr('disabled', false);
                    $("#L2_Q3_5_1").focus();
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 4)
            {
               //append the previous blocks
                if (interactiveObj.L2_Q3_incorrect_2_4 == 1)
                {
                    $("#L2_AnswerBox5_3").css('visibility', 'visible');
                    $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_5_2").attr('disabled', true);
                    $("#L2_Q3_5_1").attr('disabled', true);

                    $("#L2_AnswerBox5_4").css('visibility', 'visible');
                    $("#L2_AnswerBox5_4").css('visible', 'visible');
                    $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_5_3").attr('disabled', true);
                    $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);
                    

                    $("#L2_AnswerBox5_5").css('visibility', 'visible');
                    $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                    $("#L2_Q3").css('height', '207px'); //height increased of third question

                    $("#L2_Q3_5_4").focus();
                    $("#L2_Q3_5_6").attr('disabled', true);
                    $("#L2_Q3_5_5").attr('disabled', true);
                    $("#L2_Q3_5_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                     //$("#L2_Q3_5_1").addClass('wrongAnswer');
                        if(interactiveObj.L2_Q3_correct_2_3==1)
                        {
                            if(interactiveObj.L2_Q3_2_check==1)
                            {
                                ////alert("u dint got it anyway")
                                $("#L2_AnswerBox5_5").hide();
                                $("#L2_Q3_5_3").attr('placeholder',interactiveObj.L2_T3_2_2/interactiveObj.L2_T3_2_3 );
                                $("#L2_Q3_5_1").attr('placeholder',(interactiveObj.L2_T3_2_3*interactiveObj.L2_T3_2_1+interactiveObj.L2_T3_2_2)/interactiveObj.L2_T3_2_3 );
                                $("#L2_Q3_5_1").addClass('wrongAnswer');
                                $("#L2_Q5").css('height', '160px');

                            }
                            else if(interactiveObj.L2_Q3_2_check==0)
                            {
                                //alert("great work")
                            }

                        }
                }
                else if (interactiveObj.L2_Q3_correct_2_4 == 1)
                {
                    $("#L2_AnswerBox5_3").css('visibility', 'visible');
                    $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_5_2").attr('disabled', true);
                    $("#L2_Q3_5_1").attr('disabled', true);

                    $("#L2_AnswerBox5_4").css('visibility', 'visible');
                    $("#L2_AnswerBox5_4").css('visible', 'visible');
                    $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_5_3").attr('disabled', true);
                    $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);
                    

                    $("#L2_AnswerBox5_5").css('visibility', 'visible');
                    $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                    $("#L2_Q5").css('height', '156px'); //height increased of third question

                    $("#L2_Q3_5_4").focus();
                    $("#L2_Q3_5_6").attr('disabled', true);
                    $("#L2_Q3_5_5").attr('disabled', true);
                    $("#L2_Q3_5_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                   // $("#L2_Q3_5_1").addClass('rightAnswer');
                    
                    if(interactiveObj.L2_Q3_correct_2_3==1)
                    {
                                 //////////alert("your answer was coorect whgat to do")
                       // $("#L2_AnswerBox5_3").css('visibility', 'visible');
                        //$("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                        $("#L2_Q3_5_2").attr('disabled', true);
                        $("#L2_Q3_5_1").attr('disabled', true);
                        $("#L2_Q3_5_1").attr('placeholder', (parseInt(interactiveObj.L2_T3_3) * parseInt(interactiveObj.L2_T3_1) + parseInt(interactiveObj.L2_T3_2)) / parseInt(interactiveObj.L2_T3_3));

                       // $("#L2_AnswerBox5_4").css('visibility', 'visible');
                        //$("#L2_AnswerBox5_4").css('visible', 'visible');
                        //$("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                        $("#L2_Q3_5_3").attr('disabled', true);
                        $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);
                        $("#L2_Q3_5_3").attr('placeholder', parseInt(interactiveObj.L2_T3_2_2) / parseInt(interactiveObj.L2_T3_2_3));
                        $("#L2_Q5").css('height', '156px');

                        $("#L2_Q3_5_1").addClass('rightAnswer');

                        $("#L2_AnswerBox5_5").css('visibility', 'hidden');
                    }
                }
             
            }
            if (interactiveObj.L2_attempt_Q5 == 5)
            {
                //append the previous blocks


                $("#L2_AnswerBox5_3").css('visibility', 'visible');
                $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_5_2").attr('disabled', true);
                $("#L2_Q3_5_1").attr('disabled', true);
                $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);

                $("#L2_AnswerBox5_4").css('visibility', 'visible');
                $("#L2_AnswerBox5_4").css('visible', 'visible');
                $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_5_3").attr('disabled', true);

                $("#L2_AnswerBox5_5").css('visibility', 'visible');
                $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_5_4").attr('disabled', true);
                $("#L2_Q3_5_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_5_5").attr('disabled', true);
                $("#L2_Q3_5_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_5_6").focus();

                $("#L2_Q5").css('height', '207px'); //height increased of third question
            }
            if (interactiveObj.L2_attempt_Q5 == 6)
            {
                $("#L2_AnswerBox5_3").css('visibility', 'visible');
                $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_5_2").attr('disabled', true);
                $("#L2_Q3_5_1").attr('disabled', true);
                $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);

                $("#L2_AnswerBox5_4").css('visibility', 'visible');
                $("#L2_AnswerBox5_4").css('visible', 'visible');
                $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_5_3").attr('disabled', false);
                $("#L2_Q3_5_3").focus();

                $("#L2_AnswerBox5_5").css('visibility', 'visible');
                $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_5_4").attr('disabled', true);
                $("#L2_Q3_5_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_5_5").attr('disabled', true);
                $("#L2_Q3_5_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_5_6").attr('disabled', true);
                $("#L2_Q3_5_6").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_2);

                $("#L2_Q5").css('height', '207px'); //height increased of third question
            }
            if (interactiveObj.L2_attempt_Q5 == 7)
            {
                $("#L2_AnswerBox5_3").css('visibility', 'visible');
                $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_5_2").attr('disabled', true);
                $("#L2_Q3_5_1").attr('disabled', false);
                $("#L2_Q3_5_1").focus();
                $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);

                $("#L2_AnswerBox5_4").css('visibility', 'visible');
                $("#L2_AnswerBox5_4").css('visible', 'visible');
                $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_5_3").attr('disabled', true);
                $("#L2_Q3_5_3").attr('placeholder', interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3);

                $("#L2_AnswerBox5_5").css('visibility', 'visible');
                $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_5_4").attr('disabled', true);
                $("#L2_Q3_5_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_5_5").attr('disabled', true);
                $("#L2_Q3_5_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_5_6").attr('disabled', true);
                $("#L2_Q3_5_6").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_2);

                $("#L2_Q5").css('height', '207px'); //height increased of third question
            }
            if (interactiveObj.L2_attempt_Q5 == 8)
            {

                $("#L2_AnswerBox5_3").css('visibility', 'visible');
                $("#L2_AnswerBox5_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_5_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_5_2").attr('disabled', true);
                $("#L2_Q3_5_2").attr('placeholder', interactiveObj.L2_T3_2_1);
                $("#L2_Q3_5_1").attr('disabled', true);
                $("#L2_Q3_5_1").attr('placeholder', (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3));

                $("#L2_AnswerBox5_4").css('visibility', 'visible');
                $("#L2_AnswerBox5_4").css('visible', 'visible');
                $("#L2_AnswerBox5_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_5_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_5_3").attr('disabled', true);
                $("#L2_Q3_5_3").attr('placeholder', interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3);

                $("#L2_AnswerBox5_5").css('visibility', 'visible');
                $("#L2_AnswerBox5_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_5_4" class="Input_Box"></input>', '<input id="L2_Q3_5_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_5_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_5_4").attr('disabled', true);
                $("#L2_Q3_5_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_5_5").attr('disabled', true);
                $("#L2_Q3_5_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_5_6").attr('disabled', true);
                $("#L2_Q3_5_6").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_2);

                $("#L2_Q5").css('height', '207px'); //height increased of third question

                if(interactiveObj.L2_Q3_incorrect_2_8==1)
                {
                    $("#L2_Q3_5_1").addClass('wrongAnswer');
                }
                else if(interactiveObj.L2_Q3_correct_2_8==1)
                {
                    $("#L2_Q3_5_1").addClass('rightAnswer');
                }
            }
        }//closure for type3
    }
    if(interactiveObj.L2_Q6_visible==1)
    {
        $("#L2_Q5").after('<div id="L2_Q6"></div>');
        $("#L2_Q6").css('visibility','visible');


        if(interactiveObj.L2_AdditonalQues6 == 2) // done
        {

            html3="";
            html3+= '<div id="L2_questions2">';
            html3+= '<div id="L2_n1">' + interactiveObj.L2_T2_2_1 + '';
            html3+= '<div id="L2_fraction"><div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div>&nbsp;=&nbsp;</div>';
            html3+= '<div id="L2_inputBox_6"><input id="L2_Q1_6_1" class="Input_Box"></input></div>';
            html3+= '<div id="L2_AnswerBox1_6"></div>';
            html3+= '</div>';
            html3+= '</div>';

            $("#L2_Q6").append(html3);
            $("#L2_Q1_6_1").focus();

            if (interactiveObj.L2_attempt_Q6 == 1)  // display part
            {
                ////////alert("Visibility after attempt 1")
                if (interactiveObj.L2_incorrect_2_6_1 == 1)// user answered wrong first time
                {
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box"></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").focus();
                }

                if (interactiveObj.L2_correct_2_6_1 == 1) //user answer correct
                {
                    $("#L2_Q1_6_1").attr('disabled', true);
                    $("#L2_Q1_6_1").attr('placeholder', (interactiveObj.L2_T2_2_3*interactiveObj.L2_T2_2_1+interactiveObj.L2_T2_2_2)/interactiveObj.L2_T2_2_3);

                    $("#L2_Q1_6_1").addClass('rightAnswer');
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 2)
            {
                if (interactiveObj.L2_incorrect_2_6_2 == 1)
                {
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_6_3").focus();
                }

                if (interactiveObj.L2_correct_2_6_2 == 1) //user answer correct
                {
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_6_3").focus();

                    //setting color of previous blocks

                }
            }
            if (interactiveObj.L2_attempt_Q6 == 3)
            {
                if (interactiveObj.L2_incorrect_2_6_3 == 1)
                {
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box"></input></div>');
                    $("#L2_Q1_6_3").focus();

                }

                if (interactiveObj.L2_correct_2_6_3 == 1) //user answer correct
                {
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_6_4" class="Input_Box"></input></div>');
                    $("#L2_Q1_6_3").attr('disabled', true);
                    $("#L2_Q1_6_4").focus();

                }
            }
            if (interactiveObj.L2_attempt_Q6 == 4)
            {
                if (interactiveObj.L2_incorrect_2_6_4 == 1)
                {
                   // ////////alert(1);
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_6_4" class="Input_Box"></input></div>');
                    $("#L2_Q1_6_3").attr('disabled', true);
                    $("#L2_Q1_6_4").focus();
                }

                if (interactiveObj.L2_correct_2_6_4 == 1) //user answer correct
                {
               
                    if (interactiveObj.L2_correct_2_4_6_1 == 1)
                    {
                        $("#L2_inputBox_6").html('');
                        $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                        $("#L2_Q1_6_2").attr('disabled', true);
                        $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_6_4" class="Input_Box" ></input></div>');
                        $("#L2_Q1_6_3").attr('disabled', true);

                        $("#L2_Q1_6_4").focus();
                    }
                    else
                    {
                        $("#L2_inputBox_6").html('');
                        $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                        $("#L2_Q1_6_2").attr('disabled', true);
                        $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_6_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '></input></div>');
                        //$("#L2_AnswerBox1").append('<div id="sub3">&nbsp;=&nbsp;'+interactiveObj.L2_T1_1+'&nbsp;+&nbsp;<input id="L2_Q1_3" class="Input_Box" placeholder='+interactiveObj.L2_T1_2/interactiveObj.L2_T1_3+'></input>&nbsp;=&nbsp;<input id="L2_Q1_4" class="Input_Box" ></input></div>');
                        $("#L2_Q1_6_3").attr('disabled', true);

                        $("#L2_Q1_6_4").attr('disabled', true);

                    }


                }
            }
            if (interactiveObj.L2_attempt_Q6 == 5)
            {

                if (interactiveObj.L2_incorrect_2_6_5 == 1)
                {
           
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_6_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '></input></div>');
                    $("#L2_Q1_6_3").attr('disabled', true);
                    $("#L2_Q1_6_4").attr('disabled', true);
                    $("#L2_Q1_6_4").addClass('wrongAnswer');
                }
                if (interactiveObj.L2_correct_2_6_5 == 1) //user answer correct
                {
                    $("#L2_inputBox_6").html('');
                    $("#L2_inputBox_6").append('<div id="sub2"><input id="L2_Q1_6_2" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_1 + '></input>&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div>');
                    $("#L2_Q1_6_2").attr('disabled', true);
                    $("#L2_AnswerBox1_6").append('<div id="sub3">&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<input id="L2_Q1_6_3" class="Input_Box" placeholder=' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '></input>&nbsp;=&nbsp;<input id="L2_Q1_6_4" class="Input_Box" placeholder=' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '></input></div>');
                    $("#L2_Q1_6_3").attr('disabled', true);
                    $("#L2_Q1_6_4").attr('disabled', true);
                    $("#L2_Q1_6_4").addClass('rightAnswer');

                }
            } 
        }
        if(interactiveObj.L2_AdditonalQues6 == 3) //done
        {
            html3="";
           
            html3 += '<div id="L2_6_questions">';
            html3 += '<div id="L2_n1_2">' + interactiveObj.L2_T3_2_1 + '';
            html3 += '<div id="L2_fraction_6"><div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T3_2_2 + '</div><div class="frac">' + interactiveObj.L2_T3_2_3 + '</div></div>&nbsp;=&nbsp;</div>';
            html3 += '<div id="L2_inputBox_6"><input id="L2_Q3_6_1" class="Input_Box"></input></div>';
            html3 += '<div id="L2_AnswerBox6_3"></div>';
            html3 += '<div id="L2_AnswerBox6_4"></div>';
            html3 += '<div id="L2_AnswerBox6_5"></div>';
            html3 += '</div>';
            html3 += '</div>';
            

            $("#L2_Q6").append(html3);
            $("#L2_Q3_6_1").focus();

            if (interactiveObj.L2_attempt_Q6 == 1)//all done
            {
                if (interactiveObj.L2_Q3_incorrect_2_6_1 == 1)
                {
                    $("#L2_AnswerBox6_3").css('visibility', 'visible');
                    $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_6_2").focus();
                    $("#L2_Q3_6_1").attr('disabled', true);
                }
                else if (interactiveObj.L2_Q3_correct_2_6_1)
                {
                    $("#L2_Q3_6_1").attr('placeholder', (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3));
                    $("#L2_Q3_6_1").attr('disabled', true);
                    $("#L2_Q3_6_1").addClass('rightAnswer');
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 2)
            {
                //append previous blocks
                $("#L2_AnswerBox6_3").css('visibility', 'visible');
                $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_6_2").attr('disabled', true);
                $("#L2_Q3_6_1").attr('disabled', true);

                if (interactiveObj.L2_Q3_incorrect_2_6_2 == 1)
                {
                    //blc'+promptArr['txt_7']+'s of this level
                    $("#L2_AnswerBox6_4").css('visibility', 'visible');
                    $("#L2_AnswerBox6_4").css('visible', 'visible');
                    $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_6_3").focus();
                }
                if (interactiveObj.L2_Q3_correct_2_6_2 == 1)
                {
                    $("#L2_AnswerBox6_4").css('visibility', 'visible');
                    $("#L2_AnswerBox6_4").css('visible', 'visible');
                    $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_6_3").focus();
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 3)
            {

                if (interactiveObj.L2_Q3_incorrect_2_6_3 == 1)
                {
                    $("#L2_AnswerBox6_3").css('visibility', 'visible');
                    $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_6_2").attr('disabled', true);
                    $("#L2_Q3_6_1").attr('disabled', true);

                    $("#L2_AnswerBox6_4").css('visibility', 'visible');
                    $("#L2_AnswerBox6_4").css('visible', 'visible');
                    $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_6_3").attr('disabled', true);
                    $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);


                    $("#L2_AnswerBox6_5").css('visibility', 'visible');
                    $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')
                    $("#L2_Q3_6_4").attr('disabled', true);
                    $("#L2_Q3_6_6").attr('disabled', true);
                    $("#L2_Q3_6_5").focus();
                     $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                    $("#L2_Q6").css('height', '207px'); //height increased of third question
                }
                else if (interactiveObj.L2_Q3_correct_2_6_3 == 1)
                {
                    //////////alert("u got it right dude");
                    $("#L2_AnswerBox6_3").css('visibility', 'visible');
                    $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_6_2").attr('disabled', true);
                    $("#L2_Q3_6_1").attr('disabled', true);

                    $("#L2_AnswerBox6_4").css('visibility', 'visible');
                    $("#L2_AnswerBox6_4").css('visible', 'visible');
                    $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_6_3").attr('disabled', true);

                    $("#L2_Q3_6_3").attr('placeholder', parseInt(interactiveObj.L2_T3_2_2) / parseInt(interactiveObj.L2_T3_2_3));
                    $("#L2_Q3_6_1").attr('disabled', false);
                    $("#L2_Q3_6_1").focus();
                    $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 4)
            {
                //append the previous blocks
                if (interactiveObj.L2_Q3_incorrect_2_6_4 == 1)
                {
                    $("#L2_AnswerBox6_3").css('visibility', 'visible');
                    $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_6_2").attr('disabled', true);
                    $("#L2_Q3_6_1").attr('disabled', true);

                     $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                    
                    $("#L2_AnswerBox6_4").css('visibility', 'visible');
                    $("#L2_AnswerBox6_4").css('visible', 'visible');
                    $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_6_3").attr('disabled', true);

                    $("#L2_AnswerBox6_5").css('visibility', 'visible');
                    $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                    $("#L2_Q6").css('height', '207px'); //height increased of third question

                    $("#L2_Q3_6_4").focus();
                    $("#L2_Q3_6_6").attr('disabled', true);
                    $("#L2_Q3_6_5").attr('disabled', true);
                    $("#L2_Q3_6_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                    if (interactiveObj.L2_Q3_correct_2_6_3==1)
                        {
                          if(interactiveObj.L2_Q3_2_6_check==1)
                          {
                            ////alert("ur nt getting it idiot")
                             $("#L2_AnswerBox6_5").hide();
                             $("#L2_Q3_6_3").attr('placeholder',interactiveObj.L2_T3_2_2/interactiveObj.L2_T3_2_3);
                             $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                             $("#L2_Q3_6_1").attr('placeholder',(interactiveObj.L2_T3_2_3*interactiveObj.L2_T3_2_1+interactiveObj.L2_T3_2_2)/interactiveObj.L2_T3_2_3);
                            $("#L2_Q3_6_1").addClass('wrongAnswer');
                          }
                          else if(interactiveObj.L2_Q3_2_6_check==0)
                          {
                            ////alert("Great work")
                          }

                        }
                }
                else if (interactiveObj.L2_Q3_correct_2_6_4 == 1)
                {
                    $("#L2_AnswerBox6_3").css('visibility', 'visible');
                    $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                    $("#L2_Q3_6_2").attr('disabled', true);
                    $("#L2_Q3_6_1").attr('disabled', true);

                    $("#L2_AnswerBox6_4").css('visibility', 'visible');
                    $("#L2_AnswerBox6_4").css('visible', 'visible');
                    $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                    $("#L2_Q3_6_3").attr('disabled', true);

                    $("#L2_AnswerBox6_5").css('visibility', 'visible');
                    $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                    $("#L2_Q5").css('height', '156px'); //height increased of third question

                    $("#L2_Q3_6_4").focus();
                    $("#L2_Q3_6_6").attr('disabled', true);
                    $("#L2_Q3_6_5").attr('disabled', true);
                    $("#L2_Q3_6_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                     $("#L2_Q3_6_1").addClass('rightAnswer');
                } 
            }
            if (interactiveObj.L2_attempt_Q6 == 5)
            {
                //append the previous blocks

                 $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                $("#L2_AnswerBox6_3").css('visibility', 'visible');
                $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_6_2").attr('disabled', true);
                $("#L2_Q3_6_1").attr('disabled', true);

                $("#L2_AnswerBox6_4").css('visibility', 'visible');
                $("#L2_AnswerBox6_4").css('visible', 'visible');
                $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_6_3").attr('disabled', true);

                $("#L2_AnswerBox6_5").css('visibility', 'visible');
                $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_6_4").attr('disabled', true);
                $("#L2_Q3_6_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_6_5").attr('disabled', true);
                $("#L2_Q3_6_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_6_6").focus();

                $("#L2_Q6").css('height', '207px'); //height increased of third question
            }
            if (interactiveObj.L2_attempt_Q6 == 6)
            {
                $("#L2_AnswerBox6_3").css('visibility', 'visible');
                $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_6_2").attr('disabled', true);
                $("#L2_Q3_6_1").attr('disabled', true);
                 $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                $("#L2_AnswerBox6_4").css('visibility', 'visible');
                $("#L2_AnswerBox6_4").css('visible', 'visible');
                $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_6_3").attr('disabled', false);
                $("#L2_Q3_6_3").focus();

                $("#L2_AnswerBox6_5").css('visibility', 'visible');
                $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_6_4").attr('disabled', true);
                $("#L2_Q3_6_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_6_5").attr('disabled', true);
                $("#L2_Q3_6_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_6_6").attr('disabled', true);
                $("#L2_Q3_6_6").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_2);

                $("#L2_Q6").css('height', '207px'); //height increased of third question
            }
            if (interactiveObj.L2_attempt_Q6 == 7)
            {
                $("#L2_AnswerBox6_3").css('visibility', 'visible');
                $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_6_2").attr('disabled', true);
                $("#L2_Q3_6_1").attr('disabled', false);
                $("#L2_Q3_6_1").focus();
                 $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                $("#L2_AnswerBox6_4").css('visibility', 'visible');
                $("#L2_AnswerBox6_4").css('visible', 'visible');
                $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_6_3").attr('disabled', true);
                $("#L2_Q3_6_3").attr('placeholder', interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3);

                $("#L2_AnswerBox6_5").css('visibility', 'visible');
                $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_6_4").attr('disabled', true);
                $("#L2_Q3_6_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_6_5").attr('disabled', true);
                $("#L2_Q3_6_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_6_6").attr('disabled', true);
                $("#L2_Q3_6_6").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_2);

                $("#L2_Q6").css('height', '207px'); //height increased of third question
            }
            if (interactiveObj.L2_attempt_Q6 == 8)
            {

                $("#L2_AnswerBox6_3").css('visibility', 'visible');
                $("#L2_AnswerBox6_3").append('<div>&nbsp;=&nbsp;<input id="L2_Q3_6_2" class="Input_Box"></input>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div>');
                $("#L2_Q3_6_2").attr('disabled', true);
                $("#L2_Q3_6_1").attr('disabled', true);
                $("#L2_Q3_6_1").attr('placeholder', (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3));
                 $("#L2_Q3_6_2").attr('placeholder',interactiveObj.L2_T3_2_1);
                $("#L2_AnswerBox6_4").css('visibility', 'visible');
                $("#L2_AnswerBox6_4").css('visible', 'visible');
                $("#L2_AnswerBox6_4").append('<div><div style="display:inline-block;">' + promptArr['txt_34'] + '&nbsp;&nbsp;</div><div style="display:inline-block;">' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;<input id="L2_Q3_6_3" class="Input_Box"/></div></div>')
                $("#L2_Q3_6_3").attr('disabled', true);
                $("#L2_Q3_6_3").attr('placeholder', interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3);

                $("#L2_AnswerBox6_5").css('visibility', 'visible');
                $("#L2_AnswerBox6_5").append('<div>' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q3_6_4" class="Input_Box"></input>', '<input id="L2_Q3_6_5" class="Input_Box"></input>') + '&nbsp;=&nbsp;' + createFrac('<input id="L2_Q3_6_6" class="Input_Box"></input>', interactiveObj.num3_1) + '</div>')

                $("#L2_Q3_6_4").attr('disabled', true);
                $("#L2_Q3_6_4").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                $("#L2_Q3_6_5").attr('disabled', true);
                $("#L2_Q3_6_5").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                $("#L2_Q3_6_6").attr('disabled', true);
                $("#L2_Q3_6_6").attr('placeholder', interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_2);

                $("#L2_Q6").css('height', '207px'); //height increased of third question
               if(interactiveObj.L2_Q3_incorrect_2_6_8==1)
               {
                $("#L2_Q3_6_1").addClass('wrongAnswer');
               }
               else
               {
                $("#L2_Q3_6_1").addClass('rightAnswer');
               }
            }
        }
        if(interactiveObj.L2_AdditonalQues6 == 4)
        {
                //////////alert("Appending 6th question of type 4")

                html3="";
                html3 += '<div id="L2_6_questions">';
                html3 += '<div id="L2_n1_2">' + interactiveObj.L2_T4_2_1 + '';
                html3 += '<div id="L2_fraction_6"><div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T4_2_2 + '</div><div class="frac">' + interactiveObj.L2_T4_2_3 + '</div></div>&nbsp;=&nbsp;</div>';
                html3 += '<div id="L2_inputBox_6"><input id="L2_Q4_6_1" class="Input_Box"></input></div>';

                html3 += '<div id="L2_AnswerBox4_6_1"></div>';
                html3 += '<div id="L2_AnswerBox4_6_2"></div>';
                html3 += '<div id="L2_AnswerBox4_6_3"></div>';
                html3 += '<div id="L2_AnswerBox4_6_4"></div>';

                html3 += '</div>';
                html3 += '</div>';

                $("#L2_Q6").append(html3);
                $("#L2_Q4_6_1").focus();

                if (interactiveObj.L2_attempt_Q6 == 1)
                {
                    if (interactiveObj.L2_4_incorrect_6_1 == 1)
                    {
                        $("#L2_Q4_6_1").focus();
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                    }
                    else if (interactiveObj.L2_4_correct_6_1 == 1)
                    {
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_Q4_6_1").attr('placeholder', (interactiveObj.L2_T4_2_3*interactiveObj.L2_T4_2_1+interactiveObj.L2_T4_2_2)/interactiveObj.L2_T4_2_3);
                        $("#L2_Q4_6_1").addClass('rightAnswer');
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 2)
                {
                    if (interactiveObj.L2_4_incorrect_6_2 == 1)
                    {
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        $("#L2_Q4_6_2").focus();
                    }
                    else if (interactiveObj.L2_4_correct_6_2 == 1)
                    {

                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        $("#L2_Q4_6_2").focus();
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 3)
                {
                    if (interactiveObj.L2_4_incorrect_6_3 == 1)
                    {
                        //previous blc'+promptArr['txt_7']+'s loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')

                        $("#L2_Q4_6_3").focus();
                    }
                    else if (interactiveObj.L2_4_correct_6_3 == 1)
                    {
                        //previous blc'+promptArr['txt_7']+'s loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')

                        $("#L2_Q4_6_3").focus();
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 4)
                {
                    if (interactiveObj.L2_4_incorrect_6_4 == 1)
                    {
                        //previous boxes loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')
                        $("#L2_Q4_6_3").attr('disabled', true);
                        
                        $("#L2_Q4_6_2").attr('disabled', true);

                        $("#L2_Q4_6_3").attr('placeholder', interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                        //
                        $("#L2_AnswerBox4_6_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_6_4" class="Input_Box"></input></div>');
                        $("#L2_Q4_6_4").focus();
                    }
                    else if(interactiveObj.L2_4_correct_6_4 == 1)
                    {
                        //previous boxes loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')
                        $("#L2_Q4_6_3").attr('disabled', true);
                        $("#L2_Q4_6_3").attr('placeholder', interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                        //
                        $("#L2_Q4_6_2").attr('disabled', true);
                         $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);

                        $("#L2_AnswerBox4_6_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_6_4" class="Input_Box"></input></div>');
                        $("#L2_Q4_6_4").focus();
                        //if(interactiveObj.L2_4_correct_6_3==1){$("#L2_Q4_6_4").attr('disabled', true);}
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 5)
                {
                    if (interactiveObj.L2_4_incorrect_6_5 == 1)
                    {
                        //previous boxes loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')
                        $("#L2_Q4_6_3").attr('disabled', true);
                        $("#L2_Q4_6_3").attr('placeholder', interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                        //
                        $("#L2_AnswerBox4_6_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_6_4" class="Input_Box"></input></div>');
                        $("#L2_Q4_6_4").focus();
                    }
                    else if (interactiveObj.L2_4_correct_6_5 == 1)
                    {
                        //previous boxes loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')
                        $("#L2_Q4_6_3").attr('disabled', true);
                        $("#L2_Q4_6_3").attr('placeholder', interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                        //
                        $("#L2_AnswerBox4_6_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_6_4" class="Input_Box"></input></div>');
                        $("#L2_Q4_6_4").attr('disabled',true);
                        $("#L2_Q4_6_4").attr('placeholder',((interactiveObj.L2_T4_2_3*interactiveObj.L2_T4_2_1+interactiveObj.L2_T4_2_2)/interactiveObj.L2_T4_2_3));
                         $("#L2_Q4_6_4").addClass('rightAnswer');
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 6)
                {
                    if (interactiveObj.L2_4_incorrect_6_6 == 1)
                    {
                        //previous boxes loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')
                        $("#L2_Q4_6_3").attr('disabled', true);
                        $("#L2_Q4_6_3").attr('placeholder', interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                        //
                        $("#L2_AnswerBox4_6_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_6_4" class="Input_Box"></input></div>');
                        $("#L2_Q4_6_4").attr('disabled', true);
                        $("#L2_Q4_6_4").attr('placeholder', (interactiveObj.L2_T4_2_3 * interactiveObj.L2_T4_2_1 + interactiveObj.L2_T4_2_2) / interactiveObj.L2_T4_2_3);
                    
                        $("#L2_Q4_6_4").addClass('wrongAnswer');
                    }
                    else if (interactiveObj.L2_4_correct_6_6 == 1)
                    {
                        //previous boxes loaded
                        $("#L2_Q4_6_1").attr('placeholder', interactiveObj.L2_T4_2_1);
                        $("#L2_Q4_6_1").attr('disabled', true);
                        $("#L2_AnswerBox4_6_1").append('<div>&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div>');
                        $("#L2_AnswerBox4_6_2").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;&nbsp;( &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp; &#215; &nbsp;' + createFrac('<input id="L2_Q4_6_2" class="Input_Box"></input>', interactiveObj.multiplyBy2) + '&nbsp; ) &nbsp;</div>');
                        //----//
                        $("#L2_Q4_6_2").attr('placeholder', interactiveObj.multiplyBy2);
                        $("#L2_Q4_6_2").attr('disabled', true);
                        $("#L2_AnswerBox4_6_3").append('<div>&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + ' &nbsp;+&nbsp;' + createFrac('<input id="L2_Q4_6_3" class="Input_Box"></input>', interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '</div>')
                        $("#L2_Q4_6_3").attr('disabled', true);
                        $("#L2_Q4_6_3").attr('placeholder', interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                        //
                        $("#L2_AnswerBox4_6_4").append('<div>&nbsp;=&nbsp;<input id="L2_Q4_6_4" class="Input_Box"></input></div>');
                        $("#L2_Q4_6_4").attr('disabled', true);
                        $("#L2_Q4_6_4").attr('placeholder', (interactiveObj.L2_T4_2_3 * interactiveObj.L2_T4_2_1 + interactiveObj.L2_T4_2_2) / interactiveObj.L2_T4_2_3);
                    
                        $("#L2_Q4_6_4").addClass('rightAnswer');
                    }
                }
        }
    }
}
questionInteractive.prototype.L2_checkAnswer = function(id2)
{
    interactiveObj.L2_QType = id2;

    $("#L2_prompt").html('');
    html2 = "";

    if (interactiveObj.L2_QType == "one")
    {

        //prompts//
        interactiveObj.num = createFrac(interactiveObj.L2_T1_2, interactiveObj.L2_T1_3);
        interactiveObj.num2 = createFrac(interactiveObj.L2_T1_2, interactiveObj.L2_T1_3)

        if (interactiveObj.L2_T1_3 == 100)
        {
            interactiveObj.place = promptArr['txt_26'];
            interactiveObj.place = interactiveObj.place.toLowerCase();

        }
        else
        {
            interactiveObj.place = promptArr['txt_25'];
            interactiveObj.place = interactiveObj.place.toLowerCase();
        }

        html2 += '<div id="L2_Incorrect" class="correct" style="top: 81px;"><div class="sparkie"></div><div id="L2_txt">' + promptArr['txt_29'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:57px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect_2" class="correct" style="top: 78px;"><div class="sparkie"></div><div id="L2_txt2">' + interactiveObj.L2_T1_1 + '<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2 + '</div><div class="frac">' + interactiveObj.L2_T1_3 + '</div></div>&nbsp;=&nbsp;<div id="L2_secondPart" style="display:inline-block;">' + interactiveObj.L2_T1_1 + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2 + '</div><div class="frac">' + interactiveObj.L2_T1_3 + '</div></div></div></div><button id="L2_B1" class=buttonPrompt style="left: 86px;top: 61px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect_3"class="correct" style="top: 82px;left: 359px;"><div class="sparkie"></div><div id="L2_txt3">' + createFrac(interactiveObj.L2_T1_2,interactiveObj.L2_T1_3) + ' '+promptArr['txt_45']+' ' + interactiveObj.L2_T1_2 + ' ' + interactiveObj.place + '</div><button id="L2_B1" class=buttonPrompt style="left: 90px;top: 50px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Final_Animation" class="correct"><div class="sparkie"></div><div id="L2_animationHeader">' + replaceDynamicText(promptArr['txt_27'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp; ' + interactiveObj.L2_T1_2 + '&nbsp;&nbsp;' + interactiveObj.place + '</div><div id="L2_animation_table"></div><button id="L2_B1" class=buttonPrompt style="left: 147px;top:225px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="correct_Answer" class="correct" style="top: 84px;"><div class="sparkie"></div><div id="L2_txt4">' + interactiveObj.L2_T1_1 + '' + createFrac(interactiveObj.L2_T1_2, interactiveObj.L2_T1_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T1_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T1_2, interactiveObj.L2_T1_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T1_1 + '&nbsp;+ &nbsp;' + interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3 + '&nbsp;=&nbsp;' + (interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3 + '</div><button id="L2_B1" class=buttonPrompt style="left:126px;top:66px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        $("#L2_popOut").html(html2);
        $(".correct").draggable({containment: "#container"});

        interactiveObj.L2_attempt_Q1 += 1;

        if (interactiveObj.L2_attempt_Q1 == 1)
        {

            interactiveObj.answer = parseFloat($("#L2_Q1_1").val());
            interactiveObj.correctAnswer = ((interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1) + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3;


            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
                interactiveObj.L2_incorrect_1 = 1;
                $("#L2_Incorrect").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                
                  extraParameterArr[1]+="Stage 2:-(Q1:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+"~";
            }
            else
            {
                // code for answer correct 
                interactiveObj.L2_correct_1 = 1;
                interactiveObj.L2_Q2_visible = 1;
                interactiveObj.Additoanl_Ques1=1;
             extraParameterArr[1]+="Stage 2:-(Q1:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+")";
                interactiveObj.L2_ScoreType1=10;

                interactiveObj.L2_Score=interactiveObj.L2_ScoreType1;
                
                levelWiseScoreArr[Ltype-1]+=parseInt(interactiveObj.L2_Score);
                interactiveObj.correctCounter2+=1;


                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 2)
        {
            interactiveObj.answer = $("#L2_Q1_2").val();
            interactiveObj.correctAnswer = interactiveObj.L2_T1_1;
            extraParameterArr[1]+=interactiveObj.answer+"~";

            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
                //user answer is wrong
                interactiveObj.L2_incorrect_2 = 1;
                $("#L2_Incorrect_2").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
            else
            {
                //code for correct answers 
                interactiveObj.L2_correct_2 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 3)
        {
            interactiveObj.answer = $("#L2_Q1_3").val();
            interactiveObj.correctAnswer = interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3;
            extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
                interactiveObj.L2_incorrect_3 = 1;

                $("#L2_Incorrect_3").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                //code for correct answrs 
                interactiveObj.L2_correct_3 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 4)
        {
            
            if (interactiveObj.L2_correct_3 == 1)
            {
                interactiveObj.answer = $("#L2_Q1_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3;

                //------//change in code
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_4 = 1;

                    //$("#L2_Final_Animation").css('visibility', 'visible');
                    //$(".buttonPrompt").attr('disabled',true);                    
                    //window.setTimeout(function(){$(".buttonPrompt").attr('disabled',false);},6000);
                    //window.setTimeout(function(){ $(".buttonPrompt").focus();},6200);

                    $("#correct_Answer").css('visibility','visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    //window.setTimeout("interactiveObj.L2_animation();", 1000);
                      extraParameterArr[1]+=interactiveObj.answer+"~";
                }
                else
                {
                    //code for correct answer
                    interactiveObj.L2_correct_4 = 1;
                    interactiveObj.Additoanl_Ques1=1;
                    interactiveObj.L2_Q2_visible = 1;
                    interactiveObj.L2_ScoreType1=5;
                    extraParameterArr[1]+=interactiveObj.answer+")";
                    interactiveObj.L2_Score=interactiveObj.L2_ScoreType1;
                    levelWiseScoreArr[Ltype-1]+=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;
                  

                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                //------//

            }
            if (interactiveObj.L2_incorrect_3 == 1)
            {
                interactiveObj.answer = $("#L2_Q1_3").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3;
               // ////////alert("this is where problem arises");
                 extraParameterArr[1]+=interactiveObj.answer+"~";
                //------//change in code
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_4 = 1;
                    $("#L2_Final_Animation").css('visibility', 'visible');
                    $(".buttonPrompt").attr('disabled',true); 

                     window.setTimeout(function(){$(".buttonPrompt").attr('disabled',false);},6000);
                    window.setTimeout(function(){ $(".buttonPrompt").focus();},6200);
                   // $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    window.setTimeout("interactiveObj.L2_animation();", 1000);

                }
                else
                {
                    //code for correct answer
                    interactiveObj.L2_correct_4 = 1;
                    interactiveObj.L2_correct_4_1 = 1;

                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                //------//
            }
        }
        if (interactiveObj.L2_attempt_Q1 == 5)//done
        {
            interactiveObj.answer = $("#L2_Q1_4").val();
            interactiveObj.correctAnswer = (interactiveObj.L2_T1_3 * interactiveObj.L2_T1_1 + interactiveObj.L2_T1_2) / interactiveObj.L2_T1_3;
            extraParameterArr[1]+=interactiveObj.correctAnswer+":"+interactiveObj.answer+")";
            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
                interactiveObj.L2_incorrect_5 = 1;
                interactiveObj.L2_Q2_visible = 1;
                $("#correct_Answer").css('visibility', 'visible');
                $(".buttonPrompt").focus();
            }
            else
            {
                //code for correct answer 
                interactiveObj.L2_correct_5 = 1;
                interactiveObj.L2_Q2_visible = 1;
                interactiveObj.Additoanl_Ques1=1;
                 interactiveObj.L2_ScoreType1=5;

                 interactiveObj.L2_Score=interactiveObj.L2_ScoreType1;

                levelWiseScoreArr[Ltype-1]+=parseInt(interactiveObj.L2_Score);
                interactiveObj.correctCounter2+=1;
                // levelWiseScore=levelWiseScore+"|"+interactiveObj.L2_Score;

                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
    }
    if (interactiveObj.L2_QType == "two")
    {
        html2 = "";
       
        interactiveObj.L2_T2_1=parseInt(interactiveObj.L2_T2_1);
        interactiveObj.L2_T2_2=parseInt(interactiveObj.L2_T2_2);
        interactiveObj.L2_T2_3=parseInt(interactiveObj.L2_T2_3);

        interactiveObj.num = createFrac(interactiveObj.L2_T2_2, interactiveObj.L2_T2_3);
        interactiveObj.num2 = createFrac(interactiveObj.L2_T2_2, interactiveObj.L2_T2_3);
        interactiveObj.place = promptArr['txt_26'];
        interactiveObj.place = interactiveObj.place.toLowerCase();


        html2 += '<div id="L2_Incorrect" class="correct" style="top: 169px;"><div class="sparkie"></div><div id="L2_txt">' + promptArr['txt_29'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:57px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect_2" class="correct" style="top: 168px;"><div class="sparkie"></div><div id="L2_txt2">' + interactiveObj.L2_T2_1 + '<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_3 + '</div></div>&nbsp;=&nbsp;<div id="L2_secondPart" style="display:inline-block;">' + interactiveObj.L2_T2_1 + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_3 + '</div></div></div></div><button id="L2_B1" class=buttonPrompt style="left: 86px;top: 61px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect_3"class="correct"  style="top: 169px; left: 362px;"><div class="sparkie"></div><div id="L2_txt3">' + createFrac(interactiveObj.L2_T2_2,interactiveObj.L2_T2_3) + ' is ' + interactiveObj.L2_T2_2 + ' ' + interactiveObj.place + '</div><button id="L2_B1" class=buttonPrompt style="left: 90px;top: 50px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Final_Animation" class="correct"  style="top: 159px;"><div class="sparkie"></div><div id="L2_animationHeader">' + replaceDynamicText(promptArr['txt_27'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp; ' + interactiveObj.L2_T2_2 + '&nbsp;&nbsp;' + interactiveObj.place + '</div><div id="L2_animation_table"></div><button id="L2_B1" class=buttonPrompt style="left: 147px;top:225px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="correct_Answer" class="correct"  style="top: 159px;"><div class="sparkie"></div><div id="L2_txt4">' + interactiveObj.L2_T2_1 + '' + createFrac(interactiveObj.L2_T2_2, interactiveObj.L2_T2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T2_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T2_2, interactiveObj.L2_T2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T2_1 + '&nbsp;+ &nbsp;' + interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3 + '&nbsp;=&nbsp;' + (interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3 + '</div><button id="L2_B1" class=buttonPrompt style="left:126px;top:66px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="wellDone_LF" class="correct"  style="top: 119px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        $("#L2_popOut").html(html2);
        $(".correct").draggable({containment: "#container"});

        interactiveObj.L2_2_attempt_Q1 += 1;

        if (interactiveObj.L2_2_attempt_Q1 == 1)
        {
            ////////alert("Attemp1")
            interactiveObj.answer = parseFloat($("#L2_Q2_1").val());
            interactiveObj.correctAnswer = ((interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1) + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3;

            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
             
                interactiveObj.L2_2_incorrect_1 = 1;
                $("#L2_Incorrect").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                 
                 extraParameterArr[1]+="(Q2:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+"~";

            }
            else
            {
                // code for answer correct 
               
                interactiveObj.L2_2_correct_1 = 1;
                interactiveObj.L2_Q3_visible = 1;
                interactiveObj.Additoanl_Ques2=1;
                interactiveObj.L2_ScoreType2=10;
                
                 
                 interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                 extraParameterArr[1]+="(Q2:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+")";
                 levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                 interactiveObj.correctCounter2+=1;

                 //levelWiseScore=levelWiseScore+"|"+interactiveObj.L2_Score;
                
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 2)
        {
            interactiveObj.answer = $("#L2_Q2_2").val();
            interactiveObj.correctAnswer = interactiveObj.L2_T2_1;
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
                //user answer is wrong
                //////////alert("Attemp2 ICorrect")
                interactiveObj.L2_2_incorrect_2 = 1;
                $("#L2_Incorrect_2").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                //code for correct answers 
               // ////////alert("Attemp2 Correct")
                interactiveObj.L2_2_correct_2 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 3)
        {
            interactiveObj.answer = $("#L2_Q2_3").val();
            interactiveObj.correctAnswer = interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3;
            extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
                interactiveObj.L2_2_incorrect_3 = 1;
                ////////alert("Attemp3 ICorrect")

                $("#L2_Incorrect_3").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                //code for correct answrs 
                ////////alert("Attemp3 Correct")
                interactiveObj.L2_2_correct_3 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 4)
        {
            if (interactiveObj.L2_2_correct_3 == 1)
            {
                interactiveObj.answer = $("#L2_Q2_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3;
                
                //------//change in code
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                   // ////////alert("Attemp4  attempt correct")
                     extraParameterArr[1]+=interactiveObj.correctAnswer+":"+interactiveObj.answer+"~";
                    interactiveObj.L2_2_incorrect_4 = 1;
                    $("#L2_Final_Animation").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    window.setTimeout("interactiveObj.L2_animation();", 1000);
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                }
                else
                {
                    //code for correct answer
                    
                     interactiveObj.L2_2_correct_4 = 1;
                     //interactiveObj.L2_Q3_visible = 1;
                     interactiveObj.L2_ScoreType2=5;
                     interactiveObj.Additoanl_Ques2=1;

                    interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;
                   extraParameterArr[1]+=interactiveObj.answer+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                //------//
            }
            if (interactiveObj.L2_2_incorrect_3 == 1)
            {
                interactiveObj.answer = $("#L2_Q2_3").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3;
           

                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                   
                    interactiveObj.L2_2_incorrect_4 = 1;
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                    $("#L2_Final_Animation").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    window.setTimeout("interactiveObj.L2_animation();", 1000);
                }
                else
                {
                    //code for correct answer
                    interactiveObj.L2_2_correct_4 = 1;
                    interactiveObj.L2_2_correct_4_1 = 1;
                    //interactiveObj.L2_Q3_visible = 1;
                    interactiveObj.L2_ScoreType2=5;
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                     interactiveObj.Additoanl_Ques2=1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                //------//
            }
        }
        if (interactiveObj.L2_2_attempt_Q1 == 5)//done
        {
            interactiveObj.answer = $("#L2_Q2_4").val();
            interactiveObj.correctAnswer = (interactiveObj.L2_T2_3 * interactiveObj.L2_T2_1 + interactiveObj.L2_T2_2) / interactiveObj.L2_T2_3;
             extraParameterArr[1]+=interactiveObj.answer+")";
            if (interactiveObj.answer != interactiveObj.correctAnswer)
            {
        
                interactiveObj.L2_2_incorrect_5 = 1;
                interactiveObj.L2_Q3_visible = 1;
               // interactiveObj.Additoanl_Ques3=1;
                $("#correct_Answer").css('visibility', 'visible');
                $(".buttonPrompt").focus();
            }
            else
            {
                //code for correct answer 
                ////////alert("Attemp 5 Correct")
                interactiveObj.L2_2_correct_5 = 1;
                interactiveObj.L2_Q3_visible = 1;
                interactiveObj.Additoanl_Ques2=1;
                 interactiveObj.L2_ScoreType2=5;

                 interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                 levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                 interactiveObj.correctCounter2+=1;
                 

                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
    }
    if (interactiveObj.L2_QType == "three")
    {
        html2 = "";

        if (interactiveObj.L2_T3_3 == 2 || interactiveObj.L2_T3_3 == 5)
        {
            interactiveObj.divideBy = parseInt(10);
            interactiveObj.num3_1 = interactiveObj.divideBy;
        }
        else
        interactiveObj.num3_1 = parseInt(100);
        interactiveObj.num3_2 = createFrac(interactiveObj.num3_1 / interactiveObj.L2_T3_3, interactiveObj.num3_1 / interactiveObj.L2_T3_3);
        interactiveObj.num3_3 = createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3);
        interactiveObj.num3_4 = createFrac(interactiveObj.L2_T3_2 * interactiveObj.num3_1 / interactiveObj.L2_T3_3, interactiveObj.L2_T3_3 * interactiveObj.num3_1 / interactiveObj.L2_T3_3);
        interactiveObj.num3_5 = interactiveObj.L2_T3_2 / interactiveObj.L2_T3_3;

        html2 += '<div id="L2_Incorrect" class="correct" style="top: 223px;"><div class="sparkie"></div><div id="L2_txt">' + replaceDynamicText(promptArr['txt_43'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:73px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Additon" class="correct" style="top: 252px;left: 232px;"><div class="sparkie"></div><div  style="margin-left:33px;margin-top:-25px;">' + interactiveObj.L2_T3_1 + '' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T3_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left:76px;top:65px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_messageSimplify" class="correct" style="top:291px;left:399px;"><div class="sparkie"></div><div style="margin-left:40px;margin-top:-37px;">' + promptArr['txt_31'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 102px;top:43px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_messageEquivalence" class="correct" style="width:380px;top:225px;left:383px;"><div class="sparkie"></div><div style="margin-top:-26px;margin-left:34px;">' + replaceDynamicText(promptArr['txt_32'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 156px;top:71px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_messageEquivalence2" class="correct" style="top: 247px;left: 391px;"><div class="sparkie"></div><div style="margin-left:29px;margin-top: -23px;">' + replaceDynamicText(promptArr['txt_33'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 149px;top: 74px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Answer" class="correct" style="top: 235px;left: 243px;"><div class="sparkie"></div><div style="margin-left: 37px;margin-top: -22px;">' + interactiveObj.L2_T3_1 + '' + createFrac(interactiveObj.L2_T3_2, interactiveObj.L2_T3_3) + '&nbsp;=&nbsp' + interactiveObj.L2_T3_1 + '&nbsp;+&nbsp;' + interactiveObj.L2_T3_2 / interactiveObj.L2_T3_3 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 79px;top: 74px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Answer2"class="correct" ><div class="sparkie"></div><div style="margin-left:54px;margin-top:-23px;">' + interactiveObj.L2_T3_2 + '&nbsp;&#215;&nbsp;' + interactiveObj.num3_1 / interactiveObj.L2_T3_3 + '&nbsp;=&nbsp;' + (interactiveObj.num3_1 / interactiveObj.L2_T3_3) * interactiveObj.L2_T3_2 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 62px;top:61px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="wellDone_LF" class="correct"  style="top: 119px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

        $("#L2_popOut").html(html2);
        $(".correct").draggable({containment: "#container"});

        interactiveObj.L2_3_attempt_Q1 += 1;

        if (interactiveObj.L2_3_attempt_Q1 == 1)
        {
            interactiveObj.answer = parseFloat($("#L2_Q3_1").val());
            interactiveObj.correctAnswer3 = (interactiveObj.L2_T3_3 * interactiveObj.L2_T3_1 + interactiveObj.L2_T3_2) / interactiveObj.L2_T3_3;

            if (interactiveObj.answer != interactiveObj.correctAnswer3)
            {
                //user answer not correct
                interactiveObj.L2_Q3_incorrect_1 = 1;
                //interactiveObj.sub2=1;
                extraParameterArr[1]+="(Q3:"+interactiveObj.correctAnswer3+":"+interactiveObj.answer+"~";
                $("#L2_Incorrect").css('visibility', 'visible');
                $(".buttonPrompt").focus();
            }
            else
            {
                //code for correct answer is to be written
                interactiveObj.L2_Q3_correct_1 = 1;
                interactiveObj.L2_Q4_visible = 1;
                interactiveObj.Additoanl_Ques3=1;
                interactiveObj.L2_ScoreType3=10;

                interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                 levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                 interactiveObj.correctCounter2+=1;
                  extraParameterArr[1]+="(Q3:"+interactiveObj.correctAnswer3+":"+interactiveObj.answer+")";
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 2)
        {
            interactiveObj.answer = parseInt($("#L2_Q3_2").val());
            interactiveObj.correctAnswer3 = parseInt(interactiveObj.L2_T3_1);
            extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correctAnswer3)
            {
                //user answer not correct
                interactiveObj.L2_Q3_incorrect_2 = 1;
                $("#L2_Additon").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                //code for correct answer is to be written
                interactiveObj.L2_Q3_correct_2 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 3)
        {
            interactiveObj.answer = parseFloat($("#L2_Q3_3").val());
            interactiveObj.correct_Answer3 = interactiveObj.L2_T3_2 / interactiveObj.L2_T3_3;
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correct_Answer3)
            {
                //user answer correct
                interactiveObj.L2_Q3_incorrect_3 = 1;
                $("#L2_messageSimplify").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                //code for correct answer is to be written
                interactiveObj.L2_Q3_correct_3 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 4)
        {

            if (interactiveObj.L2_Q3_correct_3 == 1) //if answer from third attempt is right
            {
               // ////////alert("second LAst attempt was correct")
                interactiveObj.answer = parseFloat($("#L2_Q3_1").val());
                interactiveObj.correct_Answer3 = (parseInt(interactiveObj.L2_T3_3) * parseInt(interactiveObj.L2_T3_1) + parseInt(interactiveObj.L2_T3_2)) / parseInt(interactiveObj.L2_T3_3);

                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                   
                    interactiveObj.L2_Q3_check=1;

                    interactiveObj.L2_Q3_incorrect_4 = 1;
                    $("#L2_Answer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                     extraParameterArr[1]+=interactiveObj.answer+")";
                }
                else
                {
                    //code when anwers is right
                    interactiveObj.L2_Q3_check=0;

                    interactiveObj.L2_Q3_correct_4 = 1;
                    interactiveObj.L2_Q4_visible = 1;
                    interactiveObj.Additoanl_Ques3=1;
                    interactiveObj.L2_ScoreType3=5;

                     interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;
                     extraParameterArr[1]+=interactiveObj.answer+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            else if (interactiveObj.L2_Q3_incorrect_3 == 1) // when attempts are made wrong in continous manner
            {
               // ////////alert(" second  LAst attempt was incorrect")
                interactiveObj.answer = parseInt($("#L2_Q3_5").val());
                interactiveObj.correct_Answer3 = parseInt(interactiveObj.num3_1 / interactiveObj.L2_T3_3);
                 extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_4 = 1;
                    interactiveObj.L2_loadQuestions();
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_4 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 5)
        {
            interactiveObj.answer = parseInt($("#L2_Q3_4").val());
            interactiveObj.correct_Answer3 = interactiveObj.num3_1 / interactiveObj.L2_T3_3;
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correct_Answer3)
            {
                interactiveObj.L2_Q3_incorrect_5 = 1;
                $("#L2_messageEquivalence").css('visibility', 'visible');
                $(".buttonPrompt").focus();
            }
            else
            {
                //code for correct answer is to be written
                interactiveObj.L2_Q3_correct_5 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 6)
        {
            interactiveObj.answer = parseInt($("#L2_Q3_6").val());
            interactiveObj.correct_Answer3 = (interactiveObj.num3_1 / interactiveObj.L2_T3_3) * interactiveObj.L2_T3_2;
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correct_Answer3)
            {
                interactiveObj.L2_Q3_incorrect_6 = 1;
                $("#L2_Answer2").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
            else
            {
                //code for correct answer is yet to be written
                interactiveObj.L2_Q3_correct_6 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 7)
        {
            interactiveObj.answer = parseFloat($("#L2_Q3_3").val());
            interactiveObj.correct_Answer3 = interactiveObj.L2_T3_2 / interactiveObj.L2_T3_3;
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correct_Answer3)
            {
                interactiveObj.L2_Q3_incorrect_7 = 1;
                $("#L2_messageEquivalence2").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
            else
            {
                //code for correct answer is yet to be written
                interactiveObj.L2_Q3_correct_7 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_3_attempt_Q1 == 8)
        {
            interactiveObj.answer = parseFloat($("#L2_Q3_1").val());
            interactiveObj.correct_Answer3 = (parseInt(interactiveObj.L2_T3_3) * parseInt(interactiveObj.L2_T3_1) + parseInt(interactiveObj.L2_T3_2)) / parseInt(interactiveObj.L2_T3_3);
             extraParameterArr[1]+=interactiveObj.answer+")";
            if (interactiveObj.answer != interactiveObj.correct_Answer3)
            {
                interactiveObj.L2_Q3_incorrect_8 = 1;
                interactiveObj.L2_Q4_visible = 1;
                $("#L2_Answer").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                interactiveObj.L2_Q4_visible = 1;
 
                interactiveObj.L2_Q3_correct_8 = 1;
                interactiveObj.Additoanl_Ques3=1;

                interactiveObj.L2_ScoreType3=5;

                 interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                 levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                 interactiveObj.correctCounter2+=1;

                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
    }
    if (interactiveObj.L2_QType == "four")
    {
        html2 = "";

        interactiveObj.num4_1 = interactiveObj.L2_T4_1;

        if(interactiveObj.L2_T4_3==20 || interactiveObj.L2_T4_3==25 || interactiveObj.L2_T4_3==50)
        {
            interactiveObj.num4_11=parseInt(100);
        }
        else
        {
            interactiveObj.num4_11=parseInt(1000);
        }

        interactiveObj.num4_2 = createFrac(interactiveObj.L2_T4_2 * interactiveObj.multiplyBy, interactiveObj.L2_T4_3 * interactiveObj.multiplyBy);

        html2 += '<div id="L2_Incorrect" class="correct" style="top: 291px;left: 393px;"><div class="sparkie"></div><div id="L2_txt"><div>' + replaceDynamicText(promptArr['txt_37'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 79px;top:60px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect4_2" class="correct" style="width: 206px;height: 94px;position: absolute;top: -7px;left: 33px;"><div class="sparkie"></div><div style="margin-left: 44px;margin-top: -31px;">' + interactiveObj.L2_T4_1 + '' + createFrac(interactiveObj.L2_T4_2, interactiveObj.L2_T4_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T4_1 + '&nbsp; + &nbsp;' + createFrac(interactiveObj.L2_T4_2, interactiveObj.L2_T4_3) + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 81px;top:58px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect4_3" class="correct" style="left: 107px;"><div class="sparkie"></div><div style="margin-left: 35px;margin-top: -16px;width: 218px;">' + promptArr['txt_35'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 109px;top:108px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect4_4" class="correct" style="width: 177px;height: 79px;top: 70px;left: 195px;"><div class="sparkie"></div><div style="margin-left: 45px;margin-top: -33px;">' + interactiveObj.L2_T4_2 + ' &nbsp; &#215; &nbsp; '+interactiveObj.multiplyBy+' &nbsp;= &nbsp;'+(interactiveObj.multiplyBy*interactiveObj.L2_T4_2)+'</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 77px;top:49px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect4_5" class="correct" style="width: 234px;height: 118px;position: absolute;top: -57px;left: 101px;"><div class="sparkie"></div><div style="margin-left: 41px;margin-top: -30px;">' + replaceDynamicText(promptArr['txt_36'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 93px;top:83px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="L2_Incorrect4_6" class="correct" style="width: 179px;height: 126px;position: absolute;top: 49px;left: 109px;"><div class="sparkie"></div><div style="margin-left: 44px;margin-top: -35px;line-height: 38px;">' + createFrac(interactiveObj.L2_T4_2 * interactiveObj.multiplyBy, interactiveObj.L2_T4_3 * interactiveObj.multiplyBy) + '&nbsp;=&nbsp;' + interactiveObj.L2_T4_2 / interactiveObj.L2_T4_3 + '<br/>' + interactiveObj.L2_T4_1 + '&nbsp;+&nbsp;' + (interactiveObj.L2_T4_2 / interactiveObj.L2_T4_3) + '&nbsp;=&nbsp;' + (interactiveObj.L2_T4_3 * interactiveObj.L2_T4_1 + interactiveObj.L2_T4_2) / interactiveObj.L2_T4_3 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 72px;top:97px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
        html2 += '<div id="wellDone_LF" class="correct"  style="top: -23px;left: 25px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
        // all the prompts go in here

        $("#L2_popOut").html(html2);
        $(".correct").draggable({containment: "#container"});

        interactiveObj.L2_4_attempt_Q1 += 1;

        if (interactiveObj.L2_4_attempt_Q1 == 1)
        {
            interactiveObj.answer = parseFloat($("#L2_Q4_1").val());
            interactiveObj.correct_Answer4 = (parseInt(interactiveObj.L2_T4_3) * parseInt(interactiveObj.L2_T4_1) + parseInt(interactiveObj.L2_T4_2)) / parseInt(interactiveObj.L2_T4_3);

            if (interactiveObj.answer != interactiveObj.correct_Answer4)
            {
                interactiveObj.L2_4_incorrect_1 = 1;
                $("#L2_Incorrect").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                extraParameterArr[1]+="(Q4:"+interactiveObj.correct_Answer4+":"+interactiveObj.answer+"~";
            }
            else
            {
                //code for correct part
                interactiveObj.L2_4_correct_1 = 1;
                interactiveObj.Additoanl_Ques4=1;
                interactiveObj.L2_ScoreType4=10;

                interactiveObj.L2_Score+=interactiveObj.L2_ScoreType4;
                levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                interactiveObj.correctCounter2+=1;
                extraParameterArr[1]+="(Q4:"+interactiveObj.correct_Answer4+":"+interactiveObj.answer+")";
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                interactiveObj.L2_getResult();


            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 2)
        {
            interactiveObj.answer = parseFloat($("#L2_Q4_1").val());
            interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.L2_T4_1);
            
            extraParameterArr[1]+=interactiveObj.answer+"~";

            if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
            {
                interactiveObj.L2_4_incorrect_2 = 1;
                $("#L2_Incorrect4_2").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
            else
            {
                //code for correct answer
                interactiveObj.L2_4_correct_2 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);

            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 3)
        {
            interactiveObj.answer = parseFloat($("#L2_Q4_2").val());
            interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.multiplyBy);
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
            {

                interactiveObj.L2_4_incorrect_3 = 1;
                $("#L2_Incorrect4_3").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                //code for correct asnwer
                interactiveObj.L2_4_correct_3 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 4)
        {
            interactiveObj.answer = parseInt($("#L2_Q4_3").val());
            interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.L2_T4_2 * interactiveObj.multiplyBy);
             extraParameterArr[1]+=interactiveObj.answer+"~";
            if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
            {

                interactiveObj.L2_4_incorrect_4 = 1;
                $("#L2_Incorrect4_4").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
            else
            {
                interactiveObj.L2_4_correct_4 = 1;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 5)
        {
            interactiveObj.answer = parseFloat($("#L2_Q4_4").val());
            interactiveObj.correct_Answer4 = (parseInt(interactiveObj.L2_T4_3) * parseInt(interactiveObj.L2_T4_1) + parseInt(interactiveObj.L2_T4_2)) / parseInt(interactiveObj.L2_T4_3);
             
            if (interactiveObj.answer != interactiveObj.correct_Answer4)
            {
                interactiveObj.L2_4_incorrect_5 = 1;
                $("#L2_Incorrect4_5").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                extraParameterArr[1]+=interactiveObj.answer+"~";
            }
            else
            {
                interactiveObj.L2_4_correct_5 = 1;
                interactiveObj.Additoanl_Ques4=1;
                interactiveObj.L2_ScoreType4=5;
                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                interactiveObj.L2_getResult();
                extraParameterArr[1]+=interactiveObj.answer+")";
            }
        }
        if (interactiveObj.L2_4_attempt_Q1 == 6)
        {
            interactiveObj.answer = parseFloat($("#L2_Q4_4").val());
            interactiveObj.correct_Answer4 = (parseInt(interactiveObj.L2_T4_3) * parseInt(interactiveObj.L2_T4_1) + parseInt(interactiveObj.L2_T4_2)) / parseInt(interactiveObj.L2_T4_3);
            
            if (interactiveObj.answer != interactiveObj.correct_Answer4)
            {
                interactiveObj.L2_4_incorrect_6 = 1;
                $("#L2_Incorrect4_6").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                extraParameterArr[1]+=interactiveObj.answer+")";
                interactiveObj.L2_getResult();
            }
            else
            {
                interactiveObj.L2_4_correct_6 = 1;
                interactiveObj.Additoanl_Ques4=1;
                interactiveObj.L2_ScoreType4=5;

                interactiveObj.L2_Score+=interactiveObj.L2_ScoreType4;
                levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                interactiveObj.correctCounter2+=1;

                $("#wellDone_LF").css('visibility', 'visible');
                $(".buttonPrompt").focus();
                $(".Input_Box").attr('disabled', true);
                extraParameterArr[1]+=interactiveObj.answer+")";
                interactiveObj.L2_getResult();
            }
        }
    }
    if (interactiveObj.L2_QType == "five")
    {
   
        if(interactiveObj.L2_AdditonalQues5==1)//done
        {
           // ////////alert("In type 1 of ques 5")
            interactiveObj.num = createFrac(interactiveObj.L2_T1_2_2, interactiveObj.L2_T1_2_3);
            interactiveObj.num2 = createFrac(interactiveObj.L2_T1_2_2, interactiveObj.L2_T1_2_3);

            if (interactiveObj.L2_T1_2_3 == 100)
            {
                interactiveObj.place = promptArr['txt_26'];
                 interactiveObj.place = interactiveObj.place.toLowerCase();

            }
            else
            {
                interactiveObj.place = promptArr['txt_25'];
                 interactiveObj.place = interactiveObj.place.toLowerCase();
            }

            html12="";
            html2 += '<div id="L2_Incorrect" class="correct" style="top: 243px;left: 282px;"><div class="sparkie"></div><div id="L2_txt">' + promptArr['txt_29'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:57px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect_2" class="correct" style="top: 251px;"><div class="sparkie"></div><div id="L2_txt2">' + interactiveObj.L2_T1_2_1 + '<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div>&nbsp;=&nbsp;<div id="L2_secondPart" style="display:inline-block;">' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T1_2_2 + '</div><div class="frac">' + interactiveObj.L2_T1_2_3 + '</div></div></div></div><button id="L2_B1" class=buttonPrompt style="left: 86px;top: 61px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect_3"class="correct" style="top: 251px;"><div class="sparkie"></div><div id="L2_txt3">' + createFrac(interactiveObj.L2_T1_2_2,interactiveObj.L2_T1_2_3) + ' is ' + interactiveObj.L2_T1_2_2 + ' ' + interactiveObj.place + '</div><button id="L2_B1" class=buttonPrompt style="left: 90px;top: 50px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Final_Animation" class="correct" style="top: 251px;"><div class="sparkie"></div><div id="L2_animationHeader">' + replaceDynamicText(promptArr['txt_27'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp; ' + interactiveObj.L2_T1_2_2 + '&nbsp;&nbsp;' + interactiveObj.place + '</div><div id="L2_animation_table"></div><button id="L2_B1" class=buttonPrompt style="left: 147px;top:225px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="correct_Answer" class="correct" style="top: 251px;"><div class="sparkie"></div><div id="L2_txt4">' + interactiveObj.L2_T1_2_1 + '' + createFrac(interactiveObj.L2_T1_2_2, interactiveObj.L2_T1_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T1_2_2, interactiveObj.L2_T1_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T1_2_1 + '&nbsp;+ &nbsp;' + interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3 + '&nbsp;=&nbsp;' + (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3 + '</div><button id="L2_B1" class=buttonPrompt style="left:126px;top:66px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="wellDone_LF" class="correct" style="top: 251px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            $("#L2_popOut").html(html2);
            $(".correct").draggable({containment: "#container"});

            interactiveObj.L2_attempt_Q5 += 1;

            if (interactiveObj.L2_attempt_Q5 == 1)
            {

                interactiveObj.answer = parseFloat($("#L2_Q1_5_1").val());
                interactiveObj.correctAnswer = ((interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1) + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3;

                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    ////////alert("Answer Incorrect")
                    interactiveObj.L2_incorrect_2_1 = 1;
                    $("#L2_Incorrect").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    extraParameterArr[1]+="(Q5:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+"~";

                }
                else
                {   ////////alert("Answer correct")
                    // code for answer correct 
                    interactiveObj.L2_correct_2_1 = 1;
                    //interactiveObj.L2_Q2_visible = 1;
                    //interactiveObj.Additoanl_Ques1=1;
                    interactiveObj.L2_Q6_visible=1;
                    interactiveObj.L2_ScoreType1+=10;
                    interactiveObj.L2_Score+=interactiveObj.L2_ScoreType1;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;
                    extraParameterArr[1]+="(Q5:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);


                }
            }
            if (interactiveObj.L2_attempt_Q5 == 2)
            {
                interactiveObj.answer = $("#L2_Q1_5_2").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T1_2_1;

                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    //user answer is wrong
                    interactiveObj.L2_incorrect_2_2 = 1;
                    $("#L2_Incorrect_2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answers 
                    interactiveObj.L2_correct_2_2 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 3)
            {
                interactiveObj.answer = $("#L2_Q1_5_3").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3;

                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_2_3 = 1;

                    $("#L2_Incorrect_3").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answrs 
                    interactiveObj.L2_correct_2_3 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 4)
            {
                interactiveObj.answer = $("#L2_Q1_5_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3;

                if (interactiveObj.L2_correct_2_3 == 1)
                {
                    interactiveObj.answer = $("#L2_Q1_5_4").val();
                    interactiveObj.correctAnswer = (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3;

                    //------//change in code
                    if (interactiveObj.answer != interactiveObj.correctAnswer)
                    {
                        interactiveObj.L2_incorrect_2_4 = 1;
                        $("#L2_Final_Animation").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        window.setTimeout("interactiveObj.L2_animation();", 1000);

                            extraParameterArr[1]+=interactiveObj.answer+"~";
                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_correct_2_4 = 1;
                        //interactiveObj.L2_Q2_visible = 1;
                        interactiveObj.L2_Q6_visible=1;
                        interactiveObj.L2_ScoreType1+=5;
                         interactiveObj.L2_Score+=interactiveObj.L2_ScoreType1;
                        levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                        interactiveObj.correctCounter2+=1;

                        extraParameterArr[1]+=interactiveObj.answer+")";

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    //------//

                }
                if (interactiveObj.L2_incorrect_2_3 == 1)
                {
                    interactiveObj.answer = $("#L2_Q1_5_3").val();
                    interactiveObj.correctAnswer = interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3;
                    //////////alert("this is where problem arises");


                    //------//change in code
                    if (interactiveObj.answer != interactiveObj.correctAnswer)
                    {
                        interactiveObj.L2_incorrect_2_4 = 1;
                        $("#L2_Final_Animation").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        window.setTimeout("interactiveObj.L2_animation();", 1000);
                        extraParameterArr[0]+=interactiveObj.answer+"~";
                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_correct_2_4 = 1;
                        interactiveObj.L2_correct_2_4_1 = 1;

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        extraParameterArr[0]+=interactiveObj.answer+"~";
                    }
                    //------//
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 5)
            {
                interactiveObj.answer = $("#L2_Q1_5_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T1_2_3 * interactiveObj.L2_T1_2_1 + interactiveObj.L2_T1_2_2) / interactiveObj.L2_T1_2_3;
                extraParameterArr[1]+=interactiveObj.answer+")";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_2_5 = 1;
                   // interactiveObj.L2_Q2_visible = 1;
                   interactiveObj.L2_Q6_visible=1;
                    $("#correct_Answer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else
                {
                    //code for correct answer 
                    interactiveObj.L2_correct_2_5 = 1;
                    //interactiveObj.L2_Q2_visible = 1;
                    //interactiveObj.Additoanl_Ques1=0;
                    interactiveObj.L2_Q6_visible=1;
                     interactiveObj.L2_ScoreType1+=5;
                      interactiveObj.L2_Score+=interactiveObj.L2_ScoreType1;
                     levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                     interactiveObj.correctCounter2+=1;


                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
        }

        if(interactiveObj.L2_AdditonalQues5==2)
        {
            

            interactiveObj.L2_T2_2_1=parseInt(interactiveObj.L2_T2_2_1);
            interactiveObj.L2_T2_2_2=parseInt(interactiveObj.L2_T2_2_2);
            interactiveObj.L2_T2_2_3=parseInt(interactiveObj.L2_T2_2_3);

            interactiveObj.num = createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3);
            interactiveObj.num2 = createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3);


            html12="";
            html2 += '<div id="L2_Incorrect" class="correct"><div class="sparkie"></div><div id="L2_txt">' + promptArr['txt_29'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:57px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect_2" class="correct"><div class="sparkie"></div><div id="L2_txt2">' + interactiveObj.L2_T2_2_1 + '<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div>&nbsp;=&nbsp;<div id="L2_secondPart" style="display:inline-block;">' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div></div><button id="L2_B1" class=buttonPrompt style="left: 86px;top: 61px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect_3"class="correct"><div class="sparkie"></div><div id="L2_txt3">' + createFrac(interactiveObj.L2_T2_2_2,interactiveObj.L2_T2_2_3) + ' is ' + interactiveObj.L2_T2_2_2 + ' ' + interactiveObj.place + '</div><button id="L2_B1" class=buttonPrompt style="left: 90px;top: 50px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Final_Animation" class="correct"><div class="sparkie"></div><div id="L2_animationHeader">' + replaceDynamicText(promptArr['txt_27'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp; ' + interactiveObj.L2_T2_2_2 + '&nbsp;&nbsp;' + interactiveObj.place + '</div><div id="L2_animation_table"></div><button id="L2_B1" class=buttonPrompt style="left: 147px;top:225px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="correct_Answer" class="correct"><div class="sparkie"></div><div id="L2_txt4">' + interactiveObj.L2_T2_2_1 + '' + createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+ &nbsp;' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '&nbsp;=&nbsp;' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '</div><button id="L2_B1" class=buttonPrompt style="left:126px;top:66px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            $("#L2_popOut").html(html2);
            $(".correct").draggable({containment: "#container"});

            interactiveObj.L2_attempt_Q5 += 1;

            if (interactiveObj.L2_attempt_Q5 == 1)
            {

                interactiveObj.answer = parseFloat($("#L2_Q1_5_1").val());
                interactiveObj.correctAnswer = ((interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1) + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;

                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    ////////alert("Answer Incorrect")
                    interactiveObj.L2_incorrect_2_1 = 1;
                    $("#L2_Incorrect").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    extraParameterArr[1]+="(Q5:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+"~";

                }
                else
                {   ////////alert("Answer correct")
                    // code for answer correct 
                    interactiveObj.L2_correct_2_1 = 1;
                    //interactiveObj.L2_Q2_visible = 1;
                    //interactiveObj.Additoanl_Ques1=1;
                    interactiveObj.L2_Q6_visible=1;
                     interactiveObj.L2_ScoreType2+=10;
                      interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                     levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                     interactiveObj.correctCounter2+=1;

                     extraParameterArr[1]+="(Q5:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);


                }
            }
            if (interactiveObj.L2_attempt_Q5 == 2)
            {
                interactiveObj.answer = $("#L2_Q1_5_2").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T2_2_1;

                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    //user answer is wrong
                    interactiveObj.L2_incorrect_2_2 = 1;
                    $("#L2_Incorrect_2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answers 
                    interactiveObj.L2_correct_2_2 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 3)
            {
                interactiveObj.answer = $("#L2_Q1_5_3").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_2_3 = 1;

                    $("#L2_Incorrect_3").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answrs 
                    interactiveObj.L2_correct_2_3 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 4)
            {
                interactiveObj.answer = $("#L2_Q1_5_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;

                if (interactiveObj.L2_correct_2_3 == 1)
                {
                    interactiveObj.answer = $("#L2_Q1_5_4").val();
                    interactiveObj.correctAnswer = (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;

                    //------//change in code
                    if (interactiveObj.answer != interactiveObj.correctAnswer)
                    {
                        interactiveObj.L2_incorrect_2_4 = 1;
                        $("#L2_Final_Animation").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        window.setTimeout("interactiveObj.L2_animation();", 1000);
                        extraParameterArr[1]+=interactiveObj.answer+"~";
                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_correct_2_4 = 1;
                        //interactiveObj.L2_Q2_visible = 1;
                        interactiveObj.L2_Q6_visible=1;
                        interactiveObj.L2_ScoreType2+=5;
                         interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;
                    extraParameterArr[1]+=interactiveObj.answer+")";
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    //------//

                }
                if (interactiveObj.L2_incorrect_2_3 == 1)
                {
                    interactiveObj.answer = $("#L2_Q1_5_3").val();
                    interactiveObj.correctAnswer = interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3;
                    //////////alert("this is where problem arises");


                    //------//change in code
                    if (interactiveObj.answer != interactiveObj.correctAnswer)
                    {
                        interactiveObj.L2_incorrect_2_4 = 1;
                        $("#L2_Final_Animation").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        window.setTimeout("interactiveObj.L2_animation();", 1000);
                        extraParameterArr[0]+=interactiveObj.answer+"~";
                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_correct_2_4 = 1;
                        interactiveObj.L2_correct_2_4_1 = 1;
                        extraParameterArr[0]+=interactiveObj.answer+"~";

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    //------//


                }
            }
            if (interactiveObj.L2_attempt_Q5 == 5)
            {
                interactiveObj.answer = $("#L2_Q1_5_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;
                extraParameterArr[1]+=interactiveObj.answer+")";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_2_5 = 1;
                   // interactiveObj.L2_Q2_visible = 1;
                   interactiveObj.L2_Q6_visible=1;
                    $("#correct_Answer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else
                {
                    //code for correct answer 
                    interactiveObj.L2_correct_2_5 = 1;
                    //interactiveObj.L2_Q2_visible = 1;
                    //interactiveObj.Additoanl_Ques1=0;
                    interactiveObj.L2_Q6_visible=1;
                    interactiveObj.L2_ScoreType2+=5;
                     
                     interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                     levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                     interactiveObj.correctCounter2+=1;

                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
        }

        if(interactiveObj.L2_AdditonalQues5==3)
        {

            html2 = "";

            if (interactiveObj.L2_T3_2_3 == 2 || interactiveObj.L2_T3_2_3 == 5)
            {
                interactiveObj.divideBy = parseInt(10);
                interactiveObj.num3_1 = interactiveObj.divideBy;
            }
            else
            interactiveObj.num3_1 = parseInt(100);
            interactiveObj.num3_2 = createFrac(interactiveObj.num3_1 / interactiveObj.L2_T3_2_3, interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
            interactiveObj.num3_3 = createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3);
            interactiveObj.num3_4 = createFrac(interactiveObj.L2_T3_2_2 * interactiveObj.num3_1 / interactiveObj.L2_T3_2_3, interactiveObj.L2_T3_2_3 * interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
            interactiveObj.num3_5 = interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3;

            html2 += '<div id="L2_Incorrect" class="correct" style="top: 223px;"><div class="sparkie"></div><div id="L2_txt">' + replaceDynamicText(promptArr['txt_30'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:73px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Additon" class="correct" style="top: 252px;left: 232px;"><div class="sparkie"></div><div  style="margin-left:33px;margin-top:-25px;">' + interactiveObj.L2_T3_2_1 + '' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T3_2_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left:76px;top:65px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_messageSimplify" class="correct" style="top:403px;left:399px;"><div class="sparkie"></div><div style="margin-left:40px;margin-top:-37px;">' + promptArr['txt_31'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 102px;top:43px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_messageEquivalence" class="correct" style="width:380px;top:225px;left:383px;"><div class="sparkie"></div><div style="margin-top:-26px;margin-left:34px;">' + replaceDynamicText(promptArr['txt_32'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 156px;top:71px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_messageEquivalence2" class="correct" style="top: 247px;left: 391px;"><div class="sparkie"></div><div style="margin-left:29px;margin-top: -23px;">' + replaceDynamicText(promptArr['txt_33'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 149px;top: 74px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Answer" class="correct" style="top: 212px;left: 243px;"><div class="sparkie"></div><div style="margin-left: 37px;margin-top: -22px;">' + interactiveObj.L2_T3_2_1 + '' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp' + interactiveObj.L2_T3_2_1 + '&nbsp;+&nbsp;' + interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 79px;top: 74px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Answer2"class="correct" style="top:390"><div class="sparkie"></div><div style="margin-left:54px;margin-top:-23px;">' + interactiveObj.L2_T3_2_2 + '&nbsp;&#215;&nbsp;' + interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 + '&nbsp;=&nbsp;' + (interactiveObj.num3_1 / interactiveObj.L2_T3_2_3) * interactiveObj.L2_T3_2_2 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 62px;top:61px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"  style="top: 119px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            $("#L2_popOut").html(html2);
            $(".correct").draggable({containment: "#container"});

            interactiveObj.L2_attempt_Q5 += 1;

            if (interactiveObj.L2_attempt_Q5 == 1)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_5_1").val());
                interactiveObj.correctAnswer3 = (interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_1 + interactiveObj.L2_T3_2_2) / interactiveObj.L2_T3_2_3;

                if (interactiveObj.answer != interactiveObj.correctAnswer3)
                {
                    //user answer not correct
                    interactiveObj.L2_Q3_incorrect_2_1 = 1;
                    //interactiveObj.sub2=1;
                    extraParameterArr[1]+="(Q5:"+interactiveObj.correctAnswer3+":"+interactiveObj.answer+"~";
                    $("#L2_Incorrect").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_1 = 1;
                   // interactiveObj.L2_Q4_visible = 1;
                    //interactiveObj.Additoanl_Ques3=1;
                    interactiveObj.L2_Q6_visible=1;
                    interactiveObj.L2_ScoreType3+=10;
                    extraParameterArr[1]+="(Q5:"+interactiveObj.correctAnswer3+":"+interactiveObj.answer+")";
                    interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;

                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 2)
            {
                interactiveObj.answer = parseInt($("#L2_Q3_5_2").val());
                interactiveObj.correctAnswer3 = parseInt(interactiveObj.L2_T3_2_1);
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer3)
                {
                    //user answer not correct
                    interactiveObj.L2_Q3_incorrect_2_2 = 1;
                    $("#L2_Additon").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_2 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 3)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_5_3").val());
                interactiveObj.correct_Answer3 = interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    //user answer correct
                    interactiveObj.L2_Q3_incorrect_2_3 = 1;
                    $("#L2_messageSimplify").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_3 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);

                }
            }
            if (interactiveObj.L2_attempt_Q5 == 4)
            {

                if (interactiveObj.L2_Q3_correct_2_3 == 1) //if answer from third attempt is right
                {
                   // ////////alert("second LAst attempt was correct")
                    interactiveObj.answer = parseFloat($("#L2_Q3_5_1").val());
                    interactiveObj.correct_Answer3 = (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3);

                    if (interactiveObj.answer != interactiveObj.correct_Answer3)
                    {
                        interactiveObj.L2_Q3_incorrect_2_4 = 1;
                        $("#L2_Answer").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        extraParameterArr[1]+=interactiveObj.answer+"~";

                        interactiveObj.L2_Q3_2_check=1;
                        interactiveObj.L2_Q6_visible=1;

                    }
                    else
                    {
                        //code when anwers is right

                        interactiveObj.L2_Q3_correct_2_4 = 1;
                        interactiveObj.L2_Q3_2_check=0;
                      //  interactiveObj.L2_Q4_visible = 1;
                      //  interactiveObj.Additoanl_Ques3=1;
                      interactiveObj.L2_Q6_visible=1;
                      interactiveObj.L2_ScoreType3+=5;
                      extraParameterArr[1]+=interactiveObj.answer+")";
                       interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
                else if (interactiveObj.L2_Q3_incorrect_2_3 == 1) // when attempts are made wrong in continous manner
                {
                   // ////////alert(" second  LAst attempt was incorrect")
                    interactiveObj.answer = parseInt($("#L2_Q3_5_5").val());
                    interactiveObj.correct_Answer3 = parseInt(interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);

                    if (interactiveObj.answer != interactiveObj.correct_Answer3)
                    {
                        interactiveObj.L2_Q3_incorrect_2_4 = 1;
                        extraParameterArr[0]+=interactiveObj.answer+"~";
                        interactiveObj.L2_loadQuestions();
                    }
                    else
                    {
                        //code for correct answer is to be written
                        interactiveObj.L2_Q3_correct_2_4 = 1;
                        extraParameterArr[0]+=interactiveObj.answer+"~";
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 5)
            {
                interactiveObj.answer = parseInt($("#L2_Q3_5_4").val());
                interactiveObj.correct_Answer3 = interactiveObj.num3_1 / interactiveObj.L2_T3_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_5 = 1;
                    $("#L2_messageEquivalence").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_5 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 6)
            {
                interactiveObj.answer = parseInt($("#L2_Q3_5_6").val());
                interactiveObj.correct_Answer3 = (interactiveObj.num3_1 / interactiveObj.L2_T3_2_3) * interactiveObj.L2_T3_2_2;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_6 = 1;
                    $("#L2_Answer2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);

                }
                else
                {
                    //code for correct answer is yet to be written
                    interactiveObj.L2_Q3_correct_2_6 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 7)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_5_3").val());
                interactiveObj.correct_Answer3 = interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_7 = 1;
                    $("#L2_messageEquivalence2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);

                }
                else
                {
                    //code for correct answer is yet to be written
                    interactiveObj.L2_Q3_correct_2_7 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q5 == 8)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_5_1").val());
                interactiveObj.correct_Answer3 = (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3);
                extraParameterArr[1]+=interactiveObj.answer+")";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_8 = 1;
                   // interactiveObj.L2_Q4_visible = 1;
                    interactiveObj.L2_Q6_visible=1;
                    $("#L2_Answer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    interactiveObj.L2_Q4_visible = 1;
                    //code for correct answer is yet to be written
                    interactiveObj.L2_Q3_correct_2_8 = 1;
                 //   interactiveObj.Additoanl_Ques3=1;
                    interactiveObj.L2_Q6_visible=1;
                    interactiveObj.L2_ScoreType3+=5;
                     
                         interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;

                   
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
        }
    }
    if(interactiveObj.L2_QType == "six")
    {
        if(interactiveObj.L2_AdditonalQues6 == 2)
        {

           // ////////alert("In type 1 of ques 5")
            interactiveObj.num = createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3);
            interactiveObj.num2 = createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3);

            interactiveObj.L2_T2_2_1=parseInt(interactiveObj.L2_T2_2_1);
            interactiveObj.L2_T2_2_2=parseInt(interactiveObj.L2_T2_2_2);
            interactiveObj.L2_T2_2_3=parseInt(interactiveObj.L2_T2_2_3);


            if (interactiveObj.L2_T2_2_3 == 100)
            {
                interactiveObj.place = promptArr['txt_26'];
                interactiveObj.place = interactiveObj.place.toLowerCase();

            }
            else
            {
                interactiveObj.place = promptArr['txt_25'];
                interactiveObj.place = interactiveObj.place.toLowerCase();
            }

            html12="";
            html2 += '<div id="L2_Incorrect" class="correct"><div class="sparkie"></div><div id="L2_txt">' + promptArr['txt_29'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:57px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect_2" class="correct"><div class="sparkie"></div><div id="L2_txt2">' + interactiveObj.L2_T2_2_1 + '<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div>&nbsp;=&nbsp;<div id="L2_secondPart" style="display:inline-block;">' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;<div class="fraction"><div class="frac numerator">' + interactiveObj.L2_T2_2_2 + '</div><div class="frac">' + interactiveObj.L2_T2_2_3 + '</div></div></div></div><button id="L2_B1" class=buttonPrompt style="left: 86px;top: 61px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect_3"class="correct"><div class="sparkie"></div><div id="L2_txt3">' + createFrac(interactiveObj.L2_T2_2_2,interactiveObj.L2_T2_2_3) + ' is ' + interactiveObj.L2_T2_2_2 + ' ' + interactiveObj.place + '</div><button id="L2_B1" class=buttonPrompt style="left: 90px;top: 50px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Final_Animation" class="correct"><div class="sparkie"></div><div id="L2_animationHeader">' + replaceDynamicText(promptArr['txt_27'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;=&nbsp; ' + interactiveObj.L2_T2_2_2 + '&nbsp;&nbsp;' + interactiveObj.place + '</div><div id="L2_animation_table"></div><button id="L2_B1" class=buttonPrompt style="left: 147px;top:225px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="correct_Answer" class="correct"><div class="sparkie"></div><div id="L2_txt4">' + interactiveObj.L2_T2_2_1 + '' + createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T2_2_2, interactiveObj.L2_T2_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T2_2_1 + '&nbsp;+ &nbsp;' + interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3 + '&nbsp;=&nbsp;' + (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3 + '</div><button id="L2_B1" class=buttonPrompt style="left:126px;top:66px;"onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            $("#L2_popOut").html(html2);
            $(".correct").draggable({containment: "#container"});

            interactiveObj.L2_attempt_Q6 += 1;

            if (interactiveObj.L2_attempt_Q6 == 1)
            {

                interactiveObj.answer = parseFloat($("#L2_Q1_6_1").val());
                interactiveObj.correctAnswer = ((interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1) + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;

                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    //////////alert("Answer Incorrect")
                    interactiveObj.L2_incorrect_2_6_1 = 1;
                    $("#L2_Incorrect").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                    extraParameterArr[1]+="(Q6:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+"~";

                }
                else
                {   //////////alert("Answer correct")
                    // code for answer correct 
                    interactiveObj.L2_correct_2_6_1 = 1;
                    interactiveObj.L2_ScoreType2+=10;
                         interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                     levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                     interactiveObj.correctCounter2+=1;
                     interactiveObj.L2_FinalResult();

                    //interactiveObj.L2_Q2_visible = 1;
                    //interactiveObj.Additoanl_Ques1=1;
                    //interactiveObj.L2_Q6_visible=1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                     extraParameterArr[1]+="(Q6:"+interactiveObj.correctAnswer+":"+interactiveObj.answer+")";


                }
            }
            if (interactiveObj.L2_attempt_Q6 == 2)
            {
                interactiveObj.answer = $("#L2_Q1_6_2").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T2_2_1;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    //user answer is wrong
                    interactiveObj.L2_incorrect_2_6_2 = 1;
                    $("#L2_Incorrect_2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answers 
                    interactiveObj.L2_correct_2_6_2 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 3)
            {
                interactiveObj.answer = $("#L2_Q1_6_3").val();
                interactiveObj.correctAnswer = interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_2_6_3 = 1;

                    $("#L2_Incorrect_3").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answrs 
                    interactiveObj.L2_correct_2_6_3 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 4)
            {
                interactiveObj.answer = $("#L2_Q1_6_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;

                if (interactiveObj.L2_correct_2_6_3 == 1)
                {
                    interactiveObj.answer = $("#L2_Q1_6_4").val();
                    interactiveObj.correctAnswer = (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;

                    //------//change in code
                    if (interactiveObj.answer != interactiveObj.correctAnswer)
                    {
                        interactiveObj.L2_incorrect_2_6_4 = 1;
                        $("#L2_Final_Animation").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        window.setTimeout("interactiveObj.L2_animation();", 1000);
                        extraParameterArr[1]+=interactiveObj.answer+"~";
                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_correct_2_6_4 = 1;
                        //interactiveObj.L2_Q2_visible = 1;
                        //interactiveObj.L2_Q6_visible=1;
                        interactiveObj.L2_ScoreType2+=5;
                            interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                    levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                    interactiveObj.correctCounter2+=1;
                    interactiveObj.L2_FinalResult();
                    extraParameterArr[1]+=interactiveObj.answer+")";
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    //------//
                }
                if (interactiveObj.L2_incorrect_2_6_3 == 1)
                {
                    interactiveObj.answer = $("#L2_Q1_6_3").val();
                    interactiveObj.correctAnswer = interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3;
                    //////////alert("this is where problem arises");


                    //------//change in code
                    if (interactiveObj.answer != interactiveObj.correctAnswer)
                    {
                        interactiveObj.L2_incorrect_2_6_4 = 1;
                        $("#L2_Final_Animation").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        window.setTimeout("interactiveObj.L2_animation();", 1000);
                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_correct_2_6_4 = 1;
                        interactiveObj.L2_correct_2_4_6_1 = 1;

                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    //------//
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 5)
            {
                interactiveObj.answer = $("#L2_Q1_5_4").val();
                interactiveObj.correctAnswer = (interactiveObj.L2_T2_2_3 * interactiveObj.L2_T2_2_1 + interactiveObj.L2_T2_2_2) / interactiveObj.L2_T2_2_3;
                extraParameterArr[1]+=interactiveObj.answer+")";
                if (interactiveObj.answer != interactiveObj.correctAnswer)
                {
                    interactiveObj.L2_incorrect_2_6_5 = 1;
                       interactiveObj.L2_FinalResult();
                   // interactiveObj.L2_Q2_visible = 1;
                   //interactiveObj.L2_Q6_visible=1;
                    $("#correct_Answer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else
                {
                    //code for correct answer 
                    interactiveObj.L2_correct_2_6_5 = 1;
                    //interactiveObj.L2_Q2_visible = 1;
                    iinteractiveObj.L2_ScoreType2+=5;
                        interactiveObj.L2_Score+=interactiveObj.L2_ScoreType2;
                     levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                     interactiveObj.correctCounter2+=1;
                     interactiveObj.L2_FinalResult();
                    //interactiveObj.Additoanl_Ques1=0;
                    //interactiveObj.L2_Q6_visible=1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }   
        }//closure for type 2
        if(interactiveObj.L2_AdditonalQues6 == 3)
        {
            

            html2 = "";

            if (interactiveObj.L2_T3_2_3 == 2 || interactiveObj.L2_T3_2_3 == 5)
            {
                interactiveObj.divideBy = parseInt(10);
                interactiveObj.num3_1 = interactiveObj.divideBy;
            }
            else
            interactiveObj.num3_1 = parseInt(100);
            interactiveObj.num3_2 = createFrac(interactiveObj.num3_1 / interactiveObj.L2_T3_2_3, interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
            interactiveObj.num3_3 = createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3);
            interactiveObj.num3_4 = createFrac(interactiveObj.L2_T3_2_2 * interactiveObj.num3_1 / interactiveObj.L2_T3_2_3, interactiveObj.L2_T3_2_3 * interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
            interactiveObj.num3_5 = interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3;

            html2 += '<div id="L2_Incorrect" class="correct" style="top: 223px;"><div class="sparkie"></div><div id="L2_txt">' + replaceDynamicText(promptArr['txt_30'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 116px;top:73px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Additon" class="correct" style="top: 252px;left: 232px;"><div class="sparkie"></div><div  style="margin-left:33px;margin-top:-25px;">' + interactiveObj.L2_T3_2_1 + '' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T3_2_1 + '&nbsp;+&nbsp;' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left:76px;top:65px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_messageSimplify" class="correct" style="top:291px;left:399px;"><div class="sparkie"></div><div style="margin-left:40px;margin-top:-37px;">' + promptArr['txt_31'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 102px;top:43px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_messageEquivalence" class="correct" style="width:380px;top:225px;left:383px;"><div class="sparkie"></div><div style="margin-top:-26px;margin-left:34px;">' + replaceDynamicText(promptArr['txt_32'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 156px;top:71px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_messageEquivalence2" class="correct" style="top: 247px;left: 391px;"><div class="sparkie"></div><div style="margin-left:29px;margin-top: -23px;">' + replaceDynamicText(promptArr['txt_33'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 149px;top: 74px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Answer" class="correct" style="top: 212px;left: 243px;"><div class="sparkie"></div><div style="margin-left: 37px;margin-top: -22px;">' + interactiveObj.L2_T3_2_1 + '' + createFrac(interactiveObj.L2_T3_2_2, interactiveObj.L2_T3_2_3) + '&nbsp;=&nbsp' + interactiveObj.L2_T3_2_1 + '&nbsp;+&nbsp;' + interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 79px;top: 74px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Answer2"class="correct" ><div class="sparkie"></div><div style="margin-left:54px;margin-top:-23px;">' + interactiveObj.L2_T3_2_2 + '&nbsp;&#215;&nbsp;' + interactiveObj.num3_1 / interactiveObj.L2_T3_2_3 + '&nbsp;=&nbsp;' + (interactiveObj.num3_1 / interactiveObj.L2_T3_2_3) * interactiveObj.L2_T3_2_2 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 62px;top:61px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"  style="top: 119px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';

            $("#L2_popOut").html(html2);
            $(".correct").draggable({containment: "#container"});

            interactiveObj.L2_attempt_Q6 += 1;

            if (interactiveObj.L2_attempt_Q6 == 1)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_6_1").val());
                interactiveObj.correctAnswer3 = (interactiveObj.L2_T3_2_3 * interactiveObj.L2_T3_2_1 + interactiveObj.L2_T3_2_2) / interactiveObj.L2_T3_2_3;

                if (interactiveObj.answer != interactiveObj.correctAnswer3)
                {
                    //user answer not correct
                    interactiveObj.L2_Q3_incorrect_2_6_1 = 1;
                    //interactiveObj.sub2=1;

                    $("#L2_Incorrect").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    extraParameterArr[1]+="(Q6:"+interactiveObj.correctAnswer3+":"+interactiveObj.answer+"~";
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_6_1 = 1;
                   // interactiveObj.L2_Q4_visible = 1;
                    //interactiveObj.Additoanl_Ques3=1;
                   // interactiveObj.L2_Q6_visible=1;
                   interactiveObj.L2_ScoreType3+=10;
                        interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                     levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                     interactiveObj.correctCounter2+=1;
                     interactiveObj.L2_FinalResult();
                      extraParameterArr[1]+="(Q6:"+interactiveObj.correctAnswer3+":"+interactiveObj.answer+")";
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 2)
            {
                interactiveObj.answer = parseInt($("#L2_Q3_6_2").val());
                interactiveObj.correctAnswer3 = parseInt(interactiveObj.L2_T3_2_1);
               extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correctAnswer3)
                {
                    //user answer not correct
                    interactiveObj.L2_Q3_incorrect_2_6_2 = 1;
                    $("#L2_Additon").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_6_2 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 3)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_6_3").val());
                interactiveObj.correct_Answer3 = interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    //user answer correct
                    interactiveObj.L2_Q3_incorrect_2_6_3 = 1;
                    $("#L2_messageSimplify").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_6_3 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);

                }
            }
            if (interactiveObj.L2_attempt_Q6 == 4)
            {

                if (interactiveObj.L2_Q3_correct_2_6_3 == 1) //if answer from third attempt is right
                {
                   // ////////alert("second LAst attempt was correct")
                    interactiveObj.answer = parseFloat($("#L2_Q3_6_1").val());
                    interactiveObj.correct_Answer3 = (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3);

                    if (interactiveObj.answer != interactiveObj.correct_Answer3)
                    {
                        interactiveObj.L2_Q3_incorrect_2_6_4 = 1;
                        extraParameterArr[1]+=interactiveObj.answer+")";
                        $("#L2_Answer").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);

                         interactiveObj.L2_Q3_2_6_check=1;
                    }
                    else
                    {
                        //code when anwers is right
                        interactiveObj.L2_Q3_2_6_check=0;

                        interactiveObj.L2_Q3_correct_2_6_4 = 1;
                        interactiveObj.L2_ScoreType3+=5;
                               interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                                levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                                interactiveObj.correctCounter2+=1;
                                interactiveObj.L2_FinalResult();
                                extraParameterArr[1]+=interactiveObj.answer+")";
                      //  interactiveObj.L2_Q4_visible = 1;
                      //  interactiveObj.Additoanl_Ques3=1;
                     // interactiveObj.L2_Q6_visible=1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
                else if (interactiveObj.L2_Q3_incorrect_2_6_3 == 1) // when attempts are made wrong in continous manner
                {
                   // ////////alert(" second  LAst attempt was incorrect")
                    interactiveObj.answer = parseInt($("#L2_Q3_6_5").val());
                    interactiveObj.correct_Answer3 = parseInt(interactiveObj.num3_1 / interactiveObj.L2_T3_2_3);
                    extraParameterArr[0]+=interactiveObj.answer+"~";
                    if (interactiveObj.answer != interactiveObj.correct_Answer3)
                    {
                        interactiveObj.L2_Q3_incorrect_2_6_4 = 1;
                        interactiveObj.L2_loadQuestions();
                    }
                    else
                    {
                        //code for correct answer is to be written
                        interactiveObj.L2_Q3_correct_2_6_4 = 1;
                        
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 5)
            {
                interactiveObj.answer = parseInt($("#L2_Q3_6_4").val());
                interactiveObj.correct_Answer3 = interactiveObj.num3_1 / interactiveObj.L2_T3_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_6_5 = 1;
                    $("#L2_messageEquivalence").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                }
                else
                {
                    //code for correct answer is to be written
                    interactiveObj.L2_Q3_correct_2_6_5 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 6)
            {
                interactiveObj.answer = parseInt($("#L2_Q3_6_6").val());
                interactiveObj.correct_Answer3 = (interactiveObj.num3_1 / interactiveObj.L2_T3_2_3) * interactiveObj.L2_T3_2_2;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_6_6 = 1;
                    $("#L2_Answer2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);

                }
                else
                {
                    //code for correct answer is yet to be written
                    interactiveObj.L2_Q3_correct_2_6_6 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 7)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_6_3").val());
                interactiveObj.correct_Answer3 = interactiveObj.L2_T3_2_2 / interactiveObj.L2_T3_2_3;
                extraParameterArr[1]+=interactiveObj.answer+"~";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_6_7 = 1;
                    $("#L2_messageEquivalence2").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);

                }
                else
                {
                    //code for correct answer is yet to be written
                    interactiveObj.L2_Q3_correct_2_6_7 = 1;
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
            if (interactiveObj.L2_attempt_Q6 == 8)
            {
                interactiveObj.answer = parseFloat($("#L2_Q3_6_1").val());
                interactiveObj.correct_Answer3 = (parseInt(interactiveObj.L2_T3_2_3) * parseInt(interactiveObj.L2_T3_2_1) + parseInt(interactiveObj.L2_T3_2_2)) / parseInt(interactiveObj.L2_T3_2_3);
                extraParameterArr[1]+=interactiveObj.answer+")";
                if (interactiveObj.answer != interactiveObj.correct_Answer3)
                {
                    interactiveObj.L2_Q3_incorrect_2_6_8 = 1;
                       interactiveObj.L2_FinalResult();
                   // interactiveObj.L2_Q4_visible = 1;
                   // interactiveObj.L2_Q6_visible=1;
                    $("#L2_Answer").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
                else
                {
                 //   interactiveObj.L2_Q4_visible = 1;
                    //code for correct answer is yet to be written
                    interactiveObj.L2_Q3_correct_2_6_8 = 1;
                    interactiveObj.L2_ScoreType3+=5;
                          interactiveObj.L2_Score+=interactiveObj.L2_ScoreType3;
                          levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                          interactiveObj.correctCounter2+=1;
                          interactiveObj.L2_FinalResult();
                 //   interactiveObj.Additoanl_Ques3=1;
                   // interactiveObj.L2_Q6_visible=1;
                   // ////////alert("THird questrion correct")
                    $("#wellDone_LF").css('visibility', 'visible');
                    $(".buttonPrompt").focus();
                    $(".Input_Box").attr('disabled', true);
                }
            }
        }//closure for type 3
        if(interactiveObj.L2_AdditonalQues6 == 4)
        {
                
            html2 = "";

            interactiveObj.num4_1 = interactiveObj.L2_T4_2_1;
            interactiveObj.num4_2 = createFrac(interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2, interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2);

            if(interactiveObj.L2_T4_2_3==20 || interactiveObj.L2_T4_2_3==25 || interactiveObj.L2_T4_2_3==50)
            {
                interactiveObj.num4_11=parseInt(100);
            }
            else
            {
                interactiveObj.num4_11=parseInt(1000);
            }


            html2 += '<div id="L2_Incorrect" class="correct" style="top: 223px;"><div class="sparkie"></div><div id="L2_txt"><div>' + replaceDynamicText(promptArr['txt_37'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 79px;top:60px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect4_2" class="correct" style="width: 206px;height: 94px;position: absolute;top: 103px;left: 44px;"><div class="sparkie"></div><div style="margin-left: 44px;margin-top: -31px;">' + interactiveObj.L2_T4_2_1 + '' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_1 + '&nbsp; + &nbsp;' + createFrac(interactiveObj.L2_T4_2_2, interactiveObj.L2_T4_2_3) + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 81px;top:58px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect4_3" class="correct" style="margin-left: 13px;margin-top: -11px;text-align: center;position:absolute;top: 204px;left: 271px;" ><div class="sparkie"></div><div>' + promptArr['txt_35'] + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 109px;top:108px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect4_4" class="correct" style="width: 160px;height: 79px;top: 150px;left: 355px;"><div class="sparkie"></div><div style="margin-left: 45px;margin-top: -33px;">' + interactiveObj.L2_T4_2_2 + ' &nbsp; &#215; &nbsp; '+interactiveObj.multiplyBy2+' &nbsp;= &nbsp;'+(interactiveObj.multiplyBy2*interactiveObj.L2_T4_2_2)+'</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 62px;top:49px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect4_5" class="correct" style="width: 234px;height: 118px;position: absolute;top: 125px;left: 333px;"><div class="sparkie"></div><div style="margin-left: 41px;margin-top: -30px;">' + replaceDynamicText(promptArr['txt_36'], interactiveObj.numberLanguage, "interactiveObj") + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 93px;top:83px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="L2_Incorrect4_6" class="correct" style="width: 179px;height: 126px;position: absolute;top: -8px;left: 365px"><div class="sparkie"></div><div style="margin-left: 44px;margin-top: -35px;line-height: 38px;">' + createFrac(interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2, interactiveObj.L2_T4_2_3 * interactiveObj.multiplyBy2) + '&nbsp;=&nbsp;' + interactiveObj.L2_T4_2_2 / interactiveObj.L2_T4_2_3 + '<br/>' + interactiveObj.L2_T4_2_1 + '&nbsp;+&nbsp;' + (interactiveObj.L2_T4_2_2 / interactiveObj.L2_T4_2_3) + '&nbsp;=&nbsp;' + (interactiveObj.L2_T4_2_3 * interactiveObj.L2_T4_2_1 + interactiveObj.L2_T4_2_2) / interactiveObj.L2_T4_2_3 + '</div><button id="L2_B1" class=buttonPrompt style="position: absolute;left: 72px;top:97px;" onclick="interactiveObj.L2_loadQuestions();">'+promptArr['txt_7']+'</button></div>';
            html2 += '<div id="wellDone_LF" class="correct"  style="top: 119px;"><div class="sparkie"></div><div class="textCorrect_WellDone">'+replaceDynamicText(promptArr['txt_4'],interactiveObj.numberLanguage,"interactiveObj")+'</div><br/><button class="buttonPrompt" onclick=interactiveObj.L2_loadQuestions();>' + replaceDynamicText(promptArr['txt_7'], interactiveObj.numberLanguage, "interactiveObj") + '</button></div>';
            // all the prompts go in here

            $("#L2_popOut").html(html2);
            $(".correct").draggable({containment: "#container"});



                interactiveObj.L2_attempt_Q6 += 1;

                if (interactiveObj.L2_attempt_Q6 == 1)
                {
                    interactiveObj.answer = parseFloat($("#L2_Q4_6_1").val());
                    interactiveObj.correct_Answer4 = (parseInt(interactiveObj.L2_T4_2_3) * parseInt(interactiveObj.L2_T4_2_1) + parseInt(interactiveObj.L2_T4_2_2)) / parseInt(interactiveObj.L2_T4_2_3);

                    if (interactiveObj.answer != interactiveObj.correct_Answer4)
                    {
                        interactiveObj.L2_4_incorrect_6_1 = 1;
                        $("#L2_Incorrect").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                        extraParameterArr[1]+="(Q6:"+interactiveObj.correct_Answer4+":"+interactiveObj.answer+"~";
                    }
                    else
                    {
                        //code for correct part
                        interactiveObj.L2_4_correct_6_1 = 1;
                        interactiveObj.L2_ScoreType4+=10;
                              interactiveObj.L2_Score+=interactiveObj.L2_ScoreType4;
                             levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                             interactiveObj.correctCounter2+=1;
                                interactiveObj.L2_FinalResult();
                                   extraParameterArr[1]+="(Q6:"+interactiveObj.correct_Answer4+":"+interactiveObj.answer+")";
                       // interactiveObj.Additoanl_Ques4=1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                       // interactiveObj.L2_getResult();
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 2)
                {
                    interactiveObj.answer = parseFloat($("#L2_Q4_6_1").val());
                    interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.L2_T4_2_1);
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                    if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
                    {
                        interactiveObj.L2_4_incorrect_6_2 = 1;
                        $("#L2_Incorrect4_2").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);

                    }
                    else
                    {
                        //code for correct answer
                        interactiveObj.L2_4_correct_6_2 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);

                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 3)
                {
                    interactiveObj.answer = parseFloat($("#L2_Q4_6_2").val());
                    interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.multiplyBy2);
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                    if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
                    {

                        interactiveObj.L2_4_incorrect_6_3 = 1;
                        $("#L2_Incorrect4_3").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    else
                    {
                        //code for correct asnwer
                        interactiveObj.L2_4_correct_6_3 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 4)
                {
                    interactiveObj.answer = parseInt($("#L2_Q4_6_3").val());
                    interactiveObj.correctAnswer_Ques4 = parseInt(interactiveObj.L2_T4_2_2 * interactiveObj.multiplyBy2);
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                    if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
                    {

                        interactiveObj.L2_4_incorrect_6_4 = 1;
                        $("#L2_Incorrect4_4").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                    else
                    {
                        interactiveObj.L2_4_correct_6_4 = 1;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 5)
                {
                    interactiveObj.answer = parseFloat($("#L2_Q4_6_4").val());
                    interactiveObj.correctAnswer_Ques4 = (parseInt(interactiveObj.L2_T4_2_3) * parseInt(interactiveObj.L2_T4_2_1) + parseInt(interactiveObj.L2_T4_2_2)) / parseInt(interactiveObj.L2_T4_2_3);
                    extraParameterArr[1]+=interactiveObj.answer+"~";
                    if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
                    {
                        interactiveObj.L2_4_incorrect_6_5 = 1;
                        $("#L2_Incorrect4_5").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                         extraParameterArr[1]+=interactiveObj.answer+"~";
                    }
                    else
                    {
                        interactiveObj.L2_ScoreType4+=5;
                        interactiveObj.L2_4_correct_6_5 = 1;
                        interactiveObj.correctCounter2+=1;
                        interactiveObj.L2_Score+=interactiveObj.L2_ScoreType4;
                        levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                         extraParameterArr[1]+=interactiveObj.answer+")";
                        interactiveObj.L2_FinalResult();
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                    }
                }
                if (interactiveObj.L2_attempt_Q6 == 6)
                {
                    interactiveObj.answer = parseFloat($("#L2_Q4_6_4").val());
                    interactiveObj.correctAnswer_Ques4 = (parseInt(interactiveObj.L2_T4_2_3) * parseInt(interactiveObj.L2_T4_2_1) + parseInt(interactiveObj.L2_T4_2_2)) / parseInt(interactiveObj.L2_T4_2_3);
                    extraParameterArr[1]+=interactiveObj.answer+")";
                    if (interactiveObj.answer != interactiveObj.correctAnswer_Ques4)
                    {
                        interactiveObj.L2_4_incorrect_6_6 = 1;
                        interactiveObj.L2_FinalResult();
                        $("#L2_Incorrect4_6").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                       // interactiveObj.L2_getResult();
                    }
                    else
                    {
                        interactiveObj.L2_4_correct_6_6 = 1;
                        interactiveObj.L2_ScoreType4+=5;

                         interactiveObj.L2_Score+=interactiveObj.L2_ScoreType4;
                          levelWiseScoreArr[Ltype-1]=parseInt(interactiveObj.L2_Score);
                          interactiveObj.correctCounter2+=1;
                           interactiveObj.L2_FinalResult();
                        // interactiveObj.Additoanl_Ques4=0;
                        $("#wellDone_LF").css('visibility', 'visible');
                        $(".buttonPrompt").focus();
                        $(".Input_Box").attr('disabled', true);
                       // interactiveObj.L2_getResult();
                    }
                }
        }//closure for type 3
    }
}
questionInteractive.prototype.L2_animation = function()
{

    if (interactiveObj.L2_QType == "one")
     {
        interactiveObj.displayAnswer=interactiveObj.L2_T1_2 / interactiveObj.L2_T1_3;
     }
    if (interactiveObj.L2_QType == "two")
     {
         interactiveObj.displayAnswer=interactiveObj.L2_T2_2 / interactiveObj.L2_T2_3;
     }

    if (interactiveObj.L2_QType == "five")
     {
        if(interactiveObj.L2_AdditonalQues5==1)
        {
            interactiveObj.displayAnswer=interactiveObj.L2_T1_2_2 / interactiveObj.L2_T1_2_3;
        }
        if(interactiveObj.L2_AdditonalQues5==2)
        {
            interactiveObj.displayAnswer=interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3;
        }
         
     }

    if (interactiveObj.L2_QType == "six")
    {
        if(interactiveObj.L2_AdditonalQues6==2)
        {
            interactiveObj.displayAnswer=interactiveObj.L2_T2_2_2 / interactiveObj.L2_T2_2_3;
        }
    }

        html3 = '<table>';
        html3 += '<tr>';
        html3 += '<th>' + replaceDynamicText(promptArr['txt_23'],interactiveObj.numberLanguage,"interactiveObj") + '</th>';
        html3 += '<th> ' + replaceDynamicText(promptArr['txt_24'],interactiveObj.numberLanguage,"interactiveObj") + ' </th>';
        html3 += '<th> . </th>';
        html3 += '<th> ' + replaceDynamicText(promptArr['txt_25'],interactiveObj.numberLanguage,"interactiveObj") + ' </th>';
        html3 += '<th> ' + replaceDynamicText(promptArr['txt_26'],interactiveObj.numberLanguage,"interactiveObj") + '</th>';
        html3 += '</tr>';
        html3 += '<tr style="height:38px;">';
        html3 += '<td>  </td>';
        html3 += '<td id="zero">  </td>';   // zero
        html3 += '<td id="dot">  </td>';    // dot
        html3 += '<td id="expNo1"> </td>';  // expNo1
        html3 += '<td id="expNo2"> </td>';  // expNo2
        html3 += '</tr>';
        html3 += '</table>';
        html3 += '<div id="arrow"><img src="../assets/arrowDown.png" style="width: 13px;height: 13px;"></img></div>';

        html3 += '<div id="finalExp">' + replaceDynamicText(promptArr['txt_28'], interactiveObj.numberLanguage, "interactiveObj") + '&nbsp;= &nbsp;' +replaceDynamicText(interactiveObj.displayAnswer,interactiveObj.numberLanguage,"interactiveObj")+ '</div>';


        $("#L2_animation_table").append(html3);
        interactiveObj.L2_animation2();
}
questionInteractive.prototype.L2_animation2 = function()
{


    $('#arrow').css('visibility', 'hidden');
    $('#zero').text('');
    $('#dot').text('');
    $('#expNo1').text('');
    $('#expNo2').text('');
   

    $('#arrow').css('left', '75px');

    setTimeout(function() {
        $('#arrow').css('visibility', 'visible')
    }, 700);
    setTimeout(function() {
        $('#zero').text('0')
    }, 1400);
    setTimeout(function() {
        $('#arrow').css('visibility', 'hidden')
    }, 1600);
    setTimeout(function() {
        $('#arrow').css('visibility', 'visible')
    }, 1900);
    setTimeout(function() {
        $('#arrow').css('visibility', 'hidden')
    }, 2200);

    setTimeout(function() {
        $('#dot').text('')
    }, 2500);
    setTimeout(function() {
        $('#dot').text('.')
    }, 2800);
    setTimeout(function() {
        $('#dot').text('')
    }, 3100);
    setTimeout(function() {
        $('#dot').text('.')
    }, 3400);

   if (interactiveObj.L2_QType == "one")
   {
        interactiveObj.tempAnswer=parseInt(interactiveObj.L2_T1_2)/parseInt(interactiveObj.L2_T1_3);

        if (interactiveObj.tempAnswer.toString().length==3)
        {
             t1 = interactiveObj.tempAnswer.toString().charAt(2);
             t2 = '';
        }
        else if (interactiveObj.tempAnswer.toString().length==4)
        {
             t1 = interactiveObj.tempAnswer.toString().charAt(2);
             t2 = interactiveObj.tempAnswer.toString().charAt(3);
        }
   
   }
   if (interactiveObj.L2_QType == "two")
   {
        interactiveObj.tempAnswer=parseInt(interactiveObj.L2_T2_2)/parseInt(interactiveObj.L2_T2_3);

        if (interactiveObj.tempAnswer.toString().length==3)
        {
             t1 = interactiveObj.tempAnswer.toString().charAt(2);
             t2 = ''
        }
        else if (interactiveObj.tempAnswer.toString().length==4)
        {
             t1 = interactiveObj.tempAnswer.toString().charAt(2);
             t2 = interactiveObj.tempAnswer.toString().charAt(3);
        }
   }
   if(interactiveObj.L2_QType == "five")
   {
       if (interactiveObj.L2_AdditonalQues5==1)
       {
                interactiveObj.tempAnswer=parseInt(interactiveObj.L2_T1_2_2)/parseInt(interactiveObj.L2_T1_2_3);

                if (interactiveObj.tempAnswer.toString().length==3)
                {
                     t1 = interactiveObj.tempAnswer.toString().charAt(2);
                     t2 = '';
                }
                else if (interactiveObj.tempAnswer.toString().length==4)
                {
                     t1 = interactiveObj.tempAnswer.toString().charAt(2);
                     t2 = interactiveObj.tempAnswer.toString().charAt(3);
                }
       }
       if (interactiveObj.L2_AdditonalQues5==2)
       {
                interactiveObj.tempAnswer=parseInt(interactiveObj.L2_T2_2_2)/parseInt(interactiveObj.L2_T2_2_3);

                if (interactiveObj.tempAnswer.toString().length==3)
                {
                     t1 = interactiveObj.tempAnswer.toString().charAt(2);
                     t2 = ''
                }
                else if (interactiveObj.tempAnswer.toString().length==4)
                {
                     t1 = interactiveObj.tempAnswer.toString().charAt(2);
                     t2 = interactiveObj.tempAnswer.toString().charAt(3);
                }
       }
   }
   if(interactiveObj.L2_QType == "six")
   {
        if(interactiveObj.L2_AdditonalQues6==2)
        {
            interactiveObj.tempAnswer=parseInt(interactiveObj.L2_T2_2_2)/parseInt(interactiveObj.L2_T2_2_3);

                if (interactiveObj.tempAnswer.toString().length==3)
                {
                     t1 = interactiveObj.tempAnswer.toString().charAt(2);
                     t2 = ''
                }
                else if (interactiveObj.tempAnswer.toString().length==4)
                {
                     t1 = interactiveObj.tempAnswer.toString().charAt(2);
                     t2 = interactiveObj.tempAnswer.toString().charAt(3);
                }
        }    
   }
    

    setTimeout(function() {
        $('#arrow').css('left', '177px')
    }, 3500);
    setTimeout(function() {
        $('#arrow').css('visibility', 'visible')
    }, 3550);

    setTimeout(function() {
        $('#expNo1').text(replaceDynamicText(t1 + "", interactiveObj.numberLanguage, 'interactiveObj'))
    }, 4000);
    setTimeout(function() {
        $('#arrow').css('visibility', 'hidden')
    }, 4300);
    setTimeout(function() {
        $('#arrow').css('visibility', 'visible')
    }, 4600);
   
     setTimeout(function() {
        $('#arrow').css('visibility', 'hidden')
    }, 4900);    



    if ((interactiveObj.tempAnswer.toString()).length == 4)
    {
        setTimeout(function() {
            $('#arrow').css('left', '258px')
        }, 5000);
        setTimeout(function() {
            $('#arrow').css('visibility', 'visible')
        }, 5300);
        setTimeout(function() {
            $('#expNo2').text(replaceDynamicText(t2 + "", interactiveObj.numberLanguage, 'interactiveObj'))
        }, 5500);
        setTimeout(function() {
            $('#arrow').css('visibility', 'hidden')
        }, 5700);
        setTimeout(function() {
            $('#arrow').css('visibility', 'visible')
        }, 6000);
        setTimeout(function() {
            $('#arrow').css('visibility', 'hidden')
        }, 6300);
    }
    setTimeout(function() {
        $('#finalExp').css({'visibility': 'visible'});
    }, (6800));
    //setTimeout(function(){$('#expDivButton').focus();},(6700));
 }
questionInteractive.prototype.L2_getResult=function()
{
    L2_AQ[0]=interactiveObj.Additoanl_Ques1;
    L2_AQ[1]=interactiveObj.Additoanl_Ques2;
    L2_AQ[2]=interactiveObj.Additoanl_Ques3;
    L2_AQ[3]=interactiveObj.Additoanl_Ques4;
    a=0;

    ////console.log("Final Status="+ L2_AQ);

    for(interactiveObj.i=0;interactiveObj.i<4;interactiveObj.i++)
    {
        if(L2_AQ[interactiveObj.i]==0)
        {
            interactiveObj.AdditionalQues+= 1;
            L2_Ques[a] = parseInt(interactiveObj.i+1);
            a++;
        }
    }

    interactiveObj.L2_AdditonalQues5=L2_Ques[0];
    interactiveObj.L2_AdditonalQues6=L2_Ques[1];

    if(interactiveObj.AdditionalQues == 0 || interactiveObj.AdditionalQues == 1 )
    {
        completed=1;
        
        levelWiseStatus=interactiveObj.tempLevelStatus+"|"+1;
        interactiveObj.L2_Q5_visible=0;
        interactiveObj.tempArr="---->Type Wise Score:("+interactiveObj.L2_ScoreType1+","+interactiveObj.L2_ScoreType2+","+interactiveObj.L2_ScoreType3+","+interactiveObj.L2_ScoreType4+")";
        extraParameterArr[1]+=interactiveObj.tempArr;
       // window.clearInterval(tempController);

       window.setTimeout(function(){window.clearInterval(tempController)},1000);

       EndPage=setTimeout("interactiveObj.EndPage();",3000);
    }

    if(interactiveObj.AdditionalQues>1)
    {
        if(interactiveObj.L2_AdditonalQues5==1)
        {
            interactiveObj.L2_A_RNG_T1();
            interactiveObj.L2_called1=1;
            interactiveObj.L2_1=interactiveObj.L2_T1_2_1;
            interactiveObj.L2_2=interactiveObj.L2_T1_2_2;
            interactiveObj.L2_3=interactiveObj.L2_T1_2_3;
             interactiveObj.L2_Q5_visible=1;
        }
        if(interactiveObj.L2_AdditonalQues5==2)
        {
            interactiveObj.L2_A_RNG_T2();
            interactiveObj.L2_called2=1;
            interactiveObj.L2_1=interactiveObj.L2_T2_2_1;
            interactiveObj.L2_2=interactiveObj.L2_T2_2_2;
            interactiveObj.L2_3=interactiveObj.L2_T2_2_3;
             interactiveObj.L2_Q5_visible=1;
        }
        if(interactiveObj.L2_AdditonalQues5==3)
        {
            interactiveObj.L2_A_RNG_T3();
            interactiveObj.L2_called3=1;
            interactiveObj.L2_1=interactiveObj.L2_T3_2_1;
            interactiveObj.L2_2=interactiveObj.L2_T3_2_2;
            interactiveObj.L2_3=interactiveObj.L2_T3_2_3;
             interactiveObj.L2_Q5_visible=1;
        }
 
  
        if(interactiveObj.L2_AdditonalQues6==2)
        {
            interactiveObj.L2_A_RNG_T2();
            interactiveObj.L2_called22=1;
        }
        if(interactiveObj.L2_AdditonalQues6==3)
        {
            interactiveObj.L2_A_RNG_T3();
            interactiveObj.L2_called23=1;
        }
        if(interactiveObj.L2_AdditonalQues6==4)
        {
            interactiveObj.L2_A_RNG_T4();
            interactiveObj.L2_called24=1;
        }
    }
}
questionInteractive.prototype.L2_FinalResult=function()
{
  
    if(interactiveObj.correctCounter2>=4)
    {
       
        completed=1;
        levelAttempted="L1|L2";
        levelWiseStatus=interactiveObj.tempLevelStatus+"|"+1;
        interactiveObj.tempArr="---->Type Wise Score:("+interactiveObj.L2_ScoreType1+","+interactiveObj.L2_ScoreType2+","+interactiveObj.L2_ScoreType3+","+interactiveObj.L2_ScoreType4+")";
        extraParameterArr[1]+=interactiveObj.tempArr;
          window.setTimeout(function(){window.clearInterval(tempController)},1000);
          EndPage=setTimeout("interactiveObj.EndPage();",6000);
    }
    if(interactiveObj.correctCounter2<4)
    {
      
        completed=1;
        levelAttempted="L1|L2";
        levelWiseStatus=interactiveObj.tempLevelStatus+"|"+2;
       interactiveObj.tempArr="---->Type Wise Score:("+interactiveObj.L2_ScoreType1+","+interactiveObj.L2_ScoreType2+","+interactiveObj.L2_ScoreType3+","+interactiveObj.L2_ScoreType4+")";
        extraParameterArr[1]+=interactiveObj.tempArr;
          window.setTimeout(function(){window.clearInterval(tempController)},1000);
          EndPage=setTimeout("interactiveObj.EndPage();",6000);
    }
}
questionInteractive.prototype.EndPage=function()
{
    html='';
    html2='';
    $("#container").html(html);


    html='<div id="EndPage"><div><img src="../assets/sparkie2.gif"/></div><div id="endText">'+replaceDynamicText(promptArr['txt_44'],interactiveObj.numberLanguage,"interactiveObj")+'</div></div>';
    $("#container").html(html);

    $("#EndPage").delay(500).animate({'opacity':'1'},1000);
}
