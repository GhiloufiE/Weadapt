<?php

if ( ! function_exists( 'weadapt_post_type_templates' ) ) :

	/**
	 * Custom Post Type Templates
	 */
	function weadapt_post_type_templates() {
		$template_headings = [
			'event' => [
				__( 'Recording', 'weadapt' ),
				__( 'Key messages', 'weadapt' ),
				__( 'Aim', 'weadapt' ),
				__( 'Agenda', 'weadapt' ),
				__( 'Speakers', 'weadapt' ),
				__( 'Registration details', 'weadapt' )
			],
			'article' => [
				__( 'Summary', 'weadapt' ),
				__( 'Introduction', 'weadapt' ),
				__( 'Methodology', 'weadapt' ),
				__( 'Adaptation strategies', 'weadapt' ),
				__( 'Barriers', 'weadapt' ),
				__( 'Enablers', 'weadapt' ),
				__( 'Outcomes', 'weadapt' ),
				__( 'Impacts', 'weadapt' ),
				__( 'Lessons Learned', 'weadapt' )
			],
			'case-study' => [
				__( 'Summary', 'weadapt' ),
				__( 'Introduction', 'weadapt' ),
				__( 'Methodology', 'weadapt' ),
				__( 'Adaptation strategies', 'weadapt' ),
				__( 'Barriers', 'weadapt' ),
				__( 'Enablers', 'weadapt' ),
				__( 'Outcomes', 'weadapt' ),
				__( 'Impacts', 'weadapt' ),
				__( 'Lessons Learned', 'weadapt' )
			],
			'blog' => [
				__( 'Introduction', 'weadapt' ),
				__( 'Key messages', 'weadapt' ),
				__( 'Reflection', 'weadapt' )
			],
			'course' => [
				__( 'Introduction', 'weadapt' ),
				__( 'Institutional background and trainer', 'weadapt' ),
				__( 'Who would find this useful?', 'weadapt' ),
				__( 'Training materials', 'weadapt' ),
				__( 'Learning outcomes', 'weadapt' )
			],
		];

		if ( ! empty( $template_headings ) ) {
			foreach ( $template_headings as $post_type => $headings ) {
				$template = [];

				foreach ( $headings as $heading ) {
					$template[] = array( 'core/heading',
						array(
							'content' => $heading
						)
					);
					$template[] = array( 'core/paragraph' );
				}

				if ( ! empty( $template ) && ! empty( $post_type_object = get_post_type_object( $post_type ) ) ) {
					$post_type_object->template = $template;
				}
			}
		}
	}
endif;

add_filter( 'init', 'weadapt_post_type_templates' );