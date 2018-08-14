var startChar='start';
var resetChar='back';
var idleChar='think';
var newChar='new';
var rightChars=new Array("walkinclap","walkindance","walkinyes","walkingreat");
var wrongChars=new Array("walkincry","walkinno","walkinwrong");

var t;

var canvas, stage, cHolder, speech;
var canvasWidth=300, canvasHeight=240;
var mouseTarget;	// the display object currently under the mouse, or being dragged
var dragStarted;	// indicates whether we are currently in a drag operation
var offset;
var update = true;
var move = false;

var buddyPos = {};
var ss, grant;

var buddycode=2;
var characterClickFlag = 0;
var canvasClickFlag = 0;

var filesadded="" //list of files already added
var showMsgFlag = 0;

function buddyinit(){
	// create stage and point it to the canvas:
	cHolder=document.getElementById("ichar");
	//canvas=document.getElementById('buddyCanvas');
	canvas=document.createElement('canvas');
  	canvas.setAttribute("id","buddyCanvas");
  	canvas.setAttribute("width",canvasWidth+"px");
 	canvas.setAttribute("height",canvasHeight+"px");
  	canvas.setAttribute("style", "pointer-events:none;");
  	canvas.setAttribute("style", "display:block;");
	cHolder.appendChild(canvas);	

	speech=document.createElement('div');
	speech.setAttribute("id","speechBubble");
	//speech.setAttribute("style","display:none;position:absolute;top:120px;left:300px;border:2px solid black;border-radius:5px;background-color:#fff;");
	speech.setAttribute("style","display:none;position:absolute;top:112px;left:300px;");
	cHolder.appendChild(speech);
	$("#speechBubble").addClass("triangle-right left");
	$('#ichar').draggable({containment:"document"});
	$('#dragObj').draggable();
	$('#ichar').draggable( 'disable' );		
	canvas.setAttribute("style", "pointer-events:all;");
	ss = new createjs.SpriteSheet({
	"animations":
		{
			"walkin": [0, 6, false],
			"walkback": {
				frames: [6,5,4,3,2,1,0],
				next: "idle0"
			},
			"walkinclap": [0, 6, "clap"],
			"walkincry": [0, 6, "cry"],
			"walkindance": [0, 6, "dance"],
			"walkinyes": [0, 6, "yes"],
			"walkinno": [0, 6, "no"],
			"walkingreat": [0, 6, "great"],
			"walkinwrong": [0, 6, "wrong"],
			"comein": {
				frames: [7,8,9,10,11,12,13,14,15,16,17,18,19,20,20,20],
				next: "walkback"
			},
			"clap": {
				frames: [21,21,22,23,22,23,22,23,22,23],
				next: "walkback",
				frequency: 2
			},
			"cry":  {
				frames: [24,25,26,27,28,28,25,26,27,28,28,25,26,27,28,28,24,24],
				next: "walkback",
				frequency: 2
			},
			"dance":  {
				frames: [54,56,55,54,56,55,54,61,58,60,61,58,60,61],
				next: "walkback",
				frequency: 2
			},
			"yes":  {
				frames: [52,53,52,53,52,53,52,53,52,52,52,52,52],
				next: "walkback",
				frequency: 2
			},
			"no":  {
				frames: [49,50,51,50,51,50,51,50,51,49,49,49,49,49,49],
				next: "walkback",
				frequency: 2
			},
			"great":  {
				frames: [43,44,45,44,45,44,45,44,44],
				next: "walkback",
				frequency: 2				
			},
			"wrong":  {
				frames: [46,47,48,47,46,47,48,47,46,47,48,47,46,47,47,47,47,47,47],
				next: "walkback",
				frequency: 2
			},
			"idle0":  { //just standing
				frames: [29,29,29,29,29,29,29,29,29,29,29,29,29,29],
				next: "idle1",
				frequency: 3
			},
			"idle1":  { //hitting head
				frames: [29,29,29,30,30,31,32,30,30,31,32,30,30,31,32,29,29,29],
				next: "idle1a",
				frequency: Math.round(Math.random()*2)
			},
			"idle1a":  { //hitting head
				frames: [29,29,29,29,29,29,29,29,29,29,29,29,29,29],
				next: "idle2",
				frequency: 3
			},
			"idle2":  { //tapping feet & looking at time
				frames: [29,29,29,33,29,33,29,33,29,33,29,34,35,34,35,34,35,29,33,29,33,29,29,29],
				next: "idle2a",
				frequency: Math.round(Math.random()*4)
			},
			"idle2a":  { //tapping feet & looking at time
				frames: [29,29,29,29,29,29,29,29,29,29,29,29,29,29],
				next: "idle3",
				frequency: 3
			},
			"idle3":  { //yoyo
				frames: [29,29,29,36,37,38,37,36,36,37,38,37,36,36,37,38,37,36,29,29,29],
				next: "idle0",
				frequency: Math.round(Math.random()*3)
			},
			"idle4":  { //thinking
				frames: [29,29,29,39,39,39,39,39,39,40,41,42,41,40,40,41,42,41,40,40,41,42,41,40,39,39,39,39,39,39,40,41,42,41,40,40,41,42,41,40,40,41,42,41,40,39,39,29,29,29],
				next: "idle0",
				frequency: Math.round(Math.random()*2)
			}
		},
	"images": ["assets/buddy.png"],
	"frames":
		{
			"height": 240,
			"width":300,
			"regX": 0,
			"regY": 0,
			"count": 62
		}
	});

	grant = new createjs.BitmapAnimation(ss);
	grant.onAnimationEnd = function(bitmap, animation)
	{
	    //console.log( animation);
		if(animation == "walkback" && showMsgFlag == 1)
		{
			showMsgFlag = 0;
			speech.style.display = "block";
			if (buddyPos.X>canvas.getAttribute("width")) speech.style.left=(buddyPos.X-300)+"px";
    		else {
					$(speech).animate({left:"100px"},700);
				 }
    		speech.style.top=(buddyPos.Y - 115 )+"px";//(buddyPos.Y - 60 
		}			
	};
	/*canvas.onmousedown=function(e){
		e.preventDefault();
		//canvas.setAttribute("style", "pointer-events:all;");
		if (move){
			var mx=(e.pageX)?e.pageX:e.originalEvent.targetTouches[0].pageX;
			var my=(e.pageY)?e.pageY:e.originalEvent.targetTouches[0].pageY;
			var cHolderX=cHolder.offsetLeft;var cHolderY=cHolder.offsetTop;
			var dx=mx-cHolderX;var dy=my-cHolderY;
			document.onmousemove=function(e){
				var mx=(e.pageX)?e.pageX:e.originalEvent.targetTouches[0].pageX;
				var my=(e.pageY)?e.pageY:e.originalEvent.targetTouches[0].pageY;
				
				if (mx>=10 && mx<window.innerWidth-10) cHolder.style.left=(mx-dx)+"px";
				if (my>=10 && my<window.innerHeight-10) cHolder.style.top=(my-dy)+"px";
			};
		}
		else return false;
	};
	canvas.onmouseup=canvas.onmouseout=function(){
		document.onmousemove=null;
		//canvas.setAttribute("style", "pointer-events:none;");
	};
	*/
	//check to see if we are running in a browser with touch support
	//exportRoot = new lib.AnimationCompleteCallBack();
	stage = new createjs.Stage(canvas);
	//bitmap.onAnimationEnd = angleChange;
	//var clip = exportRoot.clip;
   // clip.onClipAnimationComplete = handleComplete;
	// enable touch interactions if supported on the current device:
	createjs.Touch.enable(stage);

	// enabled mouse over / out events
	stage.enableMouseOver(10);
	stage.mouseMoveOutside = true; // keep tracking the mouse even when it leaves the canvas
	
	// load the source image:
	var image = new Image();
	image.src = "assets/buddy.png";
	//image.width = "10px";
	//image.height = "10px";
	//image.style.backgroundSize="4000px 4000px";
	image.onload = handleImageLoad;
	//image.onerror= errorFunc;
	
	initTouch(); 	
}
function stop1() {
	Ticker.removeListener(window);
}


function handleImageLoad(event) {
	//alert("handle");
	var image = event.target;
	var bitmap;
	var container = new createjs.Container();
	stage.addChild(container);
	
	// create and populate the screen with random daisies:
	//for(var i = 0; i < 1; i++){
		bitmap = grant;//new createjs.Bitmap(image);
		grant.gotoAndPlay("comein");
		container.addChild(bitmap);
		bitmap.x = canvasWidth/2;//canvas.width * Math.random()|0;
		bitmap.y = canvasHeight/2;//canvas.height * Math.random()|0;
		bitmap.rotation = 0;//360 * Math.random()|0;
		bitmap.regX = canvasWidth/2;//bitmap.image.width/2|0;
		bitmap.regY = canvasHeight/2;//bitmap.image.height/2|0;
		bitmap.scaleX = bitmap.scaleY = bitmap.scale = 1;//Math.random()*0.4+0.6;
		bitmap.name = "bmp_0";
		
		buddyPos = {
			X:bitmap.x,
			Y:bitmap.y
		};
		//speech.innerHTML="Hello!";
		// wrapper function to provide scope for the event handlers:
		(function(target) {
		//bitmap.onPress = function(evt) {
					// bump the target in front of it's siblings:
					//container.addChild(target);
					/*var offset = {x:target.x-evt.stageX, y:target.y-evt.stageY};
					speech.style.display="block";
*/
					// add a handler to the event object's onMouseMove callback
					// this will be active until the user releases the mouse button:
					/*evt.onMouseMove = function(ev) {
						//target.x = ev.stageX+offset.x;
						//target.y = ev.stageY+offset.y;
						 console.log(ev.stageX+offset.x);
						 //indicate that the stage should be updated on the next tick:
						buddyPos = {
							X:ev.stageX+offset.x,
							Y:ev.stageY+offset.y
						};
						console.log(buddyPos.X);
					*/	
						//alert(move)
						/*if(move)
						{
							console.log("move");
							canvas.setAttribute("style", "pointer-events:all;");
                        	cHolder.style.cursor="pointer";
							if (buddyPos.X>canvas.getAttribute("width")) {console.log("tt");  speech.style.left=(buddyPos.X-300)+"px"; }
						else { speech.style.left=(buddyPos.X-40)+"px"; }
						speech.style.top=(buddyPos.Y - 60 )+"px";	*
						}*/	
						 	
						
						//update = true;
				//	}
		//	}
			
			bitmap.onPress = function() {
				characterClickFlag = 1;
				canvas.setAttribute("style", "pointer-events:all;");
				cHolder.style.cursor="pointer";
				$('#ichar').draggable( 'enable' );
				move=true;	
				update = true;
			}
			
			bitmap.onMouseOver = function() {
				//console.log("over");	
				//alert("over");			
				target.scaleX = target.scaleY = target.scale*1.01;
				cHolder.style.cursor="pointer";
				update = true;
				canvas.setAttribute("style", "pointer-events:all;");
				move=true;
				$('#ichar').draggable( 'enable' );	
	
			}
			bitmap.onMouseOut = function() {
				//console.log("out");
				//alert("out");
				characterClickFlag = 0;
				target.scaleX = target.scaleY = target.scale;
				cHolder.style.cursor="default";
				update = true;
				canvas.setAttribute("style", "pointer-events:none;");
				//speech.style.display="none";
				move=false;
				$('#ichar').draggable( 'disable' );				
			}
		})(bitmap);
	//}

	createjs.Ticker.setFPS(24);
	createjs.Ticker.addListener(stage);		
	//createjs.Ticker.addListener(window);	
}

function tick() {
	// this set makes it so the stage only re-renders when an event handler indicates a change has happened.
	if (update) {
		update = false; // only update once
		stage.update();
	}
}
function updateBuddy(resp, msg){
	
	buddycode=resp;
	if (buddycode==1) grant.gotoAndPlay(rightChars[Math.round((rightChars.length-1)*Math.random())]);
	else if (buddycode==0) grant.gotoAndPlay(wrongChars[Math.round((wrongChars.length-1)*Math.random())]);
	else if (buddycode==2) grant.gotoAndPlay("back");
        if( typeof(msg)!=="undefined" && msg!="")
        {
			showMsgFlag = 1;	
            //setTimeout(function(){
			speech.innerHTML = msg;//+ "dfg dfg dfg dfgdfg dfg dfg dfgdf gdfg dfgdf gdfg dfg dfg dfg dfg dfg dfgdf gdfg dfg df gdg dfg dfg dfgdfg dfg dfg dfgfd dfg dfgdfg dfg dfg dfg dfg ";
            //speech.style.display = "block";
			$('#speechBubble').css("padding-bottom",(13)+"px");
			$("#closeBtn").show();
            if (buddyPos.X>canvas.getAttribute("width")) speech.style.left = (buddyPos.X-300)+"px";
    		else {speech.style.left="160px"; }//(buddyPos.X-40)(buddyPos.X-55)+"px";
    		speech.style.top=(buddyPos.Y - 115 )+"px";//(buddyPos.Y - 60 
            $(speech).show("explode");
			//},2000);
        }
        else
           speech.style.display = "none";
}


function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
    switch(event.type)
    {
        case "touchstart": type = "mousedown"; break;
        case "touchmove":  type="mousemove"; break;        
        case "touchend":   type="mouseup"; break;
        case "touchcancel":   type="mouseup"; break;
        default: return;
    }

             //initMouseEvent(type, canBubble, cancelable, view, clickCount, 
    //           screenX, screenY, clientX, clientY, ctrlKey, 
    //           altKey, shiftKey, metaKey, button, relatedTarget);
	
    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1, 
                              first.screenX, first.screenY, 
                              first.clientX, first.clientY, false, 
                              false, false, false, 0/*left*/, null);

    first.target.dispatchEvent(simulatedEvent);
    //event.preventDefault();
}

function initTouch() 
{
	document.addEventListener("touchstart", touchHandler, true);
    document.addEventListener("touchmove", touchHandler, true);
    document.addEventListener("touchend", touchHandler, true);
    document.addEventListener("touchcancel", touchHandler, true);    
}

function closeBtnFunc(type)
{
	if(type=="hide")
		$("#ichar").hide("slide","",1000,callback());
	else
	{
		$("#showBuddyDiv").hide("slide");	
		$( "#ichar" ).fadeIn();		
	}	
}
// callback function to bring a hidden box back
function callback() {

   	$("#showBuddyDiv").show();
};

 
var onPointerUp = function (evt) {
  	$("#"+evt.target.id).show();
};

var onPointerDown = function (evt) {
  	if($("#"+evt.target.id).css("pointer-events") == "none")
	{
		$("#"+evt.target.id).hide();
		$(document.elementFromPoint(evt.clientX, evt.clientY)).click(); 
		$(document.elementFromPoint(evt.clientX, evt.clientY)).focus();
	}
};


var attachIE9Event = function() {
    var plainCanvas = document.getElementById("buddyCanvas");
    plainCanvas.width = plainCanvas.clientWidth;
    plainCanvas.height = plainCanvas.clientHeight;

    var context = plainCanvas.getContext("2d");

    context.fillStyle = "rgba(50, 50, 50, 1)";
    context.fillRect(0, 0, plainCanvas.width, plainCanvas.height);

	if(navigator.appName.indexOf("Internet Explorer") !=-1)
	{
		plainCanvas.addEventListener("mousedown", onPointerDown, false);
	    plainCanvas.addEventListener("mouseup", onPointerUp, false);
	    plainCanvas.addEventListener("mouseout", onPointerUp, false);	   
	}
};

if (document.addEventListener !== undefined) {
     document.addEventListener("DOMContentLoaded", attachIE9Event, false);
};