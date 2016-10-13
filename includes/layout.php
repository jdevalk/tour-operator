<?php
/**
 * Structure the TO pages
 *
 * @package   	tour-operator
 * @subpackage 	layout
 * @license   	GPL3
 */

/**
 * Adds the template tags to the bottom of the single accommodation
 *
 * @package 	tour-operator
 * @subpackage	template-tag
 * @category 	accommodation
 */
function to_single_accommodation_content_bottom() {
	if(is_singular('accommodation')){
		to_accommodation_units('<section id="{units}"><h2 class="section-title">'.__('{units}','tour-operator').'</h2><div class="info row">','</div></section>');
		
		to_accommodation_facilities('<section id="facilities"><h2 class="section-title">'.__('Facilities','tour-operator').'</h2><div class="info row">','</div></section>');	

		if(to_has_map()){ ?>
			<section id="accommodation-map">
				<h2 class="section-title"><?php esc_html_e('Map','tour-operator'); ?></h2>
				<?php to_map(); ?>
			</section>			
		<?php }			
		
		if(function_exists('to_gallery')) { to_gallery('<section id="gallery"><h2 class="section-title">'.__('Gallery','tour-operator').'</h2>','</section>'); }
		
		if(function_exists('to_videos')) { to_videos('<div id="videos"><h2 class="section-title">'.__('Videos','tour-operator').'</h2>','</div>'); }

		to_related_items('travel-style','<section id="related-items"><h2 class="section-title">'.__(to_get_post_type_section_title('accommodation', 'similar', 'Related Accommodation'),'tour-operator').'</h2>','</section>');	

		$connected_tours = get_post_meta(get_the_ID(),'tour_to_accommodation',false); 
		if(to_accommodation_display_connected_tours() && post_type_exists('tour') && is_array($connected_tours) && !empty($connected_tours)){
			to_related_items($connected_tours,'<section id="related-items"><h2 class="section-title">'.__('Related Tours','tour-operator').'</h2>','</section>',true,'tour');
		}	
	}
}
add_action('lsx_content_bottom','to_single_accommodation_content_bottom');

/**
 * Adds the template tags to the bottom of the single destination
 *
 * @package 	tour-operator
 * @subpackage	template-tag
 * @category 	destination
 */
function to_single_destination_content_bottom() {
	if(is_singular('destination')){

		to_country_regions();
		
		to_destination_tours();

		to_region_accommodation();

		to_destination_activities();
			
		if(to_has_map()){ ?>
			<section id="destination-map">
				<h2 class="section-title"><?php esc_html_e('Map','tour-operator'); ?></h2>
				<?php to_map(); ?>
			</section>			
		<?php }		
		
		if(function_exists('to_gallery')) { to_gallery('<section id="gallery"><h2 class="section-title">'.__('Gallery','tour-operator').'</h2>','</section>'); }
		
		if(function_exists('to_videos')) { to_videos('<div id="videos"><h2 class="section-title">'.__('Videos','tour-operator').'</h2>','</div>'); }		
	}	
}
add_action('lsx_content_bottom','to_single_destination_content_bottom');

/**
 * Adds the template tags to the bottom of the single tour
 *
 * @package 	tour-operator
 * @subpackage	template-tag
 * @category 	tour
 */
function to_single_tour_content_bottom() {
	if(is_singular('tour')){ ?>
		<section id="highlights">
			<div class="row">
				<div class="col-sm-6">
					<?php to_highlights('<div class="highlights"><h2 class="section-title">'.__('Highlights','tour-operator').'</h2>','</div>'); ?>
				</div>
				<div class="col-sm-6">
					<?php to_best_time_to_visit('<div class="best-time-to-visit"><h2 class="section-title">'.__('Best time to visit','tour-operator').'</h2><div class="best-time-to-visit-content">','</div></div>'); ?>
				</div>	
			</div>				
		</section>
		
		<?php if(to_has_itinerary()){ ?>
			<section id="itinerary">
				<h2 class="section-title"><?php esc_html_e('Full Day by Day Itinerary','tour-operator');?></h2>
				<?php while(to_itinerary_loop()){ ?>
					<?php to_itinerary_loop_item(); ?>
					<div <?php to_itinerary_class('itinerary-item'); ?>>
						<div class="row">
							<div class="panel col-sm-12">
								<div class="itinerary-inner">
									<?php if(to_itinerary_has_thumbnail()) { ?>
										<div class="itinerary-image col-sm-3">
											<div class="thumbnail">
												<?php to_itinerary_thumbnail(); ?>
											</div>
										</div>
									<?php } ?>
									<div class="itinerary-content col-sm-<?php if(to_itinerary_has_thumbnail()) { ?>9<?php }else{?>12<?php }?>">
										<div class="col-sm-8">
											<h3><?php to_itinerary_title(); ?></h3>
											<strong><small><?php to_itinerary_tagline() ?></small></strong>
											<div class="entry-content">
												<?php to_itinerary_description(); ?>
											</div>
										</div>
										<div class="col-sm-4">
											<?php to_itinerary_destinations('<div class="meta destination">'.__('Destination','tour-operator').': ','</div>'); ?>
											<?php to_itinerary_accommodation('<div class="meta accommodation">'.__('Accommodation','tour-operator').': ','</div>'); ?>
											<?php to_itinerary_activities('<div class="meta activities">'.__('Activites','tour-operator').': ','</div>'); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>					
				<?php } ?>
				<?php to_itinerary_read_more(); ?>
			</section>
		<?php }
		
		to_pricing_block();

		if(to_has_map()){ ?>
			<section id="tour-map">
				<h2 class="section-title"><?php esc_html_e('Map','tour-operator'); ?></h2>
				<?php to_map(); ?>
			</section>			
		<?php }			
		
		if(function_exists('to_gallery')) { to_gallery('<section id="gallery"><h2 class="section-title">'.__('Gallery','tour-operator').'</h2>','</section>'); }
		
		if(function_exists('to_videos')) { to_videos('<div id="videos"><h2 class="section-title">'.__('Videos','tour-operator').'</h2>','</div>'); }
		
		to_related_items('travel-style','<section id="related-items"><h2 class="section-title">'.__(to_get_post_type_section_title('tour', 'related', 'Related Tours'),'tour-operator').'</h2>','</section>');		
	}	
}
add_action('lsx_content_bottom','to_single_tour_content_bottom');