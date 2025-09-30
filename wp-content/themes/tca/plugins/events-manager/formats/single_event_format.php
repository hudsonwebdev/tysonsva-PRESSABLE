
	<main id="primary" class="site-main event-page">
	<div class="backing-svg"><svg xmlns="http://www.w3.org/2000/svg" width="699" height="1126" viewBox="0 0 699 1126" fill="none">
		<path d="M-23 516.388V1126L699 405.612V-204L-23 516.388Z" fill="#9ADCF7" fill-opacity="0.35"/>
		</svg>
	</div>
	<div class="uk-container">
		<a href="/events" class="back-to-events"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="18" viewBox="0 0 12 18" fill="none">
		<path d="M10 16L3 8.99577L10 2"  stroke-width="3" stroke-miterlimit="10"/></svg> Back to Events</a>

		<div class="event-header">
			<div class="event-info">
				<div class="inner">
					<div class="card-header">
						<div class="card-type">Event</div>
						<div class="card-date">
							#_EVENTDATES #_EVENTTIMES
						</div>
					</div>
					<br>       
					<div class="card-title">
						<h1>#_EVENTNAME</h1>
					</div>

					{has_location_venue}
					<div class="location-info uk-flex">
						<div class="pin">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 20 30" >
							<path d="M17.0739 3.06602C13.1681 -1.02201 6.83423 -1.02201 2.92848 3.06602C-0.38103 6.53 -0.951339 11.9385 1.55456 16.0808L9.99685 30L18.4391 16.0808C20.9537 11.9385 20.3834 6.53 17.0652 3.06602H17.0739ZM10.0055 14.4709C7.96621 14.4709 6.31577 12.7434 6.31577 10.609C6.31577 8.47453 7.96621 6.74706 10.0055 6.74706C12.0448 6.74706 13.6952 8.47453 13.6952 10.609C13.6952 12.7434 12.0448 14.4709 10.0055 14.4709Z" >
							</svg>
						</div>
						<div class="address">
							#_LOCATION

							{has_event_location}
					
								#_EVENTLOCATION<br><br>
								#_LOCATIONFULLLINE
						
							{/has_event_location}
						
						</div>
					</div>
					{/has_location_venue}	
					
				
				
					{has_att_Card_Info}
					<div class="additional-card-info">
                           #_ATT{Card Info}
					</div>
                     {/has_att_Card_Info}
					
					
					#_TICKETLINKTYSONS   
					
					{has_att_External_URL}
                                
                        
						<a href="#_ATT{External URL}" class="learn-more" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path d="M6.63356 4.13525H1.93701V17.0632H14.8588V12.3605" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M8.51318 1.93677H17.0631V10.4867" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            <path d="M16.6509 2.35522L6.5708 12.4353" stroke="white" stroke-width="3" stroke-miterlimit="10"/>
                            </svg> <span>Learn More</span></a>
                             
                    {/has_att_External_URL}

					
					
					#_EVENTADDTOCALENDARTYSONS 
				</div>
			</div>	
			<div class="featured-image">
				<div class="aspect-ratio-container">
						{has_image}
			
				#_EVENTIMAGE
			
			{/has_image}
				</div>
			</div>
		</div>
	</div>


	<div class="uk-container uk-container-small">    
		<div class="event-content">
			#_EVENTNOTES
			<hr>
			<div class="share">
					#_DRAWSHARELINKS
			</div>
		</div>
	</div>


<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "#_EVENTNAME",
  "startDate": "#_EVENTISOSTART",
  "endDate": "#_EVENTISOEND",
  "eventStatus": "https://schema.org/EventScheduled",
  "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
  "location": {
    "@type": "Place",
    "name": "#_LOCATIONNAME",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "#_LOCATIONADDRESS",
      "addressLocality": "#_LOCATIONTOWN",
      "postalCode": "#_LOCATIONPOSTCODE",
      "addressRegion": "#_LOCATIONREGION",
      "addressCountry": "#_LOCATIONCOUNTRY"
    }
  },
  "image": [
    "#_EVENTIMAGEURL"
  ],
  "description": "#_EVENTEXCERPT",
  "organizer": {
    "@type": "Organization",
    "name": "Tysons",
    "url": ""
  }
}
</script>




</main>
