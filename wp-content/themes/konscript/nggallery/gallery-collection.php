<?php 
/**
Template Page for the gallery overview

Follow variables are useable :

	$gallery     : Contain all about the gallery
	$images      : Contain all images, path, title
	$pagination  : Contain the pagination content

 You can check the content when you insert the tag <?php var_dump($variable) ?>
 If you would like to show the timestamp of the image ,you can use <?php echo $exif['created_timestamp'] ?>
**/
?>
<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><?php if (!empty ($gallery)) : ?>

<div class="ngg-galleryoverview collection" id="<?php echo $gallery->anchor ?>">
<p class="gallery-title">
	<?php echo $gallery->title; ?>	
</p>
	<!-- Thumbnails -->
	<?php foreach ( $images as $image ) : ?>

	<?php
		$NA = '';
		$colors = !$image->ngg_custom_fields["color"] ? $NA : $image->ngg_custom_fields["color"];
		$sizes = !$image->ngg_custom_fields["size"] ? $NA : $image->ngg_custom_fields["size"];	
		$separator = $colors=="" || $sizes == "" ? '' : '|';
				
	?>
	
	<div id="ngg-image-<?php echo $image->pid ?>" class="ngg-gallery-thumbnail-box" <?php echo $image->style ?> >
		<div class="ngg-gallery-thumbnail imageHover" >
			<a href="<?php echo $image->imageURL ?>" title="<?php echo $image->alttext ?>" <?php echo $image->thumbcode ?> >
				<?php if ( !$image->hidden ) { ?>
				<img title="<?php echo $image->alttext ?>" alt="<?php echo $image->alttext ?>" src="<?php echo $image->thumbnailURL ?>" <?php echo $image->size ?> />
				<?php } ?>
			</a>

				<span class="product-title"><?php echo $image->alttext; ?></span>				
				<span class="product-colors"><?php echo $colors; ?></span>
				<span><?php echo $separator; ?></span>
				<span class="product-sizes"><?php echo $sizes; ?></span>
			


		</div>
	</div>
	
	<?php if ( $image->hidden ) continue; ?>
	<?php if ( $gallery->columns > 0 && ++$i % $gallery->columns == 0 ) { ?>
		<br style="clear: both" />
	<?php } ?>

 	<?php endforeach; ?>
 	
	<?php
	//$ajax = $_GET["ajax"]==True ? "ajax=true&" : "";
	//$path = get_current_slug()."?".$ajax."album=".$album->id."&gallery=".$gallery->gid;	
	?> 	
 	
	<!-- Pagination -->
 	<?php echo $pagination ?>
 	
</div>

<?php endif; ?>
