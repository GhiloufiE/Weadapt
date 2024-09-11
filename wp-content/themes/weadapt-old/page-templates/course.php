<?php
/*
* Template Name: Page Course
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type'             => 'course',
	'query_post_types' => ['course'],
	'show_post_types'  => false,
	'show_filters'     => false
] );

get_footer();