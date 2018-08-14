
	var fracString,classD,isCtrl = false,isAlt = false,isShift = false,isSpace = false,isUp = false,isDown = false;
	var enteredChars=new String,saveCurPos=0,par=null,params = {};
	var editable,edite,selected0,selected1,sel,savedSel,userInput,cursorPos,timeF,keyD=false;
	var characterKeys=["&#960;","&#176;","&#8730;","&#177;","&#215;","&#247;","&#8756;","&#8805;","&#8804;","&#8736;"];
						//48, 		49, 	50, 	51,		52,			53,	   54,		55,			56, 	57
	document.onselectstart = function(){ return false; }
	function oKeyUp(e) {keyD=false;keyUp(e);}
	function oKeyDown(e){keyDown(e);}
	function oClick(e){edite.contentWindow.setHLite();}
	function oBlur(e){isAlt=false;isCtrl=false;}
	function loadDoc(){
		var query = window.location.search.substring(1);//window.location.search gives the the string from ? in the address bar.
		var vars = query.split("&");  //if multiple parameters passed
		for (var i=0;i<vars.length;i++){var pair = vars[i].split("=");params[pair[0]] = pair[1];}
		var wd=200,ht=100;
		if (params.width) wd=params.width;//$('body').css('width',params.width);
		if (params.height) ht=params.height;//$('body').css('height',params.height);
		
		edite=document.getElementById('test');
		editel=edite.contentWindow;
		editable=editel.document.body;
		$('body').css({'width':wd+'px','height':ht+'px'});
		$('#test').css({'width':(wd-4)+'px','height':(ht-4)+'px','border':'2px solid #018b1d;'});
		$(editable).css({'width':'100%','height':(ht-20)+'px','margin':'8px','min-height':'0px','overflow-x': 'hidden'});
		$(editable).html("");		
		editable.designMode="On"
		
		editable.addEventListener("keyup", oKeyUp, false);
		editable.addEventListener("keydown", oKeyDown, false);
		editable.addEventListener("click", oClick, false);
	}
	function resetKeys(){
		isAlt=false;
		isShift=false;
		isSpace=false;
		isCtrl=false;
	}
	function addFracBox(){
		var str=fracD("&nbsp;","&nbsp;");
		setChar(str,1);
		isAlt=false;
	}
	function fracD(a,b){
		var str='<table border="0" class="fraction"><tbody><tr class="num"><td class="num">'+a+'</td></tr><tr class="den"><td class="den">'+b+'</td></tr></tbody></table>';
		return str;
	}
	function command(cmd,bool,value) {var returnValue = edite.contentWindow.inBetween(cmd,bool,value);}
	function setChar(charS,k){command('inserthtml',false,charS);}
	function keyUp(e){
		if(e.which == 18) {isAlt=false;}
		else if(e.which == 17) {isCtrl=false;}
		else if (e.which==9){}
		else if (isAlt && (e.which==38 || e.which==40)) {e.preventDefault();return;}
		else if (e.which==32 || e.which==37 || e.which==38 || e.which==39 || e.which==40 || e.which==17 || e.which==16 || e.which==46 || e.which==13 || isCtrl){edite.contentWindow.setHLite();}
		else if (e.which==27){e.preventDefault();}
		else if (!isAlt || e.which==8) {edite.contentWindow.replaceC();}
	}
	function setText(str){$('#backup').html(str);}
	function keyDown(e){
		if(e.which == 17) {isCtrl=false; e.preventDefault(); return false;}
		else if(e.which == 18) {isAlt=false; e.preventDefault(); return false;}
		else if (e.which == 38 && isAlt){e.preventDefault();command("superscript",false,null);return;}
		else if (e.which == 40 && isAlt){e.preventDefault();command("subscript",false,null);return;}
		else if (e.which == 9){e.preventDefault();/*setChar('<hr class="tab"/>&nbsp;',0);*/}
		else if (e.which == 13){e.preventDefault();/*setChar('<br>&nbsp;',0);*/}
		else if (isCtrl==true){if (e.which>=65 && e.which<=90) e.preventDefault();}
		else if (e.which!=27){str1="";if (isAlt && (e.which>=48 && e.which<=57)){str1=characterKeys[e.which-48];setChar(str1,0);return false;}}
		else{e.preventDefault();}
	}
	function min(a,b){mi=a<b?a:b;return mi;}
	var re00 = /<i>/ig,re01 = /<\/i>/ig,re1 = /<span class=['"][a-z]+['"]>/ig,re1a = /<span class=['"][a-z]+['"] style=['"][A-z#\-:; (),0-9.]+['"]>/ig;
	var re1b = /<span style=['"][A-z#\-:; (),0-9.]+['"]>/ig,re1c = /<br style=['"][A-z#\-:; (),0-9.]+['"]>/ig,re11 = /<span>/ig,re2 = /<\/span>/ig;
	var re3 = /(^|[^a-z])([a-z])((?![a-z]))/ig;
	var re4 = / /g;
	var re5=/&nbsp;/ig,re6=/([a-z]|span>)([0-9])((?![0-9]))/ig,re7=/([a-z0-9>]+[^<])\/((?=[a-z0-9<]+))/ig,re8=/<pre class=['"][a-z]+['"]>([^()]*)<\/pre>/ig;
	var re9=/\[tab\]/g,re100=/<p>/ig,re101=/<\/p>/ig,re99 = /<font[ ]+size=['"][0-9]+['"]>([^()]*[ ]*)<\/font>/ig,re99a=/<font[ ]*>([^()]*)<\/font>/ig;
	var re9a=/face=['"][A-z#\-:; (),0-9.]+['"]/ig,re9b=/color=['"][A-z#\-:; (),0-9.]+['"]/ig,re98=/<sup>([^<()]*)<\/sup>/ig,re98a=/<sup style=['"][A-z#\-:; (),0-9.]+['"]>([^<()]*)<\/sup>/ig;
	var re00 = /<[ib]{1}>|<\/[ib]{1}>|<img[^<]*?>|<\/img>|<span[^<]*?>|<\/span>|<p[^<]*?>|<\/p>|<font[^<]*?>|<\/font>/ig;
	var re1a = /<sup[^>]*>/ig,re1b = /<sub[^>]*>/ig,re1c = /<\/sup>([ ]?)|<\/sub>([ ]?)/ig;
	var reBr = /<br[^<]*?>[ ]?/ig,remJunk = /<head>[^]*?<\/head>/ig,remAll = /<[^<]+?>/g;
	var remAllE = /<(?=table|tbody|tr|td|br|body|\/body|\/table|\/tbody|\/tr|\/td)[^<]+?>/ig;
	var remAllH = /<(?=html|body|\/html|\/body)[^<]+?>|<xml>(.*)<\/xml>|<style>(.*)<\/style>/ig;
	var remAllS = /<[^<]*?style="[^<]*?"[^<]*?>/ig;
	
	function stripHTML(eqs){
		var content=eqs.replace(/<div><br><\/div>/g,"\n");
		content=content.replace(/<div>/g,"\n");content=content.replace(/<br>/g,"\n");content=content.replace(/<\/div>/g,"");
		content = content.replace(re00, "");content = content.replace(re01, "");content = content.replace(re01, "");
		content = content.replace(re1a, "^(").replace(re1b, "_(").replace(re1c, ")");
		content = content.replace(remAllS, "");content = content.replace(remAllH, "");content = content.replace(remJunk, "");
		content = content.replace(remAll, "");content = content.replace(reBr, "\n");
		content=content.replace(re1, "");content=content.replace(re1a, "");content=content.replace(re1b, "");content=content.replace(re1c, "\n");
		content=content.replace(re11, "");content=content.replace(re2, "");content=content.replace(re100, "");content=content.replace(re101, "");
		content = content.replace(re98, "^$1");content = content.replace(re98a, "^$1");content = content.replace(re5, "");
		content = content.replace(/([0-9a-zA-z]*?)[ ]?{([0-9a-zA-z]*?)}\/{([0-9a-zA-z]*?)}/g,"$1 $2/$3");
		return content;
	}
	function getData(){
		$('iframe body').removeAttr("contenteditable");
		$('#backup').html(editable.innerHTML);
		$('#backup td.num').each(function(index) {$(this).html('{'+$(this).html()+'}');});
		$('#backup td.den').each(function(index) {$(this).html('/{'+$(this).html()+'}');});
		$('#stripped').val(stripHTML($('#backup').html()));return $('#stripped').val();
	}
	function disableFracbox(){
		$('#layer').show();
		if(editable) $(editable).removeAttr('contenteditable');
	}
	function enableFracbox(){
		$('#layer').hide();
		if(editable) $(editable).attr('contenteditable','true');
	}