function drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,angleValueString,sideValueString,rotateOffsetAngle,arcFlagString,angleColorString,sideColorString,sideLablelString){
	var ctx = document.getElementById(canvasID).getContext("2d");
	//alert($("#"+canvasID).attr("height"));
	//alert($("#"+canvasID).attr("width"));
	
	var stringArr = triangleName.split('');
	
	var temp1 = angleValueString.split('|');
	var ang1Value = temp1[0];
	var ang2Value = temp1[1];
	var ang3Value = temp1[2];
	
	var temp2 = sideValueString.split('|');
	var side1Value = temp2[0];
	var side2Value = temp2[1];
	var side3Value = temp2[2];
	
	var temp3 = arcFlagString.split('|');
	var drawArc1Flag = temp3[0];
	var drawArc2Flag = temp3[1];
	var drawArc3Flag = temp3[2];
	
	var temp4 = angleColorString.split('|');
	var angColor1 = temp4[0];
	var angColor2 = temp4[1];
	var angColor3 = temp4[2];
	
	var temp5 = sideColorString.split('|');
	var sideColor1 = temp5[0];
	var sideColor2 = temp5[1];
	var sideColor3 = temp5[2];
	
	var temp6 = sideLablelString.split('|');
	var side1Label = temp6[0];
	var side2Label = temp6[1];
	var side3Label = temp6[2];
	
	side1Label = side1Label.replace("sqrt","\u221A");
	side2Label =side2Label.replace("sqrt","\u221A");
	side3Label =side3Label.replace("sqrt","\u221A");
	
	var angleCount = 3;
	var sideCount = 3;
	var triangleType;
	var angDetected = new Array(1,1,1);
	var sideDetected = new Array(1,1,1);
	rotateOffsetAngle = rotateOffsetAngle * Math.PI / 180;
	
	if (ang1Value==""){
		angleCount--;
		angDetected[0] = 0;
	}
	else {
		ang1Value = ang1Value * Math.PI / 180;
	}		
	if (ang2Value==""){
		angleCount--;
		angDetected[1] = 0;
	}
	else {
		ang2Value = ang2Value * Math.PI / 180;
	}		
	if (ang3Value==""){
		angleCount--;
		angDetected[2] = 0;
	}
	else {
		ang3Value = ang3Value * Math.PI / 180;
	}	
		
	if (side1Value==""){
		sideCount--;
		sideDetected[0] = 0;
	}		
	if (side2Value==""){
		sideCount--;
		sideDetected[1] = 0;
	}		
	if (side3Value==""){
		sideCount--;
		sideDetected[2] = 0;
	}
	
	//alert(angDetected,sideDetected);	
	
	if (sideCount==3&&angleCount==0){
		triangleType = "SSS";
	}
	else if (sideCount==2&&angleCount==1){
		triangleType = "SAS";	
	}
	else if (angleCount==2&&sideCount==1){
		triangleType = "AAS";
	}
	else {
		alert("Error : Triangle not possible with given parameters");
		return false;
	}
	
	if (triangleType=="SSS"){
		ang1Value = Math.acos(((side2Value*side2Value)+(side3Value*side3Value)-(side1Value*side1Value))/(2*side2Value*side3Value));
		ang2Value = Math.acos(((side1Value*side1Value)+(side3Value*side3Value)-(side2Value*side2Value))/(2*side1Value*side3Value));
		ang3Value = Math.acos(((side2Value*side2Value)+(side1Value*side1Value)-(side3Value*side3Value))/(2*side2Value*side1Value));
	}

	else if (triangleType=="AAS") {
		if (angDetected[0]==1 && angDetected[1]==1){
			//ang1Value,ang2Value are known
			ang3Value = Math.PI - (ang1Value + ang2Value);
			if (sideDetected[2]==1){
				// side3Value is known
				side1Value = side3Value*(Math.sin(ang1Value)/Math.sin(ang3Value)); 
				side2Value = side3Value*(Math.sin(ang2Value)/Math.sin(ang3Value));
			}
			else if (sideDetected[0]==1) {
				// side1Value is known
				side3Value = side1Value*(Math.sin(ang3Value)/Math.sin(ang1Value)); 
				side2Value = side1Value*(Math.sin(ang2Value)/Math.sin(ang1Value));
			}
			else if (sideDetected[1]==1) {
				// side2Value is known
				side1Value = side2Value*(Math.sin(ang1Value)/Math.sin(ang2Value)); 
				side3Value = side2Value*(Math.sin(ang3Value)/Math.sin(ang2Value));
			}	
		}
		else if (angDetected[1]==1 && angDetected[2]==1) {
			//ang2Value,ang3Value are known
			ang1Value = Math.PI - (ang2Value + ang3Value);
			if (sideDetected[0]==1) {
				// side1Value is known
				side3Value = side1Value*(Math.sin(ang3Value)/Math.sin(ang1Value)); 
				side2Value = side1Value*(Math.sin(ang2Value)/Math.sin(ang1Value));
			}
			else if (sideDetected[1]==1) {
				// side2Value is known
				side1Value = side2Value*(Math.sin(ang1Value)/Math.sin(ang2Value)); 
				side3Value = side2Value*(Math.sin(ang3Value)/Math.sin(ang2Value));
			}
			else if (sideDetected[2]==1) {
				// side3Value is known
				side1Value = side3Value*(Math.sin(ang1Value)/Math.sin(ang3Value)); 
				side2Value = side3Value*(Math.sin(ang2Value)/Math.sin(ang3Value));
			}
		}
		else if (angDetected[2]==1 && angDetected[0]==1){
			//ang3Value,ang1Value are known
			ang2Value = Math.PI - (ang1Value + ang3Value);
			if (sideDetected[1]==1){
				// side2Value is known
				side1Value = side2Value*(Math.sin(ang1Value)/Math.sin(ang2Value)); 
				side3Value = side2Value*(Math.sin(ang3Value)/Math.sin(ang2Value));
			}
			else if (sideDetected[0]==1) {
				// side1Value is known
				side3Value = side1Value*(Math.sin(ang3Value)/Math.sin(ang1Value)); 
				side2Value = side1Value*(Math.sin(ang2Value)/Math.sin(ang1Value));
			}
			else if (sideDetected[2]==1) {
				// side3Value is known
				side1Value = side3Value*(Math.sin(ang1Value)/Math.sin(ang3Value)); 
				side2Value = side3Value*(Math.sin(ang2Value)/Math.sin(ang3Value));
			}
		}
	} 
	
	else if (triangleType=="SAS") {
		if (sideDetected[0]==1 && sideDetected[1]==1){
			//side1Value,side2Value are known
			if (angDetected[2]==1){
				// ang3Value is known
				side3Value = Math.sqrt((side1Value*side1Value) + (side2Value*side2Value) - (2*side1Value*side2Value*Math.cos(ang3Value)));
				ang1Value = Math.asin((side1Value*Math.sin(ang3Value))/side3Value); 
				ang2Value = Math.asin((side2Value*Math.sin(ang3Value))/side3Value);
			}
			else if (angDetected[0]==1) {
				// ang1Value is known
				ang2Value = Math.asin((side2Value*Math.sin(ang1Value))/side1Value);
				ang3Value = Math.PI - (ang1Value + ang2Value);
				side3Value = Math.sqrt((side1Value*side1Value) + (side2Value*side2Value) - (2*side1Value*side2Value*Math.cos(ang3Value)));
			}
			else if (angDetected[1]==1) {
				// ang2Value is known
				ang1Value = Math.asin((side1Value*Math.sin(ang2Value))/side2Value);
				ang3Value = Math.PI - (ang1Value + ang2Value);
				side3Value = Math.sqrt((side1Value*side1Value) + (side2Value*side2Value) - (2*side1Value*side2Value*Math.cos(ang3Value)));
			}	
		}
		else if (sideDetected[1]==1 && sideDetected[2]==1){
			//side2Value,side3Value are known
			if (angDetected[0]==1){
				// ang1Value is known
				side1Value = Math.sqrt((side3Value*side3Value) + (side2Value*side2Value) - (2*side3Value*side2Value*Math.cos(ang1Value)));
				ang3Value = Math.asin((side3Value*Math.sin(ang1Value))/side1Value); 
				ang2Value = Math.asin((side2Value*Math.sin(ang1Value))/side1Value);
			}
			else if (angDetected[1]==1) {
				// ang2Value is known
				ang3Value = Math.asin((side3Value*Math.sin(ang2Value))/side2Value);
				ang1Value = Math.PI - (ang3Value + ang2Value);
				side1Value = Math.sqrt((side3Value*side3Value) + (side2Value*side2Value) - (2*side3Value*side2Value*Math.cos(ang1Value)));
			}
			else if (angDetected[2]==1) {
				// ang3Value is known
				ang2Value = Math.asin((side2Value*Math.sin(ang3Value))/side3Value);
				ang1Value = Math.PI - (ang3Value + ang2Value);
				side1Value = Math.sqrt((side3Value*side3Value) + (side2Value*side2Value) - (2*side3Value*side2Value*Math.cos(ang1Value)));
			}
		}
		else if (sideDetected[2]==1 && sideDetected[0]==1){
			//side3Value,side1Value are known
			if (angDetected[1]==1){
				// ang2Value is known
				side2Value = Math.sqrt((side3Value*side3Value) + (side1Value*side1Value) - (2*side3Value*side1Value*Math.cos(ang2Value)));
				ang1Value = Math.asin((side1Value*Math.sin(ang2Value))/side2Value);
				ang3Value = Math.asin((side3Value*Math.sin(ang2Value))/side2Value);
			}
			else if (angDetected[0]==1) {
				// ang1Value is known
				ang3Value = Math.asin((side3Value*Math.sin(ang1Value))/side1Value);
				ang2Value = Math.PI - (ang3Value + ang1Value);
				side2Value = Math.sqrt((side3Value*side3Value) + (side1Value*side1Value) - (2*side3Value*side1Value*Math.cos(ang2Value)));
			}
			else if (angDetected[2]==1) {
				// ang3Value is known
				ang1Value = Math.asin((side1Value*Math.sin(ang3Value))/side3Value);
				ang2Value = Math.PI - (ang3Value + ang1Value);
				side2Value = Math.sqrt((side3Value*side3Value) + (side1Value*side1Value) - (2*side3Value*side1Value*Math.cos(ang2Value)));
			}
		}
	}
	
	//alert(ang1Value,ang2Value,ang3Value,side1Value,side2Value,side3Value);
	
	if (isNaN(ang1Value) || isNaN(ang2Value) || isNaN(ang3Value) || isNaN(side1Value) || isNaN(side2Value) || isNaN(side3Value)){
		alert("Error : Triangle not possible with given parameters");
		return false;
	}
	else if (ang1Value==0 || ang2Value ==0 || ang3Value==0 || side1Value==0 || side2Value==0 || side3Value==0){
		alert("Error : Triangle not possible with given parameters");
		return false;
	}
	else if((ang1Value+ang2Value+ang3Value).toFixed(4)!=3.1416){
		alert("Error : Triangle not possible with given parameters");
		return false;
	}
	
	side1Value = scaleFactor*Math.abs(side1Value);
	side2Value = scaleFactor*Math.abs(side2Value);
	side3Value = scaleFactor*Math.abs(side3Value);
	
	var x2 = startX;
	var y2 = startY;
	
	var x1 = side3Value * Math.cos(ang2Value);
	var y1 = side3Value * Math.sin(ang2Value);

	var x3 = side2Value * Math.cos(ang3Value);
	var y3 = side2Value * Math.sin(ang3Value);
	
	var point1_x = x2 + x1;
	var point2_x = x2;
	var point3_x = x1 + x2 + x3;
	
	var point1_y = y2 - y1;
	var point2_y = y2;
	var point3_y = (y2 - y1) + y3;
	
	if (rotateOffsetAngle==""){
		
		ctx.strokeStyle = (sideColor3=="")?"#000":sideColor3;
		ctx.beginPath();
			ctx.moveTo(point2_x, point2_y);
			ctx.lineTo(point1_x, point1_y);			
			ctx.stroke();
		ctx.closePath();
		
		ctx.strokeStyle = (sideColor2=="")?"#000":sideColor2;
		ctx.beginPath();	
			ctx.moveTo(point1_x, point1_y);
			ctx.lineTo(point3_x, point3_y);			
			ctx.stroke();
		ctx.closePath();
		
		ctx.strokeStyle = (sideColor1=="")?"#000":sideColor1;
		ctx.beginPath();		
			ctx.moveTo(point3_x, point3_y);
			ctx.lineTo(point2_x, point2_y);
			ctx.stroke();
		ctx.closePath();
		
		ctx.strokeStyle = "#000";
		ctx.fillStyle = "#000";
		
		ctx.font="12pt Arial Unicode MS";
		ctx.textAlign = "center";
		ctx.textBaseline = "middle";
		ctx.fillText(stringArr[0], point1_x, point1_y - 10);
		ctx.fillText(stringArr[1], point2_x - 10, point2_y + 10);
		ctx.fillText(stringArr[2], point3_x + 10, point3_y + 10);
		ctx.font="10pt Arial Unicode MS";
		ctx.fillText(side1Label, (point2_x+point3_x)/2, point2_y+15);
		ctx.fillText(side2Label, (point3_x+point1_x)/2 + 15, (point3_y+point1_y)/2);
		ctx.fillText(side3Label, (point2_x+point1_x)/2 - 15, (point2_y+point1_y)/2);
		
				
		if (drawArc1Flag==1){
			var currentFill = (angColor1=="")?currentFill="#FFF":currentFill=angColor1;
			if (ang1Value.toFixed(4)==1.5708){
				$("#"+canvasID).rotateCanvas({
					rotate: ang3Value * 180 / Math.PI,
					x: point1_x, y: point1_y,
				})
				.drawRect({
					fillStyle: currentFill,
					strokeStyle: "#000",
					x: point1_x, y: point1_y,
					width: 15, height: 15,
					fromCenter: false
				})
				.restoreCanvas();
			}
			else {
				ctx.fillStyle = currentFill;
				ctx.beginPath();
					ctx.moveTo(point1_x,point1_y);
					ctx.arc(point1_x, point1_y, 15, ang3Value, ang3Value + ang1Value, false);
				ctx.closePath();
				ctx.fill();
				ctx.stroke();
				
			}			
		}
		if (drawArc2Flag==1){
			var currentFill = (angColor2=="")?currentFill="#FFF":currentFill=angColor2;
			if (ang2Value.toFixed(4)==1.5708){
				$("#"+canvasID).drawRect({
					fillStyle: currentFill,
					strokeStyle: "#000",
					x: point2_x, y: point2_y - 15,
					width: 15, height: 15,
					fromCenter: false
				});				
			}
			else {
				ctx.fillStyle = currentFill;
				ctx.beginPath();
					ctx.moveTo(point2_x, point2_y);
					ctx.arc(point2_x, point2_y, 15, 0, -ang2Value, true);
				ctx.closePath();
				ctx.fill();
				ctx.stroke();
			}
		}
		if (drawArc3Flag==1){
			var currentFill = (angColor3=="")?currentFill="#FFF":currentFill=angColor3;
			if (ang3Value.toFixed(4)==1.5708){
				$("#"+canvasID).drawRect({
					fillStyle: currentFill,
					strokeStyle: "#000",
					x: point3_x - 15, y: point3_y - 15,
					width: 15, height: 15,
					fromCenter: false
				});				
			}
			else {
				ctx.fillStyle = currentFill;
				ctx.beginPath();
					ctx.moveTo(point3_x, point3_y);
					ctx.arc(point3_x, point3_y, 15, Math.PI, Math.PI + ang3Value, false);
				ctx.closePath();
				ctx.fill();
				ctx.stroke();
			}
		}
		return point1_x.toFixed(2)+","+point1_y.toFixed(2)+"|"+point2_x.toFixed(2)+","+point2_y.toFixed(2)+"|"+point3_x.toFixed(2)+","+point3_y.toFixed(2)+"|"+ (ang1Value * 180 / Math.PI)+","+(ang2Value * 180 / Math.PI)+","+(ang3Value * 180 / Math.PI);
	}
	else {
		ctx.save();
			ctx.translate(point2_x, point2_y);
			ctx.rotate(-rotateOffsetAngle);
			
			ctx.strokeStyle = (sideColor3=="")?"#000":sideColor3;
			ctx.beginPath();
				ctx.moveTo(0, 0);
				ctx.lineTo(x1, -y1);			
				ctx.stroke();
			ctx.closePath();
			
			ctx.strokeStyle = (sideColor2=="")?"#000":sideColor2;
			ctx.beginPath();	
				ctx.moveTo(x1, -y1);
				ctx.lineTo(side1Value,0);			
				ctx.stroke();
			ctx.closePath();
			
			ctx.strokeStyle = (sideColor1=="")?"#000":sideColor1;
			ctx.beginPath();		
				ctx.moveTo(side1Value,0);
				ctx.lineTo(0,0);
				ctx.stroke();
			ctx.closePath();
		ctx.restore();
		
		ctx.strokeStyle = "#000";
		
		var text3X = side1Value*Math.cos(rotateOffsetAngle);
		var text3Y = side1Value*Math.sin(rotateOffsetAngle);
		text3X = point2_x + text3X;
		text3Y = point2_y - text3Y;
		
		var text1X = side3Value*Math.cos(ang2Value+rotateOffsetAngle);
		var text1Y = side3Value*Math.sin(ang2Value+rotateOffsetAngle);
		text1X = point2_x + text1X;
		text1Y = point2_y - text1Y;
		
		ctx.font="12pt Arial Unicode MS";
		ctx.textAlign = "center";
		ctx.textBaseline = "middle";
		ctx.fillText(stringArr[0], text1X, text1Y - 10);
		ctx.fillText(stringArr[1], point2_x - 10, point2_y + 10);
		ctx.fillText(stringArr[2], text3X + 10, text3Y + 10);	
		ctx.font="10pt Arial Unicode MS";
		ctx.fillText(side1Label, (point2_x+text3X)/2, (point2_y+text3Y)/2+15);
		ctx.fillText(side2Label, (text3X+text1X)/2 + 15, (text3Y+text1Y)/2-15);
		ctx.fillText(side3Label, (point2_x+text1X)/2 - 15, (point2_y+text1Y)/2);
		
		if (drawArc1Flag==1){
			var currentFill = (angColor1=="")?currentFill="#FFF":currentFill=angColor1;
			if (ang1Value.toFixed(4)==1.5708){
				$("#"+canvasID).rotateCanvas({
					rotate: ((ang3Value - rotateOffsetAngle) * 180) / Math.PI,
					x: text1X, y: text1Y
				})
				.drawRect({
					fillStyle: currentFill,
					strokeStyle: "#000",
					x: text1X, y: text1Y,
					width: 15, height: 15,
					fromCenter: false
				})
				.restoreCanvas();
			}
			else {
				ctx.save();
					ctx.translate(point2_x, point2_y);
					ctx.rotate(-rotateOffsetAngle);
					ctx.fillStyle = currentFill;
					ctx.beginPath();
						ctx.moveTo(x1, -y1);
						ctx.arc(x1, -y1, 15, ang3Value, ang3Value + ang1Value, false);
					ctx.closePath();
					ctx.fill();
					ctx.stroke();					
				ctx.restore();				
			}			
		}
		if (drawArc2Flag==1){
			var currentFill = (angColor2=="")?currentFill="#FFF":currentFill=angColor2;
			if (ang2Value.toFixed(4)==1.5708){
				$("#"+canvasID).rotateCanvas({
					rotate: (-rotateOffsetAngle) * 180 / Math.PI,
					x: point2_x, y: point2_y,
				})
				.drawRect({
					fillStyle: currentFill,
					strokeStyle: "#000",
					x: point2_x, y: point2_y-15,
					width: 15, height: 15,
					fromCenter: false
				})
				.restoreCanvas();			
			}
			else {
				ctx.save();
					ctx.translate(point2_x, point2_y);
					ctx.rotate(-rotateOffsetAngle);
					ctx.fillStyle = currentFill;
					ctx.beginPath();
						ctx.moveTo(0,0);
						ctx.arc(0, 0, 15, 0, -ang2Value, true);
					ctx.closePath();
					ctx.fill();
					ctx.stroke();		
				ctx.restore();	
			}
		}
		if (drawArc3Flag==1){
			var currentFill = (angColor3=="")?currentFill="#FFF":currentFill=angColor3;
			if (ang3Value.toFixed(4)==1.5708){
				$("#"+canvasID).rotateCanvas({
					rotate: (-rotateOffsetAngle) * 180 / Math.PI,
					x: text3X, y: text3Y,
				})
				.drawRect({
					fillStyle: currentFill,
					strokeStyle: "#000",
					x: text3X-15, y: text3Y-15,
					width: 15, height: 15,
					fromCenter: false
				})
				.restoreCanvas();			
			}
			else {
				ctx.save();
					ctx.translate(point2_x, point2_y);
					ctx.rotate(-rotateOffsetAngle);
					ctx.fillStyle = currentFill;
					ctx.beginPath();
						ctx.moveTo(side1Value, 0);
						ctx.arc(side1Value, 0, 15, Math.PI, Math.PI + ang3Value, false);
					ctx.closePath();
					ctx.fill();
					ctx.stroke();	
				ctx.restore();	
			}
		}
		return text1X.toFixed(2)+","+text1Y.toFixed(2)+"|"+point2_x.toFixed(2)+","+point2_y.toFixed(2)+"|"+text3X.toFixed(2)+","+text3Y.toFixed(2);	
	}
}

function blinkAngle(angleNo,noOfTimes,canvasID,startX,startY,scaleFactor,triangleName,angleValueString,sideValueString,rotateOffsetAngle,arcFlagString,angleColorString,sideColorString,sideLablelString){
	var count = 0;
	var count1 = 0;
	var count2 = 0;
	var count3 = 0;
	
	var temp1 = angleValueString.split('|');
	var ang1Value = temp1[0];
	var ang2Value = temp1[1];
	var ang3Value = temp1[2];
	
	var temp2 = sideValueString.split('|');
	var side1Value = temp2[0];
	var side2Value = temp2[1];
	var side3Value = temp2[2];
	
	var temp3 = arcFlagString.split('|');
	var drawArc1Flag = temp3[0];
	var drawArc2Flag = temp3[1];
	var drawArc3Flag = temp3[2];
	
	var temp4 = angleColorString.split('|');
	var angColor1 = temp4[0];
	var angColor2 = temp4[1];
	var angColor3 = temp4[2];
	
	var temp5 = sideColorString.split('|');
	var sideColor1 = temp5[0];
	var sideColor2 = temp5[1];
	var sideColor3 = temp5[2];
	
	var temp6 = sideLablelString.split('|');
	var side1Label = temp6[0];
	var side2Label = temp6[1];
	var side3Label = temp6[2];
	
	side1Label = side1Label.replace("sqrt","\u221A");
	side2Label =side2Label.replace("sqrt","\u221A");
	side3Label =side3Label.replace("sqrt","\u221A");
		
	if (angleNo==1){
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,''+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);
	}
	else if (angleNo==2){
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+''+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);
	}
	else if (angleNo==3){
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+'',sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);
	}
	else if (angleNo=="all") {
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,''+"|"+''+"|"+'',sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);		
	}
	else {
		var angArr = angleNo.split("|");
		if (angArr[0]=="1"){
			var timer1 = window.setInterval(function(){
				count1++;
				$("#"+canvasID).clearCanvas();
				if (count1%2==0){
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				else {
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,''+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				if (count1>=2*noOfTimes){
					window.clearInterval(timer1);
				}
			},500);
			
			window.setTimeout(function(){
				var timer2 = window.setInterval(function(){
					count2++;
					$("#"+canvasID).clearCanvas();
					if (count2%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+''+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count2>=2*noOfTimes){
						window.clearInterval(timer2);
					}
				},500);
			},1000*noOfTimes);
			
			window.setTimeout(function(){
				var timer3 = window.setInterval(function(){
					count3++;
					$("#"+canvasID).clearCanvas();
					if (count3%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+'',sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count3>=2*noOfTimes){
						window.clearInterval(timer3);
					}
				},500);
			},2000*noOfTimes);
		}
		else if (angArr[0]=="2"){
			var timer1 = window.setInterval(function(){
				count1++;
				$("#"+canvasID).clearCanvas();
				if (count1%2==0){
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				else {
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+''+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				if (count1>=2*noOfTimes){
					window.clearInterval(timer1);
				}
			},500);
			
			window.setTimeout(function(){
				var timer2 = window.setInterval(function(){
					count2++;
					$("#"+canvasID).clearCanvas();
					if (count2%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+'',sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count2>=2*noOfTimes){
						window.clearInterval(timer2);
					}
				},500);
			},1000*noOfTimes);
			
			window.setTimeout(function(){
				var timer3 = window.setInterval(function(){
					count3++;
					$("#"+canvasID).clearCanvas();
					if (count3%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,''+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count3>=2*noOfTimes){
						window.clearInterval(timer3);
					}
				},500);
			},2000*noOfTimes);
		}
		else if (angArr[0]=="3"){
			var timer1 = window.setInterval(function(){
				count1++;
				$("#"+canvasID).clearCanvas();
				if (count1%2==0){
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				else {
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+'',sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				if (count1>=2*noOfTimes){
					window.clearInterval(timer1);
				}
			},500);
			
			window.setTimeout(function(){
				var timer2 = window.setInterval(function(){
					count2++;
					$("#"+canvasID).clearCanvas();
					if (count2%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,''+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count2>=2*noOfTimes){
						window.clearInterval(timer2);
					}
				},500);
			},1000*noOfTimes);
			
			window.setTimeout(function(){
				var timer3 = window.setInterval(function(){
					count3++;
					$("#"+canvasID).clearCanvas();
					if (count3%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+''+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count3>=2*noOfTimes){
						window.clearInterval(timer3);
					}
				},500);
			},2000*noOfTimes);
		}
	}
}

function blinkSide(sideNo,noOfTimes,canvasID,startX,startY,scaleFactor,triangleName,angleValueString,sideValueString,rotateOffsetAngle,arcFlagString,angleColorString,sideColorString,sideLablelString){
	var count = 0;
	var count1 = 0;
	var count2 = 0;
	var count3 = 0;
	
	var temp1 = angleValueString.split('|');
	var ang1Value = temp1[0];
	var ang2Value = temp1[1];
	var ang3Value = temp1[2];
	
	var temp2 = sideValueString.split('|');
	var side1Value = temp2[0];
	var side2Value = temp2[1];
	var side3Value = temp2[2];
	
	var temp3 = arcFlagString.split('|');
	var drawArc1Flag = temp3[0];
	var drawArc2Flag = temp3[1];
	var drawArc3Flag = temp3[2];
	
	var temp4 = angleColorString.split('|');
	var angColor1 = temp4[0];
	var angColor2 = temp4[1];
	var angColor3 = temp4[2];
	
	var temp5 = sideColorString.split('|');
	var sideColor1 = temp5[0];
	var sideColor2 = temp5[1];
	var sideColor3 = temp5[2];
	
	var temp6 = sideLablelString.split('|');
	var side1Label = temp6[0];
	var side2Label = temp6[1];
	var side3Label = temp6[2];
	
	side1Label = side1Label.replace("sqrt","\u221A");
	side2Label =side2Label.replace("sqrt","\u221A");
	side3Label =side3Label.replace("sqrt","\u221A");
	
	if (sideNo==1){
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);
	}
	else if (sideNo==2){
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+''+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);
	}
	else if (sideNo==3){
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);
	}
	else if (sideNo=="all") {
		var timer = window.setInterval(function(){
			count++;
			$("#"+canvasID).clearCanvas();
			if (count%2==0){
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+sideColor2+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
			}
			else {
				drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+''+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
			}
			if (count>=2*noOfTimes){
				window.clearInterval(timer);
			}
		},500);		
	}
	else {
		var angArr = sideNo.split("|");
		if (angArr[0]=="1"){
			var timer1 = window.setInterval(function(){
				count1++;
				$("#"+canvasID).clearCanvas();
				if (count1%2==0){
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
				}
				else {
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+''+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
				}
				if (count1>=2*noOfTimes){
					window.clearInterval(timer1);
				}
			},500);
			
			window.setTimeout(function(){
				var timer2 = window.setInterval(function(){
					count2++;
					$("#"+canvasID).clearCanvas();
					if (count2%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+sideColor2+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count2>=2*noOfTimes){
						window.clearInterval(timer2);
					}
				},500);
			},1000*noOfTimes);
			
			window.setTimeout(function(){
				var timer3 = window.setInterval(function(){
					count3++;
					$("#"+canvasID).clearCanvas();
					if (count3%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+''+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count3>=2*noOfTimes){
						window.clearInterval(timer3);
					}
				},500);
			},2000*noOfTimes);
		}
		else if (angArr[0]=="2"){
			var timer1 = window.setInterval(function(){
				count1++;
				$("#"+canvasID).clearCanvas();
				if (count1%2==0){
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
				}
				else {
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+sideColor2+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
				}
				if (count1>=2*noOfTimes){
					window.clearInterval(timer1);
				}
			},500);
			
			window.setTimeout(function(){
				var timer2 = window.setInterval(function(){
					count2++;
					$("#"+canvasID).clearCanvas();
					if (count2%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+''+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count2>=2*noOfTimes){
						window.clearInterval(timer2);
					}
				},500);
			},1000*noOfTimes);
			
			window.setTimeout(function(){
				var timer3 = window.setInterval(function(){
					count3++;
					$("#"+canvasID).clearCanvas();
					if (count3%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+''+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count3>=2*noOfTimes){
						window.clearInterval(timer3);
					}
				},500);
			},2000*noOfTimes);
		}
		else if (angArr[0]=="3"){
			var timer1 = window.setInterval(function(){
				count1++;
				$("#"+canvasID).clearCanvas();
				if (count1%2==0){
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
				}
				else {
					drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+''+"|"+sideColor3,side1Label+"|"+side2Label+"|"+side3Label);
				}
				if (count1>=2*noOfTimes){
					window.clearInterval(timer1);
				}
			},500);
			
			window.setTimeout(function(){
				var timer2 = window.setInterval(function(){
					count2++;
					$("#"+canvasID).clearCanvas();
					if (count2%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,sideColor1+"|"+''+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count2>=2*noOfTimes){
						window.clearInterval(timer2);
					}
				},500);
			},1000*noOfTimes);
			
			window.setTimeout(function(){
				var timer3 = window.setInterval(function(){
					count3++;
					$("#"+canvasID).clearCanvas();
					if (count3%2==0){
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					else {
						drawTriangle(canvasID,startX,startY,scaleFactor,triangleName,ang1Value+"|"+ang2Value+"|"+ang3Value,side1Value+"|"+side2Value+"|"+side3Value,rotateOffsetAngle,drawArc1Flag+"|"+drawArc2Flag+"|"+drawArc3Flag,angColor1+"|"+angColor2+"|"+angColor3,''+"|"+sideColor2+"|"+'',side1Label+"|"+side2Label+"|"+side3Label);
					}
					if (count3>=2*noOfTimes){
						window.clearInterval(timer3);
					}
				},500);
			},2000*noOfTimes);
		}
	}
}