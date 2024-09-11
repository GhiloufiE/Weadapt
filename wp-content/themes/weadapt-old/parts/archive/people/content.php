<?php
/**
 * Single Blog Content
 *
 * @package WeAdapt
 */

get_part( 'components/cpt-query/index', [
	'query_type'  => 'user_query',
	'show_search' => true,
	'show_categories' => false,
	'query_args'  => [
		'fields'      => 'ID',
		'number'      => 13,
		'orderby'     => 'user_registered',
		'order'       => 'DESC',
		'theme_query' => true, // multisite fix
		'meta_query'  => [
			'relation' => 'AND',
			[
				'key'     => 'avatar',
				'value'   => 0,
				'compare' => '!='
			],
			[
				'key'     => 'avatar',
				'value'   => '',
				'compare' => '!='
			]
		]
	]
]);