<?php
/**
Template Name: Newsletters
 */
?>

<?php get_header(); ?>

		<?php
			$wp_query = new WP_Query();
			$wp_query->query( array( 'posts_per_page' => get_option( 'posts_per_page' ), 'paged' => $paged, 'post_type' => 'newsletter' ) );
			$more = 0;
		?>

		<?php if ( $wp_query->have_posts() ) : while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
<div class="ind-post">
	<h2><a href="<?php the_permalink() ?>" title="<?php printf(__("Permanent Link to %s", "magazine-basic"), the_title_attribute('echo=0')); ?>">
			<?php if(function_exists('has_post_thumbnail') && has_post_thumbnail()) { 
				//echo '<a href="'.get_permalink().'">';
				the_post_thumbnail('thumbnail', array('class'=>'alignleft'));
				//echo '</a>';
			} else { 
				echo resize(get_option('thumbnail_size_w'),get_option('thumbnail_size_h')); 
			}?>
			<?php the_title(); ?></a></h2>
	<?php /*<div class="meta">
		<?php if(theme_option('dates_index')=='on') { echo '<div class="date">'; the_time(get_option('date_format')); echo '</div>'; } ?>
		 <?php if(theme_option('authors_index')=='on') { _e("By", "magazine-basic"); echo ' '; the_author_posts_link(); } ?> 
	</div>*/?>
	<div class="clear">&nbsp;</div>
	<div class="storycontent">
		<?php
 			if(theme_option('excerpt_content')=='2') { 
				theme_content(__('Read more &raquo;', "magazine-basic"));
			} else {
				theme_excerpt(theme_option('excerpt_one'));
			}	
		?>
	</div>
</div>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
