var cX = 0; var cY = 0; var rX = 0; var rY = 0;
var scaleFactor = 1;
function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;}
function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;}
if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; }
else { document.onmousemove = UpdateCursorPosition; }

$(window).resize(function(){
	scaleGlossaryDiv();
})
function AssignPosition()
{
	if(window.pageYOffset)
	{
		rX = window.pageXOffset;
		rY = window.pageYOffset;
	}
	else if(document.documentElement && document.documentElement.scrollTop)
	{
		rX = document.documentElement.scrollLeft;
		rY = document.documentElement.scrollTop;
	}
	else if(document.body)
	{
		rX = document.body.scrollLeft;
		rY = document.body.scrollTop;
	}
	if(document.all)
	{
		cX += rX;
		cY += rY;
	}
	var docWidth = $(window).width();
	var docHeight = $(window).height();
	var finalX = cX+10;
	var finalY = cY+10;
	if(finalX + 655 > docWidth)//655 is model width
		finalX = docWidth - 665;
	if(finalY + 485 > docHeight)//485 is model height
		finalY = docHeight - 495;
	d = document.getElementById("glossaryDiv");
	d.style.left = finalX + "px";
	d.style.top = finalY + "px";
}
$(document).ready(function(e) {
	$("#overlay, #glossaryClose").live("click",function(){
		$("#overlay").remove();
		$("#glossaryDiv").hide();
	})
});
function removeNestedGlossary()
{
	$(".glossarySpan").each(function(index, element) {
        $(this).html($(this).text());
    });
	//$(".glossarySpan").append('<span class="questionMark"><sup>?</sup></span>');
}
function addGlossary(elementId, text, indexWOspace, index)
{
    var elementHtml = document.getElementById(elementId).innerHTML;
    var tags = [];
    var tagLocations= [];
    var htmlTagRegEx = /<(?:.|\n)*?>/;

    //Strip the tags from the elementHtml and keep track of them
    var htmlTag;
    while(htmlTag = elementHtml.match(htmlTagRegEx))
	{
        tagLocations[tagLocations.length] = elementHtml.search(htmlTagRegEx);
        tags[tags.length] = htmlTag;
        elementHtml = elementHtml.replace(htmlTag, '');
    }

    //Search for the text in the stripped html
	var searchTextNew = text;
	var searchHTML = elementHtml;
    var textLocation = searchHTML.indexOf(searchTextNew);
    if(textLocation != -1)
	{
        //Add the highlight
		var myRegxp = /^([a-zA-Z0-9_-]+)$/;
		if(myRegxp.test(searchHTML.charAt(textLocation-1)) != false)
		{
			return false;
		}
		if(myRegxp.test(searchHTML.charAt(textLocation+searchTextNew.length)) != false)
		{
			return false;
		}

        var highlightHTMLStart = '<div style="display:inline" class="glossarySpan" title="Click here to see glossary!" onclick="getGlossaryDetails(\''+indexWOspace+'\')">';
        var highlightHTMLEnd = '</div>';
        var textEndLocation = textLocation + text.length - 1;

        elementNewHtml = elementHtml.replace(text, highlightHTMLStart + text + highlightHTMLEnd);

        //plug back in the HTML tags
        for(i=tagLocations.length-1; i>=0; i--)
		{
            var location = tagLocations[i];
            if(location > textEndLocation)
			{
                location += highlightHTMLStart.length + highlightHTMLEnd.length;
            }
			else if(location > textLocation)
			{
                location += highlightHTMLStart.length;
            }
            elementNewHtml = elementNewHtml.substring(0,location) + tags[i] + elementNewHtml.substring(location);
        }
		document.getElementById(elementId).innerHTML = elementNewHtml;
    }
}
function showGlossaryWindow()
{
	$("body").append('<div id="overlay"></div>');
	$('#overlay').css({
		 width:  $(document).width(),
		 height:  $(document).height()
	});
	if($("#glossaryDiv").is(":visible"))
		$("#glossaryDiv").hide();
	$("#glossaryDiv").show();
	scaleGlossaryDiv();
}
function scaleGlossaryDiv()
{
	if(window.innerWidth && window.innerHeight && (window.innerWidth < 900 || window.innerHeight < 500))
	{
		scaleFactor = 0.7;
	}
	else
	{
		scaleFactor = 1;
	}
	$("#glossaryDiv").css({"-webkit-transform": "scale("+scaleFactor+")"});
	$("#glossaryDiv").css({"-moz-transform": "scale("+scaleFactor+")"});
	$("#glossaryDiv").css({"-o-transform": "scale("+scaleFactor+")"});
	$("#glossaryDiv").css({"transform": "scale("+scaleFactor+")"});
	AssignPosition();
}
function getGlossaryDetails(keyTerm,setPosition)
{
	setPosition = typeof setPosition !== 'undefined' ? setPosition : true;
	keyTerm = keyTerm.split(' ').join('_');
	var details = glossaryDescArray[keyTerm]['description'];
	var mainTerm = glossaryDescArray[keyTerm]['mainTerm'];
	var relatedTerms = glossaryDescArray[keyTerm]['relatedTerms'];

	var d = "glossaryDiv";
	if(d.length < 1) { return; }

	var dd = document.getElementById(d);
	$("body").append('<div id="dummyDiv"></div>');
	$("#dummyDiv").html(details);
	$("#glossaryImage").html('');
	$("#relatedGlossary").html('');
	$("#dummyDiv").find("img").appendTo("#glossaryImage");
	$("#dummyDiv").find("img").remove();
	var imgCount = $("#glossaryImage img").length;
	$("#glossaryImage img").each(function(index, element) {
		$(this).load(function(e) {
			if($(this).width() > $(this).parent().width())
			{
				var ratio = $(this).parent().width() / $(this).width();
				var newWidth = Math.floor($(this).width() * ratio);
				var newHeight = Math.floor($(this).height() * ratio);
				$(this).width(newWidth);
				$(this).height(newHeight);
			}
        });
    });
	//$("#glossaryImage img").css("max-width",$("#glossaryImage img").parent().width() / imgCount);
	//$("#glossaryImage img").css("max-height",$("#glossaryImage img").parent().height() / imgCount);
	$("#glossaryBody").html($("#dummyDiv").html());
	$("#glossaryTitle").text(toTitleCase(mainTerm));
	$("#dummyDiv").remove();
	if(relatedTerms != "")
	{
		var relatedArray = relatedTerms.split(",");
		$("#relatedGlossary").html('<strong>Related Terms: </strong>');
		$.each(relatedArray,function(index,term){
			term = $.trim(term);
			$("#relatedGlossary").append('&nbsp;<a href="javascript:void(0)" onclick="loadGlossaryAjax(\''+term+'\')">'+toTitleCase(term)+'</a>');
		})
	}
	if(setPosition)
		AssignPosition();
	showGlossaryWindow();
}
function loadGlossaryAjax(term)
{
	indexWOspace = term.split(' ').join('_');
	if(glossaryDescArray[indexWOspace])
	{
		getGlossaryDetails(indexWOspace,false);
	}
	else
	{
		$.get("functions/ajaxGlossary.php?fetchSingleTerm="+term,function(data){
			data=JSON.parse(data);
			if(data!=null)
			{
				for(index in data)
				{
				//$.each(data,function(index, value){
					value = data[index];
					indexWOspaceNew = index.split(' ').join('_').toUpperCase();

					glossaryDescArray[indexWOspaceNew] = new Array();
					glossaryDescArray[indexWOspaceNew]['description'] = value.description;
					glossaryDescArray[indexWOspaceNew]['mainTerm'] = value.mainTerm;
					glossaryDescArray[indexWOspaceNew]['relatedTerms'] = value.relatedTerms;
				//})
				}
				getGlossaryDetails(indexWOspace,false);
			}
		})
	}
}
function toTitleCase(str)
{
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}

function findVariants(term)
{
	term = $.trim(term);
	var allPossibles = new Array();

	allPossibles.push(term);
	if(term.match(/\s/g))
	{
		if(term.match(/\s/g).length > 0)
		{
			allPossibles.push(term.split(" ").join("-"));
			allPossibles.push(term.charAt(0).toUpperCase() + term.slice(1));
		}
		if(term.match(/\s/g).length > 1)
		{
			allPossibles = spaceReplace(allPossibles,term);
		}
		else
		{
			allPossibles.push(term.split(" ").join("&nbsp;"));
		}
	}
	allPossibles.push(toTitleCase(term));
	return(allPossibles);
}
function spaceReplace(allPossibles, term)
{
	term = $.trim(term);
	if(term.match(/\s/g))
	{
		var noOfSpaces = term.match(/\s/g).length;
		for(var i=0;i<noOfSpaces;i++)
		{
			//var newString = term.replace(" ","&nbsp;");
			var newString = replacenth(term,i);
			allPossibles.push(newString);
			allPossibles = spaceReplace(allPossibles,newString);
		}
	}
	return allPossibles;
}
function replacenth(term,i)
{
	term = $.trim(term);
	if(term.match(/\s/g))
	{
		var returnStr = "";
		var dummyArray = new Array();
		dummyArray = term.split(" ");
		for(var j=0;j<dummyArray.length;j++)
		{
			if(j == i)
				returnStr += dummyArray[j]+"&nbsp;";
			else
				returnStr += dummyArray[j]+" ";
		}
	}
	else
		var returnStr = term;
	return returnStr;
}
function showGlossary(qcode,dispAns)
{
	$.get("functions/ajaxGlossary.php","qcode="+qcode,function(data){
		data=JSON.parse(data);
		if(data!=null)
		{
			for(index in data)
			{
				//$.each(data,function(index, value){
				value = data[index];
				indexWOspace = index.split(' ').join('_').toUpperCase();

				var replaceDta	=	"<span style='color:blue;cursor:pointer' onclick=\"getGlossaryDetails('"+indexWOspace+"')\">"+index+"</span>";
				var highlightStartTag	=	"<span class='glossarySpan' title='Click here to see glossary!' onclick=\"getGlossaryDetails('"+indexWOspace+"')\">";
				var highlightEndTag		=	"</span>";

				glossaryDescArray[indexWOspace] = new Array();
				glossaryDescArray[indexWOspace]['description'] = value.description;
				glossaryDescArray[indexWOspace]['mainTerm'] = value.mainTerm;
				glossaryDescArray[indexWOspace]['relatedTerms'] = value.relatedTerms;

				var searchTerm	=	index;

				if(dispAns!='') //for display ans
				{
					//Calling Function For all possible varients..
					var possibleTerms = findVariants(searchTerm);
					$.each(possibleTerms,function(key,singleTerm){
						addGlossary('displayanswer', $.trim(singleTerm), indexWOspace, index);
					})
				}
				else	//for question
				{
					//
					var possibleTerms = findVariants(searchTerm);
					$.each(possibleTerms,function(key,singleTerm){
						addGlossary('q2', $.trim(singleTerm), indexWOspace, index);
					})
				}
				//})
			}
			if($("input[type=text]:visible:first").length > 0)
				$("input[type=text]:visible:first").focus();
			else if($("select:visible:first").length > 0)
				$("select:visible:first").focus();
		}
		removeNestedGlossary();
	});
}