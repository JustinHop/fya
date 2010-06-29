<?php
/*
Plugin Name: Menus Plus+
Plugin URI: http://www.keighl.com/plugins/menus-plus/
Description: Create <strong>multiple</strong> customized menus with pages, categories, and urls. Use a widget or a template tag <code>&lt;?php menusplus(); ?&gt;</code>. <a href="themes.php?page=menusplus">Configuration Page</a>
Version: 1.9.6
Author: Kyle Truscott
Author URI: http://www.keighl.com
*/

/*  Copyright 2009 Kyle Truscott  (email : info@keighl.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// TODO Address custom taxonomies
// TODO More dynamic storage system (for custom taxonomies)

#$menusplus = new MenusPlus();

class MenusPlus {

	function MenusPlus() {
		
		load_plugin_textdomain('menus-plus', false, 'menus-plus/languages');
		
		register_activation_hook(__FILE__, array(&$this, 'install'));
		
		add_action('admin_menu', array(&$this, 'add_admin'));
		
		add_action("admin_print_scripts", array(&$this, 'js_libs'));
		add_action("admin_print_styles", array(&$this, 'style_libs'));
		
		add_action('wp_ajax_menusplus_list', array(&$this, 'list_menu'));
		add_action('wp_ajax_menusplus_add_dialog', array(&$this, 'add_dialog'));
		add_action('wp_ajax_menusplus_edit_dialog', array(&$this, 'edit_dialog'));
		add_action('wp_ajax_menusplus_add', array(&$this, 'add'));
		add_action('wp_ajax_menusplus_validate', array(&$this, 'validate'));
		add_action('wp_ajax_menusplus_edit', array(&$this, 'edit'));
		add_action('wp_ajax_menusplus_sort', array(&$this, 'sort'));
		add_action('wp_ajax_menusplus_remove', array(&$this, 'remove'));
		add_action('wp_ajax_menusplus_remove_hybrid_dialog', array(&$this, 'remove_hybrid_dialog'));
		add_action('wp_ajax_menusplus_edit_hybrid_dialog', array(&$this, 'edit_hybrid_dialog'));
		add_action('wp_ajax_menusplus_edit_hybrid', array(&$this, 'edit_hybrid'));
		add_action('wp_ajax_menusplus_remove_hybrid', array(&$this, 'remove_hybrid'));
		
		
		add_action('wp_ajax_menusplus_menu_title', array(&$this, 'menu_title'));
		add_action('wp_ajax_menusplus_menus_dropdown', array(&$this, 'menus_dropdown'));
		
		add_action('wp_ajax_menusplus_add_new_menu_dialog', array(&$this, 'new_menu_dialog'));
		add_action('wp_ajax_menusplus_new_menu', array(&$this, 'new_menu'));
		
		add_action('wp_ajax_menusplus_edit_menu_dialog', array(&$this, 'edit_menu_dialog'));
		add_action('wp_ajax_menusplus_edit_menu', array(&$this, 'edit_menu'));
		
		add_action('wp_ajax_menusplus_remove_menu_dialog', array(&$this, 'remove_menu_dialog'));
		add_action('wp_ajax_menusplus_remove_menu', array(&$this, 'remove_menu'));
		
		add_action("widgets_init", array(&$this, 'init_widget'));
		add_shortcode('menusplus', array(&$this, 'mp_shortcode'));
				
	}
	
	// Install
	
	function install() {
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$menus_table = $wpdb->prefix . "menusplus_menus";
		
		$charset_collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset) ) {
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate) ) {
				$charset_collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		$items_fields = "
			id int NOT NULL AUTO_INCREMENT,
			wp_id int NULL,
			list_order int DEFAULT '0' NOT NULL, 
			type text NOT NULL,
			class text NULL,
			url text NULL,
			label text NULL,
			children text NULL,
			children_order text NULL,
			children_order_dir text NULL,
			menu_id int DEFAULT '1' NOT NULL,
			target text NULL,
			depth int DEFAULT '0' NOT NULL,
			title text NULL,
			PRIMARY  KEY id (id)
		";
		
		$menus_fields = "
			id int NOT NULL AUTO_INCREMENT,
			parent_id int NULL,
			menu_title text NULL,
			menu_description text NULL,
			PRIMARY  KEY id (id)
		";
		
		dbDelta("CREATE TABLE $items_table ($items_fields) $charset_collate;");
		dbDelta("CREATE TABLE $menus_table ($menus_fields) $charset_collate;");
		
		// Make sure there's something in the Menus Table
		
		$exists = $wpdb->get_results("SELECT * FROM $menus_table");
		
		if (!$exists) {
			$data_array = array(
				'menu_title' => __("Default", "menusplus")
			);
			
			$wpdb->insert($menus_table, $data_array );
		}
		
		$mp_version = "1.9.6";
		update_option('mp_version', $mp_version);

	}
	
	function add_admin() {

		add_theme_page('Menus Plus+', 'Menus Plus+', 'moderate_comments', 'menusplus', array(&$this, 'admin'));

	}
	
	function js_libs() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('thickbox');
			
	}
	
	function style_libs() {
		wp_enqueue_style('thickbox');
	}
	
	function init_widget() {

		register_widget('MenusPlusWidget');

	}
	
	// Views
	
	function admin() {
		
		$menu_id_from_get = $_GET['menu_id'];
		$menu_id = $this->get_menu_id($menu_id_from_get); 
	
		$parent = $this->menu_has_parent($menu_id);
		
		$this->js($menu_id, $parent);
		$this->style();
	
		if ( function_exists('wp_nonce_field') )
			wp_nonce_field('menus_plus_nonce', 'menus_plus_nonce');
			
		?> 

		<div class="wrap mp_margin_bottom">
	    	<h2>Menus Plus+ <span class="mp_heading">v<?php echo get_option('mp_version'); ?> <a href="http://www.keighl.com/plugins/menus-plus/">by Keighl</a></span></h2> 
		</div>
		<div class="wrap mp_margin_bottom">
			<div class="mp_buttons_left">
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=home&width=350&height=350" title="<?php _e("Add Home Page"); ?>"><?php _e("Home"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=cat&width=350&height=350" title="<?php _e("Add a Category"); ?>"><?php _e("Category"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=page&width=350&height=350" title="<?php _e("Add a Page"); ?>"><?php _e("Page"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=post&width=350&height=350" title="<?php _e("Add a Post"); ?>"><?php _e("Post"); ?></a>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=url&width=350&height=350" title="<?php _e("Add a URL"); ?>"><?php _e("URL"); ?></a>
				<?php if (!$parent):?>
				<a class="thickbox button" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_dialog&type=hybrid&width=350&height=350" title="<?php _e("Add a Hybrid Menu"); ?>"><?php _e("Hybrid Menu"); ?></a>
				<?php endif; ?>
				<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
			</div>
			<div class="mp_buttons_right">
				
				<span class="mp_menu_title"></span> 

				<select class="mp_switch_menu">
				</select>
				
				<?php if (!$parent):?>
				
				<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_edit_menu_dialog&menu_id=<?php echo $menu_id; ?>&width=350&height=200" title="<?php _e("Edit Menu"); ?>">
					<img src="<?php echo plugin_dir_url( __FILE__ );?>images/edit.png" />
				</a>
				<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_remove_menu_dialog&menu_id=<?php echo $menu_id; ?>&width=350&height=100" title="<?php _e("Delete Menu"); ?>">
					<img src="<?php echo plugin_dir_url( __FILE__ );?>images/remove.png" />
				</a>
				
				<?php endif; ?>
				
				<?php if ($parent) : ?>
					
					<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_edit_hybrid_dialog&menu_id=<?php echo $menu_id; ?>&width=350&height=350" title="<?php _e("Edit Hybrid Menu"); ?>">
						<img src="<?php echo plugin_dir_url( __FILE__ );?>images/edit.png" />
					</a>
					
				<?php endif; ?>
				
				<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_add_new_menu_dialog&width=350&height=100" title="<?php _e("New Menu"); ?>">
					<img src="<?php echo plugin_dir_url( __FILE__ );?>images/add.png" />
				</a>
				
			</div>
			<div class="clear_list_floats"></div>
		</div>
		<div class="wrap postbox" id="menusplus_list">
			<ul <?php if ($parent) { echo 'class="parent_menu_box"'; } ?> ></ul>
		</div>
		
		<?php if (!$parent) : ?>
			<div class="wrap">
				<table cellspacing="6">
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
		<?php endif; ?>
					
	 	<?php 

	}
	
	function add_dialog() {
		
		$type = $_GET['type'];
		
		if (!$type) { exit(); }
				
		?>
		<div class="mp_add">
			<table cellspacing="16" cellpadding="0">
				<?php if ($type == "home") : ?>
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
				<?php elseif ($type == "cat") : ?>
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
									if ( function_exists('mycategoryorder') ) :
										echo '<option value="order">' . __('My Category Order', "menus-plus") . '</option>';
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
								<img src="<?php echo plugin_dir_url( __FILE__ );?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php elseif ($type == "page") : ?>
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
									if ( function_exists('mypageorder') ) :
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
								<img src="<?php echo plugin_dir_url( __FILE__ );?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php elseif ($type == "post") : ?>
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
						<td><input class="add_class widefat" value="" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="add_title widefat" value="" /></td>
					</tr>
				<?php elseif ($type == "url") : ?>
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
				<?php elseif ($type == "hybrid") : ?>
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
				<?php endif; ?>
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
		
		if (!$id) { exit(); }
		
		// Assemble our knowledge of this list item
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();

		$metas = $wpdb->get_results("SELECT * FROM $items_table WHERE id = $id", ARRAY_A );
		
		if (count($metas) > 0) :
			foreach ($metas as $meta) :
				$type  = $meta['type'];
				$wp_id = $meta['wp_id'];
				$class = $meta['class'];
				$label = $meta['label'];
				$url   = $meta['url'];
				$children = $meta['children'];
				$children_order = $meta['children_order'];
				$children_order_dir = $meta['children_order_dir'];
				$list_order = $meta['list_order'];
				$menu_id = $meta['menu_id'];
				$target = $meta['target'];
				$depth = $meta['depth'];
				$title = $meta['title'];
			endforeach;
		endif;
		
		?>
		<div class="mp_edit">
			<input  type="hidden" value="<?php echo $type; ?>" class="edit_type" />
			<table cellspacing="16" cellpadding="0">
				<?php if ($type == "home") : ?>
					<tr>
						<td><div align="right"><?php _e("URL", "menus-plus"); ?></div></td>
						<td><span class="mp_home_url"><? bloginfo('siteurl'); ?></span></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Label", "menus-plus"); ?></div></td>
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
				<?php elseif ($type == "cat") : ?>
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
	                        	<input type="radio" name="edit_children" value="true" <?php if ($children == "true") : ?> checked="checked" <?php endif; ?> /> <?php _e("Yes", "menus-plus"); ?>
	                        </label>
							<label>
	                        	<input type="radio" name="edit_children" value="false" <?php if ($children == "false") : ?> checked="checked" <?php endif; ?> /> <?php _e("No", "menus-plus"); ?>
	                        </label>
	                    </td>
				    </tr>
					<tr id="children_order_box">
						<td><div align="right"><?php _e("Child Order", "menus-plus"); ?></div></td>
						<td>
							<select class="edit_children_order">
								<option value="name" <?php if ($children_order == "name") : ?> selected="selected" <?php endif; ?> ><?php _e("Name", "menus-plus"); ?></option>
								<option value="ID" <?php if ($children_order == "ID") : ?> selected="selected" <?php endif; ?> ><?php _e("ID", "menus-plus"); ?></option>
								<option value="count" <?php if ($children_order == "count") : ?> selected="selected" <?php endif; ?> ><?php _e("Count", "menus-plus"); ?></option>
								<option value="slug" <?php if ($children_order == "slug") : ?> selected="selected" <?php endif; ?> ><?php _e("Slug", "menus-plus"); ?></option>
								<option value="term_group" <?php if ($children_order == "term_group") : ?> selected="selected" <?php endif; ?> ><?php _e("Term Group", "menus-plus"); ?></option>
								<?php if ( function_exists('mycategoryorder') ) : ?>
									<option value="order" <?php if ($children_order == "order") : ?> selected="selected" <?php endif; ?> ><?php _e("My Category Order", "menus-plus"); ?></option>	
								<?php endif; ?>
							</select>
							<select class="edit_children_order_dir">
								<option value="ASC" <?php if ($children_order_dir == "ASC") : ?> selected="selected" <?php endif; ?> >ASC</option>
								<option value="DESC" <?php if ($children_order_dir == "DESC") : ?> selected="selected" <?php endif; ?> >DESC</option>
							</select>
	                    </td>
				    </tr>
					<tr>
						<td><div align="right"><?php _e("Depth", "menus-plus"); ?></div></td>
						<td>
							<input class="edit_depth widefat" value="<?php echo $depth; ?>" style="width:2em;" />
							<a class="depth_help">
								<img src="<?php echo plugin_dir_url( __FILE__ );?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php elseif ($type == "page") : ?>
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
	                        	<input type="radio" name="edit_children" value="true" <?php if ($children == "true") : ?> checked="checked" <?php endif; ?> /> <?php _e("Yes", "menus-plus"); ?>
	                        </label>
							<label>
	                        	<input type="radio" name="edit_children" value="false" <?php if ($children == "false") : ?> checked="checked" <?php endif; ?> /> <?php _e("No", "menus-plus"); ?>
	                        </label>
	                    </td>
				    </tr>
					<tr id="children_order_box">
						<td><div align="right"><?php _e("Child Order", "menus-plus"); ?></div></td>
						<td>
							<select class="edit_children_order">
								<option value="post_title" <?php if ($children_order == "post_title") : ?> selected="selected" <?php endif; ?> ><?php _e("Title", "menus-plus"); ?></option>
								<option value="post_date" <?php if ($children_order == "post_date") : ?> selected="selected" <?php endif; ?> ><?php _e("Date", "menus-plus"); ?></option>
								<option value="post_modified" <?php if ($children_order == "post_modified") : ?> selected="selected" <?php endif; ?> ><?php _e("Date Modified", "menus-plus"); ?></option>
								<option value="ID" <?php if ($children_order == "ID") : ?> selected="selected" <?php endif; ?> ><?php _e("ID", "menus-plus"); ?></option>
								<option value="post_author" <?php if ($children_order == "post_author") : ?> selected="selected" <?php endif; ?> ><?php _e("Author", "menus-plus"); ?></option>
								<option value="post_name" <?php if ($children_order == "post_name") : ?> selected="selected" <?php endif; ?> ><?php _e("Slug", "menus-plus"); ?></option>
								<?php if ( function_exists('mypageorder') ) : ?>
									<option value="menu_order" <?php if ($children_order == "menu_order") : ?> selected="selected" <?php endif; ?> ><?php _e("My Page Order", "menus-plus"); ?></option>	
								<?php endif; ?>
							</select>
							<select class="edit_children_order_dir">
								<option value="ASC" <?php if ($children_order_dir == "ASC") : ?> selected="selected" <?php endif; ?> >ASC</option>
								<option value="DESC" <?php if ($children_order_dir == "DESC") : ?> selected="selected" <?php endif; ?> >DESC</option>
							</select>
	                    </td>
				    </tr>
					<tr>
						<td><div align="right"><?php _e("Depth", "menus-plus"); ?></div></td>
						<td>
							<input class="edit_depth widefat" value="<?php echo $depth; ?>" style="width:2em;" />
							<a class="depth_help">
								<img src="<?php echo plugin_dir_url( __FILE__ );?>images/help.png" align="absmiddle" />
							</a>
	                    </td>
				    </tr>
				<?php elseif ($type == "post") : ?>
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
						<td><input class="edit_class widefat" value="<?php echo $class; ?>" /></td>
					</tr>
					<tr>
						<td><div align="right"><?php _e("Attribute Title", "menusplus"); ?></div></td>
						<td><input class="edit_title widefat" value="<?php echo $title; ?>" /></td>
					</tr>
				<?php elseif ($type == "url") : ?>
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
								<option value="_parent" <?php if ($target == "_parent") : ?> selected="selected" <?php endif; ?> ><?php _e('Same window', "menus-plus"); ?></option>
								<option value="_blank" <?php if ($target == "_blank") : ?> selected="selected" <?php endif; ?> ><?php _e('New window', "menus-plus"); ?></option>
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
				<?php else : ?>
					
				<?php endif; ?>
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
	
	function new_menu_dialog() {
		
		?>
		
		<div class="new_menu">
			<table cellspacing="16" cellpadding="0">
				<tr>
					<td><div align="right"><?php _e("Title", "menus-plus"); ?></div></td>
					<td><input class="new_menu_title widefat" value="" /></td>
				</tr>
				<tr>
					<td>
						<div align="right">
							<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
						</div>
					</td>
					<td>
						<a class="button" id="new_menu_submit" rel="<?php echo $type; ?>"><?php _e("Add", "menus-plus"); ?></a>
						<a class="button" id="mp_cancel"><?php _e("Cancel", "menus-plus"); ?></a>
					</td>
				</tr>
			</table>
		</div>
		
		<?php
		
		exit();
		
	}
	
	function edit_menu_dialog() {
		
		$menu_id = $_GET['menu_id'];
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();

		$menus = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $menu_id", ARRAY_A );
		
		$title = $menus['menu_title'];
		
		?>
		
		<div class="new_menu">
			<table cellspacing="16" cellpadding="0">
				<tr>
					<td><div align="right"><?php _e("Title", "menus-plus"); ?></div></td>
					<td><input class="edit_menu_title widefat" value="<?php echo $title; ?>" /></td>
				</tr>
				<tr>
					<td>
						<div align="right">
							<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
						</div>
					</td>
					<td>
						<a class="button" id="edit_menu_submit" rel="<?php echo $type; ?>"><?php _e("Update", "menus-plus"); ?></a>
						<a class="button" id="mp_cancel"><?php _e("Cancel", "menus-plus"); ?></a>
					</td>
				</tr>
			</table>
		</div>
		
		<?php
		
		exit();
		
	}
	
	function remove_menu_dialog() {
		
		$menu_id = $_GET['menu_id'];
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();

		$menus = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $menu_id", ARRAY_A );
		
		$title = $menus['menu_title'];
		
		?>
		
		<div class="remove_menu">
			<p><?php _e("Are you sure you want to delete the menu <strong>$title</strong>, and all menus beneath it?", "menus-plus"); ?></p>
			<p>
				<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
				<a class="button" id="remove_menu_submit" rel="<?php echo $type; ?>"><?php _e("Delete", "menus-plus"); ?></a>
				<a class="button" id="mp_cancel"><?php _e("Cancel", "menus-plus"); ?></a>
			</p>
		</div>
		
		<?php
		
		exit();
		
	}
	
	function edit_hybrid_dialog() {
		
		$id = $_GET['menu_id'];
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();

		$menu = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $id", OBJECT );
		$item = $wpdb->get_row("SELECT * FROM $items_table WHERE wp_id = $id", OBJECT );
		
		?>
		<div class="mp_edit_hybrid">
		<table cellspacing="16" cellpadding="0">
			<tr>
				<td><div align="right"><?php _e("Label", "menus-plus"); ?></div></td>
				<td><input class="edit_hybrid_label widefat" value="<?php echo $menu->menu_title; ?>" /></td>
			</tr>
			<tr>
				<td><div align="right"><?php _e("Class", "menus-plus"); ?></div></td>
				<td><input class="edit_hybrid_class widefat" value="<?php echo $item->class; ?>" /></td>
			</tr>
			<tr>
				<td><div align="right"><?php _e("URL", "menus-plus"); ?></div></td>
				<td><input class="edit_hybrid_url widefat" value="<?php echo $item->url; ?>" /></td>
			</tr>
			<tr>
				<td><div align="right"><?php _e("Attribute Title", "menus-plus"); ?></div></td>
				<td><input class="edit_hybrid_title widefat" value="<?php echo $item->title; ?>" /></td>
			</tr>
			<tr>
				<td>
					<div align="right">
						<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
					</div>
				</td>
				<td>
					<a class="button" id="edit_hybrid_submit" rel="<?php echo $id; ?>"><?php _e("Update"); ?></a>
					<a class="button" id="mp_cancel"><?php _e("Cancel", "menus-plus"); ?></a>
				</td>
			</tr>
		</table>
		</div>
		
		<?php
		
		exit();
		
	}
	
	function remove_hybrid_dialog() {
		
		$menu_id = $_GET['menu_id'];
		$id = $_GET['id'];
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();

		$menus = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $menu_id", ARRAY_A );
		
		$title = $menus['menu_title'];
		
		?>
		
		<div class="remove_menu">
			<p><?php _e("Are you sure you want to delete <strong>$title</strong>? All other menus and items beneath it will be deleted as well.", "menus-plus"); ?></p>
			<p>
				<img src="<?php echo admin_url("images/loading.gif"); ?>" align="absmiddle" class="menusplus_loading" />
				<a class="button" id="remove_hybrid_submit" rel="<?php echo $id; ?>"><?php _e("Delete", "menus-plus"); ?></a>
				<a class="button" id="mp_cancel"><?php _e("Cancel", "menus-plus"); ?></a>
			</p>
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
			
		</style>

	<?php

	}

	function js($menu_id) {
		?>

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
									alert('<?php _e('Depth must be an integer.' , "menus-plus"); ?>');
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
	
	// Methods
	
	function get_menu_id($menu_id_from_get = null) {
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		// Returns the best possible menu_id
		
		if (!$menu_id_from_get) :
			$item = $wpdb->get_row("SELECT * FROM $menus_table ORDER BY id", ARRAY_A );
			return $item['id'];
		else :
			return $menu_id_from_get;
		endif;
		
	}
	
	function menu_has_children($menu_id) {
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		// Does this menu have children?
		
		$children = $wpdb->get_results("SELECT * FROM $menus_table WHERE parent_id = $menu_id", ARRAY_A);
		$children_a = array();

		if (!$children) :
			RETURN FALSE;
		else :
			foreach ($children as $child) :
				$children_a[] = $child['id'];
			endforeach;
			RETURN $children_a;
		endif;
		
	}
	
	function menu_has_parent($menu_id) {
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		// Does this menu have a parent?
		
		$menu = $wpdb->get_row("SELECT parent_id FROM $menus_table WHERE id = $menu_id", OBJECT);
		
		if (!$menu->parent_id) {
			RETURN FALSE;
		} else {
			RETURN $menu->parent_id;
		}
		
	}
		
	function menu_parent($menu_id){
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
				
		$menu = $wpdb->get_row("SELECT parent_id FROM $menus_table WHERE id = $menu_id", OBJECT);
		
		if (!$menu->parent_id) {
			RETURN FALSE;
		} else {
			$parent_id = $menu->parent_id;
			$parent = $wpdb->get_row("SELECT menu_title FROM $menus_table WHERE id = $parent_id", OBJECT);
			RETURN $parent->menu_title;
		}
		
	}
	
	function menu_walker($menu_id){
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$parent = $this->menu_has_parent($menu_id); 
		
		if ($parent) :
			$level = 0;
			while ($parent) :
				$parent = $this->menu_has_parent($parent);
				$level++;
			endwhile;
			RETURN $level * 15 ."px";
		else :
			RETURN 0 . "px";
		endif;
		
	}
	
	function menu_title() {
		
		$menu_id = $_POST['menu_id'];
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$menu = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $menu_id", ARRAY_A );
		
		$parent = $this->menu_has_parent($menu_id);
		
		if ($parent) :
			echo $this->menu_parent($menu_id) . " : ";
		else :
			//echo $menu['menu_title'];
		endif;
		exit();
		
	}
	
	function menus_dropdown() {
		
		$menu_id = $_POST['menu_id'];
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$items = $wpdb->get_results("SELECT * FROM $menus_table WHERE parent_id is NULL ORDER BY menu_title ASC", ARRAY_A );
		
		if ($items) :
		
			foreach ($items as $item) :
				
				$id = $item['id'];
				$title = $item['menu_title'];
		
				$is_selected = ($id == $menu_id) ? 'selected="selected"' : '';		
				$level = $this->menu_walker($id);
				echo "<option style=\"margin-left:$level;\" $is_selected value=\"$id\">$title</option>";
				
				$children = $this->menu_has_children($id);
								
				if ($children) :
					foreach ($children as $child) :
						
						$meta = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $child", OBJECT);
						$level = $this->menu_walker($child);
						$is_selected = ($child == $menu_id) ? 'selected="selected"' : '';	
						echo "<option style=\"margin-left:$level;\" $is_selected value=\"$child\">$meta->menu_title</option>";
											
					endforeach;
				endif;
			
			endforeach;
		
		endif;
		
		exit();
		
	}
	
	function list_menu() {

		global $wpdb;
		
		$menu_id = $_POST['menu_id'];
		
		$items_table = $wpdb->prefix . "menusplus";
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();

		$items = $wpdb->get_results("SELECT * FROM $items_table WHERE menu_id = $menu_id ORDER BY list_order ASC", ARRAY_A );

		if (count($items) > 0) :
			foreach ($items as $item) :

				$id = $item['id'];
				$wp_id = $item['wp_id'];
				$type = $item['type'];
				$list_order = $item['list_order'];
				$url = $item['url'];
				$label = $item['label'];
				$menu_id = $item['menu_id'];
				
				switch ($type) :
					case "home" :
						$sort_title = $label;
						$display_type = __("Home", "menus-plus");
						break;
					case "page" :
						$page = get_page($wp_id);
						$sort_title = $page->post_title;
						$display_type = __("Page", "menus-plus");
						break;
					case "post" :
						$page = get_page($wp_id);
						$sort_title = $page->post_title;
						$display_type = __("Post", "menus-plus");
						break;
					case "cat" :
						$cat = $wpdb->get_row("SELECT * FROM $wpdb->terms WHERE term_ID='$wp_id'", OBJECT);
						$sort_title = $cat->name; 
						$display_type = __("Category", "menus-plus");
						break;
					case "url" :
						$sort_title = $label;
						$display_type = __("URL", "menus-plus");
						break;
					case "hybrid" :
						$menu = $wpdb->get_row("SELECT * FROM $menus_table WHERE id = $wp_id", OBJECT );
						$sort_title = $menu->menu_title;
						$display_type = __("Hybrid", "menus-plus");
						break;
					default:
				endswitch;
				?>
				<li id="mp_id_<?php echo $id; ?>" class="mp_list_item">
					<div class="list_item_left">
						<div class="list_item_title">
							<?php echo $sort_title; ?>
						</div>
					</div>
					<div class="list_item_right">
						<div>
							
							<span class="list_item_type"><?php echo $display_type; ?></span> 						
							
							<?php if ($type == "hybrid") : ?>
								
								<a  href="themes.php?page=menusplus&menu_id=<?php echo $wp_id; ?>" title="<?php _e("Edit", "menus-plus"); ?> <?php echo $sort_title; ?>">
									<img src="<?php echo plugin_dir_url( __FILE__ );?>images/edit.png" align="absmiddle" />
								</a>
								
								<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_remove_hybrid_dialog&id=<?php echo $id; ?>&menu_id=<?php echo $wp_id; ?>&width=350&height=300" title="<?php _e("Remove", "menus-plus"); ?>">
									<img src="<?php echo plugin_dir_url( __FILE__ );?>images/remove.png" align="absmiddle" />
								</a>
								
							<?php else : ?>
								
								<a class="thickbox" href="<?php echo get_option('siteurl'); ?>/wp-admin/admin-ajax.php?action=menusplus_edit_dialog&id=<?php echo $id; ?>&width=350&height=350&current_id=<?php echo $menu_id; ?>" title="<?php _e("Edit", "menus-plus"); ?> <?php echo $sort_title; ?>">
									<img src="<?php echo plugin_dir_url( __FILE__ );?>images/edit.png" align="absmiddle" />
								</a>
								
								<a class="mp_remove" id="mp_remove_<?php echo $id; ?>" title="<?php _e("Remove", "menus-plus"); ?>">
									<img src="<?php echo plugin_dir_url( __FILE__ );?>images/remove.png" align="absmiddle" />
								</a>
							
							<?php endif; ?>
							
						</div>
					</div>
					<div class="clear_list_floats"></div>
				</li>
				<?php 

			endforeach;
		endif;

		exit();

	}

	function add() {

		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit(); 
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$data = $this->prep_item_post($_POST);
		$data['list_order'] = $this->highest_order($data['menu_id']) + 1;
		unset($data['id']); // Unsets the id key used only by Edit
		unset($data['nonce']);
							
		if ($data['type'] == "hybrid") :
								
			$menu_data_array = array(
				'menu_title' => $data['label'],
				'parent_id'	 => $data['menu_id']
			);

			$wpdb->insert($menus_table, $menu_data_array );
			$last_result = $wpdb->insert_id;
			$data['wp_id'] = $last_result; // Insert the parent menu id as the wpid. 			
					
			echo $last_result; // Redirect for new hybrid list
			
		endif;

		$wpdb->insert($items_table, $data );
		exit();

	}

	function edit() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();
		
		$data = $this->prep_item_post($_POST);
		
		echo $where = array('id' => $data['id']);
		unset($data['id']); // Don't pass this
		unset($data['nonce']);
		$wpdb->update($items_table, $data, $where );
		
		exit();
		
	}
		
	function edit_hybrid() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$data = $this->prep_hybrid_post($_POST);
				
		$item_array = $data;
			unset($item_array['label']);
			unset($item_array['menu_id']);
			unset($item_array['nonce']);
		$where = array('wp_id' => $data['menu_id']);
		$wpdb->update($items_table, $item_array, $where );
		
		$menu_array = array(
			'menu_title' => $data['label'], // In the future, use unset() filter data. 
		);
		$where = array('id' => $data['menu_id']);
		$wpdb->update($menus_table, $menu_array, $where );
		
		exit();
	}	
		
	function validate() {
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		array_map('stripslashes_deep', $_POST);
		
		$type  = $_POST['type'];
		$label = $_POST['label'];
		$url   = $_POST['url'];
		$depth = $_POST['depth'];
		
		// Use PHP 5.2.0's filter_var for URL regex, if earlier PHP use the defined regex
		
		if (version_compare("5.2", phpversion(), "<=")) { 
			$valid_url = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
		} else {
			$regex = "((https?|ftp)\:\/\/)?"; // SCHEME
			$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
		    $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
		    $regex .= "(\:[0-9]{2,5})?"; // Port
		    $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
		    $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
		    $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor
			$valid_url = preg_match("/^$regex$/", $url);
		}	
	
		if ($type == "url") :
					
			if (!$valid_url) :
				echo "1"; // URL error
				exit();
			endif;
			
			if (empty($label)) :
				echo "2"; // Label error
				exit();
			endif;
			
		elseif ($type == "home") :
		
			if (empty($label)) :
				echo "2"; // Label error
				exit();
			endif;
			
		elseif ($type == "hybrid") :
		
			if (empty($label)) :
				echo "2"; // Label error
				exit();
			endif;
			
			if (!empty($url)) :
				if (!$valid_url) :
					echo "1"; // URL error
					exit();
				endif;
			endif;
			
		elseif ($type == "cat" || $type == "page") :

			if (!is_numeric($depth)) :
				echo "3"; // Depth error
				exit();
			endif;
			
		endif;
		
		exit();
		
	}
	
	function sort() {

		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();

		$ids = $_POST['list_order'];
		$ids = explode('mp_id[]=', $ids);

		$list_order = -1;

		foreach ($ids as $id) :

			$list_order++;

			$pattern = "/&/";
			$id = preg_replace($pattern, '' , $id);
						
			$data_array = array(
				"list_order" => $list_order
				);
			$where = array('id' => $id);
			$wpdb->update($items_table, $data_array, $where );

		endforeach;

		exit();

	}

	function new_menu() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		$title = $_POST['title'];
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
	
	function edit_menu() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		$title = $_POST['title'];
		$id = $_POST['menu_id'];
		
		if (empty($title)) { echo 1; exit(); }
		
		$title = stripslashes($title);
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$data_array = array(
			'menu_title' => $title,
		);
		
		$where = array('id' => $id);
		$wpdb->update($menus_table, $data_array, $where );

		exit();
		
	}
	
	function remove_menu() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		$id = $_POST['menu_id'];
		
		// Delete the menu
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();
		
		// How many menus are there?
		
		$count = $wpdb->query("SELECT * from $menus_table WHERE parent_id is NULL");

		if ($count == 1) :
		
			echo 1;
			
		else :
		
			$wpdb->query("DELETE from $items_table WHERE menu_id = $id");
			$wpdb->query("DELETE from $menus_table WHERE id = $id");
			$children = $this->menu_has_children($id);
			if ($children) :
				foreach ($children as $child) :
					$wpdb->query("DELETE from $menus_table WHERE id = $child");
					$wpdb->query("DELETE from $items_table WHERE wp_id = $child");
					$wpdb->query("DELETE from $items_table WHERE menu_id = $child");
				endforeach;
			endif;
			
		endif;

		exit();
		
	}

	function remove() {

		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();

		$id = $_POST['id'];
		$id = trim($id, 'mp_remove_');
		
		$wpdb->query("DELETE from $items_table WHERE id = $id");

		exit();

	}
	
	function remove_hybrid() {
		
		if (!wp_verify_nonce($_POST['nonce'], 'menus_plus_nonce')) 
			exit();
		
		$id = $_POST['id'];
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$item = $wpdb->get_row("SELECT * FROM $items_table WHERE id = $id", OBJECT);
		$menu_id = $item->wp_id; 
		
		$wpdb->query("DELETE FROM $menus_table WHERE id = $menu_id");
		
		// Now delete all the items beneath this hybrid
		
		$wpdb->query("DELETE FROM $items_table WHERE wp_id = $menu_id");
		$wpdb->query("DELETE FROM $items_table WHERE menu_id = $menu_id");
		
		exit();
	}
	
	function highest_order($menu_id) {
		
		// Find the highest order thus far
		// dapt for future release to accomdate sortable children
		
		global $wpdb;
		$items_table = $wpdb->prefix . "menusplus";
		$wpdb->show_errors();
		
		$items = $wpdb->get_results("SELECT list_order FROM $items_table WHERE menu_id = $menu_id", ARRAY_N);

		if (count($items) > 0) :
			$order_set = array();
			foreach ($items as $item) :
			  $order_set[] = $item[0];
			endforeach;
			$highest_order = max($order_set);
		else :
			$highest_order = 0;
	    endif;

		return $highest_order;
		
		exit();
		
	}
	
	function is_undefined($str) {
		if ($str == "undefined" || empty($str) || !$str || $str == "") : 
			return null;
		else : 
			return $str;
		endif;
	}
	
	function prep_item_post($arr) {
		
		$arr = array_map( 'stripslashes_deep', $arr );
		$arr['id']  = $this->is_undefined($arr['id']); // Only used for Edit
		$arr['wp_id'] = intval($this->is_undefined($arr['wp_id']));
		$arr['class'] = strip_tags($this->is_undefined($arr['opt_class']));  // Changes key from opt_class to class ... 
		$arr['label'] = $this->is_undefined($arr['label']);
		$arr['url']  =  esc_url_raw($this->is_undefined($arr['url']));
		$arr['children'] = $this->is_undefined($arr['children']);
		$arr['children_order'] = $this->is_undefined($arr['children_order']);
		$arr['children_order_dir'] = $this->is_undefined($arr['children_order_dir']);
		$arr['target'] = $target = $this->is_undefined($arr['target']);
		$arr['depth'] = intval($this->is_undefined($arr['depth']));
		$arr['title'] = strip_tags($this->is_undefined($arr['title']));
		
		unset($arr['action']); 
		unset($arr['opt_class']); 
		
		return $arr;
	}
	
	function prep_hybrid_post($arr) {
		
		$arr = array_map( 'stripslashes_deep', $arr );
		$arr['menu_id']  = intval($this->is_undefined($arr['menu_id']));
		$arr['label']  = $this->is_undefined($arr['label']); 
		$arr['class'] = strip_tags($this->is_undefined($arr['opt_class'])); // Changes key from opt_class to class ... 
		$arr['url']  =  esc_url_raw($this->is_undefined($arr['url']));
		$arr['title'] = strip_tags($this->is_undefined($arr['title']));
				
		unset($arr['action']); 
		unset($arr['opt_class']); 
		
		return $arr;
	}
	
	function mp_dropdown_posts($selected_wpid = null) {
		
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$items = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC", ARRAY_A );
		
		if ($items) :
		
			foreach ($items as $item) :
				$wpid = $item['ID'];
				$post_title = $item['post_title'];
				$is_selected = ($wpid == $selected_wpid) ? 'selected="selected"' : '';
				echo "<option value=\"$wpid\" $is_selected >$post_title</option>";
			endforeach;
		
		endif;
		
	}
	
	// Shortcode
	
	function mp_shortcode($atts) {
		
		extract(shortcode_atts(array(
			'menu' => null,
		), $atts));

		return "<ul>" . menusplus($menu) . "</ul";
		
	}

}

class MenusPlusWidget extends WP_Widget {
	
	function MenusPlusWidget() {
		
		$widget_ops = array( 'classname' => 'menus_plus', 'description' => 'Add one of your Menus Plus+ lists in widget form.' );

		$control_ops = array( 'id_base' => 'menus_plus' );

		$this->WP_Widget( 'menus_plus', __('Menus Plus+', 'menus_plus'), $widget_ops, $control_ops );
		
	}
	
	function form($instance) {
		
		$instance = wp_parse_args( (array) $instance, $defaults );	
		
		?>
		
		<table width="100%" cellspacing="6">
			<!-- Title -->
			<tr>
				<td>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><strong><?php _e('Title:', "menus-plus"); ?></strong></label>
				</td>
				<td>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
				</td>
			</tr>
			<!-- Menu -->
			<tr>
				<td>
					<label for="<?php echo $this->get_field_id( 'menu' ); ?>"><strong><?php _e('Menu:', "menus-plus"); ?></strong></label>
				</td>
				<td>
					<select id="<?php echo $this->get_field_id( 'menu' ); ?>" name="<?php echo $this->get_field_name( 'menu' ); ?>">
						<?php $this->menus_dropdown($instance['menu']); ?>
					</select>
				</td>
			</tr>
		</table>
		
		<?php		
	}
	
	function update($new_instance, $old_instance) {
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['menu'] = strip_tags( $new_instance['menu'] );
		
		return $instance;
		
	}
	
	function menus_dropdown($menu = NULL) {
				
		global $wpdb;
		$menus_table = $wpdb->prefix . "menusplus_menus";
		$wpdb->show_errors();
		
		$items = $wpdb->get_results("SELECT * FROM $menus_table WHERE parent_id is NULL ORDER BY id ASC", ARRAY_A );
		
		if ($items) :
		
			foreach ($items as $item) :
				$id = $item['id'];
				$title = $item['menu_title'];
				
				$is_selected = ($menu == $id) ? 'selected="selected"' : '';
				
				echo "<option value=\"$id\" $is_selected >$title</option>";
			
			endforeach;
		
		endif;
		
	}
	
	function widget($args, $instance) {
		
		extract( $args );
		
	 	$title = apply_filters('widget_title', $instance['title'] );
		$menu = $instance['menu'];
		
		echo $before_widget;
		
			if ($title) : 
				echo $before_title . $title . $after_title;
			endif;
			
			echo "<ul class='menus-plus-widget' id='menus-plus-" . $menu . "'>";
			if (function_exists('menusplus')) :
				menusplus($menu);
			endif;
			echo "</ul>";
		
		echo $after_widget;
		
	}
	
}

// Template tags

function menusplus($passed_menu_id = null) {

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
	
	if (count($items) > 0) :
		foreach ($items as $item) :

			$id = $item['id'];
			$wp_id = $item['wp_id'];
			$list_order = $item['list_order'];
			$type = $item['type'];
			$class = $item['class'];
			$url = $item['url'];
			$label = $item['label'];
			$children = $item['children'];
			$children_order = $item['children_order'];
			$children_order_dir = $item['children_order_dir'];
			$target = $item['target'];
			$depth = $item['depth'];
			$title = $item['title'];
			
			$siteurl = get_bloginfo('siteurl');
			
			if ($type == "home") :
			
				echo '<li class="' . $class . '">';
				echo '<a href="' . $siteurl . '" title="' . $title . '">' . $label . '</a>';
				echo "</li>";
			
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
				$menus_table = $wpdb->prefix . "menusplus_menus";
				$wpdb->show_errors();

				$posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE ID = '$wp_id'", ARRAY_A );

				if ($posts) :
					foreach ($posts as $post) :
						echo '<li class="' . $class . '">';
						echo '<a href="' . get_permalink($wp_id) . '" title="' . $title . '">' . $post['post_title'] . '</a>'; 
						echo '</li>';
					endforeach;
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

?>
