$(document).ready(function(e) {
    $(".forLowerOnly,.completeHigher").remove();
});
/*function adjustScreenElements(){
    $('#question').css("height","");
    var infobarHeight = document.getElementById("info_bar").offsetHeight;
	var a= window.innerHeight -infobarHeight - 80 -17;
	var b= window.innerHeight -infobarHeight - 80 -17;
	var c= window.innerHeight - 292;
	var d= window.innerHeight - 307;
	$('#question').css('min-height',c+"px");
	$('#reviewDiv').css('min-height',d+"px");
	$('#pnlQuestion').css("height",b);
	$('#scroll').css("height",a);
	$('#pnlLoading').css("height",a);
}*/

function adjustScreenElements(){
	if(window.innerHeight>400 && !isAndroid ){
		$('#question').css("height","");
	    var infobarHeight = document.getElementById("info_bar").offsetHeight;
		var a= window.innerHeight -infobarHeight - 80 -17;
		var b= window.innerHeight -infobarHeight - 80 -17;
		$('#pnlQuestion').css("height",b);
		$('#scroll').css("height",a);
		$("body").css("overflow-y","hidden");
		$("#hideShowBar,#showHide").css("display","block");
		$('#pnlLoading').css("height",a);
		$("#confused").css("background","url('assets/confused.png') no-repeat 0 0");
		$("#bored").css("background","url('assets/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/excited.png') no-repeat 0 0");
		$("#like").css("background","url('assets/like.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/dislike.png') no-repeat 0 0");
		$("#comment").css("background","url('assets/comment.png') no-repeat 0 0");
	}
	else {
		$("#scroll").css("height","auto");
		$("#pnlQuestion").css("height","auto");
		$("#hideShowBar,#showHide").css("display","none");
		$("body").css("overflow-y","scroll");
	}
}

function showSubmitButton()
{
	if(reviewed==0)
 	   $("#submitQuestion").css("display","block");
    enableSubmitButton();
    $('#submit_bar').css("display","block");
    $('#nextQuestion').css("display","none");
    $('#arrow').css("display","none");
}
function showNextButton()
{	
    $('#submitQuestion').css("display","none");
	$('#nextQuestion').css("display","block");
	$('#mcqText').css("display","none");
    disableSubmitButton();
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
    //$('#submitQuestion').attr("disabled",true);
}
function enableSubmitButton()
{
    //$('#submitQuestion').attr("disabled",false);
}
function animateAnswerBox()
{
    $('#pnlAnswer').css("display","block");
	
	//var h = parseInt($('#question').css("height"));
    var d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 120;

    //var e = d + h;
    /*$('#submitQuestion').css("display","none");
	$('#nextQuestion').css("display","block");
	$('#arrow').css("display","block");
	$('#submit_bar').css("display","block");*/
	
	//$('#question').css("height",e+"px");
	$('#pnlAnswer').css('height',d+'px');
	//$('#pnlAnswer').animate({'height':d+'px'},500);
	//alert($("#quesStem").height());
	$('#scroll').animate({
	   scrollTop: $("#quesStem").height() +45
	}, 'slow',function(){
		$('#nextQuestion').css("display","block");	
	});
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
	$('#submitQuestion').css("display","none");
	$('#nextQuestion').css("display","block");
	$('#arrow').css("display","block");
	$('#submit_bar').css("display","block");
	$('#question').css("height",e+"px");
	$('#validate').animate({'height':d+'px'},500);
	$('#scroll').jScrollPane({showArrows: true});
	var element = $('#scroll').jScrollPane();
	var api = element.data('jsp');
	api.scrollToElement($('#validate'), true, true);
}*/



function openTextBox(){
	Id=document.getElementById("DropDown1");
	if(Id.selectedIndex != 0){
		$('#textBox').css("display","block");
	}
}
function cancel() {
	$('#commentBox').css("display","none");
	$("#higherLevelClick").css("display","none");
	//Id=document.getElementById("DropDown1");
	//Id.selectedIndex = 0;
	$('#textBox').css("display","none");
	$("#endSessionClick").css("display","none");
	$("#endTopic").css("display","none");
}

function top1(){
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
}
function markOption(userAns)
{
	$('.optionX').removeClass("optionInactive");
	$('#option'+userAns+' .optionX').addClass("optionActive");
}