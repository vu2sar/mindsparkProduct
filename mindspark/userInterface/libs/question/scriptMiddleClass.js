$(document).ready(function (e) {
    $(".forLowerOnly,.forHighestOnly").remove();
    if ($('#quesCategory').val() == "NCERT") {
        $('#sessionTime,#session').appendTo('#topic_ncert');
        $('#sessionTime').css('top', '115px');
        $('#session').css('top', '118px');
        $("#topic").remove();
    }
});

function adjustScreenElements() {
    if (window.innerHeight > 400 && !isAndroid && !isIpad) {
        $('#question').css("height", "");
        var infobarHeight = document.getElementById("info_bar").offsetHeight;
        var a = window.innerHeight - infobarHeight - 80 - 17;
        var b = window.innerHeight - infobarHeight - 80 - 17;
        $('#pnlQuestion').css("height", b);
        $('#scroll').css("height", a);
        $("body").css("overflow-y", "hidden");
        $("#hideShowBar,#showHide").css("display", "block");
        $('#pnlLoading').css("height", a);
        $("#confused").css("background", "url('assets/confused.png') no-repeat 0 0");
        $("#bored").css("background", "url('assets/bored.png') no-repeat 0 0");
        $("#excited").css("background", "url('assets/excited.png') no-repeat 0 0");
        $("#like").css("background", "url('assets/like.png') no-repeat 0 0");
        $("#dislike").css("background", "url('assets/dislike.png') no-repeat 0 0");
        $("#comment").css("background", "url('assets/comment.png') no-repeat 0 0");
    }
    else {
        $("#scroll").css("height", "auto");
        $("#pnlQuestion").css("height", "auto");
        $("#hideShowBar,#showHide").css("display", "none");
        $("body").css("overflow-y", "scroll");
    }
}
function showSubmitButton() {
    $("#submitQuestion").css("display", "block");
	if ($('#quesCategory').val() == "NCERT") {
    	$('#saveNCERTQuestion').css("display", "block");
    }
    showSkip('1');
    enableSubmitButton();
    $('#submit_bar').css("display", "block");
    $('#nextQuestion').css("display", "none");
    $('#arrow2').css("display", "none");
}
function showNextButton() {
    $('#submitQuestion').css("display", "none");
	$('#saveNCERTQuestion').css("display", "none");
    $('#nextQuestion').css("display", "block");
    $("#skipQuestion").css("display", "none");
    $('#mcqText').css("display", "none");
    disableSubmitButton();
    $('#arrow2').css("display", "block");
    $('#submit_bar').css("display", "block");
    
    // if question is of NCERT and it's last group submit then hide submit and next button
    if($(".groupNav").length == $(".complete").length && $(".groupNav").length > 0) {
		hideSubmitNextButton();
	}
}
function hideSubmitBar() {
    $('#submit_bar').css("display", "none");
    disableSubmitButton();
    $('#scroll').animate({
        scrollTop: 0
    }, 'slow');
}
function disableSubmitButton() {

}
function enableSubmitButton() {

}
function animateAnswerBox() {
    $('#pnlAnswer').css("display", "block");

	setTimeout(function(){
		var d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 85;
		$('#pnlAnswer').css('height', d + 'px');
	
		/*if($("#pnlRateDa").is(":visible"))
			$('#pnlAnswer').css('height', (d+15) + 'px');*/
	},100);
		
    if (isAndroid || isIpad) {
        $('body').animate({
            scrollTop: $("#quesStem").height() + 45 + $("#top_bar").height() + $("#info_bar").height()
        }, 'slow', function () {
            $('#arrow2').css("display", "block");
        });
    } else {
        $('#scroll').animate({
            scrollTop: $("#quesStem").height() + 45
        }, 'slow', function () {
            $('#arrow2').css("display", "block");
        });
    }
}

var hideToolBarTimeout;
function toolbar() {
    if (toolClick == 0) {

        $('#elements').fadeIn(500);
        $('#whiteContainer').fadeIn(500);
        toolClick = 1;
    }
    else if (toolClick == 1) {
        //clearTimeout(hideToolBarTimeout);
        $('#elements').fadeOut(500);
        $('#whiteContainer').fadeOut(500);
        $('#toolContainer').css("background-color", "#ffffff");
        $('.toolbarText').css("color", "#e52e00");
        toolClick = 0;
        return;
    }
}

function toolbar1(id) {
    var id = id;
   // $("#comment").css("background", "url('assets/comment.png') no-repeat 0 0");
    if (id == "confused") {
        $("#confused").css("background", "url('assets/confused.png') no-repeat 0 -54px");
        $("#bored").css("background", "url('assets/bored.png') no-repeat 0 0");
        $("#excited").css("background", "url('assets/excited.png') no-repeat 0 0");
        $("#dislike").css("background", "url('assets/dislike.png') no-repeat 0 0");
        $("#like").css("background", "url('assets/like.png') no-repeat 0 0");
    }
    else if (id == "bored") {
        $("#bored").css("background", "url('assets/bored.png') no-repeat 0 -54px");
        $("#excited").css("background", "url('assets/excited.png') no-repeat 0 0");
        $("#dislike").css("background", "url('assets/dislike.png') no-repeat 0 0");
        $("#like").css("background", "url('assets/like.png') no-repeat 0 0");
        $("#confused").css("background", "url('assets/confused.png') no-repeat 0 0");
    }
    else if (id == "excited") {
        $("#excited").css("background", "url('assets/excited.png') no-repeat 0 -54px");
        $("#bored").css("background", "url('assets/bored.png') no-repeat 0 0");
        $("#dislike").css("background", "url('assets/dislike.png') no-repeat 0 0");
        $("#like").css("background", "url('assets/like.png') no-repeat 0 0");
        $("#confused").css("background", "url('assets/confused.png') no-repeat 0 0");
    }
    else if (id == "like") {
        $("#like").css("background", "url('assets/like.png') no-repeat 0 -42px");
        $("#dislike").css("background", "url('assets/dislike.png') no-repeat 0 0");
        $("#bored").css("background", "url('assets/bored.png') no-repeat 0 0");
        $("#excited").css("background", "url('assets/excited.png') no-repeat 0 0");
        $("#confused").css("background", "url('assets/confused.png') no-repeat 0 0");
    }
    else if (id == "dislike") {
        $("#dislike").css("background", "url('assets/dislike.png') no-repeat 0 -42px");
        $("#like").css("background", "url('assets/like.png') no-repeat 0 0");
        $("#bored").css("background", "url('assets/bored.png') no-repeat 0 0");
        $("#excited").css("background", "url('assets/excited.png') no-repeat 0 0");
        $("#confused").css("background", "url('assets/confused.png') no-repeat 0 0");
    }
    var id1 = id;
    $('#elements').fadeOut(200);
    $('#whiteContainer').fadeOut(500);
    toolClick = 0;
}

function comment() {
    $('#commentBox').css("display", "block");
    $('#scroll').animate({
        scrollTop: 0
    }, 'slow');
    $("#comment").css("background", "url('assets/comment.png') no-repeat 0 -42px");
}
function openTextBox() {
    var Id = document.getElementById("selCategory");
    if (Id.selectedIndex != 0) {
        $('#textBox').css("display", "block");
    }
}
function cancel() {
    $('#commentBox').css("display", "none");
    $("#higherLevelClick").css("display", "none");
    $('#textBox').css("display", "none");
    $("#endSessionClick").css("display", "none");
    $("#endTopic,#blackScreen").css("display", "none");
}
function hideBar() {
    if (infoClick == 0) {
        $("#hideShowBar").text("+");
		if($("#quesCategory").val() == "daTest")
	        $("#showHide").html("");
		else
			$("#showHide").html("Show");
        $("#showHide").animate({ 'margin-top': '31px' }, 600);
        if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "wildcard" || $("#quesCategory").val() == "bonusCQ") {
            $('#topic_name').fadeOut(300);
        }
        else {
            $('#questionType').css("display", "none");
        }
        $('#topic_name').animate({ 'margin-top': '-10px' }, 600);
        $('#correct_bar').fadeOut(300);
        if (progressBarFlag)
            $('#percent').fadeIn(300);
        else
            $('#percent').fadeOut(300);
        $('#progress_text').fadeOut(300);        
        $('#changeTopic').fadeOut(300);
        $('#endSession').animate({ 'margin-top': '28px' }, 600);
        $('.class').animate({ 'margin-top': '10px', "font-size": "1.4em" }, 600);
        $('.Name').animate({ 'margin-top': '10px', "font-size": "1.4em" }, 600);
        $('#quitHigherLevel').fadeOut(300);
		$('#sessionTime').animate({ 'top': ($('#quesCategory').val()=="NCERT") ? '45px' : '41px', 'margin-left': '27%' }, 600); // position varies slightly in ncert page
        $('#session').animate({ 'top': ($('#quesCategory').val()=="NCERT") ? '48px' : '44px', 'margin-left': '40%' }, 600); // position varies slightly in ncert page
        $('#question_number').animate({ 'top': '44px', 'margin-left': '52%' }, 600);

        $('#info_bar').animate({ 'height': '60px' }, 600);
        if ($("#quesCategory").val() != "topicRevision" && $('#quesCategory').val() != "diagnosticTest")
            $('#progress_bar').animate({ 'margin-top': '5px' }, 600);
        var a = window.innerHeight - 130 - 27;
        $('#pnlQuestion').animate({ 'height': a }, 600);
        $('#scroll').css("height", a);
        var b = window.innerHeight - 257 - 200 - 17 - 140;
        var c = window.innerHeight - 80 - 137 - 80;
		if(c<300)
			c = 300;
        $('#question').animate({ 'min-height': c + "px" }, 600);
        infoClick = 1;
        if ($('#quesCategory').val() == "NCERT") {
            $('.icon_text11').animate({ 'margin-top': '15px', 'margin-left': '65px' }, 600);
        }
    }
    else if (infoClick == 1) {
        $("#hideShowBar").html("&ndash;");
        $("#showHide").html("Hide");
        $("#showHide").animate({ 'margin-top': '96px' }, 600);
        if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "wildcard" || $("#quesCategory").val() == "bonusCQ") {
            $('#topic_name').animate({ 'margin-top': '10px' }, 600);
            $('#topic_name').fadeIn(300);
            $('#questionType').fadeIn(600);
        }
        else {
            $('#questionType').css("display", "none");
            $('#topic_name').animate({ 'margin-top': '10px' }, 600);
        }

        $('#correct_bar').fadeIn(600);
        $('#percent').fadeIn(300);
        $('#progress_text').fadeIn(600);        
        $('#changeTopic').fadeIn(600);
        $('#endSession').animate({ 'margin-top': '50px' }, 600);
        $('.class').animate({ 'margin-top': '15px', "font-size": "1.8em" }, 600);
        $('.Name').animate({ 'margin-top': '15px', "font-size": "1.8em" }, 600);
        $('#quitHigherLevel').fadeIn(300);
		$('#sessionTime').animate({ 'top': ($('#quesCategory').val()=="NCERT") ? '115px' : '107px', 'margin-left': '10px' }, 600); // position varies slightly in ncert page
        $('#session').animate({ 'top': ($('#quesCategory').val()=="NCERT") ? '118px' : '110px', 'margin-left': '150px' }, 600); // position varies slightly in ncert page
        $('#question_number').animate({ 'top': '110px', 'margin-left': '290px' }, 600);
        if ($("#quesCategory").val() != "topicRevision" && $('#quesCategory').val() != "diagnosticTest")
            $('#progress_bar').animate({ 'margin-top': '20px' }, 600);
        $('#info_bar').animate({ 'height': '130px' }, 600);
        var a = window.innerHeight - 210 - 17;
        var b = window.innerHeight - 257 - 210 - 17 - 140;
        var c = window.innerHeight - 80 - 140 - 80 - 67;
        $('#pnlQuestion').animate({ 'height': a }, 600);
		if(c<300)
			c = 300;
        $('#question').animate({ 'min-height': c + "px" }, 600);
        $('#scroll').animate({ "height": a }, 600);

        infoClick = 0;
        if ($('#quesCategory').val() == "NCERT") {
            $('.icon_text11').animate({ 'margin-top': '45px', 'margin-left': '0px' }, 600);
        }
    }
}
function top1() {
    if (isAndroid || isIpad) {
        $('body').animate({
            scrollTop: 0
        }, 'slow');
    } else {
        $('#scroll').animate({
            scrollTop: 0
        }, 'slow');
    }

}
function markOption(userAns) {
    $('.optionX').removeClass("optionInactive");
    $('#option' + userAns + ' .optionX').addClass("optionActive");
}
function changeQuestion() {

}

// Added for NCERT module
function hideSubmitNextButton()
{
	$('#submitQuestion').hide();
	$('#saveNCERTQuestion').hide();
	disableSubmitButton();
	$('#nextQuestion').hide();
	$('#arrow2').hide();
}