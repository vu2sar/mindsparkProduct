<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Mindspark English</title>
    <base href= <?php echo base_url(); ?> ></base>
    <link rel="stylesheet" href="../../ms_english/theme/css/bootstrap.min.css"></link>
    <link rel="stylesheet" href="../../ms_english/theme/font-awesome/css/font-awesome.min.css"></link>
    <script type="text/javascript" src="../../ms_english/theme/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="../../ms_english/theme/js/jquery-ui.interactions-dialog-tooltip.min.js"></script>
    <script type="text/javascript" src="../../ms_english/theme/js/encrypt.js"></script>
    <style>
        @font-face {
            font-family: "Gotham-Book";
            src: url('../../ms_english/theme/fonts/Gotham-Book.eot');
            src: local('Gotham-Book'), url('../../ms_english/theme/fonts/Gotham-Book.eot?#iefix') format('embedded-opentype'), url('../../ms_english/theme/fonts/Gotham-Book.woff') format('woff'), url('../../ms_english/theme/fonts/Gotham-Book.ttf') format('truetype'), url('../../ms_english/theme/fonts/Gotham-Book.svg#webfont') format('svg');
        }
		a {
			color: black;
			cursor: pointer;
		}
		
		body, html {
			height: 100%;
			width: 100%;
			overflow: hidden;
            font-family: "Gotham-Book";
        }
        
        a.navbar-brand{
            cursor: default;
        }
        .btn
        {
            font-family: "Gotham-Book";
            font-size: 1.5em;
        }
       
        .hovers {
            display: none;
            z-index: 100000;
            position: absolute;
            left: 50%;
            top: 50%;
            padding: 7px;
            max-height: 90%;
            height: auto;
            -webkit-transform: translate(-50%,-50%);
            -moz-transform: translate(-50%,-50%);
            -ms-transform: translate(-50%,-50%);
            transform: translate(-50%,-50%);
            -webkit-box-shadow: 0px 0px 6px #333333 !important;
            -moz-box-shadow: 0px 0px 6px #333333 !important;
            box-shadow: 0px 0px 6px #333333 !important;
            border-radius: 3px;
            background-color: #eee;
            min-height: 400px;
            height: 90%;
        }
        
        .containers {
            overflow-x: hidden;
            overflow-y: hidden;
        }
        .containers .fancyText {
            padding: 5px;
        }
        #aboutMSE .containers {
            font-size: 15.4px;
        }
        
        .hovers::before {
            position: absolute;
            width: 20px;
            height: 20px;
            content: 'X';
            top: -16px;
            right: -17px;
            background-color: inherit;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid #555;
            line-height: 18px;
        }

        .icon{
            padding:10px;
        }
        .form-group > *{
            font-size: 20px;
            height: 45px;
            
        }
        
        .navbar {
            margin-bottom: 0px;
            height: 60px;
        }

        #headerContainer {
            text-align: center;
        }


        .navbar-brand {
            height: auto;
            padding: 0px !important;
            padding-top: 15px !important;
        }

        .navbar-brand img {
            margin-top: -12px;
            width: 276px;
            cursor: pointer;
        }

        #links {
            float: none;
            display: inline-block;
            vertical-align: middle;
        }

        #corousel img {
            height: 100%;
            width: 25%;
            float: left;
            margin: 0px;
        }

        #corousel {
            width: 400%;
            height: 100%;
        }

        #mainContent {
            height: calc(100% - 60px);
            position: relative;
        }

        #corouselWrapper {
            height: 100%;
            padding: 0px;
        }

        #corousel img {
            -webkit-transition: left 2s;
            transition: left 2s;
            position: relative;
            display: block;
            vertical-align: baseline;
        }

        #formWrapper {
            height: 100%;
            background-color: rgba(255,255,255,0.7);
            position: absolute;
            right: 0px;
            /*width: 20%;*/
            top: 0px;
            /*min-width: 240px;*/
        }
        .logoOverlay
        {
            position: absolute;
            /*width: 80%;*/
            height: 101%;
            z-index: 2;
            background: rgba(255, 255, 255,0.4);
            color: black;
            padding: 17px;
            font-size: 19px;
            font-family: "Gotham-Book";
        }
        .corouselNavigator {
            position: fixed;
            left: 1%;
            text-align: center;
            bottom: 1em;
        }

        .corouselButton {
            background-color: #FFF;
            width: 15px;
            height: 15px;
            cursor: pointer;
            float: left;
            margin-left: 15px;
            border-radius: 2px;
        }

        .ui-tooltip {
            padding: 8px;
            position: absolute;
            z-index: 1000000;
            max-width: 200px;
            background-color: white;
            font-weight: bold;
            font-size: 15px;
            color: black;
            border-radius: 5px;
            box-shadow: 0px 0px 4px gray;
        }

        .showTooltip[title]:hover {
            box-shadow: 0px 0px 2px 2px black !important;
        }

        .tooltipHighlight {
            box-shadow: 0px 0px 2px 2px black !important;
        }

        form {
            top: 50%;
            -webkit-transform: translate(0, -50%);
            -moz-transform: translate(0, -50%);
            -ms-transform: translate(0, -50%);
            -o-transform: translate(0, -50%);
            transform: translate(0, -50%);
            position: relative;
            padding: 10px;
        }

        .active {
            opacity: 1;
            background-color: #777777;
        }

        #logo {
            height: auto;
            left: 2%;
            position: absolute;
            top: 2%;
            width: 10%;
            z-index: 1000;
            background-color: rgba(255,255,255,0.3);
            box-shadow: 0px 0px 3px #888888;
            border-radius: 3px;
        }

        #loginButton {
            width: 100%;
            height: 45px;
        }
        
        .ui-widget-overlay {
            display: none;
            background: none repeat scroll 0 0 #808080;
            bottom: 0;
            left: 0;
            opacity: 0.7;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 10000;
        }
        /* span on login error*/
        #errorMessage
        {
            color: red;
            position: absolute;
            top: 386px;
            font-size: 15px;
            background-color: white;
            width: 100%;
            text-align: center;
        }
        .fa
        {
            line-height: 2.2em;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid" id="headerContainer">
            <div id="branding" class="navbar-header">
                <a class="navbar-brand"> <img src="../../ms_english/theme/img/Language/logo-01.png" onclick="explainMSE()"> </a>
            </div>
            <!--ul id="links" class="nav navbar-nav">
                <ul id="links" class="nav navbar-nav">
                <li>
                    <a title="Educational Initiatives believes in making a difference in education through personalized learning and ensure that students learn with understanding.">About EI </a>
                </li>
                <li>
                    <a title="Phone: +91-79-66211600 Fax: +97-79-66211700 Email: info@ei-india.com">Contact </a>
                </li>
            </ul>
            </ul-->
        </div>
    </nav>
    <div id="mainContent" class="row">
        <!-- <div class='logoOverlay col-md-9 col-xs-6' style="display:block">
            <div class="containers row">
                <p class="col-md-6 fancyText">One could argue that English is a necessary skill to survive our increasingly globalizing world. English in India has a very unique status – It is understood by many, but used correctly by very few.  </p>
                <p class="col-md-6 fancyText">We use English terms unconsciously all the time, but fluent reading and writing remains a gargantuan task for a large part of our populace. The reasons for this are manifold – lack of exposure, lack of proper teaching and lack of practice, amongst a dozen other problems. So where do we start?</p>
            </div>
            <div class="containers row">
                <p class="col-md-6 fancyText">There are conflicting views on the ‘best’ way to teach English. Some encourage complete memorization of grammatical rules and extensive writing practice to iron out errors and misconceptions. Others seek to teach through competitive learning and standardized textbook education, to ensure equal learning levels.</p> 
                <p class="col-md-6 fancyText">Inquiry-based educators prefer a constructivist approach, letting students figure out syntax and semantics via observation and interaction without much interference from the teacher. Many people encourage translation-based learning, for students to use existing knowledge of a language to learn another. </p>
            </div>
            <div class="containers row">
                <p class="col-md-6 fancyText">At Mindspark, we enable children to engage with English through adaptive and immersive learning. Mindspark English is being built on a combination of 3 strengths – technology, pedagogy and innovation. Structurally, Mindspark English will cover all major skills with Listening, Speaking, Reading and Writing. However, our purpose is to help children apply their knowledge of language, not to memorise rules.</p>
                <p class="col-md-6 fancyText">Based on student and expert feedback as well as collaboration, we are continuously refining and building upon our core features. The aim is for a student of English language to have the best learning experience and extract maximum value from Mindspark.</p>
            </div>
        </div> -->
        <div id="corousel" class="first">
            <img id="corousel1" src="../../ms_english/theme/img/Language/login/homepage-01.png" class="corouselImage">
            <img id="corousel2" src="../../ms_english/theme/img/Language/login/homepage-02.png" class="corouselImage">
            <img id="corousel3" src="../../ms_english/theme/img/Language/login/homepage-03.png" class="corouselImage">
        </div>
        <div class="corouselNavigator">
            <div class="corouselButton active"></div>
            <div class="corouselButton"></div>
            <div class="corouselButton"></div>
        </div>
        <div id="formWrapper" class="col-md-4 col-lg-3">
            <form id="formSubmit" method="post">
                <div class="form-group has-feedback has-feedback-left">
                    <i class="fa fa-user form-control-feedback"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Username">
                </div>
                <div class="form-group has-feedback has-feedback-left">
                    <i class="fa fa-lock glyphicon form-control-feedback"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                </div>
                <button type="button" id="loginButton" class="btn btn-primary" onclick="submitForm()"> Login </button>
            </form>
        </div>
    </div>
    <div id="contact" class="hovers">
        Educational Initiatives Pvt. Ltd.<br>A/201, Baleshwar Square,<br>Opp. ISKCON Temple, S.G. Highway,<br>Ahmedabad 380015<br>INDIA<br><br>Phone: +91-79-66211600.<br>Fax: +91-79-66211700.<br>Email: <a href="mailto:info@ei-india.com" target="_blank">info@ei-india.com</a>
    </div>
    <div id="aboutEI" class="hovers">
        <div class="containers">
            <p>Founded by a group of IIMA alumni, with ample personal experience of educational institutions, Educational Initiatives (EI) is an effort to ensure every child learns with understanding.</p>
            <p>Established in 2001, Educational Initiatives believes in making a difference in education through personalized learning and ensuring that students learn with understanding.</p>
            <p>EI has over 15 years of expertise in education, with a deep understanding of child psychology and efficient methods of teaching, based on detailed research and a formidable database of student learning through ASSET.</p>
            <p>Our detailed research has proven that children today respond to rote-based questions relatively well, however, they fail to answer unfamiliar or application based questions due to unclear core concepts.</p>
            <p>We aim at addressing this fundamental problem with a competitive team of professionals from the industry and strong trust based relationships that we share with over 3,000 schools in India over the last 14 years. Our team consists of professionals who have vast experience in the field of school education. The team members have taught in leading schools, been members of the state textbook committees and designed and taught courses at the school as well as teacher-training level.</p>
            <p>We believe in learning through understanding, so that the education lasts the students for a lifetime as a tool to help them in all their endeavours.</p>
            <p>Through our other interactive tools like Mindspark (digital self learning program), ASSET, Detailed Assessment, CCE Certificate Course, Teacher Evaluation Program, Teacher Sheets and more, Educational Initiatives assists thousands of teachers in improving their students’ achievements.</p>
            <p>EI is also working with leading organizations like World Bank, Michael and Susan Dell Foundation, Google, Azim Premji Foundation and is doing large scale assessment projects with various State Governments. In the last 14 years, EI has assessed over 2 million students, with more than 65,000 students experiencing personalized learning through Mindspark in cities like Lansing and Michigan, USA.</p>
            <p>Today, EI has worked with over 3000 schools with over 3.5 lac students taking the EI’s International Benchmarking Test – ASSET every year and has a presence in UAE, Kuwait, Singapore and USA. EI, in association with Google, has also conducted one of the largest studies to know the student learning levels in government schools in 21 states of India, along with a Students Learning in Metros Study conducted in association with Wipro. The Government of Bhutan has also partnered with EI to conduct Student and Teachers Assessments on annual basis.</p>
        </div>
    </div>
    <div id="aboutMSE" class="hovers">
        <div class="containers">
            <p>One could argue that English is a necessary skill to survive our increasingly globalizing world. English in India has a very unique status – It is understood by most, used by many, but used correctly by very few.  </p>
            <p>We use English terms unconsciously all the time, but fluent reading and writing remains a gargantuan task for a large part of our populace. The reasons for this are manifold – lack of exposure, lack of proper teaching and lack of practice, amongst a dozen other problems. So where do we start?</p>
            <p>There are conflicting views on the ‘best’ way to teach English. Some encourage complete memorization of grammatical rules and extensive writing practice to iron out errors and misconceptions. Others seek to teach through competitive learning and standardized textbook education, to ensure equal learning levels.</p> 
            <p>Inquiry-based educators prefer a constructivist approach, letting students figure out syntax and semantics via observation and interaction without much interference from the teacher. Many people encourage translation-based learning, for students to use existing knowledge of a language to learn another. </p>
            <p>At Mindspark, we enable children to engage with English through adaptive and immersive learning. Mindspark English is being built on a combination of 3 strengths – technology, pedagogy and innovation. Structurally, Mindspark English will cover all major skills with Listening, Speaking, Reading and Writing. However, our purpose is to help children apply their knowledge of language, not to memorise rules.</p>
            <p>Based on student and expert feedback as well as collaboration, we are continuously refining and building upon our core features. The aim is for a student of English language to have the best learning experience and extract maximum value from Mindspark.</p>
        </div>
    </div>
    <div id="modalBlocker" onclick="hideHovers()" class="ui-widget-overlay"></div>
    <script>
        var fancyArr = {
            0:
            ["orange","46%","rotate(-4deg)","25px","11px"],
            1:
            ["yellowgreen","49%","rotate(3deg)","-110px","512px"],
            2:
            ["#60FD71","53%","rotate(0deg)","-9px","6px"],
            3:
            ["#FB60FC","39%","rotate(-2deg)","-125px","600px"],
            4:
            ["#F9F96B","45%","rotate(-6deg)","-44px","9px"],
            5:
            ["#60FDF1","47%","rotate(3deg)","-146px","506px"]
        };
        $(document).ready(function(){
            for(i=0; i<$(".containers .fancyText").length; i++)
            {
                $($(".containers .fancyText")[i]).css({
                    "background-color":fancyArr[i][0],
                    //"width":fancyArr[i][1],
                    //"transform":fancyArr[i][2],
                    //"margin-top":fancyArr[i][3],
                    //"margin-left":fancyArr[i][4]
                });
            }
            $('input').keypress(function (e) {
                if (e.which == 13) {
                    submitForm();
                    
                }
            });
        });
        var timeDelayBetweenImages = 5000;
        var corouselPosition = 0;
        
        function submitForm() {
            var username = document.getElementById("username").value;
            //var password = CryptoJS.MD5(document.getElementById("password").value);
            var password = document.getElementById("password").value;
            password = password.toString();
            if (username.trim() != "" && password.trim() != "") {
                localStorage.clear(); // clears the local storage variable for question number
            } else {
                alert("Please enter login credentials");
                return false;
            }
            $.ajax({
                url : 'Language/login',
                data : { 'username' : username , 'password' : password },
                method : 'POST',
                success: function(data){
                    data = $.parseJSON(data);
                    if(data.allow == 0)
                    {
                        window.location = 'Language/session';
                    }
                    else if(data.allow == 1)
                    {
                        alert("Invalid credentials. Please try again");
                    }
                    else if(data.allow == 2)
                    {
                        alert("Your data is not synced. Please try again later!!");
                    }else if(data.allow == 3)
                    {
                        alert("Sorry! Home usage is not allowed for this account.");                        
                    }
                },
                error : function(){
                    alert("Something went wrong.");
                }

            });
        };

        var corouselButtons = document.getElementsByClassName('corouselButton');
        for (var i = 0; i < corouselButtons.length; i++) {
            corouselButtons[i].addEventListener('click', function(index) {
                return function() {
                    corouselPosition = index;
                    setImage();
                }
            }(i), 'false');
        }
        
        $('.hovers').on('click', onHoverClick);
        function onHoverClick(event) {
            if (event.offsetX > this.offsetWidth - 4 && event.offsetY < 2) {
                hideHovers(this.id);
            }
        }

        function setImage() {
            clearTimeout(timerId);
            $('.corouselImage').css('transition','left 2s');
            var leftString = -((100 / (corouselButtons.length+1)) * corouselPosition) + '%';
            $('.corouselImage').css('left', leftString);
            $('.corouselButton').removeClass('active');
            if(corouselPosition < corouselButtons.length){
                corouselButtons[corouselPosition].className = 'corouselButton active';
            }
            else{
                corouselButtons[0].className = 'corouselButton active';
            }
            startTimeoutForNextImage();
        }

        function startTimeoutForNextImage() {
            timerId = setTimeout(function() {
                corouselPosition++;
                if (corouselPosition > corouselButtons.length) {
                    $('.corouselImage').css('transition','none');
                    $('.corouselImage').css('left', '0%');
                    corouselPosition = 1;
                }
                setTimeout(setImage, 1000);
            }, timeDelayBetweenImages-1000)
        }
        
        var corousel = $('#corousel')[0];
        $(corousel).append($(corousel.children[0]).clone());
        startTimeoutForNextImage();
        
        $('[data-open]').bind('click', function(){
            $('#' + $(this).attr('data-open')).show();
            $('.ui-widget-overlay').show();
        });
        
        function hideHovers(hidingWhat) {
            $('.hovers').hide();
            $('#modalBlocker').hide();
        }
        function explainMSE()
        {
            /*$("#aboutMSE").show();
            $('#modalBlocker').show();*/
            if($(".logoOverlay").is(":visible"))
                $(".logoOverlay").fadeOut(500);
            else
                $(".logoOverlay").fadeIn(500);
        }
        
        $("[title]").bind('mouseenter', function(){
            $('.ui-tooltip').hide();
            $(this).tooltip('open');
        }).tooltip({
            show: {
                delay: 0,
                duration: 0
            },
            hide: {
                delay: 200,
                duration: 200
            }
       });

    </script>
</body>
</html>
