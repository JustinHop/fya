<div id="poststuff" class="dlm">

	<form name="form0" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="border:none;background:transparent;">

	<?php include(HYBRID_HOP . '/library/admin/general.php'); ?>

	<p class="submit">
		<input type="submit" name="Submit"  class="button-primary" value="<?php _e('Save Changes', 'hybrid' ) ?>" />
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
	</p>

	</form>

</div>

</div>
