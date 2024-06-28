<?php

/**
* Estimated reading time in minutes
*
* @param $content
* @param $words_per_minute
* @param $with_gutenberg
*
* @return int estimated time in minutes
*/

if ( ! function_exists( 'get_estimate_reading_time' ) ) :

	function get_estimate_reading_time( $content = '', $words_per_minute = 300 ) {
		// Remove HTML tags from string
		$content = wp_strip_all_tags( $content );

		// Count words containing string
		$words_count = str_word_count( $content );

		// Calculate time for read all words and round
		$minutes = ceil( $words_count / $words_per_minute );

		return sprintf( '%s %s', $minutes, __( 'min read', 'weadapt' ) );
	}

endif;