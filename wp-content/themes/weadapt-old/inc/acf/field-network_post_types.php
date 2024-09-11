<?php

if ( ! is_multisite() )
	return;


/**
 * Group | Network Post Types
 */
add_filter( 'acf/load_field/name=network_post_types', function( $field ) {
	$choices    = [];
	$sub_fields = [];
	$post_types = apply_filters( 'theme_cpt', array() );


	if ( ! empty( $post_types ) ) {
		foreach ( $post_types as $post_type ) {
			$choices[$post_type['post_type']] = $post_type['multiple_name'];
		}
	}

	foreach ( get_sites() as $key => $site ) {
		$site_name = get_blog_details( $site->blog_id )->blogname;

		// Message
		$sub_fields[] = array(
			'ID'       => 'field_' . $key . '_0',
			'key'      => 'field_' . $key . '_0',
			'label'    => $site_name,
			'name'     => $site->blog_id . '_message',
			'_name'    => $site->blog_id . '_message',
			'type'     => 'message',
			'message'  => $site_name,
			'required' => false,
			'wrapper'  => array(
				'width'  => '',
				'class'  => 'heading',
				'id'     => ''
			),
			'parent'   => $field['ID']
		);

		// Checkboxes
		$sub_fields[] = array(
			'ID'            => 'field_' . $key . '_1',
			'key'           => 'field_' . $key . '_1',
			'label'         => __( 'Post Types', 'weadapt' ),
			'name'          => $site->blog_id . '_post_types',
			'_name'         => $site->blog_id . '_post_types',
			'type'          => 'checkbox',
			'layout'        => 'horizontal',
			'allow_custom'  => false,
			'save_custom'   => false,
			'toggle'        => true,
			'return_format' => 'value',
			'choices'       => $choices,
			'required'      => 0,
			'default_value' => array(), // array_keys( $choices ),
			'wrapper'       => array(
				'width'  => '',
				'class'  => '',
				'id'     => ''
			),
			'parent'        => $field['ID'],
		);

		// Frontend Redirect URL
		$sub_fields[] = array(
			'ID'            => 'field_' . $key . '_2',
			'key'           => 'field_' . $key . '_2',
			'label'         => __( 'Frontend Redirect URL', 'weadapt' ),
			'name'          => $site->blog_id . '_frontend_redirect_url',
			'_name'         => $site->blog_id . '_frontend_redirect_url',
			'type'          => 'text',
			'prefix'        => '',
			'required'      => 0,
			'placeholder'   => '',
			'default_value' => '',
			'required'      => false,
			'maxlength'     => '',
			'wrapper'       => array(
				'width' => '',
				'class' => '',
				'id'    => ''
			),
			'parent'        => $field['ID']
		);
	}

	if ( ! empty( $sub_fields ) ) {
		$field['sub_fields'] = $sub_fields;
	}

	return $field;
} );


/**
 * Flush Refrite Rules on Network General Settings Page
 */
add_action( 'acf/save_post', function() {
	$screen = get_current_screen();

	if ( isset( $screen->id ) && strpos( $screen->id, 'network-general-settings') == true ) {
		foreach ( get_sites() as $site ) {
			switch_to_blog( $site->blog_id );

			delete_option( 'rewrite_rules' );

			flush_rewrite_rules();

			restore_current_blog();
		}
	}
}, 20);