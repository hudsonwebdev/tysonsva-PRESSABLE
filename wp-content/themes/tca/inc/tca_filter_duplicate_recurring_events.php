<?php

/**
 * Filter to remove duplicate recurring events from Events Manager event list display
 * Prevents the same recurring event from appearing multiple times on the same page
 * 
 * @param array $events Array of EM_Event objects
 * @param int $events_count Total number of events
 * @param array $args Query arguments
 * @return array Filtered array of events with duplicates removed
 */
function tca_filter_duplicate_recurring_events( $events, $events_count, $args ) {

	// Track which recurring events we've already displayed
	static $displayed_recurring_events = array();
	
	// Reset the tracking array if this is a new page request
	// This ensures the filter works correctly across multiple event lists on the same page
	if ( ! did_action('wp_head') && empty($displayed_recurring_events) ) {
		$displayed_recurring_events = array();
	}
	
	$filtered_events = array();
	
	foreach ( $events as $event ) {
		// Check if this is a recurring event
		// Recurring events have a recurrence_id property that links them to their parent template
		if ( ! empty( $event->recurrence_id ) && $event->recurrence_id > 0 ) {
			$recurrence_id = $event->recurrence_id;
			
			// Check if we've already displayed an event from this recurrence series
			if ( ! in_array( $recurrence_id, $displayed_recurring_events ) ) {
				// This is the first occurrence of this recurring event, so include it
				$filtered_events[] = $event;
				// Mark this recurrence series as displayed
				$displayed_recurring_events[] = $recurrence_id;
			}
			// Otherwise, skip this event as it's a duplicate from the same recurrence series
		} else {
			// Not a recurring event, always include it
			$filtered_events[] = $event;
		}
	}
	
	return $filtered_events;
}
add_filter( 'em_events_output_events', 'tca_filter_duplicate_recurring_events', 10, 3 );
