<?php
/**
 * Shared Flickr photo markup with optional deferred loading.
 *
 * @param object $photo  Flickr API photo object.
 * @param bool   $defer  When true, image loads when near the viewport.
 */
function tca_flickr_render_photo( $photo, $defer = false ) {
	$photo_url = sprintf(
		'https://live.staticflickr.com/%s/%s_%s_b.jpg',
		$photo->server,
		$photo->id,
		$photo->secret
	);
	$alt = isset( $photo->title ) ? $photo->title : '';
	?>
	<div class="flickr-photo">
		<?php if ( $defer ) : ?>
			<img
				class="flickr-photo__img--deferred"
				data-src="<?php echo esc_url( $photo_url ); ?>"
				alt="<?php echo esc_attr( $alt ); ?>"
				width="400"
				height="400"
				decoding="async"
			/>
		<?php else : ?>
			<img
				src="<?php echo esc_url( $photo_url ); ?>"
				alt="<?php echo esc_attr( $alt ); ?>"
				width="400"
				height="400"
				loading="lazy"
				decoding="async"
			/>
		<?php endif; ?>
	</div>
	<?php
}
