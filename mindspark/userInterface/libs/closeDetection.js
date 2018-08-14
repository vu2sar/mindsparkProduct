var tryingToUnloadPage = false;
function setTryingToUnload(){
	tryingToUnloadPage = true;
}
if(window.location.href.indexOf("teacherInterface") < 0)
{
	jQuery(document).ready(function(e) {
		jQuery("a").click(function(){
			setTryingToUnload();
		});	
	});

	jQuery(document).keydown(function(e) {
		   if (e.keyCode == 116){
			   tryingToUnloadPage = true;
		   }
		   if (e.keyCode == 82 && e.ctrlKey) {
			   tryingToUnloadPage = true;
		   }
		   if (e.keyCode == 82 && e.shiftKey && e.ctrlKey) {
			   tryingToUnloadPage = true;
		   }
	   });
	   

	window.onbeforeunload = function() {
		if(tryingToUnloadPage==false) {
			return 'Are you sure that you want to close or refresh this page?';
		}
	};
	 
	window.onunload = function() {
		if(tryingToUnloadPage === false){	
			jQuery.ajax({
				type: "POST",
				url: "../userInterface/controller.php?mode=endSessionType",
				"async": false,
				success: function(msg){
					 //alert( "Data Saved: " + msg );
				}
			});
		}
	}
 }