var qq=0;
$(document).ready(function(e) {
	if($('#quesCategory').val()=="daTest"){
		$("#drawer5").hide();
		$("#reportText").hide();
		$("#reportIcon").hide();
	}
    $(".hidden").remove();
	if($('#quesCategory').val()=="NCERT"){
		$("#progress_bar,.pieContainer,#question_number,#topic_name").remove();
	}

	if($('.dynamic-text span').width()>250)
	{
		$('#topic_name').removeClass("topic_name_large");
		$('#topic_name').addClass("topic_name_small");
	}
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
		if(qq==0 && $('#quesCategory').val()!="NCERT" && $('#quesCategory').val()!="worksheet"){
			if(newQues.quesType && newQues.quesType.substring(0, 3) == "MCQ")
				{
					$("#submitArrow").css("display","none");
					if($("#quesCategory").val() != 'daTest'){
						$("#submitQuestion2").css("display","block");
						$("#submitQuestion2").html("Click on option to answer.");
						$("#submitQuestion2").attr("onclick","");
						$("#submitQuestion2").css("cursor","default");
						$("#submitQuestion2").css("font-size","1.3em");
						$("#submitQuestion2").css("font-weight","initial");	
					}
					
					showSkip('3');
				}
				else{
					$("#submitArrow").css("display","block");
					$("#submitQuestion2").html("Submit");
					$("#submitQuestion2").attr("onclick","submitAnswer()");
					$("#submitQuestion2").css("cursor","pointer");
					$("#submitQuestion2").css("font-size","1.7em");
					$("#submitQuestion2").css("font-weight","bold");
					showSkip('3');
				}
				if(newQues.quesType && newQues.quesType=="I" && /\/ADA_eqs\/src\/index\.html/.test(Q2)) {
					$("#submitArrow").hide();
				}
				qq=1;
		}
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
	if ($('#quesCategory').val() == "NCERT") {
    	$('#saveNCERTQuestion2').css("display", "block");
    }
	showSkip('3');
     enableSubmitButton();
    $('#submit_bar').css("display","block");
    $('#nextQuestion2').css("display","none");
    $('#submitArrow').css("display","block");
    $('#arrow').css("display","none");
}
function showNextButton()
{
    $('#submitQuestion2').css("display","none");
	$('#saveNCERTQuestion2').css("display", "none");
	$("#skipQuestion2").css("display","none");
	$("#submitArrow").css("display","block");
	$('#mcqText').css("display","none");
    disableSubmitButton();
    $('#nextQuestion2').css("display","block");
	$("#skipQuestion2").css("display","none");
    $('#arrow').css("display","block");
    $('#submit_bar').css("display","block");
    
    // if question is of NCERT and it's last group submit then hide submit and next button
    if($(".groupNav").length == $(".complete").length && $(".groupNav").length > 0) {
		hideSubmitNextButton();
	}
}
function hideSubmitBar()
{
    $('#submit_bar').css("display","none");
    hideSubmitNextButton();
    disableSubmitButton();
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
}
function disableSubmitButton()
{

}
function enableSubmitButton()
{

}

function hideBar(){
	
}
function animateAnswerBox()
{	
    $('#pnlAnswer').css("display","block");
	
	setTimeout(function(){
		var d;
		d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 235;
		$('#pnlAnswer').css('height',d+'px');
	},100);

	if(isAndroid || isIpad){
		$("#sideBar").css("height",$("#pnlQuestion").css("height"));
		$('body').animate({
		    scrollTop: $("#quesStem").height() +45+$("#top_bar").height()+$("#info_bar").height()
			}, 'slow');
	}else{
		$('#scroll').animate({
		   scrollTop: $("#quesStem").height() +45
			}, 'slow');
	}

}

var hideToolBarTimeout;
function toolbar(){
    if(toolClick==0){
		
		/*$('#elements').fadeIn(500);*/
	/*	$('#whiteContainer').fadeIn(500);*/
		toolClick=1;
		$("#rate").css("background","url('assets/higherClass/question/rate.png') no-repeat 0 -64px");
		//clearTimeout(hideToolBarTimeout);
		//hideToolBarTimeout	=	setTimeout(function(){
		//	if(toolClick==1)
		//	{
		//		toolbar();
		//	}
		//},3000);
	}
	else if(toolClick==1){
		//clearTimeout(hideToolBarTimeout);
	/*	$('#elements').fadeOut(500);*/
		$("#rate").css("background","url('assets/higherClass/question/rate.png') no-repeat 0 0");
		/*$('#whiteContainer').fadeOut(500);*/
		$('#toolContainer').css("background-color","#ffffff");
		toolClick=0;
		return;
	}
}

function toolbar1(id){
	var id=id;
	//$("#comment").css("background","url('assets/higherClass/question/comment.png') no-repeat 0 0");
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
	/*$('#elements').fadeOut(200);*/
/*	$('#whiteContainer').fadeOut(500);*/
	$("#rate").css("background","url('assets/higherClass/question/rate.png') no-repeat 0 0");
	toolClick=0;
}
function comment(){
	$('#commentBox').css("display","block");
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
	$("#comment").css("background","url('assets/higherClass/question/comment.png') no-repeat 0 -64px");
}
function openTextBox(){
	Id=document.getElementById("selCategory");
	if(Id.selectedIndex != 0){
		$('#textBox').css("display","block");
	}
}
function cancel() {
	$('#commentBox').css("display","none");
	$('#textBox').css("display","none");
	$("#endSessionClick").css("display","none");
	$("#endTopic,#blackScreen").css("display","none");
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
	if(isAndroid || isIpad){
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

// Added for NCERT module
function hideSubmitNextButton()
{
    $("#submitQuestion2").hide();
	$('#saveNCERTQuestion').hide();
    disableSubmitButton();
//    $('#nextQuestion2').hide();
    $('#submitQuestion2').hide();
    $('#submitArrow').hide();
}
function hideSuperTestBar(){
	var currentValue = $("#super-test-hideShowBar").html();
	if(currentValue == '-'){
		$(".color-info").hide("slow");
		$("#super-test-hideShowBar").html("+");
	}
	else{
		$(".color-info").show("slow");
		$("#super-test-hideShowBar").html("-");
	}
}