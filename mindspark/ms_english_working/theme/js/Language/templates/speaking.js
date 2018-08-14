/*
	Author:- Praneeth Katragadda
    Template:- Speaking
*/

var speakingObject;
var qcode;
var status_flag;
var completed_flag;
var speakingAttemptID;
var questionPart;
var result,json_arr;
var text_arr=new Array(20);
var questionParameters;//=new Map(); 
var param_arr;
var chunks=[];
var contentPercentage=0;
var presentContentNumber=0;

function speaking(view) {
	qcode=view.quesDataArr.qcode;
	questionParameters=view.quesDataArr.queParams;
	var path=view.imagePath;
	var titleImage=view.quesDataArr.quesImage;
	var title=view.quesDataArr.titleText;
    var text=view.quesDataArr.quesText;
    text=text.replace("'","&apos;");	
	var titleInstruction=questionParameters["quesInstruction"];
	var speechbox_count=0;
	var speakingContainer='';
    Helpers.loadJs("/techmCodeCommit/mindsparkProduct/mindspark/ms_english/theme/js/Language/RecorderRTC.js", function() {
        console.log("included Recorder");
    });

    var createMarkup=function(){
		speakingContainer='<div id=mainSpeakingContainer style="text-align:left">';
		title_components();
		Content_Process();
	};

	var title_components=function()
	{
		
		var image_stitch='<div id="headSpeaking" style="margin-top:3%;margin-bottom: 5px; text-align: center;padding-left:20%; padding-right:20%;">';
		if(title!=null && title!=null){
			image_stitch='<div style="text-align:center;"><h1><b>'+title+'</b></h1></div>';
		}
		if(titleImage!=null && titleImage!=""){
			var src=path+titleImage;
			image_stitch=image_stitch+'<img src='+src+' style="height:200px;">';
		}
		//console.log(titleInstruction);
		if(titleInstruction!=null && titleInstruction!=""){
			image_stitch=image_stitch+'<div style="text-align:center;font-size: 1.8em; margin-top:5px;color:white;">'+titleInstruction+'</div>';
		}
		image_stitch=image_stitch+'</div>';
		$(view.container).append(image_stitch);
		$(view.container).append('<audio controls id="audiotag"  style="display:none;" ></audio>');
		text=text.replace(/(Alignment)/g,"Left"); // By default they are aligned to Left
	}
	
	function Content_Process(){
		var i=1,no_speech_flag=false;
		while(i>0){
			var start_tag="{content"+i;
			var end_tag='{/content'+i+'}';
			if(text.match(start_tag)==null){
				break;	
			}
			var div=text.match(start_tag+'(.*?)'+end_tag);
			var div_alignment=div[1].match(/\(([^)]+)\)/)[1];
            div[1]=div[1].replace(/\(([^)]+)\)/,"");
            div[1]=div[1].replace('}',"");
			div[1]=div[1].replace(/^(<br( \/)?>)*|(<br( \/)?>)*$/gm,'');
            div[1]=div[1].replace(/^(<br>)*|(<br>)*$/gm,'');
            //console.log(div[1]);        
            div[1]=Image_replace(div[1]);
			div[1]=Instruction_replace(div[1]);
			div[1]=Text_replace(i,div[1],div_alignment.toLowerCase());
			var add_divTag='';
			if(div[1].match(/speechbox/)!=null){
				if(div[1].match('{speechbox:'+'(.*?)'+'}')!=null){
					if(div[1].match('{speechbox:'+'(.*?)'+'}')[1]!=null){
						var textGoToDb=div[1].match('{speechbox:'+'(.*?)'+'}')[1].trim();
						text_arr.splice(i,1,textGoToDb);
					}
				}
				var speechbox='<div id=speaker'+i+' style="margin: 5px;display: flex;justify-content: left;align-items: center;"><div id=recordingBox'+i+'  style="cursor:pointer;width:60px;height:60px;clear: both;float: left;border-radius: 35px;background-color: white;display: flex;align-items: center;justify-content: center;"  onclick=start('+i+') ><img id=microphone'+i+' height="40px" style="vertical-align:middle;cursor: pointer;" src='+path+'microphone.png  alt="Mic Image...." /><img id=stop'+i+' height="40px" style="cursor: pointer;vertical-align:middle;display:none;width: 35px;height: 35px;" src='+path+'stop.jpg alt="Mic Image...." /></div> &nbsp; &nbsp; <span id=micInstruction'+i+' style="font-weight:bold;color:white;">(Start recording)</span></div><div id=finalAnswer'+i+' style="width: 93%;height: 26px;display:none;align-items:center;justify-content:left;color:white;font-size:1.2em;"></div>';

				div[1]=div[1].replace(/{speechbox:(.*?)}/,speechbox);
				add_missleneousTag='<image id=loadingSpinner'+i+' src='+path+'loadingSpinner.gif width="50" height="50" style="margin-left: 25%;display:none;" >';
			}
			else{
				add_missleneousTag='<div><button id=moveAhead'+i+' onclick="moveAhead('+i+')" style="background-color: #0572a8;;margin:5px;color:white;border-radius:25px;"> Move Ahead </button></div>';
			}

			if(div_alignment.toLowerCase()=='center'){
				add_divTag='<div id=div'+i+'  display:inline style="display:none; margin-left:17%; margin-top:5px; margin-bottom:5px;margin-right:5px;width:80%" >'+div[1];
			}

			else{
                if(div_alignment.toLowerCase()=="left"){
                    var divWidth="60%";
                }
                else{
                    var divWidth="45%"; 
                }
				add_divTag='<div id=div'+i+'  display:inline style="display:none;clear:both;margin:5px;float:'+div_alignment.toLowerCase()+'; width:'+divWidth+' ;">'+div[1];
                 // console.log(add_divTag);
			}
				
			add_divTag=add_divTag+add_missleneousTag+'</div>';
			i++;
			speakingContainer=speakingContainer+add_divTag;
			//console.log(add_divTag);
		}
		speakingContainer=speakingContainer+"</div>";
		$(view.container).append(speakingContainer);
		document.getElementById("div1").style.display="";
		formatHTMLView();
		intiate();
	}

    function Image_replace(str){
		var regex= /{image/g;
		var result=[];
	    var match;
	    while (match = regex.exec(str)){
	   		result.push(match.index);
		}
		while(0<1){
			if(result.length==0){
				break;
			}
			var index_start=result.pop();
			var index_end=-1;var dimensions='';var dimension_end='';
			for(var j=index_start;;j++){
				if(str.charAt(j)=='}'){
					index_end=j;	
				}
				if(str.charAt(j)==')'){
					dimension_end=j;
					dimensions=str.substring(index_end+2,j).split(',');
					break;	
				}
			};
			var tmp_image=str.substring(index_start+1,index_end);
			var original=questionParameters[tmp_image];
			var id=original;
			var height=dimensions[0];
			var width=dimensions[1];
			if(height.toLowerCase()=='height' || width.toLowerCase()=="width"){
				id=id+' height="80px" width="80px" ';
			}
			else{
				id=id+' height='+height+'px width='+width+'px';
			}
			original=path+original;
			str.replace("(","");str.replace(")","");
            if(height>80 && width>80){
                str=str.substring(0,index_start)+'<div style="display:flex;margin:5px;width:75%;border-radius: 5px;justify-content:center;align-items:center;"><img id='+id+' src='+original+' alt="Image...."></div>'+str.substring(dimension_end+1);
            }
            else{
                str=str.substring(0,index_start)+'<div style="display: inline-block;margin:5px;"><img id='+id+' src='+original+' alt="Image...."></div>'+str.substring(dimension_end+1);    
            }
			
		}
		return str;
	}

	function Instruction_replace(str){
		var front='{instruction:';
		var back='}';
		var instruction_content=str.match(front+'(.*?)'+back);
		if(instruction_content==null){
			return str;
		}
		var insert='<div id="instruction_div" style="display:inline-block;margin:5px;font-size: 1.2em; color:white;"><strong>'+instruction_content[1]+'</strong></div>';	
		str=str.replace(/{instruction:.*?}/,insert);
		return str;
	}

	function Text_replace(i,str,alignment){
        var front='{text:';
        var back='}';
        var text_content=str.match(front+'(.*?)'+back);
        if(text_content==null){
            return str;
        }
        text_content[1]=Algorithm_ReplaceTextWithSpan(text_content[1],i);
        if(alignment=="left"){
            var textBoxWidth="75%";
        }
        else{
            var textBoxWidth="93%";
        }
        var insert='<div id="textBox" style=" cursor: pointer;display:flex;border-radius: 10px;margin:5px;padding:5px;color: black;font-size:1.2em;background-color: white;vertical-align:middle;width:'+textBoxWidth+';min-height:55px;justify-content: center;align-items: center;"><div style="display:inline-block;float:left;margin-right:3%;border-radius: 52px;background-color:#0572a8;min-width: 40px;min-height: 40px;"><image src='+path+'play.png onclick=play_sentence('+i+') height=30  style="display: inline-block;margin-top: 6px;display: inline-block;width: 25px;height: 23px;margin-left: 7px;" > </image></div>&nbsp;<div style="display:inline-block;width:80%;float:left;" >'+text_content[1]+'</div></div>';
        str=str.replace(/{text:.*?}/,insert);
        return str;
    }

    function makeTTS(p,divCounter){
        p=p.replace("\n",'');
        var word=p.trim();
        var attach=word.replace(' ','');
        if(word==''){
            return "";
        }
        var before=word;
        attach=word.replace(',','');
        attach=attach.replace('.','');
        attach=attach.replace('!','');
        attach=attach.replace('?','');
        attach=attach.replace(/"/g,'');
        //before=attach;
        attach=attach.replace(/&apos;/g,"");
        attach=attach.replace(/&rsquo;/g,"");
        attach=attach.replace(/&quot;/g,"");
        attach=attach.replace(/&nbsp;/g,"");
        attach=attach.replace(/'/g,"");
        attach=attach.replace(/"/g,"");
        var attach_id=attach+"~"+divCounter;
        attach="'"+attach+"~"+divCounter+"'";
        var attach_final='<span onmouseover=javascript:tempColor=document.getElementById("'+attach_id+'").style.color;document.getElementById("'+attach_id+'").style.color="blue"; onmouseout=javascript:document.getElementById("'+attach_id+'").style.color=tempColor; id='+attach_id+' onclick=play_sound('+attach+')> '+before+' </span>';
        return attach_final;
    }

    function Algorithm_ReplaceTextWithSpan(p,divCounter){
        // console.log("Printing :  "+p);
        var flag_tag=false;
        var word='';
        var finalText='';
        for(i=0;i<p.length;i++){

            if(p[i]=='<'){
                if(word.trim()!=''){
                    var tmp=TTSreplacesentence(word,divCounter);
                    finalText=finalText+tmp;
                    word='';
                }
                flag_tag=true;  
                finalText=finalText+p[i];
                continue;
            }

            else if(p[i]=='>' && flag_tag){
                flag_tag=false;
                finalText=finalText+p[i];
                continue;
            }

            else if(p[i]!='<' && p[i]!='>' && !flag_tag){
                word=word+p[i];
                continue;
            }

            else{
                finalText=finalText+p[i];
                continue;
            }

        }
        if(word.trim()!=''){    
            // console.log("last stage:"+word);
            tmp=TTSreplacesentence(word,divCounter);
            finalText=finalText+tmp;
        }
        // console.log("Final text: "+finalText)
        return finalText;   
    }


    function TTSreplacesentence(word,divCounter){
        //word=word.replace(/,/g," ");
        var splitSentence=word.trim().split(" ");
        var replace_sentence='';
        for(j=0;j<splitSentence.length;j++){
            if(splitSentence[j].trim()==""){
                continue;
            }
            replace_sentence+=makeTTS(splitSentence[j],divCounter);
        }
        return replace_sentence.trim();
    } 

	 var setParameters = function() {
        view.model.currentQuestion.correct = 1;
        view.model.currentQuestion.userResponse = "speaking Response Done";
        view.model.currentQuestion.extraParam = '';
        view.model.currentQuestion.score = 0;
        view.model.currentQuestion.completed = 1;
    };	
	
	var saveResponse = function() {
		setParameters();
		view.onAttempt();
		return true;
	};
	
	var createQuestion = function() {
        createMarkup();
        view.onSubmit =saveResponse;
        restrictQuestions(1);
    };

    return {
        show : createQuestion,
    };
}

function showSpeakingQuestion(view) {
    if (speakingObject === undefined) {
        speakingObject = speaking(view);
    }
    $( "img" )
      .error(function() {
        imgNotLoading();
    });

    speakingObject.show();
}

// Annyang integration started
        var orignalSpeakingText ='';
        var speakingResponseccuracy=-1;
        var speechDetected='';
        var recognition;
       
        function speechRecognitionStart(index){
            try{
                recognition = new webkitSpeechRecognition();
                document.getElementById("annyangDisp"+index).innerHTML='';
                speechDetected ='';
                recognition.continuous = true;
                recognition.interimResults = false;
                recognition.lang = "en-IN";
                recognition.start();
                recognition.onstart = function () {
                    offlineFlag=false;
                };
                recognition.onresult = function (event) {
                    for (var i = event.resultIndex; i < event.results.length; ++i) {
                        if (event.results[i].isFinal) {
                            speechDetected=speechDetected+event.results[i][0].transcript;
                        }
                    }
                };
                recognition.onspeechend = function() {
                  console.log('Speech has stopped being detected');
                }
                recognition.onerror = function (event) {
                    offlineFlag = true;
                };       
            }
            catch(err){
                console.log("webkitSpeechRecognition is not working in this system");
                offlineFlag=true;
            }
            
        }

    
/* This below function will give advice to student according to the accuracy
    Params: i ------> content id 
            studentAccuracy--> Accuracy of the sentence spoken by student  
*/
    function checkRecord(i,totalWords,missingWords,textRecognized) {
        var advice,messageTextColor,messageBackGroundColor;
        studentAccuracy=((totalWords-missingWords)/totalWords)*100;
        studentAccuracy=Number(studentAccuracy).toFixed(2);
        if(studentAccuracy == 0.0 && textRecognized==''){
            advice ="Sorry! We didn't hear anything.";
            messageTextColor="#ff5454";
            messageBackGroundColor="#FFE5E5";
            document.getElementById("speakingScoreChart"+i).style.display='none';
            document.getElementById("feedbackMessage"+i).style.width="98%";
        }
        else if (studentAccuracy < 45) {
            advice = "You seem to have said something different from the text. Try recording again!";
            messageTextColor="#ff5454";
            messageBackGroundColor="#FFE5E5";
        } else {
            if (studentAccuracy < 75) {
            advice = "Good going! But maybe you can do better.";
            messageTextColor="#ffab00";
            messageBackGroundColor="#FFEABF";
        } else {
            advice = "Well done! You seem to have spoken really well.";
            messageTextColor="#00c853";
            messageBackGroundColor="#CCF4DD";
                }
        }
        var speakingScore=Number(studentAccuracy).toFixed(0);
        var message=(totalWords-missingWords)+"&nbsp;correct <br/><span style='font-size: 16px;'> out of "+totalWords+" words</span>";
        //var attachMessage="<span id=scoreBoard"+i+" >"+message+"</span>";
        document.getElementById("scoreBoard"+i).innerHTML=message;
        document.getElementById("speakingScoreChart"+i).style.backgroundColor=messageBackGroundColor;
        document.getElementById("scoreBoard"+i).style.color=messageTextColor;
        document.getElementById("annyangDisp"+i).innerHTML = advice;
        document.getElementById("annyangDisp"+i).style.display='';
    }

    var isEdge = navigator.userAgent.indexOf('Edge') !== -1 && (!!navigator.msSaveOrOpenBlob || !!navigator.msSaveBlob);
    var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    var recorder; // globally accessible
    var microphone;
    var present_div=-1;
    var present_blob='';
    var offlineFlag='';

    function intiate(){
        if (!microphone) {
            captureMicrophone(function(mic) {
                microphone = mic;
                console.log(microphone);
            });
        }
    }

    function captureMicrophone(callback) {
        if(microphone) {
            callback(microphone);
            return;
        }

        if(typeof navigator.mediaDevices === 'undefined' || !navigator.mediaDevices.getUserMedia) {
            alert('This browser does not supports WebRTC getUserMedia API.');
            // Skip the speaking question should be called at this point 
        }

        navigator.mediaDevices.getUserMedia({
            audio: isEdge ? true : {
                echoCancellation: false
            }
        }).then(function(mic) {
            callback(mic);
        }).catch(function(error) {
            alert('Unable to capture your microphone. Please check console logs. \n Error: '+error);
            console.log(error);
        });
    }

    function start(i) {
        if(microphone==undefined){
            intiate();
            alert('Microphone is not yet ready ! \n press "ok" and try again.');
            return;
        }
        if(document.getElementById("feedback")!=null){
            document.getElementById("feedback").remove();    
        }
        presentContentNumber=i;
        var feedback='<div id=feedback style="display:none;width: 764px;height: 163px;border-radius: 10px;background-color: #ffffff;box-shadow: 0 0 10px 0 rgba(0, 72, 107, 0.5); margin:5px; position: relative;bottom: 0px;left:10%;"><div id=uls'+presentContentNumber+' style="margin:5px;"></div><div id=speakingScoreChart'+presentContentNumber+' style="display: flex; height:84px; width:154px;float:left;border-radius: 20px;justify-content: center;align-items:center;text-align: center;margin-left: 11px;"><span id=scoreBoard'+presentContentNumber+' style="font-size: 24px;font-weight: 500;font-style: normal;font-stretch: normal;line-height: normal;letter-spacing: normal;"></span></div><div id=feedbackMessage'+presentContentNumber+' style="width: 584px;height: 82px;border-radius: 10px;background-color: #d8ecf6;float: left;display: flex;justify-content: center;align-items: center;margin-left:1%;"><div id=annyangDisp'+presentContentNumber+' tabindex="0" style="width: 242px;height: 52px;font-size: 16px;font-weight: normal;font-style: normal;font-stretch: normal;line-height: 1.44;letter-spacing: normal;text-align: left;color: #1a1a1a;display:flex;align-items:center;"></div><button id=tryAgain'+presentContentNumber+' style="width: 137px;height: 38px;border-radius: 20px;background-color: #ffffff;border: solid 1px #0572a8;float:left;color: #0572a8;" onclick="tryAgainFunc('+presentContentNumber+')">Try Again</button><button id=submit'+presentContentNumber+' style="display: flex;width: 147px;height: 38px;border-radius: 20px;background-color: #3191ce;float:left;" onclick="submit('+presentContentNumber+')">Move Ahead</button></div>';
        document.getElementById("mainSpeakingContainer").innerHTML+=feedback;
        var elem = document.getElementsByClassName('row the_classroom none appContainers moduleContainer');
        elem.scrollTop = elem.scrollHeight;       
        speechRecognitionStart(i);
        document.getElementById("microphone"+i).style.display='none';
        document.getElementById("micInstruction"+i).innerHTML="(Stop recording)";
        document.getElementById("stop"+i).style.display='';
        document.getElementById("recordingBox"+i).setAttribute("onclick","stop('"+i+"')");
        document.getElementById("uls"+i).style.display='none';
        replaceAudio('',i);
        audio.muted = true;
        setSrcObject(microphone, audio);
        var options = {
            type: 'audio',
            numberOfAudioChannels: isEdge ? 1 : 2,
            checkForInactiveTracks: true,
            bufferSize: 16384
        };

        if(navigator.platform && navigator.platform.toString().toLowerCase().indexOf('win') === -1) {
            options.sampleRate = 48000; // or 44100 or remove this line for default
        }

        if(recorder) {
            recorder.destroy();
            recorder = null;
        }

        recorder = RecordRTC(microphone, options);

        recorder.startRecording();
    }

    function stop(i) {
        var speechResponseTimeOut=0;
        document.getElementById("recordingBox"+i).setAttribute("onclick","start('"+i+"')");
        document.getElementById("loadingSpinner"+i).style.display='';
        present_div=i;
        this.disabled = true;
        recorder.stopRecording(stopRecordingCallback);
        if(document.getElementById("speaker"+i)!=null){
            document.getElementById("speaker"+i).style.display="none";
        }
        if(!offlineFlag){
            // online condition
            recognition.stop();
            speechResponseTimeOut=2000;    
        }
        // document.getElementById(st).style.display='none';
        //console.log(speechResponseTimeOut);
        setTimeout(function() {
            document.getElementById("loadingSpinner"+i).style.display='none';
            document.getElementById("uls"+i).style.display='';
            document.getElementById("microphone"+i).style.display='';
            document.getElementById("micInstruction"+i).innerHTML="(Start recording)";
            document.getElementById("stop"+i).style.display='none';
            document.getElementById("submit"+i).style.display='';
            document.getElementById("tryAgain"+i).style.display='';
            // Evaluating students response
            var idealSentence=text_arr[i];
            //console.log(idealSentence);
            var textRecognized=speechDetected;
            if(!offlineFlag){
                speechDetected='';
                highLightMissingWords(textRecognized,idealSentence,i);   
                if(document.getElementById("feedback")!=null){
                    document.getElementById("feedback").style.display="inline-block";
                }
                if(contentPercentage==100){
                    submit(i); // if 100% automatically move student to next content
                }
            }
            else{
                document.getElementById("annyangDisp"+i).innerHTML = "Keep practising until you and the speaker sound the same!";
                document.getElementById("annyangDisp"+i).style.display='';
                document.getElementById("feedback").style.display="inline-block";
                document.getElementById("speakingScoreChart"+i).style.display="none";
                document.getElementById("feedbackMessage"+i).style.width="98%";
                document.getElementById("annyangDisp"+i).style.width="352px";

            }
        }, speechResponseTimeOut);
    };
    
    function highLightMissingWords(textRecognized,idealSentence,divCounter){
        console.log("text Recognized: "+textRecognized);
        console.log("ideal text: "+idealSentence);
        textRecognized=textRecognized.toLowerCase();
        var idealSentenceCheck=idealSentence;
        var splitIdealSentence=idealSentenceCheck.trim().split(" ");
        var temp='',totalWords=0,missingWords=0;
        var textRecognizedArr=textRecognized.split(" ");
        for(var index=0;index<splitIdealSentence.length;index++){
            temp=splitIdealSentence[index].trim();
            temp=temp.replace("'",'');
            temp=temp.replace(",",' ');
            temp=temp.replace(".",'');
            temp=temp.replace('"','');
            totalWords++;
            if(textRecognizedArr.indexOf(temp.toLowerCase())==-1 || textRecognized=="" && temp!=""){
                // Missing word 
                temp=temp+"~"+divCounter;
                missingWords++;
                if(document.getElementById(temp)!=null){
                    document.getElementById(temp).style.color="red";    
                }
                //console.log(temp);
            }
        }
        // console.log(totalWords+" "+missingWords);
        contentPercentage=((totalWords-missingWords)/totalWords)*100;
        checkRecord(divCounter,totalWords,missingWords,textRecognized);        
    }

    function removeHighlighting(divCounter){
        var tmpSentence=text_arr[divCounter]; // text_arr contains the speechbox content
        var splitIdealSentence=tmpSentence.trim().split(" ");var temp='';
        for(var index=0;index<splitIdealSentence.length;index++){
            temp=splitIdealSentence[index].trim();
            temp=temp.replace("'",'');
            temp=temp.replace(",",' ');
            temp=temp.replace(".",'');
            temp=temp.replace('"','');
            temp=temp+"~"+divCounter;
            if(document.getElementById(temp)!=null){
                document.getElementById(temp).style.color="black";    
            }
        }
    }

    function replaceAudio(src,i) {
        var newAudio = document.createElement('audio');
        newAudio.style.width="100%";
        newAudio.controls = true;
        if(src) {
            newAudio.src = src;
        }
        var parentNode =document.getElementById("uls"+i);
        parentNode.innerHTML = '';
        parentNode.appendChild(newAudio);
        audio = newAudio;
    }

    function stopRecordingCallback(i) {
        replaceAudio(URL.createObjectURL(recorder.getBlob()),present_div);
        present_blob=recorder.getBlob();
        setTimeout(function() {
            if(!audio.paused) return;

            setTimeout(function() {
                if(!audio.paused) return;
                //audio.play();
            }, 1000);
            
        }, 300);
    }

    function submit(i){
        //document.getElementById("submit"+i).remove();
        if(offlineFlag){
            status="You seem to have spoken well";
            document.getElementById("annyangDisp"+i).innerHTML=status;
            document.getElementById("annyangDisp"+i).style.display='';   
        }
        var tempVar=document.getElementById("annyangDisp"+i).innerHTML;
        document.getElementById("finalAnswer"+i).innerHTML=tempVar;
        document.getElementById("finalAnswer"+i).style.display="flex";
        document.getElementById("feedback").remove();
        moveAhead(i);
        removeHighlighting(i);
    }

    function saveAudioResponses(i){
    	var text_tag="NA";
    	if(text_arr[i]!=undefined){
    		text_tag=text_arr[i];	
    	}
        if(document.getElementById("uls"+i)!=null  && text_tag!=null){
            //console.log(document.getElementById("uls"+i)+" "+i);
            blob = present_blob;//new Blob(chunks, {type:'audio/ogg' });//present_blob;
            var url = URL.createObjectURL(blob);
            var fd = new FormData();
            fd.append('fname', 'test.ogg');
            fd.append('data', blob);
            fd.append('qcode',qcode);
            fd.append('content',text_tag);
            fd.append('questionPart',i);
            fd.append('speakingPercentage',contentPercentage);
            fd.append('speakingAttemptID',speakingAttemptID);
            $.ajax({
                url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/speakingSaveResponse',
                type : 'POST',
                data: fd,  //{studentResponse:blob},
                processData: false,
                contentType: false,  
                cache: false,      
                async: false,       
                success: function(data) 
                {               
                    console.log("Success check Database");
                }
            });
            document.getElementById("uls"+i).innerHTML='';
            contentPercentage=null;    
        }
    }

    function updateSpeakingQuestionPart(i){
    	var fd = new FormData();
        fd.append('questionPart',i);
        fd.append('speakingAttemptID',speakingAttemptID);
        $.ajax({
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/updateSpeakingQuestionPart',
            type : 'POST',
            data: fd,  //{studentResponse:blob},
            processData: false,
            contentType: false,  
            cache: false,      
            async: false,       
            success: function(data) 
            {               
                console.log("Success check Database");
            }
        });
    }

    function isSpeakingCompleted(){
    	var fd = new FormData();
        fd.append('qcode',qcode);
        $.ajax({
            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/isSpeakingCompleted',
            type : 'POST',
            data: fd,  //{studentResponse:blob},
            processData: false,
            contentType: false,  
            cache: false,      
            async: false,       
            success: function(data) 
            {                         
                result=JSON.parse(data);
                //console.log(result.result_data);
                if(result.result_data==null || result.result_data=="null"){
                	completed_flag=-1;	
                }
                else{
                	json_arr=JSON.parse(result.result_data);
	                completed_flag=json_arr['completed'];
	                speakingAttemptID=json_arr['speakingAttemptID'];
	                questionPart=json_arr['questionPart'];
                }
                
            }
        });
    }

    function tryAgainFunc(i){
        if(document.getElementById("feedback")!=null){
            document.getElementById("feedback").remove();    
        }
        if(document.getElementById("speaker"+i)!=null){
            document.getElementById("speaker"+i).style.display="flex";
        }
        removeHighlighting(i);
        start(i);
    }

	function moveAhead(i){

        if(document.getElementById("speakingScoreChart"+i)!=null){
            if(document.getElementById("div"+i).style.float=="left"){
                document.getElementById("speakingScoreChart"+i).style.marginRight="28%";
            }
        }

		if(document.getElementById("moveAhead"+i)!=null){
		    document.getElementById("moveAhead"+i).remove();    
		}
        
        if(document.getElementById("tryAgain"+i)!=null){
            document.getElementById("tryAgain"+i).remove();    
        }

        if(document.getElementById("uls"+i)!=null){
            document.getElementById("uls"+i).style.display='none';    
        }
		
        saveAudioResponses(i);
		updateSpeakingQuestionPart(i);
		i++;
		
        if(document.getElementById("div"+i)!=null){
		    document.getElementById("div"+i).style.display='';
            window.scrollBy(0, 100);    
		}
		if(document.getElementById("div"+i)==null){
			setSpeakingQuestionCompletedFlag();
            //$('#questionSubmitButton').show();
            document.getElementById("passageNext").style.display="inline-block";
		}
	}

	function formatHTMLView(){
		isSpeakingCompleted();
		if(completed_flag==-1 || completed_flag==1){
			// display entire question and also enter values in speakingAttempts
			// completed_flag==1  => question arriving twice
			// completed_flag==-1 => question arriving first time
			var fd = new FormData();
	        fd.append('qcode',qcode);
	        fd.append('questionPart',0);
	        fd.append('totalTimeTaken',0);
	        fd.append('completed',0);
	        $.ajax({
	            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/saveSpeakingAttempt',
	            type : 'POST',
	            data: fd,
	            processData: false,
	            contentType: false,  
	            cache: false,      
	            async: false,       
	            success: function(data) 
	            {    
	            	result=JSON.parse(data);
	                //console.log(result.result_data);
	                if(result.result_data==null || result.result_data=="null" ){
	                	completed_flag=-1;	
	                }
	                else{
	                	json_arr=JSON.parse(result.result_data);
		                completed_flag=json_arr['completed'];
		                speakingAttemptID=json_arr['speakingAttemptID'];
		                questionPart=json_arr['questionPart'];
	                }
	            }
	        });
		}
		else if(completed_flag==0){
			// This states that question had an entry in speakingAttempts continuation 
			//console.log(questionPart);
			//console.log(speakingAttemptID);
			//console.log(completed_flag);
			var pointer=1;
			while(pointer <= questionPart){
				if(document.getElementById("uls"+pointer)!=null){
					document.getElementById("uls"+pointer).style.display='none';
				}
				if(document.getElementById("submit"+pointer)!=null){
					document.getElementById("submit"+pointer).style.display='none';
				}
				if(document.getElementById("moveAhead"+pointer)!=null){
					document.getElementById("moveAhead"+pointer).style.display='none';
				}
				// if(document.getElementById("speechbox"+pointer)!=null){
				// 	document.getElementById("speechbox"+pointer).style.display='none';
				// }
				
				if(document.getElementById("annyangDisp"+pointer)!=null){
					document.getElementById("annyangDisp"+pointer).innerHTML="You seem to have spoken well";
					document.getElementById("annyangDisp"+pointer).style.display='';
				}
        		pointer++;
        		if(document.getElementById("div"+pointer)!=null){
					document.getElementById("div"+pointer).style.display='';
				}
				if(document.getElementById("div"+pointer)==null){
					$('#questionSubmitButton').show();
				}
			}
		}
		else{
			// if nothings is the case then there is some error
			console.log("error in speaking ");
		}

	}

	

	function setSpeakingQuestionCompletedFlag(){
		var fd = new FormData();
	    fd.append('speakingAttemptID',speakingAttemptID);
		$.ajax({
	            url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/setSpeakingQuestionCompletedFlag',
	            type : 'POST',
	            data: fd,
	            processData: false,
	            contentType: false,  
	            cache: false,      
	            async: false,       
	            success: function(data) 
	            {    
	            	console.log("set completed falg to 1 completed");
	            }
	        });
	}
		
	function play_sound(param){
        if(param==''){
            return;
        }
        var play_text=document.getElementById(param).innerHTML;
        play_text=play_text.replace(",","");
        play_text=play_text.replace(".","");
        play_text=play_text.replace("!","");
        play_text=play_text.replace("&nbsp","");
        play_text=play_text.replace(/"/g,'');
        play_text=play_text.replace(/&apos;/g,"");
        play_text=play_text.replace(/&rsquo;/g,"");
        play_text=play_text.replace(/&quot;/g,"");
        play_text=play_text.replace(/&nbsp;/g,"");
        play_text=play_text.replace(/'/g,"");
        play_text=play_text.replace(/"/g,"");
        // console.log(play_text);
        var path="https://mindspark-eng.s3.ap-southeast-1.amazonaws.com/templates_qtype/SpeakingTTS/"+play_text.trim().toLowerCase()+".mp3";
        var audio = document.getElementById('audiotag');
        audio.src = path;
        audio.load();
        audio.play();
    }

    function play_sentence(param){
        param=qcode+"_"+param;
        var path='https://mindspark-eng.s3.ap-southeast-1.amazonaws.com/templates_qtype/SpeakingTTS/'+param+'.mp3';
        var audio = document.getElementById('audiotag');
        audio.src = path;
        audio.load();
        audio.play();
    }

