var flipped=1;
var mx=0,my=0;var ismdown=false;
var rulerMoved = false;
function closeAllTooltips(){$('.jqx-tooltip').css('opacity',0);}
var Geometry = function () {
    var _geometry = {};
    var _protractor = null;
    var _protractorL = null;
    var _protractorR = null;
    var _ruler = null;
	var _rulerHandle = null;
	var _compass = null;
	var compassShowing = false;
    var viewport = null;
    var scale = { w: (50) };
    _geometry.data = function () {
        var data = sketchHelper.nodesToStr();
        var sketchScale = sketchHelper.getScale();
        var lineScale = '<linescale scale="' + sketchScale + '" />';
        data.push(lineScale);
        return data;
    };

    _geometry.init = function () {
        Initialize();
    };

    _geometry.clear = function (sketchpad) {
        //clears history
        if (sketchpad) {
            sketchpad.clear(true);
            $('#canvasContainer').svg('destroy').unbind("keydown", sketchHelper.keyEvt).undelegate('._point_lbl', 'keyup');
            $('._selShapeGeo').unbind("click");
            $('._show-protractor').unbind("click");
            $('._hide-protractor').unbind("click");
        }
        sketchHelper.reset(sketchpad);
        drawcode.replayAnime();
    };

	_geometry.addHandler = function (node, sketchpad){
		mHandleNode(node, sketchpad);
	}

    function assignToolTip(el,text){
        $(el).mouseover(function(e){
            $('#tooltip').css({'top':(e.clientY+5)+'px','left':(e.clientX-5)+'px'}).html(text);
            $(document).bind('mousemove',function(e){
                $('#tooltip').css({'top':(e.clientY+15)+'px','left':(e.clientX+15)+'px'});
            });
            $('#tooltip').show();
            $(this).mouseout(function(e){
                $('#tooltip').hide();
                $(document).unbind('mousemove');
            });
        });
        try{
            $(el).touchstart(function(e){
               $('#tooltip').css({'top':(e.clientY+5)+'px','left':(e.clientX-5)+'px'}).html(text);
               $(document).bind('mousemove',function(e){
                   $('#tooltip').css({'top':(e.clientY+15)+'px','left':(e.clientX+15)+'px'});
               });
               $('#tooltip').show();
               $(this).touchend(function(e){$('#tooltip').hide();$(document).unbind('mousemove',function(e){})}); 
            });
        }catch(er){}

    }
    function mHandleNode(newNode, sketchpad) {
        if (!newNode) {
            return;
        }
        $(newNode).mousedown(function (e) {
            // document.addEventListener('mouseleave', outOfDocument, false);
            if($(newNode).is('#ruler') || $(newNode).is('#rulerHandle')) {rulerMoved = false;}
            var arcCon = sketchHelper.startDragCon(e, sketchpad, $('#geoshape').val(), this);
            if (arcCon) {
                callMouseDrag(sketchpad, arcCon);
            }
        }).mousemove(function (e) {
			e.preventDefault();
            if($(newNode).is('#ruler') || $(newNode).is('#rulerHandle')) {rulerMoved = true;}
            var outLine = sketchHelper.dragging(e, sketchpad, $('#geoshape').val());closeAllTooltips();
            callMouseDrag(sketchpad, outLine);
        }).mouseup(function (e) {
            // document.removeEventListener('mouseleave', outOfDocument);
            var toolState = $('#geoshape').val();
            var nNode = sketchHelper.endDragCon(e, sketchpad, $('#geoshape').val(), this);
            if (toolState != 'select' && nNode) {
                if (toolState=="arc") $('#geoshape').val("select");
                callEndDrag(sketchpad, nNode);
            } else {
                $('#canvasContainer').focus();
            }
        }).click(function (e, eventParameter) {
            if(eventParameter=='tapped') {
                rulerTapped = true;
            } else if(rulerTapped==true) {
                rulerTapped = false;
                return;
            }
            if($(newNode).is('#ruler') || $(newNode).is('#rulerHandle')) {
                if(!rulerMoved) {
                    if($(newNode).is('#ruler') && geometryTools.ruler.mobility.rotate) {
                        $('#rulerHandle').show();
                        $('#rulerHandle').trigger('mouseover');
                        $('#tooltip').css({'top':e.clientY,'left':e.clientX});
                    }
                    else if($(newNode).is('#rulerHandle')) {
                        $('#rulerHandle').hide();
                        $('#ruler').trigger('mouseover');
                        $('#tooltip').css({'top':e.clientY,'left':e.clientX});
                    }
                } else {
                    rulerMoved = false;
                }
            }
            var nNode = sketchHelper.endDragCon(e, sketchpad, $('#geoshape').val(), this);
            if ($('#geoshape').val() != 'select' && nNode) {
                callEndDrag(sketchpad, nNode);
            } else {
                $('#canvasContainer').focus();
            }
        });
    }

    _geometry.addNode = function (elemStr, sketchpad, setting) {
        var newNode = sketchHelper.addNewShape(elemStr, sketchpad, setting);
        mHandleNode(newNode, sketchpad);
        $('#canvasContainer').focus();
        $('#canvasContainer').trigger('graph_change');
        return newNode;
    };
	_geometry.addAnimNode = function (elemStr, sketchpad, setting, anim, animtime) {
        var newNode = sketchHelper.addShape(elemStr, sketchpad, setting, anim, animtime);
        //mHandleNode(newNode, sketchpad);
        $('#canvasContainer').focus();
        $('#canvasContainer').trigger('graph_change');
        return newNode;
    };

	_geometry.flipCompass = function(){
			if(!geometryTools.compass.mobility.flip)
				return;
			$('#compass').attr('transform',$('#compass').attr('transform')+',scale(-1,1)')
			flipped*=-1;
			//console.log('flipped');
	}
	_geometry.isCompassFlipped = function (){
		return flipped;
	}
    _geometry.getViewport = function(){
        return viewport;
    }
	function resetInstr(){
		$("#instructions").stop();$("#instructions").css({top:'480px'});$("#instructions").html('');$('#canvasBackground').css("cursor","crosshair");
	}
	function showInstr(txt,time){
		resetInstr();$("#instructions").html(txt);
		$("#instructions").animate({top:'460px'},100,function(){
	  		if(time!=0) $("#instructions").delay(time).animate({top:'480px'},100);
		});
	}
    function Initialize() {
        $('#canvasContainer').svg({settings: {height: '480px',width: '640px', id: 'canvasBackground'}, onLoad: function (svg) {
            FormSketchpad(svg);
			drawcode.setSVG(svg);drawcode.setViewport(viewport);
        }
        });
        var offset = $('#canvasContainer');
		var helpOpen=0;
        sketchHelper.setOffset(offset);sketchHelper.setViewport(viewport);
        var sketchpad = $('#canvasContainer').svg('get');
        $('._selShapeGeo').click(function () {
            $('._selShapeGeo').removeClass('gBtnHoldCurr').addClass('gBtnHold');
            $(this).removeClass('gBtnHold').addClass('gBtnHoldCurr');
            $('#geoshape').val($(this).attr('rel'));
			if ($('#geoshape').val()=="undo") {
				if (sketchHelper.implUndo(sketchpad)==1) showInstr('Undone last action', 2000);
				else showInstr('Nothing left to undo', 2000);
				$('#geoshape').val('select');
			}
            else if ($('#geoshape').val()=="redo") {
                if (sketchHelper.implRedo(sketchpad).length) showInstr('Redone last action', 2000);
                else showInstr('Nothing left to redo', 2000);
                $('#geoshape').val('select');
            }
            else if ($('#geoshape').val()=="clear") {
                sketchHelper.clearCanv(sketchpad); showInstr('Cleared all constructions made by you.', 2000);
                $('#geoshape').val('select');
            }
			else {
				showInstr('Click anywhere on the board to place a point. You can add a label to the point in the textbox next to it.',0);
                $('#canvasBackground').css("cursor","url(../assets/pencil.png) 0 0,crosshair");
			}
        });
		$('.toolHov:eq(0)').click(function(){$(this).hide();$('.toolHov:eq(1)').show();$("#toolHelp").animate({right:'0px'},1000,function(){});});
		$('.toolHov:eq(1)').click(function(){$(this).hide();$("#toolHelp").animate({right:'-180px'},1000,function(){$('.toolHov:eq(0)').show();});});
        $('._show-protractor').click(function (event) {
            $('._show-protractor').hide();
            $('._hide-protractor').show();
            // if(event.originalEvent!==undefined) {
                $('#toolHelp').show();$('.hLinks').eq(1).trigger('click');helpOpen+=2;
                if(showHelp!='auto' && !(explanationMode && event.originalEvent===undefined))
                    showInstr('Showing protrator.. Click SHOW for help.', 3000);
            // }
            var center = {
                x: geometryTools.protractor.layout.x,
                y: geometryTools.protractor.layout.y,
            };
            var leftTop = {
                x: geometryTools.protractor.layout.x-200,
                y: geometryTools.protractor.layout.y-200,
            };
            var angle = geometryTools.protractor.layout.angle;
            var settings;
            settings = { "id": "protractor", "opacity": "0.5", "transform":"rotate("+angle+" "+center.x+" "+center.y+")" };
            _protractor = sketchpad.image(leftTop.x, leftTop.y, 400, 214, '../assets/pr.png', settings);
            if(geometryTools.protractor.mobility.translate) {
                $(_protractor).attr("style", "cursor:move");
                mHandleNode(_protractor, sketchpad);
                assignToolTip($("#protractor"),'<b>Move</b>');
            }
            if(geometryTools.protractor.mobility.rotate) {
                settings = { "id": "protractorL", "opacity": "0.5", "transform":"rotate("+angle+" "+center.x+" "+center.y+")" };
                _protractorL = sketchpad.image(leftTop.x-30, leftTop.y+150, 30, 75, '../assets/ar.png', settings);
                $(_protractorL).attr("style", "cursor:move");
                mHandleNode(_protractorL, sketchpad);
                settings = { "id": "protractorR", "opacity": "0.5", "transform":"rotate("+(angle+173)+" "+center.x+" "+center.y+")" };
                _protractorR = sketchpad.image(leftTop.x-30, leftTop.y+150, 30, 75, '../assets/ar.png', settings);
                $(_protractorR).attr("style", "cursor:move");
                mHandleNode(_protractorR, sketchpad);
                assignToolTip($("#protractorL,#protractorR"),'<b>Rotate</b>');
            } else {
                settings = { "id": "protractorL", "opacity": "0.0", "transform":"rotate("+angle+" "+center.x+" "+center.y+")" };
                _protractorL = sketchpad.image(leftTop.x-30, leftTop.y+150, 30, 75, '../assets/ar.png', settings);
                settings = { "id": "protractorR", "opacity": "0.0", "transform":"rotate("+(angle+173)+" "+center.x+" "+center.y+")" };
                _protractorR = sketchpad.image(leftTop.x-30, leftTop.y+150, 30, 75, '../assets/ar.png', settings);
            }
            $('._selShapeGeo[rel="select"]').click();
            sketchHelper.setDActive();
            if(showHelp=='auto' && event.originalEvent!==undefined)
                $('.toolHov:eq(0)').trigger('click');
        });
        $('._hide-protractor').click(function () {
            $('._show-protractor').show();
            $('._hide-protractor').hide();
			helpOpen-=2;if (helpOpen==0) $('#toolHelp').hide();
            sketchpad.remove(_protractor);
            sketchpad.remove(_protractorR);
            sketchpad.remove(_protractorL);
            sketchHelper.setDInActive();
        });
		$('._show-ruler').click(function (event) {
            $('._show-ruler').hide();
            $('._hide-ruler').show();
			// if(event.originalEvent!==undefined) {
                $('#toolHelp').show();$('.hLinks').eq(2).trigger('click');helpOpen+=4;
                if(showHelp!='auto' && !staticRuler && !(explanationMode && event.originalEvent===undefined))
                    showInstr('Showing ruler.. Click SHOW for help.', 3000);
            // }
            var leftTop = {
                x: geometryTools.ruler.layout.x,
                y: geometryTools.ruler.layout.y,
            };
            var angle = geometryTools.ruler.layout.angle;
            var settings = { "id": "ruler", "opacity": "0.7" };
            if(angle!=0)
                settings['transform'] = "rotate("+angle+" "+leftTop.x+" "+leftTop.y+")";
            _ruler = sketchpad.image(leftTop.x, leftTop.y, 650, 52, '../assets/rul.png', settings);
            if(geometryTools.ruler.mobility.translate) {
                $(_ruler).attr("style", "cursor:move");
                mHandleNode(_ruler, sketchpad);
                assignToolTip($("#ruler"),'<b>Move</b>');
            }
            var settings = { "id": "rulerHandle", "opacity": "0.8" };
            if(angle!=0)
                settings['transform'] = "rotate("+angle+" "+leftTop.x+" "+leftTop.y+")";
            _rulerHandle = sketchpad.image(leftTop.x+0, leftTop.y, 650, 52, '../assets/rulh.png', settings);
            if(geometryTools.ruler.mobility.rotate) {
                $(_rulerHandle).attr("style", "cursor:url(../assets/moveicon.png),move");
                mHandleNode(_rulerHandle, sketchpad);
                assignToolTip($("#rulerHandle"),'<b>Rotate</b>');
            }
            $(_rulerHandle).hide();
            $('._selShapeGeo[rel="select"]').click();
            sketchHelper.setRActive();
            if(showHelp=='auto' && event.originalEvent!==undefined && !staticRuler)
                $('.toolHov:eq(0)').trigger('click');
        });
        $('._hide-ruler').click(function () {
            $('._show-ruler').show();
            $('._hide-ruler').hide();
			helpOpen-=4;if (helpOpen==0) $('#toolHelp').hide();
            $('#tooltip').hide();
            sketchpad.remove(_ruler);
            sketchpad.remove(_rulerHandle);
            sketchHelper.setRInActive();
        });
        sketchHelper.setuseLabelFlag(true);
        $(document).bind("keydown", sketchHelper.keyEvt);
        $('#canvasContainer').delegate('._point_lbl', 'keyup', function (e) {
            var nlbl = $(this).attr('rel');
            $(this).removeClass('error');
			$(this).val($(this).val().toUpperCase());
			if (e.which == 13){
				if ($.trim($(this).val()).length > 0 && !sketchHelper.pointNameInValid($(this).val(),nlbl)) {
	                sketchHelper.assignLabel(nlbl, sketchpad, $(this).val().toUpperCase());
	            }
                else if ($(this).val()==""){
					alert('A blank label is not allowed. Try some name.');
				}
                else{
                    alert($(this).val()+' is an invalid entry or has been already used. Try some other name.');  
                }
			}
            else{
                sketchHelper.updateLabel(nlbl, sketchpad, $(this).val().toUpperCase());
            }
        });
        $('#canvasContainer').delegate('*','mouseup',function(event){
            if(out_of_document) {
                out_of_document = false;
                event.stopPropagation();
                return;
            }
            if (!$(this).is('input[type="text"]._point_lbl')){
                $('input[type="text"]._point_lbl').each(function(ind,itm){
                    if ($(itm).is('.labelAddedNow')) {$('.labelAddedNow').removeClass('labelAddedNow'); return;}
                    var nlbl = $(itm).attr('rel');
                    $(itm).val($(itm).val().toUpperCase());
                    if ($.trim($(itm).val()).length > 0 && !sketchHelper.pointNameInValid($(itm).val(),nlbl)) {
                        $(itm).removeClass('editing').blur().focusout();
                        sketchHelper.assignLabel(nlbl, sketchpad, $(itm).val().toUpperCase());
                    }
                    else if ($.trim($(itm).val()).length>0){
                        alert($(itm).val()+' is an invalid entry or has been already used. Try some other name.');
                        $(itm).addClass('error');
                    }
                    else {
                        $(itm).removeClass('editing').blur().focusout();
                        sketchHelper.assignLabel(nlbl, sketchpad, $(itm).val().toUpperCase());
                    }
                });
            }
            event.stopPropagation();
        });
        $('#viewport').delegate('text,circle','click',function (){
            if ($(this).is('text')) {if (!$(this).attr('for_pt')) return;}
            else if ($(this).is('circle')) {if (!$(this).attr('pt_lbls')) return;}
            sketchHelper.editLabel($(this));
        });
        $('#canvasContainer').delegate('input[type="text"]', 'focusin', function () {
            $('.editing').removeClass('editing');
            $(this).addClass('editing');
        });
        $('#canvasContainer').delegate('.editing', 'focusout', function () {
           var nlbl = $(this).attr('rel');
            $(this).val($(this).val().toUpperCase());
            if ($.trim($(this).val()).length > 0 && !sketchHelper.pointNameInValid($(this).val(),nlbl)) {
                $(this).removeClass('editing');
                sketchHelper.assignLabel(nlbl, sketchpad, $(this).val().toUpperCase());
            }
            else if ($.trim($(this).val()).length>0){
                alert($(this).val()+' is an invalid entry or has been already used. Try some other name.');
                $(this).addClass('error').show()[0].focus();
            }
            else {
                $(this).removeClass('editing');
            }
        });
        $('#canvasContainer').delegate('#compassbob', 'click', function (e) {
			//console.log('flip');
            if(compassFlipEvent) {
                compassFlipEvent = false;
                return;
            }
			_geometry.flipCompass();
		});
        $('#canvasContainer').bind("trigger_undo", function (e) {
            var newNode = sketchHelper.implUndo(sketchpad);
            mHandleNode(newNode,sketchpad);
        });
        $('._pan-canvas').hide();
        /*
        $('._pan-canvas').click(function(){
            $('.button:visible').addClass('allHide').hide();
            $('._fix-canvas').show().removeClass('allHide');
            showInstr('Drag the sheet to move it around. Click Fix Sheet when done.', 3000);
            //sketchpad.group('viewport');
            //$('#canvasBackground').append('<g id="viewport"></g>');
            //$('#canvasBackground *').not('#surface,#viewport,#protractor,#protractorL,#protractorR,#ruler,#rulerHandle,#compass').each(function(){
            //    var tmp = document.createElement("circle");
            //    document.getElementById('viewport').appendChild(tmp);
            //    document.getElementById('viewport').replaceChild($(this)[0],tmp);
            //});
            //$('#canvasContainer').append('<div id="panDiv"></div>');
            $('#panDiv').mousedown(function(e){
              mx=e.clientX;my=e.clientY;
              $('#panDiv').bind('mousemove',function(e){
                 var matArr=[0,0];
                 if ($('#viewport').attr('transform')) matArr=($('#viewport').attr('transform')).replace(/matrix\(1,0,0,1,|\)/g,'').split(',');
                 var nx=matArr[0]*1+e.clientX-mx;mx=e.clientX;
                 var ny=matArr[1]*1+e.clientY-my;my=e.clientY;
                 $('#viewport').attr('transform','matrix(1,0,0,1,'+nx+','+ny+')');
                 sketchHelper.updateTextLables(nx,ny);
              });
            });
            $('#panDiv').mouseup(function(e){$('#panDiv').unbind('mousemove');});
        });
        $('._fix-canvas').click(function(){
            $('._fix-canvas').hide();
            $('.allHide').show().removeClass('allHide');
            showInstr('Sheet fixed.', 3000);
            //$('#viewport *').each(function(){
            //    var tmp = document.createElement("circle");
            //    document.getElementById('canvasBackground').appendChild(tmp);
            //    document.getElementById('canvasBackground').replaceChild($(this)[0],tmp);
            //});
            $('#panDiv').remove();
        });
        */
		$('._show-compass').click(function (event) {
            $('._show-compass').hide();
            $('._hide-compass').show();
			// if(event.originalEvent!==undefined) {
                $('#toolHelp').show();$('.hLinks').eq(0).trigger('click');helpOpen+=1;
                if(showHelp!='auto' && !(explanationMode && event.originalEvent===undefined))
                    showInstr('Showing compass.. Click SHOW for help.', 3000);
            // }
			flipped=1;
            var compassTip = {
                x: geometryTools.compass.layout.x,
                y: geometryTools.compass.layout.y-296,
            };
            var internalAngle = Math.asin((geometryTools.compass.layout.radius*scale.w)/(2*201))*180/Math.PI;
            var rotationAngle = geometryTools.compass.layout.angle + internalAngle;
            var flip = geometryTools.compass.layout.flip;
            flipped = flip;
            var settings;
            settings = { "label":"0 0", "init":"0 296 0"};
            //var settings = { "label":"0 0", "init":"0 0 0"};
            _compass=sketchpad.group('compass',settings);
			settings = { "id": "compasspt", "opacity": "1" };
            var compassPt = sketchpad.image(_compass,-11, 95, 11, 201, '../assets/ptH.png', settings);
			var g1=sketchpad.group(_compass,'compasspnH');
			settings = {"id": "compasspn", "opacity": "1" };
            var compassPn = sketchpad.image(g1, 0, 95, 11, 188, '../assets/ptH1.png', settings);
            if(geometryTools.compass.mobility.translate) {
                $(compassPt).attr("style", "cursor:move"); 
                $(compassPn).attr("style", "cursor:move").attr("title","Move"); 
                assignToolTip($("#compasspn,#compasspt"),'<b>Move</b>');
            }
            settings = {"id":"pHead","opacity": "1", "fill": "#e88a2c", "stroke-width": "1","stroke": "#000", "transform": "rotate(10 10.5 237.5)"};
			var pHead=sketchpad.rect(g1,0, 164, 22, 27, settings);
            if(geometryTools.compass.mobility.rotate) {
                $("#pHead").attr({
                    title: 'Rotate',
                    style: 'cursor:ne-resize',
                });
                // settings = {"id":"pHead","opacity": "1", "fill": "#BB8E4C", "stroke-width": "1","stroke": "#000", "transform": "rotate(10 10.5 237.5)", "title" : "Rotate", "style" : "cursor:ne-resize"};
                assignToolTip($("#pHead"),'<b>Rotate</b>');
            }
            settings = {"id":"pBody","opacity": "1", "fill": "#bfbfbf", "stroke-width": "1","stroke": "#000", "transform": "rotate(10 10.5 237.5)"};
            var pHead=sketchpad.rect(g1,-2, 190, 26, 55, settings);
            if(geometryTools.compass.mobility.extend) {
                $("#pBody").attr({
                    title: 'Extend',
                    style: 'cursor:url(../assets/moveicon.png) 20 9,auto',
                });
                // settings = {"id":"pBody","opacity": "1", "fill": "#bfbfbf", "stroke-width": "1","stroke": "#000", "transform": "rotate(10 10.5 237.5)", "title" : "Extend", "style" : "cursor:url(../assets/moveicon.png) 20 9,auto"};
                assignToolTip($("#pBody"),'<b>Extend</b>');
            }
            settings = { "id": "pTip", "opacity": "1", "fill": "#BB8E4C", "stroke-width": "1","stroke": "#000", "transform": "rotate(10 10.5 237.5)"};
            var pTip = sketchpad.path(g1, "m0,245l22,0l0,30l-11,22l-11,-22l0,-30zm0,30l22,0l-11,22l-0.5,-1l1,0l0.5,-1l-2,0l-0.5,-1l3,0l0.5,-1l-4,0l-0.5,-1l5,0l0.5,-1l-6,0l-0.5,-1l7,0l0.5,-1l-8,0l-7,-14", settings);
            if(geometryTools.compass.mobility.draw) {
                $("#pTip").attr({
                    title: 'Draw',
                });
                // settings = { "id": "pTip", "opacity": "1", "fill": "#BB8E4C", "stroke-width": "1","stroke": "#000", "transform": "rotate(10 10.5 237.5)", "title" : "Draw"};
                assignToolTip($("#pTip"),'<b>Draw</b>');
            }
			settings = { "id": "compassbob", "opacity": "1", "title" : "Flip"};
            var compassBob = sketchpad.image(_compass,-18, 50, 36, 63, '../assets/bob.png', settings);
            if(geometryTools.compass.mobility.flip) {
                $(compassBob).attr("style", "cursor:url(../assets/flip.png),auto"); 
                assignToolTip($("#compassbob"),'<b>Flip</b>');
            }
            mHandleNode(_compass, sketchpad);
            $('._selShapeGeo[rel="select"]').click();
			sketchpad.change(_compass, { label: compassTip.x+' '+compassTip.y});
			sketchpad.change(_compass, { transform: 'translate('+compassTip.x+', '+compassTip.y+'),rotate('+rotationAngle+' 0 296),scale('+flip+', 1)' });
            sketchpad.change(compassBob, { transform: 'translate(0, 0),rotate('+-internalAngle+' 0 95)' });
            sketchpad.change(g1, { transform: 'translate(0, 0),rotate('+-2*internalAngle+' 0 95)' });
            sketchHelper.setCompassActive();
            if(showHelp=='auto' && event.originalEvent!==undefined)
                $('.toolHov:eq(0)').trigger('click');
            
        });
        $('._hide-compass').click(function () {
            $('._show-compass').show();
            $('._hide-compass').hide();helpOpen-=1;
			if (helpOpen==0) $('#toolHelp').hide();
            sketchpad.remove(_compass);
            sketchHelper.setCompassInActive();
        });


		$('.hLinks').click(function(){
			$('.hLinks').removeClass('sel');$(this).addClass('sel');
			slideIntoView($(this).index());
		});
    }
	function slideIntoView(ind){
		$('.hDivs').parent().animate({'left':(-1*ind*180)});
	}
    function callEndDrag(svg, newNode) {
		//console.log(newNode);
    	if (!newNode) {
            return;
        }
        mHandleNode(newNode, svg);
        var nNodeArr = sketchHelper.checkForCrossed(newNode, svg);
        //console.log('checkForCrossed called');
        for (var i in nNodeArr) {
            mHandleNode(nNodeArr[i], svg);
        }
		$('#geoshape').val('select');
		resetInstr();
		sketchHelper.bringPointsToFrontV();

        $('#canvasContainer').trigger('graph_change');
    }

    function callMouseDrag(svg, outLine) {
        if (outLine) {
            $(outLine).mousedown(function (e) {
                var arcCon = sketchHelper.startDrag(e, svg, $('#geoshape').val());
				//console.log(arcCon);
                if (arcCon) {
                    callMouseDrag(sketchpad, arcCon);
                }
            }).mouseup(function (e) {
                var newNode = sketchHelper.endDrag(e, sketchpad, $('#geoshape').val());
                if ($('#geoshape').val() != 'select' || compassState!='off') {
                    compassState='off';
                    callEndDrag(svg, newNode);
                } 
                else {
                    $('#canvasContainer').focus();
                }
                if ($('#geoshape').val() == 'line') $('#geoshape').val('select');
            });
        }
    }
	_geometry.addEvts = function (svg){
        sketchHelper.setAnimeDrawnCount();
		$('#surface').mousedown(function (e) {
			if ($('#geoshape').val()=='line') {
				$('#geoshape').val('select');
			}
            var arcCon = sketchHelper.startDrag(e, svg, $('#geoshape').val());
            if (arcCon) {
                callMouseDrag(sketchpad, arcCon); //TODO: add some comments 
            };
			$(document).mousemove(function (e) {
	            var outLine = sketchHelper.dragging(e, svg, $('#geoshape').val());closeAllTooltips();
	            callMouseDrag(svg, outLine);
	        }).mouseup(function (e) {
	            var newNode = sketchHelper.endDrag(e, svg, $('#geoshape').val());
	            if ($('#geoshape').val() == 'line') $('#geoshape').val('select');
				else if ($('#geoshape').val() != 'select') {
                    if ($('#geoshape').val()=="arc") $('#geoshape').val("select");
	                if (newNode) callEndDrag(svg, newNode);
	            } else {
	                $('#canvasContainer').trigger('graph_change');
	                //$('#canvasContainer').focus();
	            }
	        });
        }).mousemove(function (e) {
			e.preventDefault();
            var outLine = sketchHelper.dragging(e, svg, $('#geoshape').val());closeAllTooltips();
            callMouseDrag(svg, outLine);
        }).mouseup(function (e) {
            var newNode = sketchHelper.endDrag(e, svg, $('#geoshape').val());
            if ($('#geoshape').val()!="select") {
				if ($('#geoshape').val()=="arc") $('#geoshape').val("select");
                if (newNode) callEndDrag(svg, newNode);
            } else {
                $('#canvasContainer').trigger('graph_change');
                $('#canvasContainer').focus();
            }
        });
	}
    function FormSketchpad(svg) {
        sketchpad = svg;
        var surface = svg.rect(0, 0, '100%', '100%', { id: 'surface', fill: '#d4e3fc', opacity: '1' });
        viewport = svg.group('viewport');
    }

    _geometry.clearCanv = function () {
        //would keep history
        var svg = $('#canvasContainer').svg('get');
        sketchHelper.clearCanv(svg);
        $('#canvasContainer').focus();
    };

    return _geometry;
} ();

var geometry;
if (geometry == undefined) {
    geometry = Geometry;
}