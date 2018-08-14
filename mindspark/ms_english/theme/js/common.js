/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*  jQuery dialog box for Error & Success message display   */
function showAlert(message,title)
{
    if(title == undefined)
        title = 'Message';
    $('#alert_title').html(title);
    $('#alert_msg').html(message);
    $('#showalert').click();
}

/*  jquery popup window with external url   */
function showPopupup(url, divId, title)
{
    $('.modal-title').html(title);
    $('#'+divId).load(url, function(){
//        $('#'+divId).dialog({
//            title: title,
//            modal:true,
//            width:'auto',
//            height:'auto',
//            position:['center', 'center']
//        });
    });
}

/*  jQuery form submit via ajax, load response in popup div */
function submitAjaxForm(url, postData, containerDiv)
{
    $.post(url, postData, function(response){
       $('#'+containerDiv).html(response);
    });
}

/*
 * action can be fadeIn or slideDown
 */
function animateAppendTo(sourcediv, destinationdiv, id, action, speed) 
{
    if(speed == undefined)
        speed = 1000;
    
    var source = $('#'+sourcediv);
    var clonehtml = source.clone(true);
    
    clonehtml.find('#question_no').html(id);
    clonehtml.find('.questiontypenew').attr('class', clonehtml.find('.questiontypenew').attr('class')+' questiontype');
    clonehtml.find('.questiontypenew').attr('id', id);
    clonehtml.find('#question_new').attr('id', 'question_' + id);
    clonehtml.find('#question_addmore_new').attr('id', 'question_addmore_' + id);
    
    
    /*
     * Change Question ID as per Add Question requested
     */
    
        clonehtml.find('#question_ed').attr('id', 'question_ed_' + id);
    
//        var newQuesEditorName = clonehtml.find('#question_ed_' + id).attr('id');
//    
//        clonehtml.find('#question_ed_' + id).attr('name',newQuesEditorName);
        
//            var editor = CKEDITOR.instances[newQuesEditorName];
//            if (editor) { editor.destroy(true); }
//            CKEDITOR.replace(newQuesEditorName);
//            
//        clonehtml.find('#question_ed_' + id).attr('name','question');
    
    
    /*  Set Question Section Id for Children Reference  */
    
    clonehtml.find('#question_id').attr('id', 'question_id_' + id);
    
    /*  Set Question Save Button to pass associated data    */
    
    clonehtml.find('#save_question_btn').attr('id', 'save_question_btn_' + id);
    
    /*  Set Quiz Question Id for DB reference   */
    
    var newEle = clonehtml.appendTo($('#'+destinationdiv));
    $('#'+destinationdiv).find('#addquestion').attr('id', '');
    
    /*
     * Change Multiple Choices Question Answer's Option Name and Ids
     */
    if(sourcediv == "radiobtn") 
    {
        var multichoicecnt = $('#question_id_'+id+' #multiplechoice').children().length;
        if($('#question_id_'+id+' #multiplechoice').children().length > 1)
        {
            $('#question_id_'+id+' #multiplechoice #multiplechoice_rd').attr('id', 'multiplechoice_rd_'+multichoicecnt);
            
            $('#question_id_'+id+' #multiplechoice #answer_options').attr('id', 'answer_options_'+multichoicecnt);
            
            $('#question_id_'+id+' #multiplechoice #multiplechoice_ans').val(multichoicecnt);
            $('#question_id_'+id+' #multiplechoice #multiplechoice_ans').attr('id', 'multiplechoice_ans_'+multichoicecnt);
            
            $('#question_id_'+id+' #multiplechoice #multiplechoice_ans_count').val(multichoicecnt);
        }
    }
    
    /*
     * Change Check Boxes Question Answer's Option Name and Ids
     */
    if(sourcediv == "checkbox") 
    {
        var checkboxcnt = $('#question_id_'+id+' #checkboxes').children().length;
        if($('#question_id_'+id+' #checkboxes').children().length > 1)
        {
            $('#question_id_'+id+' #checkboxes #checkbox_id').attr('id', 'checkbox_id_'+checkboxcnt);
            
            $('#question_id_'+id+' #checkboxes #answer_options').attr('id', 'answer_options_'+checkboxcnt);
            
            $('#question_id_'+id+' #checkboxes #answer_points').attr('id', 'answer_points_'+checkboxcnt);
            
            $('#question_id_'+id+' #checkboxes #checkboxes_ans').val(checkboxcnt);
            $('#question_id_'+id+' #checkboxes #checkboxes_ans').attr('id', 'checkboxes_ans_'+checkboxcnt);
            
            $('#question_id_'+id+' #checkboxes #checkboxes_count').val(checkboxcnt);
        }
    }
    
    /*
     * Change Multi Questions Answer's Option Name and Ids
     */
    if(sourcediv == "multiques") 
    {
        var multiquescnt = $('#question_id_'+id+' #multiquestions').children().length;
        
        if(multiquescnt > 1)
        {
            $('#question_id_'+id+' #multiquestions #multiques_id').val(surveycnt);
            
            $('#question_id_'+id+' #multiquestions #multiques_id').attr('id', 'multiques_id_'+multiquescnt);
            
            $('#question_id_'+id+' #multiquestions #answer_options').attr('id', 'answer_options_'+multiquescnt);
            
            $('#question_id_'+id+' #multiquestions #answer_points').attr('id', 'answer_points_'+multiquescnt);
            
            $('#question_id_'+id+' #multiquestions #correct_answer').attr('id', 'correct_answer_'+multiquescnt);
            
            $('#question_id_'+id+' #multiquestions #multiques_count').val(multiquescnt);
            
        }
    }
    
    
    /*
     * Change Survey Question Answer's Option Name and Ids
     */
    if(sourcediv == "survey") 
    {
        var surveycnt = $('#question_id_'+id+' #surveys').children().length;
        if($('#question_id_'+id+' #surveys').children().length > 1)
        {
            $('#question_id_'+id+' #surveys #survey_ans_label').html('Option '+surveycnt);

            $('#question_id_'+id+' #surveys #survey_ans_label').attr('id', 'survey_ans_label_'+surveycnt);
            
            $('#question_id_'+id+' #surveys #answer_options').attr('id', 'answer_options_'+surveycnt);
            
            $('#question_id_'+id+' #surveys #survey_ans_id').val(surveycnt);
            
            $('#question_id_'+id+' #surveys #survey_ans_id').attr('id', 'survey_ans_id_'+surveycnt);
            
            $('#question_id_'+id+' #surveys #survey_ans_count').val(surveycnt);
        }
    }
    
    /*
     * Change Matches Question Answer's Option Name and Ids
     */
    if(sourcediv == "match") 
    {
        var matchescnt = $('#question_id_'+id+' #matches').children().length;
        if($('#question_id_'+id+' #matches').children().length > 1)
        {
            $('#question_id_'+id+' #matches #answer_options').attr('id', 'answer_options_'+matchescnt);
            
            $('#question_id_'+id+' #matches #matches_answer_options').attr('id', 'matches_answer_options_'+matchescnt);
            
            $('#question_id_'+id+' #matches #matches_ans_id').val(matchescnt);
            
            $('#question_id_'+id+' #matches #matches_ans_id').attr('id', 'matches_ans_id_'+matchescnt);
            
            $('#question_id_'+id+' #matches #matches_ans_count').val(matchescnt);
        }
    }
    
    if(sourcediv == "textarea") 
    {
        var newtextareaname = $('#question_id_'+id+' #correct_answer').attr('id')+'_'+id;
        $('#question_id_'+id+' #correct_answer').attr('name',newtextareaname);
        
            var editor = CKEDITOR.instances[newtextareaname];
//            if (editor) { editor.destroy(true); }
            CKEDITOR.replace(newtextareaname);
            
        $('#question_id_'+id+' #correct_answer').attr('name',$('#question_id_'+id+' #correct_answer').attr('id'));
    }
    
    $('#question_id_'+id+' #resultMsg').attr('id','result_'+id);
    $('#question_id_'+id+' #resulterrMsg').attr('id','resulterr_'+id);
    
    newEle.hide();
    newEle[action]( speed, function() {
//        newEle.find(':last-child').focus();
//        newEle.closest('.sortable').find('.questiontype').focus();
    });
//    var newPos = newEle.position();
//    newEle.animate(newPos, speed, function() {
//        newEle.show();
//    });
};

/*
 * Radio Option Removal Logic and Re-Assignment of Ids
 */

function removeRadioElem(containerDivObj, eleObj) 
{
    if(eleObj != undefined && containerDivObj != undefined)
    {
        var cbAnsKey; 
        var nextCBAnsKey; 
        var removedElemId; 
        
        var totalElemCnt = $(containerDivObj).find('#multiplechoice_ans_count').val() - 1;
        $(containerDivObj).find('#multiplechoice_ans_count').val(totalElemCnt);
        
        removedElemId = $(eleObj).find('.radioBlock input[name="multiplechoice_ans"]').val();
        
        $(eleObj).closest('#radiobtn').remove();
        
        $.each( $(containerDivObj).find('.radioBlock'), function( key, value ) {
            cbAnsKey = key + 1;
            nextCBAnsKey = cbAnsKey + 1;
            
            if($(containerDivObj).find('.radioBlock input[id="answer_options_'+cbAnsKey+'"]').attr("id") == undefined)
            {
                $(containerDivObj).find('.radioBlock input[id="multiplechoice_rd_'+nextCBAnsKey+'"]').attr("id","multiplechoice_rd_"+(cbAnsKey));
                $(containerDivObj).find('.radioBlock input[id="answer_options_'+nextCBAnsKey+'"]').attr("id","answer_options_"+(cbAnsKey));
                $(containerDivObj).find('.radioBlock input[id="multiplechoice_ans_'+nextCBAnsKey+'"]').val(cbAnsKey);
                $(containerDivObj).find('.radioBlock input[id="multiplechoice_ans_'+nextCBAnsKey+'"]').attr("id","multiplechoice_ans_"+(cbAnsKey));
            }
        });

    }
}

/*
 * Chkbox Option Removal Logic and Re-Assignment of Ids
 */

function removeChkboxElem(containerDivObj, eleObj) 
{
    if(eleObj != undefined && containerDivObj != undefined)
    {
        var cbAnsKey; 
        var nextCBAnsKey; 
        var removedElemId; 
        
        var totalElemCnt = $(containerDivObj).find('#checkboxes_count').val() - 1;
        $(containerDivObj).find('#checkboxes_count').val(totalElemCnt);
        
        removedElemId = $(eleObj).find('.checkboxBlock input[name="checkboxes_ans[]"]').val();
        
        $(eleObj).closest('#checkbox').remove();
        
        $.each( $(containerDivObj).find('.checkboxBlock'), function( key, value ) {
            cbAnsKey = key + 1;
            nextCBAnsKey = cbAnsKey + 1;
            
            if($(containerDivObj).find('.checkboxBlock input[id="answer_options_'+cbAnsKey+'"]').attr("id") == undefined)
            {
                $(containerDivObj).find('.checkboxBlock input[id="checkbox_id_'+nextCBAnsKey+'"]').attr("id","checkbox_id_"+(cbAnsKey));
                $(containerDivObj).find('.checkboxBlock input[id="answer_options_'+nextCBAnsKey+'"]').attr("id","answer_options_"+(cbAnsKey));
                $(containerDivObj).find('.checkboxBlock input[id="answer_points_'+nextCBAnsKey+'"]').attr("id","answer_points_"+(cbAnsKey));
                $(containerDivObj).find('.checkboxBlock input[id="checkboxes_ans_'+nextCBAnsKey+'"]').val(cbAnsKey);
                $(containerDivObj).find('.checkboxBlock input[id="checkboxes_ans_'+nextCBAnsKey+'"]').attr("id","checkboxes_ans_"+(cbAnsKey));
            }
        });
        
    }
}

/*
 * Multi Questions Option Removal Logic and Re-Assignment of Ids
 */

function removeMultiQuesElem(containerDivObj, eleObj) 
{
    if(eleObj != undefined && containerDivObj != undefined)
    {
        var cbAnsKey; 
        var nextCBAnsKey; 
        var removedElemId; 
        
        var totalElemCnt = $(containerDivObj).find('#multiques_count').val() - 1;
        $(containerDivObj).find('#multiques_count').val(totalElemCnt);
        
        removedElemId = $(eleObj).find('.multiQuesBlock input[name="multiques_id"]').val();
        
        $(eleObj).closest('#multiques').remove();
        
        $.each( $(containerDivObj).find('.multiQuesBlock'), function( key, value ) {
            cbAnsKey = key + 1;
            nextCBAnsKey = cbAnsKey + 1;
            
            if($(containerDivObj).find('.multiQuesBlock input[id="answer_options_'+cbAnsKey+'"]').attr("id") == undefined)
            {
                $(containerDivObj).find('.multiQuesBlock input[id="answer_options_'+nextCBAnsKey+'"]').attr("id","answer_options_"+(cbAnsKey));
                $(containerDivObj).find('.multiQuesBlock input[id="correct_answer_'+nextCBAnsKey+'"]').attr("id","correct_answer_"+(cbAnsKey));
                $(containerDivObj).find('.multiQuesBlock input[id="answer_points_'+nextCBAnsKey+'"]').attr("id","answer_points_"+(cbAnsKey));
                $(containerDivObj).find('.multiQuesBlock input[id="multiques_id_'+nextCBAnsKey+'"]').val(cbAnsKey);
                $(containerDivObj).find('.multiQuesBlock input[id="multiques_id_'+nextCBAnsKey+'"]').attr("id","multiques_id_"+(cbAnsKey));
            }
        });
        
    }
}

/*
 * Matches Option Removal Logic and Re-Assignment of Ids
 */

function removeMatchesElem(containerDivObj, eleObj) 
{
    if(eleObj != undefined && containerDivObj != undefined)
    {
        var cbAnsKey; 
        var nextCBAnsKey; 
        var removedElemId; 
        
        
        var totalElemCnt = $(containerDivObj).find('#matches_ans_count').val() - 1;
        $(containerDivObj).find('#matches_ans_count').val(totalElemCnt);
        
        removedElemId = $(eleObj).find('.matchBlock input[name="matches_ans_id"]').val();
        
        $(eleObj).closest('#match').remove();
        
        $.each( $(containerDivObj).find('.matchBlock'), function( key, value ) {
            cbAnsKey = key + 1;
            nextCBAnsKey = cbAnsKey + 1;
            
            if($(containerDivObj).find('.matchBlock input[id="answer_options_'+cbAnsKey+'"]').attr("id") == undefined)
            {
                $(containerDivObj).find('.matchBlock input[id="answer_options_'+nextCBAnsKey+'"]').attr("id","answer_options_"+(cbAnsKey));
                $(containerDivObj).find('.matchBlock input[id="matches_answer_options_'+nextCBAnsKey+'"]').attr("id","matches_answer_options_"+(cbAnsKey));
                $(containerDivObj).find('.matchBlock input[id="matches_ans_id_'+nextCBAnsKey+'"]').val(cbAnsKey);
                $(containerDivObj).find('.matchBlock input[id="matches_ans_id_'+nextCBAnsKey+'"]').attr("id","matches_ans_id_"+(cbAnsKey));
            }
        });

    }
}


/*
 * Survey Option Removal Logic and Re-Assignment of Ids
 */

function removeSurveyElem(containerDivObj, eleObj) 
{
    if(eleObj != undefined && containerDivObj != undefined)
    {
        var cbAnsKey; 
        var nextCBAnsKey; 
        var removedElemId; 
        
        var totalElemCnt = $(eleObj).find('#survey_ans_count').val() - 1;
        $(containerDivObj).find('#survey_ans_count').val(totalElemCnt);
        
        removedElemId = $(eleObj).find('.surveyBlock input[name="survey_ans_id"]').val();
        
        $(eleObj).closest('#survey').remove();
        
        $.each( $(containerDivObj).find('.surveyBlock'), function( key, value ) {
            cbAnsKey = key + 1;
            nextCBAnsKey = cbAnsKey + 1;
            
            if($(containerDivObj).find('.surveyBlock input[id="answer_options_'+cbAnsKey+'"]').attr("id") == undefined)
            {
                $(containerDivObj).find('.surveyBlock #survey_ans_label_'+nextCBAnsKey).html('Option '+cbAnsKey);
                $(containerDivObj).find('.surveyBlock #survey_ans_label_'+nextCBAnsKey).attr("id","survey_ans_label_"+(cbAnsKey));
                $(containerDivObj).find('.surveyBlock input[id="answer_options_'+nextCBAnsKey+'"]').attr("id","answer_options_"+(cbAnsKey));
                $(containerDivObj).find('.surveyBlock input[id="survey_ans_id_'+nextCBAnsKey+'"]').val(cbAnsKey);
                $(containerDivObj).find('.surveyBlock input[id="survey_ans_id_'+nextCBAnsKey+'"]').attr("id","survey_ans_id_"+(cbAnsKey));
            }
        });

    }
}

function chkForNumeric(containerId, fieldName, errMsg)
{
    if(errMsg == undefined)
        errMsg = 'Invalid Data';
    
    var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
    
    if(!numericReg.test($('#'+containerId+' #'+fieldName).val())) {
        $('#'+containerId+' #'+fieldName).val('');
        showAlert(errMsg,'Invalid Data');
    }
}
    
function checkAllGrades() {
    if($('#all_grades').is(':checked')) {        
        $('#prepared_for_grade input[name="prepared_for_grade[]"]').attr('checked',true);
    } else {
        $('#prepared_for_grade input[name="prepared_for_grade[]"]').attr('checked',false);
    }
}
