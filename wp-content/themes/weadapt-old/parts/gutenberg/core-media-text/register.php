<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( 'core/media-text' === $block['blockName'] ) {
		$block_content = '<div data-key="core-media-text"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );