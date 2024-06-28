<?php
/**
 * Single Blog Aside
 *
 * @package WeAdapt
 */

$people = get_field( 'people' );

$contributors_IDs = ! empty( $people['contributors'] ) ? $people['contributors'] : [];

if ( ! empty( $contributors_IDs ) ) {
	foreach ( $contributors_IDs as $contributors_ID ) {
		get_part('components/info-widget-user/index', [
			'user_ID' => $contributors_ID,
		]);
	}
}

get_part('components/tags/index', ['title' => __( 'Tags', 'weadapt' )]);

get_part('components/single-published/index');