	<div class="uk-container">
		<a href="/events" class="back-to-events"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18" fill="none">
		<path d="M10 16L3 8.99577L10 2"  stroke-width="3" stroke-miterlimit="10"/></svg> Back to Events</a>
		<div class="location-header">
			<div class="location-info">
				<div class="inner">
					<div class="card-header">
						<div class="card-type">Location</div>
					</div>
					<div class="card-title">
						<h1>#_LOCATIONNAME</h1>
					</div>

					<div class="location-address uk-flex">
						<div class="pin">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 20 30" >
							<path d="M17.0739 3.06602C13.1681 -1.02201 6.83423 -1.02201 2.92848 3.06602C-0.38103 6.53 -0.951339 11.9385 1.55456 16.0808L9.99685 30L18.4391 16.0808C20.9537 11.9385 20.3834 6.53 17.0652 3.06602H17.0739ZM10.0055 14.4709C7.96621 14.4709 6.31577 12.7434 6.31577 10.609C6.31577 8.47453 7.96621 6.74706 10.0055 6.74706C12.0448 6.74706 13.6952 8.47453 13.6952 10.609C13.6952 12.7434 12.0448 14.4709 10.0055 14.4709Z" >
							</svg>
						</div>
						<div class="address">
							#_LOCATIONFULLBR
						</div>
					</div>

					{has_att_Website}
					<p class="location-website">
						<a href="#_LATT{Website}" target="_blank" rel="noopener noreferrer">Website</a>
					</p>
					{/has_att_Website}
				</div>
			</div>
			{has_loc_image}
			<div class="featured-image">
				<div class="aspect-ratio-container">
					#_LOCATIONIMAGE
				</div>
			</div>
			{/has_loc_image}
		</div>

		<div class="location-map">
			#_LOCATIONMAP
		</div>

		<div class="location-content">
			#_LOCATIONNOTES
		</div>

		{has_events}
		<div class="location-upcoming">
			<h2><?php echo esc_html__( 'Upcoming Events', 'tca' ); ?></h2>
			#_LOCATIONNEXTEVENTS
		</div>
		{/has_events}
	</div>
