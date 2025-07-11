<div class="em-event em-item">
<div class="event-card #_CATEGORYSLUG">
            <div class="outerblue">
                <div class="outer-card-chevron"><?php drawSVG('outer-chevron-up-card'); ?></div>
            </div> 
            <div class="inner-wrap">

                <div class="chev-up"><?php drawSVG('chevron-up-card'); ?></div>

                <div class="innerblue"></div>

                    <div class="single-stack">

                        <div class="card-image">

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
                                    <div class="card-type">Event</div>

                                   
                                    <div class="card-date">#_EVENTDATESTYSONS</div>
                                    
                                </div>
                                <div class="card-title">
                                
                                
                                
                                   <h4 class="em-item-title"><a href="#_EVENTLINKTYSONS" target="#_EVENTTARGETTYSONS">#_EVENTNAME</a></h4>
                                    {has_location_venue}
									<div class="em-item-meta-line em-event-location">
										<span class="em-icon-location em-icon"></span>
										#_LOCATIONLINK
									</div>
									{/has_location_venue}
              
                                    
                                </div>
                                

                                
                            </div>
                        </div>

                    </div>

             </div>
    
</div>
</div>

