<?php
/**
 * Single Theme Filter Tabs
 *
 * @package WeAdapt
 */
$user_ID    = ! empty( $args['user_ID'] ) ? intval( $args['user_ID'] ) : 0;
$is_profile = ! empty( $args['is_profile'] ) ? wp_validate_boolean( $args['is_profile'] ) : false;

$items = [
	[
		'id' => 'tab-latest',
		'controls' => 'tab-latest-panel',
		'selected' => true,
		'label' => __( 'Latest', 'weadapt' ),
	],
	[
		'id' => 'tab-about',
		'controls' => 'tab-about-panel',
		'selected' => false,
		'label' => __( 'About', 'weadapt' ),
	]
];

if ( $is_profile ) {
	$items = array_merge( $items, [
		// temp-hide notifications content
		// [
		// 	'id' => 'tab-notifications',
		// 	'controls' => 'tab-notifications-panel',
		// 	'selected' => false,
		// 	'label' => __( 'Notifications', 'weadapt' ),
		// ],
		[
			'id' => 'tab-badges',
			'controls' => 'tab-badges-panel',
			'selected' => false,
			'label' => __( 'Badges', 'weadapt' ),
		],
		[
			'id' => 'tab-created-content',
			'controls' => 'tab-created-content-panel',
			'selected' => false,
			'label' => __( 'Created content', 'weadapt' ),
		],
		[
			'id' => 'tab-bookmarked',
			'controls' => 'tab-bookmarked-panel',
			'selected' => false,
			'label' => __( 'Bookmarked', 'weadapt' ),
		],
	] );

	if ( class_exists( 'Front_End_Pm_Pro' ) ) {
		$items = array_merge( $items, [ [
			'id'         => 'tab-messages',
			'controls'   => 'tab-messages-panel',
			'selected'   => false,
			'label'      => __( 'Messages', 'weadapt' ),
			'attributes' => [
				'data-messages' => function_exists( 'fep_get_new_message_number' ) ? fep_get_new_message_number() : 0
			]
		] ] );
	}
}
else {
	// if ( ! empty( get_field( 'badges', 'user_' . $user_ID ) ) ) {
	// 	$items = array_merge( $items, [
	// 		[
	// 			'id' => 'tab-badges',
	// 			'controls' => 'tab-badges-panel',
	// 			'selected' => false,
	// 			'label' => __( 'Badges', 'weadapt' ),
	// 		]
	// 	] );
	// }
}

get_part( 'components/single-tabs-nav/index', [ 'items' => $items ] );