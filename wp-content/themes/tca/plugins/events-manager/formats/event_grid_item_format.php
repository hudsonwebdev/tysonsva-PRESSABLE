<div class="em-event em-item">
<div class="event-card #_ALLCATEGORYSLUG">
            <div class="outerblue">
                <div class="outer-card-chevron"><?php drawSVG('outer-chevron-up-card'); ?></div>
            </div> 
            <div class="inner-wrap">

                <div class="chev-up"><?php drawSVG('chevron-up-card'); ?></div>

                <div class="innerblue"></div>

                    <div class="single-stack">

                        <div class="card-image">

                 
    
            #_EVENTDATESTYSONSCARDS


    
                        {has_image}
						#_EVENTIMAGE{medium}
						{/has_image}
						{no_image}
						<div class="em-item-image-placeholder">
							<div class="date">
								<span class="day">#d</span>
								<span class="month">#M</span>
							</div>
						</div>
						{/no_image}
                        </div>
                       
                        
                        
                        <div class="tca-card-info">
                            <div class="inner">

                            <div class="card-header">

                                    <div class="card-type">EVENT</div>
                                    
                                      
                                        <div class="card-date">
				                          #_EVENTDATEDISPLAYTYSONSCARDS
                                         </div>
                                      
                                </div>

                                <br>
                              
                                <div class="card-title">
                                
                                
                                
                                   <h4 class="em-item-title"><a class="event-card-title-link" href="#_EVENTLINKTYSONS" target="#_EVENTTARGETTYSONS">#_EVENTNAME</a></h4>
                                    {has_location_venue}
									

                                    <div class="location-info uk-flex">
                                    <div class="pin">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="30" viewBox="0 0 20 30" >
                                        <path d="M17.0739 3.06602C13.1681 -1.02201 6.83423 -1.02201 2.92848 3.06602C-0.38103 6.53 -0.951339 11.9385 1.55456 16.0808L9.99685 30L18.4391 16.0808C20.9537 11.9385 20.3834 6.53 17.0652 3.06602H17.0739ZM10.0055 14.4709C7.96621 14.4709 6.31577 12.7434 6.31577 10.609C6.31577 8.47453 7.96621 6.74706 10.0055 6.74706C12.0448 6.74706 13.6952 8.47453 13.6952 10.609C13.6952 12.7434 12.0448 14.4709 10.0055 14.4709Z" >
                                        </svg>
                                    </div>
                                    <div class="address">

                                 

                                        <div class="location-title">#_LOCATION</div>


                                    

                                    </div>
                                </div>


									{/has_location_venue}
              
                                    <div class="add-card-info">
                                        #_ADDITIONALCARDINFOTYSONS
                                    </div>
                                </div>
                                

                                
                            </div>
                        </div>

                    </div>

             </div>
    <a class="event-card-overlay" href="#_EVENTLINKTYSONS" target="#_EVENTTARGETTYSONS" tabindex="-1" aria-hidden="true"></a>
</div>
</div>

