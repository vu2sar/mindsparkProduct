
function resetzoom(e)
            {
                ////alert("check");
                //document.getElementById("view").setAttribute('content','user-scalable=yes, initial-scale=1.0,width=device-width, minimum-scale = 1.0, maximum-scale = 3.0');
	   var mobile_timer = false;
	   var ua = navigator.userAgent;
	    var checker = {  apple: ua.match(/(iPhone|iPod|iPad)/),
	 macintosh: ua.match(/Macintosh/),
	android: ua.match(/Android 3.2|Android 3.1|Android 3.0/)
	 };
	    
var orientation = window.orientation; 
/* if(checker.apple)
	 {
switch(orientation) { 
    case 0: 
            
    replacejscssfile("ipadlongdivision.css", "ipadportriat.css", "css");
             
              break;
         //Portrait mode 
    case 90:  
    replacejscssfile("ipadportriat.css","ipadlongdivision.css", "css");
        
          break;
          
         // Landscape left 
    case -90: 
    replacejscssfile("ipadportriat.css","ipadlongdivision.css", "css");
        
          break;
         //Landscape right 
     case 180:
     replacejscssfile("ipadlongdivision.css", "ipadportriat.css", "css");
           
              break;

} 
	 }else
		 {*/
		
		 switch(orientation) { 
		    case 0: 
		  
		    replacejscssfile("androidportrait.css","androidlandscape.css", "css");         
		              break;
		         //Portrait mode 
		    case 90:  
		    	replacejscssfile("androidlandscape.css", "androidportrait.css", "css");
		    
		          break;
		          
		         // Landscape left 
		    case -90: 
		   
		    replacejscssfile("androidlandscape.css", "androidportrait.css", "css");   
		          break;
		         //Landscape right 
		     case 180:

		    	 replacejscssfile("androidportrait.css","androidlandscape.css", "css");       
		              break;

		} 
		 
 
    
           
   if (checker.apple || checker.macintosh)
   {
    //e.target.style.webkitTransform="scale(1)";
        var viewportmeta = document.querySelector('meta[name="viewport"]');
        if (viewportmeta) {
            
            viewportmeta.content = 'width=device-width, minimum-scale=0.25,maximum-scale=1.0, initial-scale=1.0';
            document.body.addEventListener('gesturestart', function () {
                clearTimeout(mobile_timer);
               viewportmeta.content = 'width=device-width,minimum-scale=0.25, maximum-scale=1.6';
            }, false);
            
            window.addEventListener('touchend',function () 
            {
            mobile_timer = setTimeout(function () {
                clearTimeout(mobile_timer);
                viewportmeta.setAttribute('content','width=device-width,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0');
            },1000);
        },false);
            
        }
 }
  
            }
function createjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
 }
 return fileref
}

function replacejscssfile(oldfilename, newfilename, filetype){
 var targetelement=(filetype=="js")? "script" : (filetype=="css")? "link" : "none" //determine element type to create nodelist using
 var targetattr=(filetype=="js")? "src" : (filetype=="css")? "href" : "none" //determine corresponding attribute to test for
 var allsuspects=document.getElementsByTagName(targetelement)
 for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
  if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(oldfilename)!=-1){
   var newelement=createjscssfile(newfilename, filetype)
   allsuspects[i].parentNode.replaceChild(newelement, allsuspects[i])
  }
 }
}
