<?php
/**
 * Single Blog Breadcrumbs
 *
 * @package WeAdapt
 */

$breadcrumbs = [];

foreach ( [
	'learn', get_post_type()
] as $template_name ) {
	if ( ! empty( $page_ID = get_page_id_by_template( $template_name ) ) ) {
		$breadcrumbs[] = ['url' => get_permalink( $page_ID ), 'label' => get_the_title( $page_ID ) ];
	}
}
$breadcrumbs[] = ['url' => '' , 'label' => get_the_title() ];

