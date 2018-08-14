/*
* author: Kalpesh Awathare
*
* Passigifies a div element. Can be used anywhere with some external css.
* dependencies: JQuery 1.7 or up (review before updating), Helper.js
* Usage:Call Passage with paramerter1: Dom element, Parameter2: boolean - need controls or not.
* Use this constructor to create a nice passage object that does all the passage related work internally: Like getting
* the passage, preparing the view and displaying the passage.
*/

//Constants.
var PASSAGE_VIEW = '<div class="passageButtonContainer"><div class = "passage"></div><button class = "leftButton navigationButton"> <i class="fa fa-caret-left"></i>  </button><button class = "rightButton navigationButton"> <i class="fa fa-caret-right"></i> </button></div>';
﻿
function Passage(element, navigation) {
    //Holds all the passage data and the functions of get passage Data.
    this.Model = function() {
        this.storedPassages = {};
        this.currentPassage = {
            passage : '',
            page : 0,
            name : '',
            timeTaken : 0,
        };
        this.timerId = 0;
        
        // Get the passage from database. If already loaded once then gets them from the stored passages.
        this.getPassage = function(passageID) {
            if (this.storedPassages[passageID]) {
                this.currentPassage = this.storedPassages[passageID];
                this.view.update();
                return;
            }

            var queryOn = 'passageName';
            if (!isNaN(passageID)) {
                queryOn = 'passageID';
            }

            this.currentPassage = {
                passage : '',
                page : 0,
                timeTaken : 0,
            };

            this.currentPassage.ID = passageID;

            var formData = {
                'loadSaveMode' : 1,
                'passageID' : passageID,
            };

            $.ajax({
                context : this,
                url : Helpers.constants['GET_PASSAGE_PATH'],
                type : "POST",
                data : formData,
                dataType : "json"
            }).done(this.setPassage);
        };
        
        this.setPassage = function(response){
            if (response != '0') {
                this.currentPassage = response;
                this.storedPassages[this.currentPassage.passageID] = this.currentPassage;
            } else {
                alert('no such passage found');
            }
            this.view.update();
        };

        this.startCountingTime = function() {
            clearInterval(this.timerId);
            this.timerId = setInterval(this.timeStep, 1000);
        };

        var _this = this;
        // this timer is calculating the timetaken in each page. the timer is reset to 0 on page change.
        this.timeStep = function() {
            _this.currentPassage.timeTaken++;
        };

        this.stopCountingTime = function() {
            clearInterval(this.timerId);
        };
    };

    /*
     * Creates Views. Functions to show and update Views.
     * param1: model = the model to bind to
     * param2: element = the container inside which views need to be generated.
     * param3: navigation = need controls or not.
     */
    this.View = function(model, element, navigation) {
        this.model = model;
        this.container = $(element);
        this.mode = 'passage';
        this.targetClone = '';
        this.firstShift = true;

        this.container.append(PASSAGE_VIEW);
        this.passageBin = $('.passage', this.container[0]);
        this.imageBin = $('.imageBin', this.container[0]);

        if (!navigation) {
            $('.navigationButton', this.container[0]).remove();
        }
        
        /*
         * This is the key function called externally to render passage.
         */
        this.show = function(params) {
            this.mode = 'passage';
            this.afterViewUpdate = params.onload;
            this.onComplete = params.oncomplete;
            if(params.passage)
                this.model.setPassage(params.data);
            else
                this.model.getPassage(params.id);
            /*no continuation after the last page if passage is isolated.
             *In general: passages are isolated in passage viewer and continuous in student interface 
             */ 
            this.isolated = params.isolated;
        };
        
        this.update = function() {
            if (this.mode == 'passage') {
                this.container.show();
                if(this.model.currentPassage.Form == 'Graphic'){
                    this.createGraphicPages();
                }else{
                    this.normalPages();
                }
            } else {
                this.setCompressedPassage();
            }
        };
        
        this.normalPages = function(){
            this.insertImages(this.model.currentPassage.passageContent); 
        };
        
        /* create pages in the order of passage images in passage creator.
         */
        this.createGraphicPages = function(){
            this.model.currentPassage.passage = [];
            this.createTitlePage();
            
            this.imageBin.hide();
            this.passageBin.css('text-align', 'center');
            
            var images = $('img', this.model.currentPassage.passageImages);
            for(var i = 0; i < images.length; i++){
                var filename = images[i].getAttribute('filename');
                if(filename != this.model.currentPassage.titleImage){
                    $(images[i]).attr('src', this.getPathForAssets(filename));
                    this.model.currentPassage.passage.push(images[i].outerHTML);
                }
            }
            
            this.startPassage();
        };

        /*Title page is not in passage content. This page is generated based on these passage data:
         *Author, Source, titleImage, passageName and intro
         * and added as first page. 
         */
        this.createTitlePage = function() {
            var container = document.createElement('div');

            var currentPassage = this.model.currentPassage;
            
            //title Image
            var titleImage;
            if(currentPassage.titleImage){
                titleImage = document.createElement('img');
                titleImage.src = this.getPathForAssets(currentPassage.titleImage);
                container.appendChild(titleImage);
                $(titleImage).css({
                    'height': (this.passageBin[0].offsetHeight * 0.5) + 'px',
                    /*'min-width' : '50%',*/
                    'max-width' : '90%'
                });
            }
            
            //title:
            var title = document.createElement('h1');

            title.innerHTML = currentPassage.passageName.replace(/\\/g, '');
            container.appendChild(title);

            //by:
            var author = currentPassage.Author;
            if (author && author.trim()) {
                var by = document.createElement('h3');
                by.innerHTML = 'By: ' + author;
                $(by).css({
                    'font-style': 'italic',
                });
                container.appendChild(by);
            }
            
            //Intro:
           var intro = currentPassage.intro;
            if(intro && intro.trim()){
                var introDiv = document.createElement('span');
                introDiv.innerHTML = intro;
                $(introDiv).css({
                    'display': 'block',
                    'font-size': '.83em',
                    'margin-top':'1.67em',
                    'margin-bottom':'1.67em',
                    'margin-left': '0',
                    'margin-right': '0'
                });
				
				container.appendChild(introDiv);
            }

            $(container).css({
                width: '100%', 
                top: '50%', 
                position: 'relative',
                'clear': 'both',
                'display': 'block',
                '-moz-transform': 'translate(0,-50%)',
                '-webkit-transform': 'translate(0,-50%)',
                '-ms-transform': 'translate(0,-50%)',
                'transform': 'translate(0,-50%)',
                'text-align': 'center',
                'left': '0px',
				'line-height' : '1.2' 
            });
            var pageString = container.outerHTML;
            
            //source:
            var source = currentPassage.Source;
            if (source && source.trim()) {
                var sourceTag = document.createElement('span');
				var regex = /(?:^<p[^>]*>)|(?:<\/p>$)/g;
				source=source.replace(regex, "");
                sourceTag.innerHTML = 'From: ' + source;
                $(sourceTag).css({
                    'bottom': '0px',
                    'left': '0px',
                    'position':'absolute',
                    'width':'100%',
                    'padding': '10px',
                    'text-align': 'center',
                    'color': '#888888'
                });
                pageString += sourceTag.outerHTML;
            }
            

          currentPassage.passage.push(pageString);
        };
    
        /*
         * called whenever there is a page change. This is explicitly called from page changing functions
         */
        this.notifyPageChange = function(pageNo, finish) {
            if (this.onPageChange) {
                this.onPageChange(pageNo, finish);
            }
        };
        
        /*
         * Image is inserted after the paragraph containing the tag. This function controls how/where the image should be
         * in the paragraph. Some bit of help is also taken from css to align the image.
         * Images are preloaded before creating pages.
         */
        this.insertImages = function(){
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.model.currentPassage.passageContent;
            var that = this;
            
            var imagePaths = [];
            $('span[data-file]', tempDiv).each(function(index, element){
                var parent = Helpers.closestParent(this, 'P');
                imagesInParent = $('img', tempDiv);
                var filename = this.getAttribute('data-file');
                for(var i = 0 ; i < imagesInParent.length; i++){
                    if(imagesInParent[i].getAttribute('data-file') == filename){
                        return;
                    }
                }
                var image = document.createElement('img');
                var path = that.getPathForAssets(filename);
                image.src = path;
                image.className = 'passageImage';
                $(image).attr('data-file', filename);
                $(image).insertAfter(parent);
                imagePaths.push(path);
            });
            
            var callback = function(context){
                return function(){
                    context.createPages(tempDiv.innerHTML);
                    context.startPassage();
                };
            }(this);
            
            Helpers.preloadImages(imagePaths, callback);
            sessionData.currentLocation.type = 'passage';
            $( "img" )
              .error(function() {
                 imgNotLoading();   
            });
        };
        
        /*
         * creates a clone of the container element inside which passage will be shown. If the paging had been done in the container
         * itself the text would flash for a brief period during page creation process. The clone is invisible.
         */
        this.createPages = function(passageContent) {
            this.targetClone = document.createElement('div');
            var $targetClone = $(this.targetClone);
            $targetClone.addClass('passage hiddenClone');
            $('.passageButtonContainer', this.container).append(this.targetClone);
            var containerHeight = this.passageBin[0].offsetHeight;
            this.targetClone.className = 'passage';

            this.model.currentPassage.passage = [];
            this.createTitlePage();
            this.fillPages($targetClone, passageContent);
            $targetClone.remove();
        };

        
        /* Fill the page until the container overflows. On overflow create a new page. Victory recursion.
         * Keys
         * ≴ - change target to the container clone
         * ☎ - change target to parent
         * Џ - &nbsp; character.
         */
        this.fillPages = function(target, content) {
            var tempDiv = document.createElement('div');
            //&nbsp;
            content = content.replace(/&nbsp;/g, 'Џ');
            tempDiv.innerHTML = content;
            var elementToReplace = $(tempDiv).children()[0];

            // replacers are for replacing any content that is not a letter but is an element in the flow.
            // like tags, &nbsp; will be replaced with single character and then again assigned in content array
            // at their respective places.
            var replacers = {};
            while (elementToReplace) {
                $(elementToReplace, tempDiv).replaceWith(' #7@7@7# ');
                replacers[tempDiv.innerHTML.indexOf(' #7@7@7# ')] = elementToReplace.outerHTML;
                tempDiv.innerHTML = tempDiv.innerHTML.replace(' #7@7@7# ', '7');
                elementToReplace = $(tempDiv).children()[0];
            }

            var contentArray = tempDiv.innerHTML.split('');
            for (var key in replacers) {
                if (replacers.hasOwnProperty(key)) {
                    contentArray[key] = replacers[key];
                }
            }

            var indexOfEntry = -1;
            var previousHTML = target[0].innerHTML;
            while (this.checkHeight()) {
                indexOfEntry++;
                if (indexOfEntry >= contentArray.length) {
                    this.makePage();
                    return;
                }
                if (contentArray[indexOfEntry] == '≴') {
                    target = $(this.targetClone);
                    previousHTML = target[0].innerHTML;
                } else if (contentArray[indexOfEntry] == '☎') {
                    target = $(target).parent();
                    previousHTML = target[0].innerHTML;
                } else if (contentArray[indexOfEntry] == 'Џ') {
                    previousHTML = target[0].innerHTML;
                    target.append('&nbsp;');
                } else {
                    previousHTML = target[0].innerHTML;
                    target.append(contentArray[indexOfEntry].replace(/Џ/g, '&nbsp;'));
                }
            }

            if (indexOfEntry == -1) {
                this.makePage();
                target = this.cleanTarget(target);
                if (this.leftOverContent.length > 0) {
                    this.fillPages(target, this.leftOverContent.join(''));
                }
                return;
            }

            //finer separation. Absolute magic happening below. Meddle at your own risk.
            target[0].innerHTML = previousHTML;
            if (this.firstShift) {
                this.leftOverContent = contentArray.slice(indexOfEntry);
            } else {
                this.leftOverContent[0] = contentArray.slice(indexOfEntry).join('') + (this.leftOverContent[0] ? this.leftOverContent[0] : '');
            }
            
            //? is self closing tag
            var selfClosing = false;
            var $collection;
            if(contentArray[indexOfEntry].length  >=  2){
                $collection = $(contentArray[indexOfEntry]);
                if($collection.length == 1 && /IMG|LINK|HR/.test($collection[0].tagName)){
                    selfClosing = true;
                }
            }
                

            if (contentArray[indexOfEntry].length == 1) {
                //search till the last word breaking character like the ' ', '-' , '.' , '"' , '''
                var partialWord = '';
                var targetContent = target[0].innerHTML;
                indexOfEntry--;
                if(indexOfEntry >= 0 ){
                    while(!(/[\' \"\,.>]/.test(contentArray[indexOfEntry])) && indexOfEntry > 0){
                        partialWord += contentArray[indexOfEntry];
                        targetContent = targetContent.substring(0, targetContent.length - 1);
                        indexOfEntry--;
                    }
                }
                partialWord = partialWord.split('').reverse().join('');
                this.leftOverContent[0] = partialWord + (this.leftOverContent[0] ? this.leftOverContent[0] : '');
                target[0].innerHTML = targetContent; 
                this.makePage();
                target = this.cleanTarget(target);
                this.fillPages(target, this.leftOverContent.join(''));
            }
            else if(selfClosing){
                this.makePage();
                target = this.cleanTarget(target);
                this.fillPages(target, this.leftOverContent.join(''));
            }
            else {
                //fetch the first tag in the set. leave the rest.
                var thisContent = this.leftOverContent.shift();
                var tempTag = document.createElement('div');
                tempTag.innerHTML = thisContent;
                var firstChild = $(tempTag).children()[0];

                var moreLeftOverContent = thisContent.replace(firstChild.outerHTML, '');
                this.leftOverContent.unshift('☎' + moreLeftOverContent);

                target.append(firstChild.outerHTML);

                var lastContainer = $(target).children().last()[0];
                var lastContainerContent = lastContainer.innerHTML;
                lastContainer.innerHTML = '';

                if (this.firstShift) {
                    this.firstShift = false;
                }

                this.fillPages($(lastContainer), lastContainerContent);
            }
        };

        /*
         *remove all the nodes except for the target and its ancestors. Return cleaned target which serves as a new page 
         */
        this.cleanTarget = function(target) {
            target.html('');
            if(target.hasClass('passage')){
                return target;
            }
            
            var currentWorker = target;
            var parent = $(currentWorker).parent();
            while (!parent.hasClass('passage')) {
                parent.html(currentWorker[0].outerHTML);
                currentWorker = parent;
                parent = $(currentWorker).parent();
            }

            var child = parent.children()[0];
            while (child) {
                parent = $(child);
                child = parent.children()[0];
            }
            return parent;
        };
        
        /*
         * Check overflow
         */
        this.checkHeight = function() {
            if ((this.targetClone.scrollHeight - 2) <= this.targetClone.clientHeight) { //2 pixels are given for margin to work correctly on chrome
                return true;
            } else {
                return false;
            }
        };
        
        //freezes cloning and stamps a copy of the page content, converting into a page.
        this.makePage = function() {
            this.firstShift = true;
            this.model.currentPassage.passage.push(this.targetClone.innerHTML);
            $(this.targetClone).children(':not(:last)').remove();
        };

        /*
         * start displaying the passage after creating pages.
         */
        this.startPassage = function() {
            this.model.currentPassage.page = 0;
            this.model.currentPassage.complete = 0;
            
            if (this.afterViewUpdate) {
                this.afterViewUpdate(this.model.currentPassage);
            }
            
            this.controls.activateKeyboardControls();
            this.model.startCountingTime();
            $('.navigationButton', this.container).show();
            
            $('.navigationButton.leftButton').css('visibility', 'hidden');
            if (this.model.currentPassage.passage.length <= 1) {
                $('.navigationButton.rightButton').css('visibility', 'hidden');
            }
            
            this.setPage(this.model.currentPassage.page);
        };

        this.setPage = function(pageNo) {
            this.passageBin[0].innerHTML = this.model.currentPassage.passage[pageNo];
            this.model.currentPassage.timeTaken = 0;
            if(this.model.currentPassage["Form"] == "Poem")
            {
                $(".passage p").css("text-align","center");
                $(".passageImage").addClass("passageImageForPoem");
            }
            else if(this.model.currentPassage["Form"] == "Graphic" && pageNo != 0)
            {
                $(".passage p").css("text-align","justify");
                $(".passage img").addClass("passageGraphicImage");
            } 
            else
            {
                if(pageNo == 0)
                    $(".passage p").css("text-align","center");
                else
                    $(".passage p").css("text-align","justify");
                
                if($(".passageImage").hasClass("passageImageForPoem"))
                    $(".passageImage").removeClass("passageImageForPoem");
            }
        };

        this.getPathForAssets = function(fileName) {
            return Helpers.constants['LIVE_CONTENT_PATH']+ 'passages/eng/' + this.model.currentPassage.passageID + '/assets/' + fileName;
        };

        this.showLookback = function(passageID, target, callback) {
            this.mode = "lookback";
            this.afterViewUpdate = callback;
            this.lookbackTarget = target;
            this.model.getPassage(passageID);
        };

        this.setCompressedPassage = function() {
            var lookbackContainer = document.createElement('div');
            lookbackContainer.className = 'lookbackContainer';
            var close_button = '<div class="prompt-heading"><button class="close-prompt toast-close" prompt-close=\'#lookbackContent\'><i class="fa fa-close"></i></button> </div>';
            var header = document.createElement('h1');
            header.innerHTML = this.model.currentPassage.passageName.replace(/\\/g, '');
            //lookbackContainer.innerHTML =  close_button + header.outerHTML + this.model.currentPassage.passageContent;
			if(this.model.currentPassage["Form"] == "Graphic")
            {
				lookbackContainer.innerHTML =  close_button + header.outerHTML + this.model.currentPassage.passageImages;
            }else{
				lookbackContainer.innerHTML =  close_button + header.outerHTML + this.model.currentPassage.passageContent.replace(/<img\s[^>]*?src\s*=\s*['\"]([^'\"]*?)['\"][^>]*?>/ig, "<img src=''/>"); // to remove image link from passage content
			}
            

            $(this.lookbackTarget).html(lookbackContainer);
            if(this.model.currentPassage["Form"] == "Poem")
            {
                $(".lookbackContainer").css("text-align","center");
            }
            else
            {
                $(".lookbackContainer").css("text-align","justify");
            }
            this.afterViewUpdate && this.afterViewUpdate();
        };

        this.setOnPageChange = function(callback) {
            this.onPageChange = callback;
        };
    };

    this.Controller = function(model, view) {
        this.view = view;
        this.model = model;

        this.letsRead = function(){
            var leftButton = $('.navigationButton.leftButton');
            var rightButton = $('.navigationButton.rightButton');
            $(leftButton).prop('disabled',true);
            $(rightButton).prop('disabled',true);
            this.deactivateKeyboardsControls();
            this.model.timerDisable = setTimeout(this.activateControls,2000);
        };
        this.getActivateTime = function(){
            var wordCounter = $(".passage").text(); 
            wordCounter = wordCounter.replace(/\s/ig,'');
            wordCounter = wordCounter.replace(/[^\w\s]/ig,'');
            wordCounter = wordCounter.split('');
            wordCounter = wordCounter.length;
            var time = 0;
            if(sessionData.childClass == 4 ||  sessionData.childClass == 5)
            {
                 time = parseInt(wordCounter/50)*1000;
            }
            else if(sessionData.childClass == 6 ||  sessionData.childClass == 7)
            {
                 time = parseInt(wordCounter/70)*1000;
            }
            else if(sessionData.childClass == 8 ||  sessionData.childClass == 9)
            {
                 time = parseInt(wordCounter/90)*1000;
            }
            if(time < 2000)
            {
                time = 2000;
            }

            time = 2000;
            return time;


        };
        //both these functions return a valid number on successful page change. returns false when
        //passage is already at its ends.
        this.nextPage = function() {
            this.letsRead();
            if (this.model.currentPassage.page >= (this.model.currentPassage.passage.length - 1)) {
                //this.view.onComplete && this.view.onComplete();
                //var activateTime = this.getActivateTime();
                //this.model.timerDisable = setTimeout(this.activateControls,activateTime);
                this.view.notifyPageChange(this.model.currentPassage.page, true);
                return false;
            }
            $('.navigationButton.leftButton').css('visibility', 'visible');
            
            this.model.currentPassage.page++;
            this.view.notifyPageChange(this.model.currentPassage.page);
            
            if(this.view.isolated && this.model.currentPassage.page == (this.model.currentPassage.passage.length - 1)){
                $('.navigationButton.rightButton').css('visibility', 'hidden');
            }
            
            this.view.setPage(this.model.currentPassage.page);
            this.enablePassageViewerControl();
            return this.model.currentPassage.page;
        };

        this.previousPage = function() {
            // Hide plugin.
            this.letsRead();
            hidePluginIfAny();
            
            if (this.model.currentPassage.page == 0) {
                return false;
            }
            $('.navigationButton.rightButton').css('visibility', 'visible');
            if (this.model.currentPassage.page == 1) {
                $('.navigationButton.leftButton').css('visibility', 'hidden');
            }
            this.model.currentPassage.page--;
            this.view.notifyPageChange(this.model.currentPassage.page);
            this.view.setPage(this.model.currentPassage.page);
            
            return this.model.currentPassage.page;
        };
        this.enablePassageViewerControl = function(){
            var activateTime = this.getActivateTime();
            clearTimeout(this.model.timerDisable);
            this.model.timerDisable = setTimeout(this.activateControls,activateTime);
        };
        
        this.onKeyPress = function(event){
            if(document.activeElement.tagName == "BODY")
            {
                /*if(event.keyCode == 13){// || event.keyCode == 39){
                    this.nextPage();
                    this.deactivateKeyboardsControls();
                }*/  
                /*if(event.keyCode == 37){
                    this.previousPage();
                }*/
            }
        };
        
        this.activateKeyboardControls = function(){
            $(document).unbind('keypress',passageViewer).bind('keypress', passageViewer);
            /* document.addEventListener('keydown', (function(context){
                context.keyboardFunction = function(event){
                    context.onKeyPress(event);
                };
                return context.keyboardFunction;
            })(this), false);*/
        };
        
        this.deactivateKeyboardsControls = function(){
           // document.removeEventListener('keydown', this.keyboardFunction);
           $(document).unbind('keypress',passageViewer);
        };

        this.activateControls = function()
        {
            var leftButton = $('.navigationButton.leftButton');
            var rightButton = $('.navigationButton.rightButton');
            if (passageObject.controls.model.currentPassage.page < (passageObject.controls.model.currentPassage.passage.length )) {
                $(document).unbind('keypress',passageViewer).bind('keypress', passageViewer);
            }
            $(leftButton).prop('disabled',false);
            $(rightButton).prop('disabled',false);
        };
    };

    
    /*
     * creating instances of the classes created above.
     */
    this.passageModel = new this.Model();
    this.view = new this.View(this.passageModel, element, navigation);
    this.controls = new this.Controller(this.passageModel, this.view);
    this.passageModel.view = this.view;
    this.view.controls = this.controls;
    
    /*
     * A stop function that stops any ongoing intervals and deactivates controls. This should be called when the passage component is no 
     * longer needed to be displayed.
     */
    this.stop = function() {
        this.view.passageBin.html('');
        $('.navigationButton',this.view.container).hide();
        this.passageModel.stopCountingTime();
        this.controls.deactivateKeyboardsControls();
    };

    //returns the time spent on the current passage.
    this.getTimeTaken = function() {
        return this.passageModel.currentPassage.timeTaken;
    };

    //returns the current passage page number.
    this.getPageNumber = function() {
        return this.passageModel.currentPassage.page;
    };

    //attaching events if navigation is turned on. 
    if (navigation) {
        $('.navigationButton.rightButton', element).bind('click', (function(context) {
            return function() {
                context.controls.nextPage();
            };
        })(this));
        $('.navigationButton.leftButton', element).bind('click', (function(context) {
            return function() {
                context.controls.previousPage();
            };
        })(this));
    }
}