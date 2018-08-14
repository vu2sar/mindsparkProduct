var marginleft = 30;
var margintop = 30;
var AQattempt = 0;
var consecutiveTrue = 0;
var levelWiseStatus = 0;
var levelWiseScore = 0;	
var setConsecutiveFlag=0;
var Trueattempts=0;
var levelsAttempted = "L1";
var levelWiseTimeTaken=0;
var extraParameters="";
var time = 0;
var noOfLevels;
var lastLevelCleared;
var toRun=0;
var completed = 0;
var currentQues = 0;
var firstTry = 0;
var wrongAttempt = 0;
var score = 0;
var tmp1 = ' &nbsp; &nbsp; ';
var incChange = 0;
var comingFromInit = 1;
var right;
var wrong;

function questionInteractive()
{
	if (parseInt(getParameters['noOfLevels']) != 1 )
	{
		$("#container").html("Wrong number of levels entered! Enter again");
		toRun=1;
		return;
		
	}
	if (parseInt(getParameters['lastLevelCleared']) != 0 )
	{
		$("#container").html("Variable lastLevelCleared is incorrect! Enter correct variable");
		toRun=1;
		return;
	}

	if (typeof getParameters['this.language']=='undefined' )
		this.language = 'english';	
	else
		this.language = getParameters['this.language'];

	if (typeof getParameters['numberLanguage']=='undefined' )
		this.numberLanguage = 'english';	
	else
		this.numberLanguage = getParameters['numberLanguage'];

	right = '<img src="../assets/right.gif">';
	wrong = '<img src="../assets/wrong.gif">';

	this.trial = 0;
	this.m;
	this.n;
	this.a;
	this.b;
	this.randomizeDisplay = Math.ceil(Math.random()*2); 
	this.comparevalue;
	this.compareSymbols = [ '>', '<', '=' ];
	this.trueans; 						//0 if a/m > b/n; 1 if a/m < b/n
	this.maxattempts=5;
	this.attempt=0;
	this.frac1; 
	this.frac2;
	this.number1;
	this.number2;
	this.radioAnswer;
	this.P1flag=0;
	this.buttonOrder;
	this.temp1;
	this.temp2;
	this.toppos=120;
	this.leftpos=100;
	this.displaymsg;
	this.htmlx;
	calcTime();
}

function calcTime()
{
	time = setInterval( function(){ levelWiseTimeTaken++ }, 1000);
}

questionInteractive.prototype.init = function()
{
	interactiveObj.P1flag=0;
	do
	{
		interactiveObj.calcNumbers();
	}
	while (interactiveObj.a == interactiveObj.b )
		
	AQattempt = 0;
	interactiveObj.toppos = 120;
	interactiveObj.leftpos = 100;
	interactiveObj.askQuestion(interactiveObj.animateP3,interactiveObj.animateP1);	
	currentQues++;
	firstTry = 1;
	incChange = 1;
	$("#selectionbox").change(function()
	{	
		$("#selectionbox").prop('disabled',true);
		interactiveObj.comparevalue = $("#selectionbox").val(); 
		$("#hand").css({"opacity":"0"});
		if (incChange == 1)
			interactiveObj.displaymsg = promptArr['M1'];
		else
			interactiveObj.displaymsg = promptArr['WP'];
		interactiveObj.calcAns(interactiveObj.temp1, interactiveObj.temp2);
		firstTry = 0;
		$("#ok").focus();
	}); 
	
	$("#ok").keypress(function(event)
	{
	    if(event.keyCode == 13)
		    {
		        $(".okButton").click();
		    }
	});
}

questionInteractive.prototype.askQuestion = function(fnCalledIfYes,fnCalledIfNo)
{
	var html = '';
	$("#container").html(html);
	/* drawText */
	var html ='';
	html += '<div id="div1"> '+promptArr['div1']+' </div>';
	$("#container").append(html);
	
	var html = '';

	html += '<div id="div2" >';
		html += '<div class="fraction" ><div class="frac numerator" > '+replaceDynamicText(interactiveObj.numberOrder[0],interactiveObj.numberLanguage,'interactiveObj')+' ';
		html += '</div><div class="frac"> '+replaceDynamicText(interactiveObj.numberOrder[1],interactiveObj.numberLanguage,'interactiveObj')+' </div> </div>';

		html += '<select id="selectionbox" >';
			html += '<option id="Select"> '+promptArr['select']+' </option>';
  			html += '<option value="0">'+promptArr['isGreaterThan']+' </option>';
   		html += '<option value="1">'+promptArr['isLessThan']+' </option>';
   		html +='<option value="2">'+promptArr['equals']+' </option>';
   	html += '</select>'
   
  		html += '<div class="fraction" >';
  			html +='<div class="frac numerator" > '+replaceDynamicText(interactiveObj.numberOrder[2],interactiveObj.numberLanguage,'interactiveObj')+'</div>';
			html +='<div class="frac"> '+replaceDynamicText(interactiveObj.numberOrder[3],interactiveObj.numberLanguage,'interactiveObj')+' </div> </div>';
			html += '<span style="margin-top:3px;position:absolute">&nbsp;.</span>';
		html += '</div>';

	html += '<div > <img id="hand" src="../assets/hand.png" style="width:40px; height:20px; margin-top:20px;margin-left:15px;"> </div>' ;

	$("#container").append(html);
	interactiveObj.focusOnAQ(interactiveObj.animateP3, interactiveObj.animateP1 );
	$("#selectionbox").focus();
}

questionInteractive.prototype.calcNumbers = function()
{	
	function isfactor(numerator,denominator)
	{
		newnumerator = reduce(numerator,denominator);
		if (numerator != newnumerator) return true;
		if (numerator == 1) return false;
		if(denominator%numerator==0) return false; 
	}

	function reduce(numerator,denominator){
 	 	var gcd = function gcd(a,b){
 		   return b ? gcd(b, a%b) : a;
 	 	};
 		 gcd = gcd(numerator,denominator);
 	 	return numerator/gcd;
	}
	/* generate random number m */
	interactiveObj.m = Math.ceil(Math.random()*5 + 1);
	/* a is always less than m */
	if (interactiveObj.m ==2 ) interactiveObj.a = 1;
	else
		do 
		{
			interactiveObj.a = Math.ceil(Math.random()*(interactiveObj.m-1) );	
		} 
		while ( interactiveObj.a >= interactiveObj.m || isfactor(interactiveObj.a,interactiveObj.m) );
	
	/* n=2m or n=3m */
	if(interactiveObj.m > 4	)
		interactiveObj.n = 2*interactiveObj.m; //multiply only by 2 if m > 4
	else	
		interactiveObj.n = Math.ceil(Math.random()*2+1)*interactiveObj.m;
	/* b  */	
	do
	{
		interactiveObj.b = Math.ceil(Math.random()*(interactiveObj.n-1));
	}
	while ( interactiveObj.b >= interactiveObj.n || isfactor(interactiveObj.b,interactiveObj.n) );

	if (interactiveObj.randomizeDisplay==1)
		interactiveObj.numberOrder = [interactiveObj.a,interactiveObj.m,interactiveObj.b,interactiveObj.n];	
	else
		interactiveObj.numberOrder = [interactiveObj.b,interactiveObj.n,interactiveObj.a,interactiveObj.m];
}	

questionInteractive.prototype.calcAns = function(fnCalledIfYes, fnCalledIfNo)
{	
	if(interactiveObj.randomizeDisplay===1)
	{	
		var multiplyby  = interactiveObj.numberOrder[3]/interactiveObj.numberOrder[1];
		interactiveObj.trueans = (interactiveObj.numberOrder[0]*multiplyby > interactiveObj.numberOrder[2] )? 0 : 1;
	}
	else
	{
		var multiplyby  = interactiveObj.numberOrder[1]/interactiveObj.numberOrder[3];
		interactiveObj.trueans = (interactiveObj.numberOrder[2]*multiplyby > interactiveObj.numberOrder[0] )? 1 : 0;
	}

//	Id = document.getElementById("selectionbox");
//	Id.selectedIndex = "Select"; 

	if (interactiveObj.comparevalue == interactiveObj.trueans)
	{	
		correctTry = 1;
		if (firstTry == 1)
		{
			consecutiveTrue++;
			Trueattempts++;
			score++;
		}
		var html='';
		interactiveObj.displaymsg = promptArr['CP'];
		delayTime = 5000;
		interactiveObj.promptFunc(delayTime,fnCalledIfYes);
		$('#selectionbox').css('color','green');
	}   
	else
	{	
		correctTry = 0;
		consecutiveTrue = 0;
		delayTime = 5000;
		displaymsg = promptArr['M1'] ;
		//if (AQattempt == 0)
		interactiveObj.promptFunc(delayTime,fnCalledIfNo);
		$('#selectionbox').css('color','red');
		if (firstTry == 1)
		{
			wrongAttempt++;			
		}
	}

	if (firstTry == 1)
	{
		(consecutiveTrue > 1)? setConsecutiveFlag = 1 : setConsecutiveFlag = 0;
		(interactiveObj.maxattempts < 7 && setConsecutiveFlag == 0 && currentQues>4)? interactiveObj.maxattempts++ : interactiveObj.maxattempts;
		(interactiveObj.comparevalue == interactiveObj.trueans)? tmp1 = 1 : tmp1 = 0;

		if (currentQues == 1)
		{
			extraParameters = '( ('+interactiveObj.numberOrder[0]+'/'+interactiveObj.numberOrder[1]+', '+interactiveObj.numberOrder[2]+'/'+interactiveObj.numberOrder[3]+', '+tmp1+') )';
		}
		else
		{
			extraParameters = extraParameters.substring(0,extraParameters.length-1);
			extraParameters = extraParameters + " | " + '('+interactiveObj.numberOrder[0]+'/'+interactiveObj.numberOrder[1]+', '+interactiveObj.numberOrder[2]+'/'+interactiveObj.numberOrder[3]+', '+tmp1+') )';
		}	
	}
	else
	{
		extraParameters = extraParameters.substring(0,extraParameters.length-3);
		extraParameters = ''+extraParameters+', '+correctTry+') )'; 
	}
}

questionInteractive.prototype.animateP3 = function()
{
	var html = '';
	html += '<div id="P3">';
	
		html += '<div id="P3Line1">';
			html += '<div class="fraction" style="position:relative"><div class="frac numerator" > '+replaceDynamicText(interactiveObj.numberOrder[0],interactiveObj.numberLanguage,'interactiveObj')+' ';
			html += '</div><div class="frac"> '+replaceDynamicText(interactiveObj.numberOrder[1],interactiveObj.numberLanguage,'interactiveObj')+' </div> </div>';
			html += '&nbsp; '+interactiveObj.compareSymbols[interactiveObj.trueans]+' ';
			html += '<div class="fraction" ><div class="frac numerator" > '+replaceDynamicText(interactiveObj.numberOrder[2],interactiveObj.numberLanguage,'interactiveObj')+' ';
			html += '</div><div class="frac"> '+replaceDynamicText(interactiveObj.numberOrder[3],interactiveObj.numberLanguage,'interactiveObj')+' </div> </div>';
		html += '</div>';

	/* Line 2 */

	html +='<div id="P3Line2" >';
	
	/* draw boxes */
		interactiveObj.htmlx = '<div id="P3boxes1"  >';
		for (var i=0; i<interactiveObj.n; i++)
		{	
			var boxwidth = 160/interactiveObj.n;  //height specified in css 
			if ( i < interactiveObj.b ) 
				interactiveObj.htmlx += '<div id="P3box1" class="P3opaquebox1" style="width:'+(boxwidth-2)+'px" >  </div>';
			else
				interactiveObj.htmlx += '<div id="P3box1" style="width:'+(boxwidth-2)+'px" >  </div>';
		}
		interactiveObj.htmlx +='</div>';

		interactiveObj.frac1 = '<div class="fraction" style="position:relative"><div class="frac numerator" > '+interactiveObj.b+' </div><div class="frac"> '+interactiveObj.n+' </div> </div>';
		html += '<div style="display:inline-block; float:left"> '+replaceDynamicText(promptArr['P3text1'], interactiveObj.numberLanguage, 'interactiveObj')+' </div>'; 

	html += '</div>';

	
	/* Line 3 */
	html +='<div id="P3Line3" >';

		interactiveObj.htmlx = '<div id="P3boxes2">';
		for (var i=0; i<interactiveObj.m; i++)
		{
			var boxwidth = 160/interactiveObj.m;  //height specified in css 
			if ( i < interactiveObj.a ) 
				interactiveObj.htmlx += '<div id="P3box2" class="P3opaquebox2" style="width:'+(boxwidth-2)+'px" >  </div>';
			else
				interactiveObj.htmlx += '<div id="P3box2" style="width:'+(boxwidth-2)+'px" >  </div>';
			}
		interactiveObj.htmlx += '</div>';
	
		interactiveObj.frac2 = '<div class="fraction" style="position:relative"><div class="frac numerator" > '+interactiveObj.a+' </div><div class="frac"> '+interactiveObj.m+' </div> </div>';
		html += ' <div style="display:inline-block; float:left"> '+replaceDynamicText(promptArr['P3text2'], interactiveObj.numberLanguage, 'interactiveObj')+' </div>'; 

	html +='</div>';

	/* Line 4 */
	html +='<div id="P3Line4" >';
	//html += ' or '+interactiveObj.a*(interactiveObj.n/interactiveObj.m)+' out of '+interactiveObj.n+' equal parts';
		interactiveObj.temp2 = interactiveObj.a*interactiveObj.n/interactiveObj.m;
		html += ' <div> '+replaceDynamicText(promptArr['P3text3'], interactiveObj.numberLanguage, 'interactiveObj')+' </div>';
	html += '</div>';
	
	html += '</div>';
	
	$("#P2").remove();
	$("#P1").remove();
	$("#container").append(html); 		
	
	$("#P3Line1").delay(0).animate({opacity:0},0);
	$("#P3Line2").delay(0).animate({opacity:0},0);
	$("#P3boxes1").delay(0).animate({opacity:0},0);
	$("#P3Line3").delay(0).animate({opacity:0},0);
	$("#P3boxes2").delay(0).animate({opacity:0},0);
	$("#P3Line4").delay(0).animate({opacity:0},0);
	
	$("#P3Line1").delay(500).animate({opacity:1},100);
	$("#P3Line2").delay(2000).animate({opacity:1},100);
	$("#P3boxes1").delay(2500).animate({opacity:1},100);
	$(".P3box1").delay(3500).animate({opacity:1},100);
	$("#P3Line3").delay(4000).animate({opacity:1},100);
	$("#P3boxes2").delay(5500).animate({opacity:1},100);
	$(".P3box2").delay(6000).animate({opacity:1},100);
	$("#P3Line4").delay(6500).animate({opacity:1},100);
	
	setTimeout( function() { interactiveObj.decideIfNext(); }, 7000 );
}	

questionInteractive.prototype.decideIfNext = function()
{
	interactiveObj.attempt++;

	if (interactiveObj.attempt < interactiveObj.maxattempts) 
	{	
		/* Show next */
		var html='';
		html += '<div id="next">';
		html += ' <button type="button" id="nextButton" class="button"> '+promptArr['nextText']+' </button> </div>';
		$("#container").append(html);
		$("#nextButton").click(function ()
		{
			$("#next").css('visibility','hidden');
			comingFromInit = 1;
			interactiveObj.init();
		});			
	}
	else
	{
		interactiveObj.endWindow();
		if (wrongAttempt < 3) 
		{
			levelWiseStatus = 1;
		}
		else 
		{
			levelWiseStatus = 2;
		}
		completed = 1;
		levelWiseScore = Trueattempts *100 / interactiveObj.maxattempts;
		(levelWiseScore > 0.5)? levelWiseStatus = 1 : levelWiseStatus = 2;
		 
		clearInterval(time);
	}
}	
	
questionInteractive.prototype.animateP1 = function()
{
	interactiveObj.toppos = 230;
	interactiveObj.leftpos = 280;
	$("#P1").remove(); 
	$("#div2").css("border","none");
	var html = '';
	interactiveObj.frac1 = '<div class="fraction"><div class="frac numerator" > '+interactiveObj.numberOrder[0]+' </div><div class="frac"> '+interactiveObj.numberOrder[1]+' </div> </div>';
	interactiveObj.frac2 = '<div class="fraction"><div class="frac numerator" > '+interactiveObj.numberOrder[2]+' </div><div class="frac"> '+interactiveObj.numberOrder[3]+' </div> </div>';
	interactiveObj.number0 = interactiveObj.numberOrder[0];
	interactiveObj.number1 = interactiveObj.numberOrder[1];
	interactiveObj.number2 = interactiveObj.numberOrder[2];
	interactiveObj.number3 = interactiveObj.numberOrder[3];
	incChange = 1;

	html += '<div id="P1">'
	//	html += '<div id="P1text1"> '+promptArr['if']+' </div> ';
		interactiveObj.htmlx = '<div id="P1boxes1">' 
			for (var i=0; i<interactiveObj.number1; i++)
			{
				var boxwidth = 160/interactiveObj.number1;  //height specified in css 
				if ( i < interactiveObj.number0 ) 
					interactiveObj.htmlx += '<div id="P1box1" class="P1opaquebox1" style="width:'+(boxwidth-2)+'px" >  </div>';
				else
					interactiveObj.htmlx += '<div id="P1box1" style="width:'+(boxwidth-2)+'px" >  </div>';
			}
		interactiveObj.htmlx += '</div>';
		tmp1 = '&nbsp;';
		html += '<div id="P1text2"> ';
		html += ' '+replaceDynamicText(promptArr['P3msg'],interactiveObj.numberLanguage,'interactiveObj')+' ';
		html += '</div>';
		
	/* Draw radio buttons */
		
		html += '<br></br> <div id="radioButtonDiv"> ';
		randomRadioButton();
		
		function randomRadioButton()
		{
			if (interactiveObj.P1flag==0)
				interactiveObj.buttonOrder = arrayShuffle([1,2,3]);
			for (i=0; i<3; i++)
			{
				var stringname = 'radioButton'+''+interactiveObj.buttonOrder[i]+''+'()';
				eval(stringname);
			}
		}
		
		function radioButton1()
		{	
			html += '<div id="0" style="height:22px; width:190px; margin-left:5px; padding:4px" >';
			html += '<input type="radio" value="0" style="float: left" name="radioAnswer" >';
			for (var i=0; i<interactiveObj.number3; i++)
				{
					var boxwidth = 160/interactiveObj.number3;  //height specified in css 
					if ( i < interactiveObj.number2 ) 
						html += '<div id="P1box1" class="P1opaquebox1" style="width:'+(boxwidth-2)+'px" >  </div>';
					else
						html += '<div id="P1box1" style="width:'+(boxwidth-2)+'px" >  </div>';
				}
			html +='</div>';
			html += '<br>';
		}	
		
		function radioButton2()
		{	
			html += '<div id="1" style="height:22px; width:190px; margin-left:5px; padding:4px" >';
			html += '<input type="radio" value="1" style="float: left" name="radioAnswer">';
			for (var i=0; i<(interactiveObj.number2+interactiveObj.number3); i++)
				{	
					var boxwidth = 160/(interactiveObj.number2+interactiveObj.number3);  //height specified in css 
					if ( i < interactiveObj.number2 ) 
						html += '<div id="P1box1" class="P1opaquebox1" style="width:'+(boxwidth-2)+'px" >  </div>';
					else
						html += '<div id="P1box1" style="width:'+(boxwidth-2)+'px" >  </div>';
				}
			html += '</div>';
			html += '<br>';
		}
		
		function radioButton3()
		{	
			html += '<div id="2" style="height:22px; width:190px; margin-left:5px; padding:4px" >';
			html += '<input type="radio" value="2" style="float: left" name="radioAnswer">';
			for (var i=0; i<interactiveObj.number3; i++)
				{
					var boxwidth = 160/interactiveObj.number3;  //height specified in css 
					if ( i < interactiveObj.number0 ) 
						html += '<div id="P1box1" class="P1opaquebox1" style="width:'+(boxwidth-2)+'px" >  </div>';
					else
						html += '<div id="P1box1" style="width:'+(boxwidth-2)+'px" >  </div>';
				}
			html += '</div>';
			html += '<br>';
		}
	html += ' </div>';
	$("#container").append(html);
	
	/* Get value from radio button and proceed in the flowchart */
	
	if (interactiveObj.P1flag==0)
	{	
		$("#radioButtonDiv").change(function()
			{
				interactiveObj.P1flag=1;
				interactiveObj.radioAnswer = $("input:radio[name=radioAnswer]:checked").val();
				$('input[name="radioAnswer"]').attr('disabled', 'disabled'); 
				if (interactiveObj.radioAnswer == 0)  //trueanswer
				{	
				//	$("#0").css("border","2px solid green");
					$("#0").css("width","240px");
					$("#0").append(right);	
					interactiveObj.displaymsg = promptArr['CP'];
					delayTime = 3000;
					interactiveObj.focusOnAQ( interactiveObj.animateP3, interactiveObj.animateP2);
				}				
				else
				{
					/* show prompt P11 */
				//	$("#"+interactiveObj.radioAnswer).css("border","2px solid red");
					$("#"+interactiveObj.radioAnswer).css("width","240px");
					$("#"+interactiveObj.radioAnswer).append(wrong)

					interactiveObj.frac1 = '<div class="fraction"><div class="frac numerator" > '+interactiveObj.numberOrder[0]+' </div><div class="frac"> '+interactiveObj.numberOrder[1]+' </div> </div>';				
					interactiveObj.displaymsg = replaceDynamicText( promptArr['P11text'], interactiveObj.numberLanguage, 'interactiveObj');
					delayTime = 6000;
					interactiveObj.promptFunc(delayTime,interactiveObj.animateP1);
				}
				$("#ok").focus();
			});
	}	
	else
	{
	//	$("#"+interactiveObj.radioAnswer).css("border","2px solid red");
	//	$("#0").css("border","2px solid blue");
		$("#"+interactiveObj.radioAnswer).css("width","240px");
		$("#0").css("width","240px");
		$("#0").append(right);
		$("#"+interactiveObj.radioAnswer).append(wrong);

		setTimeout(function() 
		{ 
			//$("#0").css('border','none');
			interactiveObj.focusOnAQ(interactiveObj.animateP3, interactiveObj.animateP2);
		},1000 ); 
	} 
}

questionInteractive.prototype.focusOnAQ = function(fnCalledIfYes, fnCalledIfNo)
{
	//alert();
	$('input[name="radioAnswer"]').attr('disabled', 'disabled'); 

	if (comingFromInit == 1)
	{
		$("#hand").css({"opacity":"0"});
		comingFromInit = 0;
	}
	else{	
		$("#hand").css({"opacity":"1"});
		$("#hand").delay(50).hide(0);
		$("#hand").delay(100).show(0)
		$("#hand").delay(350).hide(0);
		$("#hand").delay(400).show(0);
		$("#hand").delay(750).hide(0);
		$("#hand").delay(800).show(0);
		$("#hand").delay(1150).hide(0);
		$("#hand").delay(1200).show(0);
	}	
	AQattempt++;
	Id = document.getElementById("selectionbox");
	Id.selectedIndex = "Select"; 
	$("#selectionbox").css('color','black');
	$("#selectionbox").prop('disabled',false);
	interactiveObj.temp1 = fnCalledIfYes;
	interactiveObj.temp2 = fnCalledIfNo;
	$("#selectionbox").focus();
}

questionInteractive.prototype.animateP2 = function()
{	
	$("#div2").css("border","none");
	$("#P1").remove();
	$("#P1").remove();
	incChange = 0;

	interactiveObj.frac1 = '<div class="fraction"><div class="frac numerator" > '+interactiveObj.a+' </div><div class="frac"> '+interactiveObj.m+' </div> </div>';
	
	var html = '';
	html += '<div id="P2">';
		html += '<table >';
			html += '<tr>';
				html += '<td class="row1To4">';
					html += replaceDynamicText(promptArr['P2text11'],interactiveObj.numberLanguage,'interactiveObj');
				html += '</td>';
			html += '</tr>';

			html += '<tr>';
				html += '<td class="row1To4">';					
					html += '<div id="P2boxes1">';
						for (var i=0; i<interactiveObj.m; i++)
						{
							var boxwidth = 160/interactiveObj.m;  //height specified in css 
							if ( i < interactiveObj.a ) 
								html += '<div id="P2box1" class="P2opaquebox1" style="width:'+(boxwidth-2)+'px" >  </div>';
							else
								html += '<div id="P2box1" style="width:'+(boxwidth-2)+'px" >  </div>';
						}
					html +='</div>';
				html += '</td>';
			html += '</tr>';

			html += '<tr>';
				html += '<td class="row1To4">';
					html += replaceDynamicText(promptArr['P2text12'],interactiveObj.numberLanguage,'interactiveObj');
				html += '</td>';
			html += '</tr>';

			html += '<tr>';
				html += '<td class="row1To4">';
					html += '<div id="P2boxes2">';
						for (var i=0; i<interactiveObj.n; i++)
						{
							var boxwidth = 160/interactiveObj.n;  //height specified in css 
							if ( i < interactiveObj.b ) 
								html += '<div id="P2box1" class="P2opaquebox3" style="width:'+(boxwidth-2)+'px" >  </div>';
							else
								html += '<div id="P2box1" style="width:'+(boxwidth-2)+'px" >  </div>';
						}
						html +='</div>';
					html += '</td>';
			html += '</tr>';
		
			html += '<tr>';
				html += '<td class="row1To4" style="padding-top:5px">';
					html += ' <div id= "P2text2"> '+promptArr['P2text2']+' </div>';
				html += '</td>';
			html += '</tr>';

				html += '<tr>';
					html += '<td style="padding-top:10px">';
					html += ' <div class="P2RadioButtonDiv" > ';
						html += '<div id="P20" style="position:absolute;padding:5px">';
							html += '<input type="radio" value="0" style="float: left" name="radioAnswer" >';
							for (var i=0; i<interactiveObj.m; i++)
							{
								var boxwidth = 160/interactiveObj.m;  //height specified in css 
								if ( i < interactiveObj.a) 
									html += '<div id="P2box2" class="P2opaquebox2" style="width:'+(boxwidth-2)+'px" >  </div>';
								else
									html += '<div id="P2box2" style="width:'+(boxwidth-2)+'px" >  </div>';
							}
							html +='</div>';
						html += '</div>';
					html += '</td>';
				html += '</tr>';

				html += '<tr>';
					html += '<td>';
						html += ' <div class="P2RadioButtonDiv" > ';	
							html += '<div id="P21" style="margin-top:40px;position:absolute;padding:5px">';
								html += '<input type="radio" value="1" style="float: left" name="radioAnswer" >';
									for (var i=0; i<interactiveObj.n; i++)
									{
										var boxwidth = 160/interactiveObj.n;  //height specified in css 
										if ( i < interactiveObj.b ) 
											html += '<div id="P2box3" class="P2opaquebox3" style="width:'+(boxwidth-2)+'px" >  </div>';
										else
											html += '<div id="P2box3" style="width:'+(boxwidth-2)+'px" >  </div>';
									}
								html +='</div>';
						html += '</div>';
					html += '</td>';
				html += '</tr>';

			html += '</table>';
	html += '</div>';
$("#container").append(html);
	
	trueans = ( interactiveObj.a*interactiveObj.n/interactiveObj.m > interactiveObj.b)? 0 :  1;

	$(".P2RadioButtonDiv").change(function()
		{
			interactiveObj.P1flag=1;
			comingFromInit = 0;
			interactiveObj.radioAnswer = $("input:radio[name=radioAnswer]:checked").val();
			$('input[name="radioAnswer"]').attr('disabled', 'disabled')  
			if (trueans == interactiveObj.radioAnswer) 
			{	
			//	$("#P2"+interactiveObj.radioAnswer).css("border", "2px solid green");
				$("#P2"+interactiveObj.radioAnswer).css("width","240px");
				$("#P2"+interactiveObj.radioAnswer).append(right);
			}
			else
			{	
			//	$("#P2"+interactiveObj.radioAnswer).css("border", "2px solid red");
				$("#P2"+interactiveObj.radioAnswer).css("width","240px");
				$("#P2"+interactiveObj.radioAnswer).append(wrong);
			}
			interactiveObj.toppos = 310;
			interactiveObj.leftpos = 315;
			if ( interactiveObj.radioAnswer == trueans )
			{	
				interactiveObj.displaymsg = promptArr['CP'];
				delayTime = 3000;
				interactiveObj.promptFunc( delayTime+3000, interactiveObj.animateP2C );
			}
			else
			{
				interactiveObj.displaymsg = promptArr['WP'];
				delayTime = 3000;
				setTimeout( function()
				{	
					$('input[name="radioAnswer"]').attr('checked', false);
				//	$("#P2"+(parseInt(interactiveObj.radioAnswer-1))).css("border", "2px solid blue");
				//	$("#P2"+(parseInt(interactiveObj.radioAnswer+1))).css("border", "2px solid blue");
					$("#P2"+(parseInt(interactiveObj.radioAnswer-1))).css("width","240px");
					$("#P2"+(parseInt(interactiveObj.radioAnswer+1))).css("width","240px");
					$("#P2"+(parseInt(interactiveObj.radioAnswer-1))).append(right);
					$("#P2"+(parseInt(interactiveObj.radioAnswer+1))).append(right);

				}, 1000);
				interactiveObj.promptFunc( delayTime+3000, interactiveObj.animateP2C );
			}
			$("#ok").focus();
		});
}

questionInteractive.prototype.animateP2C = function()
{

	delayTime = 3000;

	interactiveObj.frac1 = '<div class="fraction"><div class="frac numerator" > '+interactiveObj.numberOrder[0]+' </div><div class="frac"> '+interactiveObj.numberOrder[1]+' </div> </div>';
	interactiveObj.frac2 = '<div class="fraction"><div class="frac numerator" > '+interactiveObj.numberOrder[2]+' </div><div class="frac"> '+interactiveObj.numberOrder[3]+' </div> </div>';
	
	interactiveObj.displaymsg = replaceDynamicText(promptArr['tryComparing'],interactiveObj.numberLanguage ,'interactiveObj') ;

	interactiveObj.promptFunc(delayTime,interactiveObj.callNextAfterP2C);
}


questionInteractive.prototype.callNextAfterP2C = function()
{
	interactiveObj.focusOnAQ( interactiveObj.animateP3,interactiveObj.wrongPrompt );
}

questionInteractive.prototype.wrongPrompt = function()
{
	interactiveObj.displaymsg = promptArr['WP'];
	delayTime = 3000;
	interactiveObj.promptFunc( delayTime, interactiveObj.animateP3 );
}

questionInteractive.prototype.promptFunc = function(delayTime,fnToBeCalled)
{	
	var html = '';
	//html+= '<div id="promptDiv" class="prompt" >';
	interactiveObj.promptDiv(html);
	$(".promptContainer").css('top',interactiveObj.toppos+"px");
	$(".promptContainer").css('left',interactiveObj.leftpos+"px");
	$(".promptContainer").css('visibility', 'visible');

	var clickFlag = 0;
	$(".okButton").click(function ()
	{
	 	$(".promptContainer").css('visibility','hidden');
	 	$(".promptContainer").remove();
		fnToBeCalled();
		clickFlag++;
		$("#ok").focus();
	});
	
	setTimeout( function() 
	{	
		if (clickFlag != 1)
		{
			$(".promptContainer").css('visibility', 'hidden');
			$(".promptContainer").remove();
			fnToBeCalled();
		}
	}, delayTime );		
}

questionInteractive.prototype.promptDiv = function(html)
{
	html +='<div class="promptContainer" >';
		html +='<div class="promptText2"> '+ interactiveObj.displaymsg +' </div>';	           
		html +='<div style="clear:both"></div>';
		html +='<button class="okButton" id="ok" > '+promptArr['ok']+' </button>';	
	html +='</div>';

	$("#container").append(html);
	$(".promptContainer").draggable({ containment: "#container" });
	$(".promptContainer").css('visibility', 'hidden');  
}

questionInteractive.prototype.endWindow = function()
{
	html = '<div id="endWindow">';
		html += '<div id="theEnd"> '+promptArr['theEnd']+' </div>';
	html += '</div>';
	$("#container").append(html);
}

