/**
 * @author BABA
 */
var msEnglishBucket = 'http://mindspark-lang.s3.amazonaws.com';
var saverLoaderURL = 'src/saverLoader.php';
var dataArr = [];
var masterUserArr=['anand.mishra','sridhar','harsha.dediya','bhushan.kothari','dev.dutta','sarah.dsouza','aarushi.prabhakar','diana.romany','bindu.balan','shriram.chenji','aparna.muralidharan','neeti.singhal'];
var masterEdit=false;
var dicCategoriesAndSubCatObj={};
var dicSubCategories=new Array();

/*
 * overwriting the audio link here. This is not the cloudfront link because the cloudfront files are refreshed
 * every 24 hour cycle but bucket change is immediately reflected to developer. The UI version of Audio uses cloudfront link.
 */
AUDIO.LIVE_CONTENT_LINK = msEnglishBucket + '/';

//holds paths to the project Images
var projectImages = [];
var imageTaggers = {};

/*
 * Initial declare that the passage is always new.
 */
var passageCreatorData = {
    newPassage : true,
    version : 2
};


var timer = null;
var ignoreWords = [];

$(document).ready(function() {
    $("#showMisspelledWords").colorbox({
        inline : true,
        width : "50%"
    });
	getDicCategoriesAndSubCats();
});

function openMisspelledWordsBox() {
    $("#showMisspelledWords").click();
}

window.onload = function() {
    loadXML('src/xml.xml', initializeProgram);
};

$('#workingArea').keydown(function() {
    clearTimeout(timer);
    timer = setTimeout(getReadabilityScore, 500);
});

var PASSAGE_TAGS = {
    'NORMAL_GENRE' : ["Folktale/Fable", "(Auto)Biography", "Science/Tech", "Literature", "Environment", "History", "Modern Fiction"],
    'CONVERSATION_GENRE' : ["History", "Literature", "Environment", "Daily Life", "Current Events", "Science/Tech", "Entertainment"],
    'NORMAL_FORM' : ["Story", "Poem", "Dialog/Speech", "Graphic", "Article", "Epistolary"],
    'CONVERSATION_FORM' : ["Interview", "Discussion", "Argument", "Speech/Drama"]
};

function getDicCategoriesAndSubCats()
{
	$.ajax({
			url:'../ajax/getDicCategoriesAndSubCats.php',
			dataType:'JSON',
			success: function(data){
				$.each(data,function(key,value){
					dicCategoriesAndSubCatObj[key]=value;
				});
			}
		});		
}

function refillSubTypes(typeID){
	var index=typeID[typeID.selectedIndex].id;	
	var rowNo = typeID.id.substr(typeID.id.length - 1);
	$('#optionSubTypes'+rowNo).empty(); //remove all child nodes of subType
	if(dicCategoriesAndSubCatObj[index]['subCategories']!=undefined)
	{	
		$('#optionSubTypes'+rowNo).show();
		$.each(dicCategoriesAndSubCatObj[index]['subCategories'],function(key,value){
			var newOption = $('<option value="'+value['subCategoryName']+'">'+value['subCategoryName']+'</option>');
			$('#optionSubTypes'+rowNo).append(newOption);
			$('#optionSubTypes'+rowNo).trigger("chosen:updated");
		});
	}
	else{
		// assigning blank value option for sending & saving blank value for relative subtype 
		var newOption = $('<option value="">No Option</option>');  
		$('#optionSubTypes'+rowNo).append(newOption);
		$('#optionSubTypes'+rowNo).hide();
	}
}

$('.dictionary').live('change', function (e) {
	var rowCount = $('#mW tr').length - 1;
	var selectedItemId = $(this).attr('id');
	var splittedSelectedItemId = selectedItemId.split('Options');
	var hideElement1Name = '#optionTypes'+splittedSelectedItemId[1];
	var hideElement2Name = '#optionSubTypes'+splittedSelectedItemId[1];
	var optionSelected = $("option:selected", this);
	// console.log($(this).attr('id'));
	// console.log(rowCount);
	if(rowCount==1)
	{
		if(optionSelected[0].innerHTML=="Will edit" || optionSelected[0].innerHTML=="Ignore Word")
		{	
			
			changeColumnAppearence(3,"hide");
			changeColumnAppearence(4,"hide");
		}
		else
		{	
			changeColumnAppearence(3,"show");
			changeColumnAppearence(4,"show");
		}
	}
	else if(rowCount>1)
	{
		if(optionSelected[0].innerHTML=="Will edit" || optionSelected[0].innerHTML=="Ignore Word"){	
			$(hideElement1Name).css('display','none');
			$(hideElement2Name).css('display','none');
		}
		else
		{
			$(hideElement1Name).css('display','block');
			if($(hideElement2Name).val()!="")
				$(hideElement2Name).css('display','block');
		}
	}
});

function getReadabilityScore() {
    var readabilityScoreParams = new FormData();
    readabilityScoreParams.append('textValue', $('#workingArea').html().replace(/<(?:.|\n)*?>/gm, ' '));

    request = $.ajax({
        url : "../TextStatistics/readibility.php",
        cache : false,
        type : "POST",
        data : readabilityScoreParams,
        contentType : false,
        processData : false,
        global : false,
        success : function(data) {
            $('#scoresSection').html(data);
        }
    });
}

function initializeProgram() {
    if($('#qcode').val() != "")
		masterEdit=true;
	rangy.init();
    decodeParams();
    setDiv();
    setBindings();

    audioObject = new Audio($('#audioContainer')[0]);
}

function decodeParams() {
    pageParams = Helpers.getUrlParameters();
}

function setDiv() {
    $('#toolbar').show();
    $('#proofType').show();
    $('#proofFrame').css('overflow', 'hidden');

    $('fieldset legend').append('<button type="button" class="toggleFieldSets"></button>');
    $('fieldset').prepend('<div class="fieldsetSpaceMaker"> </div>');
    populateProofTypeForm();

    $('#editorContainer').draggable({
        containment : 'body'
    });
}

function setBindings() {
    $('#workingArea span[class]').live('mouseenter', function() {
        fileNames = $(this).attr('data-file').split(' ');
        for (var i = 0; i < fileNames.length; i++) {
            $("img[filename='" + decodeURIComponent(fileNames[i]) + "']").parent().addClass('highlight');
        }
    });

    $('#workingArea span[class]').live('mouseout', function() {
        $('.addedImages').removeClass('highlight');
    });

    $('#passageType').bind('change', setType);
    $('#passageForm').bind('change', function() {
        var form = $('#passageForm')[0].value;
        if (form === "Graphic") {
            $('#workingArea').hide();
        } else {
            $('#workingArea').show();
        }
    });

    $('.loader').bind('ajaxStart', function() {
        $(this).show();
    }).bind('ajaxStop', function() {
        $(this).hide();
    });

    /*$('.activeWorkingArea').live('click', onElementClick);*/

    $('fieldset legend').bind('click', function() {
        $(this).parent().toggleClass('closed');
    });

    $('#qcode').bind('keypress', function(event) {
        event.keyCode = event.keyCode || event.which;

        if (event.keyCode === 13) {
            $('#go').trigger('click');
            event.preventDefault();
            $(this).unbind('keypress');
        }
        
        //preventing users to tab across to next element.
        if (event.keyCode == 9) {
            event.preventDefault();
            return false;
        }
    });
}

/*
 * called when user choosed type as conversation
 */
function setForAudioPassage() {
    $('#audioUploader').show();
    $('#panesSelector').hide();
    if (parseInt(passageCreatorData.isAudioUploaded)) {
        $('#audioContainer').show();
        $('#audioRefresh').show();
        $('#noAudioMessage').hide();
        audioObject.view.show(passageCreatorData.passageID);
    } else {
        $('#noAudioMessage').show();
    }
    $('#thisIsListening').hide();
    $('#imageUploader').hide();
    $('#passageStyle').parent().hide();
    Helpers.populateSelectElement($('#passageGenre'), PASSAGE_TAGS.CONVERSATION_GENRE);
    Helpers.populateSelectElement($('#passageForm'), PASSAGE_TAGS.CONVERSATION_FORM);

    setRepopulatedFields();
}

/*
 * call when user chooses type which is not conversation.
 */
function unsetForAudioPassage() {
    $('#audioRefresh').hide();
    $('#audioUploader').hide();
    $('#noAudioMessage').hide();
    $('#workingArea').show();
    $('#panesSelector').show();
    $('#audioContainer').hide();
    $('#noAudioMessage').hide();
    $('#imageUploader').show();
    $('#passageStyle').parent().show();
    Helpers.populateSelectElement($('#passageGenre'), PASSAGE_TAGS.NORMAL_GENRE);
    Helpers.populateSelectElement($('#passageForm'), PASSAGE_TAGS.NORMAL_FORM);

    setRepopulatedFields();
}

/*
 * Because passageType conversation have different genre and Form tags they need to be updated when user changes passageType
 */
function setRepopulatedFields() {
    if ($('#passageForm option[value="' + passageCreatorData.Form + '"]').length >= 1) {
        $('#passageForm')[0].value = passageCreatorData.Form;
    }
    if ($('#passageGenre option[value="' + passageCreatorData.Genre + '"]').length >= 1) {
        $('#passageGenre')[0].value = passageCreatorData.Genre;
    }
}

/*
 * Set passageEditing mode to either Conversation or regular
 */
function setType() {
    var type = $('#passageType')[0].value;

    if (type === "Conversation") {
        setForAudioPassage();
    } else {
        unsetForAudioPassage();
    }
}

/*
 * uploads single/multiple images. On response passage is saved silently
 */
function uploadImage() {
    if ($('#filesToUpload')[0].files.length == 0) {
        alert('Select an image file to upload!!');
        return;
    }

    $('.loader').show();
    $('#projectNameImage').attr('value', passageCreatorData.passageID);
    var httpRequest = new XMLHttpRequest(),
        formData = new FormData(document.getElementById('imageUploader'));

    httpRequest.open('POST', 'src/imageUploader.php', true);
    httpRequest.send(formData);

    httpRequest.onreadystatechange = function() {
        if (httpRequest.readyState === 4 && httpRequest.status === 200) {
            handleImageLoaderResponse(httpRequest.responseText,'noSpellCheck');
            $('.loader').hide();
            saveProject('noMessage',0,'noSpellCheck');
        }
    };
}

/*
 * on successful upload isAudioUploaded flag is set to 1 and passage is saved silently.
 */
function audioUpload() {
    if ($('#audioToUpload')[0].files.length === 0) {
        alert('Select an audio file to upload!!');
        return;
    }

    var data = new FormData();
    $.each($('#audioToUpload')[0].files, function(i, file) {
        data.append('file-' + i, file);
    });
    data.append('passageID', passageCreatorData.passageID);
    $.ajax({
        url : 'src/audioUploader.php',
        data : data,
        cache : false,
        contentType : false,
        processData : false,
        type : 'POST',
        success : function(data) {
            if (data === '1') {
                alert('Your audio has been uploaded.');
                passageCreatorData.isAudioUploaded = 1;
                saveProject('noMessage', setForAudioPassage,'noSpellCheck');
            } else {
                alert(data);
            }
        }
    });
}

/*when the user click go the load save mode is 2 in saverLoader.php
 * 2 means the passage needs to be searched and returned in case it is found. In case it is not found a new entry has to be made and new 
 * passage has to be created on the editor.
 * After getting passage authorization is checked based on whose allotment the passage is and based on the current status.
 */
function onGoClick() {
    if (Helpers.isBlank($('#qcode').attr('value'))) {
        alert('Enter the project name');
        return;
    }
	
    $('#proofFrame').css('overflow', '');

    $('#projectNameExport').attr('value', $('#qcode').attr('value'));

    $('.workingArea').html('');
    $('#workWindow').show();
    $('.toolWindow').show();
	
	
    //$('#spellCheck').css('display', 'block');
    //$('#addToDictionary').css('display', 'block');
	
	setContentToSave();
    //fetch passages if they exist else make an insert
    passageCreatorData.loadSaveMode = '2';
	
	
    $.ajax({
        url : 'src/saverLoader.php',
        type : 'post',
        dataType : 'json',
        data : passageCreatorData
    }).done(function(response) {
        if ( typeof response === 'object') {
            receivePassageContent(response);
            if (parseInt(passageCreatorData.status) === 0) {
                $('#reviewer1').attr('disabled', false);
                $('#reviewer2').attr('disabled', false);
            } else {
                $('#reviewer1').attr('disabled', true);
                $('#reviewer2').attr('disabled', true);
            }

            //passageCreatorData.username = passageCreatorData.passageMaker;
            //dev
            //passageCreatorData.currentAlloted = passageCreatorData.passageMaker;
            if ((passageCreatorData.passageMaker === passageCreatorData.currentAlloted && passageCreatorData.username === passageCreatorData.passageMaker) || masterEdit) {
            //dev
                setLoadedProject();
            } else {
                if (confirm('You are not authorized to edit this passage. Would you like to view it.')) {
                    viewPassage('samePage');
                } else {
                    location.assign(document.URL.split('?')[0]);
                }
            }
        } else {
            newProject(response);
        }
		
       tinymce.init(tinymceConfig1);
	   tinymce.init(tinymceConfig2);
    });
}

function readyPassageCreatorForEditing() {
    $(".ui-menu-item").hide();
    $("#go").hide();

    setType();

    $('#qcode').blur();
    $('#proofFrame').removeClass('closed');
    $('#submitForReviewButton').show();
    $('#submitButton').show();
    $("#publishButton").show();
    $("#save").show();
    $('#proofFrame').css('height', 'auto');
}

/*
 * opens passage in new windows.
 * param1: string: if passed any value evaluated as true the current page is navigated to passage viewer
 * else passage viewer is opened in newTab.
 */
function viewPassage(string) {
    if (string) {
        window.location.assign('../PassageViewer/src/index.html?passageID=' + passageCreatorData.passageID + '&username=' + passageCreatorData.username + '&passageType=' + passageCreatorData.passageType);
    } else {
        window.open('../PassageViewer/src/index.html?passageID=' + passageCreatorData.passageID + '&username=' + passageCreatorData.username + '&passageType=' + passageCreatorData.passageType);
        window.focus();
    }
}

/*
 * start a new project. Initially allotment is the currentuser. passageMaker is the currentUser.
 * Id is got form saverLoader.php after it has made an insert
 */
function newProject(response) {
    passageCreatorData.passageID = parseInt(response);
    passageCreatorData.currentAlloted = passageCreatorData.username;
    passageCreatorData.passageMaker = passageCreatorData.username;
    readyPassageCreatorForEditing();
}

/*
 * Creates the json of existing passage in passageCreatorData
 */
function receivePassageContent(retrievedContent) {
    for (var key in retrievedContent) {
        if (retrievedContent.hasOwnProperty(key)) {
            passageCreatorData[key] = retrievedContent[key];
        }
    }
}

/*
 * Sets property values to their respective field names.
 */
function setLoadedProject() {
	$('#projectImages').html(passageCreatorData.passageImages);
    $('#passageAuthor').attr('value', passageCreatorData.Author);
    $('#passageSource').attr('value', passageCreatorData.Source);
    $('#passageLevel')[0].value = passageCreatorData.msLevel;
    $('#passageType')[0].value = passageCreatorData.passageType;
    $('#passageForm')[0].value = passageCreatorData.Form;
    $('#passageGenre')[0].value = passageCreatorData.Genre;
    $('#passageStyle')[0].value = passageCreatorData.Style;
    $('#reviewer1')[0].value = passageCreatorData.first_alloted;
    $('#reviewer2')[0].value = passageCreatorData.second_alloted;
    $('#projectNameImage')[0].value = passageCreatorData.passageID;
    $('#passageIntro')[0].value = passageCreatorData.intro;
	
    readyPassageCreatorForEditing();
    // indication that the passage is old format when it contains workingArea text
    if (passageCreatorData.passageContent.indexOf('class="workingArea') == -1) {
        $('.workingArea')[0].innerHTML = passageCreatorData.passageContent;
        // remove font-size for already sadly converted passages.
        $('*', $('.workingArea')).css('font-size', '');
    } else {
        generateCompressedPassage();
    }

    if (parseInt(passageCreatorData.status, 10) >= 6 || masterEdit) {
        $('#submitForReviewButton').hide();
        $('#submitButton').hide();
    }

    //setting Image path
    var images = $('img[filename]');
    for (var i = 0; i < images.length; i++) {
        setImagePath(images[i]);
    }

    setProjectImages();
    $('#titleImage')[0].value = passageCreatorData.titleImage || 'none';
}

/*
 * makes projectimages sortable. Also makes them tag makers by calling createProjectImage
 */
function setProjectImages() {
    var images = $('#projectImages img');
    projectImages = [];
    $('#imageList').html('');
    $('#projectImages').html('');
    for (var i = 0; i < images.length; i++) {
        createProjectImage(images[i]);
    }
    $('#projectImages').sortable({
        helper : 'clone'
    });
}

/*
 * creates a tag making image container. The container is clickable. when clicked on x button the image is deleted from bucket.
 * when clicked on the image itself selected text is tagged to image. This is called whenever a new image has to be added.
 */
function createProjectImage(image) {
    var filename = Helpers.getFileName(image.src);
    if (projectImages.indexOf(filename) != -1) {
        return;
    }
    projectImages.push(filename);
    $(image).removeAttr('class');
    var count = Object.keys(imageTaggers).length;
    var container = Helpers.createDiv(image.outerHTML, 'projectImages' + count, onImageClick);
    container.className = 'addedImages';
    $('#projectImages').append(container);
    createCssApplier(filename, count);
    $('#titleImage').append(Helpers.createOption(filename, filename));
}

/*
 * creates rangy object that will be used while tagging. When text is tagged. The selected text is enclosed inside a tag whose name is 
 * defined in the elementTagName: here it is span. Additional attributes have been applied like the filename. This filename attribute is 
 * used to identify which image the current text selection has been tagged to. Even the passage viewer relies on this attribute 
 * to fetch image.
 */
function createCssApplier(filename, id) {
    imageTaggers[id] = rangy.createClassApplier(id, {
        elementTagName : "span",
        elementAttributes : {
            'data-file' : filename
        }
    });
}

/*
 * on projectImage click two scenarious are possible:
 * 1. user clicks on x button which caused the image to delete.
 * 2. user clicks on the image itself toggling tagging to selection.
 */
function onImageClick(event) {
    var filename = $($(this).children('img')[0]).attr('filename');
    var id = this.id.replace(/projectImages/, '');
    var imageObject = this;
    if (event.offsetX > this.offsetWidth - 4 && event.offsetY < 2) {
        //image deletion
        if (confirm('Are you sure you want to delete Image?')) {
            $.ajax({
                url : 'src/imageDelete.php',
                datatype : 'string',
                data : {
                    filename : filename,
                    projectID : passageCreatorData.passageID
                }
            }).done(function(response) {
                if (response == '1') {
                    alert('Image Deleted');
                    projectImages.splice(projectImages.indexOf(filename), 1);
                    $("#titleImage option[value='" + filename + "']").remove();
                    $(imageObject).remove();
                    saveProject('noMessage');
                    clearTagging(id);
                }
            });
        }
    } else {
        var selection = document.getSelection();
        if (tinyMCE.activeEditor.selection.getContent().trim() == '') {
            alert('selection is Empty.');
        } else {
            imageTaggers[this.id.replace(/projectImages/, '')].toggleSelection();
            var ed = tinyMCE.editors[0];
            ed.selection.select(ed.getBody(), true);
            // ed is the editor instance
            ed.selection.collapse(false);
        }
    }
}

/*
 * convert passage to new format. Where previously supported styles and attributes are removed. Content inside 
 * each page (.workingArea) is appended to #workingArea creating and continuous flow of content.
 * Blank spans and paragraphs are removed. line break is introduces after each page. 
 */
function generateCompressedPassage() {
    var div = document.createElement('div');
    div.innerHTML = passageCreatorData.passageContent;

    var lookbackContainer = document.createElement('div');
    lookbackContainer.className = 'lookbackContainer';

    $(this.lookbackTarget).html('');
    var workingAreas = $('.workingArea', div);
    for (var i = 0; i < workingAreas.length; i++) {
        var page = document.createElement('div');
        var containers = $('div', workingAreas[i]);
        for (var j = 0; j < containers.length; j++) {
            page.innerHTML += containers[j].innerHTML + '<br>';
        }
        page.className = 'lookbackPage';

        var spans = $('span', page);
        for (var j = 0; j < spans.length; j++) {
            if (spans[j].innerHTML.trim() == '&nbsp;' || spans[j].innerHTML.trim() == '') {
                $(spans[j]).remove();
            }
        }

        var paragraphs = $('p', page);
        for (var j = 0; j < paragraphs.length; j++) {
            if (paragraphs[j].innerHTML.trim() == '&nbsp;' || paragraphs[j].innerHTML.trim() == '') {
                $(paragraphs[j]).remove();
            }
        }
        $('*', page).css('text-align', '');
        $('*', page).css('position', '');
        $('*', page).css('left', '');
        $('*', page).css('top', '');
        $('*', page).css('width', '');
        $('*', page).css('height', '');
        $('*', page).css('font-size', '');
        $('*', page).removeAttr('data-mce-style');
        $('#workingArea').append(page.innerHTML);
    }
};

function handleImageLoaderResponse(imageLoaderResponse,mode) {
    var imagesLoaded = imageLoaderResponse.split('**');
    //removing empty strings
    for (var i = 0; i < imagesLoaded.length; i++) {
        if (imagesLoaded[i] == '') {
            imagesLoaded.splice(i, 1);
        }
    }

    var alertString = '';
    for (var i = 0; i < imagesLoaded.length; i++) {
        var evaluater = imagesLoaded[i].split('|');

        if (evaluater[0] == '1') {
            var path = evaluater[1];
            path = Helpers.replaceAll('**', '', path);
            var filename = Helpers.getFileName(path);
            var image = createImage(filename);

            createProjectImage(image);
        } else {
            alertString += 'Failed to upload ' + evaluater[1] + '\n';
        }
    }

    publish('noMessage',mode);
    if (alertString != '') {
        alert(alertString);
    }
};

/*
 * saves the project to db. if string is noMessage the passage is saved silently.
 */
function saveProject(string, callback, mode) {
	var isTextMispelled = 0;
		if(mode == 'noSpellCheck')
			isTextMispelled = 0;
		else
			isTextMispelled = checkMispelledWords();
			
    if (isTextMispelled == 0) {
        setContentToSave();
        $.ajax({
            url : 'src/saverLoader.php',
            type : 'POST',
            data : passageCreatorData,
            async:false
        }).done(function(response) {
            if ( typeof parseInt(response) == 'number') {
                if (isNaN(passageCreatorData.passageID)) {
                    passageCreatorData.passageID = parseInt(response);
                }
				
				if($("#masterUser").val()=="yes" && string!="noMessage"){
					if($("#urlReview").val()==""){
						window.close();
					}else{
						window.open($("#urlReview").val(), '_blank');
						return;
					}
					
				}
				
				if (string == 'view') {
                    viewPassage();
                } else if (string == 'makeQuestions') {
                    alert('Passage has been submitted to ' + passageCreatorData.currentAlloted + ' for review');
                    location.assign('../src/addGroupQuestions.php?groupQuesPassageId=' + passageCreatorData.passageID);
                } else if (string == 'submit') {
                    alert('Passage has been submitted to ' + passageCreatorData.currentAlloted + ' for review');
                    location.assign(document.URL.split('?')[0]);
                } else if (string != 'noMessage') {
                    alert('Passage saved succesfully');
                }

            } else {
                alert(response);
            }
				
            if (callback)
                callback();
        });

    } else if (isTextMispelled == 1){
        alert("Spelling/formatting issues found.");
    } else if (isTextMispelled == 2){
        alert("Please remove highlighting from text");
    }
	
}

function checkMispelledWords() {
	
	//debugger;
	if(passageCreatorData.username!="harsha.dediya" && passageCreatorData.username!="anand.mishra" && passageCreatorData.username!="sridhar" && passageCreatorData.username!="dev.dutta" && passageCreatorData.username!="aarushi.prabhakar")
	{
		return 0;
	}
	else
	{
	dataArr = [$('#workingArea').html()];
	/*dataArr = [];
	dataArr['value'] = $('#workingArea').html().replace(/<(?:.|\n)*?>/gm, '');
	dataArr['type'] = 'passage';
	dataArr['passageID'] = passageCreatorData.passageID;
	var jsonString = JSON.stringify(dataArr);*/
	var errordDetected = 0;
	$.ajax({
		url : '../ajax/spellChecking.php',
        type : 'POST',
        dataType : 'json',
        data : {
            'data' : dataArr ,
			'type' : 'passage',
			'qcode' : passageCreatorData.passageID
        },
		async : false,
			success: function(data) 
			{	
				//debugger;
				var qcodeDetailsArr=data['resultData'];
				var spellCheckErrors = data['spellingErrors'];
				var formattingErrorExists = data['formattingErrorExists'];
				var currentUser=$("#usernameTOsend").val();
				var editWordText = "Will edit";
				var trHTML = "";
				trHTML = '<thead> <tr> <th>Word</th> <th>Action</th> <th>Type</th><th>SubType</th> </tr> </thead>';
				
				
				if(spellCheckErrors.length>0 || formattingErrorExists>0)
				{
					/*	if($.inArray(currentUser, masterUserArr) !== -1) {  
							editWordText = "Will edit";
						}*/
						
						//wordHighlight = spaceHighlight = "";
						for (var i = 0; i < spellCheckErrors.length; i++) {
							var elementID = "optionTypes"+i;
							var SubElementID = "optionSubTypes"+i;
							var dictionaryOptions = "dictionaryOptions"+i;
							
							
								trHTML += '<tbody><tr><td>' + spellCheckErrors[i] + '</td><td>' + '<select class="dictionary" id='+dictionaryOptions+'> <option value="check">Need to check</option> <option value="add">Add to Dictionary</option> <option value="edit">'+editWordText+'</option> <option value="ignore">Ignore Word</option></select>' + '</td> <td> <select id='+elementID+' onchange="refillSubTypes(this)">';
							
								$.each(dicCategoriesAndSubCatObj,function(key,value){
									trHTML += '<option value="'+value['categoryName']+'" id='+key+'>'+value['categoryName']+'</option>'
								});
								
								trHTML +='</select> </td> <td> <select id='+SubElementID+' class="subType">';
								
								// By default show the subtypes of the first type
								
								$.each(dicCategoriesAndSubCatObj[0]['subCategories'],function(key,value){
									trHTML += '<option value="'+value['subCategoryName']+'">'+value['subCategoryName']+'</option>'
								});
								
								trHTML +='</select></tr></tbody>';
							
							
							/*mispelledWord = spellCheckErrors[i];
							highlightedWord = "<span class='sp_err'>"+mispelledWord+"</span>";
							var re = new RegExp(mispelledWord,"g");
							wordHighlight = $('#workingArea').html();
							wordHighlight = wordHighlight.replace(re,highlightedWord)
							$('#workingArea').html(wordHighlight);*/
							

					}
					
					/*highlightedSpace = "<span class='for_err'>&nbsp;&nbsp;</span>"
					spaceHighlight = $('#workingArea').html().replace(/(( &nbsp;){2,})/g,highlightedSpace);
					$('#workingArea').html(spaceHighlight);
					
					$('#workingArea').html().replace(/&nbsp;(\.|,|!|\?|:|;|\))/g, function(match, contents, offset, s)
				    {
						return "<span clas=='for_err'>"+contents+"</span>"
				    });
							
					wordHighlight = spaceHighlight = "";*/
					/*
					if($.inArray(currentUser, masterUserArr) !== -1) {  
						$('#rejectQues').attr({"disabled":true});
                   		$('#rejectQues').css('background-color','rgb(242, 242, 242)');
                        $('#rejectQues').css('color','lightgray');
					}*/
					errordDetected = 1;
					//alert(qcodeDetailsArr);
					
				}
				 
				if(spellCheckErrors.length>0)
				{
					$('#mW').html("");
					$('#mW').append(trHTML );
					$('#spellCheck').css('display','block');
					/*$('#saveAllQues').attr({"disabled":true});
                    $('#saveAllQues').css('background-color','rgb(242, 242, 242)');
                    $('#saveAllQues').css('color','lightgray');
					$('#AssignReviewer').attr({"disabled":true});
                    $('#AssignReviewer').css('background-color','rgb(242, 242, 242)');
                    $('#AssignReviewer').css('color','lightgray');*/
				}
				else
				{
					/*spaceHighlight = $('#workingArea').html().replace(/sp_err/g, "");
					$('#workingArea').html(spaceHighlight);*/
					$('#spellCheck').css('display','none');
				}
				$('#workingArea').html(qcodeDetailsArr[0]);
				
				/*if($('#workingArea').html().indexOf('sp_err')>-1 || $('#workingArea').html().indexOf('for_err')>-1)
					errordDetected = 2;*/
				
					
				}
		
	});
	
	return errordDetected;
	}

}

/*function spellCheck() {
	dataArr.push($('#workingArea').html().replace(/<(?:.|\n)*?>/gm, ''));
    if (checkMispelledWords() == 1) {
        alert("Spelling/formatting issues found");
    }
}

function addToDictionary() {
    if (checkMispelledWords() == true) {
        alert("No spelling errors found");
    } else {
        openMisspelledWordsBox();
    }
}*/

function postToDictionary() 
{
	var index, len;
	var validate=true;
    var wrongWords = [];
	var wrongWordsType = [];
    var wrongWordsAction = [];
	var wrongWordsSubType = [];
    $("#mW tr td").each(function () {
        if($(this).find('select option').val() != undefined)
        {
            if($(this).index()==1)
				wrongWordsAction.push($(this).find('select option:selected').val());  
            else if($(this).index()==2)
			{	
				if($(this).find('select option:selected').val()=="Acronym" && $(this).siblings()[0].innerHTML!=$(this).siblings()[0].innerHTML.toUpperCase())
				{	
					alert("Please enter the word '"+$(this).siblings()[0].innerHTML+"' in uppercase for acronym category");
					validate=false;
				}
				wrongWordsType.push($(this).find('select option:selected').val());
			}
			else if($(this).index()==3)
				wrongWordsSubType.push($(this).find('select option:selected').val());
        }
        else
        {
           wrongWords.push($(this).html()); 
        }
        
    });

    if(validate){
		console.log(wrongWords);
		console.log(wrongWordsAction);
		console.log(wrongWordsType);

		var infoArr = [passageCreatorData.username,passageCreatorData.passageID,'passage'];
		var dataString = [wrongWords,wrongWordsAction,wrongWordsType,wrongWordsSubType,infoArr] ;
		console.log(dataString);
		var jsonString = JSON.stringify(dataString);
		   $.ajax({
				type: "POST",
				url: "../ajax/postToDictionary.php",
				data: {data : jsonString}, 
				cache: false,
				

				success: function(){
					//alert("OK");
				
					publish();
				}
			});
			
			$('#spellCheck').css('display','none');
	}	
		//location.reload();
 }

//decide whether the passage is old/new
/*
 * if the user enter an existing passage name with different case then the passageName in the field is corrected to contain
 * existing passageName with exact case.
 */
function correctPassageName() {
    var passageName = $('#qcode').attr('value');
    if (!passageCreatorData.newPassage)
        return;
	
    var correctedPassageName = passageName.toLowerCase();
    for (var key in passageNamesAssoc) {
        if (correctedPassageName == passageNamesAssoc[key].toLowerCase()) {
            correctedPassageName = passageNamesAssoc[key];
            passageCreatorData.newPassage = false;
            passageCreatorData.passageID = key;
            $('#qcode').attr('value', correctedPassageName);
            break;
        }
    }
}

/*
 * Gather all content that should be saved before calling saverLoader.php
 */
function setContentToSave() {
    passageCreatorData.loadSaveMode = 0;
	
	//var passageSource=(typeof(tinyMCE.get('passageSource'))!=undefined)?tinyMCE.get('passageSource').getContent():"";
	//var passageIntro=(typeof(tinyMCE.get('passageIntro'))!=undefined)?tinyMCE.get('passageIntro').getContent():"";
	correctPassageName();
	
	passageCreatorData.passageName = $('#qcode').attr('value');
    passageCreatorData.passageContent = getCleanedPassageContent();
    passageCreatorData.totalPanes = passageCreatorData.passageContent.split(paneSeparator).length;
    passageCreatorData.passageImages = $('#projectImages').html();
    passageCreatorData.Author = $('#passageAuthor').attr('value');
	
    if(tinyMCE.editors.length > 0)
	{	
		passageCreatorData.Source = $('<div></div>').append(tinyMCE.get('passageSource').getContent()).find('[data-mce-style]').removeAttr('data-mce-style').end().html();
		passageCreatorData.intro = $('<div></div>').append(tinyMCE.get('passageIntro').getContent()).find('[data-mce-style]').removeAttr('data-mce-style').end().html();
	}
	else
	{	
		passageCreatorData.Source = $('<div></div>').append($('#passageSource')[0].innerHTML).find('[data-mce-style]').removeAttr('data-mce-style').end().html();
		passageCreatorData.intro = $('<div></div>').append($('#passageIntro')[0].innerHTML).find('[data-mce-style]').removeAttr('data-mce-style').end().html();
	}
    passageCreatorData.titleImage = $('#titleImage')[0].selectedOptions[0].value == 'none' ? '' : $('#titleImage')[0].selectedOptions[0].value;
    passageCreatorData.msLevel = $('#passageLevel')[0].selectedOptions[0].value;
    passageCreatorData.Form = $('#passageForm')[0].selectedOptions[0].value;
    passageCreatorData.passageType = $('#passageType')[0].selectedOptions[0].value;
    passageCreatorData.Genre = $('#passageGenre')[0].selectedOptions[0].value;
    passageCreatorData.Style = passageCreatorData.passageType == "Conversation" ? '' : $('#passageStyle')[0].selectedOptions[0].value;
    passageCreatorData.first_alloted = $('#reviewer1')[0].selectedOptions[0].value;
    passageCreatorData.second_alloted = $('#reviewer2')[0].selectedOptions[0].value;
}

/*
 * remove unnecessary tags from passage content here.
 */
function getCleanedPassageContent() {
	/*var stringToSend = $('<div></div>').append($('.workingArea')[0].innerHTML).find('[data-mce-style]').removeAttr('data-mce-style').end().html();
    return stringToSend;*/
    if(tinyMCE.get('workingArea') == undefined )
        return '';

    return tinyMCE.get('workingArea').getContent();
}


/*
 * legacy function. can be thoughtfully removed and use saveProject instead.
 */
function publish(mode,modeSpellCheck) {
	
	//alert(modeSpellCheck);
	var isTextMispelled = 0;
	
	if(modeSpellCheck=='noSpellCheck')
		isTextMispelled = 0;
	else
		isTextMispelled = checkMispelledWords();
		
	if (isTextMispelled == 0) 
	{
	    if (mode == undefined) {
	        mode = 'view';
	    }
		saveProject(mode,0,'noSpellCheck');
	}
	else if(isTextMispelled == 1)
	{
		alert("Spelling/formatting issues found");
		return false;
		//javascript_abort();
	}
	else if(isTextMispelled == 2)
	{
		alert("Please remove highlighting from text");
		return false;
		//javascript_abort();
	}
}

function createImage(source) {
    var image = document.createElement('img');
    $(image).attr('filename', source);
    return setImagePath(image);
};

function setImagePath(image) {
    var source = $(image).attr('filename');
    $(image).attr('src', getPathForAssets(source));
    return image;
};

function getPathForAssets(fileName) {
    return msEnglishBucket + '/passages/eng/' + $('#projectNameImage').attr('value') + '/assets/' + fileName;
};

function populateProofTypeForm() {
    passageCreatorData.status = 0;
    passageCreatorData.username = $('#username').attr('value');
	//passage creator cannot be a reviewer. removeing his name from reviewer list.
    
	if(masterEdit)
	{	
		$('option[value="' + passageCreatorData.passageMaker + '"]', "#reviewer1").remove();
		$('option[value="' + passageCreatorData.passageMaker + '"]', "#reviewer2").remove();
	}
	else
	{	
		$('option[value="' + passageCreatorData.username + '"]', "#reviewer1").remove();
		$('option[value="' + passageCreatorData.username + '"]', "#reviewer2").remove();
    }
	
	
	fetchPassageNames();
    populateTaggingFields();
};

/*
 * Fetches all passageNames and passageIds to aid in autofill.
 */
function fetchPassageNames() {
    $.ajax({
        url : '../ajax/common.php',
        type : 'POST',
        dataType : 'json',
        data : {
            'function' : 'getPassageNamesAndIDs'
        },
        success : function(data) {
            passageNamesAssoc = {};
            passageNames = [];
            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    passageNamesAssoc[key] = data[key];
                    passageNames.push(data[key]);
                }
            }
            $('#qcode').autocomplete({
                source : passageNames,
                _resizeMenu : function() {
                    $(this.menu.element).css({
                        'width' : '390px'
                    });
                }
            });
            ajaxFinish();
        }
    });
};

/*
 * fetches data from passageTaggingMaster.
 */
function populateTaggingFields() {
    $.ajax({
        url : 'src/getFields.php',
        dataType : 'json',
    }).done(function(theData) {
        var fieldElement = {
            'msLevel' : $('#passageLevel')[0],
            'Type' : $('#passageType')[0],
            'Genre' : $('#passageGenre')[0],
            'Form' : $('#passageForm')[0],
            'Style' : $('#passageStyle')[0],
        };

        for (var i = 0; i < theData.length; i++) {
            $(fieldElement[theData[i]['field']]).append(Helpers.createOption(theData[i]['name'], theData[i]['name']));
        }

        ajaxFinish();
    });
};

function submitForReview(string) {
	
	var isTextMispelled = checkMispelledWords();
	if(isTextMispelled == 1)
	{
		alert("Spelling/formatting issues found");
		return false;
	}
	else if(isTextMispelled == 2)
	{
		alert("Please remove highlighting from text");
		return false;
	}
	var curStatus=1;
    if (Helpers.isBlank(passageCreatorData.first_alloted) || Helpers.isBlank(passageCreatorData.second_alloted) || passageCreatorData.first_alloted == passageCreatorData.second_alloted) {
        alert('Please assign for both reviewers');
        return;
    }

    //console.log(passageCreatorData.passageType+" "+passageCreatorData.status+" "+passageCreatorData.isAudioUploaded+" "+passageCreatorData.passageImages)
     if (passageCreatorData.passageType == 'Conversation' && passageCreatorData.status == '3' && passageCreatorData.isAudioUploaded == '0') {
        alert('Upload Audio before sending for second review');
        return;
    }else if(passageCreatorData.passageType != 'Conversation' && (passageCreatorData.passageImages == 'no Images Included.'|| passageCreatorData.passageImages=="") && passageCreatorData.status == '3'){
        alert('no Images Included');
        return;
        
    }
	/*var images = $('#projectImages img');
	for (var i = 0; i < images.length; i++) {
		var image=images[i];
	   	var filename = encodeURIComponent(Helpers.getFileName(image.src));
	   	console.log(filename)
	   	if (passageCreatorData.passageContent.contains(filename)==false) {  			
	  		alert('Following image is not tagged'+ images[i]);
        	return; 
		};
	} ;  */

    if (confirm('Are you sure you want to submit for review?')) {
        if (passageCreatorData.status < 1){
			 passageCreatorData.status = 1;
			curStatus=1;
		}
           
			
        if (passageCreatorData.status == 1) {
            passageCreatorData.currentAlloted = passageCreatorData.first_alloted;
        }  else if (passageCreatorData.status == 2) {
            passageCreatorData.currentAlloted = passageCreatorData.first_alloted;
            curStatus=1;
        }else if (passageCreatorData.status == 3) {
            passageCreatorData.currentAlloted = passageCreatorData.second_alloted;
             curStatus=4;
        }else if (passageCreatorData.status == 5) {
            passageCreatorData.currentAlloted = passageCreatorData.second_alloted;
             curStatus=4;
        }
        passageCreatorData.status=curStatus;
       
        if (string == 'makeQuestions') {
            saveProject('makeQuestions');
        } else {
            saveProject('submit');
        }
         $.ajax({
            url : '../ajax/common.php',
            type : 'POST',
            dataType : 'json',
            async:false,
            data : {
                'function' : 'addTrailAndUpdateStatus',
                'addToTrail' : passageCreatorData.currentAlloted,
                'passageID' : passageCreatorData.passageID,
                'status' : curStatus,
                'currentAlloted' : passageCreatorData.currentAlloted
            }
        });
    }
};

var startRequests = 0;

function ajaxFinish() {
    startRequests++;
    if (startRequests >= 2) {
        if (passageNamesAssoc) {
           if (masterEdit){
			    $('#qcode').attr('value', passageNamesAssoc[$('#qcode').val()]);
				$('#go').trigger('click');
		   }
		   if (pageParams['passageID']){
				$('#qcode').attr('value', passageNamesAssoc[pageParams['passageID']]);
				$('#go').trigger('click');
		   }
		}
    }
}

/*
 * clear tagging associated with an image. 
 * param id: string : if id is passed only the tagging associated with the respective image is removed. if no id is
 * passed all tagging is removed.
 */
function clearTagging(id) {
	var editor = tinymce.editors[0];
    editor.selection.select(editor.getBody(), true);

    if (!id) {
        for (var key in imageTaggers) {
            if (imageTaggers.hasOwnProperty(key)) {
                imageTaggers[key].undoToSelection();
            }
        }

        //alternate heavy tag removal
        while ($('span[data-file]').length > 0) {
            $('span[data-file]').replaceWith(function() {
                return this.innerHTML;
            });
        }
    } else {
        imageTaggers[id].undoToSelection();
    }

    $('.addedImages.highlight').removeClass('highlight');
    editor.selection.collapse();
}

/*
 * dont allow reviewer1 and reviewer2 to be same. When user is selected in one list that particular user is disabled in 
 * other list.
 */
function onReviewerSelect(e) {
    if (e.target.id == 'reviewer1') {
        passageCreatorData.first_alloted = e.target.selectedOptions[0].value;
    } else {
        passageCreatorData.second_alloted = e.target.selectedOptions[0].value;
    }
    $('#reviewer1 option, #reviewer2 option').attr('disabled', false);

    $('option[value="' + passageCreatorData.first_alloted + '"]').attr('disabled', true);
    $('option[value="' + passageCreatorData.second_alloted + '"]').attr('disabled', true);
}

function changeColumnAppearence(columNo,type) {
        var col= columNo;
        if (isNaN(col) || col == "") {
            alert("Invalid Column");
            return;
        }
        col = parseInt(col, 10);
        col = col - 1;
        var tbl = document.getElementById("mW");
 
        if (tbl != null) {
           
            for (var i = 0; i < tbl.rows.length; i++) {
                for (var j = 0; j < tbl.rows[i].cells.length; j++) {
                    
                    if (j == col && type=="hide")
                        tbl.rows[i].cells[j].style.display = "none";
					else if (j == col && type=="show")
					    tbl.rows[i].cells[j].style.display = "table-cell";
                }
            }
        }
    }
	
function javascript_abort()
{
   throw new Error('This is not an error. This is just to abort javascript');
}