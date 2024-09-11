<?php
/**
 * Single Theme Breadcrumbs
 *
 * @package WeAdapt
 */

$breadcrumbs = [];

foreach ( [
	'learn', 'theme'
] as $template_name ) {
	if ( ! empty( $page_ID = get_page_id_by_template( $template_name ) ) ) {
		$breadcrumbs[] = ['url' => get_permalink( $page_ID ), 'label' => get_the_title( $page_ID ) ];
	}
}
$breadcrumbs[] = ['url' => '' , 'label' => get_the_title() ];

get_part('components/breadcrumbs/index', ['breadcrumbs' => $breadcrumbs]);