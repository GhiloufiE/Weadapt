<?php
/*
* Template Name: Page Network
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type' => 'network'
] );

get_footer();