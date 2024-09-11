<?php

if ( 1 === get_current_blog_id() ) {
	add_filter( 'list_template_show_title_icon', '__return_false');
}