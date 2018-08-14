
var expressionCompare=function(){
	var _expressionCompare={};
	
	var postFixN=[],vars1=[],vars2=[],vars=[],vals=[];
	var finArr = ["sqrt", "cosec", "sin", "cos", "tan","cot", "sec", "csc", "log", "fact","pow"];
	var repArr = ["ž", "Ï", "§", "¤","Ð","Þ","Æ","Ï","¢","†","‡"];
	var toDegrees = 180/Math.PI;
	var message="",timeID=null;
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
		replaceString = replaceString.replace(regex, 'Math.'+find[i]+'|(');
	  }
	  return replaceString.replace(new RegExp('\\|','g'),'');
	};
	Math.sec=function(x){return (1/Math.cos(x));}
	Math.cosec=function(x){return (1/Math.sin(x));}
	Math.csc=function(x){return (1/Math.sin(x));}
	Math.cot=function(x){return (1/Math.tan(x));}
	Math.sgn=function(x){return (x>0)?1:((x<0)?-1:0);}
	Math.fact=function(x){var y=Math.round(x);if (y==1) return 1; else return y*Math.fact(y-1);}
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
		var strE = eq.replace(/([0-9])([a-z])/g,"$1*$2").replace(/\{|\[/g,'(').replace(/\}|\]/g,')').replace(/\(\)/g,'(0)');
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
	_expressionCompare.compEqs = function (str1,str2){
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
			try {exV1=eval(ex1);}catch (er){return false;}
			var ex2=exp2.getMathFns(finArr);
			try {exV2=eval(ex2);}catch (er){return false;}
			if (Math.round(exV1*1000)==Math.round(exV2*1000)){
				
			}
			else{
				resul=false;
			}
		}
		return resul;
	}
	_expressionCompare.compEqsVal = function (str1,solveFor,valFor){
		var strippedFns1=checkEq(str1,1);
		if (typeof(strippedFns1)==='object') {return message;}
		var fns1=strippedFns1.replaceArray(repArr,finArr,1);
		var resul=true;
			var regex = new RegExp(solveFor,'g');
			var exp1=setPow(strippedFns1).replace(regex,valFor).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			var exV1;
			try {exV1=eval(exp1);}catch (er){return false;}
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
			var expL=setPow(strippedFns1).replace(regex,minval).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			var expR=setPow(strippedFns2).replace(regex,minval).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			var exminV1,exminV2;
			try {exminV1=eval(expL);}catch (er){return false;}
			try {exminV2=eval(expR);}catch (er){return false;}
			
			expL=setPow(strippedFns1).replace(regex,maxval).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			expR=setPow(strippedFns2).replace(regex,maxval).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			var exmaxV1,exmaxV2;
			try {exmaxV1=eval(expL);}catch (er){return false;}
			try {exmaxV2=eval(expR);}catch (er){return false;}
			
			var sgnMin=Math.sgn(exminV1-exminV2);
			var sgnMax=Math.sgn(exmaxV1-exmaxV2);
			var exmidV1,exmidV2;
		do{
			midval=(minval+maxval)/2;
			expL=setPow(strippedFns1).replace(regex,midval).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			expR=setPow(strippedFns2).replace(regex,midval).replace(/\+\+/g,"+").replace(/\+-/g,"-").replace(/-\+/g,"-").replace(/--/g,"+").replaceArray(repArr,finArr,1).getMathFns(finArr);
			
			try {exmidV1=eval(expL);}catch (er){return false;}
			try {exmidV2=eval(expR);}catch (er){return false;}
			
			var sgnMid=Math.sgn(exmidV1-exmidV2);
			
			if (sgnMid==sgnMin) minval=midval;
			else if (sgnMid==sgnMax) maxval=midval;
			else if (sgnMid==0) break;
		}while (maxval>minval);
		return midval;
	}
	function setPow(eqn){
		eqn=eqn.replace(/(\d+(?:\.\d+)?)\^(\d+(?:\.\d+)?)/g, function(a, b, c) {
		    return Math.pow(b, c);
		});
		while(eqn.indexOf('^')>0){
			//(sin(x))^2+(cos(x))^2 + x^2y^6
			var cpos=eqn.indexOf('^');
			var base="",poww="";
			if (eqn.charAt(cpos-1)==')'){
				var bopen=-1,bpos=cpos-1;
				while (bopen<0 && bpos>=0){
					bpos--;
					if (eqn.charAt(bpos)==')') bopen--;
					if (eqn.charAt(bpos)=='(') bopen++;
				}
				base=eqn.substr(bpos,cpos-bpos);
			}
			else base=eqn.charAt(cpos-1);
			if (eqn.charAt(cpos+1)=='('){
				var bopen=1,bpos=cpos+1;
				while (bopen>0 && bpos<=eqn.length){
					bpos++;
					if (eqn.charAt(bpos)==')') bopen--;
					if (eqn.charAt(bpos)=='(') bopen++;
				}
				poww=eqn.substr(cpos+1,bpos-cpos);
			}
			else {
				var tr=cpos+1;poww=eqn.charAt(tr);tr++;
				while (tr<eqn.length && !isNaN(eqn.charAt(tr)*1))
				{
					poww+=eqn.charAt(tr);tr++;
				}
			}
			eqn=eqn.substr(0,cpos-base.length)+'‡('+base+','+poww+')'+eqn.substr(cpos+poww.length+1);
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
	return _expressionCompare;	
}();
var expComp=expressionCompare;