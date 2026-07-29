<?php
require_once __DIR__ . '/flickr-photo.php';

/** Number of slider photos to load immediately (visible on most breakpoints). */
$flickr_slider_eager_count = 4;
?>
<div uk-slider="autoplay: true">
	<div class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-light uk-grid uk-grid-small">
		<?php
		foreach ( $photos as $index => $photo ) {
			tca_flickr_render_photo( $photo, $index >= $flickr_slider_eager_count );
		}
		?>
	</div>

	<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous" aria-label="<?php echo esc_attr__( 'Previous photos', 'tca' ); ?>"></a>
	<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next" aria-label="<?php echo esc_attr__( 'Next photos', 'tca' ); ?>"></a>

	<ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
</div>
