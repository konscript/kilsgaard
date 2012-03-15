<?php
/**
 Template Name: Front Page
 **/
 
if(!$_GET["ajax"]){	
	get_header(); 	
}else{
	echo get_ajax_header();
}
?>
		<div id="container">
			<div id="content" role="main">

			</div><!-- #content -->
		</div><!-- #container -->

<?php
if(!$_GET["ajax"]){
	get_footer(); 
}
?>
