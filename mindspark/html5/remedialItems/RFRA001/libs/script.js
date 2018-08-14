var interactiveObj;
var multiplier=0;
var fracArr;
var no=0; //for storing a random number from 0 to 7 to fetch value from fraction Array
var parameterMissing = false;
var html='';
var wrongAttemptsBefore=0;//to store the number of wrong attempts entered in the first text box for level1
var wrongAttemptsAfter=0;//to store the number of wrong attempts entered in the first text box for level1
var numberLanguage;
var choc1='';
var no1=1;
var no2=2;
var nextLevelFlag=0;
var level1Time='';
var level2Time='';
var level3Time='';
var frac='';
var frac1='';
var frac3='';
var frac2='';
var frac4='';
var frac5='';
var callback='';
var callbackFlag=0;
var part1=0;
var globalCallback;
var level2Entered=0;
var fraction=0;
var n1=0;
var d1=0;
var n2=0;
var d=0;
var d2=0;
var choc1="";
var choc2="";
var fraction1="";
var fraction2="";
var level2Counter=0;
var n=0;
var partLength2;
var level2Flag=0;
var level3Flag=0;
var quesNo=0;
var level3Entered=0;
var userResponseArr=new Array();// to store the user responses for the first level // to check for the stopping criterion
var incorrectAttemptsL2A=0;
var incorrectAttemptsL2B=0;
var sameFractionAttempt=0;
var errorTypeB=0;
var userResponseArrL2=new Array();//to store the user responses for the second level // to check for the stopping criterion
var userResponseArrL3=new Array();//to store the user responses for the third level 
var randomArr=new Array();
var level2BCounter=1;
var quesString='';
var level3Counter=0;
var incorrectAttemptsL3=0;
var rArr;
var parentId;
var z=0;
var r=0;
var t=0;
var n3=0;
var number1=0;
var number2=0;
var ctx1;
var ctx2;
var rArrL1=new Array();
var m=0;
var m1=0,m2=0;
var timer=0;
var level1Score=0;
var level2Score=0;
var level3Score=0;
var level1Flag=0;
var level3Flag=0;
/********************************///IGRE Input-Output Parameters:
var levelsAttempted='';
var levelWiseScore='';
var levelWiseStatus='0';
var completed=0;
var noOfLevels;
var lastLevelCleared;
var levelWiseTimeTaken='0';
/********************************///extraParameters:
var extraParameters='';
var zeroFlag=0;
var txtval;
var numer1;
var denom1;
var divisor;
var appleDevice = false;
var androidDevice = false;

$(document).ready(function (e)
{
	if(navigator.userAgent.indexOf("Android") != -1)
	{
		androidDevice = true;
	}
	else if(window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
	{
		appleDevice = true;
	}

	$(document).live("touchstart",function(){
		$("#txt").blur();
	});

	if((appleDevice || androidDevice))
	{
	    $("input[type='text']").live("touchstart", function (e)
	    {
	    	/*document.activeElement.blur();*/
	        if (1)
	        {		
	            var id = $(this).attr('id');
	            /*alert("inside click")*/
	            $("#" + id).keypad({
	              layout: ['1234', '5678', '90', $.keypad.CLEAR + $.keypad.CLOSE + $.keypad.BACK + $.keypad.ENTER],
	              keypadClass: 'midnightKeypad',
	               prompt: 'Enter values using this keypad.', closeText: 'X', clearText: 'Clear', backText: '&#8592;', enterText: '&#8629;',
				    onKeypress: function (key, value, inst)
	                {						
	                    //console.log(key,value,inst);
	                    //jQuery.event.trigger({ type: 'keypress', which: value.charCodeAt(0) });
	                    //document.activeElement.blur();
	                    if(key)
	                    {
	                    	if(key.charCodeAt(0) == 13)
	                    	{
	                    		$(".keypad-popup").hide();
	                    	}
		                    var e = $.Event("keypress", { keyCode: key.charCodeAt(0) });
		                    $("#" + id).trigger(e);
		                }
	                },
	                beforeShow: function (div, inst)
	                {
	                   // $("#title").html(orient);
					   
	                 window.setTimeout(function ()
	                    {

	                       $(".keypad-popup").css({
	                           
	                            'width': '210px'
	                            /*'left' : '522px',
	                            'top'  : '384px'*/
	                           
	                        });
	                        $(".keypad-close").attr('id', 'closed');

	                   }, 1);
	                },
	                showAnim: ''
	            });

	        }
	    });
	}
});

function questionInteractive() 
{	
	this.lastlevelcleared;
	$('#container').css('border','2px solid black');
	$('#container').css('border-radius','10px');
	
	if(typeof getParameters['noOfLevels']=="undefined") 
		{ 
			$('#container').html('<h1 align="center">Parameter noOfLevels not set</h1>'); 
			parameterMissing=true; 
			return; 
		}
		else
		{
			if(getParameters['noOfLevels']!=3)
			{
				$('#container').html('<h1 align="center">Parameter noOfLevels not correctly set</h1>'); 
				parameterMissing=true; 
				return; 
			}
			else
			{
				noOfLevels=getParameters['noOfLevels'];
				parameterMissing=false;
			
			}
		}
		if(typeof getParameters['lastLevelCleared']=="undefined") 
		{ 
			$('#container').html('<h1 align="center">Parameter lastLevelCleared not set</h1>'); 
			parameterMissing=true; 
			return; 
		}
		else
		{
			lastLevelCleared=parseInt(getParameters['lastLevelCleared']);
			this.lastlevelcleared=parseInt(getParameters['lastLevelCleared'])+1;
			parameterMissing=false;
		}
		
	$('#container').css('border','0px solid black');
	$('#container').css('border-radius','0px');	
	
	this.levelWiseScoreArr = new Array(0,0,0);
	this.levelWiseStatusArr = new Array(0,0,0);
	this.levelWiseTimeTakenArr = new Array(0,0,0);
	this.extraParametersArr=new Array(0,0,0);
	this.levelsAttemptedArr = new Array();
	this.levelsAttemptedArr.push(this.lastlevelcleared);
	levelsAttempted = "L"+this.levelsAttemptedArr+"|";
	
	randomArr=new Array();
	for(i=0;i<5;i++)
	{
		randomArr[i]=i;
	}
	window.setInterval(function()
	{
		timer++;
	},1000);
	randomArr.sort(function ()
	{
		return Math.random() - 0.5;
	});
	
	fracArr=new Array("2/3","1/2","3/4","1/3","2/5","1/4","1/5","1/6");
	if(typeof getParameters['numberLanguage']=="undefined")
	{
		numberLanguage="english";
	}
	else
	{
		numberLanguage=getParameters['numberLanguage'];
	}
	
	/**************************************(live(#txt))*****************************************************************/
	$(".textbox1").live("keypress",function(e)
	{
		if(androidDevice || appleDevice)
		{
			document.activeElement.blur();
		}
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
			$("#midnightKeypad").hide();
			var value = $(this).val();
            if (value == "") 
			{
				$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
				
				$('#dialogueButton').css('margin-top',"20px");
                
				showPrompt(replaceDynamicText(promptArr['prompt20'],numberLanguage,'interactiveObj'),function()
				{
					alertForEmptyValue();
				});
				
            }
            else 
			{
				//alert("a)"+txtval+" b)"+multiplier+" c)"+fracArr[no].substring(0,1));
				if(txtval==multiplier*fracArr[no].substring(0,1))
				{
					$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
					if(wrongAttemptsBefore==2)
					{
						$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
					}
					extraParameters+='1);';
				
					$(".textbox1").attr("readOnly","true");
					$(".textbox1").css("border","1px solid black");
					$(".textbox1").addClass('greenBorder');
					
					showPrompt(promptArr['prompt1'],function()
					{
						correct();
					});
				}
				else
				{
					switch(wrongAttemptsBefore)
					{
						case 0:
						{
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							
							extraParameters+='0,';
							wrongAttemptsBefore++;
							$(".textbox1").attr("disabled","disabled");
							$('.textbox1').addClass('redBorder');
							
							frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';
							frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText($("#txt").val(),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(multiplier*fracArr[no].substring(02),numberLanguage,'interactiveObj')+'</div></div>';
							
							showPrompt(replaceDynamicText(promptArr['prompt2'],numberLanguage,'interactiveObj'),function()
							{
								incorrect1();
							});
							break;
						}
						case 1:
						{
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							
							extraParameters+='0,';
							wrongAttemptsBefore++;
							$(".textbox1").attr("disabled","disabled");
							$('.textbox1').addClass('redBorder');
							
							showPrompt(replaceDynamicText(promptArr['prompt3'],numberLanguage,'interactiveObj'),function()
							{
								incorrect2();
							});
							$("#txt").blur();
							break;
						}
						case 2:
						{
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							
							extraParameters+='0);';
							$(".textbox1").attr("disabled","true");
							$(".textbox1").addClass('redBorder');
							n1=fracArr[no].substring(0,1);
							n2=multiplier*fracArr[no].substring(0,1);
							d1=fracArr[no].substring(2);
							d2=multiplier*fracArr[no].substring(2);
							
							fraction1='<div class="fraction" style="width:25px;"><div class="frac numerator">'+replaceDynamicText(fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';
							fraction2='<div class="fraction" style="width:25px;"><div class="frac numerator">'+replaceDynamicText(multiplier*fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(multiplier*fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';
							
							frac1='<div class="fraction" style="width:25px;"><div class="frac numerator">'+replaceDynamicText(fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';
							frac2='<div class="fraction" style="width:25px;"><div class="frac numerator">'+replaceDynamicText(multiplier*fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(multiplier*fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';;
							
							/*****************/
							choc1='<p id="c1" style="margin-top:10px;display:inline-block;"><canvas id="choc1" height="25px" width="240px" style="background:#B05F3C;border:1px solid black;"></canvas></p>'; 
							choc2='<span id="c2"><canvas id="choc2" height="25px" width="240px" style="margin-top:15px;background:#B05F3C;border:1px solid black;"></canvas></span>'; 
							/*****************/ 
							$('.promptText').css('height','230px');
							$('.promptText').css('width','630px');
							$('.promptContainer').css("width","700px");
							$('.promptContainer').css("height","280px");
							$('.promptContainer').css("z-index","2");
							$('.promptContainer').css("margin-left","-100px");
							$('.promptContainer').css("margin-top","40px");
							
							$("#dialogueButton").attr('disabled','true');
							showPrompt('<div id="row1">'+replaceDynamicText(promptArr['prompt13'],numberLanguage,'interactiveObj')+'</div>',function()
							{
								incorrect5();
							});
							
							window.setTimeout(function()
							{
								$('.promptText').append('<div>'+replaceDynamicText(promptArr['prompt14'],numberLanguage,'interactiveObj')+'</div>');
								var c1=document.getElementById('choc1');
								var ctx1=c1.getContext('2d');
							
								var c2=document.getElementById('choc2');
								var ctx2=c2.getContext('2d');
								var partLength1=240/fracArr[no].substring(2);
							//ctx.fillRect(0,0,120/fracArr[no].substring(2),25);
								for(i=1;i<=parseInt(fracArr[no].substring(2));i++)
								{
									ctx1.beginPath();
									ctx1.moveTo(i*partLength1,0);
									ctx1.lineTo(i*partLength1,65);
									ctx1.stroke();
									ctx1.closePath();
								}
								
								var parts=parseInt(fracArr[no].substring(2))*multiplier;						
								partLength2=240/parts;
								 
								for(i=1;i<=parseInt(fracArr[no].substring(2))*multiplier;i++)
								{
									ctx2.beginPath();
									ctx2.moveTo(i*partLength2,0);
									ctx2.lineTo(i*partLength2,35);
									ctx2.stroke();
									ctx2.closePath();
								}
								
								var a=0.005;
								var temp1=window.setInterval(function()
								{
									ctx1.beginPath();
									ctx1.moveTo(0,0);
									ctx1.fillStyle="rgba(255,255,255,"+a+")";
									ctx1.fillRect(0,0,parseInt(fracArr[no].substring(0,1))*partLength1,35);
									ctx1.fill();
									ctx1.closePath();
									a+=0.005;
									if(a>=0.13)
									{
										window.clearInterval(temp1);
									}
								},100);
								
								var a=0.005;
								var temp2=window.setInterval(function()
								{
									ctx2.beginPath();
									ctx2.moveTo(0,0);
									ctx2.fillStyle="rgba(255,255,255,"+a+")";
									ctx2.fillRect(0,0,parseInt(fracArr[no].substring(0,1))*partLength1,35);
									ctx2.fill();
									ctx2.closePath();
									a+=0.005;
									if(a>=0.13)
									{
										window.clearInterval(temp2);
									}
								},100);
								
								$("#f1").css('display','inline-block');
								$("#f2").css('display','inline-block');
								$("#f1").css('padding-bottom','10px');
							},1000);
							window.setTimeout(function()
							{
								$('.promptText').append(replaceDynamicText(promptArr['prompt15'],numberLanguage,'interactiveObj'));
							},2000);
							window.setTimeout(function()
							{
								$("#dialogueButton").removeAttr('disabled').focus();
							},2500);
							break;
						}
					}
				}
			}
			return false;
		}
		else 
		{
            if(($(this).val().length > 2 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
			{
				return false;
            }
        }
        setTimeout(function(){  //keep interval >= 10 for mini iPad issue (mantis-10199)
        	txtval = $(".textbox1").val();
			$(".textbox1").attr("value",replaceDynamicText(txtval,numberLanguage,'interactiveObj'));
        	/*alert(txtval);*/
        },10);
        
    });

	   
	/*********************************************/
	$(".ans").live("keypress",function(e)
	{
		if(androidDevice || appleDevice)
		{
			document.activeElement.blur();
		}
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
            var value = $(this).val();
			$('.promptContainer').css("margin-left","0px");
			$('.promptContainer').css("margin-top","0px");
							
            if (value == "") 
			{
				$('.promptContainer').css({'position':'absolute','left':'140px','top':'37px'});
              
                showPrompt(replaceDynamicText(promptArr['prompt20'],numberLanguage,'interactiveObj'),function()
				{
					alertForEmptyValue();
				});
            }
            else 
			{
				if(txtval==multiplier*fracArr[no].substring(0,1))
				{	
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'375px'});
							
							$(".ans").attr("readOnly","true");
							$(".ans").css("border","1px solid black");
							$('.ans').addClass('greenBorder');
						showPrompt(replaceDynamicText(promptArr['prompt16'],numberLanguage,'interactiveObj'),function()
						{
							correct4();
						});
				}
				else
				{
					switch(wrongAttemptsAfter)
					{
						case 0:
						{
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'375px'});
							wrongAttemptsAfter++;
							$(".ans").attr("disabled","disabled");
							$(".ans").css("border","1px solid black");
							$(".ans").blur();
							$('.ans').addClass('redBorder');
							
							showPrompt(replaceDynamicText(promptArr['prompt11'],numberLanguage,'interactiveObj'),function()
							{
								incorrect3();
							});
							break;
						}
						case 1:
						{
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'375px'});
							$("#ans").focus();
							$(".ans").attr("readOnly","true");
							$(".ans").css("border","1px solid black");
							$(".ans").addClass('redBorder');
							part1=fracArr[no].substring(0,1)*multiplier;
							fraction='<div class="fraction"><div class="frac numerator">'+fracArr[no].substring(0,1)+'</div><div class="frac">'+fracArr[no].substring(2)+'</div></div>';
							
							showPrompt(replaceDynamicText(promptArr['prompt12'],numberLanguage,'interactiveObj'),function()
							{
								incorrect4();
							});
							break;
						}
					}
				}
			}
			return false;
		}
		else 
		{
            if(($(this).val().length > 2 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
		 	{
                return false;
            }
            setTimeout(function(){
        		txtval = $(".ans").val();
				$(".ans").attr("value",replaceDynamicText(txtval,numberLanguage,'interactiveObj'));
        	},10);
        }
    });
	/*********************************************/
	$("input:radio[name=optionValues]").live("click", function()
	{

		var id = $(this).attr("id");
		if(id=="opt1"+level3Counter)
		{
			if(level3Counter==3)
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'170px'});
			else
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'335px'});
			
			extraParameters+='1);';
			parentId=$("#"+id).parent('div').attr('id');

			$("#"+parentId).addClass('greenBorder');
			
			if(incorrectAttemptsL3==0)
			{
				level3Score++;
				userResponseArrL3.push('true');
			}
			$('.optionButtons'+level3Counter).attr('disabled',true);
			
			showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
			{
				correctResponseL3();
			});
		}
	
		if(id=="opt4"+level3Counter)
		{
			if(level3Counter==3)
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'170px'});
			else
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'335px'});
			switch(incorrectAttemptsL3)
			{
				case 0:
				{	
					extraParameters+='0,';
					userResponseArrL3.push('false');
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1+number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2+number1,numberLanguage,'interactiveObj')+'</div></div>';
					
					incorrectAttemptsL3++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					
					showPrompt(replaceDynamicText(promptArr['prompt33'],numberLanguage,'interactiveObj'),function()
					{
						incorrectResponseL3();
					});
					break;
				}
				case 1:
				{
					extraParameters+='0,';
					frac='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1+number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2+number1,numberLanguage,'interactiveObj')+'</div></div>';
					
					incorrectAttemptsL3++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					$('.optionButtons'+level3Counter).attr('disabled',true);
					
					showPrompt(replaceDynamicText(promptArr['prompt32'],numberLanguage,'interactiveObj'),function()
					{
						incorrectResponseL3();
					});
					break;
				}
				case 2:
				{
					extraParameters+='0);';
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(2*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(2*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac3='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(3*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(3*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac4='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(4*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(4*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac5='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
					frac6='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac7='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
					frac8='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1+'&#215;'+number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2+'&#215;'+number1,numberLanguage,'interactiveObj')+'</div></div>';
					
					$('.promptContainer').css('margin-left','-100px');
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					$('.optionButtons'+level3Counter).attr('disabled',true);
					
					showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
					{
						lastIncorrectResponseL3();
					});
					break;
				}
			}
		}
	
		if(id=="opt2"+level3Counter)
		{
			if(level3Counter==3)
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'170px'});
			else
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'335px'});
			switch(incorrectAttemptsL3)
			{
				case 0:
				{	
					extraParameters+='0,';
					userResponseArrL3.push('false');
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText((no1-1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText((no2),numberLanguage,'interactiveObj')+'</div></div>';
					
					incorrectAttemptsL3++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt33'],numberLanguage,'interactiveObj'),function()
					{
						incorrectResponseL3();
					});
					break;
				}
				case 1:
				{
					extraParameters+='0,';
					frac='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					incorrectAttemptsL3++;
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					$('.optionButtons'+level3Counter).attr('disabled',true);
					
					showPrompt(replaceDynamicText(promptArr['prompt32'],numberLanguage,'interactiveObj'),function()
					{
						incorrectResponseL3();
					});
					break;
				}
				case 2:
				{
					extraParameters+='0);';
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(2*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(2*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac3='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(3*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(3*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac4='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(4*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(4*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac5='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
					frac6='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac7='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
					frac8='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1+'&#215;'+number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2+'&#215;'+number1,numberLanguage,'interactiveObj')+'</div></div>';
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					$('.optionButtons'+level3Counter).attr('disabled',true);
					
					showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
					{
						lastIncorrectResponseL3();
					});
					break;
				}
			}
		}
		
		if(id=="opt3"+level3Counter)
		{
			if(level3Counter==3)
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'170px'});
			else
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'335px'});
			
			switch(incorrectAttemptsL3)
			{
				case 0:
				{
					extraParameters+='0,';
					userResponseArrL3.push('false');
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(m1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(m2,numberLanguage,'interactiveObj')+'</div></div>';
					
					incorrectAttemptsL3++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					$('.optionButtons'+level3Counter).attr('disabled',true);
					
					showPrompt(replaceDynamicText(promptArr['prompt33'],numberLanguage,'interactiveObj'),function()
					{
						incorrectResponseL3();
					});
					break;	
				}
				case 1:
				{
					extraParameters+='0,';
					incorrectAttemptsL3++;
					
					
					frac='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					
					if(m==0)
					{
						if(level3Counter==3)
							$('.promptContainer').css('margin-top','0px');
						n=no1;
						n1=r;
						n2=r*no1;
						n3=no2;
						$('.optionButtons'+level3Counter).attr('disabled',true);
						
						showPrompt(replaceDynamicText(promptArr['prompt35'],numberLanguage,'interactiveObj'),function()
						{
							incorrectResponseL3();
						});
					}
					else
					{
						n=no2;
						n1=r;
						n2=r*no2;
						n3=no1;
						$('.optionButtons'+level3Counter).attr('disabled',true);
						
						showPrompt(replaceDynamicText(promptArr['prompt34'],numberLanguage,'interactiveObj'),function()
						{
							incorrectResponseL3();
						});
					}
					break;
				}
				case 2:
				{
					extraParameters+='0);';
					frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(2*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(2*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac3='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(3*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(3*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac4='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(4*no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(4*no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac5='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
					frac6='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
					frac7='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
					frac8='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(no1+'&#215;'+number1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(no2+'&#215;'+number1,numberLanguage,'interactiveObj')+'</div></div>';
					
					parentId=$("#"+id).parent('div').attr('id');
					
					$("#"+parentId).addClass('redBorder');

					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
					{
						lastIncorrectResponseL3();
					});
					break;
				}
			}
		}
    });
	/*********************************************/
	//live method for .txt class textboxes, level 2
	
	/**********************************************/
	$(".txt1").live("keypress",function(e)
	{
		if(androidDevice || appleDevice)
		{
			document.activeElement.blur();
		}
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
            var value = $(this).val();
            if (value == "") 
			{
				$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
				
                showPrompt(replaceDynamicText(promptArr['prompt20'],numberLanguage,'interactiveObj'),function()
				{
					alertForEmptyValue();
				});
            }
            else 
			{
				if($("#n"+level2Counter).val()==""||$("#d"+level2Counter).val()=="")
				{
					if($(this).attr("id").substr(0,1)=='d'&&$("#n"+level2Counter).val()=="")
					{
						if($(this).attr("id").substr(0,1)=='d'&&$("#d"+level2Counter).val()==0)
						{
							$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
							showPrompt(replaceDynamicText(promptArr['prompt36'],numberLanguage,'interactiveObj'),function()
							{
								zeroValue();
							});
							$("#d"+level2Counter).val('');
						}
						else
						{
							$("#n"+level2Counter).focus();
						}
					}
					if($(this).attr("id").substr(0,1)=='n'&&$("#d"+level2Counter).val()=="")
					{
						if($(this).attr("id").substr(0,1)=='n'&&$("#n"+level2Counter).val()==0)
						{	
							$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
							showPrompt(replaceDynamicText(promptArr['prompt37'],numberLanguage,'interactiveObj'),function()
							{
								zeroValue();
							});
							$("#n"+level2Counter).val('');
						}
						else
						{
							$("#d"+level2Counter).focus();
						}
					}
				}
				else
				{
					if($("#d"+level2Counter).val() % quesString[1]==0)
					{
						if(  $("#n"+level2Counter).val() %  ($("#d"+level2Counter).val()/quesString[1]) ==0 )
						{
							divisor=$("#d"+level2Counter).val()/quesString[1];
							if(($("#n"+level2Counter).val()==quesString[0])&&($("#d"+level2Counter).val()==quesString[1]))
							{
								if(sameFractionAttempt==0)
								{
									userResponseArrL2.push('false');
								}
								//cases and prompt structure if the user enters the SAME FRACTION as the answer
								switch(sameFractionAttempt)
								{								
									case 0:
									{
										sameFractionAttempt++;
										
										$("#n"+level2Counter).attr("readOnly","true");
										$("#d"+level2Counter).attr("readOnly","true");
										
										$("#n"+level2Counter).addClass('redBorder');
										$("#d"+level2Counter).addClass('redBorder');
										
										frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
										
										$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
										showPrompt(replaceDynamicText(promptArr['prompt25'],numberLanguage,'interactiveObj'),function()
										{
											level2Prompt2B();
										});
										break;
									}
									case 1:
									{
										sameFractionAttempt++;
										
										$("#n"+level2Counter).attr("readOnly","true");
										$("#d"+level2Counter).attr("readOnly","true");
										
										$("#n"+level2Counter).addClass('redBorder');
										$("#d"+level2Counter).addClass('redBorder');
										
										frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
										frac2='<div class="fraction" style="width:"><div class="frac numerator" style="width:40px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+" x 2"+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+" x 2"+'</div></div>';
										
										$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
										showPrompt(replaceDynamicText(promptArr['prompt26'],numberLanguage,'interactiveObj'),function()
										{
											level2Prompt2B();
										});
										break;
									}
									case 2:
									{
										sameFractionAttempt++;
										
										$("#n"+level2Counter).attr("readOnly","true");
										$("#d"+level2Counter).attr("readOnly","true");

										
										$("#n"+level2Counter).addClass('redBorder');
										$("#d"+level2Counter).addClass('redBorder');
										
										frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
										frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
										frac2='<div class="fraction"><div class="frac numerator" style="width:35px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'x2'+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'x2'+'</div></div>';
										frac3='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
										frac4='<div class="fraction"><div class="frac numerator" style="width:40px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+" x 2"+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+" x 2"+'</div></div>';
										frac5='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
										
										$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
										showPrompt(replaceDynamicText(promptArr['prompt27'],numberLanguage,'interactiveObj'),function()
										{
											level2Prompt3B();
										});
										break;
									}
								}
							}
							else
							{
								if($('#n'+level2Counter).val()/divisor==quesString[0])
								{
									extraParameters+='1);';
									if(sameFractionAttempt==0&&errorTypeB==0&&incorrectAttemptsL2A==0&&incorrectAttemptsL2B==0)
									{
										userResponseArrL2.push('true');
										level2Score++;
									}
									$("#n"+level2Counter).css("border","1px solid black");
									$("#n"+level2Counter).addClass('greenBorder');
									
									$("#d"+level2Counter).css("border","1px solid black");
									$("#d"+level2Counter).addClass('greenBorder');
									
									$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
									showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
									{
										level2CorrectAttempt();
									});
								}
								else
								{
									if(errorTypeB==0)
									{
										userResponseArrL2.push('false');
										extraParameters+='0,';
									}
									switch(errorTypeB)
									{
									//cases and prompt structure if the user enters same figure as denominator and a random number for numerator (e.g. 1/5::2/5)
										case 0:
										{
											errorTypeB++;
											
											frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]+"&#215;2",numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]+"&#215;2",numberLanguage,'interactiveObj')+'</div></div>';
											frac3='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText("?",numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
											
											$("#n"+level2Counter).attr("readOnly","true");
											$("#d"+level2Counter).attr("readOnly","true");
											
											$("#n"+level2Counter).addClass('redBorder');
											$("#d"+level2Counter).addClass('redBorder');
											n=$("#d"+level2Counter).val()/quesString[1];
											d=quesString[1];
											
											$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
											if($('#n'+level2Counter).val()==0)
											{
												errorTypeB=0;
												showPrompt(replaceDynamicText(promptArr['prompt37'],numberLanguage,'interactiveObj'),function()
												{
													zeroValue();
												});
											}
											else if($("#n"+level2Counter).val()==1 && $("#d"+level2Counter).val()==quesString[1])
											{
												showPrompt(replaceDynamicText(promptArr['prompt38'],numberLanguage,'interactiveObj'),function()
												{
													level2Prompt1B();
												});
											}
											else
											{
												showPrompt(replaceDynamicText(promptArr['prompt18'],numberLanguage,'interactiveObj'),function()
												{
													level2Prompt1B();
												});
											}
											break;
										}
										
										case 1:
										{
											extraParameters+='0,';
											frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+"x"+2+'</div><div class="frac" >'+replaceDynamicText(quesString[1]+"x"+2,numberLanguage,'interactiveObj')+'</div></div>';
											frac3='<div class="fraction"><div class="frac numerator" >?</div><div class="frac" >'+replaceDynamicText(quesString[1]*n,numberLanguage,'interactiveObj')+'</div></div>';
											
											errorTypeB++;
											
											$("#n"+level2Counter).attr("readOnly","true");
											$("#d"+level2Counter).attr("readOnly","true");
											
											$("#n"+level2Counter).addClass('redBorder');
											$("#d"+level2Counter).addClass('redBorder');
											
											//$('.promptContainer').css('margin-top','90px');
											n=$("#d"+level2Counter).val()/quesString[1];
											d=quesString[1];
											
											$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
											showPrompt(replaceDynamicText(promptArr['prompt19'],numberLanguage,'interactiveObj'),function()
											{
												level2Prompt2B();
											});
											break;
										}
										case 2:
										{
											extraParameters+='0);';
											var randomArr=new Array(3,4,5,10);
											var m=randomArr[Math.floor(Math.random() * (3 - 0 )) + 0];
										
											frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]+"&#215;"+2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]+"&#215;"+2,numberLanguage,'interactiveObj')+'</div></div>';
											frac3='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
											frac4='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
											frac5='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											frac6='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*m,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*m,numberLanguage,'interactiveObj')+'</div></div>';
											frac7='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
											
											errorTypeB++;
											
											$("#n"+level2Counter).attr("readOnly","true");
											$("#d"+level2Counter).attr("readOnly","true");
											
											$("#n"+level2Counter).addClass('redBorder');
											$("#d"+level2Counter).addClass('redBorder');
											
											//$('.promptContainer').css('margin-top','90px');
											n=$("#d"+level2Counter).val()/quesString[1];
											d=quesString[1];
											
											$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
											showPrompt(replaceDynamicText(promptArr['prompt21'],numberLanguage,'interactiveObj'),function()
											{
												level2Prompt3B();
											});
											break;
										}
									}
								}
							}
						}
						else
						{
							//cases and prompt structure for error type A, i.e. denominator is a multiple but numerator is a random number (e.g. 1/3::1/9)
							if(incorrectAttemptsL2A==0)
							{
								userResponseArrL2.push('false');
								extraParameters+='0,';
							}
							switch(incorrectAttemptsL2A)
							{
								case 0:
								{
									incorrectAttemptsL2A++;
									
									$("#n"+level2Counter).attr("readOnly","true");
									$("#d"+level2Counter).attr("readOnly","true");
									
									$("#n"+level2Counter).addClass('redBorder');
									$("#d"+level2Counter).addClass('redBorder');
									
									//$('.promptContainer').css('margin-top','90px');
									n=$("#d"+level2Counter).val()/quesString[1];
									d=quesString[1];
									
									$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
									if($("#d"+(level2Counter)).val()==0)
									{
										incorrectAttemptsL2A=0;
										showPrompt(replaceDynamicText(promptArr['prompt36'],numberLanguage,'interactiveObj'),function()
										{
											zeroValue();
										});
									}
									else
									{
										showPrompt(replaceDynamicText(promptArr['prompt18'],numberLanguage,'interactiveObj'),function()
										{
											level2Prompt1A();
										});
									}
									break;
								}
								case 1:
								{
									extraParameters+='0,';
									
									incorrectAttemptsL2A++;
									
									$("#n"+level2Counter).attr("readOnly","true");
									$("#d"+level2Counter).attr("readOnly","true");
									
									$("#n"+level2Counter).addClass('redBorder');
									$("#d"+level2Counter).addClass('redBorder');
									
									n=$("#d"+level2Counter).val()/quesString[1];
									d=quesString[1];
									
									frac1='<div class="fraction"><div class="frac numerator" >'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
									frac2='<div class="fraction"><div class="frac numerator" >'+replaceDynamicText(n+"&#215;"+quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(n+"&#215;"+quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
									frac3='<div class="fraction"><div class="frac numerator" >?</div><div class="frac" >'+replaceDynamicText(quesString[1]*n,numberLanguage,'interactiveObj')+'</div></div>';
									
									$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
									showPrompt(replaceDynamicText(promptArr['prompt19'],numberLanguage,'interactiveObj'),function()
									{
										level2Prompt2A();
									});
									break;
								}
								case 2:
								{
									extraParameters+='0);';
									
									incorrectAttemptsL2A++;
									
									$("#n"+level2Counter).attr("readOnly","true");
									$("#d"+level2Counter).attr("readOnly","true");
									
									$("#n"+level2Counter).addClass('redBorder');
									$("#d"+level2Counter).addClass('redBorder');
															
									n=$("#d"+level2Counter).val()/quesString[1];
									
									frac='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(quesString[0]*n,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*n,numberLanguage,'interactiveObj')+'</div></div>';
									frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
									frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(n+"&#215;"+quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(n+"&#215;"+quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
									frac3='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(quesString[0]*n,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*n,numberLanguage,'interactiveObj')+'</div></div>';
									frac4='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(quesString[0]*n,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*n,numberLanguage,'interactiveObj')+'</div></div>';
									frac5='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
									
									$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
									showPrompt(replaceDynamicText(promptArr['prompt21'],numberLanguage,'interactiveObj'),function()
									{
										level2Prompt3A();
									});
									break;
								}
							}
						}
					}
					else
					{
					if(incorrectAttemptsL2B==0)
					{
						extraParameters+='0,';
						userResponseArrL2.push('false');
					}
						switch(incorrectAttemptsL2B)
						{
							case 0:
							{
								incorrectAttemptsL2B++;
								$(".txt1").attr("readOnly","true");
								
								$('#n'+level2Counter).css('border','1px solid black');
								$('#n'+level2Counter).addClass('redBorder');
								
								$('#d'+level2Counter).css('border','1px solid black');
								$('#d'+level2Counter).addClass('redBorder');
								
								//$('.promptContainer').css('margin-top','90px');
								n=$("#d"+level2Counter).val()/quesString[1];
								d=quesString[1];
								
								$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
								showPrompt(replaceDynamicText(promptArr['prompt22'],numberLanguage,'interactiveObj'),function()
								{
									level2Prompt1B();
								});
								break;
							}
							
							case 1:
							{
								extraParameters+='0,';
								frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
								frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
								frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]+"&#215;"+2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]+"&#215;"+2,numberLanguage,'interactiveObj')+'</div></div>';
							
								incorrectAttemptsL2B++;
								
								$(".txt1").attr("readOnly","true");
								
								$("#n"+level2Counter).css("border","1px solid black");
								$("#n"+level2Counter).addClass('redBorder');
								
								$("#d"+level2Counter).css("border","1px solid black");
								$("#d"+level2Counter).addClass('redBorder');
																
								n=$("#d"+level2Counter).val()/quesString[1];
								d=quesString[1];
								
								$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
								showPrompt(replaceDynamicText(promptArr['prompt23'],numberLanguage,'interactiveObj'),function()
								{
									level2Prompt2B();
								});
								break;
							}
							
							case 2:
							{
								extraParameters+='0);';
								var randomArr=new Array(3,4,5,10);
								var m=randomArr[Math.floor(Math.random() * (3 - 0 )) + 0];
								frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
								frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
								frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]+"&#215;"+2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]+"&#215;"+2,numberLanguage,'interactiveObj')+'</div></div>';
								frac3='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
								frac4='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj')+'</div></div>';
								frac5='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
								frac6='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0]*m,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1]*m,numberLanguage,'interactiveObj')+'</div></div>';
								frac7='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(quesString[0],numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(quesString[1],numberLanguage,'interactiveObj')+'</div></div>';
								
								incorrectAttemptsL2B++;
								$(".txt1").attr("readOnly","true");
								
								$("#n"+level2Counter).css("border","1px solid black");
								$("#n"+level2Counter).addClass('redBorder');
								
								$("#d"+level2Counter).css("border","1px solid black");
								$("#d"+level2Counter).addClass('redBorder');
															
								n=$("#d"+level2Counter).val()/quesString[1];
								d=quesString[1];
								
								$('.promptContainer').css({'position':'absolute','left':'210px','top':'100px'});
								showPrompt(replaceDynamicText(promptArr['prompt24'],numberLanguage,'interactiveObj'),function()
								{
									level2Prompt3B();
								});
								break;
							}
						}	
					}
				}
			}
			return false;		
		}
		else 
		{
            if(($(this).val().length > 2 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
			{
                return false;
            }
           setTimeout(function(){
        		numer1 = $("#n"+level2Counter).attr("value");
        		denom1 = $("#d"+level2Counter).attr("value");
				$("#n"+level2Counter).attr("value",replaceDynamicText(numer1,numberLanguage,'interactiveObj'));
				$("#d"+level2Counter).attr("value",replaceDynamicText(denom1,numberLanguage,'interactiveObj'));
       		 },10);
           /* setTimeout(function(){
        		numer1 = $("#n"+level2Counter).attr("value");
        		denom1 = $("#d"+level2Counter).attr("value");
        	},5);*/
        }
    });
}

function correctResponseL3()
{
		interactiveObj.fireQuestionL3();
}

function incorrectResponseL3()
{
	$('.optionButtons'+level3Counter).prop('checked',false);
	$('.optionButtons'+level3Counter).removeAttr('disabled');
	$('#'+parentId).removeClass('redBorder');	
}

function lastIncorrectResponseL3()
{	
	$('.optionButtons'+level3Counter).prop('checked',false);
	$('.optionButtons'+level3Counter).removeAttr('disabled');
	$('#'+parentId).removeClass('redBorder');
	parentId=$("#opt1"+level3Counter).parent('div').attr('id');
	$("#"+parentId).addClass('blueBorder');
	interactiveObj.fireQuestionL3();	
}

function level2Prompt1B()
{
	$('#n'+level2Counter).val('');
	$("#d"+level2Counter).val('');
	
	$('#n'+level2Counter).removeClass('redBorder')
	$("#d"+level2Counter).removeClass('redBorder');
	
	if(!(appleDevice || androidDevice))
	{
		$("#n"+level2Counter).removeAttr("readOnly");
		$('#d'+level2Counter).removeAttr('readOnly');
	}
	
	$('#d'+level2Counter).focus();
}

function level2Prompt2B()
{
	$('#n'+level2Counter).val('');
	$("#d"+level2Counter).val('');
	$("#d"+level2Counter).focus();
	
	$('#n'+level2Counter).removeClass('redBorder');
	$("#d"+level2Counter).removeClass('redBorder');
	
	if(!(appleDevice || androidDevice))
	{
		$("#n"+level2Counter).removeAttr("readOnly");
		$('#d'+level2Counter).removeAttr('readOnly');
	}	
	$('#d'+level2Counter).focus();
}

function level2CorrectAttempt()
{
	//alert('inside correct attempt');
	
	$("#n"+level2Counter).attr('readOnly','true');
	$("#d"+level2Counter).attr('readOnly','true');
	
	$("#n"+level2Counter).attr('border','1px solid black');
	$("#d"+level2Counter).attr('border','1px solid black');
	
	$("#n"+level2Counter).css("box-shadow","0px 0px 10px #00ff00,0px 0px 10px #00ff00,0px 0px 10px #00ff00,0px 0px 10px #00ff00");
	$("#d"+level2Counter).css("box-shadow","0px 0px 10px #00ff00,0px 0px 10px #00ff00,0px 0px 10px #00ff00,0px 0px 10px #00ff00");

	interactiveObj.fireQuestionL2();
}

function level2Prompt3B()
{
	$('#n'+level2Counter).val(replaceDynamicText(quesString[0]*2,numberLanguage,'interactiveObj'));
	$('#d'+level2Counter).val(replaceDynamicText(quesString[1]*2,numberLanguage,'interactiveObj'));
	
	$("#n"+level2Counter).removeClass('redBorder');
	$("#d"+level2Counter).removeClass('redBorder');
	
	$("#n"+level2Counter).addClass('blueBorder');
	$("#d"+level2Counter).addClass('blueBorder');
	
	if(!(appleDevice || androidDevice))
		$(".txt1").removeAttr("readOnly");
	interactiveObj.fireQuestionL2();
}

function zeroValue()
{
	$('#n'+level2Counter).val("");
	$('#d'+level2Counter).val("");
	$('#n'+level2Counter).removeClass('redBorder');
	$('#d'+level2Counter).removeClass('redBorder');

	if(!(androidDevice || appleDevice))
	{
		$('#n'+level2Counter).removeAttr('readOnly');
		$('#d'+level2Counter).removeAttr('readOnly');		
	}
	$('#d'+level2Counter).focus();
}

function alertForEmptyValue()
{
	$('.textbox1').focus();
	$('#ans').focus();
	$(this).focus();
	$('#n'+level2Counter).removeClass('redBorder');
	$('#d'+level2Counter).removeClass('redBorder');

	if(!(appleDevice || androidDevice))
	{
		$('#n'+level2Counter).removeAttr('readOnly');
		$('#d'+level2Counter).removeAttr('readOnly');			
	}
	if(lastLevelCleared==1)
	{
		if($('#d'+level2Counter).val()=="")
			$('#d'+level2Counter).focus();
		else
			$('#n'+level2Counter).focus();
	}	
	if(zeroFlag==1)
	{
		$('#n'+level2Counter).val("");
		$('#d'+level2Counter).val("");
	}
}

function level2Prompt1A()
{
	$('#n'+level2Counter).val('');
	$("#n"+level2Counter).focus();
	
	$("#n"+level2Counter).css("border","1px solid black");
	$("#n"+level2Counter).removeClass('redBorder');
	
	$("#d"+level2Counter).css("border","1px solid black");
	$("#d"+level2Counter).removeClass('redBorder');
	
	if(!(appleDevice || androidDevice))
	{
		$("#n"+level2Counter).removeAttr("readOnly");
		$("#d"+level2Counter).removeAttr("readOnly");
	}
	
}

function level2Prompt2A()
{
	$('#n'+level2Counter).val('');
	$("#n"+level2Counter).focus();
	
	$("#n"+level2Counter).css("border","1px solid black");
	$("#n"+level2Counter).removeClass('redBorder');
	
	$("#d"+level2Counter).css("border","1px solid black");
	$("#d"+level2Counter).removeClass('redBorder');
	
	if(!(appleDevice || androidDevice))
	{
		$("#d"+level2Counter).removeAttr("readOnly");
		$("#n"+level2Counter).removeAttr("readOnly");
	}
	$('#n'+level2Counter).focus();
}

function level2Prompt3A()
{
	$("#n"+level2Counter).removeClass('redBorder');
	$("#d"+level2Counter).removeClass('redBorder');
	
	$('#n'+level2Counter).val(replaceDynamicText(quesString[0]*n,numberLanguage,'interactiveObj'));
	$('#d'+level2Counter).val(replaceDynamicText(quesString[1]*n,numberLanguage,'interactiveObj'));
	
	$("#n"+level2Counter).css("border","1px solid black");
	$("#n"+level2Counter).addClass('blueBorder');
	
	$("#d"+level2Counter).css("border","1px solid black");
	$("#d"+level2Counter).addClass("blueBorder");
	
	$('#n'+level2Counter).attr('readOnly','true');
	$('#d'+level2Counter).attr('readOnly','true');
	
	$('#n'+level2Counter).focus();
	
	interactiveObj.fireQuestionL2();
}

function showPrompt(msg,callback)
{
	globalCallback = callback;
       $(".promptText").html('<b>'+msg+'</b>');
       $(".promptContainer").show();
	   $('#dialogueButton').focus();
	   
	   $("#next").focus();
}

function correct()
{
	if(wrongAttemptsBefore==0)
	{
		userResponseArr.push(true);
		level1Score++;
	}

	frac1='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';
	frac2='<div class="fraction"><div class="frac numerator">'+replaceDynamicText(multiplier*fracArr[no].substring(0,1),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(multiplier*fracArr[no].substring(2),numberLanguage,'interactiveObj')+'</div></div>';
	if(wrongAttemptsBefore==0)
	{
		$('#correctText').css('top','185px');
		$('#correctText').html(replaceDynamicText(instArr['inst2'],numberLanguage,'interactiveObj'));
	}
	else
	{
		$('#correctText').css('top','375px');
		$('#correctText').html(replaceDynamicText(instArr['inst2'],numberLanguage,'interactiveObj'));
	}
	$('#text').css('margin-top','150px');
	if(wrongAttemptsBefore==2)
	{
		$("#text").css("margin-top","0px");
	}
	$("#a").fadeOut("slow");
	$("#a").fadeIn("slow",function(){
		$('#next').show().focus();
	});
}

function incorrect1()
{
	userResponseArr.push(false);
	$(".textbox1").val("");
	$('.textbox1').removeClass('redBorder');
	$(".textbox1").removeAttr("disabled","disabled");
	$(".textbox1").focus();
}

function incorrect2()
{
	var i;
	$("#txt").val("");
	$('.textbox1').removeClass('redBorder');
	var canvas1=document.createElement('canvas');
	//var div=document.getElementById("canvas1");
	canvas1.id="chocolate1";
	canvas1.width="240";
	canvas1.style.backgroundColor="#B05F3C";
	canvas1.style.border="1px solid black";
	canvas1.height="35";
	canvas1.style.position="absolute";
	canvas1.style.top="150px";
	canvas1.style.left="455px";
	canvas1.style.zIndex="1";
	$('#canvas1').html(canvas1);

	var canvas2=document.createElement('canvas');
	//var div=document.getElementById("canvas2");
	canvas2.id="chocolate2";
	canvas2.width="240";
	canvas2.style.backgroundColor="#B05F3C";
	canvas2.style.border="1px solid black";
	canvas2.height="35";
	canvas2.style.position="absolute";
	canvas2.style.top="200px";
	canvas2.style.left="455px";
	canvas2.style.zIndex="1";
	$('#canvas2').html(canvas2);
	
	$("#tbl").css("display","block");
	for(i=1;i<=5;i++)
	{
		$("#row"+i).css("opacity","0");
	}
	
	var c1=document.getElementById("chocolate1");
	ctx1=c1.getContext("2d");
	
	var c2=document.getElementById("chocolate2");
	ctx2=c2.getContext("2d");
	
	
	var partLength1=240/(fracArr[no].substring(2));
	frac='<div class="fraction"><div class="frac numerator">'+fracArr[no].substring(0,1)+'</div><div class="frac">'+fracArr[no].substring(2)+'</div></div>';
	n=fracArr[no].substring(2)*multiplier;
	$("#row1").html(replaceDynamicText(promptArr['prompt4'],numberLanguage,'interactiveObj'));
	$("#row2").html(replaceDynamicText(promptArr['prompt5'],numberLanguage,'interactiveObj'));
	$("#row3").html(replaceDynamicText(promptArr['prompt6'],numberLanguage,'interactiveObj'));
	$("#row4").html(replaceDynamicText(promptArr['prompt7'],numberLanguage,'interactiveObj'));
	$("#row5").html(replaceDynamicText(promptArr['prompt8'],numberLanguage,'interactiveObj')+"<input type='text' pattern='[0-9]*' id='ans' value='' class='ans' pattern='[0-9]*' maxLength='2' readonly='readonly'/>");
	$("#ans").css("display","none");
	
	window.setTimeout(function()
	{
		for(i=0;i<=4;i++)
		{
			$('#row' + (i+1)).delay((i)*4000).animate({opacity: 1},500);
		}
	},10);
	
	window.setTimeout(function()
	{
		$("#canvas1").css("display","block");
		$("#canvas2").css("display","block");
		$("#john").css("display","block");
		$("#pam").css("display","block");
	},2500);

	window.setTimeout(function()
	{
		$("#ans").css("display","inline-block");
		if(!(appleDevice || androidDevice))
			$("#ans").removeAttr("readonly");
		$("#ans").focus();
	},16000);
	window.setTimeout(function()
	{
		for(i=1;i<=parseInt(fracArr[no].substring(2));i++)
		{
			ctx1.beginPath();
			ctx1.moveTo(i*partLength1,0);
			ctx1.lineTo(i*partLength1,34);
			ctx1.stroke();
			ctx1.closePath();
		}
		
		var a=0.005;
		var temp=window.setInterval(function()
		{
			ctx1.beginPath();
			ctx1.moveTo(0,0);
			ctx1.fillStyle="rgba(255,255,255,"+a+")";
			ctx1.fillRect(0,0,parseInt(fracArr[no].substring(0,1))*partLength1,35);
			ctx1.fill();
			ctx1.closePath();
			a+=0.005;
			if(a>=0.09)
			{
				window.clearInterval(temp);
			}
		},100);
	},6500);

	window.setTimeout(function()
	{
		var parts=parseInt(fracArr[no].substring(2))*multiplier;
		
		partLength2=240/parts;
		 
		for(i=1;i<=parseInt(fracArr[no].substring(2))*multiplier;i++)
		{
			ctx2.beginPath();
			ctx2.moveTo(i*partLength2,0);
			ctx2.lineTo(i*partLength2,35);
			ctx2.stroke();
			ctx2.closePath();
		}
	},10500);	
}

function correct4()
{
	var c=document.getElementById('chocolate2');
	var ctx=c.getContext('2d');
			ctx.beginPath();
			ctx.moveTo(0,0);
			ctx.fillStyle="rgba(255,255,255,0.5)";
			ctx.fillRect(0,0,parseInt(fracArr[no].substring(0,1))*partLength2*multiplier,35);
			ctx.fill();
			ctx.closePath();
	
	$("#prompt").remove();
	$(".ans").removeClass('redBorder');
	$(".ans").removeClass('blueBorder');
	$("#ans").val(multiplier*fracArr[no].substring(0,1));
	
		if(!(appleDevice || androidDevice))
			$("#txt").removeAttr("readOnly");
	
	$("#txt").focus();
	$(".hand").css("display","block");
	$('.textbox1').removeAttr("disabled","disabled");

	if(!(appleDevice || androidDevice))
		$("#ans").removeAttr("readOnly");
} 

 
function incorrect4()
{
	var c=document.getElementById('chocolate2');
	var ctx=c.getContext('2d');
			ctx.beginPath();
			ctx.moveTo(0,0);
			ctx.fillStyle="rgba(255,255,255,0.5)";
			ctx.fillRect(0,0,parseInt(fracArr[no].substring(0,1))*partLength2*multiplier,35);
			ctx.fill();
			ctx.closePath();
	
	$('.textbox1').removeAttr("disabled");
	
	$("#prompt").remove();
	$('.ans').removeClass('redBorder');
	
	$("#ans").val(replaceDynamicText(multiplier*fracArr[no].substring(0,1),numberLanguage,'interactiveObj'));
	if(wrongAttemptsAfter==0)
	{
		$("#ans").addClass('redBorder');
		if(!(appleDevice || androidDevice))
			$("#txt").removeAttr("readOnly");
	}
	else if(wrongAttemptsAfter==1)
	{
		$("#ans").addClass('blueBorder');
		if(!(appleDevice || androidDevice))
			$("#txt").removeAttr("readOnly");
	}
	$("#txt").focus();
	$(".hand").css("display","block");
	if(!(appleDevice || androidDevice))		
		$("#ans").removeAttr("readOnly");
} 
 
function incorrect3()
{

	$("#prompt").remove();
	$('.ans').removeClass('redBorder');
	
	$("#ans").val("");
	$("#ans").val("");
	$("#prompt").remove();
	$(".ans").removeClass('redBorder');
	
			$("#ans").attr("readOnly","true");
			
			var partLength=240/(fracArr[no].substring(2));
			var container=document.getElementById("container");
			var dv=document.createElement('div');
			dv.setAttribute("id","fadedParts");
			dv.style.position="absolute";
			dv.style.left="455px";
			dv.style.top="153px";
			dv.style.width=(parseInt(fracArr[no].substring(0,1))*partLength-1)+"px";
			dv.style.height="35px";
			dv.style.backgroundColor="#daa794";
			//dv.style.border="1px solid black";
			dv.style.zIndex="3";
			dv.style.opacity="0.6";
			container.appendChild(dv);
			$("#fadedParts").animate({top:"201px"},1500);
			$('.ans').removeClass('redBorder');
			$("#ans").val("");
			
			window.setTimeout(function()
			{
				$("#fadedParts").fadeOut();	
				if(!(appleDevice || androidDevice))				
					$("#ans").removeAttr("readOnly");
				$("#ans").removeAttr("disabled","disabled");
				$("#ans").focus();
				//$("#container").append("<div id='prompt' style='width:400px;height:110px;top:380px;left:370px;'><img id='sparky' src='../assets/sparky.png' /><p id='msg' style='position:relative;margin-top:-30px;margin-left:35px;'>"+promptArr['prompt12']+"</p><button id='ok' onClick='incorrect3();' style='position:relative;margin-left:120px;margin-top:10px;'>Ok</button</div>");				
			},3500);
			
}
 
function incorrect5()
{
	$("#correctText").css("top","375px");
	
	$('.promptText').css('height','auto');
	$('.promptText').css('width','auto');
	
	$('.promptContainer').css('width','auto');
	$('.promptContainer').css('height','auto');
	
	$('.promptContainer').css('margin-top','0px');
	$('.promptContainer').css('margin-left','0px');
	
	$("#prompt").remove();
	$('.textbox1').removeClass('redBorder');
	$("#txt").val(replaceDynamicText(multiplier*fracArr[no].substring(0,1),numberLanguage,'interactiveObj'));

	$('.textbox1').addClass('blueBorder');
	$("#correctText").html(replaceDynamicText(instArr['inst2'],numberLanguage,'interactiveObj'));
	$('#text').css('margin-top','0px');
	
	$("#a").fadeOut("slow");
	$("#a").fadeIn("slow",function(){
		$('#next').show().focus();
	});
} 
 
questionInteractive.prototype.init = function() 
{
	if(parameterMissing == true) return;
	var html='';
	html+='<div id="background" class=background><p id="inst1"><b>'+replaceDynamicText(instArr['inst1'],numberLanguage,'interactiveObj')+'</b></p><div class="fraction1"><div id="num1" class="frac1 numerator1"></div><div id="den1" class="frac1"></div></div>';
	html+='<div id="equals">=</div>';
	html+='<div class="fraction2"><div id="num2" class="frac2 numerator2"><input type="text" id="txt" class="textbox1" pattern="[0-9]*" maxLength="2" readonly="true"/></div><div id="den2" class="frac2"></div></div>';
	html+='<div class=hand><img id="hand" src="../assets/hand.png" /></div>';
	html+='<div id="correctText"></div>';
	html+='<button id="next" onClick="interactiveObj.fireQuestionL1()"></button>';
	html+='</div>';
	html+='<table id="tbl">';
	html+='<tr id="row1" style="height:40px;"><td id="td1"></td></tr>';
	html+='<tr id="row2" style="height:40px;"><td id="td2"></td></tr>';
	html+='<tr id="row3" style="height:40px;"><td id="td3"></td></tr>';
	html+='<tr id="row4" style="height:40px;"><td id="td4"></td></tr>';
	html+='<tr id="row5" style="height:40px;"><td id="td5"><input type="text" id="ans" readonly="true"/></td></tr>';
	html+='</table>';
	html+='<div id="canvas1"></div>';
	html+='<div id="john" class="john">'+replaceDynamicText(promptArr["prompt9"],numberLanguage,'interactiveObj')+'</div>';
	html+='<div id="canvas2"></div>';
	html+='<div id="pam" class="pam">'+replaceDynamicText(promptArr["prompt10"],numberLanguage,'interactiveObj')+'</div>';
	html+='<div id="promptContainer2" class="promptContainer" draggable="true">';
		html+='<div id="sparkieIcon"></div>';
		html+='<div class="promptText"></div>';
		html+='<div style="clear:both"></div>';
		html+='<button class="button" id="dialogueButton">'+replaceDynamicText(promptArr['prompt0'],numberLanguage,'interactiveObj')+'</button>';
    html+='</div>';
	$("#container").html(html);	
	if(!(appleDevice || androidDevice))
	{
		$("input[type='text']").removeAttr("readonly");
	}
	$("#next").html(replaceDynamicText(miscArr["next"],numberLanguage,'interactiveObj'));
	$(".promptContainer").hide();
	$("#dialogueButton").click(function(e)
	{	
		$("#dialogueButton").blur();
		$(".promptContainer").hide();
		$("#container").focus();
            globalCallback();
	});
	
$('#promptContainer2').draggable({
	containment:"#container"
	// cursorAt: {top:70,left: 200}
});
	
	$("#next").hide();
	fracArr=new Array("2/5","3/4","2/3","1/2","1/3","1/4","1/5","1/6");
	for(i=0;i<8;i++)
	{
		rArrL1[i]=i;
	}
	rArrL1.sort(function ()
	{
		return Math.random() - 0.5;
	});
		
	if(lastLevelCleared==0)
	{
		extraParameters+='Level1';
		interactiveObj.fireQuestionL1();
		level1Flag=1;
	}
	else if(lastLevelCleared==1)
		interactiveObj.goToLevel2();
	else if(lastLevelCleared==2)
		interactiveObj.goToLevel3();
} 

questionInteractive.prototype.fireQuestionL1=function()
{ 	
	$('.textbox1').removeAttr("disabled","disabled");
	$("#fadedParts").remove();
	if(level2Entered==0)
	{
	$('#correctText').html('');
	$('.textbox1').removeClass('redBorder');
	$('.textbox1').removeClass('greenBorder');
	$('.textbox1').removeClass('blueBorder');
	levelsAttempted='L1';
	levelWiseStatus='0';
	levelWiseScore='0';
		if(quesNo>=4)
		{
			for(i=0;i<=(quesNo-1);i++)
			{
				if(userResponseArr[i]==true&&userResponseArr[i+1]==true&&nextLevelFlag==0)
				{	
					$('#text').remove();
					
					completed=0;
					interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level1Score;
					interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
					interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
					this.lastlevelcleared=2;
					interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
					interactiveObj.setOutputParameters();
					level2Entered=1;
					interactiveObj.goToLevel2();
				}
			}
		}
	if(quesNo==8)
	{
				level2Entered=1;
				completed=0;
				interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level1Score;
				interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
				interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
				this.lastlevelcleared=2;
				interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
				interactiveObj.setOutputParameters();
				$('#text').remove();
				interactiveObj.goToLevel2();
	}
	if(quesNo!=8&&level2Entered==0)
	{
		$('#next').hide();
		$('.hand').css('display','none');
		wrongAttemptsBefore=0;
		wrongAttemptsAfter=0;
		
		$("#tbl").css("display","none");
		$("#canvas1").css("display","none");
		$("#canvas2").css("display","none");
		$("#john").css("display","none");
		$("#pam").css("display","none");
		$("#txt").val("");
		if(!(appleDevice || androidDevice))
			$("#txt").removeAttr("readOnly");
		
		$("#text").remove();
		
		no=rArrL1[quesNo];
		
		extraParameters+='('+fracArr[no]+",";
		multiplier = Math.random() < 0.5 ? 2 : 3 ;
		$("#num1").html(replaceDynamicText(fracArr[no].substring(0,1),numberLanguage,'interactiveObj'));
		$("#den1").html(replaceDynamicText(fracArr[no].substring(2),numberLanguage,'interactiveObj'));
		$("#den2").html(replaceDynamicText(multiplier*fracArr[no].substring(2),numberLanguage,'interactiveObj'));
		$("#txt").focus();
		quesNo++;
	}
	}
}

questionInteractive.prototype.goToLevel2=function()
{
	nextLevelFlag=1;
	$("#tbl").remove();
	$("#canvas1").remove();
	$("#canvas2").remove();
	$("#john").remove();
	$("#pam").remove();
	
	var counter=1;
	level2flag=1;
	var html1='';
	timer=0;
	extraParameters+='|Level2';
	
	html+='<div id="background" class=background>';
		html1+='<div id="level2background" class=level2background></div>';
		html1+='<p id="inst1"><b>'+replaceDynamicText(instArr['inst4'],numberLanguage,'interactiveObj')+'</b></p>';
		html+='<div id="promptContainer" class="promptContainer">';
		html+='<div id="sparkieIcon"></div>';
		html+='<div class="promptText"></div>';
		html+='<div style="clear:both"></div>';
		html+='<button class="button" id="dialogueButton">'+replaceDynamicText(promptArr['prompt0'],numberLanguage,'interactiveObj')+'</button>';
		html+='</div>';
	$("#background").html(html1);
	interactiveObj.fireQuestionL2();
}

questionInteractive.prototype.fireQuestionL2=function()
{
	if(level3Entered==0)
	{
	sameFractionAttempt=0;
	incorrectAttemptsL2A=0;
	incorrectAttemptsL2B=0;
	errorTypeB=0;
	
	var html='';
	////////////////////////op[arr[counter]]
	level2Counter++;
	if(level2Flag==1)
		level2BCounter++;
	
	if(level2Counter==4)
	{
		for(i=0;i<=(level2Counter-1);i++)
		{
			if(userResponseArrL2[i]=='true'&&userResponseArrL2[i+1]=='true'&&level2Flag==0)
			{
				level2Flag=1;
				fracArr=new Array("7/9","12/19","13/17","14/19","15/17","1/12","5/14","9/11","8/17","11/19","16/19","7/20");
				for(i=0;i<10;i++)
				{
					randomArr[i]=i;
				}
				randomArr.sort(function ()
				{
					return Math.random() - 0.5;
				});
			}
		}
	}
	else if(level2Counter>5&&level2Flag==0)
	{
		level2Flag=1;
		fracArr=new Array("7/9","12/19","13/17","14/19","1/12","5/14","15/17","9/11","8/17","11/19","16/19","7/20");
		for(i=0;i<10;i++)
		{
			randomArr[i]=i;
		}
		randomArr.sort(function()
		{
			return Math.random() - 0.5;
		});
	}
	
	if(level2BCounter>=5)
	{
		for(i=0;i<=(level2BCounter-1);i++)
		{
			if(userResponseArrL2[i]=='true'&&userResponseArrL2[i]=='true')
			{
				interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level2Score;
					interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
					interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
					this.lastlevelcleared=3;
					interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
					interactiveObj.setOutputParameters();
					interactiveObj.goToLevel3();
			}
		}
	}
	if(level2BCounter==8)
	{
				interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level2Score;
				interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
				interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
				this.lastlevelcleared=3;
				interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
				interactiveObj.setOutputParameters();
				interactiveObj.goToLevel3();
	}
	if(level2Flag==1)
		no=randomArr[level2BCounter-1];
	else
		no=randomArr[level2Counter-1];
	/*if(level2Flag==1)
	{	
		completed=0;
				interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level2Score;
				interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
				interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
				this.lastlevelcleared=3;
				interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
				interactiveObj.setOutputParameters();
				level3Entered=1;
				interactiveObj.goToLevel3();
	}
	else if(level2Counter>5&&level2Flag==0)
	{
		completed=0;
				interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level2Score;
				interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
				interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
				this.lastlevelcleared=3;
				interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
				interactiveObj.setOutputParameters();
				level3Entered=1;
				interactiveObj.goToLevel3();
	}*/
	
	html+='<br>';
	html+='<div class="fraction3"><div id="nume'+level2Counter+'" class="frac1 numerator1"></div><div id="deno'+level2Counter+'" class="frac1"></div></div>';
	html+='<div id="equals">=</div>';
	html+='<div class="fraction4" ><div id="numerator2" class="frac2 numerator2" ><input type="text" pattern="[0-9]*" id="n'+level2Counter+'" class="txt1" maxLength="2" readonly="true"/></div><div id="denominator2" class="frac2" ><input type="text" pattern="[0-9]*" id="d'+level2Counter+'" class="txt1" style="margin-left:-17px;margin-top:15px;" maxLength="2" readonly="true"/></div></div>';
	html+='<br/>';
	$("#level2background").append(html);
	if(!(appleDevice || androidDevice))
	{
		$("input[type='text']").removeAttr("readonly");
	}
	
	if(level3Entered==0)
	extraParameters+='('+fracArr[no]+',';
	
	quesString=fracArr[no].split("/");
	
	$("#nume"+level2Counter).html(replaceDynamicText(quesString[0],numberLanguage,'interactiveObj'));
	$("#deno"+level2Counter).html(replaceDynamicText(quesString[1],numberLanguage,'interactiveObj'));
	$("#d"+level2Counter).focus();
	}
}

questionInteractive.prototype.goToLevel3=function()
{
	var counter=1;
	timer=0;
	level2flag=1;
	extraParameters+='|Level3';
	
	fracArr=new Array("3/20","9/20","7/20","6/17","12/19","3/20","3/19","7/18","5/12","5/11","2/17","5/17","13/17","14/19","2/19","7/12","5/14","15/17","9/11","8/17","11/19","16/19","7/20","3/10");
	for(i=0;i<24;i++)
	{
		randomArr[i]=i;
	}
	randomArr.sort(function ()
	{
		return Math.random() - 0.5;
	});
		
	var html1='';
	html1+='<div id="level3Background" class=level2background>';
	html1+='</div>';
	html+='<div id="promptContainer" class="promptContainer">';
		html+='<div id="sparkieIcon"></div>';
		html+='<div class="promptText"></div>';
		html+='<div style="clear:both"></div>';
		html+='<button class="button" id="dialogueButton">'+replaceDynamicText(promptArr['prompt0'],numberLanguage,'interactiveObj')+'</button>';
    html+='</div>';
	$("#background").html(html1);
	interactiveObj.fireQuestionL3();	
}

questionInteractive.prototype.fireQuestionL3=function()
{
	level3Entered=1;
	incorrectAttemptsL3=0;
	var html='';
	
	rArr=new Array();
	no=randomArr[level3Counter];
	level3Counter++;
	
		if(level3Counter==3)
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'170px'});
			else
				$('.promptContainer').css({'position':'absolute','left':'155px','top':'335px'});
	
	
	if(level3Counter>3)
	{
		$('.optionButtons'+(level3Counter-1)).attr('disabled',true);
	}
	
	if(level3Counter>3)
	{
		if(level3Score==3)
		{
			completed=1;
					interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level3Score;
					interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
					interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
					interactiveObj.setOutputParameters();
		}
		else
		{
			completed=1;
					interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level3Score;
					interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
					interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
					interactiveObj.setOutputParameters();
		}
	}
	if(level3Counter<=3)
	{
		$('.optionButtons'+(level3Counter-1)).attr('disabled',true);
		extraParameters+='('+fracArr[no]+',';
		n=fracArr[no].split('/');
		no1=parseInt(n[0]);
		no2=parseInt(n[1]);
		
		z=Math.floor(Math.random() * (30 - 2 + 1)) + 2;
		r=Math.floor(Math.random() * (10 - 2 + 1)) + 2;
		t=Math.floor(Math.random() * (10 - 2 + 1)) + 2;
		number1=Math.floor(Math.random() * (10 - 2 + 1)) + 2;
		number2=Math.floor(Math.random() * (20 - 2 + 1)) + 2;
		m = Math.random() < 0.5 ? 0 : 1;
		if(m==0)
		{
			m1=r*no1;
			m2=z;
		}
		else
		{
			m1=z;
			m2=r*no2;
		}
		
		for(i=0;i<4;i++)
		{
			rArr[i]=i+1;
		}
		rArr.sort(function ()
		{
			return Math.random() - 0.5;
		});
		
		frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
		frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText(no1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(no2,numberLanguage,'interactiveObj')+'</div></div>';
		
		/*******************4 options*********************/
		frac3='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(no1*number1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(no2*number1,numberLanguage,'interactiveObj')+'</div></div>';
		frac4='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText((no1-1),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText((no2),numberLanguage,'interactiveObj')+'</div></div>';
		frac5='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(m1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(m2,numberLanguage,'interactiveObj')+'</div></div>';
		frac6='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(no1+number1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(no2+number1,numberLanguage,'interactiveObj')+'</div></div>';
		/*************************************************/
		
		html+='<div id="questionText" ><font size="3pt"><b>'+replaceDynamicText(instArr['inst5'],numberLanguage,'interactiveObj')+'?</b></font></div>';
		html+='<br/>';
		html+='<div id="div1'+level3Counter+'" style="float:left;height:50px;font-size:15pt;padding-right:35px;margin-left:25px;"><input type="radio" name="optionValues" id="opt'+rArr[0]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[0]+level3Counter+'"></label></div>';
		html+='<div id="div2'+level3Counter+'" style="float:left;height:50px;font-size:15pt;padding-right:35px;margin-left:25px;"><input type="radio" name="optionValues" id="opt'+rArr[1]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[1]+level3Counter+'"></label></div>';
		html+='<div id="div3'+level3Counter+'" style="float:left;height:50px;font-size:15pt;padding-right:35px;margin-left:25px;"><input type="radio" name="optionValues" id="opt'+rArr[2]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[2]+level3Counter+'"></label></div>';
		html+='<div id="div4'+level3Counter+'" style="float:left;height:50px;font-size:15pt;padding-right:35px;margin-left:25px;"><input type="radio" name="optionValues" id="opt'+rArr[3]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[3]+level3Counter+'"></label></div>';
		html+='<br/><br/><br/><br/><br/>';
		
		//for generating random numbers from 1 to 4, for shuffling the option each time	
		
		$("#level3Background").append(html);
		$('label[for=opt1'+level3Counter+']').html('&nbsp;&nbsp;'+frac3);
		$('label[for=opt2'+level3Counter+']').html('&nbsp;&nbsp;'+frac4);
		$('label[for=opt3'+level3Counter+']').html('&nbsp;&nbsp;'+frac5);
		$('label[for=opt4'+level3Counter+']').html('&nbsp;&nbsp;'+frac6);
	}
}
	
function resize()
{ 
	if(window.innerHeight < $("#container").height()) 
	{
		scaleFactor = parseFloat(window.innerHeight/$("#container").height()); 
	} else if(window.innerWidth < $("#container").width()) {
		scaleFactor = parseFloat(window.innerWidth/$("#container").width());
	} else
	{
		scaleFactor = 1 ;									
	} 	
	$("#container").css({"-webkit-transform": "scale("+scaleFactor+")"});
	$("#container").css({"-moz-transform": "scale("+scaleFactor+")"});	
	$("#container").css({"-o-transform": "scale("+scaleFactor+")"});	
	$("#container").css({"-ms-transform": "scale("+scaleFactor+")"});	
	$("#container").css({"transform": "scale("+scaleFactor+")"});		
}

questionInteractive.prototype.setOutputParameters = function() 
{
	levelsAttempted = "";		
	levelWiseStatus = "";		
	levelWiseScore 	= "";
	levelWiseTimeTaken = "";
	
	for(var i=0;i<interactiveObj.levelsAttemptedArr.length;i++)
	{
		levelsAttempted += "L"+interactiveObj.levelsAttemptedArr[i]+"|";
		levelWiseStatus += interactiveObj.levelWiseStatusArr[interactiveObj.levelsAttemptedArr[i]-1]+"|";
		levelWiseScore  += interactiveObj.levelWiseScoreArr[interactiveObj.levelsAttemptedArr[i]-1]+"|";
		levelWiseTimeTaken  += interactiveObj.levelWiseTimeTakenArr[interactiveObj.levelsAttemptedArr[i]-1]+"|";		
	}
	levelsAttempted = levelsAttempted.substr(0,(levelsAttempted.length-1));
	levelWiseStatus = levelWiseStatus.substr(0,(levelWiseStatus.length-1));
	levelWiseScore  = levelWiseScore.substr(0,(levelWiseScore.length-1));
	levelWiseTimeTaken  = levelWiseTimeTaken.substr(0,(levelWiseTimeTaken.length-1));
};

  (function(){
  				var parObject = getURLParameters;
  				if(parObject.hasOwnProperty("numberLanguage"))
  				{
	  				if(getURLParameters()['numberLanguage'] == 'english')
	  				{
	                	return;
	                }
	            }
	            else
	            {
	            	return;
	            }
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
            jQuery.fn.attr = function(){
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
            }
        })();