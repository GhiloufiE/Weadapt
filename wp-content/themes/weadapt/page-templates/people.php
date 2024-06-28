<?php
/**
 * Template Name: Page People
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */
get_header();

get_template_part( 'index', null, [
	'type' => 'people'
] );

get_footer();