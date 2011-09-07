<?php
/**
 Template Name: Gallery Page
 **/
 
if(!$_GET["ajax"]){	
	get_header(); 	
}else{

	echo get_ajax_header();
}
?>
		<div id="container">
			<div id="content" role="main">
					<?php
					get_template_part( 'loop', 'page' );
					?>
			</div><!-- #content -->
		</div><!-- #container -->

<?php
if(!$_GET["ajax"]){
	get_footer(); 
}
?>
