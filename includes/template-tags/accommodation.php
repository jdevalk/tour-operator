<?php
/**
 * Template Tags
 *
 * @package   		tour-operator
 * @subpackage 		template-tags
 * @category 		accommodation
 * @license   		GPL3
 */

/**
 * Outputs the current accommodations room total
 * 
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_accommodation_room_total($before="",$after="",$echo=true){
	lsx_to_custom_field_query('number_of_rooms',$before,$after,$echo);
}

/**
 * Gets the current accommodations rating
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @param		$post_id	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_accommodation_rating($before="",$after="",$echo=true,$post_id=false){
	lsx_to_custom_field_query('rating',$before,$after,$echo,$post_id);
}

/**
 * Outputs the accommodations facilities
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_has_facilities(){
	// Get any existing copy of our transient data
	$facilities = get_transient( get_the_ID().'_facilities' );
	if ( false === $facilities ) {
		// It wasn't there, so regenerate the data and save the transient
		
		$facilities = wp_get_object_terms(get_the_ID(),'facility');
		$main_facilities = false;
		$child_facilities = false;
		$return = false;
		if ( ! empty( $facilities ) && ! is_wp_error( $facilities ) ) {
			foreach( $facilities as $facility ) {
				if(0 === $facility->parent){
					$main_facilities[] = $facility;
				}else{
					$child_facilities[$facility->parent][] = $facility;
				}
			}


			set_transient( get_the_ID().'_facilities', $location, 30 );
		}


	}else{
		return false;
	}	
}

/**
 * Outputs the accommodations facilities
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_accommodation_facilities($before="",$after="",$echo=true){
	$facilities = wp_get_object_terms(get_the_ID(),'facility');
	$main_facilities = false;
	$child_facilities = false;
	$return = false;
	if ( ! empty( $facilities ) && ! is_wp_error( $facilities ) ) {
		foreach( $facilities as $facility ) {
			if(0 === $facility->parent){
				$main_facilities[] = $facility;
			}else{
				$child_facilities[$facility->parent][] = $facility;
			}
		}

		//Output in the order we want
		if ( count( $main_facilities ) > 0 && count( $child_facilities ) > 0 ) {
			foreach($main_facilities as $heading){
				$return .= '<div class="'.$heading->slug.' col-sm-6"><div class="facilities-content"><h3><a href="'.get_term_link( $heading->slug, 'facility' ).'">'.esc_html( $heading->name ).'</a></h3>';
				
				if(is_array($child_facilities) && isset($child_facilities[$heading->term_id])){
					$return .= '<ul class="row">';
						foreach($child_facilities[$heading->term_id] as $child_facility){
							$return .= '<li class="col-sm-4"><a href="'.get_term_link( $child_facility->slug, 'facility' ).'">'.esc_html( $child_facility->name ).'</a></li>';
						}
					$return .= '</ul>';
				}
				$return .= '</div></div>';
			}
			
			$return = $before.$return.$after;
			if($echo){
				echo wp_kses_post( $return );
			}else{
				return $return;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

/**
 * Outputs the Spoken Languages HTML
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_accommodation_spoken_languages($before="",$after="",$echo=true){
	$spoken_languages = get_post_meta( get_the_ID(), 'spoken_languages', true );
	if ( is_string( $spoken_languages ) && ! empty( $spoken_languages ) ) {
		$spoken_languages = array( $spoken_languages );
	}
	$return = '';

	if ( ! empty( $spoken_languages ) && ! is_wp_error( $spoken_languages ) ) {
		$return .= '<span class="values">';

		foreach ( $spoken_languages as $i => $spoken_language ) {
			$return .= ucwords( str_replace( '_', ' / ', str_replace( '-', ' ', str_replace( '-and-', ' & ', $spoken_language ) ) ) );

			if ( ( $i + 1 ) < count( $spoken_languages ) ) {
				$return .= ', ';
			}
		}

		$return .= '</span>';
		$return = $before.$return.$after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}		
	} else {
		return false;
	}
}

/**
 * Outputs the Special Interests HTML
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_accommodation_special_interests($before="",$after="",$echo=true,$post_id=false){
	if(false === $post_id){
		$post_id = get_the_ID();
	}
	$special_interests = get_post_meta( $post_id, 'special_interests', true );
	if ( is_string( $special_interests ) && ! empty( $special_interests ) ) {
		$special_interests = array( $special_interests );
	}
	$return = '';

	if ( ! empty( $special_interests ) && ! is_wp_error( $special_interests ) ) {
		$return .= '<span class="values">';

		foreach ( $special_interests as $i => $special_interest ) {
			$return .= ucwords( str_replace( '_', ' / ', str_replace( '-', ' ', str_replace( '-and-', ' & ', $special_interest ) ) ) );

			if ( ( $i + 1 ) < count( $special_interests ) ) {
				$return .= ', ';
			}
		}

		$return .= '</span>';
		$return = $before.$return.$after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}		
	} else {
		return false;
	}
}

/**
 * Outputs the Friendly HTML
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 * @category 	activity
 */
function lsx_to_accommodation_activity_friendly($before="",$after="",$echo=true){
	$friendly = get_post_meta( get_the_ID(), 'suggested_visitor_types', true );
	if ( is_string( $friendly ) && ! empty( $friendly ) ) {
		$friendly = array( $friendly );
	}
	$return = '';

	if ( ! empty( $friendly ) && ! is_wp_error( $friendly ) ) {
		$return .= '<span class="values">';

		foreach ( $friendly as $i => $friendly_item ) {
			$return .= ucwords( str_replace( '_', ' / ', str_replace( '-', ' ', str_replace( '-and-', ' & ', $friendly_item ) ) ) );

			if ( ( $i + 1 ) < count( $friendly ) ) {
				$return .= ', ';
			}
		}

		$return .= '</span>';
		$return = $before.$return.$after;

		if ( $echo ) {
			echo wp_kses_post( $return );
		} else {
			return $return;
		}		
	} else {
		return false;
	}
}

/**
 * Outputs the accommodation meta
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	accommodation
 */
function lsx_to_accommodation_meta(){
	if('accommodation' === get_post_type()){
	?>
	<div class="accommodation-details meta taxonomies">
		<?php lsx_to_price('<div class="meta info"><span class="price">'.esc_html__('From price','tour-operator').': ','</span></div>'); ?>
		<?php lsx_to_accommodation_room_total('<div class="meta rooms">'.esc_html__('Rooms','tour-operator').': <span>','</span></div>'); ?>
		<?php lsx_to_accommodation_rating('<div class="meta rating">'.esc_html__('Rating','tour-operator').': ','</div>'); ?>
		<?php the_terms( get_the_ID(), 'travel-style', '<div class="meta travel-style">'.esc_html__('Accommodation Style','tour-operator').': ', ', ', '</div>' ); ?>
		<?php the_terms( get_the_ID(), 'accommodation-brand', '<div class="meta accommodation-brand">'.esc_html__('Brand','tour-operator').': ', ', ', '</div>' ); ?>
		<?php the_terms( get_the_ID(), 'accommodation-type', '<div class="meta accommodation-type">'.esc_html__('Type','tour-operator').': ', ', ', '</div>' ); ?>
		<?php lsx_to_accommodation_spoken_languages('<div class="meta spoken_languages">'.esc_html__('Spoken Languages','tour-operator').': <span>','</span></div>'); ?>
		<?php lsx_to_accommodation_activity_friendly('<div class="meta friendly">'.esc_html__('Friendly','tour-operator').': <span>','</span></div>'); ?>
		<?php lsx_to_accommodation_special_interests('<div class="meta special_interests">'.esc_html__('Special Interests','tour-operator').': <span>','</span></div>'); ?>
		<?php lsx_to_connected_destinations('<div class="meta destination">'.esc_html__('Location','tour-operator').': ','</div>'); ?>
	</div>	
<?php } }

/**
 * Gets the current specials connected accommodation
 *
 * @param		$before	| string
 * @param		$after	| string
 * @param		$echo	| boolean
 * @return		string
 *
 * @package 	tour-operator
 * @subpackage	template-tags
 * @category 	connections
 */
function lsx_to_connected_accommodation($before="",$after="",$echo=true){
	lsx_to_connected_items_query('accommodation',get_post_type(),$before,$after,$echo);
}