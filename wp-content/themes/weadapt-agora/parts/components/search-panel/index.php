<?php
	echo load_inline_styles( __DIR__, 'search-panel' );

	$i18n_placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : __('Search...', 'weadapt');

	// The filtering by certain post_types must be finished.
	$post_types = isset( $args['post_types'] ) ? $args['post_types'] : [];
?>

<form role="search" aria-label="<?php esc_attr_e( 'Search Form', 'weadapt' ); ?>" method="GET" class="search-panel" action="<?php echo esc_url( home_url( '/' ) ) ?>">
	<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'weadapt' ); ?></span>
	<input type="search" class="search-panel__field" placeholder="<?php echo $i18n_placeholder; ?>" value="<?php echo get_search_query() ?>" name="s">

	<?php if ( ! empty( $post_types ) ) : ?>
		<?php foreach ( $post_types as $post_type ) : ?>
			<!-- <input type="hidden" value="post-1" name="post_type[]" /> -->
		<?php endforeach; ?>
	<?php endif; ?>

	<button type="submit" class="search-panel__submit" aria-label="<?php esc_attr_e( 'Search', 'weadapt' ); ?>"><?php echo __( 'Search', 'weadapt' ); ?> <?php echo get_img( 'icon-search' ); ?></button>
</form>