<?php

/**
 * Add Unique Key
 */
add_filter( 'render_block', function( $block_content, $block ) {
	if ( 'core/heading' === $block['blockName'] ) {
		$block_content = '<div data-key="core-heading"></div>' . $block_content;
	}

	return $block_content;
}, 10, 2 );