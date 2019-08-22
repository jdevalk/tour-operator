<?php
/**
 * The Trip Schema for Tours
 *
 * @package tour-operator
 */

/**
 * Returns schema Review data.
 *
 * @since 10.2
 */
class LSX_TO_Schema_Trip implements WPSEO_Graph_Piece {

	/**
	 * A value object with context variables.
	 *
	 * @var \WPSEO_Schema_Context
	 */
	private $context;

	/**
	 * Constructor.
	 *
	 * @param \WPSEO_Schema_Context $context A value object with context variables.
	 */
	public function __construct( WPSEO_Schema_Context $context ) {
		$this->context = $context;
	}

	/**
	 * Determines whether or not a piece should be added to the graph.
	 *
	 * @return bool
	 */
	public function is_needed() {
		if ( ! is_singular() ) {
			return false;
		}

		if ( $this->context->site_represents === false ) {
			return false;
		}

		return self::is_tour_post_type( get_post_type() );
	}

	/**
	 * Returns Review data.
	 *
	 * @return array $data Review data.
	 */
	public function generate() {
		$post          = get_post( $this->context->id );
		$tour_author   = get_post_meta( $post->ID, 'tourer_name', true );
		$tour_email    = get_post_meta( $post->ID, 'tourer_email', true );
		$comment_count = get_comment_count( $this->context->id );
		$data          = array(
			'@type'            => 'Trip',
			'@id'              => $this->context->canonical . '#tour',
			'author'           => array(
				'@type' => 'Person',
				'@id'   => $this->get_tour_author_schema_id( $tour_author, $tour_email, $this->context ),
				'name'  => $tour_author,
				'email' => $tour_email,
			),
			'headline'         => get_the_title(),
			'datePublished'    => mysql2date( DATE_W3C, $post->post_date_gmt, false ),
			'dateModified'     => mysql2date( DATE_W3C, $post->post_modified_gmt, false ),
			'commentCount'     => $comment_count['approved'],
			'mainEntityOfPage' => array(
				'@id' => $this->context->canonical . WPSEO_Schema_IDs::WEBPAGE_HASH,
			),
		);

		if ( $this->context->site_represents_reference ) {
			$data['publisher'] = $this->context->site_represents_reference;
		}

		$data = $this->add_image( $data );
		$data = $this->add_keywords( $data );
		$data = $this->add_sections( $data );

		return $data;
	}

	/**
	 * Determines whether a given post type should have Review schema.
	 *
	 * @param string $post_type Post type to check.
	 *
	 * @return bool True if it has tour schema, false if not.
	 */
	public static function is_tour_post_type( $post_type = null ) {
		if ( is_null( $post_type ) ) {
			$post_type = get_post_type();
		}

		/**
		 * Filter: 'wpseo_schema_tour_post_types' - Allow changing for which post types we output Review schema.
		 *
		 * @api string[] $post_types The post types for which we output Review.
		 */
		$post_types = apply_filters( 'wpseo_schema_tour_post_types', array( 'tour' ) );

		return in_array( $post_type, $post_types );
	}

	/**
	 * Adds tags as keywords, if tags are assigned.
	 *
	 * @param array $data Review data.
	 *
	 * @return array $data Review data.
	 */
	private function add_keywords( $data ) {
		/**
		 * Filter: 'wpseo_schema_tour_keywords_taxonomy' - Allow changing the taxonomy used to assign keywords to a post type Review data.
		 *
		 * @api string $taxonomy The chosen taxonomy.
		 */
		$taxonomy = apply_filters( 'wpseo_schema_tour_keywords_taxonomy', 'post_tag' );

		return $this->add_terms( $data, 'keywords', $taxonomy );
	}

	/**
	 * Adds categories as sections, if categories are assigned.
	 *
	 * @param array $data Review data.
	 *
	 * @return array $data Review data.
	 */
	private function add_sections( $data ) {
		/**
		 * Filter: 'wpseo_schema_tour_sections_taxonomy' - Allow changing the taxonomy used to assign keywords to a post type Review data.
		 *
		 * @api string $taxonomy The chosen taxonomy.
		 */
		$taxonomy = apply_filters( 'wpseo_schema_tour_sections_taxonomy', 'category' );

		return $this->add_terms( $data, 'tourSection', $taxonomy );
	}

	/**
	 * Adds a term or multiple terms, comma separated, to a field.
	 *
	 * @param array  $data     Review data.
	 * @param string $key      The key in data to save the terms in.
	 * @param string $taxonomy The taxonomy to retrieve the terms from.
	 *
	 * @return mixed array $data Review data.
	 */
	private function add_terms( $data, $key, $taxonomy ) {
		$terms = get_the_terms( $this->context->id, $taxonomy );
		if ( is_array( $terms ) ) {
			$keywords = array();
			foreach ( $terms as $term ) {
				// We are checking against the WordPress internal translation.
				// @codingStandardsIgnoreLine
				if ( $term->name !== __( 'Uncategorized' ) ) {
					$keywords[] = $term->name;
				}
			}
			$data[ $key ] = implode( ',', $keywords );
		}

		return $data;
	}

	/**
	 * Adds an image node if the post has a featured image.
	 *
	 * @param array $data The Review data.
	 *
	 * @return array $data The Review data.
	 */
	private function add_image( $data ) {
		if ( $this->context->has_image ) {
			$data['image'] = array(
				'@id' => $this->context->canonical . WPSEO_Schema_IDs::PRIMARY_IMAGE_HASH,
			);
		}

		return $data;
	}

	/**
	 * Retrieve a users Schema ID.
	 *
	 * @param string               $name The Name of the Reviewer you need a for.
	 * @param WPSEO_Schema_Context $context A value object with context variables.
	 *
	 * @return string The user's schema ID.
	 */
	public function get_tour_author_schema_id( $name, $email, $context ) {
		return $context->site_url . WPSEO_Schema_IDs::PERSON_HASH . wp_hash( $name . $email );
	}

	/**
	 * Generates the itemReviewed schema
	 *
	 * @param  array  $items The array of IDS.
	 * @param  string $type The schema type.
	 * @return array $schema An array of the schema markup.
	 */
	public function get_item_toured_schema( $items = array(), $type = '' ) {
		$schema = array();
		if ( false !== $items && ! empty( $items ) && '' !== $type ) {
			foreach ( $items as $item ) {
				$title       = get_the_title( $item );
				$item_schema = array(
					'@type' => $type,
					'name'  => $title,
				);
				$schema[]    = $item_schema;
			}
		}
		return $schema;
	}
}
