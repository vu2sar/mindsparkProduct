
	var fracString,classD;
	
	var isCtrl = false;
	var isAlt = false;
	var isShift = false;
	var isSpace = false;
	var isUp = false;
	var isDown = false;
	var enteredChars=new String;
	var saveCurPos=0;
	var par=null;
	var params = {};
	document.onselectstart = function(){ return false; }
	
	var editableC=document.getElementById('in1');
	var editable;
	var edite,editel;
	
	var re00 = /<[ib]{1}>|<\/[ib]{1}>|<img[^<]*?>|<\/img>|<span[^<]*?>|<\/span>|<p[^<]*?>|<\/p>|<font[^<]*?>|<\/font>/ig;
	var re1a = /<sup[^>]*>/ig;
	var re1b = /<sub[^>]*>/ig;
	var re1c = /<\/sup>([ ]?)|<\/sub>([ ]?)/ig;
	var re3 = /(^|[^a-zA-Z])([a-z])((?![a-zA-Z]))/g;
	var re5 = /&nbsp;/ig;
	var re5W = /&nbsp;/g;
	var re5i = /[ ]{2}/g;
	var re5e = /[ ]$/g;
	var re6 = /([a-z]|span>)([0-9])((?![0-9]))/ig;
	var re7 = /([a-z0-9>]+[^<])\/((?=[a-z0-9<]+))/ig;
	
	var re8 = /<pre[^<]*?>([^()]*)<\/pre>/ig;
	var re9 = /\[tab\]/g;
	var re00d = /<hr[^<]*?class="tab"[^<]*?>/ig //Match tabs. Replace with [$1]
	var re07 = /(\[tab\])+/g; //Replace with <hr class="tab"/>
	var re07a = /\[tab\] /g; //Replace with <hr class="tab"/>
	var ref1 = /<table[^<]*?class="fraction[^<]*?><tbody[^<]*?><tr[^<]*?class="num[^<]*?><td[^<]*?class="num[^<]*?>/ig;
	var ref2 = /<\/td><\/tr><tr[^<]*?class="den[^<]*?><td[^<]*?class="den[^<]*?>/ig;
	var ref3 = /<\/td><\/tr><\/tbody><\/table>[ ]?/ig;
	
	var reBr = /<br[^<]*?>/ig;
	var reBr2 = /<br><br>/ig;
	var remJunk = /<head>[^]*?<\/head>/ig;
	var remAll = /<[^<]+?>/g;
	var remAllE = /<(?!br|body|\/body)[^<]+?>/ig;
	//var remAllE = /<(?!table|tbody|tr|td|br|\/body|\/table|\/tbody|\/tr|\/td)[^<]+?>/ig;
	var remAllH = /<(?=html|body|\/html|\/body)[^<]+?>|<xml>(.*)<\/xml>|<style>(.*)<\/style>/ig;
	var remAllS = /<([^<]*?)style="[^<]*?"([^<]*?)>/ig;
	var allnonCh= /\s|&nbsp;|&zwj;|<br[^<]*?>/ig;
	
	
	var helpCanv, helpCtx;
	var helpState=0;
	var helpPlay=0,helpCanvStep=0;
	var editorMode=new Array(0,0);
	
	var editorHelpMsg = {
		"editor":{
			"m1":{
				"msg":"Click here to begin typing.",
				"style":{top:"80px", left:"40px", fontSize:"16px"},
				"arrs":{
					"a1": {"stx":100,"sty":80,"sx":60,"sy":40,"ex":25,"ey":28}
				},
				"oText":{
					"msg":"2 + 3x = 30",
					"style":{fontSize:"16px"},
					"delay": 150
				},
				"duration": 4500
			},
			"m2":{
				"msg":"Click a button in the buttons panel to insert a symbol here.",
				"style":{top:"160px", left:"350px", fontSize:"16px",width:"250px"},
				"arrs":{
					"a1": {"stx":405,"sty":167,"sx":460,"sy":140,"ex":620,"ey":108},
					"a2": {"stx":500,"sty":200,"sx":250,"sy":300,"ex":90,"ey":28}
				},
				"oText":{
					"msg":"2 + 3x = 30~°",
					"style":{fontSize:"16px"},
					"delay": 2500,
					"type" : 1
				},
				"duration": 5000
			},
			"m3":{
				"msg":"Press ENTER and continue typing in a new line.",
				"style":{top:"120px", left:"40px", fontSize:"16px",width:"300px"},
				"arrs":{
					"a1": {"stx":100,"sty":120,"sx":60,"sy":80,"ex":50,"ey":48}
				},
				"oText":{
					"msg":"2 + 3x = 30°~3 + 3√3 = 3(1 + √3)",
					"style":{fontSize:"16px"},
					"delay": 150,
					"type" : 1,
					"newline":1
				},
				"duration": 5000
			},
			"m4":{
				"msg":"To enter a fraction, just write the (numerator)/(denominator).",
				"style":{top:"160px", left:"40px", fontSize:"16px",width:"400px"},
				"arrs":{
					"a1": {"stx":100,"sty":160,"sx":60,"sy":120,"ex":50,"ey":78},
				},
				"oText":{
					"msg":"2 + 3x = 30°<br>3 + 3√3 = 3(1 + √3)~4 + 3/4 = 16/4 + 3/4 = (16 + 3)/4 = 19/4",
					"style":{fontSize:"16px"},
					"delay": 150,
					"type" : 1,
					"newline":1
				},
				"duration": 9000
			},
			"m5":{
				"msg":"You could also use the Fraction button here.",
				"style":{top:"160px", left:"40px", fontSize:"16px",width:"400px"},
				"arrs":{
					"a1": {"stx":280,"sty":184,"sx":300,"sy":240,"ex":625,"ey":348}
				},
				"oText":{
					"msg":"2 + 3x = 30°<br>3 + 3√3 = 3(1 + √3)<br>4 + 3/4 = 16/4 + 3/4 = (16 + 3)/4 = 19/4~ = 4(3)/4",
					"style":{fontSize:"16px"},
					"delay": 10,
					"type" : 1
				},
				"duration": 6500
			},
			"m6":{
				"msg":"Write a number after a variable to automatically raise the variable it to the power.",
				"style":{top:"160px", left:"40px", fontSize:"16px",width:"400px"},
				"arrs":{
					"a1": {"stx":280,"sty":160,"sx":200,"sy":120,"ex":80,"ey":113}
				},
				"oText":{
					"msg":"2 + 3x = 30°<br>3 + 3√3 = 3(1 + √3)<br>4 + 3/4 = 16/4 + 3/4 = (16 + 3)/4 = 19/4 = 4(3)/4~x2 +y2 +z2",
					"style":{fontSize:"16px"},
					"delay": 800,
					"type" : 1,
					"newline":1
				},
				"duration": 12000
			},
			"m7":{
				"msg":"Or you could also use the ^ symbol to raise any term to a power.",
				"style":{top:"160px", left:"40px", fontSize:"16px",width:"450px"},
				"arrs":{
					"a1": {"stx":280,"sty":160,"sx":200,"sy":125,"ex":100,"ey":120}
				},
				"oText":{
					"msg":"2 + 3x = 30°<br>3 + 3√3 = 3(1 + √3)<br>4 + 3/4 = 16/4 + 3/4 = (16 + 3)/4 = 19/4 = 4(3)/4<br>x2 +y2 +z2+~(3+x)^2",
					"style":{fontSize:"16px"},
					"delay": 500,
					"type" : 1
				},
				"duration": 8000
			},
			"m8":{
				"msg":"Close the tutorial and start exploring for yourself. Or click Replay to view this tutorial again.",
				"style":{top:"180px", left:"40px", fontSize:"16px",width:"450px"},
				"arrs":{
					"a1": {"stx":240,"sty":200,"sx":280,"sy":320,"ex":610,"ey":382}
				},
				"oText":{
					"msg":"2 + 3x = 30°<br>3 + 3√3 = 3(1 + √3)<br>4 + 3/4 = 16/4 + 3/4 = (16 + 3)/4 = 19/4 = 4(3)/4<br>x2 +y2 +z2+(3+x)^2<br>~You have completed the tutorial!",
					"style":{fontSize:"16px"},
					"delay": 100,
					"type" : 1,
					"newline":1
				},
				"duration": 6000
			}
		},
		"canvas":{
			"m1":{
				"msg":"Select the pencil tool.<br><br>Now draw on the white canvas area.<br><br> <br><br> <br><br> <br><br>",
				"style":{top:"160px", left:"340px", fontSize:"16px",width:"250px"},
				"arrs":{
					"a1": {"stx":420,"sty":167,"sx":460,"sy":140,"ex":620,"ey":108}
				},
				"oCanv":{
					"steps":{
						"s1":"pause,2000",
						"s2":"move,300,200,1000",
						"s3":"line,295,195,50",
						"s4":"line,290,192,50",
						"s5":"line,280,190,50",
						"s6":"line,270,190,50",
						"s7":"line,255,200,100",
						"s8":"line,247,212,50",
						"s9":"line,245,220,50",
						"s10":"line,250,236,50",
						"s11":"line,254,240,50",
						"s12":"line,260,245,50",
						"s13":"line,268,250,50",
						"s14":"line,272,252,50",
						"s15":"line,278,252,50",
						"s16":"line,280,250,50",
						"s17":"line,290,245,50",
						"s18":"line,298,238,50"
					}
				},
				"duration": 6000
			},
			"m2":{
				"msg":"Select the Line tool.<br><br>Now draw a line on the canvas area.<br><br>",
				"style":{top:"160px", left:"340px", fontSize:"16px",width:"250px"},
				"arrs":{
					"a1": {"stx":420,"sty":167,"sx":460,"sy":140,"ex":670,"ey":114}
				},
				"oCanv":{
					"steps":{
						"s1":"pause,2000",
						"s2":"move,300,200,2000",
						"s3":"move,200,100,1000",
						"s4":"line,290,140,500",
						"s5":"line,287,130,200",
						"s6":"move,290,140,200",
						"s7":"line,283,144,200",
						"s8":"move,300,200,200"
					}
				},
				"duration": 7000
			},
			"m3":{
				"msg":"Let's make a circle. Select the Circle tool. Now press down the mouse at a point on the canvas. This is the centre of the circle. <br><br>Now drag the mouse by a distance(radius). Leave the mouse to draw your circle.",
				"style":{top:"130px", left:"250px", fontSize:"16px",width:"350px"},
				"arrs":{
					"a1": {"stx":520,"sty":147,"sx":580,"sy":170,"ex":670,"ey":144}
				},
				"oCanv":{
					"steps":{
						"s1":"pause,4000",
						"s2":"move,150,100,4000",
						"s3":"move,151,100,4000",
						"s4":"move,225,100,1000",
						"s5":"circle,150,100,75,500"
					}
				},
				"duration":15000
			},
			"m4":{
				"msg":"Use the eraser tool to erase your drawing.<br><br>The clear tools can clear a part or the entire canvas area.<br><br>Add text using the Text tool. ",
				"style":{top:"80px", left:"250px", fontSize:"16px",width:"350px"},
				"arrs":{
					"a1": {"stx":330,"sty":80,"sx":500,"sy":-20,"ex":670,"ey":194,"delay":200},
					"a2": {"stx":360,"sty":143,"sx":520,"sy":150,"ex":670,"ey":234,"delay":5000},
					"a3": {"stx":360,"sty":143,"sx":520,"sy":150,"ex":625,"ey":234,"delay":5000},
					"a4": {"stx":450,"sty":203,"sx":520,"sy":190,"ex":675,"ey":284,"delay":10000}
				},
				"duration":15000
			},
			"m5":{
				"msg":"You can change the colour or the thickness of you pencil using these tools. ",
				"style":{top:"80px", left:"250px", fontSize:"16px",width:"350px"},
				"arrs":{
					"a1": {"stx":350,"sty":120,"sx":500,"sy":150,"ex":625,"ey":294,"delay":200},
					"a2": {"stx":350,"sty":120,"sx":520,"sy":150,"ex":625,"ey":348,"delay":200}
				},
				"duration":7000
			},
			"m6":{
				"msg":"Congratulations! You have completed the canvas tutorial. Use the tools to make figures that would help you solve questions. <br><br>Close the tutorial and start exploring for yourself. Or click Replay to view this tutorial again.",
				"style":{top:"80px", left:"20px", fontSize:"16px",width:"500px"},
				"arrs":{
					"a1": {"stx":200,"sty":170,"sx":280,"sy":320,"ex":610,"ey":382,"delay":4000}
				},
				"oCanv":{
					"steps":{
						"s1":"move,300,200,4000"
					}
				},
				"duration":7000
			},
		}		
	};
	function oKeyUp(e) {keyD=false;setTimeout(function(){keyUp(e);},30);}
	function oKeyDown(e){editel.focus();setTimeout(function(){keyDown(e);},30);}
	function oMouseUp(e){}
	function oClick(e){setHLite();}
	function oBlur(e){isAlt=false;isCtrl=false;}
	function startHelp(n){
		$("#helpBox").show();
		$("#closeCtrl").css({display:"block"});
		var sx,sy,ex,ey;
		helpCanv=document.getElementById('helpCanv');
		helpCtx=helpCanv.getContext("2d"); helpCanv.width=helpCanv.width;
		if (n==1) {
			tut="Sketchpad";sx=380;sy=350;ex=665;ey=45;$("#crossCur").css({"display":"block"});
		}
		else {
			tut="Editor";sx=380;sy=350;ex=615;ey=40;$("#crossCur").css({"display":"none"});
		}
		$("#demoEdit").html("");
		$("#helpText1").css({top:"180px", left:"180px", fontSize:"20px", width:"400px"});
		$("#helpText1").html("Welcome to the "+tut+" Tutorial!");
		
		helpCtx.beginPath();		helpCtx.fillStyle = "rgba(0, 0, 0,1)";
		helpCtx.moveTo(350,210);helpCtx.quadraticCurveTo(sx, sy, ex, ey);helpCtx.stroke();
		var ang = findAngle(sx, sy, ex, ey);
		//helpCtx.fillRect(ex, ey, 2, 2);
		drawArrowhead(ex, ey, ang, 12, 12, helpCtx);
		$("#closeCtrl").css({backgroundPosition:"-68px 0px"});
		helpState=0;
		editorMode[0]=n;editorMode[1]=0;
	}
	function demoCanv(drawObj){
		var s=drawObj.steps;
		if (helpCanvStep==Object.keys(s).length) return;
		helpCanvStep++;
		step=eval("s.s"+helpCanvStep);
		//$.each(steps,function (i, step){
		step=step.split(",");
		switch (step[0]){
			case "pause":{
				setTimeout(function (){demoCanv(drawObj)},Number(step[1]));break;
			}
			case "move":{
				//console.log("move");
				$("#crossCur").animate({"top":(Number(step[2])-12)+"px","left":(Number(step[1])-12)+"px"},Number(step[3]),function (){
					helpCtx.moveTo(Number(step[1]),Number(step[2]));
					demoCanv(drawObj);
				});break;
			}
			case "line":{
				$("#crossCur").animate({"top":(Number(step[2])-12)+"px","left":(Number(step[1])-12)+"px"},Number(step[3]),function (){
					helpCtx.lineTo(Number(step[1]),Number(step[2]));helpCtx.stroke();
					demoCanv(drawObj);
				});break;
			}
			case "circle":{
				setTimeout(function (){
					helpCtx.arc(Number(step[1]),Number(step[2]), Number(step[3]), 0, Math.PI*2, true);helpCtx.stroke();
					demoCanv(drawObj);
				},Number(step[4]));break;
			}
		}
	}
	function demoEditor(textObj){
		var m=textObj.msg,delay=textObj.delay,n,o;
		$("#demoEdit").css(textObj.style);
		var t=textObj.type;m=m.split('~');
		if (m.length==1) {o=""; n=m[0];}
		else {o=m[0];n=m[1];}//m.split('~')[0];//$("#demoEdit").html().substr(0,$("#demoEdit").html().length-1);
		if (textObj.newline) o+="<br>";
		$.each(n.split(''), function(i, letter){
	        setTimeout(function(){
				if (t==1) $("#demoEdit").html(fracConvert(o+n.substr(0,i))+"|");
				else $("#demoEdit").html(fracConvert(n.substr(0,i))+"|");
	        }, delay * i);
	    });
		setTimeout(function(){
				if (t==1) $("#demoEdit").html(fracConvert(o+n.substr(0))+"|");
				else $("#demoEdit").html(fracConvert(n.substr(0))+"|");
	      	}, delay * n.length);
	}
	function playingHelp(){
		if (helpState==1){
			switch (editorMode[0]){
				case 0:{
					$("#helpText1").fadeOut(1000);
					$("#helpCanv").fadeOut(1000, function() {
						helpCanv.width=helpCanv.width;
						editorMode[1]++;
						//console.log("editor:"+editorMode[1]+" of "+ Object.keys(editorHelpMsg.editor).length);
						var mvar=eval("editorHelpMsg.editor.m"+(editorMode[1]));
						var msgs=mvar.msg.split('<br><br>').length;$("#helpText1").html("");
						$.each(mvar.msg.split('<br><br>'), function(i, letter){
							setTimeout(function(){
								$("#helpText1").html($("#helpText1").html()+letter+"<br><br>");
					        }, mvar.duration/msgs * i);
						});
						$("#helpText1").css(mvar.style);
						$("#helpText1,#helpCanv").fadeIn(1000);
						$.each(mvar.arrs, function (key, value){
							var d=300;
							if (value.delay) d=value.delay;
							setTimeout(function (){drawArrow(value);},d);
						});
						if (mvar.oText){
							demoEditor(mvar.oText);
						}
						setTimeout(function(){playingHelp();},mvar.duration);
						if (editorMode[1]==Object.keys(editorHelpMsg.editor).length) {
							$("#closeCtrl").css({backgroundPosition:"68px 0px"});
							$("#closeCtrl").css({display:"block"});
							helpState=2;editorMode[1]=0;
							//return;
						}
					});
					break;
				}
				case 1:{
					$("#helpText1").fadeOut(1000);
					$("#helpCanv").fadeOut(1000, function() {
						helpCanv.width=helpCanv.width;
						editorMode[1]++;
						//console.log("canvas:"+editorMode[1]+" of "+ Object.keys(editorHelpMsg.canvas).length);
						var mvar=eval("editorHelpMsg.canvas.m"+(editorMode[1]));
						var msgs=mvar.msg.split('<br><br>').length;$("#helpText1").html("");
						$.each(mvar.msg.split('<br><br>'), function(i, letter){
							setTimeout(function(){
								$("#helpText1").html($("#helpText1").html()+letter+"<br><br>");
					        }, mvar.duration/msgs * i);
						});
						$("#helpText1").css(mvar.style);
						$("#helpText1,#helpCanv").fadeIn(1000);
						$.each(mvar.arrs, function (key, value){
							var d=300;
							if (value.delay) d=value.delay;
							setTimeout(function (){drawArrow(value);},d);
						});
						if (mvar.oCanv){
							helpCanvStep=0;
							demoCanv(mvar.oCanv);
						}
						setTimeout(function(){playingHelp();},mvar.duration);
						if (editorMode[1]==Object.keys(editorHelpMsg.canvas).length) {
							$("#closeCtrl").css({backgroundPosition:"68px 0px"});
							$("#closeCtrl").css({display:"block"});
							helpState=2;editorMode[1]=0;
							//return;
						}
					});
				}
			}
		}
	}
	// returns radians
	function findAngle(sx, sy, ex, ey) {
	    // make sx and sy at the zero point
		var diffx=(ex - sx);var diffy=(ey - sy);
		var slope=diffy / diffx;
		var angle=(slope<0 || (diffx>0 && diffy>0))?Math.atan((ey - sy) / (ex - sx)):Math.atan((ey - sy) / (ex - sx))+Math.PI;
	    return angle;
	}
	function drawArrow(arr){
		//helpCanv.width=helpCanv.width;
		helpCtx.beginPath();
		helpCtx.moveTo(arr.stx,arr.sty);
		helpCtx.quadraticCurveTo(arr.sx, arr.sy, arr.ex, arr.ey);
		helpCtx.stroke();
		var ang = findAngle(arr.sx, arr.sy, arr.ex, arr.ey);
		drawArrowhead(arr.ex, arr.ey, ang, 12, 12, helpCtx);
	}
	function drawArrowhead(locx, locy, angle, sizex, sizey, ctx) {
	    var hx = sizex / 1.2;
	    var hy = sizey / 2;
		ctx.translate((locx ), (locy));
		//if(angle<0) 
		ctx.rotate(angle);
		//else ctx.rotate(angle-Math.PI);
		ctx.translate(-hx,-hy);
		//ctx.beginPath();	
		ctx.moveTo(0,0); ctx.lineTo(1*hx,1*hy);ctx.lineTo(0,1*sizey);
		//ctx.moveTo(0,0);    		ctx.closePath();	
		ctx.stroke();	//ctx.fill();
		ctx.translate(hx,hy);
		//if(angle<0) 
		ctx.rotate(-angle);
		//else ctx.rotate(-angle+Math.PI);
		ctx.translate((-locx ), (-locy));
	}


	function setFunctionsOnEditor(){
		editable.contentEditable = true;	// Op, IE, Saf
		editable.innerHTML="";
		editable.spellcheck = false;
		editable.focus();
		editel.document.addEventListener("paste", getPaste, false);
		editel.document.addEventListener("drop", getDrop, false);
		
		try{
			editel.document.execCommand('enableInlineTableEditing',null,false);
			editel.document.execCommand('enableObjectResizing',null,false);
		}catch(err){}
		
		$('#test').contents().find('head').append('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=9"><link href="divst.css" rel="stylesheet" type="text/css">');
		$('#test').contents().find('head').append('<style></style>').children('style').text('body{font-family:serif;}div#header {top: 0;position: absolute;padding-left: 0;}span#cursor {display: inline-block;width: 2px;background-color: #000000;}.empty{border:1px dotted black;}');
		getCursor();
	}
	function getDrop(e){
		/*var text = fracConvert(e.dataTransfer.getData("text/html"));
		text=(text.indexOf("<body")>=0)?text.substring(text.indexOf("<body"),text.indexOf("</body>")+7):text;
		//console.log("droppedText:"+text);document.execCommand("insertHTML", false, text);*/
		e.preventDefault();return false;
	}
	var pasteObj=null;
	function getPaste(e){
		/*var pastedText=e.clipboardData.getData('text/html');
		pastedText=(pastedText.indexOf("<body")>=0)?pastedText.substring(pastedText.indexOf("<body"),pastedText.indexOf("</body>")+7):pastedText;
		//console.log("pastedText:"+pastedText);
		pastedText=fracConvert(pastedText);pasteObj=e;document.execCommand("insertHTML", false, pastedText);*/
		e.preventDefault();
		return false;
	}
	function fracConvert(fracStr)
	{
		var fracString=fracStr;
		var fracArr=new Array();
		fracString = fracString.replace(remAllS, "<$1 $2>").replace(remAllH, "").replace(remJunk, "").replace(re5," ").replace(re00d, "[tab]").replace(re00, "").replace(re1a, "^(").replace(re1b, "_(").replace(re1c, ")").replace(reBr, "<br>").replace(remAllE, "");	
		
		//unrip
		//fracString=fracString.replace(/(^|[^a-z])([a-z])([0-9])/ig,"$1$2<sup>$3</sup>&nbsp;");
		fracString=fracString.replace(/\^([a-z0-9]+)|\^\(([^()]*)\)/ig,"<sup>$1$2</sup>&nbsp;").replace(/\^([a-z0-9]+)|\^\(([^()]*)\)/ig,"<sup>$1$2</sup>&nbsp;");	
		fracString=fracString.replace(/\_([a-z0-9]+)|\_\(([^()]*)\)/ig,"<sub>$1$2</sub>&nbsp;").replace(/\_([a-z0-9]+)|\_\(([^()]*)\)/ig,"<sub>$1$2</sub>&nbsp;");
		fracString = fracString.replace("\\","/");
		fracString=fracString.replace(/\(([^()]+)\)\/\(([^()]+)\)|\(([^()]*)\)\/([a-z0-9]+)|([a-z0-9]+)\/\(([^()]*)\)|([a-z0-9]+)\/([a-z0-9]+)/ig,"<table border=\"0\" class=\"fraction\"><tbody><tr class=\"num\"><td class=\"num\">$1$3$5$7</td></tr><tr class=\"den\"><td class=\"den\">$2$4$6$8</td></tr></tbody></table>&nbsp;");
		fracString=fracString.replace(/\{([^{}]+)\}\/\{([^{}]+)\}/ig,"<table border=\"0\" class=\"fraction\"><tbody><tr class=\"num\"><td class=\"num\">$1</td></tr><tr class=\"den\"><td class=\"den\">$2</td></tr></tbody></table>");
		fracString=fracString.replace(/\{\}\/\{([^{}]+)\}/ig,"/$1");fracString=fracString.replace(/\{\}\/\{([^{}]+)\}/ig,"/$1").replace(/\{([^{}]+)\}\/\{\}/ig,"$1/").replace(/\{\}\/\{\}/ig,"");
		fracString=fracString.replace(re07a, '<hr class="tab"/>&nbsp;');
		fracString=fracString.replace(re07, '<hr class="tab"/>');
		fracString=fracString.replace(re3, '$1<span class="var">$2</span>');
		//fracString=fracString.replace(re5i, '&nbsp;&nbsp;');
		fracString=fracString.replace(re5e, '&nbsp;');
		var fracRet=fracString.replace(reBr2, "<br>");
		return fracRet;
	}
	function replaceC()	
	{
		$('#test').contents().find('body td.num').each(function(index) {if ($(this).html().replace(allnonCh,'')=="") $(this).addClass('empty').html('&zwj;'); else $(this).removeClass('empty').html($(this).html().replace(/^&zwj;/,''));});
		$('#test').contents().find('body td.den').each(function(index) {if ($(this).html().replace(allnonCh,'')=="") $(this).addClass('empty').html('&zwj;'); else $(this).removeClass('empty').html($(this).html().replace(/^&zwj;/,''));});
		//$(editable).find('.empty').each(function(){$(this).html('&zwj;');});
		var content=editable.innerHTML;
		getCursor();
		content1=fracConvert(content);
		editable.innerHTML = content1;
		setCursor();
		setText(content1);
	}
	var el_body=null;var el_demo=null;
	function setCursor(){
		range.selectCharacters(editable, selStartOffset, selEndOffset);
		sel.setSingleRange(range);
		setHighlight();
	}
	var el,userInput,sel,range,selEndOffset,selStartOffset;
	function getCursor(){
		el = editable;
		userInput = el.textContent || el.innerText; 
		try{
			sel = rangy.getSelection(edite);
			range = sel.getRangeAt(0);
			// Get the text preceding the selection boundaries and then remove whitespace to get the character offsets
			var rangePrecedingBoundary = range.cloneRange();
				rangePrecedingBoundary.setStart(el, 0);
			selEndOffset = rangePrecedingBoundary.text().length;
				rangePrecedingBoundary.setEnd(range.startContainer, range.startOffset);
			selStartOffset = rangePrecedingBoundary.text().length;
				rangePrecedingBoundary.detach();
			//console.log(userInput,selStartOffset,selEndOffset);
		}
		catch(er){
			
		}
	}
	function setHLite(){
		var children=editable.getElementsByTagName('*');var s;
		$('#test').contents().find('body *').removeClass("highlight");
		sel = rangy.getSelection(edite);
		if (sel.anchorNode){
			s=(sel.anchorNode.parentNode.nodeName).toUpperCase();
			if (s!="HTML" && s!="BODY" && s!="DIV" && s!="BR") $(sel.anchorNode.parentNode).addClass("highlight");
		}
		else{editable.focus();}
	}
	function setHighlight(){
		var children=editable.getElementsByTagName('*');var s;
		$('#test').contents().find('body *').removeClass("highlight");
		s=(sel.anchorNode.parentNode.nodeName).toUpperCase();
		if (s!="HTML" && s!="BODY" && s!="DIV" && s!="BR") $(sel.anchorNode.parentNode).addClass("highlight");
	}
	function inBetween(command,bool,value) {
		var returnValue = null;
		try{returnValue = editel.document.execCommand(command,bool,value);}
		catch(err){
			var dv=editel.document.createElement('div');dv.innerHTML=value;var dvl=(dv.textContent || dv.innerText).length;
			range.pasteHtml(value);
			selEndOffset+=dvl;selStartOffset=selEndOffset;setCursor();
		}
		if (returnValue) return returnValue;
	}
	function loadDoc(){
		$('#helpBox').css('display',"none");
		edite=document.getElementById('test');

		editel=edite.contentWindow;
		
		editable=editel.document.body;
		editable.innerHTML="";
		editable.designMode="On";
		
		editel.document.addEventListener("keyup", oKeyUp, false);
		editel.document.addEventListener("keydown", oKeyDown, false);
		
		setFunctionsOnEditor();
		
		var query = window.location.search.substring(1);//window.location.search gives the the string from ? in the address bar.
		var vars = query.split("&");  //if multiple parameters passed
		for (var i=0;i<vars.length;i++){
			var pair = vars[i].split("=");
			params[pair[0]] = pair[1];
		}
		if(params.mode){eqmode(params.mode);}
		else{eqmode('all');} 
		$('#savedIm').css({'display':'none'});
		$('.symB').mouseover(function (){
			$(this).css({backgroundColor: '#c0dcc0'});
			$(this).mouseout(function (){$(this).css({backgroundColor: '#c4c4c4'});});
			$(this).mouseup(function (){$(this).css({backgroundColor: '#c0dcc0'});});
			$(this).mousedown(function (){$(this).css({backgroundColor: '#ffff00'});});
		});
		$('#tools0 .symB').attr("UNSELECTABLE","ON");
		$('#closeHelp').click(function (){helpState=0;editorMode[1]=0;$("#helpBox").hide();});
		
		$("#closeCtrl").mouseup(function (){
				if (helpState==0 || helpState==2){
					//play clicked
					$(this).css({backgroundPosition:"-204px 0px"});
					$(this).css({display:"none"});
					helpState=1;
					playingHelp();
				}
				else if (helpState==1){
					$(this).css({backgroundPosition:"-68px 0px"});
					helpState=2;
				}
		});
		$("#closeCtrl").mousedown(function (){
			$(this).css({backgroundPosition:"+=68px 0px"});
		});

		
	}
	function eqmode(mode){
		switch (mode){
			case 'canvas'	:
			case 'draw'		:
				{
					$('#tools0').css("display","none");$('#tools0Row').css("display","none");$('#categB0').css("display","none");
					$(editableC).css("display","none");$(editableC).css("zIndex","100");
					$('#tools1').css("display","table");$('#tools1Row').css("display","table-row");
					$('#sk1').css("zIndex","110");$('#imageTemp').css("zIndex","120");$('#savedIm').css("zIndex","130");
					toggleDivCan=1;
					break;
				}
			case 'editor'	:
			case 'write'	:
				{
					$('#tools0').css("display","table");$('#tools0Row').css("display","table-row");
					$('#tools1').css("display","none");$('#tools1Row').css("display","none");$('#categB1').css("display","none");
					$('#sk1').css("display","none");$('#savedIm').css("display","none");
					toggleDivCan=0;
					break;
				}
		}
		toggleDivCanF();
	}
	
	function resetKeys(){
		isAlt=false;isShift=false;isSpace=false;isCtrl=false;
	}
	function focusInDiv(){setTimeout(function(){$(editable).focus();},20);}
	var toggleDivCan=0; //0 for Div 1 for Canvas
	var selected0,selected1;
	function toggleDivCanF(){
		if (toggleDivCan){
			$('#sk1').css("zIndex","110");$('#imageTemp').css("zIndex","120");$('#savedIm').css("zIndex","130");
			$(editableC).css("zIndex","100");
			$('#tools0').css("display","none");$('#tools0Row').css("display","none");
			$('#tools1').css("display","table");$('#tools1Row').css("display","table-row");
			if (!selected1) changeIcons("11a");
		}else{
			$('#sk1').css("zIndex","100");$('#imageTemp').css("zIndex","90");$('#savedIm').css("zIndex","80");
			$(editableC).css("zIndex","110");
			$('#tools1').css("display","none");$('#tools1Row').css("display","none");
			$('#tools0').css("display","table");$('#tools0Row').css("display","table-row");
		}
		document.getElementById('categB'+toggleDivCan).style.backgroundColor="yellow";
	}
	function resetCol(){
		document.getElementById('tools11k').title=" Stroke width ";
		document.getElementById('stroke').innerHTML="Stroke width";
		document.getElementById('imageTemp').style.cursor="crosshair";
	}
	function changeIcons(a){
		if (a==0 || a==1){
			$('#categB0').css('backgroundColor',"#c4c4c4");
			$('#categB1').css('backgroundColor',"#c4c4c4");
			toggleDivCan=a;toggleDivCanF();return;
		}
		resetCol();
		if (a.substr(0,1)=="0"){selected0=a;}
		else if (a.substr(0,1)=="1"){selected1=a;}
		switch (a){
			case "11a"://pencil
					ev_tool_change('pencil');break;
			case "11b"://line
					ev_tool_change('line');break;
			case "11c"://rectangle
					ev_tool_change('rect');break;
			case "11d"://circle
					ev_tool_change('circ');break;
			case "11e"://triangle
					ev_tool_change('tria');break;
			case "11f"://eraser
					ev_tool_change('eraser');
					$('#imageTemp').css('cursor','none');$('#tools11k').attr('title'," Eraser size ");$('#stroke').html("Eraser size");break;
			case "11g"://clearRect
					ev_tool_change('clr');break;
			case "11h"://clearAll
					ev_tool_change('cls');break;
			case "11l"://save
					ev_tool_change('text');break;
			case "01a"://&#176;
					setChar("&#176;",1);focusInDiv();break;
			case "01b"://&#8730;
					setChar("&#8730;",1);focusInDiv();break;
			case "01c"://&#177;
					setChar("&#177;",1);focusInDiv();break;
			case "01d"://&#215;
					setChar("&#215;",1);focusInDiv();break;
			case "01e"://&#247;
					setChar("&#247;",1);focusInDiv();break;
			case "01f"://&#8756;
					setChar("&#8756;",1);focusInDiv();break;
			case "01g"://&#8805;
					setChar("&#8805;",1);focusInDiv();break;
			case "01h"://&#8804;
					setChar("&#8804;",1);focusInDiv();break;
			case "01i"://&#8736;
					setChar("&#8736;",1);focusInDiv();break;
			case "01j"://&#960;
					setChar("&#960;",1);focusInDiv();break;
			case "01k"://&#960;
					var str=fracD("&nbsp;","&nbsp;");setChar(str,1);focusInDiv();break;
			case "01l"://&#960;
					isUp=!isUp;isDown=false;command("superscript",false,null);focusInDiv();return;break;
			case "01m"://&#960;
					isDown=!isDown;isUp=false;command("subscript",false,null);focusInDiv();return;break;
		}
		return;
	}
	
	function addFracBox(){var str=fracD("&nbsp;","&nbsp;");setChar(str,1);isAlt=false;}
	function fracD(a,b){
		return '<table border="0" class="fraction"><tbody><tr class="num"><td class="num" >'+a+'</td></tr><tr class="den"><td class="den" >'+b+'</td></tr></tbody></table>';
	}
	var sel,savedSel,userInput,cursorPos;
	function command(cmd,bool,value) {var returnValue = inBetween(cmd,bool,value);}
	function setChar(charS,k){command('inserthtml',false,charS);replaceC();}
	var timeF;
	
	function keyUp(e){
		//alert(e.which);
		if(e.which == 18) {isAlt=false;}
		else if(e.which == 17) {isCtrl=false;}
		else if (e.which==9){}
		else if (isAlt && (e.which==38 || e.which==40)) {e.preventDefault();return;}
		else if (e.which==32 || e.which==37 || e.which==38 || e.which==39 || e.which==40 || e.which==17 || e.which==16 || e.which==46 || e.which==13 || isCtrl){setHLite();}
		else if (e.which==27){e.preventDefault();}
		else if (!isAlt || e.which==8) {replaceC();}
	}
	function setText(str){document.getElementById('backup').value=str;}
	function subsup(){
		if (isUp) {$('#tools01l').css('backgroundColor',"yellow");}
		else if (isDown){$('#tools01m').css('backgroundColor',"yellow");}
	}
	function keyDown(e){
		if(e.which == 17) {isCtrl=true; e.preventDefault(); return false;}
		else if(e.which == 18) {isAlt=true; e.preventDefault(); return false;}
		else if (e.which == 38 && isAlt){e.preventDefault();command("superscript",false,null);return;}
		else if (e.which == 40 && isAlt){e.preventDefault();	command("subscript",false,null);return;}
		else if (e.which == 9){e.preventDefault();setChar("<hr class='tab'/>&zwj;",0);}
		else if (e.which == 13){e.preventDefault();setChar("<br>&zwj;",0);}
		else if (isCtrl==true){if (e.which>=65 && e.which<=90) e.preventDefault();}
		else if (e.which!=27){
			str1="";
			if (isAlt){
				switch (e.which){
					case 48:	str1="&#960;";break;
					case 49:	str1="&#176;";break;
					case 50:	str1="&#8730;";break;
					case 51:	str1="&#177;";break;		
					case 52:	str1="&#215;";break;
					case 53:	str1="&#247;";break;
					case 54:	str1="&#8756;";break;
					case 55:	str1="&#8805;";break;
					case 56:	str1="&#8804;";break;		
					case 57:	str1="&#8736;";break;
					default:	str1="";
				}
				setChar(str1,0);return false;
			}
		}
		else{e.preventDefault();}
	}
	var keyD=false;
	function min(a,b){mi=a<b?a:b;return mi;}
	function showAnswer(str){
		editable.innerHTML = str;		
	}
	function storeAnswer(dt){		
		return editable.innerHTML;		
	}