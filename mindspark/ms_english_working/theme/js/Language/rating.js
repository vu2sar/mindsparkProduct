/* Author Rochak */
var skipRating = 1;
function RATING(settings){
    _myself = this;
    currentObject = settings;
    defaults = {
        title : 'Rating (Optional)',
        title_on_select_star : 'Please tell us more!(Optional)',
        star_container : '.rating',
        stars : 5,
        fade_element : '',
        ajax_url : Helpers.constants['CONTROLLER_PATH'] + 'questionspage/saveUserRating',
        theme : 'fa-star',
        tooltips : ['Bad','Not so good','Okay','Good','Excellent'],
        additional_info_star : 2,
        additional_info : ['Boring','Difficult','Easy','Too Long','Too Short'],
        additional_info_element : '#popover',
        parameters : {
            contentID : 0,
            contentType : '',
            rating : 0,
            comment : '',
            ratingReasonOther : ''
            
        },
        callback : {
            callback_function : '',
            callback_parameters : '',
        } 
    };
    rating_done : true;

    this.init = function ()
    {
        // Assign the settings passed with the default settings.
        if(Helpers.object_length(currentObject) > 0)
        {
            $.each(currentObject, function (key, value) {
                defaults[key] = value;
            });
        }

        // Create the no of rating star needed.

        this.set_title(defaults.title);
        this.setup_rating_stars();
        this.setBindings();
        $('.rating-feedback').removeClass('none').show();
        skipRating = 1;
        // Set rating feedback to center of screen
        var left = ($(document).width()/2) - ($('.rating-feedback').width()/2);
        var top = ($(document).height()/2) - ($('.rating-feedback').height()/2); // +25 for the converstion type
        $(".rating-feedback").css({ 'left' : left + 'px', 'top' : top + 'px'});
       

        $(defaults.fade_element).addClass('fading-for-rating');

    };
    // Set the title of the rating plugin.
    this.set_title = function(title)
    {
        if(title != '')
            $(".rating-heading").html(title);
    }
    // Create the star for current plugin.
    this.setup_rating_stars = function()
    {
        $(defaults.star_container).empty();
        var tooltip_text = '';
        for(var i = 0 ; i < defaults.stars ; i++ )
        {
            // This function add a star in the plugin. With the tooltip from the tooltip array
            try
            {
                tooltip_text = defaults.tooltips[i];
            }
            catch(e){
                tooltip_text = '';
            }
            this.add_star(tooltip_text);   
        }
    };
    this.isRatingValid = function()
    {
        if(this.checkStarGiven() == this.checkLowRatingComment())
            return true;
        else
            return false;
    }
    this.save_rating = function(callback_flag){
        if(skipRating == 0)
        {

            // Validate the rating done or not.
            rating_done = this.checkStarGiven();
            rating_done = this.checkLowRatingComment();

            if(rating_done)
            {
                // Add the ajax call to save the rating for the entity
                skipRating = 1;   
                $.ajax({
                    url : defaults.ajax_url,
                    type : 'POST',
                    data : defaults.parameters,
                    success : function(response){
                        _myself.reset_parameters();
                        /*if(callback_flag != 'no_callback')
                        {
                            if( defaults.callback.callback_function != '')
                            {
                                if(defaults.callback.callback_parameters != '')
                                    defaults.callback.callback_function.apply(null, [defaults.callback.callback_parameters]);
                                else
                                    defaults.callback.callback_function.apply(null, '');

                            }
                        }*/

                            if(callback_flag != 'no_callback' && callback_flag !="gre" ) // && callback_flag !="gre" 
                            {    
                               //$(".rightButton").trigger('click');
                            }
                            else if(callback_flag=="gre"){
                                 rating_object.vanish();
                                 rating_object = Helpers.destroy_object();
                                 if(sessionData.currentLocation.location=='the_classroom')
                                       saveQuestion();
                                else
                                    {
                                        activityCloseButtonAction();
                                        Helpers.prompt("Your feedback has been submitted");
                                    }
                            } 
                    }
                        
                });
                _myself.vanish();
                
                        
            }
           
        }



    };
    this.checkStarGiven = function()
    {
        if(defaults.parameters.rating == 0)
            return false;

        return true;
    };
    this.checkLowRatingComment = function()
    {

        if(defaults.parameters.ratingReasonOther == "" && defaults.parameters.rating <= defaults.additional_info_star && defaults.parameters.comment.trim() == '' && defaults.parameters.contentType!='gre')  // change in condition for Content type gre && defaults.parameters.contentType!='gre'
            return false;

        if(defaults.parameters.ratingReasonOther == undefined && defaults.parameters.comment.trim() == '')
            return false;

        return true;
    };

    this.vanish = function()
    {
        $('.rating-feedback').addClass('none').hide();
        $(defaults.fade_element).removeClass('fading-for-rating');
        Helpers.popover({show:'false'});
    }
    this.add_star = function(tooltip_text){
        var star = Helpers.createElement('i', {
            'className' : 'fa '+ defaults.theme + ' fa-2x',
            'attributes' : {
                                'data-toggle' : 'tooltip',
                                'data-title' : tooltip_text
                           }
        });

        // Append a star in the plugin.
        $(defaults.star_container).append(star);
    };
    this.get_additional_info_content = function()
    {
        var additional_content = [];
        var length = defaults.additional_info.length;
        for( var i = 0 ; i < length ; i++ )
        {
            additional_content.push(Helpers.createElement('button',{
                                className : 'btn-primary small-button',
                                html : defaults.additional_info[i],
                          }));
        }
        return additional_content;
    };
    this.get_comment_box = function()
    {
          return Helpers.createElement('textarea',{
                            className : 'form-control',
                            value : '',
                            attributes : {
                                rows : 3,
                                //placeholder : 'Type in your comments.\nPress enter to save and proceed'
                                placeholder : 'Type your comments here. Press Enter (or click on Submit) to proceed.'
                        }
                  });
    };
    this.get_comments_button = function()
    {
          return Helpers.createElement('button',{
                            id : 'save_rating_button',
                            className : 'btn-primary small-button',
                            html : 'Submit',
                  });
    };
    this.add_additional_info = function(current_index)
    {
        var additional_content = this.get_additional_info_content();
        $(defaults.additional_info_element).html('');
        if(current_index < defaults.additional_info_star)
        {
            $.each(additional_content,function(){
                $(defaults.additional_info_element).append($(this));
            });
        }
        $(defaults.additional_info_element).append(this.get_comment_box());
        $(defaults.additional_info_element).append(this.get_comments_button());
        defaults.parameters.ratingReasonOther = '';
        defaults.parameters.comment = ''; 

        $("#save_rating_button").off('click').on('click',function(){
            if(!rating_object.isRatingValid())
            {
                Helpers.prompt('Please select or type in the reason.');
                return;
            }
           //rating_object.save_rating(currentQuestion.qType);
           rating_object.save_rating(defaults.parameters.contentType);
           if ($("#passageContainer").css('display') == 'block')
           {
                $('.navigationButton.rightButton').trigger('click');
           } 
           if ($('#passageNext').is(':visible')) {
               $('#passageNext').trigger('click');
               return;
           }
           // rating_object.save_rating();
        });
    };
    /*
     * creates div that displays value of the node.
     */
    
    this.setBindings = function(){
        // Skip rating
        $(".rating_cross").off('click').on('click',function(){
            if(defaults.parameters.contentType=='gre'){            // change here current qType to content type 
                rating_done = _myself.checkStarGiven();
              if(rating_done)
                {
                    _myself.save_rating('gre');
                }                
              else
                {
                    rating_object.vanish();
                    rating_object = Helpers.destroy_object();
                    if(sessionData.currentLocation.location=='the_classroom')   // my code
                        saveQuestion();
                    else
                        activityCloseButtonAction();
                }
            }
          else if(currentQuestion.info.passageType === "Conversation")
            {
                $("#passageNext").trigger('click');
            }
            else
            {
                $('.navigationButton.rightButton').trigger('click');
            }
        });
        // Hover on star
        $('.fa-star').hover(function(){
            $('.fa-star').removeClass('fa-star-hover');
            var current_index = $(this).index();
            $('.fa-star').each(function(){
                var current_child_index = $(this).index();
                if(current_child_index <= current_index)
                {
                    $(this).addClass('fa-star-hover');
                }
            });
        });
        // Remove the hover effect when mouse out of the star container.
        $(defaults.star_container).mouseout(function(){
            $('.fa-star').removeClass('fa-star-hover');
        });

        $('.fa-star').click(function(){
            $('.fa-star').removeClass('fa-star-hover');
            $('.fa-star').removeClass('fa-star-selected');
            var current_index = $(this).index();
            $('.fa-star').each(function(){
                var current_child_index = $(this).index();
                if(current_child_index <= current_index)
                {
                    $(this).addClass('fa-star-selected');
                }
            }); 
            defaults.parameters.rating = current_index + 1;
            // Reset the title if specified.
            _myself.set_title(defaults.title_on_select_star);

            _myself.add_additional_info(current_index);
            Helpers.popover({element : $(this), show : 'true'});
            skipRating = 0;
        });

        $(defaults.additional_info_element).on('click','.small-button',function(){
            var current_index = $(this).index();
            //$(defaults.additional_info_element).find('textarea').val('');
            defaults.parameters.ratingReasonOther = defaults.additional_info[current_index];
            $(defaults.additional_info_element).find('.small-button').addClass('fading-for-rating');
            $(this).removeClass('fading-for-rating');
            $(defaults.additional_info_element).find('.small-button').last().removeClass('fading-for-rating');

        });

        $(defaults.additional_info_element).off('keyup').on('keyup','textarea',function(e){
            //defaults.parameters.ratingReasonOther = '';
            //$(defaults.additional_info_element).find('.small-button').addClass('fading-for-rating');
            defaults.parameters.comment = $(this).val();
            if(e.keyCode === 13)
            {
                Helpers.popover({show : 'false'});
            }
        });
    };
    this.reset_parameters = function()
    {
        defaults.parameters.contentID = 0;
        defaults.parameters.contentType = '';

        defaults.parameters.rating = 0;
        defaults.parameters.comment = '';
        defaults.parameters.ratingReasonOther = '';

        $('.fa-star').removeClass('fa-star-hover');
        $('.fa-star').removeClass('fa-star-selected');

        $(defaults.additional_info_element).find('.small-button').removeClass('fading-for-rating');
    };
    this.init();    
};
