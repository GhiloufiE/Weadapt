<?php

/**
 * Get theme button base on ACF Link field
 *
 * @param array ACF Link field
 * @param string Gutenberg style name
 * @param string additional custom class
 */
if ( ! function_exists( 'get_button' ) ) :

	function get_button( $link, $style = '', $custom_class = '', $icon = '', $is_popup = false ) {
		$link_html = '';

		if ( ! empty( $link ) && is_array( $link ) ) {
			$link_url        = $link['url'];
			$link_title      = $link['title'];
			$link_target     = ! empty( $link['target'] ) ? $link['target']: '_self';
			$link_attributes = ! empty( $link['attributes'] ) ? $link['attributes'] : [];

			if ( strpos( $link_url, '#popup-' ) !== false ) {
				$link_url = str_replace( '#popup-', '#', $link['url'] );
				$is_popup = true;
			}

			$args = [
				'link_url'        => $link_url,
				'link_title'      => $link_title,
				'link_target'     => $link_target,
				'link_attributes' => $link_attributes,
				'style'           => $style,
				'set_class'       => $custom_class,
				'icon'            => $icon,
				'is_popup'        => $is_popup
			];

			ob_start();
			get_part( 'components/button/index', $args );
			$link_html = ob_get_clean();
		}

		return $link_html;
	}

endif;