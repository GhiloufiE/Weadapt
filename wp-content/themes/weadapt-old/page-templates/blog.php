<?php
/*
* Template Name: Page Blog
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type'             => 'blog',
	'query_post_types' => ['blog'],
	'show_post_types'  => false,
	'show_filters'     => false
] );

get_footer();