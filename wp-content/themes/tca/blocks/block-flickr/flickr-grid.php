<?php
/**
 * Paginated Flickr photo grid.
 *
 * Expects $photos, $flickr_grid_columns, and $flickr_grid_rows from render.php.
 */

require_once __DIR__ . '/flickr-photo.php';

$grid_cols = isset( $flickr_grid_columns ) ? (int) $flickr_grid_columns : 4;
$grid_rows = isset( $flickr_grid_rows ) ? (int) $flickr_grid_rows : 2;
$grid_cols = max( 2, min( 6, $grid_cols ) );
$grid_rows = max( 2, min( 4, $grid_rows ) );
$per_page  = $grid_cols * $grid_rows;
$pages     = array_chunk( $photos, $per_page );
$multi     = count( $pages ) > 1;

$grid_style = sprintf(
	'--flickr-grid-cols:%d;--flickr-grid-rows:%d;',
	$grid_cols,
	$grid_rows
);
?>
<div class="flickr-grid" style="<?php echo esc_attr( $grid_style ); ?>">
	<?php if ( $multi ) : ?>
		<div class="flickr-grid__slider" uk-slider>
			<div class="uk-position-relative uk-visible-toggle" tabindex="-1">
				<ul class="uk-slider-items uk-child-width-1-1">
					<?php foreach ( $pages as $page_index => $page ) : ?>
						<li class="flickr-grid__page uk-width-1-1">
							<div class="flickr-grid__cells">
								<?php
								foreach ( $page as $photo ) {
									tca_flickr_render_photo( $photo, $page_index > 0 );
								}
								?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous" aria-label="<?php echo esc_attr__( 'Previous photos', 'tca' ); ?>"></a>
				<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next" aria-label="<?php echo esc_attr__( 'Next photos', 'tca' ); ?>"></a>
			</div>
			<ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin flickr-grid__dots"></ul>
		</div>
	<?php else : ?>
		<div class="flickr-grid__page">
			<div class="flickr-grid__cells">
				<?php
				foreach ( $pages[0] as $photo ) {
					tca_flickr_render_photo( $photo, false );
				}
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
