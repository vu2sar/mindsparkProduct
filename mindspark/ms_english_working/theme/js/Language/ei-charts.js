;(function (factory) {
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else if (typeof module === 'object' && typeof module.exports === 'object') {
		module.exports = factory(require('jquery'));
	} else {
		factory(jQuery);
	}
}(function($){

	var Chart = {
		getCenter : function(){
			return { x: this.canvas.width() / 2, y: this.canvas.height() / 2 };
		},
		getPieRadius : function(){
			return this.canvas.height() < this.canvas.width() ? this.canvas.height()/4 : this.canvas.width()/4;
		},
		getPieAppearance : function(appear){
			return ['pie','donut','donut-border','progress'].indexOf(appear) == -1 ? 'progress' : appear;
		},
		convertPercentToDeg : function(percent){
			return percent * 360 / 100;
		}

	};
       //fallback function older browser chrome not supporting object.assign        
if (typeof Object.assign != 'function') {
  Object.assign = function(target) {
    'use strict';
    if (target == null) {
      throw new TypeError('Cannot convert undefined or null to object');
    }

    target = Object(target);
    for (var index = 1; index < arguments.length; index++) {
      var source = arguments[index];
      if (source != null) {
        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }
    }
    return target;
  };
}
	var $PieChart = $.PieChart = function (el, options){
		var drawArea;
		drawArea = this;
		drawArea.canvas = $(el);

		this.parent = Object.assign({},Chart);
		var center = this.parent.getCenter();
		
		this._defaults = {
			background : '#fff',
			radius : this.parent.getPieRadius(),
			appearance : this.parent.getPieAppearance(), // Options pie, donut, donut_border, progress
			label: {
                        text: 'Good', // Text to be displayed in the label
                        position: 'in', // Options in or out of chart
                        pointer: 'none', // Options avaiable none, indicator  
                        textColor: '#69C30F', // Text color for the label
                        textPrefix : '',
                		textPostfix : '%'
                },
                showLegend:false,
			data : [{
				label : {
					text : '100',
					position : 'out', // Options in or out
					pointer : 'none' // Options avaiable none, indicator  
				},
				value : '50',
                color : '#ddd',
                borderColor : '#aaa',
                textColor : '#000',
                strokeColor : '#000',
                textPrefix : '',
                textPostfix : '%'
			}],
			fontSize : 14,
            fontFamily : 'Verdana, sans-serif',
            borderWidth : 3,
			borderShadow : 2,
			ledgends : {
				orientation : 'right' // Options available left, top, right, bottom
			},
			animate : true,
			duration : 1000
		};

		drawArea.init = function(){
	    	// Change user config.
	    	$.each(options , function(key, value){
	    		drawArea._defaults[key] = value;
	    	});
	   		this.set_chart_options();
	    	this.drawChart();
	    };
	    drawArea.drawChart = function(){
	    	switch (this._defaults.appearance){
	    		case 'donut_border':
	    			this.donut_border();
	    			break;

	    		case 'progress':
	    			this.progress();
	    			break;  
	    	}
	    }
	    drawArea.set_chart_options = function(){

	    };
	    drawArea.donut_border = function(){
	    	$(drawArea.canvas).drawRect({
	    		layer : true,
	    		fillStyle : this._defaults.background,
	    		x : center.x,
	    		y : center.y,
    			width : drawArea.canvas.width(),
	    		height : drawArea.canvas.height()
	    	});

	    	var startAng = 0;
	    	var endAng = 0;
	    	$.each(this._defaults.data, function(){
		    	endAng = startAng + drawArea.parent.convertPercentToDeg(this.value); 
		    	$(drawArea.canvas).drawArc({
		    		layer : true,
		    		strokeWidth : drawArea._defaults.borderWidth,
		    		strokeStyle : this.borderColor,
		    		x : center.x,
		    		y : center.y,
		    		radius : drawArea._defaults.radius,
		    		start : startAng + 2,
		    		end : endAng - 1
		    	});
		    	$(drawArea.canvas).drawSlice({
		    		layer : true,
		    		fillStyle   : this.color,
		    		x : center.x,
		    		y : center.y,
		    		radius : drawArea._defaults.radius,
		    		start : startAng + 2,
		    		end : endAng - 2
		    	});
		    	startAng = endAng;
	    	});
	    	$(drawArea.canvas).drawSlice({
	    		layer : true,
	    		fillStyle : this._defaults.background,
	    		x : center.x,
	    		y : center.y,
	    		radius : drawArea._defaults.radius/2.5,
	    		start : 0,
	    		end : 359,
	    		bringToFront : true
	    	});
	    	$(drawArea.canvas).drawText({
		    		layer : true,
		    		x : center.x,
		    		y : center.y,
		    		strokeWidth : 0,
		    		strokeStyle : this._defaults.label.textColor, 
		    		fillStyle : this._defaults.label.textColor, 
		    		fontSize: 16,
		    		fontFamily : 'Verdana, sans-serif', 
		    		text : this._defaults.label.text+this._defaults.label.textPostfix
		    	});
	    };
	    drawArea.progress = function(){
	    	var endAng = 0;
	    	$.each(this._defaults.data, function(){
	    		endAng = drawArea.parent.convertPercentToDeg(this.value); 
	    		$(drawArea.canvas).drawArc({
		    		layer : true,
		    		strokeWidth : 3,
		    		strokeStyle : this.strokeColor,
		    		x : center.x,
		    		y : center.y,
		    		radius : drawArea._defaults.radius,
		    		start : 0,
		    		end : 359
		    	});	
		    	$(drawArea.canvas).drawArc({
		    		layer : true,
		    		strokeWidth : drawArea._defaults.borderWidth,
		    		strokeStyle : '#fff',
		    		x : center.x,
		    		y : center.y,
		    		radius : drawArea._defaults.radius,
		    		start : 0,
		    		end : endAng
		    	});
		    	$(drawArea.canvas).drawArc({
		    		layer : true,
		    		strokeWidth : drawArea._defaults.borderWidth,
		    		strokeStyle : this.borderColor,
		    		x : center.x,
		    		y : center.y,
		    		radius : drawArea._defaults.radius,
		    		start : 5,
		    		end : endAng - 5
		    	});
		    	$(drawArea.canvas).drawText({
		    		layer : true,
		    		x : center.x,
		    		y : center.y,
		    		strokeWidth : 0,
		    		strokeStyle : this.borderColor, 
		    		fillStyle : this.borderColor, 
		    		fontSize: 16,
		    		fontFamily : 'Verdana, sans-serif', 
		    		text : this.label.text+this.textPostfix
		    		
		    	});
	    	});
	    };
	    drawArea.getLabels = function(){

	    }
	    this.init();
	};

	$.fn.eiPie = function(options){
		Chart.canvas = this;
		new $PieChart(this,options);
	}
	//return $eiKeyboard;
}));