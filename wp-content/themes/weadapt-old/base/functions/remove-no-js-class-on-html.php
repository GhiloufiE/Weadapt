<?php

/**
 * Change documentElement Class when JS is Enabled
 */
add_action( 'wp_head', function() {
	?><script>document.documentElement.className = document.documentElement.className.replace('no-js', 'js');</script><?php
} );
