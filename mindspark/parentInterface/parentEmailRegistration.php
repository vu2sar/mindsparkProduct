<?php
include("header.php");
?>
<title>Parent e-mail registration</title>
<link href="css/common.css" rel="stylesheet" type="text/css">
<script>
    var langType = '<?= $language; ?>';
    function redirect(URL)
    {
		setTryingToUnload();
        window.location=URL;
    }
</script>
</head>
<body class="translation" style="height: 100%; vertical-align: central" height="100%">
    <?php include("eiColors.php") ?>
    <div class="logo">
    </div>
    <div style="vertical-align: middle; text-align: center; margin:0 auto;width: 40%; margin-left: auto; margin-top: auto; padding-top:10%; font-size: 1.5em; ">
        <div style="vertical-align: central; font-size: 1.9em; color: #2f99cb">Welcome to Mindspark Parent Connect!</div>
        <br/>
        <div style="margin-bottom: 10px;">Is your son/daughter an existing Mindspark user?</div>
        <br/>
        <input type="submit" class="loginButton" id="yes" value="Yes" onClick="javascript:redirect('childVerificationOuter.php')" />
        <input type="submit" class="loginButton" id="no" value="No, Subscribe Now" onClick="javascript:redirect('http://www.mindspark.in/registrationform.php')"/>
        <input type="submit" class="loginButton" id="cancel" value="Cancel" onClick="javascript:redirect('../logout.php')"/>
    </div>
    <div id="bottom_bar" style="position:fixed; width:100%; padding:0px; bottom:0px;">
        <div id="copyright" data-i18n="[html]common.copyright" style="height: 100%;">&copy; 2009-2014, Educational Initiatives Pvt. Ltd.</div>
    </div>   
</body>
</html>