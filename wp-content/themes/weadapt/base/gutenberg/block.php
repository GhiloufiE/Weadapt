<?php
/**
 * Global Block Class
 */
class Block {
	private $_block = null;

	public function __construct( $block ) {
		$this->_block = $block;
	}

	public function id( $custom = '' ) {
		$id = '';

		if ( ! empty( $this->_block['anchor'] ) || ! empty( $custom ) ) {
			$id_arr = [];

			if ( ! empty( $this->_block['anchor'] )) {
				$id_arr[] = $this->_block['anchor'];
			}

			if ( ! empty( $custom ) ) {
				$id_arr[] = "$custom";
			}

			if ( ! empty( $id_arr ) ) {
				$id = ' id="' . implode( ' ', $id_arr ) . '"';
			}
		}

		return $id;
	}

	public function class( $custom = '' ) {
		$name =  str_replace( 'acf/', '', $this->_block['name'] );
		$class_arr = [$name];

		if ( ! empty( $custom ) ) {
			$class_arr[] = $custom;
		}

		if ( ! empty( $this->_block['align'] ) ) {
			$class_arr[] = 'align' . $this->_block['align'];
		}

		if( ! empty( $this->_block['align_text'] ) ) {
			$class_arr[] = 'has-text-align-' . esc_attr( $this->_block['align_text'] );
		}

		if( ! empty( $this->_block['align_content'] ) ) {
			$class_arr[] = 'd-flex is-content-justification-' . str_replace( ' ','-', $this->_block['align_content'] );
		}

		if ( isset( $this->_block['className'] ) ) {
			$class_arr[] = $this->_block['className'];
		}

		if ( ! empty( $this->spacings() ) ) {
			$class_arr[] = $this->spacings();
		}

		$class = 'class="' . implode( ' ', $class_arr ) . '"';

		return $class;
	}

	public function name() {
		return str_replace( 'acf/', '', $this->_block['name'] );
	}

	public function attr( $custom_class = '', $custom_id = '', $custom_attr = '' ) {
		$attributes = $this->class($custom_class) . $this->id($custom_id);

		if ( ! empty($custom_attr) ) {
			$attributes .= " $custom_attr";
		}

		return $attributes;
	}

	public function spacings() {
		$spacing_field = get_field('section_spacings');
		$spacing_class = '';

		if ( ! empty( $spacing_field ) ) {
			$spacing_arr = [];

			if ( isset($spacing_field['spacing_top']) && $spacing_field['spacing_top'] ) {
				$spacing_arr[] = 'block-spacing--pt';
			}

			if ( isset($spacing_field['double_spacing_top']) && $spacing_field['double_spacing_top'] ) {
				$spacing_arr[] = 'block-spacing-double--pt';
			}

			if ( isset($spacing_field['spacing_bottom']) && $spacing_field['spacing_bottom'] ) {
				$spacing_arr[] = 'block-spacing--pb';
			}

			if ( isset($spacing_field['double_spacing_bottom']) && $spacing_field['double_spacing_bottom'] ) {
				$spacing_arr[] = 'block-spacing-double--pb';
			}

			if ( isset($spacing_field['margin_top']) && $spacing_field['margin_top'] ) {
				$spacing_arr[] = 'block-spacing--mt';
			}

			if ( isset($spacing_field['margin_bottom']) && $spacing_field['margin_bottom'] ) {
				$spacing_arr[] = 'block-spacing--mb';
			}

			if ( !empty( $spacing_arr ) ) {
				$spacing_class = implode( ' ', $spacing_arr );
			}
		}

		return $spacing_class;
	}

	public function subtitle( $class = '', $tag = 'p' ) {
		$subtitle_html = '';

		$subtitle = get_field( 'section_subtitle' );

		if ( ! empty( $subtitle ) ) {
			$class = ! empty( $class ) ? "section-subtitle $class" : 'section-subtitle';

			$subtitle_html = "<$tag class='$class'>$subtitle</$tag>";
		}

		return $subtitle_html;
	}

	public function title( $class = '', $tag = 'h2' ) {
		$title_html = '';

		$title = get_field('section_title');

		if ( !empty( $title ) ) {
			$class = ! empty($class) ? "section-title $class" : 'section-title';

			$title_html = "<$tag class='$class'>$title</$tag>";
		}

		return $title_html;
	}

	public function desc( $class = '' ) {
		$description_html = '';

		$description = get_field('section_description');

		if ( !empty( $description ) ) {
			$class = ! empty($class) ? "section-description $class" : 'section-description';

			$description_html = "<div class='$class'>$description</div>";
		}

		return $description_html;
	}

	public function image( $class = '', $size = 'large' ) {
		$image_html = '';

		$image = get_field( 'section_image' );

		if ( ! empty( $image ) ) {
			$class = ! empty( $class ) ? "section-image $class" : 'section-image';

			$image_html = "<div class='$class'>" . get_img( $image, $size ) . "</div>";
		}

		return $image_html;
	}

	public function button( $btn_style = '', $btn_class = '', $btn_icon = '', $btn_is_popup = false ) {
		$button = get_field( 'section_button' );
		$button_html = get_button( $button, $btn_style, $btn_class, $btn_icon, $btn_is_popup );

		return $button_html;
	}
}
