/*COMMENT SYSTEM START*/
englishInterface.controller('commentSystemController', ['$scope', '$http', '$rootScope',
function($scope, $http, $rootScope) {

    $scope.hideSubCat    = true;
    $scope.catBtnClicked = false;
    $scope.subCat = '';
    $scope.promptShown = false;

    /*GET CATEORIES*/
    
    $http.get(Helpers.constants['CONTROLLER_PATH'] + 'commentSystem/getCategories').success(function(data) {
            $scope.categories = data.categoriesArr;
            $scope.subCat     = data.subCat;
    });

    /*GET SUB CATEGORIES*/
    $scope.getSubCategories = function(categoryID, categoryName)
    {
        $scope.categoryID   = categoryID;

        $scope.categoryName = categoryName.toLowerCase();

        
        if($(".ui-dialog").is(":visible")){
            Helpers.close_prompt();
        }
        /*if($scope.promptShown)
            Helpers.close_prompt();*/

        if($scope.categoryName == 'doubt')
            $('#commentSubCatID').val('');

        $(".subCatBtns").removeClass('selected-btn-comment');
        $(".catBtns").css('background', 'white');
        $(".catBtns").css('color', 'black');
        $(".arrow").removeClass('arrow_box');

        $("#cat_"+categoryID).css('background' ,'#3a2816');
        $("#cat_"+categoryID).css('color' ,'white');
        $("#div_"+categoryID).addClass('arrow_box');

        /*$http.get(Helpers.constants['CONTROLLER_PATH'] + 'commentSystem/getSubCategories/' + $scope.categoryID +'/').success(function(data) {*/
            //$scope.subCategories = data;

            $scope.subCategories = $scope.subCat[categoryName];

            if($scope.subCategories == '' || $scope.subCategories == undefined)
            {
                $scope.hideSubCat = true;
                $scope.subcatbtn  = false;
            }
            else
            {
                $scope.hideSubCat       = false;
                $scope.subcatbtn        = true;
                $scope.subCatBtnClicked = false;
            }
            
            $scope.catBtnClicked = true;
        //});
    }

    $scope.subCatBtn = function(subCategoryID, subCatName)
    {
        $(".subCatBtns").removeClass('selected-btn-comment');
        $("#sub_cat_"+subCategoryID).addClass('selected-btn-comment');

        $scope.subCategoryID = subCategoryID;
        $scope.subCatName    = subCatName.toLowerCase();

        //if($scope.subcatbtn != undefined && $scope.subcatbtn != false)
        if($scope.subcatbtn != false)
            $scope.subCatBtnClicked = true;
        else
            $scope.subCatBtnClicked = false;
    }

    $scope.closeCommentPanel = function()
    {
        $scope.hideSubCat       = true;
        $scope.catBtnClicked    = false;
        $scope.subcatbtn        = false;
        $scope.subCatBtnClicked = false;

        
        $("#modalBlockerCommentSystem").hide();

        if($scope.promptShown)
            Helpers.close_prompt();

        sessionData.currentLocation.location = '';
    }

}]);    

function showCommentPanel() {

    $(".newEssay").removeClass('active');
    $(".essaySummay").removeClass('active');
    //stopAndHideOtherActivities();
    var scope = angular.element(document.getElementById("commentPanel")).scope();
    sessionData.classRoomStarted=true;
    sessionData.currentLocation.location = 'comment_system';

    scope.promptShown = false;
    scope.catBtnClicked = false;
    scope.subcatbtn     = false;
    scope.hideSubCat    = true;

    hidePluginIfAny();
    //sessionData.seeingInstructions = true;
    //$('#modalBlocker').show();
    $("#modalBlockerCommentSystem").show();
    $('#commentPanel').show();
    $('#comment').focus(250);
    $('#comment')[0].value = '';

    $(".catBtns").css('background', 'white');
    $(".catBtns").css('color', 'black');
    $(".arrow").removeClass('arrow_box');
    //$("#cmtNoti").hide();
}

function submitCommentCheck() {

    var scope = angular.element(document.getElementById("commentPanel")).scope();
    scope.promptShown = false;
   
    //if(scope.catBtnClicked == undefined || scope.catBtnClicked == false)
    if(scope.catBtnClicked == false)
    {
        Helpers.prompt('Please select an option');
        scope.promptShown = true;
    }
    else if(scope.catBtnClicked == true && scope.subCatBtnClicked == undefined)
    {
        if(scope.subcatbtn == true)
        {
            scope.promptShown = true;
            Helpers.prompt('Please select a sub-option');
        }
        else
        {
            //submit comment here
            submitComment();
            scope.promptShown = false;
        }
    }
    else if(scope.subCatBtnClicked == false && scope.subcatbtn == true)
    {
        scope.promptShown = true;
        Helpers.prompt('Please select a sub-option');
    }
    else
    {
        scope.promptShown = false;
        //submit comment here
        submitComment();

    }
    
}

function submitComment()
{
    var scope = angular.element(document.getElementById("commentPanel")).scope();

    if(scope.subCategoryID != undefined && scope.subCategoryID != '' && scope.categoryName != 'doubt')
        var subcatID = scope.subCategoryID;
    else
        var subcatID = 0;

    if(scope.categoryID != undefined && scope.categoryID != '')
        var catID = scope.categoryID;
    else
        var catID = 0;

    var type = '';
    sessionData.currentLocation.value = 0;

    switch(sessionData.currentLocation.type) {
        case 'passage':
            type = 'passage';
            sessionData.currentLocation.value = currentQuestion.qID;
            break;
        case 'freeQues':
        case 'passageQues':
            type = 'question';
            sessionData.currentLocation.value = currentQuestion.qID;
            break;
        case 'home':
            type = 'home';
            sessionData.currentLocation.value = 0;
            break;
        case 'profile_details':
            type = 'misc';
            sessionData.currentLocation.value = 0;
            break;
        case 'activity':
            type = 'grounds';
            sessionData.currentLocation.value = 0;
            break;
        case 'singleGame':
            type = 'grounds';
            if(currentQuestion.qID != undefined)
                sessionData.currentLocation.value = currentQuestion.qID;
            break;
        case 'essayWriter':
                type = 'essayWriter';
                sessionData.currentLocation.value = 0;
            break;
        case 'essay':
            type = 'essayWriter';
            if(currentQuestion.qID != undefined)
                sessionData.currentLocation.value = currentQuestion.qID;
            break;
        case 'dictionary':
            type = 'dictionary';
            sessionData.currentLocation.value = 0;
            break;
        case 'reports':
            type = 'reports';
            sessionData.currentLocation.value = 0;
            break;
        case 'myStudents':
            type = 'myStudents';
            sessionData.currentLocation.value = 0;
            break;
        case 'essayReview':
            type = 'essayReview';
            if(currentQuestion.qID != undefined)
                sessionData.currentLocation.value = currentQuestion.qID;
            else
                sessionData.currentLocation.value = 0;
            break;
        case 'misc':
            type = 'misc';
            sessionData.currentLocation.value = 0;
            break;
        default:
            type = 'misc';
            sessionData.currentLocation.value = 0;
            return;
            break;

    }

    if((scope.categoryName == 'doubt' || scope.subCatName == 'suggestion') && Helpers.isBlank($('#comment')[0].value))
    {
        Helpers.prompt('Please enter your comment.');
        scope.promptShown = true;
    }
    else
    {
        var commentToPost = {
            'comment' : $('#comment')[0].value
        };

        $.ajax({
            type : "POST",
            url : Helpers.constants['CONTROLLER_PATH'] + "commentSystem/checkProfanity",
            data : commentToPost
        }).done(function(data) {
            if(data=="true"){
                Helpers.prompt('You seem to have used inappropriate language in your response. Please edit it, or write to us if there is no inappropriate language and you are still getting this message');
                return;
            }
            else
            {
                
                    scope.promptShown = false;
                    $("#modalBlockerCommentSystem").hide();
                    $('#commentPanel').hide();
                    scope.catBtnClicked    = false;
                    scope.subcatbtn        = false;
                    scope.subCatBtnClicked = false;
                    scope.hideSubCat       = true;
                    //hideHovers();
                    var arrayToPost = {
                        //'userID' : sessionData.userID,
                        'itemID' : sessionData.currentLocation.value,
                        'page' : type,
                        'status' : 'pendingToBeResolved',
                        'commentCategoryID': catID,
                        'commentSubCategoryID': subcatID,
                        'comment' : $('#comment')[0].value,
                    };

                    $.ajax({
                        type : "POST",
                        url : Helpers.constants['CONTROLLER_PATH'] + "commentSystem/insertUserComments",
                        data : arrayToPost
                    }).done(function(data) {
                        //Helpers.prompt('Your comment has been submitted.');
                        Helpers.ajax_response( '' , data, []);
                    });
            }
            //Helpers.ajax_response( '' , data, []);
        });
       
    }
}


englishInterface.controller('commentSystemListController', ['$scope', '$sce','$http', '$rootScope',
function($scope, $sce, $http, $rootScope) {

    $scope.showComtList = function()
    {
        $(".moduleContainer").hide();
        $("#listView").show();
        $('#commentList').show();
        $('#commentList > .none').show();

        $('#viewComment').hide();
        $('#viewComment > .none').hide();
        
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'commentSystem/getUserComments/').success(function(commentData) {
            $scope.userComments = commentData;
            if($scope.userComments.length === 0 || $scope.userComments.length === undefined)
                $scope.showHeader = false;
            else
                $scope.showHeader = true;
        }); 
    }

    $scope.showComtDetails = function(commentID)
    {
        $("#replyToMs").val('');
        $(".moduleContainer").hide();
        $("#listView").show();
        $('#commentList').hide();
        $('#commentList > .none').hide();

        $('#viewComment').show();
        $('#viewComment > .none').show();


        $scope.commentID = commentID
        $http.get(Helpers.constants['CONTROLLER_PATH'] + 'commentSystem/fetchCommentDetails/'+$scope.commentID+'/').success(function(commentDetails) {


            setNotification();

            $scope.showReply            = commentDetails.showReply;

            if(commentDetails.content != undefined)
                $scope.content              = commentDetails.content.replace(/&nbsp;/ig,' ');
            else
                $scope.content              = commentDetails.content;

            $scope.contentName          = commentDetails.contentName;

            $scope.showPsg     = false;
            $scope.showQues    = false;
            $scope.showEssay   = false;
            $scope.showIGRE    = false;
            $scope.passageType = commentDetails.passageType;

            if(commentDetails.subCategoryName != '')
                $scope.catContent = commentDetails.categoryName+' > '+commentDetails.subCategoryName;
            else
                $scope.catContent = commentDetails.categoryName;

            if(commentDetails.page == 'passage')
            {
                $scope.heading = 'Passage';
                $scope.showPsg = true;
            }
            else if(commentDetails.page == 'question' && $scope.content != '')
            {
                $scope.heading = 'Question';
                $scope.showQues = true; 
            }
            else if(commentDetails.page == 'essayWriter' && $scope.contentName != '')
            {
                $scope.heading = 'Essay Topic';
                $scope.showEssay = true;
            }
            else if(commentDetails.page == 'grounds' && $scope.contentName != '')
            {
                $scope.heading = 'Activity';
                $scope.showIGRE = true;
            }
            /*else
            {
                if(commentDetails.subCategoryName != '')
                    $scope.catContent = commentDetails.categoryName+' > '+commentDetails.subCategoryName;
                else
                    $scope.catContent = commentDetails.categoryName;

            }*/
            $scope.commentCategoryID    = commentDetails.commentCategoryID;
            $scope.commentSubCategoryID = commentDetails.commentSubCategoryID;
            $scope.itemID               = commentDetails.itemID;
            $scope.userCommentDetails   = commentDetails.result;
        });
    }

    $scope.trustAsHtml = function(string) {
        return $sce.trustAsHtml(string);
    };
    $scope.set_color = function (viewed) {
      if (viewed == 0) {
        return { 
            "background": "#86c387" ,
            "font-weight":"bolder"
        }
      }
    }

    $scope.showContent = function(passageType,itemID)
    {
    
        //if passage is a conversation
        if(passageType == 'Conversation')
        {

            audioObject = new Audio($('#audioContainer')[0]);
            audioObject.view.showLookback(itemID, $('#audioLookback')[0], function() {
                $('#modalBlocker').show();
                $('#audioLookback').show();
            });
        } 
        else
        {
            /*$('#lookbackContentComment').show();
            $('#modalBlocker').show();
            //var html = '<div class="prompt-heading"><button class="close-prompt" prompt-close=\'#lookbackContentComment\'><i class="fa fa-close"></i></button> </div>'+$scope.content;
            $('#lookbackContentComment').html($scope.content);*/


            //$('#lookbackContentComment').show();

            $("#lookbackContent").show();
            $('#modalBlocker').show();
            var lookbackContainer = document.createElement('div');
            lookbackContainer.className = 'lookbackContainer';
            var close_button = '<div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#lookbackContent\'><i class="fa fa-close"></i></button> </div>';
             lookbackContainer.innerHTML =  close_button + $scope.content;
            $('#lookbackContent').html(lookbackContainer);
            $(".lookbackContainer").css("text-align","justify");
        }
        

        //$('#lookbackContentComment').html($scope.content);
    }

    $scope.submitReplyComment = function()
    {

        var replyArrToPost = {
            'status':    'reopen',
            'comment':   $('#replyToMs').val(),
            'commentID': $('#commentID').val(),
            'commentCategoryID': $('#commentCatID').val(),
            'commentSubCategoryID': $('#commentSubCatID').val(),
        };

        if(Helpers.isBlank($('#replyToMs')[0].value))
        {
            Helpers.prompt('Please enter your comment.');
        }
        else
        {
            $.ajax({
                type : "POST",
                url : Helpers.constants['CONTROLLER_PATH'] + "commentSystem/insertUserReplyComments",
                data : replyArrToPost
            }).done(function(data) {
                Helpers.ajax_response( '' , data, []);
                $("#replyToMs").val('');
                $scope.showComtList();
            });
        }
    }

}]);
$( "#commentListShow" ).on('click', function(e) {
    stopSnowFall();
    sessionData.currentLocation.type = 'misc';
   $('.sidebar-nav .active').removeClass('active');
    // Hide plugin 
    $("#modalBlockerCommentSystem").hide()
    $('#commentPanel').hide();
    hidePluginIfAny();
    sidebarToggle(false);
    showComtList();

}); 

function showComtList()
{
    var scope = angular.element(document.getElementById("listView")).scope();
    scope.$apply(function () {
        scope.showComtList();
    });
}
$(document).ready(function(){
  $('#comment').bind("cut copy paste",function(e) {
      e.preventDefault();
  });
    setNotification();

    setInterval(function(){
         setNotification();
    },60000);
});

function setNotification()
{
    $.ajax({
        type : "POST",
        url : Helpers.constants['CONTROLLER_PATH'] + "commentSystem/notificationCount",
    }).done(function(data) {

        Helpers.ajax_response( notification , data, [] );
    });
}

function notification(response , extraParams)
{
    if (!Helpers.isResponseValid(response)) {
        return;
    }
    if(response.commentReplyRcvd > 0)
    {
        $("#cmtNoti").show();
        $("#cmtNoti").text(response.commentReplyRcvd);
    }
    else
        $("#cmtNoti").hide();
}
/*COMMENT SYSTEM END*/