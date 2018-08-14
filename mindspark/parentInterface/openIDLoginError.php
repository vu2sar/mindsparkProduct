<title>Parent e-mail registration</title>
<link href="css/common.css" rel="stylesheet" type="text/css">
<script>
    var langType = '<?= $language; ?>';
</script>
</head>
<body class="translation" style="height: 100%" height="100%">
    <?php include("eiColors.php") ?>
    <div class="logo">
    </div>
    <div style="alignment-adjust: central; vertical-align: middle; padding-top: 200px; font-size: 1.5em; text-align: center">
        Oops! There was an error in login process.
        <br/>Error from <?= $_GET['openIDProvider']; ?>:<br/>
        <?= stripslashes($_GET['error']); ?>
        <br/><br/>
        <a href="../logout.php" class="usual">Back</a>
    </div>
    <div id="bottom_bar" style="position:absolute; width:100%; padding:0px; bottom:0px;">
        <div id="copyright" data-i18n="[html]common.copyright" style="height: 100%;">&copy; 2009-2014, Educational Initiatives Pvt. Ltd.</div>
    </div>
    <!--    <div style="position:fixed; width:100%; height:70px; background-color:yellow; padding:5px; bottom:0px; ">
                test content :D
            </div>-->
</body>
</html>