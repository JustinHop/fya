<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
<div>
<input type="text" class="search_input" value="<?php _e('Search &amp; Hit Enter', "magazine-basic"); ?>" name="s" id="s" onfocus="if (this.value == '<?php _e('Search &amp; Hit Enter', "magazine-basic"); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search &amp; Hit Enter', "magazine-basic"); ?>';}" />
<input type="hidden" id="searchsubmit" />
</div>
</form>
