<?php

/**
 * Replace CSS Image Url
 */
if ( ! function_exists( 'check_styles_urls' ) ) :

	function check_styles_urls( $string ) {
		$path    = get_theme_file_uri( '/assets/images' );
		$url     = "url($path$3)";
		$pattern = '/url\s*\(\s*[\'"]?(?!(((?: https?: )?\/\/)|(?: data\: ?: )|(?: #)))([^\'"\)]+)[\'"]?\s*\)/i';

		return preg_replace( $pattern, $url, $string );
	}

endif;