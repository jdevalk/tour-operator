<?php
/**
 * Accommodation Widget Content Part
 * 
 * @package 	tour-operator
 * @category	accommodation-type
 * @subpackage	widget
 */
global $term,$taxonomy,$disable_placeholder;
?>
<article id="term-<?php echo esc_attr( $term->term_id ); ?>" class="term-<?php echo esc_attr( $term->term_id ); ?> <?php echo esc_attr( $taxonomy ); ?> type-<?php echo esc_attr( $taxonomy ); ?> status-publish hentry">
 	<?php if('1' !== $disable_placeholder && true !== $disable_placeholder) { ?>
		<?php if ( to_has_term_thumbnail($term->term_id) ) : ?>
			<div class="thumbnail">
				<a href="<?php echo esc_url( home_url($taxonomy.'/'.$term->slug.'/') ); ?>">
					<?php to_term_thumbnail( $term->term_id,'lsx-thumbnail-wide' ); ?>
				</a>
			</div>	
		<?php else: ?>	
			<div class="thumbnail">
				<a href="<?php echo esc_url( home_url($taxonomy.'/'.$term->slug.'/') ); ?>">
					<img alt="Placeholder" class="attachment-responsive wp-post-image lsx-responsive" src="<?php echo esc_attr( LSX_TO_PATHPlaceholders::placeholder_url() . ( parse_url( LSX_TO_PATHPlaceholders::placeholder_url(), PHP_URL_QUERY ) ? '&' : '?' ) ); ?>w=350&amp;h=230">
				</a>	
			</div>				
		<?php endif; ?>	
	<?php } ?>
	
	<h4 class="title"><a href="<?php echo esc_url( home_url($taxonomy.'/'.$term->slug.'/') ); ?>"><?php echo esc_html( apply_filters('the_title', $term->name) ); ?></a></h4>
</article>