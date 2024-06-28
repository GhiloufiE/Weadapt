<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( 'core/image' === $block['blockName'] ) {
		$block_content = '<div data-key="core-image"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );