<?php
/**
 * Module Template.
 *
 * @package   Lsx_Destination
 * @author    LightSpeed
 * @license   GPL3
 * @link      
 * @copyright 2016 LightSpeedDevelopment
 */

/**
 * Main plugin class.
 *
 * @package Lsx_Destination
 * @author  LightSpeed
 */
class Lsx_Destination{

	/**
	 * The slug for this plugin
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'destination';

	/**
	 * Holds class instance
	 *
	 * @since 1.0.0
	 *
	 * @var      object|Module_Template
	 */
	protected static $instance = null;

	/**
	 * Holds the option screen prefix
	 *
	 * @since 1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function __construct() {
		// activate property post type
		$temp = get_option('_lsx_lsx-settings',false);
		if(false !== $temp && isset($temp[$this->plugin_slug]) && !empty($temp[$this->plugin_slug])){
			$this->options = $temp[$this->plugin_slug];
		}

		add_action( 'init', array( $this, 'register_post_types' ) );
		add_filter( 'cmb_meta_boxes', array( $this, 'register_metaboxes') );
		
		if(!is_admin()){
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}
		add_filter( 'to_entry_class', array( $this, 'entry_class') );
		
		add_action('to_map_meta',array($this, 'content_meta'));
		add_action('to_modal_meta',array($this, 'content_meta'));		

		add_action( 'to_framework_destination_tab_general_settings_bottom', array($this,'general_settings'), 10 , 1 );	
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return    object|Module_Template    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	
	/**
	 * Register the landing pages post type.
	 *
	 *
	 * @return    null
	 */
	public function register_post_types() {
	
		$labels = array(
		    'name'               => _x( 'Destinations', 'tour-operator' ),
		    'singular_name'      => _x( 'Destination', 'tour-operator' ),
		    'add_new'            => _x( 'Add New', 'tour-operator' ),
		    'add_new_item'       => _x( 'Add New Destination', 'tour-operator' ),
		    'edit_item'          => _x( 'Edit Destination', 'tour-operator' ),
		    'new_item'           => _x( 'New Destination', 'tour-operator' ),
		    'all_items'          => _x( 'All Destinations', 'tour-operator' ),
		    'view_item'          => _x( 'View Destination', 'tour-operator' ),
		    'search_items'       => _x( 'Search Destinations', 'tour-operator' ),
		    'not_found'          => _x( 'No destinations found', 'tour-operator' ),
		    'not_found_in_trash' => _x( 'No destinations found in Trash', 'tour-operator' ),
		    'parent_item_colon'  => '',
		    'menu_name'          => _x( 'Destinations', 'tour-operator' )
		);

		$args = array(
            'menu_icon'          =>'dashicons-admin-site',
		    'labels'             => $labels,
		    'public'             => true,
		    'publicly_queryable' => true,
		    'show_ui'            => true,
		    'show_in_menu'       => true,
		    'query_var'          => true,
		    'rewrite'            => array('slug'=>'destination'),
		    'capability_type'    => 'page',
		    'has_archive'        => 'destinations',
		    'hierarchical'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields','page-attributes' )
		);

		register_post_type( $this->plugin_slug, $args );	
		
	}
	
	function register_metaboxes( array $meta_boxes ) {

		$fields[] = array( 'id' => 'featured',  'name' => 'Featured', 'type' => 'checkbox' );

		if(!class_exists('TO_Banners')){
			$fields[] = array( 'id' => 'tagline',  'name' => 'Tagline', 'type' => 'text' );
		}
		$fields[] = array( 'id' => 'team_to_destination', 'name' => 'Destination Expert', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'team','nopagin' => true,'posts_per_page' => 1000, 'orderby' => 'title', 'order' => 'ASC' ), 'allow_none'=>true, 'cols' => 12, 'allow_none' => true );

		if(!class_exists('TO_Maps')){
			$fields[] = array( 'id' => 'location',  'name' => 'Location', 'type' => 'gmap' );	
		}
		
		$fields[] = array( 'id' => 'connections_title',  'name' => 'Connections', 'type' => 'title' );
		if(post_type_exists('accommodation')){
			$fields[] = array( 'id' => 'accommodation_to_destination', 'name' => 'Accommodations related with this destination', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'accommodation','nopagin' => true,'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'ASC' ), 'repeatable' => true, 'sortable' => true );
		}
		if(post_type_exists('activity')){
			$fields[] = array( 'id' => 'activity_to_destination', 'name' => 'Activities related with this destination', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'activity','nopagin' => true,'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'ASC' ), 'repeatable' => true, 'sortable' => true );
		}
		if(post_type_exists('special')){		
			$fields[] = array( 'id' => 'special_to_destination', 'name' => 'Specials related with this destination', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'special','nopagin' => true,'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'ASC' ), 'repeatable' => true, 'sortable' => true );
		}
		if(post_type_exists('review')){		
			$fields[] = array( 'id' => 'review_to_destination', 'name' => 'Reviews related with this destination', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'review','nopagin' => true,'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'ASC' ), 'repeatable' => true, 'sortable' => true );
		}
		if(post_type_exists('tour')){		
			$fields[] = array( 'id' => 'tour_to_destination', 'name' => 'Tours related with this destination', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'tour','nopagin' => true,'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'ASC' ), 'repeatable' => true, 'sortable' => true );
		}
		if(post_type_exists('vehicle')){		
			$fields[] = array( 'id' => 'vehicle_to_destination', 'name' => 'Vehicles related with this destination', 'type' => 'post_select', 'use_ajax' => false, 'query' => array( 'post_type' => 'vehicle','nopagin' => true,'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'ASC' ), 'repeatable' => true, 'sortable' => true );
		}

		//Allow the addons to add additional fields.
		$fields = apply_filters('to_destination_custom_fields',$fields);
		
		$meta_boxes[] = array(
				'title' => 'LSX Tour Operators',
				'pages' => 'destination',
				'fields' => $fields
		);		
	
		return $meta_boxes;
	}
	
	/**
	 * Set the main query to pull through only the top level destinations.
	 *
	 */
	function pre_get_posts($query) {
		if($query->is_main_query() && $query->is_post_type_archive($this->plugin_slug)){
			$query->set('post_parent','0');
		}
		return $query;
	}
	
	/**
	 * A filter to set the content area to a small column on single
	 */
	public function entry_class( $classes ) {
		global $to_archive;
		if(1 !== $to_archive){$to_archive = false;}
		if(is_main_query() && is_singular($this->plugin_slug) && false === $to_archive){
			$classes[] = 'col-sm-9';
		}
		return $classes;
	}

	/**
	 * Displays the destination specific settings
	 */
	public function general_settings( $post_type ) {
		?>
			<tr class="form-field -wrap">
				<th scope="row">
					<label for="description">Display the map in the banner</label>
				</th>
				<td>
					<input type="checkbox"  {{#if enable_banner_map}} checked="checked" {{/if}} name="enable_banner_map" />
				</td>
			</tr>
		<?php
	}


	/**
	 * Outputs the destination meta
	 */
	public function content_meta(){ 
		if('destination' === get_post_type()){
		?>
		<div class="destination-details meta taxonomies">
			<?php the_terms( get_the_ID(), 'travel-style', '<div class="meta travel-style">'.__('Travel Style','tour-operator').': ', ', ', '</div>' ); ?>				
			<?php to_connected_activities('<div class="meta activities">'.__('Activites','tour-operator').': ','</div>'); ?>				
		</div>
	<?php } }		
}
$to_destination = Lsx_Destination::get_instance();