$(document).ready(function(e) {
    $(".forHigherOnly").remove();
});


function adjustScreenElements() {
	if(window.innerHeight>400 && !isAndroid ){
		$('#question').css("height","");
	/*var element = $('#scroll').jScrollPane({showArrows: true});
	var api = element.data('jsp');
	api.scrollToElement($('#question'), true, true);*/

		/*var a= window.innerHeight -34 -17 - 25;
		var b= window.innerHeight -34 -17-155 - 25;
		var c= window.innerHeight -34 -17-180-40;
		$('#pnlQuestion').css("height",a);
		$('#scroll').css("height",b+"px");
		$('#question').css("height",c+"px");*/
		
		//var b= window.innerHeight - (34 +155 + 50);
		var b= window.innerHeight - (170);
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
		if($("#pnlOptions").css("display")=="block")
		{
			$("#submitQuestion1").css("display","none");
			//$('#submit_bar1').css("display","none");
		}
		//$('#scroll').jScrollPane({showArrows: true});
	}
    else {
		if(window.innerHeight > 900)
		{
			var a= window.innerHeight - (170);
			$("#scroll").css("min-height",a+"px");
		}
		else
		{
			$("#scroll").css("min-height","332px");
			$("#scroll").css("height","auto");
		}
		$("#question").css("height","auto");
		$('#pnlQuestion').css({"height":"auto","display":"block"});
		if($("#pnlOptions").css("display")=="block")
		{
			$("#submitQuestion1").css("display","none");
		}
	}
}
function showSubmitButton()
{
    $("#submitQuestion1").css("display","block");
	$('#scroll').animate({
		   scrollTop: 0
		}, 'slow');	
     enableSubmitButton();
    $('#submit_bar1').css("display","block");
    $('#nextQuestion1').css("display","none");
}
function showNextButton()
{
    $('#submitQuestion1').css("display","none");
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
	if(objQuestion.quesType == 'I')
		$('#quesStem').css('pointer-events', 'none');
    var d= document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 110;
    /*var e= d + parseInt($('#question').css("height"));
	
    $('#pnlAnswer').animate({'height':d+'px'},1,function(){
		$('#scroll').animate({
		   scrollTop: e
		}, 'slow');	
	});*/
	$('#pnlAnswer').css('height',d+'px');
	//$('#pnlAnswer').animate({'height':d+'px'},500);
	//alert($("#quesStem").height());
	/*alert($("#quesStem").height());
	$('#scroll').scrollTop(parseInt($("#quesStem").height())+25);
	$('#nextQuestion1').css("display","block");*/
	if(isAndroid){
		$('#scroll').css("height","auto");
		$('body').animate({
		    scrollTop: $("#quesStem").height() +25+$("#top_bar").height()+$("#info_bar").height()+50
			}, 'slow',function(){
				//$('#nextQuestion1').css("display","block");
		});
	}else{
		$('#scroll').animate({
		   scrollTop: $("#quesStem").height() +25
			}, 'slow',function(){
				//$('#nextQuestion1').css("display","block");
		});
	}
	

    //$('#scroll').jScrollPane({showArrows: true});
    /*var element = $('#scroll').jScrollPane({autoReinitialise: true});
    var api = element.data('jsp');
    api.scrollToElement($('#pnlAnswer'), true, true);*/
	// pending
    $('#submitQuestion1').css("display","none");
    $('#submit_bar1').css("display","block");
	$("#question").height($("#quesStem").height() + $("#pnlAnswer").height());
    //$('#option'+id+' optionX').css("background","url('assets/loweClass/mcq.button.hoover.png') no-repeat 0 0");
}

function hideBar(){

}
var hideToolBarTimeout;
function toolbar(){
	if(toolClick==0) {
		$("#close").css("display","none");
		$("#open,.closeToolbar").css("display","block");
		toolClick=1;
		clearTimeout(hideToolBarTimeout);
		hideToolBarTimeout = setTimeout(function(){
			if(toolClick==1)
				toolbar();
		},5000);
	}
	else if(toolClick==1) {
		clearTimeout(hideToolBarTimeout);
		$("#open,.closeToolbar").css("display","none");
		$("#close").css("display","block");
		toolClick=0;
	}
}
function changeQuestion(){

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