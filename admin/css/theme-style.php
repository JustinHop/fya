<?php
ob_start();
	global $post;
	$values = get_option(THEME_FILE);
	$height = 28;
	if($values['site_width']) {
		$site = $values['site_width'];
		$sidebar = $values['sidebar_width1'];	
		$secondsidebar =  $values['sidebar_width2'];
		$sidewidget = $sidebar - 20;
		$sidewidget2 = $secondsidebar - 20;
		if($secondsidebar==0) {
			$content =  $site - $sidebar - 65;
		} else {
			$content =  $site - $sidebar - $secondsidebar - 88;		
		}
	} else {
		$site = 800;
		$sidebar = 180;
		$sidewidget = 160;
		$content = 560;
	}
?>
body { width: <?php echo $site; ?>px; <?php //echo 'background: url('.get_bloginfo('template_url').'/images/'.$bg.') repeat-y center;' ?> }
#mainwrapper { width: <?php echo $site-20; ?>px; }
#sidebar { width: <?php echo $sidebar; ?>px; }
#sidebar .side-widget { width: <?php echo $sidewidget; ?>px; }
#secondsidebar { width: <?php echo $secondsidebar; ?>px; }
#secondsidebar .side-widget { width: <?php echo $sidewidget2; ?>px; }
#leftcontent, #twocol, #threecol, #threecol2, .commentlist { width: <?php echo $content; ?>px; }
#leftcontent img { max-width: <?php echo $content; ?>px; height: auto; }
<?php
$page = ob_get_contents();
ob_end_flush();
$path = dirname(__FILE__);
$filename = fopen($path."/theme-style.css","w");
fwrite($filename,$page);
fclose($filename);
?>