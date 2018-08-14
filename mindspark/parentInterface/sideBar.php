
<a href="home.php"><div id="home">
        <div class="home"></div>
        <div class="sideText1">HOME</div>
    </div></a>
<a href="accountManagement.php"><div class="sideMenu">
        <div class="arrow-right"></div>
        <div class="rectangle-right"></div>
        <div class="sideText" id="classes">MY ACCOUNT</div>
    </div></a>
<?php if ($_SESSION['childSubcategory'] == "Individual" && $_SESSION['childClassUsed']!=1 && $_SESSION['childClassUsed']!=2 && $_SESSION['childClassUsed']!=10) { ?>
    <a href="aqad.php"><div class="sideMenu">
            <div class="arrow-right-yellow"></div>
            <div class="rectangle-right-yellow"></div>
            <div class="sideText" id="students">QUESTION-A-DAY</div>
        </div></a>
<?php } ?>
<a href="topicUsage.php"><div class="sideMenu">
        <div class="arrow-right-blue"></div>
        <div class="rectangle-right-blue"></div>
        <div class="sideText" id="features">REPORTS</div>
    </div></a>
<a href="registerStudent.php"><div class="sideMenu">
        <div class="arrow-right-green"></div>
        <div class="rectangle-right-green"></div>
        <div class="sideText" id="registerChild">REGISTER ANOTHER CHILD</div>
    </div></a>
	<!--<a href="http://www.mindspark.in/summer" target="_blank"><img src="assets/summer.jpg" style="margin-left:10px;margin-top:14px;"/></a>-->
	<div id="essay" onclick="showPdf();" style="cursor:pointer;"><img src="assets/EWS_logo.png" style="margin-left:10px;margin-top:14px;"/></div>
<?php if ($_SESSION['childSubcategory'] == "Individual" && $_SESSION['childClassUsed']>=5 && $_SESSION['childClassUsed']<=7) { ?>	
<!--<div class="blackboard_screen_notice" align="justify">
    <div class="titleTestimonial" align="justify">Notice Board</div>
    <div class="testimonial" align="justify"><p>Dear Parents,</br>Mindspark <b>Super Test</b> is back.</br><b>Date:</b> 6th to 8th February, 2015</br><b>Topic:</b> Geometry<br/><b>Duration: </b>30 minutes</p></div>
</div>-->
<?php } ?>
<div class="blackboard_screen<?php //if ($_SESSION['childSubcategory'] == "Individual" && $_SESSION['childClassUsed']>=5 && $_SESSION['childClassUsed']<=7) echo " blackboard_screen_alert" ?>" align="justify">
    <div class="titleTestimonial" align="justify">Testimonial</div>
    <div class="testimonial" align="justify"><p>Our children are really benefited by Mindspark where they get different types of question that enhance their thinking ability and their understanding skills. Questions are very interesting and application based. Overall their performance in Maths have improved a lot. Thank you so much.</p>
    <cite>-Mrs. Jhumur Gupta,</cite>
	<cite>Mindspark Co-ordinator, Sarala Birla Gyan Jyoti, Guwahati</cite>
</div>

</div>