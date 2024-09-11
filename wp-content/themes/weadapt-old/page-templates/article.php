<?php
/*
* Template Name: Page Article
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type'             => 'article',
	'query_post_types' => ['article'],
	'show_post_types'  => false,
	'show_filters'     => false
] );

get_footer();