//simplify


var Simplify = function(){
	var _simplify = {};
	var finalAns = "";
	var variables,expression,steps,numSteps;
	var arithF=new Array("+","-","*","/","^");
	var ET=new Array();
	var varsvMain = new Array(),varsnMain = new Array();
	var AnswerSteps= new Array();var steps=0;
	_simplify.returnFinalAns = function (){
		return finalAns;
	}
	_simplify.solveExp = function (a){
		AnswerSteps= new Array();
		var tA=simplify(a,1);console.log();finalAns=tA;steps=AnswerSteps.length;
	}
	_simplify.solveMainEq = function (a){
		AnswerSteps= new Array();
		var tA=simplify(a,1);finalAns=tA;steps=AnswerSteps.length;
		tA=getTerms(tA);
		var p=new Array();
		for (var k=0;k<tA.length;k++) {
			var a1=tA[k].replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			a1=removeNull(a1.split(/[{}]/));var newT="1";
			for (var a1c=0;a1c<a1.length;a1c++){
				if (a1[a1c]=="" || a1[a1c]==".") a1[a1c]=1;
				//console.log("sending..."+newT+"   "+a1[a1c]);
				newT=productOf(newT,a1[a1c]);
			}
			var a1=newT.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			var pi=(a1.indexOf("{")>=0)?a1.indexOf("{"):a1.length;
			var coff=a1.slice(0,pi);
			var p=new Array();p[0]=coff;p[1]=a1.slice(coff.length);
			if (p[0]=="") p[0]="1";
			else if (p[0]=="+" || p[0]=="-") p[0]=p[0]+"1";
			p[0]=p[0].replace(/([+\-]?)([0-9]+)([\^]+)([0-9]+)/g,"$1"+1+"*Math.pow($2,$4)");
			if (/^[a-z]+$/i.test (p[0].substring(0,1)) ) {
				cof=1;p[1]=p[0];
			}
			else{
				cof=eval(p[0]);
			}
			if (p.length==1) p.push("");
			p[1]=p[1].replace(/[.]/g,"");
			var tempP=removeNull(p[1].split(/[{}]/g));
			//console.log("p:"+p[1]);
			//tempP=p[1].split("");
			for (var vc0=0;vc0<tempP.length-1;vc0++){
				var tmp=tempP[vc0].split("^");if (tmp[0]=="") continue;
				for (var vc1=1;vc1<tempP.length;vc1++){
					var tmp1=tempP[vc1].split("^");if (tmp1[0]=="") continue;
					var cc0=tmp[0].charCodeAt(0);
					var cc1=tmp1[0].charCodeAt(0);
					if (cc0>cc1) {
						var temp=tempP[vc0];tempP[vc0]=tempP[vc1];tempP[vc1]=temp;
					}
				}
			}
			p[1]=tempP.join("");
			
			var q=inArray(p[1],varsnMain);
			if (q<0) {varsnMain.push(p[1]);varsvMain.push(cof);}
			else {varsvMain[q]+=cof;}
		}
	}
	function initialize(a){
		variables=new Array();
		expression=a;
		expression=expression.replace(")(",")*(");
		expression=expression.replace(/([a-z0-9]+)\(/ig,"$1*(");
		expression=expression.replace(/\)([a-z0-9]+)/ig,")*$1");
		expression=expression.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
		ET=null;ET=new Array();
		createExprTable(expression);
		//document.getElementById('solved').value = expression;
	}
	function isArithF(op){
		for (var i=0;i<arithF.length;i++){
			if (op==arithF[i]) return true;
		}
		return false;
	}
	function oLev(op){
		for (var i=0;i<arithF.length;i++){
			if (op==arithF[i]) return (i+1);
		}
	}
	function isLastArith(i,expr){
		for (var j=i+1;j<expr.length;j++) 
			if (isArithF(expr.charAt(j))) {
				if (expr.charAt(j)=="^" && expr.indexOf('}',j)>j && (expr.indexOf('{',j)==-1 || expr.indexOf('{',j)>expr.indexOf('}',j))){}
				else{/*console.log(expr.charAt(j));*/ return false;}
			}
		return true;
	}
	function getArg(expr,s,e){
		var n1="";
		for (var i=s+1;i<e;i++){
			if (expr.charAt(i)=="(") {}
			else if (expr.charAt(i)==")") {}
			else if (expr.charAt(i)=="{"){
				exP=expr.indexOf("}",i);
				n1+=expr.slice(i+1,exP);
				i=exP;
			}
			else n1+=expr.charAt(i);
		}
		return n1.trim();
	}
	function createExprTable(expr){
		var n1="",pLevel=0,id=0;
		for (var i=0;i<expr.length;i++){
			if (isArithF(expr.charAt(i))){
				id++;
				if (isLastArith(i,expr)) {
					var Erow=new Array(id,expr.charAt(i),n1.trim(),getArg(expr,i,expr.length),oLev(expr.charAt(i)),pLevel,0,0);
					ET.push(Erow);
					//console.log(Erow);
				}
				else{
					var Erow=new Array(id,expr.charAt(i),n1.trim(),"",oLev(expr.charAt(i)),pLevel,0,0);
					ET.push(Erow);
					//console.log(Erow);
				}
				n1="";
			}
			else if (expr.charAt(i)=="(") pLevel+=10;
			else if (expr.charAt(i)==")") pLevel-=10;
			else if (expr.charAt(i)=="{"){
				exP=expr.indexOf("}",i);
				n1+=expr.slice(i+1,exP);
				i=exP;
			}
			else n1+=expr.charAt(i);
		}
		for (var i=0;i<ET.length-1;i++){
			ET[i][3]=ET[i+1][2];
		}
	}
	function simplify(a,n){
		initialize(a);
		console.log(a);
		if (ET.length==0) return a;
		buildRET(); //Relations
		var solved=trysolve(n);
		return solved;
	}
	function evaluate(a,b,c){
		//var ans=(b=="^")?eval("Math.pow("+a+","+c+")"):eval(a+""+b+""+c);
		var an=a+""+b+""+c
		//console.log(an);
		//return (ans);
		var ans="";
		if (b=="^") ans=reArrangeTerms(getTerms(getProduct(a,c,1)));
		else if (b=="*") ans=reArrangeTerms(getTerms(getProduct(a,c,0)));
		else {
			var p=getProduct(b,c,0);
			if (p!=0) ans=reArrangeTerms(getTerms(a+""+p));
			else  ans=reArrangeTerms(getTerms(a));
		}
		if (ans.charAt(0)=="+") ans=ans.substr(1);
		return ans;
	}
	function displayAns(ans,pos,lev,n){
		var ansString="";
		if (ans.charAt(0)=="+") ans=ans.substr(1);
		var newpos=-1;
		if (pos>=0) {
			var curLev = lev;
			if (ET[pos][5]<curLev){
				if (n-1==1){
					ansString = "("+ET[pos][2]+")"+ET[pos][1]+""+ET[pos][3];
				}
				else{
					ansString = ET[pos][2]+""+ET[pos][1]+"("+ET[pos][3]+")";
				}
			}
			else{
				ansString = ET[pos][2]+""+ET[pos][1]+""+ET[pos][3];
			}
			if (ansString.charAt(0)=="+") ansString=ansString.substr(1);
			newpos = ET[pos][6]-1;
			curLev=ET[pos][5];
			while (newpos>=0 && newpos!=pos){
				//console.log(ET[pos][5]+"----"+curLev);
				if (ET[newpos][5]<curLev){
					ansString=(ET[pos][7]>1)?(ET[newpos][2]+""+ET[newpos][1]+"("+ansString+")"):("("+ansString+")"+ET[newpos][1]+""+ET[newpos][3]);
				}else{
					ansString=(ET[pos][7]>1)?(ET[newpos][2]+""+ET[newpos][1]+""+ansString):(ansString+""+ET[newpos][1]+""+ET[newpos][3]);
				}
				//console.log("newpos:"+newpos+"    ansString:"+ansString);
				pos=newpos;curLev = ET[pos][5];
				newpos = ET[pos][6]-1;
			}
		}
		else {
			ansString = ans;
		}
		return (ansString);
	}
	function trysolve(getSteps){//console.log("trySolve:"+arguments.callee.caller.name);
		for (var i=0;i<ET.length;i++){
			maxL=getMaxLev1();k=-1;//console.log("maxL:"+maxL);
			for (var j=0;j<ET.length;j++)
				if (((ET[j][4]+ET[j][5])==maxL) && ET[j][6]>=0) {k=j;break;}
			//console.log("--------------------K----------"+k);
			if (k<0) break;
			if (ET[k][3]=="") ET[k][3]=ET[k+1][3];
			var sol=evaluate(ET[k][2],ET[k][1],ET[k][3]);
			var m=(ET[k][6])-1;var n=(ET[k][7])+1;//console.log("sol:"+sol+"          m:"+m+", n:"+n);
			if (m>=0) {
				ET[m][n]=sol;ET[k][6]=-1;
				//console.log(ET[k]);
				if (getSteps) AnswerSteps.push(displayAns(sol,m,ET[k][5],n));
			}
			else {
				console.log("Answer:"+sol);
				if (getSteps) AnswerSteps.push(sol);
				return sol;
			}
		}
	}
	function getMaxLev1(){
		var maxLev=0;
		for (var i=0;i<ET.length;i++)
			if ((maxLev<(ET[i][4]+ET[i][5])) && ET[i][6]!=-1) maxLev=ET[i][4]+ET[i][5];
		
		return maxLev;
	}
	function getMaxLev0(){
		var maxLev=0,k=0;
		for (var i=0;i<ET.length;i++)
			if ((maxLev<(ET[i][4]+ET[i][5])) && ET[i][6]==0) {maxLev=ET[i][4]+ET[i][5];k=i;}
		
		return k;
	}
	function buildRET(){
		var k=getMaxLev0();
		//console.log("maxLevPos:"+k);//console.log("buildRET:"+arguments.callee.caller.name);
		if (k<0) return;
		var u=k-1,ul=0;
		while (u>=0){
			if (ET[u][6]==0) {
				ul=ET[u][4]+ET[u][5];
				break;
			}
			u--;
		}
		var d=k+1,dl=0;
		while (d<ET.length){
			if (ET[d][6]==0) {
				dl=ET[d][4]+ET[d][5];
				break;
			}
			d++;
		}
		if (dl>ul){
			//select d
			ET[k][6]=d+1;ET[k][7]=1;
			//console.log(ET[k]);
			buildRET();
		}
		else if (ul==0){
			//return
			return;
		}
		else{
			//select u
			ET[k][6]=u+1;ET[k][7]=2;
			//console.log(ET[k]);
			buildRET();
		}
		return;
	}
	function getTerms(eq){
		var i=0;var tcount=0;var term=new Array();//alert(eq);
		eq=eq.replace(/\s/g,"");
		l=eq.length;j=0;var trm="";var bpon=0;
		while (i<l){
			if (((eq.charAt(i)=='+' || eq.charAt(i)=='-')) && bpon==0) {
				term[tcount]=trm.trim();
				tcount++;
				trm="";
				trm=eq.charAt(i);
				if (trm=="(") bpon++;
				i++;
			}else{
				trm+=eq.charAt(i);
				if (eq.charAt(i)=="(") bpon++;
				if (eq.charAt(i)==")") bpon--;
				i++;
			}
		}
		term[tcount]=trm.trim();
		tcount=term.length;
		for (i=0;i<tcount;i++){
			if (term[i]=="") {term.splice(i,1);tcount--;}
		}
		//console.log("getTerms():"+term);
		return term;
	}
	function reArrangeTerms(tA){
		//a1v=a1[1].split(".");b1v=b1[1].split(".");
		//console.log("Start reArrangeTerms():"+tA);
		var varsn=new Array();var varsv=new Array();
		for (var k=0;k<tA.length;k++) {
			//console.log("a [for term k]:"+tA[k]+"   "+varsn);
			var a1=tA[k].replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			a1=removeNull(a1.split(/[{}]/));var newT="1";
			for (var a1c=0;a1c<a1.length;a1c++){
				if (a1[a1c]=="" || a1[a1c]==".") a1[a1c]=1;
				//console.log("sending..."+newT+"   "+a1[a1c]);
				newT=productOf(newT,a1[a1c]);
			}
			//console.log(newT);
			var a1=newT.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			//console.log("reArrangeTerms() a1:"+a1);
			var pi=(a1.indexOf("{")>=0)?a1.indexOf("{"):a1.length;
			var coff=a1.slice(0,pi);
			var p=new Array();p[0]=coff;p[1]=a1.slice(coff.length);
			//p=a1.split("*");console.log(p);
			if (p[0]=="") p[0]="1";
			else if (p[0]=="+" || p[0]=="-") p[0]=p[0]+"1";
			p[0]=p[0].replace(/([+\-]?)([0-9]+)([\^]+)([0-9]+)/g,"$1"+1+"*Math.pow($2,$4)");
			if (/^[a-z]+$/i.test (p[0].substring(0,1)) ) {
				cof=1;p[1]=p[0];
			}
			else{
				cof=eval(p[0]);
			}
			//console.log(p[0]+"--- "+cof);
			if (p.length==1) p.push("");
			p[1]=p[1].replace(/[.]/g,"");
			var tempP=removeNull(p[1].split(/[{}]/g));
			//console.log("p:"+p[1]);
			//tempP=p[1].split("");
			//console.log(tempP);
			for (var vc0=0;vc0<tempP.length-1;vc0++){
				var tmp=tempP[vc0].split("^");
				if (tmp[0]!=""){
					for (var vc1=1;vc1<tempP.length;vc1++){
						var tmp1=tempP[vc1].split("^");
						if (tmp1[0]!=""){
							var cc0=tmp[0].charCodeAt(0);
							var cc1=tmp1[0].charCodeAt(0);
							if (cc0>cc1) {
								var temp=tempP[vc0];tempP[vc0]=tempP[vc1];tempP[vc1]=temp;
							}
						}
					}
				}
			}
			p[1]=tempP.join("");
			//alert(p[1]);
			/*if (p[1].length>1) {
				p[1]=p[1].replace(/([a-z]+)([a-z])/g,"$1.$2");
				//p[1]=(p[1].split("")).join(".");
			}*/
			q=inArray(p[1],varsn);
			//alert(p[1]);
			//console.log("for "+p[0]+" power ="+tn);
			if (q<0) {varsn.push(p[1]);varsv.push(cof);q=varsv.length-1;}
			else {varsv[q]+=cof;}
			//console.log("varsv..."+varsv[q]+"   varsn:"+varsn[varsn.length-1]);
		}
		var newt=new Array();
		for (k=0;k<varsn.length;k++){
			if (varsv[k]==0) continue;
			else if (varsv[k]==1) cof="+";
			else if (varsv[k]==-1) cof="-";
			else if (varsv[k]>0) cof="+"+varsv[k];
			else cof=varsv[k];
			var varSN=varsn[k];//console.log(varSN);
			if (varSN=="" && (cof=="+" || cof=="-")) varSN="1";
			newt.push(cof+""+varSN.replace(/[{}]/g,""));
		}
		var newK=newt.join(""); 
		if (newK.replace(" ","")=="") newK="0";
		//console.log("Rearranged:" +newK);
		return newK;
	}
	function addAst(eq){
		var t=eq.indexOf("(");
		while (t>=0){
			bopen=1;j=t;
			while (bopen!=0){
				j++;
				if (eq.charAt(j)=="(") bopen++;
				if (eq.charAt(j)==")") bopen--;
			}
			if (eq.charAt(j+1)=="(") eq=eq.slice(0,j+1)+"~"+eq.slice(j+1,eq.length);
			t=eq.indexOf("(",j);
		}
		return eq;
	}
	function getProduct(a,b,flag){
		
		var t1=getTerms(a);var t2="";//console.log("getProduct().."+a+".."+b+".."+flag);
		var ans="";
		if (flag) {
			t2=eval(b);
			if (t2<=2){
				return getProduct(a,a,0);
			}else{
				ans=getProduct(a,getProduct(a,t2-1,1),0);
				return ans;
			}
		}
		else {
			t2=getTerms(b);
			for (var k=0;k<t1.length;k++){
				for (var l=0;l<t2.length;l++){
					ans+=""+productOf(t1[k],t2[l]);
				}
			}
			return ans;
		}
		
	}
	function inArray(needle, haystack) {
	    var length = haystack.length;
	    for(var i = 0; i < length; i++) {
	        if(haystack[i] == needle) return i;
	    }
	    return -1;
	}
	function removeNull(arr){
		var i=0;
		while (i<arr.length){
			if (arr[i]=="" || arr[i]==".") arr.splice(i,1);
			else i++;
		}
		return arr;
	}
	function productOf(a,b){
		//console.log("productOf().."+a+".."+b);
		if (!isNaN(a*1) && !isNaN(b*1)) {cof=eval(a+"*"+b);varCT="";}
		else{
			//var a1=a.replace(/([a-z.]+[a-z.\^0-9]*)/g,"*$1");
			//var b1=b.replace(/([a-z.]+[a-z.\^0-9]*)/g,"*$1");
			var a=a.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			var b=b.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			//console.log("check term1: "+a);console.log("check term2: "+b);
			a1=new Array(2);b1=new Array(2);
			a1[0]=(a.indexOf("{")>=0)?a.slice(0,a.indexOf("{")):a.slice(0,a.length);
			b1[0]=(b.indexOf("{")>=0)?b.slice(0,b.indexOf("{")):b.slice(0,b.length);
			a1[1]=a.slice(a1[0].length);
			b1[1]=b.slice(b1[0].length);
			//a1=a1.split("*");	b1=b1.split("*");
			//console.log("check term1: "+a1);console.log("check term2: "+b1);
			//a1v=a1[1].split("^");	b1v=b1[1].split("^");
			
			if (a1[0]=="") a1[0]="1";
			else if (a1[0]=="+" || a1[0]=="-") a1[0]=a1[0]+"1";
			if (b1[0]=="") b1[0]="1";
			else if (b1[0]=="+" || b1[0]=="-") b1[0]=b1[0]+"1";
			//console.log("cof.."+a1[0]+"*"+b1[0]);
			cof=eval(a1[0]+"*"+b1[0]);
			if (a1.length==1) a1[1]="";
			if (b1.length==1) b1[1]="";
			var a1v=a1[1].replace(/([.])/g,"");
			var b1v=b1[1].replace(/([.])/g,"");
			var a1v=a1v.replace(/([.]?[a-z.][\^]?[0-9]*)/g,".$1");
			var b1v=b1v.replace(/([.]?[a-z.][\^]?[0-9]*)/g,".$1");
			a1v=removeNull(a1[1].split(/[{}]/g));b1v=removeNull(b1[1].split(/[{}]/g));
			//a1v.splice(0,1);b1v.splice(0,1);
			//console.log("check variables1: "+a1v);console.log("check variables2: "+b1v);
			var varsn=new Array();var varsv=new Array();var p=null;
			for (var k=0;k<a1v.length;k++) {
				p=a1v[k].split("^");if (p[0]=="") continue;
				q=inArray(p[0],varsn);
				if (p.length==1) tn=1;
				else tn=p[1]*1;
				//console.log("for "+p[0]+" power ="+tn);
				if (q<0) {varsn.push(p[0]);varsv.push(tn);}
				else {varsv[q]+=tn;}
			}
			for (var k=0;k<b1v.length;k++) {
				p=b1v[k].split("^");if (p[0]=="") continue;
				q=inArray(p[0],varsn);
				if (p.length==1) tn=1;
				else tn=p[1]*1;
				//console.log("for "+p[0]+" power ="+tn);
				if (q<0) {varsn.push(p[0]);varsv.push(tn);}
				else {varsv[q]+=tn;}
			}
			for (var k=0;k<varsn.length-1;k++){
				for (var l=k+1;l<varsn.length;l++){
					if (varsn[k]>varsn[l]){
						t=varsn[k];varsn[k]=varsn[l];varsn[l]=t;
						t=varsv[k];varsv[k]=varsv[l];varsv[l]=t;
					}
				}
			}
			//console.log(varsn+"------"+varsv);
			varCT="";
			for (var k=0;k<varsn.length;k++){
				if (k>0) varCT+="";
				varCT+=varsn[k];
				if (varsv[k]>1) varCT+="^"+varsv[k];
			}
		}
		if (cof>0) cof="+"+cof;
		//console.log("product:"+cof+varCT);
		return (cof+""+varCT);
	}
	function getVars(tArr){
		for (var i=0;i<tArr.length;i++){
			if (tArr[i]=="(" || tArr[i]==")" || tArr[i]=="+" || tArr[i]=="-" ) continue;
			if (!isNaN(tArr[i]*1)) {vars[0]+=tArr[i]*1;varCT[0]="";}
			else{
				em=tArr[i].replace(/([a-z]+[\^]?[0-9]?)/g,"*$1");console.log(em);
				t=em.split("*");
				p=t[1].split("^");cof=0;
				if (t[0]=="") {t[0]="1";cof=1;}
				else if (t[0]=="+" || t[0]=="-"){t[0]=t[0]+"1";cof=eval(t[0]);}
				else{cof=eval(t[0]);}
				if (p.length==1) {vars[1]+=cof;varCT[1]=t[1];}
				else {vars[p[1]*1]+=cof;varCT[p[1]*1]=t[1];}
			}
		}
		var newA=new Array();
		for (var i=vars.length-1;i>=0;i--) {
			if (vars[i]!=0) {
				console.log("x^"+i+":"+vars[i]);
				varT=vars[i];
				if (vars[i]>0) varT="+"+vars[i];
				newA.push(varT+varCT[i]);
			}
		}
		
		return newA;
	}
	_simplify.compareTerms = function (b){
		if (finalAns==b) return true;
		console.log("Comparing "+finalAns+"       to   "+b);
		var b1=simplify(b,0);
		console.log("b1-----------:"+b1);
		b1=getTerms(b1);
		var varsn2=new Array();var varsv2=new Array();var p=new Array();
		for (var k=0;k<b1.length;k++) {
			var a1=b1[k].replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			a1=removeNull(a1.split(/[{}]/));var newT="1";
			for (var a1c=0;a1c<a1.length;a1c++){
				if (a1[a1c]=="" || a1[a1c]==".") a1[a1c]=1;
				console.log("sending..."+newT+"   "+a1[a1c]);
				newT=productOf(newT,a1[a1c]);
			}
			//console.log(newT);
			var a1=newT.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			console.log("compareTerms() a1:"+a1);
			var pi=(a1.indexOf("{")>=0)?a1.indexOf("{"):a1.length;
			var coff=a1.slice(0,pi);
			var p=new Array();p[0]=coff;p[1]=a1.slice(coff.length);
			//p=a1.split("*");console.log(p);
			if (p[0]=="") p[0]="1";
			else if (p[0]=="+" || p[0]=="-") p[0]=p[0]+"1";
			p[0]=p[0].replace(/([+\-]?)([0-9]+)([\^]+)([0-9]+)/g,"$1"+1+"*Math.pow($2,$4)");
			if (/^[a-z]+$/i.test (p[0].substring(0,1)) ) {
				cof=1;p[1]=p[0];
			}
			else{
				cof=eval(p[0]);
			}
			console.log(p[0]+"--- "+cof);
			if (p.length==1) p.push("");
			p[1]=p[1].replace(/[.]/g,"");
			var tempP=removeNull(p[1].split(/[{}]/g));
			//console.log("p:"+p[1]);
			//tempP=p[1].split("");
			for (var vc0=0;vc0<tempP.length-1;vc0++){
				var tmp=tempP[vc0].split("^");if (tmp[0]=="") continue;
				for (var vc1=1;vc1<tempP.length;vc1++){
					var tmp1=tempP[vc1].split("^");if (tmp1[0]=="") continue;
					var cc0=tmp[0].charCodeAt(0);
					var cc1=tmp1[0].charCodeAt(0);
					if (cc0>cc1) {
						var temp=tempP[vc0];tempP[vc0]=tempP[vc1];tempP[vc1]=temp;
					}
				}
			}
			p[1]=tempP.join("");
			
			q=inArray(p[1],varsnMain);
			console.log(p[1]+"  in "+varsnMain);
			//console.log("for "+p[0]+" power ="+tn);
			if (q<0) {return false;}
			else if (varsvMain[q]!=cof) return false;
		}
		return true;
	}
	_simplify.checkifFinalAns = function (b){
		if (finalAns==b) return true;
		console.log("Check if Final "+finalAns+"       to   "+b);
		var b1=getTerms(b);console.log(b1);
		var varsn2=new Array();var varsv2=new Array();var p=new Array();
		for (var k=0;k<b1.length;k++) {
			console.log("checking term:"+b1[k]);
			var a1=b1[k].replace(/[\^][\(]([0-9]*)[\)]/g,"^$1");
			if (a1.indexOf("(")>=0 || a1.indexOf(")")>=0) return false;
			var a1=a1.replace(/([a-z]{1}[\^]?[0-9]*)/ig,"{$1}");
			a1=removeNull(a1.split(/[{}]/));var newT="1";
			for (var a1c=0;a1c<a1.length;a1c++){
				if (a1[a1c]=="" || a1[a1c]==".") a1[a1c]=1;
				console.log("sending..."+newT+"   "+a1[a1c]);
				newT=productOf(newT,a1[a1c]);
			}
			//console.log(newT);
			var a1=newT.replace(/([a-z]{1}[\^]?[0-9]*)/g,"{$1}");
			console.log("checkifFinalAns() a1:"+a1);
			var pi=(a1.indexOf("{")>=0)?a1.indexOf("{"):a1.length;
			var coff=a1.slice(0,pi);
			var p=new Array();p[0]=coff;p[1]=a1.slice(coff.length);
			//p=a1.split("*");console.log(p);
			if (p[0]=="") p[0]="1";
			else if (p[0]=="+" || p[0]=="-") p[0]=p[0]+"1";
			p[0]=p[0].replace(/([+\-]?)([0-9]+)([\^]+)([0-9]+)/g,"$1"+1+"*Math.pow($2,$4)");
			if (/^[a-z]+$/i.test (p[0].substring(0,1)) ) {
				cof=1;p[1]=p[0];
			}
			else{
				cof=eval(p[0]);
			}
			console.log(p[0]+"--- "+cof);
			if (p.length==1) p.push("");
			p[1]=p[1].replace(/[.]/g,"");
			var tempP=removeNull(p[1].split(/[{}]/g));
			//console.log("p:"+p[1]);
			//tempP=p[1].split("");
			for (var vc0=0;vc0<tempP.length-1;vc0++){
				var tmp=tempP[vc0].split("^");if (tmp[0]=="") continue;
				for (var vc1=1;vc1<tempP.length;vc1++){
					var tmp1=tempP[vc1].split("^");if (tmp1[0]=="") continue;
					var cc0=tmp[0].charCodeAt(0);
					var cc1=tmp1[0].charCodeAt(0);
					if (cc0>cc1) {
						var temp=tempP[vc0];tempP[vc0]=tempP[vc1];tempP[vc1]=temp;
					}
				}
			}
			p[1]=tempP.join("");
			
			q=inArray(p[1],varsnMain);
			console.log(p[1]+"  in "+varsnMain);
			//console.log("for "+p[0]+" power ="+tn);
			if (q<0) {return false;}
			else if (varsvMain[q]!=cof) return false;
			
			console.log("term matched");
		}
		return true;
	}
	return _simplify;

}();

var simplifyF;
if (simplifyF == undefined) {
    simplifyF = Simplify;
}