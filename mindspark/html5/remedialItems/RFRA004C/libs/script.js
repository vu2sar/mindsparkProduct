var totalTimeTaken = 0;
var completed = 0;
var extraParameters = "";
var noOfLevels;
var levelWiseMaxScores = "0";
var lastLevelCleared;
var previousLevelLock;
var levelsAttempted= ""; //"L1|L2|L3|L4";
var levelWiseStatus = "0";//0|1|2 ? 0: In progress, 1: Pass, 2: Fail
var levelWiseScore = "0";
var levelWiseTimeTaken= "";//250|120 (in seconds)
var levelWiseTimeTakenArr = new Array(0);
var levelAttemptedArr = new Array();
var levelWiseStatusArr = new Array(0);
var levelWiseScoreArr = new Array(0);
var extraParametersArr = new Array("");
var currentLevel = 1;
var currentScreen = 0;
var numberLanguage;

var phrase=0;
var click_shaded=0;
var counter1=0;
var counter2=0;
var counter3=0;
var range1=[2,3,4];
var range2=[5,7];
var attempted=0;
var show_phrase=0;
var ans_flag=0;
var given_answer=0;
var input1,input2,input3,input4;
var input_1,input_2;
var deno1,deno2;
var arr_to_shuffle=[1,2,3,4];
var level1_attempt=0;
var right_ans=new Array();
var opt1;
var opt2;
var opt3;
var opt4;
var num1=1;
var num2=1;
var question1;
var question2;
var textbx=0;
var ans1,ans2;
var hint_1=0;
var deno=0;
var again=0;
var selected;
var a1_val=[];
var b1_val=[];
var c1_val=[];
var d1_val=[];
var ctx1,ctx2,ctx3;
var rad_di;
var answer;
var wrng_c=0;
var subtr_ques1=[1,2];
var subtr_ques2=[2,3,4];
var range3=[1,2,3];
var range4=[7,5];
var num1=0;
var num2=0;
var deno1=0;
var deno2=0;
var id1;
var drp_attmpt=0;
var q_type=2;
var sign;
var total1=0;
var total2=0;
var num_p1=0;
var num_p2=0;
var deno_p1=0;
var deno_p2=0;
var exp1;
var exp2;
var prom_pos=0;
var rect1flag=0;
var rect2flag=0;
var rect3flag=0;
var saw_it=0;
var type=0;
var fr1,fr2,fr3;
var question_attmpt=0;
var val_drp=0;
var select_question=[];
var gc=0;
var opt5;
var ques_askd=[];
$(document).ready(function(e){
	
	var imageArray = new Array('hand.png','bg.png');
	var loader = new PxLoader(); 
	$.each(imageArray,function(key,value){
		var pxImage = new PxLoaderImage('../assets/'+value); 
		loader.add(pxImage);
	});
	loader.addCompletionListener(function () {
	    var gameXMLFile = "xml.xml";
        //Loading XML file for data..
        loadXML(gameXMLFile, function () {
            var getParameters = getURLParameters();
            
            if (typeof getParameters['noOfLevels'] == "undefined") { showPrompt("Pls pass the noOfLevel"); } else noOfLevels = getParameters['noOfLevels'];
            if (typeof getParameters['lastLevelCleared'] == "undefined") { $("#OK").hide(); showPrompt("Pls pass the lastLevelCleared"); } else lastLevelCleared = getParameters['lastLevelCleared'];
            if (typeof getParameters['previousLevelLock'] == "undefined") { $("#OK").hide(); showPrompt("Pls pass the previousLevelLock"); } else previousLevelLock = getParameters['previousLevelLock'];
		   /* if (typeof getParameters['q_type'] == "undefined") { $("#OK").hide(); showPrompt("Pls pass the q_type"); } else q_type = getParameters['q_type'];
		*/	if(typeof getParameters['language']=="undefined") this.language="english"; else this.language=getParameters['language'];
			
            if (noOfLevels != 1) {
                $("#OK").hide();
                $("#screenModal").show();
                showPrompt("Levels mismatch");

            } else {

                for (var i = 0; i < noOfLevels; i++) {
                    var lock = new Image();
                    lock.src = "../assets/lock2.png";
                    lock.id = "lock" + i;
                    $("#levelButtons").append("<div id='level" + (i + 1) + "' class='button2 levelButts'>Level" + (i + 1) + "</div>")
                    $("#level" + (i + 1)).css({ "margin-left": i * 100 + "px" });
                    $("#level" + (i + 1)).append(lock);
                    $("#lock" + i).css({ "position": "absolute", "left": 260 + (i * 100) + "px", "top": 200 + (i * 58) + "px", "opacity": "0.6" });
                }
                $(".levelButts").css({
                    "width": "80px",
                    "margin-top": "20px",
					"margin-left": "-=30px",
                    "opacity": "0.5",
                    "cursor": "default"
                });
				
                for (var i = 0; i < (parseInt(lastLevelCleared) + 1); i++) {
                    $("#level" + (i + 1)).css({ "opacity": "1", "cursor": "pointer" });
                    $("#lock" + i).hide();

                }
                $('#PreLoader').css({ 'display': 'none' });
                previousLevelLock = parseInt(previousLevelLock);
                if (previousLevelLock==0) {
                    $('#landingPage').fadeIn(200);
                }
                else if ((lastLevelCleared == 0)&&(previousLevelLock!=0)) {
                    $('#landingPage').hide();
			        $('#container,#mainDiv').fadeIn(200);
					level=1;
					if(q_type==3){
			          showScreen(2);	
					}
					else{
			          showScreen(1);
					}		
					startGameTimer();
                }
              
            }
        });
    }); 
	loader.start();
  	  $("#level1").live('click', function () {
        $('#landingPage').hide();
        $('#container,#mainDiv').fadeIn(200);
			level=1;
        showScreen(1);
        startGameTimer();
    });
  		$("#next").click(function(e){
			question_attmpt=0;
			attempted=0;
			click_shaded=0;
			ans_flag=0;
			show_phrase=0;
			hint_1=0;
			prom_pos=0;
			phrase=0;
			 counter1=0;
			 counter2=0;
			 counter3=0;
			 rect1flag=0;
			 rect2flag=0;
			 rect3flag=0;
			 drp_attmpt=0;
			 again=0;
			 val_drp=0;
			 given_answer=0;
			 saw_it=0;
			 wrng_c=0;
			 type=0;
				if(level1_attempt<3){
						$('#showScreen'+currentScreen).html("");
						showScreen(currentScreen);	
				}
				else if(level1_attempt==3){
					levelWiseStatusArr[0]=1;
					$('#next,#div6').hide();
					setTimeout(function(){
						completed=1;	
					},1000);
				}
			});	
			$('input[type=radio]').live('change',function(){
				
				rad_di=$(this).attr('id');
				selected=$(this).attr('id');
				$('#sub').show();
		 });
		
		$('div').live("click",function(){
			if($(this).attr('id')=="equi_prom_blank"){	
				return false;
			}
			else{	
				$('#equi_prom').hide();	
			}
				
		});
		
		$('#equi_prom_blank').live("click",function(){
			
				flag_prom=1;
				var html='';
				html+='<div id="line1">'+replaceDynamicText(promptArr['p215'],numberLanguage,"")+'</div>';
				$('#equi_prom').html(html);
				$('#equi_prom').show();
				setTimeout(function(){
					$('#equi_prom').hide();
				},6000);
	
		});
 		$('.txt').live('keydown',function(e) {
			 $(".txt").keypress(function(e) {
				     var a = [];
			        var k = e.which;
					if($(this).attr('id')=="txt3" ||$(this).attr('id')=="txt4" ){
							
				        for (x = 48; x < 58; x++)
				            a.push(x);
					}
					else{
						
				        for (x = 47; x < 58; x++)
				            a.push(x);	
					}
					 if (!($.inArray(k,a)>=0) && e.keyCode != 9 && e.keyCode != 8)
			            e.preventDefault();
					
			});
			 if(e.keyCode == 13)
		      		 {
					 		if($(this).val()!="")
							{	
								attempted++;
								check_answer($(this).attr('id'));
							}
					 }
		});
		$('#promp').draggable({
			containment:'body'
		});
});
function submit_radio(){
	$('#sub').hide();
	var lsel;
				question_attmpt++;
				if(selected=="a1"){
					lsel=a1_val[1];
					selected=($('#l11').html());
				}if(selected=="b1"){
					lsel=b1_val[1];
					selected=($('#l21').html());
				}if(selected=="c1"){
					lsel=c1_val[1];
					selected=($('#l31').html());
				}if(selected=="d1"){
					lsel=d1_val[1];
					selected=($('#l41').html());
				}
					$('#t3').html(replaceDynamicText(promptArr['p112'],numberLanguage,""));
					$('#t4').html(replaceDynamicText(promptArr['p113'],numberLanguage,""));
				
				if(currentLevel==1)
				{	
					if(question_attmpt<3){
					
				
						if($('#'+rad_di).hasClass('correct')==true)
						{
							$('.rad1').attr('disabled',true);
							given_answer++;
							ans_flag=1;
							$('#'+rad_di).parent().addClass('green');
							showPrompt(instArr['correct']);
						}
						else
						{
							attempted++;
							if(click_shaded==2 && $('#'+rad_di).hasClass('wrongc')==true){
								//type=1;
									showPrompt(replaceDynamicText(instArr['i107'],numberLanguage,""));	
							
							}
							else if(click_shaded==2 && $('#'+rad_di).hasClass('wrongc')!=true){
								type=1;
									showPrompt(replaceDynamicText(instArr['i101'],numberLanguage,""));	
							
							}
							else{
							
								if(show_phrase==0)
								{
									if($('#'+rad_di).hasClass('wrongc')==true){
										attempt_c();
									}
									else{
										attempt();	
									}	
								}
								else
								{
									attempt_next();	
								}
								$('#'+rad_di).parent().addClass('red');
									
							}
						}
					}
					else{
						if($('#'+rad_di).hasClass('correct')==true)
						{
							$('.rad1').attr('disabled',true);
							given_answer++;
							ans_flag=1;
							$('#'+rad_di).parent().addClass('green');
							showPrompt(instArr['correct']);
						}
						else{
								showPrompt(replaceDynamicText(instArr['i107'],numberLanguage,""));	
						}
						
					}
				}
				
}
function OK(){
	$('#screenModal').hide();
	if(question_attmpt<3){
			
			if(click_shaded==0){	
					if(hint_1!=1){
						
							if(currentScreen==1){
								if(again==0){
									if(show_phrase==0){
										if(ans_flag==1){
											attempted=0;ans_flag=0;
											$('#next').show();
											if(level1_attempt>=3){
											 levelWiseStatusArr[0]=1;
												$('#next,#div6').hide();	
											
												setTimeout(function(){
													completed=1;	
												},1000);
											}
										}
										else if(ans_flag!=1 && attempted==1){
											show_phrase++;
											attempted=0;ans_flag=0;
											if(wrng_c==1){
												click_shade();
												wrng_c=0;
											}
											else{
												type=1;
												$('#equi_prom_blank').show();
												$('#div2').show();
											}
											
											blur("div1");
											$('.rad1').attr('disabled',true);
										}	
									}
									else if(show_phrase==1){
										if(again==0){	
												if(ans_flag==1 && textbx=="txt1"){
													ans_flag=0;
													attempted=0;
													$('#txt2').attr('disabled',false);
												}				
												else if(ans_flag==1 && textbx=="txt2"){
													ans_flag=0;
													attempted=0;
													$('#hint').attr("disabled",true);
														setTimeout(function(){
															again=1;
															attempted=0;
															showPrompt(instArr['i106']);
														},2000);
												}					
												else if(ans_flag==1 && textbx=="txt3"){
													ans_flag=0;
													attempted=0;
													$('#textboxa1').html(replaceDynamicText(promptArr['p108'],numberLanguage,""));	
													$('#ans1').addClass('green').removeClass('red');
													$('#txt2').attr('disabled',false);
											}						
												else if(ans_flag==1 && textbx=="txt4"){
													ans_flag=0;
													attempted=0;
													$('#textboxb1').html(replaceDynamicText(promptArr['p109'],numberLanguage,""));	
													$('#ans2').addClass('green').removeClass('red');
													$('#hint').attr("disabled",true);
														setTimeout(function(){
															again=1;
															attempted=0;
															showPrompt(instArr['i106']);
														},2000);
													
											}								
												else if(ans_flag==0){
													
												if(textbx=="txt1" || textbx=="txt2"){
													if(attempted==2){
														attempted=0;
														if(textbx=="txt1"){
															$('#textboxa1').html(replaceDynamicText(promptArr['p106'],numberLanguage,""));	
														}
														else{
															$('#textboxb1').html(replaceDynamicText(promptArr['p107'],numberLanguage,""));	
														}
													 }
													else if(attempted==1 || attempted==0){
														$('#'+textbx).attr('disabled',false);
														$('#'+textbx).removeClass('red');
														$('#'+textbx).val('');
													}	
												}
												else if(textbx=="txt3" || textbx=="txt4"){
												
													if(textbx=="txt3"){	
													attempted=0;
														$('#textboxa1').html(replaceDynamicText(promptArr['p108'],numberLanguage,""));
														$('#ans1').addClass('blue').removeClass('red');	
														$('#txt2').attr('disabled',false);
													}
													else if(textbx=="txt4"){	
														$('#textboxb1').html(replaceDynamicText(promptArr['p109'],numberLanguage,""));	
														$('#ans2').addClass('blue').removeClass('red');
														$('#hint').attr("disabled",true);
														setTimeout(function(){
															again=1;
															attempted=0;
															showPrompt(instArr['i106']);
														},2000);
													}
												}
											}																																
						
										}						
									}
								}
								else if(again==1){
									if(ans_flag!=1){
										if(attempted==0){
											blur("div2");
											unblur("div1");
											$('.rad1').attr('disabled',false).attr('checked',false);
											$('.lable').removeClass('red');				
										}
										else{
											if($('#'+rad_di).hasClass('wrongc')==true && saw_it==0){
												click_shade();
											}
											else{			
												text_explanation();	
											}
											
										}
									}
									else{
										$('#next').show();
										if(level1_attempt>=3){
											levelWiseStatusArr[0]=1;
											
												setTimeout(function(){
												$('#next,#div6').hide();
											
													completed=1;	
												},1000);
											}
									}
									
								}
							}
							else if(currentScreen==2){
								if(again==0){
									if(show_phrase==0){
										if(ans_flag==1){
											attempted=0;ans_flag=0;
											$('#next').show();
											if(level1_attempt>=3){
											levelWiseStatusArr[0]=1;
													$('#next,#div6').hide();	
											
												setTimeout(function(){
													completed=1;	
												},1000);
											}
										}
										else if(ans_flag!=1 && attempted==1){
											show_phrase++;
											attempted=0;ans_flag=0;
											if(wrng_c==1){
												$('#div4').show();
												wrng_c=0;
											}
											else{
												type=1;
												$('#equi_prom_blank').show();
												$('#div2').show();
											}
											
											blur("div1");
											$('.rad1').attr('disabled',true);
										}	
									}
									else if(show_phrase==1){
										if(again==0){	
												if(ans_flag==1 && textbx=="txt1"){
												ans_flag=0;
												attempted=0;
												$('#txt2').attr('disabled',false);
											}				
												else if(ans_flag==1 && textbx=="txt2"){
												ans_flag=0;
												attempted=0;
												$('#hint').attr("disabled",true);
														setTimeout(function(){
															again=1;
															attempted=0;
															showPrompt(instArr['i106']);
														},2000);
											}					
												else if(ans_flag==1 && textbx=="txt3"){
												ans_flag=0;
												attempted=0;
													$('#textboxa1').html(replaceDynamicText(promptArr['p108'],numberLanguage,""));	
													$('#ans1').addClass('green').removeClass('red');
													$('#txt2').attr('disabled',false);
											}						
												else if(ans_flag==1 && textbx=="txt4"){
												ans_flag=0;
												attempted=0;
													$('#textboxb1').html(replaceDynamicText(promptArr['p109'],numberLanguage,""));	
													$('#ans2').addClass('green').removeClass('red');
													$('#hint').attr("disabled",true);
														setTimeout(function(){
															again=1;
															attempted=0;
															showPrompt(instArr['i106']);
														},2000);
													
											}								
												else if(ans_flag==0){
												if(textbx=="txt1" || textbx=="txt2"){
													if(attempted==2){
														attempted=0;
														if(textbx=="txt1"){
															$('#textboxa1').html(replaceDynamicText(promptArr['p106'],numberLanguage,""));	
														}
														else{
															$('#textboxb1').html(replaceDynamicText(promptArr['p107'],numberLanguage,""));	
														}
													 }
													else if(attempted==1 || attempted==0){
														$('#'+textbx).attr('disabled',false);
														$('#'+textbx).removeClass('red');
														$('#'+textbx).val('');
													}	
												}
												else if(textbx=="txt3" || textbx=="txt4"){
													if(textbx=="txt3"){	
													attempted=0;
													ans_flag=0;
														$('#textboxa1').html(replaceDynamicText(promptArr['p108'],numberLanguage,""));
														$('#ans1').addClass('blue').removeClass('red');	
														$('#txt2').attr('disabled',false);
													}
													else if(textbx=="txt4"){	
														$('#textboxb1').html(replaceDynamicText(promptArr['p109'],numberLanguage,""));	
														$('#ans2').addClass('blue').removeClass('red');
														$('#hint').attr("disabled",true);
														setTimeout(function(){
															again=1;
															attempted=0;
															showPrompt(instArr['i106']);
														},2000);
													}
												}
											}												
										}						
									}
								}
								else if(again==1){
									if(ans_flag!=1){
										if(attempted==0){
											blur("div2");
											unblur("div1");
											$('.rad1').attr('disabled',false).attr('checked',false);
											$('.lable').removeClass('red');				
										}
										else{
											if($('#'+rad_di).hasClass('wrongc')==true && saw_it==0){
												click_shade();
											}
											else{			
												text_explanation();	
											}
											
										}	
									}
									else{
									
										$('#next').show();
										if(level1_attempt>=3){
											levelWiseStatusArr[0]=1;
												$('#next,#div6').hide();
											
												setTimeout(function(){
													completed=1;	
												},1000);
											}
									}
								}
							}
					}
					else if(hint_1==1){
						hint_1=0;
					}
			}
			else if(click_shaded==1){
				if(phrase==0){
					if(counter1!=0){
						$('#ques2').show();
						rect1flag=1;
						$('#submit1').hide();
						if(counter1!=num1){
							$('.rect1').css({'background-color':'white'});
							for(var i=0;i<num1;i++){
								if(deno1>deno2){
									$('#dv1'+(i+1)).css({'background-color':'pink'});
								}
								else{
									$('#dv1'+(i+1)).css({'background-color':'blue'});
								}
								
							}
						}
						phrase=1;	
					}
				}
				else if(phrase==1){
					if(counter2!=0){
						rect2flag=1;
						$('#submit2').hide();
						$('#ques3').show();
						if(counter2!=num2){
							$('.rect2').css({'background-color':'white'});
							for(var i=0;i<num2;i++){
								if(deno2>deno1){
									$('#dv2'+(i+1)).css({'background-color':'pink'});
								}
								else{
									$('#dv2'+(i+1)).css({'background-color':'blue'});
								}
							}
						}
						phrase=2;
					}
				}
				else if(phrase==2){
					if(counter3!=0){
						rect3flag=1;
						$('#submit3').hide();
						$('#ques4').show();
						if(counter3!=(num1+num2)){
							$('.rect3').css({'background-color':'white'});
							for(var i=0;i<(num1+num2);i++){
								$('#dv3'+(i+1)).css({'background-color':'green'});
							}
						}
						phrase=3;
					}
					
				}
				else if(phrase==3){
					
						blur("div4");
						unblur("div1");
						$('.rad1').attr('disabled',false).attr('checked',false);
						$('.lable').removeClass('red');	
						click_shaded=2;
						setTimeout(function(){
							$('#div4').hide();
						},3000);			
					
					
				}
			}
			else if(click_shaded==2){
						attempted=0;
						
						show_phrase=0;
					if(ans_flag==1){
						ans_flag=0;
						$('#next').show();
							if(level1_attempt>=3){
								 levelWiseStatusArr[0]=1;
								 $('#next,#div6').hide();
						
									setTimeout(function(){
										completed=1;	
									},1000);
								}
					}
					else{
						ans_flag=0;
						if(type==1){
							show_phrase=1;
							click_shaded=0;
							again=0;
							blur("div1");
							$('.rad1').attr('disabled',true);
							$('#equi_prom_blank').show();
							$('#div2').show();				
						}
						else{
							text_explanation();	
						}
					}
			}
	
	}
	else{
		if(ans_flag==1){
			attempted=0;ans_flag=0;
			$('#next').show();
			if(level1_attempt>=3){
			 levelWiseStatusArr[0]=1;
			 $('#next,#div6').hide();
	
				setTimeout(function(){
					completed=1;	
				},1000);
			}
		}
		else{
			text_explanation();
		}
		
	}
}
function generate_question(){
	if(q_type==1){
		//addition wid different deno
			num1=1;
			num2=1;
		 deno1=generate_ques(range1);
		 deno2=generate_ques(range2);
		// range1=[2,3,4];
		 range2=[5,7];
	}
	else if(q_type==2){
		//addition wid deno multiples
//		 num1=generate_ques(range3);
//		 num2=generate_ques(range3);

		 deno1=generate_ques(range4);
		 deno2=deno1*(2);
		 range3=[1,2,3];
		 range4=[7,5];
		 if(level1_attempt==0){
		 	 num1=1;
			 num2=2;
		 }
		 else if(level1_attempt==1){
		 	 num1=3;
			 num2=1;
		 }
		  else if(level1_attempt==2){
		 	 num1=3;
			 num2=2;
		 }
		
		
	}
	else if(q_type==3){
		
			 num1=generate_ques(subtr_ques1);
				 if(num1==1){
				 	deno1=generate_ques(subtr_ques2);
				 }
				 else if(num1==2){
				 	deno1=3;
				 }
				 if(deno1==4){
				 	deno2=8;
					num2=1;
				 }
				 else{
				 	deno2=4;
					num2=1;
				 }
				 if(level1_attempt==0){
				 	num1=1;
					deno1=4;
					num2=1;
					deno2=8;
				 }
				 else if(level1_attempt==1){
				 	num1=1;
					deno1=generateRandomNo(2,4);
					num2=1;
					deno2=4;
				 }
				 else{
				 	num1=2;
					deno1=3;
					num2=1;
					deno2=4;
				 }
				 /*if(num1==2){
				 	 subtr_ques1=[1];
				 }
				 else{
				 	 subtr_ques1=[1,2];
				 }
				
				 subtr_ques2=[2,3,4];*/
		//substraction wid different deno
	}
}
function startGameTimer(){
	
    if (completed != 1) {
        totalTimeTaken++;
        levelWiseTimeTakenArr[currentLevel - 1]=totalTimeTaken;
		setOutputParamter();
        window.setTimeout(startGameTimer, 1000);
    }
	
}
function showScreen(screenNo){
		
	$(".screenDiv").hide();
	$("#screenDiv"+screenNo).show();
	showAnimation(screenNo);
}
function showPrompt(msg){

	$("#txt p").html(msg);
	$('#promp').show();
	$("#screenModal").show();
	$('#OK').css({'left':(parseInt($('#promp').css('width'))/2)-15+'px'});
	$('#OK').html(miscArr['ok']);
	if(show_phrase==0){
		$('#promp').css({'left': '200px','top': '280px'});
	}
	else{
		$('#promp').css({'left': '200px','top': '417px'});
	}
	if(prom_pos==1){
		prom_pos=0;
		$('#promp').css({'left': '110px','top': '195px'});
	}
}
function showAnimation(screenNo){

	$('#next').hide();
	$('#next').html(miscArr['next']);
	generate_question();
	fr1='<div class="fraction" style="padding:3px;"><div class="frac numerator">'+1+'</div><div class="frac">'+2+'</div></div></b></font>';
	fr2='<div class="fraction"  style="padding:3px;"><div class="frac numerator">1 <i>x</i> 10</div><div class="frac">2 <i>x</i> 10</div></div></b></font>';
	fr3='<div class="fraction" style="padding:3px;"><div class="frac numerator">10</div><div class="frac">20</div></div></b></font>';

	switch(screenNo){
		case 1:
				if(level1_attempt==0){			
					levelWiseStatusArr[0]=0;
					levelAttemptedArr.push(1);
					score=0;	
				}
			currentLevel=1;
			currentScreen = 1;
			level1_attempt++;
			attempted=0;
			again=0;
			ans_flag=0;
			input_box();
			var html="";
			for(var i=1;i<2;i++)
			{
				html+='<div id="div'+i+'">';
					html+='<div id="question'+i+'" class="question"></div>';
					html+='<div id="option'+i+'">';
						html+='<div class="lable" style="padding-left: 0px; padding-right: 10px; ">';
							html+='<input type="radio" id="a'+i+'" name="ques'+i+'" value="opt1" class="rad'+i+'"/><label for="a'+i+'" id="l1'+i+'"></label>';
						html+='</div>';
						html+='<div  class="lable">';
							html+='<input type="radio" id="b'+i+'" name="ques'+i+'" value="opt2" class="rad'+i+'"/><label for="b'+i+'" id="l2'+i+'"></label>';	
						html+='</div>';
						html+='<div class="lable">';
							html+='<input type="radio" id="c'+i+'" name="ques'+i+'" value="opt3" class="rad'+i+'"/><label for="c'+i+'" id="l3'+i+'"></label>';
						html+='</div>';
						html+='<div  class="lable">';
							html+='<input type="radio" id="d'+i+'" name="ques'+i+'" value="opt1" class="rad'+i+'"/><label for="d'+i+'" id="l4'+i+'"></label>';	
						html+='</div>';
					html+='</div>';
					html+='<div id="sub" class="button">'+miscArr['submit']+'</div>';
				html+='</div>';		
			}
				html+='<div id="equi_prom" class="triangle-border left"></div>';
				html+='<div id="equi_prom_blank"></div>';
				html+='<div id="div2">';
				html+='<div id="question2" class="question"></div>';
				for(var i=1;i<3;i++){
					
					html+='<div id="part'+i+'">';
						html+='<div id="textboxa'+i+'"></div>';
						html+='<div id="textboxb'+i+'" style="margin-top: 35px;"></div>';
					html+='</div>';	
					
				}
				
				html+='<button id="hint" onclick="hint();">'+miscArr['hint']+'</button>';
				html+='</div>';
				html+='<div id="div3">';
					html+='<div id="rectan"></div>';
					html+='<div id="explanation">';
						html+='<div id="e1"></div>';
						html+='<div id="e2"></div>';
						html+='<div id="e3"></div>';
					html+='</div>';
					
					html+='<div id="replay" class="button">'+miscArr['replay']+'</div>';
				
				html+='</div>';
				html+='<div id="div4">';
				html+='</div>';
				html+='<div id="div5">';
				html+='</div>';
				html+='<div id="div6">'+miscArr['enter']+'</div>';
				$('#screenDiv1').html(html);
				
				 question1='<div class="fraction"><div class="frac numerator">'+num1+'</div><div class="frac">'+deno1+'</div></div></b></font></div>';
				 question2='<div class="fraction"><div class="frac numerator">'+num2+'</div><div class="frac">'+deno2+'</div></div></b></font></div>';
				
				$('#question1').html(replaceDynamicText(promptArr['p101'],numberLanguage,""));
				$('#question2').html(replaceDynamicText(promptArr['p102'],numberLanguage,""));
				$('#textboxa1').html(replaceDynamicText(promptArr['p104'],numberLanguage,""));
				$('#textboxb1').html(replaceDynamicText(promptArr['p105'],numberLanguage,""));
				$('#t1').html(replaceDynamicText(promptArr['p110'],numberLanguage,""));
				$('#t2').html(replaceDynamicText(promptArr['p111'],numberLanguage,""));
					show_phrase=0;
					option(1);	
					$('#txt2').attr('disabled',true);
					 $('#sub').click(function(e){
					 	submit_radio();
					 });
					
			break;
			case 2:	
				if(level1_attempt==0){			
					levelWiseStatusArr[0]=0;
					levelAttemptedArr.push(1);
					score=0;	
				}
			currentLevel=1;
			currentScreen = 2;
			level1_attempt++;
			attempted=0;
			again=0;
			ans_flag=0;
			input_box();
			var html="";
			for(var i=1;i<2;i++)
			{
				html+='<div id="div'+i+'">';
					html+='<div id="question'+i+'" class="question"></div>';
					html+='<div id="option'+i+'">';
						html+='<div class="lable" style="padding-left: 0px; padding-right: 10px; ">';
							html+='<input type="radio" id="a'+i+'" name="ques'+i+'" value="opt1" class="rad'+i+'"/><label for="a'+i+'" id="l1'+i+'"></label>';
						html+='</div>';
						html+='<div  class="lable">';
							html+='<input type="radio" id="b'+i+'" name="ques'+i+'" value="opt2" class="rad'+i+'"/><label for="b'+i+'" id="l2'+i+'"></label>';	
						html+='</div>';
						html+='<div class="lable">';
							html+='<input type="radio" id="c'+i+'" name="ques'+i+'" value="opt3" class="rad'+i+'"/><label for="c'+i+'" id="l3'+i+'"></label>';
						html+='</div>';
						html+='<div  class="lable">';
							html+='<input type="radio" id="d'+i+'" name="ques'+i+'" value="opt1" class="rad'+i+'"/><label for="d'+i+'" id="l4'+i+'"></label>';	
						html+='</div>';
					html+='</div>';
					html+='<div id="sub" class="button">'+miscArr['submit']+'</div>';
				html+='</div>';		
			}
				html+='<div id="equi_prom" class="triangle-border left"></div>';
				html+='<div id="equi_prom_blank" onclick="equi();"></div>';
				
				html+='<div id="div2">';
				html+='<div id="question2" class="question"></div>';
				for(var i=1;i<3;i++){
					
					html+='<div id="part'+i+'">';
						html+='<div id="textboxa'+i+'"></div>';
						html+='<div id="textboxb'+i+'" style="margin-top: 35px;"></div>';
					html+='</div>';	
					
				}
				
				html+='<button id="hint" onclick="hint();">'+miscArr['hint']+'</button>';
				html+='</div>';
				html+='<div id="div3">';
					html+='<div id="rectan"></div>';
				
					html+='<div id="explanation">';
						html+='<div id="e1"></div>';
						html+='<div id="e2"></div>';
						html+='<div id="e3"></div>';
					html+='</div>';
						html+='<div id="replay" class="button">'+miscArr['replay']+'</div>';
				html+='</div>';
				html+='<div id="div4">';
					html+='<div id="ques1"><div id="t1" class="txtt"></div><canvas id="canvas1" class="can" height=100 width=100></canvas></div>';
					html+='<div id="ques2"><div id="t2" class="txtt"></div><canvas id="canvas2" class="can" height=100 width=100></canvas></div>';
					html+='<div id="ques3"><div id="t3" class="txtt"></div><canvas id="canvas3" class="can" height=100 width=100></canvas></div>';
					html+='<div id="ques4"><div id="t4" class="txtt"></div></div>';
				html+='</div>';
				html+='<div id="div6">'+miscArr['enter']+'</div>';
				$('#screenDiv2').html(html);
			
				 question1='<div class="fraction"><div class="frac numerator">'+num1+'</div><div class="frac">'+deno1+'</div></div></b></font></div>';
				 question2='<div class="fraction"><div class="frac numerator">'+num2+'</div><div class="frac">'+deno2+'</div></div></b></font></div>';
				$('#question1').html(replaceDynamicText(promptArr['p201'],numberLanguage,""));
				$('#question2').html(replaceDynamicText(promptArr['p202'],numberLanguage,""));
				$('#textboxa1').html(replaceDynamicText(promptArr['p204'],numberLanguage,""));
				$('#textboxb1').html(replaceDynamicText(promptArr['p205'],numberLanguage,""));
				$('#t1').html(replaceDynamicText(promptArr['p210'],numberLanguage,""));
				$('#t2').html(replaceDynamicText(promptArr['p211'],numberLanguage,""));
				
					show_phrase=0;
					option(2);	
					$('#txt2').attr('disabled',true);
					$('#replay').click(function(){replay();});
					 $('#sub').click(function(e){
					 	submit_radio();
					 });
			break;				
	}
}
function equi(){
	flag_prom=1;
	var html='';
	html+='<div id="line1">'+replaceDynamicText(promptArr['p215'],numberLanguage,"")+'</div>';
	$('#equi_prom').html(html);
	$('#equi_prom').show();
	setTimeout(function(){
		$('#equi_prom').hide();
	},6000);
	
}
function attempt()
{
	if(attempted==1)
	{
		showPrompt(replaceDynamicText(instArr['i101'],numberLanguage,""));
	}
	
}
function attempt_next()
{
	if(again==0)
	{
		showPrompt(replaceDynamicText(instArr['i105'],numberLanguage,""));
	}
	else if(again==1){
		if($('#'+rad_di).hasClass('wrongc')==true){
			attempt_c();
		}
		else{
			showPrompt(replaceDynamicText(instArr['i107'],numberLanguage,""));	
		}	
			
	}
}
function attempt_c(){
	wrng_c=1;
	if(attempted==1 && saw_it==0)
	{
		showPrompt(replaceDynamicText(instArr['i108'],numberLanguage,""));
	}
	if(saw_it==1){
		showPrompt(instArr['i107']);	
	}
	
}
function gcdFunc(a,b){
    var reminder;
    while(b!=0){
        reminder = a % b;
        a = b;
        b = reminder;
    }
    var gcd = a;
    
    return gcd;
}
function option(id)
{
	if(q_type==2){
		num_p1=(num1*2);
		num_p2=(num2);
		deno_p1=deno1*2;
		deno_p2=deno2;
	}
	else if(q_type==1){
		num_p1=(num1*deno2);
		num_p2=(num2*deno1);
		deno_p1=deno1*deno2;
		deno_p2=deno2*deno1;
	}
	else{
		if(level1_attempt==3){
			num_p1=(num1*deno2);
			num_p2=(num2*deno1);
			deno_p1=deno1*deno2;
			deno_p2=deno2*deno1;	
		}
		else{
			if(deno1==3){
				num_p1=(num1*deno2);
				num_p2=(num2*deno1);
				deno_p1=deno1*deno2;
				deno_p2=deno2*deno1;
			}
			else{
				num_p1=(num1*2);
				num_p2=(num2);
				deno_p1=deno2;
				deno_p2=deno2;
			}
			
		}
		
	}
	
	gc=gcdFunc(((num2*deno1)+(num1*deno2)),(deno1*deno2));
	if(id==1){
		if(q_type==2){
			 opt1='<div class="fraction"><div class="frac numerator">'+((num1*2)+(num2))+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
		}
		else{
			 opt1='<div class="fraction"><div class="frac numerator">'+(((num2*deno1)+(num1*deno2))/gc)+'</div><div class="frac">'+((deno1*deno2)/gc)+'</div></div></b></font></div>';
		}
		
	opt2='<div class="fraction"><div class="frac numerator">'+(num1*num2)+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
	opt3='<div class="fraction"><div class="frac numerator">'+(num1+num2)+'</div><div class="frac">'+(deno1+deno2)+'</div></div></b></font></div>';
	if(q_type==1){
		if(deno1>deno2){
			opt4='<div class="fraction"><div class="frac numerator">'+(num1+num2)+'</div><div class="frac">'+(deno1)+'</div></div></b></font></div>';
	
		}
		else{
			opt4='<div class="fraction"><div class="frac numerator">'+(num1+num2)+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
	
		}
		
	}
	else if(q_type==2){
		opt4='<div class="fraction"><div class="frac numerator">'+(num1+num2)+'</div><div class="frac">'+deno2+'</div></div></b></font></div>';
	
	}
	if(q_type==2){
		input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
	input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
	
	}
	else if(q_type==1){
	input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
	input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
		
	}
	else{
		if(level1_attempt==3){
		
		input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
		input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
				
		}
		else{
			
		input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
		input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
			
		}
	}
	ans1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num_p1+'</div><div class="frac">'+deno_p1+'</div></div></b></font></div>';
	ans2='<div class="fraction" id="ans2" style="padding:3px;"><div class="frac numerator">'+num_p2+'</div><div class="frac">'+deno_p2+'</div></div></b></font></div>';
	exp1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num1+'<i> x </i>'+deno2+'</div><div class="frac">'+deno1+' x '+deno2+'</div></div></b></font></div>';
	exp2='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num2+'<i> x</i> '+deno1+'</div><div class="frac">'+deno2+' x '+deno1+'</div></div></b></font></div>';
	
	a1_val=[(deno1+deno2),(deno1*deno2)];
	b1_val=[(num1*num2),(deno1*deno2)];
	c1_val=[(num1+num2),(deno1+deno2)];
	d1_val=[(num1+num2),(deno1*deno2)];
		arr_shuffled=arrayShuffle(arr_to_shuffle);
				for(var i=0;i<4;i++)
				{
						$('#l'+(i+1)+'1').html(replaceDynamicText(miscArr['misc10'+arr_shuffled[i]],numberLanguage,""));	
					if(arr_shuffled[i]==1)
					{
						var sel_let;
						if(i==0){
							sel_let="a";
						}
						else if(i==1){
							sel_let="b";
						}
						else if(i==2){
							sel_let="c";
						}
						else if(i==3){
							sel_let="d";
						}
						
						right_ans[0]=sel_let+id;
						$('#'+sel_let+id).addClass('correct');
					}
					if(arr_shuffled[i]==3){
						var sel_let;
						if(i==0){
							sel_let="a";
						}
						else if(i==1){
							sel_let="b";
						}
						else if(i==2){
							sel_let="c";
						}
						else if(i==3){
							sel_let="d";
						}
							$('#'+sel_let+id).addClass('wrongc');
					}
				}
	}
	else if(id==2){
		if(deno1==4){
			 opt1='<div class="fraction"><div class="frac numerator">'+(1)+'</div><div class="frac">'+8+'</div></div></b></font></div>';
		}
		else if(deno1==2 && deno2==4){
			opt1='<div class="fraction"><div class="frac numerator">'+1+'</div><div class="frac">'+4+'</div></div></b></font></div>';
	
		}
		else{
			opt1='<div class="fraction"><div class="frac numerator">'+((num1*deno2)-(num2*deno1))+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
	
		}
		if(num1==2){
			opt2='<div class="fraction"><div class="frac numerator">'+1+'</div><div class="frac">'+4+'</div></div></b></font></div>';
			opt3='<div class="fraction"><div class="frac numerator">'+1+'</div><div class="frac">'+7+'</div></div></b></font></div>';
			opt4='<div class="fraction" ><div class="frac numerator" style="border-bottom: 0px solid;">'+1+'</div></div></b></font></div>';
			
		}
		else{
			if(deno1==3 && deno2==4){
			
			opt2='<div class="fraction"><div class="frac numerator">'+num1+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
				
			}
			else{
			
			opt2='<div class="fraction"><div class="frac numerator">'+num1+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
				
			}opt3='<div class="fraction"><div class="frac numerator">'+num1+'</div><div class="frac">'+(deno1+deno2)+'</div></div></b></font></div>';
			if(deno2-deno1 == 1){
				opt4='<div class="fraction"><div class="frac numerator"  style="border-bottom: 0px solid;">'+1+'</div></div></b></font></div>';
			}
			else{
				opt4='<div class="fraction"><div class="frac numerator">'+num1+'</div><div class="frac">'+(deno2-deno1)+'</div></div></b></font></div>';
				
			}
			
		}

			if(level1_attempt==3){
		
				input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
				input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno1*deno2)+'</div></div></b></font></div>';
				ans1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num_p1+'</div><div class="frac">'+deno_p1+'</div></div></b></font></div>';
				ans2='<div class="fraction" id="ans2" style="padding:3px;"><div class="frac numerator">'+num_p2+'</div><div class="frac">'+deno_p2+'</div></div></b></font></div>';
				exp1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num1+'<i> x </i>'+deno2+'</div><div class="frac">'+deno1+' x '+deno2+'</div></div></b></font></div>';
				exp2='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num2+'<i> x</i> '+deno1+'</div><div class="frac">'+deno2+' x '+deno1+'</div></div></b></font></div>';

		}
		else{
			if(deno1==3){
				
				input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno2*deno1)+'</div></div></b></font></div>';
				input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno2*deno1)+'</div></div></b></font></div>';
				exp1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num1+'<i> x </i>'+deno2+'</div><div class="frac">'+deno1+' x '+deno2+'</div></div></b></font></div>';
				exp2='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num2+'<i> x</i> '+deno1+'</div><div class="frac">'+deno2+' x '+deno1+'</div></div></b></font></div>';

			}
			else{
			
				input_1='<div class="fraction"><div class="frac numerator">'+input3+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
				input_2='<div class="fraction"><div class="frac numerator">'+input4+'</div><div class="frac">'+(deno2)+'</div></div></b></font></div>';
				exp1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num1+'<i> x </i>'+2+'</div><div class="frac">'+deno1+' x '+2+'</div></div></b></font></div>';
				exp2='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num2+'<i> x</i> '+1+'</div><div class="frac">'+deno2+' x '+1+'</div></div></b></font></div>';

			}
				ans1='<div class="fraction" id="ans1" style="padding:3px;"><div class="frac numerator">'+num_p1+'</div><div class="frac">'+deno_p1+'</div></div></b></font></div>';
				ans2='<div class="fraction" id="ans2" style="padding:3px;"><div class="frac numerator">'+num_p2+'</div><div class="frac">'+deno_p2+'</div></div></b></font></div>';
			
		}
	
	a1_val=[(deno1+deno2),(deno1*deno2)];
	b1_val=[(num1*num2),(deno1*deno2)];
	c1_val=[(num1+num2),(deno1+deno2)];
	d1_val=[(num1+num2),(deno1*deno2)];
		arr_shuffled=arrayShuffle(arr_to_shuffle);
				for(var i=0;i<4;i++)
				{
						$('#l'+(i+1)+'1').html(replaceDynamicText(miscArr['misc20'+arr_shuffled[i]],numberLanguage,""));	
					if(arr_shuffled[i]==1)
					{
						var sel_let;
						if(i==0){
							sel_let="a";
						}
						else if(i==1){
							sel_let="b";
						}
						else if(i==2){
							sel_let="c";
						}
						else if(i==3){
							sel_let="d";
						}
						
						right_ans[0]=sel_let+(id-1);
						$('#'+sel_let+(id-1)).addClass('correct');
					}
					
				}
	}
}
function input_box(){
	input1='<input type="text" id="txt1" class="txt" style="width:50px; text-align:center; font-size:20px;"  maxlength="5" />';
	input2='<input type="text" id="txt2" class="txt" style="width:50px; text-align:center; font-size:20px;"  maxlength="5" />';
	input3='<input type="text" id="txt3" class="txt" style="width:50px; text-align:center; font-size:20px;"  maxlength="5" />';
	input4='<input type="text" id="txt4" class="txt" style="width:50px; text-align:center; font-size:20px;"  maxlength="5" />';
}
function generateRandomNo(start,end)
{
    var range = end - start;
	var random= Math.floor((Math.random()*range)+start);
	return random;

}
function setOutputParamter()
{
	
	levelsAttempted = "";		
	levelWiseStatus = "";		
	levelWiseScore 	= "";
	levelWiseTimeTaken = "";
	extraParameters = "";
			
	for(var i=0;i<(levelAttemptedArr.length);i++)
	{
		levelsAttempted += "L"+levelAttemptedArr[i]+"|";
		levelWiseStatus += levelWiseStatusArr[levelAttemptedArr[i]-1]+"|";
		levelWiseScore  += levelWiseScoreArr[levelAttemptedArr[i]-1]+"|";	
		levelWiseTimeTaken  += levelWiseTimeTakenArr[levelAttemptedArr[i]-1]+"|";
		extraParameters     += extraParametersArr[levelAttemptedArr[i]-1]+"|";		
	}
		
	
	levelsAttempted = levelsAttempted.substr(0,(levelsAttempted.length-1));
	levelWiseStatus = levelWiseStatus.substr(0,(levelWiseStatus.length-1));
	//levelWiseScore  = levelWiseScore.substr(0,(levelWiseScore.length-1));
	levelWiseScore  =0;
	levelWiseTimeTaken  = levelWiseTimeTaken.substr(0,(levelWiseTimeTaken.length-1));	
    extraParameters  = extraParameters.substr(0,(extraParameters.length-1));
}
function generate_ques(arr){
			var x= arr[Math.floor(Math.random()*arr.length)];
			arr.splice(arr.indexOf(x),1);
			return x;
}
function check_answer(id){
	textbx=id;
	$('#'+id).attr('disabled',true);
	if(id=="txt1" || id=="txt2"){		
		if ($('#'+id).val().indexOf('/') == -1) {
			attempted--;
			showPrompt(instArr['i111']);
		}
		else{
			var p=$('#'+id).val().split('/');
			var q=p[0];
			var s=p[1];
			if(id=="txt1"){
				if(q_type==2){
					
					if(q==num1*2 && s==deno1*2){
						ans_flag=1;
						$('#'+id).addClass('green').attr('disabled',true);
						showPrompt(instArr['correct']);
					}
					else{
						wrong();
					}	
				}
				else if(q_type==1){
				
					if(q==num1*deno2 && s==deno1*deno2){
						ans_flag=1;
						$('#'+id).addClass('green').attr('disabled',true);
						showPrompt(instArr['correct']);
					}
					else{
						wrong();
					}		
				}
				else{
					if(level1_attempt==1){
						if(q==num1*2 && s==deno2){
							ans_flag=1;
							$('#'+id).addClass('green').attr('disabled',true);
							showPrompt(instArr['correct']);
						}
						else{
							wrong();
						}	
					}
					else if(level1_attempt==2){
						if(deno1==3){
							if(q==num1*deno2 && s==deno2*deno1){
								ans_flag=1;
								$('#'+id).addClass('green').attr('disabled',true);
								showPrompt(instArr['correct']);
							}
							else{
								wrong();
							}	
						}
						else{
							if(q==num1*2 && s==deno2){
								ans_flag=1;
								$('#'+id).addClass('green').attr('disabled',true);
								showPrompt(instArr['correct']);
							}
							else{
								wrong();
							}	
						}
						
					}
					else{
						if(q==num1*deno2 && s==deno2*deno1){
							ans_flag=1;
							$('#'+id).addClass('green').attr('disabled',true);
							showPrompt(instArr['correct']);
						}
						else{
							wrong();
						}	
					}
						
				}
			}
			else if(id=="txt2"){
				if(q_type==1){
					if(q==num2*deno1 && s==deno1*deno2){
						ans_flag=1;
						$('#'+id).addClass('green').attr('disabled',true);
						showPrompt(instArr['correct']);
					}
					else{
						wrong();
					}
				}
				else if(q_type==2){
					if(q==num2 && s==deno2){
						ans_flag=1;
						$('#'+id).addClass('green').attr('disabled',true);
						showPrompt(instArr['correct']);
					}
					else{
						wrong();
					}
				}
				else{
					if(level1_attempt==1){
						if(q==num2 && s==deno2){
							ans_flag=1;
							$('#'+id).addClass('green').attr('disabled',true);
							showPrompt(instArr['correct']);
						}
						else{
							wrong();
						}
					}
					else if(level1_attempt==2){
						if(deno1==3){
							if(q==num2*deno1 && s==deno2*deno1){
								ans_flag=1;
								$('#'+id).addClass('green').attr('disabled',true);
								showPrompt(instArr['correct']);
							}
							else{
								wrong();
							}
						}
						else{
							if(q==num2 && s==deno2){
								ans_flag=1;
								$('#'+id).addClass('green').attr('disabled',true);
								showPrompt(instArr['correct']);
							}
							else{
								wrong();
							}
						}
						
					}
					else{
						if(q==num2*deno1 && s==deno2*deno1){
							ans_flag=1;
							$('#'+id).addClass('green').attr('disabled',true);
							showPrompt(instArr['correct']);
						}
						else{
							wrong();
						}
					}
					
				}
						
			}
		}		
	}
	else if(id=="txt3" || id=="txt4"){
		var q=$('#'+id).val();
		if(q_type==2){
			if((q==num1*2 && id=="txt3") || ((q==num2 && id=="txt4"))){
				ans_flag=1;
				$('#'+id).addClass('green').attr('disabled',true);
				showPrompt(instArr['correct']);
			}
			else{
				wrong1();
			}	
		}
		else if(q_type==1){
			if((q==num1*deno2 && id=="txt3") || ((q==num2*deno1 && id=="txt4"))){
				ans_flag=1;
				$('#'+id).addClass('green').attr('disabled',true);
				showPrompt(instArr['correct']);
			}
			else{
				wrong1();
			}		
		}
		else{
			if((q==num_p1 && id=="txt3") || ((q==num_p2 && id=="txt4"))){
				ans_flag=1;
				$('#'+id).addClass('green').attr('disabled',true);
				showPrompt(instArr['correct']);
			}
			else{
				wrong1();
			}		
		}
	}
}
function wrong(){
	$('#'+textbx).addClass('red');
	if(attempted==1){
		showPrompt(instArr['i103']);	
	}
	else{
		showPrompt(instArr['i104']);
	}
}
function wrong1(){
	$('#'+textbx).addClass('red');
	if(q_type==3){
		
		if(textbx=="txt3"){
			showPrompt(replaceDynamicText(instArr['i105f'],numberLanguage,""));	
		}
		else{
			showPrompt(replaceDynamicText(instArr['i105g'],numberLanguage,""));	
		}	
	}
	else{
		if(textbx=="txt3"){
			showPrompt(replaceDynamicText(instArr['i105'],numberLanguage,""));	
		}
		else{
			showPrompt(replaceDynamicText(instArr['i105a'],numberLanguage,""));	
		}		
	}
	
}
function hint(){
	hint_1=1;
	if(q_type==1){
		deno=deno1*deno2;
	}
	else if(q_type==2){		
		deno=deno2;
	}
	else{
		if(level1_attempt==1){
			deno=deno2;
		}
		else if(level1_attempt==2){
			if(deno1==3){
				deno=deno2*deno1;
			}
			else{
				deno=deno2;
			}
			
		}
		else{
			deno=deno1*deno2;
		}
	}
	showPrompt(replaceDynamicText(instArr['i112'],numberLanguage,""));
}
function blur(id){
	$('#'+id).css({'opacity':'0.35'});
	if(id=="div1"){
		$('#sub').hide();
	}
}
function unblur(id){
	$('#'+id).css({'opacity':'1'});
}
function click_shade(){
	$('.rad1').attr('disabled',true);
	$('#div2').hide();
	saw_it=1;
	sign="+";
click_shaded=1;
	var html='';			
		html+='<div id="ques1" style="width: 715px;"><div id="t1" class="txtt"></div><div id="rect1"></div><div id="submit1" class="submit button"></div></div>';
		html+='<div id="ques2" style="width: 715px;"><div id="t2" class="txtt"></div><div id="rect2"></div><div id="submit2" class="submit button"></div></div>';
		html+='<div id="ques3" style="width: 715px;"><div id="t3" class="txtt"></div><div id="rect3"></div><div id="submit3" class="submit button"></div></div>';
		html+='<div id="ques4" style="width: 715px;"><div id="t4" class="txtt"></div><select id="drp" onchange="select(id)"><option value="default" id="def">'+miscArr['select']+'</option><option value="opt1" id="opt1">'+miscArr['yes']+'</option><option value="opt2" id="opt2">'+miscArr['no']+'</option><option value="opt3" id="opt3">'+miscArr['notsure']+'</option></select><div id="hand"></div></div>';
		html+='<div id="ques5" style="width: 715px;"><div id="t5" class="txtt"></div><div id="rect4"></div></div>';
	
	$('#div4').html(html);
	$('#div4').show();
	$('.submit').html(miscArr['submit']);
	$('#t1').html(replaceDynamicText(promptArr['p210'],numberLanguage,""));
	$('#t2').html(replaceDynamicText(promptArr['p211'],numberLanguage,""));
	$('#t3').html(replaceDynamicText(promptArr['p212'],numberLanguage,""));
	$('#t4').html(replaceDynamicText(promptArr['p213'],numberLanguage,""));
	$('#t5').html(replaceDynamicText(promptArr['p214'],numberLanguage,""));
		var html1='';
		for(var i=0;i<deno1;i++){
			html1+='<div id="dv1'+(i+1)+'" class="rect1" style=" width:'+((210/deno1)-2)+'px; display:inline-block; height:25px;"></div>';
				
		}	
		$('#rect1').html(html1);
	
		var html2='';
		for(var i=0;i<deno2;i++){
			html2+='<div id="dv2'+(i+1)+'" class="rect2" style=" width:'+((210/deno2)-2)+'px; display:inline-block; height:25px;"></div>';		
		}	
		$('#rect2').html(html2);
		var html3='';
		for(var i=0;i<(deno1+deno2);i++){
			html3+='<div id="dv3'+(i+1)+'" class="rect3" style=" width:'+((210/(deno1+deno2))-2)+'px; display:inline-block; height:25px;"></div>';		
		}	
		$('#rect3').html(html3);
	
	
		if(q_type==1){
	
			/*var html4='';
			for(var i=0;i<(deno1*deno2);i++){
				html4+='<div id="dv4'+(i+1)+'" class="rect4" style="width:'+(198/(deno1*deno2))+'px; display:inline-block; height:25px;"></div>';		
			}	
			$('#rect4').html(html4);
				for(var i=0;i<((deno1+deno2));i++){
				$('#dv4'+(i+1)).css({'background-color':'pink'});
			}*/
			var html4='';
			for(var i=0;i<(deno1*deno2);i++){
				html4+='<div id="dv4'+(i+1)+'" class="rect4" style="width:'+((210/(deno1*deno2))-2)+'px; display:inline-block; height:25px; border-color:transparent"></div>';		
			}	
			$('#rect4').html(html4);
			for(var i=0;i<((deno1+deno2));i++){
				if(i<deno1){
					$('#dv4'+(i+1)).css({'background-color':'pink','border-color':'transparent'});
				}
				else{
					$('#dv4'+(i+1)).css({'background-color':'blue','border-color':'transparent'});
				}
			}
		}
		else{
			var gc=gcdFunc(((num2*deno1)+(num1*deno2)),(deno1*deno2));
			var html4='';
			for(var i=0;i<(deno2);i++){
				html4+='<div id="dv4'+(i+1)+'" class="rect4" style=" width:'+(((210/(deno2))-2))+'px; display:inline-block; height:25px; "></div>';		
			}	
			$('#rect4').html(html4);
			
			for(var i=0;i<(((2*(num1))+num2));i++){
				if(i<(2*num1)){
					$('#dv4'+(i+1)).css({'background-color':'blue'});
				}
				else{
					$('#dv4'+(i+1)).css({'background-color':'pink'});
				}
				
			}
		}
		
		
	$('.rect1').click(function(){
		if(deno2>deno1){
			if(rect1flag==0){
				if($(this).css('background-color')=="rgb(255, 255, 255)"){
					$(this).css({'background-color':'blue'});
					counter1++;	
				}
				else if($(this).css('background-color')=="rgb(0, 0, 255)"){
					$(this).css({'background-color':'white'});
						counter1--;	
				}	
			}
		}
		else{
			if(rect1flag==0){
				if($(this).css('background-color')=="rgb(255, 255, 255)"){
					$(this).css({'background-color':'pink'});	
						counter1++;
				}
				else if($(this).css('background-color')=="rgb(255, 192, 203)"){
					$(this).css({'background-color':'white'});	
						counter1--;
				}	
			}
		}
		
	});
	$('.rect2').click(function(){
		if(deno1>deno2){
			if(rect2flag==0){
				if($(this).css('background-color')=="rgb(255, 255, 255)"){
					$(this).css({'background-color':'blue'});
					counter2++;	
				}
				else if($(this).css('background-color')=="rgb(0, 0, 255)"){
					$(this).css({'background-color':'white'});
						counter2--;	
				}	
			}
		}
		else{
			if(rect2flag==0){
				if($(this).css('background-color')=="rgb(255, 255, 255)"){
					$(this).css({'background-color':'pink'});	
						counter2++;
				}
				else if($(this).css('background-color')=="rgb(255, 192, 203)"){
					$(this).css({'background-color':'white'});	
						counter2--;
				}	
			}
		}
	});
	$('.rect3').click(function(){
		if(rect3flag==0){
			if($(this).css('background-color')=="rgb(255, 255, 255)"){
				$(this).css({'background-color':'green'});
					counter3++;	
			}
			else if($(this).css('background-color')=="rgb(0, 128, 0)"){
				$(this).css({'background-color':'white'});	
				counter3--;
			}	
		}
	});
	$('#submit1').click(function(){
		if(counter1==0){
			showPrompt(instArr['i114']);			
		}
		else{
			if(counter1==num1){
				showPrompt(instArr['correct']);
			}
			else{
				if(num1==1){
					showPrompt(replaceDynamicText(instArr['i115a'],numberLanguage,""));
				}
				else{
					showPrompt(replaceDynamicText(instArr['i115'],numberLanguage,""));
				}
				
			}	
		}
	});
	$('#submit2').click(function(){
		if(counter2==0){
			showPrompt(instArr['i114']);			
		}
		else{
			if(counter2==num2){
				showPrompt(instArr['correct']);
			}
			else{		
				if(num2==1){
					showPrompt(replaceDynamicText(instArr['i116a'],numberLanguage,""));
				}
				else{
					showPrompt(replaceDynamicText(instArr['i116'],numberLanguage,""));
				}
			}
		}
	});
	$('#submit3').click(function(){
		if(counter3==0){
			showPrompt(instArr['i114']);			
		}
		else{
			if(counter3==(num1+num2)){
				showPrompt(instArr['correct']);
			}
			else{
				total1=num1+num2;
				total2=deno1+deno2;
				showPrompt(replaceDynamicText(instArr['i117'],numberLanguage,""));
			}
		}
	});
}

function select(id){
	if(val_drp==0){
		drp_attmpt++;
			 id1 = $('#'+id).children(":selected").attr("id");
				if(id1!="def"){
					$('#drp').attr('disabled',true);
					if(id1=="opt2"){
						showPrompt(instArr['i106']);
						$('#drp').addClass('green');
						$('#drp').removeClass('red');
					}
					else{
						$('#drp').addClass('red');
						if(drp_attmpt==1){
							$('#ques5').show();
							setTimeout(function(){
								$('#drp').attr('disabled',false);
								$('#drp').removeClass('red');
								val_drp=1;
								$("#drp").val("default");
								val_drp=0;
								$('#hand').fadeIn(400).fadeOut(400).fadeIn(400).fadeOut(400).fadeIn(400);
							},2800);
						}	
						else{
							prom_pos=1;
							
							showPrompt(replaceDynamicText(instArr['i113'],numberLanguage,""));	
							
						}
					}
				}
	}

}

function text_explanation(){
	if(q_type==1){
			 opt5='<div class="fraction"><div class="frac numerator">'+(((num2*deno1)+(num1*deno2))/gc)+'</div><div class="frac">'+((deno1*deno2)/gc)+'</div></div></b></font></div>';
	
	}
	else if(q_type==2){
		 opt5='<div class="fraction"><div class="frac numerator">'+((num2)+(num1*2))+'</div><div class="frac">'+((deno2))+'</div></div></b></font></div>';
		
	}
	else{
		if(level1_attempt==3){
			 opt5='<div class="fraction"><div class="frac numerator">'+(((num1*deno2)-(num2*deno1))/gc)+'</div><div class="frac">'+((deno1*deno2)/gc)+'</div></div></b></font></div>';
	
		}
		else{
			if(level1_attempt==2 && deno1==3){
		
				 opt5='<div class="fraction"><div class="frac numerator">'+(((num1*deno2)-(num2*deno1))/gc)+'</div><div class="frac">'+((deno1*deno2)/gc)+'</div></div></b></font></div>';
			
			}
			else{
			
				 opt5='<div class="fraction"><div class="frac numerator">'+(((2*num1)-(num2)))+'</div><div class="frac">'+((deno1*deno2)/gc)+'</div></div></b></font></div>';
		
			}
		}
			
	}
	$('#sub').hide();
	$('#div4').hide();
	if(q_type==3){
		sign="-";
	}
	else{
		sign="+";
	}
	if(click_shaded==2 || q_type==3){
		if(q_type==3){
			$('#div2').hide();
		}
		$('#div3').css({'top':'210px'});
	}
	$('.rad1').attr('disabled',true);
	$('.lable').removeClass('red');
	$('#'+right_ans[0]).parent().addClass('blue');
	$('#'+right_ans[0]).attr('checked',true);
	$('#e1').html(replaceDynamicText(instArr['i105'],numberLanguage,""));

	setTimeout(function(){
		$('#e2').html(replaceDynamicText(instArr['i105b'],numberLanguage,""));
	},2000);

	setTimeout(function(){
		var html5='';
		if(q_type==2){
			for(var i=0;i<(deno2);i++){
				html5+='<div id="dv5'+(i+1)+'" class="rect4" style="border:1px solid; width:'+(198/(deno2))+'px; display:inline-block; height:40px;"></div>';		
			}
			$('#rectan').html(html5);
			$('#e3').html(replaceDynamicText(instArr['i105c'],numberLanguage,""));
		
			for(var i=0;i<(num_p1);i++){
				$('#dv5'+(i+1)).css({'background-color':'pink'});
			}
		}
		else if(q_type==1){
			for(var i=0;i<(deno1*deno2);i++){
				html5+='<div id="dv5'+(i+1)+'" class="rect4" style="border:1px solid; width:'+(198/(deno1*deno2))+'px; display:inline-block; height:40px;"></div>';		
			}
			$('#rectan').html(html5);
			$('#e3').html(replaceDynamicText(instArr['i105c'],numberLanguage,""));
		
			for(var i=0;i<((deno2*num1));i++){
				$('#dv5'+(i+1)).css({'background-color':'pink'});
			}
		}	
		else{
			if(level1_attempt==3){
				for(var i=0;i<(deno1*deno2);i++){
					html5+='<div id="dv5'+(i+1)+'" class="rect4" style="border:1px solid; width:'+(198/(deno1*deno2))+'px; display:inline-block; height:40px;"></div>';		
				}
				$('#rectan').html(html5);
				$('#e3').html(replaceDynamicText(instArr['i105c'],numberLanguage,""));
			
				for(var i=0;i<((deno2*num1));i++){
					$('#dv5'+(i+1)).css({'background-color':'pink'});
				}
			}
			else{
				if(deno1==3){
					for(var i=0;i<(deno2*deno1);i++){
						html5+='<div id="dv5'+(i+1)+'" class="rect4" style="border:1px solid; width:'+(198/(deno2*deno1))+'px; display:inline-block; height:40px;"></div>';		
					}
					$('#rectan').html(html5);
					$('#e3').html(replaceDynamicText(instArr['i105c'],numberLanguage,""));
				
					for(var i=0;i<((num2-num1));i++){
						$('#dv5'+(i+1)).css({'background-color':'pink'});
					}	
				}
				else{
					
					for(var i=0;i<(deno2);i++){
						html5+='<div id="dv5'+(i+1)+'" class="rect4" style="border:1px solid; width:'+(198/(deno2))+'px; display:inline-block; height:40px;"></div>';		
					}
					$('#rectan').html(html5);
					$('#e3').html(replaceDynamicText(instArr['i105c'],numberLanguage,""));
				
					for(var i=0;i<((2*num1));i++){
						$('#dv5'+(i+1)).css({'background-color':'pink'});
					}	
				}
			}
			
		}
			
		
	},6000);


	setTimeout(function(){
		$('#e3').html(replaceDynamicText(instArr['i105d'],numberLanguage,""));
		
		if(q_type==3){
			$('.rect4').css({'background-color':'transparent'});
			if(level1_attempt==3){
				for(var i=0;i<((deno2*num1)-(deno1*num2));i++){
					if(i<(deno2*num1)){
						$('#dv5'+(i+1)).css({'background-color':'pink'});
					}
					else{
						$('#dv5'+(i+1)).css({'background-color':'blue'});
					}
					
				}
			}
			else{
				for(var i=0;i<((2*num1)-(num2));i++){
					if(deno1==3){
						if(i<(num2-num1)){
							$('#dv5'+(i+1)).css({'background-color':'pink'});
						}
						else{
							$('#dv5'+(i+1)).css({'background-color':'blue'});
						}
					}
					else{
						if(i<(2*num1)){
							$('#dv5'+(i+1)).css({'background-color':'pink'});
						}
						else{
							$('#dv5'+(i+1)).css({'background-color':'blue'});	
						}
					}
				
				}
			}
			
		}
		else{
			if(q_type==1){
				
				for(var i=0;i<((deno2*num1)+(deno1*num2));i++){
					if(i<(deno2*num1)){
						$('#dv5'+(i+1)).css({'background-color':'pink'});					
					}
					else{
						$('#dv5'+(i+1)).css({'background-color':'blue'});
					}
				}		
			}
			else{
					
				for(var i=0;i<((num_p1+num_p2));i++){
					if(i<num_p1){
						$('#dv5'+(i+1)).css({'background-color':'pink'});						
					}
					else{
						$('#dv5'+(i+1)).css({'background-color':'blue'});
					}
				}	
			}
		}
	},10000);

	setTimeout(function(){
		$('#e3').html(replaceDynamicText(instArr['i105e'],numberLanguage,""));
	},13000);
	if(level1_attempt<3){
	
		setTimeout(function(){
			if(q_type==3){
			
			$('#replay').show();	
			}
			$('#next').show();
		},15000);	
	}
	else{
		setTimeout(function(){
			$('#next,#div6').hide();
			$('#blank').show();
			completed=1;
		},15000);	
		
	}
}
function replay(){
	$('#e1').html("");
	$('#e2').html("");
	$('#e3').html("");
	$('#rectan').html("");
	$('#replay,#next').hide();
	text_explanation();
}