	
	//----------------------------------------------------------
	var re00 = /<i>/ig;
	var re01 = /<\/i>/ig;
	var re1 = /<span class=['"][a-z]+['"]>/ig;
	var re1a = /<span class=['"][a-z]+['"] style=['"][A-z#\-:; (),0-9.]+['"]>/ig;
	var re1b = /<span style=['"][A-z#\-:; (),0-9.]+['"]>/ig;
	var re1c = /<br style=['"][A-z#\-:; (),0-9.]+['"]>/ig;
	var re11 = /<span>/ig;
	var re2 = /<\/span>/ig;
	var re3 = /(^|[^a-z])([a-z])((?![a-z]))/ig;
	//var re21 = /(^|<\span>)(.+)((?!<\/span))/ig;
	var re4 = / /g;
	var re5 = /&nbsp;/ig;
	var re6 = /([a-z]|span>)([0-9])((?![0-9]))/ig;
	var re7 = /([a-z0-9>]+[^<])\/((?=[a-z0-9<]+))/ig;
	
	var re8 = /<pre class=['"][a-z]+['"]>([^()]*)<\/pre>/ig;
	
	var re9 = /\[tab\]/g;
	var re100 = /<p>/ig;
	var re101 = /<\/p>/ig;
	var re99 = /<font[ ]+size=['"][0-9]+['"]>([^()]*[ ]*)<\/font>/ig;
	var re99a = /<font[ ]*>([^()]*)<\/font>/ig;
	var re9a = /face=['"][A-z#\-:; (),0-9.]+['"]/ig;
	var re9b = /color=['"][A-z#\-:; (),0-9.]+['"]/ig;
	var re98 = /<sup>([^<()]*)<\/sup>/ig;
	var re98a = /<sup style=['"][A-z#\-:; (),0-9.]+['"]>([^<()]*)<\/sup>/ig;
	
	var re00 = /<[ib]{1}>|<\/[ib]{1}>|<img[^<]*?>|<\/img>|<span[^<]*?>|<\/span>|<p[^<]*?>|<\/p>|<font[^<]*?>|<\/font>/ig;
	var re1a = /<sup[^>]*>/ig;
	var re1b = /<sub[^>]*>/ig;
	var re1c = /<\/sup>([ ]?)|<\/sub>([ ]?)/ig;
	
	var reBr = /<br[^<]*?>[ ]?/ig;
	var remJunk = /<head>[^]*?<\/head>/ig;
	var remAll = /<[^<]+?>/g;
	var remAllE = /<(?!table|tbody|tr|td|br|\/body|\/table|\/tbody|\/tr|\/td)[^<]+?>/ig;
	var remAllH = /<(?=html|body|\/html|\/body)[^<]+?>|<xml>(.*)<\/xml>|<style>(.*)<\/style>/ig;
	var remAllS = /<[^<]*?style="[^<]*?"[^<]*?>/ig;
	
	function stripHTML(eqs){
		var content=eqs.replace(/<div><br><\/div>/g,"\n");
		content=content.replace(/<div>/g,"\n");
		content=content.replace(/<br>/g,"\n");
		content=content.replace(/<\/div>/g,"");
		content = content.replace(re00, "");
		content = content.replace(re01, "");
		content = content.replace(re01, "");
		content = content.replace(re1a, "^(").replace(re1b, "_(").replace(re1c, ")");
		content = content.replace(remAllS, "");
		content = content.replace(remAllH, "");
		content = content.replace(remJunk, "");
		content = content.replace(remAll, "");
		content = content.replace(reBr, "\n");
		content = content.replace(re1, "");
		content = content.replace(re1a, "");
		content = content.replace(re1b, "");
		content = content.replace(re1c, "\n");
		content = content.replace(re11, "");
		content = content.replace(re2, "");
		
		content = content.replace(re100, "");
		content = content.replace(re101, "");
		content = content.replace(re98, "^$1");
		content = content.replace(re98a, "^$1");
		content = content.replace(re5, "");
		
		//alert(content);
		return content;
	}
	var eqs=new Array();
	var eqBin=new Array();
	var eqTerm=new Array();
	function evaluateEquations(mainEq){
		var edite=document.getElementById('test');
		var editel=edite.contentWindow;
		
		var editable=editel.document.body;
		var newEs=stripHTML(editable.innerHTML);
		newEs=newEs.replace(/\n/g,"=");
		newEs=newEs.replace(/\s/g,"");
		newEs=newEs.split("=");
		console.log(newEs.length);
		var i=0;
		while (i<newEs.length){
			console.log('eq'+i+':  '+newEs[i]+'   length:'+newEs[i].length);
			if (newEs[i]=="" || newEs[i].length==0) newEs.splice(i,1);
			else i++;
		}
		console.log(newEs.length);
		var l=0;
		if (mainEq=="") {mainE=newEs[0];l=1;}
		else mainE=mainEq;
		var matchingTerms=new Array(),finalAnsAt=-1;
		
		/////////////////////////
		
		simplifyF.solveMainEq(mainE);
		for (i=l;i<newEs.length;i++){
			if (newEs[i].length==0) continue;
			if (newEs[i].charAt(0)=="=") newEs[i]=newEs[i].slice(1,newEs[i].length);
			console.log("--------------------------\n main:"+mainE+"     comp:"+newEs[i]);
			if (simplifyF.compareTerms(newEs[i])==true) {matchingTerms.push(i);console.log('matched');}
			if (simplifyF.checkifFinalAns(newEs[i])==true) finalAnsAt=i+1;
		}
		//document.getElementById('oldEq').innerHTML+="<br>---------<br>"+mainE+"<br><br>Matching Terms:<br>";
		//if (matchingTerms.length==0) document.getElementById('oldEq').innerHTML+="None";
		var k=0,w=0;
		var ansTerm=finalAns;console.log('main answer:'+finalAns);
		var answerHTML=fordisp(ansTerm)+"<br><br>"+fordisp(mainE)+"<br>";
		for (i=l;i<newEs.length;i++){
			if (newEs[i].length==0) continue;
			answerHTML+=fordisp(newEs[i]);
			if (i==matchingTerms[k]) {img="<img src='right.gif'><br>"; k++;}
			else {img="<img src='wrong.gif'><br>";w++;}
			answerHTML+=img;
			console.log(newEs[i]);
		}
		//alert(newEs.length+"  "+finalAnsAt+"   "+w);
		if (finalAnsAt<0 && w==0) answerHTML+="<br> You have not arrived at the final answer.";
		else if (finalAnsAt<0 && w>0) answerHTML+="<br> You entered a wrong step. Look at your mistake carefully and solve it correctly this time to arrive at the final answer.";
		else if (finalAnsAt==newEs.length && w>0) answerHTML+="<br> You final answer is correct, but an intermediate step is wrong. Find the mistake and solve carefully this time to arrive at the final answer.";
		else if (finalAnsAt<newEs.length && w==0) answerHTML+="<br> You had arrived at the final answer at step "+finalAnsAt+". Check the steps carefully to arrive at the final answer.";
		else if (finalAnsAt<newEs.length && w>0) answerHTML+="<br> You had arrived at the final answer at step "+finalAnsAt+", but have made a few errors in some steps. Please check so that you can solve this correctly next time.";
		/*if (finalAnsAt!=newEs.length && w!==0){
			answerHTML+="<br>&nbsp;<br>The correct steps to solve the expression are shown below:<br>"+fordisp(AnswerSteps.join("<br>"))+"<br>";
		}*/
		return answerHTML;
	}
	function fordisp(str){
		str=str.replace(/([a-z]+)/g,"<i>$1</i>");
		str=str.replace(/\^([a-z0-9]+)|\^\(([^()]*)\)/ig,"<sup>$1$2</sup>&nbsp;").replace(/\^([a-z0-9]+)|\^\(([^()]*)\)/ig,"<sup>$1$2</sup>&nbsp;");	
		str=str.replace(/\_([a-z0-9]+)|\_\(([^()]*)\)/ig,"<sub>$1$2</sub>&nbsp;").replace(/\_([a-z0-9]+)|\_\(([^()]*)\)/ig,"<sub>$1$2</sub>&nbsp;");
		return str;
	}
