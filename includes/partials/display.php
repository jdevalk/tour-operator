<div class="uix-field-wrapper">
    <?php $class = 'active'; ?>

	<ul class="ui-tab-nav">
		<?php if ( class_exists( 'LSX_Banners' ) ) { ?>
			<li><a href="#ui-placeholders" class="active"><?php esc_html_e( 'Placeholders', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Currencies' ) ) { ?>
			<li><a href="#ui-currencies" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Currencies', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Team' ) ) { ?>
			<li><a href="#ui-team" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Team', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Testimonials' ) ) { ?>
			<li><a href="#ui-testimonials" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Testimonials', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Projects' ) ) { ?>
			<li><a href="#ui-projects" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Projects', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Services' ) ) { ?>
			<li><a href="#ui-services" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Services', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Blog_Customizer' ) ) { ?>
			<li><a href="#ui-blog-customizer" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Blog Customizer (posts widget)', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_Sharing' ) ) { ?>
			<li><a href="#ui-sharing" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( 'Sharing', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_TO_Maps' ) ) { ?>
			<li><a href="#ui-maps" class="<?php echo esc_attr( $class ); ?>"><?php esc_html_e( 'Maps', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<?php if ( class_exists( 'LSX_TO_Search' ) ) { ?>
			<li><a href="#ui-search" class="<?php echo esc_attr( $class ); ?>"><?php esc_html_e( 'Search', 'tour-operator' ); ?></a></li>
		<?php $class = ''; } ?>

		<li><a href="#ui-basic" class="<?php echo esc_attr( $class ); ?>"><?php esc_html_e( 'Basic', 'tour-operator' ); ?></a></li>
		<li><a href="#ui-advanced"><?php esc_html_e( 'Advanced', 'tour-operator' ); ?></a></li>
	</ul>

	<?php $class = 'active'; ?>

	<?php if ( class_exists( 'LSX_Banners' ) ) { ?>
		<div id="ui-placeholders" class="ui-tab <?php echo esc_attr( $class ); ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'placeholders' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_Currencies' ) ) { ?>
		<div id="ui-currencies" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'currency_switcher' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_Team' ) ) { ?>
		<div id="ui-team" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'team' ); ?>
				</tbody>
			</table>
		</div>
	<?php } ?>

	<?php if ( class_exists( 'LSX_Testimonials' ) ) { ?>
		<div id="ui-testimonials" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'testimonials' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_Projects' ) ) { ?>
		<div id="ui-projects" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'projects' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_Services' ) ) { ?>
		<div id="ui-services" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'services' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_Blog_Customizer' ) ) { ?>
		<div id="ui-blog-customizer" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'blog-customizer' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_Sharing' ) ) { ?>
		<div id="ui-sharing" class="ui-tab <?php echo esc_attr( $class ) ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'sharing' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_TO_Maps' ) ) { ?>
		<div id="ui-maps" class="ui-tab <?php echo esc_attr( $class ); ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'maps' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<?php if ( class_exists( 'LSX_TO_Search' ) ) { ?>
		<div id="ui-search" class="ui-tab <?php echo esc_attr( $class ); ?>">
			<table class="form-table">
				<tbody>
					<?php do_action( 'lsx_to_framework_display_tab_content', 'search' ); ?>
				</tbody>
			</table>
		</div>
	<?php $class = ''; } ?>

	<div id="ui-basic" class="ui-tab <?php echo esc_attr( $class ); ?>">
		<table class="form-table">
			<tbody>
				<?php do_action( 'lsx_to_framework_display_tab_content', 'basic' ); ?>
			</tbody>
		</table>
	</div>

	<div id="ui-advanced" class="ui-tab">
		<table class="form-table">
			<tbody>
				<?php do_action( 'lsx_to_framework_display_tab_content', 'advanced' ); ?>
			</tbody>
		</table>
	</div>

	<?php do_action( 'lsx_to_framework_display_tab_bottom', 'display' ); ?>

</div>
