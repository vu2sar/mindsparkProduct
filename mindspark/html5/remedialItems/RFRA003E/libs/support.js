
st2count=0;
idgen=0;
stcount=0;

click=0;

 function smalltable(par,mul,stat)
 {
 	$("#smtable").css({'z-index':'5','display':'block'});
dw=$(document).width();
dh=$(document).height();


 	stcount=0;
 	desquot=par;
 	despar=mul;
 	stsol=par*mul;
 
 	strl=par.toString();
	nor=strl.length+3;
		
 	$('#table').css("width",10*nor+"%");
 	$('#table').css("height",10*5+"%");
 	$('#table').css("border","1px solid");
 	$('#table').css("border-color","#515151");
	sw=$('#smtable').width();
	sh=$('#smtable').height();
	 $("#stbutton").css("font-size",(sw+sh)/50+"px");
	   $("#statbutton").css("font-size",(sw+sh)/50+"px");
	 $("#dynsttext").css("font-size",(sw+sh)/40+"px");
	  $("#desheading").css("font-size",(sw+sh)/40+"px");
	   $('#stbutton').css('display','block');
	
	//alert(nor);
	
for(i=0;i<5;i++)
{
	 newRow = document.createElement("tr");
	for(j=0;j<nor;j++)
	{
		if(i==1)
		{
			if(j>=2)
			{
				if(st2count!=strl.length)
				{
				ststart(strl[st2count],0);
				st2count++;
				}
				else
				ststart("",0);
			}
			else
			ststart("",0);
		}
		else if(i==2)
		{
			if(j==1)
			ststart("X",0);
			else if(j==strl.length+1)
			ststart(mul,0);
			else
			ststart("",0);
		}
		else if(i==3)
		{
			if(j>=1 && j<=strl.length+1)
			{
				ststart("",1);
			}
			else
			ststart("",0);
		}
		
		else
 	ststart("",0);
 	
 	
 	
 	
 	
    }
 
}
if(stat==1)
{
	staticsmall();
}

 }
 
 
 
function ststart(num,tb)
{

var table=document.getElementById("table");
var newCol = document.createElement("td");
newCol.setAttribute("class","stb");
newCol.setAttribute("bgcolor", "#66CDAA");
newCol.setAttribute("align", "center");
//newCol.setAttribute("border", "1px solid black");
//newCol.setAttribute("border-color","red");
//alert((sw+sh)/20);
newCol.setAttribute("width",(sw+sh)/20+"px");
newCol.setAttribute("height",(sw+sh)/20+"px");
$(".stb").css("font-size",(sw+sh)/25+"px");



if(tb==0)
{
var newTxt = document.createTextNode(num);
//newTxt.setAttribute("width",(sw+sh)/20+"px");
	//newTxt.setAttribute("height",(sw+sh)/20+"px");
newCol.appendChild(newTxt);
newRow.appendChild(newCol);
table.getElementsByTagName("tbody")[0].appendChild(newRow); // appends it to the first tbody element
}	
if(tb==1)
{
	//alert("into");
	var txtbox=document.createElement("input");
	txtbox.setAttribute("class","txtstyle");
	txtbox.setAttribute("id","sttb"+idgen);
	txtbox.setAttribute("maxlength","1");
	//txtbox.setAttribute("readonly",false);
	//txtbox.attachEvent("onkeyup",stkeyup);
	//txtbox.addEventListener("onclick",stkeyup);
	
	
	
	
	newCol.appendChild(txtbox);
newRow.appendChild(newCol);
table.getElementsByTagName("tbody")[0].appendChild(newRow);


$("#sttb"+idgen).keyup(function(e){
		stkeyup(e);
	});

$(".txtstyle").css("font-size",(sw+sh)/25+"px");


$("#sttb"+idgen).css("top",63+"%");

if(strl.length==5)
$("#sttb"+idgen).css("left",13.8+idgen*12.5+"%");
else
$("#sttb"+idgen).css("left",calcleft()+idgen*dynleft()+"%");

if(strl.length==5)
$("#sttb"+idgen).css("width",9+"%");
else
$("#sttb"+idgen).css("width",9+(6-strl.length)+"%");
$("#sttb"+idgen).css("height",14+"%");

	
idgen++;	
}
$('#stbutton').val('Check');

if(strestart==1)
{
  //  var temp= $('#dynsttext').position().top;
  		 $('#dynsttext').css("top",75+'%');
  		 $('#stbutton').css("top",60+'%');
}
else
{
	stbuttop=$('#stbutton').position().top;
	sttextop= $('#dynsttext').position().top;
}

}
function statcheck()
{
	removetable();
	procedue();
}
function staticsmall()
{
	$('#stbutton').css('display','none');
	$('#statbutton').css('display','block');
	$('#statbutton').val('OK');
  var smallsol=stsol.toString();
   if(smallsol.length==idgen)
   {
   	statstart=0;
   }
   else
   {
   	statstart=1;
   }
  
  
  for(i=statstart;i<idgen;i++)
  {
  	if(statstart)
  	$('#sttb'+i).val(smallsol[i-1]);
  	else
  		$('#sttb'+i).val(smallsol[i]);
  }
}
strestart=0;
function stkeyup(e)
{
	
	str=(e.currentTarget.id).toString();
	stpres=parseInt(str[str.length-1]);
	if(stpres!=0)
	{
	stpres--;
	$("#sttb"+stpres).focus();
	}
	else{
		$("#sttb1").focus();
	}
}


function calcleft()
{
	var retl=0;
	var templ=5-strl.length-1;
	while(templ)
	{
      		retl=retl+Math.pow(2,templ);
      		templ--;
	}
	return 16+retl;
}
function dynleft()
{
	if(strl.length==4)
	return 14;
	if(strl.length==3)
	return 17;
	if(strl.length==2)
	return 20;
	if(strl.length==1)
	return 25;
	
	
}
	
$(window).resize(function() {
	sw=$('#smtable').width();
	sh=$('#smtable').height();
	
//	if(dw!=$(document).width() && dh!=$(document).height())
	//{
	
	$(".stb").css("width",(sw+sh)/20+"px");
	$(".stb").css("height",(sw+sh)/20+"px");
	if(navigator.userAgent.match(/ipad/i)!=null)
	{
		$(".txtstyle").css("font-size","12px");
		$(".stb").css("font-size","12px");
	}
		
	else	
	{
		$(".txtstyle").css("font-size",(sw+sh)/25+"px");
		$(".stb").css("font-size",(sw+sh)/25+"px");
	}
		
		
	
	 $("#stbutton").css("font-size",(sw+sh)/50+"px");
	  $("#statbutton").css("font-size",(sw+sh)/50+"px");
	  $("#dynsttext").css("font-size",(sw+sh)/40+"px");
	   $("#desheading").css("font-size",(sw+sh)/40+"px");
	    $("#desheading").css("top",0.2+"%");
	   
	   for(i=0;i<chkarr.length;i++)
	   {
	  
	   $('#t'+chkarr[i]).css("top",(($('#tabledes').height()-$('#deshedtext').height())/60)*(i+1)+"%");
  			 $('#t'+chkarr[i]).css("font-size",(($('#tabledes').width())/9)+"px");
  			}
  		//}
  		
  	/*	else if(dw!=$(document).width() && dh==$(document).height())
  		{
  			$(".stb").css("width",(sw)/9+"px");
	//$(".stb").css("height",(sw)/10+"px");
	$(".stb").css("font-size",(sw)/12+"px");
	$(".txtstyle").css("font-size",(sw)/12+"px");
	 $("#stbutton").css("font-size",(sw)/20+"px");
	  $("#statbutton").css("font-size",(sw)/20+"px");
	  $("#dynsttext").css("font-size",(sw)/20+"px");
	   $("#desheading").css("font-size",(sw)/19+"px");
	      $("#desheading").css("top",0.2+"%");
	   
	   for(i=0;i<chkarr.length;i++)
	   {
	  	 $('#t'+chkarr[i]).css("font-size",(($('#tabledes').width())/8.45)+"px");
  			}
  		}
  		else if(dw==$(document).width() && dh!=$(document).height())
  		{
  			//$(".stb").css("width",(sw+sh)/20+"px");
	    $(".stb").css("height",(sh)/10+"px");
	    $(".stb").css("font-size",(sh)/10+"px");
	     $(".txtstyle").css("font-size",(sh)/12+"px");
	 $("#stbutton").css("font-size",(sh)/25+"px");
	  $("#statbutton").css("font-size",(sh)/25+"px");
	  $("#dynsttext").css("font-size",(sh)/20.5+"px");
	   $("#desheading").css("font-size",(sh)/20.5+"px");
	    $("#desheading").css("top",0.2+"%");
	   
	   for(i=0;i<chkarr.length;i++)
	   {
	  
	   $('#t'+chkarr[i]).css("top",($('#tabledes').height()/52)*(i+1)+"%");
  			 $('#t'+chkarr[i]).css("font-size",($('#tabledes').height()/20.5)+"px");
  			}
  		}*/

  
});
	

function stvalidate()
{
	var myval=""; // regular array (add an optional integer
//myCars[0]="Saab";       // argument to control array's size)
//myCars[1]="Volvo";
//myCars[2]="BMW";
	
	
	stcount++;
  for(i=0;i<=strl.length;i++)
  {
  	
  	myval=myval+$("#sttb"+i).val();
  	
  }
 // alert(myval);
  //alert("myval:"+parseInt(myval,10));
 
 
  if(parseInt(myval,10)==stsol)
  {
  	stcount=0;
  	//alert("corect");
  	//call to display table
  	//hide the smalltable
  	explainextention();
  	adddiv(desquot,despar);
  }
  else
  {
  	if(stcount==1)
  	{
  $('#dynsttext').text("Check your multiplication!");
   colortexbox();
  	}
  	else if(stcount==2)
  	{
  		 $('#dynsttext').text("Your product is still incorrect. Try multiplying again!");
  		   colortexbox();
  	}
  	else if(stcount==3)
  	{
  		 $('#dynsttext').text("That is not correct. Check your multiplication again!");
  		  colortexbox();
  	}
  	else 
  	{
  		
  		strestart=1;
  		 $('#dynsttext').text("The correct answer is "+stsol);
  		var temp= $('#dynsttext').position().top;
  		 $('#dynsttext').css("top",60+'%');
  		 $('#stbutton').css("top",85+'%');
  		 $('#stbutton').val('OK');
  		 var varans=stsol.toString();
  		 if(varans.length==idgen)
  		 {
  		 crcktst=0;
  		// alert("f"+idgen+'---'+varans.length);
  		 }
  		 else
  		 {
  		 	// alert("else"+idgen+'---'+varans.length);
  		 crcktst=1;
  		  $('#sttb0').val('');
  		   $('#sttb0').css('color','black');
  		  $('#sttb0').attr("readonly","true");
  		 // document.getElementById("sttb0").readOnly = true;
  		 }
  		 
  		  for(i=crcktst;i<idgen;i++)
         {
         	
         	if(crcktst)
         	 $('#sttb'+i).val(varans[i-1]);
           else
  		    $('#sttb'+i).val(varans[i]);
  		   
  		   
  		   $('#sttb'+i).css('color','black');
  		    $('#sttb'+i).attr("readonly","true");
  		    // document.getElementById("sttb"+i).readOnly = true;
  		} 
  		 
  		  $("#stbutton").click(function(){
  		  	if( $('#stbutton').val()=='OK' && stcount==5)
  		  	{
  		  	explainextention();
     	 adddiv(desquot,despar);
     	 }
   
   });
  		 
  		
  	}
  	
  }
  
  
	

     
}
function colortexbox()
{
	
	 for(i=0;i<=strl.length;i++)
    {
    	if($("#sttb"+i).val()=="")
    	{
    		$("#sttb"+i).css("color","black");
    	}
    	else
    	{
    		$("#sttb"+i).css("color","red");
    	}
     }
}
chkarr=new Array();

 function adddiv(desquot,despar)
  		{
  			removetable();
  			$('#desheading').css('display','block');
  			 
  			if(despar<10 && despar>=0)
  			{
  				var res=false;
  				for(i=0;i<chkarr.length;i++)
  				{
  					if(chkarr[i]==despar)
  					{
  						res=true;
  						break;
  					}
  				}
  			//var res=jQuery.inArray(par, arr);
  			//alert(res);
  			if(!res)
  			{
  				creatediv(desquot,despar);
  			}
  			else
  			{
  				manidiv(despar);
  			}
  			}
  		}
  		countarr=0;
  		function creatediv(desquot,despar)
  		{
  			if(despar=="")
  			{
  				despar=0;
  			}
  			//document.getElementById("tables").innerHTML=xmlDoc.getElementsByTagName("tables")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
  			//$('#tables').css("left",$('#mainright').position().left+($('#mainright').width()-$('#tables').width)/2);
  			
  			str=desquot+" x "+despar+" = "+(desquot*despar);
  			$("#tabledes").append("<span id=t"+despar+" class='tabledivr'>"+str+"</span>");
  			$('#t'+despar).css("position","absolute");
  			$('#t'+despar).css("left","5%");
  			$('#t'+despar).css("top",(($('#tabledes').height()-$('#deshedtext').height())/60)*(countarr+1)+"%");
  			 $('#t'+despar).css("font-size",(($('#tabledes').width())/9)+"px");
  			
  		
  			
  			chkarr[countarr]=despar;
  			countarr++;
  		
  		}
  		
  		function manidiv(par)
  		{
  			
  			 $('#t'+par).fadeOut("50").fadeIn("50").fadeOut("50").fadeIn("50").fadeOut("50").fadeIn("50");
  			  
  			
  		}

function removetable()
{
	var p2 = document.getElementById('table');
	var p1=document.getElementById('smtable');
    //  p2.parentNode.removeChild(p2);
    p1.removeChild(p2);
    
    var p3 =document.createElement('table');
    p3.setAttribute("id","table");
	p3.setAttribute("cellpadding","0px");
	p3.setAttribute("cellspacing","0px");
    var p4=document.createElement('tbody');
    p3.appendChild(p4);
    p1.appendChild(p3);
     $('#dynsttext').text("");
     	 $('#stbutton').val('Check');
     	 $('#stbutton').css('display','none');
     	  $('#statbutton').css('display','none');
st2count=0;
idgen=0;
stcount=0;
    
    
    
    
  
}