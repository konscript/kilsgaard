<?php 
if(!$_GET["ajax"]){
	get_header(); 
}else{
	echo get_ajax_header($data);
}

?>

		<div id="container">
			<div id="content" role="main">

			<?php
			/* Run the loop to output the page.
			 * If you want to overload this in a child theme then include a file
			 * called loop-page.php and that will be used instead.
			 */
			get_template_part( 'loop', 'page' );
			?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php 
if(!$_GET["ajax"]){
	get_footer(); 
}
?>
