<?php
/**
 * Travel Style Widget Content Part
 * 
 * @package 	lsx-tour-operators
 * @category	travel-style
 * @subpackage	widget
 */
global $term,$taxonomy,$disable_placeholder;
?>
<article id="term-<?php echo $term->term_id; ?>" class="term-<?php echo $term->term_id; ?> <?php echo $taxonomy; ?> type-<?php echo $taxonomy; ?> status-publish hentry">
 	<?php if('1' !== $disable_placeholder && true !== $disable_placeholder) { ?>
		<?php if ( lsx_has_term_thumbnail($term->term_id) ) : ?>
			<div class="thumbnail">
				<a href="<?php echo home_url($taxonomy.'/'.$term->slug.'/'); ?>">
					<?php lsx_term_thumbnail( $term->term_id,'lsx-thumbnail-wide' ); ?>
				</a>
			</div>	
		<?php else: ?>	
			<div class="thumbnail">
				<a href="<?php echo home_url($taxonomy.'/'.$term->slug.'/'); ?>">
					<img alt="Placeholder" class="attachment-responsive wp-post-image lsx-responsive" src="<?php echo LSX_Placeholders::placeholder_url() . ( parse_url( LSX_Placeholders::placeholder_url(), PHP_URL_QUERY ) ? '&' : '?' ); ?>w=350&amp;h=230">
				</a>	
			</div>				
		<?php endif; ?>
	<?php } ?>
	
	<h4 class="title"><a href="<?php echo home_url($taxonomy.'/'.$term->slug.'/'); ?>"><?php echo apply_filters('the_title', $term->name); ?></a></h4>
	<?php lsx_term_tagline($term->term_id,'<p class="tagline">','</p>'); ?>
	
	<div class="widget-content">
		<div class="view-more" style="text-align:center;">
			<a href="<?php echo home_url($taxonomy.'/'.$term->slug.'/'); ?>" class="btn btn-primary text-center"><?php _e('View','lsx-tour-operators'); ?> <?php echo apply_filters('the_title', $term->name); ?> <?php _e('tours','lsx-tour-operators'); ?></a>
		</div>	
	</div>
	
</article>