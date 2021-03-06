<?php get_header(); ?>
	<?php
    $mySearch =& new WP_Query("s=$s & showposts=-1");
    $num = $mySearch->post_count;
    echo '<h1 class="catheader">'; printf(__('%1$s search results for "%2$s"', "magazine-basic"), $num, get_search_query()); echo '</h1>';
    ?>
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
    <div class="posts">
		<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__("Permanent Link to %s", "magazine-basic"), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h2>
        <div class="meta">
			<?php if(theme_option('dates_cats')=='on') { echo '<div class="date">'; the_time(get_option('date_format')); echo '</div>'; } ?>
            <?php if(theme_option('authors_cats')=='on') { _e("By", "magazine-basic"); echo ' '; the_author_posts_link(); } ?> 
        </div>
		<div class="storycontent">
			<?php 			
			if(function_exists('has_post_thumbnail') && has_post_thumbnail()) { 
				echo '<a href="'.get_permalink().'">';
				the_post_thumbnail('thumbnail', array('class'=>'alignleft'));
				echo '</a>';
			} else { 
				echo resize(get_option('thumbnail_size_w'),get_option('thumbnail_size_h')); 
			}
			theme_excerpt(55);
			?>
		</div>
        <p class="meta"><?php the_tags(__("Tags: ", "magazine-basic"), ', ', '<br />'); ?> <?php _e("Posted in", "magazine-basic"); echo " "; the_category(', '); ?> | <?php edit_post_link(__("Edit", "magazine-basic"), '', ' | '); ?>  <?php comments_popup_link(__( 'No Comments &#187;', "magazine-basic"), __('1 Comment &#187;', "magazine-basic"), __('% Comments &#187;', "magazine-basic")); ?></p>
    </div>
    
    <?php endwhile; ?>
	<?php if(function_exists('pagination')) { pagination(); } ?>      
    <?php else : ?>
    	<p><?php _e("Sorry, but you are looking for something that isn't here.", "magazine-basic"); ?></p>
    <?php endif; ?>

<?php get_footer(); ?>
