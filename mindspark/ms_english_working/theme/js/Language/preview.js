/**
 * @author eicpu92
 */

/*  PREVIEW PAGE - EXACTLY the same page as the main page. CODE FOR EPIC WIN.
    Loads only when mode is preview.
 * */

function preparePreview() {
    $('.none').hide();
    $('.the_classroom').show();
    sidebarToggle(false);
    stopAndHideOtherActivities();
    
    tryingToUnloadPage = true;
    
    var parameters = Helpers.getUrlParameters();
    var array = parameters['id'].split(',');

    var beforeSetQuestion = function(){
        if($(this).attr('style'))
            return;
            
        $(this).siblings().removeAttr('style');
        $(this).css('background-color', '#FFcc66');
        $('#questionSubmitButton').hide();
        question.qID = this.innerHTML;
        setQuestion(question);
    };
    
    var question = {
        qID: array[0],
        qType: parameters['type'],
        info: {
            'passageType': parameters['passageType']
        },
    };
    
    if(array.length > 1){
        links = document.createElement('div');
        $(links).css({
            'background-color': 'white',
            'display': 'inline-block',
        });
        $(links).insertBefore('.the_classroom');
        for(i = 0 ; i < array.length; i++ ){
            var link = Helpers.createElement('button',{
                click: beforeSetQuestion,
                html: array[i]
            });
            $(links).append(link);
        }
        $($(links).children()[0]).css('background-color', '#FFcc66');
    }
    
    setQuestion(question);
}