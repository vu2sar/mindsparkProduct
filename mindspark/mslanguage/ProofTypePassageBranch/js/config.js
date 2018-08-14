/**
 * @author eicpu92
 */

var MINIMUM_ALLOWABLE_WIDTH = 40;
var MINIMUM_ALLOWABLE_HEIGHT = 40;

var resizableConfig = {
    handles : 'se'
};

var globalFontSize = 15;
var globalAlignment = 'left';

var draggableConfig = {
    containment : '.activeWorkingArea',
};

var tinyMCEImageList = [];
var paneSeparator = '|*|p@ne Seper@t0r|*|';

var CSSConfig = {
    'text-align' : 'left',
    'display' : 'inline-block',
    'vertical-align' : 'middle',
    'overflow' : 'auto',
};

var tinymceConfig1 = {
    selector : "div.editable",
    inline : true,
    // browser_spellcheck : true,

    content_css : 'css/editor.css',

    menubar : "format edit insert",
    menu : {
        edit : {
            title : 'Edit',
            items : 'undo redo | cut copy paste pastetext | selectall'
        },
        insert : {
            title : 'Insert',
            items : 'insertdatetime charmap'
        },
        format : {
            title : 'Format',
            items : 'bold italic underline strikethrough superscript subscript | formats | removeformat'
        },
        table : {
            title : 'Table',
            items : 'inserttable tableprops deletetable | cell row column'
        },
    },
    plugins : "lists, charmap, table, insertdatetime, paste, directionality, nonbreaking, textcolor", //image plugin removed
    //image_list: tinyMCEImageList,

    toolbar : "undo redo | forecolor backcolor | singleQuote doubleQuote | bold italic | underline | alignleft alignright aligncenter alignjustify | forecolor backcolor",
    setup : function(editor) {
        editor.addButton('singleQuote', {
            title : 'singleQuote',
            text : '‘’',
            onclick : function() {
                selectedText = editor.selection.getContent();
                if (selectedText == '') {
                    alert('no text selected');
                    return;
                }
                editor.selection.setContent('‘' + selectedText + '’');
            }
        });

        editor.addButton('doubleQuote', {
            title : 'doubleQuote',
            text : '“”',
            onclick : function() {
                selectedText = editor.selection.getContent();
                if (selectedText == '') {
                    alert('no text selected');
                    return;
                }
                editor.selection.setContent('“' + selectedText + '”');
            }
        });
        
        editor.on('keydown',function(e) {
            // Capture CTRL+Enter
            if (((e.keyCode == 13 ) || (e.keyCode == 10 ) ) && (e.ctrlKey == true )) {
                var dom = this.dom;
				writingNode = this.selection.getNode();
                if(writingNode.tagName != 'SPAN'){
                    return;
                }
                
                var parents = dom.getParents(writingNode);
                
                for (var i = 0; i < parents.length; i++) {
                    currentNode = parents[i];

                    // Insert empty paragraph at the end of the parent of the closest custom tag
                    if (currentNode.nodeName == 'P') {
                        // dom.insertAfter doesn't work reliably
                        var uniqueID = dom.uniqueId();
                        $('<p id="' + uniqueID + '"><br /></p>').insertAfter(currentNode);

                        // Move to the new node
                        var newParagraph = dom.select( 'p#' + uniqueID )[0];
                        this.selection.setCursorLocation(newParagraph);

                        // Don't create an extra paragraph
                        e.preventDefault();
                        break;
                    }
                }
            }
        });
    },
};

var tinymceConfig2 = {
    // browser_spellcheck : true,
	mode:"exact",
	elements:"passageIntro,passageSource",
	menubar: false,
	statusbar: false,
    plugins : "lists, charmap, table, insertdatetime, paste, directionality, nonbreaking, textcolor", //image plugin removed
    //image_list: tinyMCEImageList,
	convert_fonts_to_spans : false,
    toolbar : "bold italic",
	panel_align: "auto",
	setup : function(editor) {
        editor.addButton('singleQuote', {
            title : 'singleQuote',
            text : '‘’',
            onclick : function() {
                selectedText = editor.selection.getContent();
                if (selectedText == '') {
                    alert('no text selected');
                    return;
                }
                editor.selection.setContent('‘' + selectedText + '’');
            }
        });

        editor.addButton('doubleQuote', {
            title : 'doubleQuote',
            text : '“”',
            onclick : function() {
                selectedText = editor.selection.getContent();
                if (selectedText == '') {
                    alert('no text selected');
                    return;
                }
                editor.selection.setContent('“' + selectedText + '”');
            }
        });
        
        editor.on('keydown',function(e) {
            // Capture CTRL+Enter
            if (((e.keyCode == 13 ) || (e.keyCode == 10 ) ) && (e.ctrlKey == true )) {
                var dom = this.dom;
				writingNode = this.selection.getNode();
                if(writingNode.tagName != 'SPAN'){
                    return;
                }
                
                var parents = dom.getParents(writingNode);
                
                for (var i = 0; i < parents.length; i++) {
                    currentNode = parents[i];

                    // Insert empty paragraph at the end of the parent of the closest custom tag
                    if (currentNode.nodeName == 'P') {
                        // dom.insertAfter doesn't work reliably
                        var uniqueID = dom.uniqueId();
                        $('<p id="' + uniqueID + '"><br /></p>').insertAfter(currentNode);

                        // Move to the new node
                        var newParagraph = dom.select( 'p#' + uniqueID )[0];
                        this.selection.setCursorLocation(newParagraph);

                        // Don't create an extra paragraph
                        e.preventDefault();
                        break;
                    }
                }
            }
        });
    },
};
