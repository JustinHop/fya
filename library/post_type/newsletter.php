<?php
/**
 * This will hold newsletter custom post type logic
 */
// Load all the nav menu interface functions
#require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );
function newsletter_posttype() {
    $args = array('label' => __('Newsletters'), 'description' => 'FYA Newsletters', 'hierarchial' => false, 'capability_type' => 'post', 'public' => true, 'show_ui' => true, 'register_meta_box_cb' => newsletter_metabox, 'supports' => array('title',
    /*'editor',
    'custom-fields',
    /*'thumbnail',
    'excerpt',
    'revisions',*/
    ));
    register_post_type('newsletter', $args);
    wp_register_style('jquery-ui', get_bloginfo('template_directory') . '/library/css/start/jquery-ui-1.7.2.custom.css');
    wp_enqueue_style('jquery-ui');
}
function newsletter_date_inner_html() {
    global $post;
?>
<!-- fya_partner_inner_html() -->
<input type="hidden" name="newsletter_nonce" id="newsletter_nonce" value="<?php wp_create_nonce(get_bloginfo('theme_directory')) ?>" />
<p>Date: <input type="text" id="datepicker" size="30"/></p>
<a class="button">Do Something</a>

<?php
}
function newsletter_menu_inner_html() {
    global $post;
    global $newsmenu;
    $newsmenu->admin();
}
function newsletter_debug_contents_inner_html() { ?>
<?php global $post; ?>
<table><tr>
<td>post_ID</td><td class="post_ID"><?php echo get_the_ID(); ?></td></tr>
<tr><td>menu_id</td><td class="menu_id"><?php echo get_post_meta($post->ID, 'menu_id', true); ?></td>
<tr><td>GET menu_id</td><td class="menu_id"><?php echo $_REQUEST['menu_id']; ?></td>
</tr>
</table>
<?php
}
function newsletter_metabox() {
    #add_meta_box( 'add-custom-links', __('Add Custom Links'), 'wp_nav_menu_item_link_metabox', 'newsletter', 'side', 'default' );
    #	  wp_nav_menu_post_type_metaboxes();
    #	  wp_nav_menu_taxonomy_metaboxes();
    add_meta_box('newsletter_menu', __('Newsletter Contents'), 'newsletter_menu_inner_html', 'newsletter', 'normal', 'core');
    add_meta_box('newsletter_contents', __('Debug Newsletter Contents'), 'newsletter_debug_contents_inner_html', 'newsletter', 'side', 'default');
    /*add_meta_box( 'newsletter_add_posts', __('Add Posts to Newsletter'),
    'newsletter_add_posts_inner_html', 'newsletter', 'side' );
    add_meta_box( 'newsletter_news', __('Newsletter Contents'),
    'newsletter_news_inner_html', 'newsletter', 'normal' );*/
};
function newsletter_admin_footer() {
    if (get_post_type() == 'newsletter') {
        echo '<!-- THIS IS newsletter_admin_footer() -->';
?>

<!-- Mine -->
<script type="text/javascript">
jQuery(document).ready(function($) {
   $('#datepicker').datepicker();
   $('#title').datepicker({dateFormat: 'MM d, yy'});

    	
    	$('.post_ID').text($('input[name=post_ID]').attr('value'));
});
</script>
<?php
    }
}
function newsletter_admin_header_new() {
    global $post;
    global $newsmenu;
    if (get_post_type() == 'newsletter') {
        echo '<!-- THIS IS newsletter_admin_header_new() -->';
        echo '<!-- ' . $post->ID . ' -->';
        update_post_meta($post->ID, 'menu_id', $newsmenu->quick_new_menu());
    }
}
function newsletter_save_postdata($post_id) {
    global $newsmenu;
    if (!wp_verify_nonce($_POST['fya_partner_featured_nonce'], get_bloginfo('theme_directory'))) {
        //echo '<!-- ERROR ->';
        //return $post_id;
    }
    //if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    if ($_REQUEST['menu_id']) {
        update_post_meta($post_id, 'menu_id', true);
        //$newsmenu->quick_edit_menu($_REQUEST['menu_id'], $_REQUEST['title']);
    }
}
function newsletter_admin_head() {
    wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"), false);
    wp_register_script('jquery-ui', ("http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"), array('jquery'));
    wp_register_script('newsletter-admin', get_bloginfo('template_directory') . '/library/js/newsletter-admin.js', array('jquery-ui'));
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui');
    // Metaboxes
    wp_enqueue_script('common');
    wp_enqueue_script('wp-lists');
    wp_enqueue_script('postbox');
    // Nav Menu CSS
    #wp_admin_css( 'nav-menu' );
    wp_enqueue_script('jquery-autocomplete');
    wp_enqueue_script('newsletter-admin');
    // Thickbox
    add_thickbox();
}

?>
