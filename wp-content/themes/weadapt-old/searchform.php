<form role="search" aria-label="<?php esc_attr_e( 'Search Form', 'weadapt' ); ?>" method="GET" class="search-form mb-popup-content" action="<?php echo esc_url( home_url( '/' ) ) ?>">
	<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'weadapt' ); ?></span>
	<input type="search" class="search-form__field" placeholder="<?php esc_attr_e( 'Search', 'weadapt' ); ?>" value="<?php echo get_search_query() ?>" name="s">
	<button type="submit" class="search-form__submit" aria-label="<?php esc_attr_e( 'Search', 'weadapt' ); ?>"><?php echo get_img( 'icon-search' ); ?></button>
	<button type="reset" class="search-form__reset" aria-label="<?php esc_attr_e( 'Reset', 'weadapt' ); ?>"><?php echo get_img( 'icon-close' ); ?></button>
	<span class="search-form__loader"><?php echo get_img( 'loader' ); ?></span>
</form>

<div class="search-form__content__wrap mb-popup-content active">
	<div class="container">
		<div class="search-form__content"></div>
	</div>
</div>