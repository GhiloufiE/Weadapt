<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Fep_Read_Receipt {

	private static $instance;

	public static function init() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
			return self::$instance;
	}

	function actions_filters() {
			add_filter( 'fep_settings_fields', array( $this, 'settings_fields' ) );

		if ( fep_get_option( 'read_receipt', 1 ) ) {
			add_action( 'fep_display_after_message', array( $this, 'display_read_receipt' ), 99 );
		}

	}

	function settings_fields( $fields ) {
		$fields['read_receipt'] = array(
			'type'     => 'checkbox',
			'class'    => '',
			'section'  => 'mr_multiple_recipients',
			'value'    => fep_get_option( 'read_receipt', 1 ),
			'label'    => __( 'Read Receipt', 'front-end-pm' ),
			'cb_label' => __( 'Show read receipt bottom of every message?', 'front-end-pm' ),
		);

			return $fields;

	}

	function display_read_receipt() {

		$participants = FEP_Participants::init()->get( fep_get_the_id(), false, true );

		$receipt = array();
		foreach ( $participants as $participant ) {
			if ( ! $participant->mgs_read ) {
				continue;
			}
			if ( fep_get_message_field( 'mgs_author' ) == $participant->mgs_participant ) {
				continue;
			}

			// date_i18 creates problem converting form gmt
			// $receipt[] = sprintf(__('Read by %s &#x40; %s', 'front-end-pm' ), fep_user_name( $user_id ), date_i18n( get_option( 'date_format' ). ' '.get_option( 'time_format' ), $time, true ));
			$receipt[ $participant->mgs_read ] = apply_filters( 'fep_filter_read_receipt_individual', '<div class="fep-read-receipt-individual">' . sprintf( __( 'Read by %1$s &#x40; %2$s', 'front-end-pm' ), fep_user_name( $participant->mgs_participant ), get_date_from_gmt( date( 'Y-m-d H:i:s', $participant->mgs_read ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ) . '</div>', $participant->mgs_read, $participant->mgs_participant );
		}
		if ( $receipt ) {
			ksort( $receipt, SORT_NUMERIC );
			echo apply_filters( 'fep_filter_read_receipt', '<hr /><div class="fep-read-receipt">' . implode( '', array_filter( $receipt ) ) . '</div>', $receipt );
		}

	}
} //END CLASS

add_action( 'init', array( Fep_Read_Receipt::init(), 'actions_filters' ) );

