var interactiveObj;
var extraParameters="";
var correctResponse="";
var parameterMissing = false;
var completed = 0;
var qCnt = 0;
var attemptArr = new Array();
var tmp = 0;
var levelWiseTimeTaken=0;
var textFocus;

var noOfLevels = 1;		
var levelWiseMaxScores = 0;
var lastLevelCleared = "";
var previousLevelLock = 0;
var levelsAttempted = "";
var levelWiseScore = 0;	
var levelWiseStatus = 0;

function questionInteractive() {
	
	this.nArr		= new Array();
	this.dArr		= [2,4,5,20,25,50];
	this.ansArr		= new Array();
	this.resArr		= new Array();
	this.frac		= '';
	this.frac1		= '';
	this.frac2		= '';
	this.unit;
	var n;
	var y;
	
	for(var i=0 ; i<6 ; i++)
	{
		attemptArr[i] = 0;
		if(this.dArr[i] == 2)
		{
			this.nArr.push(1);
		}
		else if(this.dArr[i] == 4)
		{
			for(y=0;y<10;y++)
			{
				n=Math.floor((Math.random()*3)+1);
				if(n==1 || n==3)
				{
					this.nArr.push(n);
					break;
				}
			}
		}
		else if(this.dArr[i] == 5)
		{
			n=Math.floor((Math.random()*4)+1);
			this.nArr.push(n);
		}
		else if(this.dArr[i] == 20)
		{
			for(y=0;y<20;y++)
			{
				n=Math.floor((Math.random()*19)+1);
				if(n%2!=0 && n%5!=0 && n>2)
				{
					this.nArr.push(n);
					break;
				}
			}
		}
		else if(this.dArr[i] == 25)
		{
			for(y=0;y<25;y++)
			{
				n=Math.floor((Math.random()*24)+1);
				if(n%5!=0 && n>2)
				{
					this.nArr.push(n);
					break;
				}
			}
		}
		else if(this.dArr[i] == 50)
		{
			for(y=0;y<25;y++)
			{
				n=Math.floor((Math.random()*49)+1);
				if(n%2!=0 && n%5!=0 && n>5)
				{
					this.nArr.push(n);
					break;
				}
			}
		}
		this.ans = this.nArr[i]/this.dArr[i];
		this.ansArr.push(this.ans);
	}
	
	noOfLevels = getParameters['noOfLevels'];
	
	if(typeof getParameters['noOfLevels']=="undefined" || getParameters['noOfLevels'] != 1) 
	{
		parameterMissing = true;
		$('#container').html("<h2>&nbsp;&nbsp; Parameter noOfLevels is either missing or incorrect!</h2>");
	}
		
	else 
		this.noOfLevels=getParameters['noOfLevels'];
	
	
	if(typeof getParameters['language']=="undefined") 
		this.language="english"; 
	else 
		this.language=getParameters['language'];
	if(typeof getParameters['numberLanguage']=="undefined")
		this.numlanguage="english";
	else
		this.numlanguage=getParameters['numberLanguage'];
	calTime();
}

function calTime()
{
	tmp = setInterval(function(){levelWiseTimeTaken += 1},1000);
}

questionInteractive.prototype.generateQuestion = function() {
	var correctCount=0;
	var x;
	
	if(qCnt <= 6)
	{
		if(this.dArr[qCnt] == 2 || this.dArr[qCnt] == 5 )
			this.unit=10;
		else if(this.dArr[qCnt] == 4 || this.dArr[qCnt] == 20 || this.dArr[qCnt] == 25 || this.dArr[qCnt] == 50 )
			this.unit=100;
			
		levelsAttempted = "L1";
		if(qCnt > 3)
		{
			for(var c = 0; c < interactiveObj.resArr.length; c++)
			{
   				 if(interactiveObj.resArr[c] == 1)
       				 correctCount++;
			}
			if((correctCount/interactiveObj.resArr.length >= 0.8) || (interactiveObj.resArr[qCnt-1] == 1 && interactiveObj.resArr[qCnt-2] == 1 && interactiveObj.resArr[qCnt-3] == 1 && interactiveObj.resArr[qCnt-4] == 1))
			{
				completed = 1;
				levelWiseStatus = 1;
				
				x = extraParameters.lastIndexOf("|");
				extraParameters = extraParameters.substring(x,0);
				clearInterval(tmp);
				return;
			}
		}
		
		if(qCnt == 6)
		{
			x = extraParameters.lastIndexOf("|");
			extraParameters = extraParameters.substring(x,0);
			completed = 1;
			levelWiseStatus = 2;
//			alert('end '+qCnt);
			clearInterval(tmp);
			return;
		}
		else if(qCnt < 6)
		{
			extraParameters+=interactiveObj.nArr[qCnt]+"/"+interactiveObj.dArr[qCnt];
			var html = '';
			
			html+='<div class="no" id="no'+qCnt+'">';
			html+='<span id="no" class="fraction">';
				html+='<div id="n" class="frac numerator">' + changeLanguage(this.nArr[qCnt]+"",interactiveObj.numlanguage) + '</div>'; // n
				html+='<div id="d" class="frac">' +changeLanguage(this.dArr[qCnt]+"",interactiveObj.numlanguage)+'</div>';   // d
			html+='</span>';  // no
			html+='<span id="equal">&nbsp;&nbsp; = &nbsp;&nbsp;</span>';    // equal
			html+='<span id="ip"> <input type="text" id="ans'+qCnt+'" maxlength="5" class="ans" pattern="[0-9]*"></span>&nbsp;&nbsp;';  // ip ans
			html+='<span id="noHand'+qCnt+'" class="hand"><img src="../assets/handArrow.png" style="height:22px; width:40px;"></span>';
			html+='</div><br>';  // no1,2,3..
			
			html+='<div id="sub1'+qCnt+'" class="sub">';
				html+='<span id="no" class="fraction">';
					html+='<div class="frac numerator">' + changeLanguage(this.nArr[qCnt]+"",interactiveObj.numlanguage) + '</div>'; // n
					html+='<div class="frac">' +changeLanguage(this.dArr[qCnt]+"",interactiveObj.numlanguage)+'</div>';   // d
				html+='</span>';  // no
				html+='<span id="equal">&nbsp;&nbsp; = &nbsp;&nbsp;</span>';    // equal
				html+='<span id="no" class="fraction" style="margin-bottom:12px;">';
					html+='<div class="frac numerator" style="padding-bottom:5px;"><input type="text" id="sub11'+qCnt+'" maxlength="5" class="ans" style="width:50px; height:20px;" pattern="[0-9]*"></div>'; // n
					if(this.dArr[qCnt] == 2 || this.dArr[qCnt] == 5 )
						html+='<div class="frac">' +changeLanguage(10+"",interactiveObj.numlanguage)+'</div>';   // d
					else if(this.dArr[qCnt] == 4 || this.dArr[qCnt] == 20 || this.dArr[qCnt] == 25 || this.dArr[qCnt] == 50 )
						html+='<div class="frac">' +changeLanguage(100+"",interactiveObj.numlanguage)+'</div>';   // d
				html+='</span>';  // no
				html+='<span id="sub1Hand'+qCnt+'" class="hand"><img src="../assets/handArrow.png" style="height:22px; width:40px;"></span>';
			html+='</div><br>';  // sub1
			
			html+='<div id="sub2'+qCnt+'" class="sub" style="top:200px;">';
				html+='<span id="no" class="fraction">';
					html+='<div class="frac numerator">' + changeLanguage(this.nArr[qCnt]+"",interactiveObj.numlanguage) + '</div>'; // n
					html+='<div class="frac">' +changeLanguage(this.dArr[qCnt]+"",interactiveObj.numlanguage)+'</div>';   // d
				html+='</span>';  // no
				html+='<span id="equal">&nbsp;&nbsp; = &nbsp;&nbsp;</span>';    // equal
				html+='<span id="no" class="fraction">';
					html+='<div class="frac numerator">' + changeLanguage(this.nArr[qCnt]+"",interactiveObj.numlanguage) + '</div>'; // n
					html+='<div class="frac">' +changeLanguage(this.dArr[qCnt]+"",interactiveObj.numlanguage)+'</div>';   // d
				html+='</span>';  // no
				html+='<span>&nbsp;&nbsp; X &nbsp;&nbsp;</span>';    
				html+='<span id="no" class="fraction">';
					html+='<div class="frac numerator" style="padding-bottom:5px;"><input type="text" id="sub22'+qCnt+'" maxlength="5" class="ans" style="width:50px; height:20px;" pattern="[0-9]*"></div>'; // n
					html+='<div class="frac" style="padding-top:5px;"><input type="text" id="sub21'+qCnt+'" maxlength="5" class="ans" style="width:50px; height:20px;" pattern="[0-9]*"></div>';   // d
				html+='</span>';  // no
				html+='<span> = </span>';
				html+='<span id="no" class="fraction" style="margin-bottom:10px;">';
					html+='<div class="frac numerator" style="padding-bottom:5px;"><input type="text" id="sub23'+qCnt+'" maxlength="5" class="ans" style="width:50px; height:20px;" pattern="[0-9]*"></div>'; // n
					if(this.dArr[qCnt] == 2 || this.dArr[qCnt] == 5 )
						html+='<div  class="frac">' +changeLanguage(10+"",interactiveObj.numlanguage)+'</div>';   // d
					else if(this.dArr[qCnt] == 4 || this.dArr[qCnt] == 20 || this.dArr[qCnt] == 25 || this.dArr[qCnt] == 50 )
						html+='<div class="frac">' +changeLanguage(100+"",interactiveObj.numlanguage)+'</div>';   // d
				html+='</span>';  // no
				html+='<span id="sub2Hand'+qCnt+'" class="hand"><img src="../assets/handArrow.png" style="height:22px; width:40px;"></span>';
			html+='</div><br>'; // sub2
			$('#numbersDiv').append(html);
			$("#numbersDiv").animate({ scrollTop: ($("#numbersDiv").prop("scrollHeight") + 20) }, 3000);
//			$("#numbersDiv").attr({ scrollTop: ($("#numbersDiv").attr("scrollHeight")+20) });
			$('#ans'+qCnt).focus();		
			$('#sub1'+qCnt).css('display','none');
			$('#sub2'+qCnt).css('display','none');
			
			$("input").keypress(function(e) {
			   var a = [];
			   var k = e.which;
		    
				if($(this).attr("class")=="ans" && e.which == 13)
				{
					textFocus = $(this).attr('id');
					interactiveObj.checkAns();
				}
				for (x = 48; x < 58 ; x++)
				{
					a.push(x);
				}
				a.push(46);	
				if (!($.inArray(k,a)>=0) && e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39)
				{
					e.preventDefault();
				}
			});
		}
	}
}
questionInteractive.prototype.init = function() {
	var html = '';
	if(parameterMissing == true) return;

	html+='<div id="top">';
		html+=promptArr['message1'];
	html+='</div><br>';  // top
	
	html+='<div id="numbersDiv">';
	html+='</div>';  //numbersDiv
	
// ======================      PROMPTS		===========================
	
	html+='<div id="promptDiv" class="prompt">';
		html+='<div><img src="../assets/spark.png"></div>';
		html+='<div id="promptMessage"> </div>';
		html+='<div style="text-align:center;"><button type="button" id="promptDivButton" class="button">' + promptArr['message5'] + '</button></div>';
	html+='</div>';   // promptDiv
	
// =========================  PROMPTS - END  ============================
	
	$('#container').html(html);	
	
	containerResize();
	
	interactiveObj.generateQuestion();
	$("#promptDiv").draggable({ containment: "#container" });
	$("#promptDiv").css('visibility', 'hidden');
	$("#promptDiv").css('top',( ($("#promptDiv").scrollTop() - 320 ) + 'px'));
}

questionInteractive.prototype.showPrompt = function(message,width)
{
	$("#promptDiv").css('visibility', 'visible');	
	$('#promptMessage').html(message);
	$('#promptDiv').css('width',width+"px");
	$("#promptDivButton").focus();
}

questionInteractive.prototype.checkAns = function() 
{
	if($('#'+textFocus).val() == "")   // if textbox left blank
	{
//		alert(textFocus);
		interactiveObj.showPrompt(promptArr['message2'],320);
		$("#promptDivButton").click(function () {$("#promptDiv").css('visibility','hidden');$('#'+textFocus).focus();});
	}
	else
	{	
		this.val1 = this.unit / this.dArr[qCnt] * this.nArr[qCnt];
		this.val2 = this.unit / this.dArr[qCnt];
		this.ans  = this.ansArr[qCnt];

//	=============================	click Function	===================================		
		
		$("#promptDivButton").click(function () {
			
			$("#promptDiv").css('visibility','hidden');
			
			if($('#'+textFocus).val() == "")   // if textbox left blank
			{
				$("#promptDiv").css('visibility','hidden');
				$('#'+textFocus).focus();
			}
			else if(textFocus.indexOf("ans")  == 0)			// for ans
			{
				if(interactiveObj.ansArr[qCnt] == $('#ans'+qCnt).val())  // if ans is correct
				{
					if(attemptArr[qCnt] > 1)
						$('#noHand'+qCnt).css('visibility','hidden');
						
					qCnt+=1;
					interactiveObj.generateQuestion();
					$('#ans'+qCnt).focus();
				}
				else  //  if ans is incorrect
				{
					
					if(attemptArr[qCnt] == 1)    //   if ans is incorrect for 1st time
					{
						
						$('#ans'+qCnt).val('');
						$('#ans'+qCnt).css({'box-shadow' : '0 0','-webkit-box-shadow' : '0 0', '-moz-box-shadow' : '0 0', '-ms-box-shadow' : '0 0'});
						$('#ans'+qCnt).attr('disabled','true');
						$('#sub1'+qCnt).css('display','block');
						$('#sub1Hand'+qCnt).css('visibility','visible');
						$('#sub11'+qCnt).focus();
//						alert(qCnt);
//						console.log($('#sub11'+qCnt));
					}
					else if(attemptArr[qCnt] > 1)			//	if ans is incorrect for 2nd time
					{
						$('#noHand'+qCnt).css('visibility','hidden');
						attemptArr[qCnt] = 2;
						$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px blue', '-webkit-box-shadow' : '0 0 10px 1px blue', '-moz-box-shadow' : '0 0 10px 1px blue', '-ms-box-shadow' : '0 0 10px 1px blue'});
						$('#ans'+qCnt).val(replaceDynamicText(interactiveObj.ans, interactiveObj.numlanguage, 'interactiveObj'));
						$('#ans'+qCnt).attr('disabled','true');
						qCnt+=1;
						interactiveObj.generateQuestion();
						$('#ans'+qCnt).focus();
					}
				}
			}
			else if(textFocus.indexOf("sub11") == 0)			// for sub1
			{
				if($('#'+textFocus).val() != (this.val1)   )		// if incorrect
				{
					if(attemptArr[qCnt] == 1)				// for 1st time
					{
						$('#'+textFocus).css({'box-shadow' : '0 0','-webkit-box-shadow' : '0 0', '-moz-box-shadow' : '0 0', '-ms-box-shadow' : '0 0'});
						$('#'+textFocus).val("");
						$('#sub1Hand'+qCnt).css('visibility','hidden');
						textFocus = "sub21"+qCnt;
						$("#sub2"+qCnt).css('display','block');
						$('#sub2Hand'+qCnt).css('visibility','visible');
						$('#sub22'+qCnt).attr('disabled','true');
						$('#sub23'+qCnt).attr('disabled','true');
						$('#sub21'+qCnt).focus();		// problem
					}
					else if(attemptArr[qCnt] > 1)				//	for 2nd time
					{
						$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px blue', '-webkit-box-shadow' : '0 0 10px 1px blue', '-moz-box-shadow' : '0 0 10px 1px blue', '-ms-box-shadow' : '0 0 10px 1px blue'});
						$('#'+textFocus).val(replaceDynamicText(interactiveObj.val1, interactiveObj.numlanguage, 'interactiveObj'));
						$('#ans'+qCnt).removeAttr('disabled');
						$('#ans'+qCnt).focus();
						$('#sub1Hand'+qCnt).css('visibility','hidden');
						$('#noHand'+qCnt).css('visibility','visible');
					}
				}
			}
			else if(textFocus.indexOf("sub2") == 0)			//	for sub2
			{
				if(textFocus == "sub22"+qCnt)
				{
					if($('#'+textFocus).val() != interactiveObj.val2) 
					{
						$('#'+textFocus).val(replaceDynamicText(interactiveObj.val2, interactiveObj.numlanguage, 'interactiveObj'));
						$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px blue', '-webkit-box-shadow' : '0 0 10px 1px blue', '-moz-box-shadow' : '0 0 10px 1px blue','-ms-box-shadow' : '0 0 10px 1px blue'});
						$('#sub22'+qCnt).attr('disabled','true');
						$('#sub23'+qCnt).removeAttr('disabled');
						$('#sub23'+qCnt).focus();
					}
				}
				else if(textFocus == "sub23"+qCnt)					//	for sub23
				{
					if($('#'+textFocus).val() != interactiveObj.val1)		
					{
						attemptArr[qCnt]+=1;
						$('#'+textFocus).val(replaceDynamicText(interactiveObj.val1, interactiveObj.numlanguage, 'interactiveObj'));
						$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px blue', '-webkit-box-shadow' : '0 0 10px 1px blue', '-moz-box-shadow' : '0 0 10px 1px blue','-ms-box-shadow' : '0 0 10px 1px blue'});
						$('#sub2Hand'+qCnt).css('visibility','hidden');
						$('#sub1Hand'+qCnt).css('visibility','visible');
						$('#sub11'+qCnt).removeAttr('disabled');
						$('#sub11'+qCnt).focus();
					}
				}
			}
		});
		
//	===============================		click function - End	==============================
		
		if(textFocus.indexOf("ans")  == 0)
		{
			attemptArr[qCnt] += 1;
				
			if(interactiveObj.ansArr[qCnt] == $('#ans'+qCnt).val())   // if ans is correct
			{
				extraParameters+=" ; " + $('#ans'+qCnt).val() + " | ";
				
				if(attemptArr[qCnt] == 1)
					levelWiseScore += 10;
				else if(attemptArr[qCnt] > 1)
					levelWiseScore += 5;
					
				interactiveObj.resArr.push(1);
				interactiveObj.showPrompt(promptArr['message3'],125);
				$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px green', '-webkit-box-shadow' : '0 0 10px 1px green', '-moz-box-shadow' : '0 0 10px 1px green','-ms-box-shadow' : '0 0 10px 1px green'});
				$('#ans'+qCnt).attr('disabled','true');
			}
			else   // if ans is incorrect
			{
				
				this.frac1 = '<div class="fraction"><div class="frac numerator">'+ this.nArr[qCnt]+'</div><div class="frac">'+  this.dArr[qCnt] +'</div></div>';
				this.frac2 = '<div class="fraction"><div class="frac numerator">'+ this.val1 +'</div><div class="frac">'+ this.unit +'</div></div>';
				if(attemptArr[qCnt] > 1) // if ans is incorrect 2nd time
				{
					extraParameters+=" ; " + $('#ans'+qCnt).val() + " | ";
					interactiveObj.resArr.push(0);
					$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red','-ms-box-shadow' : '0 0 10px 1px red'});
					interactiveObj.showPrompt(replaceDynamicText(promptArr['message9'],this.numlanguage,'interactiveObj'),150);
				}
				else     //  if ans is incorrect 1st time
				{
					extraParameters+=" ; " + $('#ans'+qCnt).val() ;
					$('#ans'+qCnt).attr('disabled','true');
					$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red','-ms-box-shadow' : '0 0 10px 1px red'});
					interactiveObj.showPrompt(replaceDynamicText(promptArr['message4'],this.numlanguage,'interactiveObj'),300);
				}
			}
		}
		else if(textFocus.indexOf("sub11") == 0)  // checks for sub11
		{
			if($('#'+textFocus).val() == (this.val1)) // if sub1 is correct
			{
				
				$('#'+textFocus).attr('disabled','true');
				$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px green', '-webkit-box-shadow' : '0 0 10px 1px green', '-moz-box-shadow' : '0 0 10px 1px green','-ms-box-shadow' : '0 0 10px 1px green'});
				$('#sub1Hand'+qCnt).css('visibility','hidden');
				$("#ans"+qCnt).removeAttr('disabled');
				$('#noHand'+qCnt).css('visibility','visible');
				$("#ans"+qCnt).focus();
			}
			else   // if sub1 is incorrect
			{
				$('#'+textFocus).attr('disabled','true');
				$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red','-ms-box-shadow' : '0 0 10px 1px red'});
				
				if(attemptArr[qCnt] > 1) // if sub1 is incorrect 2nd time
				{
					interactiveObj.showPrompt(replaceDynamicText(promptArr['message8'],this.numlanguage,'interactiveObj'),280);
				}
				else   // if sub1 is incorrect for 1st time
				{
					interactiveObj.showPrompt(promptArr['message6'],250);
				}
			}
		}
		else if(textFocus.indexOf("sub2") == 0)   // checks for sub2 textboxs
		{
			if(textFocus == "sub21"+qCnt || textFocus == "sub22"+qCnt)  // for sub21 and sub22
			{
				if($('#'+textFocus).val() == this.val2)  // if value of sub21 and sub22 is correct
				{
					$('#'+textFocus).attr('disabled','true');
					$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px green', '-webkit-box-shadow' : '0 0 10px 1px green', '-moz-box-shadow' : '0 0 10px 1px green','-ms-box-shadow' : '0 0 10px 1px green'});
					if(textFocus == "sub21"+qCnt )
					{
						$('#sub22'+qCnt).removeAttr('disabled');
						$('#sub22'+qCnt).focus();
					}
					if(textFocus == "sub22"+qCnt )
					{
						$('#sub23'+qCnt).removeAttr('disabled');
						$('#sub23'+qCnt).focus();
					}
				}
				else   //  if value of sub21 and sub22 is incorrect
				{
					if(textFocus == "sub22"+qCnt ) // if textbox is sub22
					{
						this.frac= '<div class="fraction"><div class="frac numerator">'+ this.val2+'</div><div class="frac">'+  this.val2 +'</div></div>';
						$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red','-ms-box-shadow' : '0 0 10px 1px red'});
						$('#'+textFocus).attr('disabled','true');
						interactiveObj.showPrompt(replaceDynamicText(promptArr['message7'],this.numlanguage,'interactiveObj'),310);
					}
					else   //  if textbox is sub21
					{
						$('#'+textFocus).val(replaceDynamicText(interactiveObj.val2, this.numlanguage, 'interactiveObj'));
						$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px blue', '-webkit-box-shadow' : '0 0 10px 1px blue', '-moz-box-shadow' : '0 0 10px 1px blue','-ms-box-shadow' : '0 0 10px 1px blue'});
						$('#'+textFocus).attr('disabled','true');
						$('#sub22'+qCnt).removeAttr('disabled');
						$('#sub22'+qCnt).focus();
					}
				}
			}
			else  //  for sub23
			{
				if($('#'+textFocus).val() == this.val1) // if sub23 is correct
				{
					$('#'+textFocus).attr('disabled','true');
					$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px green', '-webkit-box-shadow' : '0 0 10px 1px green', '-moz-box-shadow' : '0 0 10px 1px green','-ms-box-shadow' : '0 0 10px 1px green'});
					$('#sub2Hand'+qCnt).css('visibility','hidden');
					$('#sub1Hand'+qCnt).css('visibility','visible');
					$('#sub11'+qCnt).removeAttr('disabled');
					$('#sub11'+qCnt).focus();
				}
				else  //  if sub23 is incorrect
				{
					$('#'+textFocus).attr('disabled','true');
					$('#'+textFocus).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red','-ms-box-shadow' : '0 0 10px 1px red'});
					interactiveObj.showPrompt(replaceDynamicText(this.nArr[qCnt],this.numlanguage,'interactiveObj') + " X " + replaceDynamicText(this.val2 , this.numlanguage, 'interactiveObj') + " = " + replaceDynamicText((this.val1),this.numlanguage,'interactiveObj' )+"<br><br>", 125 );
				}
			}
		}
	}
}