/**
 * editor_plugin_src.js
 *
 * Copyright 2011, Education Initiative Pvt Ltd
 */
var tttt=1;
(function() {
    var Label = function(ed){
        // Create label el
        this.text = ed.getElement().getAttribute("placeholder");
        this.contentAreaContainer = ed.getContentAreaContainer();
        this.parentTable=$(ed.getContainer()).find('table.mceLayout')[0];

        tinymce.DOM.setStyle(this.contentAreaContainer, 'position', 'relative');
        tinymce.DOM.setStyle(this.parentTable, 'height', 'auto');

        attrs = {style: {position: 'absolute', top:'5px', left:0, color: '#888', padding: '1%', width:'98%', overflow: 'hidden', 'z-index': -1} };
        this.el = tinymce.DOM.add( this.contentAreaContainer, "label", attrs, this.text );
    }

    Label.prototype.hide = function(){
        tinymce.DOM.setStyle( this.el, 'display', 'none' );
    }

    Label.prototype.show = function(){
        tinymce.DOM.setStyle( this.el, 'display', '' );   
    }
    tinymce.create('tinymce.plugins.placeholderPlugin', {
        init: function(ed) {
            ed.addCommand('getPlaceHolder',function(ed,o){

                var label = new Label(ed);
                onBlur();
                tinymce.DOM.bind(label.el, 'click', onFocus);
                
                tinymce.dom.Event.add(tinymce.settings.content_editable ? ed.getBody() : (tinymce.isGecko ? ed.getDoc() : ed.getWin()), 'blur', function(e) {
                    onBlur(this);
                });
                tinymce.dom.Event.add(tinymce.settings.content_editable ? ed.getBody() : (tinymce.isGecko ? ed.getDoc() : ed.getWin()), 'focus', function(e) {
                    onFocus(this);
                });

                function onFocus(e){
                    label.hide();
                    if (e.target)
                        setTimeout(function(){tinyMCE.execCommand('mceFocus', false, ed.id);},1);
                    //setTimeout(function(){tinyMCE.activeEditor.focus();},1);
                }

                function onBlur(){
                    if(ed.getContent() == '') {
                        label.show();
                    }else{
                        label.hide();
                    }
                }
            });  
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Placeholder Plugin',
                author : '',
                authorurl : '',
                infourl : '',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('placeholder', tinymce.plugins.placeholderPlugin);
})();