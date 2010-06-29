<?php
function partner_posttype() {
    $args = array('label' => __('Partners'), 'description' => 'FYA Partners', 'hierarchial' => true, 'capability_type' => 'page', 'public' => true, 'show_ui' => true, 'register_meta_box_cb' => partner_metabox, 'supports' => array('title',
    /*'editor',
    'custom-fields',*/
    'thumbnail', 'excerpt', 'revisions',));
    register_post_type('partner', $args);
}
function partner_head_css() { ?>
<style type="text/css">
#hybrid-partner-meta-box { display: none; }
</style>
<?php
}
function partner_inner_html() {
    global $post;
?>
<!-- partner_inner_html() -->
<input type="hidden" name="partner_nonce" id="partner_nonce" value="<?php wp_create_nonce(get_bloginfo('theme_directory')) ?>" />

    <table>
    <tr><td colspan=2>Organization:</td></tr>
    <tr> <td>
            <label for="partner_address">Address</label>
        </td><td>
        <textarea name="partner_address" rows=4><?php
    echo get_post_meta($post->ID, 'partner_address', true);
?></textarea>
    </td></tr><tr><td>
            <label for="partner_phonenumber">Phone Number</label>
        </td><td>
            <input type="text" name="partner_phonenumber" value="<?php
    echo get_post_meta($post->ID, 'partner_phonenumber', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_website">Website</label>
        </td><td>
            <input type="text" name="partner_website" value="<?php
    echo get_post_meta($post->ID, 'partner_website', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_phonenumber">Fax Number</label>
        </td><td>
            <input type="text" name="partner_faxnumber" value="<?php
    echo get_post_meta($post->ID, 'partner_faxnumber', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_email">Email</label>
        </td><td>
            <input type="text" name="partner_email" value="<?php
    echo get_post_meta($post->ID, 'partner_email', true);
?>" />
    </td></tr>    <tr><td colspan=2><hr /></td></tr>
    <tr><td colspan=2>Primary Contact:</td></tr>
    <tr><td>
            <label for="partner_contact">Name</label>
        </td><td>
            <input type="text" name="partner_contact" value="<?php
    echo get_post_meta($post->ID, 'partner_contact', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact_title">Title</label>
        </td><td>
            <input type="text" name="partner_contact_title" value="<?php
    echo get_post_meta($post->ID, 'partner_contact_title', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact_phone">Phone Number</label>
        </td><td>
            <input type="text" name="partner_contact_phone" value="<?php
    echo get_post_meta($post->ID, 'partner_contact_phone', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact_email">Email</label>
        </td><td>
            <input type="text" name="partner_contact_email" value="<?php
    echo get_post_meta($post->ID, 'partner_contact_email', true);
?>" />
    </td></tr>
    <tr><td colspan=2><hr /></td></tr>
    <tr><td colspan=2>Secondary Contact:</td></tr>
    <tr><td>
            <label for="partner_contact2">Name</label>
        </td><td>
            <input type="text" name="partner_contact2" value="<?php
    echo get_post_meta($post->ID, 'partner_contact2', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact2_title">Title</label>
        </td><td>
            <input type="text" name="partner_contact2_title" value="<?php
    echo get_post_meta($post->ID, 'partner_contact2_title', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact2_phone">Phone Number</label>
        </td><td>
            <input type="text" name="partner_contact2_phone" value="<?php
    echo get_post_meta($post->ID, 'partner_contact2_phone', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact2_email">Email</label>
        </td><td>
            <input type="text" name="partner_contact2_email" value="<?php
    echo get_post_meta($post->ID, 'partner_contact2_email', true);
?>" />
    </td></tr>
    <tr><td colspan=2><hr /></td></tr>
    <tr><td colspan=2>Tertiary Contact:</td></tr>
    <tr><td>
            <label for="partner_contact3">Name</label>
        </td><td>
            <input type="text" name="partner_contact3" value="<?php
    echo get_post_meta($post->ID, 'partner_contact3', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact3_title">Title</label>
        </td><td>
            <input type="text" name="partner_contact3_title" value="<?php
    echo get_post_meta($post->ID, 'partner_contact3_title', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact3_phone">Phone Number</label>
        </td><td>
            <input type="text" name="partner_contact3_phone" value="<?php
    echo get_post_meta($post->ID, 'partner_contact3_phone', true);
?>" />
    </td></tr><tr><td>
            <label for="partner_contact3_email">Email</label>
        </td><td>
            <input type="text" name="partner_contact3_email" value="<?php
    echo get_post_meta($post->ID, 'partner_contact3_email', true);
?>" />
    </td></tr>
    </table>

<?php
}
function partner_featured_inner_html() {
    global $post;
?>
<!-- partner_featured_inner_html() -->
<input type="hidden" name="partner_featured_nonce" id="partner_featured_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
<table>
    <tr><td>
            <label for="partner_featured">Featured</label>
        </td><td>
        <input type="checkbox" name="partner_featured" <?php
    if (preg_match("/on/", get_post_meta($post->ID, 'partner_featured', true))) {
        echo "checked=true ";
    } ?>  />
    </td></tr> </table>
<?php
}
function partner_save_postdata($post_id) {
    if (!wp_verify_nonce($_POST['partner_featured_nonce'], __FILE__)) {
        //echo 'DID NOT VERIFY';
        return $post_id;
        //return new WP_Error('nonce', __("Did not verify Nonce"));
        
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    $partner_meta = array('partner_featured', 'partner_address', 'partner_website', 'partner_phonenumber', 'partner_faxnumber', 'partner_email', 'partner_contact', 'partner_contact_title', 'partner_contact_phone', 'partner_contact_email', 'partner_contact2', 'partner_contact2_title', 'partner_contact2_phone', 'partner_contact2_email', 'partner_contact3', 'partner_contact3_title', 'partner_contact3_phone', 'partner_contact3_email',);
    foreach($partner_meta as $field) {
        if ($_REQUEST[$field]) {
            update_post_meta($post_id, $field, $_REQUEST[$field]);
        } else {
            delete_post_meta($post_id, $field);
        }
    }
}
function partner_metabox() {
    add_meta_box('partner_info', __('Partners Info'), 'partner_inner_html', 'partner', 'side');
    add_meta_box('partner_featured_info', __('Featured Partners Info'), 'partner_featured_inner_html', 'partner', 'side');
    /*
    add_meta_box( 'partner_info', __('Partners Info'),
    'partner_inner_html', 'partner', 'side' );
    add_meta_box( 'partner_info', __('Partners Info'),
    'partner_inner_html', 'side', 'normal' );
    //add_meta_box( 'partner_info', __('Partners Info'),
    //   'partner_inner_html',  );*/
};
function partner_post_type_add_template() {
    $post_type = get_query_var('post_type');
    if (!empty($post_type)) {
        if (is_single()) {
            locate_template(array("{$post_type}-single.php", "{$post_type}.php", "index.php"), true);
        } else {
            locate_template(array("{$post_type}.php", "index.php"), true);
        }
        exit;
    }
}
?>
