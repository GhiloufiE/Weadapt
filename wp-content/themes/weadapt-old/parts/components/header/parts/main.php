<?php load_blocks_script( 'popup', 'weadapt/popup' ); ?>

<div class="main-header__main-area">
	<div class="container">
		<div class="main-header__logo">
			<a href="<?php echo get_bloginfo( 'url' ); ?>" class="main-logo">
				<span class="screen-reader-text"><?php _e( 'Main Logo', 'weadapt' ); ?></span>
				<?php
					if ( $header_logo = get_field( 'header_logo', 'options' ) ) {
						echo get_img( $header_logo );
					}

					if ( $header_logo_text = get_field( 'header_logo_text', 'options' ) ) {
						echo sprintf( '<span translate="no">%s</span>', get_field( 'header_logo_text', 'options' ) );
					}
				?>
			</a>
		</div>

		<?php echo get_search_form(); ?>

		<?php
			$menu_location = is_user_logged_in() ? 'header-main-menu-logged-in' : 'header-main-menu';

			if ( has_nav_menu( $menu_location ) ) :
		?>
			<nav role="navigation" class="main-header__nav main-header__nav--main mb-popup-content" aria-label="<?php _e( 'Main Navigation', 'weadapt' ); ?>">
				<?php
					wp_nav_menu( [
						'theme_location' => $menu_location,
						'container'      => false,
						'walker'         => new Card_Walker_Nav_Menu()
					] );
				?>
			</nav>
		<?php endif; ?>
	</div>
</div>