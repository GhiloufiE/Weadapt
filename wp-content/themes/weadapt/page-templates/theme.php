<?php
/*
* Template Name: Page Theme
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type' => 'theme'
] );

get_footer();