<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class FEP_Pro_Cron {
	private static $instance;

	public static function init() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
			return self::$instance;
	}

	function actions_filters() {
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		add_action( 'fep_action_after_admin_options_save', array($this, 'reschedule_event' ) );
	}

	function cron_schedules( $schedules ) {
		$schedules['fep_15_min'] = array(
			'interval' => 15 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 15 minutes', 'front-end-pm' ),
		);
		return $schedules;
	}
	
	function reschedule_event( $old_value ){
		if( ! isset( $old_value['email_interval'] ) ){
			$old_value['email_interval'] = '';
		}
		$interval = fep_get_option( 'email_interval', 'fep_15_min' );
		
		if ( $interval !== $old_value['email_interval'] ) {
			if ( wp_next_scheduled( 'fep_email_interval_event' ) ) {
				wp_clear_scheduled_hook( 'fep_email_interval_event' );
			}
			wp_schedule_event( time(), $interval, 'fep_email_interval_event' );
		}
		
		if ( ! isset( $old_value['ep_enable'] ) ) {
			$old_value['ep_enable'] = '';
		}
		if ( fep_get_option( 'ep_enable' ) !== $old_value['ep_enable'] ) {
			if ( 'pop3' === fep_get_option( 'ep_enable' ) ) {
				wp_schedule_event( time(), 'fep_15_min', 'fep_pop3_event' );
			} else {
				wp_clear_scheduled_hook( 'fep_pop3_event' );
			}
		}
	}

} //END CLASS

add_action( 'init', array( FEP_Pro_Cron::init(), 'actions_filters' ) );

