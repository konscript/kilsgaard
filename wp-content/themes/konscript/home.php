<?php
/**
 * Home Template
 *
 * This template is loaded when on the home/blog page.
 */
if(!$_GET["ajax"]){
	get_header(); 
}else{
	echo get_ajax_header($data);
}
?>
	<div id="content" class="hfeed content">	
		
			 <?php get_template_part( 'loop', 'index' ); ?>
				
	</div><!-- .content .hfeed -->

<?php 
if(!$_GET["ajax"]){
	get_footer();
}
?>
