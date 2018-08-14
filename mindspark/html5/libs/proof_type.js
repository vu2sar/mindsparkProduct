var result = 2, userResponse = '', extraParameters = '';
var GET_parameters = getURLParameters();
var number_language = /^(english|hindi|gujarati)$/.test(GET_parameters.numberLanguage) ? GET_parameters.numberLanguage : 'english';
var flow_order = [], current_flow_index;
$(function() {
	loadXML('xml.xml', load_images);
});
function load_images() {
	var images = quesArr.join('').match(/@#image:.*?#@/g);
	var loader = new PxLoader();
	if(images) {
		var i = images.length;
		while(i--)
			loader.add(new PxLoaderImage('../assets/'+images[i].replace(/(@#image:)|(#@)/g, '')));
		loader.addCompletionListener(fill_proof);
		loader.start();
	} else {
		fill_proof();
	}
}
function fill_proof() {
	for(var key in quesArr)
		if(quesArr.hasOwnProperty(key) && key.indexOf('section')==0)
			$('#'+key).html(replaceDynamicText(quesArr[key].replace(/&lt;/g,'<').replace(/&gt;/g,'>'), number_language, ''));
	$('#container').html(parse_elements($('#container').html()));
	create_draggables();
	initialize();
}
function parse_elements(element_text) {
	return element_text
	.replace(/@#image:(\w+\.\w+)(\|(\d+))?#@/g, function(match, capture_1, capture_2, capture_3) {
		return '<img src="../assets/'+capture_1+'"'+(typeof capture_2=='undefined' ? '' : ' style="height: '+capture_3+'px;"')+'>';
	})
	.replace(/@#blank(\!?):(.*?)#@/g, function(match, capture_1, capture_2) {
		return '<input class="question" answer="'+capture_2.replace(/"/g,'&quot;')+'"'+(capture_1=='!' ? '' : ' score=""')+' />';
	})
	.replace(/@#menu(\!?):(.*?)#@/g, function(match, capture_1, capture_2) {
		var menu_options = capture_2.split(',');
		var answer = menu_options[0].replace(/"/g,'&quot;');
		menu_options.forEach(function(value, key) {
			menu_options[key] = '<option value="'+value.replace(/"/g, '&quot;')+'">'+value+'</option>';
		});
		return '<select class="question" answer="'+answer+'"'+(capture_1=='!' ? '' : ' score=""')+'>'+menu_options.join('')+'</select>';
	})
	.replace(/@#drag(\!?):(.*?)#@/g, function(match, capture_1, capture_2) {
		return '<div class="question" answer="'+capture_2.replace(/"/g,'&quot;')+'"'+(capture_1=='!' ? '' : ' score=""')+'></div>';
	})
	.replace(/@#fraction:(.*?)#@/g, function(match, capture_1) {
		return '<div class="fraction">'+
			'<div class="numerator">'+capture_1.split(',')[0]+'</div>'+
			'<div class="vinculum">/</div>'+
			'<div class="denominator">'+capture_1.split(',')[1]+'</div>'+
		'</div>';
	})
	;
}
function initialize() {
	if(GET_parameters.hasOwnProperty('showAnswers'))
		show_answers();
	else {
		$('.distractor').removeClass('distractor');
		$('.section').hide().each(function() {
			var this_flow_number = Number(this.getAttribute('flow_number'));
			if(flow_order.indexOf(this_flow_number)<0)
				flow_order.push(this_flow_number);
		});
		flow_order.sort(function(a, b) {
			return a-b;
		});
		shuffle_options();
		handle_events();
		$('.section[flow_number="'+flow_order[current_flow_index = 0]+'"]').show();
		$('.question').attr('response', '');
	}
	$('#loading').remove();
	$('#container').show();
}
function create_draggables() {
	$('div.question').each(function() {
		var option_content = this.getAttribute('answer');
		var existing_option_shelf = $('#option_box>.option_shelf[content="'+option_content.replace(/"/g, '\\"')+'"]');
		if(existing_option_shelf.length==0)
			$('<div class="option_shelf" instances="1"></div>').appendTo('#option_box').attr('content', option_content);
		else
			existing_option_shelf.attr('instances', Number(existing_option_shelf.attr('instances'))+1);
	});
	for(var key in quesArr)
		if(quesArr.hasOwnProperty(key) && key.indexOf('distractor')==0) {
			$('<div class="option_shelf distractor" instances="'+key.split('#')[1]+'"></div>').appendTo('#option_box').attr('content', replaceDynamicText(quesArr[key].replace(/&lt;/g,'<').replace(/&gt;/g,'>'), number_language, ''));
		}
	$('.option_shelf').each(function() {
		$('<div class="space_holder"></div>').appendTo(this).html(this.getAttribute('content'));
		var i = $(this).attr('instances');
		while(i--)
			$('<div class="drag_option"></div>').appendTo(this).html(this.getAttribute('content'));
	});
}
function show_answers() {
	$('input.question').each(function() {
		this.value = this.getAttribute('answer');
		this.disabled = true;
	});
	$('select.question').each(function() {
		var answer = this.getAttribute('answer');
		$(this).children().each(function() {
			if($(this).is('option[value="'+answer.replace(/"/g, '\\"')+'"]')) {
				this.selected = true;
				return false;
			}
		});
		this.disabled = true;
	});
	$('div.question').each(function() {
		var question = $(this);
		$('.option_shelf').each(function() {
			if($(this).is('[content="'+question.attr('answer')+'"]')) {
				$(this).children(':last-child').appendTo(question);
				return false;
			}
		});
	});
	$('.option_shelf:not(.distractor)').remove();
}
function shuffle_options() {
	$('select.question').each(function() {
		for(var i = this.children.length; i>=0; i--) {
			this.appendChild(this.children[Math.random()*i | 0]);
		}
		this.selectedIndex = -1;
	});
	if($('#option_box').length==1) {
		var optArea = $('#option_box')[0];
		if(optArea.children.length>0){
			for(var i = optArea.children.length; i>=0; i--) {
				optArea.appendChild(optArea.children[Math.random()*i | 0]);
			}
		}
	}
}
function handle_events() {
	$('input.question').on('blur', function() {
		evaluate($(this).attr('response', this.value));
	}).on('keydown', function(event) {
		if(event.keyCode==13) {
			$(this).trigger('blur');
		}
	});
	$('select.question').on('change', function() {
		evaluate($(this).attr('response', $(this).children(':selected').html().replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g, '&')));
	});
	$('input.question,select.question').on('click', function() {
		$('.clicked_option').removeClass('clicked_option');
	});
	$('.drag_option').draggable({
		addClasses: false,
		containment: '#container',
		revert: 'invalid',
		revertDuration: 0,
		start: function(event, ui) {
			$('.clicked_option').removeClass('clicked_option');
		},
		stop: function(event, ui) {
			$(this).css({
				'left': '',
				'top': '',
			});
			$('.clicked_option').removeClass('clicked_option');
		},
		zIndex: 5,
	}).on('click', function() {
		if(!$(this).parent().is('.option_shelf'))
			return;
		$('.clicked_option').removeClass('clicked_option');
		$(this).addClass('clicked_option');
	}).addClass('movable');
	$('div.question').droppable({
		accept: '.drag_option',
		addClasses: false,
		drop: function(event, ui) {
			fill_drag_in_drop(ui.draggable, $(this));
		},
	}).on('click', function() {
		if($('.clicked_option').length==0)
			return;
		fill_drag_in_drop($('.clicked_option'), $(this));
	});
	$('#container:not(div.question)').droppable({
		accept: 'div.question>.drag_option',
		addClasses: false,
		drop: function(event, ui) {
			evaluate(ui.draggable.parent().attr('response', ''));
			$('.option_shelf').each(function() {
				if($(this).is('[content="'+ui.draggable.html().replace(/"/g, '\\"')+'"]')) {
					ui.draggable.appendTo(this);
					return false;
				}
			});
		},
	});
}
function fill_drag_in_drop(option, droppable) {
	var old_option = droppable.children('.drag_option');
	if(old_option.length>0) {
		$('.option_shelf').each(function() {
			if($(this).is('[content="'+old_option.html().replace(/"/g, '\\"')+'"]')) {
				old_option.appendTo(this);
				return false;
			}
		});
	}
	var old_parent = option.parent();
	if(old_parent.is('div.question'))
		evaluate(old_parent.attr('response', ''));
	var option_html = option.html();
	evaluate(droppable.append(option).attr('response', option_html));
}
function evaluate(question) {
	question.attr('score')!==undefined && question.attr('score', (
		question.attr('response')==='' ? '' : (
			question.attr('response')===question.attr('answer') ? 1 : 0
		)
	));
	update_result_and_userResponse();
	continue_flow();
}
function update_result_and_userResponse() {
	var responses = [];
	$('.question').each(function() {
		responses.push(this.getAttribute('response'));
	});
	userResponse = responses.join('|').replace(/<sup>(.*?)<\/sup>/g, '^$1');
	result = $('.question[score=""]').length ? 2 : ($('.question[score="0"]').length ? 0 : 1);
}
function continue_flow() {
	if($('.section[flow_number="'+flow_order[current_flow_index+1]+'"]').css('display')=='none' && $('.section[flow_number="'+flow_order[current_flow_index]+'"] .question[response=""]').length==0)
		$('.section[flow_number="'+flow_order[++current_flow_index]+'"]').show();
}
