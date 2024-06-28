<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Group_Message
  {
	private static $instance;
	
	public static function init()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self;
            }
            return self::$instance;
        }
	
    function actions_filters()
    	{
			add_filter( 'init', array( $this, 'register_cpt' ), 12 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_filter( 'wp_insert_post_data', array( $this, 'process_group_content' ), 10, 2 );
			add_filter( 'manage_fep_group_posts_columns', array( $this, 'columns_head' ) );
			add_filter( 'manage_edit-fep_group_sortable_columns', array( $this, 'sortable_columns' ) );
			add_action( 'manage_fep_group_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
			add_action( 'transition_post_status', array( $this, 'clear_group_cache' ), 10, 3 );
			add_action( 'save_post_fep_group', array( $this, 'clear_user_group_cache' ) );
			add_action( 'set_user_role', array( $this, 'clear_user_group_cache_role_change' ) );
			add_filter( 'fep_admin_settings_tabs', array($this, 'admin_settings_tabs' ) );
			add_filter( 'fep_settings_fields', array($this, 'settings_fields' ) );
			add_filter( 'fep_current_user_can_send_to_group', array( $this, 'can_send_to_group' ), 10, 3 );
			add_action( 'fep_plugin_manual_update', array( $this, 'manual_update' ) );
			
			if( 'group' == Fep_Pro_To::init()->message_sending_to() ){
				add_action( 'fep_form_field_validate_fep_pro_to', array($this, 'form_field_validate_group' ), 15, 2 );
				add_action( 'fep_action_message_after_send', array($this, 'add_message_group' ), 5, 3 );
			}
			
			add_action( 'fep_action_message_after_send', array($this, 'add_reply_group' ), 5, 3 );
			add_filter( 'fep_is_group_message', array($this, 'is_group_message' ), 10, 2 );
			
			if( fep_get_option('can-add-to-group', 0 ) ){
				add_action( 'show_user_profile', array($this, 'user_profile_fields' ) );
				add_action( 'edit_user_profile', array($this, 'user_profile_fields' ) );
				add_action( 'personal_options_update', array($this, 'save_user_profile_fields' ) );
				add_action( 'edit_user_profile_update', array($this, 'save_user_profile_fields' ) );
				
				add_filter( 'fep_form_fields', array($this, 'user_settings_fields' ), 10, 2 );
				add_action ('fep_action_form_validated', array( $this, 'user_settings_save' ), 10, 2);
				add_filter( 'fep_filter_user_settings_before_save', array($this, 'user_settings_save_remove_groups' ) );
			}
    	}
		
	function register_cpt() {
		$labels = array(
			'name'               => _x( 'Front End PM Groups', 'post type general name', 'front-end-pm' ),
			'singular_name'      => _x( 'Group', 'post type singular name', 'front-end-pm' ),
			'menu_name'          => _x( 'Groups', 'admin menu', 'front-end-pm' ),
			'add_new_item'       => __( 'Add New Group', 'front-end-pm' ),
			'new_item'           => __( 'New Group', 'front-end-pm' ),
			'edit_item'          => __( 'Edit Group', 'front-end-pm' ),
			'view_item'          => __( 'View Group', 'front-end-pm' ),
			'all_items'          => __( 'All Groups', 'front-end-pm' ),
			'search_items'       => __( 'Search Groups', 'front-end-pm' ),
			'not_found'          => __( 'No Groups found.', 'front-end-pm' ),
			'not_found_in_trash' => __( 'No Groups found in Trash.', 'front-end-pm' ),
			'attributes'         => __( 'Group Attributes', 'front-end-pm' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'front-end-pm' ),
			'public'             => false,
			'publicly_queryable' => false,
			'query_var'          => false,
			'rewrite'            => false,
			'show_ui'            => true,
			'show_in_menu'       => 'fep-all-messages',
			'capability_type'    => 'page',
			'hierarchical'       => false,
			'supports'           => array( 'title', 'page-attributes' ),
		);

		register_post_type( 'fep_group', $args );
	}
	
	function add_meta_boxes() {
		add_meta_box( 'fep_group_members', __( 'Group Members', 'front-end-pm' ), array( $this, 'group_members_output' ), 'fep_group', 'normal', 'high' );
	}
	
	function group_members_output( $post ) {
		?>
		<div><textarea id="fep_group_members_textarea" name="post_content"></textarea></div>
		<?php
		wp_enqueue_style( 'fep-tokeninput-style' );
		wp_enqueue_script( 'fep-tokeninput' );
		
		$members      = array_unique( array_filter( explode( ',', $post->post_content ) ) );
		$pre_populate = array();
		foreach ( $members as $member_id ) {
			if ( is_numeric( $member_id ) ) {
				if ( $name = fep_user_name( $member_id ) ) {
					$pre_populate[] = array(
						'id'   => $member_id,
						'name' => esc_js( $name ),
					);
				}
			} elseif( false !== strpos( $member_id, '{role-unparsed}-' ) ) {
				$role = str_replace( '{role-unparsed}-', '', $member_id );
				if ( wp_roles()->is_role( $role ) ) {
					$pre_populate[] = array(
						'id'   => '{role-unparsed}-' . esc_js( $role ),
						'name' => '{role-unparsed}-' . esc_js( wp_roles()->role_names[ $role ] ),
					);
				}
			}
		}
		fep_tokeninput_localize(
			[
				'selector'    => '#fep_group_members_textarea',
				'for'         => 'group',
				'prePopulate' => $pre_populate,
			]
		);
	}
	
	function process_group_content( $data, $postarr ) {
		if ( isset( $postarr['post_type'] ) && 'fep_group' === $postarr['post_type'] && ! empty( $postarr['post_content'] ) ) {
			$members = array_filter( explode( ',', $postarr['post_content'] ) );
			$roles   = array();
			foreach ( $members as $x => $member ) {
				if ( $member && is_numeric( $member ) ) {
					// This is User ID.
				} elseif ( false !== strpos( $member, '{role}-' ) ) {
					$roles[] = str_replace( '{role}-', '', $member );
					unset( $members[ $x ] );
				} elseif ( false !== strpos( $member, '{role-unparsed}-' ) ) {
					// This will be parsed when called.
				} else {
					unset( $members[ $x ] );
				}
			}
			if ( $roles ) {
				$users   = get_users( array( 'fields' => 'ids', 'role__in' => $roles ) );
				$members = array_merge( $members, $users );
			}
			$data['post_content'] = ',' . implode( ',', array_filter( array_unique( $members ) ) ) . ',';
		}
		return $data;
	}
	
	function columns_head( $defaults ) {
		$defaults['post_content'] = __( 'Members', 'front-end-pm' );
		$defaults['menu_order']   = __( 'Order', 'front-end-pm' );
		return $defaults;
	}
	
	function sortable_columns( $columns ) {
		$columns['menu_order'] = 'menu_order';
		return $columns;
	}
	
	function columns_content( $column_name, $post_ID ) {
		if ( 'post_content' === $column_name ) {
			$members = array_filter( explode( ',', get_post_field( 'post_content', $post_ID ) ) );
			$output  = array();
			foreach ( $members as $member_id ) {
				if ( is_numeric( $member_id ) ) {
					if ( $name = fep_user_name( $member_id ) ) {
						$output[] = esc_html( $name );
					}
				} elseif ( false !== strpos( $member_id, '{role-unparsed}-' ) ) {
					$role = str_replace( '{role-unparsed}-', '', $member_id );
					if ( wp_roles()->is_role( $role ) ) {
						$output[] = '{role-unparsed}-' . esc_html( wp_roles()->role_names[ $role ] );
					}
				}
			}
			echo implode( '<br />', $output );
		} elseif ( 'menu_order' === $column_name ) {
			echo esc_html( get_post_field( 'menu_order', $post_ID ) );
		}
	}
	
	function clear_group_cache( $new_status, $old_status, $post ) {
		if ( $new_status !== $old_status && 'fep_group' === $post->post_type ) {
			wp_cache_delete( $new_status, 'fep-all-groups' );
			wp_cache_delete( $old_status, 'fep-all-groups' );
			wp_cache_delete( 'all', 'fep-all-groups' );
		}
	}
	
	function clear_user_group_cache( $post_id ) {
		wp_cache_delete( 'with-roles', 'fep-user-groups' );
		wp_cache_delete( 'without-roles', 'fep-user-groups' );
	}
	
	function clear_user_group_cache_role_change( $user_id ) {
		$all_user_groups = wp_cache_get( 'with-roles', 'fep-user-groups' );
		if ( is_array( $all_user_groups ) && isset( $all_user_groups[ $user_id ] ) ) {
			unset( $all_user_groups[ $user_id ] );
			wp_cache_set( 'with-roles', $all_user_groups, 'fep-user-groups' );
		}
	}
	
	function manual_update( $prev_ver ) {
		global $wpdb;
		if ( version_compare( $prev_ver, '11.1.1', '<' ) && ! fep_get_option( 'v1111-part-1', 0, 'fep_updated_versions' ) ) {
			$options = get_option( 'FEP_admin_options' );
			if ( ! is_array( $options ) ) {
				$options = [];
			}
			if ( ! isset( $options['gm_groups'] ) ) {
				fep_update_option( 'v1111-part-1', 1, 'fep_updated_versions' );
				return;
			}
			$groups = $options['gm_groups'];
			
			if ( $groups && is_array( $groups ) ) {
				foreach ( $groups as $group ) {
					$args = [
						'post_title'   => $group['name'],
						'post_content' => $group['members'],
						'post_status'  => 'publish',
						'post_type'    => 'fep_group',
					];
					if ( $post_id = wp_insert_post( $args ) ) {
						$mgs_ids = $wpdb->get_col( $wpdb->prepare( "SELECT fep_message_id FROM $wpdb->fep_messagemeta WHERE meta_key = %s AND meta_value = %s", '_fep_group', $group['slug'] ) );
						
						if ( $mgs_ids ) {
							$mgs_ids    = array_unique( array_filter( $mgs_ids ) );
							$data_array = [];
							foreach ( $mgs_ids as $mgs_id ) {
								$data_array[] = $wpdb->prepare( '(%d, %s, %d)', $mgs_id, '_fep_group_id', $post_id );
								$data_array[] = $wpdb->prepare( '(%d, %s, %s)', $mgs_id, '_fep_group_name', $group['name'] );
							}
							$wpdb->query( "INSERT INTO $wpdb->fep_messagemeta ( fep_message_id, meta_key, meta_value ) VALUES " . implode( ',', $data_array ) );
							
							foreach ( $mgs_ids as $mgs_id ) {
								wp_cache_delete( $mgs_id, 'fep_message_meta' );
							}
						}
					}
				}
			}
			unset( $options['gm_groups'] );
			update_option( 'FEP_admin_options', $options );
			fep_update_option( 'v1111-part-1', 1, 'fep_updated_versions' );
			$response = array(
				'update'     => 'continue',
				'message'    => __( 'Groups updated.', 'front-end-pm' ),
				'custom_int' => 0,
				'custom_str' => '',
			);
			wp_send_json( $response );
		}
	}
		
	function admin_settings_tabs( $tabs ) {
				
		$tabs['gm_groups'] =  array(
				'section_title'			=> __('Groups', 'front-end-pm'),
				'section_page'		=> 'fep_settings_recipient',
				'priority'			=> 20,
				'tab_output'		=> false
				);
				
		return $tabs;
	}
	
	function settings_fields( $fields )
		{
			$fields['can-send-to-group'] =   array(
				'type'	=>	'checkbox',
				'class'	=> '',
				'section'	=> 'gm_groups',
				'value' => fep_get_option('can-send-to-group', 0 ),
				'cb_label' => __( 'Can users send message to group.', 'front-end-pm' ),
				'label' => __( 'Can send to group', 'front-end-pm' )
				);
			$fields['can-add-to-group'] =   array(
				'type'	=>	'checkbox',
				'class'	=> '',
				'section'	=> 'gm_groups',
				'value' => fep_get_option('can-add-to-group', 0 ),
				'cb_label' => __( 'Can users add themself to group.', 'front-end-pm' ),
				'label' => __( 'Can add to group', 'front-end-pm' )
				);
			$fields['gm_frontend'] =   array(
				'type'	=>	'select',
				'section'	=> 'gm_groups',
				'value' => fep_get_option('gm_frontend', 'dropdown' ),
				'description' => __( 'Select how you want to see in frontend.', 'front-end-pm' ),
				'label' => __( 'Show in front end as', 'front-end-pm' ),
				'options'	=> array(
					'dropdown'	=> __( 'Dropdown', 'front-end-pm' ),
					'radio'	=> __( 'Radio Button', 'front-end-pm' )
					)
				);
								
			return $fields;
			
		}
	
	function can_send_to_group( $can, $cap, $id ) {
		if ( ! $id || ! is_numeric( $id ) || ! fep_get_option( 'can-send-to-group', 0 ) ) {
			return $can;
		}
		if ( in_array( get_current_user_id(), $this->get_group_members( $id, false ) ) ) {
			// using false will not search in roles, so better performance.
			return true;
		} elseif ( in_array( get_current_user_id(), $this->get_group_members( $id ) ) ) {
			return true;
		} elseif ( array_key_exists( $id, apply_filters( 'fep_filter_groups_to_send_message', $this->get_user_groups() ) ) ) {
			return true;
		}
		return $can;
	}
	
	function form_field_validate_group( $field, $errors ){
		$_POST['message_to_group'] = '';
		
		if( ! fep_get_option('can-send-to-group', 0 ) ) {
			$errors->add( 'pro_to' , __('You do not have permission send message to group.', 'front-end-pm'));
			return false;
		}
		
		$to = isset( $_POST['fep_gm_to'] ) ? $_POST['fep_gm_to'] : '';
		if ( ! $to || ! fep_current_user_can( 'send_to_group', $to ) ) {
			$errors->add( 'pro_to', __( 'You must select group.', 'front-end-pm' ) );
		} else {
			$_POST['message_to_group'] = $to;
		}
		if( $_POST['message_to_group'] ){
			$_POST['message_to_id'] = $this->get_group_members( $_POST['message_to_group'] );
		}
	}
	
	function add_message_group( $message_id, $message, $inserted_message ){
		
		if( ! empty( $message['message_to_group'] ) && ! $inserted_message->mgs_parent ){
			fep_add_meta( $message_id, '_fep_group_id', $message['message_to_group'], true );
			fep_add_meta( $message_id, '_fep_group_name', $this->get_group_field( $message['message_to_group'], 'post_title' ), true );
		}
	}
	
	function add_reply_group( $message_id, $message, $inserted_message ){
		if( $inserted_message->mgs_parent ) {
			$group = fep_get_meta( $inserted_message->mgs_parent, '_fep_group_id', true );
			if( $group ){
				fep_add_meta( $message_id, '_fep_group_id', $group, true );
				fep_add_meta( $message_id, '_fep_group_name', fep_get_meta( $inserted_message->mgs_parent, '_fep_group_name', true ), true );
			}
		}
	}
	
	function get_group_field( $group_id, $field ) {
		$return = '';
		$group  = get_post( $group_id );
		if ( $group && 'fep_group' === $group->post_type && isset( $group->$field ) ) {
			$return = $group->$field;
		}
		return $return;
	}
	
	function get_user_groups( $user_id = 0, $search_roles = true ){
		global $wpdb;
		
		if( ! $user_id )
		$user_id = get_current_user_id();
		
		$user = get_userdata( $user_id );
		
		if ( ! $user ) {
			return array();
		}
		
		if ( $search_roles ) {
			$cache_key = 'with-roles';
		} else {
			$cache_key = 'without-roles';
		}
		$all_user_groups = wp_cache_get( $cache_key, 'fep-user-groups' );
		if ( ! is_array( $all_user_groups ) ) {
			$all_user_groups = [];
		}
		if ( isset( $all_user_groups[ $user_id ] ) ) {
			return apply_filters( 'fep_get_user_groups', $all_user_groups[ $user_id ], $user_id, $search_roles );
		}
		
		$user_groups = array();
		$query  = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'fep_group' AND post_status = 'publish'";
		
		$search_array = [ $wpdb->prepare( 'post_content LIKE %s', '%,' . $wpdb->esc_like( $user_id ) . ',%' ) ];
		
		if ( $search_roles ) {
			foreach ( (array) $user->roles as $role ) {
				$search_array[] = $wpdb->prepare( 'post_content LIKE %s', '%' . $wpdb->esc_like( ",{role-unparsed}-{$role}," ) . '%' );
			}
		}

		$query .= ' AND (' . implode( ' OR ', $search_array ) . ')';
		$query .= ' ORDER BY menu_order ASC';
		
		$result = $wpdb->get_results( $query );
		
		if ( $result ){
			foreach ( $result as $group ) {
				$user_groups[ $group->ID ] = $group->post_title;
			}
		}
		$all_user_groups[ $user_id ] = $user_groups;
		wp_cache_set( $cache_key, $all_user_groups, 'fep-user-groups' );

		return apply_filters( 'fep_get_user_groups', $all_user_groups[ $user_id ], $user_id, $search_roles );
	}
	
	function get_all_groups( $status = 'publish' ) {
		global $wpdb;
		
		if ( ! $status ) {
			$status = 'all';
		}
		$groups = wp_cache_get( $status, 'fep-all-groups' );
		if ( is_array( $groups ) ) {
			return apply_filters( 'fep_get_all_groups', $groups, $status );
		} else {
			$groups = [];
		}
		
		$query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'fep_group'";
		if ( 'all' !== $status ) {
			$query .= $wpdb->prepare( ' AND post_status = %s', $status );
		}
		$query .= ' ORDER BY menu_order ASC';
		$result = $wpdb->get_results( $query );
		if ( $result ) {
			foreach ( $result as $group ) {
				$groups[ $group->ID ] = $group->post_title;
			}
		}
		wp_cache_set( $status, $groups, 'fep-all-groups' );

		return apply_filters( 'fep_get_all_groups', $groups, $status );
	}
	
	function get_group_members( $group_id, $search_roles = true ){
		
		$members = array();
		$roles   = array();
		if ( $content = $this->get_group_field( $group_id, 'post_content' ) ) {
			$group_members = array_filter( explode( ',', $content ) );
			foreach ( $group_members as $group_member ) {
				if ( is_numeric( $group_member ) ) {
					$members[] = $group_member;
				} elseif ( false !== strpos( $group_member, '{role-unparsed}-' ) ) {
					$roles[] = str_replace( '{role-unparsed}-', '', $group_member );
				}
			}
		}
		if ( $roles && $search_roles ) {
			$users   = get_users( array( 'fields' => 'ids', 'role__in' => $roles ) );
			$members = array_merge( $members, $users );
		}
		$members = array_unique( array_filter( $members ) );
		
		return apply_filters( 'fep_get_group_members', $members, $group_id, $search_roles );
	}
	
	function add_users_to_group( $group_id, $user_ids ) {
		if ( ! $user_ids || ! is_array( $user_ids ) ) {
			return false;
		}
		if ( $this->get_group_field( $group_id, 'ID' ) ) {
			$members = array_filter( explode( ',', $this->get_group_field( $group_id, 'post_content' ) ) );
			if ( $added = array_diff( $user_ids, $members ) ) {
				$members = array_merge( $added, $members );
				return wp_update_post( [
					'ID'           => $group_id,
					'post_content' => implode( ',', array_unique( $members ) ),
					]
				);
			}
		}
		return false;
	}
	
	function remove_users_from_group( $group_id, $user_ids ) {
		if ( ! $user_ids || ! is_array( $user_ids ) ) {
			return false;
		}
		if ( $this->get_group_field( $group_id, 'ID' ) ) {
			$members = array_filter( explode( ',', $this->get_group_field( $group_id, 'post_content' ) ) );
			if ( $removed = array_intersect( $user_ids, $members ) ) {
				$members = array_diff( $members, $removed );
				return wp_update_post( [
					'ID'           => $group_id,
					'post_content' => implode( ',', array_unique( $members ) ),
					]
				);
			}
		}
		return false;
	}
	
	function keep_user_to_groups( $user_id, $group_ids ) {
		$user_groups = array_keys( $this->get_user_groups( $user_id, false ) );
		
		if( ! is_array( $group_ids ) )
		$group_ids = array();
		
		if ( $added = array_diff( $group_ids, $user_groups ) ) {
			foreach ( $added as $group_id ) {
				$this->add_users_to_group( $group_id, [ $user_id ] );
			}
		}
		if ( $removed = array_diff( $user_groups, $group_ids ) ) {
			foreach ( $removed as $group_id ) {
				$this->remove_users_from_group( $group_id, [ $user_id ] );
			}
		}
	}
	
	function is_group_message( $return, $mgs_id ) {
		if ( fep_is_group_message( $mgs_id ) ) {
			$return = __('Group', 'front-end-pm') . ': ' . fep_get_group_name( $mgs_id );
		}
		return $return;
	}
	
	function user_profile_fields( $user ){
		$groups = $this->get_all_groups();
		
		if( ! $groups || ! is_array( $groups) )
		return false;
		?>
		<h3><?php _e("FEP Group", "front-end-pm"); ?></h3>

    <table id="fep_groups_table" class="form-table">
		<tbody>
		    <tr>
		        <th><label for="fep_groups"><?php _e( 'Groups', 'front-end-pm' ); ?></label></th>
		        <td>
					<?php 
						$user_groups = $this->get_user_groups( $user->ID, false );
						foreach ( $groups as $group_id => $group ) { ?>
							<label><input type="checkbox" name="fep_groups[]" value="<?php echo esc_attr( $group_id ) ?>" <?php checked( isset( $user_groups[ $group_id ] ), true ); ?> /> <?php echo esc_attr( $group ) ?></label><br />
						<?php } ?>
						<span class="description"><?php _e('Please select groups which you want to join.', 'front-end-pm'); ?></span>
		        </td>
		    </tr>
		</tbody>
	</table>
	<?php }
	
	function save_user_profile_fields( $user_id ) {
	    if ( !current_user_can( 'edit_user', $user_id ) ) { 
	        return false;
	    }
		$selected_groups = isset( $_POST["fep_groups"] ) ? $_POST["fep_groups"] : array();
		$this->keep_user_to_groups( $user_id, $selected_groups );
	}
	
	function user_settings_fields( $fields, $where ){
		if ( 'settings' != $where ) {
			return $fields;
		}
		$groups = $this->get_all_groups();
		if( ! $groups || ! is_array( $groups ) )
		return $fields;
		
		$fields['fep_groups'] = array(
			'label'       => __( 'Groups', 'front-end-pm' ),
			'type'        =>  'checkbox',
			'multiple'	=> true,
			'value'     => array_keys( $this->get_user_groups( get_current_user_id(), false ) ),
			'priority'    => 45,
			'where'    => 'settings',
			'description'	=> __('Please select groups which you want to join.', 'front-end-pm'),
			'options'	=> $groups,
		);
		return $fields;
	}
	
	function user_settings_save( $where, $fields ){
		
		if( 'settings' != $where )
			return;
		
		if( !$fields || !is_array($fields) || !isset($fields['fep_groups']) || !is_array($fields['fep_groups']) )
			return;
		
		$selected_groups = is_array( $fields['fep_groups']['posted-value'] ) ? $fields['fep_groups']['posted-value'] : array();
		
		$this->keep_user_to_groups( get_current_user_id(), $selected_groups );
		}
	
	function user_settings_save_remove_groups( $settings ){
		
		unset( $settings['fep_groups'] );
		return $settings;
	}

  } //END CLASS

add_action('init', array(Fep_Group_Message::init(), 'actions_filters'));

