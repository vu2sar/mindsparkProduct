(function(){

	var CustomShape = function(animation,showAns){
		this.initialize(animation,showAns);
	}

	var p = CustomShape.prototype = new createjs.Container();

	p.Container_initialize = p.initialize;

	p.lineArr = new Array();

	p.facesArr = new Array();

	p.vertexArr = new Array();

	p.lineShapeArr = new Array();

	p.vertexShapeArr = new Array();

	p.facesShapeArr = new Array();

	CustomShape.prototype.initialize = function(animation,showAns){

		this.x = 10;
		this.y = 10;
		var borderColor = 'rgba(0,0,0,1)';
		this.brdClr = '#ff000f';
		var obj = shapejson1;

		this.animation = animation;
		this.showAns = showAns;
		this.indxtext = new createjs.Text("", "16px Arial Bold", "#000000");
		this.addChild(this.indxtext);
		this.indxtext.visible = false;
		
		// var faces = obj.
		for (var j = obj.faces.length - 1; j >= 0; j--) {
		
		var arr = obj.faces[j].sides;

		shape  = new createjs.Shape();
		
		shapeg = shape.graphics;

		for(var i  = 0; i < arr.length; i++ ){
			var ends = arr[i].ends;
			if(arr[i].isdotted)
				shapeg.dashedLineTo(ends[0].x,ends[0].y,ends[1].x,ends[1].y,7,borderColor);
			else
				shapeg.ss(1,'round','round').s('#000000').mt(ends[0].x,ends[0].y).lt(ends[1].x,ends[1].y);

			this.addEndsToArray(ends);

			this.addVertextToArray(ends[0]);
			this.addVertextToArray(ends[1]);
		}
			this.facesArr.push(obj.faces[j]);
			this.addChild(shape);
		}

		if(this.animation=='vertex'){
			this.drawVertexFromArray();
			this.indxtext.visible = showAns;
			this.animateVertex();
		}else if(this.animation=='edge'){
			this.drawLinesFromArray();
		//	this.index=1;
			this.indxtext.visible = showAns;
			
			lineIndx = 0;
			this.displayLines();
		}else if(this.animation=='face'){
			this.drawFacesFromArray();
			this.animateFaces();
			this.indxtext.visible = showAns;
		}	
	}

	p.drawLinesFromArray = function(){
		for (var j = 0; j < this.lineArr.length; j++) {

			var ends = this.lineArr[j];

			var lShape = new createjs.Shape();

			lShape.graphics.ss(2,'round','round').s(this.brdClr).mt(ends[0].x,ends[0].y).lt(ends[1].x,ends[1].y);
			
			lShape.visible = false;

			this.addChild(lShape);

			this.lineShapeArr.push(lShape);
		}
	}

	p.drawVertexFromArray = function(){
		for (var j = 0; j < this.vertexArr.length; j++) {
			 var obj1 = this.vertexArr[j];

			 var vShape = new createjs.Shape();

			 vShape.graphics.beginFill(this.brdClr).drawCircle(obj1.x,obj1.y,5);

			 vShape.visible = false;

			 this.addChild(vShape);

			 this.vertexShapeArr.push(vShape);
		}
	}

	p.drawFacesFromArray = function(){

		var shapeColor = "rgba(83,212,255,0.5)";
		for (var j = 0; j < this.facesArr.length; j++) {

        	 var vShape = new createjs.Shape();
			 
			 var obj1 = this.facesArr[j];
			 if(!this.faceAreaArray)
			 	this.faceAreaArray = [];

			 var maxX=0, maxY=0,minX=1000,minY=1000;
			 var arr = obj1.sides;
			 var initialx,initialy;
			for(var i  = 0; i < arr.length; i++ ){
				var ends = arr[i].ends;
				maxX = maxX>ends[0].x?maxX:ends[0].x;
				maxY =maxY>ends[0].y?maxY:ends[0].y;
				minX = minX<ends[0].x?minX:ends[0].x;
				minY =minY<ends[0].y?minY:ends[0].y;

				if(i==0){
					initialx = ends[0].x;
					initialy = ends[0].y;

					vShape.graphics.ss(2,'round','round').s(this.brdClr).f(shapeColor).mt(initialx,initialy).lt(ends[0].x,ends[0].y).lt(ends[1].x,ends[1].y);
				}
				else{
					vShape.graphics.lt(ends[0].x,ends[0].y).lt(ends[1].x,ends[1].y);
				}
				
				initialx = ends[1].x;
				initialy = ends[1].y;		
			}

			 vShape.visible = false;

			 this.addChild(vShape);

			 this.facesShapeArr.push(vShape);
			 				this.faceAreaArray.push(
						{
							x1:minX,
							y1:minY,
							x2:maxX,
							y2:maxY
						}
					);
		}

	}

	lineIndx = 0;

	p.displayLines = function(){

		if(lineIndx < this.lineShapeArr.length){
			var that = this;


		this.lineShapeArr[lineIndx].visible = true;
						var ends = that.lineArr[lineIndx];
						var textX = ends[0].x +(ends[1].x-ends[0].x)/2 + ((ends[1].x-ends[0].x) == 0 ? 2 : 0 );
						var textY = ends[0].y +(ends[1].y-ends[0].y)/2 + ((ends[1].y-ends[0].y) == 0 ? 2 : 0 );
						that.indxtext.text = "" + (lineIndx+1);
				        that.indxtext.y=textY;
				        that.indxtext.x=textX;
				        console.log(ends);
				        //console.log(this.lineShapeArr[lineIndx]);
				        console.log(this.indxtext);
		
		createjs.Tween.get(this.lineShapeArr[lineIndx],{loop:false},true).wait(200)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5)).
					call(function(){
						//this.removeChild(text);
		
				 
		
						
						lineIndx++;
						that.displayLines();

					});
		}
		else{
			this.index=1;
			this.onAnimationComplete();
		}
	}

	vertIndx = 0;
	p.animateVertex = function(){
		if(vertIndx < this.vertexShapeArr.length){
			var that = this;
		this.vertexShapeArr[vertIndx].visible = true;
		var textX = this.vertexArr[vertIndx].x+2;
						var textY = this.vertexArr[vertIndx].y+2;
						that.indxtext.text = "" + (vertIndx+1);
				        that.indxtext.y=textY;
				        that.indxtext.x=textX;
		createjs.Tween.get(this.vertexShapeArr[vertIndx],{loop:false},true).wait(200)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5)).
					call(function(){
						vertIndx ++;
						that.animateVertex();
					});
		}
		else{
			
			 this.onAnimationComplete();
		}
	}

	faceIndx = 0;

	p.animateFaces = function(){
		if(faceIndx < this.facesShapeArr.length){
			var that = this;
		this.facesShapeArr[faceIndx].visible = true;
		var textX =this.faceAreaArray[faceIndx].x1 + (this.faceAreaArray[faceIndx].x2 - this.faceAreaArray[faceIndx].x1)/2 ;
		var textY =this.faceAreaArray[faceIndx].y1 + (this.faceAreaArray[faceIndx].y2 - this.faceAreaArray[faceIndx].y1)/2;
		that.indxtext.text = "" + (faceIndx+1);
        that.indxtext.y=textY;
        that.indxtext.x=textX;
		createjs.Tween.get(this.facesShapeArr[faceIndx],{loop:false},true).wait(200)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.1},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.1},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.1},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.1},200,createjs.Ease.get(5))	
					.wait(100)
					.to({alpha:0.8},200,createjs.Ease.get(5))
					.wait(100)
					.to({alpha:0.0},200,createjs.Ease.get(5)).
					call(function(){
						faceIndx ++;
						that.animateFaces();
					});
		}
		else{
			this.onAnimationComplete();
		}
	}

	p.addEndsToArray  = function(ends){
		var isPresent = false;
		for (var j = this.lineArr.length - 1; j >= 0; j--) {
			ends1 = this.lineArr[j];
			if((ends1[0].x == ends[0].x && ends1[0].y == ends[0].y && ends1[1].x == ends[1].x && ends1[1].y == ends[1].y) ||
				(ends1[1].x == ends[0].x && ends1[1].y == ends[0].y && ends1[0].x == ends[1].x && ends1[0].y == ends[1].y)){
				isPresent = true;
			}
		}

		if(!isPresent)
			this.lineArr.push(ends);
	}

	p.addVertextToArray = function(vertex){
		var isPresent = false;
		for (var j = this.vertexArr.length - 1; j >= 0; j--) {
			vertex1 = this.vertexArr[j];

			if(vertex1.x == vertex.x && vertex1.y == vertex.y)
				isPresent = true;
		}

		if(!isPresent)
			this.vertexArr.push(vertex);
	}

	p.onAnimationComplete = function(){
		bitmap.visible = true;
	}

	p.replayAnimation = function(){
		lineIndx = 0;
		vertIndx = 0;
		faceIndx = 0;

		if(this.animation=='vertex'){
			
			this.animateVertex();
		}else if(this.animation=='edge'){
			
			this.displayLines();
		}else if(this.animation=='face'){
			
			this.animateFaces();
		}	

	}


createjs.Graphics.prototype.dashedLineTo = function(x1, y1, x2, y2, dashLen,stroke) {

    this.moveTo(x1, y1);
   	 
    var dX = x2 - x1;
    var dY = y2 - y1;
    var dashes = Math.floor(Math.sqrt(dX * dX + dY * dY) / dashLen);
    var dashX = dX / dashes;
    var dashY = dY / dashes;
    
    var q = 0;
    while (q++ < dashes) {
        x1 += dashX;
        y1 += dashY;
       this[q % 2 == 0 ? 'moveTo' : 'lineTo'](x1, y1);
    }
    
    this[q % 2 == 0 ? 'moveTo' : 'lineTo'](x2, y2); 
}


	window.CustomShape = CustomShape;
}());