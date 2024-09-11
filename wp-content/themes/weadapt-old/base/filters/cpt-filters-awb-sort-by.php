<?php

if ( 1 === get_current_blog_id() ) {
	add_filter( 'cpt_filters_awb_sort_by', function( $sort_by ) {
		return [];
	} );
}