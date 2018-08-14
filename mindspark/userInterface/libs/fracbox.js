var tooltip=function(){
 var id = 'tt';
 var top = 3;
 var left = 3;
 var maxw = 300;
 var speed = 10;
 var timer = 20;
 var endalpha = 95;
 var alpha = 0;
 var tt,t,c,b,h;
 var ie = document.all ? true : false;
 var parentOb;
 return{
  show:function(ob,v,w){
  	parentOb = ob;
   if(tt == null){
    tt = document.createElement('div');
    tt.setAttribute('id',id);
    t = document.createElement('div');
    t.setAttribute('id',id + 'top');
    c = document.createElement('div');
    c.setAttribute('id',id + 'cont');
    b = document.createElement('div');
    b.setAttribute('id',id + 'bot');
    tt.appendChild(t);
    tt.appendChild(c);
    tt.appendChild(b);
    document.body.appendChild(tt);
    tt.style.opacity = 0;
    tt.style.filter = 'alpha(opacity=0)';
    document.onmousemove = this.pos;
	document.onkeyup = this.pos;
   }
   tt.style.display = 'block';
   c.innerHTML = v;
   jsMath.ProcessBeforeShowing(c);
   tt.style.width = w ? w + 'px' : 'auto';
   tt.style.width = parseInt(c.offsetWidth)+10 + "px";
   if(!w && ie){
    t.style.display = 'none';
    b.style.display = 'none';
    tt.style.width = tt.offsetWidth;
    t.style.display = 'block';
    b.style.display = 'block';
   }
  if(tt.offsetWidth > maxw){tt.style.width = maxw + 'px'}
  h = parseInt(tt.offsetHeight) + top;
  clearInterval(tt.timer);
  tt.timer = setInterval(function(){tooltip.fade(1)},timer);
  },
  pos:function(e){
   if(document.getElementById(parentOb))
   {	  
	   var u = findPosY(document.getElementById(parentOb));//[0];
	   var l = findPosX(document.getElementById(parentOb));//position[1];
	   /*tt.style.top = (u - h) + 'px';
	   tt.style.left = (l + left) + 'px';*/
	   tt.style.top = (u - 4) + 'px';
	   tt.style.left = (l + 120)+'px';
   }
  },
  fade:function(d){
   var a = alpha;
   if((a != endalpha && d == 1) || (a != 0 && d == -1)){
    var i = speed;
   if(endalpha - a < speed && d == 1){
    i = endalpha - a;
   }else if(alpha < speed && d == -1){
     i = a;
   }
   alpha = a + (i * d);
   tt.style.opacity = alpha * .01;
   tt.style.filter = 'alpha(opacity=' + alpha + ')';
  }else{
    clearInterval(tt.timer);
     if(d == -1){tt.style.display = 'none'}
  }
 },
 hide:function(){
  clearInterval(tt.timer);
   tt.timer = setInterval(function(){tooltip.fade(-1)},timer);
  }
 };
}();

$(document).ready(function(event) {
	jQuery(".fracBox").live("click",function(event) {
		var curID	=	jQuery(this).attr("id");
		if(!jQuery("#"+curID).attr("disabled"))
		{   
			jQuery("#"+curID).blur();
			jQuery("#"+curID).focus();
		}
	});

	jQuery(".fracBox").live("keyup",function(event) {
		var unicode = (event.keyCode ? event.keyCode : event.which);
		var objid = jQuery(this).attr("id");
		var tmpArray = objid.split("_");
		var compressedID = "fracV_"+tmpArray[1];
		var storageID = "fracS_"+tmpArray[1];
		
		if(unicode==8)
		{
			jQuery(this).html(jQuery(this).html().substring(0, jQuery(this).html().length - 1));
			return false;
		}
		if(unicode==13)
		{
			jQuery("#"+objid).blur();
		}
		else
			checkFracBox(objid,compressedID,storageID);
	});

	jQuery(".fracBox").live("focusin",function(e) {
        var objid = jQuery(this).attr("id");
		var tmpArray = objid.split("_");
		var str = "fracV_"+tmpArray[1];
		var sto = "fracS_"+tmpArray[1];
		jQuery(this).html(jQuery("#"+str).val());
		if(!jQuery.browser.msie)
			tooltip.show(objid,fracConvert(jQuery(this).html(),0),document.getElementById(objid).width);
    });
	jQuery(".fracBox").live("focusout",function(e) {
		var objid = jQuery(this).attr("id");
		var tmpArray = objid.split("_");
		var str = "fracV_"+tmpArray[1];
		var sto = "fracS_"+tmpArray[1];
		if(jQuery(this).html().length < 50)
		{
			jQuery("#"+str).val(jQuery(this).text());
		}
		jQuery(this).html(fracConvert(jQuery("#"+str).val(),1));
		jsMath.ProcessBeforeShowing(objid);
		if(!jQuery.browser.msie)
			tooltip.hide();
    });
});

function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

function findPosY(obj)
{
	var curtop = 0;
	if(obj.offsetParent)
		while(1)
		{
		  curtop += obj.offsetTop;
		  if(!obj.offsetParent)
			break;
		  obj = obj.offsetParent;
		}
	else if(obj.y)
		curtop += obj.y;
	return curtop;
}


function gcd(n1, n2){
	if (n1>=n2){
		if (n1%n2 == 0) return n2;
		else return gcd(n2,n1%n2);
	}else{
		if (n2%n1 == 0) return n1;
		else return gcd(n1,n2%n1);
	}
}

function checkFracBox(e,str,sto){
		var de=document.getElementById(e).innerHTML;
		var content=fracConvert(de,0);
		if(!jQuery.browser.msie)
			tooltip.show(e,content,document.getElementById(e).width);

		document.getElementById(str).value=de;
		document.getElementById(sto).value=de;
		document.getElementById(sto).value=stripFrac(de);
 }

function stripFrac(conT){

	conT=conT.replace(/<br>/g,"");

	conT=conT.replace(/\(([^(){}]*)\)\/\(([^(){}]*)\)/g,"{($1)}\/{($2)}");
	conT=conT.replace(/\{([^{}()]*)\}\/\{([^{}()]*)\}/g,"{($1)}\/{($2)}");
	conT=conT.replace(/\{([^{}]*)\}\/\{([^{}]*)\}/g,"{$1}\/{$2}");

	conT=conT.replace(/\(([^{}()]*)\)\/([A-z0-9]+)/g,"{($1)}\/{($2)}");
	conT=conT.replace(/([A-z0-9]+)\/\(([^(){}]*)\)/g,"{($1)}\/{($2)}");
	conT=conT.replace(/([A-z0-9]+)\/([A-z0-9]+)/g,"{($1)}\/{($2)}");
	conT=conT.replace(/\{([^{}]*)\}\/\{([^{}]*)\}/g,"{$1}\/{$2}");
	conT=conT.replace(/&nbsp;/g,"");
	conT=conT.replace(/[ ]/g,"");
	var tr=String;
	checkNo = conT.replace(/{/g,"");
	checkNo = checkNo.replace(/}/g,"");
	checkNo = checkNo.replace(/\)/g,"");
	checkNo = checkNo.replace(/\(/g,"");
	var checkNo	=	checkNo.split("/");
	jQuery.trim(checkNo[0]);
	jQuery.trim(checkNo[1]);
	if(!isNaN(checkNo[0]) && !isNaN(checkNo[1]))
		conT=replFrac(conT);

	conT	=	conT.replace(/\(/g,"");
	conT	=	conT.replace(/\)/g,"");
	return conT;
}

function replFrac(content){
	var d=0;var len=0;
	while (content.indexOf('{(',d+len)!=-1){
		d=content.indexOf('{(',0);
		e=content.indexOf(')}/',d);
		num=content.substring(d+1,e+1);
		f=content.indexOf('/{(',e);
		g=content.indexOf(')}',f);
		den=content.substring(f+2,g+1);
		try {
			nN=eval(num);
		}
		catch(err){
			nN=num;
		}
		try {
			dN=eval(den);
		}
		catch(err){
			dN=den;
		}
		if (!(isNaN(nN) || isNaN(dN))) {
			mul=gcd(nN,dN);
			nN=nN/mul;dN=dN/mul;
		}
		cont=content.substring(0,d);
		cont=cont+"{"+nN+"}/{"+dN+"}";
		len="{"+nN+"}/{"+dN+"}".length;
		cont=cont+content.substring(g+2);
		content=cont;
	}
	return content;
}

function fracConvert(fracString,classD)
{

	//fracString = fracString.replace("\\","/");
	fracString=fracString.replace(/\(([^()]*)\)\/\(([^()]*)\)/g,"{($1) \\over ($2)}");
	fracString=fracString.replace(/\(([^()]*)\)\/([A-z0-9]+)/g,"{($1) \\over $2}");
	fracString=fracString.replace(/([A-z0-9]+)\/\(([^()]*)\)/g,"{$1 \\over ($2)}");
	fracString=fracString.replace(/([A-z0-9]+)\/([A-z0-9]+)/g,"{$1 \\over $2}");

	return "<span class='math'>"+fracString+"</span>";
}