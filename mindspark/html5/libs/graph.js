var time4;
var objectName = '';
var numberLanguage;
var getParameters = getURLParameters();
if (typeof getParameters['numberLanguage'] == "undefined") numberLanguage = 'english'; else numberLanguage = getParameters['numberLanguage'];
function Graph(config) {
    // user defined properties
    objectName = config.object;
    this.canvas = document.getElementById(config.canvasId);
    this.minX = config.minX;
    this.minY = config.minY;
    this.maxX = config.maxX;
    this.maxY = config.maxY;
    this.unitsPerTick = config.unitsPerTick;
    this.unitsPerTickX = config.unitsPerTickX || config.unitsPerTick;
    this.unitsPerTickY = config.unitsPerTickY || config.unitsPerTick;
    this.gridLine = config.gridLineColor || 'rgb(195, 94, 94)';
    this.axisColor = config.axisColor || 'rgb(195, 94, 94)';
    this.font = config.font || '8pt Calibri';
    this.drawGrid = config.drawGrid;
    this.drawTicks = config.drawTicks || false;
    this.lineArrowHead = config.lineArrow;
    this.textColor = config.textColor;
    this.dotRadius = config.dotRadius || 3;
    this.drawAxes = config.drawAxes;
    this.drawNumbers = config.drawNumbers || false;
    this.tickSize = 10;

    // relationships
    this.context = this.canvas.getContext('2d');
    this.rangeX = this.maxX - this.minX;
    this.rangeY = this.maxY - this.minY;

    this.unitX = this.canvas.width / this.rangeX;
    this.unitY = this.canvas.height / this.rangeY;
    this.centerY = Math.round(Math.abs(this.maxY / this.rangeY) * this.canvas.height);
    this.centerX = Math.round(Math.abs(this.minX / this.rangeX) * this.canvas.width);
    this.iteration = (this.maxX - this.minX) / 1000;
    this.scaleX = this.canvas.width / this.rangeX;
    this.scaleY = this.canvas.height / this.rangeY;
    this.context.fillStyle = "blue";
    //this.canvas.style.border = '2px solid ' + this.gridLine;
    if (this.drawGrid == true)
        this.drawGridLines();
    if (this.drawAxes) {
        this.drawXAxis();
        this.drawYAxis();
    }


    // draw x and y axis

}
Graph.prototype.drawGridLines = function () {

    var context = this.context;
    context.save();
    var xInr = this.canvas.width / this.rangeX;
    var xVar = 0;
    var yInr = this.canvas.height / this.rangeY;
    var yVar = 0;
    context.beginPath();
    context.strokeStyle = this.gridLine;
    context.stokeWidth = 2;
    for (var i = this.minY; i <= this.maxY; i++) {
        context.moveTo(0, yVar);
        context.lineTo(this.canvas.width, yVar);
        yVar += yInr;

    }
    context.stroke();
    context.closePath();
    context.beginPath();
    context.strokeStyle = this.gridLine;
    context.stokeWidth = 2;
    for (var i = this.minX; i <= this.maxX; i++) {
        context.moveTo(xVar, 0);
        context.lineTo(xVar, this.canvas.height);
        xVar += xInr;

    }
    context.stroke();
    context.closePath();
    context.save();
}
Graph.prototype.drawXAxis = function () {
    var context = this.context;
    context.save();

    // Draws arrow heads on X-Axis
    context.beginPath();
    context.moveTo(4, this.centerY - 3);
    context.lineTo(1, this.centerY);
    context.lineTo(4, this.centerY + 3);
    context.lineTo(4, this.centerY - 3);
    context.closePath();

    context.strokeStyle = this.axisColor;
    context.lineWidth = 2;
    context.stroke();

    context.beginPath();
    context.moveTo(this.canvas.width - 4, this.centerY - 3);
    context.lineTo(this.canvas.width - 1, this.centerY);
    context.lineTo(this.canvas.width - 4, this.centerY + 3);
    context.lineTo(this.canvas.width - 4, this.centerY - 3);
    context.closePath();

    context.strokeStyle = this.axisColor;
    context.lineWidth = 2;
    context.stroke();
    //////////////////////////////////////

    context.fillStyle = this.textColor;
    context.beginPath();
    context.moveTo(0, this.centerY);
    context.lineTo(this.canvas.width, this.centerY);
    context.strokeStyle = this.axisColor;
    context.lineWidth = 2;
    context.stroke();

    // draw tick marks
    var xPosIncrement = this.unitsPerTickX * this.unitX;
    var xPos, unit;
    context.font = this.font;
    context.textAlign = 'center';
    context.textBaseline = 'top';

    // draw left tick marks
    xPos = this.centerX - xPosIncrement;
    unit = -1 * this.unitsPerTickX;
    while (xPos > 0) {
        if (this.drawTicks == true) {
            context.moveTo(xPos, this.centerY - this.tickSize / 2);
            context.lineTo(xPos, this.centerY + this.tickSize / 2);
            context.stroke();
        }
        if (this.drawNumbers)
            context.fillText(replaceDynamicText(unit, numberLanguage, ''), xPos, this.centerY + this.tickSize / 2 + 3);
        unit -= this.unitsPerTickX;
        xPos = Math.round(xPos - xPosIncrement);
    }

    // draw right tick marks
    xPos = this.centerX + xPosIncrement;
    unit = this.unitsPerTickX;
    while (xPos < this.canvas.width) {
        if (this.drawTicks == true) {

            context.moveTo(xPos, this.centerY - this.tickSize / 2);
            context.lineTo(xPos, this.centerY + this.tickSize / 2);
            context.stroke();
        }
        if (this.drawNumbers)
            context.fillText(replaceDynamicText(unit, numberLanguage, ''), xPos, this.centerY + this.tickSize / 2 + 3);
        unit += this.unitsPerTickX;
        xPos = Math.round(xPos + xPosIncrement);
    }
    context.restore();
};
Graph.prototype.drawYAxis = function () {
    var context = this.context;
    context.save();

    // Draw the arrow heads on Y-Axis
    context.beginPath();
    context.moveTo(this.centerX, 1);
    context.lineTo(this.centerX + 3, 4);
    context.lineTo(this.centerX + 3, 4);
    context.lineTo(this.centerX - 3, 4);
    context.closePath();

    context.fillStyle = this.textColor;
    context.strokeStyle = this.axisColor;
    context.lineWidth = 2;
    context.stroke();

    context.beginPath();
    context.moveTo(this.centerX, this.canvas.height - 1);
    context.lineTo(this.centerX + 3, this.canvas.height - 4);
    context.lineTo(this.centerX + 3, this.canvas.height - 4);
    context.lineTo(this.centerX - 3, this.canvas.height - 4);
    context.closePath();


    context.strokeStyle = this.axisColor;
    context.lineWidth = 2;
    context.stroke();
    ////////////////////////////////////////

    context.beginPath();
    context.moveTo(this.centerX, 0);
    context.lineTo(this.centerX, this.canvas.height);
    context.strokeStyle = this.axisColor;
    context.lineWidth = 2;
    context.stroke();

    // draw tick marks
    var yPosIncrement = this.unitsPerTickY * this.unitY;
    var yPos, unit;
    context.font = this.font;
    context.textAlign = 'right';
    context.textBaseline = 'middle';

    // draw top tick marks and labels
    yPos = this.centerY - yPosIncrement;
    unit = this.unitsPerTickY;
    while (yPos > 0) {
        if (this.drawTicks == true) {
            context.moveTo(this.centerX - this.tickSize / 2, yPos);
            context.lineTo(this.centerX + this.tickSize / 2, yPos);
            context.stroke();
        }
        if (this.drawNumbers)
            context.fillText(replaceDynamicText(unit, numberLanguage, ''), this.centerX - this.tickSize / 2 - 3, yPos);
        unit += this.unitsPerTickY;
        yPos = Math.round(yPos - yPosIncrement);
    }

    //// draw bottom tick marks
    yPos = this.centerY + yPosIncrement;
    if (this.drawNumbers)
        context.fillText(replaceDynamicText('0', numberLanguage, ''), this.centerX + 12, this.centerY + 10);
    unit = -1 * this.unitsPerTickY;

    while (yPos < this.canvas.height) {
        if (this.drawTicks == true) {
            context.moveTo(this.centerX - this.tickSize / 2, yPos);
            context.lineTo(this.centerX + this.tickSize / 2, yPos);
            context.stroke();
        }
        if (this.drawNumbers)
            context.fillText(replaceDynamicText(unit, numberLanguage, ''), this.centerX - this.tickSize / 2 - 3, yPos);
        unit -= this.unitsPerTickY;
        yPos = Math.round(yPos + yPosIncrement);
    }
    context.restore();
};
Graph.prototype.drawEquation = function (equation, color, thickness) {
    var context = this.context;
    context.save();
    this.transformContext();

    context.beginPath();
    context.moveTo(this.minX, equation(this.minX));

    for (var x = this.minX + this.iteration; x <= this.maxX; x += this.iteration) {
        context.lineTo(x, equation(x));
    }

    context.restore();
    context.lineJoin = 'milter';
    context.lineWidth = thickness;
    context.strokeStyle = color;
    context.stroke();
    context.restore();
};
Graph.prototype.transformContext = function () {
    var context = this.context;

    // move context to center of canvas
    this.context.translate(this.centerX, this.centerY);


    /*
    * stretch grid to fit the canvas window, and
    * invert the y scale so that that increments
    * as you move upwards
    */
    context.scale(this.scaleX, -this.scaleY);
};
Graph.prototype.transformContext2 = function () {
    var context = this.context;

    // move context to center of canvas
    this.context.translate(this.centerX, this.centerY);


    /*
    * stretch grid to fit the canvas window, and
    * invert the y scale so that that increments
    * as you move upwards
    */
    //context.scale(this.scaleX, -this.scaleY);
};
Graph.prototype.drawDot = function (equation, x1, x2, label) {
    var context = this.context;
    context.save();
    this.transformContext2();
    //this.object.clearCan();
    context.beginPath();
    context.strokeStyle = 'black';
    context.fillStyle = 'black';
    context.arc(parseFloat(x1) * this.scaleX, equation(parseFloat(x1)) * this.scaleY * -1, this.dotRadius, 0, Math.PI * 2);
    if ((label == true || label == undefined) && label != false)
        context.fillText(replaceDynamicText('(' + x1.toFixed(2) + ', ' + equation(x1).toFixed(2) + ')', numberLanguage, ''), (x1 * this.scaleX) + 10, (equation(x1) * this.scaleY * -1) + 10);
    else if (label != false)
        context.fillText(replaceDynamicText(label, numberLanguage, ''), (x1 * this.scaleX) + 10, (equation(x1) * this.scaleY * -1) + 10);
    context.stroke();
    context.fill();
    context.restore();
    context.closePath();
}
Graph.prototype.blinkDot = function (equation, x1, x2, times, direction, callback) {
    var context = this.context;
    context.save();
    this.transformContext();
    interval = setInterval(function () {

        if (times > 0) {
            //this.object.clearCan();
            context.beginPath();
            context.strokeStyle = 'red';
            if (context.fillStyle == "#000000")
                context.fillStyle = '#ffffff';
            else
                context.fillStyle = '#000000';

            //context.clearRect((parseInt(x1) * this.scaleX)-1, (equation(parseInt(x1)) * this.scaleY * -1)-1,(parseInt(x1) * this.scaleX)+1, (equation(parseInt(x1)) * this.scaleY * -1)+1)
            context.arc(parseInt(x1) * this.scaleX, equation(parseInt(x1)) * this.scaleY * -1, this.dotRadius, 0, Math.PI * 2);
            //console.log(times);
            context.fill();
            context.save();
            context.restore();
            context.closePath();
            times--;
        }
        else {
            clearInterval(interval);
            context.beginPath();
            if (direction == "")
                direction = "right";
            context.fillStyle = "#000000";
            context.font = this.font;
            context.arc(parseInt(x1) * this.scaleX, equation(parseInt(x1)) * this.scaleY * -1, this.dotRadius, 0, Math.PI * 2);
            if (direction == "right")
                context.fillText(replaceDynamicText('(' + x1 + ',' + equation(x1) + ')', numberLanguage, ''), (x1 * this.scaleX) + 10, (equation(x1) * this.scaleY * -1) - 10);
            else if (direction == "left")
                context.fillText(replaceDynamicText('(' + x1 + ',' + equation(x1) + ')', numberLanguage, ''), (x1 * this.scaleX) - 40, (equation(x1) * this.scaleY * -1) - 10);

            context.fill();
            context.save();
            context.restore();
            context.closePath();
            if (callback)
                callback();
        }
    }, 200);
}
Graph.prototype.moveDot = function (equation, x1, x2, time,timer) {

    var context = this.context;
    context.save();
    this.transformContext2();
    var diff = (x2 - x1) / 100;
    var canvas = this.canvas;

    this.clearCan();
    var temp = this;
    timer=window.setInterval(function () {
        //context.clearRect(0, 0, 300, 150);
        temp.clearCan();

        context.beginPath();
        context.strokeStyle = 'white';
        context.fillStyle = 'black';
        context.arc(x1 * temp.scaleX, -1 * equation(x1) * temp.scaleY, 5, 0, Math.PI * 2);
        //console.log(evalEq(expression, x1), equation(x1))
        //console.log(equation, x1, expression); ;
        //$("#points").html('(' + x1.toFixed(2) + ', ' + equation(x1).toFixed(2) + ')');
        context.stroke();
        context.fill();
        //console.log(x1, x2);
        context.closePath();
        //alert();

        if ((x1 > (x2) && diff > 0) || (x1 < (x2) && diff < 0)) {
            //console.log(x1, x2 + 'fsad');
            window.clearInterval(timer);
            context.restore();
            //alert(demos);

        }
        x1 += diff;
    }, time);



}
Graph.prototype.clearCan = function () {
    var context = this.context;
    var canvas = this.canvas;
    context.restore();
    canvas.width = canvas.width;
    context.save();
    this.transformContext2();
}
Graph.prototype.clearCan2 = function () {
    var canvas = this.canvas;
    canvas.width = canvas.width;

}
Graph.prototype.animateLine = function (equation, x1, x2, color, thickness, arrows, callBack) {

    var context = this.context;
    var canvas = this.canvas;
    arrows = arrows.split('|');
    var arrowTop = parseInt(arrows[0]);
    var arrowBottom = parseInt(arrows[1]);
    var increment = 0;
    if (arrowTop) {
        context.restore();
        context.save();
        context.translate(this.centerX, this.centerY);
        //context.scale(this.scaleX, -this.scaleY);
        context.translate(x1 * this.scaleX, equation(x1) * -this.scaleY);

        var m = (equation(x2) - equation(x1)) / (x2 - x1);
        var deg = Math.atan(m);
        if (m < 0)
            var degM = 270;
        else
            var degM = 90;
        deg = deg * 180 / Math.PI;

        context.rotate((degM - deg) * Math.PI / 180);
        context.beginPath();
        context.strokeStyle = color;
        context.fillStyle = color;
        if (equation(x2) < equation(x1)) {
            context.moveTo(-8, 8);
            context.lineTo(0, 0);
            context.lineTo(8, 8);
            context.lineTo(-8, 8);
        }
        else {
            context.moveTo(8, -8);
            context.lineTo(0, 0);
            context.lineTo(-8, -8);
            context.lineTo(8, -8);
        }
        context.stroke();
        context.fill();
        context.closePath();
        context.restore();
    }
    context.save();
    this.transformContext2();
    var diff = (x2 - x1) / 100;
    var time = 0;
    var temp = this;
    var temp2 = x1;
    var arrowB = this.lineArrowBottom;
    time4 = window.setInterval(function () {


        //if ((x1 >= (x2) && diff > 0) || (x1 <= (x2) && diff < 0)) {
        if (increment >= 100) {
            window.clearInterval(time4);
            context.restore();
            if (arrowBottom) {
                context.restore();
                context.save();
                context.translate(temp.centerX, temp.centerY);
                //context.scale(this.scaleX, -this.scaleY);
                context.translate(x2 * temp.scaleX, equation(x2) * -temp.scaleY);
                var m = (equation(x2) - equation(x1)) / (x2 - x1);
                var deg = Math.atan(m);
                deg = deg * 180 / Math.PI;
                context.rotate((degM - deg) * Math.PI / 180);
                context.beginPath();
                context.strokeStyle = color;
                context.fillStyle = color;
                if (equation(x2) < equation(x1)) {
                    context.moveTo(-8, 8);
                    context.lineTo(0, 0);
                    context.lineTo(8, 8);
                    context.lineTo(-8, 8);
                }
                else {
                    context.moveTo(8, -8);
                    context.lineTo(0, 0);
                    context.lineTo(-8, -8);
                    context.lineTo(8, -8);
                }
                context.stroke();
                context.fill();
                context.closePath();
                context.restore();
            }
            callBack();
        }
        else {
            temp.drawLine(equation, x1, x1 + diff, color, thickness, '0|0');
            x1 += diff;
            increment++;



        }

    }, time);



}
Graph.prototype.drawLine = function (equation, x1, x2, color, thickness, arrows) {
    var context = this.context;
    arrows = arrows.split('|');
    var arrowTop = parseInt(arrows[0]);
    var arrowBottom = parseInt(arrows[1]);
    if (arrowTop) {
        context.restore();
        context.save();
        context.translate(this.centerX, this.centerY);
        //context.scale(this.scaleX, -this.scaleY);
        context.translate(x1 * this.scaleX, equation(x1) * -this.scaleY);

        var m = (equation(x2) - equation(x1)) / (x2 - x1);
        var deg = Math.atan(m);
        deg = deg * 180 / Math.PI;
        if (m < 0)
            var degM = 270;
        else
            var degM = 90;

        context.rotate((degM - deg) * Math.PI / 180);
        context.beginPath();
        context.strokeStyle = color;
        context.fillStyle = color;
        if (equation(x2) < equation(x1)) {
            context.moveTo(-8, 8);
            context.lineTo(0, 0);
            context.lineTo(8, 8);
            context.lineTo(-8, 8);
        }
        else {
            context.moveTo(8, -8);
            context.lineTo(0, 0);
            context.lineTo(-8, -8);
            context.lineTo(8, -8);
        }
        context.stroke();
        context.fill();
        context.closePath();
        context.restore();
    }

    if (arrowBottom) {
        context.restore();
        context.save();
        context.translate(this.centerX, this.centerY);
        //context.scale(this.scaleX, -this.scaleY);
        context.translate(x2 * this.scaleX, equation(x2) * -this.scaleY);
        var m = (equation(x2) - equation(x1)) / (x2 - x1);
        var deg = Math.atan(m);
        deg = parseInt(deg * 180 / Math.PI);
        if (m < 0)
            var degM = 270;
        else
            var degM = 90;
        context.rotate((degM - deg) * Math.PI / 180);
        context.beginPath();
        context.strokeStyle = color;
        context.fillStyle = color;
        if (equation(x2) > equation(x1)) {
            context.moveTo(-8, 8);
            context.lineTo(0, 0);
            context.lineTo(8, 8);
            context.lineTo(-8, 8);
        }
        else {
            context.moveTo(8, -8);
            context.lineTo(0, 0);
            context.lineTo(-8, -8);
            context.lineTo(8, -8);
        }
        context.stroke();
        context.fill();
        context.closePath();
        context.restore();
    }
    context.save();
    this.transformContext();

    if (x1 != x2) {
        context.beginPath();

        context.moveTo(x1, equation(x1));
        var diff = parseFloat((x2 - x1) / 100);
        x1 = parseFloat(x1);
        x2 = parseFloat(x2);

        for (var x = x1 + diff; ((x >= x2 && diff < 0) || (x <= x2 && diff > 0)); x += diff) {
            context.lineTo(x, equation(x));

        }
    }
    else {
        context.beginPath();
        context.moveTo(x1, this.minY);
        context.lineTo(x1, this.maxY);

    }

    context.restore();
    context.lineJoin = 'milter';
    context.lineWidth = thickness;
    context.strokeStyle = color;
    context.stroke();
    context.restore();

}
