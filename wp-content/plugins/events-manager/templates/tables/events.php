<?php
	//TODO Simplify panel for events, use form flags to detect certain actions (e.g. submitted, etc)
	global $wpdb, $bp, $EM_Notices;
	/* @var array $args */
	/* @var array $EM_Events */
	/* @var int $events_count */
	/* @var int $future_count */
	/* @var int $past_count */
	/* @var int $draft_count */
	/* @var int $pending_count */
	/* @var bool $show_add_new */
	/* @var int $limit */
	/* @var int $page */
	$url = esc_url(add_query_arg(array('scope' => null, 'status' => null, 'em_search' => null, 'pno' => null, 'admin_mode' => null))); //template for cleaning the link for each view below
	?>
	<div class="<?php em_template_classes('events-admin', 'events-admin-list'); ?>">
		<form id="posts-filter" action="" method="get" class="input">
			<?php
			echo $EM_Notices;
			//add new button will only appear if called from em_event_admin template tag, or if the $show_add_new var is set
			if(!empty($show_add_new) && current_user_can('edit_events')) echo '<a class="em-button button add-new-h2" href="'.em_add_get_params($_SERVER['REQUEST_URI'],array('action'=>'edit','scope'=>null,'status'=>null,'event_id'=>null, 'success'=>null)).'">'.__('Add New','events-manager').'</a>';
			?>
			<div class="subsubsub">
				<?php $url = esc_url(add_query_arg(array('scope'=>null,'status'=>null,'em_search'=>null,'pno'=>null, 'admin_mode'=>null))); //template for cleaning the link for each view below ?>
				<a href='<?php echo add_query_arg('view', 'future', $url); ?>' <?php echo ( !isset($_GET['view']) ) ? 'class="current"':''; ?>><?php _e ( 'Upcoming', 'events-manager'); ?> <span class="count">(<?php echo $future_count; ?>)</span></a> &nbsp;|&nbsp;
				<?php if( $pending_count > 0 ): ?>
				<a href='<?php echo add_query_arg('view', 'pending', $url); ?>' <?php echo ( isset($_GET['view']) && $_GET['view'] == 'pending' ) ? 'class="current"':''; ?>><?php _e ( 'Pending', 'events-manager'); ?> <span class="count">(<?php echo $pending_count; ?>)</span></a> &nbsp;|&nbsp;
				<?php endif; ?>
				<?php if( $draft_count > 0 ): ?>
				<a href='<?php echo add_query_arg('view', 'draft', $url); ?>' <?php echo ( isset($_GET['view']) && $_GET['view'] == 'draft' ) ? 'class="current"':''; ?>><?php _e ( 'Draft', 'events-manager'); ?> <span class="count">(<?php echo $draft_count; ?>)</span></a> &nbsp;|
				<?php endif; ?>
				<a href='<?php echo add_query_arg('view', 'past', $url); ?>' <?php echo ( isset($_GET['view']) && $_GET['view'] == 'past' ) ? 'class="current"':''; ?>><?php _e ( 'Past Events', 'events-manager'); ?> <span class="count">(<?php echo $past_count; ?>)</span></a>
				
				<?php if( current_user_can('edit_others_events') ): ?>
					<div class="admin-events-filter">
						<a href='<?php echo add_query_arg('admin_mode', 1, $url); ?>' <?php echo ( !empty($_GET['admin_mode']) ) ? 'class="current"':''; ?>><?php esc_html_e ( 'All Events', 'events-manager'); ?></a> &nbsp;|&nbsp;
						<a href='<?php echo add_query_arg('admin_mode', null, $url); ?>' <?php echo ( empty($_GET['admin_mode']) ) ? 'class="current"':''; ?>><?php esc_html_e ( 'My Events', 'events-manager'); ?></a>
					</div>
				<?php endif; ?>
			</div>
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input"><?php _e('Search Events','events-manager'); ?>:</label>
				<input type="text" id="post-search-input" name="em_search" value="<?php echo (!empty($_REQUEST['em_search'])) ? esc_attr($_REQUEST['em_search']):''; ?>" />
				<?php if( !empty($_REQUEST['view']) ): ?>
				<input type="hidden" name="view" value="<?php echo esc_attr($_REQUEST['view']); ?>" />
				<?php endif; ?>
				<input type="submit" value="<?php _e('Search Events','events-manager'); ?>" class="button" />
			</p>
			<div class="tablenav">
				<?php
				if ( $events_count >= $limit ) {
					$events_nav = em_admin_paginate( $events_count, $limit, $page);
					echo $events_nav;
				}
				?>
				<br class="clear" />
			</div>
				
			<?php
			if ( empty($EM_Events) ) {
				echo get_option ( 'dbem_no_events_message' );
			} else {
			?>
					
			<table class="widefat events-table">
				<thead>
					<tr>
						<?php /* 
						<th class='manage-column column-cb check-column' scope='col'>
							<input class='select-all' type="checkbox" value='1' />
						</th>
						*/ ?>
						<th><?php _e ( 'Name', 'events-manager'); ?></th>
						<th>&nbsp;</th>
						<?php if( get_option('dbem_locations_enabled') ): ?>
						<th><?php _e ( 'Location', 'events-manager'); ?></th>
						<?php endif; ?>
						<th colspan="2"><?php _e ( 'Date and time', 'events-manager'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$rowno = 0;
					foreach ( $EM_Events as $EM_Event ) {
						/* @var EM_Event $EM_Event */
						$rowno++;
						$class = ($rowno % 2) ? 'alternate' : '';
						
						if( $EM_Event->start()->getTimestamp() < time() && $EM_Event->end()->getTimestamp() < time() ){
							$class .= " past";
						}
						//Check pending approval events
						if ( !$EM_Event->get_status() ){
							$class .= " pending";
						}					
						?>
						<tr class="event <?php echo trim($class); ?>" id="event_<?php echo $EM_Event->event_id ?>">
							<?php /*
							<td>
								<input type='checkbox' class='row-selector' value='<?php echo $EM_Event->event_id; ?>' name='events[]' />
							</td>
							*/ ?>
							<td>
								<strong>
									<a class="row-title" href="<?php echo esc_url($EM_Event->get_edit_url()); ?>"><?php echo esc_html($EM_Event->event_name); ?></a>
								</strong>
								<?php 
								if( get_option('dbem_rsvp_enabled') == 1 && $EM_Event->event_rsvp == 1 ){
									?>
									<br/>
									<a href="<?php echo $EM_Event->get_bookings_url(); ?>"><?php esc_html_e("Bookings",'events-manager'); ?></a> &ndash;
									<?php esc_html_e("Booked",'events-manager'); ?>: <?php echo $EM_Event->get_bookings()->get_booked_spaces()."/".$EM_Event->get_spaces(); ?>
									<?php if( get_option('dbem_bookings_approval') == 1 ): ?>
										| <?php _e("Pending",'events-manager') ?>: <?php echo $EM_Event->get_bookings()->get_pending_spaces(); ?>
									<?php endif;
								}
								?>
								<div class="row-actions">
									<?php if( current_user_can('delete_events')) : ?>
									<span class="trash"><a href="<?php echo esc_url(add_query_arg(array('action'=>'event_delete', 'event_id'=>$EM_Event->event_id, '_wpnonce'=> wp_create_nonce('event_delete_'.$EM_Event->event_id)))); ?>" class="em-event-delete"><?php _e('Delete','events-manager'); ?></a></span>
									<?php endif; ?>
								</div>
							</td>
							<td>
								<a href="<?php echo $EM_Event->duplicate_url(); ?>" title="<?php _e ( 'Duplicate this event', 'events-manager'); ?>">
									<strong>+</strong>
								</a>
							</td>
							<?php if( get_option('dbem_locations_enabled') ): ?>
								<td>
									<?php
									if( $EM_Event->has_location() ){
										echo "<b>" . esc_html($EM_Event->get_location()->location_name) . "</b><br/>" . esc_html($EM_Event->get_location()->location_address) . " - " . esc_html($EM_Event->get_location()->location_town);
									}elseif( $EM_Event->has_event_location() ) {
										echo $EM_Event->get_event_location()->get_admin_column();
									}else{
										echo __('None','events-manager');
									}
									?>
								</td>
							<?php endif; ?>
							<td>
								<?php echo $EM_Event->output_dates(); ?>
								<br />
								<?php echo $EM_Event->output_times(); ?>
							</td>
							<td>
								<?php 
								if ( $EM_Event->is_recurrence(true) ) {
									$recurrence_delete_confirm = __('WARNING! You will delete ALL recurrences of this event, including booking history associated with any event in this recurrence. To keep booking information, go to the relevant single event and save it to detach it from this recurrence series.','events-manager');
									$event = $EM_Event->get_recurring_event();
									?>
									<strong>
									<?php echo $EM_Event->get_recurrence_description(); ?> <br />
									<?php if ( $event->is_recurring() ) : ?>
										<a href="<?php echo esc_url($EM_Event->get_edit_reschedule_url()); ?>"><?php echo esc_html( sprintf( __('Edit %s', 'events-manager'), __('Recurring Events') ) ); ?></a>
									<?php elseif ( $event->is_repeating() ) : ?>
										<a href="<?php echo esc_url($EM_Event->get_edit_reschedule_url()); ?>"><?php echo esc_html( sprintf( __('Edit %s', 'events-manager'), __('Repeating Events') ) ); ?></a>
									<?php endif; ?>
									<?php if( current_user_can('delete_events')) : ?>
									<span class="trash"><a href="<?php echo esc_url(add_query_arg(array('action'=>'event_delete', 'event_id'=>$EM_Event->recurrence_id, '_wpnonce'=> wp_create_nonce('event_delete_'.$EM_Event->recurrence_id)))); ?>" class="em-event-rec-delete" onclick ="if( !confirm('<?php echo $recurrence_delete_confirm; ?>') ){ return false; }"><?php _e('Delete','events-manager'); ?></a></span>
									<?php endif; ?>										
									</strong>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>  
			<?php
			} // end of table
			?>
			<div class='tablenav'>
				<div class="alignleft actions">
				<br class='clear' />
				</div>
				<?php if ( $events_count >= $limit ) : ?>
				<div class="tablenav-pages">
					<?php
					echo $events_nav;
					?>
				</div>
				<?php endif; ?>
				<br class='clear' />
			</div>
		</form>
	</div>