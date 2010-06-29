<?php
	$hop_categories = get_categories();
	$hop_categories[] = false;
?>

<div class="postbox open">

<h3><?php _e('Foster Youth Alliance Settings','hybrid'); ?></h3>

<div class="inside">

	<table class="form-table">

	<tr>
		<th>
			<label for="<?php echo $data['newsletter']; ?>"><?php _e('Enable Newsletter Post Type:','news'); ?></label>
		</th>
		<td>
			Yes<input type="radio" id="<?php echo $data['newsletter']; ?>" name="<?php echo $data['newsletter']; ?>" value="Yes" <?php if($val['newsletter'] == "Yes") echo " checked"; ?>>
			No<input type="radio" id="<?php echo $data['newsletter']; ?>" name="<?php echo $data['newsletter']; ?>" value="No" <?php if($val['newsletter'] == "No") echo " checked"; ?>>

		</td>
	</tr>
	<tr>
		<th>
			<label for="<?php echo $data['partner']; ?>"><?php _e('Enable Partner Post Type:','news'); ?></label>
		</th>
		<td>
			Yes<input type="radio" id="<?php echo $data['partner']; ?>" name="<?php echo $data['partner']; ?>" value="Yes" <?php if($val['partner'] == "Yes") echo " checked"; ?>>
			No<input type="radio" id="<?php echo $data['partner']; ?>" name="<?php echo $data['partner']; ?>" value="No" <?php if($val['partner'] == "No") echo " checked"; ?>>

		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['multicontent']; ?>"><?php _e('Enable Multi Content for Partners:','news'); ?></label>
		</th>
		<td>
			Yes<input type="radio" id="<?php echo $data['multicontent']; ?>" name="<?php echo $data['multicontent']; ?>" value="Yes" <?php if($val['multicontent'] == "Yes") echo " checked"; ?>>
			No<input type="radio" id="<?php echo $data['multicontent']; ?>" name="<?php echo $data['multicontent']; ?>" value="No" <?php if($val['multicontent'] == "No") echo " checked"; ?>>

		</td>
	</tr>

	</table>

</div>
</div>
