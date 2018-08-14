var interactiveObj;
var extraParameters="";
var extraParametersT1="Type1: ";
var extraParametersT2="Type2: ";
var correctResponse="";
var parameterMissing = false;
var scoreT1 = 0;
var scoreT2 = 0;
var completed = 0;
var qCnt = 0;
var attemptArr = new Array();
var tmp = 0;
var levelWiseTimeTaken=0;

var noOfLevels = 1;		
var levelWiseMaxScores = 0;
var lastLevelCleared = "";
var previousLevelLock = 0;
var levelsAttempted = "";
var levelWiseScore = 0;	
var levelWiseStatus = 0;
var numberLanguage;
var txtval;

function questionInteractive() {
	
	this.nArr		= new Array();
	this.dArr		= new Array();
	this.ansArr		= new Array();
	this.resArr		= new Array();
	this.frac		= '';
	
	for(var i=0 ; i<6 ; i++)
	{
		attemptArr[i] = 0;
		if(i%2 == 0)
		{
			this.n1= Math.floor((Math.random()*98)+1);
			if(this.nArr.indexOf(this.n2)!=-1)
			{
				this.n1++;
			}
			
			if(this.n1 > 0 && this.n1 < 10 )
				this.d1= 10;
			else
				this.d1= 100;
			this.ans  =  this.n1 / this.d1 ;
			this.nArr.push(this.n1);
			this.dArr.push(this.d1);
			this.ansArr.push(this.ans);
		}
		else
		{
			this.n2 		= Math.floor((Math.random()*9)+1);
			if(Math.floor(Math.random()*10)%2)
				this.n2 	= 10*Math.floor((Math.random()*9)+1);
			if(this.nArr.indexOf(this.n2)!=-1)
			{
				if(this.n2 % 10 == 0)
				{
					if(this.n2 != 90 && this.n2 == 10)
						this.n2 += 10;
					else if(this.n2 != 10)
						this.n2 -= 10;
				}
				else
				{
					if(this.n2 != 9 && this.n2 == 1)
						this.n2 += 1;
					else if(this.n2 != 1)
						this.n2 -= 1;
				}
			}
			if(this.n2==100)
				this.n2 	= 10;
			this.d2 = 100;
			this.ans  =  this.n2 / this.d2 ;
			this.nArr.push(this.n2);
			this.dArr.push(this.d2);
			this.ansArr.push(this.ans);
		}
	}
//	for(var i=0 ; i<6 ; i++)
//	{
//		this.ansArr[i] = this.ansArr[i].toString();
//	}
//	this.ans = this.ans.toString();

	noOfLevels = getParameters['noOfLevels'];
	
	if(typeof getParameters['noOfLevels']=="undefined" || getParameters['noOfLevels'] != 1) 
	{
		parameterMissing = true;
		$('#container').html("<h2>&nbsp;&nbsp; Parameter noOfLevels is either missing or incorrect!</h2>");
	}

	if(typeof getParameters['language']=="undefined") 
		this.language="english"; 
	else 
		this.language=getParameters['language'];
	if(typeof getParameters['numberLanguage']=="undefined")
	{
		this.numlanguage = "english";
		numberLanguage = "english";
	}
	else
	{
		this.numlanguage = getParameters['numberLanguage'];
		numberLanguage = getParameters['numberLanguage'];
	}
		
	calTime();
//	alert(this.nArr +' -> '+ this.dArr);
}
function calTime()
{
	tmp = setInterval(function(){levelWiseTimeTaken += 1},1000);
}
questionInteractive.prototype.generateQuestion = function() {
	var correctCount=0;
	var x;
//	var incorrectCount=0;
	
	if(qCnt <= 6)
	{
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
				extraParameters+=" ScoreT1: " + scoreT1 + "| ScoreT2: " + scoreT2 ;
				levelWiseScore = scoreT1 + scoreT2;
//				x = extraParameters.lastIndexOf("|");
//				extraParameters = extraParameters.substring(x,0);
				clearInterval(tmp);
//				alert(levelWiseTimeTaken);
				return;
			}
		}
		
		if(qCnt == 6)
		{
			extraParameters+=" ScoreT1: " + scoreT1 + "| ScoreT2: " + scoreT2 ;
//			x = extraParameters.lastIndexOf("|");
//			extraParameters = extraParameters.substring(x,0);
			completed = 1;
			levelWiseStatus = 2;
			levelWiseScore = scoreT1 + scoreT2;
			clearInterval(tmp);
		}
		else if(qCnt < 6)
		{
			if(qCnt % 2 == 0)
				extraParametersT1+=interactiveObj.nArr[qCnt]+"/"+interactiveObj.dArr[qCnt];
			else
				extraParametersT2+=interactiveObj.nArr[qCnt]+"/"+interactiveObj.dArr[qCnt];
				
			extraParameters = extraParametersT1 + extraParametersT2;
			
			var html = '';
			
			html+='<div class="no" id="no'+qCnt+'">';
			html+='<span id="no" class="fraction">';
				html+='<div id="n" class="frac numerator">' + changeLanguage(this.nArr[qCnt]+"",interactiveObj.numlanguage) + '</div>'; // n
				html+='<div id="d" class="frac">' +changeLanguage(this.dArr[qCnt]+"",interactiveObj.numlanguage)+'</div>';   // d
			html+='</span>';  // no
			html+='<span id="equal">&nbsp;&nbsp; = &nbsp;&nbsp;</span>';    // equal
			html+='<span id="ip"> <input type="text" id="ans'+qCnt+'" maxlength="5" class="ans" pattern="[0-9]*" ></span>';  // ip ans
			html+='</div><br>';  // no1
			
			$('#numbersDiv').append(html);
			
			$('#ans'+qCnt).focus();
			
			$("input").keypress(function(e) {
			   var a = [];
			   var k = e.which;
		    
				if($(this).attr("class")=="ans" && e.which == 13)
					interactiveObj.checkAns();
				
				for (x = 48; x < 58 ; x++)
				{
					a.push(x);
				}
				a.push(46);	
				if (!($.inArray(k,a)>=0) && e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39)
				{
					e.preventDefault();
				}

				setTimeout(function(){
					txtval = $("#ans"+qCnt).val();
					$("#ans"+qCnt).attr("value",replaceDynamicText(txtval,numberLanguage,""));
				},1);
			});
		}
	}
}
questionInteractive.prototype.init = function() {
	var html = '';
	if(parameterMissing == true) return;
	html+='<div id="numbersDiv">';
		html+='<div id="top">';
			html+=promptArr['message1'];
		html+='</div><br>';  // top
	html+='</div>';  //numbersDiv
	
// ======================      PROMPTS		===========================
	
	html+='<div id="fillDiv" class="prompt">';
		html+='<span><img src="../assets/spark.png" class="img"></span>';
		html+='<span>' + promptArr['message2'] + '</span>';
		html+='<div style="text-align:center;"><button type="button" id="fillDivButton" class="button">' + promptArr['message5'] + '</button></div>';
	html+='</div>';   // fillDiv
	
	html+='<div id="correctDiv" class="prompt">';
		html+='<span><img src="../assets/spark.png" class="img"></span>';
		html+='<span>' + promptArr['message3'] + ' </span><br><br>';
		html+='<div style="text-align:center;"><button type="button" id="correctDivButton" class="button">' + promptArr['message5'] + '</button></div>';
	html+='</div>'; // correctDiv
	
	html+='<div id="wrongDiv1" class="prompt">';
		html+='<span><img src="../assets/spark.png" class="img"></span>';
		this.frac= '<div class="fraction"><div class="frac numerator">'+interactiveObj.nArr[qCnt]+'</div><div class="frac">'+interactiveObj.dArr[qCnt] +'</div></div>';
		if(this.dArr[qCnt] == 100)
		{
			this.no = this.nArr[qCnt];
			this.unit = replaceDynamicText(promptArr["hundred"],interactiveObj.numlanguage,"interactiveObj");
			html+='<span id="m1"> '+ replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj') +' </span>';
		}
		else if(this.dArr[qCnt] == 10)
		{
			this.no = this.nArr[qCnt];
			this.unit = replaceDynamicText(promptArr["tenth"],interactiveObj.numlanguage,"interactiveObj");
			html+='<span id="m1"> '+ replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj') +' </span>';
		}
		html+='<div style="padding-left:35px;">' + promptArr['message4'] + '</div>';
		html+='<div style="text-align:center;"><button type="button" id="wrongDiv1Button" class="button">' + promptArr['message5'] + '</button></div>';
	html+='</div>';  // wrongDiv1
	
	html+='<div id="wrongDiv2" class="prompt">';
		html+='<span><img src="../assets/spark.png" class="img"></span>';
		this.frac= '<div class="fraction"><div class="frac numerator">'+interactiveObj.nArr[qCnt]+'</div><div class="frac">'+interactiveObj.dArr[qCnt] +'</div></div>';
		this.no = this.nArr[qCnt];
		this.unit = replaceDynamicText(promptArr["hundred"],interactiveObj.numlanguage,"interactiveObj");
		html+='<span id="m4"> '+ replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj') +' </span>';
		this.no = this.nArr[qCnt];
		this.frac = this.no / 10;
		this.unit = replaceDynamicText(promptArr["tenth"],interactiveObj.numlanguage,"interactiveObj");
		html+='<div style="padding-left:35px;" id="m5">'  + replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj') + '</div>';
		html+='<div style="padding-left:35px;">' + promptArr['message4'] + '</div>';
		html+='<div style="text-align:center;"><button type="button" id="wrongDiv2Button" class="button">' + promptArr['message5'] + '</button></div>';
	html+='</div>';  // wrongDiv2
	
	html+='<div id="expDiv" class="prompt">';
		html+='<span><img src="../assets/spark.png" class="img"></span>';
		this.frac= '<div class="fraction"><div class="frac numerator">'+interactiveObj.nArr[qCnt]+'</div><div class="frac">'+interactiveObj.dArr[qCnt] +'</div></div>';
		if(this.dArr[qCnt] == 100)
		{
			this.no = this.nArr[qCnt];
			this.unit = replaceDynamicText(promptArr["hundred"],interactiveObj.numlanguage,"interactiveObj");
			html+='<span id="m2"> '+ replaceDynamicText(promptArr['message12'],interactiveObj.numlanguage,'interactiveObj') +' </span>';
		}
		else if(this.dArr[qCnt] == 10)
		{
			this.no = this.nArr[qCnt];
			this.unit = replaceDynamicText(promptArr["tenth"],interactiveObj.numlanguage,"interactiveObj");
			html+='<span id="m2"> '+ replaceDynamicText(promptArr['message12'],interactiveObj.numlanguage,'interactiveObj') +' </span>';
		}
		html+='<div id="arrow">';
			html+='<img src="../assets/arrowDown.png" class="arrowImg">';
		html+='</div>';	//	arrow
		
		html+='<div  style="padding-left:20px;"><br>';
			html+='<table>';
				html+='<tr>';
					html+='<th> ' + promptArr['message6'] + ' </th>';
					html+='<th> ' + promptArr['message7'] + ' </th>';
					html+='<th> . </th>';
					html+='<th> ' + promptArr['message8'].replace(/#unit#/,capitaliseFirstLetter(replaceDynamicText(promptArr["tenth"],interactiveObj.numlanguage,"interactiveObj"))) + ' </th>';
					html+='<th> ' + promptArr['message8'].replace(/#unit#/,capitaliseFirstLetter(replaceDynamicText(promptArr["hundred"],interactiveObj.numlanguage,"interactiveObj"))) + ' </th>';
				html+='</tr>';
				html+='<tr style="height:38px;">';
					html+='<td>  </td>';
					html+='<td id="zero">  </td>';   // zero
					html+='<td id="dot">  </td>';    // dot
					html+='<td id="expNo1"> </td>';  // expNo1
					html+='<td id="expNo2"> </td>';  // expNo2
				html+='</tr>';
			html+='</table>';
			html+='<div id="finalExp"><br>';
			this.frac= '<div class="fraction"><div class="frac numerator">'+interactiveObj.nArr[qCnt]+'</div><div class="frac">'+interactiveObj.dArr[qCnt] +'</div></div>';
			interactiveObj.ans = this.ansArr[qCnt];
				html+='<span id="m3">' + replaceDynamicText(promptArr['message9'],interactiveObj.numlanguage,'interactiveObj') + '</span>';
				html+='<div style="text-align:center;"><button type="button" id="expDivButton" class="button">' + promptArr['message5'] + '</button></div>';
			html+='</div>';  // finalExps
		html+='</div>';
	html+='</div>';    // expDiv
	
//	==============================		PROMPTS - END		===========================
	
	$('#container').html(html);	
	containerResize();
	
	interactiveObj.generateQuestion();
	
	$("#fillDiv").draggable({ containment: "#container" });
	$("#correctDiv").draggable({ containment: "#container" });
	$("#wrongDiv1").draggable({ containment: "#container" });
	$("#wrongDiv2").draggable({ containment: "#container" });
	$("#expDiv").draggable({ containment: "#container" });
	
	$("#fillDiv").css('visibility', 'hidden');	
	$("#correctDiv").css('visibility', 'hidden');	
	$("#wrongDiv1").css('visibility', 'hidden');	
	$("#wrongDiv2").css('visibility', 'hidden');	
	$("#expDiv").css('visibility', 'hidden');	
	$("#correctDivButton").click(function () { $("#correctDiv").css('visibility','hidden'); interactiveObj.generateQuestion(); });
	$("#expDivButton").click(function () { $("#expDiv").css('visibility','hidden'); $('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px blue','-webkit-box-shadow' : '0 0 10px 1px blue','-moz-box-shadow' : '0 0 10px 1px blue','-ms-box-shadow' : '0 0 10px 1px blue'}); $('#ans'+qCnt).val(replaceDynamicText( interactiveObj.ansArr[qCnt] , interactiveObj.numlanguage , 'interactiveObj' ) ); $("#finalExp").css('visibility', 'hidden'); qCnt+=1; interactiveObj.generateQuestion(); } );
}


questionInteractive.prototype.checkAns = function() 
{
	if($('#ans'+qCnt).val() == "")   // if textbox left blank
	{
		$("#fillDiv").css('visibility', 'visible');	
		$("#fillDivButton").focus();
		$("#fillDivButton").click(function () {$("#fillDiv").css('visibility','hidden');$('#ans'+qCnt).focus();});
		
	}
	else
	{
		attemptArr[qCnt] += 1;
//		var t = $('#ans').val().replace(/(\.[0-9]*?)0+$/, "$1"); // removes zeros from decimal point e.g. 0.3000 -> 0.3
		if(interactiveObj.ansArr[qCnt] == $('#ans'+qCnt).val())  // if answer is correct
		{
			if(qCnt % 2 == 0)
				extraParametersT1+=" ; "+$('#ans'+qCnt).val()+" | ";
			else
				extraParametersT2+=" ; "+$('#ans'+qCnt).val()+" | ";
				
			extraParameters = extraParametersT1 + extraParametersT2;
			
			interactiveObj.resArr.push(1);
			if(attemptArr[qCnt]  == 1)
			{
				if(qCnt % 2 == 0)
					scoreT1 += 10;
				else
					scoreT2 += 10;
			}
			else if(attemptArr[qCnt]  == 2)
			{
				if(qCnt % 2 == 0)
					scoreT1 += 5;
				else
					scoreT2 += 5;
			}
			$("#correctDiv").css('visibility', 'visible');
			$("#correctDivButton").focus();
			$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px green', '-webkit-box-shadow' : '0 0 10px 1px green', '-moz-box-shadow' : '0 0 10px 1px green', '-ms-box-shadow' : '0 0 10px 1px green'});
			$('#ans'+qCnt).attr('readonly','true');
			qCnt+=1;
			$("#correctDivButton").click(function () {$('#ans'+qCnt).focus();});
		}
		else
		{
			if(qCnt % 2 == 0)
				extraParametersT1+=" ; "+$('#ans'+qCnt).val();
			else
				extraParametersT2+=" ; "+$('#ans'+qCnt).val();
				
			extraParameters = extraParametersT1 + extraParametersT2;
			
			interactiveObj.no = interactiveObj.nArr[qCnt];
			interactiveObj.ans = this.ansArr[qCnt];
			
			if(interactiveObj.dArr[qCnt] == 100)
				interactiveObj.unit = replaceDynamicText(promptArr["hundred"],interactiveObj.numlanguage,"interactiveObj");
			else if(interactiveObj.dArr[qCnt] == 10)
				interactiveObj.unit = replaceDynamicText(promptArr["tenth"],interactiveObj.numlanguage,"interactiveObj");
				
			interactiveObj.frac= '<div class="fraction"><div class="frac numerator">'+interactiveObj.nArr[qCnt]+'</div><div class="frac">'+interactiveObj.dArr[qCnt] +'</div></div>';
			$("#m1").html(replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj'));
			$("#m2").html(replaceDynamicText(promptArr['message12'],interactiveObj.numlanguage,'interactiveObj'));
			$("#m3").html(replaceDynamicText(promptArr['message9'],interactiveObj.numlanguage,'interactiveObj'));
			$("#m4").html(replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj'));
			interactiveObj.frac = interactiveObj.nArr[qCnt] /10;
			interactiveObj.unit = replaceDynamicText(promptArr["tenth"],interactiveObj.numlanguage,"interactiveObj");
			$("#m5").html(replaceDynamicText(promptArr['message11'],interactiveObj.numlanguage,'interactiveObj'));
			
			if(attemptArr[qCnt] > 1)
			{
				if(qCnt % 2 == 0)
					extraParametersT1+=" | ";
				else
					extraParametersT2+=" | ";
					
				extraParameters = extraParametersT1 + extraParametersT2 ;
					
				interactiveObj.resArr.push(0);
				$("#m1").html(replaceDynamicText(promptArr['message12'],interactiveObj.numlanguage,'interactiveObj'));
				$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red', 'box-shadow' : '-ms-0 0 10px 1px red'});
				$('#ans'+qCnt).attr('readonly','true');
				$('#ans'+qCnt).blur();
				$("#expDiv").css('visibility', 'visible');
				interactiveObj.animate();
				$("#expDivButton").click(function () {$('#ans'+qCnt).focus();});
			}
			else
			{
				$('#ans'+qCnt).css({'box-shadow' : '0 0 10px 1px red', '-webkit-box-shadow' : '0 0 10px 1px red', '-moz-box-shadow' : '0 0 10px 1px red', '-ms-box-shadow' : '0 0 10px 1px red'});
				$('#ans'+qCnt).attr('readonly','true');
			
				if(interactiveObj.nArr[qCnt] < 10 && interactiveObj.dArr[qCnt] == 100 && $('#ans'+qCnt).val() == interactiveObj.nArr[qCnt] / 10)
				{
					$("#wrongDiv2").css('visibility', 'visible');
					$("#wrongDiv2Button").focus();
					$("#wrongDiv2Button").click(function () { $("#wrongDiv2").css('visibility','hidden'); $('#ans'+qCnt).css({'box-shadow' : '0 0', '-webkit-box-shadow' : '0 0', '-moz-box-shadow' : '0 0', '-ms-box-shadow' : '0 0'}); $('#ans'+qCnt).val('');  $('#ans'+qCnt).focus(); $('#ans'+qCnt).removeAttr('readonly'); } );
				}
				else if(interactiveObj.dArr[qCnt] == 100 || interactiveObj.dArr[qCnt] == 10)
				{
					$("#wrongDiv1").css('visibility', 'visible');
					$("#wrongDiv1Button").focus();
					$("#wrongDiv1Button").click(function () { $("#wrongDiv1").css('visibility','hidden'); $('#ans'+qCnt).css({'box-shadow' : '0 0', '-webkit-box-shadow' : '0 0', '-moz-box-shadow' : '0 0', '-ms-box-shadow' : '0 0'}); $('#ans'+qCnt).val('');  $('#ans'+qCnt).removeAttr('readonly'); $('#ans'+qCnt).focus();  } );
				}
			}
		}
	}
	
}
questionInteractive.prototype.animate = function() {
	$("#finalExp").css('visibility', 'hidden');
	$('#arrow').css('visibility','hidden');
	$('#zero').text('');
	$('#dot').text('');
	$('#expNo1').text('');
	$('#expNo2').text('');
	interactiveObj.ansArr[qCnt] = interactiveObj.ansArr[qCnt].toString();
	
	$('#arrow').css('left','95px');
	
	setTimeout(function(){ $('#arrow').css('visibility','visible')},700);
	setTimeout(function(){ $('#zero').text('0')},1400);
	setTimeout(function(){ $('#arrow').css('visibility','hidden')},1600);
	setTimeout(function(){ $('#arrow').css('visibility','visible')},1900);
	setTimeout(function(){ $('#arrow').css('visibility','hidden')},2200);
	
	setTimeout(function(){$('#dot').text('')},2500);
	setTimeout(function(){$('#dot').text('.')},2800);
	setTimeout(function(){$('#dot').text('')},3100);
	setTimeout(function(){$('#dot').text('.')},3400);
	
	var t1 = this.ansArr[qCnt].charAt(2);
	var t2 = this.ansArr[qCnt].charAt(3);
	
	setTimeout(function(){ $('#arrow').css('left','203px')},3500);
	setTimeout(function(){ $('#arrow').css('visibility','visible')},3550);
	
	setTimeout(function(){$('#expNo1').text(replaceDynamicText( t1 +"" , interactiveObj.numlanguage, 'interactiveObj' ))},4000);
	setTimeout(function(){ $('#arrow').css('visibility','hidden')},4300);
	setTimeout(function(){ $('#arrow').css('visibility','visible')},4600);
	setTimeout(function(){ $('#arrow').css('visibility','hidden')},4900);
	
	if( this.ansArr[qCnt].charAt(3) != '')
	{
		setTimeout(function(){ $('#arrow').css('left','278px')},5000);
		setTimeout(function(){ $('#arrow').css('visibility','visible')},5300);
		setTimeout(function(){$('#expNo2').text(replaceDynamicText( t2 +"" , interactiveObj.numlanguage, 'interactiveObj' ))},5500);
		setTimeout(function(){$('#arrow').css('visibility','hidden') },5700);
		setTimeout(function(){$('#arrow').css('visibility','visible')},6000);
		setTimeout(function(){$('#arrow').css('visibility','hidden')},6300);
	}
	setTimeout(function(){$('#finalExp').css({'visibility':'visible'});},(6600));
	setTimeout(function(){$('#expDivButton').focus();},(6700));
	
}
function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
};

(function(){
            // Store a reference to the original remove method.
            var originalVal = jQuery.fn.val;

            // Define overriding method.
            jQuery.fn.val = function(){
            	if(arguments.length >= 1){
            		var value = originalVal.apply( this, arguments );
                	return value;
            	}
                if(numberLanguage == 'english'){
                	var value = originalVal.apply( this, arguments );
                	return value;
                }

                else if(numberLanguage == 'gujarati'){
                	var value = originalVal.apply( this, arguments );

                	for(var i = 0 ; i< gujratiNumerals.length; i++){
                		var exp = new RegExp(gujratiNumerals[i], 'g');
                		value= value.replace(exp, i);
                	}
                	return value;
                }
                else if(numberLanguage == 'hindi'){
	            	var value = originalVal.apply( this, arguments );

                	for(var i = 0 ; i< hindiNumerals.length; i++){
                		var exp = new RegExp(hindiNumerals[i], 'g');
                		value= value.replace(exp, i);
                	}
                		
                	return value;
                }
                else{
                	originalVal.apply( this, arguments );
                }
            }

            var originalAttr = jQuery.fn.attr;
            // Define overriding method.
            /*jQuery.fn.attr = function(){
            	if(arguments.length > 1){
            		var value = originalAttr.apply( this, arguments );
            		return value;
            	}
            	if(arguments[0] !== 'value'){
            		value = originalAttr.apply( this, arguments );
            		return value;
            	}

                if(numberLanguage == 'english')
                	originalAttr.apply( this, arguments );
                else if(numberLanguage == 'gujarati'){
                	var value = originalAttr.apply( this, arguments );

                	for(var i = 0 ; i< gujratiNumerals.length; i++){
                		var exp = new RegExp(gujratiNumerals[i], 'g');
                		value= value.replace(exp, i);
                	}
                	return value;
                }
                else if(numberLanguage == 'hindi'){
	            	var value = originalAttr.apply( this, arguments );

                	for(var i = 0 ; i< hindiNumerals.length; i++){
                		var exp = new RegExp(hindiNumerals[i], 'g');
                		value= value.replace(exp, i);
                	}

                	return value;
                		
                }
                else{
                	originalAttr.apply( this, arguments );
                }
            }*/
        })();