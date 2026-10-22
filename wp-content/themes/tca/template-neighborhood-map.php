<?php
/**
 * Template Name: Neighborhood Guide Map (Overlay)
 *
 * Alternate neighborhood map. Does not replace the live Neighborhood Guide.
 *
 * @package tca
 */

get_header();
?>

<main id="primary" class="site-main neighborhood-overlay-map-page">
	<div class="uk-container">
		<?php
		while ( have_posts() ) :
			the_post();
			if ( get_the_title() ) {
				echo '<h1 class="tca-ng-overlay-map-title">' . esc_html( get_the_title() ) . '</h1>';
			}
			if ( get_the_content() ) {
				echo '<div class="tca-ng-overlay-map-intro">';
				the_content();
				echo '</div>';
			}
		endwhile;
		?>

		<div class="tca-ng-overlay-map" data-tca-ng-overlay-map>
			<div id="tca-ng-overlay-map-canvas" class="tca-ng-overlay-map__canvas" role="presentation"></div>
			<button type="button" class="tca-ng-overlay-map__scrim" hidden tabindex="-1" aria-label="<?php echo esc_attr__( 'Close neighborhood popup', 'tca' ); ?>"></button>
			<aside class="tca-ng-overlay-map__panel" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tca-ng-overlay-heading">
				<button type="button" class="tca-ng-overlay-map__close" aria-label="<?php echo esc_attr__( 'Close', 'tca' ); ?>">&times;</button>
				<div class="tca-ng-overlay-map__image-wrap tca-ng-overlay-map__image-wrap--empty">
					<img src="" alt="" width="960" height="540" decoding="async" hidden />
				</div>
				<h2 class="tca-ng-overlay-map__heading" id="tca-ng-overlay-heading"></h2>
				<p class="tca-ng-overlay-map__description"></p>
			</aside>
		</div>
	</div>
</main>

<?php
get_footer();
