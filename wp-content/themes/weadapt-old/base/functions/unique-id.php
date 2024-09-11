<?php

/**
 * Unique ID
 */
if ( ! function_exists( 'get_unique_ID' ) ) :

	function get_unique_ID( $name = 'block' ) {
		switch ( $name ) {
			case 'block': static $block_key = 0; return ++ $block_key; break;
			case 'form':  static $form_key = 0;  return ++ $form_key; break;
			case 'input': static $input_key = 0;  return ++ $input_key; break;
			default:      static $theme_key = 0; return ++ $theme_key; break;
		}
	}

endif;