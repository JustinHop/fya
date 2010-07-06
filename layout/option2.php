<!-- Option Two -->
<?php while (have_posts()) : the_post(); ?>
<?php if($x == 1) { ?>
<div class="ind-post">
	<h1><a href="<?php the_permalink() ?>" title="<?php printf(__("Permanent Link to %s", "magazine-basic"), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h1>
	<div class="meta">
		<?php if(theme_option('dates_index')=='on') { echo '<div class="date">'; the_time(get_option('date_format')); echo '</div>'; } ?>
		 <?php if(theme_option('authors_index')=='on') { _e("By", "magazine-basic"); echo ' '; the_author_posts_link(); } ?>
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
			if(theme_option('excerpt_content')=='2') { 
				theme_content(__('Read more &raquo;', "magazine-basic"));
			} else {
				theme_excerpt(theme_option('excerpt_one'));
			}	
		?>
	</div>
</div>
<?php $x++; ?>
<?php } else {
	if($x==2) { echo '<div id="twocol">'; $i=1; } ?>
	<div class="twopost twopost<?php if($i==5) { $i = 3; } echo $i; $i++; ?>">
		<h1><a href="<?php the_permalink() ?>" title="<?php printf(__("Permanent Link to %s", "magazine-basic"), the_title_attribute('echo=0')); ?>"><?php the_title(); ?></a></h1>
		<div class="meta">
		<?php if(theme_option('dates_index')=='on') { echo '<div class="date">'; the_time(get_option('date_format')); echo '</div>'; } ?>
		 <?php if(theme_option('authors_index')=='on') { _e("By", "magazine-basic"); echo ' '; the_author_posts_link(); } ?> 
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
			if(theme_option('excerpt_content')=='2') { 
				theme_content(__('Read more &raquo;', "magazine-basic"));
			} else {
				theme_excerpt(theme_option('excerpt_two'));
			}	
			?>
		</div>
	 </div>
<?php $x++; } ?>
<?php endwhile; ?>
</div>
