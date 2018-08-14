$(document).ready(function(e) {
	$(".hidden").remove();
});
/*function adjustScreenElements(){
	$(".hidden").remove();
    $('#question').css("height","");
    var infobarHeight = document.getElementById("info_bar").offsetHeight;
	var b= window.innerHeight -infobarHeight - 80 -17;
	var category = $('#quesCategory').val();
}*/
function adjustScreenElements(){
	if(window.innerHeight>400 && !isAndroid ){
	    $('#question').css("height","");
	    var infobarHeight = document.getElementById("info_bar").offsetHeight;
		var a= window.innerHeight - (170);
		var b= window.innerHeight - (610);
		$('#pnlQuestion').css({"height":a+"px"});
		$('#topicInfoContainer').css({"height":a+"px"});
		$('#scroll').css({"height":a+"px"});
		$('#menu_bar').css({"height":a+"px"});
		$('#sideBar').css({"height":a+"px"});
		$("#reviewQuestion").css("margin-top","175px");
		$("#reviewArrow").css("margin-top","340px");
		var category = $('#quesCategory').val();
	}
	else{
		$("#scroll").css("height","auto");
		$("#pnlQuestion").css("height","auto");
		$("#menuBar").css("height","auto");
		$("#reviewQuestion").css("margin-top","15px");
		$("#reviewArrow").css("margin-top","170px");
		$("#sideBar").css("height",$("#pnlQuestion").css("height"));
	}
}

function showSubmitButton()
{
	if(reviewed==0)
    	$("#submitQuestion2").css("display","block");
	else
		$("#submitArrow").hide();
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
	
	//var h = parseInt($('#question').css("height"));
    var d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 120;

    //var e = d + h;
    /*$('#submitQuestion2').css("display","none");
	$('#nextQuestion2').css("display","block");
	$('#arrow').css("display","block");
	$('#submit_bar').css("display","block");*/
	
	//$('#question').css("height",e+"px");
	$('#pnlAnswer').css('height',d+'px');
	//$('#pnlAnswer').animate({'height':d+'px'},500);
	//alert($("#quesStem").height());
	$('#scroll').animate({
	   scrollTop: $("#quesStem").height() +45
	}, 'slow');
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

function toolbar(){
	if(click==0){
		$('#elements').fadeIn(500);
		$('#whiteContainer').fadeIn(500);
		$('#toolContainer').css("background-color","#033342");
		$('.toolbarText').css("color","#ffffff");
		click=1;
	}
	else if(click==1){
		$('#elements').fadeOut(500);
		$('#whiteContainer').fadeOut(500);
		$('#toolContainer').css("background-color","#ffffff");
		$('.toolbarText').css("color","#033342");
		click=0;
		return;
	}
}

function toolbar1(){
	$('#whiteContainer').fadeOut(500);
	click=0;
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
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
}
function markOption(userAns)
{
	$('.optionX').removeClass("optionInactive");
	$('#option'+userAns+' .optionX').addClass("optionActive");
}
function changeQuestion(){

}