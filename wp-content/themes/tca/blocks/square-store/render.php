<?php
if ( render_block_preview_if_applicable( $block ) ) {
	return;
}

$container_settings = get_field( 'container_settings' );
$section_header     = get_field( 'section_header' );
include __DIR__ . '/../../inc/common_block_variables.php';

$column_count_desktop = get_field( 'column_count_desktop' ) ? (int) get_field( 'column_count_desktop' ) : 3;
$buy_button_style     = get_field( 'buy_button_style' ) ? get_field( 'buy_button_style' ) : 'blue';

$column_count_desktop = max( 2, min( 4, $column_count_desktop ) );
$container_class      = 'column-count-' . $column_count_desktop;

$allowed_button_styles = array( 'blue', 'white', 'dark', 'green' );
if ( ! in_array( $buy_button_style, $allowed_button_styles, true ) ) {
	$buy_button_style = 'blue';
}

openSection(
	$wrap_size,
	$container_size,
	$anchor,
	$class_name,
	$container_type,
	$background_color,
	$background_image,
	$text_color,
	$disable_animation,
	$vertical_pad_top,
	$vertical_pad_bottom
);

drawSectionHeader( $section_title_size, $section_title, $title_alignment, $show_underline, $section_intro, $section_button, $section_button_style );
?>

<div class="square-store" data-square-store>
	<?php if ( have_rows( 'store_items' ) ) : ?>
		<div class="square-store__grid <?php echo esc_attr( $container_class ); ?>">
			<?php
			while ( have_rows( 'store_items' ) ) :
				the_row();

				$item_image    = get_sub_field( 'item_image' );
				$item_title    = get_sub_field( 'item_title' );
				$item_price    = get_sub_field( 'item_price' );
				$item_desc     = get_sub_field( 'item_description' );
				$item_gallery  = get_sub_field( 'item_gallery' );
				$buy_now_url   = get_sub_field( 'buy_now_url' );
				$button_label  = get_sub_field( 'buy_button_label' );
				$button_label  = $button_label ? $button_label : 'Buy Now';

				$unique_images = array();
				if ( ! empty( $item_image['id'] ) ) {
					$unique_images[ (int) $item_image['id'] ] = $item_image;
				}
				if ( ! empty( $item_gallery ) && is_array( $item_gallery ) ) {
					foreach ( $item_gallery as $gallery_image ) {
						if ( ! empty( $gallery_image['id'] ) ) {
							$unique_images[ (int) $gallery_image['id'] ] = $gallery_image;
						}
					}
				}
				$unique_images = array_values( $unique_images );

				if ( ! empty( $unique_images ) ) {
					$item_image = $unique_images[0];
				}

				$use_flip             = 2 === count( $unique_images );
				$flip_image           = $use_flip ? $unique_images[1] : null;
				$extra_gallery_images = $use_flip ? array() : array_slice( $unique_images, 1 );

				if ( empty( $item_title ) && empty( $item_image ) && empty( $item_gallery ) ) {
					continue;
				}
				?>
				<article class="square-store__item">
					<?php if ( ! empty( $item_image ) || ! empty( $item_gallery ) ) : ?>
						<div class="square-store__media"<?php echo $use_flip ? '' : ' uk-lightbox="animation: slide"'; ?>>
							<?php if ( $use_flip && $flip_image ) : ?>
								<?php
								$main_alt  = ! empty( $item_image['alt'] ) ? $item_image['alt'] : $item_title;
								$flip_alt  = ! empty( $flip_image['alt'] ) ? $flip_image['alt'] : $item_title;
								$flip_hint = $item_title
									? sprintf(
										/* translators: %s: product title */
										__( '%s — hover to see alternate image', 'tca' ),
										$item_title
									)
									: __( 'Hover to see alternate product image', 'tca' );
								?>
								<div class="square-store__flip" tabindex="0" aria-label="<?php echo esc_attr( $flip_hint ); ?>">
									<div class="square-store__flip-inner">
										<div class="square-store__flip-face square-store__flip-face--front">
											<img <?php awesome_acf_responsive_image( $item_image['id'], 'large', '600px', $main_alt ); ?> />
										</div>
										<div class="square-store__flip-face square-store__flip-face--back">
											<img <?php awesome_acf_responsive_image( $flip_image['id'], 'large', '600px', $flip_alt ); ?> />
										</div>
									</div>
								</div>
							<?php else : ?>
								<?php if ( ! empty( $item_image ) && ! empty( $item_image['id'] ) ) : ?>
									<?php
									$main_full_url = wp_get_attachment_image_url( $item_image['id'], 'large' );
									$main_alt      = ! empty( $item_image['alt'] ) ? $item_image['alt'] : $item_title;
									?>
									<a
										href="<?php echo esc_url( $main_full_url ? $main_full_url : $item_image['url'] ); ?>"
										class="square-store__image-link"
										<?php echo $item_title ? 'data-caption="' . esc_attr( $item_title ) . '"' : ''; ?>
									>
										<span class="square-store__image">
											<img <?php awesome_acf_responsive_image( $item_image['id'], 'large', '600px', $main_alt ); ?> />
										</span>
									</a>
								<?php endif; ?>

								<?php if ( ! empty( $extra_gallery_images ) ) : ?>
									<ul class="square-store__gallery" aria-label="<?php esc_attr_e( 'Product image gallery', 'tca' ); ?>">
										<?php foreach ( $extra_gallery_images as $gallery_image ) : ?>
											<?php
											$gallery_full_url = wp_get_attachment_image_url( $gallery_image['id'], 'large' );
											$gallery_alt      = ! empty( $gallery_image['alt'] ) ? $gallery_image['alt'] : $item_title;
											?>
											<li>
												<a
													href="<?php echo esc_url( $gallery_full_url ? $gallery_full_url : $gallery_image['url'] ); ?>"
													class="square-store__gallery-link"
													<?php echo $item_title ? 'data-caption="' . esc_attr( $item_title ) . '"' : ''; ?>
												>
													<img <?php awesome_acf_responsive_image( $gallery_image['id'], 'thumbnail', '120px', $gallery_alt ); ?> />
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="square-store__body">
						<?php if ( $item_title ) : ?>
							<h3 class="square-store__title"><?php echo esc_html( $item_title ); ?></h3>
						<?php endif; ?>

						<?php if ( $item_price ) : ?>
							<p class="square-store__price"><?php echo esc_html( $item_price ); ?></p>
						<?php endif; ?>

						<?php if ( $item_desc ) : ?>
							<div class="square-store__description">
								<?php echo wp_kses_post( $item_desc ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $buy_now_url ) : ?>
							<a
								class="tca-button <?php echo esc_attr( $buy_button_style ); ?> square-store__buy"
								href="<?php echo esc_url( $buy_now_url ); ?>"
								target="_blank"
								rel="noopener noreferrer"
							>
								<?php echo esc_html( $button_label ); ?>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</div>
<script>
document.querySelectorAll('[data-square-store]:not([data-flip-init])').forEach(function (root) {
	root.dataset.flipInit = '1';
	root.querySelectorAll('.square-store__flip').forEach(function (el) {
		el.addEventListener('click', function () {
			el.classList.toggle('is-flipped');
		});
	});
});
</script>

<?php
closeSection( $wrap_size, $container_size, $container_type, $overlapping_graphic );
