
var expressionCompare=function(){
	var _expressionCompare={};
	var toRad=Math.PI/180;
	var toDeg = 180/Math.PI;
	var toAng=toRad;	
	var postFixN=[],vars1=[],vars2=[],vars=[],vals=[];
	var mfunctions=/^(ang|floor|ceil|sqrt|pow|(sin|cos|tan|cot|sec|csc)h?s?)\(/;
	var finArr = ["sqrt", "cosec", "sin", "cos", "tan","cot", "sec", "csc", "cosecs", "sins", "coss", "tans","cots", "secs", "cscs","log", "fact","pow","ang"];
	var repArr = ["ž", "Ï", "§", "¤","Ð","Þ","Æ","Ï","¢","†","‡",'˯','˰','˱','˲','˹','˺','˻','˼'];
	var message="",timeID=null;
	var rounding=1000;
	String.prototype.replaceArray = function(find, replace,fn) {
	  var replaceString = this;
	  var regex; var adx='\\(',ads='(';
	  if (!fn) {adx='';ads='';}
	  for (var i = 0; i < find.length; i++) {
	    regex = new RegExp(find[i]+adx, "g");
	    replaceString = replaceString.replace(regex, replace[i]+ads);
	  }
	  return replaceString;
	};
	String.prototype.getMathFns = function(find) {
	  var replaceString = this;
	  var regex; 
	  for (var i = 0; i < find.length; i++) {
		regex = new RegExp(find[i]+'\\(', "g");
		if(i>1 && i<5) replaceString = replaceString.replace(regex, 'Math.'+find[i]+'D|(');
		else replaceString = replaceString.replace(regex, 'Math.'+find[i]+'|(');
	  }
	  replaceString = replaceString.replace(/([^\|\*\+-\/\()])\(/g, '$1*(');
	  return replaceString.replace(new RegExp('\\|','g'),'');
	};

	Math.sins=function(x){return Math.sinD(x)*Math.sinD(x);}
	Math.coss=function(x){return Math.cosD(x)*Math.cosD(x);}
	Math.tans=function(x){return Math.tanD(x)*Math.tanD(x);}

	Math.cots=function(x){return Math.cot(x)*Math.cot(x);}
	Math.cscs=function(x){return Math.csc(x)*Math.csc(x);}
	Math.secs=function(x){return Math.sec(x)*Math.sec(x);}
	Math.cosecs=function(x){return Math.cosec(x)*Math.cosec(x);}
	

	Math.sinD=function(x){return (Math.sin((x)*toAng));}
	Math.cosD=function(x){return (Math.cos((x)*toAng));}
	Math.tanD=function(x){return (Math.tan((x)*toAng));}
	
	Math.sec=function(x){return (1/Math.cosD(x));}
	Math.cosec=function(x){return (1/Math.sinD(x));}
	Math.csc=function(x){return (1/Math.sinD(x));}
	Math.cot=function(x){return (1/Math.tanD(x));}
	
	Math.sgn=function(x){return (x>0)?1:((x<0)?-1:0);}
	Math.fact=function(x){var y=Math.round(x);if (y==1) return 1; else return y*Math.fact(y-1);}
	Math.ang=function(x){var y=getValueOfVar(x);return (Array.isArray(y))?y[0]:y;}
	var initVars={};

	_expressionCompare.setAng=function(ang) {
		switch(ang){
			case 'deg': toAng=toRad;
			case 'rad': toAng=1;
		}
	};
	_expressionCompare.resetVars=function(){
		initVars={};
	};
	_expressionCompare.setRounding=function(n){
		rounding=Math.pow(10,n);
	};
	_expressionCompare.setVars=function(obj){
		for (var key in obj){
			if (obj.hasOwnProperty(key)) initVars[key]=obj[key];
		}
	};
	function getValueOfVar(v) {
		if (initVars.hasOwnProperty(v)) return initVars[v];
		else return getRandomValue();
	};

	function arrayUnique(array) {
	    var a = array.concat();
	    for(var i=0; i<a.length; ++i) {
	        for(var j=i+1; j<a.length; ++j) {
	            if(a[i] === a[j])
	                a.splice(j--, 1);
	        }
	    }

	    return a;
	};

	function checkEq(eq,n){
		var strE = getTerms(eq)['display'];
		strE = strE.replace(',','').replace(/([0-9])([a-z])/g,"$1*$2").replace(/\{|\[/g,'(').replace(/\}|\]/g,')')
			.replace(/\(\)/g,'(0)').replace(/([0-9])\(/g,'$1*(').replace(/\*+/g,'*').replace(/\~/g,'/');
		strE = strE.replaceArray(finArr, repArr,1);
		strE = strE.replace(/\+\+/,"+").replace(/\+-/,"-").replace(/-\+/,"-");
		var parenChk=checkForParenthesis(strE,n);
		if (typeof(parenChk)=="string") return [1,parenChk];
		strE = strE.replace(/\B([.]?[a-z][\^]?[0-9]*)/g,"*$1");
		var patt=/[a-z]/g;
		var varArray=[];
		while ((match = patt.exec(strE)) != null) {
		    if (varArray.indexOf(match[0])<0)	varArray.push(match[0]);
		}
		switch (n){
			case 1: vars1=varArray;break;
			case 2: vars2=varArray;break;
		}

		return strE;
	}
	
	_expressionCompare.compEqs = function (str1,str2,quesPage,autoRounding){if (str1===str2) return true;
		if (!autoRounding) autoRounding = true;
		var strippedFns1=checkEq(str1,1);
		if (typeof(strippedFns1)==='object') {return message;}
		var fns1=strippedFns1.replaceArray(repArr,finArr,1);
		var strippedFns2=checkEq(str2,2);
		if (typeof(strippedFns2)==='object') {return message;}
		var fns2=strippedFns2.replaceArray(repArr,finArr,1);
		vars=arrayUnique(vars1.concat(vars2));
		vals=[];
		var resul=true;
		for (var i=0;i<5;i++){
			genRandomValues(i);
			var exp1=setPow(strippedFns1).replaceArray(vars,vals[vals.length-1],0).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(");
			var exp2=setPow(strippedFns2).replaceArray(vars,vals[vals.length-1],0).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(");
			var ex1=exp1.getMathFns(finArr);
			var exV1,exV2;
			try {exV1=eval(this.replaceOctal(ex1));}catch (er){return false;}
			var ex2=exp2.getMathFns(finArr);
			try {exV2=eval(this.replaceOctal(ex2));}catch (er){return false;}
			if (Math.round(exV1*1000)==Math.round(exV2*1000) && autoRounding){
				
			}
			else{
				var cvals=compareValues(exV1,exV2);
				if (!cvals) {
					ca_numDec = (exV1+'').indexOf('.')<0?0:(exV1+'').length-(exV1+'').indexOf('.')-1;
					ua_numDec = (exV2+'').indexOf('.')<0?0:(exV2+'').length-(exV2+'').indexOf('.')-1;
					if (!quesPage){
						if (ca_numDec > ua_numDec){
							if (Math.round(exV1*Math.pow(10,ua_numDec))/Math.pow(10,ua_numDec) != exV2)
								resul = false;
						}
						else {
							if (Math.round(exV2*Math.pow(10,ca_numDec))/Math.pow(10,ca_numDec) != exV1)
								resul = false;
						}
						if ((exV1+'').indexOf('.')<0 || (exV2+'').indexOf('.')<0) resul=false;
					}
					else{
						if (ua_numDec < ca_numDec) resul = false;
						else if (Math.round(exV2*Math.pow(10,ca_numDec))/Math.pow(10,ca_numDec) != exV1)
							resul = false;
					}
				}
				if (!resul) return resul;
			}
			if (vars.length==0) break;
		}
		return resul;
	}
	
	_expressionCompare.compEqsVal = function (str1,solveFor,valFor){
		var strippedFns1=checkEq(str1,1);
		if (typeof(strippedFns1)==='object') {return message;}
		var fns1=strippedFns1.replaceArray(repArr,finArr,1);
		var resul=true;
		var regex = new RegExp(solveFor,'g');
		var exp1=setPow(strippedFns1).replace(regex,valFor).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
		var exV1;
		try {exV1=eval(this.replaceOctal(exp1));}catch (er){return false;}
		if (Math.round(exV1*1000)==0){
			
		}
		else{
			resul=false;
		}
		return resul;
	}
	_expressionCompare.getValueOf = function (eqlhs,eqrhs,variable){
		var strippedFns1=checkEq(eqlhs,1);
		if (typeof(strippedFns1)==='object') {return message;}
		var fns1=strippedFns1.replaceArray(repArr,finArr,1);
		var strippedFns2=checkEq(eqrhs,1);
		if (typeof(strippedFns2)==='object') {return message;}
		var fns2=strippedFns2.replaceArray(repArr,finArr,1);
		var minval=-10000,maxval=10000;midval=0;
			var regex = new RegExp(variable,'g');
			var expL=setPow(strippedFns1).replace(regex,minval).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
			var expR=setPow(strippedFns2).replace(regex,minval).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
			var exminV1,exminV2;
			try {exminV1=eval(replaceOctal(expL));}catch (er){/*console.log("Error in LHS");*/return false;}
			try {exminV2=eval(replaceOctal(expR));}catch (er){/*console.log("Error in RHS");*/return false;}
			
			expL=setPow(strippedFns1).replace(regex,maxval).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
			expR=setPow(strippedFns2).replace(regex,maxval).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
			var exmaxV1,exmaxV2;
			try {exmaxV1=eval(replaceOctal(expL));}catch (er){/*console.log("Error in LHS");*/return false;}
			try {exmaxV2=eval(replaceOctal(expR));}catch (er){/*console.log("Error in RHS");*/return false;}
			
			var sgnMin=Math.sgn(exminV1-exminV2);
			var sgnMax=Math.sgn(exmaxV1-exmaxV2);
			var exmidV1,exmidV2;yo=0;
		do{
			midval=(minval+maxval)/2;
			expL=setPow(strippedFns1).replace(regex,midval).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
			expR=setPow(strippedFns2).replace(regex,midval).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(").getMathFns(finArr);
			try {exmidV1=eval(replaceOctal(expL));}catch (er){/*console.log("Error in LHS");*/return false;}
			try {exmidV2=eval(replaceOctal(expR));}catch (er){/*console.log("Error in RHS");*/return false;}

			var sgnMid=Math.sgn(Math.round(exmidV1*Math.pow(10,10))/Math.pow(10,10)-Math.round(exmidV2*Math.pow(10,10))/Math.pow(10,10));
			yo++;if (yo==80) break;
			if (sgnMid==sgnMin) minval=midval;
			else if (sgnMid==sgnMax) maxval=midval;
			else if (sgnMid==0) break;
		}while (maxval>minval || yo<80);
		return Math.round(midval*Math.pow(10,10))/Math.pow(10,10);
	}
	_expressionCompare.getValueOfExpression = function (exp){
		var strippedFns=checkEq(exp,1);
		if (typeof(strippedFns)==='object') {return false;}
		var fns=strippedFns.replaceArray(repArr,finArr,1);
		var expL=setPow(strippedFns).replace(/(\+\+)+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/(--)+/g,"+").replaceArray(repArr,finArr,1).replace(/\)\(/g,")*(");
		var exL=expL.getMathFns(finArr);
		var exV;
		try {exV=eval(replaceOctal(exL));}catch (er){console.log(exL+"Error in Expression");return false;}
		return exV;
	}
	function compareValues(a,b){
		if (a==b) return true;
		if (Math.sgn(a)!=Math.sgn(b)){
			var ab=Math.abs(a-b).toPrecision();//console.log(ab,ab<Math.pow(10,-12));
			if (ab<Math.pow(10,-12)) return true;
			//if (Math.abs(a+b).toPrecision().length>12 || Math.abs(a+b).toPrecision().indexOf('e')>0) return true;
			else return false;
		} 
		var f=12;
		a=(a+'').replace(/^-/,'');b=(b+'').replace(/^-/,'');var p=a.indexOf('.'),q=b.indexOf('.');
		var l=Math.max(a.length,b.length);
		if (p<0) a+='.0';
		if (q<0) b+='.0';
		
		if (l<f) return false;
		else {
			p=a.indexOf('.');q=b.indexOf('.');
			da=Math.pow(10,(f-p));db=Math.pow(10,(f-q));
			var m=Math.round(a*da)/da;var n=Math.round(b*db)/db;//console.log(m,n);
			if (m==n) return true;
			else return false;

		}
		//472564847663.4049
		//472564847663.40497

		//0.30000000000000004
		//0.3

		//15.876371389354004 
		//15.876371389353999

		//476419519339732.5 
		//476419519339732.44

		//155325562238121.16 
		//155325562238121.12
	}
	function replaceOctal(exp){
		return (exp.replace(/([^0-9.]+)0+([0-9]+)|^0+([0-9]+)/g,"$1$2$3"));
	}
	_expressionCompare.replaceOctal = function(exp){
        return (exp.replace(/([^0-9.]+)0+([0-9]+)|^0+([0-9]+)/g,"$1$2$3"));
    };
    function getClosingBracPos(tr,n){
    	var bopen=1, i=n;
    	while (bopen!=0 && i<tr.length){
    		if (i+1==tr.length) break;
    		i++;
    		if (tr.charAt(i)=='(') bopen++;
    		if (tr.charAt(i)==')') bopen--;
    	}
    	return i;
    }
    function getStartingBracPos(tr,n){
    	var bopen=-1, i=n;
    	while (bopen!=0 && i>0){
    		if (i-1==0) break;
    		i--;
    		if (tr.charAt(i)=='(') bopen++;
    		if (tr.charAt(i)==')') bopen--;
    	}
    	return i;
    }
	function setPow(eqn){
		eqn=eqn.replace(/(\d+(?:\.\d+)?)\^(\d+(?:\.\d+)?)/g, function(a, b, c) {return Math.pow(b, c);});
		while(eqn.indexOf('^')>0){
			//(sin(x))^2+(cos(x))^2 + x^2y^6
			var cpos=eqn.indexOf('^');
			var base="",poww="",trigFn=0;
			if (eqn.charAt(cpos-1)==')'){ 
				bpos=getStartingBracPos(eqn,cpos-1);
				base=eqn.substr(bpos,cpos-bpos);
			}
			else if (/sin|cos|tan|cot|csc|sec/.test(eqn.substr(cpos-3,3))){
				base=eqn.substr(cpos-3,3);trigFn=1;
			}
			else if (/[a-zA-Z]/.test(eqn.charAt(cpos-1))) base=eqn.charAt(cpos-1);
			else if (/(\d+(?:\.\d+)?)$/.test(eqn.substr(0,cpos))) base=eqn.substr(0,cpos).match(/(\d+(?:\.\d+)?)$/)[0];
			if (eqn.charAt(cpos+1)=='('){
				bpos=getClosingBracPos(eqn,cpos+1);
				poww=eqn.substr(cpos+1,bpos-cpos);
			}
			else {
				var tr=cpos+1;
				poww=/^[+-]?(\d+(?:\.\d+)?)/.test(eqn.substr(tr))?eqn.substr(tr).match(/^[+-]?(\d+(?:\.\d+)?)/)[0]:eqn.charAt(tr);
			}
			if (trigFn==1 && eqn.charAt(cpos+poww.length+1)=="("){
				bpos=getClosingBracPos(eqn,cpos+poww.length+1);
				trigArgs=eqn.substr(cpos+poww.length+1,bpos-cpos-poww.length);
				base+=trigArgs;
			}
			eqn=eqn.substr(0,cpos-base.length)+repArr[finArr.indexOf('pow')]+'('+base+','+poww.replace(/\s/g, '')+')'+eqn.substr(cpos+poww.length+1);
		}
		return eqn;
	}
	function inArray(needle, haystack) {
	    var length = haystack.length;
	    for(var i = 0; i < length; i++) {
	        if(haystack[i] == needle) return i;
	    }
	    return -1;
	}
	function genRandomValues(n){
		var v=[];
		for (var i=0;i<vars.length;i++){
			switch (n){
				case 0: v.push(-Math.round((Math.random()*100)*100)/100);break;
				case 1: v.push(-Math.round((Math.random()*1)*100)/100);break;
				case 2: v.push(Math.round((Math.random()*1)*100)/100);break;
				case 3: v.push(Math.round((Math.random()*9+1)*100)/100);break;
				case 4: v.push(Math.round((Math.random()*100+10)*100)/100);break;
			}
		}
		vals.push(v);
	}
	function getRandomValue(){
		var v=[];
		v.push(-Math.round((Math.random()*100)*100)/100);
		v.push(-Math.round((Math.random()*1)*100)/100);
		v.push(Math.round((Math.random()*1)*100)/100);
		v.push(Math.round((Math.random()*9+1)*100)/100);
		v.push(Math.round((Math.random()*100+10)*100)/100);
		return shuffle(v);
	}
	function shuffle(o){ //v1.0
	    for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
	    return o;
	};

	function checkForParenthesis(str,n){
		if (!str.match(/\(/g) && !str.match(/\)/g)) return 1;
		if ((str.match(/\(/g) && !str.match(/\)/g)) || (!str.match(/\(/g) && str.match(/\)/g)))
			return "Parentheses mismatch!"; 
		if (str.match(/\(/g).length!=str.match(/\)/g).length)
			return "Parentheses not closed!"; 
		
		var i=0;br=0;
		while (i<str.length){
			if (str.charAt(i)=="(") br++;
			if (str.charAt(i)==")") br--;
			if (br<0) return 'Parentheses error! Make sure you have correctly placed the parentheses.';
			i++;
		}
		if (br!=0) return 'Parenthesis error! Make sure you have correctly placed the parenthesis.';
		return 1;
	}

	_expressionCompare.gt=function(expr){
		return getTerms(expr)['display'];
	}
	function getTerms(expr){
		if (expr.indexOf(':')>-0) return {'display':expr};
		var i=0;var tcount=0;var exprA=new Array();

		expr=expr.trim();
		expr=expr.replace(/,/g,"").replace(',','').replace(/([0-9])([a-z])/g,"$1*$2").replace(/\{|\[/g,'(').replace(/\}|\]/g,')')
			.replace(/\(\)/g,'(0)').replace(/([0-9])\(/g,'$1*(').replace(/\*+/g,'*').replace(/\~/g,'/');
		var l=expr.length,j=0;var trm="";var bpon=0;
		while (i<l){
			if (((expr.charAt(i)=='+' || expr.charAt(i)=='-')) && bpon==0) {
				if (i>0){
					if (expr.charAt(i-1)=='*' || expr.charAt(i-1)=='/' || expr.charAt(i-1)=='~' || expr.charAt(i-1)=='^') trm+=expr.charAt(i);
					else {exprA[tcount]=trm.trim();tcount++;trm="";trm=expr.charAt(i);}
				}
				else {exprA[tcount]=trm.trim();tcount++;trm="";trm=expr.charAt(i);}
				i++;
			}
			else{
				trm+=expr.charAt(i);(expr.charAt(i)=="(") && bpon++;(expr.charAt(i)==")") && bpon--;
				i++;
			}
		}
		exprA[tcount]=trm.trim();

		var termsO={type:"expr",terms:[],display:(expr.charAt(0).match(/[\+-]/)?"":"+")+expr};i=0;
		var mathOperators=/\+|\-|\*|\/|\~/g,eDisp='';
		while (i<exprA.length){
			if (exprA[i]=="") {	exprA.splice(i,1);}
			else {
				var termStr=exprA[i],factors=[],dividends=[],sign=1,inFunction='',dividing=0, power=0,sg="",k=(/^\+|^\-/.test(termStr))?1:0;
				var ty=[];sign=(termStr.charAt(0)=='-')?-1:1;
				while (k<termStr.length){
					if (termStr.charAt(k)=="("){
						var closBPos=getClosingBracPos(termStr,k);
						var pExpr=termStr.substring(k+1,closBPos);
						var partExpr=getTerms(pExpr);var pDisp='('+partExpr.display+')';partExpr['outerDisplay']=sg+pDisp;
						var eObj=partExpr;eObj['sign']=(sg=='-')?-1:1;
						ty.push(eObj);
						sg='';
						inFunction='';dividing=0;
						k=closBPos+1;
					}
					else if (termStr.charAt(k)=="^"){var ppos=1,pn='';var power=1;ty.push('^');k++;}
					else if (termStr.substr(k).match(/^sin|^cos|^tan|^cot|^sec|^csc|^ang|^sqrt/)){
						var gotFunction=termStr.substr(k).match(/^sin|^cos|^tan|^cot|^sec|^csc|^ang|^sqrt/)[0];
						gotFunction=gotFunction.substr(0,gotFunction.length);
						inFunction=gotFunction;
						var eObj={type:"func",name:inFunction,args:'',power:1,display:sg+inFunction,};eObj['sign']=(sg=='-')?-1:1;sg='';
						ty.push(eObj);
						k+=gotFunction.length;
					}
					else if (termStr.charAt(k)=="/"){var dividing=1;ty.push('/');k++;}
					else if (termStr.charAt(k)=="~"){ty.push('~');k++;}
					else if (termStr.charAt(k)=="*"){dividing=0;ty.push('*');k++;}
					else if (termStr.charAt(k).match(/[\+-]/)){sg=termStr.charAt(k);k++;}
					else if (/^((?:\d*)?\d+) ((?:\d*)?\d+)\/((?:\d*)?\d+)/.test(termStr.substr(k))){
						var pn = termStr.substr(k).match(/^((?:\d*)?\d+) ((?:\d*)?\d+)\/((?:\d*)?\d+)/)[0];
						var pn1= pn.replace(/^((?:\d*)?\d+) ((?:\d*)?\d+)\/((?:\d*)?\d+)/,fracNumReplacer);
						var eObj={type:"constant",value:sg+pn1,power:1,display:sg+pn1};eObj['sign']=(sg=='-')?-1:1;sg='';
						ty.push(eObj);k+=pn.length;
					}
					else if (/^((?:\d*\.)?\d+)/.test(termStr.substr(k))){
						var pn = termStr.substr(k).match(/^((?:\d*\.)?\d+)/)[0];
						var eObj={type:"constant",value:sg+pn,power:1,display:sg+pn};eObj['sign']=(sg=='-')?-1:1;sg='';
						ty.push(eObj);k+=pn.length;
					}
					else if(termStr.substr(k).match(/^[A-Z]/)){//if big variable
						var pn=termStr.substr(k).match(/^[A-Z]+/)[0];
						var eObj={type:"variable",name:pn,power:1,display:sg+pn};eObj['sign']=(sg=='-')?-1:1;sg='';
						ty.push(eObj);k+=pn.length;
					}
					else if(termStr.charAt(k).match(/[a-z]/)){//if small variable
						var pn=termStr.charAt(k);
						var eObj={type:"variable",name:pn,power:1,display:sg+pn};eObj['sign']=(sg=='-')?-1:1;sg='';
						ty.push(eObj);k+=pn.length;
					}
					else {k++;}
				}
				while (ty.indexOf('^')>0){
					var k=ty.indexOf('^');ty[k-1]['power']=ty[k+1];
					if(ty[k-1]['sign']==1)ty[k-1]['display']='('+ty[k-1]['display']+')';
					ty[k-1]['display']+='^'+(ty[k+1]['outerDisplay']||ty[k+1]['display']);
					ty[k-1]['outerDisplay']='('+ty[k-1]['display']+')';
					ty.splice(k,2);
				}
				k=0;while (k<ty.length){
					if (ty[k]['type']=='func'){
						ty[k]['args']=ty[k+1];ty[k]['display']+=ty[k+1]['outerDisplay'];
						ty.splice(k+1,1);
						if (ty[k]['power']!=1) 
							ty[k]['display']='pow('+(ty[k]['name']+''+(ty[k]['args']['outerDisplay'])+'')+','+(ty[k]['power']['outerDisplay']||ty[k]['power']['display'])+')';
							ty[k]['outerDisplay']='('+ty[k]['display']+')';

					}
					k++;
				};
				while (ty.indexOf('/')>0){var k=ty.indexOf('/'),fracNum=ty[k-1];ty[k-1]=null;
					ty[k-1]={type:'frac',num:fracNum,den:ty[k+1],display:''+(fracNum.outerDisplay||fracNum.display)+'/'+(ty[k+1].outerDisplay||ty[k+1].display)};
					ty[k-1]['outerDisplay']='('+ty[k-1]['display']+')';
					ty.splice(k,2);}
				while (ty.indexOf('~')>0){
					var k=ty.indexOf('~');
					ty[k-1]['display']+='/'+(ty[k+1]['outerDisplay']||ty[k+1]['display']);ty.splice(k,2);
					ty[k-1]['outerDisplay']='('+ty[k-1]['display']+')';
				}
				while (ty.indexOf('*')>0){var k=ty.indexOf('*');ty.splice(k,1);}
				tDisp='';for (var j=0;j<ty.length;j++){tDisp+='('+(ty[j].outerDisplay||ty[j].display)+')';}
				var nTerm={type:"term",factors:ty,display:((sign>0)?"+":"-")+tDisp,sign:sign};
				termsO.terms.push(nTerm);
				i++;
			}
		}
		for (var i=0;i<termsO.terms.length;i++){eDisp+=(termsO.terms[i].outerDisplay||termsO.terms[i].display);}
		termsO.display="("+(expr.charAt(0).match(/[\+-]/)?"":"+")+eDisp+")";
		return termsO;
	}
	var fracNumReplacer=function(){
	  var i=arguments[1]*1,j=arguments[2]*1,k=arguments[3]*1;
	  return (i*k+j)/k;
	};
	return _expressionCompare;	
}();
var expComp;
if (expComp===undefined){
	expComp=expressionCompare;
}