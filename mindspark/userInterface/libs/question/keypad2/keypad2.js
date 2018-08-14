var cnt = 0;
var tablet = osDetection();
var removedKeypad = new Array();
var firstTime = 1;
var firstTime2 = 1;
var a = 1, inner, intW, intH;
var timer;
var color;
var src = '';
var sourceKeypad = '';
var id2;
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
jQuery(document).ready(function (e) {
    if (tablet) {
        jQuery('body').append('<div id="keypadFake" style="display:none"></div>');
    }
    var time1;
    jQuery(".keypad-back").live('touchstart', function (inst) {
        if ((sourceKeypad != 'competetiveExam')) {
            window.clearInterval(time1);
            time1 = window.setInterval(function (inst) {
                var newVal = '';
                for (var i = 0; i < jQuery("#" + id2).val().length - 1; i++) {
                    newVal += jQuery("#" + id2).val()[i]
                }
                jQuery("#" + id2).val(newVal);
            }, 300);
        }

    });
    jQuery(document).live('touchend', function () {
        window.clearInterval(time1);
    });

    jQuery("input[type=text]").live("focus", function (event) {
        id2 = jQuery(this).attr('id');
        if ((sourceKeypad == 'questions' || sourceKeypad == 'competetiveExam') && jQuery(this).attr('keypadAttached') == 'true') {
            jQuery('.keypad-popup').show();
            var keyid = jQuery(this).attr('keypadid');
            var flag = 0;
            jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
            color = jQuery('input[keypadid=' + keyid + ']').css('border-color');
            window.clearInterval(timer);
            timer = window.setInterval(function () {
                var a = jQuery('input[keypadid=' + keyid + ']').val();
                if (flag == 0) {
                    flag = 1;
                    color = jQuery('input[keypadid=' + keyid + ']').css('border-color');
                    jQuery('input[keypadid=' + keyid + ']').css('border-color', 'yellow');
                }
                else {
                    flag = 0;
                    jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
                }
            }, 500);
        }
    });
});

// simulates an artificial keypress event from key pad
function in_array(what, where) {
    var a = false;
    for (var i = 0; i < where.length; i++) {
        if (what == where[i]) {
            a = true;
            break;
        }
    }
    return a;
}
function osDetection() {

    return (
        (navigator.userAgent.indexOf("iPhone") != -1) ||
        (navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1) || (navigator.userAgent.indexOf("Android") != -1)
    );
}
function attachKeypad(source) {
    if (firstTime) {
        firstTime = 0;
        jQuery.keypad.addKeyDef('ABC', 'abc', function (inst) {
            if (!jQuery(this).hasClass('num_blank')) {
                removedKeypad.push(jQuery(this).attr('keypadAttached'));
                jQuery(this).attr('keypadAttached', 'false');
                jQuery(this).keypad('destroy');
                jQuery(this).removeAttr('readonly');
                jQuery(this).blur().select();
                window.scrollTo(0, 1);
                jQuery(this).focus().select();
                jQuery(this).focus();
                var keyid = jQuery(this).attr('keypadid');
                jQuery('input[keypadid=' + keyid + ']').focus();
                window.clearInterval(timer);
                jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
            }
        });
        jQuery.keypad.addKeyDef('TAB1', 'tab1', function (inst) {
            if (sourceKeypad == 'questions') {
                var blankId = jQuery(this).attr('id');
                var numb = parseInt(blankId.slice(1, blankId.length));
                var nextNo = (numb) % jQuery("input[type=text]").length;
                jQuery("#" + jQuery("input[type=text]")[nextNo].id).focus();
            }
            else if (sourceKeypad == 'timedTest') {
                var blankId = jQuery(this).attr('id');
                var numb = parseInt(blankId.split('_')[1]);
                var nextNo = (numb) % 4;
                jQuery("#" + jQuery("input[type=text]")[3 - nextNo].id).focus();
            }
        });
        jQuery.keypad.addKeyDef('GO', 'go', function (inst) {
            if (source == 'questions') {
                if (!jQuery('#commentBox').is(":visible") && !jQuery('#markedWrongText').is(":visible") && !jQuery('#markedRepeatText').is(":visible")) {
                    if (jQuery('#result').val() == "") {
						if(jQuery("#tmpMode").val() == "NCERT") {
							submitAnswer();
						} else if (newQues.quesType.substring(0, 3) == "MCQ") {
                            alert("Please specify your answer!");
                        } else {
                            submitAnswer();
						}
                    }
                    else if (jQuery("#tmpMode").val() != "practice" && jQuery("#tmpMode").val() != "NCERT" && !jQuery('#pnlLoading').is(":visible")) {
                        if (allowed == 1) {
                            jQuery('#nextQuestion1').css("display", "none");
                            handleClose();
                        }
                    }
                    else {
                        var e = jQuery.Event("keypress", { keyCode: 13 });
                        var e1 = jQuery.Event("keyup", { keyCode: 13 });
                        var e2 = jQuery.Event("keydown", { keyCode: 13 });
                        jQuery(this).trigger(e);
                        jQuery(this).trigger(e2);
                        jQuery(this).trigger(e1);
                    }
                }
                jQuery(".keypad-popup").hide();
                window.clearInterval(timer);
                jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
                var keyid = jQuery(this).attr('keypadid');
                jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
                jQuery('#keypadFake').hide();
            }
            else {
                var e = jQuery.Event("keypress", { keyCode: 13 });
                var e1 = jQuery.Event("keyup", { keyCode: 13 });
                var e2 = jQuery.Event("keydown", { keyCode: 13 });
                jQuery(this).trigger(e);
                jQuery(this).trigger(e2);
                jQuery(this).trigger(e1);
            }
        });
    }
    sourceKeypad = source;
    if (source == 'timedTest')
        var selector = 'input[type="text"]';
    else if (source == 'questions')
        var selector = 'input[type="text"]';
    else
        var selector = 'input[type="text"]';
    jQuery(selector).keypad({
        abcText: 'ABC',
        goText: 'GO',
        tab1Text: 'TAB',
        flipStatus: 'Flip the keypad',
        layout: [jQuery.keypad.CLOSE + '1234567890.' + jQuery.keypad.BACK + jQuery.keypad.GO, jQuery.keypad.TAB1 + '+-/*' + jQuery.keypad.SPACE_BAR + '^%()' + jQuery.keypad.ABC/* + jQuery.keypad.CLEAR + jQuery.keypad.FLIP*/  /*, jQuery.keypad.CHANGE*/],
        keypadClass: 'midnightKeypad',
        closeText: 'X', clearText: 'Clear', backText: '&#8592;', enterText: 'Go', spacebarText: 'Space',
        beforeShow: function (div, inst) {
            jQuery('#keypadFake').show();
            window.setTimeout(function () {
                jQuery(".keypad-popup").nodoubletapzoom();
                jQuery(".keypad-close").attr('id', 'closed');
            }, 0);
            if (jQuery(this).hasClass('num_blank')) {
                jQuery(".keypad-abc").css('opacity', '0.7');
            }
            else {
                jQuery(".keypad-abc").css('opacity', '1');
            }
            cnt++;
            jQuery(this).attr('keypadid', 'box_' + cnt);
            jQuery('#keypadFake').show();
            //if((jQuery("input[type=text]").length-jQuery('.num_blank').length)!=0)
            jQuery(this).attr('keypadAttached', 'true');
            var keyid = jQuery(this).attr('keypadid');
            var flag = 0;
            color = jQuery('input[keypadid=' + keyid + ']').css('border-color');
            window.clearInterval(timer);
            timer = window.setInterval(function () {
                var a = jQuery('input[keypadid=' + keyid + ']').val();
                if (flag == 0) {
                    flag = 1;
                    color = jQuery('input[keypadid=' + keyid + ']').css('border-color');
                    jQuery('input[keypadid=' + keyid + ']').css('border-color', 'yellow');
                }
                else {
                    flag = 0;
                    jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
                }
            }, 500);

        },
        showAnim: '',
        onKeypress: function (key, value, inst) {
			if(typeof key != "undefined") {
				var e = jQuery.Event("keypress", { keyCode: key.charCodeAt(0) });
				var e1 = jQuery.Event("keyup", { keyCode: key.charCodeAt(0) });
				var e2 = jQuery.Event("keydown", { keyCode: key.charCodeAt(0) });
				jQuery(this).trigger(e2);
                jQuery(this).trigger(e);
                jQuery(this).trigger('input');
				jQuery(this).trigger(e1);
			}
        },
        onClose: function (div, inst) {
            jQuery('#keypadFake').hide();
            window.clearInterval(timer);
            var keyid = jQuery(this).attr('keypadid');
            jQuery('input[keypadid=' + keyid + ']').css('border-color', color);
        }

    });

}