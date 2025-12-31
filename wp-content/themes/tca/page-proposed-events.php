<?php
/**
 * Template Name: Proposed Events
 * 
 * Template for displaying proposed events on a password-protected page.
 * This template will show all proposed-event post types with their ACF fields.
 *
 * @package tca
 */

get_header();

?>

<style>
.proposed-events-month-group {
	margin-bottom: 2rem;
}
.month-heading {
	font-size: 1.5rem;
	font-weight: bold;
	margin-bottom: 1rem;
	padding-bottom: 0.5rem;
	border-bottom: 2px solid #385DFF;
}
.proposed-event-item {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	padding: 0.75rem 0;
	border-bottom: 1px solid #e0e0e0;
	flex-wrap: nowrap;
}
.proposed-event-item:last-child {
	border-bottom: none;
}
.event-title {
	font-weight: bold;
	min-width: 200px;
	flex-shrink: 0;
}
.separator {
	color: #ccc;
	margin: 0 0.25rem;
}
.event-dates {
	color: #385DFF;
	font-weight: 500;
	white-space: nowrap;
}
.duplicate-warning {
	margin-left: 0.25rem;
	font-size: 1.2rem;
	display: inline-block;
}
.event-org {
	color: #666;
	white-space: nowrap;
}
.event-attendance {
	color: #666;
	white-space: nowrap;
}
.event-description {
	color: #666;
	flex: 1;
	min-width: 200px;
}
.event-title.pending {
	background-color: yellow;
	padding: 0.25rem 0.5rem;
	border-radius: 3px;
}
.tint-form{
    background:#eee;
    padding:80px 0;
}
</style>


<main id="primary" class="site-main">
	<div class="proposed-events-page">

			<?php
			// Check if the page is password protected FIRST, before displaying any content
			if ( post_password_required() ) {
				// Show the password form
				echo get_the_password_form();
				get_footer();
				return;
			}
			
			// Display the page content if any (only if password is entered)
			while ( have_posts() ) :
				the_post();
				 the_content();
			endwhile;

            ?>
			
			<div class="uk-container">
                	<br><br>
                    <h2>Proposed Events</h2>

			<div class="proposed-events-list">
				<?php
				// Query for proposed-event post type (include both published and pending)
				$args = array(
					'post_type'      => 'proposed-event',
					'posts_per_page' => -1, // Get all proposed events
					'post_status'    => array( 'publish', 'pending' ),
					'orderby'        => 'meta_value',
					'meta_key'       => 'start_date',
					'order'          => 'ASC',
					'meta_query'     => array(
						'relation' => 'OR',
						array(
							'key'     => 'start_date',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'start_date',
							'compare' => 'NOT EXISTS',
						),
					),
				);

				$proposed_events_query = new WP_Query( $args );

				if ( $proposed_events_query->have_posts() ) :
					// First, collect all events and their dates
					$events_by_month = array();
					$all_events = array(); // Store all events for overlap detection
					
					while ( $proposed_events_query->have_posts() ) :
						$proposed_events_query->the_post();
						
						// Get post status
						$post_status = get_post_status();
						
						// Get ACF fields
						$start_date         = get_field( 'start_date' );
						$end_date           = get_field( 'end_date' );
						$organization       = get_field( 'organization' );
						$expected_attendance = get_field( 'expected_attendance' );
						$short_description  = get_field( 'short_description' );
						
						// Format dates to MM/DD/YYYY
						$start_date_formatted = '';
						$end_date_formatted   = '';
						$start_date_timestamp = null;
						$end_date_timestamp   = null;
						
						if ( $start_date ) {
							$start_date_timestamp = strtotime( $start_date );
							$start_date_formatted = date( 'm/d/Y', $start_date_timestamp );
						}
						
						if ( $end_date ) {
							$end_date_timestamp = strtotime( $end_date );
							$end_date_formatted = date( 'm/d/Y', $end_date_timestamp );
						}
						
						// If no end date, use start date as end date (single day event)
						if ( $start_date_timestamp && ! $end_date_timestamp ) {
							$end_date_timestamp = $start_date_timestamp;
						}
						
						// Determine which month to group by (use start date, or end date if no start date)
						$group_date = $start_date_timestamp ? $start_date_timestamp : ( $end_date_timestamp ? $end_date_timestamp : time() );
						$month_key = date( 'Y-m', $group_date );
						
						if ( ! isset( $events_by_month[ $month_key ] ) ) {
							$events_by_month[ $month_key ] = array();
						}
						
						// Create a unique identifier for this event (using title + dates)
						$event_signature = md5( get_the_title() . $start_date_timestamp . $end_date_timestamp );
						
						$event_data = array(
							'signature'          => $event_signature,
							'title'              => get_the_title(),
							'post_status'        => $post_status,
							'start_date'         => $start_date_formatted,
							'end_date'           => $end_date_formatted,
							'start_date_timestamp' => $start_date_timestamp,
							'end_date_timestamp'  => $end_date_timestamp,
							'organization'       => $organization,
							'expected_attendance' => $expected_attendance,
							'short_description'  => $short_description,
						);
						
						$events_by_month[ $month_key ][] = $event_data;
						$all_events[] = $event_data;
						
					endwhile;
					wp_reset_postdata();
					
					// Function to check if two date ranges overlap
					// Two ranges overlap if: start1 <= end2 AND start2 <= end1
					$check_overlap = function( $start1, $end1, $start2, $end2 ) {
						// If either event has no dates, they don't overlap
						if ( ! $start1 || ! $end1 || ! $start2 || ! $end2 ) {
							return false;
						}
						return ( $start1 <= $end2 && $start2 <= $end1 );
					};
					
					// Check each event for overlaps with other events
					$overlapping_events = array();
					for ( $i = 0; $i < count( $all_events ); $i++ ) {
						for ( $j = $i + 1; $j < count( $all_events ); $j++ ) {
							$event1 = $all_events[ $i ];
							$event2 = $all_events[ $j ];
							
							if ( $check_overlap(
								$event1['start_date_timestamp'],
								$event1['end_date_timestamp'],
								$event2['start_date_timestamp'],
								$event2['end_date_timestamp']
							) ) {
								// Mark both events as overlapping using their signatures
								$overlapping_events[ $event1['signature'] ] = true;
								$overlapping_events[ $event2['signature'] ] = true;
							}
						}
					}
					
					// Now display events grouped by month
					foreach ( $events_by_month as $month_key => $events ) :
						// Format month heading (e.g., "December 2025")
						$month_heading = date( 'F Y', strtotime( $month_key . '-01' ) );
						?>
						<div class="proposed-events-month-group">
							<h2 class="month-heading"><?php echo esc_html( $month_heading ); ?></h2>
							
							<?php foreach ( $events as $event ) : 
								// Check if this event overlaps with any other event using its signature
								$has_overlap = isset( $overlapping_events[ $event['signature'] ] ) && $overlapping_events[ $event['signature'] ];
								// Check if this event is pending
								$is_pending = ( $event['post_status'] === 'pending' );
								?>
								<div class="proposed-event-item">
									<span class="event-title <?php echo $is_pending ? 'pending' : ''; ?>"><?php echo esc_html( $event['title'] ); ?></span>
									<span class="separator">|</span>
									
									<span class="event-org"><?php echo $event['organization'] ? esc_html( $event['organization'] ) : 'N/A'; ?></span>
									<span class="separator">|</span>
									
									<?php 
									// Display dates together
									if ( $event['start_date'] || $event['end_date'] ) :
										?>
										<span class="event-dates">
											<?php if ( $event['start_date'] ) : ?>
												Start: <?php echo esc_html( $event['start_date'] ); ?>
											<?php endif; ?>
											<?php if ( $event['start_date'] && $event['end_date'] ) : ?>
												<span class="separator">|</span>
											<?php endif; ?>
											<?php if ( $event['end_date'] ) : ?>
												End: <?php echo esc_html( $event['end_date'] ); ?>
											<?php endif; ?>
											<?php if ( $has_overlap ) : ?>
												<span class="duplicate-warning" style="color: red;">⚠</span>
											<?php endif; ?>
										</span>
										<span class="separator">|</span>
									<?php endif; ?>
									
									<?php if ( $event['expected_attendance'] ) : ?>
										<span class="event-attendance">Attendance: <?php echo esc_html( number_format( $event['expected_attendance'] ) ); ?></span>
										<span class="separator">|</span>
									<?php endif; ?>
									
									<?php if ( $event['short_description'] ) : ?>
										<span class="event-description"><?php echo esc_html( $event['short_description'] ); ?></span>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
					<?php
				else :
					?>
					<div class="no-proposed-events">
						<p>No proposed events found.</p>
					</div>
					<?php
				endif;
				?>
			</div>
            </div>
            <div class="tint-form">
                <div class="uk-container">
                <h2>Propose an Event</h2>
                <?php gravity_form( 18, false, false, false, '', false ); ?>
                </div>
            </div>
		
	</div>
</main>

<?php
get_footer();

