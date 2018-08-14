$(document).ready(function(e) {
    $(".forHigherOnly,.completeHigher").remove();
});

/*function adjustScreenElements() {
    $('#question').css("height","");
	var b= window.innerHeight - (10 +155 + 50);
	var a= window.innerHeight - (65);
	
	$('#scroll').css("height",b+"px");
	$('#pnlQuestion').css({"height":a+"px","display":"block"});
}*/

function adjustScreenElements() {
	if(window.innerHeight>400 && !isAndroid ){
		$('#question').css("height","");
		var b= window.innerHeight - (215);
		var a= window.innerHeight - (65);
		$('#scroll').css("height",b+"px");
		$('#pnlQuestion').css({"height":a+"px","display":"block"});
		
		if(document.getElementById("q2").offsetHeight<250){
			var c= window.innerHeight -34 -17-180-70;
			$('#quesStem').css("height",c+"px");
		}
		else {
			$('#quesStem').css("height","auto");
		}
	}
    else {
		if(window.innerHeight > 900)
		{
			var a= window.innerHeight - (215);
			$("#scroll").css("min-height",a+"px");
		}
		else
		{
			$("#scroll").css("min-height","300px");
			$("#scroll").css("height","auto");
		}
		$("#question").css("height","auto");
		$('#pnlQuestion').css({"height":"auto","display":"block"});
	}
}

function showSubmitButton()
{
	if(reviewed==0)
    	$("#submitQuestion1").css("display","block");
    $('#submit_bar1').css("display","block");
	$('#nextQuestion1').css("display","none");
}

function showNextButton()
{
	$('#nextQuestion1').css("display","block");
    disableSubmitButton();
	$('#mcqText').css("display","none");
    $('#submit_bar1').css("display","block");
}

function hideSubmitBar()
{
    $('#submit_bar1').css("display","none");
    disableSubmitButton();
}
function disableSubmitButton()
{
    //$('#submitQuestion1').attr("disabled",true);
}
function enableSubmitButton()
{
    //$('#submitQuestion1').attr("disabled",false);
}
function animateAnswerBox()
{
    $('#pnlAnswer').css("display","block");
    var d= document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 110;
	$('#pnlAnswer').css('height',d+'px');
	$('#scroll').animate({
	   scrollTop: $("#quesStem").height() +25
	}, 'slow',function(){
		$('#nextQuestion1').css("display","block");
	});

    $('#submit_bar1').css("display","block");
	$("#question").height($("#quesStem").height() + $("#pnlAnswer").height());
}

function cancel(){
	$('#commentBox').css("display","none");
	$("#higherLevelClick").css("display","none");
	//Id=document.getElementById("DropDown1");
	//Id.selectedIndex = 0;
	$('#textBox').css("display","none");
	$("#endSessionClick").css("display","none");
	$("#endTopic").css("display","none");
}

function comment(){
	$('#commentBox').css("display","block");
	$('#scroll').animate({
		   scrollTop: 0
		}, 'slow');
	/*var element = $('#scroll').jScrollPane();
	var api = element.data('jsp');
	api.scrollToElement($('#commentBox'), true, true);*/
}

function markOption(userAns)
{
	$('.optionX').removeClass("optionInactive");
	$('#option'+userAns+' .optionX').addClass("optionActive");
}