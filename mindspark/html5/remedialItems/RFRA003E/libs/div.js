var wholeNoQues=1;
var digitBeforDecim=0;
var curPos=0;
var putdecimal=0;
var successUserAns=0;
var d;
var noAfterDecimal=0;
var quitEnterd=0;
var wholeNoQuit=0;
var NoAfterDecimalChk=0;
var decimalDivide;
var remMode='A';
var putDecimalAttempt=0;
var dividendOld;
var startDecimalFlag=0;
var tempDifference = 0;


$(window).unload( function () {
	if ($.browser.mozilla) {
		//window.location="division.html";
		window.location.reload();
	$('input').val("");
	}
else
		{
	window.location.reload();
	}
} );
//function start()
//{

$(document).ready(function() {
	
dividendNew	=	gameObj.Aval;
divisorNew	=	gameObj.Bval;

if(parseInt(dividendNew)<parseInt(divisorNew))
{
	putdecimal = 1;
	startDecimalFlag = 1;
}
	
//----------
if((dividendNew/divisorNew).toString().split(".")[0].length)
	wholeNoQuit	=	(dividendNew/divisorNew).toString().split(".")[0].length;
if((dividendNew).toString().indexOf(".")>0)
{
	NoAfterDecimalChk=(dividendNew).toString().split(".")[1].length;
}
if(NoAfterDecimalChk==2)
{
	dividendOld	=	dividendNew;
	//var checkDecimDividendLength	=	dividendNew.toString().split(".")[1].length;
	/*for(j=0;j<checkDecimDividendLength;j++)*/
	dividendNew	=	roundNumber((dividendNew * 100),1);
	var quotientTest=roundNumber((dividendNew/divisorNew),2);
	var quotientTestArr=quotientTest.toString().split(".");
	decimalDivide	=	"no";
	if(quotientTestArr[1])
	{
		dividendOldLength	=	dividendNew.toString().length;
		d=dividendOldLength+1;
		decimalDivide	=	"yes";
		noAfterDecimal	=	quotientTestArr[1].toString().length;
		digitBeforDecim	=	quotientTestArr[0].toString().length;
		for(j=0;j<noAfterDecimal;j++)
			dividendNew	=	dividendNew * 10;
	}
	remMode='D';
	NoAfterDecimalChk=0;
}
//---------------
else if(NoAfterDecimalChk==0)
{
	var quotientTest=(dividendNew/divisorNew);
	
	var quotientTestArr=quotientTest.toString().split(".");
	if(quotientTestArr[1].length>3)
	{
		quotientTest=(dividendNew/divisorNew).toFixed(3);
	
		quotientTestArr=quotientTest.toString().split(".");
	}
	decimalDivide	=	"no";
	dividendOld	=	dividendNew;
	if(quotientTestArr[1])
	{
		dividendOldLength	=	dividendNew.toString().length;
		d=dividendOldLength+1;
		decimalDivide	=	"yes";
		noAfterDecimal	=	quotientTestArr[1].toString().length;
		digitBeforDecim	=	quotientTestArr[0].toString().length;
		for(j=0;j<noAfterDecimal;j++)
			dividendNew	=	dividendNew * 10;
	}
}
else
{
	dividendOld	=	dividendNew;
	decimalDivide	=	"no";
	var checkDecimDividendLength	=	dividendNew.toString().split(".")[1].length;
	//alert(checkDecimDividendLength);
	for(j=0;j<checkDecimDividendLength;j++)
		dividendNew	=	dividendNew * 10;
}

//---------------
dividendNew	=	parseInt(dividendNew);
divisorNew	=	parseInt(divisorNew);
dividendlength=dividendNew.toString().length;
divisorlength=divisorNew.toString().length;

/*alert(dividendlength+divisorlength);
if((dividendlength+divisorlength)>11 || (divisorNew>dividendNew) || divisorNew==0 || dividendNew==0 )
{
	if((dividendlength+divisorlength)>11)
	{
	alert("Please check the number of digits entered for dividendNew and divisor. It should not exceed 11.");
	}
	else if(divisorNew>dividendNew)
	{
	alert("Please check the value entered for divisor. It should not be greater than dividend.");
	}
	else if(divisorNew==0 || dividendNew==0)
	{
	alert("Please check the values entered for dividendNew and divisor. It should be more than 0.");
	}
	else 
	{
	alert("Please check the values entered for dividendNew and divisor. Special characters are not allowed.");
	}
	return false;
}*/

	try
	{
		/*------------------- Checking Browser compatability -------------*/
 if ($.browser.msie) {
                   // xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); //If browser == IE, get ActiveX object
                    xmlhttp =  new XMLHttpRequest();
                  

                    xmlhttp.open("GET","../src/english1.xml", false);  //Open the file using the GET routine
                    xmlhttp.send();  //Send request
                   xmlDocNew = xmlhttp.responseXML;
                      text = xmlhttp.responseText;  //xmlDocNew holds the document information now
                     var parser=new DOMParser();
                    
 			         xmlDocNew=parser.parseFromString(text,"text/xml");
                                    
                                      } 
                   else if ($.browser.mozilla) {
                  
                    xmlhttp =  new XMLHttpRequest();
                     
                    xmlhttp.open("GET",'../src/english1.xml', false);  //Open the file using the GET routine
                   
                    xmlhttp.send(); 
                     //Send request
                                           
                    text = xmlhttp.responseText;  //xmlDocNew holds the document information now
                     var parser=new DOMParser();
                    
 			         xmlDocNew=parser.parseFromString(text,"text/xml");
                          
                     }
                     else 
                     {
                    xmlhttp = new XMLHttpRequest();
                    xmlhttp.open("GET",'../src/english1.xml', false);  //Open the file using the GET routine
                    xmlhttp.send(null);  //Send request
                    text = xmlhttp.responseText; 
                    var parser=new DOMParser();
 			        xmlDocNew=parser.parseFromString(text,"text/xml");                
                      }
 }catch(err)
 {
 	alert("browser is not supported"+err);
 }
loaded();
levelindex=0;
timeinterval=null;

levelsflow = new Array();
levelsflow[0]=new flowSteps(divisorlength+1,divisorlength+dividendlength,dividendlength-noAfterDecimal,"dividend");

objectArray=new Array();
enableinputsboxs=true;
numberofattempts=0;
first=true;
currentid=0;
quotintallattempts=0;
instructionallowed=true;
attempts=0;
inputvalue=0;
divisionpart=0;
headpart=document.getElementById("headpart");
bodypart=document.getElementById("bodypart");
question=document.getElementById("question");
var hold=false;
var quotienttest=document.getElementById("quotienttest");
var remaintest=document.getElementById("remaintest");
inputelementsblocked=document.getElementsByClassName("inputs");
inputelementsblocked=document.getElementsByClassName("inputsfill");
question.innerHTML=xmlDocNew.getElementsByTagName("Question")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue + dividendOld + " ÷ " +divisorNew;
headpart.innerHTML=xmlDocNew.getElementsByTagName("intro")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
quotienttest.innerHTML=xmlDocNew.getElementsByTagName('Quotient')[0].getElementsByTagName('span')[0].childNodes[0].nodeValue;
remaintest.innerHTML=xmlDocNew.getElementsByTagName('Remainder')[0].getElementsByTagName('span')[0].childNodes[0].nodeValue;

for(i=0;i<dividendlength;i++)
{
	if(decimalDivide=="no")
	{
		if(i==0)
		{
			if(i==dividendlength-1)
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),false,'none',1,0,true,true,false);
			}
			else
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),true,'farword',1,0,false,true,true);
			}
		}
		else
		{
			if(i==dividendlength-1)
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),false,'none',1,0,false,true,false);
			}
			else
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),true,'farword',1,0,false,true,false);
			}
		}
	}
	else
	{
		if(i==0)
		{
			if(i==dividendlength-1)
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),false,'none',1,0,true,true,false);
			}
			else
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),true,'farword',1,0,false,true,true);
			}
		}
		else if(i <= dividendOldLength)
		{
			if(i==dividendlength-1)
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),false,'none',1,0,false,true,false);
			}
			else
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),true,'farword',1,0,false,true,false);
			}
		}
		else
		{
			if(i==dividendlength-1)
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),false,'none',1,0,false,true,false);
			}
			else
			{
				objectArray[i]=new textbox("disable",parseInt(dividendNew.toString().charAt(i)),"t_"+(divisorlength+1+i),false,'none',1,0,false,true,false);
			}
		}
	}
}
if(NoAfterDecimalChk!=0)
	$("#td"+(divisorlength + dividendlength)).prepend($("#decimalSpanDiv").html());
else if(remMode=='D')
	$("#td"+(divisorlength + dividendlength - 3)).prepend($("#decimalSpanDiv").html());
levelsflow[1]=new flowSteps(1,divisorlength,divisorlength,"divisor");
for(i=0;i<divisorlength;i++)
{
	
		if(i==0)
		{
   if(i==divisorlength-1)
	{
	objectArray[objectArray.length]=new textbox("disable",parseInt(divisorNew.toString().charAt(i)),"t_"+(1+i),false,'none',1,1,true,false,false);
	}else
	{
    objectArray[objectArray.length]=new textbox("disable",parseInt(divisorNew.toString().charAt(i)),"t_"+(1+i),true,'farword',1,1,true,false,false);
   }
   }else
   {
   	if(i==divisorlength-1)
	{
		objectArray[objectArray.length]=new textbox("disable",parseInt(divisorNew.toString().charAt(i)),"t_"+(1+i),false,'none',1,1,false,false,false);
	}else
	{
   	objectArray[objectArray.length]=new textbox("disable",parseInt(divisorNew.toString().charAt(i)),"t_"+(1+i),false,'none',1,1,false,false,false);
   }
  }
}

creatingTextBoxes();
$("#correctStep div:eq(0)").text(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue);
$("#correctStep div:eq(1)").text(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[1].childNodes[0].nodeValue);
$("#correctStep div:eq(2)").text(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[2].childNodes[0].nodeValue);

/*for(var l=0;l<30;l++)
{
	alert(objectArray[l].value);
}*/

hei=$('#mainright').height();
wid=$('#mainright').width();
if(navigator.userAgent.match(/ipad/i)!=null)
{
	$('input').css({"font-size":"12px"});
	//$('input').css("font-size",($(document).height()+$(document).width())/112);
}
else
{
	$('input').css({"font-size":"16px"});
	//$('input').css("font-size",($(document).height()+$(document).width())/90);
	
	if(navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
	{
		var inputs=document.getElementsByTagName('input');
		for(var i=0;i<(inputs.length);i++)
		{
			if(inputs[i].getAttribute("type") == 'tel');
			{
				inputs[i].setAttribute("type","text");
			}
		}
	}
}

 //handling keydown event to supporse alphabets and special characterstics
$('input[type=tel]').bind('keydown', function(e){
	
	if(navigator.userAgent.match(/ipad/i)!=null)
	{
		if(e.currentTarget.id.charAt(1)=='_')
		{
			var index=findarraylocation(e.currentTarget.id);	
			if(objectArray[index].status=="enable")
			{
				$("#"+e.currentTarget.id).css('background','none');
			}
		}
		else
		{
			if(e.currentTarget.id=="st4" || e.currentTarget.id=="st5" )
				$("#"+e.currentTarget.id).css('background','none');
		}
	}
   if(e.shiftKey)
   {
		return(false);
   }
	else
	{
		var num=0;
		if(navigator.userAgent.match(/ipad/i)!=null || navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
		{
			if((e.which>=48 && e.which<=57) || e.which==8 || e.which==13)
			{
				if(navigator.userAgent.match(/ipad/i)!=null)
				{
					if(e.which>=48 && e.which<=57)
					{
						if(e.currentTarget.id!="quotval" && e.currentTarget.id!="remval" )
						{
							$("#"+currentid).val(parseInt(String.fromCharCode(e.which)));
						//$("#"+e.currentTarget.id).val(parseInt(String.fromCharCode(e.which)));
						}
					}
				}
				num=1;
			}
		}
		else
		{
			if((e.which>=48 && e.which<=57) || e.which==8 || e.which==13 || (e.which>=96 && e.which<=105) || (e.which>=37 && e.which<=40) )
			{
				num=1;
			}
		}
 	
 	switch(num)
	{
           case 0:{   // slash
        	   if( navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
        		   $(this).blur();
                return(false);  
           }
           // and so forth
     }
   }
});




//selecting value of the texbox while gaining focus




$('input').focus(function (e){
	
	if(navigator.userAgent.match(/ipad/i)!=null)
	{
		
		if(e.currentTarget.id.charAt(1)=='_')
			{
				
				 var index=findarraylocation(e.currentTarget.id);
		if(objectArray[index].status=="enable")
			{
			
		$("#"+e.currentTarget.id).css('background-color','#C0C0C0');
			}else
			{
				$("#"+e.currentTarget.id).blur();
			}
			}else
				{
		if(e.currentTarget.id=="st4" || e.currentTarget.id=="st5" )
			{ 
			$("#"+e.currentTarget.id).css('background-color','#C0C0C0');
				}
				}
		$("#"+e.currentTarget.id).val("");		
	}else
		{
			if(e.currentTarget.id=="remval" || e.currentTarget.id=="quotval")
			{
				$("#"+e.currentTarget.id).select();
			}else
			{
	     var index=findarraylocation(e.currentTarget.id);
		if(objectArray[index].status=="enable" && enableinputsboxs)
			{
	$("#"+e.currentTarget.id).select();
	
	
	}else
	{
		$("#"+e.currentTarget.id).blur();
	}
		}
		}
});


$("input").mouseup(function(e){
        e.preventDefault();
});

if(navigator.userAgent.match(/ipad/i)!=null)
{
	$("input").blur(function(e){
	if(e.currentTarget.id.charAt(1)=='_')
	{
	var index=findarraylocation(e.currentTarget.id);
	if(objectArray[index].status=="enable")
		{
	$("#"+e.currentTarget.id).css('background','none');
		}
		}else
			{
	if(e.currentTarget.id=="st4" || e.currentTarget.id=="st5" )
		$("#"+e.currentTarget.id).css('background','none');
			}
	});
}



editableBlocks();
resize();

});
//}

function loaded()
{
 isiPad1 = navigator.userAgent.match(/iPad/i) != null;

// For use within iPad developer UIWebView
// Thanks to Andrew Hedges!
//var ua1 = navigator.userAgent;
//isiPad1 = /iPad/i.test(ua1) || /iPhone OS 3_1_2/i.test(ua1) || /iPhone OS 3_2_2/i.test(ua1);


}



function creatingTextBoxes()
{  
	var level=0;
	var lasttextbox=0; 
	quotient=Math.floor(dividendNew/divisorNew);
	queslength=quotient.toString().length;
	var quotientlevel=0;
	var dividentlevel=0;
	var dividentstart=0;
	var quoientpos=0;
	var quotientvalue=parseInt(quotient.toString().charAt(0));
	difference=0;
	var product=0;
	var length=0;
	while(true)
	{
		
		if(level==0)
		{
			var pos=0;
			for(var i=0;i<dividendlength;i++)
			{
				if(i<dividendlength-queslength)
				{
					value=10;
					objectArray[objectArray.length]=new textbox("disable",value,"t_"+(11+divisorlength+i),false,'none',0,2,false,false,false);
				}
				else
				{
					value=parseInt(quotient.toString().charAt(pos));
					pos++;
					if(pos==1)
					{
						dividentlevel=divisorlength+i+1;
						quotientlevel=11+divisorlength+i+1;
					}
					objectArray[objectArray.length]=new textbox("disable",value,"t_"+(11+divisorlength+i),false,'none',1,2,false,false,false);
				}
			}
			levelsflow[2]=new flowSteps(11+divisorlength,11+divisorlength+dividendlength,dividendlength,"quotientall");
				
			var startbox=parseInt(objectArray[0].id.split('_')[1])+20;
			product=quotientvalue*divisorNew;
			if(product.toString().length==1)
			{
				if(product > dividendNew.toString().charAt(0))
				{
					product	=	"0"+product;
				}
			}
			for(var i=0;i<=(dividendlength-queslength);i++)
			{
				if(i==0)
				{
					objectArray[objectArray.length]=new textbox("disable",parseInt(product.toString().charAt(i)),"t_"+(startbox+i),true,'farword',1,3,true,false,false);
				}
				else
				{
					if(i==(dividendlength-queslength))
					{
						objectArray[objectArray.length]=new textbox("disable",parseInt(product.toString().charAt(i)),"t_"+(startbox+i),false,'none',1,3,false,false,false);
					}
					else
					{
						objectArray[objectArray.length]=new textbox("disable",parseInt(product.toString().charAt(i)),"t_"+(startbox+i),true,'farword',1,3,false,false,false);
					}
				}
			}
			
			{
				levelsflow[3]=new flowSteps(startbox,startbox+(dividendlength-queslength),(dividendlength-queslength+1),"product");
				level++;	
			}
			
			if(parseInt(dividendNew.toString().substr(dividentstart,divisorlength))>=divisorNew)
			{
				difference=parseInt(dividendNew.toString().substr(dividentstart,divisorlength))-(quotientvalue*divisorNew);
				length=divisorlength;
			}
			else
			{
				difference=parseInt(dividendNew.toString().substr(dividentstart,divisorlength+1))-(quotientvalue*divisorNew);
				length=divisorlength+1;
			}
			
			}
		else
		{
			var forenter=false;
			if(difference!=0)
			{
	
				for(i=0;i<(length-difference.toString().length);i++)
				{
					forenter=true;
					if(i==0)
					objectArray[objectArray.length]=new textbox("disable",10,"t_"+((level*20+11)+(dividentlevel-length)+i),false,'none',0,4,false,true,false);
					else 
					objectArray[objectArray.length]=new textbox("disable",10,"t_"+((level*20+11)+(dividentlevel-length)+i),true,'back',0,4,false,true,false);
				}
				for(i=0;i<difference.toString().length;i++)
				{
					if(i==0 && !forenter)
					{
						if(i==difference.toString().length-1)
						{
							objectArray[objectArray.length]=new textbox("disable",parseInt(difference.toString().charAt(i)),"t_"+(((level*20)+11)+(dividentlevel-length)+(length-difference.toString().length)+i),false,'none',1,4,true,true,false);
						}
						else
						{
							objectArray[objectArray.length]=new textbox("disable",parseInt(difference.toString().charAt(i)),"t_"+(((level*20)+11)+(dividentlevel-length)+(length-difference.toString().length)+i),false,'none',1,4,false,true,false);
						}
					}
					else if(i==difference.toString().length-1)
					{
						objectArray[objectArray.length]=new textbox("disable",parseInt(difference.toString().charAt(i)),"t_"+(((level*20)+11)+(dividentlevel-length)+(length-difference.toString().length)+i),true,'back',1,4,true,true,false);
					}
					else
					{
						objectArray[objectArray.length]=new textbox("disable",parseInt(difference.toString().charAt(i)),"t_"+(((level*20)+11)+(dividentlevel-length)+(length-difference.toString().length)+i),true,'back',1,4,false,true,false);
					}
				}
			}
			else
			{
				for(i=0;i<length;i++)
				{
					if(i==0)
						if(i==length-1)
							objectArray[objectArray.length]=new textbox("disable",10,"t_"+(((level*20)+11)+(dividentlevel-length)+i),false,'none',0,4,true,true,false);
						else
							objectArray[objectArray.length]=new textbox("disable",10,"t_"+(((level*20)+11)+(dividentlevel-length)+i),false,'none',0,4,false,true,false);
					else 
						if(i== length-1)
							objectArray[objectArray.length]=new textbox("disable",10,"t_"+(((level*20)+11)+(dividentlevel-length)+i),true,'back',0,4,true,true,false);
						else
							objectArray[objectArray.length]=new textbox("disable",10,"t_"+(((level*20)+11)+(dividentlevel-length)+i),true,'back',0,4,false,true,false);
				}
			}
			
			dividentstart++;
			//if(startDecimalFlag == 0)
			levelsflow[levelsflow.length]=new flowSteps(((level*20)+11)+(dividentlevel-length),((level*20)+11)+(dividentlevel),length,"difference");
			
			while(true)
			{
				//console.log(difference);
				if(difference<divisorNew && dividentlevel<divisorlength+dividendlength)
				{
					//console.log("111");
					if(NoAfterDecimalChk==0 && decimalDivide=="yes")
					{
						//console.log("222 "+ dividentlevel+" -- > "+dividendOldLength);
						if(dividentlevel > dividendOldLength)
						{
						//	console.log("333");
							//startDecimalFlag = 0;
							if(startDecimalFlag == 1)
								levelsflow[levelsflow.length]=new flowSteps(divisorlength+d+1,divisorlength+dividendlength,1,"carryZero");
							else
								levelsflow[levelsflow.length]=new flowSteps(divisorlength+d,divisorlength+dividendlength,1,"carryZero");	
							d++;
						}
					}
					var index=findarraylocation("t_"+(dividentlevel+1));
					var number=objectArray[index].value;
					objectArray[objectArray.length]=new textbox("disable",number,"t_"+(((level*20)+11)+dividentlevel),false,'none',1,levelsflow.length,true,false,false);
					difference=parseInt(difference.toString()+number.toString());
					length=difference.toString().length;
					levelsflow[levelsflow.length]=new flowSteps((((level*20)+11)+dividentlevel),(((level*20)+11)+dividentlevel),1,"bringdown");
					dividentlevel++;
					levelsflow[levelsflow.length]=new flowSteps(quotientlevel,quotientlevel,1,"quotient");
					quoientpos++;     
					quotientlevel++;			
				}
				else
				{
					break;
				}
			}
			if(difference<divisorNew)
			{
				break;
			}
			
			product=divisorNew*parseInt(quotient.toString().charAt(quoientpos));
			for(i=0;i<length;i++)
			{
				for(i=0;i<(difference.toString().length-product.toString().length);i++)
				{
					if(i==0)
					objectArray[objectArray.length]=new textbox("disable",10,"t_"+((level*20)+21+dividentlevel-length+i),true,'farword',0,levelsflow.length,true,false,false);
					else
					objectArray[objectArray.length]=new textbox("disable",10,"t_"+((level*20)+21+dividentlevel-length+i),true,'farword',0,levelsflow.length,false,false,false);
				}
				
				for(i=0;i<product.toString().length;i++)
				{
					if(i==0)
					{
						if(i==product.toString().length-1)
							objectArray[objectArray.length]=new textbox("disable",product.toString().charAt(i) ,"t_"+(((level*20)+21+dividentlevel-length+i)+(difference.toString().length-product.toString().length)),false,'none',1,levelsflow.length,true,false,false);
						else
							objectArray[objectArray.length]=new textbox("disable",product.toString().charAt(i),"t_"+(((level*20)+21+dividentlevel-length+i)+(difference.toString().length-product.toString().length)),true,'farword',1,levelsflow.length,true,false,false);
					}
					else
					{
						if(i==product.toString().length-1)
							objectArray[objectArray.length]=new textbox("disable",product.toString().charAt(i) ,"t_"+(((level*20)+21+dividentlevel-length+i)+(difference.toString().length-product.toString().length)),false,'none',1,levelsflow.length,false,false,false);
						else
							objectArray[objectArray.length]=new textbox("disable",product.toString().charAt(i),"t_"+(((level*20)+21+dividentlevel-length+i)+(difference.toString().length-product.toString().length)),true,'farword',1,levelsflow.length,false,false,false);
					}
				}
			}

			levelsflow[levelsflow.length]=new flowSteps((level*20)+21+dividentlevel-length,(level*20)+21+dividentlevel,length,"product");
			difference=difference-product;
			level++;
			}
	}
}

//textbox class 
function textbox(status,value,id,traverse,direction,optional,levelbelong,focus,topborder,leftborder)
{
	this.status=status;
	this.value=value;
	this.id=id;
	this.traverse=traverse;
	this.direction=direction;
	this.optional=optional;
	this.levelbelong=levelbelong;
	this.focus=focus;
	this.topborder=topborder;
	this.leftborder=leftborder;
}
//flow control class
function flowSteps(startid,endid,numberofboxes,type)
{
	this.startid=startid;
	this.endid=endid;
	this.numberofboxes=numberofboxes;
	this.type=type;
} 

//resizing function
function resize()
{
           				
            if(navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)==null || first)
            	  {
            	  first=false;
				  var c11=$(document).height();
	              var c22=$(document).width();
	      
	              /*v=[c11+c22]/105;
	              v1=[c11+c22]/120;
	              v2=[c11+c22]/90;
	              v3=[c11+c22]/75;
                  v4=[c11+c22]/90;
           	 	  bordfont=[c11+c22]/290;*/
				  v=16;
	              v1=16;
	              v2=16;
	              v3=16;
                  v4=16;
           	 	  bordfont=16;
           	 	
           	 	
           	   	try{
           	     document.getElementById("numbervalidate").style.fontSize=v4+"px";
           	     }
           	    catch(e)
           	   {
           	 	
           	   }
		           document.getElementById("quotienttest").style.fontSize=v3+"px"; 
	               document.getElementById("remaintest").style.fontSize=v3+"px";
                
                    question.style.fontSize=v3+"px";
                    headpart.style.fontSize=v1+"px";  
                    bodypart.style.fontSize=v1+"px"; 
              if(navigator.userAgent.match(/ipad/i)!=null)
  		{
			$('input').css({"font-size":"12px"});
  			//$('input').css("font-size",($(document).height()+$(document).width())/112);
  			 }
  		else{
			$('input').css({"font-size":"16px"});
      	//$('input').css("font-size",($(document).height()+$(document).width())/90);
         }
  		
				    v6=[c11+c22]/120;
                 //  document.getElementById("button").style.fontSize=v6+"px"
                     var percentail=(($(document).width()/screen.width)*100);
                   if(percentail>70)
                   {
                   //	//console.log('s70');
                   	headpart.style.border='2px solid #115863';
				 headpart.style.left='35%';
				  $('#imgarrow').css('left','10%');
				   $('#imgarrow').css('top','1.2%');
                   }
                  else if(percentail>60 && (percentail<=70))
                   {
                   //	//console.log('s60');
                   	headpart.style.border='2px thin #115863';
				 headpart.style.left='35%';
				  $('#imgarrow').css('left','10%');
				   $('#imgarrow').css('top','1.4%');
                   }
                  else if(percentail>50 && (percentail<=60))
                   {
                   //	//console.log('s50');
                   	headpart.style.border='1px solid #115863';
				 headpart.style.left='35%';
				 // headpart.style.width='55%';
				  $('#imgarrow').css('left','10%');
				   $('#imgarrow').css('top','1.6%');
                   }
                    else if(percentail>40 && (percentail<=50))
                   {
                   	//console.log('s40');
                   	headpart.style.border='1px medium #115863';
				 headpart.style.left='35%';
				 // headpart.style.width='55%';
				  $('#imgarrow').css('left','10.3%');
				   $('#imgarrow').css('top','1.7%');
                   }
                   else if(percentail>30 && (percentail<=40))
                   {
                   //	////console.log('s30');
                   	headpart.style.border='1px thin #115863';
				 headpart.style.left='35%';
				 // headpart.style.width='55%';
				   $('#imgarrow').css('left','10.6%');
				   $('#imgarrow').css('top','2.2%');
                   }
                    else if(percentail>20 && (percentail<=30))
                   {
                   	////console.log('s20');
                   	headpart.style.border='1px thin #115863';
				 	headpart.style.left='34%';
				 // headpart.style.width='55%';
				  $('#imgarrow').css('left','10.6%');
				   $('#imgarrow').css('top','2.4%');
                   }
                   else{
                   	////console.log('nnn')
                   	 headpart.style.border='1px thin #115863';
				 headpart.style.left='33%';
				 $('#imgarrow').css('left','10.6%');
				  $('#imgarrow').css('top','2.6%');
                   }
                   
                  
 	sw=$('#smtable').width();
	sh=$('#smtable').height();
	
//	if(dw!=$(document).width() && dh!=$(document).height())
	//{
	
	$(".stb").css("width",(sw+sh)/20+"px");
	$(".stb").css("height",(sw+sh)/20+"px");
	if(isiPad1)
	{
		//$(".stb").css("font-size",(sw+sh)/39+"px");
		$(".stb").css("font-size","12px");
	//$(".txtstyle").css("font-size",(sw+sh)/39+"px");
	$(".txtstyle").css("font-size","12px");
	}
	else{
		$(".stb").css("font-size",(sw+sh)/28+"px");
	$(".txtstyle").css("font-size",(sw+sh)/28+"px");
	}
	// 
	  $("#dynsttext").css("font-size",(sw+sh)/35+"px");
	   $("#desheading").css("font-size",(sw+sh)/40+"px");
	    $("#desheading").css("top",0.2+"%");
	   
	   for(i=0;i<chkarr.length;i++)
	   {
	  
	   $('#t'+chkarr[i]).css("top",(($('#tabledes').height()-$('#deshedtext').height())/60)*(i+1)+"%");
  			 $('#t'+chkarr[i]).css("font-size",(($('#tabledes').width())/9)+"px");
  			}
  			
  	
      
    
  	
  		$("#stbutton").css("font-size",(sw+sh)/50+"px");
	 $("#statbutton").css("font-size",(sw+sh)/50+"px");
	
                   
                if(navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null){
	$(".stb").css("font-size",(sw+sh)/50+"px");
	$(".txtstyle").css("font-size",(sw+sh)/50+"px");
}   
                   
           	  }
}

       
//enabling the textboxes

function editableBlocks()
{
	if(levelindex<levelsflow.length)
	{
	var elements_to_show = levelsflow[levelindex].numberofboxes;
	var startid=levelsflow[levelindex].startid;
	var endid=levelsflow[levelindex].endid;
	var arraylocation=findarraylocation("t_"+startid);
	var arraylocationendid=findarraylocation("t_"+endid);
	
	if(objectArray[arraylocation].levelbelong == 2 && successUserAns==0)
	{
		if(remMode=="D")
		{
			if(levelindex==2)
			{
				$("#headpart").html("");
				$("#bodypart").html($("#askUser").html());
				$("#correctStep").show();
				$("#headpart").hide();
				$("#imgarrow").hide();
				successUserAns=1;
				return(false);		
			}
			else
			{
				//do nothing
			}
		}
		else
		{
			$("#headpart").html("");
			$("#bodypart").html($("#askUser").html());
			$("#correctStep").show();
			$("#headpart").hide();
			$("#imgarrow").hide();
			successUserAns=1;
			return(false);		
		}
	}
	else if(objectArray[arraylocation].value==0 && objectArray[arraylocationendid].value==0  && successUserAns==0 && levelsflow[levelindex].type!="bringdown" && remMode!="D")
	{
		$("#headpart").html("");
		$("#bodypart").html($("#askUser").html());
		$("#correctStep").show();
		$("#headpart").hide();
		$("#imgarrow").hide();
		successUserAns=1;
		return(false);
	}
	else if(putdecimal!=1)
		successUserAns=0;

	for(i=0;i<elements_to_show;i++)
	{
		if(objectArray[arraylocation+i].topborder && objectArray[arraylocation+i].leftborder)
		{
			$("#td"+(startid+i)).css({'border-color': '#000000 #000000 #000000 -moz-use-text-color',
			'border-left': '3px solid',
			'border-top': '3px solid #000000'});
		}
		else if(objectArray[arraylocation+i].topborder)
		{
			if(levelindex==0)
			{
				$("#td"+(startid+i)).css({'border-top':'solid','border-top-width': '3','border-color':'#000000'});
			}
			else
			{
				if ($.browser.msie)
				{
					$("#td"+(startid+i)).css({'border-top':'solid','border-top-width': 1.5,'border-color':'#000000'});
				}
				else
				{
					$("#td"+(startid+i)).css({'border-top':'solid','border-top-width': '2','border-color':'#000000'});
				}
			}
		}
		if($("#"+objectArray[arraylocation+i].id).attr('readonly')=="readonly")
			$("#"+objectArray[arraylocation+i].id).removeAttr('readonly');
		$("#"+objectArray[arraylocation+i].id).css('color','black');
		$("#"+objectArray[arraylocation+i].id).css({'opacity':'1','visibility':'visible','background-color':' #FFFFFF' });
		$("#td"+(startid+i)).css('background-color',' #FFFFFF');
		objectArray[arraylocation+i].status="enable";
	}
	
	for(i=0;i<elements_to_show;i++)
	{
		if(objectArray[arraylocation+i].focus)
		{
			$("#"+objectArray[arraylocation+i].id).focus();
		}
		if(levelindex==2)
		{
			objectArray[arraylocation+i].focus=true;	
		}
	}
	}
	else
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName("quotient")[1].getElementsByTagName('insruction')[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
		$("#ansBox").attr("disabled",false);
		$("#ansBox").attr("readonly",false);
		$("#ansBox").focus();
		$("#ansBox").val("");
		/*if($.browser.msie)*/
		$("#handImg").show();
		presentMode=25;
	}
}
			


//finding position of textbox object in objectArray
function findarraylocation(id)
{
	var exsists= false;
	for( k=0;k<objectArray.length;k++)
	{
		if(objectArray[k].id==id)
		{
			exsists=true;
		 break;
		}
	}
	if(exsists)
	{
		return k;
	}
	else
	{
		return 0;
	}
}
//input box value keydown timer and focus change
function keyDownHandler(e)
{
	if(enableinputsboxs)
	{
		if(e.which!=8 && e.which!=13)
		{
			clearTimeout(timeinterval);
			index=findarraylocation(e.currentTarget.id);
			if(objectArray[index].traverse)
			{
				if(objectArray[index].direction=="farword")
				{
					$("#"+(objectArray[index+1].id)).focus();
				}
				else
				{
					$("#"+(objectArray[index-1].id)).focus();
				}
			}
			timeinterval=setTimeout(automaticChk,4000);
		}
		else if(e.which==13)
		{
			if(document.getElementById(e.currentTarget.id).value=="")
			{
			}
			else
			{
				automaticChk();
			}
		}
	}
}

function automaticChk()
{
	var levelallow=true;
	zeroenter=false;
	var zerocheck=false;
	clearTimeout(timeinterval);
	var answer=false;
	for(i=0;i<objectArray.length;i++)
	{
		if(objectArray[i].status=="enable")
		{
			if(objectArray[i].value==10)
			{
				if(document.getElementById(objectArray[i].id).value==0 || document.getElementById(objectArray[i].id).value=="")
				{
					answer=true;
					if(document.getElementById(objectArray[i].id).value==0 && document.getElementById(objectArray[i].id).value!="")
						zerocheck=true;
				}
				else 
				{
					answer=false;
					break;
				}
			}
			else if(levelindex==2)
			{
				if(levelallow)
				{
					levelallow=false;
					if(document.getElementById(objectArray[i].id).value!=objectArray[i].value)
					{
						answer=false;
						break;
					}
					else
					{
						answer=true;
						if(NoAfterDecimalChk!=0)
							quitEnterd++;
						if(zerocheck)
						{
							zeroenter=true;
						}
					}
				}
				else
				{
					if(document.getElementById(objectArray[i].id).value!="")
					{
						answer=false;
						break;
					}
					else
					{
						curPos=i;
						answer=true;
					}	
				}
			}
			else if(objectArray[i].value==document.getElementById(objectArray[i].id).value)
			{  
				if(document.getElementById(objectArray[i].id).value=="")
				{
					hold=true;
					headpart.innerHTML="Please enter the next digit of the quotient and press enter!";
					answer=false;
					break;
				}
				else
				{
					curPos=i;
					hold=false;
					answer=true;
				}
			}
			else
			{
				hold=false;
				answer=false;
				break;
			}
		}
	}
	//console.log("auto check"+answer+"---"+" NoAfterDecimalChk "+NoAfterDecimalChk+" decimalDivide "+decimalDivide+" -- "+objectArray[curPos].levelbelong+" -- curPos "+curPos+" digitBeforDecim "+digitBeforDecim+" wholeNoQues "+wholeNoQues+" quitEnterd "+quitEnterd+" wholeNoQuit "+wholeNoQuit);
	
	if(answer)
	{
		if(NoAfterDecimalChk==0 && decimalDivide=="yes")
		{
			if(objectArray[curPos].levelbelong == 2)
			{
				if(digitBeforDecim == wholeNoQues)
					putdecimal=1;
				else
					wholeNoQues++;
			}
		}
		else
		{
			if(quitEnterd == wholeNoQuit)
				putdecimal=1;
		}
		correct();
		uneditableBlocks();
		levelindex++;
		editableBlocks();
		numberofattempts=0;
	}
	else
	{
		if(!hold)
		{
			for(i=0;i<objectArray.length;i++)
			{
				if(objectArray[i].status=="enable" && document.getElementById(objectArray[i].id).value!="")
				{
				if(document.getElementById(objectArray[i].id).value!=0)
					$("#"+objectArray[i].id).css('color','red');
				}
			}
			numberofattempts++;
			description();
		}
	}
}	
	
//closing the opened textboxes
function uneditableBlocks()
{
	var elements_to_show = levelsflow[levelindex].numberofboxes;
	var startid=levelsflow[levelindex].startid;
	var arraylocation=findarraylocation("t_"+startid);
	for(i=0;i<elements_to_show;i++)
	{
		$("#"+objectArray[arraylocation+i].id).css('color','black');
		$("#"+objectArray[arraylocation+i].id).attr('readonly','readonly');
		$("#td"+(startid+i)).css('background-color','#87cefa');
		$("#"+objectArray[arraylocation+i].id).css('background-color','#87cefa');
		objectArray[arraylocation+i].status='disable';
		$("#"+objectArray[arraylocation+i].id).blur();
	}
	if(levelsflow[levelindex].type=="bringdown")
	{
		$('#downarrow').css('z-index','-1');
		document.getElementById("downarrow").style.display="none";
	}
}

function description()
{
	if(NoAfterDecimalChk==0 && decimalDivide=="yes")
	{
		if(objectArray[curPos].levelbelong == 1)
		{
			if(digitBeforDecim == wholeNoQues)
				putdecimal=1;
			else
				wholeNoQues++;
		}
	}
	else
	{
		if(quitEnterd == wholeNoQuit)
			putdecimal=1;
	}
	
	document.activeElement.blur();

	switch(levelsflow[levelindex].type)
	{
		case "dividend"   :
		            dividenddescription();
		                 break;
		case "divisor"    :
		           divisordescription();
		                 break;
		case "quotientall":
		           quotientalldescription();
		                 break;
		case "quotient"   :
		           quotientdescription();
		                  break;
		case  "product"   :
		           productdescription();
		                  break;
		case  "difference":
		            differencedescription();
		                  break;
		case  "bringdown" :
		            bringdowndescription();
		                  break;
		case  "carryZero" :
		                  break;
	}
}
function dividenddescription()
{
	headpart.innerHTML=xmlDocNew.getElementsByTagName("dividend")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
 if(xmlDocNew.getElementsByTagName("dividend")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span").length==0)
 {
 	bodypart.innerHTML="";
 }else
 {
 var bodyelements=xmlDocNew.getElementsByTagName("dividend")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements);
     
  }
  if(xmlDocNew.getElementsByTagName("dividend")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("button").length==0)
   {
     	instructionallowed=false;
     	procedue();
   }   
}
function divisordescription()
{
	headpart.innerHTML=xmlDocNew.getElementsByTagName("divisor")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
 if(xmlDocNew.getElementsByTagName("divisor")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span").length==0)
 {
 	bodypart.innerHTML="";
 }else
 {
 var bodyelements=xmlDocNew.getElementsByTagName("divisor")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements);
     
  }
  if(xmlDocNew.getElementsByTagName("divisor")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("button").length==0)
   {
     	instructionallowed=false;
     	procedue();
   }   
}
function quotientdescription()
{

	var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
		if(document.getElementById(objectArray[arraylocation].id).value=="")
		{
			inputvalue=0;
		}else
		{
	inputvalue=parseInt(document.getElementById(objectArray[arraylocation].id).value);
	}
	if(quotintallattempts==0)
	{
  	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("incorrect1")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	//alert(getRemainder() + " -- "+divisorNew+" -- "+inputvalue);
	if( ((getRemainder()/divisorNew)<inputvalue))
	{
     var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("incorrect1")[0].getElementsByTagName("body")[0].getElementsByTagName("smaller")[0].getElementsByTagName("span");
	 }
	 if( ((getRemainder()/divisorNew)>inputvalue))
	{
     var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("incorrect1")[0].getElementsByTagName("body")[0].getElementsByTagName("greater")[0].getElementsByTagName("span");
	 }
	
     bodytext1="";
     bodytextfun(bodyelements);
    }
    else
    {
    	quotintallattempts=0;
    	explain();
    }
}
function productdescription()
{
	headpart.innerHTML=xmlDocNew.getElementsByTagName("product")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
 if(xmlDocNew.getElementsByTagName("product")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span").length==0)
 {
 	bodypart.innerHTML="";
 }else
 {
 var bodyelements=xmlDocNew.getElementsByTagName("product")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements);
     
  }
  if(xmlDocNew.getElementsByTagName("product")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("button").length==0)
   {
     	instructionallowed=false;
     	procedue();
   }   
}
function differencedescription()
{
	headpart.innerHTML=xmlDocNew.getElementsByTagName("difference")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
 if(xmlDocNew.getElementsByTagName("difference")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span").length==0)
 {
 	bodypart.innerHTML="";
 }else
 {
 var bodyelements=xmlDocNew.getElementsByTagName("difference")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements);
     
  }
  if(xmlDocNew.getElementsByTagName("difference")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("button").length==0)
   {
     	instructionallowed=false;
     	procedue();
   }   
}
function  bringdowndescription()
{
	headpart.innerHTML=xmlDocNew.getElementsByTagName("bringdown")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
 if(xmlDocNew.getElementsByTagName("bringdown")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span").length==0)
 {
 	
	bodypart.innerHTML="";
 }else
 {
 var bodyelements=xmlDocNew.getElementsByTagName("bringdown")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("body")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements);
     
  }
  if(xmlDocNew.getElementsByTagName("bringdown")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("button").length==0)
   {
     	instructionallowed=false;
     	procedue();
   }   
}

function quotientalldescription()
{
	var numberofanswers=0;
	var filledinput=0;
    numberofextrainputs=0;
	var mutiplicationextension=true;
	block();
			var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
	for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
		if(document.getElementById(objectArray[arraylocation+i].id).value!="")
		{
			numberofanswers++;
		}
	}
	for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
		if(document.getElementById(objectArray[arraylocation+i].id).value!="" && document.getElementById(objectArray[arraylocation+i].id).value==0)
		{
			if(objectArray[arraylocation+i].value!=10)
			{
				break;
			}else
			{
				filledinput++;
			}
		}
	}
	for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
			if(objectArray[arraylocation+i].value!=10)
			{
				break;
			}else
			{
				numberofextrainputs++;
			}
	}
	for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
		   if(i==numberofextrainputs)
		   continue;
		   
			if(document.getElementById(objectArray[arraylocation+i].id).value!="")
			{
				mutiplicationextension=false;
				break;
			}
	}
	if(mutiplicationextension)
	{
		if(document.getElementById(objectArray[arraylocation+numberofextrainputs].id).value=="")
		mutiplicationextension=false;	
		
	}

	if(mutiplicationextension)
	{
		inputvalue=parseInt(document.getElementById(objectArray[arraylocation+numberofextrainputs].id).value);
  	block();
		if(quotintallattempts==0)
		{
  	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("incorrect1")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	
	
	if( ((getRemainder()/divisorNew)<inputvalue))
	{
     var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("incorrect1")[0].getElementsByTagName("body")[0].getElementsByTagName("smaller")[0].getElementsByTagName("span");
	 }
	 if( ((getRemainder()/divisorNew)>inputvalue))
	{
     var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("incorrect1")[0].getElementsByTagName("body")[0].getElementsByTagName("greater")[0].getElementsByTagName("span");
	 }
     bodytext1="";
     bodytextfun(bodyelements);
    }else
    {
    	quotintallattempts=0;
    	explain()
    }
	}
	else if(numberofanswers>1)
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("twoinputs")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue
	    bodypart.innerHTML="";
	    enableinputsboxs=true;
	    for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
		document.getElementById(objectArray[arraylocation+i].id).value="";
		$("#"+objectArray[arraylocation+i].id).removeAttr('readonly');
	}
  }else if(filledinput>0)
  {
  		document.getElementById("imgarrow").style.display="none";
  		document.getElementById("headpart").style.display="none";
  		headpart.innerHTML="";
     var bodyelements=xmlDocNew.getElementsByTagName("firstquotientdigitzero")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements)
  }
  else if(attempts==0)
  {
  	attempts++;
  	headpart.innerHTML=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("firstattempt")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue
     var bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("firstattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("firstpart")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements)
  }else if(attempts==1)
  {
  secondAttempt();
  }
  
}
function secondAttempt()
{	startidd=divisorlength+1;
	                   $('#secondlin').css({'display':'block', 'z-index':'7'});
	                 var offset=$('#secondlin').position();
					var widtharrowz=$('#arrowz').outerWidth(true);
					var width=$("#td"+(startidd)).outerWidth();
					if(divisorlength!=1)
					var firstlinwidth=(width)*(divisorlength);
					var secondlinleft=offset.left+(width*(divisorlength-1));
					  $('#firstlin').css({'display':'block','width': firstlinwidth});
                       $('#secondlin').css('left', secondlinleft);
                      
		              startiddvar= $("#t_"+(startidd)).val();
		              
	headpart.innerHTML=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue
     var bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("firstpart")[0].getElementsByTagName("span");
     bodytext1="";
     bodytextfun(bodyelements);
     if(startiddvar<divisorNew)
     {
     	 $("#t_"+(startidd+10)).css('color','red');
		                $("#t_"+(startidd+10)).val('X');
       bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("firstpart1")[0].getElementsByTagName("span");
       bodytextfun(bodyelements);  
       }else
       {
       	 $('#arrowz').css({'left':widtharrowz+(startidd)*width,'display':'block'});
       $('#arrowz').fadeOut("50").fadeIn("50");
         $("#t_"+(startidd+10)).css('background-color','#FAEBD7');
          $("#td"+(startidd+10)).css('background-color','#FAEBD7');
       bodytext1=bodytext1.split("<button")[0];
       if(startiddvar==divisorNew)
  		{
  			bodytext1=bodytext1.split("<button")[0];
  	  bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("aftercorrectequal")[0].getElementsByTagName("span");
  		}else{
  	  bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("aftercorrect")[0].getElementsByTagName("span");
       bodytextfun(bodyelements); 
      }
       }
       startidd++;       
    }
  function  secondAttemptcontinue()
  {
  	var stringval="";
  	var width=$("#td"+(startidd)).outerWidth();
  var widthsecond=$('#secondlin').outerWidth(true);
  var widtharrowz=$('#arrowz').outerWidth(true);
  	 $('#secondlin').css('width',widthsecond+width);
  	for(var i=divisorlength+1;i<=startidd;i++)
  	{
  		stringval+=$("#t_"+(i)).val().toString();
  	}
  	startiddvar=parseInt(stringval);
  	if(startiddvar<divisorNew)
  	{
  		bodytext1=bodytext1.split("<button")[0];
  	  bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("firstpart1")[0].getElementsByTagName("span");
       bodytextfun(bodyelements); 
       $("#t_"+(startidd+10)).css('color','red');
       $("#t_"+(startidd+10)).val('X');
  	}else
  	{
  		if(startiddvar==divisorNew)
  		{
  			bodytext1=bodytext1.split("<button")[0];
  	  bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("aftercorrectequal")[0].getElementsByTagName("span");
  		}else
  		{
  		bodytext1=bodytext1.split("<button")[0];
  	  bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("aftercorrect")[0].getElementsByTagName("span");
     }
       bodytextfun(bodyelements); 
       $('#arrowz').css({'left':widtharrowz+(startidd)*width,'display':'block'});
       $('#arrowz').fadeOut("50").fadeIn("50");
         $("#t_"+(startidd+10)).css('background-color','#FAEBD7');
          $("#td"+(startidd+10)).css('background-color','#FAEBD7');
  	}
  	 startidd++;
	 //setting scroll position to down 
var psconsole = $('#bodypart');
    bottom = $("#bodypart")[0].scrollHeight - $("#bodypart").height();
	 psconsole.scrollTop(bottom);

  }
 function secondproced()
 {
 	var firstvaluez=0;
 	var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
	for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
 	  if(objectArray[arraylocation+i].value==10)
 	  {
			$("#"+objectArray[arraylocation+i].id).val("");
		$("#"+objectArray[arraylocation+i].id).attr('readonly','readonly');
				$("#td"+(startid+i)).css('background-color','#89d0fe');
				$("#"+objectArray[arraylocation+i].id).css('background-color','#89d0fe');
				objectArray[arraylocation+i].status='disable';
				$("#"+objectArray[arraylocation+i].id).blur();
			
		}
		else
		{
			if(firstvaluez!=0)
			{
				$("#"+objectArray[arraylocation+i].id).val("");
		       $("#"+objectArray[arraylocation+i].id).attr('readonly','readonly');
				$("#td"+(startid+i)).css('background-color','#89d0fe');
				$("#"+objectArray[arraylocation+i].id).css('background-color','#89d0fe');
				objectArray[arraylocation+i].status='disable';
				$("#"+objectArray[arraylocation+i].id).blur();
				
			}
			else
			{
				$("#"+objectArray[arraylocation+i].id).removeAttr('readonly');
				$("#"+objectArray[arraylocation+i].id).css('background-color','#FFFFFF');
				$("#td"+(startid+i)).css('background-color','#FFFFFF');
				$("#"+objectArray[arraylocation+i].id).css('color','#000000');
			}
			firstvaluez++;
		}
	}
	$('#arrowz').css('display','none');
	$('#firstlin').css('display','none');
    $('#secondlin').css('display','none');
    bodytext1="";
   bodypart.innerHTML="";
 	headpart.innerHTML=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("secondattempt")[0].getElementsByTagName("direction")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
 enableinputsboxs=true;
 }
		

function buttonYes()
{
	 var bodyelements=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("firstattempt")[0].getElementsByTagName("body")[0].getElementsByTagName("nexttext")[0].getElementsByTagName("span");
   bodytext1="";
   bodytextfun(bodyelements);
}
function buttonNo(num)
{
	if(num==2)
	{
	quotintallattempts++;
	}
	headpart.innerHTML=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("firstattempt")[0].getElementsByTagName("direction")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
     bodypart.innerHTML="";
     enableinputsboxs=true;
     	var elements_to_show = levelsflow[levelindex].numberofboxes;
		var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
      for(i=0;i<elements_to_show;i++)
	{
		if(objectArray[arraylocation+i].status=="enable")
		$("#"+objectArray[arraylocation+i].id).removeAttr('readonly');
	}
	
}
function firstquotientdigitzero()
{
	document.getElementById("imgarrow").style.display="block";
	document.getElementById("headpart").style.display="block";
	headpart.innerHTML=xmlDocNew.getElementsByTagName("quotientall")[0].getElementsByTagName("firstattempt")[0].getElementsByTagName("direction")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
		bodypart.innerHTML="";
		enableinputsboxs=true;
			var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
	 for(i=0;i<levelsflow[levelindex].numberofboxes;i++)
	{
		if(objectArray[arraylocation+i].value!=10)
		{
		$("#"+objectArray[arraylocation+i].id).removeAttr('readonly');
		}else
		{
		$("#"+objectArray[arraylocation+i].id).val("");
		$("#"+objectArray[arraylocation+i].id).attr('readonly','readonly');
				$("#td"+(startid+i)).css('background-color','#89d0fe');
				$("#"+objectArray[arraylocation+i].id).css('background-color','#89d0fe');
				objectArray[arraylocation+i].status='disable';
				
				$("#"+objectArray[arraylocation+i].id).blur();
			}
	}

}
function correct()
{
	if((levelindex+1)==levelsflow.length)
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName(levelsflow[levelindex].type)[0].getElementsByTagName("correct")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	}
	else if(levelindex==2)
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName(levelsflow[levelindex+1].type)[0].getElementsByTagName("precorrect1")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	}
	else if(levelsflow[levelindex+1].type != "carryZero")
	{
		if(levelsflow[levelindex].type == "quotient" && NoAfterDecimalChk!=0)
			quitEnterd++;
		headpart.innerHTML=xmlDocNew.getElementsByTagName(levelsflow[levelindex+1].type)[0].getElementsByTagName("precorrect")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	}
	else if(levelsflow[levelindex+1].type == "carryZero")
	{
		$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);
	}
	/*else 
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName(levelsflow[levelindex+1].type)[0].getElementsByTagName("precorrect")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	}*/
	if(levelsflow[levelindex].type== "quotientall" && zeroenter)
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName(levelsflow[levelindex].type)[0].getElementsByTagName("correct0")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	}
	bodypart.innerHTML="";
}
	

function block()
{
	enableinputsboxs=false;
	var elements_to_show = levelsflow[levelindex].numberofboxes;
			var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
		for(var i=0;i<elements_to_show;i++)
				{
					if(levelsflow[levelindex].type=="bringdown" && numberofattempts==2)
				{
					var offset=$("#td"+(startid+i)).offset();
					var left=offset.left;
					var height=$("#td"+(startid+i)).outerHeight();
					var width=$("#td"+(startid+i)).outerWidth();
					var stringstartid=startid.toString();
					var heightpointer=((parseInt(stringstartid.substr(0,stringstartid.length-1))-1))*height;
					var leftpointer=left+(width/3);
					 $('#downarrow').css({'height':heightpointer ,'left': leftpointer ,'z-index':'5'});
					document.getElementById("downarrow").style.display="block";
					$("#"+objectArray[arraylocation+i].id).blur();
					if(document.getElementById(objectArray[arraylocation+i].id).value=="")
					
					{
						$("#"+objectArray[arraylocation+i].id).css('color','black');
					}
				}
					$("#"+objectArray[arraylocation+i].id).attr('readonly','readonly');
					
				}
				
}


function procedue()
{
	enableinputsboxs=true;
	bodypart.innerHTML="";
	var elements_to_show = levelsflow[levelindex].numberofboxes;
			var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
		
	if(numberofattempts==3 ||(levelsflow[levelindex].type=="product" && numberofattempts==2))
	{
			for(i=0;i<elements_to_show;i++)
			{
				if(objectArray[arraylocation+i].value==10)
				{
					$("#"+objectArray[arraylocation+i].id).val("");
				}else
				{
					$("#"+objectArray[arraylocation+i].id).css('color','black');
				$("#"+objectArray[arraylocation+i].id).val(objectArray[arraylocation+i].value);
				}
			}
	
			correct();
		uneditableBlocks()
		levelindex++;
		editableBlocks();
			numberofattempts=0;
    }
	else{
		if(instructionallowed)
		{
		headpart.innerHTML=xmlDocNew.getElementsByTagName(levelsflow[levelindex].type)[0].getElementsByTagName("instruction")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
        }
      instructionallowed=true;
			for(i=0;i<elements_to_show;i++)
			{
				$("#"+objectArray[arraylocation+i].id).removeAttr('readonly');
			    if(objectArray[arraylocation+i].focus || (levelindex==0 && i==0))
				{
					$("#"+objectArray[arraylocation+i].id).focus();
				}
			}
			}
}


function bodytextfun(bodyelements)
{
	for(i=0;i<bodyelements.length;i++)
	{
	if(bodyelements[i].getElementsByTagName("table").length!=0)
	{
						  var number2=0;
						  if(levelindex==3)
						  {
						   number2=parseInt(quotient.toString().charAt(0));
						  }
						   else
						  {
						   number2=quotientValueFinding();
						  }
						  
		smalltable(divisorNew,number2,1);
		break;
	}
	else if(i==bodyelements.length-1 && bodyelements[i].getElementsByTagName("button").length!=0)
	{
		var noofbuttons=bodyelements[i].getElementsByTagName("button");
		for(j=0;j<noofbuttons.length;j++)
		{
			bodytext1+="<button type='button' id='button' onclick='"+noofbuttons[j].getAttribute("onclick")+"'>"+ noofbuttons[j].childNodes[0].nodeValue+"</button>";
		}
	}
	else if(bodyelements[i].getElementsByTagName("input").length!=0)
	{
		bodytext1+=" <input type='tel' class='numbervalidate' id='numbervalidate' maxlength='1'/> ";
	}
	else if(bodyelements[i].getAttribute("variable")=="true")
	{ 
		var number1=0;
		switch(bodyelements[i].childNodes[0].nodeValue.trim())
		{
			case "dividendvar":
							 number1=dividendOld;
							   break;
			case "divisorvar":
							  number1=divisorNew;
							   break;
			case "quotientvar":
							   if(levelindex==3)
							   number1=parseInt(quotient.toString().charAt(0));
							   else
							   number1=quotientValueFinding();
							   break;
			case "productvar":
								
								if(levelindex==3 || levelindex==4 )
							   number1=divisorNew*parseInt(quotient.toString().charAt(0));
							   else
							   number1=divisorNew*quotientValueFinding();
							   
								break;
			case "differencevar":
								   var elements_to_show1 = levelsflow[levelindex].numberofboxes;
									var startid1=levelsflow[levelindex].startid;
								   var arraylocation1=findarraylocation("t_"+(startid1-20));
									if(levelindex==4)
									{
										 arraylocation1=findarraylocation("t_"+(startid1-30));
									}
									startid1="";
									for(var z=0;z<elements_to_show1;z++)
									{
										startid1+=objectArray[arraylocation1+z].value.toString();
									}
								   number1=startid1;
								   
								   break;
			case "dividendpartvar":
									
									number1=getRemainder();
									if(number1.toString().length==3)
										number1	=	number1 / 10;
									divisionpart=parseInt(number1);									
							   break;
			case "differencevar1" :
									   
									var elements_to_show1 = levelsflow[levelindex].numberofboxes;
									var startid1=levelsflow[levelindex].startid;
								   var arraylocation1=findarraylocation("t_"+(startid1-20));
									if(levelindex==4)
									{
										 arraylocation1=findarraylocation("t_"+(startid1-30));
									}
									startid1="";
									for(var z=0;z<elements_to_show1;z++)
									{
										startid1+=objectArray[arraylocation1+z].value.toString();
									}
								 if(levelindex==3 || levelindex==4 )
							   number1=parseInt(startid1)-divisorNew*parseInt(quotient.toString().charAt(0));
							   else
							   number1=parseInt(startid1)-divisorNew*quotientValueFinding();
							   
							   tempDifference = number1;							
							   break;
			  case "inputvalue" :
								  number1=inputvalue;
								  break;
			  case "rtproduct"  :
								  number1=divisorNew*inputvalue;
								  break;
			  case "inputvalue1" :
									 number1=inputvalue+1;
									 break;
			  case "inputvalue2" :
									  number1=inputvalue-1;
									 break;
			  case "rtproduct1"  :
									 number1=divisorNew*(inputvalue+1);
									 break;
			  case "rtproduct2"  :
									  number1=divisorNew*(inputvalue-1);
									 break;
									 
			  case "partofdividend" :  number1=startiddvar;
		}
	   
		if(bodyelements[i].getAttribute("fontColor")=="red" && bodyelements[i].getAttribute("underline")=="true")
		{
			bodytext1+="<span STYLE=' color: rgb(100%, 0%, 0%);text-decoration:underline'>" + number1 + "</span>";
		}
		else if(bodyelements[i].getAttribute("fontColor")=="red")
		{
			bodytext1+="<span STYLE=' color: rgb(100%, 0%, 0%)'>" + number1 + "</span>";
		}
		else if(bodyelements[i].getAttribute("fontColor")=="redit")
		{
			bodytext1+="<span STYLE=' color: rgb(100%, 0%, 0%);font-style:italic'>" + number1 + "</span>";
		}
		else if(bodyelements[i].getAttribute("fontColor")=="blackit")
		{
			bodytext1+="<span STYLE='font-style:italic'>" + number1 + "</span>";
		}
		else
		{
			bodytext1+=number1;
		}
	}
		else if(bodyelements[i].getAttribute("fontColor")=="red" && bodyelements[i].getAttribute("underline")=="true")
		{
			bodytext1+="<span STYLE=' color: rgb(100%, 0%, 0%);text-decoration:underline'>" + bodyelements[i].childNodes[0].nodeValue + "</span>";
		}
		else if(bodyelements[i].getAttribute("fontColor")=="red")
		{
			bodytext1+="<span STYLE=' color: rgb(100%, 0%, 0%)'>" + bodyelements[i].childNodes[0].nodeValue + "</span>";
		}
		else if(bodyelements[i].getAttribute("fontColor")=="redit")
		{
			bodytext1+="<span STYLE=' color: rgb(100%, 0%, 0%);font-style:italic'>" + bodyelements[i].childNodes[0].nodeValue + "</span>";
		}
		else if(bodyelements[i].getAttribute("fontColor")=="blackit")
		{
			bodytext1+="<span STYLE='font-style:italic'>" + bodyelements[i].childNodes[0].nodeValue + "</span>";
		}
		else
		{    
			bodytext1+=bodyelements[i].childNodes[0].nodeValue;
		}
		if(bodyelements[i].getAttribute("linebreak")=="true")
		{
			bodytext1+="<br/><br/>";
		}
	}
	if(bodytext1.indexOf('#') == -1)
	{
	
	}
	else
	{
		var n=bodytext1.replace("#","<br/>");
		bodytext1=n;
	}
	bodypart.innerHTML=bodytext1; 
	try{
			document.getElementById("numbervalidate").style.fontSize=v4+"px";
	}
	catch(e)
	{
	
	}
	if((inputvalue*divisorNew)==divisionpart)
		bodytext1	=	bodytext1.replace(/is smaller than/g, 'is same as');
	bodypart.innerHTML=bodytext1;
	if(bodyelements[(bodyelements.length)-1].getElementsByTagName("button").length!=0)
	{
		block();
	}
}

function getRemainder()
{
	if(levelindex==2)
	{
		for(z=levelindex;z<levelsflow.length;z++)
		{
			if(levelsflow[z].type=="difference")
				break;
		}
		var elements_to_show1 = levelsflow[z].numberofboxes;
		var startid1=levelsflow[z].startid1;
		
		var arraylocation1=findarraylocation("t_"+(startid1-30));
		
		startid1="";
		for(var z=0;z<elements_to_show1;z++)
		{
			startid1+=objectArray[arraylocation1+z].value.toString();
		}
		return startid1;
	}
	else
	{
		for(z=levelindex;;z--)
		{
			if(levelsflow[z].type=="difference")
				break;
		}    
		var startid1="";
		for(var p=z;;p++)
		{
			if(p==levelindex)
			{
				break;
			}
			else
			{
				var elements_to_show1 = levelsflow[p].numberofboxes;
				var startid=levelsflow[p].startid;
				var arraylocation1=findarraylocation("t_"+(startid));
				
				for(var z=0;z<elements_to_show1;z++)
				{
					//console.log(elements_to_show1);
					if(objectArray[arraylocation1+z].value!=10 && levelsflow[p].type!="quotient")
						startid1+=objectArray[arraylocation1+z].value.toString();
				}
			}
		}
		return (startid1/10);
	}
}
function quotientValueFinding()
{
		var quot=0;
		                    
		                    for(z=levelindex;z>=0;z--)
		                  {
		                  	if(levelsflow[z].type=="quotient")
		                  	{
		                  		var startid=levelsflow[z].startid;
		                  		var arraylocation=findarraylocation("t_"+startid);
		                  		quot=objectArray[arraylocation].value
		                  		break;
		                  	}
		                  }
		                  return quot;
}
function explain()
{
	if(levelindex!=2)
	{
	
  	block();
  	var numberexits=false;
	
		for(i=0;i<chkarr.length;i++)
		{
		if(inputvalue==chkarr[i])
		{
			numberexits=true;
			break;
		}
		}
		if(numberexits)
		{
			              for(z=levelindex;;z--)
		                     {
		                  	if(levelsflow[z].type=="difference")
		                  		break;
		                     }    
		                      var startid1="";
		                       	for(var p=z;;p++)
		                       	{
		                       		if(p==levelindex)
		                       		{
		                       			break;
		                       		}else
		                       		{
		                       		 var elements_to_show1 = levelsflow[p].numberofboxes;
			                    var startid=levelsflow[p].startid;
			                   var arraylocation1=findarraylocation("t_"+(startid));
			                    
		                        for(var z=0;z<elements_to_show1;z++)
		                        {
		                        	if(objectArray[arraylocation1+z].value!=10)
		                        	startid1+=objectArray[arraylocation1+z].value.toString();
		                       	}
		                       }
		                       }
		                       bodytext1="";
		               // divisionpart=parseInt(startid1);
		                var bodyelements=xmlDocNew.getElementsByTagName("extratextforlevelsix")[0].getElementsByTagName("body")[0].getElementsByTagName("span");     
		               bodytextfun(bodyelements);
		       if(Math.floor(divisionpart/divisorNew)>inputvalue)
		       {
			  
		 var bodyelements=xmlDocNew.getElementsByTagName("value")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytextfun(bodyelements);
		       }
			   else
		       {
			   
		 var bodyelements=xmlDocNew.getElementsByTagName("value")[1].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytextfun(bodyelements);
		       }
		}
		else
		{
	bodypart.innerHTML="";
	bodytext1="";
	var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	 bodytextfun(bodyelements);
	smalltable(divisorNew,inputvalue,3);
	}
	}else
	{
		bodypart.innerHTML="";
		bodytext1="";
	var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	
	 bodytextfun(bodyelements);
	 
	smalltable(divisorNew,inputvalue,3);
	}
}
function explainextention()
{
	
	greaterelement=Math.floor(divisionpart/divisorNew)+1;
	
	 lowerelement=Math.floor(divisionpart/divisorNew);
	
	if(divisionpart>=(divisorNew*inputvalue))
	{ 
		var greaterexits=false;
		for(i=0;i<chkarr.length;i++)
		{
		if(greaterelement==chkarr[i])
		{
			greaterexits=true;
			break;
		}
		}
		if(inputvalue==(divisionpart/divisorNew))
		{
			headpart.innerHTML=xmlDocNew.getElementsByTagName("lowerwithouthigher1")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
			var bodyelements=xmlDocNew.getElementsByTagName("answers1")[0].getElementsByTagName("span");
			bodypart.innerHTML="";
			bodytext1="";
			bodytextfun(bodyelements);
		}
		else if(inputvalue==Math.floor(divisionpart/divisorNew) && inputvalue==9)
		{
			headpart.innerHTML=xmlDocNew.getElementsByTagName("lowerwithouthigher")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
			var bodyelements=xmlDocNew.getElementsByTagName("answer9")[0].getElementsByTagName("span");
			bodypart.innerHTML="";
			bodytext1="";
			bodytextfun(bodyelements);
		}
		else if(inputvalue==Math.floor(divisionpart/divisorNew)&& (!greaterexits))
		{
			headpart.innerHTML=xmlDocNew.getElementsByTagName("lowerwithouthigher")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
			var bodyelements=xmlDocNew.getElementsByTagName("lowerwithouthigher")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
			bodypart.innerHTML="";
			bodytext1="";
			bodytextfun(bodyelements);
			inputvalue++;
		}
		else if(inputvalue==Math.floor(divisionpart/divisorNew)&& (greaterexits))
		{
			headpart.innerHTML=xmlDocNew.getElementsByTagName("higherafterlower")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
			var bodyelements=xmlDocNew.getElementsByTagName("higherafterlower")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
			bodytext1="";
			bodypart.innerHTML="";
			bodytextfun(bodyelements);
			adddiv(divisorNew,Math.floor(divisionpart/divisorNew));
			adddiv(divisorNew,Math.floor(divisionpart/divisorNew)+1);
		}
		else{
		headpart.innerHTML=xmlDocNew.getElementsByTagName("lowerquotient")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	var bodyelements=xmlDocNew.getElementsByTagName("lowerquotient")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodypart.innerHTML="";
	bodytext1="";
	bodytextfun(bodyelements);
	}
	}
	else
	{
		var lowerexits=false;
		for(i=0;i<chkarr.length;i++)
		{
			if(lowerelement==chkarr[i])
			{
				lowerexits=true;
				break;
			}
		}
		if(inputvalue==(Math.floor(divisionpart/divisorNew)+1)&& lowerexits)
		{
			headpart.innerHTML=xmlDocNew.getElementsByTagName("lowerwithhigher")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
			var bodyelements=xmlDocNew.getElementsByTagName("lowerwithhigher")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
			//bodyelements.toString().replace(/ is smaller than /g, ' is same as ');
			bodytext1="";
			bodypart.innerHTML="";
			
			bodytextfun(bodyelements);
			adddiv(divisorNew,Math.floor(divisionpart/divisorNew));
			adddiv(divisorNew,Math.floor(divisionpart/divisorNew)+1);
		}
		else
		{
			headpart.innerHTML=xmlDocNew.getElementsByTagName("higherquotient")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
			var bodyelements=xmlDocNew.getElementsByTagName("higherquotient")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
			bodypart.innerHTML="";
			bodytext1="";
			bodytextfun(bodyelements);
		}
	}
}

function validatehigher()
{

	   quotivalue=$('#numbervalidate').val();
	   
	 try{
	 	
	 greaterelement=Math.floor(divisionpart/divisorNew)+1;
	
	 lowerelement=Math.floor(divisionpart/divisorNew);
	 
	 	 numb=parseInt(quotivalue);
	 	var numberexits=false;
	     var lowerexits=false;
		
		for(i=0;i<chkarr.length;i++)
		{
		if(numb==chkarr[i])
		{
			
			numberexits=true;
			
		}
		if(lowerelement==chkarr[i])
		{
			
			lowerexits=true;
		}
		}
		
		
		if((numb==lowerelement) || (lowerexits && numb==greaterelement))
			{
				
				inputvalue=numb;
				smalltable(divisorNew,numb,4);
				
				//explainextention();
				//adddiv(divisorNew,numb);
			}
	else
	{ 
	
	 if(numb>=0 && numb<=9)
	 {	
	 	
	if(numb>inputvalue)
	{ 
			if(numberexits)
		{
		
			if((numb==lowerelement) || (lowerexits && numb==greaterelement))
			{
				inputvalue=numb;
				
				explainextention();
				adddiv(divisorNew,numb);
			}else
			{
			if(levelindex==2)
			{
			 var bodyelements=xmlDocNew.getElementsByTagName("previousnumbers")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytext1="";
	bodypart.innerHTML="";
	var intialnum=inputvalue;
	inputvalue=numb;
	bodytextfun(bodyelements);
	//inputvalue=intialnum;
	adddiv(divisorNew,numb);
	}else
	{
	var intialnum=inputvalue;
	inputvalue=numb;
     bodytext1="";
	bodypart.innerHTML="";
		var bodyelements=xmlDocNew.getElementsByTagName("extratextforlevelsix")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
		bodytextfun(bodyelements);
		
		  bodyelements=xmlDocNew.getElementsByTagName("previousnumbers")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
		 bodytextfun(bodyelements);
	adddiv(divisorNew,numb);
	inputvalue=intialnum;
	}
	}
	
	}else if(!numberexits)
	{
	
		 headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue
		bodytext1="";
	bodypart.innerHTML="";
	inputvalue=numb;
	
	smalltable(divisorNew,numb,3);
	}
	}
	else
	{
	
	var bodyelements=xmlDocNew.getElementsByTagName("value")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
         bodytext1=bodytext1.split("<button")[0];
	bodypart.innerHTML="";
	bodytextfun(bodyelements);
		
	$('#numbervalidate').val(quotivalue);
	$('#numbervalidate').attr('readonly','readonly');
	}
	} 
	else
	{
		headpart.innerHTML=headpart.innerHTML=xmlDocNew.getElementsByTagName("withoutvalue")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
		}
		}	
	 }catch(e) {
	 	headpart.innerHTML=headpart.innerHTML=xmlDocNew.getElementsByTagName("withoutvalue")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	 }
}
function validatelower()
{
	  quotivalue=$('#numbervalidate').val();
	  
	 try{
	 	 greaterelement=Math.floor(divisionpart/divisorNew)+1;
	 lowerelement=Math.floor(divisionpart/divisorNew);
	 	 numb=parseInt(quotivalue);
	 	var numberexits=false;
	     var lowerexits=false;
		
		for(i=0;i<chkarr.length;i++)
		{
		
		if(numb==chkarr[i])
		{
			
			numberexits=true;
			
		}
		if(lowerelement==chkarr[i])
		{
			lowerexits=true;
		}
		}
		if((numb==lowerelement) || (lowerexits && numb==greaterelement))
			{
				
				inputvalue=numb;
				smalltable(divisorNew,numb,3);
				//explainextention();
				//adddiv(divisorNew,numb);
			}else
			{
		
	if(numb>=0&&numb<=9)
	 {	 
	if(numb<inputvalue)
	{ 
		if(numberexits)
		{
			if((numb==lowerelement) || (lowerexits && numb==greaterelement))
			{
				inputvalue=numb;
				smalltable(divisorNew,numb,3);
				explainextention();
				adddiv(divisorNew,numb);
			}else
			{
			if(levelindex==2)
			{
			 var bodyelements=xmlDocNew.getElementsByTagName("previousnumbers")[1].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytext1="";
	bodypart.innerHTML="";
	var intialnum=inputvalue;
	inputvalue=numb;
	bodytextfun(bodyelements);
	//inputvalue=intialnum;
	adddiv(divisorNew,numb);
		}else
		{
		var intialnum=inputvalue;
	inputvalue=numb;
     bodytext1="";
	bodypart.innerHTML="";
		var bodyelements=xmlDocNew.getElementsByTagName("extratextforlevelsix")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
		bodytextfun(bodyelements);
		  bodyelements=xmlDocNew.getElementsByTagName("previousnumbers")[1].getElementsByTagName("body")[0].getElementsByTagName("span");
		 bodytextfun(bodyelements);
	adddiv(divisorNew,numb);
	//inputvalue=intialnum;
		}
		}
		}else
		{
		
		bodytext1="";
	bodypart.innerHTML="";
	headpart.innerHTML=headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
		inputvalue=numb;
		
			smalltable(divisorNew,numb,3);
	}
	}
	else
	{
	//	bodypart.innerHTML="";
		//bodytext1="";
		 var bodyelements=xmlDocNew.getElementsByTagName("value")[1].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytext1=bodytext1.split("<button")[0];
	bodypart.innerHTML="";
	
	bodytextfun(bodyelements);
	$('#numbervalidate').val(quotivalue);
	$('#numbervalidate').attr('readonly','readonly');
	}
	} 
	else
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName("withoutvalue")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
		}
		}	
	 }catch(e) {
	 	headpart.innerHTML=headpart.innerHTML=xmlDocNew.getElementsByTagName("withoutvalue")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	 }
}
function append(num)
{       
	var bodyelements="";
	if(num==1)
		{
		 bodyelements=xmlDocNew.getElementsByTagName("value")[0].getElementsByTagName("extension")[0].getElementsByTagName("span");
		}else
		{
			 bodyelements=xmlDocNew.getElementsByTagName("value")[1].getElementsByTagName("extension")[0].getElementsByTagName("span");
		}
	
	if(num==1)
 			{
 				if(inputvalue==9)
 				{
 					inputvalue=8;
 					 bodyelements=xmlDocNew.getElementsByTagName("value")[1].getElementsByTagName("extension")[0].getElementsByTagName("span");
 				}
 				else
 				{
 				var tempnum=inputvalue;
 				
 				for( tempnum=tempnum+1;tempnum<=9;tempnum++)
 				{
 					var allownum=false;
 			for(i=0;i<chkarr.length;i++)
		     {
		       if(tempnum==chkarr[i])
		       {
			      allownum=true;
			     break;
		     }
		    }
		    if(!allownum)
		    {
		    	break;
		    }	
 				}
 				if(tempnum==10)
 				{
 			inputvalue=inputvalue+1;
 			}else
 			{
 				
 				inputvalue=tempnum;
 			}
 			}
 			}else
 			{
 				if(inputvalue==0)
 				{
 					inputvalue=1;
 					 bodyelements=xmlDocNew.getElementsByTagName("value")[0].getElementsByTagName("extension")[0].getElementsByTagName("span");
 				}
 				else
 				{
 				var tempnum=inputvalue;
 				
 				for( tempnum=tempnum-1;tempnum>=0;tempnum--)
 				{
 					var allownum=false;
 			for(i=0;i<chkarr.length;i++)
		     {
		       if(tempnum==chkarr[i])
		       {
			      allownum=true;
			     break;
		     }
		    }
		    if(!allownum)
		    {
		    	break;
		    }	
 				}
 				if(tempnum==-1)
 				{
 			
 			inputvalue=inputvalue-1;
 			}else
 			{
 				inputvalue=tempnum;
 			}
 			}
 			}
 			
	 
		bodytext1=bodytext1.split("<button")[0];
		
	for(i=0;i<bodyelements.length;i++)
   {
 	if( bodyelements[i].getElementsByTagName("button").length!=0)
 	{
 		var noofbuttons=bodyelements[i].getElementsByTagName("button");
 		for(j=0;j<noofbuttons.length;j++)
 		{
 			bodytext1+="<button type='button' id='button' onclick='"+noofbuttons[j].getAttribute("onclick")+"'>"+ noofbuttons[j].childNodes[0].nodeValue+"</button>";
 		}
 	}else if(bodyelements[i].getElementsByTagName("input").length!=0)
 	{
 		
 		var noofbuttons=bodyelements[i].getElementsByTagName("input");
 		for(j=0;j<noofbuttons.length;j++)
 		{
 			
 			bodytext1+= " <input type='tel' class='numbervalidate' id='numbervalidate' maxlength='1'/> ";
 		}
 	}
 	else if(bodyelements[i].getAttribute("variable")=="true")
 	{
 		if(bodyelements[i].childNodes[0].nodeValue=="product")
 		{
 			//bodytext1+=stres;
 		}else
 		{
 			bodytext1+=inputvalue;
 		}
 		
 	}else
 	{
 				bodytext1+=bodyelements[i].childNodes[0].nodeValue;
 	}
 	if(bodyelements[i].getAttribute("linebreak")=="true")
 	{
 		bodytext1+="<br/><br/>";
 	}
 }
 bodypart.innerHTML=bodytext1; 
 	/*try{
           	 document.getElementById("numbervalidate").style.fontSize=v4+"px";
           	  }
           	 catch(e)
           	 {
           	 	
           	 }*/
		$('#numbervalidate').val(quotivalue);
	$('#numbervalidate').attr('readonly','readonly');
}
function extension(num)
{  
         //  var quotttval=$('#numbervalidate').val();
          
			var numberexits1=false;
		
		for(i=0;i<chkarr.length;i++)
		{
		if(inputvalue==chkarr[i])
		{
			numberexits1=true;
			break;
			
		}
		}
	if(numberexits1)
	{
	
	if(inputvalue==Math.floor(divisionpart/divisorNew))
	{
	
	explainextention();
	}else
	{
	if(divisionpart>=(divisorNew*inputvalue))
	{
			 var bodyelements=xmlDocNew.getElementsByTagName("previousnumbers")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytext1="";
	bodypart.innerHTML="";
	bodytextfun(bodyelements);
	}else
	{
	 var bodyelements=xmlDocNew.getElementsByTagName("previousnumbers")[1].getElementsByTagName("body")[0].getElementsByTagName("span");
	bodytext1="";
	bodypart.innerHTML="";
	bodytextfun(bodyelements);
	}
	}
	}
	else{
	if(num==1)
	{
		headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	bodypart.innerHTML="";
	
	smalltable(divisorNew,inputvalue,3);
	}else
	{
	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	bodypart.innerHTML="";
		smalltable(divisorNew,Math.floor(divisionpart/divisorNew)+1,3);
	
	}
	}
}
function endFirstQuotientDigit()
{
	var elements_to_show = levelsflow[levelindex].numberofboxes;
			var startid=levelsflow[levelindex].startid;
		var arraylocation=findarraylocation("t_"+startid);
		if(levelindex==2)
		{
			for(i=0;i<elements_to_show;i++)
		{
			if(i==numberofextrainputs)
			{
				$("#"+objectArray[arraylocation+i].id).css("color","black");
			$("#"+objectArray[arraylocation+i].id).val(objectArray[arraylocation+i].value);
			}else{
				$("#"+objectArray[arraylocation+i].id).val("");
			}
		$("#"+objectArray[arraylocation+i].id).attr('readonly','readonly');
				$("#td"+(startid+i)).css('background-color','#89d0fe');
				$("#"+objectArray[arraylocation+i].id).css('background-color','#89d0fe');
				objectArray[arraylocation+i].status='disable';
				$("#"+objectArray[arraylocation+i].id).blur();
			}
	}else
	{
		   $("#"+objectArray[arraylocation].id).css("color","black");
				
				$("#"+objectArray[arraylocation].id).val(objectArray[arraylocation].value);

		     $("#"+objectArray[arraylocation].id).attr('readonly','readonly');
				$("#td"+(startid)).css('background-color','#89d0fe');
				$("#"+objectArray[arraylocation].id).css('background-color','#89d0fe');
				objectArray[arraylocation].status='disable';
				$("#"+objectArray[arraylocation].id).blur();
			}
			correct();
			levelindex++;
			enableinputsboxs=true;
		editableBlocks();
		numberofattempts=0;
}
function checkquotvalue(e) 
{
	
	if (typeof e == 'undefined' && window.event) { e = window.event; }
      if (e.keyCode == 13)
        {
           $("#quotval").blur();
        	//document.getElementById("quotval").style.border="none";
        	document.getElementById("quotval").style.borderColor="#FFFFFF";
        	
        		document.getElementById("quotimg").style.display="block";
        		
        	document.getElementById("quotans").style.display="block";
        		
if (document.getElementById("quotval").value.trim()== quotient) 
        		    	{
     document.getElementById("imgarrow").style.display="block";
	document.getElementById("headpart").style.display="block";    		    		
        		    		
        		    		
                          document.getElementById("quotimg").src="../assets/correct.png";
        		    		document.getElementById("quotval").disabled=true;
        		    		document.getElementById("quotval").style.backgroundColor="#FFFFFF";
        		    		//document.getElementById("quotval").style.border="none";
        		    	//	document.getElementById("remaintest").style.display="block";
        		    		//document.getElementById("remval").style.display="block";
        
        		    		appendquesrim(1);
        		    			numberofattempts=0;
        		    	
        		    	}
        		    	else
        		    	{
         document.getElementById("imgarrow").style.display="block";
	document.getElementById("headpart").style.display="block";   		    	    
        		    	    	//document.getElementById("quotimg").src="wrong.png"	
        		   
                          document.getElementById("quotimg").src="../assets/wrong.png";
 
        		    		document.getElementById("quotval").disabled=true;
        		    			document.getElementById("quotval").style.backgroundColor="#FFFFFF";
        		    			//document.getElementById("quotval").style.border="none";
        		    			document.getElementById("quotval").style.borderColor="#FFFFFF";
        		    		numberofattempts++;
        		    		appendquesrim(2);	
        		    	}
        		    }
      }
    
function appendquesrim(num)
{
	if(num==1)
	{
	
	headpart.innerHTML=xmlDocNew.getElementsByTagName("quotient")[1].getElementsByTagName("correct")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	 buttonbut=xmlDocNew.getElementsByTagName("quotient")[1].getElementsByTagName("correct")[0].getElementsByTagName("button")[0];
	 bodypart.innerHTML="<button  id='button' type='button' onclick='"+buttonbut.getAttribute("onclick")+"'>"+ buttonbut.childNodes[0].nodeValue+"</button>";
	
	}
	if(num==3)
	{
       headpart.innerHTML=xmlDocNew.getElementsByTagName("remainder")[0].getElementsByTagName("correct")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
       bodypart.innerHTML="<button id='button' style=' margin-left: auto;   margin-right: auto;'>"+xmlDocNew.getElementsByTagName("remainder")[0].getElementsByTagName("correct")[0].getElementsByTagName("span")[1].getElementsByTagName("button")[0].childNodes[0].nodeValue+"</button>";
	   
	}
	if(num==2)
	{
		var numberofelements=xmlDocNew.getElementsByTagName("quotient")[1].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("span");
		var content="";
		for(var i=0;i<numberofelements.length;i++)
		{
			if(numberofelements[i].getAttribute("variable")=="true")
			{
				content+=quotient;
			}else
			{
			content+=numberofelements[i].childNodes[0].nodeValue;
			}
		}
		headpart.innerHTML=content;
		buttonbut=xmlDocNew.getElementsByTagName("quotient")[1].getElementsByTagName("button")[1];
      bodypart.innerHTML="<button  id='button' type='button' onclick='"+buttonbut.getAttribute("onclick")+"'>"+ buttonbut.childNodes[0].nodeValue+"</button>";
	 
	}
	if(num==4)
	{
	    	var numberofelements=xmlDocNew.getElementsByTagName("remainder")[0].getElementsByTagName("incorrect"+numberofattempts)[0].getElementsByTagName("span");
		var content="";
		for(var i=0;i<numberofelements.length;i++)
		{
			if(numberofelements[i].getAttribute("variable")=="true")
			{
				content+= difference;
			}else
			{
			content+=numberofelements[i].childNodes[0].nodeValue;
			}
		}
		headpart.innerHTML=content;
		buttonbut=xmlDocNew.getElementsByTagName("remainder")[0].getElementsByTagName("button")[numberofattempts-1];
      bodypart.innerHTML="<button  id='button' type='button' onclick='"+buttonbut.getAttribute("onclick")+"'>"+ buttonbut.childNodes[0].nodeValue+"</button>";
	  
	}
}
function unblock(num)
{
	
	if(num==1)
	{
		 document.getElementById("imgarrow").style.display="none";
	document.getElementById("headpart").style.display="none";
	document.getElementById("quotval").disabled=false;
		
	$("#quotval").val("");
	
	bodypart.innerHTML="";	
    document.getElementById("quotans").style.display="none";
    document.getElementById("quotval").style.border="1px solid black";
     $("#quotval").focus();
	
	if(numberofattempts==3)
	{
		document.getElementById("quotans").style.display="block";
		document.getElementById("quotimg").src="../assets/systementer.png"
		document.getElementById("quotval").value=quotient;
	              // document.getElementById("quotval").style.border="none";
	               document.getElementById("quotval").style.borderColor="#FFFFFF";
        		    		document.getElementById("quotval").disabled=true;
        		    			document.getElementById("quotval").style.backgroundColor="#FFFFFF";
        		    		document.getElementById("remaintest").style.display="block";
        		    		document.getElementById("remval").style.display="block";
        		    		
        		    			numberofattempts=0;
        		  	 document.getElementById("imgarrow").style.display="block";
	document.getElementById("headpart").style.display="block";  			
    headpart.innerHTML=xmlDocNew.getElementsByTagName("quotient")[0].getElementsByTagName("correct")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
      $("#remval").focus();
      }
	}else if(num==2)
	{
		$("#remval").val("");
		
			 document.getElementById("imgarrow").style.display="none";
	document.getElementById("headpart").style.display="none";
		document.getElementById("remval").disabled=false;
		bodypart.innerHTML="";
		document.getElementById("remans").style.display="none";
		document.getElementById("remval").style.border="1px solid black";
		 $("#remval").focus();
	if(numberofattempts==3)
	{
		document.getElementById("remans").style.display="block";
		document.getElementById("remval").value=difference;
	   document.getElementById("remimg").src="../assets/systementer.png"
        		    			 document.getElementById("imgarrow").style.display="block";
        		    			 //document.getElementById("remval").style.border="none";
        		    			 document.getElementById("remval").style.borderColor="#FFFFFF";
	document.getElementById("headpart").style.display="block";
        		    		document.getElementById("remval").disabled=true;
        		    			document.getElementById("remval").style.backgroundColor="#FFFFFF";
        	 headpart.innerHTML=xmlDocNew.getElementsByTagName("remainder")[0].getElementsByTagName("correct")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;	    		
        	 bodypart.innerHTML="<button id='button' style=' margin-left: auto;   margin-right: auto;'>"+xmlDocNew.getElementsByTagName("remainder")[0].getElementsByTagName("correct")[0].getElementsByTagName("span")[1].getElementsByTagName("button")[0].childNodes[0].nodeValue+"</button>";		
	      $("#remval").blur();
	}
}else
{
	document.getElementById("remaintest").style.display="block";
        		    		document.getElementById("remval").style.display="block";
        		    		
        		    		headpart.innerHTML="";
        		    		bodypart.innerHTML="";
        		    	document.getElementById("imgarrow").style.display="none";
        		    	document.getElementById("headpart").style.display="none";
        		    	$("#remval").focus();	
}
}

st2count=0;
idgen=0;
stcount=0;

click=0;

 function smalltable(par,mul,stat)
 { 	if(mul>=10 && mul<0)
 	{
 		return;
 	}
 	$("#smtable").css({'z-index':'5','display':'block'});
dw=$(document).width();
dh=$(document).height();

if(stat==1)
{
	
}else
{
	bodypart.innerHTML="";
	bodytext1="";
	var bodyelements=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("body")[0].getElementsByTagName("span");
	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[0].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue;
	 bodytextfun(bodyelements);
}
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
	$("#dynsttext").css("font-size",(sw+sh)/35+"px");
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
else
{
	$('.txtstyle').keyup(function(e){
		stkeyup(e);
	});
	

	$('.txtstyle').keydown(function(e){
	

	if(navigator.userAgent.match(/ipad/i)!=null)
	$("#"+e.currentTarget.id).css('background','none');
		
   if(e.shiftKey){
   	return(false);
   }else
   {
   	var num=0;
   if(navigator.userAgent.match(/ipad/i)!=null || navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
		{
   	if((e.which>=48 && e.which<=57) || e.which==8 || e.which==13)
   	{
   		if(navigator.userAgent.match(/ipad/i)!=null)
		{
   			if(e.which>=48 && e.which<=57)
   				{
   				
		$("#"+e.currentTarget.id).val(parseInt(String.fromCharCode(e.which)));
   					
   				}
		}
   		num=1;
   	}
   }else
   {
   	if((e.which>=48 && e.which<=57) || e.which==8 || e.which==13 || (e.which>=96 && e.which<=105) || (e.which>=37 && e.which<=40) )
   	{
   		num=1;
   	}
   }
 	
 	switch(num){
           case 0:{   // slash
        	   if( navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
        		   $(this).blur();
                return(false);  
           }
           // and so forth
     }
   }
   });	

$('.txtstyle').focus(function (e){
if(navigator.userAgent.match(/ipad/i)!=null)
	{
			$("#"+e.currentTarget.id).css('background-color','#C0C0C0');
			$("#"+e.currentTarget.id).css('font-size','12px');
				
	}else
		{
	    
	$("#"+e.currentTarget.id).select();
	}
	
});

$('.txtstyle').bind('blur', function(e){
	if(navigator.userAgent.match(/ipad/i)!=null)
	{
		 $('html, body').animate({ scrollTop: 0 }, 0); 
		 
		str=(e.currentTarget.id).toString();
		$("#"+e.currentTarget.id).css('background','none');
		$("#"+e.currentTarget.id).css('font-size','12px');
	stpres=parseInt(str[str.length-1]);
	setTimeout(function(){
	if(stpres!=0)
	{
	stpres--;
	
	 $("#sttb"+stpres).focus();
	 window.scrollTo($("#sttb"+stpres).offset().left,$("#sttb"+stpres).offset().top );
	}
	else{
		
	
		$("#sttb1").focus();
			window.scrollTo($("#sttb1").offset().left,$("#sttb1").offset().top );
    }

	},1000);
	}
	//alert('input');
});
 $("#sttb"+(idgen-1)).focus();
if(navigator.userAgent.match(/ipad/i)!=null || navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
 {
 
  window.scrollTo($("#sttb"+(idgen-1)).offset().left,$("#sttb"+(idgen-1)).offset().top );
}
}

 }
 
function displayAll(id)
{
	$("#headpart").show();
	$("#imgarrow").show();
	//alert(startDecimalFlag);
	if(remMode == 'D' && id=="decimalPointDiv")
	{
		if(levelindex == 2)
		{
			if(putDecimalAttempt==0)
			{
				editableBlocks();
				bodypart.innerHTML="";
				$("#t_12").val("0");
				$("#td13").prepend($("#decimalSpanDiv").html());
				makeUneditable(12);
				$("#t_12").focus();
				$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);
			}
			else
			{
				$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[9].childNodes[0].nodeValue);
				$("#headpart").html("Read careFully!");
				$("#bodypart").html($("#decimalWhole").html());
				putDecimalAttempt=0;
			}
		}
		else if(putDecimalAttempt==0)
		{
			$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[3].childNodes[0].nodeValue);
			$("#headpart").html("Read careFully!");
			$("#bodypart").html($("#decimalWhole").html());
			putDecimalAttempt++;
		}
		else
		{
			$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[8].childNodes[0].nodeValue);
			$("#headpart").html("Read careFully!");
			$("#bodypart").html($("#decimalWhole").html());
			putDecimalAttempt=0;
		}
	}
	else if(id=="decimalPointDiv" && remMode != 'D')
	{
		if(putDecimalAttempt==0)
		{
			if(putdecimal==1)
			{
				if(startDecimalFlag == 1)
				{
					$("#td"+parseInt(parseInt(levelsflow[levelindex].startid)+1)).prepend($("#decimalSpanDiv").html());	
					$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).prepend($("#decimalSpanDiv").html());
					$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("border-top-width","3px");
					$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("border-top-color","rgb(0, 0, 0)");
					
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).val("0");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("color","black");
					
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("color","black");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("font-size","12px");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("opacity","1");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("visibility","visible");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("background-color","rgb(135, 206, 250)");
					
					editableBlocks();
					bodypart.innerHTML="";
					$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);	
					
				}
				else
				{
					$("#td"+levelsflow[levelindex].startid).prepend($("#decimalSpanDiv").html());
					if(NoAfterDecimalChk==0)
						$("#td"+levelsflow[levelindex+2].startid).prepend($("#decimalSpanDiv").html());
					
					editableBlocks();
					bodypart.innerHTML="";
					$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);	
				}			
				
				
			}
			else
			{
				$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[3].childNodes[0].nodeValue);
				$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[7].childNodes[0].nodeValue);
				$("#bodypart").html($("#decimalWhole").html());
				putDecimalAttempt++;
			}
		}
		else
		{
			if(putdecimal==1)
			{
				if(startDecimalFlag == 1)
				{
					$("#td"+parseInt(parseInt(levelsflow[levelindex].startid)+1)).prepend($("#decimalSpanDiv").html());	
					$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).prepend($("#decimalSpanDiv").html());
					$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("border-top-width","3px");
					$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("border-top-color","rgb(0, 0, 0)");
					
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).val("0");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("color","black");
					
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("color","black");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("font-size","12px");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("opacity","1");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("visibility","visible");
					$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("background-color","rgb(135, 206, 250)");
					
					editableBlocks();
					bodypart.innerHTML="";
					$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);	
				}
				else
				{
					$("#td"+levelsflow[levelindex].startid).prepend($("#decimalSpanDiv").html());
					if(NoAfterDecimalChk==0)
						$("#td"+levelsflow[levelindex+2].startid).prepend($("#decimalSpanDiv").html());
					
					editableBlocks();
					bodypart.innerHTML="";
					$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);	
				}		
				putDecimalAttempt=0;
			}
			else
			{
				$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[8].childNodes[0].nodeValue);
				$("#headpart").html("Read careFully!");
				$("#bodypart").html($("#decimalWhole").html());
				putDecimalAttempt=0;
			}
		}
	}
	else
	{
		if(putDecimalAttempt==0)
		{
			if(levelindex == 2 && remMode == 'D')
			{
				$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[10].childNodes[0].nodeValue);
				$("#headpart").html("Read careFully!");
				$("#bodypart").html($("#decimalWhole").html());
			}
			else if(putdecimal==1 && remMode == 'D')
			{
				editableBlocks();
				bodypart.innerHTML="";
				$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);
			}
			else if(putdecimal==0)
			{
				editableBlocks();
				bodypart.innerHTML="";
				$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);
			}
			else
			{
				if(startDecimalFlag == 1)
				{
					var tempStr = xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[4].childNodes[0].nodeValue;
					tempStr = tempStr.replace("#dividend#",gameObj.Aval);
					tempStr = tempStr.replace("#divisor#",gameObj.Bval);
					$("#decimPrompt").html(tempStr);
				}
				else
				{
					var startid_td = parseInt(levelsflow[levelindex-1].startid);
					var endid_td = parseInt(levelsflow[levelindex-1].endid-1);
					var tempStr = '';
					
					for(var k=0; k < objectArray.length;k++)
					{
						if(parseInt(objectArray[k].id.replace("t_","")) >=startid_td  && parseInt(objectArray[k].id.replace("t_","")) <=endid_td)
						{
							if(parseInt(objectArray[k].value)>=10)
								tempStr +=parseInt(parseInt(objectArray[k].value)-10);
							else
								tempStr +=objectArray[k].value;		
							
						}
					}
					tempDifference = tempStr; 
					tempStr = xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[4].childNodes[0].nodeValue;
					
					tempStr = tempStr.replace("#divisor#",gameObj.Bval);
					tempStr = tempStr.replace("#dividend#",tempDifference);					
					$("#decimPrompt").html(tempStr);
				}
					
				$("#headpart").html("Read careFully!");
				$("#bodypart").html($("#decimalWhole").html());
				putDecimalAttempt++;
			}
		}
		else
		{
			if(putdecimal==0)
			{
				editableBlocks();
				bodypart.innerHTML="";
				$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);
				putDecimalAttempt=0;
			}
			else if(putdecimal==1 && remMode == 'D')
			{
				editableBlocks();
				bodypart.innerHTML="";
				$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);
				putDecimalAttempt=0;
			}
			else
			{
				$("#decimPrompt").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[9].childNodes[0].nodeValue);
				$("#headpart").html("Read careFully!");
				$("#bodypart").html($("#decimalWhole").html());
				putDecimalAttempt=0;
			}
		}
	}
}

function getDecimalSpan()
{
	if(putDecimalAttempt==1)
	{
		$("#bodypart").html($("#askUser").html());
		$("#correctStep").show();
		$("#headpart").hide();
		$("#imgarrow").hide();
		$("#headpart").html("");
	}
	else if(remMode == 'D' && putDecimalAttempt==0)
	{
		if(levelindex == 2)
		{
			editableBlocks();
			bodypart.innerHTML="";
			$("#t_12").val("0");
			$("#td13").prepend($("#decimalSpanDiv").html());
			makeUneditable(12);
			$("#t_12").focus();
			$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);
		}
		else
		{
			editableBlocks();
			bodypart.innerHTML="";
			$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);		
		}
	}
	else if(putdecimal==1 && putDecimalAttempt==0)
	{
		if(startDecimalFlag == 1)
		{
			$("#td"+parseInt(parseInt(levelsflow[levelindex].startid)+1)).prepend($("#decimalSpanDiv").html());	
			$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).prepend($("#decimalSpanDiv").html());
			
			$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("border-top-width","3px");
			$("#td"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("border-top-color","rgb(0, 0, 0)");
					
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).val("0");
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("color","black");
			
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("color","black");
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("font-size","12px");
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("opacity","1");
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("visibility","visible");
			$("#t_"+parseInt(dividendOld.toString().length+divisorNew.toString().length+1)).css("background-color","rgb(135, 206, 250)");
			
			editableBlocks();
			bodypart.innerHTML="";
			$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);	
		}
		else
		{
			$("#td"+levelsflow[levelindex].startid).prepend($("#decimalSpanDiv").html());
			if(NoAfterDecimalChk==0)
				$("#td"+levelsflow[levelindex+2].startid).prepend($("#decimalSpanDiv").html());
			
			editableBlocks();
			bodypart.innerHTML="";
			$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[5].childNodes[0].nodeValue);		
		}				
	}
	else if(putDecimalAttempt==0)
	{
		editableBlocks();
		bodypart.innerHTML="";
		$("#headpart").html(xmlDocNew.getElementsByTagName("correctStep")[0].getElementsByTagName("span")[6].childNodes[0].nodeValue);
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
if(isiPad1)
{
	if(strl.length==1)
	{
	newCol.setAttribute("width",(sw+sh)/10+"px");
    newCol.setAttribute("height",(sw+sh)/20+"px");
   }
   else if(strl.length==2)
	{
	newCol.setAttribute("width",(sw+sh)/10+"px");
    newCol.setAttribute("height",(sw+sh)/20+"px");
   }
   else if(strl.length==3)
	{
	newCol.setAttribute("width",(sw+sh)/10+"px");
    newCol.setAttribute("height",(sw+sh)/20+"px");
   }
   else if(strl.length==4)
	{
	newCol.setAttribute("width",(sw+sh)/10+"px");
    newCol.setAttribute("height",(sw+sh)/20+"px");
   }
   else
   {
   newCol.setAttribute("width",(sw+sh)/20+"px");
   newCol.setAttribute("height",(sw+sh)/20+"px");
   }
}
else
{
newCol.setAttribute("width",(sw+sh)/20+"px");
newCol.setAttribute("height",(sw+sh)/20+"px");
}
if(isiPad1)
{
	//$(".stb").css("font-size",(sw+sh)/39+"px");
	$(".stb").css("font-size","12px");
}
else
{
$(".stb").css("font-size",(sw+sh)/28+"px");
}


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
	txtbox.setAttribute("type","tel");
	
	//txtbox.setAttribute("readonly",false);
	//txtbox.attachEvent("onkeyup",stkeyup);
	//txtbox.addEventListener("onclick",stkeyup);
	
	
	
	
	newCol.appendChild(txtbox);
newRow.appendChild(newCol);
table.getElementsByTagName("tbody")[0].appendChild(newRow);


 if(navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null) 
    	  	$("#sttb"+idgen).css("top",62+"%");
    	  else
         $("#sttb"+idgen).css("top",63+"%");

if(isiPad1)
{
	
}
else
{

if(strl.length==5)
$("#sttb"+idgen).css("left",13.8+idgen*12.5+"%");
else
$("#sttb"+idgen).css("left",calcleft()+idgen*dynleft()+"%");

}
// For use within normal web clients 


if(isiPad1)
{
	//alert('ipad');
	$(".stb").css("font-size","12px");
	$(".txtstyle").css("font-size","12px");//sw+sh)/39+
	
if(strl.length==1)
{
	$(".txtstyle").css("font-size","12px");
	$("#sttb"+idgen).css("width","10%");
}
else if(strl.length==2)
{
	$(".txtstyle").css("font-size","12px");
	$("#sttb"+idgen).css("width","8.6%");
}
else if(strl.length==3)
{
	$(".txtstyle").css("font-size","12px");
	$("#sttb"+idgen).css("width","7%");
}
else if(strl.length==4)
{
	$(".txtstyle").css("font-size","12px");
	$("#sttb"+idgen).css("width","6%");
}
else
$("#sttb"+idgen).css("width","4.8%");


$("#sttb"+idgen).css("height","12px");//10%
$("#sttb"+idgen).css("text-align","left");
	
	
}
else {
if(navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
{
	
	$(".txtstyle").css("font-size",(sw+sh)/28+"px");
	if(strl.length==5)
		$("#sttb"+idgen).css("width",9+"%");
	else
		$("#sttb"+idgen).css("width",9+(6-strl.length)+"%");
	$("#sttb"+idgen).css("height",16.5+"%");
}
else
{
	
	$(".txtstyle").css("font-size",(sw+sh)/28+"px");
	if(strl.length==5)
$("#sttb"+idgen).css("width",9+"%");
else
$("#sttb"+idgen).css("width",9+(6-strl.length)+"%");
$("#sttb"+idgen).css("height",14+"%");

}
}
	
idgen++;	
}
$('#stbutton').text('Check');

if(strestart==1)
{
  //  var temp= $('#dynsttext').position().top;
  		// $('#dynsttext').css("top",75+'%');
  		// $('#stbutton').css("top",60+'%');
  		 if( navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
  		{
  			 $('#dynsttext').css("top",77+'%');
  			  $('#stbutton').css("top",63+'%');
  		}else
  		{
  		 $('#dynsttext').css("top",77+'%');
  		  $('#stbutton').css("top",62+'%');
  		}
  		 
  		 
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
  for(i=0;i<idgen;i++)
  {
  		$('#sttb'+i).attr('disabled','true');
  		$('#sttb'+i).css('filter','none');
  		$('#sttb'+i).css('opacity','1');
  		
  }
}
strestart=0;

function stkeyup(e)
{
	if(!isiPad1)
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
    }else
    {
    	document.activeElement.blur();
   }
}

function sizable()
{
	$(".stb").css("width",(sw+sh)/20+"px");
	$(".stb").css("height",(sw+sh)/20+"px");
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
	

function stvalidate()
{
	var myval="";
	bodypart.innerHTML="";
	bodytext1="";
	headpart.innerHTML=xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[1].getElementsByTagName("head")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue
	stcount++;
  for(i=0;i<=strl.length;i++)
  {
  	
  	myval=myval+$("#sttb"+i).val();
  	
  }
  if(parseInt(myval,10)==stsol)
  {
  	stcount=0;
  	explainextention();
  	adddiv(desquot,despar);
  }
  else
  {
  	if(stcount==1)
  	{
 $('#dynsttext').text(xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[1].getElementsByTagName("body")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue);
   colortexbox();
  	}
  	else if(stcount==2)
  	{
  		 $('#dynsttext').text(xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[2].getElementsByTagName("body")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue);
  		   colortexbox();
  	}
  	else if(stcount==3)
  	{
  		 $('#dynsttext').text(xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[3].getElementsByTagName("body")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue);
  		  colortexbox();
  	}
  	else 
  	{
  		
  		strestart=1;
  		 $('#dynsttext').text(xmlDocNew.getElementsByTagName("tablefield")[0].getElementsByTagName("explanation")[4].getElementsByTagName("body")[0].getElementsByTagName("span")[0].childNodes[0].nodeValue+" "+stsol);
  		var temp= $('#dynsttext').position().top;
  		if( navigator.userAgent.match(/Android|Android 3.2|Android 3.1|Android 3.0/)!=null)
  		{
  			$('#dynsttext').css("top",63+'%');
  			$('#stbutton').css("top",87+'%');
  		}
		else
  		{
			$('#dynsttext').css("top",62+'%');
			$('#stbutton').css("top",87+'%');
  		}
   		 $('#stbutton').text('OK');
  		 var varans=stsol.toString();
  		 if(varans.length==idgen)
  		 {
	  		 crcktst=0;
  		 }
  		 else
  		 {
			crcktst=1;
			$('#sttb0').val('');
			$('#sttb0').css('color','black');
			$('#sttb0').attr("readonly","true");
			$('#sttb0').attr('disabled','true');
  		 }
  		 
 		 for(i=crcktst;i<idgen;i++)
         {
         	
         	if(crcktst)
         	{
         		if(stsol!=0)
         	 $('#sttb'+i).val(varans[i-1]);
         	 else
         	 {
         	 	for(i=0;i<[idgen-1];i++)
         	 	{
         	 		 $('#sttb'+i).val('');
         	 		  $('#sttb'+i).attr("readonly","true");
         	 		  $('#sttb'+i).attr("disabled","true");
         	 		 
         	 	}
         	 $('#sttb'+[idgen-1]).val('0');
         	 }
         	}
           else
  		    $('#sttb'+i).val(varans[i]);
  		   
  		   
  		   $('#sttb'+i).css('color','black');
  		    $('#sttb'+i).attr("readonly","true");
  		    $('#sttb'+i).attr("disabled","true");
  		    // document.getElementById("sttb"+i).readOnly = true;
  		} 
  		 
  		  $("#stbutton").click(function(){
  		  	if( $('#stbutton').text()=='OK' && stcount==5)
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
		if(navigator.userAgent.match(/ipad/i)!=null)
			$("#sttb"+i).css("font-size","12px");
			
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
	p1.removeChild(p2);
	var p3 =document.createElement('table');
	p3.setAttribute("id","table");
	p3.setAttribute("cellpadding","0px");
	p3.setAttribute("cellspacing","0px");
	var p4=document.createElement('tbody');
	p3.appendChild(p4);
	p1.appendChild(p3);
	$('#dynsttext').text("");
	$('#stbutton').text('Check');
	$('#stbutton').css('display','none');
	$('#statbutton').css('display','none');
	$("#smtable").css({'z-index':'-1','display':'none'});
	st2count=0;
	idgen=0;
	stcount=0;
}
function makeUneditable(tID)
{
	$("#t_"+tID).css('color','black');
	$("#t_"+tID).attr('readonly','readonly');
	$("#td"+tID).css('background-color','#87cefa');
	$("#t_"+tID).css('background-color','#87cefa');
	objectArray[tID].status='disable';
}