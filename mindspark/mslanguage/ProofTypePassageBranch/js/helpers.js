/**
 * @author kalpesh
 * Requires jQuery
 */

//add indexOf function if it does not exist.
if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
        for (var i = (start || 0),
            j = this.length; i < j; i++) {
            if (this[i] === obj) {
                return i;
            }
        }
        return -1;
    };
}

if (!('contains' in String.prototype)) {
    String.prototype.contains = function(str, startIndex) {
        return ''.indexOf.call(this, str, startIndex) !== -1;
    };
}

Helpers = {
    getUrlParameters : function() {
        var queryString = {};
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if ( typeof queryString[pair[0]] === "undefined") {
                queryString[pair[0]] = pair[1];
            } else if ( typeof queryString[pair[0]] === "string") {
                var arr = [queryString[pair[0]], pair[1]];
                queryString[pair[0]] = arr;
            }
        }
        return queryString;
    },
    preloadImages: function(url, callback) {
        var imageArray = [];
        if(typeof url === 'string'){
            imageArray.push(url);
        }  
        else if(url.constructor === Array){
            imageArray = url;
        }
        
        var count = 0;
        var afterImageLoaded = function(){
            count++;
            if(count === imageArray.length){
                callback && callback();
            }
        };
        
        for(var i = 0 ;i < imageArray.length ; i++){
            var image = new Image();
            image.src = imageArray[i];
            image.onload = afterImageLoaded; 
        }
    },
    escapeRegExp : function(str) {
        return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    },
    replaceAll : function(find, replace, str) {
        return str.replace(new RegExp(Helpers.escapeRegExp(find), 'g'), replace);
    },
    populateSelectElement : function(element, source) {
        function append(){
            for (var i = 0; i < source.length; i++) {
                $(element).append(Helpers.createOption(source[i], source[i]));
            }
        }
        
        $(element).html('');
        
        if (Object.prototype.toString.call(source) === "[object Array]") {
            append();    
        } else if ( typeof source === 'object') {
            for (var key in source) {
                if (source.hasOwnProperty(key)) {
                    $(element).append(Helpers.createOption(source[key], key));
                }
            }
        }
        else if( typeof source === 'string'){
            source = source.split(',');
            source.unshift('select');
            append();
        }
    },
    createOption : function(html, value) {
        var option = document.createElement('option');
        option.value = value;
        option.innerHTML = html;
        return option;
    },
    nodeWalker: function(node, func){
        func(node);
        node = node.firstChild;
        while(node){
            func(node, func);
            node = node.nextSibling;
        }
    },
    createDiv: function(html, id, click){
        var div = document.createElement('div');
        if(html){
            div.innerHTML = html;
        }
        if(id){
             div.id = id;
        }
        if(click){
            $(div).bind('click', click);
        }     
        
        return div;
    },
    createButton: function(html, id, click){
        var button = document.createElement('button');
        if(html){
            button.innerHTML = html;
        }
        if(id){
             button.id = id;
        }
        if(click){
            $(button).bind('click', click);
        }     
        
        return button;
    },
    getFileName : function(path) {
        var temp = path.split('/');
        return temp[temp.length - 1];
    },
    isBlank : function(value) {
        if (value === undefined)
            return true;

        if ( typeof value === 'string') {
            if (value.trim() == '') {
                return true;
            }
        } else if ( typeof value === 'number') {
            if (isNaN(value)) {
                return true;
            }
        }
    },
}; 