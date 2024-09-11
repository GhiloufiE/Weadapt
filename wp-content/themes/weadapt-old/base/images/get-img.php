<?php

/**
 * Get SVG Tag
 */
if ( ! function_exists( 'get_svg_tag' ) ) :

	function get_svg_tag( $el ) {
		$svg = file_get_contents( $el );
		preg_match( '/<svg[\s\S]*\/svg>/m', $svg, $matches );
		$return = isset( $matches[0] ) ? $matches[0] : $svg;

		return $return;
	}

endif;


/**
 * Get Inline Image
 */
if ( ! function_exists( 'get_img' ) ) :

	function get_img( $id, $thumb = 'full', $dir = '/assets/images/svg/' ) {
		$output = '';

		if ( is_int( $id ) ) {
			$src = wp_get_attachment_image_src( $id, $thumb );

			if ( ! $src ) {
				return '';
			}

			$ext = pathinfo( $src[0], PATHINFO_EXTENSION );

			if ( $ext != 'svg' ) {
				$low_quality = false;
              	if(isset( $_COOKIE['weadapt-low-quality-images'] ) && wp_validate_boolean( $_COOKIE['weadapt-low-quality-images'] )) {
              		$low_quality = $_COOKIE['weadapt-low-quality-images'];
             	}
              	if ($low_quality) {
              		return wp_get_attachment_image( $id, 'thumbnail' );
				} else {
					return wp_get_attachment_image( $id, $thumb );
				}
			}

			$image  = realpath( get_attached_file( $id, true ) );
			$output = $image ? get_svg_tag( $image ) : '';
		}
		else {
			$file   = get_theme_file_path( $dir . $id . '.svg' );
			$output = file_exists( $file ) ? get_svg_tag( $file ): '';
		}

		return $output;
	}

endif;
