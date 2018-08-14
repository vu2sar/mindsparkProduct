(function () {

	var Arrow = function(x,y,length,headdir){
		this.initialize(x,y,length,headdir);
	}

	var p = Arrow.prototype = new createjs.Container();

	p.Container_initialize = p.initialize;

	Arrow.prototype.initialize = function(x,y,length,headdir){
		this.Container_initialize();

		this.x = x;
		this.y = y;
		this.length = length;
		this.headdir = headdir;
		this.stroke = 1;
		this.x11 = this.x+this.length;
		this.y11 = this.y;
		this.x12 = this.x+this.length -5;
		this.y12 = this.y -5;
		this.x13 = this.x12;
		this.y13 = this.y+5;
		this.clr = '#000000';


		ashape = new createjs.Shape();
		if(this.headdir=='right'){
			ashape.graphics.ss(this.stroke,'round','round').s(this.clr).mt(this.x,this.y).lt(this.x11,this.y11).endStroke();
			ashape.graphics.f(this.clr).lt(this.x12,this.y12).lt(this.x13,this.y13).lt(this.x11,this.y).endStroke();
			}
		else if(this.headdir == 'left'){
			ashape.graphics.ss(this.stroke,'round','round').s(this.clr).mt(this.x+this.length,this.y).lt(this.x,this.y).endStroke();
			ashape.graphics.f(this.clr).lt(this.x+5,this.y-5).lt(this.x+5,this.y+5).lt(this.x,this.y).endStroke();
		}
		else if(this.headdir == 'top'){
			ashape.graphics.ss(this.stroke,'round','round').s(this.clr).mt(this.x,this.y+this.length).lt(this.x,this.y).endStroke();
			ashape.graphics.f(this.clr).lt(this.x+5,this.y+5).lt(this.x-5,this.y+5).lt(this.x,this.y).endStroke();
		}
		else if(this.headdir == 'bottom'){
			ashape.graphics.ss(this.stroke,'round','round').s(this.clr).mt(this.x,this.y).lt(this.x,this.y+this.length).endStroke();
			ashape.graphics.f(this.clr).lt(this.x+5,this.y+this.length-5).lt(this.x-5,this.y+this.length-5).lt(this.x,this.y+this.length).endStroke();
			}
		this.addChild(ashape);
	}

	window.Arrow = Arrow;
}());