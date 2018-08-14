var gameObj;
var animationObj;
var lastFocus='';
var aCorrectFlag=0;
var answerFlag="";
var mainQuestionCounter=0;
var parameterMissing=false;
var posPromptFlag=0;


var interval=0;
var extraParameters='';
var levelWiseTimeTaken=0;
var levelWiseStatus=0;
var levelWiseScore=0;
var completed=0;
var levelsAttempted='';

var passing=0;
var responseArr=["","","","","",""];
var ansArray=["","","","",""];
var attemptBlank1=0;
var correctBlank1=0;

var attemptBlank2=0;
var correctBlank2=0;

var attemptBlank3=0;
var correctBlank3=0;

var attemptBlank4=0;
var correctBlank4=0;

var attemptBlank5=0;
var correctBlank5=0;

var attemptBlank6=0;
var correctBlank6=0;

var correctFlag=0;
var almostCorrect=0;
var filled=false;
var divideFlag=0;
var setBlank='';
var blankFlag=0;
var questionCounter=4;

interval=setInterval(function(){
	levelWiseTimeTaken++;
	extraParameters='';
	for(var i=0;i<5;i++)
	{
		if(ansArray[i]!="")
			extraParameters+=ansArray[i]+'|';
	}
		
	
},1000);

$(document).ready(function(){
	$("input").live('keypress',function(e)
	{
		
		
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..

		var blank=$(this).attr('id');

		/* Check the length of the value */ 
		
		if($("#"+blank).val().length>4)
			e.preventDefault();

		/* Check the input Elements  */

		if($(this).attr("id")=="blank4" || $(this).attr("id")=="blank5" || $(this).attr("id")=="blank6")
		{
			if($(this).val().indexOf('/')!=-1 && e.keyCode==47)
			{
				e.preventDefault();
			}	

			if((e.keyCode < 48 || e.keyCode > 57) && e.keyCode!=47 && e.keyCode != 9 && e.keyCode != 8) 
				e.preventDefault();
		}
		else
		{
			if((e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
				e.preventDefault();
		}
		
		   
			
	//});
	//$("input").live('keyup',function(e)
	//{
		var inputId=$(this).attr('id');
		var value=$(this).val();

		var index=parseInt(inputId.substr(inputId.length-1,inputId.length))-1;
		if(e.keyCode==13)
		{
			if($(this).val()=="")
			{
				lastFocus=inputId;
				$(this).val("");
				blankFlag=1;				
				loadPromptData("blank");
			}
			else
			{

		
				blankFlag=0;
				if(inputId=="blank1")
				{
					value=parseInt(value);
					responseArr[0]=value;
					
					if(value>20)
					{
						setBlank=inputId;
						loadPromptData("prompt7");
						disableAll();
						lastFocus="blank1";
					}
					else
					{

						animationObj.divideChocolate(value);
						gameObj.answerArray[index]=value;
						disableAll();
						enable('blank2');
					}
				}
				else if(inputId=="blank2")
				{
					value=parseInt(value);
					if(value>gameObj.answerArray[0] && aCorrectFlag==0)
					{
						blankFlag=1;
						setBlank=inputId;
						loadPromptData("prompt9");
						disableAll();
						lastFocus="blank2";
					}
					else
					{
						responseArr[1]=value;
						if(aCorrectFlag==0)
						{
							animationObj.fadePart(value,"#DBAF95");
							gameObj.answerArray[index]=value;
							disableAll();
							enable("blank3");	
						}
						else
						{
							attemptBlank2++;
							gameObj.totalFraction=gameObj.johnFraction;
							gameObj.answerArray[index]=value;
							if(value==gameObj.actualAnswerArray[1])
							{	
								correctBlank2=1;
								loadPromptData("prompt17");
								lastFocus="blank3";
								$("#markerJohn").css('opacity','0');
								animationObj.fadePart(gameObj.actualAnswerArray[1],"#DBAF95");
								$("#blank2").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[1]+'</div>');
							}
							else
							{
								$(this).addClass('wrong');
								if(attemptBlank2==1)
								{
									loadPromptData("prompt4");
									lastFocus="blank2";
									// Mark the parts by red
									animationObj.john_marker();

								}
								else
								{
									$("#markerJohn").css('opacity','0');
									loadPromptData("prompt5");
									lastFocus="blank3";
									animationObj.lastFadedPart=0;
									animationObj.fadePart(gameObj.actualAnswerArray[1],"#DBAF95");
								}
							}
						}
						
					}
				}
				else if(inputId=="blank3")
				{
					value=parseInt(value);
					if(value>gameObj.answerArray[0]  && aCorrectFlag==0)
					{
						blankFlag=1;
						setBlank=inputId;	
						loadPromptData("prompt9");
						disableAll();
						lastFocus="blank3";
					}
					else
					{
						responseArr[2]=value;
						if(aCorrectFlag==0)
						{
							animationObj.fadePart(value,"#FFECEC");
							gameObj.answerArray[index]=value;
							gameObj.checkFirstThree();
							gameObj.answerArray[index]=value;
							
						}
						else
						{
							attemptBlank3++;
							gameObj.totalFraction=gameObj.pamFraction;
							gameObj.answerArray[index]=value;
							
							if(value==gameObj.actualAnswerArray[2])
							{	
								$("#markerPam").css('opacity','0');
								animationObj.fadePart(gameObj.actualAnswerArray[2],"#FFECEC");
								loadPromptData("prompt17");
								correctBlank3=1;
								$("#next").show();
								lastFocus="blank4";
								$("#blank3").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[2]+'</div>');
							}
							else
							{
								$(this).addClass('wrong');
								if(attemptBlank3==1)
								{
									loadPromptData("prompt4");
									lastFocus="blank3";
									// Mark the parts by red
									animationObj.pam_marker();
								}
								else
								{
									$("#markerPam").css('opacity','0');
									animationObj.fadePart(gameObj.actualAnswerArray[2],"#FFECEC");
									loadPromptData("prompt6");
									lastFocus="blank4";
								}
							}
						}
						
					}
				}
				else if(inputId=="blank4")
				{
					value=value.toString();
					
					if(value.indexOf('/')!=-1)
					{
						var checkVal=value.split('/');
						if(checkVal[1]==0)
						{
							loadPromptData("denominator");
							lastFocus="blank4";
						}
						else
						{
							responseArr[3]=value;
							gameObj.reducedPart='<div class="fraction"><div class="frac numerator">'+gameObj.jNum+'</div><div class="frac">'+gameObj.jDen+'</div></div>';
							gameObj.correctPart='<div class="fraction"><div class="frac numerator">'+checkVal[0]+'</div><div class="frac">'+checkVal[1]+'</div></div>';
							value='<div class="fraction"><div class="frac numerator">'+gameObj.jNum+'</div><div class="frac">'+gameObj.jDen+'</div></div>';
							this.correctPart='';
							this.reducedPart='';
							attemptBlank4++;
							gameObj.answerArray[index]=value;
							if(parseFloat(checkVal[0]/checkVal[1])==parseFloat(gameObj.jNum/gameObj.jDen))
							{

								if(checkVal[0]==gameObj.jNum && checkVal[1]==gameObj.jDen)
								{
									loadPromptData("prompt17");
								}
								else
								{
									loadPromptData("prompt21");
								}
								correctBlank4=1;
								lastFocus="blank5";
								$("#blank4").replaceWith('<div class="answer correct">'+value+'</div>');
							}
							else
							{
								$(this).addClass('wrong');
								if(attemptBlank4==1)
								{
									loadPromptData("prompt12");
									lastFocus="blank4";
									// Mark the parts by red
								}
								else
								{
									loadPromptData("prompt13");
									lastFocus="blank4";
								}
								
							}
						}
					}
					else
					{
						loadPromptData("prompt11");
						lastFocus="blank4";
					}
				}
				else if(inputId=="blank5")
				{
					value=value.toString();
					
					if(value.indexOf('/')!=-1)
					{
						var checkVal=value.split('/');
						if(checkVal[1]==0)
						{
							loadPromptData("denominator");
							lastFocus="blank5";
						}
						else
						{
							responseArr[4]=value;
							gameObj.reducedPart='<div class="fraction"><div class="frac numerator">'+gameObj.pNum+'</div><div class="frac">'+gameObj.pDen+'</div></div>';
							gameObj.correctPart='<div class="fraction"><div class="frac numerator">'+checkVal[0]+'</div><div class="frac">'+checkVal[1]+'</div></div>';
							value='<div class="fraction"><div class="frac numerator">'+gameObj.pNum+'</div><div class="frac">'+gameObj.pDen+'</div></div>';
							gameObj.answerArray[index]=value;
							attemptBlank5++;
							if(parseFloat(checkVal[0]/checkVal[1])==parseFloat(gameObj.pNum/gameObj.pDen))
							{
								if(checkVal[0]==gameObj.pNum && checkVal[1]==gameObj.pDen)
								{
									loadPromptData("prompt17");
								}
								else
								{
									loadPromptData("prompt21");
								}
								correctBlank5=1;
								lastFocus="blank6";
								$("#blank5").replaceWith('<div class="answer correct">'+value+'</div>');
							}
							else
							{
								$(this).addClass('wrong');
								loadPromptData("prompt14");
								lastFocus="blank5";
							}
						}
					}
					else
					{
						loadPromptData("prompt11");
						lastFocus="blank5";
					}
				}	
				else if(inputId=="blank6")
				{
					value=value.toString();
					
					if(value.indexOf('/')!=-1)
					{
						var checkVal=value.split('/');
						if(checkVal[1]==0)
						{
							loadPromptData("denominator");
							lastFocus="blank6";
						}
						else
						{
							responseArr[5]=value;
							gameObj.reducedPart='<div class="fraction"><div class="frac numerator">'+gameObj.jNum+'</div><div class="frac">'+gameObj.jDen+'</div></div>';
							gameObj.correctPart='<div class="fraction"><div class="frac numerator">'+checkVal[0]+'</div><div class="frac">'+checkVal[1]+'</div></div>';
							value='<div class="fraction"><div class="frac numerator">'+gameObj.jNum+'</div><div class="frac">'+gameObj.jDen+'</div></div>';
							attemptBlank6++;
							gameObj.answerArray[index]=value;
							if(parseFloat(checkVal[0]/checkVal[1])==parseFloat(gameObj.jNum/gameObj.jDen))
							{
								if(checkVal[0]==gameObj.jNum && checkVal[1]==gameObj.jDen)
								{
									loadPromptData("prompt17");
								}
								else
								{
									loadPromptData("prompt21");
								}
								correctBlank6=1;
								$("#blank6").replaceWith('<div class="answer correct">'+value+'</div>');
							}
							else
							{
								$(this).addClass('wrong');
								if(attemptBlank6==1)
								{
									loadPromptData("prompt15");
									lastFocus="blank6";
								}
								else
								{
									loadPromptData("prompt16");
									lastFocus="blank6";
								}
								
							}
						}
					}
					else
					{
						loadPromptData("prompt11");
						lastFocus="blank6";
					}
				}	
					
			}
			ansArray[mainQuestionCounter-1]=responseArr[0]+','+responseArr[1]+','+responseArr[2]+','+responseArr[3]+','+responseArr[4]+','+responseArr[5];	
			
			
		}
	});
	$("#Ok").live('click',function(){
		if(blankFlag!=1)
		{
			if(divideFlag==0)
			{
				$("#chocolate").html("");
			}
		}
		closePrompt();
		setTimeout(function(){
			enable(lastFocus);

			},200);

	});
	$("#cancel").live('click',function(){
		setTimeout(function(){
			enable(lastFocus);
			},200);
		closePrompt();
	});
	$("#next").live('click',function(){
		posPromptFlag=1;
		gameObj.loadQuestion(4);
		$("#text2").css({'opacity':'0'});
		$("#next").hide();
		setTimeout(function(){
			enable("blank4");
			},200);
		
	});
	$("#nextQuestion").live('click',function(){
		extraParameters+='|';
		reset();
			gameObj.init();
	});
});
function reset()
{
	lastFocus='blank1';
	aCorrectFlag=0;
	answerFlag="";
	posPromptFlag=0;

	passing=0;
	responseArr=["","","","","",""];
	
	attemptBlank1=0;
	correctBlank1=0;

	attemptBlank2=0;
	correctBlank2=0;

	attemptBlank3=0;
	correctBlank3=0;

	attemptBlank4=0;
	correctBlank4=0;

	attemptBlank5=0;
	correctBlank5=0;

	attemptBlank6=0;
	correctBlank6=0;

	correctFlag=0;
	almostCorrect=0;
	filled=false;
	divideFlag=0;
	setBlank='';
	blankFlag=0;
	questionCounter=4;


}
function gameInteractive()
{
	if(typeof getParameters['numberLanguage']=="undefined")
	{
		this.numberLanguage="english";
	}
	else
	{
		this.numberLanguage=getParameters['numberLanguage'];
	}
	if(typeof getParameters['language']=="undefined")
	{
		this.language="english";
	}
	else
	{
		this.language=getParameters['language'];
	}
	if(typeof getParameters['noOfLevels']=="undefined")
	{
		$("#container").html("Pass argument noOfLevels");
		parameterMissing=true;
		return;
	}
	else
	{
		this.noOfLevels=getParameters['noOfLevels'];
		if(this.noOfLevels!="1")
		{
			$("#container").html("Please pass the argument noOfLevels correctly");
			parameterMissing=true;
			return;	

		}

	}
	if(typeof getParameters['lastLevelCleared']=="undefined")
	{
		$("#container").html("Pass argument lastLevelCleared");
		parameterMissing=true;
		return;
	}
	else
	{
		this.lastLevelCleared=parseInt(getParameters['lastLevelCleared']);
		if(this.lastLevelCleared>1)
		{
			$("#container").html("Please pass the argument lastLevelCleared correctly");
			parameterMissing=true;
			return;
		}
		else
		{
			levelsAttempted="L1";
		}
	}
	this.answerArray=[];
	this.actualAnswerArray=[];
	this.totalFraction='';
	this.fractionNum1=0;
	this.fractionDen1=0;
	this.fractionNum2=0;
	this.fractionDen2=0;
	this.totalParts=0;
	this.combinePart=0;
	this.dividebBar=0;
	this.fractionParts=0;
	this.type=1;
	
}
gameInteractive.prototype.init = function() 
{
	// Question Type
	if(this.type==1) 
		this.initializeFractions(1);
	else
		this.initializeFractions(2);

	this.jNum=0;
	this.jDen=0;
	this.pNum=0;
	this.pDen=0;
	this.correctPart='';
	this.reducedPart='';
	// Variables used in XML

	this.johnFraction='<div class="fraction"><div class="frac numerator">'+this.fractionNum1+'</div><div class="frac">'+this.fractionDen1+'</div></div>';
	this.pamFraction='<div class="fraction"><div class="frac numerator">'+this.fractionNum2+'</div><div class="frac">'+this.fractionDen2+'</div></div>';
	this.totalParts=lcm(this.fractionDen1,this.fractionDen2);
	this.johnPart=parseInt((this.totalParts*this.fractionNum1)/this.fractionDen1);
	this.pamPart=parseInt((this.totalParts*this.fractionNum2)/this.fractionDen2);
	if(this.johnPart<2)
		this.jPieceText=replaceDynamicText(miscArr["piece"],gameObj.numberLanguage,'gameObj');
	else
		this.jPieceText=replaceDynamicText(miscArr["pieces"],gameObj.numberLanguage,'gameObj');

	if(this.pamPart<2)
		this.pPieceText=replaceDynamicText(miscArr["piece"],gameObj.numberLanguage,'gameObj');
	else
		this.pPieceText=replaceDynamicText(miscArr["pieces"],gameObj.numberLanguage,'gameObj');

	this.combinePart=this.johnPart+this.pamPart;
	this.leftOutPart=this.totalParts-this.combinePart;
	this.combineFraction='<div class="fraction"><div class="frac numerator">'+this.combinePart+'</div><div class="frac">'+this.totalParts+'</div></div>';
	this.leftOutFraction='<div class="fraction"><div class="frac numerator">'+(this.totalParts-this.combinePart)+'</div><div class="frac">'+this.totalParts+'</div></div>';
	this.combineShortForm=reduce(this.combinePart,this.totalParts);
	joined(this.combinePart,this.totalParts);
	this.leftOutShortForm=reduce((this.totalParts-this.combinePart),this.totalParts);
	leftOut((this.totalParts-this.combinePart),this.totalParts);


	this.actualAnswerArray[0]=this.totalParts;
	this.actualAnswerArray[1]=this.johnPart;
	this.actualAnswerArray[2]=this.pamPart;
	this.actualAnswerArray[3]=this.combineFraction;
	this.actualAnswerArray[4]=this.leftOutFraction;
	this.actualAnswerArray[5]=this.combineFraction;
	///////////////////////////
	/// Counts the number of main Questions
	mainQuestionCounter++;

	/////////////////////////////
	var html='';
		html+='<img src="../assets/bg3.png" style="position:absolute;left:388px"/>'
		html+='<div id="questionPanel">';
			html+='<div id="questionContainer">';
				html+=this.getMainQuestion();
			html+='</div>';
			html+='<button id="next">'+replaceDynamicText(miscArr['next'],gameObj.numberLanguage,'gameObj')+'</button>';
		html+='</div>';

		html+='<div id="animationPanel">';
		html+='</div>';
		html+='<div id="prompt">';
			html+='<div id="barTop"></div>';
			html+='<div id="cancel">X</div>';
			html+='<div id="message"></div>';
			html+='<div id="promptButton"><button id="Ok">'+replaceDynamicText(miscArr['okButton'],this.numberLanguage,'gameObj')+'</button></div>';
		html+='</div>';
		html+='<button id="nextQuestion">'+replaceDynamicText(miscArr['nextQuestion'],gameObj.numberLanguage,'gameObj')+'</button>';
		html+='<div id="help">'+replaceDynamicText(miscArr['help'],this.numberLanguage,'gameObj')+'</div>';

	$("#container").html(html);
	$("#prompt").draggable({
		containment:"#container"
	});
	$("#next").hide();
	$("#nextQuestion").hide();
	$("#prompt").hide();
	setTimeout(function(){$("#mainQuestion1").css({'opacity':'1'});},1000);
	setTimeout(function(){$("#mainQuestion2").css({'opacity':'1'});},2000);
	setTimeout(function(){$("#mainQuestion3").css({'opacity':'1'});},3000);
	
	setTimeout(function(){
		gameObj.loadQuestion(1);
		gameObj.loadQuestion(2);
		gameObj.loadQuestion(3);
		disableAll();
		lastFocus="blank1";
		enable(lastFocus);
		animationObj=new animationStatic();
		
	},4000);
	/*this.loadQuestion(4);
	this.loadQuestion(5);
	this.loadQuestion(6);*/
	//animationObj.hintDiagram1(1);
	//animationObj.hintDiagram2();
	//animationObj.explainDivision();
	//animationObj.explainAlmostCorrect();
	//animationObj.explainCorrect();
}
gameInteractive.prototype.checkFirstThree=function()
{
	
	attemptBlank1++;
	animationObj.lastFadedPart=0;
	filled=true;
	disableAll();
	almostCorrect=0;
	var correct=0;
	var firstCorrect=0;
	if(attemptBlank1==1)
		divideFlag=1;
	else
		divideFlag=0;
	var tempCal=this.answerArray[0]/this.actualAnswerArray[0]+'';
	
	for(var i=0;i<3;i++)
	{
		if(this.answerArray[i]==this.actualAnswerArray[i])
		{
			if(i==0)
				firstCorrect=1;
			correct++;
		}
		else
		{
			if(tempCal.indexOf('.')==-1)
			{
				if(this.answerArray[i]/this.actualAnswerArray[i]==parseInt(tempCal))
				{
						almostCorrect++;
				}
			}
		}
	}
	
	if(firstCorrect==1 && correct!=3)
	{
		
		divideFlag=1;
		$("#blank1").addClass('correct');
		$("#blank2").addClass('wrong');
		$("#blank3").addClass('wrong');
		aCorrectFlag=1;
			$("#animationPanel").html("");
			animationStatic();
			animationObj.divideChocolate(gameObj.actualAnswerArray[0]);
			animationObj.hintDiagram3();
			$("#hint1").css('top','175px');
		lastFocus="blank2";
		loadPromptData('prompt19');	
		correctBlank1=1;
	}
	else if(correct==3)
	{
		$("#blank1").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[0]+'</div>');
		$("#blank2").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[1]+'</div>');
		$("#blank3").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[2]+'</div>');
		loadPromptData('prompt17');
		correctBlank1=1;
		correctBlank2=1;
		correctBlank3=1;
	}
	else if(almostCorrect==3)
	{
		$("#blank1").addClass('correct');
		$("#blank2").addClass('correct');
		$("#blank3").addClass('correct');
		loadPromptData("prompt20");
		correctBlank1=1;
		correctBlank2=1;
		correctBlank3=1;
	}
	else
	{
		$("#blank1").addClass('wrong');
		$("#blank2").addClass('wrong');
		$("#blank3").addClass('wrong');
		if(attemptBlank1==1) 
		{
			loadPromptData("prompt1");
			animationObj.hintDiagram1();
		}	
		else
		{
			loadPromptData("prompt2");

		}
			
	}

	/*if(firstCorrect==1 && correct!=3)
	{
		$("#blank1").replaceWith('<div class="answer correct">'+$("#blank1").val()+'</div>');
		$("#blank2").addClass('wrong');
		$("#blank3").addClass('wrong');
		
		aCorrectFlag=1;
		lastFocus="blank2";
		loadPromptData('prompt19');	
	}
	else if(correct==3)
	{
		loadPromptData('prompt17');
		correctFlag=1;
	}
	else if(almostCorrect==3)
	{
		correctFlag=1;
		$("#blank1").addClass('correct');
		$("#blank2").addClass('correct');
		$("#blank3").addClass('correct');
		disableAll();
		loadPromptData('prompt20');
	}
	else if(attemptBlank1==1 && correct!=3 && almostCorrect!=3)
	{

		animationObj.hintDiagram1(0);
		$("#blank1").addClass('wrong');
		$("#blank2").addClass('wrong');
		$("#blank3").addClass('wrong');
		lastFocus="blank1";
		loadPromptData("prompt1");
	}
	if(attemptBlank1==2 && correct!=3 && almostCorrect!=3)
	{
		lastFocus="blank2";
		loadPromptData("prompt2");
		$("#blank1").addClass('wrong');
		$("#blank2").addClass('wrong');
		$("#blank3").addClass('wrong');
		
	}
	*/
}

gameInteractive.prototype.getMainQuestion=function()
{
	var data='';
		data+='<div id="mainQuestion1" style="opacity:0">'+replaceDynamicText(quesArr['mainQuestion1'],this.numberLanguage,'gameObj')+'</div>';
		data+='<div id="mainQuestion2" style="opacity:0">'+replaceDynamicText(quesArr['mainQuestion2'],this.numberLanguage,'gameObj')+'</div>';
		data+='<div id="mainQuestion3" style="opacity:0">'+replaceDynamicText(quesArr['mainQuestion3'],this.numberLanguage,'gameObj')+'</div>';
	return data;
	
}

gameInteractive.prototype.loadQuestion=function(questionNumber)
{
	var data='';
		data+='<div class="block">';
			data+='<div id="question'+questionNumber+'" class="question">'+replaceDynamicText(quesArr['question_'+questionNumber],this.numberLanguage,'gameObj')+'</div>';
			data+='<div class="box"><input type="text" id="blank'+questionNumber+'" class="textBox"></div>';
		data+='</div>';
	$("#questionContainer").append(data);
}

// Initialize fraction for Question type 1 and 2
gameInteractive.prototype.initializeFractions=function(type)
{
	var num1=0;
	var num2=0;
	var den1=0;
	var den2=0;
	if(type==1)
	{
		var numberArr=[2,3,4,5];
		temp = Math.round(Math.random()*(numberArr.length-1));
		// Initialize denominator
		den1=numberArr[temp];
		den2=den1*2;
		// Initialize numerator
		num1=Math.round(Math.random()*(den1-2))+1;
		while(gcd(num1,den1)!=1)
		{
			num1=Math.round(Math.random()*(den1-2))+1;
		}
		num2=Math.round(Math.random()*(den2-2))+1;
		while(gcd(num2,den2)!=1)
		{
			num2=Math.round(Math.random()*(den2-2))+1;
		}
		
		if(parseFloat(num1/den1)<parseFloat(num2/den2) || (parseFloat(num1/den1)+parseFloat(num2/den2))>1)
		{
			this.initializeFractions(1);
		}
		else
		{
			this.fractionNum1=num1;
			this.fractionNum2=num2;
			this.fractionDen1=den1;
			this.fractionDen2=den2;
			this.type=2;	
		}
		
	}
	else
	{
		var numberArr=["2,5","3,4","3,5"];
		temp = Math.round(Math.random()*(numberArr.length-1));
		// Initialize denominator
		var temp1=numberArr[temp].split(',');
		den1=parseInt(temp1[0]);
		den2=parseInt(temp1[1]);
		// Initialize numerator
		num1=Math.round(Math.random()*(den1-2))+1;
		while(gcd(num1,den1)!=1)
		{
			num1=Math.round(Math.random()*(den1-2))+1;
		}
		num2=Math.round(Math.random()*(den2-2))+1;
		while(gcd(num2,den2)!=1)
		{
			num2=Math.round(Math.random()*(den2-2))+1;
		}
		if((parseFloat(num1/den1)+parseFloat(num2/den2))>1)
		{
			this.initializeFractions(2);
		}
		else
		{
			this.fractionNum1=num1;
			this.fractionNum2=num2;
			this.fractionDen1=den1;
			this.fractionDen2=den2;
			this.type=1;		
		}
		
	}
}
/////////////////////////////////////////////////

function animationStatic()
{
	// Initialize all the variables for this class.
	this.lastFadedPart=0
	var data='';
		data+='<div id="chocolate" class="chocolate">';
		data+='</div>';
	$("#animationPanel").append(data);
}
animationStatic.prototype.hintDiagram1=function()
{
	var num1=gameObj.fractionNum1;
	var den1=gameObj.fractionDen1;
	var num2=gameObj.fractionNum2;
	var den2=gameObj.fractionDen2;
	var data='';
	data+='<div id="hint1">';
		data+='<div id="johnPart" style="position:relative;top:-25px;">'
		data+='<div id="chocolate1" class="chocolate">';
		data+='</div>';
		data+='</div>';
		data+='<div id="pamPart" style="position:relative;top:-25px;">';
		data+='<div id="chocolate2" class="chocolate"></div>';
		data+='</div>';
	data+='</div>';
	$("#animationPanel").append(data);
	
	// Chocolate Part of John
	var width=$("#chocolate1").width()/den1;
	var height=$("#chocolate1").height();
	data='';
	for(var i=0;i<den1;i++)
	{
		if(i<num1)
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate1").html(data);
	$("#johnPart").append('<div id="john" style="position:relative;top:30px;">'+replaceDynamicText(miscArr['john'],gameObj.numberLanguage,'gameObj')+'</div>');
	// Chocolate Part of Pam

	var width=$("#chocolate2").width()/den2;
	var height=$("#chocolate2").height();
	data='';
	for(var i=0;i<den2;i++)
	{
		if(i<num2)
		{
			data+='<div id="partpam'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#FFECEC"></div>';
		}
		else
		{
			data+='<div id="partpam'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate2").html(data);
	$("#pamPart").append('<div id="pam" style="position:relative;top:30px;">'+replaceDynamicText(miscArr['pam'],gameObj.numberLanguage,'gameObj')+'</div>');
	
}
animationStatic.prototype.hintDiagram2 = function()
{
	var data='';
	data+='<div id="hint2">';
		data+='<div id="chocolate3" class="chocolate">';
		data+='</div>';
		data+='<div id="marker"></div>';
		data+='<div id="text1" style="width:400px;text-align:center;margin-top:15px;">'+replaceDynamicText(miscArr['animation2_1'],gameObj.numberLanguage,'gameObj')+'</div>';
		
	data+'</div>';
	data+='<div id="text2" style="text-align:left;position:relative;line-height:1.8em;width:360px;border:2px solid #FF8204;padding:5px;left:15px;border-radius:12px;">'+replaceDynamicText(miscArr['animation2_2'],gameObj.numberLanguage,'gameObj')+'</div>';
	$("#animationPanel").append(data);
	data='';
	var total=gameObj.totalParts;
	var num1=gameObj.johnPart;
	var num2=gameObj.johnPart+gameObj.pamPart;
	var width=$("#chocolate3").width()/gameObj.totalParts;
	var height=$("#chocolate3").height();
	
	for(var i=0;i<total;i++)
	{
		if(i<num1)
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else if(i<num2)
		{
			data+='<div id="partpam'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#FFECEC"></div>';
		}
		else
		{
			data+='<div id="partremain'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate3").html(data);
	width=$("#chocolate3").width()/gameObj.totalParts;
	
	$("#marker").css('left',$("#chocolate3").css('left'));
	$("#marker").css('width',(width*gameObj.combinePart)-4+'px');


}
animationStatic.prototype.hintDiagram3=function()
{
	var num1=gameObj.fractionNum1;
	var den1=gameObj.fractionDen1;
	var num2=gameObj.fractionNum2;
	var den2=gameObj.fractionDen2;
	var data='';


	data+='<div id="hint1">';
		
			data+='<div id="textKnow" style="width:300px;display:inline-block;text-align:left;">'+replaceDynamicText(miscArr['know'],gameObj.numberLanguage,'gameObj')+'</div>';
		data+='<div id="johnPart" style="position:relative;top:-25px;">'
		data+='<div id="chocolate1" class="chocolate">';
		data+='</div>';
		data+='</div>';
		data+='<div id="pamPart" style="position:relative;top:-25px;">';
		data+='<div id="chocolate2" class="chocolate"></div>';
		data+='</div>';
	data+='</div>';
	$("#animationPanel").append(data);
	
	// Chocolate Part of John
	var l=lcm(den1,den2);
	var width=$("#chocolate1").width()/den1;
	var height=$("#chocolate1").height();
	
	//num1=(num1*l)/den1;

	data='';
	for(var i=0;i<den1;i++)
	{
		if(i<num1)
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate1").html(data);
	$("#johnPart").append('<div id="john" style="position:relative;top:30px;">'+replaceDynamicText(miscArr['john'],gameObj.numberLanguage,'gameObj')+'</div>');
	// Chocolate Part of Pam

	var width=$("#chocolate2").width()/den2;
	var height=$("#chocolate2").height();
	data='';

	//num2=(num2*l)/den2;

	for(var i=0;i<den2;i++)
	{
		if(i<num2)
		{
			data+='<div id="partpam'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#FFECEC"></div>';
		}
		else
		{
			data+='<div id="partpam'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate2").html(data);
	$("#pamPart").append('<div id="pam" style="position:relative;top:30px;">'+replaceDynamicText(miscArr['pam'],gameObj.numberLanguage,'gameObj')+'</div>');
	
}
animationStatic.prototype.john_marker=function()
{
	
	var num1=gameObj.fractionNum1;
	var den1=gameObj.fractionDen1;
	var num2=gameObj.fractionNum2;
	var den2=gameObj.fractionDen2;

	
	// Chocolate Part of John
	var l=lcm(den1,den2);
	var width=$("#chocolate1").width()/l;
	var height=$("#chocolate1").height();
		
	num1=(num1*l)/den1;
	// Chocolate Part of John
	
	data='';
	for(var i=0;i<l;i++)
	{
		if(i<num1)
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else
		{
			data+='<div id="partjohn'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate1").html(data);
	var widthmark=width*num1;
	$("#hint1").append('<div id="markerJohn" class="boxSize" style="position:absolute;left:'+(parseInt($("#chocolate1").css('left')))+'px;top:33px;width:'+widthmark+'px;height:70px;border:2px solid red;display">');
	
}
animationStatic.prototype.pam_marker=function()
{
		var num1=gameObj.fractionNum1;
	var den1=gameObj.fractionDen1;
	var num2=gameObj.fractionNum2;
	var den2=gameObj.fractionDen2;

	
	// Chocolate Part of Pam
	var l=lcm(den1,den2);
	var width=$("#chocolate2").width()/l;
	var height=$("#chocolate2").height();
	
	num2=(num2*l)/den2;
	
	var widthmark=width*num2;
	$("#hint1").append('<div id="markerPam" class="boxSize" style="position:absolute;left:'+(parseInt($("#chocolate2").css('left')))+'px;top:148px;width:'+widthmark+'px;height:70px;border:2px solid red;display">');
	
}
animationStatic.prototype.explainDivision=function()
{
	var data='';
	data+='<div id="animation1">';
		data+='<div id="text" style="text-align:left;width:320px;height:70px;position:relative;left:25px;"></div>';
		data+='<div id="chocolate4" class="chocolate" style="left:25px;">';
		data+='</div>';
	data+='</div>';
	$("#animationPanel").append(data);

	// Creating parts of the chocolate
	setTimeout(function(){
		data='';
		var min=gameObj.fractionDen1>gameObj.fractionDen2?gameObj.fractionDen2:gameObj.fractionDen1;
		var width=$("#chocolate4").width()/min;
		var height=$("#chocolate4").height();
		gameObj.divideBar=min;
		$("#text").html(replaceDynamicText(miscArr['animation1_1'],gameObj.numberLanguage,'gameObj'));
		for(var i=0;i<min;i++)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;border:1px solid white"></div>';	
		}
		$("#chocolate4").html(data);
	},2000);
	setTimeout(function(){
		data='';
		if(gameObj.type==2)
			var m=gameObj.fractionDen2/gameObj.fractionDen1;
		else
			var m=gameObj.fractionDen1>gameObj.fractionDen2?gameObj.fractionDen1:gameObj.fractionDen2;
		var max=gameObj.totalParts;
		var width=$("#chocolate4").width()/max;
		var height=$("#chocolate4").height();
		gameObj.fractionParts=m;
		$("#text").html(replaceDynamicText(miscArr['animation1_2'],gameObj.numberLanguage,'gameObj'));
		for(var i=0;i<max;i++)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;border:1px solid white"></div>';	
		}
		$("#chocolate4").html(data);
	},5000);

}
animationStatic.prototype.explainAlmostCorrect=function()
{
	var data='';
	data+='<div id="hint3">';
		data+='<div id="chocolate5" class="chocolate">';
		data+='</div>';
		data+='<div id="marker" ></div>';
		data+='<div id="text1" style="width:400px;text-align:center;margin-top:15px;">'+replaceDynamicText(miscArr['animation2_1'],gameObj.numberLanguage,'gameObj')+'</div>';
	data+'</div>';
	data+='<div id="text2" style="text-align:left;position:relative;line-height:1.8em;width:360px;border:2px solid #FF8204;padding:5px;left:15px;border-radius:12px;">'+replaceDynamicText(miscArr['animation2_2'],gameObj.numberLanguage,'gameObj')+'</div>';
	$("#animationPanel").append(data);
	data='';
	var total=(gameObj.totalParts)*2;
	var num1=gameObj.johnPart*2;
	var num2=(gameObj.johnPart+gameObj.pamPart)*2;
	var width=$("#chocolate5").width()/total;
	var height=$("#chocolate5").height();
	
	for(var i=0;i<total;i++)
	{
		if(i<num1)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else if(i<num2)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#FFECEC"></div>';
		}
		else
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate5").html(data);
	width=$("#chocolate5").width()/gameObj.totalParts;
	
	$("#marker").css('left',$("#chocolate5").css('left'));
	$("#marker").css('width',(width*gameObj.combinePart)-4+'px');
	setTimeout(function(){
		data='';
	var total=(gameObj.totalParts)*2;
	var num1=gameObj.johnPart*2;
	var num2=(gameObj.johnPart+gameObj.pamPart)*2;
	var width=$("#chocolate5").width()/total;
	var height=$("#chocolate5").height();
	
	var left=0;
	for(var i=0;i<total;i++)
	{
		if(i%2==0)
		{
			data+='<div class="boxSize" style="position:absolute;display:inline-block;width:'+((width*2))+'px;height:'+(height)+'px;left:'+left+'px;border:2px solid red"></div>';
			left+=(width*2);
		}
		if(i<num1)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else if(i<num2)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#FFECEC"></div>';
		}
		else
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate5").html(data);
	width=$("#chocolate5").width()/gameObj.totalParts;
	
	$("#marker").css('left',$("#chocolate5").css('left'));
	$("#marker").css('width',(width*gameObj.combinePart)-4+'px');
	},3000);
	/*setTimeout(function(){
		animationObj.explainCorrect();
	},6000);*/

}
animationStatic.prototype.explainCorrect=function()
{
	var data='';
	var total=(gameObj.totalParts);
	var num1=gameObj.johnPart;
	var num2=(gameObj.johnPart+gameObj.pamPart);
	var width=$("#chocolate5").width()/total;
	var height=$("#chocolate5").height();
	$("#text2").remove();
	for(var i=0;i<total;i++)
	{
		if(i<num1)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#DBAF95"></div>';
		}
		else if(i<num2)
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;background:#FFECEC"></div>';
		}
		else
		{
			data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';	
		}
		
	}
	$("#chocolate5").html(data);
	width=$("#chocolate5").width()/gameObj.totalParts;
	
	$("#marker").css('left',$("#chocolate5").css('left'));
	$("#marker").css('width',(width*gameObj.combinePart)+'px');
	
}
animationStatic.prototype.divideChocolate=function(parts)
{
	var width=($("#chocolate").width())/parts;
	var height=$("#chocolate").height();
	var data='';
	for(var i=0;i<parts;i++)
	{
		data+='<div id="part'+i+'" class="part" style="float:left;width:'+width+'px;height:'+height+'px;"></div>';
	}
	$("#chocolate").html(data);
}
animationStatic.prototype.fadePart=function(parts,colorCode)
{
	for(var i=0;i<parts;i++)
	{
		$("#part"+this.lastFadedPart).css('background',colorCode);
		this.lastFadedPart++;
	}
	
}
animationStatic.prototype.getSubParts=function(parts)
{

}
animationStatic.prototype.type2=function()
{
	
}


function gcd(x, y) 
{
	while (y != 0) {
		var z = x % y;
		x = y;
		y = z;
	}
	return x;
}

function lcm(x,y) 
{
	var g = gcd(x,y);
	var l = (x * y) / g;
	return l;
}
function enable(id)
{
	$("#"+id).attr('disabled',false);
	$("#"+id).focus();
}
function disableAll(id)
{
	$(".textBox").attr('disabled',true);
	
}
function loadPromptData(promptId)
{
	
	$("input").attr('disabled',true);
	$("#message").html(replaceDynamicText(promptArr[promptId],gameObj.numberLanguage,'gameObj'));
	$("#prompt").show();
	$("#barTop").css('width',($("#prompt").width()+22)+'px');
	if(posPromptFlag==0)
		$("#prompt").css({'left':'10px','top':'300px'});
	else
		$("#prompt").css({'left':'380px','top':'250px'});
	$("#Ok").focus();
}
function reduce(numerator,denominator){
  g = gcd(numerator,denominator);

  var data='<div class="fraction"><div class="frac numerator">'+(numerator/g)+'</div><div class="frac">'+(denominator/g)+'</div></div>';
  return data;
}
function joined(numerator,denominator){
  g = gcd(numerator,denominator);
  gameObj.jNum=numerator/g;
  gameObj.jDen=denominator/g;
}
function leftOut(numerator,denominator){
  g = gcd(numerator,denominator);
  gameObj.pNum=numerator/g;
  gameObj.pDen=denominator/g;
}

function empty()
{
	$(".textBox").val("");
}
function closePrompt()
{
	setTimeout(function(){
		if(blankFlag==1)
		{
			$("#"+lastFocus).val("");
			enable(lastFocus);
		}
		else
		{
				
			
			if(attemptBlank1==1 || attemptBlank1==2 || attemptBlank1==4)
			{
				$("#blank1").removeClass('wrong');
				$("#blank2").removeClass('wrong');
				$("#blank3").removeClass('wrong');

				if(attemptBlank1==1 && divideFlag==1 && (correctBlank1!=1 && aCorrectFlag != 1))
				{
					$("#blank1").val('');
					$("#blank2").val('');
					$("#blank3").val('');
					$("#chocolate").html('');
					enable("blank1");
					divideFlag=10;
				}
				if(attemptBlank1==2 && (correctBlank1!=1 && aCorrectFlag != 1))
				{
					disableAll();
					$("#blank1").replaceWith('<div class="answer system">'+gameObj.actualAnswerArray[0]+'</div>');
					$("#blank2").val("");
					$("#blank3").val("");
					$("#animationPanel").html("");
						animationObj.hintDiagram3();
						$("#hint1").css('top','10px');
						setTimeout(function(){
							animationObj.explainDivision();
							setTimeout(function(){
								loadPromptData("prompt3");
								aCorrectFlag=1;
							},9000);			
						},3000);
				}
				if(almostCorrect!=3 && correctBlank1==1 && aCorrectFlag==1)
				{
					aCorrectFlag=1;
					attemptBlank1=10;
					disableAll();
					//$("#animationPanel").html("");
					//animationObj.hintDiagram2();
					$("#blank1").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[0]+'</div>');
					enable("blank2");
					$("#blank2").val("");
					$("#blank3").val("");
					
				}
				if(almostCorrect!=3 && correctBlank1==1 && correctBlank2==1 && correctBlank3==1)
				{
					aCorrectFlag=1;
					attemptBlank1=10;
					disableAll();
					$("#animationPanel").html("");
					animationObj.hintDiagram2();
					$("#next").show();
				}
				if(attemptBlank1==1 && (correctBlank1!=1 && aCorrectFlag==1))
				{
					$("#blank1").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[0]+'</div>');
					$("#text").css({'opacity':'0'});
					$("#animation1").css({'border':'2px solid transparent'});
					$("#blank2").val("");
					$("#blank3").val("");
					enable("blank2");
					attemptBlank1=10;

				}
				if(attemptBlank1==2 && (correctBlank1!=1 && aCorrectFlag==1))
				{
					$("#blank1").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[0]+'</div>');
					$("#text").css({'opacity':'0'});
					$("#animation1").css({'border':'2px solid transparent'});
					$("#blank2").val("");
					$("#blank3").val("");
					enable("blank2");
					attemptBlank1=10;

				}
				if(attemptBlank1<3 && almostCorrect==3 && correctBlank1==1)
				{
					
					aCorrectFlag=1;
					setTimeout(function(){$("#blank1").blur();$("#blank2").blur();$("#blank3").blur();},200);
					attemptBlank1=10;
					disableAll();
					$("#animationPanel").html("");
						//animationObj.hintDiagram2();
						setTimeout(function(){
							animationObj.explainAlmostCorrect();
							setTimeout(function(){
								loadPromptData("prompt10");
								attemptBlank1=4;
							},9000);			
						},3000);
				}
				if(attemptBlank1==4 && correctBlank1==1 && correctBlank2==1 && correctBlank3==1)
				{
					disableAll();
					attemptBlank1=10;
					animationObj.explainCorrect();
					$("#blank1").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[0]+'</div>');
					$("#blank2").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[1]+'</div>');
					$("#blank3").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[2]+'</div>');
					$("#next").show();
				}
				
			}
			if(attemptBlank2==1 || attemptBlank2==2)
			{
				if(correctBlank2==1)
				{
					//animationObj.fadePart(gameObj.actualAnswerArray[1],"#DBAF95");
					attemptBlank2=10;
				}
				else
				{
					$("#blank2").removeClass('wrong');
					if(attemptBlank2==2)
					{
						//animationObj.fadePart(gameObj.actualAnswerArray[1],"#DBAF95");
						$("#blank2").replaceWith('<div class="answer system">'+gameObj.actualAnswerArray[1]+'</div>');
						attemptBlank2=10;
					}	
				}
				$("#"+lastFocus).val('');
				enable(lastFocus);
			}
			if(attemptBlank3==1 || attemptBlank3==2)
			{
				if(correctBlank3==1)
				{
					//animationObj.fadePart(gameObj.actualAnswerArray[2],"#FFECEC");
					attemptBlank3=10;
					$("#animationPanel").html("");
					animationObj.hintDiagram2();
				}
				else
				{
					$("#blank3").removeClass('wrong');
					if(attemptBlank3==2)
					{
						//animationObj.fadePart(gameObj.actualAnswerArray[2],"#FFECEC");
						$("#blank3").replaceWith('<div class="answer system">'+gameObj.actualAnswerArray[2]+'</div>');
						attemptBlank3=10;
						$("#next").show();
						lastFocus="blank4";
						$("#animationPanel").html("");
						animationObj.hintDiagram2();
					}	
				}
				$("#"+lastFocus).val('');
				enable(lastFocus);
			}
			if(attemptBlank4==1 || attemptBlank4==2)
			{
				if(correctBlank4==1)
				{

					gameObj.loadQuestion(5);
					attemptBlank4=10;
				}
				else
				{
					$("#blank4").removeClass('wrong');
					if(attemptBlank4==2)
					{
						$("#blank4").replaceWith('<div class="answer system">'+gameObj.actualAnswerArray[3]+'</div>');
						attemptBlank4=10;
						lastFocus="blank5";
						gameObj.loadQuestion(5);
					}	
				}
				$("#"+lastFocus).val('');
				enable(lastFocus);
			}
			if(attemptBlank5==1)
			{

				if(correctBlank5==1)
				{
					gameObj.loadQuestion(6);
					attemptBlank5=10;
				}
				else
				{
					$("#blank5").removeClass('wrong');
					$("#blank5").replaceWith('<div class="answer system">'+gameObj.actualAnswerArray[4]+'</div>');
					attemptBlank5=10;
					lastFocus="blank6";
					gameObj.loadQuestion(6);
						
				}
				$("#"+lastFocus).val('');
				enable(lastFocus);
			}
			if(attemptBlank6==1 || attemptBlank6==2)
			{
				if(correctBlank6==1)
				{
					if(mainQuestionCounter<5)
					{
						if(correctBlank1==1 && correctBlank2==1 && correctBlank3==1 && correctBlank4==1 && correctBlank5==1 && correctBlank6==1)
						{
							levelWiseScore++;
						}
						if(levelWiseScore<3)
							$("#nextQuestion").show();
						if(levelWiseScore>=3)
							completed=1;
					}
					if(mainQuestionCounter>=5)
					{
						if(correctBlank1==1 && correctBlank2==1 && correctBlank3==1 && correctBlank4==1 && correctBlank5==1 && correctBlank6==1)
						{
							levelWiseScore++;
						}
						completed=1;
						if(levelWiseScore>=3)
						{
							levelWiseStatus=1;
						}
						else
						{
							levelWiseStatus=2;
						}
					}
						
					attemptBlank6=10;
				}
				else
				{
					$("#blank6").removeClass('wrong');
					if(attemptBlank6==2)
					{
						$("#blank6").replaceWith('<div class="answer system">'+gameObj.actualAnswerArray[5]+'</div>');
						attemptBlank6=10;
						if(mainQuestionCounter<5)
						{
							if(correctBlank1==1 && correctBlank2==1 && correctBlank3==1 && correctBlank4==1 && correctBlank5==1 && correctBlank6==1)
							{
								levelWiseScore++;
							}
							if(levelWiseScore<3)
								$("#nextQuestion").show();
							if(levelWiseScore>=3)
							completed=1;
						}
						if(mainQuestionCounter>=5)
						{
							completed=1;
							if(levelWiseScore>=3)
							{
								levelWiseStatus=1;
							}
							else
							{
								levelWiseStatus=2;
							}
						}
						
					}	
				}
				if(completed==1)
					clearInterval(interval);
				$("#"+lastFocus).val('');
				enable(lastFocus);
			}
		}
	////////////////////////////////////////
	//  Check for the chocolate bar to be reset 
			$("#prompt").hide(0);
				/*
				if(divideFlag==1 && correctFlag!=1)
				{
					animationObj.divideChocolate(gameObj.actualAnswerArray[0]);
				}
				else if(correctFlag==1 && almostCorrect!=3)
				{
					//All three are correct
					$("#blank1").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[0]+'</div>');
					$("#blank2").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[1]+'</div>');
					$("#blank3").replaceWith('<div class="answer correct">'+gameObj.actualAnswerArray[2]+'</div>');
					empty();
				}
				else if(correctFlag==1 && almostCorrect==3)
				{
					// All three are almost correct
					disableAll();
					$("#animationPanel").html("");
					//animationObj.hintDiagram2();
					setTimeout(function(){
						animationObj.explainAlmostCorrect();
						setTimeout(function(){
							loadPromptData("prompt10");
							attemptBlank1=4;
						},9000);			
					},3000);
				}
				else if(attemptBlank1==2 && correctFlag!=1)
				{
					disableAll();
					$("#blank1").replaceWith('<div class="answer correct">'+replaceDynamicText(gameObj.actualAnswerArray[0],gameObj.numberLanguage,'gameObj')+'</div>');
					aCorrectFlag=1;
					$("#animationPanel").html("");
					animationObj.hintDiagram1(1);
					setTimeout(function(){
						animationObj.explainDivision();
						setTimeout(function(){
							loadPromptData("prompt3");
							attemptBlank1=4;
							empty();
						},9000);			
					},3000);
				}
				else if(attemptBlank1==3)
				{
					$("#text").css({'opacity':'0'});
					$("#animation1").css({'border':'2px solid transparent'});
				}
				
				else if(attemptBlank1==4 && almostCorrect==3)
				{
					animationObj.explainCorrect();
					aCorrectFlag=1; 
					$("#blank1").replaceWith('<div class="answer correct">'+replaceDynamicText(gameObj.actualAnswerArray[0],gameObj.numberLanguage,'gameObj')+'</div>');
					$("#blank2").replaceWith('<div class="answer correct">'+replaceDynamicText(gameObj.actualAnswerArray[1],gameObj.numberLanguage,'gameObj')+'</div>');
					$("#blank3").replaceWith('<div class="answer correct">'+replaceDynamicText(gameObj.actualAnswerArray[2],gameObj.numberLanguage,'gameObj')+'</div>');
				}
				/////////////////////////////////////////////////

				$("#"+setBlank).val("");
				$(".textBox").removeClass('wrong');
				*/

	},210);	
}
