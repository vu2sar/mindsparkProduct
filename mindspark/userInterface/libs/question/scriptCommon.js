var toolClick = 0;
var click = 0;
var infoClick = 0;
var isLoadedDAQue = true;
var secs, secsTaken = 0;
var plstart = new Date();
var timerID = null;
var timerRunning = false;
var slowLoadTimer;
var toughQues = 0;
var isTough = "";
var noSubmit = 0;
var emotID = "";
var prevQcode = ""; // For EmotToolBar, Checkbox for 'My answer was marked wrong' & NCERT Exercise..
var prevQno = "";
var prevDynamicParams = "";
var prevQuesCategory = "";
var prevQuesSubCategory = "";
var prevQcodeAfterNext = ""; // For Student comment on prevquestions
var prevQuesCategoryAfterNext = "";
var prevQuesSubCategoryAfterNext = "";
var prevDynamicParamsAfterNext = "";
var prevQuesHtml = "";
var refreshProgessBar = 0;
var commenttype = "";
var prevQuesClone = "";
var sdlAttemptArr = new Array();
var clusterAttemptArr = new Array();
var clusterNameAttemptArr = new Array();
var topicAttemptArr = new Array();
var clusterStatusPrompt = 0;
var previousCluster;
var preFetchedAnswer;
var allowed = 1; //For concecutive enter stop the enter key press till scroll part complete 1 - allow , 0 - cancel (Submit & Next Question)
var checkFlag = "false";
var previousCommonError="";
var allowCommonError=true;
var submitCheck = 0;
var giveResult = 0;
var redirect = 1;
var counter = 0;
var etTimer = null;
var glossaryDescArray = new Array();
var autoHideDisplayBar;
var emoteToolbarTagCount = 0;
var noOfCondition = 0;
var conCheck = 0;
var condition;
var action;
var ano = 1;
var totalAttempts;
var timeToAnswer = 0; //used for hints 
var timeToAnswerID;
var userAllAnswers = '';
var dispalAnswerParam = "";
var timeTakenToViewHintID, timeTakenForAllHintStr;
var timeTakenToViewHint = 0;
var timeTakenForAllHints = new Array();
var questionsDoneEC = 0;
var quesCorrectEC = 0;
var ua = navigator.userAgent.toLowerCase();
var isAndroid = ua.indexOf("android") > -1;
var isIpad = ua.indexOf("ipad") > -1;
var isChrome = ua.indexOf("chrome") > -1;
var newQues, objNextQuestion;
var objFrustration = new frustrationModel();
var toughQuestionStatus = 0;
var toughQuestionStatus1 = 0;
var globalProcessResult = "";
var trialTough = 0;
var submitBlocked = localStorage.getItem("blockSubmit");
var globalProcessquesType = "";
var previouslyPrompted = localStorage.getItem("previousPrompted");
var captureResets = localStorage.getItem("captureReset");
var quesNoInFlow = 0;
var timeTakenAttemptArr = new Array(0, 0, 0, 0, 0);
var timeTakenExplainationArr = new Array(0, 0, 0, 0, 0);
var sparkieForExplainationFlag = 0;
var sparkieCount = 0;
var tryNo = 0;
var backToRemediationCluster = 0;
var practicClusterFlag = 0;
var currentCluster = "";
var nameOfCluster = '';
var quesAttempted, quesCorrect1, progressInTopic, lowerLevel1;
var diagnosticTestComplete = 0;
var keypadPresent = 0;
var ajaxFailure = true;
var dragging = 0;
var JSerrors = "", responseArray = "", retryNo = 0;
var atHigherLevel = 0;
var toughQuesInstances = 0, toughQuesInstancesConsecutive = 0, timeTakenToughAlert = 0, timeTakenToughAttempt = 0, timerIDTough, toughDisabled = 0, toughFlag = 0;
var message="Sorry, right-click has been disabled";
var optionDiagnosticArray = new Array();
var choiceScreen=null;
var iycAsChoice=false;
var practiseModuleStatus=1;
var practiseModuleTimeStatus=0;
var ddLevelsAttempted=null;
var displayAnsRating;
var timedTestPrompt=false;
var timedPromptTimer=null;
var autoPreSavedAnswer = "";
var questionStatus = '';
var screenState = 'normal';
var constrTool = {};
var sparkieTooltip = {
    text: {
        normal: '',
        challenge: 'This is your __challengeQuestion.attemptNumber__ attempt on the challenge question. You can earn __challengeQuestion.sparkies__ sparkies on correct attempt',
        wildcard: 'You can earn 1 sparkie on correct attempt of this wildcard question',
        bonusCQ: 'You have earned this Bonus challenge question! You can earn 5 sparkies on correct attempt',
        topicRevision: 'You can get a maximum of 5 sparkies if you do well.',
    },
    autoShow: true,
};
if (document.addEventListener)
    document.addEventListener("keydown", my_onkeydown_handler, true);
else if (document.attachEvent)
    document.attachEvent("onkeydown", my_onkeydown_handler);
if(!isIpad)
{
    window.history.forward(1);    
}

document.onkeypress = checkKeyPress;
if (document.layers) document.captureEvents(Event.KEYPRESS);

function noenter(e) {
    e = e || window.event;
    var key = e.keyCode || e.charCode;
    return key !== 13;
}

function checkLoadingComplete() {
    var currForm = document.images;
    var flag = true;
    for (var eLoop = 0; eLoop < currForm.length; eLoop++) {
        if (currForm[eLoop].complete == false)
            flag = false;
        if (typeof currForm[eLoop].naturalWidth != "undefined" && currForm[eLoop].naturalWidth == 0)
            flag = false;
    }
    if (flag) {
        var plend = new Date();
        // calculate the elapsed time between the start and the end. // This is in milliseconds
        if (plstart != null)
            plstart = new Date(plstart);
        else
            plstart = plend;
        var plload = (plend.getTime() - plstart.getTime()) / 1000;
        if (document.getElementById('pageloadtime'))
            $('#pageloadtime').val(plload);
        initializeTimer();
        adjustScreenElements();
    }
    else
        window.setTimeout("checkLoadingComplete()", 500);
}

window.onload = function () {
    var previousKey, key;
    $("#nextQuestion").click(function(){
      $("input[name=emotRespond").prop('checked', false);
      $("#like, #dislike, #comment").attr('style', '');
      if($("#quesCategory").val() == "practiseModule")
            $("#comment").attr('style','margin-top:8px');
    });

    $("#commentCancel2").click( function(){
            $("#comment").css("background", "url('assets/comment.png') no-repeat 0 0px");
        });
    $("#commentCancel3").click( function(){
           $("#comment").css("background","url('assets/higherClass/question/comment.png') no-repeat 0 0px");
        });
    $('input[type=text]').live('paste', function (e) {
        e.preventDefault();
        if(!$(".promptContainer").is(":visible"))
        {
            var prompts = new Prompt({
                text: 'Copy paste not allowed ',
                type: 'alert',
                promptId: "copyPaste",
                func1: function () {
                    jQuery("#prmptContainer_copyPaste").remove();
                }
            });
        }
        $('#promptBox').css('min-height', '100px');
        $('.butt1').focus();
    });
    $("input[type=text]").live("keyup", function (e) {
        previousKey = 0;
    });


    $("input[type=text]").live("input", function () {
        var previousValue = $(this).data('old_value') || '',
            newValue = $(this).val();

        if ((newValue.length - previousValue.length) > 1) {
            $(this).val(previousValue);
        }

        $(this).data('old_value', $(this).val());
    });
    if($("#quesCategory").val() == "practiseModule"){//} || $("#quesCategory").val() == "worksheet"){
        hideBar();
        $("#hideShowBar").remove();
        $("#showHide").remove();
    }
    if (window.location.hash.length == 0) {
        window.location.hash = 'Mindspark';
        window.location.hash = 'Loading';
        if ($("#quesCategory").val() == "topicRevision" || $('#quesCategory').val() == "diagnosticTest" || $('#quesCategory').val() == "kstdiagnosticTest" ) {
            $("#progress_bar").hide();
        }        
        if (!isAndroid && !isIpad) {
            autoHideDisplayBar = setTimeout(function () {
                if (infoClick == 0)
                    hideBar();
                else
                    clearTimeout(autoHideDisplayBar);
            }, 5000);
        }
        var category = $('#quesCategory').val();
        if(category == 'practiseTest')
        {
            var qcode = document.getElementById('qcode').value;
            params = "qcode="+qcode+"&mode=firstQuestion&quesCategory=" + category + "&qno=" + qno;
        }
        if(category == 'practiseModule')
        {
            var currentLevel ;
            var practiseModuleTestStatusId = document.getElementById('practiseModuleTestStatusId').value;
            var fromPractisePage = document.getElementById('fromPractisePage').value;
            var attemptNo = document.getElementById('attemptNo').value;
            
            params = "mode=firstQuestion&quesCategory=" + category + "&practiseModuleTestStatusId=" + practiseModuleTestStatusId +"&attemptNo="+attemptNo;
            iTargetSpeed = $("#iTargetSpeed").val();
            if(fromPractisePage == 0){
                $("#time-div").show();
            }
            draw(0);
            drawWithInputValue(iTargetSpeed);
            $("#currentScore").html(iTargetSpeed);
            if(iTargetSpeed == 0){
                $(".info-content").show("slow");
                $(".info-sign").attr("id","-");
            }
            /*if(iTargetSpeed>=100){
                setDdParameters(iTargetSpeed);
            }*/
            
        }
        else if(category == 'worksheet')
        {
            
            var qno = $('#qno').val();
            var paperQueCount = $('#daTestQueCount').val();
            var worksheetID = $('#worksheetID').val();
            var worksheetAttemptID = $('#worksheetAttemptID').val();
            var maxAttemptQno = $('#maxAttemptQno').val();

            for (i = maxAttemptQno; i <= parseInt($('#daTestQueCount').val()); i++) { 
                if(i == maxAttemptQno)
                {
                    $("#"+i+"box").removeClass('daPagingnormal');
                    $("#"+i+"box").addClass('daPagingBlue');
                    $('#daQcodeListBox').attr('data-showing',Math.floor((i-1)/10));
                }
                else
                {
                    $("#"+i+"box").removeClass('daPagingGrey');
                    $("#"+i+"box").addClass('daPagingnormal');
                }
            }
            //$('#quesCategory').val()
            $('#pre_box,#next_box').removeClass('inactive');
            var newPosDANav=$('#daQcodeListBox').attr('data-showing')*10*-40;
            if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-min')){
                $('#pre_box').addClass('inactive');newPosDANav=0;
            }
            if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-max')){
                $('#next_box').addClass('inactive');
                newPosDANav=($('#daQcodeListBox').attr('data-showing')!=$('#daQcodeListBox').attr('data-min'))?$('#daQcodeListBox').attr('data-quesCount')*-40+400:0;
            }
            $('#daQcodeListBox table').css('left',(newPosDANav)+'px');

            params = "qcode=0&mode=firstQuestion&quesCategory=" + category + "&qno=" + qno+"&worksheetID="+worksheetID+"&worksheetAttemptID="+worksheetAttemptID+"&paperQueCount="+paperQueCount;
        }
        else if(category == 'daTest')
        {
            
            var qno = $('#qno').val();
            var paperQueCount = $('#daTestQueCount').val();
            var daTestCode = $('#daPaperCode').val();
            var maxAttemptQno = $('#maxAttemptQno').val();

            for (i = 1; i < maxAttemptQno; i++) { 
                $("#"+i+"box").addClass('daPagingGrey');
                $("#"+i+"box").removeAttr('onclick'); 
            }
            
            for (i = maxAttemptQno; i <= parseInt($('#daTestQueCount').val()); i++) { 
                if(i == maxAttemptQno)
                {
                    $("#"+i+"box").removeClass('daPagingnormal');
                    $("#"+i+"box").addClass('daPagingBlue');
                    $('#daQcodeListBox').attr('data-showing',Math.floor((i-1)/10));
                }
                else
                {
                    $("#"+i+"box").removeClass('daPagingGrey');
                    $("#"+i+"box").addClass('daPagingnormal');
                }
            }
            //$('#quesCategory').val()
            $('#pre_box,#next_box').removeClass('inactive');
            var newPosDANav=$('#daQcodeListBox').attr('data-showing')*10*-40;
            if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-min')){
                $('#pre_box').addClass('inactive');newPosDANav=0;
            }
            if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-max')){
                $('#next_box').addClass('inactive');
                newPosDANav=($('#daQcodeListBox').attr('data-showing')!=$('#daQcodeListBox').attr('data-min'))?$('#daQcodeListBox').attr('data-quesCount')*-40+400:0;
            }
            $('#daQcodeListBox table').css('left',(newPosDANav)+'px');

            params = "qcode=0&mode=firstQuestion&quesCategory=" + category + "&qno=" + qno+"&daTestCode="+daTestCode+"&paperQueCount="+paperQueCount;
        }
        else
        { 
            params = "qcode=0&mode=firstQuestion&quesCategory=" + category + "&qno=" + qno;
        }
        getNextQues(params, "normal");
        refreshProgessBar = 1;
        fetchNextQues();
        if (document.getElementById('ichar'))
            buddyinit();
        if (category == "NCERT") {
            $("#topic_ncert").css("display", "block");
            $(".bubble").css("display", "none");
            $("#chart").css("display", "block");
            $("#boxRed").css("display", "block");
            $("#boxGreen").css("display", "block");
            $(".pending").css("display", "inline-block");
            $(".complete").css("display", "inline-block");
            $(".PS").css("display", "block");
            $("#nextQuestion").css("display", "block");
            $("#submitQuestion").css("display", "none");
            $("#submitQuestion2").css("display", "none");
            $("#skipQuestion").css("display", "none");
            $("#skipQuestion2").css("display", "none");

            $("#question").css("margin-top", "0px");
            $("#toolContainer").css("margin-top", "150px");
        }
    }
    else {
        var request = $.ajax('controller.php',
        {
            type: 'post',
            data: "mode=back_refresh",
            "async": false,
            success: function (transport) {
                //do nothing;
            }
        });
        setTryingToUnload();
        window.location = "error.php";
    }
}
window.onhashchange = function () {
    window.location.hash = 'Mindspark';
}

function getDAQuestionDirect(qno,qcode)
{
    var prevQ = $('#qno').val();
    if(isLoadedDAQue === false || qno == prevQ)
    {
        return false;
    }
    else
    {
        isLoadedDAQue = false;
        if($("#daFlagCheck").attr('checked'))
        {
            $("#"+prevQ+"box").addClass("daPagingYellow");
        }
    
        if ($("#"+qno+"box").hasClass("daPagingYellow")) {
             $('#daFlagCheck').attr('checked', true);
             $("#prevDAQuesFlag").val(1);
        }else
            $('#daFlagCheck').attr('checked', false);
    
        var data = $('.optionActive').text();
        var AttemptQno = $('#qno').val();
        var daQcode = $('#qcode').val();
        var ans = data;
        if(ans=="")
            ans = "No Answer";
        var result = newQues.checkAnswerMCQ(ans);

        prevQno = $('#qno').val();

        var paperQueCount = $('#daTestQueCount').val();
        var daTestCode = $('#daPaperCode').val();
        if(!$( "#"+qno+"box" ).hasClass("daPagingnormal"))
        {
            setDaColorCode(prevQno,qno,'no');
            $(".optionX").removeClass( "optionActive" );
            params = "qcode="+qcode+"&mode=firstQuestion&quesCategory=daTest&qno="+qno+"&linkedquestion=1"+"&daTestCode="+daTestCode+"&paperQueCount="+paperQueCount;
            getNextQues(params, "normal");
            refreshProgessBar = 1;
            fetchNextQues();
        }
        isLoadedDAQue = true;
    }
}
function getWSQuestionDirect(qno,qcode)
{
    var prevQ = $('#qno').val();
    if(isLoadedDAQue === false || qno == prevQ)
    {
        return false;
    }
    else
    {
        isLoadedDAQue = false;
        prevQno = $('#qno').val();
        var paperQueCount = $('#daTestQueCount').val();
        var worksheetID = $('#worksheetID').val();
        var worksheetAttemptID = $('#worksheetAttemptID').val();
        //if(!$( "#"+qno+"box" ).hasClass("daPagingnormal"))
        {
            //setDaColorCode(prevQno,qno,'no');
            $('#getDirectQuestion').val(qno);
            submitAnswer();
            /*$(".optionX").removeClass( "optionActive" );
            params = "qcode="+qcode+"&mode=firstQuestion&quesCategory=worksheet&qno="+qno+"&linkedquestion=1"+"&worksheetID="+worksheetID+"&worksheetAttemptID="+worksheetAttemptID+"&paperQueCount="+paperQueCount;
            getNextQues(params, "normal");
            refreshProgessBar = 1;
            fetchNextQues();*/
        }
    }
}

function refreshScrollBar() {
    var pane = $('#scroll').jScrollPane({ showArrows: true, arrowSize: 17, autoReinitialise: true }).data('jsp');
    pane.reinitialise();
}

function sample()
{
    $("#prompt").css("height","130%"); 
    $('#prompt').css('display','none');
}

function openWildcardInstruction()
{  
    $.fn.colorbox({'href':'#instruction','inline':true,'open':true,'escKey':true, 'height':350, 'width':500});
}
    
function clusterStatusPromptfn()
{  
    $.fn.colorbox({'href':'#clusterStatusPrompts','inline':true,'open':true,'escKey':true, 'height':250, 'width':450});
}

function openHelp(theme, baseurl)
{
    var k = window.innerWidth;
    if(theme==1) {
        var helpSource= baseurl+"theme1/index.html";
    }
    else if(theme==2){
        var helpSource= baseurl+"theme2/index.html";
    }
    else if(theme==3) {
        var helpSource= baseurl+"theme3/index.html";
    }
    if(k>1024)
    {
        $("#iframeHelp").attr("height","440px");
        $("#iframeHelp").attr("width","960px");
        $("#iframeHelp").attr("src",helpSource);
        $.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':570, 'width':1024});
    }
    else
    {
        $("#iframeHelp").attr("height","390px");
        $("#iframeHelp").attr("width","700px");
        $("#iframeHelp").attr("src",helpSource);
        $.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':500, 'width':800});
    }
}

function openHelp1(theme, baseurlProgressBar){
    var k = window.innerWidth;
    var helpSource= baseurlProgressBar+"interface"+theme+"/TPDemoInterface/index.html";
    if(k>1024)
    {
        $("#iframeHelp").attr("height","440px");
        $("#iframeHelp").attr("width","960px");
        $("#iframeHelp").attr("src",helpSource);
        $.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':570, 'width':1024});
    }
    else
    {
        $("#iframeHelp").attr("height","390px");
        $("#iframeHelp").attr("width","700px");
        $("#iframeHelp").attr("src",helpSource);
        $.fn.colorbox({'href':'#openHelp','inline':true,'open':true,'escKey':true, 'height':500, 'width':800});
    }
}

$(document).ready(function (e) {

    document.onkeypress = checkKeyPress;
    
    if (document.layers) document.captureEvents(Event.KEYPRESS);
   
    
    if (document.layers)
    {
        document.captureEvents(Event.MOUSEDOWN);
        document.onmousedown=clickNS;
    }
    else
    {
        document.onmouseup=clickNS;
        document.oncontextmenu=clickIE;
    }
    document.oncontextmenu=new Function("return false");
    
    window.onerror = function (e, url, line) {
        JSerrors = 'onerror: ' + e + ' URL:' + url + ' Line:' + line;
    }

    if (localStorage.getItem("toughDisabled") == 'true')
        toughDisabled = 1;

    $("input[type='text']").live("focus", function () {
        $(this).removeClass("incorrectblank");
    });
    if (progressBarFlag) {
        $("#showProgressBar").live("click", function () {
            if ($('#progressOverlay').css('display') == 'none') {
                $("#showProgressBar").html('-');
                $('#progressOverlay').css('display', 'block');
                $('.arrow-top').css('display', 'block');
            }
            else {
                $("#showProgressBar").html('+');
                $('#progressOverlay').css('display', 'none');
                $('.arrow-top').css('display', 'none');
            }
        });
    }
    $("#wildcardImg").live("click", function () {
		if($('#quesCategory').val()=='wildcard')
		{
			$("#wildcardInfo").dialog({
				width: "400px",
				position: "right",
				draggable: false,
				resizable: false,
				modal: true
			});
		}
    });

    $(".eqEditorToggler").live("click", function () {
        $(this).parents("td:first").next().find(".eqEditorConatiner:first").toggle("slow");
    });
    //Pending Integration
    $(".ui-widget-overlay").live("click", function () {
        $("#wildcardInfo,#commentInfo").dialog("close");
        $("#commentInfo").html("");
    });
    $("#wildcardInfo").live("click", function () {
        $("#wildcardInfo,#commentInfo").dialog("close");
        $("#commentInfo").html("");
    });
    $("#ui-dialog-titlebar").live("click", function () {
        $("#wildcardInfo,#commentInfo").dialog("close");
        $("#commentInfo").html("");
    });
    
    $("#cboxClose").click(function(){
        if($("#commentInfo").is(":visible"))
        {
            $("#commentInfo").html("");
            removeHoverClass();
            $('#txtcomment').val("");
            $('#selCategory').val("");
            $("#commentOn1").attr("checked", true);
        }
    });


    $(document).delegate('#pre_box:not(.inactive),#next_box:not(.inactive)','click',function(){
        var curPage=$('#daQcodeListBox').attr('data-showing')*1;
        var minPage=$('#daQcodeListBox').attr('data-min')*1;
        var maxPage=$('#daQcodeListBox').attr('data-max')*1;
        switch($(this).attr('id')){
            case 'pre_box':
                if (curPage==minPage) break;
                curPage--;
                break;
            case 'next_box':
                if (curPage==maxPage) break;
                curPage++;
                break;
        }
        $('#daQcodeListBox').attr('data-showing',curPage);
        $('#pre_box,#next_box').removeClass('inactive');
        var newPosDANav=$('#daQcodeListBox').attr('data-showing')*10*-40;
        if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-min')){
            $('#pre_box').addClass('inactive');newPosDANav=0;
        }
        if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-max')){
            $('#next_box').addClass('inactive');
            newPosDANav=($('#daQcodeListBox').attr('data-showing')!=$('#daQcodeListBox').attr('data-min'))?$('#daQcodeListBox').attr('data-quesCount')*-40+400:0;
        }
        $('#daQcodeListBox table').css('left',(newPosDANav)+'px');
    });

    //-----ends here
    
    $(".groupNav").click(function (e) {
        var groupNo = $(this).attr("id").replace(/groupNav/g, "");
        if (!$(this).hasClass("current")) {
            allowed = 1;
            if (!$("#submitQuestion").is(":visible")) {
                $('#userResponse').val("-1");
            }
            else {
                $('#quesform').attr("disabled", false);
                var autoSaveAnswer = new Array();
                var equationEditorAnswer = new Array();
                $(".singleQuestion").each(function (index, element) {
                    var singleQuestion = "";
                    var singleQuestionEE = "";
                    $(this).contents().find("select").each(function () {
                        singleQuestion += $(this).val() + "|";
                    })
                    $(this).contents().find("input, iframe").each(function () {
                        if ($(this).hasClass("openEnded")) {
                            singleQuestionEE += $(this)[0].contentWindow.storeAnswer('') + "@$*@";
                            singleQuestionEE += $(this)[0].contentWindow.tools.save();
                        }
                        else if ($(this).attr("type") == "text") {
                            singleQuestion += $(this).val() + "|";
                        }
                        else if ($(this).attr("type") == "radio") {
                            if ($(this).is(":checked")) {
                                singleQuestion += $(this).val() + "|";
                            }
                        }
                    })
                    if (singleQuestion != "")
                        singleQuestion = singleQuestion.substring(0, singleQuestion.length - 1);
                    // if (singleQuestionEE != "")
                        // singleQuestionEE = singleQuestionEE.substring(0, singleQuestionEE.length - 1);
                    autoSaveAnswer.push(singleQuestion);
                    equationEditorAnswer.push(removeInvalidChars(singleQuestionEE));
                });
                var autoSavedAnswer = autoSaveAnswer.join("##");
                var equationEditorAnswer = equationEditorAnswer.join('##');
                $('#userResponse').val(autoSavedAnswer);
                $('#eeResponse').val(equationEditorAnswer);
            }

            $("#mode").val("submitAnswer");
            $('#mode').val("fetchNCERTQuestion");
            $('#result').val(groupNo);
            $('#nextQuesLoaded').val("0");
            var params = $("#quesform").find("input").serialize();
            getNextQues(params, "normal");
            fetchNextQues();
        }
    });
    
    if ($("input:text").length > 0)
        $("input:text:first").focus();

    $(document).click(function (e) {
        $(".moreOption").hide();
    });

    $('#emotToolBar *').click(function (e) {
        e.stopPropagation();
    });
    $("input[name=moreOption]").click(function () {
        saveEmot($(this).val(), $(this));
    });

    $("input[name=emotRespond]").parent().click(function () {
        $(".close").click();
        $(".hoverClass").removeClass("hoverClass");
        saveEmot($(this).find("input[name=emotRespond]").val(), $(this).find("input[name=emotRespond]"));
    });

    $("#cc").parent().click(function () {
        $(".close").click();
        $(".hoverClass").removeClass("hoverClass");
        saveEmot($(this).find("input[name=emotRespond]").val(), $(this).find("input[name=emotRespond]"));
    });

    //---hints
    $("#showHint").live('click', function () {
        if (newQues.quesType.substring(0, 3) == "MCQ" && newQues.noOfTrialsAllowed == 1 && timeToAnswer < 10) {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "You seem to be using the hint very soon. Did you read the question completely?", 
                    type: 'alert', 
                    promptId: "earlyHintUse", 
                    func1: function () { 
                        jQuery("#prmptContainer_earlyHintUse").remove();
                    }
                });
            }
        }
        else {
            for (var h = 1; h < newQues.hintAvailable; h++) {
                $("#timeTakenHints").val($("#timeTakenHints").val() + "||0");
            }
            startHintTimer();
            $("#showHint").hide();
            $("#isHintUsefull").attr("checked", false);
            $("#isHintUsefull").val("0");
            $(".hintDiv").fadeIn(1000);
            $("#hintUsed").val(1);
        }
    });

    $("#nextHint").live('click', function () {
        var totalHints = newQues.hintAvailable;
        for (var k = 1; k < totalHints; k++) {
            if (document.getElementById("hintText" + k)) {
                if ($("#hintText" + k).is(":visible")) {
                    timeTakenForAllHints = $("#timeTakenHints").val().split("||");
                    timeTakenForAllHints[k - 1] = parseInt(timeTakenForAllHints[k - 1]) + timeTakenToViewHint;
                    timeTakenForAllHintStr = timeTakenForAllHints.join("||");
                    $("#timeTakenHints").val(timeTakenForAllHintStr);
                    stopHintTimer();
                    startHintTimer();
                    if (!$("#prevHint").is(":visible"))
                        $("#prevHint").show();
                    $("#hintText" + k).hide();
                    $("#hintText" + (k + 1)).show();
                    if ($("#hintUsed").val() < k + 1)
                        $("#hintUsed").val(k + 1);
                    if (k == totalHints - 1)
                        $("#nextHint").hide();
                    break;
                }
            }
        }
    });

    $("#prevHint").live('click', function () {
        var totalHints = newQues.hintAvailable;
        for (var k = 2; k < totalHints + 1; k++) {
            if (document.getElementById("hintText" + k)) {
                if ($("#hintText" + k).is(":visible")) {
                    timeTakenForAllHints = $("#timeTakenHints").val().split("||");
                    timeTakenForAllHints[k - 1] = parseInt(timeTakenForAllHints[k - 1]) + timeTakenToViewHint;
                    timeTakenForAllHintStr = timeTakenForAllHints.join("||");
                    $("#timeTakenHints").val(timeTakenForAllHintStr);
                    stopHintTimer();
                    startHintTimer();
                    if (!$("#prevHint").is(":visible"))
                        $("#prevHint").show();
                    $("#hintText" + k).hide();
                    $("#hintText" + (k - 1)).show();
                    if (k == 2)
                        $("#prevHint").hide();
                    if (k == totalHints)
                        $("#nextHint").show();
                    break;
                }
            }
        }
    });

    $("#isHintUsefull").click(function () {
        if ($("#isHintUsefull").is(":checked"))
            $("#isHintUsefull").val("1");
        else
            $("#isHintUsefull").val("0");
    });

    $(".commentOn").live("click", function () {
        if ($(this).val() == 2) {
            var selCategory = $("#selCategory").val();
            $("#selCategory").val("");
            $("#commentBox").hide();
            $("#commentOn1").attr("checked", true);
            //$("#commentInfo").html(prevQuesHtml);
            prevQuesClone.appendTo("#commentInfo");
            
            $("#commentInfo #mainHint").remove();
            $("#commentInfo #mainHint").next("br").remove();
            $("#commentInfo #submit_bar1").remove();
            // $("#commentInfo #pnlRateDa").remove();
            $("#commentInfo #arrow").remove();
            $("#commentInfo #wildcardMessage").remove();
            $("#commentInfo .cq-message-div").remove();
            $("#commentInfo .cqebuttondiv").remove();
            $("#commentInfo input[type='hidden']").remove();
                        
            $("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("Please click the Next Question button to continue.", ""));
            $("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("<br>Please click the Next Question button to continue!!<br><br>", ""));
            $("#commentInfo #displayanswer").html($("#commentInfo #displayanswer").html().replace("<br>", ""));
            $("#commentInfo #pnlAnswer").css("height","auto");
            if($("#childClass").val()<=3)
            {
                $("#commentInfo #question").css("height","auto");
                $("#commentInfo #pnlAnswer").css({"width":"100%","margin-left":"0px"});
                $("#commentInfo #quesStem").css("margin-left","120px");
                $("#commentInfo #questionText").css("margin-left","0px");
                $("#commentInfo #commentBox").css("margin-top","0px");
            }
            $("#commentInfo #pnlDisplayAnswerContainer").css("position","relative");
            $("#commentInfo #feedback_header").css("font-size", "1.2em");

            //$("#commentInfo #question").css("height", $("#q2").height()+$("#pnlOptions").height()+$("#pnlAnswer").height()+"px");
            /*$("#commentInfo #question").css("height",($("#commentInfo #question").height()-100)+"px");
            $("#commentInfo #pnlAnswer").css("height",($("#commentInfo #pnlAnswer").height()-100)+"px");*/

            $("#commentInfo").append("<div style='clear:both'></div>");

            $("#commentInfo #question .optionX").removeAttr("onclick");
            
            $("#commentBox").clone().appendTo("#commentInfo");
            $("#commentInfo #questionSelect").remove();
            if (selCategory != "")
                $("#commentInfo #selCategory").val(selCategory);
            $("#commentInfo #commentBox").show();
            $("#commentInfo #commentBoxTr").show();
            $("#commentInfo #selCategory").css({ "font-family": "Helvetica,Arial,sans-serif", "font-size": "17px" });
            $("#commentInfo #comment").css({ "font-family": "Helvetica,Arial,sans-serif", "font-size": "17px" });
            if($("#childClass").val()<=3)
            {
                $("#commentInfo #commentBox").css("margin-top","0px");
            }
            $.fn.colorbox({'href':'#commentInfo','inline':true,'open':true,'escKey':false,'overlayClose': false, 'height':'auto', 'width':'90%'});
            $("body").css("overflow-y","auto");
            $("body").animate({ scrollTop: $(document).height() }, 1000);
            
            /*$("#commentInfo").dialog({
                width: "90%",
                height: "600",
                position: "center",
                draggable: false,
                resizable: false,
                modal: true
            });
            $(".ui-widget-header").css("background", "#39A9E0");
            $(".ui-widget-content").css("background", "#FFFFFF");*/
            
        }
    });
    $("#selCategory").change(function () {
        $("#commentBoxTr").show();
    });

    $("#tagComentSave").click(function () {
        var msg = $("#tagComment").val();
        var qcodeTag = $("#tagQcode").val();
        if (msg == '') {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "You can not tag a question without commenting.", 
                    type: 'alert', 
                    promptId: "commentValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_commentValidation").remove();
                    }
                });
            }
            document.getElementById("tagMsgBox").style.display = 'none';
            return false;
        }
        $.post("controller.php", "mode=tagThisQcode&qcode=" + qcodeTag + "&msg=" + msg + "&type=" + $("#quesCategory").val()+"&dynamicParams="+$("#dynamicParams").val(), function (data) {
            if (data) {
                $("#tagtoModify" + qcodeTag).hide();
            }
        });
        $("#tagMsgBox").hide();
    });

    var objTemp = readCookie("mindspark");
    if (objTemp != null) {
        objTemp = JSON.parse(objTemp);
        objFrustration.F_current = objTemp.F_current;
        objFrustration.arrQuesResult = objTemp.arrQuesResult;
        objFrustration.F_prev = objTemp.F_prev;
        objFrustration.frustInst = objTemp.frustInst;
    }

    // rate Icon and prompts draggable
    var isTouchDevice = false;
    $("#toolContainer,#radioButtons,#progressOverlay").draggable({
        "containment": "document",
        start: function () {
            dragging = 1;
        },
        stop: function () {
            window.setTimeout(function () {
                dragging = 0;
            }, 100)
        }

    });
    $('.questionPrompts').draggable({
        "containment": "#blackScreen"
    });
    
    $('.questionPrompts').on('touchmove', function (e) {
        if (!$('#pnlQuestion').has($(e.target)).length) e.preventDefault();
    });
    
    if (userType == 'msAsStudent'){
        var right = $("#container").width()-$("#msAsStudentInfo").width();
        $('.questionPrompts1').draggable({
            "containment": [0,0,right,600],
            "drag" : function(){
                $("#msAsStudentInfo").css("right","");
            }
        });
        $('.questionPrompts1').on('touchmove', function (e) {
            if (!$('#pnlQuestion').has($(e.target)).length) e.preventDefault();
            $("#msAsStudentInfo").css("right","");
        });
    }
    $("#blackScreen,.questionPrompts,.questionPrompts1").live('touchmove', function (event) {
        event.preventDefault();
    });

    if (document.getElementById('toolContainer') != undefined) {

        document.getElementById('toolContainer').addEventListener('touchend', function () {
            if (dragging == 0)
                toolbar();
            //if (!isTouchDevice) {
            //    isTouchDevice = true;
            //    document.getElementById('toolContainer').removeEventListener('mouseup', toolbar);
            //}
        }, false);
        if (!isAndroid && !isIpad) {
            document.getElementById('toolContainer').addEventListener('mouseup', function () {
                if (dragging == 0)
                    toolbar();
            }, false);
        }

    }
    
    //added for Raty
    $.fn.raty.defaults.path = 'assets/raty';

    //ends here
    
    // sparkie logic tooltip
    if($('#childClass').val()>=4 && $('#childClass').val()<=7) {
        $('<div id="sparkieTooltip"></div>').appendTo('body').on('mousedown mouseup', function(event) {
            event.stopPropagation();
        });
        $('.bubble,.bubble:after').on('click', function(event) {
            showSparkieTooltip();
            event.stopPropagation();
        });
    }
    //ends here
    
    // fullscreen event listener
    $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange', applyScreenState);
});
function countElement(item,array) {
    var count = 0;
    $.each(array, function(i,v) { if (v === item) count++; });
    return count;
}
function getNextQues(params, mode) {
    if($('#quesCategory').val()=='normal') {
        if(questionStatus!='submitted') {
            questionStatus = 'submitted';
        }
        else {
            return;
        }
    }
    clearInterval(constrTool.trailCycle);
    if (practiseModuleTimeStatus==1){
        setTryingToUnload();
        $("#prmptContainer_resultPrompt").remove();
        window.location.assign("home.php");return;
    }
    if ($("#quesCategory").val() != "daTest" && $("#quesCategory").val() != "worksheet") {
        if (params.search(/nextQuesLoaded=1/) != -1) {
            return;
        }
    }
    ajaxFailure = true;
    responseArray = "";
    $.ajax("controller.php", {
        type: 'post',
        data: params,
        success: function process(transport, ajaxStatus) {
            ajaxFailure = false;
            if (ajaxStatus != 0) {
                var response = transport;
                if (trim(response) == "DUPLICATE" || trim(response) == "SESSION_EXPIRED") {
                    redirect = 0;
                    setTryingToUnload();
                    window.location = 'error.php';
                    return;
                }
                if (trim(response).substring(0, 18) == "DUPLICATE_QUESTION") {
                    alert("Something went wrong while displaying this question. To continue, please login again and continue your session.");
                    $.ajax({
                        url: "errorLog.php",
                        type: "POST",
                        data: "params= request- " + $("#quesform").find("input").serialize() + " response-" + encodeURIComponent(response) + "&qcode=" + $("#qcode").val() + "&typeErrorLog=duplicateQuestion",
                        dataType: "text",
                        "async": false
                    });
                    redirect = 0;
                    setTryingToUnload();
                    window.location = 'logout.php';
                    return;
                }
                if (trim(response) == "DUPLICATE_TAB") {
                    alert("It seems mindspark is open in multiple tabs. Please re-login again.");
                    redirect = 0;
                    setTryingToUnload();
                    window.location = 'logout.php';
                    return;
                }

                if (response.indexOf("Mindspark | Error Page") >= 0) {
                    setTryingToUnload();
                    window.location.href = "error.php";
                    return false;
                }

                try {
                    responseArray = $.parseJSON(response);
                }
                catch (err) {
                    $.post("errorLog.php", "params=" + err + "request- " + $("#quesform").find("input").serialize() + " response-" + encodeURIComponent(response) + "&qcode=" + $("#qcode").val() + "&typeErrorLog=7", function (data) {
                    });
                }

                if($("#quesCategory").val() =="practiseModule"){
                    setDdParameters(responseArray["currentScore"],responseArray["currentLevel"]);
                    ddLevelsAttempted=JSON.parse(responseArray['levelsAttempted']);
                    if (responseArray["nextLevelType"]=='timedTest'){
                        var timedTestCode=responseArray["qcode"];
                        $("#timedTestForPM").remove();$('body').append('<input type="hidden" id="timedTestForPM" value="'+timedTestCode+'"/>');
                        $('.timedTestForDd').attr('href','timedTest.php?timedTest='+timedTestCode+'&isInternalRequest=1&practiseModule=1');
                        //document.getElementById('qcode').value=timedTestCode;
                        //setTryingToUnload();
                        document.getElementById("nextQuesLoaded").value = 1;
                    }
                }

                if (mode == "normal") {
                    try {
                        if(typeof windowName !== 'undefined' && typeof responseArray["getWindowName"] != "undefined") { 
                            if(windowName != responseArray["getWindowName"])
                            {
                                setTryingToUnload();
                                window.location.href = "newTab.php";
                            }
                        }
                        if (responseArray['iycAsChoice']=="true"){
                            iycAsChoice=true;
                        }
                        else iycAsChoice=false;
                        // in worksheets, when qcode = -12 means worksheet enddatetime is crossed
                        if(responseArray["qcode"] == -12) {
                            clearTimeout(slowLoadTimer);
                            document.getElementById("nextQuesLoaded").value = 1;
                            wsSessionExpire();
                            return false;
                        }
                        // in NCERT, when qcode = -14 means exercise is completed
                        if(responseArray["qcode"] == -14) {
                            hideSubmitNextButton();
                            alert(i18n.t("questionPage.exerciseComplete"));
                            if($("#ncertCompleted").length == 0)
                                $("#question").before('<div id="ncertCompleted" style="margin: 0px auto !important; width: 350px; text-align: center; font-size: 14px; line-height: 22px;"><b style="color: #2FAC10;">'+i18n.t("questionPage.exerciseComplete")+'</b><br /><span style="cursor:pointer;" onclick="redirect_to_ncert_exercises();">Go to <u>NCERT Exercises</u></span></div><div style="clear:both;">&nbsp;</div>');
                            return false;
                        }
                        if(responseArray["qcode"] == -99) { //in practiceModule, when practiceModule is completed
                            ddLevelsAttempted=JSON.parse(responseArray['levelsAttempted']);
                            document.getElementById("qcode").value = responseArray["qcode"]; //qcode
                            document.getElementById("quesCategory").value = responseArray["quesCategory"];
                            clearTimeout(slowLoadTimer);
                            practiseModuleStatus=1;
                            document.getElementById("nextQuesLoaded").value = 1;
                            return false;
                        }
                        if(responseArray["tmpMode"] == "Assessment")
                        {
                            setTryingToUnload();
                            window.location.href = "controller.php?mode=startDiagnostic";
                            return false;
                        }
                        if (responseArray["tmpMode"] == "KstAssessment") {
                            $("#nextQuestion,#nextQuestion1,#nextQuestion2").hide();
                            setTryingToUnload();
                            $('#kstdiagnosticTest').append('<input type="hidden" name="quesCategory" id="quesCategory" value="kstPostTest" />');
                            document.getElementById('kstdiagnosticTest').submit();
                            return false;
                        }
                        if (responseArray["tmpMode"] == "diagnosticTestComplete") {
                            diagnosticTestComplete = 1;
                            $("#nextQuestion,#nextQuestion1,#nextQuestion2").hide();
                            var dataResponse = responseArray["dataResponse"];
                            var alertText = responseArray["alertText"];
                            if (dataResponse != "Saved" && dataResponse != "nextcluster") {
                                setTryingToUnload();
                                if (alertText != "")
                                    alert(alertText);
                                window.location.href = "controller.php?mode=startDiagnostic";
                                return false;
                            }
                            else if (alertText != "") {
                                window.setTimeout(function () {
                                    viewCMPrompt(alertText,responseArray["testType"],responseArray["lastQcode"]);
                                },500);
                            }
                            return false;
                        } else if(responseArray["tmpMode"] == "kstdiagnosticTestComplete") {
                            var kstdiagnosticTestComplete = 1;
                            $("#nextQuestion,#nextQuestion1,#nextQuestion2").hide();
                            //var dataResponse = responseArray["dataResponse"];
                            var alertText = responseArray["alertText"];
                            var qcode = responseArray["lastQcode"];
                            var featureType = responseArray["testType"];
                            /* if (dataResponse != "Saved" && dataResponse != "nextcluster") {
                                setTryingToUnload();
                                if (alertText != "")
                                    alert(alertText);
                                window.location.href = "controller.php?mode=startDiagnostic";
                                return false;
                            } else */ if (alertText != "") {
                                window.setTimeout(function () {
                                    viewCMPrompt(alertText, featureType, qcode);
                                }, 1000);

                            }
                            return false;

                        } else if (responseArray["tmpMode"] == "kstSubModule") {
                            $("#nextQuestion,#nextQuestion1,#nextQuestion2").hide();
                            setTryingToUnload();
                            document.getElementById('kstdiagnosticTest').submit();
                            return false;
                        }
                        else if (responseArray["tmpMode"] == "NCERT") {
                            objNextQuestion = new Array();
                            var correctAnsArr = responseArray["correctAnswer"].split("##");
                            var ques_typeArr = responseArray["quesType"].split("##");
                            var dropDownAnsArr = responseArray["dropdownAns"].split("##");
                            var noOfBlanksArr = responseArray["noOfBlanks"].split("##");
                            var qcodeArr = responseArray["qcode"].toString().split("##");
                            var dynamicQuesArr = responseArray["dynamicQues"].split("##");
                            var eeIconArr = responseArray["eeIcon"].split('##'); //Equation Editor Icon Flag
                            /* Pending integration for options in case of MCQ */
                            for (var i = 0; i < qcodeArr.length; i++)
                                objNextQuestion.push(new QuestionObj({ qcode: qcodeArr[i], clusterCode: responseArray["clusterCode"], noOfTrials: responseArray["noOfTrials"], hintAvailable: responseArray["hintAvailable"], quesType: ques_typeArr[i], correctAnswer: correctAnsArr[i], noOfBlanks: noOfBlanksArr[i], dropdownAns: dropDownAnsArr[i], dynamicQues: dynamicQuesArr[i], eeIcon: eeIconArr[i], quesVoiceOver: responseArray["quesVoiceOver"] }));
                            if (progressBarFlag == 1) {
                                $('#progress_bar_new').css('display', 'none');
                                $('#showProgressBar').css('display', 'none');
                            }
                        }
                        else {
                            try {
                                objNextQuestion = new QuestionObj(responseArray);
                            }
                            catch (err) {
                                $.post("errorLog.php", "params=" + err + " response-" + encodeURIComponent(response) + "&qcode=" + $("#qcode").val() + "&typeErrorLog=objNextQuestion", function (data) {

                                });
                            }
                            condition = responseArray["condition"].split("||");
                            action = responseArray["action"].split("||");

                            if (responseArray["noOfCondition"] != "")
                                noOfCondition = responseArray["noOfCondition"];
                            else
                                noOfCondition = 0;
                        }
                        if(typeof responseArray["choiceScreen"] != "undefined"){
                            choiceScreen=new ChoiceScreen(responseArray["choiceScreen"]);
                        }
                        else choiceScreen=null;
                        
                        if (progressBarFlag) {
                            sdlAttemptArr = responseArray["SDLAttempts"];
                            document.getElementById('totalSDLS').value = responseArray["totalSDLs"];
                            clusterAttemptArr = responseArray["clusterAttemptsArr"];
                            clusterNameAttemptArr = responseArray["clusterNameAttemptsArr"];
                            topicAttemptArr = responseArray["topicProgressStatus"];
                            clusterStatusPrompt = responseArray['clusterStatusPrompts'];
                            nameOfCluster = responseArray['clusterName'];
                            atHigherLevel = responseArray['higherLevel'];
                            if ($("#childClass").val() > 3) {
                                window.setTimeout(function () {
                                    showClusterPrompt();
                                }, 500);
                            }

                        }
                        
                        // topic replecation flag to show prompt
                        if (typeof responseArray['topicRepeatAttempt'] != "undefined" && userType != 'msAsStudent') {
                            window.setTimeout(function () {
                                openTopicRepeatAttempt(responseArray['topicRepeatAttempt']);
                            }, 500);
                        }

                        if (responseArray['badgeType'] == 'mileStone1') {
                            $('#prompt').css('height', '1200px');
                            $('#sparkie').css('background-image', 'url("assets/rewards/LevelZeroSparkies.png")');
                            $('#sparkie').css('height', '56px');
                            $('#sparkie').css('width', '44px');
                            $('#badge').css('background-image', 'url("assets/rewards/LevelOneSparkie.png")');
                            $('#badge').css('width', '112px');
                            $("#prompt").css("display", "block");
                            $("#desc1").css("left", "271px");
                            $("#desc2").css("left", "277px");
                            $("#badge").css("left", "338px");
                            $("#desc1").html(i18n.t("homePage.milestone1_1"));
                            $("#desc2").html(i18n.t("homePage.milestone1_2"));
                            $("#bottom_info1").html("Goodluck!");
                            $("#bottom_info2").css("display", "none");
                            $('#sparkieImage').removeClass('level1');
                            $('#sparkieImage').addClass('level2');
                        }
                        else if (responseArray['badgeType'] == 'mileStone2') {
                            $('#sparkie').css('background-image', 'url("assets/rewards/Level1_L0.png")');
                            $('#sparkie').css('height', '73px');
                            $('#sparkie').css('width', '69px');
                            $('#sparkie').css('left', '-3px');
                            $('#sparkie').css('top', '43%');
                            $('#badge').css('background-image', 'url("assets/rewards/LevelTwoSparkie.png")');
                            $('#badge').css('width', '112px');
                            $("#prompt").css("display", "block");
                            $("#desc1").css("left", "271px");
                            $("#desc2").css("left", "277px");
                            $("#badge").css("left", "338px");
                            $("#desc1").html(i18n.t("homePage.milestone2_1"));
                            $("#desc2").html(i18n.t("homePage.milestone2_2"));
                            $("#bottom_info1").html("Goodluck!");
                            $("#bottom_info2").css("display", "none");
                            $('#sparkieImage').removeClass('level2');
                            $('#sparkieImage').addClass('level3');
                        }
                        else if (responseArray['badgeType'] == 'mileStone3') {
                            $('#sparkie').css('background-image', 'url("assets/rewards/L.01_info.png")');
                            $('#sparkie').css('height', '65px');
                            $('#sparkie').css('width', '60px');
                            $('#badge').css('background-image', 'url("assets/rewards/badgesRewardSection/Unlocked/Level1500.png")');
                            $('#badge').css('width', '112px');
                            $("#prompt").css("display", "block");
                            $("#desc1").css("left", "271px");
                            $("#desc2").css("left", "272px");
                            $("#badge").css("left", "338px");
                            $("#desc1").html(i18n.t("homePage.milestone3_1"));
                            $("#desc2").html(i18n.t("homePage.milestone3_2"));
                            $("#bottom_info1").html("Goodluck!");
                            $("#bottom_info2").css("display", "none");
                            $('#sparkieImage').removeClass('level2');
                            $('#sparkieImage').addClass('level3');
                        }
                        else if (responseArray['badgeType'] == 'boy' || responseArray['badgeType'] == 'girl') {
                            $('#badge').css('background-image', 'url("assets/rewards/sparkies_notification.png")');
                            $('#badge').css('width', '112px');
                            $("#prompt").css("display", "block");
                            $("#desc1").css("left", "271px");
                            $("#desc2").css("left", "287px");
                            $("#badge").css("left", "338px");
                            $("#desc1").html(i18n.t("homePage.theme2_1"));
                            $("#desc2").html(i18n.t("homePage.theme2_2"));
                            $("#bottom_info1").html("Well Done!");
                            $("#bottom_info2").html("Activate Theme?");
                            $("#bottom_info2").css("width", "119px");
                            $("#bottom_info1").css("left", "581px");
                            $("#bottom_info1").css("font-size", "20px");
                            $("#bottom_info2").css("left", "582px");
                            $("#bottom_info2").css("width", "98px");
                            $("#bottom_info2").css("font-size", "12px");
                            $("#bottom_info2").css("display", "none");
                        }
                        else if (responseArray['badgeType'] == 'light' || responseArray['badgeType'] == 'dark') {
                            $('#badge').css('background-image', 'url("assets/rewards/sparkies_notification.png")');
                            $('#badge').css('width', '112px');
                            $("#prompt").css("display", "block");
                            $("#desc1").css("left", "271px");
                            $("#desc2").css("left", "287px");
                            $("#badge").css("left", "338px");
                            $("#desc1").html(i18n.t("homePage.theme1_1"));
                            $("#desc2").html(i18n.t("homePage.theme1_2"));
                            $("#bottom_info1").html("Well Done!");
                            $("#bottom_info2").html("Activate Theme?");
                            $("#bottom_info2").css("width", "119px");
                            $("#bottom_info1").css("left", "581px");
                            $("#bottom_info1").css("font-size", "20px");
                            $("#bottom_info2").css("left", "582px");
                            $("#bottom_info2").css("width", "98px");
                            $("#bottom_info2").css("font-size", "12px");
                            $("#bottom_info2").css("display", "none");
                        }

                        document.getElementById("toughType").value = responseArray["isTough"];

                        document.getElementById("qcode").value = responseArray["qcode"]; //qcode
                        document.getElementById("tmpMode").value = responseArray["tmpMode"]; //tmpMode
                        document.getElementById("quesCategory").value = responseArray["quesCategory"];
                        document.getElementById("showAnswer").value = responseArray["showAnswer"]; //showAnswer
                        document.getElementById("quesType").value = responseArray["quesType"]; //quesType
                        document.getElementById("clusterCode").value = responseArray["clusterCode"]; 
                        //clusterCode
                        document.getElementById("hasExpln").value = responseArray["hasExpln"];
                        //document.getElementById("signature").value = responseArray["signature"];
                        //document.getElementById("validToken").value = responseArray["validToken"];

                        if (responseArray["sdlList"] && responseArray["quesCategory"] == "normal") {
                            var sdlList = $("#sdlList").val();
                            if (sdlList != "") {
                                var sdlArr = sdlList.split("|");
                                var sdlLength = sdlArr.length;
                                if (sdlLength == 4)
                                    sdlList = sdlArr[1] + "|" + sdlArr[2] + "|" + sdlArr[3] + "|" + responseArray["sdlList"];
                                else
                                    sdlList = sdlList + "|" + responseArray["sdlList"];
                            }
                            else
                                sdlList = responseArray["sdlList"];
                            $("#sdlList").val(sdlList);
                        }
                        
                        if (responseArray['quesCategory']=='practiseModule'){
                            practiseModuleStatus=0;
                        }
                        Q1 = ""; Q2 = ""; Q4 = ""; Q5 = ""; Q6 = ""; Q7 = ""; Q8 = ""; Q9 = "";
                        
                        if(typeof responseArray["Q1"] != "undefined")
                            Q1 = responseArray["Q1"];
                        if (Q1 != "")
                            document.getElementById("qno").value = Q1; //qno

                        if(typeof responseArray["Q2"] != "undefined")
                            Q2 = responseArray["Q2"]; //question text
                        if(typeof responseArray["Q4"] != "undefined")
                            Q4 = responseArray["Q4"]; //option
                        if(typeof responseArray["dispAns"] != "undefined")
                            Q5 = responseArray["dispAns"];
                        if(typeof responseArray["dispAnsA"] != "undefined")
                            Q6 = responseArray["dispAnsA"];
                        if(typeof responseArray["dispAnsB"] != "undefined")
                            Q7 = responseArray["dispAnsB"];
                        if(typeof responseArray["dispAnsC"] != "undefined")
                            Q8 = responseArray["dispAnsC"];
                        if(typeof responseArray["dispAnsD"] != "undefined")
                            Q9 = responseArray["dispAnsD"];
                        
                        footerBar = responseArray["footer"];
                        sparkie = responseArray["sparkie"];
                        voiceover1 = responseArray["voiceover"];
                        correctAns1 = responseArray["correctAnswer"];
                        hint1 = responseArray["hint"];
                        hintAvailable1 = responseArray["hintAvailable"];
                        document.getElementById("dynamicQues").value = responseArray["dynamicQues"];
                        document.getElementById("dynamicParams").value = responseArray["dynamicParams"];
                        preload1 = responseArray["preloadDisplayAnswerImage"];
                        checkImage(preload1);
                        problemid1 = responseArray["problemid"];
                        ano = 1; //for conditional alert - attempt no.

                        if (refreshProgessBar) {
                            $('.yellow').remove();
                            $('.green').remove();
                        }

                        quesAttempted = responseArray["quesAttemptedInTopic"];
                        quesCorrect1 = responseArray["quesCorrectInTopic"];
                        progressInTopic = responseArray["progressInTopic"];
                        lowerLevel1 = responseArray["lowerLevel"];

                        if (!progressBarFlag || refreshProgessBar) {
                            refreshProgessBar = 0;
                            showProgressDetails(responseArray["quesAttemptedInTopic"], responseArray["quesCorrectInTopic"], responseArray["progressInTopic"], responseArray["lowerLevel"], 1); //Pending integration
                        }
                        document.getElementById("noOfTrialsAllowed").value = responseArray["noOfTrials"];
                        totalAttempts = 10;
                        document.getElementById("nextQuesLoaded").value = 1;

                        //markOption(responseArray["daUserAnswer"]);
                        if($("#quesCategory").val() == "worksheet" || $("#quesCategory").val() == "daTest"){
                            if(responseArray["quesType"].substring(0, 3) == "MCQ")
                            {
                                $('.optionX').removeClass("optionActive");
                                $('#option' + responseArray["daUserAnswer"] + ' .optionX').addClass("optionActive");                            
                                $('#option' + responseArray["wsUserAnswer"] + ' .optionX').addClass("optionActive");                        
                            }
                        }
                        
                        if(Q1 == 1 && $('#daInstructionPrompt').val() == 0 && $("#quesCategory").val() == "daTest")
                        {
                            $('#daInstructionPrompt').val(1);
                            var newMessage = "<ul style='text-align:left'><li>The test duration is 30 minutes.</li><li>The timer will turn red when you will have 5 minutes remaining.</li><li>The test will be automatically submitted when 30 minutes are over.</li><li>You can click on any previously attempted question to recheck it.</li><li>All the questions that are answered will be considered for evaluation.</li></ul>";
                            
                    
                            var prompts = new Prompt({
                                text: newMessage,
                                type: 'alert',
                                label2:'Continue Test',
                                promptId: "SubmitTest",
                                func1: function () {
                                    setTryingToUnload();
                                    $("#prmptContainer_SubmitTest").remove();
                                }
                            });
                        }
                        if($("#quesCategory").val() == "worksheet"){
                            $('#DASubmitTestBtn').val(0);$('#mcqText').hide();$("#nextQuestion2,#nextQuestion").show();
                            setWSColorCode(responseArray["wsAnsweredArray"],Q1);$('#submittest').removeClass('inActive');
                            if ((countElement(0,responseArray["wsAnsweredArray"])==1 && (responseArray["wsAnsweredArray"]).indexOf(0)==Q1-1) || (countElement(0,responseArray["wsAnsweredArray"])==0) ){
                                $('#DASubmitTestBtn').val(1);
                                /*$("#nextQuestion2,#nextQuestion").hide();
                                $('#submittest').css("display", "block");*/
                            }
                            else{
                                $('#submittest').addClass('inActive');
                                /*$("#nextQuestion2,#nextQuestion").show();
                                $('#submittest').css("display", "none");*/
                            }
                        }
                        else {
                            if($('#DASubmitTestBtn').val() == 1)
                            {
                                $('#submittest').css("display", "block");
                                $("#nextQuestion").hide();
                                $("#nextQuestion2").hide();
                            }
                             else
                                $('#submittest').css("display", "none");
                        }
                         if ($("#quesCategory").val() != "worksheet" && $("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest") {
                            $("#promptBox").css("width","650px");                         
                            $("#promptBox").css("margin-left","29%");
                            $("#promptBox").css("margin-top","175px");
                        }
                        
                        if(Q1 == $('#daTestQueCount').val() && $("#quesCategory").val() == "daTest")
                        {
                            $('#flagLater').css("display", "none");
                            $('#nextQuestion').css("display", "none");
                            $('#nextQuestion2').css("display", "none");
                            $('#submittest').css("display", "block");
                            $('#flagLater').css("display", "block");
                            $('#DASubmitTestBtn').val(1);
                        }
                        
                        if($("#quesCategory").val() == "daTest")
                        {
                            if(Q1 > 1)
                            {
                                if( isDaFirstQue === true)
                                {
                                //alert($('#countdown').html());
                                isDaFirstQue = false;
                                var queCount = parseInt($('#daTestQueCount').val()) + 1;
                                var pendingQue = queCount - Q1;
                                var daTopicName = $("#daTopicName").val();
                                var newMessage = "Your Super Test on '"+daTopicName+"' is pending. <br><br> Questions left to be answered : "+pendingQue+"<br><br> Time remaining:   "+responseArray["DaTimeRemaining"]+" Minutes<br><br>Since you discontinued the test by ending the session, you cannot review and change answers for the questions attempted previously.";
                                if(!$(".promptContainer").is(":visible"))
                                {
                                    var prompts = new Prompt({
                                        text: newMessage,
                                        type: 'alert',
                                        label1 : 'Continue to Super Test',
                                        promptId: "specifyAnswer",
                                        func1: function () {
                                            jQuery("#prmptContainer_specifyAnswer").remove();
                                        }
                                    });
                                }
                                $("#promptBox").css("width","610px");
                                $("#promptBox").css("margin-left","28%");
                                $("#promptBox").css("margin-top","140px");
                                }
                                
                            }else{
                                isDaFirstQue = false;
                            }
                        }

                        if($("#quesCategory").val() == 'diagnosticTest')
                            diagnosticTestTotalQuestion = responseArray['dignosticTestTotalQuestion']; 
                                                  

                    }

                    catch (err) {
                        $.post("errorLog.php", "params=" + err + " response-" + encodeURIComponent(response) + "&qcode=" + $("#qcode").val() + "&typeErrorLog=8", function (data) {

                        });
                        alert("1.3 Sorry! Could not fetch the next question. Redirecting to scorecard.\n\nGo to HOME page and click on CONTINUE.");
                        document.getElementById('nextQuesLoaded').value = "-1";
                    }
                }
            }
            else {
                $.post("errorLog.php", "params=request- " + encodeURIComponent($("#quesform").find("input").serialize()) + "&qcode=" + $("#qcode").val() + "&typeErrorLog=error 1.1", function (data) {

                });
                alert("1.1: Oops! There seems to be a technical error. Error registered. End of session!");
                document.getElementById('nextQuesLoaded').value = "-1";
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (ajaxFailure) {
                var errorLogsFail = false;
                if (retryNo == 0)
                    var retryErrorType = "error 1.2.1";
                else
                    var retryErrorType = "error 1.2";
                    
                var msPing = $.ajax({
                    url: "msPing.php",
                    type: "POST",
                    data: "",
                    dataType: "text",
                    "async": false
                });
                msPing.fail(function (jqXHR, textStatus) {
                    if(retryNo < 1)
                        errorLogsFail = true;
                    if (storageEnabled()) localStorage.setItem("sessionID", $(".sessionColor:eq(0)").text());
                    if (storageEnabled()) localStorage.setItem("qcode", $("#qcode").val());
                    if (storageEnabled()) localStorage.setItem("qno", $(".sessionColor:eq(1)").text());
                    if (storageEnabled()) localStorage.setItem("errorType", "Ping fail");
                    alert(i18n.t("questionPage.slowLoadingMsg"));

                    redirResult(8);
                });
                
                if(errorLogsFail === false)
                {
                    var errorLogs = $.ajax({
                        url: "errorLog.php",
                        type: "POST",
                        data: "params=" + JSON.stringify(xhr) + " - and - " + thrownError + " - request- " + encodeURIComponent($("#quesform").find("input").serialize()) + " retryNO - " + retryNo + "&qcode=" + $("#qcode").val() + "&typeErrorLog=" + retryErrorType,
                        dataType: "text",
                        "async": false
                    });
                    errorLogs.fail(function (jqXHR, textStatus) {
                        errorLogsFail = true;
                        if (storageEnabled()) localStorage.setItem("sessionID", $(".sessionColor:eq(0)").text());
                        if (storageEnabled()) localStorage.setItem("qcode", $("#qcode").val());
                        if (storageEnabled()) localStorage.setItem("qno", $(".sessionColor:eq(1)").text());
                        if (storageEnabled()) localStorage.setItem("errorType", "error 1.2");
                    });
                }

                if (retryNo < 1 && $('#quesCategory').val() != "NCERT") {
                    setTimeout(function () {
                        retryNo++;
                        params = "qcode=0&mode=firstQuestion&quesCategory=" + $('#quesCategory').val();
                        getNextQues(params, "normal");
                    }, 4000);
                }
                else if(errorLogsFail === false) {
                  /*  if (errorLogsFail)
                        alert(i18n.t("questionPage.slowLoadingMsg"));
                    else*/
                        alert("1.2: Oops! There seems to be a technical error. End of session!");
                    document.getElementById('nextQuesLoaded').value = "-1";
                }
            }
        },
        complete: function () {
            window.status = 'Complete...';
        }
    }
);   //Ending Ajax Request
}
function fetchNextQues() {
    slowLoadTimer = setTimeout("showSlowLoadingMsg()", 15000);
    // $("#wildcardMessage").text('').val(i18n.t("questionPage.placeHolderWildcardText"));

    prevQcodeAfterNext = prevQcode;
    prevQuesCategoryAfterNext = prevQuesCategory;
    prevQuesSubCategoryAfterNext = prevQuesSubCategory;
    prevDynamicParamsAfterNext = prevDynamicParams;

    stopHintTimer();
    //--for comments
    if ($("#q1").html() != "") {
        prevQuesClone = "";
        prevQuesHtml = $("#dlgAnswer").html();
        prevQuesClone = $("#question").clone();
        $("#commentOn2").attr('disabled', false);
    }
    else {
        $("#commentOn2").attr('disabled', true);
    }
    //--for comments

    var params = $("#quesform").find("input").serialize();
    $('#quesform').attr("disabled", true);
    $('#pnlQuestion').css("display", "none");
    $('#pnlAnswer').css("display", "none");
    //    $('#displayanswer').html("");
    //document.getElementById('pnlButton').style.display = "none";    Pending integration - check if needed
    //if (document.getElementById('pnlCQ'))
    $('#questionType').css("display", "none");
    $("#q2").css("min-height", "0px");
    /*if (document.getElementById('pnlWC'))
    document.getElementById('pnlWC').style.display = "none";*/

    $(".groupQues").hide(); // Added For Practice Cluster..
    $(".groupQues").empty(); // Added For Practice Cluster..
    hideSubmitBar();
    var infobarHeight = document.getElementById("info_bar").offsetHeight;
    var b = window.innerHeight - infobarHeight - 80 - 17;
    $('#pnlLoading').css({ "display": "block", "height": b });
    //$('#pnlLoading').css({"display":"block"});
    //return false;

    isNextQuesLoaded();
}
function isNextQuesLoaded() {
    if ($('#nextQuesLoaded').val() == "0" )
        window.setTimeout("isNextQuesLoaded()", 500);
    else {
        if ($('#nextQuesLoaded').val() == "-1" ) {
            redirResult(9); //Ajax failure
        }
        else {
            $("#quesform").attr("disabled", false);
            var code = $("#qcode").val();
            code = code.replace(/^\s*|\s*$/g, "");
            $('#refresh').val("0");
            if($("#quesCategory").val() =="worksheet"){
                isLoadedDAQue = true;
            }
            else if($("#quesCategory").val() =="practiseModule"){
                //setDdParameters(responseArray["currentScore"],responseArray["currentLevel"]);
                if (responseArray['nextLevelType']=='timedTest'){
                    var timedTestCode=$("#timedTestForPM").val();
                    if (!$(".promptContainer").is(":visible")) {
                        timedTestPrompt=true;
                        new Prompt({
                            text: "Timed Challenge begins in: <b id='timedTestCountDown'></b>",
                            type: "alert",
                            func1: function () {
                                  $("#prmptContainer_ttprompt").remove();
                                  clearInterval(timedPromptTimer);
                                  if (timedTestPrompt) $(".timedTestForDd").trigger("click");
                                  timedTestPrompt=false;
                                  tryingToUnloadPage=false;
                              },
                            promptId: "ttprompt",
                            
                        });
                        var display = $('#timedTestCountDown');
                        startTimedTestTimer(3, display,timedTestCode);
                    }
                }
                else if(responseArray["qcode"] == -99) { //in practiceModule, when practiceModule is completed
                    ddSessionCompleted();
                }
            }
            if (code == "-2") {
                if (choiceScreen && choiceScreen!="") {
                    $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html('You have completed the topic.');
                    $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+40)+'px');
                    clearTimeout(slowLoadTimer);choiceScreen.show();
                }
                else finalSubmit(code);           //Pass the code End of topic - failure
                return false;
            }
            else if (code == "-3") {
                if (choiceScreen && choiceScreen!="") {
                    $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html('Congratulations! You have successfully completed the topic.');
                    $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+40)+'px');
                    clearTimeout(slowLoadTimer);choiceScreen.show();
                }
                else finalSubmit(code);                //Pass the code End of topic - success
                return false;
            }
            else if (code == "-4") {
                alert("Error in finding minimum SDL");
                finalSubmit(code);
                //return false;
            }
            else if (code == "-5" || code == "-6" || code == "-7") {
                finalSubmit(code);
                return false;
            }
            else if (code == "-9") {
                finalSubmit(code);                //Pass the code End of topic - success
                return false;
            }
            else if (code == "-10") {
                showExerciseCompletion();
                return false;
            }
            else if (code == "-14") {
                alert(i18n.t("questionPage.exerciseComplete"));
                if($("#ncertCompleted").length == 0)
                    $("#question").before('<div id="ncertCompleted" style="margin: 0px auto !important; width: 350px; text-align: center; font-size: 14px; line-height: 22px;"><b style="color: #2FAC10;">'+i18n.t("questionPage.exerciseComplete")+'</b><br /><span style="cursor:pointer;" onclick="redirect_to_ncert_exercises();">Go to <u>NCERT Exercises</u></span></div><div style="clear:both;">&nbsp;</div>');
                return false;
            }
            else if (code == "-11") {
                redirect = 0;
                var dctCodeResponse = document.getElementById("tmpMode").value;
                var dctCodeResponseArray = dctCodeResponse.split("~");
                if(dctCodeResponseArray[0] == "DCT")
                {
                    //Create and submit form
                    var form = document.createElement("form");
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "DCT.php");
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "redirect");
                    hiddenField.setAttribute("value", dctCodeResponseArray[1]);
                    form.appendChild(hiddenField);
                    document.body.appendChild(form);
                    setTryingToUnload();
                    form.submit();
                }
                else
                {
                    var game = document.getElementById("tmpMode").value;
                    var gameArray = game.split("~");
                    var gameID = gameArray[0];
                    var gameCode = gameArray[1];
                    //var gameCode = "";
    
                    //Create and submit form
                    var form = document.createElement("form");
                    form.setAttribute("method", "post");
                    form.setAttribute("action", "enrichmentModule.php");
    
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "gameID");
                    hiddenField.setAttribute("value", gameID);
                    form.appendChild(hiddenField);
    
                    if (typeof (gameArray[1]) != "undefined") {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "gameCode");
                        hiddenField.setAttribute("value", gameCode);
                        form.appendChild(hiddenField);
                    }
    
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", "gameMode");
                    hiddenField.setAttribute("value", "DCTstage");
                    form.appendChild(hiddenField);
    
                    document.body.appendChild(form);
                    setTryingToUnload();
                    form.submit();
                }
                return false;
            }
            else if (code == "-13") {
                redirect = 0;
                document.quesform.action = 'researchModule.php';
                setTryingToUnload();
                document.quesform.submit();
            }
            else if (code=="-99"){ //practiseModule completed

            }
            else if (code == "-16") {
                finalSubmit(code);                //Completed exam corner cluster
                return false;
            }
            else {
                var tmpMode = $("#tmpMode").val().replace(/^\s*|\s*$/g, "");
                if(tmpMode.indexOf("commoninstruction") > -1 && tmpMode.indexOf("groupInstruction") > -1)
                {
                    redirect = 0;
                    document.quesform.action = 'group_instruction.php';
                    setTryingToUnload();
                    document.quesform.submit();
                }
                else if (tmpMode.indexOf("commoninstruction") > -1 && tmpMode.indexOf("enrichmentModule") > -1)
                {
                    if (choiceScreen && choiceScreen!="") { clearTimeout(slowLoadTimer); choiceScreen.show(function(){
                            redirect = 0;
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", "gameMode");
                            hiddenField.setAttribute("value", "groupInstruction");
                            quesform.appendChild(hiddenField);
                            
                            document.quesform.action = 'enrichmentModule.php';
                            setTryingToUnload();
                            document.quesform.submit();
                        },null);
                    }
                    else{
                        redirect = 0;
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", "gameMode");
                        hiddenField.setAttribute("value", "groupInstruction");
                        quesform.appendChild(hiddenField);
                    
                        document.quesform.action = 'enrichmentModule.php';
                        setTryingToUnload();
                        document.quesform.submit();
                    }
                }
                else if (tmpMode == "introVids") {
                    redirect = 0;
                    document.quesform.action = 'introductionVideos.php';
                    setTryingToUnload();
                    document.quesform.submit();
                }
                else if (tmpMode == "timedtest") {
                    redirect = 0;
                    document.quesform.action = 'timedTest.php';
                    setTryingToUnload();
                    document.quesform.submit();
                }
                else if (tmpMode == "diagnosticTest") {
                    /*if (choiceScreen && choiceScreen!="" && Object.keys(choiceScreen['choices']).length>0) { clearTimeout(slowLoadTimer); choiceScreen.show(callDiagnosticTest,null);}
                    else{*/
                        callDiagnosticTest();
                    /* } */
                }
                else if (code == "-8") {    //Added after timed test to ensure timed test is given first before the class level completion message
                    if (choiceScreen && choiceScreen!="" && Object.keys(choiceScreen['choices']).length>0) { 
                        clearTimeout(slowLoadTimer); choiceScreen.show();
                    }
                    else {
                        showClassLevelCompletion();
                        return false;
                    }
                }
                else if (code == "-12") {
                    redirect = 0;
                    document.getElementById('mode').value = "ttSelection";
                    document.quesform.action = 'controller.php?mode=ttSelection&completedPostTest=1';
                    setTryingToUnload();
                    document.quesform.submit();
                }
                else if (tmpMode == "game") {
                    redirect = 0;
                    document.getElementById('mode').value = tmpMode;
                    document.quesform.action = 'controller.php';
                    setTryingToUnload();
                    document.quesform.submit();
                }
                else if (tmpMode == "remedial") {
                    redirect = 0;
                    document.quesform.action = 'remedialItem.php';
                    setTryingToUnload();
                    document.quesform.submit();
                }
                else {
                    //alert(0);
                    if (choiceScreen && choiceScreen!="") { clearTimeout(slowLoadTimer); choiceScreen.show(showNextQuestion,null);}
                    else showNextQuestion();
                    if (progressBarFlag) {
                        updateClusterBar(); //Pending integration
                    }
                }
            }
        }
    }
}
function callDiagnosticTest(){
    setTryingToUnload();
    window.location.href = "controller.php?mode=startDiagnostic";
    return false;
}
function showNextQuestion() {
//for da rating 
    // $("#daComment").hide();
    // $("#daComment").val("");
    // $('#daRating').raty('reload');
    questionStatus = '';
    displayAnsRating = "";
    document.getElementById('toughResult').value = "NA";
    toughFlag = 0;
    retryNo = 0;
    if ($("#quesCategory").val() == "wildcard" && $("#childClass").val() > 3) {
        if(responseArray['displayText'] != null && responseArray['displayText'] != "")
        {
            $("#wildcardMessage").attr("placeholder",responseArray['displayText']);
        }
        $('#wildcardMessage').css('display', 'block');
    }
    if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "bonusCQ") {
        $("#question_number").hide();
        if($("#quesCategory").val() == "bonusCQ")
        {
            $('#progress_bar_new').css('display', 'none');
            $('#showProgressBar').css('display', 'none');
        }
        else if($("#tmpMode").val()==1)
        {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({
                    text: i18n.t("questionPage.otherCQPrompt"),
                    type: 'confirm',
                    label1: 'Yes',
                    label2:'No',
                    promptId: "otherCQ",
                    func1: function () {
                        setTryingToUnload();
                        jQuery("#prmptContainer_otherCQ").remove();
                        $.ajax('controller.php',
                            {
                                type: 'post',
                                data: "mode=otherTopicCQ&userResponse=yes",
                                success: function (transport) {
                                                                    
                                }
                            }
                            );
                    },
                    func2:function(){
                            jQuery("#prmptContainer_otherCQ").remove();
                            $.ajax('controller.php',
                            {
                                type: 'post',
                                data: "mode=otherTopicCQ&userResponse=no",
                                success: function (transport) {
                                    submitAnswer('skipped');                                    
                                }
                            }
                            );
                    }
                });
            }
        }
        $(".cq-message-div").css('display', 'block');
        $('#clusterQuestionMessage').css('display', 'block');
        $("#setAllowSend").val("0");
        $("#clusterQuestionMessage").val("");
    }
    else if ($("#quesCategory").val() == "wildcard")
        $("#question_number").hide();
    else
        $("#question_number").show();
    if($('#progress_bar_new') && $("#quesCategory").val() != "bonusCQ")
    {
        $('#progress_bar_new').show();
        if(responseArray['comprehensiveCluster'])
            $('#showProgressBar').hide();
        else
            $('#showProgressBar').show();
    }

    if (document.getElementById('ichar')) {
        updateBuddy(2);
        $("#ichar").removeClass("buddyOpacity");
    }
    allowed = 1;
    newQues = objNextQuestion;
    if (document.getElementById("tmpMode").value != "NCERT") {
        $("#q1").html(Q1);
        $('#lblQuestionNo').html(Q1);
        if ($("#quesCategory").val() == 'diagnosticTest' && $("#quesCategory").val() == "kstdiagnosticTest")
        {
            $("#lblQuestionTotal").text(diagnosticTestTotalQuestion);
            if($("#childClass").val() < 8)
            {
               $("#sessionTime").css('margin-left','34%'); 
            }            
            $("#submitQuestion1").css('float','right');
        }
        

        $('#lblQuestionNoCircle').html(Q1);
        $("#q2").html(Q2);
        if (newQues.quesType.substring(0, 3) == "MCQ") {
            $('#pnlOptions').css("display", "block");
            if ($("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest")
            {
                $('#pnlOptionTextA').html(newQues.optionA);
                $('#pnlOptionTextB').html(newQues.optionB);
            }
            else
            {
                $('#pnlOptionTextA').html(newQues.optionA[1]);
                $('#pnlOptionTextB').html(newQues.optionB[1]);
                optionDiagnosticArray["A"] = newQues.optionA[0];
                optionDiagnosticArray["B"] = newQues.optionB[0];
            }
            $('#optionC').css("display", "none");
            $('#optionD').css("display", "none");
            $('#optionC').removeClass("clear");
            if (newQues.quesType == "MCQ-3" || newQues.quesType == "MCQ-4") {
                if ($("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest")
                {
                    $('#pnlOptionTextC').html(newQues.optionC);
                }
                else
                {
                    $('#pnlOptionTextC').html(newQues.optionC[1]);
                    optionDiagnosticArray["C"] = newQues.optionC[0];
                }
                $('#optionC').css("display", "block");
                $('#optionD').css("display", "none");
                $('.option').css("width", "30%");
                $('.optionText').css("width", "75%");
            }
            if (newQues.quesType == "MCQ-4") {
                if ($("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest")
                {
                    $('#pnlOptionTextD').html(newQues.optionD);
                }
                else
                {
                    $('#pnlOptionTextD').html(newQues.optionD[1]);
                    optionDiagnosticArray["D"] = newQues.optionD[0];
                }
                $('#optionD').css("display", "block");
                $('#optionC').addClass("clear");
                $('.option').css("width", "47%");
                $('.optionText').css("width", "80%");
            }
            if (newQues.quesType == "MCQ-2") {
                $('.option').css("width", "47%");
                $('.optionText').css("width", "80%");
            }
        }
        else {
            $('#pnlOptions').css("display", "none");
        }


        if ($.trim($("#noOfSparkie").text()) != $.trim(sparkie) && $.trim($("#noOfSparkie").text()) != "0") {
            $("#noOfSparkie").removeAttr("style");
            animateSparkie("noOfSparkie");
        }
        if ($.trim($("#sparkieInfo").text()) != $.trim(sparkie) && $.trim($("#noOfSparkie").text()) != "0") {
            $(".bubble").removeAttr("style");
            animateSparkie("sparkieInfo");
        }
        if (sparkie == "") {
            sparkie = 0;
            if (parseInt($("#sparkieInfo").html()) > 0 && $("#childClass").val() < 8)
                sparkie = parseInt($("#sparkieInfo").html());
            else if (parseInt($(".redCircle").html()) > 0 && $("#childClass").val() >= 8)
                sparkie = parseInt($(".redCircle").html());
            $("#noOfSparkie").html(sparkie);
            $("#sparkieInfo").html(sparkie);
            $(".redCircle").html(sparkie);
        }
        else {
            if ($("#childClass").val() < 8) {
                if (parseInt(sparkie) < parseInt($("#sparkieInfo").html())) {
                }
                else {
                    $("#noOfSparkie").html(sparkie);
                    $("#sparkieInfo").html(sparkie);
                }
            }
            else if ($("#childClass").val() >= 8) {
                var d = sparkie + ":";
                var a = d.split(":");
                var b = a[1].split("<");
                if (parseInt(b[0]) < parseInt($(".redCircle").html())) {
                }
                else
                    $(".redCircle").html(b[0]);
            }
        }

        $("#msAsStudentInfo").html(footerBar);

        if (voiceover1 == 1) 
        {
            if (typeof newQues.quesVoiceOver != "undefined" || newQues.quesVoiceOver != "")
                var voiceFile = newQues.quesVoiceOver.split('.mp3')[0];
            document.getElementById("voiceover").innerHTML = "<div id=voiceOverImage onclick=\"playVoiceover('Q')\" > </div>" + '<div class="audioControls"><audio controls="controls" class="soundFiles" preload="auto" id="one"><source src="' + voiceFile + '.mp3" type="audio/mpeg" /><source src="' + voiceFile + '.ogg" type="audio/ogg" />Your browser does not support the audio tag.</audio></div>';
            $('#voiceover').css("display","block");
            $("#quesStem").css("margin-top", "-120px");
            var one = document.getElementById('one');
            one.load();
            one.addEventListener('ended', voicePlayFinished);
        }
        else
        {
            $('#voiceover').css("display","none");
            $("#quesStem").removeAttr("style");
            var marginTop = $("#quesStem").css("margin-top");
            $("#quesStem").css("margin-top",marginTop);
        }
        document.getElementById("hintAvailable").value = hintAvailable1;
        $("#quesVoiceOver").val(newQues.quesVoiceOver);
        $("#ansVoiceOver").val(newQues.ansVoiceOver);

        $("#hintUsed").val(0);
        $("#timeTakenHints").val("0");
        $("#userAllAnswers").val("");
        if (hint1 != "") {
            var hintArr = hint1.split("||");
            $("#hintText1").html("<b>" + i18n.t("questionPage.hint") + " 1 - </b>" + hintArr[0]);
            if (hintArr[1])
                $("#hintText2").html("<b>" + i18n.t("questionPage.hint") + " 2 - </b>" + hintArr[1]);
            else
                $("#bottomBtn").hide();
            if (hintArr[2])
                $("#hintText3").html("<b>" + i18n.t("questionPage.hint") + " 3 - </b>" + hintArr[2]);
            if (hintArr[3])
                $("#hintText4").html("<b>" + i18n.t("questionPage.hint") + " 4 - </b>" + hintArr[3]);
            if (newQues.noOfTrialsAllowed == 1) {
                if (newQues.quesType.substring(0, 3) == "MCQ") {
                    startAnswerTimer();
                }
                $("#mainHint,#showHint").show();
            }
        }
        document.getElementById('userResponse').value = "";
        document.getElementById('extraParameters').value = "";
        document.getElementById('eeResponse').value = "";
        document.getElementById('noOfTrialsTaken').value = 0;
        $(".question_text").contents().find("#q1").show();
        var qType = newQues.quesType;
        if (!(qType == "MCQ-2" || qType == "MCQ-3" || qType == "MCQ-4") && $("#quesCategory").val() != "worksheet") {
            showSubmitButton();
            $("#mcqText").hide();
            if(qType=="I" && Q2.indexOf('ADA_eqs/src/index.html')!=-1) {
                $("#submitQuestion").hide();
                $("#submitQuestion1").hide();
                $("#submitQuestion2").hide();
                $('#submit_bar').hide();
                $('#submit_bar1').hide();
                $('#submitArrow').hide();
            }
        }
        else  if ($("#quesCategory").val() == "daTest" || $("#quesCategory").val() == "worksheet") {
            $("#submit_bar").show();
            var inputCounter=0;autoPreSavedAnswer=(!responseArray["wsUserAnswer"])?[]:responseArray["wsUserAnswer"].split('|');
            $("#q2").find("input,iframe").each(function () {
                if ($(this).hasClass("openEnded")) {
                    $(this).load(function () {
                        
                    });
                }
                if ($(this).hasClass("fracBox")) {
                    $(this).load(function () {
                        $(this).contents().find("iframe").contents().find("body").html(parent.autoPreSavedAnswer[0]);
                    });
                }
                if ($(this).attr("type") == "text") {
					var tempID = $(this).attr("id");
					tempID = tempID.replace("b","");
					tempID = parseInt(tempID)-1;
                    $(this).val(autoPreSavedAnswer[tempID]);
                    inputCounter++;
                }
                else if ($(this).attr("type") == "radio") {
                    if ($(this).val() == autoPreSavedAnswer[inputCounter]) {
                        $(this).attr('checked', true);
                        inputCounter++;
                    }
                }
            });

            if(document.getElementById("qno").value != $('#daTestQueCount').val() && $('#DASubmitTestBtn').val() == 0)
                $("#nextQuestion").show();
        }
        else {
            showSubmitButton();
            $('#skipQuestion').css('margin-top', '-31px');
            $("#submitQuestion").hide();
            $("#submitQuestion2").hide();
            $("#skipQuestion2").hide();
            $("#mcqText").show();
        }

        $(".groupQues").empty();
        /*if ($("#quesCategory").val() == "normal") // for the mantis task 9194
            $("#emotToolBar").show();
        else
            $("#emotToolBar").hide();*/

        if (newQues.eeIcon == "1")
            $("#eqEditorToggler").show();
        else
            $("#eqEditorToggler").hide();
    }
    else {
        $("#pnlOptions").hide();
        // Change the procedure Of showing Question For Practice Cluster..
        var loopCounter = 0;
        $("#eqEditorToggler").hide();
        $("#question").contents().find("#q2").html(Q5);
        $("#q4:first").empty();
        var noOfColumns = hint1;
        var qcodeArray = $("#qcode").val().split("##");
        $(".groupNav").removeClass("current");
        $("#groupNav" + Q6).addClass("current");
        
        $("#question").contents().find("#lblQuestionNoCircle").html(Q6);
        
        var Q1Array = Q1.split("##");
        var Q2Array = Q2.split("##");
        var Q4Array = Q4.split("##");
        $.each(newQues, function (index, value) {
            loopCounter++;
            Q4Array[index] = Q4Array[index].replace(/ansRadio/g, "ansRadio_" + newQues[index].qcode);

            if (Q1Array.length == 1) {
                $("#questionTemplate").contents().find("#q1").html("");
            }
            else {
                $("#questionTemplate").contents().find("#q1").html("(" + Q1Array[index] + ")");
            }
            
            $("#questionTemplate").contents().find("#q2").html(Q2Array[index]);
            if (newQues[index].eeIcon == "1")
                $("#questionTemplate").contents().find(".eqEditorToggler").show();
            else
                $("#questionTemplate").contents().find(".eqEditorToggler").hide();
            $("#questionTemplate").contents().find("#q4").html(Q4Array[index]);
            $(".groupQues").append($("#questionTemplate").html());
            if (loopCounter % parseInt(noOfColumns) === 0)
                $(".groupQues").append('<div style="clear:both;">');
            if ($("#tmpMode").val() != "NCERT")
                Q1 = parseInt(Q1) + 1;
        });
        $(".groupQues .singleQuestion").addClass("column" + noOfColumns);
        $(".groupQues").append('<div style="clear:both;">'); //This should be added if noOfQuestions is not divisible by noOfColumn...
        
        $("#msAsStudentInfo").html(footerBar);
        
        // -------- Displaying Autosaved Answers ------------------------
        if ($("#tmpMode").val() == "NCERT") {
            var correctAnsArr = new Array();
            correctAnsArr = correctAns1.split("##");
            var Q7Arr = Q7.split("##");
            $(".correct,.wrong").removeClass("correct").removeClass("wrong");
            if (Q8 == 1) {
                resultArr = Q9.split("##");
                var answerToShow = voiceover1;
                var answerToShowArr = new Array();
                answerToShowArr = answerToShow.split("##");
            }
            $(".singleQuestion.column1").each(function (index, element) {
                if (Q8 == 1) {
                    var applyClass = (resultArr[index] == 1) ? "correct" : "wrong";
                    var applyClassInput = (resultArr[index] == 1) ? "correctblank" : "incorrectblank";
                    // For unreviewed open ended question..
                    if (resultArr[index] != 3) {
                        $(this).contents().find("td:first div").addClass(applyClass);
                        $(this).find(".question input").addClass(applyClassInput);
                        $(this).find(".question select").addClass(applyClassInput);
                    }
                    if (answerToShowArr[index])
                        $(this).append(i18n.t("questionPage.correctAnswer") + ": " + nAth(answerToShowArr[index]));
                }
                if (Q7Arr[index] && $.trim(Q7Arr[index]) != "@$*@") {
                    var combinedRespArray = Q7Arr[index].split("[eeresponse]");
                    var autoSavedAns = combinedRespArray[0].split("|");
                    var correctAnsPerBlankArr = nAth(correctAnsArr[index]).split("|");
                    var equationEditorSavedAns = combinedRespArray[1].split("@$*@data");
                    var inputCounter = 0;
                    $(this).contents().find("select").each(function () {
                        if($.trim(autoSavedAns[inputCounter]) != "" && autoSavedAns[inputCounter] != null && typeof(autoSavedAns[inputCounter]) != "undefined")
                            $(this).find("option:contains('" + autoSavedAns[inputCounter] + "')").attr("selected", true);
                        inputCounter++;
                        if (Q8 == 1)
                            $(this).attr("disabled", "disabled");
                    });
                    $(this).contents().find("input,iframe").each(function () {
                        if ($(this).hasClass("openEnded")) {
                            $(this).load(function () {
                                $(this)[0].contentWindow.editable.innerHTML = equationEditorSavedAns[0];
                                $(this)[0].contentWindow.restoreImage("data" + equationEditorSavedAns[1]);
                            });
                        }
                        if ($(this).attr("type") == "text") {
                            if(Q8 == 1 && resultArr[index] == 0)
                            {
                                var fracboxCheck = false;
                                if($(this).hasClass("customfrac"))
                                    fracboxCheck = true;
                                var result = newQues[index].checkIndividualBlank(correctAnsPerBlankArr[inputCounter],autoSavedAns[inputCounter],fracboxCheck,inputCounter);
                                var applyClass = (result == 1) ? "correct" : "wrong";
                                var applyClassInput = (result == 1) ? "correctblank" : "incorrectblank";
                                $(this).addClass(applyClassInput);                                
                            }
                            $(this).val(autoSavedAns[inputCounter]);
                            inputCounter++;
                        }
                        else if ($(this).attr("type") == "radio") {
                            if ($(this).val() == autoSavedAns[inputCounter]) {
                                $(this).attr('checked', true);
                                inputCounter++;
                            }
                        }
                        if (Q8 == 1)
                            $(this).attr("disabled", "disabled");
                    });
                }
            });
        }
        // -------- Displaying Autosaved Answers ------------------------

        $(".groupQues").show();
        if ($("#tmpMode").val() != "NCERT")
            Q1 = parseInt(Q1) - 1;
        document.getElementById("qno").value = Q1;
        if ($("#tmpMode").val() == "NCERT" && Q8 == 1)
            hideSubmitBar();
        else
            showSubmitButton();
        var idNo = 1;
        $(".fracBox").each(function (index, element) {
            $(this).attr("id", "fracB_" + idNo);
            $(this).next("input").attr("id", "fracV_" + idNo);
            $(this).next().next("input").attr("id", "fracS_" + idNo);
            idNo++;
        });
    }
    //End pending integration */
    if (isIpad || isAndroid) {
        keypadPresent = 1;
        attachKeypad('questions');
    }
    document.getElementById("problemid").value = problemid1;
    if (document.getElementById("subject"))
        document.getElementById("subject").value = problemid1;
    document.getElementById('pnlQuestion').style.display = "";
    document.getElementById('noOfTrialsTaken').value = 0;
    clearTimeout(slowLoadTimer);

    makeQuestionVisible();
    if($("#tmpMode").val() == "NCERT") {
        $('iframe#eqeditor').each(function() {
            if(!this.contentWindow.storeAnswer) {
                var equationEditorUrl = this.src;
                this.src = '';
                this.src = equationEditorUrl;
            }
        });
    }
    document.getElementById("nextQuesLoaded").value = 0;
    if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "bonusCQ") {
		$("#questionImage").attr("title","");
        
        $('#questionType').css("display", "block");
        $('.circle1').hide();
        $('#questionImage').css("background", "url('assets/lowerClass/challenge.png') no-repeat -2px 0");
        if ($("#childClass").val() >= 8) {
            $('#questionImage').css("background", "url('assets/higherClass/challenge.png') no-repeat -2px 0");
        }

        $("#questionImage").click(function () {
            if (document.getElementById("b1"))
                document.getElementById('b1').focus();
        });

        $("#QT").html(i18n.t("questionPage.CQ"));
    }
    //if (document.getElementById('pnlWC'))
    if ($("#quesCategory").val() == "wildcard") {
        $('#questionType').css("display", "block");
        $('.circle1').hide();
        $('#questionImage').css("background", "url('assets/lowerClass/wildcard.png') no-repeat -2px 0");
        if ($("#childClass").val() >= 8) {
            $('#questionImage').css("background", "url('assets/higherClass/wildcard.png') no-repeat -2px 0");
        }

        $("#questionImage").attr("title", i18n.t("questionPage.questionImageTitle"));
        
        $("#questionImage").click(function () {
			if ($("#quesCategory").val() == "wildcard")			
            	openWildcardInstruction();
        });

        $('#questionType').css('cursor', 'pointer');

        $("#QT").html(i18n.t("questionPage.wildCard"));
    }

    if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "bonusCQ" || $("#quesCategory").val() == "wildcard" || $("#quesCategory").val() == "practiseModule") {
        if (infoClick == 1)
            $("#topic_name").hide();
        else {
            if ($("#quesStem").css("margin-top") == "-80px") {
                $("#quesStem").css("margin-top", "-30px");
            }
        }
    }
    else {
        $("#topic_name").show();
        $('.circle1').show();
        if ($("#quesStem").css("margin-top") == "-30px") {
            $("#quesStem").css("margin-top", "-80px");
        }
    }

    if (!timedTestPrompt) $('#pnlLoading').css("display", "none");
    $('#result').val("");
    //loadbuddy(); //Pending integration - old buddy, if replaced with intelligent buddy, fun not needed
    adjustScreenElements();
    document.getElementById('questionText').scrollIntoView(1);


    $("#emotToolBar").contents().find("input:radio").removeAttr("disabled");
    $("#emotToolBar").contents().find("input:radio").removeAttr("checked");
    $("#emotToolBar").contents().find("label").removeClass("hoverClass");
    if (document.getElementById('quesType').value != "Blank" && document.getElementById('quesType').value != "D" && $("#quesCategory").val() != "daTest"  && $("#quesCategory").val() != "worksheet" )
        showGlossary(document.getElementById("qcode").value, "");

    if ($("#quesCategory").val() == "diagnosticTest" || $("#quesCategory").val() == "kstdiagnosticTest") {
        $("#skipQuestion,#skipQuestion1,#skipQuestion2").css("display", "block");
        $("#skipQuestion,#skipQuestion1,#skipQuestion2").text("Don't know the answer");
    }
    if($('#q2 iframe.constructionTool').length) {
        startConstructionTrail();
    }
    $('#sparkieTooltip').html(evaluateSparkieLogicText());
    if(($("#quesCategory").val()=='challenge' && $('#showAnswer').val()==1) || $('#quesCategory').val()=='wildcard')
        showSparkieTooltip();
    else if($("#quesCategory").val()=='normal' && $('#topicAttemptStatus').val()=='new') {
        showSparkieTooltip();
        $('#topicAttemptStatus').val('in_progress');
    }
    else if($("#quesCategory").val()=='topicRevision' && sparkieTooltip.autoShow) {
        showSparkieTooltip();
        sparkieTooltip.autoShow = false;
    }
    if(responseArray['comprehensiveModuleCompleted'])
    {
        var DTalertText = "Let us start "+responseArray['clusterName']+".<br>";
        var DTlabel = 'Continue';                                  
        diagnosticTestPromptPromptfn(DTalertText,DTlabel);
    } 
}

function makeQuestionVisible()                //Code to set focus on first textbox
{
    if (document.getElementById('b1')) {
        $(window).scrollTop(0);
        document.getElementById('b1').focus();
    }
    else
        $(window).scrollTop(0);
    disableSubmitButton();
    checkLoadingComplete();

    if ($('#quesVoiceOver').val() != '') {
        var one = document.getElementById('one');
        one.load();
        one.addEventListener('ended', voicePlayFinished);
    }
    document.onselectstart = function () { return false; } // ie

    allowed = 1;
    try {
        jsMath.Process(document);
    } catch (err) { };
}
function submitAnswer(quesCatg) {
   
    if (practiseModuleTimeStatus==1){
        setTryingToUnload();
        $("#prmptContainer_resultPrompt").remove();
        window.location.assign("home.php");return;
    }
    if(quesCatg == 'No Answer')
    {
        if (newQues.quesType.substring(0, 3) == "MCQ")
            var data = $('.optionActive').text();
        if(data)
            quesCatg = data;
    }

    tryNo = 1;
    $('#quesStem').css("height", "auto");

    if (quesCatg == "skipped" && $("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest" && $("#quesCategory").val() != "challenge") {
        var questionCode = $("#qcode").val();
        if (skipQuestions.indexOf(questionCode) < 0)
            return false;
    }
    currentCluster = $('#clusterCode').val();
    if(typeof responseArray["quesCategory"]!='undefined' && responseArray["quesCategory"] != "")     // for the mantis task 12910
    {
        $("#quesCategory").val(responseArray["quesCategory"]);
    }
    
    if ($("#quesCategory").val() == "diagnosticTest" && quesCatg == "skipped") {
        //Come back here
        $("#result").val(0);
        $("#mode").val("submitAnswer");
        var params = $("#quesform").find("input").serialize();
        getNextQues(params, "normal");
        $("#secsTaken").val(0);
        handleClose();
        return false;
    } else if ($("#quesCategory").val() == "kstdiagnosticTest" && quesCatg == "skipped"){
        $("#result").val(2);
        $("#mode").val("submitAnswer");
        var params = $("#quesform").find("input").serialize();
        getNextQues(params, "normal");
        $("#secsTaken").val(0);
        handleClose();
        return false;
    }
    else if (quesCatg == "skipped") {
        var result = 3;
        var userAnswer = "skipped";
        document.getElementById('userResponse').value = userAnswer;
        calcAns(result, newQues.quesType);
        handleClose();
        return false;
    }

    childCls = $("#childClass").val();
    prevQcode = $("#qcode").val(); // For Recording previous QCode...
    prevQno = $('#qno').val();
    prevDynamicParams = $("#dynamicParams").val();
    prevQuesCategory = $('#quesCategory').val();
    prevQuesSubCategory = $("#tmpMode").val();
    submitCheck = 1;
    if($("#quesCategory").val() != "worksheet") hideCommentBox();

    prevQuesCategory = $("#quesCategory").val(); // For Recording previous Question Category...
    allowed = 0;
    worksheetFetchNext=true;
    try {
        if (document.getElementById("tmpMode").value != "NCERT") {
            if (newQues.quesType.substring(0, 3) != "MCQ")
                $("#toughQuestionClick,#blackScreen").css("display", "none");

            disableSubmitButton();
            if (newQues.eeIcon == "1") {

                var eeLine = "";
                try {
                    eeLine += $("iframe.openEnded")[0].contentWindow.storeAnswer('') + "@$*@";
                    eeLine += $("iframe.openEnded")[0].contentWindow.tools.save();
                }
                catch (ex) {
                }
                if (eeLine != "")
                    $("#eeResponse").val(eeLine);
            }
            if (newQues.quesType == 'D') {  /*alert("1");*/
                var allDropDownsAnswered = 1;
                var objArray = document.getElementsByTagName("select");
                var ans = new Array();
                var ansByVal = new Array();
                var ansBlank = new Array();
                var realAnsBlank = new Array();
                for (var i = 0; i < objArray.length; i++) {
                    if (objArray[i].id.substr(0, 6) == "lstOpt") {
                        ans[objArray[i].id.substr(6)] = objArray[i].selectedIndex;
                        ansByVal[objArray[i].id.substr(6)] = objArray[i].value;
                        if (objArray[i].value == "")
                            allDropDownsAnswered = 0;
                    }
                }
                if (newQues.noOfBlanks > 0) {
                    var fracboxCheck = new Array();
                    var b = '', f = '';
                    for (var j = 0; j < newQues.noOfBlanks; j++) {
                        fracboxCheck[j] = false;
                        var blankno = j + 1;
                        var objStr = 'b' + blankno;
                        if (document.getElementById(objStr)) {
                            if ($("#" + objStr).hasClass("customfrac")) {
                                //f = document.getElementById('b' + blankno).value;
                                b = document.getElementById('b' + blankno).value;
                                //b = stripFrac(document.getElementById('b' + blankno).value);
                                fracboxCheck[i] = true;
                            }
                            else
                                b = document.getElementById(objStr).value;
                        }
                        else {
                            if ($('#fracB_' + blankno).hasClass('fracboxvalue')) {
                                //b = $('#fracV_' + blankno).val();
                                b = $("iframe#fracB_" + blankno)[0].contentWindow.getData();
                                fracboxCheck[j] = true;
                            }
                            else {
                                /*f = $('#fracV_' + blankno).val();
                                b = $('#fracS_' + blankno).val();*/
                                b = $("iframe#fracB_" + blankno)[0].contentWindow.getData();
                            }
                        }
                        if (f == '')
                            realAnsBlank.push(b);
                        else
                            realAnsBlank.push(f);
                        ansBlank.push(b);
                    }
                }
                var blankUserRealAns = "";
                var ddlUserAns = ans.join('|');
                var ddlUserAnsByVal = ansByVal.join('|');
                var blankUserAns = ansBlank.join('|');

                if (realAnsBlank.length != 0)
                    var blankUserRealAns = realAnsBlank.join("|");
                else
                    var blankUserRealAns = "";
                if (blankUserAns != "") {
                    if (blankUserRealAns == "")
                        document.getElementById('userResponse').value = ddlUserAnsByVal + "|" + blankUserAns;
                    else
                        document.getElementById('userResponse').value = ddlUserAnsByVal + "|" + blankUserRealAns;
                }
                else
                    document.getElementById('userResponse').value = ddlUserAnsByVal;
                if (allDropDownsAnswered)
                    var result = newQues.checkAnswerDropDown(ddlUserAns, blankUserAns.split("|"), fracboxCheck);
                else
                    var result = 2;
                calcAns(result, newQues.quesType);
            }
            else if (newQues.quesType == 'Blank') { /*alert("2");*/
                var userAns = new Array();
                var userRealAns = new Array();
                var fracboxCheck = new Array();
                var isNext = 2;         
                for (var i = 0; i < newQues.noOfBlanks; i++) {

                    var b = "";
                    var f = "";
                    fracboxCheck[i] = false;
                    if (document.getElementById('b' + (i + 1) + '')) {
                        if ($("#b" + (i + 1)).hasClass("customfrac")) {
                            f = document.getElementById('b' + (i + 1)).value;
                            b = stripFrac(document.getElementById('b' + (i + 1)).value);
                            fracboxCheck[i] = true;
                        }
                        else
                            b = document.getElementById('b' + (i + 1)).value;
                    }
                    else {
                        if ($('#fracB_' + (i + 1)).hasClass('fracboxvalue')) {
                            //b =   document.getElementById('fracV_'+(i+1)).value;
                            b = $("iframe#fracB_" + (i + 1))[0].contentWindow.getData();
                            fracboxCheck[i] = true;
                        }
                        else {
                            //b =   document.getElementById('fracS_'+(i+1)).value;
                            b = $("iframe#fracB_" + (i + 1))[0].contentWindow.getData();
                        }
                        //f =   document.getElementById('fracB_'+(i+1)).value;
                    }
                    if (f == "")
                        userRealAns.push(b);
                    else
                        userRealAns.push(f);

                    userAns.push(b);
                }
                var conditionCheck = 0;
                var priority = 0;
                var userAnswer = userAns.join("|");

                if (userRealAns.length != 0)
                    var userRealResponse = userRealAns.join("|");
                else
                    var userRealResponse = "";

                if (userRealResponse == "")
                    document.getElementById('userResponse').value = userAnswer;
                else
                    document.getElementById('userResponse').value = userRealResponse;

                var result = newQues.checkAnswerBlank(userAnswer.split("|"), fracboxCheck, true);

                if($("#quesCategory").val() == "worksheet")
                {
                    worksheetFetchNext=calcAns(result, newQues.quesType);
                    $("#nextDAQuesLoaded").val(1);
                    $('.incorrectblank').removeClass('incorrectblank');
                    $('.correctblank').removeClass('correctblank');
                }
                else{
                    if (noOfCondition > 0) {
                        if (totalAttempts > 0) {
                            var result = newQues.checkAnswerBlank(userAnswer.split("|"), fracboxCheck, true);


                            if (totalAttempts == 0) {
                                var result = newQues.checkAnswerBlank(userAnswer.split("|"), fracboxCheck, true);


                            }
                            for (var p = 1; p <= 3; p++) // looping through priorities of the conditions
                            {
                                var isNext = 0;
                                conditionCheck = 0;
                                for (var i = 0; i < noOfCondition; i++) {
                                    parser.inputValues(userAnswer, ano);
                                    if (parser.parse(condition[i]) == 1) {
                                        action1 = action[i];
                                        if (/Mark Right/i.test(action1)) {
                                            priority = 1;
                                        } else if (/Mark Wrong/i.test(action1)) {
                                            priority = 2;
                                        } else {
                                            priority = 3;
                                        }
                                        if (priority == p) {
                                            if (action1 == "Mark Right") {
                                                result = 1;
                                                calcAns(result, newQues.quesType);
                                                isNext = 1;
                                            }
                                            else if (action1 == "Mark Wrong") {
                                                result = 0;
                                                calcAns(result, newQues.quesType);
                                                isNext = 1;
                                            }
                                            else if (action1.search(/Mark Right$/) != -1) {
                                                var str = action1.substring(7, action1.length - 17);
                                                if(!$(".promptContainer").is(":visible"))
                                                {   
                                                    var prompts = new Prompt({
                                                        text: str,
                                                        type: 'alert',
                                                        promptId: "markRightCondition1",
                                                        func1: function () {
                                                            jQuery("#prmptContainer_markRightCondition1").remove();
                                                        }
                                                    });
                                                }
                                                result = 1;
                                                calcAns(result, newQues.quesType);
                                                isNext = 1;
                                            }
                                            else if (action1.search(/Mark Wrong$/) != -1) {
                                                var str = action1.substring(7, action1.length - 17);
                                                if(!$(".promptContainer").is(":visible"))
                                                {
                                                    var prompts = new Prompt({
                                                        text: str,
                                                        type: 'alert',
                                                        promptId: "markWrongCondition1",
                                                        func1: function () {
                                                            jQuery("#prmptContainer_markWrongCondition1").remove();
                                                        }
                                                    });
                                                }
                                                result = 0;
                                                calcAns(result, newQues.quesType);
                                                isNext = 1;
                                            }
                                            else {
                                                var str = action1.substring(7, action1.length - 2);
                                                if(!$(".promptContainer").is(":visible"))
                                                {
                                                    var prompts = new Prompt({
                                                        text: str,
                                                        type: 'alert',
                                                        promptId: "condition2",
                                                        func1: function () {
                                                            jQuery("#prmptContainer_condition2").remove();
                                                        }
                                                    });
                                                }
                                                isNext = 1;
                                                break;
                                            }
                                        }
                                        allowCommonError = false;
                                    }
                                    else {
                                        conditionCheck++;
                                        if (conditionCheck == noOfCondition) {
                                            conCheck = 1;
                                        }
                                        if (conCheck == 1) {
                                            isNext = 2;
                                            break;
                                        }
                                    }
                                }
                                if (isNext == 1) {
                                    break;
                                }
                            }
                            ano++;
                            totalAttempts = totalAttempts - 1;
                        }
                        else {
                            isNext=2;
                        }
                    } 
                    if((newQues.commonError.userAlert!="" && typeof newQues.commonError.userAlert!="undefined") && previousCommonError!=newQues.commonError.errorCode && allowCommonError === true){
                        if(!$(".promptContainer").is(":visible"))
                        {
                            var prompts = new Prompt({
                                text: newQues.commonError.userAlert,
                                type: 'alert',
                                promptId: "commonError1",
                                func1: function () {
                                    jQuery("#prmptContainer_commonError1").remove();
                                }
                            });
                        }
                        isNext=2;
                        previousCommonError=newQues.commonError.errorCode;
                        newQues.commonError="";
                    }
                    else if(isNext==2) {
                        calcAns(result, newQues.quesType);
                        previousCommonError="";
                    }
                }
            }
            else if (newQues.quesType != "I") {
                if (document.getElementById('userResponse').value == "") {

                    stopAnswerTimer();
                    timeToAnswer = 0;
                    var userAns = "";
                    if (arguments.length > 0)
                        userAns = arguments[0];
                    else 
                        userAns=$('.optionActive').text();
                    $('.optionX').removeClass("optionActive");
                    if (userAns=='No Answer' && $("#quesCategory").val() == "worksheet") userAns="";
                    
                    markOption(userAns);
                    if ($("#quesCategory").val() == "diagnosticTest" || $("#quesCategory").val() == "kstdiagnosticTest")
                        userAns = optionDiagnosticArray[userAns];
                    document.getElementById('userResponse').value = userAns;
                    
                    if($("#quesCategory").val() == "daTest")
                    {
                        if($("#"+prevQno+"box").hasClass("daPagingYellow") && $("#daFlagCheck").attr('checked')) 
                        {
                            $("#prevDAQuesFlag").val(1);
                        }
                        
                        $('#daFlagCheck').attr('checked', false);

                        if($("#qno").val() != $('#daTestQueCount').val())
                            $('#DASubmitTestBetween').val(1);

                        if($("#daFlagCheck").is(':checked'))
                            check = 'yes';
                        
                        if($("#qno").val() != $('#daTestQueCount').val())
                            calcAns(newQues.checkAnswerMCQ(userAns), newQues.quesType);
                        else
                            saveDALastAnswer();
                        
                        $("#nextDAQuesLoaded").val(1);
                    }
                    else if($("#quesCategory").val() == "worksheet")
                    {
                        // if($("#qno").val() != $('#daTestQueCount').val())
                        //     $('#DASubmitTestBetween').val(1);

                        //if($("#qno").val() != $('#daTestQueCount').val())
                            worksheetFetchNext=calcAns(newQues.checkAnswerMCQ(userAns), newQues.quesType);
                        //else
                            //saveDALastAnswer();
                        
                        $("#nextDAQuesLoaded").val(1);
                    }
                    else
                    {
                        calcAns(newQues.checkAnswerMCQ(userAns), newQues.quesType);
                    }
                }
            }
            else {
                if (typeof $("#quesInteractive").attr("src") != "undefined") {
                    var result = "";
                    try {
                        var frame = document.getElementById("quesInteractive");
                        var win = frame.contentWindow;
                        win.postMessage("checkAnswer", '*');
                    }
                    catch (ex) {
                        alert('error in getting the response from interactive');
                    }
                }
                else {
                    var flashMovie = getFlashMovieObject("simplemovieQ");
                    var result = flashMovie.GetVariable("answer");
                    calcAns(result, newQues.quesType);

                }
            }
            //
        }
        else {
            document.getElementById('pnlQuestion').scrollIntoView(1);
            results = evalGroupAnswers();
            var answerToShow = voiceover1;
            if (results != false) {
                if ($("#tmpMode").val() == "NCERT") {
                    $(".current").removeClass("pending").addClass("complete");
                }
                allowed = 1;
                var questionCount = 0;
                $('#quesform').attr("disabled", false);
                $(".singleQuestion").each(function () {
                    $(this).contents().find("input, select, textarea").each(function () {
                        $(this).attr("disabled", "disabled");
                    })
                })
                hideSubmitBar();
                var resultArr = new Array();
                resultArr = results.split("##");
                var answerToShowArr = new Array();
                answerToShowArr = answerToShow.split("##");
                var ncert_temp = 1;
                $(".singleQuestion").each(function () {
                    questionCount++;
                    var applyClass = (resultArr[questionCount - 1] == 1) ? "correct" : "wrong";
                    // var applyClassInput = (applyClass == "correct") ? "correctblank" : "incorrectblank";
                    if (resultArr[questionCount - 1] != 3) // Dont show answer is correct or wrong in case of open ended..
                    {
                        $(this).contents().find("td:first div").addClass(applyClass);
                        if (answerToShowArr[questionCount - 1]) {
                            $(this).append("<span id='ncertDispAns"+ncert_temp+"'>"+i18n.t("questionPage.correctAnswer") + ": " + nAth(answerToShowArr[questionCount - 1])+"</span>");
                            try {
                                jsMath.ProcessBeforeShowing(document.getElementById('ncertDispAns'+ncert_temp));
                            } catch (err) { };
                        }
                        ncert_temp++;
                            
                        // $(this).find(".question input").addClass(applyClassInput);
                        // $(this).find(".question select").addClass(applyClassInput);
                    }
                })
                showNextButton();
                $("#mode").val("submitAnswer");
                $('#secsTaken').val(secs);

                $("#result").val(results);

                var params = $("#quesform").find("input").serialize();
                getNextQues(params, "normal");
            }
        }
    } catch (err) {
        $.post("errorLog.php", "params=JSerror-" + encodeURIComponent(JSerrors) + "error-" + err + " - " + $("#quesform").find("input").serialize() + "&qcode=" + $("#qcode").val() + "&typeErrorLog=9", function (data) {

        });
    }
    if ($("#quesCategory").val() == "daTest") {

        $("#pnlAnswer").hide();

        var checkBorderColor = $("#"+prevQno+"box").css('border-top-style');
        var queattemptColor = $( "#"+prevQno+"box" ).css( "border-left-color" );
            
         $($("#userResponse").val()).addClass( "optionActive" );
         $(".optionX").addClass("optionInactive");
         $($("#userResponse").val()).removeClass( "optionActive" );
         $("#userResponse,#extraParameters").val("");
         $("#nextQuesLoaded").val("0");

         if($("#qno").val() != $('#daTestQueCount').val())
                handleClose();
    }
    if ($("#quesCategory").val() == "worksheet") {

        $("#pnlAnswer").hide();

        var checkBorderColor = $("#"+prevQno+"box").css('border-top-style');
        var queattemptColor = $( "#"+prevQno+"box" ).css( "border-left-color" );
            
         /*$($("#userResponse").val()).addClass( "optionActive" );
         $($("#userResponse").val()).removeClass( "optionActive" );*/
         $(".optionX").addClass("optionInactive");
         $("#userResponse,#extraParameters").val("");
         $("#nextQuesLoaded").val("0");
         if (worksheetFetchNext!==false) fetchNextQues();
         /*if($("#qno").val() != $('#daTestQueCount').val())
                handleClose();*/
    }
}

function setWSColorCode(quesAttemptArray,nextQues)
{
    $("#daQcodeListBox .daPagingBlue").removeClass("daPagingBlue");
    $.each(quesAttemptArray, function(i,v) { 
        if (v === 1) {
            $("#"+(i+1)+"box").addClass("daPagingGrey");
        }
        else {
            $("#"+(i+1)+"box").addClass("daPagingnormal");
        }
    });
    $("#"+nextQues+"box").removeAttr('class');
    $("#"+nextQues+"box").addClass("daPagingBlue");
    
    var curPage=Math.floor((nextQues-1)/10);
    $('#daQcodeListBox').attr('data-showing',curPage);
    $('#pre_box,#next_box').removeClass('inactive');
    var newPosDANav=$('#daQcodeListBox').attr('data-showing')*10*-40;
    if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-min')){
        $('#pre_box').addClass('inactive');newPosDANav=0;
    }
    if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-max')){
        $('#next_box').addClass('inactive');
        newPosDANav=($('#daQcodeListBox').attr('data-showing')!=$('#daQcodeListBox').attr('data-min'))?$('#daQcodeListBox').attr('data-quesCount')*-40+400:0;
    }
    $('#daQcodeListBox table').css('left',(newPosDANav)+'px');

    if($("#daFlagCheck").attr('checked'))
        $("#prevDAQuesFlag").val(1);
    else
        $("#prevDAQuesFlag").val(0);
}
function setDaColorCode(curQues,nextQues,check)
{
    //alert(curQues + '**' +nextQues + '--' + $('.optionActive').text());

    if($("#prevDAQuesFlag").val() == 1 && check == "yes")
    {
        $("#"+curQues+"box").removeClass("daPagingGrey");
        $("#"+curQues+"box").addClass("daPagingYellow");    
    }
     
    if ($("#"+nextQues+"box").hasClass("daPagingYellow"))
    {
         $('#daFlagCheck').attr('checked', true);
         $("#prevDAQuesFlag").val(1);
    }

    $("#"+nextQues+"box").removeAttr('class');
    $("#"+nextQues+"box").addClass("daPagingBlue");
    if ($("#"+nextQues+"box").hasClass("daPagingYellow")) {
         $('#daFlagCheck').attr('checked', true);
         $("#prevDAQuesFlag").val(1);
    }
    else if($("#"+curQues+"box").hasClass("daPagingBlue") && curQues != nextQues)
    {
        $("#"+curQues+"box").removeClass("daPagingBlue");
        if($('.optionActive').text()!="")
        {
                if(!$("#"+curQues+"box").hasClass("daPagingYellow"))
                {
                    $("#"+curQues+"box").addClass("daPagingGrey");
                    $("#"+curQues+"box").css("cursor","pointer");
                }
        }
        else
        {
                $("#"+curQues+"box").addClass("daPagingred");
        }

    }
    var curPage=Math.floor((nextQues-1)/10);
    $('#daQcodeListBox').attr('data-showing',curPage);
    $('#pre_box,#next_box').removeClass('inactive');
    var newPosDANav=$('#daQcodeListBox').attr('data-showing')*10*-40;
    if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-min')){
        $('#pre_box').addClass('inactive');newPosDANav=0;
    }
    if ($('#daQcodeListBox').attr('data-showing')==$('#daQcodeListBox').attr('data-max')){
        $('#next_box').addClass('inactive');
        newPosDANav=($('#daQcodeListBox').attr('data-showing')!=$('#daQcodeListBox').attr('data-min'))?$('#daQcodeListBox').attr('data-quesCount')*-40+400:0;
    }
    $('#daQcodeListBox table').css('left',(newPosDANav)+'px');

    if($("#daFlagCheck").attr('checked'))
        $("#prevDAQuesFlag").val(1);
    else
        $("#prevDAQuesFlag").val(0);
}


function calcAns(result, quesType) {
    trialTough = 0
    globalProcessResult = result;
    if (storageEnabled()) localStorage.setItem("globalResult", globalProcessResult);
    globalProcessquesType = quesType;
    var isTough = document.getElementById("toughType").value;

    if (result == 2 && ($('#quesCategory').val()!='worksheet' || ($('#quesCategory').val()=='worksheet' && $('#submitWorksheet').val()==1))) {
        if(!$(".promptContainer").is(":visible"))
        {
            var prompts = new Prompt({
                text: i18n.t("questionPage.answerNotGivenMsg"),
                type: 'alert',
                promptId: "specifyAnswer",
                func1: function () {
                    jQuery("#prmptContainer_specifyAnswer").remove();
                }
            });
        }
        enableSubmitButton();
        allowed = 1;
        $('#result').val("");
        $('#submitWorksheet').val(0);
        return false;
    }

    if (isTough == "TOUGH" && $('#quesCategory').val()!='worksheet' && Q2.indexOf('ADA_eqs/src/index.html')==-1)
        toughQues = 1;
    else
        toughQues = 0;
    if (toughQues == 1 && toughDisabled == 0 && $('#quesCategory').val()!='worksheet') {
        if (toughQuestionStatus == 0) {
            toughFlag = 1;
            toughQuesInstances++;
            var temp = localStorage.getItem("toughInstances");
            temp = parseInt(temp);
            if (storageEnabled()) localStorage.setItem("toughInstances", parseInt(temp) + 1);
            toughQuesInstancesConsecutive++;
            if ((temp + 1) > 5 || toughQuesInstancesConsecutive > 3)
                document.getElementById('toughCheck').style.display = 'block';
            toughQuestionStatus++;
            document.getElementById('toughResult').value = result;
            if (giveResult == 0) {
                var noOfBlanks = newQues.noOfBlanks;
                for (var i = 0; i < noOfBlanks; i++) {
                    document.getElementById("b" + (i + 1)).className = "";
                }
            }
            toughQuestionAlert();
            return false;
        }

        if (toughQuestionStatus1 == 1) {
            trialTough = 1;
            toughQuestionStatus1 = 0;
            $('#toughType').val("Tough Question");
            if (newQues.quesType.substring(0, 3) == "MCQ") {
                $('.optionX').removeClass("optionActive");
                $('.optionX').addClass("optionInactive");
                $('.optionX').attr("disabled", "true");
                $('.optionX').removeAttr("disabled");
            }
            newQues.noOfTrialsAllowed++;
        }

    }
    if (!toughFlag)
        toughQuesInstancesConsecutive = 0;
    stopTheTimerTough();
    document.getElementById('timeTakenToughQues').value = timeTakenToughAlert + "|" + timeTakenToughAttempt;
    timeTakenToughAttempt = 0;

    if (localStorage.getItem("toughDisabled") == 'true')
        toughDisabled = 1;

    if (document.getElementById("spnQuesCorrectEC") && $("#quesCategory").val() == "normal" && (result == 1 || result == 0)) {
        questionsDoneEC++;
        if (result == 1)
            quesCorrectEC++;
        $("#spnQuesCorrectEC").text(quesCorrectEC);
        $("#spnQuestionsDoneEC").text(questionsDoneEC);
    }
    if ($('#quesCategory').val()=='worksheet' && result==2)
        $('#result').val(-1);
    else 
        $('#result').val(result);

    if ($('#quesCategory').val()!='worksheet'){
        if (result > 3) {
            $.post("errorLog.php", "params=" + $("#quesform").find("input").serialize() + "&typeErrorLog=1", function (data) {

            });
        }
        else {
            if (result == 0 && newQues.noOfTrialsAllowed > 1 || (newQues.noOfTrialsAllowed > 1 && trialTough == 1)) {
                if (document.getElementById("spnQuesCorrectEC"))
                    questionsDoneEC--;
                var noOfTrialsTaken = parseInt(document.getElementById('noOfTrialsTaken').value);

                noOfTrialsTaken++;

                if (noOfTrialsTaken < newQues.noOfTrialsAllowed) {
                    document.getElementById('noOfTrialsTaken').value = noOfTrialsTaken;
                    $("#userAllAnswers").val($("#userAllAnswers").val() + $('#userResponse').val() + "$#@");
                    $('#result,#userResponse,#extraParameters').val('');
                    if (noOfCondition == 0) {
                        if (trialTough != 1)
                        {
                            if(!$(".promptContainer").is(":visible"))
                            {
                                var prompts = new Prompt({
                                    text: i18n.t("questionPage.reviewMsg"),
                                    type: 'alert',
                                    promptId: "toughQuestion",
                                    func1: function () {
                                        jQuery("#prmptContainer_toughQuestion").remove();
                                    }
                                });
                            }
                        }
                    }
                    if (newQues.quesType.substring(0, 3) == "MCQ") {
                        $('.optionX').addClass("optionInactive");
                    }
                    if (newQues.hintAvailable != 0) {
                        if (!$("#mainHint").is(":visible"))
                            $("#mainHint,#showHint").show(); //--show hints
                        $("#userResponse,#extraParameters").val("");
                        $("#usefullHint").show();
                    }
                    enableSubmitButton();
                    allowed = 1;
                    return false;
                }
            }

            if ($("#hintUsed").val() > 0) {
                var totalHints = newQues.hintAvailable;
                for (var f = 1; f <= totalHints; f++) {
                    if (document.getElementById("hintText" + f)) {
                        if ($("#hintText" + f).is(":visible")) {
                            timeTakenForAllHints = $("#timeTakenHints").val().split("||");
                            timeTakenForAllHints[f - 1] = parseInt(timeTakenForAllHints[f - 1]) + timeTakenToViewHint;
                            timeTakenForAllHintStr = timeTakenForAllHints.join("||");
                            $("#timeTakenHints").val(timeTakenForAllHintStr);
                            stopHintTimer();
                        }
                    }
                }
            }
            $("#mainHint,.hintDiv,#showHint,#hintText2,#hintText3,#hintText4,#prevHint,#nextHint").attr("style", "");
            $("#showHint,#usefullHint").hide();
            newAn(result, quesType, secs);
        }
    }
    $('#secsTaken').val(secs);
    secs = 0;
    $('#refresh').val("1");
    $('#mode').val("submitAnswer");
    var eeResponseByUser = "";
    if ($("iframe.openEnded").length > 0) {
        eeResponseByUser += $("iframe.openEnded")[0].contentWindow.storeAnswer('') + "@$*@";
        eeResponseByUser += $("iframe.openEnded")[0].contentWindow.tools.save();
    }
    else if ($("iframe.constructionTool").length > 0) {
        // eeResponseByUser = $("iframe.constructionTool")[0].contentWindow.drawcode.getDrawnShapes();
        eeResponseByUser = constrTool.trail;
    }
    /*else if ($("iframe.stepByStep").length > 0) {
        eeResponseByUser = $("#userResponse").val();
    }*/
    else {
        eeResponseByUser = "NO_EE"; // WHERE question does not contain equation editor, for this constant nothing will be saved..
    }
    $("#eeResponse").val(removeInvalidChars(eeResponseByUser));
    disableSubmitButton();

    if (ipadVersionCheck == true) {
        $("#sideBar").css("height", $("#pnlQuestion").css("height"));
    }

    if ($("#submitWorksheet").val()=='2') $("#submitWorksheet").val(1); //for endSession

    var params = $("#quesform").find("input").serialize();
    $('#getDirectQuestion').val(0);
    $('#submitWorksheet').val(0);

    if ($("#qcode").val() == "")    //exception, temp work around
    {
        $("#mode").val("nextAction");
        document.quesform.action = 'controller.php';
        setTryingToUnload();
        document.quesform.submit();
    }
    else {
        getNextQues(params, "normal");
        timeTakenAttemptArr[quesNoInFlow] = $("#secsTaken").val();
    }
    return true;
}


function newAn(result, ques_type, timeTaken) {
    var msg;
    //var frustrationMsg;
    var cAn = nAth(newQues.enA);
    /*try {
        var optArray = new Array('A', 'B', 'C', 'D');
        if (document.getElementById('ichar') && ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "normal")) {
            frustrationMsg = objFrustration.getFrustrationMsg(result, $("#quesCategory").val(), $('#qno').val(), timeTaken);
            if (frustrationMsg != "")
                logFrustrationMsgData(frustrationMsg, objFrustration.frustInst, objFrustration.F_current, $("#quesCategory").val(), $('#qno').val());
            var tmp = JSON.stringify(objFrustration);
            createCookie("mindspark", tmp, 0);
        }
    } catch (err) { }*/
    for (var j = 0; j < 7; j++) {
        if (document.getElementById('b' + j))
            document.getElementById('b' + j).disabled = true;
        if (document.getElementById('fracB_' + j)) {
            jQuery('#fracB_' + j).removeAttr('contenteditable').attr("disabled", "true");
        }
    }
    var dropDownObj = document.getElementsByTagName("select");
    for (var j = 0; j < dropDownObj.length; j++) {
        if (dropDownObj[j].id.substr(0, 6) == "lstOpt")
            dropDownObj[j].disabled = true;
    }
    var explanation = nAth(newQues.encryptedDispAns);
    
    if($("#quesCategory").val() == "practiseTest")
    {
        var score = document.getElementById('scorecard');
        var wrongquestioncount = document.getElementById('wrongquestioncount');
        var lifecount = document.getElementById('lifecount');
        if (result == 0)
        {
            var data = parseInt(wrongquestioncount.innerHTML);
            var wrongcount = data + 1;
            $("#wrongquestioncount").html(wrongcount);
            $("#lifecount").html(parseInt(lifecount.innerHTML) - 1);
        }
        if (result == 1) 
        {
            if(score.innerHTML < 100)
            {
                var data = parseInt(score.innerHTML);
                if(wrongquestioncount.innerHTML > 10)
                    var scorecount = parseInt(data) + 3;
                else
                    var scorecount = parseInt(data) + 4;
            }
            if(scorecount>100)
             {
                scorecount = 100;
             }
            $("#scorecard").html(scorecount);
         }
         else
         {
            if(score.innerHTML > 0)
            {
                var scorewcount = score.innerHTML - 2;
                wrongquestioncount++;
            }
            if(scorewcount<0)
            {
                scorewcount = 0;
            }
            $("#scorecard").html(scorewcount);
         }
        
        if(score.innerHTML == 100 || wrongquestioncount.innerHTML == 25 || scorecount == 100)
        {
    
            $('#endSessionClick').height(175);
            $('.button1').hide();
            $('.endSessionText').css('display', 'none');
            $('#loading').show();
            $('#loadingImage').show();
            if(wrongquestioncount.innerHTML == 25)
                var params = "mode=endpractisetestsession&action=continue";
            else
                var params = "mode=endpractisetestsession&action=done";
            try {
                var request = $.ajax('controller.php',
                                {
                                    type: 'post',
                                    data: params,
                                    success: function (transport) {
                                        if(wrongquestioncount.innerHTML == 25)
                                            alert("You ran out of energy. You can try again.");
                                        else
                                            alert("Congratulations. Now, you are an expert");
                                        finalSubmit(1);
                                    },
                                    error: function () {
                                        //alert('Something went wrong...');
                                    }
                                }
                                );
            }
            catch (err) { alert("Final Submit " + err.description); }
        }
    }
    var noPmDA=0;allowed=1;
    if (result == 1) {

        var msgArray = i18n.t("questionPage.correctMessages").split(",");
        var randomnumber = Math.floor(Math.random() * msgArray.length);
        msg = "";
        if ($("#quesCategory").val() == "challenge" || $("#quesCategory").val() == "bonusCQ")
            msg += i18n.t("questionPage.CQCorrectMsg") + "<br/>";
        else if ($("#quesCategory").val() == "wildcard") {
            if ($("#childClass").val() < 8 || rewardSystem == 1)
                msg += i18n.t("questionPage.WildCardCorrectMsg") + "<br/>";
            else
                msg += i18n.t("questionPage.WildCardCorrectMsgReward") + "<br/>";
        }
        if ($('#hasExpln').val() == 1 && $("#quesCategory").val() != "practiseModule")
            msg += "<br/><b>" + i18n.t("questionPage.answerText") + ": </b><br>" + explanation + "<br/><br/>";
        
        $("#feedback_header_img").removeClass("ques_incorrect ").addClass("ques_correct");
        $("#feedbackContainer_correct").removeClass("feedbackContainer_incorrect"); 
        if($("#quesCategory").val() == "practiseModule" ){
            iTargetSpeed = parseInt($("#iTargetSpeed").val());
            $("#currentScore").html(iTargetSpeed);  
            if ($('#fromChoiceScreen').length==0 || $('#fromChoiceScreen').val()!="1") {
                $("#feedback_header").css("font-size", "0.9em");
                $("#feedback_header_img").css({'background-size': 'cover', 'width': '50px', 'height': '50px'});
                $('#feedbackContainer_correct').css({'height':'66px','width':'45%','margin-left':'55%'});
                setTimeout(handleClose,1200);allowed=0;
                noPmDA=1;
            }
            /*$(".dd_feedback_header").show();
            $(".dd_feedback_container").css("background","#9EC955");
            $(".dd_feedback_responce").html(msgArray[randomnumber]);
            $("#feedbackContainer_correct").hide();
            $(".wrong_answer").hide();
            $(".correnct_answer").show();
            $(".dd_feedback_header").delay(3000).fadeOut("slow");
        }else{*/
        }
            
            $("#feedback_header").html(msgArray[randomnumber]);
        //$("#tl").html("<img src='images/right_smiley.png' style='vertical-align:middle'> " + msgArray[randomnumber] + "!");;
        //$("#hd").css("background-color","#00bc1b");
    }
    else if (result == 0) {

        var msgArray = i18n.t("questionPage.QuesIncorrectMsgs").split(",");
        var randomnumber = Math.floor(Math.random() * msgArray.length);
        var osda = ""; //option specific DA
        if (ques_type == 'MCQ-4' || ques_type == 'MCQ-3' || ques_type == 'MCQ-2') {
            tempResponse = $("#userResponse").val();
            var tmpAns = i18n.t("questionPage.osdaText", { userResponse: tempResponse });
            if (tempResponse == "A" && newQues.encryptedDispAnsA != "")
                osda = nAth(newQues.encryptedDispAnsA);
            else if (tempResponse == "B" && newQues.encryptedDispAnsB != "")
                osda = nAth(newQues.encryptedDispAnsB);
            else if (tempResponse == "C" && newQues.encryptedDispAnsC != "")
                osda = nAth(newQues.encryptedDispAnsC);
            else if (tempResponse == "D" && newQues.encryptedDispAnsD != "")
                osda = nAth(newQues.encryptedDispAnsD);
            if (osda != "")
                osda = tmpAns + "(" + i18n.t("questionPage.reason") + ":" + osda + ")";
            else
                osda = tmpAns;
        }
        //------End
        msg = "";

        if ($("#quesCategory").val() == "challenge" && $('#showAnswer').val() == 0)
            msg += "<br/>" + i18n.t("questionPage.CQIncorrectFirstAttemptMsg") + "<br/>";
        if ($("#quesCategory").val() == "normal" || $("#quesCategory").val() == "topicRevision" || $("#quesCategory").val() == "exercise" || $("#quesCategory").val() == "bonusCQ" || $("#quesCategory").val() == "wildcard" || ($("#quesCategory").val() == "challenge" && $('#showAnswer').val() == 1) || ($("#quesCategory").val() == "practiseModule" && $('#showAnswer').val() == 1))        //Show the display answer for normal questions only, not the challenge question.
        {
            if ($("#quesCategory").val() == "wildcard") {
                if ($("#childClass").val() < 8 || rewardSystem == 1)
                    msg += "You missed a sparkie.<br>";
                else
                    msg += "You missed 10 reward points.<br>";
            }
            msg += osda + "<br>";
            msg += i18n.t("questionPage.correctAnswer");
            if ($.trim(explanation) != cAn && $.trim(cAn)!=""){
                var correctAnswerString=[];
                $.each(cAn.split('|'),function(ind,itm){
                    correctAnswerString.push(itm.split('~')[0]);
                });

                //msg += " : <b>" + cAn.replace(/~/g,' or ').replace(/\|/g,' | ') + "</b>";
                msg += " : <b>" + correctAnswerString.join(', ') + "</b>";
            }
            else 
                msg += " : ";    
            msg += "<br>" + explanation;
            
        }
        if ($("#childClass").val() > 3)
            msg += "<br><br>";
        else
            msg += "<br>";  
        
        $("#feedback_header_img").removeClass("ques_correct ").addClass("ques_incorrect");
        $("#feedbackContainer_correct").addClass("feedbackContainer_incorrect");    
        
        if($("#quesCategory").val() == "practiseModule" ){
            if ($('#fromChoiceScreen').length==0 || $('#fromChoiceScreen').val()!="1") {
                $("#feedback_header").css("font-size", "0.9em");
                $("#feedback_header_img").css({'background-size': 'cover', 'width': '50px', 'height': '50px'});
                $('#feedbackContainer_correct').css({'height':'66px','width':'45%','margin-left':'55%'});
            }
            /*$(".dd_feedback_header").show();
            $(".dd_feedback_container").css("background","red");
            $(".dd_feedback_responce").html(msgArray[randomnumber]);
            $("#feedbackContainer_correct").hide();
            $(".wrong_answer").show();
            $(".correnct_answer").hide();
            $(".dd_feedback_header").delay(3000).fadeOut("slow");*/
        //}else{
        }
            $("#feedback_header").html(msgArray[randomnumber]);
        
        //$("#tl").html("<img src='images/wrong_smiley.png'  style='vertical-align:middle'> Sorry, that's incorrect!");
        //$("#hd").css("background-color","#CD5C5C");
        if (ques_type == 'D') {
            var objArray = document.getElementsByTagName("select");
            var ans = new Array();
            for (var i = 0; i < objArray.length; i++)
                ans[objArray[i].id.substr(6)] = objArray[i].selectedIndex;
            var cAn  = nAth(newQues.encryptedDropDownAns);
            cAn  = cAn.split('|');
            for (var j = 0; j < cAn.length; j++)
                if (cAn[j] != ans[j] && document.getElementById('spnOpt' + j))
                    document.getElementById('spnOpt' + j).style.color = 'red';
        }
    }
    //msg += i18n.t("questionPage.nextQuesMsg") + "<br/><br/>";
    if (noPmDA)
        $("#displayanswer").html('');
    else 
        $("#displayanswer").html(msg);
    if (document.getElementById('ichar')) {
        $("#ichar").addClass("buddyOpacity");
        updateBuddy(result, frustrationMsg);
    }

    document.getElementById('pnlAnswer').style.display = "none";
    try {
        jsMath.ProcessBeforeShowing(document.getElementById('displayanswer'));
    } catch (err) { };
    
    showNextButton();
    if(noPmDA)
        $('#submit_bar.daily-drill-top-bar #nextQuestion').hide();
    else 
        allowed = 1;
    if($("#quesCategory").val() != "worksheet") animateAnswerBox();
    if(dispalAnswerParam!="" && $("iframe:eq(1)"))
    {
        $("iframe:eq(1)").attr("src",$("iframe:eq(1)").attr("src")+dispalAnswerParam);
    }
    if(result==0 && $("#quesCategory").val() == "challenge" && $('#showAnswer').val() == 0)
    {
        // $("#pnlRateDa").hide(); 
        $("#pnlNextButtonInstruction").hide(); 
        $("#displayanswer").html(msg);
    }
    else if($("#displayanswer").html() !="" && $.trim(explanation)!="" && $("#quesCategory").val() != "practiseModule" && $('#hasExpln').val() == 1 && !($("#quesCategory").val() == "challenge" && $('#showAnswer').val() == 0))
    {
        /*if ($("#displayanswer").text().length > 50) 
            $("#pnlRateDa").show();
        else {
            $("#pnlRateDa").hide();
            $("#displayanswer").append('<span style="color:rgba(0,0,0,0.7);">Please click the Next Question button to continue.</span>');
        }*/
    }
    else
    {
        // $("#pnlRateDa").hide(); 
        $("#pnlNextButtonInstruction").hide(); 
        if($("#quesCategory").val() != "practiseModule")
            $("#displayanswer").html("Please click the Next Question button to continue.");
    }
    if($("#quesCategory").val() == "practiseModule" && !noPmDA)
        $("#displayanswer").append('<span style="color:rgba(0,0,0,0.7);">Please click the Next Question button to continue.</span>');
}
try {
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
    var frame = document.getElementById("quesInteractive");
    // Listen to message from child window
    eventer(messageEvent, function (e) {
        if (e.origin == "http://mindspark-ei.s3.amazonaws.com" || e.origin == "http://d2tl1spkm4qpax.cloudfront.net" || e.origin == "mindspark-ei.s3.amazonaws.com" || e.origin == "d2tl1spkm4qpax.cloudfront.net" || $("#offlineStatus").val()=="3" || $("#offlineStatus").val()=="4" || $("#offlineStatus").val()=="7" || e.origin == "https://mindspark-ei.s3.amazonaws.com" || e.origin == "https://d2tl1spkm4qpax.cloudfront.net") {
            var response1 = "";
            response1 = e.data;
            if (response1.indexOf("||") != -1) {
                dispalAnswerParam = "";
                response1Array = response1.split("||");
                result = parseInt(response1Array[0]);
                $('#userResponse').val(response1Array[1]);
                if(['', 'null', 'undefined'].indexOf(response1Array[2])==-1)
                    $('#extraParameters').val(response1Array[2].replace(/\|~\|/, '||'));
                if (typeof(response1Array[3]) !== 'undefined') {
                    dispalAnswerParam = response1Array[3];
                }
                if (result > 2) {
                    $.post("errorLog.php", "params=" + $("#quesform").find("input").serialize() + "&type=3", function (data) {

                    });
                }
                calcAns(result, newQues.quesType);
            }
            else if(response1.indexOf("frameHeight=") == 0) {
              frameHeight=response1.replace('frameHeight=','');
              $("#quesInteractive").attr('height',frameHeight);
            }
            else if(response1 == 'triggerSubmit') {
                submitAnswer();
            }
            else if(response1 == 'hideSubmit') {
                $("#submitQuestion").hide();
                $("#submitQuestion1").hide();
                $("#submitQuestion2").hide();
                $('#submit_bar').hide();
                $('#submit_bar1').hide();
                $('#submitArrow').hide();
            }
            else {
                var message;
                try {
                    message = JSON.parse(response1);
                } catch(error) {
                    message = {};
                }
                if(message.hasOwnProperty('subject')) {
                    switch(message.subject) {
                        case 'screenState': {
                            $('iframe').each(function() {
                                if(e.source===this.contentWindow) {
                                    toggleFullscreen(this);
                                    return false;
                                }
                            });
                            break;
                        }
                        case 'trail': {
                            constrTool.trail = message.content.trail;
                            break;
                        }
                    }
                }
            }
        }
    }, false);
}
catch (ex) {
    alert('error in getting the response from interactive');
}
// This Function Added For Practice Cluster..
// Evaluates all answers of questions in a group by its qustionType.(Only MCQ-2, MCQ-3,MCQ-4,Blank.) and Checks for Correctness..
function evalGroupAnswers() {
    var questionCount = newQues.length;
    var fracboxcount;
    var requireAns = new Array();
    var returnStr = "";
    var userAns = new Array();
    var userRealAns = new Array();
    var userDdlAns = new Array();
    var userDdlAnsByVal = new Array();
    var userResponse = new Array();
    var eeResponse = new Array();
    var fracboxCheck = new Array();
    var quesNo = 0;
    
    if(typeof(Q1) != "undefined")
        var Q1_array = Q1.split("##");
    else
        var Q1_array = new Array();
    
    $(".singleQuestion").each(function () {
        quesNo++;
        fracboxcount = 0;
        fracboxCheck[quesNo - 1] = new Array();
        if (quesNo > questionCount)
            return;
        var line = "";
        var userLine = "";
        var ddlLine = "";
        var ddlLineByVal = "";
        var eeLine = "";
        $(this).contents().find("select").each(function () {
            ddlLine += $(this).prop("selectedIndex") + "|";
            ddlLineByVal += $(this).val() + "|";
        });
        $(this).contents().find("input,iframe").each(function () {
            fracboxCheck[quesNo - 1][fracboxcount] = false;
            if ($(this).hasClass("openEnded")) {
                eeLine += $(this)[0].contentWindow.storeAnswer('') + "@$*@";
                eeLine += $(this)[0].contentWindow.tools.save();
            }
            else if ($(this).attr("type") == "text") {
                if ($(this).hasClass("customfrac")) {
                    line += stripFrac($(this).val()) + "|";
                    userLine += $(this).val() + "|";
                    fracboxCheck[quesNo - 1][fracboxcount] = true;
                    fracboxcount++;
                }
                else {
                    line += $(this).val() + "|";
                    userLine += $(this).val() + "|";
                }
            }
            else if ($(this).hasClass("fracBox")) {
                var txtbxID = $(this).attr("id");
                if ($('#' + txtbxID).hasClass('fracboxvalue')) {
                    line += $("iframe#" + txtbxID)[0].contentWindow.getData() + "|";
                    userLine += $("iframe#" + txtbxID)[0].contentWindow.getData() + "|";
                    fracboxCheck[quesNo - 1][fracboxcount] = true;
                    fracboxcount++;
                }
                else {
                    line += $(this).val() + "|";
                    userLine += $("iframe#" + txtbxID)[0].contentWindow.getData() + "|";
                }
                if (txtbxID.split("_")[1])
                    jQuery('#fracB_' + txtbxID.split("_")[1]).removeAttr('contenteditable');
            }
            else if ($(this).attr("type") == "radio") {
                if ($(this).is(":checked")) {
                    line += $(this).val() + "|";
                    userLine += $(this).val() + "|";
                }
            }
        });
        line = line.substring(0, line.length - 1);
        userLine = userLine.substring(0, userLine.length - 1);
        // eeLine = eeLine.substring(0, eeLine.length - 1);
        ddlLine = ddlLine.substring(0, ddlLine.length - 1);
        ddlLineByVal = ddlLineByVal.substring(0, ddlLineByVal.length - 1);
        userAns.push(line);
        userRealAns.push(userLine);
        eeResponse.push(removeInvalidChars(eeLine));
        userDdlAns.push(ddlLine);
        userDdlAnsByVal.push(ddlLineByVal);
        return;
    });
    //questionCount--; //Needed since one div extra for template
    for (var k = 0; k < questionCount; k++) {
        if (newQues[k].quesType == 'D') {
            var result = newQues[k].checkAnswerDropDown(userDdlAns[k], userAns[k].split("|"), fracboxCheck[k], k);
            var fullUserAnswer = userDdlAnsByVal[k];
            if (userRealAns[k] != "")
                fullUserAnswer += '|' + userRealAns[k];
        }
        else if (newQues[k].quesType == 'Open Ended') {
            var result = newQues[k].checkAnswerOpenEnded(eeResponse[k]);
        }
        else if (newQues[k].quesType == 'Blank') {
            var result = newQues[k].checkAnswerBlank(userAns[k].split("|"), fracboxCheck[k], true,k);
            var fullUserAnswer = userRealAns[k];
        }
        else {
            var result = newQues[k].checkAnswerMCQ(userAns[k]);
            var fullUserAnswer = userAns[k];
        }
        if (result == 2)
        {
            requireAns.push(Q1_array[k]);
        }
        returnStr += result + "##";
        userResponse.push(fullUserAnswer);
    }
    
    
    if (requireAns.length == 0) {
        var tempUserResponse = userResponse.join("##");
        $("#userResponse").val(tempUserResponse);
        $("#eeResponse").val(eeResponse.join("##"));
        return (returnStr);
    }
    else {
        var remainQues = requireAns.join(",");
        // if blank type question adds borders of correct / incorrect answers (due to checkAnswerBlank) then remove them
        $(".incorrectblank").removeClass("incorrectblank");
        $(".correctblank").removeClass("correctblank");
        if(Q1_array.length == 1)
        {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: i18n.t("questionPage.ncertOneAnswer"), 
                    type: 'alert', 
                    promptId: "specifyAnswerNcertQ1", 
                    func1: function () { 
                        jQuery("#prmptContainer_specifyAnswerNcertQ1").remove();
                    }
                });
            }
        }
        else
        {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: i18n.t("questionPage.ncertAllAnswer") + remainQues + ".", 
                    type: 'alert', 
                    promptId: "specifyAnswerNcertQM", 
                    func1: function () { 
                        jQuery("#prmptContainer_specifyAnswerNcertQM").remove();
                    }
                });
            }
        }
        return false;
    }
}

function daFlagHandle(flagCheck)
{
    var AttemptQno = $('#qno').val();
    if($("#daFlagCheck").attr('checked'))
    {
        var isflag = 1;
    }
    else
    {
        isflag = 0;
        flagCheck = 'no';
    }
    
    var data = $('.optionActive').text();
    var daQcode = $('#qcode').val();
    var ans = data;
    if(ans=="")
        ans = "No Answer";
    var result = newQues.checkAnswerMCQ(ans);
    
    $.ajax('controller.php',
        {
            type: 'post',
            data: "mode=saveDaFlag&qno="+AttemptQno+"&qcode="+daQcode+"&ans="+ans+"&result="+result+"&isflag="+isflag,
            success: function (transport) {
                    $('#pnlAnswer').css("display", "none");
                    $('#displayanswer').html("");
                    
                    if(flagCheck == 'yes')
                    {
                        $("#"+AttemptQno+"box").css("cursor","pointer");
                        $("#"+AttemptQno+"box").removeClass('daPagingGrey');
                        $("#"+AttemptQno+"box").removeClass('daPagingred');
                        $("#"+AttemptQno+"box").removeClass('daPagingBlue');
                        $("#"+AttemptQno+"box").addClass("daPagingYellow");
                        $("#prevDAQuesFlag").val(1);
                    }else
                    {
                        $('#daFlagCheck').attr('checked', false);
                        $("#prevDAQuesFlag").val(0);
                        $("#"+AttemptQno+"box").removeClass('daPagingYellow');
                        $("#"+AttemptQno+"box").addClass("daPagingBlue");
                    }
            }
        }
        );
    
}

function saveDALastAnswer()
{
    var AttemptQno = $('#qno').val();
    var data = $('.optionActive').text();
    var daQcode = $('#qcode').val();
    var ans = '';
    var result = newQues.checkAnswerMCQ(data);
    if(data)
        ans = data;
    else
        ans = "No Answer";

    $.ajax('controller.php',
            {
                type: 'post',
                data: "mode=saveDaLastAnswer&qno="+AttemptQno+"&qcode="+daQcode+"&ans="+ans+"&result="+result,
                success: function (transport) {
                    if($("#"+AttemptQno+"box").hasClass("daPagingYellow") || $("#prevDAQuesFlag").val() == 1)
                        $('#daFlagCheck').attr('checked', true);
                }
            }
        );
}
function evaluateSingleQuestion(){
    if (newQues.quesType == 'Blank') { /*alert("2");*/
        var userAns = new Array();
        var userRealAns = new Array();
        var fracboxCheck = new Array();
        var isNext = 2;         
        for (var i = 0; i < newQues.noOfBlanks; i++) {

            var b = "";
            var f = "";
            fracboxCheck[i] = false;
            if (document.getElementById('b' + (i + 1) + '')) {
                if ($("#b" + (i + 1)).hasClass("customfrac")) {
                    f = document.getElementById('b' + (i + 1)).value;
                    b = stripFrac(document.getElementById('b' + (i + 1)).value);
                    fracboxCheck[i] = true;
                }
                else
                    b = document.getElementById('b' + (i + 1)).value;
            }
            else {
                if ($('#fracB_' + (i + 1)).hasClass('fracboxvalue')) {
                    //b =   document.getElementById('fracV_'+(i+1)).value;
                    b = $("iframe#fracB_" + (i + 1))[0].contentWindow.getData();
                    fracboxCheck[i] = true;
                }
                else {
                    //b =   document.getElementById('fracS_'+(i+1)).value;
                    b = $("iframe#fracB_" + (i + 1))[0].contentWindow.getData();
                }
                //f =   document.getElementById('fracB_'+(i+1)).value;
            }
            if (f == "")
                userRealAns.push(b);
            else
                userRealAns.push(f);

            userAns.push(b);
        }
        var conditionCheck = 0;
        var priority = 0;
        var userAnswer = userAns.join("|");

        if (userRealAns.length != 0)
            var userRealResponse = userRealAns.join("|");
        else
            var userRealResponse = "";

        if (userRealResponse == "")
            document.getElementById('userResponse').value = userAnswer;
        else
            document.getElementById('userResponse').value = userRealResponse;
        var result = newQues.checkAnswerBlank(userAnswer.split("|"), fracboxCheck, true);
    }
}
function handleClose() {
    //$("#nextDAQuesLoaded").val(1);

    $(".dd_feedback_header").hide();
    var DaFlag = 0;
    var setAllowSend =$("#setAllowSend").val();
    if($("#clusterQuestionMessage").is(":visible") && setAllowSend == 0){
        if(setAllowSend == 0){
            var cqtextstr = $("#clusterQuestionMessage").val();
            if(cqtextstr.replace(/\s/g, '').length != 0){
                if (!$(".promptContainer").is(":visible")) {
                    new Prompt({
                        text: "You haven't submitted your explanation.Click on the send button below your explanation to send your answer to Team Mindspark.",
                        type: "alert",
                        promptId: "cqprompt",
                        func1: function() {
                            $("#setAllowSend").val("1");
                            jQuery("#prmptContainer_cqprompt").remove();
                            $("#clusterQuestionMessage").prop("disabled", false);
                            $("#cqebutton").prop("disabled", false);
                        }
                    })
                }
                
                $("#clusterQuestionMessage").attr("disabled","true");
                $("#cqebutton").attr("disabled","true");
                return false;
            }           
        }
    }
    if($("#quesCategory").val() == "worksheet" && !((countElement(0,responseArray["wsAnsweredArray"])==1 && (responseArray["wsAnsweredArray"]).indexOf(0)==Q1-1) || (countElement(0,responseArray["wsAnsweredArray"])==0))){
        if(!$(".promptContainer").is(":visible"))
        {
            var prompts = new Prompt({ 
                text: "Please answer all questions to submit the worksheet.", 
                type: 'alert', 
                promptId: "numberValidation", 
                func1: function () { 
                    jQuery("#prmptContainer_numberValidation").remove();
                }
            });
        }
        return false;
    }
    else if($("#quesCategory").val() == "worksheet" && ((countElement(0,responseArray["wsAnsweredArray"])==1 && (responseArray["wsAnsweredArray"]).indexOf(0)==Q1-1) || (countElement(0,responseArray["wsAnsweredArray"])==0)))
    {
        var lastAttemptQno = $('#qno').val();
        var usedTime = $('#countdown').html();
        var res = usedTime.split(" ");
        $("#submitWorksheet").val(1);submitAnswer();
        if(!$(".promptContainer").is(":visible"))   
        {
            var prompts = new Prompt({
                text: i18n.t("questionPage.worksheetSubmitMsg"),
                type: 'confirm',
                label1:'Cancel',
                label2: 'Submit',
                promptId: "SubmitTest",
                func2: function () {
                    //setTryingToUnload();
                    //jQuery("#prmptContainer_SubmitTest").remove();                                     
                    $.ajax('controller.php',
                        {
                            type: 'post',
                            data: "mode=endWorksheet&qno="+lastAttemptQno+"&time="+res[0]+"&status=3&response="+data,
                            async:false,
                            success: function (transport) {
                                resp = transport;//jQuery("#prmptContainer_SubmitTest").remove();
                                setTryingToUnload();
								$("#prmptContainer_SubmitTest").remove();
								if (!$(".promptContainer").is(":visible")) {
									new Prompt({
										text: "You have completed the Worksheet!<br><br>Sparkie Earned: 10",
										type: "alert",
										label1 : "Home",
										func1: function () {                                               
												setTryingToUnload();												
												window.location.assign("home.php");
											},
										promptId: "worksheetSparkie",
									})
								}
                                //window.location.assign("worksheetSelection.php");
                            }
                         }
                    );
                },
                func1:function(){
                    $("#prmptContainer_SubmitTest").remove();
                }
            });
        }
        return false;
    }
    if($("#quesCategory").val() == "daTest" && ($("#qno").val() == $('#daTestQueCount').val() || ($('#DASubmitTestBtn').val() == 1) && $('#DASubmitTestBetween').val() == 0 ))
    {
        var lastAttemptQno = $('#qno').val();
        var usedTime = $('#countdown').html();
        var res = usedTime.split(" ");
        if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({
                    text: i18n.t("questionPage.daTestSubmitMssg"),
                    type: 'confirm',
                    label1: 'Yes',
                    label2:'No',
                    promptId: "SubmitTest",
                    func1: function () {
                        
                        var data = $('.optionActive').text();
                        if(data)
                                submitAnswer(data);
                        else
                                submitAnswer('No Answer');
                        
                        setTryingToUnload();
                        jQuery("#prmptContainer_SubmitTest").remove();
                        $.ajax('controller.php',
                            {
                                type: 'post',
                                data: "mode=endDaTest&qno="+lastAttemptQno+"&time="+res[0]+"&status=3&response="+data,
                                success: function (transport) {
                                    resp = transport;
                                    setTryingToUnload();
                                    window.location.assign("da_feedbackForm.php");
                                }
                            }
                            );
                    },
                    func2:function(){
                            $("#prmptContainer_SubmitTest").remove();
                        }
                });
            }
        return false;
    }

    if($("#quesCategory").val() == "daTest")
    {
        var data = $('.optionActive').text();

        if($("#qno").val() != $('#daTestQueCount').val() || $("#quesCategory").val() == "worksheet")
        {

            if($("#nextDAQuesLoaded").val() != 1)
            {
                if(data)
                    submitAnswer(data);
                else
                {
                    submitAnswer('No Answer');
                    DaFlag = 1;
                }
            }
        }

         var qno = parseInt($("#qno").val());
         var nextQue =  qno + 1;
            if ($("#quesCategory").val() == "daTest") setDaColorCode(qno , nextQue,'yes');

            $('#DASubmitTestBetween').val(0);
    }

    if (diagnosticTestComplete == 1 && kstdiagnosticTestComplete == 1) {
        $("#nextQuestion,#nextQuestion1,#nextQuestion2").show();
        return false;
    }
    if($("#quesCategory").val() !="practiseModule")
        $("#toolContainer,#radioButtons,.questionPrompts").attr('style', '');
    else{
         $("#radioButtons,.questionPrompts").attr('style', '');
    }

    $("#radioButtons").css('position', 'relative');
    noSubmit = 0;
    giveResult = 0;
    toughQuestionStatus = 0;
    submitCheck = 0;
    qq = 0;
    toolClick = 1;
    $('#wildcardMessage').css('display', 'none');
    $('#clusterQuestionMessage').css('display', 'none');
    $("#cqebutton").css('display', 'none');
    $(".cq-message-div").css('display', 'none');
    if (userType == 'msAsStudent'){
        $("#msAsStudentInfo").css("right","0px");
        $("#msAsStudentInfo").css("left","");
        $("#msAsStudentInfo").css("top","215px");
    }
    $('#skipQuestion').css('margin-top', '23px');
    document.getElementById("promptNo").value = 0;
    //close toolbar if open
    if (toolClick == 1)
        toolbar();

    $('.optionX').removeClass("optionActive");

    $("#cc").attr("class", "emotImage comment_new");
    $('.optionX').addClass("optionInactive");
    try {
        stop1();    //for buddy
        if ($("#result").val() == "")
            return;
    } catch (err) { };

    if($("#quesCategory").val() != "worksheet") hideCommentBox();

    window.status = '';
    plstart = new Date();
    stopTheTimer();
    var wildCardText = $("#wildcardMessage").val().trim();
    if(wildCardText==i18n.t("questionPage.placeHolderWildcardText"))
        wildCardText = "";
    timeTakenExplainationArr[quesNoInFlow] = secs;
    if (sparkieForExplaination() && $("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest" && $("#quesCategory").val() != "practiseModule") {
        sparkieCount = 1;
        $("#sparkieInfo").html(parseInt($("#sparkieInfo").text()) + 1);
        $("#noOfSparkie").html(parseInt($("#noOfSparkie").text() + 1));
        $(".redCircle").text(parseInt($(".redCircle").text()) + 1);
    }
    if ($("#quesCategory").val() != "diagnosticTest" && $("#quesCategory").val() != "kstdiagnosticTest" && $("#quesCategory").val() != "daTest" && $("#quesCategory").val() != "practiseTest")
        updateTimeTakenForExpln(secs, wildCardText);

    modifyDOMforPC();
    fetchNextQues();
    saveEmotByAjax(emotID);
    emotID = "";
}
function updateTimeTakenForExpln(secs, wildCardText) {
    if ($("#submit_bar1").is(":visible"))
        $("#nextQuestion1").hide();
    else if ($("#submit_bar").is(":visible"))
        $("#nextQuestion").hide();
    else if ($("#menuBar").is(":visible"))
    {
        $("#nextQuestion2").hide();
        $("#submitQuestion2").hide();
        $("#skipQuestion2").hide();
    }

    var params = "mode=timeTakenForExpln";
    params += "&timeTaken=" + secs;
    params += "&qcode=" + prevQcode;
    params += "&question_type=" + prevQuesCategory;
    params += "&quesCategory=" + $('#quesCategory').val();
    params += "&sparkieExplaination=" + sparkieCount;
    params += "&displayAnsRating=" + displayAnsRating;
    // params += "&daComment=" + $("#daComment").val();
    sparkieCount = 0;

    if (!(wildCardText == i18n.t("questionPage.placeHolderWildcardText") || wildCardText == "" || wildCardText == "questionPage.placeHolderWildcardText"))
        params += "&wildCardText=" + wildCardText;

    try {
        $.ajax('controller.php',{
                type: 'post',
                data: params,
                success: function (transport) {
                    /*modifyDOMforPC();
                    fetchNextQues();*/
                }
            }
        );
    }
    catch (err) { alert("updateTimeTakenForExpln " + err.description); }
}

function modifyDOMforPC() {
    $(".singleQuestion").each(function () {
        $(this).contents().find(".correct").removeClass("correct");
        $(this).contents().find(".wrong").removeClass("wrong");
    })
}
function removeInvalidChars(tmp) {
    tmp = escape(tmp);
    tmp = tmp.replace(/%D7/g, "&times;");
    tmp = tmp.replace(/%F7/g, "&divide;");
    tmp = tmp.replace(/%AB/g, "&laquo;");
    tmp = tmp.replace(/%B0/g, "&deg;");
    tmp = tmp.replace(/%BB/g, "&raquo;");
    tmp = tmp.replace(/%u2220/g, "&ang;");
    tmp = tmp.replace(/%u03B1/g, "&alpha;");
    tmp = tmp.replace(/%u03B2/g, "&beta;");
    tmp = tmp.replace(/%u03B3/g, "&gamma;");
    tmp = tmp.replace(/%u0394/g, "&Delta;");
    tmp = tmp.replace(/%u03BB/g, "&lambda;");
    tmp = tmp.replace(/%u03B8/g, "&theta;");
    tmp = tmp.replace(/%u03C0/g, "&pi;");
    tmp = tmp.replace(/%u2211/g, "&sum;");
    tmp = tmp.replace(/%u221A/g, "&#8730;");
    tmp = tmp.replace(/%u221B/g, "&#8731;");
    tmp = tmp.replace(/%BB/g, "&raquo;");
    tmp = tmp.replace(/%AB/g, "&laquo;");
    tmp = tmp.replace(/%F7/g, "&divide;");
    tmp = tmp.replace(/%D7/g, "&times;");
    tmp = tmp.replace(/%u2264/g, "&le;");
    tmp = tmp.replace(/%u2265/g, "&ge;");
    tmp = tmp.replace(/%u22A5/g, "&perp;");
    tmp = tmp.replace(/%u03B4/g, "&delta;");
    tmp = tmp.replace(/%u03C3/g, "&sigma;");
    tmp = tmp.replace(/%u2282/g, "&sub;");
    tmp = tmp.replace(/%u2284/g, "&#8836;");
    tmp = tmp.replace(/%u2245/g, "&cong;");
    tmp = tmp.replace(/%u2260/g, "&ne;");
    tmp = tmp.replace(/%u2208/g, "&#8712;");
    tmp = tmp.replace(/%u2209/g, "&#8713;");
    tmp = tmp.replace(/%u222A/g, "&#8746;");
    tmp = tmp.replace(/%u2229/g, "&#8745;");
    tmp = tmp.replace(/%u223C/g, "&#8764;");
    tmp = tmp.replace(/%B1/g, "&plusmn;");
    tmp = tmp.replace(/%u2234/g, "&there4;");
    tmp = unescape(tmp);
    return tmp;
}
function nAth(str) {
    if (str=="") return "";
    var strtodecrypt = str.split("-");
    var msglength = strtodecrypt.length;
    decrypted_message = "";
    for (var position = 0; position < msglength; position++) {
        ascii_num_byte_to_decrypt = strtodecrypt[position];
        ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt / 2;
        ascii_num_byte_to_decrypt = ascii_num_byte_to_decrypt - 5;
        decrypted_byte = String.fromCharCode(ascii_num_byte_to_decrypt);
        decrypted_message += decrypted_byte;
    }
    return decrypted_message;
}
// return true for 1234567890-./
function CheckIfNumeric(evt) {
    evt = (evt) ? evt : window.event;
    if(/Firefox/.test(window.navigator.userAgent) && evt.keyCode!=0) {
        return true;
    }
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && charCode != 46 && charCode != 47 && charCode != 45 && (charCode < 48 || charCode > 57) || (charCode==45 && (evt.target.selectionStart!=0 || evt.target.value.indexOf('-')>=0)) || (charCode==46 && evt.target.value.indexOf('.')>=0) || (charCode==47 && (evt.target.selectionStart==0 || evt.target.value.indexOf('/')>=0))) {
        if (!keypadPresent)
        {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "Please enter only numbers.", 
                    type: 'alert', 
                    promptId: "numberValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_numberValidation").remove();
                    }
                });
            }
        }
        return false;
    }
    return true;
}
function showProgressDetails(quesDone, quesCorrect, progress, lowerLevel, newProgressBar) {
    if (quesDone == "") quesDone = 0;
    if (quesCorrect == "") quesCorrect = 0;
    if (progress == "") progress = 0;
    if (lowerLevel == "") lowerLevel = 0;
    if (!progressBarFlag) {
        var angle = 360 * progress / 100;
        $(".pie").css({ '-webkit-transform': 'rotate(' + angle + 'deg)', '-moz-transform': 'rotate(' + angle + 'deg)', '-o-transform': 'rotate(' + angle + 'deg)', 'transform': 'rotate(' + angle + 'deg)', '-ms-transform': 'rotate(' + angle + 'deg)' });
        if (angle < 180) {
            $("#pieSlice2").attr("class", "hold1");
            $("#pieSlice2").css({ '-webkit-transform': 'rotate(' + (180 - angle) + 'deg)', '-moz-transform': 'rotate(' + (180 - angle) + 'deg)', '-o-transform': 'rotate(' + (180 - angle) + 'deg)', 'transform': 'rotate(' + (180 - angle) + 'deg)', '-ms-transform': 'rotate(' + (180 - angle) + 'deg)' });
            $(".pie").css({ '-webkit-transform': 'rotate(180deg)', '-moz-transform': 'rotate(180deg)', '-o-transform': 'rotate(180deg)', 'transform': 'rotate(180deg)', '-ms-transform': 'rotate(180deg)' });
        }
    }
    $("#spnProgress").html(progress + "%");
    $("#spnProgress1").html(progress + "%");
    $("#spnProgressNew").html(progress + "%");
    $("#spnQuestionsDone").html(quesDone);
    $("#spnQuesCorrect").html(quesCorrect);

    if (document.getElementById("green"))
        document.getElementById("green").style.width = progress + "%";

    if (progressBarFlag && newProgressBar) {
        // CLuster Progress
        //makeClusterBar();
        //makeProgressBar();

    }
    else {
        if (document.getElementById("green"))
            document.getElementById("green").style.width = progress + "%";
    }
}

function remakeProgressBar() {
    var lastKey = 0;
    var firstKey = 0;
    var previousKey = 0;
    var totalSDLs = $('#totalSDLS').val();
    var progressBarWidth = $('#progress_bar_new').width();
    var yellowWidth = Math.round(progressBarWidth - 1) / totalSDLs;
    var indices = {};

    divBar = document.getElementById('yellowBars');

    $.each(sdlAttemptArr, function (key, value) {
        var div = document.createElement("div");
        div.id = 'yellow_' + key;
        div.className = 'hidden';
        div.className = 'yellow';
        div.style.width = yellowWidth + "px";
        $(div).attr('sdl', key);
        if($("#quesCategory").val() != "daTest" && $('#quesCategory').val() != "practiseTest" && divBar)
            divBar.appendChild(div);
        //parseInt(sdlAttemptArr[key]) = parseInt(parseInt(sdlAttemptArr[key]));
        if (parseInt(sdlAttemptArr[key]) == 4)
            div.style.backgroundColor = 'white';
        else if (parseInt(sdlAttemptArr[key]) == 1)
            div.style.backgroundColor = '#9ec956';
        else if (parseInt(sdlAttemptArr[key]) == 0)
            div.style.backgroundColor = '#e75903';
        else if (parseInt(sdlAttemptArr[key]) == 5) {
            //div.className += ' currentSDLs';
            div.style.backgroundColor = '#fbd212';
        }
        //div.style.backgroundColor = '#fbd212';
        else if (parseInt(sdlAttemptArr[key]) == 2 || parseInt(sdlAttemptArr[key]) == 3)
            div.style.backgroundColor = '#e75903';
        else
            div.style.backgroundColor = 'white';

        /*if (sdlAttemptArr[previousKey] == 4 && parseInt(sdlAttemptArr[key]) != 4) {
            divBars = document.getElementById('yellow_' + previousKey);
            divBars.style.backgroundColor = '#9ec956';
        }*/

        if (firstKey == 0)
            firstKey = key;

        previousKey = key;
        lastKey = key;
    });
    $('#progress_text_new').html(nameOfCluster);
    if ($('#progress_text_new').text().length > 40) {
        $('#progress_text_new').html($('#progress_text_new').text().slice(0, 40) + '...');
    }
    var noOfClusters = 0;
    $.each(clusterAttemptArr, function (key, value) {
        if (clusterAttemptArr[key][2] != 'activity' && clusterAttemptArr[key][2] != 'timedTest') {
            noOfClusters++;
        }
    });
    var lastKey1 = 0;
    var totalClusters = noOfClusters;
    var progressBarWidth = 386;
    var greenWidth = Math.round(progressBarWidth) / totalClusters;
    divBar = document.getElementById('greenBars');
    var clusterPresent = 0;
    var biggerCluster = '';
    var noOfClusters = 0;
    var progressBarWidthCovered = 0;

    $.each(clusterAttemptArr, function (key, value) {
        var div = document.createElement("div");
        if (clusterAttemptArr[key][2] != 'activity' && clusterAttemptArr[key][2] != 'timedTest') {
            div.id = 'green_' + key;
            div.className = 'hidden';
            div.className = 'green';
            div.style.width = (clusterAttemptArr[key][3]*progressBarWidth) + "px";
            div.style.backgroundColor = 'white';
            div.title = clusterNameAttemptArr[clusterAttemptArr[key][0]];
            noOfClusters++;
            progressBarWidthCovered += (clusterAttemptArr[key][3]*progressBarWidth);

        }
        else if (clusterAttemptArr[key][2] == 'activity') {
            div.id = 'green_' + key;
            div.className = 'hidden';
            div.className = 'activityTTIcons activityIcon';
            //div.className = 'greenCircle';
            div.style.left = (progressBarWidthCovered - 10)+ "px";
            div.title = clusterAttemptArr[key][1];
            //div.innerHTML = 'A';
            //div.style.backgroundColor = 'black';
            if (clusterAttemptArr[key][3] == 'attempted') {
                //div.style.backgroundColor = 'green';
                div.className = 'activityTTIcons activityIcon_attempted';
            }
        }
        else if (clusterAttemptArr[key][2] == 'timedTest') {
            div.id = 'green_' + key;
            div.className = 'hidden';
            //div.className = 'greenCircle';
            div.className = 'activityTTIcons timedTestIcon';
            div.style.left = (progressBarWidthCovered - 10) + "px";
            div.title = clusterAttemptArr[key][1];
            //div.innerHTML = 'T';
            //div.style.backgroundColor = 'black';
            if (clusterAttemptArr[key][3] == 'passed') {
                //div.style.backgroundColor = 'green';
                div.className = 'activityTTIcons timedTestIcon_passed';
            }
            else if (clusterAttemptArr[key][3] == 'failed') {
                //div.style.backgroundColor = 'red';
                div.className = 'activityTTIcons timedTestIcon_failed';
            }
            if (clusterAttemptArr[key - 1][2] == 'activity') {
                div.style.top = "-48px";
            }
            if (clusterAttemptArr[key - 1][2] == 'timedTest') {
                div.style.top = "-48px";
            }
        }
        if (divBar) divBar.appendChild(div);
        $(div).attr('cluster', clusterAttemptArr[key][0]);
        indices[clusterAttemptArr[key][0]] = new Array();
        var idx = topicAttemptArr.indexOf(clusterAttemptArr[key][0]);
        while (idx != -1) {
            indices[clusterAttemptArr[key][0]].push(idx);
            idx = topicAttemptArr.indexOf(clusterAttemptArr[key][0], idx + 1);
        }
        for (var i = 0; i < indices[clusterAttemptArr[key][0]].length; i++) {

            if (topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 2] == "SKIPPED") {
                div.style.backgroundColor = '#2f99cb';
                i = indices[clusterAttemptArr[key][0]].length;
                break;
            }
            else if (topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 2] == "SUCCESS") {
                div.style.backgroundColor = '#9ec956';
                i = indices[clusterAttemptArr[key][0]].length;
                break;
            }
            else if (topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 2] == null || topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 2] == "") {
                if (topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 1] == 1)
                    div.style.backgroundColor = '#fbd212';
                else {
                    div.style.backgroundColor = '#orange';
                    biggerCluster = clusterAttemptArr[key][0];
                }

            }
            else if (topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 2] == "FAILURE") {
                if (topicAttemptArr[indices[clusterAttemptArr[key][0]][i] + 1] >= 4)
                    div.style.backgroundColor = '#e75903';
                else {
                    div.style.backgroundColor = 'orange';
                    biggerCluster = clusterAttemptArr[key][0];
                }

            }

        }

        if (clusterAttemptArr[key][0] == $('#clusterCode').val()) {
            clusterPresent = 1;
            div.style.height = 25 + "px";
            div.style.marginTop = -5 + "px";
            if (div.style.backgroundColor == 'orange')
                backToRemediationCluster = 1;

        }
    });
   /* var greenWidth = Math.round(progressBarWidth / noOfClusters);*/
  /*  $(".green").css('width', greenWidth + 'px');*/
  
   $("#topicProgressBar").css('width', progressBarWidth+'px');
    if (clusterPresent == 0 && atHigherLevel!=1) {
        $('div[cluster=' + biggerCluster + ']').css({
            'height': '25px',
            'margin-top': '-5px'
        });
    }

}
function showClusterPrompt() {
    var lastCluster = "";
    $.each(clusterAttemptArr, function (key, value) {
        if (clusterAttemptArr[key][2] != 'activity' && clusterAttemptArr[key][2] != 'timedTest') {
            lastCluster = clusterAttemptArr[key][0];
        }
    });
    if (clusterStatusPrompt == 5 && $("#quesCategory").val() == "bonusCQ") {
        clusterStatusPromptfn();
        $('#clusterStatusPrompts').html('Good progress ' + $('#childNm').val() + i18n.t("questionPage.bonusCQPrompt"));
        clusterStatusPrompt = 0;
        noSubmit = 1;
    }
    if (clusterStatusPrompt == 2 && currentCluster != lastCluster) {
        /*clusterStatusPromptfn();
        $('#clusterStatusPrompts').html(i18n.t("questionPage.clusterStatusPrompt2"));
        clusterStatusPrompt = 0;
        /*lastClusterFlag = 0;*/

        //noSubmit = 1;
        if (choiceScreen && choiceScreen!="" && Object.keys(choiceScreen['choices']).length>0) {
            $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html(i18n.t("questionPage.clusterStatusPrompt2"));
            $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+40)+'px');
            //$('#choiceScreenDiv').css('height','270px');
        }
        else {
            clusterStatusPromptfn();
            $('#clusterStatusPrompts').html(i18n.t("questionPage.clusterStatusPrompt2"));
            clusterStatusPrompt = 0;choiceScreen=null;
            noSubmit = 1;
        }
    }

    else if (clusterStatusPrompt == 1) {
        clusterStatusPromptfn();
        $('#clusterStatusPrompts').html($('#childNm').val() + i18n.t("questionPage.clusterStatusPrompt1"));

        clusterStatusPrompt = 0;

        noSubmit = 1;
    }
    else if (clusterStatusPrompt == 3) {
        clusterStatusPromptfn();
        $('#clusterStatusPrompts').html($('#childNm').val() + i18n.t("questionPage.clusterStatusPrompt3"));
        clusterStatusPrompt = 0;

        noSubmit = 1;
    }

    else if (clusterStatusPrompt == 5 && currentCluster != lastCluster) {
        if (choiceScreen && choiceScreen!="" && Object.keys(choiceScreen['choices']).length>0) {
            $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html('Good progress ' + $('#childNm').val() + i18n.t("questionPage.clusterStatusPrompt0"));
            $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+40)+'px');
            //$('#choiceScreenDiv').css('height','270px');
        }
        else {
            clusterStatusPromptfn();
            $('#clusterStatusPrompts').html('Good progress ' + $('#childNm').val() + i18n.t("questionPage.clusterStatusPrompt0"));
            clusterStatusPrompt = 0;choiceScreen=null;
            noSubmit = 1;
        }
    }
    else if (clusterStatusPrompt == 4 && backToRemediationCluster == 1) {
        if (choiceScreen && choiceScreen!="" && Object.keys(choiceScreen['choices']).length>0) {
            $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html($('#childNm').val() + i18n.t("questionPage.clusterStatusPrompt4"));
            $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+60)+'px');
            backToRemediationCluster = 0;
            //$('#choiceScreenDiv').css('height','270px');
        }
        else {
            clusterStatusPromptfn();
            $('#clusterStatusPrompts').html($('#childNm').val() + i18n.t("questionPage.clusterStatusPrompt4"));
            backToRemediationCluster = 0;
            clusterStatusPrompt = 0;choiceScreen=null;
            noSubmit = 1;
        }

    }
    else if (currentCluster==lastCluster){
        if (choiceScreen && choiceScreen!="") {
            $('<div id="choiceScreenTextMesage" style="text-align: center;padding: 5px;"></div>').insertBefore('#choiceScreenText').html('Congratulations! You have successfully completed the topic.');
            $('#choiceScreenDiv').css('height',($('#choiceScreenDiv').height()+40)+'px');
        }
    }

}
function updateClusterBar() {
    if (previousCluster != $('#clusterCode').val() && $("#quesCategory").val() != "challenge" && $("#quesCategory").val() != "bonusCQ" && $("#quesCategory").val() != "daTest") {
        $('.green,.yellow').remove();
        remakeProgressBar();
    }
    previousCluster = $('#clusterCode').val();
    var previousKey = 0;
    showProgressDetails(quesAttempted, quesCorrect1, progressInTopic, lowerLevel1, 0); //Pending integration
    $.each(sdlAttemptArr, function (key, value) {
        divs = document.getElementById('yellow_' + key);
        if (parseInt(sdlAttemptArr[key]) == 5) {
            if ($('div[sdl="' + key + '"]').css('background-color') != 'rgb(251, 210, 18)' && $('div[sdl="' + key + '"]').css('background-color') != 'rgb(255, 255, 255)') {
                $('.green,.yellow').remove();
                $('.yellow').css('background-color', 'white');
                remakeProgressBar();
            }
            $('div[sdl="' + key + '"]').css('background-color', '#fbd212');
            /*if (sdlAttemptArr[previousKey] == 4 && parseInt(sdlAttemptArr[key]) != 4) {
                divBars = document.getElementById('yellow_' + previousKey);
                divBars.style.backgroundColor = '#9ec956';
            }*/
        }
        else if (parseInt(sdlAttemptArr[key]) == 1 && $("#quesCategory").val() != "daTest" && divs)
            divs.style.backgroundColor = '#9ec956';
        else if (parseInt(sdlAttemptArr[key]) == 0 && $("#quesCategory").val() != "daTest" && divs)
            divs.style.backgroundColor = '#e75903';
        else if (parseInt(sdlAttemptArr[key]) == 2 || parseInt(sdlAttemptArr[key]) == 3 && $("#quesCategory").val() != "daTest" && divs)
            divs.style.backgroundColor = '#e75903';
        else if($("#quesCategory").val() != "daTest" && $('#quesCategory').val() != "practiseTest" && divs)
             divs.style.backgroundColor = 'white';
        /*switch (parseInt(sdlAttemptArr[previousKey])) {
            case 1:
                $('div[sdl="' + previousKey + '"]').css('background-color', '#9ec956')
                break;
            case 0:
                $('div[sdl="' + previousKey + '"]').css('background-color', '#e75903')
                break;

        }*/
        previousKey = key;
        lastKey = key;
    });

}
function finalSubmit(code) {
    $('#mode').val(code);
    if (code == 1 || code == -5 || code == 6 || code == -6 || code == -7 || code == -9)        //i.e. 1 means End Session button clicked.
    {
        $('#endSessionClick').height(175);
        $('.button1').hide();
        $('.endSessionText').css('display', 'none');
        $('#loading').show();
        $('#loadingImage').show();
        if (iycAsChoice || typeof(iycAsChoiceFlag)!='undefined')
            alert('You can complete this later from the Exam Corner.');
        var params = "mode=endsession";
        params += "&code=" + code;
        try {
            var request = $.ajax('controller.php',
                            {
                                type: 'post',
                                data: params,
                                success: function (transport) {
                                    resp = transport;
                                    redirResult(0);
                                },
                                error: function () {
                                    //alert('Something went wrong...');
                                }
                            }
                            );
        }
        catch (err) { alert("Final Submit " + err.description); }
    }
    else
        redirResult(0);
}
function redirResult(endType) {
    redirect = 0;
    if ($('#quesCategory').val() == "topicRevision")
        document.quesform.action = 'topicRevisionReport.php?endType=' + endType;
    else if ($('#quesCategory').val() == "practiseTest")
    {
        var score = document.getElementById('scorecard');
         var sr = score.innerHTML;
        document.quesform.action = 'endSessionReport.php?endType=' + endType + '&practiseTest=true&sr='+sr;
    }   
    else if ($('#quesCategory').val() == "exercise")
        document.quesform.action = 'exerciseSessionReport.php?endType=' + endType;
    else if ($('#quesCategory').val() == "NCERT" || $('#quesCategory').val() == "NCERT REPORT")
        if (userType == 'msAsStudent')
            document.quesform.action = 'home.php';
        else
            document.quesform.action = 'homeworkSelection.php?endType=' + endType;
    else if ($('#quesCategory').val() == "msAsStudentNCERT")
        document.quesform.action = 'home.php';
    else if ($('#quesCategory').val() == "daTest")
        document.quesform.action = 'daTestReport.php?endType=' + endType;
    else
        document.quesform.action = 'endSessionReport.php?endType=' + endType;
    setTryingToUnload();
    document.quesform.submit();
}
function showSlowLoadingMsg() {
    if ($("#practiceCompletionPrompt").is(":visible")) return;
    clearTimeout(slowLoadTimer);
    var errorLogs = $.ajax({
        url: "errorLog.php",
        type: "POST",
        data: "params=request- " + $("#quesform").find("input").serialize() + " error-" + encodeURIComponent(JSerrors) + " ajaxFailure - " + ajaxFailure + " , responseArray - " + responseArray + "," + "&qcode=" + $("#qcode").val() + "&typeErrorLog=slow internet",
        dataType: "text",
        "async": false
    });
    ajaxFailure = false;
    errorLogs.fail(function (jqXHR, textStatus) {
        if (storageEnabled()) localStorage.setItem("sessionID", $(".sessionColor:eq(0)").text());
        if (storageEnabled()) localStorage.setItem("qcode", $("#qcode").val());
        if (storageEnabled()) localStorage.setItem("qno", $(".sessionColor:eq(1)").text());
        if (storageEnabled()) localStorage.setItem("errorType", "error 1");
    });
    alert(i18n.t("questionPage.slowLoadingMsg"));

    redirResult(8); //Slow loading
}
function endtopic() {
    $("#endTopic,#blackScreen").css("display", "block");
}
function changeTopic() {
    redirect = 0;
    $('#mode').val("topicSwitch");
    $('#quesform').attr("action", "controller.php");
    setTryingToUnload();
    document.forms[0].submit();
}

function daSessionExpire()
{
    var newMessage = "Your time is out. The system has auto-submitted your test. Please give us a feedback to continue to the results.";
    if(!$(".promptContainer").is(":visible"))
    {
        var prompts = new Prompt({
            text: newMessage,
            type: 'alert',
            label1 : 'Continue to Home page',
            promptId: "specifyAnswer",
            func1: function () {
                $.ajax('controller.php',
                {
                    type: 'post',
                    data: "mode=endDaTest&qno="+$("#qno").val()+"&status=3",
                    success: function (transport) {
                        resp = transport;
                        setTryingToUnload();
                        window.location.assign("da_feedbackForm.php");
                    }
                }
                );
            }
        });
    }
}
function wsSessionExpire()
{
    var newMessage = "Your time is out. The system has auto-submitted your worksheet. ";
    if(!$(".promptContainer").is(":visible"))
    {
        var usedTime = $('#countdown').html();
        var res = usedTime.split(" ");
        var data1 = $('.optionActive').text();
        var prompts = new Prompt({
            text: newMessage,
            type: 'alert',
            label1 : 'Continue to Worksheets page',
            promptId: "specifyAnswer",
            func1: function () {
                $.ajax('controller.php',
                {
                    type: 'post',
                    data: "mode=endWorksheet&qno="+$("#qno").val()+"&time="+res[0]+"&status=3&response="+data1,
                    success: function (transport) {
                        resp = transport;
                        setTryingToUnload();
                        window.location.assign("worksheetSelection.php");
                    }
                }
                );
            }
        });
    }
}
var sparkieInfoForDDHtml="";
function sparkieInfoForDD(showhide){
    if (showhide){
        $('#practiceCompletionPrompt #pmReport').hide();
        $('#practiceCompletionPrompt #pmSparkieInfo').show();
    }
    else {
        $('#practiceCompletionPrompt #pmSparkieInfo').hide();
        $('#practiceCompletionPrompt #pmReport').show();
    }
    
}
function ddSessionCompleted(){
    ddLevelsAttempted=JSON.parse(responseArray['levelsAttempted']);
    var pmlevels=ddLevelsAttempted;
    var accuracySparkies=responseArray['completionSparkie']['accuracy']==""?0:parseInt(responseArray['completionSparkie']['accuracy']);
    var speedSparkies=responseArray['completionSparkie']['speed']==""?0:parseInt(responseArray['completionSparkie']['speed']);
    var attemptNo = parseInt(responseArray['attemptNo']);
    var finalSpeedSparkies = speedSparkies-(attemptNo-1) > 0 ? speedSparkies-(attemptNo-1) : 0;
    var accuracy = responseArray['accuracy'];
    var avgTimePerQues = parseFloat(responseArray['avgTimePerQues']);
    var benchmark50Val = parseFloat(responseArray['benchmark50']);
    var benchmark75Val = parseFloat(responseArray['benchmark75']);
    var bubbleMsg = "";
    if(accuracy >= 70)
    {
        if(speedSparkies > 0)
        {
            if(finalSpeedSparkies > 0)
            {
                if(avgTimePerQues <= benchmark75Val)
                    bubbleMsg = "Hey!! You just got "+(finalSpeedSparkies)+" sparkies for superb speed.";
                else if(avgTimePerQues > benchmark75Val && avgTimePerQues <= benchmark50Val)
                    bubbleMsg = "Hey!! You just got "+(finalSpeedSparkies)+" sparkies for good speed.";
                else if(attemptNo<5)
                    bubbleMsg = "Hey!! You can earn additional sparkies if you answer quickly.";                
            }
        }
        else if(attemptNo<5)
            bubbleMsg = "Hey!! You can earn additional sparkies if you answer quickly.";
    }
    else
    {
        bubbleMsg = "Buckle up! You need more practice of this topic.";
    }
    var alertLabel="Continue";
    var colorForAccuracy = accuracy>=80 ? "green" : (accuracy<60 ? "red" : "yellow");
    var colorForSpeed = avgTimePerQues<=benchmark75Val ? "green" : (avgTimePerQues>benchmark50Val ? "red" : "yellow");
    var pmHTML='<div id="practiceCompletionPrompt"><div id="pmReport"><div style="border-bottom:1px solid #2f99cb;">You have completed the Practice!</div>';
    if ($('#fromChoiceScreen').length==0 || $('#fromChoiceScreen').val()!="1")
    {
        pmHTML+= '<div id="iconDiv" class="practiseSparkieDetails" title="Click to know more" style="width:100%;height:120px;display:inline-block;position: relative;cursor:pointer;">';
            pmHTML+='<div id="accuracyDiv">';
                pmHTML+='<div style="width: 60px;height: 50px;background-color: white;position: absolute;top: 25px;left: 35px;background: url(\'assets/'+colorForAccuracy+'_arrow_target.png\') no-repeat;background-size: 100% 100%;display: inline-block;"></div>';
                pmHTML+='<div style="width: 55px;font-size: 0.8em;position: absolute;top: 80px;left: 30px;border-top: 1px solid #2f99cb;text-align: left;">Accuracy</div>';
            pmHTML+='</div>';
            pmHTML+='<div id="speedDiv">';
                pmHTML+='<div style="width: 60px;height: 50px;background-color: white;position: absolute;top: 25px;right: 35px;background: url(\'assets/'+colorForSpeed+'_lightening.png\') no-repeat;background-size: 50% 100%;background-position: 50%;display: inline-block;"></div>';
                pmHTML+='<div style="width: 75px;font-size: 0.8em;position: absolute;top: 80px;right: 25px;border-top: 1px solid #2f99cb;">Speed</div>';
            pmHTML+='</div>';
        pmHTML+='</div>';


        pmHTML+= '<div id="numberDiv" class="practiseSparkieDetails" style="width:100%;height:120px;display:none;position: relative;cursor:pointer;">';
            pmHTML+='<div id="accuracyDiv">';
                pmHTML+='<div style="width: 60px;height: 50px;background-color: white;position: absolute;top: 25px;left: 28px;display: inline-block;">';
                    pmHTML+='<span style="font-size: 1.5em;line-height: 3;">'+accuracy+'</span><span style="font-size: 0.8em;position: relative;top: -8px;left: 3px;">%</span>'
                pmHTML+='</div>';
                pmHTML+='<div style="width: 55px;font-size: 0.8em;position: absolute;top: 80px;left: 30px;border-top: 1px solid #2f99cb;text-align: left;">Accuracy</div>';
            pmHTML+='</div>';
            pmHTML+='<div id="speedDiv">';
                pmHTML+='<div style="width: 60px;height: 50px;background-color: white;position: absolute;top: 25px;right: 40px;display: inline-block;">';
                    pmHTML+='<span style="font-size: 1.5em;line-height: 3;">'+avgTimePerQues+'</span><span style="font-size: 0.8em;position: relative;top: -8px;left: 3px;">s/ques</span>'
                pmHTML+='</div>';
                pmHTML+='<div style="width: 55px;font-size: 0.8em;position: absolute;top: 80px;right: 45px;border-top: 1px solid #2f99cb;">Speed</div>';
            pmHTML+='</div>';
        pmHTML+='</div>';

        alertLabel="Home";
    }
    pmHTML+='<div style="width:90%;margin:auto;">';
    $.each(pmlevels,function(index,item){
        pmHTML+='<div style="border-bottom:1px solid #2f99cb;text-align:left;padding:5px;">'+(item['levelType']=='timedTest'?'Timed Challenge <span style="display:inline-block;padding:2px;background:#04ed21;border-radius:5px;width:20%;float:right;text-align:center;">'+item['accuracy']+"%":'Level '+item['level']+' <span style="display:inline-block;padding:2px;background:#04ed21;border-radius:5px;width:20%;float:right;text-align:center;">'+item['score']+'/'+item['onTotal'])+'</span></div>';
        accuracySparkies+=(item['levelType']=='timedTest' && item['score']!=0)?(item['onTotal']==""?0:item['onTotal']*1):0;
    });
    pmHTML+='<div style="border-bottom:1px solid #2f99cb;text-align:left;padding:5px;"><img src="assets/sparkie.png" style="vertical-align:middle;" height="30px"> Sparkies Earned <a href="#" onclick="sparkieInfoForDD(1);"><sup>?</sup></a> <span style="display:inline-block;padding:2px;background:#2196f3;border-radius:5px;min-width:20%;max-width:25%;float:right;text-align:center;">'+accuracySparkies+' + '+finalSpeedSparkies+'</span></div>';
    if(bubbleMsg != "")
    {
        pmHTML+='<div class="practiseModulePromptBubble practiseSparkieDetails" title="Click To know more">'+bubbleMsg+'</div>';
    }
    pmHTML+='</div></div>';
    var sparkieInfoDD='<div id="pmSparkieInfo" style="display:none;"><table><tr><th style="color:white;background:#5F9EA0;" colspan="3">Sparkie Information</th></tr>';
        sparkieInfoDD+='<tr><th>Category</th><th>Percentage</th><th>Sparkie</th></tr>';
        sparkieInfoDD+='<tr><td>Daily Practice</td><td> &lt; 50 </td><td></td></tr>';
        sparkieInfoDD+='<tr><td>(Accuracy)</td><td>50 - 60</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr><td></td><td>60 - 70</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr><td></td><td>70 - 80</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr><td></td><td>80 - 90</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr><td></td><td>90 - 100</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr style="height:20px;"><td> </td><td> </td><td> </td></tr>';
        sparkieInfoDD+='<tr><td>Daily Practice</th><td>Percentile</td><td></td></tr>';
        sparkieInfoDD+='<tr><td>(Speed)</td><td>&lt; 50</td><td>None</td></tr>';
        sparkieInfoDD+='<tr><td></td><td>50 - 75</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr><td></td><td>75 - 100</td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr style="height:20px;"><td> </td><td> </td><td> </td></tr>';
        sparkieInfoDD+='<tr><td>Timed test</td><td> &gt; 75 </td><td><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"><img src="assets/sparkie.png" style="vertical-align:middle;" height="20px"></td></tr>';
        sparkieInfoDD+='<tr><td colspan="3">For each subsequent attempt, you get one sparkie less.(up to 5 attempts)</td></tr></table><center><input type="button" value="Back to Report" onclick="sparkieInfoForDD(0);"></center></div>';
        pmHTML+=sparkieInfoDD+'</div>';
    
    if (!$(".promptContainer").is(":visible")) {
        new Prompt({
            text: pmHTML,
            type: "alert",
            label1 : alertLabel,
            func1: function () {
                    setTryingToUnload();
                    $("#prmptContainer_resultPrompt").remove();
                    if ($('#fromChoiceScreen').length>0 && $('#fromChoiceScreen').val()=="1"){
                        $('#choiceScreenRedirectFromPractiseModule input').each(function(){
                            $(this).attr('name',$(this).attr('name').replace(/^choice([A-Z])/,function(all, letter){return letter.toLowerCase();}));
                        });
                        $('#choiceScreenRedirectFromPractiseModule').submit();
                    }
                    else window.location.assign("home.php");
                },
            promptId: "resultPrompt",
        });
        $("#promptBox").css({"width":"400px","margin-left":"32%","margin-top":"50px"});
        $(".practiseSparkieDetails").click(function(){
            $(".practiseModulePromptBubble").hide();
           if($("#numberDiv").is(":visible"))
           {
               $("#numberDiv").hide();
               $("#iconDiv").show();
           }
           else
           {
               $("#iconDiv").hide();
               $("#numberDiv").show();
           }
        });
    }
}
function ddSessionExpire()
{
    var pmlevels=ddLevelsAttempted;
    //var totalSparkies=responseArray['completionSparkie']==""?0:parseInt(responseArray['completionSparkie']);
    var alertLabel="Continue";
    var pmHTML='<div id="practiceCompletionPrompt"><div style="border-bottom:1px solid #2f99cb;">Time\'s Up! You will have an opportunity to complete it later. </div>';
        pmHTML+='<div style="width:100%;height:120px;position:relative;text-align:center;"><div style="top:0;left:0;width:50%;height:120px;float:left;padding:10px;box-sizing:border-box;z-index:10;position:absolute;text-align:left;">You\'ve reached:<br><span style="font-size:1.3em">'+$("#iTargetSpeed").val()+'</span></div>';
        if ($('#dailyDrillRightSection canvas').length>0){
            var c=$('#dailyDrillRightSection canvas')[0];
            pmHTML+='<div style="width:100%;height:120px;float:right;overflow:hidden;position:relative;"><img id="dailyDrillReportImg" src="'+c.toDataURL()+'" width="225" height="150" style="position:absolute;top:-25px;right:0px;"/></div>';
        }
        pmHTML+='</div>';
        alertLabel="Continue to Home page";
    pmHTML+='<div style="width:90%;margin:auto;">';
    $.each(pmlevels,function(index,item){
        pmHTML+='<div style="border-bottom:1px solid #2f99cb;text-align:left;padding:5px;">'+(item['levType']=='timedTest'?'Timed Challenge <span style="display:inline-block;padding:2px;background:#04ed21;border-radius:5px;width:20%;float:right;text-align:center;">'+((item['level']!==null)?(item['accuracy']+"%"):" &bull;&bull;&bull; "):'Level '+item['levelNumber']+' <span style="display:inline-block;padding:2px;background:#04ed21;border-radius:5px;width:20%;float:right;text-align:center;">'+((item['level']!==null)?item['score']+'/'+item['onTotal']:" &bull;&bull;&bull; "))+'</span></div>';
        //totalSparkies+=(item['levType']=='timedTest' && item['score']!=0)?(item['onTotal']==""?0:item['onTotal']*1):0;
    });
    pmHTML+='</div></div>';
    if (!$(".promptContainer").is(":visible")) {
        new Prompt({
            text: pmHTML,
            type: "alert",
            label1 : alertLabel,
            func1: function () {
                    setTryingToUnload();
                    $("#prmptContainer_resultPrompt").remove();
                    window.location.assign("home.php");
                },
            promptId: "resultPrompt",
        })
    }
    clearTimeout(slowLoadTimer);
    practiseModuleStatus=1;
    practiseModuleTimeStatus=1;
}
function quitPractiseModule(){
    if ($('#quesCategory').val() == 'practiseModule' && $('#fromChoiceScreen').length>0 && $('#fromChoiceScreen').val()=="1"){
        var newMessage='You have not completed this practice. You can choose to complete it from the choices at the end of the topic.';
        var prompts=new Prompt({
            text:newMessage,
            type:'alert',
            func1:function(){
                $('#choiceScreenRedirectFromPractiseModule input').each(function(){
                    $(this).attr('name',$(this).attr('name').replace(/^choice([A-Z])/,function(all, letter){return letter.toLowerCase();}));
                });
                setTryingToUnload();
                $('#choiceScreenRedirectFromPractiseModule').submit();
            },
            promptId:"createCustom"
        });
    }
}
function endsession() {
    if($('#quesCategory').val() == 'daTest' )
    {
        $('#qDAQuesPromptLoad').val(1);
        var lastAttemptQno = $('#qno').val();
        var usedTime = $('#countdown').html();
        var res = usedTime.split(" ");
        var data = $('.optionActive').text();
        var newMessage = "You are ending the session in between the test. In that case, you will not be allowed to review the flagged questions. Also, you cannot change the previously answered questions.";
        var prompts=new Prompt({
            text:newMessage,
            type:'confirm',
            label2:'Continue the Super Test',
            label1:'End Session, anyway!',
            func1:function(){
                $.ajax('controller.php',
                {
                    type: 'post',
                    data: "mode=endDaTest&qno="+lastAttemptQno+"&time="+res[0]+"&status=2&response="+data,
                    success: function (transport) {
                        resp = transport;
                        setTryingToUnload();
                        window.location.assign("home.php");
                    }
                }
                );
            },
            func2:function(){
                $("#prmptContainer_createCustom").remove();
            },
            promptId:"createCustom"
        });
        $("#promptBox").css("width","610px");
        $("#promptBox").css("margin-left","31%");
        $("#promptBox").css("margin-top","180px");
    }
    else if($('#quesCategory').val() == 'worksheet' )
    {
        $('#qDAQuesPromptLoad').val(1);
        var lastAttemptQno = $('#qno').val();
        var usedTime = $('#countdown').html();
        var res = usedTime.split(" ");
        var data = $('.optionActive').text();
        var newMessage = "Your responses for the worksheet will be saved. Do you want to end the session?";
        var prompts=new Prompt({
            text:newMessage,
            type:'confirm',
            label2:'Cancel',
            label1:'End Session!',
            func1:function(){                               
                setTryingToUnload();
                window.location.assign("worksheetSelection.php");                
            },
            func2:function(){
                $("#prmptContainer_createCustom").remove();
            },
            promptId:"createCustom"
        });
        $("#submitWorksheet").val(2);submitAnswer();
        $("#promptBox").css("width","610px");
        $("#promptBox").css("margin-left","31%");
        $("#promptBox").css("margin-top","180px");
    }
    else{
        $("#endSessionClick,#blackScreen").css("display", "block");
    }
}

function toughQuestionAlert() {
    timeTakenToughAlert = 0;
    startTheTimerToughAlert();
    disableEnableBoxes(true);
    $("#submitQuestion").css("display", "none");
    $("#skipQuestion").css("display", "none");
    $("#submitQuestion2").css("display", "none");
    $("#skipQuestion2").css("display", "none");

    if(isAndroid && isChrome)
        toughQuestionJsAlert();
    else
        $("#toughQuestionClick,#blackScreen").css("display", "block");
    giveResult = 1;
    noSubmit = 1;
}

function toughQuestionJsAlert()
{
    if(confirm(i18n.t("questionPage.toughQuestionText").replace(/<br>/g, '\n')))
        {
            cancelToughQuestionAlert();
        }
    else
        {
            submitToughQuestionAlert();
        }
}

function cancelToughQuestionAlert() {
    if (storageEnabled()) localStorage.setItem("toughDisabled", document.getElementById('dontShowTough').checked);
    noSubmit = 0;
    
    if (!(globalProcessquesType == 'MCQ-4' || globalProcessquesType == 'MCQ-3' || globalProcessquesType == 'MCQ-2')) {
        disableEnableBoxes(false);
        if (document.getElementById("b1"))
            document.getElementById('b1').focus();
        $("#submitQuestion").css("display", "block");
        showSkip('1');
        $("#submitQuestion2").css("display", "block");
        showSkip('3');
        $("#submitQuestion1").css("display", "block");
        showSkip('2');
    }
    $("#toughQuestionClick,#blackScreen").css("display", "none");
    toughQuestionStatus1 = 1;
    calcAns(globalProcessResult, globalProcessquesType);
    stopTheTimerTough();
    timeTakenToughAttempt = 0;
    startTheTimerToughAttempt();
}

function submitToughQuestionAlert() {
    stopTheTimerTough();
    if (storageEnabled()) localStorage.setItem("toughDisabled", document.getElementById('dontShowTough').checked);
    noSubmit = 0;
    
    disableEnableBoxes(false);
    $("#toughQuestionClick,#blackScreen").css("display", "none");
    giveResult = 1;
    toughQuestionStatus1 = 2;

    if (globalProcessquesType == "Blank")
        submitAnswer();
    else
        calcAns(globalProcessResult, globalProcessquesType);
}

function showSkip(interfaces) {
    var questionCode = $("#qcode").val();
    var check = skipQuestions.indexOf(questionCode);
    if (interfaces == 1) {
        if (check != -1 && $("#quesCategory").val() == "wildcard")
            $("#skipQuestion").css("display", "block");
        else
            $("#skipQuestion").css("display", "none");
    }
    if (interfaces == 2) {
        if (check != -1 && $("#quesCategory").val() == "wildcard")
            $("#skipQuestion1").css("display", "block");
        else
            $("#skipQuestion1").css("display", "none");
    }
    if (interfaces == 3) {
        if (check != -1 && $("#quesCategory").val() == "wildcard")
            $("#skipQuestion2").css("display", "block");
        else
            $("#skipQuestion2").css("display", "none");
    }

}

function disableEnableBoxes(status) {
    for (var j = 0; j < 7; j++) {
        if (document.getElementById('b' + j))
            document.getElementById('b' + j).disabled = status;
        if (document.getElementById('fracB_' + j)) {
            jQuery('#fracB_' + j).removeAttr('contenteditable').attr("disabled", status);
        }
    }
    var dropDownObj = document.getElementsByTagName("select");
    for (var j = 0; j < dropDownObj.length; j++) {
        if (dropDownObj[j].id.substr(0, 6) == "lstOpt")
            dropDownObj[j].disabled = status;
    }
}

function quitTopic() {
    redirect = 0;
    $('#mode').val("topicQuit");
    $('#quesform').attr("action", "controller.php");
    setTryingToUnload();
    document.forms[0].submit();
}
function sendMail(params) {
    try {
        var request = $.ajax('controller.php',
                        {
                            type: 'post',
                            data: params,
                            success: function (transport) {
                                resp = transport;
                                $('#comment').val("");
                            }
                        }
                        );
    }
    catch (err) { alert("sendMail " + err.description); }
}
function mailComment() {
    if ($("#commentInfo").is(":visible")) {
        $("body").css("overflow-y","hidden");
        if ($("#commentInfo").find("#selCategory").val() == "") {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "Please select category!", 
                    type: 'alert', 
                    promptId: "commentValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_commentValidation").remove();
                    }
                });
            }
            $('#commentInfo #selCategory').focus();
            return false;
        }
        else if ($("#commentInfo").find("#txtcomment").val() == "") {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "Please enter a comment!", 
                    type: 'alert', 
                    promptId: "commentValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_commentValidation").remove();
                    }
                });
            }
            $('#commentInfo #comment').focus();
            return false;
        }
        else {
            var params = {};
            params["problemid"]= $('#problemid').val();
            params["comment"]= $("#commentInfo").find("#txtcomment").val();
            params["quesNo"]= $('#qno').val() - 1;
            params["qcode"]= prevQcodeAfterNext;
            params["selCategory"]= $("#commentInfo").find("#selCategory").val();
            if(prevQuesCategoryAfterNext == "wildcard")
            {
                if(prevQuesSubCategoryAfterNext == "research")
                    params["type"] = "wildcard_research";
                else
                    params["type"] = "wildcard_normal";
            }
            else
                params["type"]= prevQuesCategoryAfterNext;
            params["previousQuestionDetails"]="";
            params["previousQuestion"]=1;
            params['dynamicParams'] = prevDynamicParamsAfterNext;
            params["mode"]="comment";
            sendMail(params);
        }
    }
    else {
        if (trim($('#selCategory').val()) == "") {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "Please select category!", 
                    type: 'alert', 
                    promptId: "commentValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_commentValidation").remove();
                    }
                });
            }
            $('#selCategory').focus();
            return false;
        }
        if (trim($('#txtcomment').val()) == "") {
            if(!$(".promptContainer").is(":visible"))
            {
                var prompts = new Prompt({ 
                    text: "Please enter a comment!", 
                    type: 'alert', 
                    promptId: "commentValidation", 
                    func1: function () { 
                        jQuery("#prmptContainer_commentValidation").remove();
                    }
                });
            }
            $('#txtcomment').focus();
            return false;
        }
        else {
            var params = {};
            params["problemid"] = $('#problemid').val();
            params["comment"] = $('#txtcomment').val();
            if (submitCheck == 1) {
                params["qcode"] = prevQcode;
                params["quesNo"] = prevQno;
                params["type"] = prevQuesCategory;
                params['subType'] = prevQuesSubCategory
                params['dynamicParams'] = prevDynamicParams;
            } else {
                params["quesNo"] = $('#qno').val();
                params["qcode"] = $('#qcode').val();
                params["type"] = $("#quesCategory").val();
                params['subType'] = $("#tmpMode").val();
                params['dynamicParams'] = $("#dynamicParams").val();
            }
            if(params["type"] == "wildcard")
            {
                if(params['subType'] == "research")
                    params["type"] = "wildcard_research";
                else
                    params["type"] = "wildcard_normal";
            }
            params["selCategory"] = $('#selCategory').val();
            if (prevQcodeAfterNext != "")
                params["previousQuestionDetails"] = prevQcodeAfterNext + "~" + prevQuesCategoryAfterNext;
            else
                params["previousQuestionDetails"] ="";
            if ($(".commentOn:checked").val() == 3)
                params["notRelatedToQuestion"] =1;
            params["mode"] ="comment";
            sendMail(params);
        }
    }
    hideCommentBox("submit");
}

function hideCommentBox(chkSource) {

    if ($("#commentInfo").is(":visible")) {
        $("#wildcardInfo,#cboxClose").click();
        $("body").css("overflow-y","hidden");
        $("#commentInfo").html("");
        $('#txtcomment').val("");
        $('#selCategory').val("");
        $("#commentOn1").attr("checked", true);
        $("#comment").attr("style","");
    }
    else {
        $('#txtcomment').val("");
        $('#selCategory').val("");
        $("#commentOn1").attr("checked", true);

        if (document.getElementById('result').value != "") {
            document.getElementById('pnlAnswer').style.display = "block";
        }
        document.getElementById('pnlQuestion').style.display = "block";
        $('#commentBox').css("display", "none");
    }
    if(chkSource!="submit")
        removeHoverClass();
}
function trim(query) {
    var s = query.replace(/\s+/g, "");
    return s.toUpperCase();
}
function unique(a) {
    var r = new Array();
    o: for (var i = 0, n = a.length; i < n; i++) {
        for (var x = 0, y = r.length; x < y; x++) {
            if (trim(r[x]) == trim(a[i]))
                continue o;
        }
        r[r.length] = a[i];
    }
    return r;
}
function stopTheTimer() {
    if (timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}
function stopTheTimerTough() {
    clearTimeout(timerIDTough);
}
function startTheTimer() {
    secs = secs + 1;
    timerRunning = true;
    timerID = window.setTimeout("startTheTimer()", 1000);
}
function startTheTimerToughAlert() {
    timeTakenToughAlert = timeTakenToughAlert + 1;
    timerIDTough = window.setTimeout("startTheTimerToughAlert()", 1000);
}
function startTheTimerToughAttempt() {
    timeTakenToughAttempt = timeTakenToughAttempt + 1;
    timerIDTough = window.setTimeout("startTheTimerToughAttempt()", 1000);
}
function initializeTimer() {
    secs = 0;
    disableSubmitButton();
    if (document.getElementById('btnTopicChange'))
        document.getElementById('btnTopicChange').disabled = false;
    startTheTimer();
}
function noimage(a) {
    var image_attempt = $(a).attr("data-atp");
    var image_src = $(a).attr("src").split("?");
    
    if(image_attempt == null || typeof(image_attempt) == "undefined" || image_attempt == "") image_attempt = 1;
    
    if(image_attempt > 5) {
        $(a).attr("onerror", "");   // disable onerror to prevent endless loop
        $.post("errorLog.php", "request- " + $("#quesform").find("input").serialize() + " imgName-" + image_src[0] + "&qcode=" + $("#qcode").val() + "&typeErrorLog=imageNotLoading", function (data) {
            
        });
    } else {
        image_attempt++;
        setTimeout(function () {
            $(a).attr("src", image_src[0]+"?"+(new Date()).getTime());
            $(a).attr("data-atp", image_attempt);
        }, 750);
    }
}
function checkImage(str) {
    var pic = new Array();
    var col_array = str.split(",");
    var part_num = 0;
    while (part_num < col_array.length) {
        try {
            imgName = col_array[part_num];
            pic[part_num] = new Image();
            pic[part_num].src = col_array[part_num];
        } catch(err) {
            // do nothing
        }
        part_num++;
    }
}
function checkKeyPress(e) {
    var keyPressed = e ? e.which : window.event.keyCode;
    if (noSubmit != 1) {
        if (keyPressed == 13)        //13 implies enter key
        {
            var setAllowSend = $("#setAllowSend").val();
             if((!$("#clusterQuestionMessage").is(':focus'))){
                if (!$('.promptContainer').is(":visible") && !$('#clusterStatusPrompts').is(":visible") && !$('#commentBox').is(":visible")) {
                    if ($('#result').val() == "") {
                        if($("#quesCategory").val() == "daTest") {
                            //submitAnswer();
                        }
                        else if($("#tmpMode").val() == "NCERT") {
                            submitAnswer();
                        } else if (newQues.quesType.substring(0, 3) == "MCQ" && $("#quesCategory").val() != "worksheet") { 
                            if(!$(".promptContainer").is(":visible"))
                            {
                                var prompts = new Prompt({ 
                                    text: i18n.t("questionPage.answerNotGivenMsg"), 
                                    type: 'alert', 
                                    promptId: "specifyAnswer", 
                                    func1: function () { 
                                        jQuery("#prmptContainer_specifyAnswer").remove();
                                    }
                                });
                            }
                        } else {
                            submitAnswer();
                        }
                    }
                    else if($("#clusterQuestionMessage").is(":visible") && setAllowSend == 0){
                        if(setAllowSend == 0){
                            var cqtextstr = $("#clusterQuestionMessage").val();
                            if(cqtextstr.replace(/\s/g, '').length != 0){
                                if (!$(".promptContainer").is(":visible")) {
                                    new Prompt({
                                        text: "You haven't submitted your explanation.Click on the send button below your explanation to send your answer to Team Mindspark.",
                                        type: "alert",
                                        promptId: "cqprompt",
                                        func1: function() {
                                            $("#setAllowSend").val("1");
                                            jQuery("#prmptContainer_cqprompt").remove();
                                            $("#clusterQuestionMessage").prop("disabled", false);
                                            $("#cqebutton").prop("disabled", false);
                                        }
                                    })
                                }
                                
                                $("#clusterQuestionMessage").attr("disabled","true");
                                $("#cqebutton").attr("disabled","true");
                                return false;
                            }           
                        }
                    }
                    //else if ($("#tmpMode").val() != "practice" && $("#tmpMode").val() != "NCERT" && !$('#pnlLoading').is(":visible")) {
                    else if ($("#tmpMode").val() != "NCERT" && !$('#pnlLoading').is(":visible")) {
                        if (allowed == 1) {
                            $('#nextQuestion1').css("display", "none");
                            handleClose();
                        }
                    }
                }
        }
        }
    }
}

function my_onkeydown_handler(ev) {
    var ev = ev || window.event;

    //Code for Key press event, include third party shortcut.js works in IE & FF - 06- oct 09
    shortcut.add("Ctrl+F5", function () {
        return false;
    });
    shortcut.add("Alt+LEFT", function () {
        return false;
    });
    shortcut.add("Alt+RIGHT", function () {
        return false;
    });
    shortcut.add("Ctrl+R", function () {
        return false;
    });
    shortcut.add("Backspace", function () {
        return;
    });
    shortcut.add("Alt+Backspace", function () {
        return false;
    });
    switch (ev.keyCode) {
        case 13: //enter
            redirect = 0;
            break;
        case 116:
            if (document.getElementById('result').value != "") {
                cancelKey(ev);
                return false;
            }
    }
}
function cancelKey(evt) {
    if (evt.preventDefault) {
        evt.preventDefault();
        document.getElementById('mode').value = "refresh";
        redirResult();
        return false;
    }
    else {
        evt.keyCode = 0;
        evt.returnValue = false;
        document.getElementById('mode').value = "refresh";
        redirResult();
    }
}

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

shortcut = {
    'all_shortcuts': {}, //All the shortcuts are stored in this array
    'add': function (shortcut_combination, callback, opt) {
        //Provide a set of default options
        var default_options = {
            'type': 'keydown',
            'propagate': false,
            'disable_in_input': true,
            'target': document,
            'keycode': false
        }
        if (!opt) opt = default_options;
        else {
            for (var dfo in default_options) {
                if (typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
            }
        }

        var ele = opt.target;
        if (typeof opt.target == 'string') ele = document.getElementById(opt.target);
        var ths = this;
        shortcut_combination = shortcut_combination.toLowerCase();

        //The function to be called at keypress
        var func = function (e) {
            e = e || window.event;

            if (opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
                var element;
                if (e.target) element = e.target;
                else if (e.srcElement) element = e.srcElement;
                if (element.nodeType == 3) element = element.parentNode;

                if (element.tagName == 'INPUT' && element.type == 'text' || element.tagName == 'TEXTAREA') return;
            }

            //Find Which key is pressed
            if (e.keyCode) code = e.keyCode;
            else if (e.which) code = e.which;
            var character = String.fromCharCode(code).toLowerCase();

            if (code == 188) character = ","; //If the user presses , when the type is onkeydown
            if (code == 190) character = "."; //If the user presses , when the type is onkeydown

            var keys = shortcut_combination.split("+");
            //Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
            var kp = 0;

            //Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
            var shift_nums = {
                "`": "~",
                "1": "!",
                "2": "@",
                "3": "#",
                "4": "$",
                "5": "%",
                "6": "^",
                "7": "&",
                "8": "*",
                "9": "(",
                "0": ")",
                "-": "_",
                "=": "+",
                ";": ":",
                "'": "\"",
                ",": "<",
                ".": ">",
                "/": "?",
                "\\": "|"
            }
            //Special Keys - and their codes
            var special_keys = {
                'esc': 27,
                'escape': 27,
                'tab': 9,
                'space': 32,
                'return': 13,
                'enter': 13,
                'backspace': 8,

                'scrolllock': 145,
                'scroll_lock': 145,
                'scroll': 145,
                'capslock': 20,
                'caps_lock': 20,
                'caps': 20,
                'numlock': 144,
                'num_lock': 144,
                'num': 144,

                'pause': 19,
                'break': 19,

                'insert': 45,
                'home': 36,
                'delete': 46,
                'end': 35,

                'pageup': 33,
                'page_up': 33,
                'pu': 33,

                'pagedown': 34,
                'page_down': 34,
                'pd': 34,

                'left': 37,
                'up': 38,
                'right': 39,
                'down': 40,

                'f1': 112,
                'f2': 113,
                'f3': 114,
                'f4': 115,
                'f5': 116,
                'f6': 117,
                'f7': 118,
                'f8': 119,
                'f9': 120,
                'f10': 121,
                'f11': 122,
                'f12': 123
            }

            var modifiers = {

                shift: { wanted: false, pressed: false },
                ctrl: { wanted: false, pressed: false },
                alt: { wanted: false, pressed: false },
                meta: { wanted: false, pressed: false}  //Meta is Mac specific
            };

            if (e.ctrlKey) modifiers.ctrl.pressed = true;
            if (e.shiftKey) modifiers.shift.pressed = true;
            if (e.altKey) modifiers.alt.pressed = true;
            if (e.metaKey) modifiers.meta.pressed = true;

            for (var i = 0; k = keys[i], i < keys.length; i++) {
                //Modifiers
                if (k == 'ctrl' || k == 'control') {
                    kp++;
                    modifiers.ctrl.wanted = true;
                } else if (k == 'shift') {
                    kp++;
                    modifiers.shift.wanted = true;
                } else if (k == 'alt') {
                    kp++;
                    modifiers.alt.wanted = true;
                } else if (k == 'meta') {
                    kp++;
                    modifiers.meta.wanted = true;
                } else if (k.length > 1) { //If it is a special key
                    if (special_keys[k] == code) kp++;

                } else if (opt['keycode']) {
                    if (opt['keycode'] == code) kp++;

                } else { //The special keys did not match
                    if (character == k) kp++;
                    else {
                        if (shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
                            character = shift_nums[character];
                            if (character == k) kp++;
                        }
                    }
                }
            }

            if (kp == keys.length &&
                            modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
                            modifiers.shift.pressed == modifiers.shift.wanted &&
                            modifiers.alt.pressed == modifiers.alt.wanted &&
                            modifiers.meta.pressed == modifiers.meta.wanted) {
                callback(e);

                if (!opt['propagate']) { //Stop the event
                    //e.cancelBubble is supported by IE - this will kill the bubbling process.
                    e.cancelBubble = true;
                    e.returnValue = false;
                    //e.stopPropagation works in Firefox.
                    if (e.stopPropagation) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    return false;
                }
            }
        }
        this.all_shortcuts[shortcut_combination] = {
            'callback': func,
            'target': ele,
            'event': opt['type']
        };
        //Attach the function with the event
        if (ele.addEventListener) ele.addEventListener(opt['type'], func, false);
        else if (ele.attachEvent) ele.attachEvent('on' + opt['type'], func);
        else ele['on' + opt['type']] = func;
    }
}

function saveEmot(emot, ele) {
    toolClick = 1;
    toolbar();
    var emotArray = new Array();
    emotArray["Like"] = 1;
    emotArray["Dislike"] = 2;
    emotArray["I am loving it!"] = 3;
    emotArray["Great question!"] = 4;
    emotArray["Too easy."] = 5;
    emotArray["I hate this question."] = 6;
    emotArray["Questions are repeating."] = 7;
    emotArray["I didn't understand this question."] = 8;
    emotArray["No idea how to solve this."] = 9;
    emotArray["Question is confusing."] = 10;
    emotArray["Question is difficult."] = 11;
    emotArray["Excited"] = 12;
    emotArray["Confused"] = 13;
    emotArray["Bored"] = 14;
    /*if(emot == "Like" || emot == "Dislike" || emot == "Comment")
    {*/
    ele.parent().addClass("hoverClass");
    if (emot == "Comment") {
        //showCommentBox();
    }
    /*}*/
    if (emot != "Comment") {
        emotID = emotArray[emot];
    }
    window.clearTimeout(etTimer); //Prevent Event Bubble
    //etTimer = window.setTimeout(function (){hideEmotToolbar(); },1500);
}

function saveEmotByAjax(emotID) {
    if (emotID != "") {
        var qcode = prevQcode;
        $.post("controller.php", "qcode=" + qcode + "&emotID=" + emotID + "&mode=saveEmot", function (data) {
            emoteToolbarTagCount++;
            if (emoteToolbarTagCount == 10) {
                $(".emotIcons").find("label").remove();
                $(".emotIcons").append('<h4 align="center">You have already tagged 10 questions!</h4>');
            }
        })
    }
    emotID = "";
}

function animateSparkie(id) {
    if (id == "noOfSparkie")
        var newID = "#noOfSparkie";
    else
        var newID = ".bubble";
    $(newID).animate({ borderSpacing: 360 }, {
        step: function (now, fx) {
            $(this).css('-webkit-transform', 'rotate(' + now + 'deg)');
            $(this).css('-moz-transform', 'rotate(' + now + 'deg)');
            $(this).css('-ms-transform', 'rotate(' + now + 'deg)');
            $(this).css('-o-transform', 'rotate(' + now + 'deg)');
            $(this).css('transform', 'rotate(' + now + 'deg)');
        },
        duration: 2500
    }, 'linear');
}
function showClassLevelCompletion() {
    redirect = 0;
    $('#quesform').append('<input type="hidden" name="fromQuesPage" value="1">');
    document.quesform.action = 'classLevelCompletion.php';
    setTryingToUnload();
    document.quesform.submit();
}
function clickIE() {
    if (document.all) {
        window.status = message;
        return false;
    }
}
function clickNS(e) {
    if (document.layers || (document.getElementById && !document.all)) {
        if (e.which == 2 || e.which == 3) {
            window.status = message;
            return false;
        }
    }
}
function getFlashMovieObject(simplemovieQ) {
    if (window.document.simplemovieQ)
        return window.document.simplemovieQ;
    if (navigator.appName.indexOf("Microsoft Internet") == -1) {
        if (window.document.controller.embeds && window.document.controller.embeds[simplemovieQ]) {
            return window.document.controller.embeds[simplemovieQ];
        }
    }
    else
        return window.document.getElementById("simplemovieQ");
}
function quitHigherLevel() {
    $("#higherLevelClick,#blackScreen").show();
}
function showTagBox(id, visibility, qcode) {
    document.getElementById('showTaggedQcode').innerHTML = "Need to modify qcode: " + qcode;
    document.getElementById("tagQcode").value = qcode;
    document.getElementById(id).style.display = visibility;
    document.getElementById("tagComment").value = '';
    document.getElementById("tagComment").focus();
}
function stopAnswerTimer() {
    clearTimeout(timeToAnswerID);
    timeToAnswer = 0;
}
function startAnswerTimer() {
    timeToAnswer = parseInt(timeToAnswer + 1);
    timeToAnswerID = window.setTimeout("startAnswerTimer()", 1000);
}
function stopHintTimer() {
    clearTimeout(timeTakenToViewHintID);
    timeTakenToViewHint = 0;
}
function startHintTimer() {
    timeTakenToViewHint = parseInt(timeTakenToViewHint + 1);
    timeTakenToViewHintID = window.setTimeout("startHintTimer()", 1000);
}

function playVoiceover(mode) {
    if (mode == 'Q') {
        var one = document.getElementById('one');
        voicePlaying = isPlaying(one);
        if(voicePlaying==0)
        {
            $('#voiceOverImage').css('background-position','-65px -5px');
            one.play();
            $.post("controller.php", "mode=voiceOverLog&qcode=" + $('#qcode').val() , function (data) {
            });
        }
    }
}
function sparkieForExplaination() {
    return false;

    quesNoInFlow++;
    if (quesNoInFlow == 5) {
        quesNoInFlow = 0;
        sparkieForExplainationFlag = 1;
    }
    if (sum(timeTakenExplainationArr) / sum(timeTakenAttemptArr) > 0.4 && sparkieForExplainationFlag == 1) {
        timeTakenAttemptArr = new Array(0, 0, 0, 0, 0);
        timeTakenExplainationArr = new Array(0, 0, 0, 0, 0);
        sparkieForExplainationFlag = 0;
        quesNoInFlow = 0;
        return true;
    }
    else
        return false;
}

function sum(arr) {
    var num = 0;
    for (var i = 0; i < arr.length; i++) {
        num += parseInt(arr[i]);
    }
    return num;
}


function getImportantQues() {
    if (confirm("You will get only important questions")) {
        $("#report").remove();
        $("#impQuesMode").val("1");
        var qno = document.getElementById('qno').value;
        params = "qcode=0&mode=firstQuestion&quesCategory=" + category + "&qno=" + qno + "&impQuesMode=1";
        getNextQues(params, "normal");
        fetchNextQues();
    }
}

function logoff() {
    redirect = 0;
    finalSubmit(6);
}

function frustrationModel() {
    this.arrQuesResult = Array(5); //(a[i])
    this.Feature1 = 0;
    this.Feature2 = 0;
    this.Feature3 = 0;
    this.Feature4 = 0;
    this.Feature5 = 0;
    this.msgCount = 0;
    this.avgResponseTime = 22;    //22 sec
    this.responseRate = 80;    //get it from the db - %correct for the current Question    (RR))
    this.F_current = 0;    //frustration Index;
    this.F_prev = 0;
    this.frustInst = 0;
    for (var i = 0; i < 5; i++)
        this.arrQuesResult[i] = "";
}
frustrationModel.prototype.getFrustrationMsg = function (result, quesType, qno, timeTaken) {
    var msg = "";
    this.calculateFrustrationIndex(result, quesType, qno, timeTaken);
    this.msgCount = this.Feature1 + this.Feature2 + this.Feature3 + this.Feature4;
    if (this.F_current > 0.49) {
        this.frustInst++;
        if ($("#childClass").val() < 8 || rewardSystem == 1)
            var str = "Sparkie";
        else
            var str = "Reward Point";
        if (this.msgCount == 0)
            msg = "";
        else if (this.msgCount == 1)
            msg = this.getMsgForEvent2(quesType, timeTaken);    //event E2
        else if (this.msgCount == 2)
            msg = this.getMsgForEvent3(timeTaken);    //event E3
        else if (this.msgCount == 3)
            msg = this.getMsgForEvent4(timeTaken, str);    //event E4
        else if (this.msgCount == 4)
            msg = this.getMsgForEvent5(timeTaken, str);    //event E5
        else if (this.msgCount == 5)
            msg = this.getMsgForEvent6(timeTaken);    //event E6
    }
    return msg;
}
frustrationModel.prototype.calculateFrustrationIndex = function (result, quesType, qno, timeTaken) {
    this.F_prev = this.F_current;
    var index = 4;
    //alert(qno + " - " + index);
    var I = quesType == "challenge" ? 1 : 0;
    if (quesType == "normal") {
        for (var i = 1; i < 5; i++) {
            this.arrQuesResult[i - 1] = this.arrQuesResult[i];
        }
        this.arrQuesResult[i - 1] = result;
    }
    if (quesType == "normal" && qno <= 4)
        this.F_current = 0;
    else {
        this.Feature1 = (1 - this.arrQuesResult[index]) * (1 - I);
        this.Feature2 = ((this.arrQuesResult[index - 2] * this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index])) + this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index])) * (1 - I);
        this.Feature3 = ((this.arrQuesResult[index - 4] * this.arrQuesResult[index - 3] * this.arrQuesResult[index - 2] * this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index])) + (this.arrQuesResult[index - 3] * this.arrQuesResult[index - 2] * this.arrQuesResult[index - 1] * (1 - this.arrQuesResult[index]))) * (1 - I);

        this.Feature4 = I * (1 - result);
        this.Feature5 = timeTaken;
        this.F_current = 0.8 * (0.147 + 0.423 * (this.Feature1 - 0.25) - 0.0301 * (this.Feature2 - 0.25) / 2 + 0.0115 * (this.Feature3 - 0.11) / 2 + 0.8359 * (this.Feature4 - 0.04) + 0.1864 * (this.Feature5 - 22.5) / 300) + (0.2 * this.F_prev);
    }

};
frustrationModel.prototype.getMsgForEvent2 = function (quesType, timeTaken) {
    var msg = "";
    if (quesType == "normal") {
        if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
            msg = "Good, you tried hard to get the correct answer. Keep trying.";
        else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
            msg = "Try harder, you might get the correct answer next time.";
        else if (this.frustInst == 2 && this.responseRate > 50)
            msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
        else if (this.frustInst == 2 && this.responseRate < 50)
            msg = "It seems this question is tough, your friends felt the same, try again";
        else if (this.frustInst == 3)
            msg = "Would you like to give your feedback?";
    }
    else if (quesType == "challenge") {
        if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
            msg = "You did well in the last four questions, and have tried hard to answer this challenge question too. Keep trying, you may solve it next time.";
        else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
            msg = "You did well in the last four questions. Try harder, you may solve it next time.";
        else if (this.frustInst == 2)
            msg = "Don't worry, this is a tough question for many of your friends too. You can attempt it again.";
        else if (this.frustInst == 3)
            msg = "Would you like to give your feedback?";
    }
    return msg;
};

frustrationModel.prototype.getMsgForEvent3 = function (timeTaken) {
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last question, and have tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last question. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this question is tough, your friends felt the same, try again";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
frustrationModel.prototype.getMsgForEvent4 = function (timeTaken, str) {
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last two questions, and have tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last two questions. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this is a tough question for many of your friends too. Try again. You may get a " + str + " next time.";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
frustrationModel.prototype.getMsgForEvent5 = function (timeTaken, str) {
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last three questions and got a " + str + "! You tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last three questions. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this is a tough question for many of your friends too. Try again.";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
frustrationModel.prototype.getMsgForEvent6 = function (timeTaken) {
    var msg = "";
    if (this.frustInst == 1 && timeTaken > this.avgResponseTime)
        msg = "You did well in the last four questions, and have tried hard to answer this question too. I am sure you will do well in the next questions.";
    else if (this.frustInst == 1 && timeTaken < this.avgResponseTime)
        msg = "You did well in the last four questions. Try hard, I am sure you will do well in the next questions.";
    else if (this.frustInst == 2 && this.responseRate > 50)
        msg = "It is okay to get the wrong answer sometimes. You may have found the questions hard, but practice will make it easier. Try again.";
    else if (this.frustInst == 2 && this.responseRate < 50)
        msg = "It seems this is a tough question for many of your friends too. Try again. You may get a Challenge Question next time.";
    else if (this.frustInst == 3)
        msg = "Would you like to give your feedback?";
    return msg;
};
function storageEnabled() {
    try {
        localStorage.setItem("__test", "data");
    } catch (e) {
        //if (/QUOTA_?EXCEEDED/i.test(e.name)) {
            return false;
        //}
    }
    return true;
}
function redirectDiagnostic(lastQcode) {
    var mode = lastQcode;
    if (document.getElementById('remedialMode').value == 1) {
        document.getElementById('diagnosticTest').action = "remedialItem.php";
    }
    else if (document.getElementById('timedtestMode').value == 1) {
        document.getElementById('diagnosticTest').action = "timedTest.php";
    }
    else if (document.getElementById('activityMode').value == 1) {
        document.getElementById('diagnosticTest').action = "enrichmentModule.php";
    }
    else if (document.getElementById('clusterMode').value == 1) {
        document.getElementById('mode').value = "comprehensiveAfterActivity";
        document.getElementById('diagnosticTest').action = "controller.php";
    }
    else if (mode == '-2' || mode == '-3' || mode == '-5' || mode == '-6') {
        document.getElementById('mode').value = mode;
        document.getElementById('diagnosticTest').action = "endSessionReport.php";
    }
    // else if (mode == '-8') {
    //     document.getElementById('diagnosticTest').action = "classLevelCompletion.php";
    // }
    else if (mode == '-1') {
        document.getElementById('mode').value = mode;
        document.getElementById('diagnosticTest').action = "dashboard.php";
    }
    else {
        document.getElementById('diagnosticTest').action = "question.php";
    }
        setTryingToUnload();
        document.getElementById('diagnosticTest').submit();
}

function isPlaying(player) {
    return !(player.ended||player.paused)  && 0 < player.currentTime;
}

function voicePlayFinished()
{
    $('#voiceOverImage').css('background-position','-5px -5px');
}

function redirect_to_ncert_exercises() {
    redirect = 0;
    setTryingToUnload();
    if (userType == 'msAsStudent')
        window.location.href = "home.php";
    else
        window.location.href = "homeworkSelection.php";
}

function saveNCERTAnswer() {
    var groupNo = $(".groupNav.current").attr("id").replace(/groupNav/g, "");
    groupNo = parseInt(groupNo);
    
    if (($("#submitQuestion").is(":visible") || $("#submitQuestion2").is(":visible")) && !isNaN(groupNo)) {
        allowed = 1;
        $('#quesform').attr("disabled", false);
        
        var autoSaveAnswer = new Array();
        var equationEditorAnswer = new Array();
        $(".singleQuestion").each(function (index, element) {
            var singleQuestion = "";
            var singleQuestionEE = "";
            $(this).contents().find("select").each(function () {
                singleQuestion += $(this).val() + "|";
            })
            $(this).contents().find("input, iframe").each(function () {
                if ($(this).hasClass("openEnded")) {
                    singleQuestionEE += $(this)[0].contentWindow.storeAnswer('') + "@$*@";
                    singleQuestionEE += $(this)[0].contentWindow.tools.save();
                }
                else if ($(this).attr("type") == "text") {
                    singleQuestion += $(this).val() + "|";
                }
                else if ($(this).attr("type") == "radio") {
                    if ($(this).is(":checked")) {
                        singleQuestion += $(this).val() + "|";
                    }
                }
            })
            if (singleQuestion != "")
                singleQuestion = singleQuestion.substring(0, singleQuestion.length - 1);
            // if (singleQuestionEE != "")
                // singleQuestionEE = singleQuestionEE.substring(0, singleQuestionEE.length - 1);
            autoSaveAnswer.push(singleQuestion);
            equationEditorAnswer.push(removeInvalidChars(singleQuestionEE));
        });
        var autoSavedAnswer = autoSaveAnswer.join("##");
        var equationEditorAnswer = equationEditorAnswer.join('##');
        $('#userResponse').val(autoSavedAnswer);
        $('#eeResponse').val(equationEditorAnswer);
        
        $("#mode").val("submitAnswer");
        $('#mode').val("fetchNCERTQuestion");
        $('#result').val(groupNo);
        $('#nextQuesLoaded').val("0");
        var params = $("#quesform").find("input").serialize();
        getNextQues(params, "normal");
        fetchNextQues();
    }
}

// Functions for replecation of topic prompt
function openTopicRepeatAttempt(topicRepeatAttempt) {
    if(topicRepeatAttempt >= 3 && topicRepeatAttempt <= 5) {
        var text_to_display = i18n.t("questionPage.topicRepeatAttemptPrompt1");
        if(topicRepeatAttempt == 3)
            text_to_display = text_to_display.replace("###", topicRepeatAttempt+"rd");
        else
            text_to_display = text_to_display.replace("###", topicRepeatAttempt+"th");
        
        $("#promptHeader").append(i18n.t("questionPage.topicRepeatAttemptPromptHead"));
        $("#promptData").html(text_to_display);

        if(isAndroid && isChrome)
            JsAlertopenTopicRepeatAttempt(text_to_display)
        else
            $("#topicRepeatAttempt,#blackScreen").show();

        noSubmit = 1;
        
    } else if(topicRepeatAttempt >= 6 && topicRepeatAttempt <= 9) {
        var text_to_display = i18n.t("questionPage.topicRepeatAttemptPrompt2");
        text_to_display = text_to_display.replace("###", topicRepeatAttempt+"th");
        text_to_display += (topicRepeatAttempt == 6) ? " "+i18n.t("questionPage.topicRepeatCQTextAppend") : "";     // if attempt is 6th, add CQ prompt text
        
        $("#promptHeader").append(i18n.t("questionPage.topicRepeatAttemptPromptHead"));
        $("#promptData").html(text_to_display);

        if(isAndroid && isChrome)
            JsAlertopenTopicRepeatAttempt(text_to_display)
        else
            $("#topicRepeatAttempt,#blackScreen").show();
        noSubmit = 1;
        
    } else if(topicRepeatAttempt >= 10 && topicRepeatAttempt <= 20) {
        var text_to_display = i18n.t("questionPage.topicRepeatAttemptPrompt3");
        text_to_display = text_to_display.replace("###", topicRepeatAttempt+"th");
        
        $("#promptHeader").append(i18n.t("questionPage.topicRepeatAttemptPromptHead"));
        $("#promptData").html(text_to_display);

        if(isAndroid && isChrome)
            JsAlertopenTopicRepeatAttempt(text_to_display)
        else
            $("#topicRepeatAttempt,#blackScreen").show();
        noSubmit = 1;
        
    } else if(topicRepeatAttempt >= 21) {
        var text_to_display = i18n.t("questionPage.topicRepeatAttemptPrompt4");
        if((topicRepeatAttempt % 10) == 1 && ((topicRepeatAttempt % 100) < 10 || (topicRepeatAttempt % 100) > 20))
            text_to_display = text_to_display.replace("###", topicRepeatAttempt+"st");
        if((topicRepeatAttempt % 10) == 2 && ((topicRepeatAttempt % 100) < 10 || (topicRepeatAttempt % 100) > 20))
            text_to_display = text_to_display.replace("###", topicRepeatAttempt+"nd");
        if((topicRepeatAttempt % 10) == 3 && ((topicRepeatAttempt % 100) < 10 || (topicRepeatAttempt % 100) > 20))
            text_to_display = text_to_display.replace("###", topicRepeatAttempt+"rd");
        else
            text_to_display = text_to_display.replace("###", topicRepeatAttempt+"th");
        
        $("#promptHeader").append(i18n.t("questionPage.topicRepeatAttemptPromptHead"));
        $("#promptData").html(text_to_display);

        if(isAndroid && isChrome)
            JsAlertopenTopicRepeatAttempt(text_to_display)
        else
            $("#topicRepeatAttempt,#blackScreen").show();
        noSubmit = 1;
    }
}
function closeTopicRepeatAttempt() {
    $("#topicRepeatAttempt,#blackScreen").hide();
    noSubmit = 0;
}
function closeDiagnosticTest() {
    $("#colorbox,#cboxOverlay").hide();   
}
function JsAlertopenTopicRepeatAttempt(text_to_display)
{
    alert(text_to_display);
    closeTopicRepeatAttempt();
}

function endsessionJsAlert()
{
    if($('#quesCategory').val() == 'worksheet' )
    {
        endsession(); return;
    }
    if(confirm(i18n.t("questionPage.endSessionText")))
    {
        setTryingToUnload();
        finalSubmit(1);
    }
}

function endtopicJsAlert()
{
    if(confirm(i18n.t("questionPage.endTopicText")))
                {
                    setTryingToUnload();
                    changeTopic();
                }
}

function quittopicJsAlert()
{
    if(confirm(i18n.t("questionPage.quitHighLevelText")))
                {
                    quitTopic();
                }
}
function checkSubmitVisibility(str){
    if(str.replace(/\s/g, '').length > 0) $("#cqebutton").show(); else $("#cqebutton").hide();
}
function saveClusterQuestionMessage(){
    var qcodeToPass ="";
    if(document.getElementById('userResponse').value == ""){
        qcodeToPass = $("#qcode").val();
    }
    else{
        qcodeToPass = prevQcode;
    }
    //var userResponce = escape($("#clusterQuestionMessage").val());
    //var data = 'cqUserResponse='+ userResponce +'&qcode='+ qcodeToPass + '&mode=saveCQUserResponse';
    $.ajax({
          url: "controller.php",
          data: {
              cqUserResponse : ""+$("#clusterQuestionMessage").val()+"",
              qcode : qcodeToPass,
              mode : "saveCQUserResponse"
          },
          cache: false,
          type: "POST",
          success: function(response){
              var msg = "Thanks! Your explanation has been sent to Team Mindspark!";
              $(".cq-message-div").css('display', 'none');
              $("#clusterQuestionMessage").css('display', 'none');
              $("#cqebutton").css('display', 'none');
              $("#clusterQuestionMessage").val("")
              if (!$(".promptContainer").is(":visible")) {
                  new Prompt({
                      text: msg,
                      type: "alert",
                      promptId: "markRightCondition1",
                      func1: function() {
                          jQuery("#prmptContainer_markRightCondition1").remove()
                      }
                  })
              }
          }
        });
    $(".cq-message-div").css('display', 'none');
    $("#clusterQuestionMessage").val("");
    $("#cqebutton").css('display', 'none');
}
var blinkThrice;
function blinkThrice(el) {
  var count = 0, $div = $(el), blinkInterval = setInterval(function() {
    if ($div.hasClass('orange')) {
      $div.removeClass('orange'); ++count;
    }
    else
      $div.addClass('orange');

    if (count === 3) clearInterval(blinkInterval);
  }, 300);
}
function setDdParameters(currentScore,currentLevel){
    if (typeof currentScore=='undefined') return;
    $("#iTargetSpeed").val(currentScore);
    drawWithInputValue(currentScore);
    $("#currentScore").html(currentScore+'<style>.orange{color:orange;}</style>');
    if (typeof currentLevel=='undefined') return;
    var oldLevel=$("#currentLevel").html();
    $("#currentLevel").html(currentLevel);
    if (oldLevel!=currentLevel) blinkThrice($("#currentLevel").parent());
   /* if(currentLevel == 4){
        if ($('.timedTestForDd').length>0){
            if (!$(".promptContainer").is(":visible")) {
                new Prompt({
                    text: "Timed Challenge begins in: <b id='timedTestCountDown'></b>",
                    type: "alert",
                    func1: function () {
                          $("#prmptContainer_ttprompt").remove();
                          clearInterval(timedPromptTimer);
                          $(".timedTestForDd").trigger("click");
                      },
                    promptId: "ttprompt",
                    
                })
            }
            var display = $('#timedTestCountDown');
            startTimedTestTimer(3, display);
        }
        else{
            $("#currentLevel").html(3);
            setTryingToUnload();
            var ddTtParameters =  "mode=completePractiseModule";
            $.ajax('controller.php',{
                    type: 'post',
                    data: ddTtParameters,
                    success: function (result) {
                    }
                }
            );
        }
    }*/
}
function redirectFromPractiseModule(){
    setTryingToUnload();
    if ($('#fromChoiceScreen').length>0 && $('#fromChoiceScreen').val()=="1")
        window.location.href("dashboard.php");        
    else window.location.href("home.php");
}

function CheckTimedTestStatus(){
    if ($('#pnlLoading').is(":visible")) $('#pnlLoading').css("display", "none");
    var getId = $(".fancybox-wrap").contents().find("iframe").attr("id");

    var timedTestComplete = document.getElementById(getId).contentWindow.ddStatus;
    var timedTestClosed = document.getElementById(getId).contentWindow.closeDD;
    var timedTestAttemptId = document.getElementById(getId).contentWindow.document.getElementById("timedTestAttemptID").value;
    if(timedTestComplete != 1 && timedTestAttemptId != "" && !timedTestClosed && !timedTestPrompt){
        var qno=($('#qno').val()==""?0:$('#qno').val());
        var timedTestCode=$("#timedTestForPM").val();$("#timedTestForPM").remove();
        params="mode=submitAnswer&quesCategory=practiseModule&timedTestAttemptId="+timedTestAttemptId+"&timedTestCode="+timedTestCode+'&qno='+qno;
        getNextQues(params, "normal");
        refreshProgessBar = 1;timedTestPrompt=true;
        setTimeout(CheckTimedTestStatus, 100);
    }
    else if(timedTestComplete != 1 && timedTestAttemptId != "" && timedTestClosed){
        jQuery.fancybox.close();timedTestPrompt=false;
        fetchNextQues();
    }else{
        setTimeout(CheckTimedTestStatus, 100);
    }
}
function startTimedTestTimer(duration, display, timedTestCode) {
    var timer = duration, minutes, seconds;
    timedPromptTimer = setInterval(function () {
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        seconds = seconds < 10 ?  seconds : seconds;

        display.text( seconds);

        //update table every 10 secounds.
        
        if (--timer < 0 && timedTestPrompt) {
            clearInterval(timedPromptTimer);
            $("#prmptContainer_ttprompt").remove();
            if (timedTestPrompt)$(".timedTestForDd").trigger("click");
            timedTestPrompt=false;
            tryingToUnloadPage=false;
        }
    }, 1000);
}
function getWindowOrigin(url) {
    var dummy = document.createElement('a');
    dummy.href = url;
    return dummy.protocol+'//'+dummy.hostname+(dummy.port ? ':'+dummy.port : '');
}
function toggleFullscreen(element) {
    if(screenState=='fullscreen') {
        if(document.exitFullscreen) {
            document.exitFullscreen();
        } else if(document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if(document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if(document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    } else {
        if(element.requestFullscreen) {
            element.requestFullscreen();
        } else if(element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if(element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if(element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }
}
function applyScreenState(event) {
    screenState = (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) ? 'fullscreen' : 'normal';
    if(screenState=='fullscreen') {
        var scale = Math.min(screen.width/+event.target.width.replace('px', ''), screen.height/+event.target.height.replace('px', ''));
        $(event.target).css('transform', 'scale('+scale+')');
    } else {
        $(event.target).css('transform', '');
    }
    event.target.contentWindow.postMessage(JSON.stringify({
        subject: 'screenState',
        content: {
            type: 'confirmation',
            state: screenState,
        },
    }), getWindowOrigin(event.target.src));
}
function evaluateSparkieLogicText() {
    var questionCategory = $('#quesCategory').val();
    switch(questionCategory) {
        case 'normal': {
            if(!sparkieTooltip.text['normal']) {
                var topicAttempt = {
                    number: $('#topicAttemptNumber').val(),
                };
                topicAttempt.ordinal = convertToOrdinal(topicAttempt.number);
                var sparkiesEarned;
                if(topicAttempt.number>20) {
                    sparkiesEarned = 'You will not earn sparkies';
                } else {
                    if(topicAttempt.number>=1 && topicAttempt.number<=2) {
                        topicAttempt.consecutiveCorrectAnswers = 3;
                    } else if(topicAttempt.number>=3 && topicAttempt.number<=5) {
                        topicAttempt.consecutiveCorrectAnswers = 4;
                    } else if(topicAttempt.number>=6 && topicAttempt.number<=9) {
                        topicAttempt.consecutiveCorrectAnswers = 5;
                    } else if(topicAttempt.number>=10 && topicAttempt.number<=20) {
                        topicAttempt.consecutiveCorrectAnswers = 10;
                    }
                    sparkiesEarned = 'You will earn 1 sparkie for '+topicAttempt.consecutiveCorrectAnswers+' questions correct in a row';
                }
                sparkieTooltip.text['normal'] = 'This is your '+topicAttempt.ordinal+' attempt on this topic. '+sparkiesEarned;
            }
            break;
        }
        case 'challenge': {
            var challengeQuestion = {
                attemptNumber: convertToOrdinal($('#showAnswer').val()==0 ? 1 : 2),
                sparkies: $('#showAnswer').val()==0 ? 5 : 2,
            };
            break;
        }
		case 'topicRevision': {
            if(topicRevisionAttemptNo>5)
				sparkieTooltip.text['topicRevision'] = "This is your '"+topicRevisionAttemptNo+"th' attempt. You get sparkies for only first 5 attempts.";
			else
				sparkieTooltip.text['topicRevision'] = "You can get a maximum of 5 sparkies if you do well.";
            break;
        }
    }
    return (sparkieTooltip.text[questionCategory] || '').replace(/__(.+?)__/g, function(match, group1) {
        return eval(group1);
    });
}
function showSparkieTooltip() {
    if(!$('#sparkieTooltip').html() || $('#sparkieTooltip').is(':visible'))
        return;
    $('#sparkieTooltip').css({
        left: $('.bubble').offset().left+70+'px',
        top: $('.bubble').offset().top+18+'px',
    }).show();
    $(document).on('click', hideSparkieTooltip);
    sparkieTooltip.timer = setTimeout(hideSparkieTooltip, 5000);
}
function hideSparkieTooltip() {
    clearTimeout(sparkieTooltip.timer);
    $(document).off('click', hideSparkieTooltip);
    $('#sparkieTooltip').hide().css({
        left: '',
        top: '',
    });
}
function convertToOrdinal(number) {
    var cardinal = Math.abs(number)%100, suffixes = ['th', 'st', 'nd', 'rd'];
    return number+(suffixes[cardinal>=10 && cardinal<=19 ? 0 : cardinal%10] || suffixes[0]);
}
function startConstructionTrail() {
    constrTool.trailCycle = setInterval(function() {
        var toolFrame = $('#q2 iframe.constructionTool')[0];
        toolFrame.contentWindow.postMessage(JSON.stringify({
            subject: 'trail',
            content: {
                type: 'store',
            },
        }), getWindowOrigin(toolFrame.src));
    }, 1000);
}
function diagnosticTestPromptPromptfn(alertText,label) {
    window.setTimeout(function () {
        if(!$(".promptContainer").is(":visible"))
        {
            var prompts = new Prompt({
                text: alertText,
                type: 'alert',
                label1 : label,
                promptId: 'diagnosticTestPrompt',
                func1: function() {
                    jQuery("#prmptContainer_diagnosticTestPrompt").remove();
                },
            });
            $('#promptBox').attr('style', 'width:550px !important');
            $("#promptBox").css({"margin-left":"29%","margin-top":"175px"});
            $("#promptText").css({"text-align":"left"});
        }
    },500);
}
function viewCMPrompt(alertText,testType,lastQcode)
{
    if(!$(".promptContainer").is(":visible"))
    {
        if(testType == "Prerequisite")
            var label = 'Continue';
        else
            var label = 'Continue learning the topic';

             var prompts = new Prompt({
                text: alertText,
                type: 'alert',
                label1 : label,
                func1: function() {
                    jQuery("#prmptContainer_comprehensivePrompt").remove();
                    if(lastQcode == 'done'){
                        setTryingToUnload();
                        document.getElementById('kstdiagnosticTest').submit();
                    } else {
                        redirectDiagnostic(lastQcode);
                    }
                },
                promptId: 'comprehensivePrompt'
            });
        $("#promptBox").css({"width":"550px","margin-left":"29%","margin-top":"175px"});
        $("#promptText").css({"text-align":"left"});
    }          
}
