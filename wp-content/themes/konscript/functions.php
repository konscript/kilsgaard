<?php

/* Load the core theme framework. */
require_once( trailingslashit( TEMPLATEPATH ) . 'hybrid-core/hybrid.php' );
$theme = new Hybrid();

require_once (TEMPLATEPATH . '/functions/admin-menu.php');  

/* Theme PHP code will go here. */
add_action( 'after_setup_theme', 'konscript_theme_setup', 10 );


/* theme setup function */
function konscript_theme_setup() {

	/* Theme-supported features go here. */
	add_theme_support( 'hybrid-core-menus', array('primary') );
	add_theme_support( 'hybrid-core-theme-settings' ); // Enables settings page
	add_theme_support( 'post-thumbnails' ); //enable featured images

	/* Action and filters go here. */
}

function get_ajax_header($data = array()){

	$data_default = array(
		"pageClasses"=> custom_body_class(),
		"pageTitle"	=> custom_document_title(),
		"pageImage" => get_option('omr_background_image')		 		
	);
	
	//add featured image to data default
	if ( has_post_thumbnail() ){
		$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), "full" );
		$data_default["pageImage"] = $featured_image[0];
	}

	if(empty($data)){		
		$data = $data_default;
	}else{
		$data = array_merge($data_default, $data);
	}


	return "
	<script type=\"text/javascript\"> 
		var data = ".json_encode($data).";
	</script>";
}


function get_current_slug(){
	global $post;
	return $post->post_name;
}

/**
 * Provides classes for the <body> element depending on page context.
 *
 * @return array
 */
function custom_body_class( $class = '' ) {
	global $wp_query;

	/* Text direction (which direction does the text flow). */
	$classes = array( 'wordpress', get_bloginfo( 'text_direction' ), get_locale() );

	/* Multisite check adds the 'multisite' class and the blog ID. */
	if ( is_multisite() ) {
		$classes[] = 'multisite';
		$classes[] = 'blog-' . get_current_blog_id();
	}

	/* Date classes. */
	$time = time() + ( get_option( 'gmt_offset' ) * 3600 );
	$classes[] = strtolower( gmdate( '\yY \mm \dd \hH l', $time ) );

	/* Is the current user logged in. */
	$classes[] = ( is_user_logged_in() ) ? 'logged-in' : 'logged-out';

	/* WP admin bar. */
	if ( function_exists( 'is_admin_bar_showing' ) && is_admin_bar_showing() )
		$classes[] = 'admin-bar';

	/* Merge base contextual classes with $classes. */
	$classes = array_merge( $classes, hybrid_get_context() );

	/* Singular post (post_type) classes. */
	if ( is_singular() ) {

		/* Checks for custom template. */
		$template = str_replace( array ( "{$wp_query->post->post_type}-template-", "{$wp_query->post->post_type}-", '.php' ), '', get_post_meta( $wp_query->post->ID, "_wp_{$wp_query->post->post_type}_template", true ) );
		if ( !empty( $template ) )
			$classes[] = "{$wp_query->post->post_type}-template-{$template}";

		/* Post format. */
		if ( current_theme_supports( 'post-formats' ) ) {
			$post_format = get_post_format( $wp_query->post->ID );
			$classes[] = ( ( empty( $post_format ) || is_wp_error( $post_format ) ) ? "{$wp_query->post->post_type}-format-standard" : "{$wp_query->post->post_type}-format-{$post_format}" );
		}

		/* Attachment mime types. */
		if ( is_attachment() ) {
			foreach ( explode( '/', get_post_mime_type() ) as $type )
				$classes[] = "attachment-{$type}";
		}
	}

	/* Paged views. */
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 ) {
		$page = intval( $page );
		$classes[] = 'paged paged-' . $page;
	}

	/* Input class. */
	if ( !empty( $class ) ) {
		if ( !is_array( $class ) )
			$class = preg_split( '#\s+#', $class );
		$classes = array_merge( $classes, $class );
	}

	/* Apply the filters for WP's 'body_class'. */
	$classes = apply_filters( 'body_class', $classes, $class );

	/* Join all the classes into one string. */
	$class = join( ' ', $classes );
	
	return $class;
}


/**
 * Function for handling what the browser/search engine title should be. Attempts to handle every 
 * possible situation WordPress throws at it for the best optimization.
 *
 * @since 0.1.0
 * @global $wp_query
 */
function custom_document_title() {
	global $wp_query;

	/* Set up some default variables. */
	$domain = hybrid_get_textdomain();
	$doctitle = '';
	$separator = ':';

	/* If viewing the front page and posts page of the site. */
	if ( is_front_page() && is_home() )
		$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

	/* If viewing the posts page or a singular post. */
	elseif ( is_home() || is_singular() ) {
		$post_id = $wp_query->get_queried_object_id();

		$doctitle = get_post_meta( $post_id, 'Title', true );

		if ( empty( $doctitle ) && is_front_page() )
			$doctitle = get_bloginfo( 'name' ) . $separator . ' ' . get_bloginfo( 'description' );

		elseif ( empty( $doctitle ) )
			$doctitle = get_post_field( 'post_title', $post_id );
	}

	/* If viewing any type of archive page. */
	elseif ( is_archive() ) {

		/* If viewing a taxonomy term archive. */
		if ( is_category() || is_tag() || is_tax() ) {

			if ( function_exists( 'single_term_title' ) ) {
				$doctitle = single_term_title( '', false );
			} else { // 3.0 compat
				$term = $wp_query->get_queried_object();
				$doctitle = $term->name;
			}
		}

		/* If viewing a post type archive. */
		elseif ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() ) {
			$post_type = get_post_type_object( get_query_var( 'post_type' ) );
			$doctitle = $post_type->labels->name;
		}

		/* If viewing an author/user archive. */
		elseif ( is_author() )
			$doctitle = get_the_author_meta( 'display_name', get_query_var( 'author' ) );

		/* If viewing a date-/time-based archive. */
		elseif ( is_date () ) {
			if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'g:i a', $domain ) ) );

			elseif ( get_query_var( 'minute' ) )
				$doctitle = sprintf( __( 'Archive for minute %1$s', $domain ), get_the_time( __( 'i', $domain ) ) );

			elseif ( get_query_var( 'hour' ) )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'g a', $domain ) ) );

			elseif ( is_day() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'F jS, Y', $domain ) ) );
	
			elseif ( get_query_var( 'w' ) )
				$doctitle = sprintf( __( 'Archive for week %1$s of %2$s', $domain ), get_the_time( __( 'W', $domain ) ), get_the_time( __( 'Y', $domain ) ) );

			elseif ( is_month() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), single_month_title( ' ', false) );

			elseif ( is_year() )
				$doctitle = sprintf( __( 'Archive for %1$s', $domain ), get_the_time( __( 'Y', $domain ) ) );
		}
	}

	/* If viewing a search results page. */
	elseif ( is_search() )
		$doctitle = sprintf( __( 'Search results for &quot;%1$s&quot;', $domain ), esc_attr( get_search_query() ) );

	/* If viewing a 404 not found page. */
	elseif ( is_404() )
		$doctitle = __( '404 Not Found', $domain );

	/* If the current page is a paged page. */
	if ( ( ( $page = $wp_query->get( 'paged' ) ) || ( $page = $wp_query->get( 'page' ) ) ) && $page > 1 )
		$doctitle = sprintf( __( '%1$s Page %2$s', $domain ), $doctitle . $separator, number_format_i18n( $page ) );

	/* Apply the wp_title filters so we're compatible with plugins. */
	$doctitle = apply_filters( 'wp_title', $doctitle, $separator, '' );

	/* Print the title to the screen. */
	return apply_atomic( 'document_title', esc_attr( $doctitle ) );
}

// Password Protected Page Message
function custom_password_form($form) {
  $subs = array(
    '#<p>This post is password protected. To view it please enter your password below:</p>#' => '<p>This section can only be viewed by people with a valid password - contact Kilsgaard Eyewear to request access  </p>'
  );

  echo preg_replace(array_keys($subs), array_values($subs), $form);
}

add_filter('the_password_form', 'custom_password_form');

require_once (TEMPLATEPATH . '/functions/twentyten.php');  
?>
