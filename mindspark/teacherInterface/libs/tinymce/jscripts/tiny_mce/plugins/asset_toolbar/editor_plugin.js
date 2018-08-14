/**
 * editor_plugin_src.js
 *
 * Copyright 2011, Educational Initiative Pvt Ltd
 * ASSET Toolbar
 * Author : Naveen
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
	tinymce.create('tinymce.plugins.asset', {
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
			var child=	ed.windowManager.open({
						file : url + '/uploadFile.php',
						width : 320 + ed.getLang('example.delta_width', 0),
						height : 120 + ed.getLang('example.delta_height', 0),
						inline : 1
				}, {
						plugin_url : url, // Plugin absolute URL
						some_custom_arg : 'custom arg' // Custom argument
				});
				return false;
			});

			ed.onInit.add(function( ed ) {
				var str =this.id+"_toolbar2";
				document.getElementById(str).style.display="none";				
			});
			
			ed.addCommand('assetBlank', function() {
				blankCounter = getBlankNo(ed.getContent());
				ed.execCommand('mceInsertContent',false,'[blank_'+blankCounter+']');
				return false;
			});
			
			ed.addCommand('assetNumeric', function() {
				blankCounter = getBlankNo(ed.getContent());
				ed.execCommand('mceInsertContent',false,'[blank_'+blankCounter+',numeric]');
				return false;
			});
			
			ed.addCommand('assetFracbox', function() {
				blankCounter = getBlankNo(ed.getContent());
				ed.execCommand('mceInsertContent',false,'[blank_'+blankCounter+',fracbox]');
				return false;
			});
			
			ed.addCommand('assetEqu', function() {
				ed.execCommand('mceInsertContent',false,'&lt;equ> Type your equation here &lt;/equ>');
				return false;
			});

			
			ed.addCommand('assetFraction', function() {
				ed.execCommand('mceInsertContent',false,'{frac(a/b)}');
				return false;
			});
			
			ed.addCommand('assetPower', function() {
				ed.execCommand('mceInsertContent',false,'<span class="math">{x}^{a}</span>');
				return false;
			});
			
			ed.addCommand('assetBMatrix', function() {
				ed.execCommand('mceInsertContent',false,'&#92;begin{bmatrix} a_{11} & a_{12} & &#92;ldots & a_{1n} &#92;cr a_{21} & a_{22} & &#92;ldots & a_{2n} &#92;cr &#92;vdots & &#92;vdots & &#92;ddots & &#92;vdots &#92;cr a_{m1} & a_{m2} & &#92;ldots & a_{mn} &#92;end{bmatrix}');
				return false;
			});
			
			ed.addCommand('assetPMatrix', function() {
				ed.execCommand('mceInsertContent',false,'&#92;begin{pmatrix} a_{11} & a_{12} & &#92;ldots & a_{1n} &#92;cr a_{21} & a_{22} & &#92;ldots & a_{2n} &#92;cr &#92;vdots & &#92;vdots & &#92;ddots & &#92;vdots &#92;cr a_{m1} & a_{m2} & &#92;ldots & a_{mn} &#92;end{pmatrix}');
				return false;
			});
			
			ed.addCommand('assetPI', function() {
				ed.execCommand('mceInsertContent',false,'<span style="font-family: \'Times New Roman\'; font-size: 20px">&pi;</span>');
				return false;
			});
			
			ed.addCommand('alpha', function() {
				ed.execCommand('mceInsertContent',false,'&alpha;');
				theme_advanced_buttons3:"";
				return false;
			});
			ed.addCommand('beta', function() {
				ed.execCommand('mceInsertContent',false,'&beta;');				
				return false;
			});
			ed.addCommand('gamma', function() {
				ed.execCommand('mceInsertContent',false,'&gamma;');				
				return false;
			});
			ed.addCommand('delta1', function() {
				ed.execCommand('mceInsertContent',false,'&Delta;');				
				return false;
			});
			ed.addCommand('delta', function() {
				ed.execCommand('mceInsertContent',false,'&delta;');				
				return false;
			});
			ed.addCommand('theta', function() {
				ed.execCommand('mceInsertContent',false,'&theta;');				
				return false;
			});
			ed.addCommand('lambda', function() {
				ed.execCommand('mceInsertContent',false,'&lambda;');				
				return false;
			});
			
			ed.addCommand('pi', function() {
				ed.execCommand('mceInsertContent',false,'&pi;');				
				return false;
			});
			ed.addCommand('sigma', function() {
				ed.execCommand('mceInsertContent',false,'&sigma;');				
				return false;
			});
			ed.addCommand('sum', function() {
				ed.execCommand('mceInsertContent',false,'&sum;');				
				return false;
			});
			ed.addCommand('root', function() {
				ed.execCommand('mceInsertContent',false,'&radic;');				
				return false;
			});
			ed.addCommand('angle1', function() {
				ed.execCommand('mceInsertContent',false,'&ang;');				
				return false;
			});
			ed.addCommand('leq', function() {
				ed.execCommand('mceInsertContent',false,'&le;');				
				return false;
			});
			ed.addCommand('geq', function() {
				ed.execCommand('mceInsertContent',false,'&ge;');				
				return false;
			});
			ed.addCommand('gg', function() {
				ed.execCommand('mceInsertContent',false,'&raquo;');				
				return false;
			});
			ed.addCommand('ll', function() {
				ed.execCommand('mceInsertContent',false,'&laquo;');				
				return false;
			});
			ed.addCommand('subset', function() {
				ed.execCommand('mceInsertContent',false,'&sub;');				
				return false;
			});
			ed.addCommand('diamond', function() {
				ed.execCommand('mceInsertContent',false,'&diams;');				
				return false;
			});
			ed.addCommand('divide', function() {
				ed.execCommand('mceInsertContent',false,'&divide;');				
				return false;
			});
			ed.addCommand('multiply', function() {
				ed.execCommand('mceInsertContent',false,'&times;');				
				return false;
			});
			ed.addCommand('parallel', function() {
				ed.execCommand('mceInsertContent',false,'||');				
				return false;
			});
			ed.addCommand('perpendicular', function() {
				ed.execCommand('mceInsertContent',false,'&perp;');				
				return false;
			});
			ed.addCommand('congruent', function() {
				ed.execCommand('mceInsertContent',false,'&cong;');				
				return false;
			});
			ed.addCommand('notequalto', function() {
				ed.execCommand('mceInsertContent',false,'&ne;');				
				return false;
			});
			ed.addCommand('belongsto', function() {
				ed.execCommand('mceInsertContent',false,'&#8712;');				
				return false;
			});
			ed.addCommand('notbelongsto', function() {
				ed.execCommand('mceInsertContent',false,'&#8713;');				
				return false;
			});
			ed.addCommand('union', function() {
				ed.execCommand('mceInsertContent',false,'&#8746;');				
				return false;
			});
			ed.addCommand('intersection', function() {
				ed.execCommand('mceInsertContent',false,'&#8745;');				
				return false;
			});
			ed.addCommand('similarto', function() {
				ed.execCommand('mceInsertContent',false,'&#8764;');				
				return false;
			});
			ed.addCommand('degree', function() {
				ed.execCommand('mceInsertContent',false,'&#176;');				
				return false;
			});
			ed.addCommand('therefore', function() {
				ed.execCommand('mceInsertContent',false,'&there4;');				
				return false;
			});
			ed.addCommand('because', function() {
				ed.execCommand('mceInsertContent',false,'&#8757;');				
				return false;
			});
			ed.addCommand('almost_equal', function() {
				ed.execCommand('mceInsertContent',false,'&#8776;');				
				return false;
			});
			ed.addCommand('sube', function() {
				ed.execCommand('mceInsertContent',false,'&#8838;');				
				return false;
			});
			ed.addCommand('phi', function() {
				ed.execCommand('mceInsertContent',false,'&#934;');				
				return false;
			});
			ed.addCommand('ballot_box', function() {
				ed.execCommand('mceInsertContent',false,'&#9744;');				
				return false;
			});
			
			
			ed.addCommand('maths',function(){
				var str =this.id+"_toolbar2";
				if(document.getElementById(str).style.display=="none")
					document.getElementById(str).style.display="";
				else
					document.getElementById(str).style.display="none";
				return false;
			});

			// Register example button
			ed.addCommand('Imag',function(){
		//	insert_image1('oDivP');
			window.open("insert_image_temp.php?textEI=oDivQ","_blank","top =100 left=800 width=400 height=400");
			return false;
			});

			ed.addButton('asset_toolbarBlank', {
				title : 'Insert Blank',
				cmd : 'assetBlank',
			});
			

			ed.addButton('asset_toolbarNumeric', {
				title : 'Insert Numeric Blank',
				cmd : 'assetNumeric',
			});
			
			ed.addButton('asset_toolbarFracbox', {
				title : 'Insert Fracbox',
				cmd : 'assetFracbox',
			});
			
			ed.addButton('asset_toolbarEqu', {
				title : 'Add jsMath Tag',
				cmd : 'assetEqu',
			});
			
			ed.addButton('asset_toolbarAbyB', {
				title : 'Fractions',
				cmd : 'assetFraction',
			});
			
			ed.addButton('asset_toolbarXpowerY', {
				title : 'X power A',
				cmd : 'assetPower',
			});
			
			ed.addButton('asset_toolbarBmatrix', {
				title : 'Matrix [::]',
				cmd : 'assetBMatrix',
			});
			
			ed.addButton('asset_toolbarPmatrix', {
				title : 'Matrix (::)',
				cmd : 'assetPMatrix',
			});
			
			
			ed.addButton('asset_toolbarImage', {
				title : 'asset Image',
				cmd : 'assetImage',
			});
			
			ed.addButton('asset_toolbarFU', {
				title : 'Upload a File',
				cmd : 'uploadFile',
			});
			
			
			ed.addButton('asset_toolbarAlpha', {
				title : 'alpha',
				cmd : 'alpha',
			});
			ed.addButton('asset_toolbarBeta', {
				title : 'beta',
				cmd : 'beta',
			});
			ed.addButton('asset_toolbarGamma', {
				title : 'gamma',
				cmd : 'gamma',			
				
			});
			ed.addButton('asset_toolbarDelta1', {
				title : 'delta1',
				cmd : 'delta1',
			});
			ed.addButton('asset_toolbarDelta', {
				title : 'delta',
				cmd : 'delta',
			});
			ed.addButton('asset_toolbarTheta', {
				title : 'theta',
				cmd : 'theta',
			});

			ed.addButton('asset_toolbarLambda', {
				title : 'lambda',
				cmd : 'lambda',
			});
			ed.addButton('asset_toolbarPi', {
				title : 'pi',
				cmd : 'pi',
			});
			ed.addButton('asset_toolbarSigma', {
				title : 'sigma',
				cmd : 'sigma',
			});
			ed.addButton('asset_toolbarSum', {
				title : 'sum',
				cmd : 'sum',
			});
			ed.addButton('asset_toolbarRoot', {
				title : 'root',
				cmd : 'root',
			});
			ed.addButton('asset_toolbarAngle1', {
				title : 'angle1',
				cmd : 'angle1',
			});
			ed.addButton('asset_toolbarLeq', {
				title : 'leq',
				cmd : 'leq',
			});
			ed.addButton('asset_toolbarGeq', {
				title : 'geq',
				cmd : 'geq',
			});
			ed.addButton('asset_toolbarAngle1', {
				title : 'angle1',
				cmd : 'angle1',
			});
			ed.addButton('asset_toolbarGg', {
				title : 'gg',
				cmd : 'gg',
			});
			ed.addButton('asset_toolbarLl', {
				title : 'll',
				cmd : 'll',
			});
			ed.addButton('asset_toolbarSubset', {
				title : 'subset',
				cmd : 'subset',
			});
					ed.addButton('asset_toolbarSubset', {
				title : 'subset',
				cmd : 'subset',
			});
			ed.addButton('asset_toolbarDiamond', {
				title : 'diamond',
				cmd : 'diamond',
			});
			ed.addButton('asset_toolbarDivide', {
				title : 'divide',
				cmd : 'divide',
			});
			ed.addButton('asset_toolbarMultiply', {
				title : 'multiply',
				cmd : 'multiply',
			});
			ed.addButton('asset_toolbarParallel', {
				title : 'parallel',
				cmd : 'parallel',
			});
			ed.addButton('asset_toolbarPerpendicular', {
				title : 'perpendicular',
				cmd : 'perpendicular',
			});
			ed.addButton('asset_toolbarCongruent', {
				title : 'congruent',
				cmd : 'congruent',
			});
			ed.addButton('asset_toolbarNotequalto', {
				title : 'notequalto',
				cmd : 'notequalto',
			});
			ed.addButton('asset_toolbarBelongsto', {
				title : 'belongsto',
				cmd : 'belongsto',
			});
			ed.addButton('asset_toolbarNotbelongsto', {
				title : 'notbelongsto',
				cmd : 'notbelongsto',
			});
			ed.addButton('asset_toolbarUnion', {
				title : 'union',
				cmd : 'union',
			});
			ed.addButton('asset_toolbarIntersection', {
				title : 'intersection',
				cmd : 'intersection',
			});
			ed.addButton('asset_toolbarSimilarto', {
				title : 'similarto',
				cmd : 'similarto',
			});
			ed.addButton('asset_toolbarDegree', {
				title : 'degree',
				cmd : 'degree',
			});
			ed.addButton('asset_toolbarTherefore', {
				title: 'therefore',
				cmd: 'therefore',
			});
			ed.addButton('asset_toolbarBecause', {
				title: 'because',
				cmd: 'because',
			});
			
			ed.addButton('asset_toolbarAlmost_equal', {
				title: 'almost_equal',
				cmd: 'almost_equal',
			});
			ed.addButton('asset_toolbarSube', {
				title: 'sube',
				cmd: 'sube',
			});
			ed.addButton('asset_toolbarPhi', {
				title: 'phi',
				cmd: 'phi',
			});
			ed.addButton('asset_toolbarBallot_box', {
				title: 'ballot_box',
				cmd: 'ballot_box',
			});
			
			ed.addButton('asset_toolbarMaths', {
				title: 'maths',
				cmd: 'maths',
			});
			ed.addButton('asset_toolbarImg', {
				title: 'Imag',
				cmd: 'Imag',
			});
			//ed.addButton('preview', {title : 'preview.preview_desc', cmd : 'mcePreview'});
			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('asset_toolbar', n.nodeName == 'IMG');
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
				longname : 'Asset Toolbar',
				author : 'Educational Initiative Pvt Ltd ',
				authorurl : 'http://www.ei-india.com',
				infourl : 'http://www.ei-india.com',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('asset_toolbar', tinymce.plugins.asset);
})();