$(document).ready(function(e) {
    $(".forLowerOnly").remove();
});

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
		//$('#scroll').jScrollPane({showArrows: true});
		/*if(window.innerHeight>700)
		{
			var b= window.innerHeight -257 -infobarHeight -17 -140 ;
			$('#submit_bar').css("margin-top",b);
		}*/
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
    $("#submitQuestion").css("display","block");
     enableSubmitButton();
    $('#submit_bar').css("display","block");
    $('#nextQuestion').css("display","none");
    $('#arrow2').css("display","none");
}
function showNextButton()
{	
    $('#submitQuestion').css("display","none");
	$('#nextQuestion').css("display","block");
	$('#mcqText').css("display","none");
    disableSubmitButton();
    $('#arrow2').css("display","block");
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
	if(objQuestion.quesType == 'I')
		$('#quesStem').css('pointer-events', 'none');
	
	//var h = parseInt($('#question').css("height"));
    var d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 165;

    //var e = d + h;
    /*$('#submitQuestion').css("display","none");
	$('#nextQuestion').css("display","block");
	$('#arrow').css("display","block");
	$('#submit_bar').css("display","block");*/
	
	//$('#question').css("height",e+"px");
	$('#pnlAnswer').css('height',d+'px');
	//$('#pnlAnswer').animate({'height':d+'px'},500);
	//alert($("#quesStem").height());
	
	if(isAndroid){
		$('body').animate({
		    scrollTop: $("#quesStem").height() +45+$("#top_bar").height()+$("#info_bar").height()
			}, 'slow',function(){
				//$('#nextQuestion').css("display","block");
				$('#arrow2').css("display","block");
		});
	}else{
		$('#scroll').animate({
		   scrollTop: $("#quesStem").height() +45
			}, 'slow',function(){
				//$('#nextQuestion').css("display","block");
				$('#arrow2').css("display","block");
		});
	}
	
	/*$('#scroll').jScrollPane({showArrows: true});
	var element = $('#scroll').jScrollPane();
	var api = element.data('jsp');
	api.scrollToElement($('#pnlAnswer'), true, true);*/
}

var hideToolBarTimeout;
function toolbar(){
	if(toolClick==0){
		
		$('#elements').fadeIn(500);
		$('#whiteContainer').fadeIn(500);
		toolClick=1;
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
		$('#whiteContainer').fadeOut(500);
		$('#toolContainer').css("background-color","#ffffff");
		$('.toolbarText').css("color","#e52e00");
		toolClick=0;
		return;
	}
}

function toolbar1(id){
	var id=id;
	$("#comment").css("background","url('assets/comment.png') no-repeat 0 0");
	if(id=="confused"){
		$("#confused").css("background","url('assets/confused.png') no-repeat 0 -54px");
		$("#bored").css("background","url('assets/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/excited.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/dislike.png') no-repeat 0 0");
		$("#like").css("background","url('assets/like.png') no-repeat 0 0");
	}
	else if(id=="bored")
	{
		$("#bored").css("background","url('assets/bored.png') no-repeat 0 -54px");
		$("#excited").css("background","url('assets/excited.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/dislike.png') no-repeat 0 0");
		$("#like").css("background","url('assets/like.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/confused.png') no-repeat 0 0");
	}
	else if(id=="excited")
	{
		$("#excited").css("background","url('assets/excited.png') no-repeat 0 -54px");
		$("#bored").css("background","url('assets/bored.png') no-repeat 0 0");
		$("#dislike").css("background","url('assets/dislike.png') no-repeat 0 0");
		$("#like").css("background","url('assets/like.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/confused.png') no-repeat 0 0");
	}
	else if(id=="like")
	{
		$("#like").css("background","url('assets/like.png') no-repeat 0 -42px");
		$("#dislike").css("background","url('assets/dislike.png') no-repeat 0 0");
		$("#bored").css("background","url('assets/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/excited.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/confused.png') no-repeat 0 0");
	}
	else if(id=="dislike")
	{
		$("#dislike").css("background","url('assets/dislike.png') no-repeat 0 -42px");
		$("#like").css("background","url('assets/like.png') no-repeat 0 0");
		$("#bored").css("background","url('assets/bored.png') no-repeat 0 0");
		$("#excited").css("background","url('assets/excited.png') no-repeat 0 0");
		$("#confused").css("background","url('assets/confused.png') no-repeat 0 0");
	}
	var id1=id;
	$('#elements').fadeOut(200);
	$('#whiteContainer').fadeOut(500);
	toolClick=0;
}

function comment(){
	$('#commentBox').css("display","block");
	$('#scroll').animate({
	   scrollTop: 0
	}, 'slow');
	$("#comment").css("background","url('assets/comment.png') no-repeat 0 -42px");
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
	$("#higherLevelClick").css("display","none");
	//Id=document.getElementById("DropDown1");
	//Id.selectedIndex = 0;
	$('#textBox').css("display","none");
	$("#endSessionClick").css("display","none");
	$("#endTopic").css("display","none");
}
function hideBar(){
	if (infoClick==0){
		$("#hideShowBar").text("+");
		$("#showHide").html("Show");
		$("#showHide").animate({'margin-top':'31px'},600);
		if($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "wildcard")
		{
            $('#topic_name').fadeOut(300);
		}
		else
		{
			$('#questionType').css("display","none");
		}
		$('#topic_name').animate({'margin-top':'-10px'},600);
		$('#correct_bar').fadeOut(300);
        if($('#letsPractice').css("display")=="block")
            $('#letsPractice').fadeOut(300);
		$('#percent').fadeOut(300);
		$('#progress_text').fadeOut(300);
		$('#changeTopic').fadeOut(300);
		$('#endSession').animate({'margin-top':'28px'},600);
		$('.class').animate({'margin-top':'10px',"font-size":"1.4em"},600);
		$('.Name').animate({'margin-top':'10px',"font-size":"1.4em"},600);
		$('#quitHigherLevel').fadeOut(300);
		
		/*$('#session').animate({'top':'44px','margin-left':'65px'},600);
		$('#question_number').animate({'top':'44px','margin-left':'235px'},600);*/
		
		$('#session').animate({'top':'44px','margin-left':'300px'},600);
		$('#question_number').animate({'top':'44px','margin-left':'460px'},600);
		
		$('#info_bar').animate({'height':'60px'},600);
		if($("#quesCategory").val()!="topicRevision")
			$('#progress_bar').animate({'margin-top':'5px'},600);
		//$('#topic_name').animate({'margin-top':'-10px'},600);
		var a= window.innerHeight -130 -27;
		$('#pnlQuestion').animate({'height':a},600);
		$('#scroll').css("height",a);
		//$('#scroll').jScrollPane({showArrows: true});
		var b= window.innerHeight -257 -200 -17 -140;
		//$('#submit_bar').animate({'margin-top':b},600);
		var c= window.innerHeight - 80 - 137 - 80;
		$('#question').animate({'min-height':c+"px"},600);
		infoClick=1;
		if($('#quesCategory').val()=="NCERT"){
			$('.icon_text11').animate({'margin-top':'15px','margin-left':'65px'},600);
		}
	}
	else if(infoClick==1){
		$("#hideShowBar").html("&ndash;");
		$("#showHide").html("Hide");
		$("#showHide").animate({'margin-top':'96px'},600);
		if($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "wildcard")
		{
			$('#topic_name').animate({'margin-top':'10px'},600);
			$('#topic_name').fadeIn(300);
            $('#questionType').fadeIn(600);
		}
		else
		{
			$('#questionType').css("display","none");
			$('#topic_name').animate({'margin-top':'10px'},600);
		}
	
        if($('#tmpMode').val()=="practice")
            $('#letsPractice').fadeIn(600);             //End check
		$('#correct_bar').fadeIn(600);
		$('#percent').fadeIn(300);
		$('#progress_text').fadeIn(600);
		$('#changeTopic').fadeIn(600);
		$('#endSession').animate({'margin-top':'50px'},600);
		$('.class').animate({'margin-top':'15px',"font-size":"1.8em"},600);
		$('.Name').animate({'margin-top':'15px',"font-size":"1.8em"},600);
		$('#quitHigherLevel').fadeIn(300);
		$('#session').animate({'top':'110px','margin-left':'10px'},600);
		$('#question_number').animate({'top':'110px','margin-left':'180px'},600);
		if($("#quesCategory").val()!="topicRevision")
			$('#progress_bar').animate({'margin-top':'20px'},600);
		$('#info_bar').animate({'height':'130px'},600);
		//$('#topic_name').animate({'margin-top':'10px'},600);
		var a= window.innerHeight -210 -17;
		var b= window.innerHeight -257 -210 -17 -140;
		var c= window.innerHeight - 80 - 140 - 80 - 67;
		$('#pnlQuestion').animate({'height':a},600);
		$('#question').animate({'min-height':c+"px"},600);
		$('#scroll').animate({"height":a},600);
		//$('#scroll').jScrollPane({showArrows: true});
		//$('#submit_bar').animate({'margin-top':b},600);
		
		infoClick=0;
		if($('#quesCategory').val()=="NCERT"){
			$('.icon_text11').animate({'margin-top':'45px','margin-left':'0px'},600);
		}
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