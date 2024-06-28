<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Pro_Ajax
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
			//add_action('wp_ajax_fep_group_members', array($this, 'fep_group_members' ) );
    	}
	
	function fep_group_members() {
		
		if ( check_ajax_referer( 'fep_group_members', 'token', false )) {
		
		$searchq = $_POST['q'];
		$already = empty( $_POST['x'] ) ? array() : explode( ',', $_POST['x']);
		
		
		$args = array(
			'search' => "*{$searchq}*",
			'search_columns' => array( 'user_login', 'display_name' ),
			'exclude' => $already,
			'number' => 10,
			'orderby' => 'display_name',
			'order' => 'ASC',
			'fields' => array( 'ID', 'display_name' )
		);
		
		$ret = array();
			
		if(strlen($searchq)>0)
		{
			$args = apply_filters ('fep_group_members_suggestion_arguments', $args );
			$roles = get_editable_roles();
			
			if ( false !== strpos( $searchq, '{role}-' ) ) {
				$search_role = str_replace( '{role}-', '', $searchq );
				
				foreach ( $roles as $key => $role ) {
					if ( ! $search_role ) {
						$ret[] = array(
							'id'   => '{role}-' . $key,
							'name' => '{role}-' . esc_js( $role['name'] ),
						);
					} elseif ( false !== strpos( $key, $search_role ) || false !== strpos( $role['name'], $search_role ) ) {
						$ret[] = array(
							'id'   => '{role}-' . $key,
							'name' => '{role}-' . esc_js( $role['name'] ),
						);
					}
				}
			} elseif ( false !== strpos( $searchq, '{role-unparsed}-' ) ) {
				$search_role = str_replace( '{role-unparsed}-', '', $searchq );
				
				foreach ( $roles as $key => $role ) {
					if ( ! $search_role ) {
						$ret[] = array(
							'id'   => '{role-unparsed}-' . $key,
							'name' => '{role-unparsed}-' . esc_js( $role['name'] ),
						);
					} elseif ( false !== strpos( $key, $search_role ) || false !== strpos( $role['name'], $search_role ) ) {
						$ret[] = array(
							'id' => '{role-unparsed}-' . $key,
							'name' => '{role-unparsed}-' . esc_js( $role['name'] ),
						);
					}
				}
			} else {
				// The Query
				$users = get_users( $args );
			
				foreach( $users as $user){
					$ret[] = array(
						'id' => $user->ID,
						'name' => fep_user_name( $user->ID )
					);
				}
			}
		}
		
		wp_send_json($ret);
		}
		die();
	}
  } //END CLASS

add_action('init', array(Fep_Pro_Ajax::init(), 'actions_filters'));

