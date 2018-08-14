$(document).ready(function(){
    if(navigator.userAgent.indexOf("Android") != -1)
    {
        isAndroid = true;
    }
    else if(window.navigator.userAgent.indexOf("iPad")!=-1 || window.navigator.userAgent.indexOf("iPhone")!=-1)
    {
        isIpad = true;
    }
    
    if(screen.width < 1025)
    {
      if($("#upperPart .partHeader .partHeaderText").text().trim().length <= 40)
          $("#upperPart .partHeader .partHeaderText").css("line-height","60px");
      else
          $("#upperPart .partHeader .partHeaderText").css("line-height","30px");

      if($("#lowerPart .partHeader .partHeaderText").text().trim().length <= 40)
          $("#lowerPart .partHeader .partHeaderText").css("line-height","60px");
      else
          $("#lowerPart .partHeader .partHeaderText").css("line-height","30px");
    }
    else
    {
      if($("#upperPart .partHeader .partHeaderText").text().trim().length <= 58)
          $("#upperPart .partHeader .partHeaderText").css("line-height","60px");
      else
          $("#upperPart .partHeader .partHeaderText").css("line-height","30px");

      if($("#lowerPart .partHeader .partHeaderText").text().trim().length <= 58)
          $("#lowerPart .partHeader .partHeaderText").css("line-height","60px");
      else
          $("#lowerPart .partHeader .partHeaderText").css("line-height","30px");
    }

    if(isAndroid)
    {
       $("#homeTextInner").css("top","15px"); 
       $(".class,.Name").css("margin-top","30px");
       $(".partActionBtn").css("line-height","2.5");
       $(".partHeader").css("margin-top","5px");
    }
    if(isIpad)
    {
       $("#homeTextInner").css("top","15px"); 
       $(".partActionBtn").css("line-height","2.5");
       $("#doughNutInner").css("left","18%");
       $(".clusterName").css("margin-left","10px");
       $("#progressData").css({"left":"140px","top":"-120px"});
       $(".partHeaderText").css("width","60%");
       $(".partHeader").css("margin-top","10px");
       $("#lowerPart").css("margin-top","-12px");
    }
    setTimeout(function(){
      $("#leftPart").css("height",document.getElementById("container").scrollHeight+"px"); // For coloring left div

    },500);

});