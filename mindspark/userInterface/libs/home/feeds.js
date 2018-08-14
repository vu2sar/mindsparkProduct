var FeedPuller = function(){
	var _feedPuller = {};
	//var url = "feeds.php";
	var url = "/mindspark/userInterface/feeds.php";
	var schoolCode=0;
	var studentClass=0;
	var schoolUser=0;
	var userID=0;
	var allFeedIDs=[];
	var addedNewAllFeeds=[];
	var myFeedIDs=[];
	var addedNewMyFeeds=[];
	var slideInRunningA=false;
	var slideInRunningM=false;
	var loadingOldA=false;
	var loadingOldM=false;

	var EnrichmentFolder=null;

	_feedPuller.startFeeds = function (uID, sCode,sClass, isSchoolUser, enrichmentFolder){
		userID=uID;
		schoolCode=sCode;
		studentClass=sClass;
		schoolUser=isSchoolUser;
		EnrichmentFolder=enrichmentFolder;
		if (window.jQuery) {
			setFeedActions();
			loadingFeeds('#myFeeds,#allFeeds');
		    loadFeeds(1,0); // (1 for new 0 for old, 1 for myFeeds, 0 for all)
		    loadFeeds(1,1); // (1 for new 0 for old, 1 for myFeeds, 0 for all)
		}
	}
	function loadFeeds(type,mF){
		var onlyMe=((mF)?userID:mF);
		var getFeedsObj={schoolCode: schoolCode, stClass: studentClass, isSchoolUser: schoolUser, type:type,onlyMe:onlyMe};
		var feedIDs=(mF)?myFeedIDs:allFeedIDs;
		var feedContainer=(mF)?'#myFeeds':'#allFeeds';
		if (feedIDs.length>0) {getFeedsObj['newestFeed']=feedIDs[feedIDs.length-1];getFeedsObj['oldestFeed']=feedIDs[0];}
		$.post( "feeds.php", getFeedsObj, function( data ) {
			var feedsObj = data;
	  	    if (typeof(feedsObj)=='object'){
	  	    	$(feedContainer+' .loadingFeeds').remove();
	  	    	if (!type) {loadingOldA=(!mF)?false:loadingOldA;loadingOldM=(mF)?false:loadingOldM;}
	  	    	var gotfeedIDs=Object.keys(feedsObj);var newF=(feedIDs.length==0)?1:0;
				if (feedIDs.length==0 && $(feedContainer+' .errorFeed').length==0 && gotfeedIDs.length==0) {
					var msg=(mF)?'Oh no! There are no feeds to show right now..<br><br>If you are new to Mindspark, you could try completing a topic. You can see your Mindspark activity feed here.':'Oh no! There are no feeds to show right now..';
					$('<div class="feed errorFeed"><div class="fInfo">'+msg+'</div></div>').prependTo(feedContainer);
					return;
				}
				var addedNewFeeds=(mF)?addedNewMyFeeds:addedNewAllFeeds;
	  	    	for (var i=0;i<gotfeedIDs.length;i++){
	  	    		var index=(type)?gotfeedIDs[i]:gotfeedIDs[gotfeedIDs.length-1-i];var feed=feedsObj[index];
	  	    		var f=showFeed(index,feed,type,mF);if (f && !newF && type) {addedNewFeeds.push(f);f.hide().css('margin-top','-'+f.height()+'px');}
	  			}
	  			if (!newF && type){
	  				slideInAddedFeeds(1,mF);
	  			}
	  	    }
		}, "json");
		if (type==1 && !mF) setTimeout(function(){loadFeeds(1,0);},180000);
	}
	function showFeed (index,feed,type,mF) {
		var el=null;var feedIDs=(mF)?myFeedIDs:allFeedIDs;
		if (inArray(index,feedIDs)<0) {
			if (type==1) feedIDs.push(index);
			else feedIDs=feedIDs.splice(0,0,index);
			var studentInfo='<span class="sName" rel="'+feed.userID+'" data-userimage="'+feed.studentIcon+'" data-userclass="'+feed.childClass+'" data-userschoolcode="'+feed.schoolCode+'">'+feed.childName+'</span>';
			var actInfo='<span class="acName" rel="'+feed.actID+'" data-actimage="'+feed.actIcon+'">'+feed.actDesc+'</span>';
			if (feed.ftype=='milestone' || feed.ftype=='badge')
			{
				var rw=feed.actDesc;rw=rw.split('~');
				if (rw.length>2)
					actInfo='<span class="acName" rel="'+feed.actID+'" data-actimage="'+rw[1]+'.png">'+rw[0]+'</span>';
			}
			var str='';
			switch (feed.ftype){
				case 'topic': 
					str='<div class="feed fTopic" rel="'+index+'" data-ftype="'+feed.ftype+'" data-score="'+feed.score+'" data-timetaken="'+feed.timeTaken+'"><div class="fInfo">'+studentInfo+' completed the topic '+actInfo+'.</div></div>';break;
				case 'timedTest': 
					str='<div class="feed fTimedTest" rel="'+index+'" data-ftype="'+feed.ftype+'"  data-score="'+feed.score+'" data-timetaken="'+feed.timeTaken+'"><div class="fInfo">'+studentInfo+' took the timed test '+actInfo+'.</div></div>';break;
				case 'game': 
					str='<div class="feed fGame" rel="'+index+'" data-ftype="'+feed.ftype+'"  data-score="'+feed.score+'" data-timetaken="'+feed.timeTaken+'"><div class="fInfo">'+studentInfo+' played the game '+actInfo+'.</div></div>';break;
				case 'enrichment': 
					str='<div class="feed fEnrich" rel="'+index+'" data-ftype="'+feed.ftype+'"  data-score="'+feed.score+'" data-timetaken="'+feed.timeTaken+'"><div class="fInfo">'+studentInfo+' worked on the enrichment module '+actInfo+'.</div></div>';break;
				case 'milestone': 
					str='<div class="feed fMilestone" rel="'+index+'" data-ftype="'+feed.ftype+'"  data-score="'+feed.score+'" data-timetaken="'+feed.timeTaken+'"><div class="fInfo">'+studentInfo+' has achieved '+actInfo+'.</div></div>';break;
				case 'badge': 
					str='<div class="feed fBadge" rel="'+index+'" data-ftype="'+feed.ftype+'"  data-score="'+feed.score+'" data-timetaken="'+feed.timeTaken+'"><div class="fInfo">'+studentInfo+' received the badge '+actInfo+'.</div></div>';break;
				default: break;
			}
			var feedContainer=(mF)?'#myFeeds':'#allFeeds';
			if (type==1) el=$(str).prependTo(feedContainer);
			else el=$(str).appendTo(feedContainer);
			//feed.userID==userID && el.addClass('myFeed');
			el.css('opacity',0)
				.animate({'opacity':1},500,function(){$(this).removeClass('new-feed')})
				.click(function(){
					var t=$(this);
					var sInfo=t.find('.sName'),aInfo=t.find('.acName');
					var fID=$(this).attr('rel');
					var ftype=$(this).attr('data-ftype');
					var score=$(this).attr('data-score');
					var timeTaken=$(this).attr('data-timeTaken')*1;
					var sName=$(sInfo).text();
					var sID=$(sInfo).attr('rel');
					var sImage=$(sInfo).attr('data-userimage');
					var sClass=$(sInfo).attr('data-userclass');
					var sSchoolC=$(sInfo).attr('data-userschoolcode');
					var actName=$(aInfo).text();
					var actID=$(aInfo).attr('rel');
					var actImage=$(aInfo).attr('data-actimage');

					if (t.is('.feedFocussed')) {
						$('#feedExtnd').stop();$('.feedFocussed').removeClass('feedFocussed');
						$('#feedExtnd').fadeOut(200);
					}
					else {
						$('#feedExtnd').stop();$('.feedFocussed').removeClass('feedFocussed');
						$('#feedExtnd').fadeOut(200,function(){
							var simg=(sImage=="")?'assets/feeds/naSt.png':''+sImage;
							var sstr='<div class="feStudent" rel="'+sID+'"><img src="'+simg+'" /><span>'+sName+'</span></div>';
							var aimg=(actImage=="")?'na.png':''+actImage;var apstr='',arStr='';
							switch (ftype){
								case 'topic': 
									aimg=(actImage=="")?'':'<img src="'+actImage+'" />';
									//apstr='Accuracy: '+score+'% ';
									arStr='completed the topic';break;
								case 'timedTest': 
									aimg=(actImage=="")?'':'<img src="'+actImage+'" />';
									//apstr='Accuracy: '+score+'% &nbsp;&nbsp;&nbsp;';
									apstr+='Time taken: '+timeTaken+'s';
									arStr='took the timed test';break;
								case 'game': 
									aimg=(actImage=="")?'':'<td style="width:80px"><img src="'+EnrichmentFolder+'/html5/games/'+actID+'/'+actImage+'" /></td>';
									//apstr='Score: '+score+' &nbsp;&nbsp;&nbsp;';
									apstr+='Time taken: '+timeTaken+'s';
									arStr='played the game';break;
								case 'enrichment': 
									aimg=(actImage=="")?'':'<td style="width:80px"><img src="'+EnrichmentFolder+'/html5/enrichments/'+actID+'/'+actImage+'" /></td>';
									apstr='Time spent : '+timeTaken+'s';
									arStr='worked on the enrichment module';break;
								case 'milestone': 
									aimg=(actImage=="")?'':'<td style="width:60px"><img src="assets/rewards/badgesRewardSection/Unlocked/'+actImage+'" style="width:60px"/></td>';
									apstr='';
									arStr='achieved ';break;
								case 'badge':
									aimg=(actImage=="")?'':'<td style="width:60px"><img src="assets/rewards/badgesRewardSection/Unlocked/'+actImage+'" style="width:60px"/></td>';
									apstr='';
									arStr='received the badge';break;
							}
							var astr='<div class="feActivity" rel="'+actID+'">'+arStr+'<table width="100%"><tr><td><div>'+actName+'</div><span>'+apstr+'</span></td>'+aimg+'</tr></table></div>';
							var tstr='<table width="100%"><tr><td class="feAIcon '+ftype+'"></td><td class="feText" style="padding-left:5px;">'+sstr+astr+'</td></tr></table>';
							$('#feedExtnd').html('<div id="feCont">'+tstr+'</div><span id="feedArr"></span><span id="feedArrb"></span>');
							var pos=t.offset(),fleft=t.parent().offset().left-350, tH=t.height(),ftop=0;
							if ($('#feedExtnd').height()+pos.top>$(document).height()-5) ftop=$(document).height()-$('#feedExtnd').height()-5;
							else if (pos.top-10<5) ftop=5;
							else ftop=pos.top-10;
							$('#feedExtnd').css({'top':ftop+'px','left':fleft+'px'});
							$('#feedExtnd').css({display:'block',opacity:0});
							$('.feAIcon').css('height',$('.feText').height()+'px');
							$('#feedArr').css('top',(pos.top+tH/2 - ftop-5)+'px');
							$('#feedArrb').css('top',(pos.top+tH/2 - ftop+1-5)+'px');
							$('#feedExtnd').animate({opacity:1},200);
							t.addClass('feedFocussed');
						});
					}
					//
				})
				.hover(function(){$(this).toggleClass('feedHover')});
		}
		return el;
	}
	function slideInAddedFeeds (t,mF) {
		var addedNewFeeds=(mF)?addedNewMyFeeds:addedNewAllFeeds;
		if (((mF && slideInRunningM)||(!mF && slideInRunningA)) && t) return;
		if (addedNewFeeds.length==0) {
			slideInRunningM=(mF)?false:slideInRunningM;slideInRunningA=(!mF)?false:slideInRunningA;
		}
		else {
			$(addedNewFeeds[0]).show().animate({'margin-top':'0px'},800);
			addedNewFeeds.splice(0,1);
			slideInRunningM=(mF)?true:slideInRunningM;slideInRunningA=(!mF)?true:slideInRunningA;
			setTimeout(function(){slideInAddedFeeds(0,mF)},5000);
		}
	}
	function inArray(needle, haystack) {
	    var length = haystack.length;
	    for(var i = 0; i < length; i++) {
	        if(haystack[i] == needle) return i;
	    }
	    return -1;
	}
	function loadingFeeds(container) {
		$(container).append('<div class="loadingFeeds"></div>');
	}
	function setFeedActions(){
		$('#feedExtnd').click(function(e){e.stopPropagation();});
		$('#feeds').append('<div id="myFeeds"></div><div id="allFeeds"></div>');
		$(document).mousedown(function (e){
		    var container = $(".feedFocussed, #feedExtnd");
		    if (!container.is(e.target) // if the target of the click isn't the container...
		        && container.has(e.target).length === 0) // ... nor a descendant of the container
		    {
		        $('#feedExtnd').delay(200).fadeOut(200);
		    }
		});
		$('#feeds>div').scroll(function(){
			var itm=$(this);var scrollBottom=$(this).scrollTop()+$(this).height() >= 0.9*($(this)[0].scrollHeight);
			if (scrollBottom){
				if ($(itm).is('#myFeeds') && !loadingOldM){
					loadingOldM=true;loadingFeeds('#myFeeds');loadFeeds(0,1);
				}
				else if (!$(itm).is('#myFeeds') && !loadingOldA){
					loadingOldA=true;loadingFeeds('#allFeeds');loadFeeds(0,0);
				}
			}
		});
		$('<div class="flipswitch">'+
				'<input type="checkbox" name="flipswitch" class="flipswitch-cb" id="fs">'+
				'<label class="flipswitch-label" for="fs">'+
				'	<div class="flipswitch-inner"></div>'+
				'	<div class="flipswitch-switch"></div>'+
				'</label>'+
			'</div>').appendTo('#feedBoxHeading').click(function(){
			myFeed=($(this).find('input').is(':checked'))?1:0;
			if(myFeed) {$('#myFeeds').css({'left':'0','display':'block'});$('#allFeeds').css({'left':'100%','display':'none'});}
			else {$('#myFeeds').css({'left':'-100%','display':'none'});$('#allFeeds').css({'left':'0','display':'block'});}
		});
		
		//<input type="checkbox" id="showMyFeeds" /><label for="showMyFeeds">Only me</
	}
	return _feedPuller;	
}();
var feedPuller;
if (feedPuller===undefined){
	feedPuller=FeedPuller;
}