<?php
/*
* Template Name: Page Organisation
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type' => 'organisation'
] );

get_footer();