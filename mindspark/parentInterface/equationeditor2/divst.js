
	var fracString,classD;
	
	var isCtrl = false;
	var isAlt = false;
	var isShift = false;
	var isSpace = false;
	var isUp = false;
	var isDown = false;
	var enteredChars=new String;
	var saveCurPos=0;
	
	document.onselectstart = function(){ return false; }
	
	var editableC=document.getElementById('in1');
	var editable;
	var edite;
	function oKeyUp(e) {
			keyD=false;
			setTimeout(keyUp(e),200);
		}
	function oKeyDown(e){
			//if (!keyD) 
			{setTimeout(keyDown(e),200);keyD=true;}
		}
	function oMouseUp(e){
			//getCaretPosition(1);
		}
	
	function loadDoc(){
		edite=document.getElementById('test');
		editel=edite.contentWindow;
		
		editable=editel.document.body;
		//editable=document.getElementById('test1');
		editable.designMode="On"
		
		editable.addEventListener("keyup", oKeyUp, false);
		editable.addEventListener("keydown", oKeyDown, false);
		//editable.addEventListener("mouseup", oMouseUp, false);
	}
	
	function resetKeys(){
		isAlt=false;
		isShift=false;
		isSpace=false;
		isCtrl=false;
	}
	
	var toggleDivCan=0; //0 for Div 1 for Canvas
	var selected0,selected1;
	function toggleDivCanF(){
		if (toggleDivCan){
				document.getElementById('sk1').style.zIndex=110;
				document.getElementById('imageTemp').style.zIndex=120;
				document.getElementById('savedIm').style.zIndex=130;
				editableC.style.zIndex=100;
				document.getElementById('tools1').style.display="inline";
				document.getElementById('tools0').style.display="none";
				if (!selected1) changeIcons('11a');
		}else{
				document.getElementById('sk1').style.zIndex=100;
				document.getElementById('imageTemp').style.zIndex=90;
				document.getElementById('savedIm').style.zIndex=80;
				document.getElementById('savedIm').style.display="none";
				editableC.style.zIndex=110;
				document.getElementById('tools1').style.display="none";
				document.getElementById('tools0').style.display="inline";
		}
		document.getElementById('categB'+toggleDivCan).style.backgroundColor="yellow";
	}
	function resetCol(){
		var syms = document.getElementsByName('toolb');
		for (i=0;i<syms.length;i++){
			syms[i].style.backgroundColor="#c4c4c4";
		}
		document.getElementById('tools11k').title=" Stroke width ";
		document.getElementById('stroke').innerHTML="Stroke width";
		document.getElementById('imageTemp').style.cursor="crosshair";
	}
	function changeIcons(a){
		if (a==0 || a==1){
			document.getElementById('categB0').style.backgroundColor="#c4c4c4";
			document.getElementById('categB1').style.backgroundColor="#c4c4c4";
			toggleDivCan=a;
			
			toggleDivCanF();
			return;
		}
		resetCol();
		if (a.substr(0,1)=="0"){
			selected0=a;
		}else if (a.substr(0,1)=="1"){
			selected1=a;
		}
		switch (a){
			case '11a'://pencil
					ev_tool_change('pencil');
				break;
			case '11b'://line
					ev_tool_change('line');
				break;
			case '11c'://rectangle
					ev_tool_change('rect');
				break;
			case '11d'://circle
					ev_tool_change('circ');
				break;
			case '11e'://triangle
					ev_tool_change('tria');
				break;
			case '11f'://eraser
					ev_tool_change('eraser');
					document.getElementById('imageTemp').style.cursor="url(erasing.gif),wait";
					document.getElementById('tools11k').title=" Eraser size ";
					document.getElementById('stroke').innerHTML="Eraser size";
				break;
			case '11g'://clearRect
					ev_tool_change('clr');
				break;
			case '11h'://clearAll
					ev_tool_change('cls');
				break;
			case '11l'://save
					ev_tool_change('text');
				break;
			case '01a'://&#176;
					setChar('&#176;',1);editable.focus();
				break;
			case '01b'://&#8730;
					setChar('&#8730;',1);editable.focus();
				break;
			case '01c'://&#177;
					setChar('&#177;',1);editable.focus();
				break;
			case '01d'://&#215;
					setChar('&#215;',1);editable.focus();
				break;
			case '01e'://&#247;
					setChar('&#247;',1);editable.focus();
				break;
			case '01f'://&#8756;
					setChar('&#8756;',1);editable.focus();
				break;
			case '01g'://&#8805;
					setChar('&#8805;',1);editable.focus();
				break;
			case '01h'://&#8804;
					setChar('&#8804;',1);editable.focus();
				break;
			case '01i'://&#8736;
					setChar('&#8736;',1);editable.focus();
				break;
			case '01j'://&#960;
					setChar('&#960;',1);editable.focus();
				break;
			case '01k'://&#960;
					var str=fracD("&nbsp;","&nbsp;");
					setChar(str,1);editable.focus();
				break;
			case '01l'://&#960;
					isUp=!isUp;isDown=false;command("superscript",false,null);
					subsup();editable.focus();return;
				break;
			case '01m'://&#960;
					isDown=!isDown;isUp=false;command("subscript",false,null);
					subsup();editable.focus();return;
				break;
		}
		document.getElementById('tools'+a).style.backgroundColor="yellow";
		
		return;
	}
	
	function addFracBox(){
		var str=fracD("&nbsp;","&nbsp;");
		setChar(str,1);
		isAlt=false;
	}
	function fracD(a,b){
		var str='<table border="0" class="fraction"><tbody><tr class="num"><td class="num" >'+a+'</td></tr><tr class="den"><td class="den" >'+b+'</td></tr></tbody></table>';
		return str;
	}
	var sel;
	var savedSel;
	var userInput;
	var cursorPos;
	
	function getRange(){
		var userSelection=editable;
		if (window.getSelection) {
			userSelection = window.getSelection();
		}
		var start = userSelection.anchorOffset;
		var end = userSelection.focusOffset;
		var range=[start,end];
		return range;
	}
	
	function showSelect(){
		var userSelection,node;
		if (window.getSelection) {
			userSelection = window.getSelection();
			node = userSelection.anchorNode;
		}
		if (node) {
     		 return (node.nodeName != "in1" ? node.parentNode : node);
    	}
		var start = userSelection.anchorOffset;
		var end = userSelection.focusOffset;
	}
	function command(cmd,bool,value) {
		var returnValue = edite.contentWindow.inBetween(cmd,bool,value);
		//var returnValue = inBetween(cmd,bool,value);
	}
	function setChar(charS,k){
		command('inserthtml',false,charS);
	}
	var timeF;
	
	function keyUp(e){
		if(e.which == 18) {
			isAlt=false;
		}
		if(e.which == 17) {
			isCtrl=false;
		}
		else if (e.which==32 || e.which==37 || e.which==38 || e.which==39 || e.which==40 || e.which==17 || e.which==16 || e.which==8 || e.which==46 || e.which==13 || e.which==9 || isCtrl){
			//replace();
		}else if (!isAlt) {
			edite.contentWindow.replaceC();
		}
	}
	function subsup(){
		if (isUp) {
			document.getElementById('tools01l').style.backgroundColor="yellow";
		}else if (isDown){
			document.getElementById('tools01m').style.backgroundColor="yellow";
		}
	}
	function keyDown(e){
		if(e.which == 17) {
			isCtrl=true; return false;
		}else if(e.which == 18) {
			isAlt=true; return false;
		}else if (e.which == 38 && isAlt){
			isUp=!isUp;isDown=false;command("superscript",false,null);resetCol();subsup();editable.focus();
		}else if (e.which == 40 && isAlt){
			isDown=!isDown;isUp=false;command("subscript",false,null);resetCol();subsup();editable.focus();
		}else if (e.which == 9){
			return false;
		}
		else{
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
				setChar(str1,0);
				return false;
			}
		}
	}
	var keyD=false;
	
	function min(a,b){
		mi=a<b?a:b;
		return mi;
	}
	function showAnswer(str){
		
		editable.innerHTML = str;		
	}
	
	function storeAnswer(dt){		
		return editable.innerHTML;		
		//dt is the image file name 
		/*var http = getHTTPObject();
        http.onreadystatechange=function(){
    		if(http.readyState==4){
				ajax_response=http.responseText;
				//alert(dt);
				//------------Get Next Question Here-------------
				//getNextQues();
				//------------End Get Next Question Here-------------
       		}
    	}
		//alert(encodeURIComponent(editable.innerHTML));
		var params="stData="+encodeURIComponent(editable.innerHTML);
			params+="&timeTaken="+secs;
			params+="&qpage="+document.getElementById('page').value;
			params+="&imID="+dt;
		
		var url="savedata.php?";
    	url=url + params;
		http.open("GET",url,true);
		http.send(null);*/
	}