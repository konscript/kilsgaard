<?php
/**
 * Header Template
 *
 * Master template for head-section and body opening with generic elements
 */
?>
LALAA TEST FRA sqren
<!DOCTYPE HTML>
<html>
	<head>	
		<base href="<?php echo get_bloginfo( 'wpurl' ); ?>/" />
		<title>Kilsgaard Eyewear</title>
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo('charset'); ?>" />
		
		<link rel="shortcut icon" type="image/ico" href="<?php echo THEME_URI .'/img/favicon.ico'; ?>" />
		<link href='http://fonts.googleapis.com/css?family=Arimo:regular,italic,bold,bolditalic&v1' rel='stylesheet' type='text/css'>
		<link type="text/css" href="<?php echo THEME_URI . '/css/screen.css'; ?>" rel="stylesheet" media="screen" />
		<link type="text/css" href="<?php echo THEME_URI . '/css/print.css'; ?>" rel="stylesheet" media="print" />
		<link rel="stylesheet" href="<?php echo THEME_URI; ?>/css/shadowbox.css" type="text/css" />		
		<!--
		<link type="text/css" href="<?php echo THEME_URI . '/hybrid-core/css/drop-downs.css'; ?>" rel="stylesheet" media="screen" />		
		-->				
		<script src="<?php echo THEME_URI . '/js/jquery-1.6.1.min.js'; ?>"></script> 
		<script src="<?php echo THEME_URI . '/js/jquery.backstretch.js'; ?>"></script> 
		<script src="<?php echo THEME_URI . '/js/jquery.address-1.4.min.js'; ?>"></script> 
		<script src="<?php echo THEME_URI . '/js/jquery.cookie.js'; ?>"></script> 			
		<script src="<?php echo THEME_URI . '/hybrid-core/js/drop-downs.js'; ?>"></script>
		<script src="<?php echo THEME_URI . '/js/jquery.form.js'; ?>"></script>			
		<script src="<?php echo THEME_URI . '/js/autocolumn.min.js'; ?>"></script> 
		<script src="<?php echo THEME_URI; ?>/js/shadowbox.js" type="text/javascript"></script>
		<script src="<?php echo THEME_URI . '/js/global.js'; ?>"></script> 
		<?php wp_head(); // WP head hook ?>
						
	</head>


	 <body class="<?php hybrid_body_class(); ?>"> 	
	
	<div id="wrapper">	
		
			<div id="header">
						
				<div id="site-title">
					<a href="<?php echo home_url(); ?>" class="logo_image index" ></a>
				</div>	
				
				<div class="menu">
				
				<?php wp_nav_menu(array(
					'theme_location'=> 'primary',
					'container' 	=> false,
					'menu_class' 	=> 'primary-menu custom-color-2',
					'menu_id' 		=> 'primary-menu',
					'fallback_cb' 	=> false
					)); ?>											
				</div>															
			</div><!-- #header -->
		
		<div id="main">
