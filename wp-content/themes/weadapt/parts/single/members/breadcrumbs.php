<?php
/**
 * Single Blog Breadcrumbs
 *
 * @package WeAdapt
 */

$breadcrumbs = [];

foreach ( [
	'connect'
] as $template_name ) {
	if ( ! empty( $page_ID = get_page_id_by_template( $template_name ) ) ) {
		$breadcrumbs[] = ['url' => get_permalink( $page_ID ), 'label' => get_the_title( $page_ID ) ];
	}
}
$post_id = get_the_ID();
error_log("post id " . $post_id);

$user_info = get_userdata($post_id);
if ($user_info) {
	$breadcrumbs[] = ['url' => '', 'label' => $user_info->display_name];
} else {
	$breadcrumbs[] = ['url' => '', 'label' => get_the_title($post_id)];
}

get_part('components/breadcrumbs/index', ['breadcrumbs' => $breadcrumbs]);