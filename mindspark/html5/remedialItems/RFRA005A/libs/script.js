var totalTimeTaken = 0;
var completed = 0;
var extraParameters = "";
var noOfLevels;
var levelWiseMaxScores = "0|0|0";
var lastLevelCleared;
var previousLevelLock;
var levelsAttempted= ""; //"L1|L2|L3";
var levelWiseStatus = "0";//0|1|2 – 0: In progress, 1: Pass, 2: Fail
var levelWiseScore = "0|0|0";
var levelWiseTimeTaken= "";//250|120 (in seconds)
var levelWiseTimeTakenArr = new Array(0,0,0);
var levelAttemptedArr = new Array();
var levelWiseStatusArr = new Array(0,0,0);
var extraParametersArr = new Array("","","");
var currentLevel = 1;
var currentScreen = 0;
var numberLanguage;


//------------------------------------------------------------------//
var screen4Counter=1;
var screen4CounterMax=3;
var screen2EventsFlag=1;
var screen3EventsFlag=1;
var screen4EventsFlag=1;
var firstTimeFlag=1;
var blinkFlag=1;
var name1,name2;
var avtrChoice1, avtrChoice2;
var frac1,frac2;
var part1,part2;
var frac1Num,frac2Num;
var okVal=null;
var ateMore='';
var dropDown1,dropDown2,dropDown3,dropDown4,dropDown5,dropDown6,dropDown7;
var correctSign;
var canvasFlag,selection,counter1,counter2,submitCounter,a,b;
var correctOptionDropDown1,correctOptionDropDown2,correctOptionDropDown3,correctOptionDropDown4,correctOptionDropDown5,correctOptionDropDown6,correctOptionDropDown7;
var dropDown5Counter;
var questionCorrect=0;

var q1Frac,q2Frac;
var q1Num,q1Den;
var q2Num,q2Den;
var qUsed = new Array();
var qArray = new Array();
	for(var i=0;i<12;i++){
		qArray[i] = new Array();
	}
qArray[0] = ['2|4','2|5'];	
qArray[1] = ['2|3','2|7'];	
qArray[2] = ['1|2','1|6'];	
qArray[3] = ['1|4','1|6'];	
qArray[4] = ['2|5','2|6'];	
qArray[5] = ['1|3','1|7'];	
qArray[6] = ['2|4','2|6'];	
qArray[7] = ['2|4','2|7'];	
qArray[8] = ['2|5','2|7'];	
qArray[9] = ['1|5','1|6'];	
qArray[10] = ['1|3','1|5'];	
qArray[11] = ['2|7','2|6'];	
var qUsed = new Array();

//------------------------------------------------------




(function($){
    $.fn.writeHTML = function(content,callback){
        var contentArray = content.split(""),
        current = 0,
        elem = this;
        window.setInterval(function(){
            if(current < contentArray.length){
				elem.html(elem.html() + contentArray[current++]);
            }
			else if(current == contentArray.length){
				current++;
				if(callback) callback();
			}
        },150);
    };
})(jQuery);

$(document).ready(function(e){
	// Create the loader and queue all images. Images will not 
	// begin downloading until we tell the loader to start.
	var imageArray = new Array('avatar1.png','avatar2.png','avatar3.png','avatar4.png','avatar5.png','avatar6.png','avatar7.png','avatar8.png','bg.jpg','chocolate.png','scroll.png');
	var loader = new PxLoader(); 
	$.each(imageArray,function(key,value){
		var pxImage = new PxLoaderImage('../assets/'+value); 
		loader.add(pxImage);
	});
	
	loader.addCompletionListener(function () {
        var gameXMLFile = "XML.xml";
        //Loading XML file for data..

        loadXML(gameXMLFile, function () {
            var getParameters = getURLParameters();
            
            if (typeof getParameters['noOfLevels'] == "undefined") { showPrompt("Pls pass the noOfLevel"); } else noOfLevels = getParameters['noOfLevels'];
            if (typeof getParameters['lastLevelCleared'] == "undefined") { $("#OK").hide(); showPrompt("Pls pass the lastLevelCleared"); } else lastLevelCleared = getParameters['lastLevelCleared'];
            if (typeof getParameters['previousLevelLock'] == "undefined") { $("#OK").hide(); showPrompt("Pls pass the previousLevelLock"); } else previousLevelLock = getParameters['previousLevelLock'];
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
                    "margin-left": "+=20px",
                    "opacity": "0.5",
                    "cursor": "default"
                });
                for (var i = 0; i < (parseInt(lastLevelCleared) + 1); i++) {
                    $("#level" + (i + 1)).css({ "opacity": "1", "cursor": "pointer" });
                    $("#lock" + i).hide();

                }

                $('#PreLoader').css({ 'display': 'none' });
                previousLevelLock = parseInt(previousLevelLock);
                if (!previousLevelLock) {
                    $('#landingPage').fadeIn(200);

                }
                else if (lastLevelCleared == 0) {
                    $('#landingPage').hide();
                    $('#mainDiv').fadeIn(200);
                    showScreen(1);
                    startGameTimer();
                }
                else if (lastLevelCleared == 1) {
                    $('#landingPage').hide();
                    $('#mainDiv').fadeIn(200);
                    showScreen(5);
                    startGameTimer();
                }
                else if (lastLevelCleared == 2) {
                    $('#landingPage').hide();
                    $('#mainDiv').fadeIn(200);
                    showScreen(7);
                    startGameTimer();
                }
                //showScreen(1);
                //startGameTimer();
            }
        });
    });
	 
	loader.start();
	$('.promptContainer').draggable();	
    $("#level1").live('click', function () {
        $('#landingPage').hide();
        $('#mainDiv').fadeIn(200);
        showScreen(1);
        startGameTimer();
    });


    $("#level2").live('click', function () {
        if (lastLevelCleared > 0) {
            $('#landingPage').hide();
            $('#mainDiv').fadeIn(200);
            showScreen(5);
            startGameTimer();
        }

    });

     $("#OK").live("click", function () {
        $("#screenModal").hide();
    });

    $("#level3").live('click', function () {
        if (lastLevelCleared > 1) {
            $('#landingPage').hide();
            $('#mainDiv').fadeIn(200);
            showScreen(7);
            startGameTimer();
        }
    });


	$(".textInput").keypress(function(e) {
        var a = [];
        var k = e.which;
    
        for (x = 48; x < 58; x++)
            a.push(x);
    
        if (!($.inArray(k,a)>=0) && e.keyCode != 9 && e.keyCode != 8)
            e.preventDefault();
    });
	
});

function resize()
{ 
    var scaleFactor = 1;
	if(window.innerHeight < 600) {
		scaleFactor = parseFloat(window.innerHeight/600); //console.log("height "+window.innerWidth+'-'+window.innerHeight+"-"+scaleFactor);
	} else if(window.innerWidth < 800) {
		scaleFactor = parseFloat(window.innerWidth/800); //console.log("width "+window.innerWidth+'-'+window.innerHeight+"-"+scaleFactor);
	} else{
		scaleFactor = 1 ;									
	} 	
    $("#mainDiv").css({"-webkit-transform": "scale("+scaleFactor+")"});
	$("#mainDiv").css({"-moz-transform": "scale("+scaleFactor+")"});	
	$("#mainDiv").css({"-o-transform": "scale("+scaleFactor+")"});	
	$("#mainDiv").css({"-ms-transform": "scale("+scaleFactor+")"});	
	$("#mainDiv").css({"transform": "scale("+scaleFactor+")"});	
}


function startGameTimer(){
    if (completed != 1) {
        totalTimeTaken++;
        levelWiseTimeTakenArr[currentLevel - 1]++;
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
	$(".promptText").html(msg);
	$("#screenModal").show();
}

function blinker(id){
	if(blinkFlag){
		$(id).fadeOut(500);
		setTimeout(function(){
			$(id).fadeIn(200,function(){
				blinker(id);
			});
		},300);		
	}
	else{
		$(id).show();
	}
}

function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function showAnimation(screenNo){
	switch(screenNo){
		case 1:
			currentScreen = 1;	
			levelAttemptedArr.push(1);
			startGameTimer();
			blinker('#q1');
			$('#t1').html(promptArr['p100']);
			$('#q1').html(promptArr['p123']);
			$('#n1').val(promptArr['p101']);
			$('#n2').val(promptArr['p102']);
			$('#start').html(promptArr['p125']);
		
			for(var i=1;i<=8;i++){
				$('#avatar'+i).css({
					'background':'url(../assets/avatar'+i+'.png) 90%/90% no-repeat'
				});
			}
		
			var chance=1;
			var flag=0;
			$('.nameInput').on('click',function(){
				if(flag==0){
					$(this).val('');
					flag=1;				
				}
			});
			
			$('#n1').live('keyup',function(e){
				e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
				if(e.keyCode == 13 && $('#n1').attr('value')!='') {
					blinkFlag=1;
					blinker('#q1');
					$('#'+avtrChoice1).hide();
					$('.avatar').css('border','none');
					$('#q1').html(promptArr['p124']);
					$('#n1').attr('disabled','disabled');
					name1 = $('#n1').attr('value');
					name1 = capitaliseFirstLetter(name1);
					flag=0;
					chance=2;
				}
			});
			$('#n2').live('keypress',function(e){
				e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
				if(e.keyCode == 13){
					if($('#n2').attr('value')!='' && $('#n2').attr('value')!=$('#n1').attr('value')){
						$('#n2').attr('disabled','disabled');
						name2 = $('#n2').attr('value');
						name2 = capitaliseFirstLetter(name2);
						$('#start').show();
						$('#start').focus();
						chance=0;
					}
					else{
						$('#n2').attr('value','');
					}
				}
			});
			
			$('.avatar').on('click',function(){
				blinkFlag=0;
				$('.avatar').css('border','none');
				$(this).css({
						'border':'2px solid white'
				});
				if(chance==1){
					avtrChoice1 = this.id;
					$('#n1').show();
					//$('#n1').focus();	
					
				}
				else 
				if(chance==2){
					avtrChoice2 = this.id;
					$('#n2').show();
					//$('#n2').focus();
				}
			});
			$('#start').live('click',function(){
				showScreen(2);
			});
			
			break;
		
		case 2:
			currentScreen = 2;	
			getScreen2Html();
			divideChocolates();
			randomChocPos();
			
			$('#player1').html(name1);			
			$('#player2').html(name2);		
			$('#submit').html(promptArr['p117']);
			$('#reset').html(promptArr['p127']);
			$('#picture1').css('background','url(../assets/'+avtrChoice1+'.png) 90%/90% no-repeat');	
			$('#picture2').css('background','url(../assets/'+avtrChoice2+'.png) 90%/90% no-repeat');	
			$('#picture1,#picture2').css('background-color','black');
			$('#t2').html(replaceDynamicText(promptArr['p107'],numberLanguage,''));
			$('#next').html(promptArr['p104']);
			$('#bag1Header').html(replaceDynamicText(promptArr['p105'],numberLanguage,''));
			$('#bag2Header').html(replaceDynamicText(promptArr['p106'],numberLanguage,''));
			$('.serveClick').html(promptArr['p112']);
			
			okVal=null;
			canvasFlag=0;
			selection=0;
			counter1=1,counter2=1;
			submitCounter=0;
			a = frac1Num/part1;
			b = frac2Num/part2;
			correctOptionDropDown1 = (a>b)?1:2;
			ateMore = eval('name'+correctOptionDropDown1);
		
			if(a==b){
				correctOptionDropDown2 = 3;
				correctSign = '=';
			}
			else if(a>b){
				correctOptionDropDown2 = 2;
				correctSign = '&gt;'
			}
			else if(a<b){
				correctOptionDropDown2 = 1;
				correctSign = '&lt';
			}
		    dropDown2Counter=0;
			
			if(screen2EventsFlag==1){
				screen2Events();	
				screen2EventsFlag=0;
			}
			
			
			break;
		
		case 3:
			currentScreen = 3;
			getScreen3Html();
			$('#screen3Line1').html(replaceDynamicText(promptArr['p132'],numberLanguage,''));
			$('#screen3Line2').html(replaceDynamicText(promptArr['p136'],numberLanguage,''));
			$('#screen3Line3').html(replaceDynamicText(promptArr['p137'],numberLanguage,''));
			$('#screen3Next').html(promptArr['p104']);
			$('#topPart').html($('#appendDiv1').html());
			$('#bottomPart').html($('#appendDiv2').html());	
			$('#topPart').append(frac1+"&nbsp;&nbsp;&nbsp;"+name1+"'s share.");
			$('#bottomPart').append(frac2+"&nbsp;&nbsp;&nbsp;"+name2+"'s share.");
			
			dropDown3='';
			dropDown3+='<select id="dropDown3">';
				dropDown3+='<option value="0">'+promptArr['p119']+'</option>';
				dropDown3+='<option value="1">'+name1+'</option>';
				dropDown3+='<option value="2">'+name2+'</option>';
			dropDown3+='</select>';
			$('#screen3Line5').html(replaceDynamicText(promptArr['p138'],numberLanguage,''));
			
			correctOptionDropDown3;
			if(frac1Num/part1 > frac2Num/part2){
				correctOptionDropDown3 = 2;
			}
			else correctOptionDropDown3 = 1;
			
			dropDown4='';
			dropDown4+='<select id="dropDown4">';
				dropDown4+='<option value="0">'+promptArr['p119']+'</option>';
				dropDown4+='<option value="1">'+name1+'</option>';
				dropDown4+='<option value="2">'+name2+'</option>';
			dropDown4+='</select>';
			$('#screen3Line6').html(replaceDynamicText(promptArr['p140'],numberLanguage,''));
			
			correctOptionDropDown5 = 1;
			dropDown5Counter=0;
			dropDown5='';
			dropDown5+='<select id="dropDown5">';
				dropDown5+='<option value="0">'+promptArr['p119']+'</option>';
				dropDown5+='<option value="1">'+promptArr['p143']+'</option>';
				dropDown5+='<option value="2">'+promptArr['p144']+'</option>';
				dropDown5+='<option value="3">'+promptArr['p145']+'</option>';
			dropDown5+='</select>';
			$('#screen3Line7').html(replaceDynamicText(promptArr['p142'],numberLanguage,''));
			
			dropDown6='';
			dropDown6+='<select id="dropDown6">';
				dropDown6+='<option value="0"></option>';
				dropDown6+='<option value="1">&gt;</option>';
				dropDown6+='<option value="2">&lt;</option>';
				dropDown6+='<option value="3">=</option>';
			dropDown6+='</select>';
			
			var randomX = genQuestNo();		
			q1Num = parseInt(qArray[randomX][0].split('|')[0]);
			q2Num = parseInt(qArray[randomX][1].split('|')[0]);
			q1Den = parseInt(qArray[randomX][0].split('|')[1]);
			q2Den = parseInt(qArray[randomX][1].split('|')[1]);
			q1Frac = returnFrac(q1Num,q1Den);
			q2Frac = returnFrac(q2Num,q2Den);
			$('#screen3Line8').html(replaceDynamicText(promptArr['p147'],numberLanguage,''));	
			
			if(screen3EventsFlag==1){
				screen3Events();		
				screen3EventsFlag=0;
			}

			break;
		
		case 4:
			currentScreen = 4;
			var html='';
			html+='<div id="screen4Line1"></div>';
			html+='<div id="screen4Next"></div>';
			$('#screenDiv4').html(html);
			$('#screen4Next').html(promptArr['p104']);
			
			dropDown7='';
			dropDown7+='<select id="dropDown7">';
				dropDown7+='<option value="0"></option>';
				dropDown7+='<option value="1">&gt;</option>';
				dropDown7+='<option value="2">&lt;</option>';
				dropDown7+='<option value="3">=</option>';
			dropDown7+='</select>';
			
			var randomX = genQuestNo();
			q1Num = parseInt(qArray[randomX][0].split('|')[0]);
			q2Num = parseInt(qArray[randomX][1].split('|')[0]);
			q1Den = parseInt(qArray[randomX][0].split('|')[1]);
			q2Den = parseInt(qArray[randomX][1].split('|')[1]);
			q1Frac = returnFrac(q1Num,q1Den);
			q2Frac = returnFrac(q2Num,q2Den);
			$('#screen4Line1').html(replaceDynamicText(promptArr['p149'],numberLanguage,''));	
			if(q1Num/q1Den>q2Num/q2Den){
				correctOptionDropDown7 = 1;
			}	
			else correctOptionDropDown7 = 2;
			
			if(screen4EventsFlag==1){
				screen4Events();
				screen4EventsFlag=0;
			}

			break;
			
		case 'default':
			// Do nothing...
				
			break;
	}
}	

function genQuestNo(){
	var temp;
	if(qUsed.length<11){
		do{
			temp = Math.floor(Math.random()*11);
		}
		while(qUsed.indexOf(temp)!=-1);
		qUsed.push(temp);
		return temp;
	}
}

function divideChocolates(){
	if(firstTimeFlag==1){
		var randomX = genQuestNo();	
		frac1Num = parseInt(qArray[randomX][0].split('|')[0]);
		frac2Num = parseInt(qArray[randomX][1].split('|')[0]);
		part1 = parseInt(qArray[randomX][0].split('|')[1]);
		part2 = parseInt(qArray[randomX][1].split('|')[1]);
		frac1 = returnFrac(frac1Num,part1);
		frac2 = returnFrac(frac2Num,part2);
		firstTimeFlag=0;
	}	
		
		for(var t=1;t<part1;t++){
			$("#choc1Canvas").drawLine({
				strokeStyle: "#fff",
				strokeWidth: 2,
				x1:(92/part1)*t, y1:0,
				x2:(92/part1)*t, y2:45,
			});	
		}
		
			$("#choc1Canvas").drawLine({
				strokeStyle: "#fff",
				strokeWidth: 2,
				fillStyle:'#fff',
				x1:92, y1:0,
				x2:92, y2:45,
				x3:99,y3:45,
				x4:99,y4:0,
				closed:true
			});
			
		for(var i=1;i<part2;i++){
			$("#choc1Canvas").drawLine({
				strokeStyle: "#fff",
				strokeWidth: 2,
				x1:(100/part2)*i, y1:55,
				x2:(100/part2)*i, y2:100,
			});
			$("#choc2Canvas").drawLine({
				strokeStyle: "#fff",
				strokeWidth: 2,
				x1:(100/part2)*i, y1:55,
				x2:(100/part2)*i, y2:100,
			});
		}

		for(var i=1;i<part1;i++){
			$("#choc2Canvas").drawLine({
				strokeStyle: "#fff",
				strokeWidth: 2,
				x1:(100/part1)*i, y1:0,
				x2:(100/part1)*i, y2:45,
			});
			$("#choc3Canvas").drawLine({
				strokeStyle: "#fff",
				strokeWidth: 2,
				x1:(100/part1)*i, y1:0,
				x2:(100/part1)*i, y2:45,
			});
		}
		
		var offset=0;
		for(var j=1;j<part2;j++){
			if(j%2==0){
				offset = 50/part2;
			}
			else offset=0;
			
			$('#choc3Canvas').drawLine({
				strokeStyle:'#fff',
				strokeWidth:2,
				x1:(100/part2)*j - 50/part2 + offset,y1:55,
				x2:(100/part2)*j - 50/part2 + offset,y2:100
			});
			
		}
}

function randomChocPos(){
	var pos1 = [53,83];
	var pos2 = [173,83];
	var pos3 = [113,218];
	
	var rd;
	var posArr = [1,2,3];
	posArr = arrayShuffle(posArr);
	
	rd = posArr[0];
	$('#choc1').css({
		'left':eval("pos"+rd+"[0]")+'px',
		'top':eval("pos"+rd+"[1]")+'px'
	});
	$('#choc1Canvas').css({
		'left':eval("pos"+rd+"[0]")+3+'px',
		'top':eval("pos"+rd+"[1]")+3+'px'
	}); 
	
	rd = posArr[1];
	$('#choc2').css({
		'left':eval("pos"+rd+"[0]")+'px',
		'top':eval("pos"+rd+"[1]")+'px'
	});
	$('#choc2Canvas').css({
		'left':eval("pos"+rd+"[0]")+3+'px',
		'top':eval("pos"+rd+"[1]")+3+'px'
	});	
	
	rd = posArr[2];
	$('#choc3').css({
		'left':eval("pos"+rd+"[0]")+'px',
		'top':eval("pos"+rd+"[1]")+'px'
	});
	$('#choc3Canvas').css({
		'left':eval("pos"+rd+"[0]")+3+'px',
		'top':eval("pos"+rd+"[1]")+3+'px'
	});
}

function returnFrac(a,b){
	var html='';
	html+='<div class="fraction">';
		html+='<div class="numerator">'+a+'</div>';
		html+='<div class="frac">'+b+'</div>';
	html+='</div>';
	return html;
}

function getScreen2Html(){
	$('#screenDiv2').html('');
	var html='';
			html+='<div id="t2"></div>';
			html+='<div id="scroll">';
				html+='<canvas id="choc1Canvas" class="canvas" height=100 width=100 value="0"></canvas>';
				html+='<canvas id="choc2Canvas" class="canvas" height=100 width=100 value="0"></canvas>';
				html+='<canvas id="choc3Canvas" class="canvas" height=100 width=100 value="0"></canvas>';
				html+='<div id="choc1" class="chocBlock">';
					html+='<img src="../assets/chocolate.png" style="float:left"></img>';
					html+='<img src="../assets/chocolate.png" style="float:left;margin-top:10px"></img>';
				html+='</div>';
				html+='<div id="choc2" class="chocBlock">';
					html+='<img src="../assets/chocolate.png" style="float:left"></img>';
					html+='<img src="../assets/chocolate.png" style="float:left;margin-top:10px"></img>';
					html+='<div id="topChoc"></div>';
					html+='<div id="bottomChoc"></div>';
				html+='</div>';
				html+='<div id="choc3" class="chocBlock">';
					html+='<img src="../assets/chocolate.png" style="float:left"></img>';
					html+='<img src="../assets/chocolate.png" style="float:left;margin-top:10px"></img>';
				html+='</div>';
			html+='</div>';
			html+='<div id="serve1" class="serveClick button"></div>';
			html+='<div id="serve2"  class="serveClick button"></div>';
			html+='<div id="bag1" class="bag">';
				html+='<div id="bag1Header"></div>';
				html+='<div id="appendDiv1" class="appendDiv"></div>';
			html+='</div>';
			html+='<div id="bag2" class="bag">';
				html+='<div id="bag2Header"></div>';
				html+='<div id="appendDiv2" class="appendDiv"></div>';
			html+='</div>';
			html+='<div id="picture1"></div>';
			html+='<div id="picture2"></div>';
			html+='<div id="player1"></div>';
			html+='<div id="player2"></div>';
			html+='<div id="submit" class="button"></div>';
			html+='<div id="reset" class="button"></div>';
			html+='<div id="info"></div>';
			html+='<div id="infoReplay">';
				html+='<img src="../assets/replay.png">';
			html+='</div>';
			html+='<div id="infoNext"></div>';
			html+='<div id="questionBox">';
				html+='<div id="qLine1"></div>';
				html+='<div id="qLine2"></div>';
			html+='</div>';
			html+='<div id="next"></div>';
		$('#screenDiv2').html(html);
}

function getScreen3Html(){
	var html='';
	html+='<div id="screen3Line1"></div>';
	html+='<div id="screen3Line2"></div>';
	html+='<div id="screen3Line3"></div>';
	html+='<div id="screen3Line4">';
		html+='<div id="topPart"></div>';
		html+='<div id="bottomPart"></div>';
	html+='</div>';
	html+='<div id="screen3Line5"></div>';
	html+='<div id="screen3Line6"></div>';
	html+='<div id="screen3Line7"></div>';
	html+='<div id="screen3Line8"></div>';
	html+='<div id="screen3Next"></div>';
	$('#screenDiv3').html(html);
}

function screen2Events(){
			$('.canvas').live('click',function(){	
				var id  = $(this).attr('id');
				var val = parseInt($(this).attr('value'));
				val++;
				$(this).attr('value',val);
				if(val==2 && canvasFlag==0){
					canvasFlag=1;
					$('.serveClick').show();
					$('#t2').html(replaceDynamicText(promptArr['p113'],numberLanguage,''));
					$('#choc2').css('opacity','1');
					$('.canvas').css('cursor','default');
					
					showPrompt(replaceDynamicText(promptArr['p110'],numberLanguage,''));
				}
				else if(canvasFlag==0){
					if(id=='choc1Canvas'){
						showPrompt(replaceDynamicText(promptArr['p109'],numberLanguage,''));
					}
					else if(id=='choc2Canvas'){
						canvasFlag=1;
						$('.serveClick').show();
						showPrompt(replaceDynamicText(promptArr['p108'],numberLanguage,''));
						$('#t2').html(replaceDynamicText(promptArr['p113'],numberLanguage,''));
						$('#choc2').css('opacity','1');
						$('.canvas').css('cursor','default');
						
					}
					else if(id=='choc3Canvas'){
						showPrompt(replaceDynamicText(promptArr['p126'],numberLanguage,''));
					}		
				}
			});
			
		$('.serveClick').live('click',function(){
			var id = $(this).attr('id');
			if(id=='serve1'){
				$('.bag').css('opacity',1);
				$('#bag2').css('opacity',0.5);
				$('#topChoc').css('opacity',0);
				$('#bottomChoc').css('opacity',0.5);
				$('#topChoc').css('cursor','pointer');
				$('#bottomChoc').css('cursor','default');
				selection=1;
			}
			else{
				$('.bag').css('opacity',1);
				$('#bag1').css('opacity',0.5);
				$('#topChoc').css('opacity',0.5);
				$('#bottomChoc').css('opacity',0);	
				$('#bottomChoc').css('cursor','pointer');
				$('#topChoc').css('cursor','default');
				selection=2;	
			}
		});
		$('#topChoc').live('click',function(){
			if(selection==1 && counter1<=part1){
				var html='';
				html+='<img id="bag1temp'+counter1+'" src="../assets/chocolate.png" class="tempPart1">';
				$('#appendDiv1').append(html);
				
				var width =113/part1;			// 113 is width of chocolate image.
				$('.tempPart1').css('width',width+'px');
				
				counter1++;	
				if(counter1>1 && counter2>1){
					$('#submit,#reset').show();
				}
			}
		});
		$('#bottomChoc').live('click',function(){
			if(selection==2 && counter2<=part2){
				var html='';
				html+='<img id="bag2temp'+counter2+'" src="../assets/chocolate.png" class="tempPart2">';
				$('#appendDiv2').append(html);
				var width = 113/part2;
				$('.tempPart2').css('width',width+'px');
				counter2++;	
				if(counter1>1 && counter2>1){
					$('#submit,#reset').show();
				}
			}
		});
		$('.tempPart1').live('click',function(){
			if(okVal!='serve' && selection!=0){
				$(this).remove();
				counter1--;	
			}
		});
		$('.tempPart2').live('click',function(){
			if(okVal!='serve' && selection!=0){
				$(this).remove();
				counter2--;		
			}
		});
		$('#reset').live('click',function(){
			$('.tempPart1,.tempPart2').remove();
			counter1=1;
			counter2=1;
		});
		$('#submit').live('click',function(){
			submitCounter++;
			if(counter1==frac1Num+1 && counter2==frac2Num+1){
				showPrompt(replaceDynamicText(promptArr['p116'],numberLanguage,''));
				selection=0;
				$('#topChoc,#bottomChoc').css('opacity',0);
				$('#topChoc,#bottomChoc').css('cursor','default');
				$('#appendDiv1,#appendDiv2').html('');
				for(var i=0;i<part1;i++){
					$('#appendDiv1').append('<img id="bag1temp'+i+'" src="../assets/chocolate.png" class="tempPart1">');
					$('.tempPart1').css('width',(113/part1)+'px');
				}
				for(var j=0;j<part2;j++){
					$('#appendDiv2').append('<img id="bag2temp'+j+'" src="../assets/chocolate.png" class="tempPart2">');
					$('.tempPart2').css('width',(113/part2)+'px');
				}
				$('.tempPart1,.tempPart2').css('cursor','default');
				$('.bag').css('opacity',1);
				$('#submit,#reset,#serve1,#serve2').hide();
				$('.tempPart1,.tempPart2').css('opacity',0.5);
				for(var k=0;k<frac1Num;k++){
					$('#bag1temp'+k).css('opacity',1);
				}
				for(var k=0;k<frac2Num;k++){
					$('#bag2temp'+k).css('opacity',1);
				}
				$('#infoNext').show();
				$('#infoNext').html(promptArr['p104']);
				$('#infoNext').css({
					'left':'732px',
					'top':'573px'
				});
			}
			else {
				if(submitCounter==1){
					showPrompt(replaceDynamicText(promptArr['p115'],numberLanguage,''));
				}
				if(submitCounter==2){
					showPrompt(replaceDynamicText(promptArr['p129'],numberLanguage,''));
					okVal = 'serve';
					$('#bottomChoc,#topChoc').css('opacity',0);
					selection=0;
					$('#bottomChoc,#topChoc').css('cursor','default');
				}	
			}
		});
		$('#OK').live('click',function(){
			if(okVal=='serve'){
				$('#appendDiv1,#appendDiv2').html('');
				for(var i=0;i<part1;i++){
					$('#appendDiv1').append('<img id="bag1temp'+i+'" src="../assets/chocolate.png" class="tempPart1">');
					$('.tempPart1').css('width',(113/part1)+'px');
				}
				for(var j=0;j<part2;j++){
					$('#appendDiv2').append('<img id="bag2temp'+j+'" src="../assets/chocolate.png" class="tempPart2">');
					$('.tempPart2').css('width',(113/part2)+'px');
				}
				$('.tempPart1,.tempPart2').css('cursor','default');
				$('.bag').css('opacity',1);
				$('#submit,#reset,#serve1,#serve2').hide();
				$('.tempPart1,.tempPart2').css('opacity',0.5);
				for(var k=0;k<frac1Num;k++){
					$('#bag1temp'+k).css('opacity',1);
				}
				for(var k=0;k<frac2Num;k++){
					$('#bag2temp'+k).css('opacity',1);
				}
				$('#info').show();
				$('#infoReplay').show();
				$('#info').html(replaceDynamicText(promptArr['p130'],numberLanguage,''));
				var infoStatus=1;
				$('#infoReplay').live('click',function(){
					if(infoStatus==1){
						$('#info').html(replaceDynamicText(promptArr['p131'],numberLanguage,''));	
						infoStatus=2;	
						$('#infoNext').show();
						$('#infoNext').html(promptArr['p104']);
					}
					else{
						$('#info').html(replaceDynamicText(promptArr['p130'],numberLanguage,''));
						infoStatus=1;
					}
				});
			}
		});
		$('#infoNext').live('click',function(){
			$('#infoNext').remove();
			$('#infoReplay').remove();
			$('#scroll').hide();
			$('#info').remove();
			$('#t2').html(replaceDynamicText(promptArr['p132'],numberLanguage,''));
			$('#questionBox').show();
			$('#qLine1').html(promptArr['p118']);
			dropDown1='';
			dropDown1+='<select id="dropDown1">';
				dropDown1+='<option value="0">'+promptArr['p119']+'</option>';
				dropDown1+='<option value="1">'+name1+'</option>';
				dropDown1+='<option value="2">'+name2+'</option>';
			dropDown1+='</select>';
			$('#qLine1').append(dropDown1);
		});
		$('#dropDown1').live('change',function(){
			var value = parseInt($('#dropDown1').val());
			if(correctOptionDropDown1==value){
				//do nothing
			}
			else {
				showPrompt(replaceDynamicText(promptArr['p133'],numberLanguage,''));
				$('#dropDown1').val(correctOptionDropDown1);
			}
			$('#dropDown1').attr('disabled','disabled');
			dropDown2='';
			dropDown2+='<select id="dropDown2">';
				dropDown2+='<option value="0"></option>';
				dropDown2+='<option value="1">&lt;</option>';
				dropDown2+='<option value="2">&gt;</option>';
				dropDown2+='<option value="3">=</option>';
			dropDown2+='</select>';
			$('#qLine2').html(replaceDynamicText(promptArr['p120'],numberLanguage,''));
		});
		$('#dropDown2').live('change',function(){
			dropDown2Counter++;
			var value = parseInt($('#dropDown2').val());
			if(value==correctOptionDropDown2){
				showPrompt(promptArr['p128']);
				$('#dropDown2').attr('disabled','disabled');
				$('#next').show();
			}
			else {
				if(dropDown2Counter==1){
					showPrompt(replaceDynamicText(promptArr['p121'],numberLanguage,''));
				}
				else if(dropDown2Counter==2){
					if(correctOptionDropDown2==2){
						showPrompt(replaceDynamicText(promptArr['p134'],numberLanguage,''));
						$('#dropDown2').val(2);
					}
					if(correctOptionDropDown2==1){
						showPrompt(replaceDynamicText(promptArr['p135'],numberLanguage,''));
						$('#dropDown2').val(1);
					}
					$('#dropDown2').attr('disabled','disabled');
					$('#next').show();
					
				}
			}
		});
		$('#next').live('click',function(){
			showScreen(3);
		});
}

function screen3Events(){
	$('#dropDown3').live('change',function(){
				var val = parseInt($('#dropDown3').val());
				if(val!=correctOptionDropDown3){
					showPrompt(eval("name"+correctOptionDropDown3)+promptArr['p139']);	
				}
				$('#dropDown3').val(correctOptionDropDown3);
				$('#dropDown3').attr('disabled','disabled');
				$('#screen3Line6').show();
			});

	$('#dropDown4').live('change',function(){
				var val = parseInt($('#dropDown4').val());
				if(val!=correctOptionDropDown3){
					showPrompt(eval("name"+correctOptionDropDown3)+promptArr['p141']);	
				}
				$('#dropDown4').val(correctOptionDropDown3);
				$('#dropDown4').attr('disabled','disabled');
				$('#screen3Line7').show();
			});

	$('#dropDown5').live('change',function(){
				dropDown5Counter++;
				var val = parseInt($('#dropDown5').val());
				if(dropDown5Counter==1){
					if(val!=1){
						showPrompt(replaceDynamicText(promptArr['p146'],numberLanguage,''));
					}	
					else{
						$('#dropDown5').val(1);
						$('#dropDown5').attr('disabled','disabled');
						$('#screen3Line8').show();
					}				
				}
				if(dropDown5Counter==2){
					if(val!=1){
						showPrompt(promptArr['p143']);
					}
					$('#dropDown5').val(1);
					$('#dropDown5').attr('disabled','disabled');
					$('#screen3Line8').show();
				}	
			});	
	
	if(q1Num/q1Den>q2Num/q2Den){
		correctOptionDropDown6 = 1;
	}
	else correctOptionDropDown6 = 2;
	
	
	$('#dropDown6').live('change',function(){
		var val = parseInt($('#dropDown6').val());
		if(val!=correctOptionDropDown6){
			showPrompt(promptArr['p148']);
			$('#dropDown6').val(correctOptionDropDown6);
			questionCorrect=0;
			screen4CounterMax=4;
		}
		else {
			showPrompt(promptArr['p122']);
			questionCorrect=1;
		}	
		$('#screen3Next').show();
		$('#dropDown6').attr('disabled','disabled');
	});		
	
	$('#screen3Next').live('click',function(){
		if(questionCorrect==0){
			frac1Num = q1Num;
			frac2Num = q2Num;
			part1 = q1Den;
			part2 = q2Den;
			frac1 = returnFrac(frac1Num,part1);
			frac2 = returnFrac(frac2Num,part2);
			showScreen(2);	
		}
		else{
			showScreen(4);
		}
		
	});
}

function screen4Events(){
	$('#dropDown7').live('change',function(){
		var val = parseInt($('#dropDown7').val());
		if(val!=correctOptionDropDown7){
			showPrompt(promptArr['p148']);
			$('#dropDown7').val(correctOptionDropDown7);
			questionCorrect=0;
			screen4CounterMax=4;
		}
		else
		{
			showPrompt(promptArr['p122']);
			questionCorrect=1;
		}	
		$('#screen4Next').show();
		$('#dropDown7').attr('disabled','disabled');
	});		
			
	$('#screen4Next').live('click',function(){
		if(screen4Counter<=screen4CounterMax){
			screen4Counter++;
			if(questionCorrect==1){
				showScreen(4);
			}
			else{
				frac1Num = q1Num;
				frac2Num = q2Num;
				part1 = q1Den;
				part2 = q2Den;
				frac1 = returnFrac(frac1Num,part1);
				frac2 = returnFrac(frac2Num,part2);
				showScreen(2);	
			}	
		}
		else{
			$('#screen4Next').hide();
			$('#screen4Line1').hide();
			showPrompt(promptArr['p150']);
			levelWiseStatusArr[currentLevel-1]=1;
			setOutputParamter();
			completed=1;
		}
		
	});
}

function setOutputParamter()
{
	levelsAttempted = "";		
	levelWiseStatus = "";		
	levelWiseScore 	= "0";
	levelWiseTimeTaken = "";
	extraParameters = "";
	for(var i=0;i<(levelAttemptedArr.length);i++)
	{
		levelsAttempted += "L"+levelAttemptedArr[i]+"|";
		levelWiseStatus += levelWiseStatusArr[levelAttemptedArr[i]-1]+"|";
		levelWiseScore  += "|";		
		levelWiseTimeTaken  += levelWiseTimeTakenArr[levelAttemptedArr[i]-1]+"|";
		extraParameters     += extraParametersArr[levelAttemptedArr[i]-1]+"|";		
	}
	levelsAttempted = levelsAttempted.substr(0,(levelsAttempted.length-1));
	levelWiseStatus = levelWiseStatus.substr(0,(levelWiseStatus.length-1));
	levelWiseScore  = levelWiseScore.substr(0,(levelWiseScore.length-1));
	levelWiseTimeTaken  = levelWiseTimeTaken.substr(0,(levelWiseTimeTaken.length-1));	
    extraParameters  = extraParameters.substr(0,(extraParameters.length-1));
}