</div>
</div>
</div>
        <?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */ /* <div data-role="footer">
		<h4>Footer content</h4>
	</div><!-- /footer --> */ ?>

</div><!-- /page -->
<?php if(module_config::c('mobile_content_scroll',1) && module_security::is_logged_in()){ ?>
<script type="text/javascript">
    var contentscroll = [];
    var content = null;
    window.addEventListener("resize", function() {
        // Get screen size (inner/outerWidth, inner/outerHeight)
//        var headerheight = 20;
//        $('div[data-role="header"]').each(function(){
//            headerheight+= $(this).height();
//        });
//        if(content != null)content.width(window.innerWidth-10).height(window.innerHeight-headerheight);
//        if(contentscroll != null)contentscroll.refresh();
        for (var i in contentscroll){
            if(typeof contentscroll[i] == 'object'){
                contentscroll[i].refresh();
            }
        }
    }, false);

    $(function(){
        /*if(content == null){
            content = $('#mobile_content');
        }
        contentscroll = new iScroll(content[0] ,{
            onBeforeScrollStart: null,
            onTouchEnd: null,
            vScroll:true,
            hScroll:true,
            bounce: false,
            momentum: true,
            hScrollbar: false,
            vScrollbar: false
        });*/
        $('.iscroll').each(function(){
            contentscroll.push( new iScroll(this ,{
                onBeforeScrollStart: null,
                onTouchEnd: null,
                vScroll:true,
                hScroll:true,
                bounce: false,
                momentum: true,
                hScrollbar: false,
                vScrollbar: false
            }) );
        });
    });
</script>
<?php } ?>
</body>
</html>