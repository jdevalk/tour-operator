<?php
/**
 * Frontend actions for the LSX TO Plugin
 *
 * @package   LSX_TO_Frontend
 * @author    LightSpeed
 * @license   GPL3
 * @link      
 * @copyright 2016 LightSpeedDevelopment
 */

/**
 * Main plugin class.
 *
 * @package LSX_TO_Frontend
 * @author  LightSpeed
 */
class LSX_TO_Frontend extends Tour_Operator {

	/**
	 * This holds the class OBJ of LSX_TO_Template_Redirects
	 */
	public $redirects = false;	

	/**
	 * Enable Modals
	 *
	 * @since 1.0.0
	 *
	 * @var      boolean|LSX_TO_Frontend
	 */
	public $enable_modals = false;

	/**
	 * Holds the modal ids for output in the footer
	 *
	 * @since 1.0.0
	 *
	 * @var      array|LSX_TO_Frontend
	 */
	public $modal_ids = array();

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	public function __construct() {
		$this->options = get_option('_lsx-to_settings',false);	
		$this->set_vars();

		add_filter( 'post_class', array( $this, 'replace_class'), 10, 1 );
		add_filter( 'body_class', array( $this, 'replace_class'), 10, 1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylescripts' ) );	
		add_action( 'wp_head', array( $this,'wp_head') , 10 );
		add_filter( 'body_class', array( $this, 'body_class') );

		if(!is_admin()){
			add_filter( 'pre_get_posts', array( $this,'taxonomy_pre_get_posts') , 10, 1 );
			add_action( 'pre_get_posts', array( $this,'team_pre_get_posts') , 10, 1 );
		}	

		add_filter( 'lsx_to_connected_list_item', array( $this,'add_modal_attributes') , 10, 3 );
		add_action( 'wp_footer', array( $this,'output_modals') , 10 );
		add_filter( 'use_default_gallery_style', '__return_false' );
		add_filter('lsx_to_tagline',array($this,'get_tagline'),1,3);

		add_filter( 'the_terms', array( $this,'links_new_window') , 10, 2);		

		if(!class_exists('LSX_TO_Template_Redirects')){
			require_once( LSX_TO_PATH . 'classes/class-template-redirects.php' );
		}
		$this->redirects = new LSX_TO_Template_Redirects(LSX_TO_PATH,array_keys($this->base_post_types),array_keys($this->base_taxonomies));

		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title'),100 );

		//Redirects if disabled
		add_action( 'template_redirect', array( $this, 'redirect_singles') );
		add_action( 'template_redirect', array( $this, 'redirect_archive') );

		//LSX
		add_action( 'lsx_content_before', array( $this, 'remove_jetpack_share' ));
		add_action( 'lsx_content_wrap_before', array( $this, 'remove_jetpack_share' ));

		//Jetpack
		add_filter( 'sharing_show', array( $this, 'show_jetpack_sharing_filter'),2,100 );	

		// Readmore
		add_filter( 'the_content', array( $this, 'modify_read_more_link') );
		remove_filter( 'term_description','wpautop' );
		add_filter( 'term_description', array( $this, 'modify_term_description') );			
	}	

	/**
	 * A filter to replace anything with '-TO_POST_TYPE' by '-lsx-to-TO_POST_TYPE'
	 */
	public function replace_class( $classes ) {
		foreach ( $this->active_post_types as $key1 => $value1 ) {
			foreach ( $classes as $key2 => $value2 ) {
				$classes[ $key2 ] = str_replace( "-{$value1}", "-lsx-to-{$value1}", $value2 );
			}
		}

		return $classes;
	}

	/**
	 * Initate some boolean flags
	 */
	public function wp_head() {
		if((is_singular($this->active_post_types) || is_post_type_archive($this->active_post_types))
			&& false !== $this->options
			&& isset($this->options['display']['enable_modals'])
			&& 'on' === $this->options['display']['enable_modals']){
				$this->enable_modals = true;				
		}

		if((is_post_type_archive($this->active_post_types)) || (is_tax(array_keys($this->taxonomies)))){
			remove_action('lsx_content_wrap_before','lsx_global_header');
			add_action('lsx_content_wrap_before','lsx_to_global_header',100);
			add_action('lsx_content_wrap_before','lsx_to_archive_description',100);
			add_filter('lsx_to_archive_description',array($this,'get_post_type_archive_description'),1,3);
		}
		
		if(is_singular($this->active_post_types)){
			remove_action('lsx_content_wrap_before','lsx_global_header');
			add_action('lsx_content_wrap_before','lsx_to_global_header',100);
		}
		
		if(class_exists('LSX_Banners')){
			remove_action('lsx_content_top', 'lsx_breadcrumbs',100);
			add_action('lsx_banner_container_top', 'lsx_breadcrumbs');
		}		
	}

	/**
	 * a filter to overwrite the links with modal tags.
	 */
	public function add_modal_attributes($html,$post_id,$link) {
		if(true === $this->enable_modals && true === $link){
			$html = '<a data-toggle="modal" data-target="#lsx-modal-'.$post_id.'" href="#">'.get_the_title($post_id).'</a>';
			if(!in_array($post_id,$this->modal_ids)){
				$this->modal_ids[] = $post_id;
			}
		}	
		return $html;
	}

	/**
	 * a filter to overwrite the links with modal tags.
	 */
	public function output_modals() {
		global $lsx_to_archive,$post;
		if(true === $this->enable_modals && !empty($this->modal_ids)){
			$temp = $lsx_to_archive;
			$lsx_to_archive = 1;
			foreach($this->modal_ids as $post_id){
			$post = get_post($post_id);
			?>	
				<div class="lsx-modal modal fade" id="lsx-modal-<?php echo esc_attr( $post_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo get_the_title($post_id); ?>">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-body">
				      	<button type="button" class="close" data-dismiss="modal" aria-label="<?php esc_html_e('Close','tour-operator'); ?>"><span aria-hidden="true">×</span></button>
				        <?php lsx_to_content( 'content', 'modal' ); ?>
				      </div>
				    </div>
				  </div>
				</div>
			<?php
			}
			$lsx_to_archive = $temp;
			wp_reset_postdata();
		}
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @return    null
	 */
	public function enqueue_stylescripts() {
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			$min = '';
		} else {
			$min = '.min';
		}

		if(!isset($this->options['display']['disable_js'])){
			wp_enqueue_script( 'fixto', LSX_TO_URL . 'assets/js/vendor/fixto.min.js', array( 'jquery' ), LSX_TO_VER, true );
			wp_enqueue_script( 'jquery-touchswipe', LSX_TO_URL . 'assets/js/vendor/jquery.touchSwipe.min.js', array( 'jquery' ) , LSX_TO_VER, true );
			wp_enqueue_script( 'tour-operator-script', LSX_TO_URL . 'assets/js/custom.min.js', array( 'jquery' ), LSX_TO_VER, true );
		}
		if(!isset($this->options['display']['disable_css'])){
			wp_enqueue_style( 'tour-operator-style', LSX_TO_URL . 'assets/css/style.css', array(), LSX_TO_VER );
		}
		if(defined('JETPACK__VERSION') && defined('WP_SHARING_PLUGIN_URL')){
			wp_enqueue_style( 'sharing', WP_SHARING_PLUGIN_URL.'sharing.css', false, JETPACK__VERSION );
		}
	}

	/**
	 * Set the main query to pull through only the top level destinations.
	 */
	public function taxonomy_pre_get_posts($query) {
		if($query->is_main_query() && $query->is_tax(array('travel-style'))){
			$query->set('post_type',array('tour','accommodation'));
		}	
		return $query;
	}
	
	/**
	 * Set the Team Archive to infinite posts per page
	 */
	public function team_pre_get_posts($query) {
		if($query->is_main_query() && $query->is_post_type_archive(array('team'))){
			$query->set('posts_per_page',-1);
		}	
		return $query;
	}

	/**
	 * Add a some classes so we can style.
	 */
	public function body_class( $classes ) {	
		global $post;
		if(false !== $this->post_types && is_singular(array_keys($this->post_types))){
			$classes[] = 'single-tour-operator';
		}
		elseif(false !== $this->post_types && is_post_type_archive(array_keys($this->post_types))){
			$classes[] = 'archive-tour-operator';
		}
		elseif(false !== $this->taxonomies && is_tax(array_keys($this->taxonomies))){
			$classes[] = 'archive-tour-operator';
		}
		elseif(is_search()){
			$classes[] = 'archive-tour-operator';
		}
		return $classes;
	}

	/**
	 * add target="_blank" to the travel style links
	 */
	public function links_new_window($terms,$taxonomy) {
		if('travel-style' === $taxonomy || 'accommodation-type' === $taxonomy){
			$terms = str_replace('<a','<a target="_blank"',$terms);
		}	
		return $terms;
	}

	/**
	 * Remove the "Archives:" from the post type archives.
	 *
	 * @param	$title
	 *
	 * @return	$title
	 */
	public function get_the_archive_title($title) {
		if(is_post_type_archive(array_keys($this->post_types))){
			$title = post_type_archive_title( '', false );
		}
		if(is_tax(array_keys($this->taxonomies))){
			$title = single_term_title( '', false );
		}
		return $title;
	}	

	/**
	 * Redirect the single links to the homepage if the single is set to be disabled.
	 *
	 * @param	$template
	 *
	 * @return	$template
	 */
	public function redirect_singles() {
		$queried_post_type = get_query_var('post_type');
		if(is_singular() && false !== $this->options && isset($this->options[$queried_post_type]) && isset($this->options[$queried_post_type]['disable_single'])){
			if ( is_singular($queried_post_type)) {
				wp_redirect( home_url(), 301 );
				exit;
			}
		}
	}
	
	/**
	 * Redirect the archive links to the homepage if the disable archive is set to be disabled.
	 *
	 * @param	$template
	 *
	 * @return	$template
	 */
	public function redirect_archive() {
		$queried_post_type = get_query_var('post_type');
		if(is_post_type_archive() && false !== $this->options && isset($this->options[$queried_post_type]) && isset($this->options[$queried_post_type]['disable_archives'])){
			if ( is_post_type_archive($queried_post_type) ) {
				wp_redirect( home_url(), 301 );
				exit;
			}
		}
	}

	/**
	 *	Removes the Jetpack Sharing so we can add it in manually.
	 */
	public function remove_jetpack_share() {
		
		if(in_array(get_post_type(),$this->post_types)){
			remove_filter( 'the_content', 'sharing_display',19 );
			remove_filter( 'the_excerpt', 'sharing_display',19 );
			if ( class_exists( 'Jetpack_Likes' ) ) {
				remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
			}
		}
	}

	/**
	 * Enables the Jetpack sharing on the TO post types
	 *
	 * @param $show
	 * @param $post
	 * @return bool
	 */
	public function show_jetpack_sharing_filter($show, $post) {
		if(in_array($post->post_type,$this->post_types)){
			$show = true;
		}
		return $show;
	}
	/**
	 * Modify the read more link
	 *
	 * @param 		string $content
	 * @return   	string $content
	 */
	public function modify_read_more_link($content) {
		$content = str_replace('<span id="more-'.get_the_ID().'"></span>','<a class="btn btn-default more-link" data-collapsed="true" href="' . get_permalink() . '">'.esc_html__('Read More','tour-operator').'</a>',$content);
		return $content;
	}

	/**
	 * Modify term_description to use the_content filter
	 *
	 * @param 		string $content
	 * @return   	string $content
	 */
	public function modify_term_description( $content ) {
		$more_link_text = esc_html__('Read More','tour-operator');
		$output = '';

		if ( preg_match( '/<!--more(.*?)?-->/', $content, $matches ) ) {
			$content = explode( $matches[0], $content, 2 );
			
			if ( ! empty( $matches[1] ) && ! empty( $more_link_text ) )
				$more_link_text = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
		} else {
			$content = array( $content );
		}

		$teaser = $content[0];
		$output .= $teaser;

		if ( count( $content ) > 1 ) {
			$output .= "<a class=\"btn btn-default more-link\" data-collapsed=\"true\" href=\"#more-000\">{$more_link_text}</a>" . $content[1];
		}

		$output = apply_filters( 'the_content', $output );
		return $output;
	}			
}