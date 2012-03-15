<?php

// create custom plugin settings menu  
add_action('admin_menu', 'konscript_menu');  
  
function konscript_menu() {  
  
    //create new top-level menu  
    add_submenu_page('themes.php','Default background image', 'Background image', 'administrator', __FILE__, 'omr_settings_page');  
  
    //call register settings function  
    add_action( 'admin_init', 'register_mysettings' );  
}  

function register_mysettings() {  
    //register our settings  
    register_setting( 'omr-settings-group', 'omr_background_image' );  
}

// prepare gallery picker
function wp_gear_manager_admin_scripts() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
}

// gallery picker style
function wp_gear_manager_admin_styles() {
	wp_enqueue_style('thickbox');
}

add_action('admin_print_scripts', 'wp_gear_manager_admin_scripts');
add_action('admin_print_styles', 'wp_gear_manager_admin_styles');

function omr_settings_page() {  
?>  

<script language="JavaScript">
jQuery(document).ready(function() {
	jQuery('#upload_image_button').click(function() {
		formfield = jQuery('#upload_image').attr('name');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src');
		jQuery('#upload_image').val(imgurl);
		tb_remove();
	}

});
</script>

<div class="wrap">  
<h2>Background image</h2>  
  
<form method="post" action="options.php">  
  
    <?php settings_fields('omr-settings-group'); ?>  
<label for="upload_image">
		<input id="upload_image" type="text" size="80" name="omr_background_image" value="<?php echo get_option('omr_background_image'); ?>" />
		<input id="upload_image_button" type="button" value="Select image" />
		</label>
    <p class="submit">  
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />  
    </p>  

<?php
$currentBg = get_option('omr_background_image');
if($currentBg){
	echo '<img height="400" src="'.$currentBg.'">';
}
?>
  
</form>  
</div>  
<?php } 
