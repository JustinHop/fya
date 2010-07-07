<?php
/**
 * Template Name: Newsletter
 *
 * This displays a newsletter
 *
 */
?>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <title>FYA Bulletin - <?php the_title(); ?></title>
   <style type="text/css" media="screen">
      body {
      	background-color: #998877; /* tan */
      	margin: 0;
      	padding: 0;
      }

      a {
      	  text-decoration: none;
      }

      p a {
      	color: #005566; /* blue */
      	text-decoration: none;
      }

      td.permission {
      	padding: 8px 0 8px 0;
      }

      td.date {
      	padding: 0 0 4px 0;
      }

      td.permission p {
      	font-family: Georgia;
      	font-size: 11px;
      	font-weight: normal;
      	font-style: italic;
      	color: #151515;
      	text-align: center;
      }

      td.permission a {
      	color: #005566; /* blue */
      	text-decoration: none;
      }

      table.bgTop {
      	background-color: #005566; /* brown */
      }

      table.bg {
      	background-color: #ffffff;
      }

      table.header td {
      	height: 108px;
      }

      table.header td h1 {
      	font-family: 'Futura', Arial;
      	font-size: 26px;
      	font-weight: 900;
      	color: #998877; /* Blue */
      	display: inline;
      	margin: 0 0 0 6px;
      	padding: 0;
      	/*text-transform: uppercase;*/
      }

      table.header td h3 {
      	font-family: Georgia, Arial;
      	font-size: 12px;
      	font-weight: normal;
      	font-style: italic;
      	color: #ffffff;
      	display: inline;
      	margin: 0;
      	padding: 0 0 6px 0;
      }

      td.sidebar {
      	background-color: #f8f8f8;
      	border-right: 1px solid #bdbcb8;
      }

      td.sidebar h3 {
      	font-family: Georgia;
      	font-size: 11px;
      	font-weight: normal;
      	color: #666655; /* olive */
      	margin: 0 0 10px 0;
      	border-bottom: 3px solid #998877; /* tan */
      	text-transform: uppercase;
      }
      td.sidebar h2 {
      	font-family: Georgia;
      	font-size: 16px;
      	font-weight: normal;
      	color: #666655; /* olive */
      	margin: 0 0 10px 0;
      	/*text-transform: uppercase;*/
      }

      td.sidebar ul {
      	margin: 0 0 20px 24px;
      	padding: 0;
      	font-family: Georgia;
      	font-size: 12px;
      	font-weight: normal;
      	color: #313131;
      }

      td.sidebar ul a {
      	font-family: Georgia;
      	font-size: 12px;
      	font-weight: normal;
      	color: #005566; /* blue */
      }

      td.sidebar p {
      	font-family: Georgia;
      	font-size: 12px;
      	font-weight: normal;
      	color: #313131;
      	margin: 0 0 14px 0;
      	padding: 0;
      }

      td.sidespace {
         padding: 20px;
      }

      td.sidespace h2 a {
      	  color: #881100; /* red */
      }

      table.options {
      	border-top: 1px solid #9f9862;
      	border-bottom: 1px solid #9f9862;
      }

      table.options td {
      	padding: 16px 22px 16px 22px;
      }

      td.border {
      	border-bottom: 1px solid #998877; /* tan */
      }

      table.options h3 {
      	font-family: Georgia;
      	font-size: 16px;
      	font-weight: normal;
      	color: #9f9862;
      	margin: 0 0 4px 0;
      	padding: 0;
      }

      table.options p {
      	font-family: Georgia;
      	font-size: 13px;
      	font-weight: normal;
      	color: #313131;
      	margin: 0;
      	padding: 0;
      }

      table.options p a {
      	color: #313131;
      	text-decoration: none;
      	border: none;
      	margin: 0;
      	padding: 0;
      }

      td.mainbar {
         padding: 20px;
      }

      td.mainbar h2 {
      	font-family: Georgia;
      	font-size: 20px;
      	font-weight: bold;
      	color: #9f9862;
      	margin: 0 0 10px 0;
      	padding: 0;
      }

      td.mainbar h2 a {
      	font-family: Georgia;
      	font-size: 20px;
      	font-weight: bold;
      	color: #881100; /* red */
         text-decoration: none;
      }

      td.mainbar h3 {
      	font-family: Verdana;
      	font-size: 11px;
      	font-weight: normal;
      	color: #998877; /* tan */
      	border-bottom: 3px solid #998877; /* tan */
      	text-transform: uppercase;
      	margin: 0 0 8px 0;
      	padding: 0;
      }

      td.mainbar p {
      	font-family: Georgia;
      	font-size: 12px;
      	font-weight: normal;
      	color: #333333;
      	margin: 0 0 16px 0;
      	padding: 0;
      }

      td.mainbar p.top {
      	font-family: Verdana;
      	font-size: 10px;
      	font-weight: normal;
      	color: #44a0df;
      	text-transform: uppercase;
      	width: 100%;
      	text-align: right;
      	margin: 14px 0 4px 0;
      }

      table.footer {
      	font-family: Georgia;
      	font-size: 11px;
      	font-weight: normal;
      	color: #999999;
      	text-align: center;
      	/*border: 1px solid #575757;*/
      }

      table.footer td {
      	height: 88px;
      }

      table.footer span {
      	color: #ffffff;
      }

      table.bgBottom {
         padding: 10px;
      	background-color: #005566;
      }
   </style>

</head>
<body>
<a name="top" id="top"></a>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
      <td align="center">
         
         <?php // THIS IS THE HEADER ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

         <table width="579" border="0" cellspacing="0" cellpadding="0">
            <tr>
               <td class="permission">
                  <p>You're receiving this newsletter because you signed up.</p>
                  <p>Not interested anymore? <unsubscribe>Unsubscribe</unsubscribe>. Having trouble viewing this email? <a href="<?php the_permalink(); ?>">View it in your browser</a>.</p>
               </td>
            </tr>
         </table>
         

         <table width="579" border="0" cellspacing="0" cellpadding="0" class="bgTop">
            <tr>
               <td align="center">
                  
                  <table width="579" height="108" border="0" cellspacing="0" cellpadding="0" class="header">
                     <tr>
                     <td><center><h1>The Alameda County Foster Youth Alliance</h1><br><br>
                            <h3><?php the_title(); ?></h3></center></td>
                     </tr>
                  </table>
                  
               </td>
            </tr>
         </table>
         
         <table width="579" border="0" cellspacing="0" cellpadding="0" class="bg">
            <tr>
               <td width="325" align="center" valign="top">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                        <td align="left" class="mainbar">
                           
                           <?php if ( false ) : ?>
                           <h3>Feature</h3>
                           
                           <h2>Lorem ipsum dolor sit amet conse ct etuer adipiscing elit.</h2>
                           <p><img src="<?php bloginfo('template_directory'); ?>/images/main-content-inline-small-1.jpg" width="160" height="151" align="right" hspace="10"> Donec imperdiet, nibh sit amet pharetra placerat, tortor purus condimentum lectus, at dignissim nibh velit vitae sem. Nunc <a href="#">condimen-tum blandit</a> tortorphasellus facilisis neque vitae purus. Aliquam facilisis nisl in nisi. Ut ultricies massa eget est. Donec eget orci eget urna aliquam egestas. Nulla vitae felis. <a href="#">Maecenas bibendum</a>, nunc eu aliquet ultricies, <a href="#">Read More</a>.</p>
                           
                           <p class="top"><a href="#top">Back to top</a></p>
                           <img src="<?php bloginfo('template_directory'); ?>/images/hr.gif" width="326" height="19">
                           
                           <h3>Fashion</h3>
                           
                           <h2>Aliquam lectus orci, adipiscing</h2>
                           <p>Aliquam facilisis nisl in nisi. Ut ultricies massa eget est. Donec eget orci eget urna aliquam. <a href="#">Read More</a></p>
                           
                           <h2>Sellus facilisis neque</h2>
                           <p>Donec imperdiet, nibh sit amet pharetra placerat, tortor purus condimentum lectus, at dignissim nibh velit vitae sem. Aliquam facilisis nisl in nisi. <a href="#">Read More</a></p>
                           
                           <h2>Nunc a purus eu sapien-cinia</h2>
                           <p>Aliquam facilisis nisl in nisi. Ut ultricies massa eget est. Donec eget orci eget urna aliquam egestas. <a href="#">Read More</a></p>
                           
                           <p class="top"><a href="#top">Back to top</a></p>
                           <img src="<?php bloginfo('template_directory'); ?>/images/hr.gif" width="326" height="19">
                           
                           <h3>Culture</h3>
                           
                           <h2>Fermentum quam—donec imperde lorem ipsum dolar</h2>
                           <p><img src="<?php bloginfo('template_directory'); ?>/images/main-content-inline-small-2.jpg" width="160" height="151" align="right" hspace="10">Cras purus. Nunc rhoncus. Pellentesque semper. Donec imperdiet accumsan felis. Proin eget mi. Sed at est. Nunc a purus eu sapien laci-nia fermentum. Donec iaculis Sed at est. <a href="#">Nunc a purus eu sapien-lacinia fermentum</a>. Donec iaculis fermentum quam. Donec imperdiet, nibh sit amet pharetra placerat, tortor purus condimentum lectus. <a href="#">Read More</a></p>
                           
                           <h2>Pharetra Placerat</h2>
                           <p>Donec eget orci eget urna aliquam egestas. Nulla vitae felis. <a href="#">Maecenas bibendum</a>, nunc eu aliquet ultricies, massa massa aliquet est, nec dignissim nisl ante eget lectus. <a href="#">Read More</a></p>
                           
                           <?php else: ?>
                            <?php 
                                $menu_id = get_post_meta(get_the_ID(), 'menu_id', true); 
                                if ( $menu_id ) {
                                    newsletter_parts($menu_id, 'main');
                                }
                            ?>
                            <?php endif; ?>

                           <p class="top"><a href="#top">Back to top</a></p>
                           <img src="<?php bloginfo('template_directory'); ?>/images/hr.gif" width="326" height="19">

                        </td>
                     </tr>
                  </table>
                  
               </td>
               
               <td width="254" align="center" valign="top" class="sidebar">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                        <td align="left" class="sidespace">

                           <?php // TABLE OF CONTENTS LOOP ?>
                           <h3>In this issue</h3>
                           <ul>
                           <?php if (false) : ?>
                              <li><a href="#">Lorem ipsum dolar sit amet conse</a></li>
                              <li><a href="#">Aliquam lectus orci, adip?</a></li>
                              <li><a href="#">Fermentum quam—donec imperde lorem ipsum</a></li>
                              <?php else : ?>
                            <?php
                                if ( $menu_id ) {
                                    newsletter_parts($menu_id, 'toc');
                                }
                            ?>
                            <?php endif; ?>
                           </ul>
                           
                           <?php // SIDE NOTES BAR ?>
                           <?php if (false) : ?>
                           <h2>In short</h2>
                           <p><strong>Lorem ipsum dolor sit</strong> consectetuer adipiscing elit. Morbi commodo, ipsum sed pharetra gravida, orci magna rhoncus neque, id <a href="#">pulvinar odio</a> lorem non turpis. Nullam sit amet enim.</p>
                           
                           <p><strong>Aliquam facilisis misl in</strong> pulvinar odio lorem non turpis. Nullam sit amet enim. Suspendisse id velit vitae ligula volutpat condimentum. <a href="#">Aliquam erat volutpat</a>. fermentum bibendum enim nibh blandit sed, blandit a, eros.</p>
                           
                           <p><strong>Sed quis velit.</strong> Nulla facilisi. Nulla libero. Vivamus pharetra posuere sapien. Nam consectetuer. Sed aliquam, nunc eget euismod ullamcorper, lectus nunc ullamcorper orci.</p>
                           <?php else: ?>

                            <?php
                                if ( $menu_id ) {
                                    newsletter_parts($menu_id, 'side');
                                }
                            ?>
                           <?php endif; ?>
                           <table width="100%" height="173" border="0" cellspacing="0" cellpadding="0" class="options">
                              <tr>
                                 <td align="center" class="border" valign="top">
                                    <h3>UNSUBSCRIBE</h3>
                                    <p><unsubscribe>Click to instantly unsubscribe from this email</unsubscribe></p>
                                 </td>
                              </tr>
                              <tr>
                                 <td align="center" valign="top">
                                    <h3>FORWARD</h3>
                                    <p><forwardtoafriend>Click to forward this email to a friend</forwardtoafriend></p>
                                 </td>
                              </tr>
                           </table>
                           
                           
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
         </table>

	<?php endwhile; else: ?>
		<p><?php _e("Sorry, no posts matched your criteria.", "magazine-basic"); ?></p>
<?php endif; ?>
         
         <table width="579" height="108" border="0" cellspacing="0" cellpadding="0" class="bgBottom">
            <tr>
               <td align="center" valign="middle">
                  
                  <table width="559" height="88" border="0" cellspacing="0" cellpadding="0" class="footer">
                     <tr>
                        <td align="center">The lawyers will fill this part in for <span>The Alameda County Foster Youth Alliance</span>.<br />The Alameda County Foster Youth Alliance - 123 Some Street, San Francisco, CA 99999. ph +1 4 1477 89 745</td>
                     </tr>
                  </table>
                  
               </td>
            </tr>
         </table>
         
      </td>
   </tr>
</table>

</body>
</html>
