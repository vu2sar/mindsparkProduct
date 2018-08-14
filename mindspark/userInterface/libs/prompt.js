function Prompt(config) {
    this.promptText = config.text;
    this.promptType = config.type;
    this.butt1Txt = config.label1 || false;
    this.butt2Txt = config.label2 || false;
    this.butt1func = config.func1 || false;
    this.butt2func = config.func2 || false;
    this.containerID = config.promptId || "";
    this.showPrompt();
}
var func1, func2;
Prompt.prototype.showPrompt = function () {
    jQuery('head').append('<link href="/mindspark/userInterface/css/prompt.css" rel="stylesheet" type="text/css">');
    if (this.containerID == "")
        var prmpHtml = '<div id="prmptContainer" class="promptContainer"><div id="promptBox">';
    else
        var prmpHtml = '<div id="prmptContainer_' + this.containerID + '" class="promptContainer"><div id="promptBox">';
    prmpHtml += '<div id="promptText">' + this.promptText + '</div><br/>';
    if (this.promptType == 'confirm') {
        func1 = this.butt1func;
        func2 = this.butt2func;
        if (!this.butt1Txt)
            prmpHtml += '<button class="prmptbutton butt1"  >Yes</button>';
        else
            prmpHtml += '<div class="prmptbutton butt1"  >' + this.butt1Txt + '</div>';
        if (!this.butt2Txt)
            prmpHtml += '<button class="prmptbutton butt2"  >No</button>';
        else
            prmpHtml += '<div class="prmptbutton butt2"  >' + this.butt2Txt + '</div>';
    }
    else if (this.promptType == 'alert') {
        func1 = this.butt1func;
		if (!this.butt1Txt)
            prmpHtml += '<button class="prmptbutton butt1" >OK</button>';
        else
            prmpHtml += '<div class="prmptbutton butt1"  >' + this.butt1Txt + '</div>';
    }
    prmpHtml += '</div></div>';
    jQuery('body').append(prmpHtml);
    if (this.containerID == "")
        jQuery("#prmptContainer").show();
    else
        jQuery("#prmptContainer_" + this.containerID).show();
    jQuery(".prmptbutton").click(function() {
        if (jQuery(this).hasClass('butt1') && func1 != false)
            func1();
        else if (jQuery(this).hasClass('butt2') && func2 != false)
            func2();
        //jQuery("#prmptContainer").remove();
    });
}

//jQuery(document).ready(function () {
//    jQuery(".prmptbutton").live('click', function () {
//       if (jQuery(this).hasClass('butt1') && func1 != false)
//            func1();
//        else if (jQuery(this).hasClass('butt2') && func2 != false)
//            func2();
//        //jQuery("#prmptContainer").remove();
//    });
//});
