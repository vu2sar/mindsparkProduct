/**
 * editor_plugin_src.js
 *
 * Copyright 2011, Education Initiative Pvt Ltd
 */

(function() {
	// Load plugin specific language pack	
	function getBlankNo(ques)
	{ 
		var matches = ques.match(/\[blank_\d[^\]]*\]/g);
	
		if(matches===null)
			return 1;
	
	
		var maxblankno=0;
		for(var i=0; i<matches.length; i++)
		{
			var tmp = matches[i].substring(7,8);
			if(parseInt(tmp)>maxblankno)
				maxblankno = parseInt(tmp);
		}
		maxblankno += 1;
		return maxblankno;
	}
	
	tinymce.create('tinymce.plugins.mindspark', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('uploadFile', function() {
				ed.windowManager.open({
						file : url + '/uploadFile.php',
						width : 320 + ed.getLang('example.delta_width', 0),
						height : 120 + ed.getLang('example.delta_height', 0),
						inline : 1
				}, {
						plugin_url : url, // Plugin absolute URL
						some_custom_arg : 'custom arg' // Custom argument
				});
				//ed.execCommand('mceInsertContent',false,'Manish Dariyani');
				return false;
			});
			
			ed.addCommand('mindsparkBlank', function() {
				blankCounter = getBlankNo(ed.getContent());
				ed.execCommand('mceInsertContent',false,'[blank_'+blankCounter+']');
				return false;
			});
			

			ed.addCommand('mindsparkNumeric', function() {
				blankCounter = getBlankNo(ed.getContent());
				ed.execCommand('mceInsertContent',false,'[blank_'+blankCounter+',numeric]');
				return false;
			});
			
			ed.addCommand('mindsparkFracbox', function() {
				blankCounter = getBlankNo(ed.getContent());
				ed.execCommand('mceInsertContent',false,'[blank_'+blankCounter+',fracbox]');
				return false;
			});
			
			ed.addCommand('mindsparkEqu', function() {
				ed.execCommand('mceInsertContent',false,'&lt;equ> Type your equation here &lt;/equ>');
				return false;
			});

			
			ed.addCommand('mindsparkFraction', function() {
				ed.execCommand('mceInsertContent',false,'{a} &#92;over {b}');
				return false;
			});
			
			ed.addCommand('mindsparkPower', function() {
				ed.execCommand('mceInsertContent',false,'<span class="math">{x}^{a}</span>');
				return false;
			});
			
			ed.addCommand('mindsparkBMatrix', function() {
				ed.execCommand('mceInsertContent',false,'&#92;begin{bmatrix} a_{11} & a_{12} & &#92;ldots & a_{1n} &#92;cr a_{21} & a_{22} & &#92;ldots & a_{2n} &#92;cr &#92;vdots & &#92;vdots & &#92;ddots & &#92;vdots &#92;cr a_{m1} & a_{m2} & &#92;ldots & a_{mn} &#92;end{bmatrix}');
				return false;
			});
			
			ed.addCommand('mindsparkPMatrix', function() {
				ed.execCommand('mceInsertContent',false,'&#92;begin{pmatrix} a_{11} & a_{12} & &#92;ldots & a_{1n} &#92;cr a_{21} & a_{22} & &#92;ldots & a_{2n} &#92;cr &#92;vdots & &#92;vdots & &#92;ddots & &#92;vdots &#92;cr a_{m1} & a_{m2} & &#92;ldots & a_{mn} &#92;end{pmatrix}');
				return false;
			});
			
			ed.addCommand('mindsparkPI', function() {
				ed.execCommand('mceInsertContent',false,'<span style="font-family: \'Times New Roman\'; font-size: 20px">&pi;</span>');
				return false;
			});
			
			ed.addCommand('mindsparkVector', function() {
				ed.execCommand('mceInsertContent',false,'<span style="font-family: \'Times New Roman\'; font-size: 20px">vec{~}</span>');
				return false;
			});
			
			ed.addCommand('mindsparkRupee', function() {
				ed.execCommand('mceInsertContent',false,'<span class="WebRupee">Rs.</span>');
				return false;
			});
			

			// Register example button
			ed.addButton('mindsparkToolbarRupee', {
				title : 'Insert Rupee',
				cmd : 'mindsparkRupee',
				image : url + '/img/blank.jpg'
			});
			
			ed.addButton('mindsparkToolbarBlank', {
				title : 'Insert Blank',
				cmd : 'mindsparkBlank',
				image : url + '/img/blank.jpg'
			});
			
			ed.addButton('mindsparkToolbarNumeric', {
				title : 'Insert Numeric Blank',
				cmd : 'mindsparkNumeric',
				image : url + '/img/numeric.jpg'
			});
			
			ed.addButton('mindsparkToolbarFracbox', {
				title : 'Insert Fracbox',
				cmd : 'mindsparkFracbox',
				image : url + '/img/fracbox.jpg'
			});
			
			ed.addButton('mindsparkToolbarEqu', {
				title : 'Add jsMath Tag',
				cmd : 'mindsparkEqu',
				image : url + '/img/equ.png'
			});
			
			ed.addButton('mindsparkToolbarAbyB', {
				title : 'Fractions',
				cmd : 'mindsparkFraction',
				image : url + '/img/AbyB.png'
			});
			
			ed.addButton('mindsparkToolbarXpowerY', {
				title : 'X power A',
				cmd : 'mindsparkPower',
				image : url + '/img/XpowerY.png'
			});
			
			ed.addButton('mindsparkToolbarBmatrix', {
				title : 'Matrix [::]',
				cmd : 'mindsparkBMatrix',
				image : url + '/img/bmatrix.png'
			});
			
			ed.addButton('mindsparkToolbarPmatrix', {
				title : 'Matrix (::)',
				cmd : 'mindsparkPMatrix',
				image : url + '/img/pmatrix.png'
			});
			
			ed.addButton('mindsparkToolbarPi', {
				title : 'Mindspark PI',
				cmd : 'mindsparkPI',
				image : url + '/img/pi.png'
			});
			
			ed.addButton('mindsparkToolbarVector', {
				title : 'Mindspark Vector',
				cmd : 'mindsparkVector',
				image : url + '/img/vector.PNG'
			});
			
			ed.addButton('mindsparkToolbarFU', {
				title : 'Upload a File',
				cmd : 'uploadFile',
				image : url + '/img/upload.gif'
			});
			
			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('mindsparkToolbar', n.nodeName == 'IMG');
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
				longname : 'Mindspark Toolbar',
				author : 'Education Initiative Pvt Ltd',
				authorurl : 'http://www.ei-india.com',
				infourl : 'http://www.ei-india.com',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('mindsparkToolbar', tinymce.plugins.mindspark);
})();