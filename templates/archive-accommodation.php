<?php
/**
 * Accommodation Archive
 *
 * @package 	tour-operator
 * @category	accommodation
 */

get_header(); ?>

	<?php to_content_wrap_before(); ?>

	<section id="primary" class="content-area <?php echo esc_attr( to_main_class() ); ?>">

		<?php to_content_before(); ?>

		<main id="main" class="site-main" role="main">

		<?php 
		/**
		 * Hooked
		 * 
		 *  - to_archive_header() - 100
		 *  - to_archive_description() - 100
		 */
			to_content_top();
		?>
		
		<?php if ( have_posts() ) : ?>

			<div class="row">
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="panel col-sm-12">
					<?php to_content( 'content', 'accommodation' ); ?>
				</div>
			<?php endwhile; ?>
			</div>
			<?php //to_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		<?php to_content_bottom(); ?>
		
		<?php to_safari_brands('<section id="safari-brands"><h2 class="section-title">'.__(to_get_post_type_section_title('accommodation', 'brands', 'Accommodation Brands'),'tour-operator').'</h2>','</section>');?>
		
		<?php to_sharing(); ?>

		</main><!-- #main -->

		<?php to_content_after(); ?>
		
	</section><!-- #primary -->

<?php to_content_wrap_after(); ?>

<?php get_sidebar(); ?>

<?php get_footer();