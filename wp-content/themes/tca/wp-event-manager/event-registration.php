<?php if($register = get_event_registration_method()) :
	wp_enqueue_script('wp-event-manager-event-registration');

	if($register->type) : ?>
		<div class="event_registration registration">
			<?php do_action('event_registration_start', $register); ?>
		
			
				<?php
				/**
				 * event_manager_registration_details_email or event_manager_registration_details_url hook
				 */
				do_action('event_manager_registration_details_' . $register->type, $register);
				?>
			
			<?php do_action('event_registration_end', $register); ?>
		</div>
	<?php endif;
endif; ?>