<?php if( $iPad ==true|| $Android ==true){?>
<script>
    doOnOrientationChange();
</script>
<style>


@media all and (orientation:portrait) {
    #prmptContainer {
        display: block;
    }
    #promptBox
    {
        margin-left: 50px;        
    }
}
@media all and (orientation:landscape) {
    #prmptContainer {
        display: none;
    }
   
}
</style>
<?php }?>
<div style="display:none">
        <div id="openHelp">
			<h2 align="center">Quick Tutorial</h2>
            <iframe id="iframeHelp" width="960px" height="440px" scrolling="no"></iframe>
        </div>
    </div>
<div id="bottom_bar">
    <div id="copyright" data-i18n="[html]common.copyright"></div>
</div>
</body>
</html>