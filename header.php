<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php if((is_home() && ($paged < 2 )) || is_single() || is_page()) { echo '<meta name="robots" content="index,follow" />'; } else { echo '<meta name="robots" content="noindex,follow" />'; } ?>

<?php if (is_single() || is_page() ) : if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<meta name="description" content="<?php metaDesc(); ?>" />
<?php csv_tags(); ?>
<?php endwhile; endif; elseif(is_home()) : ?>
<meta name="description" content="<?php if(theme_option('site_description')) { echo trim(stripslashes(theme_option('site_description'))); } else { bloginfo('description'); } ?>" />
<meta name="keywords" content="<?php if(theme_option('keywords')) { echo trim(stripslashes(theme_option('keywords'))); } else { echo 'wordpress,c.bavota,feed me seymour,custom theme,themes.bavotasan.com,premium themes'; } ?>" />
<?php endif; ?>

<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' | '; } ?><?php bloginfo('name'); if(is_home()) { echo ' | '; bloginfo('description'); } ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo THEME_URL; ?>/admin/css/theme-style.css" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php echo THEME_URL; ?>/iestyles.css" />
<![endif]-->
<!--[if lte IE 6]>
<script defer type="text/javascript" src="<?php echo THEME_URL; ?>/images/pngfix.js"></script>
<![endif]-->

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_enqueue_script('jquery'); ?>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<!-- begin header -->
<div id="header">
	<?php
    $headeralign = theme_option('logo_location');
	if($headeralign=="fl") $adfloat = ' class="fr"';
	if($headeralign=="fr") $adfloat = ' class="fl"';
	if($headeralign=="aligncenter") $adfloat = ' class="aligncenter"';
	$float = ' class="'.$headeralign.'"';
    ?>
	<?php if (theme_option('user_login') != "2") { ?>
	<div id="login">
    	<?php
			global $user_identity, $user_level;
			if (is_user_logged_in()) { ?>
            	<ul>
                <li><span style="float:left;"><?php _e("Logged in as:", "magazine-basic"); ?><strong> <?php echo $user_identity ?></strong></span></li>
				<li><a href="<?php bloginfo('url'); ?>/wp-admin"><?php _e("Control Panel", "magazine-basic"); ?></a></li>
                <?php if ( $user_level >= 1 ) { ?>
                	<li class="dot"><a href="<?php bloginfo('url') ?>/wp-admin/post-new.php"><?php _e("Write", "magazine-basic"); ?></a></li>
				<?php } ?>
                <li class="dot"><a href="<?php bloginfo('url') ?>/wp-admin/profile.php"><?php _e("Profile", "magazine-basic"); ?></a></li>
				<li class="dot"><a href="<?php echo wp_logout_url() ?>&amp;redirect_to=<?php echo urlencode($_SERVER['REQUEST_URI']) ?>" title="<?php _e('Log Out', "magazine-basic") ?>"><?php _e('Log Out', "magazine-basic"); ?></a></li>
                </ul>
            <?php 
			} else {
				echo '<ul>';
				echo '<li><a href="'.get_bloginfo("url").'/wp-login.php">'.__('Log In', "magazine-basic").'</a></li>';
				if (get_option('users_can_register')) { ?>
					<li class="dot"><a href="<?php echo site_url('wp-login.php?action=register', 'login') ?>"><?php _e('Register', "magazine-basic") ?></a> </li>
                
            <?php 
				}
				echo "</ul>";
			} ?> 
    </div>
    <?php } ?>
    <?php if(theme_option('header_ad') == 'on') { ?>
		<?php if(theme_option('headerad_img')) { ?>
            <div id="headerad"<?php echo $adfloat; ?>>
                <a href="<?php echo theme_option('headerad_link'); ?>"><img src="<?php echo theme_option('headerad_img'); ?>" alt="" /></a>
            </div>
        <?php } else { ?>
            <div id="headerad"<?php echo $adfloat; ?>>
                <a href="http://themes.bavotasan.com"><img src="<?php echo THEME_URL; ?>/images/topbanner.png" alt="Themes by bavotasan.com" /></a>
            </div>
        <?php } ?>
    <?php } ?>
	<?php if (theme_option('logo_header')) { ?>
    	<a href="<?php bloginfo('url'); ?>/" class="headerimage"><img src="<?php echo theme_option('logo_header'); ?>" alt="<?php bloginfo('name'); ?>"<?php echo $float; ?> /></a>
    <?php } else { ?>
    <div id="title"<?php echo $float; ?>>
    	<a href="<?php bloginfo('url'); ?>/"><?php bloginfo('name'); ?></a>
        <div id="description">
            <?php bloginfo('description'); ?>
        </div>        
    </div>
    <?php } ?>
    <?php
    if(function_exists('wp_nav_menu')) {
		 wp_nav_menu( array( 'theme_location' => 'main', 'menu_class' => 'sf-menu','sort_column' => 'menu_order', 'container_class' => 'main-navigation', 'fallback_cb' => 'display_home' ) ); 
		 wp_nav_menu( array( 'theme_location' => 'sub', 'sort_column' => 'menu_order', 'container_class' => 'sub-navigation', 'fallback_cb' => 'display_none' ) ); 
	} else {
		echo '<div class="main-navigation"><ul class="sf-menu"><li><a href="'.get_bloginfo('url').'">Home</a></li>';
		wp_list_categories('title_li=');
		echo '</ul></div>';
	}
	?>

</div>
<!-- end header -->


<div id="mainwrapper">
<?php
	$loc = theme_option('sidebar_location');
	if($loc==1 || $loc==3 || $loc==5) {
		get_sidebar(); // calling the First Sidebar
	}
	if($loc==3) get_sidebar( "second" );
	?>
	<div id="leftcontent">
