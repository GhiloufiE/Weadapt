<?php

/**
 * Get Current URL
 */
if ( ! function_exists( 'get_page_id_by_template' ) ) :

	function get_page_id_by_template( $template_name ) {
		$query = new WP_Query( [
			'post_type'  => ['page'],
			'fields'     => 'ids',
			'theme_query' => true, // multisite fix
			'meta_query'  => [
				'key'   => '_wp_page_template',
				'value' => "page-templates/$template_name.php"
			]
		] );

		return ! empty( $query->posts[0] ) ? intval( $query->posts[0] ) : 0;
	}

endif;