/**
 * @author Kalpesh Awathare
 * 
 * Dependencies: JQuery, helper.js - mindspark english
 */

SKILLOMETER_CONTSANTS = {
    METER_WIDTH: 300
};

//function SKILLOMETER(userId, parent){
function SKILLOMETER(parent){
    var _myself = this;
    this.parent = parent;
    
    //this.getData = function(id){
    this.getData = function(){
        if(sessionData.category != 'STUDENT')
            return;
        $.ajax({
            context: this,
            //url : Helpers.constants['CONTROLLER_PATH'] + 'home/getSkillOMeterInfo/' + id,
            url : Helpers.constants['CONTROLLER_PATH'] + 'home/getSkillOMeterInfo/',
            dataType : 'JSON'
        }).done(function(response){
            Helpers.ajax_response( _myself.showSkillOMeter , response , []);
        });
    };
    
    this.showSkillOMeter = function( data , extraParams )
    {
        _myself.createSkillometer(data);
        _myself.setBindings();  
        if(sessionData.currentLocation.location === "")
            flashTip('skillometer');
    }
    /*
     * converts a json into skillometer. Epic win here.
     */
    this.createSkillometer = function(data){
        this.createNodes(data, this.parent);
        this.getNonBaseNodes();
        this.computeNodeValues();
        this.setMeters();
        var _this = this;
        //time delay for transition to work
        setTimeout(function(){
            _this.setWidthForMeters();
            _this.writeTextValues();
            _this.updateMeterTexts();
        },50);
    };
    
    /*
     *Creates array of collapsibles, these contain base nodes or other collapsibles.
     */
    this.getNonBaseNodes = function(){
         //creating an array of '.collapsible' with the order:innermost on bottom
        var currentLevelArray = $('#skillometer > div > .collapsible');
        this.collapsibles = [];
    
        while(currentLevelArray.length != 0){
            for(var i = 0 ; i < currentLevelArray.length; i ++){
                this.collapsibles.push(currentLevelArray[i]);
            }
            var tempArray = [];
            for(var i = 0; i < currentLevelArray.length ; i++){
                var innerItems = $(currentLevelArray[i]).children('.collapsible');
                for(var j = 0 ; j < innerItems.length; j++){
                    tempArray.push(innerItems[j]);
                }
            }
            currentLevelArray = tempArray;
        }
    };
    
    /* 
     *  Fills meter values from inner most collapsibles thus creating averages while going outwards from inner divs
     *  Based on their data attributes
     */
    this.computeNodeValues = function(){
        for(var i = this.collapsibles.length - 1 ; i >=0 ; i--){
            var itsMeters = $(this.collapsibles[i]).children('div').children('.meterContainer');
            var value = 0;
            for(var j = 0; j < itsMeters.length; j++){
                value += parseFloat($(itsMeters[j]).attr('data'));
            }
            value = value / itsMeters.length;
            
            //creating meter div for each collapsible, which are basically the averages of inner metre divs.
            this.createMeterDiv(this.collapsibles[i], value);    
        }
    };
    
    /* 
     *Creates presentation (bars and text) inside the meter containers
     */
    this.setMeters = function(){
        var meters = $('.meterContainer', this.parent);
        for(var i = 0 ; i < meters.length; i++){
            var element = document.createElement('div');
            element.className = 'meterPresent';
            $(meters[i]).append(element);
            
            var meterText = document.createElement('div');
            meterText.className = 'meterText';
            //parseFloat 100.00 gives 100. win here.
            $(element).append(meterText);
        }
    };
    
    /*
     * Sets the width for the bars
     */
    this.setWidthForMeters = function(){
        this.meterPresents = $('.meterPresent', this.parent);
        for(var i = 0 ; i < this.meterPresents.length; i++){
            var percentageWidth = $($(this.meterPresents[i]).parent()).attr('data');
            if((percentageWidth/100) * SKILLOMETER_CONTSANTS.METER_WIDTH > 50){
                $(this.meterPresents[i]).addClass('toUpdate');
            }
            $(this.meterPresents[i]).css({
                'width': percentageWidth + '%',
                'height': '100%'
            });
        }
        
        this.toUpdate = $('.toUpdate', this.parent);
    };
    
    /*
     * create nodes inside current skill branch passed as key. 
     * Warning! uses recursion.
     * 
     * Magic happening below. If stuck ask for baba. Else god bless
     */
    this.createNodes = function(data ,parent, key){
        /*
         * For every key - value pair there needs to be a div. The div can either be
         * 1. Container for inner key - value pairs
         * 2. Atomic div that displays data. 
         */ 
        var element  = document.createElement('div');
        element.className = 'skillometer-container';
        
        /*
         * Only the first call will not have a key because it is the parent container of all 
         * skillometer data. All the recursive call will have the label appended.
         */         
        if(key){
            $(element).append("<label class='skillometer-label'>" + key + "</label>");
        }
    
        /*
         * If the data passed is not an atomic float value.
         * Create container of collapsible else create an end value node.
         */
        if(typeof data == "object"){
            // biapassing first call by checking for key.
            if(key){
                this.createContainerDiv(element);
            }
    
            for(var key in data){
                var value = this.createNodes(data[key], element, key);
            }
        }
        else{
            this.createMeterDiv(element, data);
        }
        $(parent).append(element);
        return data;
    };
    
    /*
     * creates container div with a collapsor button.
     */
    this.createContainerDiv = function(element){
        element.className = 'collapsible collapsed';
        var button = document.createElement('button');
        button.className = 'collapsor collapsed';
        $(element).append(button);
    };
    
    /*
     * creates div that displays value of the node.
     */
    this.createMeterDiv = function(element, value){
        var meterElement = document.createElement('div');
        meterElement.className = 'meterContainer';
        $(meterElement).attr('data', value);
        $(element).append(meterElement);
    };
    
    this.setBindings = function(){
         $('.collapsor').bind('click', function(){
            $(this).toggleClass('collapsed');
            var parent = $(this).parent();
            parent.toggleClass('collapsed');
            
            //collapse other siblings and their buttons if this is opened.
            if(!parent.hasClass('collapsed')){
                parent.siblings('.collapsible').addClass('collapsed');
                parent.siblings('.collapsible').find('.collapsor').addClass('collapsed');
            }
        });
    };
    
    /*
     * starts an interval that checks the current width percentage and writes them into the .meterValue div 
     */
    this.updateMeterTexts = function(){
        var _this = this;
        this.intervalId = setInterval(function(){
            for(var i = 0 ; i < _this.toUpdate.length; i++){
                $('.meterText', _this.toUpdate[i]).html(Helpers.decimalToPlaces($(_this.toUpdate[i]).width() * 100/SKILLOMETER_CONTSANTS.METER_WIDTH, 0) +  '%');
            }    
        },10);
        /*
         * add event listener here and then clear interval.
         */
        $('.meterPresent')[0].addEventListener(Helpers.getTransitionEndEventName(), function(){
            clearInterval(_this.intervalId);
            _this.writeTextValues();
        });
    };
    
    this.writeTextValues = function(){
        this.meterTexts = $('.meterText');
        for(var i = 0 ; i < this.meterTexts.length; i++){
            var meterText = this.meterTexts[i];
            $(meterText).html(Math.round($($(meterText).parent().parent()).attr('data')) + '%');
        }
    };
    
    //starts the magic
    //this.getData(userId);
    // The game begins
    this.getData();
};
