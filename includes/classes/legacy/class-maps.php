<?php
/**
 * Maps Class
 *
 * @package   Destination
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2019 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Destination
 * @author  LightSpeed
 */
class Maps {

	/**
	 * Holds instances of the class
	 *
	 * @var object \lsx\legacy\Maps()
	 */
	protected static $instance;

	/**
	 * If the maps are enabled.
	 *
	 * @var bool
	 */
	public $maps_enabled = false;	

	/**
	 * Holds the value of the current marker
	 *
	 * @var bool
	 */
	public $current_marker = false;

	/**
	 * If the map placeholder is enabled.
	 *
	 * @var bool
	 */
	public $placeholder_enabled = false;

	/**
	 * If the map should display in the destinations banner.
	 *
	 * @var bool
	 */
	public $enable_banner_map = false;

	/**
	 * The post types this post should work with
	 *
	 * @var array
	 */
	public $post_types = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ), 1499 );
		$this->post_types = array(
			'destination',
			'accommodation',
			'tour',
		);
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Enques the assets
	 */
	public function assets() {
		$settings = tour_operator()->options;
		$api_key = '';
		if ( isset( $settings->google_api_key ) ) {
			$api_key = $settings->google_api_key;
		}
		if ( isset( $settings['destination'] ) && isset( $settings['destination']['enable_banner_map'] ) && 'on' === $settings['destination']['enable_banner_map'] ) {
			$this->enable_banner_map = true;
		}
		if ( isset( $settings['display'] ) && isset( $settings['display']['map_placeholder_enabled'] ) && 'on' === $settings['display']['map_placeholder_enabled'] ) {
			if ( ( is_post_type_archive( 'destination' ) || is_singular( 'destination' ) ) && true === $this->enable_banner_map ) {
				$this->placeholder_enabled = false;
			} else {
				$this->placeholder_enabled = true;
			}
		}

		wp_enqueue_script( 'googlemaps_api', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&libraries=places', array( 'jquery' ), null, true );
		wp_enqueue_script( 'googlemaps_api_markercluster', LSX_TO_URL . '/assets/js/vendor/google-markerCluster.js', array( 'googlemaps_api' ), null, true );
		if ( defined( 'SCRIPT_DEBUG' ) ) {
			$prefix = 'src/';
			$suffix = '';
		} else {
			$prefix = '';
			$suffix = '.min';
		}
		wp_enqueue_script( 'lsx_to_maps', LSX_TO_URL . '/assets/js/' . $prefix . 'maps' . $suffix . '.js', array( 'jquery', 'googlemaps_api', 'googlemaps_api_markercluster' ), null, true );
		if ( property_exists( tour_operator()->markers, 'start' ) && property_exists( tour_operator()->markers, 'end' ) ) {
			wp_localize_script( 'lsx_to_maps', 'lsx_to_maps_params', array(
				'apiKey' => $api_key,
				'start_marker' => tour_operator()->markers->start,
				'end_marker' => tour_operator()->markers->end,
				'placeholder_enabled' => $this->placeholder_enabled,
				'enable_banner_map' => $this->enable_banner_map,
			) );
		}
	}

	/**
	 * Output the Map
	 *
	 * @since 1.0.0
	 *
	 * @return    null
	 */
	public function map_output( $post_id = false, $args = array() ) {
		$defaults = array(
			'lat' => '-33.914482',
			'long' => '18.3758789',
			'zoom' => 14,
			'width' => '100%',
			'height' => '400px',
			'type' => 'single',
			'search' => '',
			'connections' => false,
			'link' => true,
			'selector' => '.lsx-map-preview',
			'icon' => false,
			'content' => 'address',
			'kml' => false,
			'cluster_small'		=> tour_operator()->markers->cluster_small,
			'cluster_medium'	=> tour_operator()->markers->cluster_medium,
			'cluster_large'		=> tour_operator()->markers->cluster_large,
			'fusion_tables' => false,
			'fusion_tables_colour_border' => '#000000',
			'fusion_tables_width_border' => '2',
			'fusion_tables_colour_background' => '#000000',
			'disable_cluster_js' => false,
			'disable_auto_zoom' => false,
		);

		$args        = wp_parse_args( $args, $defaults );
		$map_classes = array( 'lsx-map' );
		if ( true === $args['disable_auto_zoom'] ) {
			$map_classes[] = 'disable-auto-zoom';
		}
		if ( true === $this->placeholder_enabled ) {
			$map_classes[] = 'map-has-placeholder';
		}

		$thumbnail = '';
		if ( false === $args['icon'] ) {
			$icon = $this->set_icon( $post_id );
		}

		if ( ( '-33.914482' !== $args['lat'] && '18.3758789' !== $args['long'] ) || false !== $args['search'] || 'cluster' === $args['type'] || 'route' === $args['type'] ) {
			$map = '<div class="' . implode( ' ', $map_classes ) . '" data-zoom="' . $args['zoom'] . '" data-icon="' . $icon . '" data-type="' . $args['type'] . '" data-class="' . $args['selector'] . '" data-fusion-tables-colour-border="' . $args['fusion_tables_colour_border'] . '" data-fusion-tables-width-border="' . $args['fusion_tables_width_border'] . '" data-fusion-tables-colour-background="' . $args['fusion_tables_colour_background'] . '"';

			if ( 'route' === $args['type'] && false !== $args['kml'] ) {
				$map .= ' data-kml="' . $args['kml'] . '"';
			}

			$map .= ' data-lat="' . $args['lat'] . '" data-long="' . $args['long'] . '"';

			if ( false === $args['disable_cluster_js'] ) {
				$map .= ' data-cluster-small="' . $args['cluster_small'] . '" data-cluster-medium="' . $args['cluster_medium'] . '" data-cluster-large="' . $args['cluster_large'] . '"';
			}

			$map .= '>';

			$map .= '<div class="lsx-map-preview" style="width:' . $args['width'] . ';height:' . $args['height'] . ';background-color: #D8D8D8;">';
			if ( true === $this->placeholder_enabled ) {
				$map .= '<img class="lsx-map-placeholder" src="' . $this->get_map_preview_src() . '" style="cursor:pointer;width:' . $args['width'] . ';height:' . $args['height'] . ';" />';
			}
			$map .= '</div>';

			$map .= '<div class="lsx-map-markers" style="display:none;">';

			if ( 'single' === $args['type'] ) {
				$thumbnail = get_the_post_thumbnail_url( $post_id, array( 100, 100 ) );
				$tooltip   = $args['search'];

				if ( 'excerpt' === $args['content'] ) {
					$tooltip = strip_tags( get_the_excerpt( $post_id ) );
				}

				$icon = $this->set_icon( $post_id );

				$map .= '<div class="map-data" data-icon="' . $icon . '" data-id="' . $post_id . '" data-long="' . $args['long'] . '" data-lat="' . $args['lat'] . '" data-thumbnail="' . $thumbnail . '" data-link="' . get_permalink( $post_id ) . '" data-title="' . get_the_title( $post_id ) . '" data-fusion-tables="' . ( true === $args['fusion_tables'] ? '1' : '0' ) . '">
							<p style="line-height: 20px;">' . $tooltip . '</p>
						</div>';
			} elseif ( ( 'cluster' === $args['type'] || 'route' === $args['type'] ) && false !== $args['connections'] ) {
				if ( ! is_array( $args['connections'] ) ) {
					$args['connections'] = array( $args['connections'] );
				}

				foreach ( $args['connections'] as $connection ) {
					$location = get_post_meta( $connection, 'location', true );

					if ( false !== $location && '' !== $location && is_array( $location ) ) {
						$thumbnail = '';

						if ( '' !== $location['long'] && '' !== $location['lat'] ) {
							$thumbnail = get_the_post_thumbnail_url( $connection, array( 100, 100 ) );

							$this->current_marker = $connection;

							$tooltip = $location['address'];

							if ( 'excerpt' === $args['content'] ) {
								$tooltip = strip_tags( get_the_excerpt( $connection ) );
							}

							$icon = $this->set_icon( $connection );

							$map .= '<div class="map-data" data-icon="' . $icon . '" data-id="' . $connection . '" data-long="' . $location['long'] . '" data-lat="' . $location['lat'] . '" data-link="' . get_permalink( $connection ) . '" data-thumbnail="' . $thumbnail . '" data-title="' . get_the_title( $connection ) . '" data-fusion-tables="' . ( true === $args['fusion_tables'] ? '1' : '0' ) . '">';

							global $post;
							$post = get_post( $connection );
							setup_postdata( $post );

							ob_start();
							lsx_to_content( 'content', 'map-marker' );
							wp_reset_postdata();
							$tooltip = ob_get_clean();

							$map .= $tooltip;
							$map .= '</div>';
						}
					}
				}
			}

			$map .= '</div>';
			$map .= '</div>';

			return $map;
		}
	}

	/**
	 * Returns the map marker.
	 *
	 * @param boolean $post_id
	 * @return mixed
	 */
	public function set_icon( $post_id = false ) {
		$settings = tour_operator()->options;
		$icon = tour_operator()->markers->default_marker;
		if ( false !== $post_id ) {
			$connection_type = get_post_type( $post_id );
			$to_post_types = array_keys( lsx_to_get_post_types() );
			if ( in_array( $connection_type, $to_post_types ) ) {
				if ( isset( tour_operator()->markers->post_types[ $connection_type ] ) ) {
					$icon = tour_operator()->markers->post_types[ $connection_type ];
				}
			} else {
				$icon = apply_filters( 'lsx_to_default_map_marker', tour_operator()->markers->default_marker );
			}
		}
		return $icon;
	}

	/**
	 * Gets the Map Preview image src.
	 */
	public function get_map_preview_src() {
		$settings = tour_operator()->options;
		$image = LSX_TO_URL . 'assets/img/placeholders/placeholder-map.svg';
		if ( isset( $settings['display'] ) && isset( $settings['display']['map_placeholder'] ) && '' !== $settings['display']['map_placeholder'] ) {
			$image = $settings['display']['map_placeholder'];
		}
		if ( is_post_type_archive( $this->post_types ) || is_singular( $this->post_types ) ) {
			if ( isset( $settings[ get_post_type() ] ) && isset( $settings[ get_post_type() ]['map_placeholder'] ) && '' !== $settings[ get_post_type() ]['map_placeholder'] ) {
				$image = $settings[ get_post_type() ]['map_placeholder'];
			}
		}
		$image = apply_filters( 'lsx_to_map_preview_src', $image );
		return $image;
	}

}
