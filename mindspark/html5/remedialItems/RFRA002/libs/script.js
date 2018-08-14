var interactiveObj;
var sameFractionAttempt=0;
var parameterMissing=false;
var numberLanguage;
var nextLevelFlag=0;
var fracArr;
var currentLevel=0;
var rArr;
var lastDivisor=0;
var i=0;
var string='';
var blankFlag=0;
var n1=n2=n3=gcd=a=b=0;
var level3Counter=0;
var level2Counter=0;
var randomArr=new Array();
var quesNo=0;
var no1=no2=no3=no4=0;
var multiplier1=multiplier2=0;
var ctx1,ctx2,parentId,parentId1,parentId2;
var frac=frac1=frac2=frac=frac4=frac5=frac6='';
var incorrectAttemptsBefore=0;
var incorrectAttemptsAfter=0;
var incorrectAttemptsL3=0;
var incorrectAttemptsL2=0;
var r=n=t=f1=f2=m1=m2=m3=num1=num2=0;
var userResponseLevel1=new Array();
var multiplier3=0;
var level3Alert=0;
var divisor1=divisor2=0;
var level3Count=0;
var incorrectAttemptsAfterL3=0;
var check=0;
var divisor3=divisor4=0;
var globalCheck=0;
var wrongAttemptsL3=0;
var bigDivisorWrongAttempt=0;
var globalFlag=0;
var lastAttempt=0;
var nextButtonDisplayFlag=0;
var level2Counter=level3Counter=0;
var level1Counter=0;
var userResponseLevel2=new Array();
var userResponseLevel3=new Array();
var level1Score=level2Score=level3Score=0;
var levelsAttempted=0;
var levelWiseStatus=0;
var levelWiseScore=0;
var levelWiseTimeTaken=0;
var completed=0;
var timer=0;
var noOfLevels=0;
var lastLevelCleared=0;
var regularEquivalentFractionAttempt=0;
var timer=0;
var level1Flag=level2Flag=level3Flag=0;
var tempTimer=0;
var Level1Time=level2Time=level3Time=0;
var Level3Flag=0;
var extraParameters='';
var equivalentFractionAttempt=0;
var level3BCounter=1;
var criteria='';
var totalTimeTaken=0;
var levelWiseTimeTakenArr = new Array(0,0,0);
var level3EquivalentFraction=0;
var case2=0;
var case3=0;
var factors=new Array();
var catchingFactors=new Array();
var divisibleFactors=new Array();
var listFactors=new Array();
var incorrectDivisorAttempt=0;
var divideByZeroAttempt=0;
var attemptFlag='';
var disabledFlag=0;
var sub2added=0;
var correctFlag=0;
var furtherAnimationFlag=0;
var txtval;
var language;
var txtval1;
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
			lastLevelCleared=getParameters['lastLevelCleared'];
			this.lastlevelcleared=parseInt(getParameters['lastLevelCleared'])+1;
			currentLevel=parseInt(getParameters['lastLevelCleared'])+1;
			parameterMissing=false;
		}
	//startGameTimer();
	$('#container').css('border','0px solid black');
	$('#container').css('border-radius','0px');	
	
	if(typeof getParameters['numberLanguage']=='undefined')
		numberLanguage='english';
	else
		numberLanguage=getParameters['numberLanguage'];
	if(typeof getParameters['language']=='undefined')
		language='english';
	else
		language=getParameters['language'];
		
		this.levelWiseScoreArr = new Array(0,0,0);
		this.levelWiseStatusArr = new Array(0,0,0);
		
		this.levelsAttemptedArr = new Array();
		this.levelsAttemptedArr.push(this.lastlevelcleared);
		levelsAttempted = "L"+this.levelsAttemptedArr+"|";
		
		/*window.setInterval(function()
		{
			timer++;
			interactiveObj.levelWiseTimeTakenArr[this.lastlevelcleared-1]=timer;
			interactiveObj.setOutputParameters();
		},1000);*/
		/**************************************************************************************************/
		/**************************************************************************************************/
		
	$(".textbox1").live("keypress",function(e)
	{
		if(incorrectAttemptsBefore==0)
			$('#contentLine').css('margin-top','200px');
		else
			$('#contentLine').css('margin-top','0px');
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
            var value = $(this).val();
            if (value == "") 
			{
                showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
				{
					doNothing();
				});
			}
            else 
			{
				if($('#den1').html()/$('#den2').html()==$('#num1').html()/$('#txt').val())
				{
					$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
					
					$(".textbox1").addClass('greenBorder');
					$(".textbox1").attr("disabled",true);
					showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
					{
						correctBefore();
					});
					if(incorrectAttemptsBefore==0&&incorrectAttemptsAfter==0)
					userResponseLevel1.push('true');
					level1Score++;
					extraParameters+='1);';
				}
				else
				{
					switch(incorrectAttemptsBefore)
					{
						case 0:
						{
							incorrectAttemptsBefore++;
							
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							
							userResponseLevel1.push('false');
							extraParameters+='0,';
							frac1='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
							frac2='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText($('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
							$(".textbox1").addClass('redBorder');
							$('#txt').attr('disabled',true);
							
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							showPrompt(replaceDynamicText(promptArr['prompt2'],numberLanguage,'interactiveObj'),function()
							{
								L1incorrect1();
							});
							break;
						}
						case 1:
						{
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							
							extraParameters+='0,';
							incorrectAttemptsBefore++;
							$(".textbox1").addClass('redBorder');
							$('#txt').attr('disabled',true);
							
							$('.promptContainer').css({'position':'absolute','left':'140px','top':'120px'});
							showPrompt(replaceDynamicText(promptArr['prompt5'],numberLanguage,'interactiveObj'),function()
							{
								L1incorrect2();
							});
							break;
						}
						case 2:
						{
							$('.promptContainer').css({'position':'absolute','left':'0px','top':'120px'});
							
							extraParameters+='0);';
							incorrectAttemptsBefore++;
							$(".textbox1").addClass('redBorder');
							$('#txt').attr('disabled',true);
							
							$('.promptContainer').css('margin-top','40px');
							$('.promptContainer').css('margin-left','0px');
							
							$('.promptText').css('height','200px');
							$('.promptText').css('width','690px');
							
							frac1='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
							frac2='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
							frac3='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html()/($('#den1').html()/$('#den2').html()),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
							frac4='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html()/($('#den1').html()/$('#den2').html()),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
							
							choc1='<span id="c1"><canvas id="choc1" height="25px" width="240px" style="margin-top:15px;background:#B05F3C;border:1px solid black;"></canvas></span>'; 
							choc2='<span id="c2"><canvas id="choc2" height="25px" width="240px" style="margin-top:15px;background:#B05F3C;border:1px solid black;"></canvas></span>'; 
							
							no1=replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj');
							no2=replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj');
							
							no3=replaceDynamicText($('#num1').html()/($('#den1').html()/$('#den2').html()),numberLanguage,'interactiveObj');
							no4=replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj');
							
							fraction1='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText($('#num1').html()/($('#den1').html()/$('#den2').html()),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').val(),numberLanguage,'interactiveObj')+'</div></div>';
							
							frac5='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
							frac6='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText($('#num1').html()/($('#den1').html()/$('#den2').html()),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
							
							$("#dialogueButton").attr("disabled","disabled");
							
							$('.promptContainer').css({'position':'absolute','left':'0px','top':'120px'});
							showPrompt(replaceDynamicText(promptArr['prompt13'],numberLanguage,'interactiveObj'),function()
							{
								correctBefore();
							});
							
							$('#tbl').css('display','none');
							$('#john').css('display','none');
							$('#pam').css('display','none');
							$('#canvas1').css('display','none');
							$('#canvas2').css('display','none');
												
							window.setTimeout(function()
							{
								$('.promptText').append(replaceDynamicText(promptArr['prompt14'],numberLanguage,'interactiveObj'));
								$('.promptText').append(replaceDynamicText(promptArr['prompt15'],numberLanguage,'interactiveObj'));
								
								var c1=document.getElementById('choc1');
								ctx1=c1.getContext('2d');
							
								var c2=document.getElementById('choc2');
								ctx2=c2.getContext('2d');
								
								var partLength1=240/$('#den2').html();
								for(i=1;i<=parseInt($('#den2').html());i++)
								{
									ctx2.beginPath();
									ctx2.moveTo(i*partLength1,0);
									ctx2.lineTo(i*partLength1,65);
									ctx2.stroke();
									ctx2.closePath();
								}
								
								var partLength2=240/$('#den1').html();
								for(i=1;i<=parseInt($('#den1').html());i++)
								{
									ctx1.beginPath();
									ctx1.moveTo(i*partLength2,0);
									ctx1.lineTo(i*partLength2,65);
									ctx1.stroke();
									ctx1.closePath();
								}
								
								var a=0.005;
								var temp1=window.setInterval(function()
								{
									ctx2.beginPath();
									ctx2.moveTo(0,0);
									ctx2.fillStyle="rgba(255,255,255,"+a+")";
									ctx2.fillRect(0,0,parseInt($('#num1').html()/($('#den1').html()/$('#den2').html()))*partLength1,35);
									ctx2.fill();
									ctx2.closePath();
									a+=0.005;
									if(a>=0.12)
									{
										window.clearInterval(temp1);
									}
								},100);
								
								var a=0.005;
								var temp2=window.setInterval(function()
								{
									ctx1.beginPath();
									ctx1.moveTo(0,0);
									ctx1.fillStyle="rgba(255,255,255,"+a+")";
									ctx1.fillRect(0,0,parseInt($('#num1').html())*partLength2,35);
									ctx1.fill();
									ctx1.closePath();
									a+=0.005;
									if(a>=0.12)
									{
										window.clearInterval(temp2);
									}
								},100);
							},2000);
								 							
							window.setTimeout(function()
							{
								$('.promptText').append(replaceDynamicText(promptArr['prompt16'],numberLanguage,'interactiveObj'));
								$("#dialogueButton").removeAttr("disabled","disabled").focus();
							},5000);
							break;
						}
					}
				}
			}
			return false;
		}
		else 
		{
            if(($(this).val().length == 2 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
			{
                return false;
            }
            setTimeout(function(){
        	txtval = $(".textbox1").val();
			$(".textbox1").attr("value",replaceDynamicText(txtval,numberLanguage,'interactiveObj'));
        },1);
        }
    });
	/********************************************************************/
	$("input:radio[name=optionValues]").live("click", function()
	{
		$('.promptContainer').css({'position':'absolute','left':'180px','top':'120px'});
		var id = $(this).attr("id");
		if(id=='opt1'+level3Counter)	
		{
			extraParameters+='1);';
			parentId=$("#"+id).parent('div').attr('id');
			$("#"+parentId).addClass('greenBorder');
			$('.optionButtons'+level3Counter).attr('disabled',true);
			//extraParameters+='1);';
			if(incorrectAttemptsL2==0)
				userResponseLevel2.push("true");
			$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
			showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
			{
				level2Correct();	
			});
		}
		if(id=='opt2'+level3Counter)
		{
			switch(incorrectAttemptsL2)
			{
				case 0:
				{
					$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
					extraParameters+='0,';
					frac='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText(f1*n,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*n,numberLanguage,'interactiveObj')+'</div></div>';
					ctx1=f1*n;
					ctx2=f2*n;
					incorrectAttemptsL2++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt21'],numberLanguage,'interactiveObj'),function()
					{
						level2Incorrect();	
					});
					break;
				}
				case 1:
				{
					$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
					extraParameters+='0);';
					frac='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText(f1*n,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*n,numberLanguage,'interactiveObj')+'</div></div>';
					frac1='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText(f1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2,numberLanguage,'interactiveObj')+'</div></div>';
					n=m1;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt46'],numberLanguage,'interactiveObj'),function()
					{
						level2IncorrectAfter();	
					});
					break;
				}
			}
		}
		if(id=='opt3'+level3Counter)
		{
			switch(incorrectAttemptsL2)
			{
				case 0:
				{
					$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
					extraParameters+='0,';
					frac1='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText(1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction" style="width:55px;"><div class="frac numerator">'+replaceDynamicText(f1*multiplier2,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*multiplier2,numberLanguage,'interactiveObj')+'</div></div>';
					
					incorrectAttemptsL2++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt28'],numberLanguage,'interactiveObj'),function()
					{
						level2Incorrect();	
					});
					break;
				}
				case 1:
				{
					$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
					extraParameters+='0);';
					frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(f1*multiplier2,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*multiplier2,numberLanguage,'interactiveObj')+'</div></div>';
					frac1='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(f1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2,numberLanguage,'interactiveObj')+'</div></div>';
					n=multiplier2;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt27'],numberLanguage,'interactiveObj'),function()
					{
						level2IncorrectAfter();	
					});
					break;
				}
			}
		}
		if(id=='opt4'+level3Counter)
		{
			switch(incorrectAttemptsL2)
			{
				case 0:
				{	
					$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
					extraParameters+='0,';
					frac1='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(f1*r,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*t,numberLanguage,'interactiveObj')+'</div></div>';
					frac2='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(f1*multiplier2,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*multiplier2,numberLanguage,'interactiveObj')+'</div></div>';
					
					incorrectAttemptsL2++;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt28'],numberLanguage,'interactiveObj'),function()
					{
						level2Incorrect();	
					});
					break;
				}
				case 1:
				{
					$('.promptContainer').css({'position':'absolute','left':'180px','top':'180px'});
					extraParameters+='0);';
					frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(f1*multiplier2,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2*multiplier2,numberLanguage,'interactiveObj')+'</div></div>';
					frac1='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(f1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(f2,numberLanguage,'interactiveObj')+'</div></div>';
					n=multiplier2;
					
					parentId=$("#"+id).parent('div').attr('id');
					$("#"+parentId).addClass('redBorder');
					$('.optionButtons'+level3Counter).attr('disabled',true);
					showPrompt(replaceDynamicText(promptArr['prompt27'],numberLanguage,'interactiveObj'),function()
					{
						level2IncorrectAfter();	
					});
					break;
				}
			}
		}
    });
	/********************************************************************/
	$("#ans").live("keypress",function(e)
	{
		$('#contentLine1').css('margin-top','0px');
		$('.promptContainer').css({'position':'absolute','left':'200px','top':'380px'});
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
            var value = $(this).val();
            if (value == "") 
			{
				$('.promptContainer').css({'position':'absolute','left':'200px','top':'380px'});
                showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
				{
					doNothing();
				});
            }
            else 
			{
				if($("#ans").val()==$('#num1').html()/($('#den1').html()/$('#den2').html()))
				{
					frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
					$(".ans").addClass('greenBorder');
					$('#').attr('readOnly',true);
					
					$('.promptContainer').css({'position':'absolute','left':'200px','top':'380px'});
					showPrompt(replaceDynamicText(promptArr['prompt17'],numberLanguage,'interactiveObj'),function()
					{
						correctAfter();	
					});
				}
				else
				{
					switch(incorrectAttemptsAfter)
					{
						case 0:
						{
							$('.promptContainer').css({'position':'absolute','left':'200px','top':'380px'});
		
							incorrectAttemptsAfter++;
							frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
							$("#ans").addClass('redBorder');
							$('#ans').attr('disabled','disabled');
							$('#ans').attr('readOnly',true);
							showPrompt(replaceDynamicText(promptArr['prompt29'],numberLanguage,'interactiveObj'),function()
							{
								L1incorrect3();
							});
							break;
						}						
						case 1:
						{
							$('.promptContainer').css({'position':'absolute','left':'200px','top':'380px'});
		
							no=$('#num1').html()/($('#den1').html()/$('#den2').html());
							incorrectAttemptsAfter++;
							$(".ans").addClass('redBorder');
							$('#ans').attr('disabled','disabled');
							$('#ans').attr('readOnly',true);
							showPrompt(replaceDynamicText(promptArr['prompt12'],numberLanguage,'interactiveObj'),function()
							{
								L1incorrect4();
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
            if(($(this).val().length == 2 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
		 	{
                return false;
            }
            setTimeout(function(){
        	txtval = $(".ans").val();
			$(".ans").attr("value",replaceDynamicText(txtval,numberLanguage,'interactiveObj'));
        },1);
        }
    });
	///////////////////////////////////////////////////////////
	$(".txt").live("keypress",function(e)
	{
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
			var value = $(this).val();
            if (value == "") 
			{
				$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
				showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
				{
					doNothing();
				});
			}
            else 
			{
				if($("#n"+(level3Counter-1)).val()==""||$("#d"+(level3Counter-1)).val()=="")
				{
					if($(this).attr("id").substr(0,1)=='d'&&$("#n"+(level3Counter-1)).val()=="")
					{
						if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
						(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
							$("#n"+(level3Counter-1)).focus();
					}
				}
				if($("#d"+(level3Counter-1)).val()==""||$("#n"+(level3Counter-1)).val()=="")
				{
					if($(this).attr("id").substr(0,1)=="n"&&$("#d"+(level3Counter-1)).val()=="")
					{
						if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
						(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
							$("#d"+(level3Counter-1)).focus();
					}
				}
				else
				{
					if(($("#n"+(level3Counter-1)).val()==$("#num1").html())&&($("#d"+(level3Counter-1)).val()==$("#den1").html()))
					{
					
						frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText($("#num1").html(),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#den1").html(),numberLanguage,'interactiveObj')+'</div></div>';
					
						//prompt for same value... If the same fraction is entered again...
						$('.txt').attr('disabled',true);
						$('.txt').addClass("redBorder");
						
						$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
						showPrompt(replaceDynamicText(promptArr['prompt47'],numberLanguage,'interactiveObj'),function()
						{
							doNothing();
						});
					}
					else if(($("#num1").html()/$("#n"+(level3Counter-1)).val()==$("#den1").html()/$("#d"+(level3Counter-1)).val())&&($("#num1").html()!=$("#n"+(level3Counter-1)).val())&&($("#den1").html()!=$("#d"+(level3Counter-1)).val())&&($("#n0").val()!=($("#num1").html()/multiplier3))&&($('#n0').val()!=0)&&($('#d0').val()!=0))
					{
						//prompt for the equivalent fraction value...
						switch(level3EquivalentFraction)
						{
							case 0:
							{
								if(incorrectAttemptsL3>0)
								{
									extraParameters+='0,';
									level3EquivalentFraction++;
									incorrectAttemptsL3++;
								
									$('.txt').addClass("redBorder");
									$('.txt').attr('disabled','disabled');
									n1=$("#num1").html();
									n2=$("#den1").html();
									
									$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
									showPrompt(replaceDynamicText(promptArr['prompt53'],numberLanguage,'interactiveObj'),function()
									{
										addSUB2();
									});
								}
								else
								{
									userResponseLevel3.push('false');
								
									extraParameters+='0,';
									attemptFlag='a';
									level3EquivalentFraction++;
									incorrectAttemptsL3++;
								
									frac1='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText($("#n0").val(),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#d0").val(),numberLanguage,'interactiveObj')+'</div></div>';
									frac2='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText($("#num1").html(),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#den1").html(),numberLanguage,'interactiveObj')+'</div></div>';
									
									$('.txt').addClass("redBorder");
									$('.txt').attr('disabled','disabled');
									
									$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
									showPrompt(replaceDynamicText(promptArr['prompt32'],numberLanguage,'interactiveObj'),function()
									{
										displayEquivalentFractionFWAP();
									});
								}
								break;
							}
							case 1:
							{
								extraParameters+='0,';
								$('.txt').addClass("redBorder");
								$('.txt').attr('disabled','disabled');
								n1=$("#fn1").html();
								n2=$("#fd1").html();
								
								$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
								showPrompt(replaceDynamicText(promptArr['prompt53'],numberLanguage,'interactiveObj'),function()
								{
									addSUB2();
								});
							}
						}
					}
					else if($("#d0").val()==0)
					{
						//case for denominator==0
						switch(divideByZeroAttempt)
						{
							case 0:
							{
								divideByZeroAttempt++;
								$('.txt').addClass("redBorder");
								$('.txt').attr('disabled','disabled');
								
								$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
								showPrompt(replaceDynamicText(promptArr['prompt50'],numberLanguage,'interactiveObj'),function()
								{
									doNothing();
								});
								break;
							}
							case 1:
							{
								userResponseLevel3.push('false');
								extraParameters+='0,';
								divideByZeroAttempt++;
								
								$('.txt').addClass("redBorder");
								$('.txt').attr('disabled','disabled');
								$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
								showPrompt(replaceDynamicText(promptArr['prompt53'],numberLanguage,'interactiveObj'),function()
								{
									addSUB2();
								});
								break;
							}
						}
					}
					else if(($("#n"+(level3Counter-1)).val()==($("#num1").html()/multiplier3))&&($("#d"+(level3Counter-1)).val()==($("#den1").html()/multiplier3)))
					{
						//case for correct answer
						$('#n0').attr('disabled','disabled');
						$('#d0').attr('disabled','disabled');
						extraParameters+='1)';
						userResponseLevel3.push('true');
						level3Score++;
						$('.txt').addClass("greenBorder");
						
						$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
						showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
						{
							level3Correct();
						});
					}
					else if((GCD([$("#num1").html(),$("#n0").val()])!=1)&&(GCD([$("#den1").html(),$("#d0").val()])!=1)&&($("#n0").val()/$("#num1").html()!=$("#d0").val()/$("#den1").html()))
					{
						//else if((GCD([$("#num1").html(),$("#n0").val()])!=1)&&(GCD([$("#den1").html(),$("#d0").val()])!=1)&&($("#n0").val()/$("#num1").html()!=$("#d0").val()/$("#den1").html()))
						//RFRA002 spec case 2...
						switch(case2)
						{
							case 0:
							{
								if(incorrectAttemptsL3>0)
								{
									extraParameters+='0,';
									incorrectAttemptsL3++;
									case2++;
									
									$('.txt').attr('disabled','disabled');
									n1=$('#fn1').html();
									n2=$('#fd1').html();
									
									$('.txt').addClass("redBorder");
									
									$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
									frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#fn1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#fd1').html(),numberLanguage,'interactiveObj')+'</div></div>';
									showPrompt(replaceDynamicText(promptArr['prompt52'],numberLanguage,'interactiveObj'),function()
									{
										addSUB2();
									});
								}
								else
								{
									userResponseLevel3.push('false');
									extraParameters+='0,';
									attemptFlag='b';
									$('.txt').attr('disabled','disabled');
									incorrectAttemptsL3++;
									case2++;
									$('.txt').addClass("redBorder");
									
									$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
									showPrompt(replaceDynamicText(promptArr['prompt51'],numberLanguage,'interactiveObj'),function()
									{
										doNothing();
									});
								}
								break;
							}
							case 1:
							{
								case2++;
								
								extraParameters+='0,';
								n1=$('#num1').html();
								n2=$('#den1').html();
								
								$('.txt').attr('disabled','disabled');
								$('.txt').addClass("redBorder");
								
								$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
								showPrompt(replaceDynamicText(promptArr['prompt52'],numberLanguage,'interactiveObj'),function()
								{
									addSUB2();
								});
								break;
							}
						}
					}
					else
					{
						//RFRA002 spec case 3...
						userResponseLevel3.push('false');
						extraParameters+='0,';
						case3++;
						if(level3EquivalentFraction>0)
						{
							n1=$("#fn1").html();
							n2=$("#fd1").html();
							frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText($("#fn1").html(),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#fd1").html(),numberLanguage,'interactiveObj')+'</div></div>';
						}
						else
						{
							n1=$("#num1").html();
							n2=$("#den1").html();
							frac='<div class="fraction"><div class="frac numerator" style="width:18px;">'+replaceDynamicText($("#num1").html(),numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#den1").html(),numberLanguage,'interactiveObj')+'</div></div>';
						}
						$('.txt').attr('disabled','disbaled');
						
						$('.txt').addClass("redBorder");
						
						$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
						showPrompt(replaceDynamicText(promptArr['prompt52'],numberLanguage,'interactiveObj'),function()
						{
							addSUB2();
						});
					}
				}
			}
			return false;
		}
		else 
		{
			if(($(this).val().length == 3 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
			{
				return false;
			}
			setTimeout(function(){
        		numer1 = $("#n"+(level3Counter-1)).attr("value");
        		denom1 = $("#d"+(level3Counter-1)).attr("value");
				$("#n"+(level3Counter-1)).attr("value",replaceDynamicText(numer1,numberLanguage,'interactiveObj'));
				$("#d"+(level3Counter-1)).attr("value",replaceDynamicText(denom1,numberLanguage,'interactiveObj'));
       		 },1);
		}
	});
	/**********************************************************************************************************************************************************************************/
	$("#txt1").live("keypress",function(e)
	{
		$('#contentLine1').css('margin-top','0px');

		$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
            var value = $(this).val();
            if (value == "") 
			{
				if(blankFlag==0)
				{
					$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
					showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
					{
						doNothing();
					});
					$("#txt2").val("");
					$("#f3").css("display","none");
					$("#eq3").css("display","none");
				}
				else
				{
					blankFlag=0;
					$("#txt2").val("");
					$("#f3").css("display","none");
					$("#eq3").css("display","none");
				}
            }
            else 
			{
				blankFlag=1;
				$("#txt2").val(replaceDynamicText($("#txt1").val(),numberLanguage,"interactiveObj"));
				if($("#sp1").html()%$("#txt1").val()==0&&$("#sp2").html()%$("#txt1").val()==0)
				{
					$('#dv1').html(replaceDynamicText($('#sp1').html()/$('#txt1').val(),numberLanguage,"interactiveObj"));
					$('#dv2').html(replaceDynamicText($('#sp2').html()/$('#txt2').val(),numberLanguage,"interactiveObj"));
					if($("#txt1").val()!=GCD[$("#sp1").html(),$("#sp2").html()])
					{
						$("#txt1").attr("disabled","disabled");
						if($("#txt1").val()==1)
						{
							no1=$('#sp1').html();
							no2=$('#sp2').html();
							
							$("#txt1").addClass("redBorder");
							$("#txt2").addClass("redBorder");
							
							$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
							showPrompt(replaceDynamicText(promptArr['prompt55'],numberLanguage,'interactiveObj'),function()
							{
								doNothing();
							});
						}
						else
						{
							if($('#txt1').val()!=GCD([$('#sp1').html(),$('#sp2').html()]))
							{
								$("#txt1").addClass("greenBorder");
								$("#txt2").addClass("greenBorder");
								
								n1=$('#txt1').val();
								a=$('#sp1').html();
								b=$('#sp2').html();
								no1=$('#dv1').html();
								no2=$('#dv2').html();
								
								frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($("#dv1").html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($("#dv2").html(),numberLanguage,'interactiveObj')+'</div></div>';
								
								$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
								showPrompt(replaceDynamicText(promptArr['prompt54'],numberLanguage,'interactiveObj'),function()
								{
									furtherAnimation();
								});
							
								$("#eq3").css("display","inline-block");
								$("#f3").css("display","inline-block");
								$('#f3').css('margin-left','2px');
							}
							else
							{
								if($('#txt1').val()==multiplier3)
								{
									correctFlag=1;
									$("#txt1").addClass("greenBorder");
									$("#txt2").addClass("greenBorder");
									
									$("#eq3").css("display","inline-block");
									$("#f3").css("display","inline-block");
									
									$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
									showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
									{
										level3Correct();
									});
								}
								else
								{
									if($('#txt1').val()==(GCD([$('#sp1').html(),$('#sp2').html()])))
									{
										$("#txt1").addClass("greenBorder");
										$("#txt2").addClass("greenBorder");
									
										$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
										showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
										{
											level3Correct();
										});
										
										$("#eq3").css("display","inline-block");
										$("#f3").css("display","inline-block");
									}
								}
							}
						}
					}
					else
					{
						$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
						showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
						{
							level3Correct();
						});
					}
				}
				else
				{
					switch(incorrectDivisorAttempt)
					{
						case 0:
						{
							incorrectDivisorAttempt++;
							no1=$("#sp1").html();
							no2=$("#sp2").html();
							
							$("#txt1").addClass("redBorder");
							$("#txt2").addClass("redBorder");
							
							factors.push($('#num1').html());
							factors.push($('#den1').html());
							listFactors=commonFactors(factors);
							$('#txt1').attr('disabled','disabled');
							
							for(i=0;i<listFactors.length;i++)
							{
								string+=listFactors[i]+',';
							}
							n1=$('#txt1').val();
							n2=$('#sp1').html();
							n3=$('#sp2').html();
							string=string.substring(0,string.length-1);
							string+='.';
							$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
							showPrompt(replaceDynamicText(promptArr['prompt56'],numberLanguage,'interactiveObj'),function()
							{
								doNothing();
							});
							break;
						}
						case 1:
						{	
							$('.emptyTextbox1').addClass('redBorder');
							$('#txt1').attr('disabled','disabled');
							
							n1=$("#sp1").html();
							n2=$("#sp2").html();
							gcd=GCD([n1,n2]);
							
							fracn='<div id="fracn" class="fraction" style="font-size:15pt;position:absolute;"><div id="c1" class="frac numerator" style="width:20px;">'+replaceDynamicText(n1,numberLanguage,'interactiveObj')+'</div><div id="c2" class="frac" >'+replaceDynamicText(n2,numberLanguage,'interactiveObj')+'</div></div>';
							fracm='<div id="fracm" class="fraction" style="font-size:15pt;position:absolute;"><div class="frac numerator" style="width:20px;">'+replaceDynamicText($("#num1").html()/multiplier3,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#den1").html()/multiplier3,numberLanguage,'interactiveObj')+'</div></div>';
							
							$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
							showPrompt(replaceDynamicText(promptArr['prompt42'],numberLanguage,'interactiveObj'),function()
							{
								level3Incorrect();
							});
							
							if((navigator.userAgent.indexOf("Android") != -1))
							{
								$('#eq6').css('position','absolute');
								$('#eq6').css('left',$('#fracn').position().left+115+"px");
								$('#eq6').css('top',$('#fracn').position().top+90+"px");
								$('#fracm').css('left',$('#fracn').position().left+150+"px");
							}
							else if((navigator.userAgent.indexOf("iPhone") != -1) ||
								(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1))
							{
								$('#eq6').css('position','absolute');
								$('#eq6').css('left',$('#fracn').position().left+75+"px");
								$('#eq6').css('top',$('#fracn').position().top+25+"px");
								$('#fracm').css('left',$('#fracn').position().left+115+"px");
							}
							else
							{
								$('#eq6').css('position','absolute');
								$('#eq6').css('left',$('#fracn').position().left+68+"px");
								$('#eq6').css('top',$('#fracn').position().top+20+"px");
								$('#fracm').css('left',$('#fracn').position().left+105+"px");
							}
							
							$('#dialogueButton').attr('disabled','true');
							$("#eq6").css("visibility","hidden");
							$("#fracm").css("visibility","hidden");
						
							var canvas1=document.createElement('canvas');
							canvas1.id="canvas1";
							canvas1.style.width="30px";
							canvas1.style.height="23px";
							canvas1.width="30";
							canvas1.height="23";
							canvas1.style.position="absolute";
							canvas1.style.top="0px";
							canvas1.style.left="0px";
							$('#c1').append(canvas1);
							
							var c1=document.getElementById("canvas1");
							var ctx1=c1.getContext("2d");
							
							ctx1.beginPath();
							var x=30;
							var y=1;
							var temp = window.setInterval(function()
							{
								ctx1.lineTo(x-=2,y++);
								ctx1.strokeStyle="red";
								ctx1.stroke();
								if(x==-20)
									window.clearInterval(temp);
							},100);
							ctx1.closePath();
							
							window.setTimeout(function()
							{
								$("#promptContainer").append('<div id="d1" style="color:#0066ff;"></div>');
								
								if((navigator.userAgent.indexOf("Android") != -1))
								{
									$("#d1").html('<font size="4px"><b>'+replaceDynamicText(n1/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d1').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+20+"px"});
								}
								else if((navigator.userAgent.indexOf("iPhone") != -1) ||
								(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1))
								{	
									$("#d1").html('<font size="4px"><b>'+replaceDynamicText(n1/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d1').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+0+"px"});
								}
								else
								{
									$("#d1").html('<font size="4px"><b>'+replaceDynamicText(n1/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d1').css({'left':$('#fracn').position().left+95+"px",'position':'absolute','top':$('#fracn').position().top+5+"px"});		
								}
								
							},2000);
							
														
							window.setTimeout(function()
							{
								var canvas1=document.createElement('canvas');
								canvas1.id="canvas2";
								canvas1.style.width="30px";
								canvas1.style.height="23px";
								canvas1.width="30";
								canvas1.height="23";
								canvas1.style.position="absolute";
								canvas1.style.top="24px";
								canvas1.style.left="0px";
								$('#c2').append(canvas1);
								
								var c1=document.getElementById("canvas2");
								var ctx1=c1.getContext("2d");
								
								ctx1.beginPath();
								var x=30;
								var y=1;
								var temp = window.setInterval(function()
								{
									ctx1.lineTo(x-=2,y++);
									ctx1.strokeStyle="red";
									ctx1.stroke();
									if(x==-20)
										window.clearInterval(temp);
								},100);
								ctx1.closePath();
							},3000);
							window.setTimeout(function()
							{
								$("#promptContainer").append('<div id="d2" style="position:absolute;color:#0066ff;"></div>');
								$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
								if((navigator.userAgent.indexOf("Android") != -1))
								{
									$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d2').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+70+"px"});
								}
								else if((navigator.userAgent.indexOf("iPhone") != -1) ||
								(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1))
								{	
									$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d2').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+60+"px"});
								}
								else
								{
									$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d2').css({'left':$('#fracn').position().left+95+"px",'position':'absolute','top':$('#fracn').position().top+30+"px"});		
								}
							},5000);
							window.setTimeout(function()
							{
								$("#eq6").css("visibility","visible");
							},6000);
							window.setTimeout(function()
							{
								$("#fracm").css("visibility","visible");
							},7000);
							window.setTimeout(function()
							{
								$('#dialogueButton').removeAttr('disabled').focus();
							},7500);
							break;
						}
					}
				}
			}
			return false;
		}
		else 
		{
            if(($(this).val().length == 2 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
		 	{
                return false;
            }
            setTimeout(function(){
        		numer1 = $("#n"+level2Counter).attr("value");
        		denom1 = $("#d"+level2Counter).attr("value");
        		txtval1 = $("#txt1").attr("value");
				$("#n"+level2Counter).attr("value",replaceDynamicText(numer1,numberLanguage,'interactiveObj'));
				$("#d"+level2Counter).attr("value",replaceDynamicText(denom1,numberLanguage,'interactiveObj'));
				$("#txt1").attr("value",replaceDynamicText(txtval1,numberLanguage,'interactiveObj'));
       		 },1);
        }
    });
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$(".emptyTextbox2").live("keypress",function(e)
	{
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack...
        if(e.keyCode == 13) 
		{
			var value = $(this).val();
            if (value == "") 
			{
				$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
				showPrompt(replaceDynamicText(promptArr['prompt30'],numberLanguage,'interactiveObj'),function()
				{
					doNothing();
				});
            }
            else 
			{
				if(($("#t1").val()=="")||($("#t2").val()==""))
				{
					if(($(this).attr("id")=="t1")&&($("#t2").val()==""))
					{
						if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
						(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
							$("#t2").focus();
					}
					else 
					{
						if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
						(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
							$("#t1").focus();
					}
				}
				else
				{
					/******to do script here*******/
					$('.emptyTextbox2').attr('disabled','disabled');
					if(($("#t1").val()==($("#num1").html()/GCD([$("#num1").html(),$("#den1").html()])))&&($("#t2").val()==($("#den1").html()/GCD([$("#num1").html(),$("#den1").html()]))))
					{
						extraParameters+='1)';
						$('.emptyTextbox2').addClass('greenBorder');
						
						$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
						showPrompt(replaceDynamicText(promptArr['prompt1'],numberLanguage,'interactiveObj'),function()
						{
							level3Correct();
						});
					}
					else
					{
							$('.emptyTextbox2').addClass('redBorder');
							$('#txt1').attr('disabled','disabled');
							
							n1=$("#sp1").html();
							n2=$("#sp2").html();
							gcd=GCD([n1,n2]);
							
							fracn='<div id="fracn" class="fraction" style="font-size:15pt;position:absolute;"><div id="c1" class="frac numerator" style="width:20px;">'+replaceDynamicText(n1,numberLanguage,'interactiveObj')+'</div><div id="c2" class="frac" >'+replaceDynamicText(n2,numberLanguage,'interactiveObj')+'</div></div>';
							fracm='<div id="fracm" class="fraction" style="font-size:15pt;position:absolute;"><div class="frac numerator" style="width:20px;">'+replaceDynamicText($("#num1").html()/multiplier3,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText($("#den1").html()/multiplier3,numberLanguage,'interactiveObj')+'</div></div>';
							
							$('.promptContainer').css({'position':'absolute','left':'170px','top':'165px'});
							showPrompt(replaceDynamicText(promptArr['prompt42'],numberLanguage,'interactiveObj'),function()
							{
								level3Incorrect();
							});
							
							if((navigator.userAgent.indexOf("Android") != -1))
							{
								$('#eq6').css('position','absolute');
								$('#eq6').css('left',$('#fracn').position().left+115+"px");
								$('#eq6').css('top',$('#fracn').position().top+75+"px");
								$('#fracm').css('left',$('#fracn').position().left+150+"px");
							}
							else if((navigator.userAgent.indexOf("iPhone") != -1) ||
								(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1))
							{
								$('#eq6').css('position','absolute');
								$('#eq6').css('left',$('#fracn').position().left+78+"px");
								$('#eq6').css('top',$('#fracn').position().top+17+"px");
								$('#fracm').css('left',$('#fracn').position().left+115+"px");
							}
							else
							{
								$('#eq6').css('position','absolute');
								$('#eq6').css('left',$('#fracn').position().left+68+"px");
								$('#eq6').css('top',$('#fracn').position().top+5+"px");
								$('#fracm').css('left',$('#fracn').position().left+105+"px");
							}
							
							$('#dialogueButton').attr('disabled','true');
							$("#eq6").css("visibility","hidden");
							$("#fracm").css("visibility","hidden");
						
							var canvas1=document.createElement('canvas');
							canvas1.id="canvas1";
							canvas1.style.width="30px";
							canvas1.style.height="23px";
							canvas1.width="30";
							canvas1.height="23";
							canvas1.style.position="absolute";
							canvas1.style.top="0px";
							canvas1.style.left="0px";
							$('#c1').append(canvas1);
							
							var c1=document.getElementById("canvas1");
							var ctx1=c1.getContext("2d");
							
							ctx1.beginPath();
							var x=30;
							var y=1;
							var temp = window.setInterval(function()
							{
								ctx1.lineTo(x-=2,y++);
								ctx1.strokeStyle="red";
								ctx1.stroke();
								if(x==-20)
									window.clearInterval(temp);
							},100);
							ctx1.closePath();
							
							window.setTimeout(function()
							{
								$("#promptContainer").append('<div id="d1" style="color:#0066ff;"></div>');
								
								if((navigator.userAgent.indexOf("Android") != -1))
								{
									$("#d1").html('<font size="4px"><b>'+replaceDynamicText(n1/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d1').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+20+"px"});
								}
								else if((navigator.userAgent.indexOf("iPhone") != -1) ||
								(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1))
								{	
									$("#d1").html('<font size="4px"><b>'+replaceDynamicText(n1/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d1').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+0+"px"});
								}
								else
								{
									$("#d1").html('<font size="4px"><b>'+replaceDynamicText(n1/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d1').css({'left':$('#fracn').position().left+95+"px",'position':'absolute','top':$('#fracn').position().top+5+"px"});		
								}
								
							},2000);
							
														
							window.setTimeout(function()
							{
								var canvas1=document.createElement('canvas');
								canvas1.id="canvas2";
								canvas1.style.width="30px";
								canvas1.style.height="23px";
								canvas1.width="30";
								canvas1.height="23";
								canvas1.style.position="absolute";
								canvas1.style.top="24px";
								canvas1.style.left="0px";
								$('#c2').append(canvas1);
								
								var c1=document.getElementById("canvas2");
								var ctx1=c1.getContext("2d");
								
								ctx1.beginPath();
								var x=30;
								var y=1;
								var temp = window.setInterval(function()
								{
									ctx1.lineTo(x-=2,y++);
									ctx1.strokeStyle="red";
									ctx1.stroke();
									if(x==-20)
										window.clearInterval(temp);
								},100);
								ctx1.closePath();
							},3000);
							window.setTimeout(function()
							{
								$("#promptContainer").append('<div id="d2" style="position:absolute;color:#0066ff;"></div>');
								$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
								if((navigator.userAgent.indexOf("Android") != -1))
								{
									$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d2').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+70+"px"});
								}
								else if((navigator.userAgent.indexOf("iPhone") != -1) ||
								(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1))
								{	
									$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d2').css({'left':$('#fracn').position().left+105+"px",'position':'absolute','top':$('#fracn').position().top+60+"px"});
								}
								else
								{
									$("#d2").html('<font size="4px"><b>'+replaceDynamicText(n2/GCD([n1,n2]),numberLanguage,"interactiveObj")+'</b></font>');
									$('#d2').css({'left':$('#fracn').position().left+95+"px",'position':'absolute','top':$('#fracn').position().top+30+"px"});		
								}
							},5000);
							window.setTimeout(function()
							{
								$("#eq6").css("visibility","visible");
							},6000);
							window.setTimeout(function()
							{
								$("#fracm").css("visibility","visible");
							},7000);
							window.setTimeout(function()
							{
								$('#dialogueButton').removeAttr('disabled').focus();
							},7500);
					}
				}
			}
			return false;
		}
		else 
		{
			if(($(this).val().length == 3 || e.keyCode < 48 || e.keyCode > 57) && e.keyCode != 9 && e.keyCode != 8) 
			{
				return false;
			}
			setTimeout(function(){
        		numer1 = $("#t1").attr("value");
        		denom1 = $("#t2").attr("value");
				$("#t1").attr("value",replaceDynamicText(numer1,numberLanguage,'interactiveObj'));
				$("#t2").attr("value",replaceDynamicText(denom1,numberLanguage,'interactiveObj'));
       		 },1);
		}
	});
}

function displayEquivalentFractionFWAP()
{
	$('.txt').removeAttr('disabled','disabled');
	$('.txt').removeClass("redBorder");
	$('#container').append('<div id="appendedFraction" class="fraction" style="width:40px;position:relative;margin-left:105px;margin-top:-170px;"><div id="fn1" class="frac numerator" style="height:25px;font-size:16pt;">'+replaceDynamicText($("#n0").val(),numberLanguage,'interactiveObj')+'</div><div id="fd1" class="frac" style="height:25px;font-size:16pt;margin-top:2px;">'+replaceDynamicText($("#d0").val(),numberLanguage,'interactiveObj')+'</div></div><p id="eq2" style="position:absolute;width:15px;top:140px;left:150px;font-size:15pt;">=</p>');
	$('.txt').val('');
	$("#fraction2").css("margin-left","68px");
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	$("#n0").focus();
}

function commonFactors(catchingFactors)
{
	for(i=1;i<=($("#sp2").html());i++)
	{
		if(($('#sp1').html()%i==0)&&($('#sp2').html()%i==0))
		{
			divisibleFactors.push(i);
		}
	}
	return divisibleFactors;
}

function addSUB2()
{
	sub2added=1;
	if(case3>0)
	{
		if(level3EquivalentFraction>0)
		{
			$("#f2").css("margin-left","160px");
			$("#f2").css("width","260px");
			$("#fraction2").remove();
			$('#f2').append('<div style="font-size:18pt;position:absolute;margin-left:10px;border-bottom:1px solid black;width:168px;">(&nbsp;<span id="sp1" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#fn1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp;<input type="text" id="txt1" style="position:absolute;top:0px;margin-left:5px;" pattern="[0-9]*" class="emptyTextbox1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div><div id="nm1" style="font-size:18pt;position:absolute;margin-top:28px;margin-left:10px;width:168px;">(&nbsp;<span id="sp2" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#fd1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp<input type="text" pattern="[0-9]*" id="txt2" style="position:absolute;top:4px;margin-left:5px;" class="emptyTextbox1" disabled="true" /><span style="position:absolute;left:155px;">)</span></div><span id="eq3" style="text-align:center;display:inline-block;width:18px;margin-left:185px;margin-top:15px;font-size:18pt;display:none;">=</span><div id="f3" class="fraction" style="font-size:18pt;display:none;margin-top:-7px;margin-left:2px;"><div id="dv1" class="frac numerator"></div><div id="dv2" class="frac"></div></div>');
			if(language == "gujarati")
			{
				$('#nm1').css({"margin-top":"30px"});
				$('#f2').css({"margin-top":"39px"});
				$('#eq3').css({"margin-top":"15px"});
				$('#nextDisplay').css({"margin-top":"50px"});
			}
			$("#appendedFraction").css("margin-top","-209px");
			if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
					(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
			$("#txt1").focus();
		}
		else
		{
			$("#f2").css("margin-left","100px");
			$("#f2").css("width","260px");
			$("#fraction2").remove();
			$('#f2').append('<div style="font-size:18pt;position:absolute;margin-left:10px;border-bottom:1px solid black;width:168px;">(&nbsp;<span id="sp1" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#num1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp;<input type="text" id="txt1" style="position:absolute;top:0px;margin-left:5px;" pattern="[0-9]*" class="emptyTextbox1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div><div id="nm1" style="font-size:18pt;position:absolute;margin-top:28px;margin-left:10px;width:168px;">(&nbsp;<span id="sp2" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#den1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp<input type="text" pattern="[0-9]*" id="txt2" style="position:absolute;top:4px;margin-left:5px;" class="emptyTextbox1" disabled="true" /><span style="position:absolute;left:155px;">)</span></div><span id="eq3" style="text-align:center;display:inline-block;width:18px;margin-left:185px;margin-top:15px;font-size:18pt;display:none;">=</span><div id="f3" class="fraction" style="font-size:18pt;display:none;margin-top:-7px;margin-left:2px;"><div id="dv1" class="frac numerator"></div><div id="dv2" class="frac"></div></div>');
			if(language == "gujarati")
			{
				$('#nm1').css({"margin-top":"30px"});
				$('#f2').css({"margin-top":"39px"});
				$('#eq3').css({"margin-top":"15px"});
				$('#nextDisplay').css({"margin-top":"50px"});
			}
			$("#appendedFraction").css("margin-top","-170px");
			if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
					(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
			$("#txt1").focus();
		}
	}
	else if(level3EquivalentFraction>0&&incorrectAttemptsL3>0&&attemptFlag=='a')
	{
		$("#f2").css("margin-left","160px");
		$("#f2").css("width","250px");
		$("#fraction2").remove();
		$('#f2').append('<div style="font-size:18pt;position:absolute;margin-left:10px;border-bottom:1px solid black;width:168px;">(&nbsp;<span id="sp1" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#fn1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp;<input type="text" id="txt1" style="position:absolute;top:0px;margin-left:5px;" pattern="[0-9]*" class="emptyTextbox1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div><div id="nm1" style="font-size:18pt;position:absolute;margin-top:28px;margin-left:10px;width:168px;">(&nbsp;<span id="sp2" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#fd1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp<input type="text" pattern="[0-9]*" id="txt2" style="position:absolute;top:4px;margin-left:5px;" class="emptyTextbox1" disabled="true" /><span style="position:absolute;left:155px;">)</span></div><span id="eq3" style="text-align:center;display:inline-block;width:18px;margin-left:185px;margin-top:15px;font-size:18pt;display:none;">=</span><div id="f3" class="fraction" style="font-size:18pt;display:none;margin-top:-7px;margin-left:2px;"><div id="dv1" class="frac numerator"></div><div id="dv2" class="frac"></div></div>');
		if(language == "gujarati")
			{
				$('#nm1').css({"margin-top":"30px"});
				$('#f2').css({"margin-top":"39px"});
				$('#eq3').css({"margin-top":"15px"});
				$('#nextDisplay').css({"margin-top":"50px"});
			}
		$("#appendedFraction").css("margin-top","-209px");
		if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
		$("#txt1").focus();
	}
	else
	{
		$("#f2").css("margin-left","100px");
		$("#f2").css("width","250px");
		$("#fraction2").remove();
		$('#f2').append('<div style="font-size:18pt;position:absolute;margin-left:10px;border-bottom:1px solid black;width:168px;">(&nbsp;<span id="sp1" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#num1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp;<input type="text" id="txt1" style="position:absolute;top:0px;margin-left:5px;" pattern="[0-9]*" class="emptyTextbox1" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div><div id="nm1" style="font-size:18pt;position:absolute;margin-top:28px;margin-left:10px;width:168px;">(&nbsp;<span id="sp2" style="text-align:center;display:inline-block;width:32px;">'+replaceDynamicText($("#den1").html(),numberLanguage,'interactiveObj')+'</span>&nbsp;&#247;&nbsp<input type="text" pattern="[0-9]*" id="txt2" style="position:absolute;top:4px;margin-left:5px;" class="emptyTextbox1" disabled="true" /><span style="position:absolute;left:155px;">)</span></div><span id="eq3" style="text-align:center;display:inline-block;width:18px;margin-left:185px;margin-top:15px;font-size:18pt;display:none;">=</span><div id="f3" class="fraction" style="font-size:18pt;display:none;margin-top:-7px;margin-left:7px;"><div id="dv1" class="frac numerator"></div><div id="dv2" class="frac"></div></div>');
		if(language == "gujarati")
			{
				$('#nm1').css({"margin-top":"30px"});
				$('#f2').css({"margin-top":"39px"});
				$('#eq3').css({"margin-top":"15px"});
				$('#nextDisplay').css({"margin-top":"50px"});
			}
		$("#appendedFraction").css("margin-top","-170px");
		if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
		$("#txt1").focus();
	}
	n1=$('#sp1').html();
	n2=$('#sp2').html();
	
	if(level3EquivalentFraction>0)
	{
		n1=$('#sp1').html();
		n2=$('#sp2').html();
		$('#instText').html('<font size="4px"><b>('+replaceDynamicText(promptArr['prompt57'],numberLanguage,'interactiveObj')+')</b></font>');
	}
	else
	{
		n1=$('#sp1').html();
		n2=$('#sp2').html();
		$('#instText').html('<font size="4px"><b>('+replaceDynamicText(promptArr['prompt58'],numberLanguage,'interactiveObj')+')</b></font>');
	}
	$('#instText').css('display','block');
	$('#instText').fadeOut('fast').fadeIn('fast').fadeOut('fast').fadeIn('fast');
}

function level3Incorrect()
{	
	if((level3BCounter)>=2)
	{
		///////setting parameters
		if((level3BCounter)>=4)
		{
			completed=1;
						this.lastlevelcleared=3;
						interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level3Score;
						levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
						interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
						interactiveObj.setOutputParameters();
		}
		else
		{
			if((userResponseLevel3[level3Count-1]=='true'&&userResponseLevel3[level3Count-2]=='true'))
			{
				completed=1;
						this.lastlevelcleared=3;
						interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level3Score;
						levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
						if(level3Score==4)
							interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
						else
							interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
						interactiveObj.setOutputParameters();
			}/////////////////////////
			else
			{
				if(extraParameters.slice(-1)==',')
				{
					extraParameters=extraParameters.substring(0,extraParameters.length-1);
					extraParameters+=')';
				}
				window.setTimeout(function()
				{
					$('#nextL3').css('display','block');
					$('#nextL3').focus();
				},1000);
			}
		}
	}
	else
	{
		if(extraParameters.slice(-1)==',')
		{
			extraParameters=extraParameters.substring(0,extraParameters.length-1);
			extraParameters+=')';
		}
		window.setTimeout(function()
		{
			$('#nextL3').css('display','block');
			$('#nextL3').focus();
		},1000);
	}
	$('#d1').remove();
	$('#d2').remove();
	
	if(incorrectDivisorAttempt>0)
	{
		if(sub2added==1)
		{
			$('.emptyTextbox1').removeClass('redBorder');
			$('.emptyTextbox1').addClass('blueBorder');
			$('#txt1').val(GCD([$('#sp1').html(),$('#sp2').html()]));
			$('#txt2').val(GCD([$('#sp1').html(),$('#sp2').html()]));
			$('#eq3').css('display','inline-block');
			$("#f3").css("display","inline-block");
			$('#eq3').css({"margin-top":"13px"});
			
			$('#dv1').html(replaceDynamicText($('#sp1').html()/$('#txt1').val(),numberLanguage,"interactiveObj"));
			$('#dv2').html(replaceDynamicText($('#sp2').html()/$('#txt2').val(),numberLanguage,"interactiveObj"));
		}
		else
		{
			$('.emptyTextbox1').removeClass('redBorder');
			$('.emptyTextbox1').addClass('blueBorder');
			$('#txt1').val(GCD([$('#num1').html(),$('#den1').html()]));
			$('#txt2').val(GCD([$('#num1').html(),$('#den1').html()]));
			$('#eq3').css('display','inline-block');
			$("#f3").css("display","inline-block");
			$('#eq3').css({"margin-top":"13px"});
			$('#dv1').html(replaceDynamicText($('#sp1').html()/$('#txt1').val(),numberLanguage,"interactiveObj"));
			$('#dv2').html(replaceDynamicText($('#sp2').html()/$('#txt2').val(),numberLanguage,"interactiveObj"));
		}
	}
	else
	{
		if(sub2added==1)
		{
			$('.emptyTextbox2').removeClass('redBorder');
			$('.emptyTextbox2').addClass('blueBorder');
			$('#t1').val(replaceDynamicText($("#sp1").html()/GCD([$('#sp1').html(),$('#sp2').html()]),numberLanguage,"interactiveObj"));
			$('#t2').val(replaceDynamicText($("#sp2").html()/GCD([$('#sp1').html(),$('#sp2').html()]),numberLanguage,"interactiveObj"));
		}
		else
		{
			$('.emptyTextbox2').removeClass('redBorder');
			$('.emptyTextbox2').addClass('blueBorder');
			$('#t1').val(replaceDynamicText($("#num1").html()/GCD([$('#num1').html(),$('#den1').html()]),numberLanguage,"interactiveObj"));
			$('#t2').val(replaceDynamicText($("#den1").html()/GCD([$('#num1').html(),$('#den1').html()]),numberLanguage,"interactiveObj"));
		}
	}
	
	n1=$('#num1').html()/multiplier3;
	n2=$('#den1').html()/multiplier3;
	frac1='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText($('#num1').html()/multiplier3,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html()/multiplier3,numberLanguage,'interactiveObj')+'</div></div>';
	frac2='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
	$('#message').html(replaceDynamicText(promptArr['prompt59'],numberLanguage,'interactiveObj'));
}

function furtherAnimation()
{
	furtherAnimationFlag=1;
	disabledFlag=1;
	$('#appendedFraction').css('margin-top','-283px');
	$("#nextDisplay").html('<div id="next1" class="fraction" style="font-size:16pt;"><div class="frac numerator" style="width:30px;">'+replaceDynamicText($("#num1").html(),numberLanguage,"interactiveObj")+'</div><div class="frac" style="width:30px;">'+replaceDynamicText($("#den1").html(),numberLanguage,"interactiveObj")+'</div></div><span id="eq4" style="text-align:center;display:inline-block;width:18px;font-size:16pt;margin-left:3px;top:15px;position:absolute;">=</span><div id="next2" class="fraction" style="font-size:16pt;margin-left:30px;"><div id="s1" class="frac numerator" style="width:35px;">'+replaceDynamicText($("#dv1").html(),numberLanguage,"interactiveObj")+'</div><div id="s2" class="frac" style="width:35px;">'+replaceDynamicText($("#dv2").html(),numberLanguage,"interactiveObj")+'</div></div><span id="eq5" style="text-align:center;display:inline-block;width:18px;font-size:18pt;margin-left:7px;top:15px;position:absolute;">=</span><div id="next3" class="fraction" style="font-size:18pt;margin-left:30px;"><div class="frac numerator"><input type="text" pattern="[0-9]*" id="t1" class="emptyTextbox2" style="top:-5px;"/></div><div class="frac"><input type="text" id="t2" class="emptyTextbox2" style="top:3px;" pattern="[0-9]*"></div></div>');
	if(!(navigator.userAgent.indexOf("iPad") != -1))
		$("#t1").focus();
}

function GCD(nums)
{
		if(!nums.length)
                return 0;
        for(var r, a, i = nums.length - 1, GCDNum = nums[i]; i;)
                for(a = nums[--i]; r = a % GCDNum; a = GCDNum, GCDNum = r);
        return GCDNum;
}

function doNothing()
{
	/*if($('#t1').val()=="" && $('#t2').val()=="")
	{	
		if(!(navigator.userAgent.indexOf("iPad") != -1))
			$("#t1").focus();
	}	
	else
	{*/
		if(disabledFlag=0)
		{
			if(!(navigator.userAgent.indexOf("iPad") != -1))
				$('#txt1').focus();
			$('.emptyTextbox1').removeClass('redBorder');
			$('#txt2').removeClass('redBorder');
			$('.emptyTextbox1').removeClass('blueBorder');
			$('.emptyTextbox1').removeClass('greenBorder');
			$('#eq3').css('display','none');
			$('#f3').css('display','none');
			$("#txt1").removeAttr("disabled","disabled");
		}
		else
		{
			$('.emptyTextbox1').removeClass('redBorder');
			$('.emptyTextbox1').val('');
			$('#txt1').removeAttr('disabled','disabled');
			if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
			$('#txt1').focus();
		}
	//}
	$('.txt').removeAttr('disabled','disabled');
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	$('.emptyTextbox1').removeClass('redBorder');
	$('#txt2').removeClass('redBorder');
	$('.emptyTextbox1').removeClass('blueBorder');
	$('.txt').removeAttr('disablded','disabled');
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	{
		$(".textbox1").focus();
		$("#n"+(level3Counter-1)).focus();
		$("#ans").focus();
		$("#t1").focus();
	}
	$("#n"+(level3Counter-1)).removeAttr("readOnly");
	$("#n"+(level3Counter-1)).removeClass('redBorder');
	$("#d"+(level3Counter-1)).removeClass('redBorder');
	$('#txt1').removeClass('redBorder');
	emptyValue();
}

function emptyValue()
{
	$("#n"+(level3Counter-1)).val('');
	$("#d"+(level3Counter-1)).val('');
}

function goAhead()
{
	$('#numText').removeAttr('disabled');
	$('#denText').removeAttr('disabled');
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	$('#numText').focus();
}

function level2Correct()
{
	window.setTimeout(function()
	{
		$('#nextL3').css('display','block');
		$('#nextL3').focus();
	},1000);
}

function level3Correct()
{
	if((level3BCounter)>=2)
	{
		///////setting parameters
		if((level3BCounter)>=4)
		{
			completed=1;
						this.lastlevelcleared=3;
						interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level3Score;
						levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
						interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
						interactiveObj.setOutputParameters();
		}
		else
		{
			if((userResponseLevel3[level3Count-1]=='true'&&userResponseLevel3[level3Count-2]=='true'))
			{
				completed=1;
						this.lastlevelcleared=3;
						interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level3Score;
						levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
						if(level3Score==4)
							interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
						else
							interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
						interactiveObj.setOutputParameters();
			}/////////////////////////
			else
			{
				if(extraParameters.slice(-1)==',')
				{
					extraParameters=extraParameters.substring(0,extraParameters.length-1);
					extraParameters+=')';
				}
				window.setTimeout(function()
				{
					$('#nextL3').css('display','block');
					$('#nextL3').focus();
				},1000);
			}
		}
	}
	else
	{
		if(extraParameters.slice(-1)==',')
		{
			extraParameters=extraParameters.substring(0,extraParameters.length-1);
			extraParameters+=')';
		}
		window.setTimeout(function()
		{
			$('#nextL3').css('display','block');
			$('#nextL3').focus();
		},1000);
	}
	
	n1=$('#num1').html()/multiplier3;
	n2=$('#den1').html()/multiplier3;
	frac1='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText($('#num1').html()/multiplier3,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html()/multiplier3,numberLanguage,'interactiveObj')+'</div></div>';
	frac2='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den1').html(),numberLanguage,'interactiveObj')+'</div></div>';
	$('#message').html(replaceDynamicText(promptArr['prompt59'],numberLanguage,'interactiveObj'));
	
}

function correctBefore()
{	
	$('.promptContainer').css('margin-top','0px');
	$('.promptContainer').css('margin-left','0px');
	
	$("#txt").blur();

	$('#contentLine,#contentLine2,#contentLine1').show();
	frac12='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#num1').html(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html()*multiplier1,numberLanguage,'interactiveObj')+'</div></div>';
	if(incorrectAttemptsBefore==0)
	{
		frac='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac1='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(5*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(5*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac2='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(4*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(4*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac3='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(3*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(3*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac4='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText(2*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(2*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac5='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText($('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		
		$('.textbox1').addClass('greenBorder');
		
		$('#contentLine').html('<b>'+replaceDynamicText(promptArr['prompt43'],numberLanguage,'interactiveObj')+'</b>');
		if(incorrectAttemptsBefore==1)
			$('#contentLine').css('margin-top','200px');
		window.setTimeout(function()
		{
			$('#contentLine1').html('<b>'+replaceDynamicText(promptArr['prompt3'],numberLanguage,'interactiveObj')+'</b>');
			window.setTimeout(function()
			{
				$('#contentLine2').html('<b>'+replaceDynamicText(promptArr['prompt4'],numberLanguage,'interactiveObj')+'</b>');
				window.setTimeout(function()
				{
					$('#next').css('display','block');
					$('#next').focus();
				},2000);
			},2000);
		},2000);
	}
	if(incorrectAttemptsBefore>0)
	{
		$("#txt").val($('#num1').html()/($('#den1').html()/$('#den2').html()));
		$('#contentLine').css('margin-top','240px');
		frac='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText($('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac1='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText(5*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(5*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac2='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText(4*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(4*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac3='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText(3*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(3*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac4='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText(2*$('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(2*$('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		frac5='<div class="fraction" ><div class="frac numerator" >'+replaceDynamicText($('#txt').val(),numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText($('#den2').html(),numberLanguage,'interactiveObj')+'</div></div>';
		
		if(incorrectAttemptsBefore==3)
		{
			$('.textbox1').removeClass('redBorder');
			$('.textbox1').removeClass('greenBorder');
			$('.textbox1').removeClass('blueBorder');
			$(".textbox1").addClass('blueBorder');
			$("#txt").val($('#num1').html()/($('#den1').html()/$('#den2').html()));
			$('#contentLine').css('margin-top','240px');
			$('.promptText').css('width','auto');
			$('.promptText').css('height','auto');
		}
		if(incorrectAttemptsBefore==2)
		{
			$('#txt').addClass('greenBorder');
			$('#contentLine').css('margin-top','0px');
		}
		$('#contentLine').html('<font size="4px"><b>'+replaceDynamicText(promptArr['prompt43'],numberLanguage,'interactiveObj')+'</b></font>');
		window.setTimeout(function()
		{
			$('#contentLine1').html('<font size="4px"><b>'+replaceDynamicText(promptArr['prompt3'],numberLanguage,'interactiveObj')+'</b></font>');	
		},2000);
		window.setTimeout(function()
		{
			$('#contentLine2').html('<font size="4px"><b>'+replaceDynamicText(promptArr['prompt4'],numberLanguage,'interactiveObj')+'</b></font>',function(){
			});
		},3500);
		window.setTimeout(function()
		{
			$('#next').css('display','block');
			$('#next').focus();
		},5000);
	}
}

function L1incorrect1()
{
	$('#txt').removeAttr('disabled');
	$('#txt').val('');
	$('.textbox1').removeClass('redBorder');
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	$('#txt').focus();
}

function correctAfter()
{
	$('#txt').removeAttr('disabled');
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	$('#txt').focus();
	$('.hand').css('display','block');
}

function level2IncorrectAfter()
{
	$('.optionButtons'+level3Counter).prop('checked',false);
	$('#opt1'+level3Counter).prop('checked',true);
	
	parentId1=$("#opt1"+level3Counter).parent('div').attr('id');
	$("#"+parentId1).addClass('blueBorder');

	parentId2=$("#opt2"+level3Counter).parent('div').attr('id');
	$("#"+parentId2).removeClass('blueBorder');
	$("#"+parentId2).removeClass('redBorder');
	$("#"+parentId2).removeClass('greenBorder');
	
	parentId2=$("#opt3"+level3Counter).parent('div').attr('id');
	$("#"+parentId2).removeClass('blueBorder');
	$("#"+parentId2).removeClass('redBorder');
	$("#"+parentId2).removeClass('greenBorder');
	
	parentId2=$("#opt4"+level3Counter).parent('div').attr('id');
	$("#"+parentId2).removeClass('blueBorder');
	$("#"+parentId2).removeClass('redBorder');
	$("#"+parentId2).removeClass('greenBorder');
	
	frac='<div class="fraction" style="width:45px;"><div class="frac numerator">'+replaceDynamicText(multiplier2*f1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText(multiplier2*f2,numberLanguage,'interactiveObj')+'</div></div>';
	no1=f1*multiplier2;
	no2=f2*multiplier2;
	n1=m1;
	frac1='<font color="#4ecae8"><div class="fraction" ><div id="num1" class="frac numerator" ><b>'+replaceDynamicText(multiplier2*f1,numberLanguage,'interactiveObj')+'</b></div><div id="den1" class="frac"><b>'+replaceDynamicText(multiplier2*f2,numberLanguage,'interactiveObj')+'</b></div></div></font>';
	frac2='<font color="#4ecae8"><div class="fraction" ><div class="frac numerator"><b>'+replaceDynamicText((multiplier2*f1)/n1,numberLanguage,'interactiveObj')+'</b></div><div class="frac"><b>'+replaceDynamicText((multiplier2*f2)/n1,numberLanguage,'interactiveObj')+'</b></div></div></font>';
	num1=(multiplier2*f1)/n1;
	num2=(multiplier2*f2)/n1;
	n2=m2;
	frac3='<font color="#4ecae8"><div class="fraction" ><div id="num2" class="frac numerator"><b>'+replaceDynamicText(((multiplier2*f1)/n1),numberLanguage,'interactiveObj')+'</b></div><div id="den2" class="frac"><b>'+replaceDynamicText(((multiplier2*f2)/n1),numberLanguage,'interactiveObj')+'</b></div></div></font>';
	frac4='<font color="#4ecae8"><div id="frac4" class="fraction" style="width:45px;display:none;position:absolute;left:100px;top:0px;"><div id="num2" class="frac numerator"><b>'+replaceDynamicText((((multiplier2*f1)/n1)/n2),numberLanguage,'interactiveObj')+'</b></div><div id="den2" class="frac"><b>'+replaceDynamicText((((multiplier2*f2)/n1)/n2),numberLanguage,'interactiveObj')+'</b></div></div></font>';
	
	window.setTimeout(function()
	{
		$('#contentLine1').html(replaceDynamicText(promptArr['prompt22'],numberLanguage,'interactiveObj'));
		$('#contentLine1').css('display','block');
	},1000);
	window.setTimeout(function()
	{
		$('#contentLine2').html(replaceDynamicText(promptArr['prompt23'],numberLanguage,'interactiveObj'));
		$('#contentLine2').css('display','block');
	},3000);
	
	window.setTimeout(function()
	{
		$('#contentLine3').append(frac1+"<span id='equals1' style='position:absolute;left:70px;top:5px;color:#4ecae8;display:none;'><b>=</b></span><span id='frac2' style='position:absolute;top:0px;left:100px;display:none;'>"+frac2+"</span>");
		$('#contentLine3').css('display','block');
		
		var canvas=document.createElement('canvas');
		canvas.id="lineLayer1";
		canvas.width="45";
		canvas.style.top="-2px";
		canvas.style.left="0px";
		canvas.height="20";
		canvas.style.position="absolute";
		canvas.style.zIndex="1";
		$('#num1').append(canvas);
		var c1=document.getElementById("lineLayer1");
		var ctx1=c1.getContext("2d");
		ctx1.beginPath();
		var x=45;
		var y=1;
		var temp = window.setInterval(function(){
			ctx1.lineTo(x-=2,y++);
			ctx1.strokeStyle="red";
			ctx1.lineWidth=2;
			ctx1.stroke();
			if(y==19)
				window.clearInterval(temp);
		},100);
		ctx1.closePath();
		
		$('#contentLine3').append('<div id="numerator1" style="display:none;position:relative;margin-top:-60px;margin-left:50px;width:30px;height:25px;"></div>');
		$('#contentLine3').append('<div id="denominator1" style="display:none;position:relative;margin-top:30px;margin-left:50px;width:30px;height:25px;"></div>');
		$('#numerator1').html('<b><font color="#4ecae8" size="4px"><b>'+replaceDynamicText(((multiplier2*f1)/n1),numberLanguage,'interactiveObj')+'</b></font></b>');
		$('#denominator1').html('<b><font color="#4ecae8" size="4px"><b>'+replaceDynamicText(((multiplier2*f2)/n1),numberLanguage,'interactiveObj')+'</b></font></b>');
		window.setTimeout(function()
		{
			$('#numerator1').css('display','block');
		},2500);
		window.setTimeout(function()
		{
			var canvas=document.createElement('canvas');
			canvas.id="lineLayer2";
			canvas.width="45";
			canvas.style.top="20px";
			canvas.style.left="0px";
			canvas.height="20";
			canvas.style.position="absolute";
			canvas.style.zIndex="1";
			$('#den1').append(canvas);
			var c2=document.getElementById("lineLayer2");
			var ctx2=c2.getContext("2d");
			ctx2.beginPath();
			var x=45;
			var y=1;
			var temp = window.setInterval(function(){
				ctx2.lineTo(x-=2,y++);
				ctx2.strokeStyle="red";
				ctx2.lineWidth=2;
				ctx2.stroke();
				if(y==19)
					window.clearInterval(temp);
			},100);
		ctx2.closePath();
		},3000);
		window.setTimeout(function()
		{
			$('#denominator1').css('display','block');
		},5500);
	},5000);
	window.setTimeout(function()
	{
		$('#equals1').css('display','block');
		$('#frac2').css('display','block');
	},11000);
	
	window.setTimeout(function()
	{
		$('#contentLine4').html(replaceDynamicText(promptArr['prompt24'],numberLanguage,'interactiveObj'));
		$('#contentLine4').css('display','block');
	},12500);
	window.setTimeout(function()
	{
		$('#contentLine5').append('<div>'+frac3+'</div><span id="equals2" style="position:absolute;left:70px;top:5px;color:#4ecae8;display:none;"><b>=</b></span>'+frac4);
		$('#contentLine5').css('display','block');
		var canvas=document.createElement('canvas');
		canvas.id="lineLayer3";
		canvas.width="45";
		canvas.style.top="0px";
		canvas.style.left="0px";
		canvas.height="20";
		canvas.style.position="absolute";
		canvas.style.zIndex="1";
		$('#num2').append(canvas);
		var c3=document.getElementById("lineLayer3");
		var ctx3=c3.getContext("2d");
		ctx3.beginPath();
		var x=45;
		var y=1;
		var temp = window.setInterval(function(){
			ctx3.lineTo(x-=2,y++);
			ctx3.strokeStyle="red";
			ctx3.lineWidth=2;
			ctx3.stroke();
			if(y==19)
				window.clearInterval(temp);
		},100);
		ctx3.closePath();
		$('#contentLine5').append('<div id="numerator2" style="display:none;position:absolute;top:-20px;left:50px;width:30px;height:25px;"></div>');
		$('#contentLine5').append('<div id="denominator2" style="display:none;position:absolute;top:30px;left:50px;height:25px;width:30px;"></div>');
	},14000);
	window.setTimeout(function()
	{
		$('#numerator2').css('display','block');
		$('#numerator2').html('<font color="#4ecae8"><b>'+replaceDynamicText((((multiplier2*f1)/n1)/n2),numberLanguage,'interactiveObj')+'</b></font>');
	},15500);
	window.setTimeout(function()
	{
		var canvas=document.createElement('canvas');
		canvas.id="lineLayer4";
		canvas.width="45";
		canvas.style.top="23px";
		canvas.style.left="0px";
		canvas.height="20";
		canvas.style.position="absolute";
		canvas.style.zIndex="1";
		$('#den2').append(canvas);
		var c4=document.getElementById("lineLayer4");
		var ctx4=c4.getContext("2d");
		ctx4.beginPath();
		var x=45;
		var y=1;
		var temp = window.setInterval(function(){
			ctx4.lineTo(x-=2,y++);
			ctx4.strokeStyle="red";
			ctx4.lineWidth=2;
			ctx4.stroke();
			if(y==19)
				window.clearInterval(temp);
		},100);
		ctx4.closePath();
	},17000);
	window.setTimeout(function()
	{
		$('#denominator2').css('display','block');
		$('#denominator2').html('<font color="#4ecae8"><b>'+replaceDynamicText((((multiplier2*f2)/n1)/n2),numberLanguage,'interactiveObj')+'</b></font>');
	},19000);
	window.setTimeout(function()
	{
		$('#equals2').css('display','block');
		$('#frac4').css('display','block');
	},20000);
	window.setTimeout(function()
	{
		frac5='<div id="frac4" class="fraction" style="width:45px;position:relative;left:0px;top:0px;"><div id="num2" class="frac numerator">'+replaceDynamicText((((multiplier2*f1)/n1)/n2),numberLanguage,'interactiveObj')+'</div><div id="den2" class="frac">'+(((multiplier2*f2)/n1)/n2)+'</div></div>';
		$('#contentLine6').html(replaceDynamicText(promptArr['prompt25'],numberLanguage,'interactiveObj'));
		$('#contentLine6').css('display','block');
	},21000);
	window.setTimeout(function()
	{
		frac1='<div class="fraction" ><div id="num1" class="frac numerator" >'+replaceDynamicText(multiplier2*f1,numberLanguage,'interactiveObj')+'</div><div id="den1" class="frac">'+replaceDynamicText(multiplier2*f2,numberLanguage,'interactiveObj')+'</div></div>';
		frac2='<div class="fraction" ><div class="frac numerator">'+replaceDynamicText((multiplier2*f1)/n1,numberLanguage,'interactiveObj')+'</div><div class="frac">'+replaceDynamicText((multiplier2*f2)/n1,numberLanguage,'interactiveObj')+'</div></div>';
		frac3='<div class="fraction" ><div id="num2" class="frac numerator">'+replaceDynamicText((((multiplier2*f1)/n1)/n2),numberLanguage,'interactiveObj')+'</div><div id="den2" class="frac">'+replaceDynamicText((((multiplier2*f2)/n1)/n2),numberLanguage,'interactiveObj')+'</div></div>';
		
		$('#contentLine7').html(replaceDynamicText(promptArr['prompt26'],numberLanguage,'interactiveObj'));
		$('#contentLine7').css('display','block');
	},22000);
	window.setTimeout(function()
	{
		$('#nextL3').css('display','block');
		$('#nextL3').focus();
	},24000);
}

function L1incorrect4()
{
	var canvas=document.getElementById('chocolate2')
	var ctx=canvas.getContext('2d');
	var partLength1=240/($('#den2').html());
	
	var a=0.005;
	var temp=window.setInterval(function()
	{
		ctx.beginPath();
		ctx.moveTo(0,0);
		ctx.fillStyle="rgba(255,255,255,"+a+")";
		ctx.fillRect(0,0,$('#num1').html()/($('#den1').html()/$('#den2').html())*partLength1,35);
		ctx.fill();
		ctx.closePath();
		a+=0.005;
		if(a>=0.09)
		{
			window.clearInterval(temp);
		}
	},100);
	
	$('.ans').removeClass('redBorder');
	$('.ans').addClass('blueBorder');
	
	//$('#ans').focus();
	$('#ans').val($('#num1').html()/($('#den1').html()/$('#den2').html()));
	$('#txt').removeAttr('disabled');
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
	$('#txt').focus();
	$('.hand').css('display','block');
}

function level2Incorrect()
{
	$('.optionButtons'+level3Counter).prop('checked',false);
	$('.optionButtons'+level3Counter).removeAttr('disabled');
	$('#'+parentId).removeClass('redBorder');
}

function L1incorrect3()
{
	$('#ans').removeAttr('disabled','disabled');
	$('#ans').val('');
	$('.ans').removeClass('redBorder');
	//$('#ans').focus();
	
			var partLength=240/$('#den2').html();
			var multiplier1=($('#num1').html()/($('#den1').html()/$('#den2').html()));
			var dv=document.createElement('div');
			dv.setAttribute("id","fadedParts");
			dv.style.position="absolute";
			//dv.style.border="2px solid black";
			dv.style.left="455px";
			dv.style.top="153px";
			dv.style.width=(multiplier1*partLength)+"px";
			dv.style.height="35px";
			dv.style.backgroundColor="#daa794";
			//dv.style.border="1px solid black";
			dv.style.zIndex="3";
			dv.style.opacity="0.8";
			$('#container').append(dv);
			$("#fadedParts").animate({top:"201px"},1500);
			$("#ans").css("box-shadow","0px 0px 10px white");
			$("#ans").val("");
			window.setTimeout(function()
			{
				$('#fadedParts').fadeOut();
				$('#fadedParts').remove();
				$('#ans').removeAttr('readOnly');
				if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
					$('#ans').focus();
			},3500);
}

function L1incorrect2()
{
	$('#txt').val('');
	$('.textbox1').removeClass('redBorder');
	
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
		$('#txt').focus();
	
	$('#canvas1').css('display','block');
	$('#canvas2').css('display','block');
	
	var canvas1=document.createElement('canvas');
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
	$('#tbl').css('display','block');
		
	for(i=1;i<=5;i++)
	{
		$("#row"+i).css("opacity","0");
	}
	
	var c1=document.getElementById("chocolate1");
	ctx1=c1.getContext("2d");
	
	var c2=document.getElementById("chocolate2");
	ctx2=c2.getContext("2d");
	
	no=no2;
	frac='<div id="frac4" class="fraction" style="width:45px;position:relative;left:0px;top:0px;"><div id="num2" class="frac numerator">'+(multiplier1*no1)+'</div><div id="den2" class="frac">'+(multiplier1*no2)+'</div></div>';
	$("#row1").html('<b>'+replaceDynamicText(promptArr['prompt6'],numberLanguage,'interactiveObj')+'</b>');
	$("#row2").html('<b>'+replaceDynamicText(promptArr['prompt7'],numberLanguage,'interactiveObj')+'</b>');
	$("#row3").html('<b>'+replaceDynamicText(promptArr['prompt8'],numberLanguage,'interactiveObj')+'</b>');
	$("#row4").html('<b>'+replaceDynamicText(promptArr['prompt9'],numberLanguage,'interactiveObj')+'</b>');
	$("#row5").html('<b>'+replaceDynamicText(promptArr['prompt10'],numberLanguage,'interactiveObj')+"<input type='text' id='ans' value='' pattern='[0-9]*' class='ans' style='display:none;' />"+'</b>');
	
	var partLength1=240/($('#den1').html());
	window.setTimeout(function()
	{
		for(i=0;i<=4;i++)
		{
			$('#row' + (i+1)).delay((i)*4000).animate({opacity: 1},500);
		}
	},10);
	window.setTimeout(function()
	{		
			$("#ans").css("display","inline-block").focus();
	},16400);
	
	window.setTimeout(function()
	{
		$('#john').css('display','block');
		$('#pam').css('display','block');
	},500);
		
	window.setTimeout(function()
	{
		for(i=1;i<=parseInt($('#den1').html());i++)
		{
			ctx1.beginPath();
			ctx1.moveTo(i*partLength1,0);
			ctx1.lineTo(i*partLength1,40);
			ctx1.stroke();
			ctx1.closePath();
		}
		var a=0.005;
		var temp=window.setInterval(function()
		{
			ctx1.beginPath();
			ctx1.moveTo(0,0);
			ctx1.fillStyle="rgba(255,255,255,"+a+")";
			ctx1.fillRect(0,0,$('#num1').html()*partLength1,35);
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
		var parts=$('#den2').html();
		
		partLength2=240/parts;
		 
		for(i=1;i<=parseInt(fracArr[no].substring(2))*multiplier1;i++)
		{
			ctx2.beginPath();
			ctx2.moveTo(i*partLength2,0);
			ctx2.lineTo(i*partLength2,35);
			ctx2.stroke();
			ctx2.closePath();
		}
	},8500);
}
questionInteractive.prototype.startGameTimer=function(){
    if (completed != 1) 
	{
        totalTimeTaken++;
		levelWiseTimeTakenArr[currentLevel-1]++;
        interactiveObj.setOutputParameters();
        window.setTimeout(interactiveObj.startGameTimer, 1000);
    }
} 
function showPrompt(msg,callback)
{
       globalCallback = callback;
       $(".promptText").html('<b>'+msg+'</b>');
       $(".promptContainer").show();
	   $('#dialogueButton').focus();
} 
 
questionInteractive.prototype.init = function() 
{
	interactiveObj.startGameTimer();
	if(parameterMissing == true) return;
	var html='';
	html+='<div id="background" class=background><p id="inst1"><b>'+replaceDynamicText(instArr['inst1'],numberLanguage,'interactiveObj')+'</b></p><div class="fraction1"><div id="num1" class="frac1 numerator1" ></div><div id="den1" class="frac1" ></div></div>';
	html+='<div id="equals">=</div>';
	html+='<div class="fraction2" ><div id="num2" class="frac2 numerator2"><input type="text" id="txt" class="textbox1" pattern="[0-9]*"/></div><div id="den2" class="frac2"></div></div>';
	html+='<div class=hand><img id="hand" src="../assets/hand.png" /></div>';
	html+='<button id="next" onClick="interactiveObj.fireQuestionL1()"><b>'+promptArr['next']+'</b></button>';
	html+='</div>';
	html+='<table id="tbl" style="display:none;">';
	html+='<tr id="row1" style="height:40px;"><td id="td1"></td></tr>';
	html+='<tr id="row2" style="height:40px;"><td id="td2"></td></tr>';
	html+='<tr id="row3" style="height:40px;"><td id="td3"></td></tr>';
	html+='<tr id="row4" style="height:40px;"><td id="td4"></td></tr>';
	html+='<tr id="row5" style="height:40px;"><td id="td5"><input type="text" id="ans" /></td></tr>';
	html+='</table>';
	html+='<div id="canvas1"></div>';
	html+='<div id="john"><font face="Comic Sans MS" color="#01A9DB" size="4pt"><b>'+replaceDynamicText(promptArr['prompt18'])+'</b></font></div>';
	html+='<div id="canvas2"></div>';
	html+='<div id="pam"><font face="Comic Sans MS" color="#01A9DB" size="4pt"><b>'+replaceDynamicText(promptArr['prompt19'])+'</b></font></div>';
	html+='<div id="promptContainer" class="promptContainer" draggable="true">';
		html+='<div id="sparkieIcon"></div>';
		html+='<div class="promptText"></div>';
		html+='<div style="clear:both"></div>';
		html+='<button class="button" id="dialogueButton">'+replaceDynamicText(promptArr['prompt0'],numberLanguage,'interactiveObj')+'</button>';
    html+='</div>';
	html+='<div id="contentLine" class="contentLine"></div>';
	html+='<div id="contentLine1" class="contentLine1"></div>';
	html+='<div id="contentLine2" class="contentLine2"></div>';
	$("#container").html(html);
	$('.promptContainer').hide();
	$("#dialogueButton").click(function(e)
	{	
		$("#dialogueButton").blur();
		$(".promptContainer").hide();
            globalCallback();
    });
	
	$('#promptContainer').draggable({
		containment:"#container",
		cursorAt:{top: 10}
	});
	
	$('#next').css('display','none');
	for(i=0;i<=9;i++)
	{
		randomArr[i]=i;
	}
	randomArr.sort(function ()
	{
		return Math.random() - 0.5;
	});
	fracArr=new Array('2/3','3/4','2/5','3/5','5/6','1/2','1/3','1/4','1/5','1/6');

	if(getParameters['lastLevelCleared']==0)
	{
		extraParameters+='Level1';
		interactiveObj.fireQuestionL1();
		levelsAttempted='L1';
		levelWiseStatus='0';
		levelWiseScore='0';
		levelWiseTimeTaken='0';
	}
	else if(getParameters['lastLevelCleared']==1)
		interactiveObj.goToLevel2();
	else if(getParameters['lastLevelCleared']==2)
		interactiveObj.goToLevel3();
} 

questionInteractive.prototype.fireQuestionL1=function()
{
	currentLevel=1;
	$('#next').hide();
	$("#ans").attr("readOnly","true");
	$('#contentLine,#contentLine2,#contentLine1').hide();
	
	$("#txt").removeAttr("readOnly");
	$('.textbox1').removeClass('redBorder');
	$('.textbox1').removeClass('greenBorder');
	$('.textbox1').removeClass('blueBorder');
	incorrectAttemptsAfter=0;
	incorrectAttemptsBefore=0;
	level1Counter++;
	if(level1Counter>=3)
	{
		for(i=0;i<=(level1Counter-1);i++)
		{
			if((userResponseLevel1[i]==userResponseLevel1[i+1])&&(userResponseLevel1[i]=='true'))
			{
				completed=0;
				currentLevel=2;
						interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level1Score;
						levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
						interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
						this.lastlevelcleared=2;
						interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
						interactiveObj.setOutputParameters();
						level2Entered=1;
						interactiveObj.goToLevel2();
			}
		}
	}
		if(level1Counter==5)
		{
			completed=0;
				currentLevel=2;
					interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level1Score;
					levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
					interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
					this.lastlevelcleared=2;
					interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
					interactiveObj.setOutputParameters();
					level2Entered=1;
					interactiveObj.goToLevel2();
		}
	if(level2Flag==0)
	{
	$('#txt').removeAttr('disabled');
	$('.promptContainer').css('margin-left','0px');
	$('.promptContainer').css('margin-top','0px');
	
	$(".txt").removeClass('redBorder');
	$(".txt").removeClass('blueBorder');
	$(".txt").removeClass('greenBorder');
	
	$('#txt').val('');
	$('.hand').css('display','none');
	$('#john').css('display','none');
	$('#pam').css('display','none');
	$('#tbl').css('display','none');
	$('#canvas1').css('display','none');
	$('#canvas2').css('display','none');
	$('#next').css('display','none');
	$('#contentLine1').html('');
	$('#contentLine2').html('');
	$('#contentLine').html('');
	
	var no=fracArr[randomArr[level1Counter]].split('/');
	no1=no[0];
	no2=no[1];
	multiplier1 = Math.random() < 0.5 ? 2 : 3 ;
	$('#num1').html(replaceDynamicText(multiplier1*no1,numberLanguage,'interactiveObj'));
	$('#den1').html(replaceDynamicText(multiplier1*no2,numberLanguage,'interactiveObj'));
	$('#den2').html(replaceDynamicText(no2,numberLanguage,'interactiveObj'));
	
	extraParameters+='('+multiplier1*no1+'/'+multiplier1*no2+',';
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))

	if(!(navigator.userAgent.indexOf("iPad") != -1))	
		$('#txt').focus();
	}
}

questionInteractive.prototype.goToLevel2=function()
{
	level1Time=timer;
	
	fracArr=new Array("3/20","9/20","7/20","6/17","12/19","7/20","3/19","7/18","5/12","5/11","2/17","5/17","13/17","14/19","5/19","7/12","5/14","15/17","9/11","8/17","11/19","16/19","7/20","3/10");
	for(i=0;i<24;i++)
	{
		randomArr[i]=i;
	}
	randomArr.sort(function ()
	{
		return Math.random() - 0.5;
	});
	level2Flag=1;
	extraParameters+='|Level2';
	
	interactiveObj.fireQuestionL2();
}

questionInteractive.prototype.fireQuestionL2=function()
{	
	
	rArr=new Array();
		for(i=0;i<4;i++)
		{
			rArr[i]=i+1;
		}
		rArr.sort(function ()
		{
			return Math.random() - 0.5;
		});
		
	if(level2Counter>0)
	{
		if(incorrectAttemptsL2==0)
		{
			level2Score++;
			//userResponseLevel2.push('true');
		}
		else
		{
			userResponseLevel2.push('false');
		}
	}
	incorrectAttemptsL2=0;
	
	if(level2Counter==6&&nextLevelFlag==0)
	{
		level2Flag=1;
		completed=0;
		currentLevel=3;
				interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level2Score;
				levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
				interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=2;
				this.lastlevelcleared=3;
				interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
				interactiveObj.setOutputParameters();
		interactiveObj.goToLevel3();
	}
	else
	{
		if(level2Counter>=4)
		{
			for(i=0;i<=(level2Counter-1);i++)
			{
				if((userResponseLevel2[i]=='true')&&(userResponseLevel2[i+1]=='true')&&nextLevelFlag==0)
				{
					level2Flag=1;
					completed=0;
					currentLevel=3;
					interactiveObj.levelWiseScoreArr[this.lastlevelcleared-1]=level2Score;
					levelWiseTimeTakenArr[this.lastlevelcleared-1]=totalTimeTaken;
					interactiveObj.levelWiseStatusArr[this.lastlevelcleared-1]=1;
					this.lastlevelcleared=3;
					interactiveObj.levelsAttemptedArr.push(this.lastlevelcleared);
					interactiveObj.setOutputParameters();
					interactiveObj.goToLevel3();
				}
			}
		}
	}
	
	level2Counter++;
	if(Level3Flag==0)
	{
	var f=fracArr[randomArr[level2Counter]].split('/');
	f1=f[0];
	f2=f[1];
	
	var multiplierArr=new Array(4,6,8,9,10);
	var selector=Math.floor((Math.random()*5));
	
	switch(selector)
	{
		case 0:
		{
			multiplier2=multiplierArr[selector];
			n = Math.random() < 0.5 ? 2 : 2 ;
			m1=2;
			m2=2;
			r=1;
			t=2;
			break;
		}
		
		case 1:
		{
			multiplier2=multiplierArr[selector];
			n = Math.random() < 0.5 ? 2 : 3 ;
			m1=2;
			m2=3;
			r=2;
			t=3;
			break;
		}
		
		case 2:
		{
			multiplier2=multiplierArr[selector];
			n = Math.random() < 0.5 ? 2 : 2 ;
			m1=4;
			m2=2;
			r=1;
			t=2;
			break;
		}
		
		case 3:
		{
			multiplier2=multiplierArr[selector];
			n = Math.random() < 0.5 ? 3 : 3 ;
			m1=3;
			m2=3;
			r=1;
			t=3;
			break;
		}
		
		case 4:
		{
			multiplier2=multiplierArr[selector];
			n = Math.random() < 0.5 ? 2 : 5 ;
			m1=2;
			m2=5;
			r=2;
			t=5;
			break;
		}
	}
	
	extraParameters+='('+f1*multiplier2+'/'+f2*multiplier2+';';
	frac='<div class="fraction" style="width:55px;"><div class="frac numerator" style="width:45px;">'+replaceDynamicText(f1*multiplier2,numberLanguage,'interactiveObj')+'</div><div class="frac" style="width:45px;">'+replaceDynamicText(f2*multiplier2,numberLanguage,'interactiveObj')+'</div></div>';
	var html='';
	html+='<div id="background" class=background></div>';
	html+='<div id="promptContainer" class="promptContainer">';
		html+='<div id="sparkieIcon"></div>';
		html+='<div class="promptText"></div>';
		html+='<div style="clear:both"></div>';
		html+='<button class="button" id="dialogueButton">'+replaceDynamicText(promptArr['prompt0'],numberLanguage,'interactiveObj')+'</button>';
    html+='</div>';
	html+='<div id="questionText" style="position:relative;margin-top:40px;margin-left:55px;"><font size="4pt" face="Comic Sans MS"><b>'+replaceDynamicText(promptArr['prompt20'],numberLanguage,'interactiveObj')+'</b></font></div>';
	html+='<br/>';
	html+='<div id="div1'+level3Counter+'" style="float:left;height:50px;font-size:20pt;padding-right:35px;margin-left:55px;position:relative;"><input type="radio" name="optionValues" id="opt'+rArr[0]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[0]+level3Counter+'"></label></div>';
	html+='<div id="div2'+level3Counter+'" style="float:left;height:50px;font-size:20pt;padding-right:35px;margin-left:25px;position:relative;"><input type="radio" name="optionValues" id="opt'+rArr[1]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[1]+level3Counter+'"></label></div>';
	html+='<div id="div3'+level3Counter+'" style="float:left;height:50px;font-size:20pt;padding-right:35px;margin-left:25px;position:relative;"><input type="radio" name="optionValues" id="opt'+rArr[2]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[2]+level3Counter+'"></label></div>';
	html+='<div id="div4'+level3Counter+'" style="float:left;height:50px;font-size:20pt;padding-right:35px;margin-left:25px;position:relative;"><input type="radio" name="optionValues" id="opt'+rArr[3]+level3Counter+'" class="optionButtons'+level3Counter+'" style="margin-top:10px;" /><label for="opt'+rArr[3]+level3Counter+'"></label></div>';
	html+='<button id="nextL3" onClick="interactiveObj.fireQuestionL2()" ><b>'+replaceDynamicText(promptArr['next'],numberLanguage,'interactiveObj')+'</b></button>';
	html+='<br/><br/><br/><br/><br/>';
	html+='<div id="contentLine1" class="contentLines" ></div>';
	html+='<div id="contentLine2" class="contentLines" ></div>';
	html+='<div id="contentLine3" class="contentLines" ></div>';
	html+='<div id="contentLine4" class="contentLines" ></div>';
	html+='<div id="contentLine5" class="contentLines" ></div>';
	html+='<div id="contentLine6" class="contentLines" ></div>';
	html+='<div id="contentLine7" class="contentLines" ></div>';

		$('#container').html(html);
		$('.promptContainer').css('top','200px');
		$("#dialogueButton").click(function(e)
		{	
			$("#dialogueButton").blur();
			$(".promptContainer").hide();
            globalCallback();
		});
		$('#promptContainer').hide();
		$('#promptContainer').draggable({
			containment:"#container",
			cursorAt: {top: 10}
		});
		
		frac3='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(f1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(f2,numberLanguage,'interactiveObj')+'</div></div>';
		frac4='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(f1*n,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(f2*n,numberLanguage,'interactiveObj')+'</div></div>';
		frac5='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(1,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(f2,numberLanguage,'interactiveObj')+'</div></div>';
		frac6='<div class="fraction"><div class="frac numerator" style="width:48px;">'+replaceDynamicText(f1*r,numberLanguage,'interactiveObj')+'</div><div class="frac" >'+replaceDynamicText(f2*t,numberLanguage,'interactiveObj')+'</div></div>';
		
		$('label[for=opt1'+level3Counter+']').html('<font size="5px">&nbsp;&nbsp;'+frac3+'</font>');
		$('label[for=opt2'+level3Counter+']').html('<font size="5px">&nbsp;&nbsp;'+frac4+'</font>');
		$('label[for=opt3'+level3Counter+']').html('<font size="5px">&nbsp;&nbsp;'+frac5+'</font>');
		$('label[for=opt4'+level3Counter+']').html('<font size="5px">&nbsp;&nbsp;'+frac6+'</font>');
	}
}

questionInteractive.prototype.goToLevel3=function()
{
	nextLevelFlag=1;
	level2Time=timer;
	
	Level3Flag=1;
			
	extraParameters+='|Level3';
	var html='';
	html+='<div id="background" class=background></div>';
	html+='<div id="promptContainer" class="promptContainer">';
		html+='<div id="sparkieIcon"></div>';
		html+='<div class="promptText"></div>';
		html+='<div style="clear:both"></div>';
		html+='<button class="button" id="dialogueButton">'+replaceDynamicText(promptArr['prompt0'],numberLanguage,'interactiveObj')+'</button>';
    html+='</div>';
	html+='<div id="questionText" style="position:relative;margin-top:40px;margin-left:55px;"><font size="5pt" face="Comic Sans MS"><b>'+replaceDynamicText(promptArr['prompt31'],numberLanguage,'interactiveObj')+'</b></font></div>';
	html+='<div class="fraction1" style="position:relative;"><div id="num1" class="frac1 numerator1" style="width:35px;"></div><div id="den1" class="frac1" style="width:35px;" ></div></div>';
	html+='<div id="equals">=</div>';
	html+='<div id="f2" style="width:170px;">';
	html+='<div id="fraction2" style="position:relative;" ><div id="num2" class="frac2 numerator2" ><inputt ype="text" id="n'+level3Counter+'" class="txt"  pattern="[0-9]*" /></div><div id="den2" class="frac2" style="width:55px;margin-left:-2px;height:20px;margin-top:11px;"><input type="text" id="d'+level3Counter+'" class="txt"  pattern="[0-9]*" /></div></div>';
	html+='</div>';
	html+='<button id="nextL3" onClick="interactiveObj.fireQuestionL3()" ><b>'+replaceDynamicText(promptArr['next'],numberLanguage,'interactiveObj')+'</b></button>';
	html+='<div id="message"></div>';
	html+='<div id="nextDisplay"></div>';
	html+='<div id="instText"></div>';
	html+='<br/>';
	$('#container').html(html);
	
	$("#dialogueButton").click(function(e)
	{	
		$("#dialogueButton").blur();
		$(".promptContainer").hide();
            globalCallback();
    });
	$('.promptContainer').hide();
	
	$('#promptContainer').draggable({
		containment:"#container",
		cursorAt: {top: 10}
	});
	
	fracArr=new Array("2/3","1/4","2/5","1/5","1/2","1/6","1/3","3/4");
	rArr=new Array(0,1,2,3,4,5,6,7);
	rArr.sort(function ()
	{
		return Math.random() - 0.5;
	});
	interactiveObj.fireQuestionL3();	
}

questionInteractive.prototype.fireQuestionL3=function()
{
	level3Count++;
	factors=[];
	listFactors=[];
	
	catchingFactors=[];
	divisibleFactors=[];
	string='';
	$('#message').html('');
	
	level3EquivalentFraction=0;
	incorrectDivisorAttempt=0;
	divideByZeroAttempt=0;
	incorrectAttemptsL3=0;
	case2=0;case3=0;
	$('.txt').removeAttr('disabled','disabled');
	
	var html='';
	html+='<div id="fraction2" style="position:relative;" ><div id="num2" class="frac2 numerator2" ><input type="text" id="n0" class="txt"  pattern="[0-9]*" /></div><div id="den2" class="frac2" style="width:55px;margin-left:-2px;height:20px;margin-top:11px;"><input type="text" id="d0" class="txt"  pattern="[0-9]*" /></div></div>';
	$('#f2').html(html);
	$('#f2').css('margin-left','100px');
	
	$("#d"+(level3Counter-1)).val("");
	$("#n"+(level3Counter-1)).val("");
	
	
	$('#appendedFraction').remove();
	$('#eq2').remove();
	$('#eq3').remove();
	$('#eq4').remove();
	$('#eq5').remove(); 
	
	$('#next2').remove();
	$('#next1').remove();
	$('#next3').remove();
	$('#instText').html('');
	$('.txt').removeAttr('disbaled','disabled');	
	
	if(level3Flag==1)
		level3BCounter++;
	if(level3Count>=2&&level3Flag==0)
	{
			if((userResponseLevel3[level3Count-2]=='true')&&(userResponseLevel3[level3Count-3]=='true'))
			{
				criteria='pass';
				level3Alert=1;
				fracArr=new Array("3/5","3/7","2/7","4/5","4/7","5/6","5/8");
				rArr=new Array(0,1,2,3,4,5,6);
				rArr.sort(function ()
				{
					return Math.random() - 0.5;
				});
				level3Flag=1;
			}
	}
	if(level3Count>4&&level3Flag==0)
	{
				fracArr=new Array("3/5","3/7","2/7","4/5","4/7","5/6","5/8");
				rArr=new Array(0,1,2,3,4,5,6);
				rArr.sort(function ()
				{
					return Math.random() - 0.5;
				});
				level3Flag=1;
				criteria='fail';
	}
	
	$('.promptContainer').css('margin-top','100px');
	
	$('#nextL3').css('display','none');
	
	$(".txt").removeClass('redBorder');
	$(".txt").removeClass('greenBorder');
	$(".txt").removeClass('blueBorder');
	
	$(".txt").val('');
	if(level3Flag==0)
		var a=Math.floor(Math.random() * (4 - 0 + 1)) + 0;
	else
		var a=Math.floor(Math.random() * (2 - 0 + 1)) + 0;
		
	if(level3Flag==0)
		var multiplierArr=new Array(2,3,4,5,10);
	else 	
		var multiplierArr=new Array(6,8,12);
	
	var no=fracArr[rArr[level3Count-1]].split('/');
	var no1=no[0];
	var no2=no[1];
		
	multiplier3=multiplierArr[a];
	
	extraParameters+='('+no1*multiplier3+'/'+no2*multiplier3+';';
	
	$('#num1').html(replaceDynamicText(no1*multiplier3,numberLanguage,'interactiveObj'));
	$('#den1').html(replaceDynamicText(no2*multiplier3,numberLanguage,'interactiveObj'));
	level3Counter++;
	if(level3Counter>1)
		level3Counter=1;
	
	$('.txt').val("");
	$("#n0").val("");
	$("#d0").val("");
	$('#numText').val('');
	$('#denText').val('');
	
	if(!((navigator.userAgent.indexOf("iPhone") != -1) ||
				(navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1)))
		$("#n"+(level3Counter-1)).focus();
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
		levelWiseTimeTaken  += levelWiseTimeTakenArr[interactiveObj.levelsAttemptedArr[i]-1]+"|";		
	}
	levelsAttempted = levelsAttempted.substr(0,(levelsAttempted.length-1));
	levelWiseStatus = levelWiseStatus.substr(0,(levelWiseStatus.length-1));
	levelWiseScore  = levelWiseScore.substr(0,(levelWiseScore.length-1));
	levelWiseTimeTaken  = levelWiseTimeTaken.substr(0,(levelWiseTimeTaken.length-1));
};


(function(){
            // Store a reference to the original remove method.
            var originalVal = jQuery.fn.val;

            // Define overriding method.
            jQuery.fn.val = function(){
            	/*if(arguments.length == 0){
            		var value = originalVal.apply( this, arguments );
                	return value;
            	}*/
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
            }
*/
            var originalHtml = jQuery.fn.html;
            // Define overriding method.
            /*jQuery.fn.html = function(){
            	if(arguments.length > 0){
            		var value = originalHtml.apply( this, arguments );
            		return value;
            	}

                if(numberLanguage == 'english')
                	originalHtml.apply( this, arguments );
                else if(numberLanguage == 'gujarati'){
                	var value = originalHtml.apply( this, arguments );

                	for(var i = 0 ; i< gujratiNumerals.length; i++){
                		var exp = new RegExp(gujratiNumerals[i], 'g');
                		value= value.replace(exp, i);
                	}
                	return value;
                }
                else if(numberLanguage == 'hindi'){
	            	var value = originalHtml.apply( this, arguments );

                	for(var i = 0 ; i< hindiNumerals.length; i++){
                		var exp = new RegExp(hindiNumerals[i], 'g');
                		value= value.replace(exp, i);
                	}

                	return value;
                		
                }
                else{
                	originalHtml.apply( this, arguments );
                }
            }*/
        })();