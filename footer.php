	</div>
    <?php
		$loc = theme_option('sidebar_location');
		if($loc==2 || $loc==4) {
			get_sidebar(); // calling the First Sidebar
		}
		if(theme_option('sidebar_width2')!=0 && $loc!=3) get_sidebar( "second" ); // calling the Second Sidebar
	?>
</div>
<!-- begin footer -->
<div id="footer">
    <?php printf(__("Copyright &copy; %d", "magazine-basic"), date('Y')); ?> <a href="<?php bloginfo('url') ?>"><?php bloginfo('name'); ?></a>. <?php _e("All Rights Reserved", "magazine-basic"); ?>.<br />
<?php /*
    <span class="red"><?php echo THEME_NAME; ?></span> <?php _e("theme designed by", "magazine-basic"); ?> <a href="http://themes.bavotasan.com"><span class="red">Themes by bavotasan.com</span></a>.<br />
    <?php _e("Powered by", "magazine-basic"); ?> <a href="http://www.wordpress.org">WordPress</a>.
    */?>
</div>
<?php wp_footer(); ?>
<script type="text/javascript" src="<?php echo THEME_URL; ?>/js/effects.js"></script> 
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function(){
	jQuery("ul.sf-menu").supersubs({ 
		minWidth:    12,
		maxWidth:    27,
		extraWidth:  1
	}).superfish({ 
		delay:       100,
		speed:       250 
	});	});
/* ]]> */
</script>
<?php /*
<!-- <?php echo THEME_NAME; ?> theme designed by Themes by bavotasan.com - http://themes.bavotasan.com -->
<?php if(theme_option('google_analytics')) { echo stripslashes(theme_option('google_analytics')); } ?>
*/?>
</body>
</html>
