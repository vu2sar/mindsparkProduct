var Points=new Array();
var geometryTools = {
    compass: {
        autoShow: false,
        layout: {
            x: 250,
            y: 296,
            angle: 0,
            radius: 2,
            flip: 1,
        },
        mobility: {
            translate: true,
            rotate: true,
            extend: true,
            draw: true,
            flip: true,
        },
    },
    protractor: {
        autoShow: false,
        layout: {
            x: 250,
            y: 250,
            angle: 0,
        },
        mobility: {
            translate: true,
            rotate: true,
        },
    },
    ruler: {
        autoShow: false,
        layout: {
            x: 50,
            y: 150,
            angle: 0,
        },
        mobility: {
            translate: true,
            rotate: true,
        },
    },
};
var compassState="off";
var compPos={};
var params = {};
var _touchMoved=false;
var _touched=false;

$(function(){
	document.addEventListener("touchstart", touchHandler, true);
  document.addEventListener("touchmove", touchHandler, true);
  document.addEventListener("touchend", touchHandler, true);
  document.addEventListener("touchcancel", touchHandler, true);
  document.addEventListener("tap", touchHandler, true);
  geometry.init(); drawcode.init();
});

function touchHandler(event)
{
    var touches = event.changedTouches,
        first = touches[0],
        type = "";
    //alert(event.type+'  ||  '+event.target.id);
    switch(event.type)
    {
        case "touchstart": type = "mousedown"; break;//_touched=true;_touchMoved=false;break;
        case "touchmove":  type="mousemove"; break;// if(_touched) _touchMoved=true;break;        
        case "touchend":   type="mouseup"; break;// if(!_touchMoved && event.target.tagName.toLowerCase()=='circle') type="click";_touched=false;break;
        case "touchcancel":   type="mouseup"; break;
        default: return;
    }
		
    //alert(event.type+'-----------------'+event.target.tagName+'-----------------'+type);
    //initMouseEvent(type, canBubble, cancelable, view, clickCount, 
    //           screenX, screenY, clientX, clientY, ctrlKey, 
    //           altKey, shiftKey, metaKey, button, relatedTarget);
	
    var simulatedEvent = document.createEvent("MouseEvent");
    simulatedEvent.initMouseEvent(type, true, true, window, 1, 
                              first.screenX, first.screenY, 
                              first.clientX, first.clientY, false, 
                              false, false, false, 0, null);

    first.target.dispatchEvent(simulatedEvent);
    //event.preventDefault();
}
