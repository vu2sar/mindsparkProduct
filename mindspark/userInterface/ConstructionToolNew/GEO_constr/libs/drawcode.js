var out_of_document = false, compassFlipEvent = false, rulerTapped = false, lineDrawn = false;
var rulerTouchPosition = {};
var explanationMode = false;
var drawingPadLayout = {
	width: 640,
	height: 500,
};
var page, content;
$(function(){  if(window.Touch) {  touch_detect.auto_detected();  } else {    document.ontouchstart = touch_detect.surface;  }}); // End loaded jQuery
var touch_detect = {  
	auto_detected: function(event){ activateTouchArea();  },  /* add everything you want to do onLoad here (eg. activating hover controls) */
    surface: function(event){ activateTouchArea();  } /* add everything you want to do ontouchstart here (eg. drag & drop) - you can fire this in both places */     
}; // touch_detect
var touchedCircle=null;
function activateTouchArea(){  /* make sure our screen doesn't scroll when we move the "touchable area" */  
	 var element = document.getElementById('canvasBackground');  
	 element.addEventListener("touchstart", touchStart, false);
	 element.addEventListener("touchend", touchEnd, false);
	 //$('#canvasContainer').delegate('#panDiv',"touchstart", touchStart);
	 //element.addEventListener("touchstart", touchStart, false);
}
function touchStart(event) {  /* modularize preventing the default behavior so we can use it again */  
	event.preventDefault();
	if(event.target.id=="ruler" || event.target.id=="rulerHandle") {rulerTouchPosition.top=(event.touches[0].clientY+5)+'px';rulerTouchPosition.left=(event.touches[0].clientX-5)+'px';}
	if(event.target.tagName.toLowerCase()=="circle") {touchedCircle=event.target.id;}
}
function touchEnd(event){
	event.preventDefault();
	if(event.target.id=="ruler" || event.target.id=="rulerHandle") {
		var e = new jQuery.Event("click");
		e.clientX = rulerTouchPosition.left;
		e.clientY = rulerTouchPosition.top;
		if(!rulerMoved) {
			$(event.target).trigger(e, ['tapped']);
		}
	}
	if(event.target.id=="compassbob") {compassFlipEvent = true;geometry.flipCompass();} //workaround added touch event not working properly on ipad
	if(event.target.tagName.toLowerCase()=="circle") {if(lineDrawn) lineDrawn=false; else if (touchedCircle==event.target.id) sketchHelper.editLabel(event.target);}
	touchedCircle==null;
}
function outOfDocument(e) {
	e = e ? e : window.event;
	var from = e.relatedTarget || e.toElement;
	if (!from || from.nodeName == "HTML") {
		out_of_document = true;
		$('#compass').trigger('mouseup');
	}
}
function toggleFullscreen(event) {
	if(
		page.document.fullscreenElement ||
		page.document.webkitFullscreenElement ||
		page.document.mozFullScreenElement ||
		page.document.msFullscreenElement
	) {
		if(page.document.exitFullscreen) {
			page.document.exitFullscreen();
		} else if(page.document.webkitExitFullscreen) {
			page.document.webkitExitFullscreen();
		} else if(page.document.mozCancelFullScreen) {
			page.document.mozCancelFullScreen();
		} else if(page.document.msExitFullscreen) {
			page.document.msExitFullscreen();
		}
	} else {
		if(content.requestFullscreen) {
			content.requestFullscreen();
		} else if(content.webkitRequestFullscreen) {
			content.webkitRequestFullscreen(/*Element.ALLOW_KEYBOARD_INPUT*/);
		} else if(content.mozRequestFullScreen) {
			content.mozRequestFullScreen();
		} else if(content.msRequestFullscreen) {
			content.msRequestFullscreen();
		}
	}
}
function adjustDrawingPad() {
	if(
		page.document.fullscreenElement ||
		page.document.webkitFullscreenElement ||
		page.document.mozFullScreenElement ||
		page.document.msFullscreenElement
	) {
		var scale = Math.min(screen.width/drawingPadLayout.width, screen.height/drawingPadLayout.height);
		page.$('iframe').css('transform', 'scale('+scale+')');
		$('#question,#restore').show();
		$('.fullScreen').css('background-image', 'url("../assets/exitFullScreen.png")').attr('title', 'Exit full screen');
	} else {
		$('#question,#restore').hide();
		page.$('iframe').css('transform', '');
		$('.fullScreen').css('background-image', 'url("../assets/enterFullScreen.png")').attr('title', 'Full screen');
	}
}
document.addEventListener('mouseout', outOfDocument, false);
var animTimeout=null;
var timer=null;
var turn = 0;
var speedLevel=3;
var animSpeed=speedLevel*2/3;
var encodedTutorial;
var showHelp;
var staticRuler = false;
var inkColor = '#000';
var switchMode = {
	learn: function() {
		$('#buttons,#instructions,#toolHelp,#tooltip').css('visibility', 'hidden');
		$('#tutorial')[0].src = 'index.html?asda=1&prString='+encodedTutorial+'&mode=tutorial';
		$('#tutorial').animate({
			left: '0',
		}, 1000);
		$('#modeSwitcher')[0].innerHTML = 'draw';
	},
	draw: function() {
		$('#tutorial')[0].src = '';
		$('#tutorial').animate({
			left: drawingPadLayout.width+'px',
		}, 1000, function() {
			$('#buttons,#instructions,#toolHelp,#tooltip').css('visibility', '');
		});
		$('#modeSwitcher')[0].innerHTML = 'learn';
	},
};
var drawCode = function () {
	
	var _drawcode = {};
	var SVGDoc, SVG;
	var viewport;
	var mClip=[];
	var PointsArray=[];
	var LinesArray=[];
	var ArcsArray=[];
	var reps=0;
	var pointNum=0;var lineNum=0;var arcNum=0;
	var oNum=0;
	
	var animCompass='<div id="d_comp" style="position: absolute; top: 0px; left: 0px; z-index:150;"><div id="d_compg" style="position:relative;bottom:0px;left:0px;overflow: visible;"><img id="d_compl" style="position:absolute;bottom:0px;right:0px;" src="../assets/ptH.png"><img id="d_compr" style="position:absolute;bottom: 0px;left: -5px;transform: rotate(0deg);-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);transform-origin: 15% 0%;-ms-transform-origin: 15% 0%;-webkit-transform-origin: 15% 0%;-moz-transform-origin: 15% 0%;" src="../assets/pnH.png"><img id="d_compb" style="position:absolute;bottom: 182px;left: -18px;transform-origin: 50% 71%;-ms-transform-origin: 50% 71%;-moz-transform-origin: 50% 71%;-webkit-transform-origin: 50% 71%;transform: rotate(0deg);-webkit-transform: rotate(0deg);-moz-transform: rotate(0deg);-ms-transform: rotate(0deg);" src="../assets/bob.png"></div></div>';
	var addedNodes = [];

	var scale = { w: (50) };
	var params = {};

	var pointb=1;// - (default value 1), 1 to make the Add Point button visible, 0 to remove it
	var compassb=1;// - (default value 1), 1 to make the Show Compass button visible, 0 to remove it
	var protractorb=1;// - (default value 1), 1 to make the Show Protractor button visible, 0 to remove it
	var rulerb=1;// - (default value 1), 1 to make the Show Ruler button visible, 0 to remove it
	var undob=1;// - (default value 1), 1 to make the Undo button visible, 0 to remove it
	var displayFlag=0;// - (default value 0), 1 to stop input and show drawn figures, 0 for normal
	var asda=0;// - (default value 0), 1 for display answer
	var prString="";//"POINT[Z,324,240];POINT[A,24,240];JOIN[A,Z];POINT[M,45,200];POINT[N,435,340];JOIN[M,N];INTERSECTLL[AZ,MN,R];ARC[A,4,-60,-90,arcP];ARC[M,4,-60,-90,arcQ];INTERSECTAA[arcQ,arcP,G];INTERSECTLA[AZ,arcP,I]";// - (default value "POINT[Z,324,240];POINT[A,24,240];JOIN[A,Z];POINT[M,45,200];POINT[N,435,340];JOIN[M,N];INTERSECTLL[AZ,MN,R];ARC[A,4,-60,-90,arcP];ARC[M,4,-60,-90,arcQ];INTERSECTAA[arcQ,arcP,G]", Enter the urlencoded list of commands to make your construction, two commands separated by a semicolon. 
	var CommandsA = [];
	var insStages = [];
	var stagewiseNodes = [];
	var currentStage=-1;
	var toStage=0;
	var movingArc=null;
	var reps=0;
	var IntervalID=0;
	var allowPoint=false;
	var startP,endP={};
	var offset={};
	
	var nowDrawing="none";
	
	_drawcode.init = function (){
		var query = window.location.search.substring(1);//window.location.search gives the the string from ? in the address bar.
		var vars = query.split("&");  //if multiple parameters passed
		for (var i=0;i<vars.length;i++){
			var pair = vars[i].split("=");
			params[pair[0]] = pair[1];
		}
		
		$('#tooltip').hide();
		if(params.pointb!=1) pointb=0;
		if(params.compassb!=1) compassb=0;
		if(params.protractorb!=1) protractorb=0;
		if(params.rulerb!=1) rulerb=0;
		if(params.undob!=1) undob=0;
		if(params.replayb!=1) replayb=0;
		if(params.display==1) displayFlag=1;
		if(params.asda==1) asda=1;
		if(params.asda==1) explanationMode=true;
		if(params.help==1) showHelp='auto';
		if(params.question) $('#question').text(decodeURIComponent(params.question));
		if(params.mode=='tutorial') $('#buttons').hide();
		var sampleElement = $('#drawingPad')[0];
		if(window.self!==window.parent && asda!=1 && params.fullScreen!=='0' && (
			sampleElement.requestFullscreen ||
			sampleElement.webkitRequestFullscreen ||
			sampleElement.mozRequestFullScreen ||
			sampleElement.msRequestFullscreen
		)) {
			page = window.parent;
			content = page.$('iframe')[0];
			page.$('iframe').css('transform-origin', 'left top');
			page.document.addEventListener('webkitfullscreenchange', adjustDrawingPad, false);
			page.document.addEventListener('mozfullscreenchange', adjustDrawingPad, false);
			page.document.addEventListener('MSFullscreenChange', adjustDrawingPad, false);
			page.document.addEventListener('fullscreenchange', adjustDrawingPad, false);
			$('#buttons .fullScreen').removeClass('noButton');
		}
		if(!isNaN(+params.animInterval)) speedLevel=+params.animInterval;
		if(params.hasOwnProperty('instruction')) {
			encodedTutorial = params.instruction;
			$('body>div').append('<iframe id="tutorial"></iframe>').append('<div id="modeSwitcher" onclick="switchMode[this.innerHTML]();">learn</div>');
		}
		for(var tool in geometryTools) {
			if(!geometryTools.hasOwnProperty(tool) || !params[tool])
				continue;
			var toolSettings = params[tool].split('|');
			if(toolSettings[0]) {
				geometryTools[tool].autoShow = true;
				var layout = toolSettings[0].split(';');
				for(var i=0; i<layout.length; i++) {
					if(!layout[i])
						continue;
					var keyValue = layout[i].split('~');
					if(geometryTools[tool].layout.hasOwnProperty(keyValue[0]) && !isNaN(+keyValue[1]))
						geometryTools[tool].layout[keyValue[0]] = +keyValue[1];
				}
			}
			if(toolSettings[1]) {
				var mobility = toolSettings[1].split(';');
				for(var item in geometryTools[tool].mobility) {
					if(!geometryTools[tool].mobility.hasOwnProperty(item))
						continue;
					if(mobility.indexOf(item)>=0)
						geometryTools[tool].mobility[item] = true;
					else
						geometryTools[tool].mobility[item] = false;
				}
			}
		}
		//console.log(prString);
		if (!displayFlag){
			if (compassb!=0) {$('._show-compass,._hide-compass').addClass('noButton');}
			if (rulerb!=0) {$('._show-ruler,._hide-ruler').addClass('noButton');}
			if (protractorb!=0) {$('._show-protractor,._hide-protractor').addClass('noButton');}
			if (pointb!=0) {$('._selShapeGeo[rel="point"]').addClass('noButton');}
			if (undob!=0) {$('._selShapeGeo[rel="undo"]').addClass('noButton');}		
			$('#toolHelp').hide();
			for(var tool in geometryTools) {
				if(geometryTools.hasOwnProperty(tool) && geometryTools[tool].autoShow) {
					$('._show-'+tool).trigger('click');
				}
			}
			if(params.rulerFixed==1) {
				staticRuler = true;
				geometryTools.ruler.layout.x = 0;
				geometryTools.ruler.layout.y = 425;
				geometryTools.ruler.mobility.translate = false;
				geometryTools.ruler.mobility.rotate = false;
				$('._show-ruler').trigger('click');
			};
			if (!params.prString){
				$('#surface').attr('style','pointer-events:all');
				geometry.addEvts(SVG);return;
			}
			if(params.prString.length>0) prString=decodeURIComponent(params.prString.replace(/\+/g,  " "));
			else {
				$('#surface').attr('style','pointer-events:all');
				geometry.addEvts(SVG);return;
			}
			CommandsA=prString.split(";");
			insStages=findInsStage(CommandsA);
			reps=0;
			animTimeout=setTimeout(delay, speedLevel*500);
			$('#geoShapeSel').hide();
		}
		
		$('#instructions').hide();
		$(document).delegate('.nextButton','mousedown',function(){
			drawCode.nextClicked();
		});
		$(document).delegate('.prevButton','mousedown',function(){
			drawCode.prevClicked();
		});
	}
	function findInsStage(a){
		var b=[];
		for (var i=0;i<a.length;i++) if (a[i].indexOf("INSTR[",0)>=0) b.push(i);
		return b;
	}
	_drawcode.setDrawnShapes = function (jsonStr){
		var svgElems=JSON.parse(jsonStr);
		$(svgElems).each(function(ind,elem){
			$('#canvasBackground #viewport').append(parseSVG(elem));
		});
	}
	_drawcode.getDrawnShapes = function (){
		return sketchHelper.getHistory();
	}
	function parseSVG(s) {
        var div= document.createElementNS('http://www.w3.org/1999/xhtml', 'div');
        div.innerHTML= '<svg xmlns="http://www.w3.org/2000/svg">'+s+'</svg>';
        var frag= document.createDocumentFragment();
        while (div.firstChild.firstChild)
            frag.appendChild(div.firstChild.firstChild);
        return frag;
    }
	_drawcode.setSVG = function (s){
		SVG=s;
		//console.log(SVG);
	}
	_drawcode.setViewport = function (v){
		viewport=v;
		//console.log(SVG);
	}
	function reorder(){
		/*bringToFront(SVGDoc.getElementById('r3'),'main');
		bringToFront(SVGDoc.getElementById('r2'),'main');
		bringToFront(SVGDoc.getElementById('r1'),'main');
		*/
	}
	function sendToBack(object,referenceId)
	{
		SVGDoc.getElementById(referenceId).insertBefore(object,SVGDoc.getElementById(referenceId).getFirstChild());
	}

	function bringToFront(object,referenceId)
	{
		var tmp = SVGDoc.createElement("g");
		SVGDoc.getElementById(referenceId).appendChild(tmp);
		SVGDoc.getElementById(referenceId).replaceChild(object,tmp);
	}
	function addPoint(nam,x1,y1,showLab,showPoint){
		oNum++;
		var elemstr='<circle cx="'+x1+'" cy="'+y1+'" r="3" id="grph_'+oNum+'" style="cursor:default" pt_lbls="grph_'+(oNum+1)+'"></circle>';
		var settings = { fill:inkColor, stroke:inkColor, strokeWidth:"7", style: 'cursor:default', strokeOpacity: '0' };
		if (!showPoint) settings = { fill:inkColor, stroke:inkColor, strokeWidth:"2", style: 'cursor:default', opacity:'0'};
		var drawnNode = geometry.addAnimNode(elemstr,SVG,settings,null,animSpeed*500);
		addedNodes.push(drawnNode);
		oNum++;
		//<text x="381" y="445" id="grph_13" fill="#fff" rel="label" for_pt="386 425"> </text>
		var elemstr1='<text x="'+Math.round(x1 - 5)+'" y="'+Math.round(y1 + 20)+'" id="grph_'+oNum+'" rel="label" for_pt="'+x1+' '+y1+'">'+nam+'</text>';
		var settings1 = { fill:inkColor, strokeWidth:"1"};
		if (!showLab) settings1 = { fill:inkColor, opacity:'0', strokeWidth:"1"};
		var drawnNode1 = geometry.addAnimNode(elemstr1,SVG,settings1,null,animSpeed*500);
		addedNodes.push(drawnNode1);
		PointsArray.push([nam, x1, y1, 'grph_'+(oNum-1)]);
		if (currentStage>=0) stagewiseNodes[currentStage].push([[drawnNode,drawnNode1],'p',PointsArray.length-1]);
		$('#pencil,#d_comp').remove();$('<img src="../assets/pencil.png" id="pencil" style="width:32px;height:32px;position:absolute;top:'+y1+'px;left:'+x1+'px;"/>').appendTo('#canvasContainer').delay(animSpeed*500).fadeOut(animSpeed*250,function(){$(this).remove();});
	}

	function Join(nam,x1,y1,x2,y2,points,showLab){
		oNum++;
		//<line x1="235" y1="459" x2="435" y2="340" id="grph_18" style="cursor:default" len_node="len_18" pt_lbls="grph_17 grph_8"></line>
		var settings = { fill:"none", stroke:inkColor, strokeWidth:"2", strokeOpacity:"1", style:"cursor:default" };
		var elemstr = '',anim=(animSpeed>0)?{svgX2: x2, svgY2: y2}:null,drawnNode;
		if (animSpeed>0)
			elemstr = '<line x1="'+x1+'" y1="'+y1+'" x2="'+x1+'" y2="'+y1+'" id="grph_'+oNum+'" len_node="len_'+oNum+'" pt_lbls="'+points[0]+' '+points[1]+'"></line>';
		else 
			elemstr = '<line x1="'+x1+'" y1="'+y1+'" x2="'+x2+'" y2="'+y2+'" id="grph_'+oNum+'" len_node="len_'+oNum+'" pt_lbls="'+points[0]+' '+points[1]+'"></line>';
		var drawnNode = geometry.addAnimNode(elemstr,SVG,settings,anim,animSpeed*500);
		addedNodes.push(drawnNode);
		$('#pencil,#d_comp').remove();$('<img src="../assets/pencil.png" id="pencil" style="width:32px;height:32px;position:absolute;top:'+y1+'px;left:'+x1+'px;"/>').appendTo('#canvasContainer').animate({'top':y2+'px','left':x2+'px'},{duration:animSpeed*500,complete:function(){$(this).remove();}});
		
		var txtstr='<text x="'+(Math.round(x1+x2)/2)+'" y="'+(Math.round(y1+y2)/2+15)+'" id="len_'+oNum+'" rel="length" len_of="grph_'+oNum+'">'+nam+'</text>';
		var txtsetting = { fill:inkColor, strokeWidth:"1"};
		if (!showLab) txtsetting = { fill:inkColor, opacity:'0', strokeWidth:"1"};
		var drawnNode1 = geometry.addAnimNode(txtstr,SVG,txtsetting,null,animSpeed*500);
		addedNodes.push(drawnNode1);
		LinesArray.push([nam, x1, y1, x2, y2, 'grph_'+oNum ]);
		if (currentStage>=0) stagewiseNodes[currentStage].push([[drawnNode,drawnNode1],'l',LinesArray.length-1]);
	}
	
	function drawArc(arcC,Rad,sAng,tAng,nam){
		oNum++;
		var start={X:Number($('#'+arcC).attr('cx')), Y:Number($('#'+arcC).attr('cy'))};
		var drawnNode = sketchHelper.drawArcCentre(start.X, start.Y, 0, 0, "point", SVG);
		var arcPnt = drawnNode;
		addedNodes.push(drawnNode);
		//M 436,191 A 122,122	43 0,0 371,127
		//M 50  133 A 100 100   69 0 0 250 57
		//sketchpad.path(path.move(start.X, start.Y).arc(rad, rad, angle1, majArc, sweepFlag, (endPnt.X), (endPnt.Y)), outSet)
		//<path d="M436.37814523669005,191.68790017861923A122.32295988893361,122.32295988893361 43.72794441453414 0,0 371.81281925918296,127.4085223896659" fill="none" stroke="#fff" stroke-width="1" stroke-dasharray="0" id="grph_17" style="cursor:default" stroke-opacity="1" center="grph_1" type="arc" radius="122.32295989990234" angle="144.727943"></path>
		oNum++;
		var twoPI = Math.PI/180;
		var prevAng = sAng*twoPI;
		var rad = Rad*scale.w;
		//var majArc = (Math.abs(tAng)>180)?1:0;
		//var sweepFlag = (tAng>0)?1:0;
		tAng = (Math.abs(tAng)>355)?Math.sign(tAng)*359:tAng;
		var outset = {fill:"none", stroke:inkColor, strokeWidth:"2", strokeDashArray:"0,0", style:"cursor:default", strokeOpacity:"1", center:arcC, type:"arc", radius:rad, angle:tAng, stAtAng:sAng};
		var ma=(tAng>=180)?1:0, sf=(tAng>0)?1:0;
		var trnAng=sAng+tAng;
		x1 = start.X+Math.cos(prevAng)*rad;y1 = start.Y+Math.sin(prevAng)*rad;
		x2 = start.X+Math.cos(prevAng+tAng*twoPI)*rad;y2 = start.Y+Math.sin(prevAng+tAng*twoPI)*rad;
		
		var elemstr = '',drawnNode1;
		var anim=null;
		if (animSpeed>0){
			elemstr = '<path d="M'+x1+','+y1+' A'+rad+','+rad+' 0 0,0 '+x1+','+y1+'" id="grph_'+oNum+'"></path>';
			drawnNode1 = geometry.addAnimNode(elemstr,SVG,outset,anim,animSpeed*500);
			drawCircle('grph_'+oNum,start.X,start.Y,rad,sAng,tAng,animSpeed*500);
		}
		else {
			elemstr = '<path d="M'+x1+','+y1+' A'+rad+','+rad+' '+trnAng+' '+ma+','+sf+' '+x2+','+y2+(Math.abs(tAng)>358?" z":'')+'" id="grph_'+oNum+'"></path>';
			drawnNode1 = geometry.addAnimNode(elemstr,SVG,outset,anim,animSpeed*500);
		}
		addedNodes.push(drawnNode1);
		ArcsArray.push([nam, x1, y1, x2, y2, 'grph_'+oNum , arcC, Rad, tAng]);
		if (currentStage>=0) stagewiseNodes[currentStage].push([[drawnNode,drawnNode1],'a',ArcsArray.length-1]);
	}
	   
    function drawCircle(id,cx,cy,rad,sAng,tAng,ttime) {
        var circle = document.getElementById(id);movingArc=circle;
        var angle = 0, turn=0;//ttime=(ttime<50)?1:ttime;
        $('#pencil,#d_comp').remove();$(animCompass).appendTo('#canvasContainer');
        // (b^2 + c^2 - a^2)/2bc = cos(A) cosine rule
        var compVAng=-Math.acos(1- (rad*rad)/(2*200*200))*180/Math.PI;
        $('#d_compr').css({'transform':'rotate('+compVAng+'deg)','-webkit-transform':'rotate('+compVAng+'deg)','-moz-transform':'rotate('+compVAng+'deg)','-ms-transform':'rotate('+compVAng+'deg)'});
        var compAng=compVAng/2;//console.log(sAng,compAng,compAng-sAng);
        $('#d_compb').css({'transform':'rotate('+compAng+'deg)','-webkit-transform':'rotate('+compAng+'deg)','-moz-transform':'rotate('+compAng+'deg)','-ms-transform':'rotate('+compAng+'deg)'});
        $('#d_comp').css({'top':cy+'px','left':cx+'px','opacity':'0.7','transform':'rotate('+(sAng-compAng)+'deg)','-webkit-transform':'rotate('+(sAng-compAng)+'deg)','-moz-transform':'rotate('+(sAng-compAng)+'deg)','-ms-transform':'rotate('+(sAng-compAng)+'deg)'});
        timer = window.setInterval(
	        function() {
	            if (turn>=50 || !circle) {
	            	clearInterval(timer);timer=null;$('#pencil,#d_comp,#compassradius').remove();movingArc=null;
	            	return;
	            }
	            angle +=tAng/50;angle=Math.abs(angle)>355?Math.sign(angle)*359:angle;turn++;//console.log(tAng,angle);
	            var ma=(angle>=180)?1:0, sf=(angle>0)?1:0;
	            var radians= (sAng/180) * Math.PI;
	            var x1 = cx + Math.cos(radians) * rad, y1 = cy + Math.sin(radians) * rad;
	            radians= ((angle+sAng)/180) * Math.PI;
	            var x2 = cx + Math.cos(radians) * rad, y2 = cy + Math.sin(radians) * rad;
	            var trnAng=sAng+angle;
	            $('#d_comp').css({'transform':'rotate('+(trnAng-compAng)+'deg)','-webkit-transform':'rotate('+(trnAng-compAng)+'deg)','-moz-transform':'rotate('+(trnAng-compAng)+'deg)','-ms-transform':'rotate('+(trnAng-compAng)+'deg)'});
	            //console.log(trnAng,angle,compAng,compAng-trnAng);
	            if (Math.round(x1)==Math.round(x2) && Math.round(y1)==Math.round(y2)) y2-=0.1;
	            circle.setAttribute("d", "M"+x1+","+y1+" A"+rad+","+rad+" "+(trnAng)+" "+ma+","+sf+" "+ x2+","+y2+""+(Math.abs(angle)>358?" z":''));
	            $('#pencil').css({'top':y2+'px','left':x2+'px'});
	            SVG.change($('#compassradius')[0],{'x1':cx,'y1':cy,'x2':x2,'y2':y2});
	        } 
	      ,ttime/50);
	}
	/*function getNewTextPosition(orig){
		var textElemsPoints=[];
		$('#canvasContainer text').each(function(ind,itm){$(itm).attr('x')});
	}*/
	function Intersect (p1,p2,q1,q2,namey,showLab){
		var s1="i";var s2="i";
		if (p1.X-p2.X!=0) s1=(p1.Y-p2.Y)/(p1.X-p2.X);
		if (q1.X-q2.X!=0) s2=(q1.Y-q2.Y)/(q1.X-q2.X);
		
		var diffLine=true;var ip={};
		if ((p1.X==q1.X && p1.Y==q1.Y) || (p1.X==q2.X && p1.Y==q2.Y)){
			//console.log('no action');
		}
		else if (s1!=s2){
			ip.X = ((p1.X*p2.Y-p1.Y*p2.X)*(q1.X - q2.X)-(q1.X*q2.Y-q1.Y*q2.X)*(p1.X - p2.X))/((p1.X-p2.X)*(q1.Y-q2.Y)-(p1.Y-p2.Y)*(q1.X-q2.X));
			ip.Y = ((p1.X*p2.Y-p1.Y*p2.X)*(q1.Y - q2.Y)-(q1.X*q2.Y-q1.Y*q2.X)*(p1.Y - p2.Y))/((p1.X-p2.X)*(q1.Y-q2.Y)-(p1.Y-p2.Y)*(q1.X-q2.X));
			if (ip.X>=Math.max(Math.min(p1.X,p2.X),Math.min(q1.X,q2.X)) && ip.X<=Math.min(Math.max(p1.X,p2.X),Math.max(q1.X,q2.X)) && ip.Y>=Math.max(Math.min(p1.Y,p2.Y),Math.min(q1.Y,q2.Y)) && ip.Y<=Math.min(Math.max(p1.Y,p2.Y),Math.max(q1.Y,q2.Y))){
				addPoint(namey,ip.X,ip.Y,showLab,1);
			}
		}
		//var x1=Det(Det(A.X,A.X,B.X,B.X),(A.X-B.X),Det(C.X,C.Y,D.X,D.Y),(C.X-D.X))/Det((A.X-B.X),(A.Y-B.Y),(C.X-D.X),(C.Y-D.Y));
		//var y1=Det(Det(A.X,A.Y,B.X,B.Y),(A.Y-B.Y),Det(C.X,C.Y,D.X,D.Y),(C.Y-D.Y))/Det((A.X-B.X),(A.Y-B.Y),(C.X-D.X),(C.Y-D.Y));
		//console.log(namey+' '+x1+' '+y1+' '+showLab+' '+1);
		//addPoint(namey,x1,y1,showLab,1);
	}
	
	function Det(a,b,c,d){
		return(a*d-b*c);
	}
	
	function Intersectla (A,B,namey,pl1,pl2, showLab){
		//line-arc intersection
        var InterPoints = [];
        //intersection point 1
        var ip1 = {};
        //intersection point 2
        var ip2 = {};
        //calculations reference http://paulbourke.net/geometry/sphereline/ without the z co-ordinates
        var p1 = A;
        var p2 = B;
		arcs=ArcsArray[pl2];//console.log(arcs);
		var sc={X:Number($('#'+arcs[6]).attr('cx')), Y:Number($('#'+arcs[6]).attr('cy'))};
		var r=arcs[7]*scale.w;
        var dp = {};
        var a, b, c, mu1, mu2;
        dp.X = p2.X - p1.X;
        dp.Y = p2.Y - p1.Y;
        a = dp.X * dp.X + dp.Y * dp.Y;
        b = 2 * (dp.X * (p1.X - sc.X) + dp.Y * (p1.Y - sc.Y));
        c = sc.X * sc.X + sc.Y * sc.Y;
        c += p1.X * p1.X + p1.Y * p1.Y;
        c -= 2 * (sc.X * p1.X + sc.Y * p1.Y);
        c -= r * r;
        var bb4ac = b * b - 4 * a * c;
        if (bb4ac < 0) {
            mu1 = 0;
            mu2 = 0;
            return (false);
        }
        mu1 = (-b + Math.sqrt(bb4ac)) / (2 * a);
        mu2 = (-b - Math.sqrt(bb4ac)) / (2 * a);

        if (mu1 <= 1 && mu1 >= 0) {
            ip1.X = p1.X + mu1 * (p2.X - p1.X);
            ip1.Y = p1.Y + mu1 * (p2.Y - p1.Y);
            //we have a point lying on the line segment that was supposed to be on circle defined by the completion of arc
            //lets check if the point also lies on the arc (line done for arc-arc)
            if (checkIfPointOnArc(ip1, arcs, sc)) {
                InterPoints.push([ip1.X,ip1.Y,namey]);
            }
        }
        if (mu2 <= 1 && mu2 >= 0) {
            ip2.X = p1.X + mu2 * (p2.X - p1.X);
            ip2.Y = p1.Y + mu2 * (p2.Y - p1.Y);
            if (checkIfPointOnArc(ip2, arcs, sc)) {
                InterPoints.push([ip2.X,ip2.Y,namey]);
            }
        }
		for (var i=0;i<InterPoints.length;i++){
			var nam=(InterPoints.length==1 || i==0)?namey:(namey+i);
			addPoint(nam,InterPoints[i][0],InterPoints[i][1],showLab,1);
		}
	}
	function Intersectaa (c1,c2,P,pl1,pl2,namey,d,h,showLab){
		var arc1=ArcsArray[pl1];var arc2=ArcsArray[pl2];
        //[currently working on arc-arc intersection] below will be true with list only having path
        var arcRad1 = arc1[7]*scale.w;
        var arcRad2 = arc2[7]*scale.w;
		var InterPoints=[];
        var d = Math.sqrt((c1.Y-c2.Y)*(c1.Y-c2.Y) + (c1.X-c2.X)*(c1.X-c2.X));
        if (d < (arcRad1 + arcRad2) && d > (Math.abs(arcRad1 - arcRad2))) {
            //[reference]paulbourke.net/geometry/2circle/
            var a = ((Math.pow(d, 2) + Math.pow(arcRad1, 2) - Math.pow(arcRad2, 2)) / (2 * d));
            var h = Math.sqrt(Math.pow(arcRad1, 2) - Math.pow(a, 2));
            var a_d = (a / d);
            var h_d = (h / d);
            //intersection point on line joining the centers
            var p0 = { X: null, Y: null };
            p0.X = c1.X + (a_d * (c2.X - c1.X));
            p0.Y = c1.Y + (a_d * (c2.Y - c1.Y));
            //the intersection points
            var p1 = { X: null, Y: null };
            var p2 = { X: null, Y: null };
            p1.X = p0.X - (h_d * (c2.Y - c1.Y));
            p1.Y = p0.Y + (h_d * (c2.X - c1.X));

            p2.X = p0.X + (h_d * (c2.Y - c1.Y));
            p2.Y = p0.Y - (h_d * (c2.X - c1.X));
			
            if (checkIfPointOnArc(p1, arc1, c1)) {
                if (checkIfPointOnArc(p1, arc2, c2)) {
					InterPoints.push([p1.X,p1.Y,namey]);
                }
            }
            if (checkIfPointOnArc(p2, arc1, c1)) {
                if (checkIfPointOnArc(p2, arc2, c2)) {
					InterPoints.push([p2.X,p2.Y,namey]);
                }
            }
        }
		for (var i=0;i<InterPoints.length;i++){
			var nam=(InterPoints.length==1 || i==0)?namey:(namey+i);
			addPoint(nam,InterPoints[i][0],InterPoints[i][1],showLab,1);
		}
	}
	
	function checkIfPointOnArc(p, arc, arcC){
		var angle = Math.atan2(p.Y-arcC.Y, p.X-arcC.X)*180/Math.PI;
		var a1 =  Math.atan2(arc[2]-arcC.Y, arc[1]-arcC.X)*180/Math.PI;
		var a2 =  Math.atan2(arc[4]-arcC.Y, arc[3]-arcC.X)*180/Math.PI;
		var bool=false;
		a1=(a1+360)%360;a2=(a2+360)%360;angle=(angle+360)%360;
		//console.log(a1+" | "+a2+' | '+arc[8]+' | '+angle);
		/*if (arc[8]>=0){
			if (a1>a2) a1%=360;
			
		}*/
		if (arc[8]>=0) {//anticlockwise
			if ((Math.round(angle-a1)>=0 && Math.round(angle-a1)<=Math.round(arc[8])) || (Math.round(a2-angle)>=0 && Math.round(a2-angle)<=Math.round(arc[8]))) return true;
		}
		else {
			if ((Math.round(angle-a1)<=0 && Math.round(angle-a1)>=Math.round(arc[8])) || (Math.round(a2-angle)<=0 && Math.round(a2-angle)>=Math.round(arc[8]))) return true;
		}
		/*if (a2>=a1) {
			if (angle>=a1 && angle<=a2) bool=true;
		}
		else if (a1>=a2) {
			if (angle<=a1 && angle>=a2) bool=true;
		}*/
		return false;
	}
	function inBtw(xx,yy,x1,y1,x2,y2){
		if (x1>x2) {t=x1;x1=x2;x2=t;}
		if (y1>y2) {t=y1;y1=y2;y2=t;}
		
		if (xx<=x2+1 && xx>=x1-1 && yy<=y2+1 && yy>=y1-1) {return true;}
		else {return false;}
	}
	function inBtwl(xx,yy,x1,y1,x2,y2){
		if (Math.round((yy-y1)*(x1-x2))==Math.round((xx-x1)*(y1-y2))) inLine=true;
		else inLine=false;
		
		if (x1>x2) {t=x1;x1=x2;x2=t;}
		if (y1>y2) {t=y1;y1=y2;y2=t;}
		
		if (xx<=x2+1 && xx>=x1-1 && yy<=y2+1 && yy>=y1-1 && inLine) {return true;}
		else {return false;}
	}
	function delay(){
		if(reps == CommandsA.length){
			//clearInterval(IntervalID);	//When all reps done, the 500 ms intervals are not generated
			if (!asda){
				$('#surface').attr('style','pointer-events:all');
				$('#geoShapeSel').show();$('#instructions').show().attr('style','').html('');
				geometry.addEvts(SVG);
				addEventToNodes();
			}
			else if (!$('#instructions').is(':visible')){
				showInstr('');
				// $('#instructions').prepend('Click "Draw" above to continue drawing.');
			}
		}
		else{
			var showNext=parseP(CommandsA[reps]);bringPointsToFront();
			if (showNext) {
				currentStage=insStages.indexOf(reps);reps++;
				stagewiseNodes[currentStage]=[];
				if (insStages.indexOf(reps)<0) delay();
			}
			else {
				reps++;
				if (insStages.indexOf(reps)<0) animTimeout=setTimeout(delay,speedLevel*500);
			}
		}
	}
	_drawcode.nextClicked=function (){
		clearTimeout(animTimeout);$('#pencil,#compassradius').remove();
		if (timer) {clearInterval(timer);completeArcs();timer=null;} 
		if (insStages.indexOf(reps)>=0) {delay();}
		else if(reps == CommandsA.length){
			//clearInterval(IntervalID);	//When all reps done, the 500 ms intervals are not generated
			$('#surface').attr('style','pointer-events:all');
			$('#geoShapeSel').show();$('#instructions').show().attr('style','').html('');
			geometry.addEvts(SVG);
			addEventToNodes();
		}
		else {
			speedLevel=0;animSpeed=0;
			delay();
			speedLevel=3;animSpeed=speedLevel*2/3;delay();
		}
	};
	_drawcode.prevClicked=function (){
		var i=0;$('#pencil,#compassradius').remove();
		for (i=0;i<insStages.length;i++) if (reps<=insStages[i]) break;
		reps=0;
		if (i>0){
			clearTimeout(animTimeout);clearInterval(timer);timer=null;
			reset();speedLevel=0;animSpeed=speedLevel*2/3;
			if (i-2>=0) toStage=insStages[i-2];
			else toStage=insStages[i-1];
		}
		else{
			clearTimeout(animTimeout);clearInterval(timer);timer=null;
			reset();toStage=0;
		}
		ffTo();
	};
	_drawcode.replayAnime=function(){
		clearTimeout(animTimeout);clearInterval(timer);timer=null;
		reset();speedLevel=0;animSpeed=speedLevel*2/3;
		toStage=CommandsA.length;
		ffTo();
	}
	function completeArcs(){
		var elem=$(movingArc);
		if (elem.length>0){
			d=elem.attr('d').split(' ');var x1=d[0].substr(1).split(',')[0]*1,y1=d[0].substr(1).split(',')[1]*1;
			var cx=$('#'+elem.attr('center')).attr('cx')*1,cy=$('#'+elem.attr('center')).attr('cy')*1;
			var tAng=elem.attr('angle')*1,rad=elem.attr('radius')*1,sAng=Math.atan2(y1-cy,x1-cx)*180/Math.PI;
			d[2]=tAng+sAng;var ma=(tAng>=180)?1:0, sf=(tAng>0)?1:0;d[3]=ma+','+sf;
			var radians= ((tAng+sAng)/180) * Math.PI, x2 = cx + Math.cos(radians) * rad, y2 = cy + Math.sin(radians) * rad;
			if (Math.round(x1)==Math.round(x2) && Math.round(y1)==Math.round(y2)) y2-=0.1;
			d[4]=x2+','+y2;elem[0].setAttribute("d", d.join(' '));movingArc=null;
		}
	};
	function ffTo(){
		if(reps == toStage){
			speedLevel=3;animSpeed=speedLevel*2/3;setTimeout(delay,0);
		}
		else{
			var showNext=parseP(CommandsA[reps]);
			if (showNext) {
				currentStage=insStages.indexOf(reps);stagewiseNodes[currentStage]=[];
			}
			reps++;animTimeout=setTimeout(ffTo,0);
		}
	}
	function parseP(comString){
		var pString=comString;
		//console.log("parsing....."+pString);
		
		var l1=pString.indexOf("INSTR[",0);
		if (l1>=0){
			l1=l1+5;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			showInstr(params1);
			return 1;
		}

		l1=pString.indexOf("POINT[",0);
		if (l1>=0){
			l1=l1+5;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			var pArray=params1.split(",");
			if (pArray.length==1){
				addPoint(pArray[0],$('#canvasBackground').attr('width')/2,$('#canvasBackground').attr('height')/2,1,1);
			}else if (pArray.length==3){
				addPoint(pArray[0],Number(pArray[1]),Number(pArray[2]),1,1);
			}else if (pArray.length==4){
				addPoint(pArray[0],Number(pArray[1]),Number(pArray[2]),Number(pArray[3]),1);
			}else if (pArray.length==5){
				addPoint(pArray[0],Number(pArray[1]),Number(pArray[2]),Number(pArray[3]),Number(pArray[4]));
			}
			return;
		}
		
		l1=pString.indexOf("JOIN[",0);
		if (l1>=0){
			l1=l1+4;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			var pArray=params1.split(",");var showL=0;
			if (pArray.length==2){
				p1=findA(pArray[0],"p");
				p2=findA(pArray[1],"p");
				namL=pArray[0]+pArray[1];
			}else if(pArray.length==3){
				p1=findA(pArray[0],"p");
				p2=findA(pArray[1],"p");
				namL=pArray[2];showL=1;
			}else if(pArray.length==4){
				p1=findA(pArray[0],"p");
				p2=findA(pArray[1],"p");
				namL=pArray[2];showL=Number(pArray[3]);
			}
			Join(namL, PointsArray[p1][1], PointsArray[p1][2], PointsArray[p2][1], PointsArray[p2][2], [PointsArray[p1][3],PointsArray[p2][3]], showL);
		}
		
		l1=pString.indexOf("LINE[",0);
		if (l1>=0){
			l1=l1+4;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			var pArray=params1.split(",");
			var angl=0,dist=0,namL="";var showL=0;
			if (pArray.length==3){
				p1=findA(pArray[0],"p");
				dist=Number(pArray[1]);
				angl=Number(pArray[2]);
				namL="line"+p1;
			}else if(pArray.length==4){
				p1=findA(pArray[0],"p");
				dist=Number(pArray[1]);
				angl=Number(pArray[2]);
				namL=pArray[3];showL=1;
			}else if(pArray.length==5){
				p1=findA(pArray[0],"p");
				dist=Number(pArray[1]);
				angl=Number(pArray[2]);
				namL=pArray[3];showL=Number(pArray[4]);
			}
			difx=Math.sin((90-angl)*Math.PI/180)*dist*scale.w;dify=-Math.cos((90-angl)*Math.PI/180)*dist*scale.w;
			var p2=PointsArray.length;
			addPoint('',PointsArray[p1][1]+difx,PointsArray[p1][2]+dify,1,1);
			Join(namL,PointsArray[p1][1],PointsArray[p1][2],PointsArray[p2][1],PointsArray[p2][2], [PointsArray[p1][3],PointsArray[p2][3]],showL);
		}
		
		l1=pString.indexOf("ARC[",0);
		if (l1>=0){
			//console.log("....found arc at "+l1);
			l1=l1+3;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			//var Mat=params1.match(",");console.log(Mat);
			var pArray=params1.split(",");
			if (pArray.length==2){
				p1=findA(pArray[0],"p");
				Sangl=0;Tangl=360;
				namL="circle"+pArray[0];
			}
			else if (pArray.length==4){
				p1=findA(pArray[0],"p");
				Sangl=Number(pArray[2]);
				Tangl=Number(pArray[3]);
				namL="arc"+Math.round(Sangl)+Math.round(Tangl);
			}
			else if(pArray.length==5){
				p1=findA(pArray[0],"p");
				Sangl=Number(pArray[2]);
				Tangl=Number(pArray[3]);
				namL=pArray[4];
			}
			if (!Number(pArray[1])){
				p2=findA(pArray[1],"p");
				p2l=findA(pArray[1],"l");
				p2a=findA(pArray[1],"a");
				p2sp=pArray[1].split("-");
				if (pArray[1].indexOf('-'>=0)){
					p2s1=findA(p2sp[0],"p");p2s2=findA(p2sp[1],"p");
				}
				if (p2>=0){
					difx=PointsArray[p2][1]-PointsArray[p1][1];dify=PointsArray[p2][2]-PointsArray[p1][2];
					radi=Math.sqrt(difx*difx+dify*dify)/scale.w;
				}else if (p2l!=-1){
					lins1=LinesArray[p2l];//[nam, x1, y1, x2, y2, 'grph_'+oNum ]
					difx=lins1[1]-lins1[3];dify=lins1[2]-lins1[4];
					radi=Math.sqrt(difx*difx+dify*dify)/scale.w;
				}else if (p2a!=-1){
					lins1=ArcsArray[p2l];//[nam, x1, y1, x2, y2, 'grph_'+oNum , arcC, Rad]
					radi=lins1[7];
				}else if (p2sp.length>1){
					difx=PointsArray[p2s1][1]-PointsArray[p2s2][1];dify=PointsArray[p2s1][2]-PointsArray[p2s2][2];
					radi=Math.sqrt(difx*difx+dify*dify)/scale.w;
				}
			}else{
				radi=Number(pArray[1]);
			}
			drawArc(PointsArray[p1][3],radi,-Sangl,Tangl,namL);
		}

		l1=pString.indexOf("INTERSECTLL[",0);
		if (l1>=0){
			//console.log("....found Intersect at "+l1);
			l1=l1+11;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			var pArray=params1.split(",");
			var a1={};var a2={};var a3={};var a4={};
			var lk1=[];var lk2=[];
			var showL=0;
			if (pArray[pArray.length-1]=="-1" || pArray[pArray.length-1]=="-0"){
				if (pArray[pArray.length]=="-1") showL=1;
				else showL=0;
				pArray.pop();
			}else{
				showL=1;
			}
			if (pArray.length<=3){
				pl1=findA(pArray[0],"l");
				pl2=findA(pArray[1],"l");
				a1={X:LinesArray[pl1][1],Y:LinesArray[pl1][2]};
				a2={X:LinesArray[pl1][3],Y:LinesArray[pl1][4]};
				a3={X:LinesArray[pl2][1],Y:LinesArray[pl2][2]};
				a4={X:LinesArray[pl2][3],Y:LinesArray[pl2][4]};
				if (pArray.length==2){
					naml="";
				}else if (pArray.length==3){
					naml=pArray[2];
				}
			}
			else {
				p1=findA(pArray[0],"p");
				p2=findA(pArray[1],"p");
				p3=findA(pArray[2],"p");
				p4=findA(pArray[3],"p");
				a1={X:PointsArray[p1][1],Y:PointsArray[p1][2]};
				a2={X:PointsArray[p2][1],Y:PointsArray[p2][2]};
				a3={X:PointsArray[p3][1],Y:PointsArray[p3][2]};
				a4={X:PointsArray[p4][1],Y:PointsArray[p4][2]};
				if (pArray.length==4){
					naml="";
				}else if (pArray.length==5){
					naml=pArray[4];
				}
			}
			Intersect(a1,a2,a3,a4,naml,showL);	
		}
		
		l1=pString.indexOf("INTERSECTLA[",0);//console.log(l1);
		if (l1>=0){
			//console.log("....found Intersect line-arc at"+l1);
			l1=l1+11;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			//var Mat=params1.match(",");console.log(Mat);
			var pArray=params1.split(",");
			var a1={};var a2={};
			var showL=0;
			if (pArray.length==4){showL=pArray[3];pArray.pop();}
			else if (pArray.length==3){ showL=1;}
			if (pArray.length==3){
				pl1=findA(pArray[0],"l");
				pl2=findA(pArray[1],"a");
				a1={X:LinesArray[pl1][1],Y:LinesArray[pl1][2]};
				a2={X:LinesArray[pl1][3],Y:LinesArray[pl1][4]};
				naml=pArray[2];
			}
			Intersectla(a1,a2,naml,pl1,pl2,showL);	
		}
		
		l1=pString.indexOf("INTERSECTAA[",0);//console.log(l1);
		if (l1>=0){
			//console.log("....found Intersect arc-arc at "+l1);
			l1=l1+11;
			var params1=pString.substring((l1+1),pString.indexOf("]",l1));
			var pArray=params1.split(",");
			var c1={};var c2={};var P={};
			var showL=0;
			if (pArray.length==4) {showL=pArray[3];pArray.pop();}
			else if (pArray.length==3) {showL=1;}
			if (pArray.length==3){
				pl1=findA(pArray[0],"a");
				pl2=findA(pArray[1],"a");
				naml=pArray[2];
				//[nam, x1, y1, x2, y2, 'grph_'+oNum , arcC, Rad]
				arc1=ArcsArray[pl1];	arc2=ArcsArray[pl2];
				c1={X:Number($('#'+arc1[6]).attr('cx')),Y:Number($('#'+arc1[6]).attr('cy'))};r1=arc1[7]*scale.w;
				c2={X:Number($('#'+arc2[6]).attr('cx')),Y:Number($('#'+arc2[6]).attr('cy'))};r2=arc2[7]*scale.w;
				dist=Math.sqrt((c1.Y-c2.Y)*(c1.Y-c2.Y) + (c1.X-c2.X)*(c1.X-c2.X));
				//if (dist>r1+r2) console.log("No Solutions  -the circles are separate");
				//if (dist<Math.abs(r1-r2)) console.log("No Solutions  -one circle is contained within the other");
				//if (dist==0) console.log("No Solutions  -cocentric circles");
				if (dist<=(r1+r2)){
					a = (r1*r1 - r2*r2 + dist*dist ) / (2*dist);
					h = Math.sqrt(r1*r1 - a*a);
					P={X:(c1.X+a*(c2.X-c1.X)/dist),Y:(c1.Y+a*(c2.Y-c1.Y)/dist)};
					if (dist==(r1+r2)){
						addPoint(naml,P.X,P.Y,0,1);
					}else{
						//(c1,c2,P,pl1,pl2,namey,d,h,showLab)
						Intersectaa(c1,c2,P,pl1,pl2,naml,dist,h,showL);	
					}
				}
			}
		}
	}
	_drawcode.findInA = function(T,arr) {
		switch (arr){
			case "p":{
				for (var i=0;i<PointsArray.length;i++)
					if (PointsArray[i][0]==T) return i;
				break;
			}
			case "l":{
				for (var i=0;i<LinesArray.length;i++)
					if (LinesArray[i][0]==T) return i;
				break;
			}
			case "a":{
				for (var i=0;i<ArcsArray.length;i++)
					if (ArcsArray[i][0]==T) return i;
				break;
			}
		}
		return -1;
	}
	function findA(T,arr){
		switch (arr){
			case "p":{
				for (var i=0;i<PointsArray.length;i++)
					if (PointsArray[i][0]==T) return i;
				break;
			}
			case "l":{
				for (var i=0;i<LinesArray.length;i++)
					if (LinesArray[i][0]==T) return i;
				break;
			}
			case "a":{
				for (var i=0;i<ArcsArray.length;i++)
					if (ArcsArray[i][0]==T) return i;
				break;
			}
		}
		return -1;
	}
	_drawcode.clr = function (){
		clearInterval(IntervalID);
	}
	function reset () {
		$(addedNodes).each(function (ind,itm){$(itm).remove();});$('#pencil,#compassradius').remove();
		PointsArray=[];LinesArray=[];ArcsArray=[];reps=0;pointNum=0;lineNum=0;arcNum=0;oNum=0;addedNodes = [];stagewiseNodes=[];
		sketchHelper.reset();
	}
	function addEventToNodes(){
		for (var i=0;i<addedNodes.length;i++){
			geometry.addHandler(addedNodes[i], SVG);
		}
	}
	function showInstr(msg){
		$('#instructions').show().css({'bottom':'20px','min-height':'40px','height':'auto','top': 'initial','padding-left': '40px','padding-right': '40px'}).html(msg+'<div class="insControl" style="display:none;"><div class="prevButton"><div></div></div><div class="nextButton"><div></div></div></div>');
		if(params.mode!='tutorial' || insStages.length)
			$('.insControl').delay(1000).fadeIn(1000);
		if(params.mode=='tutorial' && reps>=insStages[insStages.length-1]) {
			$('.nextButton').hide();
		}

	}
	function bringPointsToFront() {
		var points=document.getElementById('canvasBackground').getElementsByTagName('circle');
		for (var i=1;i<=pointNum;i++){
			obj=points[i];
			bringToFront(obj.parentElement,"canvasBackground");
		}
	}
	function bringToFront(object,referenceId){
		var tmp = document.createElement("g");
		document.getElementById(referenceId).appendChild(tmp);
		document.getElementById(referenceId).replaceChild(object,tmp);
	}
	
	return _drawcode;
} ();

var drawcode;
if (drawcode == undefined) {
    drawcode = drawCode;
}
