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

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_stylescripts' ), 11 );
		add_action( 'wp_head', array( $this,'wp_head') , 10 );
		add_filter( 'body_class', array( $this, 'body_class') );

		if(!is_admin()){
			add_filter( 'pre_get_posts', array( $this,'taxonomy_pre_get_posts') , 10, 1 );
			add_action( 'pre_get_posts', array( $this,'team_pre_get_posts') , 10, 1 );
		}

		add_filter( 'lsx_to_connected_list_item', array( $this,'add_modal_attributes') , 10, 3 );
		add_action( 'wp_footer', array( $this,'output_modals') , 10 );
		add_filter( 'use_default_gallery_style', '__return_false' );
		add_filter( 'lsx_to_tagline', array($this,'get_tagline'),1,3);

		add_filter( 'the_terms', array( $this,'links_new_window') , 10, 2);

		$this->redirects = new LSX_TO_Template_Redirects(LSX_TO_PATH,array_keys($this->base_post_types),array_keys($this->base_taxonomies));

		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title'),100 );

		//Redirects if disabled
		add_action( 'template_redirect', array( $this, 'redirect_singles') );
		add_action( 'template_redirect', array( $this, 'redirect_archive') );

		// Readmore
		add_filter( 'the_content', array( $this, 'modify_read_more_link') );
		remove_filter( 'term_description','wpautop' );
		add_filter( 'term_description', array( $this, 'modify_term_description') );

		if ( is_admin() ) {
			add_filter( 'lsx_customizer_colour_selectors_body', array( $this, 'customizer_to_body_colours_handler' ), 15, 2 );
		}
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
		if ( ( is_singular( $this->active_post_types ) || is_post_type_archive( $this->active_post_types ) )
			&& false !== $this->options
			&& isset( $this->options['display']['enable_modals'] )
			&& 'on' === $this->options['display']['enable_modals'] ) {
				$this->enable_modals = true;
		}

		if ( ( is_post_type_archive( $this->active_post_types ) ) || ( is_tax( array_keys( $this->taxonomies ) ) ) ) {
			if ( ! class_exists( 'LSX_Banners' ) ) {
				remove_action( 'lsx_content_wrap_before', 'lsx_global_header' );
				add_action( 'lsx_content_wrap_before', 'lsx_to_global_header', 100 );
			}

			add_action( 'lsx_content_wrap_before', 'lsx_to_archive_description', 100 );
			add_filter( 'lsx_to_archive_description', array( $this, 'get_post_type_archive_description' ), 1, 3 );

			// LSX default pagination
			add_action( 'lsx_content_bottom', array( 'LSX_TO_Frontend', 'lsx_default_pagination' ) );
		}

		if ( is_singular( $this->active_post_types ) ) {
			if ( ! class_exists( 'LSX_Banners' ) ) {
				remove_action( 'lsx_content_wrap_before', 'lsx_global_header' );
				add_action( 'lsx_content_wrap_before', 'lsx_to_global_header', 100 );
			}
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
		$current_theme = wp_get_theme();
		$current_template = $current_theme->get_template();
		$theme_name = $current_theme->get( 'Name' );
		$is_lsx_theme = 'lsx' === $current_template || 'LSX' === $theme_name;

		$has_font_awesome = $is_lsx_theme ? true : wp_script_is( 'font_awesome', 'queue' ) || wp_script_is( 'fontawesome', 'queue' );
		$has_bootstrap = $is_lsx_theme ? true : wp_script_is( 'bootstrap', 'queue' );
		$has_slick = wp_script_is( 'slick', 'queue' );

		if ( ! isset( $this->options['display']['disable_js'] ) ) {
			$js_dependencies = array( 'jquery', 'fixto' );

			if ( ! $has_slick ) {
				array_push( $js_dependencies, 'slick' );
				wp_enqueue_script( 'slick', LSX_TO_URL . 'assets/js/vendor/slick.min.js', array( 'jquery' ) , LSX_TO_VER, true );
			}

			wp_enqueue_script( 'fixto', LSX_TO_URL . 'assets/js/vendor/fixto.min.js', array( 'jquery' ), LSX_TO_VER, true );
			wp_enqueue_script( 'tour-operator-script', LSX_TO_URL . 'assets/js/custom.min.js', $js_dependencies, LSX_TO_VER, true );
		}

		if ( ! isset( $this->options['display']['disable_css'] ) ) {
			$css_dependencies = array();

			if ( ! $is_lsx_theme ) {

				if ( ! $has_font_awesome ) {
					array_push( $css_dependencies, 'font_awesome' );
					wp_enqueue_style( 'font_awesome', LSX_TO_URL . 'assets/css/vendor/font-awesome.css', array(), LSX_TO_VER );
					wp_style_add_data( 'font_awesome', 'rtl', 'replace' );
				}

				// TODO
				// if ( ! $has_bootstrap ) {
				// 	array_push( $css_dependencies, 'bootstrap' );
				// 	wp_enqueue_style( 'bootstrap', LSX_TO_URL . '', array(), LSX_TO_VER );
				//  wp_style_add_data( 'bootstrap', 'rtl', 'replace' );
				// }
			}

			if ( ! $has_slick ) {
				array_push( $css_dependencies, 'slick' );
				wp_enqueue_style( 'slick', LSX_TO_URL . 'assets/css/vendor/slick.css', array(), LSX_TO_VER );
			}

			wp_enqueue_style( 'tour-operator-style', LSX_TO_URL . 'assets/css/style.css', $css_dependencies, LSX_TO_VER );
			wp_style_add_data( 'tour-operator-style', 'rtl', 'replace' );
		}

		if ( defined( 'JETPACK__VERSION' ) && defined( 'WP_SHARING_PLUGIN_URL' ) ) {
			wp_enqueue_style( 'sharing', WP_SHARING_PLUGIN_URL . 'sharing.css', false, JETPACK__VERSION );
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

	/**
	 * Outputs LSX default pagination.
	 *
	 */
	public static function lsx_default_pagination() {
		lsx_paging_nav();
	}

	/**
	 * Handle body colours that might be change by LSX Customiser
	 */
	public function customizer_to_body_colours_handler( $css, $colors ) {
		$css .= '
			@import "' . LSX_TO_PATH . '/assets/css/scss/customizer-to-body-colours";

			/**
			 * LSX Customizer - Body (Tour Operators)
			 */
			@include customizer-to-body-colours (
				$breaker:   ' . $colors['body_line_color'] . ',
				$color:    	' . $colors['body_text_color'] . ',
				$link:    	' . $colors['body_link_color'] . ',
				$hover:    	' . $colors['body_link_hover_color'] . ',
				$small:    	' . $colors['body_text_small_color'] . '
			);
		';

		return $css;
	}
}
