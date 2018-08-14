var SketchHelper = function () {
    var _sketchHelper = {};
    var drawNodes = []; //to keep list of all the elements drawn on the sketchpad
    //TODO: check what to store , DOM elements or svg model objects
    //TODO: implement object with key as id
    var _nodeNum = 0; //to keep the unique IDs for the elements
    var start = null; //position of the cursor drag start position
    var startTip = null;
    var outline = null; //dotted outline for representing the shape
    var origOffSet = null; //offset of sketchpad
    var offset = null;
    var points = []; //list of all the points to match the proximity
    //FIXME: for small charts we can go through all the point nodes to check if mouse up is near an existing point
    var startNode = null; //connector line
    var endNode = null; //connector line
    var currNode = []; //selected nodes list
    var selectorRect = []; //dotted rectangles for showing selected elements
	
	var orderOfElems=0;
    var labelPoints = [];
    //x y node-id txtLabel-id
    //TODO: comment
    var useLabelFlag = false;
	var lastCurr = null;
    var arcCtr = null; //center co-ordinates for arc
    var arcPnt = null; //center object for arc [html object]
    var rad = null; //arc radius
    var arcConn = null; // arc connector line
    var lineLen = null; //tet node for line length
    var scale = { w: (50) };
    //assuming cm can
    var prevArcA = null;//previous arc angle
    var compObjRotAngle = 0;//previous compass angle

    var _isDActive = false;    //is protractor enabled
    var _isCompActive = false;    //is compass enabled
    var _isRActive = false;    //is ruler enabled
    //var _isDActive = false;    //is protractor enabled
    //for undo redo history
    var _history = []; 
    var _redoNodes = [];
    var _actions = {
        deleted: 0,
        added: 1
    };
    var viewport,vpOffset={top:0,left:0};

    var animeDrawnCount = 0;

    _sketchHelper.setAnimeDrawnCount = function(){
        animeDrawnCount=drawNodes.length-1;
    }

    //history 'action' :1 - added 0 - deleted 'node' :node
    //sample:_history.push({ 'action': 0, 'node': deletedNodes });
    
    _sketchHelper.nodesToStr = function () {
        var data = [];
        for (var i = 0; i < drawNodes.length; i++) {
            var svgStr = XmlToSvgStr(drawNodes[i]);
            data.push(svgStr);
        }
        return data;
    };
    /*
	_sketchHelper.updateTextLables = function (nx,ny){
        vpOffset.top=ny;vpOffset.left=nx;
        $('input._point_lbl').each(function(){
            var ip=($(this).attr('-data-lblpos')).split(',');
            $(this).css({top: (ip[0]*1+vpOffset.top)+'px',left: (ip[1]*1+vpOffset.left)+'px'});
        });
    };
    */
	function nodeToStr(node){
        return XmlToSvgStr(node);
    }
    function screenToWindow(pObject){
        return {X: pObject.X-vpOffset.left, Y: pObject.Y-vpOffset.top};
    }
    function windowToScreen(pObject){
        return {X: pObject.X+vpOffset.left, Y: pObject.Y+vpOffset.top};
    }
	function panPoints(pObject){
        //return {X: pObject.X-vpOffset.left, Y: pObject.Y-vpOffset.top};
        return {X: pObject.X, Y: pObject.Y};
    }
	_sketchHelper.bringPointsToFrontV = function () {
        for (var i in points){
            obj=$('#viewport #grph_'+points[i][2])[0];
            if (obj) bringToFront(obj,"viewport");
        }
    }
    _sketchHelper.bringPointsToFrontC = function () {
		for (var i in points){
			obj=$('#canvasBackground #grph_'+points[i][2])[0];
            if (obj) bringToFront(obj,"canvasBackground");
			//bringToFront(obj,"viewport");
		}
	}
	_sketchHelper.setViewport = function (v){
        viewport=v;
    }
	function bringToFront(object,referenceId){
		var tmp = document.createElement("circle");
		document.getElementById(referenceId).appendChild(tmp);
		document.getElementById(referenceId).replaceChild(object,tmp);
	}
	
    function XmlToSvgStr(node) {
        return $('<div>').append($(node).clone()).html();
    }

    _sketchHelper.setOffset = function (noffset) {
        origOffSet = noffset;
    };
	_sketchHelper.drawArcCentre = function (x1, y1, x2, y2, shape, sp){
		return drawShape(x1, y1, x2, y2, shape, sp);
	}
	_sketchHelper.addShape = function (elemStr, sketchpad, setting, anim,animtime) {
		var wsId = parseInt(elemStr.replace(/(.*id=")([a-zA-Z_]*)(\d*)(".*)/g, "$3"));
        //<line id="grph_7" style="cursor:default" len_node="len_7" pt_lbls="grph_8 grph_9"/>
		var isId = elemStr.replace(/(.*id=")([a-zA-Z_]*)(\d*)(".*)/g, "$2");
        _nodeNum++;
        _nodeNum = Math.max(_nodeNum, wsId);
		sketchpad.add(elemStr);
        var newNode = sketchpad.getElementById(isId+wsId);
		sketchpad.change(newNode, setting);
		if (anim) $(newNode).animate(anim, animtime);
        var svgnode = mySvg.parseNode(newNode);
        if (svgnode.getnodeName() == 'circle') {
            points[drawNodes.length] = new Array();
            points[drawNodes.length] = [svgnode.getCx(), svgnode.getCy(), wsId];
        }
        else if (svgnode.getnodeName() == 'text') {
            if ($(newNode).attr('rel') == 'label') {
                var labelCoor = ($(newNode).attr('for_pt')).split(" ");
                labelPoints[labelPoints.length] = [parseFloat(labelCoor[0]), parseFloat(labelCoor[1]), wsId, wsId];
            }
        }
		else if (svgnode.getnodeName() == 'path') {
			//var cID=svgnode.getCenterId();
			//var cNode = sketchpad.getElementById(cID);
			//sketchpad.change(cNode, {r:3, fill:'#fff'});
            if (animtime>0){
                sketchpad.add('<line id="compassradius"/>');
                sketchpad.change(document.getElementById('compassradius'),{'fill':"none",'stroke':inkColor,'stroke-width':"2",'stroke-dasharray':"2,2", 'stroke-opacity':"1"});
            }
			//$(cNode).animate({svgR:3, svgFill:'#fff'}, animtime);
		}
        drawNodes[drawNodes.length] = newNode;
		_sketchHelper.bringPointsToFrontC();
        return newNode;
    };
    _sketchHelper.addNewShape = function (elemStr, sketchpad, setting) {
        //add shape with from svg string
        var wsId = parseInt(elemStr.replace(/(.*id=")([a-zA-Z_]*)(\d*)(".*)/g, "$3"));
        //<line id="grph_7" style="cursor:default" len_node="len_7" pt_lbls="grph_8 grph_9"/>
        var isId = elemStr.replace(/(.*id=")([a-zA-Z_]*)(\d*)(".*)/g, "$2");
        _nodeNum++;
        _nodeNum = Math.max(_nodeNum, wsId);
        sketchpad.add(geometry.getViewport(),elemStr);
        var newNode = sketchpad.getElementById(isId+wsId);
        sketchpad.change(newNode, setting);
        var svgnode = mySvg.parseNode(newNode);
        if (svgnode.getnodeName() == 'circle') {
            var c={X:svgnode.getCx(),Y:svgnode.getCy()}
            points[drawNodes.length] = new Array();
            points[drawNodes.length] = [c.X, c.Y, wsId];
            var labelNum=$(newNode).attr('pt_lbls').split('_')[1]*1;
            var labelNode=textLblArea(c, _nodeNum, labelNum, sketchpad,$(newNode).attr('pname'));
        }
        else if (svgnode.getnodeName() == 'text') {
            if ($(newNode).attr('rel') == 'label') {
                var labelCoor = ($(newNode).attr('for_pt')).split(" ");
                labelPoints[labelPoints.length] = [parseFloat(labelCoor[0]), parseFloat(labelCoor[1]), wsId, wsId];
            }
        }
        
        drawNodes[drawNodes.length] = newNode;
        //_history.push({ 'action': 1, 'node': [newNode] });
        _sketchHelper.bringPointsToFrontV();
        return newNode;
    };

    function setOffSet() {
        offset = origOffSet.offset();
        offset.left -= document.documentElement.scrollLeft || document.body.scrollLeft;
        offset.top -= document.documentElement.scrollTop || document.body.scrollTop;
    }

    /* Remember where we started */
    _sketchHelper.startDrag = function (event, svg, shape) {
        var shape = shape;closeAllTooltips();
        //console.log(shape);
        var sketchpad = svg;//console.log(event.target.id);
		if (geometryTools.compass.mobility.translate && (event.target.id=="compass" || event.target.id=="compasspt"  || event.target.id=="compasspn") ){
			compassState="move";//console.log(compassState);
		}else if (geometryTools.compass.mobility.rotate && event.target.id=="pHead"){
			compassState="rotate";//console.log(compassState);
		}else if (geometryTools.compass.mobility.extend && event.target.id=="pBody"){
			compassState="extend";
			var settings = { fill: inkColor, stroke: inkColor, strokeWidth: '1', strokeDashArray: '2,2'};
            arcConn = sketchpad.line(0, 0, 0, 0, settings);
		}else if (geometryTools.compass.mobility.draw && event.target.id=="pTip"){
			compassState="draw";//console.log(compassState);
		}else{
			//if (event.target.id=="compassbob") geometry.flipCompass();
			compassState="off";//console.log(compassState);
			outline = null;
		}
		if (compassState!='off'){
			var compassElem = sketchpad.getElementById('compass');
	        //var compassObj = mySvg.parseNode(compassElem);
	        //arcCtr = compassObj.getRotationPoint();
            bringToFront(compassElem,"canvasBackground");
		}
        setOffSet();
        if (shape != 'polyline') {
            //start = { X: event.clientX - offset.left, Y: event.clientY - offset.top };
            start = { X: event.clientX, Y: event.clientY};
            if (shape == 'arc') {
                if (arcCtr != null) { // when you draw the center first and than again start dragging
                    rad = calcDist(arcCtr.X, arcCtr.Y, start.X, start.Y);
                } 
                else { //else when you directly started dragging than one line connecter and center will be drawn
                    
                }
            }
        }
        $('input[type="text"]._point_lbl').css('display', 'none');
        startNode = null;
        currNode = [];
        removeSlector(sketchpad);
        event.preventDefault();
        return arcConn;
    };

    /*@whenever starting from a node this function is called*/
    _sketchHelper.startDragCon = function (event, svg, shape, node) {
        var sketchpad = svg;closeAllTooltips();
        var svgnode = mySvg.parseNode(node);
        currNode = [];
        removeSlector(sketchpad);
        if (svgnode.getnodeName() != 'circle' && shape == 'polyline') {
            return;
        }
        setOffSet();
        if (svgnode.getnodeName() == 'circle') {
            start = { X: svgnode.getCx(), Y: svgnode.getCy()};
			//console.log('point');
			if (shape == 'select'){
				$('#geoshape').val('line');
			}
        } 
        else {
            //start = { X: event.clientX - offset.left, Y: event.clientY - offset.top};
            start = { X: event.clientX, Y: event.clientY};
        }
		if (geometryTools.compass.mobility.translate && (event.target.id=="compass" || event.target.id=="compasspt"  || event.target.id=="compasspn") ){
			compassState="move";//console.log(compassState);
		}else if (geometryTools.compass.mobility.rotate && event.target.id=="pHead"){
			compassState="rotate";//console.log(compassState);
		}else if (geometryTools.compass.mobility.extend && event.target.id=="pBody"){
			compassState="extend";
			var settings = { fill: inkColor, stroke: inkColor, strokeWidth: '1', strokeDashArray: '2,2' };
            arcConn = sketchpad.line(0, 0, 0, 0, settings);
		}else if (geometryTools.compass.mobility.draw && event.target.id=="pTip"){
			compassState="draw";//console.log(compassState);
			outline = null;
		}else{
			compassState="off";
		}
		if (compassState!='off'){
			var compassElem = sketchpad.getElementById('compass');
            bringToFront(compassElem,"canvasBackground");
		}
        $('input[type="text"]._point_lbl').css('display', 'none');
        startNode = node;
        event.preventDefault();
        return arcConn;
    };

    /* Provide feedback as we drag 	*/
    _sketchHelper.dragging = function (event, svg, oshape) {
        var shape = oshape;
        if (!start) {
            return;
        }
        //console.log(shape);
        var sketchpad = svg;
        var showTxt = false;
        //var curC = {X: event.clientX - offset.left,Y: event.clientY - offset.top};      
        var curC = {X: event.clientX,Y: event.clientY};
        //settings for outline
        var outSet = { fill: 'none', stroke: inkColor, strokeWidth: 2, strokeDashArray: '2,2' };
        //initialize the outline
        switch (shape) {
            case 'rect':
                {
                    if (!outline) {
                        outline = sketchpad.rect(0, 0, 0, 0, outSet);
                    } break;
                }
            case 'polyline':
            case 'line':
                {
                    if (!outline) {
                        outline = sketchpad.line(viewport,0, 0, 0, 0, outSet);
                    } break;
                }
            case 'circle':
                {
                    if (!outline) {
                        outline = sketchpad.circle(viewport,0, 0, 0, outSet);
                    } break;
                }
            case 'arc':
                {   //console.log(shape,arcCtr,rad);
                    if (arcCtr && start != arcCtr) {
                        arcCalculator(sketchpad, curC, outSet);
                    }
                    break;
                }
            case 'select':break;
        }
		//console.log("after");
        //console.log(outline);
		//console.log(shape);
        //modify the outline with motion
        switch (shape) {
            case 'line':
                showTxt = true;
            case 'polyline':
                sketchpad.change(outline, { x1: start.X, y1: start.Y, x2: curC.X, y2: curC.Y});
                break;
            case 'select':
                {
                    if (startNode) {
						//console.log('yes:'+$(startNode).attr("id"));
                        var startNodeObj = mySvg.parseNode(startNode);
						if ($(startNode).attr("id") == 'ruler') {
                            var newPos = {
                                X: (startNodeObj.getX() + (curC.X - start.X)),
                                //X: ((curC.X)),
                                Y: (startNodeObj.getY() + (curC.Y - start.Y))
                                //Y: ((curC.Y))
                            };
							var an=startNodeObj.getRotationAngle();
							sketchpad.change(startNode, { transform: 'rotate(' + (an) + ' ' + newPos.X + ' ' + newPos.Y + ')' });
                            sketchpad.change(startNode, { x: newPos.X, y: newPos.Y });
							
							var rotateRule = sketchpad.getElementById('rulerHandle');
							sketchpad.change(rotateRule, { transform: 'rotate(' + (an) + ' ' + newPos.X + ' ' + newPos.Y + ')' });
                            var rotateRuleObj = mySvg.parseNode(rotateRule);
                            newPos = {
                                X: (rotateRuleObj.getX() + (curC.X - start.X)),
                                Y: (rotateRuleObj.getY() + (curC.Y - start.Y))
                            };
                            sketchpad.change(rotateRule, { x: newPos.X, y: newPos.Y });

                            start.X = curC.X;
                            start.Y = curC.Y;
                            break;
                        }
						else if ($(startNode).attr("id") == 'rulerHandle') {
							var rulerElem = sketchpad.getElementById('ruler');
                            var rulerObj = mySvg.parseNode(rulerElem);
                            var rotationC = {X:rulerObj.getX(),Y:rulerObj.getY()};
                            var angleRotateNew = normAngle(curC, rotationC);
                            var angleRotateOld = normAngle(start, rotationC);
                            var angleRotate = angleRotateNew - angleRotateOld;
							
							var prevAngleVal = rulerObj.getRotationAngle();
                            var rotationFinal = prevAngleVal + angleRotate;
                            rotationFinal = ((rotationFinal > 180) ? (-360 + rotationFinal) : (((rotationFinal < -180) ? (360 + rotationFinal) : rotationFinal)));
							
							sketchpad.change(rulerElem, { transform: 'rotate(' + (rotationFinal) + ' ' + rotationC.X + ' ' + rotationC.Y + ')' });
							sketchpad.change(sketchpad.getElementById('rulerHandle'), { transform: 'rotate(' + (rotationFinal) + ' ' + rotationC.X + ' ' + rotationC.Y + ')' });
							
                            start.X = curC.X;
                            start.Y = curC.Y;
                            break;
						}
                        else if ($(startNode).attr("id") == 'protractor') {
                            var rotateElemL = sketchpad.getElementById('protractorL');
                            var rotateElemLObj = mySvg.parseNode(rotateElemL);
							var rotateElemR = sketchpad.getElementById('protractorR');
                            var rotateElemRObj = mySvg.parseNode(rotateElemR);
							var currRotation = rotateElemLObj.getRotationInfo();
							var difPos = {X: (curC.X - start.X), Y: (curC.Y - start.Y)};
							if (currRotation){
								var pCentre={X:currRotation.X + (curC.X - start.X),Y:currRotation.Y + (curC.Y - start.Y)};
	                        	var i = nearExisting(pCentre, labelPoints, 5);
								if (i == -1) {
									
		                        } 
                                else {
									pCentre={ X: labelPoints[i][0], Y: labelPoints[i][1] };
									difPos={ X: pCentre.X-currRotation.X, Y: pCentre.Y-currRotation.Y};
		                        }
							}
                            var newPos = {
								X: (startNodeObj.getX() + difPos.X),
                                Y: (startNodeObj.getY() + difPos.Y)
                            };
							sketchpad.change(startNode, { x: newPos.X, y: newPos.Y });
                            newPosL = {
                                X: (rotateElemLObj.getX() + difPos.X),
                                Y: (rotateElemLObj.getY() + difPos.Y)
                            };
                            sketchpad.change(rotateElemL, { x: newPosL.X, y: newPosL.Y });
                            newPosR = {
                                X: (rotateElemRObj.getX() + difPos.X),//(curC.X - start.X)),
                                Y: (rotateElemRObj.getY() + difPos.Y)//(curC.Y - start.Y))
                            };
                            sketchpad.change(rotateElemR, { x: newPosR.X, y: newPosR.Y });

                            var currRotation = rotateElemLObj.getRotationInfo();
                            var currRotationR = startNodeObj.getRotationPoint();
							sketchpad.change(rotateElemR, { transform: 'rotate(' + rotateElemRObj.getRotationAngle() + ' ' + (currRotationR.X ) + ' ' + (currRotationR.Y) + ')' });
                            if (currRotation) {
                                sketchpad.change(startNode, { transform: 'rotate(' + currRotation.angle + ' ' + (currRotation.X + difPos.X) + ' ' + (currRotation.Y + difPos.Y) + ')' });
                                sketchpad.change(rotateElemL, { transform: 'rotate(' + currRotation.angle + ' ' + (currRotation.X + difPos.X) + ' ' + (currRotation.Y + difPos.Y) + ')' });
                            }
                            start.X = curC.X;
                            start.Y = curC.Y;
                            break;
                        } 
						else if ($(startNode).attr("id") == 'protractorR' || $(startNode).attr("id") == 'protractorL') {
                            var protractorElem = sketchpad.getElementById('protractor');
                            var protractorObj = mySvg.parseNode(protractorElem);
                            var rotationC = protractorObj.getRotationPoint();
                            var angleRotateNew = normAngle(curC, rotationC);
                            var angleRotateOld = normAngle(start, rotationC);
                            var angleRotate = angleRotateNew - angleRotateOld;

                            var prevAngleVal = protractorObj.getRotationAngle();
                            var rotationFinal = prevAngleVal + angleRotate;
                            rotationFinal = ((rotationFinal > 180) ? (-360 + rotationFinal) : (((rotationFinal < -180) ? (360 + rotationFinal) : rotationFinal)));
							rotationFinalR = ((rotationFinal +173 > 180) ? (-360 + rotationFinal +173 ) : (((rotationFinal +173  < -180) ? (360 + rotationFinal +173 ) : rotationFinal +173 )));
                            sketchpad.change(protractorElem, { transform: 'rotate(' + (rotationFinal) + ' ' + rotationC.X + ' ' + rotationC.Y + ')' });
							var rotateElem = sketchpad.getElementById('protractorL');
                            sketchpad.change(rotateElem, { transform: 'rotate(' + (rotationFinal) + ' ' + rotationC.X + ' ' + rotationC.Y + ')' });
							var rotateElem = sketchpad.getElementById('protractorR');
                            sketchpad.change(rotateElem, { transform: 'rotate(' + (rotationFinalR) + ' ' + rotationC.X + ' ' + rotationC.Y + ')' });
                            start.X = curC.X;
                            start.Y = curC.Y;
                            break;
                        }
						else if ($(startNode).attr("id") == 'compass') {
							//console.log(event.target.id+"   "+curC.X+"   "+start.X);
                            if (compassState=="move"){
								var newPos = {
	                                X: (startNodeObj.getX() + (curC.X - start.X)),
	                                Y: (startNodeObj.getY() + (curC.Y - start.Y))
	                            };
								var newA = {
									angle:startNodeObj.getRotationAngle(),
									X: startNodeObj.getIniX(),
									Y: startNodeObj.getIniY()
								};
								var compassElem = sketchpad.getElementById('compass');
	                            var compassObj = mySvg.parseNode(compassElem);
								var flipped=geometry.isCompassFlipped();
                                sketchpad.change(startNode, { label: newPos.X+" "+ newPos.Y });
                                sketchpad.change(startNode, { transform: 'translate(' + newPos.X + ', ' + newPos.Y + '),rotate(' +newA.angle+' '+newA.X+' '+newA.Y+'),scale('+flipped+', 1)' });
                                arcCtr = compassObj.getRotationPoint();
							}
							else if (compassState=="extend"){
								var penHandEl = document.getElementById('compasspnH');
								var penHand = mySvg.parseNode(penHandEl);
								var pointHand = mySvg.parseNode(sketchpad.getElementById('compasspt'));
								var bobCentre = {
									X: pointHand.getX()+pointHand.getWidth()+startNodeObj.getX()+pointHand.getHeight()*Math.sin(startNodeObj.getRotationAngle()*Math.PI/180),
									Y: startNodeObj.getIniY()+startNodeObj.getY()-pointHand.getHeight()*Math.cos(startNodeObj.getRotationAngle()*Math.PI/180)
								};
								//console.log(curC.X+" "+(bobCentre.X-startNodeObj.getX()));
                                var compassElem = sketchpad.getElementById('compass');
                                var compassObj = mySvg.parseNode(compassElem);
                                arcCtr = compassObj.getRotationPoint();
								var angleRotateNew = normAngle(curC, bobCentre);
	                            var angleRotateOld = normAngle(start, bobCentre);
	                            var angleRotate = angleRotateNew - angleRotateOld;
								var prevAngleVal = penHand.getRotationAngle();
								var flipped=geometry.isCompassFlipped();
	                            var rotationFinal = prevAngleVal + flipped*angleRotate;
	                            rotationFinal = ((rotationFinal > 180) ? (-360 + rotationFinal) : (((rotationFinal < -180) ? (360 + rotationFinal) : rotationFinal)));
								var angD=(-startNodeObj.getRotationAngle()+Math.abs(rotationFinal)/2)*Math.PI/180;
								if (flipped>0){
									if (rotationFinal<=0 && rotationFinal>=-120){
										sketchpad.change(penHandEl, { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
										sketchpad.change(document.getElementById('compassbob'), { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal/2) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
										angD=(-startNodeObj.getRotationAngle()+flipped*Math.abs(rotationFinal)/2)*Math.PI/180;
									}
								}
								else {
									if (rotationFinal<=0 && rotationFinal>=-120){
										sketchpad.change(penHandEl, { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
										sketchpad.change(document.getElementById('compassbob'), { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal/2) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
										angD=(-startNodeObj.getRotationAngle()+flipped*Math.abs(rotationFinal)/2)*Math.PI/180;
									}
								}
								rad = Math.abs(2*pointHand.getHeight()*Math.sin(rotationFinal*Math.PI/360));
								//console.log(startNodeObj.getRotationAngle()+"  ----  "+(rotationFinal/2)+"  ---  "+angD);
								var endP = {
									X: arcCtr.X + flipped*rad*Math.cos(angD),
									Y: arcCtr.Y - flipped*rad*Math.sin(angD)
								}
		                        sketchpad.change(arcConn, { x1: arcCtr.X, y1: arcCtr.Y, x2: endP.X, y2: endP.Y });
								var len = rad/scale.w;len = Math.round(len * 10) / 10;
					            var lenPanned=screenToWindow({X:(endP.X + arcCtr.X) / 2,Y:(endP.Y + arcCtr.Y + 30) / 2});
					            if (lineLen) {
					                sketchpad.change(lineLen, { x: lenPanned.X, y: lenPanned.Y });
					                $(lineLen).html(len);
					            } else {
					                lineLen = sketchpad.text(viewport, lenPanned.X, lenPanned.Y, '' + len, {fill: inkColor});
					                $(lineLen).attr('rel', 'length');
					            }
								
							}
							else if (compassState=="rotate" ){
								var compassElem = sketchpad.getElementById('compass');
	                            var compassObj = mySvg.parseNode(compassElem);
	                            var rotationC = compassObj.getRotationPoint();
	                            var angleRotateNew = normAngle(curC, rotationC);
	                            var angleRotateOld = normAngle(start, rotationC);
	                            var angleRotate = angleRotateNew - angleRotateOld;
								var flipped=geometry.isCompassFlipped();
	                            var prevAngleVal = compassObj.getRotationAngle();
	                            var rotationFinal = (prevAngleVal + angleRotate);
	                            rotationFinal = ((rotationFinal > 180) ? (-360 + rotationFinal) : (((rotationFinal < -180) ? (360 + rotationFinal) : rotationFinal)));
								var newP={X:compassObj.getX(), Y:compassObj.getY()};
	                            sketchpad.change(compassElem, { transform: 'translate(' + newP.X + ', ' + newP.Y + '),rotate(' + (rotationFinal) + ' ' + (rotationC.X-newP.X) + ' ' + (rotationC.Y-newP.Y) + '),scale('+flipped+', 1)' });
							}
							else if (compassState=="draw"){
								var compassElem = sketchpad.getElementById('compass');
                            	var compassObj = mySvg.parseNode(compassElem);
								var penHand = mySvg.parseNode(document.getElementById('compasspnH'));
								var pointHand = mySvg.parseNode(sketchpad.getElementById('compasspt'));
								var flipped=geometry.isCompassFlipped();
								var pAngle=0;
								if (!arcCtr || !rad){
									arcCtr = compassObj.getRotationPoint();
									
									rad = Math.abs(2*pointHand.getHeight()*Math.sin(penHand.getRotationAngle()*Math.PI/360));
									pAngle = penHand.getRotationAngle()/2;
								}								
                                var bobCentre = {
                                    X: pointHand.getX()+pointHand.getWidth()+startNodeObj.getX()+pointHand.getHeight()*Math.sin(startNodeObj.getRotationAngle()*Math.PI/180),
                                    Y: startNodeObj.getIniY()+startNodeObj.getY()-pointHand.getHeight()*Math.cos(startNodeObj.getRotationAngle()*Math.PI/180)
                                };
                                var angleRotateNew = normAngle(curC, bobCentre);
                                var angleRotateOld = normAngle(start, bobCentre);
                                var angleRotate = angleRotateNew - angleRotateOld;
                                var prevAngleVal = penHand.getRotationAngle();
                                var rotationFinal = prevAngleVal + flipped*angleRotate;
                                rotationFinal = ((rotationFinal > 180) ? (-360 + rotationFinal) : (((rotationFinal < -180) ? (360 + rotationFinal) : rotationFinal)));
                                var angD=(-startNodeObj.getRotationAngle()+Math.abs(rotationFinal)/2)*Math.PI/180;
                                if (flipped>0){
                                    if (rotationFinal<=0 && rotationFinal>=-120){
                                        sketchpad.change(penHandEl, { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
                                        sketchpad.change(document.getElementById('compassbob'), { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal/2) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
                                        angD=(-startNodeObj.getRotationAngle()+flipped*Math.abs(rotationFinal)/2)*Math.PI/180;
                                    }
                                }
                                else {
                                    if (rotationFinal<=0 && rotationFinal>=-120){
                                        sketchpad.change(penHandEl, { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
                                        sketchpad.change(document.getElementById('compassbob'), { transform: 'translate(' + 0 + ', ' + 0 + '),rotate(' + (rotationFinal/2) + ' ' + (pointHand.getX()+pointHand.getWidth()) + ' ' + (pointHand.getY()) + ')' });
                                        angD=(-startNodeObj.getRotationAngle()+flipped*Math.abs(rotationFinal)/2)*Math.PI/180;
                                    }
                                }
                                var radTip = Math.abs(2*pointHand.getHeight()*Math.sin(rotationFinal*Math.PI/360));
                                startTip = {
                                    X: arcCtr.X + flipped*rad*Math.cos(angD),
                                    Y: arcCtr.Y - flipped*rad*Math.sin(angD),
                                };
								var r1 = calcDist(start.X,start.Y,arcCtr.X,arcCtr.Y);
								if (Math.abs(r1-rad)>0.5){
									if (Math.abs(start.X-arcCtr.X)<0.3) {
										start.Y = (start.Y>arcCtr.Y)?(arcCtr.Y+rad):(arcCtr.Y-rad);
									}
                                    else {
										var m=(start.Y-arcCtr.Y)/(start.X-arcCtr.X);
										st1X = arcCtr.X + rad/Math.sqrt(1+m*m);st1Y = m*(st1X - arcCtr.X) + arcCtr.Y;
										
										st2X = arcCtr.X - rad/Math.sqrt(1+m*m);st2Y = m*(st2X - arcCtr.X) + arcCtr.Y;
										if (calcDist(start.X,start.Y,st1X,st1Y)>rad){
											start.X=st2X;start.Y=st2Y;
										}
										else {
											start.X=st1X;start.Y=st1Y;
										}
									}
								}
								var tol = 8;var translatedCentre=screenToWindow(arcCtr);
								var stP = nearExisting(translatedCentre, points, tol);
								if (stP!=-1) {arcPnt = document.getElementById("grph_"+points[stP][2]);}
								else{
									var drawnNode = drawShape(translatedCentre.X, translatedCentre.Y, 0, 0, "point", sketchpad);
									arcPnt = drawnNode;
									$(drawnNode).attr('withArc','1');
									geometry.addHandler(drawnNode, sketchpad);
								}
								//console.log(arcPnt);
								$('#geoshape').val('arc');
								break;
							}
                            start.X = curC.X;
                            start.Y = curC.Y;
                            break;
                        } 
                    }
					break;
                }

            case 'rect': sketchpad.change(outline, { x: Math.min(curC.X, start.X),
                    y: Math.min(curC.Y, start.Y),
                    width: Math.abs(curC.X - start.X),
                    height: Math.abs(curC.Y - start.Y)
                }); 
                break;
            case 'circle': sketchpad.change(outline, { cx: ((curC.X + start.X) / 2),
                    cy: ((curC.Y + start.Y) / 2),
                    r: Math.abs((curC.X - start.X) / 2)
                }); 
                break;
            case 'arc':
                {
                    break;
                }
        }
		if (showTxt) {
            var len = calcDist((start.X / scale.w), (start.Y / scale.w), (curC.X / scale.w), (curC.Y / scale.w));
            //till 1 decimal point
            len = Math.round(len * 10) / 10;
            if (lineLen) {
                sketchpad.change(lineLen, { x: ((curC.X + start.X) / 2), y: ((curC.Y + start.Y + 30) / 2) });
                $(lineLen).html(len);
            } else {
                lineLen = sketchpad.text(viewport,(curC.X + start.X) / 2, (curC.Y + start.Y + 30) / 2, '' + len, {fill: inkColor});
                $(lineLen).attr('rel', 'length');
            }
        }
        event.preventDefault();
        return outline;
    };
    
    function normAngle(ePt, sPt) {
    	//normalize angle -360 to 360
        //var angle = Math.atan2((ePt.Y - sPt.Y) , (ePt.X - sPt.X)) * (180 / Math.PI);
        if (ePt.X == sPt.X) { //check if perpendicular [per new format]
            var angle = (ePt.Y - sPt.Y <= 0)?(-180 + 90):90; //degrees
            return angle;
        } 
        var angle = Math.atan((ePt.Y - sPt.Y) / (ePt.X - sPt.X)) * (180 / Math.PI);
        if (ePt.Y - sPt.Y <= 0 && ePt.X - sPt.X < 0) { //check if in III Quad [per new format]
            angle = -180 + angle; //degrees

        } else if (ePt.Y - sPt.Y > 0 && ePt.X - sPt.X < 0) {
            angle = 180 + angle;
        }
        return angle;
    }

    /** the logic for arc drawing
     *  make the outline in shape of required arc
     *  convert the outline to solid arc on completion 
     **/
    function arcCalculator(sketchpad, curC, outSet) {
        var angle1 = normAngle(start, arcCtr);
        var angle2 = normAngle(curC, arcCtr);
        var minorAngle;
        var arcangle = angle1 - angle2;
        var path = sketchpad.createPath();
		var compassElem = sketchpad.getElementById('compass');
        var compassObj = mySvg.parseNode(compassElem);
        var rotationC = compassObj.getRotationPoint();
        var angleRotateNew = normAngle(curC, rotationC);
        if (!lastCurr) lastCurr=start;
        var angleRotateOld = normAngle(lastCurr, rotationC);
        lastCurr=curC;
        //angleRotateNew = ((angleRotateNew > 180) ? (-360 + angleRotateNew) : (((angleRotateNew < -180) ? (360 + angleRotateNew) : angleRotateNew)));
        //angleRotateOld = ((angleRotateOld > 180) ? (-360 + angleRotateOld) : (((angleRotateOld < -180) ? (360 + angleRotateOld) : angleRotateOld)));
        var angleRotate = angleRotateNew - angleRotateOld;
		var penHandEl = document.getElementById('compasspnH');
		var penHand = mySvg.parseNode(penHandEl);
		var flipped=geometry.isCompassFlipped();
		
		var prevAngleVal = compassObj.getRotationAngle();
		var rotationFinal = (prevAngleVal + angleRotate);
        //var rotationFinal = angleRotateNew-flipped*penHand.getRotationAngle()/2;console.log(rotationFinal);
        //console.log(compObjRotAngle+"  "+rotationFinal+"  "+angleRotateNew+"  "+angleRotateOld+"  "+angleRotate+"  "+start.X+","+start.Y+"   "+arcCtr.X+","+arcCtr.Y+"   "+rotationC.X+","+rotationC.Y);
        rotationFinal = ((rotationFinal > 180) ? (-360 + rotationFinal) : (((rotationFinal < -180) ? (360 + rotationFinal) : rotationFinal)));//console.log(rotationFinal);
        var newP={X:compassObj.getX(), Y:compassObj.getY()};
        sketchpad.change(compassElem, { transform: 'translate(' + newP.X + ', ' + newP.Y + '),rotate(' + (rotationFinal) + ' ' + (rotationC.X-newP.X) + ' ' + (rotationC.Y-newP.Y) + '),scale('+flipped+', 1)' });

		if (!rad) rad=0;
        
		if (prevArcA === null) {
            prevArcA = arcangle;
        }
        if (Math.abs(prevArcA-arcangle)>200) {
        	/**the arc is retraced back and redrawn from start point**/
            minorAngle = arcangle;
            arcangle += (prevArcA>0 ? 1 : -1)*360;
            if(Math.abs(arcangle)>360)
                arcangle = minorAngle;
        }
        var criticalArcAngle = 340, autoCompleteCircle = false;
        if((prevArcA>criticalArcAngle || prevArcA<-criticalArcAngle) && Math.abs(prevArcA-arcangle)>320) {
            arcangle = (prevArcA>=0 ? 1 : -1)*359.99;
            autoCompleteCircle = true;
        }
        prevArcA = arcangle;
        var sweepFlag = false; //anti clock - clock - wise [check w3c standards]

        var majArc = false;

        if (arcangle < 0) {
            sweepFlag = true;
        }
        if (Math.abs(arcangle) > 180) {
            majArc = true;
        }
        var angle1Tip = normAngle(startTip, arcCtr);
        var angle2Tip = angle1Tip - arcangle;
        var endPnt = {
            X: arcCtr.X + rad * Math.cos(angle2Tip * (Math.PI / 180)),
            Y: arcCtr.Y + rad * Math.sin(angle2Tip * (Math.PI / 180))
        };
        var startPanned=screenToWindow(startTip);
		var endPanned=screenToWindow(endPnt);
        if (calcDist(startTip.X,startTip.Y,arcCtr.X,arcCtr.Y)>=5 && rad>=5){
            if (!outline) {
                outline = sketchpad.path(viewport,path.move(startPanned.X, startPanned.Y).arc(rad, rad, arcangle, majArc, sweepFlag, endPanned.X, endPanned.Y), outSet);
            }
            else {
                $(outline).remove();
                outline = null;
                outline = sketchpad.path(viewport,path.move(startPanned.X, startPanned.Y).arc(rad, rad, arcangle, majArc, sweepFlag, endPanned.X, endPanned.Y), outSet);
            }
        }
        else {
            $(outline).remove();
            outline = null;
        }
        if (arcConn) sketchpad.change(arcConn, { x1: arcCtr.X, y1: arcCtr.Y, x2: endPnt.X, y2: endPnt.Y});
        // return;
        if(autoCompleteCircle) {
            prevArcA = null;
            $('#compass').trigger('mouseup');
            $('#pTip').trigger('mouseout');
            $('._hide-compass').trigger('click');
        }
    }

    /* Draw where we finish */
    _sketchHelper.endDrag = function (event, svg, oshape) {
        var shape = oshape;
        var tol = 15;
        if (!start) {
            return;
        }
        var sketchpad = svg;
        if (shape != 'arc') {
            $(outline).remove();
            outline = null;
        }
        var toDraw = false;
        var drawnNode = null;
        //var endPt = {X: event.clientX - offset.left,Y: event.clientY - offset.top};
        var endPt = {X: event.clientX,Y: event.clientY};
		//console.log(start);
		//console.log(endPt);
        switch (shape) {
            case 'point':
                {
                    toDraw = true; break;
                }
            case 'polyline':
                {
                    var i = nearExisting(panPoints(endPt), points, tol);
                    if (i != -1) {
                        endPt.X = points[i][0];
                        endPt.Y = points[i][1];
                        endNode = drawNodes[i];
                        if (startNode != endNode) {
                            toDraw = true;
                        }
                    }
                    break;
                }
            case 'line': 
            case 'circle':
            case 'rect':
                {
            	//verify if the element drawn is not less than tolerance length/area
                    if ((start.X > (endPt.X + tol) || start.X < (endPt.X - tol)) || (start.Y > (endPt.Y + tol) || start.Y < (endPt.Y - tol))) {
                        toDraw = true;
                    } else {
                        $(lineLen).remove();
                        lineLen = null;
                    } break;
                }
            case 'arc':
                {
					//$('#geoshape').val('select');
					toDraw = true;
                    break;
                }
            case 'select':
                {
					/*if (compassState=='off'){
						var maxXSel = Math.max(start.X, endPt.X);
	                    var minXSel = Math.min(start.X, endPt.X);
	                    var maxYSel = Math.max(start.Y, endPt.Y);
	                    var minYSel = Math.min(start.Y, endPt.Y);
	                    for (var i in drawNodes) {
	                        if (drawNodes[i]) {
	                            var svgNode = mySvg.parseNode(drawNodes[i]);
	                            if (minXSel <= svgNode.getMaxX() && maxXSel >= svgNode.getMinX() && minYSel <= svgNode.getMaxY() && maxYSel >= svgNode.getMinY()) {
	                                drawSelector(drawNodes[i], sketchpad);
	                                currNode.push(drawNodes[i]);
	                            }
	                        }
	                    }
					}*/
                    break;
                }
        }
        if (toDraw) {
            var startPanned=panPoints(start);var endPanned=panPoints(endPt);
            //drawnNode = drawShape(startPanned.X, startPanned.Y, endPanned.X, endPanned.Y, shape, sketchpad);
            drawnNode = drawShape(start.X, start.Y, endPt.X, endPt.Y, shape, sketchpad);
        }
        if (oshape == 'arc' && shape == 'point') {
            //arcPnt = drawnNode;
        } else {
            start = null;
        }
		if (arcConn) $(arcConn).remove();
		arcConn = null;
		if (lineLen) $(lineLen).remove();
        lineLen = null;
		if (lastCurr) lastCurr=null;
        startNode = null;
        endNode = null;
        event.preventDefault();
        return drawnNode;
    };

    /*@connector Line*/
    _sketchHelper.endDragCon = function (event, svg, shape, node) {
        var nNode = null;
        var sketchpad = svg;
        /*if (!start && shape == 'select' && compassState=='off') {
            currNode.push(node);
            //drawSelector(node, sketchpad);
            return;
        }*/
        if (!start) {
            return;
        }
        if (shape != 'arc') {
            $(outline).remove();
            outline = null;
        }
        if (startNode != node || shape != 'select') {
            //drawShape(start.X, start.Y, event.clientX - offset.left, event.clientY - offset.top, sketchpad);
            endNode = node;var startPanned=panPoints(start);
            var svgNode = mySvg.parseNode(node);
            if (svgNode.getnodeName() == 'circle' && shape != 'arc' && shape != 'select' && shape!='line') {
                nNode = drawShape(startPanned.X, startPanned.Y, svgNode.getCx(), svgNode.getCy(), shape, sketchpad);
            }
            else {
                nNode = _sketchHelper.endDrag(event, sketchpad, shape);
            }
        }
        else if (startNode == node) {
            /*if ($(startNode).attr("id") != 'protractor' && $(startNode).attr("id") != 'protractorH' && $(startNode).attr("id") != 'compass' ) {
                currNode.push(node);
                drawSelector(node, sketchpad);
            }*/
        }
        if (shape != 'arc') {
            start = null;
        }
		if (arcConn) $(arcConn).remove();
			arcConn = null;
		if (lineLen) $(lineLen).remove();
        lineLen = null;
		if (lastCurr) lastCurr=null;
		if (compassState=='move'){
			var compassElem = sketchpad.getElementById('compass');
            var compassObj = mySvg.parseNode(compassElem);
			if (!arcCtr)	arcCtr = compassObj.getRotationPoint();
			var i = nearExisting(screenToWindow(arcCtr), points, 10);
			var oldCentre = arcCtr;
			if (i!=-1){
                arcCtr = windowToScreen({X:points[i][0],Y:points[i][1]});
				//arcCtr.X=points[i][0]+vpOffset.left;
				//arcCtr.Y=points[i][1]+vpOffset.top;
				var newA = {
					angle:compassObj.getRotationAngle(),
					X: compassObj.getIniX(),
					Y: compassObj.getIniY()
				};
				var newPos = {
	                X: (arcCtr.X - compassObj.getIniX()),
	                Y: (arcCtr.Y - compassObj.getIniY())
	            };
				sketchpad.change(startNode, { label: newPos.X+" "+ newPos.Y });
				var flipped=geometry.isCompassFlipped();
				sketchpad.change(startNode, { transform: 'translate(' + newPos.X + ', ' + newPos.Y + '),rotate(' +newA.angle+' '+newA.X+' '+newA.Y+'),scale('+flipped+', 1)' });
			}
		}
        else if (compassState=='draw'){
            $('#geoshape').val('select');
        }
        event.preventDefault();
        return nNode;
    };

    _sketchHelper.nodesToStr = function () {
        var data = [];
        for (var i = 0; i < drawNodes.length; i++) {
            data.push($('<div>').append($(drawNodes[i]).clone()).html());
        }
        return data;
    };

    _sketchHelper.clearCanv = function (sketchpad) {
        var deletedNodes = [];
        var duplDrawNodes = drawNodes.slice(animeDrawnCount+1);
        if (drawNodes.length > 0) {
        	removeSlector(sketchpad);
            deletedNodes = deleteNodes(duplDrawNodes, sketchpad, 0);
            _history.push({ 'action': 0, 'node': deletedNodes });
        }
        _redoNodes = [];
    };

    _sketchHelper.reset = function (sketchpad) {
        drawNodes = [];
        sketchpad = null;
        redoNodes = [];
        points = [];
        labelPoints = [];
        _nodeNum = 0;
        currNode = [];
        selectorRect = [];
        origOffSet = null;
        _history = [];
        _redoNodes = [];
    };

    function inCheckList(name) {
        //var list = ['path', 'line', 'polyline'];
        var list = ['path', 'line'];
        return $.inArray(name, list);
    }
    _sketchHelper.checkForCrossed = function (chkNode, sketchPad) {
        //[create/update map for crossed paths]
        var nodeObj = mySvg.parseNode(chkNode);
        var checkNode = inCheckList(nodeObj.getnodeName());
        var newNodesArr = [];
        if(!chkNode){
        	return newNodesArr;
        }
        var tol = 5;
        if (checkNode > -1) { 
            switch (checkNode) {
                case 0:
                    {
                        //node is an arc
                        var arcCtrId = nodeObj.getCenterId();
                        var arcCtrObj = mySvg.parseNode(sketchPad.getElementById(arcCtrId));
                        var arcCtrCoor = { X: arcCtrObj.getCx(), Y: arcCtrObj.getCy() };
                        var arcRad1 = nodeObj.getRadius();
                        for (var i in drawNodes) {
                            var withNode = mySvg.parseNode(drawNodes[i]);
                            var chkWithNode = inCheckList(withNode.getnodeName());
                            if (chkWithNode == 0) {
                                //[currently working on arc-arc intersection] below will be true with list only having path
                                var withCtrId = withNode.getCenterId();
                                var withCtrObj = mySvg.parseNode(sketchPad.getElementById(withCtrId));
                                var withCtrCoor = { X: withCtrObj.getCx(),
                                    Y: withCtrObj.getCy()
                                };
                                var arcRad2 = withNode.getRadius();
                                var d = calcDist(arcCtrCoor.X, arcCtrCoor.Y, withCtrCoor.X, withCtrCoor.Y);
                                if (d < (arcRad1 + arcRad2) && d > (Math.abs(arcRad1 - arcRad2))) {
                                    //[reference]paulbourke.net/geometry/2circle/
                                    var a = ((Math.pow(d, 2) + Math.pow(arcRad1, 2) - Math.pow(arcRad2, 2)) / (2 * d));
                                    var h = Math.sqrt(Math.pow(arcRad1, 2) - Math.pow(a, 2));
                                    var a_d = (a / d);
                                    var h_d = (h / d);
                                    //intersection point on line joining the centers
                                    var p0 = { X: null, Y: null };
                                    p0.X = arcCtrCoor.X + (a_d * (withCtrCoor.X - arcCtrCoor.X));
                                    p0.Y = arcCtrCoor.Y + (a_d * (withCtrCoor.Y - arcCtrCoor.Y));
                                    //the intersection points
                                    var p1 = {
                                        X: null,
                                        Y: null
                                    };
                                    var p2 = {
                                        X: null, Y: null
                                    };
                                    p1.X = p0.X - (h_d * (withCtrCoor.Y - arcCtrCoor.Y));
                                    p1.Y = p0.Y + (h_d * (withCtrCoor.X - arcCtrCoor.X));

                                    p2.X = p0.X + (h_d * (withCtrCoor.Y - arcCtrCoor.Y));
                                    p2.Y = p0.Y - (h_d * (withCtrCoor.X - arcCtrCoor.X));

                                    if (checkIfPointOnArc(p1, nodeObj, arcCtrCoor)) {
                                        if (checkIfPointOnArc(p1, withNode, withCtrCoor)) {
                                            var pointNode = drawIntesectionPoint(p1, chkNode, drawNodes[i], sketchPad, tol);
                                            if (pointNode !== null) {
                                                newNodesArr.push(pointNode);
                                            }
                                        }
                                    }
                                    if (checkIfPointOnArc(p2, nodeObj, arcCtrCoor)) {
                                        if (checkIfPointOnArc(p2, withNode, withCtrCoor)) {
                                            var pointNode = drawIntesectionPoint(p2, chkNode, drawNodes[i], sketchPad, tol);
                                            if (pointNode !== null) {
                                                newNodesArr.push(pointNode);
                                            }
                                        }
                                    }

                                }
                            } else if (chkWithNode == 1) {
                                //arc-line intersection
                                //point generated when an arc is drawn on existing line
                                var intersectPtInfo = GetArcLineIntersection(drawNodes[i], chkNode, sketchPad);
                                for (var k in intersectPtInfo) {
                                    var pointNode = drawIntesectionPoint(intersectPtInfo[k], chkNode, drawNodes[i], sketchPad, tol);
                                    if (pointNode !== null) {
                                        newNodesArr.push(pointNode);
                                    }
                                }
                            }
                        }
                        break;
                    }
                case 1:
                    {
                        //break; //disabled the point generation on arc when a new line is introduced
                        //node is a line						
                        for (var i in drawNodes) {
                            var withNode = mySvg.parseNode(drawNodes[i]);
                            var chkWithNode = inCheckList(withNode.getnodeName());
                            if (chkWithNode == 0) {
                                // line-arc intersection
                                var intersectPtInfo = GetArcLineIntersection(chkNode, drawNodes[i], sketchPad);
                                for (var k in intersectPtInfo) {
                                    var pointNode = drawIntesectionPoint(intersectPtInfo[k], chkNode, drawNodes[i], sketchPad, tol);
                                    if (pointNode !== null) {
                                        newNodesArr.push(pointNode);
                                    }
                                }
                            }else if (chkWithNode == 1){
								// line-line intersection
								var myLineObj = mySvg.parseNode(chkNode);
								var ip = {};
								var p1 = myLineObj.getInitPnt();
								var p2 = myLineObj.getFinalPnt();
								var q1 = withNode.getInitPnt();
								var q2 = withNode.getFinalPnt();
								
								var s1="i";var s2="i";
								if (p1.X-p2.X!=0) s1=(p1.Y-p2.Y)/(p1.X-p2.X);
								if (q1.X-q2.X!=0) s2=(q1.Y-q2.Y)/(q1.X-q2.X);
								
								var diffLine=true;
								if ((p1.X==q1.X && p1.Y==q1.Y) || (p1.X==q2.X && p1.Y==q2.Y)){
									
								}else if (s1!=s2){
									ip.X = ((p1.X*p2.Y-p1.Y*p2.X)*(q1.X - q2.X)-(q1.X*q2.Y-q1.Y*q2.X)*(p1.X - p2.X))/((p1.X-p2.X)*(q1.Y-q2.Y)-(p1.Y-p2.Y)*(q1.X-q2.X));
									ip.Y = ((p1.X*p2.Y-p1.Y*p2.X)*(q1.Y - q2.Y)-(q1.X*q2.Y-q1.Y*q2.X)*(p1.Y - p2.Y))/((p1.X-p2.X)*(q1.Y-q2.Y)-(p1.Y-p2.Y)*(q1.X-q2.X));
									if (ip.X>Math.max(myLineObj.getMinX(),withNode.getMinX()) && ip.X<Math.min(myLineObj.getMaxX(),withNode.getMaxX()) && ip.Y>Math.max(myLineObj.getMinY(),withNode.getMinY()) && ip.Y<Math.min(myLineObj.getMaxY(),withNode.getMaxY())){
										var pointNode = drawIntesectionPoint(ip, chkNode, drawNodes[i], sketchPad, tol);
	                                    if (pointNode !== null) {
	                                        newNodesArr.push(pointNode);
	                                    }
									}
								}
							}
                        }
                        break;
                    }
            }

        }
        return newNodesArr;
    };
    function checkIfPointOnArc(pt, arcObj, arcCtrCoor) {
        //angle of the point pt arcCtr of nodeObj
        var ap1ac = normAngle(pt, arcCtrCoor);
        //arc angle
        var arcAngleVal = arcObj.getAngle();
        //calculate different angles and check if angle lie between them
        //first arc's max and min angles
        if (arcObj.getMinX()<=pt.X && arcObj.getMinY()<=pt.Y && pt.X<=arcObj.getMaxX() && pt.Y<=arcObj.getMaxY()){
            var startPtAngle1 = normAngle(arcObj.getStartPoint(), arcCtrCoor);
            var endPtAngle1 = startPtAngle1 - arcAngleVal;
            var maxAngle1 = Math.max(startPtAngle1, endPtAngle1);
            var minAngle1 = Math.min(startPtAngle1, endPtAngle1);
            if (maxAngle1 > 180) {
                ap1ac = ((ap1ac < minAngle1) ? (360 + ap1ac) : (ap1ac));
                //normalized angle w.r.t. minAngle by 360 degrees
            } else if (minAngle1 < -180) {
                ap1ac = ((ap1ac > maxAngle1) ? (-360 + ap1ac) : (ap1ac));
            }
            if (ap1ac <= maxAngle1 && ap1ac >= minAngle1) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    function GetArcLineIntersection(lineObj, arcObj, sketchPad) {
        //line-arc intersection
        var returnArr = [];
        var myLineObj = mySvg.parseNode(lineObj);
        var myArcObj = mySvg.parseNode(arcObj);
        //intersection point 1
        var ip1 = {};
        //intersection point 2
        var ip2 = {};
        //calculations reference http://paulbourke.net/geometry/sphereline/ without the z co-ordinates
        var p1 = myLineObj.getInitPnt();
        var p2 = myLineObj.getFinalPnt();
        var ctrId = myArcObj.getCenterId();
        var r = myArcObj.getRadius();
        var ctrObj = mySvg.parseNode(sketchPad.getElementById(ctrId));
        var sc = ctrObj.getCenter();
        var dp = {};
        var a, b, c;
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
            if (checkIfPointOnArc(ip1, myArcObj, sc)) {
                returnArr.push(ip1);
            }
        }
        if (mu2 <= 1 && mu2 >= 0) {
            ip2.X = p1.X + mu2 * (p2.X - p1.X);
            ip2.Y = p1.Y + mu2 * (p2.Y - p1.Y);
            if (checkIfPointOnArc(ip2, myArcObj, sc)) {
                returnArr.push(ip2);
            }
        }
        //there can be maximum 2 points of intersection between a line and an arc
        return returnArr;
    }

    function drawIntesectionPoint(p, newAddObj, existingObj, sketchPad, tol) {
        var oldPt = nearExisting(p, points, tol);
        var pointNode = null;
        var crossid = null;
        if (oldPt == -1) {
            pointNode = drawShape(p.X, p.Y, p.X, p.Y, "point", sketchPad, 'intersection');
            //newNodesArr.push(pointNode);
            crossid = $(pointNode).attr('id');
            updateCrossPt(existingObj, crossid);
        }
        else {
            crossid = 'grph_' + points[oldPt][2];
        }
        updateCrossPt(newAddObj, crossid);
        return pointNode;
    }

    function updateCrossPt(node, pointId) {
        var prevId = ($(node).attr('c_pt') == undefined) ? '' : $(node).attr('c_pt') + ' ';
        if (prevId.indexOf(pointId) == -1) {
            $(node).attr('c_pt', prevId + pointId);
        }
    }

    function nearExisting(curr, searchIn, tol) {
        var location = -1;
        for (var i in searchIn) { //@locate point near to mouse up position
            var maxX = searchIn[i][0] + tol;
            var minX = searchIn[i][0] - tol;
            var maxY = searchIn[i][1] + tol;
            var minY = searchIn[i][1] - tol;
            if (curr.X <= maxX && curr.X >= minX && curr.Y <= maxY && curr.Y >= minY) {
                location = i;
                break;
            }
        }
        return location;
    }

    function calcDist(x1, y1, x2, y2) {
        var yLen = (y2 - y1);
        var xLen = (x2 - x1);
        var dist = Math.sqrt(Math.pow(yLen, 2) + Math.pow(xLen, 2));
        return dist;
    }

    /* Draw the selected element on the canvas */
    function drawShape(x1, y1, x2, y2, shape, sketchpad, drawMode) {
		//console.log(arguments.callee.caller.name);
        _nodeNum++;//console.log("drawing Node:"+_nodeNum+"   shape:"+shape);
		var tol = 10;
        var left = Math.min(x1, x2);
        var top = Math.min(y1, y2);
        var right = Math.max(x1, x2);
        var bottom = Math.max(y1, y2);
        var returnnodes = [];
		var startPt={ X:x1, Y:y1 };
		var endPt={ X:x2, Y:y2 };
		var stP = nearExisting(startPt, points, tol);
        var endP = nearExisting(endPt, points, tol);
		
        var settings = { fill: 'none', stroke: inkColor, strokeWidth: '2', id: 'grph_' + _nodeNum, style: 'cursor:default', strokeOpacity: '1' };
        var node = null;
        if (shape == 'polyline') {
            if (x1 != x2 || y1 != y2) {
                node = sketchpad.polyline(viewport,[[x1, y1], [x2, y2]], $.extend(settings, { "class": 'connector', "connector": startNode.id + ' ' + endNode.id }));
            }
        }
        else if (shape == 'point') {
            r = 3;
            settings.fill = inkColor;
            settings.strokeWidth = '12';
            settings.strokeOpacity = '0';
			if (drawMode!='intersection' && stP!=-1) {
                return document.getElementById("grph_"+points[stP][2]);
            }
            node = sketchpad.circle(viewport,x1, y1, r, settings);
            ptWithDAttr(node, sketchpad);
            points[drawNodes.length] = new Array();
            points[drawNodes.length] = [x1, y1, _nodeNum];
        }
        else if (shape == 'rect') {
            node = sketchpad.rect(viewport, left, top, right - left, bottom - top, settings);
        }
        else if (shape == 'circle') {
            var r = (right - left) / 2;
            node = sketchpad.circle(viewport,left + r, (top + bottom) / 2, r, settings);
        }
        else if (shape == 'line') {
			if (stP!=-1) {x1=points[stP][0];y1=points[stP][1];}
			if (endP!=-1) {x2=points[endP][0];y2=points[endP][1];}
			//console.log(x1+'|'+y1+'|||||'+x2+'|'+y2);
            node = sketchpad.line(viewport,x1, y1, x2, y2, settings);
            if (node) {
				//console.log(_nodeNum);
                drawNodes[drawNodes.length] = lineLen;
                returnnodes.push(lineLen);
                $(lineLen).attr('len_of', 'grph_' + _nodeNum);
                $(lineLen).attr('id', 'len_' + _nodeNum);
                //$(lineLen).attr('style', 'display:none;');
                $(node).attr('len_node', 'len_' + _nodeNum);
            }
            else {
                $(lineLen).remove();
            	lineLen = null;
            }
        }
        else if (shape == 'polygon') {
            node = sketchpad.polygon(viewport,[[(x1 + x2) / 2, y1], [x2, y1], [x2, y2], [(x1 + x2) / 2, y2], [x1, (y1 + y2) / 2]], settings);
        }
        else if (shape == 'arc') {
            settings.strokeDashArray = '0';
            settings.strokeWidth = '2';
            sketchpad.change(outline, settings);
            var arcObj = mySvg.parseNode(outline);
            if (arcObj){
                $(outline).attr('center', $(arcPnt).attr('id')).attr('type', 'arc').attr('radius', arcObj.getRadius()).attr('angle', arcObj.getAngle());
                $(outline).unbind('mouseup');
                node = outline;
                $(arcPnt).attr('arc', $(node).attr('id'));
                if ($(arcPnt).attr('withArc')=="1")$(arcPnt).attr('withArc',$(node).attr('id'));
            }
            arcPnt = null;
            rad = null;
            outline = null;
            arcCtr = null;
			if (arcConn) {$(arcConn).remove();	arcConn = null;}
			if (lineLen) {$(lineLen).remove();	lineLen = null;}
            prevArcA = null;
        }
        //redoNodes = [];
        if (node) {
            drawNodes[drawNodes.length] = node;
            returnnodes.push(node);
        }
        returnnodes = returnnodes.concat(locateLabel(x1, y1, x2, y2, shape, sketchpad));
        //TODO:implement mhandle node on all the added nodes

        _history.push({ 'action': 1, 'node': [node] });
        _redoNodes = [];
        return node;
    }

    function ptWithDAttr(node, sketchpad) {
        if (_isDActive) {
            var protractorElem = sketchpad.getElementById("protractor");
            var protractorObj = mySvg.parseNode(protractorElem);
            var rotationC = protractorObj.getRotationPoint();

            var i = nearExisting(rotationC, labelPoints, 8);
            if (i > -1) {
                var labelId = labelPoints[i][3];
                var baseAngle = protractorObj.getRotationAngle();
                var nodeObj = mySvg.parseNode(node);
                var drawnPt = { X: nodeObj.getCx(),
                    Y: nodeObj.getCy()
                };
                var pointAngle = normAngle(drawnPt, rotationC);

                //the protractor angle of rotation is always calculated from RHS of protractor(seen when straight)
                //the other side angle is always measured anticlockwise from RHS side hence min angle = max angle - 180
                var maxAngle = baseAngle; //always between -180 to 180
                var minAngle = maxAngle - 180;
                //to check if the point is between max angle and min angle
                if (minAngle < -180) {
                    //same logic as that of Arc intersection
                    pointAngle = ((pointAngle > maxAngle) ? (-360 + pointAngle) : (pointAngle));
                }
                var isWRTd = 0;
                if (pointAngle >= minAngle && pointAngle <= maxAngle) {
                    isWRTd = 1;
                }
                $(node).attr("proc", isWRTd).attr("p_center", "grph_" + labelId).attr("p_angle", pointAngle).attr("p_baseangle", baseAngle);
            }

        }
    }

    function textLblArea(pt, nNum, txtNId, sketchpad,v) {
		//var drawnNode = drawShape(pt.X, pt.Y, 0, 0, "point", sketchpad);
		//geometry.addHandler(drawnNode, sketchpad);
				
        var nLbl = labelPoints.length;
        var offset = {top:origOffSet[0].offsetTop,left:origOffSet[0].offsetLeft};
        var lblPos={top:Math.round(pt.Y + offset.top + 2),left:Math.round(pt.X + offset.left - 22)};$('.labelAddedNow').removeClass('labelAddedNow');
        nDiv = '<input type="text" size="1" class="_point_lbl labelAddedNow" name="ptLabel' + nLbl + '" id="name_' + nLbl + '" maxlength="2" style="position:absolute;top:' + (lblPos.top+vpOffset.top) + 'px;left:' + (lblPos.left+vpOffset.left) + 'px;width:22px;padding:0;border:1px solid black;" rel="' + nLbl + '" -data-lblPos="'+lblPos.top+','+lblPos.left+'">';
        if (v!="") $(nDiv).appendTo(sketchpad._container).hide(); else $(nDiv).appendTo(sketchpad._container);
        var textLbl = sketchpad.text(viewport,Math.round(pt.X-12), Math.round(pt.Y + 15), ' ', { id: 'grph_' + (txtNId), fill: inkColor, 'text-anchor':'middle' });
        $(textLbl).attr('rel', 'label').attr('for_pt', pt.X + ' ' + pt.Y);$(textLbl).text(v);
        //storing original x,y co-ordinates with label
		//$("#name_" + nLbl).focus();
        labelPoints[nLbl] = new Array();
        labelPoints[nLbl] = [pt.X, pt.Y, nNum, txtNId, v];
        drawNodes[drawNodes.length] = textLbl;
        return textLbl;
    }
    /**searches for existing labels
     * if not found then creates new
     * text input area and svg text node
     * **/
    function locateLabel(x1, y1, x2, y2, shape, sketchpad) {
        var tol = 12;
        var labelNodes = [];
        if (useLabelFlag) {
            var nDiv = null;
            switch (shape) {
                case 'point':
                    {
                        var searchPt = {
                            X: x1,
                            Y: y1
                        };
                        var i = nearExisting(searchPt, labelPoints, tol);
                        if (i == -1) {
                            labelNodes.push(textLblArea(searchPt, _nodeNum, (_nodeNum + 1), sketchpad,""));
                            $('#grph_' + (_nodeNum)).attr('pt_lbls', 'grph_' + (_nodeNum + 1));
                            _nodeNum = _nodeNum + 1;
                        } else {
                            $('#grph_' + (_nodeNum)).attr('pt_lbls', 'grph_' + (labelPoints[i][3]));
                        }
                    }
                    break;
                case 'line':
                    {
                        var startPt = {X: x1,Y: y1};
                        var endPt = { X: x2,Y: y2};
                        var stLbl = nearExisting(startPt, labelPoints, tol);
                        var endLbl = nearExisting(endPt, labelPoints, tol);
                        if (stLbl == -1 && endLbl == -1) {
                            labelNodes.push(textLblArea(startPt, _nodeNum, (_nodeNum + 1), sketchpad,""));
                            labelNodes.push(textLblArea(endPt, _nodeNum, (_nodeNum + 2), sketchpad,""));
                            _nodeNum = _nodeNum + 2;
                            $('#grph_' + (_nodeNum - 2)).attr('pt_lbls', 'grph_' + (_nodeNum - 1) + ' ' + 'grph_' + (_nodeNum));
                        }
                        else if (stLbl == -1) {
                            labelNodes.push(textLblArea(startPt, _nodeNum, (_nodeNum + 1), sketchpad,""));
                            _nodeNum++;
                            var detOfLbl = labelPoints[endLbl];
                            $('#grph_' + (_nodeNum - 1)).attr('pt_lbls', 'grph_' + _nodeNum + ' ' + 'grph_' + detOfLbl[3]);
                        }
                        else if (endLbl == -1) {
                            labelNodes.push(textLblArea(endPt, _nodeNum, (_nodeNum + 1), sketchpad,""));
                            var detOfLbl = labelPoints[stLbl];
                            _nodeNum++;
                            $('#grph_' + (_nodeNum - 1)).attr('pt_lbls', 'grph_' + detOfLbl[3] + ' ' + 'grph_' + _nodeNum);
							
							var dwNod = drawShape(x2, y2, 0, 0, "point", sketchpad);
							geometry.addHandler(dwNod, sketchpad);
                        }
                        else {
                            var detOfsLbl = labelPoints[stLbl];
                            var detOfeLbl = labelPoints[endLbl];
                            $('#grph_' + _nodeNum).attr('pt_lbls', 'grph_' + detOfsLbl[3] + ' ' + 'grph_' + detOfeLbl[3]);
                        }
                        lineDrawn = true;
                    }
                    break;
            }
        }
        return labelNodes;
    }
	_sketchHelper.pointNameInValid = function (val,num){
		//console.log(val.charCodeAt(0));
		if (val.charCodeAt(0)<65 || val.charCodeAt(0)>90) return true;
		for (var i=0;i<val.length;i++) if (val.charCodeAt(i)<48 ||  (val.charCodeAt(i)>57 && val.charCodeAt(i)<65) || (val.charCodeAt(i)>90 && val.charCodeAt(i)<97) || val.charCodeAt(i)>122) return true;
		for (var i=0;i<labelPoints.length;i++){
			if (i==parseInt(num)) continue;
			var textLbl = labelPoints[i][3];
            if ($('#grph_' + textLbl).text()==val) return true;
		}
		return false;
	}
	function findTextLabel(txtLbl){
		for (var i=0;i<labelPoints.length;i++){
			if (labelPoints[i][3]==txtLbl) return i;
		}
		return -1;
	}
    _sketchHelper.editLabel = function(el){
        var txtL='';
        if ($(el).is('circle')) txtL=Number($(el).attr('pt_lbls').split('_')[1]);
        else if ($(el).is('text')) txtL=Number($(el).attr('id').split('_')[1]);
        else return;
        var nm=findTextLabel(txtL);
        if (nm>=0) {
            $('#name_' + nm).css({'display':'block'});
            $('#name_' + nm).val($('#grph_' + txtL).text());
        }
        //$('#grph_' + txtL+',circle[pt_lbls="grph_' + txtL+'"').unbind('click');
    }
    _sketchHelper.updateLabel = function (lblFor, sketchpad, val) {
        var num = parseInt(lblFor);
        var textLbl = labelPoints[num][3];
        $('#grph_' + textLbl).text(val);$('circle[pt_lbls="grph_' + textLbl+'"]').attr('pname',val);
    }
    _sketchHelper.assignLabel = function (lblFor, sketchpad, val) {
        var num = parseInt(lblFor);
        var textLbl = labelPoints[num][3];labelPoints[num][4]=val;
        $('#grph_' + textLbl).text(val);$('circle[pt_lbls="grph_' + textLbl+'"]').attr('pname',val);
        /*$('#grph_' + textLbl+',circle[pt_lbls="grph_' + textLbl+'"').bind('click',function (){
            sketchHelper.editLabel($(this));
        });*/
        $('#name_' + num).focusout().css({'display':'none'})[0].blur();
		//$('#name_' + num).remove();
        return false;
    };

    function drawSelector(nodeElem, sketchpad) {
        var settings = { fill: 'none', stroke: inkColor, strokeWidth: 2, strokeDashArray: '2,2' };
        var left, top, right, bottom;
        var svgNode = mySvg.parseNode(nodeElem);
        switch (svgNode.getnodeName()) {
            case 'circle':
                {
                    left = svgNode.getCx() - svgNode.getRadius();
                    top = svgNode.getCy() - svgNode.getRadius();
                    right = svgNode.getCx() + svgNode.getRadius();
                    bottom = svgNode.getCy() + svgNode.getRadius();
                    break;
                }
            case 'polyline':
            case 'text':
            case 'path':
            case 'line':
                {
                    left = svgNode.getMinX();
                    top = svgNode.getMinY();
                    right = svgNode.getMaxX();
                    bottom = svgNode.getMaxY();
                    break;
                }
        }
        var stroke_tolerance = 4;
        node = sketchpad.rect(left - (stroke_tolerance / 2), top - (stroke_tolerance / 2), right - left + (stroke_tolerance), bottom - top + stroke_tolerance, settings);
        selectorRect.push(node);
        return node;
    }

    function deleteSelected(sketchpad) {
        removeSlector(sketchpad);
        var deletedNodes = deleteNodes(currNode, sketchpad, 0);
        _history.push({ 'action': 0, 'node': deletedNodes });
        _redoNodes = [];
        currNode = [];
    }

    function deleteNodes(nodeArr, sketchpad, recurr) {
        //[todo] remove line length
        recurr++;
        //console.log(nodeArr);
        //hence recurr==1 means search and delete the child and connected elements
        var lblList = new Array();
        var deletedNodes = [];
        if (!$.isArray(nodeArr)) {
            nodeArr = [nodeArr];
        }
		//console.log(nodeArr);
        for (var j in nodeArr) {
            var elemAt = $.inArray(nodeArr[j], drawNodes);
            if (elemAt > -1) {
                var nodeObj = mySvg.parseNode(nodeArr[j]);
                if (nodeObj.getnodeName() != 'text') {
                    if (nodeObj.getnodeName() == 'circle' && recurr == 1) {
                        if ($(nodeArr[j]).attr('arc') != undefined) {
                            var toRemoveNode = sketchpad.getElementById($(nodeArr[j]).attr('arc'));
                            deletedNodes = deletedNodes.concat(deleteNodes(toRemoveNode, sketchpad, recurr));
                        }
                    }
                    else if (nodeObj.getnodeName() == 'path' && recurr == 1) {
                        if ($(nodeArr[j]).attr('center') != undefined) {
                            var toRemoveNode = sketchpad.getElementById($(nodeArr[j]).attr('center'));
							if ($('#'+$(nodeArr[j]).attr('center')).attr('withArc')==$(nodeArr[j]).attr('id'))  deletedNodes = deletedNodes.concat(deleteNodes(toRemoveNode, sketchpad, recurr));
                        }
                    }
                    else if (nodeObj.getnodeName() == 'line' && recurr == 1) {
                        if ($(nodeArr[j]).attr('len_node') != undefined) {
                            var lenNode = sketchpad.getElementById($(nodeArr[j]).attr('len_node'));
                            var lenNodeAt = $.inArray(lenNode, drawNodes);
                            if (lenNodeAt > -1) {
								//console.log(sketchpad);
								//console.log(lenNode);
                                sketchpad.remove(lenNode);
                                drawNodes.splice(lenNodeAt, 1);
                                points.splice(lenNodeAt, 1);
                            }
                        }
                    }

                    if (useLabelFlag == true && $(nodeArr[j]).attr('pt_lbls') != undefined) {
                        var lblStrL = $(nodeArr[j]).attr('pt_lbls');
                        var lblListL = lblStrL.split(" ");
                        lblList = lblList.concat(lblListL);//console.log(lblList);
                    }
                    var elemIndex = $.inArray(nodeArr[j], drawNodes);
                    sketchpad.remove(nodeArr[j]);
                    deletedNodes.push(nodeArr[j]);
                    drawNodes.splice(elemIndex, 1);
                    points.splice(elemIndex, 1);
                }
                else {
                    lblList.push($(nodeArr[j]).attr('id'));
                }
            }
        }

        for (var i in lblList) {
            var deleteFlag = true;
            for (var k in drawNodes) {
            	//to leave the label ids that are being used for other elements
                var chkWithN = drawNodes[k];
                var drawNObj = mySvg.parseNode(chkWithN);
                var toLeaveIds = $(chkWithN).attr('pt_lbls');
                if (toLeaveIds != undefined) {
                    //[ALT]create an array of to be retained id's and check for that list in next run
                    // not needed now: maximum length of lblList = 2 (deletion of line)
                    if (toLeaveIds.indexOf(lblList[i]) > -1) {
                        deleteFlag = false;
                        break;
                    }
                }
            }
            //console.log(lblList[i],deleteFlag);
            if (deleteFlag) {
                var delTextArea = false;

                var txtNodeDel = sketchpad.getElementById(lblList[i]);
                if (!txtNodeDel) {
                    continue;
                }
                if (txtNodeDel.getAttribute('rel') == 'label') {
                    delTextArea = true;
                }
                var elemAt = $.inArray(txtNodeDel, drawNodes);
                if (elemAt > -1) {
                    sketchpad.remove(txtNodeDel);
                    drawNodes.splice(elemAt, 1);
                    points.splice(elemAt, 1);
                    var nodeIdNum = parseInt(lblList[i].substr(5));
                    for (var lblNum in labelPoints) {
                        if (labelPoints[lblNum][3] == nodeIdNum) {  //text-label id is at index 3
                            if (delTextArea) {
                                $('#name_' + lblNum).remove();
                            }
                            labelPoints[lblNum] = [];
                            break;
                        }
                    }
                }
            }
        }
        return deletedNodes;
    }

    function removeSlector(sketchpad) {
        while (selectorRect.length > 0) {
            sketchpad.remove(selectorRect[selectorRect.length - 1]);
            selectorRect.splice(selectorRect.length - 1, 1);
        }
    }

    _sketchHelper.keyEvt = function (e) {
		
		
        var graphEdited = false;
        var sketchpad = $("#canvasContainer").svg('get');
        e.ctrlKey = e.ctrlKey || e.metaKey;
        switch ((e.originalEvent && e.originalEvent.keyIdentifier) || e.which) {
            case 8: //backspace
            case 'Backspace':
            case 'U+0008':
            case 46: //delete
            case 'Del':
            case 'U+007F':
                {
                    if (currNode.length > 0) {
                        deleteSelected(sketchpad);
                        graphEdited = true;
						
                    }
                    break;
                }
            case 27: //escape
            case 'Esc':
            case 'U+001B':
                {
                    removeSlector(sketchpad);
                    currNode = [];
                    break;
                }
            case 'U+0059':
            case 89:
            case 'Y':
                {
                    /*if (e.ctrlKey) {
                        $(this).trigger('trigger_redo');
                        //  implRedo(); 
                        //[NOT Able to attach drag end drag functionality when new node added]
                        graphEdited = true;
                    } break;
					*/
                }
            case 'U+005A':
            case 90:
            case 'Z':
                {
                    /*if (e.ctrlKey) {
                        //$(this).trigger('trigger_undo');
                        _sketchHelper.implUndo(sketchpad);
                        graphEdited = true;
						console.log('undo');
                    }
                    break;
					*/
                }
        }
        if (graphEdited) {
            $(this).trigger('graph_change');
            e.preventDefault();
        }
    };

	_sketchHelper.getHistory = function(){
		var t=[];
		$(drawNodes).each(function(ind,elem){
			t.push(nodeToStr(elem));
		});
		return (JSON.stringify(t));
	}

    //[TODO]- proper implementation of ctrl+z;ctrl+y
    _sketchHelper.implUndo = function (sketchpad) {
        if (_history.length == 0) {
            return 0;
        }
        var lastAct = _history.pop();
        var nNode = [];
        _redoNodes.push(lastAct);
        switch (lastAct['action']) {
            case _actions.deleted:
                {
                    nNode = addNodesBack(lastAct['node'], sketchpad);
                } break;
            case _actions.added:
                {
                    var reDrawnNodes = [];
                    for (var i in lastAct['node']) {
                        var id = $(lastAct['node'][i]).attr('id');
                        reDrawnNodes.push(sketchpad.getElementById(id));
                    }
                    deleteNodes(reDrawnNodes, sketchpad, 0);
                } break;

        }
        return 1;
    };

    _sketchHelper.implRedo = function (sketchpad) {
        if (_redoNodes.length == 0) {
            return [];
        }
        var lastAct = _redoNodes.pop();
        //the actions would be opposite for redo than undo
        var nNode = [];
        _history.push(lastAct);
        switch (lastAct['action']) {
            case _actions.deleted:
                {
                    var reDrawnNodes = [];
                    for (var i in lastAct['node']) {
                        var id = $(lastAct['node'][i]).attr('id');
                        reDrawnNodes.push(sketchpad.getElementById(id));
                    }
                    deleteNodes(reDrawnNodes, sketchpad, 0);
                } break;
            case _actions.added:
                {
                    nNode = addNodesBack(lastAct['node'], sketchpad);

                } break;

        }
        return nNode;
    };

    function addNodesBack(nodeArr, sketchpad) {
        var addedNodes = [];
        for (var i in nodeArr) {
            var svgStr = XmlToSvgStr(nodeArr[i]);
            var wsId = svgStr.replace(/(.*id=")(\w*)(".*)/g, "$2");
            addedNodes.push(geometry.addNode(svgStr, sketchpad, { id: wsId }));
        }
        return addedNodes;
    }

    //    function DeleteLast(sketchpad) {
    //        if (!drawNodes.length) {
    //            return;
    //        }
    //        //detach
    //        sketchpad.remove(drawNodes[drawNodes.length - 1]);
    //        if (drawNodes.length == points.length) {
    //            points.splice(drawNodes.length - 1, 1);
    //        }
    //        return drawNodes.splice(drawNodes.length - 1, 1);
    //    }

    _sketchHelper.setuseLabelFlag = function (flag) {
        useLabelFlag = flag;
    };

    _sketchHelper.getScale = function () {
        return scale.w;
    };

    _sketchHelper.setDActive = function () {
        _isDActive = true;
    };
    _sketchHelper.setDInActive = function () {
        _isDActive = false;
    };
	_sketchHelper.setRActive = function () {
        _isRActive = true;
    };
    _sketchHelper.setRInActive = function () {
        _isRActive = false;
    };
	_sketchHelper.setCompassActive = function () {
        _isCompActive = true;
    };
    _sketchHelper.setCompassInActive = function () {
        _isCompActive = false;
    };

    return _sketchHelper;
} ();

var sketchHelper;
if (sketchHelper === undefined) {
    sketchHelper = SketchHelper;
}
