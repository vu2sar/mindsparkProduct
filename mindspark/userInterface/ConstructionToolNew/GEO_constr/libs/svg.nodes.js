var MySvg = function () {
    var _mySvg = {};

    function isPathArc(xmlNode) {
        var pathN = xmlNode.getPathData()[1].type;//pathSegList.getItem(1).pathSegTypeAsLetter;
        if (pathN == "A") {
            return true;
        } else {
            return false;
        }
    }

    function assgnObj(svgNode) {
		if (!svgNode) return null;
        switch (svgNode.nodeName) {
            case "circle":
                {
                    return _mySvg.circle(svgNode);
                } break;
            case "line":
                {
                    return _mySvg.line(svgNode);
                } break;
            case "xaxis":
            case "yaxis":
                {
                    return _mySvg.axis(svgNode);
                } break;
            case "polyline":
                {
                    return _mySvg.polyline(svgNode);
                } break;
            case "path":
                {
                    if (isPathArc(svgNode)) {
                        return _mySvg.arc(svgNode);
                    } else {
                        return _mySvg.path(svgNode);
                    }
                } break;

            case 'rect':
                {
                    return _mySvg.rect(svgNode);
                } break;
            case 'text':
                {
                    return _mySvg.text(svgNode);
                } break;
            case 'image':
                {
                    return _mySvg.image(svgNode);
                    break;
                }
			case 'g':
				{
					return _mySvg.g(svgNode);
					break;
				}
            default: return svgNode;
        }
    }
    _mySvg.svgparse = function (childStr) {
        var loadXML4IE = function (data) {
            var xml = new ActiveXObject('Microsoft.XMLDOM');
            xml.validateOnParse = false;
            xml.resolveExternals = false;
            xml.async = false;
            xml.loadXML(data);
            if (xml.parseError.errorCode != 0) {
                reportError(xml.parseError.reason);
                return null;
            }
            return xml;
        };
        var d = ($.browser.msie ? loadXML4IE(childStr) : new DOMParser().parseFromString(childStr, 'text/xml'));
        return assgnObj(d.firstChild);
    };

    _mySvg.parseNode = function (svgNode) {
		//console.log("svg.nodes"+",   "+svgNode);
        return assgnObj(svgNode);
    };

    _mySvg.circle = function (xmlNode) {
        var _circle = {};
        var circleObj = xmlNode;
        var nodeName = xmlNode.nodeName;
        var cx = parseFloat(circleObj.getAttribute("cx"));
        var cy = parseFloat(circleObj.getAttribute("cy"));
        var r = parseFloat(circleObj.getAttribute("r"));
        _circle.getnodeName = function () {
            return nodeName;
        };
        _circle.getCenter = function () {
            var c = {
                X: cx,
                Y: cy
            };
            return c;
        };
        _circle.getCx = function () {
            return cx;
        };
        _circle.getCy = function () {
            return cy;
        };
        _circle.getRadius = function () {
            return r;
        };
        _circle.getMaxX = function () {
            return (cx + r);
        };
        _circle.getMaxY = function () {
            return (cy + r);
        };
        _circle.getMinX = function () {
            return (cx - r);
        };
        _circle.getMinY = function () {
            return (cy - r);
        };
        return _circle;
    };

    _mySvg.line = function (xmlNode) {
        var _line = {};
        var lineObj = xmlNode;
        var nodeName = xmlNode.nodeName;
        var startPt = {
            X: parseFloat(lineObj.getAttribute("x1")),
            Y: parseFloat(lineObj.getAttribute("y1"))
        };
        var endPt = {
            X: parseFloat(lineObj.getAttribute("x2")),
            Y: parseFloat(lineObj.getAttribute("y2"))
        };
        _line.getnodeName = function () {
            return nodeName;
        };
        _line.getMinX = function () {
            return Math.min(startPt.X, endPt.X);
        };
        _line.getMaxX = function () {
            return Math.max(startPt.X, endPt.X);
        };
        _line.getMinY = function () {
            return Math.min(startPt.Y, endPt.Y);
        };
        _line.getMaxY = function () {
            return Math.max(startPt.Y, endPt.Y);
        };
        _line.isHorizontal = function () {
            return (startPt.Y == endPt.Y);
        };
        _line.getInitPnt = function () {
            return startPt;
        };
        _line.getFinalPnt = function () {
            return endPt;
        };

        return _line;
    };

    _mySvg.axis = function (xmlNode) {
        var _axis = _mySvg.line(xmlNode);
        var axisObj = xmlNode;
        _axis.getScale = function () {
            return axisObj.getAttribute("scale");
        };
        _axis.getTitle = function () {
            return axisObj.getAttribute("title");
        };
        _axis.Max = function () {
            return axisObj.getAttribute("end");
        };
        _axis.Min = function () {
            return axisObj.getAttribute("start");
        };
        _axis.length = function () {
            if (_axis.getnodeName() == 'xaxis') {
                return (_axis.getMaxX() - _axis.getMinX());
            } else if (_axis.getnodeName() == 'yaxis') {
                return (_axis.getMaxY() - _axis.getMinY());
            }
        };
        return _axis;
    };

    _mySvg.polyline = function (xmlNode) {
        var _polyline = {};
        var polylineObj = xmlNode;
        _polyline.getnodeName = function () {
            return polylineObj.nodeName;
        };
        _polyline.getMinX = function () {
            return Math.min(polylineObj.points.getItem(0).x, polylineObj.points.getItem(1).x);
        };
        _polyline.getMaxX = function () {
            return Math.max(polylineObj.points.getItem(0).x, polylineObj.points.getItem(1).x);
        };
        _polyline.getMinY = function () {
            return Math.min(polylineObj.points.getItem(0).y, polylineObj.points.getItem(1).y);
        };
        _polyline.getMaxY = function () {
            return Math.max(polylineObj.points.getItem(0).y, polylineObj.points.getItem(1).y);
        };
        return _polyline;
    };

    _mySvg.path = function (xmlNode) {
        var _path = {};
        var pathObj = xmlNode;
        var nodename = xmlNode.nodeName;
        var bBox = pathObj.getBBox();
        _path.getnodeName = function () {
            return nodename;
        };
        _path.getWidth = function () {
            return bBox.width;
        };
        _path.getMinX = function () {
            return bBox.x;
        };
        _path.getMaxX = function () {
            return (bBox.x + bBox.width);
        };
        _path.getMinY = function () {
            return bBox.y;
        };
        _path.getMaxY = function () {
            return (bBox.y + bBox.height);
        };
        return _path;
    };

    _mySvg.arc = function (xmlNode) {
        var _arc = _mySvg.path(xmlNode);
        var arcObj = xmlNode;
		var nodename = xmlNode.nodeName;
        var arcPathObj = arcObj.getPathData()[1].values;//.pathSegList.getItem(1);
        var arcStartPt = arcObj.getPathData()[0].values;//.pathSegList.getItem(0);
		_arc.getnodeName = function () {
            return nodename;
        };
        _arc.getCenterId = function () {
            return arcObj.getAttribute("center");
        };
        _arc.getStartPoint = function () {
            var startPt = {
                X: arcStartPt[0],//.x,
                Y: arcStartPt[1]//.y
            };
            return startPt;
        };
        _arc.getEndPt = function () {
            var endPt = {
                X: arcPathObj[5],//.x,
                Y: arcPathObj[6]//.y
            };
            return endPt;
        };

        _arc.getAngle = function () {
            return arcPathObj[2];//.angle;
        };
        _arc.getRadius = function () {
            if (arcPathObj[0]/*.r1*/ == arcPathObj[1]/*.r2*/) {
                return arcPathObj[0]/*.r1*/;
            } else {
                return 0;
            }
        };
        return _arc;
    };

    _mySvg.rect = function (xmlNode) {
        var _rect = {};
        var rectObj = xmlNode;
        var nodename = xmlNode.nodeName;
        _rect.getnodeName = function () {
            return nodename;
        };
        return _rect;
    };

    _mySvg.text = function (xmlNode) {
        var _text = {};
        var textObj = xmlNode;
        var nodename = xmlNode.nodeName;
        var bBox;
        try {
            bBox = textObj.getBBox();
        } catch(exception) {
            bBox = {
                x: 0,
                y: 0,
                width: 0,
                height: 0,
            };
        }
        _text.getnodeName = function () {
            return nodename;
        };
        _text.getX = function () {
            return parseFloat(textObj.getAttribute('x'));
        };
        _text.getY = function () {
            return parseFloat(textObj.getAttribute('y'));
        };
        _text.getWidth = function () {
            return bBox.width;
        };
        _text.getMinX = function () {
            return bBox.x;
        };
        _text.getMaxX = function () {
            return (bBox.x + bBox.width);
        };
        _text.getMinY = function () {
            return bBox.y;
        };
        _text.getMaxY = function () {
            return (bBox.y + bBox.height);
        };

        return _text;
    };

    _mySvg.image = function (xmlNode) {
        var _image = {};
        var imageObj = xmlNode;
        var nodename = xmlNode.nodeName;
        // var bBox = imageObj.getBBox();
        _image.getnodeName = function () {
            return nodename;
        };
        _image.getX = function () {
            return parseFloat(imageObj.getAttribute("x"));
        };
        _image.getY = function () {
            return parseFloat(imageObj.getAttribute("y"));
        };
        _image.getWidth = function () {
            return parseFloat(imageObj.getAttribute("width"));
        };
        _image.getHeight = function () {
            return parseFloat(imageObj.getAttribute("height"));
        };
        _image.getRotationPoint = function () {

            var returnPt = {
                X: (_image.getX() + (_image.getWidth() / 2)),
                Y: (_image.getY() + _image.getHeight() - 14)
            };
            return returnPt;
        };

        _image.getRotationInfo = function () {
            var transformStr = imageObj.getAttribute("transform");
            if (transformStr) {
                var infoStr = transformStr.match(/[-.0-9\s]+/g);
                var infoArr = infoStr[0].split(" ");
                var currRotation = {
                    angle: parseFloat(infoArr[0]),
                    X: parseFloat(infoArr[1]),
                    Y: parseFloat(infoArr[2])
                };
                return currRotation;
            } else {
                return null;
            }
        };
        _image.getRotationAngle = function () {
            var rotateObj = _image.getRotationInfo();
            if (rotateObj) {
                return parseFloat(rotateObj.angle);
            } else {
                return 0;
            }
        };
        return _image;
    };

	_mySvg.g = function (xmlNode) {
        var _g = {};
        var gObj = xmlNode;
        var nodename = xmlNode.nodeName;
        var bBox = gObj.getBBox();
        _g.getnodeName = function () {
            return nodename;
        };
		_g.getX = function () {
            return parseFloat((gObj.getAttribute("label").split(" ")[0]));
        };
        _g.getY = function () {
            return parseFloat((gObj.getAttribute("label").split(" ")[1]));
        };
		_g.getIniX = function () {
            return parseFloat((gObj.getAttribute("init").split(" ")[0]));
        };
        _g.getIniY = function () {
            return parseFloat((gObj.getAttribute("init").split(" ")[1]));
        };
		_g.getIniA = function () {
            return parseFloat((gObj.getAttribute("init").split(" ")[2]));
        };
		_g.getRotationPoint = function () {

            var returnPt = {
                X: (_g.getIniX()+_g.getX()),
                Y: (_g.getIniY()+_g.getY())
            };
            return returnPt;
        };

        _g.getRotationInfo = function () {
            var transformStr = gObj.getAttribute("transform");
            if (transformStr && transformStr.indexOf("rotate(")>=0) {
				transformStr=transformStr.slice(transformStr.indexOf("rotate("),transformStr.indexOf(")",transformStr.indexOf("rotate(")));
                var infoStr = transformStr.match(/[-.0-9\s]+/g);
                var infoArr = infoStr[0].split(" ");
                var currRotation = {
                    angle: parseFloat(infoArr[0]),
                    X: parseFloat(infoArr[1]),
                    Y: parseFloat(infoArr[2])
                };
                return currRotation;
            } else {
                return null;
            }
        };
        _g.getRotationAngle = function () {
            var rotateObj = _g.getRotationInfo();
            if (rotateObj) {
                return parseFloat(rotateObj.angle);
            } else {
                return 0;
            }
        };
        return _g;
    };

    return _mySvg;
} ();

var mySvg;
if (mySvg === undefined) {
    mySvg = MySvg;
}