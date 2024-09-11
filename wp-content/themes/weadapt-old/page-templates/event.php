<?php
/*
* Template Name: Page Event
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type'             => 'event',
	'query_post_types' => ['event'],
	'show_post_types'  => false,
	'show_filters'     => false
] );

get_footer();