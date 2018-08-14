/* Â© 2009 ROBO Design
 * http://www.robodesign.ro
 */

// Keep everything in anonymous function, called on window load.
var canvas, context, canvaso, contexto;

  // The active tool instance.
  var tool;
  var tool_default = 'line';
  
  var strokeCol = "#000000";
  var strokeWid = 1;
  var strokeStyle = "";
  // The general-purpose event handler. This function just determines the mouse 
  // position relative to the canvas element.

  // The event handler for any changes made to the tool selector.

  // This function draws the #imageTemp canvas on top of #imageView, after which 
  // #imageTemp is cleared. This function is called each time when the user 
  // completes a drawing operation.

  // This object holds the implementation of each drawing tool.
  
  // The drawing pencil.

  // The rectangle tool.
function restoreImage(data){
	var image = new Image();
	image.src = data;
	image.onload = function (){
		contexto.drawImage(image, 0, 0);
	}
}
  
function init1 () {
	
	// Find the canvas element.
	
    canvaso = document.getElementById('sk1');
    if (!canvaso) {
      alert('Error: I cannot find the canvas element!');
      return;
    }

    if (!canvaso.getContext) {
      alert('Error: no canvas.getContext!');
      return;
    }

    // Get the 2D canvas context.
    contexto = canvaso.getContext('2d');
    if (!contexto) {
      alert('Error: failed to getContext!');
      return;
    }

    // Add the temporary canvas.
    var container = canvaso.parentNode;
    canvas = document.createElement('canvas');
    if (!canvas) {
      alert('Error: I cannot create a new canvas element!');
      return;
    }

    canvas.id     = 'imageTemp';
    canvas.width  = canvaso.width;
    canvas.height = canvaso.height;
    container.appendChild(canvas);

    context = canvas.getContext('2d');
	toggleDivCanF();
    // Get the tool select input.
    //var tool_select = document.getElementById('dtool');
    //tool_select.addEventListener('change', ev_tool_change, false);

    // Activate the default tool.
    if (tools[tool_default]) {
      tool = new tools[tool_default]();
      //tool_select.value = tool_default;
    }

    // Attach the mousedown, mousemove and mouseup event listeners.
    canvas.addEventListener('mousedown', ev_canvas, false);
    canvas.addEventListener('mousemove', ev_canvas, false);
    canvas.addEventListener('mouseup',   ev_canvas, false);
  }

// vim:set spell spl=en fo=wan1croql tw=80 ts=2 sw=2 sts=2 sta et ai cin fenc=utf-8 ff=unix:

//init();
function img_update () {
		contexto.drawImage(canvas, 0, 0);
		context.clearRect(0, 0, canvas.width, canvas.height);
  }
  
function ev_canvas (ev) {
	//alert(ev.name);
    if (ev.layerX || ev.layerX == 0) { // Firefox
      ev._x = ev.layerX;
      ev._y = ev.layerY;
    } else if (ev.offsetX || ev.offsetX == 0) { // Opera
      ev._x = ev.offsetX;
      ev._y = ev.offsetY;
    }
	
	//ev._x = ev.offsetX;
    //ev._y = ev.offsetY;
    // Call the event handler of the tool.
	//alert(ev.type);
    var func = tool[ev.type];
    if (func) {
      func(ev);
    }
  }


function ev_tool_change (toolname) {
    if (tools[toolname]) {
      tool = new tools[toolname]();
    }
	//alert(this.value);
  }

function changeCanvProp(prop,val){
	if (prop=="color")	strokeCol=val;
	if (prop=="thick")	{strokeWid=val;document.getElementById('strokeH').innerHTML=val;}
	if (prop=="style")	strokeStyle=val;
	//alert(strokeCol);
}

var tools = {};

tools.pencil = function () {
    var tool = this;
    this.started = false;
	//alert(1);
    // This is called when you start holding down the mouse button.
    // This starts the pencil drawing.
    this.mousedown = function (ev) {
        context.beginPath();
        context.moveTo(ev._x, ev._y);
        tool.started = true;
		//alert(this);
    };

    // This function is called every time you move the mouse. Obviously, it only 
    // draws if the tool.started state is set to true (when you are holding down 
    // the mouse button).
    this.mousemove = function (ev) {
      if (tool.started) {
        context.lineTo(ev._x, ev._y);
		//context.strokeStyle=strokeCol;
		//context.lineWidth=strokeWid;
        context.stroke();
		//ev.preventDefault();
      }
    };

    // This is called when you release the mouse button.
    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
        img_update();
      }
    };
  };

function updateText(tx,ty,count){
	context.clearRect(0, 0, canvas.width, canvas.height);
	switch (count){
		case 0:{
			var tb = document.getElementById('textTemp');
			(tb.parentNode).removeChild(tb);
			break;
		}
		case 1:{
			var str=document.getElementById('canvtext').value;
			context.fillText(str, tx, ty);
			var tb = document.getElementById('textTemp');
			(tb.parentNode).removeChild(tb);
        	img_update();
			break;
		}
		case 2:{
			var str=document.getElementById('canvtext').value;
			context.fillText(str, tx, ty);break;
		}
	}
}

tools.text = function () {
    var tool = this;
    this.started = false;
	var tx, ty=0;
	var tb=null;

    // This is called when you start holding down the mouse button.
    // This fixes the text position.
    this.mousedown = function (ev) {
        //context.beginPath();
		
		var tb = document.getElementById('textTemp');
			if (tb)	(tb.parentNode).removeChild(tb);
		
		tx=ev._x;ty=ev._y;
		var container = canvaso.parentNode;
    	tb = document.createElement('div');
		tb.id     = 'textTemp';
		tb.innerHTML="<input type='text' id='canvtext' autofocus size='10' onkeyup='updateText("+tx+","+ty+",2)'/><br><input type='button' value='OK' onclick='updateText("+tx+","+ty+",1);' /><input type='button' value='Cancel' onclick='updateText("+tx+","+ty+",0);'>"
		tb.style.top = ty+"px";
		tb.style.left = tx+"px";
    	container.appendChild(tb);
		if (!("autofocus" in document.createElement("input"))) {
      			document.getElementById("canvtext").focus();
    	}
		//alert(this);
    };
  };


  
  tools.eraser = function () {
    var tool = this;
    this.started = false;

    // This is called when you start holding down the mouse button.
    // This starts the pencil drawing.
    this.mousedown = function (ev) {
        tool.started = true;
		//alert(this);
    };

    // This function is called every time you move the mouse. Obviously, it only 
    // draws if the tool.started state is set to true (when you are holding down 
    // the mouse button).
    this.mousemove = function (ev) {
      if (tool.started) {
		contexto.clearRect(ev._x-strokeWid, ev._y-strokeWid, 2*strokeWid, 2*strokeWid);
		context.clearRect(ev._x-strokeWid, ev._y-strokeWid, 2*strokeWid, 2*strokeWid);
      }
    };

    // This is called when you release the mouse button.
    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
        img_update();
      }
    };
  };



tools.rect = function () {
    var tool = this;
    this.started = false;

    this.mousedown = function (ev) {
      tool.started = true;
      tool.x0 = ev._x;
      tool.y0 = ev._y;
    };

    this.mousemove = function (ev) {
      if (!tool.started) {
        return;
      }

      var x = Math.min(ev._x,  tool.x0),
          y = Math.min(ev._y,  tool.y0),
          w = Math.abs(ev._x - tool.x0),
          h = Math.abs(ev._y - tool.y0);

      context.clearRect(0, 0, canvas.width, canvas.height);

      if (!w || !h) {
        return;
      }
	  context.strokeStyle=strokeCol;
	  context.lineWidth=strokeWid;
      context.strokeRect(x, y, w, h);
    };

    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
        img_update();
      }
    };
  };



 tools.line = function () {
    var tool = this;
    this.started = false;

    this.mousedown = function (ev) {
      tool.started = true;
      tool.x0 = ev._x;
      tool.y0 = ev._y;
    };

    this.mousemove = function (ev) {
      if (!tool.started) {
        return;
      }

      context.clearRect(0, 0, canvas.width, canvas.height);

      context.beginPath();
      context.moveTo(tool.x0, tool.y0);
      context.lineTo(ev._x,   ev._y);
	  context.strokeStyle=strokeCol;
	  context.lineWidth=strokeWid;
      context.stroke();
      context.closePath();
    };

    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
        img_update();
      }
    };
  };
  
  tools.circ = function () {
    var tool = this;
    this.started = false;

    this.mousedown = function (ev) {
      tool.started = true;
      tool.x0 = ev._x;
      tool.y0 = ev._y;
    };

    this.mousemove = function (ev) {
      if (!tool.started) {
        return;
      }

      var x = tool.x0,
          y = tool.y0,
          w = Math.abs(ev._x - x),
          h = Math.abs(ev._y - y),
		  r = Math.sqrt(h*h + w*w);
	  

      if (!w || !h) {
        return;
      }
	  context.clearRect(0, 0, canvas.width, canvas.height);
	  context.beginPath();
	  context.strokeStyle=strokeCol;
	  context.lineWidth=strokeWid;
	  context.moveTo(x+r,y);
	  context.arc(x, y, r, 0, Math.PI*2, true);
      context.stroke();
    };

    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
        img_update();
      }
    };
  };

tools.tria = function () {
    var tool = this;
    this.started = false;
	var points = 0;
    this.mousedown = function (ev) {
      if (points>=3) points=0;
	  tool.started = true;
    };

    this.mousemove = function (ev) {
      if (!tool.started) {
        return;
      }
	  context.clearRect(0, 0, canvas.width, canvas.height);
	  context.strokeStyle=strokeCol;
	  context.lineWidth=strokeWid;
	  if (points == 1){
	  	context.beginPath();
      	context.moveTo(tool.x1, tool.y1);
      	context.lineTo(ev._x,   ev._y);
	  	context.strokeStyle=strokeCol;
	  	context.lineWidth=strokeWid;
      	context.stroke();
      	context.closePath();
		
	  }else if (points == 2){
	  	context.beginPath();
      	context.moveTo(tool.x1, tool.y1);
      	context.lineTo(ev._x,   ev._y);
		context.lineTo(tool.x2,   tool.y2);
		context.lineTo(tool.x1, tool.y1);
	  	context.strokeStyle=strokeCol;
	  	context.lineWidth=strokeWid;
      	context.stroke();
      	context.closePath();
	  }
    };

    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
		points++;
		eval ("tool.x"+points + " = ev._x;");
	  	eval ("tool.y"+points + " = ev._y;");
		if (points>=3){tool.started = false; img_update(); return;}
      }
    };
  };


  
  tools.save = function () {
    var tool = this;
    this.started = false;
  	// Extract the Base64 data from the canvas and post it to the server
    base64 = canvaso.toDataURL("image/png");
	return(base64);
	//fn=submitTime;
	/*var ajax = new XMLHttpRequest();
	ajax.onreadystatechange=function(){
    		if(ajax.readyState==4){
				ajax_response=ajax.responseText;
				document.getElementById('savedIm').src=ajax_response;
				document.getElementById('savedIm').width="60px";
				document.getElementById('savedIm').height="40px";
				document.getElementById('savedIm').style.display="block";
				startFade();
				storeAnswer(ajax_response);
       		}
    	}
	
	ajax.open("POST",'uploadF.php',false);
	ajax.setRequestHeader('Content-Type', 'application/upload');
	ajax.send(base64);*/
  };
  
  tools.cls = function () {
    var tool = this;
    this.started = false;
	contexto.clearRect(0, 0, canvaso.width, canvaso.height);
  };
  
  tools.clr= function () {
    var tool = this;
    this.started = false;
	var x,y,w,h;
		  
    this.mousedown = function (ev) {
      tool.started = true;
      tool.x0 = ev._x;
      tool.y0 = ev._y;
    };

    this.mousemove = function (ev) {
      if (!tool.started) {
        return;
      }

      	  x = Math.min(ev._x,  tool.x0);
          y = Math.min(ev._y,  tool.y0);
          w = Math.abs(ev._x - tool.x0);
          h = Math.abs(ev._y - tool.y0);

      context.clearRect(0, 0, canvas.width, canvas.height);

      if (!w || !h) {
        return;
      }
	  context.strokeStyle=strokeCol;
	  context.lineWidth=strokeWid;
      context.strokeRect(x, y, w, h);
    };

    this.mouseup = function (ev) {
      if (tool.started) {
        tool.mousemove(ev);
        tool.started = false;
        contexto.clearRect(x-strokeWid, y-strokeWid, w+2*strokeWid, h+2*strokeWid);
		context.clearRect(x-strokeWid, y-strokeWid, w+2*strokeWid, h+2*strokeWid);
      }
    };
  };

var fader=false;
var TimerID;
function startFade(){
	if (!fader) {fader=true;	TimerID=self.setTimeout("startFade()",3000);}
	else {document.getElementById('savedIm').style.display="none";clearTimeout(TimerID);fader=false;}
}