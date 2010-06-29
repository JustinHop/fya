<?php

/**
* Returns an array of the theme settings
* Add all options to a single array
* This makes one entry in the database
*
* @since 0.1
*/
function hop_theme_settings() {
	$settings_arr = array(
		'newsletter' => false,
		'partner' => false,
		'multicontent' => false,
	);

	return apply_filters('hop_settings_args', $settings_arr);
}

/**
* Handles the theme settings
*
* @since 0.1
*/
function hop_theme_page() {

	/*
	* Variables to be used throughout the settings page
	*/
	$theme_name = __('Hybrid Hoppextend','hop');
	$settings_page_title = __('Foster Youth Alliance Settings','hop');
	$hidden_field_name = 'hop_submit_hidden';

	/*
	* Get the theme settings and add them to the database
	*/
	$settings_arr = hop_theme_settings();
	add_option('hop_theme_settings', $settings_arr);

	/*
	* Set form data IDs the same as settings keys
	*/
	$settings_keys = array_keys($settings_arr);
	foreach($settings_keys as $key) :
		$data[$key] = $key;
	endforeach;

	/*
	* Get existing options from the database
	*/
	$settings = get_option('hop_theme_settings');

	foreach($settings_arr as $key => $value) :
		$val[$key] = $settings[$key];
	endforeach;

	/*
	* If the form has been set
	* Loop through the values
	* Set the option in the database
	*/
	if($_POST[$hidden_field_name] == 'Y') :

		foreach($settings_arr as $key => $value) :
			$settings[$key] = $val[$key] = $_POST[$data[$key]];
		endforeach;

		update_option('hop_theme_settings', $settings);

		/*
		* Open main div for the theme settings
		*/
		echo '<div class="wrap">';
		echo '<h2>' . $settings_page_title . '</h2>';
		echo '<div class="updated" style="margin:15px 0;">';
		echo '<p><strong>' . __('Settings saved.','news') . '</strong></p>';
		echo '</div>';

	else :
		echo '<div class="wrap">';
		echo '<h2>' . $settings_page_title . '</h2>';

	endif;

	/*
	* Load the theme settings form
	*/
	include(HYBRID_HOP . '/library/admin/theme-settings-xhtml.php');
}

?>
