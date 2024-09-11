<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( 'core/list' === $block['blockName'] ) {
		$block_content = '<div data-key="core-list"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );