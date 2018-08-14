var cnt = 0;
var tablet = osDetection();
//tablet = 1;
var activeInputElements = new Array();
var activeInputElements2 = new Array();
var firstTime = 1;
var firstTime2 = 1;
(function (jQuery) {
    var IS_IOS = /iphone|ipad/i.test(navigator.userAgent);
    jQuery.fn.nodoubletapzoom = function () {
        if (1)
            jQuery(this).live('touchstart', function preventZoom(e) {
                var t2 = e.timeStamp
          , t1 = jQuery(this).data('lastTouch') || t2
          , dt = t2 - t1
          , fingers = e.originalEvent.touches.length;

                jQuery(this).data('lastTouch', t2);
                if (!dt || dt > 500 || fingers > 1) return; // not double-tap
                e.preventDefault(); // double tap - prevent the zoom
                // also synthesize touchend events we just swallowed up
                jQuery(this).trigger('touchend').trigger('touchend');
            });
    };
})(jQuery);
$(document).ready(function (e) {
    //adding keyPad Styles scripts to the header
    $("head").append('<link type="text/css" href="../../../libs/keypad2/jquery.keypad.css" rel="stylesheet">');
    $("head").append('<script type="text/javascript" src="../../../libs/keypad2/jquery.keypad.js"></script>');
    $("head").append('<link type="text/css" href="../../../libs/keypad2/keypadCustomStyle.css" rel="stylesheet">');
    attachKeypad();
    //removing Elements that have lost focus
    $("input[type=text]").live("blur", function () {
        var id = this.id;
        if (in_array(id, activeInputElements) || in_array(id, activeInputElements2)) {
            var index = activeInputElements.indexOf(id);
            var index2 = activeInputElements2.indexOf(id);
            //activeInputElements.splice(index, 1);
            //activeInputElements2.splice(index2, 1);
        }
    });

    //binding all input Elements of type 'text' here
    // click tap vclick
    $("input[type=text]").live("focus click tap vclick", function () {
      
    });
});
function attachKeypad() {
    if (tablet) {
        var id = this.id;
        $(this).attr('readonly', true);
        if (firstTime) {
            firstTime = 0;
            $.keypad.addKeyDef('FLIP', 'flip', function (inst) {
                cnt++;
                if (cnt % 2 != 0) {	// why not have this css on sheets. anyways no issues
                    $(".keypad-popup").css({
                        'top': '-10px',
                        'left': '710px',
                        'width': '80px',
                        'height': '552px',
                        'float': 'right',
                        'padding-top': '40px'
                    });
                }
                else {
                    $(".keypad-popup").css({
                        'top': '526px',
                        'left': '0px',
                        'width': '790px',
                        'height': '50px',
                        'float': 'left',
                        'padding-top': '5px'
                    });
                }
            });
        }
        
        $("input[type=text]").keypad({
            flipText: 'Flip',
            flipStatus: 'Flip the keypad',
            //changeText: 'Change',
            //changeStatus: 'Change the keypad',
            layout: ['1234', '567', '890', '-/.', $.keypad.ENTER + $.keypad.CLOSE + $.keypad.BACK + $.keypad.CLEAR + $.keypad.FLIP, /*, $.keypad.CHANGE*/],
            keypadClass: 'midnightKeypad',
            prompt: 'Enter values using this keypad.', closeText: 'X', clearText: 'Clear', backText: '&#8592;', enterText: '&#8629;',
            //showOn: 'button',
            onKeypress: function (key, value, inst) {
                $(this).val($(this).val().replace(" ", ""));
                var e = $.Event("keypress", { keyCode: key.charCodeAt(0) });
                var e1 = $.Event("keyup", { keyCode: key.charCodeAt(0) });
                var e2 = $.Event("keydown", { keyCode: key.charCodeAt(0) });
                $(this).trigger(e);
                $(this).trigger(e2);
                $(this).trigger(e1);

            },
            beforeShow: function (div, inst) {

                window.setTimeout(function () {
                   if (cnt % 2 != 0) {
                        $(".keypad-popup").css({
                            'top': '-10px',
                            'left': '710px',
                            'width': '80px',
                            'height': '552px',
                            'float': 'right',
                            'padding-top': '40px'
                        });
                    }
                    else {
                        $(".keypad-popup").css({
                            'top': '526px',
                            'left': '0px',
                            'width': '790px',
                            'height': '50px',
                            'float': 'left',
                            'padding-top': '5px'
                        });

                    }
                    $(".keypad-close").attr('id', 'closed');

                }, 0);
            },
            showAnim: ''
        });
        //}
        //$(this).blur();
        if (firstTime2) {
            firstTime2 = 0;
            $(".keypad-popup").draggable(
                    {
                        'containment': '#container'
                    });
        }
        //adding the current element if not already added. If added just show the keypad
        //if (!in_array(id, activeInputElements)) {
        //    activeInputElements.push(id);
        //    $(this).blur().select();
        //    $(this).focus().select();
        //    //alert();
        //    if (firstTime) {
        //        firstTime = 0;
        //        $(".keypad-popup").draggable(
        //            {
        //                'containment': '#container'
        //            });
        //    }
        //}
        //else {

        //    $('.keypad-popup').show();
        //}
    }
}
function tabKeypad(className) {

    if (osDetection()) {
        //$("#"+className).keypad();
    }
}

// simulates an artificial keypress event from key pad
function simulateKeyPress(character) {
    jQuery.event.trigger({ type: 'keypress', which: character.charCodeAt(0) });
}