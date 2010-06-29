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
<?php $x++; ?>
<?php } else { ?>
<?php if($x == 2) { $i=1; ?></div><div id="threecol"><div id="threecol2"><?php } ?>
	<div class="threepost threepost<?php if($i==7) { $i = 4; } echo $i; $i++; ?>">
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
                    theme_excerpt(theme_option('excerpt_three'));
                }		
            ?>
		</div>
	 </div>
<?php $x++; } ?>
<?php endwhile; ?>
<?php if($x>4) { echo "</div>"; } ?>
</div>