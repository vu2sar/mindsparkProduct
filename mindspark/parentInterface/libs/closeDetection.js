var tryingToUnloadPage = false;
function setTryingToUnload(){
	tryingToUnloadPage = true;
}
jQuery(document).ready(function(e) {
	jQuery("a").click(function(){
		setTryingToUnload();
		if($(this).attr('target')=="_blank" && $(this).attr('target')!="undefined"){
			tryingToUnloadPage = false;
		}
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
	if(tryingToUnloadPage == false){	
		jQuery.ajax({
			type: "POST",
			url: "commonAjax.php?mode=endSessionTime",
			"async": false,
			success: function(msg){
			     //alert( "Data Saved: " + msg );
			}
		});
	}
}