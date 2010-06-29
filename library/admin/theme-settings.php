<?php

// Include files
	include(HYBRID_HOP. '/library/admin/theme-settings-admin.php');

// Add actions
	add_action('admin_menu', 'hop_add_pages');

/**
* Gets all theme admin menu pages
*
* @since 0.1
*/
function hop_add_pages() {
	add_theme_page(__('FYA Settings','hybrid'), __('FYA Settings','hybrid'), 10, 'hybrid-hop.php', hop_theme_page);
}

?>
