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
				?>
			</a>
		</div>

		<div class="main-header__button__list">
			<button class="main-header__button main-header__button--search" aria-label="<?php _e( 'Search', 'weadapt' ); ?>" data-popup="search-opened">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<button class="main-header__button main-header__button--menu" aria-label="<?php _e( 'Menu', 'weadapt' ); ?>" data-popup="menu-opened">
				<span></span>
				<span></span>
				<span></span>
			</button>
		</div>
	</div>
</div>