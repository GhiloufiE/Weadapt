<?php
/*
* Template Name: Page Connect
*
* @package    WordPress
* @subpackage weadapt
* @since      weadapt 1.0
*/
get_header();

get_template_part( 'index', null, [
	'type'             => 'blog',
	'query_post_types' => ['forums', 'forum'] // 'organisation'
] );

get_footer();
