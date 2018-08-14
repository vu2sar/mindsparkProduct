var qq=0;
$(document).ready(function(e) {
    $(".forLowerOnly").remove();
});

function adjustScreenElements(){
	if(window.innerHeight>400 && !isAndroid && !ipadVersionCheck){
	    $('#question').css("height","");
	    var infobarHeight = document.getElementById("info_bar").offsetHeight;
				var a= window.innerHeight - (170);
				var b= window.innerHeight - (610);
				$('#pnlQuestion').css({"height":a+"px"});
				$('#topicInfoContainer').css({"height":a+"px"});
				$('#scroll').css({"height":a+"px"});
				$('#menu_bar').css({"height":a+"px"});
				$('#sideBar').css({"height":a+"px"});
		var category = $('#quesCategory').val();
		$("#confused").css("background","url('assets/higherClass/question/confused.png') no-repeat 0 0");
		$("#bored").css("background","url('assets/higherClass/question/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/higherClass/question/excited.png') no-repeat 0 0");
		$("#like").css("background","url('assets/higherClass/question/like.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/higherClass/question/dislike.png') no-repeat 0 0");
		$("#comment").css("background","url('assets/higherClass/question/comment.png') no-repeat 0 0");
		if(qq==0){
			if(objQuestion.quesType.substring(0, 3) == "MCQ")
				{
					$("#submitArrow").css("display","none");
					$("#submitQuestion2").css("display","block");
					$("#submitQuestion2").html("Click on option to answer");
					$("#submitQuestion2").attr("onclick","");
					$("#submitQuestion2").css("cursor","default");
					$("#submitQuestion2").css("font-size","1.3em");
					$("#submitQuestion2").css("font-weight","initial");
				}
				else{
					$("#submitArrow").css("display","block");
					$("#submitQuestion2").html("Submit");
					$("#submitQuestion2").attr("onclick","submitAnswer()");
					$("#submitQuestion2").css("cursor","pointer");
					$("#submitQuestion2").css("font-size","1.7em");
					$("#submitQuestion2").css("font-weight","bold");
				}
				qq=1;
		}
		//$('#scroll').css("height",a);
		//$('#pnlLoading').css("height",a);
		//$('#scroll').jScrollPane({showArrows: true});
		/*if(window.innerHeight>700)
		{
			var b= window.innerHeight -257 -infobarHeight -17 -140 ;
			$('#submit_bar').css("margin-top",b);
		}*/	
	}
	else{
		$("#scroll").css("height","auto");
		$("#pnlQuestion").css("height","auto");
		$("#sideBar").css("height",$("#pnlQuestion").css("height"));
	}
}

function showSubmitButton()
{
    $("#submitQuestion2").css("display","block");
     enableSubmitButton();
    $('#submit_bar').css("display","block");
    $('#nextQuestion2').css("display","none");
    $('#arrow').css("display","none");
}
function showNextButton()
{
    $('#submitQuestion2').css("display","none");
	$("#submitArrow").css("display","block");
	$('#mcqText').css("display","none");
    disableSubmitButton();
    $('#nextQuestion2').css("display","block");
    $('#arrow').css("display","block");
    $('#submit_bar').css("display","block");
}
function hideSubmitBar()
{
    $('#submit_bar').css("display","none");
    disableSubmitButton();
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
}
function disableSubmitButton()
{
    //$('#submitQuestion2').attr("disabled",true);
}
function enableSubmitButton()
{
    //$('#submitQuestion2').attr("disabled",false);
}

function hideBar(){
	
}
function animateAnswerBox()
{
    $('#pnlAnswer').css("display","block");
	if(objQuestion.quesType == 'I')
		$('#quesStem').css('pointer-events', 'none');
	
	//var h = parseInt($('#question').css("height"));
    var d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 235;

    //var e = d + h;
    /*$('#submitQuestion2').css("display","none");
	$('#nextQuestion2').css("display","block");
	$('#arrow').css("display","block");
	$('#submit_bar').css("display","block");*/
	
	//$('#question').css("height",e+"px");
	$('#pnlAnswer').css('height',d+'px');
	//$('#pnlAnswer').animate({'height':d+'px'},500);
	//alert($("#quesStem").height());
	if(isAndroid){
		$("#sideBar").css("height",$("#pnlQuestion").css("height"));
		$('body').animate({
		    scrollTop: $("#quesStem").height() +45+$("#top_bar").height()+$("#info_bar").height()
			}, 'slow');
	}else{
		$('#scroll').animate({
		   scrollTop: $("#quesStem").height() +45
			}, 'slow');
	}
	/*$('#scroll').jScrollPane({showArrows: true});
	var element = $('#scroll').jScrollPane();
	var api = element.data('jsp');
	api.scrollToElement($('#pnlAnswer'), true, true);*/
}
 /*function validation(id){
	var id=id;
	if(ncert=="true"){
		return
	}
	$('#'+id).css("background","url('assets/loweClass/mcq.button.hoover.png') no-repeat 0 0");
	$('#validate').css("display","block");
	var d= document.getElementById("wrong_text").offsetHeight + 120;
	var e= d + parseInt($('#question').css("height"));
	$('#submitQuestion2').css("display","none");
	$('#nextQuestion2').css("display","block");
	$('#arrow').css("display","block");
	$('#submit_bar').css("display","block");
	$('#question').css("height",e+"px");
	$('#validate').animate({'height':d+'px'},500);
	$('#scroll').jScrollPane({showArrows: true});
	var element = $('#scroll').jScrollPane();
	var api = element.data('jsp');
	api.scrollToElement($('#validate'), true, true);
}*/
var hideToolBarTimeout;
function toolbar(){
	if(toolClick==0){
		
		$('#elements').fadeIn(500);
		$('#whiteContainer').fadeIn(500);
		toolClick=1;
		$("#rate").css("background","url('assets/higherClass/question/rate.png') no-repeat 0 -64px");
		clearTimeout(hideToolBarTimeout);
		hideToolBarTimeout	=	setTimeout(function(){
			if(toolClick==1)
			{
				toolbar();
			}
		},3000);
	}
	else if(toolClick==1){
		clearTimeout(hideToolBarTimeout);
		$('#elements').fadeOut(500);
		$("#rate").css("background","url('assets/higherClass/question/rate.png') no-repeat 0 0");
		$('#whiteContainer').fadeOut(500);
		$('#toolContainer').css("background-color","#ffffff");
		toolClick=0;
		return;
	}
}

function toolbar1(id){
	var id=id;
	$("#comment").css("background","url('assets/higherClass/question/comment.png') no-repeat 0 0");
	if(id=="confused"){
		$("#confused").css("background","url('assets/higherClass/question/confused.png') no-repeat 0 -64px");
		$("#bored").css("background","url('assets/higherClass/question/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/higherClass/question/excited.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/higherClass/question/dislike.png') no-repeat 0 0");
		$("#like").css("background","url('assets/higherClass/question/like.png') no-repeat 0 0");
	}
	else if(id=="bored")
	{
		$("#bored").css("background","url('assets/higherClass/question/bored.png') no-repeat 0 -64px");
		$("#excited").css("background","url('assets/higherClass/question/excited.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/higherClass/question/dislike.png') no-repeat 0 0");
		$("#like").css("background","url('assets/higherClass/question/like.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/higherClass/question/confused.png') no-repeat 0 0");
	}
	else if(id=="excited")
	{
		$("#excited").css("background","url('assets/higherClass/question/excited.png') no-repeat 0 -64px");
		$("#bored").css("background","url('assets/higherClass/question/bored.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/higherClass/question/dislike.png') no-repeat 0 0");
		$("#like").css("background","url('assets/higherClass/question/like.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/higherClass/question/confused.png') no-repeat 0 0");
	}
	else if(id=="like")
	{
		$("#like").css("background","url('assets/higherClass/question/like.png') no-repeat 0 -64px");
		$("#dislike").css("background","url('assets/higherClass/question/dislike.png') no-repeat 0 0");
		$("#bored").css("background","url('assets/higherClass/question/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/higherClass/question/excited.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/higherClass/question/confused.png') no-repeat 0 0");
	}
	else if(id=="dislike")
	{
		$("#dislike").css("background","url('assets/higherClass/question/dislike.png') no-repeat 0 -64px");
		$("#like").css("background","url('assets/higherClass/question/like.png') no-repeat 0 0");
		$("#bored").css("background","url('assets/higherClass/question/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/higherClass/question/excited.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/higherClass/question/confused.png') no-repeat 0 0");
	}
	var id1=id;
	$('#elements').fadeOut(200);
	$('#whiteContainer').fadeOut(500);
	$("#rate").css("background","url('assets/higherClass/question/rate.png') no-repeat 0 0");
	toolClick=0;
}
function comment(){
	$('#commentBox').css("display","block");
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
	$("#comment").css("background","url('assets/higherClass/question/comment.png') no-repeat 0 -64px");
	/*var element = $('#scroll').jScrollPane();
	var api = element.data('jsp');
	api.scrollToElement($('#commentBox'), true, true);*/
}
function openTextBox(){
	Id=document.getElementById("DropDown1");
	if(Id.selectedIndex != 0){
		$('#textBox').css("display","block");
	}
}
function cancel() {
	$('#commentBox').css("display","none");
	//Id=document.getElementById("DropDown1");
	//Id.selectedIndex = 0;
	$('#textBox').css("display","none");
	$("#endSessionClick").css("display","none");
	$("#endTopic").css("display","none");
}

function openMainBar(){
	
	if(click==0){
		$("#main_bar").animate({'width':'245px'},600);
		$("#plus").animate({'margin-left':'227px'},600);
		$("#vertical").css("display","none");
		click=1;
	}
	else if(click==1){
		$("#main_bar").animate({'width':'26px'},600);
		$("#plus").animate({'margin-left':'7px'},600);
		$("#vertical").css("display","block");
		click=0;
	}
}

function top1(){
	if(isAndroid){
		$('body').animate({
		   scrollTop: 0
		}, 'slow');
	}else{
		$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
	}
	
}
function markOption(userAns)
{
	$('.optionX').removeClass("optionInactive");
	$('#option'+userAns+' .optionX').addClass("optionActive");
}
function changeQuestion(){

}