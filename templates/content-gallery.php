<?php
/**
 * Team Content Part
 *
 * @package 	tour-operator
 * @category	team
 */
global $image, $size;
$srcset = wp_get_attachment_image_src( $image->ID, $size );

if ( false !== $srcset ) { ?>
	<figure>
		<?php echo wp_kses_post( apply_filters( 'lsx_to_lazyload_filter_images', '<img title="' . apply_filters( 'the_title', $image->post_title ) . '" src="' . $srcset[0] . '" />' ) ); ?>
	</figure>
<?php } ?>
