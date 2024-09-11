<?php

/**
 * Wrap Query String
 */
if ( ! function_exists( 'wrap_content_query_string' ) ) :

	function wrap_content_query_string( $query = '', $contents = [], $substr = false ) {
		$output = '';

		if ( is_array( $contents ) && ! empty( $contents ) ) {
			foreach ( $contents as $content ) {
				$content = wp_strip_all_tags($content);
				$content = preg_replace( "/[\r\n]+/", "\n", $content );
				$content = preg_replace( '/\s+/', ' ', $content );

				if ( $substr ) {
					preg_match( "#(\b[^,.].{0,30})($query.{0,150}\b)#ui", $content, $content_matches);

					if ( ! empty( $content_matches[0] ) ) {
						$content = '...' . trim( $content_matches[0] ) . '...';
					}
					else {
						$content_array = explode(" ", $content);
						$content_array = array_slice($content_array, 0, 25);
						$content       = implode(" ", $content_array);
					}
				}

				$output = preg_replace_callback(
					"#$query#ui",
					function( $matches ) {
						return "<strong>$matches[0]</strong>";
					},
					$content
				);
			}

			if ( empty( $output ) ) {
				$output = $contents[0];
			}
		}

		return $output;
	}

endif;


