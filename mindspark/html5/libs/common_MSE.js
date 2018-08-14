//This file includes all common functions that can be used in MSEnglish GREs

function containerResize()
{ 
	var scaleFactor = 1;
	var scaleFactor1 = 0;
	var scaleFactor2 = 0;
	var elem = 'body';
	console.log(elem)
	if(window.innerHeight < $(elem).height()) {
		scaleFactor1 = parseFloat(window.innerHeight/$(elem).height()); 
	}
	else
		scaleFactor1 = 1 ;									

	if(window.innerWidth < $(elem).width()) {
		scaleFactor2 = parseFloat(window.innerWidth/$(elem).width());
	}
	else
		scaleFactor2 = 1 ;									

	if(scaleFactor1 < scaleFactor2)
		scaleFactor = scaleFactor1;
	else
		scaleFactor = scaleFactor2;	
	console.log(scaleFactor)
	$('body').css({"-webkit-transform": "scale("+scaleFactor+")","-webkit-transform-origin":"top left"});
	$('body').css({"-moz-transform": "scale("+scaleFactor+")","-moz-transform-origin":"top left"});	
	$('body').css({"-o-transform": "scale("+scaleFactor+")","-o-transform-origin":"top left"});	
	$('body').css({"-ms-transform": "scale("+scaleFactor+")","-ms-transform-origin":"top left"});	
	$('body').css({"transform": "scale("+scaleFactor+")","transform-origin":"top left"});
}

function replaceDynamicTextNew(text)
{	
	return text.replace(/#.*?#/ig,function(match){
		var actualWord = match.replace(/#/ig, '');
		return eval(actualWord);
	});
}

function getURLParameters() {
	var parameters = new Object();
	var id = document.URL.indexOf('?');
	if (id != -1) {
		var keyValuePair = document.URL.substring(id+1, document.URL.length).split('&');
		for (var i=0; i<keyValuePair.length; i++) {
			keyValue = keyValuePair[i].split('=');
			parameters[keyValue[0]] = decodeURIComponent((keyValue[1]+'').replace(/\+/g, '%20'));
		}
	}
	return parameters;
}


function osDetection(){
	
    return ( 
        (navigator.userAgent.indexOf("iPhone") != -1) ||
        (navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1) || (navigator.userAgent.indexOf("Android") != -1)
    );
}


function implode (glue, pieces) {
    // Joins array elements placing glue string between items and return one string  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/implode
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // +   improved by: Itsacon (http://www.itsacon.net/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
    // *     returns 2: 'Kevin van Zonneveld'
    var i = '',
        retVal = '',
        tGlue = '';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof(pieces) === 'object') {
        if (Object.prototype.toString.call(pieces) === '[object Array]') {
            return pieces.join(glue);
        } 
        for (i in pieces) {
            retVal += tGlue + pieces[i];
            tGlue = glue;
        }
        return retVal;
    }
    return pieces;
} 
 

/* --------------- MSEnglish functions added --------------------- */


// clearAllTimeouts() function clears all the current existing timeouts
// self executing anonymous function
(function () {
    //IDs are stored here
    var timeoutIDs = [];

    //save the built-in function
    var _setTimeout = window.setTimeout;

    //replace the built-in by our function
    window.setTimeout = function () {
        //call the built-in with the same arguments and store the timeoutID
        timeoutIDs.push(_setTimeout.apply(window, arguments));
    };

    //clearAllTimeouts calls clearTimeout on each stored ID.
    window.clearAllTimeouts = function () {
        for (var i = 0, l = timeoutIDs.length; i != l; i++) {
            clearTimeout(timeoutIDs[i]);
        }
        //reset the store, cleared IDs are not valid anymore
        timeoutIDs = [];
    };

})();

// function for binding and unbinding click events
function bindUnbindClick(actionName,clickFunction,elementID)
{
	if(actionName == "bind")
	{
		console.log(elementID);
	}
}

// function for printing text letter by letter
// example call-> $([element]).writeHTML([content],[callback Function],[interval Time]);
$.fn.writeHTML = function (content, callback, time) {
	var contentArray = content.split(""),
	inTags = '';
	var current = 0;
	var elem = this;
	isWriting = true;
	var firstCallOfShowText=true;

	intervalTimeoutIds2 = window.setInterval(function () {
	 firstCallOfShowText=false;
	 if (current < contentArray.length) {

	    if (contentArray[current] == "<") {
	       while (contentArray[current] != ">") {
	          inTags += contentArray[current];
	          current++;
	       }
	       inTags += contentArray[current];
	       current++;
	       if (inTags != "</br>") {
	          while (contentArray[current] != ">") {
	             inTags += contentArray[current];
	             current++;
	          }
	          
	       }
	       inTags += contentArray[current];
	       current++;
	       elem.html(elem.html() + inTags);
	       inTags = '';
	    }
	    else {
	       elem.html(elem.html() + contentArray[current++]);
	    }
	 }
	 else if (current == contentArray.length) {
	    current++;
	    clearInterval(intervalTimeoutIds2);
	    isWriting = false;
	    if (callback)
	    {
	       callback();
	       firstCallOfShowText = true;
	    }
	 }
	}, time);
}

// function for printing text word by word
// example call-> $([element]).writeHTMLWord([content],[interval Time]);
$.fn.writeHTMLWord = function(str,time)
{
	var contentArray = str.split(" ");
	var elem = this;
	elem.html("");
	var spans = '<span>' + str.split(/\s+/).join(' </span><span>') + '</span>';
	$(spans).appendTo(elem).each(function(i) {
		var element = this;
		$(element).css('display','none');
		setTimeout(function(){
			$(element).css('display','inline');
		}, (time * i) );
	});
}

//to remove double spaces in string (especially used for parser)
$.fn.doubleSpaceRemove = function()
{
	var text1 = "";
	var elem = this;
	var textArr = elem.attr("value").split(" ");
	for(j=0; j<textArr.length; j++)
	{
		if(textArr[j] != "")
			text1 += " " + textArr[j];
	}
	elem.attr("value",text1.toLowerCase().trim());
}

//only alphabets as input
function onlyAlphabetsFunc()
{
	$('input[type="text"]').live('keypress',function(e) 
	{
	/*	console.log("Key pressed"); */
		e.keyCode = (e.keyCode != 0)?e.keyCode:e.which; // Mozilla hack..
		var lastString = e.target.value.substring(e.target.value.length - 1);
		if((e.keyCode < 97 || e.keyCode > 122) && (e.keyCode < 65|| e.keyCode > 90) && e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 32) 
			e.preventDefault();
		if(isNaN(parseInt(lastString))){
			if(e.target.value.substring(e.target.value.length -2) == "  ")			// checks if two consecutive spaces exist
			{	
				e.target.value = e.target.value.substring(0, e.target.value.length - 1);
			}
		}
	});
}

// for alternating css properties 
$.fn.alternate = function(JSONObject)
{
	var counter1=0;
	var setIntervalBlink1;
	var elem = this;
	var param1 = JSONObject.property1;
	var param2 = JSONObject.property2;
	var param3 = JSONObject.timeDelay;
	var param4 = JSONObject.numTimes;
	var param5 = JSONObject.callback;
	var property = Object.keys(param1)[0]; //gets the key for param1's one and only one (first) object
	
	//checking all variables
	if(!param2)
	{
		param2 = {};
		for(var keys in param1){
			if(param1.hasOwnProperty(keys)){
				param2[keys] = elem.css(keys);
			}
		}
	}
	if(param3 == null)
	{
		param3 = 500;
	}
	if(param4 == null)
	{
		param4 = 5;
	}
	setIntervalBlink1 = window.setInterval(function(){
		if(counter1%2==0)
		{
			elem.css(param1);
		}
		else
		{
			elem.css(param2);
		}
		counter1++;
		if(counter1>=param4) 
		{
			clearInterval(setIntervalBlink1);
			if(param5)
			{
				param5();
			}
		}
	},param3);	
}
