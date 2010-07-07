<?php
class NewsMenu extends MenusPlus {
    // Views
    function admin() {
        global $post;
        //$menu_id_from_get = $_GET['menu_id'];
        $menu_id_from_get = get_post_meta($post->ID, 'menu_id', true);
        if (!empty($_REQUEST['menu_id'])) {
            $menu_id_from_get = $_REQUEST['menu_id'];
        }
        $menu_id = $this->get_menu_id($menu_id_from_get);
        $parent = $this->menu_has_parent($menu_id);
        $this->js($menu_id, $parent);
        $this->style();
        if (function_exists('wp_nonce_field')) wp_nonce_field('menus_plus_nonce', 'menus_plus_nonce');
?> 
		<input id="menu_id" type="hidden" name="menu_id" value="<?php echo $menu_id; ?>" />

		<div class="wrap mp_margin_bottom">
		</div>
		<div class="wrap mp_margin_bottom">
			<div class="mp_buttons_left">
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=section&width=350&height=350" title="<?php _e("Add a Section Title"); ?>"><?php _e("Section Title"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=cat&width=350&height=350" title="<?php _e("Add a Category"); ?>"><?php _e("Category"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=page&width=350&height=350" title="<?php _e("Add a Page"); ?>"><?php _e("Page"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=post&width=350&height=350" title="<?php _e("Add a Post"); ?>"><?php _e("Post"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=url&width=350&height=350" title="<?php _e("Add a URL"); ?>"><?php _e("URL"); ?></a>
				<?php if (!$parent): ?>
				<!--<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=hybrid&width=350&height=350" title="<?php _e("Add a Hybrid Menu"); ?>"><?php _e("Hybrid Menu"); ?></a>-->
				<?php
        endif; ?>
				<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
			</div>
			<div class="mp_buttons_right">
				
				<span class="mp_menu_title"></span> 

				<select class="mp_switch_menu">
				</select>
				
				<?php if (!$parent): ?>
				
				<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_edit_menu_dialog&menu_id=<?php echo $menu_id; ?>&width=350&height=200" title="<?php _e("Edit Menu"); ?>">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/edit.png" />
				</a>
				<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_remove_menu_dialog&menu_id=<?php echo $menu_id; ?>&width=350&height=100" title="<?php _e("Delete Menu"); ?>">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/remove.png" />
				</a>
				
				<?php
        endif; ?>
				
				<?php if ($parent): ?>
					
					<a class="thickbox rawk" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_edit_hybrid_dialog&menu_id=<?php echo $menu_id; ?>&width=350&height=350" title="<?php _e("Edit Hybrid Menu"); ?>">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/edit.png" />
					</a>
					
				<?php
        endif; ?>
				
				<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_new_menu_dialog&width=350&height=100" title="<?php _e("New Menu"); ?>">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/add.png" />
				</a>
				
			</div>
			<div class="clear_list_floats"></div>
		</div>
		<div class="wrap postbox rawka" id="menusplus_list">
			<ul <?php if ($parent) {
            echo 'class="parent_menu_box"';
        } ?> ></ul>
		</div>
		
		<?php if (!$parent): ?>
			<div class="wrap">
				<table class="hidden" cellspacing="6">
					<tr>
						<td>
							<div align="right"><?php _e('Template Tag') ?></div>
						</td>
						<td>
							<input class="widefat" value="&lt;?php menusplus(<?php echo $menu_id; ?>); ?&gt;" style="width:200px;" />
						</td>
					</tr>
					<tr>
						<td>
							<div align="right"><?php _e('Shortcode') ?></div>
						</td>
						<td>
							<input class="widefat" value='[menusplus menu="<?php echo $menu_id; ?>"]' style="width:200px;" />
						</td>
					</tr>
					<tr>
						<td>
							<div align="right"><a href="http://www.keighl.com/plugins/menus-plus/"><?php _e("Docs"); ?></a></div>
						</td>
						<td></td>
					</tr>
				</table>
			</div>
		<?php
        endif; ?>
					
	 	<?php
    }
    function quickadd() {
        global $wpdb;
        $items_table = $wpdb->prefix . "menusplus";
        $menus_table = $wpdb->prefix . "menusplus_menus";
        $wpdb->show_errors();
        $data = $this->prep_item_post($_POST);
        $data['list_order'] = $this->highest_order($data['menu_id']) + 1;
        unset($data['id']); // Unsets the id key used only by Edit
        unset($data['nonce']);
        if ($data['type'] == "hybrid"):
            $menu_data_array = array('menu_title' => $data['label'], 'parent_id' => $data['menu_id']);
            $wpdb->insert($menus_table, $menu_data_array);
            $last_result = $wpdb->insert_id;
            $data['wp_id'] = $last_result; // Insert the parent menu id as the wpid.
            echo $last_result; // Redirect for new hybrid list
            
        endif;
        $wpdb->insert($items_table, $data);
        return $this->highest_order();
    }
    function quick_preload_menu($arr){
        global $post;
        global $wpdb;
        $wpdb->show_errors();
        $post_id = $_POST['id'];
        $items_table = $wpdb->prefix . "menusplus";
        $data = $this->prep_item_post( array( 'wp_id' => $arr['wp_id'],
                                                'label' => 'Featured',
                                                'post_type' => 'section',
                                                'menu_id' => $arr['menu_id'] ) );
        $data['list_order'] = $this->highest_order($data['menu_id']) + 1;
        $wpdb->insert($items_table, $data);
        $data['inserted'] = $wpdb->insert_id;
        var_dump( $data );

        return $menu_id;
    }

    function quick_new_menu() {
        global $post;
        $title = $_POST['title'];
        $title = 'new newsletter nav';
        if (empty($title)):
            echo "empty";
            exit();
        endif;
        $title = stripslashes($title);
        global $wpdb;
        $menus_table = $wpdb->prefix . "menusplus_menus";
        $wpdb->show_errors();
        $data_array = array('menu_title' => $title,);
        $wpdb->insert($menus_table, $data_array);
        return $wpdb->insert_id;
    }
    function quick_edit_menu() {
        global $post;
        $title = $_REQUEST['title'];
        $id = $_REQUEST['menu_id'];
        if (empty($id)) {
            $id = get_post_meta($post->ID, 'menu_id');
        }
        if (empty($title)) {
            echo 1;
            exit();
        }
        $title = stripslashes($title);
        global $wpdb;
        $menus_table = $wpdb->prefix . "menusplus_menus";
        $wpdb->show_errors();
        $data_array = array('menu_title' => $title,);
        $where = array('id' => $id);
        $wpdb->update($menus_table, $data_array, $where);
        exit();
    }
	function new_menu() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		$title = $_POST['title'];
		$title = 'Newsletter';
		if (empty($title)) : echo "empty"; exit(); endif;
		
		$title = stripslashes($title);
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$data_array = array(
			'menu_title' => $title,
		);
		
		$wpdb->insert($menus_table, $data_array );
		echo $last_result = $wpdb->insert_id;
		exit();
		
	}
	
    function add_admin() {
        return true;
    }
    function add_dialog() {
        $type = $_GET['type'];
        if (!$type) {
            exit();
        } ?>
		<div class="mp_add">
			<table cellspacing="16" cellpadding="0">
				<?php if ($type == "home"): ?>
					<tr>
						<td><div align="right"><?php _e("URL"); ?></div></td>
						<td><span class="mp_home_url"><? bloginfo('siteurl'); ?></span></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Label"); ?></div></td>
						<td><input class="add_label widefat" value="<?php _e('Home'); ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class"); ?></div></td>
						<td><input class="add_class widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="add_title widefat" value="" /></td>
					</tr>
				<?php
        elseif ($type == "cat"): ?>
					<tr>
						<td>
							<div align="right">
								<?php echo _e("Category"); ?>
							</div>
						</td>
						<td>
							<?php wp_dropdown_categories(array('hide_empty' => 0, 'hide_if_empty' => false, 'name' => 'add_wpid', 'hierarchical' => true)); ?>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Children"); ?></div></td>
						<td>
	                        <label>
	                        	<input type="radio" name="add_children" value="true" /> <?php _e("Yes"); ?>
	                        </label>
							<label>
	                        	<input type="radio" name="add_children" value="false" checked="checked" /> <?php _e("No"); ?>
	                        </label>
	                    </td>
				    </tr>
					<tr id="children_order_box">
						<td><div align="right"><?php _e("Child Order"); ?></div></td>
						<td>
							<select class="add_children_order">
								<option value="name"><?php _e("Name"); ?></option>
								<option value="ID"><?php _e("ID"); ?></option>
								<option value="count"><?php _e("Count"); ?></option>
								<option value="slug"><?php _e("Slug"); ?></option>
								<option value="term_group"><?php _e("Term Group"); ?></option>
								<?php
            if (function_exists('mycategoryorder')):
                echo '<option value="order">' . __('My Category Order', "menus-plus") . '</option>';
            endif; ?>
							</select>
							<select class="add_children_order_dir">
								<option value="ASC">ASC</option>
								<option value="DESC">DESC</option>
							</select>
	                    </td>
				    </tr>
					<tr>
						<td><div align="right"><?php _e("Depth"); ?></div></td>
						<td>
							<input class="add_depth widefat" value="0" style="width:2em;" />
							<a class="depth_help">
								<img src="<?php echo plugin_dir_url(__FILE__); ?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php
        elseif ($type == "page"): ?>
					<tr>
						<td>
							<div align="right">
								<?php echo _e("Page"); ?>
							</div>
						</td>
						<td>
							<?php wp_dropdown_pages("name=add_wpid"); ?>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Children"); ?></div></td>
						<td>
	                        <label>
	                        	<input type="radio" name="add_children" value="true" /> <?php _e("Yes"); ?>
	                        </label>
							<label>
	                        	<input type="radio" name="add_children" value="false" checked="checked" /> <?php _e("No"); ?>
	                        </label>
	                    </td>
				    </tr>
					<tr id="children_order_box">
						<td><div align="right"><?php _e("Child Order"); ?></div></td>
						<td>
							<select class="add_children_order">
								<option value="post_title"><?php _e("Title"); ?></option>
								<option value="post_date"><?php _e("Date"); ?></option>
								<option value="post_modified"><?php _e("Date Modified"); ?></option>
								<option value="ID"><?php _e("ID"); ?></option>
								<option value="post_author"><?php _e("Author"); ?></option>
								<option value="post_name"><?php _e("Slug"); ?></option>
								<?php
            if (function_exists('mypageorder')):
                echo '<option value="menu_order">' . __('My Page Order', "menus-plus") . '</option>';
            endif;
?>
							</select>
							<select class="add_children_order_dir">
								<option value="ASC">ASC</option>
								<option value="DESC">DESC</option>
							</select>
	                    </td>
				    </tr>
					<tr>
						<td><div align="right"><?php _e("Depth"); ?></div></td>
						<td>
							<input class="add_depth widefat" value="0" style="width:2em;" />
							<a class="depth_help">
								<img src="<?php echo plugin_dir_url(__FILE__); ?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php
        elseif ($type == "post"): ?>
					<tr>
						<td>
							<div align="right">
								<?php echo _e("Post"); ?>
							</div>
						</td>
						<td>
							<select name="add_wpid">
								<?php $this->mp_dropdown_posts(); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class"); ?></div></td>
						<td>
							<select class="add_class">
								<option value="full" selected="selected"><?php _e('Full Post'); ?></option>
								<option value="excerpt"><?php _e('Post Excerpt'); ?></option>
								<option value="title"><?php _e('Post Title Only'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="add_title widefat" value="" /></td>
					</tr>
				<?php
        elseif ($type == "url"): ?>
					<tr>
						<td><div align="right"><?php _e("URL"); ?></div></td>
						<td><input class="add_url widefat" value="http://" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Label"); ?></div></td>
						<td><input class="add_label widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Target"); ?></div></td>
						<td>
							<select class="add_target">
								<option value="_parent" selected="selected"><?php _e('Same window'); ?></option>
								<option value="_blank"><?php _e('New window'); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class"); ?></div></td>
						<td><input class="add_class widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="add_title widefat" value="" /></td>
					</tr>
				<?php /* * * stuff */ ?>
				<?php
        elseif ($type == "section"): ?>
					<tr>
						<td><div align="right"><?php _e("Section Label"); ?></div></td>
						<td><input class="add_label widefat" value="Label" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Section Column"); ?></div></td>
						<td >
							<select class="edit_target widefat">
								<option value="main"  selected="selected" ><?php _e('Main Body Full', "menus-plus"); ?></option>
								<option value="mainshort" ><?php _e('Main Body Short', "menus-plus"); ?></option>
								<option value="side" ><?php _e('Side Column', "menus-plus"); ?></option>
							</select>
						</td>
					</tr>
				<?php
        elseif ($type == "hybrid"): ?>
					<tr>
						<td><div align="right"><?php _e("Label"); ?></div></td>
						<td><input class="add_label widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("URL"); ?></div></td>
						<td><input class="add_url widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class"); ?></div></td>
						<td><input class="add_class widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="add_title widefat" value="" /></td>
					</tr>
				<?php
        endif; ?>
				<tr>
					<td>
						<div align="right">
							<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
						</div>
					</td>
					<td>
						<a class="button" id="add_submit" rel="<?php echo $type; ?>"><?php _e("Add"); ?></a>
						<a class="button" id="mp_cancel"><?php _e("Cancel"); ?></a>
					</td>
				</tr>
			</table>
		</div>
		
		<?php
        exit();
    }
    function edit_dialog() {
        $id = $_GET['id'];
        if (!$id) {
            exit();
        }
        // Assemble our knowledge of this list item
        global $wpdb;
        $items_table = $wpdb->prefix . "menusplus";
        $wpdb->show_errors();
        $metas = $wpdb->get_results("SELECT * FROM $items_table WHERE id = $id", ARRAY_A);
        if (count($metas) > 0):
            foreach($metas as $meta):
                $type = $meta['type'];
                $wp_id = $meta['wp_id'];
                $class = $meta['class'];
                $label = $meta['label'];
                $url = $meta['url'];
                $children = $meta['children'];
                $children_order = $meta['children_order'];
                $children_order_dir = $meta['children_order_dir'];
                $list_order = $meta['list_order'];
                $menu_id = $meta['menu_id'];
                $target = $meta['target'];
                $depth = $meta['depth'];
                $title = $meta['title'];
            endforeach;
        endif; ?>
		<div class="mp_edit">
			<input  type="hidden" value="<?php echo $type; ?>" class="edit_type" />
			<table cellspacing="16" cellpadding="0">
				<?php if ($type == "home"): ?>
					<tr>
						<td><div align="right"><?php _e("Section Name", "menus-plus"); ?></div></td>
						<td><input class="edit_label widefat" value="<?php echo $label; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class", "menus-plus"); ?></div></td>
						<td><input class="edit_class widefat" value="<?php echo $class; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="edit_title widefat" value="<?php echo $title; ?>" /></td>
					</tr>
					<?php
        elseif ($type == "section"): ?>
					<tr>
						<td><div align="right"><?php _e("Section Label"); ?></div></td>
						<td><input class="edit_label widefat" value="<?php echo $label; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Section Column"); ?></div></td>
						<td class="widefat">
							<select class="edit_target">
								<option value="main"  <?php 
								if ($target == "main"): ?> selected="selected" <?php endif; ?> ><?php _e('Main Body Full', "menus-plus"); ?></option>
								<option value="mainshort" <?php 
								if ($target == "mainshort"): ?> selected="selected" <?php endif; ?>  ><?php _e('Main Body Short', "menus-plus"); ?></option>
								<option value="side" <?php 
								if ($target == "side"): ?> selected="selected" <?php endif; ?> ><?php _e('Side Column', "menus-plus"); ?></option>
							</select>
						</td>
					</tr>
				<?php
        elseif ($type == "cat"): ?>
					<tr>
						<td>
							<div align="right">
								<?php _e("Category", "menus-plus"); ?>
							</div>
						</td>
						<td>
							<?php wp_dropdown_categories(array('hide_empty' => 0, 'hide_if_empty' => false, 'name' => 'edit_wpid', 'hierarchical' => true, 'selected' => $wp_id)); ?>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Children", "menus-plus"); ?></div></td>
						<td>
	                        <label>
	                        	<input type="radio" name="edit_children" value="true" <?php if ($children == "true"): ?> checked="checked" <?php
            endif; ?> /> <?php _e("Yes", "menus-plus"); ?>
	                        </label>
							<label>
	                        	<input type="radio" name="edit_children" value="false" <?php if ($children == "false"): ?> checked="checked" <?php
            endif; ?> /> <?php _e("No", "menus-plus"); ?>
	                        </label>
	                    </td>
				    </tr>
					<tr id="children_order_box">
						<td><div align="right"><?php _e("Child Order", "menus-plus"); ?></div></td>
						<td>
							<select class="edit_children_order">
								<option value="name" <?php if ($children_order == "name"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Name", "menus-plus"); ?></option>
								<option value="ID" <?php if ($children_order == "ID"): ?> selected="selected" <?php
            endif; ?> ><?php _e("ID", "menus-plus"); ?></option>
								<option value="count" <?php if ($children_order == "count"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Count", "menus-plus"); ?></option>
								<option value="slug" <?php if ($children_order == "slug"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Slug", "menus-plus"); ?></option>
								<option value="term_group" <?php if ($children_order == "term_group"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Term Group", "menus-plus"); ?></option>
								<?php if (function_exists('mycategoryorder')): ?>
									<option value="order" <?php if ($children_order == "order"): ?> selected="selected" <?php
                endif; ?> ><?php _e("My Category Order", "menus-plus"); ?></option>	
								<?php
            endif; ?>
							</select>
							<select class="edit_children_order_dir">
								<option value="ASC" <?php if ($children_order_dir == "ASC"): ?> selected="selected" <?php
            endif; ?> >ASC</option>
								<option value="DESC" <?php if ($children_order_dir == "DESC"): ?> selected="selected" <?php
            endif; ?> >DESC</option>
							</select>
	                    </td>
				    </tr>
					<tr>
						<td><div align="right"><?php _e("Depth", "menus-plus"); ?></div></td>
						<td>
							<input class="edit_depth widefat" value="<?php echo $depth; ?>" style="width:2em;" />
							<a class="depth_help">
								<img src="<?php echo plugin_dir_url(__FILE__); ?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php
        elseif ($type == "page"): ?>
					<tr>
						<td>
							<div align="right">
								<?php _e("Page", "menus-plus"); ?>
							</div>
						</td>
						<td>
							<?php wp_dropdown_pages("name=edit_wpid&selected=$wp_id"); ?>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Children", "menus-plus"); ?></div></td>
						<td>
	                        <label>
	                        	<input type="radio" name="edit_children" value="true" <?php if ($children == "true"): ?> checked="checked" <?php
            endif; ?> /> <?php _e("Yes", "menus-plus"); ?>
	                        </label>
							<label>
	                        	<input type="radio" name="edit_children" value="false" <?php if ($children == "false"): ?> checked="checked" <?php
            endif; ?> /> <?php _e("No", "menus-plus"); ?>
	                        </label>
	                    </td>
				    </tr>
					<tr id="children_order_box">
						<td><div align="right"><?php _e("Child Order", "menus-plus"); ?></div></td>
						<td>
							<select class="edit_children_order">
								<option value="post_title" <?php if ($children_order == "post_title"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Title", "menus-plus"); ?></option>
								<option value="post_date" <?php if ($children_order == "post_date"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Date", "menus-plus"); ?></option>
								<option value="post_modified" <?php if ($children_order == "post_modified"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Date Modified", "menus-plus"); ?></option>
								<option value="ID" <?php if ($children_order == "ID"): ?> selected="selected" <?php
            endif; ?> ><?php _e("ID", "menus-plus"); ?></option>
								<option value="post_author" <?php if ($children_order == "post_author"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Author", "menus-plus"); ?></option>
								<option value="post_name" <?php if ($children_order == "post_name"): ?> selected="selected" <?php
            endif; ?> ><?php _e("Slug", "menus-plus"); ?></option>
								<?php if (function_exists('mypageorder')): ?>
									<option value="menu_order" <?php if ($children_order == "menu_order"): ?> selected="selected" <?php
                endif; ?> ><?php _e("My Page Order", "menus-plus"); ?></option>	
								<?php
            endif; ?>
							</select>
							<select class="edit_children_order_dir">
								<option value="ASC" <?php if ($children_order_dir == "ASC"): ?> selected="selected" <?php
            endif; ?> >ASC</option>
								<option value="DESC" <?php if ($children_order_dir == "DESC"): ?> selected="selected" <?php
            endif; ?> >DESC</option>
							</select>
	                    </td>
				    </tr>
					<tr>
						<td><div align="right"><?php _e("Depth", "menus-plus"); ?></div></td>
						<td>
							<input class="edit_depth widefat" value="<?php echo $depth; ?>" style="width:2em;" />
							<a class="depth_help">
								<img src="<?php echo plugin_dir_url(__FILE__); ?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php
        elseif ($type == "post"): ?>
					<tr>
						<td>
							<div align="right">
								<?php echo _e("Post", "menus-plus"); ?>
							</div>
						</td>
						<td>
							<select name="edit_wpid">
								<?php $this->mp_dropdown_posts($wp_id); ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class", "menus-plus"); ?></div></td>
						<td>
							<select class="edit_class">
								<option value="full" <?php if ($target == "full"): ?> selected="selected" <?php
            endif; ?> ><?php _e('Full Post', "menus-plus"); ?></option>
								<option value="excerpt" <?php if ($target == "excerpt"): ?> selected="selected" <?php
            endif; ?> ><?php _e('Post Excerpt', "menus-plus"); ?></option>
								<option value="title" <?php if ($target == "title"): ?> selected="selected" <?php
            endif; ?> ><?php _e('Post Title Only', "menus-plus"); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="edit_title widefat" value="<?php echo $title; ?>" /></td>
					</tr>
				<?php
        elseif ($type == "url"): ?>
					<tr>
						<td><div align="right"><?php _e("URL", "menus-plus"); ?></div></td>
						<td><input class="edit_url widefat" value="<?php echo $url; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Label", "menus-plus"); ?></div></td>
						<td><input class="edit_label widefat" value="<?php echo $label; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Target", "menus-plus"); ?></div></td>
						<td>
							<select class="edit_target">
								<option value="_parent" <?php if ($target == "_parent"): ?> selected="selected" <?php
            endif; ?> ><?php _e('Same window', "menus-plus"); ?></option>
								<option value="_blank" <?php if ($target == "_blank"): ?> selected="selected" <?php
            endif; ?> ><?php _e('New window', "menus-plus"); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Class", "menus-plus"); ?></div></td>
						<td><input class="edit_class widefat" value="<?php echo $class; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="edit_title widefat" value="<?php echo $title; ?>" /></td>
					</tr>
				<?php
        else: ?>
					
				<?php
        endif; ?>
				<tr>
					<td>
						<div align="right">
							<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
						</div>
					</td>
					<td>
						<a class="button" id="edit_submit" rel="<?php echo $id; ?>"><?php _e("Update"); ?></a>
						<a class="button" id="mp_cancel"><?php _e("Cancel", "menus-plus"); ?></a>
					</td>
				</tr>
			</table>
			
		</div>
		
		<?php
        exit();
    }
    function style() { ?>

		<style>
		
			.mp_margin_bottom h2 {
				font-weight:bold;
			}
		
			.mp_heading {
				font-size:.6em;
				font-weight:normal;
				font-family: 'Lucida Grande', Helvetica, Arial, sans-serif;
				
			}
			.mp_menu_title {
				font-weight: bold;
			}
		
			.mp_margin_bottom {
				margin-bottom:15px;
			}
			
			.mp_buttons_left {
				float:left;
			}
			
			.mp_buttons_right {
				float:right;
				margin-right:20px;
				display: none;
			}
			
				.mp_buttons_right img {
					vertical-align: middle;
				}
			
			#menusplus_list {
				padding:15px;
				width:95%;
			}
			
				#menusplus_list ul {
					list-style-type:none;
					margin:0px;
					padding:0px;
				}
				
				#menusplus_list ul li {
					background-color:#21759b;
				 	padding:10px;
				 	cursor: move;
					margin-top:0px;
					margin-left:0px;
					margin-right:0px;
					margin-bottom:6px;
				}
				
				#menusplus_list ul li.mp_list_Section {
					background-color:#C6C6C6;
				}
				
				
				#menusplus_list ul.parent_menu_box li {
					background-color:#e35f00;
				}
				
				
				
					.list_item_left {
						float:left;
					}
					
						.list_item_left .list_item_title {
							font-size:1.5em;
							font-weight:bold;
							color:#ffffff;
						}
					
					
					.list_item_right {
						float:right;
					}
					
						.list_item_type {
							color:#fff;
						}
					
						.list_item_right a:link, .list_item_right a:visited {
							color:#ffffff;
							margin-left:6px;
							text-decoration:none;
						}
						
						.list_item_right a.mp_remove {
							color:#ffa3a3;
							cursor:pointer;
							margin-left:6px;
						}
					
					.clear_list_floats {
						clear: both;
					}
				
				.mp_validate {
					background-color:#c0402a;
					color:#fff;
				}
				
				.mp_home_url {
					color:#21759b;
				}
				
				.mp_in_order_to {
					color:#21759b;
				}
				
				.depth_help {
					cursor:pointer;
				}
				
				.menusplus_loading {
					display: none;
				}

				.hidden { display: none; }
			
		</style>

	<?php
    }
    function list_menu() {
    	global $post;
        global $wpdb;
        $menu_id = $_POST['menu_id'];
        $items_table = $wpdb->prefix . "menusplus";
        $menus_table = $wpdb->prefix . "menusplus_menus";
        $wp_id = $post->ID;
        $wpdb->show_errors();
        $items = $wpdb->get_results("SELECT * FROM $items_table WHERE menu_id = $menu_id ORDER BY list_order ASC", ARRAY_A);
        if (count($items) > 0):
            foreach($items as $item):
                $id = $item['id'];
                $wp_id = $item['wp_id'];
                $type = $item['type'];
                $list_order = $item['list_order'];
                $url = $item['url'];
                $label = $item['label'];
                $menu_id = $item['menu_id'];
                $title = $item['title'];
                switch ($type):
                case "home":
                    $sort_title = $label;
                    $display_type = __("Home", "menus-plus");
                break;
                case "page":
                    $page = get_page($wp_id);
                    $sort_title = $page->post_title;
                    $display_type = __("Page", "menus-plus");
                break;
                case "post":
                    $page = get_page($wp_id);
                    $sort_title = $page->post_title;
                    $display_type = __("Post", "menus-plus");
                break;
                case "cat":
                    $cat = $wpdb->get_row("SELECT * FROM $wpdb->terms WHERE term_ID='$wp_id'", OBJECT);
                    $sort_title = $cat->name;
                    $display_type = __("Category", "menus-plus");
                break;
                case "url":
                    $sort_title = $label;
                    $display_type = __("URL", "menus-plus");
                break;
                case "section":
                    $sort_title = $label;
                    $display_type = __("Section", "menus-plus");
                break;
                case "hybrid":
                    $menu = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $wp_id", OBJECT);
                    $sort_title = $menu->menu_title;
                    $display_type = __("Hybrid", "menus-plus");
                break;
                default:
                endswitch;
?>
				<li id="mp_id_<?php echo $id; ?>" class="mp_list_item mp_list_<?php echo $display_type ?>">
					<div class="list_item_left">
						<div class="list_item_title">
							<?php echo $sort_title; ?>
						</div>
					</div>
					<div class="list_item_right">
						<div>
							
							<span class="list_item_type"><?php echo preg_replace('/Home/', 'Section', $display_type); ?></span> 						
							
							<?php if ($type == "hybrid"): ?>
								
								<a  href="themes.php?page=menusplus&menu_id=<?php echo $wp_id; ?>" title="<?php _e("Edit", "menus-plus"); ?> <?php echo $sort_title; ?>">
									<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/edit.png" align="absmiddle" />
								</a>
								
								<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_remove_hybrid_dialog&id=<?php echo $id; ?>&menu_id=<?php echo $wp_id; ?>&width=350&height=300" title="<?php _e("Remove", "menus-plus"); ?>">
									<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/remove.png" align="absmiddle" />
								</a>
								
							<?php
                else: ?>
								
								<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_edit_dialog&id=<?php echo $id; ?>&width=350&height=350&current_id=<?php echo $menu_id; ?>" title="<?php _e("Edit", "menus-plus"); ?> <?php echo $sort_title; ?>">
									<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/edit.png" align="absmiddle" />
								</a>
								
								<a class="mp_remove" id="mp_remove_<?php echo $id; ?>" title="<?php _e("Remove", "menus-plus"); ?>">
									<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/images/remove.png" align="absmiddle" />
								</a>
							
							<?php
                endif; ?>
							
						</div>
					</div>
					<div class="clear_list_floats"></div>
				</li>
				<?php
            endforeach;
        endif;
        exit();
    }
    function validate() {
        global $wpdb;
        $items_table = $wpdb->prefix . "menusplus";
        $menus_table = $wpdb->prefix . "menusplus_menus";
        $wpdb->show_errors();
        array_map('stripslashes_deep', $_POST);
        $type = $_POST['type'];
        $label = $_POST['label'];
        $url = $_POST['url'];
        $depth = $_POST['depth'];
        // Use PHP 5.2.0's filter_var for URL regex, if earlier PHP use the defined regex
        if (version_compare("5.2", phpversion(), "<=")) {
            $valid_url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
        } else {
            $regex = "((https?|ftp)\:\/\/)?"; // SCHEME
            $regex.= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
            $regex.= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
            $regex.= "(\:[0-9]{2,5})?"; // Port
            $regex.= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
            $regex.= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
            $regex.= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
            $valid_url = preg_match("/^$regex$/", $url);
        }
        if ($type == "url"):
            if (!$valid_url):
                echo "1"; // URL error
                exit();
            endif;
            if (empty($label)):
                echo "2"; // Label error
                exit();
            endif;
        /*elseif ($type == "section"):
            if (empty($label)):
                echo "2"; // Label error
                exit();
            endif;*/
        elseif ($type == "home"):
            if (empty($label)):
                echo "2"; // Label error
                exit();
            endif;
        elseif ($type == "hybrid"):
            if (empty($label)):
                echo "2"; // Label error
                exit();
            endif;
            if (!empty($url)):
                if (!$valid_url):
                    echo "1"; // URL error
                    exit();
                endif;
            endif;
        elseif ($type == "cat" || $type == "page"):
            if (!is_numeric($depth)):
                echo "3"; // Depth error
                exit();
            endif;
        endif;
        exit();
    }
    function js($menu_id) { ?>

		<script type="text/javascript">
			
			jQuery(document).ready(function($) {

				// Preloads
				menu_title(<?php echo $menu_id; ?>);
				menus_dropdown(<?php echo $menu_id; ?>);
				menusplus_list(<?php echo $menu_id; ?>);
				var nonce = $("input#menus_plus_nonce").val();
								
				// Add lists
				
				$('.mp_add a#add_submit').live("click",
					function () {
						$('.menusplus_loading').show();
						var type = $(this).attr('rel');
						var wp_id = $("select[name='add_wpid']").val();
						var opt_class = $('input.add_class').val();
						var url = $('input.add_url').val();
						var label = $('input.add_label').val();
						var children = $("input[name='add_children']:checked").val();
						var children_order = $('select.add_children_order').val();
						var children_order_dir = $('select.add_children_order_dir').val();
						var target = $('select.add_target').val();
						var depth = $('input.add_depth').val();
						var title = $('input.add_title').val();
						
						// Validate
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_validate", 
								type:type,
								label:label,
								url:url,
								depth:depth,
							},
							function(str) {
								$('input').removeClass('mp_validate');
								if (str == "1") {
									// URL issue
									$('input.add_url').addClass('mp_validate');
									$('.menusplus_loading').hide();
								} else if (str == "2") {
									// Label issue
									$('input.add_label').addClass('mp_validate');
									$('.menusplus_loading').hide();
								} else if (str == "3") {
									// Depth issue
									alert('<?php _e('Depth must be an integer.', "menus-plus"); ?>');
									$('input.add_depth').addClass('mp_validate');
									$('.menusplus_loading').hide();
							 	} else {
									// Insert
									$.post(
										"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
										{
											action:"menusplus_add", 
											type:type,
											wp_id:wp_id,
											children:children,
											children_order:children_order,
											children_order_dir:children_order_dir,
											opt_class:opt_class,
											label:label,
											url:url,
											menu_id : <?php echo $menu_id; ?>,
											target : target,
											depth:depth,
											title:title,
											nonce:nonce,
										},
										function(str) {
											$('input').removeClass('mp_validate');
											if (str == "") {
												tb_remove();
												menusplus_list(<?php echo $menu_id; ?>);
												flash_change(':last');
											} else {
												window.location.replace('themes.php?page=menusplus&menu_id=' + str);
											}
											$('.menusplus_loading').hide();
										}
									);
								}
							}
						);
					}
				);
				
				// Edit
				
				$('.mp_edit a#edit_submit').live("click",
					function () {
						$('.menusplus_loading').show();
						var id = $(this).attr('rel');
						var type = $("input.edit_type").val();
						var wp_id = $("select[name='edit_wpid']").val();
						var opt_class = $('input.edit_class').val();
						var url = $('input.edit_url').val();
						var label = $('input.edit_label').val();
						var children = $("input[name='edit_children']:checked").val();
						var children_order = $('select.edit_children_order').val();
						var children_order_dir = $('select.edit_children_order_dir').val();
						var target = $('select.edit_target').val();
						var depth = $('input.edit_depth').val();
						var title = $('input.edit_title').val();
						
						// Validate
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_validate", 
								type:type,
								label:label,
								url:url,
								depth:depth,
							},
							function(str) {
								$('input').removeClass('mp_validate');
								if (str == "1") {
									// URL issue
									$('input.edit_url').addClass('mp_validate');
									$('.menusplus_loading').hide();
								} else if (str == "2") {
									// Label issue
									$('input.edit_label').addClass('mp_validate');
									$('.menusplus_loading').hide();
								} else if (str == "3") {
									// Depth issue
									alert('<?php _e('Depth must be an integer.', "menus-plus"); ?>');
									$('input.edit_depth').addClass('mp_validate');
									$('.menusplus_loading').hide();
							 	} else {
									// Insert
									$.post(
										"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
										{
											action:"menusplus_edit", 
											id:id,
											wp_id:wp_id,
											children:children,
											children_order:children_order,
											children_order_dir:children_order_dir,
											opt_class:opt_class,
											label:label,
											url:url,
											type:type,
											target:target,
											depth:depth,
											title:title,
											nonce:nonce
										},
										function(str) {
											$('input').removeClass('mp_validate');
											tb_remove();
											flash_change("#mp_id_" + id);
											//menusplus_list(<?php echo $menu_id; ?>);
											$('.menusplus_loading').hide();
										}
									);
								}
							}
						);
					}
				);
				
				// Edit Hybrid 
				
				$('.mp_edit_hybrid a#edit_hybrid_submit').live("click",
					function () {
						$('.menusplus_loading').show();
						var menu_id = "<?php echo $menu_id; ?>";
						var label = $('input.edit_hybrid_label').val();
						var opt_class = $('input.edit_hybrid_class').val();
						var url = $('input.edit_hybrid_url').val();
						var title = $('input.edit_hybrid_title').val();
						var type = "hybrid";
						// Validate
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_validate", 
								type:type,
								label:label,
								url:url,
							},
							function(str) {
								$('input').removeClass('mp_validate');
								if (str == "1") {
									// URL issue
									$('input.edit_hybrid_url').addClass('mp_validate');
									$('.menusplus_loading').hide();
								
								} else if (str == "2") {
									// Label issue
									$('input.edit_hybrid_label').addClass('mp_validate');
									$('.menusplus_loading').hide();
							 	} else {
									// Edit
									$.post(
										"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
										{
											action:"menusplus_edit_hybrid", 
											menu_id:menu_id,
											opt_class:opt_class,
											label:label,
											url:url,
											title:title,
											nonce:nonce
										},
										function(str) {
											$('input').removeClass('mp_validate');
											tb_remove();
											menu_title(<?php echo $menu_id; ?>);
											menus_dropdown(<?php echo $menu_id; ?>);
											menusplus_list(<?php echo $menu_id; ?>);
											$('.menusplus_loading').hide();
										}
									);
								}
							}
						);
					}
				);
				
				$("a.mp_remove").live("click", 
					function () {
						$('.menusplus_loading').show();
						var id = $(this).attr('id');
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_remove",
								id:id,
								nonce : nonce
							},
							function(str) {
								menusplus_list(<?php echo $menu_id; ?>);
								$('.menusplus_loading').hide();
							}
						);
					}
				);
				
				$("a#remove_hybrid_submit").live("click",
					function () {
						$('.menusplus_loading').show();
						var id = $(this).attr("rel");
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_remove_hybrid", 
								id : id,
								nonce : nonce
							},
							function (str) {
								tb_remove();
								menusplus_list(<?php echo $menu_id; ?>);
								menus_dropdown(<?php echo $menu_id; ?>);
								menu_title(<?php echo $menu_id; ?>);
								$('.menusplus_loading').hide();
							}
						);
					}
				);
				
				// Sort
				
				$("#menusplus_list ul").sortable({
					update : function (event, ui) {
						$('.menusplus_loading').show();
						flash = ui.item.attr("id");
						list_order = $(this).sortable("serialize");
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_sort", 
								list_order:list_order,
								nonce : nonce
							},
							function(str) {
								$('.menusplus_loading').hide();
								flash_change('#' + flash);
							}
						);
					} ,
					opacity: 0.6 
				});
				
				$("a#mp_cancel").live("click",
					function () {
						tb_remove();
					} 	
				);	
				
				// Add new menus
				
				$('.new_menu a#new_menu_submit').live("click",
					function () {
						$('.menusplus_loading').show();
						var title = $('input.new_menu_title').val();
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_new_menu", 
								title : title,
								nonce : nonce
							},
							function(str) {
								$('input').removeClass('mp_validate');
								if (str == "empty") {
									// Title issue
									$('input.new_menu_title').addClass('mp_validate');
								} else {
									window.location.replace('themes.php?page=menusplus&menu_id=' + str);
								}
								$('.menusplus_loading').hide();
							}
						);
					}
				);	
				
				$('a#edit_menu_submit').live("click",
					function () {
						$('.menusplus_loading').show();
						var title = $('input.edit_menu_title').val();
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_edit_menu", 
								title : title,
								menu_id : <?php echo $menu_id; ?>,
								nonce : nonce
							},
							function(str) {
								$('input').removeClass('mp_validate');
								if (str == "1") {
									// Title issue
									$('input.edit_menu_title').addClass('mp_validate');
								} else {
									tb_remove();
									// Stays on the current menu for now. 
									menu_title(<?php echo $menu_id; ?>);
									menus_dropdown(<?php echo $menu_id; ?>);
									menusplus_list(<?php echo $menu_id; ?>);
									$('.menusplus_loading').hide();
								}
							}
						);
					}
				);
				
				// Remove Menus
				
				$('a#remove_menu_submit').live("click",
					function () {
						$('.menusplus_loading').show();
						var title = $('input.edit_menu_title').val();
						$.post(
							"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
							{
								action:"menusplus_remove_menu", 
								menu_id : <?php echo $menu_id; ?>,
								nonce : nonce
							},
							function(str) {
								if (str == 1) {
									alert('<?php _e('You cannot delete your only menu.', "menus-plus"); ?>');
								} else {
									window.location.replace('themes.php?page=menusplus');
								}
								$('.menusplus_loading').hide();
							}
						);
					}
				);
				
				// Switch Menus
				
				$('.mp_switch_menu').live("change" ,
					function () {
						var menu_id = $('.mp_switch_menu').val();
						window.location.replace('themes.php?page=menusplus&menu_id='+menu_id);
					}
				);
				
				// Help dialogs
				
				$(".depth_help").live("click", 
					function () {
						var msg = '<?php _e('This parameter controls how many levels in the hierarchy are to be included. \n\n 0 : Displays all depths \n 1 : Displays only the top heirarchy \n n : Displays items to the given depth \n\n Depending on the depth you set, it may override your children display settings.', "menus-plus"); ?>';
						alert(msg);
					}
				);	
								
				
				// Funtions
				
				function menu_title(menu_id) {
					$('.menusplus_loading').show();
					$.post(
						"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
						{
							action:"menusplus_menu_title",
							menu_id: menu_id
						},
						function(str) {
							$('.mp_menu_title').html(str);
							$('.menusplus_loading').hide();
						}
					);
				}
				
				function menus_dropdown(menu_id) {
					$('.menusplus_loading').show();
					$.post(
						"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
						{
							action:"menusplus_menus_dropdown",
							menu_id: menu_id
						},
						function(str) {
							$('select.mp_switch_menu').html(str);
							$('.menusplus_loading').hide();
						}
					);
				}
								
				function menusplus_list(menu_id, flash) {
					$('.menusplus_loading').show();
					$.post(
						"<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php", 
						{
							action:"menusplus_list",
							menu_id : menu_id
						},
						function(str) {
							$('#menusplus_list ul').html(str);
							removeThickBoxEvents();
							tb_init('a.thickbox, area.thickbox, input.thickbox');
							$('.menusplus_loading').hide();
						}
					);
				}
				
				function flash_change(flash) {
					$('#menusplus_list ul li' + flash).animate({
					    opacity: 0.25,
					  }, 100, function() {
					    // Animation complete.
					});
					$('#menusplus_list ul li' + flash).animate({
					    opacity: 1.0,
					  }, 1500, function() {
					    // Animation complete.
					});
				}
				
				function removeThickBoxEvents() {
			        $('.thickbox').each(function(i) {
			            $(this).unbind('click');
			        });
			    }
			
			});
			
		</script>

	    <?php
    }
}
function newsinit() {
    global $newsmenu;
    $newsmenu = new NewsMenu();
}
add_action('init', 'newsinit');

/*
function newletter_parts($passed_menu_id = null, $passed_class = null) {

	global $wpdb;
	$items_table = $wpdb->prefix . "menusplus";
	$menus_table = $wpdb->prefix . "menusplus_menus";
	$wpdb->show_errors();
	
	// Returns the best possible menu_id

	
	if (!$passed_menu_id) :
		$item = $wpdb->get_row("SELECT * FROM $menus_table ORDER BY id ASC", ARRAY_A );
		$menu_id = $item['id'];
	else :
		$menu_id = $passed_menu_id;
	endif;
	
	$items = $wpdb->get_results("SELECT * FROM $items_table WHERE menu_id = $menu_id ORDER BY list_order ASC", ARRAY_A);
	$last_class = 'main';
	
	if (count($items) > 0) :
		foreach ($items as $item) :

			$id = $item['id'];
			$wp_id = $item['wp_id'];
			$list_order = $item['list_order'];
			$type = $item['type'];

			$class = $item['class'];
            if ( empty( $item['class'] ) {
            	$class = $last_class;
            }else{
            	$last_class = $class;
            }
			$url = $item['url'];
			$label = $item['label'];
			$children = $item['children'];
			$children_order = $item['children_order'];
			$children_order_dir = $item['children_order_dir'];
			$target = $item['target'];
			$depth = $item['depth'];
			$title = $item['title'];
			
			$siteurl = get_bloginfo('siteurl');
			
	        if ( is_string($passed_class) ) {
	        	if ( $passed_class == $class ) {
	        		next;
	        	};
	        }
			if ($type == "section") :
			
			if ( $target == "side" ) {
				echo "\n<h2>$label</h2>\n";
			}   else{
				echo "\n<h3>$label</h3>\n";
			}

			
			endif;
			
			if ($type == "page") :
				
					$array = array(
						"title_li" => NULL,
						"include" => $wp_id,
						"sort_column" => $children_order,
						"sort_order" => $children_order_dir,
						"depth" => 1,
						"echo" => 0
					);
					
					$top = wp_list_pages($array);
					if ($children == "true" && get_pages("child_of=" . $wp_id)) {
						echo $top = str_replace("</li>", "", $top);
						$array = array(
							"title_li" => NULL,
							"child_of" => $wp_id,
							"sort_column" => $children_order,
							"sort_order" => $children_order_dir,
							"depth" => $depth,
							"echo" => 0
						);
						echo "<ul>";
						echo $children = wp_list_pages($array);
						echo "</ul></li>";
					} else {
						echo $top;
					}
					
			endif;
			
			if ($type == "post") :
											
				global $wpdb;
				global $post;
				$menus_table = $wpdb->prefix . "menusplus_menus";
				$wpdb->show_errors();

				//$posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE ID = '$wp_id'", ARRAY_A );
				$post = get_post( $wp_id );

				if ($post) :
						echo '<h2 class="' . $class . '">';
						echo '<a href="' . get_permalink($wp_id) . '" title="' . $title . '">' . $post['post_title'] . '</a>'; 
						echo '</h2>';
						echo apply_filters('the_content', $post['content']);
				endif;
							
			endif;
			
			if ($type == "cat") :
			
				// Identify the current category if we're not on an archive page
				
				if (is_single()) :
					$currCat = get_the_category();
					$currCatID = $currCat[0]->cat_ID;
				else : 
					$currCatID = NULL;
				endif;
				
				// Deal with the custom attribute title
												
				if ($children == "true") :
					$children = get_categories("child_of=$wp_id&orderby=$children_order&hide_empty=0");
					foreach ($children as $child) :
						$wp_id .= ", " . $child->cat_ID;
					endforeach;
				endif;
				
				$array = array(
					"title_li" => NULL,
					"hide_empty" => 0,
					"include" => $wp_id,
					"current_category" => $currCatID,
					"order" => $childre_order_dir,
					"orderby" => $children_order,
					"depth" => $depth					
				);
				
				wp_list_categories($array);
			
			endif;
			
			if ($type == "url") :
			
				echo "<li class=\"$class\">";
				echo "<a href=\"$url\" target=\"$target\" title=\"$title\">$label</a>";
				echo "</li>";
		
			endif;
			
			if ($type == "hybrid") :
			
				$menu = $wpdb->get_row("SELECT menu_title FROM $menus_table WHERE id = $wp_id");
				echo '<li class="' . $class . '"><a href="' . $url . '" title="' . $title . '">'.$menu->menu_title.'</a>';
				echo "<ul class='children'>";
					menusplus($wp_id);
				echo "</ul></li>";
			
			endif;
			
		endforeach;
    endif;

}
*/
?>
