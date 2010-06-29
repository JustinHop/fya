<?php get_header(); ?>
	<?php
    $options = get_option("widget_sideFeature");
    $posts = theme_option('number_posts');
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if (is_active_widget('widget_myFeature')) {
        $args = array(
           'cat'=>'-'.$options['category'],
           'posts_per_page'=>$posts,
           'paged'=>$paged,
           );
    } else {
        $args = array(
           'posts_per_page'=>$posts,
           'paged'=>$paged,
           );
    }
	if(!theme_option('number_posts')) {
        $args = array(
           'posts_per_page'=>6,
           'paged'=>$paged,
           );		
	}       	
    $x = 1;
    query_posts($args);
    ?>
    <?php 
	if(is_front_page() && $paged < 2) { 
		if(theme_option('latest_story')=="on") { echo '<h5 class="latest">'.__('Latest Story', "magazine-basic").'</h5>'; }
		$optionlayout = "option".theme_option('post_layout');
		if($optionlayout) { 
			include (TEMPLATEPATH.'/layout/'.$optionlayout.'.php'); 
		} else {
			include (TEMPLATEPATH.'/layout/option3.php'); 
		}
	} else {
		include (TEMPLATEPATH.'/layout/option1.php');
	}	
    ?>
	<?php
    if(function_exists('pagination')) { pagination(); }
    ?>
<?php get_footer(); ?>