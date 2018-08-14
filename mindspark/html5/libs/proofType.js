/*
	author: kalpesh
	version: x
*/
if(!(String.prototype.contains)) {
	String.prototype.contains = function(string, startIndex) {
		return ''.indexOf.call(this, string, startIndex) !== -1;
	};
}
var
	result = 2,
	userResponse = '',
	extraParameters = '',
	number_language,
	url_parameters,
	numDrops = 0,
	numInputs = 0,
	numSelects = 0,
	dropResult = 1,
	inputResult = 1,
	selectResult = 1,
	inputElements = [],
	selectElements = [],
	container_list = {};

function resizeApp() {
	var width_ratio = (window.innerWidth<$("body").width()) ? parseFloat(window.innerWidth/$("body").width()) : 1;
	var height_ratio = (window.innerHeight<$("body").height()) ? parseFloat(window.innerHeight/$("body").height()) : 1;
	var scale = (height_ratio<width_ratio) ? height_ratio : width_ratio;
	$('body').css({
		'-webkit-transform': 'scale('+scale+')',
		'-moz-transform': 'scale('+scale+')',
		'-ms-transform': 'scale('+scale+')',
		'-o-transform': 'scale('+scale+')',
		'transform': 'scale('+scale+')'
	});
}

window.onload = initialize_app;
function initialize_app() {
	url_parameters = getURLParameters();
	number_language = (url_parameters.hasOwnProperty('numberLanguage')) ? url_parameters['numberLanguage'] : 'english';
	loadXML('xml.xml', load_images);
}
function load_images() {
	var all_images = [];
	for(var key in miscArr) {
		if(miscArr.hasOwnProperty(key)) {
			if(key.contains('image')) {
				var number = parseInt(key.replace('image', ''));
				if(!isNaN(number) && number.toString().length == key.replace('image', '').length)
					all_images.push(miscArr[key]);
			}
		}
	}
	var image_loader = new PxLoader();
	if(all_images.length>0) {
		var i = all_images.length;
		while(i--) {
			image_loader.add(new PxLoaderImage('../assets/'+all_images[i]));
		}
		image_loader.addCompletionListener(initialize_game);
		image_loader.start();
	} else
		initialize_game();
}
function initialize_game() {
	fill_sections();
	fix_issues();
	if(url_parameters.hasOwnProperty('showAnswer')) {
		show_answer();
	} else {
		shuffle_options();
		create_event_handlers();
		show_sections();
		var drops = $('.drop');
		for(var i = 0; i < drops.length; i++) {
			if(!($(drops[i]).attr('shouldEvaluate') == 'no')) {
				numDrops++;
			}
		}
		var inputs = $('.textBox');
		for(var i = 0; i < inputs.length; i++) {
			if(!($(inputs[i]).attr('shouldEvaluate') == 'no')) {
				numInputs++;
			}
		}
		var selects = $('.dropDown');
		for(var i = 0; i < selects.length; i++) {
			if(!($(selects[i]).attr('shouldEvaluate') == 'no')) {
				numSelects++;
			}
		}
	}
	$('#preLoaderContainer').hide();
	$('#loading').hide();
	$('#gameContainer').show();
	align_clones();
	var evaluated_elements = $('input[shouldEvaluate="yes"],select[shouldEvaluate="yes"],.drop[shouldEvaluate="yes"]');
}
function fix_issues() {
	$('body').html($('body').html().replace(/<\/span>&nbsp;/g, '</span>').replace(/&nbsp;/g, ' ').replace(/<\/span> \./g, '</span>.'));
	$('.selected').removeClass('selected');
	$('*:not("input,select,.drop")').removeAttr('shouldEvaluate');
	$('input,select,.drop').each(function() {
		if($(this).attr('shouldEvaluate')!='no')
			$(this).attr('shouldEvaluate', 'yes');
	})
	$('.fractionBottom:not("td")').removeClass('fractionBottom');
	$('[id^="container"]').hide().addClass('mainContainer').css({'overflow-y':'auto'});
}
function show_answer() {
	$('.drop').each(function() {
		$(this).html($('#'+$(this).attr('rightanswer')).html());
		$(this).css({
			'background-color': 'white',
			'color': 'black',
			'width': 'auto',
			'padding': '0px 4px',
			'line-height': 'normal'
		});
	});
	$('#optionArea').html('');
	$('select').each(function() {
		$(this).attr('disabled','disabled').find('option[value="'+$(this).attr('rightanswer')+'"]').attr('selected','selected');
	});
	$('input').each(function() {
		$(this).attr('disabled','disabled').val($(this).attr('rightanswer'));
	});
	$('[id^="container"]').show();
}
function shuffle_options() {
	$('select').each(function() {
		$(this).find('option:nth-child(1)').remove();
		for(var i = this.children.length; i>=0; i--) {
			this.appendChild(this.children[Math.random() * i | 0]);
		}
		this.selectedIndex = -1;
	});
	var optArea = $('#optionArea')[0];
	if($('#optionArea').length==1) {
		for(var i = optArea.children.length; i>=0; i--) {
			optArea.appendChild(optArea.children[Math.random() * i | 0]);
		}
	}
}
function show_sections() {
	get_container_list();
	var container_keys = Object.keys(container_list);
	for(var i=0; i<container_keys.length; i++) {
		if(i==0) {
			$('#container'+container_keys[i]).show();
		}
		if($('#container'+container_keys[i]).find('input,select,.drop').length==0)
			$('#container'+container_keys[i+1]).show();
		else
			break;
	}
}
function fill_sections() {
	fillHTML($('table'));
	fillHTML($('p'));
	fillHTML($('.options'));
}
function fillHTML(array) {
	array.each(function() {
		if(this.id != undefined)
			$(this).html(replaceDynamicText(quesArr[this.id], number_language, ''));
	});
}
var pointerLocationOnElement = {};
var offsetLeftOption;
var offsetTopOption;
var comingFromADroppedEvent = false;
function create_event_handlers() {
	// blanks & dropdowns
	var inputElementsTemp = $('input[type="text"]');
	var selectElementsTemp = $('select');
	for(var i = 0; i < inputElementsTemp.length; i++) {
		if(!($(inputElementsTemp[i]).attr('shouldEvaluate') == 'no')) {
			$(inputElementsTemp[i]).blur(checkInputResult);
			inputElements.push(inputElementsTemp[i]);
		}
	}
	for(var i = 0; i < selectElementsTemp.length; i++) {
		if(!($(selectElementsTemp[i]).attr('shouldEvaluate') == 'no')) {
			$(selectElementsTemp[i]).bind('change', checkSelectResult);
			selectElements.push(selectElementsTemp[i]);
		}
	}
	$(document).delegate('input,select','click',function() {
		$('.selected').removeClass('selected');
	});
	$(document).delegate('select','change',function() {
		if($(this).val()!=='0')
			look_for_next_section($(this).closest('[id^="container"]'));
	});
	$(document).delegate('input','keydown',function(event) {
		$('.selected').removeClass('selected');
		if(event.which==13 && $(this).attr('value')!='') {
			checkInputResult();
			$(this).blur();
		}
	});
	$(document).delegate('input','blur',function() {
		$('.selected').removeClass('selected');
		look_for_next_section($(this).closest('[id^="container"]'));
	});

	$('.drop').droppable({
		drop : function(event, ui) {
			var x = $('.options');
			for(var i = 0; i < x.length; i++) {
				if($(x[i]).attr('droppedon') == this.id && x[i].id != ui.draggable[0].id) {
					$(this).removeClass('highlightWrong');
					$(this).removeClass('highlightRight');
					$(x[i]).removeAttr('droppedon');
					$(x[i]).removeClass('dropped');
					$(x[i]).appendTo($('#optionArea'));
					$(x[i]).removeAttr('style');
				}
			}
			$(ui.draggable).attr('droppedon', this.id);


			pointerLocationOnElement.x = event.originalEvent.clientX - $(ui.draggable).offset().left - 2;
			pointerLocationOnElement.y = event.originalEvent.clientY - $(ui.draggable).offset().top - 2;

			var option_html=$(ui.draggable).html();
			this.innerHTML = '';
			
			$(this).css('width', '200px');
			$(ui.draggable).appendTo($(this)).html(option_html);
			checkDropResult();
			$(ui.draggable).removeAttr('style');
			$(ui.draggable).css({
				'margin' : '-1px auto 0px -1px',
				'position' : 'relative',
				'vertical-align' : 'top',
				'width': 'auto',
				'padding': '0px 5px',
			});
			$(this).css('width', 'auto');
			look_for_next_section($(this).closest('[id^="container"]'));
			$('.selected').removeClass('selected');
			align_clones();
		},
		out : function(event, ui) {
			$(this).removeClass('highlightWrong');
			$(this).removeClass('highlightRight');
			if($(ui.draggable).attr('droppedon') == '' || $(ui.draggable).attr('droppedon') == undefined) {
				checkDropResult();
				//align_clones();
				return;
			}
			var option_html=$(ui.draggable).html();
			this.innerHTML = '@blank@';
			$(this).css({'width':'80px'});
			$(ui.draggable).removeAttr('droppedon');
			$(ui.draggable).removeClass('dropped');
			$(ui.draggable).appendTo($('#optionArea')).html(option_html);
			$(ui.draggable).removeAttr('style');

			$('.selected').removeClass('selected');
			if($(ui.draggable).hasClass('clones')) {
				offsetLeftOption = 0;
				offsetTopOption = 0;
			} else {
				offsetLeftOption = $(ui.draggable).offset().left;
				offsetTopOption = $(ui.draggable).offset().top;
			}
			
			$(ui.draggable).css({
				'left' : event.originalEvent.clientX - pointerLocationOnElement.x - offsetLeftOption,
				'top' : event.originalEvent.clientY - pointerLocationOnElement.y - offsetTopOption
			});

			checkDropResult();
			comingFromADroppedEvent = true;
			
			if(!($(ui.draggable).hasClass('clones')))
				align_clones();
		},
		tolerance : 'pointer'
	});

	$('.options').draggable({
		containment : '#gameContainer',
		start : function(event, ui) {
			$('.selected').removeClass('selected');
			if($(this).hasClass('clone')) {
				$(this).css('opacity', 1);
			}
			pointerLocationOnElement.x = event.originalEvent.clientX - $(this).offset().left - 2;
			pointerLocationOnElement.y = event.originalEvent.clientY - $(this).offset().top - 2;
		},
		revert : function(event, ui) {
			if(!event) {
				$(this).removeAttr('style');

				align_clones();
			}
		},
		drag : function(event, ui) {
			if(comingFromADroppedEvent) {
				ui.position = {
					'left' : event.originalEvent.clientX - pointerLocationOnElement.x - offsetLeftOption,
					'top' : event.originalEvent.clientY - pointerLocationOnElement.y - offsetTopOption
				};
			}
		},
		stop : function(event, ui) {
			comingFromADroppedEvent = false;
		}
	});
	$(document).delegate('#optionArea>.options','click',function() {
		$('.selected').removeClass('selected');
		$(this).addClass('selected');
	});
	$(document).delegate('.drop>.options','click',function() {
		if($('.selected').length!==1)
			return;
		var drop_span=$(this).parent();
		drop_span.removeClass('highlightWrong');
		drop_span.removeClass('highlightRight');
		$(this).removeAttr('droppedon');
		$(this).removeClass('dropped');
		$(this).appendTo($('#optionArea'));
		$(this).removeAttr('style');
		$(this).css({
			'width': 'auto',
			'padding': '0px 5px'
		});
		var selected_option=$('.selected')[0];
		$(selected_option).attr('droppedon', drop_span[0].id);


		// pointerLocationOnElement.x = event.originalEvent.clientX - $(selected_option).offset().left - 2;
		// pointerLocationOnElement.y = event.originalEvent.clientY - $(selected_option).offset().top - 2;

		drop_span.html('');

		$(selected_option).appendTo(drop_span);
		checkDropResult();
		$(selected_option).removeAttr('style');
		$(selected_option).css({
			'margin' : '-1px auto 0px -1px',
			'position' : 'relative',
			'vertical-align' : 'top',
			'width': 'auto',
			'padding': '0px 5px'
		});
		drop_span.css('width', 'auto');
		look_for_next_section(drop_span.closest('[id^="container"]'));
		$('.selected').removeClass('selected');
		align_clones();
	});
	$(document).delegate('.drop','click',function() {
		if($('.selected').length!==1)
			return;
		var selected_option=$('.selected')[0];
		var x = $('.options');
		for(var i = 0; i < x.length; i++) {
			if($(x[i]).attr('droppedon') == this.id && x[i].id != selected_option.id) {
				$(this).removeClass('highlightWrong');
				$(this).removeClass('highlightRight');
				$(x[i]).removeAttr('droppedon');
				$(x[i]).removeClass('dropped');
				$(x[i]).appendTo($('#optionArea'));
				$(x[i]).removeAttr('style');
			}
		}
		$(selected_option).attr('droppedon', this.id);


		// pointerLocationOnElement.x = event.originalEvent.clientX - $(selected_option).offset().left - 2;
		// pointerLocationOnElement.y = event.originalEvent.clientY - $(selected_option).offset().top - 2;

		this.innerHTML = '';

		$(selected_option).appendTo($(this));
		checkDropResult();
		$(selected_option).removeAttr('style');
		$(selected_option).css({
			'margin' : '-1px auto 0px -1px',
			'position' : 'relative',
			'vertical-align' : 'top',
			'width': 'auto',
			'padding': '0px 5px'
		});
		$(this).css('width', 'auto');

		look_for_next_section($(this).closest('[id^="container"]'));
		$('.selected').removeClass('selected');
		align_clones();
	});
}

function get_container_list() {
	$('[id^="container"]').each(function() {
		var this_container_id = $(this).attr('id').replace('container','');
		container_list[this_container_id] = 'This is container list';
	});
}

function look_for_next_section(current_container) {
	var current_container_id = current_container.attr('id').replace('container','');
	var container_keys = Object.keys(container_list);
	var next_container_key = container_keys.indexOf(current_container_id)+1;
	if(next_container_key<container_keys.length)
		var next_container = $('#container'+container_keys[next_container_key]);
	else
		return;
	if(next_container.css('display')!=='none')
		return;
	if(current_container.find('input,select,.drop').length===0) {
		next_container.show();
		look_for_next_section(next_container);
	} else {
		var showNextContainer = 'yes';
		current_container.find('input').each(function() {
			if($(this).val()==='') {
				showNextContainer='no';
				return false;
			}
		});
		if(showNextContainer=='yes') {
			current_container.find('select').each(function() {
				if(this.selectedIndex==-1) {
					showNextContainer='no';
					return false;
				}
			});
		}
		if(showNextContainer=='yes') {
			current_container.find('.drop').each(function() {
				if($(this).find('.options').length===0) {
					showNextContainer='no';
					return false;
				}
			});
		}
		if(showNextContainer=='yes') {
			next_container.show();
			look_for_next_section(next_container);
		}
	}
}

function checkInputResult() {
	inputResult = 0;
	var inter = 0;
	for(var i = 0; i < inputElements.length; i++) {
		var element = $(inputElements[i]);
		element.removeClass('highlightWrong');
		element.removeClass('highlightRight');
		if(element.attr('value') == element.attr('rightAnswer')) {
			if(element.attr('shouldEvaluate') != 'no'){
				inter++;
			}
			element.addClass('highlightRight');
		}
		else{
			element.addClass('highlightWrong');
		}
		if(element.attr('value') == ''){
			element.removeClass('highlightWrong');
			element.removeClass('highlightRight');
		}
	}

	if(inter == numInputs) {
		inputResult = 1;
	} else {
		inputResult = 0;
	}

	checkOverall();
}

function checkSelectResult(e) {
	selectResult = 0;
	var inter = 0;
	var current_select_element = $(e.target);
	if(current_select_element.find('option:selected').val() == current_select_element.attr('rightAnswer')){
		current_select_element.removeClass('highlightWrongSelect');
		current_select_element.addClass('highlightRightSelect');
	} else if(current_select_element.val()=='0') {
		current_select_element.removeClass('highlightRightSelect highlightWrongSelect');
	} else{
		current_select_element.removeClass('highlightRightSelect');
		current_select_element.addClass('highlightWrongSelect');
	}
	
	for(var i = 0; i < selectElements.length; i++) {
		var element = $(selectElements[i]);
		if(element.attr('value')==element.attr('rightAnswer')) {
			inter++;
		}
	}
	if(inter == numSelects) {
		selectResult = 1;
	} else {
		selectResult = 0;
	}

	checkOverall();
}

function checkDropResult() {
	var droppables = $('.drop');
	var rights = 0;
	dropResult = 0;
	userResponse = '';

	var options = $('.options');

	for(var i = 0; i < options.length; i++) {
		var dropElement = $("#" + $(options[i]).attr('droppedon'));
		if(!($(options[i]).attr('droppedon') == '' || $(options[i]).attr('droppedon') == undefined )) {

			var rightAnswer = dropElement.attr('rightAnswer');
			userResponse += $('#' + rightAnswer).html() + ':' + options[i].innerHTML + '|';
			if(rightAnswer == $(options[i]).attr('reference') || $(options[i]).html()==$('#' + rightAnswer).html()) {
				if(!(dropElement.attr('shouldEvaluate') == 'no'))
					rights++;
				dropElement.addClass('highlightRight');
			} else {
				dropElement.addClass('highlightWrong');
			}
		} else {
		}
	}

	if(rights == numDrops) {
		dropResult = 1;
	} else {
		dropResult = 0;
	}

	userResponse = userResponse.replace(/\n/g, '');
	userResponse += (rights / numDrops);

	if(userResponse == '0') {
		dropResult = 2;
	}

	checkOverall();
}

function checkOverall() {
	var all_filled='yes';
	$('.drop').each(function(index,element) {
		if($(this).attr('shouldEvaluate')!='no' && $(this).find('.options').length==0) {
			all_filled='no';
			return false;
		}
	});
	$('input').each(function() {
		if($(this).attr('shouldEvaluate')!='no' && $(this).val()==='') {
			all_filled='no';
			return false;
		}
	});
	$('select').each(function() {
		if($(this).attr('shouldEvaluate')!='no' && $(this).val()==0) {
			all_filled='no';
			return false;
		}
	});
	if(all_filled=='no') {
		result=2;
		return;
	}
	if(dropResult == 1 && inputResult == 1 && selectResult == 1)
		result = 1;
	else
		result = 0;
}

function align_clones(toAlign) {
	var callAgain = false;
	if(toAlign == undefined) {
		var clones = $('.clones');
		for(var i = 0; i < clones.length; i++) {
			if($(clones[i]).attr('droppedon') == '' || $(clones[i]).attr('droppedon') == undefined) {
				var original_option = $('#' + $(clones[i]).attr('reference'));
				if(original_option.attr('droppedon') == '' || original_option.attr('droppedon') == undefined) {
					$(clones[i]).css('left', original_option.position().left + 'px');
					$(clones[i]).css('top', original_option.position().top + 'px');
				} else {
					//setting the clone to original_option position and throwing out the original_option instead to option Area
					$(clones[i]).attr('droppedon', original_option.attr('droppedon'));

					var toDrop = $('#' + $(clones[i]).attr('droppedon'));

					var top = toDrop.offset().top - 2;
					var left = toDrop.offset().left + ((toDrop.width() + 2) / 2) - (($(clones[i]).width() + 2) / 2) - 2;

					$(clones[i]).appendTo(toDrop);
					$(clones[i]).removeAttr('style');
					$(clones[i]).css({
						'margin' : '-1px auto 0px -1px',
						'position' : 'relative',
						'vertical-align' : 'top',
						'width': 'auto',
						'padding': '0px 5px'
					});
					original_option.removeAttr('style');
					original_option.css('width', 'auto');
					original_option.attr('droppedon', '');
					$(original_option).appendTo($('#optionArea'));
					checkDropResult();
				}
			}
		}

		if(callAgain) {
			align_clones();
		}
	} else {
		for(var i = 0; i < toAlign.length; i++) {
			$(toAlign[i]).css('top', $('#' + $(toAlign[i]).attr('reference')).position().top + 'px');
		}
	}
}