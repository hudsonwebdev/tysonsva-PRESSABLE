<?php
/**
 * Admin tool: resave all Events Manager locations (same as clicking Update on each).
 *
 * @package tca
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current user can run the location resave tool.
 *
 * @return bool
 */
function tca_em_resave_locations_can() {
	return current_user_can( 'manage_options' );
}

/**
 * Register Tools menu page.
 *
 * @return void
 */
function tca_em_resave_locations_menu() {
	add_management_page(
		__( 'Resave Event Locations', 'tca' ),
		__( 'Resave Event Locations', 'tca' ),
		'manage_options',
		'tca-resave-locations',
		'tca_em_resave_locations_page'
	);
}
add_action( 'admin_menu', 'tca_em_resave_locations_menu' );

/**
 * Location post type slug.
 *
 * @return string
 */
function tca_em_resave_locations_post_type() {
	return defined( 'EM_POST_TYPE_LOCATION' ) ? EM_POST_TYPE_LOCATION : 'location';
}

/**
 * Count location posts that will be resaved.
 *
 * @return int
 */
function tca_em_resave_locations_count() {
	$q = new WP_Query(
		array(
			'post_type'      => tca_em_resave_locations_post_type(),
			'post_status'    => array( 'publish', 'private', 'draft', 'pending' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => false,
		)
	);
	return (int) $q->found_posts;
}

/**
 * Resave a batch of location posts.
 *
 * @param int $offset Batch offset.
 * @param int $limit  Batch size.
 * @return array{processed: int, saved: int, failed: array<int, string>, done: bool, next_offset: int}
 */
function tca_em_resave_locations_batch( $offset, $limit = 20 ) {
	$result = array(
		'processed'   => 0,
		'saved'       => 0,
		'failed'      => array(),
		'done'        => true,
		'next_offset' => (int) $offset,
	);

	if ( ! class_exists( 'EM_Location' ) || ! function_exists( 'em_get_location' ) ) {
		$result['failed'][] = __( 'Events Manager is not available.', 'tca' );
		return $result;
	}

	$offset = max( 0, (int) $offset );
	$limit  = max( 1, min( 50, (int) $limit ) );

	$ids = get_posts(
		array(
			'post_type'              => tca_em_resave_locations_post_type(),
			'post_status'            => array( 'publish', 'private', 'draft', 'pending' ),
			'posts_per_page'         => $limit,
			'offset'                 => $offset,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( empty( $ids ) ) {
		return $result;
	}

	foreach ( $ids as $post_id ) {
		$post_id = (int) $post_id;
		++$result['processed'];

		$EM_Location = em_get_location( $post_id, 'post_id' );
		if ( ! is_object( $EM_Location ) ) {
			$result['failed'][ $post_id ] = __( 'Could not load location.', 'tca' );
			continue;
		}

		$status = get_post_status( $post_id );
		if ( is_string( $status ) && '' !== $status ) {
			$EM_Location->force_status = $status;
		}

		$ok = $EM_Location->save();
		if ( $ok ) {
			++$result['saved'];
		} else {
			$errors = array();
			if ( ! empty( $EM_Location->errors ) && is_array( $EM_Location->errors ) ) {
				$errors = $EM_Location->errors;
			}
			$name = get_the_title( $post_id );
			$result['failed'][ $post_id ] = sprintf(
				'%s (#%d)%s',
				$name ? $name : __( '(no title)', 'tca' ),
				$post_id,
				$errors ? ' — ' . implode( ' ', array_map( 'wp_strip_all_tags', $errors ) ) : ''
			);
		}
	}

	$result['next_offset'] = $offset + $result['processed'];
	$result['done']        = count( $ids ) < $limit;

	return $result;
}

/**
 * Tools page + batch runner.
 *
 * @return void
 */
function tca_em_resave_locations_page() {
	if ( ! tca_em_resave_locations_can() ) {
		wp_die( esc_html__( 'You do not have permission to run this tool.', 'tca' ) );
	}

	$running     = false;
	$batch       = null;
	$total_ok    = 0;
	$total_fail  = 0;
	$all_failed  = array();
	$offset      = 0;
	$plugin_ok   = class_exists( 'EM_Location' );

	if ( $plugin_ok && isset( $_POST['tca_em_resave_locations'] ) ) {
		check_admin_referer( 'tca_em_resave_locations' );
		$running    = true;
		$offset     = isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0;
		$total_ok   = isset( $_POST['saved'] ) ? (int) $_POST['saved'] : 0;
		$total_fail = isset( $_POST['failed_count'] ) ? (int) $_POST['failed_count'] : 0;
		if ( ! empty( $_POST['failed_log'] ) ) {
			$decoded = json_decode( wp_unslash( $_POST['failed_log'] ), true );
			if ( is_array( $decoded ) ) {
				$all_failed = $decoded;
			}
		}

		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 120 );
		}

		$batch      = tca_em_resave_locations_batch( $offset );
		$total_ok  += (int) $batch['saved'];
		$total_fail += count( $batch['failed'] );
		foreach ( $batch['failed'] as $id => $msg ) {
			$all_failed[ (string) $id ] = $msg;
		}
		$offset = (int) $batch['next_offset'];
	}

	$total = $plugin_ok ? tca_em_resave_locations_count() : 0;
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Resave Event Locations', 'tca' ); ?></h1>
		<p><?php echo esc_html__( 'This runs the same save Events Manager uses when you click Update on a location. Use it to refresh coordinates, attributes, and the locations database table for every location.', 'tca' ); ?></p>

		<?php if ( ! $plugin_ok ) : ?>
			<div class="notice notice-error"><p><?php echo esc_html__( 'Events Manager is not active.', 'tca' ); ?></p></div>
		<?php else : ?>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %d: location count */
						_n( '%d location will be resaved.', '%d locations will be resaved.', $total, 'tca' ),
						$total
					)
				);
				?>
			</p>

			<?php if ( $running && $batch ) : ?>
				<div class="notice notice-info">
					<p>
						<?php
						echo esc_html(
							sprintf(
								/* translators: 1: processed count, 2: total count */
								__( 'Processed %1$d of %2$d…', 'tca' ),
								min( $offset, $total ),
								$total
							)
						);
						?>
					</p>
					<p>
						<?php
						echo esc_html(
							sprintf(
								/* translators: 1: saved count, 2: failed count */
								__( 'Saved: %1$d. Failed: %2$d.', 'tca' ),
								$total_ok,
								$total_fail
							)
						);
						?>
					</p>
				</div>

				<?php if ( ! empty( $batch['done'] ) ) : ?>
					<div class="notice notice-success"><p><?php echo esc_html__( 'Finished resaving locations.', 'tca' ); ?></p></div>
					<?php if ( $all_failed ) : ?>
						<h2><?php echo esc_html__( 'Failed', 'tca' ); ?></h2>
						<ul>
							<?php foreach ( $all_failed as $msg ) : ?>
								<li><?php echo esc_html( $msg ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				<?php else : ?>
					<form method="post" id="tca-em-resave-continue">
						<?php wp_nonce_field( 'tca_em_resave_locations' ); ?>
						<input type="hidden" name="tca_em_resave_locations" value="1" />
						<input type="hidden" name="offset" value="<?php echo esc_attr( (string) $offset ); ?>" />
						<input type="hidden" name="saved" value="<?php echo esc_attr( (string) $total_ok ); ?>" />
						<input type="hidden" name="failed_count" value="<?php echo esc_attr( (string) $total_fail ); ?>" />
						<input type="hidden" name="failed_log" value="<?php echo esc_attr( wp_json_encode( $all_failed ) ); ?>" />
						<p><button type="submit" class="button button-primary"><?php echo esc_html__( 'Continue', 'tca' ); ?></button></p>
					</form>
					<script>
						document.getElementById('tca-em-resave-continue') && document.getElementById('tca-em-resave-continue').submit();
					</script>
				<?php endif; ?>
			<?php elseif ( $total > 0 ) : ?>
				<form method="post">
					<?php wp_nonce_field( 'tca_em_resave_locations' ); ?>
					<input type="hidden" name="tca_em_resave_locations" value="1" />
					<input type="hidden" name="offset" value="0" />
					<input type="hidden" name="saved" value="0" />
					<input type="hidden" name="failed_count" value="0" />
					<p>
						<label>
							<input type="checkbox" name="tca_em_resave_confirm" value="1" required />
							<?php echo esc_html__( 'I understand this will update every event location.', 'tca' ); ?>
						</label>
					</p>
					<p>
						<button type="submit" class="button button-primary">
							<?php echo esc_html__( 'Resave all locations', 'tca' ); ?>
						</button>
					</p>
				</form>
			<?php else : ?>
				<p><?php echo esc_html__( 'No locations found.', 'tca' ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}
