$(document).ready(function (e) {
    $(".forHigherOnly").remove();
});


function adjustScreenElements() {
    if (window.innerHeight > 400 && !isAndroid && !isIpad) {
        $('#question').css("height", "");
        var b = window.innerHeight - (170);
        var a = window.innerHeight - (65);
        $('#scroll').css("height", b + "px");
        $('#pnlQuestion').css({ "height": a + "px", "display": "block" });
        if (document.getElementById("q2").offsetHeight < 210) {
            var c = window.innerHeight - 34 - 17 - 180 - 70;
            $('#quesStem').css("height", c + "px");
        }
        else {
            $('#quesStem').css("height", "auto");
        }
        if ($("#pnlOptions").css("display") == "block") {
            $("#submitQuestion1").css("display", "none");
        }
    }
    else {
        if (window.innerHeight > 900) {
            var a = window.innerHeight - (170);
            $("#scroll").css("min-height", a + "px");
        }
        else {
            $("#scroll").css("min-height", "332px");
            $("#scroll").css("height", "auto");
        }
        $("#question").css("height", "auto");
        $('#pnlQuestion').css({ "height": "auto", "display": "block" });
        if ($("#pnlOptions").css("display") == "block") {
            $("#submitQuestion1").css("display", "none");
        }
    }
}
function showSubmitButton() {
    $("#submitQuestion1").css("display", "block");
    showSkip('2');
    $('#scroll').animate({
        scrollTop: 0
    }, 'slow');
    enableSubmitButton();
    $('#submit_bar1').css("display", "block");
    $('#nextQuestion1').css("display", "none");
}
function showNextButton() {
    $('#submitQuestion1').css("display", "none");
    $('#skipQuestion1').css("display", "none");
    $('#nextQuestion1').css("display", "block");
    disableSubmitButton();
    $('#mcqText').css("display", "none");
    $('#submit_bar1').css("display", "block");
}
function hideSubmitBar() {
    $('#submit_bar1').css("display", "none");
    disableSubmitButton();
}
function disableSubmitButton() {

}
function enableSubmitButton() {

}
function animateAnswerBox() {
    $('#pnlAnswer').css("display", "block");
	setTimeout(function(){
        var d = document.getElementById("pnlDisplayAnswerContainer").offsetHeight + 50;
        $('#pnlAnswer').css('height', d + 'px');
    
        /*if($("#pnlRateDa").is(":visible"))
            $('#pnlAnswer').css('height', (d+50) + 'px');*/
    },100);	

    if (isAndroid || isIpad) {
        $('#scroll').css("height", "auto");
        $('body').animate({
            scrollTop: $("#quesStem").height() + 25 + $("#top_bar").height() + $("#info_bar").height() + 50
        }, 'slow', function () {
        });
    } else {
        $('#scroll').animate({
            scrollTop: $("#quesStem").height() + 25
        }, 'slow', function () {
            //$('#nextQuestion1').css("display","block");
        });
    }

    $('#submitQuestion1').css("display", "none");
    $('#skipQuestion1').css("display", "none");
    $('#submit_bar1').css("display", "block");
    $("#question").height($("#quesStem").height() + $("#pnlAnswer").height());
}

function hideBar() {

}
var hideToolBarTimeout;
function toolbar() {
    if (dragging == 0) {
        if (toolClick == 0) {
            $("#close").css("display", "none");
            $("#open,.closeToolbar").css("display", "block");
            toolClick = 1;
            //clearTimeout(hideToolBarTimeout);
            //hideToolBarTimeout = setTimeout(function(){
            //	if(toolClick==1)
            //		toolbar();
            //},5000);
        }
        else if (toolClick == 1) {
            //clearTimeout(hideToolBarTimeout);
         /*   $("#open,.closeToolbar").css("display", "none");*/
            $("#close").css("display", "block");
            toolClick = 0;
        }
    }

}
function changeQuestion() {

}
function cancel() {
    $('#commentBox').css("display", "none");
    $("#higherLevelClick").css("display", "none");
    //Id=document.getElementById("DropDown1");
    //Id.selectedIndex = 0;
    $('#textBox').css("display", "none");
    $("#endSessionClick").css("display", "none");
    $("#endTopic,#blackScreen").css("display", "none");
}
function comment() {
    $('#commentBox').css("display", "block");
    $('#scroll').animate({
        scrollTop: 0
    }, 'slow');
}

function markOption(userAns) {
    $('.optionX').removeClass("optionInactive");
    $('#option' + userAns + ' .optionX').addClass("optionActive");
}