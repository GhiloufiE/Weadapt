<?php
/**
 * Archive Blog Aside
 *
 * @package WeAdapt
 */
$post_ID = get_the_ID();

get_part('components/info-widget-cpt/index', [
	'cpt_ID'      => $post_ID,
	'cpt_buttons' => ['follow', 'share', 'view-theme']
]);


get_part('components/cta-widget/index', [
	'template' => 'start_a_discussion'
]);


get_part( 'components/tags/index', ['title' => __( 'Trending tags', 'weadapt' )] );


get_part('components/members-widget/index', [
	'title'     => __( 'Users', 'weadapt' ),
	'more_link' => [get_page_id_by_template( 'people' ), __( 'View all users', 'weadapt' )],
	'members_IDs'   => get_followed_users( $post_ID, 'forums' ),
]);

