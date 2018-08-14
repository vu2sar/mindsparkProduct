function ChoiceScreen(params){
	var androidDevice = false;
	var appleDevice = false;
	var touchCount = 0;
	var colors = new Array("#6dae7e", "#faaf3d", "#058486", "#f05c42");  // {Green, Yellow, Blue, Red}
	if(window.navigator.userAgent.indexOf("Android") != -1)
	{
		androidDevice = true;	
	}
	else if(window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
	{
		appleDevice = true;
	}
	this.choiceType=params['choiceType'];
	this.choices=params['choices'];
	this.choiceReject=params['choiceReject'];
	this.choiceID=params['choiceID'];
	this.choiceTheme=params['choiceTheme']?params['choiceTheme']:"0";
	this.actions={};
	this.closingFn=null;
	this.closingFnAttr=null;

	if (typeof this.choices=='object' && Object.keys(this.choices).length>0){
		categories=Object.keys(this.choices);
		noOfChoices=(categories.length==1 && this.choiceTheme=="New")?categories.length+1:categories.length;
		switch(this.choiceTheme){
			case "0":
				choiceScreenTextMsg='Choose what you want to do next!';break;
			case "New":
				choiceScreenTextMsg='<div id="choiceTrophy"></div><div id="choiceCheerText">Cheers!<br>Topic Completed</div><div  id="choiceSparkieText">Time to earn more sparkies now</div>';
				break;
			default:
				choiceScreenTextMsg='Choose what you want to do next!';break;
		}
		switch(this.choiceTheme){
			case "0":
				noActionLinkText='<div id="arrowButton">&#9664;</div><div id="noActionText">NO, TAKE ME TO THE DASHBOARD</div>';break;
			case "new":
				noActionLinkText='<div id="noActionText">DASHBOARD</div>';
				break;
			default:
				noActionLinkText='<div id="arrowButton">&#9664;</div><div id="noActionText">NO, TAKE ME TO THE DASHBOARD</div>';break;
		}
		
		$('#choiceScreenContainer').remove();
		var choiceScreenHTML='<div id="choiceScreenContainer">'+
							'	<div id="choiceScreenLayer"></div>'+
							'	<div id="choiceScreenDiv" class="theme'+this.choiceTheme+' choiceNum'+noOfChoices+'"">'+
							'		<div id="choiceScreenText">'+choiceScreenTextMsg+'</div>'+
							'		<div id="tiles">'+
							'		</div>'+
							'		<div id="noActionLink">'+noActionLinkText+'</div>'+
							'	</div>'+
							'</div>';

		$(choiceScreenHTML).insertBefore('#bottom_bar');
		var html="";
		switch(categories.length)
		{
			case 1:
			{
				var mainCateg=getMainCateg(this.choiceTheme,categories[0],this.choices[categories[0]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[0]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				break;
			}
			case 2:
			{
				var mainCateg=getMainCateg(this.choiceTheme,categories[0],this.choices[categories[0]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[0]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				mainCateg=getMainCateg(this.choiceTheme,categories[1],this.choices[categories[1]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[1]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				break;
			}
			case 3:
			{
				var mainCateg=getMainCateg(this.choiceTheme,categories[0],this.choices[categories[0]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[0]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				mainCateg=getMainCateg(this.choiceTheme,categories[1],this.choices[categories[1]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[1]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";

				mainCateg=getMainCateg(this.choiceTheme,categories[2],this.choices[categories[2]]);
				//if(mainCateg[0] != "Enrichment")
				//{
					html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[2]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				//}
				/*else
				{
					html += "<div class='tile "+mainCateg[0]+"' style='left:25%;top:53%;background-color:"+colors[2]+";' rel=\""+categories[2]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				}*/
				break;					
			}
			case 4:
			{
				mainCateg=getMainCateg(this.choiceTheme,categories[0],this.choices[categories[0]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[0]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
				mainCateg=getMainCateg(this.choiceTheme,categories[1],this.choices[categories[1]]);
				html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[1]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";

				mainCateg=getMainCateg(this.choiceTheme,categories[2],this.choices[categories[2]]);mainCateg1=getMainCateg(this.choiceTheme,categories[3],this.choices[categories[3]]);
				if(mainCateg[0] != "Enrichment" && mainCateg1[0] != "Game")
				{
					html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[2]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
					html += "<div class='tile "+mainCateg1[0]+"' rel=\""+categories[3]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg1[1]+"</span></div></div><span class='heading'>"+mainCateg1[0].toUpperCase()+"</span></div>";
				}
				else
				{
					html += "<div class='tile "+mainCateg[0]+"' rel=\""+categories[2]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg[1]+"</span></div></div><span class='heading'>"+mainCateg[0].toUpperCase()+"</span></div>";
					html += "<div class='tile "+mainCateg1[0]+"' rel=\""+categories[3]+"\"><div class='tLayer'><div class='tLayerText'><span class='bodyText'>"+mainCateg1[1]+"</span></div></div><span class='heading'>"+mainCateg1[0].toUpperCase()+"</span></div>";
				}
				break;
			}
		}
		$("#choiceScreenDiv #tiles").html(html);
		for(var i=0;i<categories.length; i++){
			category=categories[i];
			this.actions[category]='<form method="POST" action="'+this.choices[category]['link']+'" name="choiceForm" id="choiceForm" title="'+this.choiceID+'">';

			switch(category){
				case 'IYC': this.actions[category]+='<input type="hidden" name="ttCode" value="'+this.choices[category]['ttCode']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="choiceScreen">';
					this.actions[category]+='<input type="hidden" name="onlyBronze" value="1">';
					break;
				case 'Enrichment': this.actions[category]+='<input type="hidden" name="gameID" value="'+this.choices[category]['gameID']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="choiceScreen">';
					this.actions[category]+='<input type="hidden" name="returnTo" value="'+this.choices[category]['fromCS']+'">';
					break;
				case 'Game': this.actions[category]+='<input type="hidden" name="gameID" value="'+this.choices[category]['gameID']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="choiceScreen">';
					this.actions[category]+='<input type="hidden" name="returnTo" value="'+this.choices[category]['fromCS']+'">';
					break;
				case 'HigherClass': this.actions[category]+='<input type="hidden" name="ttCode" value="'+this.choices[category]['ttCode']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="'+this.choices[category]['mode']+'">';
					this.actions[category]+='<input type="hidden" name="higherLevel" value="'+this.choices[category]['higherLevel']+'">';
					break;
				case 'repeatTopic': this.actions[category]+='<input type="hidden" name="ttCode" value="'+this.choices[category]['ttCode']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="'+this.choices[category]['mode']+'">';
					this.actions[category]+='<input type="hidden" name="higherLevel" value="'+this.choices[category]['higherLevel']+'">';
					break;
				case 'topicPractice': this.actions[category]+='<input type="hidden" name="ttCode" value="'+this.choices[category]['ttCode']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="'+this.choices[category]['mode']+'">';
					this.actions[category]+='<input type="hidden" name="userID" value="'+this.choices[category]['userID']+'">';
					this.actions[category]+='<input type="hidden" name="cls" value="'+this.choices[category]['childClass']+'">';
					break;
				case 'CEQ': this.actions[category]+='<input type="hidden" name="mode" value="choiceScreen">';
					if (this.choices[category]['topics']) this.actions[category]+='<input type="hidden" name="topics" value="'+this.choices[category]['topics']+'">';
					if (this.choices[category]['qcodes']) this.actions[category]+='<input type="hidden" name="qcodes" value="'+this.choices[category]['qcodes']+'">';
					if (this.choices[category]['sources']) this.actions[category]+='<input type="hidden" name="sources" value="'+this.choices[category]['sources']+'">';
					if (this.choices[category]['totQues']) this.actions[category]+='<input type="hidden" name="totQues" value="'+this.choices[category]['totQues']+'">';
					if (this.choices[category]['forTTcode']) this.actions[category]+='<input type="hidden" name="forTTcode" value="'+this.choices[category]['forTTcode']+'">';
					if (this.choices[category]['challengeNo']) this.actions[category]+='<input type="hidden" name="challengeNo" value="'+this.choices[category]['challengeNo']+'" rel="param">';
					break;
				case 'Solve': this.actions[category]+='<input type="hidden" name="practiseModuleId" value="'+this.choices[category]['practiseModuleId']+'" rel="param">';
					this.actions[category]+='<input type="hidden" name="mode" value="choiceScreen">';
					this.actions[category]+='<input type="hidden" name="returnTo" value="'+this.choices[category]['fromCS']+'">';
					break;
			}
			this.actions[category]+='<input type="submit" id="submitChoiceForm"></form>';
		}
		$('#choiceScreenDiv #noActionText').html(this.choiceReject['helpText']);
		$('#noActionLink').click(function(){
			$.post("controller.php",{'mode':'chooseFromChoiceScreen','clicked':'choiceReject','choiceID':choiceScreen.choiceID});
				choiceScreen.closeChoiceScreen();
		});
		addEventsToChoices(this.actions);
	}
	/*
	"choiceScreenData":{
		"choiceType":"Topic Completion",
		"choices":{
			"IYC":{"ttCode":"TT045","helpText":"Get better at some concepts.","link":"improveConcepts.php"},
			"Enrichment":{"gameID":"128","gameDesc":"The Birthday Problem","helpText":"Enrich yourself exploring Maths beyond curriculum.","link":"enrichmentModule.php"}
			},
		"choiceReject":{"ttCode":"TT045","higherLevel":0,"mode":"classLevelCompletion","helpText":"NO, TAKE ME TO THE DASHBOARD","link":"controller.php"}
	}*/

	function getMainCateg (theme,categKey,choiceCateg) {
		helpText=choiceCateg['helpText'];
		if (theme=='New' && categKey=='HigherClass')
			categ=categKey;
		else if (theme=='New' && (categKey=='Enrichment' || categKey=='Game'))
			categ="Game";
		else
			categ=(categKey=='IYC' || categKey=='repeatTopic')?'Revise':(categKey=='CEQ'?'Challenge':(categKey=='HigherClass'?'Explore':(categKey=='Game2'?'Game':categKey)));

		return [categ,helpText];
	}
	
	function closeChoiceScreen()
	{
		if (this.choiceReject['mode']){
			//"choiceReject":{"ttCode":"TT045","higherLevel":0,"mode":"classLevelCompletion","helpText":"NO, TAKE ME TO THE DASHBOARD","link":"controller.php"}
			choiceRejectHTML='<form method="POST" action="'+this.choiceReject['link']+'"><input type="hidden" name="mode" value="'+this.choiceReject['mode']+'"><input type="hidden" name="higherLevel" value="'+this.choiceReject['higherLevel']+'"><input type="hidden" name="fromQuesPage" value="1"><input type="hidden" name="fromChoiceScreen" value="1"><input type="hidden" name="ttCode" value="'+this.choiceReject['ttCode']+'"></form>';
			if (typeof setTryingToUnload=='function') setTryingToUnload();
			$(choiceRejectHTML).appendTo('body').submit();
		}
		$("#choiceScreenContainer").remove();noSubmit=0;
		this.closingFn(this.closingFnAttr);
	}
	function addEventsToChoices(actions)
	{
		var actionsForChoice=actions;
		var html = "";
		if(!androidDevice && !appleDevice)
		{
			// $(".tile").bind("mouseenter",function(){$(this).find('.tLayer').show();});
			// $(".tile").bind("mouseleave",function(){$(this).find('.tLayer').hide();});
			$(".tile").bind("click",function(){
				$(".tile").unbind("click");var categ=$(this).attr('rel');
				if (typeof setTryingToUnload=='function') setTryingToUnload();
				$(actionsForChoice[categ]).appendTo('body');
				$.post("controller.php",{'mode':'chooseFromChoiceScreen','clicked':categ,'params':$('#choiceForm input[rel=param]').val(),'choiceID':$('#choiceForm').attr('title')});
				$('#submitChoiceForm').click();
			});
		}
		else
		{
			/*$(".tile").bind("click",function(){
				if (!$(this).attr('flag')){
					$('.tile').removeAttr('flag');
					$('.tile .tLayer,.tile .goBtn').hide();
					$(this).find('.tLayer,.goBtn').show();
					$(this).attr('flag',1);
				}
				else{
					$(this).find('.tLayer,.goBtn').hide();
					$(this).removeAttr('flag');
				}
				
			});
			$('.tile .tLayer').bind('click',function(){
				$(this).parent().bind('click',function() {
					$(this).unbind('click');
					$(this).find('.tLayer,.goBtn').show();
				});
				$(this).hide();$(this).find('.goBtn').hide();
			});*/
			$(".tile").bind("click",function(){
				$(".tile").unbind("click");var categ=$(this).attr('rel');
				if (typeof setTryingToUnload=='function') setTryingToUnload();
				$(actionsForChoice[categ]).appendTo('body');
				$.post("controller.php",{'mode':'chooseFromChoiceScreen','clicked':categ,'params':$('#choiceForm input[rel=param]').val(),'choiceID':$('#choiceForm').attr('title')});
				$('#submitChoiceForm').click();
			});
		}
		
	}
};
function enableChoiceTileClickFn(){

}
ChoiceScreen.prototype.show = function(a,b){
	this.closingFn=a;this.closingFnAttr=b;//console.log('openCS');
	if ($('#choiceScreenContainer').length>0) $('#choiceScreenContainer').show();
	else this.closeChoiceScreen();
};
ChoiceScreen.prototype.closeChoiceScreen = function(){//console.log('closeCS');
	if (this.choiceReject['mode']){
		//"choiceReject":{"ttCode":"TT045","higherLevel":0,"mode":"classLevelCompletion","helpText":"NO, TAKE ME TO THE DASHBOARD","link":"controller.php"}
		choiceRejectHTML='<form method="POST" action="'+this.choiceReject['link']+'"><input type="hidden" name="mode" value="'+this.choiceReject['mode']+'"><input type="hidden" name="higherLevel" value="'+this.choiceReject['higherLevel']+'"><input type="hidden" name="fromQuesPage" value="1"><input type="hidden" name="fromChoiceScreen" value="1"><input type="hidden" name="ttCode" value="'+this.choiceReject['ttCode']+'"></form>';
		if (typeof setTryingToUnload=='function') setTryingToUnload();
		$(choiceRejectHTML).appendTo('body').submit();
	}
	$("#choiceScreenContainer").remove();noSubmit=0;
	if (typeof this.closingFn=="function" && !this.closingFnAttr)this.closingFn();
	else if (typeof this.closingFn=="function" && this.closingFnAttr) this.closingFn(this.closingFnAttr);
};