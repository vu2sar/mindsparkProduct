$(document).ready(function (e)
{
    $("head").append('<link type="text/css" href="../../../libs/keypad1/jquery.keypad.css" rel="stylesheet">');
    $("head").append('<script type="text/javascript" src="../../../libs/keypad1/jquery.keypad.js"></script>');
    $("head").append('<link type="text/css" href="../../../libs/keypad1/keypadCustomStyle.css" rel="stylesheet">');
    $(".txt").live("focus", function ()
    {
		
        if (osDetection())
        {
		
            var id = $(this).attr('id');
            $("#" + id).keypad({
              layout: ['1234', '5678', '890', $.keypad.CLEAR + $.keypad.CLOSE + $.keypad.BACK + $.keypad.ENTER],
              keypadClass: 'midnightKeypad',
               prompt: 'Enter values using<br> this keypad.', closeText: 'X', clearText: 'Clear', backText: '&#8592;', enterText: '&#8629;',
			    onKeypress: function (key, value, inst)
                {
						
                    //console.log(key,value,inst);
                    //jQuery.event.trigger({ type: 'keypress', which: value.charCodeAt(0) });
                    var e = $.Event("keydown", { keyCode: key.charCodeAt(0) });
                    $("#" + id).trigger(e);
                    var e1 = $.Event("keypress", { keyCode: key.charCodeAt(0) });
                    $("#" + id).trigger(e1);
                    var e2 = $.Event("keyup", { keyCode: key.charCodeAt(0) });
                    $("#" + id).trigger(e2);
                },
                beforeShow: function (div, inst)
                {
                    //alert('sds');
                   // $("#title").html(orient);
				   
                 window.setTimeout(function ()
                    {

                        $(".keypad-popup").css({
                           
                            'width': 'auto'
                           
                        });
                        $(".keypad-close").attr('id', 'closed');

                    }, 10);
                },
                showAnim: ''
            });

        }

    });
    var win = window;
    if (window.parent != null)
        win = window.parent;
  /*  $(win).on("orientationchange", function (event)
    {
        //$("#title").text("This device is in " + event.orientation +"  ==  "+window.orientation+ " mode!");
        var android = (navigator.userAgent.indexOf("Android") != -1);
        var ipad = (navigator.userAgent.indexOf("iPhone") != -1) || (navigator.userAgent.indexOf("iPod") != -1) || (navigator.userAgent.indexOf("iPad") != -1);
        var orient = win.orientation;
        //alert('kgh ')
      //  $("#title").html(orient);
        if (ipad)
        {
            if (orient == 90 || orient == -90)
            {
                $(".keypad-popup").css({
                    'top': '-10px',
                    'left': '700px',
                    'width': '90px',
                    'height': '585px',
                    'float': 'right'
                });

            }
            else
            {
                $(".keypad-popup").css({
                    'top': '500px',
                    'left': '0px',
                    'width': '790px',
                    'height': '75px',
                    'float': 'left'
                });
            }
        }
        else if (android)
        {
            if (orient == 90 || orient == -90)
            {
                $(".keypad-popup").css({
                    'top': '500px',
                    'left': '0px',
                    'width': '790px',
                    'height': '75px',
                    'float': 'left'
                });

            }
            else
            {

                $(".keypad-popup").css({
                    'top': '-10px',
                    'left': '700px',
                    'width': '90px',
                    'height': '585px',
                    'float': 'right'
                });
            }
        }
    });*/
});

function tabKeypad(className){

    if (osDetection()) {
        //$("#"+className).keypad();
    }
}
function simulateKeyPress(character) {
  jQuery.event.trigger({ type : 'keypress', which : character.charCodeAt(0) });

}