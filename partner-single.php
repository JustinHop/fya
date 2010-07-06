<?php
/*
 * Template Name: Partner Single
 *
 * This will be the front landing page.
 *
 * @package Hybrid
 * @subpackage Template
 */
#add_filter('pre_get_posts', 'partner_filter');
get_header(); ?>
    
<!-- PARTNER SINGLE CONTENT -->

    <div id="content-wrapper">
    <div id="content" <?php post_class(); ?>>
          
          <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <?php $custom = get_post_custom(); ?>
            <div class="partnerhead">
                <span class="imagepad200">
                <?php the_post_thumbnail('single-post-thumbnail'); ?>
                </span>
                <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to ','emerald_stretch'); ?><?php the_title(); ?>"><?php the_title(); ?></a></h1>
            <div class="partnerrow">
                <?php if ( $custom['fya_partner_address'][0] ) { ?>
                    <div class="partnercell"><?php echo apply_filters( 'the_content',  $custom['fya_partner_address'][0] ); ?>
                    <?php if ( $custom['fya_partner_phonenumber'][0] ) { ?>
                        <?php echo apply_filters( 'the_content', 'Phone: ' . $custom['fya_partner_phonenumber'][0] ); ?>
                    <?php } ?>
                    </div>
                <?php } ?>
                <?php if (  $custom['fya_partner_contact'][0] ) { ?>
                    <div class="partnercell">
                        <?php echo apply_filters( 'the_content', "Primary Contact:\n" . $custom['fya_partner_contact'][0] ); ?>
                        <?php echo apply_filters( 'the_content', "Phone: " . $custom['fya_partner_contact_phone'][0] ); ?>
                        <?php if ( $custom['fya_partner_contact_phone'] ) echo apply_filters( 'the_content', "Email: <a href='mailto:" . $custom['fya_partner_contact_email'][0] . "'>" . $custom['fya_partner_contact_email'][0] . '</a>'  ); ?>
                    </div>
                <?php } ?>
                <?php if (  $custom['fya_partner_contact2'][0] ) { ?>
                    <div class="partnercell">
                        <?php echo apply_filters( 'the_content', "Secondary Contact:\n" . $custom['fya_partner_contact2'][0] ); ?>
                        <?php echo apply_filters( 'the_content', "Phone: " . $custom['fya_partner_contact2_phone'][0] ); ?>
                        <?php if ( $custom['fya_partner_contact2_phone'] ) echo apply_filters( 'the_content', "Email: <a href='mailto:" . $custom['fya_partner_contact2_email'][0] . "'>" . $custom['fya_partner_contact2_email'][0] . '</a>'  ); ?>
                    </div>
                <?php } ?>
                <?php if (  $custom['fya_partner_contact3'][0] ) { ?>
                    <div class="partnercell">
                        <?php echo apply_filters( 'the_content', "Tertiary Contact:\n" . $custom['fya_partner_contact3'][0] ); ?>
                        <?php echo apply_filters( 'the_content', "Phone: " . $custom['fya_partner_contact3_phone'][0] ); ?>
                        <?php if ( $custom['fya_partner_contact3_phone'] ) echo apply_filters( 'the_content', "Email: <a href='mailto:" . $custom['fya_partner_contact3_email'][0] . "'>" . $custom['fya_partner_contact3_email'][0] . '</a>'  ); ?>
                    </div>
                <?php } ?>
            </div>
            </div>

            <div class="partnercontent">
                <?php $des =  get_the_block('Description');
                if ( $des ) { ?>
                    <div class="partnerblock partnerdescription"><?php echo $des; ?></div>
                <?php } ?>
                <?php $mis = get_the_block('Mission');
                if ($mis ) { ?>
                    <div class="partnerlabel">Mission</div>
                    <div class="partnerblock"><?php echo $mis; ?></div>
                <?php } ?>
                <?php $act =  get_the_block('Activities');
                if ( $act ) { ?>
                    <div class="partnerlabel">Activities</div>
                    <div class="partnerblock"><?php echo $act; ?></div>
                <?php } ?>
                <?php the_content(__('Read the rest of this post &raquo;','emerald_stretch')); ?>
            </div>

                
    <br />
             <ul class="postnav">
                 <li class="left"> 
                 <?php previous_post_link('%link', '<span>' . (__('&laquo', 'default')) . '</span> %title'); ?>
                 </li>
                 <li class="right"> 
                 <?php next_post_link('%link', '%title <span>' . (__('&raquo', 'default')) . '</span> '); ?>
                 </li>
             </ul>
    <br />
                <span class="editlink"><?php edit_post_link(__('Edit','emerald_stretch'),'',''); ?></span>

           <?php endwhile; else: ?>
              <h1><?php _e('Sorry, no posts matched your criteria', 'emerald_stretch'); ?></h1>
            <p><a href="<?php echo get_settings('home'); ?>"><?php _e('Visit the homepage &raquo;', 'emerald_stretch'); ?></a></p>
          <?php endif; ?>

    </div>
<script type="text/Javascript">
  jQuery(document).ready(function() {
  	  jQuery('.partnercell p:first-child').addClass('firstline');
  });
</script>
<!-- /CONTENT -->
<?php get_sidebar();?>    
<?php get_footer ();?>
